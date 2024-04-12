<style>
	.sticky{
		position: sticky;top:0px; background-color: white;
	}
</style>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div class="btn btn-sm btn-outline-dark float-end" v-on:click="show_create_collection" >Add Collection</div>

			<div style="height:35px;"><h4>Database - <span class="small" style="color:#999;" ><?=ucwords($db['engine']) ?></span>: <?=htmlspecialchars($db['des']) ?></h4></div>

			<div style="height: calc( 100% - 100px ); overflow:auto; padding-right:20px;">

				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

				<div class="text-center border m-5 p-5" v-if="Object.keys(tables).length == 0">
					<h5 class="text-secondary">Create your first table</h5>
					<div class="btn btn-sm btn-outline-dark" v-on:click="show_create_collection" >Add Collection</div>
				</div>
				<!-- <pre>{{ tables }}</pre> -->
				<div v-if="Object.keys(tables).length > 0">

					<table class="table table-hover table-striped table-bordered table-sm w-auto" >
					<thead class="sticky">
						<tr>
							<td>Collection</td>
							<td align="right">Documents</td>
							<td align="right">AvgSize</td>
							<td align="right">Data Size</td>
							<td align="right">Storage Size</td>
							<td align="right">Indexes</td>
							<td align="right">IndexSize</td>
							<td>Capped</td>
						</tr>
					</thead>
					<tbody>
						<tr >
							<td>-</td>
							<td align="right"><b>{{ tot['objects'] }}</b></td>
							<td align="right">-</td>
							<td align="right"><b>{{ size_format(tot['datasize']) }}</b></td>
							<td align="right"><b>{{ size_format(tot['storageSize']) }}</b></td>
							<td align="right">-</td>
							<td align="right"><b>{{ size_format(tot['indexSize']) }}</b></td>
							<td align="center">-</td>
						</tr>
						<tr v-for="d in tables">
							<td><a v-bind:href="dbpath+'table/'+d['_id']+'/manage'" >{{ d['collection'] }}</a></td>
							<td align="right">{{ d['count'] }}</td>
							<td align="right">{{ size_format(d['avgObjSize']) }}</td>
							<td align="right">{{ size_format(d['size']) }}</td>
							<td align="right">{{ size_format(d['storageSize']) }}</td>
							<td align="right">{{ d['nindexes'] }}</td>
							<td align="right">{{ size_format(d['totalIndexSize']) }}</td>
							<td align="center">{{ (d['capped']?"Capped":"") }}</td>
						</tr>
					</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="create_popup" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Create Collection</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      		<p>New Collection:</p>
				<p><input class="form-control form-control-sm" v-model="new_collection"></p>
				<p><input type="button" class="btn btn-outline-dark btn-sm" value="Create" v-on:click="create_collection"></p>
				<div v-if="cmsg" class="alert alert-primary" >{{ cmsg }}</div>
				<div v-if="cerr" class="alert alert-danger" >{{ cerr }}</div>

	      </div>
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
				"tables": {},
				"tot": {
					'objects': "",
					'datasize': "",
					'storageSize': "",
					'indexSize': "",
					'views': "",
				},
				"msg":"","err":"","cmsg":"","cerr":"",
				"new_collection": "",
				"create_popup": false,
			};
		},
		mounted: function(){
			this.load_tables();
		},
		methods: {
			show_create_collection: function(){
				this.create_popup = new bootstrap.Modal(document.getElementById('create_popup'));
				this.create_popup.show();
			},
			create_collection: function(){
				this.cerr = "";this.cmsg = "";
				this.new_collection = this.new_collection.toLowerCase().trim();
				var c = this.new_collection+'';
				if( c.match(/^[a-z][a-z0-9\-\_]{2,50}$/i) == null ){
					this.cerr = "Please choose a proper name. [a-z][a-z0-9\-\_]{2,50}";return false;
				}
				this.cmsg = "Creating...";
				axios.post("?", {
					"action":"database_mongodb_create_collection",
					"collection": this.new_collection
				}).then(response=>{
					this.cmsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.cmsg = "Collection created";
									this.load_tables();
								}else{
									this.cerr = "Error:"+response.data['error'];
								}
							}else{
								this.cerr = "Incorrect response";
							}
						}else{
							this.cerr = "Invalid resposne";
						}
					}else{
						this.cerr = "Error:"+response.status;
					}
				}).catch(error=>{
					this.cerr = "Error:"+error.message;
				});
			},
			size_format: function(v){
				// $kb = $v/1024;
				kb = Number(v)/1024;
				if( kb < 1024 ){
					return kb.toFixed(0) + " KB";
				}
				mb = kb/1024;
				if( mb < 1024 ){
					return mb.toFixed(0) + " MB";
				}
				gb = mb/1024;
				if( gb < 1024 ){
					return "<span style='color:red;'>" + gb.toFixed(0) + " GB</span>";
				}
			},
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
					"action"	: "database_mongodb_load_tables",
					"db_id"		: this.db_id,
				}).then(response=>{
					if( response.data.hasOwnProperty("status") ){
						var vdata = response.data;
						if(vdata['status'] == "success"){
							this.tables = vdata['tables'];
							this.tot = vdata['tot'];
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