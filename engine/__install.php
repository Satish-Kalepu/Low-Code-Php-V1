<?php

//error_reporting(4798);
//ini_set("display_errors", "On");
//ini_set("display_startup_errors", "On");

// echo "<pre>";
// print_r( $_SERVER );
// echo __DIR__;
// exit;

$engine_dir = __DIR__;
$engine_folder = array_pop(explode("/", $engine_dir));
if( !$engine_folder ){
	$engine_folder = array_pop(array_pop(explode("/", $engine_dir)));
}

$v = pathinfo( $_SERVER['PHP_SELF'] );
if( !isset($v['dirname']) || $v['dirname'] == "/" ){
	echo "No configuration found!<BR>Please follow installation procedures";exit;
}
$config_engine_path = $v['dirname'] . "/";

$loc = [
	"./config_global_engine.php",
	"../config_global_engine.php",
	"../../config_global_engine.php",
	"/var/tmp/config_global_engine.php",
];
$locc=0;
$file_loc = "";
$loc_found = false;
foreach( $loc as $j ){ if(file_exists($j)){$locc++;$file_loc=$j;$loc_found=true;} }

if( $locc > 1 ){
	?>
	<html><head><title>Php LowCode Engine Processor V1 Installation</title></head>
	<body>
		<p><b>Php LowCode Engine Processor Installation Step 0 </b></p>
		<p style="color:red;">Multipe Configuration files found. Please keep only one. </p>
		<p>You can put the config file in following locations:</p>
		<ul>
			<li>./config_global_engine.php (current folder)</li>
			<li>../../config_global_engine.php (outside codebase document root)</li>
			<li>/var/tmp/config_global_engine.php (system tmp folder)</li>
		</ul>
	</body>
	</html>
	<?php
	exit;
}

if( $file_loc == "" ){
	$file_loc = "../../config_global_engine.php";
}

function get_timezones(){
	$timezone_identifiers = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
	return $timezone_identifiers;
}

$variables = [
	"config_engine_key" 			=> "",
	"config_engine_app_id" 			=> "",
	"config_apimaker_endpoint_url"	=> "https://".$_SERVER['HTTP_HOST']. "/apimaker/",
	"config_engine_path"			=> "/engine/",
];

if( $loc_found ){
	require($file_loc);
	if( is_array($config_global_apimaker_engine) ){
		$variables = $config_global_apimaker_engine;
	}
}

if( $_POST['action'] == "saveconf" ){

	function get_request($options){
		$options['headers'][] = "User-Agent: PHP LowCode Engine";
		//print_r( $options );exit;
		$headers = [];
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $options['url'],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_TIMEOUT => 2,
		  CURLOPT_CONNECTTIMEOUT=>1,
		  CURLOPT_FOLLOWLOCATION => false,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HEADER => true,
		  CURLOPT_HTTPHEADER => $options['headers']?$options['headers']:[],
		));
		$response = curl_exec($curl);
		$info = curl_getinfo($curl);
		$erno = curl_errno($curl);
		if( $erno ){
			$err = $erno . ":" . curl_error($curl);
		}else{$err="";}
		curl_close( $curl );
		if( $erno ){
			$status = "error";$body = "";
		}else{
			$resp = explode("\r\n\r\n", $response);
			$body = $resp[ sizeof($resp)-1 ];
			$hh = $resp[ sizeof($resp)-2 ];
			$status = $info['http_code'];
			$hl = explode("\n", $hh);
			foreach($hl as $hi=>$hv){
				$hp = explode(':', $hv, 2);
				if (count($hp) < 2){continue;}
				$headers[strtolower(trim($hp[0]))] = trim($hp[1]);
			}
		}
		return [
			"status"=>$status,
			"headers"=>$headers,
			"body"=>$body,
			"error"=>$err
		];
	}

	$data = json_decode($_POST['data'],true);
	if( !is_array($data) || json_last_error() ){
		echo json_encode(["status"=>"error", "error"=>"Data decode failed"]);exit;
	}
	$url = $data['config_apimaker_endpoint_url'] . "config_api/get";

	$res = get_request([
		"url"=>$url,
		"headers"=>[
			'APPID: ' . $data["config_engine_app_id"],
	    	'APPKEY: ' . $data["config_engine_key"],
	    	'DOMAIN: ' . $_SERVER['HTTP_HOST'],
		],
	]);
	if( $res['status'] != 200 ){
		echo json_encode(["status"=>"error", "error"=>$res['status'] . ": " . $res['error'], "url"=>$url, "res"=>$res ]);exit;
	}else{
		$body = json_decode($res['body'],true);
		if( !$body || json_last_error() ){
			echo json_encode(["status"=>"error", "error"=>"incorrect response: " . $res['body'] ]);exit;
		}else if( !isset($body['status']) || $body['status'] != "success" ){
			echo json_encode(["status"=>"error", "error"=>"response: " . $body['error'] ]);exit;
		}
	}

	if( isset($config_global_engine) ){
		if( $_POST['force_update'] === "true" ){}else{
			echo json_encode([
				"status"=>"dbinitialized", "error"=>"Configuration is already initialized. Do you want to replace?"
			]);exit;
		}
	}

	$vstr = '<'.'?'."php\n\n";
	$vstr .= "/* Last updated on " . date("Y-m-d H:i:s") . "*/\n\n";
	$vstr .= '$config_global_apimaker_engine = ' . var_export($data,true) . ";\n\n";

	try{
		file_put_contents( $_POST['config_path'], $vstr );
		chmod( $_POST['config_path'], 0777 );
	}catch(Exception $ex){
		echo json_encode(["status"=>"error", "error"=>"Failed to create file ". $ex->getMessage() ]);exit;
	}
	if( !file_exists( $_POST['config_path'] ) ){
		echo json_encode(["status"=>"error", "error"=>"Failed to create file" ]);exit;
	}

	echo json_encode(["status"=>"success", "msg"=>"OK" ]);

	exit;
}

// print_r( $variables );

	?>
	<html><head><title>Php LowCode Engine Processor V1 Installation</title></head>
	<body>
		<style>
			* { font-family:Arial; font-size:12px; }
			td{ border:0.5px solid #ccc; }
			body{ margin:50px; }
		</style>
		<script src="www/vue3.min.prod.js" ></script>
		<p><b>Php LowCode Engine Processor Installation Step 1 </b></p>
		<div id="app" >

		<div style="padding:10px 0px;">
			<div v-if="loc_found" >
				<p>Config file location: {{ config_path }}</p>
			</div>
			<div v-else >
				<p>Config file location: <select v-model="config_path" >
					<option value="./config_global_engine.php" >./config_global_engine.php </option>
					<option value="../config_global_engine.php" >../config_global_engine.php (Recommended)</option>
					<option value="../../config_global_engine.php" >../../config_global_engine.php</option>
					<option value="/var/tmp/config_global_engine.php" >/var/tmp/config_global_engine.php</option>
					</select>
				</p>
			</div>
			<p style="color:gray;" >Storing config file in the code repository is prone to leakage of credentials and server takeover. <BR>Choose a parent folder above web server public document root or system tmp folder.</p>
		</div>

		<table cellpadding="5"  cellspacing="0" border="0" style="border-collapse:collapsed;">
			<tr style="position: sticky; top:0px; background-color: #fec;">
				<td>Variable</td>
				<td>Value</td>
			</tr>
			<tr valign="top">
				<td>Engine Key</td>
				<td>
					<input type="text" v-model="variables['config_engine_key']" style="width:400px;" >
					<div style="color:gray;">Key must be generated in the Maker APP Settings</div>
					<div v-if="errs['config_engine_key']" style="color:red;">{{ errs['config_engine_key'] }}</div>
				</td>
			</tr>
			<tr valign="top">
				<td>APP ID</td>
				<td>
					<input type="text" v-model="variables['config_engine_app_id']" style="width:400px;" >
					<div v-if="errs['config_engine_app_id']" style="color:red;">{{ errs['config_engine_app_id'] }}</div>
				</td>
			</tr>
			<tr valign="top">
				<td>Maker API URL</td>
				<td>
					<input type="text" v-model="variables['config_apimaker_endpoint_url']" style="width:400px;" >
					<div style="color:gray;">URL of engine maker system</div>
					<div v-if="errs['config_apimaker_endpoint_url']" style="color:red;">{{ errs['config_apimaker_endpoint_url'] }}</div>
				</td>
			</tr>
			<tr valign="top">
				<td>Engine Path</td>
				<td>
					<input type="text" v-model="variables['config_engine_path']" >
					<div style="color:gray;">default: /engine/. must start and end with slash</div>
					<div v-if="errs['config_engine_path']" style="color:red;">{{ errs['config_engine_path'] }}</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div><input type="button" value="VALIDATE" v-on:click="submit" ></div>
					<div v-if="voption==1" >
						<label><input type="checkbox" v-model="force_update"  > Do you want to replace previous installation? </label>
					</div>
					<div v-if="voption==2" >
						<label><input type="checkbox" v-model="force_update" > Do you want to ignore installation which already exists with different prefix? </label>
					</div>
					<div v-if="err" style="color:red; padding: 10px; margin:10px 0px; border:1px solid orange;" v-html="err" ></div>
					<div v-if="msg" style="color:blue; padding: 10px; margin:10px 0px; border:1px solid #333;" v-html="msg" ></div>
				</td>
			</tr>
		</table>
		</div>
		<script>
		var app = Vue.createApp({
			data: function(){
				return {
					"timezones": <?=json_encode(get_timezones()) ?>,
					"variables": <?=json_encode($variables) ?>,
					"msg": "",
					"err": "",
					"errs": {},
					"config_path": "<?=$file_loc ?>",
					"loc_found": <?=$loc_found?"true":"false" ?>,
					"voption": -1,
					"force_update": false,
				};
			},
			mounted: function(){
				var e = {};
				for(var k in this.variables ){
					e[ k ] = "";
				}
				this.errs = e;
			},
			methods: {
				submit: function(){
					this.err = "";
					var e = false;
					if( this.variables['config_engine_key'].match(/^[0-9a-f]{24}$/) == null ){
						this.errs[ 'config_engine_key' ] = "Empty or Incorrect key";
						e = true;
					}
					if( this.variables['config_engine_app_id'].match(/^[0-9a-f]{24}$/) == null ){
						this.errs[ 'config_engine_app_id' ] = "Empty or Incorrect key";
						e = true;
					}
					try{
						new URL(this.variables['config_apimaker_endpoint_url']);
					}catch(e){
						this.errs[ 'config_apimaker_endpoint_url' ] = "Incorrect URL";
						e = true;
					}
					if( this.variables['config_engine_path'].match(/^\/[0-9a-z\-\_\.]{2,50}\/$/i) == null ){
						this.errs[ 'config_engine_path' ] = "Incorrect Path. ";
						e = true;
					}
					if( e ){
						this.err = "Please correct values of items where errors are shown";	
					}
					if( this.err == "" ){
						this.submit2();
					}
				},
				submit2: function(){
					setmsg = this.setmsg;
					seterr = this.seterr;
					setoption = this.setoption;
					this.msg = "Submitting...";
					vpost = "action=saveconf&force_update="+this.force_update+"&config_path="+encodeURIComponent(this.config_path)+"&data="+encodeURIComponent(JSON.stringify(this.variables));
					var con = new XMLHttpRequest();
					con.open("POST","?", true);
					con.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					con.onload = function(){
						setmsg("");
						console.log( this.responseText );
						try{
							var d = JSON.parse(this.responseText);
							if( 'status' in d == false ){
								seterr( "Invalid Response: status tag missing" );
							}else if( d['status'] == "success" ){
								console.log("ok");
								setmsg( "Successfully Updated. <BR>For security reasons, please delete __install.php<BR>start using application and you can manually tweak the config file as required." );
							}else if( d['status'] == "dbinitialized" ){
								setoption( 1 );
								seterr( "Configuration file is already exists. Do you want to replace?" );
							}else{
								setoption( 0 );
								seterr( "Error: " + d['error'] );
							}
						}catch(e){
							seterr( "Incorrect Response: " + e );
						}
					}
					con.send(vpost);
				},
				setmsg: function(m){this.msg = m;},
				seterr: function(m){this.err = m;},
				setoption: function(v){this.voption = v;},
			}
		});
		app.mount("#app");
		</script>
	</body>
	</html>
	<?php

