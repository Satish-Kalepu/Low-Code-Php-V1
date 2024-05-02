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

		if( $page_version['control']['input-method'] == "GET" ){
			if( $_SERVER['REQUEST_METHOD']=="POST" ){
				header("Content-type: application/json");
				http_response_code(400);
				echo json_encode(["status"=>"fail", "error"=>"Unexpected POST Request" ]);exit;
			}
			$test = $_GET;
		}else if( $page_version['control']['input-method'] == "POST" ){
			if( $_SERVER['REQUEST_METHOD']=="GET" ){
				http_response_code(400);
				header("Content-type: application/json");
				echo json_encode(["status"=>"fail", "error"=>"Unexpected GET Request" ]);exit;
			}
			if( $page_version['control']['input-type'] == "application/json" ){
				if( preg_match("/json/i", $_SERVER['CONTENT_TYPE']) ){
					$input_data = $php_input;
					$test = json_decode($input_data, true);
					if( json_last_error() ){
						$e = "JSON Parse Error: " . json_last_error_msg();
						if( $page_version['control']['output-type'] == "application/json" ){
							header("Content-type: application/json");
							echo json_encode(["status"=>"fail", "error"=>$e ]);exit;
						}else{
							header("Content-type: " . $page_version['control']['output-type']);
							echo json_encode(["status"=>"fail", "error"=>$e]);exit;
						}
					}
					if( $test == "" ){
						$e = "Input missing";
						if( $page_version['control']['output-type'] == "application/json" ){
							header("Content-type: application/json");
							echo json_encode(["status"=>"fail", "error"=>$e ]);exit;
						}else{
							header("Content-type: " . $page_version['control']['output-type']);
							echo json_encode(["status"=>"fail", "error"=>$e]);exit;
						}
					}
				}else{
					$e = "Incorrect Input method/Content-type";
					if( $page_version['control']['output-type'] == 'application/json' ){
						header("Content-Type: application/json");
						echo json_encode(["status"=>"fail", "error"=>$e ]);exit;
					}else{
						header("Content-Type: " . $page_version['control']['output-type']);
						echo json_encode(["status"=>"fail", "error"=>$e ]);exit;
					}
				}
			}else if( $page_version['control']['input-type'] == "application/x-www-form-urlencoded" ){
				$test = $_POST;
			}
		}
		$test['server_'] = ["ip"=>$_SERVER['REMOTE_ADDR'],"user-agent"=>$_SERVER['HTTP_USER_AGENT']];
		$test['url_inputs_'] = $url_inputs;


	if( isset($page_version['control']['auth-type']) ){
		if( $page_version['control']['auth-type'] == "Access-Key" ){
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
					"app_id"=>$page_version['control']['app_id'],
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
												if( $x[1] == $page_version['control']['api_id'] ){
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

	$result_body = [];
	if( isset($page_version['control']['engine']) ){
		//print_pre( $test );exit;
		$api_engine = new api_engine();
		if( !$api_engine ){
			$e = "Error initializing api engine!";
			echo $e;exit;
		}

		//print_r( $page_version );exit;
		$page_version['control']['app_id'] = $page_version['app_id'];
		$page_version['control']['user_id'] = $page_version['user_id'];
		$result = $api_engine->execute( $page_version['control'], $test, ["request_log_id"=>$request_log_id] );

		//print_pre( $result );exit;
		if( $_GET['debug'] ){
			header("Content-Type: text/plain");
			echo json_encode($result,JSON_PRETTY_PRINT);
			$log = &$api_engine->getlog();
			echo json_encode($log,JSON_PRETTY_PRINT);
			exit;
		}

		if( gettype($result['statusCode'])=="integer" && $result['statusCode'] != 200 ){
			http_response_code((int)$result['statusCode']);
		}
		if( isset($result['headers']) && sizeof($result['headers'] ) ){
			foreach( $result['headers'] as $ii=>$jj ){ if( strtolower($ii) != "content-type" ){
				header( $ii . ":" . $jj );
			}}
		}
		if( $result['statusCode'] != 200 ){
			if( gettype($result['body']) == "array" ){
				echo json_encode($result['body'],JSON_PRETTY_PRINT);
			}else{
				echo $result['body'];
			}
			exit;
		}
		$result_body =$result['body'];
		if( !is_array($result_body) ){
			$result_body = [];
		}
	}
	$result_body["global_api_url"] = "https://". $_SERVER['HTTP_HOST'] . $hosting_path . "/_api/";

	$comps = [];
	preg_match_all("/data\-app\=\"(.*?)\"/", $page_version['html'], $m );
	//print_r( $m );exit;
	foreach( $m[1] as $i=>$j ){
		$comps[] = $j;
	}

?><html>
<head>
	<title>Page</title>
	<style id="editor_top_css__"></style>
	<link  href="<?=$config_global_apimaker_engine['config_engine_path'] ?>bootstrap/bootstrap.min.css" rel="stylesheet" >
	<link rel="stylesheet" href="<?=$config_global_apimaker_engine['config_engine_path'] ?>fontawesome/font-awesome.min.css" />
	<script src="<?=$config_global_apimaker_engine['config_engine_path'] ?>bootstrap/bootstrap.bundle.min.js" ></script>
	<script src="<?=$config_global_apimaker_engine['config_engine_path'] ?>www/vue3.min.prod.js"></script>
	<script src="<?=$config_global_apimaker_engine['config_engine_path'] ?>www/axios.min.js"></script>
	<script>var page_data = <?=json_encode($result_body) ?>;</script>
</head>
<body id="app"><?php

echo $page_version['html'];

?></body>
<script><?php
if( $page_version['script'] ){
	echo $page_version['script'];
}else{ ?>
	<?php foreach( $comps as $i=>$j ){$fn="components/component_".$j.".js"; if( file_exists($fn) ){ require($fn); } } ?>
	var app = Vue.createApp({
		data: function(){return{}},
		mounted: function(){},
		methods: function(){}
	});
	<?php foreach( $comps as $i=>$j ){$fn="components/component_".$j.".js"; if( file_exists($fn) ){ ?>app.component("app_<?=strtolower($j) ?>", app_<?=$j ?>);<?php } } ?>
	var app1 = app.mount("#app");
<?php } ?>
</script>
</html>
<script>
</script>