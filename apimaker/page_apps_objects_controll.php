<?php

$apps_folder= "appsobjects";

if( $_POST['action'] == "load_keys" ){

	$res = $mongodb_con->find( $config_global_apimaker['config_mongo_prefix'] . "_objects", [
		"app_id"=>$config_param1,
	],[
		'projection'=>[
			'n'=>1, 't'=>1
		]
	]);

	json_response($res);

	exit;
}

if( $_POST['action'] == "context_load_things" ){
	if( $_POST['thing'] == "GT-ALL" ){

		$things = [
			["th"=>"0-0", "i"=>["t"=>"N", "v"=>"1"], "l"=>["t"=>"T", "v"=>"Root"] ],
			["th"=>"0-1", "i"=>["t"=>"N", "v"=>"1000"], "l"=>["t"=>"T", "v"=>"Person"] ],
			["th"=>"0-1", "i"=>["t"=>"N", "v"=>"1001"], "l"=>["t"=>"T", "v"=>"City"] ],
			["th"=>"0-1", "i"=>["t"=>"N", "v"=>"1002"], "l"=>["t"=>"T", "v"=>"Country"] ],
			["th"=>"0-1", "i"=>["t"=>"N", "v"=>"1003"], "l"=>["t"=>"T", "v"=>"Movie"] ],
			["th"=>"0-1", "i"=>["t"=>"N", "v"=>"1004"], "l"=>["t"=>"T", "v"=>"Directors"] ],
			["th"=>"0-1", "i"=>["t"=>"N", "v"=>"1005"], "l"=>["t"=>"T", "v"=>"Pincode"] ],
			["th"=>"0-1", "i"=>["t"=>"N", "v"=>"1006"], "l"=>["t"=>"T", "v"=>"State"] ],
		];

	}else if( $_POST['thing'] == "GT-1002" ){

		$things = [
			["th"=>"1002", "i"=>["t"=>"N", "v"=>"11000"], "l"=>["t"=>"T", "v"=>"Satish"] ],
			["th"=>"1002", "i"=>["t"=>"N", "v"=>"11001"], "l"=>["t"=>"T", "v"=>"Sagar"] ],
			["th"=>"1002", "i"=>["t"=>"N", "v"=>"11002"], "l"=>["t"=>"T", "v"=>"Kumar"] ],
			["th"=>"1002", "i"=>["t"=>"N", "v"=>"11003"], "l"=>["t"=>"T", "v"=>"Kishore"] ],
			["th"=>"1002", "i"=>["t"=>"N", "v"=>"11004"], "l"=>["t"=>"T", "v"=>"Veeresh"] ],
			["th"=>"1002", "i"=>["t"=>"N", "v"=>"11005"], "l"=>["t"=>"T", "v"=>"Ganesh"] ],
			["th"=>"1002", "i"=>["t"=>"N", "v"=>"11006"], "l"=>["t"=>"T", "v"=>"Nagesh"] ],
		];

	}else if( $_POST['thing'] == "Components" ){
		$things = [
			[
				"th"=> "Component", 
				"i"=>["t"=>"T", "v"=>"HTTPRequest"],
				"l"=>["t"=>"T", "v"=>"HTTPRequest"]
			],
			[
				"th"=> "Component", 
				"i"=>["t"=>"T", "v"=>"Database-MySql"],
				"l"=>["t"=>"T", "v"=>"Database-MySql"]
			],
			[
				"th"=> "Component", 
				"i"=>["t"=>"T", "v"=>"Database-MongoDb"],
				"l"=>["t"=>"T", "v"=>"Database-MongoDb"]
			],
			[
				"th"=> "Component", 
				"i"=>["t"=>"T", "v"=>"Database-DynamoDb"],
				"l"=>["t"=>"T", "v"=>"Database-DynamoDb"]
			],
			[
				"th"=> "Component", 
				"i"=>["t"=>"T", "v"=>"Database-Redis"],
				"l"=>["t"=>"T", "v"=>"Database-Redis"]
			],
			[
				"th"=> "Component", 
				"i"=>["t"=>"T", "v"=>"Dynamic-Table"],
				"l"=>["t"=>"T", "v"=>"Dynamic-Table"]
			],
			[
				"th"=> "Component", 
				"i"=>["t"=>"T", "v"=>"Elastic-Table"],
				"l"=>["t"=>"T", "v"=>"Elastic-Table"]
			]
		];
		
	}else{
		$things = [];
	}
	json_response([
		"status"=>"success",
		"things"=>$things
	]);
}
