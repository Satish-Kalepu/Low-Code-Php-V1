<?php

require("page_apps_pages_page_vuejs_css.php"); 
require("page_apps_pages_page_vuejs_template.php"); 
require("page_apps_pages_page_vuejs_script.php");

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
