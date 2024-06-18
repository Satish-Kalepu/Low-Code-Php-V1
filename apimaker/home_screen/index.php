<?php  
$request_uri = $_REQUEST['request_url']?$_REQUEST['request_url']:"";
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

if( $config_page == "" && $_SESSION['apimaker_loggedin'] == 'y' ){
	header("Location: " . $config_global_apimaker_path . "home");
	exit;
}

if( $_SESSION['apimaker_login_ok'] && $config_page == "login" ){
	header("Location: " . $config_global_apimaker_path . "home");
	exit;
}

require("page_main.php"); 

?>