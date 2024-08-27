<?php

// $fn = "/text/filename.txt";
// print_r( explode("/", $fn) );exit;

/* backend maker */
//echo "<pre>";print_r( $_SERVER );exit;

$current_dir = __DIR__;
$config_paths = [
	"./config_global_apimaker.php",
	"../config_global_apimaker.php",
	"../../config_global_apimaker.php",
	"/var/tmp/config_global_apimaker.php"
];
foreach( $config_paths as $j ){if( file_exists($j) ){
		require($j);break;
}}
if( !$config_global_apimaker ){
	if( $_SERVER['REQUEST_METHOD'] == "GET" ){
		if( file_exists("__install.php") ){
			$v = pathinfo($_SERVER['PHP_SELF'] );
			if( !isset($v['dirname']) ){
				echo "No configuration found!<BR>Please follow installation procedures";exit;
			}
			header("Location: " . $v['dirname']. "/__install.php");
			exit;
		}
	}else{
		http_response_code(500);
		echo json_encode(["status"=>"fail","error"=>"APP not configured"]);exit;
	}
}
require_once( "config.php" );

require( "common_functions.php" ); /* here because of echo403 function */
require( "control_config.php" );

$request_uri = $_GET['request_url']?$_GET['request_url']:"";
$parts = parse_url( $request_uri );
$x = explode( "/" , $parts['path'] );

$config_page = $x[0];
if(sizeof($x)>1){$config_param1=$x[1];}
if(sizeof($x)>2){$config_param2=$x[2];}
if(sizeof($x)>3){$config_param3=$x[3];}
if(sizeof($x)>4){$config_param4=$x[4];}
if(sizeof($x)>5){$config_param5=$x[5];}
if(sizeof($x)>6){$config_param6=$x[6];}
if(sizeof($x)>7){$config_param7=$x[7];}
if(sizeof($x)>8){$config_param8=$x[8];}

session_start();
if( !isset($_SESSION['time']) ){
	$_SESSION['time'] = time();
}
if( $_SERVER['REQUEST_METHOD'] != "POST" ){
	$d = time()-$_SESSION['time'];
	if( $d > 300 ){
		if( $config_global_apimaker['config_session_timeout'] > $d ){
			session_regenerate_id();
			$_SESSION['time'] = time();
		}
	}
}

// if( isset($config_session_name) ){
// 	if( isset($_COOKIE[$config_session_name]) ){
// 		session_start();
// 		if( $_SESSION['ua'] ){
// 			if( $_SESSION['ua'] != $_SERVER['HTTP_USER_AGENT'] || $_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] ){
// 				session_destroy();
// 				session_regenerate_id();
// 				header("Location: " . $config_global_apimaker_path . "?event=Session_unRecognised");
// 				exit;
// 			}
// 		}
// 		if( !$_POST['action'] && !$_GET['action'] ){
// 			if( $config_global_apimaker['config_use_https_only'] ){
// 				setcookie($config_session_name, session_id(), (time()+(int)$config_global_apimaker[ 'config_session_timeout']), "/", "", TRUE, TRUE);
// 			}else{
// 				setcookie($config_session_name, session_id(), (time()+(int)$config_global_apimaker[ 'config_session_timeout']), "/");
// 			}
// 		}
// 	}else{
// 		unset($_GET[ $config_session_name ]);
// 		session_start();
// 		$_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];
// 		$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
// 	}
// }

$g =$_GET;
unset($g['request_url']);
$ac = [
	"ip"=>$_SERVER['REMOTE_ADDR'],
	"m"=>$_SERVER['REQUEST_METHOD'],
	"u"=>$_SERVER['REQUEST_URI'],
	"ct"=>$_SERVER['CONTENT_TYPE'],
	"ua"=>$_SERVER['HTTP_USER_AGENT'],
	"s"=>"http",
	"sid"=>session_id()
];
if( sizeof($g) ){
	$ac['g'] = $g;
}
if( sizeof($_POST) ){
	$ac['p'] = $_POST;
}
$res = $mongodb_con->insert( $config_global_apimaker['config_mongo_prefix'] . "_zlog_actions" , $ac);
$action_log_id = $res['inserted_id'];

if( $config_page == "config_api" ){
	require("page_config_api_controll.php");
	exit;
}
if( $config_page == "engine_api" ){
	require("page_engine_api_controll.php");
	exit;
}

if( $config_page == "" && $_SESSION['apimaker_loggedin'] == 'y' ){
	$config_page = "home";
}else if(  $config_page == "" ){
	$config_page = "login";
}

if( isset($_GET['check']) && $_GET['check'] == 'session' ){
	if( isset($_SESSION['apimaker_loggedin']) ){if( $_SESSION['apimaker_loggedin'] != 'y' ){
	 	echo403json();
	}}else{
		echo403json();
	}
}

require( "common_dos_blocker.php" );

if( $_GET['action'] == "logout" ){
	event_log( "system", "logout" );
	session_destroy();
	session_regenerate_id();
	header("Location: " . $config_global_apimaker_path . "login?event=Logout");
	exit;
}

if( isset($config_global_apimaker['config_allow_concurrent_login']) ){
if( $config_global_apimaker['config_allow_concurrent_login'] === false ){
	if( $_SESSION['apimaker_loggedin'] == 'y' ){ // concurrent login check
		if( $_POST['action'] != "login" ){
			$col = $config_global_apimaker['config_mongo_prefix'] . "_vars";
			$res = $mongodb_con->find_one( $col, [ "_id"=> "login_session" ] );
			if( $res['status'] != 'success' ){
				echo500("Concurrent session check: DB Error: " . $res['error']);
			}
			if( $res['data']['value'] != session_id() ){
				event_log( "system", "forcelogout", ["reason"=>"incorrect session"] );
				session_destroy();session_regenerate_id();
				header("Location: " . $config_global_apimaker_path . "login?event=SessionTakeOver");exit;
			}
			$res = $mongodb_con->update_one( $col, [ "_id"=> "login_session" ], ['time'=>date("Y-m-d H:i:s") ] );
			if( $res['status'] != 'success' ){
				echo500("Concurrent session check: DB Error: " . $res['error']);
				exit;
			}
		}
	}
}}

if( !$_SESSION['apimaker_login_ok'] ){
	if( $config_page != "login" && $config_page != "captcha" && $config_page != "install"  && $config_page != "config_api" ){
		if( $_POST['action'] || $_GET['action'] ){
			json_response("fail", "SessionExpired");
		}
		header("Location: " . $config_global_apimaker_path . "login?event=SessionExpired");
		exit;
	}
}

if( $_SESSION['apimaker_login_ok'] && $config_page == "login" ){
	header("Location: " . $config_global_apimaker_path . "home");
	exit;
}

if( $_SESSION['apimaker_login_ok'] ){
	$res = $mongodb_con->find_one( $config_global_apimaker['config_mongo_prefix'] . "_users", [
		'_id'=>$_SESSION['apimaker_login_id']
	], [
		'projection'=>['password'=>false]
	]);
	if( $res['data'] ){
		// check if user active;
		$login_user = $res['data'];
	}else{
		event_log( "system", "forcelogout", ["reason"=>"incorrect session user_id"] );
		session_destroy();
		session_regenerate_id();
		header("Location: " . $config_global_apimaker_path . "login?event=UserNotFound");
		exit;
	}
}

if( !isset($db_prefix) ){
	$db_prefix = $config_global_apimaker['config_mongo_prefix'];
}

require_once( "common_controll.php" );

if( $config_page != "" ){
	$controllerfile = "page_" . $config_page . "_controll.php";
	if( file_exists( $controllerfile ) ){
		require_once( $controllerfile );
	}
}

if( $config_page == "codeeditor" ){
	require("layout_simple.php");
}else if( $config_layout ){
	require("layout_".$config_layout.".php");
}else{
	require("layout_main.php");
}

