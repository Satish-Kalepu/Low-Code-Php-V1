<?php

class graph_processor{
	function find_permutations( $label ){
		//echo "===" . $label . "<BR>";
		$perms = [];
		$perms[ $label ] = 1;
		$x = preg_split("/[\W]+/", $label);
		if( sizeof($x) > 1 ){
			for($i=0;$i<sizeof($x);$i++){
				if( sizeof($perms) > 10 ){break;}
				$v = array_pop($x);
				//echo "last word: " . $v . "-<BR>";
				if( sizeof($x) > 1 ){
					$subperms = $this->find_permutations( implode(" ", $x) );
					//echo "sub permutations: <BR>";
					//print_r( $subperms );
					foreach( $subperms as $si=>$sv ){
						//echo "perm: " . $v . " " . $si . "<BR>";
						if( sizeof($perms) > 10 ){break;}
						$perms[ $v . " " . $si ] = 1;
					}
					array_splice($x,0,0,$v);
				}else{
					array_splice($x,0,0,$v);
					//echo "perm: " . implode(" ", $x) . "<BR>";
					$perms[ implode(" ", $x) ]= 1;
				}
			}
		}
		return $perms;
	}
	function process_task( $task ){
		global $mongodb_con; global $db_prefix; global $graph_id;

		global $graph_things;
		global $graph_keywords;
		global $graph_queue;
		global $graph_links;
		global $graph_mentions;

		$log = [];

		if( $task['data']['action'] == "thing_update" ){

			$log[] = "finding " . $task['data']['thing_id'];
			$res = $mongodb_con->find_one( $graph_things, ["_id"=>$task['data']['thing_id']] );
			if( !$res['data'] ){
				$res = $mongodb_con->delete_many( $graph_keywords, ['tid'=>$task['data']['thing_id']] );
				return ['statusCode'=>200, 'body'=>['status'=>"fail", "data"=>"node ". $task['data']['thing_id'] . " is not found", "res"=>$res ], 'log'=>$log ];
			}
			$thing = $res['data'];

			if( $thing['l']['t'] == "GT" ){
				$log[] = "label is linked " . $thing['l']['i'];
				$res = $mongodb_con->find_one( $graph_things, ["_id"=>$thing['l']['i']], ['projection'=>['l'=>1, 'i_of'=>1, 'p_of'=>1] ] );
				if( $res['data'] ){
					$dest_thing_id = $thing['l']['i'];
					$dest_thing_name = $thing['l']['v'];
					$linked = $thing['i_of'];
					if( !isset($res['data']['p_of']) ){
						$pof = [];
					}else{
						$pof = $res['data']['p_of'];
					}
					$pof[ $linked['i'] ] = [
						"t"=> "GT", 
						"i"=> $linked['i'],
						"v"=> $linked['v']
					];
					$log[] = $pof;
					$pres = $mongodb_con->update_one( $graph_things, ["_id"=>$thing['l']['i']], ['p_of'=>$pof] );
					$log[] = $pres;

					$log[] = "making link: " . $dest_thing_id . "-" . $thing['_id'] . "-label";
					$mongodb_con->update_one( $graph_links, [
						'_id'=>$dest_thing_id . "-" . $thing['_id'] . "-label"
					], [
						't1'  =>['t'=>"GT", 'i'=>$dest_thing_id, "v"=>$dest_thing_name ],
						't2'   =>['t'=>"GT", 'i'=>$thing['_id'], "v"=>$thing['l']['v']],
						't'=>"label",
						'm_u' =>date("Y-m-d H:i:s"),
					], [
						'upsert'=>true
					]);
				}
			}

			$log[] = "finding parent " . $thing['i_of']['i'];
			$res = $mongodb_con->find_one( $graph_things, ["_id"=>$thing['i_of']['i']], ['projection'=>['z_t']] );
			if( !$res['data'] ){
				return ['statusCode'=>200, 'body'=>['status'=>"fail", "data"=>"parent node ". $thing['i_of']['i'] . " is not found", "res"=>$res ], 'log'=>$log ];
			}
			$instance = $res['data'];
			//$log[] = $instance;

			foreach( $thing['props'] as $propf=>$propd ){
				foreach( $propd as $pi=>$pd ){
					if( $pd['t'] == "GT" ){
						$log[] = "making link: " . $pd['i'] . "-" . $thing['_id'] . "-" . $propf;
						$mongodb_con->update_one( $graph_links, [
							'_id'=>$pd['i'] . "-" . $thing['_id'] . "-" . $propf
						], [
							't1'  =>['t'=>"GT", 'i'=>$pd['i'], "v"=>$pd['v']],
							't2'   =>['t'=>"GT", 'i'=>$thing['_id'], "v"=>$thing['l']['v']],
							't'=>"props",
							'p'   =>['k'=>$propf,'v'=>$instance['z_t'][$propf]['l']['v']],
							'm_u' =>date("Y-m-d H:i:s"),
						], [
							'upsert'=>true
						]);

						// emp list   name   satish
						// satish_id  name  emplist
					}else if( $pd['t'] == "O" ){
						foreach( $pd['v'] as $obfi=>$obfd ){
							if( $obfd['t'] == "GT" ){
								$log[] = "making link: " . $obfd['i'] . "-" . $thing['_id'] . "-" . $propf;
								$mongodb_con->update_one( $graph_links, [
									'_id'=>$obfd['i'] . "-" . $thing['_id'] . "-" . $propf
								], [
									't1'  =>['t'=>"GT", 'i'=>$obfd['i'], "v"=>$obfd['v']],
									't2'   =>['t'=>"GT", 'i'=>$thing['_id'], "v"=>$thing['l']['v']],
									't'=>"props",
									'p'   =>['k'=>$propf,'v'=>$instance['z_t'][$propf]['l']['v']],
									'm_u' =>date("Y-m-d H:i:s"),
								], [
									'upsert'=>true
								]);
							}
						}
					}
				}
			}

			$res = $mongodb_con->delete_many( $graph_keywords, ['tid'=>$task['data']['thing_id']] );
			$permutations = $this->find_permutations($thing['l']['v']);
			$res2 = $mongodb_con->find( $graph_keywords );
			$ress = [];
			$pcnt = 0;
			foreach( $permutations as $perm=>$j ){
				//if( $pcnt > 5 ){ break; }; $pcnt++;
				$d = [
					"p"=> $perm,
					"l"=> $thing['l']['v'],
					"tid"=>$thing['_id'],
					"pid"=> $thing['i_of']['i'],
					"pl"=> $thing['i_of']['v'],
					"t"=>"p"
				];
				if( $perm == $thing['l']['v'] ){
					$d['m'] = true;
				}
				$r = $mongodb_con->update_one( $graph_keywords, [
					"_id"=> $thing['i_of']['i'] . ":" . strtolower($perm)
				],$d, [
					'upsert'=>true,
				]);
				if( $r['status'] != "success" ){
					
				}
				$ress[] = $r;
			}
			if( isset($thing['al']) ){
				foreach( $thing['al'] as $i=>$j ){
					if( isset($j['v']) ){
						$r = $mongodb_con->update_one( $graph_keywords, [
							"_id"=> $thing['i_of']['i'] . ":" . strtolower($j['v'])
						],[
							"p"=> $j['v'],
							"l"=> $thing['l']['v'],
							"tid"=>$thing['_id'],
							"pid"=> $thing['i_of']['i'],
							"pl"=> $thing['i_of']['v'],
							"t"=>"a"
						], [
							'upsert'=>true,
						]);
						if( $r['status'] != "success" ){
							
						}
						$ress[] = $r;
					}else{
						return ['statusCode'=>500, 'body'=>['status'=>"fail", "data"=>"Alias not found" ], 'log'=>$log ];
					}
				}
			}
			return ['statusCode'=>200, 'body'=>['status'=>"success", "data"=>["permutations"=>array_keys($permutations)], "res"=>$ress ], 'log'=>$log ];

		}else if( $task['data']['action'] == "thing_delete" ){

			$deleted_thing_id = $task['data']['thing_id'];

			$log[] = "finding links for ". $deleted_thing_id;
			$links_res = $mongodb_con->find( $graph_links, ["_id"=>['$gte'=>$deleted_thing_id."-", '$lte'=>$deleted_thing_id."~" ] ]);
			foreach( $links_res['data'] as $ii=>$d ){
				$log[] = $d;

				if( $d['t'] == "props" ){
					$dest_thing_res = $mongodb_con->find_one( $graph_things, ["_id"=> $d['t2']['i'] ], ['projection'=>["props.".$d['p']['k']=>1]] );
					if( $dest_thing_res['data'] ){
						$tp = $dest_thing_res['data']['props'][ $d['p']['k'] ];
						$log[] = "updating props ";
						$log[] = $tp;
						foreach( $tp as $i=>$j ){
							if( $j['t']=="GT" && $j['i'] == $deleted_thing_id ){
								$tp[ $i ] = ['t'=>"T", "v"=>$j['v']];
							}else if( $j['t']=="O" ){
								foreach( $j['v'] as $ii=>$jj ){
									if( $jj['t']=="GT" && $jj['i'] == $deleted_thing_id ){
										$tp[ $i ]['v'][ $ii ] = ['t'=>"T", "v"=>$jj['v']];
									}
								}
							}
						}
						$log[] = "to ";
						$log[] = $tp;
						$ures = $mongodb_con->update_one($graph_things, ["_id"=> $d['t2']['i'] ], [
							"props.".$d['p']['k'] => $tp
						]);
						$log[] = $ures;
					}else{
						$log[] = "linked record not found ". $d['t2']['i'];
					}
					$log[] = "deleting: " .$d['_id'];
					$dres = $mongodb_con->delete_one( $graph_links, ['_id'=>$d['_id']] );
					$log[] = $dres;
				}else if( $d['t'] == "label" ){
					$dest_thing_res = $mongodb_con->find_one( $graph_things, ["_id"=> $d['t2']['i'] ], ['projection'=>["l"=>1,"p_of"=>1]] );
					if( $dest_thing_res['data'] ){
						$l = $dest_thing_res['data']['l'];
						$log[] = $l;
						$l = ['t'=>"T", "v"=>$l['v']];
						$log[] = $l;
						$pres = $mongodb_con->update_one( $graph_things, ["_id"=>$dest_thing_res['data']['_id']], ['l'=>$l] );
						$log[] = $pres;
					}else{
						$log[] = "linked record not found ". $d['t2']['i'];
					}
					$log[] = "deleting: " .$d['_id'];
					$dres = $mongodb_con->delete_one( $graph_links, ['_id'=>$d['_id']] );
					$log[] = $dres;
				}
			}

			$log[] = "delete keywords";
			$res = $mongodb_con->delete_many( $graph_keywords, ['tid'=>$task['data']['thing_id']] );
			$log[] = $res;

			/* deleting mentions */
			$log[] = "Finding mentions";
			$links_res = $mongodb_con->find( $graph_mentions, ["_id"=>['$gte'=>$task['data']['thing_id']."-", '$lte'=>$task['data']['thing_id']."~" ] ]);
			$log[] = "found: ". sizeof($links_res['data']);
			foreach( $links_res['data'] as $ii=>$d ){
				$log[] = $d['_id'];
				$x = explode("-", $d['_id']);
				$thing_id = $x[0];
				$dataset_id = $x[1];
				$record_id = $x[2];
				$propf = $x[3];
				$graph_dataset = $graph_things . "_" . $dataset_id;
				$log[] = $graph_dataset;
				$record_res = $mongodb_con->find_one( $graph_dataset, ["_id"=>$record_id], ['projection'=>['props.'.$propf=>1] ] );
				if( $record_res['data'] ){
					//$log[] = $record_res['data'];
					$propd = $record_res['data'][ 'props' ][ $propf ];
					$log[] = $propd;
					foreach( $propd as $pi=>$pd ){
						if( $propd[ $pi ]['i'] == $thing_id ){
							$propd[ $pi ]['t'] = "T"; unset($propd[ $pi ]['i']);
						}
					}
					$log[] = $propd;
					$record_ures = $mongodb_con->update_one( $graph_dataset, ["_id"=>$record_id], ['props.'.$propf=>$propd] );
					$log[] = $record_ures;
				}
				$deleted_record_id_ = $d['_id'];
				$links_dres = $mongodb_con->delete_one( $graph_mentions, ["_id"=>$deleted_record_id_] );
				$log[] = $links_dres;
				$deleted_record_id_ = $dataset_id . "-" . $record_id . "-". $thing_id;
				$log[] = $deleted_record_id_;
				$links_dres = $mongodb_con->delete_one( $graph_mentions, ["_id"=>$deleted_record_id_] );
				$log[] = $links_dres;
			}
			return ['statusCode'=>200, 'body'=>['status'=>"success"], 'log'=>$log ];

		}else if( $task['data']['action'] == "record_update" || $task['data']['action'] == "record_create" ){

			$graph_dataset = $graph_things . "_" . $task['data']['object_id'];

			$log[] = "finding " . $task['data']['record_id'];
			$log[] = $graph_dataset;
			$res = $mongodb_con->find_one( $graph_dataset, ["_id"=>$task['data']['record_id']] );
			if( !$res['data'] ){
				return ['statusCode'=>200, 'body'=>['status'=>"fail", "data"=>"record ". $task['data']['record_id'] . " is not found" ], 'log'=>$log ];
			}
			$record = $res['data'];

			// $log[] = "finding parent " . $task['data']['object_id'];
			// $res = $mongodb_con->find_one( $graph_things, ["_id"=>$task['data']['object_id']], ['projection'=>['z_t']] );
			// if( !$res['data'] ){
			// 	return ['statusCode'=>200, 'body'=>['status'=>"fail", "data"=>"parent node ". $thing['i_of']['i'] . " is not found", "res"=>$res ], 'log'=>$log ];
			// }
			// $thing = $res['data'];

			foreach( $record['props'] as $propf=>$propd ){
				foreach( $propd as $pi=>$pd ){
					if( $pd['t'] == "GT" ){
						$id = $pd['i'] . "-" . $task['data']['object_id'] . "-" . $task['data']['record_id'] ."-". $propf;
						$log[] = $id;
						$mongodb_con->update_one( $graph_mentions, [
							'_id'=>$id
						], [
							't'=>"props",
							'm_u' =>date("Y-m-d H:i:s"),
						], [
							'upsert'=>true
						]);
						$id = $task['data']['object_id'] . "-" . $task['data']['record_id'] . "-" . $pd['i'];
						$log[] = $id;
						$mongodb_con->update_one( $graph_mentions, [
							'_id'=>$id
						], [
							't'=>"rec",
							'm_u' =>date("Y-m-d H:i:s"),
						], [
							'upsert'=>true
						]);
					}
				}
			}
			return ['statusCode'=>200, 'body'=>['status'=>"success", "data"=>[] ], 'log'=>$log ];

		}else if( $task['data']['action'] == "record_delete" ){

			$deleted_record_id = $task['data']['object_id'] . "-" . $task['data']['record_id'];

			$log[] = $deleted_record_id;
			$links_res = $mongodb_con->find( $graph_mentions, ["_id"=>['$gte'=>$deleted_record_id."-", '$lte'=>$deleted_record_id."~" ] ]);
			$log[] = "found: ". sizeof($links_res['data']);
			foreach( $links_res['data'] as $ii=>$d ){
				$log[] = $d;
				$x = explode("-", $d['_id']);
				$deleted_record_id_ = $x[2] . "-" . $task['data']['object_id'] . "-" . $task['data']['record_id'];
				$log[] = $deleted_record_id_;
				$links_dres = $mongodb_con->delete_many( $graph_mentions, ["_id"=>['$gte'=>$deleted_record_id_."-", '$lte'=>$deleted_record_id_."~" ]] );
				$log[] = $links_dres;
				$links_dres = $mongodb_con->delete_one( $graph_mentions, ["_id"=>$d['_id']] );
				$log[] = $links_dres;
			}
			return ['statusCode'=>200, 'body'=>['status'=>"success"], 'log'=>$log ];

		}else{
			return ['statusCode'=>500, 'body'=>['status'=>"fail", "data"=>"action not found" ], 'log'=>$log ];
		}
	}

}
