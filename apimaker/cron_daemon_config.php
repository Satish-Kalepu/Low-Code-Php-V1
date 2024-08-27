<?php

ini_set( "display_startup_errors", "On" );
ini_set( "display_errors", "On" );
ini_set( "html_errors", "Off" );
ini_set( "log_errors", "On" );
ini_set( "short_open_tag", "1" );
ini_set( "error_reporting", "373" );

$sysip = gethostbyname( gethostname() );

/*
php task_worker.php app_id queue_id
config_deamon_run_mode = all/single // same deamon for all apps,  single app
config_deamon_app_id = "" // null for all apps, required for single app
*/

require_once("vendor/autoload.php");

if( file_exists("../../../config_global_apimaker.php") ){
	require("../../../config_global_apimaker.php");
}else if( file_exists("../../config_global_apimaker.php") ){
	require("../../config_global_apimaker.php");
}else if( file_exists("../config_global_apimaker.php") ){
	require("../config_global_apimaker.php");
}else{
	echo "config_global_apimaker missing";exit;
}

if( !isset($config_global_apimaker["timezone"]) ){
	echo "config_global_apimaker not found";
	exit;
}

date_default_timezone_set( $config_global_apimaker["timezone"] );

/* Mongo DB connection */
require("classes/class_mongodb.php");

if( $config_global_apimaker['config_mongo_username'] ){
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
	$mongodb_con = new mongodb_connection( 
		$config_global_apimaker['config_mongo_host'], 
		$config_global_apimaker['config_mongo_port'], 
		$config_global_apimaker['config_mongo_db'] 
	);
}

$db_prefix = $config_global_apimaker[ "config_mongo_prefix" ];

