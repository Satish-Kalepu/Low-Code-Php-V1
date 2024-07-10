<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div style="float:right;"><a class="btn btn-outline-secondary btn-sm" v-bind:href="dbpath">Back</a></div>

			<h4>Database - <span class="small" style="color:#999;" ><?=ucwords($db['engine']) ?></span>: <?=htmlspecialchars($db['des']) ?> &nbsp;&nbsp;&nbsp;<span class="small" style="color:#999;" >Table:</span> {{ table['des'] }} </h4>

		<ul class="nav nav-tabs mb-2" >
			<li class="nav-item">
				<a class="nav-link<?=$config_param6=='records'||$config_param6==''?" active":"" ?>" v-bind:href="tablepath+'records'">Records</a>
			</li>
			<li class="nav-item">
				<a class="nav-link<?=$config_param6=='manage'?" active":"" ?>" v-bind:href="tablepath+'manage'">Manage</a>
			</li>
			<li class="nav-item">
				<a class="nav-link<?=$config_param6=='structure'?" active":"" ?>" v-bind:href="tablepath+'structure'">Structure</a>
			</li>
			<li class="nav-item">
				<a class="nav-link<?=$config_param6=='import'?" active":"" ?>" v-bind:href="tablepath+'import'">Import</a>
			</li>
			<li class="nav-item">
				<a disabled class="nav-link<?=$config_param6=='export'?" active":"" ?>" v-bind:href="tablepath+'export'">Export</a>
			</li>
		</ul>

			<div style="overflow: auto;height: calc( 100% - 130px );">

				<p><b>Export Format</b></p>
				<p>
					<label style="cursor: pointer; margin-right: 30px;"><input type="radio" v-model="exp['type']" value="CSV" > CSV</label>
					<label style="cursor: pointer;"><input type="radio" v-model="exp['type']" value="JSON" > JSON</label>
				</p>
<!--
				<p><label style="cursor: pointer;"><input type="checkbox" v-model="exp['compress']" > Compressed Archive</label></p>
				<p><label style="cursor: pointer;"><input type="checkbox" v-model="exp['pass']" > Password Protection</label></p>
 -->
 				<p><input v-if="show_btn" type="button" class="btn btn-outline-dark btn-sm" value="Export" v-on:click="export_data" ></p>

				<div class="alert alert-success" v-if="msg" v-html="msg" ></div>
				<div class="alert alert-danger" v-if="err" v-html="err" ></div>

				<div v-if="'temp_fn' in file" >
					<p><a v-bind:href="geturl()" target="_blank" >Click here to download the data file.</a></p>
					<p>Size {{ file['sz'] }}</p>
				</div>

			</div>

		</div>
	</div>
</div>
<script type="text/javascript">
var app = Vue.createApp({
	"data"	: function(){
		return {
			"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			"dbpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/databases/<?=$config_param3 ?>/",
			"tablepath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/databases/<?=$config_param3 ?>/table/<?=$config_param5 ?>/",
			"app_id": "<?=$config_param1 ?>",
			"db_id": "<?=$config_param3 ?>",
			"table_id": "<?=$config_param5 ?>",
			"table": <?=json_encode($table,JSON_PRETTY_PRINT) ?>,
			"msg": "", "err": "",
			"exp": {
				'type':'JSON',
				'compress': true,
				'pass': false,
				'passwd': ""
			},
			"file":{},
			"show_btn": true,
		};
	},
	created: function(){

	},
	methods:{
		echo__: function(v__){
			if( typeof(v__) == "object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
		geturl: function(){
			return this.tablepath+'export/?action=download_database_mysql_snapshot&snapshot_file='+encodeURIComponent(this.file['temp_fn']);
		},
		export_data: function(){
			this.file = {};
			this.msg = ""; this.err = "";
			axios.post("?", {
				"action": "database_mysql_export",
				"exp": this.exp
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.file = response.data;
								this.show_btn = false;
								setTimeout(function(v){v.show_btn=true;},30000,true);
							}else{
								this.err = "Error: " + response.data['error'];
							}
						}else{
							this.err = "Incorrect Response";
						}
					}else{
						this.err = "Incorrect Response";
					}
				}else{
					this.err = "http: " + response.status;
				}
			}).catch(error=>{
				this.err = "Exception: " + error.message;
			});
		}
	}
});
app.mount( "#app" );
</script>