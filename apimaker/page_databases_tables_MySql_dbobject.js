const dbobject_table_mysql = {
	data: function(){
		return {
			items2: [],
			level: 1,
			showit: false,
			add_new_item: false,
			new_item_name: "",
			primary_field: "",
		}
	},
	props: ['items', 'source_fields'],
	watch: {
		items2: {
			handler: function(){
				if( this.showit ){
					//this.$emit('edited', this.items);
					//this.informparent()
				}
			},
			deep:true,
		}
	},
	mounted: function(){
		setTimeout(this.ini,200);
		//this.ini();
	},
	methods: {
		informparent: function(){
			setTimeout(this.informparent2,100);
		},
		informparent2: function(){
			var v = {};
			for(var i=0;i<this.items2.length;i++){
				this.items2[i]['order'] = i;
				v[ this.items2[ i ]['key'] ] = this.items2[ i ];
			}
			this.items = JSON.parse( JSON.stringify( v ) );
			this.$emit("edited", v );
		},
		echo: function(v){
			if( typeof(v)=="object" ){
				console.log( JSON.stringify(v,null,4) );
			}else{
				console.log( v );
			}
		},
		ini: function(){
			if( this.items == undefined ){
				this.items = {};
			}else if( typeof(this.items) != "object" || this.items.hasOwnProperty("length") ){
				this.items = {};
			}
			var v2 = JSON.parse( JSON.stringify( this.items ) );
			var v = [];
			for( var i in v2 ){
				v.push( Number(v2[i]['order']) );
				v2[i]['key'] = i+'';
			}
			v.sort();
			var v = Array.from(new Set(v));
			var k = [];
			for( var i=0;i<v.length;i++){
				for( var j in v2 ){if( this.items[ j ]['order'] == v[i] ){
					k.push( this.items[ j ] );
				}}
			}
			this.items2 = k;
			for( var fn in this.source_fields ){
				if( this.source_fields[ fn ]['index'] == 'primary' ){
					this.primary_field = fn+'';
				}
			}
			setTimeout(function(v){v.showit = true;},200,this);
		},
		addit: function(){
			if( this.new_item_name.trim() ){
				this.items2.push({
					"key": this.new_item_name+"",
					"type": this.source_fields[ this.new_item_name ]['type']+'',
					"mapped_type": this.source_fields[ this.new_item_name ]['mapped_type']+'',
					"m": true,
					"order": -1,
				});
				this.items2[ this.items2.length-1 ]["order"] = this.items2.length;
				this.new_item_name = "";
				this.add_new_item = false;
				this.informparent();
			}
		},
		deletenode: function(vkey){
			this.items2.splice( Number( vkey ), 1 );
			this.informparent();
		},
		change_field_type: function( vkey ){
			this.informparent();
		},
		field_change: function(vf){
			var k = this.items2[ vf ]['key'];
			if( k in this.source_fields ){
				this.items2[ vf ]['mapped_type'] = this.source_fields[ k ]['mapped_type']+'';
				this.items2[ vf ]['type'] = this.source_fields[ k ]['type']+'';
			}
			this.informparent();
		},
		moveu: function( vi, vkey ){
			if( vi >= 1 ){
				var it = JSON.parse( JSON.stringify( this.items2[ vi ] ) );
				this.items2.splice( vi, 1 );
				this.items2.splice( vi-1, 0, it );
			}
			this.informparent();
		},
		moved: function( vi ){
			if( vi < this.items2.length-2 ){
				//console.log( "one" );
				var it = JSON.parse( JSON.stringify( this.items2[ vi ] ) );
				this.items2.splice( vi, 1 );
				this.items2.splice( vi+1, 0, it );
			}else if( vi < this.items2.length-1 ){
				//console.log( "two" );
				var it = JSON.parse( JSON.stringify( this.items2[ vi ] ) );
				this.items2.splice( vi, 1 );
				this.items2.push( it );
			}
			this.informparent();
		},
		listitemedited: function( vi, vsubi, vdata ){
			this.items2[vi]['sub'][vsubi] = vdata;
			this.informparent();
		}
	},
	template: `<div v-if="showit">
				<table class="table table-bordered table-sm w-auto">
				<thead>
				<tr>
					<td>Name</td>
					<td>Type</td>
					<td>Mapped</td>
					<td>Mandatory</td>
					<td>-</td>
				</tr>
				</thead>
				<tbody>
				<tr v-for="vd,vf in items2"  >
					<td>
						<select class="form-select form-select-sm w-auto" v-model="vd['key']" v-on:change="field_change(vf)" >
							<option v-for="fd,fi in source_fields" v-bind:value="fi" >{{ fi }}</option>
						</select>
					</td>
					<td>{{ vd['mapped_type'] }}</td>
					<td>{{ vd['type'] }}</td>
					<td><input type="checkbox" v-model="vd['m']" v-on:click="informparent()" ></td>
					<td><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="deletenode(vf)" ></td>
				</tr>
				</tbody>
				</table>
			<div v-if="add_new_item==false">
				<input class="btn btn-outline-dark btn-sm" style="padding:0px 3px;" type='button' v-on:click="add_new_item=true" value='+'>
			</div>
			<div v-if="add_new_item" style="display:flex; column-gap:5px; margin-bottom:5px;">
				<select class="form-select form-select-sm" style="display:inline;width:150px;" v-model="new_item_name">
					<option value="" >-</option>
					<option v-for="vd,vf in source_fields" v-bind:value="vf" >{{ vf }}</option>
				</select>
				<input class="btn btn-outline-dark btn-sm"  style="padding:0px 3px;" type='button' v-on:click="addit" value='+'>
			</div>
	</div>`
};
