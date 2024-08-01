<?php

$page_type = "functions";
$property_type = "function";

if( $_POST['action'] == "get_functions" ){
	$t = validate_token("getfunctions.". $config_param1, $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
		'app_id'=>$config_param1
	],[
		'sort'=>['name'=>1],
		'limit'=>200,
		'projection'=>[
			'engine'=>false
		]
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == "delete_function" ){
	$t = validate_token("deletefunction". $config_param1 . $_POST['function_id'], $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['function_id']) ){
		json_response("fail", "ID incorrect");
	}
	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
		'_id'=>$_POST['function_id']
	]);
	$res = $mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
		'function_id'=>$_POST['function_id']
	]);
	event_log( "system", "app_function_delete", [
		"app_id"=>$config_param1, 
		"function_id"=>$_POST['function_id'],
	]);
	update_app_pages( $config_param1 );
	json_response($res);
}

if( $_POST['action'] == "create_function" ){
	if( !preg_match("/^[a-z0-9\.\-\_\ ]{3,100}$/i", $_POST['new_function']['name']) ){
		json_response("fail", "Name incorrect");
	}
	if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{5,200}$/i", $_POST['new_function']['des']) ){
		json_response("fail", "Description incorrect");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
		"app_id"=>$config_param1,
		'name'=>$_POST['new_function']['name']
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}
	$version_id = $mongodb_con->generate_id();
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
		"app_id"=>$config_param1,
		"name"=>$_POST['new_function']['name'],
		"des"=>$_POST['new_function']['des'],
		"type"=>"function",
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
	if( $res['status'] == "success" ){
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
			"_id"=>$mongodb_con->get_id($version_id),
			"app_id"=>$config_param1,
			"function_id"=>$res['inserted_id'],
			"name"=>$_POST['new_function']['name'],
			"des"=>$_POST['new_function']['des'],
			"type"=>"function",
			"active"=>true,
			"created"=>date("Y-m-d H:i:s"),
			"updated"=>date("Y-m-d H:i:s"),
			"version"=>1,
			"output-type"	=> "application/json",
			"input-method"	=> "POST",
			"input-type"	=> "application/json",	
			"auth-type"	=> "None",		
		]);

		event_log( "system", "app_function_create", [
			"app_id"=>$config_param1, 
			"function_id"=>$res['inserted_id'],
			"function_version_id"=>$version_id
		]);

		update_app_pages( $config_param1 );
		json_response($res);
	}else{
		json_response($res);
	}
	exit;
}

if( $_POST['action'] == "app_function_import_create" ){
	//print_r( $_POST['file'] );
	if( $config_param1 != $_POST['app_id']){
		json_response([
			"status"=>"error",
			"error"=>"Incorrect URL"
		]);
	}
	if( !isset($_FILES['file']['name']) || !isset($_POST['password']) ){
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
	$import_function_data = json_decode($data,true);
	//print_r( $function );
	if( !isset($import_function_data['name']) || !isset($import_function_data['des']) || !isset($import_function_data['engine']) ){
		json_response([
			"status"=>"error",
			"error"=>"API data invalid"
		]);
	}

	$n = $import_function_data['name'];
	if( isset($_POST['name']) && $_POST['name'] != "" ){
		$n = $_POST['name'];
		if( !preg_match("/^[a-z0-9\.\-\_]{3,100}$/i", $n) ){
			json_response("fail", "Name incorrect");
		}
		$import_function_data['name'] = $n;
	}
	$d = $import_function_data['des'];
	if( isset($_POST['des'])  && $_POST['des'] != "" ){
		$d = $_POST['des'];
		if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{5,250}$/i", $d) ){
			json_response("fail", "Description incorrect");
		}
		$import_function_data['des'] = $d;
	}

	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
		"app_id"=>$config_param1,
		"name"=>$n
	]);
	if( $res['data'] ){
		json_response([
			"status"=>"error",
			"error"=>"API with same name already exists",
			"name"=>$import_function_data['name'],
			"des"=>$import_function_data['des'],
		]);
	}

	$import_function_data['created'] = date("Y-m-d H:i:s");
	$import_function_data['updated'] = date("Y-m-d H:i:s");

	$function_id = $mongodb_con->generate_id();
	$version_id = $mongodb_con->generate_id();
	$import_function_data['app_id'] = $config_param1;
	$import_function_data['function_id'] = $function_id;
	$import_function_data['_id'] = $version_id;
	$import_function_data['version'] = 1;
	$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
		"_id"=>$function_id,
		"app_id"=>$config_param1,
		"name"=>$import_function_data['name'],
		"des"=>$import_function_data['des'],
		"type"=>"function",
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
		"active"=>true,
		"version"=>1,
		"version_id"=>$version_id,
		"output-type"	=> isset($import_api_data['output-type'])?$import_api_data['output-type']:"application/json",
		"input-method"	=> isset($import_api_data['input-method'])?$import_api_data['input-method']:"POST",
		"input-type"	=> isset($import_api_data['input-type'])?$import_api_data['input-type']:"application/json",
		"auth-type"	=> isset($import_api_data['auth-type'])?$import_api_data['auth-type']:"None",
	]);
	$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", $import_function_data );

	event_log( "system", "app_function_import_create", [
		"app_id"=>$config_param1, 
		"function_id"=>$function_id,
		"function_version_id"=>$version_id
	]);
	json_response([
		"status"=>"success",
		"function_id"=>$function_id,
		"version_id"=>$version_id,
	]);
}

if( $config_param3 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param3) ){
		echo404("Incorrect API ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
		'app_id'=>$app['_id'],
		'_id'=>$config_param3
	]);
	if( !$res['data'] ){
		echo404("Api not found!");
	}
	$main_function = $res['data'];
}

if( $config_param4 && $main_function ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param4) ){
		echo404("Incorrect API Version ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
		"function_id"=>$main_function['_id'],
		"_id"=>$config_param4
	]);
	if( $res['data'] ){
		$function = $res['data'];
	}else{
		echo404("Api version not found!");
	}

	$new_version_series = 0;
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
		"app_id"=>$config_param1,
		"function_id"=>$main_function['_id'],
	],[
		'projection'=>[ 'version'=>1, 'updated'=>1, 'vn'=>1 ]
	]);
	if( $res['data'] ){
		$function_versions = $res['data'];
		foreach( $function_versions as $i=>$j ){
			if( $new_version_series < $j['version'] ){
				$new_version_series = $j['version'];
			}
		}
	}
	$new_version_series++;

	if( $main_function['version_id'] == $function['_id'] ){
		
	}

	if( !isset($function['auth-type']) ){
		$function['auth-type'] = "None";
	}
	if( isset($function['test']) ){
		if( !isset($function['test']['headers']['Access-Key']) ){
			$function['test']['headers']['Access-Key'] = "None";
		}
	}

	//print_pre( $api );exit;
	//print_pre( $function );exit;

	if( $_POST['action'] == "load_engine_data" ){
		json_response([
			"status"=>	"success", 
			"engine"=>	($function['engine']?$function['engine']:[]), 
			"test"=>	($function['test']?$function['test']:[])
		]);
	}

	if( $_POST['action'] == "app_function_export" ){
		//print_r( $function );exit;
		$fn = preg_replace("/\W/", "", $function['name']).".".$function['_id'].".api";
		unset($function['_id']);unset($function['app_id']);unset($function['function_id']);
		//unset($function['name']);unset($function['des']);
		unset($function['created']);unset($function['updated']);unset($function['version']);
		$d = json_encode($function);
		$d = "type:api_export\nexport_version:1\n".@openssl_encrypt($d, "aes256", $_POST['password']."123456");
		event_log( "system", "app_function_export", [
			"app_id"=>$config_param1, 
			"function_id"=>$function['_id'],
		]);
		json_response([
			"status"=>"success",
			"content"=>$d,
			"filename"=>$fn
		]);
	}

	if( $_POST['action'] == "app_function_import" ){
		//print_r( $_POST['file'] );
		if( !isset($_FILES['file']['name']) || !isset($_POST['password']) ){
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
		$import_function_data = json_decode($data,true);
		//print_r( $function );
		if( !isset($import_function_data['engine']) || !isset($import_function_data['input-type']) || !isset($import_api_data['output-type']) ){
			json_response([
				"status"=>"error",
				"error"=>"Function data invalid"
			]);
		}

		$import_function_data['created'] = date("Y-m-d H:i:s");
		$import_function_data['updated'] = date("Y-m-d H:i:s");

		if( $_POST['version'] == "create" ){
			$new_version_id = $mongodb_con->generate_id();
			$function['_id'] = $new_version_id;
			$function['version'] = $new_version_series;
			$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", $function);
			$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
				"app_id"=>$config_param1,
				"_id"=>$main_function['_id'],
			], [
				'version_id'=>$function['_id'],
				"version"=>$new_version_series,
			]);
			$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
				"app_id"=>$config_param1,
				"function_id"=>$main_function['_id'],
				"_id"=>$new_version_id
			], $import_function_data);

			event_log( "system", "app_function_import", [
				"app_id"=>$config_param1, 
				"function_id"=>$function['_id'],
				"function_version_id"=>$new_version_id
			]);

		}else{
			$new_version_id = $config_param4;
			$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
				"app_id"=>$config_param1,
				"function_id"=>$main_function['_id'],
				"_id"=>$config_param4
			], $import_function_data);

			event_log( "system", "app_function_import", [
				"app_id"=>$config_param1, 
				"function_id"=>$main_function['_id'],
				"function_version_id"=>$config_param4
			]);			
		}

		json_response([
			"status"=>"success",
			"new_version_id"=>$new_version_id,
		]);
	}

	if( $_POST['action'] == "app_function_load_versions_info" ){
		json_response([
			"status"=>"success",
			"versions"=>$function_versions,
			"current_version"=>$main_function['version_id'],
		]);
	}

	if( $_POST['action'] == "app_function_delete" ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
			"app_id"=>$config_param1,
			"function_id"=>$main_function['_id'],
			"_id"=>$_POST['version_id']
		]);
		if( !$res['data'] ){
			json_response([
				"status"=>"fail",
				"error"=>"Version not found",
			]);
		}
		if( $main_function['version_id'] == $_POST['version_id'] ){
			json_response([
				"status"=>"fail",
				"error"=>"You cannot delete current version",
			]);
		}
		$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
			"app_id"=>$config_param1,
			"function_id"=>$main_function['_id'],
			"_id"=>$_POST['version_id']
		]);

		event_log( "system", "app_function_delete_version", [
			"app_id"=>$config_param1, 
			"function_id"=>$main_function['_id'],
			"function_version_id"=>$_POST['version_id']
		]);	

		json_response($res);exit;
	}

	if( $_POST['action'] == "app_function_clone" ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
			"app_id"=>$config_param1,
			"function_id"=>$main_function['_id'],
			"_id"=>$_POST['from_version_id']
		]);
		if( $res['data'] ){
			$from_function = $res['data'];
		}else{
			json_response([
				"status"=>"fail",
				"error"=>"Source function version not found",
			]);
		}

		$new_version_id = $mongodb_con->generate_id();
		$from_function['vn'] = "Clone of version:" . $from_function['version'];
		$from_function['version'] = $new_version_series;
		$from_function['_id'] = $new_version_id;
		$from_function['created'] = date("Y-m-d H:i:s");
		$from_function['updated'] = date("Y-m-d H:i:s");

		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", $from_function);

		event_log( "system", "app_function_clone", [
			"app_id"=>$config_param1, 
			"function_id"=>$main_function['_id'],
			"from_version_id"=>$_POST['from_version_id'],
			"from_version_series"=>$from_function['version'],
			"to_version_id"=>$new_version_id,
			"to_version_series"=>$new_version_series,
		]);

		json_response([
			"status"=>"success",
			"error"=>"",
		]);
	}
	if( $_POST['action'] == "app_function_switch" ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
			"app_id"=>$config_param1,
			"function_id"=>$main_function['_id'],
			"_id"=>$_POST['version_id']
		]);
		if( $res['data'] ){
			$from_function = $res['data'];
		}else{
			json_response([
				"status"=>"fail",
				"error"=>"Source function version not found",
			]);
		}

		$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
			"_id"=>$main_function['_id'],
		],[
			"version_id"=>$from_function['_id'],
			"version"=>$from_function['version'],
		]);

		event_log( "system", "app_function_version_switch", [
			"app_id"=>$config_param1, 
			"function_id"=>$main_function['_id'],
			"version_id"=>$from_function['_id'],
			"version"=>$from_function['version'],
		]);

		json_response([
			"status"=>"success",
			"error"=>"",
		]);
	}



	unset($function['engine']);
	unset($function['test']);
	if( $_POST['action'] == "edit_function" ){
		$t = validate_token("edit_function". $_POST['edit_api']['_id'], $_POST['token']);
		if( $t != "OK" ){
			json_response("fail", $t);
		}
		if( !preg_match("/^[a-z0-9\.\-\_\ ]{3,100}$/i", $_POST['edit_api']['name']) ){
			json_response("fail", "Name incorrect");
		}
		if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{5,250}$/i", $_POST['edit_api']['des']) ){
			json_response("fail", "Description incorrect");
		}
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
			'name'=>$_POST['edit_api']['name'],
			'_id'=>['$ne'=>$mongodb_con->get_id($_POST['edit_api']['function_id']) ]
		]);
		if( $res['data'] ){
			json_response("fail", "Name is already in use");
		}
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
			'_id'=>$_POST['edit_api']['function_id']
		],[
			"name"=>$_POST['edit_api']['name'],
			"des"=>$_POST['edit_api']['des'],
			"updated"=>date("Y-m-d H:i:s"),
			"active"=>true,
		]);
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
			'_id'=>$config_param4
		],[
			"name"=>$_POST['edit_api']['name'],
			"des"=>$_POST['edit_api']['des'],
			"updated"=>date("Y-m-d H:i:s"),
			"active"=>true,
		]);

		event_log( "system", "app_function_edit", [
			"app_id"=>$config_param1, 
			"function_id"=>$_POST['edit_api']['function_id'],
		]);

		update_app_last_change_date( $config_param1 );
		json_response($res);
		exit;
	}

	if( $_POST['action'] == "save_engine_test" ){
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
			"app_id"=> $config_param1,
			"_id"=>$config_param4
		],[
			"test"=>$_POST['test']
		]);
		if($res["status"] == "fail" ){
			json_response("fail",$res["error"]);
		}

		event_log( "system", "app_function_save_test", [
			"app_id"=>$config_param1, 
			"function_id"=>$config_param3,
			"function_version_id"=>$config_param4
		]);

		update_app_last_change_date( $config_param1 );
		json_response("success","ok");
	}

	if( $_POST['action'] == "save_engine_data" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['function_id'] ) ){
			json_response("fail", "Error In Page Id");
		}else if( !preg_match("/^[a-f0-9]{24}$/", $_POST['version_id'] ) ){
			json_response("fail", "Error In Version Id");
		}
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
			"_id"=>$_POST['version_id']
		]);
		if( $res["status"] == "fail" ){
			json_response("fail","Error finding version: ".$res["error"]);
		}else if( !$res['data'] ){
			json_response("fail","Version record not found");
		}
		$version = $res['data'];

		if( $version['function_id'] != $_POST['function_id'] ){
			json_response("fail","Incorrect version ID mapping");
		}

		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
			"_id"=>$_POST['function_id']
		]);
		if( $res["status"] == "fail" ){
			json_response("fail","Error finding API: ".$res["error"]);
		}else if( !$res['data'] ){
			json_response("fail","API record not found");
		}
		$function = $res['data'];

		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
			"_id"=> $_POST['version_id']
		],[
			"type"		=> "function",
			"input-method"	=> "POST",
			"input-type"	=> "application/json",
			"output-type"	=> "application/json",
			"auth-type"	=> "None",
			"engine"	=> $_POST['data'],
			"updated"	=> date("Y-m-d H:i:s"),
		]);
		if( $res["status"] == "fail" ){
			json_response("fail","Version update failed: ".$res["error"]);
		}

		if( $function['version_id'] == $version['_id'] ){
			$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
				"_id"=> $_POST['function_id']
			],[
				"type"		=> "function",
				"input-method"	=> "POST",
				"input-type"	=> "application/json",
				"output-type"	=> "application/json",
				"auth-type"	=> "None",
				"engine"	=> $_POST['engine'],
				"updated"	=> date("Y-m-d H:i:s"),
			]);
			if( $res["status"] == "fail" ){
				json_response("fail","API update failed: ".$res["error"]);
			}
		}

		event_log( "system", "app_function_save_engine", [
			"app_id"=>$config_param1,
			"function_id"=>$_POST['function_id'],
			"function_version_id"=>$_POST['version_id']
		]);

		update_app_last_change_date( $config_param1 );
		json_response("success", "OK");
	}

}