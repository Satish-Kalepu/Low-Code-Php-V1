<style>

table.zz td div{ max-width:250px; max-height:75px;overflow:auto; white-space:nowrap; }
table.zz thead td { background-color:#666; color:white; }

div.zz::-webkit-scrollbar {width: 6px;height: 6px;}
div.zz::-webkit-scrollbar-track { background: #f1f1f1;}
div.zz::-webkit-scrollbar-thumb { background: #888;}
div.zz::-webkit-scrollbar-thumb:hover { background: #555;}

pre.zzz{ max-height:150px; width:auto;overflow:auto; margin:20px 10px; padding:10px; border:1px solid #999; }
pre.zzz::-webkit-scrollbar {width: 12px;height: 12px;}
pre.zzz::-webkit-scrollbar-track { background: #f1f1f1;}
pre.zzz::-webkit-scrollbar-thumb { background: #888;}
pre.zzz::-webkit-scrollbar-thumb:hover { background: #555;}

pre.fff{ max-height:300px; width:auto;overflow:auto; padding:10px; margin-right:20px; border:1px solid #999; }
pre.fff::-webkit-scrollbar {width: 12px;height: 12px;}
pre.fff::-webkit-scrollbar-track { background: #f1f1f1;}
pre.fff::-webkit-scrollbar-thumb { background: #888;}
pre.fff::-webkit-scrollbar-thumb:hover { background: #555;}

pre.sample_data{ height:300px;overflow:auto; white-space:nowrap; border:1px solid #333; }
pre.sample_data::-webkit-scrollbar {width: 6px;height: 6px;}
pre.sample_data::-webkit-scrollbar-track { background: #f1f1f1;}
pre.sample_data::-webkit-scrollbar-thumb { background: #888;}
pre.sample_data::-webkit-scrollbar-thumb:hover { background: #555;}

</style>
<div id="app" >
	<div class="leftbar" >
		<?php require( "page_apps_leftbar.php" ); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div style="float:right;"><a class="btn btn-outline-secondary btn-sm" v-bind:href="path+'tables_dynamic'">Back</a></div>

			<h4>Table - Restore from Backup Snapshot</h4>

			<div style="overflow: auto; padding:10px 20px 10px 0px; height: calc( 100% - 100px - 20px ); padding-right:10px;">

				<div style="border:1px solid #ccc; padding:10px; margin-bottom: 20px;" >
					<p>Upload Snapshot</p>
					<p><input type="file" id="upload_file" class="form-control form-control-sm w-auto" style="" v-on:change="fileselect" ></p>
					<p><label style="cursor: pointer;"><input type="checkbox" v-model="importpwd" > &nbsp; Is dump password protcted?</label></p>
					<p v-if="importpwd"><input type="text" class="form-control form-control-sm" placeholder="Password" v-model="importpass" ></p>
					<p><input type="button" class="btn btn-outline-dark btn-sm" value="Upload" v-on:click="upload_now" ></p>

					<p v-if="msg" >{{ msg }}</p>
					<p v-if="err" style="color:red;" >{{ err }}</p>
				</div>

				<div v-if="'status' in upload_data" style="border:1px solid #ccc; padding:10px; margin-bottom: 50px;" >
					<p>Table Settings:</p>
					<div class="mb-2">
						<div>Table Name</div>
						<div><input type="text" class="form-control form-control-sm" v-model="upload_data['table']['table']" placeholder="New table name"></div>
					</div>
					<div class="mb-2">
						<div>Description</div>
						<div><textarea class="form-control form-control-sm" v-model="upload_data['table']['des']" placeholder="Description"></textarea></div>
					</div>

					<div v-if="'table' in upload_data['current_table']" class="mb-2" >
						<p>A table already exists with the similar settings with name `{{ upload_data['current_table']['table'] }}` </p>
						<div><label sytle="cursor:pointer;"><input type="radio" v-model="vreplace" value="same_id" > &nbsp; Replace existing table </label></div>
						<div><label sytle="cursor:pointer;"><input type="radio" v-model="vreplace" value="new" > &nbsp; Create new table </label></div>
						<div>&nbsp;</div>
					</div>

					<div class="mb-2">
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="Proceed" v-on:click="proceed_now()" ></div>
					</div>

					<div v-if="msg2" >{{ msg2 }}</div>
					<div v-if="err2" style="color:red;" >{{ err2 }}</div>

				</div>

				<div v-if="new_table_id" style="border:1px solid #ccc; padding:10px; margin-bottom: 50px;">
					<p>Table successfully restored</p>
					<p><a target="_blank" v-bind:href="path+'tables_dynamic/'+new_table_id+'/records'" >New Table: {{ new_table_id }}</a></p>
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
			"app_id": "<?=$config_param1 ?>",
			"token": "",
			"vshow": true,
			"msg": "", "err": "", "msg2": "", "err2": "", "msg3": "", "err3": "", 
			"importpwd": false, "importpass": "",
			"upload_data": {},
			"vreplace": "none",
			"new_table_id": "",
		};
	},
	mounted : function(){
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
		get_size: function(){
			var s = this.vf.size;
			if( s < 1024 ){ return s + " bytes";}
			if( s/1024 < 1024 ){ return (s/1024).toFixed(0) + " KB";}
			if( s/1024/1024 < 1024 ){ return (s/1024/1024).toFixed(2) + " MB";}
		},
		fileselect: function(){
			this.err = "";
			this.sample_data = "";
			var vf = document.getElementById("upload_file").files[0];
			if( vf.name.match(/\.table_dynamic_dump.gz$/i) == null ){
				document.getElementById("upload_file").value = "";
				alert("Please select a proper snapshot dump file with extension .table_dynamic_dump.gz");return false;
			}
			if( vf.size > (1024*1024*50) ){
				document.getElementById("upload_file").value = "";
				this.vf = false;
				alert("Please select a file with a size less than 50MB");return false;
			}
			this.vf = vf;
		
		},
		upload_now: function(){

			if( document.getElementById("upload_file").value == "" ){
				return false;
			}

			this.upload_data = {};
			this.vreplace = "none";
			this.new_table_id = "";

			this.msg = "Fetching token...";
			this.err = "";
			axios.post("?", {
				"action":"get_token",
				"event":"tables_dynamic_importdump."+this.app_id,
				"expire":10,
				"max_hits": 1000,
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.upload_now2();
								}
							}else{
								alert("Token error: " + response.dat['data']);
								this.err = "Token Error: " + response.data['data'];
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
		upload_now2: function(){

			var vpost = new FormData();
			vpost.append("action", "tables_dynamic_importdump");
			vpost.append("file", this.vf );
			vpost.append("importpwd", this.importpwd);
			vpost.append("importpass", this.importpass);
			vpost.append("token", this.token );
			axios.post("?", vpost,{
				onUploadProgress: function (e) {
					var l = (e.loaded/e.total*100).toFixed(0);
					this.msg = "Uploading... " + l + "%";
				}
			}).then(response=>{
				if( response.status == 200 ){
					if( typeof(response.data)=="object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg = "Upload Completed";
								this.upload_data = response.data;
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = 'Incorrect Response';
						}
					}else{
						this.err = 'Incorrect Response';
					}
				}else{
					this.err = 'http:'+response.status;
				}
			}).catch(error=>{
				console.log( error );
				this.err = error.msg;
			});

		},

		proceed_now: function(){

			this.err2 = "";
			this.upload_data['table']['table'] = this.upload_data['table']['table'].replace(/\W/g, '').substr(0, 25);
			this.upload_data['table']['des'] = this.upload_data['table']['des'].substr(0, 250);

			if( this.upload_data['table']['table'].match(/^[a-z0-9\.\-\_\ ]{3,25}$/i) == null ){
				this.err2 = "Need table name in [a-z0-9.-_ ]{3,25}"; return false;
			}
			if( this.upload_data['table']['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\ \r\n]{5,200}$/i) == null ){
				this.err2 = "Need description in [a-z0-9.-_&,!@]{5,200}"; return false;
			}

			if( 'table' in this.upload_data['current_table'] ){
				if( this.vreplace == "none" ){
					this.err2 = "Please choose replace strategy"; return false;
				}
			}

			this.msg2 = "Importing...";
			axios.post("?", {
				"action":"tables_dynamic_importdump2",
				"table":this.upload_data['table'],
				"vreplace":this.vreplace,
				"importpwd": this.importpwd,"importpass": this.importpass,
				"tmp": this.upload_data['tmpname'],
			}).then(response=>{
				if( response.status == 200 ){
					if( typeof(response.data)=="object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg2 = "Upload Completed";
								this.new_table_id = response.data['new_table_id'];
								this.upload_data = {};
								this.msg2 = "";this.err2 = "";
								this.msg = "";this.err = "";
							}else{
								this.err2 = response.data['error'];
							}
						}else{
							this.err2 = 'Incorrect Response';
						}
					}else{
						this.err2 = 'Incorrect Response';
					}
				}else{
					this.err2 = 'http:'+response.status;
				}
			}).catch(error=>{
				console.log( error );
				this.err2 = error.msg;
			});
		},

	}
});

app.mount("#app");

</script>

