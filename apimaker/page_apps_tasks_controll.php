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

$external_queue_types = [
	"awssqs"=>[
		"label"=>"AWS SQS",
		"fields"=>[
			"topic"=>[
				"label"=>"Topic",
				"regexp"=>"/^[a-z0-9\-\_\.\(\)\!\@]{3,50}$/i",
				"type"=>"text"
			]
		]
	],
	"awssns"=>[
		"label"=>"AWS SNS",
		"fields"=>[
			"topic"=>[
				"label"=>"Topic",
				"regexp"=>"/^[a-z0-9\-\_\.\(\)\!\@]{3,50}$/i",
				"type"=>"text"
			]
		]
	]
];

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

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_queues_ex", [
		"app_id"=>$config_param1
	], ['sort'=>['topic'=>1]]);
	//print_r( $res );
	if( $res['data'] ){$external = $res['data'];}else{$external=[];}
	foreach( $external as $i=>$j ){
		//echo $db_prefix . "_zd_queue_" . $j['_id'];
		// $res2 = $mongodb_con->count( $db_prefix . "_zd_queue_" . $j['_id'] );
		// //print_r( $res2 );
		// if( $res2['data'] ){
		// 	$internal[ $i ]['queue'] = $res2['data'];
		// }else{
		// 	$external[ $i ]['queue'] = 0;
		// }
		$external[ $i ]['queue'] = 0;
	}

	json_response([
		'status'=>'success',
		'data'=>[
			'internal'=>$internal,
			'external'=>$external,
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

/* -------------------------------------------- */


if( $_POST['action'] == 'task_queue_external_delete' ){
	if( isset($_POST['queue_id']) ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue_id'] ) ){
			json_response("fail", "ID incorrect");
		}
	}
	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_queues_ex", [
		"app_id"=>$config_param1, "_id"=>$_POST['queue_id']
	]);

	event_log( "system", "task_queue_external_delete", [
		"app_id"=>$config_param1,
		"queue_id"=>$_POST['queue_id'],
	]);

	json_response($res);
	exit;
}

if( $_POST['action'] == 'task_queue_external_flush' ){
	if( isset($_POST['queue_id']) ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue_id'] ) ){
			json_response("fail", "ID incorrect");
		}
	}

	json_response(['status'=>"fail", "error"=>"Not implemented"]);
	exit;
}

if( $_POST['action'] == 'save_task_queue_external' ){

	if( !isset($_POST['queue_id']) ){
		json_response("fail", "ID missing");
	}
	if( !preg_match("/^([a-f0-9]{24}|new)$/i", $_POST['queue_id'] ) ){
		json_response("fail", "ID incorrect");
	}
	$queue_id = $_POST['queue_id'];

	if( !isset($_POST['queue']) ){
		json_response("fail", "Input missing 1");
	}
	$queue = $_POST['queue'];
	if( isset($queue['_id']) ){
		if( !preg_match("/^[a-f0-9]{24}$/", $queue['_id'] ) ){
			json_response("fail", "ID incorrect");
		}
		if( $queue['_id'] != $queue_id ){
			json_response("fail", "ID invalid");
		}
		$res = $mongodb_con->find_one($config_global_apimaker['config_mongo_prefix'] . "_queues_ex", [
			"app_id"=>$config_param1,
			"_id"=>$queue_id
		]);
		if( !$res['data'] ){
			json_response("fail", "Queue not found");
		}
	}
	unset($queue['_id']);

	if( !isset($queue['type']) || !isset($queue['topic']) || !isset($queue['con']) || !isset($queue['ret']) ){
		json_response("fail", "Input missing 2");
	}
	if( $queue['type']!="s" && $queue['type']!="m" ){
		json_response("fail", "Input Missing 3");
	}
	if( !preg_match("/^[a-z0-9\.\-\_]{2,25}$/i", $queue['topic'] ) ){
		json_response("fail", "Topic incorrect");
	}

	if( !isset($queue['system']) || !isset($queue['authtype']) || !isset($queue['cred']) ){
		json_response("fail", "Input missing 11");
	}
	if( !in_array($queue['system'],["awssqs", "awssns", "rabbitmq"]) ){
		json_response("fail", "Queue system not allowed 1");
	}
	if( !isset($external_queue_types[ $queue['system'] ]) ){
		json_response("fail", "Queue system not allowed 2");
	}
	if( !isset($queue[ $queue['system'] ]) ){
		json_response("fail", "Queue system not allowed 3");
	}

	foreach( $external_queue_types[ $queue['system'] ]['fields'] as $vf=>$vd ){
		if( !isset($queue[ $queue['system'] ][ $vf ]) ){
			json_response("fail", "Field: " . $vd['label'] . " not found");
		}
		//print_r( $vd );
		$val = $queue[ $queue['system'] ][ $vf ].'';
		//echo $val;
		if( !preg_match($vd['regexp'], $val) ){
			json_response("fail", "Field: " . $vd['label'] . " incorrect value", );
		}
	}

	if( $queue['authtype'] == "" ){
		json_response("fail", "Need Authentication type" );
	}
	if( $queue['authtype'] == "stored" ){
		if( $queue['cred']['cred_id'] == "" || $queue['cred']['name'] == "" ){
			json_response("fail", "Need Credentials");
		}
	}else{
		json_response("fail", "Cred type not implemented");
	}

	if( !is_numeric($queue['con']) || $queue['con']<0 || $queue['con']>5 ){
			json_response("fail", "Threads must be numeric 1-5");
	}
	if( !is_numeric($queue['ret']) || $queue['ret']<0 || $queue['ret']>5 ){
			json_response("fail", "Retention period in days must be numeric 1-5");
	}
	if( !is_numeric($queue['wait']) || $queue['wait']<5 || $queue['wait']>60 ){
			json_response("fail", "Timeout be numeric 5-60");
	}
	if( !is_numeric($queue['retry']) || $queue['retry']>3 ){
			json_response("fail", "Retry limit must be numeric 1-3");
	}
	if( !preg_match("/^[a-f0-9]{24}$/", $queue['fn_id'] ) ){
		json_response("fail", "Function ID incorrect");
	}
	if( !preg_match("/^[a-f0-9]{24}$/", $queue['fn_vid'] ) ){
		json_response("fail", "Function Version ID incorrect");
	}

	$fres = $mongodb_con->find_one( $db_prefix . "_functions_versions", [
		'app_id'=>$config_param1,
		'_id'=>$queue['fn_vid']
	]);
	if( !$fres['data'] ){
		json_response("fail", "Function nto found");
	}

	$queue['app_id'] = $config_param1;
	$queue["con"] = (int)$queue['con'];
	$queue["ret"] = (int)$queue['ret'];
	$queue["retry"] = (int)$queue['retry'];
	$queue["wait"] = (int)$queue['wait'];
	$queue["updated"] = date("Y-m-d H:i:s");

	if( $queue_id != "new" ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_queues_ex", [
			"app_id"=>$config_param1,
			"topic"=>$queue['topic'],
			"_id"=>['$ne'=>$queue_id]
		]);
		if( $res['data'] ){
			json_response("fail", "Topic with same name already exists");
		}
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_queues_ex", [
			"_id"=>$queue_id
		], $queue);
		event_log( "system", "task_queue_external_edit", [
			"app_id"=>$config_param1,
			"queue_id"=>$queue_id,
		]);
	}else{
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_queues_ex", [
			"app_id"=>$config_param1,
			"topic"=>$queue['topic'],
		]);
		if( $res['data'] ){
			json_response("fail", "Topic with same name already exists");
		}
		$res = $mongodb_con->count( $config_global_apimaker['config_mongo_prefix'] . "_queues_ex", [
			"app_id"=>$config_param1
		]);
		if( $res['data'] >= 5 ){
			json_response("fail", "Max limit of topics reached");
		}
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_queues_ex", $queue);
		$queue_id = $res['inserted_id'];
		event_log( "system", "task_queue_external_create", [
			"app_id"=>$config_param1,
			"queue_id"=>$queue_id,
		]);
		$res5 = $mongodb_con->database->createCollection($db_prefix . "_" . $config_param1 . "_zd_queue_" . $queue_id, [
			"collation"=>["locale"=>"en_US", "strength"=> 2],
			"expireAfterSeconds"=>86400,
			//expireAfterSeconds //capped, //max //size
		]);
	}
	json_response($res);

	exit;
}

if( $_POST['action'] == 'task_queue_external_start' ){
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
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_queues_ex", [
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
				$mongodb_con->update_one($db_prefix . "_queues_ex", ["_id"=>$_POST['queue_id']], [
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
			"action"=>"start_external_queue", 
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
			 "action"=>"start_external_queue", "app_id"=>$config_param1, "queue_id"=>$_POST['queue_id']
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
				event_log( "system", "task_queue_external_start", [
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
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_queues_ex", [
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

if( $_POST['action'] == 'task_queue_external_stop' ){
	if( isset($_POST['queue_id']) ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['queue_id'] ) ){
			json_response("fail", "ID incorrect");
		}
	}
	$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_queues_ex", [
		"app_id"=>$config_param1, "_id"=>$_POST['queue_id']
	],['$unset'=>['run'=>true,'started'=>true] ]);
	event_log( "system", "task_queue_external_stop", [
		"app_id"=>$config_param1,
		"queue_id"=>$_POST['queue_id'],
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == "task_load_creds" ){
	$creds = [];
	foreach( $app['creds'] as $cred_id=>$j ){
		$creds[] = [
			"cred_id"=>$cred_id,
			"name"=>$j['name'],
			"type"=>$j['type'],
		];
	}
	json_response([
		"status"=>"success", "data"=>$creds
	]);
	exit;
}

/* -------------------------------------------- */

if( $_POST['action'] == 'task_crons_load' ){
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_cronjobs", [
			"app_id"=>$config_param1
	], [
		'sort'=>[ 'des'=>1 ],
		'projection'=>[
			'des'=>1, 'type'=>1, 'repeat'=>1, 'onetime'=>1, 'onetime_gmt'=>1, 'active'=>1, 'fn_id'=>1, 'fn_vid'=>1, 'fn'=>1
		]
	]);
	json_response($res);
}

if( $_POST['action'] == 'task_cron_activate' ){
	if( !isset($_POST['cron_id']) ){
		json_response("fail", "Cron ID missing");
	}
	if( !preg_match("/^[a-f0-9]{24}$/", $_POST['cron_id']) ){
		json_response("fail", "Incorrect ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_cronjobs", [
			"app_id"=>$config_param1, "_id"=>$_POST['cron_id']
	]);
	if( !$res['data'] ){
		json_response("fail", "Cron not found");
	}
	$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_cronjobs", [
			"app_id"=>$config_param1,
			"_id"=>$_POST['cron_id']
	], [
		'active'=>true
	]);
	event_log( "system", "task_cron_activate", [
		"app_id"=>$config_param1,
		"cron_id"=>$_POST['cron_id'],
	]);
	json_response($res);
}

if( $_POST['action'] == 'task_cron_deactivate' ){
	if( !isset($_POST['cron_id']) ){
		json_response("fail", "Cron ID missing");
	}
	if( !preg_match("/^[a-f0-9]{24}$/", $_POST['cron_id']) ){
		json_response("fail", "Incorrect ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_cronjobs", [
			"app_id"=>$config_param1, "_id"=>$_POST['cron_id']
	]);
	if( !$res['data'] ){
		json_response("fail", "Cron not found");
	}
	$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_cronjobs", [
			"app_id"=>$config_param1,
			"_id"=>$_POST['cron_id']
	], [
		'active'=>false
	]);

	event_log( "system", "task_cron_deactivate", [
		"app_id"=>$config_param1,
		"cron_id"=>$_POST['cron_id'],
	]);

	json_response($res);
}

if( $_POST['action'] == 'task_cron_delete' ){
	if( !isset($_POST['cron_id']) ){
		json_response("fail", "Cron ID missing");
	}
	if( !preg_match("/^[a-f0-9]{24}$/", $_POST['cron_id']) ){
		json_response("fail", "Incorrect ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_cronjobs", [
			"app_id"=>$config_param1, "_id"=>$_POST['cron_id']
	]);
	if( !$res['data'] ){
		json_response("fail", "Cron not found");
	}
	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_cronjobs", ["app_id"=>$config_param1, "_id"=>$_POST['cron_id']] );

	event_log( "system", "task_cron_delete", [
		"app_id"=>$config_param1,
		"cron_id"=>$_POST['cron_id'],
	]);

	json_response($res);
}

if( $_POST['action'] == 'task_cron_save' ){

	if( !isset($_POST['cron']) ){
		json_response("fail", "Missing inputs 1");
	}
	if( !is_array($_POST['cron']) ){
		json_response("fail", "Missing inputs 2");
	}
	$cron = $_POST['cron'];
	if( !isset($cron['type']) ||  !isset($cron['des']) || !isset($cron['onetime']) || !isset($cron['repeat']) ){
		json_response("fail", "Missing inputs 3");
	}
	if( !isset($cron['repeat']['minute']) || !isset($cron['repeat']['hour']) || !isset($cron['repeat']['day']) || !isset($cron['repeat']['month']) || !isset($cron['repeat']['weekday']) ){
		json_response("fail", "Missing inputs 4");
	}
	if( !is_string($cron['repeat']['minute']) || !is_string($cron['repeat']['hour']) || !is_string($cron['repeat']['day']) || !is_string($cron['repeat']['month']) || !is_string($cron['repeat']['weekday']) ){
		json_response("fail", "Missing inputs 5");
	}
	if( !preg_match("/^[0-9\/\,\*]+$/", $cron['repeat']['minute']) || !preg_match("/^[0-9\/\,\*]+$/", $cron['repeat']['hour']) || !preg_match("/^[0-9\/\,\*]+$/", $cron['repeat']['day']) || !preg_match("/^[0-9\/\,\*]+$/", $cron['repeat']['month']) || !preg_match("/^[0-9\/\,\*]+$/", $cron['repeat']['weekday']) ){
		json_response("fail", "Missing inputs 6");
	}
	if( !preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}\:[0-9]{2}$/", $cron['onetime']) ){
		json_response("fail", "Missing inputs 7");
	}
	if( !preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\ [0-9]{2}\:[0-9]{2}$/", $cron['onetime_gmt']) ){
		json_response("fail", "Missing inputs 8");
	}

	if( !preg_match("/^[a-f0-9]{24}$/", $cron['fn_id'] ) ){
		json_response("fail", "Function ID incorrect");
	}
	if( !preg_match("/^[a-f0-9]{24}$/", $cron['fn_vid'] ) ){
		json_response("fail", "Function Version ID incorrect");
	}

	$fres = $mongodb_con->find_one( $db_prefix . "_functions_versions", [
		'app_id'=>$config_param1,
		'_id'=>$cron['fn_vid']
	]);
	if( !$fres['data'] ){
		json_response("fail", "Function nto found");
	}

	if( $cron['type'] == "onetime" ){
		$onetime = $cron['onetime_gmt'];
		date_default_timezone_set("UTC");
		$t = strtotime($onetime);
		if( time() > $t ){
			json_response("fail", "Schedule date/time crossed. Please set the cron in future date");
		}
		date_default_timezone_set($config_global_apimaker['timezone']);
	}
	$cron_id = $cron['_id'];
	if( $cron_id != "new" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $cron_id) ){
			json_response("fail", "Incorrect ID");
		}
	}
	unset($cron['_id']);
	//echo $cron_id ;exit;
	$cron['app_id'] = $config_param1;
	if( $cron_id == "new" ){
		$cron['created'] = date("Y-m-d H:i:s");
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_cronjobs", $cron );
		$cron_id = $res['inserted_id'];
	}else{
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_cronjobs", [
				"app_id"=>$config_param1, "_id"=>$cron_id
		]);
		if( !$res['data'] ){
			json_response("fail", "Cron not found");
		}
		$cron['updated'] = date("Y-m-d H:i:s");
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_cronjobs", [
			"app_id"=>$config_param1, "_id"=>$cron_id
		], $cron );
	}
	event_log( "system", "task_cron_save", [
		"app_id"=>$config_param1,
		"cron_id"=>$cron_id,
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == 'task_load_cron_log' ){
	if( !preg_match("/^[a-f0-9]{24}$/", $_POST['cron_id']) ){
		json_response("fail", "Incorrect cron id");
	}
	$cond = ['cron_id'=>$_POST['cron_id']];
	if( $_POST['keyword'] ){
		if( preg_match("/^[a-f0-9]{24}$/", $_POST['keyword']) ){
			$cond['$or'] = [
				['task_id'=> $_POST['keyword']],
				['_id'=> $mongodb_con->get_id($_POST['keyword']) ]
			];
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
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_zlog_cron_" . $config_param1, $cond, [
		'sort'=>['_id'=>-1], 'limit'=>100,
		'maxTimeMS'=>10000,
	]);
	//print_r( $res );
	$res['cond'] = $cond;
	json_response($res);
	exit;
}


if( $_POST['action'] == 'task_bg_load' ){
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_zlog_bg_". $config_param1, [
	], [
		'sort'=>[ '_id'=>-1 ],
		'limit'=>100,
	]);
	json_response($res);
}

