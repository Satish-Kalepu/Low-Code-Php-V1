<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div style="float:right;"><a class="btn btn-outline-secondary btn-sm" v-bind:href="dbpath">Back</a></div>

			<h4>Database - <span class="small" style="color:#999;" ><?=ucwords($db['engine']) ?></span>: <?=htmlspecialchars($db['des']) ?> &nbsp;&nbsp;&nbsp;<span class="small" style="color:#999;" >Table:</span> {{ table['table'] }} </h4>

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

<!-- 			<div style="height: 300px; overflow: auto;">
				<pre>{{ table['keys'] }}</pre>
				<pre>{{ table['all_fields'] }}</pre>
			</div> -->



			<table width="100%">
				<tr>
				<td>
					<table>
						<tr>
							<td>
								<select v-model="search_index" class="form-select form-select-sm" style="width:150px; display:inline;" v-on:change="change_index">
									<option v-for="v,indexname in table['keys']" v-bind:value="indexname">{{ indexname }}</option>
								</select>
							</td>
							<td>
								<div v-if="search_index=='primary'">
									<table>
										<tr>
											<td><span style="padding: 0px 10px;" >_id = </span></td>
											<td>
												<select v-model="primary_search['c']" class="form-select form-select-sm" style="width:70px;display:inline;">
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
												<select v-model="primary_search['sort']" class="form-select form-select-sm" style="width:100px;display:inline;">
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
												<select v-model="kd['c']" class="form-select form-select-sm" style="width:70px;display:inline;">
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
												<select v-model="kd['sort']" class="form-select form-select-sm" style="width:100px;display:inline;">
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
					<select v-model="selected_schema" class="form-select form-select-sm w-auto" >
						<option v-for="vs,vi in table['schema']" v-bind:value="vi" >{{ vs['name'] }}</option>
					</select>
				</td>
				<td width="100">
					<button class="btn btn-sm btn-outline-dark" v-on:click="add_record_now">Add Record</button>
				</td>
				</tr>
			</table>

			<div style="overflow: auto;height: calc( 100% - 200px );">	


				<div v-if="Object.keys(table['keys']).length==0" class="alert alert-danger" >Table have no indexes</div>
				<template v-else >
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
									<template v-if="primary_field in dd" >
									<input type="checkbox" :value="dd[ primary_field ]" v-model="delete_ids" >
									</template>
								</td>
								<td>
									<i class="fa fa-edit text-success"  v-on:click="show_edit_record( di )" title="Edit"></i>
								</td>
								<td>
									<i class="fa fa-trash text-danger"  v-on:click="delete_record( di )" title="Delete"></i>
								</td>
								<td class="text-nowrap" v-for="ff,fi in table['schema'][selected_schema]['fields']" ><pre>{{dd[ fi ]}}</pre></td>
							</tr>
						</tbody>
					</table>
				</template>
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

	      	<p v-if="edit_mode=='edit'" >
	      		Editing Record: {{ primary_field }} = {{ edit_record[ primary_field ] }}
	      	</p>

	      	<p>Schema: <select v-model="edit_schema" v-on:change="change_edit_schema" >
	      		<option v-for="sd,si in table['schema']" v-bind:value="si" >{{ si }}</option>
	      	</select></p>

					<table class="table table-bordered table-sm w-auto">
							<tr v-for="fd,fi in edit_template">
								<td>{{ fi }}</td>
								<td>{{ fd['mapped_type'] }}</td>
								<td>
									<div v-if="fi in edit_record" >
										<input v-if="fd['type']=='number'" type="number" class="form-control form-control-sm" v-model="edit_record[ fi ]" >
										<input v-else-if="fd['type']=='boolean'" type="checkbox" class="form-control form-control-sm" v-model="edit_record[ fi ]" >
										<input v-else type="text" class="form-control form-control-sm" v-model="edit_record[ fi ]" >
									</div>
								</td>
								<td>
									<template v-if="fi in edit_record" >
										<span v-if="fd['m']" title="mandatory" >* </span>
									</template>
									<template v-if="fi==primary_field" >
										<span>Primary Field &nbsp;</span> &nbsp; 
										<span v-if="fi in table['all_fields']">{{ table['all_fields'][ fi ]['extra'] }}</span>
									</template>
								</td>
							</tr>
					</table>

	      </div>
	      <div class="modal-footer">
	        	<div class="text-secondary">Note: if _id is unspecified, an unique id will be generated</div> 
				<div><button v-if="edit_mode=='new'"  class="btn btn-sm btn-outline-dark mt-2" v-on:click="save_data('new')"  >Insert</button></div>
				<div><button v-if="edit_mode=='edit'" class="btn btn-sm btn-outline-dark mt-2" v-on:click="save_data('edit')" >Update</button></div>
				<div v-if="permission_to_update" >Record already exists! Do you want to update? <button class="btn btn-sm btn-outline-dark mt-2" v-on:click="change_to_edit()">Yes</button> </div>
				<div v-if="error" class="alert alert-danger py-1" >{{ error }}</div>
				<div v-if="edit_status" class="alert alert-success py-1" >{{ edit_status }}</div>
	      </div>
	    </div>
	  </div>
	</div>


</div>

<style>
  .ace_editor{ font-size:1rem; }
</style>

<script type="text/javascript">
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
			"edit_schema"	: "default",
			"search_index"		: "PRIMARY",
			"primary_search"	: {"c":"=","v":"", "v2":"", "sort":"asc"},
			"index_search"		: [],
			"primary_field": "id",
			"av"			: false,
			"av2"			: false,
			"bv"			: false,
			"bv2"			: false,
			"limit"			: 1000,
			"error"			: "",
			"show_add"		: false,
			"add_record"		: {},
			"edit_record"		: {},
			"edit_template" : {},
			"new_record"		: {},
			"edit_status"		: "",
			"edit_record_index" 	: -1,
			"edit_record_id" 	: "-1",
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
		mounted: function(){
			for(var key in this.table['keys'] ){
				if( key == "PRIMARY" ){
					this.primary_field = this.table['keys'][ key ]['keys'][0]['name'];
				}
			}
			this.change_index();
			this.load_records();
		},
		methods: {
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
						if( this.primary_field in this.data_list[i] ){
							v.push( this.data_list[i][ this.primary_field ] );
						}
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
						"action"		: "database_mysql_delete_record",
						"record_id"		: this.data_list[ this.delete_record_index ][ this.primary_field ],
						"primary_field": this.primary_field,
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
						"action"		: "database_mysql_delete_record_multiple",
						"delete_ids"		: this.delete_ids,
						"primary_field": this.primary_field
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
				if( this.index_search.length == 0 ){return false;}
				var v = {
					"action": "database_mysql_load_records",
					"limit": this.limit,
					"search_index": this.search_index,
					"index_search": this.index_search,
					"primary_search": this.primary_search,
					"schema": this.selected_schema
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
			change_edit_schema: function(){
				this.edit_template =  JSON.parse(JSON.stringify(this.table['schema'][ this.edit_schema ]['fields']));
				if( this.edit_mode == "edit" ){
					delete(this.edit_template[ this.primary_field ]);
				}else{
					this.edit_record = this.create_edit_template( this.edit_template ,{} )
				}
			},
			change_to_edit: function(){
				this.edit_record_id = this.edit_record[ this.primary_field ]+'';
				delete(this.edit_template[ this.primary_field ]);
				this.edit_mode = "edit";
				this.permission_to_update=false;
				this.error = "";
			},
			show_edit_record: function( vid__ ){
				this.edit_status = "";this.error = "";
				this.edit_record_index = Number(vid__);
				this.edit_record_id = "";
				this.edit_mode = "edit";
				this.edit_record = JSON.parse(JSON.stringify(this.data_list[vid__]));
				this.edit_template =  JSON.parse(JSON.stringify(this.table['schema'][ this.edit_schema ]['fields']));
				delete(this.edit_template[ this.primary_field ]);
				this.show_add = new bootstrap.Modal(document.getElementById('edit_record_popup'));
				this.show_add.show();
			},
			add_record_now: function(){
				this.edit_status = "";this.error = "";
				this.edit_record_index = -1;
				this.edit_record_id = "";
				this.edit_mode = "new";
				this.edit_template =  JSON.parse(JSON.stringify(this.table['schema'][ this.edit_schema ]['fields']));
				this.edit_record = this.create_edit_template( this.edit_template ,{} )
				this.show_add = new bootstrap.Modal(document.getElementById('edit_record_popup'));
				this.show_add.show();
			},
			create_edit_template( vfields__ ){
				var d = {};
				for( var field__ in vfields__ ){
					if( field__ == this.primary_field ){
						d[field__+''] = "";
					}else if( vfields__[field__]['type'] == "number" ){
						d[field__+''] = 0;
					}else if( vfields__[field__]['type'] == "boolean" ){
						d[field__+''] =false;
					}else{
						d[field__+''] = "";
					}
				}
				return d;
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

				var data = {};
				for( var f in this.edit_template ){
					if( f in this.edit_record == false ){
						alert( "Field `" + f + "` not found in record");return false;
					}
					if( this.edit_record[f].toString().trim() == "" ){
						if( this.edit_template[ f ]['m'] && this.primary_field != f ){
							alert( "Field `" + f + "` is mandatory");return false;
						}
					}else{
						if( this.edit_template[ f ]['type'] == "number" ){
							if( this.edit_record[ f ].toString().match(/^[0-9\.]+$/) == null ){
								alert( "Field `" + f + "` should be numeric");return false;
							}
						}
						data[ f ] = this.edit_record[ f ];
					}
				}

				if( this.edit_mode == "edit" ){
					if( this.edit_record_index != -1 ){
						var record_id = this.data_list[ this.edit_record_index ][ this.primary_field ];
					}else{
						var record_id = this.edit_record_id;
					}
				}else{
					var record_id = "new";
				}
				vpost_data = {
					"action"		: "database_mysql_update_record",
					'record'		: data,
					'record_id'		: record_id,
					'primary_field'		: this.primary_field,
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
								if( this.edit_record_index != -1 ){
									this.data_list[ this.edit_record_index ] = JSON.parse( JSON.stringify( vdata["data"] ) );
								}
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
}).mount("#app");

</script>
