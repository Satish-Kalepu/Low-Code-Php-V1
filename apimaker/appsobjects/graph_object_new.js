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
				"i":"0",
				"v":""
			};
		}else if( typeof(this.v['i_of']) != "object" || "length" in this.v['i_of'] ){
			this.v['i_of'] = {
				"t":"GT", 
				"i":"0",
				"v":""
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
				<td>Instance Of</td>
				<td>
					<div class="codeline_thing codeline_thing_T" >
						<div title="Thing" data-type="dropdown" v-bind:data-var="datavar+':i_of:v'"  data-list="graph-thing" v-bind:data-thing="'GT-ALL'" data-thing-label="Things" >{{ v['i_of']['v'] }}</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>Label</td>
				<td><inputtextbox2 types="T,GT" v-bind:v="v['l']" v-bind:datavar="datavar+':l'" ></inputtextbox2></td>
			</tr>
			<tr>
				<td>Type</td>
				<td><label><input type="radio" v-model="v['i_t']" value="N" > Node</label> <label><input type="radio" v-model="v['i_t']" value="L" > DataSet</label></td>
			</tr>
		</table>
	</div>`
};