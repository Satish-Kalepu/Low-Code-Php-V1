<?php

if( $_GET['action'] == "initialize3" ){

	header("Content-Type: text/plain");

	function find_or_create($label, $iof){
		global $mongodb_con;
		global $graph_things;
		echo "Find or create: " . $label . ": ". print_r($iof,true) . "\n";
		$res6 = $mongodb_con->find_one($graph_things, ['i_of.i'=>$iof['i'], 'l.v'=>$label], ['projection'=>['_id'=>1]] );
		if( $res6['data'] ){
			echo "SUB: found label: " . $label."\n";
			return $res6['data']['_id'];
		}else{
			$id = uniqid();
			$res = $mongodb_con->insert($graph_things, [
				'_id'=>$id,
				'l'=>['v'=>$label,'t'=>'T'],
				'i_of'=>$iof,
				'i_t'=>['t'=>'T','v'=>"N"],
				'm_i'=>date("Y-m-d H:i:s"),'m_u'=>date("Y-m-d H:i:s"),
			]);
			send_to_keywords_queue($id);
			print_r($res);
			echo "SUB: created label: " . $label.": ". $id . "\n";
			return $id;
		}
	}

	$objects = json_decode('[
		{
			"l": "Satish Kalepu",
			"i_of": "Person",
			"props": {
				"Gender": ["Male"],
				"Siblings": ["Person:Sagar Kalepu"], "Parents": ["Person:Veera Raghavulu", "Person:Surya Kumari"],
				"Spouse": ["Person:Pichika Purna Bindu"], 
				"Children": ["Person:Pavan Veerendra", "Person:Surya Siddharth"], 
				"Occupation": ["Software Engineer"], 
				"Positions Held": ["AVP CarTradeTech", "Senior Programmer in CarTradeIndia"],
				"Date of Birth": ["1983-01-03"],
				"Place of Birth": ["City:Razole"],
				"Description": "Satish lives in Kakinada city and works for a company called CArTrade Tech Limited"
			}
		},
		{
			"l": "Sagar Kalepu",
			"i_of": "Person",
			"props": {
				"Gender": ["Male"],
				"Siblings": ["Person:Satish Kalepu"], "Parents": ["Person:Veera Raghavulu", "Person:Surya Kumari"],
				"Spouse": ["Person:Allaka Padmavathi"], 
				"Children": ["Person:Hasini Sruthi", "Person:Sai Navadeep"], 
				"Occupation": ["Software Engineer"], 
				"Positions Held": ["SAP Basis Administrator in Utilli", "Basis Administrator in UNIDO"],
				"Date of Birth": ["1984-07-10"],
				"Place of Birth": ["City:Razole"],
				"Description": "Sagar lives in Kakinada city and a good hardware engineer"
			}
		},
		{
			"l": "Pichika Purna Bindu",
			"i_of": "Person",
			"props": {
				"Gender": ["Female"],
				"Parents": ["Person:Surya Satyanarayana", "Person:Mavuri Bhavani"],
				"Spouse": ["Person:Satish Kalepu"], 
				"Children": ["Person:Pavan Veerendra", "Person:Surya Siddharth"], 
				"Occupation": ["Home maker"], 
				"Date of Birth": ["1992-04-16"],
				"Place of Birth": ["City:Malkipuram"],
				"Description": "Bindu hails from a village near Antarvedi."
			}
		},
		{
			"l": "Allaka Padmavathi",
			"i_of": "Person",
			"props": {
				"Gender": ["Female"],
				"Spouse": ["Person:Sagar Kalepu"], 
				"Children": ["Person:Hasini Sruthi", "Person:Sai Navadeep"], 
				"Occupation": ["Home maker"], 
				"Date of Birth": ["1993-06-10"],
				"Place of Birth": ["City:Vetlapalem"],
				"Description": "Nothing much"
			}
		},
		{
			"l": "Pavan Veerendra",
			"i_of": "Person",
			"props": {
				"Gender": ["Male"],
				"Parents": ["Person:Satish Kalepu", "Person:Pichika Purna Bindu"],
				"Siblings": ["Person:Surya Siddharth", "Person:Hasini Sruthi", "Person:Sai Navadeep"],
				"Occupation": ["Student"], 
				"Date of Birth": ["2012-11-10"],
				"Place of Birth": ["City:Malkipuram"]
			}
		},
		{
			"l": "Surya Siddharth",
			"i_of": "Person",
			"props": {
				"Gender": ["Male"],
				"Parents": ["Person:Satish Kalepu", "Person:Pichika Purna Bindu"],
				"Siblings": ["Person:Pavan Veerendra", "Person:Hasini Sruthi", "Person:Sai Navadeep"],
				"Occupation": ["Student"], 
				"Date of Birth": ["2012-11-10"],
				"Place of Birth": ["City:Malkipuram"]
			}
		},
		{
			"l": "Hasini Sruthi",
			"i_of": "Person",
			"props": {
				"Gender": ["Female"],
				"Parents": ["Person:Sagar Kalepu", "Person:Allaka Padmavathi"],
				"Siblings": ["Person:Pavan Veerendra", "Person:Surya Siddharth", "Person:Sai Navadeep"],
				"Occupation": ["Student"], 
				"Date of Birth": ["2014-05-10"],
				"Place of Birth": ["City:Vetlapalem"]
			}
		},
		{
			"l": "Sai Navadeep",
			"i_of": "Person",
			"props": {
				"Gender": ["Male"],
				"Parents": ["Person:Sagar Kalepu", "Person:Allaka Padmavathi"],
				"Siblings": ["Person:Pavan Veerendra", "Person:Surya Siddharth", "Person:Hasini Sruthi"],
				"Occupation": ["Student"], 
				"Date of Birth": ["2017-09-05"],
				"Place of Birth": ["City:Vetlapalem"]
			}
		},
		{
			"l": "Veera Raghavulu",
			"i_of": "Person",
			"props": {
				"Gender": ["Male"],
				"Spouse": ["Person:Surya Kumari"], 
				"Children": ["Person:Satish Kalepu", "Person:Sagar Kalepu"],
				"Occupation": ["Watch Macanic"], 
				"Date of Birth": ["1960-01-01"],
				"Place of Birth": ["City:Bandarulanka"]
			}
		},
		{
			"l": "Surya Kumari",
			"i_of": "Person",
			"props": {
				"Gender": ["Female"],
				"Spouse": ["Person:Veera Raghavulu"], 
				"Children": ["Person:Satish Kalepu", "Person:Sagar Kalepu"],
				"Occupation": ["Home Maker"], 
				"Date of Birth": ["1955-01-01"],
				"Place of Birth": ["City:Razole"]
			}
		},
		{
			"l": "Chiranjeevi",
			"i_of": "Person",
			"props": {
				"Gender": ["Male"],
				"Children": ["Person:Ramcharan"],
				"Siblings": ["Person:Pavan Kalyan", "Person:Nagababu"],
				"Occupation": ["Actor", "Politician"], 
				"Positions Held": ["MP Loksabha", "Minister Tourism"] 
			}
		},
		{
			"l": "Ramcharan",
			"i_of": "Person",
			"props": {
				"Gender": ["Male"],
				"Parents": ["Person:Chiranjeevi", "Person:Sureka"],
				"Spouse": ["Person:Upasana"],
				"Siblings": ["Person:Allu Arjun"],
				"Occupation": ["Actor", "Producer"], 
				"Positions Held": ["MD AirIndia"] 
			}
		},
		{
			"l": "Pavan Kalyan",
			"i_of": "Person",
			"props": {
				"Gender": ["Male"],
				"Spouse": ["Person:Russian"],
				"Siblings": ["Person:Chiranjeevi", "Person:Nagababu"],
				"Occupation": ["Actor", "Producer", "Politician"], 
				"Positions Held": ["MLA Pitapuram", "Deputy CM Andhra Pradesh", "Minister PanchayatiRaj"]
			}
		}
	]',true);
	echo json_last_error();
	echo json_last_error_msg();

	//print_r( $objects);

	$object_ids = [];
	//echo "<pre>";
	foreach( $objects as $i=>$j ){

		echo "---Lable: " . $j['l'] . "\n";
		print_r( $j );

		$res = $mongodb_con->find_one($graph_things, ['l.v'=>$j['i_of']]);
		if( !$res['data'] ){
			echo "parent not found\n";
			$parent_id = uniqid();
			$object_ids[ $j['i_of'] ] = $parent_id;
			$parent_object = [
				'_id'=>$parent_id,
				'l'=>[
					'v'=>$j['i_of'],
					't'=>'T'
				],
				'i_of'=>[
					'i'=>"T1",
					'v'=>"Root",
					't'=>"GT",
				],
				'i_t'=>['t'=>"T", "v"=>"N"],
				'm_i'=>date("Y-m-d H:i:s"),'m_u'=>date("Y-m-d H:i:s"),
			];
			echo "Created: ".$parent_id."\n";
			$res2 = $mongodb_con->insert($graph_things, $parent_object);
			send_to_keywords_queue($parent_id);
			$parent_object['z_t'] = [];
			$parent_object['z_n'] = 1;
			$parent_object['z_o'] = [];
		}else{
			echo "parent found: " . $parent_id . "\n";
			$parent_id = $res['data']['_id'];
			$parent_object = $res['data'];
			$object_ids[ $j['i_of'] ] = $parent_id;
			$parent_object['z_t'] = $parent_object['z_t']??[];
			$parent_object['z_n'] = $parent_object['z_n']??1;
			$parent_object['z_o'] = $parent_object['z_o']??[];
		}

		$res = $mongodb_con->find_one($graph_things, ['l.v'=>$j['l']]);
		if( !$res['data'] ){
			echo "Label not found\n";
			$id = uniqid();
			$object_ids[ $j['l'] ] = $id;
			$res2 = $mongodb_con->insert($graph_things, [
				'_id'=>$id,
				'l'=>[
					'v'=>$j['l'],
					't'=>'T'
				],
				'i_of'=>[
					'i'=>$parent_id,
					'v'=>$j['i_of'],
					't'=>"GT",
				],
				'i_t'=>['t'=>"T", "v"=>"N"], 'm_i'=>date("Y-m-d H:i:s"),'m_u'=>date("Y-m-d H:i:s"),
			]);
			send_to_keywords_queue($id);
			echo "Label inserted: ".$id."\n";
			$mongodb_con->increment($graph_things, $parent_id, "cnt", 1);
			$object = [
				'_id'=>$id,
				'l'=>[
					'v'=>$j['l'],
					't'=>'T'
				],
				'i_of'=>[
					'i'=>$parent_id,
					'v'=>$j['i_of'],
					't'=>"GT",
				],
				'i_t'=>['t'=>"T", "v"=>"N"]
			];
		}else{
			$object = $res['data'];
			$id = $object['_id'];
		}
		echo "Label: ".$id.": " . $j['l'] . "\n";
		foreach( $j['props'] as $prop_name=>$prop_values ){
			//echo "Prop check: ".$prop_name."\n";
			//print_r( array_keys($parent_object['z_t']) );
				$f = false;
				$new_p = "";
				foreach( $parent_object['z_t'] as $prop=>$propd ){
					//echo "Prop check: ".$prop.":".$propd['l']['v']."==".$prop_name."\n";
					if( $propd['l']['v'] == $prop_name ){
						$new_p = $prop;
						$f = true;break;
						echo "Prop found: ".$prop."\n";
					}
				}
				if( !$f ){
					echo "Prop not found\n";
					$new_p = "p".$parent_object['z_n'];
					echo "Creating prop: " . $new_p . ": " . $prop_name. "\n";
					$parent_object['z_n']++;
					$parent_object['z_t'][ $new_p ] = [ "l"=> ["t"=> "T", "v"=> $prop_name], "t"=> ["t"=> "KV","v"=> "Text","k"=> "T"], "e"=> false, "m"=> false ];
					$parent_object['z_o'][] = $new_p;
					$res4 = $mongodb_con->update_one($graph_things, ['_id'=>$parent_id], [
						'z_t.'.$new_p=>$parent_object['z_t'][ $new_p ],
						'z_o'=>$parent_object['z_o'],
						'z_n'=>$parent_object['z_n'],
					]);
				}
				$v = [];
				foreach( $prop_values as $pi=>$pj ){
					$x = explode(":", $pj);
					if( sizeof($x) == 2 ){
						$pn = $x[0];
						if( $object_ids[ $pn ] ){
							$pid = $object_ids[ $pn ];
							$new_id = find_or_create($x[1], ['t'=>"GT", 'v'=>$pn, 'i'=>$pid]);
							$v[] = ['t'=>"GT", "i"=>$new_id, "v"=>$x[1]];
						}else{
							$v[] = ['t'=>"T", "v"=>$pj];
						}
					}else{
						$v[] = ['t'=>"T", "v"=>$pj];
					}
				}
				$res = $mongodb_con->update_one($graph_things, ['_id'=>$id], [
					'props.'.$new_p => $v
				]);
				//print_r( $res );
		}

		//echo "parent status\n";
		//print_r( $parent_object );
		// $res = $mongodb_con->find_one($graph_things, ['_id'=>$id]);
		// print_r( $res['data']['props'] );

	}

	$res = $mongodb_con->aggregate( $graph_things, [
		['$group'=>['_id'=>'$i_of.i', 'cnt'=>['$sum'=>1]]]
	]);

	print_r( $res );

	foreach( $res['data'] as $i=>$j ){
		$r = $mongodb_con->update_one( $graph_things, ["_id"=>$j['_id']], ["cnt"=>(int)$j['cnt']] );
		print_r( $r );
	}

}