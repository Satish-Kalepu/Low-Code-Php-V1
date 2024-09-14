<script>
const object_template_create_v2 = {
	data(){
		return {
			"instance_type": {"N": "Node", "L": "DataSet", "D": "Document", "M": "Media"},
		}
	},
	props: ["ref", "refname", "data"],
	watch: {
	},
	mounted: function(){
		console.log("object_template_create_v2")
		//this.load_template();
		var d = {
			"thing": {
				"l": {
					"t": "T", "v": ""
				},
				"i_of": {
					"t": "GT", "v": "", "i": "",
				},
				"i_t": {"t": "T", "v": "N"},
				"props": {},
			},
			"thing_i_of": {},
			"template_edit": -1,
			"new_field": {"key": "p1", "name": {"t":"T", "v": ""}, "type": {"t":"KV", "k":"T", "v":"text"}, "m": {"t":"B", "v": "false"} },
			"new_field_id": -1, 
			"msg": "", "err": "",
			"msg": "", "err": "",
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
			"vedit": false,
			"new_object_id": "",
		};
		if( this.refname == "create_popup_component" ){
			this.$root.popup_data__['data'] = d;
		}else{
			this.$root.window_tabs[ this.refname ]['data'] = d;
		}
		setTimeout(this.mounted2,500);
		console.log("Mounted2")
	},
	methods: {
		mounted2: function(){
			if( this.refname == "create_popup_component" ){
				console.log("yes");
				if( 'default_template' in this.$root.popup_data__ ){
					console.log("yes");
					this.data['thing']['i_of'] = {"t":"GT", "i":"T1", "v": "Root"};
					setTimeout(this.load_template,100);
					this.data['thing']["z_t"] = this.$root.popup_data__['default_template']['z_t'];
					this.data['thing']["z_o"] = this.$root.popup_data__['default_template']['z_o'];
					this.data['thing']["z_n"] = this.$root.popup_data__['default_template']['z_n'];
				}
				if( 'is_dataset' in this.$root.popup_data__ ){
					if( this.$root.popup_data__['is_dataset'] ){
						this.data['thing']['i_t']['v'] = "L";
					}else{
						this.data['thing']['i_t']['v'] = "N";
					}
				}else{
					this.data['thing']['i_t']['v'] = "N";
				}
			}else if( this.refname in this.$root.window_tabs ){
				if( 'i_of' in this.$root.window_tabs[ this.refname ] ){
					if( this.$root.window_tabs[ this.refname ]['i_of']['i'] != "" && this.$root.window_tabs[ this.refname ]['i_of']['v'] != "" ){
						//this.echo__( this.$root.window_tabs[ this.refname ]['i_of'] );
						this.data['thing']['i_of'] = JSON.parse( JSON.stringify( this.$root.window_tabs[ this.refname ]['i_of'] ));
						//this.echo__( this.data['thing']['i_of'] );
						this.load_template();
					}
				}
			}
		},
		make_props: function(){
			var p = {};
			var zt = this.data[ 'thing_i_of' ]['z'][ 'z_t' ];
			for( var fd in zt ){
				if( zt[ fd ]['t']['k'] == "O" ){
					var zt2 = zt[ fd ]['z']['z_t'];
					var v = {};
					for( fdd in zt2 ){
						v[ fdd+'' ] = {"t":"T", "v":""};
					}
					p[ fd+'' ] = [{"t":"O", "v":v}];
				}else{
					p[ fd+'' ] = [{ "t":"T", "v":"" }];
				}
			}
			this.data['thing']['props'] = p;
		},
		load_template: function(){
			//this.data['thing_i_of']['z'] = {};
			axios.post("?", {
				"action": "objects_load_template",
				"object_id": this.data['thing']['i_of']['i'],
			}).then(response=>{
				this.data['import_msg'] = "";
				if( typeof( response.data['data'] )=="object" ){
					var z = response.data['data'];
					this.data['thing_i_of']['z'] = z;
					this.make_props();
				}else{
					this.data['import_err'] = "Template not found";
				}
			}).catch(error=>{
				this.data['import_err'] = error.message;
			});
		},
		echo__: function(v__){
			if( typeof(v__)=="object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
		add_field: function(){
			if( this.data['new_field']['name']['v'].trim() == "" ){
				alert("need field name");return;
			}
			var np = "p1";
			for(var i=Number(this.data['thing']['z_n']);i<999;i++){
				var np = "p"+i;
				if( np in this.data['thing']['z_t'] == false ){
					this.data['thing']['z_n'] = i+1;
					break;
				}
			}
			this.data['new_field']['key'] = np;
			this.data['thing']['z_t'][ np ] = JSON.parse(JSON.stringify(this.data['new_field']));
			this.data['thing']['z_o'].push( np );
			this.data['thing']['z_n'] = this.data['thing']['z_n']+0;
			this.data['new_field'] = {
				"key": np,
				"name": {"t":"T", "v":""}, 
				"type": {"t":"KV", "v":"Text", "k":"T"}, 
				"m": {"t":"B", "v":"false"}, 
			};
		},
		reset_form: function(){
			this.data['thing'] = {
				"l": {
					"t": "T", "v": ""
				},
				"i_of": JSON.parse( JSON.stringify(this.data['thing']['i_of']) ),
				"i_t": {"t": "T", "v": "N"},
				"props": {},
			};
		},
		enable_template: function(){
			this.data['thing']["z_t"] = {
				"p1": {"key": "p1", "name": {"t":"T", "v": "Description"}, "type": {"t":"KV", "k":"T", "v":"text"}, "m": {"t":"B", "v":"true"} }
			};
			this.data['thing']["z_o"] = ["p1"];
			this.data['thing']["z_n"] = 2;
		},
		create_object: function(){
			this.data['msg'] = "";this.data['err'] = "";
			axios.post("?", {
				"action": "objects_create_with_template",
				"thing": this.data['thing']
			}).then(response=>{
				this.data['msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.new_object_id = response.data['inserted_id'];
								//this.data['msg'] = "Saved. New Object Id: " + response.data['inserted_id'];
								if( this.refname == "create_popup_component" ){
									//this.$root.$refs[ "import2" ][0].new_thing_created( {"object_id":response.data['inserted_id'], "label":this.data['thing']['l']['v']} );
									this.$root.popup_callback__({"object_id":response.data['inserted_id'], "label":this.data['thing']['l']['v']} );
								}else{
									this.$root.popup_callback__({"object_id":response.data['inserted_id'], "label":this.data['thing']['l']['v']} );
									this.$root.show_thing(this.new_object_id);
									this.$root.after_create(this.data['thing']['i_of']['i']);
								}
								this.reset_form();
								setTimeout(this.clear_success,5000);
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
		clear_success: function(){
			this.new_object_id = "";
		},
		delete_field: function( vprop ){
			var i = this.data['thing']['z_o'].indexOf( vprop );
			this.data['thing']['z_o'].splice( i, 1 );
			this.echo__( this.data['thing']['z_o'] );
			delete(this.data['thing']['z_t' ][ vprop ]);
		},
		getlink: function(vi){
			//return this.$root.objectpath+'?object_id='+vi;
			//this.$root.load_new_thing(vi);
		},
		getlink2: function(){
			this.$root.show_thing( this.new_object_id );
		},
		moveup: function(vf){
			var i = this.data['thing']['z_o'].indexOf(vf);
			if( i > 0 ){
				var x = this.data['thing']['z_o'].splice(i,1);
				this.data['thing']['z_o'].splice(i-1,0,x[0]);
			}
		},
		movedown: function(vf){
			var i = this.data['thing']['z_o'].indexOf(vf);
			if( i < this.data['thing']['z_o'].length-1 ){
				var x = this.data['thing']['z_o'].splice(i,1);
				this.data['thing']['z_o'].splice(i+1,0,x[0]);
			}
		},
		add_object_field: function( ef ){
			this.data['thing']['z_t'][ ef ]['z']['z_n']++;
			var n = this.data['thing']['z_t'][ ef ]['z']['z_n']+0;
			var new_p = "p"+n;
			this.data['thing']['z_t'][ ef ]['z']['z_t'][ new_p ] = {
				"l": "Field "+n,
				"t": "T"
			};
			this.data['thing']['z_t'][ ef ]['z']['z_o'].push( new_p );
		},
		object_field_delete: function(ef,vf){
			var i = this.data['thing']['z_t'][ ef ]['z']['z_o'].indexOf(vf);
			this.data['thing']['z_t'][ ef ]['z']['z_o'].splice(i,1);
			delete(this.data['thing']['z_t'][ ef ]['z']['z_t'][ vf ]);
		},
		object_field_moveup: function(ef,vf){
			var i = this.data['thing']['z_t'][ ef ]['z']['z_o'].indexOf(vf);
			if( i > 0 ){
				var x = this.data['thing']['z_t'][ ef ]['z']['z_o'].splice(i,1);
				this.data['thing']['z_t'][ ef ]['z']['z_o'].splice(i-1,0,x[0]);
			}
		},
		object_field_movedown: function(ef,vf){
			var i = this.data['thing']['z_t'][ ef ]['z']['z_o'].indexOf(vf);
			if( i < this.data['thing']['z_t'][ ef ]['z']['z_o'].length-1 ){
				var x = this.data['thing']['z_t'][ ef ]['z']['z_o'].splice(i,1);
				this.data['thing']['z_t'][ ef ]['z']['z_o'].splice(i+1,0,x[0]);
			}
		},
		callback__: function( c ){
			var x = c.split(/\:/g);
			if( x[0] == "z_type_change" ){
				if( this.data['thing']['z_t'][ x[1] ]['type']['k'] == "GT" ){
					this.data['thing']['z_t'][ x[1] ]['i_of'] = {"t": "GT", "v": "", "i": ""};
				}else if( this.data['thing']['z_t'][ x[1] ]['type']['k'] == "O" ){
					this.data['thing']['z_t'][ x[1] ]['z'] = {
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
					delete(this.data['thing']['z_t'][ x[1] ]['z']);
				}
			}else if( x[0] == "type_change" ){
				if( this.data['thing']['i_t']['v'] == "L" ){
					if( 'z_t' in this.data['thing'] == false ){
						this.enable_template();
					}
				}else{
					if( 'z_t' in this.data['thing'] ){
						delete(this.data['thing']['z_o']);
						delete(this.data['thing']['z_t']);
						delete(this.data['thing']['z_n']);
					}
				}
			}else if( x[0] == "i_of_select" ){
				delete(this.data['thing_i_of']['z']);
				setTimeout(this.load_template,100);
			}else{
				console.log("Callback: " + c + " not defined!");
			}
		},
		add_sub: function(propf,vi){
			if( propf in this.data['thing']['props'] == false ){
				this.data['thing']['props'][ propf ] = [];
			}
			var v = {'t':"T", "v":""};
			this.data['thing']['props'][ propf ].push( v );
		},
		del_sub: function(propf,vi){
			if( confirm("Are you sure?") ){
				this.data['thing']['props'][ propf ].splice(vi,1);
			}
		},
		add_sub_object: function( propf ){
			var o = {};
			var propd = this.data['thing_i_of']['z']['z_t'][ propf ];
			for( var tdi in propd['z']['z_t'] ){
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
	},
	template: `<div class="code_line" v-if="'thing' in data" >
			<div style="display:flex; column-gap:20px;margin-bottom:10px;" >
				<div class="code_line">
					<div>Instance</div>
					<div title="Thing" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:thing:i_of:v'" data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" v-bind:data-context-callback="refname+':i_of_select'" >{{ data['thing']['i_of']['v'] }}</div>
				</div>
				<div class="code_line">
					<div>Node Type</div>
					<div><div title="Node Type" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:thing:i_t:v'" data-list="list-assoc" data-list-values="node-type" v-bind:data-context-callback="refname+':type_change'" ><span v-if="data['thing']['i_t']['v'] in instance_type" >{{ instance_type[ data['thing']['i_t']['v'] ] }}</span><span v-else>{{ data['thing']['i_t']['v'] }}</span></div></div>
				</div>
				<div class="code_line">
					<div>Node Name</div>
					<div><inputtextbox2 types="T,GT" v-bind:v="data['thing']['l']" v-bind:datavar="'ref:'+refname+':data:thing:l'" ></inputtextbox2></div>
				</div>
			</div>
			<div v-if="'z' in data['thing_i_of']==false" style="border:1px solid #ccc; margin-bottom:10px;" >No properties template defined for {{ data['thing']['i_of']['v'] }} nodes</div>
			<div v-else-if="'z_t' in data['thing_i_of']['z']==false" style="border:1px solid #ccc; margin-bottom:10px;" >No properties template defined for {{ data['thing']['i_of']['v'] }} nodes</div>
			<div v-else style="border:1px solid #ccc; margin-bottom:10px;" >
				<div style="background-color:#f0f0f0; padding:5px;">Properties of "{{ data['thing']['i_of']['v'] }}"</div>
				<div style="padding:5px;" >
				<table class="table table-bordered table-sm w-auto" >
					<tbody v-if="'length' in data['thing_i_of']['z']['z_t']==false">
					<template v-for="propf,fi in data['thing_i_of']['z']['z_o']" >
					<template v-if="propf in data['thing_i_of']['z']['z_t']" >
					<tr>
						<td>{{ propf }}</td>
						<td>{{ data['thing_i_of']['z']['z_t'][ propf ]['l']['v'] }}</td>
						<td>
							<template v-if="data['thing_i_of']['z']['z_t'][ propf ]['t']['k']=='O'" >
								<table class="table table-bordered table-striped table-sm w-auto customborder2" >
									<tbody>
										<tr>
											<td>-</td>
											<td v-for="tdv in  data['thing_i_of']['z']['z_t'][ propf ]['z']['z_o']" >{{ data['thing_i_of']['z']['z_t'][ propf ]['z']['z_t'][ tdv ]['l'] }}</td>
										</tr>
										<tr v-for="pvv,pii in data['thing']['props'][ propf ]" >
											<td><div class="btn btn-light btn-sm  text-danger py-1"  v-on:click="del_sub_object(propf,pii)" ><i class="fa-regular fa-trash-can"></i></td>
											<td v-for="tdv in data['thing_i_of']['z']['z_t'][ propf ]['z']['z_o']" >
												<template v-if="tdv in pvv['v']" >
													<inputtextbox2 types="T,GT" linkable="true" v-bind:v="pvv['v'][ tdv ]" v-bind:datavar="'ref:'+refname+':data:thing:props:'+propf+':'+pii+':v:'+tdv" ></inputtextbox2>
												</template>
											</td>
										</tr>
									</tbody>
								</table>
								<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_sub_object(propf,0)" ></div>
							</template>
							<template v-else >
								<div v-if="propf in data['thing']['props']==false" >
									<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_sub(propf,0)" ></div>
								</div>
								<div v-else >
									<div v-for="pvv,pii in data['thing']['props'][ propf ]" style="display:flex; column-gap:5px; border-bottom:1px dashed #ccc; align-items:center;" >
										<div class="btn btn-light btn-sm py-0 text-danger"  style="padding:0px;width:20px;" v-on:click="del_sub(propf,pii)" ><i class="fa-regular fa-trash-can"></i></div>
										<inputtextbox linkable="true" v-bind:v="pvv" v-bind:datavar="'ref:'+refname+':data:thing:props:'+propf+':'+pii" ></inputtextbox>
									</div>
									<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+"  style="padding:0px;width:20px;" v-on:click="add_sub(propf,pii)" ></div>
								</div>
							</template>
						</td>
					</tr>
				</table>
				</div>
			</div>
			<div v-if="'z_t' in data['thing']==false&&(data['thing']['i_t']['v']=='N')" >
				<div class="btn btn-outline-dark btn-sm py-0" v-on:click="enable_template()" >Enable Sub Nodes</div>
			</div>
			<div v-if="'z_t' in data['thing']==false&&(data['thing']['i_t']['v']=='L')" >
				<div class="btn btn-outline-dark btn-sm py-0" v-on:click="enable_template()" >Enable DataSet Columns Template</div>
			</div>
			<div style="border:1px solid #ccc; margin-bottom:10px;" v-if="'z_t' in data['thing']" >
				<div style="background-color:#f0f0f0; padding:5px;">Template for "{{ data['thing']['l']['v'] }}"</div>
				<div style="padding:5px;" >


					<table class="table table-bordered table-sm w-auto" >
						<tbody v-if="'length' in data['thing']['z_t']==false">
						<template v-for="propf,fi in data['thing']['z_o']" >
						<template v-if="propf in data['thing']['z_t']" >
						<tr>
							<td><div>{{ propf }}</div></td>
							<td>
								<inputtextbox2 types="T,GT" v-bind:v="data['thing']['z_t'][ propf ]['name']" v-bind:datavar="'ref:'+refname+':data:thing:z_t:'+propf+':name'" ></inputtextbox2>
							</td>
							<td>
								<div title="DataType" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:thing:z_t:'+propf+':type'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" v-bind:data-context-callback="refname+':z_type_change:'+propf" >{{ data['thing']['z_t'][propf]['type']['v'] }}</div>
								<div v-if="data['thing']['z_t'][propf]['type']['k']=='GT'&&'i_of' in data['thing']['z_t'][ propf ]" >
									<inputtextbox2 types="GT" v-bind:v="data['thing']['z_t'][ propf ]['i_of']" v-bind:datavar="'ref:'+refname+':data:thing:z_t:'+propf+':i_of'" ></inputtextbox2>
								</div>
							</td>
							<td>
								<div title="Mandatory" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:thing:z_t:'+propf+':m:v'" data-list="boolean" >{{ data['thing']['z_t'][ propf ]['m']['v'] }}</div>
							</td>
							<td>
								<div class="btn btn-light btn-sm py-1 text-danger" v-on:click="delete_field(propf)" ><i class="fa-regular fa-trash-can"></i></div>
							</td>
							<td>
								<div><input type="button" class="btn btn-light btn-sm py-0" value="&uarr;" v-on:click="moveup(propf)" ></div>
							</td>
							<td>
								<div><input type="button" class="btn btn-light btn-sm py-0" value="&darr;" v-on:click="movedown(propf)" ></div>
							</td>
						</tr>
						<tr v-if="data['thing']['z_t'][ propf ]['type']['k']=='O'&&'z' in data['thing']['z_t'][ propf ]" >
							<td colspan="9" >
								<div style="margin-left:20px; border-left:1px dashed #ccc; padding:0px 10px; " >
									<div style="padding:5px 0px;">Object Template</div>
									<div>
										<table class="table table-bordered table-striped table-sm w-auto">
											<tbody>
												<tr>
													<td>#</td>
													<td>Property</td>
													<td>Type</td>
													<td>-</td><td>-</td><td>-</td>
												</tr>
												<tr v-for="tvp,ti in data['thing']['z_t'][ propf ]['z']['z_o']" >
													<td>{{ tvp }}</td>
													<td><input type="text" v-model="data['thing']['z_t'][ propf ]['z']['z_t'][ tvp ]['l']" class="form-control form-control-sm" ></td>
													<td>
														<div>
															<select v-model="data['thing']['z_t'][ propf ]['z']['z_t'][ tvp ]['t']" class="form-select form-select-sm" >
																<option value="T" >Text</option>
																<option value="GT" >Thing Link</option>
																<option value="N" >Number</option>
																<option value="D" >Date</option>
															</select>
														</div>
													</td>
													<td>
														<div><input type="button" class="btn btn-light btn-sm py-0" value="&uarr;" v-on:click="object_field_moveup(propf,tvp)" ></div>
													</td>
													<td>
														<div><input type="button" class="btn btn-light btn-sm py-0" value="&darr;" v-on:click="object_field_movedown(propf,tvp)" ></div>
													</td>
													<td>
														<div class="btn btn-light btn-sm py-0 text-danger" v-on:click="object_field_delete(propf,tvp)" ><i class="fa-regular fa-trash-can"></i></div>
													</td>
												</tr>
											</tbody>
										</table>
										<div><div class="btn btn-outline-dark btn-sm py-0" v-on:click="add_object_field(propf)" >+</div></div>
									</div>
								</div>
							</td>
						</tr>
						</template>
						</template>
						</tbody>
					</table>
					<table class="table table-bordered table-sm w-auto" >
						<tbody>
						<tr>
							<td>New Field</td>
							<td>
								<inputtextbox2 types="T,GT" v-bind:v="data['new_field']['name']" v-bind:datavar="'ref:'+refname+':data:new_field:name'" ></inputtextbox2>
							</td>
							<td>
								<input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="add_field()" >
							</td>
							<td></td>
							<td></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>

			<input type="button" class="btn btn-outline-dark btn-sm" style="float:right;" value="Create Object" v-on:click="create_object()">

			<div v-if="data['msg']" v-html="data['msg']" style="color:blue;" ></div>
			<div v-if="data['err']" v-html="data['err']" style="color:red;"  ></div>
			<div v-if="new_object_id" style="color:blue;" >Success. New Object Id: <a href="#" class="btn btn-outline-dark btn-sm py-0" v-on:click.prevent.stop="getlink2()" >{{ new_object_id }}</a></div>

			<p>&nbsp;-</p><p>&nbsp;-</p>

	</div>`
};
</script>