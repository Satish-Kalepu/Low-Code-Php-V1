<?php

require( "cron_daemon_config.php" );

if( sizeof($argv) < 4 ){
	echo "Need Arguments: app_id and background_task_id function_version_id ";exit;
}
$background_task_id = $argv[2];
if( !$background_task_id ){
	echo "Need Arguments: app_id and background_task_id function_version_id ";exit;
}
$function_version_id = $argv[3];
if( !$function_version_id ){
	echo "Need Arguments: app_id and background_task_id function_version_id ";exit;
}

ini_set("max_execution_time", 20);

$start_time = microtime(true);

register_shutdown_function("shutdown");

function shutdown(){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $background_task_id;
	global $start_time;

}

function update_status( $status, $data ){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $background_task_id;
	global $start_time;

	if( $status == "error" || $status == "fail" ){
		$fres = $mongodb_con->update_one( $db_prefix . "_zlog_bg_" . $app_id, [
			"_id"=>$background_task_id
		], [
			'status'=>"error", 
			'error'=>$data,
			'time'=>round(microtime(true)-$start_time,3)
		]);
	}else{
		$fres = $mongodb_con->update_one( $db_prefix . "_zlog_bg_" . $app_id, [
			"_id"=>$background_task_id
		], [
			'status'=>"success", 
			'result'=>$data,
			'time'=>round(microtime(true)-$start_time,3)
		]);
	}
}

set_error_handler(function($errno, $errstr, $errfile, $errline ){
	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $background_task_id;

	update_status( "error", ['errno'=>$errno,'err'=>$errstr, 'errfile'=>$errfile, 'line'=> $errline] );

    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}, E_ALL & ~E_WARNING & ~E_NOTICE);

$last_fn_check = time();
$fres = $mongodb_con->find_one( $db_prefix . "_functions_versions", [
	'app_id'=>$app_id,
	'_id'=>$function_version_id
]);
if( !isset($fres['data']) ){
	update_status( "error", "Function not found" );exit;
}

$res = $mongodb_con->update_one( $db_prefix . "_zlog_bg_" . $app_id, [
	"_id"=>$background_task_id
], [
	'fn.fn_id'=>$fres['data']['function_id'],
	'fn.fn'=>$fres['data']['name'],
]);
if( !isset($fres['data']['engine']) ){
	update_status( "error", "Function engine not found" );exit;
}

$res = $mongodb_con->find_one( $db_prefix . "_zlog_bg_" . $app_id, [
	"_id"=>$background_task_id
]);
if( !isset($res['data']) ){
	update_status( "error", "BGTask entry not found" );exit;
}

$inputs = [];
if( isset($res['data']['inputs']) ){
	if( gettype($res['data']['inputs']) =="array" ){
		$inputs = $res['data']['inputs'];
	}
}

$api_engine = new api_engine();
if( !$api_engine ){
	update_status( "error", "Error initializing api engine!" );exit;
}

$result = $api_engine->execute( $fres['data'], $inputs, ["request_log_id"=>$background_task_id] );

update_status("success", [
	'status'=>$result['statusCode'], 
	'result'=>$result['body']
]);




