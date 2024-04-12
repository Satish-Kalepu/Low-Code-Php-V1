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

			<div style=" height: calc( 100% - 130px ); border-bottom:1px solid #ccc; overflow: auto; " >

			<table class="table table-sm" >
				<tr>
					<td align="right">Schema</td>
					<td>
						<table class="table table-bordered table-sm table-striped w-auto">
							<tr>
								<td>Name</td>
								<td>Type</td>
								<td>Attributes</td>
								<td>-</td>
								<td>-</td>
							</tr>
							<tr v-for="sd,si in table['all_fields']">
								<td>{{ si }}</td>
								<td>{{ sd['type'] }}</td>
								<td>{{ sd['mapped_type'] }}</td>
								<td><input type="button" class="btn btn-outline-dark btn-sm py-0" v-on:click="show_edit_form(si)" value="E"></td>
								<td><input type="button" class="btn btn-outline-danger btn-sm py-0" v-on:click="delete_field(si)" value="X"></td>
							</tr>
						</table>
						<p><input type="button" class="btn btn-outline-dark btn-sm" value="Add Field" v-on:click="show_add_form" ></p>


					</td>
				</tr>
				<tr>
					<td align="right">Indexes</td>
					<td>
						<table class="table table-bordered table-sm w-auto">
							<tr class="bg-light">
								<td>IndexName</td>
								<td>Keys</td>
								<td>Options</td>
							</tr>
							<tr v-for="kd,ki in table['keys']" >
								<td>
									{{ ki }}
								</td>
								<td>
									<div v-for="fd,fi in kd['keys']" >
										{{ fd['name'] }} - {{ fd['type'] }}
									</div>
								</td>
								<td>
									{{ (kd['unique']?"Unique":"") }}
								</td>
								<td>
									<input v-if="ki!='PRIMARY'" type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="drop_index(ki)">
								</td>
							</tr>
						</table>
						<p><input type="button" class="btn btn-outline-dark btn-sm" value="Add Index" v-on:click="show_index_form" ></p>
					</td>
				</tr>
			</table>

			<div>Previleges: </div>
			<pre>{{ roles }}</pre>

			</div>
		</div>
	</div>


	<div class="modal fade" id="add_popup" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Add new field</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        	
				<div>Add Field: </div>
				<table class="table table-bordered table-sm table-striped w-auto">
				<tr>
					<td>Name</td>
					<td><input type="text" class="form-control form-control-sm" v-model="new_field['name']"></td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<select v-model="new_field['type']" class="form-select form-select-sm" >
							<option value="INT" >INT</option>
							<option value="VARCHAR" >VARCHAR</option>
							<option value="TEXT" >TEXT</option>
							<option value="TINYINT" >TINYINT</option>
							<option value="SMALLINT" >SMALLINT</option>
							<option value="MEDIUMINT" >MEDIUMINT</option>
							<option value="INT" >INT</option>
							<option value="BIGINT" >BIGINT</option>
							<option value="DECIMAL" >DECIMAL</option>
							<option value="FLOAT" >FLOAT</option>
							<option value="DOUBLE" >DOUBLE</option>
							<option value="BOOLEAN" >BOOLEAN</option>
							<option value="DATE" >DATE</option>
							<option value="DATETIME" >DATETIME</option>
							<option value="TIMESTAMP" >TIMESTAMP</option>
							<option value="TIME" >TIME</option>
							<option value="CHAR" >CHAR</option>
							<option value="TINYTEXT" >TINYTEXT</option>
							<option value="TEXT" >TEXT</option>
							<option value="MEDIUMTEXT" >MEDIUMTEXT</option>
							<option value="LONGTEXT" >LONGTEXT</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Length</td>
					<td><input v-if="addlength()" type="number" class="form-control form-control-sm" v-model="new_field['length']"></td>
				</tr>
				<tr>
					<td>Default</td>
					<td><input type="text" class="form-control form-control-sm" v-model="new_field['default']"></td>
				</tr>
				<tr>
					<td>Position</td>
					<td>
						<select v-model="new_field['pos']" >
							<option v-for="sd,si in table['all_fields']" v-bind:value="si" >after `{{ si }}`</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="button" class="btn btn-outline-dark btn-sm" value="Add Field" v-on:click="add_now"></td>
				</tr>
				</table>
				<div v-if="amsg" class="alert alert-success py-1" >{{ amsg }}</div>
				<div v-if="aerr" class="alert alert-danger  py-1" >{{ aerr }}</div>


	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="edit_popup" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Edit field {{ edit_field['current_name'] }}</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        	
				<table class="table table-bordered table-sm table-striped w-auto">
				<tr>
					<td>Name</td>
					<td><input type="text" class="form-control form-control-sm" v-model="edit_field['name']"></td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<select v-model="edit_field['type']" class="form-select form-select-sm" >
							<option value="INT" >INT</option>
							<option value="VARCHAR" >VARCHAR</option>
							<option value="TEXT" >TEXT</option>
							<option value="TINYINT" >TINYINT</option>
							<option value="SMALLINT" >SMALLINT</option>
							<option value="MEDIUMINT" >MEDIUMINT</option>
							<option value="INT" >INT</option>
							<option value="BIGINT" >BIGINT</option>
							<option value="DECIMAL" >DECIMAL</option>
							<option value="FLOAT" >FLOAT</option>
							<option value="DOUBLE" >DOUBLE</option>
							<option value="BOOLEAN" >BOOLEAN</option>
							<option value="DATE" >DATE</option>
							<option value="DATETIME" >DATETIME</option>
							<option value="TIMESTAMP" >TIMESTAMP</option>
							<option value="TIME" >TIME</option>
							<option value="CHAR" >CHAR</option>
							<option value="TINYTEXT" >TINYTEXT</option>
							<option value="TEXT" >TEXT</option>
							<option value="MEDIUMTEXT" >MEDIUMTEXT</option>
							<option value="LONGTEXT" >LONGTEXT</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Length</td>
					<td><input v-if="editlength()" type="number" class="form-control form-control-sm" v-model="edit_field['length']"></td>
				</tr>
				<tr>
					<td>Default</td>
					<td><input type="text" class="form-control form-control-sm" v-model="edit_field['default']"></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="button" class="btn btn-outline-dark btn-sm" value="Update Field" v-on:click="edit_now"></td>
				</tr>
				</table>
				<div v-if="emsg" class="alert alert-success py-1" >{{ emsg }}</div>
				<div v-if="eerr" class="alert alert-danger  py-1" >{{ eerr }}</div>

	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="index_popup" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Create Index</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        	
				<table class="table table-bordered table-sm table-striped w-auto">
				<tr>
					<td>Name</td>
					<td><input type="text" class="form-control form-control-sm" v-model="new_index['name']"></td>
				</tr>
				<tr>
					<td>Keys</td>
					<td>
						<table class="table table-bordered table-sm w-auto" >
							<tr v-for="kd,ki in new_index['keys']" >
								<td>
									<select v-model="kd['name']" class="form-select form-select-sm" >
										<option v-for="id,iff in table['all_fields']" v-bind:value="iff" >{{ iff }}</option>
									</select>
								</td>
								<td>
									<input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="del_item(ki)" >
								</td>
							</tr>
						</table>
						<input type="button" class="btn btn-outline-dark btn-sm" value="+" v-on:click="add_item" >
					</td>
				</tr>
				<tr>
					<td>Atrributes</td>
					<td><label><input type="checkbox"  v-model="new_index['unique']" > Unique Index</label></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="button" class="btn btn-outline-dark btn-sm" value="Create Index" v-on:click="create_index"></td>
				</tr>
				</table>
				<div v-if="cimsg" class="alert alert-success py-1" >{{ cimsg }}</div>
				<div v-if="cierr" class="alert alert-danger  py-1" >{{ cierr }}</div>

	      </div>
	    </div>
	  </div>
	</div>


</div>
<script>
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
			"roles": <?=json_encode($roles) ?>,
			"con_error": "<?=$con_error ?>",
			"amsg": "", "aerr": "", "emsg": "", "eerr": "", "imsg": "", "ierr": "", "cimsg": "", "cierr": "",
			"new_field": {
				"name": "", "type": "", "length": "50", "default": "", "pos": "+1"
			},
			"edit_field": {
				"current_name": "", "name": "", "type": "", "length": "50", "default": "", "pos": "+1"
			},
			"new_index": {
				"name": "", 
				"keys": [
					{
						"name": "", "type": ""
					}
				], 
				"unique": false, 
			}
		};
	},
	mounted: function(){
		
	},
	methods : {
		show_add_form: function(){
			this.new_field= {
				"name": "", 
				"type": "", 
				"length": "50", 
				"default": "", 
				"pos": ""
			};
			this.add_popup = new bootstrap.Modal(document.getElementById('add_popup'));
			this.add_popup.show();
		},
		show_index_form: function(){
			this.new_index={
				"name": "", 
				"keys": [
					{
						"name": "", "type": ""
					}
				],
				"unique": false, 
			};
			this.index_popup = new bootstrap.Modal(document.getElementById('index_popup'));
			this.index_popup.show();
		},
		show_edit_form: function(si){
			var t = this.table['all_fields'][ si ]['mapped_type'];
			console.log( t );
			var m = t.match(/^([a-z]+)\(([0-9]+)\)$/i);
			console.log( m );
			var l = 50;
			if( m!=null ){
				t = m[1];
				l = m[2];
			}
			this.edit_field = {
				"current_name": this.table['all_fields'][ si ]['name']+'', 
				"name": this.table['all_fields'][ si ]['name']+'', 
				"type": t.toUpperCase()+'', 
				"length": l+'', 
				"default": this.table['all_fields'][ si ]['default']+'', 
			};
			this.edit_popup = new bootstrap.Modal(document.getElementById('edit_popup'));
			this.edit_popup.show();
		},
		addlength: function(){
			if( this.new_field['type'].match(/CHAR/i) ){
				return true;
			}else{ return false;}
		},
		editlength: function(){
			if( this.edit_field['type'].match(/CHAR/i) ){
				return true;
			}else{ return false;}
		},
		del_item: function(ki){
			if( this.new_index['keys'].length > 1 ){
				this.new_index['keys'].splice(ki,1);
			}
		},
		add_item: function(){
			this.new_index['keys'].push({"name": "", "type": ""});
		},
		echo__: function(v){
			if( typeof(v)=="object" ){
				console.log( JSON.stringify(v,null,4) );
			}else{
				console.log( v );
			}
		},
		add_now: function(){
			this.aerr = "";
			this.amsg = "";
			if( this.new_field['name'].match(/^[a-z][a-z0-9\-\_]{1,50}$/i) == null ){
				this.aerr = "Name should be [a-z][a-z0-9-_]{1,50}";return;
			}
			if( this.new_field['type'] == "" ){
				this.aerr = "Type required";return;
			}
			if( this.new_field['type'].match(/CHAR/i) ){
				if( this.new_field['length'].toString().match(/^[0-9]+$/) == null ){
					this.aerr = "Length is required for VARCHAR type";return;
				}
			}
			if( this.new_field['pos'] == "" ){
				this.aerr = "Position required";return;
			}else if( this.new_field['pos'] in this.table['all_fields'] == false ){
				this.aerr = "Incorrect field position";return;
			}
			this.amsg = "Creating...";
			axios.post("?", {
				"action"	: "database_mysql_add_field", 
				"new_field": this.new_field,
			}).then(response=>{
				this.amsg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.amsg = "Field created"
								this.table['all_fields'] = response.data['fields'];
								this.table['keys'] = response.data['keys'];
							}else{
								this.aerr = response.data['error'];
							}
						}else{
							this.aerr = "Invalid response";
						}
					}else{
						this.aerr = "Incorrect response";
					}
				}else{
					this.aerr = "Error: " . response.status;
				}
			}).catch(error=>{
				this.aerr = error.message;
			});
		},
		edit_now: function(){
			this.eerr = "";
			this.emsg = "";
			if( this.edit_field['name'].match(/^[a-z][a-z0-9\-\_]{1,50}$/i) == null ){
				this.eerr = "Name should be [a-z][a-z0-9-_]{1,50}";return;
			}
			if( this.edit_field['type'] == "" ){
				this.eerr = "Type required";return;
			}
			var t = this.edit_field['type'];
			if( this.edit_field['type'].match(/CHAR/i) ){
				if( this.edit_field['length'].toString().match(/^[0-9]+$/) == null ){
					this.eerr = "Length is required for VARCHAR type";return;
				}
				t = t + "(" + this.edit_field['length'] + ")";
			}
			var f = false;
			if( this.edit_field['name'] != this.edit_field['current_name'] ){
				f = true;
			}
			var d = this.table['all_fields'][ this.edit_field['current_name'] ];
			if( t != d['type'] ){
				f = true;
			}
			if( !f ){
				this.errr = "No changes identified";
			}
			this.emsg = "Creating...";
			axios.post("?", {
				"action"	: "database_mysql_edit_field", 
				"edit_field": this.edit_field,
			}).then(response=>{
				this.emsg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.emsg = "Field Updated";
								this.table['all_fields'] = response.data['fields'];
								this.table['keys'] = response.data['keys'];
							}else{
								this.eerr = response.data['error'];
							}
						}else{
							this.eerr = "Invalid response";
						}
					}else{
						this.eerr = "Incorrect response";
					}
				}else{
					this.eerr = "Error: " . response.status;
				}
			}).catch(error=>{
				this.eerr = error.message;
			});
		},
		delete_field: function(fi){
			if( confirm("Are you sure to drop field `" + fi + "`?" ) ){
				axios.post("?", {
					"action"	: "database_mysql_drop_field", 
					"field": fi,
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									alert("Field dropped");
									this.table['all_fields'] = response.data['fields'];
									this.table['keys'] = response.data['keys'];
								}else{
									alert( response.data['error']);
								}
							}else{
								alert( "Invalid response");
							}
						}else{
							alert( "Incorrect response");
						}
					}else{
						alert( "Error: " . response.status);
					}
				}).catch(error=>{
					alert( error.message );
				});
			}
		},
		drop_index: function(ki){
			if( confirm("Are you sure to drop Index `" + ki + "`?" ) ){
				axios.post("?", {
					"action"	: "database_mysql_drop_index", 
					"index": ki,
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									alert("Index dropped");
									this.table['all_fields'] = response.data['fields'];
									this.table['keys'] = response.data['keys'];
								}else{
									alert( response.data['error']);
								}
							}else{
								alert( "Invalid response");
							}
						}else{
							alert( "Incorrect response");
						}
					}else{
						alert( "Error: " . response.status);
					}
				}).catch(error=>{
					alert( error.message );
				});
			}
		},
		create_index: function(){
			this.cierr = "";
			this.cimsg = "";
			if( this.new_index['name'].match(/^[a-z][a-z0-9\-\_]{1,50}$/i) == null ){
				this.cierr = "Name should be [a-z][a-z0-9-_]{1,50}";return;
			}
			if( typeof(this.new_index['keys']) != "object" ){
				this.cierr = "Keys required";return;
			}
			for( var i=0;i<this.new_index['keys'].length;i++ ){
				if( this.new_index['keys'][i]['name'] == "" ){
					this.cierr = "Field required for index key "+(i+1);return;
				}
			}
			this.cimsg = "Creating index...";
			axios.post("?", {
				"action"	: "database_mysql_add_index", 
				"new_index": this.new_index,
			}).then(response=>{
				this.cimsg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.cimsg = "Index created";
								this.table['all_fields'] = response.data['fields'];
								this.table['keys'] = response.data['keys'];
							}else{
								this.cierr = response.data['error'];
							}
						}else{
							this.cierr = "Invalid response";
						}
					}else{
						this.cierr = "Incorrect response";
					}
				}else{
					this.cierr = "Error: " . response.status;
				}
			}).catch(error=>{
				this.cierr = error.message;
			});
		},
	}
});
app.mount("#app");
</script>
