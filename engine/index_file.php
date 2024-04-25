<?php

if( !isset($file_version['t']) ){
	http_response_code(500);
	echo "Incorrect file type";
	exit;
}
header( "Content-Type: "  . $file_version['type']);
header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );
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
	echo $file_version['data'];
}else if( $file_version['t'] == "base64" && preg_match("/^image/i", $file_version['type']) ){
	echo base64_decode($file_version['data']);exit;
}else if( $file_version['t'] == "base64" ){
	echo base64_decode($file_version['data']);exit;
}else{
	http_response_code(500);
	echo "Incorrect file type";
	exit;
}

