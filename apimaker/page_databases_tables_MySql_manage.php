<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); overflow: auto; background-color: white; " >
		<div style="padding: 10px;" >

			<div style="float:right;"><a class="btn btn-outline-secondary btn-sm" v-bind:href="dbpath">Back</a></div>

			<h4>Database - <span class="small" style="color:#999;" ><?=ucwords($db['engine']) ?></span>: <?=htmlspecialchars($db['des']) ?> &nbsp;&nbsp;&nbsp;<span class="small" style="color:#999;" >Table:</span> {{ table['des'] }} </h4>

			<ul class="nav nav-tabs mb-2" >
				<li class="nav-item">
					<a class="nav-link<?=$config_param6=='records'||$config_param6==''?" active":"" ?>" v-bind:href="tablepath+'records'">Records</a>
				</li>
				<li class="nav-item">
					<a class="nav-link<?=$config_param6=='manage'?" active":"" ?>" v-bind:href="tablepath+'manage'">Manage</a>
				</li>
				<li class="nav-item">
					<a class="nav-link<?=$config_param6=='structure'?" active":"" ?>" v-bind:href="tablepath+'structure'">Structure</a>
				</li>				
				<li class="nav-item">
					<a class="nav-link<?=$config_param6=='import'?" active":"" ?>" v-bind:href="tablepath+'import'">Import</a>
				</li>
				<li class="nav-item">
					<a disabled class="nav-link<?=$config_param6=='export'?" active":"" ?>" v-bind:href="tablepath+'export'">Export</a>
				</li>
			</ul>

			<div style=" height: calc( 100% - 110px ); border-bottom:1px solid #ccc; overflow: auto; " >

				<div style="padding:5px;">
			
					<div style="border:1px solid #ccc; margin-bottom: 10px; ">

						<div style="padding: 10px; background-color: #f8f8f8; border-bottom: 1px solid #ccc;"><b>Schema</b></div>
						<div style="padding: 10px;">

						<div v-for="sd,si in table['schema']" style="border: 1px solid #ccc; margin-bottom: 10px;" >
							<div style="padding: 5px; background-color: #f0f0f0;" >
								<span v-if="si=='default'" style="height: 30px;" >Default Schema</span>
								<div v-else style="height:30px; " >
									<div class="row">
										<div class="col-6">
											<div v-if="'e' in sd == false" >{{ sd['name'] }}</div>
											<div v-else >
												<input type="text" v-model="sd['name']" placeholder="Schema Name" >
												<input v-if="sd['name']!=sd['e']" type="button" value="Update" v-on:click="edit_schema_name(si)" >
											</div>
										</div>
										<div class="col-6">
											<div v-if="'e' in sd==false" >
												<input type="button" class="btn btn-outline-dark btn-sm pull-right" value="i" v-on:click="show_edit_schema_name(si)" >
												<input type="button" class="btn btn-outline-danger btn-sm pull-right" value="X" v-on:click="delete_schema(si)" >
											</div>
										</div>
									</div>
								</div>
							</div>
							<div style="padding: 5px;" >
								<div v-if="si=='default'" >
									<table class="table table-bordered table-sm w-auto">
									<thead>
									<tr>
										<td>Name</td>
										<td>Type</td>
										<td>Mapped</td>
										<td>Mandatory</td>
									</tr>
									</thead>
									<tbody>
									<tr v-for="vd,vf in sd['fields']" >
										<td>{{ vf }}</td>
										<td>{{ vd['mapped_type'] }}</td>
										<td>{{ vd['type'] }}</td>
										<td><input type="checkbox" v-model="vd['m']" ></td>
									</tr>
									</tbody>
									</table>
								</div>
								<dbobject_table_mysql v-else v-bind:engine="table['engine']" v-bind:level="1" v-bind:items="sd['fields']" v-bind:source_fields="table['all_fields']" v-on:edited="table_fields_edited(si, $event)" ></dbobject_table_mysql>
							</div>
						</div>
						<p><input type="button" class="btn btn-outline-dark btn-sm" value="Add Schema" v-on:click="show_add_schema=true" ></p>
						<p v-if="show_add_schema"><input type="text" v-model="new_schema" placeholder="New Schema"><input type="button" class="btn btn-outline-dark btn-sm" value="Add" v-on:click="add_schema" ></p>
						<p><button type="button" v-on:click="save_now" class="btn btn-outline-dark btn-sm">Save</button></p>

						</div>
					</div>

					<div style="border:1px solid #ccc; ">
						<div style="padding: 10px; background-color: #f8f8f8; border-bottom: 1px solid #ccc;"><b>Indexes:</b></div>
						<div style="padding: 10px;">
						
						<table class="table table-bordered table-sm w-auto">
							<tr class="bg-light">
								<td>IndexName</td>
								<td>Keys</td>
								<td>Options</td>
							</tr>
							<tr v-for="kd,ki in table['source_schema']['keys']" >
								<td>
									{{ ki }}
								</td>
								<td>
									<div v-for="fd,fi in kd['keys']" style="display:flex; column-gap: 10px;" >
										<div style="min-width: 100px;">{{ fd['name'] }}</div>
										<div style="min-width: 100px;"><select class="form-select form-select-sm w-auto" v-model="fd['type']" >
											<option value="text" >Text</option>
											<option value="number" >Number</option>
										</select>
										</div>
									</div>
								</td>
								<td>
									{{ (kd['unique']?"Unique":"") }}
								</td>
							</tr>
						</table>
						</div>
					</div>

					<p>&nbsp;</p>

					<!-- <pre>{{ table }}</pre> -->
				</div>

			</div>
		</div>
	</div>

	<div class="modal fade" id="import_modal" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Select Fields from Source Table Schema</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        	
				<select size="15" multiple v-model="importfields" style="outline:none;">
				<option v-for="vd,vf in table['source_schema']['fields']" v-bind:value="vf" >{{ vf }} - {{ vd['type'] }} - {{ vd['index'] }}</option>
				</select>

				<div><span class='text-danger'>{{ importfields_msg }}</span></div>
				<div><input type="button" value="IMPORT" style="float:right;" v-on:click="import_fields" /></div>

	      </div>
	    </div>
	  </div>
	</div>

</div>
<script>
<?php
require("page_databases_tables_MySql_dbobject.js");
?>
var app = Vue.createApp({
	data: function(){
		return {
			"db_id": "<?=$config_param3 ?>",
			"table_id": "<?=$config_param5 ?>",
			"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			"dbpath":    "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/databases/<?=$config_param3 ?>/",
			"tablepath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/databases/<?=$config_param3 ?>/table/<?=$config_param5 ?>/",
			"db": <?=json_encode($db) ?>,
			"table": <?=json_encode($table,JSON_PRETTY_PRINT) ?>,
			"table_checked": false,
			"schema_matches": false,
			"showschema": false,
			"con_error": "<?=$con_error ?>",
			"check_error": "",
			"check_msg": "",
			"import_modal": false,
			"import_schema_id": "",
			"importfields": [],
			"importfields_msg": "",
			"show_import_popup": false,
			"error": "",
			"show_add_schema": false,
			"new_schema": "",
			"tables_loading": true,
			"source_tables": [],
			"load_tables_msg": "",
			"load_tables_error": "",
		};
	},
	mounted: function(){
		if( "source_schema" in this.table == false ){
			this.table['source_schema']={
				"keys":{},
				"fields":{},
				"last_checked": "Never",
			};
		}
		this.load_source_tables();
	},
	methods : {
		load_source_tables: function(){
			this.load_tables_error = "";
			this.load_tables_msg = "Checking source database...";
			var vd__ =  {
				"action"		: "check_mysql_source_tables_list", 
				"db_id"			: this.db_id, 
				"app_id" :  "<?=$config_param1 ?>",
			};
			axios.post("?", vd__ ).then(response=>{
				this.load_tables_msg = "";
				console.log( response.status );
				if( response.status == 500 ){
					alert( "There was an error checking source database" );
					this.load_tables_error = "http 500";
				}else if( response.data["status"] == "success" ){
					this.source_tables = response.data['data'];
					this.load_tables_msg = "Database is ok";
					this.tables_loading = false;
					if( 'source_schema' in this.table == false ){
						this.check_source_table();
					}
				}else{
					this.check_error = "Database check failed...";
				}
			}).catch(error=>{
				this.load_tables_error = error.toString();
			});
		},
		echo__: function(v){
			if( typeof(v)=="object" ){
				console.log( JSON.stringify(v,null,4) );
			}else{
				console.log( v );
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
						"type": "null", "m": false, "order": cnt,
					};
				}else if( typeof(j[i]) == "boolean" ){
					k[ i+'' ] = {
						"name": i+'', "key": i+'',
						"type": "boolean", "m": false, "order": cnt,
					};
				}else if( typeof(j[i]) == "string" ){
					k[ i+'' ] = {
						"name": i+'', "key": i+'',
						"type": "text", "m": false, "order": cnt,
					};
				}else if( typeof(j[i]) == "number" ){
					k[ i+'' ] = {
						"name": i+'', "key": i+'',
						"type": "number", "m": false, "order": cnt,
					};
				}
				cnt++;
			}
			}
			this.echo__( k );
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
				delete( this.table['schema'][si] );
			}
		},
		show_edit_schema_name: function( si ){
			this.table['schema'][si]['e'] = this.table['schema'][si]['name']+"";
		},
		edit_schema_name: function( si ){
			var k = this.table['schema'][ si ]['name'].replace( /\W+/g, "" );
			var n = this.table['schema'][ si ]['name']+"";
			if( k != si ){
				var t =JSON.parse( JSON.stringify( this.table['schema'][ si ] ));
				t['name'] = n;
				delete(this.table['schema'][si]);
				this.table['schema'][k]= t;
				delete(this.table['schema'][k]['e']);
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
			this.table['schema'][si]['fields'] = vf;
		},
		save_now: function(){
			for( var sch in this.table['schema'] ){
				for(var fi in this.table['schema'][ sch ]['fields'] ){
					if( fi in this.table['all_fields'] == false ){
						alert("Field `" + fi + "` in Schema `" +  this.table['schema'][ sch ]['name'] + "` not found!");return false;
					}
				}
			}
			vd__ =  {
				"action"	: "database_mysql_save_schema", 
				"schema"		: this.table['schema'], 
			};
			axios.post( "?", vd__ ).then(response=>{
				if( response.data['status'] == "success" ){
					alert("Successfully saved");
				}else{
					alert( response.data['error'] );
				}
			});
		},
		compare_schema: function(){
			var ve = true;
			if( 1==51 ){
				for( var ind in this.table['source_schema']['keys'] ){
					var v = this.table['source_schema']['keys'][ ind ];
					if( ind in this.table['keys']==false ){
						ve = false;
						break;
					}else if( this.table['keys'][ ind ]['keys'].length != v['keys']['length'] ){
						ve = false;
						break;
					}else{
						for(var k=0;k<v['keys'].length;k++){
							if( this.table['keys'][ind]['key'][k]['name'] != v['keys'][k]['name'] ){
								ve = false;
								break;
							}
						}
					}
				}
			}
			this.schema_matches = ve;
		},
		check_source_table: function(){
			if( 'source_schema' in this.table == false && this.table['table'] != "" && this.table['table'] != "new" ){
				this.check_source_database();
			}else if( this.table['source_schema']['last_checked'] == "Never" && this.table['table'] != "" && this.table['table'] != "new" ){
				this.check_source_database();
			}
		},
		check_source_database: function(){
			this.check_error = "";
			this.check_msg = "Checking source table...";
			var vd__ =  {
				"action"		: "check_mysql_source_table", 
				"db_id"			: this.db_id, 
				"table_id"		: this.table_id, 
				"table"			: this.table['table'],
			};
			axios.post("?", vd__ ).then(response=>{
				this.check_msg = "";
				console.log( response.status );
				if( response.status == 500 ){
					alert( "There was an error checking source database" );
					this.check_error = "http 500";
				}else if( response.data["status"] == "success" ){
					this.table['source_schema'] = response.data['data'];
					if( this.table_id == "new" ){
						var s = JSON.parse( JSON.stringify( this.table['source_schema']['fields'] ) );
						for( var f in s ){
							s[ f ]['key'] = f+'';
							s[ f ]['m'] = true;
							s[ f ]['order'] = 0;
							//s[ f ]['index'] = ;
						}
						this.table['schema'][ 'default' ][ 'fields' ] = s;
					}
				}else{
					this.check_error = response.data['error'] ;
				}
			}).catch(error=>{
				this.check_error = error.toString();
			});
		},
	}
});
app.component( "dbobject_table_mysql", dbobject_table_mysql );
app.mount("#app");
</script>
