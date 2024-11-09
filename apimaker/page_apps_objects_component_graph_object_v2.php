<script>
const graph_object_v2 =  {
	data(){
		return {
			"html_update_cnt__": 1,
			"html_save_cnt__": 1,
			"html_save_busy__": false,
			"icon_popup__": false,
			"icon_domain__": "example.com",
		};
	},
	props: ["refname", "data", "object_id"],
	watch: {},
	mounted: function(){
		this.$root.window_tabs[ this.refname+'' ]['data'] = {
			"editing_dataset_record_index": -1,
			"thing": {},
			"is_deleted": false,
			"new_field": "",
			"new_field_id": -1, 
			"tab": "home",
			"show_template_edit_btn": false,
			"template_edit": -1,
			"edit_z_t": {}, "edit_z_o": [], "edit_z_n": -1,
			"edit_field": "", "new_field": "",
			"new_field_d": {
				"field": {"t":"T", "v":""}, 
				"type":  {"t":"KV", "v":"Text", "k":"T"},
				"m":  {"t":"B", "v":"false"}
			},
			"z_t_msg": "", "z_t_err": "",
			"thing_z_t_edit": {}, "thing_z_o_edit": [], "thing_z_n_edit": -1,
			"thing_z_t_edit_field": "", "thing_new_field": "",
			"thing_z_t_new_field_d": {
				"field": {"t":"T", "v":""},
				"type":  {"t":"KV", "v":"Text", "k":"T"},
				"m":  {"t":"B", "v":"false"}
			},
			"thing_z_t_msg": "", "thing_z_t_err": "",
			"edit_label": false, "edit_label_v": {},
			"edit_al": false, "edit_al_v": [],
			"edit_type": false, "edit_type_v": {},
			"edit_i_of": false, "edit_i_of_v": {}, 
			"msg": "", "err": "",
			"records": [], 
			"records_last": "", "records_from": "", "records_cnt": 0, "records_start": 0, "records_end": 0,
			"records_current_page": 1,
			"records_pages": [],
			"records_search":{
				"sort": {"t":"KV", "k":"_id","v":"ID"}, 
				"order": "Asc", 
				"cond":[{"field":{"t":"KV", "k":"_id","v":"ID"}, "ops": {"t":"KV", "k":"=", "v":"="}, "value":{"t":"T", "v":""} }]
			},
			"i_of_msg": "", "i_of_err": "", "ic_msg": "", "ic_err": "",
			"label_msg": "", "label_err": "","al_msg": "", "al_err": "","type_msg": "", "type_err": "",
			"props_msg": "", "props_err": "",
			"delete_field_id": "",
			"data_types": {"T": "Text","N": "Number","B": "Boolean","D": "Date","DT": "DateTime","TS": "Timestamp","O": "Object","L": "List","GT": "Graph Node","NL": "Null","TT": "Text Multiline","HT": "HTML Text" },
			"instance_type": {"N": "Node", "L": "DataSet", "D": "Document", "M": "Media"},
			"vedit": false,
			"editing_record_index": -1, "editing_record_id": -1, "deleting_record_index": -1, "deleting_record_id":1,
		};
		if( typeof(this.$root.icon_domain) != "undefined" ){
			this.icon_domain__ = this.$root.icon_domain+''
		}
		setTimeout(this.load_thing,200);
		document.addEventListener("click", this.clickit);
		document.addEventListener("keyup", this.keyup);
	},
	methods: {
		clickit: function(e){
			var v = e.target;
			var isin = false;
			while( 1 ){
				if( v.nodeName == "#text" ){
				}else if( v.hasAttribute("data-id") ){
					if( v.getAttribute("data-id") == "bounds" ){
						isin = true;
						break;
					}
					break;
				}
				if( v.nodeName == "BODY" ){
					break;
				}
				v = v.parentNode;
			}
			if( isin == false ){
				if( this.icon_popup__ ){
					this.icon_popup__ = false;
				}
			}
		},
		keyup: function(e){
			if( e.keyCode == 27 ){
				if( this.icon_popup__ ){
					this.icon_popup__ = false;
				}
			}
		},
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
			if( "p_of" in this.data['thing'] ){
				if( typeof(this.data['thing']['p_of']) != "object" || "length" in this.data['thing']['p_of'] ){
					unset(this.data['thing']['p_of']);
				}
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
				this.loadInstanceTemplate();
			}else if( "z_t" in this.data['thing']['i_of'] ){
				if( typeof(this.data['thing']['i_of']['z_t']) != "object" || "length" in this.data['thing']['i_of']['z_t'] ){
					this.data['thing']['i_of']['z_t'] = {
						//"p1":{"l":{"t":"T","v":"Description"}, "t":{"t":"KV", "v":"Text", "k":"T"}, "e":false, "m":false},
					};
					this.data['thing']['i_of']['z_o'] = [];
					this.data['thing']['i_of']['z_n'] = 1;
				}
			}
			this.open_records();
			this.check_zt_fields();
			this.$root.context_data__[ "search_ops" ] = [
				{"t":"KV", "k":"=", "v":"="},
				{"t":"KV", "k":"!=", "v":"!="},
				{"t":"KV", "k":">", "v":">"},
				{"t":"KV", "k":">=", "v":">="},
				{"t":"KV", "k":"<", "v":"<"},
				{"t":"KV", "k":"<=", "v":"<="},
			];
		},
		check_zt_fields: function(){
			if( 'z_t'  in this.data['thing'] ){
				var pd = [
					{"t":"KV", "k":'_id', "v":'ID'}
				];
				if( this.data['thing']['i_t']['v']=="N" ){
					pd.push( {"t":"KV", "k":'l', "v":"Label"} );
				}
				if( this.data['thing']['i_t']['v']=="N" ){
					pd.push( {"t":"KV", "k":'al', "v":"Alias"} );
				}
				for(var rd in this.data['thing']['z_t'] ){
					pd.push( {"t":"KV", "k":rd+'', "v":this.data['thing']['z_t'][rd]['l']['v']+''} );
				}
				this.$root.context_data__[ "props_fields_"+this.data['thing']['_id'] ] = pd;
			}
			this.$root.context_data__[ "search_ops" ] = [
				{"t":"KV", "k":"=", "v":"="},
				{"t":"KV", "k":"!=", "v":"!="},
				{"t":"KV", "k":">", "v":">"},
				{"t":"KV", "k":">=", "v":">="},
				{"t":"KV", "k":"<", "v":"<"},
				{"t":"KV", "k":"<=", "v":"<="},
			];
		},
		enable_template: function(){
			this.data['thing_z_t_edit'] = {
				"p1":{ 
					"l":{"t":"T","v":"Description"}, 
					"t":{"t":"KV", "v":"Text", "k":"T"}, 
					"e":false, 
					"m":{'t':"B", 'v':false}
				},
			};
			this.data['thing_z_o_edit'] = ["p1"];
			this.data['thing_z_n_edit'] = 2;
			this.thing_z_t_enable_save();
			// this.data['thing_z_t_edit'] = JSON.parse(JSON.stringify(this.data['thing']['z_t']));
			// this.data['thing_z_o_edit'] = JSON.parse(JSON.stringify(this.data['thing']['z_o']));
			// this.data['thing_z_n_edit'] = this.data['thing']['z_n']+0;
		},


		thing_add_document_content__: function(){
			this.data['thing']['body'] = {
				"html": "<p>Enter Content here...</p>", "options": {}
			};
		},
		html_body_updated__: function(vdata){
			this.data['thing']['body']['html'] = vdata;
			this.html_update_cnt__++;
			this.save_html__();
		},
		save_html__: function(){
			if( this.html_save_busy__ == false ){
				this.html_save_busy__ = true;
				setTimeout(this.save_html_queue__,1000);
			}
		},
		save_html_queue__: function(){
			{
				axios.post("?",{
					"action": "objects_save_object_html",
					"object_id": this.data['thing']['_id'],
					"body": this.data['thing']['body'],
					"cnt": this.html_update_cnt__,
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.html_save_cnt__ = Number(response.data['cnt']);
									if( this.html_update_cnt__ > this.html_save_cnt__ ){
										this.html_save_busy__ = true;
										setTimeout(this.save_html_queue__,500);
									}else{
										this.html_save_busy__ = false;
									}
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
			}
		},


		load_thing: function(){
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
				this.data['err'] = get_http_error__(error);
			});
		},
		loadInstanceTemplate: function(){
			this.data['msg'] = "Loading...";
			axios.post("?",{
				    "action": "objects_load_template",
				    "object_id": this.data['thing']['i_of']['i']
			}).then(response=>{
				this.data['msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								var v = response.data['data'];
								if( 'z_t' in v ){
									this.data['thing']['i_of']['z_t'] = v['z_t'];
									this.data['thing']['i_of']['z_o'] = v['z_o'];
									this.data['thing']['i_of']['z_n'] = v['z_n'];
								}
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
					"m": JSON.parse(JSON.stringify(this.data['new_field_d']['m'])),
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
									"m": JSON.parse(JSON.stringify(this.data['new_field_d']['m'])),
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
								this.check_zt_fields();
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
			this.show_template_edit_btn = true;
			this.data['edit_template'] = this.data['thing']['i_of']['i'];
			this.data['edit_z_t'] = JSON.parse(JSON.stringify(this.data['thing']['i_of']['z_t']));
			this.data['edit_z_o'] = JSON.parse(JSON.stringify(this.data['thing']['i_of']['z_o']));
			this.data['edit_z_n'] = this.data['thing']['i_of']['z_n']+0;
			this.data['tab']="template";
		},
		open_template2_edit: function(){
			if( 'z_t' in this.data['thing'] ){
				this.data['thing_z_t_edit'] = JSON.parse(JSON.stringify(this.data['thing']['z_t']));
				this.data['thing_z_o_edit'] = JSON.parse(JSON.stringify(this.data['thing']['z_o']));
				this.data['thing_z_n_edit'] = this.data['thing']['z_n']+0;
			}
			this.data['tab']="template2";
		},
		open_records: function(){
			this.data['records_last'] = "";
			this.data['records_cnt'] = 0;
			this.data['records_from'] = "";
			this.open_records2();
		},
		open_records2: function(){
			this.data['records'] = [];
			this.data['msg'] = "";this.data['err'] = "";
			var cond = {
				"action": "objects_load_records",
				"object_id": this.data['thing']['_id'],
				"sort": this.data['records_search']['sort']['k'],
				"order": this.data['records_search']['order'],
				"cond": this.data['records_search']['cond'],
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
								if( response.data['data'].length >= 100 ){
									if( this.data['records_search']['sort']['k'] =="_id" ){
										this.data['records_last'] = this.data['records'][ this.data['records'].length-1 ]['_id']+'';
									}else if( this.data['records_search']['sort']['k'] =="label" ){
										this.data['records_last'] = this.data['records'][ this.data['records'].length-1 ]['l']['v']+'';
									}else{
										this.data['records_last'] = this.data['records'][ this.data['records'].length-1 ]['props'][ this.data['records_search']['sort']['k'] ][0]['v']+'';
										this.echo__( this.data['records'][ this.data['records'].length-1 ]['props'][ this.data['records_search']['sort']['k'] ] );
									}
								}else{
									this.data['records_last'] = "";
								}
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
								setTimeout(function(v){v.data['msg'] = "";},3000,this);
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
								setTimeout(function(v){v.data['msg'] = "";},3000,this);
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
		thing_z_t_save_field: function(){
			var v = this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['l']['v']+'';
			for(var fd in this.data['thing_z_t_edit'] ){
				if( fd != this.data['thing_z_t_edit_field'] && this.data['thing_z_t_edit'][ fd ]['l']['v'].toLowerCase() == v.toLowerCase() ){
					alert("Field with same name already exists");return false;
				}
			}
			this.thing_z_t_save();
		},
		thing_z_t_save: function(){
			this.data['thing_z_t_msg'] = "";this.data['thing_z_t_err'] = "";
			axios.post("?", {
				"action": "objects_save_object_z_t",
				"object_id": this.data['thing']['_id'],
				"field": this.data['thing_z_t_edit_field'],
				"prop": this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ],
			}).then(response=>{
				this.data['thing_z_t_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.thing_z_t_close_edit_field();
								this.data['msg'] = "Saved";
								this.data['thing']['z_t' ] = JSON.parse(JSON.stringify(this.data['thing_z_t_edit']));
								this.data['thing']['z_o' ] = JSON.parse(JSON.stringify(this.data['thing_z_o_edit']));
								this.data['thing']['z_n' ] = this.data['thing_z_n_edit']+0;
								this.data['thing_z_t_new_field_d'] = {
									"field": {"t":"T", "v":""}, 
									"type":  {"t":"KV", "v":"Text", "k":"T"}, 
								};
								this.check_zt_fields();
								setTimeout(function(v){v.data['thing_z_t_msg'] = "";},3000,this);
							}else{
								this.data['thing_z_t_err'] = response.data['error'];
							}
						}else{
							this.data['thing_z_t_err'] = "Incorrect response";
						}
					}else{
						this.data['thing_z_t_err'] = "Incorrect response";
					}
				}else{
					this.data['thing_z_t_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['thing_z_t_err'] = error.message;
			});
		},
		thing_z_t_enable_save: function(){
			this.data['thing_z_t_msg'] = "";this.data['thing_z_t_err'] = "";
			axios.post("?", {
				"action": "objects_save_enable_z_t",
				"object_id": this.data['thing']['_id'],
				"z_t": this.data['thing_z_t_edit'],
				"z_o": this.data['thing_z_o_edit'],
				"z_n": this.data['thing_z_n_edit'],
			}).then(response=>{
				this.data['thing_z_t_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.thing_z_t_close_edit_field();
								this.data['msg'] = "Saved";
								this.data['thing']['z_t' ] = JSON.parse(JSON.stringify(this.data['thing_z_t_edit']));
								this.data['thing']['z_o' ] = JSON.parse(JSON.stringify(this.data['thing_z_o_edit']));
								this.data['thing']['z_n' ] = this.data['thing_z_n_edit']+0;
								this.data['thing_z_t_new_field_d'] = {
									"field": {"t":"T", "v":""}, 
									"type":  {"t":"KV", "v":"Text", "k":"T"}, 
								};
								this.check_zt_fields();
								setTimeout(function(v){v.data['thing_z_t_msg'] = "";},3000,this);
							}else{
								this.data['thing_z_t_err'] = response.data['error'];
							}
						}else{
							this.data['thing_z_t_err'] = "Incorrect response";
						}
					}else{
						this.data['thing_z_t_err'] = "Incorrect response";
					}
				}else{
					this.data['thing_z_t_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['thing_z_t_err'] = error.message;
			});
		},
		thing_z_t_delete_field: function( vprop ){
			if( confirm("Are you sure?") == false ){return;}
			this.data['thing_delete_field_id'] = vprop;
			this.data['thing_z_t_msg'] = "";this.data['thing_z_t_err'] = "";
			axios.post("?", {
				"action": "objects_delete_field",
				"object_id": this.data['thing']['_id'],
				"prop": vprop,
			}).then(response=>{
				this.data['thing_z_t_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['thing_z_t_msg'] = "Field is Deleted";
								var i = this.data['thing']['z_o' ].indexOf( this.data['thing_delete_field_id'] );
								this.data['thing']['z_o' ].splice( i, 1 );
								delete(this.data['thing']['z_t' ][ this.data['thing_delete_field_id'] ]);
								this.data['thing_z_o_edit'] = JSON.parse( JSON.stringify(this.data['thing']['z_o']) );
								this.data['thing_z_t_edit'] = JSON.parse( JSON.stringify(this.data['thing']['z_t']) );
								this.check_zt_fields();
								setTimeout(function(v){v.data['thing_z_t_msg'] = "";},3000,this);
							}else{
								this.data['thing_z_t_err'] = response.data['error'];
							}
						}else{
							this.data['thing_z_t_err'] = "Incorrect response";
						}
					}else{
						this.data['thing_z_t_err'] = "Incorrect response";
					}
				}else{
					this.data['thing_z_t_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['thing_z_t_err'] = error.message;
			});
		},
		thing_z_t_moveup: function(vf){
			var i = this.data['thing_z_o_edit'].indexOf(vf);
			if( i > 0 ){
				var x = this.data['thing_z_o_edit'].splice(i,1);
				this.data['thing_z_o_edit'].splice(i-1,0,x[0]);
				this.thing_z_t_save_order();
			}
		},
		thing_z_t_movedown: function(vf){
			var i = this.data['thing_z_o_edit'].indexOf(vf);
			if( i < this.data['thing_z_o_edit'].length-1 ){
				var x = this.data['thing_z_o_edit'].splice(i,1);
				this.data['thing_z_o_edit'].splice(i+1,0,x[0]);
				this.thing_z_t_save_order();
			}
		},
		thing_z_t_save_order: function(){
			this.data['thing_z_t_msg'] = "";this.data['thing_z_t_err'] = "";
			axios.post("?", {
				"action": "objects_save_z_o",
				"object_id": this.data['thing']['_id'],
				"z_o": this.data['thing_z_o_edit'],
			}).then(response=>{
				this.data['thing_z_t_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.data['thing_z_t_msg'] = "Updated";
								this.data['thing']['z_o' ] = JSON.parse( JSON.stringify(this.data['thing_z_o_edit']) );
								setTimeout(function(v){v.data['thing_z_t_msg'] = "";},3000,this);
							}else{
								this.data['thing_z_t_err'] = response.data['error'];
							}
						}else{
							this.data['thing_z_t_err'] = "Incorrect response";
						}
					}else{
						this.data['thing_z_t_err'] = "Incorrect response";
					}
				}else{
					this.data['thing_z_t_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['thing_z_t_err'] = error.message;
			});
		},
		thing_z_t_add_field: function(){
			if( this.data['thing_z_t_new_field_d']['field']['v'].trim() == "" ){
				alert("Need field name");return;
			}
			if( this.data['thing_z_t_new_field_d']['type']['v'].trim() == "" ){
				alert("Need field type");return;
			}
			var np = "p1";
			for(var i=Number(this.data['thing']['z_n']);i<999;i++){
				var np = "p"+i;
				if( np in this.data['thing_z_t_edit'] == false ){
					this.data['thing_z_n_edit'] = i+1;
					break;
				}
			}
			this.data['thing_z_t_new_field'] = np;
			this.thing_z_t_save_new_field();
		},
		thing_z_t_save_new_field: function(){
			this.data['thing_z_t_msg'] = "";this.data['thing_z_t_err'] = "";
			axios.post("?", {
				"action": "objects_object_add_field",
				"object_id": this.data['thing']['_id'],
				"field": this.data['thing_z_t_new_field'],
				"prop": {
					"l": JSON.parse(JSON.stringify(this.data['thing_z_t_new_field_d']['field'])),
					"t": JSON.parse(JSON.stringify(this.data['thing_z_t_new_field_d']['type'])),
					"e": false,
					"m": JSON.parse(JSON.stringify(this.data['thing_z_t_new_field_d']['m'])),
				},
				"z_n": this.data['thing_z_n_edit']
			}).then(response=>{
				this.data['thing_z_t_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.thing_z_t_close_edit_field();
								this.data['thing_z_t_msg'] = "Saved";
								this.data['thing_z_t_edit'][ this.data['thing_z_t_new_field']+'' ] = {
									"l": JSON.parse(JSON.stringify(this.data['thing_z_t_new_field_d']['field'])),
									"t": JSON.parse(JSON.stringify(this.data['thing_z_t_new_field_d']['type'])),
									"e": false,
									"m": JSON.parse(JSON.stringify(this.data['thing_z_t_new_field_d']['m'])),
								};
								this.data['thing_z_o_edit'].push( this.data['thing_z_t_new_field']+'' );
								this.data['thing']['z_t'] = JSON.parse(JSON.stringify(this.data['thing_z_t_edit']));
								this.data['thing']['z_o'] = JSON.parse(JSON.stringify(this.data['thing_z_o_edit']));
								this.data['thing']['z_n'] = this.data['thing_z_n_edit']+0;
								this.data['thing_z_t_new_field_d'] = {
									"field": {"t":"T", "v":""}, 
									"type":  {"t":"KV", "v":"Text", "k":"T"}
								};
								this.data['thing_z_t_new_field']='';
								this.check_zt_fields();
								setTimeout(function(v){v.data['thing_z_t_msg'] = "";},3000,this);
							}else{
								this.data['thing_z_t_err'] = response.data['error'];
							}
						}else{
							this.data['thing_z_t_err'] = "Incorrect response";
						}
					}else{
						this.data['thing_z_t_err'] = "Incorrect response";
					}
				}else{
					this.data['thing_z_t_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['thing_z_t_err'] = error.message;
			});
		},
		thing_z_t_close_edit_field: function(){
			this.data['thing_z_t_edit_field'] = '';
		},
		thing_z_t_add_object_field: function(){
			this.echo__( this.data['thing_z_t_edit_field'] );
			this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_n']++;
			var n = this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_n']+0;
			var new_p = "p"+n;
			this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_t'][ new_p ] = {
				"l": "Field "+n,
				"t": "T"
			};
			this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_o'].push( new_p );
		},
		thing_z_t_object_field_moveup: function(vf){
			var i = this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_o'].indexOf(vf);
			if( i > 0 ){
				var x = this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_o'].splice(i,1);
				this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_o'].splice(i-1,0,x[0]);
			}
		},
		thing_z_t_object_field_movedown: function(vf){
			var i = this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_o'].indexOf(vf);
			if( i < this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_o'].length-1 ){
				var x = this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_o'].splice(i,1);
				this.data['thing_z_t_edit'][ this.data['thing_z_t_edit_field'] ]['z']['z_o'].splice(i+1,0,x[0]);
			}
		},
		getlink: function(vi){
			this.$root.show_thing(vi);
		},
		save_props: function(){
			for( var pv in this.data['thing']['props'] ){
				if( this.data['thing']['i_of']['z_t'][ pv ]['t']['k'] == "O" ){
					var propd = this.data['thing']['i_of']['z_t'][ pv ]['z'];
					for( var i=0;i<this.data['thing']['props'][ pv ].length;i++ ){
						var f = false;
						for( var fd in propd['z_t'] ){
							if( fd in this.data['thing']['props'][ pv ][i]['v'] ){
								if( this.data['thing']['props'][ pv ][i]['v'][ fd ]['v'].trim() != "" ){
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
								this.data['vedit']=false;
								setTimeout(function(v){v.data['props_msg'] = "";},3000,this);
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
								setTimeout(function(v){v.data['al_msg'] = "";},3000,this);
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
								setTimeout(function(v){v.data['label_msg'] = "";},3000,this);
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

			// if( this.data['edit_type_v']['v'] == "L" && this.data['i_t']['v'] != "L" ){
			// 	if( confirm("Changing node type may result in loss of Sub Nodes\nDataSet node is a string database schema format with single value for each field\n") ){

			// 	}
			// }

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
								if( this.data['thing']['i_t']['v'] == "L" ){
									if( 'z_t' in this.data['thing'] == false ){
										this.enable_template();
									}
								}
								setTimeout(function(v){v.data['type_msg'] = "";},3000,this);
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
								setTimeout(function(v){v.data['i_of_msg'] = "";},3000,this);
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
			}else if( x[0] == "thing_z_t_edit_type" ){
				if( this.data['thing_z_t_edit'][ x[1] ]['t']['k'] == "O" ){
					this.data['thing_z_t_edit'][ x[1] ]['z'] = {
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
				}else if( x[0] == "thing_z_t_edit_type" ){
				}else{
					delete(this.data['thing_z_t_edit'][ x[1] ]['z']);
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
		object_field_delete: function(vf){
			var i = this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_o'].indexOf(vf);
			if( i > 0 ){
				var x = this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_o'].splice(i,1);
				delete( this.data['edit_z_t'][ this.data['edit_field'] ]['z']['z_t'][ vf ] );
			}
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
			if( propf in this.data['thing']['props'] == false ){
				this.data['thing']['props'][ propf ] = [];
			}
			this.data['thing']['props'][ propf ].push( {"t":"O", "v":o} );
		},
		del_sub_object: function( propf, vi ){
			this.data['thing']['props'][ propf ].splice( vi,1 );
		},
		show_create2: function(){
			this.$root.show_create({'t':"GT", "i": this.data['thing']['_id']+'',"v": this.data['thing']['l']['v']+''});
		},
		is_records_to_show: function(){
			if( 'thing' in this.data ){
				if( 'cnt' in this.data['thing'] ){
					return true;
				}else if( 'z_t' in this.data['thing'] ){
					return true;
				}
			}
			return false;
		},
		record_create: function(){
			this.$root.show_create_dataset_record(this.refname,{
				"thing": this.data['thing']
			});
		},
		record_edit: function(vid){
			this.data['editing_dataset_record_index'] = vid;
			this.$root.show_edit_dataset_record(this.refname,{
				"thing": this.data['thing'],
				"record":this.data['records'][ vid ]
			});
		},
		records_empty: function(){
			if( confirm("Are you sure to delete all nodes under `" + this.data['thing']['l']['v'] + "`") ){
				axios.post("?",{
					"action": "objects_records_empty",
					"instance_id": this.data['thing']['_id']
				}).then( response=>{
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.data['records'] = [];
									this.open_records();
								}else{
									alert( response.data['error'] );
								}
							}else{
								alert( "Incorrect response");
							}
						}else{
							alert(  "Incorrect response" );
						}
					}else{
						alert( "http error: " . response.status );
					}
				}).catch(error=>{
					alert(  this.get_http_error__(error) );
				});
			}
		},
		nodes_empty: function(){
			if( confirm("Are you sure to delete all nodes under `" + this.data['thing']['l']['v'] + "`") ){
				axios.post("?",{
					"action": "objects_nodes_empty",
					"instance_id": this.data['thing']['_id']
				}).then( response=>{
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.data['records'] = [];
									this.open_records();
								}else{
									alert( response.data['error'] );
								}
							}else{
								alert( "Incorrect response");
							}
						}else{
							alert(  "Incorrect response" );
						}
					}else{
						alert( "http error: " . response.status );
					}
				}).catch(error=>{
					alert(  this.get_http_error__(error) );
				});
			}
		},
		dataset_record_updated: function(vd){
			if( this.data['records'][ this.data['editing_dataset_record_index'] ]['_id'] == vd['record_id'] ){
				this.data['records'][ this.data['editing_dataset_record_index'] ]['props'] = JSON.parse(JSON.stringify(vd['record_props']));
			}else{
				this.echo__( "dataset_record_updated: mismatched event" );
			}
		},
		record_delete: function(vid){
			if( confirm("Are you sure?" ) ){
				this.deleting_record_index = vid;
				axios.post("?", {
					"action": "objects_dataset_record_delete", 
					"object_id": this.data['thing']['_id'], 
					"record_id": this.data['records'][ vid ]['_id']
				}).then( response=>{
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									alert("record deleted");
									this.data['records'].splice(this.deleting_record_index, 1);
								}else{
									alert( response.data['error'] );
								}
							}else{
								alert( "Incorrect response");
							}
						}else{
							alert(  "Incorrect response" );
						}
					}else{
						alert( "http error: " . response.status );
					}
				}).catch(error=>{
					alert(  this.get_http_error__(error) );
				});
			}
		},
		node_delete: function(vid){
			if( confirm("Are you sure?" ) ){
				this.deleting_record_index = vid;
				axios.post("?", {
					"action": "objects_delete_node", 
					"object_id": this.data['records'][ vid ]['_id'], 
				}).then( response=>{
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									alert("Node deleted");
									this.data['records'].splice(this.deleting_record_index, 1);
								}else{
									alert( response.data['error'] );
								}
							}else{
								alert( "Incorrect response");
							}
						}else{
							alert(  "Incorrect response" );
						}
					}else{
						alert( "http error: " . response.status );
					}
				}).catch(error=>{
					alert(  this.get_http_error__(error) );
				});
			}
		},
		node_delete_main: function(vid){
			if( confirm("Are you sure?" ) ){
				axios.post("?", {
					"action": "objects_delete_node", 
					"object_id": this.data['thing']['_id'], 
				}).then( response=>{
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									alert("Node deleted");
									this.data['is_deleted'] = true;
									this.data['thing'] = {};
								}else{
									alert( response.data['error'] );
								}
							}else{
								alert( "Incorrect response");
							}
						}else{
							alert(  "Incorrect response" );
						}
					}else{
						alert( "http error: " . response.status );
					}
				}).catch(error=>{
					alert(  this.get_http_error__(error) );
				});
			}
		},
		data_search_add_cond: function(){
			if( this.data['records_search']['cond'].length < 3 ){
				this.data['records_search']['cond'].push( {"field":{"t":"KV", "k":"_id","v":"ID"}, "ops": {"t":"KV", "k":"=", "v":"="}, "value":{"t":"T", "v":"="} } );
			}
		},
		data_search_del_cond: function(vi){
			if( this.data['records_search']['cond'].length > 1 ){
				this.data['records_search']['cond'].splice(vi,1);
			}
		},
		open_icon_popup__: function(){
			this.icon_popup__ = true;
		},
		get_icon_url__: function(vcountrycode, vsize){
			return "/"+"/" + this.icon_domain__ + "/flag-icons/flags/"+vsize+"/"+vcountrycode+".svg";
		},
		set_icon__: function( vdata ){
			this.icon_popup__ = false;
			this.data['thing']['ic'] = vdata;
			this.save_icon__();
		},
		save_icon__: function(){
			this.data['ic_msg'] = "Saving...";this.data['ic_err'] = "";
			axios.post("?", {
				"action": "objects_set_icon",
				"object_id": this.data['thing']['_id'],
				"ic": this.data['thing']['ic'],
			}).then(response=>{
				this.data['ic_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								
							}else{
								this.data['ic_err'] = response.data['error'];
							}
						}else{
							this.data['ic_err'] = "Incorrect response";
						}
					}else{
						this.data['ic_err'] = "Incorrect response";
					}
				}else{
					this.data['ic_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['ic_msg'] = "";
				this.data['ic_err'] = error.message;
			});
		}
	},
	template: `<div class="code_line">
	<div v-if="typeof(data)=='undefined'" >Loading</div>
	<div v-else-if="data['is_deleted']" >
		<p style="color:red;" >This node was deleted!</p>
	</div>
	<div v-else-if="'thing' in data==false" >Loading...</div>
	<div v-else-if="'l' in data['thing']==false||'i_of' in data['thing']==false" >
		<p>Loading... ...</p>
		<div v-if="data['msg']" style="color:blue;" >{{ data['msg'] }}</div>
		<div v-if="data['err']" style="color:red;" >{{ data['err'] }}</div>
	</div>
	<template v-else>
		<table class="table table-bordered table-sm w-auto" >
			<tbody>
			<tr>
				<td>&nbsp;</td>
				<td>ID</td>
				<td>Label</td>
				<td>-</td>
			</tr>
			<tr>
				<td>

					<div v-if="'ic' in data['thing']" class="objecticon" v-on:click.stop.prevent="open_icon_popup__" >
						<icon_view v-bind:data="data['thing']['ic']"></icon_view>
					</div>
					<div v-else class="thing_icon_false" v-on:click.stop.prevent="open_icon_popup__" ><i class="far fa-far fa-smile" ></i></div>
					<div v-if="icon_popup__" data-id="bounds" style="position:absolute; border:1px solid #999; box-shadow:2px 2px 25px #444; border-radius:5px; background-color:white; z-index:500;" >
						<iconsapp_component v-on:set_icon="set_icon__($event)" v-bind:icon_domain="icon_domain__" ></iconsapp_component>
					</div>

				</td>
				<td>
					{{ data['thing']['_id'] }}
				</td>
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
							<inputtextbox2 types="T,GT" v-bind:v="data['edit_label_v']" v-bind:datavar="'ref:'+refname+':data:edit_label_v'" ></inputtextbox2>
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
					<div class="btn btn-light btn-sm text-danger py-1" v-on:click="node_delete_main" ><i class="fa-regular fa-trash-can"></i></div>
				</td>
			</tr>
		</tbody>
		</table>
		<div v-if="data['ic_err']" class="alert alert-danger py-1 mb-2" >{{ data['ic_err'] }}</div>


		<table class="table table-bordered table-sm w-auto" >
			<tbody>
			<tr>
				<td>Type</td>
				<td>Alias</td>
				<td>Instance Of</td>
				<td>Part Of</td>
			</tr>
			<tr>
				<td>
					<div style="display:flex; column-gap:20px;" >
						<div >
							<span v-if="data['thing']['i_t']['v'] in data['instance_type']" >{{ data['instance_type'][ data['thing']['i_t']['v'] ] }}</span>
							<span v-else>{{ data['thing']['i_t']['v'] }}</span>
						</div>
						<div><div class="btn btn-outline-link btn-sm  py-0" v-on:click="open_edit_type()" >&#9998;</div></div>
					</div>
					<div v-if="data['edit_type']==true" style="position:absolute;background-color:white;border:1px solid #ccc;box-shadow:2px 2px 5px #666; z-index:10; " >
						<div style="padding:5px; background-color:#f0f0f0;">Instance Type</div>
						<div style="padding:5px;" >
							<div class="mb-2"><label style="cursor:pointer;"><input type="radio" v-model="data['edit_type_v']['v']" value="N" > Node (A thing/Person/Place etc)</label></div>
							<div class="mb-2"><label style="cursor:pointer;"><input type="radio" v-model="data['edit_type_v']['v']" value="L" > DataSet (Tabular Data)</label> </div>
							<div class="mb-2"><label style="cursor:pointer;"><input type="radio" v-model="data['edit_type_v']['v']" value="D" > Document (Article/Blog)</label> </div>
							<div class="mb-3"><label style="cursor:pointer;"><input type="radio" v-model="data['edit_type_v']['v']" value="M" > Media (Image/Video)</label></div>
							<p>
								<div class="btn btn-outline-dark btn-sm  py-0 me-2" v-on:click="save_type()" >Save</div>
								<div class="btn btn-outline-secondary btn-sm  py-0" v-on:click="data['edit_type']=false" >Cancel</div>
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
								<inputtextbox2 types="T,GT" v-bind:v="alv" v-bind:datavar="'ref:'+refname+':data:edit_al_v:'+ali" ></inputtextbox2>
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
						<inputtextbox2 types="GT" v-bind:v="data['edit_i_of_v']" v-bind:datavar="'ref:'+refname+':data:edit_i_of_v'" ></inputtextbox2>
						<div class="mt-2"><div class="btn btn-outline-dark btn-sm  py-0 me-2" v-on:click="save_i_of()" >Save</div>
						<div class="btn btn-outline-secondary btn-sm  py-0" v-on:click="data['edit_i_of']=false" >Cancel</div></div>
						<div v-if="data['i_of_msg']" style="color:blue; padding:5px; border:1px solid blue;" v-html="data['i_of_msg']" ></div>
						<div v-if="data['i_of_err']" style="color:red;  padding:5px; border:1px solid red;" v-html="data['i_of_err']" ></div>
					</div>
				</td>
				<td>
					<div v-if="'p_of' in data['thing']" >
						<template v-for="pd,pi in data['thing']['p_of']" >
							<template v-if="'v' in pd&&'i' in pd" >
								<a href="#" v-on:click.prevent.stop="getlink(pd['i'])" >{{ pd['v'] }}</a>
							</template>
						</template>
					</div>
				</td>
			</tr>
		</tbody>
		</table>


		<div class="graph_object_tabs_nav_bar">
			<div class="graph_object_tabs_nav_container" id="tabs_container">
				<div v-bind:class="{'graph_object_tab_btn':true,'graph_object_btn_active':(data['tab']=='home')}" v-bind:id="'subtab_home'">
					<div v-on:click.prevent.stop="open_props_edit()">Properties</div>
				</div>
				<div v-if="show_template_edit_btn" v-bind:class="{'graph_object_tab_btn':true,'graph_object_btn_active':(data['tab']=='template')}" v-bind:id="'subtab_template'">
					<div v-on:click.prevent.stop="open_template_edit()">Template for {{ data['thing']['i_of']['v'] }}</div>
				</div>
				<div v-if="data['thing']['i_t']['v']=='N'" v-bind:class="{'graph_object_tab_btn':true,'graph_object_btn_active':(data['tab']=='template2')}" v-bind:id="'subtab_template2'">
					<div v-on:click.prevent.stop="open_template2_edit()">Template for {{ data['thing']['l']['v'] }}</div>
				</div>
				<div v-if="data['thing']['i_t']['v']=='L'" v-bind:class="{'graph_object_tab_btn':true,'graph_object_btn_active':(data['tab']=='template2')}" v-bind:id="'subtab_template2'">
					<div v-on:click.prevent.stop="open_template2_edit()">Dataset Template</div>
				</div>
			</div>
		</div>

		<div v-if="data['tab']=='home'" style="border:1px solid #999; border-top:1px solid white;margin-bottom:20px; background-color:white; padding:10px; " >

			<div style="line-height:30px; border-bottom:1px solid #ccc; background-color:#f8f8f8; display:flex; column-gap:50px;">
				<div>Properties of "{{ data['thing']['i_of']['v'] }}" Sub Node</div>
				<div>
					<div v-if="data['vedit']==false" style=""><div class="btn btn-outline-dark btn-sm py-0" v-on:click="data['vedit']=true" >&#9998;</div></div>
					<div v-else >
						<div class="btn btn-outline-dark btn-sm me-2 py-0"  v-on:click="save_props()" >Save</div>
						<div class="btn btn-outline-secondary btn-sm py-0"  v-on:click="data['vedit']=false" >Cancel</div>
						<div class="btn btn-outline-secondary btn-sm py-0 float:right;"  v-on:click="open_template_edit()" >Edit Template</div>
					</div>
				</div>
			</div>

			<div v-if="'z_o' in data['thing']['i_of']==false"  >
				<p>There is no fields template defined for the node list of "{{ data['thing']['i_of']['v'] }}" </p>
			</div>
			<div v-else >

					<template v-if="data['vedit']==false" >
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
															<template v-if="pvv['t']=='O'" >
																<template v-if="typeof(pvv['v']=='object')" >
																	<template v-if="tdv in pvv['v']" >
																		<inputtextview v-if="'t' in pvv['v'][tdv]&&'v' in pvv['v'][tdv]" v-bind:v="pvv['v'][ tdv ]" ></inputtextview>
																	</template>
																</template>
																<div v-else>Incorrect Data</div>
															</template>
															<div v-else>Incorrect Data</div>
														</td>
													</tr>
												</tbody>
											</table>
										</template>
										<template v-else >
											<div v-for="pvv,pii in data['thing']['props'][ propf ]" >
												<inputtextview v-bind:v="pvv" ></inputtextview>
											</div>
										</template>
									</div>
								</td>
							</tr>
							</tbody>
						</table>
						<div v-if="data['edit_z_o'].length>5"><div class="btn btn-outline-dark btn-sm" v-on:click="data['vedit']=true" >&#9998;</div></div>
					</template>
					<template v-if="data['vedit']==true&&'z_o' in data['thing']['i_of']" >

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
														<template v-if="pvv['t']=='O'" >
															<template v-if="tdv in pvv['v']" >
																<inputtextbox2 types="T,GT" linkable="true" v-bind:v="pvv['v'][ tdv ]" v-bind:datavar="'ref:'+refname+':data:thing:props:'+propf+':'+pii+':v:'+tdv" ></inputtextbox2>
															</template>
														</template>
														<div v-else>Incorrect Data</div>
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
												<inputtextbox linkable="true" v-bind:v="pvv" v-bind:datavar="'ref:'+refname+':data:thing:props:'+propf+':'+pii" ></inputtextbox>
											</div>
											<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+"  style="padding:0px;width:20px;" v-on:click="add_sub('props',propf,pii)" ></div>
										</div>
									</template>
								</td>
							</tr>
							<tr valign="top">
								<td>
									<div class="btn btn-outline-dark btn-sm py-0" v-on:click="open_template_edit()" style="" >Add/Edit Properties</div>
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
			</div>
		</div>
		<div v-if="data['tab']=='template'" style="border:1px solid #999;border-top:1px solid white;margin-bottom:20px; background-color:white;padding:10px;"  >
				<div style="border-bottom:1px solid #ccc; padding:10px; background-color:#f8f8f8;">Properties template for this node which is common for nodes under {{ data['thing']['i_of']['v'] }}</div>

				<div v-if="'z_t' in data['thing']['i_of']==false" >
					<p>No fields available</p>
				</div>
				<div v-else>

					<table class="table table-bordered w-auto" >
						<tbody>
						<template v-for="propf,fi in data['edit_z_o']" >
						<tr>
							<td><div>{{ propf }}</div></td>
							<td>
								<div v-if="propf in data['edit_z_t']" >
									<div v-if="data['edit_field']==propf" >
										<inputtextbox2 types="T,GT" v-bind:v="data['edit_z_t'][ propf ]['l']" v-bind:datavar="'ref:'+refname+':data:edit_z_t:'+propf+':l'" ></inputtextbox2>
									</div>
									<div v-else-if="data['edit_z_t'][ propf ]['l']['t']=='T'">{{ data['edit_z_t'][ propf ]['l']['v'] }}</div>
									<div v-else-if="data['edit_z_t'][ propf ]['l']['t']=='GT'"><a href="#" v-on:click.prevent.stop="getlink(data['edit_z_t'][ propf ]['l']['i'])" >{{ data['edit_z_t'][ propf ]['l']['v'] }}</a></div>
								</div>
							</td>
							<td>
								<div v-if="propf in data['edit_z_t']" >
									<div v-if="data['edit_field']==propf" title="DataType" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:edit_z_t:'+propf+':t'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" v-bind:data-context-callback="refname+':z_t_type:'+propf" >{{ data['edit_z_t'][propf]['t']['v'] }}</div>
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
													<td>
														<div><input type="button" class="btn btn-outline-danger btn-sm py-0" value="x" v-on:click="object_field_delete(tvp)" ></div>
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
					<table v-if="data['edit_field']==''" class="table table-bordered w-auto" >
						<tbody>
						<tr>
							<td>New Field</td>
							<td>
								<inputtextbox2 types="T,GT" v-bind:v="data['new_field_d']['field']" v-bind:datavar="'ref:'+refname+':data:new_field_d:field'" ></inputtextbox2>
							</td>
							<td>
								<div title="DataType" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:new_field_d:type'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" >{{ data['new_field_d']['type']['v'] }}</div>
							</td>
							<td>
								<input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="add_field()" >
							</td>
						</tr>
						</tbody>
					</table>
					<div v-if="data['z_t_msg']" v-html="data['z_t_msg']" ></div>
					<div v-if="data['z_t_err']" v-html="data['z_t_err']" ></div>
				</div>

		</div>
		<div v-if="data['tab']=='template2'" style="border:1px solid #999;border-top:1px solid white;margin-bottom:20px; background-color:white;padding:10px;"  >
				<div style="border-bottom:1px solid #ccc; padding:10px; background-color:#f8f8f8;">Properties Template for sub nodes</div>
				<div v-if="data['thing_z_o_edit'].length==0" >
					<p>Do you want to enable creation fo Sub nodes under {{ data['thing']['l']['v'] }} </p>
					<div><div class="btn btn-outline-dark btn-sm" v-on:click="enable_template()" >Enable Template for Nodes</div></div>
				</div>
				<div v-else >
					<table class="table table-bordered w-auto" >
						<tbody>
						<template v-for="propf,fi in data['thing_z_o_edit']" >
						<tr>
							<td><div>{{ propf }}</div></td>
							<td>
								<div v-if="propf in data['thing_z_t_edit']" >
									<div v-if="data['thing_z_t_edit_field']==propf" >
										<inputtextbox2 types="T,GT" v-bind:v="data['thing_z_t_edit'][ propf ]['l']" v-bind:datavar="'ref:'+refname+':data:thing_z_t_edit:'+propf+':l'" ></inputtextbox2>
									</div>
									<div v-else-if="data['thing_z_t_edit'][ propf ]['l']['t']=='T'">{{ data['thing_z_t_edit'][ propf ]['l']['v'] }}</div>
									<div v-else-if="data['thing_z_t_edit'][ propf ]['l']['t']=='GT'"><a href="#" v-on:click.prevent.stop="getlink(data['thing_z_t_edit'][ propf ]['l']['i'])" >{{ data['thing_z_t_edit'][ propf ]['l']['v'] }}</a></div>
								</div>
							</td>
							<td>
								<div v-if="propf in data['thing_z_t_edit']" >
									<div v-if="data['thing_z_t_edit_field']==propf" title="DataType" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:thing_z_t_edit:'+propf+':t'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" v-bind:data-context-callback="refname+':thing_z_t_edit_type:'+propf" >{{ data['thing_z_t_edit'][propf]['t']['v'] }}</div>
									<div v-else>{{ data['thing_z_t_edit'][ propf ]['t']['v'] }}</div>
								</div>
							</td>
							<td>
								<div v-if="propf in data['thing_z_t_edit']" >
									<div v-if="data['thing_z_t_edit_field']==propf" title="Mandatory" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:thing_z_t_edit:'+propf+':m:v'" data-list="boolean" >{{ data['thing_z_t_edit'][propf]['m']['v'] }}</div>
									<div v-else>{{ data['thing_z_t_edit'][ propf ]['m']['v'] }}</div>
								</div>
							</td>
							<td v-if="data['thing_z_t_edit_field']==''">
								<div class="btn btn-light btn-sm py-1" v-on:click="data['thing_z_t_edit_field']=propf+''" ><i class="fa-regular fa-pen-to-square"></i></div>
							</td>
							<td v-if="data['thing_z_t_edit_field']&&propf==data['thing_z_t_edit_field']">
								<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="Save" v-on:click="thing_z_t_save_field()" ></div>
							</td>
							<td v-if="data['thing_z_t_edit_field']&&propf==data['thing_z_t_edit_field']">
								<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="Cancel" v-on:click="data['thing_z_t_edit_field']=''" ></div>
							</td>
							<td v-if="data['thing_z_t_edit_field']==''">
								<div class="btn btn-light btn-sm text-danger py-1" v-on:click="thing_z_t_delete_field(propf)" ><i class="fa-regular fa-trash-can"></i></div>
							</td>
							<td v-if="data['thing_z_t_edit_field']==''">
								<div><input type="button" class="btn btn-light btn-sm py-0" value="&uarr;" v-on:click="thing_z_t_moveup(propf)" ></div>
							</td>
							<td v-if="data['thing_z_t_edit_field']==''">
								<div><input type="button" class="btn btn-light btn-sm py-0" value="&darr;" v-on:click="thing_z_t_movedown(propf)" ></div>
							</td>
						</tr>
						<tr v-if="data['thing_z_t_edit'][ propf ]['t']['k']=='O'" >
							<td colspan="9" >
								<div style="margin-left:20px; border-left:1px dashed #ccc; padding:0px 10px; " >
									<div style="padding:5px 0px;">Object Template</div>
									<div  v-if="data['thing_z_t_edit_field']&&propf==data['thing_z_t_edit_field']" >
									<table class="table table-bordered table-striped table-sm w-auto">
										<tbody>
											<tr>
												<td>#</td>
												<td>Property</td>
												<td>Type</td>
												<td>-</td><td>-</td>
											</tr>
											<tr v-for="tvp,ti in data['thing_z_t_edit'][ propf ]['z']['z_o']" >
												<td>{{ tvp }}</td>
												<td><input type="text" v-model="data['thing_z_t_edit'][ propf ]['z']['z_t'][ tvp ]['l']" class="form-control form-control-sm" ></td>
												<td><select v-model="data['thing_z_t_edit'][ propf ]['z']['z_t'][ tvp ]['t']" class="form-select form-select-sm" >
													<option value="T" >Text</option>
													<option value="GT" >Thing Link</option>
													<option value="N" >Number</option>
													<option value="D" >Date</option>
												</select></td>
												<td>
													<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&uarr;" v-on:click="thing_z_t_object_field_moveup(tvp)" ></div>
												</td>
												<td>
													<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&darr;" v-on:click="thing_z_t_object_field_movedown(tvp)" ></div>
												</td>
											</tr>
										</tbody>
									</table>
									<div><div class="btn btn-outline-dark btn-sm py-0" v-on:click="thing_z_t_add_object_field" >+</div></div>
									</div>
									<table v-else  class="table table-bordered table-striped table-sm w-auto">
										<tbody>
											<tr>
												<td>#</td>
												<td>Property</td>
												<td>Type</td>
											</tr>
											<tr v-for="tvp,ti in data['thing_z_t_edit'][ propf ]['z']['z_o']" >
												<td>{{ tvp }}</td>
												<td>{{ data['thing_z_t_edit'][ propf ]['z']['z_t'][ tvp ]['l'] }}</td>
												<td>{{ data['thing_z_t_edit'][ propf ]['z']['z_t'][ tvp ]['t'] }}</td>
											</tr>
										</tbody>
									</table>
								</div>
							</td>
						</tr>
						</template>
						</tbody>
					</table>
					<table v-if="data['thing_z_t_edit_field']==''" class="table table-bordered w-auto" >
						<tbody>
						<tr>
							<td>New Field</td>
							<td>
								<inputtextbox2 types="T,GT" v-bind:v="data['thing_z_t_new_field_d']['field']" v-bind:datavar="'ref:'+refname+':data:thing_z_t_new_field_d:field'" ></inputtextbox2>
							</td>
							<td>
								<div title="DataType" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:thing_z_t_new_field_d:type'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" >{{ data['thing_z_t_new_field_d']['type']['v'] }}</div>
							</td>
							<td>
								<input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="thing_z_t_add_field()" >
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div v-if="data['z_t_msg2']" v-html="data['z_t_msg2']" ></div>
				<div v-if="data['z_t_err2']" v-html="data['z_t_err2']" ></div>
		</div>

		<div v-if="is_records_to_show()" style="border:1px solid #999;margin-bottom:20px; background-color:white;" >
		<template v-if="data['thing']['i_t']['v']=='N'" >

			<div style="line-height:30px; border-bottom:1px solid #ccc; padding:10px; background-color:#f8f8f8;">
				<span>Nodes in "{{ data['thing']['l']['v'] }}"</span>
				<div class="btn btn-sm btn-outline-dark float-end ms-2" style="float:right;" v-on:click="show_create2()" >Create Node</div>
				<div class="btn btn-sm btn-outline-dark float-end ms-2" style="float:right;" v-on:click="nodes_empty()" >Empty Nodes</div>
			</div>
			<div style="padding:10px; " >
				
				<div style="display:flex; column-gap:20px;" >
					<div>Records:  {{ data['records_cnt'] }}</div>
					<div>
						<div style="display:flex; column-gap:10px;" >
							<div>From:</div>
							<div><input type="text" class="form-control form-control-sm py-0" v-model="data['records_from']" ></div>
						</div>
					</div>
					<div>
						<input type="button" class="btn btn-outline-dark btn-sm py-0" value="Search" v-on:click="open_records()" >
					</div>
					<div>
						<input v-if="data['records'].length>0&&data['records_last']!=''" type="button" class="btn btn-outline-dark btn-sm py-0" value="Next" v-on:click="records_goto_next()" >
					</div>
				</div>

				<div style="display:flex; column-gap:10px;" >
					<div>Sort:</div>
					<div>
						<div title="Sort Field" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:records_search:sort'" data-list="list-kv" v-bind:data-list-values="'props_fields_'+data['thing']['_id']" >{{ data['records_search']['sort']['v'] }}</div>
					</div>
					<div>
						<div title="Order" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:records_search:order'" data-list="list" data-list-values="Asc,Dsc" >{{ data['records_search']['order'] }}</div>
					</div>
					<div>Search: </div>
					<div>
							<div v-for="rd,ri in data['records_search']['cond']"  style="display:flex;" >
								<div>
									<div title="Sort Field" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:records_search:cond:'+ri+':field'" data-list="list-kv" v-bind:data-list-values="'props_fields_'+data['thing']['_id']" >{{ data['records_search']['cond'][ri]['field']['v'] }}</div>
								</div>
								<div>
									<div title="Operator" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:records_search:cond:'+ri+':ops'" data-list="list-kv" data-list-values="search_ops" >{{ data['records_search']['cond'][ri]['ops']['v'] }}</div>
								</div>
								<div>
									<div title="Text" class="editable" v-bind:data-var="'ref:'+refname+':data:records_search:cond:'+ri+':value:v'"  ><div contenteditable style="white-space:nowrap;" spellcheck="false" data-type="editable"  v-bind:data-var="'ref:'+refname+':data:records_search:cond:'+ri+':value:v'" data-allow="T" >{{ rd['v'] }}</div></div>
								</div>
								<div>
									<input v-if="ri==0&&data['records_search']['cond'].length<3" type="button" class="btn btn-outline-secondary btn-sm py-0" value="+" v-on:click="data_search_add_cond()" >
									<input v-if="ri>0" type="button" class="btn btn-outline-secondary btn-sm py-0" value="x" v-on:click="data_search_del_cond(ri)" >
								</div>
							</div>
					</div>
					<div>
						<input type="button" class="btn btn-outline-dark btn-sm py-0" value="Search" v-on:click="open_records()" >
					</div>
					<div>
						<input v-if="data['records'].length>0&&data['records_last']!=''" type="button" class="btn btn-outline-dark btn-sm py-0" value="Next" v-on:click="records_goto_next()" >
					</div>
				</div>

				<div style="margin-top:10px;overflow:auto; width:calc(100%); border:1px solid #ccc; height:250px;resize:both;" >

					<table class="table table-bordered table-sm w-auto" >
						<thead class="bg-light" style="position:sticky; top:0px;">
						<tr>
							<th>-</th>
							<th>_id</th>
							<th>Label</th>
							<template v-if="'z_o' in data['thing']" >
								<th v-for="fd in data['thing']['z_o']"><span v-if="fd in data['thing']['z_t']" >{{ data['thing']['z_t'][fd]['l']['v'] }}</span><span v-else>{{ fd }}</span></th>
							</template>
							<th>Updated</th>
						</tr>
						</thead>
						<tbody>
						<tr v-for="rec,reci in data['records']">
							<td v-on:click="node_delete(reci)"><i class="fa-regular fa-trash-can"></i></td>
							<td><div class="zz" ><a href="#" v-on:click.prevent.stop="getlink(rec['_id'])" >{{ rec['_id'] }}</a></div></td>
							<td><div class="zz" v-if="'l' in rec" ><inputtextview v-bind:v="rec['l']" ></inputtextview></div></td>
							<template v-if="'z_o' in data['thing']" >
							<td nowrap v-for="fd in data['thing']['z_o']">
								<template v-if="fd in data['thing']['z_t']" >
									<div v-if="data['thing']['z_t'][ fd ]['t']['k']=='O'" >Object</div>
									<div v-else class="zz" v-if="'props' in rec" >
									<template v-if="fd in rec['props']" >
										<template v-for="item in rec['props'][fd]" >
											<inputtextview v-if="item['t']!='O'" v-bind:v="item" ></inputtextview>
										</template>
									</template>
									</div>
								</template>
							</td>
							</template>
							<td nowrap><span v-if="'m_u' in rec" >{{ rec['m_u'].substr(0,10) }}</span></td>
						</tr>
						</tbody>
					</table>

				</div>
			</div>
		</template>
		<template v-else-if="data['thing']['i_t']['v']=='L'" >

			<div style="line-height:30px; border-bottom:1px solid #ccc; padding:10px; background-color:#f8f8f8;">
				<span>Records in "{{ data['thing']['l']['v'] }}"</span>
				<div class="btn btn-sm btn-outline-dark float-end ms-2" style="float:right;" v-on:click="record_create()" >Create Record</div>
				<div class="btn btn-sm btn-outline-dark float-end ms-2" style="float:right;" v-on:click="records_empty()" >Empty List</div>
				<div style="float:right;">Records:  {{ data['records_cnt'] }}</div>
			</div>
			<div style="padding:10px; " >

				<div style="display:flex; column-gap:10px;" >
					<div>Sort:</div>
					<div>
						<div title="Sort Field" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:records_search:sort'" data-list="list-kv" v-bind:data-list-values="'props_fields_'+data['thing']['_id']" >{{ data['records_search']['sort']['v'] }}</div>
					</div>
					<div>
						<div title="Order" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:records_search:order'" data-list="list" data-list-values="Asc,Dsc" >{{ data['records_search']['order'] }}</div>
					</div>
					<div>Search: </div>
					<div>
							<div v-for="rd,ri in data['records_search']['cond']"  style="display:flex;" >
								<div>
									<div title="Sort Field" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:records_search:cond:'+ri+':field'" data-list="list-kv" v-bind:data-list-values="'props_fields_'+data['thing']['_id']" >{{ data['records_search']['cond'][ri]['field']['v'] }}</div>
								</div>
								<div>
									<div title="Operator" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:records_search:cond:'+ri+':ops'" data-list="list-kv" data-list-values="search_ops" >{{ data['records_search']['cond'][ri]['ops']['v'] }}</div>
								</div>
								<div>
									<div title="Text" class="editable" v-bind:data-var="'ref:'+refname+':data:records_search:cond:'+ri+':value:v'"  ><div contenteditable style="white-space:nowrap;" spellcheck="false" data-type="editable"  v-bind:data-var="'ref:'+refname+':data:records_search:cond:'+ri+':value:v'" data-allow="T" >{{ rd['v'] }}</div></div>
								</div>
								<div>
									<input v-if="ri==0&&data['records_search']['cond'].length<3" type="button" class="btn btn-outline-secondary btn-sm py-0" value="+" v-on:click="data_search_add_cond()" >
									<input v-if="ri>0" type="button" class="btn btn-outline-secondary btn-sm py-0" value="x" v-on:click="data_search_del_cond(ri)" >
								</div>
							</div>
					</div>
					<div>
						<input type="button" class="btn btn-outline-dark btn-sm py-0" value="Search" v-on:click="open_records()" >
					</div>
					<div>
						<input v-if="data['records'].length>0&&data['records_last']!=''" type="button" class="btn btn-outline-dark btn-sm py-0" value="Next" v-on:click="records_goto_next()" >
					</div>
				</div>

				<div style="margin-top:10px;overflow:auto; width:calc(100%); border:1px solid #ccc; height:250px;resize:both;" >

					<table class="table table-bordered table-sm w-auto" >
						<thead class="bg-light" style="position:sticky; top:0px;">
						<tr>
							<th>-</th>
							<th>-</th>
							<th>_id</th>
							<template v-if="'z_o' in data['thing']" >
								<th nowrap v-for="fd in data['thing']['z_o']">
									<span v-if="fd in data['thing']['z_t']" >{{ data['thing']['z_t'][fd]['l']['v'] }}</span>
									<span v-else>{{ fd }}</span>
								</th>
							</template>
							<th>Updated</th>
						</tr>
						</thead>
						<tbody>
						<tr v-for="rec,reci in data['records']">
							<td v-on:click="record_edit(reci)"><i class="fa-regular fa-pen-to-square"></i></td>
							<td v-on:click="record_delete(reci)"><i class="fa-regular fa-trash-can"></i></td>
							<td><div class="zz" >{{ rec['_id'] }}</div></td>
							<template v-if="'z_o' in data['thing']" >
							<td nowrap v-for="fd in data['thing']['z_o']">
								<template v-if="fd in data['thing']['z_t']" >
									<div v-if="data['thing']['z_t'][ fd ]['t']['k']=='O'" >Object</div>
									<div v-else class="zz" v-if="'props' in rec" >
									<template v-if="fd in rec['props']" >
										<template v-for="item in rec['props'][fd]" >
											<inputtextview v-if="item['t']!='O'" v-bind:v="item" ></inputtextview>
										</template>
									</template>
									</div>
								</template>
							</td>
							<td nowrap>{{ rec['m_u'].substr(0,10) }}</td>
							</template>
						</tr>
						</tbody>
					</table>

				</div>
			</div>
		</template>
		</div>

		<div v-if="data['thing']['i_t']['v']=='N'||data['thing']['i_t']['v']=='D'" data-id="root-document" style="border:1px solid #999;margin-bottom:20px; background-color:white; padding:10px 50px;" >

			<div v-if="'body' in data['thing']==false">
				<div class="btn btn-outline-dark btn-sm" v-on:click="thing_add_document_content__()" >Add Document Content</div>
			</div>
			<template v-else >
				<div v-bind:id="'editor_div_'+data['thing']['_id']" ></div>
				<!-- <pre v-if="'body' in data['thing']">{{ data['thing']['body'] }}</pre>-->
			</template>

		</div>

		<div>&nbsp;-&nbsp;</div>
		
	</template>
</div>`
};

</script>