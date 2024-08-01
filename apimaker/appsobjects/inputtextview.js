const inputtextview = {
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
		//show: "none",
	}},
	props: ["datavar", "v"],
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
		getlink: function(vi){
			this.$root.show_thing(vi);
		},
	},
	template2:`<div style="display:flex;">
		<div style="border:1px solid #eee; background-color:#f8f8f8; padding:0px 2px; min-width:15px; text-align:center;" >{{ v['t'] }}</div>
		<div style="border:1px solid #ccc; padding:0px 5px;" >
		<div v-if="v['t']=='GT'" >
			<template v-if="'v' in v&&'i' in v" >
				<a href="#" class="codeline_gt_link" v-on:click="getlink(v['i'])" >{{ v['v'] }}</a>
			</template>
			<div v-else>error object</div>
		</div>
		<div v-else-if="v['t']=='T'"  >{{ v['v'] }}</div>
		<pre v-else-if="v['t']=='TT'" >{{ v['v'] }}</pre>
		<pre v-else-if="v['t']=='HT'" >{{ v['v'] }}</pre>
		<div v-else-if="v['t']=='N'"  >{{ v['v'] }}</div>
		<pre v-else-if="v['t']=='L'"  >{{ get_list_notation__(v['v']) }}</pre>
		<pre v-else-if="v['t']=='O'"  >{{ get_object_notation__(v['v']) }}</pre>
		<div v-else-if="v['t']=='B'"  >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='D'"  >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='DT'" >{{ v['v']['v'] }} {{ v['v']['tz'] }}</div>
		<div v-else-if="v['t']=='TS'" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='NL'" >Null</div>
		<div v-else>{{ v['v'] }}</div>
		</div>
	</div>`,
	template:`<div>
		<div v-if="v['t']=='GT'" >
			<template v-if="'v' in v&&'i' in v" >
				<a href="#" v-on:click.prevent.stop="getlink(v['i'])" >{{ v['v'] }}</a>
			</template>
			<div v-else>error object</div>
		</div>
		<div v-else-if="v['t']=='T'"  >{{ v['v'] }}</div>
		<pre v-else-if="v['t']=='TT'" >{{ v['v'] }}</pre>
		<pre v-else-if="v['t']=='HT'" >{{ v['v'] }}</pre>
		<div v-else-if="v['t']=='N'"  >{{ v['v'] }}</div>
		<pre v-else-if="v['t']=='L'"  >{{ get_list_notation__(v['v']) }}</pre>
		<pre v-else-if="v['t']=='O'"  >{{ get_object_notation__(v['v']) }}</pre>
		<div v-else-if="v['t']=='B'"  >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='D'"  >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='DT'" >{{ v['v']['v'] }} {{ v['v']['tz'] }}</div>
		<div v-else-if="v['t']=='TS'" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='NL'" >Null</div>
		<div v-else>{{ v['v'] }}</div>
		</div>
	</div>`
};