<?php 

if( $config_param2 == "" ){
	require("page_apps_home.php");
}else if( $config_param2 == "apis"){
	if( $config_param3 && $config_param4 ){
		require("page_apps_apis_api.php");
	}else{
		require("page_apps_apis_home.php");
	}
}else if( $config_param2 == "apis_global"){
	require("page_apps_apis_global.php");
}else if( $config_param2 == "codeeditor" ){
	require("page_apps_codeeditor.php");
}else if( $config_param2 == "functions"){
	if( $config_param3 && $config_param4 ){
		require("page_apps_functions_function.php");
	}else{
		require("page_apps_functions_home.php");
	}
}else if( $config_param2 == "settings" ){
	require("page_apps_settings.php");

}else if( $config_param2 == "tables_dynamic" ){
	require("page_apps_tables_dynamic.php");

}else if( $config_param2 == "tables_elastic" ){
	require("page_apps_tables_elastic.php");

}else if( $config_param2 == "databases" ){
	require("page_databases.php");

}else if( $config_param2 == "plugins" ){
	require("page_apps_plugins.php");

}else if( $config_param2 == "pages" ){
	if( $config_param3 && $config_param4 ){
		require("page_apps_pages_page.php");
	}else{
		require("page_apps_pages_home.php");
	}

}else if( $config_param2 == "pages_v2" ){
	if( $config_param3 && $config_param4 ){
		require("page_apps_pages_v2_page.php");
	}else{
		require("page_apps_pages_v2_home.php");
	}

}else if( $config_param2 == "pages_v3" ){
	if( $config_param3 && $config_param4 ){
		require("page_apps_pages_v3_page.php");
	}else{
		require("page_apps_pages_v3_home.php");
	}

}else if( $config_param2 == "files" ){
	if( $config_param3 && $config_param4 ){
		require("page_apps_files_file.php");
	}else{
		require("page_apps_files_home.php");
	}
}else if( $config_param2 == "global_files" ){
	if( $config_param3 ){
		require("page_apps_global_files_file.php");
	}else{
		require("page_apps_global_files_home.php");
	}
}else if( $config_param2 == "logs" ){
	if( $config_param3 && $config_param4 ){
		require("page_apps_logs_file.php");
	}else{
		require("page_apps_logs_home.php");
	}
}else if( $config_param2 == "export" ){
	require("page_apps_export.php");
}else if( $config_param2 == "auth" ){
	require("page_apps_auth.php");
}else if( $config_param2 == "storage" ){
	require("page_apps_storage_vaults.php");

}else if( $config_param2 == "redis" ){
	require("page_apps_redis.php");

}else if( $config_param2 == "objects" ){
	require("page_apps_objects.php");

}else if( $config_param2 == "codeexport" ){
	require("page_apps_codeexport.php");

}else if( $config_param2 == "events" ){
	require("page_apps_events.php");

}else if( $config_param2 == "tasks" ){
	require("page_apps_tasks.php");

}else if( $config_param1 ){
	require("page_apps_home.php");
}

