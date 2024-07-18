<?php

ini_set( "display_startup_errors", "On" );
ini_set( "display_errors", "On" );
ini_set( "html_errors", "Off" );
ini_set( "log_errors", "On" );
ini_set( "short_open_tag", "1" );
ini_set( "error_reporting", "373" );

$sysip = gethostbyname( gethostname() );

/*
php task_worker_objects.php app_id 
config_deamon_run_mode = all/single // same deamon for all apps,  single app
config_deamon_app_id = "" // null for all apps, required for single app
*/

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

require("task_worker_objects_functions.php");
$obpr = new objects_processor();

if( $execution_mode == "local_folder" ){

	$config_paths = [
		"./config_global_engine.php",
		"../config_global_engine.php",
		"../../config_global_engine.php",
		"/var/tmp/config_global_engine.php",
	];
	foreach( $config_paths as $j ){
		if( file_exists($j) ){
			require($j);
			break;
		}
	}

	$engine_cache_path = "/tmp/apimaker/engine_" . $config_global_apimaker_engine["config_engine_app_id"] . ".php";
	if( !file_exists($engine_cache_path) ){
		echo "engine is not initialized..x " . $engine_cache_path;exit;
	}
	require_once($engine_cache_path);

	if( $app_id != $config_global_apimaker_engine["config_engine_app_id"] ){
		echo "engine app_id and argument app_id not matching\n";
		echo $app_id . ": " . $config_global_apimaker_engine["config_engine_app_id"];
		exit;
	}

	$engine_cache_path = "/tmp/apimaker/engine_" . $config_global_apimaker_engine["config_engine_app_id"] . ".php";
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
	echo "Scenario Pending";exit;
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

sleep(5); // for proper logging of timestamp

$db_prefix = $config_global_engine[ "config_mongo_prefix" ];

$cron_daemon_thread_id = rand(100,999);

$restart_mode = false;
register_shutdown_function("shutdown");

function shutdown(){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $cron_daemon_thread_id;
	global $restart_mode;
	global $total; global $success; global $fail;
	if( $restart_mode == false ){
		$res = $mongodb_con->update_one( $db_prefix . "_apps", [
			"_id"=>$app_id, 
		], [
			'$unset'=>["settins.objects.workers.".$cron_daemon_thread_id=>true, "settings.objects.run"=>true]
		]);
	}else{
		$res = $mongodb_con->update_one( $db_prefix . "_queues", [
			"app_id"=>$app_id, 
			"_id"=>$queue_id
		], [
			'$unset'=>["settings.objects.workers.".$cron_daemon_thread_id=>true]
		]);
	}
	logit("Shutdown");
}
set_error_handler(function($errno, $errstr, $errfile, $errline){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $cron_daemon_thread_id;
	echo "Error catched: ". $errno . ":" . $errstr. "\n";
	logit("error", ['error'=>['errno'=>$errno,'err'=>$errstr, 'errfile'=>$errfile, 'line'=> $errline]] );
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}, E_ALL & ~E_WARNING & ~E_NOTICE);

function logit($event, $e=[]){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $cron_daemon_thread_id;
	global $sysip;

	$d = [
		"date"=>date("Y-m-d H:i:s"),
		"tid"=>$cron_daemon_thread_id,
		"event"=>$event,
	];
	if( is_array($e) ){
		if(isset($e['_id'])){unset($e['_id']);}
		foreach( $e as $f=>$j ){
			$d[ $f ] = $j;
		}
	}elseif( is_string($e) && $e != "" ){
		$d[ 'data' ] = $e;
	}
	$res = $mongodb_con->insert( $db_prefix . "_zlog_objects", $d);
	if( $res['inserted_id'] ){
		return true;
	}else{
		return false;
	}
}

$mongodb_con->update_one( $db_prefix . "_apps", [
	"_id"=>$app_id
], [
	"settings.objects.workers.".$cron_daemon_thread_id=>[
		"id"=>$cron_daemon_thread_id, 
		"time"=>time(),
		"sysip"=>$sysip,
	],
	"settings.objects.run"=>true,
]);

logit("Started");

//sleep(10);
//echo $cron_daemon_thread_id;
$last_check = time();

clearstatcache(true, "task_worker_objects.php");
clearstatcache(true, "task_worker_objects_functions.php");
$last_file_check = filemtime("task_worker_objects.php");
$last_file_check2 = filemtime("task_worker_objects_functions.php");

$last_task_check = 0;

$start = time();
$total = 0;
$success = 0;
$fail = 0;

while( 1 ){
	//logit("Finding item");

	if( time()-$last_check > 10 ){
		$app_res = $mongodb_con->find_one( $db_prefix . "_apps", [
			"_id"=>$app_id
		], ['projection'=>['settings'=>1]] );
		if( !isset($app_res['data']['settings']['objects']['run']) ){
			//shutdown();
			logit("Stop Detected");exit;
		}
		$last_check=time();
		$res = $mongodb_con->update_one( $db_prefix . "_apps", [
			"_id"=>$app_id
		], [
			'settings.objects.lastrun'=>time(),
			'settings.objects.workers.'.$cron_daemon_thread_id=>[
				"sysip"=>$sysip,
				"time"=>time()
			]
		]);
	}

	clearstatcache(true, "task_worker_objects.php");
	clearstatcache(true, "task_worker_objects_functions.php");
	$lf = filemtime("task_worker_objects.php");
	$lf2 = filemtime("task_worker_objects_functions.php");
	if( $last_file_check != $lf || $last_file_check2 != $lf2 ){
		logit("sourceChange", ['o'=>$last_file_check, 'n'=>$lf]);
		exec('php task_worker_objects.php '. $app_id . ' > ' . $app_id .'.scheduler.log &', $eoutput);
		$restart_mode = true;
		exit;
	}

	$processed = false;


	$res = $mongodb_con->find_one_and_delete( $db_prefix . "_zd_queue_objects", [
		'_id'=>['$lte'=>date("YmdHis:999:9")]
	], ['sort'=>['_id'=>1]] );
	//print_r( $res );
	if( $res['status'] == "success" ){
		$total++;
		$processed = true;
		$task = $res['data'];
		logit( "task", ['task_id'=>$task['id'], "data"=>$task['data']] );
		$current_task_id = $task['id'];
		$x = explode(":",$task['_id']);
		array_splice($x,0,1);
		$new_task_id = date("YmdHis",time()+10).":".implode(":",$x);
		$task['_id'] = $new_task_id;
		$task['retry']=$task['retry']?$task['retry']+1:1;
		$res = $mongodb_con->insert( $db_prefix . "_zd_queue_objects", $task );

		$task_res = $obpr->process_task( $task );
		logit( "result", $task_res );

		$res = $mongodb_con->delete_one( $db_prefix . "_zd_queue_objects", ['_id'=>$new_task_id] );
		unset($api_engine);
	}


	if( $processed ){
		usleep(10000);//10ms
	}else{
		sleep( 3 );
	}

	//break;
}