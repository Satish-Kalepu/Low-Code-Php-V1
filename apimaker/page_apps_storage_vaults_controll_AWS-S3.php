<?php

$s3_key = pass_decrypt($vault['details']['key']);
$s3_secret = pass_decrypt($vault['details']['secret']);

$s3_bucket = $vault['details']['bucket'];
$s3_region = $vault['details']['region'];
//require("../vendor/autoload.php");
//Aws\S3\Exception\S3Exception

$s3con = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => $s3_region,
    'credentials' => array(
		'key'    => $s3_key,
		'secret' => $s3_secret,
    )
]);

if( $_GET['action'] == "download" ){
	try{
		$res = $s3con->getObject([
			"Bucket"=>$s3_bucket,"Key"=>$_GET['key']
		])->toArray();
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment;filename=\"".$_GET['key']."\"");
		echo $res['Body'];
		//print_r( $res );

	}catch(Aws\S3\Exception\S3Exception $ex){
		http_response_code(500);
		json_response([
			"status"=>"fail", 
			"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
		]);
	}
	exit;
}

if( $_POST['action'] == "storage_vault_load_keys" ){

	$prefix = "";
	if( isset($_POST['current_path']) && $_POST['current_path'] != "/" && $_POST['current_path'] != ""  ){
		$prefix = substr($_POST['current_path'],1,500);
	}
	if( isset($_POST['keyword']) && $_POST['keyword'] != "" ){
		$prefix = substr($_POST['keyword'],1,500);
	}

	try{
		$p  = [
			"Bucket"=>$s3_bucket,
			//"OptionalObjectAttributes"=>["Content-Type"],
			"Delimiter"=>"/",
		];
		if( $prefix ){
			$p["Prefix"]=$prefix;
		}
		//print_r( $p );
		$res = $s3con->listObjectsV2($p)->toArray();
	}catch( Aws\S3\Exception\S3Exception $ex ){
		json_response([
			"status"=>"fail", 
			"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
		]);
	}

	//print_r( $res );exit;
	if( $res['KeyCount'] ){
		$keys = $res['Contents'];
		for($i=0;$i<sizeof($keys);$i++){
			if( $prefix ){
				//if( $keys[$i]['Key'] == $prefix ){array_splice($keys,$i,1);}
			}
			unset($keys[$i]['ETag']);
		}
	}else{
		$keys = [];
	}

	$prefixes = [];
	if( isset($res['CommonPrefixes']) ){
		$prefixes = $res['CommonPrefixes'];
		foreach( $prefixes as $i=>$j ){ if( $i>20 ){ break; }
			$prefix = $j['Prefix'];
			try{
				$p  = [
					"Bucket"=>$s3_bucket,
					"Prefix"=>$prefix,"StartAfter"=>$prefix,
					//"Delimiter"=>"/"
				];
				$res = $s3con->listObjectsV2($p)->toArray();
				// echo $prefix . "\n";
				// print_r( $res );
				$prefixes[ $i ]['count'] = (isset($res['KeyCount'])?$res['KeyCount']:0)+(isset($res['CommonPrefixes'])?sizeof($res['CommonPrefixes']):0);
			}catch( Aws\S3\Exception\S3Exception $ex ){
				json_response([
					"status"=>"fail", 
					"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
				]);
			}
		}
	}



	json_response([
		"status"=>"success", 
		"keys"=>$keys,
		"prefixes"=>$prefixes,
	]);

	exit;
}


if( $_POST['action'] == "delete_file" ){
	$t = validate_token("deletefile". $config_param1 . $_POST['Key'], $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}

	$key = $_POST['Key'];
	if( preg_match("/\/$/",$key) ){
		try{
			$p  = [
				"Bucket"=>$s3_bucket,
				"Limit"=>10,
				"Prefix"=>$key
			];
			$res = $s3con->listObjectsV2($p)->toArray();
			if( $res['KeyCount'] ){
				json_response(["status"=>"fail", "error"=>"Folder is not empty"]);
			}
		}catch( Aws\S3\Exception\S3Exception $ex ){
			json_response([
				"status"=>"fail", 
				"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
			]);
		}
	}

	try{
		$res= $s3con->deleteObject([
			"Bucket"=>$s3_bucket,"Key"=>$key
		])->toArray();
	}catch( Aws\S3\Exception\S3Exception $ex ){
		json_response([
			"status"=>"fail", 
			"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
		]);
	}

	event_log( "storage_vault", "aws_s3_delete_file", [
		"app_id"=>$config_param1,
		"vault_id"=>$config_param3,
		"Bucket"=>$s3_bucket,"Key"=>$key
	]);

	json_response(["status"=>"success"]);
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
	event_log( "storage_vault", "aws_s3_create_file", [
		"app_id"=>$config_param1,
		"vault_id"=>$config_param3,
		"Bucket"=>$s3_bucket,"Key"=>$_POST['new_file']['name'],
		"file_id"=>$res['inserted_id']
	]);
	json_response($res);
	exit;
}


if( $_POST['action'] == "files_create_folder" ){
	if( !preg_match("/^[a-z0-9\.\-\_\/]{2,100}$/i", $_POST['new_folder']) ){
		json_response("fail", "Name incorrect. Min 2 chars Max 100. No special chars");
	}
	$path = $_POST['current_path'];
	$prefix = ltrim($path, "/");
	$fn =  $prefix.$_POST['new_folder']."/";
	try{
		$res =$s3con->putObject([
			"Bucket"=>$s3_bucket,
			"Key"=>$fn,
			"Body"=>"",
		]);
	}catch( Aws\S3\Exception\S3Exception $ex ){
		json_response([
			"status"=>"fail", 
			"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
		]);
	}

	event_log( "storage_vault", "aws_s3_create_folder", [
		"app_id"=>$config_param1,
		"vault_id"=>$config_param3,
		"Bucket"=>$s3_bucket,"Key"=>$fn,
	]);

	json_response(['status'=>"success", "data"=>["Key"=>$fn, "Date"=>date("Y-m-d H:i:s"), "Size"=>0 ]] );
	exit;
}

if( $_POST['action'] == "apps_file_upload" ){

	$is_public = false;
	if( $vault['details']['public'] ){
		$is_public = true;
	}

	$t = validate_token( "file.upload.".$config_param1, $_POST['token'] );
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( file_exists( $_FILES['file']['tmp_name'] ) && filesize($_FILES['file']['tmp_name']) > 0  ){
		$sz = filesize($_FILES['file']['tmp_name']);
		$prefix = ltrim($_POST['path'], "/");
		$fn = $prefix.$_FILES['file']['name'];
		try{
			$res =$s3con->putObject([
				"Bucket"=>$s3_bucket,
				"Key"=>$fn,
				"SourceFile"=>$_FILES['file']['tmp_name'],
				"ContentType"=>$_POST['type'],
				"ACL"=>$is_public?"public-read":"private"
			]);
		}catch( Aws\S3\Exception\S3Exception $ex ){
			json_response([
				"status"=>"fail", 
				"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
			]);
		}
		event_log( "storage_vault", "aws_s3_file_upload", [
			"app_id"=>$config_param1,
			"vault_id"=>$config_param3,
			"Bucket"=>$s3_bucket,"Key"=>$fn,
		]);
		json_response(['status'=>"success", "data"=>["Key"=>$fn, "Date"=>date("Y-m-d H:i:s"), "Size"=>$sz ]] );
	}else{
		json_response(['status'=>"fail", "error"=>"server error"]);
	}
	exit;
}