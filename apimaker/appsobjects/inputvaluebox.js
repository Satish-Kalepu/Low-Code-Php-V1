const inputvaluebox = {
	data(){return{
		"data_types__":{
			"T": "Text",
			"TT": "MultiLine Text",
			"N": "Number",
			"D": "Date",
			"DT": "DateTime",
			"TH": "Thing",
			"THL": "Thing List",
			"TI": "Thing Item",
			"L": "List",
			"O": "Assoc List",
			"B": "Boolean",
			"NL": "Null", 
			"BIN": "Binary",
			"V": "Variable",
			"PLG": "Plugin",
		},
		//show: "none",
	}},
	props: ["datafor", "datavar", "v" ],
	mounted(){
		if( this.v['t'] == "O" ){
			if( "length" in this.v['v'] || typeof(this.v['v'])!='object' ){
				console.log("type O incorrect value reset {}");
				this.v['v'] = {};
			}
		}else if( this.v['t'] == "L" ){
			if( "length" in this.v['v'] == false || typeof(this.v['v'])!='object' ){
				console.log("type L incorrect value reset []");
				this.v['v'] = [];
			}
		}
	},
	methods:{
		get_object_notation__(v){
			var vv = {};
			for(var k in v ){
				if( v[k]['t'] == "V" ){
					vv[ k ] = this.data_types__[ v[k]['t'] ] + "["+v[k]['v']['v']+"]";
					if( 'vs' in v[k]['v'] ){
						if( v[k]['v']['vs'] ){
							if( v[k]['v']['vs']['v'] ){
								vv[ k ] = vv[ k ] + '->' + v[k]['v']['vs']['v'];
							}
						}
					}
				}else if( v[k]['t'] == "PLG" ){
					vv[ k ] = this.data_types__[ v[k]['t'] ] + "["+v[k]['v']['v']+"]";
					if( 'vs' in v[k]['v'] ){
						if( v[k]['v']['vs'] ){
							if( v[k]['v']['vs']['v'] ){
								vv[ k ] = vv[ k ] + '->' + v[k]['v']['vs']['v'];
							}
						}
					}
				}else{
					vv[ k ] = this.derive_value__(v[k]);
				}
			}
			return Object.fromEntries(Object.entries(vv).sort());
			return vv;
		},
		derive_value__: function(v ){
			if( v['t'] == "T" || v['t']== "D" ){
				return v['v'];
			}else if( v['t']== "N" ){
				return Number(v['v']);
			}else if( v['t'] == 'O' ){
				return this.get_object_notation__(v['v']);
			}else if( v['t'] == 'L' ){
				return [this.get_object_notation__(v['v'][0])];
			}else if( v['t'] == 'B' ){
				return (v['v']?true:false);
			}else{
				return "unknown";
			}
		},
		getplg: function(){
			if( this.v['t'] == "V" ){ 
				if( this.v['v']['t'] == "PLG" ){ 
					return this.v['v']['plg'];
				}
			}
			return false;
		}
	},
	template:`<div v-bind:class="'codeline_thing codeline_thing_'+v['t']" >
		<div class="codeline_thing_pop" data-type="dropdown2" data-list="inputfactortypes" v-bind:data-for="datafor" v-bind:data-var="datavar+':t'" v-bind:title="data_types__[v['t']]" >{{ v['t'] }}</div>
		<div v-if="v['t']=='GT'" >
			<div v-if="typeof(v['v'])=='object'" title="Thing" data-type="dropdown" v-bind:data-var="datavar" data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" >{{ v['v']['l']['v'] }}</div>
			<div v-else>GT value need object</div>
		</div>
		<div v-else-if="v['t']=='TH'" style="display:flex; gap:10px; " >
			<div title="Thing" 		data-type="dropdown" data-list="things" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:th'" v-bind:data-k-type="dataktype" v-bind:data-plg="dataplg"  v-bind:fn="fn" v-bind:fnparam="fnparam">{{ v['v']['th'] }}</div>
			<div title="ThingItem"	data-type="dropdown" data-list="thing"  v-bind:data-thing="v['th']" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:v'" v-bind:data-k-type="dataktype" v-bind:data-plg="dataplg"  v-bind:fn="fn" v-bind:fnparam="fnparam">{{ v['v']['l'] }}</div>
		</div>
		<div v-else-if="v['t']=='THL'"  title="Thing List Name" class="editable" v-bind:data-var="datavar+':v:th'" v-bind:data-for="datafor" ><div contenteditable placeholder="Thing Name" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:th'" data-allow="T" v-bind:fn="fn" v-bind:fnparam="fnparam" >{{ v['v']['th'] }}</div></div>
		<div v-else-if="v['t']=='TH'" title="Thing" data-type="dropdown" data-list="things" v-bind:data-thing="v['v']['th']" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:v'" v-bind:data-k-type="dataktype" v-bind:data-plg="dataplg" v-bind:fn="fn" v-bind:fnparam="fnparam" >{{ v['v']['v']['l'] }}</div>
		<div v-else-if="v['t']=='PLG'" data-type="dropdown" data-list="plugins" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" title="Plugin" v-bind:fn="fn" v-bind:fnparam="fnparam" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='T'" title="Text" class="editable" v-bind:data-var="datavar+':v'" v-bind:data-for="datafor" ><div contenteditable data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" v-bind:data-allow="v['t']"  v-bind:fn="fn" v-bind:fnparam="fnparam">{{ v['v'] }}</div></div>
		<div v-else-if="v['t']=='TTold'" title="Text" class="editable" v-bind:data-var="datavar+':v'" v-bind:data-for="datafor" ><div contenteditable class="editabletextarea" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" v-bind:data-allow="v['t']"  v-bind:fn="fn" v-bind:fnparam="fnparam">{{ v['v'] }}</div></div>
		<pre v-else-if="v['t']=='TT'" title="Multiline Text" data-type="objecteditable"  editable-type="TT" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"  v-bind:fn="fn" v-bind:fnparam="fnparam" style="margin-bottom:5px;" >{{ v['v'] }}</pre>
		<pre v-else-if="v['t']=='HT'" title="Html Text" data-type="objecteditable"  editable-type="HT" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" v-bind:fn="fn" v-bind:fnparam="fnparam" style="margin-bottom:5px;" >{{ v['v'] }}</pre>
		<div v-else-if="v['t']=='N'" title="Number" class="editable" v-bind:data-var="datavar+':v'" v-bind:data-for="datafor"  v-bind:fnparam="fnparam"><div contenteditable data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" v-bind:data-allow="v['t']"  v-bind:fn="fn" v-bind:fnparam="fnparam">{{ v['v'] }}</div></div>
		<div v-else-if="v['t']=='L'" title="List" data-type="object" v-bind:data-var="datavar+':v'" v-bind:data-for="datafor" v-bind:fn="fn" v-bind:fnparam="fnparam" >{{ v['v'] }}</div>
		<pre v-else-if="v['t']=='O'" title="Object or Associative List" data-type="objecteditable" editable-type="O" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"  v-bind:fn="fn" v-bind:fnparam="fnparam" style="margin-bottom:5px;" >{{ get_object_notation__(v['v']) }}</pre>
		<div v-else-if="v['t']=='B'" title="Boolean" data-type="dropdown" v-bind:data-for="datafor" data-list="boolean" v-bind:data-var="datavar+':v'"  v-bind:fn="fn" v-bind:fnparam="fnparam">{{ v['v'] }}</div>
		<div v-else-if="v['t']=='NL'" title="Null" >null</div>
	</div>`
};