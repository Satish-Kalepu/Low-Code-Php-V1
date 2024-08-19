<script>
const object_template_create =  {
	data(){
		return {
			"thing": {
				"l": {
					"t": "T", "v": ""
				},
				"i_of": {
					"t": "GT", "v": "", "i": "",
				},
				"i_t": {"t": "T", "v": "N"},
				"z_t": {
					"p1": {"key": "p1", "name": {"t":"T", "v": "Description"}, "type": {"t":"KV", "k":"T", "v":"text"} }
				},
				"z_o": ["p1"],
				"z_n": 2,
				"props": {},
			},
			"thing_i_of": {},
			"vtemplate": {
				"l": "",
				"i": "",
				"z_o": [],
				"z_t": {},
				"z_n": -1
			},
			"template_edit": -1,
			"new_field": {"key": "p1", "name": {"t":"T", "v": ""}, "type": {"t":"KV", "k":"T", "v":"text"} },
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
		}
	},
	props: ["ref", "refname"],
	watch: {
	},
	mounted: function(){
		//this.load_template();
	},
	methods: {
		load_template: function(){
			axios.post("?", {
				"action": "objects_load_template",
				"object_id": this.thing['i_of']['i'],
			}).then(response=>{
				this.import_msg = "";
				if( typeof( response.data['data'] )=="object" ){
					//this.thing['props'] = {};
					this.thing_i_of['z'] = response.data['data'];
				}else{
					this.import_err = "Template not found";
				}
			}).catch(error=>{
				this.import_err = error.message;
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
			if( this.new_field['name']['v'].trim() == "" ){
				alert("need field name");return;
			}
			var np = "p1";
			for(var i=Number(this.thing['z_n']);i<999;i++){
				var np = "p"+i;
				if( np in this.thing['z_t'] == false ){
					this.thing['z_n'] = i+1;
					break;
				}
			}
			this.new_field['key'] = np;
			this.thing['z_t'][ np ] = JSON.parse(JSON.stringify(this.new_field));
			this.thing['z_o'].push( np );
			this.thing['z_n'] = this.thing['z_n']+0;
			this.new_field = {
				"key": np,
				"name": {"t":"T", "v":""}, 
				"type": {"t":"KV", "v":"Text", "k":"T"}, 
				"i_of": {"t":"GT", "i": "", "v": ""}
			};
		},
		create_object: function(){
			this.msg = "";this.err = "";
			axios.post("?", {
				"action": "objects_create_with_template",
				"thing": this.thing
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.new_object_id = response.data['inserted_id'];
								this.$root.popup_callback__({"object_id":response.data['inserted_id'], "label":this.thing['l']['v']} );
								this.thing['l']['v'] = "";
								this.thing['z_t'] = {
									"p1": {"key": "p1", "name": {"t":"T", "v": "Description"}, "type": {"t":"KV", "k":"T", "v":"text"} }
								};
								this.thing["z_o"] = ["p1"];
								this.thing["z_n"] = 2;
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
			var i = this.vtemplate['z_o'].indexOf( vprop );
			this.thing['z_o'].splice( i, 1 );
			this.echo__( this.thing['z_o'] );
			delete(this.thing['z_t' ][ vprop ]);
		},
		getlink2: function(){
			this.$root.show_thing(this.new_object_id);
		},
		moveup: function(vf){
			var i = this.thing['z_o'].indexOf(vf);
			if( i > 0 ){
				var x = this.thing['z_o'].splice(i,1);
				this.thing['z_o'].splice(i-1,0,x[0]);
			}
		},
		movedown: function(vf){
			var i = this.thing['z_o'].indexOf(vf);
			if( i < this.thing['z_o'].length-1 ){
				var x = this.thing['z_o'].splice(i,1);
				this.thing['z_o'].splice(i+1,0,x[0]);
			}
		},
		add_object_field: function( ef ){
			this.thing['z_t'][ ef ]['z']['z_n']++;
			var n = this.thing['z_t'][ ef ]['z']['z_n']+0;
			var new_p = "p"+n;
			this.thing['z_t'][ ef ]['z']['z_t'][ new_p ] = {
				"l": "Field "+n,
				"t": "T"
			};
			this.thing['z_t'][ ef ]['z']['z_o'].push( new_p );
		},
		object_field_delete: function(ef,vf){
			var i = this.thing['z_t'][ ef ]['z']['z_o'].indexOf(vf);
			this.thing['z_t'][ ef ]['z']['z_o'].splice(i,1);
			delete(this.thing['z_t'][ ef ]['z']['z_t'][ vf ]);
		},
		object_field_moveup: function(ef,vf){
			var i = this.thing['z_t'][ ef ]['z']['z_o'].indexOf(vf);
			if( i > 0 ){
				var x = this.thing['z_t'][ ef ]['z']['z_o'].splice(i,1);
				this.thing['z_t'][ ef ]['z']['z_o'].splice(i-1,0,x[0]);
			}
		},
		object_field_movedown: function(ef,vf){
			var i = this.thing['z_t'][ ef ]['z']['z_o'].indexOf(vf);
			if( i < this.thing['z_t'][ ef ]['z']['z_o'].length-1 ){
				var x = this.thing['z_t'][ ef ]['z']['z_o'].splice(i,1);
				this.thing['z_t'][ ef ]['z']['z_o'].splice(i+1,0,x[0]);
			}
		},
		callback__: function( c ){
			var x = c.split(/\:/g);
			if( x[0] == "z_type_change" ){
				if( this.thing['z_t'][ x[1] ]['type']['k'] == "GT" ){
					this.thing['z_t'][ x[1] ]['i_of'] = {"t": "GT", "v": "", "i": ""};
				}else if( this.thing['z_t'][ x[1] ]['type']['k'] == "O" ){
					this.thing['z_t'][ x[1] ]['z'] = {
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
					delete(this.thing['z_t'][ x[1] ]['z']);
				}
			}else if( x[0] == "i_of_select" ){
				delete(this.thing_i_of['z']);
				setTimeout(this.load_template,100);
			}else{
				console.log("Callback: " + c + " not defined!");
			}
		},
		add_sub: function(propf,vi){
			if( propf in this.thing['props'] == false ){
				this.thing['props'][ propf ] = [];
			}
			var v = {'t':"T", "v":""};
			this.thing['props'][ propf ].push( v );
		},
		del_sub: function(propf,vi){
			if( confirm("Are you sure?") ){
				this.thing['props'][ propf ].splice(vi,1);
			}
		},
		add_sub_object: function( propf ){
			var o = {};
			var propd = this.thing_i_of['z']['z_t'][ propf ];
			for(var tdi in propd['z']['z_t']){
				o[ tdi ] = {"t":"T", "v":""};
			}
			this.thing['props'][ propf ].push( o );
		},
		del_sub_object: function( propf, vi ){
			this.thing['props'][ propf ].splice( vi,1 );
		},
	},
	template: `<div class="code_line" >
			<div style="display:flex; column-gap:20px;margin-bottom:10px;" >
				<div class="code_line">
					<div>Instance</div>
					<div title="Thing" data-type="dropdown" v-bind:data-var="'ref:'+refname+':thing:i_of:v'" data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" v-bind:data-context-callback="refname+':i_of_select'" >{{ thing['i_of']['v'] }}</div>
				</div>
				<div class="code_line">
					<div>Node Name</div>
					<div><inputtextbox2 types="T" v-bind:v="thing['l']" v-bind:datavar="'ref:'+refname+':thing:l'" ></inputtextbox2></div>
				</div>
			</div>
			<div v-if="'z' in thing_i_of" style="border:1px solid #ccc; margin-bottom:10px;" >
				<div style="background-color:#f0f0f0; padding:5px;">Properties of "{{ thing['i_of']['v'] }}"</div>
				<div style="padding:5px;" >
				<table class="table table-bordered table-sm w-auto" >
					<tbody v-if="'length' in thing_i_of['z']['z_t']==false">
					<template v-for="propf,fi in thing_i_of['z']['z_o']" >
					<template v-if="propf in thing_i_of['z']['z_t']" >
					<tr>
						<td>{{ propf }}</td>
						<td>{{ thing_i_of['z']['z_t'][ propf ]['l']['v'] }}</td>
						<td>
							<template v-if="thing_i_of['z']['z_t'][ propf ]['t']['k']=='O'" >
								<table class="table table-bordered table-striped table-sm w-auto customborder2" >
									<tbody>
										<tr>
											<td>-</td>
											<td v-for="tdv in thing_i_of['z']['z_t'][ propf ]['z']['z_o']" >{{ thing_i_of['z']['z_t'][ propf ]['z']['z_t'][ tdv ]['l'] }}</td>
										</tr>
										<tr v-for="pvv,pii in thing['props'][ propf ]" >
											<td><input type="button" class="btn btn-outline-danger btn-sm py-0" style="padding:0px;width:20px;" value="X" v-on:click="del_sub_object(propf,pii)" ></td>
											<td v-for="tdv in thing_i_of['z']['z_t'][ propf ]['z']['z_o']" >
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
								<div v-if="propf in thing['props']==false" >
									<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_sub(propf,0)" ></div>
								</div>
								<div v-else >
									<div v-for="pvv,pii in thing['props'][ propf ]" style="display:flex; column-gap:5px; border-bottom:1px dashed #ccc; align-items:center;" >
										<div><input type="button" class="btn btn-outline-danger btn-sm py-0"  style="padding:0px;width:20px;" value="x" v-on:click="del_sub(propf,pii)" ></div>
										<inputtextbox linkable="true" v-bind:v="pvv" v-bind:datavar="'ref:'+refname+':thing:props:'+propf+':'+pii" ></inputtextbox>
									</div>
									<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+"  style="padding:0px;width:20px;" v-on:click="add_sub(propf,pii)" ></div>
								</div>
							</template>
						</td>
					</tr>
				</table>
				</div>
			</div>
			<div style="border:1px solid #ccc; margin-bottom:10px;" >
				<div style="background-color:#f0f0f0; padding:5px;">Template for "{{ thing['l']['v'] }}"</div>
				<div style="padding:5px;" >

					<table class="table table-bordered table-sm w-auto" >
						<tbody v-if="'length' in thing['z_t']==false">
						<template v-for="propf,fi in thing['z_o']" >
						<template v-if="propf in thing['z_t']" >
						<tr>
							<td><div>{{ propf }}</div></td>
							<td>
								<inputtextbox2 types="T,GT" v-bind:v="thing['z_t'][ propf ]['name']" v-bind:datavar="'ref:'+refname+':thing:z_t:'+propf+':name'" ></inputtextbox2>
							</td>
							<td>
								<div title="DataType" data-type="dropdown" v-bind:data-var="'ref:'+refname+':thing:z_t:'+propf+':type'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" v-bind:data-context-callback="refname+':z_type_change:'+propf" >{{ thing['z_t'][propf]['type']['v'] }}</div>
								<div v-if="thing['z_t'][propf]['type']['k']=='GT'&&'i_of' in thing['z_t'][ propf ]" >
									<inputtextbox2 types="GT" v-bind:v="thing['z_t'][ propf ]['i_of']" v-bind:datavar="'ref:'+refname+':thing:z_t:'+propf+':i_of'" ></inputtextbox2>
								</div>
							</td>
							<td>
								<div><input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="delete_field(propf)" ></div>
							</td>
							<td>
								<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&uarr;" v-on:click="moveup(propf)" ></div>
							</td>
							<td>
								<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&darr;" v-on:click="movedown(propf)" ></div>
							</td>
						</tr>
						<tr v-if="thing['z_t'][ propf ]['type']['k']=='O'&&'z' in thing['z_t'][ propf ]" >
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
												<tr v-for="tvp,ti in thing['z_t'][ propf ]['z']['z_o']" >
													<td>{{ tvp }}</td>
													<td><input type="text" v-model="thing['z_t'][ propf ]['z']['z_t'][ tvp ]['l']" class="form-control form-control-sm" ></td>
													<td>
														<div>
															<select v-model="thing['z_t'][ propf ]['z']['z_t'][ tvp ]['t']" class="form-select form-select-sm" >
																<option value="T" >Text</option>
																<option value="GT" >Thing Link</option>
																<option value="N" >Number</option>
																<option value="D" >Date</option>
															</select>
														</div>
													</td>
													<td>
														<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&uarr;" v-on:click="object_field_moveup(propf,tvp)" ></div>
													</td>
													<td>
														<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="&darr;" v-on:click="object_field_movedown(propf,tvp)" ></div>
													</td>
													<td>
														<div><input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="object_field_delete(propf,tvp)" ></div>
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
								<inputtextbox2 types="T,GT" v-bind:v="new_field['name']" v-bind:datavar="'ref:'+refname+':new_field:name'" ></inputtextbox2>
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

			<div v-if="msg" v-html="msg" style="color:blue;" ></div>
			<div v-if="new_object_id" style="color:blue;" >Success. New Object Id: <a href="#" class="btn btn-outline-dark btn-sm py-0" v-on:click.prevent.stop="getlink2()" >{{ new_object_id }}</a></div>
			<div v-if="err" v-html="err" style="color:red;" ></div>

			<div class="code_line">
				<div align="right"><input type="button" class="btn btn-outline-dark btn-sm" value="Create Object" v-on:click="create_object()"></div>
			</div>

	</div>`
};
</script>