<?php

require("cron_daemon_config.php");

require("task_worker_graph_functions.php");
$obpr = new graph_processor();

if( sizeof($argv) <3 ){
	echo "Need Arguments: app_id and graph_id";exit;
}
$graph_id = $argv[2];
if( !$graph_id ){
	echo "Need Arguments: app_id and graph_id";exit;
}
$graph_things 	= 	$db_prefix . "_graph_" . $graph_id . "_things";
$graph_links 	= 	$db_prefix . "_graph_" . $graph_id . "_links";
$graph_mentions 	= 	$db_prefix . "_graph_" . $graph_id . "_mentions";
$graph_keywords = 	$db_prefix . "_graph_" . $graph_id . "_keywords";
$graph_queue	= $db_prefix . "_zd_queue_graph_". $graph_id;

sleep(5); // for proper logging of timestamp

$cron_daemon_thread_id = rand(100,999);

$restart_mode = false;
register_shutdown_function("shutdown");

function shutdown(){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $graph_id;
	global $cron_daemon_thread_id;
	global $restart_mode;
	global $total; global $success; global $fail; global $graph_things; global $graph_keywords; global $graph_queue;
	if( $restart_mode == false ){
		$res = $mongodb_con->update_one( $db_prefix . "_graph_dbs", [
			"app_id"=>$app_id, 
			"_id"=>$graph_id, 
		], [
			'$unset'=>["workers.".$cron_daemon_thread_id=>true, 'run'=>true]
		]);
	}else{
		$res = $mongodb_con->update_one( $db_prefix . "_graph_dbs", [
			"app_id"=>$app_id, 
			"_id"=>$graph_id
		], [
			'$unset'=>["workers.".$cron_daemon_thread_id=>true]
		]);
	}
	logit("Shutdown");
}
set_error_handler(function($errno, $errstr, $errfile, $errline){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;global $graph_id;
	global $cron_daemon_thread_id;global $graph_things; global $graph_keywords; global $graph_queue;
	echo "Error catched: ". $errno . ":" . $errstr. "\n";
	logit("error", ['error'=>['errno'=>$errno,'err'=>$errstr, 'errfile'=>$errfile, 'line'=> $errline]] );
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}, E_ALL & ~E_WARNING & ~E_NOTICE);

function logit($event, $e=[]){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;global $graph_id;
	global $cron_daemon_thread_id;
	global $sysip;
	global $graph_things; global $graph_keywords; global $graph_queue;

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
	$res = $mongodb_con->insert( $db_prefix . "_zlog_graph_". $graph_id, $d);
	if( $res['inserted_id'] ){
		return true;
	}else{
		return false;
	}
}

$res = $mongodb_con->update_one( $db_prefix . "_graph_dbs", [
	"app_id"=>$app_id,
	"_id"=>$graph_id,
], [
	"workers.".$cron_daemon_thread_id=>[
		"id"=>$cron_daemon_thread_id, 
		"time"=>time(),
		"sysip"=>$sysip,
	],
	"run"=>true,
]);

logit("Started");

//sleep(10);
//echo $cron_daemon_thread_id;
$last_check = time();

clearstatcache(true, "task_worker_graph.php");
clearstatcache(true, "task_worker_graph_functions.php");
$last_file_check = filemtime("task_worker_graph.php");
$last_file_check2 = filemtime("task_worker_graph_functions.php");

$last_task_check = 0;

$start = time();
$total = 0;
$success = 0;
$fail = 0;

while( 1 ){
	//logit("Finding item");

	if( time()-$last_check > 10 ){
		$app_res = $mongodb_con->find_one( $db_prefix . "_graph_dbs", [
			"app_id"=>$app_id,
			"_id"=>$graph_id,
		]);
		if( !isset($app_res['data']['run']) ){
			//shutdown();
			logit("Stop Detected");exit;
		}
		$last_check=time();
		$res = $mongodb_con->update_one( $db_prefix . "_graph_dbs", [
			"_id"=>$graph_id
		], [
			'lastrun'=>time(),
			'workers.'.$cron_daemon_thread_id=>[
				"sysip"=>$sysip,
				"time"=>time()
			]
		]);
	}

	clearstatcache(true, "task_worker_graph.php");
	clearstatcache(true, "task_worker_graph_functions.php");
	$lf = filemtime("task_worker_graph.php");
	$lf2 = filemtime("task_worker_graph_functions.php");
	if( $last_file_check != $lf || $last_file_check2 != $lf2 ){
		logit("sourceChange", ['o'=>$last_file_check, 'n'=>$lf]);
		exec('php task_worker_graph.php '. $app_id . ' ' . $graph_id . '> ' . $app_id .'_' . $graph_id .'.graph.log &', $eoutput);
		$restart_mode = true;
		exit;
	}

	$processed = false;

	$res = $mongodb_con->find_one_and_delete( $graph_queue, [
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
		$res = $mongodb_con->insert( $graph_queue, $task );

		$task_res = $obpr->process_task( $task );
		$task_res['task_id'] = $task['id'];
		logit( "result", $task_res );

		$res = $mongodb_con->delete_one( $graph_queue, ['_id'=>$new_task_id] );
	}


	if( $processed ){
		usleep(10000);//10ms
	}else{
		sleep( 3 );
	}

	//break;
}