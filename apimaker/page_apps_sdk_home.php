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
				<button class="btn btn-outline-dark btn-sm" v-on:click="sdk_show_create_form()" >Create SDK</button>
			</div>
			<div class="h3 mb-3">SDK</div>
			<div style="clear: both;"></div>
			<div style="height: calc( 100% - 100px ); overflow: auto;" >
				<div v-if="msg" class="alert alert-primary py-0" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger py-0" >{{ err }}</div>

				<table class="table table-striped table-bordered table-sm" >
					<tbody>
					<tr>
						<td>ID</td>
						<td>Name</td>
						<td></td>
					</tr>
					<tr v-for="v,i in sdks">
						<td><div class="vid">#<pre class="vid">{{v['_id']}}</pre></div></td>
						<td width="90%">
							<div><a v-bind:href="path+'sdk/'+v['_id']+'/'+v['version_id']" >/{{ v['name'] }}</a></div>
							<div class="text-secondary">{{ v['des'] }}</div>
						</td>
						<td><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_sdk(i)" ></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="create_app_modal" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Create SDK</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        	<div>Name/URL Slug</div>
	        	<input type="text" class="form-control" v-model="new_sdk['name']" placeholder="Name" v-on:change="nchange" >
	        	<div class="text-secondary small">no spaces. no special chars. except dash(-/_). Title case recommended</div>
	        	<div>&nbsp;</div>
	        	<div>Description</div>
	        	<textarea class="form-control" v-model="new_sdk['des']" ></textarea>
	        	<div class="text-secondary small">no special chars except (-_,.&). minmum 5 chars</div>
	        	<div>&nbsp;</div>
	        	<div>Keywords</div>
	        	<div v-for="kv,ki in new_sdk['keywords']" style="display:flex; column-gap:10px;" >
	        		<div style="width:200px;"><input type="text" v-model="new_sdk['keywords'][ki]" class="form-control form-control-sm" placeholder="keyword" v-on:blur="clean_keyword(ki)"></div>
	        		<div><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_keyword(ki)"></div>
	        	</div>
	        	<div><input type="button" class="btn btn-outline-dark btn-sm" value="+" v-on:click="add_keyword()"></div>
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

</div>
<script>
var app = Vue.createApp({
	data(){
		return {
			path: "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			app_id: "<?=$app['_id'] ?>",
			app__: <?=json_encode($app) ?>,
			msg: "",
			err: "",
			cmsg: "",
			cerr: "",
			sdks: [],
			show_create_sdk: false,
			new_sdk: {
				"name": "",
				"des": "",
				"keywords": [],
			},
			create_app_modal: false,
			token: "",
		};
	},
	mounted(){
		this.load_sdks();
	},
	methods: {
		add_keyword: function(){
			this.new_sdk['keywords'].push("");
		},
		delete_keyword: function(vi){
			if( this.new_sdk['keywords'].length == 1 ){
				alert("minimum one keyword is required for search");return;
			}
			this.new_sdk['keywords'].splice(vi,1);
		},
		clean_keyword: function(vi){
			this.new_sdk['keywords'][vi] = this.cleanit2( this.new_sdk['keywords'][vi] );
		},
		nchange: function(){
			this.new_sdk['name'] = this.cleanit(this.new_sdk['name']);
			if( this.new_sdk['des']=="" ){
				this.new_sdk['des'] = this.new_sdk['name']+'';
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
		load_sdks(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?",{"action":"get_sdks", "app_id":this.app_id,"token":this.token}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.sdks = response.data['data'];
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
		sdk_show_create_form(){
			this.create_app_modal = new bootstrap.Modal(document.getElementById('create_app_modal'));
			this.create_app_modal.show();
			this.cmsg = "";this.cerr = "";
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
		cleanit2(v){
			v = v.replace( /\-/g, "DDD" );
			v = v.replace( /\_/g, "UUU" );
			v = v.replace( /\./g, "DTDT" );
			v = v.replace( /\ /g, "SPACE" );
			v = v.replace( /[\W]+/g, "" );
			v = v.replace( /SPACE/g, " " );
			v = v.replace( /[\ ]{2,5}/g, " " );
			v = v.trim();
			v = v.replace( /UUU/g, "_" );v = v.replace( /DDD/g, "-" );v = v.replace( /DTDT/g, "." );
			v = v.replace( /[\-]{2,5}/g, "-" );
			v = v.replace( /[\_]{2,5}/g, "_" );			
			return v;
		},
		createnow(){
			this.cerr = "";
			this.new_sdk['name'] = this.cleanit( this.new_sdk['name']  );
			if( this.new_sdk['name'].match(/^[a-z][a-z0-9\.\-\_]{2,150}$/i) == null ){
				this.cerr = "Name incorrect. Special chars not allowed. Length minimum 3 max 100";
				return false;
			}
			if( this.new_sdk['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\ \r\n]{2,300}$/i) == null ){
				this.cerr = "Description incorrect. Special chars not allowed. Length minimum 5 max 200";
				return false;
			}
			for( var i=0;i<this.new_sdk['keywords'].length;i++){
				if( this.new_sdk['keywords'][i].trim() == "" ){
					this.new_sdk['keywords'].splice(i,1);
					i--;
				}
			}
			if( this.new_sdk['keywords'].length == 0){
				this.cerr = "One keyword is required for search";return;
			}
			this.cmsg = "Creating...";
			axios.post("?", {
				"action": "create_sdk", 
				"new_sdk": this.new_sdk
			}).then(response=>{
				this.cmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.cmsg = "Created";
								this.create_app_modal.hide();
								document.location = this.path + "sdk/" + response.data['sdk_id'] + '/'+ response.data['sdk_version_id'];
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
		delete_sdk( vi ){
			if( confirm("Are you sure?") ){
				this.msg = "Deleting...";
				this.err = "";
				axios.post("?", {
					"action":"get_token",
					"event":"deletesdk"+this.app_id+this.sdks[vi]['_id'],
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
											"action":"delete_sdk",
											"token":this.token,
											"sdk_id": this.sdks[ vi ]['_id']
										}).then(response=>{
											this.msg = "";
											if( response.status == 200 ){
												if( typeof(response.data) == "object" ){
													if( 'status' in response.data ){
														if( response.data['status'] == "success" ){
															this.load_sdks();
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