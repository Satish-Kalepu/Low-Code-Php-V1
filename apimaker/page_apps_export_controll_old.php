<?php


if(  $_POST['action'] == "exports_restore_upload_confirm" ){
	if( !file_exists($_SESSION['restore_file']) || $_SESSION['restore_rand'] != $_POST['rand'] ){
		json_response(['status'=>"fail","error"=>"Incorrect confirm parameters"]);
	}

	$mode = $_POST['option'];

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

	$simulate = true;
	$datasets = [];

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
					$datasets[ "dt" ][ $table ][] = $dd;
				}else{
					if( $dd['__t'] == "app" ){
						$datasets[ $dd['__t'] ] = $dd;
					}else{
						$datasets[ $dd['__t'] ][] = $dd;
					}
				}

			}
			$d = "";
		}else{
			$d.= $line;
		}
	}

	function replace_ids( &$v ){
		global $all_ids;
		foreach( $v as $i=>$j ){
			if( gettype($j) == "string" ){
				if( strlen($j) == 24 ){
					if( isset( $all_ids[$j] ) ){
						$v[ $i ] = $all_ids[$j];
					}
				}
			}else if( gettype($j) == "array" ){
				replace_ids( $j );
			}
		}
	}

	if( $mode == "create" || $mode == "replace_with_other" ){

		$ids = [
			'app'=>[],'apis'=>[],'pages'=>[],'functions'=>[],'apis'=>[],'files'=>[],'tables_dynamic'=>[],'databases'=>[], 'storage_vaults'=>[],
		];
		$all_ids = [];

		if( $mode == "create" ){
			$new_app_id = $mongodb_con->generate_id();
		}else{
			$new_app_id = $app['_id'];
		}
		$ids['app'][ $datasets['app']['_id'] ] = $new_app_id;
		$all_ids[ $datasets['app']['_id'] ] = $new_app_id;
		$table_ids = [];
		$datasets['app']['_id'] = $new_app_id;
		if( $mode != "create" ){
			$datasets['app']['app'] = $app['app'];
			$datasets['app']['des'] = $app['des'];
			$datasets['app']['updated'] = date("Y-m-d H:i:s");
			$datasets['app']['last_updated'] = date("Y-m-d H:i:s");
			$datasets['app']['settings'] = $app['settings'];
		}else{
			unset($datasets['app']['settings']);
		}
		foreach( $datasets['apis'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['apis'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$new_version_id = $mongodb_con->generate_id();
			$all_ids[ $j['version_part']['_id'] ] = $new_version_id;
			$datasets['apis'][ $i ]['_id'] = $new_id;
			$datasets['apis'][ $i ]['app_id'] = $new_app_id;
			$datasets['apis'][ $i ]['version_id'] = $new_version_id;
			$datasets['apis'][ $i ]['version_part']['_id'] = $new_version_id;
		}
		foreach( $datasets['pages'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['pages'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$new_version_id = $mongodb_con->generate_id();
			$all_ids[ $j['version_part']['_id'] ] = $new_version_id;
			$datasets['pages'][ $i ]['_id'] = $new_id;
			$datasets['pages'][ $i ]['app_id'] = $new_app_id;
			$datasets['pages'][ $i ]['version_id'] = $new_version_id;
			$datasets['pages'][ $i ]['version_part']['_id'] = $new_version_id;
		}
		foreach( $datasets['functions'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['functions'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$new_version_id = $mongodb_con->generate_id();
			$all_ids[ $j['version_part']['_id'] ] = $new_version_id;
			$datasets['functions'][ $i ]['_id'] = $new_id;
			$datasets['functions'][ $i ]['app_id'] = $new_app_id;
			$datasets['functions'][ $i ]['version_id'] = $new_version_id;
			$datasets['functions'][ $i ]['version_part']['_id'] = $new_version_id;
		}
		foreach( $datasets['files'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['files'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$datasets['files'][ $i ]['_id'] = $new_id;
			$datasets['files'][ $i ]['app_id'] = $new_app_id;
		}
		foreach( $datasets['tables_dynamic'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['tables_dynamic'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$table_ids[ $new_id ] = $j['_id'];
			$datasets['tables_dynamic'][ $i ]['_id'] = $new_id;
			$datasets['tables_dynamic'][ $i ]['app_id'] = $new_app_id;
		}
		foreach( $datasets['databases'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['databases'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$datasets['databases'][ $i ]['_id'] = $new_id;
			$datasets['databases'][ $i ]['app_id'] = $new_app_id;
		}
		foreach( $datasets['tables'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['tables'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$datasets['tables'][ $i ]['_id'] = $new_id;
			$datasets['tables'][ $i ]['app_id'] = $new_app_id;
			$datasets['tables'][ $i ]['db_id'] = $ids['databases'][ $datasets['tables'][ $i ]['db_id'] ];
		}
		foreach( $datasets['storage_vaults'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['storage_vaults'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$datasets['storage_vaults'][ $i ]['_id'] = $new_id;
			$datasets['storage_vaults'][ $i ]['app_id'] = $new_app_id;
		}
	}

	if( $mode == "replace" || $mode == "replace_with_other" ){

		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_apps", ['_id'=>$app['_id']] );
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_apis", ['app_id'=>$app['_id']] );
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", ['app_id'=>$app['_id']] );
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_pages", ['app_id'=>$app['_id']] );
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", ['app_id'=>$app['_id']] );
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_functions", ['app_id'=>$app['_id']] );
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", ['app_id'=>$app['_id']] );		
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_files", ['app_id'=>$app['_id']] );
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults", ['app_id'=>$app['_id']] );
		$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_tables_dynamic", ['app_id'=>$app['_id']] );
		foreach( $res['data'] as $i=>$j ){
			$mongodb_con->drop_collection( $config_global_apimaker['config_mongo_prefix'] . "_dt_" . $j['_id'] );
		}
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_tables_dynamic", ['app_id'=>$app['_id']] );
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_databases", ['app_id'=>$app['_id']] );
		$mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_tables", ['app_id'=>$app['_id']] );

	}

	$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apps", $datasets['app'] );
	foreach( $datasets['apis'] as $i=>$j ){
		if( $mode == "create" ){ replace_ids( $j ); }
		$v = $j['version_part'];
		unset($j['version_part']);
		$v['api_id'] = $j['_id'];
		$v['app_id'] = $j['app_id'];
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis", $j );
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", $v );
	}
	foreach( $datasets['pages'] as $i=>$j ){
		$v = $j['version_part'];
		unset($j['version_part']);
		$v['page_id'] = $j['_id'];
		$v['app_id'] = $j['app_id'];
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_pages", $j );
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", $v );
	}
	foreach( $datasets['functions'] as $i=>$j ){
		if( $mode == "create" ){ replace_ids( $j ); }
		$v = $j['version_part'];
		unset($j['version_part']);
		$v['api_id'] = $j['_id'];
		$v['app_id'] = $j['app_id'];
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_functions", $j );
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", $v );
	}
	foreach( $datasets['files'] as $i=>$j ){
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", $j );
	}
	foreach( $datasets['storage_vaults'] as $i=>$j ){
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults", $j );
	}
	foreach( $datasets['tables_dynamic'] as $i=>$j ){
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_tables_dynamic", $j );
		$mongodb_con->create_collection( $config_global_apimaker['config_mongo_prefix'] . "_dt_" . $j['_id'] );
		if( isset($table_ids[ $j['_id'] ]) ){
			$oid = $table_ids[ $j['_id'] ];
		}else{
			$oid = $j['_id'];
		}
		foreach( $datasets['dt'][ $oid ] as $ti=>$tj ){
			$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_dt_" . $j['_id'], $tj );
		}
	}
	foreach( $datasets['databases'] as $i=>$j ){
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_databases", $j );
	}
	foreach( $datasets['tables'] as $i=>$j ){
		$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_tables", $j );
	}

	if( $mode == "create" ){
		event_log( "system", "app_restore_upload_confirm", [
			"app_id"=>$new_app_id, 
		]);
		json_response(['status'=>"success","new_app_id"=>$new_app_id ]);
	}else{
		event_log( "system", "app_restore_upload_confirm", [
			"app_id"=>$config_param1, 
		]);
		json_response(['status'=>"success"]);
	}

	exit;
}


