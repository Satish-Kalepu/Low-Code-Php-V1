<?php

function engine_api_table( $action, $table_res, $options, $post ){
	
	global $mongodb_con;
	global $app_id;
	global $db_prefix;

	$db_res = $mongodb_con->find_one( $db_prefix . "_databases", ["_id"=>$table_res['data']['db_id'] ] );
	if( !$db_res['data'] ){
		return json_response(500,["status"=>"fail", "error"=>"Database not found" ]);exit;
	}
	$db_id = $table_res['data']['db_id'];
	$table_id = $table_res['data']['_id'];

	//print_r( $db_res['data'] );exit;
	$engine = $db_res['data']['engine'];
	$col  = $table_res['data']['table'];

	if( $engine == "MongoDb" ){

		$clientdb_con = new mongodb_connection( 
			$db_res['data']['details']['host'], 
			$db_res['data']['details']['port'], 
			$db_res['data']['details']['database'], 
			pass_decrypt($db_res['data']['details']['username']), 
			pass_decrypt($db_res['data']['details']['password']), 
			$db_res['data']['details']['authSource'], 
			$db_res['data']['details']['tls'], 
		);

		if( $action == "getSchema" ){
			return json_response(200,$table_res);exit;
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
			$res = $clientdb_con->find( $col, $cond, $ops );
			if( $res['status'] != "success" ){
				return json_response(500,$res);exit;
			}
			return json_response(200,[
				"status"=>"success", "data"=>$res['data'], "query"=>$cond
			]);exit;
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
			$res = $clientdb_con->find_one( $col, $cond, $ops );
			if( $res['status'] != "success" ){
				return json_response(500,$res);exit;
			}
			return json_response(200,[
				"status"=>"success", "data"=>$res['data'], "query"=>$cond
			]);exit;

		}else if( $action == "insertMany" ){
			$ops = [];
			if( !isset($post['data']) || !is_array($post['data']) ){
				return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);exit;
			}
			$data = $post['data'];
			if( array_keys($data)[0] !== 0 ){
				return json_response(400,["status"=>"fail", "error"=>"List required" ]);exit;
			}
			$res = $clientdb_con->insert_many( $col, $data, $ops );
			if( $res['status'] != "success" ){
				return json_response(500,$res);exit;
			}
			return json_response(200,$res);exit;
		}else if( $action == "insertOne" ){
			$ops = [];
			if( !isset($post['data']) || !is_array($post['data']) ){
				return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);exit;
			}
			$res = $clientdb_con->insert( $col, $post['data'], $ops );
			if( $res['status'] != "success" ){
				return json_response(500,$res);exit;
			}
			event_log("database_table", "record_create", [
				"app_id"=>$app_id,
				"db_id"=>$db_id,
				"table_id"=>$table_id,
				"record_id"=>$res['inserted_id']
			]);
			return json_response(200,$res);exit;
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
				return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);exit;
			}
			$data = $post['update'];
			foreach( $data as $tc=>$j ){
				if( $tc == '$set' ){
					if( isset($j['_id']) ){
						return json_response(400,["status"=>"fail", "error"=>"\$set should not have _id" ]);exit;
					}
				}else if( $tc == '$unset' ){
					if( isset($j['_id']) ){
						return json_response(400,["status"=>"fail", "error"=>"\$unset should not have _id" ]);exit;
					}
				}else if( $tc == '$inc' ){
					if( isset($j['_id']) ){
						return json_response(400,["status"=>"fail", "error"=>"\$inc should not have _id" ]);exit;
					}
				}else{
					return json_response(400,["status"=>"fail", "error"=> $tc. ": operator not allowed" ]);exit;
				}
			}
			$res = $clientdb_con->update_many( $col, $cond, $data, $ops );
			if( $res['status'] != "success" ){
				return json_response(500,$res);exit;
			}
			return json_response(200,$res);exit;
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
				return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);exit;
			}
			$data = $post['update'];
			foreach( $data as $tc=>$j ){
				if( $tc == '$set' ){
					if( isset($j['_id']) ){
						return json_response(400,["status"=>"fail", "error"=>"\$set should not have _id" ]);exit;
					}
				}else if( $tc == '$unset' ){
					if( isset($j['_id']) ){
						return json_response(400,["status"=>"fail", "error"=>"\$unset should not have _id" ]);exit;
					}
				}else if( $tc == '$inc' ){
					if( isset($j['_id']) ){
						return json_response(400,["status"=>"fail", "error"=>"\$inc should not have _id" ]);exit;
					}
				}else{
					return json_response(400,["status"=>"fail", "error"=> $tc. ": operator not allowed" ]);exit;
				}
			}
			$res = $clientdb_con->update_one( $col, $cond, $data, $ops );
			if( $res['status'] != "success" ){
				return json_response(500,$res);exit;
			}
			event_log("database_table", "record_edit", [
				"app_id"=>$app_id,
				"db_id"=>$db_id,
				"table_id"=>$table_id,
				"record_id"=>$record_id
			]);
			return json_response(200,$res);exit;
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
			$res = $clientdb_con->delete_many( $col, $cond, $ops );
			if( $res['status'] != "success" ){
				return json_response(500,$res);exit;
			}
			return json_response(200,$res);exit;
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
			$res = $clientdb_con->delete_one( $col, $cond, $ops );
			if( $res['status'] != "success" ){
				return json_response(500,$res);exit;
			}
			event_log("database_table", "record_delete", [
				"app_id"=>$app_id,
				"db_id"=>$db_id,
				"table_id"=>$table_id,
				"record_id"=>$record_id
			]);
			return json_response(200,$res);exit;
		}else{
			return json_response(403,["status"=>"fail", "error"=>"Unknown action" ]);exit;
		}


	}else if( $engine == "MySql" ){

		$clientdb_con = mysqli_connect(
			$db_res['data']['details']['host'], 
			pass_decrypt($db_res['data']['details']['username']), 
			pass_decrypt($db_res['data']['details']['password']), 
			$db_res['data']['details']['database'], 
			$db_res['data']['details']['port'], 				
		);
		if( mysqli_connect_error() ){
			return json_response(500,[
				"status"=>"fail", "error"=>"DB Connect Error: " . mysqli_connect_error()
			]);exit;
		}
		mysqli_options($clientdb_con, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true); 
		mysqli_report(MYSQLI_REPORT_OFF);

		//print_r( $table_res );exit;
		$primary_keys = $table_res['data']['source_schema']['keys']['PRIMARY']['keys'];
		$primary_key = array_keys($primary_keys)[0];
		$primary_key_type = $primary_keys[ $primary_key ]['type'];

		if( $action == "getSchema" ){
			return json_response(200,$table_res);exit;
		}

		if( $action == "findMany" ){
			$where = "";
			if( isset( $post['query'] ) && is_array($post['query']) ){
				$where = mysql_cond( $clientdb_con, $post['query'] );
				if( trim($where) ){
					$where = " where " . $where;
				}
			}
			$limit = 10;
			if( isset($options['limit']) ){
				$limit = $options['limit'];
			}
			if( !isset($options['sort']) ){
				$sort = "`". $primary_key. "`";
			}else if( isset($options['sort']) ){
				$sorts = [];
				foreach( $options['sort'] as $i=>$j ){
					$sorts[] = "`".$i . "` " . ($j>0?"ASC":"DESC");
				}
				$sort = implode(", ", $sorts );
			}
			if( isset($options['projection']) && is_array($options['projection']) ){
				$fields = implode(",",array_keys($options['projection']));
			}else{
				$fields = "*";
			}
			$query = "select " . $fields . " from `" . $table_res['data']['table'] . "` " . $where . " order by " . $sort . " limit " . $limit;
			$res = mysqli_query( $clientdb_con, $query );
			if( mysqli_error($clientdb_con) ){
				return json_response(500,[
					"status"=>"fail",
					"error"=>mysqli_error($clientdb_con),
					"query"=>$query
				]);exit;
			}
			$rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
			return json_response(200,[
				"status"=>"success", "data"=>$rows, "query"=>$query
			]);exit;

		}else if( $action == "findOne" ){
			$where = "";
			if( isset( $post['query'] ) && is_array($post['query']) ){
				$where = mysql_cond( $clientdb_con, $post['query'] );
				if( trim($where) ){
					$where = " where " . $where;
				}
			}
			$limit = 1;
			if( !isset($options['sort']) ){
				$sort = "`". $primary_key. "`";
			}else if( isset($options['sort']) ){
				$sorts = [];
				foreach( $options['sort'] as $i=>$j ){
					$sorts[] = "`".$i . "` " . ($j>0?"ASC":"desc");
				}
				$sort = implode(", ", $sorts );
			}
			if( isset($options['projection']) && is_array($options['projection']) ){
				$fields = implode(",",array_keys($options['projection']));
			}else{
				$fields = "*";
			}
			$query = "select " . $fields . " from `" . $table_res['data']['table'] . "` " . $where . " order by " . $sort . " limit " . $limit;
			$res = mysqli_query( $clientdb_con, $query );
			if( mysqli_error($clientdb_con) ){
				return json_response(500,[
					"status"=>"fail",
					"error"=>mysqli_error($clientdb_con),
					"query"=>$query
				]);exit;
			}
			$row = mysqli_fetch_assoc($res);
			return json_response(200,[
				"status"=>"success", "data"=>$row, "query"=>$query
			]);exit;

		}else if( $action == "insertMany" ){
			if( !isset($post['data']) || !is_array($post['data']) ){
				return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);exit;
			}
			$data = $post['data'];
			if( array_keys($data)[0] !== 0 ){
				return json_response(400,["status"=>"fail", "error"=>"List required" ]);exit;
			}
			$values = [];
			$query = "insert into `". $table_res['data']['table'] . "` ( " . $fields . " ) values " . implode(", ", $values);
			$res = $clientdb_con->insert_many( $col, $data, $ops );
			if( $res['status'] != "success" ){
				return json_response(500,$res);exit;
			}
			return json_response(200,[
				"status"=>"success", "data"=>$row, "query"=>$query
			]);exit;

		}else if( $action == "insertOne" ){
			if( !isset($post['data']) || !is_array($post['data']) ){
				return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);exit;
			}
			$data = $post['data'];
			$values = [];
			foreach( $data as $i=>$j ){
				$values[] = "`" . $i . "` = '" . mysqli_escape_string( $clientdb_con, $j ) . "' ";
			}
			$query = "insert into `". $table_res['data']['table'] . "` set " . implode(", ", $values) . " ";
			$res = mysqli_query( $clientdb_con, $query );
			if( mysqli_error( $clientdb_con ) ){
				return json_response(500,[
					"status"=>"fail",
					"error"=>mysqli_error($clientdb_con),
					"query"=>$query
				]);exit;
			}
			event_log("database_table", "record_edit", [
				"app_id"=>$app_id,
				"db_id"=>$db_id,
				"table_id"=>$table_id,
				"record_id"=>mysqli_insert_id($clientdb_con)
			]);
			return json_response(200,[
				"status"=>"success", "inserted_id"=>mysqli_insert_id($clientdb_con), "query"=>$query
			]);exit;

		}else if( $action == "updateMany" ){
			if( !isset($post['update']) || !is_array($post['update']) ){
				return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);exit;
			}
			$data = $post['update'];
			$values = [];
			foreach( $data as $i=>$j ){
				$values[] = "`" . $i . "` = '" . mysqli_escape_string( $clientdb_con, $j ) . "' ";
			}
			$where = "";
			if( isset( $post['query'] ) && is_array($post['query']) ){
				$where = mysql_cond( $clientdb_con, $post['query'] );
				if( trim($where) ){
					$where = " where " . $where;
				}
			}
			$limit = 10;
			if( isset($options['limit']) ){
				$limit = $options['limit'];
			}
			if( !isset($options['sort']) ){
				$sort = "`". $primary_key. "`";
			}else if( isset($options['sort']) ){
				$sorts = [];
				foreach( $options['sort'] as $i=>$j ){
					$sorts[] = "`".$i . "` " . ($j>0?"ASC":"desc");
				}
				$sort = implode(", ", $sorts );
			}
			$query = "update  `". $table_res['data']['table'] . "` set " . implode(", ", $values) . " " . $where . " limit " . $limit;
			$res = mysqli_query( $clientdb_con, $query );
			if( mysqli_error( $clientdb_con ) ){
				return json_response(500,[
					"status"=>"fail",
					"error"=>mysqli_error($clientdb_con),
					"query"=>$query
				]);exit;
			}
			return json_response(200,[
				"status"=>"success", "affected"=>mysqli_affected_rows($clientdb_con), "query"=>$query
			]);exit;

		}else if( $action == "updateOne" ){
			if( !isset($post['update']) || !is_array($post['update']) ){
				return json_response(400,["status"=>"fail", "error"=>"Data invalid" ]);exit;
			}
			$data = $post['update'];
			$values = [];
			foreach( $data as $i=>$j ){
				$values[] = "`" . $i . "` = '" . mysqli_escape_string( $clientdb_con, $j ) . "' ";
			}
			$where = "";
			if( isset( $post['query'] ) && is_array($post['query']) ){
				$where = mysql_cond( $clientdb_con, $post['query'] );
				if( trim($where) ){
					$where = " where " . $where;
				}
			}
			$limit = 1;
			if( !isset($options['sort']) ){
				$sort = "`". $primary_key. "`";
			}else if( isset($options['sort']) ){
				$sorts = [];
				foreach( $options['sort'] as $i=>$j ){
					$sorts[] = "`".$i . "` " . ($j>0?"ASC":"desc");
				}
				$sort = implode(", ", $sorts );
			}
			$query = "update  `". $table_res['data']['table'] . "` set " . implode(", ", $values) . " " . $where . " limit " . $limit;
			$res = mysqli_query( $clientdb_con, $query );
			if( mysqli_error( $clientdb_con ) ){
				return json_response(500,[
					"status"=>"fail",
					"error"=>mysqli_error($clientdb_con),
					"query"=>$query
				]);exit;
			}
			return json_response(200,[
				"status"=>"success", "affected"=>mysqli_affected_rows($clientdb_con), "query"=>$query
			]);exit;

		}else if( $action == "deleteMany" ){
			$where = "";
			if( isset( $post['query'] ) && is_array($post['query']) ){
				//print_r( $post['query'] );	
				$where = mysql_cond( $clientdb_con, $post['query'] );
				//echo $where ;exit;
				if( trim($where) ){
					$where = " where " . $where;
				}
			}
			$limit = 10;
			if( isset($options['limit']) ){
				$limit = $options['limit'];
			}
			if( !isset($options['sort']) ){
				$sort = "`". $primary_key. "`";
			}else if( isset($options['sort']) ){
				$sorts = [];
				foreach( $options['sort'] as $i=>$j ){
					$sorts[] = "`".$i . "` " . ($j>0?"ASC":"desc");
				}
				$sort = implode(", ", $sorts );
			}
			$query = "delete from  `". $table_res['data']['table'] . "` " . $where . " limit " . $limit;
			$res = mysqli_query( $clientdb_con, $query );
			if( mysqli_error( $clientdb_con ) ){
				return json_response(500,[
					"status"=>"fail",
					"error"=>mysqli_error($clientdb_con),
					"query"=>$query
				]);exit;
			}
			return json_response(200,[
				"status"=>"success", "affected"=>mysqli_affected_rows($clientdb_con), "query"=>$query
			]);exit;


		}else if( $action == "deleteOne" ){
			$where = "";
			if( isset( $post['query'] ) && is_array($post['query']) ){
				$where = mysql_cond( $clientdb_con, $post['query'] );
				if( trim($where) ){
					$where = " where " . $where;
				}
			}
			$limit = 1;
			if( !isset($options['sort']) ){
				$sort = "`". $primary_key. "`";
			}else if( isset($options['sort']) ){
				$sorts = [];
				foreach( $options['sort'] as $i=>$j ){
					$sorts[] = "`".$i . "` " . ($j>0?"ASC":"desc");
				}
				$sort = implode(", ", $sorts );
			}
			$query = "delete from  `". $table_res['data']['table'] . "` " . $where . " limit " . $limit;
			$res = mysqli_query( $clientdb_con, $query );
			if( mysqli_error( $clientdb_con ) ){
				return json_response(500,[
					"status"=>"fail",
					"error"=>mysqli_error($clientdb_con),
					"query"=>$query
				]);exit;
			}
			return json_response(200,[
				"status"=>"success", "affected"=>mysqli_affected_rows($clientdb_con), "query"=>$query
			]);exit;

		}else{
			return json_response(403,["status"=>"fail", "error"=>"Unknown action" ]);exit;
		}

	}else{
		return json_response(500,["status"=>"fail", "error"=>"Unknown DB Engine" ]);exit;
	}

}