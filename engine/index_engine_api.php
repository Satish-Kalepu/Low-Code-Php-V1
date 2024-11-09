<?php

$task_insert_id = 1000;
function generate_task_queue_id($delay=0){
	global $task_insert_id;
	if( gettype($delay) != "integer" ){
		$delay = 0;
	}else if( $delay > (600) ){
		$delay =600; // max is 10 minutes
	}
	return date("YmdHis",time()+$delay).":".rand(100,999).":".$task_insert_id;
	$task_insert_id++;
}

function engine_api( $method, $content_type, $path_params, $php_input ){
	global $mongodb_con;
	global $app_id;
	global $db_prefix;


	if( $method!="POST" ){
		return [ 400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Unexpected GET Request" ]) ];
	}
	if( !preg_match("/(json|multipart)/i", $content_type) ){
		return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Unexpected payload" ]) ];
	}
	if( preg_match("/json/i", $content_type) ){
		$post = json_decode($php_input, true);
		if( json_last_error() ){
			$e = "JSON Parse Error: " . json_last_error_msg();
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Payload Json Decode Fail" ]) ];
		}
	}
	if( preg_match("/multipart/i", $content_type) ){
		$post = $_POST;
	}

	if( !isset($path_params[1]) ){
		return [404,"application/json",[], json_encode(["status"=>"fail", "error"=>"Are you lost" ]) ];
	}

	//print_r( $post );exit;
	//return [200,"application/json",[], json_encode($post) ];
	$action = "";
	if( $method =="POST"){
		if( !isset($post['action']) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Action Missing" ]) ];
		}
		$action = $post['action'];
	}
	if( $path_params[1] == "tables_dynamic" || $path_params[1] == "tables" ){
		$options = [];
		if( isset($post['options']) && is_array($post['options']) ){
			$options = $post['options'];
		}else if( isset($post['options']) && !is_array($post['options']) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Options Incorrect" ]) ];
		}
	}

	$thing_type = $path_params[1];
	if( $path_params[1] == "captcha" ){
		if( !isset($path_params[2]) ){
			return [404,"application/json",[], json_encode(["status"=>"fail", "error"=>"API Not found" ]) ];
		}
		if( $path_params[2] == "get" ){
			$thing_id = "10101";
		}
	}

	if( $path_params[1] == "files" ){
		$thing_type = "file";
		if( !isset($path_params[2]) ){
			return [404,"application/json",[], json_encode(["status"=>"fail", "error"=>"API not found" ]) ];
		}
		if( $path_params[2] == "internal" ){
			$thing_id = "f0010";
		}
	}

	if( $path_params[1] == "auth" ){
		$thing_type = "auth_api";
		if( !isset($path_params[2]) ){
			return [404,"application/json",[], json_encode(["status"=>"fail", "error"=>"API not found" ]) ];
		}
		$action = $path_params[2];
		$api_slug = $path_params[2];
		if( $api_slug == "generate_access_token" ){
			$thing_id = "10001";
		}else if( $api_slug == "user_auth" ){
			$thing_id = "10002";
		}else if( $api_slug == "user_auth_captcha" ){
			$thing_id = "10003";
		}else if( $api_slug == "verify_session_key" ){
			$thing_id = "10004";
		}else if( $api_slug == "assume_session_key" ){
			$thing_id = "10005";
		}else if( $api_slug == "verify_user_session" ){
			$thing_id = "10006";
		}else if( $api_slug == "assume_user_session_key" ){
			$thing_id = "10007";
		}else if( $api_slug == "user_session_logout" ){
			$thing_id = "10009";
		}
	}
	if( $path_params[1] == "tables_dynamic" ){
		$thing_type = "table_dynamic";
		if( !isset($path_params[2]) ){
			return [404,"application/json",[], json_encode(["status"=>"fail", "error"=>"API not found" ]) ];
		}
		$thing_id = $path_params[2];
		if( !preg_match("/^[a-f0-9]{24}$/", $thing_id) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Incorrect Table ID" ]) ];
		}
		$table_res = $mongodb_con->find_one( $db_prefix . "_tables_dynamic", [
			"app_id"=>$app_id, "_id"=>$thing_id
		]);
		//print_r( ["app_id"=>$app_id, "_id"=>$thing_id]);
		if( !$table_res['data'] ){
			return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Table not found" ]) ];
		}
	}
	if( $path_params[1] == "tables" ){
		$thing_type = "table";
		if( !isset($path_params[2]) ){
			return [404,"application/json",[], json_encode(["status"=>"fail", "error"=>"Not found" ]) ];
		}
		$thing_id = $path_params[2];
		if( !preg_match("/^[a-f0-9]{24}$/", $thing_id) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Incorrect Table ID" ]) ];
		}
		$table_res = $mongodb_con->find_one( $db_prefix . "_tables", [
			"app_id"=>$app_id, "_id"=>$thing_id
		]);
		if( !$table_res['data'] ){
			return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Table not found" ]) ];
		}
	}
	if( $path_params[1] == "storage_vaults" ){
		$thing_type = "storage_vault";
		if( !isset($path_params[2]) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Not found" ]) ];
		}
		$thing_id = $path_params[2];
		if( !preg_match("/^[a-f0-9]{24}$/", $thing_id) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Incorrect Vault ID" ]) ];
		}
		$res = $mongodb_con->find_one( $db_prefix . "_storage_vaults", [
			"app_id"=>$app_id, "_id"=>$thing_id
		]);
		if( !$res['data'] ){
			return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Vault not found" ]) ];
		}
		$storage_vault = $res['data'];
	}
	if( $path_params[1] == "objects" ){
		$thing_type = "object";
		if( !isset($path_params[2]) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Not found" ]) ];
		}
		$thing_id = $path_params[2];
		if( !preg_match("/^[a-f0-9]{24}$/", $thing_id) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Incorrect Vault ID" ]) ];
		}
		$graphres = $mongodb_con->find_one( $db_prefix . "_graph_dbs", [
			"app_id"=>$app_id, "_id"=>$thing_id
		]);
		if( !$graphres['data'] ){
			return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Objects DB not found" ]) ];
		}
		$graph_db = $graphres['data'];
	}
	$config_public_apis = [
		["auth","verify_session_key"]
	];

	$allow_policy = false;
	foreach( $config_public_apis as $i=>$j ){
		if( $path_params[1] == $j[0] ){
			if( isset($path_params[2]) ){
				if( isset($j[1]) ){
					if( $path_params[2] == $j[1] ){
						$allow_policy = true;
					}
				}
			}
		}
	}

	//echo $_SERVER['HTTP_ACCESS_KEY'];exit;

	if( !$allow_policy ){{
			if( !isset($_SERVER['HTTP_ACCESS_KEY']) ){
				return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key required" ]) ];
			}else if( !preg_match( "/^[0-9a-f]{24}$/", $_SERVER['HTTP_ACCESS_KEY']) ){
				return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key Incorrect" ]) ];
			}else{
				$res = $mongodb_con->find_one( $db_prefix . "_user_keys", [
					"app_id"=>$app_id,
					"_id"=>$_SERVER['HTTP_ACCESS_KEY']
				] );
				if( !$res['data'] ){
					return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key not found","_id"=>$_SERVER['HTTP_ACCESS_KEY'] ]) ];
				}
				if( $res['data']['expire'] < time() || $res['data']['active'] != 'y' ){
					return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key Expired/InActive" ]) ];
				}

				if( isset($res['data']['sess_id']) ){
					$sessres = $mongodb_con->find_one( $db_prefix . "_user_sessions", [
						"app_id"=>$app_id,
						"_id"=>$res['data']['sess_id']
					]);
					if( !$sessres['data'] ){
						return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Session Expired","_id"=>$res['data']['sess_id'] ]) ];
					}
				}

				$ipf = false;
				$x = explode(".", $_SERVER['REMOTE_ADDR']);
				$ip2 = implode(".",[$x[0],$x[1],$x[2]] );
				$ip3 = implode(".",[$x[0],$x[1]] );
				$ip4 = $x[0];
				if( isset($res['data']['ips']) && is_array($res['data']['ips']) ){
					foreach( $res['data']['ips'] as $ii=>$ip ){
						if( $ip == "*" ){
							$ipf = true;break;
						}
						$x = explode("/", $ip);
						$x2 = explode(".",$x[0]);
						if( $x[1] == "32" ){
							if( $_SERVER['REMOTE_ADDR'] == $x[0] ){
								$ipf = true;break;
							}
						}else if( $x[1] == "24" ){
							if( $ip2 == implode(".",[ $x2[0],$x2[1],$x2[2] ] ) ){
								$ipf = true;break;
							}
						}else if( $x[1] == "16" ){
							if( $ip3 == implode(".",[ $x2[0],$x2[1] ] ) ){
								$ipf = true;break;
							}
						}else if( $x[1] == "8" ){
							if( $ip4 == $x2[0] ){
								$ipf = true;break;
							}
						}
					}
				}
				if( $ipf == false ){
					return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key IP rejected. " . $_SERVER['REMOTE_ADDR'] ]) ];
				}
				//print_r( $res['data']['policies'] );exit;
				$allow_policy = false;
				if( isset($res['data']['policies']) && is_array($res['data']['policies']) ){
					foreach( $res['data']['policies'] as $ii=>$ip ){
						$ad_allow = false;$td_allow = false;
						if( isset($ip['service']) ){
							//print_r( $ip['actions'] );
							if( isset($ip['actions']) && is_array($ip['actions']) ){
								foreach( $ip['actions'] as $ad ){
									if( $ad == "*" || $ad == $action ){
										$ad_allow = true;break;
									}
									if( ($action == "findMany" || $action == "findOne") && ($ad == "find"||$ad=="scan") ){
										$ad_allow = true;break;
									}
								}
							}
							if( isset($ip['things']) && is_array($ip['things']) ){
								foreach( $ip['things'] as $td ){
									if( $td['_id'] == "*" ){
										$td_allow = true;break;
									}else{
										$x = explode(":", $td['_id']);
										//echo $x[0] . "==" . $thing_type . " : " . $x[1] . "==" . $thing_id . "<BR>";
										if( $x[0] == $thing_type && $x[1] == $thing_id ){
											$td_allow = true;break;
										}
									}
								}
							}
						}
						//echo ($ad_allow?"Actionok":"").($td_allow?"tableOK":"");
						if( $ad_allow && $td_allow ){
							$allow_policy = true;break;
						}
					}
				}
				if( $allow_policy == false ){
					return [403,"application/json",[], json_encode([
						"status"=>"fail", 
						"error"=>"Access-Key Policy Rejected" ,
						"ad"=>$ad_allow,
						"thing"=>$td_allow,
						"action"=>$action,
					]) ];
				}else{
					$resu = $mongodb_con->update_one( $db_prefix . "_user_keys", [
						"app_id"=>$app_id,
						"_id"=>$_SERVER['HTTP_ACCESS_KEY']
					], [
						'$set'=>['last_used'=>time(), 'last_ip'=>$_SERVER['REMOTE_ADDR']], 
						'$inc'=>['hits'=>1]
					]);
				}
			}
	}}

	if( $thing_type == "captcha" ){
		if( !isset($path_params[2]) ){
			return [404,"application/json",[], json_encode(["status"=>"fail", "error"=>"API not found" ]) ];
		}
		if( $path_params[2] == "get" ){
			return generate_captcha();
		}
	}

	if( $thing_type == "table" || $thing_type == "table_dynamic" ){
		if( isset( $post['query'] ) && !is_array($post['query']) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Query format mismatch" ]) ];
		}
		if( isset( $post['options'] ) && !is_array($post['options']) ){
			return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Options format mismatch" ]) ];
		}else if( isset( $post['options'] ) && is_array($post['options']) ){
			if( $action == "findMany" && $action == "updateMany" && $action == "deleteMany" ){
				if( !isset( $post['options']['limit'] ) ){
					return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Options limit is required" ]) ];
				}else if( !is_numeric( $post['options']['limit'] ) ){
					return [400,"application/json",[], json_encode(["status"=>"fail", "error"=>"Options limit format mismatch" ]) ];
				}
			}
		}
	}

	function mongo_query( $query, $top_field = "" ){
		global $mongodb_con;
		foreach( $query as $field=>$j ){
			if( $field == '$and' || $field == '$or' ){
				for($ii=0;$ii<sizeof($j);$ii++){
					$query[ $field ][ $ii ] = mongo_query($query[ $field ][ $ii ], $field);
				}
			}else if( $field == '_id' || $top_field == '_id' ){
				if( is_array($j) ){
					$jj = [];
					$keys = array_keys($j);
					foreach( $keys as $c ){
						$v = $j[ $c ];
						if( $c == '$in' ){
							foreach( $v as $vi=>$vd ){
								$v[ $vi ] = $mongodb_con->get_id($vd);
							}
						}
						if( is_string($v) && preg_match("/^[a-f0-9]{24}$/",$v) ){
							$v = $mongodb_con->get_id($v);
						}
						if( $c == '<'  ){ $c = '$lt';  }
						if( $c == '<=' ){ $c = '$lte'; }
						if( $c == '>'  ){ $c = '$gt';  }
						if( $c == '>=' ){ $c = '$gte'; }
						if( $c == '='  ){ $c = '$eq';  }
						if( $c == '!=' ){ $c = '$ne';  }
						$jj[ $c ] = $v;
					}
					$query[ $field ] = $jj;
				}else if( is_string($j) && preg_match("/^[a-f0-9]{24}$/",$j) ){
					$query[ $field ] = $mongodb_con->get_id($j);
				}
			}
		}
		return $query;
	}

	function mysql_cond($con, $query ){
		$cond = [];
		foreach( $query as $field=>$j ){
			//echo $field . "--";
			if( $field == '$and' ){
				$c = [];
				for($ii=0;$ii<sizeof($j);$ii++){
					$c[] = mysql_cond($con, $query[ $field ][ $ii ]);
				}
				$cond[] = " ( " . implode(" and ", $c ) . " ) ";
			}else if( $field == '$or' ){
				//echo "llll";
				$c = [];
				for($ii=0;$ii<sizeof($j);$ii++){
					$c[] = mysql_cond($con, $query[ $field ][ $ii ]);
				}
				$cond[] = " ( " . implode(" or ", $c ) . " ) ";
			}else{
				if( is_array($j) ){
					$c = array_keys($j)[0];
					$v = $j[ $c ];
					if( $c == '$lt'  ){ $c = '<';  }
					if( $c == '$lte' ){ $c = '<='; }
					if( $c == '$gt'  ){ $c = '>';  }
					if( $c == '$gte' ){ $c = '>='; }
					if( $c == '$eq'  ){ $c = '=';  }
					if( $c == '$ne'  ){ $c = '!=';  }
					$cond[] = "`" . $field . "` ".$c." '" . mysqli_escape_string($con, $v ) . "' ";
				}else{
					$cond[] = "`" . $field . "` = '" . mysqli_escape_string($con, $j ) . "' ";
				}
			}
		}
		return implode(" and ", $cond);
	}

	if( $thing_type == "auth_api" ){
		return engine_auth_api( $api_slug, $post );
	}else if( $thing_type == "table_dynamic" ){
		return engine_api_table_dynamic($action, $table_res, $options, $post);
	}else if( $thing_type == "table" ){
		return engine_api_table($action, $table_res, $options, $post);
	}else if( $thing_type == "storage_vault" ){
		return engine_api_storage_vault( $storage_vault, $action, $post );
	}else if( $thing_type == "file" ){
		return engine_api_files( $post );
	}else if( $thing_type == "object" ){
		return engine_api_object($graph_db, $action, $post );
	}

	return [400,"application/json",[], json_encode([
		"status"=>"fail", 
		"error"=>"Access Key Accepted. But action missing",
		"action"=>$post['action'] ]
	)];

}