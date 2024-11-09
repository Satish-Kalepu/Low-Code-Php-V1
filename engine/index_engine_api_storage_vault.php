<?php

function engine_api_storage_vault( $storage_vault, $action, $post ){

	global $mongodb_con; 
	global $db_prefix;
	global $app_id;

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

	//	print_r( $action );

		if( $action == "download" ){
			try{
				$res = $s3con->getObject([
					"Bucket"=>$s3_bucket,"Key"=>$_GET['key']
				])->toArray();
				return [200,"application/octet-stream",[
					"Content-Disposition"=>"attachment;filename=\"".$_GET['key']."\""
				],$res['Body']];
			}catch(Aws\S3\Exception\S3Exception $ex){
				return json_response(500,[
					"status"=>"fail", 
					"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
				]);
			}
			exit;
		}

		if( $action == "get_file" ){
			if( !isset($post['filename']) ){
				return json_response(200,["status"=>"fail","error"=>"Filename missing"]);
			}
			if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+$/i", $post['filename']) || preg_match("/\/\//i", $post['filename']) || preg_match("/\/$/i", $post['filename']) ){
				return json_response(200,["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
			}
			try{
				$res = $s3con->getObject([
					"Bucket"=>$s3_bucket,
					"Key"=>ltrim($post['filename'],"/")
				])->toArray();
				return json_response(200,['status'=>"success", "data"=>base64_encode($res['Body'])]);
			}catch(Aws\S3\Exception\S3Exception $ex){
				return json_response(500,[
					"status"=>"fail", 
					"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
				]);
			}
			exit;
		}
		if( $action == "get_raw_file" ){
			if( !isset($post['filename']) ){
				return json_response(200,["status"=>"fail","error"=>"Filename missing"]);
			}
			if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+$/i", $post['filename']) || preg_match("/\/\//i", $post['filename']) || preg_match("/\/$/i", $post['filename']) ){
				return json_response(200,["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
			}
			try{
				$f = $post['filename'];
				$x = explode("/",ltrim($post['filename'], "/"));
				$f2 = $x[ sizeof($x)-1 ];
				$res = $s3con->getObject([
					"Bucket"=>$s3_bucket,
					"Key"=>ltrim($f,"/")
				])->toArray();
				return [200,"application/octet-stream",[
					"Content-Disposition"=>"attachment;filename=\"".$f2."\""
				],$res['Body'] ];

			}catch(Aws\S3\Exception\S3Exception $ex){
				return json_response(500,[
					"status"=>"fail", 
					"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
				]);
			}
			exit;
		}

		if( $action == "list_files" ){

			if( isset($post['path']) ){
				if( $post['path'] != "/" && !preg_match("/^\/(.*?)\/$/i",$post['path']) ){
					return json_response(200,["status"=>"fail","error"=>"Path incorrect format"]);
				}
			}

			$prefix = "";
			try{
				$p  = [
					"Bucket"=>$s3_bucket,
					"Delimiter"=>"/",
					//"OptionalObjectAttributes"=>["Content-Type"],
				];
				if( isset($post['path']) && $post['path'] != "/" && $post['path'] != ""  ){
					$prefix = substr($post['path'],1,500);
					$p["Prefix"]=$prefix;
				}
				$res = $s3con->listObjectsV2($p)->toArray();
			}catch( Aws\S3\Exception\S3Exception $ex ){
				return json_response(200,[
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
							return json_response(200,[
								"status"=>"fail", 
								"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
							]);
						}
					}
					$prefixes[ $i ]["Prefix"] = "/".$prefixes[ $i ]["Prefix"];
				}
			}

			return json_response(200,[
				"status"=>"success", 
				"keys"=>$keys,
				"prefixes"=>$prefixes,
			]);

			exit;
		}
		if( $action == "list_files_mounted_path" ){

			if( isset($post['path']) ){
				if( $post['path'] != "/" && !preg_match("/^\/(.*?)\/$/i",$post['path']) ){
					return json_response(200,["status"=>"fail","error"=>"Path incorrect format"]);
				}
			}

			$prefix = "";
			try{
				$p  = [
					"Bucket"=>$s3_bucket,
					"Delimiter"=>"/",
					//"OptionalObjectAttributes"=>["Content-Type"],
				];
				if( isset($post['path']) && $post['path'] != "/" && $post['path'] != ""  ){
					$prefix = substr($post['path'],1,500);
					$p["Prefix"]=$prefix;
				}
				$res = $s3con->listObjectsV2($p)->toArray();
			}catch( Aws\S3\Exception\S3Exception $ex ){
				return json_response(200,[
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
							return json_response(200,[
								"status"=>"fail", 
								"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
							]);
						}
					}
					$prefixes[ $i ]["Prefix"] = "/".$prefixes[ $i ]["Prefix"];
				}
			}

			return json_response(200,[
				"status"=>"success", 
				"keys"=>$keys,
				"prefixes"=>$prefixes,
			]);

			exit;
		}


		if( $action == "delete_file" ){
			if( !isset($post['filename']) ){
				return json_response(200,["status"=>"fail","error"=>"Filename missing"]);
			}
			if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+$/i", $post['filename']) || preg_match("/\/\//i", $post['filename']) || preg_match("/\/$/i", $post['filename']) ){
				return json_response(200,["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
			}
			try{
				$res= $s3con->deleteObject([
					"Bucket"=>$s3_bucket, "Key"=>ltrim($post['filename'],"/")
				])->toArray();

				event_log("storage_vault", "file_delete", [
					"app_id"=>$app_id,
					"vault_id"=>$storage_vault['_id'],
					"Bucket"=>$s3_bucket,
					"type"=>"aws-s3",
					"file_id"=>$post['file_id'],
					"name"=>$post['filename']
				]);

			}catch( Aws\S3\Exception\S3Exception $ex ){
				return json_response(200,[
					"status"=>"fail", 
					"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
				]);
			}
			return json_response(200,["status"=>"success"]);
		}

		
		if( $action == "files_create_folder" ){
			if( !preg_match("/^[a-z0-9\.\-\_\/]{2,100}$/i", $post['new_folder']) ){
				return json_response("fail", "Name incorrect. Min 2 chars Max 100. No special chars");
			}
			$path = $post['path'];
			$prefix = ltrim($path, "/");
			$fn =  $prefix.$post['new_folder']."/";
			try{
				$res =$s3con->putObject([
					"Bucket"=>$s3_bucket,
					"Key"=>$fn,
					"Body"=>"",
				]);
				event_log("storage_vault", "create_folder", [
					"app_id"=>$app_id,
					"vault_id"=>$storage_vault['_id'],
					"Bucket"=>$s3_bucket,
					"type"=>"aws-s3",
					"name"=>$fn
				]);
			}catch( Aws\S3\Exception\S3Exception $ex ){
				return json_response(200,[
					"status"=>"fail", 
					"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
				]);
			}
			return json_response(200,['status'=>"success", "data"=>["Key"=>$fn, "Date"=>date("Y-m-d H:i:s"), "Size"=>0 ]] );
			exit;
		}

		if( $action == "put_file" ){

			$block_extensions = ["fap","apk","jar","ahk","cmd","ipa","run","xbe","0xe","rbf","vlx","workflow","u3p","8ck","bat","bms","exe","bin","elf","air","appimage","xap","gadget","app","mpk","widget","x86","shortcut","fba","mcr","pif","ac","com","xlm","tpk","sh","x86_64","73k","script","scpt","command","out","rxe","scb","ba_","ps1","paf.exe","scar","isu","scr","xex","fas","coffee","ex_","action","tcp","acc","celx","shb","ex5","rfu","ebs2","hta","cgi","xbap","nexe","ecf","fxp","sk","vpm","plsc","rpj","ws","azw2","js","mlx","dld","cof","vxp","caction","vbs","wsh","mcr","iim","ex_","phar","89k","cheat","esh","fpi","wcm","pex","server","gpe","a7r","dek","pyc","exe1","jsf","jsx","acr","ex4","pwc","ear","icd","epk","vexe","rox","mel","zl9","plx","mm","snap","paf","mcr","ms","tiapp","uvm","gm9","atmx","89z","vbscript","actc","pyo","applescript","frs","hms","otm","rgs","n","widget","csh","mrc","wiz","prg","ebs","tms","spr","cyw","sct","e_e","ebm","gs","mrp","osx","fky","xqt","fas","ygh","prg","app","mxe","actm","udf","kix","seed","cel","app","ezs","thm","beam","lo","vbe","kx","jse","prg","rfs","s2a","dmc","hpf","wpk","exz","scptd","ls","ms","msl","mhm","tipa","xys","prc","wpm","sca","ita","eham","wsf","qit","es","arscript","rbx","mem","sapk","ebacmd","upx","ipk","mam","ncl","ksh","dxl","ham","btm","mio","ipf","pvd","vdo","gpu","exopc","ds","mac","sbs","cfs","sts","asb","qpx","p","rpg","mlappinstall","srec","uw8","pxo","afmacros","afmacro","mamc","ore","ezt","smm","73p","bns"];

			//print_r( $_FILES );

			if( !isset($_FILES['file']) ){
				return json_response(200,["status"=>"fail","error"=>"File data missing"]);
			}
			if( !isset($post['filename']) ){
				return json_response(200,["status"=>"fail","error"=>"Filename missing"]);
			}
			if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+\.[a-z0-9]{2,5}$/i", $post['filename']) || preg_match("/\/\//i", $post['filename']) || preg_match("/\/$/i", $post['filename']) ){
				return json_response(200,["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
			}
			if( !isset($_FILES['file']['name']) || !isset($_FILES['file']['tmp_name']) ){
				return json_response(200,["status"=>"fail","error"=>"Upload failed"]);
			}

			$ext = array_pop( explode(".",$_FILES['file']['name']) );
			if( in_array($ext, $block_extensions) ){
				return json_response(200,["status"=>"fail", "error"=>"Uploaded File extension is vulnerable hence blocked"]);
			}

			$ext = array_pop( explode(".",$post['filename']) );
			if( in_array($ext, $block_extensions) ){
				return json_response(200,["status"=>"fail", "error"=>"Filename extension is vulnerable hence blocked"]);
			}

			$x = explode(".",$_FILES['file']['name']);
			$ext = $x[ sizeof($x)-1 ];
			if( in_array($ext, $block_extensions) ){
				return json_response(200,["status"=>"fail", "error"=>"File extension is vulnerable hence blocked"]);
			}
			if( file_exists( $_FILES['file']['tmp_name'] ) && filesize($_FILES['file']['tmp_name']) > 0 ){
				$sz = filesize($_FILES['file']['tmp_name']);
				$fn = ltrim($post['filename'], "/");
				try{
					$res =$s3con->putObject([
						"Bucket"=>$s3_bucket,
						"Key"=>$fn,
						"SourceFile"=>$_FILES['file']['tmp_name'],
						"ContentType"=>$post['type']
					]);
				}catch( Aws\S3\Exception\S3Exception $ex ){
					return json_response(200,[
						"status"=>"fail", 
						"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
					]);
				}

				event_log("storage_vault", "file_create", [
					"app_id"=>$app_id,
					"vault_id"=>$storage_vault['_id'],
					"Bucket"=>$s3_bucket,
					"type"=>"aws-s3",
					"name"=>$fn
				]);

				return json_response(200,['status'=>"success", "data"=>[
					"Key"=>"/".$fn, 
					"Date"=>date("Y-m-d H:i:s"), 
					"Size"=>$sz 
				]]);
			}else{
				return json_response(200,['status'=>"fail", "error"=>"server error"]);
			}
			exit;
		}

		if( $action == "create_signed_url" ){

			$block_extensions = ["fap","apk","jar","ahk","cmd","ipa","run","xbe","0xe","rbf","vlx","workflow","u3p","8ck","bat","bms","exe","bin","elf","air","appimage","xap","gadget","app","mpk","widget","x86","shortcut","fba","mcr","pif","ac","com","xlm","tpk","sh","x86_64","73k","script","scpt","command","out","rxe","scb","ba_","ps1","paf.exe","scar","isu","scr","xex","fas","coffee","ex_","action","tcp","acc","celx","shb","ex5","rfu","ebs2","hta","cgi","xbap","nexe","ecf","fxp","sk","vpm","plsc","rpj","ws","azw2","js","mlx","dld","cof","vxp","caction","vbs","wsh","mcr","iim","ex_","phar","89k","cheat","esh","fpi","wcm","pex","server","gpe","a7r","dek","pyc","exe1","jsf","jsx","acr","ex4","pwc","ear","icd","epk","vexe","rox","mel","zl9","plx","mm","snap","paf","mcr","ms","tiapp","uvm","gm9","atmx","89z","vbscript","actc","pyo","applescript","frs","hms","otm","rgs","n","widget","csh","mrc","wiz","prg","ebs","tms","spr","cyw","sct","e_e","ebm","gs","mrp","osx","fky","xqt","fas","ygh","prg","app","mxe","actm","udf","kix","seed","cel","app","ezs","thm","beam","lo","vbe","kx","jse","prg","rfs","s2a","dmc","hpf","wpk","exz","scptd","ls","ms","msl","mhm","tipa","xys","prc","wpm","sca","ita","eham","wsf","qit","es","arscript","rbx","mem","sapk","ebacmd","upx","ipk","mam","ncl","ksh","dxl","ham","btm","mio","ipf","pvd","vdo","gpu","exopc","ds","mac","sbs","cfs","sts","asb","qpx","p","rpg","mlappinstall","srec","uw8","pxo","afmacros","afmacro","mamc","ore","ezt","smm","73p","bns"];

			if( !isset($post['filename']) ){
				return json_response(200,["status"=>"fail","error"=>"Filename missing"]);
			}
			if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+\.[a-z0-9]{2,5}$/i", $post['filename']) || preg_match("/\/\//i", $post['filename']) || preg_match("/\/$/i", $post['filename']) ){
				return json_response(200,["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
			}

			$ext = array_pop( explode(".",$post['filename']) );
			if( in_array($ext, $block_extensions) ){
				return json_response(200,["status"=>"fail", "error"=>"Uploaded File extension is vulnerable hence blocked"]);
			}

			try{
				$cmd =$s3con->getCommand('PutObject',[
					"Bucket"=>$s3_bucket,
					"Key"=>substr($post['filename'],1,9999),
				]);
				$res = $s3con->createPresignedRequest($cmd, '+5 minute');
			}catch( Aws\S3\Exception\S3Exception $ex ){
				return json_response(200,[
					"status"=>"fail", 
					"error"=>$ex->getAwsErrorType() . ": " . $ex->getAwsErrorCode()
				]);
			}

			event_log("storage_vault", "signed_url_create", [
				"app_id"=>$app_id,
				"vault_id"=>$storage_vault['_id'],
				"Bucket"=>$s3_bucket,
				"type"=>"aws-s3",
				"name"=>$post['filename']
			]);

			return json_response(200,[
				'status'=>"success", "signed_url"=>$res->getUri()
			]);
			exit;
		}

	}else{
		return json_response(200,['status'=>"fail", "error"=>"Unhandled vault type or under development"]);
	}

}