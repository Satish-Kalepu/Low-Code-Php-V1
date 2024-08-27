<?php

require("cron_daemon_config.php");

sleep(2); // for proper logging of timestamp

$restart_mode = false;
register_shutdown_function("shutdown");

function shutdown(){
	global $mongodb_con;
	global $db_prefix;
	global $cron_daemon_thread_id;
	global $restart_mode;
	global $total; global $success; global $fail;
	logit("Shutdown");
}

set_error_handler(function($errno, $errstr, $errfile, $errline ){
	global $mongodb_con;
	global $db_prefix;
	global $cron_daemon_thread_id;
	logit("error", ['error'=>['errno'=>$errno,'err'=>$errstr, 'errfile'=>$errfile, 'line'=> $errline]] );
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}, E_ALL & ~E_WARNING & ~E_NOTICE);

function logit($event, $e=[]){
	global $mongodb_con;
	global $db_prefix;
	global $cron_daemon_thread_id;
	global $sysip;

	$d = [
		"date"=>date("Y-m-d H:i:s"),
		"tid"=>$cron_daemon_thread_id,
		"event"=>$event,
	];
	if( is_array($e) ){
		if(isset($e['_id'])){unset($e['_id']);}
		foreach( $e as $f=>$j ){
			$d[ $f ] = $j;
		}
	}elseif( is_string($e) && $e != "" ){
		$d[ 'data' ] = $e;
	}
	$res = $mongodb_con->insert( $db_prefix . "_zlog_home_import", $d);
	if( $res['inserted_id'] ){
		return true;
	}else{
		return false;
	}
}


$res = $mongodb_con->find_one( $db_prefix . "_settings", [
	"_id"=>"home_app_import"
]);
$mode = "create";
$fn = $res['data']['data']['restore_file'];

logit("open file", ["file"=>$fn] );

$res = $mongodb_con->update_one($db_prefix . "_settings", [
	"_id"=>"home_app_import"
],[
	"data.latest_time"=>date("Y-m-d H:i:s"),
	"data.status"=>"Running"
]);

	function import_replace_ids( $v ){
		global $all_ids;
		foreach( $v as $i=>$j ){
			if( gettype($j) == "string" ){
				if( strlen($j) == 24 ){
					if( isset( $all_ids[$j] ) ){
						$v[ $i ] = $all_ids[$j];
					}
				}
			}else if( gettype($j) == "array" ){
				$v[ $i ] = import_replace_ids( $j );
			}
		}
		return $v;
	}

	function import_insert_table_record( $t, $old_table_id, $dd ){
		global $all_ids;
		global $mongodb_con; global $db_prefix;

		unset($dd['__t']);

		$new_table_id = $all_ids[ $old_table_id ];
		$mongodb_con->insert( $db_prefix . "_dt_" . $new_table_id, $dd );
	}

	function import_insert_record( $t, $dd ){
		global $all_ids;
		global $mongodb_con; global $db_prefix;
		global $app_record;
		// echo $t . "\n--\n";
		// print_r( $dd );
		// echo "\n--\n";
		unset($dd['__t']);
		$dd = import_replace_ids( $dd );
		// print_r( $dd );
		// echo "\n-----\n";
		//exit;
		if( $t == "app" ){
			$import_app_original_name = $dd['app'];
			$dd['app'] = $import_app_original_name . "-Imported";
			$dd['des'] .= " Imported on " . date("Y-m-d");
			$appi = 2;
			while( 1 ){
				$res_app = $mongodb_con->find_one( $db_prefix . "_apps", [
					'app'=>$dd['app']
				]);
				if( $res_app['data'] ){
					$dd['app'] = $import_app_original_name . "-Imported". $appi;
					$appi++;
				}else{
					break;
				}
			}
			$dd['created'] = date("Y-m-d H:i:s");
			$dd['updated'] = date("Y-m-d H:i:s");
			$dd['last_updated'] = date("Y-m-d H:i:s");
			unset($dd['settings']);
			$app_record = $dd;
			$app_res = $mongodb_con->insert( $db_prefix . "_apps", $dd );
			if( $app_res['status'] != "success" ){
				json_response($app_res);
				logit("Error", ["error"=>$app_res] );exit;
			}
		}else if( $t == "apis" ){
			$v = $dd['version_part'];
			unset($dd['version_part']);
			$res = $mongodb_con->insert( $db_prefix . "_apis", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "apis";
				logit("Error", ["error"=>$res] );exit;
			}
			$res = $mongodb_con->insert( $db_prefix . "_apis_versions", $v );
			if( $res['status'] != "success" ){
				$res['part'] = "apis_versions";
				logit("Error", ["error"=>$res] );exit;
			}
		}else if( $t == "pages" ){
			$v = $dd['version_part'];
			unset($dd['version_part']);
			$res = $mongodb_con->insert( $db_prefix . "_pages", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "pages";
				logit("Error", ["error"=>$res] );exit;
			}
			$res = $mongodb_con->insert( $db_prefix . "_pages_versions", $v );
			if( $res['status'] != "success" ){
				$res['part'] = "pages_versions";
				logit("Error", ["error"=>$res] );exit;
			}
		}else if( $t == "functions" ){
			$v = $dd['version_part'];
			unset($dd['version_part']);
			$res = $mongodb_con->insert( $db_prefix . "_functions", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "functions";
				logit("Error", ["error"=>$res] );exit;
			}
			$res = $mongodb_con->insert( $db_prefix . "_functions_versions", $v );
			if( $res['status'] != "success" ){
				$res['part'] = "functions_versions";
				logit("Error", ["error"=>$res] );exit;
			}
		}else if( $t == "files" ){
			$res = $mongodb_con->insert( $db_prefix . "_files", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "files";
				logit("Error", ["error"=>$res] );exit;
			}
		}else if( $t == "storage_vaults" ){
			$res = $mongodb_con->insert( $db_prefix . "_storage_vaults", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "storage_vaults";
				logit("Error", ["error"=>$res] );exit;
			}
		}else if( $t == "tables_dynamic" ){
			$res = $mongodb_con->insert( $db_prefix . "_tables_dynamic", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "tables_dynamic";
				logit("Error", ["error"=>$res] );exit;
			}
			$res = $mongodb_con->create_collection( $db_prefix . "_dt_" . $dd['_id'] );
			if( $res['status'] != "success" ){
				$res['part'] = "create table_dynamic";
				logit("Error", ["error"=>$res] );exit;
			}
		}else if( $t == "databases" ){
			$res = $mongodb_con->insert( $db_prefix . "_databases", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "databases";
				logit("Error", ["error"=>$res] );exit;
			}
		}else if( $t == "tables" ){
			$res = $mongodb_con->insert( $db_prefix . "_tables", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "tables";
				logit("Error", ["error"=>$res] );exit;
			}
		}
	}

	$fp = fopen( $fn, "r" );
	$filestatus = "";
	$filestatus = fgets($fp, 4096);
	$bstats = explode(";", trim($filestatus));
	if( sizeof($bstats) < 2 ){
		logit("Error", ["error"=>"Archive Status line failed", "line"=>$filestatus] );exit;
	}
	$bst=[];
	foreach( $bstats as $i=>$j ){if( $j ){
		$x = explode(":",$j);
		$bst[ $x[0] ] = $x[1];
	}}
	logit("status", ["rec"=>$bst] );
	// $bst['BackupVersion'] = 1
	// $bst['AppVersion'] = 1
	// $bst['PasswordProtected'] = true/false

	$all_ids = [];
	$new_app_id = $mongodb_con->generate_id();
	$app_record = [];

	logit("new app id", ["id"=>$new_app_id] );	

	$simulate = true;
	$fpos = 0;
	$d = "";
	$cnt = 0;
	while( $line = trim(fgets($fp, 4096)) ){
		$fpos = ftell($fp);
		if( !trim($line) ){break;}
		if( $line == "--" ){
			if( $d ){
				//echo $d . "\n-----\n";
				$cnt++;
				if( $cnt%1000 == 0 ){
					logit("status", ["cnt"=>$cnt]);

					$res = $mongodb_con->update_one($db_prefix . "_settings", [
						"_id"=>"home_app_import"
					],[
						"data.latest_time"=>date("Y-m-d H:i:s"),
						"data.status"=>"Analysing " . $cnt . " records"
					]);
				}

				$t = "one";
				if( substr($d,0,3) == "dt_" ){
					$t = "table";
					$table = substr($d,3,24);
					//echo $table ."\n";
					//echo substr($d,28,99999);exit;
					//$dd = json_decode(substr($d,28,99999),true);
				}else if( substr($d,0,1) == "{" ){
					$dd = json_decode($d,true);
				}else{
					logit("Error", ["error"=>"Archive decryption failed at stage 5", "d"=>$d] );exit;
				}
				if( !is_array($dd) ){
					logit("Error", ["error"=>"Archive decryption failed at stage 6", "dd"=>$d] );exit;
				}

				if( $t == "table" ){
					//consider later
				}else{
					if( $dd['__t'] == "app" ){
						$all_ids[ $dd['_id'] ] = $new_app_id;
					}else if( $dd['__t'] == "files" ||  $dd['__t'] == "tables_dynamic" || $dd['__t'] == "databases"  || $dd['__t'] == "tables" || $dd['__t'] == "storage_vaults" ){
						$new_id = $mongodb_con->generate_id();
						$all_ids[ $dd['_id'] ] = $new_id;
					}else if( $dd['__t'] == "apis" || $dd['__t'] == "pages" || $dd['__t'] == "functions" ){
						$new_id = $mongodb_con->generate_id();
						$all_ids[ $dd['_id'] ] = $new_id;
						$new_idv = $mongodb_con->generate_id();
						$all_ids[ $dd['version_part']['_id'] ] = $new_idv;
					}else{
						logit("Error", ["error"=>"unknown type found: " . $dd['__t']] );exit;
					}
				}

			}
			$d = "";
		}else{
			$d.= $line;
		}
	}

	//print_r( $all_ids );
	logit("Stage2", ["ids count"=>sizeof($all_ids),"all_ids"=>$all_ids] );

	fseek($fp, 0);
	$filestatus = fgets($fp, 4096);

	logit("Rec", ["rec"=>$filestatus] );

	$cnt2 = 0;
	$fpos = 0;
	$d = "";
	while( $line = trim(fgets($fp, 4096)) ){
		$fpos = ftell($fp);
		if( !trim($line) ){
			logit("end found", ["ftell"=>$fpos] );
			break;
		}
		if( $line == "--" ){
			if( $d ){

				$cnt2++;
				if( $cnt2%1000 == 0 ){
					logit("status", ["cnt"=>$cnt2]);
					$res = $mongodb_con->update_one($db_prefix . "_settings", [
						"_id"=>"home_app_import"
					],[
						"data.latest_time"=>date("Y-m-d H:i:s"),
						"data.status"=>"Running. Processed " . round($cnt2/$cnt*100) . "% records"
					]);
				}

				//echo $d . "\n-----\n";
				$t = "one";
				if( substr($d,0,3) == "dt_" ){
					$t = "table";
					$table = substr($d,3,24);
					//echo $table ."\n";
					//echo substr($d,28,99999);exit;
					$dd = json_decode(substr($d,28,99999),true);
				}else if( substr($d,0,1) == "{" ){
					$dd = json_decode($d,true);
				}else{
					logit("Error", ["error"=>"Archive decryption failed at stage 5", "d"=>$d] );exit;
				}
				if( !is_array($dd) ){
					logit("Error", ["error"=>"Archive decryption failed at stage 6", "d"=>$d] );exit;
				}

				if( $t == "table" ){
					//logit("Record",["type"=>"table", "_id"=>$table] );
					import_insert_table_record( $t, $table, $dd );
				}else{
					logit("Record",["type"=>$dd['__t'], "_id"=>$dd['_id'] ] );
					import_insert_record( $dd['__t'], $dd );
				}

			}
			$d = "";
		}else{
			$d.= $line;
		}
	}

	logit("Status", ["cnt"=>$cnt] );

	logit("End");

	$res = $mongodb_con->update_one($db_prefix . "_settings", [
		"_id"=>"home_app_import"
	],[
		'$set'=>[
			"data.new_app_id"=>$new_app_id,
			"data.new_app"=>$app_record['app'],
			"data.status"=>"App Created Successfully"
		],
		'$unset'=>[
			"data.latest_time"=>1,
			"data.request_time"=>1,
		]
	]);


