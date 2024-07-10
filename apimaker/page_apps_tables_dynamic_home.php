<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="check-circle-fill" viewBox="0 0 16 16">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
  <symbol id="info-fill" viewBox="0 0 16 16">
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
  </symbol>
  <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
</svg>
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
				<a class="btn btn-outline-dark btn-sm me-2" v-bind:href="path+'tables_dynamic/importdump'" >Restore Snapshot</a>
				<a class="btn btn-outline-dark btn-sm me-2" v-bind:href="path+'tables_dynamic/importfile'" >Import CSV/JSON/XLS</a>
				<button class="btn btn-outline-dark btn-sm" v-on:click="function_show_create_form()" >Create Table</button>
			</div>
			<div style="position:absolute;">

				<div v-if="msg" class="alert alert-dark alert-dismissible fade show py-1" role="alert">
				  {{ msg }}
				  <button type="button" class="btn-close" v-on:click="msg=''"></button>
				</div>

				<div v-if="err" class="alert alert-danger d-flex align-items-center py-1" role="alert" style="margin-top:-30px; margin-left:50px;">
					<svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:" style="width:16px;height:16px;"><use xlink:href="#exclamation-triangle-fill"/></svg>
					<div>{{ err }}</div>
					<button type="button" class="btn-close" v-on:click="err=''" ></button>
				</div>

			</div>
			<div class="h3 mb-3">Internal Tables</div>
			<div style="height: calc( 100% - 100px ); overflow: auto;" >
				<table class="table table-striped table-bordered table-sm w-auto" >
					<tr>
						<td>ID</td>
						<td>Name</td>
						<td>Records</td>
						<td></td>
					</tr>
					<tr v-for="v,i in dynamic_tables">
						<td><div class="vid">#<pre class="vid">{{v['_id']}}</pre></div></td>
						<td>
							<div style="min-width:200px;">
								<a v-if="'schema' in v" v-bind:href="path+'tables_dynamic/'+v['_id']+'/records'" >{{ v['table'] }}</a>
								<a v-else v-bind:href="path+'tables_dynamic/'+v['_id']+'/manage'" >{{ v['table'] }}</a>
							</div>
							<div class="text-secondary">{{ v['des'] }}</div>
						</td>
						<td align="right"><span v-if="'count' in v" >{{ v['count'] }}</span></td>
						<td><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_dynamic_table(i)" ></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="modal fade" id="create_table_modal" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Create Internal Table</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        	<div>Name</div>
	        	<input type="text" class="form-control" v-model="new_table['table']" placeholder="Name" v-on:change="nchange" >
	        	<div class="text-secondary small">no spaces. no special chars. except dash(-). lowercase recommended</div>
	        	<div>&nbsp;</div>
	        	<div>Description</div>
	        	<textarea class="form-control" v-model="new_table['des']" ></textarea>
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
			dynamic_tables: [],
			token: "",
			show_create_function: false,
			new_table: {
				"table": "",
				"des": "",
			},
			create_table_modal: false,
		};
	},
	mounted(){
		this.load_dynamic_tables();
	},
	methods: {
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
		load_dynamic_tables(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?", {
				"action":"get_token",
				"event":"get_dynamic_tables."+this.app_id,
				"expire":2
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.load_dynamic_tables2();
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
		load_dynamic_tables2(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?",{"action":"get_dynamic_tables","app_id":this.app_id,"token":this.token}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.dynamic_tables = response.data['data'];
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
		delete_dynamic_table( vi ){
			if( confirm("Are you sure?") ){
				this.msg = "Deleting...";
				this.err = "";
				axios.post("?", {"action":"get_token","event":"tables_dynamic_delete"+this.app_id+this.dynamic_tables[vi]['_id'],"expire":1}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.token = response.data['token'];
									if( this.is_token_ok(this.token) ){
										axios.post("?", {
											"action":"tables_dynamic_delete",
											"token":this.token,
											"dynamic_table_id": this.dynamic_tables[ vi ]['_id']
										}).then(response=>{
											this.msg = "";
											if( response.status == 200 ){
												if( typeof(response.data) == "object" ){
													if( 'status' in response.data ){
														if( response.data['status'] == "success" ){
															this.load_dynamic_tables();
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
		},
		function_show_create_form(){
			this.create_table_modal = new bootstrap.Modal(document.getElementById('create_table_modal'));
			this.create_table_modal.show();
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
		createnow(){
			this.cerr = "";
			this.new_table['table'] = this.cleanit(this.new_table['table']);
			if( this.new_table['table'].trim() == "" ){
				this.cerr = "Name incorrect";
				return false;
			}
			if( this.new_table['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\ \r\n]{5,200}$/i) == null ){
				this.cerr = "Description incorrect. Special chars not allowed";
				return false;
			}
			this.cmsg = "Creating...";
			axios.post("?", {
				"action": "create_table_dynamic", 
				"app_id": "<?=$app['_id'] ?>",
				"new_table": this.new_table
			}).then(response=>{
				this.cmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.cmsg = "Created";
								this.create_table_modal.hide();
								this.new_table = {
									"table": "",
									"des": "",
								};
								this.load_dynamic_tables();
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
	}
}).mount("#app");
</script>