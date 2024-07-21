<?php

function send_to_keywords_queue($object_id){
	global $mongodb_con;global $db_prefix;global $graph_queue;global $graph_id;
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
			$res = $mongodb_con->find( $graph_keywords, $cond, [
				"sort"=>$sort, 
				"limit"=>100,
			]);
			//print_r( $res );
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
				$things[] = $j;
			}
			if( sizeof($things) < 20 ){
				$res = $mongodb_con->find( $graph_things, [], [
					"sort"=>['_id'=>1], 
					"projection"=>['l'=>true, 'i'=>true,'i_of'=>true,'i'=>'$_id', '_id'=>false],
					"limit"=>100,
				]);
				foreach( $res['data'] as $i=>$j ){
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
		'projection'=>['i'=>'$_id', '_id'=>false, 'l'=>1,'i_of'=>1], 
		'sort'=>['cnt'=>-1], 
		'limit'=>500
	]);
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
	}
	json_response($res);
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
		'projection'=>['l'=>1,'props'=>1,'i_of'=>1],
		'sort'=>['l.v'=>1],
		'limit'=>100,
	]);
	$res['cnt'] = $cnt;
	json_response($res);
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

	json_response($res);

	exit;
}

if( $_POST['action'] == "object_create_object" ){

	if( !isset($_POST['data']['l']['v']) ){
		json_response("fail", "Need Label");
	}else if( !preg_match("/^[a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}$/i", $_POST['data']['l']['v']) ){
		json_response("fail", "Need Label in simple format");
	}

	if( !isset($_POST['data']['i_of']['i']) ){
		json_response("fail", "Need Instance");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['data']['i_of']['i']) ){
		json_response("fail", "Instance ID Incorrect format");
	}

	if( !isset($_POST['data']['i_of']['v']) ){
		json_response("fail", "Need Instance Label");
	}else if( !preg_match("/^[a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}$/i", $_POST['data']['i_of']['v']) ){
		json_response("fail", "Need Instance Label in simple format");
	}

	$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$_POST['data']['i_of']['i'], 'l'=>$_POST['data']['l']] );
	if( $res['data'] ){
		json_response("fail", "Duplicate Node");
	}

	$id = uniqid();
	$v = [
		"_id"=>$id,
		"l"=>$_POST['data']['l'],
		"i_of"=>$_POST['data']['i_of'],
	];
	$res = $mongodb_con->insert( $graph_things, $v );
	if( $res['status'] == "fail" ){
		if( preg_match("/duplicate/i", $res['error']) && preg_match("/_id/", $res['error']) ){
			json_response(["status"=>"fail","error"=>"Duplicate primary key"]);
		}else{
			json_response($res);
		}
	}
	send_to_keywords_queue($id);
	$res2 = $mongodb_con->increment( $graph_things, $_POST['data']['i_of']['i'], "cnt", 1 );
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
	json_response( $res );
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
						if( isset( $pd[ $fd ] ) ){
							if( isset($pd[ $fd ]['t']) && isset($pd[ $fd ]['v']) ){
								if( $pd[ $fd ]['v'] ){
									$f = true;
								}
							}else{
								json_response("fail", "Property `" . $field . "` item: ".($pi+1)." property: " . $fn['l']['v'] . " has invalid value: ".json_encode($pd[$fd]));
							}
						}
					}
					if( $f == false ){
						array_slice( $props[ $field ], $pi, 1);
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
	json_response($res);
	exit;
}

if( $_POST['action'] == "objects_save_object" ){

	if( !isset($_POST['data']) ){
		json_response("fail", "Data missing");
	}else if( !is_array($_POST['data']) ){
		json_response("fail", "Data missing");
	}

	if( !isset($_POST['data']['_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['data']['_id']) && !preg_match("/^[0-9]+$/i", $_POST['data']['_id']) ){
		json_response("fail", "Thing id incorrect");
	}

	$data = $_POST['data'];
	$thing_id = $data['_id'];
	unset($data['_id']);

	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$thing_id] );
	if( !$res['data'] ){
		json_response("fail", "thing not found");
	}

	if( !isset($data['l']) ){
		json_response("fail", "Need Label");
	}else if( !preg_match("/^[a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}$/i", $data['l']['v']) ){
		json_response("fail", "Need Label in simple format");
	}

	if( !isset($data['i_of']) ){
		json_response("fail", "Need Instance");
	}else if( !is_array($data['i_of']) ){
		json_response("fail", "Instance should be array");
	}
	if( !isset($data['i_of']['v']) ){
		json_response("fail", "Need Instance Label");
	}else if( !preg_match("/^[a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}$/i", $data['i_of']['v']) ){
		json_response("fail", "Need Instance Label in simple format");
	}
	if( !isset($data['i_of']['i']) ){
		json_response("fail", "Need Instance Id");
	}else if( !preg_match("/^[0-9]+$/i", $data['i_of']['i']) ){
		json_response("fail", "Need Instance Id in simple format");
	}
	unset($data['i_of']['z_t']);unset($data['i_of']['z_o']);

	$res = $mongodb_con->find_one( $graph_things, ['_id'=>['$ne'=>$thing_id], 'i_of.i'=>$data['i_of']['i'], 'l.v'=>$data['l']['v']] );
	if( $res['data'] ){
		json_response("fail", "Duplicate Node exists (". $res['data']['_id'].")");
	}
	if( !isset($data['props']) ){
		json_response("fail", "Need Props");
	}else if( !is_array($data['props']) ){
		json_response("fail", "Props incorrect format");
	}

	//exit;
	foreach( $data['props'] as $field=>$prop ){
		if( !is_array($prop) ){
			json_response("fail", "Property `" . $field . "` has invalid value");
		}
		foreach( $prop as $pi=>$pd ){
			if( isset($pd['t']) && isset($pd['v']) ){

			}else{
				json_response("fail", "Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd));
			}
		}
	}
	//print_r( $data );

	$data['updated'] = date("Y-m-d H:i:s");

	$res = $mongodb_con->update_one( $graph_things,['_id'=>$thing_id], $data );
	json_response($res);
	exit;
}

