<?php

$is_hub_login = false;
$hub_login_email = "";
$hub_session_key = "";

$reshub = $mongodb_con->find_one($db_prefix . "_settings", ["_id"=>"hub"]);
if( $reshub['data'] ){
	$hub = $reshub['data'];
	if( isset($hub['session_key']) ){
		if( time()-$hub['time'] < (86400*5) ){
			$is_hub_login = true;
			$hub_login_email = $hub['email'];
			$hub_session_key = $hub['session_key'];
		}
	}
}

//print_r( $app['hub'] );exit;
if( $_POST['action'] == "exports_hub_login" ){

	if( !isset( $config_global_apimaker['config_hub_api_url'] ) ){
		json_response("fail", "Hub is not configured");
	}
	if( !isset( $config_global_apimaker['config_hub_access_key'] ) ){
		json_response("fail", "Hub is not configured.");
	}

	$email = base64_decode($_POST['login']['email']);
	$resp = curl_post($config_global_apimaker['config_hub_api_url'] . "login", [
		"email" => $email,
		"password" => base64_decode($_POST['login']['password']),
	], [
		"Content-Type: application/json",
		"Access-Key: " . $config_global_apimaker['config_hub_access_key']
	]);

	if( $resp['status'] == 200 ){
		$d = json_decode($resp['body'], true);
		if( !$d ){
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
		if( isset($d['status']) ){
			if( $d['status'] == "success" ){
				if( isset($d['session_access_key']) ){

					$res = $mongodb_con->update_one($db_prefix . "_settings", [
						"_id"=>"hub",
					], [
						"session_key"=>$d['session_access_key'],
						"time"=>time(),
						"email"=>$email
					],['upsert'=>true]);

					json_response(['status'=>"success"]);

				}else{
					json_response("fail", "Incorrect response from Hub: " . $resp['body']);
				}
			}else{
				json_response("fail", "Login Fail: " . $d['error']);
			}
		}else{
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
	}else{
		json_response("fail", "Invalid response: " . $resp['status'] . ": " . $resp['error']);
	}

}

if( $_POST['action'] == "exports_hub_logout" ){

	if( !isset( $config_global_apimaker['config_hub_api_url'] ) ){
		json_response("fail", "Hub is not configured");
	}
	if( !isset( $config_global_apimaker['config_hub_access_key'] ) ){
		json_response("fail", "Hub is not configured.");
	}

	$resp = curl_post($config_global_apimaker['config_hub_api_url'] . "logout", [
	], [
		"Content-Type: application/json",
		"Access-Key: " . $config_global_apimaker['config_hub_access_key'],
		"Session-Key: " . $hub_session_key
	]);

	$res = $mongodb_con->delete_one( $db_prefix . "_settings", [
		"_id"=>"hub",
	]);

	json_response("success", "OK");
}
