const graph_object_new =  {
	data(){
		return {
			add_new_item__: false,
			new_item_name__: "",
		}
	},
	props: ['v', 'datavar', 'vars'],
	mounted: function(){
		if( typeof(this.v) != "object" || "length" in this.v ){
			this.v = {};
		}
		if( "i_of" in this.v == false ){
			this.v['i_of'] = {
				"t":"GT", 
				"th":{"l": {"t":"T","v":""}, "i":{"t":"T","v":""} },
				"v": {"l": {"t":"T","v":""}, "i":{"t":"T","v":""} }
			};
		}else if( typeof(this.v['i_of']) != "object" || "length" in this.v['i_of'] ){
			this.v['i_of'] = {
				"t":"GT", 
				"th":{"l": {"t":"T","v":""}, "i":{"t":"T","v":""} },
				"v": {"l": {"t":"T","v":""}, "i":{"t":"T","v":""} }
			};
		};
	},
	methods: {
		echo__: function(v__){
			if( typeof(v__)=="object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
	},
	template: `<div class="code_row code_line">
		<table class="table table-bordered table-sm w-auto" >
			<tr>
				<td>Label</td>
				<td><inputtextbox2 types="T" v-bind:v="v['l']" v-bind:datavar="'l'" ></inputtextbox2></td>
			</tr>
			<tr>
				<td>Instance Of</td>
				<td>
					<div class="codeline_thing codeline_thing_T" >
						<div title="Thing" class="codeline_thing_pop" data-type="dropdown3" v-bind:data-var="datavar+':i_of:th'" data-list="graph-thing" data-thing="GT-ALL" data-thing-label="Nodes" >{{ v['i_of']['th']['l']['v'] }}</div>
						<div v-if="v['i_of']['th']['i']['v']" title="Thing"                            data-type="dropdown"  v-bind:data-var="datavar+':i_of:v'"  data-list="graph-thing" v-bind:data-thing="'GT-'+v['i_of']['th']['i']['v']" v-bind:data-thing-label="v['i_of']['th']['l']['v']" >{{ v['i_of']['v']['l']['v'] }}</div>
					</div>
				</td>
			</tr>
		</table>
	</div>`
};