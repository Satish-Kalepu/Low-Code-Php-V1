<?php

$page_type = "apis";
$property_type = "api";

// $res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_apps", [], ['projection'=>['_id'=>true]] );
// foreach( $res['data'] as $i=>$j ){
// 	update_app_pages( $j['_id'] );
// }

if( $_POST['action'] == "get_apis" ){
	$t = validate_token("getapis.". $config_param1, $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		'app_id'=>$config_param1,
		"path"=>$_POST['current_path'],
	],[
		'sort'=>['name'=>1],
		'limit'=>200,
		'projection'=>[
			'engine'=>false
		]
	]);

	foreach( $res['data'] as $i=>$j ){
		if( $j['vt'] == "folder" ){
			//print_r( $j['vt'] );
			//echo $j['path'].$j['name']."/";
			$res2 = $mongodb_con->count( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
				'app_id'=>$config_param1,
				"path"=>$j['path'].$j['name']."/",
			]);
			$res['data'][$i]['count'] = $res2['data'];
		}
	}

	json_response($res);
	exit;
}

if( $_POST['action'] == "delete_api" ){
	$t = validate_token("deleteapi". $config_param1 . $_POST['api_id'], $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['api_id']) ){
		json_response("fail", "ID incorrect");
	}

	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"_id"=>$_POST['api_id']
	]);
	if( !$res['data'] ){
		json_response("fail", "Not found");
	}
		if( $res['data']['vt'] == "folder" ){
			$res2 = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
				"app_id"=>$config_param1,
				'path'=>$res['data']['path'] . $res['data']['name'] . '/'
			]);
			if( $res2['data'] ){
				json_response("fail", "Folder is not empty");
			}
		}

	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		'_id'=>$_POST['api_id']
	]);
	$res = $mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
		'api_id'=>$_POST['api_id']
	]);
	event_log( "system", "app_api_delete", ["app_id"=>$config_param1,"api_id"=>$_POST['api_id']] );	
	update_app_pages( $config_param1 );
	json_response($res);
}

if( $_POST['action'] == "create_api" ){
	if( !isset($_POST['new_api']) || !isset($_POST['current_path']) ){
		json_response("fail", "Incorrect request");
	}
	// if( !preg_match("/^\/[a-z0-9\/\-\_\.]{3,100}$/i", $_POST['current_path']) ){
	// 	json_response("fail", "Name incorrect");
	// }
	if( !preg_match("/^[a-z0-9\.\-\_]{3,100}$/i", $_POST['new_api']['name']) ){
		json_response("fail", "Name incorrect");
	}
	if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{5,250}$/i", $_POST['new_api']['des']) ){
		json_response("fail", "Description incorrect");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		'name'=>$_POST['new_api']['name'],
		"path"=>$_POST['current_path'],
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}
	$version_id = $mongodb_con->generate_id();
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"path"=>$_POST['current_path'],
		'vt'=>"api",
		"name"=>$_POST['new_api']['name'],
		"des"=>$_POST['new_api']['des'],
		"type"=>"api",
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
		"active"=>true,
		"version"=>1,
		"version_id"=>$version_id,
		"output-type"	=> "application/json",
		"input-method"	=> "POST",
		"input-type"	=> "application/json",
		"auth-type"	=> "None",
	]);
	if( $res['status'] == 'success' ){
		$api_id = $res['inserted_id'];
		event_log( "system", "app_api_create", ["app_id"=>$config_param1, "api_id"=>$api_id, "api_version_id"=>$version_id ] );	
		$res2 = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			"_id"=>$mongodb_con->get_id($version_id),
			"app_id"=>$config_param1,
			"path"=>$_POST['current_path'],
			'vt'=>"api",
			"api_id"=>$api_id,
			"name"=>$_POST['new_api']['name'],
			"des"=>$_POST['new_api']['des'],
			"type"=>"api",
			"active"=>true,
			"created"=>date("Y-m-d H:i:s"),
			"updated"=>date("Y-m-d H:i:s"),
			"version"=>1,
			"output-type"	=> "application/json",
			"input-method"	=> "POST",
			"input-type"	=> "application/json",
			"auth-type"	=> "None",
		]);
		update_app_pages( $config_param1 );
		$res['version_id'] = $res2['inserted_id'];
		$res['api_id'] = $res['inserted_id'];
		unset($res['inserted_id']);
		json_response($res);
	}else{
		json_response($res);
	}
	exit;
}

if( $_POST['action'] == "apis_rename" ){
	if( !isset($_POST['api_id']) || !isset($_POST['new_name']) || !isset($_POST['current_path']) ){
		json_response("fail", "Input missing");
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['api_id']) ){
		json_response("fail", "Incorrect api id");
	}
	if( !preg_match("/^[a-z][a-z0-9\.\-\_]{2,50}$/i", $_POST['new_name']) ){
		json_response("fail", "Name incorrect. Min 2 chars Max 100. No special chars");
	}
	if( $_POST['current_path'] != "/" && !preg_match("/^\/[a-z0-9\.\-\_\/]+$/i", $_POST['current_path']) ){
		json_response("fail", "Path incorrect");
	}
	$res = $mongodb_con->find_one($config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"_id"=>$_POST['api_id']
	]);
	if( !$res['data'] ){
		json_response("fail", "API not found");
	}
	if( $res['data']['vt'] != "api" ){
		json_response("fail", "Incorrect api vt type");
	}

	$res = $mongodb_con->find_one($config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"_id"=>['$ne'=>$_POST['api_id']],
		"path"=>$_POST['current_path'],
		"name"=>$_POST['new_name'],
	]);
	if( $res['data'] ){
		json_response("fail", "Path already exists");
	}

	$res = $mongodb_con->update_one($config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"_id"=>$_POST['api_id']
	],[
		"name"=>$_POST['new_name'], "updated"=>date("Y-m-d H:i:s")
	]);
	if( $res['status'] == "fail" ){
		json_response($res);
	}
	event_log( "system", "app_api_edit", ["app_id"=>$config_param1, "api_id"=>$_POST['api_id'] ] );
	$res = $mongodb_con->update_many($config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
		"app_id"=>$config_param1,
		"api_id"=>$_POST['api_id']
	],[
		"name"=>$_POST['new_name'], "updated"=>date("Y-m-d H:i:s")
	]);
	update_app_pages( $config_param1 );
	json_response($res);
	exit;
}
if( $_POST['action'] == "apis_folder_rename" ){
	if( !isset($_POST['folder_id']) || !isset($_POST['new_name']) || !isset($_POST['current_path']) ){
		json_response("fail", "Input missing");
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['folder_id']) ){
		json_response("fail", "Incorrect folder id");
	}
	if( !preg_match("/^[a-z][a-z0-9\.\-\_]{2,50}$/i", $_POST['new_name']) ){
		json_response("fail", "Name incorrect. Min 2 chars Max 100. No special chars");
	}
	if( $_POST['current_path'] != "/" && !preg_match("/^\/[a-z0-9\.\-\_\/]+$/i", $_POST['current_path']) ){
		json_response("fail", "Path incorrect");
	}
	$res = $mongodb_con->find_one($config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"_id"=>$_POST['folder_id']
	]);
	if( !$res['data'] ){
		json_response("fail", "API Folder not found");
	}
	if( $res['data']['vt'] != "folder" ){
		json_response("fail", "Incorrect folder vt type");
	}

	$old_name = $res['data']['path'].$res['data']['name']."/";
	$new_name = $res['data']['path'].$_POST['new_name']."/";

	$res = $mongodb_con->find_one($config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"_id"=>['$ne'=>$_POST['folder_id']],
		"path"=>$_POST['current_path'],
		"name"=>$_POST['new_name'],
	]);
	if( $res['data'] ){
		json_response("fail", "Path already exists");
	}

	$res = $mongodb_con->update_one($config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"_id"=>$_POST['folder_id']
	],[
		"name"=>$_POST['new_name'], "updated"=>date("Y-m-d H:i:s")
	]);
	if( $res['status'] == "fail" ){
		json_response($res);
	}
	event_log( "system", "app_api_folder_rename", ["app_id"=>$config_param1, "folder_id"=>$_POST['folder_id'], "new_name"=>$_POST['new_name'] ] );
	$res = $mongodb_con->update_many($config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"path"=>$old_name
	],[
		"path"=>$new_name, "updated"=>date("Y-m-d H:i:s")
	]);
	if( $res['status'] == "fail" ){
		json_response($res);
	}
	$res = $mongodb_con->update_many($config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
		"app_id"=>$config_param1,
		"path"=>$old_name
	],[
		"path"=>$new_name, "updated"=>date("Y-m-d H:i:s")
	]);
	update_app_pages( $config_param1 );
	json_response($res);
	exit;
}
if( $_POST['action'] == "apis_move" ){
	if( !isset($_POST['api_id']) || !isset($_POST['new_path']) || !isset($_POST['current_path']) ){
		json_response("fail", "Input missing");
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['api_id']) ){
		json_response("fail", "Incorrect api id");
	}
	if( $_POST['new_path'] != "/" && !preg_match("/^\/[a-z0-9\.\-\_\/]{1,60}$/i", $_POST['new_path']) ){
		json_response("fail", "Path incorrect.");
	}
	if( $_POST['current_path'] != "/" && !preg_match("/^\/[a-z0-9\.\-\_\/]+$/i", $_POST['current_path']) ){
		json_response("fail", "Path incorrect");
	}
	$res = $mongodb_con->find_one($config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"_id"=>$_POST['api_id']
	]);
	if( !$res['data'] ){
		json_response("fail", "Api not found");
	}
	if( $res['data']['vt'] != "api" ){
		json_response("fail", "Incorrect api vt type");
	}

	$api_name = $res['data']['name'];
	$old_path = $res['data']['path'];
	$new_path = $_POST['new_path'];

	$res = $mongodb_con->find_one($config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"_id"=>['$ne'=>$_POST['api_id']],
		"path"=>$new_path,
		"name"=>$api_name,
	]);
	if( $res['data'] ){
		json_response("fail", "An api with same name already exists in " . $new_path);
	}

	$res = $mongodb_con->update_one($config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"_id"=>$_POST['api_id']
	],[
		"path"=>$new_path, "updated"=>date("Y-m-d H:i:s")
	]);
	if( $res['status'] == "fail" ){
		json_response($res);
	}
	event_log( "system", "app_api_move", ["app_id"=>$config_param1, "api_id"=>$_POST['api_id'], "new_path"=>$_POST['new_path'] ] );
	$res = $mongodb_con->update_many($config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
		"app_id"=>$config_param1,
		"api_id"=>$_POST['api_id']
	],[
		"path"=>$new_path, "updated"=>date("Y-m-d H:i:s")
	]);
	update_app_pages( $config_param1 );
	json_response($res);
	exit;
}
if( $_POST['action'] == "apis_create_folder" ){
	$t = validate_token("create.folder.". $config_param1, $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( !isset($_POST['current_path']) || !isset($_POST['new_folder']) ){
		json_response("fail", "Input missing");
	}
	if( !preg_match("/^[a-z0-9\.\-\_\/]{2,100}$/i", $_POST['new_folder']) ){
		json_response("fail", "Name incorrect. Min 2 chars Max 100. No special chars");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		'name'=>$_POST['new_folder'],
		"path"=>$_POST['current_path'],
	]);
	if( $res['data'] ){
		json_response("fail", "Path already exists");
	}
	$path = $_POST['current_path'];
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"name"=>$_POST['new_folder'],
		'type'=>"api",
		'vt'=>"folder", //file,folder
		'path'=>$path,
		't'=>'inline', //inline/s3/disc/base64
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
	]);
	event_log( "system", "app_api_create_folder", ["app_id"=>$config_param1, "path"=>$path, "new_folder"=>$_POST['new_folder'] ] );
	update_app_pages( $config_param1 );
	json_response($res);
	exit;
}

if( $_POST['action'] == "app_api_import_create" ){
	//print_r( $_POST['file'] );
	if( $config_param1 != $_POST['app_id']){
		json_response([
			"status"=>"error",
			"error"=>"Incorrect URL"
		]);
	}
	if( !isset($_FILES['file']['name']) || !isset($_POST['password']) || !isset($_POST['current_path']) ){
		json_response([
			"status"=>"error",
			"error"=>"Input missing"
		]);
	}
	if( !file_exists($_FILES['file']['tmp_name']) ){
		json_response([
			"status"=>"error",
			"error"=>"File upload failed"
		]);
	}
	if( !preg_match("/\.[a-f0-9]{24}\.api$/", $_FILES['file']['name']) ){
		json_response([
			"status"=>"error",
			"error"=>"File name format incorrect"
		]);
	}
	$d = file_get_contents($_FILES['file']['tmp_name']);
	$d = explode("\n", $d);
	if( sizeof($d) < 3 ){
		json_response([
			"status"=>"error",
			"error"=>"Incorrect file format"
		]);
	}
	if( trim($d[0]) != "type:api_export"){
		json_response([
			"status"=>"error",
			"error"=>"Incorrect file format."
		]);
	}
	if( trim($d[1]) != "export_version:1"){
		json_response([
			"status"=>"error",
			"error"=>"Incorrect file format."
		]);
	}
	$data = @openssl_decrypt($d[2], "aes256", $_POST['password']."123456");
	if( !$data ){
		json_response([
			"status"=>"error",
			"error"=>"Password incorrect or Decryption failed"
		]);
	}
	$import_api_data = json_decode($data,true);
	//print_r( $import_api_data );exit;
	if( !isset($import_api_data['name']) || !isset($import_api_data['des']) || !isset($import_api_data['engine']) || !isset($import_api_data['input-type']) || !isset($import_api_data['output-type']) ){
		json_response([
			"status"=>"error",
			"error"=>"API data invalid.."
		]);
	}

	$n = $import_api_data['name'];
	if( isset($_POST['name']) && $_POST['name'] != "" ){
		$n = $_POST['name'];
		if( !preg_match("/^[a-z0-9\.\-\_]{3,100}$/i", $n) ){
			json_response("fail", "Name incorrect");
		}
		$import_api_data['name'] = $n;
	}
	$d = $import_api_data['des'];
	if( isset($_POST['des']) && $_POST['des'] != "" ){
		$d = $_POST['des'];
		if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{5,250}$/i", $d) ){
			json_response("fail", "Description incorrect");
		}
		$import_api_data['des'] = $d;
	}

	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"app_id"=>$config_param1,
		"name"=>$n,
		"path"=>$_POST['current_path'],
	]);
	if( $res['data'] ){
		json_response([
			"status"=>"error",
			"error"=>"API with same name already exists",
			"name"=>$import_api_data['name'],
			"des"=>$import_api_data['des'],
		]);
	}

	$import_api_data['created'] = date("Y-m-d H:i:s");
	$import_api_data['updated'] = date("Y-m-d H:i:s");

	$api_id = $mongodb_con->generate_id();
	$version_id = $mongodb_con->generate_id();
	$import_api_data['app_id'] = $config_param1;
	$import_api_data['api_id'] = $api_id;
	$import_api_data['_id'] = $version_id;
	$import_api_data['version'] = 1;
	$import_api_data['path'] = $_POST['current_path'];
	$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		"_id"=>$api_id,
		"app_id"=>$config_param1,
		"name"=>$import_api_data['name'],
		"des"=>$import_api_data['des'],
		"path"=>$_POST['current_path'],
		"vt"=>"api",
		"type"=>$import_api_data['type'],
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
		"active"=>true,
		"version"=>1,
		"version_id"=>$version_id,
		"output-type"	=> $import_api_data['output-type'],
		"input-method"	=> $import_api_data['input-method'],
		"input-type"	=> $import_api_data['input-type'],
		"auth-type"	=> $import_api_data['auth-type'],
	]);
	$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", $import_api_data );

	event_log( "system", "app_api_import_create", [
		"app_id"=>$config_param1, 
		"path"=>$_POST['current_path'], 
		"name"=>$import_api_data['name'],
		"api_id"=> $api_id
	]);

	update_app_pages( $config_param1 );

	json_response([
		"status"=>"success",
		"api_id"=>$api_id,
		"version_id"=>$version_id,
	]);
}

if( $config_param3 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param3) ){
		echo404("Incorrect API ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		'app_id'=>$app['_id'],
		'_id'=>$config_param3
	]);
	if( !$res['data'] ){
		echo404("Api not found!");
	}
	$main_api = $res['data'];
	if( $main_api['vt'] == "folder" ){
		echo404("Api not found!");
	}
}

if( $config_param4 && $main_api ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param4) ){
		echo404("Incorrect API Version ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
		"app_id"=>$config_param1,
		"api_id"=>$main_api['_id'],
		"_id"=>$config_param4
	]);
	if( $res['data'] ){
		$api = $res['data'];
	}else{
		echo404("Api version not found!");
	}

	$new_version_series = 0;
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
		"app_id"=>$config_param1,
		"api_id"=>$main_api['_id'],
	], ['projection'=>[ 'version'=>1, 'updated'=>1, 'vn'=>1 ] ]);
	if( $res['data'] ){
		$api_versions = $res['data'];
		foreach( $api_versions as $i=>$j ){
			if( $new_version_series < $j['version'] ){
				$new_version_series = $j['version'];
			}
		}
	}
	$new_version_series++;

	if( $main_api['version_id'] == $api['_id'] ){
		
	}

	if( !isset($api['auth-type']) ){
		$api['auth-type'] = "None";
	}
	if( isset($api['test']) ){
		if( !isset($api['test']['headers']['Access-Key']) ){
			$api['test']['headers']['Access-Key'] = "None";
		}
	}

	//print_pre( $api );exit;

	if( $_POST['action'] == "load_engine_data" ){
		json_response([
			"status"=>"success", 
			"engine"=>($api['engine']?$api['engine']:[]), 
			"test"=>($api['test']?$api['test']:[])
		]);
	}

	if( $_POST['action'] == "app_api_export" ){
		//print_r( $api );exit;

		event_log( "system", "app_api_export", [
			"app_id"=>$config_param1, 
			"api_id"=> $api['_id']
		]);

		$fn = preg_replace("/\W/", "", $api['name']).".".$api['_id'].".api";
		unset($api['_id']);unset($api['app_id']);unset($api['api_id']);
		//unset($api['name']);unset($api['des']);
		unset($api['created']);unset($api['updated']);unset($api['version']);
		$d = json_encode($api);
		$d = "type:api_export\nexport_version:1\n".@openssl_encrypt($d, "aes256", $_POST['password']."123456");
		json_response([
			"status"=>"success",
			"content"=>$d,
			"filename"=>$fn
		]);
	}

	if( $_POST['action'] == "app_api_import" ){
		//print_r( $_POST['file'] );
		if( !isset($_FILES['file']['name']) || !isset($_POST['password']) || !isset($_POST['current_path']) ){
			json_response([
				"status"=>"error",
				"error"=>"Input missing"
			]);
		}
		if( !file_exists($_FILES['file']['tmp_name']) ){
			json_response([
				"status"=>"error",
				"error"=>"File upload failed"
			]);
		}
		if( !preg_match("/\.[a-f0-9]{24}\.api$/", $_FILES['file']['name']) ){
			json_response([
				"status"=>"error",
				"error"=>"File name format incorrect"
			]);
		}
		$d = file_get_contents($_FILES['file']['tmp_name']);
		$d = explode("\n", $d);
		if( sizeof($d) < 3 ){
			json_response([
				"status"=>"error",
				"error"=>"Incorrect file format"
			]);
		}
		if( trim($d[0]) != "type:api_export"){
			json_response([
				"status"=>"error",
				"error"=>"Incorrect file format."
			]);
		}
		if( trim($d[1]) != "export_version:1"){
			json_response([
				"status"=>"error",
				"error"=>"Incorrect file format."
			]);
		}
		$data = @openssl_decrypt($d[2], "aes256", $_POST['password']."123456");
		if( !$data ){
			json_response([
				"status"=>"error",
				"error"=>"Password incorrect or Decryption failed"
			]);
		}
		$import_api_data = json_decode($data,true);
		//print_r( $api );
		if( !isset($import_api_data['engine']) || !isset($import_api_data['input-type']) || !isset($import_api_data['output-type']) ){
			json_response([
				"status"=>"error",
				"error"=>"API data invalid"
			]);
		}

		$import_api_data['created'] = date("Y-m-d H:i:s");
		$import_api_data['updated'] = date("Y-m-d H:i:s");
		$import_api_data['path'] = $_POST['current_path'];
		unset($import_api_data['name']);
		unset($import_api_data['des']);

		if( $_POST['version'] == "create" ){
			$new_version_id = $mongodb_con->generate_id();
			$api['_id'] = $new_version_id;
			$api['version'] = $new_version_series;
			$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", $api);
			$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
				"app_id"=>$config_param1,
				"_id"=>$main_api['_id'],
			], [
				'version_id'=>$api['_id'],
				"version"=>$new_version_series,
			]);
			$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
				"app_id"=>$config_param1,
				"api_id"=>$main_api['_id'],
				"_id"=>$new_version_id
			], $import_api_data);
		}else{
			$new_version_id = $config_param4;
			$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
				"app_id"=>$config_param1,
				"api_id"=>$main_api['_id'],
				"_id"=>$config_param4
			], $import_api_data);
		}

		event_log( "system", "app_api_import", [
			"app_id"=>$config_param1, 
			"api_id"=>$main_api['_id']
		]);

		update_app_pages( $config_param1 );

		json_response([
			"status"=>"success",
			"new_version_id"=>$new_version_id,
		]);
	}

	if( $_POST['action'] == "app_api_load_versions_info" ){
		json_response([
			"status"=>"success",
			"versions"=>$api_versions,
			"current_version"=>$main_api['version_id'],
		]);
	}

	if( $_POST['action'] == "app_api_delete" ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			"app_id"=>$config_param1,
			"api_id"=>$main_api['_id'],
			"_id"=>$_POST['version_id']
		]);
		if( !$res['data'] ){
			json_response([
				"status"=>"fail",
				"error"=>"Version not found",
			]);
		}
		if( $main_api['version_id'] == $_POST['version_id'] ){
			json_response([
				"status"=>"fail",
				"error"=>"You cannot delete current version",
			]);
		}
		$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			"app_id"=>$config_param1,
			"api_id"=>$main_api['_id'],
			"_id"=>$_POST['version_id']
		]);

		event_log( "system", "app_api_delete_version", [
			"app_id"=>$config_param1, 
			"api_id"=>$main_api['_id'],
			"api_id"=>$_POST['version_id'],
		]);

		json_response($res);
		exit;
	}

	if( $_POST['action'] == "app_api_clone" ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			"app_id"=>$config_param1,
			"api_id"=>$main_api['_id'],
			"_id"=>$_POST['from_version_id']
		]);
		if( $res['data'] ){
			$from_api = $res['data'];
		}else{
			json_response([
				"status"=>"fail",
				"error"=>"Source api version not found",
			]);
		}

		$new_api_version = $mongodb_con->generate_id();
		$from_api['vn'] = "Clone of version:" . $from_api['version'];
		$from_api['version'] = $new_version_series;
		$from_api['_id'] = $new_api_version;
		$from_api['created'] = date("Y-m-d H:i:s");
		$from_api['updated'] = date("Y-m-d H:i:s");

		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", $from_api);
		
		event_log( "system", "app_api_clone", [
			"app_id"=>$config_param1, 
			"from_api_id"=>$main_api['_id'],
			"from_version_id"=>$_POST['from_version_id'],
			"from_version_id"=>$new_api_version,
			"from_version_series"=>$res['data']['version'],
			"to_version_series"=>$new_version_series
		]);

		update_app_pages( $config_param1 );

		json_response([
			"status"=>"success",
			"error"=>"",
		]);
	}
	if( $_POST['action'] == "app_api_switch" ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			"app_id"=>$config_param1,
			"api_id"=>$main_api['_id'],
			"_id"=>$_POST['version_id']
		]);
		if( $res['data'] ){
			$from_api = $res['data'];
		}else{
			json_response([
				"status"=>"fail",
				"error"=>"Source api version not found",
			]);
		}

		$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
			"_id"=>$main_api['_id'],
		],[
			"version_id"=>$from_api['_id'],
			"version"=>$from_api['version'],
		]);

		event_log( "system", "app_api_switch", [
			"app_id"=>$config_param1, 
			"api_id"=>$main_api['_id'],
			"to_version_id"=>$from_api['_id'],
			"to_version_series"=>$from_api['version']
		]);

		update_app_pages( $config_param1 );

		json_response([
			"status"=>"success",
			"error"=>"",
		]);
	}

	unset($api['engine']);
	unset($api['test']);
	if( $_POST['action'] == "edit_api" ){
		$t = validate_token("edit_api". $_POST['edit_api']['_id'], $_POST['token']);
		if( $t != "OK" ){
			json_response("fail", $t);
		}
		if( !preg_match("/^[a-z0-9\.\-\_]{3,100}$/i", $_POST['edit_api']['name']) ){
			json_response("fail", "Name incorrect");
		}
		if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{5,250}$/i", $_POST['edit_api']['des']) ){
			json_response("fail", "Description incorrect");
		}
		// uses above api record
		// $res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		// 	'_id'=>$_POST['edit_api']['_id']
		// ]);
		// if( !$res['data'] ){
		// 	json_response("fail", "Record not found");
		// }
		// $api = $res['data'];
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
			'name'=>$_POST['edit_api']['name'],
			'_id'=>['$ne'=>$mongodb_con->get_id($_POST['edit_api']['api_id']) ],
			"path"=>$api['path']
		]);
		if( $res['data'] ){
			json_response("fail", "Name is already in use");
		}
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
			'_id'=>$_POST['edit_api']['api_id']
		],[
			"name"=>$_POST['edit_api']['name'],
			"des"=>$_POST['edit_api']['des'],
			"updated"=>date("Y-m-d H:i:s"),
			"active"=>true,
		]);
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			'_id'=>$config_param4
		],[
			"name"=>$_POST['edit_api']['name'],
			"des"=>$_POST['edit_api']['des'],
			"updated"=>date("Y-m-d H:i:s"),
			"active"=>true,
		]);

		event_log( "system", "app_api_edit", [
			"app_id"=>$config_param1, 
			"api_id"=>$_POST['edit_api']['api_id'],
		]);

		update_app_pages( $config_param1 );
		//update_app_last_change_date( $config_param1 );
		json_response($res);
		exit;
	}

	if( $_POST['action'] == "save_engine_test" ){
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			"app_id"=> $config_param1,
			"_id"=>$config_param4
		],[
			"test"=>$_POST['test']
		]);
		if($res["status"] == "fail" ){
			json_response("fail",$res["error"]);
		}
		event_log( "system", "app_api_save_test", [
			"app_id"=>$config_param1, 
			"api_id"=>$config_param3
		]);
		json_response("success","ok");
	}

	if( $_POST['action'] == "save_engine_data" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['api_id'] ) ){
			json_response("fail", "Error In Page Id");
		}else if( !preg_match("/^[a-f0-9]{24}$/", $_POST['version_id'] ) ){
			json_response("fail", "Error In Version Id");
		}
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			"_id"=>$_POST['version_id']
		]);
		if( $res["status"] == "fail" ){
			json_response("fail","Error finding version: ".$res["error"]);
		}else if( !$res['data'] ){
			json_response("fail","Version record not found");
		}
		$version = $res['data'];

		if( $version['api_id'] != $_POST['api_id'] ){
			json_response("fail","Incorrect version ID mapping");
		}

		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
			"_id"=>$_POST['api_id']
		]);
		if( $res["status"] == "fail" ){
			json_response("fail","Error finding API: ".$res["error"]);
		}else if( !$res['data'] ){
			json_response("fail","API record not found");
		}
		$api = $res['data'];

		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			"_id"=> $_POST['version_id']
		],[
			"input-method"	=> $_POST['input-method'],
			"input-type"	=> $_POST['input-type'],
			"output-type"	=> $_POST['output-type'],
			"auth-type"	=> $_POST['auth-type'],
			"engine"	=> $_POST['data'],
			"updated"	=> date("Y-m-d H:i:s"),
		]);
		if( $res["status"] == "fail" ){
			json_response("fail","Version update failed: ".$res["error"]);
		}

		event_log( "system", "app_api_save_engine", [
			"app_id"=>$config_param1, 
			"api_id"=>$version['api_id'],
			"version_id"=>$_POST['version_id'],
		]);

		if( $api['version_id'] == $version['_id'] ){
			$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
				"_id"=> $_POST['api_id']
			],[
				"type"		=> "api",
				"input-method"	=> $_POST['input-method'],
				"input-type"	=> $_POST['input-type'],
				"output-type"	=> $_POST['output-type'],
				"auth-type"	=> $_POST['auth-type'],
				"engine"	=> $_POST['engine'],
				"updated"	=> date("Y-m-d H:i:s"),
			]);
			if( $res["status"] == "fail" ){
				json_response("fail","API update failed: ".$res["error"]);
			}
		}
		update_app_last_change_date( $config_param1 );
		json_response("success", "OK");
	}

}