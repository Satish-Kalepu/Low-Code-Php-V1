<?php 

class ClassName{
	public $version = 1.0;
	public $last_edited = "2024-11-16 16:53";
	public $developer = "Apparao";
	public $input_template = [
		"var1" => ["t"=> "T", ""=>"", "v"=> ""]
	];
	public $methods = [
		"simpleInterest"=> [
			"inputs"=> [
				"principle"=> ["t"=>"N", "v"=>"100000", "m"=>true],
				"rate_of_interest"=> ["t"=>"N", "v"=>"12", "m"=>true],
				"duration"=> ["t"=>"N", "v"=>"1", "m"=>true],
				"duration_type"=> ["t"=>"T", "v"=>"Years", "m"=>true],
			],
			"outputs"=> [
				"interest"=>["t"=>"N", "v"=>"0"],
			],
			"test_cases"=>[
				[
					"input"=>[
						"principle"=>100000,
						"rate_of_interest"=>12,
						"duration"=>2
					],
					"output"=>["status"=>"success", "data"=>["interest"=>2000] ]
				],
				[
					"input"=>[
						"principle"=>1000,
						"rate_of_interest"=>12,
						"duration"=>12
					],
					"output"=>["status"=>"success", "data"=>["interest"=>120] ]
				]
			]
		]
	];
	public function _construct($inputs){
		return ['status'=>'success', 'data'=>['t'=>'T', 'v'=>'AllOk'] ];
	}
	public function simpleInterest($inputs){
		$p = $inputs['principle'];
		$r = $inputs['rate_of_interest']/100;
		$t = $inputs['duration'];
		$i = round($p*$r*$t/12,2);
		return ['status'=>'success', 'data'=>['interest'=>$i] ];
	}
	function getGlobalVariables($inputs = []){
		return ['status'=>'success', 'data'=>['t'=>'O','v'=>$GLOBALS] ];
	}
}