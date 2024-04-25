<?php

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
		$apis[] = ["thing"=>"auth_api:assume_session_key", "_id"=>"auth_api:10005"];
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


		json_response([
			'status'=>"success", 
			"tables"=>$tables, 
			"apis"=>$apis, 
			"files"=>$files, 
			"storage"=>$storage
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
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", ['t'=>"uk", "app_id"=>$config_param1], ['sort'=>['_id'=>-1]] );
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
		json_response(['status'=>"success"]);
	}
	if( $_POST['action'] == "auth_session_key_delete" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['access_key_id']) ){
			json_response(['status'=>"fail","error"=>"Incorrect key" ]);
		}
		$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", ["app_id"=>$config_param1, 't'=>"uk", '_id'=>$_POST['access_key_id']] );
		json_response(['status'=>"success"]);
	}

	if( $_POST['action'] == "auth_access_key_delete" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['access_key_id']) ){
			json_response(['status'=>"fail","error"=>"Incorrect key id" ]);
		}
		$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", [ "app_id"=>$config_param1, 't'=>"ak", '_id'=>$_POST['access_key_id']] );
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
			json_response(['status'=>"fail", "error"=>"username already exists", "rec"=>$res['data'] ]);
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
		if( $user_id ){
			$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_user_pool", [ "app_id"=>$config_param1, '_id'=>$user_id ], $user );
		}else{
			$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_user_pool", $user );
		}
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
		$key["expire"] = (int)$key["expire"];
		if( isset($_POST['expire_minits']) ){
			date_default_timezone_set("UTC");
			$key["expire"] = time()+((int)$_POST['expire_minits']*60);
		}
		//$key['expiret'] = date("Y-m-d H:i:s", $key['expire']);
		$key['expiret'] = new \MongoDB\BSON\UTCDateTime($key["expire"]*1000);
		$key["updated"] = date("Y-m-d H:i:s");
		unset($key['_id_enc']);
		$key['app_id'] = $config_param1;
		$key['t'] = "ak"; // ak admin key, uk user key
		if( $key_id ){
			$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", [ "app_id"=>$config_param1, '_id'=>$key_id] , $key );
		}else{
			$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", $key );
			$key_id = $res['inserted_id'];
		}

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
	if( $key_id ){
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_user_roles", [ "app_id"=>$config_param1, '_id'=>$key_id] , $key );
	}else{
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_user_roles", $key );
		$key_id = $res['inserted_id'];
	}

	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_user_roles", ['_id'=>$key_id]);
	$res['data']['_id_enc'] = md5($res['data']['_id'].session_id());
	json_response([
		"status"=>"success",
		"role"=>$res['data']
	]);
}
	

