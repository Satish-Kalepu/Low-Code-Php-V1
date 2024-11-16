<?php

if( $_POST['action'] == "get_sdks" ){
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
		'app_id'=>$config_param1
	],[
		'sort'=>['name'=>1],
		'limit'=>200,
		'projection'=>[
			'version_id'=>true,
			'name'=>true,
		]
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == "delete_sdk" ){
	$t = validate_token("deletesdk". $config_param1 . $_POST['sdk_id'], $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['sdk_id']) ){
		json_response("fail", "ID incorrect");
	}
	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
		'_id'=>$_POST['sdk_id']
	]);
	$res = $mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_sdks_versions", [
		'sdk_id'=>$_POST['sdk_id']
	]);

	event_log( "system", "sdk_delete", [
		"app_id"=>$config_param1,
		"sdk_id"=>$_POST['sdk_id'],
	]);

	json_response($res);
}

if( $_POST['action'] == "create_sdk" ){
	if( !preg_match("/^[a-z][a-z0-9\.\-\_]{2,150}$/i", $_POST['new_sdk']['name']) ){
		json_response("fail", "Name incorrect");
	}
	if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{2,300}$/i", $_POST['new_sdk']['des']) ){
		json_response("fail", "Description incorrect");
	}
	if( !is_array($_POST['new_sdk']['keywords']) ){
		json_response("fail", "keywords incorrect");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
		"app_id"=>$config_param1,
		'name'=>$_POST['new_sdk']['name'],
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}

	$version_id = $mongodb_con->generate_id();
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
		"app_id"=>$config_param1,
		"name"=>$_POST['new_sdk']['name'],
		"des"=>$_POST['new_sdk']['des'],
		"keywords"=>$_POST['new_sdk']['keywords'],
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
		"active"=>true,
		"version"=>1,
		"version_id"=>$version_id,
	]);
	if( $res['status'] == 'success' ){
		$res2 = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_sdks_versions", [
			"_id"=>$mongodb_con->get_id($version_id),
			"app_id"=>$config_param1,
			"sdk_id"=>$res['inserted_id'],
			"name"=>$_POST['new_sdk']['name'],
			"des"=>$_POST['new_sdk']['des'],
			"keywords"=>$_POST['new_sdk']['keywords'],
			"created"=>date("Y-m-d H:i:s"),
			"updated"=>date("Y-m-d H:i:s"),
			"active"=>true,
			"version"=>1,
		]);

		event_log( "system", "sdk_create", [
			"app_id"=>$config_param1,
			"sdk_id"=>$res['inserted_id'],
			"sdk_version_id"=>$version_id,
		]);

		json_response(['status'=>'success', 'sdk_id'=>$res['inserted_id'], 'sdk_version_id'=>$version_id]);
	}else{
		json_response($res);
	}
	exit;
}


if( $config_param3 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param3) ){
		echo404("Incorrect sdk ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
		'app_id'=>$app['_id'],
		'_id'=>$config_param3
	]);
	if( !$res['data'] ){
		echo404("sdk not found!");
	}
	$main_sdk = $res['data'];
}

if( $config_param4 && $main_sdk ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param4) ){
		echo404("Incorrect sdk Version ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_sdks_versions", [
		"sdk_id"=>$main_sdk['_id'],
		"_id"=>$config_param4
	]);
	if( $res['data'] ){
		$sdk = $res['data'];
	}else{
		echo404("sdk version not found!");
	}

	if( $_POST['action'] == "create_sdk" ){
		$d = $_POST['data'];
		if( !is_array($d) ){
			json_response("fail", "Incorrect data!" );
		}
		foreach( $d['methods'] as $ci=>$cd ){
			if( !isset($cd['name']) ){
				json_response("fail", "Method name Missing!"  );
			}else if( !preg_match( "/^[a-z][a-zA-Z0-9\-\_]{2,100}$/i", $cd['name'] ) ){
				json_response("fail", "Method name Incorrect!" );
			}
			if( !isset($cd['des']) ){
				json_response("fail", "Method Description Missing!"  );
			}else if( !preg_match( "/^[a-z][a-z0-9\-\_\r\n\ \t\,\.\;\[\]\{\}\@\#\%\^\&\*\(\)\!]{2,100}$/i", $cd['des'] ) ){
				json_response("fail", "Method Description Incorrect!"  );
			}
			if( !isset($cd['inputs']) ){
				json_response("fail", "Method Inputs Missing!"  );
			}else if( !is_array( $cd['inputs'] ) ){
				json_response("fail", "Method Inputs Incorrect!"  );
			}
			foreach( $cd['inputs'] as $i=>$item ){
				if( !is_array($item) ){
					json_response("fail", "Method inputs ".$i." Incorrect" );
				}else if( !isset($item['name']) || !isset($item['type']) || !isset($item['default']) || !isset($item['mandatory']) ){
					json_response("fail", "Method inputs ".$i." Incorrect" );
				}
				if( !preg_match( "/^[a-z][a-zA-Z0-9\-\_]{2,100}$/i", $item['name'] ) ){
					json_response("fail", "Method input name Incorrect!" );
				}
				if( !in_array($item['type'], ["string", "int", "float", "double", "boolean", "array", "object", "null", "resource"]) ){
					json_response("fail", "Method input type Incorrect!" );
				}
			}
			foreach( $cd['outputs'] as $i=>$item ){
				if( !is_array($item) ){
					json_response("fail", "Method outputs ".$i." Incorrect" );
				}else if( !isset($item['name']) || !isset($item['type']) || !isset($item['default']) || !isset($item['mandatory']) ){
					json_response("fail", "Method outputs ".$i." Incorrect" );
				}
			}
		}
		

		exit;
	}


}