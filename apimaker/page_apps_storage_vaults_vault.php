<style>
	.download_url div{display: none;}
	.download_url:hover div{position: absolute; display: block; margin-top:-25px; background-color: white; padding: 5px; border: 1px solid #aaa;}
</style>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; "   data-id="draggable" v-on:drop.prevent="dropit" v-on:dragenter.prevent="dragenter" v-on:dragover.prevent="dragover" draggable   >
		<div v-if="dropdiv" style="position:absolute; margin-top:-2px; margin-left:-2px; padding:10px; padding:40px; width: calc( 100% + 5px ); height: calc( 100% + 5px ); background-color: rgba(255, 255, 255, 0.5); " v-on:dragenter.prevent="" v-on:dragover.prevent=""   v-on:dragleave.prevent="dragleave" >
			<div style="width: calc( 100% - 10px ); height: calc( 100% - 10px ); border:15px dashed #bbb; text-align: center; line-height: 300px; color:#bbb; font-size:5rem; " v-on:dragenter.prevent="" v-on:dragover.prevent="" >
				Drop files here
			</div>
		</div>

		<div style="padding: 10px;" >

			<div style="float:right;" >
				<button class="btn btn-outline-dark btn-sm ms-1" v-on:click="file_show_folder_form()" >Create Folder</button>
				<button class="btn btn-outline-dark btn-sm ms-1" v-on:click="file_show_upload_form()" >Upload</button>
			</div>

			<a class="btn btn-sm btn-outline-secondary float-end" v-on:href="path+'storage/'" >Back</a>
			<div class="h3 mb-3"><span class="text-secondary" >Storage Vault: {{ vault['vault_type'] }}: {{ vault['des'] }}</span></div>

			<div style="display:flex; height: 40px;">
				<div style="width:50%;">
					<div style="display: flex; height: 25px; margin-right:100px;">
						<div style="min-width:5px; cursor: pointer; padding:0px 5px; border:1px solid #ccc;" v-on:click="change_path('/')" >/</div>
						<div v-for="vv in paths" style="min-width:20px; cursor: pointer; padding:0px 5px; border:1px solid #ccc;" v-on:click="change_path(vv['tp'])" >{{ vv['p'] }}/</div>
					</div>
				</div>
				<div style="width:45%;">
					<div style="height: 25px;">
						<input type="text" class="form-control form-control-sm w-auto d-inline" v-model="keyword" placeholder="/path/filename">
						<input type="button" class="btn btn-outline-dark btn-sm" value="Search" v-on:click="searchit()">
					</div>
				</div>
			</div>

			<div style="position:relative;overflow: auto; height: calc( 100% - 130px );">

				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

				<table class="table table-sm table-bordered" >
					<thead style="position:sticky;top:0px; background-color:white;border-collapse: separate;" >
					<tr class="table-secondary bb-1">
						<td>Key</td>
						<td>-</td>
					</tr>
					</thead>
					<tbody>
					<template v-for="v,i in prefixes">
					<tr>
						<td>
							<button class="btn btn-link btn-sm px-0" v-on:click.stop.prevent="change_path('/'+v['Prefix'])" >/{{ v['Prefix'] }}</button>
							<div style="float:right; margin-right: 50px;" >
								Folder <span style="border:1px solid #ccc; padding: 0px 10px;" >{{ v['count'] }} Files</span>
							</div>
							<div style="clear: both;"></div>
						</td>
						<td width="30"><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_file(v['Prefix'])" ></td>
					</tr>
					</template>
					<template v-for="v,i in keys">
					<tr>
						<td>
							<div v-if="get_download_url(v['Key'])" style="float:left; width:20px; height:20px;" class="download_url" title="CDN URL">
								<div><a v-bind:href="get_download_url(v['Key'])" target="_blank" title="CDN URL" >{{ get_download_url(v['Key']) }}</a></div>
								<svg viewBox="0 0 64 64" fill="currentcolor"><path d="m27.75,44.73l4.24,4.24-3.51,3.51c-2.34,2.34-5.41,3.51-8.49,3.51-6.63,0-12-5.37-12-12,0-3.07,1.17-6.14,3.51-8.49l10-10c2.34-2.34,5.41-3.51,8.49-3.51s6.14,1.17,8.49,3.51l1.41,1.41-4.24,4.24-1.41-1.41c-1.13-1.13-2.64-1.76-4.24-1.76s-5.11,2.62-6.24,3.76l-8,8c-1.13,1.13-1.76,2.64-1.76,4.24,0,3.31,2.69,6,6,6,1.6,0,3.11-.62,4.24-1.76l3.51-3.51ZM44,8c-3.07,0-6.14,1.17-8.49,3.51l-3.51,3.51,4.24,4.24,3.51-3.51c1.13-1.13,2.64-1.76,4.24-1.76,3.31,0,6,2.69,6,6,0,1.6-.62,3.11-1.76,4.24l-10,10c-1.13,1.13-2.64,1.76-4.24,1.76s-3.11-.62-4.24-1.76l-1.41-1.41-4.24,4.24,1.41,1.41c2.34,2.34,5.41,3.51,8.49,3.51s6.14-1.17,8.49-3.51l10-10c2.34-2.34,3.51-5.41,3.51-8.49,0-6.63-5.37-12-12-12Z" /></svg>
							</div>
							<div v-if="get_mapped_url(v['Key'])" style="float:left; width:20px; height:20px;" class="download_url" title="Engine mapped URL" >
								<div><a v-bind:href="get_mapped_url(v['Key'])" target="_blank" title="Engine mapped URL" >{{ get_mapped_url(v['Key']) }}</a></div>
								<svg viewBox="0 0 64 64" fill="currentcolor"><path d="m27.75,44.73l4.24,4.24-3.51,3.51c-2.34,2.34-5.41,3.51-8.49,3.51-6.63,0-12-5.37-12-12,0-3.07,1.17-6.14,3.51-8.49l10-10c2.34-2.34,5.41-3.51,8.49-3.51s6.14,1.17,8.49,3.51l1.41,1.41-4.24,4.24-1.41-1.41c-1.13-1.13-2.64-1.76-4.24-1.76s-5.11,2.62-6.24,3.76l-8,8c-1.13,1.13-1.76,2.64-1.76,4.24,0,3.31,2.69,6,6,6,1.6,0,3.11-.62,4.24-1.76l3.51-3.51ZM44,8c-3.07,0-6.14,1.17-8.49,3.51l-3.51,3.51,4.24,4.24,3.51-3.51c1.13-1.13,2.64-1.76,4.24-1.76,3.31,0,6,2.69,6,6,0,1.6-.62,3.11-1.76,4.24l-10,10c-1.13,1.13-2.64,1.76-4.24,1.76s-3.11-.62-4.24-1.76l-1.41-1.41-4.24,4.24,1.41,1.41c2.34,2.34,5.41,3.51,8.49,3.51s6.14-1.17,8.49-3.51l10-10c2.34-2.34,3.51-5.41,3.51-8.49,0-6.63-5.37-12-12-12Z" /></svg>
							</div>
							<div style="float:left; width:20px; height:20px;" title="Download File" ><a target="_blank" class="text-dark" style="" title="Download File" v-bind:href="'?action=download&key='+encodeURIComponent(v['Key'])" ><i class="ri ri-ri ri-download-2-fill" ></i></a></div>
							<div>/{{ v['Key'] }}</div>
							<div style="color:gray;float:right;">
								<div v-if="'Size' in v" style="display: inline-block; width: 100px; overflow: hidden;">{{ getsz(v['Size']) }}</div>
								<div style="display: inline-block; width:100px; overflow: hidden; ">{{ getc(v) }}</div>
							</div>
							<div style="clear: both;"></div>
						</td>
						<td width="30"><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_file(v['Key'])" ></td>
					</tr>
					</template>
					</tbody>
				</table>

			</div>

		</div>
	</div>


		<div class="modal fade" id="upload_file_modal" tabindex="-1" >
		  <div class="modal-dialog  modal-xl">
		    <div class="modal-content" style="height: calc(100vh - 50px);overflow:auto;">
		      <div class="modal-header">
		        <h5 class="modal-title">Upload File</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" data-id="draggable" v-on:drop.prevent="dropit2"  data-id="draggable" v-on:dragenter.prevent="dragenter2" v-on:dragover.prevent="dragover2" draggable >
				<div v-if="dropdiv2" style="position:absolute; margin-top:-5px; margin-left:-5px; padding:10px; padding:40px; width: calc( 100% + 5px ); height: calc( 100% + 5px ); background-color: rgba(255, 255, 255, 0.5); " v-on:dragenter.prevent="" v-on:dragover.prevent=""   v-on:dragleave.prevent="dragleave2" >
					<div style="width: calc( 100% - 10px ); height: calc( 100% - 10px ); border:15px dashed #bbb; text-align: center; line-height: 300px; color:#bbb; font-size:3rem; " v-on:dragenter.prevent="" v-on:dragover.prevent="" >
						Drop files here
					</div>
				</div>
		      	<div>
		      		<input type="file" id="input_upload" multiple v-on:change="file_select" style="display: none;">
		      		<input type="button" value="Upload" v-on:click="filebrowse">
		      	</div>
				<div>&nbsp;</div>
				<div v-for="fd,fi in upload_list" class="" style="margin-bottom:5px; border-bottom: 1px solid #ccc; padding: 5px; display: flex; gap:5px;" >
					<div style="width: 100px; overflow: hidden; " >
						<div>{{ getext(fd['t']) }}</div>
					</div>
					<div style="width: calc( 100% - 120px );">
						<div>{{ fd['n'] }}</div>
						<div><span class="text-secondary">{{ fd['t'] }}</span>&nbsp; <span class="text-secondary">{{ tellsize(fd['s']) }}</span></div>
						<div v-if="fd['er']" class="text-danger" >{{ fd['er'] }}</div>
					</div>
					<div style="width: 100px; " >
						<div v-if="fd['st']=='wait-to-upload'">Pending</div>
						<div v-if="fd['st']=='error'" class="text-danger">Error</div>
						<div v-if="fd['st']=='uploading'">Uploading<BR><span style="font-size:2rem;" >{{ fd['pg'] }}%</span></div>
						<div v-if="fd['st']=='uploaded'">Ready</div>
					</div>
					<div><div class="btn btn-outline-danger btn-sm" v-on:click="upload_list_del(fi)" >X</div></div>
				</div>
				<div v-if="cmsg" class="alert alert-success" >{{ cmsg }}</div>
				<div v-if="cerr" class="alert alert-success" >{{ cerr }}</div>
				<!-- <pre>{{ upload_list }}</pre> -->
		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="create_folder_modal" tabindex="-1" >
		  <div class="modal-dialog model-sm">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Create Folder</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

		      	<div>New folder name</div>
		      	<div><span>{{ current_path }}</span><input type="text" class="form-control form-control-sm w-auto d-inline" v-model="new_folder_name" ></div>
		      	<div><input type="button" class="btn btn-outline-dark btn-sm" value="Create" v-on:click="do_create_folder" ></div>

				<div v-if="cfmsg" class="alert alert-primary" >{{ cfmsg }}</div>
				<div v-if="cferr" class="alert alert-danger" >{{ cferr }}</div>


		      </div>
		    </div>
		  </div>
		</div>
		<div class="modal fade" id="settings_modal" tabindex="-1" >
		  <div class="modal-dialog model-sm">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Storage Settings</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

		      	<div><b>Storage Vaults</b></div>

				<div v-if="smsg" class="alert alert-primary" >{{ smsg }}</div>
				<div v-if="serr" class="alert alert-danger" >{{ serr }}</div>

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
				"vaultpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/storage/<?=$config_param3 ?>/",
				"vault": <?=json_encode($vault) ?>,
				"app_id": "<?=$app['_id'] ?>",
				"vault_id"		: "<?=$vault['_id'] ?>",
				"cmsg": "", "cerr":"","msg": "", "err":"",
				"keys": [], "prefixes":[],
				"test_environments": <?=json_encode($test_environments) ?>,
				upload_list: [],
				new_folder_name: "",
				current_path: "<?=$_GET['path']?$_GET['path']:'/' ?>",
				paths: [],
				active_uploads: 0,
				keyword: "",
				ext: {
					"txt":"text/plain",
					"text":"text/plain",
					"js":"text/javascript",
					"json": "application/json",
					"html": "text/html",
					"xml": "text/xml",
					"svg": "image/svg",
					"css": "text/css",
				},
				show_create_file: false,
				new_file: {
					"name": "",
					"type": "html",
					"ssr": false,
				},
				create_file_modal: false,
				upload_file_modal: false,
				create_folder_modal: false,
				token: "",
				dropdiv: false,dropdiv2: false,
			};
		},
		mounted:function(){
			this.update_paths();
			this.load_keys();
			setInterval(this.check_queue,100);
		},
		methods: {
			get_download_url: function( vkey ){
				if( this.vault['vault_type'] == 'AWS-S3' ){
					if( 'public' in this.vault['details'] && 'static' in this.vault['details'] ){
						if( this.vault['details']['public']===true && this.vault['details']['static']===true ){
							if( this.vault['details']['alias'] ){
								return "/"+"/"+this.vault['details']['alias'] + "/" + vkey;
							}else{
								return "http:"+"/"+"/"+this.vault['details']['bucket'] + ".s3-website."+this.vault['details']['region'] + ".amazonaws.com/" + vkey;
							}
						}
					}
				}
				return false;
			},
			get_mapped_url: function( vkey ){
				console.log( this.test_environments );
				if( this.vault['vault_type'] == 'AWS-S3' ){
					if( 'rewrite' in this.vault['details'] && 'rewrite_path' in this.vault['details'] && 'dest_path' in this.vault['details'] ){
						return this.test_environments[0]['u'].substr(0,this.test_environments[0]['u'].length-1) + this.vault['details']['rewrite_path'] + vkey;
					}
				}
				return false;
			},
			getc: function(v){
				if( 'LastModified' in v ){
					return v['LastModified'].substr(0,10);
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
			dragenter: function(e){
				console.log("dragenter: " + e.target.nodeName );
				var v = e.target;
				for(var i=0;i<10;i++){
					if( v.nodeName == "BODY" || v.nodeName == "#html" ){
						break;
					}
					if( v.hasAttribute("data-id") ){
						if( v.getAttribute("data-id") == "draggable" ){
							console.log("OK");
							this.dropdiv = true;
							break;
						}
					}
					v = v.parentNode;
				}
			},
			dragenter2: function(e){
				console.log("dragenter: " + e.target.nodeName );
				var v = e.target;
				for(var i=0;i<10;i++){
					if( v.nodeName == "BODY" || v.nodeName == "#html" ){
						break;
					}
					if( v.hasAttribute("data-id") ){
						if( v.getAttribute("data-id") == "draggable" ){
							console.log("OK");
							this.dropdiv2 = true;
							break;
						}
					}
					v = v.parentNode;
				}
			},
			dragover: function(e){
				console.log("dragover: " + e.target.nodeName );
			},
			dragover2: function(e){
				console.log("dragover: " + e.target.nodeName );
			},
			dragleave: function(e){
				this.dropdiv = false;
			},
			dragleave2: function(e){
				this.dropdiv2 = false;
			},
			dropit: function(e){
				this.dropdiv = false;
				this.dropitt(e);
			},
			dropit2: function(e){
				this.dropdiv2 = false;
				this.dropitt(e);
			},
			dropitt: function(e){
				console.log( e.dataTransfer.files );
				for(var i=0;i<e.dataTransfer.files.length;i++){
					var vf = e.dataTransfer.files[i];
					var n = vf.name+'';n = n.split(".");
					var ext = n.pop();n = n.join(".");
					n = this.cleanit(n)+ "." + ext;
					this.upload_list.push({
						"n": n,
						"o": vf,
						"s": vf.size,
						"t": vf.type,
						"st": "wait-to-upload",
						"er": "",  // error
						"pg":  0,  // progress,
						"hst": "", // http status
						"_id": "", // file id,
						"version_id":"", // file version id
					});
					this.file_show_upload_form();
				}
			},
			nchange: function(){
				var m = this.new_file['name'].match(/\.([a-z]{2,5})$/);
				if( m != null ){
					if( m[1] in this.ext ){
						this.new_file['type'] = this.ext[ m[1] ];
					}
				}
			},
			searchit: function(){
				this.load_keys();
			},
			change_path: function(tp){
				console.log("change path: "+ tp );
				this.current_path = tp+'';
				this.upload_list = [];
				this.update_paths();
				this.load_keys();
			},
			enter_path: function(v){
				//alert( this.current_path );
				this.current_path = this.current_path + v + "/";
				//alert( this.current_path );
				this.upload_list = [];
				console.log( this.current_path );
				this.update_paths();
				this.load_keys();
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
				this.keys = [];
				this.paths = p;
				console.log( JSON.stringify(p,null,4) );
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
			
			file_show_upload_form(){
				//this.upload_list = [];
				this.upload_file_modal = new bootstrap.Modal(document.getElementById('upload_file_modal'));
				this.upload_file_modal.show();
				this.cmsg = ""; this.cerr = "";
				if( this.upload_list.length == 0 ){
					setTimeout(function(v){v.filebrowse();},500,this);
				}
			},
			file_show_folder_form(){
				this.create_folder_modal = new bootstrap.Modal(document.getElementById('create_folder_modal'));
				this.create_folder_modal.show();
				this.cmsg = ""; this.cerr = "";
			},
			file_show_settings(){
				this.settings_modal = new bootstrap.Modal(document.getElementById('settings_modal'));
				this.settings_modal.show();
				this.smsg = ""; this.serr = "";
			},
			cleanit( v ){
				v = v.replace( /\-/g, "DASH" );
				v = v.replace( /\//g, "SLASHS" );
				v = v.replace( /\_/g, "UDASH" );
				v = v.replace( /\./g, "DOTT" );
				v = v.replace( /\W/g, "-" );
				v = v.replace( /DASH/g, "-" );
				v = v.replace( /UDASH/g, "_" );
				v = v.replace( /DOTT/g, "." );
				v = v.replace( /SLASHS/g, "/" );
				v = v.replace( /[\-]{2,5}/g, "-" );
				v = v.replace( /[\_]{2,5}/g, "_" );
				return v;
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
											"action":"files_create_folder",
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
															this.enter_path( this.new_folder_name+'' );
															this.new_folder_name = '';
															this.create_folder_modal.hide();
															//this.load_keys();
														}else{
															this.cferr = "error:" + response.data['error'];
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
									alert("Token error: " + response.dat['error']);
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
			delete_file( vi ){
				if( confirm("Are you sure?") ){
					this.msg = "Deleting...";
					this.err = "";
					axios.post("?", {
						"action":"get_token",
						"event":"deletefile"+this.app_id+vi,
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
												"action":"delete_file",
												"token":this.token,
												"Key": vi
											}).then(response=>{
												this.msg = "";
												if( response.status == 200 ){
													if( typeof(response.data) == "object" ){
														if( 'status' in response.data ){
															if( response.data['status'] == "success" ){
																this.load_keys();
															}else{
																alert("Delete error: " + response.data['error']);
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
			},
			tellsize(v){
				if( v < 1024 ){
					return v + " bytes";
				}
				v = (v/1024).toFixed(2);
				if( v < 1024 ){
					return v + " KB";
				}
				v = (v/1024).toFixed(2);
				if( v < 1024 ){
					return v + " MB";
				}
				v = (v/1024).toFixed(2);
				if( v < 1024 ){
					return v + " GB";
				}
			},
			getext: function(v){
				v = v.split(/\+/)[0];
				//console.log( v );
				return v;
			},
			filebrowse: function(){
				document.getElementById( "input_upload"  ).click();
			},
			file_select(){
				var vf = document.getElementById( "input_upload"  ).files;
				for( var i=0;i<vf.length;i++ ){
					//console.log( vf[i] );
					var n = vf[i].name+'';n = n.split(".");
					var ext = n.pop();n = n.join(".");
					n = this.cleanit(n)+ "." + ext;
					this.upload_list.push({
						"n": n,
						"o": vf[i],
						"s": vf[i].size,
						"t": vf[i].type,
						"st": "wait-to-upload",
						"er": "",  // error
						"pg":  0,  // progress,
						"hst": "", // http status
						"_id": "", // file id,
						"version_id":"", // file version id
					});
				}
				vf.value = "";
			},
			upload_list_del: function(vi){
				this.upload_list.splice(vi,1);
			},
			check_queue: function(){
				for(var i=0;i<this.upload_list.length;i++){
					if( this.upload_list[ i ]['st'] == "wait-to-upload" ){
						for(var j=0;j<this.keys.length;j++){
							if( 'Key' in this.keys[ j ] == false ){
								console.error("files name not found: " ); 
							}else if( this.keys[ j ]['Key'].toLowerCase() == this.upload_list[ i ]['n'].toLowerCase() ){
								this.upload_list[ i ]['er'] = "A file already exists with same name";
								this.upload_list[ i ]['st'] = "error";
							}
						}
						if( this.upload_list[ i ]['st'] != "error" ){
							if( this.upload_list[ i ]['s'] > (1024*1024*5) ){
								this.upload_list[ i ]['er'] = "Too Big";
								this.upload_list[ i ]['st'] = "error";
							}else if( this.active_uploads < 1 ){
								this.start_upload(i);
							}
						}
					}
				}
			},
			start_upload: function(vi){

				this.active_uploads++;

				this.upload_list[ vi ]['st'] = 'uploading';
				this.upload_list[ vi ]['pg'] = 0;
				axios.post("?", {
					"action":"get_token",
					"event":"file.upload."+this.app_id,
					"expire":2
				}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									if( this.is_token_ok(response.data['token']) ){
										this.upload_list[ vi ]['token'] = response.data['token'];
										this.start_upload2(vi);
									}
								}else{
									this.active_uploads--;
									this.upload_list[ vi ]['st'] = "error";
									this.upload_list[ vi ]['er'] = "token error";
								}
							}else{
								this.active_uploads--;
								this.upload_list[ vi ]['st'] = "error";
								this.upload_list[ vi ]['er'] = "token error";
							}
						}else{
							this.active_uploads--;
							this.upload_list[ vi ]['st'] = "error";
							this.upload_list[ vi ]['er'] = "token error";
						}
					}else{
						this.active_uploads--;
						this.upload_list[ vi ]['st'] = "error";
						this.upload_list[ vi ]['er'] = "token error";
					}
				});
			},
			remove_from_list: function(vid){
				for(var j=0;j<this.upload_list.length;j++){
					if( this.upload_list[j]['_id'] == vid ){
						this.upload_list.splice(j,1);break;
					}
				}
			},
			start_upload2: function( vi ){
				var vf = new FormData();
				vf.append("action", "apps_file_upload");
				vf.append("file", this.upload_list[ vi ]['o'] );
				vf.append("name", this.upload_list[ vi ]['n'] );
				vf.append("type", this.upload_list[ vi ]['t'] );
				vf.append("token", this.upload_list[ vi ]['token'] );
				vf.append("path", this.current_path );
				this.upload_list[ vi ]['ax'] = axios.post("?",vf,{
					onUploadProgress: function (e) {
						var l = (e.loaded/e.total*100).toFixed(0);
						console.log( (e.loaded/e.total*100).toFixed(0) );
						app.upload_list[ vi ]['pg'] = l;
					}
				}).then(response=>{
					this.active_uploads--;
					if( response.status == 200 ){
						if( typeof(response.data)=="object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.upload_list[ vi ]['st'] = "uploaded";
									this.upload_list[ vi ]['_id'] = response.data['data']['Key'];
									this.keys.push(response.data['data']);
									setTimeout(function(v,vid){v.remove_from_list(vid)},5000,this,response.data['data']['Key']+'');
								}else{
									this.upload_list[ vi ]['st'] = "error";
									this.upload_list[ vi ]['er'] = response.data['error'];
								}
							}else{
								this.upload_list[ vi ]['st'] = "error";
								this.upload_list[ vi ]['er'] = 'Incorrect Response';
							}
						}else{
							this.upload_list[ vi ]['st'] = "error";
							this.upload_list[ vi ]['er'] = 'Incorrect Response';
						}
					}else{
						this.upload_list[ vi ]['st'] = "error";
						this.upload_list[ vi ]['er'] = 'http:'+response.status;
					}
				}).catch(error=>{
					this.active_uploads--;
					this.upload_list[ vi ]['st'] = "error";
					this.upload_list[ vi ]['er'] = "upload fail";
					console.log( error );
				});
			},
			load_keys: function(){

				var k = "";
				if( this.keyword != "" ){
					if( this.keyword.match(/^\/[a-z0-9\.\/\_\-\(\)\ ,\.\:\[\]\!\@\$\%\*]+$/i) == null ){
						alert("Keyword format ignored");
					}else{
						k = this.keyword+'';
					}
				}

				this.msg = "Loading...";
				axios.post("?",{
					"action" 		: "storage_vault_load_keys",
					"current_path": this.current_path,
					"keyword": k,
				}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									var cp = this.current_path.substr(1,5000);
									this.prefixes = response.data['prefixes'];
									this.keys = response.data['keys'];
									for(var i=0;i<this.keys.length;i++){
										console.log( this.keys[i]['Key'] + ": " + cp );
										if( this.keys[i]['Key'] == cp ){
											this.keys.splice(i,1);break;
										}
									}
									for(var i=0;i<this.prefixes.length;i++){
										console.log( this.prefixes[i]['Prefix'] + ": " + cp );
										if( this.prefixes[i]['Prefix'] == cp ){
											this.prefixes.splice(i,1);break;
										}
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
			}
		}
}).mount("#app");
</script>
