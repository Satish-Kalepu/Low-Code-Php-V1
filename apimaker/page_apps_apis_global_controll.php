<?php

function input_factors_to_values($v,$test_val){
	$vv = [];
	foreach( $v as $k=>$val ){
		if( $val['t'] == "T" ){
			$vv[$k] = ($test_val[$k]['v']?(string)$test_val[$k]['v']:"");
		}else if( $val['t'] == "N" ){
			$vv[$k] = ($test_val[$k]['v']?(int)$test_val[$k]['v']:0);
		}else if( $val['t'] == "D" ){
			$vv[$k] = ($test_val[$k]['v']?(string)$test_val[$k]['v']:"");
		}else if( $val['t'] == "DT" ){
			$vv[$k] = ($test_val[$k]['v']?(string)$test_val[$k]['v']:"");
		}else if( $val['t'] == "TS" ){
			$vv[$k] = ($test_val[$k]['v']?(string)$test_val[$k]['v']:"");
		}else if( $val['t'] == "L" ){
			$vvv = [];
			foreach( $val['v'] as $li=>$lv ){
				$vvv[] = input_factors_to_values( $lv['v'] , $test_val[$k][$li]);
			}
			$vv[$k] = $vvv;
		}else if( $val['t'] == "O" ){
			$vv[$k] = input_factors_to_values( $val['v'] , $test_val[$k][$li]);
		}else if( $val['t'] == "B" ){
			if($val['v'] == true) {
				$vv[$k] = true;
			}else{
				$vv[$k] = false;
			}
		}else if( $val['t'] == "NL" ){
			$vv[$k] = null;
		}
	}
	return $vv;
}

function schema_to_values( $v  ){
	$vv = [];
	foreach( $v as $k=>$val ){
		if( $val['type'] == "text" ){
			$vv[$k] = "text";
		}else if( $val['type'] == "number" ){
			$vv[$k] = 0;
		}else if( $val['type'] == "list" ){
			$vv[$k] = [schema_to_values($val['sub'])];
		}else if( $val['type'] == "dict" ){
			$vv[$k] = schema_to_values($val['sub']);
		}else if( $val['type'] == "boolean" ){
			$vv[$k] = true;
		}else{
			$vv[$k] = "string";
		}
	}
	return $vv;
}

function build_postman_schema( $v , $url ) {
	$req_json_data = array();
	$data_postman = array();
	
	if(isset($v['inputs']) && gettype($v['inputs']) == 'string') {
		$req_json_data = json_decode($v['inputs'],true);
	}else if(isset($v['inputs']) && count($v['inputs']) > 0 && gettype($v['inputs']) == 'array') {
		$req_json_data = input_factors_to_values($v['inputs'],$v['testing_data']);	
	}else {
		$req_json_data = [];
	}

	$req_data = array();
	$req_data['mode'] = "raw";
	$req_data['raw'] = json_encode($req_json_data,JSON_PRETTY_PRINT);
	$req_data['options']['raw']['language'] = "json";

	/*Post body input fields ends*/

	$req_url = array();
	$que_data = array();
	$req_url['raw'] = $url.$v['name'];

	$url_components = parse_url($req_url['raw']);

	if($url_components['query'] != "") {
		parse_str($url_components['query'], $query_params);

		foreach ($query_params as $key => $value) {
			$que_data[] = ['key' => $key, 'value' => $value];
		}
	}

	$req_url['protocol'] = $url_components['scheme'];;
	$req_url['host'] = $url_components['host'];
	if($url_components['port'] != "") {
		$req_url['port'] = $url_components['port'];
	}

	if($url_components['path'] != "" && $url_components['path'] != "/") {
		$path = $url_components['path'];
	}else {
		$path = $url_components['path'].$v['name'];
	}
	
	$path_array = explode('/', trim($path, '/'));
	$req_url['path'] = $path_array;

	if(count($que_data) > 0) {
		$req_url['query'] = $que_data;
	}
	
	$data_postman['name'] = $v['name'];
	$data_postman['request']['method'] = $v['method'];
	$data_postman['request']['header'] = [];
	if($data_postman['request']['method'] == "POST") {
		$data_postman['request']['body'] = $req_data;
	}
	$data_postman['request']['url'] = $req_url;
	$data_postman['response'] = [];

	return $data_postman;
}

function get_global_api_data($app_id = "") {
	global $mongodb_con,$config_global_apimaker;
	$vvv = [];
	$vvv['auth_apis'][] = [
		"_id"=>"10001",
		"path" => "_api/auth/generate_access_token",
		"name"=>"generate_access_token",
		"des"=>"Generate session token with admin token",
		'input-method'=>"POST",
		"vpost"=>json_encode([
			'action'=>"generate_access_token",
			'access_key'=>"",
			'expire_minutes'=>2,
			'client_ip'=>'192.168.1.1/32'
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
			"action"=>"generate_access_token",
			"access_key": "",
			"expire_minutes": 2 //optional
			"client_ip": "192.168.1.1/32" //optional
		}',
	];
	$vvv['auth_apis'][] = [
		"_id"=>"10005",
		"path" => "_api/auth/assume_session_key",
		"name"=>"assume_session_key",
		"des"=>"Create session token using Role",
		'input-method'=>"POST",
		"vpost"=>json_encode([
			'action'=>"assume_session_key",
			'role_id'=>"",
			'expire_type'=>"In",
			'expire_minutes'=>2,
			'expire_at'=>"2025-12-12 10:10:10",
			'client_ip'=>'192.168.1.1/32',
			'max_hits'=>10,
			'hits_per_minute'=>2
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
			"action"=>"assume_session_key",
			"role_id": "",
			"expire_type"=>"In", //In,At
			"expire_minutes"=>2,
			"expire_at"=>"2025-12-12 10:10:10",
			"client_ip": "192.168.1.1/32" //optional
			"max_hits"=>10,
			"hits_per_minute"=>2
		}',
	];
	$vvv['auth_apis'][] = [
		"_id"=>"10002",
		"path" => "_api/auth/user_auth",
		"name"=>"user_auth",
		"des"=>"Generate session token with user credentials",
		'input-method'=>"POST",
		"vpost"=>json_encode([
			"action"=>"user_auth",
			'username'=>"",
			'password'=>"",
			'expire_minutes'=>2 
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
			"action"=>"user_auth",
			"username": "",
			"password": "",
			"expire_minutes": 2 //optional
		}',
	];
	$vvv['auth_apis'][] = [
		"_id"=>"10003",
		"path" => "_api/auth/user_auth_captcha",
		"name"=>"user_auth_captcha",
		"des"=>"Generate session token with user credentials",
		'input-method'=>"POST",
		"vpost"=>json_encode([
			"action"=>"user_auth_captcha",
			'username'=>"",
			'password'=>"",
			'captcha'=>"",
			'code'=>"",
			'expire_minutes'=>2 
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
			"action"=>"user_auth_captcha",
			"username": "",
			"password": "",
			"captcha": "",
			"code": "",
			"expire_minutes": 2 //optional
		}',
	];
	$vvv['auth_apis'][] = [
		"_id"=>"10004",
		"path" => "_api/auth/verify_session_key",
		"name"=>"verify_session_key",
		"des"=>"Verify session key validity",
		'input-method'=>"POST",
		"vpost"=>json_encode([
			"action"=>"verify_session_key",
			'session_key'=>"",
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
			"action"=>"verify_session_key",
			"session_key": "",
		}',
	];

	$vvv['captcha'][] = [
		"_id"=>"10101",
		"path" => "_api/captcha/get",
		"name"=>"get",
		"des"=>"Generate Captcha",
		'input-method'=>"POST",
		"vpost"=>'{"action": "captcha_get", "ok":"ok"}',
	];

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_tables_dynamic", [
		'app_id'=>$app_id
	],[
		'sort'=>['table'=>1],
		'limit'=>200,
	]);
	foreach( $res['data'] as $i=>$j ){
		$schema = schema_to_values( $j['schema']['default']['fields'] );
		$schema2 = $schema;
		unset($schema2['_id']);
		$j['getSchema'] = [
			"action"=> "getSchema"
		];
		$j['findMany'] = [
			"action"=> "findMany",
			"query"=> [
				"field"=> "value"
			],
			"options"=>[
				"sort"=>[ "_id"=> 1 ],
				"limit"=>100
			]
		];
		$j['findOne'] = [
			"action"=> "findOne",
			"query"=> [
				"field"=> "value"
			]
		];
		$j['insertOne'] = [
			"action"=> "insertOne",
			"data"=>$schema,
		];
		$j['insertMany'] = [
			"action"=> "insertMany",
			"data"=>[
				$schema,
				$schema,
			],
		];
		$j['updateOne'] = [
			"action"=> "updateOne",
			"query"=> [
				"field"=> "value"
			],
			"update"=>[
				'$set'=>$schema2,
				'$unset'=>['field'=>true],
				'$inc'=>['field'=>1]
			],
			"options"=>[
				"upsert"=>false,
			]
		];
		$j['updateMany'] = [
			"action"=> "updateMany",
			"query"=> [
				"field"=> "value"
			],
			"update"=>[
				'$set'=>$schema2
			],
			"options"=>[
				"sort"=>[
					"_id"=>1,
				],
				"limit"=>10
			]
		];
		$j['deleteOne'] = [
			"action"=> "deleteOne",
			"query"=> [
				"field"=> "value"
			]
		];
		$j['deleteMany'] = [
			"action"=> "deleteMany",
			"query"=> [
				"field"=> "value"
			],
			"options"=>[
				"sort"=>[
					"_id"=>1,
				],
				"limit"=>10
			]
		];
		unset($j['schema']);
		$j['show'] = "";
		$j["path"] = "_api/tables_dynamic/".$j['_id'];
		$vvv['tables_dynamic'][] = $j;
	}

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_databases", [
		'app_id'=>$app_id
	],[
		'sort'=>['des'=>1],
		'limit'=>200,
		'projection'=>['details'=>false, 'm_i'=>false, 'user_id'=>false]
	]);
	foreach( $res['data'] as $i=>$j ){

		$res2 = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_tables", [
			'app_id'=>$app_id,
			"db_id"=>$j['_id']
		],[
			'sort'=>['des'=>1],
			'limit'=>200,
		]);

		foreach( $res2['data'] as $ii=>$jj ){
			$schema = schema_to_values( $res2['data'][$ii]['schema']['default']['fields'] );
			$schema2 = $schema;
			unset($schema2['_id']);
			$primary_key = "_id";
			if( $j['engine'] == "MongoDb" ){
				$primary_key = "_id";
				$primary_key_type = "text";
			}else if( $j['engine'] == "MySql" ){
				if( isset($jj['keys']) ){
					$primary_keys = $jj['keys']['PRIMARY']['keys'];
					$primary_key = $primary_keys[0]['name'];
					$primary_key_type = $primary_keys[0]['type'];
				}
			}else{
				$primary_key = "_id";
				$primary_key_type = "text";
			}
			$res2['data'][$ii]['getSchema'] = [
				"action"=> "getSchema"
			];
			$res2['data'][$ii]['findMany'] = [
				"action"=> "findMany",
				"query"=> [
					"field"=> $primary_key_type
				],
				"options"=>[
					"sort"=>[ $primary_key=> 1 ],
					"limit"=>100
				]
			];
			$res2['data'][$ii]['findOne'] = [
				"action"=> "findOne",
				"query"=> [
					$primary_key=> $primary_key_type
				]
			];
			$res2['data'][$ii]['insertOne'] = [
				"action"=> "insertOne",
				"data"=>$schema,
			];
			$res2['data'][$ii]['insertMany'] = [
				"action"=> "insertMany",
				"data"=>[
					$schema,
					$schema,
				],
			];
			if( $j['engine'] == "MongoDb" ){
				$res2['data'][$ii]['updateOne'] = [
					"action"=> "updateOne",
					"query"=> [
						$primary_key=> $primary_key_type
					],
					"update"=>['$set'=>$schema2],
					"options"=>[
						"upsert"=>false,
					]
				];
				$res2['data'][$ii]['updateMany'] = [
					"action"=> "updateMany",
					"query"=> [
						"field"=> "value"
					],
					"update"=>['$set'=>$schema2],
					"options"=>[
						"sort"=>[
							$primary_key=>1,
						],
						"limit"=>10
					]
				];
			}else{
				$res2['data'][$ii]['updateOne'] = [
					"action"=> "updateOne",
					"query"=> [
						$primary_key=> $primary_key_type
					],
					"update"=>$schema2,
					"options"=>[
						"upsert"=>false,
					]
				];
				$res2['data'][$ii]['updateMany'] = [
					"action"=> "updateMany",
					"query"=> [
						"field"=> "value"
					],
					"update"=>$schema2,
					"options"=>[
						"sort"=>[
							$primary_key=>1,
						],
						"limit"=>10
					]
				];
			}
			$res2['data'][$ii]['deleteOne'] = [
				"action"=> "deleteOne",
				"query"=> [
					$primary_key=> $primary_key_type
				]
			];
			$res2['data'][$ii]['deleteMany'] = [
				"action"=> "deleteMany",
				"query"=> [
					"field"=> "value"
				],
				"options"=>[
					"sort"=>[
						$primary_key=>1,
					],
					"limit"=>10
				]
			];
			unset($res2['data'][$ii]['schema']);
			$res2['data'][$ii]['show'] = "";
			$res2['data'][$ii]['path'] = "_api/tables/".$jj['_id'];
		}
		$vvv['databases'][ $j['_id'] ] = [ 'db'=>$j, 'tables'=> $res2['data'], "show"=> "" ];
	}
	
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults", [
		'app_id'=>$app_id
	],[
		'sort'=>['des'=>1],
		'limit'=>200,
	]);
	foreach( $res['data'] as $i=>$j ){
		unset($j['details']);
		$j['apis'] = [];

		$j['apis']['list_files'] = [
			"_id"=>"f0010",
			"name"=>"list_files", "des"=>"List files from cloud storage",
			"input-method"=>"POST",
			"path" => "_api/storage_vaults/" . $j['_id'],
			"payload" => [
				"action"=> "list_files",
				"path"=> "/",
				"options"=>[
					"limit"=>10
				]
			],
			"content-type" => "application/json",
			"response-type"=>"application/json",
			"response-body"=>[
				"status"=>"success", 
				"data"=> [
					[
						"_id"=>"file id",
						"name"=>"name",
						"path"=>"/",
					]
				],
				"error"=>""
			]
		];
		$j['apis']['get_file'] = [
			"_id"=>"f0020",
			"name"=>"get_file", "des"=>"Get file as base64 string",
			"input-method"=>"POST",
			"path" => "_api/storage_vaults/" . $j['_id'],
			"payload" => [
				"action"=> "get_file",
				"filename"=> "/path/filename",
			],
			"content-type" => "application/json",
			"response-type"=>"application/json",
			"response-body"=>[
				"status"=>"success", 
				"data"=> "Base64 String",
				"error"=>""
			]
		];
		$j['apis']['get_raw_file'] = [
			"_id"=>"f0021",
			"name"=>"get_raw_file", "des"=>"Get file as binary",
			"input-method"=>"POST",
			"path" => "_api/storage_vaults/" . $j['_id'],
			"payload" => [
				"action"=> "get_raw_file",
				"filename"=> "/path/filename",
			],
			"content-type" => "application/json",
			"response-type"=>"application/json",
			"response-body"=>"BinaryData",
		];
		$j['apis']['put_file'] = [
			"_id"=>"f0030",
			"name"=>"put_file", "des"=>"Upload a file",
			"input-method"=>"POST",
			"path" => "_api/storage_vaults/" . $j['_id'],
			"payload" => [
				"action"=> "put_file",
				"filename"=> "/path/filename",
				"file"=>"Binary Data"
			],
			"formdata" => [
				"action"=> ["type"=>"text", "value"=>"put_file"],
				"filename"=> ["type"=>"text", "value"=>"/path/filename"],
				"file"=> ["type"=>"file", "value"=>""],
			],
			"content-type" => "multipart/form-data",
			"response-type"=>"application/json",
			"response-body"=>[
				"status"=>"success", 
				"error"=>""
			]
		];
		$j['apis']['delete_file'] = [
			"_id"=>"f0040",
			"name"=>"delete_file", "des"=>"Delete a file",
			"input-method"=>"POST",
			"path" => "_api/storage_vaults/" . $j['_id'],
			"payload" => [
				"action"=> "delete_file",
				"filename"=> "/path/filename",
			],
			"content-type" => "application/json",
			"response-type"=>"application/json",
			"response-body"=> [
				"status"=>"success", 
				"error"=>""
			]
		];
		$j['show'] = "";
		$vvv['storage'][] = $j;
	}

	$d = [
		"_id"=>"f0010",
		"name"=>"list_files", "des"=>"List Files",
		"input-method"=>"POST",
		"path" => "_api/files/internal",
		"payload" => [
			"action"=>"list_files",
			"path"=> "/",
			"options"=>[
				"limit"=>10
			]
		],
		"content-type" => "application/json",
		"response-type"=>"application/json",
		"response-body"=>[
			"status"=>"success", 
			"data"=> [
				[
					"_id"=>"file id",
					"name"=>"name",
					"path"=>"/",
				]
			],
			"error"=>""
		]
	];
	$vvv['files'][] = $d;
	$d = [
		"_id"=>"f0010",
		"name"=>"get_file", "des"=>"Get file as base64 string",
		"input-method"=>"POST",
		"path" => "_api/files/internal",
		"payload" => [
			"action"=> "get_file",
			"filename"=> "/path/filename",
		],
		"content-type" => "application/json",
		"response-type"=>"application/json",
		"response-body"=>[
			"status"=>"success", 
			"data"=> "Base64 String",
			"error"=>""
		]
	];
	$vvv['files'][] = $d;
	$d = [
		"_id"=>"f0010",
		"name"=>"get_raw_file", "des"=>"Get file as binary",
		"input-method"=>"POST",
		"path" => "_api/files/internal",
		"payload" => [
			"action"=> "get_raw_file",
			"filename"=> "/path/filename",
		],
		"content-type" => "application/json",
		"response-type"=>"application/json",
		"response-body"=>"BinaryData",
	];
	$vvv['files'][] = $d;
	$d = [
		"_id"=>"f0010",
		"name"=>"get_file_by_id", "des"=>"Get file as base64 string",
		"input-method"=>"POST",
		"path" => "_api/files/internal",
		"payload" => [
			"action"=> "get_file_by_id",
			"file_id"=> "661c948c82434413160042c3",
		],
		"content-type" => "application/json",
		"response-type"=>"application/json",
		"response-body"=>[
			"status"=>"success", 
			"data"=> "Base64 String",
			"error"=>""
		]
	];
	$vvv['files'][] = $d;
	$d = [
		"_id"=>"f0010",
		"name"=>"get_raw_file_by_id", "des"=>"Get file as binary",
		"input-method"=>"POST",
		"path" => "_api/files/internal",
		"payload" => [
			"action"=> "get_raw_file_by_id",
			"file_id"=> "661c948c82434413160042c3",
		],
		"content-type" => "application/json",
		"response-type"=>"application/json",
		"response-body"=>"BinaryData",
	];
	$vvv['files'][] = $d;
	$d = [
		"_id"=>"f0010",
		"name"=>"put_file", "des"=>"Upload a file",
		"input-method"=>"POST",
		"path" => "_api/files/internal",
		"payload" => [
			"action"=> "put_file",
			"filename"=> "/path/filename",
			"file"=>"Binary Data",
			"replace"=>false,
		],
		"formdata" => [
			"action"=> ["type"=>"text", "value"=>"put_file"],
			"filename"=> ["type"=>"text", "value"=>"/path/filename"],
			"file"=> ["type"=>"file", "value"=>""],
			"replace"=> ["type"=>"boolean", "value"=>false],
		],
		"content-type" => "multipart/form-data",
		"response-type"=>"application/json",
		"response-body"=>[
			"status"=>"success", 
			"error"=>""
		]
	];
	$vvv['files'][] = $d;
	$d = [
		"_id"=>"f0010",
		"name"=>"delete_file", "des"=>"Delete a file",
		"input-method"=>"POST",
		"path" => "_api/files/internal",
		"payload" => [
			"action"=> "delete_file",
			"file_id"=> "661c948c82434413160042c3",
		],
		"content-type" => "application/json",
		"response-type"=>"application/json",
		"response-body"=> [
			"status"=>"success", 
			"error"=>""
		]
	];
	$vvv['files'][] = $d;

	return $vvv;
}

if( $_POST['action'] == "get_global_apis" ){
	$t = validate_token("get_global_apis.". $config_param1, $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	$apis = [
		"apis"=>[],
		"auth_apis"=>[],
		"captcha"=>[],
		"tables_dynamic"=>[],
		"databases"=>[],
		"files"=>[],
		"storage"=>[],
	];
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		'app_id'=>$config_param1
	],[
		'sort'=>['name'=>1],
		'limit'=>200,
	]);

	foreach( $res['data'] as $i=>$j ){

		$res2 = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			'_id'=>$j['version_id'],
		],[
			'projection'=>[
				'engine.stages'=>false
			]
		]);

		if( $j['input-method'] == "GET" ){
			$fr = [];
			foreach( $res2['data']['engine']['input_factors'] as $fi=>$fd){
				$t = $fd['t'];
				if( $t == "N" || $t == "TS" ){ $t = "number";}
				if( $t == "T"||$t == "TT"||$t == "D"||$t == "DT" ){ $t = "text";}
				if( $t == "B" ){ $t = "boolean";}
				$fr[ $fi ] = ["type"=>$t, "value"=>$fd['v']];
			}
			$j['formdata'] = $fr;
		}else if( isset( $res2['data']['engine']['input_factors'] ) ){
			$j['vpost'] = json_encode(input_factors_to_values($res2['data']['engine']['input_factors'],$res2['data']['test']['factors']['v']),JSON_PRETTY_PRINT);
		}else{
			$j['vpost'] = '{}';
		}
		$apis['apis'][] = $j;
	}

	$predefined_apis = get_global_api_data($config_param1);

	foreach($apis as $i => $j) {
		if(in_array($i,array_keys($predefined_apis))) {
			$apis[$i] = $predefined_apis[$i];
		}else {
			$apis[$i] = $j;
		}
	}

	json_response([
		'status'=>"success", 
		"apis"=>$apis,
		"data" => $data
	]);
	exit;
}


if( $_POST['action'] == "generate_access_token" ){

	if( $_POST['type'] == "tables_dynamic" ){
		$service = "tables";
		$thing = [
			"_id"=>"table_dynamic:".$_POST['thing_id'],
			"thing"=>"internal:something"
		];
	}else if( $_POST['type'] == "tables" ){
		$service = "tables";
		$thing = [
			"_id"=>"table:".$_POST['thing_id'],
			"thing"=>"external:something"
		];
	}else if( $_POST['type'] == "apis" ){
		$service = "apis";
		$thing = [
			"_id"=>"api:".$_POST['thing_id'],
			"thing"=>"api:something"
		];
	}else if( $_POST['type'] == "auth_apis" ){
		$service = "apis";
		$thing = [
			"_id"=>"auth_api:".$_POST['thing_id'],
			"thing"=>"auth_api:something"
		];
	}else if( $_POST['type'] == "captcha" ){
		$service = "captcha";
		$thing = [
			"_id"=>"captcha:".$_POST['thing_id'],
			"thing"=>"captcha:something"
		];
	}else if( $_POST['type'] == "files" ){
		$service = "files";
		$thing = [
			"_id"=>"file:".$_POST['thing_id'],
			"thing"=>"file:something"
		];
	}else if( $_POST['type'] == "storage" ){
		$service = "storage_vaults";
		$thing = [
			"_id"=>"storage_vault:".$_POST['thing_id'],
			"thing"=>"storage_vault:something"
		];
	}else{
		json_response("fail", "unknown type");
	}

	$expire = time()+(5*60);
	$data = [
		'app_id'=>$config_param1,
		"t"=>"uk",
		"active"=>'y',
		"expire"=>$expire,
		"expiret"=> new \MongoDB\BSON\UTCDateTime($expire),
		"policies"=>[
			[
				"service"=> $service,
				"actions"=> ["*"],
				"things"=> [$thing],
			],
		],
		"ips"=>[$_SERVER['REMOTE_ADDR']."/32"],
		"ua"=>$_SERVER['HTTP_USER_AGENT'],
		"updated"=>date("Y-m-d H:i:s"),
	];

	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", $data );
	$key_id = $res['inserted_id'];
	json_response([
		"status"=>"success",
		"key"=>$key_id
	]);

}

if(isset($_POST['action']) && $_POST['action'] == "postman_export") {
	if(!isset($_POST['app_id'])) {
		json_response("fail", "Page Id should not be empty");
	}
	if( !preg_match("/^[a-f0-9]{24}$/", $_POST['app_id'] ) ){
		json_response("fail", "Error In Page Id");
	}

	if($_POST['app_id'] != $config_param1) {
		json_response("fail", "Error in page id");
	}	

	if(!isset($_POST['from']) || $_POST['from'] == "") {
		json_response("fail","From event should not be empty");
	}

	$from_event_list = ['apis','auth','captcha','internal','external','files','storage'];

	if(!in_array($_POST['from'], $from_event_list)) {
		json_response("fail",'Fail to export due to from event');
	}

	$api_export_details = array();

	$common_api_names = array(
		"auth" => array('name' => "Authentication",'key' => 'auth_apis'),
		"captcha" => array('name' => "Captcha",'key' => 'captcha'),
		"internal" => array('name' => "Internal Tables",'key' => 'tables_dynamic'),
		"externaal" => array('name' => "External Database",'key' => 'databases'),
		"files" => array('name' => "Internal Files",'key' => 'files'),
		"storage" => array('name' => "Storage Vault",'key' => 'storage'),
	);

	$global_api_data = get_global_api_data($config_param1);

	$internal_tables_api = ['getSchema','findOne','findMany','insertOne','insertMany','updateOne', 'updateMany', 'deleteOne', 'deleteMany'];
	$gloabl_api_schema_data = array();
	foreach($global_api_data as $i => $k) {
		foreach($k as $kk => $kkk) {
			$global_api_schema = array();
			$global_api_schema['name'] = $kkk['path'];
			$global_api_schema['inputs'] = $kkk['vpost'];
			$global_api_schema['testing_data'] = [];
			$global_api_schema['method'] = $kkk['input-method'];
			$global_api_schema['api_path'] = "";
			$global_api_schema['auth'] = "None";
			$global_api_schema['input-type'] = "application/json";

			$gloabl_api_schema_data[$i]['API'][] = $global_api_schema;
		}
	}

	if($_POST['from'] == "apis" || $_POST['from'] == "universal") {
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			'app_id'=>$config_param1,
			'type' => 'api'
		],[
			'sort'=>['name'=>1],
			'limit'=>200,
		]);

		if($res['status'] == "success") {
			$api_export_data = array();
			foreach( $res['data'] as $i=>$j ){
				$api_data = [];
				$api_data['name'] = $j['name'];
				$api_data['inputs'] = ($j['engine']['input_factors']?$j['engine']['input_factors']:[]);
				$api_data['testing_data'] = ($j['test']['factors']['v']?$j['test']['factors']['v']:[]);
				$api_data['method'] = $j['input-method'];
				$api_data['api_path'] = str_replace("/", "", $j['path']);
				$api_data['auth'] = $j['auth-type'];
				$api_data['input-type'] = $j['input-type'];

				$api_export_data['data'][(str_replace("/", "", $j['path']) == ""?"API":str_replace("/", "", $j['path']))][] = $api_data;
			}
			$api_export_details["My API's"] = $api_export_data;
		}else {
			json_response("fail","No Data found with given details 1");
		}
	}else if($_POST['from'] == "auth") {
		$api_export_details[$common_api_names[$_POST['from']]['name']]['data'] = $gloabl_api_schema_data[$common_api_names[$_POST['from']]['key']];
	}else if($_POST['from'] == "captcha") {
		$api_export_details[$common_api_names[$_POST['from']]['name']]['data'] = $gloabl_api_schema_data[$common_api_names[$_POST['from']]['key']];
	}else if($_POST['from'] == "internal") {
		$api_export_details[$common_api_names[$_POST['from']]['name']]['data'] = $gloabl_api_schema_data[$common_api_names[$_POST['from']]['key']];
	}else if($_POST['from'] == "external") {
		$api_export_details[$common_api_names[$_POST['from']]['name']]['data'] = $gloabl_api_schema_data[$common_api_names[$_POST['from']]['key']];
	}else if($_POST['from'] == "files") {
		$api_export_details[$common_api_names[$_POST['from']]['name']]['data'] = $gloabl_api_schema_data[$common_api_names[$_POST['from']]['key']];
	}else if($_POST['from'] == "storage") {
		$api_export_details[$common_api_names[$_POST['from']]['name']]['data'] = $gloabl_api_schema_data[$common_api_names[$_POST['from']]['key']];
	}else if($_POST['from'] == "universal") {
		foreach($common_api_names as $ii => $iii) {
			$api_export_details[$iii['name']]['data'] = $gloabl_api_schema_data[$iii['key']];
		}
	}else {
		json_response("fail","Fail to export due to from event");
	}

	/*Postman Collection Export Code*/
	$json_data = array();

	$json_data['info'] = ['_postman_id' => "",'name' => $_POST['from'],'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json','_exporter_id' => ""];

	$doc_data = array();

	foreach($api_export_details as $k => $l) {
		$doc_data['name'] = $k;

		foreach($l['data'] as $i => $j) {
			if($i == 'API') {
				foreach($j as $l => $ll) {
					$doc_data['item'][] = build_postman_schema($ll,$_POST['engine_path'],'');
				}
			}else {
				$vv = [];
				$vv['name'] = $i;
				foreach($j as $l => $ll) {
					$vv['item'][] = build_postman_schema($ll,$_POST['engine_path'].$i."/");
				}
				$doc_data['item'][] = $vv;
			}
		}
	}

	$json_data['item'][] = $doc_data;
	$json = json_encode($json_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

	$filename = $_POST['from'].".json";

	json_response("success",["file" => $filename,'json_data' => $json]);
}