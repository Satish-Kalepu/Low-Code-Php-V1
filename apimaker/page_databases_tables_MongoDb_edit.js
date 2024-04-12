const table_mongodb_edit_record = {
	data: function(){
		return {
			//"schema": {}
		}
	},
	props: [ "schema" ],
	mounted(){
		
	},
	methods : {
		echo__: function(v__){
	        	if( typeof(v__) == "object" ){
		                console.log( JSON.stringify(v__,null,4) );
	            	}else{
	                	console.log( v__ );
	            	}
	        },
	        create_field_template_edit(vfields__,vdata__){
			for( var i in vfields__ ){
				if( vfields__[i]['type'] == "dict" ){
					if( vdata__.hasOwnProperty(i) == false ){
						vdata__[i] = {};
					}
					vfields__[i]['sub'] = this.create_field_template_edit( vfields__[i]['sub'],vdata__[i] );
				}else if( vfields__[i]['type'] == "list" ){
					vfields__[i]['data'] = [];
					if( vdata__.hasOwnProperty(i) == false ){
						vdata__[i] = [];
						
					}
					for(var jj=0;jj<vdata__[i].length;jj++){
						var vp = {};
						for( var j=0;j<vfields__[i]['sub'].length;j++ ){
							vp = this.create_field_template_edit( vfields__[i]['sub'][j] ,vdata__[i][jj] );
						}
						vfields__[i]['data'].push(vp);
					}
				}else{
					if( vdata__.hasOwnProperty(i) == false ){
						vdata__[i] = '';
					}
					vfields__[i]['data'] = vdata__[i];
				}
			}
			return vfields__;
		},
		list_append: function( vi ){
			if( this.schema[ vi ]['data'].length ){
				var v = JSON.parse( JSON.stringify(this.schema[ vi ]['data'][0]));
			}else{
				var v = JSON.parse( JSON.stringify( this.create_field_template_edit( this.schema[ vi ]['sub'][0], {} ) ) );
			}
			this.schema[ vi ]['data'].push( v );
		},
		delete_list_item: function( vi ){
			this.schema[ vi ]['data'].splice( vi ,1 );
		}
	},
	template:`<div>
		<div>{</div>
		<div style="margin-left:20px;">
		<div v-for="vd,vi in schema" >
			<div v-if="vd['type']=='text'||vd['type']=='number'" >
				&quot;{{ vd['name'] }}&quot;: <input v-bind:type="vd['type']" v-model="vd['data']" style="width:150px; padding:0px; margin:0px; border:1px solid #999; background-color:#f0f0f0; line-height:initial;" > 
				<span v-if="vd['name']=='_id'">Auto Generated</span>
			</div>
			<div v-else-if="vd['type']=='boolean'" >
				&quot;{{ vd['name'] }}&quot;: <input type="checkbox" v-model="vd['data']" >
			</div>
			<div v-else-if="vd['type']=='list'" >
				<div style="float:left;" >&quot;{{ vd['name'] }}&quot;: &nbsp;</div>
					<div>[</div>
						<div style="margin-left:20px;" v-for="vsubd,vsubi in vd['data']">
							<input type="button" value="X" v-on:click="delete_list_item(vi,vsubi)" style="float:right;padding:2px;" >
							<table_mongodb_edit_record v-bind:schema="vsubd" v-on:edited="update_list__($event,fi)" ></table_mongodb_edit_record>
						</div>
						<div style="margin-left:20px;" >
							<input type="button" value="+" v-on:click="list_append(vi)" style="padding:0px; margin:0px; font-size:12px; height:20px; font-weight:bold; line-height:initial; ">
						</div>
					<div>]</div>
				<div style="clear:both;"></div>
			</div>
			<div v-else-if="vd['type']=='dict'" >
				<div style="float:left;" >&quot;{{ vd['name'] }}&quot;:  &nbsp;</div>
					<table_mongodb_edit_record v-bind:schema="vd['data']" ></table_mongodb_edit_record>
				<div style="clear:both;"></div>
			</div>
		</div>
		</div>
		<div>}</div>
	</div>`

};
