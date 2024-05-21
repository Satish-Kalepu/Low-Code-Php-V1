<?php

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

		$url = $_POST['crawl_link'];

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(),
		));

		$content = curl_exec($curl);
		$info = curl_getinfo($curl);
		$error = curl_error( $curl );
		$errorno = curl_errno( $curl );

		if(curl_errno($curl)) {
			json_response("fail",$error);
		}

		curl_close($curl);

		$Code = "";

		preg_match_all('/<html\b[^>]*>([\s\S]*?)<\/html>/i', $content, $matches);
		$Code = $Code.implode("\n\n", $matches[1]);

		$pattern = '/<\s*(link|meta)\b[^>]*\/?\s*>/i';
		$Code = preg_replace($pattern,'',$Code);
		$Code = preg_replace('/<title\b[^>]*>([\s\S]*?)<\/title>/i','',$Code);
		$Code = preg_replace('/<script\b[^>]*>([\s\S]*?)<\/script>/i','',$Code);
		
		$Code = preg_replace('/\s{2,}/', ' ', $Code);
		$Code = trim($Code);

		preg_match_all('/<script\b[^>]*>([\s\S]*?)<\/script>/i', $content, $matches);
		$jsCode = implode("\n\n", $matches[1]);

		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
			"app_id"=> $config_param1,
			"_id"=>$config_param4
		],[
			"html"=>$Code,
			"script"=>$jsCode,
			"updated"=>date("Y-m-d H:i:s"),
		]);
		if( $res["status"] == "fail" ){
			json_response("fail",$res["error"]);
		}
		json_response("success","ok");
	}
}