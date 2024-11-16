<?php


if( !$config_param1 ){
	echo404();
}else if( !preg_match("/^[a-f0-9]{24}$/", $config_param1) ){
	echo404("Incorrect App ID");
}
$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_apps", [
	'_id'=>$config_param1
], [
	'projection'=>[
		'pages'=>false,'files'=>false,'functions'=>false,
	]
]);
if( !$res['data'] ){
	echo404("App not found!");
}
$app = $res['data'];

$test_environments = [];
if( isset($app['settings']) ){
	if( $app['settings']['host'] ){
		foreach( $app['settings']['domains'] as $di=>$dd ){
			$test_environments[] = [
				"t"=> "custom",
				"u"=> $dd['url'],
				"d"=> $dd['domain'],
				"e"=> $dd['path'],
			];
		}
	}
	if( isset($app['settings']['cloud']) && $app['settings']['cloud'] ){
		if( $app['settings']['cloud'] ){
			$d = $app['settings']['cloud-subdomain'] . "." . $app['settings']['cloud-domain'];
			$test_environments[] = [
				"t"=> "cloud",
				"u"=> "https://" . $d . "/",
				"d"=> $d,
			];
		}
	}
	if( isset($app['settings']['alias']) && $app['settings']['alias'] ){
		$test_environments[] = [
			"t"=> "cloud-alias",
			"d"=> $app['settings']['alias-domain'],
			"u"=> "https://" . $app['settings']['alias-domain'] . "/",
		];
	}

}

if( isset($app['settings']) ){
	if( isset($app['host']) && $app['host'] == true ){
		foreach( $app['settings']['domains'] as $i=>$j ){
			if( $j['domain'] == $_SERVER['HTTP_HOST'] ){
				$config_engine_api_url_internal = "http://" . $j['domain'] . $j['path'] . "_api_system/";
			}
		}
	}
	if( isset($app['cloud']) && $app['cloud'] == true ){
		$config_engine_api_url_external = "http://" . $j['cloud-subdomain'] . "." . $j['cloud-domain'] . "/_api_system/";
	}
}
// if( !isset($app['patch1']) ){
// 	$res  = $mongodb_con->update_many( $config_global_apimaker['config_mongo_prefix'] . "_apis", [], ['path'=>'/', 'vt'=>'api'] );
// 	print_r($res);
// 	$res = $mongodb_con->update_many( $config_global_apimaker['config_mongo_prefix'] . "_apis_versions", [], ['path'=>'/', 'vt'=>'api'] );
// 	print_r($res);
// 	$mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_apps", ['_id'=>$config_param1], ['patch1'=>1] );
// 	echo "<p>A patch has been applied. please reload page</p>";
// 	exit;
// }

$take_to_settings = false;
if( !isset($app['settings']) ){
	$take_to_settings = true;
}else if( !isset($app['settings']['custom']) && !isset($app['settings']['cloud']) ){
	$take_to_settings = true;
}else if( $app['settings']['custom'] === false && $app['settings']['cloud'] === false ){
	$take_to_settings = true;
}
if( $take_to_settings ){
	if( $config_param2 != "settings" ){
		if( $_POST['action'] != "exports_check_import_status" ){
			header("Location: " . $config_global_apimaker_path . "apps/" . $config_param1 . "/settings");
			exit;
		}
	}
}
if( $config_param2 == "apis" || $config_param2 == "" ){
	require("page_apps_apis_controll.php");
}
if( $config_param2 == "apis_global" ){
	require("page_apps_apis_global_controll.php");
}
if( $config_param2 == "sdk" ){
	require("page_apps_sdk_controll.php");
}

if( $config_param2 == "auth" ){
	require("page_apps_auth_controll.php");
}
if( $config_param2 == "functions" ){
	require("page_apps_functions_controll.php");
}
if( $config_param2 == "codeeditor" ){
	require("page_apps_codeeditor_controll.php");
}
if( $config_param2 == "pages" ){
	require("page_apps_pages_controll.php");
}
if( $config_param2 == "files" ){
	require("page_apps_files_controll.php");
}
if( $config_param2 == "export" ){
	require("page_apps_export_controll.php");
}
if( $config_param2 == "global_files" ){
	require("page_apps_global_files_controll.php");
}
if( $config_param2 == "settings" ){
	require("page_apps_settings_controll.php");
}
if( $config_param2 == "databases" ){
	require("page_databases_controll.php");
}
if( $config_param2 == "redis" ){
	require("page_apps_redis_controll.php");
}

if( $config_param2 == "storage" ){
	require("page_apps_storage_vaults_controll.php");
}
if( $config_param2 == "tables_dynamic" ){
	require("page_apps_tables_dynamic_controll.php");
}
if( $config_param2 == "tables_elastic" ){
	//require("page_apps_tables_elastic_controll.php");
}
if( $config_param2 == "objects" ){
	require("page_apps_objects_controll.php");
}
if( $config_param2 == "tasks" ){
	require("page_apps_tasks_controll.php");
}
if( $config_param2 == "logs" ){
	require("page_apps_logs_controll.php");
}