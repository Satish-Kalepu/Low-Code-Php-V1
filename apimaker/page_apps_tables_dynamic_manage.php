<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div style="float:right;"><a class="btn btn-outline-secondary btn-sm" v-bind:href="dbpath">Back</a></div>

			<h4>Table - <?=ucwords($table['table']) ?></h4>

			<ul v-if="is_first==false" class="nav nav-tabs mb-2" >
				<li class="nav-item">
					<a class="nav-link<?=$config_param4=='records'||$config_param4==''?" active":"" ?>" v-bind:href="dbpath+'records'">Records</a>
				</li>
				<li class="nav-item">
					<a class="nav-link<?=$config_param4=='manage'?" active":"" ?>" v-bind:href="dbpath+'manage'">Manage</a>
				</li>
				<li class="nav-item">
					<a class="nav-link<?=$config_param4=='import'?" active":"" ?>" v-bind:href="dbpath+'import'">Import</a>
				</li>
				<li class="nav-item">
					<a disabled class="nav-link<?=$config_param4=='export'?" active":"" ?>" v-bind:href="dbpath+'export'">Export</a>
				</li>
			</ul>

			<div style="overflow: auto;height: calc( 100% - 130px );">

				<div style="border:1px solid #ccc; padding:10px; " >

				<table class="table table-sm w-auto" >
					<tr>
						<td align="right" width="150">Name</td>
						<td><input class="form-control form-control-sm" type="text" v-model="table['table']" placeholder="Name"></td>
					</tr>
					<tr>
						<td align="right" width="150">Description</td>
						<td><input class="form-control form-control-sm" type="text" v-model="table['des']" placeholder="Description/purpose"></td>
					</tr>
					<tr>
						<td align="right">Schema</td>
						<td>
							<div v-for="sd,si in table['schema']" style="border: 1px solid #999; margin-bottom: 10px;" >
								<div style="padding: 10px; background-color: #f0f0f0;" >
									<span v-if="si=='default'" >Default Schema</span>
									<div v-else >
										<div v-if="'e' in sd==false" > {{ sd['name'] }}  <input type="button" class="btn btn-outline-dark btn-sm pull-right" value="i" v-on:click="show_edit_schema_name(si)" ><input type="button" class="btn btn-outline-danger btn-sm pull-right" value="X" v-on:click="delete_schema(si)" ></div>
										<div v-else ><input type="text" v-model="sd['name']" placeholder="Schema Name" ><input v-if="sd['name']!=sd['e']" class="btn btn-outline-dark btn-sm" type="button" value="Update" v-on:click="edit_schema_name(si)" ></div>
									</div>
								</div>
								<div style="padding: 10px;" >
									<div align="right"><input type="button" value="Import" class="btn btn-outline-dark btn-sm" style="padding:0px 5px;" v-on:click="show_import(si)" ></div>
									<table_dyanmic_object v-if="vshow" v-bind:level="1" v-bind:items="sd['fields']" v-on:edited="table_fields_edited(si,$event)" ></table_dyanmic_object>
								</div>
							</div>
							<div><input type="button" class="btn btn-outline-dark btn-sm" value="Add Schema" v-on:click="show_add_schema=true" ></div>
							<div v-if="show_add_schema">
								<input type="text" class="form-control form-control-sm w-auto" v-model="new_schema" placeholder="New Schema">
								<input type="button" class="btn btn-outline-dark btn-sm" value="Add" v-on:click="add_schema" >
							</div>
						</td>
					</tr>
					
					<tr>
						<td align="right">&nbsp;</td>
						<td>
							<p><button type="button" v-on:click="save_now" class="btn btn-outline-dark btn-sm">Save</button></p>
						</td>
					</tr>
				</table>

				</div>

				<template v-if="is_first==false" >
					<div style="font-weight:500;padding:10px;">Index Settings</div>
					<div style="border:1px solid #ccc; padding:10px; " >
						<template v-if="'keys_list' in table" >
						<div v-if="table['keys_list'].length>0" style="margin-bottom:20px;" >
							<table class="table table-bordered table-sm w-auto">
								<tr class="bg-light">
									<td>IndexName</td>
									<td>Keys</td>
									<td>Unique</td>
									<td></td>
								</tr>
								<tr v-for="kd,ki in table['keys_list']" >
									<td>{{ kd['name'] }}</td>
									<td>
										<div v-for="fd,fi in kd['keys']" >
											{{ fd['name'] }} {{ fd['type'] }} {{ fd['sort'] }}
										</div>
									</td>
									<td>
										<div v-if="kd['unique']" >Yes</div>
									</td>
									<td>
										<input type="button" class="btn btn-outline-danger btn-sm" value="X" style="padding:0px 2px;" v-on:click="delete_index(ki)" >
									</td>
								</tr>
							</table>
						</div>
						</template>
						<div v-else style="margin-bottom:20px;" >There are no indexes available</div>
						<div style="padding:10px;border:1px solid #ccc;">
							<table class="table table-bordered table-sm w-auto">
								<tr class="bg-light">
									<td>IndexName</td>
									<td>Keys</td>
									<td>Unique</td>
								</tr>
								<tr>
									<td>
										<input type="text" v-model="new_index['name']" title="Index Name" v-on:change="index_update" style="width:150px;" >
									</td>
									<td>
										<div v-for="fd,fi in new_index['keys']" >
											<input type="text" v-model="fd['name']" placeholder="Field name" v-on:change="index_update"  style="width:150px;" >
											<select v-model="fd['type']" v-on:change="index_update" >
												<option value='text'>Text</option>
												<option value='number'>Number</option>
												<option value='boolean'>Boolean</option>
											</select>
											<select v-model="fd['sort']" v-on:change="index_update" >
												<option value="asc" >ASC</option>
												<option value="dsc" >DSC</option>
											</select>
											<input v-if="fi>0" type="button" class="btn btn-outline-danger btn-sm" value="X" style="padding:0px 2px;" v-on:click="index_delete_key(fi)" >
										</div>
										<div><input type="button" class="btn btn-outline-dark btn-sm" value="+" style="padding:0px 2px;" v-on:click="index_add_key()" ></div>
									</td>
									<td>
										<input type="checkbox" v-model="new_index['unique']" style="width:15px; height: 15px;" title="Unique Index">
									</td>
								</tr>
							</table>
							<p><input type="button" class="btn btn-outline-dark btn-sm" value="Add Index" v-on:click="add_index" v-if="not_busy" ></p>
							<div v-if="indmsg" class="alert alert-secondary">{{ indmsg }}</div>
						</div>
							
					</div>
				</template>

				<template v-if="is_first==false" >
					<div style="font-weight:500;padding:10px;">Table Settings</div>
					<div style="border:1px solid #ccc; padding:10px; " >

						<div class="mb-2"><div class="btn btn-outline-dark btn-sm" v-on:click="emptyit">Empty Table</div></div>
						<div><div class="btn btn-outline-danger btn-sm" v-on:click="deleteit">Delete Table</div></div>

					</div>
				</template>

			</div>
		</div>
	</div>


	<div class="modal fade" id="import_popup" tabindex="-1" >
	  <div class="modal-dialog modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Import Schema</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      		<div>Paste JSON String or comma separated column names of a CSV</div>
				<textarea spellcheck="false" class="form-control form-control-sm" style="min-height:200px;resize:both;" v-model="importjson"></textarea>
				<div><span class='text-danger'>{{ importjson_msg }}</span><input type="button" value="IMPORT" style="float:right;" v-on:click="import_schema_json" ></div>
	      </div>
	    </div>
	  </div>
	</div>


</div>

<script>
<?php
include( "page_apps_tables_dynamic_object.js" );
?>
var app = Vue.createApp({
	"data"	: function(){
		return {
			"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			"dbpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tables_dynamic/<?=$config_param3 ?>/",
			"app_id": "<?=$config_param1 ?>",
			"table_id": "<?=$config_param3 ?>",
			"table": <?=json_encode($table,JSON_PRETTY_PRINT) ?>,
			"table_checked": false,
			"schema_matches": false,
			"showschema": false,
			"check_error": "",
			"check_msg": "",
			"keys_list": [],
			"import_schema_id": "",
			"importjson": "",
			"importjson_msg": "",
			"import_popup": false,
			"error": "",
			"vshow": true,
			"show_add_schema": false,
			"new_schema": "",
			"new_index": {
				"name": "",
				"keys": [{
					"name":"",
					"type":"text",
					"sort":"asc"
				}],
				"unique":false,
			},
			"tables_loading": true,
			"source_tables": [],
			"source_tables_exists": true,
			"new_collection_name": "",
			"source_fields": [],
			"load_tables_msg": "",
			"load_tables_error": "",
			"is_first": false,
			"not_busy": true,
			"indmsg": "",
		};
	},
	mounted : function(){
		if( "schema" in this.table == false ){
			this.is_first = true;
			this.table['schema'] = {
				"default": {
					"name": "default",
					"fields": {
						"_id": {
							"key": "_id", "name": "_id",
							"type": "text",
							"m": true,
							"sub": {},
							"order":1,
							"index": "primary",
						},
						"f1": {
							"key": "f1", "name": "f1",
							"type": "text",
							"m": true,
							"sub": {},
							"order":2,
							"index": "",
						},
						"f2": {
							"key": "f2", "name": "f2",
							"type": "text",
							"m": true,
							"sub": {},
							"order":3,
							"index": "",
						}
					}
				}
			};
		}
		if( "keys_list" in this.table == false ){
			this.table['keys_list'] = [];
		}else if( typeof(this.table["keys_list"]) != "object" || this.table["keys_list"] == null || "length" in this.table["keys_list"] == false ){
			this.table['keys_list'] = [];
		}
		if( "keys" in this.table == false ){
			this.table['keys'] = {};
		}else if( typeof(this.table["keys_list"]) != "object" || this.table["keys_list"] == null || "length" in this.table["keys_list"] ){
			this.table['keys'] = {};
		}
		//this.load_source_tables();
	},
	methods: {
		echo__: function(v){
			if( typeof(v)=="object" ){
				console.log( JSON.stringify(v,null,4) );
			}else{
				console.log( v );
			}
		},
		show_import: function( vsi ){
			this.import_schema_id = vsi+'';
			this.import_popup = new bootstrap.Modal(document.getElementById('import_popup'));
			this.import_popup.show();
		},
		hide_import_popup: function(){
			this.import_popup.hide();
		},
		import_schema_json: function(){
			this.importjson_msg = "";
			var v = this.importjson.trim() +'';
			if( v.match(/^\{/) || v.match(/^\}/) ){
				v = v.replace(/\,\}/g, "}");
				v = v.replace(/\,\]/g, "}");
				try{
					var j = JSON.parse( v );
					var fv = this.make_fields_schema( j );
					fv[ "_id" ] = {"name":"_id", "key":"_id","type":"text", "order":0,"m":true};
					this.vshow = false;
					this.table['schema'][ this.import_schema_id ][ 'fields' ] = fv;
					setTimeout(function(v){v.vshow=true;},300,this);
					this.hide_import_popup();
				}catch( e ){
					this.importjson_msg = "Error in Import: "+ e;
				}
			}else if( v.match(/^[a-z0-9\,\-\_\.\ ]+$/i) ){
				var cols = v.split(/\,/g);
				if( cols.length < 2 ){
					this.importjson_msg = "Does not match to JSON or CSV format ";
				}else{
					var sch = {};
					for(var i in cols){
						var k = cols[i].replace(/\W/g, "");
						sch[ k ] = {
							"name": k, "key": k,
							"type": "text", "m": true, "order": i+1,
							"sub": {},
						}
					}
					sch[ "_id" ] = {"name":"_id", "key":"_id","type":"text", "order":0,"m":true};
					this.vshow = false;
					this.table['schema'][ this.import_schema_id ][ 'fields' ] = sch;
					setTimeout(function(v){v.vshow=true;},300,this);
					this.hide_import_popup();
				}
			}
		},
		make_fields_schema: function( j ){
			var k = {};
			var cnt = 1;
			if( typeof(j) == "object" && "length" in j == false ){
			for(var i in j ){
				if( j[i] == null ){
					k[ i+'' ] = {
						"name": i+'', "key": i+'',
						"type": "null", "m": true, "order": cnt,
						"sub": {},
					};
				}else if( typeof(j[i]) == "boolean" ){
					k[ i+'' ] = {
						"name": i+'', "key": i+'',
						"type": "boolean", "m": true, "order": cnt,
						"sub": {},
					};
				}else if( typeof(j[i]) == "string" ){
					k[ i+'' ] = {
						"name": i+'', "key": i+'',
						"type": "text", "m": true, "order": cnt,
						"sub": {},
					};
				}else if( typeof(j[i]) == "number" ){
					k[ i+'' ] = {
						"name": i+'', "key": i+'',
						"type": "number", "m": true, "order": cnt,
						"sub": {},
					};
				}else if( typeof(j[i]) == "object" && "length" in j[i] == false ){
					k[ i+'' ] = {
						"name": i+'', "key": i+'',
						"type": "dict", "m": true, "order": cnt,
						"sub": this.make_fields_schema( j[i] ),
					};
				}else if( typeof(j[i]) == "object" && "length" in j[i] ){
					k[ i+'' ] = {
						"name": i+'', "key": i+'',
						"type": "list", "m": true, "order": cnt,
						"sub": [ this.make_fields_schema( j[i][0] ) ],
					};
				}
				cnt++;
			}
			}
			this.echo__(k );
			return k;
		},
		add_schema: function(){
			if( this.new_schema ){
				var k = this.new_schema.replace( /\W+/g, "" );
				if( k in this.table['schema'] ){
					alert("Schema name already exists!");
				}else{
					var t =JSON.parse( JSON.stringify( this.table['schema']['default'] ));
					t['name'] = this.new_schema+"";
					this.table['schema'][k] = t;
					this.new_schema = "";
					this.show_add_schema=false;
				}
			}
		},
		delete_schema: function(si){
			if( confirm("Are you sure to delete `" + si + "` schema" ) ){
				this.$delete( this.table['schema'], si );
			}
		},
		show_edit_schema_name: function( si ){
			this.table['schema'][si][ 'e' ] = this.table['schema'][si]['name']+"";
		},
		edit_schema_name: function( si ){
			var k = this.table['schema'][ si ]['name'].replace( /\W+/g, "" );
			var n = this.table['schema'][ si ]['name']+"";
			if( k != si ){
				var t =JSON.parse( JSON.stringify( this.table['schema'][ si ] ));
				t['name'] = n;
				this.$delete(this.table['schema'], si);
				this.table['schema'][ k ] = t;
				this.$delete(this.table['schema'][k], 'e');
			}
		},
		table_fields_edited: function( si, vf ){
			var v = [];
			for( var i in vf ){
				v.push( Number(vf[i]['order']) );
			}
			v.sort();
			var v_fn = [];
			var k = [];
			for( var i=0;i<v.length;i++){
				for( var j in vf ){if( vf[ j ]['order'] == v[i] ){
					v_fn.push( vf[ j ]['name']+'' );
				}}
			}
			this.echo__( vf );
			this.table['schema'][si][ 'fields' ] =vf;
		},
		index_add_key: function(){
			this.new_index['keys'].push({
				"name":"",
				"type":"text",
				"sort":"asc"
			});
		},
		index_delete_key: function(ki ){
			this.new_index['keys'].splice(ki,1);
		},
		add_index: function(){
			this.new_index['name'] = this.new_index['name'].toLowerCase().trim();
			if( this.new_index['name'].match(/^[a-z][a-z0-9\_\-\.]{1,25}$/i) == null ){
				alert("Need proper index name");return false;
			}
			for(var i=0;i<this.new_index['keys'].length;i++){
				if( this.new_index['keys'][i]['name'] != "_id" && this.new_index['keys'][i]['name'].match(/^[a-z][a-z0-9\_\-\.]{1,100}$/i) == null ){
					alert("Need proper field name for index:"+(i+1));return false;
				}
			}
			this.indmsg = "Creating index...";
			this.not_busy = false;
			vd__ =  {
				"action"	: "table_dynamic_create_index", 
				"app_id"	: this.app_id, 
				"table_id"	: "<?=$config_param3 ?>",
				"new_index": this.new_index,
			};
			axios.post( "?", vd__ ).then(response=>{
				if( response.data['status'] == "success" ){
					this.indmsg = "Index is being created in the background!";
					alert("Index is being created in the background!");
					setTimeout("document.location.reload()", 3000);
				}else{
					this.not_busy = true;
					this.indmsg = "Error: " + response.data['error'];
					alert( "There was an error\n\n"+ response.data['error'] );
				}
			});
		},
		delete_index: function(vi){
			if( confirm("Are you sure to drop index"  ) ){
				vd__ =  {
					"action"	: "table_dynamic_drop_index", 
					"app_id"	: this.app_id, 
					"table_id"	: "<?=$config_param3 ?>",
					"index": this.table['keys_list'][vi]['name'],
				};
				axios.post( "?", vd__ ).then(response=>{
					if( response.data['status'] == "success" ){
						alert("Index is successfully dropped!");
						document.location.reload();
					}else{
						alert( "There was an error\n\n"+ response.data['error'] );
					}
				});
			}
			this.keys_list.splice(vi,1);
		},
		save_now: function(){
			this.table['table'] = (this.table['table']+"").trim();
			this.table['des']   = (this.table['des']+"").trim();
			if( this.table['des']== "" ){
				alert("Enter Table Description");return false;
			}else if( this.table['des'].match( /^[A-Za-z0-9\-\_\s\.\ ]{5,50}$/ ) == null ){
				alert("Table description From 5 to 50 characters in length, A-Z a-z 0-9 _ - . and spaces allowed.");return false;
			}else if( this.table['table']== "" ){
				alert("Enter Table Name");return false;
			}else if( this.table['table'].match( /^[a-z0-9\-\_\.]{2,25}$/ ) == null ){
				alert("Table name From 5 to 25 characters in length, lowercase a-z 0-9 _ - . allowed. space is not allowed");return false;
			}else{
				for(var ind in this.table['keys'] ){
					if( ind.match(/^[a-z][a-z0-9]{2,60}$/) == null ){
						alert("Index name " + ind + " incorrect\nonly alphanumeric expected");return false;
					}else{
						for(var i=0;i<this.table['keys'][ ind ]['keys'];i++){
							if(   this.table['keys'][ ind ]['keys'][i]["name"].match(/^[a-z0-9\.\-\_]{2,60}$/) == null ){
								alert("Index field name `" + this.table['keys'][ind]['keys'][i]["name"] + "` incorrect");return false;
							}
						}
					}
				}
				vd__ =  {
					"action"	: "save_table_dynamic", 
					"table"		: this.table, 
					"app_id"	: this.app_id, 
					"table_id"	: "<?=$config_param3 ?>",
				};
				axios.post( "?", vd__ ).then(response=>{
					if( response.data['status'] == "success" ){
						alert("Successfully saved");
						document.location.reload();
					}else{
						alert( response.data['error'] );
					}
				});
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
		emptyit: function(){
			if( confirm("Are you sure?\nDo you want delete all the records of the table?\nYou will not be able to recover") ){
				axios.post("?", {"action":"get_token","event":"tables_dynamic_empty"+this.app_id+this.table['_id'],"expire":1}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.token = response.data['token'];
									if( this.is_token_ok(this.token) ){
										axios.post( "?", {
											"action": "tables_dynamic_delete", 
											"table_id": this.table['_id'],
											"token": this.token,
										}).then(response=>{
											if( response.status == 200 ){
												if( typeof(response.data) == "object" ){
													if( 'status' in response.data ){
														if( response.data['status'] == "success" ){
															alert("Table is deleted successfully!");
															document.location = this.path + "tables_dynamic?event=TableDeleted";
														}else{
															alert("There was an error: " + response.data['error'] );
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
					}
				});
			}
		},
		deleteit: function(){
			if( confirm("Are you sure?\nDo you want delete the table?\nYou will not be able to recover") ){
				axios.post("?", {"action":"get_token","event":"tables_dynamic_delete"+this.app_id+this.table['_id'],"expire":1}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.token = response.data['token'];
									if( this.is_token_ok(this.token) ){
										axios.post( "?", {
											"action": "tables_dynamic_delete", 
											"dynamic_table_id": this.table['_id'],
											"token": this.token,
										}).then(response=>{
											if( response.status == 200 ){
												if( typeof(response.data) == "object" ){
													if( 'status' in response.data ){
														if( response.data['status'] == "success" ){
															alert("Table is deleted successfully!");
															document.location = this.path + "tables_dynamic?event=TableDeleted";
														}else{
															alert("There was an error: " + response.data['error'] );
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
					}
				});
			}
		},

	}
});
app.component( "table_dyanmic_object", table_dyanmic_object );
app.mount("#app");

</script>
