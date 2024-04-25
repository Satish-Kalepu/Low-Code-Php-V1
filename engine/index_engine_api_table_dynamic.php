<?php

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
	$res = $mongodb_con->find( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $ops );
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
	$res = $mongodb_con->find_one( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $ops );
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
	$res = $mongodb_con->insert_many( $db_prefix . "_dt_" . $table_res['data']['_id'], $data, $ops );
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
	$res = $mongodb_con->insert( $db_prefix . "_dt_" . $table_res['data']['_id'], $_POST['data'], $ops );
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
	$res = $mongodb_con->update_many( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $data, $ops );
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
	$res = $mongodb_con->update_one( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $data, $ops );
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
	$res = $mongodb_con->delete_many( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $ops );
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
	$res = $mongodb_con->delete_one( $db_prefix . "_dt_" . $table_res['data']['_id'], $cond, $ops );
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


