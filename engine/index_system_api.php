<?php

function system_api( $method, $content_type, $path_params, $php_input ){

	global $mongodb_con;
	global $app_id;
	global $db_prefix;

	if( $method!="POST" ){
		return [ 400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Unexpected GET Request" ]) ];
	}
	if( !preg_match("/(json)/i", $content_type) ){
		return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Unexpected payload" ]) ];
	}
	if( preg_match("/json/i", $content_type) ){
		$post = json_decode($php_input, true);
		if( json_last_error() ){
			$e = "JSON Parse Error: " . json_last_error_msg();
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Payload Json Decode Fail" ]) ];
		}
	}

	if( !isset($path_params[1]) ){
		return [404,"application/json",[], json_encode(["status"=>"fail", "error"=>"Are you lost" ]) ];
	}
	$action = "";
	if( $method =="POST"){
		if( !isset($post['action']) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Action Missing" ]) ];
		}
		$action = $post['action'];
	}
	if( !isset($_SERVER['HTTP_ACCESS_KEY']) ){
		return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access key is required" ]) ];
	}
	$access_key = $_SERVER['HTTP_ACCESS_KEY'];
	$akey = pass_decrypt_static($access_key, "abcdefgh" );
	if( !$akey ){
		return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access key decryption failed" ]) ];
	}
	$adata = json_decode($akey,true);
	if( !is_array($adata) ){
		return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access key decryption failed" ]) ];
	}
	foreach( $adata as $i=>$j ){
		if( $post[ $i ] != $adata[ $i ] ){
			return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access key Rejected: " . $i ]) ];
		}
	}

	if( $path_params[1] == "tasks" ){
		if( $post['action'] == "start_queue" ){

			$res = $mongodb_con->find_one( $db_prefix . "_queues", ['app_id'=>$post['app_id'],'_id'=>$post['queue_id']] );
			if( !$res['data'] ){
				return [200,"application/json",[], json_encode(["status"=>"fail", "error"=>"Queue data not found", "action"=>$post['action'] ]) ];
			}

			if( !file_exists("task_worker.php") ){
				return [200,"application/json",[], json_encode(["status"=>"fail", "error"=>"Worker script not found", "action"=>$post['action'] ]) ];
			}
			//echo "php task_worker.php ". $post['app_id'] . " ". $post['queue_id'];
			$res = exec('php task_worker.php '. $post['app_id'] . ' '. $post['queue_id'] . ' > ' . $post['app_id'] . '_'. $post['queue_id'] . '.task.log &', $eoutput);
			//print_r( $eoutput );
			return [200,"application/json",[], json_encode(["status"=>"success", "error"=>"", "data"=>implode("; ",$eoutput) ]) ];
		}
		if( $post['action'] == "start_taskscheduler" ){

			$res = $mongodb_con->find_one( $db_prefix . "_apps", ['_id'=>$post['app_id']], ['projection'=>['settings'=>1] ] );
			if( !$res['data'] ){
				return [200, "application/json", [], json_encode(["status"=>"fail", "error"=>"App data not found", "action"=>$post['action'] ]) ];
			}
			if( isset($res['data']['settings']['tasks']['workers']) ){
				$f = false;
				foreach( $res['data']['settings']['tasks']['workers'] as $worker_id=>$wd ){
					if( time()-$wd['time'] < 30 ){
						$f = true;
					}else{
						$mongodb_con->update_one( $db_prefix . "_apps", ["_id"=>$post['app_id']], [
							'$unset'=>['settings.tasks.workers.'.$worker_id=>true]
						]);
					}
				}
				if( $f ){
					return [200, "application/json", [], json_encode(["status"=>"fail", "error"=>"A job is already running", "action"=>$post['action'] ]) ];
				}
			}
			if( !file_exists("cron_daemon.php") ){
				return [200,"application/json", [], json_encode(["status"=>"fail", "error"=>"Worker script not found", "action"=>$post['action'] ]) ];
			}
			//echo "php task_worker.php ". $post['app_id'] . " ". $post['queue_id'];
			$res = exec('php cron_daemon.php '. $post['app_id'] .' > ' . $post['app_id'] . '.scheduler.log &', $eoutput);
			//print_r( $eoutput );
			return [200,"application/json",[], json_encode(["status"=>"success", "error"=>"", "data"=>implode("; ",$eoutput) ]) ];
		}
	}
	return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access Key Accepted. But action missing","action"=>$post['action'] ]) ];

}