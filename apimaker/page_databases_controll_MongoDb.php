<?php

$db['details']['username'] = pass_decrypt($db['details']['username']);
$db['details']['password'] = pass_decrypt($db['details']['password']);

$con = new mongodb_connection( $db['details']['host'], $db['details']['port'], $db['details']['database'], $db['details']['username'], $db['details']['password'],$db['details']['authSource'], ($db['details']['tls']?true:false) );
function find_field_type( $fn, $v, $fields ){
	if( $fields[ $fn ] ){
		if( $fields[ $fn ]['type'] == "number" ){
			if( strpos($v,".") ){
				$v = (float)$v;
			}else{
				$v = (int)$v;
			}
		}
	}
	return $v;
}
function find_mongodb_fields_structure2($rec){
	$fields = [];
	$cnt= 0;
	foreach( $rec as $i=>$j ){
		$cnt++;
		$t = "text";
		if( gettype($j)== "integer" || gettype($j)== "double" ){$t = "number";}
		if( gettype($j)== "boolean" ){$t = "boolean";}
		if( gettype($j)== "array" ){
			if(array_keys($j) !== range(0, count($j) - 1)){
			    $t = "dict";
			}else{
			    $t = "list";
			}
		}
		$fields[ $i ] = [ "key"=>$i, "name"=>$i, "type"=>$t, "m"=>true, "order"=>$cnt, "sub"=>[] ];
		if( $t == "dict" ){
			$fields[ $i ]['sub'] = find_mongodb_fields_structure2($j);
		}
		if( $t == "list" ){
			$fields[ $i ]['sub'] = [];
			$fields[ $i ]['sub'][0] = find_mongodb_fields_structure2($j[0]);
		}
	}
	if( sizeof($fields) == 0 ){
		$fields['_id'] = [ "key"=>'_id', "name"=>'_id', "type"=>'text', "m"=>true, "order"=>1, "sub"=>[] ];
	}
	return $fields;
}
function find_mongodb_fields_structure( $recs ){
	$fields = [];
	foreach( $recs as $i=>$j ){
		$f = find_mongodb_fields_structure2( $j );
		$fields = array_replace_recursive( $fields, $f );
	}
	return $fields;
}
/*Manage*/

if( $_POST['action'] == "database_mongodb_load_tables" ){

	$collections = [];

	$res = $con->database->listCollectionNames();

	foreach( $res as $i=>$j ){
		$d = [];
		$d['collection'] = $j;
		$stats_res = $con->database->command( ["collStats"=>$j ] )->toArray();
		//print_pre( $stats_res );exit;
		$d['size'] = $stats_res['0']['size'];
		$d['count'] = $stats_res['0']['count'];
		$d['avgObjSize'] = (isset($stats_res['0']['avgObjSize'])?$stats_res['0']['avgObjSize']:'');
		$d['storageSize'] = $stats_res['0']['storageSize'];
		$d['freeStorageSize'] = $stats_res['0']['freeStorageSize'];
		$d['capped'] = $stats_res['0']['capped'];
		$d['totalIndexSize'] = $stats_res['0']['totalIndexSize'];
		$d['totalSize'] = $stats_res['0']['totalSize'];
		$d['indexSizes'] = $stats_res['0']['indexSizes'];
		//$d['indexDetails'] = $stats_res['0']['capped'];
		$d['nindexes'] = $stats_res['0']['nindexes'];
		$collections[ $j ] = $d;
	}
	//print_pre( $databases );
	//ksort($collections);

	$total_objects = 0;
	$total_datasize = 0;
	$total_storageSize = 0;
	$total_indexSize = 0;
	$total_views = 0;
	foreach( $collections as $collection=>$d ){
		$total_objects += $d['count'];
		$total_datasize += $d['size'];
		$total_storageSize += $d['storageSize'];
		$total_indexSize += $d['totalIndexSize'];
		$total_views += (isset($d['views'])?$d['views']:0);
	}

	//print_r( $collections );

	// $res = $con->list_collections();
	// print_r( $res );exit;

	$tables = [];
	$tables_res = $mongodb_con->find( $config_api_tables, [
		"app_id"=>$config_param1, 
		"db_id"=>$config_param3
	], [
		'projection'=>["table"=>1,"des"=>1] 
	]);
	if( !isset($tables_res['data']) ){
		json_response("fail", "Table Details Missing!");
	}else{
		foreach( $tables_res['data'] as $i=>$j ){
			$tables[ $j['table'] ] = $j;
		}
	}
	foreach( $collections as $collection=>$cd ){
		if( isset( $tables[ $collection ] ) ){
			$tables[ $collection ]['f'] = true;
			$collections[ $collection ]['_id'] = $tables[ $collection ]['_id'];
		}else{
			$tables[ $collection ]['f'] = true;
			$insert_data = [
				"table"=>$collection,
				"des"=>$collection,
				"app_id"=>$config_param1,
				"db_id"=>$config_param3,
			];

			$res = $con->list_indexes_raw( $collection );
			if( $res['status'] == 'fail' ){
				json_response( "fail", $res['error'] );
			}
			$res2 	= $con->find( $collection, [], ["limit"=>10] );
			if( !isset($res2['data']) || sizeof($res2['data']) == 0 ){
				$fields = [ '_id'=>[ "key"=>'_id', "name"=>'_id', "type"=>'text', "m"=>true, "order"=>1, "sub"=>[] ] ];
			}else{
				$fields = find_mongodb_fields_structure( $res2['data'] );
			}
			$insert_data[ "source_schema" ] = [
				"keys" => $res['data'],
				"fields" => $fields,
				"last_checked" => date("Y-m-d H:i:s")
			];
			$insert_data["keys"] = $res['data'];
			$insert_data["f_n" ] = array_keys($fields);
			$insert_data["schema"] = [
				"default"=> [
					"name"		=> "Default",
					"fields" 	=> $fields,
				]
			];
			$res_insert = $mongodb_con->insert( $config_api_tables, $insert_data );
			if( $res_insert['status'] != "success" ){
				json_response($res_insert);
			}
			$collections[ $ci ]['_id']= $res_insert['inserted_id'];

			event_log( "system", "database_table_create", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MongoDb",
				'table_id'=>$res_insert['inserted_id']
			]);
		}
	}
	//print_r( $tables );
	foreach( $tables as $collection=>$j ){
		if( !isset($j['f']) ){
			$collections[ $collection ] = [
				"_id"=>$j['_id'],
				"collection"=>$collection,
				'size' => 0,
				'count' => 0,
				'avgObjSize' => 0,
				'storageSize' => 0,
				'freeStorageSize' => 0,
				'capped' => 0,
				'totalIndexSize' => 0,
				'totalSize' => 0,
				'indexSizes' => 0,
				'nindexes' => 0,
			];
		}
	}
	ksort($collections);
	json_response([
		'status'=>"success", 
		"tables"=>$collections,
		"tot"=>[
			'objects'=> $total_objects,
			'datasize'=> $total_datasize,
			'storageSize'=> $total_storageSize,
			'indexSize'=> $total_indexSize,
			'views'=> $total_views,
		]
	]);
}

if( $_POST['action'] == "database_mongodb_create_collection" ){
	if( !isset($_POST['collection']) ){
		json_response("fail", "Need collection name");
	}else if( !preg_match("/^[a-z][a-z0-9\-\_]{2,50}$/", $_POST['collection'] ) ){
		json_response("fail","Collection name should be lowercase. no special chars and spaces");
	}

	$res = $mongodb_con->find_one($config_api_tables, [
		"app_id"=>$config_param1,
		"db_id"=>$config_param3, 
		"table"=>$_POST['collection']
	]);
	if( $res['data'] ){
		json_response([
			"status"=>"fail", "error"=>"Collection already exists"
		]);
	}

	$res = $con->database->listCollectionNames();
	$f = false;
	foreach( $res as $i=>$j ){
		if( $j == $_POST['collection'] ){
			$f = true;
		}
	}
	if( $f ){
		$cres = $con->create_collection($_POST['collection']);
		if( $cres['status'] != "success" ){
			json_response($cres);
		}
	}

	$insert_data = [
		"table"=> $_POST['collection'],
		"des"  => $_POST['collection'],
		"app_id"=> $config_param1,
		"db_id" => $config_param3,
	];

	$fields = [ 
		'_id'=>[ "key"=>'_id', "name"=>'_id', "type"=>'text', "m"=>true, "order"=>1, "sub"=>[] ],
		'f1'=>[ "key"=>'f1', "name"=>'f1', "type"=>'text', "m"=>true, "order"=>2, "sub"=>[] ],
		'f2'=>[ "key"=>'f2', "name"=>'f2', "type"=>'text', "m"=>true, "order"=>3, "sub"=>[] ] 
	];
	//$insert_data[ "source_schema" ] = ;
	$insert_data["keys"] = [];
	$insert_data["f_n" ] = array_keys($fields);
	$insert_data["schema"] = [
		"default"=> [
			"name"		=> "Default",
			"fields" 	=> $fields,
		]
	];
	$res = $mongodb_con->insert( $config_api_tables, $insert_data );

	event_log( "system", "database_table_create", [
		"app_id"=>$config_param1,
		'db_id'=>$config_param3, 
		"engine"=>"MongoDb",
		'table_id'=>$insert_data['inserted_id']
	]);

	json_response($res);
}





if( $_POST['action'] == "check_mongodb_source_collection_list" ){
	$res = $con->list_collections();
	json_response($res);
	exit;
}

if( $_POST['action'] == "check_mongodb_source_table" ){
	$config_debug = false;
	{
		$res 	= $con->list_indexes_raw( $_POST['table'] );
		if( $res['status'] == 'fail' ){
			json_response( "fail", $res['error'] );
		}
		$res2 	= $con->find( $_POST['table'], [], ["limit"=>10] );
		if( !isset($res2['data']) || sizeof($res2['data']) == 0 ){
			$fields = [ '_id'=>[ "key"=>'_id', "name"=>'_id', "type"=>'text', "m"=>true, "order"=>1, "sub"=>[] ] ];
		}else{
			$fields = find_mongodb_fields_structure( $res2['data'] );
		}
			if( $config_param4 != "new" ){
				$update_data = [
					"source_schema"=>[
						"schema"=>$res['data'],
						"last_checked"=>date("Y-m-d H:i:s")
					]
				];
				$res3 = $mongodb_con->update_one($config_api_tables,[
					"app_id"=>$_POST['app_id'],
					"db_id"=>$_POST['db_id'],
					"_id"=>$main_table['_id']
				],$update_data );
				if( $res3['status'] == "fail" ){
					json_response( "fail", $res3["error"] );
				}
				event_log( "system", "database_table_update", [
					"app_id"=>$config_param1,
					'db_id'=>$config_param3, 
					"engine"=>"MongoDb",
					'table_id'=>$main_table['_id']
				]);
			}
			json_response( "success", [
				"keys" => $res['data'],
				"fields" => $fields,
				"last_checked" => date("Y-m-d H:i:s")
			]);
	}
}

/*Import*/

if( $_POST["action"] == "import_mongodb_data" ){
	$config_debug = false;
	if( $config_param3 != $_POST['db_id'] ){
		json_response("fail", ["error_type" =>"error","error"=>"Incorrect DB id"]);
	}else if( $config_param5 != $_POST['table_id'] ){
		json_response("fail", ["error_type" =>"dulipcates","error"=>"Incorrect credentials"]);
	}else if( sizeof( $_POST["data"] ) == 0 ){
		json_response("fail",["error_type" =>"dulipcates","error"=>"Please Enter File With Data"]);
	}else{
		$main_table_res = $mongodb_con->find_one($config_api_tables, ["_id" => $_POST['table_id'] ]);
		if($main_table_res["status"] == "fail" ){
			json_response("fail",["error_type" =>"dulipcates","error"=>"Table not found!"]);
		}else if($main_table_res["data"] == "" ){
			json_response("fail",["error_type" =>"dulipcates","error"=>"Table not found!"]);
		}else{
			$main_table = $main_table_res["data"];
			$error  = $fields = [];
			$tablename = $main_table['table'];
			for($rec=0;$rec < sizeof($_POST["data"]);$rec++){
				if( $rec >=sizeof($_POST["data"]) ){break;}
				$record = $_POST['data'][ $rec ];
				$d_r_1 = [];
				for($rec=0;$rec < sizeof($_POST["data"]);$rec++){
    				if( $rec >=sizeof($_POST["data"]) ){break;}
    				$record = $_POST['data'][ $rec ];
    				$d_r_1 = [];
    				foreach( $_POST["fields"] as $f => $field ){
    					if( $field["type"] == "number" ){
							if( preg_match("/\./", $record[ $f ] ) ){
								$val____ = (float)$record[ $f ];
							}else{
								$val____ = (int)$record[ $f ];
							}
						}else if( $field["type"] == "text" ){
							$val____ = trim($record[ $f ]);
						}else{
							$val____ = ($record[ $f ]);
						}
						if( $f == "_id" && $record[$f] == "" ){
							$val____ = new MongoDB\BSON\ObjectID();
						}
						$record[ $f ] = $val____;
						if( $field["m"] == true && $field["insert"] == true){
		  					$d_r_1[ $f ] = $record[ $f ];
							if($val____ =='' ){
								$errors[ $rec ][ $f ] = "required!";
							}
						}
    				}
					$record['_status__'] = "done";
					$du_r = $con->find_one($tablename,$d_r_1);
					if($du_r["status"] == "success" && sizeof( $du_r["data"] ) != 0 ){
						$duplicate_records[] = $_POST["data"][$rec];
						if( $_POST["duplicate_check"] == "skip" ){
							$record['_status__'] = "skip";
						}
					}
					$records[] = $record;
    			}
    		}
			if( sizeof($errors) ){
				json_response("fail",["error_type" =>"server_errors", "record_wise_errors"=>$errors]);
			}else if( $_POST["duplicate_check"] == "check" && sizeof($duplicate_records) >0 ){
				json_response("fail",["error_type" =>"dulipcates", "duplicate_records"=>$duplicate_records]);
			}else{
    			$main_fields = $main_table["schema"];
				foreach( $_POST["fields"] as $i => $j ){
					unset($j["new_field"] );
					if( $j["insert"] == true || $j["key"] == "_id" ){
							unset($j["insert"]);
							$fields[ $i ] = $j;
					}
				};
    			$main_fields[$_POST["selected_schema"]]["fields"] = $fields;
    			$errors = [];
                foreach( $records as $field => $rec ){
                	unset( $rec["_insert__"] );unset( $rec["_main_cnt__"] );
                	if( $rec["_status__"] != "skip" ){
						unset( $rec["_status__"] );
						$new_insert_res = $con->insert( $tablename, $rec, "check" );
						if( $new_insert_res['status'] == "success" ){
							event_log( "database_table", "record_create", [
								"app_id"=>$config_param1,
								'db_id'=>$config_param3, 
								"engine"=>"MongoDb",
								'table_id'=>$main_table['_id']
							]);
					    	$increment_rec = $mongodb_con->increment($config_api_tables, $main_table['_id'], "count", 1);
							if( $increment_rec['status'] == "fail" ){
								$error_log                = [];
								$error_log["page"]        = "Database MongoDb Import";
								$error_log["url"]         = $request_uri;
								$error_log["user_id"]     = $_SESSION["user_id"];
								$error_log["event"]       = "increment error";
								$error_log["error"]       = $increment_rec['error'];
								$error_log["action"]      = "import_mongodb_data";
								$error_log["data"]        = $rec;
								$error_log["date"]        = date("d-m-Y H:i:s");
								$error_log_res = $con->insert($error_log_col, $error_log);
							}
						}else{
							$error_log                = [];
							$error_log["page"]        = "Database MongoDb Import";
							$error_log["url"]         = $request_uri;
							$error_log["user_id"]     = $_SESSION["user_id"];
							$error_log["event"]       = "Insert error";
							$error_log["error"]       = $new_insert_res['error'];
							$error_log["action"]      = "import_mongodb_data";
							$error_log["data"]        = $rec;
							$error_log["date"]        = date("d-m-Y H:i:s");
							$error_log_res = $con->insert($error_log_col, $error_log);
						}
					}
            	}
            	$update_rec = $mongodb_con->update_one( $config_api_tables,["schema" => $main_fields], ["_id"=>$_POST['table_id'] ] );
				if( $update_rec["status"] == "fail" ||  ($update_rec["status"] == "success" && $update_rec["data"]["matched_count"] != $update_rec["data"]["modified_count"] ) ){
					json_response("fail",$update_rec['error']);
				}else{
					json_response("success", "ok");
				}
    		}
		}
	}
}

/*Export*/
if( $_POST["action"] == "export_mongodb_data" ){
	$config_debug = false;
	
	if( $config_param4 != $_POST['table_id'] ){
		$_SESSION["export_error"] = "Table not found!";
		header("Location: /databases/".$config_param1."/table/".$config_param4."/export?event=fail");exit;
	}else{
		$main_table_res = $mongodb_con->find_one($config_api_tables, ["_id" => $_POST['table_id'] ]);
		if($main_table_res["status"] == "fail" ){
			$_SESSION["export_error"] = "Table not found!";
			header("Location: /databases/".$config_param1."/table/".$config_param4."/export?event=fail");exit;
		}else if($main_table_res["data"] == "" ){
			$_SESSION["export_error"] = "Table not found!";
			header("Location: /databases/".$config_param1."/table/".$config_param4."/export?event=fail");exit;
		}else{
			$main_table = $main_table_res["data"];
			$filters = ["="=>'$eq',"!="=>'$ne', "<"=>'$lt', "<="=>'$lte', ">"=>'$gt', ">="=>'$gte'];
			$primary_search = bson_to_json(json_decode($_POST["primary_search"])  );
			$delimeter = $primary_search["delimeter"] ? $primary_search["delimeter"]:",";
			$cond = [];
			$options = ["limit"=>(int) $_POST['limit'] ];
			if( $_POST["search_index"] == "primary" ){
				$ac = $primary_search['c'];
				$av = $primary_search['v'];
				if( $av ){
					$av = $mongodb_con->get_id($av);
				}
				$av2 = $primary_search['v2'];
				if( $av2 ){
					$av2 = $mongodb_con->get_id($av2);
				}
				if( $av ){
					if( $ac == "=" ){
						$cond[ "_id" ] = $av;
					}else if( $ac == "><"){
						$cond[ "_id" ] = [];
						$cond[ "_id" ][ $filters['>='] ] = $av;
						$cond[ "_id" ][ $filters['<='] ] = $av2;
					}else{
						$cond[ "_id" ] = [];
						$cond[ "_id" ][ $filters[ $ac ] ] = $av;
					}
				}
				if( $_POST['last_key'] ){
					if( $_POST['primary_search']['sort']=="desc" ){
						$cond['_id'] = ['$lt'=>$mongodb_con->get_id($_POST['last_id']) ];
					}else{
						$cond['_id'] = ['$gt'=>$mongodb_con->get_id($_POST['last_id']) ];
					}
				}
				$s = [];
				$s[ "_id" ] = ($_POST['primary_search']['sort']=="desc"?-1:1);
				$options["sort"] = $s;

			}else{
				$options["hint"] = $_POST['search_index'];
				$sort = [];
				foreach( $_POST['index_search'] as $i=>$j ){
					$bv = $bv2 = "";
					$sort[ $j['name'] ] = ($j['sort']=="asc"?1:-1);
					if( $j['name'] == "_id" ){
						if( $j['v'] ){
							$bv = $mongodb_con->get_id( $j['v'] );
						}
						if( $j['v2'] ){
							$bv2 = $mongodb_con->get_id( $j['v2'] );
						}
					}else{
						if( $j['v'] ){
							$bv = find_field_type( $j['name'], $j["v"], $main_table['fields'] );
						}
						if( $j['v2'] ){
							$bv2 = find_field_type( $j['name'], $j["v2"], $main_table['fields'] );
						}
					}
					if( $bv ){
						if( $j['cond'] == "=" ){
							$cond[ $j['name'] ] = $bv;
						}else if( $j['cond'] == "><"){
							$cond[ $j['name'] ] = [];
							$cond[ $j['name'] ][$filters['>=']] = $bv;
							$cond[ $j['name'] ][$filters['<=']] = $bv2;
						}else{
							$cond[ $j['name'] ] = [];
							$cond[ $j['name'] ][ $filters[ $j['cond'] ] ] = $bv;
						}
					}
				}
				if( $_POST['skip'] ){
					$options['skip'] = (int)$_POST['skip'];
				}
				$options["sort"] = $sort;
			}
			try{
				$titles = [];
				$fields = $main_table["schema"][ $_POST["selected_schema"] ]["fields"];
				foreach ($fields as $ij=>$jj) {
					$titles[] = $ij;// str_replace($ij,$delimeter," ");
				}
				$exported_data = [];
				$data_export =$con->find($main_table['table'], $cond, $options);
				if( $data_export["status"] == "fail" ){
					json_response( "fail",$data_export["error"] );
				}else{
					if( sizeof($data_export["data"]) == 0 || $data_export["data"] == "" ){
						$data_export["data"] = [];
					}
					foreach ($data_export["data"] as $key => $value) {
						foreach ($fields as $field => $fn) {
							if($_POST["export_type"] == "csv"){
								$add_data = false;
								if( $fn["type"] == "_id" || $fn["type"] == "text" || $fn["type"] == "number" ){
									$add_data = true;
								}
							}else{
								$add_data = true;
							}
							if( $add_data ){
								if($value[$field]){
									$exported_data[$key][$field] =  ($value[$field]);
								}else{
									$exported_data[$key][$field] =  "";
								}
							}
						}
					}
					$export_filename = ($mongodb_con->clean_text($main_table["table"])).'_'.date("Ymd_His");
					if($_POST["export_type"] == "csv"){
						$export_path = "./tempfiles/" . $export_filename . ".csv";
					}else{
						$export_path = "./tempfiles/" . $export_filename . ".json";
					}
					$fp = fopen( $export_path, "w");
					if($_POST["export_type"] == "csv"){
						fputs($fp, implode($delimeter, $titles) . "\r\n" );
						foreach ($exported_data as $i=>$j) {
						 	fputs($fp, implode($delimeter, $j) . "\r\n" );
						}
						fclose($fp);
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; filename="'.$export_filename.'.csv";' );
						readfile($export_path);exit;
					}else{
						foreach($exported_data as $i=>$j){
							fwrite($fp, json_encode( $j ) . "\r\n" );
						}
						fclose($fp);
						header("Content-type: application/json");
						header('Content-Disposition: attachment; filename="'.$export_filename.'.json";' );
						readfile($export_path);exit;
					}
				}
			}catch(Exception $ex){
				$_SESSION["export_error"] = $e->getMessage();
				header("Location: /databases/".$config_param1."/table/".$config_param4."/export?event=fail");
				exit;
			}
		}
	}
}
/*Browse*/	
if( $_POST['action'] == "load_mongodb_records" ){
	if( $config_param3 != $_POST['db_id'] ){
		json_response("fail", "Incorrect Db Id");
	}
	if( $config_param5 != $_POST['table_id'] ){
		json_response("fail", "Incorrect Table Id");
	}else{
		$main_table_res = $mongodb_con->find_one($config_api_tables, [
			"db_id"=> $_POST['db_id'],
			"_id" => $_POST['table_id'] 
		]);
		if(!$main_table_res['data'] ){
			json_response("fail","Table not found!");
		}else{
			$main_table = $main_table_res['data'];
			$filters = ["="=>'$eq',"!="=>'$ne', "<"=>'$lt', "<="=>'$lte', ">"=>'$gt', ">="=>'$gte'];
			$primary_search = $_POST["primary_search"];
			$cond = [];
			$options = ["limit"=>$_POST['limit'] ];
			if( $_POST["search_index"] == "primary" ){
				$ac = $primary_search['c'];
				$av = $primary_search['v'];
				if( $av ){
					$av = $mongodb_con->get_id($av);
				}
				$av2 = $primary_search['v2'];
				if( $av2 ){
					$av2 = $mongodb_con->get_id($av2);
				}
				if( $av ){
					if( $ac == "=" ){
						$cond[ "_id" ] = $av;
					}else if( $ac == "><"){
						$cond[ "_id" ] = [];
						$cond[ "_id" ][ $filters['>='] ] = $av;
						$cond[ "_id" ][ $filters['<='] ] = $av2;
					}else{
						$cond[ "_id" ] = [];
						$cond[ "_id" ][ $filters[ $ac ] ] = $av;
					}
				}
				if( $_POST['last_key'] ){
					if( $_POST['primary_search']['sort']=="desc" ){
						$cond['_id'] = ['$lt'=>$mongodb_con->get_id($_POST['last_id']) ];
					}else{
						$cond['_id'] = ['$gt'=>$mongodb_con->get_id($_POST['last_id']) ];
					}
				}
				$s = [];
				$s[ "_id" ] = ($_POST['primary_search']['sort']=="desc"?-1:1);
				$options["sort"] = $s;

			}else{
				$options["hint"] = $_POST['search_index'];
				$sort = [];
				foreach( $_POST['index_search'] as $i=>$j ){
					$bv = $bv2 = "";
					$sort[ $j['field'] ] = ($j['sort']=="asc"?1:-1);
					if( $j['field'] == "_id" ){
						if( $j['v'] ){
							$bv = $mongodb_con->get_id( $j['v'] );
						}
						if( $j['v2'] ){
							$bv2 = $mongodb_con->get_id( $j['v2'] );
						}
					}else{
						if( $j['v'] ){
							$bv = find_field_type( $j['field'], $j["v"], $main_table['fields'] );
						}
						if( $j['v2'] ){
							$bv2 = find_field_type( $j['field'], $j["v2"], $main_table['fields'] );
						}
					}
					if( $bv ){
						if( $j['cond'] == "=" ){
							$cond[ $j['field'] ] = $bv;
						}else if( $j['cond'] == "><"){
							$cond[ $j['field'] ] = [];
							$cond[ $j['field'] ][$filters['>=']] = $bv;
							$cond[ $j['field'] ][$filters['<=']] = $bv2;
						}else{
							$cond[ $j['field'] ] = [];
							$cond[ $j['field'] ][ $filters[ $j['cond'] ] ] = $bv;
						}
					}
				}
				if( $_POST['skip'] ){
					$options['skip'] = (int)$_POST['skip'];
				}
				$options["sort"] = $sort;
			}
			//print_r( $cond );exit;
			try{
				$records_list = $con->find( $main_table['table'], $cond, $options );
				if( !isset($records_list['data']) || $records_list['status'] == "fail" ){
					json_response( "fail", $records_list["error"] );
				}else{
					json_response("success",[
						"records"=>$records_list['data'], 
						"c"=>$cond, 
						"o"=>$options
					]);
				}
			}catch(Exception $ex){
				json_response(["status"=>"fail", "error"=>$ex->getMessage(), "cond"=>$cond]);
			}
		}
	}
	exit;
}


if( $config_param4 == "table" && $config_param5 == "new" ){
	$table = [
		"_id"    => "new",
		"app_id"  => $config_param1,
		"db_id"  => $config_param3,
		"des"	 => "",
		"table"	 => "",
		"keys"	 => [],/*["a"=>["field"=>"","order"=>""], "b"=>["field"=>"","order"=>"", "m"=>false], "type"=>"index/sparse/unique"]*/
		"f_n"	 => ["_id", "f1", "f2"],
		"schema" => [
			"default"=> [
				"name"		=> "Default",
				"fields" 	=> [
					"_id"=> ["key"=>"_id", "name"=>"_id", "des"=>"Primary ID", "type"=> "text", "m"=> true, "order"=> 0],
					"f1" => ["key"=>"f1", "name"=>"f1", "des"=>"Name", "type"=> "text", "m"=> true, "order"=> 1],
					"f2" => ["key"=>"f2", "name"=>"f2", "des"=>"Mobile", "type"=> "number", "m"=> true, "order"=> 2],
			   ]
			]
		]
	];
}else if( $config_param4 == "table" && $config_param5 ){
	if( !$mongodb_con->is_id_valid( $config_param5 ) ){
		echo404("Incorrect URL!");
	}
	$table_res = $mongodb_con->find_one($config_api_tables, ["_id"=>$config_param5]);
	if( !$table_res['data'] ){
		echo404("Table Not Found!");
	}else{
		$table = $table_res['data'];		
		$table_rec_cnt = $con->count($table["table"],[],[] );
		if($table_rec_cnt["status"] == "fail"){
			echo404("count fail: " . $table_rec_cnt['error']);
		}else{
			$total_cnt = (int)$table_rec_cnt['data'];
		}

		$ires = $con->list_indexes_raw( $table['table'] );
		//print_r( $ires );print_r( $table['keys'] );
		foreach( $ires['data'] as $name=>$j ){
			if( !isset($table['keys'][ $name ]) ){
				$j['type'] = "text";
				$table['keys'][ $name ] = $j;
				$ures = $mongodb_con->update_one( $config_api_tables, ['_id'=>$config_param5], [
					'keys.'. $name => $j
				]);
				if( $ures['status'] != "success" ){
					json_response($ures);
				}
			}
		}
		//exit;

		if( $_POST['action'] == "database_table_schema_update" ){
			//print_r( $_POST );	exit;
			$res = $mongodb_con->update_one($config_api_tables, [
				"_id"=> $config_param5
			], [
				'schema'=>$_POST['schema']
			]);

			event_log( "system", "database_table_schema_update", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MongoDb",
				'table_id'=>$config_param5
			]);
			json_response($res);
		}

		if( $_POST['action'] == "database_mongodb_drop_index" ){
			if( !isset($_POST['name']) ){
				json_response(['status'=>"fail", "error"=>"incorrect payload"]);
			}
			$dres = $con->drop_index( $table['table'], $_POST['name']);
			if( $dres['status'] !="success"){ 
				if( !preg_match("/not found/i", $dres['error']) ){
					json_response($dres);
				}
			}
			$res = $mongodb_con->update_one( $config_api_tables, ['_id'=>$config_param5],[
				'$unset'=>["keys." . $_POST['name']=>true]
			]);
			event_log( "system", "database_table_index_drop", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MongoDb",
				'table_id'=>$config_param5
			]);
			json_response($res);
			exit;
		}

		if( $_POST['action'] == "database_mongodb_update_index_type" ){
			if( !isset($_POST['index']) || !isset($_POST['keys']) ){
				json_response(['status'=>"fail", "error"=>"incorrect payload"]);
			}
			if( !isset($table['keys'][ $_POST['index'] ]) ){
				json_response(['status'=>"fail", "error"=>"Index not found"]);
			}
			$res = $mongodb_con->update_one( $config_api_tables, ['_id'=>$config_param5],[
				"keys." . $_POST['index'] . ".keys"=>$_POST['keys']
			]);
			event_log( "system", "database_table_index_update", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MongoDb",
				'table_id'=>$config_param5,
				"index"=>$_POST['keys']
			]);
			json_response($res);
			exit;
		}

		if( $_POST['action'] == "database_mongodb_create_index" ){
		//	print_r( $_POST );exit;
			if( !isset($_POST['new_index']) ){
				json_response(['status'=>"fail", "error"=>"incorrect payload"]);
			}
			if( !isset($_POST['new_index']['name']) ){
				json_response(['status'=>"fail", "error"=>"incorrect indexname"]);
			}else if( !preg_match( "/^[a-z0-9\-\_\.]{2,100}$/i", $_POST['new_index']['name']) ){
				json_response(['status'=>"fail", "error"=>"incorrect indexname"]);
			}
			if( !isset($_POST['new_index']['keys']) ){
				json_response(['status'=>"fail", "error"=>"Require keys"]);
			}else if( !is_array($_POST['new_index']['keys']) ){
				json_response(['status'=>"fail", "error"=>"Require keys"]);
			}
			$keys = [];
			$ops = [
				"name"=>$_POST['new_index']['name'],
				"sparse"=>($_POST['new_index']['sparse']?true:false),
				"unique"=>($_POST['new_index']['unique']?true:false),
			];
			foreach( $_POST['new_index']['keys'] as $i=>$j ){
				if( !isset($j['name']) || !isset($j['type']) || !isset($j['sort']) ){
					json_response(['status'=>"fail", "error"=>"Incorrect keys"]);
				}
				$keys[ $j['name'] ] = ($j['sort']=="asc"?1:-1);
			}

			$ires = $con->create_index( $table['table'], $keys, $ops );
			if( $ires['status'] != "success" ){
				json_response($ires);
			}

			if( !isset($table['keys']) ){
				$keys = [];
			}else{
				$keys = $table['keys'];
			}
			$keys[ $_POST['new_index']['name'] ] = $_POST['new_index'];
			$res = $mongodb_con->update_one( $config_api_tables, ['_id'=>$config_param5], [
				'keys'=>$keys
			]);
			event_log( "system", "database_table_index_create", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MongoDb",
				'table_id'=>$config_param5,
				"index"=>$_POST['new_index']
			]);
			json_response($res);

			exit;
		}

		if( $_POST['action'] == "database_mongodb_import_batch" ){

			$t = validate_token("database_mongodb_import_batch.". $config_param1 . "." .$table['_id'], $_POST['token']);
			if( $t != "OK" ){
				json_response("fail", $t);
			}

			$res = $con->count( $table['table'] );
			if( $res['status'] != "success" ){
				json_response(['status'=>"fail", "error"=>"Count check failed: " . $res['error']]);
			}
			if( $res['data'] > 20000 ){
				json_response(['status'=>"fail", "error"=>"Table already has more than 20k records"]);
			}

			$success = 0;
			$skipped = 0;
			$error = "";
			$skipped_items = [];

			foreach( $_POST['data'] as $i=>$j ){
				$res = $con->insert( $table['table'], $j );
				if( $res['status'] != "success" ){
					$error = $res['error'];
					break;
				}
				event_log( "database_table", "record_create", [
					"app_id"=>$config_param1,
					'db_id'=>$config_param3, 
					"engine"=>"MongoDb",
					'table_id'=>$config_param5,
					"record_id"=>$res['inserted_id']
				]);
				$success++;
			}

			event_log( "system", "database_table_import_data", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MongoDb",
				'table_id'=>$config_param5,
				"success"=>$success,
				"skipped"=>$skipped,
				"skipped_items"=>$skipped_items,
			]);

			if( $error ){
				json_response([
					'status'=>"fail",
					"success"=>$success,
					"skipped"=>$skipped,
					"skipped_items"=>$skipped_items,
					"error"=>$error
				]);
			}else{
				json_response([
					'status'=>"success",
					"success"=>$success,
					"skipped"=>$skipped,
					"skipped_items"=>$skipped_items,
					"error"=>$error,
				]);
			}
			exit;
		}

		if( $_POST['action'] == "database_table_export" ){

			if( !isset($_POST['exp']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect input"]);
			}
			if( !isset($_POST['exp']['type']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect input"]);
			}
			if( !preg_match("/^(JSON|CSV)$/", $_POST['exp']['type'] ) ){
				json_response(['status'=>"fail", "error"=>"Unhandled export type"]);
			}

			@mkdir( "/tmp/phpengine_backups/", 0777 );
			$tmfn = "/tmp/phpengine_backups/". preg_replace("/\W/", "", $table['table']) . "_" . preg_replace("/\W/", "", $table['des']) . "_" . date("Ymd_His") . "." . strtolower($_POST['exp']['type']);
			if( file_exists($tmfn) ){
				if( filemtime($tmfn) > time()-60 ){
					json_response(['status'=>"fail", "error"=>"An export was just invoked. please wait ".(60-(time()-filemtime($tmfn)))." seconds"]);
				}
			}
			$fp = fopen($tmfn, "w");

			if( $_POST['exp']['type'] == "CSV" ){
				$res = $con->find( $table['table'], [], ['limit'=>10, 'sort'=>['_id'=>1] ]);
				$fields = [];
				foreach( $res['data'] as $i=>$j ){
					foreach( $j as $fn=>$fd ){
						$fields[ $fn ]+=1;
					}
				}
				if( sizeof( $fields ) == 0 ){
					json_response(['status'=>"fail", "error"=>"Table already has more than 20k records"]);
				}
				fputcsv($fp, array_keys($fields) );
			}

			$last_id = "";
			$perpage = 500;
			while( 1 ){
				$cond = [];
				if( $last_id ){
					$cond['_id'] = ['$gt'=>$last_id];
				}
				$res = $con->find( $table['table'], $cond, ['limit'=>$perpage, 'sort'=>['_id'=>1] ]);
				if( !$res['data'] ){break;}
				foreach( $res['data'] as $i=>$j ){

					if( $_POST['exp']['type'] == "JSON" ){
						fwrite($fp, json_encode($j) . "\n" );
					}else if( $_POST['exp']['type'] == "CSV" ){
						$rec = [];
						foreach( $fields as $fn=>$f ){
							if( isset($j[ $fn ]) ){
								if( gettype($j[ $fn ]) == "array" ){
									$rec[]= "Array";
								}else{
									$rec[]= $j[ $fn ];
								}
							}else{
								$rec[]="";
							}
						}
						fputcsv($fp, $rec);
					}
					$last_id = $j['_id'];
				}
			}
			fclose($fp);
			chmod($tmfn, 0777);
			$sz = filesize($tmfn);
			if( $sz < 1024 ){
				$sz .= " Bytes";
			}else{
				$sz = round($sz/1024);
				if( $sz < 1024 ){
					$sz .= " KB";
				}else{
					$sz = round($sz/1024);
					if( $sz < 1024 ){
						$sz .= " MB";
					}
				}
			}
			event_log( "system", "database_table_export", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MongoDb",
				'table_id'=>$config_param5,
			]);
			json_response(['status'=>"success", "temp_fn"=>str_replace("/tmp/phpengine_backups/", "", $tmfn), "sz"=>$sz]);
			exit;
		
		}
		if( $_GET['action'] == "download_database_mongodb_snapshot" ){
			$fn = $_GET['snapshot_file'];
			$tmfn = "/tmp/phpengine_backups/". $fn;
			//ini_set('zlib.output_compression','On');
			header( 'Content-Type: application/x-download' );
			header( 'Transfer-Encoding: gzip' );
			header( 'Content-Length: '.filesize($tmfn) );
			header( 'Content-Disposition: attachment; filename="'.$fn.'"' );
			ob_start("ob_gzhandler");
			readfile($tmfn);
			ob_end_flush();
			exit;
		}

		if( $_POST['action'] == "database_mongodb_update_record" ){
			if( !isset($_POST['record_id']) || !isset($_POST['record']) ){
				json_response(['status'=>"fail","error"=>"payload incorrect"]);
			}
			if( $_POST['record_id']  == "new" ){
				if( isset($_POST["record"]["_id"]) ){
					if( $_POST["record"]["_id"] == "" || preg_match("/uniqueid/i",$_POST["record"]["_id"]) ){
						unset($_POST["record"]["_id"]);
					}
				}
				$res = $con->insert( $table["table"], $_POST['record'], "check" );
				if( $res['status'] == "success" ){
					$res = $con->find_one( $table["table"], ['_id'=>$res['inserted_id'] ] );

					event_log( "database_table", "record_create", [
						"app_id"=>$config_param1,
						'db_id'=>$config_param3, 
						"engine"=>"MongoDb",
						'table_id'=>$config_param5,
						"record_id"=>$res['inserted_id']
					]);

					json_response($res);
				}
				json_response($res);
			}else{
				$record_id = $_POST['record_id'];
				if( $record_id != $_POST["record"]["_id"] ){
					json_response("fail", "_id should not be changed while editing" );
				}
				$main_rec = $con->find_one( $table["table"],["_id"=> $record_id ] );
				if( !$main_rec['data'] ){
					json_response("fail", "Record not found" );
				}else{
					unset($_POST["record"]["_id"] );
					$res2 = $con->update_one( $table["table"], ["_id"=>$record_id ], $_POST['record'] );
					if( $res2['status'] == "success" ){
						$res = $con->find_one( $table["table"], ['_id'=>$record_id ] );
						event_log( "database_table", "record_edit", [
							"app_id"=>$config_param1,
							'db_id'=>$config_param3, 
							"engine"=>"MongoDb",
							'table_id'=>$config_param5,
							"record_id"=>$record_id
						]);
						json_response($res);
					}
					json_response($res2);
				}
			}
		}
		if($_POST['action'] == "database_mongodb_delete_record_multiple"){
			if( !isset($_POST['delete_ids']) ){
				json_response("fail", "Incorrect paylaod");
			}
			if( !is_array($_POST['delete_ids']) ){
				json_response("fail", "Incorrect paylaod");
			}
			foreach( $_POST["delete_ids"] as $i=>$j ){
				$res = $con->delete_one( $table['table'], ["_id"=>$j ] );
				if( $res['status'] == "fail" ){
					json_response($res);
				}
				event_log( "database_table", "record_delete", [
					"app_id"=>$config_param1,
					'db_id'=>$config_param3, 
					"engine"=>"MongoDb",
					'table_id'=>$config_param5,
					"record_id"=>$j
				]);
			}
			json_response("success","ok");
			exit;
		}

		if( $_POST['action'] == "database_mongodb_delete_record" ){
			$res = $con->delete_one( $table['table'], ["_id"=>$_POST['record_id'] ] );
			event_log( "database_table", "record_delete", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MongoDb",
				'table_id'=>$config_param5,
				"record_id"=>$_POST['record_id']
			]);
			json_response($res);
		}

	}
}
