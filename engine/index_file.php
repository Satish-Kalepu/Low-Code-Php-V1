<?php

function index_file($file_version){
	global $mongo_db;
	global $app_id;
	global $db_prefix;
	global $config_global_apimaker_engine;

	if( !isset($file_version['t']) ){
		return [500,"text/plain",[],"Incorrect file type" ];
		exit;
	}
	//echo "xxx";exit;
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
		return [200,$file_version['type'],[],$file_version['data'] ];
	}else if( $file_version['t'] == "base64" && preg_match("/^image/i", $file_version['type']) ){
		return [200,$file_version['type'],[],base64_decode($file_version['data']) ];
	}else if( $file_version['t'] == "base64" ){
		return [200,$file_version['type'],[],base64_decode($file_version['data']) ];
	}else{
		return [500,"text/plain",[],"Incorrect file type" ];
		exit;
	}

}