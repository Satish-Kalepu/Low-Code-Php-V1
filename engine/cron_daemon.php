<?php

require("cron_daemon_config.php");


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
			'$unset'=>["settins.tasks.workers.".$cron_daemon_thread_id=>true, "settings.tasks.run"=>true],
			'$inc'=>['settings.tasks.processed'=>$total,'settings.tasks.success'=>$success,'settings.tasks.fail'=>$fail]
		]);
	}else{
		$res = $mongodb_con->update_one( $db_prefix . "_queues", [
			"app_id"=>$app_id, 
			"_id"=>$queue_id
		], [
			'$unset'=>["settings.tasks.workers.".$cron_daemon_thread_id=>true],
			'$inc'=>['settings.tasks.processed'=>$total,'settings.tasks.success'=>$success,'settings.tasks.fail'=>$fail]
		]);
	}
	logit("Shutdown");
}
set_error_handler(function($errno, $errstr, $errfile, $errline){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $cron_daemon_thread_id;
	logit("error", "error", ['error'=>['errno'=>$errno,'err'=>$errstr, 'errfile'=>$errfile, 'line'=> $errline]] );
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}, E_ALL & ~E_WARNING & ~E_NOTICE);

function logit($event, $message="", $e=[]){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $cron_daemon_thread_id;
	global $sysip;

	$d = [
		"date"=>date("Y-m-d H:i:s"),
		"tid"=>$cron_daemon_thread_id,
		"event"=>$event,
		"message"=>$message,
	];
	if( is_array($e) ){
		if(isset($e['_id'])){unset($e['_id']);}
		foreach( $e as $f=>$j ){
			$d[ $f ] = $j;
		}
	}elseif( is_string($e) && $e != "" ){
		$d[ 'data' ] = $e;
	}
	$res = $mongodb_con->insert( $db_prefix . "_zlog_tasks", $d);
	if( $res['inserted_id'] ){
		return true;
	}else{
		return false;
	}
}

$mongodb_con->update_one( $db_prefix . "_apps", [
	"_id"=>$app_id
], [
	"settings.tasks.workers.".$cron_daemon_thread_id=>[
		"id"=>$cron_daemon_thread_id, 
		"time"=>time(),
		"sysip"=>$sysip,
	]
]);

logit("Started");

//sleep(10);
//echo $cron_daemon_thread_id;
$last_check = time();
$app_res = $mongodb_con->find_one( $db_prefix . "_apps", [
	"_id"=>$app_id
], ['projection'=>['settings'=>1] ]);
if( !isset($app_res['data']['settings']['tasks']['run']) ){
	logit("Stop Detected");
	shutdown();
	exit;
}

clearstatcache(true, "cron_daemon.php");
$last_file_check = filemtime("cron_daemon.php");

$last_task_check = 0;

$start = time();
$total = 0;
$success = 0;
$fail = 0;

while( 1 ){
	//logit("Finding item");

	$app_res = $mongodb_con->find_one( $db_prefix . "_apps", [
		"_id"=>$app_id
	], ['projection'=>['settings'=>1]] );
	if( !isset($app_res['data']['settings']['tasks']['run']) ){
		//shutdown();
		logit("Stop Detected");exit;
	}
	$last_check=time();

	clearstatcache(true, "cron_daemon.php");
	$lf = filemtime("cron_daemon.php");
	if( $last_file_check != $lf ){
		logit("sourceChange", "Restarting", ['o'=>$last_file_check, 'n'=>$lf]);
		exec('php cron_daemon.php '. $app_id . ' > ' . $app_id .'.scheduler.log &', $eoutput);
		$restart_mode = true;
		exit;
	}

	if( isset($app_res['data']['settings']['tasks']['workers']) ){
		if( sizeof($app_res['data']['settings']['tasks']['workers']) > 1 ){
			logit("Daemon", "Found multiple workers", ['workers'=>$app_res['data']['settings']['tasks']['workers']]);
			$s = rand(5,30);
			logit("Daemon", "Sleeping:".$s );
			sleep($s);
			$app_res = $mongodb_con->find_one( $db_prefix . "_apps", [
				"_id"=>$app_id
			], ['projection'=>['settings'=>1]] );
			if( sizeof($app_res['data']['settings']['tasks']['workers']) > 1 ){
				logit("Daemon", "Found multiple workers", ['workers'=>$app_res['data']['settings']['tasks']['workers']]);
				$mongodb_con->update_one($db_prefix . "_apps", ["_id"=>$app_id],[
					'$unset'=>['settings.tasks.workers.'.$cron_daemon_thread_id=>true]
				]);
				logit("Daemon", "Stopped Unwanted");
				$restart_mode = true;
				exit;
			}
		}
	}

	$mongodb_con->update_one( $db_prefix . "_apps", [
		"_id"=>$app_id
	], [
		"settings.tasks.workers.".$cron_daemon_thread_id=>[
			"id"=>$cron_daemon_thread_id, 
			"time"=>time(),
			"sysip"=>$sysip,
		]
	]);

	if( isset($app_res['data']['settings']['objects']['enabled']) ){
		if( $app_res['data']['settings']['objects']['enabled'] ){
			$need_objects_start = false;
			if( !isset($app_res['data']['settings']['objects']['run']) ){
				logit("Objects", "Not running");
				$need_objects_start = true;
			}else if( (time()-(int)$app_res['data']['settings']['objects']['lastrun']) > 60 ){
				logit("Objects", "last run was " . (time()-(int)$app_res['data']['settings']['objects']['lastrun']) . " seconds ago");
				$need_objects_start = true;
			}
			if( isset($app_res['data']['settings']['objects']['workers']) ){
				if( sizeof($app_res['data']['settings']['objects']['workers']) > 3 ){
					$need_objects_start = false;
					logit("Objects", "TooMany Threads Found", ["count"=>sizeof($app_res['data']['settings']['objects']['workers']) ] );
					foreach( $app_res['data']['settings']['objects']['workers'] as $worker_id=>$wd ){
						logit("Objects", "Thread Delete", ["thread_id"=>$worker_id]);
						$mongodb_con->update_one( $db_prefix . "_apps", ["_id"=>$app_id], [
							'$unset'=>["settings.objects.workers.".$worker_id=>true]
						]);
					}
					$mongodb_con->update_one( $db_prefix . "_apps", ["_id"=>$app_id], [
						'$unset'=>["settings.objects.run"=>true]
					]);
					logit("Objects", "Stop Invoked");
				}
			}
			if( $need_objects_start ){
				logit("Objects", "Start");
				exec('php task_worker_objects.php '. $app_id . ' > ' . $app_id .'.objects.log &', $eoutput);
			}
		}
	}

	if( (time()-$last_task_check) > 60 ){
		$last_task_check = time();

		/* queue tasks checks */
		$queue_res = $mongodb_con->find( $db_prefix . "_queues", ['app_id'=>$app_id] );
		foreach( $queue_res['data'] as $i=>$queue ){

			$need_start = false;
			$current_active = 0;
			if( isset($queue['started']) && $queue['started'] == true ){
				if( isset($queue['workers']) ){
					foreach( $queue['workers'] as $worker_id=>$wd ){
						if( (time()-$wd['time']) > 60 ){
							logit("Internal Queue", "Found Inactive Thread", ["queue_id"=>$queue['_id'], "worker"=>$wd]);
							$mongodb_con->update_one( $db_prefix . "_queues", ['_id'=>$queue['_id']], [
								'$unset'=>['workers.'.$worker_id=>true],
							]);
							$need_start = true;
						}else{
							$current_active++;
						}
					}
				}
				logit("Internal Queue", "Status", ["queue_id"=>$queue['_id'], "active_tasks"=>$current_active, "required"=>$queue['con']]);
				if( $need_start || ($queue['con']-$current_active) > 0 ){
					logit("Internal Queue", "Insufficient Threads", ["queue_id"=>$queue['_id'], "active_tasks"=>$current_active, "required"=>$queue['con']]);
					$mongodb_con->update_one( $db_prefix . "_queues", ['_id'=>$queue['_id']], [
						'run'=>true,
					]);
					for($i=$current_active+1;$i<=$queue['con'];$i++){
						exec('php task_worker.php '. $app_id . ' '. $queue['_id'] . ' >> ' . $app_id . '_'. $queue['_id'] . '.task.log &', $eoutput);
						logit("Internal Queue", "Start Job");
					}
				}else if( ($current_active>$queue['con']) ){
					logit("Internal Queue", "Too many jobs Restarting", ["queue_id"=>$queue['_id'], "active_tasks"=>$current_active, "required"=>$queue['con']]);
					$mongodb_con->update_one( $db_prefix . "_queues", ['_id'=>$queue['_id']], [
						'$unset'=>['run'=>true],
					]);
				}
			}else{
				if( isset($queue['workers']) && isset($queue['run']) ){
					logit("Internal Queue", "Stopping");
					foreach( $queue['workers'] as $worker_id=>$wd ){
						if( (time()-$wd['time']) > 60 ){
							$mongodb_con->update_one( $db_prefix . "_queues", ['_id'=>$queue['_id']], [
								'$unset'=>['workers.'.$worker_id=>true],
							]);
						}
					}
					$mongodb_con->update_one( $db_prefix . "_queues", ['_id'=>$queue['_id']], [
						'$unset'=>['run'=>true],
					]);
				}
			}
		}

	}

	sleep(10);
	//break;
}