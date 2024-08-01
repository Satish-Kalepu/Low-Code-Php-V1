<script>
var objects_import = {
	"data": function(){
		return {
			"token": "",
			"vshow": true,
			"msg": "", "err": "", "msg2": "", "err2": "", "msg3": "", "err3": "", 
			"import_msg": "", "import_err": "",
			"upload_type": "CSV",
			"filedata": "nothing",
			"sample_records": [],
			"is_head": true,
			"head_record": [],
			"step": 1,
			"tot_cnt": 0,
			"sch_keys": {}, "sch_ikeys": {},
			"template_keys": {},
			"import_primary_field": "_default_id",
			"import_label_field": "",
			"import_alias_field": [],
			"add_table": {"table": "", "des": ""},
			"fields_match": {}, "keys_match": {},
			"sample_data": "",
			"upload_progress": 0,
			"upload_cnt": 0,"upload_success_cnt": 0,"upload_inserts_cnt": 0,"upload_updates_cnt": 0,"upload_skipped_cnt": 0, "upload_batch_cnt": 0,
			"upload_skipped_items": [],
			"csv_batch_limit": 500,
			"json_batch_limit": 100,
			"upload_create": false,
			"schema_1": {},
			"schema_2": {},
			"analyzing":false,
			"vimport": {
				"i_of": {"t": "GT", "i": "", "v": ""},
				"data": [
				],
				"template":{},
				"edit_field":"",
			},
			"create_instance": false,
			"new_collection": {
				"i_of": {"t":"GT", "i": "", "v": ""},
				"v": {"t":"T", "v":""},
			}
		};
	},
	props:[ "refname", "path", "app_id" ],
	mounted: function(){
		
	},
	"methods": {
		check_template: function(){
			try{
				var s = {"props": {} };
				s[ "_id" ] = {"type": "UniqId", "name": "Unique Key", "map": -1, "csvf": "", "targetf": "UniqId", "use": true, "m": true, "primary": true, "label": false};
				s[ "label" ] = {"type": "text", "name": "Label", "map": -1, "csvf": "", "targetf": "", "use": true, "m": true, "primary": false, "label": true};
				for( var fd in this.vimport['template'] ){
					s[ "props" ][ fd ] = {"type": "text", "name": this.vimport['template'][ fd ]['l']['v']+'', "map": -1, "csvf": "", "targetf": "", "use": true, "m": true, "primary": false, "label": false};
				}
				this.template_keys = s;
			}catch(e){
				console.error( "check_template" );
				console.error( e );
			}
		},
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
				if( this.sch_keys[vf]['use']== false ){
					if( this.import_label_field == vf ){
						this.import_label_field = "";
					}
					if( this.import_alias_field.indexOf(vf) > -1 ){
						this.import_alias_field.splice( this.import_alias_field.indexOf(vf), 1 );
					}
				}else{
					this.import_primary_field = "_default_id";
					this.import_primary_check2( vf );
				}
			}
		},
		import_primary_check: function(vf){
			setTimeout(this.import_primary_check2,100,vf);
		},
		import_primary_check2: function(vf){
			if( this.import_label_field == vf ){
				this.import_label_field = "";
			}
			if( this.import_alias_field.indexOf(vf) > -1 ){
				this.import_alias_field.splice( this.import_alias_field.indexOf(vf), 1 );
			}
			this.sch_keys[ vf ]['targetf'] = "";
			if( vf != "_default_id" && this.sch_keys['_default_id']['use'] ){
				this.sch_keys['_default_id']['use'] = false;
			}
		},
		import_label_check: function(vf){
			setTimeout(this.import_label_check2,100,vf);
		},
		import_label_check2: function(vf){
			//this.sch_keys[ vf ]['targetf'] = "";
		},
		import_alias_check: function(vf){
			
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
			if( this.vf.size > 1024*1024*5 ){
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
					"type": "UniqId", "map": -1, "csvf": "", "targetf": "UniqId", "use": true, "m": true, "i_of": {"i": "","v":""},
				}
				for( var vf in d ){
					this.fields_match[ d[vf] ] = vf;
					var k = d[vf].trim().replace(/\W/g,'');
					this.sch_keys[ k ] = {
						"type": "text", "map": vf, "csvf": d[vf], "targetf": "", "use": true, "m": true, "i_of": {"i": "","v":""},
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
			if( this.vimport['i_of']['v'] == "" ){
				this.err2 = "Need Instance Node"; return false;
			}
			if( this.upload_type == "CSV" ){
				if( this.import_primary_field.trim() == "" ){
					this.err2 = "Need ID (primary key) field"; return false;
				}else if( this.import_label_field.trim() == "" ){
					this.err2 = "Need Label Unique field"; return false;
				}
				for( var fd in this.sch_keys ){if( this.sch_keys[fd]['use'] ){
					if( fd != this.import_primary_field && fd != this.import_label_field ){
						if( this.sch_keys[ fd ]['type'] == "" ){
							this.err2 = "Field type required for `"+fd+"` "; return false;
						}
						if( this.sch_keys[ fd ]['targetf'] == "" ){
							this.err2 = "Field mapping required for `"+fd+"` "; return false;
						}
					}
					if( fd != this.import_primary_field ){
						if( this.sch_keys[ fd ]['type'] == "graph-thing" ){
							if( this.sch_keys[ fd ]['i_of']['v'] == "" ){
								this.err2 = "Field `"+fd+"` linkable instance name required"; return false;
							}
						}
					}
				}}

				var prime_index = -1;
				for( var fd in this.sch_keys ){
					if( fd == this.import_primary_field ){
						prime_index = this.sch_keys[ fd ]['map'];
					}
				}
				var label_index = -1;
				for( var fd in this.sch_keys ){
					if( fd == this.import_label_field ){
						label_index = this.sch_keys[ fd ]['map'];
					}
				}
				this.fpos = 0;
				var d = this.readcsvline();
				var prime_keys = {};
				var label_keys = {};
				var rcnt = 0;
				while( 1 ){
					var d = this.readcsvline();
					rcnt++;
					if( typeof(d) == "object" ){
						if( prime_index != -1 ){
							if( d.length > prime_index ){
								if( d[prime_index] in prime_keys == false ){
									prime_keys[ d[prime_index] ] = 1;
									if( d[prime_index].match(/^[a-z0-9]{5,24}$/) == null ){
										this.err2 = "Primary field value `" + d[prime_index] + "` format is not allowed for record: "+rcnt; return false;
									}
								}else{
									prime_keys[ d[prime_index] ]++;
									this.err2 = "Primary field value `" + d[prime_index] + "` repeated for record: "+rcnt; return false;
								}
							}
						}
						if( label_index != -1 ){
							if( d.length > label_index ){
								if( d[label_index] in label_keys == false ){
									label_keys[ d[label_index] ] = 1;
								}else{
									label_keys[ d[label_index] ]++;
									this.err2 = "Label value `" + d[label_index] + "` repeated for record: "+rcnt; return false;
								}
							}
						}
					}else if( d == "end" ){ break; }else{ this.err2 = "Failed to read file";return false; break; }
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
			this.upload_cnt = 0;this.upload_success_cnt = 0;this.upload_skipped_cnt = 0;this.upload_inserts_cnt = 0;this.upload_updates_cnt = 0;this.upload_skipped_items = [];
			this.err3 = "";
			this.msg3 = "Initiating...";
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
			this.start_importing_csv_batch();
		},
		discon: function(){
			axios.post( "?", {
				"action": "objects_import_data",
				"schema": this.sch_keys,
				"upload_type": this.upload_type,
				"token": this.token
			}).then(response=>{
				this.msg3 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
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
					var rec = {
						"l": {"t":"T", "v":""}, "props": {}, "al": []
					};
					var pfi = this.sch_keys[ this.import_primary_field ]['map'];
					if( pfi > -1 ){
						rec["_id"] = d[ Number(pfi) ];
					}
					var lfi = this.sch_keys[ this.import_label_field ]['map'];
					rec["l"] = {"t": "T", "v": d[ Number(lfi) ]};
					if( this.sch_keys[ this.import_label_field ]['type'] == "graph-thing" ){
						rec["l"]["t"] = "GT";
						rec['l']['i_of'] = this.sch_keys[ this.import_label_field ]['i_of'];
					}
					for(var ai=0;ai<this.import_alias_field.length;ai++){
						var afi = this.sch_keys[ this.import_alias_field[ai] ]['map'];
						if( afi > -1 ){
							if( typeof(d[ Number(afi) ])!="undefined" ){
								rec["al"].push({"t":"T","v": d[ Number(afi) ] });
							}
						}
					}
					for( var fi in this.sch_keys ){
						var fd = this.sch_keys[fi];
						var k = fd['targetf'];
						if( fd['map'] != "-1" && fd['use'] ){
							if( d[ Number(fd['map']) ] ){
								var v = d[ Number(fd['map']) ];
								if( v != undefined ){
									if( fd['type'] == "number" ){
										if( typeof(v) == "string" ){
											rec["props"][ k ] = {"t":"N", "v":Number(v)};
										}else if( typeof(v) == "number" ){
											rec["props"][ k ] = {"t":"N", "v":Number(v)};
										}else{
											rec["props"][ k ] = {"t":"N", "v":0};
										}
									}else if( fd['type'] == "graph-thing" ){
										rec["props"][ k ] = {"t":"GT", "i_of":fd['i_of'], "v":v};
									}else{
										rec["props"][ k ] = {"t":"T", "v":v};
									}
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
					"action": "object_import_batch",
					"object_id": this.vimport['i_of']['i'],
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
									this.upload_updates_cnt += response.data['updates'];
									this.upload_inserts_cnt += response.data['inserts'];
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
									this.upload_updates_cnt += response.data['updates'];
									this.upload_inserts_cnt += response.data['inserts'];
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
		},
		template_edit_popup_open: function(){
			this.$root.import_template_edit_popup_open( this.vimport['i_of']['i'], this.vimport['i_of']['v'] );
		},
		template_create_popup_open: function(){
			this.$root.import_template_create_popup_open();
		},
		callback__: function( c ){
			var x = c.split(/\:/g);
			if( x[0] == "import_select_iof" ){
				this.import_select_iof();
			}else{
				console.log("Callback: " + c + " not defined!");
			}
		},
		import_select_iof: function(){
			this.import_err = "";
			this.import_msg = "Loading template";
			axios.post("?", {
				"action": "objects_load_template",
				"object_id": this.vimport['i_of']['i']
			}).then( response=>{
				this.import_msg = "";
				if( typeof( response.data['data'] )=="object" ){
					this.vimport['template'] = response.data['data']['z_t'];
					this.import_msg = "Template loaded..";
					setTimeout(function(v){v.import_msg = "";},3000,this);
					this.check_template();
				}else{
					this.import_err = "Template not found";
				}
			}).catch(error=>{
				this.import_err = error.message;
			});
		},
		new_thing_created: function(vdata){
			this.vimport['i_of']['i'] = vdata['object_id'];
			this.vimport['i_of']['v'] = vdata['label'];
			this.import_select_iof();
		}
	},
	template: `<div>
			<div v-if="step==1" >
				<div>
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
			</div>
			<div v-if="step==2" >
				<div style="height:50px; " >
					<div v-if="analyzing" style="color:blue; float:right; margin-right: 20px;" >Analyzing file</div>
					<input v-else-if="tot_cnt<=20000" type="button" class="btn btn-outline-dark btn-sm" v-on:click="doimport" value="Next" style="float:right; margin-right: 10px;">
					<input type="button" class="btn btn-outline-dark btn-sm" v-on:click="cancel_step2" value="Cancel" style="float:right;margin-right: 10px;">

					<div style="display: flex; gap:20px;">
						<div>FileSize: {{ get_size() }}</div>
						<div>FileType: {{ upload_type }}</div>
						<div>
							<div>Preview: <span class="badge text-bg-light">{{ sample_records.length }}</span> of <span class="badge text-bg-light">{{ tot_cnt }}</span> records </div>
						</div>
						<div v-if="tot_cnt>20000" style="color:red;">File has more than 20,000 records. Not allowed.</div>
					</div>
				</div>
				<div style="overflow: auto;height: calc( 100% - 130px - 20px ); padding-right:10px;">
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
			</div>
			<div v-if="step==3" >
				<div>
					<input v-if="tot_cnt<=20000" type="button" class="btn btn-outline-dark btn-sm" v-on:click="doimport2" value="Next" style="float:right; margin-right: 10px;">
					<input type="button" class="btn btn-outline-dark btn-sm" v-on:click="step=2" value="Back" style="float:right; margin-right: 10px;">

					<div style="display: flex; gap:20px;">
						<div>FileSize: {{ get_size() }}</div>
						<div>FileType: {{ upload_type }}</div>
					</div>

					<div>&nbsp;</div>

					

					<div style="display:flex; column-gap:20px;">
						<div>
							<p>Import data into:</p>
						</div>
						<div>
							<div>Select Existing Instance</div>
							<div>
								<div class="code_line codeline_thing" >
									<div title="Thing" data-type="dropdown" v-bind:data-var="'ref:'+refname+':vimport:i_of:v'" data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" v-bind:data-context-callback="refname+':import_select_iof'" >{{ vimport['i_of']['v'] }}</div>
									<div v-if="vimport['i_of']['v']&&vimport['i_of']['i']" class="btn btn-link btn-sm" v-on:click="template_edit_popup_open()" >Edit Template</div>
									<div v-if="vimport['i_of']['v']&&vimport['i_of']['i']" class="btn btn-link btn-sm py-0" v-on:click="import_select_iof()" ><i class="fa-solid fa-arrows-rotate"></i></div>
								</div>
							</div>
						</div>
						<div>
							<div>New Instance</div>
							<div>
								<input type="button" class="btn btn-outline-dark btn-sm py-0" value="Create New Instance" v-on:click="template_create_popup_open()" >
							</div>
						</div>
					</div>

				</div>
				<div style="overflow: auto;height: calc( 100% - 200px ); ">

					<template v-if="upload_type=='CSV'" >
						<template v-if="vimport['i_of']['v']==''" >
							<p>Select Instance to proceed</p>
						</template>
						<template v-else>
							<div class="py-2">Map columns of CSV file to the Database Schema. Make sure primary field value should be in the format of UniqueId or Hexadecimal code</div>
							<table class="table table-bordered table-sm w-auto">
								<thead>
									<tr class="text-bg-light">
										<td>CSV Column</td>
										<td>=&gt;</td>
										<td align="center" width="50">ID</td>
										<td align="center" width="50">Label</td>
										<td align="center" width="50">Alias</td>
										<td>Import</td>
										<td>Object Property</td>
										<td>Type</td>
										<td>Link</td>
									</tr>
								</thead>
								<tbody>
									<tr v-for="fd,f in sch_keys" >
										<td>{{ fd['csvf'] }}</td>
										<td>=&gt;</td>
										<td align="center"><input type="radio" v-model="import_primary_field" v-bind:value="f" v-on:click="import_primary_check(f)" ></td>
										<td align="center"><input v-if="import_primary_field!=f&&f!='_default_id'" type="radio" v-model="import_label_field" v-bind:value="f" v-on:click="import_label_check(f)" ></td>
										<td align="center"><input v-if="import_label_field!=f&&f!='_default_id'" type="checkbox" v-model="import_alias_field" v-bind:value="f" v-on:click="import_alias_check(f)" ></td>
										<td><input v-if="f!='_default_id'" type="checkbox" v-model="fd['use']" v-on:click="use_check(f)" ></td>
										<td>
											<span v-if="f=='_default_id'&&import_primary_field==f" >Node Id</span>
											<template v-else-if="f!='_default_id'" >
												<select v-model="fd['targetf']" v-if="fd['use']" >
													<option value="" >-</option>
													<option v-for="tfd, tfi in template_keys['props']" v-bind:value="tfi" >{{ tfd['name'] }}</option>
												</select>
											</template>
										</td>
										<td>
											<select v-if="fd['use']&&f!='_default_id'" v-model="fd['type']" >
												<option value="text" >Text</option>
												<option value="number" >Number</option>
												<option value="graph-thing" >Graph Thing</option>
											</select>
											<span v-else-if="f=='_default_id'" >UniqId</span>
										</td>
										<td>
											<div v-if="fd['type']=='graph-thing'" class="code_line codeline_thing" >
												<div title="Thing" data-type="dropdown" v-bind:data-var="'ref:'+refname+':sch_keys:'+f+':i_of:v'" data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" >{{ fd['i_of']['v'] }}</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</template>
					</template>
					<template v-else-if="upload_type=='JSON'" >

						<div class="py-2">JSON Schema check</div>
						<pre class="fff">{{ schema_1 }}</pre>

					</template>
					<template>Unhandled upload type</template>

				</div>
			</div>
			<div v-if="step==4" >
				<div>
					<input type="button" class="btn btn-outline-dark btn-sm" v-on:click="step=3" value="Back" style="float:right;">
					<div style="display: flex; gap:20px;">
						<div>
							<div>FileSize: {{ get_size() }}</div>
							<div>FileType: {{ upload_type }}</div>
						</div>
					</div>
				</div>

				<div>Progress <span style="font-size:1.2rem;">{{ upload_progress }} %</span> </div>
				<div>Uploaded <span style="font-size:1.2rem;">{{ upload_cnt }}/{{ tot_cnt }}</span> </div>

				<div style="display:flex; column-gap:20px;">
					<div class="btn btn-light btn-sm">Success <span class="badge text-bg-primary" >{{ upload_success_cnt }}</span></div>
					<div class="btn btn-light btn-sm">Skipped <span class="badge text-bg-danger" >{{ upload_skipped_cnt }}</span></div>
					<div class="btn btn-light btn-sm">Inserts <span class="badge text-bg-info" >{{ upload_inserts_cnt }}</span></div>
					<div class="btn btn-light btn-sm">Updates <span class="badge text-bg-success" >{{ upload_updates_cnt }}</span></div>
				</div>

				<div  class="py-2" v-if="msg3" v-html="msg2" ></div>
				<div  class="py-2" v-if="err3" style="color:red;" >{{ err3 }}</div>

				<div v-if="uploaded_skipped_cnt>0" >
					<div>Skipped Items: </div>
					<div v-for="v in upload_skipped_items" >{{ v }}</div>
				</div>

			</div>
			<div style="height: 30px; padding-right:10px;" >
				<div v-if="import_msg" style="color:blue;" >{{ import_msg }}</div>
				<div v-if="import_err" style="float:right;color:red;" >{{ import_err }}</div>

				<div style="display:inline-block; color:blue;" v-if="msg2" v-html="msg2" ></div>
				<div style="display:inline-block; color:red;" v-if="err2" style="color:red;" v-html="err2" ></div>

			</div>
</div>`
};
</script>