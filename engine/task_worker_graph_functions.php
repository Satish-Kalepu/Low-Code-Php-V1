<?php

class graph_processor{
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
					$subperms = $this->find_permutations( implode(" ", $x) );
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
	function process_task( $task ){
		global $mongodb_con; global $db_prefix; global $graph_id;
		if( $task['data']['action'] == "thing_update" ){

			global $graph_things;
			global $graph_keywords;
			global $graph_queue;

			$res = $mongodb_con->find_one( $graph_things, ["_id"=>$task['data']['thing_id']] );
			if( !$res['data'] ){
				$res = $mongodb_con->delete_many( $graph_keywords, ['tid'=>$task['data']['thing_id']] );
				return ['statusCode'=>200, 'body'=>['status'=>"fail", "data"=>"node ". $task['data']['thing_id'] . " is not found", "res"=>$res ] ];
			}
			$thing = $res['data'];
			$res = $mongodb_con->delete_many( $graph_keywords, ['tid'=>$task['data']['thing_id']] );
			$permutations = $this->find_permutations($thing['l']['v']);
			$res2 = $mongodb_con->find( $graph_keywords );
			$ress = [];
			foreach( $permutations as $perm=>$j ){
				$d = [
					"p"=> $perm,
					"l"=> $thing['l']['v'],
					"tid"=>$thing['_id'],
					"pid"=> $thing['i_of']['i'],
					"pl"=> $thing['i_of']['v'],
					"t"=>"p"
				];
				if( $perm == $thing['l']['v'] ){
					$d['m'] = true;
				}
				$r = $mongodb_con->update_one( $graph_keywords, [
					"_id"=> $thing['i_of']['i'] . ":" . strtolower($perm)
				],$d, [
					'upsert'=>true,
				]);
				if( $r['status'] != "success" ){
					
				}
				$ress[] = $r;
			}
			if( isset($thing['al']) ){
				foreach( $thing['al'] as $i=>$j ){
					$r = $mongodb_con->update_one( $graph_keywords, [
						"_id"=> $thing['i_of']['i'] . ":" . strtolower($j['v'])
					],[
						"p"=> $j['v'],
						"l"=> $thing['l']['v'],
						"tid"=>$thing['_id'],
						"pid"=> $thing['i_of']['i'],
						"pl"=> $thing['i_of']['v'],
						"t"=>"a"
					], [
						'upsert'=>true,
					]);
					if( $r['status'] != "success" ){
						$ress[] = $r;
					}
				}
			}
			return ['statusCode'=>200, 'body'=>['status'=>"success", "data"=>["permutations"=>array_keys($permutations)], "res"=>$ress ] ];
		}else{
			return ['statusCode'=>500, 'body'=>['status'=>"fail", "data"=>"action not found" ] ];
		}
	}

}
