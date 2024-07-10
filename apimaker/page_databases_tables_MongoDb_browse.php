<script  src="<?=$config_global_apimaker_path ?>js/beautify-html.js" ></script>
<script  src="<?=$config_global_apimaker_path ?>js/beautify-css.js" ></script>
<script  src="<?=$config_global_apimaker_path ?>js/beautify.js" ></script>

<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
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
					<a class="nav-link<?=$config_param6=='import'?" active":"" ?>" v-bind:href="tablepath+'import'">Import</a>
				</li>
				<li class="nav-item">
					<a disabled class="nav-link<?=$config_param6=='export'?" active":"" ?>" v-bind:href="tablepath+'export'">Export</a>
				</li>
			</ul>

			<table width="100%">
				<tr>
				<td>
					<table>
						<tr>
							<td>
								<select v-model="search_index" class="form-control form-control-sm" style="width:150px;display:inline;" v-on:change="change_index">
									<option value="primary">Primary Index</option>
									<option v-for="v,indexname in table['keys']" v-bind:value="indexname">{{ indexname }}</option>
								</select>
							</td>
							<td>
								<div v-if="search_index=='primary'">
									<table>
										<tr>
											<td><span style="padding: 0px 10px;" >_id = </span></td>
											<td>
												<select v-model="primary_search['c']" class="form-control form-control-sm" style="width:70px;display:inline;">
													<option v-for="f,i in filters" v-bind:value="i" >{{ f }}</option>
												</select>
											</td>
											<td>
												<template v-if="primary_search['c']!='><'">
													<input type="text" autocomplete="off" v-model="primary_search['v']" placeholder="Search"  v-bind:class="{'form-control form-control-sm':true,'border-danger':av}"  style="width:150px;display:inline;" >
												</template>
												<template v-else>
													<input type="text" autocomplete="off" v-model="primary_search['v']" placeholder="From"  v-bind:class="{'form-control form-control-sm':true,'border-danger':av}"  style="width:80px;display:inline;" >
													<input type="text" autocomplete="off" v-model="primary_search['v2']" placeholder="To"  v-bind:class="{'form-control form-control-sm':true,'border-danger':av2}"  style="width:80px;display:inline;" >
												</template>
											</td>
											<td>
												<select v-model="primary_search['sort']" class="form-control form-control-sm" style="width:100px;display:inline;">
													<option value="asc" >Ascending</option>
													<option value="desc" >Descending</option>
												</select>
											</td>
										</tr>
									</table>
								</div>
								<div v-else-if="search_index in table['keys']">
									<table v-if="index_search.length>0">
										<tr v-for="kd,ki in index_search">
											<td><span style="padding: 0px 10px;" >{{ kd['field'].replace(".","->") }}</span></td>
											<td>
												<select v-model="kd['c']" class="form-control form-control-sm" style="width:70px;display:inline;">
													<option v-for="f,i in filters" v-bind:value="i" >{{f}}</option>
												</select>
											</td>
											<td>
												<template v-if="kd['c']!='><'">
													<input v-bind:type="kd['type']" autocomplete="off" v-model="kd['v']" placeholder="Search" v-bind:class="{'form-control form-control-sm':true,'border-danger':bv}"  style="width:150px;display:inline;" >
												</template>
												<template v-else>
													<input v-bind:type="kd['type']" autocomplete="off" v-model="kd['v']" placeholder="From" v-bind:class="{'form-control form-control-sm':true,'border-danger':bv}"  style="width:80px;display:inline;" >
													<input v-bind:type="kd['type']" autocomplete="off" v-model="kd['v2']" placeholder="To" v-bind:class="{'form-control form-control-sm':true,'border-danger':bv2}"  style="width:80px;display:inline;" >
												</template>
											</td>
											<td>
												<select v-model="kd['sort']" class="form-control form-control-sm" style="width:100px;display:inline;">
													<option value="asc" >Ascending</option>
													<option value="desc" >Descending</option>
												</select>
											</td>
										</tr>
									</table>
								</div>
							</td>
							<td>
								<button class="btn btn-sm btn-outline-dark" v-on:click="search_filter_cond">Search</button>
							</td>
						</tr>
					</table>
				</td>
				<td width="100">
					<select v-model="selected_schema" >
						<option v-for="vs,vi in table['schema']" v-bind:value="vi" >{{ vs['name'] }}</option>
					</select>
				</td>
				<td width="100">
					<button class="btn btn-sm btn-outline-dark" v-on:click="add_record_now">Add Record</button>
				</td>
				</tr>
			</table>

			<div style="overflow: auto;height: calc( 100% - 160px );">	
				<table class="table table-hover table-striped table-sm w-auto"  >
					<thead style="position: sticky;top:0px; background-color:white;box-shadow: inset 0 1px 0 #aaa, inset 0 -1px 0 #aaa;">
						<tr>
							<td>
								<input type="checkbox" v-model="selected_all" v-on:click="Select_all">
							</td>
							<td></td>
							<td>
								<i class="fa fa-trash text-danger" v-on:click="Delete_Record_Multi" v-if="show_delete"></i>
							</td>
							<td  v-for="ff,fi in table['schema'][selected_schema]['fields']"  >{{ ff['name'] }}</td>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dd,di in data_list" class="content" >
							<td>
								<input type="checkbox" :value="dd['_id']" v-model="delete_ids" >
							</td>
							<td>
								<i class="fa fa-edit text-success"  v-on:click="edit_record( di )" title="Edit"></i>
							</td>
							<td>
								<i class="fa fa-trash text-danger"  v-on:click="delete_record( di )" title="Delete"></i>
							</td>
							<td class="text-nowrap" v-for="ff,fi in table['schema'][selected_schema]['fields']" ><pre>{{dd[ fi ]}}</pre></td>
						</tr>
					</tbody>
				</table>
			</div>
			<button v-if="found_more" class="btn btn-outline-dark btn-sm float-end" v-on:click="load_more" >Load More</button>
			<div>Records: {{ total_cnt }} </div>

		</div>
	</div>


	<div class="modal fade" id="edit_record_popup" tabindex="-1" >
	  <div class="modal-dialog modal-lg modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Edit Record</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">

	      	<div id="editorblock" style="display: relative; width:100%; min-height: 350px; height: 100%;" ></div>

	      </div>
	      <div class="modal-footer">
	        	<div class="text-secondary">Note: if _id is unspecified, an unique id will be generated</div> 
				<div><button v-if="edit_mode=='new'"  class="btn btn-sm btn-outline-dark mt-2" v-on:click="save_data('new')"  >Insert</button></div>
				<div><button v-if="edit_mode=='edit'" class="btn btn-sm btn-outline-dark mt-2" v-on:click="save_data('edit')" >Update</button></div>
				<div v-if="permission_to_update" >Record already exists! Do you want to update? <button class="btn btn-sm btn-outline-dark mt-2" v-on:click="edit_mode='edit';permission_to_update=false;error=''">Yes</button> </div>
				<div v-if="error" class="alert alert-danger py-1" >{{ error }}</div>
				<div v-if="edit_status" class="alert alert-success py-1" >{{ edit_status }}</div>
	      </div>
	    </div>
	  </div>
	</div>



<!-- 	<div v-if="show_add" class="importpopup" >
		<div class="importhead clearfix">
			<h4 class="float-start m-1">{{edit_mode == "new"?"Add Data":"Edit Data"}}</h4>
			<button type="button" class="btn btn-sm btn-danger float-end " v-on:click="show_add = false" >&times;</button>
		</div>
		<div class="importbody">
			<ul class="nav nav-tabs mb-2" >
				<li class="nav-item">
					<button v-bind:class="{'nav-link':true,'active':Edit_Tab=='schema'}" href="#" v-on:click="toggle_edit_tab('schema')">Schema</button>
				</li>
				<li class="nav-item">
					<button v-bind:class="{'nav-link':true,'active':Edit_Tab=='json'}" href="#" v-on:click="toggle_edit_tab('json')">Json</button>
				</li>
			</ul>
			<textarea v-if="Edit_Tab=='json'" v-model="add_record2" style="width:100%; height: 300px;"></textarea>
			<table_mongodb_edit_record v-if="Edit_Tab=='schema'" v-bind:schema="add_record"></table_mongodb_edit_record>
		</div>
		<div class="importfooter" >
			<div class="mb-1 text-center">
				<button v-if="edit_mode=='new'" class="btn btn-sm btn-success mt-2" v-on:click="save_data('new')">Insert</button>
				<button v-if="edit_mode=='edit'" class="btn btn-sm btn-success mt-2" v-on:click="save_data('edit')">Update</button>
				<div v-if="permission_to_update" >Record already exists! Do you want to update? <button class="btn btn-sm btn-success mt-2" v-on:click="edit_mode='edit';permission_to_update=false;error=''">Yes</button> </div>
				<div v-if="error" class="alert alert-danger" >{{ error }}</div>
				<div v-if="edit_status" class="alert alert-success" >{{ edit_status }}</div>
			</div>
		</div>
	</div> -->


</div>

<script defer src="<?=$config_global_apimaker_path ?>ace/src/ace.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-language_tools.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-beautify.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-modelist.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-options.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-searchbox.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-statusbar.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-themelist.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-searchbox.js" ></script>

<style>
  .ace_editor{ font-size:1rem; }
</style>


<script type="text/javascript">
<?php
	require("page_databases_tables_MongoDb_edit.js");
?>
var app = Vue.createApp({
	"data"	: function(){
		return {
			"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			"dbpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/databases/<?=$config_param3 ?>/",
			"tablepath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/databases/<?=$config_param3 ?>/table/<?=$config_param5 ?>/",
			"app_id": "<?=$config_param1 ?>",
			"db_id": "<?=$config_param3 ?>",
			"table_id": "<?=$config_param5 ?>",
			"table": <?=json_encode($table,JSON_PRETTY_PRINT) ?>,
			"data_list"		: [],
			"ace": false,
			"selected_schema"	: "default",
			"search_index"		: "primary",
			"primary_search"	: {"c":"=","v":"", "v2":"", "sort":"asc"},
			"index_search"		: [],
			"av"			: false,
			"av2"			: false,
			"bv"			: false,
			"bv2"			: false,
			"limit"			: 1000,
			"error"			: "",
			"show_add"		: false,
			"add_record"		: {},
			"add_record2"		: {},
			"new_record"		: {},
			"edit_status"		: "",
			"edit_record_index" 	: -1,
			"delete_record_index"	: -1,
			"edit_mode"		: "new",
			"permission_to_update"	: false,
			"editdata"		: [],
			"current_data"		: {},
			"last_key"		: false,
			"found_more"		: false,
			"count" 		: 0,
			"sort"			: "desc",
			"first_page"		: false,
			"Edit_Tab"		: 'schema',
			"total_cnt"		: "<?=$total_cnt?$total_cnt:0 ?>",
			"selected_all"		: "",
			"delete_ids"			: [],
			"show_delete"		: "",
			"filters"	  	: {"="	: "=","!=": "!=","<" : "<","<="	: "<=",">": ">",">=": ">=","><"	: "><","^." : "^..."},
			};
		},
		watch:{
		  	delete_ids:function(){
				if( this.delete_ids.length > 0 ){
					this.show_delete = true;
				}
			}
		},
		mounted : function(){
			this.load_records();
		},
		methods : {
		        echo__: function(v__){
				if( typeof(v__) == "object" ){
					console.log( JSON.stringify(v__,null,4) );
				}else{
					console.log( v__ );
				}
			},
			colspan_count: function(){
				return 3+(Object.keys(this.table['schema'][this.selected_schema]['fields']).length);
			},
			Select_all:function(){
				if( this.selected_all == false ){
					this.selected_all = true;
					this.show_delete = true;
					v = [];
					for(i in this.data_list){
						v.push( this.data_list[i]['_id'] );
					}
					this.delete_ids = v;
				}else{
					this.selected_all = false;
					this.show_delete = false;
					this.delete_ids = [];
				}
			},
			delete_record:function( vi ){
				this.delete_record_index = vi;
				if(confirm("Are you sure you want to delete")){
					var vd__ = {
						"action"		: "database_mongodb_delete_record",
						"record_id"		: this.data_list[ this.delete_record_index ]['_id'],
					};
					axios.post( "?", vd__ ).then(response=>{
						if( response.data.hasOwnProperty("status") ){
							var vdata = response.data;
							if(vdata['status'] == "success"){
								this.data_list.splice(this.delete_record_index,1);
								this.delete_record_index = -1;
								alert("Record deleted");
							}else{
								alert( vdata['error'] );
							}
						}else{
							console.log( "error" );
							console.log( response.data );
							alert("incorrect resposne");
						}
					});
				}
			},
			Delete_Record_Multi:function(){
				if( confirm("Are You Sure To Delete This Record") ){
					var vd__ = {
						"action"		: "database_mongodb_delete_record_multiple",
						"delete_ids"		: this.delete_ids,
					};
					axios.post( "?", vd__ ).then(response=>{
						if( "status" in response.data ){
							var vdata = response.data;
							if( vdata['status'] == "success" ){
								this.search_filter_cond();
								alert("Success");
							}else{
								alert("Error: " + vdata['error'] );
							}
						}else{
					        console.log("error");
					        console.log(response.data);
					        alert("incorrect response");
						}
					});
				}
			},
			get_type: function( v ){
				if( this.search_index in this.table['keys'] ){
					if( v == "a" ){
						return this.table['keys'][ this.search_index ]['pk']['type']+'';
					}else if( v == "b" ){
						return this.table['keys'][ this.search_index ]['sk']['type']+'';
					}else{
						return "text";
					}
				}else{
					return "text";
				}
			},
			toggle_edit_tab: function( v ){
				this.Edit_Tab = v+'';
			},
			change_index: function(){
				if( this.search_index == 'primary' ){

				}else{
					if( this.search_index in this.table['keys'] ){
						var k = [];
						for(var i=0;i<this.table['keys'][ this.search_index ]['keys'].length;i++){
							var j = this.table['keys'][ this.search_index ]['keys'][i];
							k.push({
								"field": j['name']+'',
								"type": j['type']+'',
								"cond": "=",
								"value": "",
								"value2": "",
								"sort": "asc",
							});
						}
						this.index_search = k;
					}
				}
			},
			prev: function(){
				this.current_fields_id--;
			},
			next: function(){
				this.current_fields_id++;
			},
			search_filter_cond:function(v){
				this.first_page = true;
				this.last_key = false;
				this.data_list = [];
				this.load_records();
			},
			reset_filter:function(v){
				this.first_page = true;
				this.last_key	= false;
				this.load_records();
			},
			load_more: function(){
				this.load_records();
			},
			load_records: function(){
				var v = {
					"action"		:"load_mongodb_records",
					"db_id"			: this.table['db_id'],
					"table_id"		: this.table['_id'],
					"limit"			: this.limit,
					"skip"			: this.data_list.length,
					"search_index"		: this.search_index,
					"index_search"		: this.index_search,
					"primary_search"	: this.primary_search,
				};
				if( this.last_key ){
					v['last_key'] = this.last_key;
				}
				axios.post("?",v).then(response=>{
					if("status" in response.data ){
						var vdata = response.data;
						if( vdata['status'] == "success" ){
							var r = vdata['data']['records'];
							if( r.length == 0 ){
								this.found_more = false;
								//this.last_key = "";
							}else{
								this.first_page = false;
								for(var j=0;j<r.length;j++){
									this.data_list.push( r[j] );
								}
								if( r.length >= this.limit ){
									this.found_more = true;
									if( this.search_index =="primary"){
										this.last_key = r[ r.length-1 ]['_id'];
									}
								}else{
									this.found_more = false;
								}
							}
						}else{
							this.error = vdata['data'];
						}
					}else{
					        console.log("error");
					        console.log(response.data);
					}
				});
			},
			edit_record: function( vid__ ){
				var vfield__ =  JSON.parse(JSON.stringify(this.table['schema'][ this.selected_schema ]['fields']));
				var vdata__ =  JSON.parse(JSON.stringify(this.data_list[vid__]));
				this.edit_record_index = vid__;
				this.edit_mode = "edit";
				this.add_record2 = JSON.stringify(this.data_list[vid__],null,4);
				this.show_add = new bootstrap.Modal(document.getElementById('edit_record_popup'));
				this.show_add.show();
				setTimeout(this.init_ace,500);
			},
			add_record_now: function(){
				this.edit_record_index = -1;
				this.edit_mode = "new";
				var vfield__ =  JSON.parse(JSON.stringify(this.table['schema'][ this.selected_schema ]['fields']));
				this.add_record2 = JSON.stringify(this.create_json_template( vfield__ ,{} ),null,4).replace(/[\ ]{4}/g, "\t");
				this.show_add = new bootstrap.Modal(document.getElementById('edit_record_popup'));
				this.show_add.show();
				setTimeout(this.init_ace,500);
			},
			init_ace: function(){
				if( typeof(ace) == "undefined" ){
					console.log("Waiting");
					setTimeout(this.init_ace,100);
				}else{
					try{
						this.ace = ace.edit("editorblock");
						this.ace.setOptions({
							enableAutoIndent: true,
							behavioursEnabled: true,
							useSoftTabs: true,
							showPrintMargin: false,
							printMargin: false,
							showFoldWidgets: true,
							showLineNumbers: true,
							customScrollbar: true,
							//fontSize: "12px", //fontFamily: "Arial", // theme: // mode: // tabSize: number// wrap: "off"|"free"|"printmargin"|boolean|number //readOnly: false,
						});
						this.ace.session.setMode("ace/mode/javascript");
						this.ace.setValue( this.add_record2 );
					}catch(e){
						console.log("ACe init error");
					}
				}
			},
			create_json_template( vfields__ ){
				var d = {};
				for( var field__ in vfields__ ){
					if( vfields__[field__]['type'] == "list" ){
						v1 = [];
						v1.push( this.create_json_template(vfields__[field__]['sub'][0]) );
						d[field__+''] = v1 ;
					}else if( vfields__[field__]['type'] == "dict" ){
						d[field__+''] = this.create_json_template( vfields__[field__]['sub'] );
					}else if( vfields__[field__]['type'] == "text" ){
						d[field__+''] = "";
					}else if( vfields__[field__]['type'] == "UniqueId" ){
						d[field__+''] = "(UniqueId)";
					}else if( vfields__[field__]['type'] == "number" ){
						d[field__+''] = 0;
					}else if( vfields__[field__]['type'] == "boolean" ){
						d[field__+''] =false;
					}
				}
				return d;
			},
			create_field_template_edit(vfields__,vdata__){
				for( var i in vfields__ ){
					if( vfields__[i]['type'] == "dict" ){
						if( vdata__.hasOwnProperty(i) == false ){
							vdata__[i] = {};
						}
						vfields__[i]['data'] = this.create_field_template_edit( vfields__[i]['sub'],vdata__[i] );
					}else if( vfields__[i]['type'] == "list" ){
						vfields__[i]['data'] = [];
						if( vdata__.hasOwnProperty(i) == false ){
							vdata__[i] = [];
						}
						for(var jj=0;jj<vdata__[i].length;jj++){
							var vp = {};
							for( var j=0;j<vfields__[i]['sub'].length;j++ ){
								vp = this.create_field_template_edit( JSON.parse( JSON.stringify( vfields__[i]['sub'][0] )) ,vdata__[i][jj] );
							}
							vfields__[i]['data'].push(vp);
						}
					}else{
						if( vdata__.hasOwnProperty(i) == false ){
							vdata__[i] = '';
						}
						vfields__[i]['data'] = vdata__[i];
					}
				}
				return vfields__;
			},
			create_field_template: function( vfields__ ){
				for( var i in vfields__ ){
					if( vfields__[i]['type'] == "dict" ){
						vfields__[i]['sub'] = this.create_field_template( vfields__[i]['sub'] );
					}else if( vfields__[i]['type'] == "list" ){
						vfields__[i]['data'] = [];
						for( var j=0;j<vfields__[i]['sub'].length;j++ ){
							vfields__[i]['data'][j] = this.create_field_template( vfields__[i]['sub'][j] );
						}
					}else{
						vfields__[i]['data'] = "";
					}
				}
				return vfields__;
			},
			create_data_template: function( vdata__ ){
				for( var i in vdata__ ){
					if( vdata__[i]['type'] == "dict" ){
						vdata__[i] = this.create_data_template( vdata__[i]['data'] )
					}else if( vdata__[i]['type'] == "list" ){
						var v = [];
						for( var vsubi = 0;vsubi<vdata__[i]['data'].length;vsubi++){
							v.push( this.create_data_template( vdata__[i]['data'][vsubi] ) );
						}
						vdata__[i] = v;
					}else{
						if( vdata__[i]['type'] == "number" ){
							if( 'data' in vdata__[i] ){
								try{
									if( typeof(vdata__[i]['data']) == "string" ){
										if( vdata__[i]['data'].match(/^[0-9\.]+$/)){
											vdata__[i] = Number(vdata__[i]['data']);
										}else{
											vdata__[i] = 0;
										}
									}else{
										vdata__[i] = vdata__[i]['data'];
									}
								}catch(e){
									console.log("errro : " +  e);
									this.echo__( vdata__[i]['data'] );
								}
							}else{
								vdata__[i]['data'] = 0;
							}
						}else{
							vdata__[i] = vdata__[i]['data']+'';
						}
					}
				}
				return vdata__;
			},
			validate_json: function(template, data, p = ""){
				for( var f in template ){
					if( f in data == false && template[f]['m'] == true ){
						this.error = "Field `" + p+f + "` is required!";
						return true;
					}else if( f in data ){
						var vt = typeof( data[f] );
						if( vt == "string" ){ vt = "text";}
						if( vt == "number" ){ vt = "number";}
						if( vt == "object" && 'length' in data[f] ){ vt = "list";}
						if( vt == "object" && 'length' in data[f] == false ){ vt = "dict";}
						console.log( template[f]['type'] + ":" + vt + ":" + data[f]);
						if( template[f]['type'] == "UniqueId" && vt != "text" ){
							this.error = "Field `"+ p+f +"` should be " + template[f]['type'];return true;
						}else if( template[f]['type'] == "UniqueId"){
							if( data[f] != "(UniqueId)" ){
								this.error = "Field `"+ p+f +"` value should be (UniqueId)";return true;
							}
						}else if( vt != template[f]['type'] ){
							this.error = "Field `"+ p+f +"` should be " + template[f]['type'];return true;
						}
						if( vt == "dict" ){
							if( this.validate_json( template[f]['sub'], data[f], p+f+"." ) ){
								return true;
							}
						}else if( vt == "list" ){
							for(var i=0;i<data[f].length;i++){
								if( this.validate_json( template[f]['sub'][0], data[f][i], p+f+"["+i+"]." ) ){
									return true;
								}
							}
						}
					}
				}
				return false;
			},
			save_data: function(){
				this.error = "";
				this.edit_status = "";

				var d = this.ace.getValue();
				d = d.replace(/\,[\r\n\ \t]*\}/g, "}");
				d = d.replace(/\,[\r\n\ \t]*\]/g, "]");
				try{
					var data = JSON.parse( d );
					var v = this.validate_json(this.table['schema'][ this.selected_schema ]['fields'],data);
					if( v ){
						return false;
					}
				}catch(e){
					this.error = "Error in json: " + e
					return false;
				}
				console.log( data );
				if( this.edit_mode == "edit" ){
					var record_id = data['_id'];
				}else{
					var record_id = "new";
				}
				vpost_data = {
					"action"		: "database_mongodb_update_record",
					'record'		: data,
					'record_id'		: record_id,
					"record_index" 	: this.edit_record_index,
					"edit_mode"		: this.edit_mode,
				};
				axios.post( "?", vpost_data ).then(response => {
					if( response.data.hasOwnProperty("status") ){
						vdata = response.data;
						if( vdata["status"] == "success" ){
							//this.show_add = false;
							if( this.edit_mode == "new" ){
								this.data_list.splice(0, 0, JSON.parse( JSON.stringify( vdata["data"] ) ) );
								this.edit_status = "Record Inserted";
							}else{
								this.data_list[ this.edit_record_index ] = JSON.parse( JSON.stringify( vdata["data"] ) );
								this.edit_status = "Record Updated";
							}
						}else if( vdata['error'].match(/duplicate/i) ){
							this.error = "Record already exists";
							if( this.edit_mode == "new" ){
								this.permission_to_update = true;
							}
						}else{
							this.error = response.data['error'];
						}
					}else{
						console.log("error");
						console.log(response.data);
					}
				});
			},
			ucwords( v ){
				if( v != '' ){
					var str = v.replace( /[\\~\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\\\|\;\:\"\'\,\.\/\<\>\?\t\r\n]+/g, " " );
					str = str.replace( /[\ ]{2,10}/g, " ");
					str = str.trim();
					return (str + '').replace(/^(.)|\s+(.)/g, function ($1){return $1.toUpperCase()})
				}
			},
		}
});
app.component( "table_mongodb_edit_record", table_mongodb_edit_record );
app.mount("#app");

</script>
