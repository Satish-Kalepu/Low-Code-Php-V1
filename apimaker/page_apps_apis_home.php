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
				<button class="btn btn-outline-dark btn-sm ms-1" id="import_btn" v-on:click="api_show_import_form()" >Import</button>
				<button class="btn btn-outline-dark btn-sm ms-1" id="create_folder_btn" v-on:click="api_show_folder_form()" >Create Folder</button>
				<button class="btn btn-outline-dark btn-sm ms-1" id="create_api_btn" v-on:click="api_show_create_form()" >Create API</button>
			</div>
			<div class="h3 mb-3">APIs</div>
			<div style="clear:both;"></div>

			<div style="display: flex; height: 25px;">
				<div style="min-width:5px; cursor: pointer; padding:0px 5px; border:1px solid #ccc;" v-on:click="change_path('/')" >/</div>
				<div v-for="vv in paths" style="min-width:20px; cursor: pointer; padding:0px 5px; border:1px solid #ccc;" v-on:click="change_path(vv['tp'])" >{{ vv['p'] }}/</div>
			</div>

			<div style="height: calc( 100% - 130px ); padding-right:20px; overflow: auto;" >

			<div v-if="msg" class="alert alert-primary py-0" >{{ msg }}</div>
			<div v-if="err" class="alert alert-danger py-0" >{{ err }}</div>

			<table class="table table-striped table-bordered table-sm" >
				<tr>
					<td>ID</td>
					<td>Name/Path</td>
					<td></td>
					<td></td>
				</tr>
				<template v-for="v,i in apis">
				<tr  class="api_tr" v-if="v['vt']=='folder'">
					<td><div class="vid">#<pre class="vid">{{v['_id']}}</pre></div></td>
					<td width="90%">
						<div style="display: inline-block; padding: 0px 5px; border: 1px solid #ccc; width:55px; margin-right:0px;">Folder</div>&nbsp;
						<a v-bind:href="path+'apis/?path='+v['name']" v-on:click.stop.prevent="enter_path(v['name'])" >{{ this.current_path + v['name'] }}/</a>
						<div style="float:right;" ><span  class="badge bg-secondary" >{{ v['count'] }}</span></div>
					</td>
					<td><div  v-bind:id="'folder_edit_btn_'" class="btn btn-outline-dark btn-sm" v-on:click="folder_settings(i)" ><i class="fa-regular fa-pen-to-square"></i></div></td>
					<td><input type="button"  v-bind:id="'folder_delete_btn_'" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="delete_api(i)" ></td>
				</tr>
				</template>
				<template v-for="v,i in apis">
				<tr class="api_tr" v-if="v['vt']!='folder'">
					<td><div class="vid">#<pre class="vid">{{v['_id']}}</pre></div></td>
					<td width="90%">
						<div style="display: inline-block; padding: 0px 5px; border: 1px solid #ccc; width:55px; margin-right:0px;">{{ v['input-method'] }}</div>&nbsp;
						<div style="display:inline-block;"><a v-bind:href="path+'apis/'+v['_id']+'/'+v['version_id']" >{{ current_path + v['name'] }}</a></div>
						<div style="float:right;" >{{ getc(v) }}</div>
						<div class="text-secondary">{{ v['des'] }}</div>
					</td>
					<td><div class="btn btn-outline-dark btn-sm" v-bind:id="'api_edit_btn_'" v-on:click="api_settings(i)" ><i class="fa-regular fa-pen-to-square"></i></div></td>
					<td><input type="button" v-bind:id="'api_delete_btn_'" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="delete_api(i)" ></td>
				</tr>
				</template>
			</table>
			</div>
		</div>
	</div>
		<div class="modal fade" id="create_app_modal" tabindex="-1" >
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Create API</h5>
		        <button type="button"  id="create_app_modal_close_btn" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">
		        	<div>Name/URL Slug</div>
		        	<input type="text" class="form-control" v-model="new_api['name']" placeholder="Name" v-on:change="nchange" >
		        	<div class="text-secondary small">no spaces. no special chars. except dash(-). lowercase recommended</div>
		        	<div>&nbsp;</div>
		        	<div>Description</div>
		        	<textarea class="form-control" v-model="new_api['des']" ></textarea>
		        	<div>&nbsp;</div>
		        	<div>Use Template</div>
		        	<select class="form-select form-select-sm" >
		        		<option>Sample</option>
		        		<option>HTTP API</option>
		        		<option>AWS S3 Upload</option>
		        		<option>Image Resizer</option>
		        		<option>MySql CRUD Functions</option>
		        		<option>MongoDB CRUD Functions</option>
		        		<option>PDF Generator</option>
		        		<option>Encryption</option>
		        	</select>
		        	<div>&nbsp;</div>
		        	<div v-if="cmsg" class="alert alert-success" >{{ cmsg }}</div>
		        	<div v-if="cerr" class="alert alert-success" >{{ cerr }}</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button"  id="create_app_cancel_btn" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
		        <button type="button"  id="create_app_save_btn" class="btn btn-primary btn-sm"  v-on:click="createnow">Create</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="import_modal" tabindex="-1" >
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Import API</h5>
		        <button type="button" id="import_modal_btn_close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

		        	<input type="button" id="import_modal_btn_import" spellcheck="false" class="btn btn-outline-dark btn-sm" v-on:click="import_api__" value="Import" >
		        	<div v-if="imsg" class="alert alert-success" >{{ imsg }}</div>
		        	<div v-if="ierr" class="alert alert-danger"  >{{ ierr }}</div>
		      </div>
		    </div>
		  </div>
		</div>


		<div class="modal fade" id="create_folder_modal" tabindex="-1" >
		  <div class="modal-dialog model-sm">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Create Folder</h5>
		        <button type="button"  id="create_folder_modal_btn_close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

		      	<div>New folder name</div>
		      	<div><span>{{ current_path }}</span><input type="text" class="form-control form-control-sm w-auto d-inline" v-model="new_folder_name" ></div>
		      	<div><input type="button"  id="create_folder_modal_btn" class="btn btn-outline-dark btn-sm" value="Create" v-on:click="do_create_folder" ></div>

				<div v-if="cfmsg" class="alert alert-primary" >{{ cfmsg }}</div>
				<div v-if="cferr" class="alert alert-danger" >{{ cferr }}</div>


		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="api_settings_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg modal-xl">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">API Settings</h5>
		        <button type="button"  id="api_settings_modal_btn_close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

		      	<p><b><span v-if="editing_api_index>-1" >{{ apis[ editing_api_index ]['name'] }}</span></b></p>

				<div style="border:1px solid #ccc; margin-bottom: 20px;">
					<div style="padding:10px; background-color: #f8f8f8;">Rename</div>
					<div style="padding:10px;">
						<div><input type="text" class="form-control form-control-sm" v-model="new_api_name" >
						<input type="button"  id="api_settings_btn" class="btn btn-outline-dark btn-sm" value="Update" v-on:click="change_api_name" ></div>
					</div>
				</div>

				<div v-if="amsg" class="alert alert-primary" >{{ amsg }}</div>
				<div v-if="aerr" class="alert alert-danger"  >{{ aerr }}</div>

				<div style="border:1px solid #ccc;">
					<div style="padding:10px; background-color: #f8f8f8;">Move to Folder</div>
					<div style="padding:10px;">
						<p>
							<select v-model="move_to_folder_name" >
								<option v-for="v in get_available_paths()" v-bind:value="v" >{{v}}</option>
							</select>
						</p>
						<p><input type="button" id="move_api_btn" class="btn btn-outline-dark btn-sm" value="Update" v-on:click="move_api" ></p>
						<!-- <div><label style="cursor: pointer;"><input type="checkbox" v-model="move_to_new_folder" > Create new folder</label></div> -->
						<p>&nbsp;</p>
					</div>
				</div>

				<p>&nbsp;</p>

		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="folder_settings_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg modal-xl">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Folder Settings</h5>
		        <button type="button"  id="folder_settings_btn_close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

		      	<p><b><span v-if="editing_folder_index>-1" >{{ apis[ editing_folder_index ]['name'] }}</span></b></p>

				<div style="border:1px solid #ccc;">
					<div style="padding:10px; background-color: #f8f8f8;">Rename Folder</div>
					<div style="padding:10px;">
						<div><input type="text" class="form-control form-control-sm w-auto d-inline" v-model="rename_to_folder_name" >
						<input type="button"  id="folder_settings_btn" class="btn btn-outline-dark btn-sm" value="Update" v-on:click="change_folder_name" ></div>
					</div>
				</div>

				<div v-if="fmsg" class="alert alert-primary" >{{ fmsg }}</div>
				<div v-if="ferr" class="alert alert-danger" >{{ ferr }}</div>


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
			msg: "",err: "",cfmsg: "",cferr: "",cmsg: "",cerr: "",imsg: "",ierr: "",fmsg: "",ferr: "",amsg: "",aerr: "",
			new_folder_name: "",
			current_path: "<?=$_GET['path']?$_GET['path']:'/' ?>",
			paths: [],
			import_modal__: false, "import_password__": "", "import_file__": "", "import_version__": "create", "import_name__": "", "import_des__": "", "import_check__": false,
			apis: [],
			show_create_api: false,
			new_api: {
				"name": "",
				"des": "",
			},
			create_app_modal: false, editing_api_index: -1, editing_folder_index: -1,
			api_settings_modal: false,folder_settings_modal: false,
			token: "",
			new_api_name: "", move_to_folder_name: "",move_to_new_folder: false, rename_to_folder_name:"",
		};
	},
	mounted(){
		this.update_paths();
		this.load_apis();
	},
	methods: {
		get_available_paths: function(){
			var v = [];
			if( this.current_path!='/' ){
				v.push( "/" );
			}
			var x = this.current_path.split(/\//g);
			var vf = "/";
			for(var i=0;i<x.length;i++){if( x[i]!="" ){
				vf = vf + x[i] + "/";
				if( this.current_path!=vf ){
					v.push( vf );
				}
			}}
			for(var i=0;i<this.apis.length;i++){if( this.apis[i]['vt']=="folder"){
				v.push( this.current_path+this.apis[i]['name']+'/' );
			}}
			return v;
		},
		folder_settings: function(vi){
			this.editing_folder_index = vi;
			this.rename_to_folder_name = this.apis[ vi ]['name']+'';
			this.folder_settings_modal = new bootstrap.Modal(document.getElementById('folder_settings_modal'));
			this.folder_settings_modal.show();
			this.fmsg = ""; this.ferr = "";
		},
		api_settings: function(vi){
			this.move_to_folder_name = ""; 
			this.editing_api_index = vi;
			this.new_api_name = this.apis[ this.editing_api_index ]['name']+'';
			this.api_settings_modal = new bootstrap.Modal(document.getElementById('api_settings_modal'));
			this.api_settings_modal.show();
			this.amsg = ""; this.aerr = "";
		},
		change_api_name: function(vi){
			this.aerr = ""; this.amsg = "";
			if( this.new_api_name.match(/^[a-z][a-z0-9\-\_\.]{2,50}$/) == null ){
				this.aerr = "Api name should not contain special chars. spaces";return;
			}
			if( this.new_api_name == this.apis[ this.editing_api_index ]['name'] ){
				this.aerr = "No change";return ;
			}
			axios.post("?", {
				"action":"apis_rename",
				"api_id":this.apis[ this.editing_api_index ]['_id'],
				"new_name": this.new_api_name,
				"current_path": this.current_path,
			}).then(response=>{
				this.amsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.amsg = "Successfully updated";
								setTimeout( function(v){ v.api_settings_modal.hide(); v.editing_api_index=-1; v.load_apis(); }, 2000, this );
							}else{
								this.aerr = "Error: " + response.data['error'];
							}
						}else{
							this.aerr = "Incorrect response";
						}
					}else{
						this.aerr = "Incorrect response Type";
					}
				}else{
					this.aerr = "Response Error: " . response.status;
				}
			});
		},
		move_api: function(vi){
			this.aerr = ""; this.amsg = "";
			this.amsg = "Moving...";
			axios.post("?", {
				"action":"apis_move",
				"api_id":this.apis[ this.editing_api_index ]['_id'],
				"new_path": this.move_to_folder_name,
				"current_path": this.current_path,
			}).then(response=>{
				this.amsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.amsg = "Successfully updated";
								setTimeout( function(v){ v.api_settings_modal.hide();v.editing_api_index=-1; v.load_apis(); }, 2000, this );
							}else{
								this.aerr = "Error: " + response.data['error'];
							}
						}else{
							this.aerr = "Incorrect response";
						}
					}else{
						this.aerr = "Incorrect response Type";
					}
				}else{
					this.aerr = "Response Error: " . response.status;
				}
			});
		},
		change_folder_name: function(vi){
			this.ferr = ""; this.fmsg = "";
			if( this.rename_to_folder_name.match(/^[a-z][a-z0-9\-\_\.]{2,50}$/) == null ){
				this.ferr = "Folder name should not contain special chars. spaces";return;
			}
			if( this.rename_to_folder_name == this.apis[ this.editing_folder_index ]['name'] ){
				this.ferr = "No change";return ;
			}
			axios.post("?", {
				"action":"apis_folder_rename",
				"folder_id":this.apis[ this.editing_folder_index ]['_id'],
				"new_name": this.rename_to_folder_name,
				"current_path": this.current_path,
			}).then(response=>{
				this.fmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.fmsg = "Successfully updated";
								setTimeout( function(v){ v.folder_settings_modal.hide(); v.editing_folder_index=-1;  v.load_apis(); }, 2000, this );
							}else{
								this.ferr = "Error: " + response.data['error'];
							}
						}else{
							this.ferr = "Incorrect response";
						}
					}else{
						this.ferr = "Incorrect response Type";
					}
				}else{
					this.ferr = "Response Error: " . response.status;
				}
			});
		},
		getc: function(v){
			if( 'updated' in v ){
				return v['updated'].substr(0,10);
			}return "";
		},
		change_path: function(tp){
			console.log("change path: "+ tp );
			this.current_path = tp+'';
			this.update_paths();
			this.load_apis();
		},
		enter_path: function(v){
			this.current_path = this.current_path + v + "/";
			console.log( this.current_path );
			this.update_paths();
			this.load_apis();
		},
		update_paths(){
			console.log( this.current_path );
			var paths = this.current_path.split(/\//g);
			paths.splice(0,1);
			paths.pop();
			var p = [];
			var tp = "/";
			console.log( JSON.stringify(paths,null,4) );
			for(var i=0;i<paths.length;i++){
				tp = tp + paths[i] + "/";
				p.push({
					"p":paths[i],
					"tp": tp+'',
				});
			}
			this.apis = [];
			this.paths = p;
			console.log( JSON.stringify(p,null,4) );
		},
		nchange: function(){
			if( this.new_api['des']=="" ){
				this.new_api['des'] = this.new_api['name']+'';
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
		load_apis(){
			this.editing_api_index= -1; this.editing_folder_index= -1;
			this.msg = "Loading...";
			this.err = "";
			axios.post("?", {
				"action":"get_token",
				"event":"getapis."+this.app_id,
				"expire":2
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.load_apis2();
								}
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
		load_apis2(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?",{
				"action":"get_apis",
				"app_id":this.app_id,
				"token":this.token,
				"current_path": this.current_path,
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.apis = response.data['data'];
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
		api_show_create_form(){
			this.create_app_modal = new bootstrap.Modal(document.getElementById('create_app_modal'));
			this.create_app_modal.show();
			this.cmsg = ""; this.cerr = "";
		},
		api_show_import_form(){
			this.import_check__ = false;
			this.import_name__ = "";this.import_des__ = "";
			this.import_modal__ = new bootstrap.Modal(document.getElementById('import_modal'));
			this.import_modal__.show();
			this.imsg = ""; this.ierr = "";
		},
		cleanit(v){
			v = v.replace( /\-/g, "DASH" );
			v = v.replace( /\_/g, "UDASH" );
			v = v.replace( /\W/g, "-" );
			v = v.replace( /UDASH/g, "_" );v = v.replace( /DASH/g, "-" );
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
		import_api__: function(){
			this.ierr = "";
			this.imsg = "";
			if( this.import_password__.trim()=="" ){
				this.ierr = "password is must";return;
			}
			if( document.getElementById("import_file__").value == "" ){
				this.ierr = "select file";return;
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
			vpost.append( "action", "app_api_import_create" );
			vpost.append( "file", vf);
			vpost.append( "password", this.import_password__ );
			vpost.append( "app_id", this.app_id );
			vpost.append( "name", this.import_name__ );
			vpost.append( "des", this.import_des__ );
			vpost.append( "current_path", this.current_path );
			this.imsg = "Importing...";
			axios.post("?", vpost).then(response=>{
				this.imsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.imsg = "Imported successfully. Redirecting ...";
								setTimeout(function(){document.location = "<?=$config_global_apimaker_path ?>apps/<?=$config_param1 ?>/apis/"+response.data['api_id']+"/"+response.data['version_id']; },1000);
							}else{
								this.ierr = ( "Export Error: " + response.data['error'] );
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
							this.ierr = ("Incorrect response");
						}
					}else{
						this.ierr = ("Incorrect response Type");
					}
				}else{
					this.ierr = ("Response Error: " + response.status );
				}
			}).catch(error=>{
				console.log( error );
				this.ierr = ( "Error Exporting" );
			});
		},
		createnow(){
			this.cerr = "";
			this.new_api['name'] = this.cleanit(this.new_api['name']);
			if( this.new_api['name'].match(/^[a-z0-9\.\-\_\ ]{3,100}$/i) == null ){
				this.cerr = "Name incorrect. Special chars not allowed. Length minimum 3 max 100";
				return false;
			}
			if( this.new_api['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\ \r\n]{5,200}$/i) == null ){
				this.cerr = "Description incorrect. Special chars not allowed. Length minimum 5 max 200";
				return false;
			}
			this.cmsg = "Creating...";
			axios.post("?", {
				"action": "create_api", 
				"new_api": this.new_api,
				"current_path": this.current_path,
			}).then(response=>{
				this.cmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.cmsg = "Created";
								setTimeout("document.location='" + this.path + 'apis/'+ response.data['api_id'] + '/'+response.data['version_id'] + "'", 1000 );
								//this.create_app_modal.hide();
								//this.load_apis();
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
		delete_api( vi ){
			if( confirm("Are you sure?") ){
				this.msg = "Deleting...";
				this.err = "";
				axios.post("?", {
					"action":"get_token",
					"event":"deleteapi"+this.app_id+this.apis[vi]['_id'],
					"expire":1
				}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.token = response.data['token'];
									if( this.is_token_ok(this.token) ){
										axios.post("?", {
											"action":"delete_api",
											"token":this.token,
											"api_id": this.apis[ vi ]['_id']
										}).then(response=>{
											this.msg = "";
											if( response.status == 200 ){
												if( typeof(response.data) == "object" ){
													if( 'status' in response.data ){
														if( response.data['status'] == "success" ){
															this.load_apis();
														}else{
															alert("Error: " + response.data['error']);
															this.err = "Error: " + response.data['error'];
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
			}
		},
		api_show_folder_form(){
			this.create_folder_modal = new bootstrap.Modal(document.getElementById('create_folder_modal'));
			this.create_folder_modal.show();
			this.cmsg = ""; this.cerr = "";
		},
		do_create_folder: function(){
			this.cferr = "";
			this.cfmsg = "";
			this.new_folder_name = this.cleanit(this.new_folder_name);
			if( this.new_folder_name.match(/^[a-z0-9\.\-\_\/]{2,100}$/) == null){
				this.cferr = "Folder name should be [a-z0-9\.\-\_\/]{2,100}";return false;
			}

			this.cfmsg = "Creating Folder";
			axios.post( "?", {
				"action":"get_token",
				"event":"create.folder."+this.app_id,
				"expire":1
			}).then( response=>{
				this.cfmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								var token = response.data['token'];
								if( this.is_token_ok( token) ){
									axios.post("?", {
										"action":"apis_create_folder",
										"token":token,
										"current_path": this.current_path,
										"new_folder": this.cleanit(this.new_folder_name),
									}).then(response=>{
										this.cfmsg = "";
										if( response.status == 200 ){
											if( typeof(response.data) == "object" ){
												if( 'status' in response.data ){
													if( response.data['status'] == "success" ){
														//this.current_path = this.current_path + this.new_folder_name + '/';
														//this.enter_path( this.new_folder_name+'' );
														this.new_folder_name = '';
														this.create_folder_modal.hide();
														this.load_apis();
													}else{
														console.log("xxxxx");
														this.cferr = response.data['error'];
													}
												}else{
													this.cferr = "Incorrect response";
												}
											}else{
												this.cferr = "Incorrect response Type";
											}
										}else{
											this.cferr = "Response Error: " . response.status;
										}
									});
								}
							}else{
								alert("Token error: " + response.data['error']);
								this.cferr = "Token Error: " + response.data['error'];
							}
						}else{
							this.cferr = "Incorrect response";
						}
					}else{
						this.cferr = "Incorrect response Type";
					}
				}else{
					this.cferr = "Response Error: " . response.status;
				}
			});
		},
	}
}).mount("#app");
</script>