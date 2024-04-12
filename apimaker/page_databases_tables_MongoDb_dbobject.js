const dbobject_table_mongodb = {
	data: function(){
		return {
			items2: [],
			showit: false,
			add_new_item: false,
			new_item_name: "",
		}
	},
	props: ['items', 'level'],
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
			var v = {};
			for(var i=0;i<this.items2.length;i++){
				this.items2[i]['order'] = i;
				v[ this.items2[ i ]['name'] ] = this.items2[ i ];
			}
			//this.echo( v );
			//this.items = JSON.parse( JSON.stringify( v ) );
			//this.echo( this.items );
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
			//this.echo( this.items );
			//console.log("level = " + this.level);
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
			setTimeout(function(v){v.showit = true;},200,this);
		},
		fix_name: function( v ){
			v = v.replace(/\-/g, "XXXXXX");
			v = v.replace(/\_/g, "YYYYYY");
			v = v.replace(/\W+/g, "");
			v = v.replace(/XXXXXX/g, "-");
			v = v.replace(/YYYYYY/g, "_");
			return v;
		},
		edited_name: function( vkey ){
			var v = this.items2[ Number(vkey) ]['name'];
			v = v.replace(/\-/g, "XXXXXX");
			v = v.replace(/\_/g, "YYYYYY");
			v = v.replace(/\W+/g, "");
			v = v.replace(/XXXXXX/g, "-");
			v = v.replace(/YYYYYY/g, "_");
			v = v.toLowerCase();
			if( v.match( /^(pk|sk|pk2|sk2|pk2n|sk2n|pk3|sk3|pk3n|sk3n|pk4|sk4|pk4n|sk4n|pk5|sk5|pk5n|sk5n)/ ) ){
				v = v + "aa";
			}
			this.items2[ Number(vkey) ]['name'] = v;
			this.items2[ Number(vkey) ]['key'] = v;
			this.informparent();
		},
		addit: function(){
			this.new_item_name = this.fix_name( this.new_item_name );
			if( this.new_item_name.trim() ){
				this.items2.push({
					"key": this.new_item_name.trim()+"",
					"name": this.new_item_name.trim()+"",
					"type": "text",
					"m": true,
					"sub": {
					}
				});
				this.new_item_name = "";
				this.add_new_item = false;
				this.informparent();
			}
		},
		deletenode: function(vkey){
			this.items2.splice( Number(vkey), 1 );
			this.informparent();
		},
		change_field_type: function( vkey ){
			if( this.items2[ vkey ]['type'] =='list' ){
				this.items2[ vkey ]["sub"] = [
					{
						"f1": {
							"key": "f1",
							"name": "f1",
							"type": "text",
							"m": true,
							"index": "none",
							"sub": {
							}
						}
					}
				];
			}
			if( this.items2[ vkey ]['type'] =='dict' ){
				this.items2[ vkey ]["sub"] = {
					"f1": {
						"key": "f1",
						"name": "f1",
						"type": "text",
						"m": true,
						"index": "none",
						"sub": {
						}
					}
				};
			}
			this.informparent();
		},
		moveu: function( vi, vkey ){
			if( vi >= 2 ){
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
		addtolist: function( vi ){
			this.items2[vi]['sub'].push( JSON.parse( JSON.stringify( this.items2[vi]['sub'][0] ) ) );
			this.informparent();
		},
		listitemedited: function( vi, vsubi, vdata ){
			this.items2[vi]['sub'][ vsubi ] = vdata;
			this.informparent();
		}
	},
	template: `<div v-if="showit">
		<div>{</div>
		<div style="margin-left:30px;">
			<template v-for="vitem,vi in items2"  >
			<div style="white-space:nowrap; display:flex; column-gap:5px; margin-bottom:5px;">
				<div>
					<div v-if="level==1&&vitem['name']=='_id'" >_id</div>
					<div v-else >
						<input class="form-control form-control-sm" style="display:inline;width:150px;" type='text' v-model="vitem['name']" v-on:blur="edited_name(vi)" >
					</div>
				</div>
				<div>
					<div v-if="level==1&&'_id'==vitem['name']" >
						<span> ( primary ) </span>
						<select class="form-select form-select-sm" style="display:inline;width:100px;" v-model="vitem['type']" v-on:change="change_field_type(vi)" >
							<option value='UniqueId'>UniqueId</option>
							<option value='text'>Text</option>
							<option value='number'>Number</option>
						</select>
					</div>
					<select v-else class="form-select form-select-sm" style="display:inline;width:100px;" v-model="vitem['type']" v-on:change="change_field_type(vi)" >
						<option value='text'>Text</option>
						<option value='number'>Number</option>
						<option value='boolean'>Boolean</option>
						<option value='dict'>Assoc List</option>
						<option value='list'>List</option>
					</select>
				</div>
				<div>
					<input v-if="vitem['name']!='_id'" type="checkbox" v-model="vitem['m']" title="Mandatory" >
				</div>
				<div v-if="vitem['name']!='_id'" >
					<button class="btn btn-outline-secondary btn-sm me-2" style="padding:0px 5px;" v-on:click="moveu(vi)" >&#8593;</button>
					<button class="btn btn-outline-secondary btn-sm me-2" style="padding:0px 5px;" v-on:click="moved(vi)" >&darr;</button>
				</div>
				<div>
					<input v-if="vitem['name']!='_id'" class="btn btn-outline-danger btn-sm" style="padding:0px 3px;" type='button' v-on:click="deletenode(vi)" value='X' >
				</div>
			</div>
			<div v-if="vitem['type']=='dict'||vitem['type']=='list'" >
				<div v-if="vitem['type']=='dict'&&typeof(vitem['sub'])=='object'&&vitem['sub'].hasOwnProperty('length')==false" >
				<dbobject_table_mongodb v-bind:level="level+1" v-bind:items="vitem['sub']" v-on:edited="items2[vi]['sub']=$event" ></dbobject_table_mongodb>
				</div>
				<div v-else-if="vitem['type']=='list'&&typeof(vitem['sub'])=='object'&&vitem['sub'].hasOwnProperty('length')">
					<div>[</div>
						<div style="margin-left:30px;">
							<div v-for="vsub,vsubi in vitem['sub']" >
								<dbobject_table_mongodb v-bind:level="level+1" v-bind:items="vsub" v-on:edited="listitemedited(vi,vsubi,$event)" ></dbobject_table_mongodb>
							</div>
						</div>
					<div>]</div>
				</div>
				<div v-else>Incorrect List Data</div>
			</div>
			</template>
			<div v-if="add_new_item==false">
				<input class="btn btn-outline-dark btn-sm" style="padding:0px 3px;" type='button' v-on:click="add_new_item=true" value='+'>
			</div>
			<div v-if="add_new_item">
				<input type='text' v-model="new_item_name" placeholder="New Property" >
				<input class="btn btn-outline-dark btn-sm"  style="padding:0px 3px;" type='button' v-on:click="addit" value='+'>
			</div>
		</div>
		<div>}</div>
	</div>`
};
