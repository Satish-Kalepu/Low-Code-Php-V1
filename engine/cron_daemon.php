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
		$res = $mongodb_con->update_one( $db_prefix . "_apps", [
			"_id"=>$app_id
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
	logit("error", "error", [ 'error'=>['errno'=>$errno,'err'=>$errstr, 'errfile'=>$errfile, 'line'=> $errline]] );
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
	$res = $mongodb_con->insert( $db_prefix . "_zlog_tasks_" . $app_id, $d);
	if( $res['inserted_id'] ){
		return true;
	}else{
		return false;
	}
}

$res = $mongodb_con->update_one( $db_prefix . "_apps", [
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
$last_cron_check = 0;

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
			foreach( $app_res['data']['settings']['tasks']['workers'] as $worker_id=>$wd ){
				if( (time()-$wd['time']) > 30 ){
					logit("Daemon", "Found Inactive Thread", ["worker"=>$wd]);
					$mongodb_con->update_one( $db_prefix . "_apps", ['_id'=>$app_id], [
						'$unset'=>['settings.tasks.workers.'.$worker_id=>true],
					]);
					unset($app_res['data']['settings']['tasks']['workers'][ $worker_id ]);
					$need_start = true;
				}else{
					$current_active++;
				}
			}
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

	$graph_res = $mongodb_con->find( $db_prefix . "_graph_dbs", ['app_id'=>$app_id] );
	foreach( $graph_res['data'] as $i=>$graph ){
		$need_objects_start = false;
		if( !isset($graph['run']) ){
			logit("Graph: " . $graph['name'], "Not running", ['graph'=>$graph['_id']]);
			$need_objects_start = true;
		}else if( isset($graph['workers']) ){
			if( sizeof($graph['workers']) > 2 ){
				$need_objects_start = false;
				logit("Graph: " . $graph['name'], "TooMany Threads Found", ['graph'=>$graph['_id'], "count"=>sizeof($graph['workers']) ] );
				foreach( $graph['workers'] as $worker_id=>$wd ){
					logit("Graph: " . $graph['name'], "Thread Delete", ['graph'=>$graph['_id'], "thread_id"=>$worker_id]);
					$mongodb_con->update_one( $db_prefix . "_graph_dbs", ["_id"=>$graph['_id']], [
						'$unset'=>["workers.".$worker_id=>true]
					]);
				}
				$mongodb_con->update_one( $db_prefix . "_graph_dbs", ["_id"=>$graph['_id']], [
					'$unset'=>["run"=>true]
				]);
				logit("Graph: " . $graph['name'], "Stop Invoked", ['graph'=>$graph['_id']]);
			}else if( sizeof($graph['workers']) ){
				foreach( $graph['workers'] as $worker_id=>$wd ){
					if( (time()-$wd['time']) > 30 ){
						logit("Graph: " . $graph['name'], "Thread Inactive");
						$mongodb_con->update_one( $db_prefix . "_graph_dbs", ["_id"=>$graph['_id']], [
							'$unset'=>["workers.".$worker_id=>true]
						]);
					}
				}
			}else{
				logit("Graph: " . $graph['name'], "Zero Threads Found");
				$need_objects_start = true;
			}
		}
		if( $need_objects_start ){
			logit("Graph: " . $graph['name'], "Start", ['graph'=>$graph['_id']]);
			exec('php task_worker_graph.php '. $app_id . ' ' . $graph['_id'] . ' >> ' . $app_id .'_'.$graph['_id'].'.graph.log &', $eoutput);
		}
	}

	//if( (time()-$last_task_check) > 60 )
	{
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
							logit("Internal Queue: " . $queue['topic'], "Found Inactive Thread", ["queue_id"=>$queue['_id'], "worker"=>$wd]);
							$mongodb_con->update_one( $db_prefix . "_queues", ['_id'=>$queue['_id']], [
								'$unset'=>['workers.'.$worker_id=>true],
							]);
							$need_start = true;
						}else{
							$current_active++;
						}
					}
				}
				logit("Internal Queue: " . $queue['topic'], "Status", ["queue_id"=>$queue['_id'], "active_tasks"=>$current_active, "required"=>$queue['con']]);
				if( $need_start || ($queue['con']-$current_active) > 0 ){
					logit("Internal Queue: " . $queue['topic'], "Insufficient Threads", ["queue_id"=>$queue['_id'], "active_tasks"=>$current_active, "required"=>$queue['con']]);
					$mongodb_con->update_one( $db_prefix . "_queues", ['_id'=>$queue['_id']], [
						'run'=>true,
					]);
					for($i=$current_active+1;$i<=$queue['con'];$i++){
						exec('php task_worker.php '. $app_id . ' '. $queue['_id'] . ' >> ' . $app_id . '_'. $queue['_id'] . '.task.log &', $eoutput);
						logit("Internal Queue: " . $queue['topic'], "Start Job");
					}
				}else if( ($current_active>$queue['con']) ){
					logit("Internal Queue: " . $queue['topic'], "Too many jobs Restarting", ["queue_id"=>$queue['_id'], "active_tasks"=>$current_active, "required"=>$queue['con']]);
					$mongodb_con->update_one( $db_prefix . "_queues", ['_id'=>$queue['_id']], [
						'$unset'=>['run'=>true],
					]);
				}
			}else{
				if( isset($queue['workers']) && isset($queue['run']) ){
					logit("Internal Queue: " . $queue['topic'], "Stopping", ['graph'=>$graph['_id']]);
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

	//if( (time()-$last_cron_check) > 55 )
	$time_sec = (int)date("s");
	if( $time_sec <= 9 )
	{

		$last_cron_check = time();

		$cron_res = $mongodb_con->find( $db_prefix . "_cronjobs", ['app_id'=>$app_id] );
		foreach( $cron_res['data'] as $i=>$cron ){

			$run_now = false;

			if( $cron['type'] == "repeat" ){

				$min_range = [];
				if( preg_match("/^[0-9\,]+$/", $cron['repeat']['minute'] ) ){
					$x = explode(",",$cron['repeat']['minute']);
					foreach($x as $i=>$min){
						if( $min!="" ){
							$min = (int)$min;
							$min_range[] = $min;
						}
					}
				
				}else if( preg_match("/^\*\/[0-9]+$/", $cron['repeat']['minute']) ){
					preg_match("/^\*\/([0-9]+)$/", $cron['repeat']['minute'], $m);
					$min = (int)$m[1];
					for($di=0;$di<60;$di=$di+$min){
						$min_range[] = $di;
					}
				
				}else if( $cron['repeat']['minute'] == "*" ){
					for($di=0;$di<=59;$di++){
						$min_range[] = $di;
					}
				}

				$hour_range = [];
				if( preg_match("/^[0-9\,]+$/", $cron['repeat']['hour']) ){
					$x = explode(",",$cron['repeat']['hour']);
					foreach($x as $i=>$min){
						if( $min!="" ){
							$min = (int)$min;
							$hour_range[] = $min;
						}
					}
				
				}else if( preg_match("/^\*\/[0-9]+$/", $cron['repeat']['hour']) ){
					preg_match("/^\*\/([0-9]+)$/", $cron['repeat']['hour'], $m);
					$min = (int)$m[1];
					for($di=0;$di<24;$di=$di+$min){
						$hour_range[] = $di;
					}
				
				}else if( $cron['repeat']['hour'] == "*" ){
					for($di=0;$di<=59;$di++){
						$hour_range[] = $di;
					}
				}

				$date_range = [];
				if( preg_match("/^[0-9\,]+$/",$cron['repeat']['day']) ){
					$x = explode(",",$cron['repeat']['day']);
					foreach($x as $i=>$min){
						if( $min!="" ){
							$min = (int)$min;
							$date_range[] = $min;
						}
					}
				
				}else if( $cron['repeat']['day'] == "*" ){
					for($di=1;$di<=31;$di++){
						$date_range[] = $di;
					}
				}

				$month_range = [];
				if( preg_match("/^[0-9\,]+$/", $cron['repeat']['month']) ){
					$x = explode(",", $cron['repeat']['month']);
					$mons = [];
					foreach($x as $i=>$min){
						if( $min!="" ){
							$min = (int)$min;
							$month_range[] = $min;
						}
					}
				
				}else if( $cron['repeat']['month'] == "*" ){
					for($di=0;$di<12;$di++){
						$month_range[] = $di;
					}
				}

				$week_range = [];
				if( preg_match("/^[0-9\,]+$/", $cron['repeat']['weekday']) ){
					$x = explode(",", $cron['repeat']['weekday']);
					$w = [];
					foreach($x as $i=>$min){
						if( $min!="" ){
							$min = (int)$min;
							$week_range[] = $min;
						}
					}
				
				}else if( $cron['repeat']['weekday'] == "*" ){
					for($di=0;$di<=6;$di++){
						$week_range[] = $di;
					}
				}

				$t = time();
				$year = (int)date("Y");
				$month = ((int)date("m")) - 1;
				$date = (int)date("d");
				$hour = (int)date("H");
				$min = (int)date("i");
				$week = (int)date("w");

				$run_now = true;
				$skip_reason = "Okay";

				if( !in_array($week, $week_range) ){
					$skip_reason = "week";
					$run_now = false;
				}
				if( !in_array($month, $month_range) ){
					$skip_reason = "month";
					$run_now = false;
				}
				if( !in_array($date, $date_range) ){
					$skip_reason = "date";
					$run_now = false;
				}
				if( !in_array($hour, $hour_range) ){
					$skip_reason = "hour";
					$run_now = false;
				}
				if( !in_array($min, $min_range) ){
					$skip_reason = "minite";
					$run_now = false;
				}

				$cron_log = [
					"cron_id"=>$cron['_id'],
					"type"=>$cron['type'],
					"run"=>$run_now,
					"reason"=>$skip_reason,
					"date"=>date("Y-m-d H:i:s")
				];
				logit( "Cron", $cron['_id'], ["run"=>$run_now, "reason"=>$skip_reason] );
				if( $run_now ){
					$background_task_id = $mongodb_con->generate_id();
					$cron_log["task_id"] = $background_task_id;
					$cron_res = $mongodb_con->insert( $db_prefix . "_zlog_cron_" . $app_id, $cron_log );
				}

			}else if( $cron['type'] == "onetime" ){

				$run_now = false;
				$d = gmdate("Y-m-d H:i") . " = " . substr($cron['onetime_gmt'],0,16);
				if( gmdate("Y-m-d H:i") == substr($cron['onetime_gmt'],0,16) ){
					$run_now = true;
				}
				$cron_log = [
					"cron_id"=>$cron['_id'],
					"type"=>$cron['type'],
					"run"=>$run_now,
					"reason"=>$d,
					"date"=>date("Y-m-d H:i:s")
				];
				logit( "Cron", $cron['_id'], ["run"=>$run_now, "reason"=>$d] );
				if( $run_now ){
					$background_task_id = $mongodb_con->generate_id();
					$cron_log["task_id"] = $background_task_id;
					$cron_res = $mongodb_con->insert( $db_prefix . "_zlog_cron_" . $app_id, $cron_log );
				}
			}

			if( $run_now ){
				$lfn = $app_id . '.background.log';
				if( file_exists($lfn) ){
					$s = filesize($lfn);
					if( $s > (1024*1024) ){
						unlink($lfn);
					}
				}
				$bg_res = $mongodb_con->insert( $db_prefix . "_zlog_bg_" . $app_id, [
					"_id"=>$background_task_id,
					//"app_id"=>$app_id, 
					"fn"=>[
						"fn_vid"=>$cron['fn_vid'],
					],
					"source"=>[
						"type"=>"cron",
						"id"=>$cron['_id'],
						"des"=>$cron['des'],
					],
					"start"=>date("Y-m-d H:i:s"),
					"status"=>"Running"
				]);
				exec('php task_worker_background.php '. $app_id . ' ' . $background_task_id . ' ' . $cron['fn_vid'] . ' >> ' . $lfn . ' &', $eoutput);
			}
		}
	}
	sleep(10);
	//break;
}