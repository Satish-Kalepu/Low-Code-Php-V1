<?php

/* required config for local folder  in config_global_engine.php outside httpdocs

$deployment_mode = "apache"; // apache / lambda / container
$execution_mode = "local_folder"; // local_folder / cloud_folder

$config_global_apimaker_engine = [

	// default database settings:
	"config_engine_key" 			=> "",
	"config_engine_app_id" 			=> "",
	"config_apimaker_endpoint_url"			=> "http://v2.backendmaker.com/apimaker/",

	"config_engine_path"			=> "/engine/",

	"config_engine_cache_interval"	=>	60, // seconds
	"config_engine_cache_refresh_action_query_string"	=>	["cache"=>"refresh"], // seconds

];
*/ 

/* required config for Cloud folder  in config_global_engine.php outside httpdocs

$deployment_mode = "apache"; // apache / lambda / container
$execution_mode = "cloud_folder"; // local_folder / cloud_folder

$config_global_engine = [

	"timezone"						=> "Asia/Kolkata",

	"config_apimaker_endpoint_url"			=> "http://v2.backendmaker.com/apimaker/",
	"config_engine_cache_interval"	=>	60, // seconds
	"config_engine_cache_refresh_action_query_string"	=>	["cache"=>"refresh"], // seconds

	// default database settings:
	"config_mongo_host" 			=> "localhost",
	"config_mongo_port" 			=> 8889,
	"config_mongo_db" 				=> "backendmaker_v2",
	"config_mongo_debug"			=> true,
	"config_mongo_username" 		=> "stage",
	"config_mongo_password" 		=> "stage",
	"config_mongo_authSource" 		=> "admin", // default is always admin
	"config_mongo_tls" 				=> false,  // used for aws services or mongodb atlas
	"config_mongo_prefix"			=> "apimaker", // [a-z] no special chars

	//default cache database ( redis ) used for security, sessions
	"config_use_redis" 				=> false,
	"config_redis_host" 			=> "localhost",
	"config_redis_port" 			=> 6379,
	"config_redis_username" 		=> "",
	"config_redis_password" 		=> "",

	"config_encrypt_keys" 			=> [
		"k1"	=>	["key"=>"abcdef", "comments"=> "default"],
	],
];

*/