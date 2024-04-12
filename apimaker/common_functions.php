<?php

	if( preg_match( "/\.php/i", $_SERVER['REQUEST_URI'] ) ){
		header( "HTTP/1.1 403 Forbidden" );
		header( "stage: Script blocker" );
		exit;
	}

	if( file_exists("common_functions_new.php") ){
		require("common_functions_new.php");
	}

	function vget($v, $v2){
		if( isset($v) ){return $v;}else{return $v2;}
	}

	function get_token( $event ="", $expire = 5, $max_hits = 10 ){ // expire = minits
		global $config_global_apimaker;
		global $mongodb_con;
		if( !$event ){ // event is a combinations of event and respective record id
			return "EventRequired!";
		}
		//event should be a combination of action and respective record ids.
		$res1 = $mongodb_con->count( $config_global_apimaker['config_mongo_prefix'] . "_session_tokens", ['s'=>session_id()] );
		if( $res1['data'] ){
			$tokens_per_session = $res1['data']['val'];
		}else{
			$tokens_per_session = 0;
		}
		if( $tokens_per_session > $config_global_apimaker['config_max_tokens_per_session'] ){
			return "TooManyTokens!";
		}
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_session_tokens", [
			's'=>session_id(),
			't'=>time(),
			'ip'=>$_SERVER['REMOTE_ADDR'],
			'ua'=>$_SERVER['HTTP_USER_AGENT'],
			'e'=>$event,
			'exp'=>$expire,
			'date'=>new MongoDB\BSON\UTCDateTime(),
			'expire'=>new MongoDB\BSON\UTCDateTime( (time()+($expire*60))*1000 ),
			'cnt'=>1,
			'mh'=>$max_hits,
		]);
		return $res['inserted_id'];
	}
	function update_token_cnt( $token ){ // expire = minits
		global $mongodb_con;
		global $config_global_apimaker;
		$res = $mongodb_con->increment( $config_global_apimaker['config_mongo_prefix'] . "_session_tokens", $token, 'cnt', 1 );
	}
	function delete_token( $token ){ // expire = minits
		global $mongodb_con;
		global $config_global_apimaker;
		$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_session_tokens", [
			'_id'=>$token
		]);
	}
	function validate_token( $event, $token ){ // expire = minits
		//event should be a combination of action and respective record ids.
		if( !isset($event) ){
			return "EventParam Error";
		}else if( !isset($token) ){
			return "TokenParam Error";
		}
		global $mongodb_con;
		global $config_global_apimaker;
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_session_tokens", [
			'_id'=>$token
		]);
		if( $res['data'] ){
			if( $res['data']['e'] != $event ){
				return "IncorrectToken";
			}else if( $res['data']['ua'] != $_SERVER['HTTP_USER_AGENT'] ){
				return "IncorrectSource";
			}else if( $res['data']['s'] != session_id() ){
				return "SessionChanged";
			}else if( $res['data']['ip'] != $_SERVER['REMOTE_ADDR'] ){
				return "NetworkChanged";
			}else if( $res['data']['t'] < ( time()-($res['data']['exp']*60) ) ){
				return "TimeExpired";
			}else if( $res['data']['t'] < ( time()-($res['data']['exp']*60) ) ){
				return "TimeExpired";
			}else if( $res['data']['cnt'] > $res['data']['mh'] ){
				return "TooManyHits";
			}else{
				update_token_cnt($token, 'cnt', 1);
				return "OK";
			}
		}else{
			return "TokenNotFound";
		}
	}
	function password_strength( $p ){
	 	$f = true;
	 	if( !preg_match( "/^[A-Za-z0-9\W]{8,25}$/", $p ) ){
	 		$f = false;
		}
		if( !preg_match( "/[A-Z]/", $p ) ){
			$f = false;
		}
		if( !preg_match( "/[a-z]/", $p ) ){
			$f = false;
		}
		if( !preg_match( "/[0-9]/", $p ) ){
			$f = false;
		}
		if( !preg_match( "/\W/", $p ) ){
			$f = false;
		}
		return $f;
	}

	function http_response( $http_code, $body, $ct="text/plain" ){
		global $config_global_apimaker;
		if( $ct != "text/plain" && $ct != "text/html" && $ct != "application/json" ){
			$ct = "text/plain";
		}
		header("http/1.1 " . $http_code . " NotOK");
		if( $ct == "text/html" ){
			header("Content-Type: ".$ct);
			echo "<html>
			<head>
				<title>".$config_global_apimaker['config_app_name']."</title>
			</head>
			<body>" . $dody . "</body>
			</html>";
		}else if( is_array($body) ){
			header("Content-Type: application/json");
			echo json_encode($body);
		}else{
			header("Content-Type: ".$ct);
			echo $body;
		}
		exit;
	}

	function echosp($v){
		echo "<div>" . htmlspecialchars($v) . "</div>";
	}
	function print_pre( $v, $output = false ){
		if( $output ){
			return "<pre align=left style'text-align:left;'>".print_r($v,true)."</pre>";
		}else{
			echo "<pre>";
			print_r( $v );
			echo "</pre>";
		}
	}

   	function echo403json( $v = "Forbidden or Session Expired" ){
		//header("http/1.1 403 Forbidden");
		json_response("fail", "SessionExpired");
		exit;
	}

	function echo403( $v = "Forbidden or Session Expired" ){
		header("http/1.1 403 Forbidden");
		echo "<html><body><p>" . $v . "</p></body></html>";
		exit;
	}

	function echo404( $v = "Not Found" ){
		header("http/1.1 404 page not found");
		echo "<html><body><p>" . $v . "</p></body></html>";
		exit;
	}

	function echo400( $v = "Incorrect Request" ){
		header("http/1.1 400 Request Error");
		echo "<html><body><p>" . $v . "</p></body></html>";
		exit;
	}
	function echo500( $v = "Incorrect Request" ){
		header("http/1.1 500 Request Error");
		echo "<html><body><p>" . $v . "</p></body></html>";
		exit;
	}

	function json_response( $param1, $param2 = "" ){
		if( is_string($param1 ) ){
			if( $param1 == "success" ){
				$st = json_encode( array("status"=>$param1, "data"=>$param2), JSON_PRETTY_PRINT );
			}else if( $param1 == "fail" ){
				$st = json_encode( array("status"=>$param1, "error"=>$param2), JSON_PRETTY_PRINT );
			}else{
				$st = json_encode( array("status"=>$param1, "data"=>$param2), JSON_PRETTY_PRINT );
			}
		}else if( is_array($param1) ){
			$st = json_encode( $param1, JSON_PRETTY_PRINT );
		}
        if( !$st || json_last_error() ){
        	header("http/1.1 500 Error");
        	header("Content-Type: text/plain");
            echo "There was an error in output json encode: " . json_last_error_msg();
            print_r( $param1 );print_r( $param2 );
            exit;
        }else{
        	header("Content-Type: application/json");
            echo $st;
        }
		exit;
	}

	function pass_hash( $pass ){
		global $config_global_apimaker;
		$ctx = hash_init('whirlpool');
		//echo $config_global_apimaker['config_password_salt'];exit;
		hash_update( $ctx, $config_global_apimaker['config_password_salt'] );
		hash_update( $ctx, $pass );
		return hash_final( $ctx );
	}
	function pass_hash2( $pass, $salt ){
		$ctx = hash_init('whirlpool');
		hash_update( $ctx, $salt );
		hash_update( $ctx, $pass );
		return hash_final( $ctx );
	}
	function pass_encrypt( $data, $key= "" ){
		global $config_global_apimaker;
		if( !$key ){
			$key = $config_global_apimaker['config_encrypt_default_key'];
		}else if( !$config_global_apimaker['config_encrypt_keys'][ $key ] ){
			echo "Error in pass_encrypt key";exit;
		}
		if( strpos($data,$key.":") === 0 ){
			return $data;
		}
		$secret = $config_global_apimaker['config_encrypt_keys'][ $key ]['key'];

		$encrypted = @openssl_encrypt($data, "aes256", $secret);
		if( !$encrypted ){
			return "";
		}
		return $key.":".base64_encode($encrypted);
	}
	function pass_decrypt( $data ){
		global $config_global_apimaker;
		list($key,$data) = explode(":",$data,2);
		if( !$key ){
			return $data;
		}
		if( !$config_global_apimaker['config_encrypt_keys'][ $key ] ){
			echo "Error in pass_decrypt key";exit;
		}
		$secret = $config_global_apimaker['config_encrypt_keys'][ $key ]['key'];
		$decrypted =  openssl_decrypt(base64_decode($data), "aes256", $secret );
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
	function session_encrypt( $pass ){
		//$pass = strrev($pass);
		// return "s2_" . $pass;
		// return "s2_" . str_pad($pass, 10, "0", STR_PAD_LEFT);
		$encrypted = @openssl_encrypt($pass, "aes128", session_id() );
		return "s1_".base64_encode($encrypted);
	}
	function session_decrypt($pass) {
		if( substr($pass,0,3) == "s1_" ){
			$decrypted =  openssl_decrypt(base64_decode( substr($pass,3,4096) ),"aes128",session_id() );
			if( !$decrypted ){
				json_response("fail", "action_decrypt_error");
				exit;
			}
			return $decrypted;
		}else{
			return $pass;
		}
	}
	function data_encrypt( $pass ){
		if( $pass == "" ){
			return $pass;
		}
		global $config_global_apimaker;
		$encrypted = @openssl_encrypt($pass, $config_global_apimaker['config_encrypt_algo'], $config_global_apimaker['config_encrypt_key'] );
		return base64_encode($encrypted);
	}
	function data_decrypt( $pass ){
		if( $pass == "" ){
			return $pass;
		}
		global $config_global_apimaker;
		$decrypted = openssl_decrypt(base64_decode($pass),$config_global_apimaker['config_encrypt_algo'], $config_global_apimaker['config_encrypt_key']);
		return $decrypted;
	}

	function get_time_diff_text( $vsec ){
		$minits = round($vsec/60);
		if( $minits > 60 ){
			$hours = round($minits/60);
			if( $hours > 60 ){
				$days = round($hours/24);
				return $days . " days ";
			}
			return $hours . " hours ";
		}
		return $minits . " minutes ";	
	}

	function update_app_last_change_date( $app_id ){
		global $mongodb_con;
		global $config_global_apimaker;
		$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apps", [
			'_id'=>$app_id
		],[
			'last_updated'=>date("Y-m-d H:i:s")
		]);
	}

	function update_app_pages( $app_id ){
		//echo "update app pages: " . $app_id ;exit;
		global $mongodb_con;
		global $config_global_apimaker;
		if( !$app_id ){
			error_log("update_app_pages: app_id: missing");
		}else{
			$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apps", [
				"_id"=>$app_id,
			], ['projection'=>['settings.homepage'=>1]]);
			if( !$res['data'] ){
				error_log("update_app_pages: app_id: missing");
				return false;
			}
			$home_id = explode(":",$res['data']['settings']['homepage']['v'])[0];
			$home_version_id = explode(":",$res['data']['settings']['homepage']['v'])[1];
			$pages = []; $functions = []; $files = [];
			$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
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
			$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
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
			$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_files", [
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
			$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
				"app_id"=>$app_id,
			], ['projection'=>[
				'name'=>1, "version_id"=>1,
			]]);
			if( $res['data'] ){
				foreach( $res['data'] as $i=>$j ){if( !isset( $functions[ $j['name'] ] ) ){if( $j['name'] ){
					$functions[ $j['name'] ] = $j;
				}}}
			}
			$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apps", [
				'_id'=>$app_id
			],[
				'pages'=>$pages,
				'functions'=>$functions,
				'last_updated'=>date("Y-m-d H:i:s")
			]);
		}
	}
