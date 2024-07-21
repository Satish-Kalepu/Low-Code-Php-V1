<?php

require("cron_daemon_config.php");

require("task_worker_objects_functions.php");
$obpr = new objects_processor();

sleep(5); // for proper logging of timestamp

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