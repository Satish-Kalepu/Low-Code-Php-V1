<?php

function print_pre( $v ){
	echo "<pre>";
	print_r( $v );
	echo "</pre>";
}
function print_json( $v ){
	echo "<pre>";
	echo json_encode($v,JSON_PRETTY_PRINT);
	echo "</pre>";
}

function event_log( $system, $event, $data =[] ){
	global $mongodb_con;
	global $db_prefix;
	$data['system'] = $system;
	$data['event'] = $event;
	$data['m_i'] = date("Y-m-d H:i:s");
	$data['user_id'] = $_SESSION['apimaker_login_id'];
	$data['ip'] = $_SERVER['REMOTE_ADDR'];
	$sid = session_id();
	if( $sid ){
		$data['sid'] = $session_id;
	}
	try{
		$mongodb_con->insert( $db_prefix . "_zd_events", $data);
	}catch(Exception $ex){
		echo "Error event log" . $ex->getMessage();exit;
	}
}

function get_request($options){
	$headers = [];
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $options['url'],
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_TIMEOUT => 2,
	  CURLOPT_CONNECTTIMEOUT=>1,
	  CURLOPT_FOLLOWLOCATION => false,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_HEADER => true,
	  CURLOPT_HTTPHEADER => $options['headers']?$options['headers']:[],
	));
	$response = curl_exec($curl);
	$info = curl_getinfo($curl);
	$erno = curl_errno($curl);
	if( $erno ){
		$err = $erno . ":" . curl_error($curl);
	}else{$err="";}
	curl_close( $curl );
	if( $erno ){
		$status = "error";$body = "";
	}else{
		$resp = explode("\r\n\r\n", $response);
		$body = $resp[ sizeof($resp)-1 ];
		$hh = $resp[ sizeof($resp)-2 ];
		$status = $info['http_code'];
		$hl = explode("\n", $hh);
		foreach($hl as $hi=>$hv){
			$hp = explode(':', $hv, 2);
			if (count($hp) < 2){continue;}
			$headers[strtolower(trim($hp[0]))] = trim($hp[1]);
		}
	}
	return [
		"status"=>$status,
		"headers"=>$headers,
		"body"=>$body,
		"error"=>$err
	];
}


function pass_hash( $pass ){
	global $config_global_engine;
	$ctx = hash_init('whirlpool');
	hash_update( $ctx, $config_global_engine['config_password_salt'] );
	hash_update( $ctx, $pass );
	return hash_final( $ctx );
}
function pass_encrypt( $data, $key= "" ){
	global $config_global_engine;
	if( !$key ){
		$key = $config_global_engine['config_encrypt_default_key'];
	}else if( !$config_global_engine['config_encrypt_keys'][ $key ] ){
		echo "Error in pass_encrypt key";exit;
	}
	if( strpos($data,$key.":") === 0 ){
		return $data;
	}
	$secret = $config_global_engine['config_encrypt_keys'][ $key ]['key'];

	$encrypted = @openssl_encrypt($data, "aes256", $secret);
	if( !$encrypted ){
		return "";
	}
	return $key.":".base64_encode($encrypted);
}
function pass_decrypt( $data ){
	global $config_global_engine;
	list($key,$data) = explode(":",$data,2);
	if( !$key ){
		return $data;
	}
	if( !$config_global_engine['config_encrypt_keys'][ $key ] ){
		return "pass_decrypt key not found";
		echo "Error in pass_decrypt key";exit;
	}
	$secret = $config_global_engine['config_encrypt_keys'][ $key ]['key'];
	$decrypted =  openssl_decrypt(base64_decode($data), "aes256", $secret );
	if( !$decrypted ){
		return "DecryptFailed";
	}
	return $decrypted;
}

function pass_encrypt_static( $data, $key= "abcdefghijklmnop" ){
	$encrypted = openssl_encrypt($data, "aes256", $key);
	if( !$encrypted ){
		return "";
	}
	return $encrypted;
}
function pass_decrypt_static( $data, $key= "abcdefghijklmnop" ){
	$decrypted =  openssl_decrypt($data, "aes256", $key);
	return $decrypted;
}



function update_app_last_change_date( $app_id ){
	global $mongodb_con;
	global $db_prefix;
	if( !isset($db_prefix) ){
		http_response_code(500);
		echo "Error 55669";exit;
		return ;
	}
	$mongodb_con->update_one( $db_prefix . "_apps", [
		'_id'=>$app_id
	],[
		'last_updated'=>date("Y-m-d H:i:s")
	]);
}

function update_app_pages( $app_id ){
	//echo "update app pages: " . $app_id ;exit;
	global $mongodb_con;
	global $db_prefix;
	if( !isset($db_prefix) ){
		http_response_code(500);
		echo "Error 55669";exit;
		return ;
	}
	if( !$app_id ){
		error_log("update_app_pages: app_id: missing");
	}else{
		$res = $mongodb_con->find_one( $db_prefix . "_apps", [
			"_id"=>$app_id,
		], ['projection'=>['settings.homepage'=>1]]);
		if( !$res['data'] ){
			error_log("update_app_pages: app_id: missing");
			return false;
		}
		$home_id = explode(":",$res['data']['settings']['homepage']['v'])[0];
		$home_version_id = explode(":",$res['data']['settings']['homepage']['v'])[1];
		$pages = []; $functions = []; $files = [];
		$res = $mongodb_con->find( $db_prefix . "_pages", [
			"app_id"=>$app_id,
		], ['projection'=>[
			'name'=>1, "version_id"=>1, 'input-method'=>1,
		]]);
		if( $res['data'] ){
			foreach( $res['data'] as $i=>$j ){if( $j['name'] ){
				$j['t'] = "page";
				$pages[ $j['name'] ] = $j;
				// if( $j['_id'] == $home_id){
				// 	$home_version_id = $j['version_id'];
				// }
			}}
		}
		$pages['home'] = ['version_id'=>$home_version_id, 't'=>'page'];
		$res = $mongodb_con->find( $db_prefix . "_apis", [
			"app_id"=>$app_id,
			"vt"=>"api",
		], ['projection'=>[
			'name'=>1, "version_id"=>1, 'input-method'=>1, 'path'=>1, 'vt'=>1,
		]]);
		if( $res['data'] ){
			foreach( $res['data'] as $i=>$j ){if( !isset( $pages[ $j['name'] ] ) ){if( $j['name'] ){
				$j['t'] = "api";
				$p = ltrim($j['path'], "/");
				$pages[ $p.$j['name'] ] = $j;
			}}}
		}
		$res = $mongodb_con->find( $db_prefix . "_files", [
			"app_id"=>$app_id, 'vt'=>'file',
		], [
			'projection'=>[
				'name'=>1, "version_id"=>1, 'path'=>1, 'vt'=>1, 't'=>1, 'type'=>1
			],
			'sort'=>[
				'path'=>1,'name'=>1
			]
		]);
		if( $res['data'] ){
			//print_r( $res['data'] );exit;
			foreach( $res['data'] as $i=>$j ){
				$fn = substr($j['path'],1,500) . $j['name'];
				if( !isset( $pages[ $fn ] ) ){if( $j['name'] ){
					$j['tt'] = $j['t'];
					$j['t'] = "file";
					$pages[ $fn ] = $j;
				}}
			}
		}
		//print_r( $pages );exit;
		$res = $mongodb_con->find( $db_prefix . "_functions", [
			"app_id"=>$app_id,
		], ['projection'=>[
			'name'=>1, "version_id"=>1,
		]]);
		if( $res['data'] ){
			foreach( $res['data'] as $i=>$j ){if( !isset( $functions[ $j['name'] ] ) ){if( $j['name'] ){
				$functions[ $j['name'] ] = $j;
			}}}
		}
		$mongodb_con->update_one( $db_prefix . "_apps", [
			'_id'=>$app_id
		],[
			'pages'=>$pages,
			'functions'=>$functions,
			'last_updated'=>date("Y-m-d H:i:s")
		]);
	}
}
