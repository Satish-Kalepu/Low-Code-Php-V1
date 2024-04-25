<?php

//echo $storage_vault['vault_type'];
if( $storage_vault['vault_type'] == "AWS-S3" ){

	$s3_key = pass_decrypt($storage_vault['details']['key']);
	$s3_secret = pass_decrypt($storage_vault['details']['secret']);

	$s3_bucket = $storage_vault['details']['bucket'];
	$s3_region = $storage_vault['details']['region'];
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

//	print_r( $_POST['action'] );

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

	if( $_POST['action'] == "get_file" ){
		if( !isset($_POST['filename']) ){
			json_response(["status"=>"fail","error"=>"Filename missing"]);
		}
		if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+$/i", $_POST['filename']) || preg_match("/\/\//i", $_POST['filename']) || preg_match("/\/$/i", $_POST['filename']) ){
			json_response(["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
		}
		try{
			$res = $s3con->getObject([
				"Bucket"=>$s3_bucket,
				"Key"=>ltrim($_POST['filename'],"/")
			])->toArray();
			json_response(['status'=>"success", "data"=>base64_encode($res['Body'])]);
		}catch(Aws\S3\Exception\S3Exception $ex){
			http_response_code(500);
			json_response([
				"status"=>"fail", 
				"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
			]);
		}
		exit;
	}
	if( $_POST['action'] == "get_raw_file" ){
		if( !isset($_POST['filename']) ){
			json_response(["status"=>"fail","error"=>"Filename missing"]);
		}
		if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+$/i", $_POST['filename']) || preg_match("/\/\//i", $_POST['filename']) || preg_match("/\/$/i", $_POST['filename']) ){
			json_response(["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
		}
		try{
			$f = $_POST['filename'];
			$x = explode("/",ltrim($_POST['filename'], "/"));
			$f2 = $x[ sizeof($x)-1 ];
			$res = $s3con->getObject([
				"Bucket"=>$s3_bucket,
				"Key"=>ltrim($f,"/")
			])->toArray();
			//json_response(['status'=>"success", "data"=>base64_encode($res['Body'])]);
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment;filename=\"".$f2."\"");
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

	if( $_POST['action'] == "list_files" ){

		if( isset($_POST['path']) ){
			if( $_POST['path'] != "/" && !preg_match("/^\/(.*?)\/$/i",$_POST['path']) ){
				json_response(["status"=>"fail","error"=>"Path incorrect format"]);
			}
		}

		$prefix = "";
		try{
			$p  = [
				"Bucket"=>$s3_bucket,
				"Delimiter"=>"/",
				//"OptionalObjectAttributes"=>["Content-Type"],
			];
			if( isset($_POST['path']) && $_POST['path'] != "/" && $_POST['path'] != ""  ){
				$prefix = substr($_POST['path'],1,500);
				$p["Prefix"]=$prefix;
			}
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
				$keys[$i]['Key'] = "/" . $keys[$i]['Key'];
				if( $prefix ){
					if( $keys[$i]['Key'] == $prefix ){array_splice($keys,$i,1);}
				}
				unset($keys[$i]['ETag']);
			}
		}else{
			$keys = [];
		}

		$prefixes = [];
		if( isset($res['CommonPrefixes']) ){
			$prefixes = $res['CommonPrefixes'];
			foreach( $prefixes as $i=>$j ){
				if( $i<20 ){
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
				$prefixes[ $i ]["Prefix"] = "/".$prefixes[ $i ]["Prefix"];
			}
		}

		json_response([
			"status"=>"success", 
			"keys"=>$keys,
			"prefixes"=>$prefixes,
		]);

		exit;
	}
	if( $_POST['action'] == "list_files_mounted_path" ){

		if( isset($_POST['path']) ){
			if( $_POST['path'] != "/" && !preg_match("/^\/(.*?)\/$/i",$_POST['path']) ){
				json_response(["status"=>"fail","error"=>"Path incorrect format"]);
			}
		}

		$prefix = "";
		try{
			$p  = [
				"Bucket"=>$s3_bucket,
				"Delimiter"=>"/",
				//"OptionalObjectAttributes"=>["Content-Type"],
			];
			if( isset($_POST['path']) && $_POST['path'] != "/" && $_POST['path'] != ""  ){
				$prefix = substr($_POST['path'],1,500);
				$p["Prefix"]=$prefix;
			}
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
				$keys[$i]['Key'] = "/" . $keys[$i]['Key'];
				if( $prefix ){
					if( $keys[$i]['Key'] == $prefix ){array_splice($keys,$i,1);}
				}
				unset($keys[$i]['ETag']);
			}
		}else{
			$keys = [];
		}

		$prefixes = [];
		if( isset($res['CommonPrefixes']) ){
			$prefixes = $res['CommonPrefixes'];
			foreach( $prefixes as $i=>$j ){
				if( $i<20 ){
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
				$prefixes[ $i ]["Prefix"] = "/".$prefixes[ $i ]["Prefix"];
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
		if( !isset($_POST['filename']) ){
			json_response(["status"=>"fail","error"=>"Filename missing"]);
		}
		if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+$/i", $_POST['filename']) || preg_match("/\/\//i", $_POST['filename']) || preg_match("/\/$/i", $_POST['filename']) ){
			json_response(["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
		}
		try{
			$res= $s3con->deleteObject([
				"Bucket"=>$s3_bucket,"Key"=>ltrim($_POST['filename'],"/")
			])->toArray();
		}catch( Aws\S3\Exception\S3Exception $ex ){
			json_response([
				"status"=>"fail", 
				"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
			]);
		}
		json_response(["status"=>"success"]);
	}

	
	if( $_POST['action'] == "files_create_folder" ){
		if( !preg_match("/^[a-z0-9\.\-\_\/]{2,100}$/i", $_POST['new_folder']) ){
			json_response("fail", "Name incorrect. Min 2 chars Max 100. No special chars");
		}
		$path = $_POST['path'];
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
		json_response(['status'=>"success", "data"=>["Key"=>$fn, "Date"=>date("Y-m-d H:i:s"), "Size"=>0 ]] );
		exit;
	}

	if( $_POST['action'] == "put_file" ){

		$block_extensions = ["fap","apk","jar","ahk","cmd","ipa","run","xbe","0xe","rbf","vlx","workflow","u3p","8ck","bat","bms","exe","bin","elf","air","appimage","xap","gadget","app","mpk","widget","x86","shortcut","fba","mcr","pif","ac","com","xlm","tpk","sh","x86_64","73k","script","scpt","command","out","rxe","scb","ba_","ps1","paf.exe","scar","isu","scr","xex","fas","coffee","ex_","action","tcp","acc","celx","shb","ex5","rfu","ebs2","hta","cgi","xbap","nexe","ecf","fxp","sk","vpm","plsc","rpj","ws","azw2","js","mlx","dld","cof","vxp","caction","vbs","wsh","mcr","iim","ex_","phar","89k","cheat","esh","fpi","wcm","pex","server","gpe","a7r","dek","pyc","exe1","jsf","jsx","acr","ex4","pwc","ear","icd","epk","vexe","rox","mel","zl9","plx","mm","snap","paf","mcr","ms","tiapp","uvm","gm9","atmx","89z","vbscript","actc","pyo","applescript","frs","hms","otm","rgs","n","widget","csh","mrc","wiz","prg","ebs","tms","spr","cyw","sct","e_e","ebm","gs","mrp","osx","fky","xqt","fas","ygh","prg","app","mxe","actm","udf","kix","seed","cel","app","ezs","thm","beam","lo","vbe","kx","jse","prg","rfs","s2a","dmc","hpf","wpk","exz","scptd","ls","ms","msl","mhm","tipa","xys","prc","wpm","sca","ita","eham","wsf","qit","es","arscript","rbx","mem","sapk","ebacmd","upx","ipk","mam","ncl","ksh","dxl","ham","btm","mio","ipf","pvd","vdo","gpu","exopc","ds","mac","sbs","cfs","sts","asb","qpx","p","rpg","mlappinstall","srec","uw8","pxo","afmacros","afmacro","mamc","ore","ezt","smm","73p","bns"];

		//print_r( $_FILES );

		if( !isset($_FILES['file']) ){
			json_response(["status"=>"fail","error"=>"File data missing"]);
		}
		if( !isset($_POST['filename']) ){
			json_response(["status"=>"fail","error"=>"Filename missing"]);
		}
		if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+\.[a-z0-9]{2,5}$/i", $_POST['filename']) || preg_match("/\/\//i", $_POST['filename']) || preg_match("/\/$/i", $_POST['filename']) ){
			json_response(["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
		}
		if( !isset($_FILES['file']['name']) || !isset($_FILES['file']['tmp_name']) ){
			json_response(["status"=>"fail","error"=>"Upload failed"]);
		}

		$ext = array_pop( explode(".",$_FILES['file']['name']) );
		if( in_array($ext, $block_extensions) ){
			json_response(["status"=>"fail", "error"=>"Uploaded File extension is vulnerable hence blocked"]);
		}

		$ext = array_pop( explode(".",$_POST['filename']) );
		if( in_array($ext, $block_extensions) ){
			json_response(["status"=>"fail", "error"=>"Filename extension is vulnerable hence blocked"]);
		}

		$x = explode(".",$_FILES['file']['name']);
		$ext = $x[ sizeof($x)-1 ];
		if( in_array($ext, $block_extensions) ){
			json_response(["status"=>"fail", "error"=>"File extension is vulnerable hence blocked"]);
		}
		if( file_exists( $_FILES['file']['tmp_name'] ) && filesize($_FILES['file']['tmp_name']) > 0 ){
			$sz = filesize($_FILES['file']['tmp_name']);
			$fn = ltrim($_POST['filename'], "/");
			try{
				$res =$s3con->putObject([
					"Bucket"=>$s3_bucket,
					"Key"=>$fn,
					"SourceFile"=>$_FILES['file']['tmp_name'],
					"ContentType"=>$_POST['type']
				]);
			}catch( Aws\S3\Exception\S3Exception $ex ){
				json_response([
					"status"=>"fail", 
					"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
				]);
			}
			json_response(['status'=>"success", "data"=>[
				"Key"=>"/".$fn, 
				"Date"=>date("Y-m-d H:i:s"), 
				"Size"=>$sz 
			]]);
		}else{
			json_response(['status'=>"fail", "error"=>"server error"]);
		}
		exit;
	}

}else{
	json_response(['status'=>"fail", "error"=>"Unhandled vault type or under development"]);
}