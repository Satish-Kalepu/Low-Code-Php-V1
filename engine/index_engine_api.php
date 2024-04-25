<?php

	/*
		auth_api/generate_access_token
		auth_api/user_authentication
		tables_dynamic/table_id
		tables/table_id
	*/

	if( $_SERVER['REQUEST_METHOD']!="POST" ){
		http_response_code(400);header("Coontent-Type:application/json");
		echo json_encode(["status"=>"fail", "error"=>"Unexpected GET Request" ]);exit;
	}
	if( !preg_match("/(json|multipart)/i", $_SERVER['CONTENT_TYPE']) ){
		http_response_code(400);header("Coontent-Type:application/json");
		echo json_encode(["status"=>"fail", "error"=>"Unexpected Payload" ]);exit;
	}
	if( preg_match("/json/i", $_SERVER['CONTENT_TYPE']) ){
		$input_data = $php_input;
		$_POST = json_decode($input_data, true);
		if( json_last_error() ){
			$e = "JSON Parse Error: " . json_last_error_msg();
			http_response_code(400);header("Coontent-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Payload Json Decode Fail" ]);exit;
		}
	}
	if( preg_match("/multipart/i", $_SERVER['CONTENT_TYPE']) ){
	}

	//print_r( $_POST );

	if( !isset($path_params[1]) ){
		http_response_code(404);header("Coontent-Type:application/json");
		echo json_encode(["status"=>"fail", "error"=>"Are you lost." ]);exit;
	}
	$action = "";
	if( $_SERVER['REQUEST_METHOD'] =="POST"){
		if( !isset($_POST['action']) ){
			http_response_code(403);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"Action missing" ]);exit;
		}
		$action = $_POST['action'];
	}
	if( $path_params[1] == "tables_dynamic" || $path_params[1] == "tables" ){
		$options = [];
		if( isset($_POST['options']) && is_array($_POST['options']) ){
			$options = $_POST['options'];
		}else if( isset($_POST['options']) && !is_array($_POST['options']) ){
			http_response_code(403);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"Options incorrect" ]);exit;
		}
	}

	$thing_type = $path_params[1];
	if( $path_params[1] == "captcha" ){
		if( !isset($path_params[2]) ){
			http_response_code(404);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"API Not found" ]);exit;
		}
		if( $path_params[2] == "get" ){
			$thing_id = "10101";
		}
	}

	if( $path_params[1] == "files" ){
		$thing_type = "file";
		if( !isset($path_params[2]) ){
			http_response_code(404);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"API Not found" ]);exit;
		}
		if( $path_params[2] == "internal" ){
			$thing_id = "f0010";
		}
	}

	if( $path_params[1] == "auth" ){
		$thing_type = "auth_api";
		if( !isset($path_params[2]) ){
			http_response_code(404);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"API Not found" ]);exit;
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
		}
	}
	if( $path_params[1] == "tables_dynamic" ){
		$thing_type = "table_dynamic";
		if( !isset($path_params[2]) ){
			http_response_code(404);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"Not found" ]);exit;
		}
		$thing_id = $path_params[2];
		if( !preg_match("/^[a-f0-9]{24}$/", $thing_id) ){
			http_response_code(400);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"Incorrect URL ID" ]);exit;
		}
		$table_res = $mongodb_con->find_one( $db_prefix . "_tables_dynamic", [
			"app_id"=>$app_id, "_id"=>$thing_id
		]);
		if( !$table_res['data'] ){
			http_response_code(400);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"Table not found" ]);exit;
		}
	}
	if( $path_params[1] == "tables" ){
		$thing_type = "table";
		if( !isset($path_params[2]) ){
			http_response_code(404);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"Not found" ]);exit;
		}
		$thing_id = $path_params[2];
		if( !preg_match("/^[a-f0-9]{24}$/", $thing_id) ){
			http_response_code(400);
			echo json_encode(["status"=>"fail", "error"=>"Incorrect URL ID" ]);exit;
		}
		$table_res = $mongodb_con->find_one( $db_prefix . "_tables", [
			"app_id"=>$app_id, "_id"=>$thing_id
		]);
		if( !$table_res['data'] ){
			http_response_code(400);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"Table not found" ]);exit;
		}
	}
	if( $path_params[1] == "storage_vaults" ){
		$thing_type = "storage_vault";
		if( !isset($path_params[2]) ){
			http_response_code(404);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"Not found" ]);exit;
		}
		$thing_id = $path_params[2];
		if( !preg_match("/^[a-f0-9]{24}$/", $thing_id) ){
			http_response_code(400);
			echo json_encode(["status"=>"fail", "error"=>"Incorrect URL ID" ]);exit;
		}
		$res = $mongodb_con->find_one( $db_prefix . "_storage_vaults", [
			"app_id"=>$app_id, "_id"=>$thing_id
		]);
		if( !$res['data'] ){
			http_response_code(400);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"Table not found" ]);exit;
		}
		$storage_vault = $res['data'];
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

	if( !$allow_policy ){{
			if( !isset($_SERVER['HTTP_ACCESS_KEY']) ){
				http_response_code(403);
				header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Access-Key required" ]);exit;
			}else if( !preg_match( "/^[0-9a-f]{24}$/", $_SERVER['HTTP_ACCESS_KEY']) ){
				http_response_code(403);
				header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Access-Key Incorrect" ]);exit;
			}else{
				$res = $mongodb_con->find_one( $db_prefix . "_user_keys", [
					"app_id"=>$app_id,
					"_id"=>$_SERVER['HTTP_ACCESS_KEY']
				] );
				if( !$res['data'] ){
					http_response_code(403);
					header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"Access-Key not found" ]);exit;
				}
				// echo time();
				// print_pre( $res['data'] );exit;
				if( $res['data']['expire'] < time() || $res['data']['active'] != 'y' ){
					http_response_code(403);
					header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"Access-Key Expired/InActive" ]);exit;
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
					http_response_code(403);
					header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"Access Key IP rejected" ]);exit;
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
					http_response_code(403);
					header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"Access Key Policy Rejected" ]);exit;
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
			http_response_code(404);header("Coontent-Type:application/json");
			echo json_encode(["status"=>"fail", "error"=>"API Not found" ]);exit;
		}
		if( $path_params[2] == "get" ){
			require("api_captcha.php");exit;
		}
	}

	if( $thing_type == "table" || $thing_type == "table_dynamic" ){
		if( isset( $_POST['query'] ) && !is_array($_POST['query']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Query format mismatch" ]);exit;
		}
		if( isset( $_POST['options'] ) && !is_array($_POST['options']) ){
			http_response_code(400);header("Content-Type: application/json");
			echo json_encode(["status"=>"fail", "error"=>"Options format mismatch" ]);exit;
		}else if( isset( $_POST['options'] ) && is_array($_POST['options']) ){
			if( $action == "findMany" && $action == "updateMany" && $action == "deleteMany" ){
				if( !isset( $_POST['options']['limit'] ) ){
					http_response_code(400);header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"Options:Limit is required" ]);exit;
				}else if( !is_numeric( $_POST['options']['limit'] ) ){
					http_response_code(400);header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"Options:Limit format mismatch" ]);exit;
				}
			}
		}
	}

	function mongo_query( $query ){
		global $mongodb_con;
		foreach( $query as $field=>$j ){
			if( $field == '$and' || $field == '$or' ){
				for($ii=0;$ii<sizeof($j);$ii++){
					$query[ $field ][ $ii ] = mongo_query($query[ $field ][ $ii ]);
				}
			}else if( $field == '_id' ){
				if( is_array($j) ){
					$jj = [];
					$keys = array_keys($j);
					foreach( $keys as $c ){
						$v = $j[ $c ];
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
		require_once("index_engine_api_auth_api.php");
	}else if( $thing_type == "table_dynamic" ){
		require_once("index_engine_api_table_dynamic.php");
	}else if( $thing_type == "table" ){
		require_once("index_engine_api_table.php");
	}else if( $thing_type == "storage_vault" ){
		require_once("index_engine_api_storage_vault.php");
	}else if( $thing_type == "file" ){
		require_once("index_engine_api_files.php");
	}


	header("Content-Type: application/json");
	echo json_encode(["status"=>"fail", "error"=>"Access Key Accepted. But action missing", "action"=>$_POST['action'] ]);exit;

exit;