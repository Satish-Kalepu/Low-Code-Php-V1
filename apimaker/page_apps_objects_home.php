<style>
	.mongoid{ display:block; cursor:pointer; width:30px; }
	.mongoid:hover{ background-color:#eee; }
	.mongoid div{ display:none; }
	.mongoid:hover div{ display: block; position:absolute; background-color:white; box-shadow:2px 2px 5px #666; border:1px solid #999; padding:0px 10px; }
</style>

<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; width:calc( 100% - 150px ); height: calc( 100% - 50px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div class="btn btn-sm btn-outline-dark float-end" v-on:click="show_configure()" >Configure</div>

			<div style="display:flex; column-gap: 50px;">
				<div class="h3 mb-3"><span class="text-secondary" >Object Store</span></div>
			</div>

			<div style="position:relative;overflow: auto; height: calc( 100% - 60px );">
				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

				<div style="float:right;">
					<div class="btn btn-sm btn-outline-dark" v-on:click="show_create_internal()" >Create Internal Graph Database</div>
				</div>
				<p>Internal Databases:</p>
				<table class="table table-bordered table-striped w-auto" >
					<tbody>
						<tr>
							<td>#</td>
							<td>Database</td>
							<td></td>
						</tr>
						<tr v-for="d,i in dbs['internal']">
							<td><div class="mongoid"><div>{{ d['_id'] }}</div><span>#</span></div></td>
							<td><div style="min-width:300px;"><a v-bind:href="objectpath+d['_id']" >{{ d['name'] }}</a></div></td>
							<td><input type="button" value="X" class="btn btn-outline-danger btn-sm" v-on:click="delete_database_internal(i)" ></td>
						</tr>
					</tbody>
				</table>
				<p>&nbsp;</p>

				<div style="float:right;">
					<div class="btn btn-sm btn-outline-dark" v-on:click="show_create_internal()" >Create External Graph Database</div>
				</div>
				<p>External Databases:</p>
				<table class="table table-bordered table-striped table-sm w-auto" >
					<tbody>
						<tr>
							<td>#</td>
							<td>Database</td>
							<td></td>
						</tr>
					</tbody>
				</table>
				
			</div>

		</div>
	</div>
	<div class="modal fade" id="create_internal_modal" tabindex="-1" >
		<div class="modal-dialog modal-lg modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Create Internal Graph Database</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" id="create_popup_body">
					<p>Database name</p>
					<div><input type="text" class="form-control form-control-sm" v-model="new_internal_dbname" value="" placeholder="Database name" ></div>

					<div><input type="button" class="btn btn-outline-dark btn-sm" value="Create" v-on:click="create_internal_database()"></div>
					<div v-if="cmsg" class="alert alert-primary" >{{ cmsg }}</div>
					<div v-if="cerr" class="alert alert-danger" >{{ cerr }}</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
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
			"objectpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/",
			"app_id" : "<?=$app['_id'] ?>",
			msg: "", err: "",cmsg: "", cerr: "",
			enabled: false,
			dbs: {
				"internal":[],
				"external":[],
			},
			new_internal_dbname: "",
		};
	},
	mounted:function(){
		this.load_dbs();
	},
	watch: {

	},
	methods: {
		delete_database_internal: function( vi ){
			if( confirm("Are you sure?") ){
				axios.post("?", {
					"action": "objects_delete_database",
					"graph_id": this.dbs['internal'][ vi ]['_id']
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.load_dbs();
								}else{
									alert(response.data['error'] );
								}
							}else{
								alert("Incorrect response" );
							}
						}else{
							alert("Incorrect response" );
						}
					}else{
						alert("http error: " . response.status );
					}
				}).catch(error=>{
					alert( this.get_http_error__(error) );
				});
			}
		},
		enable_objects: function(){
			this.confmsg = "Enabling...";
			this.conferr = "";
			axios.post("?",{
				"action": "objects_enable"
			}).then(response=>{
				this.confmsg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								document.location.reload();
							}else{
								this.conferr = response.data['error'];
							}
						}else{
							this.conferr = "Incorrect response";
						}
					}else{
						this.conferr = "Incorrect response";
					}
				}else{
					this.conferr = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.conferr = this.get_http_error__(error);
			});
		},
		disable_objects: function(){
			this.confmsg = "Disabling...";
			this.conferr = "";
			axios.post("?",{
				"action": "objects_disable"
			}).then(response=>{
				this.confmsg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								document.location.reload();
							}else{
								this.conferr = response.data['error'];
							}
						}else{
							this.conferr = "Incorrect response";
						}
					}else{
						this.conferr = "Incorrect response";
					}
				}else{
					this.conferr = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.conferr = this.get_http_error__(error);
			});
		},
		load_dbs: function(){
			this.msg = "Loading...";
			axios.post("?", {
				"action": "graph_load_dbs"
			}).then(response=>{
				this.msg = "";
				this.dbs = response.data['data'];
			});
		},
		show_create_internal: function(){
			this.create_internal_popup = new bootstrap.Modal(document.getElementById('create_internal_modal'));
			this.create_internal_popup.show();
		},
		create_internal_database: function(){
			this.cmsg = "";
			this.cerr = "";
			if( this.new_internal_dbname.match(/^[a-z0-9\-\_\.\ ]{3,50}$/i) == null ){
				this.cerr = "Please enter proper name: [a-z0-9\-\_\.\ ]{3,50}";return;
			}
			this.cmsg = "Creating...";
			axios.post("?",{
				"action": "objects_create_database",
				"dbname": this.new_internal_dbname
			}).then(response=>{
				this.cmsg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.create_internal_popup.hide();
								this.load_dbs();
							}else{
								this.cerr = response.data['error'];
							}
						}else{
							this.cerr = "Incorrect response";
						}
					}else{
						this.cerr = "Incorrect response";
					}
				}else{
					this.cerr = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.cerr = error.message
			});
		},
		get_http_error__: function(e){
			if( typeof(e) == "object" ){
				if( 'status' in e ){
					if( 'error' in e ){
						return e['error'];
					}else{
						return "There was no error";
					}
				}else if( 'response' in e ){
					var s = e.response.status;
					if( typeof( e['response']['data'] ) == "object" ){
						if( 'error' in e['response']['data'] ){
							return s + ": " + e['response']['data']['error'];
						}else{
							return s + ": " + JSON.stringify(e['response']['data']).substr(0,100);
						}
					}else{
						return s + ": " + e['response']['data'].substr(0,100);
					}
				}else if( 'message' in e ){
					return e['message'];
				}else{
					return "Incorrect response";
				}
			}else{
				return "Invalid response"
			}
		},
	}
});

var app1 = app.mount("#app");

</script>
