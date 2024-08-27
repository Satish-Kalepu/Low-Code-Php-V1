<div id="app" >
	<div  class="leftbar"  >
		<a class="left_btn" href="<?=$config_global_apimaker_path ?>">APPs</a>
		<a class="left_btn" href="<?=$config_global_apimaker_path ?>users">Users</a>
		<a class="left_btn" href="<?=$config_global_apimaker_path ?>settings">Settings</a>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 20px;" >
			<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
			<div v-if="err" class="alert alert-danger" >{{ err }}</div>
			<div style="float:right;" ><div class="btn btn-outline-dark btn-sm" v-on:click="show_create_app()" >Create App</div></div>
			<div style="float:right;" ><div class="btn btn-outline-dark btn-sm me-2" v-on:click="show_import_app()" >Import</div></div>
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

	<div class="modal fade" id="import_modal__" tabindex="-1" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
	        <div class="modal-title" ><h5 class="d-inline">Import App</h5></div>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body"  style="position: relative;">
	      	<div style="margin-bottom:10px;">

	      		<p>Import App</p>

	      		<div v-if="restore_status==5||restore_task" >
					<p>Import is initiated...</p>
					<p><span v-html="restore_msg" ></span></p>
					<!-- <p><button class="btn btn-outline-dark btn-sm py-0" >Check Status</button></p> -->
					<p>Status: Running</p>
					<div v-if="restore_app_id" ><a class="btn btn-link" v-bind:href="path+'apps/'+restore_app_id" >{{ restore_app_name }}</a></div>
				</div>
				<template v-else >
					<p>You can restore any app which will delete all the settings of the current app. Or you can create a new app.</p>
					<p><input type="file" class="form-control form-control-sm" id="restore_file" style="display:;" v-on:change="restore_fileselect"></p>
					<template v-if="restore_file" >
						<p><label><input type="checkbox" v-model="restore_pwd" > Archive is password protected?</label></p>
						<p v-if="restore_pwd">Archive Password: <input type="text" v-model="restore_pass" class="form-control form-control-sm w-auto" placeholder="Password" ></p>
						<div style="display:flex; gap:20px;" >
							<div v-if="restore_status!=5">
								<p><input type="button" class="btn btn-outline-dark btn-sm" v-on:click="restore_uploadnow" value="Upload" ></p>
							</div>
							<div>
								<p v-if="restore_status==1" >Uploading {{ restore_pg }}%</p>
								<div v-if="restore_status==2" >
									<p>Uploaded</p>
									<p>App Name: {{ restore_app_name }}</p>
									<ul>
										<li v-for="v,i in restore_summary" >{{ i }}: {{ v }}</li>
									</ul>
									<p><span v-html="restore_msg" ></span></p>
									<p><input type="button" class="btn btn-outline-dark btn-sm" value="Proceed" v-on:click="restore_step2now" ></p>
								</div>
								<p v-if="restore_status==9" style="color:red;" >Error: {{ restore_error }}</p>
							</div>
						</div>
					</template>
				</template>

		      	<div v-if="imsg" class="alert alert-primary" >{{ imsg }}</div>
				<div v-if="ierr" class="alert alert-danger" >{{ ierr }}</div>
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
			path: "<?=$config_global_apimaker_path ?>",
			msg: "", err: "",
			cmsg: "", cerr: "",
			clmsg: "", clerr: "",
			token: "",
			apps: [],
			create_modal__: false,
			clone_modal__: false,
			import_modal__: false,
			delete_app_id: "",
			new_app: {'app': '', "des": "" },
			new_app_err: {'app': false, "des": false },
			new_name: "",
			clone_pr: 0,
			table_queue: {},
			table_queue_l: 0,
			restore_f: false,
			restore_file: false,
			restore_pwd: false,
			restore_pass: "",
			restore_pg: 0,
			restore_status: 0,
			restore_error: "",
			restore_msg: "",
			restore_rand: "",
			restore_task: <?=$restore_task?"true":"false" ?>,
			restore_app_name: "",
			restore_app_id: "",
			restore_summary: {},
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
		show_import_app: function(){
			if( this.import_modal__ == false ){
				this.import_modal__ = new bootstrap.Modal( document.getElementById('import_modal__') );
			}
			this.import_modal__.show();
			if( this.restore_task ){
				this.check_import_status();
			}
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
		},
		restore_step2now: function(){
			axios.post("?", {
				"action": "home_restore_upload_confirm",
				"rand":    this.restore_rand,
			}).then(response=>{
				if( response.status == 200 ){
					if( typeof( response.data ) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.restore_status = 5;
								this.restore_msg = "Background Task Initiated.";
								setTimeout(this.check_import_status,3000);
							}else{
								this.restore_status = 9;
								this.restore_error = "Restore Failed: " + response.data['error'];
							}
						}else{
							this.restore_status = 9;
							this.restore_error = "Incorrect Response";
						}
					}else{
						this.restore_status = 9;
						this.restore_error = "Invalid Response";
					}
				}
			}).catch(error=>{
				this.restore_status = 9;
				this.restore_error = error.message;
				cosole.log( error );
			});
		},
		restore_uploadnow: function(){
			var vs = new FormData();
			vs.append("action", "home_restore_upload");
			vs.append("file", this.restore_f );
			vs.append("pwd", this.restore_pwd );
			vs.append("pass", this.restore_pass );
			this.restore_status = 1;
			this.restore_error = "";
			this.restore_msg = "";
			axios.post("?", vs, {
				onUploadProgress: function (e){
					var l = (e.loaded/e.total*100).toFixed(0);
					console.log( (e.loaded/e.total*100).toFixed(0) );
					app.restore_pg = l;
				}
			}).then(response=>{
				console.log( response );
				if( response.status == 200 ){
					if( typeof( response.data ) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.restore_status = 2;
								this.restore_msg = "You are going to restore an app snapshot which was taken on <BR>" + response.data['date'] + "<BR><BR>Please confirm to proceed";
								this.restore_summary = response.data['summary'];
								this.restore_app_name = response.data['app'];
								this.restore_rand = response.data['rand'];
							}else{
								alert("Incorrect response");
							}
						}else{
							this.restore_status = 9;
							this.restore_error = "Something wrong";
						}
					}
				}
			}).catch(error=>{
				this.restore_status = 9;
				this.restore_error = error.msg;
				cosole.log( error );
			});
		},
		check_import_status: function(){
			axios.post("?", {
				"action": "home_check_import_status"
			}).then(response=>{
				console.log( response );
				if( response.status == 200 ){
					if( typeof( response.data ) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.restore_msg = response.data['data']['status'];
								if( 'new_app_id' in response.data['data'] ){
									this.restore_app_id = response.data['data']['new_app_id'];
									this.restore_app_name = response.data['data']['new_app'];
								}else{
									setTimeout(this.check_import_status,3000);
								}
							}else{
								alert("Incorrect response");
							}
						}else{
							this.restore_status = 9;
							this.restore_error = "Something wrong";
						}
					}
				}
			}).catch(error=>{
				this.restore_status = 9;
				this.restore_error = error.msg;
				cosole.log( error );
			});
		},
		restorenow: function(){
			document.getElementById("restore_file").click();
		},
		restore_fileselect: function(){
			var vf = document.getElementById("restore_file").files[0];
			this.restore_f = vf;
			//document.getElementById("restore_file").value = "";
		//	console.log( vf );
			console.log( vf.name );
			if( vf.name.match(/^[A-Za-z0-9]+\_[a-f0-9]{24}\_[0-9]{8}\_[0-9]{6}\.gz$/) == null ){
				alert("Selected filename format incorrect.\n\nExpected:\napp_id yymmdd hhiiss");
				return false;
			}
			this.restore_file = vf.name+'';
			// var objectURL = window.URL.createObjectURL(vf); // console.log( objectURL );
		},
	},

}).mount("#app");
</script>