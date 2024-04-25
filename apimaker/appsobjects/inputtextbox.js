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
		//show: "none",
	}},
	props: ["datafor", "datavar", "v", "vars", "fn", "fnparam"],
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
		// get_object_notation__(v){
		// 	var vv = {};
		// 	for(var k in v ){
		// 		if( v[k]['t'] == "V" ){
		// 			vv[ k ] = this.data_types__[ v[k]['t'] ] + "["+v[k]['v']['v']+"]";
		// 			if( 'vs' in v[k]['v'] ){
		// 				if( v[k]['v']['vs'] ){
		// 					if( v[k]['v']['vs']['v'] ){
		// 						vv[ k ] = vv[ k ] + '->' + v[k]['v']['vs']['v'];
		// 					}
		// 				}
		// 			}
		// 		}else if( v[k]['t'] == "PLG" ){
		// 			vv[ k ] = this.data_types__[ v[k]['t'] ] + "["+v[k]['v']['v']+"]";
		// 			if( 'vs' in v[k]['v'] ){
		// 				if( v[k]['v']['vs'] ){
		// 					if( v[k]['v']['vs']['v'] ){
		// 						vv[ k ] = vv[ k ] + '->' + v[k]['v']['vs']['v'];
		// 					}
		// 				}
		// 			}
		// 		}else{
		// 			vv[ k ] = this.derive_value__(v[k]);
		// 		}
		// 	}
		// 	return Object.fromEntries(Object.entries(vv).sort());
		// 	return vv;
		// },
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
		}
	},
	template:`<div v-bind:class="'codeline_thing codeline_thing_'+v['t']" >
		<div class="codeline_thing_pop" data-type="dropdown2" data-list="datatype" v-bind:data-for="datafor" v-bind:data-var="datavar+':t'" v-bind:title="data_types__[v['t']]"  >{{ v['t'] }}</div>
		<varselect2 v-if="v['t']=='V'" v-bind:data-k-type="v['t']" v-bind:data-plg="getplg()" title="Variable" v-bind:datavar="datavar+':v'" v-bind:datafor="datafor" v-bind:vars="vars" v-bind:v="v['v']"  ></varselect2>
		<div v-else-if="v['t']=='TI'" style="display:flex; gap:10px; " >
			<div style="display:flex; border:1px solid #999; padding:3px;" >
				<div>Id:</div>
				<div title="Thing ID" class="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:i'"  ><div contenteditable spellcheck="false" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:i'" v-bind:data-allow="text"  >{{ v['v']['i'] }}</div></div>
			</div>
			<div style="display:flex; border:1px solid #999; padding:3px;" >
				<div>Label:</div>
				<div title="Thing Label" class="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:l'" ><div contenteditable spellcheck="false" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:l'" v-bind:data-allow="text"  >{{ v['v']['l'] }}</div></div>
			</div>
		</div>
		<div v-else-if="v['t']=='TH'" style="display:flex; gap:10px; " >
			<template v-if="'l' in v['v']" >
			<div title="Thing" 		data-type="dropdown" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:th'" data-list="list" v-bind:data-list="thing_vars"  >{{ v['v']['th'] }}</div>
			<div title="ThingItem"	data-type="dropdown" data-list="thing"  v-bind:data-thing="v['th']" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:v'" v-bind:data-k-type="dataktype" v-bind:data-plg="dataplg"  >{{ v['v']['l']['v'] }}</div>
			</template><div>error!</div>
		</div>
		<div v-else-if="v['t']=='THL'"  title="Thing List Name" class="editable" v-bind:data-var="datavar+':v:th'" v-bind:data-for="datafor" ><div contenteditable spellcheck="false" placeholder="Thing Name" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:th'" data-allow="T"  >{{ v['v']['th'] }}</div></div>
		<div v-else-if="v['t']=='PLG'" data-type="dropdown" data-list="plugins" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" title="Plugin"  >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='T'" title="Text" class="editable" v-bind:data-var="datavar+':v'" v-bind:data-for="datafor" ><div contenteditable spellcheck="false" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" v-bind:id="datavar+':v'" v-bind:data-allow="v['t']"   >{{ v['v'] }}</div></div>
		<div v-else-if="v['t']=='TTold'" title="Text" class="editable" v-bind:data-var="datavar+':v'" v-bind:data-for="datafor" ><div contenteditable  spellcheck="false" class="editabletextarea" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" v-bind:data-allow="v['t']"  >{{ v['v'] }}</div></div>
		<pre v-else-if="v['t']=='TT'" title="Multiline Text" data-type="objecteditable"  editable-type="TT" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"   style="margin-bottom:5px;" >{{ v['v'] }}</pre>
		<pre v-else-if="v['t']=='HT'" title="Html Text" data-type="objecteditable"  editable-type="HT" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"  style="margin-bottom:5px;" >{{ v['v'] }}</pre>
		<div v-else-if="v['t']=='N'" title="Number" class="editable" v-bind:data-var="datavar+':v'" v-bind:data-for="datafor" ><div contenteditable spellcheck="false" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" v-bind:data-allow="v['t']"  >{{ v['v'] }}</div></div>
		<!--<div v-else-if="v['t']=='L'" title="List" data-type="object" v-bind:data-var="datavar+':v'" v-bind:data-for="datafor"  >{{ v['v'] }}</div>-->
		<pre v-else-if="v['t']=='L'" title="List Object" data-type="objecteditable" editable-type="L" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"   style="margin-bottom:5px;" >{{ get_list_notation__(v['v']) }}</pre>
		<pre v-else-if="v['t']=='O'" title="Object or Associative List" data-type="objecteditable" editable-type="O" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"   style="margin-bottom:5px;" >{{ get_object_notation__(v['v']) }}</pre>
		<pre v-else-if="v['t']=='MongoQ'" title="MongoDb Query Condition" data-type="objecteditable" editable-type="MongoQ" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"   style="margin-bottom:5px;" >{{ get_object_notation__(v['v']) }}</pre>
		<pre v-else-if="v['t']=='MongoP'" title="MongoDb Output Projection" data-type="objecteditable" editable-type="MongoP" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"   style="margin-bottom:5px;" >{{ get_object_notation__(v['v']) }}</pre>
		<div v-else-if="v['t']=='B'" title="Boolean" data-type="dropdown" v-bind:data-for="datafor" data-list="boolean" v-bind:data-var="datavar+':v'"  >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='D'" title="Date" data-type="popupeditable" v-bind:data-for="datafor" editable-type="d" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='DT'" title="DateTime" data-type="popupeditable" v-bind:data-for="datafor" editable-type="dt" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v']['v'] }} {{ v['v']['tz'] }}</div>
		<div v-else-if="v['t']=='TS'" title="Unix TimeStamp" data-type="popupeditable" v-bind:data-for="datafor" editable-type="ts" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='NL'" title="Null" ></div>
		<div v-else>Unknown Type</div>
	</div>`
};