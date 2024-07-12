const graph_object =  {
	data(){
		return {
			"saveit": false,
			"cf": false,
		}
	},
	props: ['v', 'temp', 'datavar', 'vars'],
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
			this.v['i_of'] = {'t':"L", 'v':[{
				"t":"GT", 
				"v": {"t":"T","v":""},
				"i":{"t":"T","v":""}
			}]};
		}
		if( "p_of" in this.v == false || typeof(this.v['p_of']) != "object" || "length" in this.v['p_of'] ){
			this.v['p_of'] = {'t':"L", 'v':[{
				"t":"GT", 
				"v": {"t":"T","v":""},
				"i":{"t":"T","v":""}
			}]};
		}
		if( "props" in this.v == false || typeof(this.v['props']) != "object" || "length" in this.v['props'] ){
			this.v['props'] = {"t": "O", "v": {
				"c_date":{
					"t":"L", "v": [{"t":"T", "v": "ss"}]
				},
				"u_date":{
					"t":"L", "v": [{"t":"T", "v": "ss"}]
				},
			}};
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
			this.cf=false;
		},
		add_sub: function(vv,vk){
			if( this.v[vv]['v'][ vk ]['v'].length>0 ){
				var v = JSON.parse(JSON.stringify( this.v[vv]['v'][ vk ]['v'][0] ));
			}else{
				var v = {'t':"T", "v":""};
			}
			this.v[vv]['v'][ vk ]['v'].push( v );
		},
		del_sub: function(vv,vk,vi){
			this.v[vv]['v'][ vk ]['v'].splice(vi,1);
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

	},
	template: `<div class="code_line">
		<table class="table table-bordered table-sm w-auto" >
			<tr>
				<td>Label</td>
				<td>
					<div>
					<div title="Label" class="editable" v-bind:data-var="datavar+':l'"  ><div contenteditable spellcheck="false" data-type="editable"  v-bind:data-var="datavar+':l'" v-bind:id="datavar+':l'" data-allow="T"   >{{ v['l'] }}</div></div>
					</div>
				</td>
			</tr>
			<tr>
				<td>Instance Of</td>
				<td>
					<template v-if="'i_of' in v" >
						<div style="display:flex;column-gap:10px;" >
							<div v-for="i_of_v,ii in v['i_of']['v']" class="codeline_thing" style="border:1px solid #eee; display:inline-flex; margin-right:10px; " >
								<div>
									<div v-if="typeof(i_of_v)=='object'" title="Thing" data-type="dropdown" v-bind:data-var="datavar+':i_of:v:'+ii" data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" >{{ i_of_v['v']['v'] }}</div>
									<div v-else>i_of_v Undefined</div>
								</div>
								<div v-if="v['i_of']['v'].length>1"><input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="del_i_of(ii)" ></div>
							</div>
							<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="add_i_of()" ></div>
						</div>
					</template>
					<div v-else >Undefined</div>
				</td>
			</tr>
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
		</table>
		<template v-if="'props' in v" >
		<p>Object Properties: </p>
		<table class="table table-bordered table-sm w-auto" >
			<tr v-for="propf in v['z_o']" valign="top">
				<td>
					<span v-if="propf in v['z_t']" >{{ v['z_t'][ propf ]['v'] }}</span>
					<span v-else >{{ propf }}</span>
				</td>
				<td>
					<div v-if="propf in v['props']==false" >
						Field not created
					</div>
					<div v-else >
						<div v-for="pvv,pii in v['props'][ propf ]" style="display:flex; column-gap:5px; border-bottom:1px solid #ccc;" >
							<div v-if="v['props'][ propf ].length>1"><input type="button" class="btn btn-outline-secondary btn-sm py-0"  style="padding:0px 5px;" value="x" v-on:click="del_sub('props',propf,pii)" ></div>
							<div><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="+" style="padding:0px 5px;" v-on:click="add_sub('props',propf)" ></div>
							<inputtextbox v-bind:v="pvv" v-bind:datavar="datavar+':props:'+propf+':'+pii" ></inputtextbox>
						</div>
					</div>
				</td>
			</tr>
		</table>
		</template>
		<div style="clear:both;" ></div>
		<div v-if="cf==false"><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="cf=true" ></div>
		<div v-if="cf" style="border:1px solid #ccc; display:inline-block; min-width:300px; " >
			<div style="background-color:#f8f8f8; padding:5px; border-bottom:1px solid #ccc;" >
				<div style="float:right;" ><input type="button" class="btn btn-outline-secondary btn-sm py-0" value="X" v-on:click="cf=false" ></div>
				<div>Create new Field</div>
			</div>
			<div style="padding:5px; " >
				<div style="display:flex; column-gap:5px;">
					<div><div>Field</div><div><inputtextbox2 types="T" v-bind:v="temp['new_field']" datavar="temp:new_field" ></inputtextbox2></div></div>
					<div><div>Value</div><div><inputtextbox v-bind:v="temp['new_value']" datavar="temp:new_value" ></inputtextbox></div></div>
					<div><div>&nbsp;</div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="create_field()" ></div>
				</div>
			</div>
		</div>
		<div style="clear:both;" >&nbsp;</div>
	</div>`
};