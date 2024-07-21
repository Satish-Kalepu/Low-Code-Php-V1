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

$graph_dbs 			= $db_prefix . "_graphdbs";
$graph_things 		= $db_prefix . "_graph_things";
$graph_things2 		= $db_prefix . "_graph_things2";
$graph_instances 	= $db_prefix . "_graph_instances";
$graph_keywords 	= $db_prefix . "_graph_keywords";
$graph_links 		= $db_prefix . "_graph_links";
$graph_props 		= $db_prefix . "_graph_props";
$graph_queue 		= $db_prefix . "_zd_queue_objects";

//echo $graph_things;exit;

$objects_enabled = false;
if( isset($app['settings']['objects']['enabled']) ){
	if( $app['settings']['objects']['enabled'] ){
		$objects_enabled = true;
	}
}

if( $_GET['action'] == "uninstall" ){
	$mongodb_con->drop_collection($graph_things);
	$mongodb_con->drop_collection($graph_things2);
	$mongodb_con->drop_collection($graph_queue);
	$mongodb_con->drop_collection($graph_keywords);
}

if( $_POST['action'] == "objects_disable" ){

	$mongodb_con->drop_collection($graph_things);
	$mongodb_con->drop_collection($graph_things2);
	$mongodb_con->drop_collection($graph_queue);
	$mongodb_con->drop_collection($graph_keywords);
	$res = $mongodb_con->update_one( $db_prefix . "_apps", ["_id"=>$config_param1],[
		'$set'=>["settings.objects.enabled" => false ],
		'$unset'=>["settings.objects.run" => true ] 
	]);

	json_response($res);
}

function send_to_keywords_queue($object_id){
	global $mongodb_con;global $db_prefix;global $graph_queue;
	$task_id = generate_task_queue_id();
	$mongodb_con->insert( $graph_queue, [
		'_id'=>$task_id,
		'id'=>$task_id,
		'data'=>[
			"action"=> "thing_update",
			"graph_id"=>$object_id
		],
		'm_i'=>date("Y-m-d H:i:s")
	]);
}

if( $_POST['action'] == "objects_enable" ){

	if( isset($app['settings']['objects']['enabled']) ){
		if( $app['settings']['objects']['enabled'] ){
			json_response("fail", "Objects are already enabled");
		}
	}

	$res = $mongodb_con->update_one( $db_prefix . "_apps", ["_id"=>$config_param1],[
		"settings.objects.enabled" => true
	]);

	$mongodb_con->drop_collection($graph_things );
	$mongodb_con->drop_collection($graph_things2 );
	$mongodb_con->drop_collection($graph_queue);
	$mongodb_con->drop_collection($graph_keywords);

	$mongodb_con->create_collection($graph_things );
	$mongodb_con->create_collection($graph_things2 );
	$mongodb_con->create_index($graph_things,  ["l.v"=>1], ["sparse"=>true]);
	$mongodb_con->create_index($graph_things,  ["cnt"=>1], ["sparse"=>true]);
	$mongodb_con->create_index($graph_things,  ["i_of.i"=>1,"l.v"=>1], ["unique"=>true,"sparse"=>true]);
	$mongodb_con->create_index($graph_things,  ["i_of.i"=>1,"_id"=>1], ["index"=>true,"sparse"=>true]);
	//$mongodb_con->create_index($graph_things2, ["t_id"=>1,"l"=>1], ["sparse"=>true]);
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

	json_response("success");
	exit;
}
if( $_GET['action'] == "initialize" ){

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
	echo "Initialized Database";
	exit;
}

if( $_GET['action'] == "initialize2" ){
	require("page_apps_objects_controll2.php");
	exit;
}
if( $_GET['action'] == "initialize3" ){
	require("page_apps_objects_controll3.php");
	exit;
}

if( $_GET['action'] == "buildkeywords" ){
	$res= $mongodb_con->find( $graph_things );
	foreach( $res['data'] as $i=>$j ){
		echo $j['_id'] . ": " . $j['l']['v'] . "<BR>";
		send_to_keywords_queue( $j['_id'] );
	}
	exit;
}

$apps_folder = "appsobjects";

if( $_POST['action'] == "context_load_things" ){
	$things = [];
	if( $_POST['thing'] == "GT-ALL" ){
		$cond = [];
		$sort = [];
		if( $_POST['keyword'] ){
			$cond['p'] = ['$gte'=>$_POST['keyword'], '$lte'=>$_POST['keyword']."zzz" ];
			$sort = ['p'=>1];
			$res = $mongodb_con->find( $graph_keywords, $cond, [
				"sort"=>$sort, 
				"limit"=>100,
			]);
			//print_r( $res );
			foreach( $res['data'] as $i=>$j ){
				$things[] = [
					'l'=>['t'=>'T', 'v'=>$j['p']],
					'i_of'=>['i'=>$j['pid'],'v'=>$j['pl'],'t'=>"GT"],
					'i'=>$j['tid'],
					'ol'=>$j['l'],
					'm'=>isset($j['m'])?true:false,
					't'=>$j['t'],
				];
			}
		}else{
			$cond['cnt'] = ['$gt'=>1];
			$sort = ['cnt'=>-1];
			$res = $mongodb_con->find( $graph_things, $cond, [
				"sort"=>$sort, 
				"projection"=>['l'=>true, 'i'=>true,'i_of'=>true,'i'=>'$_id', '_id'=>false],
				"limit"=>100,
			]);
			foreach( $res['data'] as $i=>$j ){
				$things[] = $j;
			}
		}
		//$cond['l.v'] = ['$gt'=>"pe", '$lt'=>"pezzz" ];

	}else{
		$things = [];
	}
	json_response([
		"status"=>"success",
		"things"=>$things,
		"keyword"=>$_POST['keyword']??'',
	]);
}

if( $_POST['action'] == "context_load_things" ){
	$things = [];
	if( $_POST['thing'] == "GT-ALL" ){
		$cond = [];
		$sort = [];
		if( $_POST['keyword'] ){
			$cond['l.v'] = ['$gte'=>$_POST['keyword'], '$lte'=>$_POST['keyword']."zzz" ];
			$sort = ['l.v'=>1];
		}else{
			$cond['cnt'] = ['$gt'=>1];
			$sort = ['cnt'=>-1];
		}
		//$cond['l.v'] = ['$gt'=>"pe", '$lt'=>"pezzz" ];
		$res = $mongodb_con->find( $graph_things, $cond, [
			"sort"=>$sort, 
			"projection"=>['l'=>true, 'i'=>true,'i_of'=>true,'i'=>'$_id', '_id'=>false],
			"limit"=>100,
		]);
		foreach( $res['data'] as $i=>$j ){
			$things[] = $j;
			// foreach( $j['i_of'] as $ii=>$jj ){
			// 	$things[] = [
			// 		"i"=>$j['_id'],
			// 		"v"=>$j['l'], 
			// 		"i_of"=>$jj
			// 	];
			// }
		}

	}else{
		$things = [];
	}
	json_response([
		"status"=>"success",
		"things"=>$things,
		"keyword"=>$_POST['keyword']??'',
	]);
}

if( $_POST['action'] == "objects_load_basic" ){
	$res = $mongodb_con->find( $graph_things, ['cnt'=>['$exists'=>1]], [
		'projection'=>['i'=>'$_id', '_id'=>false, 'l'=>1,'i_of'=>1], 
		'sort'=>['cnt'=>-1], 
		'limit'=>500
	]);
	json_response($res);
	exit;
}

if( $_POST['action'] == "objects_load_object" ){
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( $res['data'] ){
		$res2 = $mongodb_con->find_one( $graph_things, [
			'_id'=>$res['data']['i_of']['i'] 
		], [
			'projection'=>['z_t'=>1,'z_o'=>1,'z_n'=>1]
		]);
		if( isset($res2['data']) ){
			$res['data']['i_of']['z_t'] = isset($res2['data']['z_t'])?$res2['data']['z_t']:[];
			$res['data']['i_of']['z_o'] = isset($res2['data']['z_o'])?$res2['data']['z_o']:[];
			$res['data']['i_of']['z_n'] = isset($res2['data']['z_n'])?$res2['data']['z_n']:1;
		}else{
			$res['data']['i_of']['z_t'] = [];
			$res['data']['i_of']['z_o'] = [];
			$res['data']['i_of']['z_n'] = 1;
		}
	}
	json_response($res);
	exit;
}

if( $_POST['action'] == "objects_load_records" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Object ID");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Need object ID");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Node not found");
	}
	$cond = ['i_of.i'=>$_POST['object_id']];

	$res = $mongodb_con->count( $graph_things, $cond );
	$cnt = (int)$res['data'];

	if( $_POST['from'] ){
		$cond['l.v'] = ['$gt'=> $_POST['from']];
	}
	if( $_POST['last'] ){
		$cond['l.v'] = ['$gt'=> $_POST['last']];
	}
	$res = $mongodb_con->find( $graph_things, $cond, [
		'projection'=>['l'=>1,'props'=>1,'i_of'=>1],
		'sort'=>['l.v'=>1],
		'limit'=>100,
	]);
	$res['cnt'] = $cnt;
	json_response($res);
	exit;
}

if( $_POST['action'] == "objects_load_browse_list" ){

	$res = $mongodb_con->count( $graph_things, [] );
	$cnt = (int)$res['data'];
	$cond = [];
	if( $_POST['sort'] == "label" ){
		if( $_POST['order'] == "asc" ){
			$sort = ['l.v'=>1];
			if( $_POST['from'] ){
				$cond['l.v'] = ['$gte'=> $_POST['from']];
			}
			if( $_POST['last'] ){
				$cond['l.v'] = ['$gte'=> $_POST['last']];
			}
		}else{
			$sort = ['l.v'=>-1];
			if( $_POST['from'] ){
				$cond['l.v'] = ['$lte'=> $_POST['from']];
			}
			if( $_POST['last'] ){
				$cond['l.v'] = ['$lte'=> $_POST['last']];
			}
		}
	}else if( $_POST['sort'] == "ID" ){
		if( $_POST['order'] == "asc" ){
			$sort = ['_id'=>1];
			if( $_POST['from'] ){
				$cond['_id'] = ['$gte'=> $_POST['from']];
			}
			if( $_POST['last'] ){
				$cond['_id'] = ['$gte'=> $_POST['last']];
			}
		}else{
			$sort = ['_id'=>-1];
			if( $_POST['from'] ){
				$cond['_id'] = ['$lte'=> $_POST['from']];
			}
			if( $_POST['last'] ){
				$cond['_id'] = ['$lte'=> $_POST['last']];
			}
		}
	}else if( $_POST['sort'] == "nodes" ){
		$cond['cnt'] = ['$gt'=>1];
		if( $_POST['order'] == "asc" ){
			$sort = ['cnt'=>1];
		}else{
			$sort = ['cnt'=>-1];
		}
	}
	$res = $mongodb_con->find( $graph_things, $cond, [
		'projection'=>['l'=>1,'i_of'=>1, 'm_i'=>1, 'm_u'=>1,'cnt'=>1],
		'sort'=>$sort,
		'limit'=>100,
	]);
	$res['cnt'] = $cnt;
	json_response($res);
	exit;
}

if( $_POST['action'] == "objects_delete_field" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Object ID");
	}else if( !preg_match("/^[a-z0-9]{2,100}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Need object ID");
	}
	if( !isset($_POST['prop']) ){
		json_response("fail", "Need Prop ID");
	}else if( !preg_match("/^p[0-9]+$/i", $_POST['prop']) ){
		json_response("fail", "Need Prop ID");
	}

	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Node not found");
	}

	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$_POST['object_id']], [
		'$unset'=>[
			'z_t.' . $_POST['prop']=>true,
		],
		'$pull'=>[
			'z_o'=>$_POST['prop']
		]
	]);

	json_response($res);

	exit;
}

if( $_POST['action'] == "object_create_object" ){

	if( !isset($_POST['data']['l']['v']) ){
		json_response("fail", "Need Label");
	}else if( !preg_match("/^[a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}$/i", $_POST['data']['l']['v']) ){
		json_response("fail", "Need Label in simple format");
	}

	if( !isset($_POST['data']['i_of']['i']) ){
		json_response("fail", "Need Instance");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['data']['i_of']['i']) ){
		json_response("fail", "Instance ID Incorrect format");
	}

	if( !isset($_POST['data']['i_of']['v']) ){
		json_response("fail", "Need Instance Label");
	}else if( !preg_match("/^[a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}$/i", $_POST['data']['i_of']['v']) ){
		json_response("fail", "Need Instance Label in simple format");
	}

	$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$_POST['data']['i_of']['i'], 'l'=>$_POST['data']['l']] );
	if( $res['data'] ){
		json_response("fail", "Duplicate Node");
	}

	$id = uniqid();
	$v = [
		"_id"=>$id,
		"l"=>$_POST['data']['l'],
		"i_of"=>$_POST['data']['i_of'],
	];
	$res = $mongodb_con->insert( $graph_things, $v );
	if( $res['status'] == "fail" ){
		if( preg_match("/duplicate/i", $res['error']) && preg_match("/_id/", $res['error']) ){
			json_response(["status"=>"fail","error"=>"Duplicate primary key"]);
		}else{
			json_response($res);
		}
	}
	send_to_keywords_queue($id);
	$res2 = $mongodb_con->increment( $graph_things, $_POST['data']['i_of']['i'], "cnt", 1 );
	json_response($res);
	exit;
}

if( $_POST['action'] == "objects_edit_label" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$object_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$object_id] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	$object = $res['data'];
	if( !isset($_POST['label']) ){
		json_response("fail", "Need Label");
	}else if( !is_array($_POST['label']) ){
		json_response("fail", "Need Label");
	}else if( !isset($_POST['label']['t']) || !isset($_POST['label']['v']) ){
		json_response("fail", "Need Label");
	}
	$label = $_POST['label'];

	$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$object['i_of']['i'], 'l.v'=>$label['v'], '_id'=>['$ne'=>$object_id] ] );
	if( $res['data'] ){
		json_response("fail", "Duplicate Node Exists");
	}

	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
		'l'=>$label,
		'updated'=>date("Y-m-d H:i:s")
	]);

	send_to_keywords_queue($object_id);

	json_response( $res );
	exit;
}
if( $_POST['action'] == "objects_edit_alias" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$object_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$object_id] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	$object = $res['data'];
	if( !isset($_POST['alias']) ){
		json_response("fail", "Need alias");
	}else if( !is_array($_POST['alias']) ){
		json_response("fail", "Need alias");
	}else{
		if( array_keys($_POST['alias'])[0] !== 0 ){
			$_POST['alias'] = [];
		}
		$als = [];
		for($i=0;$i<sizeof($_POST['alias']);$i++){
			$v =$_POST['alias'][$i];
			if( !isset($v['t']) || !isset($v['v']) || $v['v'] == "" ){
				array_splice($_POST['alias'],$i,1);$i--;
			}else if( strtolower($v['v']) == strtolower($object['l']['v']) ){
				json_response("fail", "Label and Alias should be different");
			}
			if( in_array(strtolower($v['v']), $als) ){
				array_splice($_POST['alias'],$i,1);$i--;
			}else{
				$als[] = strtolower($v['v']);
			}
		}
	}
	//print_r( $als );exit;
	if( sizeof($_POST['alias']) ){
		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
			'al'=>$_POST['alias'],
			'updated'=>date("Y-m-d H:i:s")
		]);
	}else{
		$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
			'$unset'=>[ 'al'=>true ],
			'$set'=>['updated'=>date("Y-m-d H:i:s")],
		]);
	}
	send_to_keywords_queue($object_id);
	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_edit_i_of" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$object_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$object_id] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	$object = $res['data'];
	if( !isset($_POST['i_of']) ){
		json_response("fail", "Need Instance Of");
	}else if( !is_array($_POST['i_of']) ){
		json_response("fail", "Need Instance Of");
	}else if( !isset($_POST['i_of']['t']) || !isset($_POST['i_of']['v']) ){
		json_response("fail", "Need Instance Of");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['i_of']['i']) && !preg_match("/^[0-9]+$/i", $_POST['i_of']['i'] ) ){
		json_response("fail", "Instance id incorrect");
	}
	$i_of = $_POST['i_of'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$i_of['i']] );
	if( !$res['data'] ){
		json_response("fail", "Instance not found");
	}
	$instance = $res['data'];

	$res = $mongodb_con->find_one( $graph_things, ['i_of.i'=>$i_of['i'], 'l.v'=>$object['l']['v'], '_id'=>['$ne'=>$object_id] ] );
	if( $res['data'] ){
		json_response("fail", "Duplicate Node Exists in Instance: " . $i_of['v']);
	}
	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$object_id ], [
		'i_of'=>$i_of,
		'updated'=>date("Y-m-d H:i:s")
	]);
	send_to_keywords_queue($object_id);

	$res2 = $mongodb_con->increment( $graph_things, $object['i_of']['i'], "cnt", -1 );
	$res2 = $mongodb_con->increment( $graph_things, $_POST['data']['i_of']['i'], "cnt", 1 );

	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_object_add_field" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	// field
	// prop
	if( !isset($_POST['field']) ){
		json_response("fail", "Incorrect data 1");
	}else if( !preg_match("/^p[0-9]+$/",$_POST['field']) ){
		json_response("fail", "Incorrect data 2");
	}
	if( !isset($_POST['prop']) ){
		json_response("fail", "Incorrect data 3");
	}else if( !is_array($_POST['prop']) ){
		json_response("fail", "Incorrect data 4");
	}

	if( isset($res['data']['z_t'][ $_POST['field'] ]) ){
		json_response("fail", "Field key ".$_POST['field']." already exists");
	}
	$n = intval(str_replace("p","",$_POST['field']));
	if( $n < $res['data']['z_n'] ){
		json_response("fail", "Field keyindex ".$_POST['z_n']." already exists");
	}

	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$_POST['object_id']], [
		'$set'=>[ 
			'z_t.'. $_POST['field']=>$_POST['prop'],
			'z_n'=>$_POST['z_n']
		],
		'$push'=>['z_o'=>$_POST['field']],
	]);
	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_save_object_z_t" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	// field
	// prop
	if( !isset($_POST['field']) ){
		json_response("fail", "Incorrect data 1");
	}else if( !preg_match("/^p[0-9]+$/",$_POST['field']) ){
		json_response("fail", "Incorrect data 2");
	}
	if( !isset($_POST['prop']) ){
		json_response("fail", "Incorrect data 3");
	}else if( !is_array($_POST['prop']) ){
		json_response("fail", "Incorrect data 4");
	}
	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$_POST['object_id']], [
		'z_t.'. $_POST['field']=>$_POST['prop'],
	]);
	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_save_z_o" ){
	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	if( !isset($_POST['z_o']) ){
		json_response("fail", "Incorrect data 1");
	}else if( !is_array($_POST['z_o']) ){
		json_response("fail", "Incorrect data 2");
	}
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$_POST['object_id']] );
	if( !$res['data'] ){
		json_response("fail", "Thing not found");
	}
	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$_POST['object_id']], [
		'z_o'=>$_POST['z_o'],
	]);
	json_response( $res );
	exit;
}

if( $_POST['action'] == "objects_save_props" ){

	if( !isset($_POST['object_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['object_id']) && !preg_match("/^[0-9]+$/i", $_POST['object_id']) ){
		json_response("fail", "Thing id incorrect");
	}
	$thing_id = $_POST['object_id'];
	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$thing_id] );
	if( !$res['data'] ){
		json_response("fail", "thing not found");
	}
	$res2 = $mongodb_con->find_one( $graph_things, ['_id'=>$res['data']['i_of']['i']] );
	if( !$res2['data'] ){
		json_response("fail", "parent not found");
	}
	$parent = $res2['data'];

	if( !isset($_POST['props']) ){
		json_response("fail", "Data missing");
	}else if( !is_array($_POST['props']) ){
		json_response("fail", "Data missing");
	}

	$props = $_POST['props'];

	foreach( $props as $field=>$values ){
		if( !is_array($values) ){
			json_response("fail", "Property `" . $field . "` has invalid value");
		}
		if( isset($parent['z_t'][ $field ]) ){
			if( $parent['z_t'][ $field ]['t']['k'] == "O" ){
				for($pi=0;$pi<sizeof($props[ $field ]);$pi++){
					$pd = $props[ $field ][ $pi ];
					$f = false;
					foreach( $parent['z_t'][ $field ]['z']['z_t'] as $fd=>$fn ){
						if( isset( $pd[ $fd ] ) ){
							if( isset($pd[ $fd ]['t']) && isset($pd[ $fd ]['v']) ){
								if( $pd[ $fd ]['v'] ){
									$f = true;
								}
							}else{
								json_response("fail", "Property `" . $field . "` item: ".($pi+1)." property: " . $fn['l']['v'] . " has invalid value: ".json_encode($pd[$fd]));
							}
						}
					}
					if( $f == false ){
						array_slice( $props[ $field ], $pi, 1);
						$pi--;
					}
				}
			}else{
				foreach( $values as $pi=>$pd ){
					if( isset($pd['t']) && isset($pd['v']) ){

					}else{
						json_response("fail", "Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd));
					}
				}
			}
		}
	}
	//print_r( $data );

	$data = [
		'updated' => date("Y-m-d H:i:s"),
		'props' => $props
	];
	
	$res = $mongodb_con->update_one( $graph_things, ['_id'=>$thing_id], $data );
	json_response($res);
	exit;
}

if( $_POST['action'] == "objects_save_object" ){

	if( !isset($_POST['data']) ){
		json_response("fail", "Data missing");
	}else if( !is_array($_POST['data']) ){
		json_response("fail", "Data missing");
	}

	if( !isset($_POST['data']['_id']) ){
		json_response("fail", "Need Thing id");
	}else if( !preg_match("/^[a-z0-9]{2,24}$/i", $_POST['data']['_id']) && !preg_match("/^[0-9]+$/i", $_POST['data']['_id']) ){
		json_response("fail", "Thing id incorrect");
	}

	$data = $_POST['data'];
	$thing_id = $data['_id'];
	unset($data['_id']);

	$res = $mongodb_con->find_one( $graph_things, ['_id'=>$thing_id] );
	if( !$res['data'] ){
		json_response("fail", "thing not found");
	}

	if( !isset($data['l']) ){
		json_response("fail", "Need Label");
	}else if( !preg_match("/^[a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}$/i", $data['l']['v']) ){
		json_response("fail", "Need Label in simple format");
	}

	if( !isset($data['i_of']) ){
		json_response("fail", "Need Instance");
	}else if( !is_array($data['i_of']) ){
		json_response("fail", "Instance should be array");
	}
	if( !isset($data['i_of']['v']) ){
		json_response("fail", "Need Instance Label");
	}else if( !preg_match("/^[a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}$/i", $data['i_of']['v']) ){
		json_response("fail", "Need Instance Label in simple format");
	}
	if( !isset($data['i_of']['i']) ){
		json_response("fail", "Need Instance Id");
	}else if( !preg_match("/^[0-9]+$/i", $data['i_of']['i']) ){
		json_response("fail", "Need Instance Id in simple format");
	}
	unset($data['i_of']['z_t']);unset($data['i_of']['z_o']);

	$res = $mongodb_con->find_one( $graph_things, ['_id'=>['$ne'=>$thing_id], 'i_of.i'=>$data['i_of']['i'], 'l.v'=>$data['l']['v']] );
	if( $res['data'] ){
		json_response("fail", "Duplicate Node exists (". $res['data']['_id'].")");
	}
	if( !isset($data['props']) ){
		json_response("fail", "Need Props");
	}else if( !is_array($data['props']) ){
		json_response("fail", "Props incorrect format");
	}

	//exit;
	foreach( $data['props'] as $field=>$prop ){
		if( !is_array($prop) ){
			json_response("fail", "Property `" . $field . "` has invalid value");
		}
		foreach( $prop as $pi=>$pd ){
			if( isset($pd['t']) && isset($pd['v']) ){

			}else{
				json_response("fail", "Property `" . $field . "` item: ".($pi+1)." has invalid value: ".json_encode($pd));
			}
		}
	}
	//print_r( $data );

	$data['updated'] = date("Y-m-d H:i:s");

	$res = $mongodb_con->update_one( $graph_things,['_id'=>$thing_id], $data );
	json_response($res);
	exit;
}

if( 1==2 ){
	for($i=0;$i<3;$i++){
		$max_res = $mongodb_con->get_max_id( $graph_things );
		if( $max_res['status'] == "success" ){
			$max_id = $max_res['data']+10;
		}else{
			json_rsponse($max_res);
		}
		$v = [
			"_id"=>$max_id,
			"l"=>$data['l'],
			"i_of"=>[ $data['i_of'] ],
			"props"=>[
				"c_date"=>date("Y-m-d H:i:s"),
			]
		];
		$res = $mongodb_con->insert( $graph_things, $v );
		if( $res['status'] == "fail" ){
			if( preg_match("/duplicate/i", $res['error']) && preg_match("/_id/", $res['error']) ){
				// retry 
			}else{
				json_response($res);
				break;
			}
		}else{break;}
	}
	if( $res['status'] == "success" ){
		json_response($res);
	}else{
		json_response(["status"=>"fail", "error"=>"Maximum retries reached. insert failed ". $res['error']]);
	}
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

	json_response([
		"status"=>"success",
		"data"=>[
			"internal"=>[],
			"external"=>[],
		]
	]);

	exit;
}

//echo $config_param1 . ": " . $config_param2 . ": " . $config_param3 . ": " . $config_param4 . "<BR>";exit;
if( $config_param3 ){

}

// $v = find_permutations("Mohandas");
// echo "final perms<BR>";
// print_r($v);exit;