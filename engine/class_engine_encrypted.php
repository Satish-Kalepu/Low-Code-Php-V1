<?php

//phpinfo();exit;

class api_engine{
	public $s2_nnnnnnnnoc = false;
	public $s2_ggggggggol = [];
	public $s2_ggggol_bus = [];
	public $s2_tttttluser = [];
	public $s2_sgnittesegap = [];
	public $s2_ssssstupni = [];
	public $s2_sssstuptuo = [];
	public $s2_tttttneilc = "simulate";
	public $s2_eeeeenigne = [];
	public $s2_ssssnoitpo = [];
	public $s2_di_gol_tseuqer = "";
	public $s2_snoitcennoc_bd = [];
	public $s2_noitareti_sqs_tnerruc = 0;
	public $s2_egats_tnerruc  = 0;
	public $s2_noitucexe_dnev = false;
	public $s2_nnngissaer = false;
	public $s2_lmth_tuptuo = "";
	public $s2_rrrrrrorre = "";
	public $task_insert_id = 1000;
	public $s2_sssssspmuj = 0;
	public $s2_egats_ot_pmuj = "";
	public $s2_dddddi_ppa = "";
	public $s2_ddddi_resu = "";
	public $s2_sssssutats = "";
	public $s2_vvvv_dnarv = [];
	public $s2_ssmeti_pxe = [];
	public $s2_ssssslebal = [];
	public $s2_level_evisrucer = 1;
	public $s2_eeesnopser = [
		"statusCode"=>200,
		"headers"=>[
			"content-type"=>"application/json"
		],
		"body"=>["status"=>"success"],
		"pretty"=>false,
	];
	public $s2_xxiferp_bd = "apimaker";

	function __construct(){
		global $mongodb_con;
		$this->s2_nnnnnnnnoc = $mongodb_con;
		if( !$mongodb_con ){
			echo "APP Engine: DB Connection Error!";
			exit;
		}
	}
	function isBinary( $str ){
		//preg_match_all('~[^\x20-\x7E\t\r\n]~', $str,$m);
		// echo $str . "\n";
		//echo strlen($str) . ": " . mb_detect_encoding($str) . "\n";
		if( mb_detect_encoding($str) == "" ){ return true; }
		if( mb_detect_encoding($str) == "UTF-8" ){ return false; }
		if( mb_detect_encoding($str) == "ASCII" ){ 
			return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
			//return true; 
		}
	}
	function getlog(){
		return $this->s2_ggggggggol;
	}
	function execute( $s2_eeeeenigne, $s2_stupni_tset, $s2_ssssnoitpo=[] ){
		global $config_global_engine;
		$this->s2_xxiferp_bd = $config_global_engine['config_mongo_prefix'];
		$this->s2_ggggggggol = [];
		$this->s2_tttttluser = [];
		$this->s2_ssssstupni = [];
		$this->s2_sssstuptuo = [];
		$this->s2_lmth_tuptuo = "";
		$this->s2_tttttneilc = "Simulate";
		$this->s2_sssssutats = "success";
		$this->s2_rrrrrrorre = "";
		$this->s2_di_gol_tseuqer = "";
		$this->s2_sssssspmuj = 0;
		$this->s2_egats_ot_pmuj = "";
		$this->s2_egats_tnerruc  = 0;
		$this->s2_noitareti_sqs_tnerruc = 0;
		$this->s2_nnngissaer = false;
		$this->s2_ssssnoitpo = $s2_ssssnoitpo;
		if( $this->s2_nnnnnnnnoc == false ){
			return $this->s2_rorre_dnopser("Error with Database Connection!");
		}
		$this->s2_dddddi_ppa = $s2_eeeeenigne['app_id'];
		$this->s2_ddddi_resu = $s2_eeeeenigne['user_id'];
		$this->s2_eeeeenigne = $s2_eeeeenigne;
		
		$this->s2_eeesnopser['headers']['content-type'] = $s2_eeeeenigne['output-type'];
		$this->s2_ssssstupni = $this->s2_eeeeenigne["engine"]["input_factors"];
		$this->s2_noitucexe_dnev = false;
		
		if( $s2_ssssnoitpo["request_log_id"] ){
	        	$this->s2_di_gol_tseuqer = $s2_ssssnoitpo['request_log_id'];
		}
		if( $s2_ssssnoitpo["recursive_level"] ){
	        	$this->s2_level_evisrucer = $s2_ssssnoitpo['recursive_level'];
		}
		if( $s2_ssssnoitpo["result"] ){
	        	$this->s2_tttttluser = $s2_ssssnoitpo['result'];
		}
		if( $s2_ssssnoitpo["inputs"] ){
	        	$this->s2_ssssstupni = $s2_ssssnoitpo['inputs'];
		}
		if( $s2_ssssnoitpo["outputs"] ){
	        	$this->s2_sssstuptuo = $s2_ssssnoitpo['outputs'];
		}
		if( $s2_ssssnoitpo["log"] ){
	        	$this->s2_ggggggggol = $s2_ssssnoitpo['log'];
		}
		$this->s2_ggggggggol[] = "Testing Started: " . date("Y-m-d H:i:s");
		if( $this->s2_ssssnoitpo['raw_output'] ){
			if( isset($s2_stupni_tset['t'])&&isset($s2_stupni_tset['v'])&&$s2_stupni_tset['t']=='O' ){
				$s2_stupni_tset = $s2_stupni_tset['v'];
			}
		}else if( isset($s2_stupni_tset['t'])&&isset($s2_stupni_tset['v'])&&$s2_stupni_tset['t']=='O'){
			$s2_stupni_tset = $s2_stupni_tset['v'];
		}else{
			if( $s2_stupni_tset != null && gettype($s2_stupni_tset) == "array" ){
				$this->s2_tcejbo_ot_tupni($s2_stupni_tset);
			}
		}
		foreach( $s2_stupni_tset as $inputi=>$inputv ){if($inputi){
			$this->s2_tttttluser[ $inputi ] = $inputv;
		}}
		//print_pre( $s2_stupni_tset );
		//exit;
		//print_json( $s2_eeeeenigne['engine']['input_factors'] );exit;
		//print_json( $this->s2_tttttluser );exit;
		foreach( $s2_eeeeenigne['engine']['input_factors'] as $i=>$j ){
			if( gettype($j['m']) =="string" ){
				if( $j['m'] === "true"  ){ $j['m'] = true;}
				if( $j['m'] === "false" ){ $j['m'] = false;}
			}
			//echo $j['m'] ; 
			if( $j['m'] && !isset($this->s2_tttttluser[ $i ])  ){
				return $this->s2_rorre_dnopser("Input: " . $i . " required");
			}else if( $j['m'] && $this->s2_tttttluser[ $i ]['v'] == "" ){
				return $this->s2_rorre_dnopser("Input: " . $i . " required");
			}else if( isset($this->s2_tttttluser[ $i ]) ){
				if( $j['t'] =="N" ){
					$this->s2_tttttluser[ $i ]['v'] = $this->s2_rebmun_ot_gnirts( $this->s2_tttttluser[ $i ]['v'] );
				}
			}
		}
		//print_json( $this->s2_tttttluser );exit;
		//exit;
		//$e = false;
		//$this->s2_ggggggggol[] = $this->s2_tttttluser;
		$s2_egats_gnitrats = 0;
		if( $s2_ssssnoitpo["current_sqs_iteration"] ){
			$this->s2_ggggggggol[] = "SQS Iteration: " . $s2_ssssnoitpo['current_sqs_iteration'];
			$this->s2_noitareti_sqs_tnerruc = $s2_ssssnoitpo['current_sqs_iteration'];
		}
		if( $s2_ssssnoitpo["task_queue_url"] ){
			$this->task_queue_url = $s2_ssssnoitpo['task_queue_url'];
		}
		if( $s2_ssssnoitpo["start_from_stage"] ){
			$this->s2_ggggggggol[] = "Start from Stage: " . ($s2_ssssnoitpo["start_from_stage"]+1);
			$s2_egats_gnitrats = $s2_ssssnoitpo["start_from_stage"];
		}
		$s2_oooot_pmuj = 0;
		$s2_ttnc_poolf = 0;
		$s2_spool_xamf = 5000;
		$s2_verp_iegatsf = 0;
		for( $s2_iiiiegatsf=0; $s2_iiiiegatsf<sizeof($this->s2_eeeeenigne["engine"]['stages']); $s2_iiiiegatsf++ ){
			$s2_ttnc_poolf++;
			if( $s2_ttnc_poolf >= $s2_spool_xamf ){
				$this->response['statusCode'] = 500;
				$this->response['body'] = "Maximum Steps Reached: " . $s2_ttnc_poolf;
				$this->s2_ggggggggol[] = "Maximum Steps Reached: " . $s2_ttnc_poolf;
				break;
			}
			$next_fstaged = $this->s2_eeeeenigne["engine"]['stages'][$s2_iiiiegatsf+1];
			if( $next_fstaged['type'] == "ForEach" ){
				unset($this->s2_eeeeenigne["engine"]['stages'][$s2_iiiiegatsf+1]['keys']);
				unset($this->s2_eeeeenigne["engine"]['stages'][$s2_iiiiegatsf+1]['keyi']);
			}
			$s2_ddddegatsf = $this->s2_eeeeenigne["engine"]['stages'][$s2_iiiiegatsf]; 
			if( $this->s2_noitucexe_dnev ){
				$this->s2_ggggggggol[] = "End of Execution";
				break;
			}
			if( isset($s2_ddddegatsf['a']) ){
				if( $s2_ddddegatsf['a']===true ){
					if( $s2_ddddegatsf['k']['t'] == "c" ){
						$this->s2_ggggggggol[] = $s2_ttnc_poolf . ": " . ($s2_iiiiegatsf+1) . ": " . $s2_ddddegatsf['k']['v'] . ": " . "Skipped";
					}else{
						$this->s2_ggggggggol[] = $s2_ttnc_poolf . ": " . ($s2_iiiiegatsf+1) . ": " . $s2_ddddegatsf['k']['t'] . ":" . $s2_ddddegatsf['k']['v'] . ": ". "Skipped";
					}
					continue;
				}
			}
			if( $s2_ddddegatsf['k']['t'] == "c" ){
				$d = $s2_ttnc_poolf . ": " . ($s2_iiiiegatsf+1) . ": " . $s2_ddddegatsf['k']['v'];
				if( $s2_ddddegatsf['k']['v'] == "Let" ){
					if( $s2_ddddegatsf['d']['rhs']['t'] == "V" ){
						$d .= ": ". $s2_ddddegatsf['d']['lhs'] . "= " . $s2_ddddegatsf['d']['rhs']['t']. ":" .$s2_ddddegatsf['d']['rhs']['v']['v'] . ":" . $s2_ddddegatsf['d']['rhs']['v']['t'];
					}else{
						$d .= ": ". $s2_ddddegatsf['d']['lhs'] . "= " . $s2_ddddegatsf['d']['rhs']['t']. ":" .$s2_ddddegatsf['d']['rhs']['v'];
					}
					if( isset($s2_ddddegatsf['d']['rhs']['v']['vs']) ){
						if( $s2_ddddegatsf['d']['rhs']['v']['vs']['v'] ){
							$d .= "->". $s2_ddddegatsf['d']['rhs']['v']['vs']['v'];
						}
					}
				}
			}else{
				$d = $s2_ttnc_poolf . ": " . ($s2_iiiiegatsf+1) . ": " . $s2_ddddegatsf['k']['t'] . ":" . $s2_ddddegatsf['k']['v'];
				if( $s2_ddddegatsf['k']['vs']['v'] ){
					$d .= "->" . $s2_ddddegatsf['k']['vs']['v'];
				}
			}
			$this->s2_ggggggggol[] = $d;

			//$this->s2_ggggggggol[] = $this->s2_tttttluser;
			//print_pre( $s2_ddddegatsf );

			if( $s2_ddddegatsf['k']['t'] == "c" ){
				if( $s2_ddddegatsf['k']['v'] == "Let" ){
					$this->s2_ggggggggol[] = $s2_ddddegatsf['d']['lhs'] . " = " . $s2_ddddegatsf['d']['rhs']['t'] . ":" . $s2_ddddegatsf['d']['rhs']['v'];
					$s2_sssssssshl = $s2_ddddegatsf['d']['lhs'];
					$s2_sssssssshr = $s2_ddddegatsf['d']['rhs'];
					if( preg_match("/\W/", $s2_sssssssshl ) ){
						return $this->s2_rorre_dnopser("Line: ".$s2_iiiiegatsf . ": Let variable name should not contain special chars");
					}
					if( $s2_sssssssshr['t'] == "Function" ){
						$val = $this->s2_noitcnuf_od( $s2_ddddegatsf['d']['rhs']['v'] );
						if( $s2_ddddegatsf['d']['rhs']['v']['return'] ){
							$this->s2_tttttluser[ $s2_ddddegatsf['d']['lhs'] ] = $val;
						}
					}else if( $s2_sssssssshr['t'] == "V" ){
						$v = $this->s2_eeulav_teg($s2_sssssssshr);
						//print_pre( $v );exit;
						//$this->s2_ggggggggol[] = $v;
						$this->s2_tluser_tes( $s2_sssssssshl, $v );
					}else{
						$this->s2_tttttluser[ $s2_sssssssshl ] =$s2_sssssssshr;
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "Assign" ){
					$var = $s2_ddddegatsf['d']['lhs']['v']['v'];
					$s2_sssssssshl = $this->s2_eeulav_teg($s2_ddddegatsf['d']['lhs']);
					if( $s2_ddddegatsf['d']['rhs']['t'] == "Function" ){
						$val = $this->s2_noitcnuf_od( $s2_ddddegatsf['d']['rhs']['v'] );
						if( $s2_ddddegatsf['d']['rhs']['v']['return'] ){
							$s2_sssssssshr = $val;
						}
					}else{
						$s2_sssssssshr = $this->s2_eeulav_teg($s2_ddddegatsf['d']['rhs']);
					}
					if( $s2_sssssssshl['t'] != $s2_sssssssshr['t'] ){
						$this->s2_ggggggggol[] = $s2_sssssssshr['v'];
						$this->s2_ggggggggol[] = "Warning: Assign: ". $s2_sssssssshl['t'] . ":" . $var . " = " . $s2_sssssssshr['t']. ":";
						$this->s2_ggggggggol[] = $s2_sssssssshr['v'];
					}
					$this->s2_tluser_tes( $var, $s2_sssssssshr );
				}
				if( $s2_ddddegatsf['k']['v'] == "Expression_Unwanted" ){
					$this->s2_ssmeti_pxe = [];
					$exp = "(" . $s2_ddddegatsf['d']['rhs']['v'] . ")";
					$exp = preg_replace_callback("/[\(\)\+\-\/\%\*]/", function($m){
						$this->s2_ssmeti_pxe[] = $m[0];
						return "|e|";
					},$exp);
					$x = explode("|e|", $exp);
					$exp2 = "";
					foreach( $x as $i=>$j ){
						if( trim($j) ){
							if( is_numeric(trim($j) ) ){}else{
								$kv = $this->s2_eeulav_teg(trim($j));
								if( $kv['t'] == "N" || is_numeric($kv['v']) ){
									$x[$i] = $this->s2_rebmun_ot_gnirts( $kv['v'] );
								}else{
									$this->s2_ggggggggol[] = "Error: Expresssion Variable is not Numeric: " . $j . " : " . $kv['t'];
									$x[$i] = "0";
								}
							}
						}
						$exp2 .= $x[$i] . $this->s2_ssmeti_pxe[$i];
					}
					$this->s2_ggggggggol[] = $exp2;
					//echo $exp2;exit;
					$exp2= '$vv='.$exp2. ";";
					try{
						eval($exp2);
					}catch(Exception $ex){
						$this->s2_ggggggggol[] = "Expression error: " . $ex->getMessage();
						$vv = 0;
					}
					$this->s2_tluser_tes( $s2_ddddegatsf['d']['lhs'], ['t'=>"N","v"=>$vv] );
				}
				if( $s2_ddddegatsf['k']['v'] == "Expression" ){
					$this->s2_ssmeti_pxe = [];
					$this->s2_ggggggggol[] = $s2_ddddegatsf['d']['rhs']['v'];
					$exp = "(" . $s2_ddddegatsf['d']['rhs']['v'] . ")";
					$exp = preg_replace_callback("/\[([a-z][a-z0-9\-\>\_\ ]*)\]/", function($m){
						$this->s2_ssmeti_pxe[] = $m[1];
						return "|e|";
					},$exp);
					$x = explode("|e|", $exp);
					$exp2 = "";
					foreach( $x as $i=>$j ){
						if( $i < sizeof($this->s2_ssmeti_pxe) ){
							$kv = $this->s2_eeulav_teg( trim($this->s2_ssmeti_pxe[$i]) );
							if( $kv['t'] == "N" || is_numeric($kv['v']) ){
								$kv['v'] = $this->s2_rebmun_ot_gnirts( $kv['v'] );
							}else{
								$this->s2_ggggggggol[] = "Error: Expresssion Variable is not Numeric: " . $this->s2_ssmeti_pxe[$i] . " : " . $kv['t'];
								$x[$i] = "0";
							}
							$exp2 .= $j . $kv['v'];
						}else{
							$exp2 .= $j;
						}
					}
					$this->s2_ggggggggol[] = $exp2;
					//echo $exp2;exit;
					$exp2= '$vv='.$exp2. ";";
					try{
						eval($exp2);
					}catch(Exception $ex){
						$this->s2_ggggggggol[] = "Expression error: " . $ex->getMessage();
						$vv = 0;
					}
					$this->s2_tluser_tes( $s2_ddddegatsf['d']['lhs'], ['t'=>"N","v"=>$vv] );
				}
				if( $s2_ddddegatsf['k']['v'] == "Math" ){
					$s2________shlv = $this->s2_eeulav_teg($s2_ddddegatsf['d']['lhs']);
					if( $s2________shlv['t'] != "N" ){
						$this->s2_ggggggggol[] = "Warning: Math: lhs: not numeric";
					}
					$s2_sssssssshr = $s2_ddddegatsf['d']['rhs'];
					$s2_sssssssser = $this->s2_hhhhtam_od( $s2_sssssssshr );
					$this->s2_tluser_tes( $s2_ddddegatsf['d']['lhs'], ["t"=>"N", "v"=>$s2_sssssssser] );
				}
				if( $s2_ddddegatsf['k']['v'] == "If" ){
					$s2_kkkkkodnoc = true;
					foreach( $s2_ddddegatsf['d']['cond'] as $ci=>$cd ){
						$s2_sssssssshl = $this->s2_eeulav_teg($cd['lhs']);
						$s2_sssssssshr = $this->s2_eeulav_teg($cd['rhs']);
						$this->s2_ggggggggol[] = "If " . $s2_sssssssshl['t'].":".$s2_sssssssshl['v'] . " " . $cd['op'] . " " . $s2_sssssssshl['t'].":".$s2_sssssssshr['v'];
						if( $cd['op'] == "==" ){
							if( $s2_sssssssshl['t'] == $s2_sssssssshr['t'] ){
								if( $s2_sssssssshl['v'] == $s2_sssssssshr['v'] ){}else{$s2_kkkkkodnoc = false;break; }
							}else{
								$s2_kkkkkodnoc = false;break;
							}
						}else if( $cd['op'] == "!=" ){
							if( $s2_sssssssshl['t'] == $s2_sssssssshr['t'] ){
								if( $s2_sssssssshl['v'] != $s2_sssssssshr['v'] ){}else{$s2_kkkkkodnoc = false;break; }
							}else{
								$s2_kkkkkodnoc = false;break;
							}
						}else if( $cd['op'] == "<" ){
							if( $s2_sssssssshl['t'] == $s2_sssssssshr['t'] ){
								if( $s2_sssssssshl['v'] < $s2_sssssssshr['v'] ){}else{$s2_kkkkkodnoc = false;break; }
							}else{
								$s2_kkkkkodnoc = false;break;
							}
						}else if( $cd['op'] == "<=" ){
							if( $s2_sssssssshl['t'] == $s2_sssssssshr['t'] ){
								if( $s2_sssssssshl['v'] <= $s2_sssssssshr['v'] ){}else{$s2_kkkkkodnoc = false;break; }
							}else{
								$s2_kkkkkodnoc = false;break;
							}
						}else if( $cd['op'] == ">" ){
							if( $s2_sssssssshl['t'] == $s2_sssssssshr['t'] ){
								if( $s2_sssssssshl['v'] > $s2_sssssssshr['v'] ){}else{$s2_kkkkkodnoc = false;break; }
							}else{
								$s2_kkkkkodnoc = false;break;
							}
						}else if( $cd['op'] == ">=" ){
							if( $s2_sssssssshl['t'] == $s2_sssssssshr['t'] ){
								if( $s2_sssssssshl['v'] >= $s2_sssssssshr['v'] ){}else{$s2_kkkkkodnoc = false;break; }
							}else{
								$s2_kkkkkodnoc = false;break;
							}
						}
					}
					if( $s2_kkkkkodnoc ){
						//$this->s2_ggggggggol[] = "If matched";
					}else{
						$s2_iiiiegatsf = $this->s2_dnar_txen_dnif( $s2_iiiiegatsf );
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "For" ){
					$a = false;
					$vrand = $s2_ddddegatsf['vrand'];
					if( isset($this->s2_vvvv_dnarv[ $vrand ]) ){
						$a = $this->s2_vvvv_dnarv[ $vrand ]['a'];
					}
					if( !$a ){
						$this->s2_vvvv_dnarv[ $vrand ] = [
							"s" => $this->s2_eeulav_teg($s2_ddddegatsf['d']['start'])['v'],
							"e" => $this->s2_eeulav_teg($s2_ddddegatsf['d']['end'])['v'],
							"o" => $s2_ddddegatsf['d']['order'],
							"m" => $this->s2_eeulav_teg($s2_ddddegatsf['d']['modifier'])['v'],
							"mx" => (int)$s2_ddddegatsf['d']['maxloops'],
							"as" => $s2_ddddegatsf['d']['as'],
							"a"=>true,
							"c"=>0
						];
						$this->s2_ggggggggol[] = "Start: " . $this->s2_vvvv_dnarv[ $vrand ]['s'] . ", End: " . $this->s2_vvvv_dnarv[ $vrand ]['e'] . ", o: " . $this->s2_vvvv_dnarv[ $vrand ]['o']  . ", mx: ". $this->s2_vvvv_dnarv[ $vrand ]['mx'] . ", as: ". $this->s2_vvvv_dnarv[ $vrand ]['as'];
						$this->s2_tttttluser[ $this->s2_vvvv_dnarv[ $vrand ]['as'] ] = ["t"=>"N", "v"=>$this->s2_vvvv_dnarv[ $vrand ]['s'] ];
					}
					//print_pre( $this->s2_tttttluser );
					$c = $this->s2_vvvv_dnarv[ $vrand ]['c']++;
					$o = $this->s2_vvvv_dnarv[ $vrand ]['o'];
					$mx = $this->s2_vvvv_dnarv[ $vrand ]['mx'];
					// $this->s2_eeeeenigne["engine"]['stages'][ $s2_iiiiegatsf ][ "start" ] = $start;
					// $this->s2_eeeeenigne["engine"]['stages'][ $s2_iiiiegatsf ][ "end" ] = $end;
					$x = $this->s2_tttttluser[ $this->s2_vvvv_dnarv[ $vrand ]['as'] ]['v'];
					$e = $this->s2_vvvv_dnarv[ $vrand ]['e'];
					$f = false;
					if( $o == "a-z" ){
						$this->s2_ggggggggol[] = "For: ". $x . " <= " . $e . " && " . $c . " < " . $mx;
						if( $x <= $e && $c < $mx ){$f = true;}
					}else{
						$this->s2_ggggggggol[] = "For: ". $x . " >= " . $e . " && " . $c . " < " . $mx;
						if( $x >= $e && $c < $mx ){$f = true;}
					}
					if( $f ){
						// process loop
					}else{
						$this->s2_vvvv_dnarv[ $vrand ]['a'] = false;
						$s2_iiiiegatsf = $this->s2_dnar_txen_dnif( $s2_iiiiegatsf );
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "EndFor" ){
					$vrand = $s2_ddddegatsf['vrand'];
					$as = $this->s2_vvvv_dnarv[ $vrand ]['as'];
					$o = $this->s2_vvvv_dnarv[ $vrand ]['o'];
					$m = $this->s2_vvvv_dnarv[ $vrand ]['m'];
					if( $o == "a-z" ){
						$this->s2_tttttluser[ $as ]['v']+=$m;
					}else{
						$this->s2_tttttluser[ $as ]['v']-=$m;
					}
					if( $this->s2_vvvv_dnarv[ $vrand ]['c'] > $this->s2_vvvv_dnarv[ $vrand ]['mx'] ){
						$this->s2_ggggggggol[] = "For crossed maximum iterations!";
					}else{
						$s2_iiiiegatsf = $this->s2_dnar_verp_dnif( $s2_iiiiegatsf );
						$s2_iiiiegatsf--;
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "ForEach" ){
					$a = false;
					$vrand = $s2_ddddegatsf['vrand'];
					if( isset($this->s2_vvvv_dnarv[ $vrand ]) ){
						$a = $this->s2_vvvv_dnarv[ $vrand ]['a'];
					}
					$v = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['var'] );
					if( $v['t']!="O" && $v['t']!="L" ){
						$this->s2_ggggggggol[] = "Error: ForEach Expects a List";
						return $this->s2_rorre_dnopser("Incorrect variable for ForEach");
					}
					if( !$a ){
						if( $v['t'] != "O" && $v['t'] != "L" ){
							return $this->s2_rorre_dnopser("Incorrect variable ". $v['t'] ." for ForEach");
						}
						$this->s2_vvvv_dnarv[ $vrand ] = [
							"var"=>$s2_ddddegatsf['d']['var']['v'],
							"keys"=>array_keys($v['v']),
							"k" => $s2_ddddegatsf['d']['key'],
							"v" => $s2_ddddegatsf['d']['value'],
							"a"=>true,
						];
					}
					if( sizeof( $this->s2_vvvv_dnarv[ $vrand ]['keys'] ) ){
						$k1 = array_splice($this->s2_vvvv_dnarv[ $vrand ]['keys'],0,1)[0];
						//echo $k1 . "--";
						$this->s2_tttttluser[ $this->s2_vvvv_dnarv[ $vrand ]['k'] ] = ["t"=>"T", "v"=>$k1 ];
						$this->s2_tttttluser[ $this->s2_vvvv_dnarv[ $vrand ]['v'] ] = $v['v'][ $k1 ];
						$this->s2_ggggggggol[] = $k1;
					}else{
						$this->s2_vvvv_dnarv[ $vrand ]['a'] = false;
						$s2_iiiiegatsf = $this->s2_dnar_txen_dnif( $s2_iiiiegatsf );
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "EndForEach" ){
					$vrand = $s2_ddddegatsf['vrand'];
					$s2_iiiiegatsf = $this->s2_dnar_verp_dnif( $s2_iiiiegatsf );
					$s2_iiiiegatsf--;
				}
				if( $s2_ddddegatsf['k']['v'] == "While" ){
					$a = false;
					$vrand = $s2_ddddegatsf['vrand'];
					if( isset($this->s2_vvvv_dnarv[ $vrand ]) ){
						$a = $this->s2_vvvv_dnarv[ $vrand ]['a'];
					}
					if( !$a ){
						$this->s2_vvvv_dnarv[ $vrand ] = [
							"mx" => (int)$s2_ddddegatsf['d']['maxloops'],
							"a"=>true,
							"c"=>0
						];
					}
					//print_pre( $this->s2_tttttluser );
					$c = $this->s2_vvvv_dnarv[ $vrand ]['c']++;
					$mx = $this->s2_vvvv_dnarv[ $vrand ]['mx'];
					$f = true;
					foreach( $s2_ddddegatsf['d']['cond'] as $ci=>$cd ){
						$s2_sssssssshl = $this->s2_eeulav_teg( $cd['lhs'] );
						$s2_sssssssshr = $this->s2_eeulav_teg( $cd['rhs'] );
						$op = $cd['op'];
						if( $s2_sssssssshl['t'] != $s2_sssssssshr['t'] ){
							$this->s2_ggggggggol[] = "Error: while condition: data type mismatch: " . $s2_sssssssshl['t'] . ":" . $s2_sssssssshl['v'] . " to " . $s2_sssssssshr['t'] . ":" . $s2_sssssssshr['v'];
						}
						if( $op == "==" ){
							$this->s2_ggggggggol[] = $s2_sssssssshl['v'] . " == " . $s2_sssssssshr['v'];
							if( $s2_sssssssshl['v'] == $s2_sssssssshr['v'] ){}else{$f = false;}
						}else if( $op == "!=" ){
							$this->s2_ggggggggol[] = $s2_sssssssshl['v'] . " != " . $s2_sssssssshr['v'];
							if( $s2_sssssssshl['v'] != $s2_sssssssshr['v'] ){}else{$f = false;}
						}else if( $op == "<" ){
							$this->s2_ggggggggol[] = $s2_sssssssshl['v'] . " < " . $s2_sssssssshr['v'];
							if( $s2_sssssssshl['v'] < $s2_sssssssshr['v']  ){}else{$f = false;}
						}else if( $op == "<=" ){
							$this->s2_ggggggggol[] = $s2_sssssssshl['v'] . " <= " . $s2_sssssssshr['v'];
							if( $s2_sssssssshl['v'] <= $s2_sssssssshr['v'] ){}else{$f = false;}
						}else if( $op == ">" ){
							$this->s2_ggggggggol[] = $s2_sssssssshl['v'] . " > " . $s2_sssssssshr['v'];
							if( $s2_sssssssshl['v'] > $s2_sssssssshr['v']  ){}else{$f = false;}
						}else if( $op == ">=" ){
							$this->s2_ggggggggol[] = $s2_sssssssshl['v'] . " >= " . $s2_sssssssshr['v'];
							if( $s2_sssssssshl['v'] >= $s2_sssssssshr['v'] ){}else{$f = false;}
						}else{
							$this->s2_ggggggggol[] = $op . " not implemented";
							$f = false;
						}
					}
					if( $f ){
						// process loop
					}else{
						$this->s2_vvvv_dnarv[ $vrand ]['a'] = false;
						$s2_iiiiegatsf = $this->s2_dnar_txen_dnif( $s2_iiiiegatsf );
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "EndWhile" ){
					$vrand = $s2_ddddegatsf['vrand'];
					if( $this->s2_vvvv_dnarv[ $vrand ]['c'] >= $this->s2_vvvv_dnarv[ $vrand ]['mx'] ){
						$this->s2_ggggggggol[] = "For crossed maximum iterations!";
					}else{
						$s2_iiiiegatsf = $this->s2_dnar_verp_dnif( $s2_iiiiegatsf );
						$s2_iiiiegatsf--;
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "BreakLoop" ){
					for($s2_222iegatsf=$s2_iiiiegatsf+1;$s2_222iegatsf<sizeof($this->s2_eeeeenigne["engine"]['stages']);$s2_222iegatsf++){
						$ld = $this->s2_eeeeenigne["engine"]['stages'][$s2_222iegatsf];
						if( $ld['k']['v'] == "EndWhile" || $ld['k']['v'] == "EndForEach" || $ld['k']['v'] == "EndFor" ){
							//$vrand = $ld['vrand'];
							$s2_iiiiegatsf = $s2_222iegatsf;
						}
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "NextLoop" ){
					for($s2_222iegatsf=$s2_iiiiegatsf+1;$s2_222iegatsf<sizeof($this->s2_eeeeenigne["engine"]['stages']);$s2_222iegatsf++){
						$ld = $this->s2_eeeeenigne["engine"]['stages'][$s2_222iegatsf];
						if( $ld['k']['v'] == "EndWhile" || $ld['k']['v'] == "EndForEach" || $ld['k']['v'] == "EndFor" ){
							//$vrand = $ld['vrand'];
							$s2_iiiiegatsf = $s2_222iegatsf-1;
						}
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "SetLabel" ){
					$this->s2_ssssslebal[ $s2_ddddegatsf['d']['v'] ] = $s2_iiiiegatsf;
				}
				if( $s2_ddddegatsf['k']['v'] == "JumpToLabel" ){
					if( isset($this->s2_ssssslebal[ $s2_ddddegatsf['d']['v'] ]) ){
						$s2_iiiiegatsf = $this->s2_ssssslebal[ $s2_ddddegatsf['d']['v'] ];
					}else{
						$this->s2_ggggggggol[] = "Label not found!";
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "CustomSDK" ){
					$sdk_id = $s2_ddddegatsf['d']['data']['sdk']['i']['v'];
					$sdk = $s2_ddddegatsf['d']['data']['sdk']['l']['v'];
					$method = $s2_ddddegatsf['d']['data']['method']['v'];
					$inputs = $this->s2_erup_yarra_ot_etalpmet( $s2_ddddegatsf['d']['data']['inputs']['v'] );
					//$this->s2_noitucexe_dne(200,$s2_ddddegatsf['d']['data']['inputs']['v']);
					$output = $s2_ddddegatsf['d']['data']['output']['v'];
					$this->s2_ggggggggol[] = $sdk_id . ":" . $sdk . ":" . $method;
					$this->s2_ggggggggol[] = $inputs;
					$sdk_res = $this->s2_nnnnnnnnoc->find_one( $this->s2_xxiferp_bd . "_sdks_versions", ["_id"=>$sdk_id], ['projection'=>['body'=>1]] );
					if( !$sdk_res['data'] ){
						$this->s2_noitucexe_dne(500,['status'=>"fail", "error"=>"SDK not found"]);break;
					}
					if( !$sdk_res['data']['body'] ){
						$this->s2_noitucexe_dne(500,['status'=>"fail", "error"=>"SDK body not found"]);break;
					}
					$b = base64_decode($sdk_res['data']['body']);
					if( trim($b) == "" ){
						$this->s2_noitucexe_dne(500,['status'=>"fail", "error"=>"SDK body decode failed"]);break;
					}
					$b = str_replace('<'.'?'.'php', '',$b);
					$b = str_replace('?'.'>', '',$b);
					$classname = "sdk_".$sdk_id;
					$b = preg_replace('/ClassName/', $classname, $b);
					//$this->s2_noitucexe_dne(200, $b );break;
					eval($b);
					if( !class_exists($classname) ){
						$this->s2_noitucexe_dne(500,['status'=>"fail", "error"=>"SDK failed to initialize"]);break;
					}
					try{
						$temp_sdk = new $classname();
						$v = get_class_methods($temp_sdk);
					}catch(Exception $ex){
						$this->s2_noitucexe_dne(500,['status'=>"fail", "error"=>"SDK failed to initialize ". $ex->getMessage()]);break;
					}

					$res = $temp_sdk->$method($inputs);
					$this->s2_tcejbo_ot_tupni($res);

					$this->s2_tluser_tes( $output, ["t"=>"O", "v"=>$res ] );
				}
				if( $s2_ddddegatsf['k']['v'] == "PushToQueue" ){
					$val = $this->s2_eueuq_ot_hsup( $s2_ddddegatsf );
				}
				if( $s2_ddddegatsf['k']['v'] == "VerifyCaptcha" ){
					//$this->s2_eueuq_ot_hsup( $s2_ddddegatsf );
					$code = $this->s2_eulav_erup_teg( $s2_ddddegatsf['d']['code'] );
					$captcha = $this->s2_eulav_erup_teg( $s2_ddddegatsf['d']['captcha'] );
					$output = $s2_ddddegatsf['d']['output']['v'];
					$res = $this->s2_nnnnnnnnoc->find_one( $this->s2_xxiferp_bd . "_captcha", ["_id"=>$code]);
					$verror = "";
					$vstatus = "success";
					if( $res['data'] ){
						if( $res['data']['c'] == $captcha ){
							$this->s2_nnnnnnnnoc->delete_one( $this->s2_xxiferp_bd . "_captcha", ["_id"=>$code]);
							$vstatus = "success";
						}else{
							$vstatus = "fail";
							$verror = "Captcha does't match";
						}
					}else{
						$vstatus = "fail";
						$verror = "Captcha not found";
					}
					$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
						'status'=>['t'=>"T","v"=>$vstatus],
						"error"=>['t'=>"T","v"=>$verror]
					]] );
				}
				if( $s2_ddddegatsf['k']['v'] == "Respond" ){
					//print_pre( $this->s2_tttttluser );
					$this->s2_ggggggggol[] = "Respond";
					if( $s2_ddddegatsf['d']['t'] == "O" ){
						$this->s2_eeesnopser['body'] =$this->s2_yarra_ot_etalpmet( $s2_ddddegatsf['d']['v'] );
						$this->s2_noitucexe_dnev = true;
						return $this->s2_eeesnopser;
					}else{
						$this->s2_ggggggggol[] = "Respond: " . $s2_ddddegatsf['d']['t'];
						$this->s2_eeesnopser['body'] =["status"=>"fail", "error"=>"Unhandled retrun type"];
						$this->s2_noitucexe_dnev = true;
						return $this->s2_eeesnopser;
					}
				}
				//echo $s2_ddddegatsf['k']['v'] . "=";
				if( $s2_ddddegatsf['k']['v'] == "SetResponseStatus" ){
					//print_r( $s2_ddddegatsf['d'] );exit;
					$v = $this->s2_eulav_erup_teg( $s2_ddddegatsf['d']['statusCode'] );
					if( is_numeric($v) ){
						$this->s2_eeesnopser['statusCode'] = (int)$v;
					}else{
						$this->s2_eeesnopser['statusCode'] = 500;
						$this->s2_eeesnopser['body'] = ["status"=>"fail","error"=>"SetResponseStatus non numeric value" . $v];
						$this->s2_noitucexe_dnev = true;
						break;
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "RespondStatus" ){
					//print_pre( $this->s2_tttttluser );
					$this->s2_ggggggggol[] = "RespondStatus";
					$this->s2_eeesnopser['body'] = $this->s2_yarra_ot_etalpmet([
						'status'=>$s2_ddddegatsf['d']['status'],
						'data'=>$this->s2_eeulav_teg($s2_ddddegatsf['d']['data']),
						'error'=>$this->s2_eeulav_teg($s2_ddddegatsf['d']['error']),
					]);
					$this->s2_noitucexe_dnev = true;
					return $this->s2_eeesnopser;
				}
				if( $s2_ddddegatsf['k']['v'] == "RespondJSON" ){
					if( isset($this->s2_ssssnoitpo['raw_output']) ){
						return ["status"=>"success", "data"=>$this->s2_etutitsbus_ot_etalpmet( $s2_ddddegatsf['d']['output']['v'] ) ];
					}else if( $s2_ddddegatsf['d']['output']['t'] == "O" ){
						$this->s2_eeesnopser['body'] =$this->s2_yarra_ot_etalpmet( $s2_ddddegatsf['d']['output']['v'] );
						if( $s2_ddddegatsf['d']['pretty']['v'] != "false" && $s2_ddddegatsf['d']['pretty']['v'] !== false  ){
							$this->s2_eeesnopser['pretty'] = true;
						}
						$this->s2_noitucexe_dnev = true;
						return $this->s2_eeesnopser;
					}else{
						$this->s2_ggggggggol[] = "Respond: " . $s2_ddddegatsf['d']['t'];
						$this->s2_eeesnopser['body'] = ["status"=>"fail", "error"=>"Unhandled retrun type"];
						$this->s2_noitucexe_dnev = true;
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "RespondVar" ){
					$r = false;
					//var_dump( $s2_ddddegatsf['d']['raw'] );exit;
					//print_r( $this->s2_tttttluser );
					// print_r( $s2_ddddegatsf['d']['output'] );
					// print_r( $this->s2_eeulav_teg( $s2_ddddegatsf['d']['output'] ) );
					if( isset($s2_ddddegatsf['d']['raw']) ){ if( $s2_ddddegatsf['d']['raw']['v'] === "true" ){$r = true;} }
					if( isset($this->s2_ssssnoitpo['raw_output']) || $r ){
						//echo "giving raw";exit;
						if( $s2_ddddegatsf['d']['output']['t'] == "V" ){
							$this->s2_eeesnopser['body'] = $this->s2_eeulav_teg($s2_ddddegatsf['d']['output']);
						}else{
							$this->s2_eeesnopser['body'] = $s2_ddddegatsf['d']['output'];
						}
						$this->s2_noitucexe_dnev = true;
						return $this->s2_eeesnopser;
					}else if( $s2_ddddegatsf['d']['output']['t'] == "V" ){
						$this->s2_eeesnopser['body'] = $this->s2_eulav_erup_teg( $s2_ddddegatsf['d']['output'] );
						$this->s2_noitucexe_dnev = true;
						return $this->s2_eeesnopser;
					}else{
						$this->s2_ggggggggol[] = "Respond: " . $s2_ddddegatsf['d']['t'];
						$this->s2_eeesnopser['body'] = ["status"=>"fail", "error"=>"Unhandled retrun type"];
					}
					$this->s2_noitucexe_dnev = true;
				}
				if( $s2_ddddegatsf['k']['v'] == "RespondGlobals" ){

					$vv = [];
					foreach( $this->s2_tttttluser as $ii=>$jj ){
						if( $s2_ddddegatsf['d']['raw']['v'] === "true" ){
							$vv[ $ii ] = $this->s2_eeulav_teg($jj);
						}else{
							$vv[ $ii ] = $this->s2_eulav_erup_teg($jj);
						}
					}
					$this->s2_eeesnopser['body'] = $vv;
					$this->s2_noitucexe_dnev = true;
					return $this->s2_eeesnopser;
				}
				if( $s2_ddddegatsf['k']['v'] == "RespondVars" ){
					if( isset($this->s2_ssssnoitpo['raw_output']) ){
						$vv = [];
						foreach( $s2_ddddegatsf['d']['outputs'] as $ii=>$jj ){
							$vv[ $jj['v']['v'] ] = $this->s2_eeulav_teg($jj);
						}
						$this->s2_noitucexe_dnev = true;
						return ["status"=>"success", "data"=>$vv ];
					}else{
						$vv = [];
						foreach( $s2_ddddegatsf['d']['outputs'] as $ii=>$jj ){
							$vv[ $jj['v']['v'] ] = $this->s2_eulav_erup_teg($jj);
						}
						$this->s2_eeesnopser['body'] = $vv;
						$this->s2_noitucexe_dnev = true;
						return $this->s2_eeesnopser;
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "RespondXML" ){
					if( $s2_ddddegatsf['d']['output']['t'] == "O" ){
						$this->s2_eeesnopser['body'] =$this->s2_yarra_ot_etalpmet( $s2_ddddegatsf['d']['v'] );
						$this->s2_noitucexe_dnev = true;
						return $this->s2_eeesnopser;
					}else{
						$this->s2_ggggggggol[] = "Respond: " . $s2_ddddegatsf['d']['t'];
						$this->s2_eeesnopser['body'] = ["status"=>"fail", "error"=>"Unhandled retrun type"];
						$this->s2_noitucexe_dnev = true;
					}
				}
				//echo $s2_ddddegatsf['k']['v'];exit;
				if( $s2_ddddegatsf['k']['v'] == "RespondPage" ){
					if( isset($s2_ddddegatsf['d']['page']['v']['i']['v']) ){
						if( $s2_ddddegatsf['d']['page']['v']['i']['v'] != "" ){
							$page_version = $this->s2_nnnnnnnnoc->find_one( $this->s2_xxiferp_bd . "_pages_versions", [
								"_id"=>$s2_ddddegatsf['d']['page']['v']['i']['v']
							]);
							if( !$page_version['data'] ){
								$this->s2_noitucexe_dnev = true;
								$this->s2_eeesnopser = [
									'statusCode'=>500, 
									'body'=> $page_version, 
									["headers"=>['content-type'=>"application/json"]]
								];break;
							}
							$page_version = $page_version['data'];
							require_once("index_page.php");exit;
						}else{
							$this->s2_noitucexe_dnev = true;
							$this->s2_eeesnopser = [
								'statusCode'=>500, 
								'body'=> "Page version not found",
								["headers"=>['content-type'=>"text/plain"]]
							];break;
						}
					}else{
						$this->s2_noitucexe_dnev = true;
						$this->s2_eeesnopser = [
							'statusCode'=>500, 
							'body'=> "Page version not found",
							["headers"=>['content-type'=>"text/plain"]]
						];break;
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "RespondFile" ){
					if( isset($s2_ddddegatsf['d']['file']['v']['i']['v']) ){
						if( $s2_ddddegatsf['d']['file']['v']['i']['v'] != "" ){
							$file_version = $this->s2_nnnnnnnnoc->find_one( $this->s2_xxiferp_bd . "_files", [
								"_id"=>$s2_ddddegatsf['d']['file']['v']['i']['v']
							]);
							//print_r( $file_version );exit;
							if( !$file_version['data'] ){
								$this->s2_eeesnopser = [
									'statusCode'=>500, 
									'body'=> $file_version, 
									["headers"=>['content-type'=>"application/json"]]
								];break;
							}
							$file_version = $file_version['data'];
							$file_id = $file_version['_id'];
							require_once("index_file.php");
							$resp = index_file($file_version);
							$this->s2_eeesnopser['statusCode']=$resp[0];
							$this->s2_eeesnopser['headers']['Content-Type']=$resp[1];
							$this->s2_eeesnopser['body'] = $resp[3];
							$this->s2_noitucexe_dnev = true;
						}else{
							$this->s2_noitucexe_dne(500, ["status"=>"fail", "error"=>"File not found"] );break;
						}
					}else{
						$this->s2_noitucexe_dne(500, ["status"=>"fail", "error"=>"File not found"] );break;
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "AddHTML" ){
					//print_pre( $this->s2_tttttluser );
					if( $this->s2_eeeeenigne['output-type'] != "text/html" ){
						$this->s2_ggggggggol[] = "Incorrect page type and Response Format";
					}else{
						$this->s2_ggggggggol[] = "AddHTML";
						if( gettype( $this->s2_eeesnopser['body'] ) == "array" ){
							$this->s2_eeesnopser['body'] = "";
						}
						if( $s2_ddddegatsf['d']['t'] == "T" || $s2_ddddegatsf['d']['t'] == "TT" ){
							$this->s2_eeesnopser['body'] .= $this->s2_srav_lmth_ecalper( $s2_ddddegatsf['d']['v'] ) . "\n";
						}else if( $s2_ddddegatsf['d']['t'] == "HT" ){
							$this->s2_eeesnopser['body'] .= $this->s2_srav_lmth_ecalper( $s2_ddddegatsf['d']['v'] ) . "\n";
						}else if( $s2_ddddegatsf['d']['t'] == "V" ){
							$d = $this->s2_eeulav_teg( $s2_ddddegatsf['d'] );
							if( gettype($d) == "array" ){
								if( $d['t'] == "O" || $d['t'] == "L" ){
									$this->s2_eeesnopser['body'] .= json_encode( $this->s2_yarra_ot_etalpmet( $d['v'] ) , JSON_PRETTY_PRINT);
								}else{
									$this->s2_eeesnopser['body'] .= $this->s2_srav_lmth_ecalper( $d['v'] );
								}
							}
						}else{
							//print_pre( $s2_ddddegatsf['d'] );exit;
							$this->s2_eeesnopser['body'] .= "\nIncorrect Output Format: " . $s2_ddddegatsf['d']['output']['t'];
							$this->s2_ggggggggol[] = "Respond: " . $s2_ddddegatsf['d']['t'];
						}
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "RenderHTML" ){
					//print_pre( $this->s2_tttttluser );
					if( $this->s2_eeeeenigne['output-type'] != "text/html" ){
						$this->s2_ggggggggol[] = "Incorrect page type and Response Format";
					}else{
						$this->s2_ggggggggol[] = "RenderHTML";
						if( gettype( $this->s2_eeesnopser['body'] ) == "array" ){
							$this->s2_eeesnopser['body'] = "";
						}
						if( $s2_ddddegatsf['d']['t'] == "T" || $s2_ddddegatsf['d']['t'] == "TT" || $s2_ddddegatsf['d']['t'] == "HT" ){
							$this->s2_eeesnopser['body'] .= $this->s2_srav_lmth_ecalper( $s2_ddddegatsf['d']['v'] ) . "\n";
						}else{
							$this->s2_eeesnopser['body'] .= "\nIncorrect Output Format: " . $s2_ddddegatsf['d']['output']['t'];
							$this->s2_ggggggggol[] = "Respond: " . $s2_ddddegatsf['d']['t'];
						}
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "Sleep" ){
					sleep((int)$s2_ddddegatsf['d']['v']);
				}
				if( $s2_ddddegatsf['k']['v'] == "SleepMs" ){
					usleep( ((int)$s2_ddddegatsf['d']['v']*1000) );
				}
				if( $s2_ddddegatsf['k']['v'] == "Log" ){
					if( $s2_ddddegatsf['d']['t'] == "O" ){
						$this->s2_ggggggggol[] = $this->s2_yarra_ot_etalpmet( $s2_ddddegatsf['d']['v'] );
					}else{
						$this->s2_ggggggggol[] = "Log: Incorrect Type: " . $s2_ddddegatsf['d']['t'];
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "Function" ){
					$val = $this->s2_noitcnuf_od( $s2_ddddegatsf['d'] );
					if( $s2_ddddegatsf['d']['return'] ){
						$this->s2_tluser_tes( $s2_ddddegatsf['d']['lhs'], $val );
					}
				}
				if( $s2_ddddegatsf['k']['v'] == "FunctionCall" ){
					$s2_sssssssser = $this->s2_llac_noitcnuf_od( $s2_ddddegatsf['d'] );
					//print_pre( $s2_sssssssser );
					if( $s2_sssssssser['status'] == "fail" ){
						if( isset($this->s2_ssssnoitpo['raw_output']) ){
							return $s2_sssssssser;
						}else{
							$this->s2_eeesnopser['statusCode'] = 500;
							$this->s2_eeesnopser['body'] = $s2_sssssssser;
							if( isset($this->s2_ssssnoitpo['raw_output']) ){
								return $s2_sssssssser;
							}
							return $this->s2_eeesnopser;
						}
					}
					//print_pre( $s2_sssssssser );
					if( !isset($s2_sssssssser['body']) ){
						$this->s2_eeesnopser['statusCode'] = 500;
						$this->s2_eeesnopser['body'] = "functionCall: No data returned";
						return $this->s2_eeesnopser;
					}
					if( !isset($s2_sssssssser['body']['t']) || !isset($s2_sssssssser['body']['v']) ){
						$this->s2_eeesnopser['statusCode'] = 500;
						$this->s2_eeesnopser['body'] = "functionCall: Incorrect return type". json_encode($s2_sssssssser['data']);
						return $this->s2_eeesnopser;
					}
					$this->s2_tluser_tes( $s2_ddddegatsf['d']['lhs'], $s2_sssssssser['body'] );
				}
				if( $s2_ddddegatsf['k']['v'] == "MongoDb" ){
					$val = $this->s2_bbbbdognom( $s2_ddddegatsf );
				}
				if( $s2_ddddegatsf['k']['v'] == "MySql" ){
					$val = $this->s2_llllllqsym( $s2_ddddegatsf );
					//print_r( $val );exit;
				}
				if( $s2_ddddegatsf['k']['v'] == "Internal-Table" ){
					$val = $this->table_dynamic( $s2_ddddegatsf );
					//echo "xxx".$val;
				}
				if( $s2_ddddegatsf['k']['v'] == "HTTPRequest" ){
					$val = $this->s2_tseuqeRPTTH( $s2_ddddegatsf );
				}
				if( $s2_ddddegatsf['k']['v'] == "Create-Access-Key" ){
					$val = $this->s2_yek_etaerc_htua( $s2_ddddegatsf );
				}
				if( $s2_ddddegatsf['k']['v'] == "Generate-Session-Key" ){
					$val = $this->s2_yek_noisses_etareneg_htua( $s2_ddddegatsf );
				}
				if( $s2_ddddegatsf['k']['v'] == "Assume-Session-Key" ){
					$val = $this->s2_yek_noisses_emussa_htua( $s2_ddddegatsf );
				}
				
			}else{ // variable commands
				if( $s2_ddddegatsf['k']['v'] != "None" && $s2_ddddegatsf['k']['v'] != "none" ){
					if( $this->s2_ttttttessi( $s2_ddddegatsf['k']['v'] ) ){
						//print_pre( $s2_ddddegatsf['k'] ); //exit;
						//echo $s2_ddddegatsf['k']['v'] . "\n";
						$var = $s2_ddddegatsf['k']['v'];
						if( $s2_ddddegatsf['k']['vs']['d']['self'] && $s2_ddddegatsf['k']['vs']['d']['replace'] ){
							$newval = $this->s2_eeulav_teg(['t'=>"V", 'v'=>$s2_ddddegatsf['k']]);
							$this->s2_tluser_tes( $var, $newval );
						}else{
							$this->s2_eeulav_tes($s2_ddddegatsf['k']);
						}
					}else{
						$this->s2_ggggggggol[] = "ERROR: " . $s2_ddddegatsf['k']['v'] . " not found!";
					}
				}
			}
			$s2_verp_iegatsf = $s2_iiiiegatsf;
		}
		//print_r( $this->s2_eeesnopser );
		return $this->s2_eeesnopser;
	}
	function s2_srav_lmth_ecalper( $v ){
		preg_match_all( "/\{\{(.*?)\}\}/", $v, $m);
		if( $m[0] ){
			foreach( $m[0] as $ii=>$jj ){
				$d = $this->s2_eeulav_teg( trim($m[1][$ii]) );
				if( $d['t'] == "O" || $d['t'] == "L" ){
					$v = str_replace( $jj, json_encode($d['v']), $v );
				}else{
					$v = str_replace( $jj, $d['v'], $v );
				}
			}
		}
		return $v;
	}
	function s2_noitcnuf_od( $s2_ddddegatsf ){
		$_fn = $s2_ddddegatsf['fn'];
		$_fn_inputs = $s2_ddddegatsf['inputs'];
		unset($_fn_inputs['type']);
		$_c = "";
		$_ct = "B";
		foreach( $_fn_inputs as $i=>$j ){if( $i != "type" ){
			if( $j['t'] == "V" ){
				//echo $j['v']['v']. "\n";
				$v = $this->s2_eeulav_teg( $j['v']['v'] );
				$_fn_inputs[ $i ]['v'] = $v['v'];
				$_fn_inputs[ $i ]['t'] = $v['t'];
			}
		}}
		if( !$_fn_inputs["p1"]['v'] && gettype( $_fn_inputs["p1"]['v'] ) != "array" ){
			$this->s2_ggggggggol[] = "Variable [".$_fn_inputs["p1"]['name']."] empty";
		}
		if( $s2_ddddegatsf['fn'] == "Round" ){
			$_ct = "N";
			$p1 = $this->s2_rebmun_ot_gnirts($_fn_inputs["p1"]['v']);
			if( $_fn_inputs["p3"]['v'] ){
				$op = $_fn_inputs["p3"]['v'];
				if( $op == "Upper" ){
					$op = PHP_ROUND_HALF_UP;
				}else if( $op == "Upper" ){
					$op = PHP_ROUND_HALF_DOWN;
				}else{
					$op = null;
				}
				$_c = round( $p1, $this->s2_rebmun_ot_gnirts($_fn_inputs["p2"]['v']), $op );
			}else{
				$_c = round( $p1, $this->s2_rebmun_ot_gnirts($_fn_inputs["p2"]['v']) );
			}
		}
		if( $s2_ddddegatsf['fn'] == "Random Number" ){
			$_ct = "N";
			if( $_fn_inputs["p1"]['v']  && $_fn_inputs["p2"]['v'] ){
				$_c = rand((int) $_fn_inputs["p1"]['v'], (int)$_fn_inputs["p2"]['v'] );
			}else{
				$_c = rand( 0,1000 );
			}
		}
		if( $s2_ddddegatsf['fn'] == "Text to Number" ){
			$_ct = "N";
			$_c = $this->s2_rebmun_ot_gnirts( $_fn_inputs["p1"]['v'] );
		}
		if( $s2_ddddegatsf['fn'] == "Number Format" ){
			$_ct = "T";
			$_c = (string)number_format($this->s2_rebmun_ot_gnirts($_fn_inputs["p1"]['v']),(int)$_fn_inputs["p2"]['v']);
		}
		if( $s2_ddddegatsf['fn'] == "Text Padding" ){
			$_ct = "T";
			$m = $_fn_inputs["p4"]['v'];
			if( $m == "Left" ){$m = STR_PAD_LEFT;}
			if( $m == "Right" ){$m = STR_PAD_RIGHT;}
			if( $m == "Both" ){$m = STR_PAD_BOTH;}
			$_c = (string)str_pad( $_fn_inputs["p1"]['v'], $_fn_inputs["p2"]['v'], (string)$_fn_inputs["p3"]['v'],$m);
		}
		if( $s2_ddddegatsf['fn'] == "Number to Text" ){
			$_ct = "T";
			$_c = (string)$_fn_inputs["p1"]['v'];
		}
		if( $s2_ddddegatsf['fn'] == "is it Text" ){
			$_ct = "B";
			$_c = false;
			if( is_string($_fn_inputs["p1"]['v']) == "string" ){
				$_c = true;
			}
		}
		if( $s2_ddddegatsf['fn'] == "is it Numeric" ){
			$_ct = "B";
			$_c = false;
			if( is_numeric($_fn_inputs["p1"]['v']) ){
				$_c = true;
			}
		}
		if( $s2_ddddegatsf['fn'] == "is it Binary" ){
			$_ct = "B";
			if( $this->isBinary($_fn_inputs["p1"]['v']) || $_fn_inputs["p1"]['t'] == "BIN" ){
				$_c = true;
			}else{
				$_c = false;
			}
		}
		if( $s2_ddddegatsf['fn'] == "Number to Letter" ){
			$_ct = "T";
			$_c = chr($_fn_inputs["p1"]['v']);
		}
		if( $s2_ddddegatsf['fn'] == "Change Type" ){
			if( $_fn_inputs["p2"]['v'] == "T" ){
				$_c = (string)$_fn_inputs["p1"]['v'];
				$_ct = 'T';
			}else if( $_fn_inputs["p2"]['v'] == "N" ){
				$_c = $this->s2_rebmun_ot_gnirts( $_fn_inputs["p1"]['v'] );
				$_ct  = 'N';
			}else if( $_fn_inputs["p2"]['v'] == "B" ){
				$_c = true;
				$_ct  = 'B';
			}else if( $_fn_inputs["p2"]['v'] == "O" ){
				$_c = [];
				$_ct  = 'O';
			}else if( $_fn_inputs["p2"]['v'] == "L" ){
				$_c = [];
				$_ct  = 'L';
			}else if( $_fn_inputs["p2"]['v'] == "BIN" ){
				$_c = "";
				$_ct = 'BIN';
			}else if( $_fn_inputs["p2"]['v'] == "B64" ){
				$_c = "";
				$_ct = 'B64';
			}
		}
		if( $s2_ddddegatsf['fn'] == "ucwords" ){
			$_ct = "T";
			$_c = ucwords($_fn_inputs["p1"]['v']);
		}
		if( $s2_ddddegatsf['fn'] == "UniqID" ){
			$_ct = "T";
			$_c = uniqid();
		}
		if( $s2_ddddegatsf['fn'] == "mongodb_id" ){
			$_ct = "T";
			$_c = $this->s2_nnnnnnnnoc->generate_id();
		}
		if( $s2_ddddegatsf['fn'] == "Get Date String" ){
			$_ct = "T";
			$f = $_fn_inputs["p1"]['v'];
			$ts = $_fn_inputs["p2"]['v'];
			if( !is_numeric($ts) ){ $ts = -1;}
			if( $ts != -1 ){
				$_c = date( $f, $ts );
			}else{
				$_c = date( $f );
			}
		}
		if( $s2_ddddegatsf['fn'] == "Get Date" ){
			$_ct = "D";
			$_c = date("Y-m-d");
		}
		if( $s2_ddddegatsf['fn'] == "Get DateTime" ){
			$_ct = "DT";
			$_c = ["v"=>date("Y-m-d H:i:s"), "t"=> "DT", "tz"=> "UTC+00:00"];
		}
		if( $s2_ddddegatsf['fn'] == "Get Timestamp" ){
			$_ct = "TS";
			$_c = time();
		}

		if( $s2_ddddegatsf['fn'] == "Set Timezone" ){
			$_ct = "B";
			$t = $_fn_inputs["p1"]['v'];
			date_default_timezone_set( "Asia/Kolkata" );
			$_c = true;
		}
		if( $s2_ddddegatsf['fn'] == "Get Timezone" ){
			$_ct = "T";
			$_c = date_default_timezone_get();
		}
		if( $s2_ddddegatsf['fn'] == "Add Days" ){
			$_ct = "D";
			$_c = date("Y-m-d", strtotime( $_fn_inputs["p1"]['v'] )+($_fn_inputs["p2"]['v']*86400) );
		}
		if( $s2_ddddegatsf['fn'] == "Date To Timestamp" ){
			$_ct = "TS";
			// $t = $this->s2_eeulav_teg( $_fn_inputs["p1"] );
			$_c = $this->s2_emit_hcope_erup_teg( $_fn_inputs["p1"] );
		}
		if( $s2_ddddegatsf['fn'] == "Timestamp To Date" ){
			$_ct = "D";
			$t = $this->s2_emit_hcope_erup_teg( $_fn_inputs["p1"] );
			$_c = date("Y-m-d",$t);
		}
		if( $s2_ddddegatsf['fn'] == "Timestamp To DateTime" ){
			$_ct = "DT";
			$t = $this->s2_emit_hcope_erup_teg( $_fn_inputs["p1"] );
			$_c = ['v'=>date("Y-m-d H:i:s",$t), "t"=> "DT", "tz"=> date_default_timezone_get() ];
		}
		if( $s2_ddddegatsf['fn'] == "Minus Days" ){
			$_ct = "D";
			$_c = date("Y-m-d", strtotime( $_fn_inputs["p1"]['v'] )-($_fn_inputs["p2"]['v']*86400) );
		}
		if( $s2_ddddegatsf['fn'] == "StrToTime" ){
			$_ct = "TS";
			$_c = strtotime( $_fn_inputs["p1"]['v'] );
		}
		if( $s2_ddddegatsf['fn'] == "Days till Today" ){
			$_ct = "N";
			$_c = time()-strtotime( $_fn_inputs["p1"]['v'] );
			$_c = floor($_c/86400);
		}
		if( $s2_ddddegatsf['fn'] == "Months till Today" ){
			$_ct = "N";
			$_c = time()-strtotime( $_fn_inputs["p1"]['v'] );
			$_c = floor($_c/86400/30);
		}
		if( $s2_ddddegatsf['fn'] == "Years till Today" ){
			$_ct = "N";
			$_c = time()-strtotime( $_fn_inputs["p1"]['v'] );
			$_c = floor($_c/86400/365);
		}
		if( $s2_ddddegatsf['fn'] == "Days Diff" ){
			$_ct = "N";
			$_c = strtotime( $_fn_inputs["p2"]['v'] )-strtotime( $_fn_inputs["p1"]['v'] );
			$_c = floor($_c/86400);
		}
		if( $s2_ddddegatsf['fn'] == "Months Diff" ){
			$_ct = "N";
			$_c = strtotime( $_fn_inputs["p2"]['v'] )-strtotime( $_fn_inputs["p1"]['v'] );
			$_c = floor($_c/86400/30);
		}
		if( $s2_ddddegatsf['fn'] == "Years Diff" ){
			$_ct = "N";
			$s2_11111111c_ = strtotime( $_fn_inputs["p1"]['v'] );
			$s2_22222222c_ = strtotime( $_fn_inputs["p2"]['v'] );
			$s2_33333333c_ = $s2_22222222c_ - $s2_11111111c_;
			$_c = floor($s2_33333333c_/86400/365);
		}
		if( $s2_ddddegatsf['fn'] == "Change Format" ){
			$_ct = "D";
			$_c = date($_fn_inputs["p2"]['v'], strtotime( $_fn_inputs["p1"]['v'] ));
		}

		//  LIST FUNCTIONS 
		if( $s2_ddddegatsf['fn'] == "List Length" ){
			$_ct = "N";
			if( gettype($_fn_inputs["p1"]['v']) =="array" && array_keys($_fn_inputs["p1"]['v'])[0]===0 ){
				$_c = sizeof( $_fn_inputs["p1"]['v'] );
			}else{
				$this->s2_ggggggggol[] = "List length: non array";
				$_c = 0;
			}
		}
		if( $s2_ddddegatsf['fn'] == "Get List Item" ){
			$k = $_fn_inputs["p1"]['v'][ $_fn_inputs["p2"]['v'] ];
			$_ct = $k['t'];
			$_c = $k['v'];
		}
		if( $s2_ddddegatsf['fn'] == "List Append" ){
			$_ct = "B";
			$_c = true;
			if( !is_array($_fn_inputs["p1"]['v']) ){
				$this->s2_ggggggggol[] = "List Append Error: Value is not list!";
			}else{
				if( $_fn_inputs["p1"]['t'] != "L" ){
					$this->s2_ggggggggol[] = "List Append Data is not List";
					$_c = false;
				}else{
					foreach( $_fn_inputs["p2"]['v'] as $i=>$j ){
						$_fn_inputs["p1"]['v'][] = $j;
					}
					$this->s2_tluser_tes( $s2_ddddegatsf['inputs']['p1']['v']['v'], $_fn_inputs["p1"] );
					$_c = true;
				}
			}
		}
		if( $s2_ddddegatsf['fn'] == "List Prepend" ){
			$_ct = "B";
			$_c = true;
			if( !is_array($_fn_inputs["p1"]['v']) ){
				$this->s2_ggggggggol[] = "List Prepend Error: Value is not list!";
			}else{
				if( $_fn_inputs["p1"]['t'] != "L" ){
					$this->s2_ggggggggol[] = "List Prepend Data is not List";
					$_c = false;
				}else{
					foreach( $_fn_inputs["p2"]['v'] as $i=>$j ){
						array_splice($_fn_inputs["p1"]['v'], $i, 0, [$j] );
					}
					$this->s2_tluser_tes( $s2_ddddegatsf['inputs']['p1']['v']['v'], $_fn_inputs["p1"] );
					$_c = true;
				}
			}
		}
		if( $s2_ddddegatsf['fn'] == "List Item Remove" ){
			$k = array_splice( $_fn_inputs["p1"]['v'], $_fn_inputs["p2"]['v'], 1 );
			$_c = $k[0]['v'];
			$_ct = $k[0]['t'];
			$this->s2_tluser_tes( $s2_ddddegatsf['inputs']['p1']['v']['v'], $_fn_inputs["p1"] );
		}
		if( $s2_ddddegatsf['fn'] == "Get Value" ){
			$_ct = $s2_ddddegatsf['return'];
			$_c = $_fn_inputs["p1"]['v'][ $_fn_inputs["p2"]['v'] ];
		}
		if( $s2_ddddegatsf['fn'] == "Set Value" ){
			$_fn_inputs["p1"]['v'][ $_fn_inputs["p2"]['v'] ] = $_fn_inputs["p3"]['v'];
			$_ct = "O";
			$_fn_inputs["p1"]['v'][ $_fn_inputs["p2"]['v'] ];
			$_c = $_fn_inputs["p1"]['v'];
		}
		// LIST FUNCTIONS
		// String Functions Start
		
		if( $s2_ddddegatsf['fn'] == "Concat" ){
			$_ct = "T";
			$_c = "";
			foreach( $_fn_inputs as $i=>$j ){
				$_c .= $j['v'];
			}
		}
		if( $s2_ddddegatsf['fn'] == "Sub String" ){
			$_ct = "T";
			$_c = substr( (string)$_fn_inputs["p1"]['v'], (int)$_fn_inputs["p2"]['v'], (int)$_fn_inputs["p3"]['v'] );
		}
		if( $s2_ddddegatsf['fn'] == "Replace Text" ){
			$_ct = "T";
			$_c = str_replace( $_fn_inputs["p2"]['v'], $_fn_inputs["p3"]['v'], $_fn_inputs["p1"]['v'] );
		}
		if( $s2_ddddegatsf['fn'] == "to Upper Case" ){
			$_ct = "T";
			$_c = strtoupper( $_fn_inputs["p1"]['v'] );
		}
		if( $s2_ddddegatsf['fn'] == "to Lower Case" ){
			$_ct = "T";
			$_c = strtolower( $_fn_inputs["p1"]['v'] );
		}
		if( $s2_ddddegatsf['fn'] == "Trim" ){
			$_ct = "T";
			$_c = trim( $_fn_inputs["p1"]['v'] );
		}
		if( $s2_ddddegatsf['fn'] == "Clean" ){
			$_ct = "T";
			$_c = preg_replace("/[\W]+/", "", $_fn_inputs["p1"]['v'] );
		}
		if( $s2_ddddegatsf['fn'] == "Match Pattern" ){
			$m = false;
			@preg_match($_fn_inputs["p2"]['v'], $_fn_inputs["p1"]['v'], $m );
			// if( !$m ){
			// 	echo $_fn_inputs["p1"]['v'];
			// 	echo $_fn_inputs["p2"]['v'];
			// 	exit;
			// }
			if( $m ){
				if( $_fn_inputs["p3"]['v'] == "True" ){
					$_ct = "B";
					$_c = true;
				}else if( $_fn_inputs["p3"]['v'] == "Matched String" ){
					$_ct = "T";
					$_c = $m[0];
				}else if( $_fn_inputs["p3"]['v'] == "Matched Group 1" && $m[1] ){
					$_ct = "T";
					$_c = $m[1];
				}else if( $_fn_inputs["p3"]['v'] == "Matched Group 2" && $m[2] ){
					$_ct = "T";
					$_c = $m[2];
				}else if( $_fn_inputs["p3"]['v'] == "Matched Group 3" && $m[3] ){
					$_ct = "T";
					$_c = $m[3];
				}else{
					$_ct = "B";
					$_c = false;
				}
			}else{
				$_ct = "B";
				$_c = false;
			}
		}
		if( $s2_ddddegatsf['fn'] == "Validate Input" ){
			$_c = true;
			$_ct = "B";
			if( $_fn_inputs["p2"]['v'] == "Email" ){
				@preg_match("/^[a-z0-9\.\-]{3,50}\@[a-z0-9\.\-]{3,50}\.[a-z\.]{2,6}$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "Mobile" ){
				@preg_match("/^[0-9]{10}$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "Phone" ){
				@preg_match("/^[0-9\+\-\ ]{10,16}$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "yyyy-mm-dd" ){
				@preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "Alpha" ){
				@preg_match("/^[a-z]+$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "Alpha with spaces" ){
				@preg_match("/^[a-z\ ]+$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "Alpha with spaces - . _" ){
				@preg_match("/^[a-z\ \-\.\_]+$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "AlphaNumeric" ){
				@preg_match("/^[a-z0-9]+$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "AlphaNumeric with spaces" ){
				@preg_match("/^[a-z0-9\ ]+$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "AlphaNumeric with space - . _" ){
				@preg_match( "/^[a-z0-9\ \.\-\_]+$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "Numeric" ){
				@preg_match( "/^[0-9\.]+$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "PAN" ){
				@preg_match( "/^[a-z]{5}[0-9]{4}[a-z]$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "CreditCard" ){
				@preg_match( "/^[0-9]{4}[\-\ ][0-9]{4}[\-\ ][0-9]{4}[\-\ ][0-9]{4}$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "Aadhaar" ){
				@preg_match( "/^[0-9]{4}[\-\ ]{0,1}[0-9]{4}[\-\ ]{0,1}[0-9]{4}$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "MongoDBId" ){
				@preg_match( "/^[a-f0-9]{24}$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}else if( $_fn_inputs["p2"]['v'] == "HexCode" ){
				@preg_match( "/^[a-f0-9]+$/i", $_fn_inputs["p1"]['v'], $m );
				if( $m ){ $_c = true; }else{ $_c = false; }
			}
			if( $_c ){
				//if( strlen($_fn_inputs["p1"]['v']) < $_fn_inputs["p3"]['v'] )
			}
		}
		if( $s2_ddddegatsf['fn'] == "JSON Encode" ){
			$_ct = "T";
			if( $_fn_inputs['p2']['v'] == "true" ){
				$_c = json_encode( $this->s2_yarra_ot_etalpmet( $_fn_inputs["p1"]['v'] ), JSON_PRETTY_PRINT );
			}else{
				$_c = json_encode( $this->s2_yarra_ot_etalpmet( $_fn_inputs["p1"]['v'] ) );
			}
			if( json_last_error() ){
				$this->s2_ggggggggol[] = "JSON Encode Error: " . json_last_error_msg();
				$_c = "";
			}
		}
		if( $s2_ddddegatsf['fn'] == "JSON Decode" ){
			if( !is_string($_fn_inputs["p1"]['v']) ){
				$this->s2_ggggggggol[] = "JSON Decode Error: Input is not string";
				$_ct = "O";
				$_c = [];
			}else{
				$_c = json_decode( $_fn_inputs["p1"]['v'], true );
				$this->s2_tcejbo_ot_tupni( $_c );
				if( gettype($_c) == "array" ){
					if( array_keys($_c)[0] === 0 ){
						$_ct = "L";
					}else{
						$_ct = "O";
					}
				}
				if( json_last_error() ){
					$this->s2_ggggggggol[] = "JSON Decode Error: " . json_last_error_msg();
					$_ct = "O";
					$_c = [];
				}
			}
		}
		if( $s2_ddddegatsf['fn'] == "HTML Entity Decode" ){
			$_ct = "T";
			$_c = str_replace("&quot;", "\"", $_fn_inputs["p1"]['v']);
			$_c = str_replace("&lt;", "<", $_c);
			$_c = str_replace("&gt;", ">", $_c);
		}
		if( $s2_ddddegatsf['fn'] == "XML Decode" ){
			$_ct = "O";
			$_c = "";
			$_error = "";
			try{
				$body_parsed = simplexml_load_string($_fn_inputs["p1"]['v']);
				preg_match("/^\<\?xml.*\?\>/i", $_fn_inputs["p1"]['v'], $m);
				if( $m ){
					$_fn_inputs["p1"]['v'] = substr($_fn_inputs["p1"]['v'], strlen($m[0]), strlen($_fn_inputs["p1"]['v']));
				}
				preg_match("/^\<([a-z0-9\:\-\_\.]+)/i", $_fn_inputs["p1"]['v'], $m);
				if( $m ){
					if( $m[1] ){
						$body_parsed = $this->parsexml($body_parsed);
						$_c = [$m[1]=>$body_parsed];
					}
				}
			}catch(Exception $ex){
				$_error = $ex->getMessage();
				$this->s2_ggggggggol[] = "XML Decode Error: " . $_error;
			}
		}
		if( $s2_ddddegatsf['fn'] == "Base64 Encode" ){
			$_ct = "B64";
			$_c = base64_encode($_fn_inputs["p1"]['v']);
		}
		if( $s2_ddddegatsf['fn'] == "Base64 Decode" ){
			$_ct = "T";
			$_c = base64_decode($_fn_inputs["p1"]['v']);
			if( $this->isBinary($_c) ){
				$_ct = "BIN";
			}
		}
		if( $s2_ddddegatsf['fn'] == "Generate IV" ){
			if( $_fn_inputs["p2"]['v'] == "NullBytes" ){
				$_c = "0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
				$_c = substr($_c, 0, (int)$_fn_inputs["p1"]['value'] );
			}else{
				$_c = random_bytes( (int)$_fn_inputs["p1"]['v'] );
			}
			$_ct = "BIN";
		}
		if( $s2_ddddegatsf['fn'] == "Random Text" ){
			$_c = substr( str_shuffle( "abcdef0123456790ghijklmnopqrstuvwxyzabcdef0123456790ghijklmnopqrstuvwxyzabcdef0123456790ghijklmnopqrstuvwxyzabcdef0123456790ghijklmnopqrstuvwxyzabcdef0123456790ghijklmnopqrstuvwxyzabcdef0123456790ghijklmnopqrstuvwxyz" ), 0, (int)$_fn_inputs["p1"]['v'] );
			$_ct = "T";
		}
		if( $s2_ddddegatsf['fn'] == "Get IV Size" ){
			$_ct = "N";
			$_c = openssl_cipher_iv_length($_fn_inputs["p1"]['value']);
		}
		if( $s2_ddddegatsf['fn'] == "Hex to Bin" ){
			$_ct = "BIN";
			$_c = hex2bin($_fn_inputs["p1"]['value']);
		}
		if( $s2_ddddegatsf['fn'] == "Hash" ){
			$_ct = "T";
			$ctx = hash_init($_fn_inputs["p2"]['v']);
			if( $_fn_inputs["p3"]['v'] ){
				hash_update( $ctx, $_fn_inputs["p3"]['v'] );
			}
			hash_update( $ctx, $_fn_inputs["p1"]['v'] );
			$_c = hash_final( $ctx );
			$_ct = "B64";
		}
		if( $s2_ddddegatsf['fn'] == "OpenSSL Public Encrypt" ){
			if( $_fn_inputs["p3"]['v'] == "OPENSSL_NO_PADDING" ){
				$st = openssl_public_encrypt( $_fn_inputs["p1"]['v'], $crypted, $_fn_inputs["p2"]['v'] );
			}else{
				$_fn_inputs["p3"]['v'] = constant( $_fn_inputs["p3"]['v'] );
				$st = openssl_public_encrypt( $_fn_inputs["p1"]['v'], $crypted, $_fn_inputs["p2"]['v'], $_fn_inputs["p3"]['v'] );
			}
			if( $st ){
				if( $this->s2_eulav_erup_teg($_fn_inputs["p4"]) ){
					$_ct = "B64";
					$_c = base64_encode($crypted);
					if( !$_c ){
						echo "Error base64_encode ";exit;
					}
				}else{
					$_ct = "BIN";
					$_c = $crypted;
				}
			}else{
				$this->s2_ggggggggol[] = "Encryption failed: " . $st;
				//echo "Encryption Failed" . $st;
				$_ct = "B";
				$_c = false;
			}
			//exit;
		}
		if( $s2_ddddegatsf['fn'] == "OpenSSL Public Decrypt" ){
			$v = $this->s2_eeulav_teg($_fn_inputs["p1"]);
			if( $v['t'] == "B64" ){
				$v['v'] = base64_decode($v['v']);
			}
			if( $_fn_inputs["p3"]['v'] == "OPENSSL_NO_PADDING" ){
				$st = openssl_public_decrypt( $v['v'], $crypted, $_fn_inputs["p2"]['v'] );
			}else{
				$_fn_inputs["p3"]['v'] = constant( $_fn_inputs["p3"]['v'] );
				$st = openssl_public_decrypt( $v['v'], $crypted, $_fn_inputs["p2"]['v'], $_fn_inputs["p3"]['v'] );
			}
			if( $st ){
				$_ct = "T";
				$_c = $crypted;
			}else{
				$this->s2_ggggggggol[] = "Decryption failed: " . $st;
				//echo "Encryption Failed" . $st;
				$_ct = "B";
				$_c = false;
			}
		}
		if( $s2_ddddegatsf['fn'] == "OpenSSL Private Encrypt" ){
			if( $_fn_inputs["p3"]['v'] == "OPENSSL_NO_PADDING" ){
				$st = openssl_private_encrypt( $_fn_inputs["p1"]['v'], $crypted, $_fn_inputs["p2"]['v'] );
			}else{
				$_fn_inputs["p3"]['v'] = constant( $_fn_inputs["p3"]['v'] );
				//echo $_fn_inputs["p3"]['v'];exit;
				$st = openssl_private_encrypt( $_fn_inputs["p1"]['v'], $crypted, $_fn_inputs["p2"]['v'], $_fn_inputs["p3"]['v'] );
			}
			if( $st ){
				if( $this->s2_eulav_erup_teg($_fn_inputs["p4"]) ){
					$_ct = "B64";
					$_c = base64_encode($crypted);
				}else{
					$_ct = "BIN";
					$_c = $crypted;
				}
			}else{
				$this->s2_ggggggggol[] = "Encryption failed: " . $st;
				$_ct = "B";
				$_c = false;
			}
		}
		if( $s2_ddddegatsf['fn'] == "OpenSSL Private Decrypt" ){
			if( $_fn_inputs["p1"]['t'] == "B64" ){
				$_fn_inputs["p1"]['v'] = base64_decode($_fn_inputs["p1"]['v']);
			}
			if( $_fn_inputs["p3"]['v'] == "OPENSSL_NO_PADDING" ){
				$st = openssl_private_decrypt( $_fn_inputs["p1"]['v'], $out, $_fn_inputs["p2"]['v'] );
			}else{
				$_fn_inputs["p3"]['v'] = constant( $_fn_inputs["p3"]['v'] );
				$st = openssl_private_decrypt( $_fn_inputs["p1"]['v'], $out, $_fn_inputs["p2"]['v'], $_fn_inputs["p3"]['v'] );
			}
			if( $st ){
				$_ct = "T";
				$_c = $out;
			}else{
				$this->s2_ggggggggol[] = "Decryption failed: " . $st;
				$_ct = "B";
				$_c = false;
			}
		}
		if( $s2_ddddegatsf['fn'] == "OpenSSL Encrypt" ){
			$_ct = "B64";
			if( $_fn_inputs["p4"]['v'] ){
				$_c = openssl_encrypt( $_fn_inputs["p1"]['v'], $_fn_inputs["p2"]['v'], $_fn_inputs["p3"]['v'], 0, $_fn_inputs["p4"]['v'] );
			}else{
				$_c = openssl_encrypt( $_fn_inputs["p1"]['v'], $_fn_inputs["p2"]['v'], $_fn_inputs["p3"]['v'] );
			}
		}
		if( $s2_ddddegatsf['fn'] == "OpenSSL Decrypt" ){
			$_ct = "T";
			if( $_fn_inputs["p4"]['v'] ){
				$_c = openssl_decrypt( $_fn_inputs["p1"]['v'], $_fn_inputs["p2"]['v'], $_fn_inputs["p3"]['v'], 0, $_fn_inputs["p4"]['v'] );
			}else{
				$_c = openssl_decrypt( $_fn_inputs["p1"]['v'], $_fn_inputs["p2"]['v'], $_fn_inputs["p3"]['v'] );
			}
		}
		if( gettype($_c) == "float" || gettype($_c) == "double" ){
			if( is_nan($_c) || is_infinite($_c) ){
				$_ct = "NL";
				$_c = "NULL";
			}
		}
		///$this->s2_ggggggggol[] = "setting: " . $_c;

		if( $s2_ddddegatsf['return'] ){
			if( isset( $s2_ddddegatsf['lhs'] ) ){
				$this->s2_ggggggggol[] = $s2_ddddegatsf['lhs']['v']['t'] . ":" . $_ct;
				if( $s2_ddddegatsf['lhs']['v']['t'] != $_ct && $s2_ddddegatsf['lhs']['v']['t'] != ($_ct=="B64"?"T":$_ct) ){
					$this->s2_ggggggggol[] = "Unexpected type assignment ... " . $s2_ddddegatsf['lhs']['v']['t'] . "!=". $_ct  ;
				}
				if( $s2_ddddegatsf['lhs']['v']['t'] != "BIN" && $_ct == "BIN" ){
					$this->s2_ggggggggol[] = "Unexpected: Binary data striped";
				}
			}
			//$this->s2_ggggggggol[] = "setting....";
			//$this->s2_tluser_tes( $s2_ddddegatsf['lhs'], ["t"=>$_ct, "v"=>$_c] );
		}
		return ["t"=>$_ct, "v"=>$_c];
	}

	function s2_noitcnuf_enilni_od( &$v, $vs ){
		// print_pre( "do_inline_function" );
		// print_pre( $v );
		// print_pre( $vs ); 
		// //exit;
		$rt = true;
		$inputs = $vs['d']['inputs'];
		if( $v['t'] == "N" ){
			foreach( $inputs as $i=>$j ){

			}
			if( $vs['v'] == "set" ){
				// return $this->s2_eeulav_teg($inputs['p2']['v']);
				//  $v['v'] = $inputs['p2']['v'];
				$v['v'] = $this->s2_eulav_erup_teg( $inputs['p2']['v'] );
				return $v;
			}
			if( $vs['v'] == "add" ){
				$add = $this->s2_eeulav_teg( $inputs['p2']['v'] );
				$v['v']+=$add['v'];
				return $v;
			}
			if( $vs['v'] == "subtract" ){
				$add = $this->s2_eeulav_teg( $inputs['p2']['v'] );
				$v['v']-=$add['v'];
				return $v;
			}
			if( $vs['v'] == "round" ){
				$de = $this->s2_eeulav_teg( $inputs['p2']['v'] );
				$v['v'] = round( (float)$v['v'], (int)$de );
				return $v;
			}
			if( $vs['v'] == "floor" ){
				$v['v'] = floor((float)$v['v']);
				return $v;
			}
			if( $vs['v'] == "ceil" ){
				$v['v'] = ceil((float)$v['v']);
				return $v;
			}
			if( $vs['v'] == "parseInt" ){
				$v['v'] = (int)$v['v'];
				return $v;
			}
			if( $vs['v'] == "convertToText" ){
				return ['t'=>"T", 'v'=>(string)$v['v']];
			}
			if( $vs['v'] == "textPadding" ){
				$mm = $this->s2_eeulav_teg($inputs['p4']['v'])['v'];
				if( $mm == "Left" ){$m = STR_PAD_LEFT;}else
				if( $mm == "Right" ){$m = STR_PAD_RIGHT;}else
				if( $mm == "Center" ){$m = STR_PAD_BOTH;}else{$m = STR_PAD_RIGHT;}
				$v['v'] = str_pad( $v['v'], (int)($this->s2_eeulav_teg($inputs['p2']['v'])['v']), $this->s2_eeulav_teg($inputs['p3']['v'])['v'], $m );
				$v['t'] = "T";
				return $v;
			}
		}else if( $v['t'] == "T" ){
			foreach( $inputs as $i=>$j ){}
			if( $vs['v'] == "set" ){
				//print_r( $inputs );exit;
				//$this->s2_eeulav_tes($inputs['p2']['v']);
				//$v['v'] = $inputs['p2']['v'];
				$v['v'] = $this->s2_eulav_erup_teg($inputs['p2']['v']);
				return $v;
			}
			if( $vs['v'] == "toLowerCase" ){
				$v['v'] = strtolower($v['v']);return $v;
			}
			if( $vs['v'] == "toUpperCase" ){
				$v['v'] = strtoupper($v['v']);return $v;
			}
			if( $vs['v'] == "trim" ){
				$v['v'] = trim($v['v']);return $v;
			}
			if( $vs['v'] == "matchPattern" ){
				$rt = $this->s2_eeulav_teg( $inputs['p3']['v'] )['v'];
				preg_match( $this->s2_eeulav_teg( $inputs['p2']['v'] )['v'], $v['v'], $m );
				if( $m ){
					if( $rt == "true" ){
						return ['t'=>"B", "v"=>true];
					}else if( $rt == "List" ){
						for($i=0;$i<sizeof($m);$i++){
							$m[ $i ] = ["t"=>"T", "v"=>$m[ $i ]];
						}
						return ['t'=>"L", "v"=>$m];
					}else if( $rt == "$0" ){
						return ['t'=>"T", "v"=>$m[0]];
					}else if( $rt == "$1" ){
						return ['t'=>"T", "v"=>$m[1]];
					}else if( $rt == "$2" ){
						return ['t'=>"T", "v"=>$m[2]];
					}
				}else{
					if( $rt == "true" ){
						return ['t'=>"B", "v"=>false];
					}else{
						return ['t'=>"T", "v"=>""];
					}
				}
			}
			if( $vs['v'] == "searchPattern" ){
				$rt = $this->s2_eeulav_teg( $inputs['p3']['v'] )['v'];
				$reg = $this->s2_eeulav_teg( $inputs['p2']['v'] )['v'];
				preg_match_all( $reg, $v['v'], $m );
				if( is_array($m) ){
					for($i=0;$i<sizeof($m);$i++){
						$mm = [];
						for($j=0;$j<sizeof($m[ $i ]);$j++){
							$mm[ $j ] = ['t'=>'T', 'v'=>$m[ $i ][ $j ]];
						}
						$m[ $i ] = ["t"=>"L", "v"=>$mm];
					}
				}else{
					$m = [];
				}
				return ['t'=>"L", "v"=>$m];
			}
			if( $vs['v'] == "isNumeric" ){
				if( preg_match("/^[0-9\.]+$/", $v['v']) ){
					return ['t'=>"B", "v"=>true];
				}else{
					return ['t'=>"B", "v"=>false];
				}
			}
			if( $vs['v'] == "subString" ){
				$i = $this->s2_eeulav_teg( $inputs['p2']['v'] )['v'];
				$s = $this->s2_eeulav_teg( $inputs['p3']['v'] )['v'];
				return ['t'=>"T", "v"=>substr($v['v'],$i,$s) ];
			}
			if( $vs['v'] == "append" ){
				for($i=2;$i<=5;$i++){
					if( $inputs['p'.$i]['v'] ){
						$v['v'] .= $this->s2_eeulav_teg( $inputs['p'.$i]['v'] )['v'];
					}
				}
				return ['t'=>"T", "v"=>$v['v'] ];
			}
			if( $vs['v'] == "prepend" ){
				$i = $this->s2_eeulav_teg( $inputs['p2']['v'] )['v'];
				return ['t'=>"T", "v"=>$i . $v['v'] ];
			}
			if( $vs['v'] == "length" ){
				return ['t'=>"N", "v"=>strlen($v['v']) ];
			}
			if( $vs['v'] == "clean" ){
				return ['t'=>"T", "v"=>preg_replace("/[\W]/", "",$v['v']) ];
			}
			if( $vs['v'] == "convertToNumber" ){
				$v['v'] = preg_replace("/[^0-9\.]+/", "",$v['v']);
				if( preg_match("/\./", $v['v'] ) ){
					return ['t'=>"N", "v"=>(float)$v['v']  ];
				}else{
					return ['t'=>"N", "v"=>(int)$v['v']  ];
				}
			}
			if( $vs['v'] == "split" ){
				$d = $this->s2_eeulav_teg( $inputs['p2']['v'] )['v'];
				$l = $this->s2_eeulav_teg( $inputs['p3']['v'] )['v'];
				if( !preg_match("/^\/(.*)\/$/", $d) ){
					$d = "/" . $d . "/";
				}
				$this->s2_ggggggggol[] = "regex: " . $d;
				$parts = preg_split( ($d??""),($v['v']??""),($l??-1) );
				for($i=0;$i<sizeof($parts);$i++){
					$parts[$i] = ['t'=>"T", 'v'=>$parts[$i]];
				}
				return ['t'=>"L", 'v'=>$parts ];
			}
			if( $vs['v'] == "replace" ){
				$f = $this->s2_eeulav_teg( $inputs['p2']['v'] )['v'];
				$r = $this->s2_eeulav_teg( $inputs['p3']['v'] )['v'];
				$v['v'] = str_replace( $f, $r, $v['v'] );
				return $v;
			}
			if( $vs['v'] == "validate" ){
				$r = $this->s2_eeulav_teg( $inputs['p2']['v'] )['v'];
				$min = $this->s2_eeulav_teg( $inputs['p3']['v'] )['v'];
				$max = $this->s2_eeulav_teg( $inputs['p4']['v'] )['v'];
				$_c = true;
				if( $r == "Email" ){
					@preg_match("/^[a-z0-9\.\-]{3,50}\@[a-z0-9\.\-]{3,50}\.[a-z\.]{2,6}$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "Mobile" ){
					@preg_match("/^[0-9]{10}$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "Phone" ){
					@preg_match("/^[0-9\+\-\ ]{10,16}$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "yyyy-mm-dd" ){
					@preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "Alpha" ){
					@preg_match("/^[a-z]+$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "Alpha with spaces" ){
					@preg_match("/^[a-z\ ]+$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "Alpha with spaces - . _" ){
					@preg_match("/^[a-z\ \-\.\_]+$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "AlphaNumeric" ){
					@preg_match("/^[a-z0-9]+$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "AlphaNumeric with spaces" ){
					@preg_match("/^[a-z0-9\ ]+$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "AlphaNumeric with space - . _" ){
					@preg_match( "/^[a-z0-9\ \.\-\_]+$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "Numeric" ){
					@preg_match( "/^[0-9\.]+$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "PAN" ){
					@preg_match( "/^[a-z]{5}[0-9]{4}[a-z]$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "CreditCard" ){
					@preg_match( "/^[0-9]{4}[\-\ ][0-9]{4}[\-\ ][0-9]{4}[\-\ ][0-9]{4}$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "Aadhaar" ){
					@preg_match( "/^[0-9]{4}[\-\ ]{0,1}[0-9]{4}[\-\ ]{0,1}[0-9]{4}$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "MongoDBId" ){
					@preg_match( "/^[a-f0-9]{24}$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}else if( $r == "HexCode" ){
					@preg_match( "/^[a-f0-9]+$/i", $v['v'], $m );
					if( $m ){ $_c = true; }else{ $_c = false; }
				}
				if( $_c ){
					if( strlen($v['v']) < $min || strlen($v['v']) > $max ){
						$_c = false;
					}
				}
				return ["t"=>"B", "v"=>$_c];
			}

		}else if( $v['t'] == "L" ){
			if( $vs['v'] == "get" ){
				return $v;
			}
			if( $vs['v'] == "set" ){
				$v['v'] = $this->s2_eeulav_teg($inputs['p2']['v']);;
				return ['t'=>"B", "v"=>true];
			}
			if( $vs['v'] == "setTemplate" ){
				//$v = $this->s2_eeulav_teg($inputs['p2']['v']);
				return ['t'=>"B", "v"=>true];
			}
			if( $vs['v'] == "getItem" ){
				$val = $this->s2_eeulav_teg($inputs['p2']['v']);
				return $v['v'][ $val['v'] ];
			}
			if( $vs['v'] == "setItem" ){
				$key = $this->s2_eeulav_teg($inputs['p2']['v']);
				$val = $this->s2_eeulav_teg($inputs['p3']['v']);
				$v['v'][ (int)$key['v'] ] = $val;
				return ['t'=>"B", "v"=>true];
			}
			if( $vs['v'] == "insert" ){
				$index = $this->s2_eeulav_teg($inputs['p2']['v']);
				$item = $this->s2_eeulav_teg($inputs['p3']['v']);
				array_splice($v['v'], (int)$index['v'], 0, [$item]);
				return ['t'=>"B", "v"=>true];
			}
			if( $vs['v'] == "remove" ){
				$index = $this->s2_eeulav_teg($inputs['p2']['v']);
				return ['t'=>"B", "v"=>array_splice($v['v'], (int)$index['v'], 1)];
			}
			if( $vs['v'] == "pop" ){
				return ['t'=>"B", "v"=>array_splice($v['v'], sizeof($v['v'])-1, 1 ) ];
			}
			if( $vs['v'] == "append" || $vs['v'] == "push" ){
				$val = $this->s2_eeulav_teg($inputs['p2']['v']);
				$v['v'][] = $val;
				return ['t'=>"B", "v"=>true];
			}
			if( $vs['v'] == "prepend" ){
				$val = $this->s2_eeulav_teg($inputs['p2']['v']);
				array_splice($v['v'],0,0,[$val]);
				return ['t'=>"B", "v"=>true];
			}
			if( $vs['v'] == "length" ){
				return ['t'=>"N", "v"=>sizeof($v['v'])];
			}
		}else if( $v['t'] == "O" ){
			if( $vs['v'] == "get" ){
				return $v;
			}
			if( $vs['v'] == "set" ){
				$v = $this->s2_eeulav_teg($inputs['p2']['v']);
				return ['t'=>"B", "v"=>true];
			}
			if( $vs['v'] == "getKey" ){
				$val = $this->s2_eeulav_teg($inputs['p2']['v']);
				if( !isset( $v['v'][ $val['v'] ] ) ){
					$this->s2_ggggggggol[] = "Key: " . $val['v'] . " not found";
					return ["t"=>"T", "v"=>""];
				}
				return $v['v'][ $val['v'] ];
			}
			if( $vs['v'] == "hasKey" ){
				$val = $this->s2_eeulav_teg($inputs['p2']['v']);
				if( isset($v['v'][ $val['v'] ]) ){
					return ['t'=>"B", "v"=>true ];
				}else{
					return ['t'=>"B", "v"=>false ];
				}
			}
			if( $vs['v'] == "getKeyList" ){
				$val = array_keys($v['v']);
				$this->s2_tcejbo_ot_tupni($val);
				return ['t'=>"L", "v"=>$val ];
			}
			if( $vs['v'] == "setKey" ){
				$key = $this->s2_eeulav_teg( $inputs['p2']['v'] );
				$item  = $this->s2_eeulav_teg( $inputs['p3']['v'] );
				$v['v'][ $key['v'] ] = $item;
				return ['t'=>"B", "v"=>true];
			}
			if( $vs['v'] == "removeKey" ){
				$key = $this->s2_eeulav_teg($inputs['p2']['v']);
				unset( $v['v'][ $key['v'] ] );
				return ['t'=>"B", "v"=>true];
			}
		}else if( $v['t'] == "B" ){
			if( $vs['v'] == "get" ){
				return $v;
			}
			if( $vs['v'] == "set" ){
				$v = $this->s2_eeulav_teg($inputs['p2']['v']);
				return $v;
			}
			if( $vs['v'] == "setTrue" ){
				$v['v'] = true;
				return $v;
			}
			if( $vs['v'] == "setFalse" ){
				$v['v'] = false;
				return $v;
			}
		}else if( $v['t'] == "D" ){
			if( $vs['v'] == "get" ){
				return $v;
			}
			if( $vs['v'] == "set" ){
				$v['v'] = $this->s2_eeulav_teg($inputs['p2']['v']);
				return $v;
			}
			if( $vs['v'] == "setValue" ){
				$y = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$m = $this->s2_eeulav_teg($inputs['p3']['v'])['v'];
				$d = $this->s2_eeulav_teg($inputs['p4']['v'])['v'];
				$v['v'] = date("Y-m-d", mktime(12,12,12,$m,$d,$y));
				return $v;
			}
			if( $vs['v'] == "getDate" ){
				return ['t'=>"N", date("D", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getMonth" ){
				return ['t'=>"N", date("m", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getYear" ){
				return ['t'=>"N", date("Y", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getMonthFull" ){
				return ['t'=>"T", date("M", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getMonthShort" ){
				return ['t'=>"T", date("F", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getDayFull" ){
				return ['t'=>"T", date("l", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getDayShort" ){
				return ['t'=>"T", date("D", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getDaysTill" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v']);
				$date1 = new DateTime($v['v']);
				$date2 = new DateTime($d['v']);
				$interval = $date1->diff($date2);
				return ['t'=>"N", "v"=>$interval->days ];
			}
			if( $vs['v'] == "getDaysUntil" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v']);
				$date2 = new DateTime($v['v']);
				$date1 = new DateTime($d['v']);
				$interval = $date1->diff($date2);
				return ['t'=>"N", "v"=>$interval->days ];
			}
			if( $vs['v'] == "getDaysTillToday" ){
				$date1 = new DateTime("now");
				$date2 = new DateTime($v['v']);
				$interval = $date1->diff($date2);
				return ['t'=>"N", "v"=>$interval->days ];
			}
			if( $vs['v'] == "getDaysUntilToday" ){
				$date2 = new DateTime("now");
				$date1 = new DateTime($v['v']);
				$interval = $date1->diff($date2);
				return ['t'=>"N", "v"=>$interval->days ];
			}
			if( $vs['v'] == "getFormat" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				if( $d == "dd/mm/yyyy" ){
					return ['t'=>"T", "v"=>date("d/m/Y", strtotime($v['v']) ) ];
				}else if( $d == "mm/dd/yyyy" ){
					return ['t'=>"T", "v"=>date("m/d/Y", strtotime($v['v']) ) ];
				}else if( $d == "dd-mm-yyyy" ){
					return ['t'=>"T", "v"=>date("d-m-Y", strtotime($v['v']) ) ];
				}else if( $d == "yyyy/mm/dd" ){
					return ['t'=>"T", "v"=>date("Y/m/d", strtotime($v['v']) ) ];
				}else if( $d == "yyyy/dd/mm" ){
					return ['t'=>"T", "v"=>date("Y/d/m", strtotime($v['v']) ) ];
				}else if( $d == "yyyy-mm-dd" ){
					return ['t'=>"T", "v"=>date("Y-m-d", strtotime($v['v']) ) ];
				}else if( $d == "yyyy-mm-dd" ){
					return ['t'=>"T", "v"=>date("Y-m-d", strtotime($v['v']) ) ];
				}else if( $d == "dd-mm-yyyy" ){
					return ['t'=>"T", "v"=>date("d-m-Y", strtotime($v['v']) ) ];
				}else if( $d == "dd-MM-yyyy" ){
					return ['t'=>"T", "v"=>date("d-F-Y", strtotime($v['v']) ) ];
				}else if( $d == "dd MM yyyy" ){
					return ['t'=>"T", "v"=>date("d F Y", strtotime($v['v']) ) ];
				}else if( $d == "yyyy MM dd" ){
					return ['t'=>"T", "v"=>date("Y F d", strtotime($v['v']) ) ];
				}else if( $d == "yyyy-MM-dd" ){
					return ['t'=>"T", "v"=>date("Y-F-d", strtotime($v['v']) ) ];
				}else if( $d == "dd-M-yyyy" ){
					return ['t'=>"T", "v"=>date("d-M-Y", strtotime($v['v']) ) ];
				}else if( $d == "dd M yyyy" ){
					return ['t'=>"T", "v"=>date("d M Y", strtotime($v['v']) ) ];
				}else if( $d == "yyyy M dd" ){
					return ['t'=>"T", "v"=>date("Y M d", strtotime($v['v']) ) ];
				}else if( $d == "yyyy-M-dd" ){
					return ['t'=>"T", "v"=>date("Y-M-d", strtotime($v['v']) ) ];
				}else if( $d == "dd DD MM yyyy" ){
					return ['t'=>"T", "v"=>date("d D F Y", strtotime($v['v']) ) ];
				}else if( $d == "yyyy DD dd MM" ){
					return ['t'=>"T", "v"=>date("Y d D F", strtotime($v['v']) ) ];
				}else{
					return ['t'=>"T", "v"=>$v['v']];
				}
			}
			if( $vs['v'] == "addDays" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d", strtotime($v['v'])+ (86400*$d) );
				return $v;
			}
			if( $vs['v'] == "addMonths" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d", strtotime($v['v'])+ (86400*30*$d) );
				return $v;
			}
			if( $vs['v'] == "addYears" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d", strtotime($v['v'])+ (86400*365*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractDays" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d", strtotime($v['v'])- (86400*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractMonths" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d", strtotime($v['v'])- (86400*30*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractYears" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d", strtotime($v['v'])- (86400*365*$d) );
				return $v;
			}
		}else if( $v['t'] == "DT" ){
			if( $vs['v'] == "get" ){
				return $v;
			}
			if( $vs['v'] == "set" ){
				$v['v'] = $this->s2_eeulav_teg($inputs['p2']['v']);
				return $v;
			}
			if( $vs['v'] == "setValue" ){
				$y =  $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$m =  $this->s2_eeulav_teg($inputs['p3']['v'])['v'];
				$d =  $this->s2_eeulav_teg($inputs['p4']['v'])['v'];
				$h =  $this->s2_eeulav_teg($inputs['p5']['v'])['v'];
				$i =  $this->s2_eeulav_teg($inputs['p6']['v'])['v'];
				$s =  $this->s2_eeulav_teg($inputs['p7']['v'])['v'];
				$tz = $this->s2_eeulav_teg($inputs['p8']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", mktime(12,12,12,$m,$d,$y));
				$v['tz'] = $tz;
				return $v;
			}
			if( $vs['v'] == "getDate" ){
				return ['t'=>"N", date("D", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getMonth" ){
				return ['t'=>"N", date("m", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getYear" ){
				return ['t'=>"N", date("Y", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getMonthFull" ){
				return ['t'=>"T", date("M", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getMonthShort" ){
				return ['t'=>"T", date("F", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getDayFull" ){
				return ['t'=>"T", date("l", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getDayShort" ){
				return ['t'=>"T", date("D", strtotime($v['v'])) ];
			}
			if( $vs['v'] == "getTimeZone" ){
				return ['t'=>"T", "v"=>$v['tz'] ];
			}
			if( $vs['v'] == "setTimeZone" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v']);
				$v['ts'] = $d['v'];
				return $v;
			}
			if( $vs['v'] == "addDays" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v'])+ (86400*$d) );
				return $v;
			}
			if( $vs['v'] == "addMonths" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v'])+ (86400*30*$d) );
				return $v;
			}
			if( $vs['v'] == "addYears" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v'])+ (86400*365*$d) );
				return $v;
			}
			if( $vs['v'] == "addHours" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v'])+ (60*60*$d) );
				return $v;
			}
			if( $vs['v'] == "addMinutes" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v'])+ (60*$d) );
				return $v;
			}
			if( $vs['v'] == "addSeconds" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v'])+ ($d) );
				return $v;
			}
			if( $vs['v'] == "subtractDays" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v'])- (86400*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractMonths" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v'])- (86400*30*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractYears" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v'])- (86400*365*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractHours" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v']) - (60*60*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractMinutes" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v']) - (60*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractSeconds" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", strtotime($v['v']) - ($d) );
				return $v;
			}
		}else if( $v['t'] == "TS" ){
			if( $vs['v'] == "get" ){
				return $v;
			}
			if( $vs['v'] == "set" ){
				$v['v'] = $this->s2_eeulav_teg($inputs['p2']['v']);
				return $v;
			}
			if( $vs['v'] == "setValue" ){
				$y =  $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$m =  $this->s2_eeulav_teg($inputs['p3']['v'])['v'];
				$d =  $this->s2_eeulav_teg($inputs['p4']['v'])['v'];
				$h =  $this->s2_eeulav_teg($inputs['p5']['v'])['v'];
				$i =  $this->s2_eeulav_teg($inputs['p6']['v'])['v'];
				$s =  $this->s2_eeulav_teg($inputs['p7']['v'])['v'];
				$tz = $this->s2_eeulav_teg($inputs['p8']['v'])['v'];
				$v['v'] = mktime(12,12,12,$m,$d,$y);
				$v['tz'] = $tz;
				return $v;
			}
			if( $vs['v'] == "getDate" ){
				return ['t'=>"N", date("D", (int)$v['v']) ];
			}
			if( $vs['v'] == "getMonth" ){
				return ['t'=>"N", date("m", (int)$v['v']) ];
			}
			if( $vs['v'] == "getYear" ){
				return ['t'=>"N", date("Y", (int)$v['v']) ];
			}
			if( $vs['v'] == "getMonthFull" ){
				return ['t'=>"T", date("M", (int)$v['v']) ];
			}
			if( $vs['v'] == "getMonthShort" ){
				return ['t'=>"T", date("F", (int)$v['v']) ];
			}
			if( $vs['v'] == "getDayFull" ){
				return ['t'=>"T", date("l", (int)$v['v']) ];
			}
			if( $vs['v'] == "getDayShort" ){
				return ['t'=>"T", date("D", (int)$v['v']) ];
			}
			if( $vs['v'] == "getTimeZone" ){
				return ['t'=>"T", "v"=>$v['tz'] ];
			}
			if( $vs['v'] == "setTimeZone" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v']);
				$v['ts'] = $d['v'];
				return $v;
			}
			if( $vs['v'] == "addDays" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v']+ (86400*$d) );
				return $v;
			}
			if( $vs['v'] == "addMonths" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v']+ (86400*30*$d) );
				return $v;
			}
			if( $vs['v'] == "addYears" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v']+ (86400*365*$d) );
				return $v;
			}
			if( $vs['v'] == "addHours" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v']+ (60*60*$d) );
				return $v;
			}
			if( $vs['v'] == "addMinutes" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v']+ (60*$d) );
				return $v;
			}
			if( $vs['v'] == "addSeconds" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v']+ ($d) );
				return $v;
			}
			if( $vs['v'] == "subtractDays" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v']- (86400*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractMonths" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v']- (86400*30*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractYears" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v']- (86400*365*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractHours" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v'] - (60*60*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractMinutes" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v'] - (60*$d) );
				return $v;
			}
			if( $vs['v'] == "subtractSeconds" ){
				$d = $this->s2_eeulav_teg($inputs['p2']['v'])['v'];
				$v['v'] = date("Y-m-d H:i:s", (int)$v['v'] - ($d) );
				return $v;
			}
		}else if( $v['t'] == "MongoQ" ){
			if( $vs['v'] == "addCondition" ){
				// print_r( $this->s2_tttttluser );
				$v['v'][] = [
					'f'=>$this->s2_eeulav_teg($inputs['p2']['v']),
					'c'=>$this->s2_eeulav_teg($inputs['p3']['v']),
					'v'=>$this->s2_eeulav_teg($inputs['p4']['v']),
				];
				return $v;
				// print_r( $this->s2_tttttluser );
				// exit;
				//exit;
			}

		}else if( $v['t'] == "MysqlQ" ){
			if( $vs['v'] == "addCondition" ){
				$v['v'][] = [
					'f'=>$this->s2_eeulav_teg($inputs['p2']['v']),
					'c'=>$this->s2_eeulav_teg($inputs['p3']['v']),
					'v'=>$this->s2_eeulav_teg($inputs['p4']['v']),
				];
				return $v;
			}
		}
		return $rt;
	}
	function s2_hhhhtam_od( $s2_sssssssshr ){
		$v = 0;
		$op = "+";
		foreach( $s2_sssssssshr as $i=>$j ){
			if( $op == "+" ){
				$v = $v + $this->s2_bus_htam_od( $j['m'] );
			}else if( $op == "-" ){
				$v = $v - $this->s2_bus_htam_od( $j['m'] );
			}else if( $op == "/" ){
				$v = $v / $this->s2_bus_htam_od( $j['m'] );
			}else if( $op == "*" ){
				$v = $v * $this->s2_bus_htam_od( $j['m'] );
			}else if( $op == "%" ){
				$v = $v % $this->s2_bus_htam_od( $j['m'] );
			}else if( $op == "^" ){
				$v = $v ^ $this->s2_bus_htam_od( $j['m'] );
			}
			$op = $j['OP'];
			//echo $v . ": " . $op . " : \n";
			if( $op == "." ){break;}
		}
		return $v;
	}
	function s2_bus_htam_od( $s2_sssssssshr ){
		$v = 0;
		$op = "+";
		foreach( $s2_sssssssshr as $i=>$j ){
			$vv = $this->s2_eeulav_teg($j);
			if( $vv['t'] != "N" ){
				$this->s2_ggggggggol[] = "Warning: Math: non numeric operand: " . ($j['v'] == "V"?$j['v']['t'].":".$j['v']['v']:$j['t'].":".$j['v']);
			}
			$vv['v'] = $this->s2_rebmun_ot_gnirts($vv['v']);
			//echo $vv['v'] . " " . $op . " \n";
			if( $op == "+" ){
				$v = $v + $vv['v'];
			}else if( $op == "-" ){
				$v = $v - $vv['v'];
			}else if( $op == "/" ){
				$v = $v / $vv['v'];
			}else if( $op == "*" ){
				$v = $v * $vv['v'];
			}else if( $op == "%" ){
				$v = $v % $vv['v'];
			}else if( $op == "^" ){
				$v = $v ^ $vv['v'];
			}
			$op = $j['OP'];
			if( $op == "." ){break;}
		}
		//echo "ret: " . $v . ": \n";
		return $v;
	}
	function s2_rebmun_ot_gnirts($v){
		if( gettype($v) == "string" ){
			if( is_numeric($v) ){
				if( preg_match("/\./",$v) ){
					return (float)$v;
				}else{
					return (int)$v;
				}
			}else{
				$this->s2_ggggggggol[] = "Numeric expected: ". $v;
				return 0;
			}
		}else if( gettype($v) == "integer" || gettype($v) == "float" || gettype($v) == "double" ){
			return $v;
		}else{
			$this->s2_ggggggggol[] = "Numeric expected: ". gettype($v) . ": ". $v;
			return 0;
		}
	}
	function s2_tcejbo_ot_tupni( &$d ){
		$debug = true;
		if( array_keys($d)[0] === 0 ){
			for($i=0;$i<sizeof($d);$i++){
				$j = $d[$i];
				if( gettype($j) == "array" ){
					$this->s2_tcejbo_ot_tupni($j);
					if( array_keys($j)[0] === 0 ){
						$d[ $i ] = ["t"=>"L", "v"=>$j];
					}else{
						$d[ $i ] = ["t"=>"O", "v"=>$j];
					}
				}else if( gettype($j) == "string" ){
					$d[ $i ] = ["t"=>"T", "v"=>$j];
				}else if( gettype($j) == "double" || gettype($j) == "float" || gettype($j) == "integer" ){
					$d[ $i ] = ["t"=>"N", "v"=>$j];
				}else if( gettype($j) == "boolean" ){
					$d[ $i ] = ["t"=>"B", "v"=>$j];
				}else if( gettype($j) == "NULL" ){
					$d[ $i ] = ["t"=>"NL", "v"=>null];
				}
			}
		}else{
			foreach( $d as $i=>$j ){
				if( gettype($j) == "array" ){
					$this->s2_tcejbo_ot_tupni($j);
					if( array_keys($j)[0] === 0 ){
						$d[ $i ] = ["t"=>"L", "v"=>$j];
					}else{
						$d[ $i ] = ["t"=>"O", "v"=>$j];
					}
				}else if( gettype($j) == "string" ){
					$d[ $i ] = ["t"=>"T", "v"=>$j];
				}else if( gettype($j) == "double" || gettype($j) == "float" || gettype($j) == "integer" ){
					$d[ $i ] = ["t"=>"N", "v"=>$j];
				}else if( gettype($j) == "boolean" ){
					$d[ $i ] = ["t"=>"B", "v"=>$j];
				}else if( gettype($j) == "NULL" ){
					$d[ $i ] = ["t"=>"NL", "v"=>null];
				}
			}
		}
		//return $d;
	}
	function s2_yarra_ot_atad_cigol_trevnoc( $d ){
		foreach( $d as $i=>$j ){
			if( preg_match("/(\.|\-\>)/", $i) ){
				$x = explode(".",$i);
				if( sizeof($x) == 1 ){
					$d[ $x[0] ] = $j;
				}else if( sizeof($x) == 2 ){
					$d[ $x[0] ][ $x[1] ] = $j;
				}else if( sizeof($x) == 3 ){
					$d[ $x[0] ][ $x[1] ][ $x[2] ] = $j;
				}else if( sizeof($x) == 4 ){
					$d[ $x[0] ][ $x[1] ][ $x[2] ][ $x[4] ] = $j;
				}else if( sizeof($x) == 5 ){
					$d[ $x[0] ][ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ] = $j;
				}else if( sizeof($x) == 6 ){
					$d[ $x[0] ][ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ][ $x[6] ] = $j;
				}else if( sizeof($x) == 7 ){
					$d[ $x[0] ][ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ][ $x[6] ][ $x[7] ] = $j;
				}else if( sizeof($x) == 8 ){
					$d[ $x[0] ][ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ][ $x[6] ][ $x[7] ][ $x[8] ] = $j;
				}
				unset($d[$i]);			
			}
		}
		return $d;
	}
	
	function s2_tluser_tes( $key, $v ){
		if( is_object($v) ){
			return 0;
		}else if( is_string($key) ){
			$var = $key;
		}else if( is_array($key) && isset($key['v']) && isset($key['t']) ){
			if( is_object($key['v']) ){
				return 0;
			}
			if( $key['t'] != "V" ){
				$this->s2_ggggggggol[] = "ERROR: set_result: incorrect key.: " . print_r($key,true);
				return false;
			}
			$var = $key['v']['v'];
		}else{
			$this->s2_ggggggggol[] = "ERROR: set_result: incorrect key..: " . print_r($key,true);
			return 0;
		}
		if( !isset($v) ){
			$this->s2_ggggggggol[] = "Input Missing";
		}else{
			if( !is_array($v) ){
				$this->s2_ggggggggol[] = "Warning: " . $var . " Invalid Assignment";
				$v = ["t"=>"N","v"=>0];
			}else if( $v['t'] == "N" ){
				if( $v['v'] != "" ){
					if( is_float($v['v']) && ( is_infinite($v['v']) || is_nan($v['v']) ) ){
						$this->s2_ggggggggol[] = "Warning: " . $var . " = Infinate/Nan: 0";
						$v = ["t"=>"N","v"=>0];
					}else if( is_float($v['v']) ){
						$v['v'] = (float)$v['v'];
					}else{
						//echo gettype($v['v']); echo "yes";
						$v['v'] = (int)$v['v'];
					}
				}else{
					$v['v'] = 0;
				}
			}
			if( gettype($v['t']) == "BIN" ){
				$this->s2_ggggggggol[] = "Set: " . $var . " = BinaryData";
			}else if( gettype($v['v']) == "array" ){
				$this->s2_ggggggggol[] = "Set: " . $var  . " = ";
				$this->s2_ggggggggol[] = $v['v'];
			}else if( gettype($v['v']) == "object" ){
				$this->s2_ggggggggol[] = "Set: " . $var  . " = Object ";
			}else if( gettype($v['v']) == "string" || $v['t'] == "T" ){
				if( $this->isBinary($v['v']) ){
					$this->s2_ggggggggol[] = "Set: " . $var . " = BinaryData in " . $v['t'];
				}else{
					$this->s2_ggggggggol[] = "Set: " . $var . " = " . substr($v['v'],0,200) . (strlen($v['v'])>200?"...":"" );
				}
			}else{
				$this->s2_ggggggggol[] = "Set: " . $var . " = " . $v['v'];
			}
			$x = explode("->",$var);
			$k = $this->s2_2tluser_tes( $x, $this->s2_tttttluser, $v );
			if( !$k ){ $this->s2_ggggggggol[] = "Set: Fail "; }
		}
	}
	function s2_2tluser_tes( $x, &$r, $v ){
		$key = $x[0];
		if( isset($r[ $key ]) ){
			if( sizeof($x) > 1 ){
				if( $r[ $key ]['t'] == "O" ){
					array_splice($x,0,1);
					return $this->s2_2tluser_tes($x, $r[ $key ]['v'], $v);
				}else{
					return false;
				}
			}else{
				$r[ $key ] = $v;
				//$r[ $key ]['t'] = $v['t'];
				return true;
			}
		}else{ 
			$r[ $key ] = $v;
			return true;
		}
	}
	function s2_noisses_tes( $i, $v ){
		if( $i ){
			if( is_infinite($v) || is_nan($v) ){
				$this->s2_ggggggggol[] = "Assign: " . $i  . " = Infinate/Nan: 0";
				$v = 0;
			}else{
				if( gettype($v) == "string" ){
					$v = preg_replace('/[\x00-\x1F\x7F-\xFF]/', ' ', $v);  // rpelace all non printable chars
				}
				if( gettype($v) == "array" || gettype($v) == "object" ){
					$this->s2_ggggggggol[] = "Set: " . $i  . " = ";
					$this->s2_ggggggggol[] = $v;
				}else if( gettype($v) == "string" ){
					$this->s2_ggggggggol[] = "Set: " . $i  . " = " . substr($v,0,500) . (strlen($v)>500?"...":"" );
				}else{
					$this->s2_ggggggggol[] = "Set: " . $i  . " = " . $v;
				}
			}
			$x = explode("->",$i);
			if( sizeof($x) == 2 ){ //http.set_session_value
				$_SESSION[ $x[1] ] = $v;
			}else if( sizeof($x) == 3 ){
				$_SESSION[ $x[1] ][ $x[2] ] = $v;
			}else if( sizeof($x) == 4 ){
				$_SESSION[ $x[1] ][ $x[2] ][ $x[4] ] = $v;
			}else if( sizeof($x) == 5 ){
				$_SESSION[ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ] = $v;
			}else if( sizeof($x) == 6 ){
				$_SESSION[ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ][ $x[6] ] = $v;
			}else if( sizeof($x) == 7 ){
				$_SESSION[ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ][ $x[6] ][ $x[7] ] = $v;
			}else if( sizeof($x) == 8 ){
				$_SESSION[ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ][ $x[6] ][ $x[7] ][ $x[8] ] = $v;
			}
		}
		return $v;
	}
	function s2_tluser_tesnu( $i ){
		if( $i ){
			$x = explode("->",$i);
			if( sizeof($x) == 1 ){
				unset( $this->s2_tttttluser[ $x[0] ] );
			}else if( sizeof($x) == 2 ){
				unset( $this->s2_tttttluser[ $x[0] ][ $x[1] ] );
			}else if( sizeof($x) == 3 ){
				unset( $this->s2_tttttluser[ $x[0] ][ $x[1] ][ $x[2] ] );
			}else if( sizeof($x) == 4 ){
				unset( $this->s2_tttttluser[ $x[0] ][ $x[1] ][ $x[2] ][ $x[4] ] );
			}else if( sizeof($x) == 5 ){
				unset( $this->s2_tttttluser[ $x[0] ][ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ] );
			}else if( sizeof($x) == 6 ){
				unset( $this->s2_tttttluser[ $x[0] ][ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ][ $x[6] ] );
			}else if( sizeof($x) == 7 ){
				unset( $this->s2_tttttluser[ $x[0] ][ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ][ $x[6] ][ $x[7] ] );
			}else if( sizeof($x) == 8 ){
				unset( $this->s2_tttttluser[ $x[0] ][ $x[1] ][ $x[2] ][ $x[4] ][ $x[5] ][ $x[6] ][ $x[7] ][ $x[8] ] );
			}
		}
		return $v;
	}
	function s2_eeulav_tes( &$k ){
		//print_pre( $k );exit;
		$var = $k['v'];
		$x = explode("->",$var);
		$v = $this->s2_2eulav_tes( $x, $this->s2_tttttluser, $k );
	}
	function s2_2eulav_tes( $x, &$r, &$k ){
		//echo "set value2 ";
		//print_pre( $x );
		// print_pre( $r );
		$key = $x[0];
		if( isset($r[ $key ]) ){
			if( sizeof($x) > 1 ){
				array_splice($x,0,1);
				if( $x[0] == "[]" ){
					array_splice($x,0,1);
					$index = array_splice($x,0,1)[0];
					if( $r[ $key ]['t'] == "L" ){
						if( isset($r[ $key ]['v'][ (int)$index ]) ){
							if( sizeof($x) > 0 ){
								return $this->s2_2eulav_tes( $x, $r[ $key ]['v'][ (int)$index ]['v'], $k );
							}
						}
					}
				}else{
					if( $r[ $key ]['t'] == "O" ){
						$this->s2_2eulav_tes( $x, $r[ $key ]['v'], $k);
					}
				}
			}else{
				// echo "inline function";
				// print_r( $r[ $key ] );
				// print_r( $k['vs'] );
				if( isset($k['vs']['v']) && $k['vs']['v'] ){
					$this->s2_noitcnuf_enilni_od( $r[ $key ], $k['vs'] );
				}
			}
		}
	}
	function s2_eeulav_teg( $s2_iiiiiiiiii ){
		$s2_ggggggubed = false;
		if( $s2_ggggggubed ){
			echo "s2__eulav_teg\n";
		}
		if( !is_array($s2_iiiiiiiiii) ){
			if( $s2_ggggggubed ){ echo $s2_iiiiiiiiii . "\n"; }
			$v = ['t'=>"T", "v"=>""];
			if( $s2_iiiiiiiiii ){
				$x = explode("->",$s2_iiiiiiiiii);
				$v = $this->s2_2eulav_teg( $x, $this->s2_tttttluser );
			}
			if( !isset($v['t']) || !isset($v['v']) ){
				$this->s2_ggggggggol[] = "Error: Variable " . $s2_iiiiiiiiii . " Invalid value";
				$this->s2_ggggggggol[] = $v;
				if( $s2_ggggggubed ){ echo "Error: Variable " . $s2_iiiiiiiiii . " Invalid value". "\n"; print_r( $v ); }
				$v = ['t'=>"T", "v"=>""];
			}
			if( $s2_ggggggubed ){ echo "return1: "; print_r($v); }
			return $v;
		}else if( $s2_iiiiiiiiii['t'] && isset($s2_iiiiiiiiii['v']) ){
			if( $s2_ggggggubed ){ print_r($s2_iiiiiiiiii); }
			//echo "get value\n" ; print_pre( $s2_iiiiiiiiii );
			if( $s2_iiiiiiiiii['t']=="V" ){
				$val = $this->s2_eeulav_teg( $s2_iiiiiiiiii['v']['v'] );
				//print_r( $val );
				if( isset( $s2_iiiiiiiiii['v']['vs']['v'] ) ){
					//print_r( $s2_iiiiiiiiii['v']['vs']['v'] );
					if( trim($s2_iiiiiiiiii['v']['vs']['v']) != "" ){
						$newval = $this->s2_noitcnuf_enilni_od( $val, $s2_iiiiiiiiii['v']['vs'], $s2_iiiiiiiiii['v']['v'] );
						if( $s2_ggggggubed ){ echo "return3: "; print_r($newval); }
						return $newval;
					}
				}
				if( $s2_ggggggubed ){ echo "return4: "; print_r($val); }
				return $val;
			}else{
				$val = $s2_iiiiiiiiii['v'];
				if( $s2_iiiiiiiiii['t'] == "N" && gettype($val) == "string" ){
					if( preg_match("/\./", $val) ){ $val = (float)$val; }else{ $val = (int)$val; }
				}
				if( $s2_iiiiiiiiii['t']=="B" && ( $val === "true" || $val === 1 || $val === true ) ){ $val = true; }
				if( $s2_iiiiiiiiii['t']=="B" && ( $val === "false" || $val === 0 || $val === false ) ){ $val = false; }
				if( $s2_ggggggubed ){ echo "return5: "; print_r(["t"=>$s2_iiiiiiiiii['t'], "v"=>$val]); }
				return ["t"=>$s2_iiiiiiiiii['t'], "v"=>$val];
			}
		}else{
			$this->s2_ggggggggol[] = "ERROR: get_value: incorrect: ";
			$v = ['t'=>"T", "v"=>""];
			return $v;
		}
	}
	function s2_2eulav_teg( $x, &$r ){
		$s2_ggggggubed = false;
		if( $s2_ggggggubed ){
			echo "s2__2eulav_teg\n";
		}
		$key = $x[0];
		if( $key == "[]" ){ $key = 0; }
		if( isset($r[ $key ]) ){
			if( $s2_ggggggubed ){ echo $key . ": found\n"; }
			if( sizeof($x) > 1 ){
				array_splice($x,0,1);
				if( $r[ $key ]['t'] == "O" ){
					return $this->s2_2eulav_teg($x, $r[ $key ]['v']);
				}else if( $r[ $key ]['t'] == "L" ){
					return $this->s2_2eulav_teg($x, $r[ $key ]['v']);
				}else{
					return false;
				}
			}else{
				if( $s2_ggggggubed ){ print_r( $r[ $key ]); }
				return $r[ $key ];
			}
		}else{
			if( $s2_ggggggubed ){ echo "not found\n"; }
			return false;
		}
	}
	function s2_ttttttessi( $s2_iiiiiiiiii ){
		//echo "isset: " . $s2_iiiiiiiiii; 
		$s2_vvvvvvvvvv = "";
		if( is_array($s2_iiiiiiiiii) ){
			if( $s2_iiiiiiiiii['t'] && isset($s2_iiiiiiiiii['v']) ){
				if( $s2_iiiiiiiiii['t'] == "V" ){
					$s2_vvvvvvvvvv = $s2_iiiiiiiiii['v'];
				}else{
					return true;
				}
			}else{
				return false;
			}
		}else if( is_string($s2_iiiiiiiiii) ){
			$s2_vvvvvvvvvv = $s2_iiiiiiiiii;
		}
		if( $s2_vvvvvvvvvv ){
			//print_pre( $this->s2_tttttluser );
			$x = explode("->", $s2_vvvvvvvvvv);
			return $this->s2_22222tessi( $x, $this->s2_tttttluser );
		}
		return false;
	}
	function s2_22222tessi( $x, $r ){
		//echo "isset2: "; print_r( $x ); print_r( $r );
		$key = $x[0];
		if( isset($r[ $key ]) ){
			//echo $key . " exists \n";
			if( sizeof($x) > 1 ){
				array_splice($x,0,1);
				//print_r( $x );
				if( $x[0] == "[]" ){
					array_splice($x,0,1);
					//print_r( $x );
					$index = array_splice($x,0,1)[0];
					if( $r[ $key ]['t'] == "L" ){
						//echo "ys" . $index;
						//print_r( $r[ $key ]['v'] );
						if( isset($r[ $key ]['v'][ (int)$index ]) ){
							//echo "xxx";
							if( sizeof($x) > 0 ){
								//echo "xxx";
								return $this->s2_22222tessi( $x, $r[ $key ]['v'][ (int)$index ]['v'] );
							}else{
								return true;
							}
						}else{
							return false;
						}
					}else{
						return false;
					}
				}
				if( $r[ $key ]['t'] == "O" ){
					return $this->s2_22222tessi($x, $r[ $key ]['v']);
				}else{
					return false;
				}
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
	function s2_rorre_dnopser( $error ){
		if( isset($this->s2_ssssnoitpo['raw_output']) ){
			return ['status'=>"fail","error"=>$error];
		}else{
			$this->s2_eeesnopser['statusCode'] = 500;
			$this->s2_eeesnopser['body'] = [$error];
			return $this->s2_eeesnopser;
		}
	}
	function s2_noitucexe_dne( $http_code = 500, $data = "Something wrong", $headers = [] ){
		$this->s2_eeesnopser['statusCode'] = $http_code;
		if( is_array($data ) ){
			$this->s2_eeesnopser['headers']['content-type']="applicatin/json";
			$this->s2_eeesnopser['body'] = $data;
		}else{
			$this->s2_eeesnopser['headers']['content-type']="text/html";
			$this->s2_eeesnopser['body'] = [$data];
		}
		$this->s2_noitucexe_dnev = true;
	}
	function s2_tluser_naelc($v){
		foreach($v as $i=>$j ){
			if( gettype($j) == "array" ){
				$v[$i] = $this->s2_tluser_naelc($j);
			}else if( gettype($j) == "float" || gettype($j) == "double" ){
				if( is_infinite($j) ){
					$v[$i] = "NULL";
				}
			}else if( is_nan($j) ){
				$v[$i] = "NULL";
			} 
		}
		//print_pre( $v );
		return $v;
	}
	function s2_dnepool_txen_dnif( $s2_iiiiegatsf ){
		$n = 0;
		for($i=$s2_iiiiegatsf+1;$i<sizeof($this->s2_eeeeenigne["engine"]['stages']);$i++){
			if( $this->s2_eeeeenigne["engine"]['stages'][ $i ]['type'] == "EndWhile" ||  $this->s2_eeeeenigne["engine"]['stages'][ $i ]['type'] == "EndFor" || $this->s2_eeeeenigne["engine"]['stages'][ $i ]['type'] == "EndForEach" ){
				return $i;
			}
		}
		return $s2_iiiiegatsf+1;
	}
	function s2_dnar_txen_dnif( $s2_iiiiegatsf ){
		$lastif = -1;
		$n = 0;
		$vrand = $this->s2_eeeeenigne["engine"]['stages'][ $s2_iiiiegatsf ]['vrand'];
		for($i=$s2_iiiiegatsf+1;$i<sizeof($this->s2_eeeeenigne["engine"]['stages']);$i++){
			if( $this->s2_eeeeenigne["engine"]['stages'][ $i ]['vrand'] == $vrand ){
				$lastif = $i;
				break;
			}
		}
		return $lastif;
	}
	function s2_dnar_verp_dnif( $s2_iiiiegatsf ){
		$lastif = -1;
		$n = 0;
		$vrand = $this->s2_eeeeenigne["engine"]['stages'][ $s2_iiiiegatsf ]['vrand'];
		for($i=$s2_iiiiegatsf-1;$i>-1;$i--){
			//print_pre($this->s2_eeeeenigne["engine"]['stages']);
			if( $this->s2_eeeeenigne["engine"]['stages'][ $i ]['vrand'] == $vrand ){
				$lastif = $i;
				break;
			}
		}
		//echo "lastif = ".$lastif. "<br>";
		return $lastif;
	}
	function s2_stuptuo_pam( $value, $template ){
		if( 1==15 ){
			print_pre($value);
			print_pre($template);
			exit;
		}
		$outputs = [];
		foreach( $template as $i=>$j ){
			//echo "<div>" . $i . ": " . $j['name'] . "</div>";
			if( $value[ $i ] || is_numeric($value[$i]) ){
				if( $j["value"] ){
					$outputs[ $j["value"] ] = $value[$i];
				}else if( $j["sub"] ){
					$o = $this->s2_stuptuo_pam( $value[$i], $j["sub"] );
					foreach( $o as $ii=>$jj ){
						$outputs[ $ii ] = $jj;
					}
				}
			}
		}
		return $outputs;
	}

	function s2_eman_tupni_teg( $v, $enclose = true ){
		if( $enclose ){
			return "[".$v."]";
		}else{
			return $v;
		}
	}

	function s2_yarra_ot_etalpmet_eulav_yek( $v ){
		$vv = [];
		if( is_array($v) ){
			if( array_keys($v)[0] === 0 ){
				for($i=0;$i<sizeof($v);$i++){
					$vv[ $v[$i]['k']['v'] ] = $this->s2_eulav_erup_teg( $v[$i]['v'] );
				}
			}
		}
		return $vv;
	}

	function s2_yarra_ot_etalpmet( $v ){
		// template to array except binary
		$debug = false;
		if( $debug ){
			echo "template to array\n";
			print_pre( $v );
		}
		if( is_array($v) ){
			if( array_keys($v)[0] === 0 ){
				for($i=0;$i<sizeof($v);$i++){
					$j = $v[ $i ];
					if( gettype($j) == "array" ){
						if( $j['t'] == "V" ){
							$j = $this->s2_eeulav_teg( $j );
						}
						if( $j['t'] == "BIN" ){
							$j['v'] = "BinaryData";
						}else if( gettype($j['v']) == "string" ){
							if( $this->isBinary($j['v']) ){
								$j['v'] = "Binary Stripped";
							}
						}
						if( $j['t'] == "O" || $j['t'] == "L" ){
							$v[ $i ] = $this->s2_yarra_ot_etalpmet( $j['v'] );
						}else if( $j['t'] == "N" ){
							if( gettype($j['v']) == "string" ){
								if( preg_match("/\./", $j['v']) ){
									$v[ $i ] = (float)$j['v'];
								}else{
									$v[ $i ] = (int)$j['v'];
								}
							}else{
								$v[ $i ] = $j['v'];
							}
						}else if( $j['t'] == "DT" ){
							$v[ $i ] = $j['v']['v'] . " " . $j['v']['tz'];
						}else if( $j['t'] == "B" ){
							$v[ $i ] = ((!$j['v']||$j['v']==="false"||$j['v']===false||$j['v']===0)?false:true);
						}else if( $j['t'] == "NL" ){
							$v[ $i ] = null;
						}else if( $j['t'] == "TS" ){
							$v[ $i ] = $j['v']['v'];
						}else{
							$v[ $i ] = $j['v'];
						}
					}else{
						$this->s2_ggggggggol[] = "ERROR: template_to_array: incorrect item: " . $j; 
					}
				}
			}else{
				foreach( $v as $i=>$j ){
					//echo "Each key: " . $i . "\n";
					if( gettype( $j ) == "array" ){
						if( $j['t'] == "V" ){
							$j = $this->s2_eeulav_teg( $j );
							//print_pre($j);
						}
						if( $j['t'] == "BIN" ){
							$j['v'] = "BinaryData";
						}else if( gettype($j['v']) == "string" ){
							if( $this->isBinary($j['v']) ){
								$j['v'] = "Binary Stripped";
							}
						}
						if( $j['t'] == "O" || $j['t'] == "L" ){
							$v[ $i ] = $this->s2_yarra_ot_etalpmet( $j['v'] );
						}else if( $j['t'] == "N" ){
							if( gettype($j['v']) == "string" ){
								if( preg_match("/\./", $j['v']) ){
									$v[ $i ] = (float)$j['v'];
								}else{
									$v[ $i ] = (int)$j['v'];
								}
							}else{
								$v[ $i ] = $j['v'];
							}
						}else if( $j['t'] == "B" ){
							$v[ $i ] = ((!$j['v']||$j['v']==="false"||$j['v']===false||$j['v']===0)?false:true);
						}else if( $j['t'] == "DT" ){
							$v[ $i ] = $j['v']['v'] . " " . $j['v']['tz'];
						}else if( $j['t'] == "NL" ){
							$v[ $i ] = null;
						}else if( $j['t'] == "TS" ){
							$v[ $i ] = $j['v']['v'];
						}else{
							$v[ $i ] = $j['v'];
						}
					}else{
						$this->s2_ggggggggol[] = "Error: unhandled parts " .$j;
						//echo "Unhandled parts";
						//print_pre( $j );
						//$v[ $i ] = ['t'=>"T", "v"=>$j . "(Unhandled)"];
						//$v[ $i ] = $j;
					}
				}
			}
		}else{
			$this->s2_ggggggggol[] = "template to array: " . gettype($v);
		}
		// echo "template to array returning...\n";
		if( $debug ){print_pre( $v );}
		return $v;
	}
	function s2_erup_yarra_ot_etalpmet( $v ){
		// template array with pure values, including binary
		$debug = false;
		if( $debug ){
			echo "template to array\n";
			print_pre( $v );
		}
		if( is_array($v) ){
			if( array_keys($v)[0] === 0 ){
				for($i=0;$i<sizeof($v);$i++){
					$j = $v[ $i ];
					if( gettype($j) == "array" ){
						if( $j['t'] == "V" ){
							$j = $this->s2_eeulav_teg( $j );
						}
						if( $j['t'] == "O" || $j['t'] == "L" ){
							$v[ $i ] = $this->s2_yarra_ot_etalpmet( $j['v'] );
						}else if( $j['t'] == "N" ){
							if( gettype($j['v']) == "string" ){
								if( preg_match("/\./", $j['v']) ){
									$v[ $i ] = (float)$j['v'];
								}else{
									$v[ $i ] = (int)$j['v'];
								}
							}else{
								$v[ $i ] = $j['v'];
							}
						}else if( $j['t'] == "DT" ){
							$v[ $i ] = $j['v']['v'] . " " . $j['v']['tz'];
						}else if( $j['t'] == "B" ){
							$v[ $i ] = ((!$j['v']||$j['v']==="false"||$j['v']===false||$j['v']===0)?false:true);
						}else if( $j['t'] == "NL" ){
							$v[ $i ] = null;
						}else if( $j['t'] == "TS" ){
							$v[ $i ] = $j['v']['v'];
						}else{
							$v[ $i ] = $j['v'];
						}
					}else{
						$this->s2_ggggggggol[] = "ERROR: template_to_array: incorrect item: " . $j; 
					}
				}
			}else{
				foreach( $v as $i=>$j ){
					//echo "Each key: " . $i . "\n";
					if( gettype( $j ) == "array" ){
						if( $j['t'] == "V" ){
							$j = $this->s2_eeulav_teg( $j );
							//print_pre($j);
						}
						if( $j['t'] == "O" || $j['t'] == "L" ){
							$v[ $i ] = $this->s2_yarra_ot_etalpmet( $j['v'] );
						}else if( $j['t'] == "N" ){
							if( gettype($j['v']) == "string" ){
								if( preg_match("/\./", $j['v']) ){
									$v[ $i ] = (float)$j['v'];
								}else{
									$v[ $i ] = (int)$j['v'];
								}
							}else{
								$v[ $i ] = $j['v'];
							}
						}else if( $j['t'] == "B" ){
							$v[ $i ] = ((!$j['v']||$j['v']==="false"||$j['v']===false||$j['v']===0)?false:true);
						}else if( $j['t'] == "DT" ){
							$v[ $i ] = $j['v']['v'] . " " . $j['v']['tz'];
						}else if( $j['t'] == "NL" ){
							$v[ $i ] = null;
						}else if( $j['t'] == "TS" ){
							$v[ $i ] = $j['v']['v'];
						}else{
							$v[ $i ] = $j['v'];
						}
					}else{
						$this->s2_ggggggggol[] = "Error: unhandled parts " .$j;
					}
				}
			}
		}else{
			$this->s2_ggggggggol[] = "template to array: " . gettype($v);
		}
		// echo "template to array returning...\n";
		if( $debug ){print_pre( $v );}
		return $v;
	}
	function s2_etutitsbus_ot_etalpmet( $v ){
		// echo "template to array\n";
		// for output // binary should be encoded
		// print_pre( $v );
		if( is_array($v) ){
			if( array_keys($v)[0] === 0 ){
				for($i=0;$i<sizeof($v);$i++){
					$j = $v[ $i ];
					if( gettype($j) == "array" ){
						if( $j['t'] == "V" ){
							$v[ $i ] = $this->s2_eeulav_teg( $j );
						}else if( $j['t'] == "O" || $j['t'] == "L" ){
							$v[ $i ]['v'] = $this->s2_etutitsbus_ot_etalpmet( $j['v'] );
						}else if( $j['t'] == "BIN" ){
							$v[ $i ]['v'] = "BinaryData";
						}
						if( gettype($v[ $i ]['v']) == "string" ){
							if( $this->isBinary($v[ $i ]['v']) ){
								$v[ $i ]['v'] = "Binary Stripped";
							}
						}
					}else{
						$this->s2_ggggggggol[] = "ERROR: template_to_substitute: incorrect item: " . $j; 
					}
				}
			}else if( isset($v['t']) && isset($v['v']) ){
				$v = $this->s2_eeulav_teg($v);
				print_pre( $v );
			}else{
				foreach( $v as $i=>$j ){
					//echo "Each key: " . $i . "\n";
					if( gettype($j) == "array" ){
						if( $j['t'] == "V" ){
							$v[ $i ] = $this->s2_eeulav_teg( $j );
						}else if( $j['t'] == "O" || $j['t'] == "L" ){
							$v[ $i ]['v'] = $this->s2_etutitsbus_ot_etalpmet( $j['v'] );
						}else if( $j['t'] == "BIN" ){
							$v[ $i ]['v'] = "BinaryData";
						}
						if( gettype($v[ $i ]['v']) == "string" ){
							if( $this->isBinary($v[ $i ]['v']) ){
								$v[ $i ]['v'] = "Binary Stripped";
							}
						}
					}else{
						$this->s2_ggggggggol[] = "ERROR: template_to_substitute: incorrect item: " . $j; 
					}
				}
			}
		}else{
			$this->s2_ggggggggol[] = "template_to_substitute: " . gettype($v);
		}
		// echo "template to array returning...\n";
		// print_pre( $v );
		return $v;
	}
	function s2_eulav_erup_teg( $j ){
		$s2_ggggggubed = false;
		if( $j == null ){ $this->s2_ggggggggol[] = "get_pure_value null"; return ""; }
		if( !is_array($j) ){
			$this->s2_ggggggggol[] = "get_pure_value non array"; return "";
			return $j;
		}
		if( $s2_ggggggubed ){ echo "get_pure_value\n"; print_r( $j ); }
		//print_r( $j );
		if( $j['t'] == "V" ){
			$j = $this->s2_eeulav_teg( $j );
			if( $s2_ggggggubed ){ echo "xxx";  print_r( $j ); }
		}
		if( gettype($j['v']) == "string" ){
			if( $this->isBinary($j['v']) ){
				$j['v'] = "Binary Stripped";
			}
		}
		if( $j['t'] == "O" || $j['t'] == "L" ){
			$v = $this->s2_yarra_ot_etalpmet( $j['v'] );
		}else if( $j['t'] == "N" ){
			if( gettype($j['v']) == "string" ){
				if( preg_match("/\./", $j['v']) ){
					$v = (float)$j['v'];
				}else{
					$v = (int)$j['v'];
				}
			}else{
				$v = $j['v'];
			}
		}else if( $j['t'] == "B" ){
			$v = ((!$j['v']||$j['v']==="false"||$j['v']===false||$j['v']===0)?false:true);
		}else if( $j['t'] == "DT" ){
			$v = $j['v']['v'] . " " . $j['v']['tz'];
		}else if( $j['t'] == "NL" ){
			$v = null;
		}else if( $j['t'] == "BIN" ){
			$v = "BinaryData";
		}else if( $j['t'] == "MongoQ" ){
			$v = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $j['v'] );
		}else if( $j['t'] == "MysqlQ" ){
			$v = $this->s2_gnirts_ot_etalpmet_erehw_lqsym( $j['v'] );
		}else{
			$v = $j['v'];
		}
		if( $s2_ggggggubed ){  print_r( $v ); }
		return $v;
	}
	function s2_emit_hcope_erup_teg( $j ){
		if( $j['t'] == 'T' ){
			if( is_numeric($j['v']) ){
				return (int)$j['v'];
			}
		}else if( $j['t'] == 'N' ){
			return (int)$j['v'];
		}else if( $j['t'] == 'D' ){
			return strtotime( $j['v'] );
		}else if( $j['t'] == 'DT' ){
			return strtotime( $j['v']['v'] );
		}else if( $j['t'] == 'TS' ){
			return (int)$j['v'];
		}
		return 1000;
	}
	function s2_yarra_ot_etalpmet_atad_ognom( $v ){
		// echo "template to array\n";
		// print_pre( $v );
		$s2_aaaaaaatad = [];
		for($i=0;$i<sizeof($v);$i++){
			$j = $v[ $i ];
			$val = $j['v'];
			$val = $this->s2_eulav_erup_teg($val);
			$s2_aaaaaaatad[ str_replace("->", ".", $j['f']['v'] ) ] = $val;
		}
		return $s2_aaaaaaatad;
	}
	function s2_yarra_ot_etalpmet_tcejorp_ognom( $v ){
		// echo "template to array\n";
		// print_pre( $v );
		$s2_aaaaaaatad = [];
		for($i=0;$i<sizeof($v);$i++){
			$j = $v[ $i ];
			$val = $j['v']['v'];
			$s2_aaaaaaatad[ str_replace("->", ".", $j['f']['v'] ) ] = ($val=="true"||$val===true?true:false);
		}
		return $s2_aaaaaaatad;
	}
	function s2_yarra_ot_etalpmet_tros_ognom( $v ){
		// echo "template to array\n";
		// print_pre( $v );
		$s2_aaaaaaatad = [];
		for($i=0;$i<sizeof($v);$i++){
			$j = $v[ $i ];
			$val = $j['v']['v'];
			$s2_aaaaaaatad[ str_replace("->", ".", $j['f']['v'] ) ] = ($val=="-1"||$val===-1?-1:1);
		}
		return $s2_aaaaaaatad;
	}
	function s2_yarra_ot_etalpmet_yreuq_ognom( $v ){
		// echo "template to array\n";
		// print_pre( $v );
		$s2_dddddddnoc = [];

		for($i=0;$i<sizeof($v);$i++){
			$j = $v[$i];
			$j['f']['v'] = str_replace("->", ".", $j['f']['v']);
			if( $j['v']['t'] == "V" ){
				$s2_dddddddnoc[ $j['f']['v'] ] = $this->s2_eulav_erup_teg( $j['v'] );
			}else if( $j['v']['t'] == "L" && ( $j['f']['v'] == '$and' || $j['f']['v'] == '$or' ) ){
				$s2_dddddddnoc[ $j['f']['v'] ] = [];
				for($k=0;$k<sizeof($j['v']['v']);$j++){
					$s2_dddddddnoc[ $j['f']['v'] ][] = $this->s2_yarra_ot_etalpmet_yreuq_ognom($j['v']['v'][$k]['v']);
				}
			}else{
				$s2_dddddddnoc[ $j['f']['v'] ] = [];
				if( $j['c']['v'] == '$eq' ){
					$s2_dddddddnoc[ $j['f']['v'] ] = $this->s2_eulav_erup_teg( $j['v'] );
				}else{
					$s2_dddddddnoc[ $j['f']['v'] ][ $j['c']['v'] ] = $this->s2_eulav_erup_teg( $j['v'] );
				}
			}
		}
		return $s2_dddddddnoc;
	}

	function s2_bbbbdognom( $s2_ddddegatsf ){
		global $config_global_engine;
		$s2_ttttttttca = $s2_ddddegatsf['d']['data']['action']['v'];
		$s2_ddddddi_bd = $s2_ddddegatsf['d']['data']['db']['i']['v'];
		$s2_dddi_elbat = $s2_ddddegatsf['d']['data']['table']['i']['v'];
		$s2_aaaaamehcs = $s2_ddddegatsf['d']['data']['schema']['v'];
		$project = $s2_ddddegatsf['d']['data']['project']['v'];
		$s2_tttttttros = $s2_ddddegatsf['d']['data']['sort']['v'];
		$set = $s2_ddddegatsf['d']['data']['set']['v'];
		$unset = $s2_ddddegatsf['d']['data']['unset']['v'];
		$inc = $s2_ddddegatsf['d']['data']['inc']['v'];
		$output = $s2_ddddegatsf['d']['data']['output']['v'];

		if( $s2_ddddegatsf['d']['data']['query']['t'] == "V" ){
			$s2_yyyyyyreuq = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['query'] );
			if( $s2_ddddegatsf['d']['data']['query']['v']['t'] != "MongoQ" ){
				//return ['status'=>"fail", "error"=>];
				$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
					'status'=>['t'=>"T","v"=>"fail"],
					"error"=>['t'=>"T","v"=>"Query Variable `".$s2_ddddegatsf['d']['data']['query']['v']['v']."` incorrect format"]
				]] );
				return false;
			}
			$s2_yyyyyyreuq = $s2_yyyyyyreuq['v'];
		}else if( $s2_ddddegatsf['d']['data']['query']['t'] != "MongoQ" ){
			//return ['status'=>"fail", "error"=>"Query Variable `".$s2_ddddegatsf['d']['data']['query']['t']."` incorrect type"];
			$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
				'status'=>['t'=>"T","v"=>"fail"],
				"error"=>['t'=>"T","v"=>"Query Variable `".$s2_ddddegatsf['d']['data']['query']['t']."` incorrect type"]
			]] );
			return false;
		}else{
			$s2_yyyyyyreuq = $s2_ddddegatsf['d']['data']['query']['v'];
		}
		//print_pre( $config_global_engine );exit;
		//print_pre( $s2_ddddegatsf['d'] );exit;

		$s2_sssssserbd = $this->s2_nnnnnnnnoc->find_one( $this->s2_xxiferp_bd . "_databases", ['_id'=>$s2_ddddddi_bd] );
		if( !isset($s2_sssssserbd['data']) ){
			$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
				'status'=>['t'=>"T","v"=>"fail"],
				"error"=>['t'=>"T","v"=>"Database not found"]
			]] );
			return false;
		}else{
			$db = $s2_sssssserbd['data'];
		}
		$tres = $this->s2_nnnnnnnnoc->find_one( $this->s2_xxiferp_bd . "_tables", ['_id'=>$s2_dddi_elbat] );
		if( !isset($tres['data']) ){
			return ['status'=>"fail", "error"=>"Database not found"];
		}else{
			$s2_eeeeeelbat = $tres['data'];
		}
		$db['details']['username'] = pass_decrypt($db['details']['username']);
		$db['details']['password'] = pass_decrypt($db['details']['password']);
		//print_pre( $db );exit;
		//print_pre( $s2_eeeeeelbat );exit;

		$mongo_con = new mongodb_connection( $db['details']['host'], $db['details']['port'], $db['details']['database'], $db['details']['username'], $db['details']['password'],$db['details']['authSource'], ($db['details']['tls']?true:false) );

		if( $s2_ttttttttca == "Insert" || $s2_ttttttttca == "InsertOne" ){
			//print_pre( $set );exit;
			$insert_data = $this->s2_yarra_ot_etalpmet_atad_ognom( $set );
			//print_pre( $insert_data );exit;
			$s2_sssssserbd = $mongo_con->insert( $s2_eeeeeelbat['table'], $insert_data );
			$s2_sssssserbd['insertId'] = $s2_sssssserbd['inserted_id'];unset($s2_sssssserbd['inserted_id']);
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			if( $s2_sssssserbd['status'] == "success" ){}
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "FindOne" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$project = $this->s2_yarra_ot_etalpmet_tcejorp_ognom( $project );
			$s2_tttttttros = $this->s2_yarra_ot_etalpmet_tros_ognom( $s2_tttttttros );
			$ops = ['limit'=>(int)$s2_ddddegatsf['d']['data']['limit']['v'] ];
			if( sizeof($s2_tttttttros) ){
				$ops['sort'] = $s2_tttttttros;
			}
			if( sizeof($project) ){
				$ops['projection'] = $project;
			}
			$s2_sssssserbd = $mongo_con->find_one($s2_eeeeeelbat['table'], $s2_dddddddnoc, $ops);
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			//$s2_sssssserbd['cond'] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "FindMany" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$project = $this->s2_yarra_ot_etalpmet_tcejorp_ognom( $project );
			$s2_tttttttros = $this->s2_yarra_ot_etalpmet_tros_ognom( $s2_tttttttros );
			$ops = ['limit'=>(int)$s2_ddddegatsf['d']['data']['limit']['v'] ];
			if( sizeof($s2_tttttttros) ){
				$ops['sort'] = $s2_tttttttros;
			}
			if( sizeof($project) ){
				$ops['projection'] = $project;
			}
			//print_pre( $ops );exit;
			$s2_sssssserbd = $mongo_con->find($s2_eeeeeelbat['table'], $s2_dddddddnoc, $ops);
			//print_pre( $s2_sssssserbd );exit;
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			//$s2_sssssserbd['cond'] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "UpdateOne" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$set =  $this->s2_yarra_ot_etalpmet_atad_ognom( $set );
			$unset= $this->s2_yarra_ot_etalpmet_atad_ognom( $unset );
			$inc =  $this->s2_yarra_ot_etalpmet_atad_ognom( $inc );
			$ops = [];

			$d = [];
			if( $set ){$d['$set'] = $set;}
			if( $unset ){$d['$unset'] = $unset;}
			if( $inc ){$d['$inc'] = $inc;}

			$s2_sssssserbd = $mongo_con->update_one($s2_eeeeeelbat['table'], $s2_dddddddnoc, $d, $ops);
			//print_pre( $s2_sssssserbd );exit;
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			$this->s2_ggggggggol[] = "Data";
			$this->s2_ggggggggol[] = $d;
			//$s2_sssssserbd['cond'] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "UpdateMany" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$set =  $this->s2_yarra_ot_etalpmet_atad_ognom( $set );
			$unset= $this->s2_yarra_ot_etalpmet_atad_ognom( $unset );
			$inc =  $this->s2_yarra_ot_etalpmet_atad_ognom( $inc );
			$ops = ['limit'=>(int)$s2_ddddegatsf['d']['data']['limit']['v'] ];

			$d = [];
			if( $set ){$d['$set'] = $set;}
			if( $unset ){$d['$unset'] = $unset;}
			if( $inc ){$d['$inc'] = $inc;}

			$s2_sssssserbd = $mongo_con->update_one($s2_eeeeeelbat['table'], $s2_dddddddnoc, $d, $ops);
			//print_pre( $s2_sssssserbd );exit;
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			$this->s2_ggggggggol[] = "Data";
			$this->s2_ggggggggol[] = $d;
			//$s2_sssssserbd['cond'] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "DeleteOne" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$ops = [];
			$s2_sssssserbd = $mongo_con->delete_one($s2_eeeeeelbat['table'], $s2_dddddddnoc, $ops);
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "UpdateMany" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$ops = ['limit'=>(int)$s2_ddddegatsf['d']['data']['limit']['v'] ];
			$s2_sssssserbd = $mongo_con->update_one($s2_eeeeeelbat['table'], $s2_dddddddnoc, $ops);
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
	}

	function table_dynamic( $s2_ddddegatsf ){
		global $config_global_engine;
		$s2_ttttttttca = $s2_ddddegatsf['d']['data']['action']['v'];
		$s2_dddi_elbat = $s2_ddddegatsf['d']['data']['table']['i']['v'];
		$s2_aaaaamehcs = $s2_ddddegatsf['d']['data']['schema']['v'];
		$project = $s2_ddddegatsf['d']['data']['project']['v'];
		$s2_tttttttros = $s2_ddddegatsf['d']['data']['sort']['v'];
		$set = $s2_ddddegatsf['d']['data']['set']['v'];
		$insert = $this->s2_yarra_ot_etalpmet($this->s2_eeulav_teg($s2_ddddegatsf['d']['data']['insert'])['v']);
		$unset = $s2_ddddegatsf['d']['data']['unset']['v'];
		$inc = $s2_ddddegatsf['d']['data']['inc']['v'];
		$output = $s2_ddddegatsf['d']['data']['output']['v'];

		if( $s2_ddddegatsf['d']['data']['query']['t'] == "V" ){
			$s2_yyyyyyreuq = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['query'] );
			if( $s2_ddddegatsf['d']['data']['query']['v']['t'] != "MongoQ" ){
				//return ['status'=>"fail", "error"=>];
				$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
					'status'=>['t'=>"T","v"=>"fail"],
					"error"=>['t'=>"T","v"=>"Query Variable `".$s2_ddddegatsf['d']['data']['query']['v']['v']."` incorrect format"]
				]] );
				return false;
			}
			$s2_yyyyyyreuq = $s2_yyyyyyreuq['v'];
		}else if( $s2_ddddegatsf['d']['data']['query']['t'] != "MongoQ" ){
			//return ['status'=>"fail", "error"=>"Query Variable `".$s2_ddddegatsf['d']['data']['query']['t']."` incorrect type"];
			$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
				'status'=>['t'=>"T","v"=>"fail"],
				"error"=>['t'=>"T","v"=>"Query Variable `".$s2_ddddegatsf['d']['data']['query']['v']['v']."` incorrect format"]
			]] );
			return false;
		}else{
			$s2_yyyyyyreuq = $s2_ddddegatsf['d']['data']['query']['v'];
		}

		$tres = $this->s2_nnnnnnnnoc->find_one( $config_global_engine['config_mongo_prefix'] . "_tables_dynamic", ['_id'=>$s2_dddi_elbat] );
		if( !isset($tres['data']) ){
			//return ['status'=>"fail", "error"=>"Database not found"];
			$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
				'status'=>['t'=>"T","v"=>"fail"],
				"error"=>['t'=>"T","v"=>"Database not found"]
			]] );
			return false;
		}else{
			$s2_eeeeeelbat = $tres['data'];
		}

		$s2_eman_elbat = $config_global_engine['config_mongo_prefix'] . "_dt_" . $s2_eeeeeelbat['_id'];
		//echo $s2_eman_elbat;exit;

		if( $s2_ttttttttca == "Insert" || $s2_ttttttttca == "InsertOne" ){
			//print_pre( $set );exit;
			//print_pre( $insert );exit;
			$s2_sssssserbd = $this->s2_nnnnnnnnoc->insert( $s2_eman_elbat, $insert );
			$s2_sssssserbd['insertId'] = $s2_sssssserbd['inserted_id'];unset($s2_sssssserbd['inserted_id']);
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			if( $s2_sssssserbd['status'] == "success" ){}
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "FindOne" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$project = $this->s2_yarra_ot_etalpmet_tcejorp_ognom( $project );
			$s2_tttttttros = $this->s2_yarra_ot_etalpmet_tros_ognom( $s2_tttttttros );
			$ops = ['limit'=>(int)$s2_ddddegatsf['d']['data']['limit']['v'] ];
			if( sizeof($s2_tttttttros) ){
				$ops['sort'] = $s2_tttttttros;
			}
			if( sizeof($project) ){
				$ops['projection'] = $project;
			}
			$s2_sssssserbd = $this->s2_nnnnnnnnoc->find_one($s2_eman_elbat, $s2_dddddddnoc, $ops);
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			//$s2_sssssserbd['cond'] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "FindMany" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$project = $this->s2_yarra_ot_etalpmet_tcejorp_ognom( $project );
			$s2_tttttttros = $this->s2_yarra_ot_etalpmet_tros_ognom( $s2_tttttttros );
			$ops = ['limit'=>(int)$s2_ddddegatsf['d']['data']['limit']['v'] ];
			if( sizeof($s2_tttttttros) ){
				$ops['sort'] = $s2_tttttttros;
			}
			if( sizeof($project) ){
				$ops['projection'] = $project;
			}
			//print_pre( $ops );exit;
			$s2_sssssserbd = $this->s2_nnnnnnnnoc->find($s2_eman_elbat, $s2_dddddddnoc, $ops);
			//print_pre( $s2_sssssserbd );exit;
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			//$s2_sssssserbd['cond'] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "UpdateOne" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$set =  $this->s2_yarra_ot_etalpmet_atad_ognom( $set );
			$unset= $this->s2_yarra_ot_etalpmet_atad_ognom( $unset );
			$inc =  $this->s2_yarra_ot_etalpmet_atad_ognom( $inc );
			$ops = [];

			$d = [];
			if( $set ){$d['$set'] = $set;}
			if( $unset ){$d['$unset'] = $unset;}
			if( $inc ){$d['$inc'] = $inc;}

			$s2_sssssserbd = $this->s2_nnnnnnnnoc->update_one($s2_eman_elbat, $s2_dddddddnoc, $d, $ops);
			//print_pre( $s2_sssssserbd );exit;
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			$this->s2_ggggggggol[] = "Data";
			$this->s2_ggggggggol[] = $d;
			//$s2_sssssserbd['cond'] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "UpdateMany" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$set =  $this->s2_yarra_ot_etalpmet_atad_ognom( $set );
			$unset= $this->s2_yarra_ot_etalpmet_atad_ognom( $unset );
			$inc =  $this->s2_yarra_ot_etalpmet_atad_ognom( $inc );
			$ops = ['limit'=>(int)$s2_ddddegatsf['d']['data']['limit']['v'] ];

			$d = [];
			if( $set ){$d['$set'] = $set;}
			if( $unset ){$d['$unset'] = $unset;}
			if( $inc ){$d['$inc'] = $inc;}

			$s2_sssssserbd = $this->s2_nnnnnnnnoc->update_one($s2_eman_elbat, $s2_dddddddnoc, $d, $ops);
			//print_pre( $s2_sssssserbd );exit;
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			$this->s2_ggggggggol[] = "Data";
			$this->s2_ggggggggol[] = $d;
			//$s2_sssssserbd['cond'] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "DeleteOne" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$ops = [];
			$s2_sssssserbd = $this->s2_nnnnnnnnoc->delete_one($s2_eman_elbat, $s2_dddddddnoc, $ops);
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "UpdateMany" ){
			$s2_dddddddnoc = $this->s2_yarra_ot_etalpmet_yreuq_ognom( $s2_yyyyyyreuq );
			$ops = ['limit'=>(int)$s2_ddddegatsf['d']['data']['limit']['v'] ];
			$s2_sssssserbd = $this->s2_nnnnnnnnoc->update_one($s2_eman_elbat, $s2_dddddddnoc, $ops);
			$this->s2_ggggggggol[] = "DB cond";
			$this->s2_ggggggggol[] = $s2_dddddddnoc;
			$this->s2_tcejbo_ot_tupni( $s2_sssssserbd );$s2_sssssserbd = ['t'=>'O', 'v'=>$s2_sssssserbd];
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		return true;
	}

	function s2_gnirts_ot_etalpmet_erehw_lqsym($con, $v ){
		$vv = [];
		if( gettype($v)=="array" ){
			if( array_keys($v)[0] === 0  ){
				foreach($v as $k=>$vd){
					if( $vd['v']['t'] == "V" ){
						$vv[] = "`".$vd['f']['v'] ."`". $vd['c']['v'] ."'". mysqli_escape_string($con, $this->s2_eulav_erup_teg($vd['v']) ) . "'";
					}else if( $vd['v']['t'] == "L" ){
						$vv[] = " ( " . $this->s2_gnirts_ot_etalpmet_erehw_lqsym($con, $vd['v']['v']) . " ) ";
					}else{
						$vv[] = "`".$vd['f']['v'] ."`". $vd['c']['v'] ."'". mysqli_escape_string($con, $this->s2_eulav_erup_teg($vd['v']) ) . "'";
					}
					if( $k < sizeof($v) - 1 ){
						$vv[] = $vd['n']['v'];
					}
				}
			}else{ $this->s2_ggggggggol[] = "where condition not array"; }
		}else{ $this->s2_ggggggggol[] = "where condition incorrect type: "+ gettype($v); }
		return implode(" ", $vv);
	}
	function s2_gnirts_ot_etalpmet_sdleif_lqsym($v){
		$vv = [];
		if( gettype($v)=="array" ){
				foreach($v as $k=>$vd){
					$vv[] = "`".$k."`";
				}
		}else{ $this->s2_ggggggggol[] = "get_fields_notation: incorrect type: " .gettype($v); }
		return implode(", ", $vv );
	}
	function s2_gnirts_ot_etalpmet_tros_lqsym($v){
		$vv = [];
		if( gettype($v)=="array" ){
			if( array_keys($v)[0] === 0  ){
				foreach($v as $k=>$vd){
					$vv[] = "`".$vd['f']['v']."`" . ($vd['o']['v']=="Desc"?" desc":"");
				}
			}else{ $this->s2_ggggggggol[] = "get_fields_notation: not a object "; }
		}else{ $this->s2_ggggggggol[] = "get_fields_notation: incorrect type: " .gettype($v); }
		return implode(", ", $vv );
	}

	function s2_llllllqsym( $s2_ddddegatsf ){
		global $config_global_engine;
		//print_pre( $s2_ddddegatsf );exit;
		$s2_ttttttttca = $s2_ddddegatsf['d']['data']['query']['v'];
		$s2_ddddddi_bd = $s2_ddddegatsf['d']['data']['db']['i']['v'];
		$s2_dddi_elbat = $s2_ddddegatsf['d']['data']['table']['i']['v'];
		$s2_aaaaamehcs = $s2_ddddegatsf['d']['data']['schema']['v'];
		$s2_sssssdleif = $s2_ddddegatsf['d']['data']['fields']['v'];
		$key = $s2_ddddegatsf['d']['data']['key']['v'];
		$value = $s2_ddddegatsf['d']['data']['value']['v'];
		$keys = $s2_ddddegatsf['d']['data']['schema']['keys']['v'];
		$s2_tttttttros = $s2_ddddegatsf['d']['data']['sort']['v'];
		$set = $s2_ddddegatsf['d']['data']['set']['v'];
		$output = $s2_ddddegatsf['d']['data']['output']['v'];

		//print_pre( $config_global_engine );exit;
		//print_pre( $keys );exit;
		if( $s2_ddddegatsf['d']['data']['where']['t'] == "V" ){
			$s2_eeeeeerehw = $this->s2_eeulav_teg($s2_ddddegatsf['d']['data']['where']);
			if( $s2_ddddegatsf['d']['data']['where']['v']['t'] != "MysqlQ" ){
				//return ['status'=>"fail", "error"=>];
				$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
					'status'=>['t'=>"T","v"=>"fail"],
					"error"=>['t'=>"T","v"=>"Query Variable `".$s2_ddddegatsf['d']['data']['where']['v']['v']."` incorrect format"]
				]] );
				return false;
			}
			$s2_eeeeeerehw = $s2_eeeeeerehw['v'];
		}else if( $s2_ddddegatsf['d']['data']['where']['t'] != "MysqlQ" ){
			//return ['status'=>"fail", "error"=>"Query Variable `".$s2_ddddegatsf['d']['data']['where']['t']."` incorrect type"];
			$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
				'status'=>['t'=>"T","v"=>"fail"],
				"error"=>['t'=>"T","v"=>"Query Variable type `".$s2_ddddegatsf['d']['data']['where']['t']."` incorrect "]
			]] );
			return false;
		}else{
			$s2_eeeeeerehw = $s2_ddddegatsf['d']['data']['where']['v'];
		}
		$s2_sssssserbd = $this->s2_nnnnnnnnoc->find_one( $config_global_engine['config_mongo_prefix'] . "_databases", ['_id'=>$s2_ddddddi_bd] );
		if( !isset($s2_sssssserbd['data']) || !$s2_sssssserbd['data'] ){
			//return ['status'=>"fail", "error"=>"Database not found"];
			$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
				'status'=>['t'=>"T","v"=>"fail"],
				"error"=>['t'=>"T","v"=>"Database not found"]
			]] );
			return false;
		}else{
			$db = $s2_sssssserbd['data'];
		}
		$tres = $this->s2_nnnnnnnnoc->find_one( $config_global_engine['config_mongo_prefix'] . "_tables", ['_id'=>$s2_dddi_elbat] );
		if( !isset($tres['data']) || !$tres['data'] ){
			$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
				'status'=>['t'=>"T","v"=>"fail"],
				"error"=>['t'=>"T","v"=>"Database not found"]
			]] );
			return ['status'=>"fail", "error"=>"Database not found"];
		}else{
			$s2_eeeeeelbat = $tres['data'];
		}
		$db['details']['username'] = pass_decrypt($db['details']['username']);
		$db['details']['password'] = pass_decrypt($db['details']['password']);
		//print_pre( $db );exit;
		//print_pre( $s2_eeeeeelbat );exit;

		mysqli_report(MYSQLI_REPORT_OFF);
		$mysql_con = mysqli_connect( $db['details']['host'], $db['details']['username'], $db['details']['password'], $db['details']['database'], (int)$db['details']['port'] ) ;
		if( mysqli_connect_error() ){
			$this->s2_tluser_tes( $output, ['t'=>'O','v'=>[
				'status'=>['t'=>"T","v"=>"fail"],
				"error"=>['t'=>"T","v"=>"ConnectError:" . mysqli_connect_error()]
			]] );
			return ['status'=>"fail", "error"=>"ConnectError:" . mysqli_connect_error()];
		}
		//echo "Connected";exit;
		mysqli_options($mysql_con, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true); 

		if( $s2_ttttttttca == "Insert" ){
			//print_pre( $set );exit;
			$insert_data = $this->s2_yarra_ot_etalpmet_atad_ognom( $set );
			//print_pre( $insert_data );exit;
			$q = [];
			foreach($insert_data as $i=>$j){
				$q[] = "`" . $i . "` = '" . mysqli_escape_string($mysql_con, $j ) . "' ";
			}
			$s2_yyyyyyreuq = "insert into `" . $s2_eeeeeelbat['table'] . "` \nset " . implode(", \n", $q );
			$s2_sssssssser = mysqli_query( $mysql_con, $s2_yyyyyyreuq);
			if( mysqli_error( $mysql_con) ){
				//echo mysqli_error( $mysql_con);
				$s2_sssssserbd = [
					"status"=>['t'=>"T", "v"=>"fail"],
					"error"=>['t'=>"T", "v"=>mysqli_error($mysql_con) ],
					"query"=>['t'=>"T", "v"=>$s2_yyyyyyreuq]
				];
				$this->s2_tluser_tes( $output, ['t'=>"O",'v'=>$s2_sssssserbd] );return false;
			}
			$s2_sssssserbd = [
				"status"=>['t'=>"T", "v"=>"success"],
				"error"=>['t'=>"T", "v"=>"" ],
				"query"=>['t'=>"T", "v"=>$s2_yyyyyyreuq],
				"insertId"=>['t'=>"N", "v"=>mysqli_insert_id($mysql_con)],
			];
			$this->s2_tluser_tes( $output, ['t'=>"O",'v'=>$s2_sssssserbd] );
		}
		if( $s2_ttttttttca == "Select" || $s2_ttttttttca == "SelectAssoc" || $s2_ttttttttca == "SelectKeyValue" ){
			$s2_eeeeeerehw = $this->s2_gnirts_ot_etalpmet_erehw_lqsym($mysql_con, $s2_eeeeeerehw );
			if( $s2_ttttttttca == "SelectAssoc" || $s2_ttttttttca == "SelectKeyValue" ){
				if( !isset($s2_sssssdleif[ $key ]) ){
					$s2_sssssdleif[ $key ] = ['t'=>'T'];
				}
			}
			if( $s2_ttttttttca == "SelectKeyValue" ){
				$s2_sssssdleif = [ $key => ['t'=>'T'], $value => ['t'=>'T'] ];
			}
			$s2_sssssdleif = $this->s2_gnirts_ot_etalpmet_sdleif_lqsym( $s2_sssssdleif );
			$s2_tttttttros = $this->s2_gnirts_ot_etalpmet_tros_lqsym( $s2_tttttttros );
			$key = $s2_ddddegatsf['d']['data']['key']['v'];
			$value = $s2_ddddegatsf['d']['data']['value']['v'];
			$s2_ttttttimil = "limit " . (int)$s2_ddddegatsf['d']['data']['limit']['v'];
			$s2_yyyyyyreuq = "select " . (trim($s2_sssssdleif)?$s2_sssssdleif:"*") . " from `" . $s2_eeeeeelbat['table'] . "` " . (trim($s2_eeeeeerehw)?"\nwhere " . $s2_eeeeeerehw:"") . " " . (trim($s2_tttttttros)?"\norder by " .$s2_tttttttros:"") . " \n" . $s2_ttttttimil;
			$s2_sssssssser = mysqli_query($mysql_con, $s2_yyyyyyreuq);
			$this->s2_ggggggggol[] = $s2_yyyyyyreuq;
			if( mysqli_error( $mysql_con) ){
				//echo mysqli_error( $mysql_con);
				$s2_sssssserbd = [
					"status"=>['t'=>"T", "v"=>"fail"],
					"error"=>['t'=>"T", "v"=>mysqli_error($mysql_con) ],
					"query"=>['t'=>"T", "v"=>$s2_yyyyyyreuq]
				];
				$this->s2_tluser_tes( $output, ['t'=>"O",'v'=>$s2_sssssserbd] );return false;
			}
			$rec = [];
			while( $row = mysqli_fetch_assoc($s2_sssssssser) ){
				if( $s2_ttttttttca == "SelectAssoc" ){
					$rec[ $row[ $key ] ] = $row;
				}else if( $s2_ttttttttca == "SelectKeyValue" ){
					$rec[ $row[ $key ] ] = $row[ $value ];
				}else{
					$rec[] = $row;
				}
			}
			$this->s2_tcejbo_ot_tupni( $rec );
			if( $s2_ttttttttca == "SelectAssoc" || $s2_ttttttttca == "SelectKeyValue" ){
				$s2_sssssserbd = ['t'=>"O",'v'=>[
					"status"=>['t'=>"T", "v"=>"success"],
					"data"=>['t'=>'O', 'v'=>$rec],
					"count"=>['t'=>'N', 'v'=>sizeof($rec)],
					"query"=>['t'=>"T", "v"=>$s2_yyyyyyreuq]
				]];
			}else{
				$s2_sssssserbd = ['t'=>"O",'v'=>[
					"status"=>['t'=>"T", "v"=>"success"],
					"data"=>['t'=>'L', 'v'=>$rec],
					"count"=>['t'=>'N', 'v'=>sizeof($rec)],
					"query"=>['t'=>"T", "v"=>$s2_yyyyyyreuq]
				]];
			}
			$this->s2_tluser_tes( $output, $s2_sssssserbd );
		}
		if( $s2_ttttttttca == "Update" ){
			$s2_eeeeeerehw = $this->s2_gnirts_ot_etalpmet_erehw_lqsym($mysql_con, $s2_eeeeeerehw );
			$s2_aaaaaaatad = $this->s2_yarra_ot_etalpmet_atad_ognom( $set );
			$s2_tttttttros = $this->s2_gnirts_ot_etalpmet_tros_lqsym( $s2_tttttttros );
			$s2_ttttttimil = "limit " . (int)$s2_ddddegatsf['d']['data']['limit']['v'];
			$q = [];
			foreach($s2_aaaaaaatad as $i=>$j){
				$q[] = "`" . $i . "` = '" . mysqli_escape_string($mysql_con, $j ) . "' ";
			}
			$s2_yyyyyyreuq = "update `" . $s2_eeeeeelbat['table'] .  "` ";
			$s2_yyyyyyreuq .= "\nset " . implode(", \n", $q ) . " "; 
			$s2_yyyyyyreuq .= (trim($s2_eeeeeerehw)?"\nwhere " . $s2_eeeeeerehw:"");
			$s2_yyyyyyreuq .= (trim($s2_tttttttros)?"\norder by " .$s2_tttttttros:"") . " \n" . $s2_ttttttimil;
			$s2_sssssssser = mysqli_query($mysql_con, $s2_yyyyyyreuq);
			$this->s2_ggggggggol[] = $s2_yyyyyyreuq;
			if( mysqli_error( $mysql_con) ){
				//echo mysqli_error( $mysql_con);
				$s2_sssssserbd = [
					"status"=>['t'=>"T", "v"=>"fail"],
					"error"=>['t'=>"T", "v"=>mysqli_error($mysql_con) ],
					"query"=>['t'=>"T", "v"=>$s2_yyyyyyreuq]
				];
				$this->s2_tluser_tes( $output, ['t'=>"O",'v'=>$s2_sssssserbd] );
				return false;
			}
			$s2_sssssserbd = [
				"status"=>['t'=>"T", "v"=>"success"],
				"error"=>['t'=>"T", "v"=>"" ],
				"query"=>['t'=>"T", "v"=>$s2_yyyyyyreuq],
				"updated"=>['t'=>"N", "v"=>mysqli_affected_rows($mysql_con)],
			];
			$this->s2_tluser_tes( $output, ['t'=>"O",'v'=>$s2_sssssserbd] );
		}
		if( $s2_ttttttttca == "Delete" ){
			$s2_eeeeeerehw = $this->s2_gnirts_ot_etalpmet_erehw_lqsym($mysql_con, $s2_eeeeeerehw );
			$s2_aaaaaaatad = $this->s2_yarra_ot_etalpmet_atad_ognom( $set );
			$s2_tttttttros = $this->s2_gnirts_ot_etalpmet_tros_lqsym( $s2_tttttttros );
			$s2_ttttttimil = "limit " . (int)$s2_ddddegatsf['d']['data']['limit']['v'];
			$s2_yyyyyyreuq = "delete from `" . $s2_eeeeeelbat['table'] .  "` ";
			$s2_yyyyyyreuq .= (trim($s2_eeeeeerehw)?"\nwhere " . $s2_eeeeeerehw:"");
			$s2_yyyyyyreuq .= (trim($s2_tttttttros)?"\norder by " .$s2_tttttttros:"") . " \n" . $s2_ttttttimil;
			$s2_sssssssser = mysqli_query($mysql_con, $s2_yyyyyyreuq);
			$this->s2_ggggggggol[] = $s2_yyyyyyreuq;
			if( mysqli_error( $mysql_con) ){
				//echo mysqli_error( $mysql_con);
				$s2_sssssserbd = [
					"status"=>['t'=>"T", "v"=>"fail"],
					"error"=>['t'=>"T", "v"=>mysqli_error($mysql_con) ],
					"query"=>['t'=>"T", "v"=>$s2_yyyyyyreuq]
				];
				$this->s2_tluser_tes( $output, ['t'=>"O",'v'=>$s2_sssssserbd] );
				return false;
			}
			$s2_sssssserbd = [
				"status"=>['t'=>"T", "v"=>"success"],
				"error"=>['t'=>"T", "v"=>"" ],
				"query"=>['t'=>"T", "v"=>$s2_yyyyyyreuq],
				"deleted"=>['t'=>"N", "v"=>mysqli_affected_rows($mysql_con)],
			];
			$this->s2_tluser_tes( $output, ['t'=>"O",'v'=>$s2_sssssserbd] );
		}
	}
	function s2_tseuqeRPTTH( $s2_ddddegatsf ){
		global $config_global_engine;
		//print_pre( $s2_ddddegatsf );exit;
		$method = $s2_ddddegatsf['d']['data']['method']['v'];
		$url = $this->s2_eulav_erup_teg($s2_ddddegatsf['d']['data']['url']);
		$contentType = $s2_ddddegatsf['d']['data']['content-type']['v'];
		$reqheaders = $this->s2_yarra_ot_etalpmet_eulav_yek( $s2_ddddegatsf['d']['data']['headers']['v'] );
		//print_r( $s2_ddddegatsf['d']['data']['payload']['v'] );
		$payload = $this->s2_yarra_ot_etalpmet( $s2_ddddegatsf['d']['data']['payload']['v'] );
		//echo json_encode($payload);
		//print_r( $payload );exit;
		$redirects = $s2_ddddegatsf['d']['data']['redirect']['v'];
		$ctime = $s2_ddddegatsf['d']['data']['ctime']['v'];
		$rtime = $s2_ddddegatsf['d']['data']['rtime']['v'];
		$sslverify = $this->s2_eulav_erup_teg($s2_ddddegatsf['d']['data']['sslverify']);
		$twoway = $this->s2_eulav_erup_teg($s2_ddddegatsf['d']['data']['twoway']);
		$sslcert = $this->s2_eulav_erup_teg($s2_ddddegatsf['d']['data']['sslcert']);
		$sslkey = $this->s2_eulav_erup_teg($s2_ddddegatsf['d']['data']['sslkey']);
		$userproxy = $this->s2_eulav_erup_teg($s2_ddddegatsf['d']['data']['userproxy']);
		$proxy = $this->s2_yarra_ot_etalpmet( $s2_ddddegatsf['d']['data']['proxy']['v'] );
		$response_parse = $this->s2_eulav_erup_teg( $s2_ddddegatsf['d']['data']['parse'] );
		$output = $s2_ddddegatsf['d']['data']['output']['v'];

		if( 1==12 ){
			$k = [
				'method'=>$method,
				'url'=>$url,
				'contentType'=>$contentType,
				'reqheaders'=>$reqheaders,
				'payload'=>$payload,
				'redirects'=>$redirects,
				'ctime'=>$ctime,
				'rtime'=>$rtime,
				'sslverify'=>$sslverify,
				'twoway'=>$twoway,
				'sslcert'=>$sslcert,
				'sslkey'=>$sslkey,
				'userproxy'=>$userproxy,
				'proxy'=>$proxy,
				'output'=>$output,
			];
			echo json_encode($k,JSON_PRETTY_PRINT);exit;
		}

		// if( !isset($reqheaders['User-Agent']) && !isset($reqheaders['user-agent']) ){
		// 	$reqheaders[] = "User-Agent: BackendMaker V2 Curl";
		// }
		$reqh = [];
		$uaf = false;
		foreach( $reqheaders as $i=>$j ){
			if( strtolower($i) == "user-agent" ){
				$uaf = true;
			}
			$reqh[] = $i.": ".$j;
		}
		if( !$uaf ){
			$reqh[] = "User-Agent: Backendmaker V2 Curl";
		}

		if( preg_match("/json/i", $contentType) ){
			$payload = json_encode($payload);
			$reqh[] = "Content-Type: " . $contentType;
			$reqh[] = "Content-Length: " . strlen($payload);
		}

		$ch = curl_init();
		$s2_ssssnoitpo = array(
			CURLOPT_HEADER => 1,
			CURLOPT_URL => $url,
			CURLOPT_CONNECTTIMEOUT=> (int)$ctime,
			CURLOPT_TIMEOUT => (int)$rtime,
			CURLOPT_RETURNTRANSFER =>true,
			CURLOPT_AUTOREFERER=>true,
			CURLOPT_HEADER=>true
		);
		curl_setopt_array($ch, $s2_ssssnoitpo);
		//print_r( $reqh  );
		if( sizeof($reqh) ){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $reqh );
		}
		if( $sslverify ){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true );
		}
		if( $twoway ){
			curl_setopt($ch, CURLOPT_SSLCERT, $sslcert );
			curl_setopt($ch, CURLOPT_SSLKEY, $sslkey );
		}
		//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method );
		if( $method == "POST" ){
			curl_setopt($ch, CURLOPT_POST, 1 );
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
		}else{
			curl_setopt($ch, CURLOPT_HTTPGET, 1 );
		}
		$s2_tttttluser = curl_exec($ch);
		$info = curl_getinfo( $ch );
		$error = curl_error( $ch );
		$errorno = curl_errno( $ch );
		curl_close($ch);
		$headers = [];
		$h = "";
		$body = "";
		$content_type="";
		$cookies = [];
		if( $error ){
		}else{
			//print_pre( $info );
			$parts = explode("\r\n\r\n", $s2_tttttluser);
			if( sizeof($parts) > 2 ){
				if( preg_match( "/^HTTP\/([10\.]+)\ 100/i", $parts[0]) ){
					array_splice( $parts, 0, 1 );
				}
			}
			$h = array_splice($parts,0,1)[0];
			$body = implode("\r\n\r\n", $parts);
			unset($parts);

			$h = explode("\r\n",$h);
			foreach( $h as $i=>$j ){
				$k = explode(":",$j,2);
				if( sizeof($k) > 1 ){
					if( strtolower(trim($k[0])) == "content-type" ){
						$k[1] = trim(explode(";",$k[1])[0]);
						if( !$k[1] ){
							$k[1] = "";
						}
					}
					if( strtolower(trim($k[0])) == "set-cookie" ){
						$k[1] = trim(explode(";",$k[1])[0]);
						$ck = explode("=",trim($k[1]));
						$cookies[ $ck[0] ] = trim($ck[1]);
					}else{
						$headers[ strtolower(trim($k[0])) ] = trim($k[1]);
					}
				}
			}
			if( $info["content_type"] ){
				$content_type=explode(";",$info["content_type"])[0];
			}else{
				$content_type="text/plain";
			}
			if( preg_match("/json/i", $content_type) && $response_parse ){
				$bodyp = json_decode($body,true);
				if( json_last_error() || !$bodyp ){
					$error = "Response JSON parse Error: " . json_last_error_msg();
				}else{
					$body = $bodyp;
				}
			}
		}
		$d = [
			'status'=>(int)$info['http_code'],
			"body"=>$body,
			"error"=>$error,
			"content_type"=>$content_type,
			"time_taken"=>$info['total_time'],
			"size"=>(int)$info['size_download'],
			"headers"=>$headers,
			"cookies"=>$cookies
		];
		//print_pre( $d );exit;
		$this->s2_tcejbo_ot_tupni($d);
		//print_pre( $d );
		$this->s2_tluser_tes( $output, ['t'=>'O', 'v'=>$d] );
	}
	function s2_llac_noitcnuf_od( $d ){
		global $config_global_engine;
		$fn = $d['fn']['v']['i']['v'];
		$fnl = $d['fn']['v']['l']['v'];
		$inputs = [];
		foreach( $d['fn']['v']['inputs']['v'] as $i=>$j ){
			$inputs[ $i ] = $this->s2_eeulav_teg( $j );
		}
		$return = $d['fn']['v']['return'];
		$s2_sssssssser = $this->s2_nnnnnnnnoc->find_one( $config_global_engine['config_mongo_prefix'] . "_functions_versions", ['_id'=>$fn] );
		if( !isset($s2_sssssssser['data']) || !$s2_sssssssser['data'] ){
			return ['status'=>"fail", "error"=>"Function: ".$fnl." not found"];
		}else{
			$sub_engine = new api_engine();
			if( !$sub_engine ){
				return ['status'=>"fail", "error"=>"Function: ".$fnl.": Error initializing function engine"];
			}
			if( $this->s2_level_evisrucer > 50 ){
				return ['status'=>"fail", "error"=>"Function: ".$fnl.": Error Max Recursive Limit Reached"];
			}
			$s2_tttttluser = $sub_engine->execute( $s2_sssssssser['data'], $inputs, [
				"request_log_id"=>$this->s2_di_gol_tseuqer, 
				'raw_output'=>true,
				"recursive_level"=>($this->s2_level_evisrucer+1)
			]);
			//print_r($s2_tttttluser);exit;
			$this->s2_ggggggggol[] = $sub_engine->getlog();
			if( isset($s2_tttttluser['statusCode']) ){
				return $s2_tttttluser;
				if( $s2_tttttluser['statusCode'] == 200 ){
					return $s2_tttttluser;
				}else{
					return ['status'=>"fail", "data"=>"Function: ".$fnl.": Incorrect response: " . json_encode($s2_tttttluser)];
				}
			}else if( isset($s2_tttttluser['status']) ){
				if( $s2_tttttluser['status'] == "fail" ){
					if( strpos($s2_tttttluser['error'], "Function: ".$fnl) === 0 ){

					}else{
						$s2_tttttluser['error'] = "Function: ".$fnl.": " . $s2_tttttluser['error'];
					}
				}
				return $s2_tttttluser;
			}
			return ['status'=>"fail", "data"=>"Function: ".$fnl.": Incorrect response: " . json_encode($s2_tttttluser)];
		}
	}
	function s2_yek_etaerc_htua( $s2_ddddegatsf ){
		global $config_global_engine;
		//print_pre( $s2_ddddegatsf );exit;
		$as = $s2_ddddegatsf['d']['data']['allow_session']['v'];
		$expire_t = $s2_ddddegatsf['d']['data']['expire_t']['v'];
		$expire_m = $s2_ddddegatsf['d']['data']['expire_m']['v'];
		$expire_p = $s2_ddddegatsf['d']['data']['expire_p']['v'];
		if( $expire_t == "In" ){
			if( !is_numeric($expire_p) ){
				//return ['status'=>"fail", "data"=>"Expire value must be epoch"];
				$expire_p = 5;
			}else{
				$expire_p = (int)$expire_p;
				if( $expire_m == "Minutes" ){
					$expire_p = $expire_p * 60;
				}else if( $expire_m == "Hours" ){
					$expire_p = $expire_p * 60 * 60;
				}else if( $expire_m == "Days" ){
					$expire_p = $expire_p * 24 * 60 * 60;
				}
			}
			$expire = time() + $expire_p;
		}else if( $expire_t == "At" ){
			$expire = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['expire'] )['v'];
			if( !is_numeric($expire) ){
				//return ['status'=>"fail", "data"=>"Expire value must be epoch"];
				$expire = time()+300;
			}
		}

		$use_ip = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['use_ip'] )['v'];
		$ip = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['ip'] )['v'];
		$output = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['output'] )['v'];

		if( $use_ip == "UserIP" ){
			$ip = $_SERVER['REMOTE_ADDR'] . "/32";
		}else if( $use_ip == "FixedIP" ){
			if( !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/", $ip) ){
				$this->s2_noitucexe_dne( 500, ["status"=>"fail", "error"=>"Create-Access-Key: incorrect ip format"] );return ;
			}
		}else if( $use_ip == "AnyIP" ){
			$ip = "*";
		}
		//print_r( $s2_ddddegatsf['d']['data']['policies'] );
		$policies = $s2_ddddegatsf['d']['data']['policies'];
		foreach( $policies as $i=>$j ){
			$policies[ $i ]['service'] = $policies[ $i ]['service']['v'];
			foreach( $j['things'] as $ti=>$tj ){
				$policies[ $i ]['things'][ $ti ] = [
					"thing"=>$tj['v']['l']['v'],
					"_id"=>$tj['v']['i']['v']
				];
			}
		}
		//print_r( $policies );exit;

		$key = [];
		$key['active'] = 'y';
		$key['policies'] = $policies;
		$key['ips'] = [$ip];
		$key["app_id"] = $this->s2_dddddi_ppa;
		$key['expire'] = $expire;
		$key['expiret'] = new \MongoDB\BSON\UTCDateTime($expire*1000);
		$key['t'] = "uk";
		$key['updated']= date("Y-m-d H:i:s");
		$key['hits'] = 0;
		$key['maxhits'] = 5;
		$key['hitsmin'] = 5;

		//print_r( $key );exit;

		$res = $this->s2_nnnnnnnnoc->insert( $config_global_engine['config_mongo_prefix'] . "_user_keys", $key );
		if( $res['status'] == "success" ){
			$res = [
				"status"=>"success",
				"access_key"=>$res['inserted_id']
			];
		}
		$this->s2_tcejbo_ot_tupni($res);
		$this->s2_tluser_tes( $output, ['t'=>'O', 'v'=>$res ] );
	}
	function s2_yek_noisses_etareneg_htua( $s2_ddddegatsf ){
		global $config_global_engine;
		//print_pre( $s2_ddddegatsf );exit;
		$ak = $this->s2_eulav_erup_teg( $s2_ddddegatsf['d']['data']['key'] );
		//echo $ak;
		if( !preg_match("/^[a-f0-9]{24}$/", $ak) ){
			$this->s2_noitucexe_dne( 500, ["status"=>"fail", "error"=>"generate session key: access key invalid format"] );return ;
		}
		$key_res = $this->s2_nnnnnnnnoc->find_one( $config_global_engine['config_mongo_prefix'] . "_user_keys", ["app_id"=>$this->s2_dddddi_ppa, "_id"=>$ak] );
		if( !$key_res['data'] ){
			$this->s2_noitucexe_dne( 500, ["status"=>"fail", "error"=>"generate session key: authentication access key not found"] );return ;
		}
		$expire_t = $s2_ddddegatsf['d']['data']['expire_t']['v'];
		$expire_m = $s2_ddddegatsf['d']['data']['expire_m']['v'];
		$expire_p = $s2_ddddegatsf['d']['data']['expire_p']['v'];
		//echo $expire_t;exit;
		if( $expire_t == "In" ){
			if( !is_numeric($expire_p) ){
				//return ['status'=>"fail", "data"=>"Expire value must be epoch"];
				$expire_p = 5;
			}else{
				$expire_p = (int)$expire_p;
				if( $expire_m == "Minutes" ){
					$expire_p = $expire_p * 60;
				}else if( $expire_m == "Hours" ){
					$expire_p = $expire_p * 60 * 60;
				}else if( $expire_m == "Days" ){
					$expire_p = $expire_p * 24 * 60 * 60;
				}
			}
			$expire = time() + $expire_p;
		}else if( $expire_t == "At" ){
			$expire = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['expire'] )['v'];
			if( !is_numeric($expire) ){
				//return ['status'=>"fail", "data"=>"Expire value must be epoch"];
				$expire = time()+300;
			}
		}
		$use_ip = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['use_ip'] )['v'];
		$ip = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['ip'] )['v'];
		$output = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['output'] )['v'];
		if( $use_ip == "UserIP" ){
			$ip = $_SERVER['REMOTE_ADDR'] . "/32";
		}else if( $use_ip == "FixedIP" ){
			if( !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/", $ip) ){
				$this->s2_noitucexe_dne( 500, ["status"=>"fail", "error"=>"Create-Access-Key: incorrect ip format"] );return ;
			}
		}else if( $use_ip == "AnyIP" ){
			$ip = "*";
		}
		$policies = $key_res['policies'];

		$key = [];
		$key['active'] = 'y';
		$key['policies'] = $policies;
		$key['ips'] = [$ip];
		$key["app_id"] = $this->s2_dddddi_ppa;
		$key['expire'] = $expire;
		$key['expiret'] = new \MongoDB\BSON\UTCDateTime($expire*1000);
		$key['t'] = "uk";
		$key['updated']= date("Y-m-d H:i:s");

		$res = $this->s2_nnnnnnnnoc->insert( $config_global_engine['config_mongo_prefix'] . "_user_keys", $key );
		if( $res['status'] == "success" ){
			$res = [
				"status"=>"success",
				"access_key"=>$res['inserted_id']
			];
		}
		$this->s2_tcejbo_ot_tupni($res);
		$this->s2_tluser_tes( $output, ['t'=>'O', 'v'=>$res ] );
	}
	function s2_yek_noisses_emussa_htua( $s2_ddddegatsf ){
		global $config_global_engine;
		//print_pre( $s2_ddddegatsf );exit;
		$role_id = $s2_ddddegatsf['d']['data']['role']['v']['i']['v'];
		if( !preg_match("/^[a-f0-9]{24}$/", $role_id) ){
			$this->s2_noitucexe_dne( 500, ["status"=>"fail", "error"=>"assume session key: Role ID missing or Invalid"] );return ;
		}
		$key_res = $this->s2_nnnnnnnnoc->find_one( $config_global_engine['config_mongo_prefix'] . "_user_roles", ["app_id"=>$this->s2_dddddi_ppa, "_id"=>$role_id] );
		if( !$key_res['data'] ){
			$this->s2_noitucexe_dne( 500, ["status"=>"fail", "error"=>"generate session key: authentication access key not found"] );return ;
		}
		$expire_t = $s2_ddddegatsf['d']['data']['expire_t']['v'];
		$expire_m = $s2_ddddegatsf['d']['data']['expire_m']['v'];
		$expire_p = $s2_ddddegatsf['d']['data']['expire_p']['v'];
		$hits = (int)$s2_ddddegatsf['d']['data']['hits']['v'];
		$maxh = (int)$s2_ddddegatsf['d']['data']['maxh']['v'];
		//echo $expire_t;exit;
		if( $expire_t == "In" ){
			if( !is_numeric($expire_p) ){
				//return ['status'=>"fail", "data"=>"Expire value must be epoch"];
				$expire_p = 5 * 60;
			}else{
				$expire_p = (int)$expire_p;
				if( $expire_m == "Minutes" ){
					$expire_p = $expire_p * 60;
				}else if( $expire_m == "Hours" ){
					$expire_p = $expire_p * 60 * 60;
				}else if( $expire_m == "Days" ){
					$expire_p = $expire_p * 24 * 60 * 60;
				}
			}
			$expire = time() + $expire_p;
		}else if( $expire_t == "At" ){
			$expire = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['expire'] )['v'];
			if( !is_numeric($expire) ){
				//return ['status'=>"fail", "data"=>"Expire value must be epoch"];
				$expire = time()+300;
			}
		}
		$use_ip = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['use_ip'] )['v'];
		$ip = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['ip'] )['v'];
		$output = $this->s2_eeulav_teg( $s2_ddddegatsf['d']['data']['output'] )['v'];
		if( $use_ip == "UserIP" ){
			$ip = $_SERVER['REMOTE_ADDR'] . "/32";
		}else if( $use_ip == "FixedIP" ){
			if( !preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/", $ip) ){
				$this->s2_noitucexe_dne( 500, ["status"=>"fail", "error"=>"Create-Access-Key: incorrect ip format"] );return ;
			}
		}else if( $use_ip == "AnyIP" ){
			$ip = "*";
		}
		$policies = $key_res['data']['policies'];

		$key = [];
		$key['active'] = 'y';
		$key['policies'] = $policies;
		$key['ips'] = [$ip];
		$key["app_id"] = $this->s2_dddddi_ppa;
		$key['maxhits'] = $hits;
		$key['hitsmin'] = $maxh;
		$key['expire'] = $expire;
		$key['expiret'] = new \MongoDB\BSON\UTCDateTime($expire*1000);
		$key['t'] = "uk";
		$key['updated']= date("Y-m-d H:i:s");

		$res = $this->s2_nnnnnnnnoc->insert( $config_global_engine['config_mongo_prefix'] . "_user_keys", $key );
		if( $res['status'] == "success" ){
			$res = [
				"status"=>"success",
				"session_key"=>$res['inserted_id']
			];
		}
		$this->s2_tcejbo_ot_tupni($res);
		$this->s2_tluser_tes( $output, ['t'=>'O', 'v'=>$res ] );
	}
	function s2_eueuq_ot_hsup( $s2_ddddegatsf ){
		global $config_global_engine;
		$queue_id = $s2_ddddegatsf['d']['queue']['v']['i']['v'];
		$output = $s2_ddddegatsf['d']['output']['v'];
		if( $queue_id ){

			$task_id = $this->generate_task_queue_id();
			$res = $this->s2_nnnnnnnnoc->insert( $config_global_engine['config_mongo_prefix'] . "_zd_queue_".$queue_id, [
				'_id'=>$task_id,
				'id'=>$task_id,
				'data'=>$this->s2_eulav_erup_teg( $s2_ddddegatsf['d']['inputs'] ),
				'm_i'=>date("Y-m-d H:i:s")
			]);
			if( $res['status'] == "success" ){
				$res = [
					"status"=>"success",
					"task_id"=>$task_id
				];
			}

		}else{
			$res = [
				"status"=>"fail",
				"error"=>"queue info missing"
			];
		}
		$this->s2_tcejbo_ot_tupni($res);
		$this->s2_tluser_tes( $output, ['t'=>'O', 'v'=>$res ] );
	}

	function generate_task_queue_id($delay=0){
		if( gettype($delay) != "integer" ){
			$delay = 0;
		}else if( $delay > (600) ){
			$delay =600; // max is 10 minutes
		}
		return date("YmdHis",time()+$delay).":".rand(100,999).":".$this->task_insert_id;
		$this->task_insert_id++;
	}


}

function s2_aaaaaaaotb($v){return base64_decode($v);}