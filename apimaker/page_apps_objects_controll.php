<?php

$apps_folder= "appsobjects";

if( $_POST['action'] == "load_keys" ){

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_objects", [
		"app_id"=>$config_param1,
	],[
		'projection'=>[
			'n'=>1, 't'=>1
		]
	]);

	json_response($res);

	exit;
}