<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div style="float:right;"><a class="btn btn-outline-secondary btn-sm" v-bind:href="dbpath">Back</a></div>

			<h4>Table - <?=ucwords($table['table']) ?></h4>

			<ul class="nav nav-tabs mb-2">
				<li class="nav-item">
					<a class="nav-link<?=$config_param4=='records'||$config_param4==''?" active":"" ?>" v-bind:href="dbpath+'records'">Records</a>
				</li>
				<li class="nav-item">
					<a class="nav-link<?=$config_param4=='manage'?" active":"" ?>" v-bind:href="dbpath+'manage'">Manage</a>
				</li>
				<li class="nav-item">
					<a class="nav-link<?=$config_param4=='import'?" active":"" ?>" v-bind:href="dbpath+'import'">Import</a>
				</li>
				<li class="nav-item">
					<a disabled class="nav-link<?=$config_param4=='export'?" active":"" ?>" v-bind:href="dbpath+'export'">Export</a>
				</li>
			</ul>

			<div style="height: calc( 100% - 120px ); overflow: auto; ">

				<div>&nbsp</div>

				<div style="border: 1px solid #ccc; margin-right: 20px; " >
					<div style="padding:10px; background-color: #f0f0f0; border-bottom: 1px solid #ccc; ">Export Data</div>
					<div style="padding:10px; ">
						<div style="display:flex; gap:20px; margin-bottom: 10px;">
							<div class="mb-2" >
								<div>Format: </div>
								<select class="form-select form-select-sm w-auto" v-model="export_type" >
									<option value="CSV" >CSV</option>
									<option value="JSON" >JSON</option>
									<option value="XLS" >XLS</option>
									<option value="XLSX" >XLSX</option>
								</select>
							</div>
							<div class="mb-2" >
								<div>Limit: </div>
								<input type="number" class="form-control form-control-sm" style="width:80px;" v-model="limit" >
							</div>
							<div class="mb-2" >
								<div>&nbsp;</div>
								<input type="button" class="btn btn-outline-dark btn-sm" v-on:click="exportdata" value="Export" >
							</div>
						</div>
						<div v-if="msg" >{{ msg }}</div>
						<div v-if="err" style="color:red;" >{{ err }}</div>

						<div v-if="snapshot_file" class="mb-3" >
							<p><a v-bind:href="geturl()" target="_blank" >Click here to download the data file.</a></p>
							<p>Size {{ snapshot_size }}</p>
						</div>
					</div>
				</div>

				<div style="border: 1px solid #ccc; margin-top: 20px; margin-right: 20px; " >
					<div style="padding:10px; background-color: #f0f0f0; border-bottom: 1px solid #ccc; ">Export Dump</div>
					<div style="padding:10px; ">
						<p>Dump is a snapshot archive which contain table schema and index settings along with records.</p>
						<div class="mb-2">
							<label style="cursor: pointer;"><input type="checkbox" v-model="exportpwd" > Password protect the dump </label>
						</div>
						<div v-if="exportpwd" class="mb-2">
							<input type="text"   class="form-control form-control-sm w-auto" v-model="exportpass" placeholder="password" >
						</div>
						<div class="mb-2">
							<input type="button" class="btn btn-outline-dark btn-sm" v-on:click="exportdump" value="Create Dump" >
						</div>

						<div v-if="msg2" >{{ msg2 }}</div>
						<div v-if="err2" style="color:red;" >{{ err2 }}</div>

						<div v-if="snapshot_file2" class="mb-3" >
							<p><a v-bind:href="geturl2()" target="_blank" >Click here to download the data file.</a></p>
							<p>Size {{ snapshot_size2 }}</p>
						</div>

						<div>-</div>

					</div>
				</div>



			</div>

		</div>
	</div>

</div>

<script>
var app = Vue.createApp({
	"data"	: function(){
		return {
			"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			"dbpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tables_dynamic/<?=$config_param3 ?>/",
			"app_id": "<?=$config_param1 ?>",
			"table_id": "<?=$config_param3 ?>",
			"table": <?=json_encode($table,JSON_PRETTY_PRINT) ?>,
			"token": "",
			"vshow": true,
			"msg": "", "err": "", "msg2": "", "err2": "", "msg3": "", "err3": "", 
			"export_type": "CSV",
			"limit": 1000,
			"snapshot_file": "","snapshot_size": 0,	
			"snapshot_file2": "","snapshot_size2": 0,	
			"data_file": "","data_size": 0,	
			"exportpwd": false, "exportpass": "",
		};
	},
	mounted : function(){
		//this.load_source_tables();

	},
	methods: {
		echo__: function(v){
			if( typeof(v)=="object" ){
				console.log( JSON.stringify(v,null,4) );
			}else{
				console.log( v );
			}
		},
		is_token_ok(t){
			if( t!= "OK" && t.match(/^[a-f0-9]{24}$/)==null ){
				setTimeout(this.token_validate,100,t);
				return false;
			}else{
				return true;
			}
		},
		token_validate(t){
			if( t.match(/^(SessionChanged|NetworkChanged)$/) ){
				this.err = "Login Again";
				alert("Need to Login Again");
			}else{
				this.err = "Token Error: " + t;
			}
		},
		geturl: function(){
			return this.path+'export/?action=download_snapshot&snapshot_file='+encodeURIComponent(this.snapshot_file);
		},
		geturl2: function(v){
			return this.path+'export/?action=download_snapshot&snapshot_file='+encodeURIComponent(this.snapshot_file2);
		},
		exportdata: function(){
			this.err = "";
			this.msg = "Creating file ...";
			this.snapshot_file = "";this.snapshot_size = "";
			axios.post("?",{"action":"tables_dynamic_export_data", "export_type": this.export_type}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.snapshot_file = response.data['temp_fn'];
								this.snapshot_size = response.data['sz'];
							}else{
								this.err = "Export Error: " + response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response Type";
					}
				}else{
					this.err = "Response Error: " . response.status;
				}
			});
		},
		exportdump: function(){
			this.err2 = "";
			this.snapshot_file2 = "";this.snapshot_size2 = "";
			if( this.exportpwd ){
				if( this.exportpass.trim() == "" ){
					alert("Enter password!");
					return ;
				}
			}
			this.msg2 = "Creating dump";
			axios.post("?",{
				"action":"tables_dynamic_export_dump", 
				"exportpwd": this.exportpwd,
				"exportpass": this.exportpass,
			}).then(response=>{
				this.msg2 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.snapshot_file2 = response.data['temp_fn'];
								this.snapshot_size2 = response.data['sz'];
							}else{
								this.err2 = "Export Error: " + response.data['error'];
							}
						}else{
							this.err2 = "Incorrect response";
						}
					}else{
						this.err2 = "Incorrect response Type";
					}
				}else{
					this.err2 = "Response Error: " . response.status;
				}
			});
		}
	}
});
app.mount("#app");

</script>
