<style>
	div.u{ max-width:400px; overflow:auto; }
	div._id{}
	div._id div{ display:none; }
	div._id:hover div{ display:block;position:absolute;background-color:white;box-shadow:1px 1px 2px #999; padding:5px; }
</style>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >
			<div class="h3 mb-3">Logs</div>
			<div style="height: calc( 100% - 100px ); overflow: auto;" >

				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

					<table class="table table-bordered table-striped table-hover table-sm ">
					<tbody>
						<tr>
							<td>#</td>
							<td>Date</td>
							<td>IP</td>
							<td>Schme</td>
							<td>Method</td>
							<td>URL</td>
							<td>Type</td>
							<td>Status</td>
						</tr>
						<tr v-for="d,i in records" style="cursor: pointer;">
							<td><div class="_id"><span>#</span><div>{{ d['_id'] }}</div></div></td>
							<td v-on:click="showit(i)" nowrap>{{ dateFromObjectId(d['_id']) }}</td>
							<td v-on:click="showit(i)" nowrap>{{ d['ip'] }}</td>
							<td v-on:click="showit(i)" nowrap>{{ d['s'] }}</td>
							<td v-on:click="showit(i)" nowrap>{{ d['m'] }}</td>
							<td v-on:click="showit(i)" nowrap><div class="u" >{{ d['u'] }}</div></td>
							<td v-on:click="showit(i)" nowrap>{{ d['t'] }}</td>
							<td v-on:click="showit(i)" nowrap>{{ d['rs'] }}</td>
						</tr>
					</tbody>
					</table>

			</div>
		</div>
	</div>

	<div class="modal fade" id="log_view" tabindex="-1" >
		<div class="modal-dialog modal-lg modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" v-if="'_id' in view_rec">{{ view_rec['_id'] }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body"  v-if="'_id' in view_rec">
				<!-- <pre class="vdd">{{ view_rec }}</pre> -->
				<div>{{ dateFromObjectId( view_rec['_id'] ) }}</div>
				<table class="table table-bordered table-sm">
					<tbody>
						<template v-for="fh,fd in ff">
							<template v-if="fd in view_rec">
							<tr v-if="view_rec[fd]" >
								<td nowrap>{{ fh }}</td>
								<td>{{ view_rec[ fd ] }}</td>
							</tr>
							</template>
						</template>
						<tr><td colspan="2"><b>Request:</b></td></tr>
						<template v-for="fh,fd in ff_r">
							<template v-if="fd in view_rec">
							<tr v-if="view_rec[fd]" >
								<td nowrap>{{ fh }}</td>
								<td>{{ view_rec[ fd ] }}</td>
							</tr>
							</template>
						</template>
						<tr><td colspan="2"><b>Response:</b></td></tr>
						<template v-for="fh,fd in ff_res">
							<template v-if="fd in view_rec">
							<tr v-if="view_rec[fd]" >
								<td nowrap>{{ fh }}</td>
								<td>{{ view_rec[ fd ] }}</td>
							</tr>
							</template>
						</template>
					</tbody>
				</table>
			</div>
		</div>
		</div>
	</div>

</div>
<script>
function dateFromObjectId(objectId) {
	return new Date(parseInt(objectId.substring(0, 8), 16) * 1000);
};
var app = Vue.createApp({
	data(){
		return {
			path: "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			app_id: "<?=$app['_id'] ?>",
			msg: "", err: "",
			cmsg: "",
			cerr: "",
			token: "",
			log_modal: false,
			records: [],
			view_rec: {},
			ff_r: {
				"m": "Method",
				"h": "Domain",				
				"u": "URL",
				"ct": "ContentType",
				"s": "Scheme",
				"p": "Request Body",
				"ua": "User-Agent",
			},
			ff_res: {
				"rs": "Status",
				"rct": "Content-Type",
				"rh": "Headers",
				"rsz": "Size",
				"rb": "Body",
			},
			ff: {
				"_id": "Id",
				"t": "Type",
				"ip": "IP",
				"sid": "Session ID",
				"api_id": "API Id",
				"app_id": "APP Id",
				"file_id": "File Id",
				"page_id": "Page Id",
			}
		};
	},
	mounted(){
		this.load_records();
	},
	methods: {
		dateFromObjectId: function(objectId) {
			return new Date(parseInt(objectId.substring(0, 8), 16) * 1000).toLocaleString();
		},
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
		load_records: function(){
			this.msg = "Loading...";
			this.err = "";
			var d = {
				"action": "load_records"
			};
			axios.post("?", d).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.records = response.data['data'];
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response Type";
					}
				}else{
					this.err = "Response Error: " . response.status;
				}				
			}).catch(error=>{
				this.err = "Response Error: " . error.message;
			});
		},
		showit: function(vi){
			this.view_rec = this.records[vi];
			this.log_modal = new bootstrap.Modal(document.getElementById('log_view'));
			this.log_modal.show();
		}
	}
}).mount("#app");
</script>
