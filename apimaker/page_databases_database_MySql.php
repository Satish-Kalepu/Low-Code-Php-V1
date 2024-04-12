<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<input type="button" class="btn btn-sm btn-outline-dark float-end" value="Add Table" v-on:click="show_create_table">

			<h4>Database - <span class="small" style="color:#999;" ><?=ucwords($db['engine']) ?></span>: <?=htmlspecialchars($db['des']) ?></h4>

			<div style="height: calc( 100% - 150px ); overflow:auto; padding-right:20px;">

				<div v-if="msg" class="alert alert-primary py-2" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger py-2" >{{ err }}</div>

				<div class="text-center border m-5 p-5" v-if="Object.keys(tables).length == 0">
					<h5 class="text-secondary">Create your first table</h5>
					<div><input type="button" class="btn btn-sm btn-outline-dark float-end" value="Add Table" v-on:click="show_create_table"></div>
				</div>
				<!-- <pre>{{ tables }}</pre> -->
				<div v-if="Object.keys(tables).length > 0">

					<table class="table table-hover table-striped table-bordered table-sm w-auto" >
					<thead class="sticky">
						<tr>
							<td>Table</td>
							<td align="right">Documents</td>
							<td align="right">Data Size</td>
							<td align="right">IndexSize</td>
							<td align="left"></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>-</td>
							<td align="right"><b>{{ tot['objects'] }}</b></td>
							<td align="right"><b>{{ size_format(tot['datasize']) }}</b></td>
							<td align="right"><b>{{ size_format(tot['indexSize']) }}</b></td>
							<td align="left">-</td>
						</tr>
						<tr v-for="d in tables">
							<td><a v-bind:href="dbpath+'table/'+d['_id']+'/manage'" >{{ d['table'] }}</a></td>
							<td align="right">{{ d['count'] }}</td>
							<td align="right">{{ size_format(d['size']) }}</td>
							<td align="right">{{ size_format(d['indexSize']) }}</td>
							<td align="left">
								<div class="text-danger" v-if="'error' in d">{{ d['error'] }}</div>
								<div><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_table(d['_id'])" ></div>
							</td>
						</tr>
					</tbody>
					</table>

				</div>

				<p>Grants </p>
				<pre>{{ roles }}</pre>

			</div>
		</div>
	</div>


	<div class="modal fade" id="delete_modal" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Delete table definition</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">

	      		<p><label><input type="radio" v-model="delete_type" value="defintion" > Delete table defintion</label></p>
	      		<p><label><input type="radio" v-model="delete_type" value="source" > Drop table from source database</label></p>
	        	
				<div><input type="button" value="IMPORT" style="float:right;" v-on:click="import_fields" /></div>

	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="create_popup" tabindex="-1" >
	  <div class="modal-dialog modal-lg modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Create Table</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">

	      	<div>Table Name:</div>
	      	<p><input type="text" v-model="new_table['name']" class="form-control form-control-sm w-auto" placeholder="New table name"></p>

	      	<div>Fields:</div>
	      	<table class="table table-bordered table-sm table-striped w-auto">
				<tr>
					<td>Name</td><td>Type</td><td>Length</td><td>Default</td><td>-</td><td>-</td><td>-</td>
				</tr>
				<tr v-for="fd,fi in new_table['fields']">
					<td><input type="text" class="form-control form-control-sm" v-model="fd['name']"></td>
					<td>
						<select v-model="fd['type']" class="form-select form-select-sm" >
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
					<td><input v-if="addlength(fi)" type="number" class="form-control form-control-sm" v-model="fd['length']" style="width:60px;"></td>
					<td><input type="text" class="form-control form-control-sm" v-model="fd['default']"></td>
					<td><span v-if="new_table['primary_field']==fd['name']">Key</span></td>
					<td><input type="button" class="btn btn-outline-secondary btn-sm" value="&uarr;" v-on:click="move_up(fi)"></td>
					<td><input type="button" class="btn btn-outline-secondary btn-sm" value="&darr;" v-on:click="move_down(fi)"></td>
					<td><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="del_item(fi)"></td>
				</tr>
				</table>
				<p><input type="button" class="btn btn-outline-dark btn-sm" value="Add Field" v-on:click="add_item"></p>

				<div style="display: flex; column-gap:10px; margin-bottom: 20px;">
					<div>
						<div>Primary Key</div>
						<div><select class="form-select form-select-sm" v-model="new_table['primary_field']">
							<option v-for="fd in new_table['fields']" v-bind:value="fd['name']" >{{ fd['name'] }}</option>
						</select></div>
					</div>
					<div>
						<div>Auto Increment</div>
						<div><input type="checkbox" v-model="new_table['ai']" ></div>
					</div>
				</div>

				<div v-if="cmsg" class="alert alert-success py-1" >{{ cmsg }}</div>
				<div v-if="cerr" class="alert alert-danger  py-1" >{{ cerr }}</div>

				<div><input type="button" class="btn btn-outline-dark btn-sm" value="Create Table" v-on:click="create_now" /></div>
				<!-- <pre>{{ new_table }}</pre> -->

	      </div>
	    </div>
	  </div>
	</div>



</div>

<script>
var app = Vue.createApp({
		data: function(){
			return {
				"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
				"dbpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/databases/<?=$config_param3 ?>/",
				"app__": <?=json_encode($app) ?>,
				"db": <?=json_encode($db) ?>,
				"db_id": "<?=$config_param3 ?>",
				"engine": "<?=$db['engine'] ?>",
				"create_popup": false,
				"tables": [
				],
				"tot": {
					'objects': "",
					'datasize': "",
					'storageSize': "",
					'indexSize': "",
					'views': "",
				},
				"roles": {},
				"msg": "", "err": "", "cmsg": "", "cerr": "",
				"new_table": {
					"name": "",
					"fields": [
						{"name": "id", "type": "INT", "length": "50", "default": ""},
						{"name": "name", "type": "VARCHAR", "length": "50", "default": ""},
						{"name": "city", "type": "VARCHAR", "length": "50", "default": ""},
						{"name": "age", "type": "TINYINT", "length": "50", "default": ""}
					],
					"primary_field": "id",
					"ai": true,
				},
			};
		},
		mounted: function(){
			this.load_tables();
		},
		methods: {
			del_item: function(vi){
				this.new_table['fields'].splice(vi,1);
			},
			add_item: function(){
				this.new_table['fields'].push({"name": "", "type": "", "length": "50", "default": ""});
			},
			move_up: function(vi){
				if( vi>0 ){
					var s = this.new_table['fields'].splice(vi,1);
					this.new_table['fields'].splice(vi-1,0,s[0]);
				}
			},
			move_down: function(vi){
				if( vi<this.new_table['fields'].length-1 ){
					var s = this.new_table['fields'].splice(vi,1);
					this.new_table['fields'].splice(vi+1,0,s[0]);
				}
			},
			addlength: function(vi){
				if( this.new_table['fields'][vi]['type'].match(/CHAR/i) ){
					return true;
				}else{ return false;}
			},
			show_create_table: function(){
				this.create_popup = new bootstrap.Modal(document.getElementById('create_popup'));
				this.create_popup.show();
			},
			size_format: function(v){
				// $kb = $v/1024;
				kb = Number(v)/1024;
				if( kb < 1024 ){
					return kb.toFixed(0) + " KB";
				}
				mb = kb/1024;
				if( mb < 1024 ){
					return mb.toFixed(0) + " MB";
				}
				gb = mb/1024;
				if( gb < 1024 ){
					return "<span style='color:red;'>" + gb.toFixed(0) + " GB</span>";
				}
			},
			echo: function(v){
				if( typeof(v) == "object" ){
					console.log( JSON.stringify(v,null,4) );
				}else{
					console.log( v );
				}
			},
			get_data_model: function( t ){
				console.log( t)
				return this.create_data_template( JSON.parse( JSON.stringify( t )));
			},
			create_data_template: function( vdata__ ){
				for( var i in vdata__ ){
					if( i == "_id" ){
						vdata__[ i ] = "(Primary Key)";
					}else if( vdata__[i]['type'] == "dict" ){
						vdata__[i] = this.create_data_template( vdata__[i]['sub'] );
					}else if( vdata__[i]['type'] == "list" ){
						var vv = [];
						for( var vsubd=0;vsubd<vdata__[i]['sub'].length;vsubd++){
							vv.push( this.create_data_template( vdata__[i]['sub'][vsubd] ) );
						}
						vdata__[i] = vv;
					}else if( vdata__[i]['type'] == "number" ){
						vdata__[i] = "number";
					}else{
						vdata__[i] = "text";
					}
				}
				return vdata__;
			},
			delete_table:function(vid){
				if( confirm("Are You Sure to Delete Table") ){
					vd__ = {
						"action"		: "delete_table",
						"table_id"			: vid,
					};
					axios.post("?",vd__).then(response=>{
						if( "status" in response.data ){
							var vdata = response.data;
							if( vdata['status'] == "success" ){
								this.load_tables();
							}else{
								alert( "Error deleting table: \n" + vdata["details"] );
							}
						}else{
							alert( "Incorrect response: \n" + response.data );
						}
					});
				}
			},
			load_tables:function(){
				this.err = "";
				this.msg = "Loading tables";
				axios.post("?",{
					"action"	: "database_mysql_load_tables",
				}).then(response=>{
					this.msg = "";
					if( response.data.hasOwnProperty("status") ){
						var vdata = response.data;
						if(vdata['status'] == "success"){
							this.tables = vdata['tables'];
							this.tot = vdata['tot'];
							this.roles = vdata['roles'];
						}else{
							this.err = "Error: "  + vdata['error'];
						}
					}else{
						this.err = "invalid response";
					}
				}).catch(error=>{
					this.err = error.message;
				});
			},
			create_now:function(){
				this.cerr = "";
				this.cmsg = "";
				if( this.new_table['name'].match(/^[a-z][a-z0-9\-\_]{2,50}$/i) == null ){
					this.cerr = "Table name incorrect";return ;
				}
				var pk = -1;
				var n = {};
				for(var i=0;i<this.new_table['fields'].length;i++){
					var fd = this.new_table['fields'][i];
					if( fd['name'].match(/^[a-z][a-z0-9\-\_]{1,50}$/i) == null ){
						this.cerr = "Field name `" + fd['name'] + "` incorrect";return ;
					}
					if( fd['name'] in n ){
						this.cerr = "Field name `" + fd['name'] + "` repeated";return ;
					}
					n[ fd['name'] ] = 1;
					if( fd['name'] == this.new_table['primary_field'] ){
						pk = i;
					}
					if( fd['type'] == "" ){
						this.cerr = "Field type required for `" + fd['name'] + "` ";return ;
					}
					if( fd['type'].match(/CHAR/) ){
						if( fd['length'].match(/^[0-9]+$/) == null ){
							this.cerr = "Field type "+fd['type']+" require length ";return ;
						}
					}
					if( fd['default']!="" ){
						if( fd['type'].match(/(INT|FLOAT|DOUBLE)/) ){
							if( fd['default'].match(/^[0-9\.]+$/) == null ){
								this.cerr = "Field `"+fd['name']+"` defaut value must be number ";return ;
							}
						}else{
							if( fd['default'].match(/^[a-z0-9\.\-\_]+$/) == null ){
								this.cerr = "Field `"+fd['name']+"` defaut value must be plain ";return ;
							}
						}
					}
				}
				if( this.new_table['primary_field'] == "" || pk == -1 ){
					this.cerr = "Need primary key";return ;
				}else if( pk > 0 ){
					this.cerr = "Primary key should always be the first field";return ;
				}
				if( this.new_table['fields'][0]['type'].match(/(INT|float|double)/i) == null ){
					if( this.new_table['ai'] ){
						this.cerr = "Primary key is not of type Number hence the auto increment is not possible";return ;
					}
				}
				axios.post("?",{
					"action"	: "database_mysql_create_table",
					"new_table" : this.new_table,
				}).then(response=>{
					this.cmsg = "";
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								var vdata = response.data;
								if( vdata['status'] == "success" ){
									this.cmsg = "Sucessfully created";
									this.load_tables();
								}else{
									this.cerr = "Error: "  + vdata['error'];
								}
							}else{
								this.cerr = "invalid response";
							}
						}else{
							this.cerr = "invalid response";
						}
					}else{
						this.cerr = "Error: " + response.status;
					}
				}).catch(error=>{
					this.cerr = "Error: " + error.message;
				});
			},
		}
}).mount("#app");
</script>