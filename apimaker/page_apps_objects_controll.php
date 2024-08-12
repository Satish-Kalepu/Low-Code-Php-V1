<?php

/*

keywords:
_id
parent_id:permutation   permutation   label   tid   parent_id  parentlabel  main y/n
index: permutation, 
index: tid

*/

// echo time(). "<BR>";
// echo dechex( time()  ). "<BR>";
// for($i=0;$i<1000;$i++){
// 	echo uniqid() . "<BR>";
// }
// exit;

// 66844bf6    time()  8
// 66844c8a98702   uniqueid  13
// 64f237a775a7be05200cedd0   mongodbid  24

$graph_dbs 			= $db_prefix . "_graph_dbs";

//echo $graph_things;exit;

if( $_GET['action'] == "uninstall" ){
	$mongodb_con->drop_collection($graph_things);
	$mongodb_con->drop_collection($graph_things2);
	$mongodb_con->drop_collection($graph_queue);
	$mongodb_con->drop_collection($graph_keywords);
	event_log( "system", "objects_uninstall", [
		"app_id"=>$config_param1,
	]);
}

if( $_POST['action'] == "objects_create_database" ){

	$dbname = $_POST['dbname'];
	$res = $mongodb_con->find_one($db_prefix . "_graph_dbs", ["app_id"=>$config_param1, "name"=>$dbname]);
	if( $res['data'] ){
		json_response("fail", "A database already exists with the same name");
	}

	$res = $mongodb_con->insert($db_prefix . "_graph_dbs", [
		"app_id"=>$config_param1,
		"type"=>"internal",
		"name"=>$dbname,
		"createdon"=>date("Y-m-d H:i:s"),
	]);
	if( !$res['inserted_id'] ){
		json_response("fail", "Database creation failed: " . $res['error']);
	}
	$graph_id = $res['inserted_id'];

	$graph_things = $db_prefix . "_graph_" . $graph_id . "_things";
	$graph_keywords = $db_prefix . "_graph_" . $graph_id . "_keywords";
	$mongodb_con->create_collection($graph_things);
	$mongodb_con->create_index($graph_things,  ["l.v"=>1], ["sparse"=>true]);
	$mongodb_con->create_index($graph_things,  ["cnt"=>1], ["sparse"=>true]);
	$mongodb_con->create_index($graph_things,  ["i_of.i"=>1,"l.v"=>1], ["unique"=>true,"sparse"=>true]);
	$mongodb_con->create_index($graph_things,  ["i_of.i"=>1,"_id"=>1], ["index"=>true,"sparse"=>true]);
	$mongodb_con->create_collection($graph_keywords);
	$mongodb_con->create_index($graph_keywords,  ["p"=>1], ["sparse"=>true]);
	$mongodb_con->create_index($graph_keywords,  ["tid"=>1, "p"=>1], ["sparse"=>true]);

	$initial_id = 1;
	function getinitialid(){
		global $initial_id;
		$initial_id++;
		return "T".$initial_id;
	}

	$things = ["Person", "Country", "State", "City", "Pincode", "Company", "Brand", "Website", "DataSet", "Product", "Place"];

	//  label can be a link as well. 
	//  i_of is single per object
	$mongodb_con->insert($graph_things, [
		"_id"=>"T1", 
		"l"=>["t"=>"T", "v"=>"Root"],
		"i_of"=>["t"=>"GT", "i"=> "T1", "v"=>"Root"],
		'i_t'=> ["t"=>"T", "v"=>"N"],
		"props"=>[
			"p1"=>[["t"=>"T", "v"=>"Initial Graph Super Node" ]],
		],
		"z_t"=>[ "p1"=>["l"=>["t"=>"T","v"=>"Description"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>["t"=>"B", "v"=>"false" ] ] ],
		"z_o"=>["p1"], "z_n"=>2,
		'm_i'=>date("Y-m-d H:i:s"),
		'm_u'=>date("Y-m-d H:i:s"),
	]);
	$node_series = 2;
	foreach( $things as $i=>$j ){
		$rec = [
			"_id"=>"T".$node_series, 
			"l"=>["t"=>"T", "v"=>$j],
			"i_of"=>["t"=>"GT", "i"=> "T1", "v"=>"Root"],
			'i_t'=> ["t"=>"T", "v"=>"N"],
			"props"=>[
				"p1"=>[["t"=>"T", "v"=>$j ]],
			],
			"z_t"=>[ "p1"=>["l"=>["t"=>"T","v"=>"Description"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>["t"=>"B", "v"=>"false"] ] ],
			"z_o"=>["p1"], "z_n"=>2,
			'm_i'=>date("Y-m-d H:i:s"),
			'm_u'=>date("Y-m-d H:i:s"),
		];

		$mongodb_con->insert($graph_things, $rec);
		$mongodb_con->increment($graph_things, "T1", "cnt", 1);
		$node_series++;
	}
	$mongodb_con->increment($graph_things, "T1", "series", $node_series);
	event_log("system", "objects_create_database", ["app_id"=>$config_param1, "graph_id"=>$graph_id, "type"=>"internal", "name"=>$dbname]);
	json_response("success");
	exit;
}
if( $_POST['action'] == "objects_delete_database" ){
	
	$res = $mongodb_con->find_one( $db_prefix . "_graph_dbs", [
		"app_id"=>$config_param1,
		"_id"=>$_POST['graph_id']
	]);
	if( !$res['data'] ){
		json_response("fail", "Database not found");
	}

	$res = $mongodb_con->delete_one($db_prefix . "_graph_dbs", ["app_id"=>$config_param1, "_id"=>$_POST['graph_id']] );
	$graph_things = $db_prefix . "_graph_" . $_POST['graph_id'] . "_things";
	$graph_links = $db_prefix . "_graph_" . $_POST['graph_id'] . "_links";
	$graph_keywords = $db_prefix . "_graph_" . $_POST['graph_id'] . "_keywords";
	$graph_queue = $db_prefix . "_zd_queue_graph_" . $_POST['graph_id'];
	$graph_log = $db_prefix . "_zlog_graph_" . $_POST['graph_id'];
	$mongodb_con->drop_collection( $graph_things );
	$mongodb_con->drop_collection( $graph_links );
	$mongodb_con->drop_collection( $graph_keywords );
	$mongodb_con->drop_collection( $graph_queue );
	$mongodb_con->drop_collection( $graph_log );
	
	$res6 = $mongodb_con->list_collections();
	foreach( $res6['data'] as $i=>$j ){
		if( preg_match("/graph\_" . $_POST['graph_id'] . "/", $j) ){
			$mongodb_con->drop_collection( $j );
		}
	}

	event_log( "system", "objects_delete_database", [
		"app_id"=>$config_param1,
		"graph_id"=>$_POST['graph_id'],
	]);

	json_response("success");
}

function find_permutations( $label ){
	//echo "===" . $label . "<BR>";
	$perms = [];
	$perms[ $label ] = 1;
	$x = preg_split("/[\W]+/", $label);
	if( sizeof($x) > 1 ){
		for($i=0;$i<sizeof($x);$i++){
			$v = array_pop($x);
			//echo "last word: " . $v . "-<BR>";
			if( sizeof($x) > 1 ){
				$subperms = find_permutations( implode(" ", $x) );
				//echo "sub permutations: <BR>";
				//print_r( $subperms );
				foreach( $subperms as $si=>$sv ){
					//echo "perm: " . $v . " " . $si . "<BR>";
					$perms[ $v . " " . $si ] = 1;
				}
				array_splice($x,0,0,$v);
			}else{
				array_splice($x,0,0,$v);
				//echo "perm: " . implode(" ", $x) . "<BR>";
				$perms[ implode(" ", $x) ]= 1;
			}
		}
	}
	return $perms;
}

if( $_POST['action'] == "graph_load_dbs" ){

	$res = $mongodb_con->find( $db_prefix . "_graph_dbs", ["app_id"=>$config_param1], ['sort'=>['name'=>1] ] );
	$dbs = [
		"internal"=>[],
		"external"=>[],
	];
	foreach( $res['data'] as $i=>$j ){
		if( $j['type'] == "internal" ){
			$dbs["internal"][] = $j;
		}
		if( $j['type'] == "external" ){
			$dbs["internal"][] = $j;
		}
	}

	json_response([
		"status"=>"success",
		"data"=>$dbs
	]);

	exit;
}

//echo $config_param1 . ": " . $config_param2 . ": " . $config_param3 . ": " . $config_param4 . "<BR>";exit;
if( $config_param3 ){
	if( !preg_match("/^[a-f0-9]{24}$/", $config_param3) ){
		json_response("fail", "Incorrect object id");
	}
	$res = $mongodb_con->find_one( $db_prefix . "_graph_dbs", ["app_id"=>$config_param1, "_id"=>$config_param3] );
	if( !$res['data'] ){
		json_response("fail", "Database not found");
	}
	$graph_id = $config_param3;
	$graph = $res['data'];

	$apps_folder = "appsobjects";

	$graph_things = 	$db_prefix . "_graph_" . $graph_id . "_things";
	$graph_keywords = 	$db_prefix . "_graph_" . $graph_id . "_keywords";
	$graph_links = 		$db_prefix . "_graph_" . $graph_id . "_links";
	$graph_queue	= $db_prefix . "_zd_queue_graph_". $graph_id;

	require("page_apps_objects_v1_controll.php");

	if( $_GET['action'] == "initialize2" ){
		require("page_apps_objects_controll2.php");
		exit;
	}
	if( $_GET['action'] == "initialize3" ){
		require("page_apps_objects_controll3.php");
		exit;
	}


}

// $v = find_permutations("Mohandas");
// echo "final perms<BR>";
// print_r($v);exit;