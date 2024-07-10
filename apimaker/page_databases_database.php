<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
			<div v-if="err" class="alert alert-danger" >{{ err }}</div>
			<a v-bind:href="dbpath+'table/new/manage'" class="btn btn-sm btn-outline-dark float-end" >Add Table</a>

			<h4>Database - <span class="small" style="color:#999;" ><?=ucwords($db['engine']) ?></span>: <?=htmlspecialchars($db['des']) ?></h4>

			<div class="text-center border m-5 p-5" v-if=" tables.length == 0">
				<h5 class="text-secondary">Create your first table</h5>
				<div><a v-bind:href="dbpath+'table/new/manage'" class="btn btn-sm btn-outline-dark" >Add Table</a></div>
			</div>
			<div v-if=" tables.length > 0">
				<table class="table table-bordered table-sm table-hovered" >
					<tr v-for="td,ti in tables" >
						<td>
							<div><a style="font-size: 20px;" v-bind:href="dbpath+'table/'+td['_id']+'/manage'" >{{ td['des'] }}</a></div>
							<div>{{ td['table'] }}</div>
						</td>
						<td>
							<div  style="overflow: auto; max-height: 200px;" >
							<div v-for="sd,si in td['schema']" >
								<div>{{ sd['name'] }}</div>
								<pre v-if="'fields' in sd" >{{ get_data_model( sd['fields'] ) }}</pre>
							</div>
							</div>
						</td>
						<td>
							<div v-if="engine=='dynamodb'" style="overflow: auto; max-height: 200px;" >
								<table class="table table-bordered table-sm" style="width:initial">
									<tr>
										<td>IndexName</td>
										<td>Key</td>
										<td>Sort Key</td>
									</tr>
									<tr>
										<td>Primary</td>
										<td>{{ td['pk']['field'] }}  <span class='text-secondary' >{{ td['pk']['type'] }}</span></td>
										<td><span v-if="td['sk']['enable']" >{{ td['sk']['field'] }}  <span class='text-secondary' >{{ td['sk']['type'] }}</span></span></td>
									</tr>
									<template v-if="Object.keys(td['keys']).length>0" style="margin-top:10px;" >
									<tr v-for="v,i in td['keys']" >
										<td>{{ v['name'] }}</td>
										<td>{{ v['pk']['field'] }} <span class='text-secondary' >{{ v['pk']['type'] }}</span></td>
										<td>
											<span v-if="v['sk']['enable']" >
											{{ v['sk']['field'] }} <span class='text-secondary' >{{ v['sk']['type'] }}</span>
											</span>
										</td>
									</tr>
									</template>
								</table>
							</div>
							<div v-else>
								<div>_id: Primary Key</div>
								<template v-if="Object.keys(td['keys']).length>0" style="margin-top:10px;" >
								<table class="table table-bordered table-sm" style="width:initial" >
									<tr>
										<td>IndexName</td>
										<td>Keys</td>
									</tr>
									<tr  v-for="kd,ki in td['keys']" >
										<td>{{ ki }}</td>
										<td><div v-for="vkd,vki in kd['keys']" >{{ vkd['name'] }} <span class="text-secondary" >{{ vkd['sort'] }}</span></div></td>
									</tr>
								</table>
								</template>
							</div>
						</td>
						<td width="20"><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_table(ti)" ></td>
					</tr>
				</table>
			</div>
		</div>
	</div>



</div>

<script>
var app = Vue.createApp({
		data: function(){
			return {
				"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
				"dbpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/databases/<?=$config_param3 ?>/",
				"app__": <?=json_encode($app) ?>,
				"db": <?=json_encode($db) ?>,
				"db_id": "<?=$config_param3 ?>",
				"engine": "<?=$db['engine'] ?>",
				"tables": [
				],
			};
		},
		mounted: function(){
			this.load_tables();
		},
		methods: {
			echo: function(v){
				if( typeof(v) == "object" ){
					console.log( JSON.stringify(v,null,4) );
				}else{
					console.log( v );
				}
			},
			get_data_model: function( t ){
				console.log( t)
				return this.create_data_template( JSON.parse( JSON.stringify( t )));
			},
			create_data_template: function( vdata__ ){
				for( var i in vdata__ ){
					if( i == "_id" ){
						vdata__[ i ] = "(Primary Key)";
					}else if( vdata__[i]['type'] == "dict" ){
						vdata__[i] = this.create_data_template( vdata__[i]['sub'] );
					}else if( vdata__[i]['type'] == "list" ){
						var vv = [];
						for( var vsubd=0;vsubd<vdata__[i]['sub'].length;vsubd++){
							vv.push( this.create_data_template( vdata__[i]['sub'][vsubd] ) );
						}
						vdata__[i] = vv;
					}else if( vdata__[i]['type'] == "number" ){
						vdata__[i] = "number";
					}else{
						vdata__[i] = "text";
					}
				}
				return vdata__;
			},
			delete_table:function(vid){
				if( confirm("Are You Sure to Delete Table") ){
					vd__ = {
						"action"		: "delete_table",
						"db_id"			: this.tables[ vid ]['db_id'],
						"table_id"			: this.tables[ vid ]['_id'],
					};
					axios.post("?",vd__).then(response=>{
						if( "status" in response.data ){
							var vdata = response.data;
							if( vdata['status'] == "success" ){
								this.load_tables();
							}else{
								alert( "Error deleting table: \n" + vdata["details"] );
							}
						}else{
							alert( "Incorrect response: \n" + response.data );
						}
					});
				}
			},
			load_tables:function(){
				axios.post("?",{
					"action"	: "load_tables",
					"db_id"		: this.db_id,
				}).then(response=>{
					if( response.data.hasOwnProperty("status") ){
						var vdata = response.data;
						if(vdata['status'] == "success"){
							this.tables = vdata['tables']
						}else{
							this.error = vdata['error'];
						}
					}else{
						this.error = response.data;
					}
				});
			},
		}
}).mount("#app");
</script>