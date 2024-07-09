<style>
	div.vid{ padding:0px 2px; cursor:pointer; }
	div.vid pre.vid{display: none; position: absolute; background-color: white; padding: 3px; border: 1px solid #aaa;}
	div.vid:hover pre.vid{display: block;}
</style>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:165px; top:60px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; "  >
		<div style="padding: 10px;" >
			<div class="h3 mb-3 w-auto">Global Files</div>
			<div>
				<p>Engine Environment: <select v-model="domain" v-on:click="select_test_environment__" >
						<option v-for="d,i in app__['settings']['domains']" v-bind:value="d['domain']" >{{ d['domain'] }}</option>
					</select>
				</p>
				<p>{{ test_url__ }}</p>
			</div>
			<div style="height: calc( 100% - 150px ); overflow: auto;"  >
			<table class="table table-striped table-bordered table-sm" >
				<tr style="position:sticky;top:0px;" class="bg-white">
					<td>ID</td>
					<td>Name/Path</td>
					<td></td>
				</tr>
				<template v-for="v,i in files">
				<tr>
					<td width="80%">
						<a v-bind:href="test_url__+v['name']" target="_blank" v-bind:disabled="domain==''" >{{ v['name'] }}</a>
					</td>
					<td nowrap>
						{{ v['type'] }}
					</td>
				</tr>
				</template>
			</table>
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
			files: <?=json_encode($config_global_files) ?>,
			test_url__: "<?=$test_url ?>",
			domain: "<?=$domain ?>",
		};
	},
	mounted(){
		this.app__
	},
	methods: {
		select_test_environment__: function(){
			setTimeout(this.select_test_environment__2,200);
		},
		select_test_environment__2: function(){
			for( var d in this.app__['settings']['domains'] ){
				if( this.app__['settings']['domains'][ d ]['domain'] == this.domain ){
					var tu = this.app__['settings']['domains'][ d ]['url'] 
					//+ "?version_id=<?=$config_param4 ?>&test_token=<?=md5($config_param4) ?>";
					this.test_url__ = tu;
					break;
				}
			}
		},
		getc: function(v){
			if( 'm_i' in v ){
				return v['m_i'].substr(0,10);
			}return "";
		},
		getsz: function(v){
			if( v < 1024 ){
				return (v) + " b";
			}else if( v/1024 < 1024 ){
				return (v/1024).toFixed(2) + " kb";
			}else if( v/1024/1024 < 1024 ){
				return (v/1024/1024).toFixed(2) + " mb";
			}else{
				return v;
			}
		},		
	}
}).mount("#app");
</script>