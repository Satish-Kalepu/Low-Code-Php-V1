<script>
const object_ops = {
	data(){
		return {
			"instance_type": {"N": "Node", "L": "DataSet", "D": "Document", "M": "Media"},
		}
	},
	props: ["ref", "refname", "data"],
	watch: {
	},
	mounted: function(){
		this.vreset();
	},
	methods: {
		vreset: function(){
			var d = {
				"op": "",
				"source": {"t":"GT", "i": "", "v":""},
				"thing": {},
				"to_type": "",
				"msg": "", "err":"",
				"pmsg": "", "perr":"", "pfails": {},
				"label_to":{"t":"T", "v":"Label"},
				"primary_field": "default-id",
				"label_field": "",
				"alias_field": [],
				"pipeline": [],
			};
			this.$root.window_tabs[ this.refname+'' ]['data'] = d;
			setTimeout(this.set1,200);
		},
		set1: function(){
			this.data['op'] = "BulkUpdate";
			this.data['pipeline'] = [
				{"t": ""},
				{"t": ""},
				{"t": ""}
			];
			setTimeout(this.set2,200);
		},
		set2: function(){
			this.data['pipeline'][0]['t'] = "Find";
			this.pipe_type_changed(0);
		},
		echo__: function(v__){
			if( typeof(v__)=="object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
		callback__: function( c ){
			var x = c.split(/\:/g);
			console.log( x );
			if( x[0] == "source" ){
				this.load_thing();
			}else if( x[0] == "to_type" ){
				this.check_to_type();
			}else if( x[0] == "pipe_type" ){
				console.log( "Pipe type changed: " + x[1] );
				this.pipe_type_changed( Number(x[1]) );
			}else if( x[0] == "find_object_select" ){
				console.log( "find_object_select: " + x[1] );
				this.pipe_find_object_select( Number(x[1]) );
			}else{
				console.log("Callback: " + c + " not defined!");
			}
		},
		load_thing: function(){
			this.data['msg'] = "Loading...";
			this.data['thing'] = {};
			this.data['records'] = [];
			axios.post("?",{
				"action": "objects_load_object",
				"object_id": this.data['source']['i']
			}).then(response=>{
				this.data['msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								var v = response.data['data'];
								this.data['thing'] = v;
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
		check_to_type: function(){
			if( this.data['thing']['i_t']['v'] == this.data['to_type'] ){
				this.data['to_type'] = "";
			}else{
				console.log( this.data['to_type'] );
			}
		},
		getlink: function(vi){
			this.$root.show_thing(vi);
		},
		proceed1: function(){
			this.data['perr'] = "";
			this.data['pmsg'] = "";
			if( this.data['op'] == "Convert" ){
				if( this.data['label_to']['v'].match(/^[a-z0-9\ \.\,\-\_]{2,100}$/i) == null ){
					this.data['perr'] = "Label property name required and should be simple";return ;
				}
				this.data['pmsg'] = "Converting...";
				axios.post("?",{
					"action": "objects_ops_convert_to_dataset",
					"source_object_id": this.data['source']['i'],
					"to_type": this.data['to_type'],
					"label_to": this.data['label_to']['v'],
				}).then(response=>{
					this.data['pmsg'] = "";
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.data['pmsg'] = "Successfully Converted";
									setTimeout(this.vreset,10000);
								}else{
									this.data['perr'] = response.data['error'];
								}
							}else{
								this.data['perr'] = "Incorrect response";
							}
						}else{
							this.data['perr'] = "Incorrect response";
						}
					}else{
						this.data['perr'] = "http error: " . response.status ;
					}
				}).catch(error=>{
					this.data['perr'] = get_http_error__(error);
				});
			}
		},
		proceed2: function(){
			this.data['perr'] = "";
			this.data['pmsg'] = "";
			if( this.data['op'] == "Convert" ){
				if( this.data['primary_field'] == "" ){
					this.data['perr'] = "Need Node ID";return ;
				}
				if( this.data['label_field'] == "" ){
					this.data['perr'] = "Need Label Field";return ;
				}
				if( this.data['alias_field'].length > 1 ){
					this.data['perr'] = "Only one alias is accepted";return ;
				}
				if( this.data['primary_field'] == this.data['label_field'] ){
					this.data['perr'] = "Node ID and Label should be differrent";return ;
				}
				if( this.data['alias_field'].length > 0 ){
					if( this.data['label_field'] == this.data['alias_field'][0] ){
						this.data['perr'] = "Label and alias should be differrent";return ;
					}
				}
				this.data['pmsg'] = "Converting...";
				axios.post("?",{
					"action": "objects_ops_convert_to_nodelist",
					"source_object_id": this.data['source']['i'],
					"to_type": this.data['to_type'],
					"primary_field": this.data['primary_field'],
					"label_field": this.data['label_field'],
					"alias_field": this.data['alias_field'],
				}).then(response=>{
					this.data['pmsg'] = "";
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.data[ 'pmsg'   ] = "Successfully Converted. Success: " + response.data['success'] + "; Failed: " + response.data['failed'] + ";";
									this.data[ 'pfails' ] = response.data['failed_reasons'];
									setTimeout(this.vreset, 10000);
								}else{
									this.data['perr'] = response.data['error'];
								}
							}else{
								this.data['perr'] = "Incorrect response";
							}
						}else{
							this.data['perr'] = "Incorrect response";
						}
					}else{
						this.data['perr'] = "http error: " . response.status ;
					}
				}).catch(error=>{
					this.data['perr'] = get_http_error__(error);
				});
			}
		},
		check_alias: function(fd){
			setTimeout(this.check_alias2,100,fd);
		},
		check_alias2: function(fd){
			this.data['alias_field'] = [fd+''];
		},
		check_label: function(fd){
			setTimeout(this.check_label2,100,fd);
		},
		check_label2: function(fd){
			if( this.data['alias_field'].length == 1 ){
				if( this.data['label_field'] == fd && this.data['alias_field'][0] == fd ){
					this.data['alias_field'] = [];
				}
			}
		},
		check_primary: function(fd){
			setTimeout(this.check_primary2,100,fd);
		},
		check_primary2: function(fd){
			if( this.data['primary_field'] == fd && this.data['label_field'] == fd ){
				this.data['label_field'] = "";
			}
		},
		pipe_add: function(vpos){
			var d = {
				"t": "", 
				"d": {}
			};
			if( vpos== -1 ){
				this.data['pipeline'].push(d);
			}else{
				this.data['pipeline'].splice(vpos, d);
			}
		},
		pipe_del: function(vpos){
			this.data['pipeline'].splice( vpos, 1 );
		},
		pipe_move_up: function(vpos){
			if( vpos > 0 ){
				var t = JSON.parse(JSON.stringify(this.data['pipeline'].splice(vpos,1) ));
				this.data['pipeline'].splice(vpos-1,0,t[0]);
			}
		},
		pipe_move_down: function(vpos){
			if( vpos < this.data['pipeline'].length-1 ){
				var t = JSON.parse(JSON.stringify(this.data['pipeline'].splice(vpos,1) ));
				this.data['pipeline'].splice(vpos+1,0,t[0]);
			}
		},
		pipe_type_changed: function(pi){
			if( this.data['pipeline'][ Number(pi) ]['t'] == 'Find' ){
				this.data['pipeline'][ Number(pi) ]['d'] = {
					"by": "Instance",
					"object": {"t":"GT", "i": "", "v":""},
					"object_msg": "","object_err": "",
					"sort": "Label", "order": "Asc",
					"filter": [
						{"field": {"t":"KV", "k":"","v":""}, "op": "", "value":{"t":"T", "v":""} },
					],
					"project": [
						{"field": {"t":"KV", "k":"_id","v":"ID"} },
					],
					"thing": {},
					"output": "",
				}
			}
		},
		pipe_find_filter_add: function(pi){
			this.data['pipeline'][ pi ]['d']['filter'].push({"field": {"t":"KV", "k":"","v":""}, "op": "", "value":{"t":"T", "v":""} });
		},
		pipe_find_filter_del: function(pi,fi){
			//if( this.data['pipeline'][ pi ]['d']['filter'].length > 1 )
			{
				this.data['pipeline'][ pi ]['d']['filter'].splice(fi,1);
			}
		},
		pipe_find_project_add: function(pi){
			this.data['pipeline'][ pi ]['d']['project'].push({"field": {"t":"KV", "k":"","v":""} });
		},
		pipe_find_project_del: function(pi,fi){
			if( this.data['pipeline'][ pi ]['d']['project'].length > 1 ){
				this.data['pipeline'][ pi ]['d']['project'].splice(fi,1);
			}
		},
		pipe_find_object_select: function(pi){
			this.data['pipeline'][ pi ]['d']['object_msg'] = "Loading...";
			this.data['pipeline'][ pi ]['d']['object_err'] = "";
			this.data['pipeline'][ pi ]['d']['thing'] = {};
			axios.post("?",{
				"action": "objects_load_object",
				"object_id": this.data['pipeline'][ pi ]['d']['object']['i']
			}).then(response=>{
				this.data['pipeline'][ pi ]['d']['object_msg'] = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								var v = response.data['data'];
								this.data['pipeline'][ pi ]['d']['thing'] = v;
								setTimeout(this.pipe_find_verify_thing,200, pi);
								setTimeout(function(v){v.data['pipeline'][ pi ]['d']['object_msg'] = "";},200,this);
							}else{
								this.data['pipeline'][ pi ]['d']['object_err'] = response.data['error'];
							}
						}else{
							this.data['pipeline'][ pi ]['d']['object_err'] = "Incorrect response";
						}
					}else{
						this.data['pipeline'][ pi ]['d']['object_err'] = "Incorrect response";
					}
				}else{
					this.data['pipeline'][ pi ]['d']['object_err'] = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.data['pipeline'][ pi ]['d']['object_err'] = get_http_error__(error);
			});
		},
		pipe_find_verify_thing: function(pi){
			if( 'z_t'  in this.data['pipeline'][ pi ]['d']['thing'] == false  || 'cnt' in this.data['pipeline'][ pi ]['d']['thing'] == false ){
				this.data['pipeline'][ pi ]['d']['object_err'] = "Template not defined Or there are no sub objects";
			}else{
				this.data['pipeline'][ pi ]['d']['object_msg'] = this.data['pipeline'][ pi ]['d']['thing']['cnt'] + " Nodes ";
				var pd = [
					{"t":"KV", "k":'_id', "v":'ID'}
				];
				if( this.data['pipeline'][ pi ]['d']['thing']['i_t']['v']=="N" ){
					pd.push( {"t":"KV", "k":'l', "v":"Label"} );
				}
				if( this.data['pipeline'][ pi ]['d']['thing']['i_t']['v']=="N" ){
					pd.push( {"t":"KV", "k":'al', "v":"Alias"} );
				}
				for(var rd in this.data['pipeline'][ pi ]['d']['thing']['z_t'] ){
					pd.push( {"t":"KV", "k":'props.'+rd+'', "v":'props.'+rd+':'+this.data['pipeline'][ pi ]['d']['thing']['z_t'][rd]['l']['v']+''} );
				}
				this.$root.context_data__[ "ops_find_fields" ] = pd;
			}
			this.$root.context_data__[ "ops_find_operator" ] = {
				"=": "=", "!=": "!=", ">": ">", ">=": ">=", "<": "<", "<=": "<=" 
			};
		},
	},
	template: `<div class="code_line" v-if="'source' in data" >

		<p>Operation: <div title="Operation to process" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:op'" data-list="list" data-list-values="Convert,BulkUpdate" >{{ data['op'] }}</div></p>
		<template v-if="data['op']=='Convert'" >

			<div style="display:flex; column-gap:10px; margin-bottom:10px;">
				<div>Source Object: </div>
				<div><div title="Thing" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:source:v'" data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" v-bind:data-context-callback="refname+':source'" >{{ data['source']['v'] }}</div></div>

				<template v-if="'i_t' in data['thing']" >
					<div><div>Type: <span v-if="data['thing']['i_t']['v'] in instance_type" >{{ instance_type[ data['thing']['i_t']['v'] ] }}</span><span v-else>{{ data['thing']['i_t']['v'] }}</span></div></div>
					<div><div>Instance Of: <div class="btn btn-light btn-sm py-1" v-on:click="getlink(data['thing']['i_of']['i'])" >{{ data['thing']['i_of']['v'] }}</div></div></div>
					<div><div>Nodes: <span v-if="'cnt' in data['thing']" >{{ data['thing']['cnt'] }}</span><spanv v-else>No Nodes</span></div></div>
				</template>

			</div>

			<div v-if="data['msg']" style="color:blue;" >{{ data['msg'] }}</div>
			<div v-if="data['err']" style="color:red;" >{{ data['err'] }}</div>

			<template v-if="'i_t' in data['thing']" >

				<div>Change To: 
					<div title="Node Type" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:to_type'" data-list="list-assoc" data-list-values="node-type" v-bind:data-context-callback="refname+':to_type'" ><span v-if="data['to_type'] in instance_type" >{{ instance_type[ data['to_type'] ] }}</span><span v-else>{{ data['to_type'] }}</span></div>
				</div>

				<template v-if="data['thing']['i_t']['v']=='N'&&data['to_type']=='L'" >
					<div>Convertion from Node to DataSet</div>
					<div>Unique label of Node becomes a Non-Unique Property in DataSet</div>
					<table class="table table-bordered table-sm w-auto" >
					<tbody>
						<tr>
							<td>Current Schema</td>
							<td>New Schema</td>
						</tr>
						<tr>
							<td>
								<table class="table table-bordered table-sm w-auto" >
								<tbody>
									<tr><td>Node</td><td>Label</td></tr>
									<tr v-for="fd in data['thing']['z_o']" >
										<td>{{ fd }}</td><td>{{ data['thing']['z_t'][ fd ]['l']['v'] }}</td>
									</tr>
								</tbody>
								</table>
							</td>
							<td>
								<table class="table table-bordered table-sm w-auto" >
								<tbody>
									<tr>
										<td>New Property</td><td>
										<div title="Text" class="editable" v-bind:data-var="'ref:'+refname+':data:label_to:v'"  ><div style="white-space:nowrap;" contenteditable spellcheck="false" data-type="editable" v-bind:data-var="'ref:'+refname+':data:label_to:v'" v-bind:id="'ref:'+refname+':label_to:v'" data-allow="T" >{{ data['label_to']['v'] }}</div></div>
									</td></tr>
									<tr v-for="fd in data['thing']['z_o']" >
										<td>{{ fd }}</td><td>{{ data['thing']['z_t'][ fd ]['l']['v'] }}</td>
									</tr>
								</tbody>
								</table>
							</td>
						</tr>
						</tbody>
					</table>
					<div>
						<div class="btn btn-outline-dark btn-sm" v-on:click="proceed1()" >Proceed</div>
					</div>
					<div v-if="data['pmsg']" style="color:blue;" >{{ data['pmsg'] }}</div>
					<div v-if="data['perr']" style="color:red;"  >{{ data['perr'] }}</div>
				</template>

				<template v-if="data['thing']['i_t']['v']=='L'&&data['to_type']=='N'" >
					<div>Convertion from Dataset to Node List</div>
					<div>Node needs a Unique label which should be taken from one of the properties of dataset.</div>
							<table class="table table-bordered table-sm w-auto" >
							<tbody>
								<tr>
									<td>PropKey</td>
									<td>Name</td>
									<td>NodeID</td>
									<td>Label</td>
									<td>Alias</td>
								</tr>
								<tr>
									<td>ID</td>
									<td>Auto</td>
									<td align="center"><input type="radio" v-model="data['primary_field']" value="default-id" ></td>
									<td></td>
									<td></td>
								</tr>
								<tr v-for="fd in data['thing']['z_o']" >
									<td>{{ fd }}</td>
									<td>{{ data['thing']['z_t'][ fd ]['l']['v'] }}</td>
									<td align="center"><input type="radio" v-model="data['primary_field']" v-bind:value="fd" v-on:click="check_primary(fd)" ></td>
									<td align="center"><input v-if="data['primary_field']!=fd" type="radio" v-model="data['label_field']" v-bind:value="fd" v-on:click="check_label(fd)" ></td>
									<td align="center"><input type="checkbox" v-model="data['alias_field']" v-bind:value="fd" v-on:click="check_alias(fd)" ></td>
								</tr>
							</tbody>
							</table>
					<div>
						<div class="btn btn-outline-dark btn-sm" v-on:click="proceed2()" >Proceed</div>
					</div>
					<div v-if="data['pmsg']" style="color:blue;" >{{ data['pmsg'] }}</div>
					<div v-if="data['perr']" style="color:red;"  >{{ data['perr'] }}</div>
					<ul v-if="Object.keys(data['pfails']).length>0" >
						<li v-for="td,ti in data['pfails']" >{{ td }}</li>
					</ul>
				</template>

			</template>
		</template>
		<template v-if="data['op']=='BulkUpdate'" >

			<p>Pipeline</p>
			<div v-for="pipe,pi in data['pipeline']" style="display:flex; border:1px solid #ccc; margin-bottom:10px;" >
				<div style="padding:5px; border-right:1px solid #ccc; min-width:30px; background-color:#f8f8f8; text-align:right;">{{ pi+1 }}</div>
				<div style="padding:5px; border-right:1px solid #ccc; min-width:30px; background-color:#f8f8f8; text-align:right;">
					<div class="btn btn-outline-dark btn-sm py-0" v-on:click="pipe_add(pi)" >+</div>
				</div>
				<div style="padding:5px; border-right:1px solid #ccc; background-color:#f8f8f8; text-align:right;">
					<div class="btn btn-outline-danger btn-sm py-1" v-on:click="pipe_del(pi)" ><i class="fa-regular fa-trash-can"></i></div>
				</div>
				<div style="padding:5px; border-right:1px solid #ccc; background-color:#f8f8f8; text-align:right;">
					<div class="btn btn-outline-dark btn-sm py-0" v-on:click="pipe_move_up(pi)" >&uarr;</div>
				</div>
				<div style="padding:5px; border-right:1px solid #ccc; background-color:#f8f8f8; text-align:right;">
					<div class="btn btn-outline-dark btn-sm py-0" v-on:click="pipe_move_down(pi)" >&darr;</div>
				</div>
				<div style="padding:5px; border-right:1px solid #ccc; min-width:100px;">
					<div title="Stage Type" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:pipeline:'+pi+':t'" data-list="list" data-list-values="Find,UnWind,Update,Merge,Delete,Log" v-bind:data-context-callback="refname+':pipe_type:'+pi" >{{ pipe['t'] }}</div>
				</div>
				<div style="padding:5px;">
					<template v-if="pipe['t']=='Find'" >
						<template v-if="'by' in pipe['d']" >
							<div>Find By: <div title="Find By" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:pipeline:'+pi+':d:by'" data-list="list" data-list-values="Instance,Node,ID" v-bind:data-context-callback="refname+':find_by:'+pi" >{{ pipe['d']['by'] }}</div>  </div>
							<template v-if="pipe['d']['by']=='Instance'" >
								<div>Instance: <div title="Instance" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:pipeline:'+pi+':d:object:v'" data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" v-bind:data-context-callback="refname+':find_object_select:'+pi" >{{ pipe['d']['object']['v'] }}</div></div>
								<div v-if="pipe['d']['object_msg']" style="color:blue;" >{{ pipe['d']['object_msg'] }}</div>
								<div v-if="pipe['d']['object_err']" style="color:red;"  >{{ pipe['d']['object_err'] }}</div>
								<div>Sort: <div title="Sort" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:pipeline:'+pi+':d:sort'" data-list="list" data-list-values="ID,Label" v-bind:data-context-callback="refname+':find_sort_select:'+pi" >{{ pipe['d']['sort'] }}</div>  Order: <div title="Sort" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:pipeline:'+pi+':d:order'" data-list="list" data-list-values="Asc,Dsc"  >{{ pipe['d']['order'] }}</div></div>
								<div style="display:flex;">
									<div>Filter: &nbsp;&nbsp;</div>
									<div>
										<div v-for="filterd,filteri in pipe['d']['filter']" style="display:flex; border:1px solid #ccc; " >
											<div style="padding:5px; border-right:1px solid #ccc; background-color:#f8f8f8; text-align:right;">
												<div class="btn btn-light btn-sm text-danger py-1" v-on:click="pipe_find_filter_del(pi,filteri)" ><i class="fa-regular fa-trash-can"></i></div>
											</div>
											<div style="padding:5px; ">
												<div title="Field" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:pipeline:'+pi+':d:filter:'+filteri+':field'" data-list="list-kv" data-list-values="ops_find_fields"  >{{ pipe['d']['filter'][filteri]['field']['v'] }}</div>
											</div>
											<div style="padding:5px; ">
												<div title="Operator" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:pipeline:'+pi+':d:filter:'+filteri+':op'" data-list="list-assoc" data-list-values="ops_find_operator"  >{{ pipe['d']['filter'][filteri]['op'] }}</div>
											</div>
											<div style="padding:5px; ">
												<inputtextbox2 types="T,GT" title="Value" v-bind:datavar="'ref:'+refname+':data:pipeline:'+pi+':d:filter:'+filteri+':value'" v-bind:v="filterd['value']" ></inputtextbox2>
											</div>
										</div>
										<div><div class="btn btn-outline-dark btn-sm py-0" v-on:click="pipe_find_filter_add(pi)" >+</div></div>
									</div>
								</div>
								<div style="display:flex;">
									<div>Projection: &nbsp;&nbsp;</div>
									<div>
										<div v-for="projectd,projecti in pipe['d']['project']" style="display:flex; border:1px solid #ccc; " >
											<div style="padding:5px; border-right:1px solid #ccc; background-color:#f8f8f8; text-align:right;">
												<div class="btn btn-light btn-sm text-danger py-1" v-on:click="pipe_find_project_del(pi,projecti)" ><i class="fa-regular fa-trash-can"></i></div>
											</div>
											<div style="padding:5px; ">
												<div title="Field" data-type="dropdown" v-bind:data-var="'ref:'+refname+':data:pipeline:'+pi+':d:project:'+projecti+':field'" data-list="list-kv" data-list-values="ops_find_fields"  >{{ pipe['d']['project'][projecti]['field']['v'] }}</div>
											</div>
										</div>
										<div><div class="btn btn-outline-dark btn-sm py-0" v-on:click="pipe_find_project_add(pi)" >+</div></div>
									</div>
								</div>
								<div>Output Var Name: <inputtextbox2 types="T" title="Value" v-bind:datavar="'ref:'+refname+':data:pipeline:'+pi+':d:output'" v-bind:v="pipe['d']['output']" ></inputtextbox2></div>
							</template>
							<div>Find fetches data in batches. 10 nodes per batch</div>
						</template>
						<div v-else>Object Error 1</div>
					</template>
					<template v-else>
						<pre>{{ pipe }}</pre>
					</template>
						
				</div>
			</div>
			<div><div class="btn btn-outline-dark btn-sm" v-on:click="pipe_add(-1)" >+</div></div>

		</template>

		<p>&nbsp;-</p><p>&nbsp;-</p>
	</div>`
};
</script>