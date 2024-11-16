<?php

if( $_POST['action'] == "get_pages" ){
	$t = validate_token("getpages.". $config_param1, $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		'app_id'=>$config_param1
	],[
		'sort'=>['name'=>1],
		'limit'=>200,
		'projection'=>[
			'version_id'=>true,
			'name'=>true,
		]
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == "delete_page" ){
	$t = validate_token("deletepage". $config_param1 . $_POST['page_id'], $_POST['token']);
	if( $t != "OK" ){
		json_response("fail", $t);
	}
	if( !preg_match("/^[a-f0-9]{24}$/i", $_POST['page_id']) ){
		json_response("fail", "ID incorrect");
	}
	$res = $mongodb_con->delete_one( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		'_id'=>$_POST['page_id']
	]);
	$res = $mongodb_con->delete_many( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
		'page_id'=>$_POST['page_id']
	]);

	event_log( "system", "page_delete", [
		"app_id"=>$config_param1,
		"page_id"=>$_POST['page_id'],
	]);

	update_app_pages( $config_param1 );
	json_response($res);
}

if( $_POST['action'] == "create_page" ){
	if( !preg_match("/^[a-z][a-z0-9\.\-\_]{2,100}$/i", $_POST['new_page']['name']) ){
		json_response("fail", "Name incorrect");
	}
	if( !preg_match("/^[a-z0-9\!\@\%\^\&\*\.\-\_\'\"\n\r\t\ ]{2,250}$/i", $_POST['new_page']['des']) ){
		json_response("fail", "Description incorrect");
	}
	if( !preg_match("/^(html|dynamic|vuejs|reactjs)$/i", $_POST['new_page']['type']) ){
		json_response("fail", "Type incorrect");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		"app_id"=>$config_param1,
		'name'=>$_POST['new_page']['name'],
	]);
	if( $res['data'] ){
		json_response("fail", "Already exists");
	}

	if( $_POST['new_page']['type'] == "html" ){
		if( file_exists("page_themes/". $_POST['new_page']['template'] . ".html" ) ){
			$html = file_get_contents("page_themes/". $_POST['new_page']['template'] . ".html");
		}else{
			$html = file_get_contents("page_themes/blog.html");
		}
	}else{
		$html = "";
	}

	$version_id = $mongodb_con->generate_id();
	$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		"app_id"=>$config_param1,
		"name"=>$_POST['new_page']['name'],
		"des"=>$_POST['new_page']['des'],
		"type"=>$_POST['new_page']['type'],
		"created"=>date("Y-m-d H:i:s"),
		"updated"=>date("Y-m-d H:i:s"),
		"active"=>true,
		"version"=>1,
		"version_id"=>$version_id,
	]);
	if( $res['status'] == 'success' ){
		$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
			"_id"=>$mongodb_con->get_id($version_id),
			"app_id"=>$config_param1,
			"page_id"=>$res['inserted_id'],
			"name"=>$_POST['new_page']['name'],
			"des"=>$_POST['new_page']['des'],
			"type"=>$_POST['new_page']['type'],
			"created"=>date("Y-m-d H:i:s"),
			"updated"=>date("Y-m-d H:i:s"),
			"active"=>true,
			"version"=>1,
			"html"=>$html
		]);

		event_log( "system", "page_create", [
			"app_id"=>$config_param1,
			"page_id"=>$res['inserted_id'],
		]);

		update_app_pages( $config_param1 );
		json_response($res);
	}else{
		json_response($res);
	}
	exit;
}


if( $config_param3 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param3) ){
		echo404("Incorrect PAGE ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_pages", [
		'app_id'=>$app['_id'],
		'_id'=>$config_param3
	]);
	if( !$res['data'] ){
		echo404("Page not found!");
	}
	$main_page = $res['data'];
}

if( $config_param4 && $main_page ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param4) ){
		echo404("Incorrect PAGE Version ID");
	}
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
		"page_id"=>$main_page['_id'],
		"_id"=>$config_param4
	]);
	if( $res['data'] ){
		$page = $res['data'];
	}else{
		echo404("Page version not found!");
	}

//	print_pre( $page );exit;

	$page_type = $page['type'];

	if( $page_type == 'html' ){

		if( $_POST['action'] == "save_page" ){
			if( $_POST['page_version_id'] != $config_param4 ||  $_POST['app_id'] != $config_param1 ){
				json_response("fail","Incorrect URL");
			}
			$res = $mongodb_con->update_one( $config_global_apimaker['config_mongo_prefix'] . "_pages_versions", [
				"app_id"=> $config_param1,
				"_id"=>$config_param4
			],[
				"html"=>$_POST['html'],
				"script"=>$_POST['script'],
				"settings"=>$_POST['settings'],
				"updated"=>date("Y-m-d H:i:s"),
			]);
			if( $res["status"] == "fail" ){
				json_response("fail",$res["error"]);
			}
			event_log( "system", "page_save", [
				"app_id"=>$config_param1,
				"page_id"=>$config_param3,
				"page_version_id"=>$config_param4,
			]);
			json_response("success","ok");
		}else if( isset($_POST['action']) ){
			json_response("fail", "unknown action");
		}

	}else if( $page_type == 'dynamic' ){

		if( $_POST['action'] == "save_page_dynamicstructure" ){

			$d = $_POST['data'];
			if( !is_array($d) ){
				json_response("fail", "Incorrect data!"  );
			}
			if( !isset($d['blocks']) ){
				json_response("fail", "Structure Blocks Missing!" );
			}
			foreach( $d['blocks'] as $ci=>$cd ){
				if( !isset($cd['type']) ){
					json_response("fail", "Structure block type Missing!"  );
				}else if( !preg_match( "/^(html|javascript|style)$/i", $cd['type'] ) ){
					json_response("fail", "Structure block type Incorrect!"  );
				}
				if( !isset($cd['des']) ){
					json_response("fail", "Structure block description Missing!"  );
				}else if( !preg_match( "/^[a-z][a-z0-9\-\_\r\n\ \t\,\.\;\[\]\{\}\@\#\%\^\&\*\(\)\!]{2,100}$/i", $cd['des'] ) ){
					json_response("fail", "Structure block description Incorrect!" );
				}
			}
			$res = $mongodb_con->update_one( $db_prefix . "_pages_versions", ["_id"=>$config_param4], [
				"dynamicstructure"=>$d,
				"updated"=>date("Y-m-d H:i:s"),
			]);
			$res = $mongodb_con->update_one( $db_prefix . "_pages", ["_id"=>$config_param3], [
				"updated"=>date("Y-m-d H:i:s"),
			]);

			update_app_pages( $config_param1 );

			json_response( $res );
			exit;
		}else if( isset($_POST['action']) ){
			json_response("fail", "unknown action");
		}


	}else if( $page_type == 'vuejs' ){

		if( $_POST['action'] == "save_page_vuestructure" ){

			$d = $_POST['data'];
			if( !is_array($d) ){
				json_response("fail", "Incorrect data!"  );
			}
			$rewrite = false;
			if( $d['router_enable'] ){
				$rewrite = true;
			}

			foreach( ['data','mounted', 'methods', 'template'] as $i=>$item ){
				if( !isset($d[$item]) ){
					json_response("fail", $item . " Block Missing!"  );
				}else if( !is_array($d[$item]) ){
					json_response("fail", $item . " Block Incorrect!"  );
				}else if( !isset($d[$item]['data']) ){
					json_response("fail", $item . " Block Incorrect!"  );
				}
			}
			if( !isset($d['components']) ){
				json_response("fail", "Components Block Missing!" );
			}
			foreach( $d['components'] as $ci=>$cd ){
				if( !isset($cd['name']) ){
					json_response("fail", "Component name Missing!"  );
				}else if( !preg_match( "/^[a-z][a-z0-9\-\_]{2,100}$/i", $cd['name'] ) ){
					json_response("fail", "Component name Incorrect!"  );
				}
				if( !isset($cd['des']) ){
					json_response("fail", "Component Description Missing!"  );
				}else if( !preg_match( "/^[a-z][a-z0-9\-\_\r\n\ \t\,\.\;\[\]\{\}\@\#\%\^\&\*\(\)\!]{2,100}$/i", $cd['des'] ) ){
					json_response("fail", "Component Description Incorrect!"  );
				}
				foreach( ['data','mounted', 'methods', 'template'] as $i=>$item ){
					if( !isset($cd[$item]) ){
						json_response("fail", "Component ".$d['name'] . " " . $item . " Block Missing!"  );
					}else if( !is_array($cd[$item]) ){
						json_response("fail", "Component ".$d['name'] . " " . $item . " Block Incorrect!"  );
					}else if( !isset($cd[$item]['data']) ){
						json_response("fail", "Component ".$d['name'] . " " . $item . " Block Incorrect!"  );
					}
				}
			}
			if( !isset($d['router_enable']) ){
				json_response("fail", "Router Block Missing!" );
			}
			if( !isset($d['router']) ){
				json_response("fail", "Router Block Missing!" );
			}
			if( !isset($d['structure']) ){
				json_response("fail", "Structure Block Missing!" );
			}
			if( !isset($d['structure']['blocks']) ){
				json_response("fail", "Structure Blocks Missing!" );
			}
			foreach( $d['structure']['blocks'] as $ci=>$cd ){
				if( !isset($cd['type']) ){
					json_response("fail", "Structure block type Missing!"  );
				}else if( !preg_match( "/^(html|javascript|style)$/i", $cd['type'] ) ){
					json_response("fail", "Structure block type Incorrect!"  );
				}
				if( !isset($cd['des']) ){
					json_response("fail", "Structure block description Missing!"  );
				}else if( !preg_match( "/^[a-z][a-z0-9\-\_\r\n\ \t\,\.\;\[\]\{\}\@\#\%\^\&\*\(\)\!]{2,100}$/i", $cd['des'] ) ){
					json_response("fail", "Structure block description Incorrect!" );
				}
			}
			$res = $mongodb_con->update_one( $db_prefix . "_pages_versions", ["_id"=>$config_param4], [
				"vuestructure"=>$d,
				"rewrite"=>$rewrite,
				"updated"=>date("Y-m-d H:i:s"),
			]);
			$res = $mongodb_con->update_one( $db_prefix . "_pages", ["_id"=>$config_param3], [
				"rewrite"=>$rewrite,
				"updated"=>date("Y-m-d H:i:s"),
			]);

			update_app_pages( $config_param1 );

			json_response( $res );
			exit;
		}else if( isset($_POST['action']) ){
			json_response("fail", "unknown action");
		}

	}else{
		echo404("Page type not found! " . $page_type);
	}


}