const vobject =  {
	data(){
		return {
			add_new_item__: false,
			new_item_name__: "",
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
	},
	template: `<div style="border:1px solid #ccc; padding:5px 10px;">
		<div v-if="typeof(v)!='object'||v==undefined||v==null" style="margin-left:30px;">vobject error</div>
		<div v-else>
			<div v-for="vkey in Object.keys(v)" style="display:flex; border-bottom:1px solid #ccc; margin-bottom:5px;" >
				<div><input type="button" class="btn btn-outline-secondary btn-sm me-2" style="padding:0px 5px;" value="X" v-on:click="deletenode__(vkey,$event)" ></div>
				<div style="display:flex;align-self:flex-start;">
					<div>"</div>
					<div class="editable" style="min-width:30px;" ><div spellcheck="false" contenteditable data-type="editable" v-bind:data-var="datavar+':'+vkey+':k'"  data-allow="text" >{{ v[vkey]['k'] }}</div></div>
					<div>"</div>
				</div>
				<div>&nbsp;:&nbsp;&nbsp;</div>
				<vfield v-bind:v="v[vkey]"  v-bind:datavar="datavar+':'+vkey" v-bind:vars="vars" ></vfield>
			</div>
			<div v-if="add_new_item__==false"><input class="btn btn-outline-secondary btn-sm py-0" style="padding:0px 5px;" type='button' v-on:click="add_new_item__=true" value='+'></div>
			<div v-if="add_new_item__">
				<input spellcheck="false" type='text' v-model="new_item_name__" placeholder="New Property" style="width:100px;border:1px solid #999;" ><input class="btn btn-outline-success btn-sm py-0"  style="padding:0px 5px;" type='button' v-on:click="addit__" value='+'>
			</div>
		</div>
	</div>`
};