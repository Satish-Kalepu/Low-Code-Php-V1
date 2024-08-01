<script>
const object_template_edit =  {
	data(){
		return {
			"vtemplate": {
				"l": "",
				"i": "",
				"z_o": [],
				"z_t": {},
				"z_n": -1
			},
			"edit_z_t": {}, "edit_z_o": {}, "edit_z_n": 1,
			"template_edit": -1,
			"new_field": {"key": "p1", "name": {"t":"T", "v": ""}, "type": {"t":"KV", "k":"T", "v":"text"} },
			"new_field_id": -1, 
			"edit_field": "",
			"msg": "", "err": "",
			"z_t_msg": "", "z_t_err": "",
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
		}
	},
	props: ["ref", "refname", "object_id"],
	watch: {
		v: {
			handler: function(){
				console.log("updated");
				this.saveit = true;
			}, deep:true
		}
	},
	mounted: function(){
		this.load_template();
	},
	methods: {
		load_template: function(){
			axios.post("?", {
				"action": "objects_load_template",
				"object_id": this.object_id,
			}).then(response=>{
				this.import_msg = "";
				if( typeof( response.data['data'] )=="object" ){
					this.vtemplate['z_t'] = response.data['data']['z_t'];
					this.vtemplate['z_o'] = response.data['data']['z_o'];
					if( "length" in this.vtemplate['z_t'] ){
						this.vtemplate['z_t'] = {};
					}
					this.vtemplate['z_n'] = response.data['data']['z_n'];
					this.vtemplate['i'] = response.data['data']['_id'];
					this.vtemplate['l'] = response.data['data']['l']['v'];
					this.edit_z_t = JSON.parse( JSON.stringify( this.vtemplate['z_t'] ) );
					this.edit_z_n = JSON.parse( JSON.stringify( this.vtemplate['z_n'] ) );
					this.edit_z_o = JSON.parse( JSON.stringify( this.vtemplate['z_o'] ) );
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
			if( this.new_field['type']['v'].trim() == "" ){
				alert("need field type");return;
			}
			var np = "p1";
			for(var i=Number(this.vtemplate['z_n']);i<999;i++){
				var np = "p"+i;
				if( np in this.edit_z_t == false ){
					this.edit_z_n = i+1;
					break;
				}
			}
			this.new_field['key'] = np;
			this.save_new_field();
		},
		save_new_field: function(){
			this.z_t_msg = "";this.z_t_err = "";
			axios.post("?", {
				"action": "objects_object_add_field",
				"object_id": this.vtemplate['i'],
				"field": this.new_field['key'],
				"prop": {
					"l": JSON.parse(JSON.stringify(this.new_field['name'])),
					"t": JSON.parse(JSON.stringify(this.new_field['type'])),
					"e": false,
					"m": false
				},
				"z_n": this.edit_z_n
			}).then(response=>{
				this.z_t_msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.close_edit_field();
								this.z_t_msg = "Saved";
								this.edit_z_t[ this.new_field['key']+'' ] = {
									"l": JSON.parse(JSON.stringify(this.new_field['name'])),
									"t": JSON.parse(JSON.stringify(this.new_field['type'])),
									"e": false,
									"m": false
								};
								this.edit_z_o.push( this.new_field['key']+'' );
								this.vtemplate['z_t'] = JSON.parse(JSON.stringify(this.edit_z_t));
								this.vtemplate['z_o'] = JSON.parse(JSON.stringify(this.edit_z_o));
								this.vtemplate['z_n'] = this.edit_z_n+0;
								this.new_field = {
									"key": "p"+this.edit_z_n,
									"name": {"t":"T", "v":""}, 
									"type": {"t":"KV", "v":"Text", "k":"T"}, 
								};
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
		close_edit_field: function(){
			this.edit_field = '';
			this.vtemplate['z_t'] = JSON.parse(JSON.stringify(this.edit_z_t));
			this.vtemplate['z_o'] = JSON.parse(JSON.stringify(this.edit_z_o));
		},
		save_field: function(){
			var v = this.edit_z_t[ this.edit_field ]['l']['v']+'';
			for( var fd in this.edit_z_t ){
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
				"object_id": this.vtemplate['i'],
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
								this.vtemplate['z_t' ] = JSON.parse(JSON.stringify(this.edit_z_t));
								this.vtemplate['z_o' ] = JSON.parse(JSON.stringify(this.edit_z_o));
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
				"object_id": this.vtemplate['i'],
				"prop": vprop,
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg = "Field is Deleted";
								var i = this.vtemplate['z_o' ].indexOf( this.delete_field_id );
								this.vtemplate['z_o' ].splice( i, 1 );
								delete(this.vtemplate['z_t' ][ this.delete_field_id ]);
								this.$root.edit_z_t = JSON.parse( JSON.stringify(this.vtemplate['z_t']) );
								this.edit_z_o = JSON.parse( JSON.stringify(this.vtemplate['z_o']) );
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
				"object_id": this.vtemplate['i'],
				"z_o": this.edit_z_o,
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg = "Updated";
								this.vtemplate['z_o' ] = JSON.parse( JSON.stringify(this.edit_z_o) );
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
		callback__: function( c ){
			var x = c.split(/\:/g);
			if( x[0] == "z_type_change" ){
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
	},
	template: `<div class="code_line" v-if="vtemplate['l']">
			<div style="padding:10px 0px; background-color:#f8f8f8;">Properties of {{ vtemplate['l'] }}</div>
			<table class="table table-bordered table-sm w-auto" >
				<tbody v-if="'length' in edit_z_t==false">
				<template v-for="propf,fi in edit_z_o" >
				<tr>
					<td><div>{{ propf }}</div></td>
					<td>
						<div v-if="propf in edit_z_t" >
							<div v-if="edit_field==propf" >
								<inputtextbox2 types="T,GT" v-bind:v="edit_z_t[ propf ]['l']" v-bind:datavar="'ref:'+refname+':edit_z_t:'+propf+':l'" ></inputtextbox2>
							</div>
							<div v-else-if="edit_z_t[ propf ]['l']['t']=='T'">{{ edit_z_t[ propf ]['l']['v'] }}</div>
							<div v-else-if="edit_z_t[ propf ]['l']['t']=='GT'"><a href="#" v-on:click.prevent.stop="getlink(edit_z_t[ propf ]['l']['i'])" >{{ edit_z_t[ propf ]['l']['v'] }}</a></div>
						</div>
					</td>
					<td>
						<div v-if="propf in edit_z_t" >
							<div v-if="edit_field==propf" title="DataType" data-type="dropdown" v-bind:data-var="'ref:'+refname+':edit_z_t:'+propf+':t'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" v-bind:data-context-callback="refname+':z_type_change:'+propf" >{{ edit_z_t[propf]['t']['v'] }}</div>
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
							<div v-if="edit_field&&propf==edit_field" >
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
						<inputtextbox2 types="T,GT" v-bind:v="new_field['name']" v-bind:datavar="'ref:'+refname+':new_field:name'" ></inputtextbox2>
					</td>
					<td>
						<div title="DataType" data-type="dropdown" v-bind:data-var="'ref:'+refname+':new_field:type:v'" data-list="list-kv" data-list-values="datatypes-kv" data-list-label="DataTypes" >{{ new_field['type']['v'] }}</div>
					</td>
					<td>
						<input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="add_field()" >
					</td>
					<td></td>
					<td> </td>
				</tr>
				</tbody>
			</table>
			<div v-if="z_t_msg" v-html="z_t_msg" ></div>
			<div v-if="z_t_err" v-html="z_t_err" ></div>

	</div>`
};
</script>