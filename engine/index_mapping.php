<?php

function index_mapping($storage_vault, $path){

	global $mongo_db;
	global $app_id;
	global $db_prefix;
	global $config_global_apimaker_engine;

	//echo "xxx";exit;

	//return [200,"application/json",[],json_encode($storage_vault,JSON_PRETTY_PRINT) ];

	if( $storage_vault['vault_type'] == "AWS-S3" ){

		$s3_key = pass_decrypt($storage_vault['details']['key']);
		$s3_secret = pass_decrypt($storage_vault['details']['secret']);

		$s3_bucket = $storage_vault['details']['bucket'];
		$s3_region = $storage_vault['details']['region'];
		$mapped_path = $storage_vault['details']['rewrite_path'];
		$dest_path = $storage_vault['details']['dest_path'];
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

		//return [200,"text/plain",[], $mapped_path . ":" . $dest_path  . ":" . $path ];

		$dest_key = str_replace($mapped_path, "", "/".$path);
		$dest_key = ltrim($dest_key, "/");

		//return [200, "text/plain", [], $dest_key ];

		try{
			$res = $s3con->getObject([
				"Bucket"=>$s3_bucket,
				"Key"=>$dest_key
			])->toArray();
			// return [200, "application/json", [
			// 	//"Content-Disposition"=>"attachment;filename=\"".str_replace("/", "-", $dest_key )."\""
			// ], json_encode($res,JSON_PRETTY_PRINT) ];

			$ctype = $res['ContentType']?$res['ContentType']:"application/application";
			return [200, $ctype, [
				//"Content-Disposition"=>"attachment;filename=\"".str_replace("/", "-", $dest_key )."\""
			], $res['Body']];
		}catch(Aws\S3\Exception\S3Exception $ex){
			return [500,"application/json", [], json_encode([
				"status"=>"fail", 
				"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode(),
				"details"=>$s3_bucket . ":" . $dest_key,
			])];
		}
		exit;

	}else{
		return [404,"application/json",[],json_encode(["status"=>"fail","error"=>"vault not configured"]) ];
	}

}