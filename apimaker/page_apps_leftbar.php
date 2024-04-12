
	<a class="left_btn" href="<?=$config_global_apimaker_path ?>">APPs</a>
	<!-- <p style='padding:5px; margin:5px; color:#333; font-weight: 500;'><?=htmlspecialchars($app['app']) ?></p> -->
	<a class="left_btn <?=$config_param2=='home'||$config_param2==''?"left_btn_active":"" ?>" v-bind:href="path+'home'" style="font-weight: 500;"><?=htmlspecialchars($app['app']) ?></a>
	<a class="left_btn <?=$config_param2=='apis'?"left_btn_active":"" ?>" v-bind:href="path+'apis'">APIs</a>
	<a class="left_btn <?=$config_param2=='apis_global'?"left_btn_active":"" ?>" v-bind:href="path+'apis_global'">Global APIs</a>
	<a class="left_btn <?=$config_param2=='functions'?"left_btn_active":"" ?>" v-bind:href="path+'functions'">Functions</a>
	<a class="left_btn <?=$config_param2=='pages'?"left_btn_active":"" ?>" v-bind:href="path+'pages'">Pages</a>
	<a class="left_btn <?=$config_param2=='files'?"left_btn_active":"" ?>" v-bind:href="path+'files'">Files</a>
	<a class="left_btn <?=$config_param2=='global_files'?"left_btn_active":"" ?>" v-bind:href="path+'global_files'">Global Files</a>
	<a class="left_btn <?=$config_param2=='tables_dynamic'?"left_btn_active":"" ?>" v-bind:href="path+'tables_dynamic'">Internal Tables</a>
	<a class="left_btn <?=$config_param2=='tables_elastic'?"left_btn_active":"" ?>" v-bind:href="path+'tables_elastic'">Elastic Tables</a>
	<a class="left_btn <?=$config_param2=='databases'?"left_btn_active":"" ?>" v-bind:href="path+'databases'">Databases</a>
	<a class="left_btn <?=$config_param2=='auth'?"left_btn_active":"" ?>" v-bind:href="path+'auth'">Authentication</a>
	<a class="left_btn <?=$config_param2=='settings'?"left_btn_active":"" ?>" v-bind:href="path+'settings'">Settings</a>
	<a class="left_btn <?=$config_param2=='logs'?"left_btn_active":"" ?>" v-bind:href="path+'logs'">Logs</a>
	<a class="left_btn <?=$config_param2=='export'?"left_btn_active":"" ?>" v-bind:href="path+'export'">Backup/Restore</a>
