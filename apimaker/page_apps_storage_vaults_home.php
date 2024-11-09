<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; "  >
		<div style="padding: 10px;" >

			<button class="btn btn-sm btn-outline-dark float-end" v-on:click="edit_vault( 'new' )">Add Vault</button>
			<div class="h3 mb-3"><span class="text-secondary" >Storage Vaults</span></div>

			<div style="position:relative;overflow: auto; height: calc( 100% - 110px );">

				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

				<table class="table table-sm table-bordered w-auto" >
					<thead style="position:sticky;top:0px; background-color:white;border-collapse: separate;" >
					<tr class="bg-white bb-1">
						<td>Description</td>
						<td>Type</td>
						<td>Status</td>
						<td>-</td>
					</tr>
					</thead>
					<tbody>
					<tr v-for="v,i in vaults">
						<td><a v-bind:href="vaultpath+v['_id']" >{{ v['des'] }}</a></td>
						<td>{{v['vault_type']}}</td>
						<td>
							<span v-if="'test' in v == false" >Never tested</span>
							<span v-else >
								<div>{{ v['test']['status'] }}</div>
								<div>{{ v['test']['date'] }}</div>
							</span>
						</td>
						<td>
							<div v-if="'default' in v==false" >
							<button class="btn btn-sm btn-outline-dark ms-2" v-on:click="edit_vault(v['_id'])">Settings</button>
							<button class="btn btn-sm btn-outline-danger ms-2" v-on:click="delete_vault(v['_id'])">Delete</button>
							</div>
						</td>
					</tr>
					</tbody>
				</table>

			</div>

		</div>
	</div>

	<div class="modal fade" id="edit_vault_modal" tabindex="-1" >
	  <div class="modal-dialog modal-lg modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">{{ vault_id=='new'?"Create Vault":"Edit Vault" }}</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
			<table class="table table-bordered table-sm w-auto">
				<tr>
					<td>Description</td>
					<td>
						<input type="text" class="form-control form-control-sm" v-model="edit_data['des']" placeholder="Please Enter Vault Description" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<select class="form-select form-select-sm" v-model="edit_data['vault_type']" v-on:change="change_type">
							<option v-for="di,i in vault_types" v-bind:value="di">{{ di }}</option>
						</select>
					</td>
				</tr>
				<tr v-if="edit_data['vault_type'] in template">
					<td>Details</td>
					<td>
						<table class="table table-bordered table-sm">
							<template v-for="val,prop in template[ edit_data['vault_type'] ]" >
								<tr v-if="show_field(prop)" >
									<td>{{ val['name'] }}</td>
									<td>
										<div>
											<input v-if="val['type']=='boolean'" type="checkbox" v-model="edit_data['details'][ prop ]" >
											<select v-else-if="val['type']=='select'" v-model="edit_data['details'][ prop ]" >
												<option v-for="dd,di in val['values']" v-bind:value="dd" >{{ dd }}</option>
											</select>
											<input v-else v-bind:type="val['type']" v-model="edit_data['details'][ prop ]" >
										</div>
										<div v-if="'h' in val" class="text-secondary" v-html="val['h']" ></div>
									</td>
								</tr>
							</template>
						</table>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<button class="btn btn-sm btn-outline-dark" v-on:click="save_vault">{{vault_id == 'new' ? 'Save' : 'Update'}}</button>
					</td>
				</tr>
				</table>
	        	<div v-if="cmsg" class="alert alert-success" >{{ cmsg }}</div>
	        	<div v-if="cerr" class="alert alert-danger" >{{ cerr }}</div>

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
				"vaultpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/storage/",
				"app__": <?=json_encode($app) ?>,
				"show_edit" 	: false,
				"show_mongo_details"    : false,
				"vault_id"		: "new",
				"edit_data"		: {},
				"error"		: "",
				"vaults"		: [],
				"template": <?=$config_template_json ?>,
				"vault_types"		: ["AWS-S3","Azure-Blob","Google-Cloud-Storage","Google-Drive","Microsoft-OneDrive"],
				"vault_name"	        : "",
				"edit_vault_modal": false,
				"cmsg": "", "cerr":"",
			};
		},
		mounted:function(){
			this.load_vaults();
		},
		methods: {
			show_field: function( vprop){
				var template = this.template[ this.edit_data['vault_type'] ];
				if( vprop in template ){
					if( 'vif' in template[ vprop ] ){
						var vif = template[ vprop ][ 'vif' ];
						var conds = vif.split(/\&/g);
						var f = true;
						for(var i=0;i<conds.length;i++){
							var cx = conds[i].split(/\=/);
							if( cx[0] in template ){
								var v = this.edit_data['details'][ cx[0] ];
								var t = template[ cx[0] ]['type'];
								if( t == "boolean" ){
									if( v !== true && v != "true" ){
										f = false;
									}
								}else if( v != cx[1] ){
									f = false;
								}
							}else{
								f = false;
							}
						}
						return f;
					}
				}
				return true;
			},
			isenc: function(v){
				if( typeof(v) =="string" ){
				if( v.match(/^k[0-9]+/) ){
					return true;
				}else{ return false; }
				}else{return false; }
			},
	        delete_vault:function(vid){
	        	if(confirm("Are You Sure to Delete Vault") ){
					vpost_data = {
						"action"	: "delete_vault",
						"vault_id"		: vid,
					};
					axios.post("?",vpost_data).then(response=>{
						if(response.data['status'] == "success"){
							document.location.reload();
						}else{
							alert( response.data['details'] );
						}
					});
				}
			},
			load_vaults:function(){
				var vd__ = {
					"action"	: "load_vaults",
				};
				axios.post( "?" , vd__ ).then(response=>{
					if(response.data.hasOwnProperty("status")){
						var vdata = response.data;
						if(vdata['status'] == "success"){
							this.vaults = vdata['vaults'];
						}else{
							this.error = vdata['error'];
						}
					}else{
						console.log("error");
						console.log(response.data);
					}
				});
			},
			edit_vault:function(v){
				this.vault_id = v;
				if( v == "new" ){
					this.edit_data = {
						"des"		: "",
						"vault_type"	: "",
						"details"	: false
					};
				}else{
					this.edit_data = {
						"des": this.vaults[ v ]['des']+'',
						"vault_type": this.vaults[ v ]['vault_type']+'',
						"details": JSON.parse( JSON.stringify( this.vaults[ v ]['details'] )),
					};
				}
				this.edit_vault_modal = new bootstrap.Modal(document.getElementById('edit_vault_modal'));
				this.edit_vault_modal.show();
				this.cmsg = ""; this.cerr = "";
			},
			change_type:function(){
				if(this.edit_data['vault_type'] != ""){
					if( this.edit_data['vault_type'] in this.template == false ){
						this.edit_data['details']= {}
						this.edit_data['vault_type'] = "";
					}
					var v = {};
					for(var i in  this.template[ this.edit_data['vault_type'] ]  ){
						if( typeof(this.template[ this.edit_data['vault_type'] ][i]['value']) == "object" ){
							v[ i+"" ] =  JSON.parse( JSON.stringify( this.template[ this.edit_data['vault_type'] ][i]['value'] ));
						}else if( typeof(this.template[ this.edit_data['vault_type'] ][i]['value']) == "string" ){
							v[ i+"" ] =  this.template[ this.edit_data['vault_type'] ][i]['value']+'';
						}else{
							v[ i+"" ] =  this.template[ this.edit_data['vault_type'] ][i]['value'];
						}
					}
					this.edit_data['details'] = v;
				}else{
					this.edit_data['details'] = {};
				}
			},
			save_vault:function(){
				this.cerr = "";
				this.edit_data['des'] = this.edit_data['des'].trim();
				if(this.edit_data['des'] == ""){
					this.cerr = "Please Enter Vault Description";
				}else if( this.edit_data['des'].match(/^[a-z0-9\.\-\_\ ]{3,50}$/i) == null ){
					this.cerr = "Description should have only a-z 0-9 . - _ spaces!";
				}else if(this.edit_data['vault_type'] == ""){
					this.cerr = "Please Select Vault Type";
				}else{
					for(var prop in this.template[ this.edit_data['vault_type'] ] ){
						var d =this.template[ this.edit_data['vault_type'] ][ prop ];
						if( d['m'] ){
							if( d['type'] == 'text' ){
								if( this.edit_data['details'][ prop ] == "" ){
									this.cerr = "Need `" + d['name'] + "` info";
									break;
								}
							}
							if( d['type'] == 'number' ){
								if( Number(this.edit_data['details'][ prop ]) == NaN || this.edit_data['details'][ prop ] == "" ){
									this.cerr = "Need `" + d['name'] + "` info";
									break;
								}
							}
						}
					}
				}
				if( this.cerr == "" ){
					this.cmsg = "Saving...";
					vpost_data = {
						"action" 		: "storage_vault_update",
						"des" 			: this.edit_data['des'],
						"vault_type"		: this.edit_data['vault_type'],
						"details"		: this.edit_data['details'],
						"vault_id"			: this.vault_id,
					};
					axios.post("?",vpost_data).then(response=>{
						if( response.status == 200 ){
							if( typeof(response.data) == "object" ){
								if( 'status' in response.data ){
									if( response.data['status'] == "success" ){
										this.cmsg = "Updated successfully";
										setTimeout("document.location.reload()",2000);
									}else{
										this.cerr = response.data['error'];
									}
								}else{
									this.cerr = "Invalid response";
								}
							}else{
								this.cerr = "Incorrect response";
							}
						}else{
							this.cerr = "http:"+response.status;
						}
					}).catch(error=>{
						this.cerr = error.message;
					});
				}
			},
			test_now: function( v ){
				var vd__ = {
					"action"		: "test_vault",
					"vault_id"			: v, 
				};
				axios.post( "?" ,vd__ ).then(response=>{
					if( response.data['status'] == "success" ){
						this.vaults[ response.data['details']['vault_id'] ]['test'] = response.data['details']['test'];
					}else{
						this.cerr = response.data['details'];
					}
				});
			}
		}
}).mount("#app");
</script>
