<?php

function engine_auth_api( $api_slug, $post ){

	//print_r( $post );exit;

	global $mongodb_con;
	global $app_id;
	global $db_prefix;
	if( $api_slug == "verify_session_key" ){
		if( !isset($post['session-key']) ){
			respond(200, "application/json", [], json_encode(["status"=>"fail", "error"=>"Session Key required"]) );
		}else if( !preg_match( "/^[a-f0-9]{24}$/", $post['session-key'] ) ){
			respond(200, "application/json", [], json_encode(["status"=>"fail", "error"=>"Session Key Incorrect"]) );
		}
		$res = $mongodb_con->find_one( $db_prefix . "_user_keys", ["app_id"=>$app_id, '_id'=>$post['session-key']] );
		if( !$res['data'] ){
			respond(200, "application/json", [], json_encode(["status"=>"fail", "error"=>"Session Key Expired"]) );
		}
		//print_r( $res['data'] );exit;
		$e = $res['data']['expire'];
		//echo ($e - time());
		if( $e > time() && $res['data']['ips'][0] == $_SERVER['REMOTE_ADDR'] . "/32" ){
			respond(200, "application/json", [], json_encode(["status"=>"fail", "error"=>"SessionOK"]) );
		}else{
			respond(200, "application/json", [], json_encode(["status"=>"fail", "error"=>"Session Expired", "e"=>($e - time())]) );
		}
	}else if( $api_slug == "generate_access_token" ){
		if( !isset($post['access_key']) ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Access Key required"]) );
		}else if( !preg_match( "/^[a-f0-9]{24}$/", $post['access_key'] ) ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Access Key incorrect"]) );
		}

		$res = $mongodb_con->find_one( $db_prefix . "_user_keys", [ "app_id"=>$app_id, '_id'=>$post['access_key']]);
		if( !$res['data'] ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Access Key Not Found"]) );
		}
		if( !isset($res['data']['allow_sessions']) ){
			respond(500, "application/json", [], json_encode(["status"=>"fail", "error"=>"Access Key Policy does not allow sub sessions"]) );
		}else if( $res['data']['allow_sessions'] === false ){
			respond(500, "application/json", [], json_encode(["status"=>"fail", "error"=>"Access Key Policy does not allow sub sessions"]) );
		}
		if( isset($post['expire_minutes']) ){
			if( !is_numeric($post['expire_minutes']) ){
				respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Incorrect expire minutes"]) );
			}
			$expire = (int)$post['expire_minutes'];
		}else{
			$expire = 2;
		}
		$expire = time() + ($expire*60);
		if( isset($post['client_ip']) ){
			if( !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/", $post['client_ip']) ){
				respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Incorrect client ip"]) );
			}
			$user_ip = $post['client_ip'];
		}else{
			$user_ip = $_SERVER['REMOTE_ADDR'] . "/32";
		}

		$key = [];
		$key['active'] = 'y';
		$key['policies'] = $res['data']['policies'];
		$key['ips'] = [$user_ip];
		$key["app_id"] = $app_id;
		$key['expire'] = $expire;
		$key['expiret'] = new \MongoDB\BSON\UTCDateTime($expire*1000);
		$key['t'] = "uk";
		$key['hits'] = 0;
		$key['updated']= date("Y-m-d H:i:s");
		
		$res = $mongodb_con->insert( $db_prefix . "_user_keys", $key);

		event_log("auth", "generate_access_token", [
			"app_id"=>$app_id,
			"key_id"=>$res['inserted_id']
		]);

		respond(200, "application/json", [], json_encode(["status"=>"success", "access-key"=>$res['inserted_id'] ]) );

	}else if( $api_slug == "assume_session_key" ){

		if( !isset($post['role_id']) ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Role Id"]) );
		}else if( !preg_match( "/^[a-f0-9]{24}$/", $post['role_id'] ) ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Role ID Incorrect"]) );
		}

		$res = $mongodb_con->find_one( $db_prefix . "_user_roles", [ "app_id"=>$app_id, '_id'=>$post['role_id']]);
		if( !$res['data'] ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Role not found"]) );
		}
		if( !isset($post['expire_type']) ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Expire type missing"]) );
		}
		if( $post['expire_type'] == "In" ){
			if( !isset($post['expire_minutes']) ){
				respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Expire minutes missing"]) );
			}
			$expire = time() + ( (int)$post['expire_minutes'] * 60 );
		}else if( $post['expire_type'] == "At" ){
			if( !isset($post['expire_at']) ){
				respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Expire timestamp missing"]) );
			}
			$expire = strtotime( $post['expire_at'] );
		}
		if( isset($post['client_ip']) ){
			if( !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/", $post['client_ip']) ){
				respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Incorrect client ip"]) );
			}
			$user_ip = $post['client_ip'];
		}else{
			$user_ip = $_SERVER['REMOTE_ADDR'] . "/32";
		}
		if( !isset($post['max_hits']) || !is_numeric($post['max_hits']) ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Max hits missing"]) );
		}
		if( !isset($post['hits_per_minute']) || !is_numeric($post['hits_per_minute']) ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Hits per minute missing"]) );
		}

		$key = [];
		$key['active'] = 'y';
		$key['policies'] = $res['data']['policies'];
		$key['ips'] = [$user_ip];
		$key["app_id"] = $app_id;
		$key['expire'] = $expire;
		$key['expiret'] = new \MongoDB\BSON\UTCDateTime($expire*1000);
		$key['t'] = "uk";
		$key['hits'] = 0;
		$key['maxhits'] = $post['max_hits'];
		$key['hitsmin'] = $post['hits_per_minute'];
		$key['updated']= date("Y-m-d H:i:s");

		$res = $mongodb_con->insert( $db_prefix . "_user_keys", $key);

		event_log("auth", "assume_session_key", [
			"app_id"=>$app_id,
			"key_id"=>$res['inserted_id']
		]);

		respond(200, "application/json", [], json_encode(["status"=>"success", "session-key"=>$res['inserted_id'] ]) );

	}else if( $api_slug == "user_auth" ||  $api_slug == "user_auth_captcha"  ){

		if( !isset($post['username']) || !isset($post['password']) ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Username or Password wrong ."]) );
		}else if( !preg_match( "/^[a-z][a-z0-9\-]{2,50}$/", $post['username'] ) ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Username or Password wrong .. "]) );
		}

		if( $api_slug == "user_auth_captcha" ){
			if( !preg_match( "/^[a-f0-9]{24}$/", $post['code'] ) ){
				respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"CaptchaCode incorrect"]) );
			}
			//echo $db_prefix . "_captcha";exit;
			$cap_res = $mongodb_con->find_one( $db_prefix . "_captcha", ['_id'=>$post['code']] );
			if( !$cap_res['data'] ){
				respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Captcha mismatch..."]) );
			}
			if( $cap_res['data']['c'] != $post['captcha'] ){
				respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Captcha mismatch... .."]) );
			}else{
				$mongodb_con->delete_one( $db_prefix . "_captcha", ['_id'=>$post['code']] );
			}
			//echo "captcha check pending";exit;
		}

		$user_res = $mongodb_con->find_one( $db_prefix . "_user_pool", ['username'=>$post['username']]);
		if( !$user_res['data'] ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Username or password wrong..."]) );
		}
		
		if( hash("whirlpool",$post['password']."123456") != $user_res['data']['password'] ){
			respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Username or password wrong..."]) );
		}
		if( isset($post['expire_minutes']) ){
			if( !is_numeric($post['expire_minutes']) ){
				respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Incorrect expire minutes"]) );
			}
			$expire = (int)$post['expire_minutes'];
		}else{
			$expire = 5;
		}
		$expire = time() + ($expire*60);
		if( isset($post['client_ip']) ){
			if( !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/") ){
				respond(400, "application/json", [], json_encode(["status"=>"fail", "error"=>"Incorrect client IP"]) );
			}
			$user_ip = $post['client_ip'];
		}else{
			$user_ip = $_SERVER['REMOTE_ADDR'] . "/32";
		}

		$key = [];
		$key['active'] = 'y';
		$key['app_id'] = $app_id;
		$key['policies'] = $user_res['data']['policies'];
		$key['ips'] = [$user_ip];
		$key['expire'] = $expire;
		$key['expiret'] = new \MongoDB\BSON\UTCDateTime($expire*1000);
		$key['t'] = "uk";
		$key['updated']= date("Y-m-d H:i:s");
		
		$res = $mongodb_con->insert( $db_prefix . "_user_keys", $key);
		if( $res['status'] == "success" ){
			$new_key = $res['inserted_id'];

			event_log("auth", "auth_success", [
				"app_id"=>$app_id,
				"user_id"=>$user_res['data']['_id']
			]);
			event_log("auth", "generate_access_token", [
				"app_id"=>$app_id,
				"user_id"=>$user_res['data']['_id'],
				"key_id"=>$new_key
			]);

			$res = $mongodb_con->update_one( $db_prefix . "_user_pool", ["_id"=>$user_res['data']['_id']], ['last_login'=>date("Y-m-d H:i:s")] );

			$mongodb_con->delete_one( $db_prefix . "_user_keys", ["_id"=>$_SERVER['HTTP_ACCESS_KEY']] );
			respond(200, "application/json", [], json_encode(["status"=>"success", "access-key"=>$new_key ]) );
		}else{
			respond(500, "application/json", [], json_encode(["status"=>"fail", "error"=>"DB insert error" ]) );
		}

	}else{
		respond(404, "application/json", [], json_encode(["status"=>"fail", "error"=>"Unknown Api Slug" ]) );
	}

}