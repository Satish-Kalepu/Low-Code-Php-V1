<style>
	div.vid{ padding:0px 2px; cursor:pointer; }
	div.vid pre.vid{display: none; position: absolute; background-color: white; padding: 3px; border: 1px solid #aaa;}
	div.vid:hover pre.vid{display: block;}
</style>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " data-id="draggable" v-on:drop.prevent="dropit" v-on:dragenter.prevent="dragenter" v-on:dragover.prevent="dragover" draggable   >
		<div v-if="dropdiv" style="position:absolute; margin-top:-2px; margin-left:-2px; padding:10px; padding:40px; width: calc( 100% + 5px ); height: calc( 100% + 5px ); background-color: rgba(255, 255, 255, 0.5); " v-on:dragenter.prevent="" v-on:dragover.prevent=""   v-on:dragleave.prevent="dragleave" >
			<div style="width: calc( 100% - 10px ); height: calc( 100% - 10px ); border:15px dashed #bbb; text-align: center; line-height: 300px; color:#bbb; font-size:5rem; " v-on:dragenter.prevent="" v-on:dragover.prevent="" >
				Drop files here
			</div>
		</div>
		<div style="padding: 10px;" >
			<div style="float:right;" >
				<button class="btn btn-outline-dark btn-sm ms-1" v-on:click="file_show_create_form()" >Create File</button>
				<button class="btn btn-outline-dark btn-sm ms-1" v-on:click="file_show_folder_form()" >Create Folder</button>
				<button class="btn btn-outline-dark btn-sm ms-1" v-on:click="file_show_upload_form()" >Upload</button>
				<button class="btn btn-outline-dark btn-sm ms-1" v-on:click="file_show_settings()" >Configure</button>
			</div>
			<div class="h3 mb-3 w-auto">Files</div>

			<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
			<div v-if="err" class="alert alert-danger" >{{ err }}</div>

			<div style="display: flex; height: 25px;">
				<div style="min-width:5px; cursor: pointer; padding:0px 5px; border:1px solid #ccc;" v-on:click="change_path('/')" >/</div>
				<div v-for="vv in paths" style="min-width:20px; cursor: pointer; padding:0px 5px; border:1px solid #ccc;" v-on:click="change_path(vv['tp'])" >{{ vv['p'] }}/</div>
			</div>
			<div style="height: calc( 100% - 130px ); overflow: auto;"  >
			<table class="table table-striped table-bordered table-sm" >
				<tr>
					<td>ID</td>
					<td>Name/Path</td>
					<td></td>
				</tr>
				<template v-for="v,i in files">
				<tr v-if="v['vt']=='folder'">
					<td><div class="vid">#<pre class="vid">{{v['_id']}}</pre></div></td>
					<td width="90%">
						<div v-if="'vault' in v&&v['type']=='mounted'" >
							<a v-bind:href="path+'storage/'+v['vault']['vault_id']" >{{ current_path + v['name'] }}/</a>
							<div align="right" >
								Mounted Storage Vault {{ v['vault']['vault_type'] + ' ('+v['vault']['vault_name'] + ')'+ v['vault']['vault_path'] }}
							</div>
						</div>
						<div v-else-if="v['type']=='thumbs'" >
							<a href='#' v-on:click.stop.prevent="open_thumb_rewrite(v)" >{{ current_path + v['name'] }}/</a>
							<div align="right" >
								Thumbs Rewrite {{ v['vault']['vault_type'] + ' ('+v['vault']['vault_name'] + ')'+ v['vault']['vault_path'] }}
							</div>
						</div>
						<div v-else >
							<div v-if="v['vt']=='folder'"><a v-bind:href="path+'files/?path='+v['name']" v-on:click.stop.prevent="enter_path(v['name'])" >{{ current_path + v['name'] }}/</a></div>
							<div align="right">
								Folder
							</div>
						</div>
					</td>
					<td><input v-if="'vault' in v == false&&v['type']!='mounted'" type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_file(i)" ></td>
				</tr>
				</template>
				<template v-for="v,i in files">
				<tr v-if="v['vt']!='folder'">
					<td><div class="vid">#<pre class="vid">{{v['_id']}}</pre></div></td>
					<td width="90%">
						<div v-if="v['vt']=='folder'"><a v-bind:href="path+'files/?path='+v['name']" v-on:click.stop.prevent="enter_path(v['name'])" >{{ current_path + v['name'] }}/</a></div>
						<div v-else><a v-bind:href="path+'files/'+v['_id']+'/edit'" >{{ current_path + v['name'] }}</a></div>
						<div align="right" style="color:gray;">
							<div v-if="'sz' in v" style="display: inline-block; width: 100px; overflow: hidden;">{{ getsz(v['sz']) }}</div>
							<div style="display: inline-block; width:100px; overflow: hidden; ">{{ getc(v) }}</div>
							<div style="display: inline-block; width: 100px; overflow: hidden;">{{ v['type'] }}</div>
						</div>
					</td>
					<td><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_file(i)" ></td>
				</tr>
				</template>
			</table>
			</div>
		</div>
	</div>

		<div class="modal fade" id="create_file_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Create File</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">
		        	<div>Name/URL Slug</div>
		        	<input type="text" class="form-control form-control-sm" v-model="new_file['name']" placeholder="Name" v-on:keyup="nchange" >
		        	<div class="text-secondary small">no spaces. no special chars. except dash(-). lowercase recommended</div>
		        	<div>&nbsp;</div>
		        	<div>Type</div>
		        	<input type="text" list="eee" class="form-control form-control-sm" v-model="new_file['type']" placeholder="text/html" >
		        	<datalist id="eee" ><option v-for="v in ext" v-bind:value="v" ></option></datalist>
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

				<div style="border:1px solid #ccc;">
					<div style="padding:10px; background-color: #f8f8f8; border-bottom: 1px solid #ccc;">Mount Storage Vault</div>
					<div style="padding:10px; ">
						Screen moved to storage vaults
					</div>
				</div>

		      </div>
		    </div>
		  </div>
		</div>


	<div class="modal fade" id="url_modal" tabindex="-1" >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Browse/Download File</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <template v-if="'cloud' in vurls" >
                <p>Cloud Hosting: </p>
                <p>
                  <a target="_blank" v-bind:href="vurls['cloud']" >{{ vurls['cloud'] }}</a>
                </p>
                <template v-if="'alias' in vurls" >
                  <p>Alias domain:</p>
                  <p>
                    <a target="_blank" v-bind:href="vurls['alias']" >{{ vurls['alias'] }}</a>
                  </p>
                </template>
              </template>
              <template v-if="'domains' in vurls" >
                <p>Custom Hosting: </p>
                <p v-for="u in vurls['domains']" >
                  <a target="_blank" v-bind:href="u" >{{ u }}</a>
                </p>
              </template>
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
			msg: "",err: "",cmsg: "",cerr: "",smsg: "",serr: "",cfmsg: "",cferr: "",cnfmsg: "",cnferr: "",
			files: [],
			upload_list: [],
			new_folder_name: "",
			current_path: "<?=$_GET['path']?$_GET['path']:'/' ?>",
			paths: [],
			active_uploads: 0,
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
			new_mount: {
				"vault_id": "", "vault_name": "",
				"vault_path": "/", "local_path": "/mount_folder/"
			},
			storage_vaults: [],
			token: "",
			dropdiv: false,dropdiv2: false,
			test_envs__: [],
			test_url__: "",
			test_domain__: "",
			url_modal: false,
			vurls: [],
		};
	},
	mounted(){
		this.update_paths();
		this.load_files();
		setInterval(this.check_queue,100);
		//document.addEventListener("drop", function(e){e.preventDefault();e.stopPropagation();app.dropit(e);}, true);
	    this.set_test_environments__();
	},
	methods: {

		set_test_environments__: function() {
			var e = [];
			for (var d in this.app__['settings']['domains']) {
				e.push({
					"t": "custom",
					"u": this.app__['settings']['domains'][d]['url'],
					"d": this.app__['settings']['domains'][d]['domain'],
				});
			}
			if ('cloud' in this.app__['settings']) {
				if (this.app__['settings']['cloud']) {
					var d = this.app__['settings']['cloud-subdomain'] + "." + this.app__['settings']['cloud-domain'];
					e.push({
						"t": "cloud",
						"u": "https://" + d + "/",
						"d": d,
					});
				}
			}
			if ('alias' in this.app__['settings']) {
				if (this.app__['settings']['alias']) {
					var d = this.app__['settings']['alias-domain'];
					e.push({
						"t": "cloud-alias",
						"u": "https://" + d + "/",
						"d": d,
					});
				}
			}
			this.test_envs__ = e;
			if (e.length == 1) {
				this.test_domain__ = e[1]['d'] + '';
				this.select_test_environment__2();
			}
		},
		select_test_environment__: function() {
			setTimeout(this.select_test_environment__2, 200);
		},
		select_test_environment__2: function() {
			for (var i = 0; i < this.test_envs__.length; i++) {
				//in this.app__['settings']['domains'] ){
				if (this.test_envs__[i]['d'] == this.test_domain__) {
					//this.test__['path'] = this.app__['settings']['domains'][ d ]['path'];
					var tu = this.test_envs__[i]['u'] + "?version_id=<?=$config_param4 ?>&test_token=<?=md5($config_param4) ?>";
					if (this.test_debug__) {
						tu = tu + "&debug=true";
					}
					if (this.api__['input-method'] == "GET") {
						tu = tu + "&" + this.make_query_string__(this.test__['factors']['v']);
					}
					this.test_url__ = tu;
					break;
				}
			}
		},
		open_thumb_rewrite: function(vp){
			var urls = {};
			if( 'cloud' in this.app__['settings'] ){if( this.app__['settings']['cloud'] ){
				urls['cloud'] = "https://" + this.app__['settings']['cloud-subdomain'] + '.' + this.app__['settings']['cloud-domain'] + '/' + this.app__['settings']['cloud-enginepath'] + vp['path'].substr(1,500) + vp['name'];
				if( 'alias' in this.app__['settings'] ){if( this.app__['settings']['alias'] ){
					urls['alias'] = "https://" + this.app__['settings']['alias-domain'] + vp['path'].substr(1,500) + vp['name'];
				}}
			}}

			if( 'domains' in this.app__['settings'] ){
				urls['domains'] = [];
				for(var d=0;d<this.app__['settings']['domains'].length;d++ ){
					urls['domains'].push( this.app__['settings']['domains'][ d ]['url'] + vp['path'].substr(1,500) + vp['name'] );
				}
			}
			this.vurls = urls;
			this.url_modal = new bootstrap.Modal(document.getElementById('url_modal'));
			this.url_modal.show();
		},

		getc: function(v){
			if( 'm_i' in v ){
				return v['m_i'].substr(0,10);
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
		change_path: function(tp){
			console.log("change path: "+ tp );
			this.current_path = tp+'';
			this.upload_list = [];
			this.update_paths();
			this.load_files();
		},
		enter_path: function(v){
			this.current_path = this.current_path + v + "/";
			this.upload_list = [];
			console.log( this.current_path );
			this.update_paths();
			this.load_files();
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
			this.files = [];
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
		load_storage_vaults: function(){
			axios.post("?",{
				"action":"load_storage_vaults",
			}).then(response=>{
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.storage_vaults = response.data['data'];
							}else{
								alert("error: " + response.data['error']);
							}
						}else{
							alert("Incorrect response");
						}
					}else{
						alert("Incorrect response Type");
					}
				}else{
					alert("Response Error: " . response.status);
				}
			});
		},
		load_files(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?", {
				"action":"get_token",
				"event":"getfiles."+this.app_id,
				"expire":2
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.load_files2();
								}
							}else{
								alert("Token error: " + response.dat['error']);
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
		load_files2(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?",{
				"action":"get_files",
				"app_id":this.app_id,
				"token":this.token,
				"current_path": this.current_path,
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.files = response.data['data'];
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
		file_show_create_form(){
			this.create_file_modal = new bootstrap.Modal(document.getElementById('create_file_modal'));
			this.create_file_modal.show();
			this.cmsg = ""; this.cerr = "";
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
			this.load_storage_vaults();
			this.smsg = ""; this.serr = "";
		},
		cleanit( v ){
			v = v.replace( /\//g, "SLASHS" );
			v = v.replace( /\_/g, "UDASH" );
			v = v.replace( /\-/g, "DASH" );
			v = v.replace( /\./g, "DOTT" );
			v = v.replace( /\W/g, "-" );
			v = v.replace( /UDASH/g, "_" );
			v = v.replace( /DOTT/g, "." );
			v = v.replace( /SLASHS/g, "/" );
			v = v.replace( /DASH/g, "-" );
			v = v.replace( /[\-]{2,5}/g, "-" );
			v = v.replace( /[\_]{2,5}/g, "_" );
			return v;
		},
		createnow(){
			this.cerr = "";
			this.new_file['name'] = this.cleanit(this.new_file['name']);
			if( this.new_file['name'].match(/^[a-z0-9\.\-\_\/]{3,100}\.[a-z]{2,4}$/i) == null ){
				this.cerr = "Filename must have an extension. Special chars not allowed. Length minimum 4 max 100";
				return false;
			}
			if( this.new_file['type'].match(/^[a-z]{2,50}\/[a-z]{2,50}$/i) == null ){
				this.cerr = "File type incorrect format";
				return false;
			}
			this.cmsg = "Creating...";
			axios.post("?", {
				"action": "create_file", 
				"new_file": this.new_file,
				"current_path": this.current_path,
			}).then(response=>{
				this.cmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.cmsg = "Created";
								this.create_file_modal.hide();
								this.load_files();
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
														this.load_files();
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
					"event":"deletefile"+this.app_id+this.files[vi]['_id'],
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
											"file_id": this.files[ vi ]['_id']
										}).then(response=>{
											this.msg = "";
											if( response.status == 200 ){
												if( typeof(response.data) == "object" ){
													if( 'status' in response.data ){
														if( response.data['status'] == "success" ){
															this.load_files();
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
									alert("Token error: " + response.dat['error']);
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
					for(var j=0;j<this.files.length;j++){
						if( 'name' in this.files[ j ] == false ){
							console.error("files name not found: " ); console.log( JSON.stringify(this.files[ j ],null,4) );
						}else if( this.files[ j ]['name'].toLowerCase() == this.upload_list[ i ]['n'].toLowerCase() ){
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
								this.upload_list[ vi ]['_id'] = response.data['insert_id'];
								this.files.push(response.data['data']);
								setTimeout(function(v,vid){v.remove_from_list(vid)},5000,this,response.data['insert_id']+'');
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
		select_vault: function(){
			for( var i=0;i<this.storage_vaults.length;i++){
				if( this.storage_vaults[i]['vault_id'] == this.new_mount['vault_id'] ){
					this.new_mount['vault_name'] = this.storage_vaults[i]['des']+'';
				}
			}
		},
		mountit: function(){
			
		},
	}
}).mount("#app");
</script>