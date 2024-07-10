<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >


			<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
			<div v-if="err" class="alert alert-danger" >{{ err }}</div>
			<button class="btn btn-sm btn-outline-dark float-end" v-on:click="edit_database( 'new' )">Add Database</button>
			<div class="h3 mb-3"><span class="text-secondary" >Databases</span></div>

			<div style="position:relative;overflow: auto; height: calc( 100% - 110px );">

				<table class="table table-sm table-bordered" >
					<thead style="position:sticky;top:0px; background-color:white;border-collapse: separate;" >
					<tr class="bg-white bb-1">
						<td>Description</td>
						<td>Type</td>
						<td>Database Details</td>
						<td>Status</td>
						<td>-</td>
					</tr>
					</thead>
					<tbody>
					<tr v-for="v,i in databases">
						<td><a v-bind:href="dbpath+v['_id']" >{{ v['des'] }}</a></td>
						<td>{{v['engine']}}</td>
						<td>
							<table>
								<template v-for="dv,dp in v['details']" >
								<tr v-if="isenc(dv)==false" >
									<td>{{ dp }}</td><td>{{ dv }}</td>
								</tr>
								</template>
							</table>
						</td>
						<td>
							<span v-if="'test' in v == false" >Never tested</span>
							<span v-else >
								<div>{{ v['test']['status'] }}</div>
								<div>{{ v['test']['date'] }}</div>
							</span>
						</td>
						<td>
							<div v-if="'default' in v==false" >
							<button class="btn btn-sm btn-outline-dark ms-2" v-on:click="edit_database(v['_id'])">E</button>
							<button class="btn btn-sm btn-outline-danger ms-2" v-on:click="delete_database(v['_id'])">D</button>
							</div>
						</td>
					</tr>
					</tbody>
				</table>

			</div>

		</div>
	</div>

	<div class="modal fade" id="edit_db_modal" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">{{ db_id=='new'?"Create Database":"Edit Database" }}</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        	
				<div class="row mb-2">
					<div class="col-3">Description</div>
					<div class="col-9">
						<input type="text" class="form-control form-control-sm" v-model="edit_data['des']" placeholder="Please Enter Database Description" autocomplete="off">
					</div>
				</div>
				<div class="row mb-2">
					<div class="col-3">Database</div>
					<div class="col-9">
						<select class="form-select form-select-sm" v-model="edit_data['engine']" v-on:change="change_type">
							<option v-for="di,i in database_types" v-bind:value="di">{{ di }}</option>
						</select>
					</div>
				</div>
				<div class="row mb-2" v-if="edit_data['engine'] in template">
					<div class="col-3">Details</div>
					<div class="col-9">
						<table>
						<tr v-for="val,prop in template[ edit_data['engine'] ]" >
							<td>{{ val['name'] }}</td>
							<td>
								<input v-if="val['type']=='boolean'" type="checkbox" v-model="edit_data['details'][ prop ]" >
								<select v-else-if="val['type']=='select'" v-model="edit_data['details'][ prop ]" >
									<option v-for="dd,di in val['values']" v-bind:value="dd" >{{ dd }}</option>
								</select>
								<input v-else v-bind:type="val['type']" v-model="edit_data['details'][ prop ]" >
							</td>
						</tr>
						</table>
					</div>
				</div>
				<div align="center">
					<button class="btn btn-sm btn-outline-dark" v-on:click="save_database">{{db_id == 'new' ? 'Save' : 'Update'}}</button>
				</div>

	        	<div v-if="cmsg" class="alert alert-dark" >{{ cmsg }}</div>
	        	<div v-if="cerr" class="alert alert-warning" >{{ cerr }}</div>
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
				"dbpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/databases/",
				"app__": <?=json_encode($app) ?>,
				"show_edit" 	: false,
				"show_mongo_details"    : false,
				"db_id"		: "new",
				"edit_data"		: {},
				"error"		: "",
				"databases"		: [],
				"template": <?=$config_template_json ?>,
				"database_types"		: [ "MySql", "MongoDb", "Redis", "DynamoDb", "MSSql","PostgreSQL","Oracle","Cassandra","AzureSQL","Neo4j","ElasticSearch","CouchDB","SAPHana","IBMDb2","FireBase","FireStore" ],
				"database_name"	        : "",
				"edit_db_modal": false,
				"cmsg": "", "cerr":"",
			};
		},
		mounted:function(){
			this.load_databases();
		},
		methods : {
			isenc: function(v){
				if( typeof(v) =="string" ){
				if( v.match(/^k[0-9]+/) ){
					return true;
				}else{ return false; }
				}else{return false; }
			},
	        delete_database:function(vid){
	        	if(confirm("Are You Sure to Delete Database") ){
					vpost_data = {
						"action"	: "delete_database",
						"db_id"		: vid,
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
			load_databases:function(){
				var vd__ = {
						"action"	: "load_databases",
					   };
				axios.post( "?" , vd__ ).then(response=>{
					if(response.data.hasOwnProperty("status")){
						var vdata = response.data;
						if(vdata['status'] == "success"){
							this.databases = vdata['databases'];
						}else{
							this.error = vdata['error'];
						}
					}else{
						console.log("error");
						console.log(response.data);
					}
				});
			},
			edit_database:function(v){
				this.db_id = v;
				if( v == "new" ){
					this.edit_data = {
						"des"		: "",
						"engine"	: "",
						"details"	: false
					};
				}else{
					this.edit_data = {
						"des": this.databases[ v ]['des']+'',
						"engine": this.databases[ v ]['engine']+'',
						"details": JSON.parse( JSON.stringify( this.databases[ v ]['details'] )),
					};
				}
				this.edit_db_modal = new bootstrap.Modal(document.getElementById('edit_db_modal'));
				this.edit_db_modal.show();
				this.cmsg = ""; this.cerr = "";
			},
			change_type:function(){
				if(this.edit_data['engine'] != ""){
					if( this.edit_data['engine'] in this.template == false ){
						this.edit_data['details']= {}
						this.edit_data['engine'] = "";
					}
					var v = {};
					for(var i in  this.template[ this.edit_data['engine'] ]  ){
						if( typeof(this.template[ this.edit_data['engine'] ][i]['value']) == "object" ){
							v[ i+"" ] =  JSON.parse( JSON.stringify( this.template[ this.edit_data['engine'] ][i]['value'] ));
						}else if( typeof(this.template[ this.edit_data['engine'] ][i]['value']) == "string" ){
							v[ i+"" ] =  this.template[ this.edit_data['engine'] ][i]['value']+'';
						}else{
							v[ i+"" ] =  this.template[ this.edit_data['engine'] ][i]['value'];
						}
					}
					this.edit_data['details'] = v;
				}else{
					this.edit_data['details'] = {};
				}
			},
			save_database:function(){
				this.cerr = "";
				this.edit_data['des'] = this.edit_data['des'].trim();
				if(this.edit_data['des'] == ""){
					this.cerr = "Please Enter Database Description";
				}else if( this.edit_data['des'].match(/^[a-z0-9\.\-\_\ ]{3,50}$/i) == null ){
					this.cerr = "Description should have only a-z 0-9 . - _ spaces!";
				}else if(this.edit_data['engine'] == ""){
					this.cerr = "Please Select Database Type";
				}else{
					for(var prop in this.template[ this.edit_data['engine'] ] ){
						var d =this.template[ this.edit_data['engine'] ][ prop ];
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
					vpost_data = {
						"action" 		: "update_database",
						"des" 			: this.edit_data['des'],
						"engine"		: this.edit_data['engine'],
						"details"		: this.edit_data['details'],
						"db_id"			: this.db_id,
					};
					axios.post("?",vpost_data).then(response=>{
						if( response.data['status'] == "success" ){
							document.location.reload();
						}else{
							this.cerr = response.data['details'];
						}
					});
				}
			},
			test_now: function( v ){
				var vd__ = {
					"action"		: "test_database",
					"db_id"			: v, 
				};
				axios.post( "?" ,vd__ ).then(response=>{
					if( response.data['status'] == "success" ){
						this.databases[ response.data['details']['db_id'] ]['test'] = response.data['details']['test'];
					}else{
						this.cerr = response.data['details'];
					}
				});
			}
		}
}).mount("#app");
</script>
