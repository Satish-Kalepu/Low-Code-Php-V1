<?php
	require_once("vendor/autoload.php");

	if( file_exists("../../../config_engine.php") ){
		require("../../../config_engine.php");
	}else if( file_exists("../../config_engine.php") ){
		require("../../config_engine.php");
	}else if( file_exists("../config_engine.php") ){
		require("../config_engine.php");
	}else{
		http_response_code(500);echo "config_engine missing";exit;
	}

	if( !isset($deployment_mode) ){
		http_response_code(500);
		echo "config deployment_mode missing";exit; 
	}
	if( !isset($execution_mode) ){
		http_response_code(500);
		echo "config execution_mode missing";exit; 
	}

	// define globals 

	$connections = [];
	$use_encrypted=true;
	$request_log_id= "";
	$php_input= "";
	$app_id="";$api_id="";$file_id="";$page_id="";$page_type="";
	$config_cloud_domain = $_SERVER['HTTP_HOST'];
	$hosting_path = "";

	if( $use_encrypted ){
		require_once("class_engine_encrypted.php");
	}else if( file_exists("class_engine.php") ){
		require_once("class_engine.php");
	}else{
		if( $deployment_mode == "apache" ){
			respond(500,"application/json",[], json_encode(["status"=>"fail", "error"=>"File version missing"]));
		}else{
			echo "File missing 1 ";
			exit;
		}
	}
	require("index_system_api.php");
	require("index_engine_api.php");
	require("index_engine_api_auth_api.php");
	require("index_engine_api_captcha.php");
	require("index_engine_api_files.php");
	require("index_engine_api_storage_vault.php");
	require("index_engine_api_table.php");
	require("index_engine_api_table_dynamic.php");
	require("index_engine_api_object.php");
	require("index_api.php");
	require("index_page.php");
	require("index_file.php");
	require("index_mapping.php");
	require("index_thumbs.php");

	$cache_refresh=false;
	$current_dir = __DIR__;

	if( $deployment_mode == "apache" ){
		if( $execution_mode == "local_folder" ){
			$config_paths = [
				"./config_global_engine.php",
				"../config_global_engine.php",
				"../../config_global_engine.php",
				"/var/tmp/config_global_engine.php",
				sys_get_temp_dir() . "/config_global_engine.php"
			];
			foreach( $config_paths as $j ){
				if( file_exists($j) ){
					require($j);
					break;
				}
			}

			$v = pathinfo($_SERVER['PHP_SELF'] );
			$hosting_path = $v['dirname'];
			//echo $hosting_path;exit;
			if( !$config_global_apimaker_engine ){
				if( $_SERVER['REQUEST_METHOD'] == "GET" ){
					if( file_exists("__install.php") ){
						$v = pathinfo($_SERVER['PHP_SELF'] );
						if( !isset($v['dirname']) || $v['dirname'] == "/" ){
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
		}else if( $execution_mode == "cloud_folder" ){
			if( file_exists("../config_global_engine.php") ){
				require( "../config_global_engine.php" );
				if( !isset($config_global_engine) ){
					http_response_code(500); echo "config file config_global_engine loaded but config missing";exit;
				}
			}else{
				http_response_code(500); echo "config file config_global_engine missing";exit;
			}
		}else{
			http_response_code(500); echo "incorrect execution_mode: ". htmlspecialchars($execution_mode);exit;
		}

	}else if( $deployment_mode == "lambda" ){
		http_response_code(500); echo "Lambda mode not enabled yet ";exit;
	}else{
		http_response_code(500); echo "incorrect deployment_mode: ". htmlspecialchars($deployment_mode);exit;
	}

	require( "common_functions.php" );
	require( "control_config.php" );

	$db_prefix = $config_global_engine[ "config_mongo_prefix" ];

	function respond( $statusCode, $ct, $headers = [], $body = "", $log = [] ){
		global $deployment_mode;
		global $mongodb_con;
		global $request_log_id;
		global $config_global_apimaker_engine;
		global $db_prefix;
		global $app_id; global $api_id; global $page_id; global $file_id; global $page_type;
		global $cache_refresh;
		$b = ["rs"=>$statusCode, "rct"=>$ct, "rh"=>$headers,"rsz"=>strlen($body)];
		if( preg_match("/(plain|json|html)/i", $ct) ){$b["rb"]=substr($body,0,500);}else{$b["rb"]="Binary";}
		if( isset($page_type)  ){$b['t'] = $page_type; }
		if( isset($app_id) && $app_id ){$b['app_id'] = $app_id; }
		if( isset($api_id) && $api_id ){$b['api_id'] = $api_id; }
		if( isset($page_id) && $page_id ){$b['page_id'] = $page_id;}
		if( isset($file_id) && $file_id ){$b['file_id'] = $file_id;}

		$mongodb_con->update_one( $db_prefix . "_zlog_requests", ["_id"=>$request_log_id], $b );

		if( $deployment_mode == "apache" ){

			if( $statusCode != 200 ){
				if( !is_numeric($statusCode) ){
					http_response_code(500);
					header( "Access-Control-Allow-Origin: *" );
					header( "Access-Control-Allow-Methods: *" );
					header( "Access-Control-Allow-Headers: *" );
					header( "Access-Control-Expose-Headers: *");
					header( "Content-Type: text/plain");
					header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
					header( "Cache-Control: post-check=0, pre-check=0", false );
					header( "Pragma: no-cache" );
					echo "Incorrect statuscode: \n" . print_r( $statusCode,true);
					exit;
				}
				http_response_code($statusCode);
			}

			header( "Access-Control-Allow-Origin: *" );
			header( "Access-Control-Allow-Methods: *" );
			header( "Access-Control-Allow-Headers: *" );
			header( "Access-Control-Expose-Headers: *");
			header( "Content-Type: " . $ct);
			header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
			header( "Cache-Control: post-check=0, pre-check=0", false );
			header( "Pragma: no-cache" );
			header( "AppCache: " . ($cache_refresh?"Miss":"Hit") );

			foreach( $headers as $i=>$j ){
				header( $i . ": " . $j );
			}
			echo $body;
			exit;
		}else if( $deployment_mode == "lambda" ){
			$isBase64Encoded = false;

			$headers[ "Access-Control-Allow-Origin"] = "*";
			$headers[ "Access-Control-Allow-Methods"] = "*";
			$headers[ "Access-Control-Allow-Headers"] = "*";
			$headers[ "Access-Control-Expose-Headers"] ="*";
			$headers[ "Content-Type"] = $ct;
			$headers[ "Cache-Control"] = "no-store, no-cache, must-revalidate, max-age=0";
			$headers[ "Pragma"] = "no-cache";
			$headers[ "AppCache"] = ($cache_refresh?"Miss":"Hit");

			return [
				'statusCode'=>$statusCode,
				'headers'=>$headers,
				'body'=>$body,
				'isBase64Encoded'=>$isBase64Encoded
			];
		}
	}
	function json_response($statusCode, $param1 ="", $param2 = "" ){
		if( is_string($param1 ) ){
			if( $param1 == "success" ){
				$st = json_encode( array("status"=>$param1, "data"=>$param2), JSON_PRETTY_PRINT );
			}else if( $param1 == "fail" ){
				$st = json_encode( array("status"=>$param1, "error"=>$param2), JSON_PRETTY_PRINT );
			}else{
				$st = json_encode( array("status"=>$param1, "data"=>$param2), JSON_PRETTY_PRINT );
			}
		}else if( is_array($param1) ){
			$st = json_encode( $param1, JSON_PRETTY_PRINT );
		}
		if( !$st || json_last_error() ){
			return [$statusCode,"text/plain",[], "Error output json encode: " . json_last_error_msg() ];
		}else{
			return [$statusCode,"application/json",[], $st ];
		}
	}
	function json_response2($statusCode, $content_type ="application/json", $headers = [], $body = ""){
		if( is_array($body) ){
			$rbody = json_encode( $body, JSON_PRETTY_PRINT );
		}
		if( !$st || json_last_error() ){
			return [500,"text/plain",[], "Error output json encode: " . json_last_error_msg() . "\n" . print_r($body,n) ];
		}else{
			return [$statusCode,$content_type,$headers, $rbody ];
		}
	}

function index_normal(){

	//normal apache handling

	global $mongodb_con;
	global $config_global_apimaker_engine;
	global $db_prefix;
	global $cache_refresh;
	global $last_updated;
	global $request_log_id;
	global $config_cloud_domain;
	global $php_input;
	global $app_id;
	global $api_id;global $file_id;global $page_id;global $page_type;
	global $deployment_mode; // apache / lambda / container
	global $execution_mode; // local_folder / cloud_folder
	global $hosting_path;

	if( $_SERVER['REQUEST_METHOD'] == "OPTIONS" ){
		respond(200,"application/json",[], json_encode(["status"=>"ok"]));
	}

	$last_updated = "0000-00-00 00:00:00";

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

	$request_log_id = $mongodb_con->generate_id();
	$res = $mongodb_con->insert( $db_prefix . "_zlog_requests" , [
		"_id"=>$request_log_id,
		"h"=>(isset($_SERVER['HTTP_REALHOST'])?$_SERVER['HTTP_REALHOST']:$_SERVER['HTTP_HOST']),
		"ip"=>$_SERVER['REMOTE_ADDR'],
		"m"=>$_SERVER['REQUEST_METHOD'],
		"u"=>$_SERVER['REQUEST_URI'],
		"ct"=>$_SERVER['CONTENT_TYPE'],
		"ua"=>$_SERVER['HTTP_USER_AGENT'],
		"s"=>"http",
		"p"=>$php_input,
		"sid"=>session_id()
	]);

	if( preg_match("/healthcheck/i", $_SERVER['HTTP_USER_AGENT']) ){
		respond(200,"application/json",[], json_encode(["status"=>"success","t"=>time()]));
		exit;
	}

	if( $_SERVER['HTTP_USER_AGENT'] == "Amazon Simple Notification Service Agent" ){
		$sns_req = json_decode($php_input,true);
		if( json_last_error() ){
			respond(500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Json parse error"]));
			exit;
		}
	}

	if( $deployment_mode == "apache" && $execution_mode == "cloud_folder" ){
		$cres = $mongodb_con->find_one( $db_prefix . "_cloud_domains", ['_id'=> $config_cloud_domain] );
		if( $cres['data'] ){
			$app_id = $cres['data']['app_id'];
		}else{
			respond(404,"application/json",[], json_encode([
				"status"=>"fail", 
				"error"=>$config_cloud_domain .": settings not found"
			]));
		}
	}else if( $deployment_mode == "apache" && $execution_mode == "local_folder" ){
		$app_id = $config_global_apimaker_engine[ 'config_engine_app_id' ];
	}else{
		respond(500,"application/json",[], json_encode(["status"=>"fail", "error"=>"Unknown execution mode"]));
	}

	$path = $_GET['request_url'];
	$sub_path = $_GET['request_url'];
	$path = $path?$path:"home";
	function url_repl($m){
		return "\\" . $m[0];
	}

	//echo $path;exit;

	$url_parts = parse_url( $path );
	if( isset($url_parts['path']) ){
		$path_params = explode("/", $url_parts['path'] );
	}else{
		$path_params = [];
	}

	$url_inputs = [];
	if( isset($_GET["version_id"]) ){
		$res = $mongodb_con->find_one($db_prefix."_apis_versions", ["app_id"=>$app_id, "_id"=>$_GET['version_id']] );
		if( $res['data'] ){ $api_version = $res['data']; }else{
			respond(404,"text/plain",[], "Api version not found");
		}
		$app_id = $api_version['app_id'];
		$api_id = $api_version['api_id'];
		$url_page_id = "";
		$page_type = "api";
	}else if( isset($_GET["page_version_id"]) ){
		$res = $mongodb_con->find_one($db_prefix."_pages_versions", ["app_id"=>$app_id,"_id"=>$_GET['page_version_id']] );
		if( $res['data'] ){ $page_version = $res['data']; }else{
			respond(404,"text/plain",[], "Page version not found");
		}
		$app_id = $page_version['app_id'];
		$page_id = $page_version['page_id'];
		$url_page_id = "";
		$page_type = "api";
	}else if( isset($_GET["function_version_id"]) ){
		$res = $mongodb_con->find_one($db_prefix."_functions_versions", ["app_id"=>$app_id,"_id"=>$_GET['function_version_id']] );
		if( $res['data'] ){ $api_version = $res['data']; }else{
			respond(404,"text/plain",[], "Function version not found");
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
			respond(404,"text/plain",[], "Api record not found");
		}
		$res = $mongodb_con->find_one($db_prefix."_apis_versions", [ "_id"=>$api['version_id'] ] );
		if( $res['data'] ){ $api_version = $res['data']; }else{
			respond(404,"text/plain",[], "Api version record not found");
		}
		$page_type = "api";
	}else{
		
		$url_page_id = "";
		$cache_refresh = false;
		if( $execution_mode == "local_folder" ){
			@mkdir(sys_get_temp_dir()  . "/apimaker",0777);
			$app_cache_path = sys_get_temp_dir()  . "/apimaker/app_" . $app_id . ".php";
			$app_var = "config_app_" . $app_id;
		}else{
			@mkdir(sys_get_temp_dir()  . "/engine",0777);
			$app_cache_path = sys_get_temp_dir()  . "/engine/app_" . $app_id . ".php";
			$app_var = "config_app_" . $app_id;
		}
		if( file_exists($app_cache_path) ){
			// echo "cache found";
			//echo "<pre>";echo file_get_contents($app_cache_path);exit;
			$cache_refresh = false;
			require( $app_cache_path );
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
						respond(404,"text/plain",[], "App not found .");
					}
				}
			}
		}else{
			$cache_refresh = true;
		}
		if( $cache_refresh ){
			$res = $mongodb_con->find_one($db_prefix."_apps", [ "_id"=>$app_id ] );
			if( $res['data'] ){}else{
				respond(404,"text/plain",[], "App not found ..");
			}
			$config_app = $res['data'];
			file_put_contents($app_cache_path, "<"."?php\n\$".$app_var." = " . var_export($res['data'],true) . "; ?".">" );
			if( file_exists($app_cache_path) ){
				//respond(200,"text/plain",[], $app_cache_path . " Found");
			}else{
				respond(500,"text/plain",[], "/var/lib".$app_cache_path . " Not found after create");
			}
		}

		if( isset($path_params[0]) && $path_params[0] == "_api" ){
			list($statusCode,$contenttype,$headers,$body,$log) = engine_api( $_SERVER['REQUEST_METHOD'], $_SERVER['CONTENT_TYPE'], $path_params, $php_input );
			respond($statusCode,$contenttype,$headers,$body,$log);
			exit;
		}else if( isset($path_params[0]) && $path_params[0] == "_api_system" ){
			list($statusCode,$contenttype,$headers,$body,$log) = system_api( $_SERVER['REQUEST_METHOD'], $_SERVER['CONTENT_TYPE'], $path_params, $php_input );
			respond($statusCode,$contenttype,$headers,$body,$log);
			exit;
		}

		// print_r( $path_params );
		// echo "<pre>";print_r( $config_app['mappings'] );exit;

		$page_type = "";
		$mapping_item = [];
		if( isset($config_app['mappings']) ){
			$folders = $path_params;
			if( sizeof($path_params) > 1 ){
				$fn = "/" . $path_params[0] . "/";
				//echo $fn;
				if( isset( $config_app['mappings'][ $fn ] ) ){
					$sub_path = str_replace($fn, "", "/". $path);
					$page_type = $config_app['mappings'][ $fn ]['type'];
					$mapping_item = $config_app['mappings'][ $fn ];
				}
			}
			if( $page_type == "" && sizeof($folders) > 2 ){
				$fn = "/" . $path_params[0] . "/" . $path_params[1] . "/";
				//echo $fn;
				if( isset( $config_app['mappings'][ $fn ] ) ){
					$sub_path = str_replace($fn, "", "/".$path);
					$page_type = $config_app['mappings'][ $fn ]['type'];
					$mapping_item = $config_app['mappings'][ $fn ];
				}
			}
			if( $page_type == "" && sizeof($folders) > 3 ){
				$fn = "/" . $path_params[0] . "/" . $path_params[1] . "/" . $path_params[2] . "/";
				//echo $fn;
				if( isset( $config_app['mappings'][ $fn ] ) ){
					$sub_path = str_replace($fn, "", "/".$path);
					$page_type = $config_app['mappings'][ $fn ]['type'];
					$mapping_item = $config_app['mappings'][ $fn ];
				}
			}
		}
		//exit;

		if( $mapping_item && $page_type == "page_rewrite" ){
			$version_id = $mapping_item['version_id'];
			$res = $mongodb_con->find_one($db_prefix."_pages_versions", [ "_id"=>$version_id ] );
			if( $res['data'] ){ $page_version = $res['data']; }else{
				respond(404,"text/plain",[], "Page version not found");
			}
			$page_id = $page_version['page_id'];
		}

		if( $page_type == "" ){
			if( $path != "home" && !isset($config_app['pages'][ $path ]) ){
				respond(404,"text/plain",[], "Path not found");
			}else if( isset($config_app['pages'][ $path ]) ){
				//echo "path found";exit;
			}else if( $path == "home" ){	
				if( $config_app['settings']['homepage']['t'] == "page" ){
					$version_id = explode(":",$config_app['settings']['homepage']['v'])[0];
					$config_app['pages'][ "home" ] = [
						't'=>'page',
						'version_id'=>$version_id,
					];
				}else{
					$config_app['pages'][ "home" ] = [
						't'=>'file',
						'_id'=>$config_app['settings']['homepage']['v'],
					];
				}
			}
			//echo "<pre>";print_r( $config_app );exit;
			$page_type = $config_app['pages'][ $path ]['t'];
			if( $page_type == "page" ){
				$version_id = $config_app['pages'][ $path ]['version_id'];
				$res = $mongodb_con->find_one($db_prefix."_pages_versions", [ "_id"=>$version_id ] );
				if( $res['data'] ){ $page_version = $res['data']; }else{
					respond(404,"text/plain",[], "Page version not found");
				}
				$page_id = $page_version['page_id'];
			}else if( $page_type == "api" ){
				$version_id = $config_app['pages'][ $path ]['version_id'];
				$res = $mongodb_con->find_one($db_prefix."_apis_versions", [ "_id"=>$version_id ] );
				if( $res['data'] ){ $api_version = $res['data']; }else{
					respond(404,"text/plain",[], "Api version not found");
				}
				$api_id = $api_version['api_id'];
			}else if( $page_type == "file" ){
				$version_id = $config_app['pages'][ $path ]['_id'];
				$res = $mongodb_con->find_one($db_prefix."_files", [ "_id"=>$version_id ] );
				if( $res['data'] ){ $file_version = $res['data']; }else{
					respond(404,"text/plain",[], "File not found");
				}
				$file_id = $file_version['_id'];
			}
		}
	}

	// print_r( $config_global_engine );
	// echo "--";
	// print_r( $config_global_apimaker_engine );
	// exit;

	if( $page_type == "page" ){
		list($statusCode,$contenttype,$headers,$body,$log) = index_page( $page_version, $_GET, $_POST );
		respond($statusCode,$contenttype,$headers,$body,$log);

	}else if( $page_type == "page_rewrite" ){
		list($statusCode,$contenttype,$headers,$body,$log) = index_page( $page_version, $_GET, $_POST );
		respond($statusCode,$contenttype,$headers,$body,$log);

	}else if( $page_type == "api" || $page_type == "function" ){
		list($statusCode,$contenttype,$headers,$body,$log) = index_api( $api_version, $_GET, $_POST, $php_input );
		respond($statusCode,$contenttype,$headers,$body,$log);

	}else if( $page_type == "file" ){
		list($statusCode,$contenttype,$headers,$body,$log) = index_file( $file_version );
		respond($statusCode,$contenttype,$headers,$body,$log);

	}else if( $page_type == "mapping" ){
		list($statusCode,$contenttype,$headers,$body,$log) = index_mapping( $mapping_item, $path );
		respond($statusCode,$contenttype,$headers,$body,$log);

	}else if( $page_type == "thumbs" ){
		list($statusCode,$contenttype,$headers,$body,$log) = index_thumbs( $mapping_item, $path, $sub_path );
		respond($statusCode,$contenttype,$headers,$body,$log);

	}else{
		respond(403,"text/plain",[], "Incorrect engine type " . $page_type);
	}

}

function index_lambda( $event ){ //lambda function handling

	global $request_context;
	global $response_context;

	$request_context = $event;
	$GLOBALS['errors'] = [];
	$response_context = ['cookies'=>[]];

	if( $_ENV["dolog"] ){

		//functions.insert_temp_event_log(event, "InternalAPISMS")

		try{
			$mr = new Aws\DynamoDb\Marshaler();
			$C = new Aws\DynamoDb\DynamoDbClient([
				'version' => 'latest',
				'region' => 'ap-south-1',
				'credentials' => [
					'key' => "",
					'secret' => "",
				]
			]);
			$C->putItem([
				'TableName'=>"temp_events",
				'Item'=>$mr->marshalItem([
					"logname" => "LambdaPHP",
					"time" => date("Y-m-d H:i:s"),
					"expire" => time() + ( 30 * 86400 ),
					"event" => $event
				])
			]);
		}catch(Exception $ex){
			return respond(['statusCode'=>500,'body'=>"Error logging: ". $ex->getMessage()] );
		}
	}

	echo "Validating prerequisites...\n";
	if( !$_ENV['config_allow_domains'] ){
		return respond( ['statusCode'=>400, 'body'=>"Config Allow Domains: Missing"] );
	}else{
		// validate allowed domains in captcha page
	}
	if( !$_ENV['config_captcha_tablename'] ){
		return respond( ['statusCode'=>400, 'body'=>"config_captcha_tablename: Missing"] );
	}
	if( !$_ENV['config_captcha_region'] ){
		return respond( ['statusCode'=>400, 'body'=>"config_captcha_region: Missing"] );
	}
	if( !$_ENV['AWS_SESSION_TOKEN'] && !$_ENV['config_local_aws_key'] && !$_ENV['config_local_aws_secret'] ){
		return respond( ['statusCode'=>400, 'body'=>"Missing credentials"] );
	}

	echo "Parsing...\n";

	if( !$event["requestContext"] && !$event["path"] && !$event["httpMethod"] ){
		if( $event["Records"] ){
			if( $event["Records"][0]["EventSource"] == "aws:sns" ){
				return process_sns_event( $event );
			}else{
				return ['statusCode'=>200, 'body'=> json_encode(["status"=>"Unknown event"]) ];
			}
		}else{
			return ['statusCode'=>403, 'body'=> json_encode(["status"=>"Unknown request type"]) ];
		}
	}else if( $event["requestContext"] && $event["requestContext"]["http"]["path"] && $event["requestContext"]["http"]["method"] && $event["headers"] ){
		$response = process_lambda_request();
		if( $GLOBALS['errors'] && $GLOBALS['config_display_errors'] ){
			return json_encode([
				"statusCode"=>500,
				"headers"=>["content-type"=> "text/plain"],
				"body"=>implode("\n",$GLOBALS['errors']),
			]);
		}
		return respond( $response );
	}else if( $event["requestContext"] && $event["path"] && $event["httpMethod"] && $event["headers"] ){
		$response = process_lambda_request();
		if( $GLOBALS['errors'] && $GLOBALS['config_display_errors'] ){
			return json_encode([
				"statusCode"=>500,
				"headers"=>["content-type"=> "text/plain"],
				"body"=>implode("\n",$GLOBALS['errors']),
			]);
		}
		return respond( $response );
	}else{
		return ['statusCode'=> 403, 'body'=> json_encode(["status"=> "Unknown request type"]) ];
	}

}

function process_lambda_request(){
	global $request_context;
	global $config_mime_types;
	global $config_rewrites;
	global $con;
	$method = $request_context['httpMethod']?$request_context['httpMethod']:$request_context["requestContext"]["http"]["method"];
	$path = $request_context['path']?$request_context['path']:$request_context['rawPath'];
	echo "Processing...xx--".$path."\n<BR>";

	foreach( $config_rewrites as $i=>$j ){
		if( preg_match( $j['regexp'], $path, $m ) ){
			$path = $j['path'];
			if( preg_match('/\$1/',$path) ){
				$path = str_replace( '$1', $m[1], $path );
			}
			if( preg_match('/\$2/',$path) ){
				$path = str_replace( '$2', $m[2], $path );
			}
			if( preg_match('/\$3/',$path) ){
				$path = str_replace( '$3', $m[3], $path );
			}
			if( preg_match('/\$4/',$path) ){
				$path = str_replace( '$4', $m[3], $path );
			}
			echo "Rewrite...".$path."\n<BR>";
			break;
		}
	}

	$parts = explode("/", $path);
	$page = $parts[1]?$parts[1]:"home";
	$param = $parts[2]?$parts[2]:"default";
	$params = [];
	if( sizeof( $parts ) > 2 ){
		$params = array_slice($parts,2,10);
	}

	foreach( $request_context['headers'] as $i=>$j ){
		$request_context['headers'][ strtolower($i) ] = $j;
	}

	//print_pre( $request_context['headers'] );exit;

	if( $request_context['headers']['cookie']){
		$cookies = [];
		$x = explode(";", $request_context['headers']['cookie'] );
		foreach( $x as $i=>$j ){
			$xx = explode("=", $j);
			$cookies[ trim($xx[0]) ] = urldecode(trim($xx[1]));
		}
		$request_context['headers']['cookie'] = $cookies;
		//print_pre( $cookies );exit;
	}

	//echo ".".$path;
	if( preg_match("/\.(html|js|css|jpeg|png|jpg|ico)$/i", $path) ){
		//echo ".".$path;exit;
		if( file_exists(".".$path) ){
			// static content can be served as it is
			$u = pathinfo($path);
			$mime_type = $config_mime_types[ $u['extension'] ]?$config_mime_types[ $u['extension'] ]:"application/octet-stream";
			return [
				"status"=> 200,
				"body"=>base64_encode(file_get_contents(".".$path)),
				"headers"=>[
					"Content-Type"=>$mime_type,
				],
				"isBase64Encoded"=> true
			];
		}
	}
	if( $path == "/temp.php" ){require("temp.php");exit;}
	if( preg_match("/\.(php|htaccess|git)$/i", $path) ){
		return [
			"status"=> 403,
			"body"=>"Forbidden",
			"headers"=>[
				"Content-Type"=>"text/html",
			],
		];
	}

	if( $path == "/__help" ){
		return [
			"status"=> 200,
			"body"=>[
				"env"=>$_ENV,
				"server"=>$_SERVER,
				"requestContext"=>$request_context
			],
		];
	}

	if( $path == "/__phpinfo" ){
		$auth_status = http_auth_verify();
		if( $auth_status['status'] == "fail" ){
			return [
				"status"=> 401,
				"body"=>$auth_status['error'],
				"headers"=>["Content-Type"=>"text/html","WWW-Authenticate"=>"Basic realm=\"My Realm\"","error"=>$auth_status['error']],
			];
		}
		ob_start();phpinfo();$d = ob_get_clean();
		return [
			"status"=> 200,
			"body"=>$d,
			"headers"=>["Content-Type"=>"text/html"],
		];
	}
	if( !class_exists( $page ) ){
		if( file_exists("page_".$page .".php") ){
			try{
				require_once( "page_".$page .".php" );
			}catch(Exception $ex){
				return [
					"status"=> 500,
					"body"=>"Error initializing ".$page." class",
					"headers"=>["Content-Type"=>"text/html"],
				];
			}
		}else{
			return [
				"status"=> 404,
				"body"=>"Class ".$page." not found",
				"headers"=>["Content-Type"=>"text/html"],
			];
		}
		if( !class_exists( $page ) ){
			return [
				"status"=> 500,
				"body"=>"Error initializing " . $page. " class constructor!",
				"headers"=>["Content-Type"=>"text/html"],
				"env"=>$_ENV,
				"server"=>$_SERVER
			];
		}
	}
	if( $request_context['isBase64Encoded'] ){
		$request_context['body'] = base64_decode($request_context['body']);
	}
	if( preg_match("/json/i", $request_context['headers']['content-type']) ){
		$body =json_decode($request_context['body'],true);
		if( json_last_error() ){
			return [
				"status"=> 400,
				"body"=>"Request parse failed! ". json_last_error_msg(),
				"headers"=>["Content-Type"=>"text/html"]
			];
		}
		$request_context['body'] = $body;
	}
	if( preg_match("/urlencoded/i",$request_context['headers']['content-type']) ){
		$x = explode("&", $request_context['body']);
		$b =[];
		foreach( $x as $i=>$j ){
			$xx = explode("=",$j);
			$b[ trim($j[0]) ] = urldecode(trim($j[1]));
		}
		$request_context['body'] = $body;
	}
	echo "Executing...\n";
	try{
		$page_object = new $page($request_context,$params);
		$function = $method . "_" .$param;
		if( method_exists( $page_object, $function ) ){
			$res = $page_object->$function($params );
			if( is_array($res) ){
				if( $res['statusCode'] && $res['body'] ){
					$response = [
						"statusCode"=> $res['statusCode'],
						"body"=>$res['body'],
						"headers"=>$res['headers']?$res['headers']:[],
						"isBase64Encoded"=>$res['isBase64Encoded']?$res['isBase64Encoded']:false,
					];
				}else{
					$response = [
						"statusCode"=> $page_object->status,
						"body"=>$page_object->response,
						"headers"=>$page_object->headers,
						"isBase64Encoded"=>$page_object->isBase64Encoded,
					];
				}
			}else{
				$response = [
					"statusCode"=> $page_object->status,
					"body"=>$page_object->response,
					"headers"=>$page_object->headers,
					"isBase64Encoded"=>$page_object->isBase64Encoded,
				];
			}
		}else{
			$response = [
				"statusCode"=> 404,
				"body"=>"page.method not found",
			];
		}
		if( is_string($response['response']) ){
			$isok = false;
			foreach( $response['headers'] as $i=>$j ){
				if( strtolower($i) == "content-type" ){
					if( preg_match("/(html|image)/i", $j ) ){
						$isok = true;
					}
				}
			}
			if( !$isok ){
				$response['headers']['content-type']="text/html";
				$response['headers']['Content-Type']="text/html";
			}
		}
		return $response;
	}catch(Exception $ex){
		return [
			"statusCode"=> 500,
			"body"=>$ex->getMessage(),
			"headers"=>["content-type"=>"text/plain"],
		];
	}
}

if( $deployment_mode == "apache" ){
	index_normal();
}else{
	index_lambda();
}