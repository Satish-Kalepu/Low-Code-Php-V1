<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >
			<div style="float:right;" >
			</div>
			<div style="float:right;" >

			</div>
			<div class="h3 mb-3">Elastic Tables</div>
			<div style="height: calc( 100% - 100px ); overflow: auto;" >

				<p><strong>Coming soon</strong></p>

				<p>Elastic tables are large tables with high scalability with infinate storage capacity. Based on DynamoDb/Cassandra clusters</p>
				<p>Removes complexity of configuring, deploying, managing, cost controlling, and without having to deal with steep learning curve</p>
				
			</div>
		</div>
	</div>

</div>
<script>
var app = Vue.createApp({
	data(){
		return {
			path: "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			app_id: "<?=$app['_id'] ?>",
			app__: <?=json_encode($app) ?>,
			msg: "",
			err: "",
			cmsg: "",
			cerr: "",
		};
	},
	mounted(){
	},
	methods: {
		is_token_ok(t){
			if( t!= "OK" && t.match(/^[a-f0-9]{24}$/)==null ){
				setTimeout(this.token_validate,100,t);
				return false;
			}else{
				return true;
			}
		},
		token_validate(t){
			if( t.match(/^(SessionChanged|NetworkChanged)$/) ){
				this.err = "Login Again";
				alert("Need to Login Again");
			}else{
				this.err = "Token Error: " + t;
			}
		}
	}
}).mount("#app");
</script>

