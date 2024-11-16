<?php

$apps_folder = "apps";

$_use_encrypted_scripts = true;

$_convert = true;
if( $_GET['convert'] == "skip" ){
	$_convert = false;
}
if( $_convert ){
	ob_start();
}

require("page_apps_sdk_sdk_css.php");
require("page_apps_sdk_sdk_template.php");
require("page_apps_sdk_sdk_script.php");

if( $_convert ){
//	echo ob_get_clean() ;exit;
	$d = script_convert( ob_get_clean() );
	if( 1==1 ){
		$d = preg_replace("/http\:\/\//", "httphttphttp", $d);
		$d = preg_replace("/https\:\/\//", "httpshttpshttps", $d);
		$d = preg_replace("/\/\/(.*?)[\r\n]/", "", $d);
		$d = preg_replace("/\/\*(.*?)\*\//", "", $d);
		$d = preg_replace("/[\r\n\t]{1,10}/", " ", $d);
		$d = preg_replace("/[\ ]{4,10}/", " ", $d);
		$d = preg_replace("/httpshttpshttps/", "https://", $d);
		$d = preg_replace("/httphttphttp/", "http://", $d);
		$d = preg_replace("/elseif/", "else if", $d);
	}
	echo $d;
}
