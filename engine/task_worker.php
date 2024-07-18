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

if( !isset($argv) ){
	echo "Need Arguments";exit;
}else if( !is_array($argv) ){
	echo "Need Arguments";exit;
}else if( sizeof($argv) <3 ){
	echo "Need Arguments: app_id and queue_id";exit;
}
$app_id = $argv[1];
//echo $app_id;
$queue_id = $argv[2];
if( !$app_id || !$queue_id ){
	echo "Need Arguments: app_id and queue_id";exit;
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
	global $queue_id;
	global $cron_daemon_thread_id;
	global $restart_mode;
	global $total; global $success; global $fail;
	if( $restart_mode == false ){
		$res = $mongodb_con->update_one( $db_prefix . "_queues", [
			"app_id"=>$app_id, 
			"_id"=>$queue_id
		], [
			'$unset'=>["workers.".$cron_daemon_thread_id=>true, "run"=>true],
			'$inc'=>['processed'=>$total,'success'=>$success,'fail'=>$fail]
		]);
	}else{
		$res = $mongodb_con->update_one( $db_prefix . "_queues", [
			"app_id"=>$app_id, 
			"_id"=>$queue_id
		], [
			'$unset'=>["workers.".$cron_daemon_thread_id=>true],
			'$inc'=>['processed'=>$total,'success'=>$success,'fail'=>$fail]
		]);
	}
	logit("Shutdown");
}



set_error_handler(function($errno, $errstr, $errfile, $errline ){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $queue_id;
	global $cron_daemon_thread_id;
	logit("error", ['error'=>['errno'=>$errno,'err'=>$errstr, 'errfile'=>$errfile, 'line'=> $errline]] );
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}, E_ALL & ~E_WARNING & ~E_NOTICE);

function logit($event, $e=[]){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $queue_id;
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
	$res = $mongodb_con->insert( $db_prefix . "_zlog_queue_". $queue_id, $d);
	if( $res['inserted_id'] ){
		return true;
	}else{
		return false;
	}
}

$last_check = time();
$queue_res = $mongodb_con->find_one( $db_prefix . "_queues", [
	"app_id"=>$app_id, "_id"=>$queue_id
]);
if( !isset($queue_res['data']['started']) ){
	logit("Stop Detected");
	shutdown();
	exit;
}

$mongodb_con->update_one( $db_prefix . "_queues", [
	"app_id"=>$app_id, 
	"_id"=>$queue_id
], [
	"workers.".$cron_daemon_thread_id=>[
		"id"=>$cron_daemon_thread_id, 
		"time"=>time(),
		"sysip"=>$sysip,
	],
	"run"=>true,
]);
logit("Started");

$use_encrypted=false;
$request_log_id= "";
if( $use_encrypted ){
	require_once("class_engine_encrypted.php");
}else if( file_exists("class_engine.php") ){
	require_once("class_engine.php");
}else{
	logit("error", ["error"=>"Engine file missing"] );
	exit;
}

//sleep(10);
//echo $cron_daemon_thread_id;


$last_fn_check = time();
$fres = $mongodb_con->find_one( $db_prefix . "_functions_versions", [
	'_id'=>$queue_res['data']['fn_vid']
]);
if( !isset($fres['data']) ){
	logit("error", ["error"=>"function not found"]);
	exit;
}
if( !isset($fres['data']['engine']) ){
	logit( "error", ["error"=>"Engine spec missing" ]);exit;
}

clearstatcache(true, "task_worker.php");
$last_file_check = filemtime("task_worker.php");

$start = time();
$total = 0;
$success = 0;
$fail = 0;

while( 1 ){
	//logit("Finding item");

	if( ( time() - $last_check ) > 10 ){
		$queue_res = $mongodb_con->find_one( $db_prefix . "_queues", [
			"app_id"=>$app_id, "_id"=>$queue_id
		]);
		if( !isset($queue_res['data']['started']) ){
			//shutdown();
			logit("Stop Detected");
			exit;
		}
		if( !isset($queue_res['data']['run']) ){
			//shutdown();
			logit("Temporary Stop Detected");
			exit;
		}
		$last_check=time();
	}

	if( ( time() - $last_fn_check ) > 60 ){
		$fres = $mongodb_con->find_one( $db_prefix . "_functions_versions", [
			'_id'=>$queue_res['data']['fn_vid']
		]);
		if( !isset($fres['data']) ){
			logit("error", ["error"=>"function not found"]);
			exit;
		}
		if( !isset($fres['data']['engine']) ){
			logit( "error", ["error"=>"Engine spec missing" ]);exit;
		}

		$last_fn_check=time();
	}

	$res = $mongodb_con->find_one( $db_prefix . "_queues", [
		"app_id"=>$app_id, "_id"=>$queue_id
	]);

	$processed = false;
	$res = $mongodb_con->find_one_and_delete( $db_prefix . "_zd_queue_" . $queue_id, [
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
		$res = $mongodb_con->insert( $db_prefix . "_zd_queue_" . $queue_id, $task );

		//print_pre( $test );exit;
		$api_engine = new api_engine();
		if( !$api_engine ){
			logit( "error", ["error"=>"Error initializing api engine!" ]);
		}

		$result = $api_engine->execute( $fres['data'], $task['data'], ["request_log_id"=>$request_log_id] );
		logit("executed", ['task_id'=>$task['id'], 'status'=>$result['statusCode'], 'body'=>$result['body'] ]);
		if( $result['statusCode'] == 200 ){
			$success++;
		}else{
			$fail++;
		}
		$res = $mongodb_con->delete_one( $db_prefix . "_zd_queue_" . $queue_id, ['_id'=>$new_task_id] );
		unset($api_engine);
	}

	if( (time()-$start) > 20 ){
		logit( "status", ["stats"=>["total"=>$total, "success"=>$success, "fail"=>$fail]] );
		$res = $mongodb_con->update_one( $db_prefix . "_queues", ['_id'=>$queue_id], [
			'$inc'=>['processed'=>$total,'success'=>$success,'fail'=>$fail],
			'$set'=>[
				"workers.".$cron_daemon_thread_id=>[
					"id"=>$cron_daemon_thread_id, 
					"time"=>time(),
					"sysip"=>$sysip,
				]
			]
		]);
		$total = 0;$success = 0;$fail = 0;
		$start = time();
	}

	clearstatcache(true, "task_worker.php");
	$lf = filemtime("task_worker.php");

	if( $last_file_check != $lf ){
		logit("sourceChange", ['o'=>$last_file_check, 'n'=>$lf]);
		logit("Restarting");
		exec('php task_worker.php '. $app_id . ' '. $queue_id . ' > ' . $app_id . '_'. $queue_id . '.cron.log &', $eoutput);
		$restart_mode = true;
		exit;
	}

	if( $processed ){
		usleep(10000);//10ms
	}else{
		sleep( 3 );
	}
	//break;
}