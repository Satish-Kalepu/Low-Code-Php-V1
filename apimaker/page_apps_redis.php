<style>
	.redisk{ padding:0px 5px; border-bottom:1px solid #ccc; cursor:pointer; }
	.redisk:hover{ background-color:#f8f8f8; }
	.redisk_key{ padding:0px 5px; border-bottom:1px solid #ccc;}
</style>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
	<div class="m-2">
		<div class="row">
			<div class="col-6 h3"><span class="text-secondary" >Key Value Store</span></div>
			<div class="col-6"><div class="btn btn-sm btn-outline-secondary float-end" v-on:click="show_configure()" >Configure</div></div>
		</div>
		<div class="row mt-2" v-if="saved&&settings['enable']">
			<div class="col-3">
				<select id="type" name="type" class="form-select w-100" v-model="data_type">
					<option value="">All key Types</option>
					<option value="string">String</option>
					<option value="set">Set</option>
					<option value="list">List</option>
					<option value="zset">Sorted Set (ZSet)</option>
					<option value="hash">Hash</option>
				</select>
			</div>
			<div class="col-6">
				<input type="text" class="form-control w-100" v-model="keyword" placeholder="Filter by Key Name or Pattern">
			</div>
			<div class="col-3">
				<div class="d-flex">
					<input type="button" class="mx-2 btn btn-outline-dark" value="Search" v-on:click="load_keys()">
					<input type="button" class="btn btn-outline-dark" value="Add Key" v-on:click="add_configure()">
				</div>
			</div>
		</div>
		<div class="row mt-3 mx-1">
			<div v-if="msg" class="alert alert-primary col-12" >{{ msg }}</div>
			<div v-if="err" class="alert alert-danger col-12" >{{ err }}</div>
			<div v-if="saved==false||settings['enable']==false" style="padding:50px; margin: 50px; border: 1px solid #ccc;" >
				<p>Key Value store is not enabled</p>
				<div class="btn btn-outline-dark btn-sm" v-on:click="show_configure()">Configure Redis</div>
			</div>
			<template v-else>
				<div class="row">
					<div :class="'key' in show_key?'col-6 p-2':'col-12 p-2'" style="border: 1px solid #ccc;">
						<div class="d-flex justify-content-between">
							<div><b>Key</b></div>
							<div class="d-flex justify-content-between">
								<div>Records Count : <b>{{ key_count }}</b></div>
								<div class="m-2"></div>
								<div class="m-auto p-1">
									<i style="cursor: pointer;" class="fa fa-solid fa-rotate-right text-secondary" v-on:click="load_keys()" title="Refresh"></i>
								</div>
							</div>
						</div>
					</div>
					<div :class="'key' in show_key?'col-6 p-2':'d-none'" style="border: 1px solid #ccc;">
						<div class="d-flex justify-content-between">
							<div class="d-flex">
								<div v-if="'key' in show_key"><b>{{show_key['key'] }}</b></div>
								<div class="m-2"></div>
								<span class="badge bg-dark p-2 m-auto" style="font-size: 10px;" v-if="'data' in show_key && show_key['data']['type'] != ''">{{show_key['data']['type']}}</span>
							</div>
							<div class="">
								<i style="cursor: pointer;" v-on:click="show_key = []" class="fa fa-times" title="close"></i>
							</div>
						</div>
					</div>
					<div :class="'key' in show_key?'col-6 p-2':'col-12 p-2'" style="height:calc( 100% - 150px ); min-height: 500px;overflow:auto; border:1px solid #ccc;">
						<div class="d-flex justify-content-between redisk" v-for="k in keys" v-on:click="load_key(k['key'])">
							<div class="">
								<div class="p-2">{{ k['key'] }}</div>
							</div>
							<div class="">
								<div class="d-flex justify-content-between p-2">
									<span class="badge rounded-pill bg-danger m-auto p-2" style="font-size: 10px;">{{k['time']}}</span>
									<div class="m-2"></div>
									<span class="badge rounded-pill bg-dark m-auto p-2" style="font-size: 10px;">{{k['size']}} B</span>
									<div class="m-2"></div>
									<span class="badge bg-secondary p-2 text-white" style="font-size: 12px;">{{ k['type'] }}</span> 
									<div class="m-2"></div>
									<span class="p-2 float-end"><i style="cursor: pointer;" v-on:click="deletekey(k['key']);show_key = {}" class="fa fa-trash text-danger" title="Delete"></i></span>
								</div>
							</div>
						</div>
					</div>
					<div :class="'key' in show_key?'col-6 p-2':'d-none'" style="height:calc( 100% - 150px );min-height: 500px;overflow:auto; padding:0px 20px; border:1px solid #ccc;">
						<div v-if="'data' in show_key==false" >Loading</div>
						<div v-if="'data' in show_key">
							<div class="redisk_key d-flex justify-content-between">
								<div class="d-flex">
									<div>Size : <span class="fw-bold fs-10">{{ show_key['data']['size'] }} B</span></div>
									<div class="m-2"></div>
									<div>Type : <span class="fw-bold fs-10">{{ show_key['data']['type'] }}</span></div>
									<div class="m-2"></div>
									<div class="d-flex">TTL (Expiry): 
										<template v-if="edit_key_ttl">
											<input type="tel" class="form-control-sm" v-model="show_key['data']['ttl']">
											<div class="m-1"></div>
											<i style="cursor: pointer;" v-on:click="edit_key_ttl = false;save_edit_details()" class="fa fa-floppy-o text-success p-1" title="SAVE"></i>
											<div class="m-1"></div>
											<i style="cursor: pointer;" v-on:click="edit_key_ttl = false;load_key(show_key['key'])" class="fa fa-times p-1" title="close"></i>
										</template>
										<template v-else>
											<div class="d-flex">
												<div class="m-2"></div>
												<span class="fw-bold fs-10">{{ show_key['data']['ttl'] }}</span>
												<div class="m-2"></div>
												<i style="cursor: pointer;" v-on:click="edit_key_ttl = true" class="fa fa-pencil p-1" aria-hidden="true" title="Edit Key"></i>
											</div>
										</template>
									</div>
								</div>
								<div class="d-flex float-end">
									<i style="cursor: pointer;" class="fa fa-solid fa-rotate-right text-secondary" v-on:click="load_key(show_key['key'])" title="Refresh"></i>
									<div class="m-2"></div>
									<i style="cursor: pointer;" v-on:click="edit_configure()" class="fa fa-solid fa-edit text-success" title="Edit"></i>
									<div class="m-2"></div>
									<i style="cursor: pointer;" v-on:click="deletekey(show_key['key'])" class="fa fa-solid fa-trash text-danger" title="Delete"></i>
								</div>
							</div>
							<div>Data: </div>
							<div v-if="'data' in show_key['data']">
								<template v-if="show_key['data']['type']=='string'">
									<textarea name="key_name" id="key_name" class="form-control" v-model="show_key['data']['data']"></textarea>
								</template>
								<template v-else-if="show_key['data']['type']=='list'">
									<table class="table table-bordered w-100">
										<thead>
											<tr>
												<th>Index</th>
												<th>Element</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="d,k in show_key['data']['data']">
												<td>{{ k }}</td>
												<td>{{ d }}</td>
											</tr>
										</tbody>
									</table>
								</template>
								<template v-else-if="show_key['data']['type']=='set'">
									<table class="table table-bordered w-100">
										<thead>
											<tr>
												<th>Member</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="d,k in show_key['data']['data']">
												<td>{{ d }}</td>
											</tr>
										</tbody>
									</table>
								</template>
								<template v-else-if="show_key['data']['type']=='hash'">
									<table class="table table-bordered w-100">
                                        <thead>
                                            <tr>
                                                <th>Field</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="d,k in show_key['data']['data']">
                                                <td>{{ k }}</td>
                                                <td>{{ d }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </template>
								<template v-else>
									<pre >{{ show_key['data']['data'] }}</pre>
								</template>
							</div>
						</div>
					</div>
				</div>
			</template>
		</div>
	</div>
	</div>
	<div class="modal fade" id="edit_modal" tabindex="-1" >
		<div class="modal-dialog model-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Edit Details</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" >
					<table class="table table-bordered table-sm w-100" v-if="'data' in show_key">
						<tr>
							<td>Token Key</td>
							<td><input type="text" v-model="show_key['key']" readonly class="form-control form-control-sm" placeholder="Token Key" ></td>
						</tr>
						<tr>
							<td>Type</td>
							<td>
								<select id="type" name="type" class="form-select" v-model="show_key['data']['type']" readonly>
									<option value="">Please select Type</option>
									<option value="string">String</option>
									<option value="set">Set</option>
									<option value="list">List</option>
									<option value="zset">Sorted Set (ZSet)</option>
									<option value="hash">Hash</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>Expiry</td>
							<td><input type="tel" v-model="show_key['data']['ttl']" class="form-control form-control-sm" placeholder="Expiry Time" ></td>
						</tr>
						<tr>
							<td>Data</td>
							<td>
								<textarea name="data" id="data" v-model="show_key['data']['data']" class="form-control form-control-sm" placeholder="Data to be store"></textarea>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="button" class="btn btn-outline-dark btn-sm" v-on:click="save_edit_details()" value="EDIT RECORD"></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="add_modal" tabindex="-1" >
		<div class="modal-dialog model-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add Details</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" >
					<table class="table table-bordered table-sm w-100" v-if="'data' in add_key">
						<tr>
							<td>Token Key</td>
							<td><input type="text" v-model="add_key['key']" class="form-control form-control-sm" placeholder="Token Key" ></td>
						</tr>
						<tr>
							<td>Type</td>
							<td>
								<select id="type" name="type" class="form-select" required v-model="add_key['data']['type']">
									<option value="">Please select Type</option>
									<option value="string">String</option>
									<option value="set">Set</option>
									<option value="list">List</option>
									<option value="zset">Sorted Set (ZSet)</option>
									<option value="hash">Hash</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>Expiry</td>
							<td><input type="tel" v-model="add_key['data']['ttl']" class="form-control form-control-sm" placeholder="Expiry Time" ></td>
						</tr>
						<tr>
							<td>Data</td>
							<td>
							<textarea name="data" id="data" v-model="add_key['data']['data']" class="form-control form-control-sm" placeholder="Data to be store"></textarea>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="button" class="btn btn-outline-dark btn-sm" v-on:click="add_record_details()" value="ADD RECORD"></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="settings_modal" tabindex="-1" >
		<div class="modal-dialog model-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Settings</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" >
					<div v-if="smsg" class="alert alert-primary" >{{ smsg }}</div>
					<div v-if="serr" class="alert alert-danger" >{{ serr }}</div>
					<table class="table table-bordered table-sm w-100">
						<tr>
							<td>Host</td>
							<td><input v-model="settings['host']" type="text" class="form-control form-control-sm" placeholder="Host" ></td>
						</tr>
						<tr>
							<td>Port</td>
							<td><input v-model="settings['port']" type="number" class="form-control form-control-sm" placeholder="Port" ></td>
						</tr>
						<tr>
							<td>Database</td>
							<td>
								<select v-model="settings['db']" class="form-select">
									<option value="">Select Database</option>
									<option v-for="d in 20" v-bind:value="d" >Database {{d}}</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>Username</td>
							<td><input v-model="settings['username']" type="text" class="form-control form-control-sm" placeholder="Username" ></td>
						</tr>
						<tr>
							<td>Host</td>
							<td><input v-model="settings['password']" type="text" class="form-control form-control-sm" placeholder="Password" ></td>
						</tr>
						<tr>
							<td>TLS</td>
							<td><input v-model="settings['tls']" type="checkbox" ></td>
						</tr>
						<tr>
							<td>Enable</td>
							<td><input v-model="settings['enable']" type="checkbox" ></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="button" class="btn btn-outline-dark btn-sm" value="SAVE" v-on:click="saveit"></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var app = Vue.createApp({
		"data": function(){
			return {
				"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
				"redispath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/redis/",
				"app_id" : "<?=$app['_id'] ?>",
				"settings": <?=json_encode($app['internal_redis']) ?>,
				"smsg": "", "serr":"","msg": "", "err":"","kmsg": "", "kerr":"",
				keyword: "",
				token: "",
				saved: <?=($saved?"true":"false") ?>,
				keys: [], popup: false,
				show_key: {},
				add_key : {},
				key_count: "",
				edit_key_ttl: false,
				data_type : ""
			};
		},
		mounted:function(){
			if( this.saved && this.settings['enable'] ){
				this.load_keys();
			}
		},
		methods: {
			save_edit_details : function(){
				this.smsg = "Saving...";
				this.serr = "";

				axios.post("?",{
					"action": "redis_key_edit",
					"key" : this.show_key['key'],
					"type" : this.show_key['data']['type'],
					"time" : this.show_key['data']['ttl'],
					"data" : this.show_key['data']['data'],
				}).then(response=>{
					this.smsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.popup.hide();
									this.smsg = "Saving";
									this.keyword = "";
									this.load_keys();
								}else{
									this.popup.hide();
									/*this.serr = response.data['error'];*/
									alert(response.data['error']);
									window.location.reload();
								}
							}else{
								this.serr = "Invalid response";
							}
						}else{
							this.serr = "Incorrect response";
						}
					}else{
						this.serr = "http:"+response.status;
					}
				}).catch(error=>{
					this.serr = error.message;
				});
			},
			show_configure: function(){
				this.popup = new bootstrap.Modal(document.getElementById('settings_modal'));
				this.popup.show();
			},
			edit_configure: function(){
				this.popup = new bootstrap.Modal(document.getElementById('edit_modal'));
				this.popup.show();
			},
			add_configure: function(){
				this.add_key = {
					"key" : "",
					"data" : {
						"type" : "",
						"ttl" : "",
						"data" : "",
					}
				}
				this.popup = new bootstrap.Modal(document.getElementById('add_modal'));
				this.popup.show();
			},
			add_record_details: function(){
				if(this.add_key['key'] == "") {
					alert("Please enter key name");
					return;
				}
				if(this.add_key['data']['type'] == "") {
					alert("Please enter key type");
                    return;
				}
				if(this.add_key['data']['ttl'] == "") {
					alert('Please enter time of the Key');
					return;
				}
				if(!/^[0-9]+$/.test(this.add_key['data']['ttl'])) {
					alert('Please enter valid time of the Key');
                    return;
				}
				if(this.add_key['data']['data'] == "") {
					alert("Please Add Data to store");
					return;
				}
				let type = this.add_key['data']['type'];
				let value = this.add_key['data']['data'];

				if(type == "string") {
					if (value.trim() === "") {
						alert("String value cannot be empty.");
						return false;
					}
				}else if(type == "set") {
					let values = value.split(',');
					let valueSet = new Set();
					for (let i = 0; i < values.length; i++) {
						let val = values[i].trim();
						if (val === "") {
							alert("Set values cannot be empty.");
							return false;
						}
						if (valueSet.has(val)) {
							alert("Set values must be unique. Duplicate value found: " + val);
							return false;
						}
						valueSet.add(val);
					}
				}else if(type == "list") {
					let values = value.split(',');
					for (let i = 0; i < values.length; i++) {
						if (values[i].trim() === "") {
							alert("List values cannot be empty.");
							return false;
						}
					}
				}else if(type == "zset") {
					let pairs = value.split(',');
					for (let i = 0; i < pairs.length; i++) {
						let pair = pairs[i].split(':');
						if (pair.length !== 2) {
							alert("Each zset value must be in the format 'score:member'.");
							return false;
						}
						let score = pair[0].trim();
						let member = pair[1].trim();
						if (score === "" || member === "") {
							alert("Each score and member in a zset must be non-empty.");
							return false;
						}
						if (isNaN(score)) {
							alert("Score must be a number.");
							return false;
						}
					}
				}else if(type == "hash") {
					let pairs = value.split(',');
					for (let i = 0; i < pairs.length; i++) {
						let pair = pairs[i].split(':');
						if (pair.length !== 2 || pair[0].trim() === "" || pair[1].trim() === "") {
							alert("Each hash value must be in the format 'field:value' with non-empty field and value.");
							return false;
						}
					}
				}else {
					alert("Type is not allowed");
					return;
				}

				this.show_key = {
					"key" : this.add_key['key'],
					"data" : {
						"type" : this.add_key['data']['type'],
						'ttl' : this.add_key['data']['ttl'],
						'data': this.add_key['data']['data']
					}
				}

				this.save_edit_details();
			},
			load_key: function(k){
				this.show_key = {
					"key": k+'',
					"s": false
				};
				this.kmsg = "Loading...";
				this.kerr = "";
				axios.post("?", {
					"action" 		: "redis_load_key",
					"key": k,
				}).then(response=>{
					this.kmsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.show_key['data'] = response.data['data'];
								}else{
									this.kerr = response.data['error'];
								}
							}else{
								this.kerr = "Invalid response";
							}
						}else{
							this.kerr = "Incorrect response";
						}
					}else{
						this.kerr = "http:"+response.status;
					}
				}).catch(error=>{
					this.kerr = error.message;
				});
			},
			load_keys: function(){
				this.show_key = {};
				var k = "";
				if( this.keyword != "" ){
					k = this.keyword+'';
				}

				this.msg = "Loading...";
				axios.post("?", {
					"action" 		: "redis_load_keys",
					"keyword": k,
					"data_type" : this.data_type
				}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.keys = response.data['keys'];
									this.key_count = response.data['count'];
									/*for(var i=0;i<this.keys.length;i++){
									this.keys.splice(i,1);break;
									}*/
								}else{
									this.err = response.data['error'];
								}
							}else{
								this.err = "Invalid response";
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "http:"+response.status;
					}
				}).catch(error=>{
					this.err = error.message;
				});
			},
			deletekey: function(key) {
				this.smsg = "deleting...";
				this.serr = "";

				axios.post("?",{
					"action": "redis_key_delete",
					"key" : key
				}).then(response=>{
					this.smsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.smsg = "Deleted";
									this.keyword = "";
									this.load_keys();
								}else{
									this.serr = response.data['error'];
								}
							}else{
								this.serr = "Invalid response";
							}
						}else{
							this.serr = "Incorrect response";
						}
					}else{
						this.serr = "http:"+response.status;
					}
				}).catch(error=>{
					this.serr = error.message;
				});
			},
			saveit: function(){
				this.smsg = "Saving...";
				this.serr = "";
				axios.post("?",{
					"action" 		: "redis_save_settings",
					"settings": this.settings,
				}).then(response=>{
					this.smsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.smsg = "Saved";
									this.saved = true;
								}else{
									this.serr = response.data['error'];
								}
							}else{
								this.serr = "Invalid response";
							}
						}else{
							this.serr = "Incorrect response";
						}
					}else{
						this.serr = "http:"+response.status;
					}
				}).catch(error=>{
					this.serr = error.message;
				});
			}
		}
	}).mount("#app");
</script>