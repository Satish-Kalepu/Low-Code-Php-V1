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

// $graph_things = $db_prefix . "_graph_" . $post['graph_id'] . "_things";
// $graph_links = $db_prefix . "_graph_" . $post['graph_id'] . "_links";
// $graph_keywords = $db_prefix . "_graph_" . $post['graph_id'] . "_keywords";
// $graph_queue = $db_prefix . "_zd_queue_graph_" . $post['graph_id'];
// $graph_log = $db_prefix . "_zlog_graph_" . $post['graph_id'];

function engine_api_object( $graph_db, $action, $post ){

	global $mongodb_con;
	global $app_id;
	global $db_prefix;

	$graph_id = $graph_db['_id'];
	$graph_things = $db_prefix . "_graph_" . $graph_id . "_things";
	$graph_keywords = $db_prefix . "_graph_" . $graph_id . "_keywords";	

/*
		keywordSearch    - context_load_things
		listObjects    - objects_load_basic
		getObject    - objects_load_object
		getObjectTemplate    - objects_load_template
		getObjectRecords    - objects_load_records
		getObjectNodes    - objects_load_browse_list
		getObjectLibrarySettings    - 
		objectCreate    - objects_create_node_on_fly
		objectCreateWithTemplate    - objects_create_with_template
		objectLabelUpdate    - objects_edit_label
		objectTypeUpdate    - objects_edit_type
		objectAliasUpdate    - objects_edit_alias
		objectInstanceUpdate    - objects_edit_i_of
		objectPropertiesUpdate    - objects_save_props
		objectHtmlUpdate  -  objects_save_object_html
		objectNodesTruncate    - objects_nodes_empty
		objectDelete    - objects_delete_node
		objectConverToDataset    - objects_ops_convert_to_dataset
		objectConverToNode    - objects_ops_convert_to_nodelist
		objectSetIcon    -  objects_set_icon
		objectTemplateFieldCreate    - objects_object_add_field
		objectTemplateFieldUpdate    - objects_save_object_z_t
		objectTemplateFieldDelete    - objects_delete_field
		objectTemplateEnable    - objects_save_enable_z_t
		objectTemplateOrderUpdate    - objects_save_z_o
		dataSetRecordCreate    - objects_dataset_record_create
		dataSetRecordUpdate    - objects_dataset_record_save_props
		dataSetRecordDelete    - objects_dataset_record_delete
		dataSetTruncate    - objects_records_empty

		objects_import_data_check
		object_import_batch
		save_settings_library
*/


		/*
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

	if( $action == "keywordSearch" ){ //context_load_things

		$things = [];
		$cond = [];
		$sort = [];
		if( isset($post['keyword']) && $post['keyword'] ){
			if( !preg_match("/^[a-z0-9\ \-]+$/i", $post['keyword']) ){
				return json_response(200,["status"=>"success", "things"=>[] ]);
			}
			$cond['p'] = ['$gte'=>$post['keyword'], '$lte'=>$post['keyword']."zzz" ];
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
			$res= [
				"status"=>"success",
				"things"=>$things,
				"keyword"=>$post['keyword'],
			];
			return json_response(200,$res);
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
			$res= [
				"status"=>"success",
				"things"=>$things,
				"keyword"=>$post['keyword'],
			];
			return json_response(200,$res);
		}

	}else if( $action == "listObjects" ){ //objects_load_basic
		if( !isset($post['sort']) ){
			return json_response(400,["status"=>"fail", "error"=>"Input Missing: sort" ]);
		}
		if( !preg_match("/^(ID|label|nodes)$/", $post['sort']) ){
			return json_response(400,["status"=>"fail", "error"=>"sort: choose ID/label/nodes" ]);
		}
		if( !isset($post['order']) ){
			return json_response(400,["status"=>"fail", "error"=>"Input Missing: order" ]);
		}
		if( !preg_match("/^(asc|desc|dsc)$/", $post['order']) ){
			return json_response(400,["status"=>"fail", "error"=>"order: choose asc/dsc" ]);
		}
		if( !isset($post['limit']) ){
			return json_response(400,["status"=>"fail", "error"=>"Input Missing: limit. max 500"]);
		}
		if( !is_numeric($post['limit']) || $post['limit'] < 1 || $post['limit'] > 500 ){
			return json_response(400,["status"=>"fail", "error"=>"limit invalid"]);
		}
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
		}else{
			return json_response(400,["status"=>"fail", "error"=>"Input invalid" ]);
		}
		$res = $mongodb_con->find( $graph_things, $cond, [
			'projection'=>['l'=>1,'i_of'=>1, 'm_i'=>1, 'm_u'=>1,'cnt'=>1,'ic'=>1, 'i_t'=>1],
			'sort'=>$sort,
			'limit'=>100,
		]);
		return json_response(200,[
			"status"=>"success", "data"=>$res['data'], "query"=>$cond
		]);

	}else if( $action == "getObject" ){ //objects_load_object
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$ops = [

		];
		$res = $mongodb_con->find_one( $graph_things, ["_id"=>$post['object_id']], $ops );
		if( $res['status'] != "success" ){
			return json_response(500,[
				"status"=>"fail", "error"=>"Node not found"
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

	}else if( $action == "getObjectTemplate" ){ //objects_load_template
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$ops = [
			'projection'=>['z_t'=>1,'z_o'=>1, 'z_n'=>1,'l'=>1,'i_of'=>1, 'i_t'=>1]
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

	}else if( $action == "getObjectRecords" ){ //objects_load_records
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
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
	
	}else if( $action == "getObjectNodes" ){ //objects_load_browse_list
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
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
				'projection'=>['l'=>1,'props'=>1,'i_of'=>1,'m_u'=>1,'ic'=>1, 'i_t'=>1],
				'sort'=>['l.v'=>1],
				'limit'=>100,
			]);
			$res['cnt'] = $cnt;
			$res['cnt'] = $cnt;
			$res['cond'] = $cond;
			$res['sort'] = $sort;
			return json_response(200,$res);
		}
	
	}else if( $action == "getObjectLibrarySettings" ){ 

		if( !isset($graph_db['settings']) ){
			$settings = ['enabled'=>false, 'library'=>false];
		}else{

			$library = false;
			if( $graph_db['settings']['library_enable'] ){
				$library = [
					'vault_id'=>		$graph_db['settings']['library']['vault_id'],
					'vault_name'=>		$graph_db['settings']['library']['vault']['des'],
					'vault_type'=>		$graph_db['settings']['library']['vault']['vault_type'],
					'file_id'=>			$graph_db['settings']['library']['vault']['file_id'],
					'upload'=>			$graph_db['settings']['library']['upload'],
					'size'=>			$graph_db['settings']['library']['size'],
					'dest_path'=>		$graph_db['settings']['library']['dest_path'],
					'thumb_path'=>		$graph_db['settings']['library']['thumb_path'],
					'thing_id'=>		$graph_db['settings']['library']['thing_id'],
				];
			}

			$settings = [
				'enabled'=>$graph_db['settings']['library_enable'],
				'library'=>$library
			];
		}

		return json_response(200, [
			'status'=>'success',
			'library_settings'=>$settings,
		]);

	}else if( $action == "objectCreate" ){ //objects_create_node_on_fly
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

		if( $instance['i_t']['v'] != "N" ){
			return json_response(400,["status"=>"fail", "error"=>"Instance is not of type Node. Sub Nodes are not allowed"]);
		}

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
		
		$thing['i_t']=["t"=>"T", "v"=>"N"];
		$thing['m_i']=date("Y-m-d H:i:s");
		$thing['m_u']=date("Y-m-d H:i:s");
		$res = $mongodb_con->insert( $graph_things, $thing );
		$res2 = $mongodb_con->increment( $graph_things, $instance_id, "cnt", 1 );

		send_to_keywords_queue($graph_id, $res['inserted_id'] );

		event_log( "objects", "create_on_fly", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$res['inserted_id'],
		]);
		$res['object'] = $thing;
		return json_response(200,$res);

	}else if( $action == "objectCreateWithTemplate" ){ //objects_create_with_template
		if( !isset($post['thing']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		$thing = $post['thing'];
		if( !is_array( $thing ) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		if( !isset( $thing['l'] ) || !isset( $thing['i_t'] ) || !isset( $thing['i_of'] ) || !isset( $thing['props'] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}
		if( !is_array( $thing['l'] ) || !is_array( $thing['i_t'] ) || !is_array( $thing['i_of'] ) || !is_array( $thing['props'] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Data incorrect format 1"]);
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

		if( !isset( $thing['i_t'] ) ){
			return json_response(400, ["status"=>"fail", "error"=>"Node type missing"]);
		}
		if( !isset( $thing['i_t']['t'] ) || !isset( $thing['i_t']['v'] ) ){
			return json_response(400, ["status"=>"fail", "error"=>"Node type missing"]);
		}
		if( $thing['i_t']['t'] != "T" || !preg_match("/^(N|L|M|D)$/", $thing['i_t']['v'] ) ){
			return json_response(400, ["status"=>"fail", "error"=>"Node type missing"]);
		}
		if( $thing['i_t']['t'] == "L" ){
			if( !isset($thing['z_t']) ){
				return json_response(400, ["status"=>"fail", "error"=>"Template is must for a DataSet"]);
			}
		}
		if( $thing['i_t']['t'] == "D" || $thing['i_t']['t'] == "M" ){
			if( isset($thing['z_t']) ){
				unset($thing['z_t']);
				unset($thing['z_o']);
				unset($thing['z_n']);
			}
		}

		if( !is_array( $thing['i_of'] ) || !is_array( $thing['i_of'] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
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
				return json_response(400, ["status"=>"fail", "error"=>"Properties Data missing"]);
			}
			if( !is_array( $thing['props'] ) ){
				return json_response(400, ["status"=>"fail", "error"=>"Properties Data missing"]);
			}
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

		$props = [];
		if( isset( $thing['props'] ) ){
			foreach( $thing['props'] as $propf=>$j ){
				$k = [];
				if( is_array($j) ){
					for($ii=0;$ii<sizeof($j);$ii++){
						if( isset($j[ $ii ]['t']) && isset($j[ $ii ]['v']) ){
							$k[]=$j[ $ii ];
						}
					}
				}
				if( sizeof($k) ){
					$props[ $propf ] = $k;
				}
			}
			$thing['props'] = $props;
		}

		//return json_response(200, $thing['props']);

		$z_t = [];
		if( isset($thing['z_t']) ){
			foreach( $thing['z_t'] as $i=>$j ){
				if( !isset($j['name']) || !isset($j['type']) ){
					return json_response(400, ["status"=>"fail", "error"=>"Template error: " . $i ]);
				}
				if( !$j['name']['v'] || !$j['type']['k'] ){
					return json_response(400, ["status"=>"fail", "error"=>"Template error: " . $i ]);
				}
				$z_t[ $i ] = ['l'=>$j['name'],'t'=>$j['type'],'e'=>false,'m'=>false];
				if( $j['type']['k'] =="GT" ){
					if( !isset($j['i_of']) ){
						return json_response(400, ["status"=>"fail", "error"=>"Template error: " . $i . " Graph instance" ]);
					}
					if( !$j['i_of']['i'] || !$j['i_of']['v'] ){
						return json_response(400, ["status"=>"fail", "error"=>"Template error: " . $i . " Graph instance" ]);
					}
					$z_t[ $i ]['i_of'] = $j['i_of'];
				}
			}
			$thing['z_t'] = $z_t;
		}

		$thing['m_i']=date("Y-m-d H:i:s");
		$thing['m_u']=date("Y-m-d H:i:s");
		$res = $mongodb_con->insert( $graph_things, $thing );
		if( $res['status'] == "success" ){
			$res2 = $mongodb_con->increment( $graph_things, $instance_id, "cnt", 1 );
			send_to_keywords_queue($graph_id, $res['inserted_id'] );
			event_log( "objects", "create_with_template", [
				"app_id"=>$app_id,
				"graph_id"=>$graph_id,
				"object_id"=>$res['inserted_id'],
			]);
			$res['object'] = $thing;
			return json_response(200, $res);
		}else{
			$res['object'] = $thing;
			return json_response(400, $res);
		}

	}else if( $action == "objectLabelUpdate" ){ //objects_edit_label

		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}
		$object = $res['data'];
		if( !isset($post['label']) ){
			return json_response(400, ["status"=>"fail", "error"=>"Need Label"]);
		}else if( !is_array($post['label']) ){
			return json_response(400, ["status"=>"fail", "error"=>"Need Label"]);
		}else if( !isset($post['label']['t']) || !isset($post['label']['v']) ){
			return json_response(400, ["status"=>"fail", "error"=>"Need Label"]);
		}
		$label = $post['label'];

		$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$object['i_of']['i'], 'l.v'=>$label['v'], '_id'=>['$ne'=>$object_id] ] );
		if( $res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Duplicate Node Exists"]);
		}

		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
			'l'=>$label,
			'updated'=>date("Y-m-d H:i:s")
		]);

		send_to_keywords_queue($graph_id, $object_id);

		event_log( "objects", "edit_label", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$object_id,
		]);

		return json_response(200, $res );
	}else if( $action == "objectTypeUpdate" ){ //objects_edit_type

		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$object_id = $post['object_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}
		$object = $res['data'];
		$current_type = $object['i_t']['v'];
		if( !isset($post['type']) ){
			return json_response(404,["status"=>"fail", "error"=>"Need type"]);
		}else if( !is_array($post['type']) ){
			return json_response(404,["status"=>"fail", "error"=>"Need Type"]);
		}else if( !isset($post['type']['t']) || !isset($post['type']['v']) ){
			return json_response(404,["status"=>"fail", "error"=>"Need Type"]);
		}
		$type = $post['type'];

		if( $current_type == "N" && $type['v'] != "N" ){
			if( isset($object['cnt']) && $object['cnt'] > 0 ){
				return json_response(404,["status"=>"fail", "error"=>"There are nodes " . $object['cnt'] . " under this object"]);
			}
		}
		if( $current_type == "L" && $type['v'] != "L" ){
			if( isset($object['cnt']) && $object['cnt'] > 0 ){
				return json_response(404,["status"=>"fail", "error"=>"There are records " . $object['cnt'] . " under this DataSet"]);
			}
		}

		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
			'i_t'=>$type,
			'updated'=>date("Y-m-d H:i:s")
		]);

		send_to_keywords_queue($graph_id, $object_id);

		event_log( "objects", "edit_type", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$object_id,
		]);

		return json_response(200, $res );

	}else if( $action == "objectAliasUpdate" ){ //objects_edit_alias

		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$object_id = $post['object_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}
		$object = $res['data'];
		if( !isset($post['alias']) ){
			return json_response(404,["status"=>"fail", "error"=>"Need alias"]);
		}else if( !is_array($post['alias']) ){
			return json_response(404,["status"=>"fail", "error"=>"Need alias"]);
		}else{
			if( array_keys($post['alias'])[0] !== 0 ){
				$post['alias'] = [];
			}
			$als = [];
			for($i=0;$i<sizeof($post['alias']);$i++){
				$v =$post['alias'][$i];
				if( !isset($v['t']) || !isset($v['v']) || $v['v'] == "" ){
					array_splice($post['alias'],$i,1);$i--;
				}else if( strtolower($v['v']) == strtolower($object['l']['v']) ){
					return json_response(404,["status"=>"fail", "error"=>"Label and Alias should be different"]);
				}
				if( in_array(strtolower($v['v']), $als) ){
					array_splice($post['alias'],$i,1);$i--;
				}else{
					$als[] = strtolower($v['v']);
				}
			}
		}
		//print_r( $als );exit;
		if( sizeof($post['alias']) ){
			$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
				'al'=>$post['alias'],
				'updated'=>date("Y-m-d H:i:s")
			]);
		}else{
			$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
				'$unset'=>[ 'al'=>true ],
				'$set'=>['updated'=>date("Y-m-d H:i:s")],
			]);
		}
		send_to_keywords_queue($graph_id, $object_id);
		event_log( "objects", "edit_alias", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$object_id,
		]);
		return json_response(200, $res );

	}else if( $action == "objectInstanceUpdate" ){ //objects_edit_i_of

		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$object_id = $post['object_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		$object = $res['data'];
		if( !isset($post['i_of']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Instance Of"]);
		}else if( !is_array($post['i_of']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Instance Of"]);
		}else if( !isset($post['i_of']['t']) || !isset($post['i_of']['v']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Instance Of"]);
		}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['i_of']['i']) && !preg_match("/^[0-9]+$/i", $post['i_of']['i'] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Instance id incorrect"]);
		}
		$i_of = $post['i_of'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$i_of['i']] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Instance not found"]);
		}
		$instance = $res['data'];

		$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$i_of['i'], 'l.v'=>$object['l']['v'], '_id'=>['$ne'=>$object_id] ] );
		if( $res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Duplicate Node Exists in Instance: " . $i_of['v']]);
		}
		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
			'i_of'=>$i_of,
			'updated'=>date("Y-m-d H:i:s")
		]);
		send_to_keywords_queue($graph_id, $object_id);

		$res2 = $mongodb_con->increment( $graph_things, $object['i_of']['i'], "cnt", -1 );
		$res2 = $mongodb_con->increment( $graph_things, $post['data']['i_of']['i'], "cnt", 1 );

		event_log( "objects", "edit_instance", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$object_id,
		]);

		return json_response(200, $res );

	}else if( $action == "objectPropertiesUpdate" ){ //objects_save_props

		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$thing_id = $post['object_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		$res2 = $mongodb_con->find_one( $graph_things, ['_id'=>$res['data']['i_of']['i']] );
		if( !$res2['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"parent not found"]);
		}
		$parent = $res2['data'];

		if( !isset($post['props']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}else if( !is_array($post['props']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}

		$props = $post['props'];

		foreach( $props as $field=>$values ){
			if( !is_array($values) ){
				return json_response(400,["status"=>"fail", "error"=>"Property `" . $field . "` has invalid value"]);
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
							return json_response(400,["status"=>"fail", "error"=>"Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd)]);
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
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$thing_id,
		]);

		return json_response(200,$res);

	}else if( $action == "objectHtmlUpdate" ){ //objects_save_object_html
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Thing id"]);
		}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Thing id incorrect"]);
		}

		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Thing not found"]);
		}
		if( !isset($post['body']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 1"]);
		}else if( !isset($post['body']['html']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 1"]);
		}else{
			$res = $mongodb_con->update_one( $graph_things, ['_id'=>$post['object_id']], [
				'body'=>$post['body'],
			]);
			event_log( "objects", "html_save", [
				"app_id"=>$config_param1,
				"graph_id"=>$graph_id,
				"object_id"=>$post['object_id'],
			]);
			$res['cnt'] = $post['cnt'];
			return json_response(200, $res );
		}

	}else if( $action == "objectNodesTruncate" ){ //objects_nodes_empty
		if( !isset($post['instance_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Instance id"]);
		}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['instance_id']) && !preg_match("/^[0-9]+$/i", $post['instance_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Instance id incorrect"]);
		}
		$instance_id = $post['instance_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$instance_id] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Instance not found"]);
		}

		while( 1 ){
			$res = $mongodb_con->find( $graph_things, ['i_of.i'=>$instance_id], ['limit'=>500] );
			if( sizeof($res['data']) == 0 ){
				break;
			}
			foreach( $res['data'] as $i=>$j ){
				$mongodb_con->delete_one( $graph_things,["i_of.i"=>$instance_id, "_id"=>$j['_id']] );
				send_to_keywords_delete_queue($graph_id,$j['_id']);
				event_log( "objects", "delete", [
					"app_id"=>$app_id,
					"graph_id"=>$graph_id,
					"object_id"=>$j['_id'],
				]);
			}
		}
		$mongodb_con->update_one( $graph_things, ["_id"=> $instance_id], ["cnt"=>0] );

		return json_response(200,['status'=>"success"]);


	}else if( $action == "objectDelete" ){ //objects_delete_node
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$object_id = $post['object_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		$thing = $res['data'];
		$instance_id = $thing['i_of']['i'];

		// if( $thing['i_t'] != "N" ){
		// 	return json_response(400,["status"=>"fail", "error"=>"Incorrect node type"]);
		// }
		if( $thing['cnt'] > 0 ){
			return json_response(400,["status"=>"fail", "error"=>"There are nested ".$thing['cnt']." nodes under ". $thing['l']['v']]);
		}

		$mongodb_con->delete_one( $graph_things,[
			"_id"=>$object_id
		]);
		send_to_keywords_delete_queue($graph_id, $object_id);
		event_log( "objects", "delete", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$object_id,
		]);
		$mongodb_con->increment( $graph_things, $instance_id, "cnt", -1 );
		return json_response(200,['status'=>"success"]);

	}else if( $action == "objectConverToDataset" ){ //objects_ops_convert_to_dataset
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$object_id = $_POST['object_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		$thing = $res['data'];
		$instance_id = $thing['i_of']['i'];

		if( $thing['i_t']['v'] == "L" ){
			return json_response(400,["status"=>"fail", "error"=>"Object is already a dataset" ]);
		}else if( $thing['i_t']['v'] != "N" ){
			return json_response(400,["status"=>"fail", "error"=>"Source Object must be type of Node" ]);
		}

		if( !isset($post['label_to']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need new Label Property id"]);
		}else if( !preg_match("/^[a-z0-9\.\,\-\_\ ]{2,100}$/i", $post['label_to']) ){
			return json_response(400,["status"=>"fail", "error"=>"Property name should be plain text"]);
		}

		$new_prop = trim($post['label_to']);
		foreach( $thing['z_t'] as $propf=>$p ){
			if( strtolower($new_prop) == strtolower($p['l']['v']) ){
				return json_response(400,["status"=>"fail", "error"=>"Property name `".$new_prop."` already exists!" ]);
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
					send_to_records_queue($graph_id, $object_id, $rec_id, "record_create" );
					$mongodb_con->delete_one( $graph_things, ['_id'=>$id]);
					send_to_keywords_delete_queue($graph_id,$id);
					event_log( "objects", "delete", [
						"app_id" => $app_id,
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
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$object_id,
			"from"=>"N",
			"to"=>"L",
		]);
		return json_response(200,['status'=>"success"]);

	}else if( $action == "objectConverToNode" ){ //objects_ops_convert_to_nodelist
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$object_id = $post['object_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		$thing = $res['data'];
		$instance_id = $thing['i_of']['i'];

		if( $thing['i_t']['v'] == "N" ){
			return json_response(400,["status"=>"fail", "error"=>"Object is already a node list" ]);
		}else if( $thing['i_t']['v'] != "L" ){
			return json_response(400,["status"=>"fail", "error"=>"Source Object must be type of Dataset" ]);
		}

		if( !isset($post['primary_field']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need NodeID field"]);
		}
		if( !isset( $thing['z_t'][ $post['primary_field'] ] ) && $post['primary_field'] != "default-id" ){
			return json_response(400,["status"=>"fail", "error"=>"Primary field not found"]);
		}
		if( !isset($post['label_field']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Label field"]);
		}
		if( !isset( $thing['z_t'][ $post['label_field'] ] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Label field not found"]);
		}
		if( isset($post['alias_field']) && sizeof($post['alias_field']) > 1 ){
			return json_response(400,["status"=>"fail", "error"=>"One alias field is expected"]);
		}
		if( isset($post['alias_field']) && sizeof($post['alias_field']) > 0 ){
			if( !isset( $thing['z_t'][ $post['alias_field'][0] ] ) ){
				return json_response(400,["status"=>"fail", "error"=>"Alias field not found"]);
			}
		}

		$graph_things_dataset = $graph_things . "_" . $object_id;

		if( $post['primary_field'] != "default-id" ){
			$res = $mongodb_con->aggregate( $graph_things_dataset, [
				['$group'=>['_id'=>'$props.'.$post['primary_field'].".v", 'cnt'=>['$sum'=>1]] ],
				['$sort'=>["cnt"=>-1]]
			]);
			print_r( $res );exit;
			if( $res['status'] != "success" ){
				return json_response(200,$res);
			}
			if( isset($res['data']) && sizeof($res['data']) > 0 ){
				if( $res['data'][0]['cnt'] > 1 ){
					return json_response(400,["status"=>"fail", "error"=>"Primary field value `" . $res['data'][0]['_id'][0] . "` repeated. " ]);
				}
			}else{
				return json_response(400,["status"=>"fail", "error"=>"Primary values not found"]);
			}
			foreach( $res['data'] as $i=>$j ){
				if( !preg_match( "/^[a-z0-9]{2,24}$/i", $j['_id'][0] ) ){
					return json_response(400,["status"=>"fail", "error"=>"Primary field value " . $j['_id'][0] . " is not acceptable"]);
				}
			}
		}

		//echo "xxxx";exit;

		$res = $mongodb_con->aggregate( $graph_things_dataset, [
			['$group'=>['_id'=>'$props.'.$post['label_field'].".v", 'cnt'=>['$sum'=>1]] ],
			['$sort'=>["cnt"=>-1]]
		]);
		//return json_response(200,$res);
		if( $res['status'] != "success" ){
			return json_response(200,$res);
		}
		if( isset($res['data']) && sizeof($res['data']) > 0 ){
			if( $res['data'][0]['cnt'] > 1 ){
				return json_response(400,["status"=>"fail", "error"=>"Label field `" . $res['data'][0]['_id'][0] . "` repeated. " ]);
			}
		}else{
			return json_response(400,["status"=>"fail", "error"=>"Label values not found"]);
		}
		foreach( $res['data'] as $i=>$j ){
			if( !preg_match( "/^[a-z][a-z0-9\-\.\_\,\ \(\)\@\!\&\:]{1,200}$/i", $j['_id'][0] ) ){
				return json_response(400,["status"=>"fail", "error"=>"Label field value " . $j['_id'][0] . " is not acceptable"]);
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
				if( $post['primary_field'] =="default-id" ){
					$res5 = $mongodb_con->increment( $graph_things, $object_id, "series", 1 );
					$new_id = $object_id."T" . $res5['data']['series'];
					$rec_id = $new_id;
				}else{
					$rec_id = $j[ "props" ][ $post['primary_field'] ][0]['v'];
				}
				$al = [];
				if( sizeof($post['alias_field']) ){
					if( isset($j[ "props" ][ $post['alias_field'][0] ]) ){
						$al[] = $j[ "props" ][ $post['alias_field'][0] ][0];
					}
				}
				$d = [
					"_id"=>$rec_id,
					"i_t"=>["t"=>"T", "v"=>"N"],
					"l"=>$j[ "props" ][ $post['label_field'] ][0],
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
						send_to_records_queue($graph_id, $object_id, $j['_id'], "record_delete" );
						send_to_keywords_queue($graph_id, $ires['inserted_id'] );
						event_log( "objects", "create", [
							"app_id" => $app_id,
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
					send_to_records_queue( $graph_id, $object_id, $j['_id'], "record_delete" );
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
			"app_id"=>$app_id,
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


	}else if( $action == "objectSetIcon" ){ //objects_set_icon
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Thing id"]);
		}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Thing id incorrect"]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Thing not found"]);
		}
		if( !isset($post['ic']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 1"]);
		}else if( !isset($post['ic']['t']) || !isset($post['ic']['v']) || !isset($post['ic']['it']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 1"]);
		}

		if( $post['ic']['t'] != "IC" ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 5"]);
		}
		if( !preg_match( "/^(emoji|svg|font|flag|img|img1|img2|img3|imgf)$/", $post['ic']['it'] ) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 6"]);
		}

		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$post['object_id']], [
			'ic'=>$post['ic'],
		]);
		event_log( "objects", "icon_save", [
			"app_id"=>$config_param1,
			"graph_id"=>$graph_id,
			"object_id"=>$post['object_id'],
		]);
		return json_response(200, $res );
		exit;

	}else if( $action == "objectTemplateFieldCreate" ){ //objects_object_add_field

		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		if( !isset($post['field']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 1"]);
		}else if( !preg_match("/^p[0-9]+$/",$post['field']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 2"]);
		}
		if( !isset($post['prop']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 3"]);
		}else if( !is_array($post['prop']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 4"]);
		}

		if( isset($res['data']['z_t'][ $post['field'] ]) ){
			return json_response(400,["status"=>"fail", "error"=>"Field key ".$post['field']." already exists"]);
		}
		$n = intval(str_replace("p","",$post['field']));
		if( $n < $res['data']['z_n'] ){
			return json_response(400,["status"=>"fail", "error"=>"Field keyindex ".$post['z_n']." already exists"]);
		}

		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$post['object_id']], [
			'$set'=>[ 
				'z_t.'. $post['field']=>$post['prop'],
				'z_n'=>$post['z_n']
			],
			'$push'=>['z_o'=>$post['field']],
		]);
		event_log( "objects", "field_add", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$post['object_id'],
			"field"=>$post['field']
		]);
		return json_response(200,$res);

	}else if( $action == "objectTemplateFieldUpdate" ){ //objects_save_object_z_t
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		if( !isset($post['field']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 1"]);
		}else if( !preg_match("/^p[0-9]+$/",$post['field']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 2"]);
		}
		if( !isset($post['prop']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 3"]);
		}else if( !is_array($post['prop']) ){
			return json_response(400,["status"=>"fail", "error"=>"Incorrect data 4"]);
		}
		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$post['object_id']], [
			'z_t.'. $post['field']=>$post['prop'],
		]);
		event_log( "objects", "template_save", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$post['object_id'],
		]);
		return json_response(200,$res);

	}else if( $action == "objectTemplateFieldDelete" ){ //objects_delete_field
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		if( !isset($post['prop']) ){
			return json_response(404,["status"=>"fail", "error"=>"Need Prop ID"]);
		}else if( !preg_match("/^p[0-9]+$/i", $post['prop']) ){
			return json_response(404,["status"=>"fail", "error"=>"Need Prop ID"]);
		}

		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$post['object_id']], [
			'$unset'=>[
				'z_t.' . $post['prop']=>true,
			],
			'$pull'=>[
				'z_o'=>$post['prop']
			]
		]);

		event_log( "objects", "field_delete", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$post['object_id'],
			"field"=>$post['prop']
		]);

		return json_response(200,$res);

	}else if( $action == "objectTemplateEnable" ){ //objects_save_enable_z_t
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}
		
	}else if( $action == "objectTemplateOrderUpdate" ){ //objects_save_z_o
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

	}else if( $action == "dataSetRecordCreate" ){ //objects_dataset_record_create
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$thing_id = $post['object_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		$res2 = $mongodb_con->find_one( $graph_things, ['_id'=>$res['data']['i_of']['i']] );
		if( !$res2['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"parent not found"]);
		}
		$parent = $res2['data'];

		if( !isset($post['record_props']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}else if( !is_array($post['record_props']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}

		$props = $post['record_props'];

		foreach( $props as $field=>$values ){
			if( !is_array($values) ){
				return json_response(400,["status"=>"fail", "error"=>"Property `" . $field . "` has invalid value"]);
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
							return json_response(400,["status"=>"fail", "error"=>"Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd)]);
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
		
		$res = $mongodb_con->insert( $graph_things. "_". $post['object_id'], $data );

		event_log( "objects", "record_create", [
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$post['object_id'],
			"record_id"=>$res['inserted_id'],
		]);
		send_to_records_queue($graph_id, $post['object_id'], $res['inserted_id'], "record_create" );

		return json_response(200,$res);

	}else if( $action == "dataSetRecordUpdate" ){ //objects_dataset_record_save_props
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$thing_id = $post['object_id'];
		if( !isset($post['record_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Record id"]);
		}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['record_id']) && !preg_match("/^[0-9]+$/i", $post['record_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Record id incorrect"]);
		}
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		$res2 = $mongodb_con->find_one( $graph_things, ['_id'=>$res['data']['i_of']['i']] );
		if( !$res2['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"parent not found"]);
		}
		$parent = $res2['data'];

		$record_id = $post['record_id'];
		$res = $mongodb_con->find_one( $graph_things . "_". $thing_id, ['_id'=>$record_id] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Record not found"]);
		}

		if( !isset($post['props']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}else if( !is_array($post['props']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing"]);
		}

		$props = $post['props'];

		foreach( $props as $field=>$values ){
			if( !is_array($values) ){
				return json_response(400,["status"=>"fail", "error"=>"Property `" . $field . "` has invalid value"]);
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
							return json_response(400,["status"=>"fail", "error"=>"Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd)]);
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
			"app_id"=>$app_id,
			"graph_id"=>$graph_id,
			"object_id"=>$post['object_id'],
			"record_id"=>$post['record_id'],
		]);

		return json_response(200,$res);

	}else if( $action == "dataSetRecordDelete" ){ //objects_dataset_record_delete
		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Object Id Invalid" ]);
		}
		$thing_id = $post['object_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$post['object_id']] );
		if( !$res['data'] ){
			return json_response(404,["status"=>"fail", "error"=>"Object not found"]);
		}

		if( !isset($post['record_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Record id"]);
		}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['record_id']) && !preg_match("/^[0-9]+$/i", $post['record_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Record id incorrect"]);
		}
		$record_id = $post['record_id'];

		$res = $mongodb_con->find_one( $graph_things . "_" . $thing_id, ['_id'=>$record_id] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Record not found"]);
		}

		$res = $mongodb_con->delete_one( $graph_things . "_" . $thing_id, ['_id'=>$record_id] );

		send_to_records_queue($graph_id, $thing_id, $record_id, "record_delete" );

		return json_response(200,$res);

	}else if( $action == "dataSetTruncate" ){ //objects_records_empty
		if( !isset($post['instance_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Instance id"]);
		}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['instance_id']) && !preg_match("/^[0-9]+$/i", $post['instance_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Instance id incorrect"]);
		}
		$instance_id = $post['instance_id'];
		$res = $mongodb_con->find_one( $graph_things, ['_id'=>$instance_id] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Instance not found"]);
		}

		while( 1 ){
			$res = $mongodb_con->find( $graph_things . "_". $instance_id, [], ['limit'=>500, 'sort'=>['_id'=>1]] );
			if( sizeof($res['data']) == 0 ){
				break;
			}
			foreach( $res['data'] as $i=>$j ){
				$mongodb_con->delete_one( $graph_things . "_". $instance_id, ["_id"=>$j['_id']] );
				send_to_records_queue($graph_id, $instance_id, $j['_id'], "record_delete" );
			}
		}

		$mongodb_con->update_one( $graph_things, ["_id"=> $instance_id], ["cnt"=>0] );

		return json_response(200,['status'=>"success"]);


		/* Import Actions */

	}else if( $post['action'] == "objects_import_data_check" ){

		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Thing id"]);
		}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Thing id incorrect"]);
		}
		$thing_id = $post['object_id'];

		$res = $mongodb_con->find_one( $graph_things, ["_id"=>$thing_id] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Instance not found"]);
		}
		$instance = $res['data'];

		if( !isset($post['dataset']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing 1"]);
		}
		if( $post['dataset'] === true ){
			if( $instance['i_t']['v'] != "L" ){
				return json_response(400,["status"=>"fail", "error"=>"Selected Instance `".$instance['l']['v']."` is not a type of dataset"]);
			}
			if( !isset($post['primary']) ){
				return json_response(400,["status"=>"fail", "error"=>"Data missing 2"]);
			}
			//$res = $mongodb_con->

		}else{
			if( $instance['i_t']['v'] != "N" ){
				return json_response(400,["status"=>"fail", "error"=>"Selected Instance `".$instance['l']['v']."` is not a type of Node"]);
			}
			if( !isset($post['primary']) ){
				return json_response(400,["status"=>"fail", "error"=>"Data missing 2"]);
			}
			if( !isset($post['label']) ){
				return json_response(400,["status"=>"fail", "error"=>"Data missing 3"]);
			}
		}
		if( !isset($post['schema']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data missing 4"]);
		}

		$token = rand(1,99999999);
		$ires = $mongodb_con->insert( $db_prefix . "_settings", [
			"_id"=>"import_token_" . $token,
			"post"=>$post
		]);

		return json_response(200,["status"=>"success", "token"=>$token]);


	}else if( $post['action'] == "object_import_batch" ){

		if( !isset($post['token']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Token"]);
		}

		$tres = $mongodb_con->find_one( $db_prefix . "_settings", ["_id"=>"import_token_" . $post['token']] );
		if( !$tres['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Token not found"]);
		}
		$token_post = $tres['data']['post'];

		if( !isset($post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Need Thing id"]);
		}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['object_id']) && !preg_match("/^[0-9]+$/i", $post['object_id']) ){
			return json_response(400,["status"=>"fail", "error"=>"Thing id incorrect"]);
		}
		$thing_id = $post['object_id'];

		if( $token_post['object_id'] != $post['object_id'] ){
			return json_response(400,["status"=>"fail", "error"=>"Token mismatch 1"]);
		}

		$res = $mongodb_con->find_one( $graph_things, ["_id"=>$thing_id] );
		if( !$res['data'] ){
			return json_response(400,["status"=>"fail", "error"=>"Instance not found"]);
		}
		$instance = $res['data'];

		//json_response("status", "OK");
		//print_r( $token_post );exit;
		//print_r( $post );

		$success = 0; $inserts = 0; $updates = 0; $skipped = 0;

		$data_cache = [];

		if( $token_post['dataset'] == false ){

			foreach( $post['data'] as $i=>$j ){

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
						return json_response(400,["status"=>"fail", "error"=>"Label instance missing"]);
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
							return json_response(400,["status"=>"fail", "error"=>"Label instance not found"]);
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
			foreach( $post['data'] as $i=>$j ){
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
		return json_response(200,[
			'status'=>"success", 
			"success"=>$success,
			"skipped"=>$skipped,
			"inserts"=>$inserts,
			"updates"=>$updates
		]);
	
	}else{
		return json_response(404, ["status"=>"fail", "error"=>"Unknown action"]);
	}
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

