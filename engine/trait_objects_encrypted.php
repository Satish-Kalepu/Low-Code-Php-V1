<?php

trait engine_graph_objects{

	public $s2_dddi_hparg = "";
	public $s2_sgniht_hparg = "";
	public $s2_sdrowyek_hparg = "";
	public $s2_eueuq_hparg = "";

	public function send_to_keywords_queue( $graph_id, $object_id ){
		$this->s2_eueuq_hparg = $this->s2_xxiferp_bd . "_zd_queue_graph_" . $graph_id;
		//error_log("queue: " . $object_id );
		$task_id = generate_task_queue_id();
		$this->s2_nnnnnnnnoc->insert( $this->graph_queue, [
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
	public function send_to_keywords_delete_queue($graph_id, $object_id){

		$this->s2_eueuq_hparg = $this->s2_xxiferp_bd . "_zd_queue_graph_" . $graph_id;
		//error_log("queue: " . $object_id );
		$task_id = generate_task_queue_id();
		$this->s2_nnnnnnnnoc->insert( $this->graph_queue, [
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
	public function send_to_records_queue($graph_id,  $object_id, $record_id, $action ){

		$this->s2_eueuq_hparg = $this->s2_xxiferp_bd . "_zd_queue_graph_" . $graph_id;
		//error_log("queue: " . $object_id );
		$task_id = generate_task_queue_id();
		$this->s2_nnnnnnnnoc->insert( $this->graph_queue, [
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

	public function graph_object_execute( &$db, $action, $post ){

		if( $action == "keywordSearch" ){ //context_load_things

			$things = [];
			$cond = [];
			$sort = [];
			if( isset($post['keyword']) && $post['keyword'] ){
				if( !preg_match("/^[a-z0-9\ \-]+$/i", $post['keyword']) ){
					return (["status"=>"success", "things"=>[] ]);
				}
				$cond['p'] = ['$gte'=>$post['keyword'], '$lte'=>$post['keyword']."zzz" ];
				$sort = ['p'=>1];
				$debug_t = true;
				$res = $this->s2_nnnnnnnnoc->find( $this->s2_sdrowyek_hparg, $cond, [
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
				return ($res);
			}else{
				$cond['cnt'] = ['$gt'=>1];
				$sort = ['cnt'=>-1];
				$res = $this->s2_nnnnnnnnoc->find( $this->s2_sgniht_hparg, $cond, [
					"sort"=>$sort, 
					"projection"=>['l'=>true, 'i'=>true,'i_of'=>true,'i'=>'$_id', '_id'=>false],
					"limit"=>100,
				]);
				foreach( $res['data'] as $i=>$j ){
					$j['i'] = (string)$j['i'];
					$things[] = $j;
				}
				if( sizeof($things) < 50 ){
					$res = $this->s2_nnnnnnnnoc->find( $this->s2_sgniht_hparg, [], [
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
				return ($res);
			}

		}else if( $action == "listObjects" ){ //objects_load_basic
			if( !isset($post['sort']) ){
				return (["status"=>"fail", "error"=>"Input Missing: sort" ]);
			}
			if( !preg_match("/^(ID|label|nodes)$/", $post['sort']) ){
				return (["status"=>"fail", "error"=>"sort: choose ID/label/nodes" ]);
			}
			if( !isset($post['order']) ){
				return (["status"=>"fail", "error"=>"Input Missing: order" ]);
			}
			if( !preg_match("/^(asc|desc|dsc)$/", $post['order']) ){
				return (["status"=>"fail", "error"=>"order: choose asc/dsc" ]);
			}
			if( !isset($post['limit']) ){
				return (["status"=>"fail", "error"=>"Input Missing: limit. max 500"]);
			}
			if( !is_numeric($post['limit']) || $post['limit'] < 1 || $post['limit'] > 500 ){
				return (["status"=>"fail", "error"=>"limit invalid"]);
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
				return (["status"=>"fail", "error"=>"Input invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find( $this->s2_sgniht_hparg, $cond, [
				'projection'=>['l'=>1,'i_of'=>1, 'm_i'=>1, 'm_u'=>1,'cnt'=>1,'ic'=>1, 'i_t'=>1],
				'sort'=>$sort,
				'limit'=>100,
			]);
			return ([
				"status"=>"success", "data"=>$res['data'], "query"=>$cond
			]);

		}else if( $action == "getObject" ){ //objects_load_object
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			$ops = [];
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ["_id"=>$object_id], $ops );
			if( $res['status'] != "success" ){
				return ([
					"status"=>"fail", "error"=>"Node not found"
				]);
			}
			if( $res['data'] ){
				return ([
					"status"=>"success", "data"=>$res['data']
				]);
			}else{
				return ([
					"status"=>"fail", "error"=>"Object not found"
				]);
			}

		}else if( $action == "getObjectTemplate" ){ //objects_load_template
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			$ops = [
				'projection'=>['z_t'=>1,'z_o'=>1, 'z_n'=>1,'l'=>1,'i_of'=>1, 'i_t'=>1]
			];
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ["_id"=>$object_id], $ops );
			if( $res['status'] != "success" ){
				return ($res);
			}
			if( $res['data'] ){
				return ([
					"status"=>"success", "data"=>$res['data']
				]);
			}else{
				return ([
					"status"=>"fail", "error"=>"Object not found"
				]);
			}

		}else if( $action == "getObjectRecords" ){ //objects_load_records
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}
			$this->s2__sgniht_hpargdataset = $this->s2_sgniht_hparg . "_". $object_id;

			if( $res['data']['i_t']['v'] != "L" ){
				return (["status"=>"fail", "error"=>"Object is not of type DataSet"]);
			}
			{
				$cond = [];
				$res = $this->s2_nnnnnnnnoc->count( $this->s2__sgniht_hpargdataset, $cond );
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
				if( isset($post['filter']) ){
					foreach( $post['filter'] as $i=>$j ){
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
				$res = $this->s2_nnnnnnnnoc->find( $this->s2__sgniht_hpargdataset, $cond, [
					'sort'=>$sort,
					'limit'=>100,
				]);
				$res['cnt'] = $cnt;
				$res['filter'] = $cond;
				$res['sort'] = $sort;
				return ($res);
			}
		
		}else if( $action == "getObjectNodes" ){ //objects_load_browse_list
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}
			if( $res['data']['i_t']['v'] != "N" ){
				return ( ["status"=>"fail", "error"=>"Object is not of type Node"]);
			}
			{
				$cond = ['i_of.i'=>$object_id];
				$res = $this->s2_nnnnnnnnoc->count( $this->s2_sgniht_hparg, $cond );
				$cnt = (int)$res['data'];
				if( $post['from'] ){
					$cond['l.v'] = ['$gt'=> $post['from']];
				}
				if( $post['last'] ){
					$cond['l.v'] = ['$gt'=> $post['last']];
				}
				$res = $this->s2_nnnnnnnnoc->find( $this->s2_sgniht_hparg, $cond, [
					'projection'=>['l'=>1,'props'=>1,'i_of'=>1,'m_u'=>1,'ic'=>1, 'i_t'=>1],
					'sort'=>['l.v'=>1],
					'limit'=>100,
				]);
				$res['cnt'] = $cnt;
				$res['cnt'] = $cnt;
				$res['cond'] = $cond;
				$res['sort'] = $sort;
				return ($res);
			}

		}else if( $action == "getObjectLibrarySettings" ){

			if( !isset($db['settings']) ){
				$settings = ['enabled'=>false, 'library'=>false];
			}else{

				$library = false;
				if( $db['settings']['library_enable'] ){
					$library = [
						'vault_id'=>		$db['settings']['library']['vault_id'],
						'vault_name'=>		$db['settings']['library']['vault']['des'],
						'vault_type'=>		$db['settings']['library']['vault']['vault_type'],
						'file_id'=>			$db['settings']['library']['vault']['file_id'],
						'upload'=>			$db['settings']['library']['upload'],
						'size'=>			$db['settings']['library']['size'],
						'dest_path'=>		$db['settings']['library']['dest_path'],
						'thumb_path'=>		$db['settings']['library']['thumb_path'],
						'thing_id'=>		$db['settings']['library']['thing_id'],
					];
				}

				$settings = [
					'enabled'=>$db['settings']['library_enable'],
					'library'=>$library
				];
			}

			return ( [
				'status'=>'success',
				'library_settings'=>$settings,
			]);

		}else if( $action == "objectCreate" ){ //objects_create_node_on_fly
			if( !isset($post['node']) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}
			$thing = $post['node'];
			if( !is_array( $thing ) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}
			if( !isset( $thing['l'] ) || !isset( $thing['i_of'] ) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}
			if( !is_array( $thing['i_of'] ) || !is_array( $thing['i_of'] ) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}
			if( !isset( $thing['l']['v'] ) || !$thing['l']['v'] ){
				return (["status"=>"fail", "error"=>"Node name missing"]);
			}
			if( !preg_match("/^[a-z0-9\-\_\.\,\ ]{2,200}$/i", $thing['l']['v']) ){
				return (["status"=>"fail", "error"=>"Node Label invalid"]);
			}

			$instance_id = $thing['i_of']['i'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $instance_id) ){
				return (["status"=>"fail", "status"=>"Instance id incorrect"]);
			}
			if( !preg_match("/^[a-z0-9\-\_\.\,\ ]{2,200}$/i", $thing['i_of']['v']) ){
				return (["status"=>"fail", "status"=>"Instance Label invalid"]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$instance_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "status"=>"Instance node not found"]);
			}
			$instance = $res['data'];
			$instance_id = strtoupper($instance['_id']);
			//return ($res);

			if( $instance['i_t']['v'] != "N" ){
				return (["status"=>"fail", "error"=>"Instance is not of type Node. Sub Nodes are not allowed"]);
			}

			if( $instance['l']['v'] == "Root" && $thing['l']['t'] == "GT" ){
				return (["status"=>"fail", "status"=>"Nodes under Root instance should not refer other nodes"]);
			}

			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['i_of.i'=>$instance_id, 'l.v'=>$thing['l']['v']] );
			if( $res['data'] ){
				return ( ["status"=>"fail", "error"=>"A node with same name already exists"]);
			}

			if( $instance['l']['v'] == "Root" || $instance['_id'] == "T1" ){
				if( !isset($instance['series']) ){
					$new_id = "T2";
					$res5 = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ["_id"=>$instance_id ], ["series"=>2] );
				}else{
					$res5 = $this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $instance_id, "series", 1 );
					$new_id = strtoupper("T" . $res5['data']['series']);
					$res5['x'] = "x";
					$res5['i'] = $instance_id;
					//return ($res5);
				}
				$thing['_id'] = $new_id;
			}else{
				if( !isset($instance['series']) ){
					$new_id = $instance_id."T1";
					$res5 = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ["_id"=>$instance_id ], ["series"=>1] );
				}else{
					$res5 = $this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $instance_id, "series", 1 );
					$res5['xx'] = "xx";
					$new_id = $instance_id."T" . $res5['data']['series'];
					//return ($res5);
				}
				$thing['_id'] = $new_id;
			}
			
			$thing['i_t']=["t"=>"T", "v"=>"N"];
			$thing['m_i']=date("Y-m-d H:i:s");
			$thing['m_u']=date("Y-m-d H:i:s");
			$res = $this->s2_nnnnnnnnoc->insert( $this->s2_sgniht_hparg, $thing );
			$res2 = $this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $instance_id, "cnt", 1 );

			$this->send_to_keywords_queue($this->s2_dddi_hparg, $res['inserted_id'] );

			event_log( "objects", "create_on_fly", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$res['inserted_id'],
			]);
			$res['object'] = $thing;
			return ($res);

		}else if( $action == "objectCreateWithTemplate" ){ //objects_create_with_template
			if( !isset($post['thing']) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}
			$thing = $post['thing'];
			if( !is_array( $thing ) ){
				return (["status"=>"fail", "error"=>"Data missing 2"]);
			}
			if( !isset( $thing['l'] ) || !isset( $thing['i_t'] ) || !isset( $thing['i_of'] ) || !isset( $thing['props'] ) ){
				return (["status"=>"fail", "error"=>"Data missing 3"]);
			}
			if( !is_array( $thing['l'] ) || !is_array( $thing['i_t'] ) || !is_array( $thing['i_of'] ) || !is_array( $thing['props'] ) ){
				return (["status"=>"fail", "error"=>"Data incorrect format 1"]);
			}
			if( !isset( $thing['l']['v'] ) || !$thing['l']['v'] ){
				return (["status"=>"fail", "error"=>"Node name missing"]);
			}
			if( !isset( $thing['l']['t'] ) ){
				return (["status"=>"fail", "error"=>"Node DataType missing"]);
			}
			if( $thing['l']['t'] != "T" && $thing['l']['t'] != "GT" ){
				return (["status"=>"fail", "error"=>"Node DataType Invalid"]);
			}
			if( $thing['l']['t'] == "GT" ){
				if( !isset( $thing['l']['i'] ) || !$thing['l']['i'] ){
					return (["status"=>"fail", "error"=>"Node Link ID missing"]);
				}
				if( !preg_match("/^[a-z0-9]{2,24}$/i", $thing['l']['i']) ){
					return (["status"=>"fail", "error"=>"Node Link ID invalid"]);
				}
			}
			if( !preg_match("/^[a-z0-9\-\_\.\,\ ]{2,200}$/i", $thing['l']['v']) ){
				return (["status"=>"fail", "error"=>"Node Label invalid"]);
			}

			if( !isset( $thing['i_t'] ) ){
				return ( ["status"=>"fail", "error"=>"Node type missing"]);
			}
			if( !isset( $thing['i_t']['t'] ) || !isset( $thing['i_t']['v'] ) ){
				return ( ["status"=>"fail", "error"=>"Node type missing"]);
			}
			if( $thing['i_t']['t'] != "T" || !preg_match("/^(N|L|M|D)$/", $thing['i_t']['v'] ) ){
				return ( ["status"=>"fail", "error"=>"Node type missing"]);
			}
			if( $thing['i_t']['t'] == "L" ){
				if( !isset($thing['z_t']) ){
					return ( ["status"=>"fail", "error"=>"Template is must for a DataSet"]);
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
				return (["status"=>"fail", "error"=>"Data missing"]);
			}

			$instance_id = $thing['i_of']['i'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $instance_id) ){
				return (["status"=>"fail", "status"=>"Instance id incorrect"]);
			}
			if( !preg_match("/^[a-z0-9\-\_\.\,\ ]{2,200}$/i", $thing['i_of']['v']) ){
				return (["status"=>"fail", "status"=>"Instance Label invalid"]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$instance_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "status"=>"Instance node not found"]);
			}
			$instance = $res['data'];
			$instance_id = strtoupper($instance['_id']);

			if( $instance['l']['v'] == "Root" && $thing['l']['t'] == "GT" ){
				return (["status"=>"fail", "status"=>"Nodes under Root instance should not refer other nodes"]);
			}

			if( isset($instance['z_t']) ){
				if( !isset( $thing['props'] ) ){
					return ( ["status"=>"fail", "error"=>"Properties Data missing"]);
				}
				if( !is_array( $thing['props'] ) ){
					return ( ["status"=>"fail", "error"=>"Properties Data missing"]);
				}
			}

			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['i_of.i'=>$instance_id, 'l.v'=>$thing['l']['v']] );
			if( $res['data'] ){
				return ( ["status"=>"fail", "error"=>"A node with same name already exists"]);
			}

			if( $instance['l']['v'] == "Root" || $instance['_id'] == "T1" ){
				if( !isset($instance['series']) ){
					$new_id = "T2";
					$res5 = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ["_id"=>$instance_id ], ["series"=>2] );
				}else{
					$res5 = $this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $instance_id, "series", 1 );
					$new_id = "T" . $res5['data']['series'];
				}
				$thing['_id'] = $new_id;
			}else{
				if( !isset($instance['series']) ){
					$new_id = $instance_id."T1";
					$res5 = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ["_id"=>$instance_id ], ["series"=>1] );
				}else{
					$res5 = $this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $instance_id, "series", 1 );
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

			//return ( $thing['props']);

			$z_t = [];
			if( isset($thing['z_t']) ){
				foreach( $thing['z_t'] as $i=>$j ){
					if( !isset($j['name']) || !isset($j['type']) ){
						return ( ["status"=>"fail", "error"=>"Template error: " . $i ]);
					}
					if( !$j['name']['v'] || !$j['type']['k'] ){
						return ( ["status"=>"fail", "error"=>"Template error: " . $i ]);
					}
					$z_t[ $i ] = ['l'=>$j['name'],'t'=>$j['type'],'e'=>false,'m'=>false];
					if( $j['type']['k'] =="GT" ){
						if( !isset($j['i_of']) ){
							return ( ["status"=>"fail", "error"=>"Template error: " . $i . " Graph instance" ]);
						}
						if( !$j['i_of']['i'] || !$j['i_of']['v'] ){
							return ( ["status"=>"fail", "error"=>"Template error: " . $i . " Graph instance" ]);
						}
						$z_t[ $i ]['i_of'] = $j['i_of'];
					}
				}
				$thing['z_t'] = $z_t;
			}

			$thing['m_i']=date("Y-m-d H:i:s");
			$thing['m_u']=date("Y-m-d H:i:s");
			$res = $this->s2_nnnnnnnnoc->insert( $this->s2_sgniht_hparg, $thing );
			if( $res['status'] == "success" ){
				$res2 = $this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $instance_id, "cnt", 1 );
				$this->send_to_keywords_queue($this->s2_dddi_hparg, $res['inserted_id'] );
				event_log( "objects", "create_with_template", [
					"app_id"=>$app_id,
					"graph_id"=>$this->s2_dddi_hparg,
					"object_id"=>$res['inserted_id'],
				]);
				$res['object'] = $thing;
				return ( $res);
			}else{
				$res['object'] = $thing;
				return ( $res);
			}

		}else if( $action == "objectLabelUpdate" ){ //objects_edit_label

			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}
			$object = $res['data'];
			if( !isset($post['label']) ){
				return ( ["status"=>"fail", "error"=>"Need Label"]);
			}else if( !is_array($post['label']) ){
				return ( ["status"=>"fail", "error"=>"Need Label"]);
			}else if( !isset($post['label']['t']) || !isset($post['label']['v']) ){
				return ( ["status"=>"fail", "error"=>"Need Label"]);
			}
			$label = $post['label'];

			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, [
				'i_of.i'=>$object['i_of']['i'], 
				'l.v'=>$label['v'], 
				'_id'=>['$ne'=>$object_id] 
			]);
			if( $res['data'] ){
				return (["status"=>"fail", "error"=>"Duplicate Node Exists"]);
			}

			$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id ], [
				'l'=>$label,
				'updated'=>date("Y-m-d H:i:s")
			]);

			$this->send_to_keywords_queue($this->s2_dddi_hparg, $object_id);

			event_log( "objects", "edit_label", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
			]);

			return ( $res );
		}else if( $action == "objectTypeUpdate" ){ //objects_edit_type

			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}
			$object = $res['data'];
			$current_type = $object['i_t']['v'];
			if( !isset($post['type']) ){
				return (["status"=>"fail", "error"=>"Need type"]);
			}else if( !is_array($post['type']) ){
				return (["status"=>"fail", "error"=>"Need Type"]);
			}else if( !isset($post['type']['t']) || !isset($post['type']['v']) ){
				return (["status"=>"fail", "error"=>"Need Type"]);
			}
			$type = $post['type'];

			if( $current_type == "N" && $type['v'] != "N" ){
				if( isset($object['cnt']) && $object['cnt'] > 0 ){
					return (["status"=>"fail", "error"=>"There are nodes " . $object['cnt'] . " under this object"]);
				}
			}
			if( $current_type == "L" && $type['v'] != "L" ){
				if( isset($object['cnt']) && $object['cnt'] > 0 ){
					return (["status"=>"fail", "error"=>"There are records " . $object['cnt'] . " under this DataSet"]);
				}
			}

			$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id ], [
				'i_t'=>$type,
				'updated'=>date("Y-m-d H:i:s")
			]);

			$this->send_to_keywords_queue($this->s2_dddi_hparg, $object_id);

			event_log( "objects", "edit_type", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
			]);

			return ( $res );

		}else if( $action == "objectAliasUpdate" ){ //objects_edit_alias

			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}
			$object = $res['data'];
			if( !isset($post['alias']) ){
				return (["status"=>"fail", "error"=>"Need alias"]);
			}else if( !is_array($post['alias']) ){
				return (["status"=>"fail", "error"=>"Need alias"]);
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
						return (["status"=>"fail", "error"=>"Label and Alias should be different"]);
					}
					if( in_array(strtolower($v['v']), $als) ){
						array_splice($post['alias'],$i,1);$i--;
					}else{
						$als[] = strtolower($v['v']);
					}
				}
			}
			//print_r( $als );exit;
			//return $post;
			if( sizeof($post['alias']) ){
				$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id ], [
					'al'=>$post['alias'],
					'updated'=>date("Y-m-d H:i:s")
				]);
			}else{
				$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id ], [
					'$unset'=>[ 'al'=>true ],
					'$set'=>['updated'=>date("Y-m-d H:i:s")],
				]);
			}
			$this->send_to_keywords_queue($this->s2_dddi_hparg, $object_id);
			event_log( "objects", "edit_alias", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
			]);
			return ( $res );

		}else if( $action == "objectInstanceUpdate" ){ //objects_edit_i_of

			//return $post;

			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			$object = $res['data'];
			if( !isset($post['i_of']) ){
				return (["status"=>"fail", "error"=>"Need Instance Of"]);
			}else if( !is_array($post['i_of']) ){
				return (["status"=>"fail", "error"=>"Need Instance Of"]);
			}else if( !isset($post['i_of']['t']) || !isset($post['i_of']['v']) ){
				return (["status"=>"fail", "error"=>"Need Instance Of"]);
			}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $post['i_of']['i']) && !preg_match("/^[0-9]+$/i", $post['i_of']['i'] ) ){
				return (["status"=>"fail", "error"=>"Instance id incorrect"]);
			}
			$i_of = $post['i_of'];
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$i_of['i']] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Instance not found"]);
			}
			$instance = $res['data'];

			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['i_of.i'=>$i_of['i'], 'l.v'=>$object['l']['v'], '_id'=>['$ne'=>$object_id] ] );
			if( $res['data'] ){
				return (["status"=>"fail", "error"=>"Duplicate Node Exists in Instance: " . $i_of['v']]);
			}
			$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id ], [
				'i_of'=>$i_of,
				'updated'=>date("Y-m-d H:i:s")
			]);
			$this->send_to_keywords_queue($this->s2_dddi_hparg, $object_id);

			$res2 = $this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $object['i_of']['i'], "cnt", -1 );
			$res2 = $this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $post['i_of']['i'], "cnt", 1 );

			event_log( "objects", "edit_instance", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
			]);

			return ( $res );

		}else if( $action == "objectPropertiesUpdate" ){ //objects_save_props

			//return ['s'=>1];
			//return $post;

			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			$res2 = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$res['data']['i_of']['i']] );
			if( !$res2['data'] ){
				return (["status"=>"fail", "error"=>"parent not found"]);
			}
			$parent = $res2['data'];

			if( !isset($post['props']) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}else if( !is_array($post['props']) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}

			$props = $post['props'];

			foreach( $props as $field=>$values ){
				if( !is_array($values) ){
					return (["status"=>"fail", "error"=>"Property `" . $field . "` has invalid value"]);
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
										return [
											"status"=>"fail", 
											"error"=>"Property `" . $field . "` item: ".($pi+1)." property: " . $fn['l']['v'] . " has invalid value: ".json_encode($pd['v'][$fd]) 
										];
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
								return (["status"=>"fail", "error"=>"Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd)]);
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
			
			$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id], $data );

			event_log( "objects", "props_save", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
			]);

			return ( $res);

		}else if( $action == "objectHtmlUpdate" ){ //objects_save_object_html
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}

			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Thing not found"]);
			}
			if( !isset($post['body']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 1"]);
			}else{
				$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id], [
					'body'=>[
						'html'=>$post['body']['v'],
						'options'=>[]
					]
				]);
				event_log( "objects", "html_save", [
					"app_id"=>$this->s2_dddddi_ppa,
					"graph_id"=>$this->s2_dddi_hparg,
					"object_id"=>$object_id,
				]);
				$res['cnt'] = $post['cnt'];
				return ( $res );
			}

		}else if( $action == "objectNodesTruncate" ){ //objects_nodes_empty
			if( !isset($post['instance_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['instance_id']['t']) || !isset($post['instance_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$instance_id = $post['instance_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $instance_id) && !preg_match("/^[0-9]+$/i", $instance_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$instance_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Instance not found"]);
			}

			while( 1 ){
				$res = $this->s2_nnnnnnnnoc->find( $this->s2_sgniht_hparg, ['i_of.i'=>$instance_id], ['limit'=>500] );
				if( sizeof($res['data']) == 0 ){
					break;
				}
				foreach( $res['data'] as $i=>$j ){
					$this->s2_nnnnnnnnoc->delete_one( $this->s2_sgniht_hparg,["i_of.i"=>$instance_id, "_id"=>$j['_id']] );
					$this->send_to_keywords_delete_queue($this->s2_dddi_hparg,$j['_id']);
					event_log( "objects", "delete", [
						"app_id"=>$app_id,
						"graph_id"=>$this->s2_dddi_hparg,
						"object_id"=>$j['_id'],
					]);
				}
			}
			$this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ["_id"=> $instance_id], ["cnt"=>0] );

			return (['status'=>"success"]);

		}else if( $action == "objectDelete" ){ //objects_delete_node
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			$thing = $res['data'];
			$instance_id = $thing['i_of']['i'];

			// if( $thing['i_t'] != "N" ){
			// 	return (["status"=>"fail", "error"=>"Incorrect node type"]);
			// }
			if( $thing['cnt'] > 0 ){
				return (["status"=>"fail", "error"=>"There are nested ".$thing['cnt']." nodes under ". $thing['l']['v']]);
			}

			$this->s2_nnnnnnnnoc->delete_one( $this->s2_sgniht_hparg,[
				"_id"=>$object_id
			]);
			$this->send_to_keywords_delete_queue($this->s2_dddi_hparg, $object_id);
			event_log( "objects", "delete", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
			]);
			$this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $instance_id, "cnt", -1 );
			return (['status'=>"success"]);

		}else if( $action == "objectConverToDataset" ){ //objects_ops_convert_to_dataset
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			$thing = $res['data'];
			//return $thing;
			$instance_id = $thing['i_of']['i'];

			if( !isset($thing['z_t']) ){
				$thing['z_t'] = [];
				$thing['z_o'] = [];
				$thing['z_n'] = 1;
			}

			if( $thing['i_t']['v'] == "L" ){
				return (["status"=>"fail", "error"=>"Object is already a dataset" ]);
			}else if( $thing['i_t']['v'] != "N" ){
				return (["status"=>"fail", "error"=>"Source Object must be type of Node" ]);
			}

			if( !isset($post['label_to']) ){
				return (["status"=>"fail", "error"=>"Need new Label Property id"]);
			}else if( !isset($post['label_to']['v']) ){
				return (["status"=>"fail", "error"=>"Need new Label Property id"]);
			}else if( !preg_match("/^[a-z0-9\.\,\-\_\ ]{2,100}$/i", $post['label_to']['v']) ){
				return (["status"=>"fail", "error"=>"Property name should be plain text"]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}

			$new_prop = trim($post['label_to']['v']);
			foreach( $thing['z_t'] as $propf=>$p ){
				if( strtolower($new_prop) == strtolower($p['l']['v']) ){
					return (["status"=>"fail", "error"=>"Property name `".$new_prop."` already exists!" ]);
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

			$ures = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ["_id"=>$object_id], [
				'i_t.v'=>"L",
				"z_t.". $np=> ["key"=> $np, "l"=> ["t"=>"T", "v"=> $new_prop], "t"=> ["t"=>"KV", "k"=>"T", "v"=>"text"], "m"=> ["t"=>"B", "v"=> "true"] ],
				"z_o"=> $z_o,
				"z_n"=> $z_n
			]);

			$this->s2__sgniht_hpargdataset = $this->s2_sgniht_hparg . "_" . $object_id;

			$rec_cnt = 0;
			while( 1 ){
				$res = $this->s2_nnnnnnnnoc->find( $this->s2_sgniht_hparg, ['i_of.i'=>$object_id], ['limit'=>100] );
				if( !$res['data'] || sizeof($res['data']) == 0 ){
					break;
				}
				foreach( $res['data'] as $i=>$j ){
					$rec_cnt++;
					$id = $j['_id'];
					$j[ "props" ][ $np ] = [$j['l']];
					$rec_id = uniqid();
					$ires = $this->s2_nnnnnnnnoc->insert( $this->s2__sgniht_hpargdataset, [
						"_id"=>$rec_id,
						"props"=>$j['props'],
						"m_i"=>$j['m_i'],
						"m_u"=>$j['m_u'],
					]);
					if( $ires['inserted_id'] ){
						$this->send_to_records_queue($this->s2_dddi_hparg, $object_id, $rec_id, "record_create" );
						$this->s2_nnnnnnnnoc->delete_one( $this->s2_sgniht_hparg, ['_id'=>$id]);
						$this->send_to_keywords_delete_queue($this->s2_dddi_hparg,$id);
						event_log( "objects", "delete", [
							"app_id" => $app_id,
							"graph_id" => $this->s2_dddi_hparg,
							"object_id" => $id,
						]);
					}
				}
			}

			if( $thing['cnt'] != $rec_cnt ){
				$this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id], ['cnt'=>$rec_cnt] );
			}

			event_log( "objects", "convert", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
				"from"=>"N",
				"to"=>"L",
			]);
			return (['status'=>"success"]);

		}else if( $action == "objectConverToNode" ){ //objects_ops_convert_to_nodelist
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			$thing = $res['data'];
			$instance_id = $thing['i_of']['i'];

			if( $thing['i_t']['v'] == "N" ){
				return (["status"=>"fail", "error"=>"Object is already a node list" ]);
			}else if( $thing['i_t']['v'] != "L" ){
				return (["status"=>"fail", "error"=>"Source Object must be type of Dataset" ]);
			}

			if( !isset($post['primary_field']) ){
				return (["status"=>"fail", "error"=>"Need NodeID field"]);
			}
			$primary_field = $post['primary_field']['v'];
			if( !isset( $thing['z_t'][ $primary_field ] ) && $primary_field != "default-id" ){
				return (["status"=>"fail", "error"=>"Primary field not found"]);
			}
			if( !isset($post['label_field']) ){
				return (["status"=>"fail", "error"=>"Need Label field"]);
			}
			$label_field = $post['label_field']['v'];
			if( !isset( $thing['z_t'][ $label_field ] ) ){
				return (["status"=>"fail", "error"=>"Label field not found"]);
			}
			$alias_field = $post['alias_field']['v'];

			$this->s2__sgniht_hpargdataset = $this->s2_sgniht_hparg . "_" . $object_id;

			if( $primary_field != "default-id" ){
				$res = $this->s2_nnnnnnnnoc->aggregate( $this->s2__sgniht_hpargdataset, [
					['$group'=>['_id'=>'$props.'.$primary_field.".v", 'cnt'=>['$sum'=>1]] ],
					['$sort'=>["cnt"=>-1]]
				]);
				//print_r( $res );exit;
				if( $res['status'] != "success" ){
					return ($res);
				}
				if( isset($res['data']) && sizeof($res['data']) > 0 ){
					if( $res['data'][0]['cnt'] > 1 ){
						return (["status"=>"fail", "error"=>"Primary field value `" . $res['data'][0]['_id'][0] . "` repeated. " ]);
					}
				}else{
					return (["status"=>"fail", "error"=>"Primary values not found"]);
				}
				foreach( $res['data'] as $i=>$j ){
					if( !preg_match( "/^[a-z0-9]{2,24}$/i", $j['_id'][0] ) ){
						return (["status"=>"fail", "error"=>"Primary field value " . $j['_id'][0] . " is not acceptable"]);
					}
				}
			}

			//echo "xxxx";exit;

			$res = $this->s2_nnnnnnnnoc->aggregate( $this->s2__sgniht_hpargdataset, [
				['$group'=>['_id'=>'$props.'.$label_field.".v", 'cnt'=>['$sum'=>1]] ],
				['$sort'=>["cnt"=>-1]]
			]);
			//return ($res);
			if( $res['status'] != "success" ){
				return ($res);
			}
			if( isset($res['data']) && sizeof($res['data']) > 0 ){
				if( $res['data'][0]['cnt'] > 1 ){
					return (["status"=>"fail", "error"=>"Label field `" . $res['data'][0]['_id'][0] . "` repeated. " ]);
				}
			}else{
				return (["status"=>"fail", "error"=>"Label values not found"]);
			}
			foreach( $res['data'] as $i=>$j ){
				if( !preg_match( "/^[a-z][a-z0-9\-\.\_\,\ \(\)\@\!\&\:]{1,200}$/i", $j['_id'][0] ) ){
					return (["status"=>"fail", "error"=>"Label field value " . $j['_id'][0] . " is not acceptable"]);
				}
			}

			$rec_cnt = 0;$success = 0;$failed = 0; $failed_reasons = [];
			while( 1 ){
				$res = $this->s2_nnnnnnnnoc->find( $this->s2__sgniht_hpargdataset, [], ['limit'=>100] );
				if( !$res['data'] || sizeof($res['data']) == 0 ){
					break;
				}
				//print_r( $res );
				foreach( $res['data'] as $i=>$j ){
					$rec_cnt++;
					if( $primary_field =="default-id" ){
						$res5 = $this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $object_id, "series", 1 );
						$new_id = $object_id."T" . $res5['data']['series'];
						$rec_id = $new_id;
					}else{
						$rec_id = $j[ "props" ][ $primary_field ][0]['v'];
					}
					$al = [];
					if( isset($j[ "props" ][ $alias_field ]) ){
						$al[] = $j[ "props" ][ $alias_field ][0];
					}
					$d = [
						"_id"=>$rec_id,
						"i_t"=>["t"=>"T", "v"=>"N"],
						"l"=>$j[ "props" ][ $label_field ][0],
						"i_of"=>["t"=>"GT", "i"=>$object_id, "v"=>$thing['l']['v']],
						"props"=>$j['props'],
						"m_i"=>$j['m_i'],
						"m_u"=>$j['m_u'],
					];
					if( sizeof($al) ){
						$d['al'] = $al;
					}
					//print_r( $d );exit;
					$ires = $this->s2_nnnnnnnnoc->insert( $this->s2_sgniht_hparg, $d);
					if( $ires['status'] == "success" ){
						if( $ires['inserted_id'] ){
							$this->s2_nnnnnnnnoc->delete_one( $this->s2__sgniht_hpargdataset, ['_id'=>$j['_id']] );
							$this->send_to_records_queue($this->s2_dddi_hparg, $object_id, $j['_id'], "record_delete" );
							$this->send_to_keywords_queue($this->s2_dddi_hparg, $ires['inserted_id'] );
							event_log( "objects", "create", [
								"app_id" => $app_id,
								"graph_id" => $this->s2_dddi_hparg,
								"object_id" => $rec_id,
							]);
							$success++;
						}else{
							return ( $ires );
						}
					}else{
						$failed++;
						$failed_reasons[ $j['_id'] ] = $ires['error'];
						$this->s2_nnnnnnnnoc->delete_one( $this->s2__sgniht_hpargdataset, ['_id'=>$j['_id']] );
						$this->send_to_records_queue( $this->s2_dddi_hparg, $object_id, $j['_id'], "record_delete" );
					}
				}
			}
			//exit;
			$ures = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ["_id"=>$object_id], [
				'i_t.v'=>"N",
			]);
			if( $thing['cnt'] != $rec_cnt ){
				$this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id], ['cnt'=>$rec_cnt] );
			}
			event_log( "objects", "convert", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
				"from"=>"L",
				"to"=>"N",
			]);
			return ([
				"status"=>"success",
				"success"=>$success,
				"failed"=>$failed,
				"failed_reasons"=>$failed_reasons
			]);

		}else if( $action == "objectSetIcon" ){ //objects_set_icon
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Thing not found"]);
			}
			if( !isset($post['ic']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 1"]);
			}else if( !isset($post['ic']['t']) || !isset($post['ic']['v']) || !isset($post['ic']['it']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 1"]);
			}

			if( $post['ic']['t'] != "IC" ){
				return (["status"=>"fail", "error"=>"Incorrect data 5"]);
			}
			if( !preg_match( "/^(emoji|svg|font|flag|img|img1|img2|img3|imgf)$/", $post['ic']['it'] ) ){
				return (["status"=>"fail", "error"=>"Incorrect data 6"]);
			}

			$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id], [
				'ic'=>$post['ic'],
			]);
			event_log( "objects", "icon_save", [
				"app_id"=>$this->s2_dddddi_ppa,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
			]);
			return ( $res );
			exit;

		}else if( $action == "objectTemplatePropertyCreate" ){ //objects_object_add_field

			//return $post;

			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			if( !isset($post['property']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 1"]);
			}else if( !isset($post['property']['v']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 1"]);
			}
			$property_id = $post['property']['v'];
			if( !preg_match("/^p[0-9]+$/",$property_id) ){
				return (["status"=>"fail", "error"=>"Incorrect data 2"]);
			}
			if( !isset($post['template']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 3"]);
			}else if( !is_array($post['template']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 4"]);
			}else if( !isset($post['template']['label'])  || !isset($post['template']['type'])  || !isset($post['template']['mandatory']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 5"]);
			}
			$post['template'] = [
				'l'=>$post['template']['label'],
				't'=>$post['template']['type'],
				'm'=>$post['template']['mandatory'],
				'e'=>false,
			];
			if( isset($res['data']['z_t'][ $property_id ]) ){
				return (["status"=>"fail", "error"=>"Field key ".$property_id." already exists"]);
			}
			$n = intval(str_replace("p","",$property_id));
			$post['z_n'] = $res['data']['z_n'];
			if( $n > $res['data']['z_n'] ){
				$post['z_n'] = $n;
			}
			$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id], [
				'$set'=>[
					'z_t.'.$property_id=>$post['template'],
					'z_n'=>$post['z_n']
				],
				'$push'=>['z_o'=>$property_id],
			]);
			event_log( "objects", "field_add", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
				"property"=>$property_id
			]);
			return ($res);

		}else if( $action == "objectTemplatePropertyUpdate" ){ //objects_save_object_z_t
			return $post;
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			if( !isset($post['property']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 1"]);
			}else if( !isset($post['property']['v']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 1"]);
			}
			$property_id = $post['property']['v'];
			if( !preg_match("/^p[0-9]+$/i",$property_id) ){
				return (["status"=>"fail", "error"=>"Incorrect data 2"]);
			}

			if( !isset($post['template']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 3"]);
			}else if( !is_array($post['template']) ){
				return (["status"=>"fail", "error"=>"Incorrect data 4"]);
			}
			$post['template'] = [
				'l'=>$post['template']['label'],
				't'=>$post['template']['type'],
				'm'=>$post['template']['mandatory'],
				'e'=>false,
			];

			$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id], [
				'z_t.'. $property_id=>$post['template'],
			]);
			event_log( "objects", "template_save", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
				"property"=>$property_id,
			]);
			return ($res);

		}else if( $action == "objectTemplatePropertyDelete" ){ //objects_delete_field
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			if( !isset($post['property']) ){
				return (["status"=>"fail", "error"=>"Need Prop ID"]);
			}else if( !preg_match("/^p[0-9]+$/i", $post['property']) ){
				return (["status"=>"fail", "error"=>"Need Prop ID"]);
			}

			$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ['_id'=>$object_id], [
				'$unset'=>[
					'z_t.' . $post['property']=>true,
				],
				'$pull'=>[
					'z_o'=>$post['property']
				]
			]);

			event_log( "objects", "field_delete", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
				"property"=>$post['property']
			]);

			return ($res);

		}else if( $action == "objectTemplateEnable" ){ //objects_save_enable_z_t
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}
			
		}else if( $action == "objectTemplateOrderUpdate" ){ //objects_save_z_o
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

		}else if( $action == "dataSetRecordCreate" ){ //objects_dataset_record_create
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			$res2 = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$res['data']['i_of']['i']] );
			if( !$res2['data'] ){
				return (["status"=>"fail", "error"=>"parent not found"]);
			}
			$parent = $res2['data'];

			if( !isset($post['record_properties']) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}else if( !is_array($post['record_properties']) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}

			$props = $post['record_properties'];

			foreach( $props as $field=>$values ){
				if( !is_array($values) ){
					return (["status"=>"fail", "error"=>"Property `" . $field . "` has invalid value"]);
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
										return (['status'=>"fail", "error"=>"Property `" . $field . "` item: ".($pi+1)." property: " . $fn['l']['v'] . " has invalid value: ".json_encode($pd['v'][$fd]) ]);
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
								return (["status"=>"fail", "error"=>"Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd)]);
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
			
			$res = $this->s2_nnnnnnnnoc->insert( $this->s2_sgniht_hparg. "_". $object_id, $data );

			event_log( "objects", "record_create", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
				"record_id"=>$res['inserted_id'],
			]);
			$this->send_to_records_queue($this->s2_dddi_hparg, $object_id, $res['inserted_id'], "record_create" );

			return ($res);

		}else if( $action == "dataSetRecordUpdate" ){ //objects_dataset_record_save_props
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			if( !isset($post['record_id']) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			if( !isset($post['record_id']['t']) || !isset($post['record_id']['v']) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			$record_id = $post['record_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $record_id) && !preg_match("/^[0-9]+$/i", $record_id) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			$res2 = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$res['data']['i_of']['i']] );
			if( !$res2['data'] ){
				return (["status"=>"fail", "error"=>"parent not found"]);
			}
			$parent = $res2['data'];

			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg . "_". $thing_id, ['_id'=>$record_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Record not found"]);
			}

			if( !isset($post['record_properties']) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}else if( !is_array($post['record_properties']) ){
				return (["status"=>"fail", "error"=>"Data missing"]);
			}

			$props = $post['record_properties'];

			foreach( $props as $field=>$values ){
				if( !is_array($values) ){
					return (["status"=>"fail", "error"=>"Property `" . $field . "` has invalid value"]);
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
										return(["status"=> "fail", "error"=> "Property `" . $field . "` item: ".($pi+1)." property: " . $fn['l']['v'] . " has invalid value: ".json_encode($pd['v'][$fd]) ]);
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
								return (["status"=>"fail", "error"=>"Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd)]);
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
			
			$res = $this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg. "_". $object_id, ['_id'=>$record_id], $data );

			event_log( "objects", "record_props_save", [
				"app_id"=>$app_id,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$object_id,
				"record_id"=>$record_id,
			]);

			return ($res);

		}else if( $action == "dataSetRecordDelete" ){ //objects_dataset_record_delete
			
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			if( !isset($post['record_id']) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			if( !isset($post['record_id']['t']) || !isset($post['record_id']['v']) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			$record_id = $post['record_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $record_id) && !preg_match("/^[0-9]+$/i", $record_id) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Object not found"]);
			}

			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg . "_" . $object_id, ['_id'=>$record_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Record not found"]);
			}

			$res = $this->s2_nnnnnnnnoc->delete_one( $this->s2_sgniht_hparg . "_" . $object_id, ['_id'=>$record_id] );

			$this->send_to_records_queue($this->s2_dddi_hparg, $object_id, $record_id, "record_delete" );

			return ($res);

		}else if( $action == "dataSetTruncate" ){ //objects_records_empty
			if( !isset($post['object_id']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			if( !isset($post['object_id']['t']) || !isset($post['object_id']['v']) ){
				return (["status"=>"fail", "error"=>"Object Id Invalid" ]);
			}
			$object_id = $post['object_id']['v'];
			if( !preg_match("/^[a-z0-9]{2,24}$/i", $object_id) && !preg_match("/^[0-9]+$/i", $object_id) ){
				return (["status"=>"fail", "error"=>"Record Id Invalid" ]);
			}
			$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['_id'=>$object_id] );
			if( !$res['data'] ){
				return (["status"=>"fail", "error"=>"Instance not found"]);
			}

			while( 1 ){
				$res = $this->s2_nnnnnnnnoc->find( $this->s2_sgniht_hparg . "_". $object_id, [], ['limit'=>500, 'sort'=>['_id'=>1]] );
				if( sizeof($res['data']) == 0 ){
					break;
				}
				foreach( $res['data'] as $i=>$j ){
					$this->s2_nnnnnnnnoc->delete_one( $this->s2_sgniht_hparg . "_". $object_id, ["_id"=>$j['_id']] );
					$this->send_to_records_queue($this->s2_dddi_hparg, $object_id, $j['_id'], "record_delete" );
				}
			}

			$this->s2_nnnnnnnnoc->update_one( $this->s2_sgniht_hparg, ["_id"=> $object_id], ["cnt"=>0] );

			return (['status'=>"success"]);

			/* Import Actions */

		}

	}

	public function find_or_insert( $instance, $thing_name ){
		if( isset($this->data_cache[ $instance['i'] . "." . $thing_name ]) ){
			return $this->data_cache[ $instance['i'] . "." . $thing_name ];
		}
		$res = this->s2_nnnnnnnnoc->find_one( $this->s2_sgniht_hparg, ['i_of.i'=>$instance['i'], 'l.v'=>$thing_name], ['projection'=>['_id'=>1] ] );
		if( $res['data'] ){
			$this->data_cache[ $instance['i'] . "." . $thing_name ] = $res['data']['_id'];
			return $res['data']['_id'];
		}else{

			if( $instance['v'] == "Root" || $instance['i'] == "T1" ){
				$res5 = this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $instance['i'], "series", 1 );
				$new_id = "T" . $res5['data']['series'];
			}else{
				$res5 = this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $instance['i'], "series", 1 );
				$new_id = $instance['i']."T" . $res5['data']['series'];
			}

			$instance['t'] = "GT";
			$res = this->s2_nnnnnnnnoc->insert( $this->s2_sgniht_hparg, [
				"_id"=>$new_id,
				'i_of'=>$instance, 
				'l'=>['t'=>"T", "v"=>$thing_name],
				'i_t'=>['t'=>"T", "v"=>"N"],
				'm_i'=>date("Y-m-d H:i:s"),
				'm_u'=>date("Y-m-d H:i:s")
			]);
			$node_id = $res['inserted_id'];
			$this->data_cache[ $instance['i'] . "." . $thing_name ] = $node_id;
			$resinc = this->s2_nnnnnnnnoc->increment( $this->s2_sgniht_hparg, $instance['i'], "cnt", 1 );
			$this->send_to_keywords_queue( $node_id );
			event_log( "objects", "create", [
				"app_id"=>$this->s2_dddddi_ppa,
				"graph_id"=>$this->s2_dddi_hparg,
				"object_id"=>$node_id,
			]);
			$this->data_cache[ $node_id ] = [
				'i_of'=>$instance, 
				'l'=>['t'=>"T", "v"=>$thing_name],
				'i_t'=>['t'=>"T", "v"=>"N"],
				'm_i'=>date("Y-m-d H:i:s"),
				'm_u'=>date("Y-m-d H:i:s")
			];
			return $node_id;
		}
	}
}

