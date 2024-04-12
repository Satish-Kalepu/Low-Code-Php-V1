<?php

	$use_encrypted=true;
	$current_dir = __DIR__;
	$config_paths = [
		"./config_global_engine.php",
		"../config_global_engine.php",
		"../../config_global_engine.php",
		"/var/tmp/config_global_engine.php"
	];
	foreach( $config_paths as $j ){
		if( file_exists($j) ){
			require($j);
			break;
		}
	}
	if( !$config_global_apimaker_engine ){
		if( $_SERVER['REQUEST_METHOD'] == "GET" ){
			if( file_exists("__install.php") ){
				$v = pathinfo($_SERVER['PHP_SELF'] );
				if( !isset($v['dirname']) ){
					echo "No configuration found!<BR>Please follow installation procedures";exit;
				}
				header("Location: " . $v['dirname']. "/__install.php");
				exit;
			}
		}else{
			http_response_code(500);
			echo json_encode(["status"=>"fail","error"=>"APP not configured"]);exit;
		}
	}

	require( "common_functions.php" );
	require( "control_config.php" );

	function respond( $http_status= 200, $text_body= "", $headers = [] ){
		global $db_prefix;
		global $mongodb_con;
		global $request_log_id;
		if( $http_status != 200 ){
			http_response_code((int)$http_status);
		}
		foreach( $headers as $i=>$j ){
			header($i . ": ". $j);
		}
		echo $text_body;
	}

	//print_r( $config_global_engine );exit;

	//echo "sxxx";exit;
	$db_prefix = $config_global_engine[ "config_mongo_prefix" ];
	//require("config_alias_domains.php");
	if( $_SERVER['HTTP_X_FORWARDED_FOR'] ){
		$d = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR'] );
		if( sizeof($d) == 2 ){
			$_SERVER['REMOTE_ADDR'] = trim($d[0]);
			$_SERVER['HTTP_X_REAL_IP'] = trim($d[1]);
		}else if( sizeof($d) == 3 ){
			$_SERVER['REMOTE_ADDR'] = trim($d[1]);
			$_SERVER['HTTP_X_REAL_IP'] = trim($d[2]);
		}else{
			$_SERVER['REMOTE_ADDR'] = trim($d[0]);
			$_SERVER['HTTP_X_REAL_IP'] = trim($d[0]);
		}
	}else{
		$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP']? $_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR'];
	}
	$php_input = file_get_contents( 'php://input' );

	if( preg_match("/healthcheck/i", $_SERVER['HTTP_USER_AGENT']) ){
		header("Content-Type: application/json");
		echo json_encode(["statusCode"=>200,"body"=>json_encode(["status"=>"success","t"=>time()]) ]);
		exit;
	}
	$request_log_id = $mongodb_con->generate_id();
	function raw_request_log( $status ){
		global $mongodb_con;
		global $request_log_id;
		global $php_input;
		$mongodb_con->insert($db_prefix."log_req_" , [
			"_id"=>$request_log_id,
			"domain"=>($_SERVER['HTTP_REALHOST']?$_SERVER['HTTP_REALHOST']:$_SERVER['HTTP_HOST']),
			"url"=>$_SERVER['REQUEST_URI'],
			"method"=>$_SERVER['REQUEST_METHOD'],
			"ctype"=>$_SERVER['CONTENT_TYPE'],
			"inputs"=>['get'=>$_GET, 'post'=>$_POST,'input'=>$php_input],
			"ip"=>$_SERVER['REMOTE_ADDR'],
			"user-agent"=>$_SERVER['HTTP_USER_AGENT'],
		]);
	}

	if( $_SERVER['HTTP_USER_AGENT'] == "Amazon Simple Notification Service Agent" ){
		$sns_req = json_decode($php_input,true);
		if( json_last_error() ){
			raw_request_log("JSON PARSE ERROR");
			header("Content-Type: application/json");
			echo json_encode(["statusCode"=>200,"body"=>json_encode(["status"=>"fail", "error"=>"Json parse error", "request_log_id"=>$request_log_id, "t"=>time()]) ]);
			exit;
		}
		//raw_request_log("success");
		if( $sns_req['Type'] == "Notification" ){

		}
	}
	if( preg_match("/^\/function_call\//", $_SERVER['REQUEST_URI']) || preg_match("/^\/sns_call1/", $_SERVER['REQUEST_URI']) ){
		raw_request_log("success");
		header("Content-Type: application/json");
		echo json_encode(["statusCode"=>200,"body"=>json_encode(["status"=>"success","t"=>time()]) ]);
		exit;
	}

	$last_updated = "0000-00-00 00:00:00";

	$path = $_GET['request_url'];
	$path = $path?$path:"home";
	function url_repl($m){
		return "\\" . $m[0];
	}
	$url_parts = parse_url( $path );
	if( isset($url_parts['path']) ){
		$path_params = explode("/", $url_parts['path'] );
	}else{
		$path_params = [];
	}

	$url_inputs = [];
	if( isset($_GET["version_id"]) ){
		$res = $mongodb_con->find_one($db_prefix."_apis_versions", ["_id"=>$_GET['version_id']] );
		if( $res['data'] ){ $api_version = $res['data']; }else{
			http_response_code(404);echo "API Version not found!";exit;
		}
		$app_id = $api_version['app_id'];
		$api_id = $api_version['api_id'];
		$url_page_id = "";
		$page_type = "api";
	}else if( isset($_GET["page_version_id"]) ){
		$res = $mongodb_con->find_one($db_prefix."_pages_versions", ["_id"=>$_GET['page_version_id']] );
		if( $res['data'] ){ $page_version = $res['data']; }else{
			http_response_code(404);echo "API Version not found!";exit;
		}
		$app_id = $page_version['app_id'];
		$page_id = $page_version['page_id'];
		$url_page_id = "";
		$page_type = "api";
	}else if( isset($_GET["function_version_id"]) ){
		$res = $mongodb_con->find_one($db_prefix."_functions_versions", ["_id"=>$_GET['function_version_id']] );
		if( $res['data'] ){ $api_version = $res['data']; }else{
			http_response_code(404);echo "API Version not found!";exit;
		}
		$app_id = $api_version['app_id'];
		$api_id = $api_version['api_id'];
		$url_page_id = "";
		$page_type = "function";
	}else if( preg_match("/^([a-f0-9]{24})\/([a-f0-9]{24})$/", $url_parts['path'], $m ) ){
		$app_id = $m[1];
		$api_id = $m[2];
		$res = $mongodb_con->find_one($db_prefix."_apis", [ "_id"=>$api_id ] );
		if( $res['data'] ){ $api = $res['data']; }else{
			http_response_code(404);echo "API Record not found!";exit;
		}
		$res = $mongodb_con->find_one($db_prefix."_apis_versions", [ "_id"=>$api['version_id'] ] );
		if( $res['data'] ){ $api_version = $res['data']; }else{
			http_response_code(404);echo "API Version Record not found!";exit;
		}
		$page_type = "api";
	}else{
		$app_id = $config_global_apimaker_engine[ 'config_engine_app_id' ];
		$url_page_id = "";
		$cache_refresh = false;
		$app_cache_path = "/tmp/apimaker/app_" . $app_id . ".php";
		$app_var = "config_app_" . $app_id;
		if( file_exists($app_cache_path) ){
			// echo "cache found";
			//echo "<pre>";echo file_get_contents($app_cache_path);exit;
			$cache_refresh = false;
			require_once( $app_cache_path );
			$config_app = ${$app_var};
			if( !$config_app ){
				$cache_refresh = true;
			}else if( filemtime($app_cache_path) < time()-(int)$config_global_apimaker_engine["config_engine_cache_interval"] ){
				$cache_refresh = true;
			}else{
				$k = $config_global_apimaker_engine["config_engine_cache_refresh_action_query_string"];
				if( $k ){
					if( isset($_GET[ array_keys($k)[0] ]) ){
						if( $_GET[ array_keys($k)[0] ] == $k[ array_keys($k)[0] ] ){
							$cache_refresh = true;
						}
					}
				}
				if( !$cache_refresh ){
					$res = $mongodb_con->find_one($db_prefix."_apps", [ "_id"=>$app_id ], ['projection'=>['last_updated'=>1] ] );
					if( $res['data'] ){
						//print_r( $res['data'] );
						$last_updated = $res['data']['last_updated'];
						//echo $last_updated . " >  " . $config_app['last_updated']; exit;
						if( $last_updated > $config_app['last_updated'] ){
							$cache_refresh = true;
						}
					}else{
						http_response_code(404);echo "App not found! ..";exit;
					}
				}
			}
		}else{
			$cache_refresh = true;
		}
		if( $cache_refresh ){
			$res = $mongodb_con->find_one($db_prefix."_apps", [ "_id"=>$app_id ] );
			if( $res['data'] ){}else{
				http_response_code(404);echo "App not found! ..";exit;
			}
			$config_app = $res['data'];
			file_put_contents($app_cache_path, "<"."?php\n\$".$app_var." = " . var_export($res['data'],true) . "; ?".">" );
			// echo "cache refreshed";
			// echo htmlspecialchars("<"."?php\n\$".$app_var." = " . var_export($res['data'],true) . "; ?".">");
			// exit;
		}

		// echo "<pre>";print_r( $config_app );exit;
		// echo $path; exit;
		// print_r( $path_params );exits;

		if( isset($path_params[0]) && $path_params[0] == "_api" ){
			require("index_engine_api.php");
			exit;
		}

		if( $path != "home" && !isset($config_app['pages'][ $path ]) ){
			http_response_code(404);echo "Path not found! ..";exit;
		}else if( isset($config_app['pages'][ $path ]) ){
			//echo "path found";exit;
		}else if( $path == "home" ){
			$version_id = explode(":",$config_app['settings']['homepage']['v'])[0];
			//echo "xx".$version_id;
			$res = $mongodb_con->find_one($db_prefix."_pages", [ "_id"=>$version_id ], ['projection'=>['version_id'=>1]] );
			//print_r( $res );
			if( $res['data'] ){ $version_id = $res['data']['version_id']; }else{
				http_response_code(404);echo "Page Version not found! ..";exit;
			}
			$config_app['pages'][ "home" ] = [
				't'=>'page',
				'version_id'=>$version_id,
			];
		}
		//echo "<pre>";print_r( $config_app );exit;
		$page_type = $config_app['pages'][ $path ]['t'];
		if( $page_type == "page" ){
			$version_id = $config_app['pages'][ $path ]['version_id'];
			$res = $mongodb_con->find_one($db_prefix."_pages_versions", [ "_id"=>$version_id ] );
			if( $res['data'] ){ $page_version = $res['data']; }else{
				http_response_code(404);echo "Page Version not found! ..";exit;
			}
			$page_id = $page_version['page_id'];
		}else if( $page_type == "api" ){
			$version_id = $config_app['pages'][ $path ]['version_id'];
			$res = $mongodb_con->find_one($db_prefix."_apis_versions", [ "_id"=>$version_id ] );
			if( $res['data'] ){ $api_version = $res['data']; }else{
				http_response_code(404);echo "API Version not found! ..";exit;
			}
			$api_id = $api_version['api_id'];
		}else if( $page_type == "file" ){
			$version_id = $config_app['pages'][ $path ]['_id'];
			$res = $mongodb_con->find_one($db_prefix."_files", [ "_id"=>$version_id ] );
			if( $res['data'] ){ $file_version = $res['data']; }else{
				http_response_code(404);echo "File not found! ..";exit;
			}
			$file_id = $file_version['_id'];
		}
	}

	require("vendor/autoload.php");
	if( $page_type == "page" ){
		header( "AppCache: " . ($cache_refresh?"Miss":"Hit") );
		header( "Access-Control-Allow-Origin: *" );
		header( "Access-Control-Allow-Methods: *" );
		header( "Access-Control-Allow-Headers: Content-Type" );
		header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
		header( "Cache-Control: post-check=0, pre-check=0", false );
		header( "Pragma: no-cache" );
		require("index_page.php");
	}else if( $page_type == "api" || $page_type == "function" ){
		header( "AppCache: " . ($cache_refresh?"Miss":"Hit") );
		header( "Access-Control-Allow-Origin: *" );
		header( "Access-Control-Allow-Methods: *" );
		header( "Access-Control-Allow-Headers: Content-Type" );
		header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
		header( "Cache-Control: post-check=0, pre-check=0", false );
		header( "Pragma: no-cache" );		
		require("index_api.php");
	}else if( $page_type == "file" ){
		if( !isset($file_version['t']) ){
			http_response_code(500);
			echo "Incorrect file type";
			exit;
		}
		header( "Content-Type: "  . $file_version['type']);
		header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
		header( "Cache-Control: post-check=0, pre-check=0", false );
		header( "Pragma: no-cache" );		
		if( $file_version['t'] == "inline" ){
			if( isset($file_version['vars_used']) ){
				if( is_array($file_version['vars_used']) ){
					$vars = [
						"--engine-url--" =>"https://" . $_SERVER['HTTP_HOST'] . $config_global_apimaker_engine['config_engine_path'],
						"--engine-path--"=>$config_global_apimaker_engine['config_engine_path'],
					];
					foreach( $file_version['vars_used'] as $var ){
						if( isset($vars[$var]) ){
							$file_version['data'] = str_replace( $var, $vars[$var], $file_version['data'] );
						}
					}
				}
			}
			echo $file_version['data'];
		}else if( $file_version['t'] == "base64" && preg_match("/^image/i", $file_version['type']) ){
			echo base64_decode($file_version['data']);exit;
		}else if( $file_version['t'] == "base64" ){
			echo base64_decode($file_version['data']);exit;
		}else{
			http_response_code(500);
			echo "Incorrect file type";
			exit;
		}
	}else{
		http_response_code(403);
		echo "Incorrect engine type! " . $page_type;
		exit;
	}
