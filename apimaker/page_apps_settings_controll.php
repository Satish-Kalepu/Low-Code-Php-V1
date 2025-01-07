<?php

$config_cloud_enabled = false;
if( isset($config_global_apimaker['config_cloud_enabled']) && $config_global_apimaker['config_cloud_enabled'] === true ){
	$config_cloud_enabled = true;
}

$akey = pass_encrypt_static(json_encode([
	"action"=>"start_taskscheduler", 
	"app_id"=>$config_param1,
]), "abcdefgh");

$config_settings_credential_type_names = [
	"simple" => "Standard",
	"aws_cloud" => "AWS Cloud",
	"google_cloud" => "Google Cloud",
	"rabbitmq" => "RabbitMQ",
	"sendgrid" => "SendGrid",
	"rsa_key" => "OpenSSL RSA Private Key",
	"ssl_public" => "SSL Public Certificate",
	"ssl_private" => "SSL Private Certificate",
	"ssl_bundle" => "SSL Bundle"
];


$config_settings_credential_types = [
	"simple"=> [
		"username"=> [
			"label"=> "Username",
			"type"=> "text",
			"value"=> "",
			"encrypt"=> false,
			"regexp"=> "/^[a-z0-9\.\-\_]{5,100}$/i"
		],
		"password"=> [
			"label"=> "Password",
			"type"=> "text",
			"value"=> "",
			"encrypt"=> true
		]
	],
	"aws_cloud"=> [
		"access_key"=> [
			"label"=> "Access Key",
			"type"=> "text",
			"value"=> "",
			"encrypt"=> false,
			"regexp"=> "/^[A-Z0-9]{20}$/"
		],
		"secret"=> [
			"label"=> "Secret",
			"type"=> "text",
			"value"=> "",
			"encrypt"=> true,
			"regexp"=> "/^[A-Za-z0-9\;\/\+\=\/]{40,150}$/"
		],
		"region"=> [
			"label"=> "Region",
			"type"=> "text",
			"value"=> "",
			"encrypt"=> false,
			"regexp"=> "/^[a-z0-9\-]{5,25}$/"
		]
	],
	"rsa_key"=> [
		"username"=> [
			"label"=> "Username",
			"type"=> "text",
			"value"=> "",
			"encrypt"=> false,
			"regexp"=> "/^[a-z0-9\.\-\_]{5,100}$/i",
			"regexp"=> "/^[a-z0-9\.\-\_\/\=\+\;\r\n\ ]{100,2048}$/i"
		],
		"use_username"=> [
			"label"=> "Use Username",
			"type"=> "checkbox",
			"value"=> true,
			"encrypt"=> false,
			"regexp"=> "/^[a-z0-9\.\-\_\/\=\+\;\r\n\ ]{100,2048}$/i"
		],
		"key"=> [
			"label"=> "Key",
			"type"=> "multiline",
			"value"=> "",
			"encrypt"=> true,
			"regexp"=> "/^[a-z0-9\.\-\_\/\=\+\;\r\n\ ]{100,2048}$/i"
		]
	],
	"ssl_bundle"=> [
		"public"=> [
			"label"=> "Public certificate",
			"type"=> "multiline",
			"value"=> "",
			"encrypt"=> false,
			"regexp"=> "/^[a-z0-9\.\-\_\/\=\+\;\r\n\ ]{100,2048}$/i"
		],
		"chain"=> [
			"label"=> "Intermediate chain",
			"type"=> "multiline",
			"value"=> "",
			"encrypt"=> false,
			"regexp"=> "/^[a-z0-9\.\-\_\/\=\+\;\r\n\ ]{100,2048}$/i"
		],
		"private"=> [
			"label"=> "Private Key",
			"type"=> "multiline",
			"value"=> "",
			"encrypt"=> true,
			"regexp"=> "/^[a-z0-9\.\-\_\/\=\+\;\r\n\ ]{100,2048}$/i"
		]
	]
];


if( $_POST['action'] == "get_new_key" ){
	json_response([
		"status"=>"success",
		"key"=>$mongodb_con->generate_id()
	]);
	exit;
}

if( $_POST['action'] == "app_update_name" ){
	$t = validate_token("app_update.". $config_param1, $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	$name = strtolower(trim($_POST['app']['app']));
	$des = trim($_POST['app']['des']);
	if( !preg_match("/^[a-z][a-z0-9\-]{3,25}$/", $name) ){
		json_response(['status'=>"fail", "error"=>"Name invalid"]);
	}
	if( !preg_match("/^[A-Za-z0-9\.\,\-\ \_\(\)\[\]\ \@\#\!\&\r\n\t]{4,50}$/", $des) ){
		json_response(['status'=>"fail", "error"=>"Description invalid"]);
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apps", [
		'app'=>$name,
		'_id'=>['$ne'=>$config_param1]
	]);
	if( $res['data'] ){
		json_response(['status'=>"fail", "error"=>"An app already exists with same name"]);
	}

	$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apps",[
		'_id'=>$config_param1
	],[
		'app'=>$name,'des'=>$des,
	]);

	event_log( "system", "app_update_name", [
		"app_id"=>$config_param1,
	]);
	json_response($res);

	exit;
}

if( $_POST['action'] == "settings_load_pages" ){
	$t = validate_token("getpages.". $config_param1, $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		'app_id'=>$config_param1
	],[
		'sort'=>['name'=>1], 'limit'=>200, 'projection'=>['version_id'=>1,'name'=>1]
	]);
	$res2 = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'app_id'=>$config_param1
	],[
		'sort'=>['path'=>1,'name'=>1],
		'limit'=>200,
		'projection'=>['path'=>1,'name'=>1]
	]);
	json_response([
		"status"=>"success",
		"pages"=>$res['data'],
		"files"=>$res2['data'],
	]);
}

if( $_POST['action'] == "app_save_custom_settings" ){

	$settings = $_POST['settings'];

	if( !isset($settings['host']) ){
		json_response("fail", "Incorrect data. Host settings missing.");
	}
	if( gettype($settings['host']) != "boolean" ){
		json_response("fail", "Incorrect data");
	}
	if( $settings['host'] ){
		if( !isset($settings['domains']) ){
			json_response("fail", "Incorrect data. URL settings missing.");
		}else if( sizeof($settings['domains']) == 0 ){
			json_response("fail", "Incorrect data. URL settings missing.");
		}
		foreach( $settings['domains'] as $i=>$j ){
			if( !preg_match("/^(https\:\/\/www\.|http\:\/\/www\.|https\:\/\/|http\:\/\/)(localhost|[0-9\.]{7,15}|[a-z0-9\-\.]{3,200}\.[a-z\.]{2,10})[\:0-9]*\/[a-z0-9\.\-\_\.\/]*/i", $j['url']) ){
				json_response("fail", "Incorrect url " . $j['url'] );
			}
			$v = parse_url($settings['domains'][ $i ]['url']);
			if( isset( $v['port'] ) ){
				$settings['domains'][ $i ]['domain'] = $v['host'] . ":" . $v['port'];
			}else{
				$settings['domains'][ $i ]['domain'] = $v['host'];
			}
			$settings['domains'][ $i ]['path'] = $v['path'];
		}
		if( !isset($settings['keys']) ){
			json_response("fail", "Incorrect data. Key settings missing.");
		}else if( sizeof($settings['keys']) == 0 ){
			json_response("fail", "Incorrect data. Key settings missing.");
		}
		foreach( $settings['keys'] as $i=>$j ){
			if( !isset($j['key']) ){
				json_response("fail", "Incorrect key" );
			}
			if( !preg_match("/^[a-f0-9]{24}$/", $j['key'] ) ){
				json_response("fail", "Incorrect key: " . $j['key'] );
			}
			if( !isset($j['ips_allowed']) || !is_array($j['ips_allowed']) ){
				json_response("fail", "Incorrect Ip settings" );
			}
			if( sizeof($j['ips_allowed']) == 0 ){
				json_response("fail", "Incorrect IP settings");
			}
			foreach( $j['ips_allowed'] as $ipi=>$ipv ){
				if( $ipv['ip'] == "*" || $ipv['ip'] == "0.0.0.0/0" ){}else{
				if( !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(8|16|24|32)$/", $ipv['ip'] ) ){
					json_response("fail", "Incorrect IP format: " . $ipv['ip']);
				}}
				if( $ipv['action'] != "Allow" && $ipv['action'] != "Reject" ){
					json_response("fail", "Incorrect Action: " . $ipv['action']);
				}
			}
		}
	}else{
		$settings['domains'] = [];$settings['keys'] = [];
	}

	$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apps", [
		'_id'=>$config_param1
	], [
		'settings.domains'=>$settings['domains'],
		'settings.keys'=>$settings['keys'],
		'settings.host'=>$settings['host'],
		'last_updated'=>date("Y-m-d H:i:s")
	]);
	if( $res['status'] == "fail" ){
		json_response( $res );
	}

	event_log( "system", "app_save_custom_settings", [
		"app_id"=>$config_param1,
	]);

	update_app_pages( $config_param1 );

	json_response([
		"status"=>"success",
	]);
	exit;
}



if( $_POST['action'] == "app_save_other_settings" ){

	if( !isset($_POST['homepage']) ){
		json_response("fail", "Incorrect data. Page settings missing.");
	}

	$homepage = $_POST['homepage'];

	//print_r($m);exit;
	if( $homepage['t'] == "page" ){
		if( !preg_match("/^([a-f0-9]{24})\:([a-f0-9]{24})$/", $homepage['v'], $m) ){
			json_response("fail", "Incorrect data. Incorrect format.");
		}
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
			'_id'=>$m[1]
		]);
		if( !$res['data'] ){
			json_response("fail", "Page not found.");
		}
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
			'_id'=>$m[2],
			'page_id'=>$m[1]
		]);
		if( !$res['data'] ){
			json_response("fail", "Page Version not found.");
		}
	}else if( $homepage['t'] == "file" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $homepage['v']) ){
			json_response("fail", "Incorrect data. Incorrect format.");
		}
		$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_files", [
			'_id'=>$homepage['v']
		]);
		if( !$res['data'] ){
			json_response("fail", "File not found.");
		}
	}else{
		json_response("fail", "Incorrect home page type");
	}

	$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apps", [
		'_id'=>$config_param1
	], [
		'settings.homepage'=>$homepage,
		'last_updated'=>date("Y-m-d H:i:s")
	]);
	if( $res['status'] == "fail" ){
		json_response( $res );
	}

	update_app_pages( $config_param1 );

	event_log( "system", "app_save_other_settings", [
		"app_id"=>$config_param1,
	]);

	json_response([
		"status"=>"success",
	]);
	exit;
}

//print_r( $res );
if( !$app['settings'] ){
	$settings = [
		"host"=>false,
		"domains"=>[
			[
				"domain"=>"www.example.com",
				"url"=>"http://www.example.com/path/",
				"path"=>"/path/"
			]
		],
		"keys"=>[
			[
				"key"=>$mongodb_con->generate_id(),
				"ips_allowed"=>[
					["ip"=>"*", "action"=>"Allow"],
					["ip"=>"127.0.0.1/32", "action"=>"Allow"],
					["ip"=>"10.10.10.0/24", "action"=>"Allow"],
					["ip"=>"10.10.0.0/16", "action"=>"Allow"],
					["ip"=>"10.0.0.0/16", "action"=>"Reject"]
				],
			]
		],
		"homepage"=> ['t'=>"page", 'v'=>""],
	];

	

}else{
	$settings = $app['settings'];
}

$loc = [
	"./config_global_engine.php",
	"../config_global_engine.php",
	"../../config_global_engine.php",
	"/tmp/config_global_engine.php",
];

$engined = "";
$enginep = "";
$default_app = false;
foreach( $loc as $i=>$j ){
	if( file_exists($j) ){
		$enginep = $j;
		$engined = file_get_contents($j);
		if( preg_match("/". $config_param1 . "/", $engined) ){
			$default_app = true;
		}
		break;
	}
}

if( $_POST['action'] == "app_settings_stop_job" ){
	$res = $mongodb_con->update_one( $db_prefix . "_apps", [
		"_id"=>$config_param1
	], [
		'$unset'=>['settings.tasks.run'=>true]
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == "app_settings_start_job" ){

	if( !isset($_POST['env']) ){
		json_response("fail", "Need Environment");
	}else if( !is_array($_POST['env']) ){
		json_response("fail", "Need Environment");
	}else if( !isset($_POST['env']['d']) && !isset($_POST['env']['u']) && !isset($_POST['env']['t']) ){
		json_response("fail", "Incorrect Environment Details");
	}
	$success = false;
	$apiress = [];
	//print_r( $test_environments );
	$env = $_POST['env'];

	if( $env['t'] == "custom" ){
		$url = "http://" . $env['d'] . $env['e'] . "_api_system/tasks/";
	}else if( $env['t'] == "cloud" ){
		$url = $env['u'] . "_api_system/tasks/";
	}else if( $env['t'] == "cloud-alias" ){
		json_response("fail", "Environment incorrect");
	}
	//echo $url . "\n";
	if( !$akey ){
		json_response("fail", "AccessKey not initialized");
	}

	$apires = curl_post($url, [
		 "action"=>"start_taskscheduler",
		 "app_id"=>$config_param1
	], [
		'Content-type: application/json', 
		"Access-Key: ". $akey 
	]);
	if( $apires['status'] == 200 ){
		$data = json_decode($apires['body'],true);
		if( !$data ){
			json_response(['status'=>"fail", "error"=>$apires['body']]);exit;
		}
		if( !isset($data['status']) ){
			json_response(['status'=>"fail", "error"=>"incorrect response from api"]);exit;
		}
		if( $data['status'] == "success" ){
			$success = true;
			$res = $mongodb_con->update_one( $db_prefix . "_apps", [
				"_id"=>$config_param1
			],[
				'settings.tasks'=>[
					'run'=>true,
					'env'=>$env
				],
			]);
			$res['apires'] = $apires;
			json_response($res);
		}else{
			json_response($data);exit;
		}
	}else{
		json_response(['status'=>"fail", "error"=>"Error from system api", "apires"=>$apires]);
	}

	exit;
}

if( $_POST['action'] == 'settings_load_tasks_log' ){
	$cond = [];
	if( $_POST['last'] ){
		if( preg_match("/^[a-f0-9]{24}$/i", $_POST['last']) ){
			$cond['_id'] = ['$lt'=>$_POST['last']];
		}else{
			json_response("fail", "Incorrect _id");
		}
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_zlog_tasks_". $config_param1, $cond, [
		'sort'=>['_id'=>-1], 
		'limit'=>100,
		'projection'=>['ip'=>false],
		'maxTimeMS'=>10000,
	]);
	//print_r( $res );
	json_response($res);
	exit;
}


if( $_POST['action'] == 'settings_load_background_job_log' ){
	$cond = [];
	if( $_POST['last'] ){
		if( preg_match("/^[a-f0-9]{24}$/i", $_POST['last']) ){
			$cond['_id'] = ['$lt'=>$_POST['last']];
		}else{
			json_response("fail", "Incorrect _id");
		}
	}
	$res = $mongodb_con->find( $background_jobs[ $_POST['logtype'] ]['table'], $cond, [
		'sort'=>['_id'=>-1], 
		'limit'=>100,
		'maxTimeMS'=>10000,
	]);
	//print_r( $res );
	json_response($res);
	exit;
}

if( $_POST['action'] == 'app_settings_save_credentials' ){

	if( !isset($_POST['cred_id']) ){
		json_response("fail", "Cred ID missing");
	}
	if( !preg_match("/^([a-f0-9]{24}|new)$/i", $_POST['cred_id']) ){
		json_response("fail", "Cred ID Incorrect");
	}
	if( !isset($_POST['data']) ){
		json_response("fail", "Data Missing 1");
	}
	if( !isset($_POST['data']['name']) || !isset($_POST['data']['des']) || !isset($_POST['data']['type']) || !isset($_POST['data']['environment']) || !isset($_POST['data']['values']) ){
		json_response("fail", "Data Missing 2");
	}
	if( !preg_match("/^[a-z0-9\.\-\_]{2,50}$/i", $_POST['data']['name']) ){
		json_response("fail", "Data Missing 22");
	}
	if( !preg_match("/^[a-z0-9\.\-\_\,\(\)\!\ ]{5,100}$/i", $_POST['data']['des']) ){
		json_response("fail", "Data Missing 3 ");
	}
	if( !isset($config_settings_credential_types[ $_POST['data']['type'] ]) ){
		json_response("fail", "Template not implemented");
	}
	if( !in_array($_POST['data']['environment'], ["prod", "stage1", "stage2", "dev1", "dev2"]) ){
		json_response("fail", "Envronment not implemented");
	}
	if( !is_array($_POST['data']['values']) ){
		json_response("fail", "Values incorrect");
	}

	$cred = [];
	foreach( $config_settings_credential_types[ $_POST['data']['type'] ] as $fd=>$fv ){
		if( !isset($_POST['data']['values'][ $fd ]) ){
			json_response("fail", "Field: " . $fv['label'] . " Missing");
		}
		if( preg_match("/^Encrypted\:/", $_POST['data']['values'][ $fd ]['value']) ){
			if( !preg_match("/^Encrypted\:[a-zA-Z0-9\-\_\=\+\;\:\,\.\/\?\!\@\%\*\(\)]+/", $_POST['data']['values'][ $fd ]['value']) ){
				json_response("fail", "Field: " . $fv['label'] . " Encrypted cipher corrupted");
			}
		}else if( isset($fv['regexp']) ){
			if( !preg_match($fv['regexp'], $_POST['data']['values'][ $fd ]['value']) ){
				json_response("fail", "Field: " . $fv['label'] . " Value not accepted");
			}
		}
		if( isset($fv['encrypt']) && $fv['encrypt'] ){
			if( preg_match("/^Encrypted\:/", $_POST['data']['values'][ $fd ]['value']) ){
				$cred[ $fd ] = $_POST['data']['values'][ $fd ]['value'];
			}else{
				$cred[ $fd ] = "Encrypted:".pass_encrypt_static( $_POST['data']['values'][ $fd ]['value'], $config_param1 . "abcdef" );
			}
		}else{
			$cred[ $fd ] = $_POST['data']['values'][ $fd ]['value'];
		}
	}

	if( $_POST['cred_id'] != "new" ){
		$cred_id = $_POST['cred_id'];
		if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['cred_id']) ){
			json_response("fail", "Incorrect ID");
		}
		if( isset($app['creds']) ){
			if( !isset($app['creds'][ $cred_id ]) ){
				json_response("fail", "Cred Record not found");
			}
		}else{
			json_response("fail", "Cred Record not found");
		}
	}else{
		$cred_id = $mongodb_con->generate_id();
	}

	if( isset($app['creds']) ){
		if( sizeof($app['creds']) > 10 ){
			json_response("fail", "Limit of 10 credentials per app reached.");
		}
	}

	$d = [
		"creds.". $cred_id => [
			"name"=> $_POST['data']['name'],
			"des"=> $_POST['data']['des'],
			"type"=> $_POST['data']['type'],
			"environment"=> $_POST['data']['environment'],
			"values"=> $cred,
			"date"=>date("Y-m-d H:i:s")
		]
	];
	//print_r( $d );exit;
	$res = $mongodb_con->update_one( $db_prefix . "_apps", ["_id"=>$config_param1], $d);
	//print_r( $res );
	event_log( "system", "app_update_credential", [
		"app_id"=>$config_param1,
		"cred_id"=>$cred_id
	]);
	$res['values'] = $cred;
	json_response($res);
	exit;
}
if( $_POST['action'] == 'app_settings_delete_credentials' ){
	if( !isset($_POST['cred_id']) ){
		json_response("fail", "Cred ID missing");
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['cred_id']) ){
		json_response("fail", "Cred ID Incorrect");
	}
	$cred_id = $_POST['cred_id'];
	$res = $mongodb_con->update_one( $db_prefix . "_apps", ["_id"=>$config_param1], [
		'$unset'=>['creds.'. $cred_id=>1]
	]);
	event_log( "system", "app_delete_credential", [
		"app_id"=>$config_param1,
		"cred_id"=>$cred_id
	]);
	json_response($res);
	exit;
}

$background_jobs = [];
$graph_res = $mongodb_con->find( $db_prefix . "_graph_dbs", ["app_id"=>$config_param1], ['sort'=>['name'=>1] ] );
foreach( $graph_res['data'] as $i=>$j ){
	$background_jobs["graph.".$j['_id']] = [
		"name"=> "Objects Database: " . $j['name'],
		"table"=> $db_prefix . "_zlog_graph_" . $j['_id'],
		"run"=>isset($j['run'])?true:false,
		"workers"=>isset($j['workers'])?sizeof($j['workers']):0,
	];
}
$queue_res = $mongodb_con->find( $db_prefix . "_queues", ["app_id"=>$config_param1], ['sort'=>['topic'=>1] ] );
foreach( $queue_res['data'] as $i=>$j ){
	$background_jobs["queue".$j['_id']] = [
		"name"=>"Task Queue: " . $j['topic'],
		"run"=>$j['run']?true:false,
		"table"=> $db_prefix . "_zlog_queue_" . $j['_id'],
		"workers"=>isset($j['workers'])?sizeof($j['workers']):0,
	];
}

$daemon_run_status = false;
$daemon_run_last = 0;
if( isset($settings['tasks']['workers']) ){
	foreach( $settings['tasks']['workers'] as $i=>$j ){
		if( $daemon_run_last < $j['time'] ){
			$daemon_run_last = $j['time'];
		}
		if( time()- $j['time'] < 30 ){
			$daemon_run_status = true;
		}
	}
}
if( $daemon_run_last == 0 ){
	$daemon_run_last = "Never";
}else{
	$d = (time()-$daemon_run_last);
	if( $d < 60 ){
		$daemon_run_last = $d . " seconds ago ";
	}else if( $d < 3600 ){
		$daemon_run_last = round($d/60) . " minutes ago ";
	}else{
		$daemon_run_last = round($d/3600) . " hours ago ";
	}
}
$settings['daemon_run_last'] = $daemon_run_last;
$settings['daemon_run_status'] = $daemon_run_status;


