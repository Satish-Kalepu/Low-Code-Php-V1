<?php

if( !isset($app['internal_redis']) ){
	$app['internal_redis'] = [
		"host"=>"localhost", "port"=>6379, "db" => "", "username"=>"", "password"=>"", "tls"=>false, "enable"=>false
	];
	$saved = false;
}else{
	$saved = true;
}

function v_type($vt){
	if( $vt == 1 ){return "string";}else 
	if( $vt == 2 ){return "set";} else
	if( $vt == 3 ){return "list";} else 
	if( $vt == 4 ){return "zset";} else 
	if( $vt == 5 ){return "hash";} else {return $vt;}
}

if( $_POST['action'] == 'redis_save_settings' ){

	if( !isset($_POST['settings']) ){
		json_response("fail", "Input missing");
	}
	$settings = [
		"host"=>"localhost", "port"=>6379, "username"=>"", "password"=>"", "tls"=>false, "enable"=>false
	];
	if( isset($_POST['settings']['host']) ){
		if( $_POST['settings']['host'] != "" && !preg_match("/^[a-z0-9\.\-\_]+$/", $_POST['settings']['host'] ) ){
			json_response("fail", "Host incorrect");
		}else{
			$settings["host"] = $_POST['settings']['host'];
		}
	}
	if( isset($_POST['settings']['port']) ){
		if( $_POST['settings']['port'] != "" && !preg_match("/^[0-9]+$/", $_POST['settings']['port'] ) ){
			json_response("fail", "Port incorrect");
		}else{
			$settings["port"] = (int)$_POST['settings']['port'];
		}
	}
	if( isset($_POST['settings']['db']) ) {
		if( $_POST['settings']['db'] != "" && !preg_match("/^[0-9]{1}$/",$_POST['settings']['db']) ) {
			json_response("fail","Database incorrect");
		}else {
			$settings['db'] = (int)$_POST['settings']['db'];
		}
	}
	if( isset($_POST['settings']['username']) ){
		if( $_POST['settings']['username'] != "" && !preg_match("/^[a-z0-9\.\-\_]+$/", $_POST['settings']['username'] ) ){
			json_response("fail", "Username incorrect");
		}else{
			$settings["username"] = $_POST['settings']['username'];
		}
	}
	if( isset($_POST['settings']['password']) ){
		if( $_POST['settings']['password'] != "" ){
			$settings["password"] = $_POST['settings']['password'];
		}
	}
	if( isset($_POST['settings']['tls']) ){
		$settings["tls"] = $_POST['settings']['tls']?true:false;
	}
	if( isset($_POST['settings']['enable']) ){
		$settings["enable"] = $_POST['settings']['enable']?true:false;
	}

	$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apps", [
		"_id"=>$config_param1,
	],[
		"internal_redis"=>$settings
	]);
	json_response($res);

	exit;
}


if( $_POST['action'] == 'redis_load_keys' ){

	if( $app['internal_redis']['enable'] === false ){
		json_response("fail", "Key Value store is not enabled");
	}

	$ops = [
		'host' => $app['internal_redis']['host'],
		'port' => (int)$app['internal_redis']['port'],
		'connectTimeout' => 1,
	];
	if( $app['internal_redis']['username'] && $app['internal_redis']['password'] ){
		$ops['auth'] = [$app['internal_redis']['username'], $app['internal_redis']['password']];
	}
	if( $app['internal_redis']['tls'] === true ){
		$ops['ssl'] = ['verify_peer' => true];
	}


	$redis_con = new Redis();
	if( $app['internal_redis']['username'] && $app['internal_redis']['password'] ){
		$redis_con->connect( $app['internal_redis']['host'], (int)$app['internal_redis']['port'], 1, '', 0, 0, ['auth'=>[ $app['internal_redis']['username'], $app['internal_redis']['password'] ]] );
	}else{
		$redis_con->connect( $app['internal_redis']['host'], (int)$app['internal_redis']['port'],  );
	}

	if($app['internal_redis']['db'] != "" && $app['internal_redis']['db'] > 0) {
		$redis_con->select((int)$app['internal_redis']['db']);
	}

	if($_POST['keyword'] == "") {
		$pattern = "*";
	}else {
		$pattern = $_POST['keyword'];
	}
	$k = $redis_con->keys($pattern);
	
	json_response(["status"=> "success", "keys"=>$k]);

	exit;
}

if( $_POST['action'] == 'redis_load_key' ){

	if( $app['internal_redis']['enable'] === false ){
		json_response("fail", "Key Value store is not enabled");
	}
	if( !isset($_POST['key']) ){
		json_response("fail", "Key input missing");
	}
	$key = $_POST['key'];

	$ops = [
		'host' => $app['internal_redis']['host'],
		'port' => (int)$app['internal_redis']['port'],
		'connectTimeout' => 1,
	];
	if( $app['internal_redis']['username'] && $app['internal_redis']['password'] ){
		$ops['auth'] = [$app['internal_redis']['username'], $app['internal_redis']['password']];
	}
	if( $app['internal_redis']['tls'] === true ){
		$ops['ssl'] = ['verify_peer' => true];
	}

	$redis_con = new Redis();
	if( $app['internal_redis']['username'] && $app['internal_redis']['password'] ){
		$redis_con->connect( $app['internal_redis']['host'], (int)$app['internal_redis']['port'], 1, '', 0, 0, ['auth'=>[ $app['internal_redis']['username'], $app['internal_redis']['password'] ]] );
	}else{
		$redis_con->connect( $app['internal_redis']['host'], (int)$app['internal_redis']['port'], 1 );
	}
	if($app['internal_redis']['db'] != "" && $app['internal_redis']['db'] > 0) {
		$redis_con->select((int)$app['internal_redis']['db']);
	}

	$type = v_type( $redis_con->type($key) );
	$data = [
		"type"=>$type,
		"ttl"=>$redis_con->ttl($key),
	];

	if( $type == "string" ){
		$data['data'] = $redis_con->get($key);
	}else if( $type == "zset" ){
		$fields = array();
		$data['field_length'] = $redis_con->zCard($key);
		$data['data'] = $redis_con->zscan($key, null, "*", 1000);
	}else if( $type == "hash" ){
		$data['data'] = $redis_con->hgetall($key);
	}else if( $type == "set" ){
		$data['data'] = $redis_con->smembers($key);
	}else if( $type == "list" ){
		$data['data'] = $redis_con->lrange($key, 0, 500);
	}
	json_response([
		"status"=> "success", 
		"data"=>$data
	]);
	exit;
}

if($_POST['action'] == "redis_key_delete") {
	if( $app['internal_redis']['enable'] === false ){
		json_response("fail", "Key Value store is not enabled");
	}
	if( !isset($_POST['key']) ){
		json_response("fail", "Key input missing");
	}
	$key = $_POST['key'];

	$ops = [
		'host' => $app['internal_redis']['host'],
		'port' => (int)$app['internal_redis']['port'],
		'connectTimeout' => 1,
	];
	if( $app['internal_redis']['username'] && $app['internal_redis']['password'] ){
		$ops['auth'] = [$app['internal_redis']['username'], $app['internal_redis']['password']];
	}
	if( $app['internal_redis']['tls'] === true ){
		$ops['ssl'] = ['verify_peer' => true];
	}
	$redis_db = 1;
	if($app['internal_redis']['db'] != "") {
		$redis_db = (int)$app['internal_redis']['db'];
	}

	$redis_con = new Redis();
	if( $app['internal_redis']['username'] && $app['internal_redis']['password'] ){
		$redis_con->connect( $app['internal_redis']['host'], (int)$app['internal_redis']['port'], 1, '', 0, 0, ['auth'=>[ $app['internal_redis']['username'], $app['internal_redis']['password'] ]] );
	}else{
		$redis_con->connect( $app['internal_redis']['host'], (int)$app['internal_redis']['port'], 1 );
	}
	if($app['internal_redis']['db'] != "" && $app['internal_redis']['db'] > 0) {
		$redis_con->select((int)$app['internal_redis']['db']);
	}

	$redis_delete = $redis_con->del($_POST['key']);

	json_response([
		"status"=> "success", 
		"data"=>$redis_delete
	]);
	exit;
}

if($_POST['action'] == "redis_key_edit") {
	if( $app['internal_redis']['enable'] === false ){
		json_response("fail", "Key Value store is not enabled");
	}
	if( !isset($_POST['key']) ){
		json_response("fail", "Key input missing");
	}
	$key = $_POST['key'];

	$ops = [
		'host' => $app['internal_redis']['host'],
		'port' => (int)$app['internal_redis']['port'],
		'connectTimeout' => 1,
	];
	if( $app['internal_redis']['username'] && $app['internal_redis']['password'] ){
		$ops['auth'] = [$app['internal_redis']['username'], $app['internal_redis']['password']];
	}
	if( $app['internal_redis']['tls'] === true ){
		$ops['ssl'] = ['verify_peer' => true];
	}

	$redis_con = new Redis();
	if( $app['internal_redis']['username'] && $app['internal_redis']['password'] ){
		$redis_con->connect( $app['internal_redis']['host'], (int)$app['internal_redis']['port'], 1, '', 0, 0, ['auth'=>[ $app['internal_redis']['username'], $app['internal_redis']['password'] ]] );
	}else{
		$redis_con->connect( $app['internal_redis']['host'], (int)$app['internal_redis']['port'], 1 );
	}
	if($app['internal_redis']['db'] != "" && $app['internal_redis']['db'] > 0) {
		$redis_con->select((int)$app['internal_redis']['db']);
	}

	$edit_record = $redis_con->set($key,$_POST['data']);
	$edit_record_time = $redis_con->expire($key,$_POST['time']);

	json_response([
		"status"=> "success", 
		"data"=>["record" => $edit_record,"record_time" => $edit_record_time]
	]);
	exit;
}