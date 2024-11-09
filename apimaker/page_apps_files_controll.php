<?php

if( $_POST['action'] == "load_storage_vaults" ){

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults", [
		'app_id'=>$config_param1,
	],[
		'projection'=>[
			'des'=>true,'vault_type'=>true,
		],
		'sort'=>['des'=>1],
		'limit'=>200,
	]);
	json_response($res);

	exit;
}
if( $_POST['action'] == "get_files" ){
	$t = validate_token("getfiles.". $config_param1, $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'app_id'=>$config_param1,
		"path"=>$_POST['current_path'],
	],[
		'projection'=>[
			'body'=>false,'data'=>false,
		],
		'sort'=>['name'=>1],
		'limit'=>200,
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == "delete_file" ){
	$t = validate_token("deletefile". $config_param1 . $_POST['file_id'], $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['file_id']) ){
		json_response("fail", "ID incorrect");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'_id'=>$_POST['file_id']
	]);
	if( $res['data'] ){
		if( $res['data']['vt'] == "folder" ){
			$res2 = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
				"app_id"=>$config_param1,
				'path'=>$res['data']['path'] . $res['data']['name'] . '/'
			]);
			if( $res2['data'] ){
				json_response("fail", "Folder is not empty");
			}
		}
	}
	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'_id'=>$_POST['file_id']
	]);

	event_log( "files", "delete", [
		"app_id"=>$config_param1, 
		"file_id"=>$_POST['file_id']
	]);

	update_app_pages( $config_param1 );
	json_response($res);
}

if( $_POST['action'] == "create_file" ){
	if( !preg_match("/^[a-z0-9\.\-\_\/]{3,100}\.[a-z]{2,4}$/i", $_POST['new_file']['name']) ){
		json_response("fail", "Name incorrect");
	}
	if( !preg_match("/^[a-z\/]{5,50}$/i", $_POST['new_file']['type']) ){
		json_response("fail", "Type incorrect");
	}
	preg_match("/\.([a-z]{2,4})$/i",$_POST['new_file']['name'], $m );
	if( !$m ){
		json_response("fail", "Extension is required");
	}
	$ext = strtolower($m[1]);
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		"app_id"=>$config_param1,
		'name'=>$_POST['new_file']['name'],
		"path"=>$_POST['current_path'],
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}

	$type = $_POST['new_file']['type'];

	$data = 'var a = 10; b = a + 10;';
	if( $type == "text/html" ){
		$data = "<h1>Heading</h1><p>it is a paragraph of text. </p><ul><li>One</li><li>Two</li></ul>";
	}else if( $type == "text/css" ){
		$data = ".special{ color:red; }";
	}else if( $type == "text/javascript" ){
		$data = `function foo(items) {
    var x = "All this is syntax highlighted";
    return x;
}`;
	}

	$version_id = $mongodb_con->generate_id();
	$path = $_POST['current_path'];
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		"app_id"=>$config_param1,
		"name"=>$_POST['new_file']['name'],
		'type'=>$type,
		'vt'=>"file", //file,folder
		"path"=>$path,
		't'=>'inline', //inline/s3/disc/base64
		'ext'=>$ext,
		'data'=>$data,
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
		"sz"=>100
	]);

	event_log( "files", "create", [
		"app_id"=>$config_param1, 
		"file_id"=>$res['inserted_id']
	]);

	update_app_pages( $config_param1 );
	json_response($res);
	exit;
}

if( $_POST['action'] == "files_create_folder" ){
	if( !preg_match("/^[a-z0-9\.\-\_\/]{2,100}$/i", $_POST['new_folder']) ){
		json_response("fail", "Name incorrect. Min 2 chars Max 100. No special chars");
	}
	$path = $_POST['current_path'];
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		"app_id"=>$config_param1,
		'vt'=>"folder",
		'name'=>$_POST['new_folder'],
		"path"=>$path,
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		"app_id"=>$config_param1,
		"name"=>$_POST['new_folder'],
		'type'=>$type,
		'vt'=>"folder", //file,folder
		'path'=>$path,
		't'=>'inline', //inline/s3/disc/base64
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
	]);
	event_log( "files", "folder_create", [
		"app_id"=>$config_param1, 
		"file_id"=>$res['inserted_id'],
		"name"=>$_POST['new_folder']
	]);
	update_app_pages( $config_param1 );
	json_response($res);
	exit;
}

if( $_POST['action'] == "apps_file_upload" ){
	$t = validate_token( "file.upload.".$config_param1, $_POST['token'] );
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( file_exists( $_FILES['file']['tmp_name'] ) && filesize($_FILES['file']['tmp_name']) > 0  ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			'app_id'=>$config_param1,
			'name'=>$_POST['name']
		]);
		
		$fb = file_get_contents($_FILES['file']['tmp_name']);
		//echo $_FILES['file']['name'];exit;
		//print_r( explode(".",$_FILES['file']['name']) );
		$ext = array_pop( explode(".",$_FILES['file']['name']) );
		//echo $ext;exit;
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			"app_id"=>$config_param1,
			"name"=>$_POST['name'],
			'type'=>$_FILES['file']['type'],
			'vt'=>"file",
			"path"=>$_POST['path'],
			't'=>"base64",
			"data"=>base64_encode($fb),
			"sz"=>strlen($fb),
			'ext'=>$ext,
			"created"=>date("Y-m-d H:i:s"),
			"updated"=>date("Y-m-d H:i:s"),
		]);
		if( $res['status'] == "success" ){

			event_log( "files", "upload", [
				"app_id"=>$config_param1, 
				"file_id"=>$res['inserted_id'],
				'name'=>$_POST['name'],
				"path"=>$_POST['path'],
			]);

			$res['data'] = [
				"_id"=>$res['inserted_id'],
				"app_id"=>$config_param1,
				"name"=>$_POST['name'],
				'type'=>$_FILES['file']['type'],
				'vt'=>"file",
				"path"=>$_POST['path'],
				't'=>"base64",
				"sz"=>strlen($fb),
				'ext'=>$ext,
				"created"=>date("Y-m-d H:i:s"),
				"updated"=>date("Y-m-d H:i:s"),
			];
			update_app_pages( $config_param1 );
		}
		json_response($res);
	}else{
		json_response(['status'=>"fail", "error"=>"server error"]);
	}
	exit;
}


if( $_POST['action'] == "mount_storage_vault" ){

	if( !isset($_POST['new_mount']) ){
		json_response("fail", "input missing");
	}
	if( !isset($_POST['new_mount']['vault_id']) || !isset($_POST['new_mount']['vault_name']) || !isset($_POST['new_mount']['vault_path']) || !isset($_POST['new_mount']['local_path']) ){
		json_response("fail", "input missing");
	}
	if( !preg_match("/^[0-9a-f]{24}$/", $_POST['new_mount']['vault_id']) ){
		json_response("fail", "Incorrect vault id");
	}
	$vault_res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults", [
		"_id"=>$_POST['new_mount']['vault_id']
	]);
	if( !$vault_res['data'] ){
		json_response( $vault_res );
	}
	$_POST['new_mount']['vault_name'] = $vault_res['data']['des'];
	$_POST['new_mount']['vault_type'] = $vault_res['data']['vault_type'];

	if( $_POST['new_mount']['vault_path'] != "/" && !preg_match("/^\/[a-z0-9\-\_\.\/]+\/$/i", $_POST['new_mount']['vault_path']) ){
		json_response("fail", "Vault path should be / or /path/  or /path/path/");
	}
	if( !preg_match("/^\/[a-z0-9\-\_\.\/]+\/$/i", $_POST['new_mount']['local_path']) ){
		json_response("fail", "Local path should be /path/  or /path/path/");
	}
	if( preg_match("/[\/]{2,5}/i", $_POST['new_mount']['local_path']) ){
		json_response("fail", "Local path should be /path/  or /path/path/");
	}
	if( preg_match("/[\/]{2,5}/i", $_POST['new_mount']['vault_path']) ){
		json_response("fail", "Vault path should be /path/  or /path/path/");
	}

	$x = explode("/",$_POST['new_mount']['local_path']);
	array_pop( $x );
	if( sizeof( $x ) > 1 ){
		$name = array_pop( $x );
		$path = implode("/", $x);
		if( $path == "" ){$path = "/";}
	}else{
		$name = array_pop( $x );
		$path = "/";
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'app_id'=>$config_param1,
		'path'=>$path, "name"=>$name
	]);
	$res['q'] = [
		'app_id'=>$config_param1,
		'path'=>$path, "name"=>$name
	];
	if( $res['data'] ){
		json_response("fail", $path . $name . "/ already exists with same name");
	}
	$res = $mongodb_con->insert(  $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'app_id'=>$config_param1,
		'path'=>$path, 
		"name"=>$name,
		"vt"=>"folder", 
		"type"=>"mounted",
		"vault"=>$_POST['new_mount'],
	]);

	event_log( "files", "upload", [
		"app_id"=>$config_param1, 
		"file_id"=>$res['inserted_id'],
		'path'=>$path, "name"=>$name,
		"vault_id"=>$_POST['new_mount']['vault_id'],
	]);

	json_response($res);

	exit;
}

if( $config_param3 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param3) ){
		echo404("Incorrect File ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'app_id'=>$app['_id'],
		'_id'=>$config_param3
	]);
	if( !$res['data'] ){
		echo404("File not found!");
	}
	$file = $res['data'];

	if( $_POST['action'] == "file_load_content" ){
		if( $file['t'] != "base64" ){
			json_response(['status'=>"fail", "error"=>"Incorrect file type"]);exit;
		}
		json_response([
			'status'=>'success',
			'data'=>$file['data']
		]);
		exit;
	}

	if( $file['t'] != "inline" ){
		unset($file['data']);
	}
	//print_r( $file );exit;
	//unset($file['data']);

	$mode = "htmlmixed";
	if( $file['type'] == "text/html" ){
		$mode = "htmlmixed";
	}else if( $file['type'] == "text/css" ){
		$mode = "css";
	}else if( $file['type'] == "text/javascript" ){
		$mode = "javascript";
	}

	if( $_POST['action'] == "file_save_content" ){
		if( $_POST['file_id'] != $config_param3 ||  $_POST['app_id'] != $config_param1 ){
			json_response("fail","Incorrect URL");
		}
		$t = validate_token("file.save.".$config_param1.".".$config_param3, $_POST['token']);
		if( $t != "OK" ){
			json_response("fail", $t);
		}

		$vars_used = [];
		preg_match_all("/[\-]{2}\w[a-z\-]+\w[\-]{2}/", $_POST['data'], $m);
		// print_r( $m );
		// exit;
		foreach( $m[0] as $i=>$j ){
			$vars_used[ $j ] = 2;
		}

		//print_r( $vars_used );exit;

		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			"app_id"=> $config_param1,
			"_id"=>$config_param3
		],[
			"data"=>$_POST['data'],
			"sz"=>strlen($_POST['data']),
			"vars_used"=>array_keys($vars_used),
			"updated"=>date("Y-m-d H:i:s"),
		]);
		if( $res["status"] == "fail" ){
			json_response("fail",$res["error"]);
		}

		event_log( "files", "save", [
			"app_id"=>$config_param1, 
			"file_id"=>$config_param3,
		]);

		update_app_pages( $config_param1 );
		json_response("success","ok");
	}

	if( $_POST['action'] == "file_update_settings" ){
		$t = validate_token("file.setting.save.".$config_param1.".".$config_param3, $_POST['token']);
		if( $t != "OK" ){
			json_response("fail", $t);
		}
		if( !preg_match("/^[a-z0-9\.\-\_\/]{3,100}\.[a-z]{2,4}$/i", $_POST['edit_file']['name']) ){
			json_response("fail", "Name incorrect");
		}
		if( !preg_match("/^[a-z\/]{5,50}$/i", $_POST['edit_file']['type']) ){
			json_response("fail", "Type incorrect");
		}
		preg_match("/\.([a-z]{2,4})$/i",$_POST['edit_file']['name'], $m );
		if( !$m ){
			json_response("fail", "Extension is required");
		}
		$ext = strtolower($m[1]);
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			"app_id"=>$config_param1,
			'name'=>$_POST['edit_file']['name'],
			'_id'=>['$ne'=>$_POST['file_id']]
		]);
		if( $res['data'] ){
			json_response("fail", "A file already exists with same name");
		}

		$type = $_POST['edit_file']['type'];

		$version_id = $mongodb_con->generate_id();
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			'_id'=>$_POST['file_id']
		],[
			"name"=>$_POST['edit_file']['name'],
			'type'=>$type,
			'ext'=>$ext,
			"updated"=>date("Y-m-d H:i:s"),
		]);

		event_log( "files", "update_settings", [
			"app_id"=>$config_param1, 
			"file_id"=>$_POST['file_id'],
		]);
		update_app_pages( $config_param1 );
		json_response($res);
		exit;
	}

	

}