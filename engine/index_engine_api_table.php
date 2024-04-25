<?php

//print_r( $table_res );exit;
$db_res = $mongodb_con->find_one( $db_prefix . "_databases", ["_id"=>$table_res['data']['db_id'] ] );
if( !$db_res['data'] ){
	http_response_code(500); header("Content-Type: application/json");
	echo json_encode(["status"=>"fail", "error"=>"Database not found" ]);exit;
}

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

	if( $action == "findMany" ){
		$cond = [];
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$cond = mongo_query( $_POST['query'] );
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
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode($res);exit;
		}
		header("Content-Type: application/json");
		echo json_encode([
			"status"=>"success", "data"=>$res['data'], "query"=>$cond
		]);exit;
	}else if( $action == "findOne" ){
		$cond = [];
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$cond = mongo_query( $_POST['query'] );
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
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode($res);exit;
		}
		header("Content-Type: application/json");
		echo json_encode([
			"status"=>"success", "data"=>$res['data'], "query"=>$cond
		]);exit;

	}else if( $action == "insertMany" ){
		$ops = [];
		if( !isset($_POST['data']) || !is_array($_POST['data']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Data invalid" ]);exit;
		}
		$data = $_POST['data'];
		if( array_keys($data)[0] !== 0 ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"List required" ]);exit;
		}
		$res = $clientdb_con->insert_many( $col, $data, $ops );
		if( $res['status'] != "success" ){
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode($res);exit;
		}
		header("Content-Type: application/json");
		//print_r( get_class_methods($res))
		echo json_encode($res);exit;
	}else if( $action == "insertOne" ){
		$ops = [];
		if( !isset($_POST['data']) || !is_array($_POST['data']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Data invalid" ]);exit;
		}
		$res = $clientdb_con->insert( $col, $_POST['data'], $ops );
		if( $res['status'] != "success" ){
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode($res);exit;
		}
		header("Content-Type: application/json");
		echo json_encode($res);exit;
	}else if( $action == "updateMany" ){
		$cond = [];
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$cond = mongo_query( $_POST['query'] );
		}
		$ops = [];
		if( isset($options['limit']) ){
			$ops['limit'] = $options['limit'];
		}else{
			$ops['limit'] = 100;
		}
		if( !isset($_POST['update']) || !is_array($_POST['update']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Data invalid" ]);exit;
		}
		$data = $_POST['update'];
		foreach( $data as $tc=>$j ){
			if( $tc == '$set' ){
				if( isset($j['_id']) ){
					http_response_code(400);header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"\$set should not have _id" ]);exit;
				}
			}else if( $tc == '$unset' ){
				if( isset($j['_id']) ){
					http_response_code(400);header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"\$unset should not have _id" ]);exit;
				}
			}else if( $tc == '$inc' ){
				if( isset($j['_id']) ){
					http_response_code(400);header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"\$inc should not have _id" ]);exit;
				}
			}else{
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=> $tc. ": operator not allowed" ]);exit;
			}
		}
		$res = $clientdb_con->update_many( $col, $cond, $data, $ops );
		if( $res['status'] != "success" ){
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode($res);exit;
		}
		header("Content-Type: application/json");
		echo json_encode($res);exit;
	}else if( $action == "updateOne" ){
		$cond = [];
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$cond = mongo_query( $_POST['query'] );
		}
		$ops = [];
		if( !isset($_POST['update']) || !is_array($_POST['update']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Data invalid" ]);exit;
		}
		$data = $_POST['update'];
		foreach( $data as $tc=>$j ){
			if( $tc == '$set' ){
				if( isset($j['_id']) ){
					http_response_code(400);header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"\$set should not have _id" ]);exit;
				}
			}else if( $tc == '$unset' ){
				if( isset($j['_id']) ){
					http_response_code(400);header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"\$unset should not have _id" ]);exit;
				}
			}else if( $tc == '$inc' ){
				if( isset($j['_id']) ){
					http_response_code(400);header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"\$inc should not have _id" ]);exit;
				}
			}else{
				http_response_code(400);header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=> $tc. ": operator not allowed" ]);exit;
			}
		}
		$res = $clientdb_con->update_one( $col, $cond, $data, $ops );
		if( $res['status'] != "success" ){
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode($res);exit;
		}
		header("Content-Type: application/json");
		echo json_encode($res);exit;
	}else if( $action == "deleteMany" ){
		$cond = [];
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$cond = mongo_query( $_POST['query'] );
		}
		$ops = [];
		if( isset($options['limit']) ){
			$ops['limit'] = $options['limit'];
		}else{
			$ops['limit'] = 100;
		}
		$res = $clientdb_con->delete_many( $col, $cond, $ops );
		if( $res['status'] != "success" ){
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode($res);exit;
		}
		header("Content-Type: application/json");
		echo json_encode($res);exit;
	}else if( $action == "deleteOne" ){
		$cond = [];
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$cond = mongo_query( $_POST['query'] );
		}
		$ops = [];
		$res = $clientdb_con->delete_one( $col, $cond, $ops );
		if( $res['status'] != "success" ){
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode($res);exit;
		}
		header("Content-Type: application/json");
		echo json_encode($res);exit;
	}else{
		http_response_code(403);
		header("Content-Type: application/json");
		echo json_encode(["status"=>"fail", "error"=>"Unknown action" ]);exit;
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
		http_response_code(500); header("Content-Type: application/json");
		echo json_encode([
			"status"=>"fail", "error"=>"DB Connect Error: " . mysqli_connect_error()
		]);exit;
	}
	mysqli_options($clientdb_con, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true); 
	mysqli_report(MYSQLI_REPORT_OFF);

	//print_r( $table_res );exit;
	$primary_keys = $table_res['data']['source_schema']['keys']['PRIMARY']['keys'];
	$primary_key = array_keys($primary_keys)[0];
	$primary_key_type = $primary_keys[ $primary_key ]['type'];

	if( $action == "findMany" ){
		$where = "";
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$where = mysql_cond( $clientdb_con, $_POST['query'] );
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
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode([
				"status"=>"fail",
				"error"=>mysqli_error($clientdb_con),
				"query"=>$query
			]);exit;
		}
		$rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
		header("Content-Type: application/json");
		echo json_encode([
			"status"=>"success", "data"=>$rows, "query"=>$query
		]);exit;

	}else if( $action == "findOne" ){
		$where = "";
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$where = mysql_cond( $clientdb_con, $_POST['query'] );
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
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode([
				"status"=>"fail",
				"error"=>mysqli_error($clientdb_con),
				"query"=>$query
			]);exit;
		}
		$row = mysqli_fetch_assoc($res);
		header("Content-Type: application/json");
		echo json_encode([
			"status"=>"success", "data"=>$row, "query"=>$query
		]);exit;

	}else if( $action == "insertMany" ){
		if( !isset($_POST['data']) || !is_array($_POST['data']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Data invalid" ]);exit;
		}
		$data = $_POST['data'];
		if( array_keys($data)[0] !== 0 ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"List required" ]);exit;
		}
		$values = [];
		$query = "insert into `". $table_res['data']['table'] . "` ( " . $fields . " ) values " . implode(", ", $values);
		$res = $clientdb_con->insert_many( $col, $data, $ops );
		if( $res['status'] != "success" ){
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode($res);exit;
		}
		header("Content-Type: application/json");
		//print_r( get_class_methods($res))
		echo json_encode([
			"status"=>"success", "data"=>$row, "query"=>$query
		]);exit;

	}else if( $action == "insertOne" ){
		if( !isset($_POST['data']) || !is_array($_POST['data']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Data invalid" ]);exit;
		}
		$data = $_POST['data'];
		$values = [];
		foreach( $data as $i=>$j ){
			$values[] = "`" . $i . "` = '" . mysqli_escape_string( $clientdb_con, $j ) . "' ";
		}
		$query = "insert into `". $table_res['data']['table'] . "` set " . implode(", ", $values) . " ";
		$res = mysqli_query( $clientdb_con, $query );
		if( mysqli_error( $clientdb_con ) ){
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode([
				"status"=>"fail",
				"error"=>mysqli_error($clientdb_con),
				"query"=>$query
			]);exit;
		}
		header("Content-Type: application/json");
		echo json_encode([
			"status"=>"success", "inserted_id"=>mysqli_insert_id($clientdb_con), "query"=>$query
		]);exit;

	}else if( $action == "updateMany" ){
		if( !isset($_POST['update']) || !is_array($_POST['update']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Data invalid" ]);exit;
		}
		$data = $_POST['update'];
		$values = [];
		foreach( $data as $i=>$j ){
			$values[] = "`" . $i . "` = '" . mysqli_escape_string( $clientdb_con, $j ) . "' ";
		}
		$where = "";
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$where = mysql_cond( $clientdb_con, $_POST['query'] );
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
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode([
				"status"=>"fail",
				"error"=>mysqli_error($clientdb_con),
				"query"=>$query
			]);exit;
		}
		header("Content-Type: application/json");
		echo json_encode([
			"status"=>"success", "affected"=>mysqli_affected_rows($clientdb_con), "query"=>$query
		]);exit;

	}else if( $action == "updateOne" ){
		if( !isset($_POST['update']) || !is_array($_POST['update']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Data invalid" ]);exit;
		}
		$data = $_POST['update'];
		$values = [];
		foreach( $data as $i=>$j ){
			$values[] = "`" . $i . "` = '" . mysqli_escape_string( $clientdb_con, $j ) . "' ";
		}
		$where = "";
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$where = mysql_cond( $clientdb_con, $_POST['query'] );
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
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode([
				"status"=>"fail",
				"error"=>mysqli_error($clientdb_con),
				"query"=>$query
			]);exit;
		}
		header("Content-Type: application/json");
		echo json_encode([
			"status"=>"success", "affected"=>mysqli_affected_rows($clientdb_con), "query"=>$query
		]);exit;


	}else if( $action == "deleteMany" ){
		$where = "";
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			//print_r( $_POST['query'] );	
			$where = mysql_cond( $clientdb_con, $_POST['query'] );
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
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode([
				"status"=>"fail",
				"error"=>mysqli_error($clientdb_con),
				"query"=>$query
			]);exit;
		}
		header("Content-Type: application/json");
		echo json_encode([
			"status"=>"success", "affected"=>mysqli_affected_rows($clientdb_con), "query"=>$query
		]);exit;


	}else if( $action == "deleteOne" ){
		$where = "";
		if( isset( $_POST['query'] ) && is_array($_POST['query']) ){
			$where = mysql_cond( $clientdb_con, $_POST['query'] );
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
			http_response_code(500); header("Content-Type: application/json");
			echo json_encode([
				"status"=>"fail",
				"error"=>mysqli_error($clientdb_con),
				"query"=>$query
			]);exit;
		}
		header("Content-Type: application/json");
		echo json_encode([
			"status"=>"success", "affected"=>mysqli_affected_rows($clientdb_con), "query"=>$query
		]);exit;

	}else{
		http_response_code(403);
		header("Content-Type: application/json");
		echo json_encode(["status"=>"fail", "error"=>"Unknown action" ]);exit;
	}

}else{
	http_response_code(500);
	header("Content-Type: application/json");
	echo json_encode(["status"=>"fail", "error"=>"Unknown DB Engine" ]);exit;
}

