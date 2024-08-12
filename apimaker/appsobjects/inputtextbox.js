const inputtextbox = {
	data(){return{
		"data_types__":{
			"T": "Text",
			"TT": "MultiLine Text",
			"N": "Number",
			"D": "Date",
			"DT": "DateTime",
			"TS": "Timestamp",
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
		"l": false,
		"ik": "",
		//show: "none",
	}},
	props: ["datavar", "v", "vars", "fn", "linkable", "initial_keyword"],
	watch: {
		initial_keyword: function(){
			if( typeof(this.initial_keyword) != "undefined" ){
				this.ik = this.initial_keyword+'';
			}
		}
	},
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
		if( typeof(this.linkable)=="undefined" ){
			this.l = false;
		}else{
			this.l = true;
		}
		if( typeof(this.initial_keyword)=="undefined" ){
			this.ik = "";
		}else{
			this.ik = this.initial_keyword+'';
		}
	},
	methods:{
		echo__: function(v){
			if( typeof(v) == "object" ){
				console.log( JSON.stringify(v,null,4) );
			}else{
				console.log( v );
			}
		},
		text_to_html: function(v){
			v = v.replace( /\>/g,  "&gt;" );
			v = v.replace( /\</g,  "&lt;" );
			return v;
		},
		get_object_notation__( v ){
			var vv = {};
			if( typeof(v)=="object" ){
				if( "length" in v == false ){
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
						}else{
							vv[ k ] = this.derive_value__(v[k]);
						}
					}
				}else{ console.error("get_object_notation: not a object "); this.echo__( v ); }
			}else{ console.error("get_object_notation: incorrect type: "+ typeof(v) ); }
			return Object.fromEntries(Object.entries(vv).sort());
		},
		get_list_notation__( v ){
			//this.echo__( "get object notation" );
			//this.echo__( v );
			var vv = [];
			if( typeof(v)=="object" ){
				if( "length" in v ){
					for(var k=0;k<v.length;k++ ){
						if( v[k]['t'] == "V" ){
							nv = this.data_types__[ v[k]['t'] ] + "["+v[k]['v']['v']+"]";
							if( 'vs' in v[k]['v'] ){
								if( v[k]['v']['vs'] ){
									if( v[k]['v']['vs']['v'] ){
										nv = nv + '->' + v[k]['v']['vs']['v'];
									}
								}
							}
							vv.push(nv);
						}else{
							vv.push( this.derive_value__(v[k]) );
						}
					}
				}else{ console.error("get_list_notation: not a list "); }
			}else{ console.error("get_list_notation: incorrect type: "+ typeof(v) ); }
			return vv;
		},
		derive_value__: function(v ){
			if( v['t'] == "T" ){
				return v['v'];
			}else if( v['t']== "N" ){
				return Number(v['v']);
			}else if( v['t'] == 'O' ){
				return this.get_object_notation__(v['v']);
			}else if( v['t'] == 'L' ){
				return this.get_list_notation__(v['v']);
			}else if( v['t'] == 'NL' ){
				return null;
			}else if( v['t'] == 'GT' ){
				return this.get_object_notation__(v['v']);
			}else if( v['t'] == 'B' ){
				return (v['v']?true:false);
			}else if( v['t'] == 'DT' ){
				return (v['v']['v'] + " " + v['v']['tz']);
			}else if( v['t'] == 'D' || v['t'] == 'TS' ){
				return (v['v']);
			}else{
				return "unknown: " + v['t'];
			}
		},
		getplg: function(){
			if( this.v['t'] == "V" ){ 
				if( this.v['v']['t'] == "PLG" ){ 
					return this.v['v']['plg'];
				}
			}
			return false;
		},
		getlink: function(vi){
			this.$root.show_thing(vi);
		},
		createlink: function(e){
			this.$root.convert_to_link(e.target, this.datavar );
		},
		removelink: function(){
			if( confirm("Are you sure?") ){
				this.v['t'] = "T";
				delete(this.v['i']);
			}
		},
	},
	template:`<div v-bind:class="'codeline_thing codeline_thing_'+v['t']" >
		<div class="codeline_thing_pop" data-type="dropdown2" data-list="datatype"  v-bind:data-var="datavar+':t'" v-bind:title="data_types__[v['t']]"  >{{ v['t'] }}</div>
		<div v-if="v['t']=='GT'" style="display:flex;align-items:center;" >
			<template v-if="'v' in v&&'i' in v" >
				<div title="Thing" data-type="dropdown" v-bind:data-var="datavar+':v'" data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" v-bind:data-thing-initial-keyword="ik" allow-create="true" >{{ v['v'] }}</div>
				<a class="btn btn-link btn-sm" v-if="v['i']" href="#" v-on:click.prevent.stop="getlink(v['i'])" v-bind:title="'Goto Thing: '+v['i']" >#</a>
				<div class="btn btn-link btn-sm text-danger" v-if="'i' in v&&l" v-on:click.prevent.stop="removelink()" title="Remove Link" ><i class="fas fa-unlink"></i></div>
			</template>
			<div v-else>GT v,i need object</div>
		</div>
		<div v-else-if="v['t']=='T'" style="display:flex;align-items:center;" >
			<div title="Text" class="editable" v-bind:data-var="datavar+':v'"  ><div contenteditable style="white-space:nowrap;" spellcheck="false" data-type="editable"  v-bind:data-var="datavar+':v'" v-bind:id="datavar+':v'" v-bind:data-allow="v['t']" >{{ v['v'] }}</div></div>
			<div class="btn btn-link btn-sm" v-if="'i' in v==false&&l" v-on:click.prevent.stop="createlink" title="Create Link" ><i class="fas fa-link"></i></div>
		</div>
		<pre v-else-if="v['t']=='TT'" title="Multiline Text" data-type="objecteditable"  editable-type="TT"  v-bind:data-var="datavar+':v'"   style="margin-bottom:5px;" >{{ v['v'] }}</pre>
		<pre v-else-if="v['t']=='HT'" title="Html Text" data-type="objecteditable"  editable-type="HT"  v-bind:data-var="datavar+':v'"  style="margin-bottom:5px;" >{{ v['v'] }}</pre>
		<div v-else-if="v['t']=='N'" title="Number" class="editable" v-bind:data-var="datavar+':v'"  ><div contenteditable spellcheck="false" data-type="editable"  v-bind:data-var="datavar+':v'" v-bind:data-allow="v['t']"  >{{ v['v'] }}</div></div>
		<pre v-else-if="v['t']=='L'" title="List Object" data-type="objecteditable" editable-type="L"  v-bind:data-var="datavar+':v'"   style="margin-bottom:5px;" >{{ get_list_notation__(v['v']) }}</pre>
		<div v-else-if="v['t']=='O'" >
			<vobject v-bind:datavar="datavar+':v'" v-bind:v="v['v']" ></vobject>
		</div>
		<div v-else-if="v['t']=='B'" title="Boolean" data-type="dropdown"  data-list="boolean" v-bind:data-var="datavar+':v'"  >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='D'" title="Date" data-type="popupeditable"  editable-type="d" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='DT'" title="DateTime" data-type="popupeditable"  editable-type="dt" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v']['v'] }} {{ v['v']['tz'] }}</div>
		<div v-else-if="v['t']=='TS'" title="Unix TimeStamp" data-type="popupeditable"  editable-type="ts" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='NL'" title="Null" ></div>
		<div v-else>Unknown Type</div>
	</div>`
};