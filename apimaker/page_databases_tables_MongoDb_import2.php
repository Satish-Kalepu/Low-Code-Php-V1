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

			<div style="overflow: auto;height: calc( 100% - 130px );">



				<div class="mb-1 row" v-if="!show_overview">
					<div class="col-2">
						<select v-model="selected_schema" name="selected_schema" id="selected_schema" class="form-control form-control-sm">
							<option v-for="sc,si in table['schema']" v-bind:value="si" >{{ si }}</option>
						</select>
					</div>
					<div class="col-4" v-if="selected_schema">
						<label for="file-upload" class="custom-file-upload"><i class="fa fa-cloud-upload"></i> Upload File</label>
						<input type="file"id="file-upload" name='file' id="file" ref="file" style="display:none;" v-on:change="handleFileUpload">
					</div>
					
				</div>
				<div v-if="show_overview">
					<ul class="nav nav-tabs mt-3" role="tablist">
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true, 'active':(Show_Tab == 'record')}"  v-on:click="toggle_main_tab('record')">Records</a>
						</li>
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true, 'active':(Show_Tab == 'structure')}" v-on:click="toggle_main_tab('structure')">Structure</a>
						</li>
					</ul>
				    <div class="tab-content">
						<div role="tabpanel" class="tab-pane fade" id="record" v-bind:class="{'tab-pane':true, 'show active bgcolor':(Show_Tab == 'record')}" v-if="Show_Tab == 'record'">
							<div class="mb-2 clearfix">
								<h4 class="float-start">Upload File Overview</h4>
								<div class="float-end">
									<div class="float-start m-1">
										<button type="button" v-if="vtype != 'errors' && Error_Records.length != 0 " class="btn btn-sm btn-primary" v-on:click=" get_main_records('errors')">Error Records <span class="badge badge-light">{{Error_Records.length}}</span> </button>
										<button type="button" v-if="vtype != 'main' " class="btn btn-sm btn-primary" v-on:click=" get_main_records('main')">Main Records <span class="badge badge-light">{{Records.length}}</span> </button>
										<button type="button" v-if="vtype != 'duplicate' && Duplicate_Records.length != 0  " class="btn btn-sm btn-primary" v-on:click=" get_main_records('duplicate') ">Duplicates Records <span class="badge badge-light">{{Duplicate_Records.length}}</span> </button>  
									</div>
									<div class="float-start m-1" v-if="uploaded_type == 'csv'">
										<select v-model="delimeter" class="form-control form-control-sm">
											<option v-bind:value='i__' v-for="v__,i__ in delimeter_list">{{v__}} ( {{i__}} ) </option>
										</select>
									</div>
									<div class="float-start m-1" >
										<select v-model="duplicate_check" class="form-control form-control-sm">
											<option value='check'>Check</option>
											<option value='skip'>Skip</option>
											<option value='replace'>Replace</option>
										</select>
									</div>
									<div class="float-start m-1">
										<button type="button" v-if=" Error_Records.length == 0 && Duplicate_Records.length == 0" class="btn btn-sm btn-primary" v-on:click="upload_excel_data">Import</button>
									</div>
								</div>
							</div>
							<div class="row mb-1">
								<div class="col-6">Displaying: {{Start}} to {{End}} of {{TotalRecords}}</div>
								<div class="col-6" align="right">
									<button v-on:click="get_page('prev')" v-if="CurrentPage >1" class="btn btn-sm btn-primary m-1">Prev</button>
									<button v-on:click="get_page('next')" v-if="TotalPages > 1 && CurrentPage != TotalPages " class="btn btn-sm btn-primary m-1">Next</button>
								</div>
							</div>
							<div class="home_main">
								<table class="table table-striped table-bordered table-sm w-auto">
									<thead>
										<tr>
											<td>-</td>
											<td>-</td>
											<td v-for="fv,fi in fields">
												<input v-if="fv['name']!='_id' && fv['new_field'] == 'yes'" type="checkbox" v-model="fv['insert']" title="insert/ignore" >
												<input v-if="fv['name']!='_id' && fv['new_field'] != 'yes'" type="checkbox" disabled v-model="fv['insert']" title="insert/ignore" >
											</td>
										</tr>
										<tr>
											<th>Insert</th>
											<th>Action</th>
											<th v-for="fv,fi in fields"> 
												<div :class="fv['new_field'] == 'yes'?'text-warning':''">{{ fv['name'] }}</div>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr  v-if="load_loader">
											<td v-bind:colspan="Object.keys(fields).length+2">
												<div class="spinner-border" role="status" >
													<span class="sr-only">Loading...</span>
												</div>
											</td>
										</tr>
										<tr v-for="vr,vi in Display_Records">
											<td>
												<button type="button" v-on:click="edit_record(vr['_main_cnt__'],vi )" class="btn btn-primary btn-sm">Edit</button>
											</td>
											<td>
												<input type="checkbox" v-model="vr['_insert__']" title="insert/ignore" >
											</td>
											<td nowrap v-for="fv,fi in fields" >
												<pre v-if="typeof(vr[fi]) == 'object'">{{vr[fi]}}</pre>
												<div v-else v-html="vr[fi]"></div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="structure" v-bind:class="{'tab-pane':true, 'show active bgcolor':(Show_Tab == 'structure')}" v-if="Show_Tab == 'structure'">
							<h4 class="m-1">Structure</h4>
							<dbobject_table_mongodb v-bind:engine="table['engine']" v-bind:level="1" v-bind:items="fields" v-on:edited="table_fields_edited($event)" ></dbobject_table_mongodb>
						</div>
					</div>
				</div>
				<div v-if="ShowEdit" class="importpopup" >
					<div class="importhead clearfix">
						<h4 class="float-start m-1">Edit Data</h4>
						<button type="button" class="btn btn-sm btn-danger float-end " v-on:click="ShowEdit = false" >&times;</button>
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
						<textarea v-if="Edit_Tab=='json'" v-model="EditRecord2" style="width:100%; height: 300px;"></textarea>
						<table_mongodb_edit_record v-if="Edit_Tab=='schema'" v-bind:schema="EditRecord"></table_mongodb_edit_record>
					</div>
					<div class="importfooter" >
						<div class="mb-1 text-center">
							<button class="btn btn-sm btn-success mt-2" v-on:click="save_data">Update</button>
						</div>
					</div>
				</div>
				<div class="progress_div" v-if="ShowProgress">
					<span class="mb-5">Please wait<br>Inserting Records.... </span>
					<div class="progress">
						<div class="progress-bar bg-success" :style="{'width':progress_width}" >{{progress_width}}</div>
					</div>
				</div>

			</div>

		</div>
	</div>
</div>
<script type="text/javascript">
<?php include("page_databases_tables_MongoDb_edit.js"); ?>
<?php include("page_databases_tables_MongoDb_dbobject.js"); ?>
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
			"SucessMsg"		: "",
			"Error"			: "",
			"selected_schema"	: "default",
			"fields"		: {},
			"idfield"               : {},
			"uploaded_type"		: "",
			"ShowProgress" 	        : false,
			"progress_width" 	: "10%",
			"cnt_field"             : 1,
			"PerPage"		: 20,
			"Start"			: 0,
			"End"			: 0,
			"TotalRecords"		: 0,
			"CurrentPage"		: 1,
			"TotalPages"		: 1,
			"raw_data"		: [],
			"Records"		: [],
			"Display_Records"	: [],
			"Duplicate_Records"     : [],
			"Error_Records"         : [],
			"load_loader"           : false,	
			"show_overview"		: false,	
			"ShowEdit"              : false,               
			"EditRecord"            : {},
			"EditRecord2"		: {},
			"EditId"                : 0,
			"EditIdIndex"           : 0,
			"Editcnt"               : 0,
			"Edit_ErrorId"          : 0,
			"total_processed"       : 0,
			"vtype"			: "main",
			"Show_Tab"		: "record",
			"Edit_Tab" 		: 'schema',
			"delimeter"		: ",",
			"duplicate_check"	: "check",
			"delimeter_list"	: {
				",": "Comma",
				".": "Dot",
				"/": "Slash",
				"-": "Hyphen",
				"$": "Dollor",
				"_": "Underscore",
				"#": "Hash",
				"'": "Single Qutote",
				'"': "Double Qutote",
				':': "Colon",
			},
		};
	},
	created: function(){
		this.init__();
	},
	methods:{
		echo__: function(v__){
			if( typeof(v__) == "object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}                                           	
		},
		init__: function(){
			for( field__ in this.table["schema"]["default"]["fields"] ){
				vf__ = this.table["schema"]["default"]["fields"][field__]; 
				if( field__ != "_id" ){
					this.fields[field__] = vf__;
					this.fields[field__]["order"] = this.cnt_field;
					this.fields[field__]["new_field"] ="no";
					this.fields[field__]["insert"] = true;
					this.cnt_field++;
				}else{
					this.idfield = vf__;
					this.idfield["order"] = 0;
				}
			}
		},
		toggle_main_tab: function(v__){
			this.Show_Tab = v__;
		},
		toggle_edit_tab: function( v ){
			this.Edit_Tab = v+'';
		},
		reset_overview__: function(){
			this.SucessMsg = this.Error = "";	
			this.show_overview = this.load_loader = this.ShowEdit = false;			
			this.raw_data	= this.Records = this.Display_Records = this.Duplicate_Records = this.Error_Records = [];
			this.fields = this.idfield = this.EditRecord = this.EditRecord2 ={};
			this.uploaded_type = "";
			this.Start = this.End = this.TotalRecords = this.EditId = this.EditIdIndex = this.Editcnt = this.Edit_ErrorId = this.total_processed = 0;
			this.CurrentPage = this.TotalPages = 1;
			this.init__();
		},
		handleFileUpload: function(){
			this.file = this.$refs.file.files[0];
			var vdotypes = ['csv','xls','xlsx','json'];
			var file = this.file;
			var spilt_data = file.name.split('.');
			var ext  = spilt_data.pop();
			if( vdotypes.indexOf( ext.toLowerCase() ) < 0 ){
				this.Error = 'Select File should be CSV or xlsx or json';
			}else{
				this.load_loader   = true;
				this.show_overview = true;
				this.uploaded_type = ext;
				var readFile = new FileReader();
				readFile.onload = function() { 
					var contents = readFile.result;
					var lines = contents.split(/\n/g);
					if( ext == "csv" ){
						for(var j=0;j<(lines.length-1);j++){
							lines[j] = lines[j].replace(/[\r\n]+/g, "");
							var kk = lines[j].match( /\"(.*?)\"/g );
							if( kk != null ){
								for(var k=0;k<(kk.length-1);k++){
									var kn= kk[k]+"";    						
									kn = kn.replace(/\,/g, "CCCxCCC");
									kn = kn.replace(/\W/g, "");    						
									lines[j] = lines[j].replace( kk[k], kn );
								}
							}
						}
						app2.raw_data = lines;
						app2.review_data();
					}else if( ext == "json" ){
						rows = [];
						for(var j=0;j<(lines.length-1);j++){
							lines[j] = lines[j].trim();
							if(lines[j].endsWith(',') == true ){
								lines[j] = lines[j].slice(0, -1);
								rows.push(JSON.parse(lines[j]) );
							}else{
								rows.push(JSON.parse(lines[j]) );
							}
						}
						app2.raw_data = rows;
						app2.review_data();
					}
				}
				readFile.readAsText( this.file, "utf-8" );
			}
		},
		review_data: function(){
			var vfields = ["pk","sk","pk1","pk2","pk3","pk4","pk5","sk1","sk2","sk3","sk4","sk5"];
			var error_exist = false;
			var rec__ = [];
			var csv_headers__    = [];
			var new_fields__     = {};           
			var new_data__       = [];
			var error_recs__     = [];
			var main_cnt__       = 1;
			if( this.uploaded_type == "csv" ){
				for(var j__ = 0;j__ < (this.raw_data.length-1);j__++){
					v1__ = {};
					v1__ = this.raw_data[j__].split(this.delimeter);
					for(var k__ = 0;k__ < ( v1__.length-1 );k__++){    					
						v1__[k__] = v1__[k__].replace(/CCCxCCC/g, this.delimeter); 
					}
					if( v1__ != "" ){
						rec__.push(v1__);
					}
				}
				for( j__ in rec__[0]){
					if( !new_fields__.hasOwnProperty( rec__[0][j__] ) ){
						csv_headers__.push(rec__[0][j__] );
						new_fields__[ rec__[0][j__] ] = 1;
					}		
				}
				for( field__ in csv_headers__ ){
					if( !this.fields[csv_headers__[field__] ]){
						this.fields[csv_headers__[field__]] = {
											"key"	: csv_headers__[field__].trim()+"",
											"name"	: csv_headers__[field__].trim()+"",
											"type"	: "text",
											"m"	: false,
											"order" : this.cnt_field,
											"sub"	: {},
											"new_field":"yes",
											"insert":false
										};
						this.cnt_field++;	    
					}
					if( vfields.indexOf( csv_headers__[field__] ) > 0 ){
						error_exist = true;
					}
				}
				this.$forceUpdate();
				if( error_exist == true ){
					this.Error = "Upload file should not have fields like pk, sk, pk1, pk2, pk3, pk4, pk5, sk1, sk2, sk3, sk4, sk5";
				}else{
					for(var j___=1;j___<rec__.length;j___++){
						var k__ = {};
						show_error__ = false;
						for(var hh__ = 0;hh__ < csv_headers__.length;hh__++){
							if( rec__[j___][hh__] ){
								k__[csv_headers__[hh__]] = (this.fields[csv_headers__[hh__]]['type'] == 'number'?Number(rec__[j___][hh__]):rec__[j___][hh__] );
							}else{
								k__[csv_headers__[hh__]] = '';	
							}
							if( this.fields[csv_headers__[hh__]]["m"] == true && ( rec__[j___][hh__] == "" || rec__[j___][hh__] == null || rec__[j___][hh__] == undefined)  ){
								this.Error = " Mandatory Fields Data Missing In Uploaded File ";
								this.$set(k__,"_error__","yes" );
								show_error__ = true;
							}
							this.$set(k__,"_insert__","yes" );
							this.$set(k__,"_main_cnt__",main_cnt__);
						}
						var size = Object.keys(k__).length;
						if(size>0){
							new_data__.push( k__ );
							main_cnt__++;
						}
						if( show_error__ == true ){
							this.$set(k__,"_error_cnt__",error_recs__.length);
							error_recs__.push(k__);
						}
					}
					this.Records       = new_data__;
					this.Error_Records = error_recs__;
					this.vtype         = "main";
					this.get_display_record();
				}
			}else{
				var vcnt__ = 1;
				for(var j__ = 0;j__ < (this.raw_data.length-1);j__++){
					for( jj__ in this.raw_data[j__]){
						if( !new_fields__.hasOwnProperty( jj__ ) ){
							csv_headers__.push(jj__ );
							vt = this.get_field_type( this.raw_data[j__][jj__] ,jj__,vcnt__ );
							this.$set( new_fields__, jj__ ,vt );
							vcnt__++;
						}		
					}
				}
				for( field__ in csv_headers__ ){
					if( !this.fields[csv_headers__[field__] ]){
						this.fields[csv_headers__[field__]] = {
											"key"	: csv_headers__[field__].trim()+"",
											"name"	: csv_headers__[field__].trim()+"",
											"type"	: new_fields__[ csv_headers__[field__] ]['type'],
											"m"	: false,
											"order" : this.cnt_field,
											"sub"	: new_fields__[ csv_headers__[field__] ]['sub'],
											"new_field":"yes",
											"insert":false
										};
						this.cnt_field++;	    
					}
					if( vfields.indexOf( csv_headers__[field__] ) > 0 ){
						error_exist = true;
					}
				}
				this.$forceUpdate();
				if( error_exist == true ){
					this.Error = "Upload file should not have fields like pk, sk, pk1, pk2, pk3, pk4, pk5, sk1, sk2, sk3, sk4, sk5";
				}else{ 	
					rec__ = this.raw_data;
					for(var j___=1;j___<rec__.length;j___++){
						var k__ = {};
						show_error__ = false;
						for( hh__ in rec__[j__]){
							if( rec__[j___][hh__] ){
								k__[hh__] = (this.fields[hh__]['type'] == 'number'?Number(rec__[j___][hh__]):rec__[j___][hh__] );
							}else{
								k__[hh__] = '';	
							}
							if( this.fields[hh__]["m"] == true && ( rec__[j___][hh__] == "" || rec__[j___][hh__] == null || rec__[j___][hh__] == undefined)  ){
								this.Error = " Mandatory Fields Data Missing In Uploaded File ";
								this.$set(k__,"_error__","yes" );
								show_error__ = true;
							}
							this.$set(k__,"_insert__","yes" );
							this.$set(k__,"_main_cnt__",main_cnt__);
						}
						var size = Object.keys(k__).length;
						if(size>0){
							new_data__.push( k__ );
							main_cnt__++;
						}
						if( show_error__ == true ){
							this.$set(k__,"_error_cnt__",error_recs__.length);
							error_recs__.push(k__);
						}
					}
					this.Records       = new_data__;
					this.Error_Records = error_recs__;
					this.vtype         = "main";
					this.get_display_record();                                
				}
			}
		},
		get_field_type( vrecord__,vfield__,vorder__ ){
			if( vrecord__ == null ){vrecord__ = '';}
			vt1__ = typeof( vrecord__ );
			if( vt1__ == "string" ){ 
				vt__ = {
					"key"	: vfield__,
					"name"	: vfield__,
					"type"	: "text",
					"m"	: false,
					"order"	: vorder__,
					"sub"	: {},
				     };
			}else if( vt1__ == "number" ){
				vt__ = {
					"key"	: vfield__,
					"name"	: vfield__,
					"type"	: "number",
					"m"	: false,
					"order"	: vorder__,
					"sub"	: {},
				     };
			}else if( vt1__ == "object" ){
				vsub__ = {};
				cnt__ = 1; 
				vdata__ = vrecord__;
				console.log(vrecord__);
				if( vrecord__.hasOwnProperty( 'length' ) == true ){
					vdata__ = vrecord__[0];
					vtype  = "dict";
				}else{	
						
					vtype = "list";
				}
				for( i__ in vdata__){
					if(vdata__[i__] != null ){
						vsub__[i__] = this.get_field_type(vdata__[i__],i__,cnt__ );
						cnt__++;
					}
				}
				vt__ = {
					"key"	: vfield__,
					"name"	: vfield__,
					"type"	: vtype,
					"m"	: false,
					"order"	: vorder__,
					"sub"	: vsub__
				     };
			}
			return  vt__;
		},
		get_page: function(vpage){
			this.CurrentPage =  (vpage == "next" ?this.CurrentPage+1:this.CurrentPage-1);
			this.get_display_record();
		},
		get_display_record: function(){
			this.Display_Records = [];
			if( this.vtype == "main" ){
				this.TotalRecords 	= this.Records.length;
				this.Display_Records 	= this.Records.slice( ( (this.CurrentPage-1)* this.PerPage ) ,( this.CurrentPage * this.PerPage));
			}else if( this.vtype == "duplicate" ){
				this.TotalRecords 	= this.Duplicate_Records.length;
				this.Display_Records 	= this.Duplicate_Records.slice( ( (this.CurrentPage-1)* this.PerPage ) ,( this.CurrentPage * this.PerPage));
			}else if( this.vtype == "errors" ){
				this.TotalRecords 	= this.Error_Records.length;
				this.Display_Records 	= this.Error_Records.slice( ( (this.CurrentPage-1)* this.PerPage ) ,( this.CurrentPage * this.PerPage));
			}
			this.TotalPages		= Math.ceil(this.TotalRecords/this.PerPage);
			this.Start 		= ((this.CurrentPage - 1) * this.PerPage )+1;
			this.End   		= ((this.Start + this.PerPage) > this.TotalRecords)?this.TotalRecords:(this.Start + this.PerPage -1) ;
			this.load_loader        = false;
			this.$forceUpdate();
		},
		get_main_records: function(v__){
			this.vtype            = v__;
			this.CurrentPage      = 1;
			this.Display_Records = [];
			this.get_display_record();
		},
		table_fields_edited: function(  vf ){
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
			this.fields =  vf;
		},
		edit_record: function(vid__,vindex__){
			this.ShowEdit           = true;
			this.EditId             = vid__;
			this.EditIdIndex        = vindex__;
			this.Editcnt            = this.Display_Records[vindex__]["_main_cnt__"];
			this.Edit_ErrorId       = this.Display_Records[vindex__]["_error_cnt__"];
			var vfield__            = JSON.parse(JSON.stringify(this.fields));
			var vdata__             = JSON.parse(JSON.stringify(this.Display_Records[vindex__]));
			this.EditRecord2        = JSON.stringify(this.create_json_template( vfield__,vdata__),null,4).replace(/[\ ]{4}/g, "\t");
			this.EditRecord         = JSON.parse(JSON.stringify( this.create_field_template_edit(vfield__,vdata__) ) );
		},
		create_json_template( vfields__,vdata__ ){
			var d = {};
			for( var field__ in vfields__ ){
				if( vfields__[field__]['type'] == "dict" ){
					v1 = [];
					for( i__ in vdata__[field__] ){
						v1.push(this.create_json_template( vfields__[field__]['sub'] ,vdata__[field__][i__] ) );
					}
					d[field__+''] = v1 ;
				}else if( vfields__[field__]['type'] == "list" ){
					d[field__+''] = this.create_json_template( vfields__[field__]['sub'] ,vdata__[i__] );
				}else if( vfields__[field__]['type'] == "text" ){
					d[field__+''] =( vdata__[field__] != "" && vdata__[field__] != undefined)?vdata__[field__]:'';
				}else if( vfields__[field__]['type'] == "number" ){
					if( vdata__[field__] != "" && vdata__[field__] != undefined){
						try{
							if( typeof(vdata__[field__]) == "string" ){
								if( vdata__[field__].match(/^[0-9\.]+$/)){
									vdata__[field__] = Number(vdata__[field__]);
								}else{
									vdata__[field__] = 0;
								}
							}else{
								vdata__[field__] = vdata__[field__];
							}
						}catch(e){
							console.log("errro : " +  e);
						}
					}else{
						vdata__[field__] = 0;
					}
					d[field__+''] = vdata__[field__];
				}else if( vfields__[field__]['type'] == "boolean" ){
					d[field__+''] =( vdata__[field__] != "" && vdata__[field__] != undefined)?vdata__[field__]:'';
				}
			}
			return d;
		},
		save_data: function(){
			new_record = this.create_data_template( JSON.parse(JSON.stringify(this.EditRecord)) );
			this.ShowEdit = false;
			show_error = false;
			for(  i in this.fields ){
				if( this.fields[i]["m"] == true && ( new_record[i] == "" || new_record[i] == null || new_record[i] == undefined) ){
					this.$set(new_record,"_error__","yes" );
					show_error = true;
				}
			}
			this.$set(new_record,"_insert__","yes" );
			this.$set(new_record,"_main_cnt__",this.Editcnt);
			this.$set(this.Records, this.EditId,new_record);
			this.$set(this.Display_Records, this.EditIdIndex,new_record);
			if( show_error == false ){
				this.$set(this.Error_Records, this.Edit_ErrorId,new_record);
				this.Error_Records.splice(this.Edit_ErrorId,1);
			}
			this.$forceUpdate();
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
		upload_excel_data: function(){
			if( this.duplicate_check == '' ){
				this.Error = "Please Select Duplicate Action";
			}else{
				this.fields["_id"] = this.idfield;
				//data__ = this.review_records_before_upload();
				if( 1 == 1){
					var vpost = {
							"action"	        : "import_mongodb_data",
							"duplicate_check"       : this.duplicate_check,
							"selected_schema"       : this.selected_schema,
							"data"                  : this.Records,
							"fields"  	        : this.fields,
							"table_id"	        : this.table["_id"],
							"security_token"	: $("#security_token").val(),
					}
					axios.post('?',vpost).then((response)=>{
						if(response.status == 200 ){
							if( response.data['status'] == "fail" ){
								if( response.data['details']['error_type'] == "server_errors"){
									this.Error = response.data['details']["duplicate_records"];
								}else if( response.data['details']['error_type'] == "dulipcates" ){
									this.Duplicate_Records = response.data['details']["duplicate_records"];
									this.vtype            = "duplicate";
									this.CurrentPage      = 1;
									this.Display_Records  = [];  
									this.get_display_record();
								}else{
									this.Error = response.data['details']["error"];
								}
							}else if( response.data['status'] == "fail" ){
								this.Errors = this.Records = this.Duplicate_Records = [];
								this.SucessMsg = "Bulk Upload Done Successfully."	
								this.show_overview   = false;
							}
						}else{
							this.Error = "There was an error while bulk upload: " + response.status;
						}
					})
					.catch(function () {
						console.log('FAILURE!!');
					});
				}
			}
		}
	}
});
app.component( "table_mongodb_edit_record", table_mongodb_edit_record );
app.component( "dbobject_table_mongodb", dbobject_table_mongodb );
app2 = app.mount("#app");
</script>