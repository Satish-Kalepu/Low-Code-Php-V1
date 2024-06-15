<?php

$mime_types = array("aac" => "audio/aac","abw" => "application/x-abiword","apng" => "image/apng","arc" => "application/x-freearc","avif" => "image/avif","avi" => "video/x-msvideo","azw" => "application/vnd.amazon.ebook","bin" => "application/octet-stream","bmp" => "image/bmp","bz" => "application/x-bzip","bz2" => "application/x-bzip2","cda" => "application/x-cdf","csh" => "application/x-csh","css" => "text/css","csv" => "text/csv","doc" => "application/msword","docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document","eot" => "application/vnd.ms-fontobject","epub" => "application/epub+zip","gz" => "application/gzip","gif" => "image/gif","htm" => "text/html","html" => "text/html","ico" => "image/vnd.microsoft.icon","ics" => "text/calendar","jar" => "application/java-archive","jpeg" => "image/jpeg","jpg" => "image/jpeg","js" => "text/javascript","json" => "application/json","jsonld" => "application/ld+json","mid" => "audio/midi,audio/x-midi","midi" => "audio/midi,audio/x-midi","mjs" => "text/javascript","mp3" => "audio/mpeg","mp4" => "video/mp4","mpeg" => "video/mpeg","mpkg" => "application/vnd.apple.installer+xml","odp" => "application/vnd.oasis.opendocument.presentation","ods" => "application/vnd.oasis.opendocument.spreadsheet","odt" => "application/vnd.oasis.opendocument.text","oga" => "audio/ogg","ogv" => "video/ogg","ogx" => "application/ogg","opus" => "audio/opus","otf" => "font/otf","png" => "image/png","pdf" => "application/pdf","php" => "application/x-httpd-php","ppt" => "application/vnd.ms-powerpoint","pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation","rar" => "application/vnd.rar","rtf" => "application/rtf","sh" => "application/x-sh","svg" => "image/svg+xml","tar" => "application/x-tar","tif" => "image/tiff","tiff" => "image/tiff","ts" => "video/mp2t","ttf" => "font/ttf","txt" => "text/plain","vsd" => "application/vnd.visio","wav" => "audio/wav","weba" => "audio/webm","webm" => "video/webm","webp" => "image/webp","woff" => "font/woff","woff2" => "font/woff2","xhtml" => "application/xhtml+xml","xls" => "application/vnd.ms-excel","xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","xml" => "application/xml","xul" => "application/vnd.mozilla.xul+xml","zip" => "application/zip","3gp" => "video/3gpp; audio/3gpp","3g2" => "video/3gpp2; audio/3gpp2","7z" => "application/x-7z-compressed");

$media_file_types = array('ico' => 'image/vnd.microsoft.icon','jpeg' => 'image/jpeg','jpg'  => 'image/jpeg','png'  => 'image/png','gif'  => 'image/gif','bmp'  => 'image/bmp','svg'  => 'image/svg+xml','mp3'  => 'audio/mpeg','wav'  => 'audio/wav','ogg'  => 'audio/ogg','flac' => 'audio/flac','mp4'  => 'video/mp4','avi'  => 'video/x-msvideo','mov'  => 'video/quicktime','mkv'  => 'video/x-matroska','webm' => 'video/webm','pdf'  => 'application/pdf','doc'  => 'application/msword','docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document','xls'  => 'application/vnd.ms-excel','xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','txt'  => 'text/plain','rtf'  => 'application/rtf','md'   => 'text/markdown','zip'  => 'application/zip','gz'   => 'application/gzip','tar'  => 'application/x-tar','rar'  => 'application/vnd.rar','7z'   => 'application/x-7z-compressed','exe'  => 'application/vnd.microsoft.portable-executable','dll'  => 'application/x-msdownload','ttf'  => 'font/ttf','otf'  => 'font/otf','woff' => 'font/woff');

if( $_POST['action'] == "load_storage_vaults" ){

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults", [
		'app_id'=>$config_param1,
	],[
		'projection'=>[
			'des'=>true,'vault_type'=>true,
		],
		'sort'=>['des'=>1],
		'limit'=>200,
	]);
	json_response($res);

	exit;
}
if( $_POST['action'] == "get_files" ){
	$t = validate_token("getfiles.". $config_param1, $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'app_id'=>$config_param1,
		"path"=>$_POST['current_path'],
	],[
		'projection'=>[
			'body'=>false,'data'=>false,
		],
		'sort'=>['name'=>1],
		'limit'=>200,
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == "delete_file" ){
	$t = validate_token("deletefile". $config_param1 . $_POST['file_id'], $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['file_id']) ){
		json_response("fail", "ID incorrect");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'_id'=>$_POST['file_id']
	]);
	if( $res['data'] ){
		if( $res['data']['vt'] == "folder" ){
			$res2 = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
				'path'=>$res['data']['path'] . $res['data']['name'] . '/'
			]);
			if( $res2['data'] ){
				json_response("fail", "Folder is not empty");
			}
		}
	}
	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'_id'=>$_POST['file_id']
	]);
	update_app_pages( $config_param1 );
	json_response($res);
}

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

function save_file_upload_data($data = []) {
	global $mongodb_con,$config_global_apimaker;

	if(!isset($data['app_id']) || $data['app_id'] == "") {
		json_response("fail","App ID should not be empty");
	}else {
		if( !preg_match("/^[a-f0-9]{24}$/i", $data['app_id']) ) {
			json_response("fail","Invalid App ID");
		}
	}

	if($data['name'] == "" || $data['vt'] == "" || $data['path'] == "" || $data['t'] == "" ) {
		json_response("fail","Invalid data");
	}

	$save_data = [];
	$save_data['app_id'] = $data['app_id'];
	$save_data['name'] = $data['name'];
	$save_data['type'] = $data['type'];
	$save_data['vt'] = $data["vt"];
	$save_data['path'] = $data['path'];
	$save_data['t'] = $data['t'];
	$save_data['ext'] = $data['extension'];
	$save_data['data'] = $data['code'];
	$save_data['sz'] = strlen($data['code']);
	$save_data['vars_used'] = [];
	$save_data["updated"] = date("Y-m-d H:i:s");

	$cond = [];
	$cond['name'] = $data['name'];
	$cond['app_id'] = $data['app_id'];
	$cond['path'] = $data['path'];
	$cond['vt'] = $data['vt'];

	$duplicate_check = $mongodb_con->find_one($config_global_apimaker['config_mongo_prefix'] . "_files", $cond);

	if($duplicate_check['data']['_id'] != "") {
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_files", ['_id' => $duplicate_check['data']['_id']] , $save_data );
		if($res['status'] == "fail") {
			json_response("fail","Fail to Update data " . json_encode($save_data));
		}
	}else {
		$save_data["created"] = date("Y-m-d H:i:s");
		
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", $save_data );
		if($res['status'] == "fail") {
			$data = [];
			$data['error'] = "Fail to Insert data";
			$data['db_error'] = $res;
			json_response("fail",$data);
		}
		update_app_pages( $data['app_id'] );
	}

	return $res;
}

function replaceDomainCallback($matches) {
	global $mongodb_con,$config_param1;

	$url = "";
	if (parse_url($matches[2], PHP_URL_HOST)) {
		$url = $matches[2];
	} else {
		$url = $_POST['crawl_link'] . $matches[2];
	}

	if($_POST['crawl_dtls']['download'] == "yes") {
		$newcontent = "";
		$apidata = [];
		$apidata['url'] = $url;
		$apidata['method'] = "GET";
		$apidata['headers'] = ["User-Agent: ".$_SERVER['HTTP_USER_AGENT']];
		$curl_response = execute_curl_request($apidata,"");

		if( $curl_response['status'] == "ok" && $curl_response['http_code'] == 200 ) {
			$newcontent = $curl_response['response'];
		}

		if($newcontent != "") {
			$newcode = $newcontent;
			$newcode = preg_replace('/\s{2,}/', ' ', $newcode);
			$newcode = trim($newcode);

			$newcode = preg_replace_callback('/(href|src)="([^"]*)"/i','replaceDomainCallback',$newcode);

			$file_name = "";$type = "";$t = "";$extension = "";
			$path = parse_url($url, PHP_URL_PATH);
			$path_info = pathinfo($path);
			$file_name = ($path_info['basename']?$path_info['basename']:"index");
			$type = (isset($mime_types[$path_info['extension']])?$mime_types[$path_info['extension']]:"text/html");
			$extension = ($path_info['extension']?$path_info['extension']:"html");
			$t = isset($media_file_types[$extension])?"base64":"inline";

			if($path_info['dirname'] != "/") {
				$storing_path = $_POST['current_path'].$path_info['dirname'];
			}else {
				$storing_path = $_POST['current_path'];
			}

			$folder_path = "";
			$folder_path = $_POST['current_path'];
			foreach(explode(" ",$path_info['dirname']) as $i => $j) {
				$j = str_replace("/","",$j);
				$folder_path.= "/".$j;
				$folder_path = str_replace("//","/",$folder_path);
				$save_data = [];
				$save_data['name'] = $j;
				$save_data['type'] = "";
				$save_data['vt'] = "folder";
				$save_data['path'] = $_POST['current_path'];
				$save_data['t'] = "inline";
				$save_data['extension'] = "";
				$save_data['code'] = "";
				$save_data['app_id'] = $config_param1;

				save_file_upload_data($save_data);
			}
			$storing_path = str_replace("///","/",$storing_path);
			$storing_path = str_replace("//","/",$storing_path);

			$save_data = [];
			$save_data['name'] = $file_name.".".$extension;
			$save_data['type'] = $type;
			$save_data['vt'] = "file";
			$save_data['path'] = $storing_path."/";
			$save_data['t'] = $t;
			$save_data['extension'] = $extension;
			$save_data['code'] = $newcode;
			$save_data['app_id'] = $config_param1;

			save_file_upload_data($save_data);
		}

		return preg_replace('#https?://[^/]+/#', "", $matches[1]."="."'".$url."'");
	}else {
		return $matches[1]."="."'".$url."'";
	}
}

if( $_POST['action'] == "crawl_website" ){
	if( $_POST['crawl_link'] == "" ) {
		json_response("fail", "Link is required");
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
	$code = $content;
	$code = preg_replace('/\s{2,}/', ' ', $code);
	$code = trim($code);

	$code = preg_replace_callback('/(href|src)="([^"]*)"/i','replaceDomainCallback',$code);

	$file_name = "";$type = "";$t = "";$extension = "";
	$path = parse_url($_POST['crawl_link'], PHP_URL_PATH);
	$path_info = pathinfo($path);
	$file_name = ($path_info['basename']?$path_info['basename']:"index");
	$type = (isset($mime_types[$path_info['extension']])?$mime_types[$path_info['extension']]:"text/html");
	$extension = ($path_info['extension']?$path_info['extension']:"html");
	$t = isset($media_file_types[$extension])?"base64":"inline";
	$storing_path = $_POST['current_path'];

	$save_data = [];
	$save_data['name'] = $file_name.".".$extension;
	$save_data['type'] = $type;
	$save_data['vt'] = "file";
	$save_data['path'] = $storing_path."/";
	$save_data['t'] = $t;
	$save_data['extension'] = $extension;
	$save_data['code'] = $code;
	$save_data['app_id'] = $config_param1;

	save_file_upload_data($save_data);

	json_response("success","Still in process");	
}

if( $_POST['action'] == "create_file" ){
	if( !preg_match("/^[a-z0-9\.\-\_\/]{3,100}\.[a-z]{2,4}$/i", $_POST['new_file']['name']) ){
		json_response("fail", "Name incorrect");
	}
	if( !preg_match("/^[a-z\/]{5,50}$/i", $_POST['new_file']['type']) ){
		json_response("fail", "Type incorrect");
	}
	preg_match("/\.([a-z]{2,4})$/i",$_POST['new_file']['name'], $m );
	if( !$m ){
		json_response("fail", "Extension is required");
	}
	$ext = strtolower($m[1]);
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		"app_id"=>$config_param1,
		'name'=>$_POST['new_file']['name'],
		"path"=>$_POST['current_path'],
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}

	$type = $_POST['new_file']['type'];

	$data = 'var a = 10; b = a + 10;';
	if( $type == "text/html" ){
		$data = "<h1>Heading</h1><p>it is a paragraph of text. </p><ul><li>One</li><li>Two</li></ul>";
	}else if( $type == "text/css" ){
		$data = ".special{ color:red; }";
	}else if( $type == "text/javascript" ){
		$data = `function foo(items) {
    var x = "All this is syntax highlighted";
    return x;
}`;
	}

	$version_id = $mongodb_con->generate_id();
	$path = $_POST['current_path'];
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		"app_id"=>$config_param1,
		"name"=>$_POST['new_file']['name'],
		'type'=>$type,
		'vt'=>"file", //file,folder
		"path"=>$path,
		't'=>'inline', //inline/s3/disc/base64
		'ext'=>$ext,
		'data'=>$data,
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
		"sz"=>100
	]);
	update_app_pages( $config_param1 );
	json_response($res);
	exit;
}


if( $_POST['action'] == "files_create_folder" ){
	if( !preg_match("/^[a-z0-9\.\-\_\/]{2,100}$/i", $_POST['new_folder']) ){
		json_response("fail", "Name incorrect. Min 2 chars Max 100. No special chars");
	}
	$path = $_POST['current_path'];
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		"app_id"=>$config_param1,
		'vt'=>"folder",
		'name'=>$_POST['new_folder'],
		"path"=>$path,
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		"app_id"=>$config_param1,
		"name"=>$_POST['new_folder'],
		'type'=>$type,
		'vt'=>"folder", //file,folder
		'path'=>$path,
		't'=>'inline', //inline/s3/disc/base64
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
	]);
	update_app_pages( $config_param1 );
	json_response($res);
	exit;
}


function getFileExtension($filename) {
    $basename = basename($filename);
    $pos = strrpos($basename, '.');
    if ($pos !== false) {
        return substr($basename, $pos + 1);
   } else {
        return 'no_extenstion';
   }
}

if( $_POST['action'] == "upload_zip_files" ) {
	$t = validate_token( "upload_zip_files.".$config_param1, $_POST['token'] );
	if( $t != "OK" ){
		json_response("fail", $t);
	}

	if($_POST['file'] == "") {
		json_response("fail","Invalid File list");
	}else{
		if(count($_POST['file']) == 0) {
			json_response("fail","Please select file list to upload");
		}
	}

	$path = '/tmp/';
	$file_path = $_POST['current_path'];

	$temp_zip_path = $_SESSION['temp_zip_path'];
	if (!file_exists($temp_zip_path)) {
		json_response("fail","Zip file not found");
	}

	$selected_files = $_POST['file'];
	$zip = new ZipArchive;
	if ($zip->open($temp_zip_path) === true) {
		foreach ($selected_files as $file) {
			$zip->extractTo($path, $file);
			$ext = getFileExtension($file);
			$content = "";
			$t = "";
			$mime_type ="";
			if(isset($media_file_types[$ext])){
				$file_content = file_get_contents($path.$file);
				$content = base64_encode($file_content);
				$t = "base64";
				$mime_type = $media_file_types[$ext];
			}else{
				$file_content = file_get_contents($path.$file);
				$content = $file_content;
				$t = "inline";
				$mime_type = $mime_types[$ext];
			}

			unlink($path.$file);

			$path_file = str_replace(basename($file),"",$file);

			$check_path = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", ['path' => $file_path.$path_file],['projection' => ['path' => 1]]);

			if($path_file != "/" && $path_file != "" && $check_path['data']['_id'] == "") {
				$exploded_path = explode("/",$path_file);

				$check_paath = "";
				$check_paath = $file_path;
				foreach($exploded_path as $kk => $kkk) {
					if($check_paath == $kkk) {
						$check_paath = "/";
					}else {
						if($kk == 0) {
							$check_paath = $check_paath;
						}else {
							$check_paath.= $exploded_path[$kk -1];
						}
					}
					$check_path = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", ['path' => $check_paath],['projection' => ['path' => 1]]);
					
					if( $check_path['data']['_id'] == "" && $kkk != "") {
						$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", [
							"app_id"=>$config_param1,
							"name"=>$kkk,
							'type'=>$type,
							'vt'=>"folder",
							'path'=>$check_paath,
							't'=>'inline',
							"created"=>date("Y-m-d H:i:s"),
							"updated"=>date("Y-m-d H:i:s"),
						]);
					}
				}
			}

			/*print_pre($mime_types);exit;*/

			$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", [
				"app_id" => $config_param1,
				"name" => basename($file),
				'type' => $mime_type,
				'vt' => "file",
				"path" => $file_path.$path_file,
				't' => $t,
				"data" => $content,
				"sz" => strlen($content),
				'ext' => $ext,
				"created" => date("Y-m-d H:i:s"),
				"updated" => date("Y-m-d H:i:s"),
			]);

			if( $res['status'] == "success" ){
				update_app_pages( $config_param1 );
			}
		}
		$zip->close();
		unlink($temp_zip_path);
		json_response("success",$res);
	} else {
		json_response("fail",'Failed to open the zip file.');
	}
}

if( $_POST['action'] == "apps_file_upload" ){
	$t = validate_token( "file.upload.".$config_param1, $_POST['token'] );
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( file_exists( $_FILES['file']['tmp_name'] ) && filesize($_FILES['file']['tmp_name']) > 0  ){
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			'app_id'=>$config_param1,
			'name'=>$_POST['name']
		]);
		
		$fb = file_get_contents($_FILES['file']['tmp_name']);
		//echo $_FILES['file']['name'];exit;
		//print_r( explode(".",$_FILES['file']['name']) );
		$ext = array_pop( explode(".",$_FILES['file']['name']) );
		//echo $ext;exit;
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			"app_id"=>$config_param1,
			"name"=>$_POST['name'],
			'type'=>$_FILES['file']['type'],
			'vt'=>"file",
			"path"=>$_POST['path'],
			't'=>"base64",
			"data"=>base64_encode($fb),
			"sz"=>strlen($fb),
			'ext'=>$ext,
			"created"=>date("Y-m-d H:i:s"),
			"updated"=>date("Y-m-d H:i:s"),
		]);
		if( $res['status'] == "success" ){
			$res['data'] = [
				"_id"=>$res['inserted_id'],
				"app_id"=>$config_param1,
				"name"=>$_POST['name'],
				'type'=>$_FILES['file']['type'],
				'vt'=>"file",
				"path"=>$_POST['path'],
				't'=>"base64",
				"sz"=>strlen($fb),
				'ext'=>$ext,
				"created"=>date("Y-m-d H:i:s"),
				"updated"=>date("Y-m-d H:i:s"),
			];
			update_app_pages( $config_param1 );
		}
		json_response($res);
	}else{
		json_response(['status'=>"fail", "error"=>"server error"]);
	}
	exit;
}

if( $_POST['action'] == "zip_file_upload" ){
	$t = validate_token( "file.upload.".$config_param1, $_POST['token'] );
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( file_exists( $_FILES['file']['tmp_name'] ) && filesize($_FILES['file']['tmp_name']) > 0  ){
		if($_FILES['file']['name'] != ''){
			$temp_path = $_FILES['file']['tmp_name'];
			$zip = new ZipArchive;
			if ($zip->open($temp_path) === true) {
				$file_names = [];
				for ($i = 0; $i < $zip->numFiles; $i++) {
					$fileInfo = $zip->statIndex($i);
					if (substr($fileInfo['name'], -1) != '/') {
						$file_names[] = $fileInfo['name'];
					}
				}
				$zip->close();
				$output_data = "";
				foreach ($file_names as $file) {
					$output_data.='<label><input type="checkbox" class="me-2" name="selected_files[]" value="' . $file . '" checked="true">' . $file . '</label><br>';
				}
				$temp_dir = sys_get_temp_dir();
				$temp_zip_path = $temp_dir . '/' . $_FILES['file']['name'];
				move_uploaded_file($temp_path, $temp_zip_path);
				$_SESSION['temp_zip_path'] = $temp_zip_path;
				json_response(['status'=>"success", "data"=>$output_data]);
			} else {
				json_response(['status'=>"fail", "error"=>"Invalid zip file."]);
			}
		}else {
			json_response(['status'=>"fail", "error"=>"Invalid zip file."]);
		}
	}else{
		json_response(['status'=>"fail", "error"=>"server error"]);
	}
	exit;
}

if( $_POST['action'] == "mount_storage_vault" ){

	if( !isset($_POST['new_mount']) ){
		json_response("fail", "input missing");
	}
	if( !isset($_POST['new_mount']['vault_id']) || !isset($_POST['new_mount']['vault_name']) || !isset($_POST['new_mount']['vault_path']) || !isset($_POST['new_mount']['local_path']) ){
		json_response("fail", "input missing");
	}
	if( !preg_match("/^[0-9a-f]{24}$/", $_POST['new_mount']['vault_id']) ){
		json_response("fail", "Incorrect vault id");
	}
	$vault_res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults", [
		"_id"=>$_POST['new_mount']['vault_id']
	]);
	if( !$vault_res['data'] ){
		json_response( $vault_res );
	}
	$_POST['new_mount']['vault_name'] = $vault_res['data']['des'];
	$_POST['new_mount']['vault_type'] = $vault_res['data']['vault_type'];

	if( $_POST['new_mount']['vault_path'] != "/" && !preg_match("/^\/[a-z0-9\-\_\.\/]+\/$/i", $_POST['new_mount']['vault_path']) ){
		json_response("fail", "Vault path should be / or /path/  or /path/path/");
	}
	if( !preg_match("/^\/[a-z0-9\-\_\.\/]+\/$/i", $_POST['new_mount']['local_path']) ){
		json_response("fail", "Local path should be /path/  or /path/path/");
	}
	if( preg_match("/[\/]{2,5}/i", $_POST['new_mount']['local_path']) ){
		json_response("fail", "Local path should be /path/  or /path/path/");
	}
	if( preg_match("/[\/]{2,5}/i", $_POST['new_mount']['vault_path']) ){
		json_response("fail", "Vault path should be /path/  or /path/path/");
	}

	$x = explode("/",$_POST['new_mount']['local_path']);
	array_pop( $x );
	if( sizeof( $x ) > 1 ){
		$name = array_pop( $x );
		$path = implode("/", $x);
		if( $path == "" ){$path = "/";}
	}else{
		$name = array_pop( $x );
		$path = "/";
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'app_id'=>$config_param1,
		'path'=>$path, "name"=>$name
	]);
	$res['q'] = [
		'app_id'=>$config_param1,
		'path'=>$path, "name"=>$name
	];
	if( $res['data'] ){
		json_response("fail", $path . $name . "/ already exists with same name");
	}
	$res = $mongodb_con->insert(  $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'app_id'=>$config_param1,
		'path'=>$path, 
		"name"=>$name,
		"vt"=>"folder", 
		"type"=>"mounted",
		"vault"=>$_POST['new_mount'],
	]);
	json_response($res);

	exit;
}

if( $config_param3 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param3) ){
		echo404("Incorrect File ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'app_id'=>$app['_id'],
		'_id'=>$config_param3
	]);
	if( !$res['data'] ){
		echo404("File not found!");
	}
	$file = $res['data'];

	if( $_POST['action'] == "file_load_content" ){
		if( $file['t'] != "base64" ){
			json_response(['status'=>"fail", "error"=>"Incorrect file type"]);exit;
		}
		json_response([
			'status'=>'success',
			'data'=>$file['data']
		]);
		exit;
	}

	if( $file['t'] != "inline" ){
		unset($file['data']);
	}
	//print_r( $file );exit;
	//unset($file['data']);

	$mode = "htmlmixed";
	if( $file['type'] == "text/html" ){
		$mode = "htmlmixed";
	}else if( $file['type'] == "text/css" ){
		$mode = "css";
	}else if( $file['type'] == "text/javascript" ){
		$mode = "javascript";
	}

	if( $_POST['action'] == "file_save_content" ){
		if( $_POST['file_id'] != $config_param3 ||  $_POST['app_id'] != $config_param1 ){
			json_response("fail","Incorrect URL");
		}
		$t = validate_token("file.save.".$config_param1.".".$config_param3, $_POST['token']);
		if( $t != "OK" ){
			json_response("fail", $t);
		}

		$vars_used = [];
		preg_match_all("/[\-]{2}\w[a-z\-]+\w[\-]{2}/", $_POST['data'], $m);
		// print_r( $m );
		// exit;
		foreach( $m[0] as $i=>$j ){
			$vars_used[ $j ] = 2;
		}

		//print_r( $vars_used );exit;

		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			"app_id"=> $config_param1,
			"_id"=>$config_param3
		],[
			"data"=>$_POST['data'],
			"sz"=>strlen($_POST['data']),
			"vars_used"=>array_keys($vars_used),
			"updated"=>date("Y-m-d H:i:s"),
		]);
		if( $res["status"] == "fail" ){
			json_response("fail",$res["error"]);
		}
		update_app_pages( $config_param1 );
		json_response("success","ok");
	}

	if( $_POST['action'] == "file_update_settings" ){
		$t = validate_token("file.setting.save.".$config_param1.".".$config_param3, $_POST['token']);
		if( $t != "OK" ){
			json_response("fail", $t);
		}
		if( !preg_match("/^[a-z0-9\.\-\_\/]{3,100}\.[a-z]{2,4}$/i", $_POST['edit_file']['name']) ){
			json_response("fail", "Name incorrect");
		}
		if( !preg_match("/^[a-z\/]{5,50}$/i", $_POST['edit_file']['type']) ){
			json_response("fail", "Type incorrect");
		}
		preg_match("/\.([a-z]{2,4})$/i",$_POST['edit_file']['name'], $m );
		if( !$m ){
			json_response("fail", "Extension is required");
		}
		$ext = strtolower($m[1]);
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			"app_id"=>$config_param1,
			'name'=>$_POST['edit_file']['name'],
			'_id'=>['$ne'=>$_POST['file_id']]
		]);
		if( $res['data'] ){
			json_response("fail", "A file already exists with same name");
		}

		$type = $_POST['edit_file']['type'];

		$version_id = $mongodb_con->generate_id();
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			'_id'=>$_POST['file_id']
		],[
			"name"=>$_POST['edit_file']['name'],
			'type'=>$type,
			'ext'=>$ext,
			"updated"=>date("Y-m-d H:i:s"),
		]);
		update_app_pages( $config_param1 );
		json_response($res);
		exit;
	}

	

}