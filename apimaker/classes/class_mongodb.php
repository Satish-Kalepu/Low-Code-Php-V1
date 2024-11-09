<?php

if( file_exists("vendor/autoload.php") ){
	require_once("vendor/autoload.php");
}else if( file_exists("../vendor/autoload.php") ){
	require_once("../vendor/autoload.php");
}else if( file_exists("../../vendor/autoload.php") ){
	require_once("../../vendor/autoload.php");
}else if( file_exists("../../../vendor/autoload.php") ){
	require_once("../../../vendor/autoload.php");
}else{
	echo "Incorrect include path!";exit;
}

class mongodb_connection{

	public $database = false;
	public $connection = false;
	public $debug = false;

	function __construct( $host = "localhost", $port = 27017, $db = "test", $user = "", $pass = "", $authdb='admin', $tls = false ){
		//echo $hostname;exit;
		$options = [
			'retryWrites'=>false,
			'retryReads'=>false,
			'socketTimeoutMS' => 10000,
			'connectTimeoutMS'=> 3000,
			'maxIdleTimeMS'=> 600
		];
		if( $user ){
			$options['authSource'] = $authdb;
			$auth = $user . ':' . urlencode($pass) . '@';
		}
		//echo "mongodb://". $auth . $host.":".$port;exit;
		$this->connection = new MongoDB\Client( "mongodb://". $auth . $host.":".$port. "/".$db,$options, [
			'typeMap'=>[
				'array'=>'array',
				'root'=>'array',
				'document'=>'array'
			]
		] );
		$this->database = $this->connection->{ $db };
	}

	function is_id_valid($v){
		if( preg_match("/^[a-f0-9]{24}$/", $v) ){
			return $v;
		}else{
			return false;
		}
	}

	function error($e){
		header("http/1.1 500 error");
		echo $e;
		exit;
	}

	function get_id( $vid ){
		if( preg_match("/^[a-f0-9]{24}$/", $vid) ){
			try{
				return new MongoDB\BSON\ObjectID( $vid );
			}catch(Exception $ex){
				echo $ex->getMessage();exit;
				return false;
			}
		}else{
			return $vid;
		}
	}

	function get_max_id( $collection ){
		$col = $this->database->{$collection};
		try{
			$cur = $col->find([],['projection'=>['_id'=>1],'sort'=>['_id'=>-1], 'limit'=>1])->toArray();
			if( $cur ){
				$c = $cur[0]['_id'];
				if( is_array($c) ){
					$c = (string)$c;
				}
				return [ 'status'=>"success", "data"=>$c ];
			}else{
				return [ 'status'=>"fail", "error"=>"NotFound" ];
			}
		}catch(Exception $ex){
			return [ 'status'=>"fail", "error"=>$ex->getMessage() ];
		}
	}

	function get_max_numeric_id( $collection ){
		$col = $this->database->{$collection};
		try{
			$cur = $col->find([],['projection'=>['_id'=>1],'sort'=>['_id'=>-1], 'limit'=>1])->toArray();
			if( $cur ){
				$c = $cur[0]['_id'];
				$c = (int)$c;
				return [ 'status'=>"success", "data"=>$c ];
			}else{
				return [ 'status'=>"success", "data"=>1 ];
			}
		}catch(Exception $ex){
			return [ 'status'=>"fail", "error"=>$ex->getMessage() ];
		}
	}

	function regex($v, $f = ""){
		try{
			return ['$regex' => $v, '$options' => $f];
		}catch(Exception $ex){
			error_log("class_mongodb regex " . $v . " : ". $f );
			return false;
		}
	}

	function generate_id(){
		try{
			$k = new MongoDB\BSON\ObjectID();
			$k = (array)$k;
			//print_pre( $k );
			return $k['oid'];
		}catch(Exception $ex){
			$this->error( "Object ID Parse Failed: " . $vid . " : " . $ex->getMessage() );
			return false;
		}
	}

	function create_collection( $collection ){
		try{
			$res = $this->database->createCollection( $collection, [
				"collation"=> [ "locale"=>"en_US", "strength"=> 2]
			]);
			return ['status'=>"success"];
		}catch(Exception $ex){
			return [ 'status'=>"fail", "error"=>$ex->getMessage() ];
		}
	}

	function insert( $collection, $insert_data, $options = [] ){
		$col = $this->database->{$collection};
		try{
			if( $insert_data["_id"] && is_string( $insert_data["_id"] ) ){
				$insert_data["_id"] = $this->get_id( $insert_data["_id"] );
			}
			$cur = $col->insertOne($insert_data);
			$id =  (string)$cur->getInsertedId();
			return ["status"=>"success","inserted_id"=>$id];
		}catch(Exception $ex){
			if( preg_match("/duplicate/i", $ex->getMessage() ) ){
				if( preg_match("/dup key\: \{ ([a-z\_0-9\-\.]+)\:/i", $ex->getMessage(), $m ) ){
					return ["status"=>"fail","error"=>"duplicate key error on field: " . $m[1]];
				}else{
					return ["status"=>"fail","error"=>$ex->getMessage()];
				}
			}else{
				return ["status"=>"fail","error"=>$ex->getMessage()];
			}
		}
		return false;
	}

	function find($collection, $condition = array(), $option = array() ){
		if( ! is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_array($condition) ){
			return ["status"=>"fail","error"=>"condition is not array"];
		}
		$option["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		if( !$option['limit'] ){
			$option['limit'] = 500;
		}
		$option["collation"]= [ "locale"=>"en_US", "strength"=> 2];
		$col = $this->database->{$collection};
		try{
			if( $condition["_id"] && is_string( $condition["_id"] ) ){
				$condition["_id"] = $this->get_id( $condition["_id"] );
			}else if( $condition["_id"] && is_array( $condition["_id"] ) ){
				//$q = array_keys();
				foreach( $condition["_id"] as $ci=>$cd ){
					if( preg_match("/^\W[a-z]+$/",$ci) && is_string($cd) ){
						if( preg_match("/^[a-f0-9]{24}$/",$cd) ){
							$condition["_id"][ $ci ] = $this->get_id($cd);
						}
					}
				}
			}
			$cur = $col->find($condition, $option)->toArray();
			foreach( $cur as $i=>$j ){
				if( is_string($cur[$i]['_id']) || is_array($cur[$i]['_id']) || is_object($cur[$i]['_id']) ){
					$cur[$i]['_id'] = (string)$cur[$i]['_id'];
				}
			}
			return ["status"=>"success","data"=>$cur];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
	}
	function find_with_key($collection, $key = "_id", $condition = array(), $option = array() ){
		if( !is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_string($key) ){
			return ["status"=>"fail","error"=>"key is required"];
		}
		if( !is_array($condition) ){
			return ["status"=>"fail","error"=>"condition is not array"];
		}
		$col = $this->database->{$collection};
		if( !$option['limit'] ){
			$option['limit'] = 500;
		}
		$option["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		$newcur = [];
		try{
			if( $condition["_id"] && is_string( $condition["_id"] ) ){
				$condition["_id"] = $this->get_id( $condition["_id"] );
			}else if( $condition["_id"] && is_array( $condition["_id"] ) ){
				//$q = array_keys();
				foreach( $condition["_id"] as $ci=>$cd ){
					if( preg_match("/^\W[a-z]+$/",$ci) && is_string($cd) ){
						if( preg_match("/^[a-f0-9]{24}$/",$cd) ){
							$condition["_id"][ $ci ] = $this->get_id($cd);
						}
					}
				}
			}
			//$cur = bson_to_json($col->find($condition, $option));
			$cur = $col->find($condition, $option)->toArray();
			for($i = 0; $i< count($cur);$i++){
				if( is_string($cur[$i]['_id']) || is_array($cur[$i]['_id'])  || is_object($cur[$i]['_id'])  ){
					$cur[$i]['_id']=(string)$cur[$i]['_id'];
				}
				$newcur[ $cur[$i][$key] ] = $cur[$i];
			}
			return ["status"=>"success","data"=>$newcur];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return $newcur;
	}
	function find_assoc($collection, $key = "_id", $value = "_id", $condition = array(), $option = array() ){
		if( !is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_string($key) ){
			return ["status"=>"fail","error"=>"key is required"];
		}
		if( !is_string($value) ){
			return ["status"=>"fail","error"=>"value is required"];
		}
		if( !is_array($condition) ){
			return ["status"=>"fail","error"=>"condition is not array"];
		}
		$col = $this->database->{$collection};
		if( !$option['limit'] ){
			$option['limit'] = 500;
		}
		$option["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		$option['projection'] = [ $key=>1, $value=>1];
		$newcur = [];
		try{
			if( $condition["_id"] && is_string( $condition["_id"] ) ){
				$condition["_id"] = $this->get_id( $condition["_id"] );
			}else if( $condition["_id"] && is_array( $condition["_id"] ) ){
				//$q = array_keys();
				foreach( $condition["_id"] as $ci=>$cd ){
					if( preg_match("/^\W[a-z]+$/",$ci) && is_string($cd) ){
						if( preg_match("/^[a-f0-9]{24}$/",$cd) ){
							$condition["_id"][ $ci ] = $this->get_id($cd);
						}
					}
				}
			}
			//$cur = bson_to_json($col->find($condition, $option));
			$cur = $col->find($condition, $option)->toArray();
			for($i = 0; $i< count($cur);$i++){
				if( is_string($cur[$i]['_id']) || is_array($cur[$i]['_id'])  || is_object($cur[$i]['_id'])  ){
					$cur[$i]['_id']=(string)$cur[$i]['_id'];
				}
				$newcur[ $cur[$i][$key] ] = $cur[$i][$value];
			}
			return ["status"=>"success","data"=>$newcur];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return $newcur;
	}

	function find_one($collection, $condition = array(), $option = array() ){
		if( ! is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_array($condition) ){
			return ["status"=>"fail","error"=>"condition is not array"];
		}
		$col = $this->database->{$collection};
		$option["collation"] = ["locale"=>"en_US", "strength"=> 2];
		try{
			if( $condition["_id"] && is_string( $condition["_id"] ) ){
				$condition["_id"] = $this->get_id( $condition["_id"] );
			}else if( $condition["_id"] && is_array( $condition["_id"] ) ){
				//$q = array_keys();
				foreach( $condition["_id"] as $ci=>$cd ){
					//echo $ci . "=";
					if( preg_match("/^\W[a-z]+$/",$ci) && is_string($cd) ){
						if( preg_match("/^[a-f0-9]{24}$/",$cd) ){
							$condition["_id"][ $ci ] = $this->get_id($cd);
						}
					}
				}
			}
			//print_r( $condition );
			$cur = (array)$col->findOne($condition,$option);
			if($cur['_id']){
				if( is_string($cur['_id']) || is_array($cur['_id'])  || is_object($cur['_id'])  ){
					$cur['_id'] = (string)$cur['_id'];
				}
			}
			return ["status"=>"success","data"=>$cur];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return $cur;
	}
	function count($collection, $filter = array(), $option = array() ){
		$col = $this->database->{$collection};
		$option["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		try{
			$cnt = $col->count( $filter, $option );
			return ["status"=>"success","data"=>$cnt];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return false;
	}

	function update_many($collection,$condition,$data,$option=[]){
		if( !is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_array($condition) ){
			return ["status"=>"fail","error"=>"condition is not array"];
		}
		$col = $this->database->{$collection};
		$option["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		try{
			if( $data['$set'] || $data['$inc'] || $data['$rename'] || $data['$unset'] ){
			}else{
				$data = ['$set'=>$data];
			}
			if( $condition["_id"] && is_string( $condition["_id"] ) ){
				$condition["_id"] = $this->get_id( $condition["_id"] );
			}else if( $condition["_id"] && is_array( $condition["_id"] ) ){
				//$q = array_keys();
				foreach( $condition["_id"] as $ci=>$cd ){
					if( preg_match("/^\W[a-z]+$/",$ci) && is_string($cd) ){
						if( preg_match("/^[a-f0-9]{24}$/",$cd) ){
							$condition["_id"][ $ci ] = $this->get_id($cd);
						}
					}
				}
			}
			$res=$col->updateMany($condition, $data, $option);
			return [
				"status"=>"success", 
				"data"=>[
					"matched_count"=>$res->getMatchedCount(),
					"modified_count"=>$res->getModifiedCount(),
					"upserted_count"=>$res->getUpsertedCount()
				]
			];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
			return false;
		}
		return true;
	}

	function update_one( $collection, $condition, $data, $option=[] ){
		if( !is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_array($condition) ){
			return ["status"=>"fail","error"=>"condition is not array"];
		}
		if( !is_array($data) ){
			return ["status"=>"fail","error"=>"data is not array"];
		}
		$col = $this->database->{$collection};
		$option["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		try{
			if( $condition["_id"] && is_string( $condition["_id"] ) ){
				$condition["_id"] = $this->get_id( $condition["_id"] );
			}else if( $condition["_id"] && is_array( $condition["_id"] ) ){
				//$q = array_keys();
				foreach( $condition["_id"] as $ci=>$cd ){
					if( preg_match("/^\W[a-z]+$/",$ci) && is_string($cd) ){
						if( preg_match("/^[a-f0-9]{24}$/",$cd) ){
							$condition["_id"][ $ci ] = $this->get_id($cd);
						}
					}
				}
			}
			if( $data['$set'] || $data['$inc'] || $data['$rename'] || $data['$unset'] ){
			}else{
				$data = ['$set'=>$data];
			}
			$res=$col->updateOne($condition, $data, $option);
			return [
				"status"=>"success", 
				"data"=>[
					"matched_count"=>$res->getMatchedCount(),
					"modified_count"=>$res->getModifiedCount(),
					"upserted_count"=>$res->getUpsertedCount()
				]
			];
		}catch(Exception $ex ){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return true;
	}
	function replace_one( $collection, $condition, $data, $option=[] ){
		if( !is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_array($condition) ){
			return ["status"=>"fail","error"=>"condition is not array"];
		}
		if( !is_array($data) ){
			return ["status"=>"fail","error"=>"data is not array"];
		}
		$col = $this->database->{$collection};
		$option["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		try{
			if( $condition["_id"] && is_string( $condition["_id"] ) ){
				$condition["_id"] = $this->get_id( $condition["_id"] );
			}else if( $condition["_id"] && is_array( $condition["_id"] ) ){
				//$q = array_keys();
				foreach( $condition["_id"] as $ci=>$cd ){
					if( preg_match("/^\W[a-z]+$/",$ci) && is_string($cd) ){
						if( preg_match("/^[a-f0-9]{24}$/",$cd) ){
							$condition["_id"][ $ci ] = $this->get_id($cd);
						}
					}
				}
			}
			if( $data['$set'] || $data['$inc'] || $data['$rename'] || $data['$unset'] ){
			}else{
				$data = ['$set'=>$data];
			}
			//print_r( $data );
			$res=$col->replaceOne($condition, $data, $option);
			return [
				"status"=>"success", 
				"data"=>[
					"matched_count"=>$res->getMatchedCount(),
					"modified_count"=>$res->getModifiedCount(),
					"upserted_count"=>$res->getUpsertedCount()
				]
			];
		}catch(Exception $ex ){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return true;
	}

	function getnextseq($collection, $condition = array(),$data = array()){
		if( !is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_array($condition) ){
			return ["status"=>"fail","error"=>"condition is not array"];
		}
		if( !is_array($data) ){
			return ["status"=>"fail","error"=>"data is not array"];
		}
		$col = $this->database->{$collection};
		try{
			$option =[
			'upsert'=> true,
			'new' => true,
			'returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
			];
			$cur =$col->findOneAndUpdate($condition,$data,$option);
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return $cur;
	}
	function increment($collection, $key = "something", $val = "val", $incr = 1){
		if( !is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_string($key) ){
			return ["status"=>"fail","error"=>"key is not string"];
		}
		$incr = (int)$incr;
		$col = $this->database->{$collection};
		try{
			$option = [
				'upsert'=> true,
				'returnNewDocument' => true,
				'returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
				'projection'=>[$val=>true],
			];
			$cur =$col->findOneAndUpdate([
				'_id'=>$this->get_id($key)
			],[
				'$set'=>[
					'_id'=>$this->get_id($key),
				],
				'$inc'=>[
					$val=>$incr
				]
			],$option);
			return ["status"=>"success","data"=>$cur];;
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
	}
	function decrement($collection, $key = "something", $val = "val", $incr = 1){
		if( !is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_string($key) ){
			return ["status"=>"fail","error"=>"key is not string"];
		}
		$incr = (int)$incr;
		$col = $this->database->{$collection};
		try{
			$option =[
				'upsert'=> true,
				'new' => true,
				'returnNewDocument' => true,
				'returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
				'projection'=>[$val=>true],
			];
			$cur =$col->findOneAndUpdate([
				'_id'=>$this->get_id($key)
			],[
				'$inc'=>[
					$val=>$incr
				]
			],$option);
			return ["status"=>"success","data"=>$cur];;
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
	}

	function aggregate( $collection, $pipeline, $options = [] ){
		if( !is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_array($pipeline) ){
			return ["status"=>"fail","error"=>"pipeline is not array"];
		}
		$col = $this->database->{$collection};
		$options["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		try{
			$res=$col->aggregate($pipeline, $options)->toArray();
			return ["status"=>"success","data"=>$res];;
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return false;
	}

	function delete_one( $collection, $condition, $option = [] ){
		if( !is_string($collection) ){
			return ["status"=>"fail","error"=>"collection name required"];
		}
		if( !is_array($condition) ){
			return ["status"=>"fail","error"=>"condition is not array"];
		}
		$col = $this->database->{$collection};
		$option["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		try{
			if( $condition["_id"] && is_string( $condition["_id"] ) ){
				$condition["_id"] = $this->get_id( $condition["_id"] );
			}else if( $condition["_id"] && is_array( $condition["_id"] ) ){
				//$q = array_keys();
				foreach( $condition["_id"] as $ci=>$cd ){
					if( preg_match("/^\W[a-z]+$/",$ci) && is_string($cd) ){
						if( preg_match("/^[a-f0-9]{24}$/",$cd) ){
							$condition["_id"][ $ci ] = $this->get_id($cd);
						}
					}
				}
			}
			$res = $col->deleteOne( $condition );
			return [ "status"=>"success", "deleted_count"=>$res->getDeletedCount() ];
			return true;
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return true;
	}

	function delete_many($collection, $condition, $option = []){
		$col = $this->database->{$collection};
		$option["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		try{
			if( $condition["_id"] && is_string( $condition["_id"] ) ){
				$condition["_id"] = $this->get_id( $condition["_id"] );
			}else if( $condition["_id"] && is_array( $condition["_id"] ) ){
				//$q = array_keys();
				foreach( $condition["_id"] as $ci=>$cd ){
					if( preg_match("/^\W[a-z]+$/",$ci) && is_string($cd) ){
						if( preg_match("/^[a-f0-9]{24}$/",$cd) ){
							$condition["_id"][ $ci ] = $this->get_id($cd);
						}
					}
				}
			}
			$res = $col->deletemany($condition);
			return [ "status"=>"success", "deleted_count"=>$res->getDeletedCount() ];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return true;
	}

	function find_and_delete($collection,$condition, $option = []){
		$col = $this->database->{$collection};
		$option["collation"] = [ "locale"=>"en_US", "strength"=> 2];
		try{
			if( $condition["_id"] && is_string( $condition["_id"] ) ){
				$condition["_id"] = $this->get_id( $condition["_id"] );
			}else if( $condition["_id"] && is_array( $condition["_id"] ) ){
				//$q = array_keys();
				foreach( $condition["_id"] as $ci=>$cd ){
					if( preg_match("/^\W[a-z]+$/",$ci) && is_string($cd) ){
						if( preg_match("/^[a-f0-9]{24}$/",$cd) ){
							$condition["_id"][ $ci ] = $this->get_id($cd);
						}
					}
				}
			}
			$col->findOneAndDelete($condition, $option);
			return [ "status"=>"success", "deleted_count"=>$res->getDeletedCount() ];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return true;
	}

	function drop_collection( $collection ){
		$col = $this->database->{$collection};
		try{
			$col->drop();
			return ["status"=>"success"];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
		return false;
	}

	function list_collections(){
		try{
			$res = $this->database->listCollectionNames();
			$cols =[];
			foreach( $res as $i=>$j ){
				$cols[] = $j;
			}
			return ['status'=>"success", "data"=>$cols];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
	}

	function list_indexes_raw($collection){
		try{
			$col = $this->database->{$collection};
			$s = $col->listIndexes();
			$indexes = [];
			foreach( $s as $i=>$j ){if( $j['name'] != '_id_' ){
				$indexes[ $j['name'] ] = [ "name"=>$j['name'], "keys"=>[] ];
				foreach( $j['key'] as $m=>$n ){
					$indexes[ $j['name'] ]['keys'][] = ["name"=>$m, "sort"=>($n==-1?"dsc":"asc")];
				}
				$indexes[ $j['name'] ][ "sparse" ] = $j['sparse']?true:false;
				$indexes[ $j['name'] ][ "unique" ] = $j['unique']?true:false;
			}}
			return ['status'=>"success", "data"=>$indexes];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
	}

	function create_index($collection, $keys, $ops = []){
		try{
			$ops[ "background" ] = true;
			$ops[ "collation" ] = [ "locale"=>"en_US", "strength"=> 2];
			$col = $this->database->{$collection};
			$col->createIndex($keys,$ops);
			return ['status'=>"success"];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
	}
	function drop_index($collection, $indexname = "one"){
		try{
			$ops = [];
			$ops[ "background" ] = true;
			$col = $this->database->{$collection};
			$col->dropIndex($indexname,$ops);
			return ['status'=>"success"];
		}catch(Exception $ex){
			return ["status"=>"fail","error"=>$ex->getMessage()];
		}
	}

	function drop_table($table){
		$col = $this->database->{$collection};
		$col->drop();
	}

	function currentOps(){
		//$res = $this->connection->execute("db.currentOps()");
		try{
			$curops = [];
			$res = $this->connection->admin->command(["currentOp"=>1])->toArray();
			foreach( $res as $i=>$j ){
				$curops[] = [
					"desc"=>$j['desc'],
					"client"=>$j['client'],
					"secs_running"=>$j['secs_runnings'],
					"cmd"=>($j['command']?$j['command']['$db']:""),
				];
			}
			print_pre( $res );exit;
		}catch(Exception $ex){
			if( preg_match("/not authorized/i",$ex->getMessage()) ){
				return [
					'status'=>"fail", 
					"error"=>"Not authorized"
				];
			}else{
				return [
					'status'=>"fail", 
					"error"=>$ex->getMessage()
				];
			}
		}
	}

}