<style>
	.redisk{ padding:0px 5px; border-bottom:1px solid #ccc; cursor:pointer; }
	.redisk:hover{ background-color:#f8f8f8; }
</style>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div class="btn btn-sm btn-outline-secondary float-end" v-on:click="show_configure()" >Configure</div>
			<div class="h3 mb-3"><span class="text-secondary" >Key Value Store</span></div>

			<div v-if="saved&&settings['enable']" style="display:flex; height: 40px;">
				<div>
					<input type="text" class="form-control form-control-sm w-auto d-inline" v-model="keyword" placeholder="Key">
					<input type="button" class="btn btn-outline-dark btn-sm" value="Search" v-on:click="searchit()">
				</div>
			</div>
			<div style="position:relative;overflow: auto; height: calc( 100% - 130px );">

				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

				<div v-if="saved==false||settings['enable']==false" style="padding:50px; margin: 50px; border: 1px solid #ccc;" >
					<p>Key Value store is not enabled</p>
					<div class="btn btn-outline-dark btn-sm" v-on:click="show_configure()">Configure Redis</div>
				</div>

				<template v-else >

				<div style="display:flex; ">
					<div style="height:calc( 100% - 150px ); height: 30px; min-width:300px; padding:0px 20px; border:1px solid #ccc;">
						<div>Key</div>
					</div>
					<div style="height:calc( 100% - 150px ); height: 30px; min-width:300px; padding:0px 20px; border:1px solid #ccc; ">
						<div v-if="'key' in show_key" ><b>{{ show_key['key'] }}</b></div>
					</div>
				</div>

				<div style="display:flex; ">
					<div style="height:calc( 100% - 150px ); min-height: 300px; min-width:300px; overflow:auto; padding:0px 20px; border:1px solid #ccc;">
						<div class="redisk" v-for="k in keys" v-on:click="load_key(k)">{{ k }}</div>
					</div>
					<div style="height:calc( 100% - 150px ); min-width:300px; overflow:auto; padding:0px 20px; border:1px solid #ccc; min-height: 300px;">

						<div v-if="'data' in show_key==false" >Loading</div>
						<div v-if="'data' in show_key" >
							<div>Type: {{ show_key['data']['type'] }}</div>
							<div>TTL: {{ show_key['data']['ttl'] }}</div>
							<div>Data: </div>
							<div v-if="'data' in show_key['data']">
								<pre v-if="show_key['data']['type']=='string'" >{{ show_key['data']['data'] }}</pre>
								<table v-else-if="show_key['data']['type']=='hash'" class="table table-bordered table-sm w-auto" >
									<tr v-for="d,k in show_key['data']['data']" >
										<td>{{ k }}</td><td><pre>{{ d }}</pre></td>
									</tr>
								</table>
								<pre v-else>{{ show_key['data']['data'] }}</pre>
							</div>
						</div>

					</div>
				</div>


				</template>

			</div>

		</div>
	</div>


		<div class="modal fade" id="settings_modal" tabindex="-1" >
		  <div class="modal-dialog model-sm">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Settings</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

				<div v-if="smsg" class="alert alert-primary" >{{ smsg }}</div>
				<div v-if="serr" class="alert alert-danger" >{{ serr }}</div>

				<table class="table table-bordered table-sm w-auto">
					<tr>
						<td>Host</td>
						<td><input v-model="settings['host']" type="text" class="form-control form-control-sm" placeholder="Host" >
					</tr>
					<tr>
						<td>Port</td>
						<td><input v-model="settings['port']" type="number" class="form-control form-control-sm" placeholder="Port" >
					</tr>
					<tr>
						<td>Username</td>
						<td><input v-model="settings['username']" type="text" class="form-control form-control-sm" placeholder="Username" >
					</tr>
					<tr>
						<td>Host</td>
						<td><input v-model="settings['password']" type="text" class="form-control form-control-sm" placeholder="Password" >
					</tr>
					<tr>
						<td>TLS</td>
						<td><input v-model="settings['tls']" type="checkbox" >
					</tr>
					<tr>
						<td>Enable</td>
						<td><input v-model="settings['enable']" type="checkbox" >
					</tr>
					<tr>
						<td></td>
						<td><input type="button" class="btn btn-outline-dark btn-sm" value="SAVE" v-on:click="saveit">
					</tr>
				</table>

		      </div>
		    </div>
		  </div>
		</div>


</div>


<script>

var app = Vue.createApp({
		"data": function(){
			return {
				"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
				"redispath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/redis/",
				"app_id" : "<?=$app['_id'] ?>",
				"settings": <?=json_encode($app['internal_redis']) ?>,
				"smsg": "", "serr":"","msg": "", "err":"","kmsg": "", "kerr":"",
				keyword: "",
				token: "",
				saved: <?=($saved?"true":"false") ?>,
				keys: [], popup: false,
				show_key: {}
			};
		},
		mounted:function(){
			if( this.saved && this.settings['enable'] ){
				this.load_keys();
			}
		},
		methods: {
			show_configure: function(){
				this.popup = new bootstrap.Modal(document.getElementById('settings_modal'));
				this.popup.show();
			},
			load_key: function(k){
				this.show_key = {
					"key": k+'',
					"s": false
				};
				this.kmsg = "Loading...";
				this.kerr = "";
				axios.post("?", {
					"action" 		: "redis_load_key",
					"key": k,
				}).then(response=>{
					this.kmsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.show_key['data'] = response.data['data'];
								}else{
									this.kerr = response.data['error'];
								}
							}else{
								this.kerr = "Invalid response";
							}
						}else{
							this.kerr = "Incorrect response";
						}
					}else{
						this.kerr = "http:"+response.status;
					}
				}).catch(error=>{
					this.kerr = error.message;
				});
			},
			load_keys: function(){
				var k = "";
				if( this.keyword != "" ){
					k = this.keyword+'';
				}

				this.msg = "Loading...";
				axios.post("?", {
					"action" 		: "redis_load_keys",
					"keyword": k,
				}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.keys = response.data['keys'];
									for(var i=0;i<this.keys.length;i++){
										this.keys.splice(i,1);break;
									}
								}else{
									this.err = response.data['error'];
								}
							}else{
								this.err = "Invalid response";
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "http:"+response.status;
					}
				}).catch(error=>{
					this.err = error.message;
				});
			},
			saveit: function(){
				this.smsg = "Saving...";
				this.serr = "";
				axios.post("?",{
					"action" 		: "redis_save_settings",
					"settings": this.settings,
				}).then(response=>{
					this.smsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.smsg = "Saved";
									this.saved = true;
								}else{
									this.serr = response.data['error'];
								}
							}else{
								this.serr = "Invalid response";
							}
						}else{
							this.serr = "Incorrect response";
						}
					}else{
						this.serr = "http:"+response.status;
					}
				}).catch(error=>{
					this.serr = error.message;
				});
			}
		}
}).mount("#app");
</script>
