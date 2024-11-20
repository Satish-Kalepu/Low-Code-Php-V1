<?php

if( $_POST['action'] == "get_sdks" ){
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
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

if( $_POST['action'] == "delete_sdk" ){
	$t = validate_token("deletesdk". $config_param1 . $_POST['sdk_id'], $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['sdk_id']) ){
		json_response("fail", "ID incorrect");
	}
	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
		'_id'=>$_POST['sdk_id']
	]);
	$res = $mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_sdks_versions", [
		'sdk_id'=>$_POST['sdk_id']
	]);

	event_log( "system", "sdk_delete", [
		"app_id"=>$config_param1,
		"sdk_id"=>$_POST['sdk_id'],
	]);

	json_response($res);
}

if( $_POST['action'] == "create_sdk" ){
	if( !preg_match("/^[a-z][a-z0-9\.\-\_]{2,150}$/i", $_POST['new_sdk']['name']) ){
		json_response("fail", "Name incorrect");
	}
	if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{2,300}$/i", $_POST['new_sdk']['des']) ){
		json_response("fail", "Description incorrect");
	}
	if( !is_array($_POST['new_sdk']['keywords']) ){
		json_response("fail", "keywords incorrect");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
		"app_id"=>$config_param1,
		'name'=>$_POST['new_sdk']['name'],
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}

	$version_id = $mongodb_con->generate_id();
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
		"app_id"=>$config_param1,
		"name"=>$_POST['new_sdk']['name'],
		"des"=>$_POST['new_sdk']['des'],
		"keywords"=>$_POST['new_sdk']['keywords'],
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
		"active"=>true,
		"version"=>1,
		"version_id"=>$version_id,
	]);
	if( $res['status'] == 'success' ){
		$res2 = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_sdks_versions", [
			"_id"=>$mongodb_con->get_id($version_id),
			"app_id"=>$config_param1,
			"sdk_id"=>$res['inserted_id'],
			"name"=>$_POST['new_sdk']['name'],
			"des"=>$_POST['new_sdk']['des'],
			"keywords"=>$_POST['new_sdk']['keywords'],
			"created"=>date("Y-m-d H:i:s"),
			"updated"=>date("Y-m-d H:i:s"),
			"active"=>true,
			"version"=>1,
		]);

		event_log( "system", "sdk_create", [
			"app_id"=>$config_param1,
			"sdk_id"=>$res['inserted_id'],
			"sdk_version_id"=>$version_id,
		]);

		json_response(['status'=>'success', 'sdk_id'=>$res['inserted_id'], 'sdk_version_id'=>$version_id]);
	}else{
		json_response($res);
	}
	exit;
}


if( $config_param3 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param3) ){
		echo404("Incorrect sdk ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
		'app_id'=>$app['_id'],
		'_id'=>$config_param3
	]);
	if( !$res['data'] ){
		echo404("sdk not found!");
	}
	$main_sdk = $res['data'];
}

if( $config_param4 && $main_sdk ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param4) ){
		echo404("Incorrect sdk Version ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_sdks_versions", [
		"sdk_id"=>$main_sdk['_id'],
		"_id"=>$config_param4
	]);
	if( $res['data'] ){
		$sdk = $res['data'];
	}else{
		echo404("sdk version not found!");
	}

	if( $_POST['action'] == "save_sdk_structure" ){
		if( !isset($_POST['raw']) ){
			json_response("fail", "incorrect structure format");
		}
		$body = $_POST['raw'];
		if( !is_string($body) ){
			json_response("fail", "incorrect structure format");
		}

		$res = $mongodb_con->update_one( $db_prefix . "_sdks_versions", ["_id"=>$config_param4], [
			'raw'=>base64_encode($body)
		]);

		$fn = "/tmp/" . time() . ".php";
		file_put_contents($fn, $body);
		exec("php -l " . $fn, $out);
		if( sizeof($out) ){
			if( !preg_match("/no syntax/i", $out[0]) ){
				$msg = implode("<BR>",$out);
				$msg = str_replace( $fn, "", $msg );
				json_response(['status'=>"fail", "error"=>"Syntax Errors", "msg"=>$msg]);
			}
		}
		$out = [];
	
		preg_match( "/(eval|exec|system|popen|fopen|file_put_contents|file_get_contents|unlink|delete|rename|chdir|chroot|closedir|dir|getcwd|opendir|readdir|rewinddir|scandir)[\ \t\r\n]*\(/", $body, $m );
		if( $m ){
			json_response(['status'=>"fail", "error"=>"Forbidden", "msg"=>$m[1] . " command is forbidden to use" ]);
		}

		preg_match( "/(dio\_|xdiff\_file)/", $body, $m);
		if( $m ){
			json_response(['status'=>"fail", "error"=>"Forbidden", "msg"=>$m[1] . " command is forbidden to use" ]);
		}

		preg_match( "/(basename|chgrp|chmod|chown|clearstatcache|copy|delete|dirname|disk_free_space|disk_total_space|diskfreespace|fclose|fdatasync|feof|fflush|fgetc|fgetcsv|fgets|fgetss|file|file_exists|file_get_contents|file_put_contents|fileatime|filectime|filegroup|fileinode|filemtime|fileowner|fileperms|filesize|filetype|flock|fnmatch|fopen|fpassthru|fputcsv|fputs|fread|fscanf|fseek|fstat|fsync|ftell|ftruncate|fwrite|glob|is_dir|is_executable|is_file|is_link|is_readable|is_uploaded_file|is_writable|is_writeable|lchgrp|lchown|link|linkinfo|lstat|mkdir|move_uploaded_file|parse_ini_file|parse_ini_string|pathinfo|pclose|popen|readfile|readlink|realpath|realpath_cache_get|realpath_cache_size|rename|rewind|rmdir|set_file_buffer|stat|symlink|tempnam|tmpfile|touch|umask|unlink)[\ \t\r\n]*\(/i", $body, $m );
		if( $m ){
			json_response(['status'=>"fail", "error"=>"Forbidden", "msg"=>$m[1] . " command is forbidden to use" ]);
		}

		preg_match_all("/class ([a-z0-9\_\-\.\\\$]+)/i", $body,$m);
		if( !$m ){
			json_response(['status'=>"fail", "error"=>"Class not found" ]);
		}
		if( sizeof($m[0])==0 ){
			json_response(['status'=>"fail", "error"=>"Class not found" ]);
		}
		if( sizeof($m[0]) > 1 ){
			json_response(['status'=>"fail", "error"=>"Found more than one class" ]);
		}
		//print_r( $m );exit;
		if( $m[1][0] != "ClassName" ){
			json_response(['status'=>"fail", "error"=>"class name should be ClassName. Got: " . $m[1][0] ]);
		}

		$public_methods = [];
		preg_match_all("/\n([a-z\t\ ]+)function([\ \t]+)([a-z0-9\_\-\.\ ]+)\((.*?)\)/i", $body, $m);
		//print_r( $m );exit;
		foreach( $m[1] as $i=>$j ){
			if( trim($j) != "public" && trim($j) != "private" ){
				json_response(['status'=>"fail", "error"=>"function scope (public/private) required" ]);
			}
			if( trim($m[3][$i]) != "_construct" ){
				$public_methods[] = $m[3][$i];
			}
		}

		$inifn = "/tmp/php.ini";
		file_put_contents($inifn, 'zend.exception_ignore_args On'.PHP_EOL);

		$fn2 = "/tmp/" . time() . "_.php";
		$script = "<"."?"."php"."\n";
		$script .= 'ini_set("zend.exception_ignore_args", "On");'."\n";
		$script .= "try{"."\n";
		$script .= "require(\"".$fn."\");"."\n";
		$script .= '}catch(Exception $ex){ echo json_encode(["status"=>"fail", "error"=>$ex->getMessage()]); }'."\n";
		$script .= "try{"."\n";
		$script .= '$obj = new ClassName();'."\n";
		$script .= '$methods = $obj->methods;'."\n";
		$script .= '$methods_ = get_class_methods($obj);'."\n";
		$script .= '}catch(Exception $ex){ echo json_encode(["status"=>"fail", "error"=>$ex->getMessage()]); }'."\n";
		$script .= 'if( !is_array($methods) ){ echo json_encode(["status"=>"fail", "error"=>"MethodsNotFound"]); }'."\n";
		$script .= 'echo json_encode(["status"=>"success", "methods"=>$methods, "methods_"=>$methods_]);'."\n";

		file_put_contents( $fn2, $script );
		exec( "php -c ". $inifn . " " .$fn2, $out );

		$methods = [];

		if( $out == null || !is_array($out) ){
			json_response(['status'=>"fail", "error"=>"Syntax Errors", "msg"=>"Compilation failed 1", "out"=>$out]);
		}else{
			$output = implode("", $out);
			preg_match("/^\{[\S\s]+\}$/",$output,$mm);
			if( !$mm ){
				$output = str_replace( $fn,  "", $output );
				$output = str_replace( $fn2, "", $output  );
				json_response(['status'=>"fail", "error"=>"Syntax Errors", "msg"=>"Compilation failed: ". $output, "script"=>$script, "output"=>$output]);
			}else{
				$script_res = json_decode($output, true);
				if( !$script_res ){
					json_response(['status'=>"fail", "error"=>"Syntax Errors", "msg"=>"Compilation failed: ". $output, "script"=>$script]);
				}
				if( !isset($script_res['status']) || ( !isset($script_res['methods']) && !isset($script_res['error']) ) ){
					json_response(['status'=>"fail", "error"=>"Syntax Errors", "msg"=>"Compilation failed: ". $output, "script"=>$script]);
				}
				if( $script_res['status'] == "fail" ){
					json_response(['status'=>"fail", "error"=>$script_res['error']]);
				}
				$methods = $script_res['methods'];
				$methods_ = $script_res['methods_'];
				foreach( $methods as $method=>$j ){
					if( !in_array($method, $methods_) ){
						json_response(['status'=>"fail", "error"=>"Method `".$method."` not declared" ]);
					}
				}
				foreach( $public_methods as $i=>$method ){
					if( !isset($methods[ $method ]) ){
						json_response(['status'=>"fail", "error"=>"Method `".$method."` template not found" ]);
					}
				}
			}
		}

		$res = $mongodb_con->update_one( $db_prefix . "_sdks_versions", ["_id"=>$config_param4], [
			"syntax_version"=>1,
			'body'=>base64_encode($body),
			'methods'=>$methods
		] );
		$res['output'] = $script_res;
		json_response($res);
	}

	if( $_POST['action'] == "save_settings" ){
		if( !isset($_POST['name']) ){
			json_response("fail", "incorrect name");
		}
		if( !is_string($_POST['des']) ){
			json_response("fail", "incorrect des");
		}

		$name = trim($_POST['name']);

		if( !preg_match("/^[a-z][a-z0-9\.\-\_]{2,150}$/i", $name) ){
			json_response("fail", "Name incorrect");
		}
		if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{2,300}$/i", $_POST['des']) ){
			json_response("fail", "Description incorrect");
		}
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_sdks", [
			"app_id"=>$config_param1,
			'name'=>$name,
			'_id'=>['$ne'=>$config_param3],
		]);
		if( $res['data'] ){
			json_response("fail", "Name conflicts with other SDK");
		}

		$res = $mongodb_con->update_one( $db_prefix . "_sdks", [
			"_id"=>$config_param3
		], [
			'name'=>$name,
			'des'=>$_POST['des'],
			'keywords'=>$_POST['keywords']
		]);

		$res = $mongodb_con->update_one( $db_prefix . "_sdks_versions", [
			"_id"=>$config_param4
		], [
			'name'=>$name,
			'des'=>$_POST['des']
		]);
		json_response($res);
	}

}