<?php

// $config_param3 == "db-id";
// $config_param4 == "table";
// $config_param5 == "table-id";

$db['details']['username'] = pass_decrypt($db['details']['username']);
$db['details']['password'] = pass_decrypt($db['details']['password']);

//print_r( $db );exit;

$config_mysql_datatypes = [
	"int" => "number", 
	"tinyint" => "number", 
	"smallint" => "number", 
	"mediumint" => "number", 
	"bigint" => "number", 
	"decimal" => "number", 
	"float" => "number", 
	"double" => "number", 
	"char" => "text", 
	"varchar" => "text", 
	"text" => "text", 
	"tinytext" => "number", 
	"mediumtext" => "text", 
	"longtext" => "text", 
	"date" => "date", 
	"datetime" => "datetime", 
	"timestamp" => "datetime", 
];

$con_error = "";
mysqli_report(MYSQLI_REPORT_OFF);
$con = mysqli_connect( $db['details']['host'], $db['details']['username'], $db['details']['password'], $db['details']['database'], (int)$db['details']['port'] );
if( mysqli_connect_error() ){
	if( $_SERVER['REQUEST_METHOD'] == "POST" ){
		json_response(['status'=>'fail','error'=>"DBError: " .mysqli_connect_error() ]);
	}
	$con_error = mysqli_connect_error();
}

$res = mysqli_query($con, "show grants");
$grants = mysqli_fetch_all($res);
//print_r( $grants );
$roles = [];
foreach( $grants as $i=>$j ){
	preg_match("/grant (.*?)on/i", $j[0], $m);
	//print_r( $m );
	$x = explode(",",$m[1]);
	foreach( $x as $x1=>$x2 ){
		$roles[ trim($x2) ] = 1;
	}
}

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
function find_mysql_fields_structure2($rec){
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
			$fields[ $i ]['sub'] = find_mysql_fields_structure2($j);
		}
		if( $t == "list" ){
			$fields[ $i ]['sub'] = [];
			$fields[ $i ]['sub'][0] = find_mysql_fields_structure2($j[0]);
		}
	}
	return $fields;
}
function find_mysql_fields_structure( $recs ){
	$fields = [];
	foreach( $recs as $i=>$j ){
		$f = find_mysql_fields_structure2( $j );
		$fields = array_replace_recursive( $fields, $f );
	}
	return $fields;
}
/*Manage*/


function find_schema( $table ){
	//echo $table . "\n";
	global $con;
	$indexes = [];
	$res2 = mysqli_query( $con, "show indexes from `" . $table . "`" );
	$rows = mysqli_fetch_all( $res2, MYSQLI_ASSOC );
	//print_r( $rows );
	foreach( $rows as $i=>$j ){
		if( isset($indexes[ $j['Key_name'] ]) ){
			$indexes[ $j['Key_name'] ]['keys'][] = ["name"=>$j['Column_name'], "type"=>"text"];
		}else{
			$indexes[ $j['Key_name'] ] = [
				'name'=>$j['Key_name'],
				'keys'=>[ ["name"=>$j['Column_name'], "type"=>"text"] ],
				"unique"=>($j['Non_unique']==1?false:true),
			];
		}
	}
	$primary_field = $indexes[ 'PRIMARY' ]['keys'][0]['name'];
	// echo $table . "\n";
	// print_r( $indexes );

	$res2 = mysqli_query( $con, "describe `" . $table . "`");
	$rows = mysqli_fetch_all( $res2, MYSQLI_ASSOC );
	//print_r( $rows );
	$fields = [];
	foreach( $rows as $i=>$j ){
		$t = "text";
		if( preg_match("/(int|float|decimal)/i", $j['Type']) ){
			$t = "number";
		}
		$fields[ $j['Field'] ] = [
			"key"=>$j['Field'],
			"name"=>$j['Field'],
			"mapped_type"=>$j['Type'],
			"type"=>$t,
			"m"=>true,
			"order"=>1,
			"index"=>$j['Key'],
			"extra"=>$j['Extra'],
		];
	}
	return [ $fields, $indexes, $primary_field ];
}

if( $_POST['action'] == "database_mysql_load_tables" ){

	$q = "SELECT `TABLE_NAME`, `ENGINE`, `TABLE_ROWS`, `AVG_ROW_LENGTH`, `DATA_LENGTH`, `INDEX_LENGTH`, `CREATE_TIME`, `UPDATE_TIME`, `CHECK_TIME` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = 'sample1' ORDER BY `TABLES`.`TABLE_NAME` ASC";
	$res = mysqli_query($con, $q);
	$tables_res = mysqli_fetch_all($res,MYSQLI_ASSOC);
	//print_r( $tables );
	//exit;

	$tables = [];
	foreach( $res as $i=>$j ){
		$d = [];
		$d['table'] = $j['TABLE_NAME'];
		$d['size'] = $j['DATA_LENGTH'];
		$d['indexSize'] = $j['INDEX_LENGTH'];
		$d['count'] = $j['TABLE_ROWS'];
		//$d['indexDetails'] = $stats_res['0']['capped'];
		$tables[ $j['TABLE_NAME'] ] = $d;
	}
	//print_pre( $databases );
	//ksort($collections);

	$total_objects = 0;
	$total_datasize = 0;
	$total_storageSize = 0;
	$total_indexSize = 0;
	foreach( $tables as $table=>$d ){
		$total_objects += $d['count'];
		$total_datasize += $d['size'];
		$total_indexSize += $d['indexSize'];
	}

	//print_r( $collections );

	// $res = $con->list_collections();
	// print_r( $res );exit;

	$current_tables = [];
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
			$current_tables[ $j['table'] ] = $j;
		}
	}
	foreach( $tables as $table=>$cd ){

		list($fields,$indexes,$primary_field) = find_schema($table);

		//print_r( $rows );

		if( isset( $current_tables[ $table ] ) ){
			$current_tables[ $table ]['f'] = true;
			$tables[ $table ]['_id'] = $current_tables[ $table ]['_id'];
			$res_update = $mongodb_con->update_one( $config_api_tables, [
				"_id"=>$current_tables[ $table ]['_id']
			],[
				"keys"=> $indexes,
				"all_fields"=> $fields,
				"schema.default.fields" => $fields,
				"primary_field"=>$primary_field,
			]);
			if( $res_update['status'] != "success" ){
				//print_r( $res_insert
				json_response($res_update);
			}
		}else{
			$current_tables[ $table ]['f'] = true;
			$insert_data = [
				"table"=>$table,
				"des"=>$table,
				"engine"=>"MySql",
				"app_id"=>$config_param1,
				"db_id"=>$config_param3,
				"keys" => $indexes,
				"all_fields"=>$fields,
				"schema"=>[
					"default"=> [
						"name"		=> "Default",
						"fields" 	=> $fields,
					]
				],
				"primary_field"=>$primary_field,
			];
			$res_insert = $mongodb_con->insert( $config_api_tables, $insert_data );
			if( $res_insert['status'] != "success" ){
				//print_r( $res_insert
				json_response($res_insert);
			}
			event_log( "system", "database_table_create", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$res_insert['inserted_id'],
			]);
			$tables[ $ci ]['_id']= $res_insert['inserted_id'];
		}
	}
	//print_r( $tables );
	foreach( $current_tables as $table=>$j ){
		if( !isset($j['f']) ){
			$tables[ $table ] = [
				"_id"=>$j['_id'],
				"table"=>$table,"des"=>$table,
				'size' => 0,
				'count' => 0,
				'avgObjSize' => 0,
				'indexSize' => 0,
				"error"=>"Not found at source"
			];
		}
	}
	ksort($tables);
	json_response([
		'status'=>"success",
		"tables"=>$tables,
		"tot"=>[
			'objects'=> $total_objects,
			'datasize'=> $total_datasize,
			'indexSize'=> $total_indexSize,
		],
		"roles"=>$roles
	]);
}

if( $_POST['action'] == "database_mysql_create_table" ){

	if( !isset($_POST['new_table']) ){
		json_response("fail", "Table Details Missing!");
	}
	if( !preg_match("/^[a-z][a-z0-9\-\_]{1,50}$/i", $_POST['new_table']['name']) ){
		json_response("fail", "Table name incorrect!");
	}
	if( !is_array($_POST['new_table']['fields']) ){
		json_response("fail", "Table fields missing!");
	}
	if( !isset($_POST['new_table']['primary_field']) ){
		json_response("fail", "Table Details Missing!");
	}

	//CREATE TABLE `sample1`.`ssss` (`id` INT NOT NULL AUTO_INCREMENT , `name` INT NOT NULL , `age` VARCHAR(55) NOT NULL , `ddd` TINYINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

	$fields = [];
	foreach( $_POST['new_table']['fields'] as $i=>$j ){
		if( !preg_match("/^[a-z][a-z0-9\-\_]{1,50}$/i", $j['name']) ){
			json_response("fail", "Field name `". $j['name']."` invalid");
		}
		if( $j['type'] ==""){
			json_response("fail", "Field `". $j['name']."` type missing");
		}
		$t = $j['type'];
		$default = "";
		if( preg_match("/(char)/i", $j['type']) ){
			if( !is_numeric($j['length']) || $j['length'] == "" ){
				json_response("fail", "Field `". $j['name']."` length missing");
			}
			$t .= "(" . $j['length'] . ")";
		}
		if( $j['default'] ){
			$default = " DEFAULT '" . $j['default'] . "' ";
		}
		$ai = "";
		if( $_POST['new_table']['primary_field'] == $j['name'] ){
			if( $_POST['new_table']['ai'] ){
				$ai = "AUTO_INCREMENT";
			}
		}

		$fields[] = "`" . $j['name'] . "` " . $t . " NOT NULL " . $ai . $default;
	}
	$fields[] = "PRIMARY KEY (`".$_POST['new_table']['primary_field']."`)";

	if( $_POST['new_table']['primary_field'] != $_POST['new_table']['fields'][0]['name'] ){
		json_response("fail", "Primary field incorrect specification!");
	}

	$q = "CREATE TABLE `".$_POST['new_table']['name']."` ( ". implode(",\n",$fields) . " ) ";
	//$q.= "PRIMARY KEY (`".$_POST['new_table']['primary_field']."`)";
	//echo $q;

	mysqli_query($con, $q );
	if( mysqli_error($con) ){
		json_response([
			"status"=>"fail",
			"error"=>mysqli_error($con) 
		]);
	}

	json_response([
		"status"=>"success",
	]);

	exit;
}

if( $config_param4 == "table" && $config_param5 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param5 ) ){
		echo404("Incorrect URL! " . htmlspecialchars($config_param5) );
	}
	$table = $mongodb_con->find_one( $config_api_tables, [
		"db_id"=>$db['_id'], 
		"_id"=>$config_param5
	]);
	if( !$table['data'] ){
		echo404("Table Not Found!");
	}else{
		$table = $table['data'];
		if( !$con_error ){
			$res = mysqli_query($con, "select count(*) from `" . $table["table"] . "` " );
			if( mysqli_error($con) ){
				echo404( "Error fiding count: " . mysqli_error($con) );
			}
			$total_cnt = $row[0];
		}else{
			$total_cnt = 0;
		}

		if( $_POST['action'] == "database_mysql_save_schema" ){
			$res = $mongodb_con->update_one( $config_api_tables, [
				"_id"=>$config_param5
			],[
				"schema"=>$_POST['schema']
			] );
			event_log( "system", "database_table_update", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
			]);
			json_response($res);
		}

		if( $_POST['action'] == "database_mysql_load_records" ){
			//print_r( $_POST );exit;
			$fields = "`". implode("`, `", array_keys($table['schema'][ $_POST['schema'] ]['fields'])) . "`";
			$cond = [];
			$sort = [];
			if( $_POST['search_index'] == "primary" ){
				$sort[] = "`".$j['field'] . "` " . ($j['sort']=="asc"?"asc":"desc");
			}else{
				foreach( $_POST['index_search'] as $i=>$j ){
					$sort[] = "`".$j['field'] . "` " . ($j['sort']=="asc"?"asc":"desc");
					if( $j['v'] ){
						if( $j['c'] == "><"){
							$cond[] = "`".$j['field']. "` >= '" . mysqli_escape_string($con, $j['v']) . "' ";
							$cond[] = "`".$j['field']. "` <= '" . mysqli_escape_string($con, $j['v2']) . "' ";
						}else{
							$cond[] = "`".$j['field']. "` " . $j['c'] . " '" . mysqli_escape_string($con, $j['v']) . "' ";
						}
					}
				}
			}
			$where = " ";
			if( sizeof($cond) ){
				$where = " where " . implode(" and ", $cond);
			}
			$query = "select count(*) from `" . $table['table'] . "` " . $where;
			$res = mysqli_query($con, $query);
			if( mysqli_error($con) ){
				json_response([
					"status"=>"fail",
					"error"=>mysqli_error($con),
					"query"=>$query
				]);
			}
			$row = mysqli_fetch_array( $res );
			$total = $row[0];

			$query = "select " . $fields . " from `" . $table['table'] . "` 
			" . $where . " 
			order by " . implode(", ", $sort) . " 
			limit " . $_POST['limit'];
			$res = mysqli_query($con, $query);
			if( mysqli_error($con) ){
				json_response([
					"status"=>"fail",
					"error"=>mysqli_error($con),
					"query"=>$query
				]);
			}
			$records = [];
			while( $row = mysqli_fetch_assoc( $res ) ){
				$records[] = $row;
			}
			json_response("success", [
				"records"=>$records, 
				"total"=>$total, 
				"pages"=>ceil($total/$_POST['limit']),
				"q"=>$query
			]);
		}

		if( $_POST['action'] == "database_mysql_update_record" ){
			if( $_POST['record_id']  == "new" ){
				$fields = [];
				foreach( $_POST['record'] as $f=>$v ){
					$fields[] = "`". $f  . "` = '" . mysqli_escape_string( $con, $v ) . "' ";
				}
				$q = "insert into `". $table['table'] . "` set " . implode(",",$fields);
				mysqli_query($con, $q);
				if( mysqli_error($con) ){
					json_response("fail", mysqli_error($con) );
				}
				$record_id = mysqli_insert_id($con);
				event_log( "database_table", "record_create", [
					"app_id"=>$config_param1,
					'db_id'=>$config_param3, 
					"engine"=>"MySql",
					'table_id'=>$config_param5,
					"record_id"=>$record_id
				]);
			}else{
				$record_id = $_POST['record_id'];
				$res = mysqli_query($con, "select * from `". $table['table'] . "` where `".$_POST['primary_field']."` = '" . mysqli_escape_string($con,$record_id) . "' " );
				if( mysqli_error($con) ){
					json_response("fail", mysqli_error($con) );
				}
				$row = mysqli_fetch_assoc($res);
				if( !$row ){
					json_response("fail", "Record not found" );
				}
				$fields = [];
				foreach( $_POST['record'] as $f=>$v ){
					$fields[] = "`". $f  . "` = '" . mysqli_escape_string( $con, $v ) . "' ";
				}
				$q = "update `". $table['table'] . "` set " . implode(",",$fields) . " where  `".$_POST['primary_field']."` = '" . mysqli_escape_string($con,$record_id) . "' ";
				mysqli_query($con, $q);
				if( mysqli_error($con) ){
					json_response("fail", mysqli_error($con) );
				}
				event_log( "database_table", "record_edit", [
					"app_id"=>$config_param1,
					'db_id'=>$config_param3, 
					"engine"=>"MySql",
					'table_id'=>$config_param5,
					"record_id"=>$record_id
				]);
			}
			$q = "select * from `". $table['table'] . "` 
			where `".$_POST['primary_field']."` = '" . mysqli_escape_string($con, $record_id) . "' ";
			$res = mysqli_query($con, $q );
			if( mysqli_error($con) ){
				json_response("fail", mysqli_error($con) );
			}
			$row = mysqli_fetch_assoc($res);
			if( !$row ){
				json_response( "fail", "not found" );
			}else{
				json_response( "success", $row );
			}
		}

		if( $_POST['action'] == "database_mysql_delete_record_multiple" ){
			if( !isset($_POST['delete_ids']) ){
				json_response("fail", "Incorrect paylaod");
			}
			if( !is_array($_POST['delete_ids']) ){
				json_response("fail", "Incorrect paylaod");
			}
			foreach( $_POST["delete_ids"] as $i=>$j ){
				$ids[] = "'" . mysqli_escape_string($con, $j ) . "'";
			}
			$q = "delete from `". $table['table'] . "` 
			where `". $_POST['primary_field'] . "` in ( " . implode(",", $ids ) . " ) ";
			mysqli_query($con, $q );
			if( mysqli_error($con) ){
				json_response(['status'=>"fail", "error"=>mysqli_error($con), "q"=>$q]);
			}
			foreach( $_POST["delete_ids"] as $i=>$j ){
				event_log( "database_table", "record_delete", [
					"app_id"=>$config_param1,
					'db_id'=>$config_param3, 
					"engine"=>"MySql",
					'table_id'=>$config_param5,
					"record_id"=>$j
				]);
			}
			json_response("success","ok");
			exit;
		}

		if( $_POST['action'] == "database_mysql_delete_record" ){
			$q = "delete from `". $table['table'] . "` 
			where `". $_POST['primary_field'] . "` = '" . mysqli_escape_string($con, $_POST['record_id'] ) . "'";
			mysqli_query($con, $q );
			if( mysqli_error($con) ){
				json_response(['status'=>"fail", "error"=>mysqli_error($con), "q"=>$q]);
			}
			event_log( "database_table", "record_delete", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
				"record_id"=>$_POST['record_id']
			]);
			json_response("success","ok");
		}



		if( $_POST['action'] == "database_mysql_import_batch" ){

			//print_r( $_POST );exit;

			$t = validate_token("database_mysql_import_batch.". $config_param1 . "." .$table['_id'], $_POST['token']);
			if( $t != "OK" ){
				json_response("fail", $t);
			}

			$res = mysqli_query($con, "select count(*) as cnt from `". $table['table'] . "`");
			$row = mysqli_fetch_assoc($res);
			if( $row['cnt'] > 20000 ){
				json_response(['status'=>"fail", "error"=>"Table already has more than 20k records"]);
			}

			$success = 0;
			$skipped = 0;
			$error = "";
			$skipped_items = [];

			if( 1==2 ){
				$fields = "(".implode(",",array_keys($_POST['data'][0]) ).")";
				while( 1 ){
					$d = array_splice($_POST['data'],0,100);
					$vals = [];
					foreach( $d as $di=>$dd ){
						$x = array_values($dd);
						foreach( $x as $ii=>$jj ){
							$x[ $ii ] = "'" . mysqli_escape_string( $con, $jj ) . "'";
						}
						$vals[] = "(" . implode(", ", $x) . ")";
					}
					$q = "insert into `". $table['table'] . "` " . $fields . " values " . implode(",", $vals);
					mysqli_query( $con, $q );

					$res = $con->insert( $table['table'], $j );
					if( $res['status'] != "success" ){
						$error = $res['error'];
						break;
					}
					$success++;
				}
			}else{
				foreach( $_POST['data'] as $di=>$dd ){
					$vals = [];
					foreach( $dd as $ii=>$jj ){
						$vals[] = "`" . $ii . "` =  '" . mysqli_escape_string( $con, $jj ) . "'";
					}
					$q = "insert into `". $table['table'] . "` set " . implode(",", $vals);
					mysqli_query( $con, $q );
					if( mysqli_error($con) ){
						if( preg_match("/duplicate/", mysqli_error($con)) ){
							$skipped++;
							$skipped_items[] = $dd;
						}else{
							$error = mysqli_error($con);
							break;
						}
					}else{
						event_log( "database_table", "record_insert", [
							"app_id"=>$config_param1,
							'db_id'=>$config_param3, 
							"engine"=>"MySql",
							'table_id'=>$config_param5,
							"record_id"=>mysqli_insert_id($con)
						]);
					}
					$success++;
				}
			}

			event_log( "system", "database_table_import_data", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
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
					"error"=>$error,
					"q"=>$q,
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

		if( $_POST['action'] == "database_mysql_export" ){

			//print_r( $table );exit;

			if( !isset($_POST['exp']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect input"]);
			}
			if( !isset($_POST['exp']['type']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect input"]);
			}
			if( !preg_match("/^(JSON|CSV)$/", $_POST['exp']['type'] ) ){
				json_response(['status'=>"fail", "error"=>"Unhandled export type"]);
			}
			if( !isset($table['primary_field']) ){
				json_response(['status'=>"fail", "error"=>"Primary key not found"]);
			}
			$primary_field = $table['primary_field'];

			@mkdir( "/tmp/phpengine_backups/", 0777 );
			$tmfn = "/tmp/phpengine_backups/". preg_replace("/\W/", "", $table['table']) . "_" . date("Ymd_His") . "." . strtolower($_POST['exp']['type']);
			if( file_exists($tmfn) ){
				if( filemtime($tmfn) > time()-60 ){
					json_response(['status'=>"fail", "error"=>"An export was just invoked. please wait ".(60-(time()-filemtime($tmfn)))." seconds"]);
				}
			}
			$fp = fopen($tmfn, "w");

			if( $_POST['exp']['type'] == "CSV" ){
				$res = mysqli_query( $con, "select * from `". $table['table'] . "` limit 1");
				$row = mysqli_fetch_assoc($res);
				if( !$row ){
					json_response(['status'=>"fail", "error"=>"No records found"]);
				}
				$fields = $row;
				fputcsv($fp, array_keys($fields) );
			}

			$last_id = "";
			$perpage = 500;
			while( 1 ){
				$where = "";
				if( $last_id ){
					$where = " where `" . $primary_field . "` > '" . mysqli_escape_string( $con, $last_id ) . "' ";
				}
				$q = "select * from `" . $table['table'] . "` " . $where . " order by " . $primary_field . " limit " . $perpage;
				$res = mysqli_query( $con, $q);
				if( mysqli_error($con) ){
					json_response([ "status"=>"fail", "error"=>mysqli_error($con), "q"=>$q ]);
				}
				if( mysqli_num_rows($res) == 0 ){ break; }
				while( $row = mysqli_fetch_assoc($res) ){
					if( !isset($row[ $primary_field ]) ){
						json_response([ "status"=>"fail", "error"=>"primary field missing", "data"=>$row ]);
					}
					if( $_POST['exp']['type'] == "JSON" ){
						fwrite($fp, json_encode($row) . "\n" );
					}else if( $_POST['exp']['type'] == "CSV" ){
						$rec = [];
						foreach( $fields as $fn=>$f ){
							if( isset($row[ $fn ]) ){
								if( gettype($row[ $fn ]) == "array" ){
									$rec[]= "Array";
								}else{
									$rec[]= $row[ $fn ];
								}
							}else{
								$rec[]="";
							}
						}
						fputcsv($fp, $rec);
					}
					$last_id = $row[ $primary_field ];
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
				"engine"=>"MySql",
				'table_id'=>$config_param5,
			]);
			json_response(['status'=>"success", "temp_fn"=>str_replace("/tmp/phpengine_backups/", "", $tmfn), "sz"=>$sz]);
			exit;
		
		}
		if( $_GET['action'] == "download_database_mysql_snapshot" ){
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
		if( $_POST['action'] == "database_mysql_add_field" ){
			if( !isset($_POST['new_field']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect payload"]);
			}
			if( !isset($_POST['new_field']['name']) || !isset($_POST['new_field']['type']) || !isset($_POST['new_field']['length']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect payload"]);
			}
			if( preg_match("/^$[a-z][a-z0-9\-\_]{1,50}$/i", $_POST['new_field']['name']) ){
				json_response(['status'=>"fail", "error"=>"Name incorrect format"]);
			}

			$types = ["INT","VARCHAR","TEXT","TINYINT","SMALLINT","MEDIUMINT","INT","BIGINT","DECIMAL","FLOAT","DOUBLE","BOOLEAN","DATE","DATETIME","TIMESTAMP","TIME","CHAR","VARCHAR","TINYTEXT","TEXT","MEDIUMTEXT","LONGTEXT"];
			$type = $_POST['new_field']['type'];
			if( !in_array($type, $types) ){
				json_response(['status'=>"fail", "error"=>"Type not found"]);
			}
			if( $type == "VARCHAR" || $type == "CHAR" ){
				if( !is_numeric($_POST['new_field']['length']) ){
					json_response(['status'=>"fail", "error"=>"Length required for field `" . $_POST['new_field']['name'] . "` "]);
				}
				$type .= "(".$_POST['new_field']['length'].")";
			}
			$q = "ALTER TABLE `".$table['table']."` ADD `".$_POST['new_field']['name']."` ".$type." NOT NULL ";
			$q.= ($_POST['new_field']['pos']=="start"?"FIRST":("AFTER `".$_POST['new_field']['pos']."`"));
			mysqli_query( $con, $q );
			if( mysqli_error($con) ){
				json_response([
					"status"=>"fail",
					"error"=>mysqli_error($con),
					"q"=>$q
				]);
			}

			list($fields,$indexes,$primary_field) = find_schema($table['table']);

			$res = $mongodb_con->update_one( $config_api_tables, [
				"_id"=>$config_param5
			],[
				"all_fields"=>$fields,
				"schema.default.fields"=>$fields,
				"keys"=>$indexes,
			]);
			if( $res['status'] == "fail" ){
				json_response($res);
			}
			event_log( "database_table", "alter_table", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
			]);
			event_log( "system", "database_table_update", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
			]);
			json_response([
				"status"=>"success",
				"fields"=>$fields,
				"keys"=>$indexes
			]);
			exit;
		}
		if( $_POST['action'] == "database_mysql_edit_field" ){
			if( !isset($_POST['edit_field']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect payload"]);
			}
			if( !isset($_POST['edit_field']['name']) || !isset($_POST['edit_field']['type']) || !isset($_POST['edit_field']['length']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect payload"]);
			}
			if( preg_match("/^$[a-z][a-z0-9\-\_]{1,50}$/i", $_POST['edit_field']['name']) ){
				json_response(['status'=>"fail", "error"=>"Name incorrect format"]);
			}

			$types = ["INT","VARCHAR","TEXT","TINYINT","SMALLINT","MEDIUMINT","INT","BIGINT","DECIMAL","FLOAT","DOUBLE","BOOLEAN","DATE","DATETIME","TIMESTAMP","TIME","CHAR","VARCHAR","TINYTEXT","TEXT","MEDIUMTEXT","LONGTEXT"];
			$type = $_POST['edit_field']['type'];
			if( !in_array($type, $types) ){
				json_response(['status'=>"fail", "error"=>"Type not found"]);
			}
			if( $type == "VARCHAR" || $type == "CHAR" ){
				if( !is_numeric($_POST['edit_field']['length']) ){
					json_response(['status'=>"fail", "error"=>"Length required for field `" . $_POST['edit_field']['name'] . "` "]);
				}
				$type .= "(".$_POST['edit_field']['length'].")";
			}
			$q = "ALTER TABLE `".$table['table']."` CHANGE `".$_POST['edit_field']['current_name']."` `".$_POST['edit_field']['name']."` ".$type." NOT NULL ";
			if( $_POST['edit_field']['default'] ){
				$q.= " DEFAULT '" . $_POST['edit_field']['default'] . "' ";
			}
			mysqli_query( $con, $q );	
			if( mysqli_error($con) ){
				json_response([
					"status"=>"fail",
					"error"=>mysqli_error($con),
					"q"=>$q
				]);
			}

			list($fields,$indexes,$primary_field) = find_schema($table['table']);

			$res = $mongodb_con->update_one( $config_api_tables, [
				"_id"=>$config_param5
			],[
				"all_fields"=>$fields,
				"schema.default.fields"=>$fields,
				"keys"=>$indexes,
			]);
			if( $res['status'] == "fail" ){
				json_response($res);
			}
			event_log( "database_table", "alter_table", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
			]);
			event_log( "system", "database_table_update", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
			]);
			json_response([
				"status"=>"success",
				"fields"=>$fields,
				"keys"=>$indexes
			]);
			exit;
		}
		if( $_POST['action'] == "database_mysql_drop_field" ){
			if( !isset($_POST['field']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect payload"]);
			}

			$q  = "ALTER TABLE `".$table['table']."` DROP `".$_POST['field']."`";
			mysqli_query( $con, $q );	
			if( mysqli_error($con) ){
				json_response([
					"status"=>"fail",
					"error"=>mysqli_error($con),
					"q"=>$q
				]);
			}

			list($fields,$indexes,$primary_field) = find_schema($table['table']);

			$res = $mongodb_con->update_one( $config_api_tables, [
				"_id"=>$config_param5
			],[
				"all_fields"=>$fields,
				"schema.default.fields"=>$fields,
				"keys"=>$indexes,
			]);
			if( $res['status'] == "fail" ){
				json_response($res);
			}
			event_log( "database_table", "alter_table", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
				"event"=>"drop field"
			]);
			event_log( "system", "database_table_update", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
				"event"=>"drop field"
			]);
			json_response([
				"status"=>"success",
				"fields"=>$fields,
				"keys"=>$indexes
			]);
			exit;
			exit;
		}
		if( $_POST['action'] == "database_mysql_drop_index" ){
			if( !isset($_POST['index']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect payload"]);
			}

			$q  = "ALTER TABLE `".$table['table']."` DROP INDEX `".$_POST['index']."`";
			mysqli_query( $con, $q );	
			if( mysqli_error($con) ){
				json_response([
					"status"=>"fail",
					"error"=>mysqli_error($con),
					"q"=>$q
				]);
			}

			list($fields,$indexes,$primary_field) = find_schema($table['table']);

			$res = $mongodb_con->update_one( $config_api_tables, [
				"_id"=>$config_param5
			],[
				"all_fields"=>$fields,
				"schema.default.fields"=>$fields,
				"keys"=>$indexes,
			]);
			if( $res['status'] == "fail" ){
				json_response($res);
			}
			event_log( "database_table", "alter_table", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
				"event"=>"drop index"
			]);
			event_log( "system", "database_table_update", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
				"event"=>"drop index"
			]);
			json_response([
				"status"=>"success",
				"fields"=>$fields,
				"keys"=>$indexes
			]);
			exit;
			exit;
		}

		if( $_POST['action'] == "database_mysql_add_index" ){
			if( !isset($_POST['new_index']) ){
				json_response(['status'=>"fail", "error"=>"Incorrect payload"]);
			}
			$n = $_POST['new_index']['name'];
			$t = ($_POST['new_index']['unique']?"UNIQUE":"INDEX");
			$k = [];
			foreach( $_POST['new_index']['keys'] as $i=>$j ){
				$k[] = "`".$j['name']."`";
			}
			$q = "ALTER TABLE `".$table['table']."` ADD ".($t)." `".$n."` (".implode(",",$k).")";
			mysqli_query( $con, $q );
			if( mysqli_error($con) ){
				json_response([
					"status"=>"fail",
					"error"=>mysqli_error($con),
					"q"=>$q
				]);
			}

			list($fields,$indexes,$primary_field) = find_schema($table['table']);

			$res = $mongodb_con->update_one( $config_api_tables, [
				"_id"=>$config_param5
			],[
				"all_fields"=>$fields,
				"schema.default.fields"=>$fields,
				"keys"=>$indexes,
			]);
			if( $res['status'] == "fail" ){
				json_response($res);
			}
			event_log( "database_table", "alter_table", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
				"event"=>"add index"
			]);
			event_log( "system", "database_table_update", [
				"app_id"=>$config_param1,
				'db_id'=>$config_param3, 
				"engine"=>"MySql",
				'table_id'=>$config_param5,
				"event"=>"add index"
			]);
			json_response([
				"status"=>"success",
				"fields"=>$fields,
				"keys"=>$indexes
			]);
			exit;
		}
	}
}
