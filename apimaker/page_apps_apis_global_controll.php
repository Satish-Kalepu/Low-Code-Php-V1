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

		if( isset( $res2['data']['engine']['input_factors'] ) ){
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
			'access_key'=>"",
			'expire_minutes'=>2,
			'client_ip'=>'192.168.1.1/32'
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
	"access_key": "",
	"expire_minutes": 2 //optional
	"client_ip": "192.168.1.1/32" //optional
}',
	];
	$apis['auth_apis'][] = [
		"_id"=>"10002",
		"path" => "_api/auth/user_auth",
		"name"=>"user_auth",
		"des"=>"Generate session token with user credentials",
		'input-method'=>"POST",
		"vpost"=>json_encode([
			'username'=>"",
			'password'=>"",
			'expire_minutes'=>2 
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
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
			'username'=>"",
			'password'=>"",
			'captcha'=>"",
			'code'=>"",
			'expire_minutes'=>2 
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
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
			'session_key'=>"",
		],JSON_PRETTY_PRINT),
		"vpost_help"=>'{
	"session_key": "",
}',
	];

	$apis['captcha'][] = [
		"_id"=>"10101",
		"path" => "_api/captcha/get",
		"name"=>"get",
		"des"=>"Generate Captcha",
		'input-method'=>"POST",
		"vpost"=>'{"ok":"ok"}',
		"vpost_help"=>'{"ok":"ok"}',
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

	json_response(['status'=>"success", "apis"=>$apis]);
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
