<?php

/*
Mongo DB connection 
require($_SERVER['DOCUMENT_ROOT']."admin/classes/class_mongodb.php");
$mongodb_con = new mongodb_connection( $config_mongo_host.":".$config_mongo_port );
$mongodb_con->debug = $config_mongo_debug;
$mongodb_con->connect( $config_mongo_db );
*/

if( !preg_match("/(localhost|[0-9\.]+)/", $_SERVER['HTTP_HOST']) ){
	if( !$config_global_apimaker['config_mongo_host'] || !$config_global_apimaker['config_mongo_port'] || !$config_global_apimaker['config_mongo_db'] || !$config_global_apimaker['config_mongo_username'] || !$config_global_apimaker['config_mongo_password'] ){
		header('HTTP/1.1 500 server error');
		echo "<html><head></head><body>Configuration incorrect: default database settings incorrect/missing</body></html>";
		exit;
	}
}

//print_pre($config_global_apimaker);

require( preg_replace("/[\/]{2,5}/", "/", $_SERVER['DOCUMENT_ROOT'] . "/" . $config_global_apimaker_path . "/classes/class_mongodb.php") );
if( isset($config_global_apimaker['config_mongo_username']) && $config_global_apimaker['config_mongo_username'] ){
	$mongodb_con = new mongodb_connection( 
		$config_global_apimaker['config_mongo_host'], 
		$config_global_apimaker['config_mongo_port'], 
		$config_global_apimaker['config_mongo_db'], 
		$config_global_apimaker['config_mongo_username'], 
		$config_global_apimaker['config_mongo_password'], 
		$config_global_apimaker['config_mongo_authSource'], 
		$config_global_apimaker['config_mongo_tls']
	);
}else{
	$mongodb_con = new mongodb_connection( $config_global_apimaker['config_mongo_host'], $config_global_apimaker['config_mongo_port'], $config_global_apimaker['config_mongo_db'] );
}

//echo $config_global_apimaker['config_mongo_prefix'] . "_settings";exit;

$db_prefix = $config_global_apimaker['config_mongo_prefix'];

$res = $mongodb_con->find($config_global_apimaker['config_mongo_prefix'] . "_settings");
//print_pre( $res );exit;
$config_settings = [];
$goto_install = false;
if( $res['status'] == "success" ){
	if( sizeof($res['data']) == 0 ){
		//http_response( 500, "Incorrect default settings" );
		$goto_install = true;
	}else{
		foreach( $res['data'] as $i=>$j ){
			$config_settings[ $j['_id'] ] = $j['value'];
		}
	}
}else{
	http_response(500,"Incorrect settings: " . $res['error'] );
}
$res = $mongodb_con->find($config_global_apimaker['config_mongo_prefix'] . "_vars");
$config_vars = [];
if( $res['status'] == "success" ){
	if( sizeof($res['data']) == 0 ){
		//http_response( 500, "Incorrect default settings" );
	}else{
		$config_vars = $res['data'];
	}
}else{
	http_response(500,"Incorrect prefix settings: " . $res['error'] );
}
// host, port, user, pass, authdb, tls 

if( $config_global_apimaker['config_use_redis'] ){
	$con_redis_security = new Redis();
	if( !$con_redis_security->connect($config_global_apimaker['config_redis_host'], $config_global_apimaker['config_redis_port'], 1) ){
		$con_redis_security = false;
		$rediserror = "Redis Connection ERROR - ".$_SERVER['REQUEST_URI']." .; Filename=".$_SERVER['PHP_SELF']."; Errornote:- security server is unavailable for $config_redis_security_host:$config_redis_security_port";
		error_log($rediserror);
		header('HTTP/1.1 500 server error');
		echo "<html><head></head><body>Redis connection fail</body></html>";
		exit;
	}else if( $con_redis_security->role()[0] <> 'master' ){
		error_log($_SERVER['HTTP_HOST'].' - Redis Role not Master :'.json_encode($con_redis_security));
		header('HTTP/1.1 500 server error');
		echo "<html><head></head><body>Redis is not a master node</body></html>";
		exit;
	}
}
