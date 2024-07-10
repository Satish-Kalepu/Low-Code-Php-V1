<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >
			<div style="float:right;" >
				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>
			</div>
			<div style="float:right;" >
				<button class="btn btn-outline-secondary btn-sm ms-1" disabled >Create</button>
			</div>
			<div class="h3 mb-3">Pluggins</div>
			<div style="height: calc( 100% - 100px ); overflow: auto;" >
				<p>Coming sooon</p>
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
			token: "",
		};
	},
	mounted(){
		this.load_files();
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
		},
	}
}).mount("#app");
</script>
