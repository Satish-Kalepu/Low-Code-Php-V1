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

$ids_names = [
	"app"=> "App",
	"apis"=> "Apis",
	"pages"=> "Pages",
	"tables_dynamic"=> "Internal Tables",
	"dt"=> "Table Records",
	"files"=> "Files",
	"storage_vaults"=> "Storage Vaults"
];

//echo "<pre>";print_r( $app );exit;

$is_export_busy = false;
$last_export_fn = "";
$last_export_sz = 0;
$last_export_dt = "";
if( isset($app['background_tasks']) ){
	if( isset($app['background_tasks']['app_export']) ){
		if(   $app['background_tasks']['app_export']['status'] == "running" ){
			if( (time()-$app['background_tasks']['app_export']['time']) < 300 ){
				$is_export_busy = true;
			}
		}else{
			$last_export_fn = $app['background_tasks']['app_export']['temp_fn'];
			$last_export_sz = filesize("/tmp/phpengine_backups/" . $last_export_fn);
			$last_export_sz = round($last_export_sz/1024/1024,2);
			$last_export_dt = date("d M Y H:i",$app['background_tasks']['app_export']['time'] );
		}
	}
}

//print_r( $app );

$restore_task = false;
$restore_status = "";
$sres = $mongodb_con->find_one( $db_prefix . "_settings", ['_id'=>$config_param1 . "_app_import"] );
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

$hub_restore_task = false;
$hub_restore_status = "";
$sres = $mongodb_con->find_one( $db_prefix . "_settings", ['_id'=>$config_param1 . "_app_hub_import"] );
if( $sres['data'] ){
	$hub_restore_status = $sres['data']['data']['status'];
	if( !isset($sres['data']['data']['latest_time']) ){
		if( $sres['data']['data']['request_time'] > date("Y-m-d H:i:s", time()-60 ) ){
			$hub_restore_task = true;
		}
	}else{
		if( $sres['data']['data']['latest_time'] > date("Y-m-d H:i:s", time()-60 ) ){
			$hub_restore_task = true;
		}
	}
}else{
	$hub_restore_status = "";
}

$is_hub_login = false;
$hub_login_email = "";
$hub_session_key = "";
$reshub = $mongodb_con->find_one($db_prefix . "_settings", ["_id"=>"hub"]);
if( $reshub['data'] ){
	$hub = $reshub['data'];
	if( isset($hub['session_key']) ){
		if( time()-$hub['time'] < (86400*5) ){
			$is_hub_login = true;
			$hub_login_email = $hub['email'];
			$hub_session_key = $hub['session_key'];
		}
	}
}
//print_r( $app['hub'] );exit;

if( $_POST['action'] == "exports_check_import_status" ){

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
		"data"=>$d,
	]);

	exit;
}

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

function enc_data( $data, $pass = "" ){
	if( $pass ){
		$encrypted = openssl_encrypt($data, "aes256", "abcdef".$pass);
	}else{
		$encrypted = openssl_encrypt($data, "aes256", "abcdef");
	}
	return $encrypted;
}
function dec_data( $data, $pass = "" ){
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
	set_time_limit(120);

	$res = $mongodb_con->find_one( $db_prefix . "_apps", ['_id'=>$config_param1], [
		'projection'=>['background_tasks.app_export'=>1]
	]);
	if( $res['data'] ){
		if( (time()-$res['data']['time']) > 120 ){

		}else{
			json_response("fail", "An export task is already under process");
		}
	}

	$res = $mongodb_con->find_one( $db_prefix . "_apps", ['_id'=>$config_param1], [
		'projection'=>[
			'pages'=>false, 'files'=>false, 'functions'=>false, 'background_tasks'=>false,
		]
	]);
	if( $res['data'] ){
		$app = $res['data'];
	}

	$res = $mongodb_con->update_one( $db_prefix . "_apps", ['_id'=>$config_param1], [
		'background_tasks.app_export'=>[
			'time'=>time(), 
			'session_id'=>session_id(),
			"status"=>'running',
		]
	]);

	//sleep( 30 );
	$pass = isset($_POST['backup_pass'])?$_POST['backup_pass']:"version1";
	$tmfn = create_snapshot_file($app, $pass);

	event_log( "system", "app_export", [
		"app_id"=>$config_param1, 
		"temp_fn"=>str_replace("/tmp/phpengine_backups/", "", $tmfn), 
		"sz"=>filesize($tmfn)
	]);

	$res = $mongodb_con->update_one( $db_prefix . "_apps", [
		'_id'=>$config_param1
	], [
		'background_tasks.app_export'=>[
			"time"=>time(),
			"session_id"=>session_id(),
			"status"=>'done',
			"temp_fn"=>str_replace("/tmp/phpengine_backups/", "", $tmfn), 
			"sz"=>filesize($tmfn)
		]
	]);	

	json_response(['status'=>"success", "temp_fn"=>str_replace("/tmp/phpengine_backups/", "", $tmfn), "sz"=>filesize($tmfn)]);
	exit;
}

if( $_POST['action'] == "export_hub_backup" ){
	// $t = validate_token("backupnow.". $config_param1, $_POST['token']);
	// if( $t != "OK" ){
	// 	json_response("fail", $t);
	// }

	function set_status(){
		global $mongodb_con;
		global $db_prefix;
		global $config_param1;
		$res = $mongodb_con->update_one( $db_prefix . "_apps", ['_id'=>$config_param1], [
			'background_tasks.app_export_hub'=>[
				'time'=>time(),
				'session_id'=>session_id(),
				"status"=>'running',
			]
		]);
	}

	function unset_status(){
		global $mongodb_con;
		global $db_prefix;
		global $config_param1;
		$res = $mongodb_con->update_one( $db_prefix . "_apps", [
			'_id'=>$config_param1
		], [
			'$unset'=> ['background_tasks.app_export_hub'=>1]
		]);
	}
	unset_status();

	//echo $is_hub_login .":". $hub_login_email .":". $hub_session_key; exit;	

	if( !$is_hub_login || !$hub_login_email ){
		json_response("fail", "Hub is not linked");
	}

	//header("Content-Type: text/plain");
	set_time_limit( 120  );

	$res = $mongodb_con->find_one( $db_prefix . "_apps", ['_id'=>$config_param1], [
		'projection'=>['background_tasks.app_export_hub'=>1]
	]);
	if( $res['data'] ){
		if( isset( $res['data']['background_tasks']['app_export_hub'] ) ){
			if( (time()-$res['data']['background_tasks']['app_export_hub']['time']) > 120 ){

			}else{
				json_response("fail", "An export task is already under process");
			}
		}
	}

	$res = $mongodb_con->find_one( $db_prefix . "_apps", ['_id'=>$config_param1], [
		'projection'=>[
			'pages'=>false, 'files'=>false, 'functions'=>false, 'background_tasks'=>false,
		]
	]);
	if( $res['data'] ){
		$app = $res['data'];
	}

	set_status();

	$filename = $hub_login_email . "/" . date("YmdHis") . "gz";
	$resp = curl_post($config_global_apimaker['config_hub_api_url'] . "repo-upload-key-set", [
		"repo-id"=> $app['hub']['repo']['id'],
		"file" => $filename,
		"source_version"=>( isset($app['hub']['repo']['version'])?$app['hub']['repo']['version']:"Fresh" ),
		"app"=>[
			"_id"=>$app['_id'],
			"app"=>$app['app'],
			"des"=>$app['des'],
		]
	], [
		"Content-Type: application/json",
		"Access-Key: " . $config_global_apimaker['config_hub_access_key'],
		"Session-Key: " . $hub_session_key
	]);

	$cred = [];
	$bucket = "";
	$filename = "";
	$version_id = "";
	if( $resp['status'] == 200 ){
		$d = json_decode($resp['body'], true);
		if( !$d ){
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
		if( isset($d['status']) ){
			if( $d['status'] == "success" ){
				if( isset($d['credentials']) ){
					$cred = $d['credentials'];
					$bucket = $d['bucket'];
					$filename = $d['filename'];
					$version_id = $d['version_id'];
				}else{
					json_response("fail", "Incorrect response from Hub: " . $resp['body']);
				}
			}else{
				json_response("fail", "Login Fail: " . $d['error']);
			}
		}else{
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
	}else{
		json_response("fail", "Invalid response: " . $resp['status'] . ": " . $resp['error']);
	}

	//sleep( 30 );
	$tmfn = create_snapshot_file($app, strtolower($hub_login_email) );
	if( !file_exists($tmfn) ){
		json_response("fail", "Export failed. Tmp file not found");
	}

	$s3Client = new Aws\S3\S3Client([
		'version'=>"latest",
		"region"=>"ap-south-1",
		"credentials"=>$cred
	]);

	try{
		$s3Client->putObject([
			"Bucket"=>$bucket,
			"Key"=>$filename,
			"SourceFile"=>$tmfn,
			// 'ContentType' => '<string>',
			// 'ContentEncoding' => '<string>',
		])->toArray();

	}catch(Exception $ex){
		unset_status();
		json_response("fail", $ex->getMessage() );
	}

	$resp2 = curl_post($config_global_apimaker['config_hub_api_url'] . "repo-upload-confirm", [
		"repo-id"=> $app['hub']['repo']['id'],
		"version_id" => $version_id,
	], [
		"Content-Type: application/json",
		"Access-Key: " . $config_global_apimaker['config_hub_access_key'],
		"Session-Key: " . $hub_session_key
	]);

	if( $resp2['status'] == 200 ){
		$d = json_decode($resp2['body'], true);
		if( !$d ){
			json_response("fail", "Incorrect response from Hub: " . $resp2['body']);
		}
		if( isset($d['status']) ){
			if( $d['status'] == "success" ){
			}else{
				json_response("fail", "Error: " . $d['error']);
			}
		}else{
			json_response("fail", "Incorrect response from Hub: " . $resp2['body']);
		}
	}else{
		json_response("fail", "Invalid response: " . $resp2['status'] . ": " . $resp2['error']);
	}

	event_log( "system", "app_export_hub", [
		"app_id"=>$config_param1, 
		"key"=>"s3://". $bucket . "/" . $filename,
		"repo_id"=>$app['hub']['repo']['id'],
		"version_id"=>$version_id
	]);

	unset_status();

	$res = $mongodb_con->update_one( $db_prefix . "_apps", [
		'_id'=>$config_param1
	], [
		'hub.repo.version'=>$version_id,
		'hub.repo.date'=>date("Y-m-d H:i:s")
	]);

	json_response([
		'status'=>"success",
		"version_id"=>$version_id
	]);
	exit;
}

function create_snapshot_file($app, $pass = ""){
	global $mongodb_con;
	global $db_prefix;
	global $config_param1;

	@mkdir("/tmp/phpengine_backups/", 0777);
	$tmfn = "/tmp/phpengine_backups/". preg_replace("/\W/", "", $app['app']) . "_" . $app['_id'] . "_" . date("Ymd_His");
	$fp = fopen($tmfn, "w");

	$data = "";
	if( $pass ){
		$line = "BackupVersion:1;AppVersion:1;PasswordProtected:true;Hash:" . pass_hash2( $pass, "version1" );
	}else{
		$pass = "version1";
		$line = "BackupVersion:1;AppVersion:1;PasswordProtected:false";
	}
	fwrite( $fp, $line . "\n--\n" );
	$app['__t'] = "app";
	fwrite($fp, enc_data(json_encode($app),$pass) . "\n--\n" );
	$res = $mongodb_con->find( $db_prefix . "_apis", [
		'app_id'=>$config_param1
	]);
	if( preg_match("/imported/i", $res['data']['app']) ){
		json_response("fail", "Pleae make sure app name does not contain word imported");
	}
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "apis";
		$res2 = $mongodb_con->find_one( $db_prefix . "_apis_versions", [
			'_id'=>$j['version_id']
		]);
		$j['version_part'] = $res2['data'];
		fwrite($fp, enc_data(json_encode($j),$pass) . "\n--\n" );
	}
	$res = $mongodb_con->find( $db_prefix . "_pages", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "pages";
		$res2 = $mongodb_con->find_one( $db_prefix . "_pages_versions", [
			'_id'=>$j['version_id']
		]);
		$j['version_part'] = $res2['data'];
		fwrite($fp, enc_data(json_encode($j),$pass) . "\n--\n" );
	}
	$res = $mongodb_con->find( $db_prefix . "_functions", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "functions";
		$res2 = $mongodb_con->find_one( $db_prefix . "_functions_versions", [
			'_id'=>$j['version_id']
		]);
		$j['version_part'] = $res2['data'];
		fwrite($fp, enc_data(json_encode($j),$pass) . "\n--\n" );
	}
	$res = $mongodb_con->find( $db_prefix . "_databases", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "databases";
		fwrite($fp, enc_data(json_encode($j),$pass) . "\n--\n" );
	}
	$res = $mongodb_con->find( $db_prefix . "_tables", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "tables";
		fwrite($fp, enc_data(json_encode($j),$pass) . "\n--\n" );
	}
	$res = $mongodb_con->find( $db_prefix . "_tables_dynamic", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "tables_dynamic";
		fwrite( $fp, enc_data(json_encode($j),$pass) . "\n--\n");
		$cond = [];
		$last_id = "";
		while( 1 ){
			if( $last_id ){ $cond['_id'] = ['$gt'=>$last_id]; }
			$res2 = $mongodb_con->find( $db_prefix . "_dt_" . $j['_id'], $cond, ['sort'=>['_id'=>1], 'limit'=>500]);
			if( !$res2['data'] || sizeof($res2['data']) == 0 ){break;}
			foreach( $res2['data'] as $di=>$dj ){
				fwrite($fp, "dt_" . $j['_id'] . ":" . json_encode($dj) . "\n--\n");
				$last_id = $dj['_id'];
			}
		}
	}

	$res = $mongodb_con->find( $db_prefix . "_files", [
		'app_id'=>$config_param1
	]);
	foreach( $res['data'] as $i=>$j ){
		$j['__t'] = "files";
		fwrite($fp, json_encode($j) . "\n--\n");
	}

	$res = $mongodb_con->find( $db_prefix . "_storage_vaults", [
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
	return $tmfn;
}

if( $_GET['action'] == "download_snapshot" ){
	$fn = $_GET['snapshot_file'];

	event_log( "system", "app_download_snapshot", [
		"app_id"=>$config_param1, 
	]);

	$tmfn = "/tmp/phpengine_backups/". $fn;
	ini_set('zlib.output_compression', 'Off');
	header('Content-Type: application/x-download');
	//header('Content-Encoding: gzip'); #
	header('Content-Length: '.filesize($tmfn)); #
	header('Content-Disposition: attachment; filename="'.$fn.'"');
	readfile($tmfn);
	exit;
}

if( $_POST['action'] == "exports_restore_upload" ){
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
		$app_record = [];

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
						$app_record = $dd;
					}
					if( $t == "table" ){
						$datasets[ "dt" ]++;
					}else{
						$datasets[ $dd['__t'] ]++;
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
				$datasets2[ (isset($ids_names[$i])?$ids_names[$i]:$i)  ] = $j;
			}
		}

		event_log( "system", "app_restore_upload", [
			"app_id"=>$config_param1, 
		]);

		if( $app['_id'] == $app_record['_id'] ){
			$vt = time();
			$_SESSION['restore_rand'] = $vt;
			$_SESSION['restore_file'] = $fn2;
			$dt = substr($file_match[4],0,4) . "-" .substr($file_match[4],4,2) . "-" .substr($file_match[4],6,2) . " " . substr($file_match[5],0,2) . ":" .substr($file_match[5],2,2);
			json_response(['status'=>"success2", "tot"=>$tot, "date"=>$dt, "summary"=>$datasets2, "rand"=>$vt]);
		}else if( $app['_id'] != $app_record['_id'] ){
			$vt = time();
			$_SESSION['restore_rand'] = $vt;
			$_SESSION['restore_file'] = $fn2;
			$dt = substr($file_match[4],0,4) . "-" .substr($file_match[4],4,2) . "-" .substr($file_match[4],6,2) . " " . substr($file_match[5],0,2) . ":" .substr($file_match[5],2,2);
			json_response(['status'=>"success3", "tot"=>$tot, "date"=>$dt, "summary"=>$datasets2, "rand"=>$vt]);
		}

	}else{
		json_response(['status'=>"fail","error"=>"File upload failed"]);
		exit;
	}
	exit;
}


if( $_POST['action'] == "exports_restore_upload_confirm" ){
	if( !file_exists($_SESSION['restore_file']) || $_SESSION['restore_rand'] != $_POST['rand'] ){
		json_response(['status'=>"fail","error"=>"Incorrect confirm parameters"]);
	}

	if( $restore_task ){
		json_response("fail", "An import task is already running...");
	}

	$res = $mongodb_con->update_one($db_prefix . "_settings", [
		"_id"=>$config_param1 ."_app_import"
	],[
		"data"=>[
			"action"=>$config_param1 ."_app_import",
			"restore_file"=>$_SESSION['restore_file'],
			"restore_rand"=>$_SESSION['restore_rand'],
			"request_time"=>date("Y-m-d H:i:s"),
			"status"=>"Yet to Start"
		]
	], ['upsert'=>true]);

	$mode = $_POST['option'];

	exec( 'php page_apps_export_import_task.php '. $config_param1 .' ' . $mode . ' > /tmp/apimaker_exports_import_'.$config_param1.'.cron.log &', $eoutput );

	json_response([
		'status'=>"success", 
		"task_id"=>$bg_task_id
	]);
}



if( $_POST['action'] == "exports_hub_login" ){

	if( !isset( $config_global_apimaker['config_hub_api_url'] ) ){
		json_response("fail", "Hub is not configured");
	}
	if( !isset( $config_global_apimaker['config_hub_access_key'] ) ){
		json_response("fail", "Hub is not configured.");
	}

	$email = base64_decode($_POST['login']['email']);
	$resp = curl_post($config_global_apimaker['config_hub_api_url'] . "login", [
		"email" => $email,
		"password" => base64_decode($_POST['login']['password']),
	], [
		"Content-Type: application/json",
		"Access-Key: " . $config_global_apimaker['config_hub_access_key']
	]);

	if( $resp['status'] == 200 ){
		$d = json_decode($resp['body'], true);
		if( !$d ){
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
		if( isset($d['status']) ){
			if( $d['status'] == "success" ){
				if( isset($d['session_access_key']) ){

					$res = $mongodb_con->update_one($db_prefix . "_settings", [
						"_id"=>"hub",
					], [
						"session_key"=>$d['session_access_key'],
						"time"=>time(),
						"email"=>$email
					],['upsert'=>true]);

					json_response(['status'=>"success"]);

				}else{
					json_response("fail", "Incorrect response from Hub: " . $resp['body']);
				}
			}else{
				json_response("fail", "Login Fail: " . $d['error']);
			}
		}else{
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
	}else{
		json_response("fail", "Invalid response: " . $resp['status'] . ": " . $resp['error']);
	}

}

if( $_POST['action'] == "exports_hub_logout" ){


	if( !isset( $config_global_apimaker['config_hub_api_url'] ) ){
		json_response("fail", "Hub is not configured");
	}
	if( !isset( $config_global_apimaker['config_hub_access_key'] ) ){
		json_response("fail", "Hub is not configured.");
	}

	$resp = curl_post($config_global_apimaker['config_hub_api_url'] . "logout", [
	], [
		"Content-Type: application/json",
		"Access-Key: " . $config_global_apimaker['config_hub_access_key'],
		"Session-Key: " . $hub_session_key
	]);

	$res = $mongodb_con->delete_one( $db_prefix . "_settings", [
		"_id"=>"hub",
	]);

	json_response("success", "OK");
}

if( $_POST['action'] == "exports_hub_create_repo" ){

	if( !$is_hub_login || !$hub_login_email || !$hub_session_key ){
		json_response("fail", "Hub is not linked");
	}

	if( !isset( $_POST['repo'] ) ){
		json_response("fail", "Request Error");
	}
	if( !isset( $_POST['repo']['name'] ) ){
		json_response("fail", "Request Error");
	}
	if( !preg_match( "/^[a-z0-9\-\_\.]{3,50}$/i", $_POST['repo']['name'] ) ){
		json_response("fail", "Repo name not acceptable");
	}
	if( !preg_match( "/^[a-z0-9\-\_\.\ \!\@\#\&\(\)\\,\.\r\n]{3,200}$/i", $_POST['repo']['des'] ) ){
		json_response("fail", "Repo Description not acceptable");
	}
	if( !preg_match( "/^(public|private)$/", $_POST['repo']['visibility'] ) ){
		json_response("fail", "Repo visiblity incorrect");
	}

	$resp = curl_post($config_global_apimaker['config_hub_api_url'] . "repo-create", [
		"name"=>$_POST['repo']['name'],
		"des"=>$_POST['repo']['des'],
		"visibility"=>$_POST['repo']['visibility'],
	], [
		"Content-Type: application/json",
		"Access-Key:  " . $config_global_apimaker['config_hub_access_key'],
		"Session-Key: " . $hub_session_key
	]);

	if( $resp['status'] == 200 ){
		$d = json_decode($resp['body'], true);
		if( !$d ){
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
		if( isset($d['status']) ){
			if( $d['status'] == "success" ){
				if( isset($d['repo_id']) ){

					$res = $mongodb_con->update_one($db_prefix . "_apps", [
						"_id"=>$config_param1,
					],[
						"hub.repo"=>[
							"id"=>$d['repo_id'],
							"name"=>$_POST['repo']['name'],
							"des"=>$_POST['repo']['des'],
							"visibility"=>$_POST['repo']['visibility'],
						]
					]);
					json_response([
						'status'=>"success", 
						"repo"=> [
							"id"=>$d['repo_id'],
							"name"=>$_POST['repo']['name'],
							"des"=>$_POST['repo']['des'],
							"visibility"=>$_POST['repo']['visibility'],
						]
					]);

				}else{
					json_response("fail", "Incorrect response from Hub: " . $resp['body']);
				}
			}else{
				json_response("fail", "Login Fail: " . $d['error']);
			}
		}else{
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
	}else{
		json_response("fail", "Invalid response: " . $resp['status'] . ": " . $resp['error']);
	}

}

if( $_POST['action'] == "exports_hub_repo_versions" ){

	if( !$is_hub_login || !$hub_login_email || !$hub_session_key ){
		json_response("fail", "Hub is not linked");
	}

	$resp = curl_post($config_global_apimaker['config_hub_api_url'] . "repo-version-list", [
		"repo-id"=>$app['hub']['repo']['id'],
	], [
		"Content-Type: application/json",
		"Access-Key:  " . $config_global_apimaker['config_hub_access_key'],
		"Session-Key: " . $hub_session_key
	]);

	if( $resp['status'] == 200 ){
		$d = json_decode($resp['body'], true);
		if( !$d ){
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
		if( isset($d['status']) ){
			if( $d['status'] == "success" ){
				if( isset($d['data']) ){
					json_response($d);

				}else{
					json_response("fail", "Incorrect response from Hub: " . $resp['body']);
				}
			}else{
				json_response("fail", "Login Fail: " . $d['error']);
			}
		}else{
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
	}else{
		json_response("fail", "Invalid response: " . $resp['status'] . ": " . $resp['error']);
	}

}

if( $_POST['action'] == "exports_hub_repo_list" ){

	if( !$is_hub_login || !$hub_login_email || !$hub_session_key ){
		json_response("fail", "Hub is not linked");
	}

	$resp = curl_post($config_global_apimaker['config_hub_api_url'] . "repo-list", [
	], [
		"Content-Type: application/json",
		"Access-Key:  " . $config_global_apimaker['config_hub_access_key'],
		"Session-Key: " . $hub_session_key
	]);

	if( $resp['status'] == 200 ){
		$d = json_decode($resp['body'], true);
		if( !$d ){
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
		if( isset($d['status']) ){
			if( $d['status'] == "success" ){
				if( isset($d['data']) ){
					json_response($d);
				}else{
					json_response("fail", "Incorrect response from Hub: " . $resp['body']);
				}
			}else{
				json_response("fail", "Login Fail: " . $d['error']);
			}
		}else{
			json_response("fail", "Incorrect response from Hub: " . $resp['body']);
		}
	}else{
		json_response("fail", "Invalid response: " . $resp['status'] . ": " . $resp['error']);
	}

}


if( $_POST['action'] == "exports_hub_restore_version" ){

	if( !isset($_POST['version_id']) ){
		json_response("fail", "Need Version_Id");
	}

	if( !preg_match("/^[a-f0-9]{3,50}$/i", $_POST['version_id']) ){
		json_response("fail", "Need proper Version_id");
	}

	if( $restore_task || $is_hub_import_busy ){
		json_response("fail", "An import task is already running...");
	}

	$res = $mongodb_con->update_one($db_prefix . "_settings", [
		"_id"=>$config_param1 ."_app_hub_import"
	],[
		"data"=>[
			"request_time"=>date("Y-m-d H:i:s"),
			"version_id"=>$_POST['version_id'],
			"status"=>"Yet to Start"
		]
	], ['upsert'=>true]);

	exec( 'php page_apps_export_hub_import_task.php '. $config_param1 .' ' . $_POST['version_id'] . ' ' . $hub_login_email . ' > /tmp/apimaker_exports_hub_import_'.$config_param1.'.cron.log &', $eoutput );

	json_response([
		'status'=>"success", 
		"task_id"=>$bg_task_id
	]);
}

if( $_POST['action'] == "exports_check_hub_import_status" ){

	$d = [];
	$d['status'] = $hub_restore_status;
	json_response([
		'status'=>"success", 
		"data"=>$d,
	]);

	exit;
}


if( $_POST['action'] == "exports_hub_link_repo" ){

	$res = $mongodb_con->update_one( $db_prefix . "_apps", [
		"_id"=>$config_param1,
	],[
		"hub.repo" =>[
			"id" => $_POST['repo']['id'],
			"name" => $_POST['repo']['name'],
			"des" => $_POST['repo']['des'],
			"visibility" => $_POST['repo']['visibility'],
		]
	]);
	json_response([
		'status'=>"success", 
	]);
	exit;
}
















