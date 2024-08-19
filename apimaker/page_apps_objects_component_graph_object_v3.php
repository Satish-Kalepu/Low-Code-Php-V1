<script>
const graph_object_v2 =  {
	data(){
		return {};
	},
	props: ["refname", "data", "object_id"],
	watch: {},
	mounted: function(){
		this.$root.window_tabs[ this.refname ]['data'] = {
			"thing": {},
			"new_field": "",
			"new_field_id": -1, 
			"new_field_d": {
				"field": {"t":"T", "v":""}, 
				"type":  {"t":"KV", "v":"Text", "k":"T"}
			},
			"tab": "home",
			"template_edit": -1,
			"edit_z_t": {},
			"edit_z_o": [],
			"edit_z_n": -1,
			"edit_field": "", "new_field": "",
			"edit_label": false, "edit_label_v": {},
			"edit_al": false, "edit_al_v": [],
			"edit_type": false, "edit_type_v": {},
			"edit_i_of": false, "edit_i_of_v": {}, 
			"msg": "", "err": "",
			"records": [], 
			"records_last": "", "records_from": "", "records_cnt": 0, "records_start": 0, "records_end": 0,
			"records_current_page": 1,
			"records_pages": [],
			"z_t_msg": "", "z_t_err": "",
			"i_of_msg": "", "i_of_err": "",
			"label_msg": "", "label_err": "","al_msg": "", "al_err": "","type_msg": "", "type_err": "",
			"props_msg": "", "props_err": "",
			"delete_field_id": "",
			"data_types": {"T": "Text","N": "Number","B": "Boolean","D": "Date","DT": "DateTime","TS": "Timestamp","O": "Object","L": "List","GT": "Graph Node","NL": "Null","TT": "Text Multiline","HT": "HTML Text" },
			"instance_type": {"N": "Node", "L": "DataSet", "D": "Document", "M": "Media"},
			"vedit": false,
		};
		setTimeout(this.load_thing,500);
	},
	methods: {
		echo__: function(v__){
			if( typeof(v__)=="object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
		verify_thing: function(){
			if( "i_of" in this.data['thing'] == false || typeof(this.data['thing']['i_of']) != "object" || "length" in this.data['thing']['i_of'] ){
				this.data['thing']['i_of'] = {
					"t":"GT", 
					"v":{"t":"T","v":""},
					"i":{"t":"T","v":""}
				};
			}
			if( "p_of" in this.data['thing'] == false || typeof(this.data['thing']['p_of']) != "object" || "length" in this.data['thing']['p_of'] == false ){
				this.data['thing']['p_of'] = [{
					"t":"GT", 
					"v":{"t":"T","v":""},
					"i":{"t":"T","v":""}
				}];
			}
			if( "al" in this.data['thing'] == false || typeof(this.data['thing']['al']) != "object" || "length" in this.data['thing']['al'] == false ){
				this.data['thing']['al'] = [];
			}
			if( "props" in this.data['thing'] == false ){
				this.data['thing']['props'] = {};
			}else if( typeof(this.data['thing']['props']) != "object" || "length" in this.data['thing']['props'] ){
				this.data['thing']['props'] = {};
			}
			if( "z_t" in this.data['thing']['i_of'] == false ){
				this.data['thing']['i_of']['z_t'] = {
					//"p1":{"l":{"t":"T","v":"Description"}, "t":{"t":"KV", "v":"Text", "k":"T"}, "e":false, "m":false},
				};
				this.data['thing']['i_of']['z_o'] = [];
				this.data['thing']['i_of']['z_n'] = 1;
			}else if( typeof(this.data['thing']['i_of']['z_t']) != "object" || "length" in this.data['thing']['i_of']['z_t'] ){
				this.data['thing']['i_of']['z_t'] = {
					//"p1":{"l":{"t":"T","v":"Description"}, "t":{"t":"KV", "v":"Text", "k":"T"}, "e":false, "m":false},
				};
				this.data['thing']['i_of']['z_o'] = [];
				this.data['thing']['i_of']['z_n'] = 1;
			}
		},
		load_thing: function( ){
			this.data['msg'] = "Loading...";
			this.data['thing'] = {};
			this.data['records'] = [];
			axios.post("?",{
				"action": "objects_load_object",
				"object_id": this.object_id
			}).then(response=>{
				this.data['msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								var v = response.data['data'];
								this.data['thing'] = v;
								this.$root.window_tabs[ this.refname ]['title'] = this.data['thing']['i_of']['v']+": " + this.data['thing']['l']['v'];
								this.verify_thing();
								setTimeout(function(v){v.data['msg'] = "";},200,this);
							}else{
								this.data['err'] = response.data['error'];
							}
						}else{
							this.data['err'] = "Incorrect response";
						}
					}else{
						this.data['err'] = "Incorrect response";
					}
				}else{
					this.data['err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['err'] = this.get_http_error__(error);
			});
		},
		load_records: function(){
			axios.post("?",{
				"action": "objects_load_records",
				"object_id": this.data['thing']['_id'],
			}).then(response=>{
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['records'] = response.data['data'];
							}else{
								this.data['err'] = response.data['error'];
							}
						}else{
							this.data['err'] = "Incorrect response";
						}
					}else{
						this.data['err'] = "Incorrect response";
					}
				}else{
					this.data['err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['err'] = this.get_http_error__(error);
			});
		},
		add_sub: function(vv,vk,vi){
			if( vk in this.data['thing'][vv] == false ){
				this.data['thing'][vv][ vk ] = [];
			}
			if( this.data['thing'][vv][ vk ].length>0 ){
				var v = JSON.parse(JSON.stringify( this.data['thing'][vv][ vk ][0] ));
			}else{
				var v = {'t':"T", "v":""};
			}
			this.data['thing'][vv][ vk ].push( v );
		},
		del_sub: function(vv,vk,vi){
			if( confirm("Are you sure?") ){
				this.data['thing'][vv][ vk ].splice(vi,1);
			}
		},
		add_field: function(){
			if( this.data['new_field_d']['field']['v'].trim() == "" ){
				alert("need field name");return;
			}
			if( this.data['new_field_d']['type']['v'].trim() == "" ){
				alert("need field type");return;
			}
			var np = "p1";
			for(var i=Number(this.data['thing']['i_of']['z_n']);i<999;i++){
				var np = "p"+i;
				if( np in this.data['edit_z_t'] == false ){
					this.data['edit_z_n'] = i+1;
					break;
				}
			}
			this.data['new_field'] = np;
			this.save_new_field();
		},
		save_new_field: function(){
			this.data['z_t_msg'] = "";this.data['z_t_err'] = "";
			axios.post("?", {
				"action": "objects_object_add_field",
				"object_id": this.data['thing']['i_of']['i'],
				"field": this.data['new_field'],
				"prop": {
					"l": JSON.parse(JSON.stringify(this.data['new_field_d']['field'])),
					"t": JSON.parse(JSON.stringify(this.data['new_field_d']['type'])),
					"e": false,
					"m": false
				},
				"z_n": this.data['edit_z_n']
			}).then(response=>{
				this.data['z_t_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.close_edit_field();
								this.data['z_t_msg'] = "Saved";
								this.data['edit_z_t'][ this.data['new_field']+'' ] = {
									"l": JSON.parse(JSON.stringify(this.data['new_field_d']['field'])),
									"t": JSON.parse(JSON.stringify(this.data['new_field_d']['type'])),
									"e": false,
									"m": false
								};
								this.data['edit_z_o'].push( this.data['new_field']+'' );
								this.data['thing']['i_of']['z_t'] = JSON.parse(JSON.stringify(this.data['edit_z_t']));
								this.data['thing']['i_of']['z_o'] = JSON.parse(JSON.stringify(this.data['edit_z_o']));
								this.data['thing']['i_of']['z_n'] = this.data['edit_z_n']+0;
								this.data['new_field_d'] = {
									"field": {"t":"T", "v":""}, 
									"type":  {"t":"KV", "v":"Text", "k":"T"}
								};
								this.data['new_field']='';
								setTimeout(function(v){v.z_t_msg = "";},3000,this);
							}else{
								this.data['z_t_err'] = response.data['error'];
							}
						}else{
							this.data['z_t_err'] = "Incorrect response";
						}
					}else{
						this.data['z_t_err'] = "Incorrect response";
					}
				}else{
					this.data['z_t_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['z_t_err'] = error.message;
			});
		},
		open_template_edit: function(){
			this.data['edit_template'] = this.data['thing']['i_of']['i'];
			this.data['edit_z_t'] = JSON.parse(JSON.stringify(this.data['thing']['i_of']['z_t']));
			this.data['edit_z_o'] = JSON.parse(JSON.stringify(this.data['thing']['i_of']['z_o']));
			this.data['edit_z_n'] = this.data['thing']['i_of']['z_n']+0;
			this.data['tab']="template";
		},
		open_records: function(){
			this.data['records_last'] = "";
			this.data['records_cnt'] = 0;
			this.open_records2();
		},
		open_records2: function(){
			this.data['records'] = [];
			this.data['tab']='records';
			this.data['msg'] = "";this.data['err'] = "";
			var cond = {
				"action": "objects_load_records",
				"object_id": this.data['thing']['_id'],
			};
			if( this.data['records_from'].trim() != "" ){
				cond['from'] = this.data['records_from'].trim();
			}
			if( this.data['records_last'].trim() != "" ){
				cond['last'] = this.data['records_last'].trim();
			}
			axios.post("?", cond).then(response=>{
				this.data['msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['records_cnt'] = response.data['cnt'];
								this.data['records'] = response.data['data'];
								this.data['records_last'] = this.data['records'][ this.records.length-1 ]['l']['v']+'';
								//this.records_next = this.records[ this.records.length-1 ]['l.v']+'';
							}else{
								this.data['err'] = response.data['error'];
							}
						}else{
							this.data['err'] = "Incorrect response";
						}
					}else{
						this.data['err'] = "Incorrect response";
					}
				}else{
					this.data['err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['err'] = error.message;
			});
		},
		records_goto_next: function(){
			this.open_records2();
		},
		next_page_exists: function(){

		},
		open_props_edit: function(){
			this.data['edit_template'] = this.data['thing']['i_of']['i'];
			this.data['tab']="home";
		},
		close_edit_field: function(){
			this.data['edit_field'] = '';
		},
		save_field: function(){
			var v = this.data['edit_z_t'][ this.data['edit_field'] ]['l']['v']+'';
			for(var fd in this.data['edit_z_t'] ){
				if( fd != this.data['edit_field'] && this.data['edit_z_t'][ fd ]['l']['v'].toLowerCase() == v.toLowerCase() ){
					alert("Field with same name already exists");return false;
				}
			}
			this.save_z_t();
		},
		save_z_t: function(){
			this.data['msg'] = "";this.data['err'] = "";
			axios.post("?", {
				"action": "objects_save_object_z_t",
				"object_id": this.data['thing']['i_of']['i'],
				"field": this.data['edit_field'],
				"prop": this.data['edit_z_t'][ this.data['edit_field'] ],
			}).then(response=>{
				this.data['msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.close_edit_field();
								this.data['msg'] = "Saved";
								this.data['thing']['i_of']['z_t' ] = JSON.parse(JSON.stringify(this.data['edit_z_t']));
								this.data['thing']['i_of']['z_o' ] = JSON.parse(JSON.stringify(this.data['edit_z_o']));
								this.data['thing']['i_of']['z_n' ] = this.data['edit_z_n']+0;
								this.data['new_field_d'] = {
									"field": {"t":"T", "v":""}, 
									"type":  {"t":"KV", "v":"Text", "k":"T"}, 
								};
								setTimeout(function(v){v.msg = "";},3000,this);
							}else{
								this.data['err'] = response.data['error'];
							}
						}else{
							this.data['err'] = "Incorrect response";
						}
					}else{
						this.data['err'] = "Incorrect response";
					}
				}else{
					this.data['err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['err'] = error.message;
			});
		},
		delete_field: function( vprop ){
			if( confirm("Are you sure?") == false ){return;}
			this.data['delete_field_id'] = vprop;
			this.data['msg'] = "";this.data['err'] = "";
			axios.post("?", {
				"action": "objects_delete_field",
				"object_id": this.data['thing']['i_of']['i'],
				"prop": vprop,
			}).then(response=>{
				this.data['msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['msg'] = "Field is Deleted";
								var i = this.data['thing']['i_of']['z_o' ].indexOf( this.data['delete_field_id'] );
								this.data['thing']['i_of']['z_o' ].splice( i, 1 );
								delete(this.data['thing']['i_of']['z_t' ][ this.data['delete_field_id'] ]);
								this.data['edit_z_o'] = JSON.parse( JSON.stringify(this.data['thing']['i_of']['z_o']) );
								this.data['edit_z_t'] = JSON.parse( JSON.stringify(this.data['thing']['i_of']['z_t']) );
								setTimeout(function(v){v.msg = "";},3000,this);
							}else{
								this.data['err'] = response.data['error'];
							}
						}else{
							this.data['err'] = "Incorrect response";
						}
					}else{
						this.data['err'] = "Incorrect response";
					}
				}else{
					this.data['err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['err'] = error.message;
			});
		},
		getlink: function(vi){
			this.$root.show_thing(vi);
		},
		moveup: function(vf){
			var i = this.data['edit_z_o'].indexOf(vf);
			if( i > 0 ){
				var x = this.data['edit_z_o'].splice(i,1);
				this.data['edit_z_o'].splice(i-1,0,x[0]);
				this.save_order();
			}
		},
		movedown: function(vf){
			var i = this.data['edit_z_o'].indexOf(vf);
			if( i < this.data['edit_z_o'].length-1 ){
				var x = this.data['edit_z_o'].splice(i,1);
				this.data['edit_z_o'].splice(i+1,0,x[0]);
				this.save_order();
			}
		},
		save_order: function(){
			this.data['msg'] = "";this.data['err'] = "";
			axios.post("?", {
				"action": "objects_save_z_o",
				"object_id": this.data['thing']['i_of']['i'],
				"z_o": this.data['edit_z_o'],
			}).then(response=>{
				this.data['msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['msg'] = "Updated";
								this.data['thing']['i_of']['z_o' ] = JSON.parse( JSON.stringify(this.data['edit_z_o']) );
								setTimeout(function(v){v.thing_save_msg = "";},3000,this);
							}else{
								this.data['err'] = response.data['error'];
							}
						}else{
							this.data['err'] = "Incorrect response";
						}
					}else{
						this.data['err'] = "Incorrect response";
					}
				}else{
					this.data['err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['err'] = error.message;
			});
		},
		save_props: function(){
			for( var pv in this.data['thing']['props'] ){
				if( this.data['thing']['i_of']['z_t'][ pv ]['t']['k'] == "O" ){
					var propd = this.data['thing']['i_of']['z_t'][ pv ]['z'];
					for( var i=0;i<this.data['thing']['props'][ pv ].length;i++ ){
						var f = false;
						for( var fd in propd['z_t'] ){
							if( fd in this.data['thing']['props'][ pv ][i] ){
								if( this.data['thing']['props'][ pv ][i][ fd ]['v'].trim() != "" ){
									f= true;
								}
							}
						}
						if( f == false ){
							this.data['thing']['props'][ pv ].splice(i,1);
							i--;
						}
					}
				}else{
					for( var i=0;i<this.data['thing']['props'][ pv ].length;i++ ){
						if( this.data['thing']['props'][ pv ][i]['v'].trim() == "" ){
							this.data['thing']['props'][ pv ].splice(i,1);
							i--;
						}
					}
				}
			}
			this.data['props_msg'] = "Saving...";this.data['props_err'] = "";
			axios.post("?", {
				"action": "objects_save_props",
				"object_id": this.data['thing']['_id'],
				"props": this.data['thing']['props'],
			}).then(response=>{
				this.data['props_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['props_msg'] = "Updated";
								this.data['edit']=false;
								setTimeout(function(v){v.props_msg = "";},3000,this);
							}else{
								this.data['props_err'] = response.data['error'];
							}
						}else{
							this.data['props_err'] = "Incorrect response";
						}
					}else{
						this.data['props_err'] = "Incorrect response";
					}
				}else{
					this.data['props_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['props_msg'] = "";
				this.data['props_err'] = error.message;
			});
		},
		open_edit_label: function(){
			this.data['edit_label_v'] = JSON.parse( JSON.stringify(this.data['thing']['l']) );
			this.data['edit_label'] = true;
		},
		open_edit_type: function(){
			this.data['edit_type_v'] = JSON.parse( JSON.stringify(this.data['thing']['i_t']) );
			this.data['edit_type'] = true;
		},
		open_edit_al: function(){
			this.data['edit_al_v'] = JSON.parse( JSON.stringify(this.data['thing']['al']) );
			this.data['edit_al'] = true;
		},
		open_edit_i_of: function(){
			this.data['edit_i_of_v'] = JSON.parse( JSON.stringify(this.data['thing']['i_of']) );
			this.data['edit_i_of'] = true;
		},
		save_al: function(){
			for(var i=0;i<this.data['edit_al_v'].length;i++){
				if( this.data['edit_al_v'][i]['v'].trim()=="" ){
					this.data['edit_al_v'].splice(i,1);i--;
				}else if( this.data['edit_al_v'][i]['v'].toLowerCase() == this.data['thing']['l']['v'].toLowerCase() ){
					this.data['al_err'] = "Label and Alias should be different";
				}
			}
			this.data['al_msg'] = "Saving...";this.data['al_err'] = "";
			axios.post("?", {
				"action": "objects_edit_alias",
				"object_id": this.data['thing']['_id'],
				"alias": this.data['edit_al_v'],
			}).then(response=>{
				this.data['al_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['al_msg'] = "Updated";
								this.data['thing']['al'] = JSON.parse( JSON.stringify(this.data['edit_al_v']));
								this.data['edit_al'] = false;
								setTimeout(function(v){v.al_msg = "";},3000,this);
							}else{
								this.data['al_err'] = response.data['error'];
							}
						}else{
							this.data['al_err'] = "Incorrect response";
						}
					}else{
						this.data['al_err'] = "Incorrect response";
					}
				}else{
					this.data['al_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['al_msg'] = "";
				this.data['al_err'] = error.message;
			});
		},
		save_label: function(){
			this.data['label_msg'] = "Saving...";this.data['label_err'] = "";
			axios.post("?", {
				"action": "objects_edit_label",
				"object_id": this.data['thing']['_id'],
				"label": this.data['edit_label_v'],
			}).then(response=>{
				this.data['label_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['label_msg'] = "Updated";
								this.data['thing']['l'] = JSON.parse( JSON.stringify(this.data['edit_label_v']));
								this.data['edit_label'] = false;
								setTimeout(function(v){v.label_msg = "";},3000,this);
							}else{
								this.data['label_err'] = response.data['error'];
							}
						}else{
							this.data['label_err'] = "Incorrect response";
						}
					}else{
						this.data['label_err'] = "Incorrect response";
					}
				}else{
					this.data['label_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['label_msg'] = "";
				this.data['label_err'] = error.message;
			});
		},
		save_type: function(){
			this.data['type_msg'] = "Saving...";this.data['type_err'] = "";
			axios.post("?", {
				"action": "objects_edit_type",
				"object_id": this.data['thing']['_id'],
				"type": this.data['edit_type_v'],
			}).then(response=>{
				this.data['type_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['type_msg'] = "Updated";
								this.data['thing']['i_t'] = JSON.parse( JSON.stringify(this.data['edit_type_v']));
								this.data['edit_type'] = false;
								setTimeout(function(v){v.type_msg = "";},3000,this);
							}else{
								this.data['type_err'] = response.data['error'];
							}
						}else{
							this.data['type_err'] = "Incorrect response";
						}
					}else{
						this.data['type_err'] = "Incorrect response";
					}
				}else{
					this.data['type_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['type_msg'] = "";
				this.data['type_err'] = error.message;
			});
		},
		save_i_of: function(){
			this.data['i_of_msg'] = "Saving...";this.data['i_of_err'] = "";
			axios.post("?", {
				"action": "objects_edit_i_of",
				"object_id": this.data['thing']['_id'],
				"i_of": this.data['edit_i_of_v'],
			}).then(response=>{
				this.data['i_of_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['i_of_msg'] = "Updated";
								this.data['thing']['i_of'] = JSON.parse( JSON.stringify(this.data['edit_i_of_v']));
								this.data['edit_i_of'] = false;
								setTimeout(function(v){v.i_of_msg = "";},3000,this);
							}else{
								this.data['i_of_err'] = response.data['error'];
							}
						}else{
							this.data['i_of_err'] = "Incorrect response";
						}
					}else{
						this.data['i_of_err'] = "Incorrect response";
					}
				}else{
					this.data['i_of_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['i_of_msg'] = "";
				this.data['i_of_err'] = error.message;
			});
		},
		add_al: function(){
			this.data['edit_al_v'].push({"t":"T","v":""});
		},
		del_al: function(vi){
			if( confirm("Are you sure?") ){
				this.data['edit_al_v'].splice(vi,1);
			}
		},
		callback__: function( c ){
			var x = c.split(/\:/g);
			if( x[0] == "z_t_type" ){
				if( this.data['edit_z_t'][ x[1] ]['t']['k'] == "O" ){
					this.data['edit_z_t'][ x[1] ]['z'] = {
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
					delete(this.data['edit_z_t'][ x[1] ]['z']);
				}
			}else{
				console.log("Callback: " + c + " not defined!");
			}
		},
		add_object_field: function(){
			this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_n']++;
			var n = this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_n']+0;
			var new_p = "p"+n;
			this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_t'][ new_p ] = {
				"l": "Field "+n,
				"t": "T"
			};
			this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_o'].push( new_p );
		},
		object_field_moveup: function(vf){
			var i = this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_o'].indexOf(vf);
			if( i > 0 ){
				var x = this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_o'].splice(i,1);
				this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_o'].splice(i-1,0,x[0]);
			}
		},
		object_field_movedown: function(vf){
			var i = this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_o'].indexOf(vf);
			if( i < this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_o'].length-1 ){
				var x = this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_o'].splice(i,1);
				this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_o'].splice(i+1,0,x[0]);
			}
		},
		add_sub_object: function( propf ){
			var o = {};
			var propd = this.data['thing']['i_of']['z_t'][ propf ];
			for(var tdi in propd['z']['z_t']){
				o[ tdi ] = {"t":"T", "v":""};
			}
			this.data['thing']['props'][ propf ].push( o );
		},
		del_sub_object: function( propf, vi ){
			this.data['thing']['props'][ propf ].splice( vi,1 );
		},
		show_create2: function(){
			this.$root.show_create({'t':"GT", "i": this.data['thing']['i'],"v": this.data['thing']['l']['v']});
		}
	},
	template: `<div class="code_line">
	<div v-if="typeof(data)=='undefined'" >Loading</div>
	<div v-else-if="'thing' in data==false" >Loading...</div>
	<div v-else-if="'l' in data['thing']==false||'i_of' in data['thing']==false" >Loading... ...</div>
	<template v-else>
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
					<div v-if="data['edit_label']==false" style="display:flex; column-gap:20px;" >
						<div style="min-width:250px;" >
							<div v-if="data['thing']['l']['t']=='GT'" >
								<template v-if="'v' in data['thing']['l']&&'i' in data['thing']['l']" >
									<a href="#" v-on:click.prevent.stop="getlink(data['thing']['l']['i'])" style="font-size:1.2rem;" >{{ data['thing']['l']['v'] }}</a>
								</template>
								<div v-else>error object</div>
							</div>
							<div v-else style="font-size:1.2rem;" >{{ data['thing']['l']['v'] }}</div>
						</div>
						<div><div class="btn btn-outline-link btn-sm py-0" v-on:click="open_edit_label()" >&#9998;</div></div>
					</div>
					<div v-if="data['edit_label']==true" style="min-width:250px;" >
						<p>
							<inputtextbox2 types="T,GT" v-bind:v="data['edit_label_v']" v-bind:datavar="'ref:'+refname+':edit_label_v'" ></inputtextbox2>
						</p>
						<p>
							<div class="btn btn-outline-dark btn-sm  py-0 me-2" v-on:click="save_label()" >Save</div>
							<div class="btn btn-outline-secondary btn-sm  py-0" v-on:click="data['edit_label']=false" >Cancel</div>
						</p>
						<div v-if="data['label_msg']" style="color:blue; padding:5px; border:1px solid blue;" v-html="data['label_msg']" ></div>
						<div v-if="data['label_err']" style="color:red;  padding:5px; border:1px solid red;"  v-html="data['label_err']" ></div>
					</div>
			</td>
			<td>
					<div style="display:flex; column-gap:20px;" >
						<div >
							<span v-if="data['thing']['i_t']['v'] in data['instance_type']" >{{ data['instance_type'][ data['thing']['i_t']['v'] ] }}</span>
							<span v-else>{{ data['thing']['i_t']['v'] }}</span>
						</div>
						<div><div class="btn btn-outline-link btn-sm  py-0" v-on:click="open_edit_type()" >&#9998;</div></div>
					</div>
					<div v-if="data['edit_type']==true" style="position:absolute;background-color:white;border:1px solid #ccc;box-shadow:2px 2px 5px #666; " >
						<div style="padding:5px; background-color:#f0f0f0;">Instance Type</div>
						<div style="padding:5px;" >
							<div><label><input type="radio" v-model="data['edit_type_v']['v']" value="N" > Node (A thing/Person/Place etc)</label></div>
							<div><label><input type="radio" v-model="data['edit_type_v']['v']" value="L" > DataSet (Tabular Data)</label> </div>
							<div><label><input type="radio" v-model="data['edit_type_v']['v']" value="D" > Document (Article/Blog)</label> </div>
							<div class="mb-3"><label><input type="radio" v-model="data['edit_type_v']['v']" value="M" > Media (Image/Video)</label></div>
							<p>
								<div class="btn btn-outline-dark btn-sm  py-0 me-2" v-on:click="save_type()" >Save</div>
								<div class="btn btn-outline-secondary btn-sm  py-0" v-on:click="edit_type=false" >Cancel</div>
							</p>
							<div v-if="data['type_msg']" style="color:blue; padding:5px; border:1px solid blue;" v-html="data['type_msg']" ></div>
							<div v-if="data['type_err']" style="color:red; padding:5px; border:1px solid red;" v-html="data['type_err']" ></div>
						</div>
					</div>
			</td>
			<td>
					<div v-if="data['edit_al']==false"  style="display:flex; column-gap:20px;" >
						<div>
							<div v-for="alv in data['thing']['al']">{{ alv['v'] }}</div>
						</div>
						<div><div class="btn btn-outline-link btn-sm  py-0" v-on:click="open_edit_al()" >&#9998;</div></div>
					</div>
					<div v-if="data['edit_al']==true" >
							<div style="margin-bottom:10px;">
								<div v-for="alv,ali in data['edit_al_v']" style="display:flex; column-gap:5px; border-bottom:1px dashed #ccc; align-items:center;" >
									<div><input type="button" class="btn btn-outline-danger btn-sm py-0"  style="padding:0px;width:20px;" value="x" v-on:click="del_al(ali)" ></div>
									<inputtextbox2 types="T,GT" v-bind:v="alv" v-bind:datavar="'ref:'+refname+':edit_al_v:'+ali" ></inputtextbox2>
								</div>
								<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_al(0)" ></div>
							</div>
							<div>
								<div class="btn btn-outline-dark btn-sm  py-0 me-2" v-on:click="save_al()" >Save</div>
								<div class="btn btn-outline-secondary btn-sm  py-0" v-on:click="data['edit_al']=false" >Cancel</div>
							</div>

							<div v-if="data['al_msg']" style="color:blue; padding:5px; border:1px solid blue;" v-html="data['al_msg']" ></div>
							<div v-if="data['al_err']" style="color:red; padding:5px; border:1px solid red;"   v-html="data['al_err']" ></div>
					</div>
			</td>
			<td>
					<div v-if="data['edit_i_of']==false"  style="display:flex; column-gap:20px;" >
						<div v-if="data['thing']['i_of']['t']=='GT'" >
							<template v-if="'v' in data['thing']['i_of']&&'i' in data['thing']['i_of']" >
								<a href="#" v-on:click.prevent.stop="getlink(data['thing']['i_of']['i'])" >{{ data['thing']['i_of']['v'] }}</a>
							</template>
							<div v-else>error object</div>
						</div>
						<div v-else>Error</div>
						<div><div class="btn btn-outline-link btn-sm  py-0" v-on:click="open_edit_i_of()" >&#9998;</div></div>
					</div>
					<div v-if="data['edit_i_of']==true">
						<inputtextbox2 types="GT" v-bind:v="data['edit_i_of_v']" v-bind:datavar="'ref:'+refname+':edit_i_of_v'" ></inputtextbox2>
						<div class="mt-2"><div class="btn btn-outline-dark btn-sm  py-0 me-2" v-on:click="save_i_of()" >Save</div>
						<div class="btn btn-outline-secondary btn-sm  py-0" v-on:click="data['edit_i_of']=false" >Cancel</div></div>
						<div v-if="data['i_of_msg']" style="color:blue; padding:5px; border:1px solid blue;" v-html="data['i_of_msg']" ></div>
						<div v-if="data['i_of_err']" style="color:red;  padding:5px; border:1px solid red;" v-html="data['i_of_err']" ></div>
					</div>
			</td>
			</tr>
		</tbody>
		</table>
			<!--<tr>
				<td>Part Of</td>
				<td>
					<template v-if="'p_of' in data['thing']" >
						<div v-if="'p_of' in data['thing']" class="codeline_thing" style="display:flex;column-gap:10px;" >
							<div>
								<div v-if="typeof(data['thing']['p_of']['v'])!=undefined" title="Thing" data-type="dropdown" v-bind:data-var="'ref:'+refname+':p_of:v'"  data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" >{{ data['thing']['p_of']['v']['l']['v'] }}</div>
								<div v-else>data['thing']['p_of']['v'] Undefined</div>
							</div>
						</div>
					</template>
					<div>Undefined</div>
				</td>
			</tr>-->

		<template v-if="'props' in data['thing']" >
			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a v-bind:class="{'nav-link py-0':true, 'active':data['tab']=='home'}" href="#" v-on:click.prevent.stop="open_props_edit()">Properties</a>
				</li>
				<li class="nav-item">
					<a v-bind:class="{'nav-link py-0':true, 'active':data['tab']=='template'}"  href="#" v-on:click.prevent.stop="open_template_edit()">Template</a>
				</li>
				<li class="nav-item" v-if="'cnt' in data['thing']">
					<a v-if="data['thing']['cnt']>0" v-bind:class="{'nav-link py-0':true, 'active':data['tab']=='records'}"  href="#" v-on:click.prevent.stop="open_records()">Records <span class="badge bg-secondary" >{{ data['thing']['cnt'] }}</span> </a>
				</li>
			</ul>
			<template v-if="data['vedit']==false&&data['tab']=='home'" >
				<div style="padding:10px;"><div class="btn btn-outline-dark btn-sm" v-on:click="data['vedit']=true" >&#9998;</div></div>
				<div v-if="data['props_msg']" style="color:blue; padding:5px; border:1px solid blue;" v-html="data['props_msg']" ></div>
				<div v-if="data['props_err']" style="color:red;  padding:5px; border:1px solid red;"  v-html="data['props_err']" ></div>

				<table v-if="'z_o' in data['thing']['i_of']" class="table table-bordered table-sm w-auto" >
					<tbody>
					<tr v-for="propf in data['thing']['i_of']['z_o']" valign="top">
						<td nowrap>
							<span v-if="propf in data['thing']['i_of']['z_t']" >{{ data['thing']['i_of']['z_t'][ propf ]['l']['v'] }}</span>
							<span v-else >{{ propf }}</span>
						</td>
						<td>
							<div v-if="propf in data['thing']['props']==false" >-</div>
							<div v-else-if="typeof(data['thing']['props'][ propf ])!='object'&&'length' in data['thing']['props'][ propf ]==false" >-</div>
							<div v-else-if="data['thing']['props'][ propf ].length>0" >
								<template v-if="data['thing']['i_of']['z_t'][ propf ]['t']['k']=='O'" >
									<table class="table table-bordered table-striped table-sm w-auto customborder2" >
										<tbody>
											<tr>
												<td v-for="tdv in data['thing']['i_of']['z_t'][ propf ]['z']['z_o']" >{{ data['thing']['i_of']['z_t'][ propf ]['z']['z_t'][ tdv ]['l'] }}</td>
											</tr>
											<tr v-for="pvv,pii in data['thing']['props'][ propf ]" >
												<td v-for="tdv in data['thing']['i_of']['z_t'][ propf ]['z']['z_o']" >
													<template v-if="tdv in pvv" >
														<inputtextview v-if="'t' in pvv[tdv]&&'v' in pvv[tdv]" v-bind:v="pvv[ tdv ]" ></inputtextview>
													</template>
												</td>
											</tr>
										</tbody>
									</table>
								</template>
								<template v-else >
									<div v-for="pvv,pii in data['thing']['props'][ propf ]" >
										<inputtextview v-bind:v="pvv" v-bind:datavar="'ref:'+refname+':thing:props:'+propf+':'+pii" ></inputtextview>
									</div>
								</template>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
				<div v-if="data['edit_z_o'].length>5"><div class="btn btn-outline-dark btn-sm" v-on:click="data['vedit']=true" >&#9998;</div></div>
			</template>
			<template v-if="data['vedit']==true&&data['tab']=='home'&&'z_o' in data['thing']['i_of']" >
				<div style="padding:10px;">
					<div class="btn btn-outline-dark btn-sm me-2"  v-on:click="save_props()" >Save</div>
					<div class="btn btn-outline-secondary btn-sm"  v-on:click="vedit=false" >Cancel</div>
				</div>

				<div v-if="data['props_msg']" style="color:blue; padding:5px; border:1px solid blue;" v-html="data['props_msg']" ></div>
				<div v-if="data['props_err']" style="color:red;  padding:5px; border:1px solid red;"  v-html="data['props_err']" ></div>

				<table class="table table-bordered table-sm w-auto customborder2" >
					<tbody>
					<tr v-for="propf in data['thing']['i_of']['z_o']" valign="top">
						<td>
							<span v-if="propf in data['thing']['i_of']['z_t']" >{{ data['thing']['i_of']['z_t'][ propf ]['l']['v'] }}</span>
							<span v-else >{{ propf }}</span>
						</td>
						<td>
							<template v-if="data['thing']['i_of']['z_t'][ propf ]['t']['k']=='O'" >
								<table class="table table-bordered table-striped table-sm w-auto customborder2" >
									<tbody>
										<tr>
											<td>-</td>
											<td v-for="tdv in data['thing']['i_of']['z_t'][ propf ]['z']['z_o']" >{{ data['thing']['i_of']['z_t'][ propf ]['z']['z_t'][ tdv ]['l'] }}</td>
										</tr>
										<tr v-for="pvv,pii in data['thing']['props'][ propf ]" >
											<td><input type="button" class="btn btn-outline-danger btn-sm py-0" style="padding:0px;width:20px;" value="X" v-on:click="del_sub_object(propf,pii)" ></td>
											<td v-for="tdv in data['thing']['i_of']['z_t'][ propf ]['z']['z_o']" >
												<template v-if="tdv in pvv" >
													<inputtextbox2 types="T,GT" linkable="true" v-bind:v="pvv[ tdv ]" v-bind:datavar="'ref:'+refname+':thing:props:'+propf+':'+pii+':'+tdv" ></inputtextbox2>
												</template>
											</td>
										</tr>
									</tbody>
								</table>
								<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_sub_object(propf,0)" ></div>
							</template>
							<template v-else >
								<div v-if="propf in data['thing']['props']==false" >
									<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_sub('props',propf,0)" ></div>
								</div>
								<div v-else >
									<div v-for="pvv,pii in data['thing']['props'][ propf ]" style="display:flex; column-gap:5px; border-bottom:1px dashed #ccc; align-items:center;" >
										<div><input type="button" class="btn btn-outline-danger btn-sm py-0"  style="padding:0px;width:20px;" value="x" v-on:click="del_sub('props',propf,pii)" ></div>
										<inputtextbox linkable="true" v-bind:v="pvv" v-bind:datavar="'ref:'+refname+':thing:props:'+propf+':'+pii" ></inputtextbox>
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
				<div v-if="data['thing']['i_of']['z_o'].length>5">
					<div style="padding:10px;" ><div class="btn btn-outline-dark btn-sm me-2" v-on:click="save_props()" >Save</div><div class="btn btn-outline-secondary btn-sm"  v-on:click="data['vedit']=false" >Cancel</div></div>
					<div v-if="data['props_msg']" style="color:blue; padding:5px; border:1px solid blue;" v-html="data['props_msg']" ></div>
					<div v-if="data['props_err']" style="color:red;  padding:5px; border:1px solid red;"  v-html="data['props_err']" ></div>
				</div>
			</template>
			<template v-if="data['tab']=='template'" >
				<div style="padding:10px 0px; background-color:#f8f8f8;">Properties of {{ data['thing']['i_of']['v'] }}</div>
				<table class="table table-bordered table-sm w-auto" >
					<tbody>
					<template v-for="propf,fi in data['edit_z_o']" >
					<tr>
						<td><div>{{ propf }}</div></td>
						<td>
							<div v-if="propf in data['edit_z_t']" >
								<div v-if="data['edit_field']==propf" >
									<inputtextbox2 types="T,GT" v-bind:v="data['edit_z_t'][ propf ]['l']" v-bind:datavar="'ref:'+refname+':edit_z_t:'+propf+':l'" ></inputtextbox2>
								</div>
								<div v-else-if="data['edit_z_t'][ propf ]['l']['t']=='T'">{{ data['edit_z_t'][ propf ]['l']['v'] }}</div>
								<div v-else-if="data['edit_z_t'][ propf ]['l']['t']=='GT'"><a href="#" v-on:click.prevent.stop="getlink(data['edit_z_t'][ propf ]['l']['i'])" >{{ data['edit_z_t'][ propf ]['l']['v'] }}</a></div>
							</div>
						</td>
						<td>
							<div v-if="propf in data['edit_z_t']" >
								<div v-if="data['edit_field']==propf" title="DataType" data-type="dropdown" v-bind:data-var="'ref:'+refname+':edit_z_t:'+propf+':t'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" v-bind:data-context-callback="refname+':z_t_type:'+propf" >{{ data['edit_z_t'][propf]['t']['v'] }}</div>
								<div v-else>{{ data['edit_z_t'][ propf ]['t']['v'] }}</div>
							</div>
						</td>
						<td v-if="data['edit_field']==''">
							<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="&#9998;" v-on:click="data['edit_field']=propf+''" ></div>
						</td>
						<td v-if="data['edit_field']&&propf==data['edit_field']">
							<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="Save" v-on:click="save_field()" ></div>
						</td>
						<td v-if="data['edit_field']&&propf==data['edit_field']">
							<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="Cancel" v-on:click="data['edit_field']=''" ></div>
						</td>
						<td v-if="data['edit_field']==''">
							<div><input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="delete_field(propf)" ></div>
						</td>
						<td v-if="data['edit_field']==''">
							<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&uarr;" v-on:click="moveup(propf)" ></div>
						</td>
						<td v-if="data['edit_field']==''">
							<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&darr;" v-on:click="movedown(propf)" ></div>
						</td>
					</tr>
					<tr v-if="data['edit_z_t'][ propf ]['t']['k']=='O'" >
						<td colspan="9" >
							<div style="margin-left:20px; border-left:1px dashed #ccc; padding:0px 10px; " >
								<div style="padding:5px 0px;">Object Template</div>
								<div  v-if="data['edit_field']&&propf==data['edit_field']" >
								<table class="table table-bordered table-striped table-sm w-auto">
									<tbody>
										<tr>
											<td>#</td>
											<td>Property</td>
											<td>Type</td>
											<td>-</td><td>-</td>
										</tr>
										<tr v-for="tvp,ti in data['edit_z_t'][ propf ]['z']['z_o']" >
											<td>{{ tvp }}</td>
											<td><input type="text" v-model="data['edit_z_t'][ propf ]['z']['z_t'][ tvp ]['l']" class="form-control form-control-sm" ></td>
											<td><select v-model="data['edit_z_t'][ propf ]['z']['z_t'][ tvp ]['t']" class="form-select form-select-sm" >
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
										<tr v-for="tvp,ti in data['edit_z_t'][ propf ]['z']['z_o']" >
											<td>{{ tvp }}</td>
											<td>{{ data['edit_z_t'][ propf ]['z']['z_t'][ tvp ]['l'] }}</td>
											<td>{{ data['edit_z_t'][ propf ]['z']['z_t'][ tvp ]['t'] }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
					</template>
					</tbody>
				</table>
				<table v-if="data['edit_field']==''" class="table table-bordered table-sm w-auto" >
					<tbody>
					<tr>
						<td>New Field</td>
						<td>
							<inputtextbox2 types="T,GT" v-bind:v="data['new_field_d']['field']" v-bind:datavar="'ref:'+refname+':new_field:field'" ></inputtextbox2>
						</td>
						<td>
							<div title="DataType" data-type="dropdown" v-bind:data-var="'ref:'+refname+':new_field:type'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" >{{ data['new_field_d']['type']['v'] }}</div>
						</td>
						<td>
							<input type="button" class="btn btn-outline-dark btn-sm" value="+" v-on:click="add_field()" >
						</td>
						<td></td>
						<td> </td>
					</tr>
					</tbody>
				</table>
				<div v-if="data['z_t_msg']" v-html="data['z_t_msg']" ></div>
				<div v-if="data['z_t_err']" v-html="data['z_t_err']" ></div>

				<div style="height:100px;" >-</div>


			</template>
			<template v-else-if="data['tab']=='records'" >
				
				<div style="height:40px; display:flex; column-gap:20px; padding:5px; border:1px solid #ccc;" >
					<div>Records:  {{ data['records_cnt'] }}</div>
					<div>
						<div style="display:flex; column-gap:10px;" >
							<div>From:</div>
							<div><input type="text" class="form-control form-control-sm" v-model="data['records_from']" ></div>
						</div>
					</div>
					<div>
						<input type="button" class="btn btn-outline-dark btn-sm" value="Search" v-on:click="open_records()" >
					</div>
					<div>
						<input v-if="data['records'].length>0" type="button" class="btn btn-outline-dark btn-sm" value="Next" v-on:click="records_goto_next()" >
					</div>
				</div>
				<div class="btn btn-sm btn-outline-dark float-end me-2" style="float:right;" v-on:click="show_create2()" >Create Node</div>

				<div style="margin-top:10px;overflow:auto; width:calc(100% - 10px ); height:calc( 100% - 225px );" >

				<table class="table table-bordered table-sm w-auto" >
					<thead class="bg-light" style="position:sticky; top:0px;">
					<tr>
						<th>_id</th>
						<th>Label</th>
						<template v-if="'z_o' in data['thing']" >
							<th v-for="fd in data['thing']['z_o']"><span v-if="fd in data['thing']['z_t']" >{{ data['thing']['z_t'][fd]['l']['v'] }}</span><span v-else>{{ fd }}</span></th>
						</template>
					</tr>
					</thead>
					<tbody>
					<tr v-for="rec,reci in data['records']">
						<td><div class="zz" ><a href="#" v-on:click.prevent.stop="getlink(rec['_id'])" >{{ rec['_id'] }}</a></div></td>
						<td><div class="zz" ><inputtextview v-bind:v="rec['l']" ></inputtextview></div></td>
						<template v-if="'z_o' in data['thing']" >
						<td v-for="fd in data['thing']['z_o']">
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
	</template>
</div>`
};
</script>