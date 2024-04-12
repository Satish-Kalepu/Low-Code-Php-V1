<?php
$config_template = [
	"MySql"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
		"port"=> ["name"=> "Port", "type"=>"number","value"=>3306, "store"=>"plain", "m"=>true],
		"database"=> ["name"=> "Database", "type"=>"text","value"=>"test", "store"=>"plain", "m"=>true],
		"username"=> ["name"=> "Username", "type"=>"text","value"=>"root", "store"=>"encrypt"],
		"password"=> ["name"=> "Password", "type"=>"text","value"=>"", "store"=>"encrypt"],
		"tls"=> ["name"=> "TLS", "type"=>"boolean","value"=>false, "store"=>"plain"],
	],
	"MongoDb"=> [
		"host"=> ["name"=> "Host", "type"=>"text","value"=>"localhost", "store"=>"plain", "m"=>true],
		"port"=> ["name"=> "Port", "type"=>"number","value"=>27017, "store"=>"plain", "m"=>true],
		"database"=> ["name"=> "Database", "type"=>"text","value"=>"main", "store"=>"plain", "m"=>true],
		"username"=> ["name"=> "Username", "type"=>"text","value"=>"", "store"=>"encrypt"],
		"password"=> ["name"=> "Password", "type"=>"text","value"=>"", "store"=>"encrypt"],
		"authSource"=> ["name"=> "authSource", "type"=>"text","value"=>"admin", "store"=>"plain"],
		"tls"=> ["name"=> "TLS", "type"=>"boolean","value"=>false, "store"=>"plain"],
		"flavour"=> ["name"=> "Flavour", "type"=>"select","value"=>"MongoDb", "values"=>["MongoDb", "Atlas Serverless", "DocumentDb", "CosmosDb"], "store"=>"plain"],
	],
	"Redis"=> [
		"host"=> ["name"=> "Host", "type"=>"text","value"=>"localhost", "store"=>"plain", "m"=>true],
		"port"=> ["name"=> "Port", "type"=>"number","value"=>6379, "store"=>"plain", "m"=>true],
		"auth"=> ["name"=> "Authentication", "type"=>"boolean","value"=>false, "store"=>"plain", "m"=>true],
		"password"=> ["name"=> "Password", "type"=>"text","value"=>"", "store"=>"encrypt", "m"=>false],
	],
	"DynamoDb"=> [
		"region"=> ["name"=> "Host", "type"=>"text","value"=>"ap-south-1", "store"=>"plain", "m"=>true],
		"access_key"=> ["name"=> "Access key", "type"=>"text","value"=>"", "store"=>"encrypt", "m"=>true],
		"access_secret"=> ["name"=> "Access Secret", "type"=>"text","value"=>"", "store"=>"encrypt", "m"=>true],
	],
	"MSSql"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
		"port"=> ["name"=> "Port", "type"=>"number","value"=>3306, "store"=>"plain", "m"=>true],
		"database"=> ["name"=> "Database", "type"=>"text","value"=>"test", "store"=>"plain", "m"=>true],
		"username"=> ["name"=> "Username", "type"=>"text","value"=>"root", "store"=>"encrypt"],
		"password"=> ["name"=> "Password", "type"=>"text","value"=>"", "store"=>"encrypt"],
		"tls"=> ["name"=> "TLS", "type"=>"boolean","value"=>false, "store"=>"plain"],
	],
	"PostgreSQL"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
		"port"=> ["name"=> "Port", "type"=>"number","value"=>3306, "store"=>"plain", "m"=>true],
		"database"=> ["name"=> "Database", "type"=>"text","value"=>"test", "store"=>"plain", "m"=>true],
		"username"=> ["name"=> "Username", "type"=>"text","value"=>"root", "store"=>"encrypt"],
		"password"=> ["name"=> "Password", "type"=>"text","value"=>"", "store"=>"encrypt"],
		"tls"=> ["name"=> "TLS", "type"=>"boolean","value"=>false, "store"=>"plain"],
	],
	"Oracle"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
		"port"=> ["name"=> "Port", "type"=>"number","value"=>3306, "store"=>"plain", "m"=>true],
		"database"=> ["name"=> "Database", "type"=>"text","value"=>"test", "store"=>"plain", "m"=>true],
		"username"=> ["name"=> "Username", "type"=>"text","value"=>"root", "store"=>"encrypt"],
		"password"=> ["name"=> "Password", "type"=>"text","value"=>"", "store"=>"encrypt"],
		"tls"=> ["name"=> "TLS", "type"=>"boolean","value"=>false, "store"=>"plain"],
	],
	"Cassandra"=> [
		"host"=> ["name"=> "Host", "type"=>"text","value"=>"localhost", "store"=>"plain", "m"=>true],
	],
	"AzureSQL"=> [
		"host"=> ["name"=> "Host", "type"=>"text","value"=>"localhost", "store"=>"plain", "m"=>true],
	],
	"Neo4j"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
	],
	"ElasticSearch"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
	],
	"CouchDB"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
	],
	"SAPHana"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
	],
	"IBMDb2"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
	],
	"FireBase"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
	],
	"FireStore"=> [
		"host"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
	],
];
$config_template_json = json_encode($config_template);
$config_api_databases = $config_global_apimaker['config_mongo_prefix'] . "_databases";
$config_api_tables = $config_global_apimaker['config_mongo_prefix'] . "_tables";

if( $_POST['action'] == "load_databases" ){
	$res = $mongodb_con->find_with_key( $config_api_databases, "_id", ["app_id"=>$app['_id']], [] );
	$databases = [];
	if( $res['data'] ){
		$databases = $res['data'];
		// foreach( $res['data'] as $i=>$j ){
		// 	foreach( $j['details'] as $m=>$n ){
		// 		if( preg_match("/^[a-z][0-9]+\:/", $n, $mt) ){
		// 			$databases[ $i ]['details'][ $m ] = pass_decrypt( $n );
		// 		}
		// 	}
		// }
	}
	json_response(['status'=>"success", 'databases'=>$databases]);
}

if( $_POST['action'] == "update_database" ){
	$config_debug = false;
	if(  $_POST["db_id"] != "new" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['db_id']) ){
			json_response("fail","db_id incorrect");
		}
	}
	if($_POST['des'] == ""){
		json_response("fail","Please Enter Database Description");
	}else if($_POST['engine'] == ""){
		json_response("fail","Please Select Database Type");
	}else{
		$template = $config_template[ $_POST['engine'] ];
		if( !$template ){
			json_response("fail","Incorrect Data");
		}else{
			foreach( $template as $i=>$j ){
				if( $j['m'] ){
					if( !isset($_POST['details'][ $i ]) ){
						json_response("fail",$i . " Required");
					}
				}
				if( $_POST['details'][ $i ] && $j['store'] == "encrypt" ){
					$_POST['details'][ $i ] = pass_encrypt($_POST['details'][ $i ]);
				}
			}
			$insert_data = [
				"user_id" => $_SESSION["user_id"],
				"app_id"  => $app['_id'],
				"des" => ucwords($_POST['des']),
				"engine"  => $_POST['engine'],
				"details" => $_POST['details'], 
			];
			//print_pre( $insert_data );exit;

			if( $_POST['db_id'] == "new" ){
				$cond = [
					"app_id"=>$app['_id'], 
					"des" => $mongodb_con->regex( "^". $_POST['des'] . "\$", 'i' )
				];
				$res = $mongodb_con->find_one( $config_api_databases, $cond );
				if( $res['data'] ){
					json_response("fail","Database description already exists!");
				}else{
					$insert_res = $mongodb_con->insert($config_api_databases, $insert_data);
					if( !$insert_res['status'] == "fail" ){
						json_response($insert_res);
					}
					json_response("success","ok");
				}
			}else{
				$db_res = $mongodb_con->find_one($config_api_databases, [ '_id'=>$_POST["db_id"] ],[]);
				if( !$db_res['data'] ){
					json_response("fail","Database not found!");
				}else{
					$db = $db_res['data'];
					$cond = [
						"app_id"=>$app['_id'], 
						"des" => $mongodb_con->regex( "^". $_POST['des'] . "\$", 'i' ),
						"_id"=>['$ne'=>$mongodb_con->get_id( $_POST['db_id'] )]
					];
					$res = $mongodb_con->find_one( $config_api_databases, $cond );
					if( $res['data'] ){
						json_response("fail","Database description already use!");
					}else{
						$update_cond = ["_id"=> $_POST["db_id"]  ];
						$res = $mongodb_con->update_one($config_api_databases,$update_cond,$insert_data);
						if( !$res ){
							json_response("fail","Server Error");
						}else{
							json_response("success","ok");
						}
					}
				}
			}
		}
	}
	exit;
}

if( $_POST['action'] == "test_database" ){
	$config_debug = false;
	$db = $mongodb_con->find_one($config_api_databases, ["_id"=>$_POST['db_id']]);
	if( !$db ){
		json_response("fail","Server Error");
	}
	if( $db['engine'] == "mongodb" ){

	}
	if( $db['engine'] == "dynamodb" ){

	}
	if( $db['engine'] == "mysql" ){

	}
	if( $db['engine'] == "redis" ){

	}
	exit;
}

if( $_POST['action'] == "load_tables" ){
	$config_debug = false;
	if( !$_POST['db_id'] ){
		json_response("fail", "DB id required!");
	}else{
		if( $_POST['db_id'] != "default-db" ){
			$db_res = $mongodb_con->find_one($config_api_databases, ["_id"=>$_POST['db_id']]);
			if( !$db_res['data'] ){
				json_response("fail","Database not found!");
			}else{
				$db = $db_res['data'];
			}
		}
		$tables_res = $mongodb_con->find($config_api_tables, ["app_id"=>$app['_id'], "db_id"=>$_POST['db_id'] ]);
		if( !isset($tables_res['data']) ){
			json_response("fail", "Table Details Missing!");
		}else{
			if( sizeof( $tables_res['data'] ) == 0 ){
				$tables_res = [];
			}else{
				$tables_res = $tables_res['data'];
			}
		}
		json_response([
			'status'=>"success", 
			"tables"=>$tables_res
		]);
	}
}

if( $_POST['action'] == 'delete_table' ){
	$db_res = $mongodb_con->find_one($config_api_tables,['db_id'=>$_POST["db_id"], '_id'=>$_POST["table_id"] ],[]);
	if( !$db_res ){
		json_response("fail", "Database not found!");
	}else{
		$status = $mongodb_con->delete_one( $config_api_tables, ["_id"=>$_POST["table_id"]] );
		json_response("success","Deleted Successfully");
	}
}

if( $_POST['action'] == "delete_database" ){
	$config_debug = false;
	$db_res = $mongodb_con->find_one($config_api_databases,["app_id"=>$app['_id'], '_id'=>$_POST["db_id"] ],[]);
	if( !$db_res['data'] ){
		json_response("fail","Database not found!");
	}else{
		$db = $db_res;
		$tables_res = $mongodb_con->find($config_api_tables,["app_id"=>$app['_id'], "db_id"=>$_POST["db_id"]],[]);
		if( $tables_res['data'] ){
			json_response("fail","Delete respective tables before deleting the database!");
		}else{
			$del_rec = $mongodb_con->delete_one($config_api_databases, ["app_id"=>$app['_id'], "_id"=>$_POST["db_id"]] );
			json_response($del_rec);exit;
		}
	}
	exit;
}

if( $config_param3 ){
	$db_res = $mongodb_con->find_one( $config_api_databases, [ "app_id"=>$app['_id'], "_id" => $config_param3 ] );
	if( !$db_res['data'] ){
		echo404("Database not found");exit;
	}else{
		$db = $db_res['data'];
	}
	if( $_GET["test"] == "test_1" ){
		print_pre( $db );
	}
	$meta_title = "Database - " . $db['engine'] . " - " . $db['des'];
	if( file_exists("page_databases_controll_".$db['engine'].".php") ){
		require("page_databases_controll_".$db['engine'].".php");
	}else{
		echo "DB Engine " . $db['engine'] . " module is under development!";exit;
	}
}else{
	$meta_title = "Databases";
}
