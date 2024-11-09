<?php



	$config_allow_actions = [
		"apis"=> [ "*", "invoke" ],
		"tables"=> ["*","find", "scan", "insert", "update", "delete"],
		"storage"=> ["*","list_files", "get_file", "get_raw_file", "put_file", "create_signed_url", "delete_file"],
		"files"=> ["*","list_files", "get_file", "get_raw_file", "get_file_by_id", "get_raw_file_by_id", "put_file", "delete_file"],
		"objects"=> ["*","listObjects","getObject","getObjectTemplate","getObjectRecords","getObjectNodes","objectCreate","objectCreateWithTemplate","objectLabelUpdate","objectTypeUpdate","objectAliasUpdate","objectInstanceUpdate","objectPropertiesUpdate","objectNodesTruncate","objectDelete","objectConverToDataset","objectConverToNode","objectTemplatePropertyCreate","objectTemplatePropertyUpdate","objectTemplatePropertyDelete","objectTemplateEnable","objectTemplateOrderUpdate","dataSetRecordCreate","dataSetRecordUpdate","dataSetRecordDelete","dataSetTruncate","keywordSearch"],
	];

	function validate_policy($p){
		global $config_allow_actions;
		if( gettype($p) != "array" ){
			return ['status'=>"fail", "error"=>"Invalid policy format (1)"];
		}
		if( array_keys($p)[0] !== 0 ){
			return ['status'=>"fail", "error"=>"Invalid policy format (2)"];
		}
		if( sizeof($p) > 5 ){
			return ['status'=>"fail", "error"=>"Max 5 policies allowed (3)"];
		}
		foreach( $p as $pi=>$pd ){
			if( !isset($pd['service']) || !isset($pd['actions']) || !isset($pd['things']) || !isset($pd['records']) ){
				return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Invalid Key:".$pdi];
			}
			if( !is_string($pd['service']) || !is_array($pd['actions']) || !is_array($pd['things']) || !is_array($pd['records']) ){
				return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Invalid Policy Format"];
			}
			foreach( $pd as $pdi=>$pdd ){
				if( !in_array($pdi, ["service", "actions", "records", "things"] ) ){
					return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Invalid Key:".$pdi];
				}
			}
			if( !in_array($pd['service'], ["apis", "tables", "files", "storage", "objects"] ) ){
				return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Invalid Service:".$pd['service']];
			}
			if( !isset($config_allow_actions[ $pd['service'] ]) ){
				return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Actions not available for service:".$pd['service']];
			}
			foreach( $pd['actions'] as $pdacti=>$pdaction ){
				if( !in_array( $pdaction, $config_allow_actions[ $pd['service'] ]) ){
					return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Invalid Action `".$pdaction."` in Service:".$pd['service']];
				}
			}
			$k = [];
			//print_r( $pd['things'] );
			foreach( $pd['things'] as $pdti=>$pdthing ){
				if( !is_array($pdthing) ){
					return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Property `".($pdti+1)."` Invalid"];
				}
				if( isset($k[ $pdthing['thing'] ]) ){
					return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Property `".$pdthing['thing']."` repeated"];
				}
				if( isset($k[ $pdthing['_id'] ]) ){
					return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Property `".$pdthing['_id']."` repeated"];
				}
				$k[ $pdthing['_id'] ]=1;
				$k[ $pdthing['thing'] ]=1;
				if( $pdthing['_id'] != "*" && !preg_match("/^[a-z0-9\:\-\_\.]{1,250}$/i", $pdthing['_id']) ){
					return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Invalid Property `".$pdthing['_id']."` "];
				}
				if( $pdthing['thing'] != "*" && !preg_match("/^[a-z0-9\:\-\_\.\ ]{1,250}$/i", $pdthing['thing']) ){
					return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Invalid Property `".$pdthing['thing']."` "];
				}
			}
			if( sizeof($pd['things']) > 10 ){
				return ['status'=>"fail", "error"=>"Policy ".($pi+1)." too many properties. use `*` or create separate policies"];
			}

			$k = [];
			foreach( $pd['records'] as $pdti=>$pdthing ){
				if( isset($k[ $pdthing ]) ){
					return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Record `".$pdthing."` repeated"];
				}
				$k[ $pdthing ]=1;
				if( $pdthing != "*" && !preg_match("/^[a-z0-9\:\.\-\_\/\@\*]{1,200}$/i", $pdthing) ){
					return ['status'=>"fail", "error"=>"Policy ".($pi+1)." Invalid Record `".$pdthing."` "];
				}
			}
			if( sizeof($pd['records']) > 10 ){
				return ['status'=>"fail", "error"=>"Policy ".($pi+1)." too many record entries. use `*` or create separate policies"];
			}
		}
		return ['status'=>"success"];
	}

	if( $_POST['action'] == "auth_load_users" ){
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_user_pool", [ "app_id"=>$config_param1 ], ['projection'=>['password'=>false] ] );
		foreach( $res['data'] as $i=>$j ){
			$res['data'][$i]['ch_pwd'] = false;
			$res['data'][$i]['_id_enc'] = md5($j['_id']. session_id() );
			$res['data'][$i]['password'] = "";
		}
		json_response($res);
	}

	if( $_POST['action'] == "auth_load_things" ){
		// $t = validate_token("get_global_apis.". $config_param1, $_POST['token']);
		// if( $t != "OK" ){
		// 	json_response("fail", $t);
		// }
		$tables = [];
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_tables_dynamic", [
			'app_id'=>$config_param1
		],[
			'sort'=>['table'=>1],
			'limit'=>200,
		]);
		foreach( $res['data'] as $i=>$j ){
			$tables[] = ["thing"=>"internal:".$j['table'], "_id"=>"table_dynamic:".$j['_id']];
		}

		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_databases", [
			'app_id'=>$config_param1
		],[
			'sort'=>['des'=>1],
			'limit'=>200,
			'projection'=>['details'=>false, 'm_i'=>false, 'user_id'=>false]
		]);
		foreach( $res['data'] as $i=>$j ){
			$res2 = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_tables", [
				'app_id'=>$config_param1,
				"db_id"=>$j['_id']
			],[
				'sort'=>['des'=>1],
				'limit'=>200,
				'projection'=>['f_n'=>false, 'source_schema'=>false, 'm_i'=>false, 'user_id'=>false ]
			]);
			foreach( $res2['data'] as $ii=>$jj ){
				$tables[] = ["thing"=>"external:".$j['des'] . ":" . $jj['des'], "_id"=>"table:".$jj['_id']];
			}
		}

		$apis = [];
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
			'app_id'=>$config_param1
		],[
			'sort'=>['path'=>1,'name'=>1],
			'limit'=>200,
			'projection'=>[
				'path'=>1,'name'=>true, 'des'=>true
			]
		]);
		//print_r( $res['data'] );exit;
		foreach( $res['data'] as $i=>$j ){
			$apis[] = ["thing"=>"api:".$j['path'].$j['name'], "_id"=>"api:".$j['_id']];
		}

		$apis[] = ["thing"=>"auth_api:generate_access_token", "_id"=>"auth_api:10001"];
		$apis[] = ["thing"=>"auth_api:user_auth", "_id"=>"auth_api:10002"];
		$apis[] = ["thing"=>"auth_api:user_auth_captcha", "_id"=>"auth_api:10003"];
		$apis[] = ["thing"=>"auth_api:verify_session_key", "_id"=>"auth_api:10004"];
		$apis[] = ["thing"=>"auth_api:verify_user_session", "_id"=>"auth_api:10006"];
		$apis[] = ["thing"=>"auth_api:assume_session_key", "_id"=>"auth_api:10005"];
		$apis[] = ["thing"=>"auth_api:assume_user_session_key", "_id"=>"auth_api:10007"];
		$apis[] = ["thing"=>"auth_api:user_session_logout", "_id"=>"auth_api:10009"];
		$apis[] = ["thing"=>"captcha:get", "_id"=>"captcha:10101"];

		$files = [];
		$files[] = ["thing"=>"file:internal", "_id"=>"file:f0010"];
		// $files[] = ["thing"=>"file:get_file", "_id"=>"file:f0020"];
		// $files[] = ["thing"=>"file:get_raw_file", "_id"=>"file:f0021"];
		// $files[] = ["thing"=>"file:put_file", "_id"=>"file:f0030"];
		// $files[] = ["thing"=>"file:delete_file", "_id"=>"file:f0040"];

		$storage = [];
		//$storage[] = ["thing"=>"file:external", "_id"=>"file:f0010"];
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults", [
			'app_id'=>$config_param1
		],[
			'sort'=>['des'=>1],
			'limit'=>200,
			'projection'=>['details'=>false, 'm_i'=>false, 'user_id'=>false]
		]);
		foreach( $res['data'] as $i=>$j ){
			$storage[] = ["thing"=>"file:storage_vault:".$j['des'], "_id"=>"storage_vault:".$j['_id']];
		}

		$objects = [];
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_graph_dbs", [
			'app_id'=>$config_param1
		],[
			'sort'=>['name'=>1],
			'limit'=>200,
			'projection'=>['name'=>true, 'type'=>true]
		]);
		foreach( $res['data'] as $i=>$j ){
			$objects[] = ["thing"=>"object:".$j['name'], "_id"=>"object:".$j['_id']];
		}

		json_response([
			'status'=>"success", 
			"tables"=>$tables, 
			"apis"=>$apis, 
			"files"=>$files, 
			"storage"=>$storage,
			"objects"=>$objects,
			"actions"=>$config_allow_actions,
		]);
	}
	if( $_POST['action'] == "load_access_keys" ){
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", ['t'=>"ak", "app_id"=>$config_param1 ] );
		foreach( $res['data'] as $i=>$j ){
			$res['data'][$i]['_id_enc'] = md5( $j['_id'] . session_id() );
			$res['data'][$i]['secret'] = "";
		}
		json_response($res);
	}
	if( $_POST['action'] == "load_tokens" ){
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", [
			't'=>"uk", "app_id"=>$config_param1
		], ['sort'=>['_id'=>-1]] );
		json_response($res);
	}
	if( $_POST['action'] == "load_sessions" ){
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_user_sessions", [
			"app_id"=>$config_param1
		], ['sort'=>['_id'=>-1], 'projection'=>['roles'=>false] ] );
		json_response($res);
	}
	if( $_POST['action'] == "load_roles" ){
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_user_roles", [
			"app_id"=>$config_param1 
		], ["sort"=>["name"=>1]] );
		foreach( $res['data'] as $i=>$j ){
			$res['data'][$i]['_id_enc'] = md5( $j['_id'] . session_id() );
		}
		json_response($res);
	}
	if( $_POST['action'] == "auth_user_delete" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['user_id']) ){
			json_response(['status'=>"fail","error"=>"Incorrect user id" ]);
		}
		$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_user_pool", ["app_id"=>$config_param1,'_id'=>$_POST['user_id']] );
		event_log( "system", "auth_user_delete", [
			"app_id"=>$config_param1, 
			"user_id"=>$_POST['user_id'],
		]);
		json_response(['status'=>"success"]);
	}
	if( $_POST['action'] == "auth_session_key_delete" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['access_key_id']) ){
			json_response(['status'=>"fail","error"=>"Incorrect key" ]);
		}
		$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", ["app_id"=>$config_param1, 't'=>"uk", '_id'=>$_POST['access_key_id']] );
		event_log( "system", "auth_session_key_delete", [
			"app_id"=>$config_param1, 
			"access_key_id"=>$_POST['access_key_id'],
		]);
		json_response(['status'=>"success"]);
	}

	if( $_POST['action'] == "auth_access_key_delete" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['access_key_id']) ){
			json_response(['status'=>"fail","error"=>"Incorrect key id" ]);
		}
		$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", [ "app_id"=>$config_param1, 't'=>"ak", '_id'=>$_POST['access_key_id']] );
		event_log( "system", "auth_access_key_delete", [
			"app_id"=>$config_param1, 
			"access_key_id"=>$_POST['access_key_id'],
		]);
		json_response(['status'=>"success"]);
	}
	if( $_POST['action'] == "auth_role_delete" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['role_id']) ){
			json_response(['status'=>"fail","error"=>"Incorrect Role Id" ]);
		}
		$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_user_roles", [ "app_id"=>$config_param1, '_id'=>$_POST['role_id']] );
		event_log( "system", "auth_role_delete", [
			"app_id"=>$config_param1, 
			"role_id"=>$_POST['role_id'],
		]);
		json_response(['status'=>"success"]);
	}

	if( $_POST['action'] == "save_user" ){
		$user = $_POST['user'];
		$user_id = "";
		if( $user["app_id"]!=$config_param1 ){
			json_response(['status'=>"fail", "error"=>"ID incorrect"]);
		}
		if( isset($user['_id']) ){
			$user_id = $user['_id'];
			unset($user['_id']);
			if( md5($user_id.session_id()) != $user['_id_enc'] ){
				json_response(['status'=>"fail", "error"=>"ID incorrect"]);
			}
			if( !preg_match("/^[a-f0-9]{24}$/", $user_id) ){
				json_response(['status'=>"fail", "error"=>"ID incorrect"]);
			}
			//print_r( ["app_id"=>$config_param1, "_id"=>['$ne'=>$user_id], "username"=>$user['username']] );exit;
			$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_user_pool", ["app_id"=>$config_param1, "_id"=>['$ne'=>$user_id], "username"=>$user['username']] );
		}else{
			$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_user_pool", ["app_id"=>$config_param1, "username"=>$user['username']] );
		}
		if( $res['data'] ){
			json_response([
				'status'=>"fail", 
				"error"=>"username already exists", 
				"rec"=>$res['data']
			]);
		}
		unset($user['ch_pwd']);
		unset($user['_id_enc']);
		$user['updated'] = date("Y-m-d H:i:s");
		if( isset($user['password']) && $user['password'] == "" ){
			unset($user['password']);
		}
		if( isset($user['password']) && $user['password'] != "" ){
			$user['password']=hash("whirlpool",$user['password']."123456");
			$user["pwdexpire_date"] = date("Y-m-d H:i:s", time()+(30*86400) );
		}
		$user["app_id"]=$config_param1;

		if( isset($user['roles']) ){
			if( !is_array($user['roles']) ){
				unset($user['roles']);
			}
			$k = [];
			foreach( $user['roles'] as $i=>$j ){
				if( !is_array($j) ){
					json_response(['status'=>"fail", "error"=>"Role Invalid"]);
				}
				if( !isset($j['_id']) || !isset($j['name']) ){
					json_response(['status'=>"fail", "error"=>"Role Invalid"]);
				}
				if( isset($k[ $j['_id'] ]) ){
					json_response(['status'=>"fail", "error"=>"Role Repeated"]);
				}
				$k[ $j['_id'] ] =1;
			}
		}
		if( $user_id ){
			$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_user_pool", [ "app_id"=>$config_param1, '_id'=>$user_id ], $user );
		}else{
			$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_user_pool", $user );
			$user_id = $res['inserted_id'];
		}
		event_log( "system", "auth_save_user", [
			"app_id"=>$config_param1, 
			"user_id"=>$user_id,
		]);
		json_response($res);
	}

	if( $_POST['action'] == "save_key" ){
		$key = $_POST['key'];
		$key_id = "";
		if( isset($key['_id']) ){
			$key_id = $key['_id'];
			if( $key['_id_enc'] != md5( $key_id . session_id() ) ){
				json_response(['status'=>"fail", "error"=>"Key incorrect"]);
			}
			unset($key['_id']);
			$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", ['app_id'=>$config_param1, '_id'=>$key_id] );
			if( !$res['data'] ){
				json_response(['status'=>"fail", "error"=>"Key not found"]);
			}
		}
		if( !isset($key['des']) ){
			json_response(['status'=>"fail", "error"=>"Description is required"]);
		}else if( !preg_match("/^[a-z0-9\-\_\,\.\(\)\ ]{1,200}$/i",$key['des']) ){
			json_response(['status'=>"fail", "error"=>"Description should be simple. no special chars. max length 200."]);
		}
		if( $key['s'] === true || $key['s'] === "true" ){
			$key['s'] = true;
			if( !preg_match("/^[0-9]+$/",$key['max_s']) && $key['max_s'] != "-1" ){
				json_response(['status'=>"fail", "error"=>"Max Sessions should be between 1 to 1000"]);
			}
			$key['max_s'] = (int)$key['max_s'];
			if( $key['max_s'] != "-1" ){
				if( $key['max_s'] < 1 || $key['max_s'] > 1000 ){
					json_response(['status'=>"fail", "error"=>"Max Sessions should be between 1 to 1000"]);
				}
			}
		}

		date_default_timezone_set("UTC");
		$key['expiret'] = new \MongoDB\BSON\UTCDateTime( (strtotime($key['expire_utc'])*1000)+(5*86400*1000) );
		//$key['expiret'] = [ '$date'=> [ '$numberLong'=> (string)((strtotime($key['expire_utc'])*1000) ) ] ];
		//+(5*86400*1000)
		//disabled for Admin AccessKeys
		date_default_timezone_set( $config_global_apimaker['timezone'] );
		$key["updated"] = date("Y-m-d H:i:s");
		unset($key['_id_enc']);
		$key['app_id'] = $config_param1;
		$key['t'] = "ak"; // ak admin key, uk user key

		//print_r( $key['policies'] );
		$vres = validate_policy( $key['policies'] );
		if( $vres['status'] == "fail" ){
			json_response($vres);
		}
		//print_r( $key );exit;
		if( $key_id ){
			$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", [ "app_id"=>$config_param1, '_id'=>$key_id] , $key );
		}else{
			$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", $key );
			$key_id = $res['inserted_id'];
		}

		event_log( "system", "auth_save_key", [
			"app_id"=>$config_param1, 
			"access_key_id"=>$key_id,
		]);

		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", ['_id'=>$key_id]);
		$res['data']['_id_enc'] = md5($res['data']['_id'].session_id());
		json_response([
			"status"=>"success",
			"key"=>$res['data']
		]);
	}


if( $_POST['action'] == "auth_save_role" ){
	$key = $_POST['role'];
	$key_id = "";
	if( isset($key['_id']) ){
		$key_id = $key['_id'];
		if( $key['_id_enc'] != md5( $key_id . session_id() ) ){
			json_response(['status'=>"fail", "error"=>"Key incorrect"]);
		}
		unset($key['_id']);
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_user_roles", ['app_id'=>$config_param1, '_id'=>$key_id] );
		if( !$res['data'] ){
			json_response(['status'=>"fail", "error"=>"Key not found"]);
		}
	}
	$key["updated"] = date("Y-m-d H:i:s");
	unset($key['_id_enc']);
	$key['app_id'] = $config_param1;

	$vres = validate_policy( $key['policies'] );
	if( $vres['status'] == "fail" ){
		json_response($vres);
	}

	if( $key_id ){
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_user_roles", [ "app_id"=>$config_param1, '_id'=>$key_id] , $key );
	}else{
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_user_roles", $key );
		$key_id = $res['inserted_id'];
	}

	event_log( "system", "auth_save_role", [
		"app_id"=>$config_param1, 
		"role_id"=>$key_id,
	]);

	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_user_roles", ['_id'=>$key_id]);
	$res['data']['_id_enc'] = md5($res['data']['_id'].session_id());
	json_response([
		"status"=>"success",
		"role"=>$res['data']
	]);
}
	

