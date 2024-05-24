<?php

if( $_POST['action'] == "load_records" ){

	$cond = ['app_id'=>$config_param1];
	$ops  = ['limit'=>100, 'sort'=>['_id'=>-1]];
	$res = $mongodb_con->find( $db_prefix . "_zlog_requests", $cond, $ops );

	json_response($res);

	exit;

}