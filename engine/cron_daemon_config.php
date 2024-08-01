<?php

ini_set( "display_startup_errors", "On" );
ini_set( "display_errors", "On" );
ini_set( "html_errors", "Off" );
ini_set( "log_errors", "On" );
ini_set( "short_open_tag", "1" );
ini_set( "error_reporting", "373" );


if( !isset($argv) ){
	echo "Need Arguments";exit;
}else if( !is_array($argv) ){
	echo "Need Arguments";exit;
}else if( sizeof($argv) <2 ){
	echo "Need Arguments: app_id";exit;
}
$app_id = $argv[1];
//echo $app_id;
if( !$app_id){
	echo "Need Arguments: app_id";exit;
}

$sysip = gethostbyname( gethostname() );

/*
php task_worker.php app_id queue_id
config_deamon_run_mode = all/single // same deamon for all apps,  single app
config_deamon_app_id = "" // null for all apps, required for single app
*/

require_once("vendor/autoload.php");

if( file_exists("../../../config_engine.php") ){
	require("../../../config_engine.php");
}else if( file_exists("../../config_engine.php") ){
	require("../../config_engine.php");
}else if( file_exists("../config_engine.php") ){
	require("../config_engine.php");
}else{
	echo "config_engine missing";exit;
}

if( $execution_mode == "local_folder" ){

	$config_paths = [
		"./config_global_engine.php",
		"../config_global_engine.php",
		"../../config_global_engine.php",
		"/var/tmp/config_global_engine.php",
		sys_get_temp_dir()  . "/config_global_engine.php"
	];
	foreach( $config_paths as $j ){
		if( file_exists($j) ){
			require($j);
			break;
		}
	}

	$engine_cache_path = sys_get_temp_dir()  . "/apimaker/engine_" . $config_global_apimaker_engine["config_engine_app_id"] . ".php";
	if( !file_exists($engine_cache_path) ){
		echo "engine is not initialized..x " . $engine_cache_path;exit;
	}
	require_once($engine_cache_path);

	if( $app_id != $config_global_apimaker_engine["config_engine_app_id"] ){
		echo "engine app_id and argument app_id not matching\n";
		echo $app_id . ": " . $config_global_apimaker_engine["config_engine_app_id"];
		exit;
	}

	$engine_cache_path = sys_get_temp_dir()  . "/apimaker/engine_" . $config_global_apimaker_engine["config_engine_app_id"] . ".php";
	if( file_exists($engine_cache_path) ){
		$cache_refresh = false;
		require_once($engine_cache_path);
		if( !$config_global_engine ){
			$cache_refresh = true;
		}
		if( filemtime($engine_cache_path) < time()-(int)$config_global_apimaker_engine["config_engine_cache_interval"] ){
			$cache_refresh = true;
		}
		$k = $config_global_apimaker_engine["config_engine_cache_refresh_action_query_string"];
		if( $k ){
			if( $_GET[ array_keys($k)[0] ] ){
				if( $_GET[ array_keys($k)[0] ] == $k[ array_keys($k)[0] ] ){
					$cache_refresh = "yes";
				}
			}
		}
	}else{
		echo "Error: Engine is not initialized";exit;
	}
}else{
	if( file_exists("../config_global_engine.php") ){
		require( "../config_global_engine.php" );
		if( !isset($config_global_engine) ){
			http_response_code(500); echo "config file config_global_engine loaded but config missing";exit;
		}
	}else{
		http_response_code(500); echo "config file config_global_engine missing";exit;
	}
	//echo "Scenario Pending";exit;
}

if( $config_global_engine['timezone'] ){
	date_default_timezone_set($config_global_engine['timezone']);
}

/* Mongo DB connection */
require("class_mongodb.php");

if( $config_global_engine['config_mongo_username'] ){
	$mongodb_con = new mongodb_connection( 
		$config_global_engine['config_mongo_host'], 
		$config_global_engine['config_mongo_port'], 
		$config_global_engine['config_mongo_db'], 
		$config_global_engine['config_mongo_username'], 
		$config_global_engine['config_mongo_password'], 
		$config_global_engine['config_mongo_authSource'], 
		$config_global_engine['config_mongo_tls']
	);
}else{
	$mongodb_con = new mongodb_connection( 
		$config_global_engine['config_mongo_host'], 
		$config_global_engine['config_mongo_port'], 
		$config_global_engine['config_mongo_db'] 
	);
}

$db_prefix = $config_global_engine[ "config_mongo_prefix" ];

$use_encrypted=true;
$request_log_id= "";
if( $use_encrypted ){
	require_once("class_engine_encrypted.php");
}else if( file_exists("class_engine.php") ){
	require_once("class_engine.php");
}else{
	logit("error", ["error"=>"Engine file missing"] );
	exit;
}
