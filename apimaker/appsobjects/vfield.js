const vfield = {
	data(){return{
		"data_types__":{
			"T": "Text",
			"N": "Number",
			"D": "Date",
			"DT": "DateTime",
			"L": "List",
			"O": "Assoc List",
			"B": "Boolean",
			"NL": "Null", 
			"BIN": "Binary",
			"V": "Variable",
		},
	}},
	props: ["datafor", "datavar", "v", "vars"],
	mounted(){
		if( this.datafor == undefined ){
			this.datafor = "stages";
		}
	},
	methods:{
		get_object_notation: function(){
			return 'Object Editable';
		},
		add_new_item__: function(){
			if( this.v['v'].length > 0 ){
				this.v['v'].push(JSON.parse(JSON.stringify( this.v['v'][ this.v['v'].length- 1] )) );
			}else{
				this.v['v'].push({
					"t": "T",
					"v": ""
				});
			}
		},
		deletenode__: function( li ){
			this.v['v'].splice(li,1);
		}
	},
	template:`<div v-bind:class="'codeline_thing codeline_thing_'+v['t']" >
		<div class="codeline_thing_pop" data-type="dropdown2" v-bind:data-for="datafor" data-list="datatype" v-bind:data-var="datavar+':t'" v-bind:title="data_types__[v['t']]" >{{ v['t'] }}</div>
		<varselect2 v-if="v['t']=='V'" v-bind:vars="vars" v-bind:datafor="datafor" v-bind:v="v['v']" v-bind:datavar="datavar+':v'" >{{ v['v'] }}</varselect2>
		<div v-else-if="v['t']=='T'" title="Text" class="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" ><div spellcheck="false" contenteditable data-type="editable" v-bind:data-var="datavar+':v'" v-bind:data-for="datafor" v-bind:data-allow="v['t']" >{{ v['v'] }}</div></div>
		<div v-else-if="v['t']=='N'" title="Number" class="editable" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'" ><div spellcheck="false" contenteditable data-type="editable" v-bind:data-var="datavar+':v'" v-bind:data-for="datafor" v-bind:data-allow="v['t']" >{{ v['v'] }}</div></div>
		<!--<vlist v-else-if="v['t']=='L'" v-bind:datafor="datafor" v-bind:vars="vars" v-bind:v="v['v']" v-bind:datavar="datavar+':v'" ></vlist>-->

		<div v-else-if="v['t']=='L'" >
			<div>[</div>
			<div v-if="typeof(v['v'])!='object'" style="margin-left:30px;">list expected {{ typeof(v['v']) }}</div>
			<div v-else-if="'length' in v['v']==false" style="margin-left:10px;">list expected {{ v['v'] }}</div>
			<template v-for="ld,li in v['v']" >
				<div v-if="typeof(ld)=='object'" style="margin-left:10px; display:flex;">
					<div><input type="button" class="btn btn-secondary btn-sm me-2" style="padding:0px 5px;" value="X" v-on:click="deletenode__(li)" ></div>
					<vfield v-bind:datafor="datafor" v-bind:vars="vars" v-bind:v="ld" v-bind:datavar="datavar+':v:'+li" ></vfield>
				</div>
			</template>
			<div><input class="btn btn-secondary btn-sm" style="margin-left:10px; padding:0px 5px;" type="button" v-on:click="add_new_item__" value="+"></div>
			<div>]</div>
		</div>

		<div v-else-if="v['t']=='L'" title="List" data-type="object" v-bind:data-for="datafor" v-bind:data-var="datavar+':v'">{{ v['v'] }}</div>
		<vobject v-else-if="v['t']=='O'" v-bind:datafor="datafor" v-bind:vars="vars" v-bind:v="v['v']" v-bind:datavar="datavar+':v'" ></vobject>
		<div v-else-if="v['t']=='B'"  title="Boolean" data-type="dropdown" v-bind:data-for="datafor" data-list="boolean" v-bind:data-var="datavar+':v'">{{ v['v'] }}</div>
		<div v-else-if="v['t']=='D'"  title="Date" data-type="popupeditable" v-bind:data-for="datafor" editable-type="d" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='DT'" title="DateTime" data-type="popupeditable" v-bind:data-for="datafor" editable-type="dt" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v']['v'] }}<span v-if="'tz' in v['v']" > {{ v['v']['tz'] }}</span></div>
		<div v-else-if="v['t']=='TS'" title="Unix TimeStamp" data-type="popupeditable" v-bind:data-for="datafor" editable-type="ts" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='NL'" title="Null" >null</div>
	</div>`
};