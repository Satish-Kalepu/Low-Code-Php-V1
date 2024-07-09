<div id="app" >
	<div  class="leftbar"  >
		<a class="left_btn" href="<?=$config_global_apimaker_path ?>">APPs</a>
		<a class="left_btn" href="<?=$config_global_apimaker_path ?>users">Users</a>
		<a class="left_btn" href="<?=$config_global_apimaker_path ?>settings">Settings</a>
	</div>
	<div style="position: fixed;left:165px; top:60px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 20px;" >
			<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
			<div v-if="err" class="alert alert-danger" >{{ err }}</div>
			<div style="float:right;" ><div class="btn btn-outline-dark btn-sm" v-on:click="show_create_app()" >Create App</div></div>
			<div style="float:right;" ><div class="btn btn-outline-dark btn-sm me-2" v-on:click="importnow()" >Import</div></div>
			<div class="h3 mb-3">APPs</div>
			<div style="height: calc( 100% - 100px ); padding-right:20px; overflow: auto;">
				<div v-for="v,vi in apps" style="padding:5px; border-radius:5px; margin-bottom: 10px; border:1px solid #999;" >
					<div style="float:right;">
						<div class="btn btn-outline-dark btn-sm me-2" v-on:click="clone_app__(v['_id'])" >Clone</div>
						<div class="btn btn-outline-danger btn-sm" v-on:click="delete_app__(v['_id'])" >X</div>
					</div>
					<div><a v-bind:href="'<?=$config_global_apimaker_path ?>apps/'+v['_id']" style="cursor:pointer;"><b>{{ v['app'] }}</b></a></div>
					<div>{{ v['des'] }}</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="create_modal__" tabindex="-1" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
	        <div class="modal-title" ><h5 class="d-inline">Create Modal</h5></div>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body"  style="position: relative;">
	      	<div style="margin-bottom:10px;">
	      	<div>Application Name</div>
	      	<div><input type='text' v-bind:class="{'form-control form-control-sm':true, 'border-danger':(new_app_err['app']==1), 'border-success':(new_app_err['app']==2)}" placeholder="App name" v-model="new_app['app']"></div>
	      	<div class="small" >No spaces, no special characters, length minimum 4 max 25</div>
	      	</div>
	      	<div style="margin-bottom:10px;">
	      	<div>Description</div>
	      	<div><textarea  v-bind:class="{'form-control form-control-sm':true, 'border-danger':(new_app_err['des']==1), 'border-success':(new_app_err['des']==2)}" placeholder="Description" v-model="new_app['des']"></textarea></div>
	      	<div class="small" >No special characters except -_,. length minimum 4 max 50</div>
	      	</div>
	      	<div><div class="btn btn-danger btn-sm" v-on:click="do_create">Create</div></div>
	      	<div v-if="cmsg" class="alert alert-primary" >{{ cmsg }}</div>
			<div v-if="cerr" class="alert alert-danger" >{{ cerr }}</div>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="clone_modal__" tabindex="-1" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
	        <div class="modal-title" ><h5 class="d-inline">Clone App</h5></div>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body"  style="position: relative;">
	      	<div style="margin-bottom:10px;">
	      		
	      		<p>Cloning App</p>

	      		<div class="progress">
				  <div class="progress-bar progress-bar-striped" role="progressbar" aria-label="Basic example" v-bind:style="{'width': clone_pr + '%'}" ></div>
				</div>
				<div align="center">{{ clone_pr }}</div>

		      	<div v-if="clmsg" class="alert alert-primary" >{{ clmsg }}</div>
				<div v-if="clerr" class="alert alert-danger" >{{ clerr }}</div>
			</div>
	      </div>
	    </div>
	  </div>
	</div>

</div>
<script>
var app = Vue.createApp({
	data(){
		return {
			msg: "", err: "",
			cmsg: "", cerr: "",
			clmsg: "", clerr: "",
			token: "",
			apps: [],
			create_modal__: false,
			clone_modal__: false,
			delete_app_id: "",
			new_app: {
				'app': '',
				"des": "",
			},
			new_app_err: {
				'app': false,
				"des": false,
			},
			new_name: "",
			clone_pr: 0,
			table_queue: {},
			table_queue_l: 0,
		};
	},
	mounted(){
		this.load_apps();
	},
	methods: {
		load_apps: function(){
			this.err = "";
			axios.post("?",{
				"action": "get_token",
				"event": "load_apps"
			}).then(response=>{
				if( 'token' in response.data ){
					if( response.data['status'] == "success" ){
						axios.post("?",{
							"action":"load_apps",
							"token":response.data['token']
						}).then(response=>{
							console.log("success");
							this.apps = response.data['apps'];
						}).catch(error=>{
							console.log("fail");
							console.log( error.response.status );
							console.log( error.response.data );
							this.err = error.response.status + ": " + error.response.data;
						});
					}else{

					}
				}else{
					this.cerr = "Incorrect response";
				}
			}).catch(error=>{
				this.cerr = error.response.status + ": " + error.response.data;
			});
		},
		show_create_app: function(){
			if( this.create_modal__ == false ){
				this.create_modal__ = new bootstrap.Modal( document.getElementById('create_modal__') );
			}
			this.create_modal__.show();
		},
		show_clone_app: function(){
			if( this.clone_modal__ == false ){
				this.clone_modal__ = new bootstrap.Modal( document.getElementById('clone_modal__') );
			}
			this.clone_modal__.show();
		},
		delete_app__: function( vid ){
			this.delete_app_id = vid;
			if( confirm("Do you want to delete app and its components?\nAn app contains Database tables, APIs, and other components\nDelete action will delete all the information related to the app\n\nAre you really sure to delete?\n\nPlease take backup before proceeding...")){
				this.err = "";
				this.msg = "Deleting app";
				axios.post("?",{
					"action": "get_token",
					"event": "delete_app"
				}).then(response=>{
					this.msg = "";
					if( 'token' in response.data ){
						if( response.data['status'] == "success" ){
							axios.post("?",{
								"action": "delete_app",
								"token": response.data['token'],
								"app_id": this.delete_app_id,
							}).then(response=>{
								if( "status" in response.data ){
									if( response.data['status'] == 'success' ){
										this.load_apps();
									}else{
										this.err = response.data['error'];
									}
								}
							}).catch(error=>{
								this.err = error.response.status + ": " + error.response.data;
							});
						}else{
							this.err = response.data['error'];
						}
					}else{
						this.err = "Incorrect response";
					}
				}).catch(error=>{
					this.err = error.response.status + ": " + error.response.data;
				});
			}
		},
		do_create: function(){
			var f = true;
			if( this.new_app['app'].match(/^[a-z][a-z0-9\-]{3,25}$/) == null ){
				this.new_app_err['app'] = 1;f =false;
			}else{
				this.new_app_err['app'] = 2;
			}
			if( this.new_app['des'].match(/^[A-Za-z0-9\.\,\-\ \_\(\)\[\]\ \@\#\!\&\r\n\t]{4,50}$/) == null ){
				this.new_app_err['des'] = 1;f =false;
			}else{
				this.new_app_err['des'] = 2;
			}
			if( !f ){return}
			if( f ){
				this.cerr = "";
				this.cmsg = "Submitting...";
				axios.post("?",{
					"action": "get_token",
					"event": "create_app"
				}).then(response=>{
					this.cmsg = "";
					if( 'token' in response.data ){
						if( response.data['status'] == "success" ){
							axios.post("?",{
								"action": "create_app",
								"token": response.data['token'],
								"new_app": this.new_app,
							}).then(response=>{
								if( "status" in response.data ){
									if( response.data['status'] == 'success' ){
										this.load_apps();
										this.create_modal__.hide();
									}else{
										this.cerr = response.data['error'];
									}
								}
							}).catch(error=>{
								this.err = error.response.status + ": " + error.response.data;
							});
						}else{
							this.cerr = response.data['error'];
						}
					}else{
						this.cerr = "Incorrect response";
					}
				}).catch(error=>{
					this.cerr = error.response.status + ": " + error.response.data;
				});
			}
		},
		clone_app__: function(vi){
			this.clone_pr = 0;
			this.clerr = "";
			this.clmsg = "Initiating...";
			if( confirm("Are you sure to clone this app?\n\nCloning may consume cpu and disk space" ) ){
				this.new_name = prompt("New app name?");
				if( this.new_name.match(/^[a-z][a-z0-9\-]{3,25}$/) == null ){
					alert("App name incorrect\n [a-z][a-z0-9\-]{3,25}");
					return false;
				}
				this.show_clone_app();
				axios.post("?",{
					"action": "get_token",
					"event": "clone_app" +vi,
					"expire": 5
				}).then(response=>{
					this.clmsg = "";
					if( 'token' in response.data ){
						if( response.data['status'] == "success" ){
							axios.post("?",{
								"action": "apps_clone_app",
								"token": response.data['token'],
								"new_name": this.new_name,
								"app_id": vi
							}).then(response=>{
								if( "status" in response.data ){
									if( response.data['status'] == 'success' ){
										this.clone_pr = 10;
										this.clmsg = "Cloning: " +this.clone_pr + "% done";
										//this.load_apps();
										//this.cre_modal__.hide();
										this.table_queue = response.data['table_queue'];
										this.table_queue_l = Object.keys(response.data['table_queue']).length;
										this.process_queue();
									}else{
										this.clerr = response.data['error'];
									}
								}
							}).catch(error=>{
								this.clerr = error.response.status + ": " + error.response.data;
							});
						}else{
							this.clerr = response.data['error'];
						}
					}else{
						this.clerr = "Incorrect response";
					}
				}).catch(error=>{
					this.clerr = error.response.status + ": " + error.response.data;
				});
			}
		},
		process_queue: function(){
			var old_id = Object.keys(this.table_queue)[0];
			axios.post( "?", {
				"action": "apps_clone_app_step2",
				"old_id": old_id,
				"new_id": this.table_queue[ old_id ]
			}).then(response=>{
				if( "status" in response.data ){
					if( response.data['status'] == 'success' ){
						delete this.table_queue[ old_id ];
						this.clone_pr = ( (1-(Object.keys(this.table_queue).length/this.table_queue_l) )*100 ).toFixed(0);
						this.clmsg = "Cloning: "+ this.clone_pr + "% done";
						if( Object.keys(this.table_queue).length > 0 ){
							this.process_queue();
						}else{
							this.clmsg = "Cloning success";
							this.load_apps();
						}
					}else{
						this.clerr = response.data['error'];
					}
				}else{
					this.clerr = "Incorrect response";
				}
			}).catch(error=>{
				this.clerr = error.response.status + ": " + error.response.data;
			});
		}
	}
}).mount("#app");
</script>