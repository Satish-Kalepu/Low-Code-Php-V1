<?php

function input_factors_to_values($v){
	//echo "input_factors_to_values\n";
	//print_r( $v );
	$vv = [];
	foreach( $v as $k=>$val ){
		if( $val['t'] == "T" ){
			$vv[$k] = "";
		}else if( $val['t'] == "N" ){
			$vv[$k] = 0;
		}else if( $val['t'] == "D" ){
			$vv[$k] = "";
		}else if( $val['t'] == "DT" ){
			$vv[$k] = "";
		}else if( $val['t'] == "TS" ){
			$vv[$k] = "";
		}else if( $val['t'] == "L" ){
			$vvv = [];
			foreach( $val['v'] as $li=>$lv ){
				$vvv[] = input_factors_to_values( $lv['v'] );
			}
			$vv[$k] = $vvv;
		}else if( $val['t'] == "O" ){
			$vv[$k] = input_factors_to_values( $val['v'] );
		}else if( $val['t'] == "B" ){
			$vv[$k] = false;
		}else if( $val['t'] == "NL" ){
			$vv[$k] = null;
		}
	}
	return $vv;
}

function schema_to_values( $v ){
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
		"objects"=>[],
		"files"=>[],
		"storage"=>[],
	];
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		'app_id'=>$config_param1
	],[
		'sort'=>['name'=>1],
		'limit'=>200,
	]);
	//print_r( $res['data'] );exit;
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
			//print_r( $res2['data']['engine']['input_factors'] );
			$j['vpost'] = json_encode(input_factors_to_values($res2['data']['engine']['input_factors']),JSON_PRETTY_PRINT);
		}else{
			$j['vpost'] = '{}';
		}
		//$j["path"] = ,
		$apis['apis'][] = $j;
	}

	$apis['auth_apis'][] = [
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
	$apis['auth_apis'][] = [
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
	$apis['auth_apis'][] = [
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
	$apis['auth_apis'][] = [
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
	$apis['auth_apis'][] = [
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
	$apis['auth_apis'][] = [
		"_id"=>"10006",
		"path" => "_api/auth/verify_user_session",
		"name"=>"verify_user_session",
		"des"=>"Verify user session id validity",
		'input-method'=>"POST",
		"vpost"=>json_encode([
			"action"=>"verify_user_session",
			'user_session_id'=>"",
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
	"action"=>"verify_user_session",
	"user_session_id": "",
}',
	];
	$apis['auth_apis'][] = [
		"_id"=>"10007",
		"path" => "_api/auth/assume_user_session_key",
		"name"=>"assume_user_session_key",
		"des"=>"Verify user session id validity",
		'input-method'=>"POST",
		"vpost"=>json_encode([
			"action"=>"assume_user_session_key",
			'user_session_id'=>"",
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
	"action"=>"assume_user_session_key",
	"user_session_id": "",
}',
	];
	$apis['auth_apis'][] = [
		"_id"=>"10009",
		"path" => "_api/auth/user_session_logout",
		"name"=>"user_session_logout",
		"des"=>"Delete user session id and respective session tokens created for the session",
		'input-method'=>"POST",
		"vpost"=>json_encode([
			"action"=>"user_session_logout",
			'user_session_id'=>"",
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
	"action"=>"user_session_logout",
	"user_session_id": "",
}',
	];

	$apis['captcha'][] = [
		"_id"=>"10101",
		"path" => "_api/captcha/get",
		"name"=>"get",
		"des"=>"Generate Captcha",
		'input-method'=>"POST",
		"vpost"=>'{"action": "captcha_get", "ok":"ok"}',
		//"vpost_help"=>'{"ok":"ok"}',
	];

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_tables_dynamic", [
		'app_id'=>$config_param1
	],[
		'sort'=>['table'=>1],
		'limit'=>200,
	]);
	foreach( $res['data'] as $i=>$j ){

		//print_r( $j['schema']['default']['fields'] );
		$schema = schema_to_values( $j['schema']['default']['fields'] );
		$schema2 = $schema;
		unset($schema2['_id']);
		//print_r( $schema );exit;
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
		$apis['tables_dynamic'][] = $j;
	}

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_databases", [
		'app_id'=>$config_param1
	],[
		'sort'=>['des'=>1],
		'limit'=>200,
		'projection'=>['details'=>false, 'm_i'=>false, 'user_id'=>false]
	]);
	//print_r( $res['data'] );exit;
	foreach( $res['data'] as $i=>$j ){

		$res2 = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_tables", [
			'app_id'=>$config_param1,
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
					//print_r( $jj['source_schema'] );exit;
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
		$apis['databases'][ $j['_id'] ] = [ 'db'=>$j, 'tables'=> $res2['data'], "show"=> "" ];
	}
	
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults", [
		'app_id'=>$config_param1
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
				"file"=>"Binary Data",
				"acl"=>"private"
			],
			"formdata" => [
				"action"=> ["type"=>"text", "value"=>"put_file"],
				"filename"=> ["type"=>"text", "value"=>"/path/filename"],
				"file"=> ["type"=>"file", "value"=>""],
				"acl"=> ["type"=>"text", "value"=>"private"]
			],
			"content-type" => "multipart/form-data",
			"response-type"=>"application/json",
			"response-body"=>[
				"status"=>"success", 
				"error"=>""
			]
		];
		$j['apis']['create_signed_url'] = [
			"_id"=>"f0031",
			"name"=>"create_signed_url", "des"=>"Download/Upload a file using signed url",
			"input-method"=>"POST",
			"path" => "_api/storage_vaults/" . $j['_id'],
			"payload" => [
				"action"=> "create_signed_url",
				"filename"=> "/path/filename",
				"acl"=>"private"
			],
			"formdata" => [
				"action"=> ["type"=>"text", "value"=>"create_signed_url"],
				"filename"=> ["type"=>"text", "value"=>"/path/filename"],
				"acl"=> ["type"=>"text", "value"=>"private"]
			],
			"content-type" =>"application/json",
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
		$apis['storage'][] = $j;
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
	$apis['files'][] = $d;
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
	$apis['files'][] = $d;
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
	$apis['files'][] = $d;
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
	$apis['files'][] = $d;
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
	$apis['files'][] = $d;
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
	$apis['files'][] = $d;
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
	$apis['files'][] = $d;

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_graph_dbs", [
		'app_id'=>$config_param1
	],[
		'sort'=>['name'=>1],
		'limit'=>200,
	]);
	foreach( $res['data'] as $i=>$j ){

		$j['apis'] = [];

		$j['apis']['listObjects'] = [
			"post"=>[
				"action"=> "listObjects",
				"sort"=>"ID", // ID, label, nodes
				"order"=>"asc",
				"from"=>"",
				"last"=>"",
				"limit"=>100,
			],
			"help"=>[
				"sort"=> "Required. ID, label, nodes",
				"order"=> "Required. asc|dsc",
				"limit"=> "Required. results size. max 500",
				"from"=> "Optional. Start keyword, respective to the sort field",
				"last"=> "Optional. Start keyword for next page of results, from and last overrides each other"
			]
		];
		$j['apis']['getObject'] = [
			"post"=>[
				"action"=> "getObject",
				"object_id"=>"T1",
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['getObjectTemplate'] = [
			"post"=>[
				"action"=> "getObjectTemplate",
				"object_id"=>"T1",
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['getObjectRecords'] = [
			"post"=>[
				"action"=> "getObjectRecords",
				"object_id"=>"T1",
				"sort"=>"_id",
				"order"=>"asc",
				"from"=>"",
				"last"=>"",
				"filter"=>[
					[
						"field"=>"", "op"=>"=", "value"=>"",
					]
				]
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['getObjectNodes'] = [
			"post"=>[
				"action"=> "getObjectNodes",
				"instance_id"=>"T1",
				"sort"=>"id", //label,id,nodes
				"order"=>"asc",
				"from"=>"",
				"last"=>"",
				"filter"=>[
					[
						"field"=>"", "op"=>"=", "value"=>"",
					]
				]
			],
			"help"=>[
				"instance_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['getObjectLibrarySettings'] = [
			"post"=>[
				"action"=> "getObjectLibrarySettings",
			],
			"help"=>[
				
			]
		];
		$j['apis']['objectCreate'] = [
			"post"=>[
				"action"=> "objectCreate",
				"instance_of"=> ["t"=>"GT", "v"=>"", "i"=>""],
				"label"=> ["t"=>"T", "v"=>""],
			],
			"help"=>[]
		];
		$j['apis']['objectCreateWithTemplate'] = [
			"post"=>[
				"action"=> "objectCreateWithTemplate",
				"thing"=>json_decode('{
			        "l": {
			            "t": "T",
			            "v": "Label"
			        },
			        "i_of": {
			            "t": "GT",
			            "i": "T1",
			            "v": "Root"
			        },
			        "i_t": {
			            "t": "T",
			            "v": "N"
			        },
			        "props": {
			            "p1": [
			                {
			                    "t": "T",
			                    "v": "sfsfsdf"
			                }
			            ]
			        },
			        "z_t": {
			            "p1": {
			                "key": "p1",
			                "name": {
			                    "t": "T",
			                    "v": "Description"
			                },
			                "type": {
			                    "t": "KV",
			                    "k": "T",
			                    "v": "text"
			                },
			                "m": {
			                    "t": "B",
			                    "v": "true"
			                }
			            },
			            "p2": {
			                "key": "p2",
			                "name": {
			                    "t": "T",
			                    "v": "xx"
			                },
			                "type": {
			                    "t": "KV",
			                    "k": "T",
			                    "v": "text"
			                },
			                "m": {
			                    "t": "B",
			                    "v": "false"
			                }
			            }
			        },
			        "z_o": [
			            "p1",
			            "p2"
			        ],
			        "z_n": 3
			    }', true)
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectLabelUpdate'] = [
			"post"=>[
				"action"=> "objectLabelUpdate",
				"object_id"=> "",
				"label"=>["t"=>"T", "v"=>""],
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectTypeUpdate'] = [
			"post"=>[
				"action"=> "objectTypeUpdate",
				"object_id"=> "",
				"type"=>["t"=>"T", "v"=>"N"],
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectAliasUpdate'] = [
			"post"=>[
				"action"=> "objectAliasUpdate",
				"object_id"=> "",
				"alias"=>[
					["t"=>"T", "v"=>"N"]
				]
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectInstanceUpdate'] = [
			"post"=>[
				"action"=> "objectInstanceUpdate",
				"object_id"=> "",
				"instance_of"=>[
					["t"=>"GT", "v"=>"", "i"=>""]
				]
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectPropertiesUpdate'] = [
			"post"=>[
				"action"=> "objectPropertiesUpdate",
				"object_id"=> "",
				"properties"=> [
					"p1"=> [["t"=>"T","v"=>""]]
				]
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectHtmlUpdate'] = [
			"post"=>[
				"action"=> "objectHtmlUpdate",
				"object_id"=> "",
				"properties"=> [
					"p1"=> [["t"=>"T","v"=>""]]
				]
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectNodesTruncate'] = [
			"post"=>[
				"action"=> "objectNodesTruncate",
				"object_id"=> "",
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectDelete'] = [
			"post"=>[
				"action"=> "objectDelete",
				"object_id"=> "",
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectConverToDataset'] = [
			"post"=>[
				"action"=> "objectConverToDataset",
				"object_id"=> "",
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectConverToNode'] = [
			"post"=>[
				"action"=> "objectConverToNode",
				"object_id"=> "",
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];

		$j['apis']['objectTemplatePropertyCreate'] = [
			"post"=>[
				"action"=> "objectTemplatePropertyCreate",
				"object_id"=> "",
				"property"=>["t"=>"T", "v"=>"N"],
				"config"=>[
					"l"=> ["t"=> "T", "v"=> "Description"],
					"t"=> ["t"=> "KV", "v"=> "Text", "k"=> "T"],
					"m"=> ["t"=> "B", "v"=> "false"]
				]
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
				"property"=>"Required. Unique property key from the template",
				"config"=>"An object defines configuration.\n\"l\" = Defines Label of the Property.\n\"t\" = Defines Data Type of the Property. Allowed Values are T,N,GT\n\"m\" = Defines mandatory flag. true/false."
			]
		];
		$j['apis']['objectTemplatePropertyUpdate'] = [
			"post"=>[
				"action"=> "objectTemplatePropertyUpdate",
				"object_id"=> "",
				"property"=>["t"=>"T", "v"=>"N"],
				"config"=>[
					"l"=> ["t"=> "T", "v"=> "Description"],
					"t"=> ["t"=> "KV", "v"=> "Text", "k"=> "T"],
					"m"=> ["t"=> "B", "v"=> "false"]
				]
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
				"property"=>"Required. Unique property key from the template",
				"config"=>"An object defines configuration.\n\"l\" = Defines Label of the Property.\n\"t\" = Defines Data Type of the Property. Allowed Values are T,N,GT\n\"m\" = Defines mandatory flag. true/false."
			]
		];
		$j['apis']['objectTemplatePropertyDelete'] = [
			"post"=>[
				"action"=> "objectTemplatePropertyDelete",
				"object_id"=> "",
				"property"=>"",
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
				"property"=>"Required. Unique property key from the template"
			]
		];
		$j['apis']['objectTemplateEnable'] = [
			"post"=>[
				"action"=> "objectTemplateEnable",
				"object_id"=> "",
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['objectTemplateOrderUpdate'] = [
			"post"=>[
				"action"=> "objectTemplateOrderUpdate",
				"object_id"=> "",
				"order"=> ["p1", "p2"],
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
				"order"=>"Required. List of strings represents existing property names in the template. All properties should be mentioned",
			]
		];
		$j['apis']['dataSetRecordCreate'] = [
			"post"=>[
				"action"=> "dataSetRecordCreate",
				"object_id"=> "",
				"record_id"=> "",
				"properties"=> [
					"p1"=> [["t"=>"T","v"=>""]]
				]
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
				"record_id"=>"Required. Unique record ID",
				"properties"=>"Required. A List pf Objects contains the Property Key and correspond list of values.\nproperty = Field name from the template\nfor value notation refer documentation",
			]
		];
		$j['apis']['dataSetRecordUpdate'] = [
			"post"=>[
				"action"=> "dataSetRecordUpdate",
				"object_id"=> "",
				"record_id"=> "",
				"properties"=> [
					"p1"=> [["t"=>"T","v"=>""]]
				],
				"method"=>"Replace"
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
				"record_id"=>"Required. Unique record ID",
				"properties"=>"Required. A List pf Objects contains the Property Key and correspond list of values.\nproperty = Field name from the template\nfor value notation refer documentation",
				"method"=>"Optional. Default Replace. \nreplace = Replace properties with given values. deletes other existing properties.\nupdate = Add/Update only the given properties. Keeps other existing properties",
			]
		];
		$j['apis']['dataSetRecordDelete'] = [
			"post"=>[
				"action"=> "dataSetRecordDelete",
				"object_id"=> "",
				"record_id"=> "",
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
				"record_id"=>"Required. Unique record ID",
			]
		];
		$j['apis']['dataSetTruncate'] = [
			"post"=>[
				"action"=> "dataSetTruncate",
				"object_id"=> "",
			],
			"help"=>[
				"object_id"=>"Required. Unique node ID or Key",
			]
		];
		$j['apis']['keywordSearch'] = [
			"post"=>[
				"action"=> "keywordSearch",
				"keyword"=> "value"
			],
			"help"=>[
				"keyword"=>"Required. a keyword text to start search from. max keys returned are 500. can be used for AutoSuggest Actions"
			]
		];
		$j["path"] = "_api/objects/".$j['_id'];
		$apis['objects'][] = $j;
	}

	json_response([
		'status'=>"success", 
		"apis"=>$apis
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
	}else if( $_POST['type'] == "objects" ){
		$service = "objects";
		$thing = [
			"_id"=>"object:".$_POST['thing_id'],
			"thing"=>"object:something"
		];
	}else{
		json_response("fail", "unknown type");
	}

	$expire = date("Y-m-d H:i:s", time()+(5*60) );
	date_default_timezone_set("UTC");
	$expire_utc = date("Y-m-d H:i:s", time()+(5*60) );
	$expiret = new \MongoDB\BSON\UTCDateTime( time()+(5*60) );
	date_default_timezone_set( $config_global_apimaker['timezone'] );
	$data = [
		'app_id'=>$config_param1,
		"t"=>"uk",
		"active"=>'y',
		"expire"=>$expire,
		"expire_utc"=>$expire_utc,
		"expiret"=> $expiret,
		"policies"=>[
			[
				"service"=> $service,
				"actions"=> ["*"],
				"things"=> [$thing],
				"records"=> ["*"],
			],
		],
		"ips"=>[$_SERVER['REMOTE_ADDR']."/32"],
		"ua"=>$_SERVER['HTTP_USER_AGENT'],
		"updated"=>date("Y-m-d H:i:s"),
	];

	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_user_keys", $data );
	$key_id = $res['inserted_id'];

		event_log( "system", "app_generate_token", [
			"app_id"=>$config_param1, 
			"key_id"=>$key_id,
		]);

	json_response([
		"status"=>"success",
		"key"=>$key_id
	]);

}
