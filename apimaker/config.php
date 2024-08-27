<?php


if( !isset($config_global_apimaker) ){
	http_response_code(500);
	echo "Incorrect setup!";exit;
}

$config_session_name = "phpengnemaker";

ini_set("display_startup_errors", "On" );
ini_set("display_errors", "On" );
ini_set("html_errors", "Off" );
ini_set("log_errors", "On" );
ini_set("date.timezone", $config_global_apimaker['timezone'] );
ini_set("short_open_tag", "1");
ini_set("post_max_size", "20M");
ini_set("upload_max_filesize", "50M");
ini_set("memory_limit", "512M");
ini_set("max_execution_time", "20");
ini_set("max_input_time", "20");
ini_set("error_reporting", 373);
ini_set('session.name', $config_session_name );
ini_set("session.cookie_lifetime", $config_global_apimaker['config_session_timeout'] );
ini_set("session.gc_maxlifetime", $config_global_apimaker['config_session_timeout'] );
ini_set("session.cookie_path", "/" );
if( $config_global_apimaker['config_use_https_only'] ){
	ini_set("session.cookie_httponly", "1" );
	ini_set("session.cookie_secure", "On" );
	ini_set("Strict-Transport-Security", "max-age=86400");
}

if( isset($config_global_apimaker['config_session_redis']) ){
	if( $config_global_apimaker['config_session_redis'] === true ){
		ini_set('session.save_handler', "redis" );
		ini_set('session.save_path', "tcp://".$config_global_apimaker['config_redis_host'].":".$config_global_apimaker['config_redis_port'] );
	}
}
if( $config_global_apimaker['config_apimaker_domain'] != $_SERVER['HTTP_HOST'] ){
	http_response_code("403");
	echo "Incorrect Domain Settings<BR>\n";
	echo $config_global_apimaker['config_apimaker_domain'] . ": " .  $_SERVER['HTTP_HOST'];exit;
}

header( "Access-Control-Allow-Origin: *" );
header( "Access-Control-Allow-Methods: *" );
header( "Access-Control-Allow-Headers: Content-Type" );
header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );

if( $_SERVER['HTTP_USER_AGENT'] == "" ){
	header("http/1.1 400 Bad Request" );
	exit;
}

if( $_SERVER['HTTP_X_FORWARDED_FOR'] ){
    $d = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR'] );
    $_SERVER['REMOTE_ADDR'] = trim($d[0]);
    $_SERVER['HTTP_X_REAL_IP'] = trim($d[0]);
}else{
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP']?$_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR'];
}

if( $_SERVER['REQUEST_METHOD']=="POST" && preg_match("/json/i", $_SERVER['CONTENT_TYPE']) ){
	$input_data = file_get_contents('php://input');
	$_POST = json_decode($input_data, true);
	if( json_last_error() ){
		error_log("Error parsing json post event: " . $_SERVER['REQUEST_URI'] . ": " . $input_data );
		header("http/1.1 400 Request ERROR");
		echo "JSON Parse Error: " . json_last_error_msg();
		exit;
	}
}

