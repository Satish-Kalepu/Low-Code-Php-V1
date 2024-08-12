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
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div style="float:right;"><a class="btn btn-outline-secondary btn-sm" v-bind:href="dbpath">Back</a></div>

			<h4>Table - <?=ucwords($table['table']) ?></h4>

			<ul class="nav nav-tabs mb-2">
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
				<div v-if="step==3||step==4" >
					<input type="button" class="btn btn-outline-dark btn-sm" v-on:click="cancel_step2" value="Cancel" style="float:right;">
					<input v-if="tot_cnt<=max_records_limit" type="button" class="btn btn-outline-dark btn-sm" v-on:click="doimport2" value="Proceed" style="float:right; margin-right: 10px;">

					<div style="display: flex; gap:20px;">
						<div>
							<div>FileSize: {{ get_size() }}</div>
							<div>FileType: {{ upload_type }}</div>
						</div>
						<div>
							<div><b>Schema Check</b></div>
						</div>
					</div>
				</div>
			</div>

			<div style="overflow: auto;height: calc( 100% - 130px - 50px - 30px ); padding-right:10px;">
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
					<template v-if="upload_type=='CSV'" >
						<div class="py-2">Map columns of CSV file to the Database Schema</div>

						<div class="row mb-2">
							<div  class="col-6" ><div v-if="msg2" v-html="msg2" ></div></div>
							<div  class="col-6" ><div v-if="err2" style="color:red;" >{{ err2 }}</div></div>
						</div>

						<table class="table table-bordered table-sm w-auto">
							<thead>
								<tr class="text-bg-light">
									<td>Table Field</td>
									<td>Type</td>
									<td>=</td>
									<td>CSV Column</td>
								</tr>
							</thead>
							<tbody>
								<tr v-for="fd,f in sch_keys" >
									<td>{{ f }}</td>
									<td>{{ fd['type'] }}<span v-if="f=='_id'" > Key</span></td>
									<td>=</td>
									<td>
										<select class="form-select form-select-sm w-auto" v-model="sch_keys[ f ]['map']" v-on:change="import2_check" >
											<option value="-1" >Not Mapped</option>
											<option v-for="hidx,hd in fields_match" v-bind:value="hidx+''" >{{ hd }}</option>
										</select>
									</td>
								</tr>
							</tbody>
						</table>

					</template>
					<template v-else-if="upload_type=='JSON'" >

						<div class="py-2">JSON Schema check</div>

						<table class="table table-bordered table-sm w-auto">
							<thead>
								<tr class="text-bg-light">
									<td>Table Field</td>
									<td>Type</td>
									<td>=</td>
									<td>JSON Field</td>
								</tr>
							</thead>
							<tbody>
								<tr v-for="fd,f in sch_keys" >
									<td>{{ f }}</td>
									<td>{{ fd['type'] }}<span v-if="f=='_id'" > Key</span></td>
									<td>=</td>
									<td>
										<span v-if="f in fields_match" >Found</span>
										<span v-else>Not found</span>
									</td>
								</tr>
							</tbody>
						</table>

					</template>
					<template>Unhandled upload type</template>
				</div>
				<div v-if="step==4" >
					<div class="row mb-2">
						<div class="col-6">Progress <span style="font-size:1.5rem;">{{ upload_progress }} %</span></div>
						<div class="col-6">Uploaded <span style="font-size:1.5rem;">{{ upload_cnt }}/{{ tot_cnt }}</span></div>
					</div>
					<div class="row mb-2">
						<div class="col-6">Success <span class="b-400" >{{ upload_success_cnt }}</span></div>
						<div class="col-6">Skipped <span class="bold" >{{ upload_skipped_cnt }}</span></div>
					</div>

					<div  class="py-2" v-if="msg3" v-html="msg2" ></div>
					<div  class="py-2" v-if="err3" style="color:red;" >{{ err3 }}</div>

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
			"dbpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tables_dynamic/<?=$config_param3 ?>/",
			"app_id": "<?=$config_param1 ?>",
			"table_id": "<?=$config_param3 ?>",
			"table": <?=json_encode($table,JSON_PRETTY_PRINT) ?>,
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
			"fields_match": {}, "keys_match": {},
			"sample_data": "",
			"upload_progress": 0,
			"upload_cnt": 0,"upload_success_cnt": 0,"upload_skipped_cnt": 0, "upload_batch_cnt": 0,
			"upload_skipped_items": [],
			"batch_limit": 500,
			"csv_batch_limit": 500,
			"json_batch_limit": 100,
			"upload_create": false,
			"new_table_id": "",
			"schema_1": {},
			"schema_2": {},
			"analyzing": false,
		};
	},
	mounted : function(){
		//this.load_source_tables();
		this.echo__( this.table );

		var sch_keys  = {};
		for(var sch in this.table['schema'] ){
			for( var fi in this.table['schema'][sch]['fields'] ){
				sch_keys[ fi ] = {
					"type": this.table['schema'][sch]['fields'][ fi ]['type'],
					"map": "-1"
				}
			}
		}
		this.echo__(sch_keys);
		var sch_ikeys = {};
		for(var idx in this.table['keys'] ){
			var k = Object.keys(this.table['keys'][ idx ]['keys']);
			for( var i=0;i<k.length;i++){
				sch_ikeys[ k[i] ] = 1;
			}
		}
		this.sch_keys = sch_keys;
		this.sch_ikeys = sch_ikeys;
	},
	methods: {
		echo__: function(v){
			if( typeof(v)=="object" ){
				console.log( JSON.stringify(v,null,4) );
			}else{
				console.log( v );
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
			var d = this.filedata.substr(this.fpos,2048);
			if( d.trim() == "" ){
				return "end";
			}
			var p = 0;
			var r = [];
			var cnt = 0;
			while( p<d.length-1 ){cnt++;if( cnt > 500 ){break;}
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
				var d = this.filedata.substr(this.fpos,1024);
				var line = d.split("\n")[0];
				var fields = line.split(",");
				if( fields.length < 3 ){
					alert("File is not in CSV Format");
					this.err = "File is not in CSV Format";
					//console.log( d );
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

						if( '_id' in this.fields_match == false ){
							this.fields_match = rec;
						}

					}
				}}
				if( i == 20 ){
					setTimeout(this.checkfile_json_continue,500);
				}else{
					this.analyzing = false;
				}
				this.echo__( this.schema_1 );
				this.sample_records = recs;
				this.step = 2;
			}
		},
		checkfile_json_continue: function(){
			while(1){
				if( this.fpos >= this.filedata.length-1 ){this.analyzing = false;break;}
				var ipos = this.filedata.indexOf("\n", this.fpos+1);
				if( ipos == -1 ){
					this.analyzing = false;
					console.log("File end not found");
					var j = this.filedata.substr(this.fpos,4096);
					if( j.trim() != "" ){
						try{
							var rec = JSON.parse(j);
							this.fpos+=j.length;
							this.tot_cnt++;
						}catch(e){
							console.log( j );
							console.log("last rec parse failed");
							this.analyzing = false;
						}
					}
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
			if( d === false ){ alert("Failed reading csv"); return false; }
			if( typeof(d) == "object"){
				this.head_record = d;
				this.fields_match = {};
				for(var vf in d ){
					this.fields_match[ d[vf] ] = vf;
				}
			}else{
 				alert("Failed reading csv " + d); return false; 
			}

			// this.fields_match = {};
			// this.keys_match = {};
			// for(var fi=0;fi<d.length;d++){
			// 	var field = d[fi];
			// 	if( field in this.sch_keys == false ){
			// 		this.fields_match[ field ] = false;
			// 	}else{
			// 		this.fields_match[ field ] = true;
			// 	}
			// 	this.keys_match[ field ] = true;
			// }

			var c_cnt = Object.keys(this.head_record).length;
			var r_cnt = 0;var or_cnt = c_cnt;var issue_cnt = 0;
			var recs = [];
			for(var i=0;i<100;i++){
				var d = this.readcsvline();
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
			console.log( i );
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
				}else if( d == "end" ){this.analyzing = false; break; }else{this.analyzing = false; this.err = d; break; }
			}
		},
		openbrowse: function(){
			document.getElementById("upload_file").click();
		},
		fileselect: function(){
			this.err = "";
			this.sample_data = "";
			var vf = document.getElementById("upload_file").files[0];
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
		import2_check: function(){
			this.err2 = "";
			this.msg2 = "";
			var cnt = 0;
			var matched = {};
			for( var vf in this.sch_keys ){
				if( this.sch_keys[ vf ]['map'] != "-1" ){
					if( this.sch_keys[ vf ]['map'] in matched ){
						this.err2 = "Same field `"+this.sch_keys[ vf ]['map']+"` is mapped multiple times";
					}else{
						if( vf != "_id" ){
							cnt++;
						}
						matched[ this.sch_keys[ vf ]['map'] ] = 1;
					}
				}
			}
			this.echo__(matched);
			if( Object.keys(this.sch_keys).length > cnt ){
				this.msg2 = "You have mapped <span class='badge bg-secondary' >" + cnt + "</span> fields out of <span class='badge bg-secondary' >" + Object.keys(this.sch_keys).length + "</span> fields of Table Schema";
			}
			if( cnt == 0 ){
				this.err2 = "at lease one key mapping is required for import";
			}
		},
		doimport2: function(){
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
			this.upload_cnt = 0;this.upload_success_cnt = 0;this.upload_skipped_cnt = 0;this.upload_skipped_items = [];
			this.err3 = "";
			this.msg3 = "Initiating...";
			this.fpos =0;
			axios.post("?", {
				"action":"get_token",
				"event":"tables_dynamic_import_batch."+this.app_id + "." + this.table['_id'],
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
										this.start_importing_csv_batch();
									}else if( this.upload_type == "JSON" ){
										this.start_importing_json_batch();
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
		start_importing_csv_batch: function(){
			var recs = [];
			for(var i=0;i<this.csv_batch_limit;i++){
				var d = this.readcsvline();
				if( typeof(d) == "object" ){
					var rec = {};
					for( fi in this.sch_keys ){
						var fd = this.sch_keys[fi];
						if( fd['map'] != "-1" ){
							if( d[ Number(fd['map']) ] ){
								var v = d[ Number(fd['map']) ];
								if( v != undefined ){
									rec[ fi ] = v;
								}
								if( fd['type'] == "number" ){
									if( typeof(v) == "string" ){
										rec[ fi ] = Number(v);
									}else if( typeof(v) == "number" ){
										rec[ fi ] = v;
									}else{
										rec[ $fi ] = 0;
									}
								}else{
									rec[ fi ] = v;
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
					"action": "tables_dynamic_import_batch",
					"data": recs,
					"token": this.token
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
									setTimeout(this.start_importing_batch,50);
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
		start_importing_json_batch: function(){
			var recs = [];
			for(var i=0;i<this.json_batch_limit;i++){
				console.log(": " + this.fpos + " < " + (this.filedata.length-1) );
				if( this.fpos < this.filedata.length-1 ){
					var ipos = this.filedata.indexOf("\n", this.fpos+1);
					if( ipos == -1 ){
						console.log("File end not found");
						var j = this.filedata.substr( this.fpos, 4096 );
						if( j.trim() != "" ){
							try{
								var rec = JSON.parse(j);
								recs.push(rec);
								this.fpos+=j.length;
							}catch(e){
								console.log( "File end not foubd" );
								console.log( j );console.log("File json parse failed: " + e);
								this.err3 = "File json parse failed: " + e;
							}
						}
						break;
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
					"action": "tables_dynamic_import_batch",
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
app.mount("#app");

</script>
