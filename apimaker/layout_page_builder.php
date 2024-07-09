<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?=$meta_title?$meta_title:"API Maker" ?></title>
	<link rel="stylesheet" href="<?=$config_global_apimaker_path ?>bootstrap/bootstrap.min.css" >
	<script src="<?=$config_global_apimaker_path ?>bootstrap/bootstrap.bundle.min.js"></script>
	<script src="<?=$config_global_apimaker_path ?>bootstrap/popper.min.js"></script>
	<script src="<?=$config_global_apimaker_path ?>js/vue3.min.prod.js"></script>
	<script src="<?=$config_global_apimaker_path ?>js/axios.min.js"></script>
	<link rel="stylesheet" href="<?=$config_global_apimaker_path ?>common.css" />
	<link rel="stylesheet" href="<?=$config_global_apimaker_path ?>fontawesome/font-awesome.min.css" />
</head>
<body>

<div style="background-color:white; position: fixed; width:100%; padding:5px; height: 40px; border-bottom:1px solid #999;" >
	<?php if( $_SESSION['apimaker_login_ok'] ){ ?>
	<div style="float:right; padding:0px 10px;" >
		<a href="<?=$config_global_apimaker_path ?>settings" class="btn btn-outline-dark btn-sm" >Settings</a>&nbsp;
		&nbsp;<a href="?action=logout" class="btn btn-outline-dark btn-sm" >Logout</a>
	</div>
	<?php } ?>
	<div class="text-dark" style="font-weight:500; padding-left:10px;"><?=$config_global_apimaker['config_app_name'] ?></div>
</div>
<div style="height:50px;">&nbsp;</div>

<?php	
	if( $config_page != "" ){
		$pagefile = "page_" . $config_page . ".php";
		if( file_exists( $pagefile ) ){
			include_once( $pagefile );
		}else{
			echo "<p>Page not found!</p>";
		}
	}
?>

</body>
</html>