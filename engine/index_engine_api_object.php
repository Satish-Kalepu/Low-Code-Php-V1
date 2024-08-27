<?php

function send_to_keywords_queue( $graph_id, $object_id ){
	global $mongodb_con;global $db_prefix;
	$graph_queue = $db_prefix . "_zd_queue_graph_" . $graph_id;
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
function send_to_keywords_delete_queue($graph_id, $object_id){
	global $mongodb_con;global $db_prefix;
	$graph_queue = $db_prefix . "_zd_queue_graph_" . $graph_id;
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
function send_to_records_queue($graph_id,  $object_id, $record_id, $action ){
	global $mongodb_con;global $db_prefix;
	$graph_queue = $db_prefix . "_zd_queue_graph_" . $graph_id;
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

// $graph_things = $db_prefix . "_graph_" . $_POST['graph_id'] . "_things";
// $graph_links = $db_prefix . "_graph_" . $_POST['graph_id'] . "_links";
// $graph_keywords = $db_prefix . "_graph_" . $_POST['graph_id'] . "_keywords";
// $graph_queue = $db_prefix . "_zd_queue_graph_" . $_POST['graph_id'];
// $graph_log = $db_prefix . "_zlog_graph_" . $_POST['graph_id'];

function engine_api_table_dynamic( $graph_db, $action, $post ){

	global $mongodb_con;
	global $app_id;
	global $db_prefix;

	$graph_id = $graph_db['_id'];
	$graph_things = $db_prefix . "_graph_" . $graph_id . "_things";
	$graph_keywords = $db_prefix . "_graph_" . $graph_id . "_keywords";	

		/*
		$j['apis']['objectCreateWithTemplate'] = [
			"action"=> "objectCreateWithTemplate",
			"object_id"=> "",
			"record_id"=> "",
			"properties"=> [
				"p1"=> [["t"=>"T","v"=>""]]
			],
			"template"=> [
				"z_t"=>[
				],
				"z_o"=>[],
				"z_n"=>1
			]
		];
		$j['apis']['objectLabelUpdate'] = [
			"action"=> "objectLabelUpdate",
			"object_id"=> "",
			"label"=>["t"=>"T", "v"=>""],
		];
		$j['apis']['objectTypeUpdate'] = [
			"action"=> "objectTypeUpdate",
			"object_id"=> "",
			"type"=>["t"=>"T", "v"=>"N"],
		];
		$j['apis']['objectAliasUpdate'] = [
			"action"=> "objectAliasUpdate",
			"object_id"=> "",
			"alias"=>[
				["t"=>"T", "v"=>"N"]
			]
		];
		$j['apis']['objectInstanceUpdate'] = [
			"action"=> "objectInstanceUpdate",
			"object_id"=> "",
			"instance_of"=>[
				["t"=>"GT", "v"=>"", "i"=>""]
			]
		];
		$j['apis']['objectPropertiesUpdate'] = [
			"action"=> "objectPropertiesUpdate",
			"object_id"=> "",
			"properties"=> [
				"p1"=> [["t"=>"T","v"=>""]]
			]
		];
		$j['apis']['objectNodesTruncate'] = [
			"action"=> "objectNodesTruncate",
			"object_id"=> "",
		];
		$j['apis']['objectDelete'] = [
			"action"=> "objectDelete",
			"object_id"=> "",
		];
		$j['apis']['objectConverToDataset'] = [
			"action"=> "objectConverToDataset",
			"object_id"=> "",
		];
		$j['apis']['objectConverToNode'] = [
			"action"=> "objectConverToNode",
			"object_id"=> "",
		];

		$j['apis']['objectTemplateFieldCreate'] = [
			"action"=> "objectTemplateFieldCreate",
			"object_id"=> "",
			"field"=>["t"=>"T", "v"=>"N"],
			"config"=>[
				"l"=> ["t"=> "T", "v"=> "Description"],
				"t"=> ["t"=> "KV", "v"=> "Text", "k"=> "T"],
				"m"=> ["t"=> "B", "v"=> "false"]
			]
		];
		$j['apis']['objectTemplateFieldUpdate'] = [
			"action"=> "objectTemplateFieldUpdate",
			"object_id"=> "",
			"field"=>["t"=>"T", "v"=>"N"],
			"config"=>[
				"l"=> ["t"=> "T", "v"=> "Description"],
				"t"=> ["t"=> "KV", "v"=> "Text", "k"=> "T"],
				"m"=> ["t"=> "B", "v"=> "false"]
			]
		];
		$j['apis']['objectTemplateFieldDelete'] = [
			"action"=> "objectTemplateFieldDelete",
			"object_id"=> "",
			"field"=>"",
		];
		$j['apis']['objectTemplateEnable'] = [
			"action"=> "objectTemplateEnable",
			"object_id"=> "",
		];
		$j['apis']['objectTemplateOrderUpdate'] = [
			"action"=> "objectTemplateOrderUpdate",
			"object_id"=> "",
			"order"=> ["p1", "p2"],
		];
		$j['apis']['dataSetRecordCreate'] = [
			"action"=> "dataSetRecordCreate",
			"object_id"=> "",
			"record_id"=> "",
			"properties"=> [
				"p1"=> [["t"=>"T","v"=>""]]
			]
		];
		$j['apis']['dataSetRecordUpdate'] = [
			"action"=> "dataSetRecordUpdate",
			"object_id"=> "",
			"record_id"=> "",
			"properties"=> [
				"p1"=> [["t"=>"T","v"=>""]]
			]
		];
		$j['apis']['dataSetRecordDelete'] = [
			"action"=> "dataSetRecordDelete",
			"object_id"=> "",
			"record_id"=> "",
		];
		$j['apis']['dataSetTruncate'] = [
			"action"=> "dataSetTruncate",
			"object_id"=> "",
		];
		$j['apis']['keywordSearch'] = [
			"action"=> "keywordSearch",
			"keyword"=> "value"
		];
		*/


	if( $action == "listObjects" ){

		$cond = [];
		if( $post['sort'] == "label" ){
			if( $post['order'] == "asc" ){
				$sort = ['l.v'=>1];
				if( $post['from'] ){
					$cond['l.v'] = ['$gte'=> $post['from']];
				}
				if( $post['last'] ){
					$cond['l.v'] = ['$gte'=> $post['last']];
				}
			}else{
				$sort = ['l.v'=>-1];
				if( $post['from'] ){
					$cond['l.v'] = ['$lte'=> $post['from']];
				}
				if( $post['last'] ){
					$cond['l.v'] = ['$lte'=> $post['last']];
				}
			}
		}else if( $post['sort'] == "ID" ){
			if( $post['order'] == "asc" ){
				$sort = ['_id'=>1];
				if( $post['from'] ){
					$cond['_id'] = ['$gte'=> $post['from']];
				}
				if( $post['last'] ){
					$cond['_id'] = ['$gte'=> $post['last']];
				}
			}else{
				$sort = ['_id'=>-1];
				if( $post['from'] ){
					$cond['_id'] = ['$lte'=> $post['from']];
				}
				if( $post['last'] ){
					$cond['_id'] = ['$lte'=> $post['last']];
				}
			}
		}else if( $post['sort'] == "nodes" ){
			$cond['cnt'] = ['$gt'=>1];
			if( $post['order'] == "asc" ){
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
		return json_response(200,[
			"status"=>"success", "data"=>$res['data'], "query"=>$cond
		]);
	}else if( $action == "getObject" ){
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$ops = [

		];
		$res = $mongodb_con->find_one( $graph_things, ["_id"=>$post['object_id']], $ops );
		if( $res['status'] != "success" ){
			return json_response(500,[
				"status"=>"success", "data"=>$res['data'], "query"=>$cond
			]);
		}
		if( $res['data'] ){
			return json_response(200,[
				"status"=>"success", "data"=>$res['data']
			]);
		}else{
			return json_response(404,[
				"status"=>"fail", "error"=>"Object not found"
			]);
		}

	}else if( $action == "getObjectTemplate" ){
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$ops = [
			'projection'=>['z_t'=>1,'z_o'=>1, 'z_n'=>1]
		];
		$res = $mongodb_con->find_one( $graph_things, ["_id"=>$post['object_id']], $ops );
		if( $res['status'] != "success" ){
			return json_response(500,$res);
		}
		if( $res['data'] ){
			return json_response(200,[
				"status"=>"success", "data"=>$res['data']
			]);
		}else{
			return json_response(404,[
				"status"=>"fail", "error"=>"Object not found"
			]);
		}

	}else if( $action == "getObjectRecords" ){
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}
		$graph_things_dataset = $graph_things . "_". $post['object_id'];

		if( $res['data']['i_t']['v'] != "L" ){
			return json_response(404,["status"=>"fail", "error"=>"Object is not of type DataSet"]);
		}
		{
			$cond = [];
			$res = $mongodb_con->count( $graph_things_dataset, $cond );
			$cnt = (int)$res['data'];
			$sort = [];
			if( $post['sort'] == "_id" ){
				if( $post['order'] == "Asc" ){
					$sort['_id'] = 1;
					if( $post['from'] ){$cond['_id'] = ['$gte'=> $post['from']];}
					if( $post['last'] ){$cond['_id'] = ['$gt'=> $post['last']];}
				}else{
					$sort['_id'] = -1;
					if( $post['from'] ){$cond['_id'] = ['$lte'=> $post['from']];}
					if( $post['last'] ){$cond['_id'] = ['$lt'=> $post['last']];}
				}
			}else{
				if( $post['order'] == "Asc" ){
					$sort = [ "props.".$post['sort'].".v" => 1, "_id"=>1 ];
					if( $post['from'] ){$cond["props.".$post['sort'].".v"] = ['$gte'=> $post['from']];}
					if( $post['last'] ){$cond["props.".$post['sort'].".v"] = ['$gt'=> $post['last']];}
				}else{
					$sort = [ "props.".$post['sort'].".v" => -1, "_id"=>1 ];
					if( $post['from'] ){$cond["props.".$post['sort'].".v"] = ['$lte'=> $post['from']];}
					if( $post['last'] ){$cond["props.".$post['sort'].".v"] = ['$lt'=> $post['last']];}
				}
			}
			$ops = ['='=>'$eq','!='=>'$ne', '>'=>'$le', '>='=>'$leq', '<'=>'$ge', '<='=>'$geq'];
			if( isset($post['cond']) ){
				foreach( $post['cond'] as $i=>$j ){
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
			return json_response(200,$res);
		}
	}else if( $action == "getObjectNodes" ){
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}
		if( $res['data']['i_t']['v'] != "N" ){
			return json_response(404, ["status"=>"fail", "error"=>"Object is not of type Node"]);
		}
		{
			$cond = ['i_of.i'=>$post['object_id']];
			$res = $mongodb_con->count( $graph_things, $cond );
			$cnt = (int)$res['data'];
			if( $post['from'] ){
				$cond['l.v'] = ['$gt'=> $post['from']];
			}
			if( $post['last'] ){
				$cond['l.v'] = ['$gt'=> $post['last']];
			}
			$res = $mongodb_con->find( $graph_things, $cond, [
				'projection'=>['l'=>1,'props'=>1,'i_of'=>1,'m_u'=>1],
				'sort'=>['l.v'=>1],
				'limit'=>100,
			]);
			$res['cnt'] = $cnt;
			$res['cnt'] = $cnt;
			$res['cond'] = $cond;
			$res['sort'] = $sort;
			return json_response(200,$res);
		}

	}else if( $action == "objectCreate" ){
		if( !isset($post['node']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		$thing = $post['node'];
		if( !is_array( $thing ) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		if( !isset( $thing['l'] ) || !isset( $thing['i_of'] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		if( !is_array( $thing['i_of'] ) || !is_array( $thing['i_of'] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		if( !isset( $thing['l']['v'] ) || !$thing['l']['v'] ){
			return json_response(400,["status"=>"fail", "error"=>"Node name missing"]);
		}
		if( !preg_match("/^[a-z0-9\-\_\.\,\ ]{2,200}$/i", $thing['l']['v']) ){
			return json_response(400,["status"=>"fail", "error"=>"Node Label invalid"]);
		}

		$instance_id = $thing['i_of']['i'];
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $instance_id) ){
			return json_response(400,["status"=>"fail", "status"=>"Instance id incorrect"]);
		}
		if( !preg_match("/^[a-z0-9\-\_\.\,\ ]{2,200}$/i", $thing['i_of']['v']) ){
			return json_response(400,["status"=>"fail", "status"=>"Instance Label invalid"]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$instance_id] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "status"=>"Instance node not found"]);
		}
		$instance = $res['data'];

		if( $instance['l']['v'] == "Root" && $thing['l']['t'] == "GT" ){
			return json_response(400,["status"=>"fail", "status"=>"Nodes under Root instance should not refer other nodes"]);
		}

		$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$instance_id, 'l.v'=>$thing['l']['v']] );
		if( $res['data'] ){
			return json_response(400, ["status"=>"fail", "error"=>"A node with same name already exists"]);
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
		$new_thing = [
			'_id'=>$new_id,
			'l'=>[
				't'=>"T", "v"=>$post['l']['v']
			],
			'i_of'=>[
				't'=>"GT",
				'i'=>$instance_id,
				'v'=>$post['i_of']['v']
			],
			'i_t'=> ["t"=>"T", "v"=>"N"],
			'm_i'=> date("Y-m-d H:i:s"),
			'm_u'=> date("Y-m-d H:i:s")
		];
		$res = $mongodb_con->insert( $graph_things, $new_thing );
		$res2 = $mongodb_con->increment( $graph_things, $instance_id, "cnt", 1 );
		send_to_keywords_queue($graph_id, $res['inserted_id'] );
		event_log( "objects", "create_on_fly", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$res['inserted_id'],
		]);
		$res['object'] = $new_thing;
		return json_response(200,$res);

	}else if( $action == "objectCreateWithTemplate" ){
		if( !isset($post['node']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		$thing = $post['node'];
		if( !is_array( $thing ) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		if( !isset( $thing['l'] ) || !isset( $thing['i_of'] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		if( !is_array( $thing['i_of'] ) || !is_array( $thing['i_of'] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		if( !isset( $thing['l']['v'] ) || !$thing['l']['v'] ){
			return json_response(400,["status"=>"fail", "error"=>"Node name missing"]);
		}
		if( !isset( $thing['l']['t'] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Node DataType missing"]);
		}
		if( $thing['l']['t'] != "T" && $thing['l']['t'] != "GT" ){
			return json_response(400,["status"=>"fail", "error"=>"Node DataType Invalid"]);
		}
		if( $thing['l']['t'] == "GT" ){
			if( !isset( $thing['l']['i'] ) || !$thing['l']['i'] ){
				return json_response(400,["status"=>"fail", "error"=>"Node Link ID missing"]);
			}
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $thing['l']['i']) ){
				return json_response(400,["status"=>"fail", "error"=>"Node Link ID invalid"]);
			}
		}
		if( !preg_match("/^[a-z0-9\-\_\.\,\ ]{2,200}$/i", $thing['l']['v']) ){
			return json_response(400,["status"=>"fail", "error"=>"Node Label invalid"]);
		}

		$instance_id = $thing['i_of']['i'];
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $instance_id) ){
			return json_response(400,["status"=>"fail", "status"=>"Instance id incorrect"]);
		}
		if( !preg_match("/^[a-z0-9\-\_\.\,\ ]{2,200}$/i", $thing['i_of']['v']) ){
			return json_response(400,["status"=>"fail", "status"=>"Instance Label invalid"]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$instance_id] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "status"=>"Instance node not found"]);
		}
		$instance = $res['data'];

		if( $instance['l']['v'] == "Root" && $thing['l']['t'] == "GT" ){
			return json_response(400,["status"=>"fail", "status"=>"Nodes under Root instance should not refer other nodes"]);
		}

		if( isset($instance['z_t']) ){
			if( !isset( $thing['props'] ) ){
				json_response("fail", "Properties Data missing");
			}
			if( !is_array( $thing['props'] ) ){
				json_response("fail", "Properties Data missing");
			}
		}

		$new_thing = [
			'l'=>[
				't'=>"T", "v"=>$post['l']['v']
			],
			'i_of'=>[
				't'=>"GT",
				'i'=>$instance_id,
				'v'=>$post['i_of']['v']
			],
			'i_t'=> ["t"=>"T", "v"=>"N"],
			'm_i'=> date("Y-m-d H:i:s"),
			'm_u'=> date("Y-m-d H:i:s")
		];

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

		$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$instance_id, 'l.v'=>$thing['l']['v']] );
		if( $res['data'] ){
			return json_response(400, ["status"=>"fail", "error"=>"A node with same name already exists"]);
		}

		if( $instance['l']['v'] == "Root" || $instance['_id'] == "T1" ){
			if( !isset($instance['series']) ){
				$new_id = "T2";
				$res5 = $mongodb_con->update_one( $graph_things, ["_id"=>$instance_id ], ["series"=>2] );
			}else{
				$res5 = $mongodb_con->increment( $graph_things, $instance_id, "series", 1 );
				$new_id = "T" . $res5['data']['series'];
			}
			$new_thing['_id'] = $new_id;
		}else{
			if( !isset($instance['series']) ){
				$new_id = $instance_id."T1";
				$res5 = $mongodb_con->update_one( $graph_things, ["_id"=>$instance_id ], ["series"=>1] );
			}else{
				$res5 = $mongodb_con->increment( $graph_things, $instance_id, "series", 1 );
				$new_id = $instance_id."T" . $res5['data']['series'];
			}
			$new_thing['_id'] = $new_id;
		}

		$res = $mongodb_con->insert( $graph_things, $new_thing );
		$res2 = $mongodb_con->increment( $graph_things, $instance_id, "cnt", 1 );
		send_to_keywords_queue($graph_id, $res['inserted_id'] );
		event_log( "objects", "create_on_fly", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$res['inserted_id'],
		]);
		$res['object'] = $new_thing;
		return json_response(200,$res);
	}else{
		return json_response(403,["status"=>"fail", "error"=>"Unknown action" ]);
	}

}