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
	props: ["datavar", "v", "vars"],
	mounted(){
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
		<div class="codeline_thing_pop" data-type="dropdown2"  data-list="datatype" v-bind:data-var="datavar+':t'" v-bind:title="data_types__[v['t']]" >{{ v['t'] }}</div>
		<varselect2 v-if="v['t']=='V'" v-bind:vars="vars"  v-bind:v="v['v']" v-bind:datavar="datavar+':v'" >{{ v['v'] }}</varselect2>
		<div v-else-if="v['t']=='T'" title="Text" class="editable"  v-bind:data-var="datavar+':v'" ><div spellcheck="false" contenteditable data-type="editable" v-bind:data-var="datavar+':v'"  v-bind:data-allow="v['t']" >{{ v['v'] }}</div></div>
		<div v-else-if="v['t']=='N'" title="Number" class="editable"  v-bind:data-var="datavar+':v'" ><div spellcheck="false" contenteditable data-type="editable" v-bind:data-var="datavar+':v'"  v-bind:data-allow="v['t']" >{{ v['v'] }}</div></div>
		<!--<vlist v-else-if="v['t']=='L'"  v-bind:vars="vars" v-bind:v="v['v']" v-bind:datavar="datavar+':v'" ></vlist>-->
		<div v-if="v['t']=='GT'" >
			<template v-if="typeof(v['v'])=='object'" >
				<div title="Thing" data-type="dropdown" v-bind:data-var="datavar+':v'" data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" >{{ v['v']['v'] }}</div>
				<a v-if="v['i']['v']" class="codeline_gt_link" href="#" >{{ v['i']['v'] }}</a>
			</template>
			<div v-else>GT value need object</div>
		</div>
		<div v-else-if="v['t']=='L'"  style="border:1px solid #ccc; padding:5px 10px;" >
			<div v-if="typeof(v['v'])!='object'" style="margin-left:30px;">list expected {{ typeof(v['v']) }}</div>
			<div v-else-if="'length' in v['v']==false" style="margin-left:10px;">list expected {{ v['v'] }}</div>
			<template v-for="ld,li in v['v']" >
				<div v-if="typeof(ld)=='object'" style=" display:flex; column-gap:5px; margin-bottom:5px; border-bottom:1px solid #ccc;">
					<div style="width:30px;text-align:center; background-color:#f8f8f8;">{{ li }}</div>
					<div><input type="button" class="btn btn-outline-secondary btn-sm me-2" style="padding:0px 5px;" value="X" v-on:click="deletenode__(li)" ></div>
					<vfield  v-bind:vars="vars" v-bind:v="ld" v-bind:datavar="datavar+':v:'+li" ></vfield>
				</div>
			</template>
			<div><input class="btn btn-outline-secondary btn-sm" style="margin-left:10px; padding:0px 5px;" type="button" v-on:click="add_new_item__" value="+"></div>
		</div>
		<div v-else-if="v['t']=='L'" title="List" data-type="object"  v-bind:data-var="datavar+':v'">{{ v['v'] }}</div>
		<vobject v-else-if="v['t']=='O'"  v-bind:vars="vars" v-bind:v="v['v']" v-bind:datavar="datavar+':v'" ></vobject>
		<div v-else-if="v['t']=='B'"  title="Boolean" data-type="dropdown"  data-list="boolean" v-bind:data-var="datavar+':v'">{{ v['v'] }}</div>
		<div v-else-if="v['t']=='D'"  title="Date" data-type="popupeditable"  editable-type="d" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='DT'" title="DateTime" data-type="popupeditable"  editable-type="dt" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v']['v'] }}<span v-if="'tz' in v['v']" > {{ v['v']['tz'] }}</span></div>
		<div v-else-if="v['t']=='TS'" title="Unix TimeStamp" data-type="popupeditable"  editable-type="ts" v-bind:data-var="datavar+':v'" style="border:1px solid #888; padding:0px 5px;cursor:pointer;" >{{ v['v'] }}</div>
		<div v-else-if="v['t']=='NL'" title="Null" >null</div>
	</div>`
};