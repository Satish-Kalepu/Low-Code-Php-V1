<?php

if( $_POST['action'] == "login" ){

	if( !$_POST['token'] ){
		json_response([
			"status"=>"fail",
			"error"=>"Token not found"
		]);
	}
	$token_status = validate_token(  "login", $_POST['token'] );

	if( $token_status != "OK" ){
		json_response([
			"status"=>"TokenError",
			"error"=>$token_status
		]);
	}

	if( $_POST['captcha'] == $config_global_apimaker['config_captcha_bypass'] ){
	}else{
		if( $_POST['captcha'] != $_SESSION['login_captcha'] || $_POST['captcha_code'] != $_SESSION['login_code'] ){
			event_log( "system", "login_fail", [
				'user'=>$usr,
				"error"=>"Incorrect Code",
			]);
			json_response([
				"status"=>"fail",
				"error"=>"Incorrect Code",
				//"session"=>$_SESSION
			]);
		}
	}

	$usr = $_POST['user'];
	$pass = $_POST['pass'];
	$pass = substr($pass, 10, 160);
	$pass = str_replace("<?=session_id() ?>", "", $pass);
	try{
		$pass = base64_decode($pass);
		//echo pass_hash($pass);exit;
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_users", [
			"username"=>$usr,
			"password"=>pass_hash($pass)
		]);
		if( $res['data'] ){
			$_SESSION['apimaker_login_ok'] = true;
			$_SESSION['apimaker_login_id'] = $res['data']['_id'];
			session_regenerate_id();
			$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_tmp", [
				"_id"=>"login_session"
			],[
				"_id"=>"login_session",
				"value"=>session_id()
			],['upsert'=>true] );
			event_log( "system", "login_success", [
				'user'=>$usr,
				"user_id"=>$res['data']['_id']
			]);
			json_response([
				"status"=>"success",
			]);
		}else{
			event_log( "system", "login_fail", [
				'user'=>$usr,
				"error"=>"Username or Password Incorrect"
			]);
			json_response([
				"status"=>"fail",
				"error"=>"Username or Password Incorrect"
			]);
		}
	}catch(Exception $ex){
		event_log( "system", "login_fail", [
			'user'=>$usr,
			"error"=>$ex->getMessage()
		]);
		json_response([
			"status"=>"fail",
			"error"=>$ex->getMessage()
		]);
	}
	exit;
}