<style>
	.redisk{ padding:0px 5px; border-bottom:1px solid #ccc; cursor:pointer; }
	.redisk:hover{ background-color:#f8f8f8; }
	.redisk_key{ padding:0px 5px; border-bottom:1px solid #ccc;}
	.fs-10 { width: 70px; }
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

			<!-- filter search and add button element start -->
			<div class="row mt-2" v-if="saved&&settings['enable']">
				<div class="col-6">
					<input type="text" class="form-control form-control-sm w-100" v-model="keyword" placeholder="Filter by Key Name or Pattern">
				</div>
				<div class="col-3">
					<div class="d-flex">
						<input type="button" class="btn-sm btn btn-outline-dark" value="Search" v-on:click="load_keys()">
						<input type="button" class="mx-2 btn btn-sm btn-outline-dark" value="Add Key" v-on:click="show_key = {};expire_minits = '';add_edit_configure()">
					</div>
				</div>
				<div class="col-3">
				</div>
			</div>
			<!-- filter search and add button element start -->
			<div class="row mt-3 mx-1 table-responsive" style="overflow: auto;height: auto;max-height: 500px;">
				<div v-if="msg" class="alert alert-primary col-12" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger col-12" >{{ err }}</div>
				<table v-if="saved==false||settings['enable']==false" class="table table-bordered w-100">
					<thead>
						<tr>
							<th><p>Key Value store is not enabled</p></th>
							<th><div class="btn btn-outline-dark btn-sm" v-on:click="show_configure()">Configure Redis</div></th>
						</tr>
					</thead>
				</table>
				<table v-else class="table table-bordered w-100">
					<thead>
						<tr>
							<td width="50%">
								<div class="d-flex justify-content-between">
									<div>Key</div>
									<div class="d-flex justify-content-between">
										<div>Records Count : {{ key_count }}</div>
										<div class="m-2"></div>
										<div class="m-auto p-1">
											<i style="cursor: pointer;" class="fa fa-solid fa-rotate-right text-dark" v-on:click="load_keys()" title="Refresh"></i>
										</div>
									</div>
								</div>
							</td>
							<td v-if="'key' in show_key && show_key['key'] != ''" width="50%">
								<div class="d-flex justify-content-between">
									<div class="d-flex">
										<div v-if="'key' in show_key">{{ show_key['key'] }}</div>
										<div class="m-2"></div>
										<button class="btn btn-sm btn-outline-dark fs-10" v-if="'data' in show_key && show_key['data']['type'] != ''">{{show_key['data']['type']}}</button>
									</div>
									<div class="">
										<i style="cursor: pointer;" v-on:click="show_key = []" class="fa fa-times" title="close"></i>
									</div>
								</div>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 0px">
								<table class="table table-bordered w-100 m-auto">
									<tr v-for="k in keys" v-on:click="load_key(k['key'])" style="cursor:pointer;">
										<td width="80%">{{ k['key'] }}</td>
										<td width="5%"><button class="btn btn-sm btn-outline-info fs-10">{{k['time']}}</button></td>
										<td width="5%"><button class="btn btn-sm btn-outline-success fs-10">{{k['size']}} B</button></td>
										<td width="5%"><button class="btn btn-sm btn-outline-dark fs-10">{{ k['type'] }}</button></td>
										<td width="5%"><span class="p-2 float-end"><i v-on:click="show_key = {};deletekey(k['key']);" class="fa fa-trash text-danger" title="Delete"></i></span></td>
									</tr>
								</table>
							</td>
							<td v-if="'key' in show_key && show_key['key'] != '' && 'data' in show_key">
								<table class="table table-borderless w-100">
									<tr>
										<td>
											<div class="d-flex justify-content-between">
												<div class="d-flex">
													<div>Size : <span class="fs-10">{{ show_key['data']['size'] }} B</span></div>
													<div class="m-2"></div>
													<div>Type : <span class="fs-10">{{ show_key['data']['type'] }}</span></div>
													<div class="m-2"></div>
													<div class="d-flex">TTL (Expiry): 
														<template v-if="edit_key_ttl">
															<input type="tel" class="form-control-sm" v-model="show_key['data']['ttl']" v-on:keypress="handleKeyPress($event)">
															<div class="m-1"></div>
															<i style="cursor: pointer;" v-on:click="edit_key_ttl = false;save_details()" class="fa fa-floppy-o text-success p-1" title="SAVE"></i>
															<div class="m-1"></div>
															<i style="cursor: pointer;" v-on:click="edit_key_ttl = false;load_key(show_key['key'])" class="fa fa-times p-1" title="close"></i>
														</template>
														<template v-else>
															<div class="d-flex">
																<div class="m-2"></div>
																<span class="fs-10">{{ show_key['data']['ttl'] }}</span>
																<div class="m-2"></div>
																<i style="cursor: pointer;" v-on:click="edit_key_ttl = true" class="fa fa-pencil p-1" aria-hidden="true" title="Edit Key"></i>
															</div>
														</template>
													</div>
												</div>
												<div class="d-flex float-end">
													<i style="cursor: pointer;" class="fa fa-solid fa-rotate-right text-secondary" v-on:click="load_key(show_key['key'])" title="Refresh"></i>
													<div class="m-2"></div>
													<i style="cursor: pointer;" v-on:click="add_edit_configure()" class="fa fa-solid fa-edit text-success" title="Edit"></i>
													<div class="m-2"></div>
													<i style="cursor: pointer;" v-on:click="deletekey(show_key['key'])" class="fa fa-solid fa-trash text-danger" title="Delete"></i>
												</div>
											</div>
											<hr>
										</td>
									</tr>
									<tr>
										<td>Data:</td>
									</tr>
									<tr>
										<td>
											<template v-if="show_key['data']['type']=='string'">
												<textarea name="key_name" id="key_name" class="form-control" v-model="show_key['data']['data']"></textarea>
												<div class="m-2"></div>
												<button class="btn btn-sm btn-outline-dark float-end" v-on:click="save_details()">Add</button>
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
															<td>
																<div class="d-flex justify-content-between">
																	<input type="text" class="form-control form-control-sm w-100 mx-2" name="data" v-model="show_key['data']['data'][k]">
																	<button class="btn btn-sm btn-outline-danger" v-on:click="remove_inline_field(k)">X</button>
																</div>
															</td>
														</tr>
													</tbody>
													<tfoot>
														<tr>
															<td colspan="2">
																<button class="btn btn-sm btn-outline-dark float-end mx-2" v-on:click="save_details()">Add</button>
																<button class="btn btn-sm btn-outline-primary float-end" v-on:click="show_key['data']['data'][Object.keys(show_key['data']['data']).length] = '';">+</button>
															</td>
														</tr>
													</tfoot>
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
															<td>
																<div class="d-flex justify-content-between">
																	<input type="text" name="set" class="form-control form-control-sm mx-2" v-model="show_key['data']['data'][k]">
																	<button class="btn btn-sm btn-outline-danger" v-on:click="remove_inline_field(k)">X</button>
																</div>
															</td>
														</tr>
													</tbody>
													<tfoot>
														<tr>
															<td colspan="2">
																<button class="btn btn-sm btn-outline-dark float-end" v-on:click="save_details()">Add</button>
																<button class="btn btn-sm btn-outline-primary float-end mx-2" v-on:click="show_key['data']['data'][Object.keys(show_key['data']['data']).length] = '';">+</button>
															</td>
														</tr>
													</tfoot>
												</table>
											</template>
											<template v-else-if="show_key['data']['type']=='hash' || show_key['data']['type']=='zset'">
												<table class="table table-bordered w-100">
													<thead>
														<tr>
															<th>Field</th>
															<th>Value</th>
															<th></th>
														</tr>
													</thead>
													<tbody>
														<tr v-for="d,k in show_key['data']['data']">
															<td>
																<input type="text" name="hash" class="form-control form-control-sm w-100" v-model="show_key['data']['data'][k]['key']">
															</td>
															<td>
																<input type="text" name="hash" class="form-control form-control-sm w-100" v-model="show_key['data']['data'][k]['val']">
															</td>
															<td class="text-center">
																<button class="btn btn-sm btn-outline-danger" v-on:click="remove_inline_field(k)">X</button>
															</td>
														</tr>
													</tbody>
													<tfoot>
														<tr>
															<td colspan="3">
																<button class="btn btn-sm btn-outline-dark float-end mx-2" v-on:click="save_details()">Add</button>
																<button class="btn btn-sm btn-outline-primary float-end" v-on:click="show_key['data']['data'].push({'key' :'','val' : ''})">+</button>
															</td>
														</tr>
													</tfoot>
												</table>
											</template>
											<template v-else>
												<pre >{{ show_key['data']['data'] }}</pre>
											</template>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Added or edit model popup start -->
	<div class="modal fade" id="add_edit_modal" tabindex="-1" >
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ 'key' in show_key && show_key['key'] != '' ?'Edit Details':'Add Details' }}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" >
					<table class="table table-bordered table-sm w-100" v-if="'data' in add_edit_key">
						<tr>
							<td>Token Key</td>
							<td><input type="text" v-model="add_edit_key['key']" class="form-control form-control-sm" placeholder="Token Key" ></td>
						</tr>
						<tr>
							<td>Type</td>
							<td>
								<select id="type" name="type" class="form-select form-select-sm" required v-model="add_edit_key['data']['type']" v-on:change="add_change_field_types()">
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
							<td>
								<table>
									<tr>
										<td>
											<input type="tel" v-model="add_edit_key['data']['ttl']" class="form-control form-control-sm" placeholder="Enter Custome Expiry Time" v-on:keypress="handleKeyPress($event)">
										</td>
										<td>
											<select v-model="expire_minits" class="form-select form-select-sm">
												<option value="">Select default time</option>
												<option value="5">5 Minits</option>
												<option value="10">10 Minits</option>
												<option value="20">20 Minits</option>
												<option value="60">1 Hour</option>
												<option value="120">2 Hours</option>
												<option value="360">6 Hours</option>
												<option value="720">12 Hours</option>
												<option value="1440">1 Day</option>
												<option value="21600">15 Days</option>
												<option value="43200">1 Month</option>
												<option value="259200">6 Months</option>
												<option value="518400">1 Year</option>
												<option value="-1">Never</option>
											</select>
										</td>
										<td>
											<input type="button" class="mx-2 btn btn-sm btn-outline-dark" v-on:click="ak_record_timestamp" value="SET" >
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>Data</td>
							<td>
								<template v-if="add_edit_key['data']['type'] == 'string' || add_edit_key['data']['type'] == ''">
									<textarea name="data" id="data" v-model="add_edit_key['data']['data']" class="form-control form-control-sm w-100" placeholder="Data to be store"></textarea>
								</template>
								<template v-else-if="add_edit_key['data']['type'] == 'set' || add_edit_key['data']['type'] == 'list'">
									<table class="table table-sm w-100">
										<tbody v-for="k,i in add_edit_key['data']['data']">
											<tr>
												<td><input type="text" class="form-control form-select-sm" v-model="add_edit_key['data']['data'][i]" placeholder="Enter Value"></td>
												<td><button class="btn btn-sm btn-outline-danger" v-on:click="remove_field_types(i)">X</button></td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<td colspan=3 class="float-end"><button class="btn btn-sm btn-outline-dark" v-on:click="add_edit_key['data']['data'][Object.keys(add_edit_key['data']['data']).length] = '';">+</button></td>
											</tr>
										</tfoot>
									</table>
								</template>
								<template v-else-if="add_edit_key['data']['type'] == 'hash' || add_edit_key['data']['type'] == 'zset'">
									<table class="table table-sm w-100">
										<tbody v-for="k,j in add_edit_key['data']['data']">
											<tr>
												<td><input type="text" class="form-control form-select-sm" v-model="add_edit_key['data']['data'][j]['key']" placeholder="Enter Field"></td>
												<td><input type="text" class="form-control form-select-sm" v-model="add_edit_key['data']['data'][j]['val']" placeholder="Enter Value"></td>
												<td><button class="btn btn-sm btn-outline-danger" v-on:click="remove_field_types(j)">X</button></td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<td colspan=3 class="float-end"><button class="btn btn-sm btn-outline-dark" v-on:click="add_edit_key['data']['data'][Object.keys(add_edit_key['data']['data']).length] = {'key' : '','val' :''};">+</button></td>
											</tr>
										</tfoot>
									</table>
								</template>
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
	<!-- Added or edit model popup end -->

	<!-- Configure model popup start -->
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
	<!-- Configure model popup end -->
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
				add_edit_key : {
					"key" : "",
					"data" : {
						"type" : "",
						'ttl' : "",
						'data': ""
					}
				},
				key_count: "",
				edit_key_ttl: false,
				expire_minits : "",
				expire_project : ""
			};
		},
		mounted:function(){
			if( this.saved && this.settings['enable'] ){
				this.load_keys();
			}
		},
		methods: {
			handleKeyPress :function(evt) {
				evt = evt || window.event;
				var charCode = evt.which || evt.keyCode;
				if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
					evt.preventDefault();
				} else {
					return true;
				}
			},
			ak_record_timestamp: function() {
				if(this.expire_minits == "") {
					alert("Please select defaault time");
					return;
				}
				this.add_edit_key['data']['ttl'] = (this.expire_minits > 1 ? this.expire_minits*60 : this.expire_minits);
			},
			remove_field_types: function(k) {
				if(confirm("Are you sure to delete this Key?") === true) {
					delete this.add_edit_key['data']['data'][k];
				}
			},
			remove_inline_field: function(k) {
				if(confirm("Are you sure to delete this Key?") === true) {
					this.show_key['data']['data'].splice(k,1);
					this.save_details();
				}
			},
			add_edit_configure: function(){
				this.add_edit_key = {
					"key" : "",
					"data" : {
						"type" : "",
						'ttl' : "",
						'data': ""
					}
				}
				if("key" in this.show_key && this.show_key['key'] != "") {
					this.add_edit_key = {
						"key" : this.show_key['key'],
						"data" : {
							"type" : this.show_key['data']['type'],
							'ttl' : this.show_key['data']['ttl'],
							'data': this.show_key['data']['data']
						}
					}
				}

				this.popup = new bootstrap.Modal(document.getElementById('add_edit_modal'));
				this.popup.show();
			},
			add_change_field_types: function() {
				if(this.add_edit_key['data']['type'] == "") {
					alert('Please select data type');
				}else if(this.add_edit_key['data']['type'] == "string") {
					this.add_edit_key['data']['data'] = '';
				}else if(this.add_edit_key['data']['type'] == "zset" || this.add_edit_key['data']['type'] == "hash"){
					this.add_edit_key['data']['data'] = {};
					this.add_edit_key['data']['data'][0] = {"key" : "",'val' : ""};
				}else {
					this.add_edit_key['data']['data'] = {};
					this.add_edit_key['data']['data'][0] = "";
				}
			},
			add_record_details: function(){
				this.show_key = {
					"key" : this.add_edit_key['key'],
					"data" : {
						"type" : this.add_edit_key['data']['type'],
						'ttl' : this.add_edit_key['data']['ttl'],
						'data': this.add_edit_key['data']['data']
					}
				}

				this.save_details();
			},
			save_details : function(){
				if(this.show_key['key'] == "") {
					alert("Please enter key name");
					return;
				}
				if(this.show_key['data']['type'] == "") {
					alert("Please enter key type");
					return;
				}
				if(this.show_key['data']['ttl'] == "") {
					alert('Please enter time of the Key');
					return;
				}
				if(!/^[0-9\-]+$/.test(this.show_key['data']['ttl'])) {
					alert('Please enter valid time of the Key');
					return;
				}
				if(this.show_key['data']['data'] == "") {
					alert("Please Add Data to store");
					return;
				}
				let type = this.show_key['data']['type'];
				let value = this.show_key['data']['data'];

				if(type == "string") {
					if (value.trim() === "") {
						alert("String value cannot be empty.");
						return false;
					}
				}else if(type == "list" || type == "set") {
					for (let [key, val] of Object.entries(value)) {
						if(val.trim() === "") {
							alert("List values cannot be empty.");
							return false;
						}
					}
				}else if(type == "zset" || type == "hash") {
					for (let [i, j] of Object.entries(value)) {
						let key = j['key'];
						let val = j['val'];
						if(key.trim() === "" || val.trim() === "") {
							alert("Each score and member in a zset must be non-empty.");
							return false;
						}
					}
				}else {
					alert("Type is not allowed");
					return;
				}

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
									this.load_keys();
									this.popup.hide();
									this.smsg = "Saving";
									this.keyword = "";
								}else{
									this.popup.hide();
									alert(response.data['error']);
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
			load_key: function(k){
				this.show_key = {
					"key": k+'',
					"s": false
				};
				this.kmsg = "Loading...";
				this.kerr = "";
				axios.post("?", {
					"action" : "redis_load_key",
					"key": k,
				}).then(response=>{
					this.kmsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.show_key['data'] = response.data['data'];
									if(this.show_key['data']['type'] == "hash" || this.show_key['data']['type'] == "zset") {
										let data = this.show_key['data']['data'];
										this.show_key['data']['data'] = [];
										for(const [i,j] of Object.entries(data)) {
											let new_value = {'key' : i,'val' : j};
											this.show_key['data']['data'].push(new_value);
										}
									}
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
					"keyword": k
				}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.keys = response.data['keys'];
									this.key_count = response.data['count'];
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
				if(confirm("Are you sure to delete this Key?") === true) {
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
				}
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