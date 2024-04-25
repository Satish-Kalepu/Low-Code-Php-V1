<?php


	if( $api_slug == "verify_session_key" ){
		if( !isset($_POST['session-key']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Session Key required"]);exit;
		}else if( !preg_match( "/^[a-f0-9]{24}$/", $_POST['session-key'] ) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Session Key incorrect"]);exit;
		}
		$res = $mongodb_con->find_one( $db_prefix . "_user_keys", ["app_id"=>$app_id, '_id'=>$_POST['session-key']] );
		if( !$res['data'] ){
			header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Session Expired"]);exit;
		}
		//print_r( $res['data'] );exit;
		$e = $res['data']['expire'];
		//echo ($e - time());
		if( $e > time() && $res['data']['ips'][0] == $_SERVER['REMOTE_ADDR'] . "/32" ){
			header("Content-Type: application/json");
			echo json_encode(["status"=>"success", "error"=>"SessionOK"]);exit;
		}else{
			header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Session Expired", "e"=>($e - time()) ]);exit;
		}
	}else if( $api_slug == "generate_access_token" ){

		if( !isset($_POST['access_key']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Access Key required"]);exit;
		}else if( !preg_match( "/^[a-f0-9]{24}$/", $_POST['access_key'] ) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Access Key incorrect"]);exit;
		}

		$res = $mongodb_con->find_one( $db_prefix . "_user_keys", [ "app_id"=>$app_id, '_id'=>$_POST['access_key']]);
		if( !$res['data'] ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Access Key Not Found"]);exit;
		}
		if( !isset($res['data']['allow_sessions']) ){
			http_response_code(500);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Access Key Policy does not allow sub sessions"]);exit;
		}else if( $res['data']['allow_sessions'] === false ){
			http_response_code(500);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Access Key Policy does not allow sub sessions"]);exit;
		}
		if( isset($_POST['expire_minutes']) ){
			if( !is_numeric($_POST['expire_minutes']) ){
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Incorrect expire minutes"]);exit;
			}
			$expire = (int)$_POST['expire_minutes'];
		}else{
			$expire = 2;
		}
		$expire = time() + ($expire*60);
		if( isset($_POST['client_ip']) ){
			if( !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/", $_POST['client_ip']) ){
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Incorrect client IP"]);exit;
			}
			$user_ip = $_POST['client_ip'];
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
		header("Content-Type: application/json");
		echo json_encode(["status"=>"success", "access-key"=>$res['inserted_id'] ]);exit;

	}else if( $api_slug == "assume_session_key" ){

		if( !isset($_POST['role_id']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Role id"]);exit;
		}else if( !preg_match( "/^[a-f0-9]{24}$/", $_POST['role_id'] ) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Role id incorrect"]);exit;
		}

		$res = $mongodb_con->find_one( $db_prefix . "_user_roles", [ "app_id"=>$app_id, '_id'=>$_POST['role_id']]);
		if( !$res['data'] ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Role Not Found"]);exit;
		}
		if( !isset($_POST['expire_type']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Expire type missing"]);exit;
		}
		if( $_POST['expire_type'] == "In" ){
			if( !isset($_POST['expire_minutes']) ){
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Expire minutes missing"]);exit;
			}
			$expire = time() + ( (int)$_POST['expire_minutes'] * 60 );
		}else if( $_POST['expire_type'] == "At" ){
			if( !isset($_POST['expire_at']) ){
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Expire Timestamp missing"]);exit;
			}
			$expire = strtotime( $_POST['expire_at'] );
		}
		if( isset($_POST['client_ip']) ){
			if( !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/", $_POST['client_ip']) ){
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Incorrect client IP"]);exit;
			}
			$user_ip = $_POST['client_ip'];
		}else{
			$user_ip = $_SERVER['REMOTE_ADDR'] . "/32";
		}
		if( !isset($_POST['max_hits']) || !is_numeric($_POST['max_hits']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Max hits missing"]);exit;
		}
		if( !isset($_POST['hits_per_minute']) || !is_numeric($_POST['hits_per_minute']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Hits per minute missing"]);exit;
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
		$key['maxhits'] = $_POST['max_hits'];
		$key['hitsmin'] = $_POST['hits_per_minute'];
		$key['updated']= date("Y-m-d H:i:s");

		$res = $mongodb_con->insert( $db_prefix . "_user_keys", $key);
		header("Content-Type: application/json");
		echo json_encode(["status"=>"success", "session-key"=>$res['inserted_id'] ]);exit;

	}else if( $api_slug == "user_auth" ||  $api_slug == "user_auth_captcha"  ){

		if( !isset($_POST['username']) || !isset($_POST['password']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Username or Password wrong"]);exit;
		}else if( !preg_match( "/^[a-z][a-z0-9\-]{2,50}$/", $_POST['username'] ) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Username or password wrong.."]);exit;
		}

		if( $api_slug == "user_auth_captcha" ){
			if( !preg_match( "/^[a-f0-9]{24}$/", $_POST['code'] ) ){
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"CaptchaCode incorrect"]);exit;
			}
			$cap_res = $mongodb_con->find_one( $db_prefix . "_captcha", ['_id'=>$_POST['code']] );
			if( !$cap_res['data'] ){
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Captcha mismatch..."]);exit;
			}
			if( $cap_res['data']['c'] != $_POST['captcha'] ){
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Captcha mismatch..."]);exit;
			}else{
				$mongodb_con->delete_one( $db_prefix . "_captcha", ['_id'=>$_POST['code']] );
			}
			//echo "captcha check pending";exit;
		}

		$user_res = $mongodb_con->find_one( $db_prefix . "_user_pool", ['username'=>$_POST['username']]);
		if( !$user_res['data'] ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Username or password wrong..."]);exit;
		}
		
		if( hash("whirlpool",$_POST['password']."123456") != $user_res['data']['password'] ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Username or password wrong...."]);exit;
		}
		if( isset($_POST['expire_minutes']) ){
			if( !is_numeric($_POST['expire_minutes']) ){
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Incorrect expire minutes"]);exit;
			}
			$expire = (int)$_POST['expire_minutes'];
		}else{
			$expire = 5;
		}
		$expire = time() + ($expire*60);
		if( isset($_POST['client_ip']) ){
			if( !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/") ){
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Incorrect client IP"]);exit;
			}
			$user_ip = $_POST['client_ip'];
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

			$res = $mongodb_con->update_one( $db_prefix . "_user_pool", ["_id"=>$user_res['data']['_id']], ['last_login'=>date("Y-m-d H:i:s")] );

			$mongodb_con->delete_one( $db_prefix . "_user_keys", ["_id"=>$_SERVER['HTTP_ACCESS_KEY']] );

			header("Content-Type: application/json");
			echo json_encode(["status"=>"success", "access-key"=>$new_key ]);exit;
		}else{
			http_response_code(500);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"DB insert error" ]);exit;
		}

	}else{
		http_response_code(404);header("Content-Type: application/json");
		echo json_encode(["status"=>"fail", "error"=>"Unknown Api Slug"]);exit;
	}

