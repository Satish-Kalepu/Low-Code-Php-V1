<?php

if(  $_POST['action'] == "home_restore_upload_confirm2" ){
	if( !file_exists($_SESSION['restore_file']) || $_SESSION['restore_rand'] != $_POST['rand'] ){
		json_response(['status'=>"fail","error"=>"Incorrect confirm parameters"]);
	}

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
			}
		}else if( $t == "apis" ){
			$v = $dd['version_part'];
			unset($dd['version_part']);
			$res = $mongodb_con->insert( $db_prefix . "_apis", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "apis";
				json_response($res);
			}
			$res = $mongodb_con->insert( $db_prefix . "_apis_versions", $v );
			if( $res['status'] != "success" ){
				$res['part'] = "apis_versions";
				json_response($res);
			}
		}else if( $t == "pages" ){
			$v = $dd['version_part'];
			unset($dd['version_part']);
			$res = $mongodb_con->insert( $db_prefix . "_pages", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "pages";
				json_response($res);
			}
			$res = $mongodb_con->insert( $db_prefix . "_pages_versions", $v );
			if( $res['status'] != "success" ){
				$res['part'] = "pages_versions";
				json_response($res);
			}
		}else if( $t == "functions" ){
			$v = $dd['version_part'];
			unset($dd['version_part']);
			$res = $mongodb_con->insert( $db_prefix . "_functions", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "functions";
				json_response($res);
			}
			$res = $mongodb_con->insert( $db_prefix . "_functions_versions", $v );
			if( $res['status'] != "success" ){
				$res['part'] = "functions_versions";
				json_response($res);
			}
		}else if( $t == "files" ){
			$res = $mongodb_con->insert( $db_prefix . "_files", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "files";
				json_response($res);
			}
		}else if( $t == "storage_vaults" ){
			$res = $mongodb_con->insert( $db_prefix . "_storage_vaults", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "storage_vaults";
				json_response($res);
			}
		}else if( $t == "tables_dynamic" ){
			$res = $mongodb_con->insert( $db_prefix . "_tables_dynamic", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "tables_dynamic";
				json_response($res);
			}
			$res = $mongodb_con->create_collection( $db_prefix . "_dt_" . $dd['_id'] );
			if( $res['status'] != "success" ){
				$res['part'] = "create table_dynamic";
				json_response($res);
			}
		}else if( $t == "databases" ){
			$res = $mongodb_con->insert( $db_prefix . "_databases", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "databases";
				json_response($res);
			}
		}else if( $t == "tables" ){
			$res = $mongodb_con->insert( $db_prefix . "_tables", $dd );
			if( $res['status'] != "success" ){
				$res['part'] = "tables";
				json_response($res);
			}
		}
	}

	$mode = "create";

	$fn = $_SESSION['restore_file'];

	$fp = fopen( $fn, "r" );
	$filestatus = "";
	$filestatus = fgets($fp, 4096);
	$bstats = explode(";", trim($filestatus));
	if( sizeof($bstats) < 2 ){
		json_response(['status'=>"fail","error"=>"Archive Status line failed", "line"=>$filestatus]);
	}
	$bst=[];
	foreach( $bstats as $i=>$j ){if( $j ){
		$x = explode(":",$j);
		$bst[ $x[0] ] = $x[1];
	}}
	// $bst['BackupVersion'] = 1
	// $bst['AppVersion'] = 1
	// $bst['PasswordProtected'] = true/false

	$all_ids = [];
	$new_app_id = $mongodb_con->generate_id();
	$app_record = [];

	$simulate = true;
	$fpos = 0;
	$d = "";
	while( $line = trim(fgets($fp, 4096)) ){
		$fpos = ftell($fp);
		if( !trim($line) ){break;}
		if( $line == "--" ){
			if( $d ){
				//echo $d . "\n-----\n";
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
					json_response(['status'=>"fail","error"=>"Archive decryption failed at stage 5", "d"=>$d]);
				}
				if( !is_array($dd) ){
					json_response(['status'=>"fail","error"=>"Archive decryption failed at stage 6", "dd"=>$d]);
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
						json_response("fail", "unknown type found: " . $dd['__t']);
					}
				}

			}
			$d = "";
		}else{
			$d.= $line;
		}
	}

	print_r( $all_ids );

	fseek($fp, 0);
	$filestatus = fgets($fp, 4096);

	$fpos = 0;
	$d = "";
	while( $line = trim(fgets($fp, 4096)) ){
		$fpos = ftell($fp);
		if( !trim($line) ){break;}
		if( $line == "--" ){
			if( $d ){
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
					json_response(['status'=>"fail","error"=>"Archive decryption failed at stage 5", "d"=>$d]);
				}
				if( !is_array($dd) ){
					json_response(['status'=>"fail","error"=>"Archive decryption failed at stage 6", "dd"=>$d]);
				}

				if( $t == "table" ){
					import_insert_table_record( $t, $table, $dd );
				}else{
					import_insert_record( $dd['__t'], $dd );
				}

			}
			$d = "";
		}else{
			$d.= $line;
		}
	}
	
	event_log( "system", "app_restore_upload_confirm", [
		"app_id"=>$new_app_id, 
	]);
	json_response(['status'=>"success","new_app_id"=>$new_app_id, "app"=>$app_record['app'] ]);

	exit;
}

