<?php

function index_thumbs($mapping_conf, $path, $subpath){

	global $mongo_db;
	global $app_id;
	global $db_prefix;
	global $config_global_apimaker_engine;
	global $connections;

	//return [200,"application/json",[],json_encode($storage_vault,JSON_PRETTY_PRINT) ];
	//return [200,"text/plain",[], $path . ":" . $subpath];

	if( $subpath == "" ){
		$m = "Thumb module: \nUsage: /thumb-path/thumb-(config)/dest-path/filename.image\n";
		$m .= "config: \n";
		$m .= "example: thumb-w100-h200-mfill\n";
		$m .= "w(number): width\n";
		$m .= "h(number): height\n";
		$m .= "m(mode): fill/crop. when height is fixed\n";
		$m .= "m(mode): fill/crop. when height is fixed\n";
		return [200,"text/plain",[], $m];
	}

	preg_match("/^original\/([a-z0-9\-\/\_\.]+)$/", $subpath, $m );
	if( $m ){
		$original_filename = $m[1];
		$thumb_filename = $m[0];
		if( !isset( $connections[ $mapping_conf['vault_id'] ] ) ){
			try{
				$s3con = new Aws\S3\S3Client([
					'version' => 'latest',
					'region' => $mapping_conf['vault']['region'],
					'credentials' => [
						'key'=>pass_decrypt($mapping_conf['vault']['key']),
						'secret'=>pass_decrypt($mapping_conf['vault']['secret'])
					],
				]);
				$connections[ $mapping_conf['vault_id'] ] = $s3con;
			}catch(Exception $ex){
				return [500,"text/plain",[], "Error s3 initialize: " . $ex->getMessage()];
			}
		}else{
			$s3con = $connections[ $mapping_conf['vault_id'] ];
		}
		try{
			$res = $s3con->getObject([
				'Bucket' => $mapping_conf['vault']['bucket'],
				"Key" => $original_filename
			]);
			$body = $res->get("Body");
			$ct = "image/jpeg";
			if( $res->get("ContentType") ){
				$ct = $res->get("ContentType");
			}
			return [200, $ct, [
				"Expires"=>date("D, d M Y H:i:s IST", time() + (30*86400) ),
				"Cache-Control"=> "Max-Age=2592000",
				"Cache"=>"Hit"
			], $body ];
		}catch(Aws\S3\Exception\S3Exception $ex){
			//echo "<Pre>";print_r( get_class_methods($ex) );
			//echo $ex->getAwsErrorType() . ":" . $ex->getAwsErrorMessage(); 
			//exit;
			return [404, "text/plain", [], "Source key read error: " . $ex->getAwsErrorType() . ":" . $ex->getAwsErrorMessage()	];
		}		
	}


	preg_match("/^thumb\-([a-z0-9\-]+)\/([a-z0-9\-\/\_\.]+)$/", $subpath, $m );
	if( !$m && $subpath != "original" ){
		return [404,"text/plain",[], $subpath . ": not found"];
	}


	//return [200,"text/plain",[], json_encode($m)];

	// validate input

	$query = explode("-",$m[1]);
	$original_filename = $m[2];
	$thumb_filename = $m[0];

	if( !preg_match("/\.(jpeg|jpg|gif|png|webp)$/i", $original_filename) ){
		return [400,"text/plain",[], "Incorrect filename format. " . $original_filename];
	}

	$query_params = [];
	foreach( $query as $i=>$j ){
		if( preg_match("/^w([0-9]+)$/", $j, $m) ){
			$query_params[ "width" ] = (int)$m[1];
		}
		if( preg_match("/^h([0-9]+)$/", $j, $m) ){
			$query_params[ "height" ] = (int)$m[1];
		}
		if( preg_match("/^m(fill|crop)$/", $j, $m) ){
			$query_params[ "mode" ] = $m[1];
		}
	}

	//return [200,"text/plain",[], json_encode($query_params)];
	//return [200,"text/plain",[], json_encode($mapping_conf,JSON_PRETTY_PRINT)];

	// fetch original

	if( !isset( $connections[ $mapping_conf['vault_id'] ] ) ){
		try{
			$s3con = new Aws\S3\S3Client([
				'version' => 'latest',
				'region' => $mapping_conf['vault']['region'],
				'credentials' => [
					'key'=>pass_decrypt($mapping_conf['vault']['key']),
					'secret'=>pass_decrypt($mapping_conf['vault']['secret'])
				],
			]);
			$connections[ $mapping_conf['vault_id'] ] = $s3con;
		}catch(Exception $ex){
			return [500,"text/plain",[], "Error s3 initialize: " . $ex->getMessage()];
		}
	}else{
		$s3con = $connections[ $mapping_conf['vault_id'] ];
	}

	try{
		$res = $s3con->getObject([
			'Bucket' => $mapping_conf['vault']['bucket'],
			"Key" => $thumb_filename
		]);
		$body = $res->get("Body");
		return [200, "image/jpeg", [
			"Expires"=>date("D, d M Y H:i:s IST", time() + (30*86400) ),
			"Cache-Control"=> "Max-Age=2592000",
			"Cache"=>"Hit"
		], $body ];
	}catch(Aws\S3\Exception\S3Exception $ex){
		// continue resizing in resizer mode.  service mode exit;
	}

	try{
		$res = $s3con->getObject([
			'Bucket' => $mapping_conf['vault']['bucket'],
			"Key" => $original_filename
		]);
		$body = $res->get("Body");
	}catch(Aws\S3\Exception\S3Exception $ex){
		//echo "<Pre>";print_r( get_class_methods($ex) );
		//echo $ex->getAwsErrorType() . ":" . $ex->getAwsErrorMessage(); 
		//exit;
		return [404, "text/plain", [], "Source key read error: " . $ex->getAwsErrorType() . ":" . $ex->getAwsErrorMessage()	];
	}
	try{
		$im = imagecreatefromstring( $body );
	}catch(Exception $ex){
		return [500, "text/plain", [], "Error reading source file: " .$ex->getMessage()];
	}
	$res_body = image_resize_sub($im,$query_params);

	if( is_array($res_body) ){
		return [500, "text/plain", [], json_encode($res_body) ];
	}

	//if( $mapping_conf['cache'] )
	{
		try{
			$s3_status = $s3con->putObject([
				'Body'=>$res_body,
				'Bucket' => $mapping_conf['vault']['bucket'],
				"Key" => $thumb_filename,
				'ContentType'=>"image/jpeg",
				'ACL'=>"private", 
				'Expires'=>date("D, d M Y H:i:s IST", time() + (30*86400) ), 
				'CacheControl'=> "Max-Age=2592000"
			]);
		}catch(Aws\S3\Exception\S3Exception $ex){
			return ["statusCode"=>500, "body"=>"Error saving file!" . $ex->getAwsErrorType() . ":" . $ex->getAwsErrorMessage()];
		}
	}

	return [200, "image/jpeg", [
		"Expires"=>date("D, d M Y H:i:s IST", time() + (30*86400) ),
		"Cache-Control"=> "Max-Age=2592000",
		"Cache"=>"Miss"
	], $body ];

}


	function image_resize_sub($im, $query){
		$debug = [];
		$original_width = imagesx($im);
		$original_height = imagesy($im);
		$debug[] = "original: " . $original_width . "x" . $original_height . "\n";
		$new_width  = $query["width"];
		$new_height = isset($query["height"])?$query['height']:null;
		if( !$new_height ){
			unset($query['mode']);
		}
		$debug[] = "thumb: " . $new_width . "x" . $new_height . "\n";

		if( $new_height ){
			$query["mode"] = "fill";
			// if( $new_width > $original_width || $new_height > $original_height ){
			// 	$query["mode"] = "fill";
			// }
		}
		if( !isset($query["mode"]) ){
			if( $new_width <= $original_width ){
				$d = $new_width/$original_width;
				$new_height = round($original_height*$d);
			}
			if( $new_width > $original_width ){
				$new_width = $original_width;
				$new_height = $original_height;
			}
			$newim = imagecreatetruecolor($new_width,$new_height);
			imagesavealpha($newim, true);
			$col = imagecolorallocate($newim, 245,245,245);
			imagefill($newim,0,0,$col);
			imagecopyresampled($newim, $im, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
		}else if( $query['mode'] == "crop" ){
			$ratio_thumb=$new_width/$new_height; // ratio thumb
			$ratio_original=$original_width/$original_height; // ratio original
			if( $ratio_original>=$ratio_thumb ){
				$yo=$original_height; 
				$xo=ceil(($yo*$new_width)/$new_height);
				$xo_ini=ceil(($original_width-$xo)/2);
				$xy_ini=0;
			}else{
				$xo=$original_width; 
				$yo=ceil(($xo*$new_height)/$new_width);
				$xy_ini=ceil(($original_height-$yo)/2);
				$xo_ini=0;
			}
			$newim = imagecreatetruecolor($new_width,$new_height);
			imagesavealpha($newim, true);
			$col = imagecolorallocate($newim, 245,245,245);
			imagefill($newim,0,0,$col);
			imagecopyresampled($newim, $im, 0, 0, $xo_ini, $xy_ini, $new_width, $new_height, $xo, $yo);
			
		}else if( $query["mode"] == "fill" ){
			if( $_GET['debug'] == "true" ){ echo "333\n"; }
			$new_dest_x = 0;
			$new_dest_y = 0;
			$new_dest_width = $new_width;
			$new_dest_height = $new_height;
			if( $new_dest_width > $original_width || $new_dest_height > $original_height ){
				if( $_GET['debug'] == "true" ){ echo "111xxxx\n"; }
				if( $_GET['debug'] == "true" ){ echo " = " . $new_dest_width . "x". $new_dest_height . " - " . $original_width . " x " . $original_height . "\n"; }
				$cnt = 0;
				while( $new_dest_width > $original_width || $new_dest_height > $original_height ){
					$cnt++;
					if( $cnt > 5 ){break;}

					if( $new_dest_width > $original_width ){
						$new_dest_width = $original_width;
						if( $new_dest_height < $original_height ){
							$d = ($new_dest_height/$original_height);
							$new_dest_width = round($new_dest_width*$d);
							$new_dest_height = $new_dest_height;
						}
					}
					if( $new_dest_height > $original_height ){
						$new_dest_height = $original_height;
						if( $new_dest_width < $original_width ){
							$d = ($new_dest_width/$original_width);
							$new_dest_height = round($new_dest_height*$d);
							$new_dest_width = $new_dest_width;
						}
					}
					if( $_GET['debug'] == "true" ){ echo  " = " . $new_dest_width . "x". $new_dest_height . " - " . $original_width . " x " . $original_height . "\n"; }
				}
			}else{
				$d = ($new_width/$original_width);
				$new_temp_width = $new_width;
				$new_temp_height = round($original_height*$d);
				if( $new_temp_height < $new_height ){
					// fill horizontal 
					$new_dest_y = ($new_height-$new_temp_height)/2;
					$new_dest_height = $new_temp_height;
				}else{
					$d = ($new_height/$new_temp_height);
					$new_temp_width = round($new_temp_width*$d);
					$new_temp_height = $new_height;
					$hor_diff = ($new_width-$new_temp_width)/2;
					$new_dest_x = $hor_diff;
					$new_dest_width = $new_temp_width;
					// fill vertical
				}
			}

			$new_dest_x = ($new_width-$new_dest_width)/2;
			$new_dest_y = ($new_height-$new_dest_height)/2;
			$newim = imagecreatetruecolor($new_width,$new_height);
			$col = imagecolorallocate($newim, 245,245,245);
			imagefill($newim,0,0,$col);
			imagecopyresampled($newim,$im,$new_dest_x,$new_dest_y,0,0,$new_dest_width,$new_dest_height,$original_width,$original_height);
			}else{
			if( $_GET['debug'] == "true" ){ echo "444"; }
			$newim = imagecreatetruecolor($new_width,$new_height);
			imagecopyresampled($newim, $im, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
		}

		//ob_start();imagejpeg($newim);$body=ob_get_clean();return $body;

		if( isset($query['wm']) ){
			$newim = add_watermark($newim,$query);
			if( is_array($newim) ){
				return $newim;
			}
		}
		if( isset($query['text']) ){
			$newim = add_text($newim,$query);
			if( is_array($newim) ){
				return $newim;
			}
		}
		ob_start();
		imagejpeg($newim);
		return ob_get_contents();
	}
	function validate_input(){
		global $query;
		$err = [];
		if( $query['input_validated'] ){
		}
		if( !$query['resize'] ){
			$query['resize'] = false;
		}
		if( $query['action'] ){
			if( $query['action'] == "replace_original" ){
				if( !$query["o_b"] ){
					$err[] = "Input: output bucket: required";
				}
				if( !$query["o_k"] ){
					$err[] = "Input: output key: required";
				}
				if( !preg_match("/^[0-9]+$/", $query["w"]) ){
					$err[] = "Input: Width: required";
				}
				if( !preg_match("/^[0-9]+$/", $query["h"]) ){
					$err[] = "Input: Height: required";
				}
				if( !preg_match("/^(crop|fill|none)$/i", $query["m"] ) ){
					$err[] = "Input: Mode: Unknown";
				}
			}
		}else{
			if( !$query["s_b"] ){
				$err[] = "Input: source_bucket: required";
			}else if( strpos($_ENV['config_read_buckets'], $query['s_b'] ) ){
				$err[] = "Input: source_bucket: not in allowed list";
			}
			if( !$query["s_k"] ){
				$err[] = "Input: source key: required";
			}else{
				if( $query['s_k'][0] == "/" ){
					$query['s_k'] = substr($query['s_k'],1,9999);
				}
			}
		}

		if( !$query['n_s'] ){
			if( !$query["d_b"] ){
				$err[] = "Input: source_bucket: required";
			}else if( strpos($_ENV['config_write_buckets'], $query['d_b'] ) ){
				$err[] = "Input: destination_bucket: not in allowed list";
			}
			if( !$query["d_k"] ){
				$err[] = "Input: dest key: required";
			}else{
				if( $query['d_k'][0] == "/" ){
					$query['d_k'] = substr($query['d_k'],1,9999);
				}
			}
		}

		if( sizeof($err) == 0 ){
			if( $query['ACL'] ){
				if( !preg_match("/^(private|public|public-read)$/", $query['ACL']) ){
					$err[] = "Input: ACL: Incorrect";
				}
			}else{
				$query["ACL"] = "public-read";
			}
			if( $query['M'] ){
				$config_allowed_MIME = [ "image/jpeg"=>1, "image/png"=>1, "image/gif"=>1 ];
				if( !$config_allowed_MIME[ $query['M'] ] ){
					$err[] = "Input: MIME: Incorrect";
				}
			}else{
				$v = strtolower(pathinfo($query['s_k']));
				if( $v['extension'] == "jpg" || $v['extension'] == "jpeg" ){
					$query['M'] = "image/jpeg";
				}else if( $v['extension'] == "png" ){
					$query['M'] = "image/png";
				}else{
					$err[] = "Input: Source key File Exntesion: not image";
				}
			}
			if( $query["b_c"] ){
				if( preg_match("/^([0-9]+)\,([0-9]+)\,([0-9]+)$/", $query['b_c'], $m) ){
					if( $m[1] < 0 || $m[1] > 255 || $m[2] < 0 || $m[2] > 255 || $m[3] < 0 || $m[3] > 255 ){
						$err[] = "Input: background color: incorrect2";	
					}
				}else{
					$err[] = "Input: background color: incorrect1";
				}
			}else{
				$query["b_c"] = "255,255,255";
			}
		}
	
		if( sizeof($err) == 0 ){
			if( !$query["wm"] || $query["wm"] == "0" || $query["wm"] == 0 ){
				$query["wm"] = false;
			}else{
				$query["wm"] = true;
				if( !$query["wm_b"] ){
					$err[] = "Input: Watermark file source bucket: required";
				}else if( !$query["wm_k"] ){
					$err[] = "Input: Watermark file key: required";
				}
				if( !$query["wm_p"] ){
					$query["wm_p"] = "BR";
				}else if( !preg_match("/^(TL|TR|BL|BR|C)$/", $query["wm_p"] ) ){
					$err[] = "Input: Watermark position: incorrect";
				}
				if( !$query["wm_s"] ){
					$query["wm_s"] = 15;
				}else if( $query["wm_s"] < 20 || $query["wm_s"] > 200 ){
					$err[] = "Input: Watermark size: incorrect";
				}
				$query["wm_s"] = (int)$query["wm_s"];
				if( !$query["wm_t"] ){
					$query["wm_t"] = false;
				}else if( !preg_match("/^(2|4|8|16)$/",$query["wm_t"] ) ){
					$err[] = "Input: Watermark tiles: incorrect";
				}
				$query["wm_t"] = (int)$query["wm_t"];
				if( !$query["wm_o"] ){
					$query["wm_o"] = 20;
				}else if( $query["wm_o"] < 0 || $query["wm_o"] > 100 ){
					$err[] = "Input: Watermark opacity: incorrect2";
				}
			}
			if( !$query["t"] || $query["t"] == "0" ){
				$query["t"] = false;
			}else{
				$query["t"] = true;
				if( !$query['t_t'] ){
					$err[] = "Input: text: required";
				}
				if( !$query['t_p'] ){
					$query['t_p'] = "BL";
				}else if( !preg_match("/^(TL|TR|BL|BR|C)$/", $query['t_p']) ){
					$err[] ="Input: Text position: incorrect";
				}
				if( !$query['t_s'] ){
					$query['t_s'] = 12;
				}else if( $query['t_s'] < 5 || $query["t_s"] > 80 ){
					$err[] ="Input: Text size: incorrect";
				}
				if( !$query['t_c'] ){
					$query['t_c'] = "50,50,50";
				}else{
					$x = explode(",",$query["t_c"]);
					if( $x[0] < 0 || $x[0] > 255 || $x[1] < 0 || $x[1] > 255 || $x[2] < 0 || $x[2] > 255 ){
						$err[] ="Input: Text color: incorrect";
					}
				}
			}
		}
		return $err;
	}
	function add_watermark( $newim, $query ){
		global $s3con;

		if( $this->watermark_images[ $query["wm_b"].$query["wm_k"] ] ){
			$wimgfn = $this->watermark_images[ $query["wm_b"].$query["wm_k"] ];
			$wbody =file_get_contents($wimgfn);
		}else{
			$wimgfn = $_ENV['config_temp_directory']?$_ENV['config_temp_directory']:"/tmp/". time();
			try{
				$res = $s3con->getObject([
					'Bucket'=>$query['wm_b'],
					'Key'=>$query['wm_k']
				]);
				$wbody = $res->get("Body");
				file_put_contents($wimgfn, $wbody);
			}catch(Aws\S3\Exception\S3Exception $ex){
				return [
					"statusCode"=>500, 
					"body"=>"watermark read error: " . $query['wm_b'] . ": " . $query['wm_k'] . ": " . $ex->getAwsErrorType() . ":" . $ex->getAwsErrorMessage()
				];
			}
		}

		try{
			$wimg = imagecreatefromstring($wbody);
			$wm_width = imagesx($wimg);
			$wm_height = imagesy($wimg);
		}catch(Exception $ex){
			return [
				"statusCode"=>500, 
				"body"=>"watermark load error: " . $query['wm_b'] . ": " . $query['wm_k'] . ": " .$ex->getMessage()
			];
		}

		$original_width = imagesx($newim);
		$original_height = imagesy($newim);
		// echo "<p>$original_width x $original_height</p>";
		// print_r( $query );
		// echo $query['wm_s'];
		// exit;

		if( !$query['wm_t'] ){
			$wm_resize_width = round($original_width * ($query['wm_s']/100));
			$d = $wm_resize_width/$wm_width;
			$wm_resize_height = $wm_height*$d;
			//echo "<p>$wm_resize_width x $wm_resize_height</p>";
			if( $query["wm_p"] == "C" ){
				$pos_diff = 0.8;
			}else{
				$pos_diff = 1.0;
			}
			if( $wm_resize_width > ( $original_width*$pos_diff ) || $wm_resize_height > ( $original_height*$pos_diff )  ){
				$wm_resize_width = round($original_width * $pos_diff * ($query['wm_s']/100));
				$d = $wm_resize_width/$wm_width;
				$wm_resize_height = $wm_height*$d;
			}
			//echo "<p>$wm_resize_width x $wm_resize_height</p>";exit;
			$wimgnew = imagecreatetruecolor($wm_resize_width,$wm_resize_height);
			$col = imagecolorallocatealpha($wimgnew, 255,255,255, 127);
			imagefill($wimgnew,0,0,$col);
			imagecopyresampled($wimgnew, $wimg, 0, 0, 0, 0, $wm_resize_width, $wm_resize_height, $wm_width, $wm_height);
			//return $wimgnew;
			$wm_width = $wm_resize_width;
			$wm_height = $wm_resize_height;
			if( $query["wm_p"] == "BR" ){
				$pos_x = round($original_width*.95)-$wm_width;
				$pos_y = round($original_height*.95)-$wm_height;
			}else if( $query["wm_p"] == "BL" ){
				$pos_x = round($original_width*.05);
				$pos_y = round($original_height*.95)-$wm_height;
			}else if( $query["wm_p"] == "TR" ){
				$pos_x = round($original_width*.95)-$wm_width;
				$pos_y = round($original_height*.05);
			}else if( $query["wm_p"] == "TL" ){
				$pos_x = round($original_width*.05);
				$pos_y = round($original_height*.05);
			}else if( $query["wm_p"] == "C" ){
				$pos_x = round(($original_width-$wm_width)/2);
				$pos_y = round(($original_height-$wm_height)/2);
			}
			imagecopyresampled($newim, $wimgnew, $pos_x, $pos_y, 0, 0, $wm_width, $wm_height, $wm_width, $wm_height );
		}else{
			$wm_t = $query["wm_t"]/2;
			$wm_resize_width = round($original_width/$wm_t);
			$d = $wm_resize_width/$wm_width;
			$wm_resize_height = round($wm_height*$d);
			$wimgnew = imagecreatetruecolor($wm_resize_width, $wm_resize_height);
			$col = imagecolorallocatealpha($wimgnew, 0,0,0, 127); imagefill($wimgnew,0,0,$col);
			imagesavealpha($wimgnew, true);
			imagecopyresampled($wimgnew, $wimg, 0, 0, 0, 0, $wm_resize_width, $wm_resize_height, $wm_width, $wm_height);

			$wm_width = $wm_resize_width;
			$wm_height = $wm_resize_height;
			$h_d = ($original_height/$wm_t)-$wm_height;
			$h_ = $original_height/$wm_t;
			$x = 0;
			$y = 0;
			while( $x < $wm_t ){
				$y = 0;
				while( $y < $wm_t ){
					$pos_x = $x*$wm_resize_width;
					$pos_y = ($y*$h_)+$h_d;
					imagecopy($newim, $wimgnew, $pos_x,$pos_y, 0,0, $wm_width, $wm_height );
					$y = $y +1;
				}
				$x = $x+1;
			}
			//exit;
		}
		return $newim;
	}
	function add_text( $newim, $query ){

		$original_width = imagesx($newim);
		$original_height = imagesy($newim);
		$th = $query["t_s"]*2;

		$pos_x = 10;
		$pos_y = 10;

		$fnt = getcwd()."/arial-bold.ttf";
		$type_space = imagettfbbox((int)$query["t_s"], 0, $fnt, $query['t_t']);
		//print_r( $type_space );
		$txt_width = abs($type_space[4] - $type_space[0]) + 10;
		$txt_height = abs($type_space[5] - $type_space[1]) + 10;

		if( $query["t_p"] == "BL" ){
			$pos_x = round($original_width*.05);
			$pos_y = round($original_height*.95)- $txt_height;
		}
		if( $query["t_p"] == "BR" ){
			$pos_x = round($original_width*.05)-$txt_width;
			$pos_y = round($original_height*.95)- $txt_height;
		}
		if( $query["t_p"] == "TL" ){
			$pos_x = round($original_width*0.05);
			$pos_y = round($original_height*0.05);
		}
		if( $query["t_p"] == "TR" ){
			$pos_x = round($original_width*0.95)-$txt_width;
			$pos_y = round($original_height*0.05);
		}
		if( $query["t_c"] ){
			$x = explode(",",$query["t_c"]);
			$col = imagecolorallocate($newim,(int)$x[0],(int)$x[1],(int)$x[2]);
		}else{
			$col = imagecolorallocate($newim,0,0,0);
		}
		
		//echo "<div>" . $txt_width . " x " . $txt_height . "</div>";
		//echo "<div>" . $pos_x . " : " . $pos_y . " : " . $fnt . "</div>";
		//echo "<pre>";print_r( $query );exit;
		imagettftext($newim,(int)$query["t_s"],0,$pos_x,$pos_y,$col,$fnt,$query['t_t']);
		return $newim;
	}