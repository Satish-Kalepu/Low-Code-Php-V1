<?php

	if( $config_param3 && $config_param4 ){

		if( $page_type == 'html' ){
			require("page_apps_pages_page_html.php");
		
		}else if( $page_type == 'dynamic' ){
			require("page_apps_pages_page_dynamic.php");
		
		}else if( $page_type == 'vuejs' ){
			require("page_apps_pages_page_vuejs.php");
		
		}else{
			echo '<P>Unknown page type</p>';
		}

	}else{
		require("page_apps_pages_home.php");
	}
