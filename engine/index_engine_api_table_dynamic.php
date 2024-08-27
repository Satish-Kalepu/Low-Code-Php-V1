<?php

function engine_api_table_dynamic( $action, $table_res, $options, $post ){

	global $mongodb_con;
	global $app_id;
	global $db_prefix;

	$table_id = $table_res['_id'];

	if( $action == "getSchema" ){
		unset($table_res['_id']);unset($table_res['app_id']);
		return json_response(200,$table_res);
	}

	if( $action == "findMany" ){
		$cond = [];
		if( isset( $post['query'] ) && is_array($post['query']) ){
			$cond = mongo_query( $post['query'] );
		}
		$ops = [];
		if( isset($options['limit']) ){
			$ops['limit'] = $options['limit'];
		}else{
			$ops['limit'] = 10;
		}
		if( !isset($options['sort']) ){
			$ops['sort'] = ['_id'=>1];
		}else if( isset($options['sort']) ){
			$ops['sort'] = $options['sort'];
		}
		if( isset($options['projection']) && is_array($options['projection']) ){
			$ops['projection'] = $options['projection'];
		}
		if( isset($options['hint']) ){
			$ops['hint'] = $options['hint'];
		}
		$res = $mongodb_con->find( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $ops );
		if( $res['status'] != "success" ){
			return json_response(500,$res);
		}
		return json_response(200,[
			"status"=>"success", "data"=>$res['data'], "query"=>$cond
		]);
	}else if( $action == "findOne" ){
		$cond = [];
		if( isset( $post['query'] ) && is_array($post['query']) ){
			$cond = mongo_query( $post['query'] );
		}
		$ops = [];
		if( !isset($options['sort']) ){
			$ops['sort'] = ['_id'=>1];
		}
		if( isset($options['projection']) && is_array($options['projection']) ){
			$ops['projection'] = $options['projection'];
		}
		$res = $mongodb_con->find_one( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $ops );
		if( $res['status'] != "success" ){
			return json_response(200,$res);
		}
		return json_response(200,[
			"status"=>"success", "data"=>$res['data'], "query"=>$cond
		]);

	}else if( $action == "insertMany" ){
		$ops = [];
		if( !isset($post['data']) || !is_array($post['data']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);
		}
		$data = $post['data'];
		if( array_keys($data)[0] !== 0 ){
			return json_response(400,["status"=>"fail", "error"=>"List required" ]);
		}
		$res = $mongodb_con->insert_many( $db_prefix . "_dt_" . $table_res['data']['_id'], $data, $ops );
		if( $res['status'] != "success" ){
			return json_response(500,$res);
		}
		return json_response(200,$res);
	}else if( $action == "insertOne" ){
		$ops = [];
		if( !isset($post['data']) || !is_array($post['data']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);
		}
		$res = $mongodb_con->insert( $db_prefix . "_dt_" . $table_res['data']['_id'], $post['data'], $ops );
		if( $res['status'] != "success" ){
			return json_response(500,$res);
		}
		event_log("tables_dynamic", "record_create", [
			"app_id"=>$app_id,
			"table_id"=>$table_id,
			"record_id"=>$res['inserted_id']
		]);
		return json_response(200,$res);
	}else if( $action == "updateMany" ){
		$cond = [];
		if( isset( $post['query'] ) && is_array($post['query']) ){
			$cond = mongo_query( $post['query'] );
		}
		$ops = [];
		if( isset($options['limit']) ){
			$ops['limit'] = $options['limit'];
		}else{
			$ops['limit'] = 100;
		}
		if( !isset($post['update']) || !is_array($post['update']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);
		}
		$data = $post['update'];
		foreach( $data as $tc=>$j ){
			if( $tc == '$set' ){
				if( isset($j['_id']) ){
					return json_response(400,["status"=>"fail", "error"=>"\$set should not have _id" ]);
				}
			}else if( $tc == '$unset' ){
				if( isset($j['_id']) ){
					return json_response(400,["status"=>"fail", "error"=>"\$unset should not have _id" ]);
				}
			}else if( $tc == '$inc' ){
				if( isset($j['_id']) ){
					return json_response(400,["status"=>"fail", "error"=>"\$inc should not have _id" ]);
				}
			}else{
				return json_response(400,["status"=>"fail", "error"=> $tc. ": operator not allowed" ]);
			}
		}
		$res = $mongodb_con->update_many( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $data, $ops );
		if( $res['status'] != "success" ){
			return json_response(500,$res);
		}
		return json_response(200,$res);
	}else if( $action == "updateOne" ){
		$cond = [];
		if( isset( $post['query'] ) && is_array($post['query']) ){
			$cond = mongo_query( $post['query'] );
		}
		$record_id = "";
		if( $post['query']['_id'] ){
			if( is_array($post['query']['_id']) ){
				$record_id = $post['query']['_id'][ array_keys($post['query']['_id'])[0] ];
			}else{
				$record_id = $post['query']['_id'];
			}
		}
		$ops = [];
		if( !isset($post['update']) || !is_array($post['update']) ){
			return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);
		}
		$data = $post['update'];
		foreach( $data as $tc=>$j ){
			if( $tc == '$set' ){
				if( isset($j['_id']) ){
					return json_response(400,["status"=>"fail", "error"=>"\$set should not have _id" ]);
				}
			}else if( $tc == '$unset' ){
				if( isset($j['_id']) ){
					return json_response(400,["status"=>"fail", "error"=>"\$unset should not have _id" ]);
				}
			}else if( $tc == '$inc' ){
				if( isset($j['_id']) ){
					return json_response(400,["status"=>"fail", "error"=>"\$inc should not have _id" ]);
				}
			}else{
				return json_response(400,["status"=>"fail", "error"=> $tc. ": operator not allowed" ]);
			}
		}
		$res = $mongodb_con->update_one( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $data, $ops );
		if( $res['status'] != "success" ){
			return json_response(500,$res);
		}
		$res['query']=$cond;
		event_log("tables_dynamic", "record_edit", [
			"app_id"=>$app_id,
			"table_id"=>$table_id,
			"record_id"=>$record_id
		]);
		return json_response(200,$res);
	}else if( $action == "deleteMany" ){
		$cond = [];
		if( isset( $post['query'] ) && is_array($post['query']) ){
			$cond = mongo_query( $post['query'] );
		}
		$ops = [];
		if( isset($options['limit']) ){
			$ops['limit'] = $options['limit'];
		}else{
			$ops['limit'] = 100;
		}
		$res = $mongodb_con->delete_many( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $ops );
		if( $res['status'] != "success" ){
			return json_response(500,$res);
		}
		$res['query']=$cond;
		return json_response(200,$res);
	}else if( $action == "deleteOne" ){
		$cond = [];
		if( isset( $post['query'] ) && is_array($post['query']) ){
			$cond = mongo_query( $post['query'] );
		}
		$record_id = "";
		if( $post['query']['_id'] ){
			if( is_array($post['query']['_id']) ){
				$record_id = $post['query']['_id'][ array_keys($post['query']['_id'])[0] ];
			}else{
				$record_id = $post['query']['_id'];
			}
		}
		$ops = [];
		$res = $mongodb_con->delete_one( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $ops );
		if( $res['status'] != "success" ){
			return json_response(500,$res);
		}
		event_log("tables_dynamic", "record_delete", [
			"app_id"=>$app_id,
			"table_id"=>$table_id,
			"record_id"=>$record_id
		]);
		return json_response(200,$res);
	}else{
		return json_response(403,["status"=>"fail", "error"=>"Unknown action" ]);
	}

}