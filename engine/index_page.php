<?php

function index_page( $page_version, $get, $post ){

	global $mongodb_con;
	global $db_prefix;
	global $app_id;
	global $config_global_engine;
	global $config_global_apimaker_engine;
	global $hosting_path;

	$h = [];
	$test = [];

	if( $page_version['control']['input-method'] == "GET" ){
		if( $_SERVER['REQUEST_METHOD']=="POST" ){
			return json_response(400,["status"=>"fail", "error"=>"Unexpected POST Request" ]);exit;
		}
		$test = $get;
	}else if( $page_version['control']['input-method'] == "POST" ){
		if( $_SERVER['REQUEST_METHOD']=="GET" ){
			return json_response(400,["status"=>"fail", "error"=>"Unexpected GET Request" ]);exit;
		}
		if( $page_version['control']['input-type'] == "application/json" ){
			if( preg_match("/json/i", $_SERVER['CONTENT_TYPE']) ){
				$input_data = $php_input;
				$test = json_decode($input_data, true);
				if( json_last_error() ){
					$e = "JSON Parse Error: " . json_last_error_msg();
					return json_response(200, ["status"=>"fail", "error"=>$e ]);exit;
				}
				if( $test == "" ){
					$e = "Input missing";
					return json_response(200, ["status"=>"fail", "error"=>$e ]);exit;
				}
			}else{
				$e = "Incorrect Input method/Content-type";
				return json_response(200, ["status"=>"fail", "error"=>$e ]);exit;
			}
		}else if( $page_version['control']['input-type'] == "application/x-www-form-urlencoded" ){
			$test = $post;
		}
	}
	$test['server_'] = ["ip"=>$_SERVER['REMOTE_ADDR'],"user-agent"=>$_SERVER['HTTP_USER_AGENT']];
	$test['url_inputs_'] = $url_inputs;


	if( isset($page_version['control']['auth-type']) ){
		if( $page_version['control']['auth-type'] == "Access-Key" ){
			if( !isset($_SERVER['HTTP_ACCESS_KEY']) ){
				return json_response(403,["status"=>"fail", "error"=>"Access-Key required" ]);exit;
			}else if( !preg_match( "/^[0-9a-f]{24}$/", $_SERVER['HTTP_ACCESS_KEY']) ){
				return json_response(403,["status"=>"fail", "error"=>"Access-Key Incorrect" ]);exit;
			}else{
				$res = $mongodb_con->find_one( $config_global_engine['config_mongo_prefix'] . "_user_keys", [
					"app_id"=>$page_version['control']['app_id'],
					"_id"=>$_SERVER['HTTP_ACCESS_KEY']
				] );
				if( !$res['data'] ){
					return json_response(403,["status"=>"fail", "error"=>"Access-Key not found" ]);exit;
				}
				// echo time();
				// print_pre( $res['data'] );exit;
				if( $res['data']['expire'] < time() || $res['data']['active'] != 'y' ){
					return json_response(403,["status"=>"fail", "error"=>"Access-Key Expired/InActive" ]);exit;
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
					return json_response(403,["status"=>"fail", "error"=>"Access Key IP rejected" ]);exit;
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
					return json_response(403,["status"=>"fail", "error"=>"Access Key Policy Rejected" ]);exit;
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
			return json_response(500,['status'=>"fail","error"=>$e]);exit;
		}

		//print_r( $page_version );exit;
		$page_version['control']['app_id'] = $page_version['app_id'];
		$page_version['control']['user_id'] = $page_version['user_id'];
		$result = $api_engine->execute( $page_version['control'], $test, ["request_log_id"=>$request_log_id] );

		//print_pre( $result );exit;
		if( $get['debug'] ){
			header("Content-Type: text/plain");
			echo json_encode($result,JSON_PRETTY_PRINT);
			$log = &$api_engine->getlog();
			echo json_encode($log,JSON_PRETTY_PRINT);
			exit;
		}

		$statusCode = 200;

		if( gettype($result['statusCode'])=="integer" && $result['statusCode'] != 200 ){
			$statusCode = (int)$result['statusCode'];
		}
		$h = [];
		if( isset($result['headers']) && sizeof($result['headers'] ) ){
			foreach( $result['headers'] as $ii=>$jj ){ if( strtolower($ii) != "content-type" ){
				$h[$ii] = $jj;
			}}
		}
		if( $statusCode != 200 ){
			if( gettype($result['body']) == "array" ){
				return [$statusCode, "application/json", $h, json_encode($result['body'],JSON_PRETTY_PRINT) ];
			}else{
				return [$statusCode, "text/plain", $h, $result['body'] ];
			}
			exit;
		}
		$result_body =$result['body'];
		if( !is_array($result_body) ){
			$result_body = [];
		}
	}
	$result_body["global_api_url"] = "https://". $_SERVER['HTTP_HOST'] . $hosting_path . "/_api/";

	if( $page_version['type'] == "html" ){

		ob_start();
		?><html>
		<head>
			<meta charset="utf-8">
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
		<?php
		$d = ob_get_clean();

	}else if( $page_version['type'] == "vuejs" ){

		$vue = $page_version['vuestructure'];

		$vue['methods'] = str_replace("methods = ", "", $vue['methods']);
		foreach( $vue['components'] as $i=>$j ){
			$vue['components'][ $i ]['methods'] = str_replace( "methods = ", "", $vue['components'][ $i ]['methods'] );
		}

		$routes = [];
		if( $vue['router_enable'] === true ){
			foreach( $vue['router'] as $ri=>$rd ){
				$routes[] = [
					'path'=>$config_global_apimaker_engine['config_engine_path'] . $page_version['name'] . $rd['path'],
					'component'=>"---component_" . $rd['component'] . "---",
				];
			}
			$routes_str = json_encode($routes,JSON_PRETTY_PRINT);
			$routes_str = str_replace("\"---", "", $routes_str);
			$routes_str = str_replace("---\"", "", $routes_str);
			$routes_str = str_replace("\\", "", $routes_str);
			//return [ 200, "text/html", $h, $routes_str ];
		}

		$data_block = $vue['data']['data'];
		$data_block = str_replace("%base_path%", $config_global_apimaker_engine['config_engine_path'] . $page_version['name'], $data_block );

		ob_start();
		?><html>
		<head>
			<meta charset="utf-8">
			<title><?=htmlentities($vue['structure']['head_tags']['title']) ?></title>
			<style id="editor_top_css__"></style>
			<link href="<?=$config_global_apimaker_engine['config_engine_path'] ?>bootstrap/bootstrap.min.css" rel="stylesheet" >
			<link rel="stylesheet" href="<?=$config_global_apimaker_engine['config_engine_path'] ?>fontawesome/font-awesome.min.css" />
			<script src="<?=$config_global_apimaker_engine['config_engine_path'] ?>bootstrap/bootstrap.bundle.min.js" ></script>
			<script src="<?=$config_global_apimaker_engine['config_engine_path'] ?>www/vue3.min.prod.js"></script>
			<script src="<?=$config_global_apimaker_engine['config_engine_path'] ?>www/vue.router.min.js"></script>
			<script src="<?=$config_global_apimaker_engine['config_engine_path'] ?>www/axios.min.js"></script>
<?php 
	foreach( $vue['structure']['head_tags']['meta-names'] as $i=>$j ){
		echo "\t".'<meta name="'.$j['name'].'" content="'.htmlentities($j['content']).'" >' . "\n";
	}
	foreach( $vue['structure']['head_tags']['meta-props'] as $i=>$j ){
		echo "\t".'<meta property="'.$j['name'].'" content="'.htmlentities($j['content']).'" >' . "\n";
	} 
	foreach( $vue['structure']['head_tags']['othertags'] as $i=>$j ){
		echo "\t".$j['data'] . "\n";
	} ?>
	<script>var page_data = <?=json_encode($result_body) ?>;</script>
		</head>
		<body><?php
		foreach( $vue['structure']['blocks'] as $i=>$j ){
			if( $j['type'] == "html" ){
				echo $j['data'];
			}else if( $j['type'] == "javascript" ){
				echo "<script>" . $j['data'] . "</script>";
			}else if( $j['type'] == "style" ){
				echo "<style>" . $j['data'] . "</style>";
			}
		}
		?>
		<script>
			var vstructure = <?=json_encode($vue) ?>;
			var vapp = {
				data: function()<?=$data_block ?>,
				mounted: function()<?=$vue['mounted']['data'] ?>,
				methods: <?=$vue['methods']['data'] ?>,
			};
			<?php if( $vue['template_use'] ){ ?>
				vapp['template'] = `<?=$vue['template']['data'] ?>`;
			<?php } ?>
			var app = Vue.createApp(vapp);
			<?php foreach( $vue['components'] as $i=>$j ){ ?>
				var component_<?=$j['name'] ?> = {
					data: function()<?=$j['data']['data'] ?>,
					mounted: function()<?=$j['mounted']['data'] ?>,
					methods: <?=$j['methods']['data'] ?>,
					template: `<?=$j['template']['data'] ?>`,
				};
				app.component( "<?=$j['name'] ?>" , component_<?=$j['name'] ?> );
			<?php } ?>

			<?php if( $vue['router_enable'] === true ){ ?>

				const component_none = {
					template: `<div><div>&nbsp;</div></div>`
				};

				const routes = <?=$routes_str ?>;

				const router = VueRouter.createRouter({
					history: VueRouter.createWebHistory(), routes
				});
				app.use( router );
			<?php } ?>
			var app1 = app.mount( "<?=$vue['element'] ?>" );
		</script>
		</body>
		</html>
		<?php
		$d = ob_get_clean();

	}else if( $page_version['type'] == "dynamic" ){

		$dynamic = $page_version['dynamicstructure'];

		foreach( $dynamic['blocks'] as $i=>$j ){
			$dynamic['blocks'][ $i ]['data'] = str_replace("%base_path%", $config_global_apimaker_engine['config_engine_path'] . $page_version['name'], $dynamic['blocks'][ $i ]['data'] );
		}

ob_start();
?><html>
<head>
	<meta charset="utf-8">
	<title><?=htmlentities($dynamic['head_tags']['title']) ?></title>
<?php 
	foreach( $dynamic['head_tags']['meta-names'] as $i=>$j ){
		echo "\t".'<meta name="'.$j['name'].'" content="'.htmlentities($j['content']).'" >' . "\n";
	}
	foreach( $dynamic['head_tags']['meta-props'] as $i=>$j ){
		echo "\t".'<meta property="'.$j['name'].'" content="'.htmlentities($j['content']).'" >' . "\n";
	} 
	foreach( $dynamic['head_tags']['othertags'] as $i=>$j ){
		echo "\t".$j['data'] . "\n";
	} ?>
	<script>var page_data = <?=json_encode($result_body) ?>;</script>
</head>
<body>
<?php
foreach( $dynamic['blocks'] as $i=>$j ){
	if( $j['type'] == "html" ){
		echo $j['data'] . "\n\n";
	}else if( $j['type'] == "javascript" ){
		echo "<script>" . $j['data'] . "</script>\n\n";
	}else if( $j['type'] == "style" ){
		echo "<style>" . $j['data'] . "</style>\n\n";
	}
}
?>
</body>
</html>
		<?php
		$d = ob_get_clean();

	}else{

		$d = "<div>Unhandled page type";
	}

	return [ 200, "text/html", $h, $d ];

}