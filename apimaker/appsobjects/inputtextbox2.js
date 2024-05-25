const inputtextbox2 = {
	data(){return{
		"data_types__":{
			"T": "Text",
			"N": "Number",
			"D": "Date",
			"DT": "DateTime",
			"TH": "Thing",
			"TI": "Thing Item",
			"L": "List",
			"O": "Assoc List",
			"B": "Boolean",
			"NL": "Null", 
			"BIN": "Binary",
			"V": "Variable",
		},
		types_: [],
	}},
	props: ["datafor", "datavar", "v", "types"],
	mounted: function(){
		if( this.datafor == undefined){
			this.datafor = "stages";
		}
		if( 'types' in this ){
			if( typeof(this.types) == "object" ){
				if( "length" in this.types ){
					console.log( "types alread split" );
					this.types_ = this.types;
				}else{
					console.log( "types Incorrect: " );
					console.log( this.types );
				}
			}else if( typeof(this.types) == "string" ){
				try{
					this.types_ = this.types.split(",");
				}catch(e){
					console.log("inputtextbox2 types not found");
					console.log( this.types );
					console.log( this.datavar );
				}
			}
		}else{
			console.log("inputtextbox2 types not found")
		}
	},
	methods:{
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
			if( v['t'] == "T" || v['t'] == "TT" ||  v['t'] == "HT" || v['t']== "D" ){
				return v['v'].toString();
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
				return (v['v']['v'] + " " + v['v']['tz']).toString();
			}else if( v['t'] == 'D' || v['t'] == 'TS' ){
				return (v['v']).toString();
			}else if( v['t'] == 'D' || v['t'] == 'DT' || v['t'] == 'TS' ){
				return (v['v']).toString();
			}else{
				return "unknown: "+ v['t'];
			}
		},
	},
	template:`<div v-bind:class="'codeline_thing codeline_thing_'+v['t']" >
		<div v-if="types_.length!=1" class="codeline_thing_pop" data-type="dropdown2" data-list="datatype" v-bind:data-list-filter="types" v-bind:data-for="datafor" v-bind:data-var="datavar+':t'" v-bind:title="data_types__[v['t']]" >{{ v['t'] }}</div>
		<div v-if="v['t']=='V'" title="Variable" data-type="dropdown" data-list="vars" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:v'"  >{{ v['v']['v'] }}</div>
		<div v-else-if="v['t']=='TI'" style="display:flex; gap:10px; " >
			<div style="display:flex; border:1px solid #999; padding:3px;" >
				<div>Id:</div>
				<div title="Thing ID" class="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:i'" ><div contenteditable spellcheck="false" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:i'" v-bind:id="datavar+':v:i'" v-bind:data-allow="text" >{{ v['v']['i'] }}</div></div>
			</div>
			<div style="display:flex; border:1px solid #999; padding:3px;" >
				<div>Label:</div>
				<div title="Thing Label" class="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:l'" ><div contenteditable spellcheck="false" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:l'" v-bind:data-allow="text" >{{ v['v']['l'] }}</div></div>
			</div>
		</div>
		<div v-else-if="v['t']=='TH'" style="display:flex; gap:10px; " >
			<div v-if="'thfixed' in v==false" title="Thing" data-type="dropdown" data-list="things" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:th'"  >{{ v['v']['th'] }}</div>
			<div title="ThingItem" data-type="dropdown" data-list="thing" v-bind:data-thing="v['v']['th']" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"   >{{ v['v']['l']['v'] }}</div>
		</div>
		<div v-else-if="v['t']=='THL'" placeholder="Thing Name" title="Thing List Name" class="editable" v-bind:data-var="datavar+':v:th'" v-bind:data-for="datafor" ><div placeholder="Thing Name" contenteditable spellcheck="false" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:th'" data-allow="T" >{{ v['v']['th'] }}</div></div>
		<div v-else-if="v['t']=='TH'" title="Thing" data-type="dropdown" data-list="things" v-bind:data-thing="v['v']['th']" v-bind:data-for="datafor" v-bind:data-var="datavar+':v:v'"  >{{ v['v']['v']['l'] }}</div>
		<div v-else-if="v['t']=='T'" title="Text" class="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" ><div contenteditable spellcheck="false" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" v-bind:id="datavar+':v'" v-bind:data-allow="v['t']"  >{{ v['v'] }}</div></div>
		<pre v-else-if="v['t']=='TT'" title="Multiline Text" data-type="objecteditable"  editable-type="TT" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" style="margin-bottom:5px;" >{{ v['v'] }}</pre>
		<pre v-else-if="v['t']=='HT'" title="Html Text" data-type="objecteditable"       editable-type="HT" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" style="margin-bottom:5px;" >{{ v['v'] }}</pre>
		<div v-else-if="v['t']=='N'" title="Number" class="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" ><div contenteditable spellcheck="false" data-type="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" v-bind:data-allow="v['t']" >{{ v['v'] }}</div></div>
		<pre v-else-if="v['t']=='L'" title="Object List" data-type="objecteditable" editable-type="L" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" style="margin-bottom:5px;" >{{ get_list_notation__(v['v']) }}</pre>
		<pre v-else-if="v['t']=='O'" title="Object or Associative List" data-type="objecteditable" editable-type="O" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" style="margin-bottom:5px;" >{{ get_object_notation__(v['v']) }}</pre>
		<pre v-else-if="v['t']=='MongoQ'" title="MongoDb Query Condition" data-type="objecteditable" editable-type="MongoQ" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"  style="margin-bottom:5px;" >{{ get_object_notation__(v['v']) }}</pre>
		<pre v-else-if="v['t']=='MongoP'" title="MongoDb Output Projection" data-type="objecteditable" editable-type="MongoP" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"  style="margin-bottom:5px;" >{{ get_object_notation__(v['v']) }}</pre>
		<pre v-else-if="v['t']=='DBCondObject'" title="Database Condition Object" data-type="objecteditable" editable-type="DBCondObject" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" style="margin-bottom:5px;">{{ get_dbcond_object_notation__(v['v']) }}</pre>
		<div v-else-if="v['t']=='B'" title="Boolean" data-type="dropdown" data-list="boolean" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='D'" title="Date" data-type="popupeditable" v-bind:data-for="datafor" editable-type="d" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='DT'" title="DateTime" data-type="popupeditable" v-bind:data-for="datafor" editable-type="dt" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v']['v'] }} {{ v['v']['tz'] }}</div>
		<div v-else-if="v['t']=='TS'" title="Unix TimeStamp" data-type="popupeditable" v-bind:data-for="datafor" editable-type="ts" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='NL'" title="Null" ></div>
		<div v-else v-bind:title="v['t']" data-type="dropdown" v-bind:data-list="v['t']" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'"    >{{ v['v'] }}</div>
		<div v-if="types_.indexOf(v['t'])==-1" style='color:red;' >Type not allowed{{ v['t'] }} {{ types_ }}</div>
	</div>`
};