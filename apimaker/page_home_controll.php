<?php

//phpinfo();exit;

$ids_names = [
	"app"=> "App",
	"apis"=> "Apis",
	"pages"=> "Pages",
	"tables_dynamic"=> "Internal Tables",
	"dt"=> "Table Records",
	"files"=> "Files",
	"storage_vaults"=> "Storage Vaults"
];


$restore_task = false;
$restore_status = "";
$sres = $mongodb_con->find_one( $db_prefix . "_settings", ['_id'=>"home_app_import"] );
if( $sres['data'] ){
	$restore_status = $sres['data']['data']['status'];
	if( !isset($sres['data']['data']['latest_time']) ){
		if( $sres['data']['data']['request_time'] > date("Y-m-d H:i:s", time()-60 ) ){
			$restore_task = true;
		}
	}else{
		if( $sres['data']['data']['latest_time'] > date("Y-m-d H:i:s", time()-60 ) ){
			$restore_task = true;
		}
	}
}else{
	$restore_status = "Status not found";
}

if( $_POST['action'] == "home_check_import_status" ){

	$d = [];
	$d['status'] = $restore_status;
	if( isset($sres['data']['data']['new_app_id']) ){
		$d['new_app_id'] = $sres['data']['data']['new_app_id'];
		$d['new_app'] = $sres['data']['data']['new_app'];
	}

	// $lres = $mongodb_con->find( $db_prefix . "_zlog_home_import", [], [
	// 	'sort'=>['_id'=>-1], 
	// 	'limit'=>10
	// ]);
	// $d['log'] = $lres['data'];

	json_response([
		'status'=>"success", 
		"data"=>$d
	]);

	exit;
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


if( $_POST['action'] == "load_apps" ){

	if( !$_POST['token'] ){
		json_response([
			"status"=>"fail", "error"=>"Token not found"
		]);
	}
	$token_status = validate_token(  "load_apps", $_POST['token'] );

	if( $token_status != "OK" ){
		json_response([
			"status"=>"TokenError", "error"=>$token_status
		]);
	}

	$res = $mongodb_con->find( $db_prefix . "_apps", ['deleted'=>['$exists'=>false]], ['sort'=>[ 'app'=>1 ]] );
	if( $res['status'] == 'success' ){
		json_response([
			"status"=>"success", "apps"=>$res['data']
		]);
	}else{
		json_response([
			"status"=>"fail", "error"=>$res['error']
		]);
	}
}

if( $_POST['action'] == "delete_app" ){
	if( !$_POST['token'] ){
		json_response([
			"status"=>"fail", "error"=>"Token not found"
		]);
	}
	$token_status = validate_token(  "delete_app", $_POST['token'] );

	if( $token_status != "OK" ){
		json_response([
			"status"=>"TokenError", "error"=>$token_status
		]);
	}

	if( !preg_match("/^[a-f0-9]{24}$/", $_POST['app_id']) ){
		json_response("fail", "ID Incorrect");
	}

	$res = $mongodb_con->find_one( $db_prefix . "_apps", [
		'_id'=>$_POST['app_id']
	]);
	if( !$res['data'] ){
		json_response("fail", "Does not exists");
	}
	// $res = $mongodb_con->update_one( $db_prefix . "_apps", [
	// 	'_id'=>$_POST['app_id']
	// ], [
	// 	"deleted"=>'y',
	// 	"deleted_date"=>date("Y-m-d H:i:s"),
	// 	"active"=>false,
	// ]);
	//http_response(500, "something wrong");

	$res = $mongodb_con->delete_one(  $db_prefix . "_apps", ['_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->delete_many( $db_prefix . "_apis", ['app_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->delete_many( $db_prefix . "_apis_versions", ['app_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->delete_many( $db_prefix . "_pages", ['app_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->delete_many( $db_prefix . "_pages_versions", ['app_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->delete_many( $db_prefix . "_functions", ['app_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->delete_many( $db_prefix . "_functions_versions", ['app_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->delete_many( $db_prefix . "_files", ['app_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->delete_many( $db_prefix . "_databases", ['app_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->delete_many( $db_prefix . "_tables", ['app_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->find( $db_prefix . "_tables_dynamic", ['app_id'=>$_POST['app_id'] ] );
	foreach( $res['data'] as $i=>$j ){
		$res = $mongodb_con->drop_collection( $db_prefix . "_dt_" . $j['_id'] );
	}
	$res = $mongodb_con->delete_many( $db_prefix . "_tables_dynamic", ['app_id'=>$_POST['app_id'] ] );
	$res = $mongodb_con->delete_many( $db_prefix . "_cloud_domains", ['app_id'=>$_POST['app_id'] ] );

	event_log( "system", "app_delete", [
		'app_id'=>$_POST['app_id']
	]);

	json_response([
		"status"=>"success",
	]);
}

if( $_POST['action'] == "create_app" ){

	if( !$_POST['token'] ){
		json_response([
			"status"=>"fail", "error"=>"Token not found"
		]);
	}
	$token_status = validate_token(  "create_app", $_POST['token'] );

	if( $token_status != "OK" ){
		json_response([
			"status"=>"TokenError", "error"=>$token_status
		]);
	}

	if( !preg_match("/^[a-z0-9\-]{3,25}$/", $_POST['new_app']['app']) ){
		json_response("fail", "Name incorrect");
	}
	if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{5,50}$/i", $_POST['new_app']['des']) ){
		json_response("fail", "Description incorrect");
	}
	$_POST['new_app']['app'] = trim($_POST['new_app']['app']);
	$_POST['new_app']['des'] = trim($_POST['new_app']['des']);
	$res = $mongodb_con->find_one( $db_prefix . "_apps", [
		'app'=>$_POST['new_app']['app'], 'active'=>true,
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}

	require_once("config_default_app_settings.php");

	$app_id = $mongodb_con->generate_id();
	$res = $mongodb_con->insert( $db_prefix . "_apps", [
		"_id"=>$app_id,
		"app"=>$_POST['new_app']['app'], "des"=>$_POST['new_app']['des'],
		"created"=>date("Y-m-d H:i:s"), "updated"=>date("Y-m-d H:i:s"),
		"active"=>true,
	]);

	$page_id = $mongodb_con->generate_id();
	$page_version_id = $mongodb_con->generate_id();

	//print_r( $config_page_record );print_r( $config_page_version_record );exit;

	$config_page_record['_id'] = $page_id;
	$config_page_record['version_id'] = $page_version_id;
	$config_page_record['app_id'] = $app_id;
	$config_page_record['created'] = date("Y-m-d H:i:s");
	$config_page_record['updated'] = date("Y-m-d H:i:s");

	$res2 = $mongodb_con->insert( $db_prefix . "_pages", $config_page_record );

	$config_page_version_record['_id'] = $page_version_id;
	$config_page_version_record['page_id'] = $page_id;
	$config_page_version_record['app_id'] = $app_id;
	$config_page_version_record['created'] = date("Y-m-d H:i:s");
	$config_page_version_record['updated'] = date("Y-m-d H:i:s");

	$res2 = $mongodb_con->insert( $db_prefix . "_pages_versions", $config_page_version_record );

	event_log( "system", "app_create", [
		'app_id'=>$app_id
	]);

	//http_response(500, "something wrong");
	json_response([
		"status"=>"success",
	]);
}

if( $_POST['action'] == "apps_clone_app" ){

	if( !$_POST['token'] ){
		json_response([
			"status"=>"fail",
			"error"=>"Token not found"
		]);
	}
	$app_id = $_POST['app_id'];
	$token_status = validate_token(  "clone_app" .$app_id , $_POST['token'] );

	if( $token_status != "OK" ){
		json_response([
			"status"=>"TokenError",
			"error"=>$token_status
		]);
	}

	if( !preg_match( "/^[a-f0-9]{24}$/", $app_id ) ){
		json_response("fail", "App ID incorrect");
	}
	if( !preg_match( "/^[a-z][a-z0-9\-]{3,25}$/i", $_POST['new_name'] ) ){
		json_response("fail", "Name not allowed. [a-z][a-z0-9\-]{3,25}");
	}
	$res = $mongodb_con->find_one( $db_prefix . "_apps", [
		'app'=>$_POST['new_name'], 
	]);
	if( $res['data'] ){
		json_response("fail", "An APP with same name already exists");
	}

	$res = $mongodb_con->find_one( $db_prefix . "_apps", [
		'_id'=>$app_id, 
	]);
	if( !$res['data'] ){
		json_response("fail", "APP not found");
	}

	$time = microtime(true);
	$records = 1;

	$app = $res['data'];
	$new_app_id = $mongodb_con->generate_id();
	$app['app'] = $_POST['new_name'];
	$app['updated'] = date("Y-m-d H:i:s");
	// http_response( 500, "something wrong" );


	$simulate = true;
	$datasets = [];

	$res = $mongodb_con->find( $db_prefix . "_apis", [
		'app_id'=>$app_id
	]);
	foreach( $res['data'] as $i=>$j ){
		$res2 = $mongodb_con->find_one( $db_prefix . "_apis_versions", [
			'_id'=>$j['version_id']
		]);
		$j['version_part'] = $res2['data'];
		$datasets['apis'][] = $j;
	}
	$res = $mongodb_con->find( $db_prefix . "_pages", [
		'app_id'=>$app_id
	]);
	foreach( $res['data'] as $i=>$j ){
		$res2 = $mongodb_con->find_one( $db_prefix . "_pages_versions", [
			'_id'=>$j['version_id']
		]);
		$j['version_part'] = $res2['data'];
		$datasets['pages'][] = $j;
	}
	$res = $mongodb_con->find( $db_prefix . "_functions", [
		'app_id'=>$app_id
	]);
	foreach( $res['data'] as $i=>$j ){
		$res2 = $mongodb_con->find_one( $db_prefix . "_functions_versions", [
			'_id'=>$j['version_id']
		]);
		$j['version_part'] = $res2['data'];
		$datasets['functions'][] = $j;
	}
	$res = $mongodb_con->find( $db_prefix . "_databases", [
		'app_id'=>$app_id
	]);
	foreach( $res['data'] as $i=>$j ){
		$datasets['databases'][] = $j;
	}
	$res = $mongodb_con->find( $db_prefix . "_tables", [
		'app_id'=>$app_id
	]);
	foreach( $res['data'] as $i=>$j ){
		$datasets['tables'][] = $j;
	}
	$res = $mongodb_con->find( $db_prefix . "_tables_dynamic", [
		'app_id'=>$app_id
	]);
	foreach( $res['data'] as $i=>$j ){
		$datasets['tables_dynamic'][] = $j;
	}
	$res = $mongodb_con->find( $db_prefix . "_files", [
		'app_id'=>$app_id
	]);
	foreach( $res['data'] as $i=>$j ){
		$datasets['files'][] = $j;
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

		$ids = [
			'app'=>[],'apis'=>[],'pages'=>[],'functions'=>[],'apis'=>[],'files'=>[],'tables_dynamic'=>[],'databases'=>[], 'storage_vaults'=>[],
		];
		$all_ids = [];

		$ids['app'][ $app['_id'] ] = $new_app_id;
		$all_ids[ $app['_id'] ] = $new_app_id;
		$table_ids = [];
		$app['_id'] = $new_app_id;
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


	$mongodb_con->insert( $db_prefix . "_apps", $app );
	foreach( $datasets['apis'] as $i=>$j ){
		$records+=2;
		replace_ids( $j );
		$v = $j['version_part'];
		unset($j['version_part']);
		$v['api_id'] = $j['_id'];
		$v['app_id'] = $j['app_id'];
		$mongodb_con->insert( $db_prefix . "_apis", $j );
		$mongodb_con->insert( $db_prefix . "_apis_versions", $v );
	}
	foreach( $datasets['pages'] as $i=>$j ){
		$records+=2;
		$v = $j['version_part'];
		unset($j['version_part']);
		$v['page_id'] = $j['_id'];
		$v['app_id'] = $j['app_id'];
		$mongodb_con->insert( $db_prefix . "_pages", $j );
		$mongodb_con->insert( $db_prefix . "_pages_versions", $v );
	}
	foreach( $datasets['functions'] as $i=>$j ){
		$records+=2;
		replace_ids( $j );
		$v = $j['version_part'];
		unset($j['version_part']);
		$v['api_id'] = $j['_id'];
		$v['app_id'] = $j['app_id'];
		$mongodb_con->insert( $db_prefix . "_functions", $j );
		$mongodb_con->insert( $db_prefix . "_functions_versions", $v );
	}
	foreach( $datasets['files'] as $i=>$j ){
		$records+=1;
		$mongodb_con->insert( $db_prefix . "_files", $j );
	}
	foreach( $datasets['databases'] as $i=>$j ){
		$records+=1;
		$mongodb_con->insert( $db_prefix . "_databases", $j );
	}
	foreach( $datasets['tables'] as $i=>$j ){
		$records+=1;
		$mongodb_con->insert( $db_prefix . "_tables", $j );
	}
	foreach( $datasets['storage_vaults'] as $i=>$j ){
		$mongodb_con->insert( $db_prefix . "_storage_vaults", $j );
	}
	$table_queue = [];
	foreach( $datasets['tables_dynamic'] as $i=>$j ){
		$records+=1;
		$mongodb_con->insert( $db_prefix . "_tables_dynamic", $j );
		$mongodb_con->create_collection( $db_prefix . "_dt_" . $j['_id'] );
		if( isset($table_ids[ $j['_id'] ]) ){
			$oid = $table_ids[ $j['_id'] ];
		}else{
			$oid = $j['_id'];
		}
		$table_queue[ $oid ] = $j['_id'];
	}

	$_SESSION['table_queue'] = $table_queue;

	event_log( "system", "app_create", [
		'app_id'=>$new_app_id
	]);

	json_response([
		"status"=>"success", "records"=>$records, 
		"duration"=>round(microtime(true)-$time,3),
		"table_queue"=>$table_queue,
	]);
}

if( $_POST['action'] == "apps_clone_app_step2" ){

	$app_id = $_POST['app_id'];

	if( !preg_match( "/^[a-f0-9]{24}$/", $_POST['old_id'] ) ){
		json_response("fail", "App ID 1 incorrect");
	}
	if( !preg_match( "/^[a-f0-9]{24}$/", $_POST['new_id'] ) ){
		json_response("fail", "App ID 2 incorrect");
	}

	if( !isset($_SESSION['table_queue'][ $_POST['old_id'] ]) ){
		json_response("fail", "Queue ID not found");
	}
	if( $_SESSION['table_queue'][ $_POST['old_id'] ] != $_POST['new_id'] ){
		json_response("fail", "Queue ID not matching");
	}

	$records = 0;
	$last_id = "";
	while( 1 ){
		$cond = [];
		if( $last_id ){ $cond['_id'] = ['$gt'=>$last_id] ;}
		$res = $mongodb_con->find( $db_prefix . "_dt_" . $_POST['old_id'], $cond, ['limit'=>500, 'sort'=>['_id'=>1] ]);
		if( is_array($res['data']) ){
			if( sizeof($res['data'])==0 ){
				break;
			}
			foreach( $res['data'] as $ti=>$tj ){
				$last_id = $tj['_id'];
				$records+=1;
				$mongodb_con->insert( $db_prefix . "_dt_" . $_POST['new_id'], $tj );
			}
		}else{
			break;
		}
	}
	json_response([
		"status"=>"success", "records"=>$records, "duration"=>round(microtime(true)-$time,3)
	]);
}


if( $_POST['action'] == "home_restore_upload" ){

	if( $restore_task ){
		json_response("fail", "An import task is already running...");
	}

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
					if( $t == "table" ){
						fwrite($fp2, "dt_". $table . ":". json_encode($dd) . "\n--\n");
					}else{
						fwrite($fp2, json_encode($dd) . "\n--\n");
					}
					if( $dd['__t'] == "app" ){
						$app_name = $dd['app'];
					}
					if( $t == "table" ){
						$datasets[ "dt" ]++;
					}else{
						$datasets[ $dd['__t'] ]++;
					}
				}
				$d = "";
			}else{
				$d .= $line;
			}
		}

		fclose($fp);
		fclose($fp2);	
		chmod($fn2, 0777);

		$tot = 0;
		$datasets2 = [];
		foreach( $datasets as $i=>$j ){
			if( $i != "app" ){
				$datasets2[ (isset($ids_names[$i])?$ids_names[$i]:$i)  ] = $j;
			}
		}

		event_log( "system", "app_restore_upload", [
			"app_id"=>$config_param1, 
		]);

		$vt = time();
		$_SESSION['restore_rand'] = $vt;
		$_SESSION['restore_file'] = $fn2;
		$dt = substr($file_match[4],0,4) . "-" .substr($file_match[4],4,2) . "-" .substr($file_match[4],6,2) . " " . substr($file_match[5],0,2) . ":" .substr($file_match[5],2,2);
		json_response([
			'status'=>"success", 
			"tot"=>$tot, 
			"date"=>$dt, 
			"summary"=>$datasets2, 
			"app"=>$app_name,
			"rand"=>$vt
		]);

	}else{
		json_response(['status'=>"fail","error"=>"File upload failed"]);
		exit;
	}
	exit;
}

if( $_POST['action'] == "home_restore_upload_confirm" ){
	if( !file_exists($_SESSION['restore_file']) || $_SESSION['restore_rand'] != $_POST['rand'] ){
		json_response(['status'=>"fail","error"=>"Incorrect confirm parameters"]);
	}

	if( $restore_task ){
		json_response("fail", "An import task is already running...");
	}

	$res = $mongodb_con->update_one($db_prefix . "_settings", [
		"_id"=>"home_app_import"
	],[
		"data"=>[
			"action"=>"home_app_import",
			"restore_file"=>$_SESSION['restore_file'],
			"restore_rand"=>$_SESSION['restore_rand'],
			"request_time"=>date("Y-m-d H:i:s"),
			"status"=>"Yet to Start"
		]
	], ['upsert'=>true]);

	exec( 'php page_home_import_task.php > /tmp/apimaker_home_import_task.cron.log &', $eoutput );

	json_response([
		'status'=>"success", 
		"task_id"=>$bg_task_id
	]);
}

