<?php

$config_template = [
	"AWS-S3"=> [
		"region"=> ["name"=> "Region", "type"=>"text", "value"=>"ap-south-1", "store"=>"plain", "m"=>true, "h"=>"example: ap-south-1, us-northeast-1 etc", "regexp"=>["/^[a-z][a-z0-9\-\_\.]{2,50}$/i"]],
		"bucket"=> ["name"=> "Bucket", "type"=>"text","value"=>"", "store"=>"plain", "m"=>true, "regexp"=>["/^[a-z0-9\-\_\.]{2,100}$/i"]],
		"key"=> ["name"=> "AccessKey", "type"=>"text","value"=>"", "store"=>"plain", "m"=>true, "store"=>"encrypt", "regexp"=>["/^[a-z0-9\=\/\+\-\_\.]{20}$/i","/^[a-z0-9]+\:[a-z0-9\=\/\+\-\_\.]+$/i"]],
		"secret"=> ["name"=> "AccessSecret", "type"=>"text","value"=>"", "store"=>"encrypt", "regexp"=>["/^[a-z0-9\=\/\+\-\_\.]{40}$/i","/^[a-z0-9]+\:[a-z0-9\=\/\+\-\_\.]+$/i"]],
		"path"=> ["name"=> "Path", "type"=>"text","value"=>"/", "h"=>"Folder mapping at destination", "regexp"=>["/^\/[a-z0-9\/\-\_\.]+\/$/i","/^\/$/i"]],
		"acl"=> ["name"=> "ACL", "type"=>"boolean","value"=>true, "h"=> "Access Control List Permissions"],
		"public"=> ["name"=> "Public Access", "type"=>"boolean","value"=>false, "h"=> "Is objects publicly available"],
	],
	"Azure-Blob"=> [
		"region"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
		"bucket"=> ["name"=> "Port", "type"=>"text","value"=>3306, "store"=>"plain", "m"=>true],
		"key"=> ["name"=> "AccessKey", "type"=>"text","value"=>"test", "store"=>"plain", "m"=>true, "store"=>"encrypt"],
		"secret"=> ["name"=> "AccessSecret", "type"=>"text","value"=>"root", "store"=>"encrypt"],
		"path"=> ["name"=> "Password", "type"=>"text","value"=>""],
		"acl"=> ["name"=> "ACL", "type"=>"boolean","value"=>true],
		"public"=> ["name"=> "Public Access", "type"=>"boolean","value"=>false],
	],
	"Google-Cloud-Storage"=> [
		"region"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
		"bucket"=> ["name"=> "Port", "type"=>"text","value"=>3306, "store"=>"plain", "m"=>true],
		"key"=> ["name"=> "AccessKey", "type"=>"text","value"=>"test", "store"=>"plain", "m"=>true, "store"=>"encrypt"],
		"secret"=> ["name"=> "AccessSecret", "type"=>"text","value"=>"root", "store"=>"encrypt"],
		"path"=> ["name"=> "Password", "type"=>"text","value"=>""],
		"acl"=> ["name"=> "ACL", "type"=>"boolean","value"=>true],
		"public"=> ["name"=> "Public Access", "type"=>"boolean","value"=>false],
	],
	"Google-Drive"=> [
		"region"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
		"bucket"=> ["name"=> "Port", "type"=>"number","value"=>3306, "store"=>"plain", "m"=>true],
		"key"=> ["name"=> "AccessKey", "type"=>"text","value"=>"test", "store"=>"plain", "m"=>true, "store"=>"encrypt"],
		"secret"=> ["name"=> "AccessSecret", "type"=>"text","value"=>"root", "store"=>"encrypt"],
		"path"=> ["name"=> "Password", "type"=>"text","value"=>""],
		"acl"=> ["name"=> "ACL", "type"=>"boolean","value"=>true],
		"public"=> ["name"=> "Public Access", "type"=>"boolean","value"=>false],
	],
	"Microsoft-OneDrive"=> [
		"region"=> ["name"=> "Host", "type"=>"text", "value"=>"localhost", "store"=>"plain", "m"=>true],
		"bucket"=> ["name"=> "Port", "type"=>"number","value"=>3306, "store"=>"plain", "m"=>true],
		"key"=> ["name"=> "AccessKey", "type"=>"text","value"=>"test", "store"=>"plain", "m"=>true, "store"=>"encrypt"],
		"secret"=> ["name"=> "AccessSecret", "type"=>"text","value"=>"root", "store"=>"encrypt"],
		"path"=> ["name"=> "Password", "type"=>"text","value"=>""],
		"acl"=> ["name"=> "ACL", "type"=>"boolean","value"=>true],
		"public"=> ["name"=> "Public Access", "type"=>"boolean","value"=>false],
	],
	"DigitalOcean Spaces"=> [
		"info"=> ["name"=> "Info", "type"=>"text", "value"=>"Not REady", "store"=>"plain", "m"=>true],
	],
	"Git"=> [
		"info"=> ["name"=> "Info", "type"=>"text", "value"=>"Not REady", "store"=>"plain", "m"=>true],
	],
	"LocalFileSystem"=> [
		"info"=> ["name"=> "Info", "type"=>"text", "value"=>"Not REady", "store"=>"plain", "m"=>true],
	],
	"SFTP"=> [
		"info"=> ["name"=> "Info", "type"=>"text", "value"=>"Not REady", "store"=>"plain", "m"=>true],
	],
	"Dropbox"=> [
		"info"=> ["name"=> "Info", "type"=>"text", "value"=>"Not REady", "store"=>"plain", "m"=>true],
	],
	"Box"=> [
		"info"=> ["name"=> "Info", "type"=>"text", "value"=>"Not REady", "store"=>"plain", "m"=>true],
	],
];
$config_template_json = json_encode($config_template);
$config_api_vaults = $config_global_apimaker['config_mongo_prefix'] . "_storage_vaults";

if( $_POST['action'] == "load_vaults" ){
	$res = $mongodb_con->find_with_key( $config_api_vaults, "_id", ["app_id"=>$config_param1], [] );
	$vaults = [];
	if( $res['data'] ){
		$vaults = $res['data'];
	}
	json_response(['status'=>"success", 'vaults'=>$vaults]);
}

if( $_POST['action'] == "storage_vault_update" ){
	$config_debug = false;
	if( $_POST["vault_id"] != "new" ){
		if( !preg_match("/^[a-f0-9]{24}$/", $_POST['vault_id']) ){
			json_response("fail","vault_id incorrect");
		}
		$vault_res = $mongodb_con->find_one($config_api_vaults, [ '_id'=>$_POST["vault_id"] ],[]);
		if( !$vault_res['data'] ){
			json_response("fail", "Vault not found!");
		}
	}
	if($_POST['des'] == ""){
		json_response("fail","Please Enter Vault Description");
	}else if($_POST['vault_type'] == ""){
		json_response("fail","Please Select Vault Type");
	}

	{
		if( !isset($config_template[ $_POST['vault_type'] ]) ){
			json_response("fail","Incorrect Data");
		}else{
			$template = $config_template[ $_POST['vault_type'] ];
			foreach( $template as $i=>$j ){
				if( $j['m'] ){
					if( !isset($_POST['details'][ $i ]) ){
						json_response("fail",$i . " Required");
					}
				}
				if( isset($_POST['details'][ $i ]) && $_POST['details'][ $i ]!="" ){
					if( isset($j['regexp']) && is_array($j['regexp']) ){
						$vf=false;
						foreach( $j['regexp'] as $ri=>$rd ){
							if( preg_match( $rd, $_POST['details'][ $i ]) ){
								$vf = true;
							}
						}
						if( !$vf ){
							json_response("fail", $i . " required to match");
						}
					}
				}
				if( $_POST['details'][ $i ] && $j['store'] == "encrypt" ){
					if( !preg_match("/^[a-z0-9]+\:[a-z0-9\=\/\+\-\_\.]+$/i", $_POST['details'][ $i ]) ){
						$_POST['details'][ $i ] = pass_encrypt($_POST['details'][ $i ]);
					}
				}
			}
			$insert_data = [
				"user_id" => $_SESSION["user_id"],
				"app_id"  => $config_param1,
				"des" => ucwords($_POST['des']),
				"vault_type"  => $_POST['vault_type'],
				"details" => $_POST['details'], 
			];
			//print_pre( $insert_data );exit;

			if( $_POST['vault_id'] == "new" ){
				$cond = [
					"app_id"=>$config_param1, 
					"des" => $mongodb_con->regex( "^". $_POST['des'] . "\$", 'i' )
				];
				$res = $mongodb_con->find_one( $config_api_vaults, $cond );
				if( $res['data'] ){
					json_response("fail","Vault with same name already exists!");
				}else{
					$insert_res = $mongodb_con->insert($config_api_vaults, $insert_data);
					if( !$insert_res['status'] == "fail" ){
						json_response($insert_res);
					}
					json_response("success","ok");
				}
			}else{
				{
					$cond = [
						"app_id"=>$config_param1, 
						"des" => $mongodb_con->regex( "^". $_POST['des'] . "\$", 'i' ),
						"_id"=>['$ne'=>$mongodb_con->get_id( $_POST['vault_id'] )]
					];
					$res = $mongodb_con->find_one( $config_api_vaults, $cond );
					if( $res['data'] ){
						json_response("fail","Vault description already use!");
					}else{
						$update_cond = ["_id"=> $_POST["db_id"]  ];
						$res = $mongodb_con->update_one($config_api_vaults,$update_cond,$insert_data);
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
if( $_POST['action'] == "test_vault" ){
	$config_debug = false;
	$vault = $mongodb_con->find_one($config_api_vaults, ["_id"=>$_POST['vault_id']]);
	if( !$vault ){
		json_response("fail","Server Error");
	}
	if( $vault['vault_type'] == "AWS-S3" ){

	}
	if( $vault['vault_type'] == "Azure-Blob" ){

	}
	if( $vault['vault_type'] == "Google-Drive" ){

	}
	if( $vault['vault_type'] == "Google-Cloud-Storage" ){

	}
	exit;
}

if( $_POST['action'] == "delete_vault" ){
	$config_debug = false;
	$vault_res = $mongodb_con->find_one($config_api_vaults,["app_id"=>$config_param1, '_id'=>$_POST["vault_id"] ],[]);
	if( !$vault_res['data'] ){
		json_response("fail","Vault not found!");
	}else{
		$vault = $vault_res;
	}
	exit;
}

if( $config_param3 ){
	$vault_res = $mongodb_con->find_one( $config_api_vaults, [ "app_id"=>$config_param1, "_id" => $config_param3 ] );
	if( !$vault_res['data'] ){
		echo404("Vault not found");exit;
	}else{
		$vault = $vault_res['data'];
	}
	//print_r( $vault );exit;
	if( $_GET["test"] == "test_1" ){
		print_pre( $vault );
	}
	$meta_title = "Storage Vault - " . $vault['vault_type'] . " - " . $vault['des'];
	$fn = "page_apps_storage_vaults_controll_". $vault['vault_type'].".php";
	if( file_exists( $fn ) ){
		require( $fn );
	}else{
		echo "Vault Type " . $vault['vault_type'] . " module is under development!";exit;
	}
	unset($vault['details']['key']);unset($vault['details']['secret']);

}else{
	$meta_title = "Storage Vaults";
}
