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
		return "T".str_pad($initial_id,5,"0",STR_PAD_LEFT);
	}

	$things = [
		["_id"=>"T1",  "l"=>["t"=>"T", "v"=>"Root"			],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T2",  "l"=>["t"=>"T", "v"=>"Person"		],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T3",  "l"=>["t"=>"T", "v"=>"Country"		],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T4",  "l"=>["t"=>"T", "v"=>"State"			],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T5",  "l"=>["t"=>"T", "v"=>"City"			],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T7",  "l"=>["t"=>"T", "v"=>"Pincode"		],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T8",  "l"=>["t"=>"T", "v"=>"Company"		],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T9",  "l"=>["t"=>"T", "v"=>"Brand"			],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T10",  "l"=>["t"=>"T", "v"=>"Website"		],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T11",  "l"=>["t"=>"T", "v"=>"DataSet"		],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T11",  "l"=>["t"=>"T", "v"=>"Product"		],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
		["_id"=>"T12",  "l"=>["t"=>"T", "v"=>"Place"		],	"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"]	],
	];

	//  label can be a link as well. 
	//  i_of is single per object

	foreach( $things as $i=>$j ){
		$j['i_t'] = ["t"=>"T", "v"=>"N"];
		$j["props"]=[
			"p1"=>[["t"=>"T", "v"=>$j['l']['v'] ]],
		];
		$j["z_t"]=[
			//label , editable, mandatory
			"p1"=>["l"=>["t"=>"T","v"=>"Description"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
		];
		$j["z_o"]=["p1"];
		$j["z_n"]=2;
		$j['m_i']=date("Y-m-d H:i:s");
		$j['m_u']=date("Y-m-d H:i:s");
		//$j['cnt']=0;
		$mongodb_con->insert($graph_things, $j);
		$mongodb_con->increment($graph_things, $j['i_of']['i'], "cnt", 1);
		// $d = $j;
		// $d['t_id'] = $d['_id'];
		// $d['_id'] = $j['i_of']['i'] . ":" . $d['l']['v'];
		// echo $d['_id'] . "<BR>";
		// $mongodb_con->insert($graph_things2, $d);
	}
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
	$graph_keywords = $db_prefix . "_graph_" . $_POST['graph_id'] . "_keywords";
	$graph_queue = $db_prefix . "_zd_queue_graph_" . $_POST['graph_id'];
	$graph_log = $db_prefix . "_zlog_graph_" . $_POST['graph_id'];
	$mongodb_con->drop_collection( $graph_things );
	$mongodb_con->drop_collection( $graph_keywords );
	$mongodb_con->drop_collection( $graph_queue );
	$mongodb_con->drop_collection( $graph_log );

	event_log( "system", "objects_delete_database", [
		"app_id"=>$config_param1,
		"graph_id"=>$_POST['graph_id'],
	]);

	json_response("success");
}
if( $_GET['action'] == "initialize" ){

	exit;

	$mongodb_con->drop_collection($graph_things);
	$mongodb_con->drop_collection($graph_things2);
	$mongodb_con->drop_collection($graph_queue);
	$mongodb_con->drop_collection($graph_keywords);

	$mongodb_con->create_collection($graph_things);
	$mongodb_con->create_collection($graph_things2);
	$mongodb_con->create_index($graph_things,  ["l.v"=>1], ["sparse"=>true]);
	$mongodb_con->create_index($graph_things,  ["cnt"=>1], ["sparse"=>true]);
	$mongodb_con->create_index($graph_things,  ["i_of.i"=>1,"l.v"=>1], ["unique"=>true,"sparse"=>true]);
	$mongodb_con->create_index($graph_things,  ["i_of.i"=>1,"_id"=>1], ["index"=>true,"sparse"=>true]);
	//$mongodb_con->create_index($graph_things2, ["t_id"=>1,"l"=>1], ["sparse"=>true]);

	$initial_id = 1;
	function getinitialid(){
		global $initial_id;
		$initial_id++;
		return "T".str_pad($initial_id,5,"0",STR_PAD_LEFT);
	}

	$things = [
		["_id"=>"T1",  "l"=>["t"=>"T", "v"=>"Root"              ],			"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"] 	],
		["_id"=>"T2",  "l"=>["t"=>"T", "v"=>"Person"            ],			"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"] 	],
		["_id"=>"T3",  "l"=>["t"=>"T", "v"=>"City"              ],	 		"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"] 	],
		["_id"=>"T4",  "l"=>["t"=>"T", "v"=>"Country"           ],			"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"] 	],
		["_id"=>"T5",  "l"=>["t"=>"T", "v"=>"Movie"             ],			"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"] 	],
		["_id"=>"T6",  "l"=>["t"=>"T", "v"=>"Directors"         ],			"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"] 	],
		["_id"=>"T7",  "l"=>["t"=>"T", "v"=>"Pincode"           ],			"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"] 	],
		["_id"=>"T8",  "l"=>["t"=>"T", "v"=>"State"             ],			"i_of"=> ["t"=>"GT", "i"=> "T1", "v"=>"Root"] 	],
		["_id"=>"T11", "l"=>["t"=>"T", "v"=>"Kalepu Satish"            ],			"i_of"=> ["t"=>"GT", "i"=> "T2", "v"=>"Person"] 	],
		["_id"=>"T12", "l"=>["t"=>"T", "v"=>"Kalepu Sagar"             ],			"i_of"=> ["t"=>"GT", "i"=> "T2", "v"=>"Person"] 	],
		["_id"=>"T13", "l"=>["t"=>"T", "v"=>"Pichika Purna Bindu"             ],			"i_of"=> ["t"=>"GT", "i"=> "T2", "v"=>"Person"] 	],
		["_id"=>"T14", "l"=>["t"=>"T", "v"=>"Allaka Padmavathi"           ],			"i_of"=> ["t"=>"GT", "i"=> "T2", "v"=>"Person"] 	],
		["_id"=>"T15", "l"=>["t"=>"T", "v"=>"Veera Raghavulu"           ],			"i_of"=> ["t"=>"GT", "i"=> "T2", "v"=>"Person"] 	],
		["_id"=>"T16", "l"=>["t"=>"T", "v"=>"Surya Kumari"            ],			"i_of"=> ["t"=>"GT", "i"=> "T2", "v"=>"Person"] 	],
		["_id"=>"T17", "l"=>["t"=>"T", "v"=>"Pavan Veerendra"            ],			"i_of"=> ["t"=>"GT", "i"=> "T2", "v"=>"Person"] 	],
		["_id"=>"T18", "l"=>["t"=>"T", "v"=>"Surya Siddharth"        ],			"i_of"=> ["t"=>"GT", "i"=> "T2", "v"=>"Person"] 	],
		["_id"=>"T19", "l"=>["t"=>"T", "v"=>"Hasini Sruthi"     ],			"i_of"=> ["t"=>"GT", "i"=> "T2", "v"=>"Person"] 	],
		["_id"=>"T20", "l"=>["t"=>"T", "v"=>"Sai Navadeep"         ],			"i_of"=> ["t"=>"GT", "i"=> "T2", "v"=>"Person"] 	],
		["_id"=>"T21", "l"=>["t"=>"T", "v"=>"Kakinada"          ],			"i_of"=> ["t"=>"GT", "i"=> "T3", "v"=>"City"] 	],
		["_id"=>"T22", "l"=>["t"=>"T", "v"=>"Rajahmundry"       ],			"i_of"=> ["t"=>"GT", "i"=> "T3", "v"=>"City"] 	],
		["_id"=>"T23", "l"=>["t"=>"T", "v"=>"Hyderabad"         ],			"i_of"=> ["t"=>"GT", "i"=> "T3", "v"=>"City"] 	],
		["_id"=>"T24", "l"=>["t"=>"T", "v"=>"Pitapuram"         ],			"i_of"=> ["t"=>"GT", "i"=> "T3", "v"=>"City"] 	],
		["_id"=>"T25", "l"=>["t"=>"T", "v"=>"Amalapuram"        ],			"i_of"=> ["t"=>"GT", "i"=> "T3", "v"=>"City"] 	],
		["_id"=>"T31", "l"=>["t"=>"T", "v"=>"India"             ],			"i_of"=> ["t"=>"GT", "i"=> "T4", "v"=>"Country"] 	],
		["_id"=>"T32", "l"=>["t"=>"T", "v"=>"Srilanka"          ],			"i_of"=> ["t"=>"GT", "i"=> "T4", "v"=>"Country"] 	],
		["_id"=>"T33", "l"=>["t"=>"T", "v"=>"Russia"            ],			"i_of"=> ["t"=>"GT", "i"=> "T4", "v"=>"Country"] 	],
		["_id"=>"T34", "l"=>["t"=>"T", "v"=>"Thailand"          ],			"i_of"=> ["t"=>"GT", "i"=> "T4", "v"=>"Country"] 	],
		["_id"=>"T35", "l"=>["t"=>"T", "v"=>"Singapore"         ],			"i_of"=> ["t"=>"GT", "i"=> "T4", "v"=>"Country"] 	],
		["_id"=>"T41", "l"=>["t"=>"T", "v"=>"Bahubali"          ],			"i_of"=> ["t"=>"GT", "i"=> "T5", "v"=>"Movie"] 	],
		["_id"=>"T42", "l"=>["t"=>"T", "v"=>"RRR"               ],			"i_of"=> ["t"=>"GT", "i"=> "T5", "v"=>"Movie"] 	],
		["_id"=>"T43", "l"=>["t"=>"T", "v"=>"Titanic"           ],			"i_of"=> ["t"=>"GT", "i"=> "T5", "v"=>"Movie"] 	],
		["_id"=>"T44", "l"=>["t"=>"T", "v"=>"True Lies"         ],			"i_of"=> ["t"=>"GT", "i"=> "T5", "v"=>"Movie"] 	],
		["_id"=>"T45", "l"=>["t"=>"T", "v"=>"Jurassic Park"     ],			"i_of"=> ["t"=>"GT", "i"=> "T5", "v"=>"Movie"] 	],
		["_id"=>"T60", "l"=>["t"=>"T", "v"=>"Andhra Pradesh"    ],			"i_of"=> ["t"=>"GT", "i"=> "T8", "v"=>"State"] 	],
		["_id"=>"T65", "l"=>["t"=>"T", "v"=>"Telangana"         ],			"i_of"=> ["t"=>"GT", "i"=> "T8", "v"=>"State"] 	],
	];

	//  label can be a link as well. 
	//  i_of is single per object

	foreach( $things as $i=>$j ){
		$j["props"]=[
			"p1"=>[["t"=>"T", "v"=>$j['l']['v'] ]],
		];
		$j["z_t"]=[
			//label , editable, mandatory
			"p1"=>["l"=>["t"=>"T","v"=>"Description"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
		];
		$j["z_o"]=["p1"];
		$j["z_n"]=2;
		$j['m_i']=date("Y-m-d H:i:s");
		$j['m_u']=date("Y-m-d H:i:s");
		//$j['cnt']=0;
		$mongodb_con->insert($graph_things, $j);
		$mongodb_con->increment($graph_things, $j['i_of']['i'], "cnt", 1);
		// $d = $j;
		// $d['t_id'] = $d['_id'];
		// $d['_id'] = $j['i_of']['i'] . ":" . $d['l']['v'];
		// echo $d['_id'] . "<BR>";
		// $mongodb_con->insert($graph_things2, $d);
	}
	$res = $mongodb_con->find( $graph_things );
	foreach( $res['data'] as $i=>$j ){
		send_to_keywords_queue($j['_id']);
	}

	event_log( "system", "objects_initialize1", [
		"app_id"=>$config_param1,
		"graph_id"=>$graph_id,
	]);

	echo "Initialized Database";
	exit;
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