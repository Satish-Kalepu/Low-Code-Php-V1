<?php

if( !isset($_SERVER['HTTP_APPID']) || !isset($_SERVER['HTTP_APPKEY']) || !isset($_SERVER['HTTP_DOMAIN']) ){
	json_response([
		"status"=>"fail",
		"error"=>"AppID or Key Missing"
	]);
}
$app_id = $_SERVER['HTTP_APPID'];
$app_key = $_SERVER['HTTP_APPKEY'];
$app_domain = $_SERVER['HTTP_DOMAIN'];

if( !$app_id || !$app_key || !$app_domain ){
	json_response([
		"status"=>"fail",
		"error"=>"AppID or Key Missing"
	]);
}

$res = $mongodb_con->find_one( $config_global_apimaker[ "config_mongo_prefix" ] . "_apps", ['_id'=>$app_id] );
if( !$res['data'] ){
	json_response([
		"status"=>"fail",
		"error"=>"App not found"
	]);
}

$app = $res['data'];
if( !isset($app['settings']) ){
	json_response([
		"status"=>"fail",
		"error"=>"Settings not initialized"
	]);
}

if( !isset($app['settings']['host']) ){
	json_response([
		"status"=>"fail",
		"error"=>"Custom hosting is not enabled"
	]);
}
if( $app['settings']['host'] === false ){
	json_response([
		"status"=>"fail",
		"error"=>"Custom hosting is not enabled"
	]);
}
if( !isset($app['settings']['domains']) || !is_array($app['settings']['domains']) || !isset($app['settings']['keys']) || !is_array($app['settings']['keys']) ){
	json_response([
		"status"=>"fail",
		"error"=>"Custom hosting settings error"
	]);
}
$domains = $app['settings']['domains'];
$keys = $app['settings']['keys'];

$f = false;
if( is_array($domains) ){ foreach( $domains as $i=>$j ){
	if( $j['domain'] == $app_domain ){$f = true;}
}}
if( !$f ){
	json_response([
		"status"=>"fail",
		"error"=>"Domain is not allowed " . $app_domain
	]);
}

$f = false;
if( is_array( $keys ) ){
foreach( $keys as $i=>$j ){
	if( $j['key'] == $app_key ){
		$f = true;
		$key = $j;
	}
}
}
if( !$f ){
	json_response([
		"status"=>"fail",
		"error"=>"Key not found"
	]);
}
$allow = false;

$ip1 = $_SERVER['REMOTE_ADDR']."/32";
$x = explode(".",$_SERVER['REMOTE_ADDR']);
$ip2 = $x[0] . "." . $x[1] . "." . $x[2] . ".0/24";
$ip3 = $x[0] . "." . $x[1] . ".0.0/16";
$ip4 = $x[0] . ".0.0.0/8";
$ip5 = "0.0.0.0/0";

if( isset($key) ){
	if( isset($key['ips_allowed']) ){
		foreach( $key['ips_allowed'] as $ipi=>$ipv ){
			if( $ipv['ip'] == "*" || $ipv['ip'] == $ip2 || $ipv['ip'] == $ip3 || $ipv['ip'] == $ip4 || $ipv['ip'] == $ip5 ){
				$allow = ($ipv['action']=="Allow"?true:false);
			}
		}
	}
}

if( !$allow ){
	json_response([
		"status"=>"fail",
		"error"=>"IP is not allowed"
	]);
}

unset( $config_global_apimaker['config_engine_keys'] );
json_response([
	"status" => "success",
	"configs" => $config_global_apimaker
]);
