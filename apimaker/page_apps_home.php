<div id="app" >
	<div  class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >
			<div style="float:right;" >
				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>
			</div>
			<div class="h3 mb-3">{{ app__['app'] }} - <span class="text-secondary" >APP Dashboard</span></div>

			<div class="row" >
				<div class="col-6" >
					<div style="border:1px solid #ccc; min-height: 150px; text-align: center; line-height: 100px;">Usage Graph</div>
				</div>
				<div class="col-6" >
					<div style="border:1px solid #ccc; min-height: 150px; text-align: center; line-height: 100px;">Users</div>
				</div>
			</div>
			<div>&nbsp;</div>
			<div class="row" >
				<div class="col-6" >
					<div style="border:1px solid #ccc; min-height: 150px; text-align: center; line-height: 100px;">Settings</div>
				</div>
				<div class="col-6" >
					<div style="border:1px solid #ccc; min-height: 150px; text-align: center; line-height: 100px;">Request Log</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var app = Vue.createApp({
	data(){
		return {
			app_id: "<?=$config_param1 ?>",
			path: "<?=$config_global_apimaker_path ?>apps/<?=$config_param1 ?>/",
			app__: <?=json_encode($app) ?>,
			msg: "", 
			err: "",
		};
	},
	mounted(){
		
	},
	methods: {
		
	}
}).mount("#app");
</script>