<script>
const object_dataset_edit_record =  {
	data(){
		return {"msg": "", "err":""};
	},
	props: ["ref", "refname", "data"],
	watch: {
	},
	mounted: function(){
		//this.load_template();
	},
	methods: {
		echo__: function(v__){
			if( typeof(v__)=="object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
		save_props: function(){
			this.msg = "";this.err = "";
			axios.post("?", {
				"action": "objects_dataset_record_save_props",
				"object_id": this.data['thing']['_id'],
				"record_id": this.data['record']['_id'],
				"props": this.data['record']['props'],
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg = "Record updated successfully!";
								this.$root.popup_callback__({"event":"edited","thing_id": this.data['thing']['_id'], "record_id": this.data['record']['_id'],"record_props":this.data['record']['props']});
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response";
					}
				}else{
					this.err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.err = error.message;
			});
		},
		callback__: function( c ){
			var x = c.split(/\:/g);
			if( x[0] == "z_type_change" ){

			}else{
				console.log("Callback: " + c + " not defined!");
			}
		},
		add_sub: function(propf,vi){
			if( propf in this.data['record']['props'] == false ){
				this.data['record']['props'][ propf ] = [];
			}
			var v = {'t':"T", "v":""};
			this.data['record']['props'][ propf ].push( v );
		},
		del_sub: function(propf,vi){
			if( confirm("Are you sure?") ){
				this.data['record']['props'][ propf ].splice(vi,1);
			}
		},
		add_sub_object: function( propf ){
			var o = {};
			var propd = this.data['thing']['z_t'][ propf ];
			for(var tdi in propd['z']['z_t']){
				o[ tdi ] = {"t":"T", "v":""};
			}
			this.data['record']['props'][ propf ].push( o );
		},
		del_sub_object: function( propf, vi ){
			this.data['record']['props'][ propf ].splice( vi,1 );
		},
	},
	template: `<div class="code_line" >
		<div v-if="'thing' in data==false||'record' in data==false" >
			Data required
		</div>
		<template v-else >

		<div style="padding:10px;float:right;" ><div class="btn btn-outline-dark btn-sm me-2" v-on:click="save_props()" >Save</div></div>
		<div>DataSet: {{ data['thing']['l']['v'] }}. Record ID: {{ data['record']['_id'] }}</div>

		<div v-if="msg" style="color:blue; padding:5px; border:1px solid blue;" v-html="msg" ></div>
		<div v-if="err" style="color:red;  padding:5px; border:1px solid red;"  v-html="err" ></div>

		<table class="table table-bordered table-sm w-auto customborder2" >
			<tbody>
			<tr v-for="propf in data['thing']['z_o']" valign="top">
				<td>
					<span v-if="propf in data['thing']['z_t']" >{{ data['thing']['z_t'][ propf ]['l']['v'] }}</span>
					<span v-else >{{ propf }}</span>
				</td>
				<td>
					<template v-if="data['thing']['z_t'][ propf ]['t']['k']=='O'" >
						<table class="table table-bordered table-striped table-sm w-auto customborder2" >
							<tbody>
								<tr>
									<td>-</td>
									<td v-for="tdv in data['thing']['z_t'][ propf ]['z']['z_o']" >{{ data['thing']['z_t'][ propf ]['z']['z_t'][ tdv ]['l'] }}</td>
								</tr>
								<tr v-for="pvv,pii in data['record']['props'][ propf ]" >
									<td><input type="button" class="btn btn-outline-danger btn-sm py-0" style="padding:0px;width:20px;" value="X" v-on:click="del_sub_object(propf,pii)" ></td>
									<td v-for="tdv in data['thing']['z_t'][ propf ]['z']['z_o']" >
										<template v-if="pvv['t']=='O'" >
											<template v-if="tdv in pvv['v']" >
												<inputtextbox2 types="T,GT"  v-bind:v="pvv['v'][ tdv ]" v-bind:datavar="'ref:'+refname+':data:record:props:'+propf+':'+pii+':v:'+tdv" ></inputtextbox2>
											</template>
										</template>
										<div v-else>Incorrect Data</div>
									</td>
								</tr>
							</tbody>
						</table>
						<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_sub_object(propf,0)" ></div>
					</template>
					<template v-else >
						<template v-if="propf in data['record']['props']==false" >
							<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" style="padding:0px;width:20px;" v-on:click="add_sub('props',propf,0)" ></div>
						</template>
						<template v-else>
							<div v-for="pvv,pii in data['record']['props'][ propf ]" style="display:flex; column-gap:5px; border-bottom:1px dashed #ccc; align-items:center;" >
								<div><input type="button" class="btn btn-outline-danger btn-sm py-0"  style="padding:0px;width:20px;" value="x" v-on:click="del_sub(propf,pii)" ></div>
								<inputtextbox  v-bind:v="pvv" v-bind:datavar="'ref:'+refname+':data:record:props:'+propf+':'+pii" ></inputtextbox>
							</div>
							<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+"  style="padding:0px;width:20px;" v-on:click="add_sub(propf,pii)" ></div>
						</template>
					</template>
				</td>
			</tr>
			<tr valign="top">
				<td>
					<div class="btn btn-outline-dark btn-sm py-0" v-on:click="open_template_edit()" style="" >+</div>
				</td>
				<td>
					
				</td>
			</tr>
			</tbody>
		</table>
		<div v-if="data['thing']['z_o'].length>5">
			<div style="padding:10px;" ><div class="btn btn-outline-dark btn-sm me-2" v-on:click="save_props()" >Save</div></div>
			<div v-if="msg" style="color:blue; padding:5px; border:1px solid blue;" v-html="msg" ></div>
			<div v-if="err" style="color:red;  padding:5px; border:1px solid red;"  v-html="err" ></div>
		</div>
	</div>`
};
</script>