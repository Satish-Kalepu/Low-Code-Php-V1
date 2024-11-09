<?php

/*

"app_id"=>
"user_id"=>
"owner"=>"user",  user/system
"type"=>  s/m  single thread/multi thread
"topic"=> name
"fn_id"=> function id
"fn_vid"=>  function version id
"fn"=>  function name
"con"=> consumers 1-5
"ret"=> retention period 1-5
"retry"=> retry 
"wait"=> wait delay 5-60
"created"=>
"updated"=>

*/

if( 1==2 ){
	$tid = generate_task_queue_id();
	$mongodb_con->insert($config_global_apimaker['config_mongo_prefix'] . "_zd_queue_6693d28ead3714ae000d70ec", [
		"_id"=>$tid,
		"id"=>$tid,
		"data"=>"ok", "e"=>"ok"
	]);
}

if( $_POST['action'] == 'load_functions' ){
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
		"app_id"=>$config_param1
	], [
		'projection'=>['name'=>1,'version_id'=>1], 
		'sort'=>['name'=>1]
	]);
	//print_r( $res );
	json_response($res);
	exit;
}

if( $_POST['action'] == 'task_load_internal_queue_log' ){
	$cond = [];
	if( $_POST['task_id'] ){
		if( preg_match("/^[a-z0-9\:]+$/i", $_POST['task_id']) ){
			$cond['task_id'] = $_POST['task_id'];
		}else{
			json_response("fail", "Incorrect task id");
		}
	}
	if( $_POST['last'] ){
		if( preg_match("/^[a-f0-9]{24}$/i", $_POST['last']) ){
			$cond['_id'] = ['$lt'=>$_POST['last']];
		}else{
			json_response("fail", "Incorrect _id");
		}
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_zlog_queue_" . $_POST['queue_id'], $cond, [
		'sort'=>['_id'=>-1], 'limit'=>100,
		'projection'=>['ip'=>false],
		'maxTimeMS'=>10000,
	]);
	//print_r( $res );
	json_response($res);
	exit;
}

if( $_POST['action'] == 'load_task_queues' ){
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
		"app_id"=>$config_param1
	], ['sort'=>['topic'=>1]]);
	//print_r( $res );
	if( $res['data'] ){$internal = $res['data'];}else{$internal=[];}
	foreach( $internal as $i=>$j ){
		//echo $db_prefix . "_zd_queue_" . $j['_id'];
		$res2 = $mongodb_con->count( $db_prefix . "_zd_queue_" . $j['_id'] );
		//print_r( $res2 );
		if( $res2['data'] ){
			$internal[ $i ]['queue'] = $res2['data'];
		}else{
			$internal[ $i ]['queue'] = 0;
		}
	}

	json_response([
		'status'=>'success',
		'data'=>[
			'internal'=>$internal,
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

	$q= $db_prefix . "_zd_queue_". $_POST['queue_id'];
	$ql= $db_prefix . "_zlog_queue_". $_POST['queue_id'];

	$res2 = $mongodb_con->database->{$q}->drop();
	$res3 = $mongodb_con->database->{$ql}->drop();
	$res['q'] = $res2;
	$res['ql'] = $res3;
	$res['q_'] = $q;$res['ql_'] = $ql;

	event_log( "system", "task_queue_delete", [
		"app_id"=>$config_param1,
		"queue_id"=>$_POST['queue_id'],
	]);

	json_response($res);
	exit;
}

if( $_POST['action'] == 'task_queue_flush' ){
	if( isset($_POST['queue_id']) ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue_id'] ) ){
			json_response("fail", "ID incorrect");
		}
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
		"app_id"=>$config_param1, "_id"=>$_POST['queue_id']
	]);

	$q= $db_prefix . "_zd_queue_". $_POST['queue_id'];
	$ql= $db_prefix . "_zlog_queue_". $_POST['queue_id'];

	$res2 = $mongodb_con->delete_many( $q, [] );
	$res3 = $mongodb_con->insert($ql, [
		"date"=>date("Y-m-d H:i:s"),
		"event"=>"Flush Queue",
		"result"=>$res2,
	]);

	event_log( "system", "task_queue_flush", [
		"app_id"=>$config_param1,
		"queue_id"=>$_POST['queue_id'],
	]);

	json_response($res);
	exit;
}

if( $_POST['action'] == 'save_task_queue' ){

	if( !isset($_POST['queue']) ){
		json_response("fail", "Input missing 1");
	}
	if( isset($_POST['queue']['_id']) ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue']['_id'] ) ){
			json_response("fail", "ID incorrect");
		}
	}
	if( !isset($_POST['queue']['type']) || !isset($_POST['queue']['topic']) || !isset($_POST['queue']['con']) || !isset($_POST['queue']['ret']) ){
		json_response("fail", "Input missing 2");
	}
	if( $_POST['queue']['type']!="s" && $_POST['queue']['type']!="m" ){
		json_response("fail", "Input Missing 3");
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

	$fres = $mongodb_con->find_one( $db_prefix . "_functions_versions", [
		'app_id'=>$config_param1,
		'_id'=>$_POST['queue']['fn_vid']
	]);
	if( !$fres['data'] ){
		json_response("fail", "Function nto found");
	}
	if( isset($_POST['queue']['_id']) ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
			"app_id"=>$config_param1,
			"topic"=>$_POST['queue']['topic'],
			"_id"=>['$ne'=>$_POST['queue']['_id']]
		]);
		if( $res['data'] ){
			json_response("fail", "Topic with same name already exists");
		}
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
			"_id"=>$_POST['queue']['_id']
		],[
			"type"=>$_POST['queue']['type'],
			"owner"=>"user",
			"type"=>$_POST['queue']['type'],
			"topic"=>$_POST['queue']['topic'],
			"fn_id"=>$_POST['queue']['fn_id'],
			"fn_vid"=>$_POST['queue']['fn_vid'],
			"fn"=>$_POST['queue']['fn'],
			"con"=>(int)$_POST['queue']['con'],
			"ret"=>(int)$_POST['queue']['ret'],
			"retry"=>(int)$_POST['queue']['retry'],
			"wait"=>(int)$_POST['queue']['wait'],
			"updated"=>date("Y-m-d H:i:s")
		]);
		event_log( "system", "task_queue_edit", [
			"app_id"=>$config_param1,
			"queue_id"=>$_POST['queue']['_id'],
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
			"user_id"=>$_SESSION['user_id'],
			"owner"=>"user",
			"type"=>$_POST['queue']['type'],
			"topic"=>$_POST['queue']['topic'],
			"fn_id"=>$_POST['queue']['fn_id'],
			"fn_vid"=>$_POST['queue']['fn_vid'],
			"fn"=>$_POST['queue']['fn'],
			"con"=>(int)$_POST['queue']['con'],
			"ret"=>(int)$_POST['queue']['ret'],
			"retry"=>(int)$_POST['queue']['retry'],
			"wait"=>(int)$_POST['queue']['wait'],
			"created"=>date("Y-m-d H:i:s"),
			"updated"=>date("Y-m-d H:i:s")
		]);
		$queue_id = $res['inserted_id'];
		event_log( "system", "task_queue_create", [
			"app_id"=>$config_param1,
			"queue_id"=>$queue_id,
		]);

		$res5 = $mongodb_con->database->createCollection($db_prefix . "_zd_queue_". $queue_id, [
			"collation"=>["locale"=>"en_US", "strength"=> 2],
			"expireAfterSeconds"=>86400,
			//expireAfterSeconds //capped, //max //size
		]);
	}
	
	json_response($res);

	exit;
}

function find_api_url(){
	global $mongodb_con;global $db_prefix;global $app;
	//if( $app[''])
}

if( $_POST['action'] == 'task_queue_start' ){
	if( isset($_POST['queue_id']) ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue_id'] ) ){
			json_response("fail", "ID incorrect");
		}
	}else{
		json_response("fail", "Need queue id");
	}
	if( !isset($_POST['env']) ){
		json_response("fail", "Need Environment");
	}else if( !is_array($_POST['env']) ){
		json_response("fail", "Need Environment");
	}else if( !isset($_POST['env']['d']) && !isset($_POST['env']['u']) && !isset($_POST['env']['t']) ){
		json_response("fail", "Incorrect Environment Details");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
		"app_id"=>$config_param1, "_id"=>$_POST['queue_id']
	]);
	if( !$res['data'] ){
		json_response("fail", "Queue not found");
	}
	$queue = $res['data'];

	$sz = 0;
	if( isset($queue['workers']) ){
		foreach( $queue['workers'] as $worker_id=>$wd ){
			if( (time()-$wd['time']) > 30 ){
				$mongodb_con->update_one($db_prefix . "_queues", ["_id"=>$_POST['queue_id']], [
					'$unset'=>["workers." .$worker_id=>true]
				]);
			}else{
				$sz++;
			}
		}
		if( $sz >= $queue['con'] ){
			json_response("fail", "Max workers are running");
		}
	}

	if( $queue['type'] == 's' || $_POST['mode'] == "single" ){
		$total_pending = 1-$sz;
	}else{
		$total_pending = $queue['con']-$sz;
	}

	$success = false;
	$apiress = [];
	for($tt=0;$tt<$total_pending;$tt++){
		$akey = pass_encrypt_static(json_encode([
			"action"=>"start_queue", 
			"app_id"=>$config_param1, 
			"queue_id"=>$_POST['queue_id']
		]), "abcdefgh" );
		//print_r( $test_environments );
		$env = $_POST['env'];

		if( $env['t'] == "custom" ){
			$url = "http://" . $env['d'] . $env['e'] . "_api_system/tasks/";
		}else if( $env['t'] == "cloud" ){
			$url = $env['u'] . "_api_system/tasks/";
		}else if( $env['t'] == "cloud-alias" ){
			json_response("fail", "Environment incorrect");
		}
		//echo $url . "\n";

		$apires = curl_post($url, [
			 "action"=>"start_queue", "app_id"=>$config_param1, "queue_id"=>$_POST['queue_id']
		], [
			'Content-type: application/json', 
			"Access-Key: ". $akey 
		]);
		$apiress[] = $apires;
		//print_r( $res );
		if( $apires['status'] == 200 ){
			$data = json_decode($apires['body'],true);
			if( !$data ){
				json_response(['status'=>"fail", "error"=>$apires['body']]);exit;
			}
			if( !isset($data['status']) ){
				json_response(['status'=>"fail", "error"=>"incorrect response from api"]);exit;
			}
			if( $data['status'] == "success" ){
				$success = true;
				event_log( "system", "task_queue_start", [
					"app_id"=>$config_param1,
					"queue_id"=>$_POST['queue_id'],
				]);
			}else{
				json_response($data);exit;
			}
		}
	}

	//curl_post("http://" . );
	if( $success){
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
			"app_id"=>$config_param1, "_id"=>$_POST['queue_id']
		],[
			'run'=>true,
			'started'=>true
		]);
		$res['apires'] = $apiress;

		json_response($res);
	}else if( !$url ){
		json_response("fail", "no api available");
	}else{
		json_response(['status'=>"fail", "error"=>"Error from system api", "apires"=>$apiress]);
	}
	exit;
}

if( $_POST['action'] == 'task_queue_stop' ){
	if( isset($_POST['queue_id']) ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue_id'] ) ){
			json_response("fail", "ID incorrect");
		}
	}
	$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_queues", [
		"app_id"=>$config_param1, "_id"=>$_POST['queue_id']
	],['$unset'=>['run'=>true,'started'=>true] ]);
	event_log( "system", "task_queue_stop", [
		"app_id"=>$config_param1,
		"queue_id"=>$_POST['queue_id'],
	]);
	json_response($res);
	exit;
}


