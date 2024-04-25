<?php

	if( $use_encrypted ){
		require_once("class_engine_encrypted.php");
	}else if( file_exists("class_engine.php") ){
		require_once("class_engine.php");
	}else{
		http_response_code(500);
		echo "File version missing!";exit;
	}
	$test = [];
	if( $_GET["function_version_id"] ){
		$input_data = $php_input;
		$test = json_decode($input_data, true);
	}else{
		//print_pre( $api_version );exit;
		if( $api_version['input-method'] == "GET" ){
			if( $_SERVER['REQUEST_METHOD']=="POST" ){
				header("Content-type: application/json");
				http_response_code(400);
				echo json_encode(["status"=>"fail", "error"=>"Unexpected POST Request" ]);exit;
			}
			$test = $_GET;
		}else if( $api_version['input-method'] == "POST" ){
			if( $_SERVER['REQUEST_METHOD']=="GET" ){
				http_response_code(400);
				header("Content-type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Unexpected GET Request" ]);exit;
			}
			if( $api_version['input-type'] == "application/json" ){
				if( preg_match("/json/i", $_SERVER['CONTENT_TYPE']) ){
					$input_data = $php_input;
					$test = json_decode($input_data, true);
					if( json_last_error() ){
						$e = "JSON Parse Error: " . json_last_error_msg();
						raw_request_log($e);
						if( $api_version['output-type'] == "application/json" ){
							header("Content-type: application/json");
							echo json_encode(["status"=>"fail", "error"=>$e ]);exit;
						}else{
							header("Content-type: " . $api_version['output-type']);
							echo json_encode(["status"=>"fail", "error"=>$e]);exit;
						}
					}
					if( $test == "" ){
						$e = "Input missing";
						raw_request_log($e);
						if( $api_version['output-type'] == "application/json" ){
							header("Content-type: application/json");
							echo json_encode(["status"=>"fail", "error"=>$e ]);exit;
						}else{
							header("Content-type: " . $api_version['output-type']);
							echo json_encode(["status"=>"fail", "error"=>$e]);exit;
						}
					}
				}else{
					$e = "Incorrect Input method/Content-type";
					raw_request_log($e);
					if( $api_version['output-type'] == 'application/json' ){
						header("Content-Type: application/json");
						echo json_encode(["status"=>"fail", "error"=>$e ]);exit;
					}else{
						header("Content-Type: " . $api_version['output-type']);
						echo json_encode(["status"=>"fail", "error"=>$e ]);exit;
					}
				}
			}else if( $api_version['input-type'] == "application/x-www-form-urlencoded" ){
				$test = $_POST;
			}
		}
		$test['server_'] = ["ip"=>$_SERVER['REMOTE_ADDR'],"user-agent"=>$_SERVER['HTTP_USER_AGENT']];
		$test['url_inputs_'] = $url_inputs;
	}

	//print_pre( $api_version );exit;

	if( isset($api_version['auth-type']) ){
		if( $api_version['auth-type'] == "Access-Key" ){
			if( !isset($_SERVER['HTTP_ACCESS_KEY']) ){
				http_response_code(403);
				header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Access-Key required" ]);exit;
			}else if( !preg_match( "/^[0-9a-f]{24}$/", $_SERVER['HTTP_ACCESS_KEY']) ){
				http_response_code(403);
				header("Content-Type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Access-Key Incorrect" ]);exit;
			}else{
				$res = $mongodb_con->find_one( $config_global_engine['config_mongo_prefix'] . "_user_keys", [
					"app_id"=>$api_version['app_id'],
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
				$allow_policy = false;
				if( isset($res['data']['policies']) && is_array($res['data']['policies']) ){
					foreach( $res['data']['policies'] as $ii=>$ip ){
						$ad_allow = false;$td_allow = false;
						if( isset($ip['service']) ){
							if( $ip['service'] == "apis" ){
								if( isset($ip['service']) && is_array($ip['actions']) ){
									foreach( $ip['actions'] as $ad ){
										if( $ad == "*" || $ad == "invoke" ){
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
											if( $x[0] == "api" ){
												if( $x[1] == $api_version['api_id'] ){
													$td_allow = true;break;
												}
											}
										}
									}
								}
							}
						}
						if( $ad_allow && $td_allow ){
							$allow_policy = true;break;
						}
					}
				}
				if( $allow_policy == false ){
					http_response_code(403);
					header("Content-Type: application/json");
					echo json_encode(["status"=>"fail", "error"=>"Access Key Policy Rejected" ]);exit;
				}
			}
		}
	}

	//print_pre( $test );exit;
	$api_engine = new api_engine();
	if( !$api_engine ){
		$e = "Error initializing api engine!";
		raw_request_log($e);
		echo $e;exit;
	}
	if( !isset($api_version['engine']) ){
		http_response_code(404);echo "Engine spec missing";exit;
	}
	$result = $api_engine->execute( $api_version, $test, ["request_log_id"=>$request_log_id] );

	//print_pre( $result );exit;

	function filter_result( $res ){
		$r = [];
		foreach( $result_ as $i=>$j ){
			if( !$i ){$i = "Undefined";}
			if( gettype($j) == "string" ){
				if( strlne($j) < 250 ){
					$r[ $i ] = substr($j,0,250);
				}else{
					$r[ $i ] = $j;
				}
			}else if( is_array($j) == "" ){
				$r[ $i ] = filter_result( $j );
			}else{
				$r[ $i ] = $j;
			}
		}
		return $r;
	}
	//$log = filter_result($result['log']);
	//print_pre( $_SESSION );exit;
	raw_request_log("success");
	function request_log($status, $error, $ctype, $output, $log){
		global $mongodb_con;
		global $request_log_id;
		global $api_version;
		global $app;
		global $alias_rec;
		global $test;
		$log_data = [
			"_id"=>$request_log_id,
			"type"=>"page",
			"api_key"=>"",
			"app"=>[
				"_id"=>$app['_id'], "app"=>$app['name']
			],
			"domain"=>($_SERVER['HTTP_REALHOST']?$_SERVER['HTTP_REALHOST']:$_SERVER['HTTP_HOST']),
			"url"=>$_SERVER['REQUEST_URI'],
			"page"=>[
				"_id"=>$api_version['_id'], "version"=>$_GET['version_id'], "reg_url"=>$api_version['reg_url'],
			],
			"ctype"=>$ctype,
			"inputs"=>$test,
			"status"=>$status,
			"error"=>$error,
			"output"=>$output,
			"log"=>$log,
			"timestamp" => date('Y-m-d H:i:s'),
			"ip" => $_SERVER['REMOTE_ADDR'],
			"user_id"=>$alias_rec['user']['_id'],
			"app"=>$app,
		];
		$log_insert_result = $mongodb_con->insert("log_requests", $log_data );
	}

	//echo $api_version['output-type'];
	//print_pre( $result );exit;

	$log = &$api_engine->getlog();
	if( $_GET["function_version_id"] ){
		header("content-type: application/json");
		$r = ['status'=>"success", "functionResponse"=>$result['body'] ];
		if( $test['debug'] ){
			$r['log'] = $log;
		}
		echo json_encode($r,JSON_PRETTY_PRINT);
		exit;
	}else{
		$result_content_type = "application/json";
		if( isset($result['headers']['content-type']) ){
			$result_content_type = $result['headers']['content-type'];
		}
		//echo $result_content_type;exit;
		if( $api_version['output-type'] == "application/json" || $result_content_type == "application/json" ){	
			if( gettype($result['statusCode'])=="integer" && $result['statusCode'] != 200 ){
				http_response_code((int)$result['statusCode']);
			}
			header("content-type: application/json");
			if( isset($result['headers']) && sizeof($result['headers'] ) ){
				foreach( $result['headers'] as $ii=>$jj ){ if( strtolower($ii) != "content-type" ){
					header( $ii . ":" . $jj );
				}}
			}
			if( $result['status'] == "fail" ){
				request_log($result['status'], $result['error'], $api_version['output-type'], "", $log);
				echo json_encode($result);
				exit;
			}
			if( !is_array($result['body']) ){
				$result['body'] = [];
			}
			if( !$test['debug'] ){
				if( $result['pretty'] ){
					$d = json_encode($result['body'],JSON_PRETTY_PRINT);
				}else{
					$d = json_encode($result['body']);
				}
			}else{
				if( $test['debug'] ){
					if( !isset($result['body']) ){
						$result['body'] = [];
					}
					$result['body']['log'] = $log;
				}
				$d = json_encode($result['body'],JSON_PRETTY_PRINT);
			}
			request_log($result['status'], $result['error'], $api_version['output-type'], substr($d,0,1024), $log);
			echo $d;
			exit;
		}else if( $api_version['output-type'] == "text/html" ){
			if( gettype($result['statusCode'])=="integer" && $result['statusCode'] != 200 ){
				http_response_code((int)$result['statusCode']);
			}
			header("content-type: text/html");
			if( sizeof($result['headers'] ) ){
				foreach( $result['headers'] as $ii=>$jj ){ if( strtolower($ii) != "content-type" ){
					header( $ii . ":" . $jj );
				}}
			}
			ob_start();
			echo "<html>\n";
			echo "<head>\n";
			if( isset($result['pagesettings']) ){
				echo "<title>" . ($result['pagesettings']['title']?$result['pagesettings']['title']['title']['value']:$app['app'] . " - ". $config_main_domain . " - " . $api_version['name'] ) . "</title>\n";
				if( $result['pagesettings']['viewport'] ){
					echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
				}
				if( $result['pagesettings']['meta_desc'] ){
					echo "<meta name=\"description\" content=\"" . $result['pagesettings']['meta_desc']['description']['value']. "\" >\n";
				}
				if( $result['pagesettings']['meta_keywords'] ){
					echo "<meta name=\"description\" content=\"" . $result['pagesettings']['meta_keywords']['keywords']['value']. "\" >\n";
				}
				if( $result['pagesettings']['meta_tag'] ){
					foreach( $result['pagesettings']['meta_tag'] as $i=>$j ){
						echo "<meta name=\"".$j['name']['value']."\" content=\"" . $j['descriptin']['value']. "\" >\n";
					}
				}
				if( $result['pagesettings']['vuejs'] ){
					echo "<script src=\"/js/vue.min.js\"></script>\n";
				}
				if( $result['pagesettings']['jquery'] ){
					echo "<script src=\"/js/jquery-3.3.1.min.js\"></script>\n";
				}
				if( $result['pagesettings']['jqueryui'] ){
					echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/js/jquery-ui.min.css\">\n";
					echo "<script src=\"/js/jquery-ui.min.js\"></script>\n";
				}
				if( $result['pagesettings']['bootstrap'] ){
					echo "<link rel='stylesheet' href='/bootstrap/css/bootstrap.min.css' >\n";
					echo "<script src='/bootstrap/js/bootstrap.min.js'></script>\n";
					/*
					echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/bootstrap.min.css\">\n";
					echo "<script src=\"/js/bootstrap.min.js\" async ></script>\n";*/
				}
				if( $result['pagesettings']['bootstrapvue'] ){
					echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/js/bootstrap-vue-js/bootstrap-vue.min.css\">\n";
					echo "<script src=\"/js/bootstrap-vue-js/bootstrap-vue.min.js\" ></script>\n";
				}
				if( $result['pagesettings']['axios'] ){
					echo "<script src=\"/js/axios.min.js\"></script>\n";
				}
				if( $result['pagesettings']['fontawesome'] ){
					echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css\">\n";
				}
				if( $result['pagesettings']['link_js'] ){
					foreach( $result['pagesettings']['link_js'] as $i=>$j ){
						if( $j['url']['vtype'] == "file" ){
							$j['url']['value'] = file_path( $app['_id'], $j['url']['value'] );
						}
						echo "<script src=\"".$j['url']['value']."\"" . ($j['defer']['value']?" defer":"").($j['async']['value']?" async":"")."></script>\n";
					}
				}
				if( $result['pagesettings']['link_css'] ){
					foreach( $result['pagesettings']['link_css'] as $i=>$j ){
						if( $j['url']['vtype'] == "file" ){
							$j['url']['value'] = file_path( $app['_id'], $j['url']['value'] );
						}
						echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $j['url']['value'] . "\">\n";
					}
				}
				if( $result['pagesettings']['custom'] ){
					foreach( $result['pagesettings']['custom'] as $i=>$j ){
						echo $j['html_text']['value'];
					}
				}
			}else{
				echo "<title>" . $api_version['name'] . "</title>";
			}
			echo "</head>\n";
			echo "<body";
			if( isset($result['pagesettings']) ){ 
				echo ($result['pagesettings']['bodytag']['class']?" class=\"".$result['pagesettings']['bodytag']['class']['value']."\"":"");
				echo ($result['pagesettings']['bodytag']['id']?" id=\"".$result['pagesettings']['bodytag']['id']['value']."\"":"");
			}
			echo ">\n";
			//echo "<pre>"; print_r( $result['pagesettings'] ); echo "</pre>";
			//echo "-----------------\n";
			if( gettype($result['body']) == "array" ){
					echo '<pre>';echo json_encode($result['body']); echo '</pre>';
			}else{
				echo $result['body'];
			}
			if( $_GET['debug'] ){
				echo "\n<pre>";
				print_r( $log );
				echo "</pre>";
			}
			echo "\n</body>\n";
			echo "</html>";
			$d = ob_get_clean();
			request_log("success", "", $api_version['output-type'], substr($d,0,1024), $log);
			echo $d;
		}else if( $api_version['output-type'] == "text/plain" ){
			//print_r( $result );exit;
			//request_log($result['status'], $result['error'], $api_version['output-type'], substr($result['body'],0,1024), $log);
			header("content-type: text/plain");
			echo $result['body'] . "\n";
			if( $_GET['debug'] ){
				print_r( $log );
			}
		}else{
			//require("layout_home.php");
			//request_log("fail", "unhandled output type", "text/plain", "", []);
			header("http/1.1 404 not found");
			header("content-type: text/plain");
			header("stage: one");
			echo $_SERVER['HTTP_REALHOST'] . "\n\n" . "/".$path . "\n\nUnhandled output type\n";
			echo $api_version['output-type'];
			exit;
		}
	}
