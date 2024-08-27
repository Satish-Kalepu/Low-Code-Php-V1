<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >
			<div style="float:right;" >
				<!-- <button class="btn btn-outline-secondary btn-sm" v-on:click="api_show_create_form()" >Create API</button> -->
			</div>
			<div class="h3 mb-3">Manage APP Data</div>
			<div style="height: calc( 100% - 100px ); overflow: auto; padding-right:10px;" >

				<div style="border: 1px solid #ccc; margin-bottom: 20px; " >
					<div style="background-color:#e8e8e8; padding: 5px 10px;">Export to BackendMaker Hub</div>
					<div style="padding:10px;">

						<div v-if="is_hub_loggedin==false" >
							<p>You can take cloud backup of your app in the Hub, which you can access from anywhere.</p>
							<p><input type="button" class="btn btn-outline-dark btn-sm" value="Login" v-on:click="hub_login()" ></p>

						</div>
						<template v-else >

							<input type="button" class="btn btn-outline-danger btn-sm" style="float:right;" value="UnLink" v-on:click="hub_reset" >
							<p>Backup Hub is linked with user: {{hub_login_email}}  </p>
							<div v-if="'repo' in hub==false" >
								<p><input type="button" class="btn btn-outline-dark btn-sm" value="Link Repository" v-on:click="hut_repo_link()" > <input type="button" class="btn btn-outline-secondary btn-sm" value="Logout" v-on:click="hub_reset" ></p>
							</div>
							<div v-else>

								<input type="button" class="btn btn-outline-dark btn-sm" style="float:right;" value="Change Repository" v-on:click="hut_repo_link()" >
								<p>Application is linked with repository: {{ hub['repo']['name'] }} ({{ hub['repo']['visibility'] }}) </p>

								<p>Recent snapshot {{ hub['repo']['version'] }} <i style="background-color:#eee;">{{ get_age_hub() }}</i></p>
								<p>App last update <i style="background-color:#eee;">{{ get_age_app() }}</i></p>

								<p v-if="hub_backup_busy==false&&hub_restore_task==false">
									<input type="button" class="btn btn-outline-dark btn-sm me-3" value="Export Backup" v-on:click="hub_backupnow" >
									<input type="button" class="btn btn-outline-dark btn-sm me-3" value="Restore" v-on:click="hub_restore_show" >
								</p>

								<div v-if="hub_backup_msg" class="alert alert-primary" >{{ hub_backup_msg }}</div>
								<div v-if="hub_backup_err" class="alert alert-danger" >{{ hub_backup_err }}</div>

								<div v-if="hub_restore_task" >
									<p>Restore is initiated...</p>
									<p><span v-html="hub_restore_msg" ></span></p>
								</div>
								<template v-else >
									<p><span v-html="restore_msg" ></span></p>
								</template>

							</div>

						</template>

					</div>
				</div>

				<div style="border: 1px solid #ccc; margin-bottom: 20px; " >
					<div style="background-color:#e8e8e8; padding: 5px 10px;">Backup</div>
					<div style="padding:10px;">

						<div v-if="is_export_busy" >
							<p>An export is being under process. Please revisit in a moment...</p>
						</div>
						<template v-else >

							<p>You can download app data and all dependent media and databases. which you can use to restore or create new app.</p>
							<p><label><input type="checkbox" v-model="backup_pwd" > Protect backup with a password</label></p>
							<p v-if="backup_pwd"><input type="text" v-model="backup_pass" class="form-control form-select-sm w-auto" placeholder="Password" ></p>
							<p><label><input type="checkbox" v-model="skip_files" > Skip files</label></p>
							<p><label><input type="checkbox" v-model="skip_tables" > Skip internal table records</label></p>
							<p><input type="button" class="btn btn-outline-dark btn-sm" value="Take Backup" v-on:click="backupnow" ></p>

							<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
							<div v-if="err" class="alert alert-danger" >{{ err }}</div>

							<div v-if="snapshot_file"  style="padding:10px; border:1px solid #ccc; " >
								<p>Your export is ready to download</p>
								<p><a v-bind:href="geturl()" target="_blank" >Click here to download the snapshot file.</a></p>
								<p>Size {{ snapshot_size }} MB</p>
							</div>

							<div v-else>
								<div v-if="last_export_fn" style="padding:10px; border:1px solid #ccc; " >
									<p>An export is ready to download which was taken on {{ last_export_dt }}</p>
									<p><a v-bind:href="geturl3()" target="_blank" >Click here to download the snapshot file.</a></p>
									<p>Size {{ last_export_sz }} MB</p>
								</div>
							</div>

						</template>
					</div>
				</div>


				<div style="border: 1px solid #ccc; margin-bottom: 20px;" >
					<div style="background-color:#e8e8e8; padding: 5px 10px;">Restore App</div>
					<div style="padding:10px;">


						<div v-if="restore_status==5||restore_task" >
							<p>Import is initiated...</p>
							<p><span v-html="restore_msg" ></span></p>
							<!-- <p><button class="btn btn-outline-dark btn-sm py-0" >Check Status</button></p> -->
							<div v-if="restore_app_id" ><a class="btn btn-link" v-bind:href="root_path+'apps/'+restore_app_id+'/'" >{{ restore_app_name }}</a></div>
						</div>
						<template v-else >

								<p>You can restore any app which will delete all the settings of the current app. Or you can create a new app.</p>
								<!-- <p><input type="button" class="btn btn-outline-dark btn-sm" value="Restore" v-on:click="restorenow" ></p> -->
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
												<p>
													<select class="form-select form-select-sm w-auto" v-model="restore_step2_option" >
														<option value="replace" >Replace Current APP</option>
														<option value="create" >Create as New APP</option>
													</select>
												</p>
												<p><input type="button" class="btn btn-outline-dark btn-sm" value="Proceed" v-on:click="restore_step2now" ></p>
											</div>
											<div v-if="restore_status==3" >
												<p>Uploaded</p>
												<ul>
													<li v-for="v,i in restore_summary" >{{ i }}: {{ v }}</li>
												</ul>
												<p><span v-html="restore_msg" ></span></p>
												<p><input type="button" class="btn btn-outline-dark btn-sm" value="Proceed" v-on:click="restore_step2now" ></p>
											</div>
											<div v-if="restore_status==5" >
												<p>Imported</p>
												<p><span v-html="restore_msg" ></span></p>
											</div>
											<p v-if="restore_status==9" style="color:red;" >Error: {{ restore_error }}</p>
										</div>
									</div>
								</template>
						</template>
					</div>
				</div>

				<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>

			</div>
		</div>
	</div>

	<div class="modal fade" id="hub_login_modal" tabindex="-1" >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Backup Hub Login</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div>Email</div>
              <input type="text" spellcheck="false" autocomplete="false" class="form-control form-control-sm" v-model="hub_login_d['email']" placeholder="Email">
              <div>Password</div>
              <input type="password" spellcheck="false" autocomplete="false" class="form-control form-control-sm" v-model="hub_login_d['password']" >
              <div>&nbsp;</div>
              <div><input type="button" class="btn btn-outline-dark btn-sm" value="Login" v-on:click="hub_login_now()" ></div>
              <div v-if="hub_login_msg" class="alert alert-success" >{{ hub_login_msg }}</div>
              <div v-if="hub_login_err" class="alert alert-success" >{{ hub_login_err }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="hub_link_modal" tabindex="-1" >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Backup Hub Repo Linking</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
	          	<div style="border:1px solid #bbb;">
	              <div style="padding:5px; background-color:#f8f8f8; border-bottom:1px solid #ccc;">Repos available</div>
	              <div style="padding:5px;" >
									<div v-if="hub_repo_list.length==0" >No Repos available</div>
									<table class="table table-bordered table-sm w-auto" >
										<tbody>
											<tr>
												<td>ID</td>
												<td>Name</td>
												<td>Visibility</td>
												<td>Updated On</td>
												<td>-</td>
											</tr>
											<tr v-for="hv,hi in hub_repo_list">
												<td>{{ hv['id'] }}</td>
												<td>{{ hv['repo_name'] }}</td>
												<td>{{ hv['visibility'] }}</td>
												<td>{{ get_age(hv['uploaded_on']) }}</td>
												<td><button type="button" class="btn btn-outline-dark btn-sm" v-on:click="hub_link_repo(hi)" >Select</button></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
	          	<div style="border:1px solid #bbb;margin-top:20px;">
	              <div style="padding:5px; background-color:#f8f8f8; border-bottom:1px solid #ccc;">Create new Repository:</div>
	              <div style="padding:5px;" >
		              <div>Repo Name</div>
		              <div><input type="text" spellcheck="false" autocomplete="false" class="form-control form-control-sm" placeholder="Repository Name" v-model="hub_create['name']" ></div>
		              <div>Description</div>
		              <div><textarea spellcheck="false" autocomplete="false" class="form-control form-control-sm" placeholder="Repository Description" v-model="hub_create['des']" ></textarea></div>
		              <div><label style="cursor: pointer;"><input type="radio" v-model="hub_create['visibility']" value="public" > Public</label></div>
		              <div><label style="cursor: pointer;"><input type="radio" v-model="hub_create['visibility']" value="private" > Private</label></div>
		              <div><input type="button" class="btn btn-outline-dark btn-sm" value="Create Repo" v-on:click="hub_create_repo()" ></div>
		              <div v-if="hub_create_msg" class="alert alert-success" >{{ hub_create_msg }}</div>
		              <div v-if="hub_create_err" class="alert alert-success" >{{ hub_create_err }}</div>
		            </div>
		          </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="hub_restore_modal" tabindex="-1" >
      <div class="modal-dialog modal-lg modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Hub Backup Versions</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
          	<div v-if="hub_versions.length==0" >No backups available</div>
          	<table class="table table-bordered table-sm w-auto" >
          		<tbody>
								<tr>
									<td>Version</td>
									<td>Date</td>
									<td>APP Details</td>
									<td>Source Version</td>
									<td>-</td>
								</tr>
          			<tr v-for="hv in hub_versions">
									<td nowrap>
										<div v-bind:class="{'text-success':is_same_version(hv['version'])}">{{ hv['version'] }}</div>
										<div v-if="is_same_version(hv['version'])">Current Version</div>
									</td>
									<td nowrap>{{ hv['date'].substr(0,16) }}</td>
									<td nowrap>
										<div v-if="'app' in hv" >
											<div>{{ hv['app']['app'] }}</div>
											<div v-bind:class="{'text-danger':hv['app']['_id']!=app_id}">{{ hv['app']['_id'] }}</div>
										</div>
									</td>
									<td nowrap>{{ hv['source'] }}</td>
									<td><button type="button" class="btn btn-outline-dark btn-sm" v-on:click="hub_restore_version(hv['version'])" >Restore</button></td>
								</tr>
          		</tbody>
          	</table>
          </div>
        </div>
      </div>
    </div>

</div>
<script>
var app = Vue.createApp({
	data(){
		return {
			root_path: "<?=$config_global_apimaker_path ?>",
			path: "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			app_id: "<?=$app['_id'] ?>",
			app_name: "<?=$app['app'] ?>",
			app_des: "<?=$app['des'] ?>",
			last_updated: "<?=$app['last_updated'] ?>",
			msg: "", err: "", msg2: "", err2: "", hub_backup_msg: "", hub_backup_err: "",hub_create_msg: "", hub_create_err: "",
			cmsg: "",
			cerr: "",
			apis: [],
			show_create_api: false,
			new_api: {
				"name": "",
				"des": "",
			},
			is_export_busy: <?=$is_export_busy?"true":"false" ?>,
			last_export_fn: "<?=$last_export_fn ?>",
			last_export_sz: <?=$last_export_sz ?>,
			last_export_dt: "<?=$last_export_dt ?>",
			create_app_modal: false,
			token: "",
			backup_pwd: false,
			backup_pass: "",
			skip_files: false,skip_tables: false,
			snapshot_file: "",snapshot_size: 0,

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

			hub_restore_task: <?=$hub_restore_task?"true":"false" ?>,
			hub_restore_status: 0,
			hub_restore_msg: "",
			hub_restore_error: "",

			hub_login_msg: "", hub_login_err: "",
			hub_backup_msg: "", hub_backup_err: "",
			hub_create_msg: "", hub_create_err: "",
			hub_versions_msg: "", hub_versions_err: "",
			hub_login_form: false,
			is_hub_loggedin: <?=$is_hub_login?"true":"false" ?>,
			hub_login_email: "<?=$hub_login_email ?>",
			hub_login_d: {"email": "", "password": ""},
			hub_create: {"name": "", "des": "", "visibility": "public"},
			hub_link_modal: false,
			hub: <?=isset($app['hub'])?json_encode($app['hub']):"{}" ?>,
			hub_backup_busy: false,
			hub_restore_modal: false,
			hub_versions: [],
			hub_repo_list: [],
		};
	},
	mounted(){
		if( this.restore_task ){
			this.check_import_status();
		}
	},
	methods: {
		is_same_version: function(v){
			if( 'repo' in this.hub  == false ){
				return false;
			}
			if( v == this.hub['repo']['version'] ){
				return true;
			}else{
				return false;
			}
		},
		get_age_hub: function(){
			if( 'repo' in this.hub ){
				if( 'date' in this.hub['repo'] ){
					var dt = new Date(this.hub['repo']['date']);
					var s = dt.getTime()/1000;
					var dt = new Date();
					var n = dt.getTime()/1000;
					var d = n-s;
					if( d < 60 ){
						return parseInt(d) + " Seconds Ago";
					}else{
						d = d/60;
						if( d<60 ){
							return parseInt(d) + " Minutes Ago";
						}else{
							d = d/60;
							if( d<48 ){
								return parseInt(d) + " Hours Ago";
							}else{
								d = d/24;
								return parseInt(d) + " Days Ago";
							}
						}
					}
				}else{
					return "Not Available";
				}
			}else{
				return "Not Available";
			}
		},
		get_age_app: function(){
				var dt = new Date(this.last_updated);
				var s = dt.getTime()/1000;
				var dt = new Date();
				var n = dt.getTime()/1000;
				var d = n-s;
				if( d < 60 ){
					return parseInt(d) + " Seconds Ago";
				}else{
					d = d/60;
					if( d<60 ){
						return parseInt(d) + " Minutes Ago";
					}else{
						d = d/60;
						if( d<48 ){
							return parseInt(d) + " Hours Ago";
						}else{
							d = d/24;
							return parseInt(d) + " Days Ago";
						}
					}
				}
		},
		get_age: function(vd){
			if( vd.match(/^000/) ){
				return "Never";
			}
				var dt = new Date(vd);
				var s = dt.getTime()/1000;
				var dt = new Date();
				var n = dt.getTime()/1000;
				var d = n-s;
				if( d < 60 ){
					return parseInt(d) + " Seconds Ago";
				}else{
					d = d/60;
					if( d<60 ){
						return parseInt(d) + " Minutes Ago";
					}else{
						d = d/60;
						if( d<48 ){
							return parseInt(d) + " Hours Ago";
						}else{
							d = d/24;
							return parseInt(d) + " Days Ago";
						}
					}
				}
		},
		restore_step2now: function(){
			if( confirm("Are you sure?") ){
				axios.post("?", {
					"action": "exports_restore_upload_confirm",
					"rand":    this.restore_rand,
					"option":  this.restore_step2_option
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof( response.data ) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.restore_status = 5;
									this.restore_msg = "Background Task Initiated.";
									setTimeout(this.check_import_status, 3000);
								}else{
									this.restore_status = 9;
									this.restore_error = "Restore Failed: " + response.data['error'];
								}
							}else{
								this.restore_status = 9;
								this.restore_error = "Something wrong";
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
			}
		},
		restore_uploadnow: function(){
			var vs = new FormData();
			vs.append("action", "exports_restore_upload");
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
							if( response.data['status'] == "success2" ){
								this.restore_summary = response.data['summary'];
								this.restore_status = 2;
								this.restore_step2_option = "replace";
								this.restore_msg = "You are going to restore <b>same app</b> with a <b>older snapshot</b> which was taken on <BR>" + response.data['date'] + "<BR><BR>You may lose latest changes<BR>Please confirm to proceed";
								this.restore_rand = response.data['rand'];
							}else if( response.data['status'] == "success3" ){
								this.restore_summary = response.data['summary'];
								this.restore_status = 3;
								this.restore_msg = "You are going to restore this app with a snapshot of a <b>different app</b> which was taken on <BR>" + response.data['date'] + "<BR><BR>You will lose all the updates and settings on current app<BR>Please confirm to proceed";
								this.restore_step2_option = "replace_with_other";
								this.restore_rand = response.data['rand'];
							}else{
								this.restore_status = 9;
								this.restore_error = "Error: " + response.data['error'];
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
		check_import_status: function(){
			axios.post("?", {
				"action": "exports_check_import_status"
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
		geturl: function(){
			return this.path+'export/?action=download_snapshot&snapshot_file='+encodeURIComponent(this.snapshot_file);
		},
		geturl2: function(v){
			return this.path+'export/?action=download_snapshot&snapshot_file='+encodeURIComponent(v);
		},
		geturl3: function(){
			return this.path+'export/?action=download_snapshot&snapshot_file='+encodeURIComponent(this.last_export_fn);
		},
		backupnow(){
			this.snapshot_file = "";
			this.msg = "Loading...";
			this.err = "";
			if( this.backup_pwd ){
				if( this.backup_pass.match( /^[A-Za-z0-9\!\@\#\$\%\^\&\*\(\)\_\+\-\=\{\}\[\]\:\;\,\.\/\<\>\?]{8,64}$/ ) ){
					alert("please enter password. \nMin 8 chars, max 64 chars. no spaces or special chars ");
					return;
				}
			}
			axios.post("?", {
				"action":"get_token",
				"event":"backupnow."+this.app_id,
				"expire":2
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.backupnow2();
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
					this.err = "Response Error: " + response.status;
				}
			});
		},
		backupnow2(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?",{
				"action":"app_backup",
				"app_id":this.app_id,
				"token":this.token,
				"backup_pwd":this.backup_pwd,
				"backup_pass":this.backup_pass,
				"skip_files": this.skip_files,
				"skip_tables": this.skip_tables,
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.snapshot_file = response.data['temp_fn'];
								this.snapshot_size = (Number(response.data['sz'])/1024/1024).toFixed(2);
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
					this.err = "Response Error: " + response.status;
				}
			});
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
		api_show_create_form(){
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
		hub_backupnow(){
			this.hub_backup_busy = true;
			this.hub_backup_msg = "Taking backup...";
			this.hub_backup_err = "";
			axios.post("?", {
				"action":"export_hub_backup",
			}).then(response=>{

				this.hub_backup_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.hub_backup_msg = "Backup is successful. Version ID: " + response.data['version_id'];
							}else{
								this.hub_backup_err = response.data['error'];
							}
						}else{
							this.hub_backup_err = "Incorrect response";
						}
					}else{
						this.hub_backup_err = "Incorrect response Type";
					}
				}else{
					this.hub_backup_err = "Response Error: " + response.status;
				}
			}).catch(error=>{
				this.hub_backup_err = "Response Error: " + error.message;
			});
		},
		hub_login: function(){
			this.hub_login_modal = new bootstrap.Modal(document.getElementById('hub_login_modal'));
			this.hub_login_modal.show();
			this.hub_login_msg = ""; this.hub_login_err = "";
		},
		hub_login_now: function(){
			this.hub_login_msg = "Checking...";
			this.hub_login_err = "";
			axios.post("?",{
				"action":"exports_hub_login",
				"login":{
					"email": btoa(this.hub_login_d['email']),
					"password": btoa(this.hub_login_d['password']),
				}
			}).then(response=>{
				this.hub_login_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.is_hub_loggedin = true;
								this.hub_login_email = this.hub_login_d['email']+'';
								this.hub_login_modal.hide();
							}else{
								alert("Token error: " + response.data['error']);
								this.hub_login_err = "Token Error: " + response.data['error'];
							}
						}else{
							this.hub_login_err = "Incorrect response";
						}
					}else{
						this.hub_login_err = "Incorrect response Type";
					}
				}else{
					this.hub_login_err = "Response Error: " + response.status;
				}
			}).catch(error=>{
				this.hub_login_err = "Response Error: " + error.message;
			});
		},
		hub_reset: function(){
			if( confirm("Are you sure to Logout from Backup Hub?") ){
				axios.post("?",{
					"action":"exports_hub_logout",
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.is_hub_loggedin = false;
									this.hub_login_email = '';
								}else{
									alert(response.data['error']);
								}
							}else{
								alert("Incorrect response");
							}
						}else{
							alert("Incorrect response Type");
						}
					}else{
						alert("Response Error: " + response.status);
					}
				}).catch(error=>{
					alert("Response Error: " + error.message);
				});
			}
		},
		hut_repo_link: function(){
			this.hub_link_modal = new bootstrap.Modal(document.getElementById('hub_link_modal'));
			this.hub_link_modal.show();
			this.hub_create_msg = ""; this.hub_create_err = "";
			this.hub_load_repo_list();
			this.hub_create['name'] = this.app_name + '';
			this.hub_create['des'] = this.app_des + '';
		},
		hub_create_repo: function(){
			this.hub_create_msg = "";
			this.hub_create_err = "";
			if( this.hub_create['name'].match(/^[a-z0-9\-\_\.]{3,50}$/i) == null ){
				this.hub_create_err = "Repo name should be simple without spaces";return;
			}
			if( this.hub_create['des'].match(/^[a-z0-9\-\_\.\ \!\@\#\&\(\)\\,\.\r\n]{3,200}$/i) == null ){
				this.hub_create_err = "Repo description is required. max length 200";return;
			}
			if( this.hub_create['visibility'].match(/^(public|private)$/) == null ){
				this.hub_create_err = "Repo visibility is required.";return;
			}
			this.hub_create_msg = "Checking...";
			this.hub_create_err = "";
			axios.post("?",{
				"action":"exports_hub_create_repo",
				"repo": this.hub_create
			}).then(response=>{
				this.hub_create_err = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.hub['repo'] = response.data['repo'];
								this.hub_link_modal.hide();
							}else{
								this.hub_create_err = "Token Error: " + response.data['error'];
							}
						}else{
							this.hub_create_err = "Incorrect response";
						}
					}else{
						this.hub_create_err = "Incorrect response Type";
					}
				}else{
					this.hub_create_err = "Response Error: " + response.status;
				}
			}).catch(error=>{
				this.hub_create_err = "Response Error: " + error.message;
			});
		},
		hub_restore_show: function(){
			this.hub_restore_modal = new bootstrap.Modal(document.getElementById('hub_restore_modal'));
			this.hub_restore_modal.show();
			this.hub_create_msg = ""; this.hub_create_err = "";
			this.hub_load_backups();
		},
		hub_load_backups: function(){
			this.hub_versions_msg = ""; this.hub_versions_err = "";
			axios.post("?",{
				"action":"exports_hub_repo_versions",
			}).then(response=>{
				this.hub_versions_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.hub_versions = response.data['data'];
							}else{
								this.hub_versions_err = "Token Error: " + response.data['error'];
							}
						}else{
							this.hub_versions_err = "Incorrect response";
						}
					}else{
						this.hub_versions_err = "Incorrect response Type";
					}
				}else{
					this.hub_versions_err = "Response Error: " + response.status;
				}
			}).catch(error=>{
				this.hub_versions_err = "Response Error: " + error.message;
			});
		},
		hub_load_repo_list: function(){
			this.hub_versions_msg = ""; this.hub_versions_err = "";
			axios.post("?",{
				"action":"exports_hub_repo_list",
			}).then(response=>{
				this.hub_versions_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.hub_repo_list = response.data['data'];
							}else{
								this.hub_versions_err = "Token Error: " + response.data['error'];
							}
						}else{
							this.hub_versions_err = "Incorrect response";
						}
					}else{
						this.hub_versions_err = "Incorrect response Type";
					}
				}else{
					this.hub_versions_err = "Response Error: " + response.status;
				}
			}).catch(error=>{
				this.hub_versions_err = "Response Error: " + error.message;
			});
		},
		hub_restore_version: function(vd){
			if( confirm("Are you sure to replace current version with a older version?\n\nAny unsaved changes in current version will lost permanently") ){
				this.hub_versions_msg = ""; this.hub_versions_err = "";
				axios.post("?",{
					"action":"exports_hub_restore_version",
					"version_id": vd
				}).then(response=>{
					this.hub_versions_msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.hub_restore_task = true;	
									this.hub_restore_modal.hide();
									setTimeout(this.check_hub_import_status, 3000);
									this.hub_versions_msg = "Background Task Initiated.";
								}else{
									alert(response.data['error']);
								}
							}else{
								alert("Incorrect response");
							}
						}else{
							alert("Incorrect response Type");
						}
					}else{
						alert("Response Error: " + response.status);
					}
				}).catch(error=>{
					alert("Response Error: " + error.message);
				});
			}
		},
		check_hub_import_status: function(){
			axios.post("?", {
				"action": "exports_check_hub_import_status"
			}).then(response=>{
				console.log( response );
				if( response.status == 200 ){
					if( typeof( response.data ) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.hub_restore_msg = response.data['data']['status'];
								setTimeout(this.check_hub_import_status,3000);
							}else{
								alert("Incorrect response");
							}
						}else{
							this.hub_restore_status = 9;
							this.hub_restore_error = "Something wrong";
						}
					}
				}
			}).catch(error=>{
				this.hub_restore_status = 9;
				this.hub_restore_error = error.msg;
				cosole.log( error );
			});
		},
		hub_link_repo: function(vi){
			axios.post("?", {
				"action": "exports_hub_link_repo",
				"repo": {
					"id": this.hub_repo_list[vi]['id'],
					"name": this.hub_repo_list[vi]['repo_name'],
					"des": this.hub_repo_list[vi]['des'],
					"visibility": this.hub_repo_list[vi]['visibility'],
				}
			}).then(response=>{
				if( response.status == 200 ){
					if( typeof( response.data ) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.hub['repo'] = {
									"id": this.hub_repo_list[vi]['id']+'',
									"name": this.hub_repo_list[vi]['repo_name']+'',
									"des": this.hub_repo_list[vi]['des']+'',
									"visibility": this.hub_repo_list[vi]['visibility']+'',
								};
								this.hub_link_modal.hide();
								alert("Success");
							}else{
								alert("Error: " + response.data['error']);
							}
						}else{
							alert("Invalid response");
						}
					}else{
						alert("Incorrect response");
					}
				}else{
					alert("Error: " + response.status);
				}
			}).catch(error=>{
				alert( error.message );
			});
		}
	},
}).mount("#app");
</script>