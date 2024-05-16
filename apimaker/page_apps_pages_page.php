<?php

$apps_folder = "apps_";

$_use_encrypted_scripts = false;
if( $_GET['action'] == "convert_scripts" ){
	
}

$_convert = false;
if( $_GET['convert'] == "skip" ){
	$_convert = false;
}
if( $_convert ){
	ob_start();
}

require("page_apps_pages_page_css.php"); 
require("page_apps_pages_page_html.php"); 
require("page_apps_pages_page_script.php");

if( $_convert ){
	//	echo ob_get_clean() ;exit;
	$d = script_convert( ob_get_clean() );
	$d = preg_replace("/http\:\/\//", "httphttphttp", $d);
	$d = preg_replace("/https\:\/\//", "httpshttpshttps", $d);
	$d = preg_replace("/\/\/(.*?)[\r\n]/", "", $d);
	$d = preg_replace("/\/\*(.*?)\*\//", "", $d);
	//$d = preg_replace("/[\r\n\t\ ]{2,10}/", " ", $d);
	$d = preg_replace("/httpshttpshttps/", "https://", $d);
	$d = preg_replace("/httphttphttp/", "http://", $d);
	$d = preg_replace("/elseif/", "else if", $d);
	echo $d;
}
