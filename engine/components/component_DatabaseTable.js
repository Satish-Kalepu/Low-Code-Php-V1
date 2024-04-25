const app_DatabaseTable = {
	data: function(){
		return {
			"api_url": "",
			"options": {},
			"a":1, "init_error": "",
			"user": "",
			"pass": "", 
			"msg": "","err": "","cmsg": "","cerr": "",
			"captcha": "",
			"captcha_img": "",
			"captcha_code": "",
			"cap": false,
			"login": false,
			"session_key": "",
			"table_conf": {},

			"data_list"		: [],
			"selected_schema"	: "default",
			"search_index"		: "primary",
			"primary_search"	: {"cond":"=", "type":"text", "value":"", "value2":"", "sort":"asc"},
			"index_search"		: [],
			"av"			: false,
			"av2"			: false,
			"bv"			: false,
			"bv2"			: false,
			"limit"			: 200,
			"error"			: "",
			"show_add"		: false,
			"add_record"		: {},
			"add_record2"		: {},
			"new_record"		: {},
			"edit_status"		: "",
			"edit_record_json": "{}",
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
			"D_Rs"			: [],
			"show_delete"		: "",
			"filters"	  	: {"="	: "=","!=": "!=","<" : "<","<="	: "<=",">": ">",">=": ">=","><"	: "><","^." : "^..."},


			css: `
			.special{color:red;}
			table.zz td div{ max-width:250px; max-height:75px;overflow:auto; white-space:nowrap; }
			table.zz thead td { background-color:#666; color:white; }

			div.zz::-webkit-scrollbar {width: 6px;height: 6px;}
			div.zz::-webkit-scrollbar-track { background: #f1f1f1;}
			div.zz::-webkit-scrollbar-thumb { background: #888;}
			div.zz::-webkit-scrollbar-thumb:hover { background: #555;}

			td pre{margin-bottom: 0px;}
			`,
		};
	},
	props: ["dataappoptions"],
	watch:{
	  	D_Rs:function(){
			if( this.D_Rs.length > 0 ){
				this.show_delete = true;
			}else{
				this.show_delete = false;
			}
		}
	},
	mounted: function(){
		if( document.getElementById("css_block_DatabaseTable") == null ){
			var v= document.createElement("style");
			v.innerText = this.css;v.id="css_block_DatabaseTable";
			document.body.appendChild(v);
		}
		if( document.getElementById("edit_record_popup") == null ){
			document.body.appendChild( document.getElementById("edit_record_popup") );
		}
		if( "dataappoptions" in this){
			if( typeof(this["dataappoptions"]) == "string" ){
				this.options = JSON.parse(atob(this["dataappoptions"]));
			}else{
				this.init_error = "Incorrect configuration of TAG";return;
			}
		}else{
			this.init_error = "Tag configuration not found";return;
		}
		if( typeof(page_data) != "object" ){
			this.init_error = "page_data missing";return;
		}else if( "database_table_session_token" in page_data == false ){
			this.init_error = "session token missing";return;
		}else if( "global_api_url" in page_data == false ){
			this.init_error = "api url missing";return;
		}else{
			this.init_error = "";
			var x = this.options['table']['i'].split(":");
			if( x[0]  == "table_dynamic" ){ x[0] = "tables_dynamic";}
			if( x[0]  == "table" ){ x[0] = "tables";}
			this.api_url = page_data['global_api_url'] + x[0]+"/"+x[1];
			this.load_db_spec();
		}
	},
	methods: {

		echo__: function(v__){
			if( typeof(v__) == "object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
		Select_all:function(){
			if(this.selected_all == false){
				this.selected_all = true;
				this.show_delete = true;
				v = [];
				for(var i=0;i<this.data_list.length;i++){
					v.push( this.data_list[i]['_id'] );
				}
				this.D_Rs = v;
			}else{
				this.selected_all = false;
				this.show_delete = false;
				this.D_Rs =[];
			}
		},
		delete_record:function( vi ){
			this.delete_record_index = vi;
			if(confirm("Are you sure you want to delete")){
				var rec = this.data_list[ this.delete_record_index ];
				var vd__ = {
					"action"		: "deleteOne",
					"query"		: {"_id": rec['_id']},
				};
				axios.post( this.api_url, vd__, {"headers":{"Access-Key": page_data['database_table_session_token']}} ).then(response=>{
					if(response.data.hasOwnProperty("status")){
						var vdata = response.data;
						if(vdata['status'] == "success"){
							this.data_list.splice(this.delete_record_index,1);
							this.delete_record_index = -1;
						}else{
							this.error = vdata['data'];
						}
					}else{
						console.log("error");
						console.log(response.data);
					}
				});
			}
		},
		Delete_Record_Multi:function(){
			if( confirm("Are you sure to delete this Record") ){
				var vd__ = {
					"action"		: "deleteMany",
					"query"		: {"_id": {'$in': this.D_Rs}},
				};
				axios.post(this.api_url, vd__,{"headers":{"Access-Key": page_data['database_table_session_token']} } ).then(response=>{
					if( response.data.hasOwnProperty("status") ){
						var vdata = response.data;
						if( vdata['status'] == "success" ){
							this.search_filter_cond();
						}else{
							this.err = vdata['error'];
						}
					}else{
						console.log("error");
						console.log(response.data);
					}
				});
			}
		},
		get_type: function( v ){
			if( 'keys' in this.table_conf ){
				if( this.search_index in this.table_conf['keys'] ){
					if( v == "a" ){
						return this.table_conf['keys'][ this.search_index ]['pk']['type']+'';
					}else if( v == "b" ){
						return this.table_conf['keys'][ this.search_index ]['sk']['type']+'';
					}else{
						return "text";
					}
				}else{
					return "text";
				}
			}
		},
		toggle_edit_tab: function( v ){
			this.Edit_Tab = v+'';
		},
		change_index: function(){
			if( this.search_index == 'primary' ){
				//this.index_search = k;
			}else{
				if( this.search_index in this.table_conf['keys'] ){
					var kl = this.table_conf['keys_list'];
					this.echo__( kl );
					var k = [];
					for(var i=0;i<kl.length;i++){
						if( kl[i]['name'] == this.search_index ){
						for(var j=0;j<kl[i]['keys'].length;j++){
							k.push({
								"field": kl[i]['keys'][j]['name']+'',
								"type": kl[i]['keys'][j]['type']+'',
								"cond": "=",
								"value": "",
								"value2": "",
								"sort": "asc",
							});
						}
						this.index_search = k;
						}
					}
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
			this.err = "";
			var filters = {"=":'$eq',"!=":'$ne', "<":'$lt', "<=":'$lte', ">":'$gt', ">=":'$gte'};
			try{
				var cond = {};
				var options = {};
				if( this.search_index == "primary" ){
					var ac = this.primary_search["cond"];
					var av = this.primary_search["value"];
					if( this.primary_search["type"] == "number" && typeof(av) == "string" ){
						if( av.match(/^[0-9\.]+$/) == false ){ alert("Numeric required for search"); }
						av = Number(av);
					}
					if( av ){
						if( ac == "=" ){
							cond[ "_id" ] = av;
						}else if( ac == "><"){
							var av2 = this.primary_search["value2"];
							if( this.primary_search["type2"] == "number" && typeof(av2) == "string" ){
								if( av2.match(/^[0-9\.]+$/) == false ){ alert("Numeric required for search"); }
								av2 = Number(av2);
							}
							cond[ "_id" ] = {};
							cond[ "_id" ][ filters['>='] ] = av;
							cond[ "_id" ][ filters['<='] ] = av2;
						}else{
							cond[ "_id" ] = {};
							cond[ "_id" ][ filters[ ac ] ] = av;
						}
					}
					if( this.last_key ){
						if( this.primary_search['sort']=="desc" ){
							cond['_id'] = {'lt':this.last_key };
						}else{
							cond['_id'] = {'gt':this.last_key };
						}
					}
					s = {};
					s[ "_id" ] = (this.primary_search['sort']=="desc"?-1:1);
					options["sort"] = s; 
				}else{
					options["hint"] = this.search_index;
					s = {};
					this.echo__( this.index_search );
					for(var i in this.index_search ){
						var j = this.index_search[i];
						var af = j['field'];
						var ac = j["cond"];
						var av = j["value"];
						var t1 = j["type"];
						if( t1 == "number"  && typeof(av) == "string" ){
							if( av.match(/^[0-9\.]+$/) == false ){ alert("Numeric required for search"); }
							av = Number(av);
						}
						av2 = j["value2"];
						if( av ){
							if( ac == "=" ){
								cond[ af ] = av;
							}else if( ac == "><"){
								if( t1 == "number"  && typeof(av2) == "string" ){
									if( av2.match(/^[0-9\.]+$/) == false ){ alert("Numeric required for search"); }
									av2 = Number(av2);
								}
								cond[ af ] = {};
								cond[ af ][ filters['>='] ] = av;
								cond[ af ][ filters['<='] ] = av2;
							}else{
								cond[ af ] = {};
								cond[ af ][ filters[ ac ] ] = av;
							}
						}
						s[ af ] = (j['sort']=="desc"?-1:1);
					}
					options["sort"] = s;
				}
			}catch(e){
				this.err = "Error building condition: " + e;return;
			}
			axios.post( this.api_url, {
				"action": "findMany",
				"options": options,
				"query": cond,
			}, {"headers":{"Access-Key": page_data['database_table_session_token']} }).then(response=>{	
				this.msg = "";
				if("status" in response.data ){
					var vdata = response.data;
					if( vdata['status'] == "success" ){
						var r = vdata['data'];
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
			this.edit_record_json = JSON.stringify(this.data_list[ vid__ ],null,4).replace(/[\ ]{4}/g, "\t");
			this.edit_record_index = vid__;
			this.edit_mode = "edit";
			if( !this.show_add ){
				this.show_add = new bootstrap.Modal(document.getElementById('edit_record_popup'));
			}
			this.show_add.show();
		},
		add_record_now: function(){
			this.edit_record_index = -1;
			this.edit_mode = "new";
			var vfield__ =  JSON.parse(JSON.stringify(this.table_conf['schema'][ this.selected_schema ]['fields']));
			delete vfield__['_id'];
			this.edit_record_json = JSON.stringify(this.create_json_template(vfield__, {}),null,4).replace(/[\ ]{4}/g, "\t");
			if( !this.show_add ){
				this.show_add = new bootstrap.Modal(document.getElementById('edit_record_popup'));
			}
			this.show_add.show();
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
					d[field__+''] = this.create_json_template( vfields__[field__]['sub'] ,vdata__[field__] );
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
		validate_json: function(tf, jf, p = ""){
			for(var f in tf ){
				if( f in jf == false && tf[f]['m'] == true ){
					if( p == "" ){

					}else{
						this.error = "Field `" + p+f + "` is required!";
						return true;
					}
				}else if( f in jf ){
					var vt = typeof( jf[f] );
					if( vt == "string" ){ vt = "text";}
					if( vt == "number" ){ vt = "number";}
					if( vt == "object" && 'length' in jf[f] ){ vt = "list";}
					if( vt == "object" && 'length' in jf[f] == false ){ vt = "dict";}
					if( vt != tf[f]['type'] ){
						this.error = "Field `"+ p+f +"` should be " + tf[f]['type'];
						return true;
					}
					if( vt == "dict" ){
						if( this.validate_json( tf[f]['sub'], jf[f], p+f+"." ) ){
							return true;
						}
					}else if( vt == "list" ){
						for(var i=0;i<jf[f].length;i++){
							if( this.validate_json( tf[f]['sub'][0], jf[f][i], p+f+"["+i+"]." ) ){
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
			var v = this.edit_record_json+''
			v = v.replace(/\,[\r\n\ \t]*\}/g, "}");
			v = v.replace(/\,[\r\n\ \t]*\]/g, "]");
			try{
				record = JSON.parse( v );
			}catch(e){
				this.error = "Error in json: " + e
				return false;
			}
			if( 1 == 1 ){
				if( this.edit_record_index == -1 ){
					vpost_data = {
						"action"		: "insertOne",
						'data'		: record,
					};
				}else{
					var c = {"_id": record['_id']};
					var d = JSON.parse( JSON.stringify(record));
					delete(d['_id']);
					vpost_data = {
						"action": "updateOne",
						'update'	: {'$set': d },
						"query": c
					};
				}
				axios.post(this.api_url,vpost_data,{
					"headers":{"Access-Key": page_data['database_table_session_token']} 
				}).then(response=>{

					if( response.data.hasOwnProperty("status") ){
						vdata = response.data;
						if( vdata["status"] == "success" ){
							//this.show_add = false;
							this.edit_status = "Record Updated";
						}else if( vdata['data'] == "Record already Exists" ){
							this.error = response.data['data'];
							if( this.edit_mode == "new" ){
								this.permission_to_update = true;
							}
						}else{
							this.error = response.data['data'];
						}
					}else{
						console.log("error");
						console.log(response.data);
					}
				});
			}
		},
		ucwords( v ){
			if( v != '' ){
				var str = v.replace( /[\\~\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\\\|\;\:\"\'\,\.\/\<\>\?\t\r\n]+/g, " " );
				str = str.replace( /[\ ]{2,10}/g, " ");
				str = str.trim();
				return (str + '').replace(/^(.)|\s+(.)/g, function ($1){return $1.toUpperCase()})
			}
		},

		load_db_spec: function(){
			if( this.init_error ){
				alert(this.init_error);exit;
			}
			this.msg = "Loading schema ...";
			this.err = "";
			var x = this.options['table']['i'].split(":");
			if( x[0]  == "table_dynamic" ){ x[0] = "tables_dynamic";}
			if( x[0]  == "table" ){ x[0] = "tables";}
			axios.post( page_data['global_api_url'] + x[0]+"/"+x[1], {
				"action": "getSchema"
			}, {"headers":{"Access-Key": page_data['database_table_session_token']} }).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.table_conf = response.data['data'];
								this.msg = "Loading records...";
								this.change_index();
								this.load_records();
							}else{
								this.err = "Error: " + response.data['error'];
							}
						}else{
							this.err = "Error: incorrect response";
						}
					}else{
						this.err = "Error: unexpected response";
					}
				}else{
					this.err = "Error: http: " . response.status;
				}
			}).catch(error=>{
				this.msg = "";
				if( typeof(error.response.data)=="object" ){
					if( "error" in error.response.data ){
						this.err = error.response.data['error'];
					}else{
						this.err = "Unknown response: " + error.response.data;
					}
				}else{
					this.err = error.message;
				}
			});
		},
		get_captcha: function(){
			this.msg = "Loading capthca ...";
			this.cap_img = "";
			this.cerr = "";
			axios.post(page_data['global_api_url'] + "captcha/get", {
				"action":"captcha_get"
			}, {"headers":{"Access-Key": page_data['database_table_session_token']} }).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'img' in response.data && 'code' in response.data ){
							this.cap = true;
							this.captcha_img = response.data['img'];
							this.captcha_code = response.data['code'];
						}else{
							this.cerr = "Error loading captcha";
						}
					}else{
						this.cerr = "Error loading captcha";
					}
				}else{
					this.cerr = "Error loading captcha";
				}
			}).catch(error=>{
				this.msg = "";
				if( typeof(error.response.data)=="object" ){
					if( "error" in error.response.data ){
						this.cerr = error.response.data['error'];
					}else{
						this.cerr = "Unknown response: " + error.response.data;
					}
				}else{
					this.cerr = error.message;
				}
			});
		}
	},
	template: `<div style="border:1px solid #ccc;">
		<div class="alert alert-danger" v-if="init_error">
			<p>{{ init_error }}</p>
		</div>
		<div class="alert alert-danger"  v-if="err">{{ err }}</div>
		<div class="alert alert-success" v-if="msg">{{ msg }}</div>
		<div v-if="'schema' in table_conf" >

			<table>
				<tr>
				<td>
					<table>
						<tr>
							<td>
								<select v-model="search_index" class="form-select form-select-sm w-auto" style="width:150px;display:inline;" v-on:change="change_index">
									<option value="primary">Primary Index</option>
									<template v-if="'keys' in table_conf" >
									<option v-for="v,indexname in table_conf['keys']" v-bind:value="indexname">{{ indexname }}</option>
									</template>
								</select>
							</td>
							<td>
								<div v-if="search_index=='primary'">
									<table>
										<tr>
											<td><span style="padding: 0px 10px;" >_id </span></td>
											<td>
												<select v-model="primary_search['cond']" class="form-select form-select-sm w-auto" style="width:70px;display:inline;">
													<option v-for="f,i in filters" v-bind:value="i" >{{ f }}</option>
												</select>
											</td>
											<td>
												<template v-if="primary_search['cond']!='><'">
													<select v-model="primary_search['type1']" class="form-select form-select-sm w-auto" style="width:70px;display:inline;">
														<option value="text" >Text</option><option value="number" >Number</option>
													</select>
													<input type="text" autocomplete="off" v-model="primary_search['value']" placeholder="Search"  v-bind:class="{'form-control form-control-sm':true,'border-danger':av}"  style="width:150px;display:inline;" >
												</template>
												<template v-else>
													<select v-model="primary_search['type1']" class="form-select form-select-sm w-auto" style="width:70px;display:inline;">
														<option value="text" >Text</option><option value="number" >Number</option>
													</select>
													<input type="text" autocomplete="off" v-model="primary_search['value']" placeholder="From"  v-bind:class="{'form-control form-control-sm':true,'border-danger':av}"  style="width:80px;display:inline;" >
													<select v-model="primary_search['type2']" class="form-select form-select-sm w-auto" style="width:70px;display:inline;">
														<option value="text" >Text</option><option value="number" >Number</option>
													</select>
													<input type="text" autocomplete="off" v-model="primary_search['value2']" placeholder="To"  v-bind:class="{'form-control form-control-sm':true,'border-danger':av2}"  style="width:80px;display:inline;" >
												</template>
											</td>
											<td>
												<select v-model="primary_search['sort']" class="form-select form-select-sm w-auto" style="width:100px;display:inline;">
													<option value="asc" >Ascending</option>
													<option value="desc" >Descending</option>
												</select>
											</td>
										</tr>
									</table>
								</div>
								<template v-else-if="'keys' in table_conf" >
								<div v-if="search_index in table_conf['keys']">
									<table v-if="index_search.length>0">
										<tr v-for="kd,ki in index_search">
											<td><span style="padding: 0px 10px;" >{{ kd['field'].replace(".","->") }}</span></td>
											<td>
												<select v-model="kd['cond']" class="form-select form-select-sm w-auto" style="width:70px;display:inline;">
													<option v-for="f,i in filters" v-bind:value="i" >{{f}}</option>
												</select>
											</td>
											<td>
												<template v-if="kd['cond']!='><'">
													<input v-bind:type="kd['type']" autocomplete="off" v-model="kd['value']" placeholder="Search" v-bind:class="{'form-control form-control-sm':true,'border-danger':bv}"  style="width:150px;display:inline;" >
												</template>
												<template v-else>
													<input v-bind:type="kd['type']" autocomplete="off" v-model="kd['value']" placeholder="From" v-bind:class="{'form-control form-control-sm':true,'border-danger':bv}"  style="width:80px;display:inline;" >
													<input v-bind:type="kd['type']" autocomplete="off" v-model="kd['value2']" placeholder="To" v-bind:class="{'form-control form-control-sm':true,'border-danger':bv2}"  style="width:80px;display:inline;" >
												</template>
											</td>
											<td>
												<select v-model="kd['sort']" class="form-select form-select-sm w-auto" style="width:100px;display:inline;">
													<option value="asc" >Asc</option>
													<option value="desc" >Desc</option>
												</select>
											</td>
										</tr>
									</table>
								</div>
								</template>
							</td>
							<td>
								<button class="btn btn-sm btn-outline-dark" v-on:click="search_filter_cond">Search</button>
							</td>
						</tr>
					</table>
				</td>
				<td width="100">
					<select class="form-select form-select-sm w-auto" v-model="selected_schema" >
						<option v-for="vs,vi in table_conf['schema']" v-bind:value="vi" >{{ vs['name'] }}</option>
					</select>
				</td>
				<td width="100">
					<button class="btn btn-outline-dark btn-sm" v-on:click="add_record_now">Add Record</button>
				</td>
				</tr>
			</table>

			<div style="overflow: auto;height: 300px;resize:both;">	
				<table class="table table-hover table-bordered table-striped table-sm w-auto zz"  >
					<thead style="position: sticky;top:0px; background-color:#666;color:white;box-shadow: inset 0 1px 0 #aaa, inset 0 -1px 0 #aaa;">
						<tr>
							<td>
								<input type="checkbox" v-model="selected_all" v-on:click="Select_all">
							</td>
							<td></td>
							<td>
								<i class="fa fa-trash text-danger" v-on:click="Delete_Record_Multi" v-if="show_delete"></i>
							</td>
							<td  v-for="ff,fi in table_conf['schema'][selected_schema]['fields']"  >{{ ff['name'] }}</td>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dd,di in data_list" class="content" >
							<td>
								<input type="checkbox" v-bind:value="dd['_id']" v-model="D_Rs" >
							</td>
							<td>
								<i class="fa fa-edit text-success"  v-on:click="edit_record( di )" title="Edit"></i>
							</td>
							<td>
								<i class="fa fa-trash text-danger"  v-on:click="delete_record( di )" title="Delete"></i>
							</td>
							<td class="text-nowrap" v-for="ff,fi in table_conf['schema'][selected_schema]['fields']" v-on:dblclick="edit_record(di)" ><div class="zz">{{dd[ fi ]}}</div></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style="padding:5px 10px;">
				<button v-if="found_more" class="btn btn-outline-dark btn-sm float-end" v-on:click="load_more" >Load More</button>
				<div>Records: {{ total_cnt }} </div>
			</div>

		</div>

	<div class="modal fade" id="edit_record_popup" tabindex="-1" >
	  <div class="modal-dialog modal-xl modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">{{edit_mode == "new"?"Add Data":"Edit Data"}}</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      	<div style="overflow: auto; resize:both;" >
				<textarea spellcheck="false" class="form-control form-control-sm" v-model="edit_record_json" style="height: 300px;resize:both; font-size:1.1rem;"></textarea>
			</div>
	      </div>
	      <div class="modal-footer">
	      		<div style="display: flex; column-gap:5px;" >
					<div><button class="btn btn-sm btn-outline-dark" v-on:click="save_data()">SAVE</button></div>
					<div>
						<div v-if="permission_to_update" >Record already exists! Do you want to update? <button class="btn btn-sm btn-outline-dark mt-2" v-on:click="edit_mode='edit';permission_to_update=false;error=''">Yes</button> </div>
						<div v-if="error" class="alert alert-danger" style="padding:3px 10px;" >{{ error }}</div>
						<div v-if="edit_status" class="alert alert-success alert-sm" style="padding:3px 10px;" >{{ edit_status }}</div>
					</div>
				</div>
	      </div>
	    </div>
	  </div>
	</div>

		
	</div>`,

};