<style>

table.zz td div{ max-width:250px; max-height:75px;overflow:auto; white-space:nowrap; }
table.zz thead td { background-color:#666; color:white; }

div.zz::-webkit-scrollbar {width: 6px;height: 6px;}
div.zz::-webkit-scrollbar-track { background: #f1f1f1;}
div.zz::-webkit-scrollbar-thumb { background: #888;}
div.zz::-webkit-scrollbar-thumb:hover { background: #555;}

pre.zzz{ max-height:150px; width:auto;overflow:auto; margin:20px 10px; padding:10px; border:1px solid #999; }
pre.zzz::-webkit-scrollbar {width: 12px;height: 12px;}
pre.zzz::-webkit-scrollbar-track { background: #f1f1f1;}
pre.zzz::-webkit-scrollbar-thumb { background: #888;}
pre.zzz::-webkit-scrollbar-thumb:hover { background: #555;}

pre.fff{ max-height:300px; width:auto;overflow:auto; padding:10px; margin-right:20px; border:1px solid #999; }
pre.fff::-webkit-scrollbar {width: 12px;height: 12px;}
pre.fff::-webkit-scrollbar-track { background: #f1f1f1;}
pre.fff::-webkit-scrollbar-thumb { background: #888;}
pre.fff::-webkit-scrollbar-thumb:hover { background: #555;}

pre.sample_data{ height:300px;overflow:auto; white-space:nowrap; border:1px solid #333; }
pre.sample_data::-webkit-scrollbar {width: 6px;height: 6px;}
pre.sample_data::-webkit-scrollbar-track { background: #f1f1f1;}
pre.sample_data::-webkit-scrollbar-thumb { background: #888;}
pre.sample_data::-webkit-scrollbar-thumb:hover { background: #555;}

</style>
<div id="app" >
	<div class="leftbar" >
		<?php require( "page_apps_leftbar.php" ); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div style="float:right;"><a class="btn btn-outline-secondary btn-sm" v-bind:href="path+'tables_dynamic'">Back</a></div>

			<h4>Table - Create from File</h4>

			<div style="height: 50px; border-bottom: 1px solid #ccc; ">
				<div v-if="step==1" >
					<div class="mb-2" style="display:flex; gap:20px;" >
						<select class="form-select form-select-sm w-auto" v-model="upload_type" v-on:change="upload_type_select" >
							<option value="CSV" >CSV</option>
							<option value="JSON" >JSON</option>
							<option value="XLS" >XLS</option>
							<option value="XLSX" >XLSX</option>
						</select>
						<input type="file" id="upload_file" class="form-control form-control-sm w-auto" style="" v-on:change="fileselect" >
					</div>
					<div class="mb-2" v-if="err" >
						<div>Sample Data:</div>
						<pre v-if="sample_data" >{{ sample_data }}</pre>
					</div>
				</div>
				<div v-if="step==2" >
					<input type="button" class="btn btn-outline-dark btn-sm" v-on:click="cancel_step2" value="Cancel" style="float:right;">
					<div v-if="analyzing" style="color:blue; float:right; margin-right: 20px;" >Analyzing file</div>
					<input v-else-if="tot_cnt<=max_records_limit" type="button" class="btn btn-outline-dark btn-sm" v-on:click="doimport" value="Import" style="float:right; margin-right: 10px;">

					<div style="display: flex; gap:20px;">
						<div>
							<div>FileSize: {{ get_size() }}</div>
							<div>FileType: {{ upload_type }}</div>
						</div>
						<div>
							<div>Preview: <span class="badge text-bg-light">{{ sample_records.length }}</span> of <span class="badge text-bg-light">{{ tot_cnt }}</span> records </div>
						</div>
						<div v-if="tot_cnt>max_records_limit" style="color:red;">File has more than 20,000 records. Not allowed.</div>
					</div>
				</div>
				<div v-if="step==3" >
					<input type="button" class="btn btn-outline-dark btn-sm" v-on:click="cancel_step2" value="Cancel" style="float:right;">
					<input v-if="tot_cnt<=max_records_limit" type="button" class="btn btn-outline-dark btn-sm" v-on:click="doimport2" value="Proceed" style="float:right; margin-right: 10px;">

					<div style="display: flex; gap:20px;">
						<div>
							<div>FileSize: {{ get_size() }}</div>
							<div>FileType: {{ upload_type }}</div>
						</div>
						<div>
							<div>Preview: <span class="badge text-bg-light">{{ sample_records.length }}</span> of <span class="badge text-bg-light">{{ tot_cnt }}</span> records </div>
						</div>
					</div>
				</div>
				<div v-if="step==4" >
					<p>Upload Status</p>
				</div>
			</div>

			<div style="overflow: auto;height: calc( 100% - 130px - 20px ); padding-right:10px;">
				<div v-if="step==2" >
					<template v-if="upload_type=='CSV'" >
						<template v-if="sample_records.length>0" >
							<table class="table table-striped table-bordered table-sm w-auto zz" >
								<thead v-if="head_record" style="position:sticky; top:0px; ">
									<tr>
										<td v-for="f in head_record" ><div class="zz">{{ f }}</div></td>
									</tr>
								</thead>
								<tbody>
									<tr v-for="d in sample_records" >
										<td v-for="f in d" ><div class="zz">{{ f }}</div></td>
									</tr>
								</tbody>
							</table>
						</template>
					</template>
					<template v-if="upload_type=='JSON'" >
						<template v-if="sample_records.length>0" >
							<pre class="zzz" v-for="v in sample_records">{{ v }}</pre>
						</template>
					</template>
				</div>
				<div v-if="step==3" >

						<div class="row mb-2">
							<div  class="col-6" ><div v-if="msg2" v-html="msg2" ></div></div>
							<div  class="col-6" ><div v-if="err2" style="color:red;" >{{ err2 }}</div></div>
						</div>

						<div class="mb-2">
							<div>Table</div>
							<div><input type="text" class="form-control form-control-sm" v-model="add_table['table']" placeholder="New table name"></div>
						</div>
						<div class="mb-2">
							<div>Description</div>
							<div><textarea class="form-control form-control-sm" v-model="add_table['des']" placeholder="Description"></textarea></div>
						</div>

						<template v-if="upload_type=='CSV'" >

							<div class="py-2">Map columns of CSV file to the Database Schema</div>
							<table class="table table-bordered table-sm w-auto">
								<thead>
									<tr class="text-bg-light">
										<td>Import</td>
										<td>CSV Column</td>
										<td>=&gt;</td>
										<td>Table Field</td>
										<td>Type</td>
										<td>Mandatory</td>
										<td>PrimaryIndex</td>
									</tr>
								</thead>
								<tbody>
									<tr v-for="fd,f in sch_keys" >
										<td><input type="checkbox" v-model="fd['use']" v-on:click="use_check(f)" ></td>
										<td>{{ fd['csvf'] }}</td>
										<td>=&gt;</td>
										<td><input v-if="f!='_default_id'" type="text" v-model="fd['targetf']" v-if="fd['use']" >
											<span v-else>_id</span>
										</td>
										<td>
											<select v-if="fd['use']&&f!='_default_id'" v-model="fd['type']" >
											<option value="text" >Text</option>
											<option value="number" >Number</option>
											</select>
											<span v-else-if="fd['use']&&f=='_default_id'" >ObjectID</span>
										</td>
										<td><input v-if="fd['use']&&f!='_default_id'" type="checkbox" v-model="fd['m']" ></td>
										<td><input v-if="fd['use']" type="checkbox" v-model="fd['primary']" v-on:click="primary_check(f)" ></td>
									</tr>
								</tbody>
							</table>
						</template>
						<template v-else-if="upload_type=='JSON'" >

							<div class="py-2">JSON Schema check</div>
							<pre class="fff">{{ schema_1 }}</pre>

						</template>
						<template>Unhandled upload type</template>

				</div>
				<div v-if="step==4" >
					<div class="mb-2">
						<div style="height:30px; border:1px solid #ccc;">
							<div style="position:absolute;" > Progress <span style="font-size:1.2rem;">{{ upload_progress }} %</span> </div>
							<div v-bind:style="'height:30px; background-color:#ababd1; width:'+upload_progress+'%'" >&nbsp;</div>
						</div>
					</div>
					<div class="mb-2">Uploaded <span style="font-size:1.5rem;">{{ upload_cnt }}/{{ tot_cnt }}</span> </div>
					<div class="row mb-2">
						<div class="col-6">Success <span class="b-400" >{{ upload_success_cnt }}</span></div>
						<div class="col-6">Skipped <span class="bold" >{{ upload_skipped_cnt }}</span></div>
					</div>

					<div  class="py-2" v-if="msg3" v-html="msg2" ></div>
					<div  class="py-2" v-if="err3" style="color:red;" >{{ err3 }}</div>

					<div class="py-2">
						<a target="_blank" v-bind:href="path+'tables_dynamic/'+new_table_id+'/records'" >New Table: {{ new_table_id }}</a>
					</div>

					<div v-if="uploaded_skipped_cnt>0" >
						<div>Skipped Items: </div>
						<div v-for="v in upload_skipped_items" >{{ v }}</div>
					</div>

				</div>


			</div>
			<div style="height: 30px; padding-right:10px;" >
				<div v-if="msg" >{{ msg }}</div>
				<div v-if="err" style="float:right;color:red;" >{{ err }}</div>
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
			"app_id": "<?=$config_param1 ?>",
			"token": "",
			"vshow": true,
			"msg": "", "err": "", "msg2": "", "err2": "", "msg3": "", "err3": "", 
			"upload_type": "CSV",
			"filedata": "nothing",
			"sample_records": [],
			"is_head": true,
			"head_record": [],
			"step": 1,
			"tot_cnt": 0,
			"max_records_limit": 200000,
			"max_file_size_limit": 1024*1024*50,			
			"sch_keys": {}, "sch_ikeys": {},
			"add_table": {"table": "", "des": ""},
			"fields_match": {}, "keys_match": {},
			"sample_data": "",
			"upload_progress": 0,
			"upload_cnt": 0,"upload_success_cnt": 0,"upload_skipped_cnt": 0, "upload_batch_cnt": 0,
			"upload_skipped_items": [],
			"csv_batch_limit": 500,
			"json_batch_limit": 100,
			"upload_create": false,
			"new_table_id": "",
			"schema_1": {},
			"schema_2": {},
			"analyzing":false,
		};
	},
	mounted : function(){
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
		create_schema_from1: function( v ){
			this.echo__( v );
			var s = {};
			for( var fn in v ){
				if( typeof(v[fn]) == "object" && "length" in v[fn] ){
					s[ fn ] = {
						"name": fn,
						"key": fn,
						"type": "list",
						"m": true,
						"order": 1,
						"sub": [this.create_schema_from1( v[fn][0] )]
					}
				}else if( typeof(v[fn]) == "object" && "length" in v[fn] ){
					s[ fn ] = {
						"name": fn,
						"key": fn,
						"type": "list",
						"m": true,
						"order": 1,
						"sub": this.create_schema_from1( v[fn] )
					}
				}else{
					s[ fn ] = {
						"name": fn,
						"key": fn,
						"type": v[fn],
						"m": true,
						"order": 1,
						"sub": []
					}
				}
			}
			return s;
		},
		json_add_schema: function( v1, v2 ){
			if( v2 != null ){
			for( var fn in v2 ){
				if( v2[fn] != null ){
					if( typeof(v2[fn]) == "object" ){
						if( "length" in v2[fn] ){
							if( fn in v1 == false ){
								v1[ fn ] = [{}];
							}
							if( typeof(v2[fn][0]) == "object" && "length" in v2[fn][0] == false ){
								this.json_add_schema( v1[ fn ][ 0 ], v2[fn][0] );
							}
						}else{
							if( fn in v1 == false ){
								v1[ fn ] = {};
							}
							this.json_add_schema( v1[ fn ], v2[fn] );
						}
					}else{
						v1[ fn ] = typeof( v2[fn] );
					}
				}
			}
			}
		},
		json_add_schema2: function( v1, v2 ){
			if( v2 != null ){
			for( var fn in v2 ){
				if( v2[fn] != null ){
					if( typeof(v2[fn]) == "object" ){
						if( "length" in v2[fn] ){
							if( fn in v1 == false ){
								v1[ fn ] = {"key": fn+'', "type": "list", "sub": {} };
							}
							if( typeof(v2[fn][0]) == "object" && "length" in v2[fn][0] == false ){
								this.json_add_schema( v1[ fn ]['sub'], v2[fn][0] );
							}
						}else{
							if( fn in v1 == false ){
								v1[ fn ] = {"key": fn+'', "type": "dict", "sub": {} };
							}
							this.json_add_schema( v1[ fn ]['sub'], v2[fn] );
						}
					}else{
						v1[ fn ] = {"key": fn+'', "type": typeof( v2[fn] ) };
					}
				}
			}
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
		get_size: function(){
			var s = this.vf.size;
			if( s < 1024 ){ return s + " bytes";}
			if( s/1024 < 1024 ){ return (s/1024).toFixed(0) + " KB";}
			if( s/1024/1024 < 1024 ){ return (s/1024/1024).toFixed(2) + " MB";}
		},
		readcsvline: function(){
			if( this.fpos >= this.filedata.length ){
				return "end";
			}
			var d = this.filedata.substr(this.fpos,1024);
			if( d.trim() == "" ){
				return "end";
			}
			var p = 0;
			var r = [];
			var cnt = 0;
			while( p<d.length-1 ){cnt++;if( cnt > 1000 ){break;}
				//console.log("Pos:"+p);
				var dd = d.substr( p, 200 );
				//console.log( dd );
				if( dd.trim() == "" ){
					console.log(  (this.fpos +p) + " >= " + (this.filedata.length - 10) );
					if( this.fpos +p >= this.filedata.length - 10 ){
						r.push( "" );p++;this.fpos += p; return r;
					}
					break;
				}
				if( dd.substr(0,1) == "," ){
					r.push( "" );p++;continue;
				}
				if( dd.substr(0,1) == "\n" ){
					r.push( "" );p++;this.fpos += p; return r;
				}
				m1 = dd.match( /^\"([\S\s]+?)\"([\,\n])/ );
				if( m1 != null ){
					//console.log( m1[0] );
					var v = m1[1].replace(/[\r\n]/, " ").replace(/\"\"/g, "'");
					r.push( v );
					p+= m1[0].length;
					if( m1[2] == "\n" ){ this.fpos += p; return r; }
				}else{
					m1 = dd.match( /^([\S\s]+?)([\,\n])/ );
					if( m1 != null ){
						var v = m1[1].replace(/[\r\n]/, " ").replace(/\"\"/g, "'");
						r.push( v );
						p+= m1[0].length;
						if( m1[2] == "\n" ){ this.fpos += p; return r; }
					}else{
						console.log("Null");
						return "error";
					}
				}
			}
			if( this.fpos +p >= this.filedata.length - 10 ){
				r.push( "" );p++;this.fpos += p; return r;
			}
			console.log("Read line failed with max loops: " + cnt);
			return "Failed";
		},
		cancel_step2: function(){
			this.step = 1;
			this.filedata = "";
			this.vf = false;
			this.sample_data = "";
			this.sample_records = [];
			this.head_record = {};
			this.fields_match = {};
			this.tot_cnt = 0;
		},
		checkfile: function(){
			this.analyzing = true;
			if( this.upload_type == "CSV" ){
				var d = this.filedata.substr(0,1024);
				var line = d.split("\n")[0];
				//console.log( line );
				var fields = line.split(",");
				if( fields.length < 2 ){
					alert("File is not in CSV Format");
					this.err = "File is not in CSV Format";
					console.log( d );
					this.sample_data = d;
				}else{
					this.checkfile_csv();
				}
			}else if( this.upload_type == "JSON" ){
				this.checkfile_json();
			}else{
				alert("Unhandled file type");
			}
		},
		checkfile_json: function(){
			this.schema_1 = {};
			if( this.filedata.substr(0,1) == "[" && this.filedata.substr( this.filedata.length-1, 1) == "]" ){
				console.log("ys");
				try{
					this.sample_records = JSON.parse(this.filedata);
				}catch(e){
					alert("JSON file parsing failed");
					return false;
				}
			}else{
				var i = this.filedata.indexOf("}\r\n");
				var i2 = this.filedata.indexOf("}\n");
				if( i == -1 && i2 == -1 ){
					alert("JSON file is not in required format.");
					return false;
				}
				this.fpos = 0;
				var recs = [];
				this.tot_cnt = 0;
				for(var i=0;i<20;i++){if( this.fpos < this.filedata.length-1 ){
					var ipos = this.filedata.indexOf("\n", this.fpos+1);
					//console.log( this.fpos +  " : " + ipos );
					if( ipos == -1 ){
						this.err = "File end may not reached";
						break;
					}else{
						var l = ipos-this.fpos;
						console.log( l );
						var j = this.filedata.substr(this.fpos,l).trim();
						var rec = {};
						try{
							var rec = JSON.parse(j);
						}catch(e){
							console.log( j );
							console.log("File json parse failed: " + e);
							return;
						}
						recs.push(rec);
						this.tot_cnt++;
						this.fpos=ipos;						
						this.json_add_schema(this.schema_1, rec);
					}
				}}
				if( i == 20 ){
					setTimeout(this.checkfile_json_continue,500);
				}else{this.analyzing = false;}
				this.echo__( this.schema_1 );
				this.sample_records = recs;
				this.step = 2;
			}
		},
		use_check: function(vf){
			setTimeout(this.use_check2,100,vf);
		},
		use_check2: function(vf){
			if( vf == "_default_id" ){
				if( this.sch_keys["_default_id"]['use'] ){
					this.sch_keys["_default_id"]['primary'] = true;
					this.primary_check2("_default_id");
				}
			}
		},
		primary_check: function(vf){
			setTimeout(this.primary_check2,100,vf);
		},
		primary_check2: function(vf){
			if( this.sch_keys[ vf ]['primary'] ){
				this.sch_keys[ vf ]['targetf'] = "_id";
				this.sch_keys[ vf ]['m'] = true;
			}else{
				this.sch_keys[ vf ]['targetf'] = vf+'';
			}
			for( var i in this.sch_keys ){
				if( i != vf ){
					if( this.sch_keys[ i ]['targetf' ] == "_id" ){
						this.sch_keys[ i ]['primary' ] = false;
						this.sch_keys[ i ]['targetf' ] = i+'';
					}
					this.sch_keys[ i ]['primary'] = false;
					if( i == "_default_id" ){
						this.sch_keys[ i ]['use'] = false;
					}
				}
			}
		},
		checkfile_json_continue: function(){
			while(1){
				if( this.fpos >= this.filedata.length-1 ){this.analyzing = false;break;}
				var ipos = this.filedata.indexOf("\n", this.fpos+1);
				if( ipos == -1 ){
					this.analyzing = false;
					console.log("File end not found");
					break;
				}else{
					this.fpos=ipos;
					this.tot_cnt++;
				}
			}
		},
		checkfile_csv: function(){
			this.err = "";
			this.msg = "";

			this.fpos =0;
			this.tot_cnt = 0;
			if( this.vf.size > this.max_file_size_limit ){
				this.msg = "File size is more than 5 MB";
			}
			var d = this.readcsvline();
			//console.log( d );
			//return;
			if( d === false ){ alert("Failed reading csv"); return false; }
			if( typeof(d) == "object"){
				for( i in d ){
					d[i] = d[i].toLowerCase();
				}
				this.head_record = d;
				this.fields_match = {};
				this.sch_keys = {};
				this.sch_keys[ "_default_id" ] = {
					"type": "ObjectID", "map": -1, "csvf": "", "targetf": "_id", "use": true, "m": true, "primary": true,
				}
				for(var vf in d ){
					this.fields_match[ d[vf] ] = vf;
					var k = d[vf].trim().replace(/\W/g,'');
					this.sch_keys[ k ] = {
						"type": "text", "map": vf, "csvf": d[vf], "targetf": d[vf], "use": true, "m": true, "primary": false,
					}
				}
			}else{
 				alert("Failed reading csv " + d); return false; 
			}

			var c_cnt = Object.keys(this.head_record).length;
			var r_cnt = 0;var or_cnt = c_cnt;var issue_cnt = 0;
			var recs = [];
			for(var i=0;i<100;i++){
				var d = this.readcsvline();
				//console.log( d );
				if( typeof(d) == "object" ){
					recs.push(d);
					this.tot_cnt++;
				}else if( d == "end" ){ break; }else{ this.err = d; break; }
				var r_cnt = Object.keys(d).length;
				if( r_cnt != or_cnt || c_cnt != r_cnt ){issue_cnt++;}or_cnt = r_cnt+0;
				//break;
			}
			if( i == 100 ){
				setTimeout(this.checkfile_csv_continue,500);
			}else{this.analyzing = false;}
			this.sample_records = recs;
			this.step = 2;
			if( r_cnt != c_cnt ){ this.err = "Header and records column count not same."; }
			if( issue_cnt > 10 ){
				this.err = this.err  + " Column count inconsistent in " + issue_cnt + "% records";
			}else if( issue_cnt > 0 ){
				this.err = this.err + " Column count issues";
			}

			if( this.err == "" ){

			}
		},
		checkfile_csv_continue: function(){
			while( 1 ){
				var d = this.readcsvline();
				if( typeof(d) == "object" ){
					this.tot_cnt++;
				}else if( d == "end" ){ this.analyzing = false; break; }
				else{ this.analyzing = false; this.err = d; break; }
			}
		},
		openbrowse: function(){
			document.getElementById("upload_file").click();
		},
		fileselect: function(){
			this.err = "";
			this.sample_data = "";
			var vf = document.getElementById("upload_file").files[0];
			this.add_table['table'] = vf.name.replace(/\W/g, '').substr(0,25);
			this.add_table['des'] = vf.name.substr(0,250);
			console.log( vf );
			if( this.upload_type == "CSV" ){
				if( vf.name.match(/\.csv$/i) == null ){
					document.getElementById("upload_file").value = "";
					alert("Please select a file type .CSV");return false;
				}
			}else if( this.upload_type == "JSON" ){
				if( vf.name.match(/\.json$/i) == null ){
					document.getElementById("upload_file").value = "";
					alert("Please select a file type .JSON");return false;
				}
			}else{
				document.getElementById("upload_file").value = "";
				alert( this.upload_type + " not ready. please choose CSV/JSON");return false;
			}

			if( vf.size > (1024*1024*20) ){
				document.getElementById("upload_file").value = "";
				alert("Please select a file with a size less than 20MB and max 10000 records");return false;
			}

			this.vf = vf;
			this.fn = vf.name+'';

			this.fr = new FileReader();
			this.fr.vapp = this;
			this.fr.onload = (e) => {
				e.target.vapp.filedata = e.target.result;
				e.target.vapp.checkfile();
			}
			this.fr.readAsText(vf);
		},
		doimport: function(){
			this.step = 3;
			this.err2 = "";
			this.msg2 = "";
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
		doimport2: function(){
			this.err2 = "";
			this.add_table['table' ] = this.add_table['table' ].trim();
			if( this.add_table['table' ].match(/^[a-z0-9\.\-\_\ ]{3,25}$/i) == null ){
				//alert("Need table name in [a-z0-9.-_ ]{3,25}"); return false;
				this.err2 = "Need table name in [a-z0-9.-_ ]{3,25}"; return false;
			}
			if( this.add_table['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\ \r\n]{5,200}$/i) == null ){
				this.err2 = "Need description in [a-z0-9.-_&,!@]{5,200}"; return false;
			}

			if( this.upload_type == "CSV" ){
				var is_prime = false;
				var pc = 0;
				var prime_f = -1;var prime_ff = -1;
				for( var f in this.sch_keys ){if( this.sch_keys[f]['use'] ){
					if( this.sch_keys[f]['targetf'] == "_id" ){
						is_prime = true;
						prime_f = Number(this.sch_keys[f]['map']);
						prime_ff = f+'';
						pc++;
					}
				}}
				if( !is_prime ){
					this.err2 = "Need at least one primary field mapped to _id"; return false;
				}else if( pc > 1 ){
					this.err2 = "Only one field should be mapped to _id"; return false;
				}else if( prime_f == -1 && prime_ff != "_default_id" ){
					this.err2 = "Primary field not selected"; return false;
				}

				this.fpos =0;
				if( prime_f != -1 ){
					var keys = {};
					var rcnt = 0;
					while( 1 ){
						var d = this.readcsvline();
						rcnt++;
						if( typeof(d) == "object" ){
							if( d[prime_f] in keys == false ){
								keys[ d[prime_f] ] = 1;
							}else{
								keys[ d[prime_f] ]++;
								this.err2 = "Primary field value `" + d[prime_f] + "` repeated for record: "+rcnt; return false;
							}
						}else if( d == "end" ){ break; }else{ this.err2 = "Failed to read file";return false; break; }
					}
				}

			}

			if( this.err2 ){
				if( confirm("There was some errors in mapping.\n\nDo you still want to proceed?") ){
					this.start_importing();
				}
			}else{
				if( confirm("Have you verified data mappings?\nIncorrect mapping can lead to garbage collection\n\nDo you want to proceed?") ){
					this.start_importing();
				}
			}
		},
		start_importing: function(){

			this.step = 4;
			this.upload_progress = 0;
			this.upload_create = false;
			this.upload_cnt = 0;this.upload_success_cnt = 0;this.upload_skipped_cnt = 0;this.upload_skipped_items = [];
			this.err3 = "";
			this.msg3 = "Initiating...";
			this.new_table_id = "";
			this.fpos =0;
			axios.post("?", {
				"action":"get_token",
				"event":"tables_dynamic_importfile_batch."+this.app_id,
				"expire":10,
				"max_hits": 1000,
			}).then(response=>{
				this.msg3 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									if( this.upload_type == "CSV" ){
										var d = this.readcsvline();
										this.start_importing_csv_table();
									}else if( this.upload_type == "JSON" ){
										this.start_importing_json_table();
									}
								}
							}else{
								alert("Token error: " + response.dat['data']);
								this.err3 = "Token Error: " + response.data['data'];
							}
						}else{
							this.err3 = "Incorrect response";
						}
					}else{
						this.err3 = "Incorrect response Type";
					}
				}else{
					this.err3 = "Response Error: " . response.status;
				}
			});
		},
		start_importing_csv_table: function(){
			axios.post( "?", {
				"action": "tables_dynamic_importfile_create",
				"table": this.add_table,
				"schema": this.sch_keys,
				"upload_type": this.upload_type,
				"token": this.token
			}).then(response=>{
				this.msg3 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.new_table_id = response.data['inserted_id'];
								setTimeout(this.start_importing_csv_batch, 50);
							}else{
								this.err3 = "Import Error: " + response.data['error'];
							}
						}else{
							this.err3 = "Incorrect response";
						}
					}else{
						this.err3 = "Incorrect response Type";
					}
				}else{
					this.err3 = "Response Error: " . response.status;
				}
			});
		},
		start_importing_csv_batch: function(){
			var recs = [];
			for(var i=0;i<this.csv_batch_limit;i++){
				var d = this.readcsvline();
				if( typeof(d) == "object" ){
					var rec = {};
					for( fi in this.sch_keys ){
						var fd = this.sch_keys[fi];
						var k = fd['targetf'];
						if( fd['map'] != "-1" && fd['use'] ){
							if( d[ Number(fd['map']) ] ){
								var v = d[ Number(fd['map']) ];
								if( v != undefined ){
									rec[ k ] = v;
								}
								if( fd['type'] == "number" ){
									if( typeof(v) == "string" ){
										rec[ k ] = Number(v);
									}else if( typeof(v) == "number" ){
										rec[ k ] = v;
									}else{
										rec[ k ] = 0;
									}
								}else{
									rec[ k ] = v;
								}
							}
						}
					}
					recs.push( rec );
				}else if( d == "end" ){ break; }else{ this.err3 = d; break; }
			}
			if( recs.length ){
				this.upload_batch_cnt = recs.length;
				axios.post( "?", {
					"action": "tables_dynamic_importfile_batch",
					"table_id": this.new_table_id,
					"data": recs,
					"token": this.token,
					"upload_type": this.upload_type,
				}).then(response=>{
					this.msg3 = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.upload_cnt = Number(this.upload_cnt) + Number(this.upload_batch_cnt);
									this.upload_progress = ((this.upload_cnt/this.tot_cnt)*100).toFixed(1);
									this.upload_success_cnt += response.data['success'];
									this.upload_skipped_cnt += response.data['skipped'];
									for( var i in response.data['skipped_items'] ){
										this.upload_skipped_items.push( response.data['skipped_items'][ i ] );
									}
									setTimeout(this.start_importing_csv_batch,50);
								}else{
									this.err3 = "Import Error: " + response.data['error'];
								}
							}else{
								this.err3 = "Incorrect response";
							}
						}else{
							this.err3 = "Incorrect response Type";
						}
					}else{
						this.err3 = "Response Error: " . response.status;
					}
				});
			}
		},
		start_importing_json_table: function(){
			var schema = this.create_schema_from1( this.schema_1 );
			//this.echo__( schema );	return ;
			axios.post( "?", {
				"action": "tables_dynamic_importfile_create",
				"table": this.add_table,
				"schema": schema,
				"upload_type": this.upload_type,
				"token": this.token
			}).then(response=>{
				this.msg3 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.new_table_id = response.data['inserted_id'];
								setTimeout(this.start_importing_json_batch, 50);
							}else{
								this.err3 = "Import Error: " + response.data['error'];
							}
						}else{
							this.err3 = "Incorrect response";
						}
					}else{
						this.err3 = "Incorrect response Type";
					}
				}else{
					this.err3 = "Response Error: " . response.status;
				}
			});
		},
		start_importing_json_batch: function(){
			var recs = [];
			for(var i=0;i<this.json_batch_limit;i++){
				console.log(": " + this.fpos + " < " + (this.filedata.length-1) );
				if( this.fpos < this.filedata.length-1 ){
					var ipos = this.filedata.indexOf("\n", this.fpos+1);
					if( ipos == -1 ){
						console.log("File end not found");
						return;
					}else{
						var l = ipos-this.fpos;
						var j = this.filedata.substr(this.fpos,l).trim();
						console.log( j );
						var rec = {};
						try{var rec = JSON.parse(j);}
						catch(e){
							console.log( j );console.log("File json parse failed: " + e);
							this.err3 = "File json parse failed: " + e;
							return;
						}
						recs.push(rec);
						this.fpos=ipos;
					}
				}else{
					break;
				}
			}
			if( recs.length ){
				this.upload_batch_cnt = recs.length;
				axios.post( "?", {
					"action": "tables_dynamic_importfile_batch",
					"table_id": this.new_table_id,
					"data": recs,
					"token": this.token,
					"upload_type": this.upload_type,
				}).then(response=>{
					this.msg3 = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.upload_cnt = Number(this.upload_cnt) + Number(this.upload_batch_cnt);
									this.upload_progress = ((this.upload_cnt/this.tot_cnt)*100).toFixed(1);
									this.upload_success_cnt += response.data['success'];
									this.upload_skipped_cnt += response.data['skipped'];
									for( var i in response.data['skipped_items'] ){
										this.upload_skipped_items.push( response.data['skipped_items'][ i ] );
									}
									setTimeout(this.start_importing_json_batch,50);
								}else{
									this.err3 = "Import Error: " + response.data['error'];
								}
							}else{
								this.err3 = "Incorrect response";
							}
						}else{
							this.err3 = "Incorrect response Type";
						}
					}else{
						this.err3 = "Response Error: " . response.status;
					}
				});
			}
		}
	}
});

app.component( "table_dyanmic_object", table_dyanmic_object );

var obj = {
	data: function(){
		return {

		};
	},
	props:['items'],
	methods: {

	},
	template: `<ul>
		<li v-for="v,f in items" >
			<div>{{ f }}: {{ v['type'] }}</div>
			<obj v-if="v['type']=='dict'||v['type']=='list'" v-bind:items="v['sub']" ></obj>
		</li>
	</ul>`
}

app.component( "obj", obj );
app.mount("#app");

</script>

