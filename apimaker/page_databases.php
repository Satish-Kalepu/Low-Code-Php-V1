<?php
	if( $config_param3 ){
		if( $config_param4 == "table" && $config_param5 ){
			if( $config_param6 == "manage" ){
				require("page_databases_tables_".$db['engine']."_manage.php");

			}else if( $config_param6== "" || $config_param6 == "records" ){
				require("page_databases_tables_".$db['engine']."_browse.php");

			}else if( $config_param6 == "import" ){
				require("page_databases_tables_".$db['engine']."_import.php");

			}else if( $config_param6 == "structure" ){
				require("page_databases_tables_".$db['engine']."_structure.php");

			}else if( $config_param6 == "export" ){
				require("page_databases_tables_".$db['engine']."_export.php");

			}else{
				require("page_databases_tables_".$db['engine'].".php");

			}
		}else{
			require("page_databases_database_".$db['engine'].".php");
		}
	}else{
		require("page_databases_home.php");
	}

