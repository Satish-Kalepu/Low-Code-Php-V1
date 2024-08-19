<script>
const graph_object =  {
	data(){
		return {
			"saveit": false,
			"tab": "home",
			"template_edit": -1,
			"new_field": "", "new_field_type": "T",
			"new_field_id": -1, 
			"edit_z_o": [],
			"edit_field": "", "new_field": "",
			"edit_label": false,
			"edit_label_v": {},
			"edit_al": false,
			"edit_al_v": [],
			"edit_type": false,
			"edit_type_v": {},
			"edit_i_of_v": {},
			"edit_i_of": false,
			"msg": "", "err": "",
			"records": [], 
			"records_last": "",
			"records_from": "",
			"records_cnt": 0,
			"records_start": 0,
			"records_end": 0,
			"records_current_page": 1,
			"records_pages": [],
			"z_t_msg": "", "z_t_err": "",
			"i_of_msg": "", "i_of_err": "",
			"label_msg": "", "label_err": "","al_msg": "", "al_err": "","type_msg": "", "type_err": "",
			"props_msg": "", "props_err": "",
			"delete_field_id": "",
			"data_types": {
				"T": "Text",
				"N": "Number",
				"B": "Boolean",
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
				"O": "Object",
				"L": "List",
				"GT": "Graph Node",
				"NL": "Null",
				"TT": "Text Multiline",
				"HT": "HTML Text",
			},
			"instance_type": {
				"N": "Node", "L": "DataSet", "D": "Document", "M": "Media"
			},
			"vedit": false,
		}
	},
	props: ['v', 'temp', 'datavar', 'vars', "edit_z_t", "refname" ],
	watch: {
		v: {
			handler: function(){
				//this.$emit("updated", this.v);
				console.log("updated");
				this.saveit = true;
			}, deep:true
		}
	},
	mounted: function(){
		if( typeof(this.v) != "object" || "length" in this.v ){
			this.v = {};
		}
		if( "i_of" in this.v == false || typeof(this.v['i_of']) != "object" || "length" in this.v['i_of'] ){
			this.v['i_of'] = {
				"t":"GT", 
				"v":{"t":"T","v":""},
				"i":{"t":"T","v":""}
			};
		}
		if( "p_of" in this.v == false || typeof(this.v['p_of']) != "object" || "length" in this.v['p_of'] == false ){
			this.v['p_of'] = [{
				"t":"GT", 
				"v":{"t":"T","v":""},
				"i":{"t":"T","v":""}
			}];
		}
		if( "al" in this.v == false || typeof(this.v['al']) != "object" || "length" in this.v['al'] == false ){
			this.v['al'] = [];
		}
		if( "props" in this.v == false ){
			this.v['props'] = {};
		}else if( typeof(this.v['props']) != "object" || "length" in this.v['props'] ){
			this.v['props'] = {};
		}
		if( "z_t" in this.v['i_of'] == false ){
			this.v['i_of']['z_t'] = {
				//"p1":{"l":{"t":"T","v":"Description"}, "t":{"t":"KV", "v":"Text", "k":"T"}, "e":false, "m":false},
			};
			this.v['i_of']['z_o'] = [];
			this.v['i_of']['z_n'] = 1;
		}else if( typeof(this.v['i_of']['z_t']) != "object" || "length" in this.v['i_of']['z_t'] ){
			this.v['i_of']['z_t'] = {
				//"p1":{"l":{"t":"T","v":"Description"}, "t":{"t":"KV", "v":"Text", "k":"T"}, "e":false, "m":false},
			};
			this.v['i_of']['z_o'] = [];
			this.v['i_of']['z_n'] = 1;
		}
	},
	methods: {
		echo__: function(v__){
			if( typeof(v__)=="object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
		create_field: function(){
			var f = this.temp['new_field']['v']+'';
			var v = JSON.parse(JSON.stringify( this.temp['new_value'] ));
			f = f.replace(/[\.]+/g, 'xDOTx');
			f = f.replace(/[\-]+/g, 'xHYPHENx');
			f = f.replace(/[\_]+/g, 'xUSCOREx');
			f = f.replace(/[\ ]+/g, 'xSPACEx');
			f = f.replace(/\W/g, '');
			f = f.replace(/xSPACEx/g, ' ');
			f = f.replace(/xUSCOREx/g, '_');
			f = f.replace(/xHYPHENx/g, '-');
			f = f.replace(/xDOTx/g, '.');
			//if( )
			for(var k in this.v['z_t']['v']){
				if( this.v['z_t']['v'][k]['v'].toLowerCase() == f.toLowerCase() ){
					alert("Field `" + f + "` already exists");
					return false;
				}
			}
			var nf = 'p1';var n = 1;
			while( nf in this.v['z_t']['v'] ){
				n++; nf = 'p'+n;
			}
			this.v['props']['v'][ nf ] = {'t':"L", "v": [v]};
			this.v['z_t']['v'][ nf ] = {'t':v['t']+'', 'v':f+''};
			if( 'z_t' in this.v['i_of']['v'][0] == false ){
				this.v['i_of']['v'][0]['z_t'] = {'t':"O", "v":{}};
			}
			this.v['i_of']['v'][0][ 'z_t' ]['v'][ nf ] = {'t':v['t']+'', 'v':f+''};
			this.$root.temp = {"new_field": {'t':"T",'v':""}, "new_value": {'t':"T",'v':""}};
			this.tab="home";
		},
		add_sub: function(vv,vk,vi){
			if( vk in this.v[vv] == false ){
				this.v[vv][ vk ] = [];
			}
			if( this.v[vv][ vk ].length>0 ){
				var v = JSON.parse(JSON.stringify( this.v[vv][ vk ][0] ));
			}else{
				var v = {'t':"T", "v":""};
			}
			this.v[vv][ vk ].push( v );
		},
		del_sub: function(vv,vk,vi){
			if( confirm("Are you sure?") ){
				this.v[vv][ vk ].splice(vi,1);
			}
		},
		add_i_of: function(){
			if( this.v['i_of']['v'].length < 4 ){
				this.v['i_of']['v'].push({
					"t":"GT", 
					"v":{"t":"T","v":""},
					"i":{"t":"T","v":""}
				});
			}else{
				alert("max tags reached");
			}
		},
		del_i_of: function(vi){
			if( this.v['i_of']['v'].lenth <= 1 ){
				alert("need at least one item");return false;
			}
			this.v['i_of']['v'].splice(vi,1);
		},
		add_field: function(){
			if( this.temp['new_field']['v'].trim() == "" ){
				alert("need field name");return;
			}
			if( this.temp['new_type']['v'].trim() == "" ){
				alert("need field type");return;
			}
			var np = "p1";
			for(var i=Number(this.v['i_of']['z_n']);i<999;i++){
				var np = "p"+i;
				if( np in this.edit_z_t == false ){
					this.new_field_id = i+1;
					break;
				}
			}
			this.new_field = np;
			this.save_new_field();
		},
		save_new_field: function(){
			this.z_t_msg = "";this.z_t_err = "";
			axios.post("?", {
				"action": "objects_object_add_field",
				"object_id": this.v['i_of']['i'],
				"field": this.new_field,
				"prop": {
					"l": JSON.parse(JSON.stringify(this.temp['new_field'])),
					"t": JSON.parse(JSON.stringify(this.temp['new_type'])),
					"e": false,
					"m": false
				},
				"z_n": this.new_field_id
			}).then(response=>{
				this.z_t_msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.close_edit_field();
								this.z_t_msg = "Saved";
								this.edit_z_t[ this.new_field ] = {
									"l": JSON.parse(JSON.stringify(this.temp['new_field'])),
									"t": JSON.parse(JSON.stringify(this.temp['new_type'])),
									"e": false,
									"m": false
								};
								this.edit_z_o.push( this.new_field );
								this.v['i_of']['z_t'] = JSON.parse(JSON.stringify(this.edit_z_t));
								this.v['i_of']['z_o'] = JSON.parse(JSON.stringify(this.edit_z_o));
								this.v['i_of']['z_n'] = this.new_field_id+0;
								this.$root.temp = {
									"new_field": {"t":"T", "v":""}, 
									"new_type":  {"t":"KV", "v":"Text", "k":"T"}, 
								};
								this.new_field='';
								setTimeout(function(v){v.z_t_msg = "";},3000,this);
							}else{
								this.z_t_err = response.data['error'];
							}
						}else{
							this.z_t_err = "Incorrect response";
						}
					}else{
						this.z_t_err = "Incorrect response";
					}
				}else{
					this.z_t_err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.z_t_err = error.message;
			});
		},
		open_template_edit: function(){
			this.edit_template = this.v['i_of']['i'];
			this.$root.edit_z_t = JSON.parse(JSON.stringify(this.v['i_of']['z_t']));
			this.edit_z_o = JSON.parse(JSON.stringify(this.v['i_of']['z_o']));
			this.tab="template";
		},
		open_records: function(){
			this.records_last = "";
			this.records_cnt = 0;
			//this.records_current_page = 1;
			//this.records_pages = [];
			this.open_records2();
		},
		open_records2: function(){
			this.records = [];
			this.tab='records';
			this.msg = "";this.err = "";
			var cond = {
				"action": "objects_load_records",
				"object_id": this.v['_id'],
			};
			if( this.records_from.trim() != "" ){
				cond['from'] = this.records_from.trim();
			}
			if( this.records_last.trim() != "" ){
				cond['last'] = this.records_last.trim();
			}
			axios.post("?", cond).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.records_cnt = response.data['cnt'];
								this.records = response.data['data'];
								this.records_last = this.records[ this.records.length-1 ]['l']['v']+'';
								//this.records_next = this.records[ this.records.length-1 ]['l.v']+'';
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response";
					}
				}else{
					this.err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.err = error.message;
			});
		},
		records_goto_next: function(){
			//this.records_last = this.records[ this.records.length-1 ]['l']['v'];
			this.open_records2();
		},
		next_page_exists: function(){

		},
		open_props_edit: function(){
			this.edit_template = this.v['i_of']['i'];
			console.log('333');
			console.log( JSON.parse(JSON.stringify(this.edit_z_t)) );
			console.log('444');
			console.log( JSON.parse(JSON.stringify(this.edit_z_o)) );
			this.v['i_of']['z_t'] = JSON.parse(JSON.stringify(this.edit_z_t));
			this.v['i_of']['z_o'] = JSON.parse(JSON.stringify(this.edit_z_o));
			this.tab="home";
		},
		close_edit_field: function(){
			this.edit_field = '';
			this.v['i_of']['z_t'] = JSON.parse(JSON.stringify(this.edit_z_t));
			this.v['i_of']['z_o'] = JSON.parse(JSON.stringify(this.edit_z_t));
		},
		save_field: function(){
			var v = this.edit_z_t[ this.edit_field ]['l']['v']+'';
			for(var fd in this.edit_z_t ){
				if( fd != this.edit_field && this.edit_z_t[ fd ]['l']['v'].toLowerCase() == v.toLowerCase() ){
					alert("Field with same name already exists");return false;
				}
			}
			this.save_z_t();
		},
		save_z_t: function(){
			this.msg = "";this.err = "";
			axios.post("?", {
				"action": "objects_save_object_z_t",
				"object_id": this.v['i_of']['i'],
				"field": this.edit_field,
				"prop": this.edit_z_t[ this.edit_field ],
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.close_edit_field();
								this.msg = "Saved";
								this.v['i_of']['z_t' ] = JSON.parse(JSON.stringify(this.edit_z_t));
								this.v['i_of']['z_o' ] = JSON.parse(JSON.stringify(this.edit_z_o));
								this.$root.temp = {
									"new_field": {"t":"T", "v":""}, 
									"new_type":  {"t":"KV", "v":"Text", "k":"T"}, 
								};
								setTimeout(function(v){v.this.thing_save_msg = "";},3000,this);
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response";
					}
				}else{
					this.err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.err = error.message;
			});
		},
		delete_field: function( vprop ){
			if( confirm("Are you sure?") == false ){return;}
			this.delete_field_id = vprop;
			this.msg = "";this.err = "";
			axios.post("?", {
				"action": "objects_delete_field",
				"object_id": this.v['i_of']['i'],
				"prop": vprop,
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg = "Field is Deleted";
								var i = this.v['i_of']['z_o' ].indexOf( this.delete_field_id );
								this.v['i_of']['z_o' ].splice( i, 1 );
								delete(this.v['i_of']['z_t' ][ this.delete_field_id ]);
								this.$root.edit_z_t = JSON.parse( JSON.stringify(this.v['i_of']['z_t']) );
								this.edit_z_o = JSON.parse( JSON.stringify(this.v['i_of']['z_o']) );
								setTimeout(function(v){v.thing_save_msg = "";},3000,this);
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response";
					}
				}else{
					this.err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.err = error.message;
			});
		},
		getlink: function(vi){
			//return this.$root.objectpath+'?object_id='+vi;
			this.$root.load_new_thing(vi);
		},
		moveup: function(vf){
			var i = this.edit_z_o.indexOf(vf);
			if( i > 0 ){
				var x = this.edit_z_o.splice(i,1);
				this.edit_z_o.splice(i-1,0,x[0]);
				this.save_order();
			}
		},
		movedown: function(vf){
			var i = this.edit_z_o.indexOf(vf);
			if( i < this.edit_z_o.length-1 ){
				var x = this.edit_z_o.splice(i,1);
				this.edit_z_o.splice(i+1,0,x[0]);
				this.save_order();
			}
		},
		save_order: function(){
			this.msg = "";this.err = "";
			axios.post("?", {
				"action": "objects_save_z_o",
				"object_id": this.v['i_of']['i'],
				"z_o": this.edit_z_o,
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg = "Updated";
								this.v['i_of']['z_o' ] = JSON.parse( JSON.stringify(this.edit_z_o) );
								setTimeout(function(v){v.thing_save_msg = "";},3000,this);
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response";
					}
				}else{
					this.err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.err = error.message;
			});
		},
		save_props: function(){
			for( var pv in this.v['props'] ){
				if( this.v['i_of']['z_t'][ pv ]['t']['k'] == "O" ){
					var propd = this.v['i_of']['z_t'][ pv ]['z'];
					for( var i=0;i<this.v['props'][ pv ].length;i++ ){
						var f = false;
						for( var fd in propd['z_t'] ){
							if( fd in this.v['props'][ pv ][i] ){
								if( this.v['props'][ pv ][i][ fd ]['v'].trim() != "" ){
									f= true;
								}
							}
						}
						if( f == false ){
							this.v['props'][ pv ].splice(i,1);
							i--;
						}
					}
				}else{
					for( var i=0;i<this.v['props'][ pv ].length;i++ ){
						if( this.v['props'][ pv ][i]['v'].trim() == "" ){
							this.v['props'][ pv ].splice(i,1);
							i--;
						}
					}
				}
			}
			this.props_msg = "Saving...";this.props_err = "";
			axios.post("?", {
				"action": "objects_save_props",
				"object_id": this.v['_id'],
				"props": this.v['props'],
			}).then(response=>{
				this.props_msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.props_msg = "Updated";
								this.vedit=false;
								setTimeout(function(v){v.props_msg = "";},3000,this);
							}else{
								this.props_err = response.data['error'];
							}
						}else{
							this.props_err = "Incorrect response";
						}
					}else{
						this.props_err = "Incorrect response";
					}
				}else{
					this.props_err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.props_msg = "";
				this.props_err = error.message;
			});
		},
		open_edit_label: function(){
			this.v['edit_label_v'] = JSON.parse( JSON.stringify(this.v['l']) );
			this.edit_label = true;
		},
		open_edit_type: function(){
			this.v['edit_type_v'] = JSON.parse( JSON.stringify(this.v['i_t']) );
			this.edit_type = true;
		},
		open_edit_al: function(){
			this.v['edit_al_v'] = JSON.parse( JSON.stringify(this.v['al']) );
			this.edit_al = true;
		},
		open_edit_i_of: function(){
			this.v['edit_i_of_v'] = JSON.parse( JSON.stringify(this.v['i_of']) );
			this.edit_i_of = true;
		},
		save_al: function(){
			for(var i=0;i<this.v['edit_al_v'].length;i++){
				if( this.v['edit_al_v'][i]['v'].trim()=="" ){
					this.v['edit_al_v'].splice(i,1);i--;
				}else if( this.v['edit_al_v'][i]['v'].toLowerCase() == this.v['l']['v'].toLowerCase() ){
					this.al_err = "Label and Alias should be different";
				}
			}
			this.al_msg = "Saving...";this.al_err = "";
			axios.post("?", {
				"action": "objects_edit_alias",
				"object_id": this.v['_id'],
				"alias": this.v['edit_al_v'],
			}).then(response=>{
				this.al_msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.al_msg = "Updated";
								this.v['al'] = JSON.parse( JSON.stringify(this.v['edit_al_v']));
								this.edit_al = false;
								setTimeout(function(v){v.al_msg = "";},3000,this);
							}else{
								this.al_err = response.data['error'];
							}
						}else{
							this.al_err = "Incorrect response";
						}
					}else{
						this.al_err = "Incorrect response";
					}
				}else{
					this.al_err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.al_msg = "";
				this.al_err = error.message;
			});
		},
		save_label: function(){
			this.label_msg = "Saving...";this.label_err = "";
			axios.post("?", {
				"action": "objects_edit_label",
				"object_id": this.v['_id'],
				"label": this.v['edit_label_v'],
			}).then(response=>{
				this.label_msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.label_msg = "Updated";
								this.v['l'] = JSON.parse( JSON.stringify(this.v['edit_label_v']));
								this.edit_label = false;
								setTimeout(function(v){v.label_msg = "";},3000,this);
							}else{
								this.label_err = response.data['error'];
							}
						}else{
							this.label_err = "Incorrect response";
						}
					}else{
						this.label_err = "Incorrect response";
					}
				}else{
					this.label_err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.label_msg = "";
				this.label_err = error.message;
			});
		},
		save_type: function(){
			this.type_msg = "Saving...";this.type_err = "";
			axios.post("?", {
				"action": "objects_edit_type",
				"object_id": this.v['_id'],
				"type": this.v['edit_type_v'],
			}).then(response=>{
				this.type_msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.type_msg = "Updated";
								this.v['i_t'] = JSON.parse( JSON.stringify(this.v['edit_type_v']));
								this.edit_type = false;
								setTimeout(function(v){v.type_msg = "";},3000,this);
							}else{
								this.type_err = response.data['error'];
							}
						}else{
							this.type_err = "Incorrect response";
						}
					}else{
						this.type_err = "Incorrect response";
					}
				}else{
					this.type_err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.type_msg = "";
				this.type_err = error.message;
			});
		},
		save_i_of: function(){
			this.i_of_msg = "Saving...";this.i_of_err = "";
			axios.post("?", {
				"action": "objects_edit_i_of",
				"object_id": this.v['_id'],
				"i_of": this.v['edit_i_of_v'],
			}).then(response=>{
				this.i_of_msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.i_of_msg = "Updated";
								this.v['i_of'] = JSON.parse( JSON.stringify(this.v['edit_i_of_v']));
								this.edit_i_of = false;
								setTimeout(function(v){v.i_of_msg = "";},3000,this);
							}else{
								this.i_of_err = response.data['error'];
							}
						}else{
							this.i_of_err = "Incorrect response";
						}
					}else{
						this.i_of_err = "Incorrect response";
					}
				}else{
					this.i_of_err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.i_of_msg = "";
				this.i_of_err = error.message;
			});
		},
		add_al: function(){
			this.v['edit_al_v'].push({"t":"T","v":""});
		},
		del_al: function(vi){
			if( confirm("Are you sure?") ){
				this.v['edit_al_v'].splice(vi,1);
			}
		},
		callback__: function( c ){
			var x = c.split(/\:/g);
			if( x[0] == "z_t_type" ){
				this.echo__( this.edit_z_t[ x[1] ] );
				if( this.edit_z_t[ x[1] ]['t']['k'] == "O" ){
					this.edit_z_t[ x[1] ]['z'] = {
						"z_t": {
							"p1": {
								"l": "Field 1",
								"t": "T"
							},
							"p2": {
								"l": "Field 2",
								"t": "T"
							}
						},
						"z_n":3,
						"z_o": ["p1", "p2"],
					}
				}else{
					delete(this.edit_z_t[ x[1] ]['z']);
				}
			}else{
				console.log("Callback: " + c + " not defined!");
			}
		},
		add_object_field: function(){
			this.edit_z_t[ this.edit_field ]['z']['z_n']++;
			var n = this.edit_z_t[ this.edit_field ]['z']['z_n']+0;
			var new_p = "p"+n;
			this.edit_z_t[ this.edit_field ]['z']['z_t'][ new_p ] = {
				"l": "Field "+n,
				"t": "T"
			};
			this.edit_z_t[ this.edit_field ]['z']['z_o'].push( new_p );
		},
		object_field_moveup: function(vf){
			var i = this.edit_z_t[ this.edit_field ]['z']['z_o'].indexOf(vf);
			if( i > 0 ){
				var x = this.edit_z_t[ this.edit_field ]['z']['z_o'].splice(i,1);
				this.edit_z_t[ this.edit_field ]['z']['z_o'].splice(i-1,0,x[0]);
			}
		},
		object_field_movedown: function(vf){
			var i = this.edit_z_t[ this.edit_field ]['z']['z_o'].indexOf(vf);
			if( i < this.edit_z_t[ this.edit_field ]['z']['z_o'].length-1 ){
				var x = this.edit_z_t[ this.edit_field ]['z']['z_o'].splice(i,1);
				this.edit_z_t[ this.edit_field ]['z']['z_o'].splice(i+1,0,x[0]);
			}
		},
		add_sub_object: function( propf ){
			var o = {};
			var propd = this.v['i_of']['z_t'][ propf ];
			for(var tdi in propd['z']['z_t']){
				o[ tdi ] = {"t":"T", "v":""};
			}
			this.v['props'][ propf ].push( o );
		},
		del_sub_object: function( propf, vi ){
			this.v['props'][ propf ].splice( vi,1 );
		},
		show_create2: function(){
			this.$root.show_create({'t':"GT", "i": this.v['i'],"v": this.v['l']['v']});
		}
	},
	template: `<div class="code_line">

		<table class="table table-bordered table-sm w-auto" >
			<tbody>
			<tr>
				<td>Label</td>
				<td>Type</td>
				<td>Alias</td>
				<td>Instance Of</td>
			</tr>
			<tr>
				<td>
					<div v-if="edit_label==false" style="display:flex; column-gap:20px;" >
						<div style="min-width:250px;" >
							<div v-if="v['l']['t']=='GT'" >
								<template v-if="'v' in v['l']&&'i' in v['l']" >
									<a href="#" v-on:click.prevent.stop="getlink(v['l']['i'])" style="font-size:1.2rem;" >{{ v['l']['v'] }}</a>
								</template>
								<div v-else>error object</div>
							</div>
							<div v-else style="font-size:1.2rem;" >{{ v['l']['v'] }}</div>
						</div>
						<div><div class="btn btn-outline-link btn-sm py-0" v-on:click="open_edit_label()" >&#9998;</div></div>
					</div>
					<div v-if="edit_label==true" style="min-width:250px;" >
							<p>
								<inputtextbox2 types="T,GT" v-bind:v="v['edit_label_v']" v-bind:datavar="datavar+':edit_label_v'" ></inputtextbox2>
							</p>
							<p>
								<div class="btn btn-outline-dark btn-sm  py-0 me-2" v-on:click="save_label()" >Save</div>
								<div class="btn btn-outline-secondary btn-sm  py-0" v-on:click="edit_label=false" >Cancel</div>
							</p>
							<div v-if="label_msg" style="color:blue; padding:5px; border:1px solid blue;" v-html="label_msg" ></div>
							<div v-if="label_err" style="color:red; padding:5px; border:1px solid red;" v-html="label_err" ></div>
					</div>
			</td>
			<td>
					<div style="display:flex; column-gap:20px;" >
						<div >
							<span v-if="v['i_t']['v'] in instance_type" >{{ instance_type[ v['i_t']['v'] ] }}</span>
							<span v-else>{{ v['i_t']['v'] }}</span>
						</div>
						<div><div class="btn btn-outline-link btn-sm  py-0" v-on:click="open_edit_type()" >&#9998;</div></div>
					</div>
					<div v-if="edit_type==true" style="position:absolute;background-color:white;border:1px solid #ccc;box-shadow:2px 2px 5px #666; " >
						<div style="padding:5px; background-color:#f0f0f0;">Instance Type</div>
						<div style="padding:5px;" >
							<div><label><input type="radio" v-model="v['edit_type_v']['v']" value="N" > Node (A thing/Person/Place etc)</label></div>
							<div><label><input type="radio" v-model="v['edit_type_v']['v']" value="L" > DataSet (Tabular Data)</label> </div>
							<div><label><input type="radio" v-model="v['edit_type_v']['v']" value="D" > Document (Article/Blog)</label> </div>
							<div class="mb-3"><label><input type="radio" v-model="v['edit_type_v']['v']" value="M" > Media (Image/Video)</label></div>
							<p>
								<div class="btn btn-outline-dark btn-sm  py-0 me-2" v-on:click="save_type()" >Save</div>
								<div class="btn btn-outline-secondary btn-sm  py-0" v-on:click="edit_type=false" >Cancel</div>
							</p>
							<div v-if="type_msg" style="color:blue; padding:5px; border:1px solid blue;" v-html="type_msg" ></div>
							<div v-if="type_err" style="color:red; padding:5px; border:1px solid red;" v-html="type_err" ></div>
						</div>
					</div>
			</td>
			<td>
					<div v-if="edit_al==false"  style="display:flex; column-gap:20px;" >
						<div>
							<div v-for="alv in v['al']">{{ alv['v'] }}</div>
						</div>
						<div><div class="btn btn-outline-link btn-sm  py-0" v-on:click="open_edit_al()" >&#9998;</div></div>
					</div>
					<div v-if="edit_al==true" >
							<div style="margin-bottom:10px;">
								<div v-for="alv,ali in v['edit_al_v']" style="display:flex; column-gap:5px; border-bottom:1px dashed #ccc; align-items:center;" >
									<div><input type="button" class="btn btn-outline-danger btn-sm py-0"  style="padding:0px;width:20px;" value="x" v-on:click="del_al(ali)" ></div>
									<inputtextbox2 types="T,GT" v-bind:v="alv" v-bind:datavar="datavar+':edit_al_v:'+ali" ></inputtextbox2>
								</div>
								<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_al(0)" ></div>
							</div>
							<div>
								<div class="btn btn-outline-dark btn-sm  py-0 me-2" v-on:click="save_al()" >Save</div>
								<div class="btn btn-outline-secondary btn-sm  py-0" v-on:click="edit_al=false" >Cancel</div>
							</div>

							<div v-if="al_msg" style="color:blue; padding:5px; border:1px solid blue;" v-html="al_msg" ></div>
							<div v-if="al_err" style="color:red; padding:5px; border:1px solid red;"   v-html="al_err" ></div>
					</div>
			</td>
			<td>
					<div v-if="edit_i_of==false"  style="display:flex; column-gap:20px;" >
						<div v-if="v['i_of']['t']=='GT'" >
							<template v-if="'v' in v['i_of']&&'i' in v['i_of']" >
								<a href="#" v-on:click.prevent.stop="getlink(v['i_of']['i'])" >{{ v['i_of']['v'] }}</a>
							</template>
							<div v-else>error object</div>
						</div>
						<div v-else>Error</div>
						<div><div class="btn btn-outline-link btn-sm  py-0" v-on:click="open_edit_i_of()" >&#9998;</div></div>
					</div>
					<div v-if="edit_i_of==true">
						<inputtextbox2 types="GT" v-bind:v="v['edit_i_of_v']" v-bind:datavar="datavar+':edit_i_of_v'" ></inputtextbox2>
						<div class="mt-2"><div class="btn btn-outline-dark btn-sm  py-0 me-2" v-on:click="save_i_of()" >Save</div><div class="btn btn-outline-secondary btn-sm  py-0" v-on:click="edit_i_of=false" >Cancel</div></div>
						<div v-if="i_of_msg" style="color:blue; padding:5px; border:1px solid blue;" v-html="i_of_msg" ></div>
						<div v-if="i_of_err" style="color:red;  padding:5px; border:1px solid red;" v-html="i_of_err" ></div>
					</div>
			</td>
			</tr>
		</tbody>
		</table>
			<!--<tr>
				<td>Part Of</td>
				<td>
					<template v-if="'p_of' in v" >
						<div v-if="'p_of' in v" class="codeline_thing" style="display:flex;column-gap:10px;" >
							<div>
								<div v-if="typeof(v['p_of']['v'])!=undefined" title="Thing" data-type="dropdown" v-bind:data-var="datavar+':p_of:v'"  data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" >{{ v['p_of']['v']['l']['v'] }}</div>
								<div v-else>v['p_of']['v'] Undefined</div>
							</div>
						</div>
					</template>
					<div>Undefined</div>
				</td>
			</tr>-->

		<template v-if="'props' in v" >
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a v-bind:class="{'nav-link py-0':true, 'active':tab=='home'}" href="#" v-on:click.prevent.stop="open_props_edit()">Properties</a>
			</li>
			<li class="nav-item">
				<a v-bind:class="{'nav-link py-0':true, 'active':tab=='template'}"  href="#" v-on:click.prevent.stop="open_template_edit()">Template</a>
			</li>
			<li class="nav-item" v-if="'cnt' in v">
				<a v-if="v['cnt']>0" v-bind:class="{'nav-link py-0':true, 'active':tab=='records'}"  href="#" v-on:click.prevent.stop="open_records()">Records <span class="badge bg-secondary" >{{ v['cnt'] }}</span> </a>
			</li>
		</ul>
		<template v-if="vedit==false&&tab=='home'" >
			<div style="padding:10px;"><div class="btn btn-outline-dark btn-sm" v-on:click="vedit=true" >&#9998;</div></div>
			<div v-if="props_msg" style="color:blue; padding:5px; border:1px solid blue;" v-html="props_msg" ></div>
			<div v-if="props_err" style="color:red;  padding:5px; border:1px solid red;"  v-html="props_err" ></div>

			<table v-if="'z_o' in v['i_of']" class="table table-bordered table-sm w-auto" >
				<tbody>
				<tr v-for="propf in v['i_of']['z_o']" valign="top">
					<td nowrap>
						<span v-if="propf in v['i_of']['z_t']" >{{ v['i_of']['z_t'][ propf ]['l']['v'] }}</span>
						<span v-else >{{ propf }}</span>
					</td>
					<td>
						<div v-if="propf in v['props']==false" >-</div>
						<div v-else-if="typeof(v['props'][ propf ])!='object'&&'length' in v['props'][ propf ]==false" >-</div>
						<div v-else-if="v['props'][ propf ].length>0" >
							<template v-if="v['i_of']['z_t'][ propf ]['t']['k']=='O'" >
								<table class="table table-bordered table-striped table-sm w-auto customborder2" >
									<tbody>
										<tr>
											<td v-for="tdv in v['i_of']['z_t'][ propf ]['z']['z_o']" >{{ v['i_of']['z_t'][ propf ]['z']['z_t'][ tdv ]['l'] }}</td>
										</tr>
										<tr v-for="pvv,pii in v['props'][ propf ]" >
											<td v-for="tdv in v['i_of']['z_t'][ propf ]['z']['z_o']" >
												<template v-if="tdv in pvv" >
													<inputtextview v-if="'t' in pvv[tdv]&&'v' in pvv[tdv]" v-bind:v="pvv[ tdv ]" ></inputtextview>
												</template>
											</td>
										</tr>
									</tbody>
								</table>
							</template>
							<template v-else >
								<div v-for="pvv,pii in v['props'][ propf ]" >
									<inputtextview v-bind:v="pvv" v-bind:datavar="datavar+':props:'+propf+':'+pii" ></inputtextview>
								</div>
							</template>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			<div v-if="edit_z_o.length>5"><div class="btn btn-outline-dark btn-sm" v-on:click="vedit=true" >&#9998;</div></div>
		</template>
		<template v-if="vedit==true&&tab=='home'&&'z_o' in v['i_of']" >
			<div style="padding:10px;">
				<div class="btn btn-outline-dark btn-sm me-2"  v-on:click="save_props()" >Save</div>
				<div class="btn btn-outline-secondary btn-sm"  v-on:click="vedit=false" >Cancel</div>
			</div>

			<div v-if="props_msg" style="color:blue; padding:5px; border:1px solid blue;" v-html="props_msg" ></div>
			<div v-if="props_err" style="color:red;  padding:5px; border:1px solid red;"  v-html="props_err" ></div>

			<table class="table table-bordered table-sm w-auto customborder2" >
				<tbody>
				<tr v-for="propf in v['i_of']['z_o']" valign="top">
					<td>
						<span v-if="propf in v['i_of']['z_t']" >{{ v['i_of']['z_t'][ propf ]['l']['v'] }}</span>
						<span v-else >{{ propf }}</span>
					</td>
					<td>
						<template v-if="v['i_of']['z_t'][ propf ]['t']['k']=='O'" >
							<table class="table table-bordered table-striped table-sm w-auto customborder2" >
								<tbody>
									<tr>
										<td>-</td>
										<td v-for="tdv in v['i_of']['z_t'][ propf ]['z']['z_o']" >{{ v['i_of']['z_t'][ propf ]['z']['z_t'][ tdv ]['l'] }}</td>
									</tr>
									<tr v-for="pvv,pii in v['props'][ propf ]" >
										<td><input type="button" class="btn btn-outline-danger btn-sm py-0" style="padding:0px;width:20px;" value="X" v-on:click="del_sub_object(propf,pii)" ></td>
										<td v-for="tdv in v['i_of']['z_t'][ propf ]['z']['z_o']" >
											<template v-if="tdv in pvv" >
												<inputtextbox2 types="T,GT" linkable="true" v-bind:v="pvv[ tdv ]" v-bind:datavar="datavar+':props:'+propf+':'+pii+':'+tdv" ></inputtextbox2>
											</template>
										</td>
									</tr>
								</tbody>
							</table>
							<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_sub_object(propf,0)" ></div>
						</template>
						<template v-else >
							<div v-if="propf in v['props']==false" >
								<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_sub('props',propf,0)" ></div>
							</div>
							<div v-else >
								<div v-for="pvv,pii in v['props'][ propf ]" style="display:flex; column-gap:5px; border-bottom:1px dashed #ccc; align-items:center;" >
									<div><input type="button" class="btn btn-outline-danger btn-sm py-0"  style="padding:0px;width:20px;" value="x" v-on:click="del_sub('props',propf,pii)" ></div>
									<inputtextbox linkable="true" v-bind:v="pvv" v-bind:datavar="datavar+':props:'+propf+':'+pii" ></inputtextbox>
								</div>
								<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+"  style="padding:0px;width:20px;" v-on:click="add_sub('props',propf,pii)" ></div>
							</div>
						</template>
					</td>
				</tr>
				<tr valign="top">
					<td>
						<div class="btn btn-outline-dark btn-sm py-0" v-on:click="open_template_edit()" style="" >+</div>
					</td>
					<td>
						
					</td>
				</tr>
				</tbody>
			</table>
			<div v-if="v['i_of']['z_o'].length>5">
				<div style="padding:10px;" ><div class="btn btn-outline-dark btn-sm me-2" v-on:click="save_props()" >Save</div><div class="btn btn-outline-secondary btn-sm"  v-on:click="vedit=false" >Cancel</div></div>
				<div v-if="props_msg" style="color:blue; padding:5px; border:1px solid blue;" v-html="props_msg" ></div>
				<div v-if="props_err" style="color:red;  padding:5px; border:1px solid red;"  v-html="props_err" ></div>
			</div>
		</template>
		<template v-if="tab=='template'" >
			<div style="padding:10px 0px; background-color:#f8f8f8;">Properties of {{ v['i_of']['v'] }}</div>
			<table class="table table-bordered table-sm w-auto" >
				<tbody>
				<template v-for="propf,fi in edit_z_o" >
				<tr>
					<td><div>{{ propf }}</div></td>
					<td>
						<div v-if="propf in edit_z_t" >
							<div v-if="edit_field==propf" >
								<inputtextbox2 types="T,GT" v-bind:v="edit_z_t[ propf ]['l']" v-bind:datavar="'edit_z_t:'+propf+':l'" ></inputtextbox2>
							</div>
							<div v-else-if="edit_z_t[ propf ]['l']['t']=='T'">{{ edit_z_t[ propf ]['l']['v'] }}</div>
							<div v-else-if="edit_z_t[ propf ]['l']['t']=='GT'"><a href="#" v-on:click.prevent.stop="getlink(edit_z_t[ propf ]['l']['i'])" >{{ edit_z_t[ propf ]['l']['v'] }}</a></div>
						</div>
					</td>
					<td>
						<div v-if="propf in edit_z_t" >
							<div v-if="edit_field==propf" title="DataType" data-type="dropdown" v-bind:data-var="'edit_z_t:'+propf+':t'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" v-bind:data-context-callback="refname+':z_t_type:'+propf" >{{ edit_z_t[propf]['t']['v'] }}</div>
							<div v-else>{{ edit_z_t[ propf ]['t']['v'] }}</div>
						</div>
					</td>
					<td v-if="edit_field==''">
						<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="&#9998;" v-on:click="edit_field=propf+''" ></div>
					</td>
					<td v-if="edit_field&&propf==edit_field">
						<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="Save" v-on:click="save_field()" ></div>
					</td>
					<td v-if="edit_field&&propf==edit_field">
						<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="Cancel" v-on:click="edit_field=''" ></div>
					</td>
					<td v-if="edit_field==''">
						<div><input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="delete_field(propf)" ></div>
					</td>
					<td v-if="edit_field==''">
						<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&uarr;" v-on:click="moveup(propf)" ></div>
					</td>
					<td v-if="edit_field==''">
						<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&darr;" v-on:click="movedown(propf)" ></div>
					</td>
				</tr>
				<tr v-if="edit_z_t[ propf ]['t']['k']=='O'" >
					<td colspan="9" >
						<div style="margin-left:20px; border-left:1px dashed #ccc; padding:0px 10px; " >
							<div style="padding:5px 0px;">Object Template</div>
							<div  v-if="edit_field&&propf==edit_field" >
							<table class="table table-bordered table-striped table-sm w-auto">
								<tbody>
									<tr>
										<td>#</td>
										<td>Property</td>
										<td>Type</td>
										<td>-</td><td>-</td>
									</tr>
									<tr v-for="tvp,ti in edit_z_t[ propf ]['z']['z_o']" >
										<td>{{ tvp }}</td>
										<td><input type="text" v-model="edit_z_t[ propf ]['z']['z_t'][ tvp ]['l']" class="form-control form-control-sm" ></td>
										<td><select v-model="edit_z_t[ propf ]['z']['z_t'][ tvp ]['t']" class="form-select form-select-sm" >
											<option value="T" >Text</option>
											<option value="GT" >Thing Link</option>
											<option value="N" >Number</option>
											<option value="D" >Date</option>
										</select></td>
										<td>
											<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&uarr;" v-on:click="object_field_moveup(tvp)" ></div>
										</td>
										<td>
											<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&darr;" v-on:click="object_field_movedown(tvp)" ></div>
										</td>
									</tr>
								</tbody>
							</table>
							<div><div class="btn btn-outline-dark btn-sm py-0" v-on:click="add_object_field" >+</div></div>
							</div>
							<table v-else  class="table table-bordered table-striped table-sm w-auto">
								<tbody>
									<tr>
										<td>#</td>
										<td>Property</td>
										<td>Type</td>
									</tr>
									<tr v-for="tvp,ti in edit_z_t[ propf ]['z']['z_o']" >
										<td>{{ tvp }}</td>
										<td>{{ edit_z_t[ propf ]['z']['z_t'][ tvp ]['l'] }}</td>
										<td>{{ edit_z_t[ propf ]['z']['z_t'][ tvp ]['t'] }}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				</template>
				</tbody>
			</table>
			<table v-if="edit_field==''" class="table table-bordered table-sm w-auto" >
				<tbody>
				<tr>
					<td>New Field</td>
					<td>
						<inputtextbox2 types="T,GT" v-bind:v="temp['new_field']" v-bind:datavar="'temp:new_field'" ></inputtextbox2>
					</td>
					<td>
						<div title="DataType" data-type="dropdown" v-bind:data-var="'temp:new_type'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" >{{ temp['new_type']['v'] }}</div>
					</td>
					<td>
						<input type="button" class="btn btn-outline-dark btn-sm" value="+" v-on:click="add_field()" >
					</td>
					<td></td>
					<td> </td>
				</tr>
				</tbody>
			</table>
			<div v-if="z_t_msg" v-html="z_t_msg" ></div>
			<div v-if="z_t_err" v-html="z_t_err" ></div>

			<div style="height:200px;" >-</div>


		</template>
		<template v-else-if="tab=='records'" >
			
			<div style="height:40px; display:flex; column-gap:20px; padding:5px; border:1px solid #ccc;" >
				<div>Records:  {{ records_cnt }}</div>
				<div>
					<div style="display:flex; column-gap:10px;" >
						<div>From:</div>
						<div><input type="text" class="form-control form-control-sm" v-model="records_from" ></div>
					</div>
				</div>
				<div>
					<input type="button" class="btn btn-outline-dark btn-sm" value="Search" v-on:click="open_records()" >
				</div>
				<div>
					<input v-if="records.length>0" type="button" class="btn btn-outline-dark btn-sm" value="Next" v-on:click="records_goto_next()" >
				</div>
			</div>
			<div class="btn btn-sm btn-outline-dark float-end me-2" style="float:right;" v-on:click="show_create2()" >Create Node</div>

			<div style="margin-top:10px;overflow:auto; width:calc(100% - 10px ); height:calc( 100% - 225px );" >

			<table class="table table-bordered table-sm w-auto" >
				<thead class="bg-light" style="position:sticky; top:0px;">
				<tr>
					<th>_id</th>
					<th>Label</th>
					<template v-if="'z_o' in v" >
						<th v-for="fd in v['z_o']"><span v-if="fd in v['z_t']" >{{ v['z_t'][fd]['l']['v'] }}</span><span v-else>{{ fd }}</span></th>
					</template>
				</tr>
				</thead>
				<tbody>
				<tr v-for="rec,reci in records">
					<td><div class="zz" ><a href="#" v-on:click.prevent.stop="getlink(rec['_id'])" >{{ rec['_id'] }}</a></div></td>
					<td><div class="zz" ><inputtextview v-bind:v="rec['l']" ></inputtextview></div></td>
					<template v-if="'z_o' in v" >
					<td v-for="fd in v['z_o']">
						<div class="zz" v-if="'props' in rec" >
						<template v-if="fd in rec['props']" ><inputtextview v-for="item in rec['props'][fd]" v-bind:v="item" ></inputtextview></template>
						</div>
					</td>
					</template>
				</tr>
				</tbody>
			</table>

			</div>



		</template>

		</template>
		<div style="clear:both;" ></div>

	</div>`
};
</script>