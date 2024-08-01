<?php

/*
	need to add objects

tables:
apps
settings
cloud_domains
users
user_keys
	app_id, t
	expiret
	expire
user_pool
user_roles
apis
apis_version
captcha    need ttl 
databases
tables
tables_dynamic
files
storage_vaults
functions
functions_versions
pages
pages_versions
queues
graph_things
graph_keywords


zlog_actions  = apimaker action log
zlog_requests = engine request log


*/

if( $_POST['action'] == "exports_get_snapshots" ){
	$f = [];
	if( is_dir("/tmp/phpengine_backups/") ){
		$fp = dir("/tmp/phpengine_backups/");
		while( $fn = $fp->read() ){if( preg_match("/^[a-f0-9]+\_[a-f0-9]{24}\_[0-9]+\_[0-9]+\.gz$/", $fn) ){
			$f[] = $fn;
		}}
		sort($f);
	}
	json_response(['status'=>'success', 'snapshots'=>$f]);
}

function enc_data( $data ){
	global $pass;
	if( $pass ){
		$encrypted = openssl_encrypt($data, "aes256", "abcdef".$pass);
	}else{
		$encrypted = openssl_encrypt($data, "aes256", "abcdef");
	}
	return $encrypted;
}
function dec_data( $data ){
	global $pass;
	if( $pass ){
		$encrypted = openssl_decrypt($data, "aes256", "abcdef".$pass);
	}else{
		$encrypted = openssl_decrypt($data, "aes256", "abcdef");
	}
	return $encrypted;
}

if( $_POST['action'] == "app_backup" ){
	// $t = validate_token("backupnow.". $config_param1, $_POST['token']);
	// if( $t != "OK" ){
	// 	json_response("fail", $t);
	// }

	//header("Content-Type: text/plain");

	@mkdir("/tmp/phpengine_backups/", 0777);
	$tmfn = "/tmp/phpengine_backups/". preg_replace("/\W/", "", $app['app']) . "_" . $app['_id'] . "_" . date("Ymd_His");
	$fp = fopen($tmfn, "w");
	$pass = $_POST['backup_pass'];

	$data = "";
	if( $_POST['backup_pass'] ){
		$pass = $_POST['backup_pass'];
		$line = "BackupVersion:1;AppVersion:1;PasswordProtected:true;Hash:" . pass_hash2( $pass, "version1" );
	}else{
		$pass = "version1";
		$line = "BackupVersion:1;AppVersion:1;PasswordProtected:false";
	}
	fwrite($fp, $line . "\n--\n" );
	$app['__t'] = "app";
	fwrite($fp, enc_data(json_encode($app),$pass) . "\n--\n" );
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_apis", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "apis";
		$res2 = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [
			'_id'=>$j['version_id']
		]);
		$j['version_part'] = $res2['data'];
		fwrite($fp, enc_data(json_encode($j),$pass) . "\n--\n" );
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "pages";
		$res2 = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
			'_id'=>$j['version_id']
		]);
		$j['version_part'] = $res2['data'];
		fwrite($fp, enc_data(json_encode($j),$pass) . "\n--\n" );
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_functions", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "functions";
		$res2 = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", [
			'_id'=>$j['version_id']
		]);
		$j['version_part'] = $res2['data'];
		fwrite($fp, enc_data(json_encode($j),$pass) . "\n--\n" );
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_databases", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "databases";
		fwrite($fp, enc_data(json_encode($j),$pass) . "\n--\n" );
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_tables", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "tables";
		fwrite($fp, enc_data(json_encode($j),$pass) . "\n--\n" );
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_tables_dynamic", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "tables_dynamic";
		fwrite($fp,enc_data(json_encode($j)) . "\n--\n");
		$res2 = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_dt_" . $j['_id'], [
		]);
		foreach( $res2['data'] as $di=>$dj ){
			fwrite($fp,"dt_" . $j['_id'] . ":" . json_encode($dj) . "\n--\n");
		}
	}

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_files", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "files";
		fwrite($fp, json_encode($j) . "\n--\n");
	}

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "storage_vaults";
		fwrite($fp, json_encode($j) . "\n--\n");
	}
	fclose($fp);
	chmod($tmfn, 0777);
	//echo $tmfn;
	exec("gzip " . $tmfn);
	$tmfn .= ".gz";
	chmod($tmfn, 0777);

	event_log( "system", "app_export", [
		"app_id"=>$config_param1, 
		"temp_fn"=>str_replace("/tmp/phpengine_backups/", "", $tmfn), 
		"sz"=>filesize($tmfn)
	]);

	json_response(['status'=>"success", "temp_fn"=>str_replace("/tmp/phpengine_backups/", "", $tmfn), "sz"=>filesize($tmfn)]);
	exit;
}

if( $_GET['action'] == "download_snapshot" ){
	$fn = $_GET['snapshot_file'];

	event_log( "system", "app_download_snapshot", [
		"app_id"=>$config_param1, 
	]);

	$tmfn = "/tmp/phpengine_backups/". $fn;
	ini_set('zlib.output_compression','Off');
	header('Content-Type: application/x-download');
	//header('Content-Encoding: gzip'); #
	header('Content-Length: '.filesize($tmfn)); #
	header('Content-Disposition: attachment; filename="'.$fn.'"');
	readfile($tmfn);
	exit;
}

if( $_POST['action'] == "exports_restore_upload" ){
	if( file_exists( $_FILES['file']['tmp_name'] ) && filesize($_FILES['file']['tmp_name']) > 0  ){
		if( !preg_match("/^(([A-Za-z0-9]+)\_([a-f0-9]{24})\_([0-9]{8})\_([0-9]{6}))\.gz$/", $_FILES['file']['name'], $file_match) ){
			json_response(['status'=>"fail","error"=>"Filename format mismatch"]);
		}
		@mkdir( "/tmp/phpengine_snapshots_uploaded/", 0777 );
		$fn = "/tmp/phpengine_snapshots_uploaded/" . $file_match[1] . ".gz";
		move_uploaded_file( $_FILES['file']['tmp_name'], $fn );
		if( !file_exists($fn) ){
			json_response(['status'=>"fail","error"=>"File upload failed 2"]);
		}
		$st = exec("gzip --uncompress " . $fn, $out);
		if( $st === false ){
			json_response(['status'=>"fail", "error"=>"File uncompress failed"]);
		}

		$fn = "/tmp/phpengine_snapshots_uploaded/" . $file_match[1];
		$fn2 = "/tmp/phpengine_snapshots_uploaded/" . $file_match[1] . "_decrypted";
		$fp = fopen( $fn, "r" );
		$fp2 = fopen( $fn2, "w" );
		$filestatus = "";
		$filestatus = fgets($fp, 4096);
		fwrite($fp2, $filestatus);
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
		if( $bst['PasswordProtected'] == "true" ){
			if( !$_POST['pwd'] || !$_POST['pass'] ){
				json_response(['status'=>"fail","error"=>"Archive is password protected. Please provide password"]);
			}
			$newhash = pass_hash2( $_POST['pass'], "version1" );
			if( $bst['Hash'] == $newhash ){
				echo "all ok";
			}else{
				json_response(['status'=>"fail","error"=>"Incorrect password"]);
			}
			$pass = $_POST['pass'];
		}else{
			$pass = "version1";
		}

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
					$dd = false;
					if( substr($d,0,3) == "dt_" ){
						$t = "table";
						$table = substr($d,3,24);
						//echo $table ."\n";
						//echo substr($d,28,99999);exit;
						$dd = json_decode(substr($d,28,99999),true);
					}else if( substr($d,0,1) == "{" ){
						$dd = json_decode($d,true);
					}else{
						$dd = dec_data($d, $pass);
						if( $dd == null || $dd == "" ){
							json_response(['status'=>"fail","error"=>"Archive decryption failed at stage 5"]);
						}
						$dd = json_decode( $dd,true);
					}
					if( !is_array($dd) ){
						json_response(['status'=>"fail","error"=>"Archive decryption failed at stage 6", "dd"=>$d]);
					}
					fwrite($fp2, json_encode($dd) . "\n--\n");
					if( $t == "table" ){
						$dd['__t'] = "dt";
					}
					if( $dd['__t'] == "app" ){
						$datasets[ $dd['__t'] ] = $dd;
					}else{
						$datasets[ $dd['__t'] ][] = $dd['_id'];
					}
				}
				$d = "";
			}else{
				$d.= $line;
			}
		}

		fclose($fp);
		fclose($fp2);
		chmod($fn2, 0777);

		$tot = 0;
		$datasets2 = [];
		foreach( $datasets as $i=>$j ){
			if( $i != "app" ){
				$tot+=sizeof($j);
				$datasets2[ $i ] = sizeof($j);
			}
		}

		event_log( "system", "app_restore_upload", [
			"app_id"=>$config_param1, 
		]);

		if( $app['_id'] == $datasets['app']['_id'] ){
			$vt = time();
			$_SESSION['restore_rand'] = $vt;
			$_SESSION['restore_file'] = $fn2;
			$dt = substr($file_match[4],0,4) . "-" .substr($file_match[4],4,2) . "-" .substr($file_match[4],6,2) . " " . substr($file_match[5],0,2) . ":" .substr($file_match[5],2,2);
			json_response(['status'=>"success2", "tot"=>$tot, "date"=>$dt, "summary"=>$datasets2, "rand"=>$vt]);

		}else if( $app['_id'] != $datasets['app']['_id'] ){
			$res2 = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apps", [
				'_id'=>$datasets['app']['_id']
			]);
			if( $res2['data'] ){
				json_response(['status'=>"success3", "app"=>$res2['data']]);
			}
		}

	}else{
		json_response(['status'=>"fail","error"=>"File upload failed"]);
	}
	exit;
}

if(  $_POST['action'] == "exports_restore_upload_confirm"   ){
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
				if( strlen($i) == 24 ){
					if( isset( $all_ids[$j] ) ){
						$v[ $i ] = $all_ids[$j];
					}
				}
			}else if( gettype($j) == "array" ){
				replace_ids( $j );
			}
		}
	}

	if( $mode == "create" ){

		$ids = [
			'app'=>[],'apis'=>[],'pages'=>[],'functions'=>[],'apis'=>[],'files'=>[],'tables_dynamic'=>[],'databases'=>[], 'storage_vaults'=>[],
		];
		$all_ids = [];

		$new_app_id = $mongodb_con->generate_id();
		$ids['app'][ $datasets['app']['_id'] ] = $new_app_id;
		$all_ids[ $datasets['app']['_id'] ] = $new_app_id;
		$table_ids = [];
		$datasets['app']['_id'] = $new_app_id;
		foreach( $datasets['apis'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['apis'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$datasets['apis'][ $i ]['_id'] = $new_id;
			$datasets['apis'][ $i ]['app_id'] = $new_app_id;
		}
		foreach( $datasets['pages'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['pages'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$datasets['pages'][ $i ]['_id'] = $new_id;
			$datasets['pages'][ $i ]['app_id'] = $new_app_id;
		}
		foreach( $datasets['functions'] as $i=>$j ){
			$new_id = $mongodb_con->generate_id();
			$ids['functions'][ $j['_id'] ] = $new_id;
			$all_ids[ $j['_id'] ] = $new_id;
			$datasets['functions'][ $i ]['_id'] = $new_id;
			$datasets['functions'][ $i ]['app_id'] = $new_app_id;
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
	}else{

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


if( 1==2 ){
				if( $d['__t'] == "app" ){
					unset($d['__t']);
					$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apps", $d );
				}else if( $d['__t'] == "apis" ){
					unset($d['__t']);
					$v = $d['version_part'];
					unset($d['version_part']);
					$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis", $d );
					$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", $v );
				}else if( $d['__t'] == "pages" ){
					unset($d['__t']);
					$v = $d['version_part'];
					unset($d['version_part']);
					$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_pages", $d );
					$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", $v );
				}else if( $d['__t'] == "functions" ){
					unset($d['__t']);
					$v = $d['version_part'];
					unset($d['version_part']);
					$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_functions", $d );
					$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_functions_versions", $v );
				}else if( $d['__t'] == "tables_dynamic" ){
					unset($d['__t']);
					$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_tables_dynamic", $d );
				}else if( $d['__t'] == "files" ){
					unset($d['__t']);
					$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_files", $d );
				}else if( $t == "table" ){
					$mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_dt_" . $table, $d );
				}
}
