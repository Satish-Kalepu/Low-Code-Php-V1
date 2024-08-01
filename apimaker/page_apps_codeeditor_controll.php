<?php

$config_layout = "simple";

$page_type = "codeeditor";
if( !$config_param3 || !$config_param4 ){
	echo404("Incorrect URL");
}
$property_type = $config_param3;

if( $property_type == "pagecontrol" && $config_param4 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param4) ){
		echo404("Incorrect Page Version ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
		"app_id"=>$config_param1,
		"_id"=>$config_param4
	]);
	if( $res['data'] ){
		$code = $res['data'];
	}else{
		echo404("Api version not found!");
	}
	$code['version_id'] = $code['_id'];
	if( !$code['control'] ){
		$code["control"] = [
			"input-method"=>"GET",
			"input-type"=>"query_string",
			"output-type"=>"text/html",
			"auth-type"=>"None",
		];
	}
	if( $_POST['action'] == "load_engine_data" ){
		json_response([
			"status"=> "success",
			"engine"=> (isset($code['control']['engine'])?$code['control']['engine']: [] ),
			"test"=>   (isset($code['control']['test'])  ?$code['control']['test']:   [] )
		]);
	}

	unset($code['control']['engine']);
	unset($code['control']['test']);

	if( $_POST['action'] == "save_engine_data" ){
		$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
			"app_id"=>$config_param1,
			"_id"=> $config_param4
		],[
			"control.input-method"	=> $_POST['input-method'],
			"control.input-type"	=> $_POST['input-type'],
			"control.output-type"	=> $_POST['output-type'],
			"control.auth-type"	=> $_POST['auth-type'],
			"control.engine"	=> $_POST['data'],
			"control.updated"	=> date("Y-m-d H:i:s"),
		]);
		if( $res["status"] == "fail" ){
			json_response("fail","API update failed: ".$res["error"]);
		}
		event_log( "system", "app_codeeditor_save_engine", [
			"app_id"=>$config_param1, 
			"page_id"=>$config_param3,
			"page_version_id"=>$config_param4,
		]);
		json_response($res);
	}
}else{
	echo404("Unhandled");
}