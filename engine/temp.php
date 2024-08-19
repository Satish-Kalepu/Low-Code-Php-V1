<?php

exit;

require("vendor/autoload.php");

/* Mongo DB connection */
require("class_mongodb.php");

$mongodb_con = new mongodb_connection( 
	"localhost", 
	8889, 
	"backendmaker_v2", 
	"stage", 
	"stage", 
	"admin", 
	false
);

$r = rand(100,999);

$start = time();
while(1){

	$res = $mongodb_con->increment("apimaker_graph_66a4e4ec7c813e84e00e66db_keywords", "T2:sekhar kasireddy chandra", "one", 1);
	echo $r.":".time().":".$res['data'] . "\n";
	if( time()-$start > 5 ){
		break;
	}
}