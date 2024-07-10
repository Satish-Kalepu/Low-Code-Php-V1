<style>
	div.vid{ padding:0px 2px; cursor:pointer; }
	div.vid pre.vid{display: none; position: absolute; background-color: white; padding: 3px; border: 1px solid #aaa;}
	div.vid:hover pre.vid{display: block;}
</style>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >
			<div style="float:right;" >
				<button class="btn btn-outline-dark btn-sm me-1" v-on:click="function_show_import_form()" >Import</button>
				<button class="btn btn-outline-dark btn-sm" v-on:click="function_show_create_form()" >Create Function</button>
			</div>
			<div style="float:right; margin-right:20px;" >
				<div v-if="msg" class="alert alert-primary py-0" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger py-0" >{{ err }}</div>
			</div>
			<div style="clear: both;"></div>
			<div class="h3 mb-3">Functions</div>
			<div style="height: calc( 100% - 100px ); overflow: auto;" >
			<table class="table table-striped table-bordered table-sm" >
				<tr>
					<td>ID</td>
					<td>Name</td>
					<td></td>
				</tr>
				<tr v-for="v,i in functions">
					<td><div class="vid">#<pre class="vid">{{v['_id']}}</pre></div></td>
					<td width="90%">
						<div><a v-bind:href="path+'functions/'+v['_id']+'/'+v['version_id']" >{{ v['name'] }}</a></div>
						<div class="text-secondary">{{ v['des'] }}</div>
					</td>
					<td><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_function(i)" ></td>
				</tr>
			</table>
			</div>
		</div>
	</div>
		<div class="modal fade" id="create_app_modal" tabindex="-1" >
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Create Function</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">
		        	<div>Name</div>
		        	<input type="text" class="form-control" v-model="new_function['name']" placeholder="Name" v-on:change="nchange" >
		        	<div class="text-secondary small">no spaces. no special chars. except dash(-). lowercase recommended</div>
		        	<div>&nbsp;</div>
		        	<div>Description</div>
		        	<textarea class="form-control" v-model="new_function['des']" ></textarea>
		        	<div>&nbsp;</div>
		        	<div v-if="cmsg" class="alert alert-success" >{{ cmsg }}</div>
		        	<div v-if="cerr" class="alert alert-success" >{{ cerr }}</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
		        <button type="button" class="btn btn-primary btn-sm"  v-on:click="createnow">Create</button>
		      </div>
		    </div>
		  </div>
		</div>


		<div class="modal fade" id="import_modal" tabindex="-1" >
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Import Function</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">
		        	<div>Password</div>
		        	<input spellcheck="false" type="text" class="form-control form-control-sm" v-model="import_password__" placeholder="Password" >
		        	<div>File</div>
		        	<input type="file" spellcheck="false" class="form-control form-control-sm" id="import_file__" v-on:change="import_selected__" >
		        	<div>&nbsp;---&nbsp;</div>
		        	<template v-if="import_check__" >
			        	<div>Name</div>
			        	<input type="text" spellcheck="false" class="form-control form-control-sm" v-model="import_name__" placeholder="Default from import" >
			        	<div>Description</div>
			        	<input type="text" spellcheck="false" class="form-control form-control-sm" v-model="import_des__" placeholder="Default from import" >
			        	<div>&nbsp;</div>
			        </template>

		        	<input type="button" spellcheck="false" class="btn btn-outline-dark btn-sm" v-on:click="import_function__" value="Import" >
		        	<div v-if="imsg__" class="alert alert-success" >{{ imsg__ }}</div>
		        	<div v-if="ierr__" class="alert alert-danger"  >{{ ierr__ }}</div>
		      </div>
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
			msg: "",err: "",cmsg: "",cerr: "",imsg__: "",ierr__: "",
			import_modal__: false, "import_password__": "", "import_file__": "", "import_version__": "create", "import_name__": "", "import_des__": "", "import_check__": false,
			functions: [],
			show_create_function: false,
			new_function: {
				"name": "",
				"des": "",
			},
			create_app_modal: false,
			token: "",
		};
	},
	mounted(){
		this.load_functions();
	},
	methods: {
		nchange: function(){
			if( this.new_function['des']=="" ){
				this.new_function['des'] = this.new_function['name']+'';
			}
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
		load_functions(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?", {
				"action":"get_token",
				"event":"getfunctions."+this.app_id,
				"expire":2
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.load_functions2();
								}
							}else{
								alert("Token error: " + response.dat['data']);
								this.err = "Token Error: " + response.data['data'];
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
			});
		},
		load_functions2(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?",{"action":"get_functions","app_id":this.app_id,"token":this.token}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.functions = response.data['data'];
							}else{
								alert("Token error: " + response.data['error']);
								this.err = "Token Error: " + response.data['error'];
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
			});
		},
		function_show_import_form(){
			this.import_check__ = false;
			this.import_name__ = "";this.import_des__ = "";
			this.import_modal__ = new bootstrap.Modal(document.getElementById('import_modal'));
			this.import_modal__.show();
			this.imsg = ""; this.ierr = "";
		},
		function_show_create_form(){
			this.create_app_modal = new bootstrap.Modal(document.getElementById('create_app_modal'));
			this.create_app_modal.show();
			this.cmsg = ""; this.cerr = "";
		},
		cleanit(v){
			v = v.replace( /\-/g, "DASH" );
			v = v.replace( /\_/g, "UDASH" );
			v = v.replace( /\W/g, "-" );
			v = v.replace( /DASH/g, "-" );v = v.replace( /UDASH/g, "_" );
			v = v.replace( /[\-]{2,5}/g, "-" );
			v = v.replace( /[\_]{2,5}/g, "_" );
			return v;
		},
		import_selected__: function(){
			this.import_file__ = "";
			this.ierr__ = "";
			var vf = document.getElementById("import_file__").files[0];
			if( vf.name.match(/\.[a-f0-9]{24}\.api$/) == null ){
				this.ierr__ = "Please select a proper file";
				document.getElementById("import_file__").value = "";
			}
			this.import_file__ = vf.name+'';
		},
		import_function__: function(){
			this.ierr__ = "";
			this.imsg__ = "";
			if( this.import_password__.trim()=="" ){
				this.ierr__ = "password is must";return;
			}
			if( document.getElementById("import_file__").value == "" ){
				this.ierr__ = "select file";return;
			}

			if( this.import_name__.trim() != "" ){
				this.import_name__ = this.cleanit(this.import_name__);
				if( this.import_name__.match(/^[a-z0-9\.\-\_\ ]{3,100}$/i) == null ){
					this.ierr = "Name incorrect. Special chars not allowed. Length minimum 3 max 100";
					return false;
				}
			}
			if( this.import_des__.trim() != "" ){
				if( this.import_des__.match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\ \r\n]{5,200}$/i) == null ){
					this.ierr = "Description incorrect. Special chars not allowed. Length minimum 5 max 200";
					return false;
				}
			}

			var vpost = new FormData();
			var vf = document.getElementById("import_file__").files[0];
			vpost.append( "action", "app_function_import_create" );
			vpost.append( "file", vf);
			vpost.append( "password", this.import_password__ );
			vpost.append( "app_id", this.app_id );
			vpost.append( "name", this.import_name__ );
			vpost.append( "des", this.import_des__ );
			this.imsg__ = "Importing...";
			axios.post("?", vpost).then(response=>{
				this.imsg__ = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.imsg__ = "Imported successfully. Redirecting ...";
								setTimeout(function(){document.location = "<?=$config_global_apimaker_path ?>apps/<?=$config_param1 ?>/functions/"+response.data['function_id']+"/"+response.data['version_id']; },1000);
							}else{
								this.ierr__ = ( "Export Error: " + response.data['error'] );
								if( 'name' in response.data ){
									this.import_check__ = true;
								}
								if( 'name' in response.data && this.import_name__ == "" ){
									this.import_name__ = response.data['name'];
								}
								if( 'des' in response.data && this.import_des__ == "" ){
									this.import_des__ = response.data['des'];
								}
							}
						}else{
							this.ierr__ = ("Incorrect response");
						}
					}else{
						this.ierr__ = ("Incorrect response Type");
					}
				}else{
					this.ierr__ = ("Response Error: " + response.status );
				}
			}).catch(error=>{
				console.log( error );
				this.ierr__ = ( "Error Exporting" );
			});
		},
		createnow(){
			this.cerr = "";
			this.new_function['name'] = this.cleanit(this.new_function['name']);
			if( this.new_function['name'].match(/^[a-z0-9\.\-\_\ ]{3,100}$/i) == null ){
				this.cerr = "Name incorrect. Special chars not allowed. Length minimum 3 max 100";
				return false;
			}
			if( this.new_function['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\ \r\n]{5,200}$/i) == null ){
				this.cerr = "Description incorrect. Special chars not allowed. Length minimum 5 max 200";
				return false;
			}
			this.cmsg = "Creating...";
			axios.post("?", {
				"action": "create_function", 
				"new_function": this.new_function
			}).then(response=>{
				this.cmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.cmsg = "Created";
								this.create_app_modal.hide();
								this.load_functions();
							}else{
								this.cerr = response.data['error'];
							}
						}else{
							this.cerr = "Incorrect response";
						}
					}else{
						this.cerr = "Incorrect response Type";
					}
				}else{
					this.cerr = "Response Error: " . response.status;
				}
			});
		},
		delete_function( vi ){
			if( confirm("Are you sure?") ){
				this.msg = "Deleting...";
				this.err = "";
				axios.post("?", {"action":"get_token","event":"deletefunction"+this.app_id+this.functions[vi]['_id'],"expire":1}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.token = response.data['token'];
									if( this.is_token_ok(this.token) ){
										axios.post("?", {
											"action":"delete_function",
											"token":this.token,
											"function_id": this.functions[ vi ]['_id']
										}).then(response=>{
											this.msg = "";
											if( response.status == 200 ){
												if( typeof(response.data) == "object" ){
													if( 'status' in response.data ){
														if( response.data['status'] == "success" ){
															this.load_functions();
														}else{
															alert("Token error: " + response.data['data']);
															this.err = "Token Error: " + response.data['data'];
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
										});
									}
								}else{
									alert("Token error: " + response.dat['data']);
									this.err = "Token Error: " + response.data['data'];
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
				});
			}
		}
	}
}).mount("#app");
</script>