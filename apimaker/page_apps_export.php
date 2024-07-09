<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:165px; top:60px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >
			<div style="float:right;" >
				<!-- <button class="btn btn-outline-secondary btn-sm" v-on:click="api_show_create_form()" >Create API</button> -->
			</div>
			<div class="h3 mb-3">Manage APP Data</div>
			<div style="height: calc( 100% - 100px ); overflow: auto; padding-right:10px;" >

				<div style="border: 1px solid #ccc; margin-bottom: 20px; " >
					<div style="background-color:#e8e8e8; padding: 5px 10px;">Export to Hub</div>
					<div style="padding:10px;">
							<p>Still cooking. Please check later</p>
					</div>
				</div>

				<div style="border: 1px solid #ccc; margin-bottom: 20px; " >
					<div style="background-color:#e8e8e8; padding: 5px 10px;">Backup</div>
					<div style="padding:10px;">
						<p>You can download app data and all dependent media and databases. which you can use to restore or create new app.</p>
						<p><label><input type="checkbox" v-model="backup_pwd" > Protect backup with a password</label></p>
						<p v-if="backup_pwd"><input type="text" v-model="backup_pass" class="form-control form-select-sm w-auto" placeholder="Password" ></p>
						<p><label><input type="checkbox" v-model="skip_files" > Skip files</label></p>
						<p><label><input type="checkbox" v-model="skip_tables" > Skip internal table records</label></p>
						<p><input type="button" class="btn btn-outline-dark btn-sm" value="Take Backup" v-on:click="backupnow" ></p>

						<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
						<div v-if="err" class="alert alert-danger" >{{ err }}</div>

						<div v-if="snapshot_file" >
							<p><a v-bind:href="geturl()" target="_blank" >Click here to download the snapshot file.</a></p>
							<p>Size {{ snapshot_size }}</p>
						</div>
					</div>
				</div>

				<div v-if="snapshots.length>0" style="border: 1px solid #ccc; margin-bottom: 20px; " >
					<div style="background-color:#e8e8e8; padding: 5px 10px;">Backup Snapshots Available</div>
					<div style="padding:10px;">
						<p>History of snapshots taken and available in this instance.</p>
						<div v-for="v in snapshots" style="display: flex; gap:20px; margin-bottom: 5px; border-bottom:1px solid #ccc;" >
							<div>{{ v.substr(25,15) }}</div>
							<div><a v-bind:href="geturl2(v)" class="btn btn-outline-dark btn-sm" >Download</a></div>
							<div><input type="button" value="Restore" class="btn btn-outline-dark btn-sm" v-on:click="restore_available" ></div>
						</div>
					</div>
				</div>

				<div style="border: 1px solid #ccc; margin-bottom: 20px;" >
					<div style="background-color:#e8e8e8; padding: 5px 10px;">Restore</div>
					<div style="padding:10px;">
						<p>You can restore any app which will delete all the settings of the current app. Or you can create a new app.</p>
						<!-- <p><input type="button" class="btn btn-outline-dark btn-sm" value="Restore" v-on:click="restorenow" ></p> -->
						<p><input type="file" class="form-control form-control-sm" id="restore_file" style="display:;" v-on:change="restore_fileselect"></p>
						<template v-if="restore_file" >
							<p><label><input type="checkbox" v-model="restore_pwd" > Archive is password protected?</label></p>
							<p v-if="restore_pwd">Archive Password: <input type="text" v-model="restore_pass" class="form-control form-control-sm w-auto" placeholder="Password" ></p>
							<div style="display:flex; gap:20px;">
								<div>
									<p><input type="button" class="btn btn-outline-dark btn-sm" v-on:click="restore_uploadnow" value="Upload" ></p>
								</div>
								<div>
									<p v-if="restore_status==1" >Uploading {{ restore_pg }}%</p>
									<div v-if="restore_status==2" >
										<p>Uploaded</p>
										<p><span v-html="restore_msg" ></span></p>
										<p><select class="form-select form-select-sm w-auto" v-model="restore_step2_option" >
											<option value="replace" >Replace Current APP</option>
											<option value="create" >Create as New APP</option>
										</select></p>
										<p><input type="button" class="btn btn-outline-dark btn-sm" value="Proceed" v-on:click="restore_step2now" ></p>
									</div>
									<p v-if="restore_status==3" style="color:red;" >Error: {{ restore_error }}</p>
								</div>
						</template>
					</div>
				</div>

				<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>

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
			msg: "", err: "", msg2: "", err2: "",
			cmsg: "",
			cerr: "",
			apis: [],
			show_create_api: false,
			new_api: {
				"name": "",
				"des": "",
			},
			create_app_modal: false,
			token: "",
			backup_pwd: false,
			backup_pass: "",
			skip_files: false,skip_tables: false,
			snapshot_file: "",snapshot_size: 0,
			snapshots:[],
			restore_f: false,
			restore_file: false,
			restore_pwd: false,
			restore_pass: "",
			restore_pg: 0,
			restore_status: 0,
			restore_error: "",
			restore_msg: "",
			restore_step2_option: "replace", // replace/new
			restore_rand: "",
		};
	},
	mounted(){
		this.load_snapshots();
	},
	methods: {
		restore_step2now: function(){
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
								this.response_msg = "Restoration Successfull";
							}else{
								this.restore_error2 = "Restore Failed: " + response.data['error'];
							}
						}else{
							this.restore_status = 3;
							this.restore_error2 = "Something wrong";
						}
					}
				}
			}).catch(error=>{
				this.restore_status = 3;
				this.restore_error = error.msg;
				cosole.log( error );
			});
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
								this.restore_status = 2;
								this.restore_msg = "You are going to restore this app to a older snapshot which was taken on <BR>" + response.data['date'] + "<BR><BR>You may lose latest changes<BR>Please confirm to proceed";
								this.restore_rand = response.data['rand'];
							}else if( response.data['status'] == "success3" ){
								this.restore_status = 3;
								this.restore_msg = "";
							}
						}else{
							this.restore_status = 3;
							this.restore_error = "Something wrong";
						}
					}
				}
			}).catch(error=>{
				this.restore_status = 3;
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
		load_snapshots: function(){
			axios.post("?", {
				"action":"exports_get_snapshots",
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.snapshots = response.data['snapshots'];
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
		geturl: function(){
			return this.path+'export/?action=download_snapshot&snapshot_file='+encodeURIComponent(this.snapshot_file);
			//return this.path+"export/?action=app_backup&app_id="+this.app_id+"&backup_pwd="+this.backup_pwd+"&backup_pass="+encodeURIComponent(this.backup_pass);
		},
		geturl2: function(v){
			return this.path+'export/?action=download_snapshot&snapshot_file='+encodeURIComponent(v);
			//return this.path+"export/?action=app_backup&app_id="+this.app_id+"&backup_pwd="+this.backup_pwd+"&backup_pass="+encodeURIComponent(this.backup_pass);
			},
		backupnow(){
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
					this.err = "Response Error: " . response.status;
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
								this.snapshot_size = response.data['sz'];
								//this.load_snapshots();
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
	}
}).mount("#app");
</script>