<?php

function send_to_keywords_queue($object_id){
	global $mongodb_con;global $db_prefix;global $graph_queue;global $graph_id;
	//error_log("queue: " . $object_id );
	$task_id = generate_task_queue_id();
	$mongodb_con->insert( $graph_queue, [
		'_id'=>$task_id,
		'id'=>$task_id,
		'data'=>[
			"action"=> "thing_update",
			'graph_id'=>$graph_id,
			"thing_id"=>$object_id
		],
		'm_i'=>date("Y-m-d H:i:s")
	]);
}
function send_to_keywords_delete_queue($object_id){
	global $mongodb_con;global $db_prefix;global $graph_queue;global $graph_id;
	//error_log("queue: " . $object_id );
	$task_id = generate_task_queue_id();
	$mongodb_con->insert( $graph_queue, [
		'_id'=>$task_id,
		'id'=>$task_id,
		'data'=>[
			"action"=> "thing_delete",
			'graph_id'=>$graph_id,
			"thing_id"=>$object_id
		],
		'm_i'=>date("Y-m-d H:i:s")
	]);
}
function send_to_records_queue( $object_id, $record_id, $action ){
	global $mongodb_con;global $db_prefix;global $graph_queue;global $graph_id;
	//error_log("queue: " . $object_id );
	$task_id = generate_task_queue_id();
	$mongodb_con->insert( $graph_queue, [
		'_id'=>$task_id,
		'id'=>$task_id,
		'data'=>[
			"action"=> $action,
			'graph_id'=>$graph_id,
			"object_id"=>$object_id,
			"record_id"=>$record_id
		],
		'm_i'=>date("Y-m-d H:i:s")
	]);
}


if( $_GET['action'] == "buildkeywords" ){
	$res= $mongodb_con->find( $graph_things );
	foreach( $res['data'] as $i=>$j ){
		echo $j['_id'] . ": " . $j['l']['v'] . "<BR>";
		send_to_keywords_queue( $j['_id'] );
	}
	exit;
}

if( $_POST['action'] == "context_load_things" ){
	$things = [];
	if( $_POST['thing'] == "GT-ALL" ){
		$cond = [];
		$sort = [];
		if( $_POST['keyword'] ){
			$cond['p'] = ['$gte'=>$_POST['keyword'], '$lte'=>$_POST['keyword']."zzz" ];
			$sort = ['p'=>1];
			$debug_t = true;
			$res = $mongodb_con->find( $graph_keywords, $cond, [
				"sort"=>$sort, 
				"limit"=>200,
			]);
			foreach( $res['data'] as $i=>$j ){
				$things[] = [
					'l'=>['t'=>'T', 'v'=>$j['p']],
					'i_of'=>['i'=>$j['pid'],'v'=>$j['pl'],'t'=>"GT"],
					'i'=>$j['tid'],
					'ol'=>$j['l'],
					'm'=>isset($j['m'])?true:false,
					't'=>$j['t'],
				];
			}
		}else{
			$cond['cnt'] = ['$gt'=>1];
			$sort = ['cnt'=>-1];
			$res = $mongodb_con->find( $graph_things, $cond, [
				"sort"=>$sort, 
				"projection"=>['l'=>true, 'i'=>true,'i_of'=>true,'i'=>'$_id', '_id'=>false],
				"limit"=>100,
			]);
			foreach( $res['data'] as $i=>$j ){
				$j['i'] = (string)$j['i'];
				$things[] = $j;
			}
			if( sizeof($things) < 50 ){
				$res = $mongodb_con->find( $graph_things, [], [
					"sort"=>['_id'=>1], 
					"projection"=>['l'=>true, 'i'=>true,'i_of'=>true,'i'=>'$_id', '_id'=>false],
					"limit"=>100,
				]);
				foreach( $res['data'] as $i=>$j ){
					$j['i'] = (string)$j['i'];
					$things[] = $j;
				}
			}
		}
		//$cond['l.v'] = ['$gt'=>"pe", '$lt'=>"pezzz" ];

	}else{
		$things = [];
	}
	json_response([
		"status"=>"success",
		"things"=>$things,
		"keyword"=>$_POST['keyword']??'',
	]);
}


if( $_POST['action'] == "objects_load_basic" ){
	$res = $mongodb_con->find( $graph_things, ['cnt'=>['$exists'=>1]], [
		'projection'=>['i'=>'$_id', '_id'=>false, 'l'=>1,'i_of'=>1,'cnt'=>1], 
		'sort'=>['cnt'=>-1], 
		'limit'=>500
	]);
	foreach( $res['data'] as $i=>$j ){
		//print_r( $j );
		$res['data'][ $i ]['i'] = (string)$res['data'][ $i ]['i'];
	}
	json_response($res);
	exit;
}

if( $_POST['action'] == "objects_load_object" ){
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( $res['data'] ){
		$res2 = $mongodb_con->find_one( $graph_things, [
			'_id'=>$res['data']['i_of']['i'] 
		], [
			'projection'=>['z_t'=>1,'z_o'=>1,'z_n'=>1]
		]);
		if( isset($res2['data']) ){
			$res['data']['i_of']['z_t'] = isset($res2['data']['z_t'])?$res2['data']['z_t']:[];
			$res['data']['i_of']['z_o'] = isset($res2['data']['z_o'])?$res2['data']['z_o']:[];
			$res['data']['i_of']['z_n'] = isset($res2['data']['z_n'])?$res2['data']['z_n']:1;
		}else{
			$res['data']['i_of']['z_t'] = [];
			$res['data']['i_of']['z_o'] = [];
			$res['data']['i_of']['z_n'] = 1;
		}
		json_response($res);
	}else{
		json_response(["status"=>"fail", "error"=>"Node not found"]);
	}
	exit;
}

if( $_POST['action'] == "objects_load_template" ){
	$res = $mongodb_con->find_one( $graph_things, [
		'_id'=>$_POST['object_id']
	], [
		'projection'=>['z_t'=>1,'z_o'=>1,'z_n'=>1,'l'=>1,'i_of'=>1, 'i_t'=>1]
	]);
	if( $res['data'] ){
		if( !isset($res['data']['z_t']) ){
			// $res['data']['z_t'] = [];
			// $res['data']['z_o'] = [];
			// $res['data']['z_n'] = 1;
		}
		json_response(['status'=>"success", "data"=>$res['data']]);
	}else{
		json_response(['status'=>"fail", "error"=>"Not found"]);
	}
	exit;
}

if( $_POST['action'] == "objects_load_records" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Object ID");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Need object ID");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Node not found");
	}
	$graph_things_dataset = $graph_things . "_". $_POST['object_id'];

	if( $res['data']['i_t']['v'] == "L" ){
		$cond = [];
		$res = $mongodb_con->count( $graph_things_dataset, $cond );
		$cnt = (int)$res['data'];
		$sort = [];
		if( $_POST['sort'] == "_id" ){
			if( $_POST['order'] == "Asc" ){
				$sort['_id'] = 1;
				if( $_POST['from'] ){$cond['_id'] = ['$gte'=> $_POST['from']];}
				if( $_POST['last'] ){$cond['_id'] = ['$gt'=> $_POST['last']];}
			}else{
				$sort['_id'] = -1;
				if( $_POST['from'] ){$cond['_id'] = ['$lte'=> $_POST['from']];}
				if( $_POST['last'] ){$cond['_id'] = ['$lt'=> $_POST['last']];}
			}
		}else{
			if( $_POST['order'] == "Asc" ){
				$sort = [ "props.".$_POST['sort'].".v" => 1, "_id"=>1 ];
				if( $_POST['from'] ){$cond["props.".$_POST['sort'].".v"] = ['$gte'=> $_POST['from']];}
				if( $_POST['last'] ){$cond["props.".$_POST['sort'].".v"] = ['$gt'=> $_POST['last']];}
			}else{
				$sort = [ "props.".$_POST['sort'].".v" => -1, "_id"=>1 ];
				if( $_POST['from'] ){$cond["props.".$_POST['sort'].".v"] = ['$lte'=> $_POST['from']];}
				if( $_POST['last'] ){$cond["props.".$_POST['sort'].".v"] = ['$lt'=> $_POST['last']];}
			}
		}
		$ops = ['='=>'$eq','!='=>'$ne', '>'=>'$le', '>='=>'$leq', '<'=>'$ge', '<='=>'$geq'];
		if( isset($_POST['cond']) ){
			foreach( $_POST['cond'] as $i=>$j ){
				if( isset($j['field']['k']) && isset($j['ops']['k']) && isset($j['value']['v']) ){
					if( $j['field']['k'] && $j['ops']['k'] && trim($j['value']['v']) ){
						if( $j['field']['k'] == "_id" ){
							$cond[ $j['field']['k'] ] = [ $ops[ $j['ops']['k'] ] => $j['value']['v'] ];
						}else{
							$cond[ 'props.'. $j['field']['k'].".v" ] = [ $ops[ $j['ops']['k'] ] => $j['value']['v'] ];
						}
					}
				}
			}
		}
		$res = $mongodb_con->find( $graph_things_dataset, $cond, [
			'sort'=>$sort,
			'limit'=>100,
		]);
		$res['cnt'] = $cnt;
		$res['cond'] = $cond;
		$res['sort'] = $sort;
		json_response($res);
	}else{
		$cond = ['i_of.i'=>$_POST['object_id']];
		$res = $mongodb_con->count( $graph_things, $cond );
		$cnt = (int)$res['data'];
		if( $_POST['from'] ){
			$cond['l.v'] = ['$gt'=> $_POST['from']];
		}
		if( $_POST['last'] ){
			$cond['l.v'] = ['$gt'=> $_POST['last']];
		}
		$res = $mongodb_con->find( $graph_things, $cond, [
			'projection'=>['l'=>1,'props'=>1,'i_of'=>1,'m_u'=>1],
			'sort'=>['l.v'=>1],
			'limit'=>100,
		]);
		$res['cnt'] = $cnt;
		json_response($res);
	}
	exit;
}

if( $_POST['action'] == "objects_load_browse_list" ){

	$res = $mongodb_con->count( $graph_things, [] );
	$cnt = (int)$res['data'];
	$cond = [];
	if( $_POST['sort'] == "label" ){
		if( $_POST['order'] == "asc" ){
			$sort = ['l.v'=>1];
			if( $_POST['from'] ){
				$cond['l.v'] = ['$gte'=> $_POST['from']];
			}
			if( $_POST['last'] ){
				$cond['l.v'] = ['$gte'=> $_POST['last']];
			}
		}else{
			$sort = ['l.v'=>-1];
			if( $_POST['from'] ){
				$cond['l.v'] = ['$lte'=> $_POST['from']];
			}
			if( $_POST['last'] ){
				$cond['l.v'] = ['$lte'=> $_POST['last']];
			}
		}
	}else if( $_POST['sort'] == "ID" ){
		if( $_POST['order'] == "asc" ){
			$sort = ['_id'=>1];
			if( $_POST['from'] ){
				$cond['_id'] = ['$gte'=> $_POST['from']];
			}
			if( $_POST['last'] ){
				$cond['_id'] = ['$gte'=> $_POST['last']];
			}
		}else{
			$sort = ['_id'=>-1];
			if( $_POST['from'] ){
				$cond['_id'] = ['$lte'=> $_POST['from']];
			}
			if( $_POST['last'] ){
				$cond['_id'] = ['$lte'=> $_POST['last']];
			}
		}
	}else if( $_POST['sort'] == "nodes" ){
		$cond['cnt'] = ['$gt'=>1];
		if( $_POST['order'] == "asc" ){
			$sort = ['cnt'=>1];
		}else{
			$sort = ['cnt'=>-1];
		}
	}
	$res = $mongodb_con->find( $graph_things, $cond, [
		'projection'=>['l'=>1,'i_of'=>1, 'm_i'=>1, 'm_u'=>1,'cnt'=>1],
		'sort'=>$sort,
		'limit'=>100,
	]);
	$res['cnt'] = $cnt;
	json_response($res);
	exit;
}

if( $_POST['action'] == "objects_edit_label" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$object_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$object_id] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	$object = $res['data'];
	if( !isset($_POST['label']) ){
		json_response("fail", "Need Label");
	}else if( !is_array($_POST['label']) ){
		json_response("fail", "Need Label");
	}else if( !isset($_POST['label']['t']) || !isset($_POST['label']['v']) ){
		json_response("fail", "Need Label");
	}
	$label = $_POST['label'];

	$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$object['i_of']['i'], 'l.v'=>$label['v'], '_id'=>['$ne'=>$object_id] ] );
	if( $res['data'] ){
		json_response("fail", "Duplicate Node Exists");
	}

	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
		'l'=>$label,
		'updated'=>date("Y-m-d H:i:s")
	]);

	send_to_keywords_queue($object_id);

	event_log( "objects", "edit_label", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$object_id,
	]);

	json_response( $res );
	exit;
}
if( $_POST['action'] == "objects_edit_type" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$object_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$object_id] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	$object = $res['data'];
	$current_type = $object['i_t']['v'];
	if( !isset($_POST['type']) ){
		json_response("fail", "Need type");
	}else if( !is_array($_POST['type']) ){
		json_response("fail", "Need Type");
	}else if( !isset($_POST['type']['t']) || !isset($_POST['type']['v']) ){
		json_response("fail", "Need Type");
	}
	$type = $_POST['type'];

	if( $current_type == "N" && $type['v'] != "N" ){
		if( isset($object['cnt']) && $object['cnt'] > 0 ){
			json_response("fail", "There are nodes " . $object['cnt'] . " under this object");
		}
	}

	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
		'i_t'=>$type,
		'updated'=>date("Y-m-d H:i:s")
	]);

	send_to_keywords_queue($object_id);

	event_log( "objects", "edit_type", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$object_id,
	]);

	json_response( $res );
	exit;
}
if( $_POST['action'] == "objects_edit_alias" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$object_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$object_id] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	$object = $res['data'];
	if( !isset($_POST['alias']) ){
		json_response("fail", "Need alias");
	}else if( !is_array($_POST['alias']) ){
		json_response("fail", "Need alias");
	}else{
		if( array_keys($_POST['alias'])[0] !== 0 ){
			$_POST['alias'] = [];
		}
		$als = [];
		for($i=0;$i<sizeof($_POST['alias']);$i++){
			$v =$_POST['alias'][$i];
			if( !isset($v['t']) || !isset($v['v']) || $v['v'] == "" ){
				array_splice($_POST['alias'],$i,1);$i--;
			}else if( strtolower($v['v']) == strtolower($object['l']['v']) ){
				json_response("fail", "Label and Alias should be different");
			}
			if( in_array(strtolower($v['v']), $als) ){
				array_splice($_POST['alias'],$i,1);$i--;
			}else{
				$als[] = strtolower($v['v']);
			}
		}
	}
	//print_r( $als );exit;
	if( sizeof($_POST['alias']) ){
		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
			'al'=>$_POST['alias'],
			'updated'=>date("Y-m-d H:i:s")
		]);
	}else{
		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
			'$unset'=>[ 'al'=>true ],
			'$set'=>['updated'=>date("Y-m-d H:i:s")],
		]);
	}
	send_to_keywords_queue($object_id);
	event_log( "objects", "edit_alias", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$object_id,
	]);
	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_edit_i_of" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$object_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$object_id] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	$object = $res['data'];
	if( !isset($_POST['i_of']) ){
		json_response("fail", "Need Instance Of");
	}else if( !is_array($_POST['i_of']) ){
		json_response("fail", "Need Instance Of");
	}else if( !isset($_POST['i_of']['t']) || !isset($_POST['i_of']['v']) ){
		json_response("fail", "Need Instance Of");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['i_of']['i']) && !preg_match("/^[0-9]+$/i", $_POST['i_of']['i'] ) ){
		json_response("fail", "Instance id incorrect");
	}
	$i_of = $_POST['i_of'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$i_of['i']] );
	if( !$res['data'] ){
		json_response("fail", "Instance not found");
	}
	$instance = $res['data'];

	$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$i_of['i'], 'l.v'=>$object['l']['v'], '_id'=>['$ne'=>$object_id] ] );
	if( $res['data'] ){
		json_response("fail", "Duplicate Node Exists in Instance: " . $i_of['v']);
	}
	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
		'i_of'=>$i_of,
		'updated'=>date("Y-m-d H:i:s")
	]);
	send_to_keywords_queue($object_id);

	$res2 = $mongodb_con->increment( $graph_things, $object['i_of']['i'], "cnt", -1 );
	$res2 = $mongodb_con->increment( $graph_things, $_POST['data']['i_of']['i'], "cnt", 1 );

	event_log( "objects", "edit_instance", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$object_id,
	]);

	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_object_add_field" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	// field
	// prop
	if( !isset($_POST['field']) ){
		json_response("fail", "Incorrect data 1");
	}else if( !preg_match("/^p[0-9]+$/",$_POST['field']) ){
		json_response("fail", "Incorrect data 2");
	}
	if( !isset($_POST['prop']) ){
		json_response("fail", "Incorrect data 3");
	}else if( !is_array($_POST['prop']) ){
		json_response("fail", "Incorrect data 4");
	}

	if( isset($res['data']['z_t'][ $_POST['field'] ]) ){
		json_response("fail", "Field key ".$_POST['field']." already exists");
	}
	$n = intval(str_replace("p","",$_POST['field']));
	if( $n < $res['data']['z_n'] ){
		json_response("fail", "Field keyindex ".$_POST['z_n']." already exists");
	}

	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$_POST['object_id']], [
		'$set'=>[ 
			'z_t.'. $_POST['field']=>$_POST['prop'],
			'z_n'=>$_POST['z_n']
		],
		'$push'=>['z_o'=>$_POST['field']],
	]);
	event_log( "objects", "field_add", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$_POST['object_id'],
		"field"=>$_POST['field']
	]);
	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_delete_field" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Object ID");
	}else if( !preg_match("/^[a-z0-9]{2,100}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Need object ID");
	}
	if( !isset($_POST['prop']) ){
		json_response("fail", "Need Prop ID");
	}else if( !preg_match("/^p[0-9]+$/i", $_POST['prop']) ){
		json_response("fail", "Need Prop ID");
	}

	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Node not found");
	}

	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$_POST['object_id']], [
		'$unset'=>[
			'z_t.' . $_POST['prop']=>true,
		],
		'$pull'=>[
			'z_o'=>$_POST['prop']
		]
	]);

	event_log( "objects", "field_delete", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$_POST['object_id'],
		"field"=>$_POST['prop']
	]);

	json_response($res);

	exit;
}

if( $_POST['action'] == "objects_save_object_z_t" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	// field
	// prop
	if( !isset($_POST['field']) ){
		json_response("fail", "Incorrect data 1");
	}else if( !preg_match("/^p[0-9]+$/",$_POST['field']) ){
		json_response("fail", "Incorrect data 2");
	}
	if( !isset($_POST['prop']) ){
		json_response("fail", "Incorrect data 3");
	}else if( !is_array($_POST['prop']) ){
		json_response("fail", "Incorrect data 4");
	}
	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$_POST['object_id']], [
		'z_t.'. $_POST['field']=>$_POST['prop'],
	]);
	event_log( "objects", "template_save", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$_POST['object_id'],
	]);
	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_save_enable_z_t" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	// field
	// prop
	if( !isset($_POST['z_t']) ){
		json_response("fail", "Incorrect data 01");
	}else if( !is_array($_POST['z_t']) ){
		json_response("fail", "Incorrect data 02");
	}
	if( !isset($_POST['z_o']) ){
		json_response("fail", "Incorrect data 11");
	}else if( !is_array($_POST['z_o']) ){
		json_response("fail", "Incorrect data 12");
	}
	if( !isset($_POST['z_n']) ){
		json_response("fail", "Incorrect data 11");
	}else if( !is_numeric($_POST['z_n']) ){
		json_response("fail", "Incorrect data 22");
	}
	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$_POST['object_id']], [
		'z_t'=>$_POST['z_t'],'z_o'=>$_POST['z_o'],'z_n'=>$_POST['z_n'],
	]);
	event_log( "objects", "template_enable", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$_POST['object_id'],
	]);
	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_save_z_o" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	if( !isset($_POST['z_o']) ){
		json_response("fail", "Incorrect data 1");
	}else if( !is_array($_POST['z_o']) ){
		json_response("fail", "Incorrect data 2");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$_POST['object_id']], [
		'z_o'=>$_POST['z_o'],
	]);
	event_log( "objects", "template_save_order", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$_POST['object_id'],
	]);
	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_save_props" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$thing_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$thing_id] );
	if( !$res['data'] ){
		json_response("fail", "thing not found");
	}
	$res2 = $mongodb_con->find_one( $graph_things, ['_id'=>$res['data']['i_of']['i']] );
	if( !$res2['data'] ){
		json_response("fail", "parent not found");
	}
	$parent = $res2['data'];

	if( !isset($_POST['props']) ){
		json_response("fail", "Data missing");
	}else if( !is_array($_POST['props']) ){
		json_response("fail", "Data missing");
	}

	$props = $_POST['props'];

	foreach( $props as $field=>$values ){
		if( !is_array($values) ){
			json_response("fail", "Property `" . $field . "` has invalid value");
		}
		if( isset($parent['z_t'][ $field ]) ){
			if( $parent['z_t'][ $field ]['t']['k'] == "O" ){
				for($pi=0;$pi<sizeof($props[ $field ]);$pi++){
					$pd = $props[ $field ][ $pi ];
					$f = false;
					foreach( $parent['z_t'][ $field ]['z']['z_t'] as $fd=>$fn ){
						if( isset( $pd['v'][ $fd ] ) ){
							if( isset($pd['v'][ $fd ]['t']) && isset($pd['v'][ $fd ]['v']) ){
								if( $pd['v'][ $fd ]['v'] ){
									$f = true;
								}
							}else{
								json_response( "fail", "Property `" . $field . "` item: ".($pi+1)." property: " . $fn['l']['v'] . " has invalid value: ".json_encode($pd['v'][$fd]) );
							}
						}
					}
					if( $f == false ){
						array_splice( $props[ $field ], $pi, 1);
						$pi--;
					}
				}
			}else{
				foreach( $values as $pi=>$pd ){
					if( isset($pd['t']) && isset($pd['v']) ){

					}else{
						json_response("fail", "Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd));
					}
				}
			}
		}
	}
	//print_r( $data );

	$data = [
		'updated' => date("Y-m-d H:i:s"),
		'props' => $props
	];
	
	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$thing_id], $data );

	event_log( "objects", "props_save", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$_POST['object_id'],
	]);

	json_response($res);
	exit;
}
if( $_POST['action'] == "objects_dataset_record_save_props" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	if( !isset($_POST['record_id']) ){
		json_response("fail", "Need Record id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['record_id']) && !preg_match("/^[0-9]+$/i", $_POST['record_id']) ){
		json_response("fail", "Record id incorrect");
	}
	$thing_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$thing_id] );
	if( !$res['data'] ){
		json_response("fail", "thing not found");
	}
	$res2 = $mongodb_con->find_one( $graph_things, ['_id'=>$res['data']['i_of']['i']] );
	if( !$res2['data'] ){
		json_response("fail", "parent not found");
	}
	$parent = $res2['data'];

	$record_id = $_POST['record_id'];
	$res = $mongodb_con->find_one( $graph_things . "_". $thing_id, ['_id'=>$record_id] );
	if( !$res['data'] ){
		json_response("fail", "Record not found");
	}

	if( !isset($_POST['props']) ){
		json_response("fail", "Data missing");
	}else if( !is_array($_POST['props']) ){
		json_response("fail", "Data missing");
	}

	$props = $_POST['props'];

	foreach( $props as $field=>$values ){
		if( !is_array($values) ){
			json_response("fail", "Property `" . $field . "` has invalid value");
		}
		if( isset($parent['z_t'][ $field ]) ){
			if( $parent['z_t'][ $field ]['t']['k'] == "O" ){
				for($pi=0;$pi<sizeof($props[ $field ]);$pi++){
					$pd = $props[ $field ][ $pi ];
					$f = false;
					foreach( $parent['z_t'][ $field ]['z']['z_t'] as $fd=>$fn ){
						if( isset( $pd['v'][ $fd ] ) ){
							if( isset($pd['v'][ $fd ]['t']) && isset($pd['v'][ $fd ]['v']) ){
								if( $pd['v'][ $fd ]['v'] ){
									$f = true;
								}
							}else{
								json_response( "fail", "Property `" . $field . "` item: ".($pi+1)." property: " . $fn['l']['v'] . " has invalid value: ".json_encode($pd['v'][$fd]) );
							}
						}
					}
					if( $f == false ){
						array_splice( $props[ $field ], $pi, 1);
						$pi--;
					}
				}
			}else{
				foreach( $values as $pi=>$pd ){
					if( isset($pd['t']) && isset($pd['v']) ){

					}else{
						json_response("fail", "Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd));
					}
				}
			}
		}
	}
	//print_r( $data );

	$data = [
		'm_u' => date("Y-m-d H:i:s"),
		'props' => $props
	];
	
	$res = $mongodb_con->update_one( $graph_things. "_". $thing_id, ['_id'=>$record_id], $data );

	event_log( "objects", "record_props_save", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$_POST['object_id'],
		"record_id"=>$_POST['record_id'],
	]);

	json_response($res);
	exit;
}
if( $_POST['action'] == "objects_dataset_record_create" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$thing_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$thing_id] );
	if( !$res['data'] ){
		json_response("fail", "thing not found");
	}
	$res2 = $mongodb_con->find_one( $graph_things, ['_id'=>$res['data']['i_of']['i']] );
	if( !$res2['data'] ){
		json_response("fail", "parent not found");
	}
	$parent = $res2['data'];

	if( !isset($_POST['record_props']) ){
		json_response("fail", "Data missing");
	}else if( !is_array($_POST['record_props']) ){
		json_response("fail", "Data missing");
	}

	$props = $_POST['record_props'];

	foreach( $props as $field=>$values ){
		if( !is_array($values) ){
			json_response("fail", "Property `" . $field . "` has invalid value");
		}
		if( isset($parent['z_t'][ $field ]) ){
			if( $parent['z_t'][ $field ]['t']['k'] == "O" ){
				for($pi=0;$pi<sizeof($props[ $field ]);$pi++){
					$pd = $props[ $field ][ $pi ];
					$f = false;
					foreach( $parent['z_t'][ $field ]['z']['z_t'] as $fd=>$fn ){
						if( isset( $pd['v'][ $fd ] ) ){
							if( isset($pd['v'][ $fd ]['t']) && isset($pd['v'][ $fd ]['v']) ){
								if( $pd['v'][ $fd ]['v'] ){
									$f = true;
								}
							}else{
								json_response( "fail", "Property `" . $field . "` item: ".($pi+1)." property: " . $fn['l']['v'] . " has invalid value: ".json_encode($pd['v'][$fd]) );
							}
						}
					}
					if( $f == false ){
						array_splice( $props[ $field ], $pi, 1);
						$pi--;
					}
				}
			}else{
				foreach( $values as $pi=>$pd ){
					if( isset($pd['t']) && isset($pd['v']) ){

					}else{
						json_response("fail", "Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd));
					}
				}
			}
		}
	}
	//print_r( $data );

	$data = [
		'm_i' => date("Y-m-d H:i:s"),
		'm_u' => date("Y-m-d H:i:s"),
		'props' => $props
	];
	
	$res = $mongodb_con->insert( $graph_things. "_". $thing_id, $data );

	event_log( "objects", "record_create", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$_POST['object_id'],
		"record_id"=>$res['inserted_id'],
	]);
	send_to_records_queue( $_POST['object_id'], $res['inserted_id'], "record_create" );

	json_response($res);
	exit;
}


if( $_POST['action'] == "objects_create_with_template" ){

	if( !isset($_POST['thing']) ){
		json_response("fail", "Data missing");
	}
	$thing = $_POST['thing'];
	if( !is_array( $thing ) ){
		json_response("fail", "Data missing");
	}
	if( !isset( $thing['l'] ) || !isset( $thing['i_of'] ) ){
		json_response("fail", "Data missing");
	}
	if( !is_array( $thing['l'] ) || !is_array( $thing['i_of'] ) ){
		json_response("fail", "Data missing");
	}
	if( !isset( $thing['l']['v'] ) || !$thing['l']['v'] ){
		json_response("fail", "Node name missing");
	}
	if( !isset( $thing['i_t'] ) ){
		json_response("fail", "Node type missing");
	}
	if( !isset( $thing['i_t']['t'] ) || !isset( $thing['i_t']['v'] ) ){
		json_response("fail", "Node type missing");
	}
	if( $thing['i_t']['t'] != "T" || !preg_match("/^(N|L|M|D)$/", $thing['i_t']['v'] ) ){
		json_response("fail", "Node type missing");
	}

	$instance_id = $thing['i_of']['i'];
	if( !preg_match("/^[a-z0-9]{2,24}$/i", $instance_id) ){
		json_response("fail", "Instance id incorrect");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$instance_id] );
	if( !$res['data'] ){
		json_response("fail", "Instance node not found");
	}
	$instance = $res['data'];

	if( $instance['l']['v'] == "Root" && $thing['l']['t'] == "GT" ){
		json_response("fail", "Nodes under Root instance should not refer other nodes");
	}

	if( isset($instance['z_t']) ){
		if( !isset( $thing['props'] ) ){
			json_response("fail", "Properties Data missing");
		}
		if( !is_array( $thing['props'] ) ){
			json_response("fail", "Properties Data missing");
		}
	}
	$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$instance_id, 'l.v'=>$thing['l']['v']] );
	if( $res['data'] ){
		json_response("fail", "A node with same name already exists");
	}	

	if( $instance['l']['v'] == "Root" || $instance['_id'] == "T1" ){
		if( !isset($instance['series']) ){
			$new_id = "T2";
			$res5 = $mongodb_con->update_one( $graph_things, ["_id"=>$instance_id ], ["series"=>2] );
		}else{
			$res5 = $mongodb_con->increment( $graph_things, $instance_id, "series", 1 );
			$new_id = "T" . $res5['data']['series'];
		}
		$thing['_id'] = $new_id;
	}else{
		if( !isset($instance['series']) ){
			$new_id = $instance_id."T1";
			$res5 = $mongodb_con->update_one( $graph_things, ["_id"=>$instance_id ], ["series"=>1] );
		}else{
			$res5 = $mongodb_con->increment( $graph_things, $instance_id, "series", 1 );
			$new_id = $instance_id."T" . $res5['data']['series'];
		}
		$thing['_id'] = $new_id;
	}


	$props =[];
	if( isset( $thing['props'] ) ){
		foreach( $thing['props'] as $i=>$j ){
			$k = [];
			if( is_array($j) ){
				for($ii=0;$ii<sizeof($j);$ii++){
					if( isset($j[ $ii ]['t']) && isset($j[ $ii ]['v']) ){
						$k[]=$j;
					}
				}
				if( sizeof($k) ){
					$props[ $i ] = $k;
				}
			}
		}
		$thing['props'] = $props;
	}
	$z_t = [];
	if( isset($thing['z_t']) ){
		foreach( $thing['z_t'] as $i=>$j ){
			if( !isset($j['name']) || !isset($j['type']) ){
				json_response("fail", "Template error: " . $i );
			}
			if( !$j['name']['v'] || !$j['type']['k'] ){
				json_response("fail", "Template error: " . $i );
			}
			$z_t[ $i ] = ['l'=>$j['name'],'t'=>$j['type'],'e'=>false,'m'=>false];
			if( $j['type']['k'] =="GT" ){
				if( !isset($j['i_of']) ){
					json_response("fail", "Template error: " . $i . " Graph instance" );
				}
				if( !$j['i_of']['i'] || !$j['i_of']['v'] ){
					json_response("fail", "Template error: " . $i . " Graph instance" );
				}
				$z_t[ $i ]['i_of'] = $j['i_of'];
			}
		}
		$thing['z_t'] = $z_t;
	}
	$thing['m_i']=date("Y-m-d H:i:s");
	$thing['m_u']=date("Y-m-d H:i:s");
	$res = $mongodb_con->insert( $graph_things, $thing );
	$res2 = $mongodb_con->increment( $graph_things, $instance_id, "cnt", 1 );

	send_to_keywords_queue( $res['inserted_id'] );

	event_log( "objects", "create_with_template", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$res['inserted_id'],
	]);

	json_response($res);

	exit;
}
if( $_POST['action'] == "objects_create_node_on_fly" ){

	if( !isset($_POST['node']) ){
		json_response("fail", "Data missing");
	}
	$thing = $_POST['node'];
	if( !is_array( $thing ) ){
		json_response("fail", "Data missing");
	}
	if( !isset( $thing['l'] ) || !isset( $thing['i_of'] ) ){
		json_response("fail", "Data missing");
	}
	if( !is_array( $thing['i_of'] ) || !is_array( $thing['i_of'] ) ){
		json_response("fail", "Data missing");
	}
	if( !isset( $thing['l']['v'] ) || !$thing['l']['v'] ){
		json_response("fail", "Node name missing");
	}

	$instance_id = $thing['i_of']['i'];
	if( !preg_match("/^[a-z0-9]{2,24}$/i", $instance_id) ){
		json_response("fail", "Instance id incorrect");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$instance_id] );
	if( !$res['data'] ){
		json_response("fail", "Instance node not found");
	}
	$instance = $res['data'];

	$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$instance_id, 'l.v'=>$thing['l']['v']] );
	if( $res['data'] ){
		json_response("fail", "A node with same name already exists");
	}

	if( $instance['l']['v'] == "Root" || $instance['_id'] == "T1" ){
		if( !isset($instance['series']) ){
			$new_id = "T2";
			$res5 = $mongodb_con->update_one( $graph_things, ["_id"=>$instance_id ], ["series"=>2] );
		}else{
			$res5 = $mongodb_con->increment( $graph_things, $instance_id, "series", 1 );
			$new_id = "T" . $res5['data']['series'];
		}
		$thing['_id'] = $new_id;
	}else{
		if( !isset($instance['series']) ){
			$new_id = $instance_id."T1";
			$res5 = $mongodb_con->update_one( $graph_things, ["_id"=>$instance_id ], ["series"=>1] );
		}else{
			$res5 = $mongodb_con->increment( $graph_things, $instance_id, "series", 1 );
			$new_id = $instance_id."T" . $res5['data']['series'];
		}
		$thing['_id'] = $new_id;
	}

	$thing['i_t']=["t"=>"T", "v"=>"N"];
	$thing['m_i']=date("Y-m-d H:i:s");
	$thing['m_u']=date("Y-m-d H:i:s");
	$res = $mongodb_con->insert( $graph_things, $thing );
	$res2 = $mongodb_con->increment( $graph_things, $instance_id, "cnt", 1 );

	send_to_keywords_queue( $res['inserted_id'] );

	event_log( "objects", "create_on_fly", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$res['inserted_id'],
	]);

	$res['label'] = $thing['l']['v'];

	json_response($res);

	exit;
}

function find_or_insert( $instance, $thing_name ){
	global $mongodb_con; global $db_prefix; global $graph_things; global $graph_queue; global $graph_keywords;
	global $data_cache; global $config_param1; global $graph_id;
	if( isset($data_cache[ $instance['i'] . "." . $thing_name ]) ){
		return $data_cache[ $instance['i'] . "." . $thing_name ];
	}
	$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$instance['i'], 'l.v'=>$thing_name], ['projection'=>['_id'=>1] ] );
	if( $res['data'] ){
		$data_cache[ $instance['i'] . "." . $thing_name ] = $res['data']['_id'];
		return $res['data']['_id'];
	}else{

		if( $instance['v'] == "Root" || $instance['i'] == "T1" ){
			$res5 = $mongodb_con->increment( $graph_things, $instance['i'], "series", 1 );
			$new_id = "T" . $res5['data']['series'];
		}else{
			$res5 = $mongodb_con->increment( $graph_things, $instance['i'], "series", 1 );
			$new_id = $instance['i']."T" . $res5['data']['series'];
		}

		$instance['t'] = "GT";
		$res = $mongodb_con->insert( $graph_things, [
			"_id"=>$new_id,
			'i_of'=>$instance, 
			'l'=>['t'=>"T", "v"=>$thing_name],
			'i_t'=>['t'=>"T", "v"=>"N"],
			'm_i'=>date("Y-m-d H:i:s"),
			'm_u'=>date("Y-m-d H:i:s")
		]);
		$node_id = $res['inserted_id'];
		$data_cache[ $instance['i'] . "." . $thing_name ] = $node_id;
		$resinc = $mongodb_con->increment( $graph_things, $instance['i'], "cnt", 1 );
		send_to_keywords_queue( $node_id );
		event_log( "objects", "create", [
			"app_id"=>$config_param1,
			"graph_id"=>$graph_id,
			"object_id"=>$node_id,
		]);
		$data_cache[ $node_id ] = [
			'i_of'=>$instance, 
			'l'=>['t'=>"T", "v"=>$thing_name],
			'i_t'=>['t'=>"T", "v"=>"N"],
			'm_i'=>date("Y-m-d H:i:s"),
			'm_u'=>date("Y-m-d H:i:s")
		];
		return $node_id;
	}
}

if( $_POST['action'] == "objects_import_data_check" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$thing_id = $_POST['object_id'];

	$res = $mongodb_con->find_one( $graph_things, ["_id"=>$thing_id] );
	if( !$res['data'] ){
		json_response("fail", "Instance not found");
	}
	$instance = $res['data'];

	if( !isset($_POST['dataset']) ){
		json_response("fail", "Data missing 1");
	}
	if( $_POST['dataset'] === true ){
		if( $instance['i_t']['v'] != "L" ){
			json_response("fail", "Selected Instance `".$instance['l']['v']."` is not a type of dataset");
		}
		if( !isset($_POST['primary']) ){
			json_response("fail", "Data missing 2");
		}
		//$res = $mongodb_con->

	}else{
		if( $instance['i_t']['v'] != "N" ){
			json_response("fail", "Selected Instance `".$instance['l']['v']."` is not a type of Node");
		}
		if( !isset($_POST['primary']) ){
			json_response("fail", "Data missing 2");
		}
		if( !isset($_POST['label']) ){
			json_response("fail", "Data missing 3");
		}
	}
	if( !isset($_POST['schema']) ){
		json_response("fail", "Data missing 4");
	}

	$token = rand(1,99999999);
	$_SESSION['tokens'][ (string)$token ] = $_POST;
	json_response(["status"=>"success", "token"=>$token]);

	exit;
}


if( $_POST['action'] == "object_import_batch" ){

	if( !isset($_POST['token']) ){
		json_response("fail", "Need Token");
	}
	if( !isset($_SESSION['tokens'][ $_POST['token'] ]) ){
		json_response("fail", "Token not found");
	}
	$token_post = $_SESSION['tokens'][ $_POST['token'] ];

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$thing_id = $_POST['object_id'];

	if( $token_post['object_id'] != $_POST['object_id'] ){
		json_response("fail", "Token mismatch 1");
	}

	$res = $mongodb_con->find_one( $graph_things, ["_id"=>$thing_id] );
	if( !$res['data'] ){
		json_response("fail", "Instance not found");
	}
	$instance = $res['data'];

	//json_response("status", "OK");
	//print_r( $token_post );exit;
	//print_r( $_POST );

	$success = 0; $inserts = 0; $updates = 0; $skipped = 0;

	$data_cache = [];

	if( $token_post['dataset'] == false ){

		foreach( $_POST['data'] as $i=>$j ){

			if( !isset($j['l']) ){
				json_response([
					'status'=>"fail", 
					"error"=>"Label required for all records",
					"success"=>$success,
					"skipped"=>$skipped,
					"inserts"=>$inserts,
					"updates"=>$updates
				]);
			}
			if( !is_array($j['l']) ){
				json_response([
					'status'=>"fail", 
					"error"=>"Label required for all records",
					"success"=>$success,
					"skipped"=>$skipped,
					"inserts"=>$inserts,
					"updates"=>$updates
				]);
			}
			if( !isset($j['l']['t']) || !isset($j['l']['v']) ){
				json_response([
					'status'=>"fail", 
					"error"=>"Label required for all records",
					"success"=>$success,
					"skipped"=>$skipped,
					"inserts"=>$inserts,
					"updates"=>$updates
				]);
			}
			if( !$j['l']['v'] ){
				json_response([
					'status'=>"fail", 
					"error"=>"Label required for all records",
					"success"=>$success,
					"skipped"=>$skipped,
					"inserts"=>$inserts,
					"updates"=>$updates
				]);
			}
			if( isset($j['l']['i_of']) ){
				if( !isset($j['l']['i_of']['i']) || !isset($j['l']['i_of']['v']) ){
					json_response("fail", "Label instance missing");
					json_response([
						'status'=>"fail", 
						"error"=>"Label instance missing for: " . $j['l']['v'],
						"success"=>$success,
						"skipped"=>$skipped,
						"inserts"=>$inserts,
						"updates"=>$updates
					]);
				}
				if( !$data_cache[ $j['l']['i_of']['i'] ] ){
					$res2 = $mongodb_con->find_one( $graph_things, ["_id"=>$j['l']['i_of']['i']] );
					if( $res2['data'] ){
						$data_cache[ $j['l']['i_of']['i'] ] = $res2['data'];
					}else{
						json_response("fail", "Label instance not found");
						json_response([
							'status'=>"fail", 
							"error"=>"Label instance missing for: " . $j['l']['v'],
							"success"=>$success,
							"skipped"=>$skipped,
							"inserts"=>$inserts,
							"updates"=>$updates
						]);
					}
				}else{

				}
			}

			$al = false;
			if( $j['al'] ){
				$al = $j['al'];
			}
			$res2 = $mongodb_con->find_one( $graph_things, ["i_of.i"=>$thing_id, "l.v"=>$j['l']['v'] ] );
			if( $res2['data'] ){
				$id = $res2['data']['_id'];
				$update = [];
				if( $al ){
					$update[ "al" ] = $al;
				}
				foreach( $j['props'] as $prop=>$propd ){
					if( isset($propd['i_of']) ){
						$node_id = find_or_insert( $propd['i_of'], $propd['v'] );
						$propd = ["t"=>"GT", "v"=>$propd["v"], "i"=>$node_id ];
					}
					$update[ "props." . $prop ] = [ $propd ];
				}
				$mongodb_con->update_one( $graph_things, ["_id"=>$id], $update);
				$updates++;
				$success++;
				event_log( "objects", "update", [
					"app_id"=>$config_param1,
					"graph_id"=>$graph_id,
					"object_id"=>$id,
				]);
				send_to_keywords_queue( $id );
			}else{
				$res5 = $mongodb_con->increment( $graph_things, $instance['_id'], "series", 1 );
				$new_id = $instance['_id']."T" . $res5['data']['series'];
				$l = [
					"t"=>"T",
					"v"=>$j['l']['v'],
				];
				if( isset($j['l']['i_of']) ){
					$node_id = find_or_insert( $j['l']['i_of'], $j['l']['v'] );
					$l['t'] = "GT"; 
					$l['i'] = $node_id;
					unset($l['i_of']);
				}
				$insert = [
					"_id"=>$new_id,
					"l"=>$l,
					"i_of"=>["t"=>"GT", "i"=>$instance['_id'], "v"=>$instance['l']['v']],
					'i_t'=>['t'=>'T','v'=>"N"],
					"m_i"=>date("Y-m-d H:i:s"),
					"m_u"=>date("Y-m-d H:i:s"),
					"props"=>[]
				];
				if( $al ){
					$insert[ "al" ] = $al;
				}
				foreach( $j['props'] as $prop=>$propd ){
					if( isset($propd['i_of']) ){
						$node_id = find_or_insert( $propd['i_of'], $propd['v'] );
						$propd = ["t"=>"GT", "v"=>$propd["v"], "i"=>$node_id ];
					}
					$insert[ "props" ][ $prop ] = [ $propd ];
				}
				$insert['m_i'] = date("Y-m-d H:i:s");
				$res5 = $mongodb_con->insert( $graph_things, $insert );
				$res2 = $mongodb_con->increment( $graph_things, $instance["_id"], "cnt", 1 );
				$inserts++;
				$success++;
				event_log( "objects", "create", [
					"app_id"=>$config_param1,
					"graph_id"=>$graph_id,
					"object_id"=>$new_id,
				]);
				send_to_keywords_queue( $new_id );
			}
		}
	}else{

		$graph_things_dataset = $graph_things . "_" . $thing_id;

		// $res5 = $mongodb_con->get_max_numeric_id( $graph_things_dataset );
		// $max_id = $res5['data'];
		foreach( $_POST['data'] as $i=>$j ){
			$id = uniqid();
			$res2 = $mongodb_con->find_one( $graph_things_dataset, ["_id"=>$id] );
			if( $res2['data'] ){
				$update = [];
				foreach( $j['props'] as $prop=>$propd ){
					if( isset($propd['i_of']) ){
						$node_id = find_or_insert( $propd['i_of'], $propd['v'] );
						$propd = ["t"=>"GT", "v"=>$propd["v"], "i"=>$node_id ];
					}
					$update[ "props." . $prop ] = [ $propd ];
				}
				$mongodb_con->update_one( $graph_things_dataset, ["_id"=>$id], $update);
				send_to_records_queue( $thing_id, $id, "record_update" );
				$updates++;
				$success++;
			}else{
				$insert = [
					"_id"=>$id,
					"m_i"=>date("Y-m-d H:i:s"),
					"m_u"=>date("Y-m-d H:i:s"),
					"props"=>[]
				];
				foreach( $j['props'] as $prop=>$propd ){
					if( isset($propd['i_of']) ){
						$node_id = find_or_insert( $propd['i_of'], $propd['v'] );
						$propd = ["t"=>"GT", "v"=>$propd["v"], "i"=>$node_id ];
					}
					$insert[ "props" ][ $prop ] = [ $propd ];
				}
				$res5 = $mongodb_con->insert( $graph_things_dataset, $insert );
				$res2 = $mongodb_con->increment( $graph_things, $instance["_id"], "cnt", 1 );
				send_to_records_queue( $thing_id, $id, "record_update" );
				$inserts++;
				$success++;
			}
		}
	}
	json_response([
		'status'=>"success", 
		"success"=>$success,
		"skipped"=>$skipped,
		"inserts"=>$inserts,
		"updates"=>$updates
	]);
	exit;
}

if( $_POST['action'] == "objects_dataset_record_delete" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$thing_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$thing_id] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}

	if( !isset($_POST['record_id']) ){
		json_response("fail", "Need Record id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['record_id']) && !preg_match("/^[0-9]+$/i", $_POST['record_id']) ){
		json_response("fail", "Record id incorrect");
	}
	$record_id = $_POST['record_id'];

	$res = $mongodb_con->find_one( $graph_things . "_" . $thing_id, ['_id'=>$record_id] );
	if( !$res['data'] ){
		json_response("fail", "Record not found");
	}

	$res = $mongodb_con->delete_one( $graph_things . "_" . $thing_id, ['_id'=>$record_id] );

	send_to_records_queue( $thing_id, $record_id, "record_delete" );

	json_response($res);

	exit;
}

if( $_POST['action'] == "objects_records_empty" ){
	if( !isset($_POST['instance_id']) ){
		json_response("fail", "Need Instance id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['instance_id']) && !preg_match("/^[0-9]+$/i", $_POST['instance_id']) ){
		json_response("fail", "Instance id incorrect");
	}
	$instance_id = $_POST['instance_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$instance_id] );
	if( !$res['data'] ){
		json_response("fail", "Instance not found");
	}

	while( 1 ){
		$res = $mongodb_con->find( $graph_things . "_". $instance_id, [], ['limit'=>500, 'sort'=>['_id'=>1]] );
		if( sizeof($res['data']) == 0 ){
			break;
		}
		foreach( $res['data'] as $i=>$j ){
			$mongodb_con->delete_one( $graph_things . "_". $instance_id, ["_id"=>$j['_id']] );
			send_to_records_queue( $instance_id, $j['_id'], "record_delete" );
		}
	}

	$mongodb_con->update_one( $graph_things, ["_id"=> $instance_id], ["cnt"=>0] );

	json_response("success");

	exit;
}

if( $_POST['action'] == "objects_nodes_empty" ){
	if( !isset($_POST['instance_id']) ){
		json_response("fail", "Need Instance id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['instance_id']) && !preg_match("/^[0-9]+$/i", $_POST['instance_id']) ){
		json_response("fail", "Instance id incorrect");
	}
	$instance_id = $_POST['instance_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$instance_id] );
	if( !$res['data'] ){
		json_response("fail", "Instance not found");
	}

	while( 1 ){
		$res = $mongodb_con->find( $graph_things, ['i_of.i'=>$instance_id], ['limit'=>500] );
		if( sizeof($res['data']) == 0 ){
			break;
		}
		foreach( $res['data'] as $i=>$j ){
			$mongodb_con->delete_one( $graph_things,["i_of.i"=>$instance_id, "_id"=>$j['_id']] );
			send_to_keywords_delete_queue($j['_id']);
			event_log( "objects", "delete", [
				"app_id"=>$config_param1,
				"graph_id"=>$graph_id,
				"object_id"=>$j['_id'],
			]);
		}
	}
	$mongodb_con->update_one( $graph_things, ["_id"=> $instance_id], ["cnt"=>0] );

	json_response("success");

	exit;
}

if( $_POST['action'] == "objects_delete_node" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Object id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Object id incorrect");
	}
	$object_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$object_id] );
	if( !$res['data'] ){
		json_response("fail", "Object not found");
	}
	$thing = $res['data'];
	$instance_id = $thing['i_of']['i'];

	// if( $thing['i_t'] != "N" ){
	// 	json_response("fail", "Incorrect node type");
	// }
	if( $thing['cnt'] > 0 ){
		json_response("fail", "There are nested ".$thing['cnt']." nodes under ". $thing['l']['v']);
	}

	$mongodb_con->delete_one( $graph_things,[
		"_id"=>$object_id
	]);
	send_to_keywords_delete_queue($object_id);
	event_log( "objects", "delete", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$object_id,
	]);
	$mongodb_con->increment( $graph_things, $instance_id, "cnt", -1 );
	json_response("success");

	exit;
}

if( $_POST['action'] == "objects_ops_convert_to_dataset" ){
	if( !isset($_POST['source_object_id']) ){
		json_response("fail", "Need Object id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['source_object_id']) && !preg_match("/^[0-9]+$/i", $_POST['source_object_id']) ){
		json_response("fail", "Object id incorrect");
	}
	$object_id = $_POST['source_object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$object_id] );
	if( !$res['data'] ){
		json_response("fail", "Object not found");
	}
	$thing = $res['data'];
	$instance_id = $thing['i_of']['i'];

	if( $thing['i_t']['v'] == "L" ){
		json_response("fail", "Object is already a dataset" );
	}else if( $thing['i_t']['v'] != "N" ){
		json_response("fail", "Source Object must be type of Node" );
	}

	if( !isset($_POST['label_to']) ){
		json_response("fail", "Need new Label Property id");
	}else if( !preg_match("/^[a-z0-9\.\,\-\_\ ]{2,100}$/i", $_POST['label_to']) ){
		json_response("fail", "Property name should be plain text");
	}

	$new_prop = trim($_POST['label_to']);
	foreach( $thing['z_t'] as $propf=>$p ){
		if( strtolower($new_prop) == strtolower($p['l']['v']) ){
			json_response("fail", "Property name `".$new_prop."` already exists!" );
		}
	}

	$z_n = ($thing['z_n']??1);
	$z_n++;
	$np = "p" . $z_n;
	while( 1 ){
		if( isset($thing['z_t'][ $np ]) ){
			$z_n++;
			$np = "p" . $z_n;
		}else{
			break;
		}
	}

	$z_o = $thing['z_o'];
	array_splice($z_o, 0, 0, $np);
	$z_n = $z_n+1;

	$ures = $mongodb_con->update_one( $graph_things, ["_id"=>$object_id], [
		'i_t.v'=>"L",
		"z_t.". $np=> ["key"=> $np, "l"=> ["t"=>"T", "v"=> $new_prop], "t"=> ["t"=>"KV", "k"=>"T", "v"=>"text"], "m"=> ["t"=>"B", "v"=> "true"] ],
		"z_o"=> $z_o,
		"z_n"=> $z_n
	]);

	$graph_things_dataset = $graph_things . "_" . $object_id;

	$rec_cnt = 0;
	while( 1 ){
		$res = $mongodb_con->find( $graph_things, ['i_of.i'=>$object_id], ['limit'=>100] );
		if( !$res['data'] || sizeof($res['data']) == 0 ){
			break;
		}
		foreach( $res['data'] as $i=>$j ){
			$rec_cnt++;
			$id = $j['_id'];
			$j[ "props" ][ $np ] = [$j['l']];
			$rec_id = uniqid();
			$ires = $mongodb_con->insert( $graph_things_dataset, [
				"_id"=>$rec_id,
				"props"=>$j['props'],
				"m_i"=>$j['m_i'],
				"m_u"=>$j['m_u'],
			]);
			if( $ires['inserted_id'] ){
				send_to_records_queue( $object_id, $rec_id, "record_create" );
				$mongodb_con->delete_one( $graph_things, ['_id'=>$id]);
				send_to_keywords_delete_queue($id);
				event_log( "objects", "delete", [
					"app_id" => $config_param1,
					"graph_id" => $graph_id,
					"object_id" => $id,
				]);
			}
		}
	}

	if( $thing['cnt'] != $rec_cnt ){
		$mongodb_con->update_one( $graph_things, ['_id'=>$object_id], ['cnt'=>$rec_cnt] );
	}

	event_log( "objects", "convert", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$object_id,
		"from"=>"N",
		"to"=>"L",
	]);
	json_response("success");

	exit;
}

if( $_POST['action'] == "objects_ops_convert_to_nodelist" ){

	if( !isset($_POST['source_object_id']) ){
		json_response("fail", "Need Object id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['source_object_id']) && !preg_match("/^[0-9]+$/i", $_POST['source_object_id']) ){
		json_response("fail", "Object id incorrect");
	}
	$object_id = $_POST['source_object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$object_id] );
	if( !$res['data'] ){
		json_response("fail", "Object not found");
	}
	$thing = $res['data'];
	$instance_id = $thing['i_of']['i'];

	if( $thing['i_t']['v'] == "N" ){
		json_response("fail", "Object is already a node list" );
	}else if( $thing['i_t']['v'] != "L" ){
		json_response("fail", "Source Object must be type of Dataset" );
	}

	if( !isset($_POST['primary_field']) ){
		json_response("fail", "Need NodeID field");
	}
	if( !isset( $thing['z_t'][ $_POST['primary_field'] ] ) && $_POST['primary_field'] != "default-id" ){
		json_response("fail", "Primary field not found");
	}
	if( !isset($_POST['label_field']) ){
		json_response("fail", "Need Label field");
	}
	if( !isset( $thing['z_t'][ $_POST['label_field'] ] ) ){
		json_response("fail", "Label field not found");
	}
	if( isset($_POST['alias_field']) && sizeof($_POST['alias_field']) > 1 ){
		json_response("fail", "One alias field is expected");
	}
	if( isset($_POST['alias_field']) && sizeof($_POST['alias_field']) > 0 ){
		if( !isset( $thing['z_t'][ $_POST['alias_field'][0] ] ) ){
			json_response("fail", "Alias field not found");
		}
	}

	$graph_things_dataset = $graph_things . "_" . $object_id;

	if( $_POST['primary_field'] != "default-id" ){
		$res = $mongodb_con->aggregate( $graph_things_dataset, [
			['$group'=>['_id'=>'$props.'.$_POST['primary_field'].".v", 'cnt'=>['$sum'=>1]] ],
			['$sort'=>["cnt"=>-1]]
		]);
		print_r( $res );exit;
		if( $res['status'] != "success" ){
			json_response($res);
		}
		if( isset($res['data']) && sizeof($res['data']) > 0 ){
			if( $res['data'][0]['cnt'] > 1 ){
				json_response("fail", "Primary field value `" . $res['data'][0]['_id'][0] . "` repeated. " );
			}
		}else{
			json_response("fail", "Primary values not found");
		}
		foreach( $res['data'] as $i=>$j ){
			if( !preg_match( "/^[a-z0-9]{2,24}$/i", $j['_id'][0] ) ){
				json_response("fail", "Primary field value " . $j['_id'][0] . " is not acceptable");
			}
		}
	}

	//echo "xxxx";exit;

	$res = $mongodb_con->aggregate( $graph_things_dataset, [
		['$group'=>['_id'=>'$props.'.$_POST['label_field'].".v", 'cnt'=>['$sum'=>1]] ],
		['$sort'=>["cnt"=>-1]]
	]);
	//json_response($res);
	if( $res['status'] != "success" ){
		json_response($res);
	}
	if( isset($res['data']) && sizeof($res['data']) > 0 ){
		if( $res['data'][0]['cnt'] > 1 ){
			json_response("fail", "Label field `" . $res['data'][0]['_id'][0] . "` repeated. " );
		}
	}else{
		json_response("fail", "Label values not found");
	}
	foreach( $res['data'] as $i=>$j ){
		if( !preg_match( "/^[a-z][a-z0-9\-\.\_\,\ \(\)\@\!\&\:]{1,200}$/i", $j['_id'][0] ) ){
			json_response("fail", "Label field value " . $j['_id'][0] . " is not acceptable");
		}
	}

	$rec_cnt = 0;$success = 0;$failed = 0; $failed_reasons = [];
	while( 1 ){
		$res = $mongodb_con->find( $graph_things_dataset, [], ['limit'=>100] );
		if( !$res['data'] || sizeof($res['data']) == 0 ){
			break;
		}
		//print_r( $res );
		foreach( $res['data'] as $i=>$j ){
			$rec_cnt++;
			if( $_POST['primary_field'] =="default-id" ){
				$res5 = $mongodb_con->increment( $graph_things, $object_id, "series", 1 );
				$new_id = $object_id."T" . $res5['data']['series'];
				$rec_id = $new_id;
			}else{
				$rec_id = $j[ "props" ][ $_POST['primary_field'] ][0]['v'];
			}
			$al = [];
			if( sizeof($_POST['alias_field']) ){
				if( isset($j[ "props" ][ $_POST['alias_field'][0] ]) ){
					$al[] = $j[ "props" ][ $_POST['alias_field'][0] ][0];
				}
			}
			$d = [
				"_id"=>$rec_id,
				"i_t"=>["t"=>"T", "v"=>"N"],
				"l"=>$j[ "props" ][ $_POST['label_field'] ][0],
				"i_of"=>["t"=>"GT", "i"=>$object_id, "v"=>$thing['l']['v']],
				"props"=>$j['props'],
				"m_i"=>$j['m_i'],
				"m_u"=>$j['m_u'],
			];
			if( sizeof($al) ){
				$d['al'] = $al;
			}
			//print_r( $d );exit;
			$ires = $mongodb_con->insert( $graph_things, $d);
			if( $ires['status'] == "success" ){
				if( $ires['inserted_id'] ){
					$mongodb_con->delete_one( $graph_things_dataset, ['_id'=>$j['_id']] );
					send_to_records_queue( $object_id, $j['_id'], "record_delete" );
					send_to_keywords_queue( $rec_id );
					event_log( "objects", "create", [
						"app_id" => $config_param1,
						"graph_id" => $graph_id,
						"object_id" => $rec_id,
					]);
					$success++;
				}else{
					json_response( $ires );
				}
			}else{
				$failed++;
				$failed_reasons[ $j['_id'] ] = $ires['error'];
				$mongodb_con->delete_one( $graph_things_dataset, ['_id'=>$j['_id']] );
				send_to_records_queue( $object_id, $j['_id'], "record_delete" );
			}
		}
	}

	//exit;
	$ures = $mongodb_con->update_one( $graph_things, ["_id"=>$object_id], [
		'i_t.v'=>"N",
	]);

	if( $thing['cnt'] != $rec_cnt ){
		$mongodb_con->update_one( $graph_things, ['_id'=>$object_id], ['cnt'=>$rec_cnt] );
	}

	event_log( "objects", "convert", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
		"object_id"=>$object_id,
		"from"=>"L",
		"to"=>"N",
	]);
	json_response([
		"status"=>"success",
		"success"=>$success,
		"failed"=>$failed,
		"failed_reasons"=>$failed_reasons
	]);

	exit;
}