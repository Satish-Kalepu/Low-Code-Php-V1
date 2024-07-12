<?php

function index_api($api_version, $get, $post, $php_input){

	global $mongodb_con;
	global $config_global_engine;
	global $app_id;
	global $db_prefix;

	$test = [];
	if( $get["function_version_id"] ){
		$input_data = $php_input;
		$test = json_decode($input_data, true);
	}else{
		//print_pre( $api_version );exit;
		if( $api_version['input-method'] == "GET" ){
			if( $_SERVER['REQUEST_METHOD']=="POST" ){
				return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Unexpected POST Request"]) ];
			}
			$test = $get;
		}else if( $api_version['input-method'] == "POST" ){
			if( $_SERVER['REQUEST_METHOD']=="GET" ){
				return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Unexpected GET Request"]) ];
			}
			if( $api_version['input-type'] == "application/json" ){
				if( preg_match("/json/i", $_SERVER['CONTENT_TYPE']) ){
					$input_data = $php_input;
					$test = json_decode($input_data, true);
					if( json_last_error() ){
						$e = "JSON Parse Error: " . json_last_error_msg();
						return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>$e]) ];
					}
					if( $test == "" ){
						return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>"input missing"]) ];
					}
				}else{
					return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Incorrect Input method/Content-type" ]) ];
				}
			}else if( $api_version['input-type'] == "application/x-www-form-urlencoded" ){
				$test = $post;
			}
		}
		$test['server_'] = ["ip"=>$_SERVER['REMOTE_ADDR'],"user-agent"=>$_SERVER['HTTP_USER_AGENT']];
		$test['url_inputs_'] = $url_inputs;
	}

	//print_pre( $api_version );exit;

	if( isset($api_version['auth-type']) ){
		if( $api_version['auth-type'] == "Access-Key" ){
			if( !isset($_SERVER['HTTP_ACCESS_KEY']) ){
				return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key required" ]) ];
			}else if( !preg_match( "/^[0-9a-f]{24}$/", $_SERVER['HTTP_ACCESS_KEY']) ){
				return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key incorrect" ]) ];
			}else{
				//echo $config_global_engine['config_mongo_prefix'] . "_user_keys";exit;
				$res = $mongodb_con->find_one( $config_global_engine['config_mongo_prefix'] . "_user_keys", [
					"app_id"=>$api_version['app_id'],
					"_id"=>$_SERVER['HTTP_ACCESS_KEY']
				] );
				if( !$res['data'] ){
					return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key Not found" ]) ];
				}
				// echo time();
				// print_pre( $res['data'] );exit;
				if( $res['data']['expire'] < time() || $res['data']['active'] != 'y' ){
					return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key Expired/Inactive" ]) ];
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
						}else{
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
				}
				if( $ipf == false ){
					return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key IP rejected", "ip"=>$_SERVER['REMOTE_ADDR'] ]) ];
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
					return [403,"application/json",[], json_encode(["status"=>"fail", "error"=>"Access-Key Policy Rejected" ]) ];
				}
			}
		}
	}

	//print_pre( $test );exit;
	$api_engine = new api_engine();
	if( !$api_engine ){
		return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Error initializing api engine!" ]) ];
	}
	if( !isset($api_version['engine']) ){
		return [500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Engine spec missing" ]) ];
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

	$log = &$api_engine->getlog();
	if( $get["function_version_id"] ){
		$r = ['status'=>"success", "functionResponse"=>$result['body'] ];
		if( $test['debug'] ){
			$r['log'] = $log;
		}
		return [200,"application/json",[], json_encode($r,JSON_PRETTY_PRINT) ];
	}else{
		$result_content_type = "application/json";
		if( isset($result['headers']['content-type']) ){
			$result_content_type = $result['headers']['content-type'];
		}
		$statusCode = 200;
		if( gettype($result['statusCode'])=="integer" && $result['statusCode'] != 200 ){
			$statusCode = (int)$result['statusCode'];
		}
		$h = [];
		if( isset($result['headers']) && sizeof($result['headers'] ) ){
			foreach( $result['headers'] as $ii=>$jj ){ if( strtolower($ii) != "content-type" ){
				$h[ $ii ] = $jj;
			}}
		}
		//echo $result_content_type;exit;
		if( $api_version['output-type'] == "application/json" || $result_content_type == "application/json" ){	
			if( $result['status'] == "fail" ){
				return [$statusCode,"application/json",[], json_encode($result)];
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
			return [$statusCode,$api_version['output-type'], $h, $d, $log ];
		}else if( $api_version['output-type'] == "text/html" ){
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
			if( $get['debug'] ){
				echo "\n<pre>";
				print_r( $log );
				echo "</pre>";
			}
			echo "\n</body>\n";
			echo "</html>";
			$d = ob_get_clean();
			return [$statusCode, $result_content_type, $h, $d, $log];
		}else if( $api_version['output-type'] == "text/plain" ){
			if( gettype($result['body']) == "array" ){
				$result['body'] = json_encode($result['body']);
			}
			return [$statusCode, $result_content_type, $h, $result['body'], $log];
		}else{
			return [404, "text/plain", [], "Unhandled output-type: ".$api_version['output-type'] ];
		}
	}

}