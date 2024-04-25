<?php

	$is_it_vault = false;
	$vault = "";
	$storage_vault = "";
	$prefix_path = "";

	if( $_POST['path'] != "/" ){
		$x = explode("/",$_POST['path']);
		array_pop( $x );
		if( sizeof( $x ) > 1 ){
			$name = array_pop( $x );
			$path = implode("/", $x);
			if( $path == "" ){$path = "/";}
		}else{
			$name = array_pop( $x );
			$path = "/";
		}

		$res = $mongodb_con->find_one( $db_prefix . "_files", [
			'app_id'=>$app_id,
			"path"=>$path, "name"=>$name,
		]);
		if( $res['data'] ){
			if( $res['data']['vt'] == "folder" ){
				if( isset($res['data']['vault']) ){
					$is_it_vault = true;
					$vault = $res['data']['vault'];
					$res = $mongodb_con->find_one( $db_prefix . "_storage_vaults", ["_id"=>$vault['vault_id']] );
					if( !$res['data'] ){
						json_response(['status'=>"fail", "error"=>"Mounted storage vault not found"]);
					}
					$storage_vault = $res['data'];
					$prefix_path = $_POST['path'];
				}
			}
		}
	}

if( $_POST['action'] == "list_files" ){
	if( isset($_POST['path']) ){
		if( $_POST['path'] != "/" && !preg_match("/^\/(.*?)\/$/i",$_POST['path']) ){
			json_response(["status"=>"fail","error"=>"Path incorrect format"]);
		}
	}

	if( $is_it_vault ){
		$_POST['path'] = $vault['vault_path'];
		$_POST['action'] = "list_files_mounted_path";
		require_once("index_engine_api_storage_vault.php");
		exit;
	}

	$res = $mongodb_con->find( $db_prefix . "_files", [
		'app_id'=>$app_id,
		"path"=>$_POST['path'],
	],[
		'projection'=>[
			'body'=>false,'data'=>false,
		],
		'sort'=>['name'=>1],
		'limit'=>1000,
	]);
	//print_r($res['data']);
	$keys = []; $folders = [];
	foreach( $res['data'] as $i=>$j ){
		if( $j['vt'] == "folder" ){
			$cnt = 0;
			$res = $mongodb_con->count( $db_prefix . "_files", [
				'app_id'=>$app_id,
				"path"=>$j['path'] . $j['name'] . "/",
			]);
			$cnt = $res['data'];
			$folders[] = [
				"_id"=>$j['_id'],
				"folder"=>$j['path'] . $j['name'],
				"count"=>$cnt
			];
		}else{
			$keys[] = [
				"_id"=>$j['_id'],
				"name"=>$j['path'] . $j['name'],
				"size"=>$j['sz'],
				"type"=>$j['type'],
				"t"=>$j['t'],
			];
		}
	}
	json_response([
		"status"=>"success",
		"files"=>$keys, "folders"=>$folders
	]);
	exit;
}


if( $_POST['action'] == "get_file" ){
	if( !isset($_POST['filename']) ){
		json_response(["status"=>"fail","error"=>"Filename missing"]);
	}
	if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+$/i", $_POST['filename']) || preg_match("/\/\//i", $_POST['filename']) || preg_match("/\/$/i", $_POST['filename']) ){
		json_response(["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
	}
	$x = explode("/", $_POST['filename']);
	$fn = array_pop($x);
	$path = implode("/", $x) . "/";
	if( $path == "" ){ $path = "/";}
	$res = $mongodb_con->find_one( $db_prefix . "_files", [
		'app_id'=>$app_id,
		"path"=>$path,
		"name"=>$fn
	]);
	if( $res['data'] ){
		json_response([
			'status'=>"success", 
			"_id"=>$res['data']['_id'],
			"path"=>$res['data']['path'],
			"name"=>$res['data']['name'],
			"data"=>$res['data']['data'],
			"type"=>$res['data']['type'],
			"t"=>$res['data']['t'],
			"sz"=>$res['data']['sz'],
		]);
	}else{
		json_response("fail", "File not found");
	}
	exit;
}
if( $_POST['action'] == "get_file_by_id" ){
	if( !isset($_POST['file_id']) ){
		json_response(["status"=>"fail","error"=>"Filename missing"]);
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['file_id']) ){
		json_response(["status"=>"fail","error"=>"file_id format mismatch"]);
	}
	$res = $mongodb_con->find_one( $db_prefix . "_files", [
		'app_id'=>$app_id,
		"_id"=>$_POST['file_id'],
	]);
	if( $res['data'] ){
		json_response([
			'status'=>"success", 
			"_id"=>$res['data']['_id'],
			"path"=>$res['data']['path'],
			"name"=>$res['data']['name'],
			"data"=>$res['data']['data'],
			"type"=>$res['data']['type'],
			"t"=>$res['data']['t'],
			"sz"=>$res['data']['sz'],
		]);
	}else{
		json_response("fail", "File not found");
	}
	exit;
}
if( $_POST['action'] == "get_raw_file" ){
	if( !isset($_POST['filename']) ){
		json_response(["status"=>"fail","error"=>"Filename missing"]);
	}
	if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+$/i", $_POST['filename']) || preg_match("/\/\//i", $_POST['filename']) || preg_match("/\/$/i", $_POST['filename']) ){
		json_response(["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
	}
	$x = explode("/", $_POST['filename']);
	$fn = array_pop($x);
	$path = implode("/", $x) . "/";
	$res = $mongodb_con->find_one( $db_prefix . "_files", [
		'app_id'=>$app_id,
		"path"=>$path,
		"name"=>$fn
	]);
	if( $res['data'] ){
		if( $res['data']['vt'] != "file" ){
			json_response("fail", "Requested object is not a file");
		}
		if( $res['data']['t'] == "base64" ){
			header("Content-Type: application/octetstream");
			header("Content-Disposition: attachment;filename=\"".$res['data']['name']."\"");
			echo base64_decode($res['data']['data']);
		}else if( $res['data']['t'] == "inline" ){
			header("Content-Type: ". $res['data']['type']);
			header("Content-Disposition: attachment;filename=\"".$res['data']['name']."\"");
			echo $res['data']['data'];
		}else{
			json_response("fail", "Unhandled file format");
		}
		//print_r( $res );
	}else{
		json_response("fail", "File not found");
	}
	exit;
}
if( $_POST['action'] == "get_raw_file_by_id" ){
	if( !isset($_POST['file_id']) ){
		json_response(["status"=>"fail","error"=>"Filename missing"]);
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['file_id']) ){
		json_response(["status"=>"fail","error"=>"file_id format mismatch"]);
	}
	$res = $mongodb_con->find_one( $db_prefix . "_files", [
		'app_id'=>$app_id,
		"_id"=>$_POST['file_id'],
	]);
	if( $res['data'] ){
		if( $res['data']['vt'] != "file" ){
			json_response("fail", "Requested object is not a file");
		}
		if( $res['data']['t'] == "base64" ){
			header("Content-Type: application/octetstream");
			header("Content-Disposition: attachment;filename=\"".$res['data']['name']."\"");
			echo base64_decode($res['data']['data']);
		}else if( $res['data']['t'] == "inline" ){
			header("Content-Type: ". $res['data']['type']);
			header("Content-Disposition: attachment;filename=\"".$res['data']['name']."\"");
			echo $res['data']['data'];
		}else{
			json_response("fail", "Unhandled file format");
		}
		//print_r( $res );
	}else{
		json_response("fail", "File not found");
	}
	exit;
}

if( $_POST['action'] == "delete_file" ){
	if( !isset($_POST['file_id']) ){
		json_response("fail", "Need file_id");
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['file_id']) ){
		json_response("fail", "ID incorrect");
	}
	$res = $mongodb_con->find_one( $db_prefix . "_files", [
		'_id'=>$_POST['file_id']
	]);
	if( $res['data'] ){
		if( $res['data']['vt'] == "folder" ){
			$res2 = $mongodb_con->find_one( $db_prefix . "_files", [
				'path'=>$res['data']['path'] . $res['data']['name'] . '/'
			]);
			if( $res2['data'] ){
				json_response("fail", "Folder is not empty");
			}
		}
	}
	$res = $mongodb_con->delete_one( $db_prefix . "_files", [
		'_id'=>$_POST['file_id']
	]);
	update_app_pages( $app_id );
	json_response($res);
}

if( $_POST['action'] == "put_file" ){

	$block_extensions = ["fap","apk","jar","ahk","cmd","ipa","run","xbe","0xe","rbf","vlx","workflow","u3p","8ck","bat","bms","exe","bin","elf","air","appimage","xap","gadget","app","mpk","widget","x86","shortcut","fba","mcr","pif","ac","com","xlm","tpk","sh","x86_64","73k","script","scpt","command","out","rxe","scb","ba_","ps1","paf.exe","scar","isu","scr","xex","fas","coffee","ex_","action","tcp","acc","celx","shb","ex5","rfu","ebs2","hta","cgi","xbap","nexe","ecf","fxp","sk","vpm","plsc","rpj","ws","azw2","js","mlx","dld","cof","vxp","caction","vbs","wsh","mcr","iim","ex_","phar","89k","cheat","esh","fpi","wcm","pex","server","gpe","a7r","dek","pyc","exe1","jsf","jsx","acr","ex4","pwc","ear","icd","epk","vexe","rox","mel","zl9","plx","mm","snap","paf","mcr","ms","tiapp","uvm","gm9","atmx","89z","vbscript","actc","pyo","applescript","frs","hms","otm","rgs","n","widget","csh","mrc","wiz","prg","ebs","tms","spr","cyw","sct","e_e","ebm","gs","mrp","osx","fky","xqt","fas","ygh","prg","app","mxe","actm","udf","kix","seed","cel","app","ezs","thm","beam","lo","vbe","kx","jse","prg","rfs","s2a","dmc","hpf","wpk","exz","scptd","ls","ms","msl","mhm","tipa","xys","prc","wpm","sca","ita","eham","wsf","qit","es","arscript","rbx","mem","sapk","ebacmd","upx","ipk","mam","ncl","ksh","dxl","ham","btm","mio","ipf","pvd","vdo","gpu","exopc","ds","mac","sbs","cfs","sts","asb","qpx","p","rpg","mlappinstall","srec","uw8","pxo","afmacros","afmacro","mamc","ore","ezt","smm","73p","bns"];

	if( !isset($_FILES['file']) ){
		json_response(["status"=>"fail","error"=>"File data missing"]);
	}
	if( !isset($_POST['filename']) ){
		json_response(["status"=>"fail","error"=>"Filename missing"]);
	}

	if( !isset($_POST['filename']) ){
		json_response(["status"=>"fail","error"=>"Filename missing"]);
	}
	if( !preg_match("/^\/[a-z0-9\.\,\_\-\ \/\@\!\(\)]+\.[a-z0-9]{2,5}$/i", $_POST['filename']) || preg_match("/\/\//i", $_POST['filename']) || preg_match("/\/$/i", $_POST['filename']) ){
		json_response(["status"=>"fail","error"=>"Filename format invalid. expected: /filename.ext or /path/filename.ext"]);
	}

	$ext = array_pop( explode(".",$_FILES['file']['name']) );
	if( in_array($ext, $block_extensions) ){
		json_response(["status"=>"fail", "error"=>"Uploaded File extension is vulnerable hence blocked"]);
	}

	$ext = array_pop( explode(".",$_POST['filename']) );
	if( in_array($ext, $block_extensions) ){
		json_response(["status"=>"fail", "error"=>"Filename extension is vulnerable hence blocked"]);
	}

	$x = explode("/", $_POST['filename']);
	$fn = array_pop($x);
	$path = implode("/", $x) . "/";

	$res = $mongodb_con->find_one( $db_prefix . "_files", [
		'app_id'=>$app_id,
		"path"=>$path,
		"vt"=>"folder"
	]);
	if( !$res['data'] ){
		json_response("fail", "path `" .$path . "` not found");
	}

	$res = $mongodb_con->find_one( $db_prefix . "_files", [
		'app_id'=>$app_id,
		"path"=>$path,
		"name"=>$fn
	]);
	if( $res['data'] ){
		if( $res['data']['vt'] == "folder" ){
			json_response("fail", "A folder already exists with same name");
		}
		if( !isset($_POST['replace']) || $_POST['replace'] === false ){
			json_response("fail", "A file already exists with same name");
		}
	}

	if( file_exists( $_FILES['file']['tmp_name'] ) && filesize($_FILES['file']['tmp_name']) > 0 ){
		$fb = file_get_contents($_FILES['file']['tmp_name']);
		$res = $mongodb_con->insert( $db_prefix . "_files", [
			"app_id"=>$app_id,
			"name"=>$fn,
			'type'=>$_FILES['file']['type'],
			'vt'=>"file",
			"path"=>$path,
			't'=>"base64",
			"data"=>base64_encode($fb),
			"sz"=>strlen($fb),
			'ext'=>$ext,
			"created"=>date("Y-m-d H:i:s"),
			"updated"=>date("Y-m-d H:i:s"),
		]);
		if( $res['status'] == "success" ){
			$res['data'] = [
				"_id"=>$res['inserted_id'],
				"app_id"=>$app_id,
				"name"=>$fn,
				'type'=>$_FILES['file']['type'],
				'vt'=>"file",
				"path"=>$path,
				't'=>"base64",
				"sz"=>strlen($fb),
				'ext'=>$ext,
				"created"=>date("Y-m-d H:i:s"),
				"updated"=>date("Y-m-d H:i:s"),
			];
			update_app_pages( $app_id );
		}
		json_response($res);
	}else{
		json_response(['status'=>"fail", "error"=>"server error"]);
	}
	exit;
}

if( $_POST['action'] == "files_create_folder" ){
	if( !preg_match("/^[a-z0-9\.\-\_\/]{2,100}$/i", $_POST['new_folder']) ){
		json_response("fail", "Name incorrect. Min 2 chars Max 100. No special chars");
	}
	$path = $_POST['current_path'];
	$res = $mongodb_con->find_one( $db_prefix . "_files", [
		"app_id"=>$app_id,
		'vt'=>"folder",
		'name'=>$_POST['new_folder'],
		"path"=>$path,
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}
	$res = $mongodb_con->insert( $db_prefix . "_files", [
		"app_id"=>$app_id,
		"name"=>$_POST['new_folder'],
		'type'=>$type,
		'vt'=>"folder", //file,folder
		'path'=>$path,
		't'=>'inline', //inline/s3/disc/base64
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
	]);
	update_app_pages( $app_id );
	json_response($res);
	exit;
}
