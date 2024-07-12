<?php

if( $_GET['action'] == "initialize2" ){

	/*
	[0] => budget   10
    [1] => genres   15
    [2] => homepage  20
    [3] => id  90
    [4] => keywords   25
    [5] => original_language   30
    [6] => original_title    35
    [7] => overview p5
    [8] => popularity   85
    [9] => production_companies  40
    [10] => production_countries   45
    [11] => release_date 50
    [12] => revenue  55
    [13] => runtime  60
    [14] => spoken_languages  65
    [15] => status  70
    [16] => tagline p4
    [17] => title  p3 
    [18] => vote_average  75
    [19] => vote_count  80
	*/

    $country_i = "T4";
	$res = $mongodb_con->update_one($graph_things, ['_id'=>$country_i], [
		"z_t"=>[
			//label , editable, mandatory
			"p1"=>["l"=>["t"=>"T","v"=>"Description"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p3"=>["l"=>["t"=>"T","v"=>"ISO ID"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p4"=>["l"=>["t"=>"T","v"=>"Name"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
		],
		"z_o"=>["p1", "p3", "p4"],
		"z_n"=>50,
	]);
	print_r( $res );
	$company_i = uniqid();
	$data = [
		"_id"=>$company_i,
		"l"=>["t"=>"T", "v"=> "Production Company"],
		"i_of"=>["i"=>"T1","v"=>"Root"],
    	"props"=>[
			"p1"=>[["t"=>"T", "v"=>"Production Company" ]],
		],
		'm_i'=>date("Y-m-d H:i:s"),
		'm_u'=>date("Y-m-d H:i:s"),
	];
	$mongodb_con->insert($graph_things, $data );
	$mongodb_con->increment($graph_things, "T1", "cnt", 1);

	$language_i = uniqid();
	$data = [
		"_id"=>$language_i,
		"l"=>["t"=>"T", "v"=> "Languages"],
		"i_of"=>["i"=>"T1","v"=>"Root"],
		'm_i'=>date("Y-m-d H:i:s"),
		'm_u'=>date("Y-m-d H:i:s"),
	];
	$mongodb_con->insert($graph_things, $data );
	$mongodb_con->increment($graph_things, "T1", "cnt", 1);

	$genre_i = uniqid();
	$data = [
		"_id"=>$genre_i,
		"l"=>["t"=>"T", "v"=> "Movie Genre"],
		"i_of"=>["i"=>"T1","v"=>"Root"],
		'm_i'=>date("Y-m-d H:i:s"),
		'm_u'=>date("Y-m-d H:i:s"),
	];
	$mongodb_con->insert($graph_things, $data );
	$mongodb_con->increment($graph_things, "T1", "cnt", 1);

	$imdb_id = uniqid();
	$data = [
		"_id"=>$imdb_id,
		"l"=>["t"=>"T", "v"=> "IMDB Database"],
		"i_of"=>["t"=>"GT", "i"=>"T1", "v"=>"Root"],
		"z_t"=>[
			//label , editable, mandatory
			"p3"=>["l"=>["t"=>"T","v"=>"Title"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p4"=>["l"=>["t"=>"T","v"=>"Tagline"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p5"=>["l"=>["t"=>"T","v"=>"Overview"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p10"=>["l"=>["t"=>"T","v"=>"Budget"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p15"=>["l"=>["t"=>"T","v"=>"Genres"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p20"=>["l"=>["t"=>"T","v"=>"Homepage"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p25"=>["l"=>["t"=>"T","v"=>"Keywords"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p30"=>["l"=>["t"=>"T","v"=>"Original Language"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p35"=>["l"=>["t"=>"T","v"=>"Original Title"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p40"=>["l"=>["t"=>"T","v"=>"Production Company"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p45"=>["l"=>["t"=>"T","v"=>"Production Countries"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p50"=>["l"=>["t"=>"T","v"=>"Release Date"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p55"=>["l"=>["t"=>"T","v"=>"Revenue"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p60"=>["l"=>["t"=>"T","v"=>"Runtime"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p65"=>["l"=>["t"=>"T","v"=>"Spoken Languages"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p70"=>["l"=>["t"=>"T","v"=>"Status"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p75"=>["l"=>["t"=>"T","v"=>"Vote Average"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p80"=>["l"=>["t"=>"T","v"=>"Vote Count"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p85"=>["l"=>["t"=>"T","v"=>"Popularity"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
			"p90"=>["l"=>["t"=>"T","v"=>"IMDB Id"], "t"=>["t"=>"KV", "v"=>"Text", "k"=>"T"], "e"=>false, "m"=>false],
		],
		"z_o"=>["p3", "p4", "p5", "p10", "p15", "p20", "p25", "p30", "p35", "p40", "p45", "p50", "p55", "p60", "p65", "p70", "p75", "p80",  "p85", "p90" ],
		"z_n"=>95,
		'm_i'=>date("Y-m-d H:i:s"),
		'm_u'=>date("Y-m-d H:i:s"),
	];
	$mongodb_con->insert($graph_things, $data );
	$mongodb_con->increment($graph_things, "T1", "cnt", 1);

	$fp = fopen("./sample_data/tmdb_5000_movies.csv", "r");
	$cnt = 0;
	header("Content-Type: text/plain");
	$d = fgetcsv($fp, 2048);
	while( $d = fgetcsv($fp, 4096) ){
	if( sizeof($d) > 3 ){
	if( trim($d[17]) ){

		//print_r( $d );

		$production_countries = json_decode($d[10], true);
		//print_r( $production_countries );
		$pc = [];
		if( is_array($production_countries) ){
			foreach( $production_countries as $i=>$j ){
				$res = $mongodb_con->find_one($graph_things, ["i_of.i"=>$country_i, "l.v"=>$j['name']]);
				if( $res['data'] ){
					$id = $res['data']['_id'];
				}else{
					$id = uniqid();
					$mongodb_con->insert($graph_things,[
						"_id"=>$id, 
						"l"=>["t"=>"T", "v"=>$j['name']],
						"i_of"=>["t"=>"GT", "i"=>$country_i,"v"=>"Country"],
						"props"=>[
							"p3"=>[ ["t"=>"T", "v"=>$j['iso_639_1'] ] ],
							"p4"=>[ ["t"=>"T", "v"=>$j['name'] ] ],
						],
						'm_i'=>date("Y-m-d H:i:s"),
						'm_u'=>date("Y-m-d H:i:s"),						
					]);
					$mongodb_con->increment($graph_things, $country_i, "cnt", 1);
				}
				$pc[] = ["t"=>"GT", "v"=>$j['name'], "i"=>$id];
			}
		}

		$languages = json_decode($d[14],true);
		$lc = [];
		if( is_array($languages) ){
			foreach( $languages as $i=>$j ){if( $j['name'] ){
				$res = $mongodb_con->find_one($graph_things, ["i_of.i"=>$language_i, "l.v"=>$j['name']]);
				if( $res['data'] ){
					$id = $res['data']['_id'];
				}else{
					$id = uniqid();
					$mongodb_con->insert($graph_things,[
						"_id"=>$id, 
						"l"=>["t"=>"T", "v"=>$j['name']],
						"i_of"=>["t"=>"GT", "i"=>$language_i,"v"=>"Languages"],
						'm_i'=>date("Y-m-d H:i:s"),
						'm_u'=>date("Y-m-d H:i:s"),
					]);
					$mongodb_con->increment($graph_things, $language_i, "cnt", 1);
				}
				$lc[] = ["t"=>"GT", "v"=>$j['name'], "i"=>$id];
			}}
		}

		$genres = json_decode($d[1],true);
		//print_r( $genres );
		$gc = [];
		if( is_array($genres) ){
			foreach( $genres as $i=>$j ){
				$res = $mongodb_con->find_one($graph_things, ["i_of.i"=>$genre_i, "l.v"=>$j['name']]);
				if( $res['data'] ){
					$id = $res['data']['_id'];
				}else{
					$id = uniqid();
					$mongodb_con->insert($graph_things,[
						"_id"=>$id, 
						"l"=>["t"=>"T", "v"=>$j['name']],
						"i_of"=>["t"=>"GT", "i"=>$genre_i,"v"=>"Genre"],
						'm_i'=>date("Y-m-d H:i:s"),
						'm_u'=>date("Y-m-d H:i:s"),						
					]);
					$mongodb_con->increment($graph_things, $genre_i, "cnt", 1);
				}
				$gc[] = ["t"=>"GT", "v"=>$j['name'], "i"=>$id];
			}
		}

		$keywords = json_decode($d[4],true);
		$kc =[];
		if( is_array($keywords) ){
			foreach( $keywords as $i=>$j ){
				$kc[] = ["t"=>"T", "v"=>$j['name']];
			}
		}

		$production_companies = json_decode($d[9],true);
		$pcomp = [];
		if( is_array($production_companies) ){
			foreach( $production_companies as $i=>$j ){
				$res = $mongodb_con->find_one($graph_things, ["i_of.i"=>$company_i, "l.v"=>$j['name']]);
				if( $res['data'] ){
					$id = $res['data']['_id'];
				}else{
					$id = uniqid();
					$mongodb_con->insert($graph_things,[
						"_id"=>$id, 
						"l"=>["t"=>"T", "v"=>$j['name']],
						"i_of"=>["t"=>"GT", "i"=>$company_i,"v"=>"Production Company"],
						'm_i'=>date("Y-m-d H:i:s"),
						'm_u'=>date("Y-m-d H:i:s"),
					]);
					$mongodb_con->increment($graph_things, $company_i, "cnt", 1);
				}
				$pcomp[] = ["t"=>"GT", "v"=>$j['name'], "i"=>$id];
			}
		}

		$id = uniqid();
		$data = [
			"_id"=>$id,
			"l"=>["t"=>"T", "v"=> $d[17]],
			"i_of"=>["t"=>"GT","i"=>$imdb_id,"v"=>"IMDB Database"],
	    	"props"=>[
				"p10"=>[ ["t"=>"T", "v"=>$d[0] ] ],
				"p15"=>$gc,
				"p20"=>[ ["t"=>"T", "v"=>$d[2] ] ],
				"p90"=>[ ["t"=>"T", "v"=>$d[3] ] ],
				"p25"=>$kc,
				"p30"=>[ ["t"=>"T", "v"=>$d[5] ] ],
				"p35"=>[ ["t"=>"T", "v"=>$d[6] ] ],
				"p5"=>[ ["t"=>"T", "v"=>$d[7] ] ],
				"p85"=>[ ["t"=>"T", "v"=>$d[8] ] ],
				"p40"=>$pcomp,
				"p45"=>$pc,
				"p50"=>[ ["t"=>"T", "v"=>$d[11] ] ],
				"p55"=>[ ["t"=>"T", "v"=>$d[12] ] ],
				"p60"=>[ ["t"=>"T", "v"=>$d[13] ] ],
				"p65"=>$lc,
				"p70"=>[ ["t"=>"T", "v"=>$d[15] ] ],
				"p4"=>[ ["t"=>"T", "v"=>$d[16] ] ],
				"p3"=>[ ["t"=>"T", "v"=>$d[17] ] ],
				"p75"=>[ ["t"=>"T", "v"=>$d[18] ] ],
				"p80"=>[ ["t"=>"T", "v"=>$d[19] ] ],
			],
			'm_i'=>date("Y-m-d H:i:s"),
			'm_u'=>date("Y-m-d H:i:s"),
		];
		$mongodb_con->insert($graph_things, $data);
		$mongodb_con->increment($graph_things, $imdb_id, "cnt", 1);
		//if( $cnt > 1000 ){break;}

	}else{
		echo "skipped: " . $cnt . ": "; print_r( $d );
	}
	}else{
		echo "skipped2: " . $cnt . ": "; print_r( $d );
	}
	$cnt++;
	}

	echo '#'.$cnt.'#';
	exit;

}