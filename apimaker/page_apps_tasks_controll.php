<?php

if( $_POST['action'] == 'load_functions' ){
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
		"app_id"=>$config_param1
	], ['projection'=>['name'=>1,'version_id'=>1], 'sort'=>['name'=>1]]);
	//print_r( $res );
	json_response($res);
	exit;
}

if( $_POST['action'] == 'load_task_queues' ){
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
		"app_id"=>$config_param1
	], ['sort'=>['topic'=>1]]);
	//print_r( $res );
	if( $res['data'] ){$i = $res['data'];}else{$i=[];}
	json_response([
		'status'=>'success',
		'data'=>[
			'internal'=>$i,
			'external'=>[]
		]
	]);
	exit;
}
if( $_POST['action'] == 'task_queue_delete' ){
	if( isset($_POST['queue_id']) ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue_id'] ) ){
			json_response("fail", "ID incorrect");
		}
	}
	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
		"app_id"=>$config_param1, "_id"=>$_POST['queue_id']
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == 'save_task_queue' ){

	if( !isset($_POST['queue']) ){
		json_response("fail", "Input missing");
	}
	if( isset($_POST['queue']['_id']) ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue']['_id'] ) ){
			json_response("fail", "ID incorrect");
		}
	}
	if( !isset($_POST['queue']['type']) || !isset($_POST['queue']['topic']) || !isset($_POST['queue']['des']) || !isset($_POST['queue']['con']) || !isset($_POST['queue']['ret']) ){
		json_response("fail", "Input missing");
	}

	if( $_POST['queue']['type']!="s" && $_POST['queue']['type']!="m" ){
		json_response("fail", "Input Missing");
	}
	if( !preg_match("/^[a-z0-9\.\-\_]{2,25}$/i", $_POST['queue']['topic'] ) ){
			json_response("fail", "Topic incorrect");
	}
	if( !is_numeric($_POST['queue']['con']) || $_POST['queue']['con']<0 || $_POST['queue']['con']>5 ){
			json_response("fail", "Threads must be numeric 1-5");
	}
	if( !is_numeric($_POST['queue']['ret']) || $_POST['queue']['ret']<0 || $_POST['queue']['ret']>5 ){
			json_response("fail", "Retention period in days must be numeric 1-5");
	}
	if( !is_numeric($_POST['queue']['wait']) || $_POST['queue']['wait']<5 || $_POST['queue']['wait']>60 ){
			json_response("fail", "Timeout be numeric 5-60");
	}
	if( !is_numeric($_POST['queue']['retry']) || $_POST['queue']['retry']>3 ){
			json_response("fail", "Retry limit must be numeric 1-3");
	}
	if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue']['fn_id'] ) ){
		json_response("fail", "Function ID incorrect");
	}
	if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue']['fn_vid'] ) ){
		json_response("fail", "Function Version ID incorrect");
	}

	if( isset($_POST['queue']['_id']) ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
			"app_id"=>$config_param1,
			"topic"=>$_POST['queue']['topic'],"_id"=>['$ne'=>$_POST['queue']['_id']]
		]);
		if( $res['data'] ){
			json_response("fail", "Topic with same name already exists");
		}
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
			"_id"=>$_POST['queue']['_id']
		],[
			"type"=>$_POST['queue']['type'],"topic"=>$_POST['queue']['topic'],"des"=>$_POST['queue']['des'],
			"con"=>(int)$_POST['queue']['con'],"ret"=>(int)$_POST['queue']['ret'],"updated"=>date("Y-m-d H:i:s")
		]);
	}else{
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
			"app_id"=>$config_param1,
			"topic"=>$_POST['queue']['topic'],
		]);
		if( $res['data'] ){
			json_response("fail", "Topic with same name already exists");
		}
		$res = $mongodb_con->count( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
			"app_id"=>$config_param1
		]);
		if( $res['data'] >= 5 ){
			json_response("fail", "Max limit of topics reached");
		}
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
			"app_id"=>$config_param1,
			"type"=>$_POST['queue']['type'],"topic"=>$_POST['queue']['topic'],
			"fn_id"=>$_POST['queue']['fn_id'],"fn_vid"=>$_POST['queue']['fn_vid'],"fn"=>$_POST['queue']['fn'],
			"con"=>(int)$_POST['queue']['con'],"ret"=>(int)$_POST['queue']['ret'],"retry"=>(int)$_POST['queue']['retry'],"wait"=>(int)$_POST['queue']['wait'],
			"created"=>date("Y-m-d H:i:s"),"updated"=>date("Y-m-d H:i:s")
		]);
	}
	
	json_response($res);

	exit;
}
