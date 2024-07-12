const vlist =  {
	data(){
		return {
		}
	},
	props: ['v','datavar', 'vars'],
	methods: {
		echo__: function(v__){
			if( typeof(v__)=="object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
		add_new_item__: function(){
			if( this.v.length > 0 ){
				this.v.push(JSON.parse(JSON.stringify( this.v[ this.v.length- 1] )) );
			}else{
				this.v.push({
					"t": "T",
					"v": ""
				});
			}
		},
		deletenode__: function( li ){
			this.v.splice(li,1);
		}
	},
	template: `<div>
		<div>[</div>
		<div v-if="typeof(v)!='object'" style="margin-left:30px;">list expected {{ typeof(v) }}</div>
		<div v-else-if="'length' in v==false" style="margin-left:10px;">list expected {{ v }}</div>
		<template v-for="ld,li in v" >
			<div v-if="typeof(ld)=='object'" style="margin-left:10px; display:flex;">
				<div><input type="button" class="btn btn-outline-secondary btn-sm me-2" style="padding:0px 5px;" value="X" v-on:click="deletenode__(li)" ></div>
				<vfield  v-bind:vars="vars" v-bind:v="ld" v-bind:datavar="datavar+':'+li" ></vfield>
			</div>
		</template>
		<div><input class="btn btn-outline-secondary btn-sm py-0" style="margin-left:10px; padding:0px 5px;" type="button" v-on:click="add_new_item__" value="+"></div>
		<div>]</div>
	</div>`
};