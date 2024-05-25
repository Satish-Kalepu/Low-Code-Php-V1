const graph_object =  {
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
		if( "i_of" in this.v == false ){  this.v['i_of'] = {"t":"L", "v": []}; }else
		if( typeof(this.v['i_of']) != "object" || "length" in this.v['i_of'] ){ this.v['i_of'] = {"t":"L", "v": []}; };
	},
	methods: {
		echo__: function(v__){
			if( typeof(v__)=="object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
		newsubitem__: function(){
			return "f_" + parseInt(Math.random()*1000);
		},
		addit__: function(){
			var k = this.new_item_name__.trim();
			k = k.replace(/\W/g, '');
			if( k ){
				this.v[ k+'' ] =  {"t": "T","v": "", "k":k+''};
				this.new_item_name__ = "";
				this.add_new_item__ = false;
				//this.$emit("updated", this.v);
			}
		},
		deletenode__: function( k, e ){
			if( e.ctrlkey ){
				delete this.v[ k ];
				//this.$emit("updated", this.v);
			}else if( confirm("are you sure?\nctrl+click to avoid prompt") ){
				delete this.v[ k ];
				//this.$emit("updated", this.v);
			}
		},
		add_i_of__: function(){
			if( this.v['i_of']['v'].length == 0 ){
				var v  = {
					"t":"GT", 
					"th":{"l": {"t":"T","v":""}, "i":{"t":"T","v":""} },
					"v":{"l": {"t":"T","v":""}, "i":{"t":"T","v":""} }
				}
				this.v['i_of']['v'].push( v );
			}else{
				var v = JSON.parse(JSON.stringify( this.v['i_of']['v'][0] ));
				v['v'] = {"l": {"t":"T","v":""}, "i":{"t":"T","v":""} };
				this.v['i_of']['v'].push( v );
			}
		},
		del_i_of__: function(vi){
			this.v['i_of']['v'].splice(vi,1);
		}
	},
	template: `<div>
		<table class="table table-bordered table-sm w-auto" >
			<tr>
				<td>Label</td>
				<td><inputtextbox2 types="T" v-bind:v="v['l']" v-bind:datavar="'l'" ></inputtextbox2></td>
			</tr>
			<tr>
				<td>Instance Of</td>
				<td>
					<div v-for="vd,vi in v['i_of']['v']" style="display:flex; column-gap:10px;" >
						<div class="codeline_thing codeline_thing_T" >
							<div class="codeline_thing_pop"  title="Thing" data-type="dropdown3" v-bind:data-var="datavar+':i_of:v:'+vi+':th'" data-list="graph-thing" data-thing="GT-ALL" data-thing-label="Nodes" >{{ vd['th']['l']['v'] }}</div>
							<div title="Thing" data-type="dropdown" v-bind:data-var="datavar+':i_of:v:'+vi+':v'" data-list="graph-thing" v-bind:data-thing="'GT-'+vd['th']['i']['v']" v-bind:data-thing-label="vd['th']['l']['v']" >{{ vd['v']['l']['v'] }}</div>
						</div>
						<div><input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="del_i_of__(vi)" ></div>
					</div>
					<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="add_i_of__" ></div>
				</td>
			</tr>
			<tr>
				<td>Part Of</td>
				<td>
					<div v-for="vd,vi in v['i_of']['v']" style="display:flex; column-gap:10px;" >
						<div class="codeline_thing codeline_thing_T" >
							<div class="codeline_thing_pop"  title="Thing" data-type="dropdown3" v-bind:data-var="datavar+':i_of:v:'+vi+':th'" data-list="graph-thing" data-thing="GT-ALL" data-thing-label="Nodes" >{{ vd['th']['l']['v'] }}</div>
							<div title="Thing" data-type="dropdown" v-bind:data-var="datavar+':i_of:v:'+vi+':v'" data-list="graph-thing" v-bind:data-thing="'GT-'+vd['th']['i']['v']" v-bind:data-thing-label="vd['th']['l']['v']" >{{ vd['v']['l']['v'] }}</div>
						</div>
						<div><input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" style="" v-on:click="del_i_of__(i)" ></div>
					</div>

					<div><input type="button" class="btn btn-outline-dark   btn-sm py-0" value="+" v-on:click="add_i_of__" ></div>
				</td>
			</tr>
		</table>
	</div>`
};