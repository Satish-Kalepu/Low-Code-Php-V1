<?php
function execute_curl_request($apidata = array(),$postbody = ""){
	$timeout = ((int)$apidata["timeout"]>0?$apidata["timeout"]:20);

	if(!$apidata["headers"] || !is_array($apidata["headers"])){
		$return_result = ["status"=>"fail","data"=>"headers parameter  is empty"];
		return $return_result ;
		exit;
	}

	if(!$apidata["method"] || $apidata["method"] ==""){
		$return_result = ["status"=>"fail","data"=>"action parameter  is empty"];
		return $return_result ;
		exit;
	}

	if(!$apidata["url"] || $apidata["url"] ==""){
		$return_result = ["status"=>"fail","data"=>"url parameter  is empty"];
		return $return_result ;
		exit;
	}

	$ch = curl_init();
	$options = array(
		CURLOPT_HEADER => 0,
		CURLOPT_URL => $apidata['url'],
		CURLOPT_CONNECTTIMEOUT_MS=> 5000,
		CURLOPT_TIMEOUT => (int)$timeout,
		CURLOPT_RETURNTRANSFER =>true,
		CURLOPT_AUTOREFERER=>true,
		CURLOPT_SSL_VERIFYHOST=>false,
		CURLOPT_SSL_VERIFYPEER=>false
	);
	$request_headers = [];
	$is_user_agent=false;
	$is_content_type=false;
	foreach( $apidata['headers'] as $i=>$j ){
		$request_headers[] = $i.": ". $j;
		if( strtolower((string)$i) == "user-agent" ){
			$is_user_agent=true;
		}
		if( strtolower((string)$i) == "content-type" ){
			$is_content_type=true;
		}
	}
	if( !$is_user_agent ){
		$request_headers[] = "User-Agent: ".$_SERVER['HTTP_USER_AGENT'];
	}
	$url_parts = parse_url($apidata['url']);

	if( $apidata['method'] == "POST" ){
		$options[CURLOPT_POST] = 1;
		$options[CURLOPT_POSTFIELDS] = $postbody;
		if( !$is_content_type ){
			if( $apidata['content-type'] ){
				if( $api['content-type']  == "multipart/form-data" ){
					return false;
				}else{
					$request_headers[] = "Content-Type: " . $apidata['content-type'];
				}
			}
		}
	}else if( $apidata['method'] == "GET" ){
		$options[CURLOPT_HTTPGET] =1;
	}else if( $apidata['method'] == "PUT" ){
		return ["status"=>"fail", "error"=>"Method not implemented"];
		$options[CURLOPT_PUT] =1;
	}
	if( sizeof($request_headers) ){
		$options[CURLOPT_HTTPHEADER] = $request_headers;
	}

	curl_setopt_array( $ch, $options );
	$result = curl_exec( $ch );
	$info = curl_getinfo( $ch );
	if( $info["content_type"] ){
		$content_type=explode(";",$info["content_type"])[0];
	}else{
		$content_type="text/plain";
	}
	
	$status = "ok";
	$errtxt = curl_error( $ch );
	$errno = curl_errno( $ch );

	if( $errno ){
		$status = "CurlError";
		$error = $errno .":" .$errtxt;
	}else if($info["http_code"] == 0 && round($info["total_time"]) >= $timeout){
		$status = "timeout";
	}else if($info["http_code"] == 0){
		$status = "timeout";
	} 

	$return_result = [
		"status"=>$status,
		"curl_info"=>$info,
		"response"=>$result,
		"http_code"=>$info['http_code'],
		"error"=>$error,
		"content_type"=>$info['content_type'],
		"total_time"=>$info['total_time']
	];
	
	return $return_result;
}

if( $_POST['action'] == "get_pages" ){
	$t = validate_token("getpages.". $config_param1, $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		'app_id'=>$config_param1
	],[
		'sort'=>['name'=>1],
		'limit'=>200,
		'projection'=>[
			'version_id'=>true,
			'name'=>true,
		]
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == "delete_page" ){
	$t = validate_token("deletepage". $config_param1 . $_POST['page_id'], $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['page_id']) ){
		json_response("fail", "ID incorrect");
	}
	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		'_id'=>$_POST['page_id']
	]);
	$res = $mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
		'page_id'=>$_POST['page_id']
	]);
	update_app_pages( $config_param1 );
	json_response($res);
}

if( $_POST['action'] == "create_page" ){
	if( !preg_match("/^[a-z][a-z0-9\.\-\_]{2,100}$/i", $_POST['new_page']['name']) ){
		json_response("fail", "Name incorrect");
	}
	if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{2,250}$/i", $_POST['new_page']['des']) ){
		json_response("fail", "Description incorrect");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		"app_id"=>$config_param1,
		'name'=>$_POST['new_page']['name']
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}

	if( file_exists("page_themes/". $_POST['new_page']['template'] . ".html" ) ){
		$html = file_get_contents("page_themes/". $_POST['new_page']['template'] . ".html");
	}else{
		$html = file_get_contents("page_themes/blog.html");
	}

	$version_id = $mongodb_con->generate_id();
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		"app_id"=>$config_param1,
		"name"=>$_POST['new_page']['name'],
		"des"=>$_POST['new_page']['des'],
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
		"active"=>true,
		"version"=>1,
		"version_id"=>$version_id,
	]);
	if( $res['status'] == 'success' ){
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
			"_id"=>$mongodb_con->get_id($version_id),
			"app_id"=>$config_param1,
			"page_id"=>$res['inserted_id'],
			"name"=>$_POST['new_page']['name'],
			"des"=>$_POST['new_page']['des'],
			"created"=>date("Y-m-d H:i:s"),
			"updated"=>date("Y-m-d H:i:s"),
			"active"=>true,
			"version"=>1,
			"html"=>$html
		]);
		update_app_pages( $config_param1 );
		json_response($res);
	}else{
		json_response($res);
	}
	exit;
}


if( $config_param3 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param3) ){
		echo404("Incorrect PAGE ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		'app_id'=>$app['_id'],
		'_id'=>$config_param3
	]);
	if( !$res['data'] ){
		echo404("Page not found!");
	}
	$main_page = $res['data'];
}

if( $config_param4 && $main_page ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param4) ){
		echo404("Incorrect PAGE Version ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
		"page_id"=>$main_page['_id'],
		"_id"=>$config_param4
	]);
	if( $res['data'] ){
		$page = $res['data'];
	}else{
		echo404("Page version not found!");
	}

	if( $_POST['action'] == "save_page" ){
		if( $_POST['page_version_id'] != $config_param4 ||  $_POST['app_id'] != $config_param1 ){
			json_response("fail","Incorrect URL");
		}
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
			"app_id"=> $config_param1,
			"_id"=>$config_param4
		],[
			"html"=>$_POST['html'],
			"script"=>$_POST['script'],
			"settings"=>$_POST['settings'],
			"updated"=>date("Y-m-d H:i:s"),
		]);
		if( $res["status"] == "fail" ){
			json_response("fail",$res["error"]);
		}
		json_response("success","ok");
	}

	if($_POST['action'] == "crawl_website") {
		if( $_POST['page_version_id'] != $config_param4 ||  $_POST['app_id'] != $config_param1 ){
			json_response("fail","Incorrect URL");
		}
		if(!isset($_POST['crawl_link']) || $_POST['crawl_link'] == "") {
			json_response("fail","Please enter Crawl Link");
		}
		$content = "";

		$apidata = [];
		$apidata['url'] = $_POST['crawl_link'];
		$apidata['method'] = "GET";
		$apidata['headers'] = ["User-Agent: ".$_SERVER['HTTP_USER_AGENT']];
		$curl_response = execute_curl_request($apidata,"");

		if( $curl_response['status'] == "ok" && $curl_response['http_code'] == 200 ) {
			$content = $curl_response['response'];
		}else {
			json_response("fail",$curl_response['error']);
		}

		$code = "";

		/* CSS code extraction using links provided start */
		if($_POST['crawl_dtls']['css_type'] == "external" && count($_POST['crawl_dtls']['css_links']) > 0) {
			$css_code = "";
			$css_code.="<style>";
			foreach($_POST['crawl_dtls']['css_links'] as $i => $j) {
				$css_content = "";

				$apidata = [];
				$apidata['url'] = $j;
				$apidata['method'] = "GET";
				$apidata['headers'] = ["User-Agent: ".$_SERVER['HTTP_USER_AGENT']];
				$curl_response = execute_curl_request($apidata,"");

				if( $curl_response['status'] == "ok" && $curl_response['http_code'] == 200 ) {
					$css_content = $curl_response['response'];
				}else {
					json_response("fail",$curl_response['error']);
				}

				$pattern = '/<\s*(link|meta)\b[^>]*\/?\s*>/i';
				$css_code.= preg_replace($pattern,'',$css_content);
				$css_code.= preg_replace('/<title\b[^>]*>([\s\S]*?)<\/title>/i','',$css_code);
			}
			$css_code.="</style>";
			$code.=$css_code;
		}
		/* CSS code extraction using links provided ends */

		preg_match_all('/<html\b[^>]*>([\s\S]*?)<\/html>/i', $content, $matches);
		$code = $code.implode("\n\n", $matches[1]);

		$pattern = '/<\s*(link|meta)\b[^>]*\/?\s*>/i';
		$code = preg_replace($pattern,'',$code);
		$code = preg_replace('/<title\b[^>]*>([\s\S]*?)<\/title>/i','',$code);
		$code = preg_replace('/<script\b[^>]*>([\s\S]*?)<\/script>/i','',$code);
		$code = preg_replace('/\s{2,}/', ' ', $code);
		$code = trim($code);

		preg_match_all('/<script\b[^>]*>([\s\S]*?)<\/script>/i', $content, $matches);
		$jscode = implode("\n\n", $matches[1]);

		/* js code extraction using links provided start */
		if($_POST['crawl_dtls']['js_type'] == "external" && count($_POST['crawl_dtls']['js_links']) > 0) {
			$js_code = "";
			foreach($_POST['crawl_dtls']['js_links'] as $i => $j) {
				$content = "";

				$apidata = [];
				$apidata['url'] = $j;
				$apidata['method'] = "GET";
				$apidata['headers'] = ["User-Agent: ".$_SERVER['HTTP_USER_AGENT']];
				$curl_response = execute_curl_request($apidata,"");

				if( $curl_response['status'] == "ok" && $curl_response['http_code'] == 200 ) {
					$content = $curl_response['response'];
				}else {
					json_response("fail",$curl_response['error']);
				}

				$js_code.= preg_replace('/<script\b[^>]*>([\s\S]*?)<\/script>/i','',$js_code);
			}
			$jscode.=$js_code;
		}
		/* js code extraction using links provided ends */

		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
			"app_id"=> $config_param1,
			"_id"=>$config_param4
		],[
			"html"=>$code,
			"script"=>$jscode,
			"updated"=>date("Y-m-d H:i:s"),
		],[
			"socketTimeoutMS" => 25000,
			"connectTimeoutMS" => 10000
		]);
		if( $res["status"] == "fail" ){
			json_response("fail",$res["error"]);
		}
		json_response("success","ok");
	}
}