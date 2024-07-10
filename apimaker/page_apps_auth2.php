<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >
			<div class="h3 mb-3">Authentication</div>
			<div style="clear:both;"></div>

				<div>
					<ul class="nav nav-tabs">
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true,'active':(tab=='users')}" href="#" v-on:click="open_tab('users')">Users</a>
						</li>
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true,'active':(tab=='keys')}" href="#" v-on:click="open_tab('keys')">Access Keys</a>
						</li>
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true,'active':(tab=='tokens')}" href="#" v-on:click="open_tab('tokens')">Session Tokens</a>
						</li>
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true,'active':(tab=='help')}" href="#" v-on:click="open_tab('help')">Help</a>
						</li>
					</ul>
				</div>

			<div style="height: calc( 100% - 100px - 50px ); overflow: auto;" >
				<div v-if="msg" class="alert alert-primary py-0" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger py-0" >{{ err }}</div>
	
					<div v-if="tab=='keys'" >
						<div v-if="ak_msg" >{{ ak_msg }}</div>
						<div v-if="ak_error" ckass="text-danger" >{{ ak_error }}</div>
						<div><input type="button" class="btn btn-secondary btn-sm" value="Add Key" v-on:click="show_ak_add" ></div>
						<table class="table table-bordered table-sm w-auto">
							<tr>
								<td></td><td></td>
								<td>Key</td>
								<td>Created</td>
								<td>Expires</td>
								<td>Active</td>
							</tr>
							<tr v-for="v,i in ak_keys">
								<td><div class="bsvg" v-on:click="ak_edit_open(i)"><img src="<?=$config_global_apimaker_path ?>images/edit.svg" style="width:20px;" ></div></td>
								<td><div class="bsvg" v-on:click="ak_delete(i)"><img src="<?=$config_global_apimaker_path ?>images/delete.svg" style="width:20px;"></div></td>
								<td nowrap>{{ v['_id'] }}</td>
								<td nowrap>{{ v['updated'].substr(0,16) }}</td>
								<td nowrap>
									<div>{{ v['expire'] }}</div>
									<div v-html="ak_list_get_date( v['expire'] )"></div>
								</td>
								<td>{{ v['active'] }}</td>
							</tr>
						</table>

					</div>
					<div v-if="tab=='tokens'" >
						<div v-if="ak_msg" >{{ ak_msg }}</div>
						<div v-if="ak_error" ckass="text-danger" >{{ ak_error }}</div>
						<table class="table table-bordered table-sm w-auto">
							<tr>
								<td></td><td></td>
								<td>Token</td>
								<td>Created</td>
								<td>Expires</td>
								<td>IP</td>
								<td>UserAgent</td>
							</tr>
							<tr v-for="v,i in tokens">
								<td><div class="bsvg" v-on:click="token_edit_open(i)"><img src="<?=$config_global_apimaker_path ?>images/edit.svg" style="width:20px;" ></div></td>
								<td><div class="bsvg" v-on:click="token_delete(i)"><img src="<?=$config_global_apimaker_path ?>images/delete.svg" style="width:20px;"></div></td>
								<td nowrap>{{ v['_id'] }}</td>
								<td nowrap>{{ v['updated'].substr(0,16) }}</td>
								<td nowrap>
									<div>{{ v['expire'] }}</div>
									<div v-html="token_get_date( v['expire'] )"></div>
								</td>
								<td></td>
								<td></td>
							</tr>
						</table>

					</div>
					<div v-else-if="tab=='users'" >
						<div class="alret alert-danger py-1" >{{user_error}}</div>
						<div><input type="button" class="btn btn-secondary btn-sm" value="Add User" v-on:click="show_user_add" ></div>
						<table class="table table-bordered table-sm w-auto">
							<tr>
								<td></td><td></td>
								<td>Username</td>
								<td>Created</td>
								<td>Expires</td>
								<td>Active</td>
							</tr>
							<tr v-for="v,i in users">
								<td><div class="bsvg" v-on:click="user_edit_open(i)"><img style="width:20px;" src="<?=$config_global_apimaker_path ?>images/edit.svg" ></div></td>
								<td><div class="bsvg" v-on:click="user_delete(i)"><img style="width:20px;" src="<?=$config_global_apimaker_path ?>images/delete.svg" ></div></td>
								<td nowrap>{{ v['username'] }}</td>
								<td nowrap>{{ v['updated'].substr(0,10) }}</td>
								<td nowrap>
									<div>{{ v['pwdexpire_date'].substr(0,10) }}</div>
								</td>
								<td>{{ v['active'] }}</td>
							</tr>
						</table>

					</div>
					<div v-else-if="tab=='help'" >
						<p><b>Help</b></p>
						<p>Environment Variables</p>
						<ul>
							<li>allow_concurrent_login: yes/no</li>
							<li>maximum_failed_attempts: 3</li>
							<li>maximum_keys_per_ip: 3</li>
							<li>maximum_keys_per_authkey: 3</li>
							<li>maximum_api_calls_per_client_per_second: 5</li>
							<li>config_user_table</li>
							<li>config_aws_region</li>
							<li>config_allow_domains</li>
							<li>config_local_aws_key (development)</li>
							<li>config_local_aws_secret (development)</li>
						</ul>

						<p>TableName: db_api_users</p>
						<ul>
							<li>primary key: pk (string), range key: sk (string)</li>
							<li>global key: pk2 (string), range key: sk2 (string)</li>
						</ul>
						<p>Record 1: temporary session tokens</p>
						<ul>
							<li>pk: session</li>
							<li>sk: session_id (encrypted)</li>
							<li>type: admin/client</li>
							<li>ip: ip</li>
							<li>ua: user agent</li>
							<li>created: datetime</li>
							<li>allow_ips: [ "*", "ip1", "ip2" ]</li>
							<li>allow_actions: [ "*", "getItem", "putItem", "deleteItem", "updateItem", "find", "scan" ]</li>
							<li>allow_tables: [ "*", "table1", "table2", etc ]</li>
							<li>expire: time</li>
						</ul>
						<p>Record 2: access keys. </p>
						<ul>
							<li>pk: (admin_keys|user_keys) admin keys have unlimited session time</li>
							<li>sk: random_key (encrypted)</li>
							<li>created: datetime</li>
							<li>lastused: datetime</li>
							<li>active: y/n</li>
							<li>allow_ips: [ "*", "ip1", "ip2" ]</li>
							<li>allow_actions: [ "*", "getItem", "putItem", "deleteItem", "updateItem", "find", "scan" ]</li>
							<li>allow_tables: [ "*", "table1", "table2", etc ]</li>
							<li>expire: time</li>
						</ul>
						<p>Record 3: users with policies</p>
						<ul>
							<li>pk: user</li>
							<li>sk: username</li>
							<li>password: password (hashed)</li>
							<li>created: datetime</li>
							<li>lastLogin: datetime</li>
							<li>lastFailedLogin: datetime</li>
							<li>active: y/n</li>
							<li>allow_ips: [ "*", "ip1", "ip2" ]</li>
							<li>allow_actions: [ "*", "getItem", "putItem", "deleteItem", "updateItem", "find", "scan" ]</li>
							<li>allow_tables: [ "*", "table1", "table2", etc ]</li>
							<li>policy_expire: time</li>
							<li>expire: time</li>
						</ul>
						<p>Record 4: login log</p>
						<ul>
							<li>pk: log</li>
							<li>sk: login</li>
							<li>username: </li>
							<li>authkey: </li>
							<li>datetime: datetime</li>
							<li>expire: config_log_time</li>
						</ul>

						<p><b>API 1: Authentication</b></p>
						<div>POST /dbapi/generate_session_token</div>
						<pre>		{
						"Key": "authentication key", 
						"allow_actions":[], default "*" 
						"allow_tables": [], tablename mandatory
						"allow_ips":[], default client ip
						"expire": in minits. default 5, max 60 minits
					}</pre>
						<div>Works only for keys with create_session permissions</div>
						<div>default ip is client ip. check_session is not allowed in request actions</div>
						<div>this api supposed to be allowed for private client server side communication, who can authenticate and genreate a temporary session token, which can later be used from public clients (browser)</div>
						<div>Response:</div>
						<pre>		{
						"status": "success/fail",
						"Key": "session key",
						"error": ""
					}</pre>

						<p><b>API 2: User Authentication</b></p>
						<div>POST /api/user_auth</div>
						<pre>		{
						"username": "username", 
						"password": "password",
					}</pre>
						<div>genreates a session key for the request client ip</div>
						<pre>		{
						"status": "success/fail",
						"Key": "session key",
						"error": ""
					}</pre>

						<p><b>API 3: DB API</b></p>
						<div>POST /api/(find/scan/getItem/putItem/updateItem/deleteItem)</div>
						<pre>		{
						"Key": "session key", 
						"TableName": "",
						"Condition": {},  for find/scan
						"Filter": {},  for query/scan
						"Key": {},  for getItem/deleteItem
						"Item": {},   for putItem/updateItem
						"ExclusiveStartKey": {},  for query/scan
						"Order": "asc",  asc/dsc for query
						"IndexName": "main",  for query/scan
					}</pre>
						<div>Responses: </div>
						<pre>		{
						"status": "success/fail",
						"error": "",   when error
						"Item": {},  when getItem
						"Items": {},  when query/scan
						"LastEvaluatedKey": {},   when query/scan
						"ConsumedCapacity": "",  info
						"Count": "",  info
						"ScannedCount": "",  info
					}</pre>

					</div>



			</div>
		</div>
	</div>

		<div class="modal fade" id="user_edit_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">User Edit</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">

					<table class="table table-bordered table-sm" >
						<tr>
							<td>Username</td>
							<td><input type="text" class="form-control form-control-sm" v-model="user_record['username']"></td>
						</tr>
						<tr v-if="'_id' in user_record">
							<td>Passowrd</td>
							<td>
								<div><label style="cursor:pointer;"><input  type="checkbox" v-model="user_record['ch_pwd']"> Change Password</label></div>
								<div v-if="user_record['ch_pwd']"><input  type="text" class="form-control form-control-sm" v-model="user_record['password']" placeholder="New Password"></div>
							</td>
						</tr>
						<tr v-else>
							<td>Passowrd</td>
							<td><input type="text" class="form-control form-control-sm" v-model="user_record['password']">
								<div v-if="'_id' in user_record">Fill to update password.</div>
							</td>
						</tr>
						<tr>
							<td>Policies</td>
							<td>
								<div v-for="pv,pi in user_record['policies']" style="padding:10px; border:1px solid #bbb; margin-bottom:10px;" >
									<div v-on:click="user_add_policy_del(pi)" class="btn btn-outline-danger btn-sm py-0" style="float:right;" >Delete Policy</div>
									<p>Policy: <b>{{ pi+1 }}</b> </p>
									<table class="table table-bordered table-sm w-auto" >
										<tr>
											<td>Tables</td>
											<td>
												<div v-for="v,i in user_record['policies'][pi]['allow_tables']" style="display: flex;" >
													<select class="form-select form-select-sm w-auto" v-model="user_record['policies'][pi]['allow_tables'][i]['_id']" v-on:change="user_record_table_select(pi,i)" >
														<option value="*" >*</option>
														<option v-for="tv,ti in config_allow_tables" v-bind:value="tv['_id']" >{{ tv['table'] }}</option>
														<option v-if="user_record['policies'][pi]['allow_tables'][i]['table']!='*'&&user_record['policies'][pi]['allow_tables'][i]['table']!=''" v-bind:value="user_record['policies'][pi]['allow_tables'][i]['_id']" >{{ user_record['policies'][pi]['allow_tables'][i]['table'] }}</option>
													</select>
													<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="user_add_table_delete(pi,i)">
												</div>
												<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="user_add_table_add(pi)"></div>
											</td>
										</tr>
										<tr>
											<td>Actions</td>
											<td>
												<div v-for="v,i in config_allow_actions" ><label><input type="checkbox" v-model="user_record['policies'][pi]['allow_actions']" v-bind:value="v" v-on:click="user_record_action_select(pi)"> {{ v }}</label></div>
											</td>
										</tr>
									</table>
								</div>
								<div><div v-on:click="user_add_policy_add" class="btn btn-outline-dark btn-sm py-0" >Add Policy</div></div>
							</td>
						</tr>
					</table>
					<div v-if="user_add_error" class="text-danger" >{{ user_add_error }}</div>
					<div v-if="user_add_msg" class="text-success" >{{ user_add_msg }}</div>

					<!-- <pre>{{ user_record }}</pre> -->

		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
		        <button type="button" class="btn btn-outline-dark btn-sm"  v-on:click="user_save_record">Save</button>
		      </div>
		    </div>
		  </div>
		</div>




		<div class="modal fade" id="key_edit_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title"><span v-if="'_id' in ak_record" >Key: {{ ak_record['_id'] }}</span><span v-else >Create Key</span></h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">

								<table class="table table-bordered table-sm w-auto" >
									<tr>
										<td>Policies</td>
										<td>
											<div v-for="pv,pi in ak_record['policies']" style="padding:10px; border:1px solid #bbb; margin-top:10px;" >
												<div v-on:click="ak_add_del_policy(pi)" class="btn btn-link btn-sm py-0" style="float:right;" >Delete Policy</div>
												<p>Policy: <b>{{ pi+1 }}</b> </p>
												<table class="table table-bordered table-sm w-auto" >
													<tr>
														<td>Tables</td>
														<td>
															<div v-for="v,i in pv['allow_tables']" style="display:flex;" >
																<select v-model="ak_record['policies'][pi]['allow_tables'][i]['_id']"  v-on:change="ak_record_table_select(pi,i)"  >
																	<option value="*" >*</option>
																	<option v-for="tv,ti in config_allow_tables" v-bind:value="tv['_id']" >{{ tv['table'] }}</option>
																	<option v-if="ak_record['policies'][pi]['allow_tables'][i]!='*'&&ak_record['policies'][pi]['allow_tables'][i]!=''" v-bind:value="ak_record['policies'][pi]['allow_tables'][i]['_id']" >{{ ak_record['policies'][pi]['allow_tables'][i]['table'] }}</option>
																</select>
																<input type="button" value="X" v-on:click="ak_add_table_delete(pi,i)">
															</div>
															<div><input type="button" value="+" v-on:click="ak_add_table_add(pi)"></div>
														</td>
													</tr>
													<tr>
														<td>Actions</td>
														<td>
															<div v-for="v,i in config_allow_actions" ><label><input type="checkbox" v-model="ak_record['policies'][pi]['allow_actions']" v-bind:value="v"  v-on:click="ak_record_action_select(pi)"> {{ v }}</label></div>
														</td>
													</tr>
												</table>
											</div>
											<div><div v-on:click="ak_add_add_policy" class="btn btn-link btn-sm py-0" >Add Policy</div></div>
										</td>
									</tr>
									<tr>
									<tr>
										<td>Ips</td>
										<td>
											<div v-for="v,i in ak_record['allow_ips']" ><input type="text" v-model="ak_record['allow_ips'][i]" placeholder="IPAddress"><input type="button" value="X" v-on:click="ak_add_ip_delete(i)"></div>
											<div><input type="button" value="+" v-on:click="ak_add_ip_add"></div>
										</td>
									</tr>
									<tr>
										<td>Create Sessions</td>
										<td>
											<label><input type="checkbox" v-model="ak_record['allow_sessions']" > Allow creation of sub client sessions</label>
										</td>
									</tr>
									<tr>
										<td>Expires In</td>
										<td>
											<div>Timestamp: <input type="number" v-model="ak_record['expire']" v-on:keydown="ak_record_expire_change" v-on:change="ak_record_expire_change" ></div>
											<div>{{ expire_project }}</div>
											Set to: <select v-model="expire_minits" >
												<option value="5" >5 Minits</option>
												<option value="10" >10 Minits</option>
												<option value="20" >20 Minits</option>
												<option value="60" >1 Hour</option>
												<option value="120" >2 Hours</option>
												<option value="360" >6 Hours</option>
												<option value="720" >12 Hours</option>
												<option value="1440" >1 Day</option>
												<option value="21600" >15 Days</option>
												<option value="43200" >1 Month</option>
												<option value="259200" >6 Months</option>
												<option value="518400" >1 Year</option>
												<option value="-1" >Never</option>
											</select>
											<input type="button" v-on:change="ak_record_timestamp" value="SET" >
										</td>
									</tr>
								</table>
								<div v-if="ak_add_error" class="text-danger" >{{ ak_add_error }}</div>
								<div v-if="ak_add_msg" class="text-success" >{{ ak_add_msg }}</div>

		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
		        <button type="button" class="btn btn-outline-dark btn-sm"  v-on:click="ak_save_record">Save</button>
		      </div>
		    </div>
		  </div>
		</div>


		<div class="modal fade" id="token_edit_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Session Key: {{ token_record['_id'] }}</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">

								<table class="table table-bordered table-sm w-auto" >
									<tr>
										<td>Policies</td>
										<td>
											<pre>{{ token_record['policies'] }}</pre>
										</td>
									</tr>
									<tr>
										<td>Expires In</td>
										<td>
											<div>{{ token_expire_project }}</div>
										</td>
									</tr>
								</table>
								<div v-if="ak_add_error" class="text-danger" >{{ ak_add_error }}</div>
								<div v-if="ak_add_msg" class="text-success" >{{ ak_add_msg }}</div>

		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
		      </div>
		    </div>
		  </div>
		</div>



</div>
<script>
var app = Vue.createApp({
	data(){
		return {
			path: "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			app_id: "<?=$app['_id'] ?>",
			app__: <?=json_encode($app) ?>,
			msg: "", err: "", cmsg: "", cerr: "",
			apis: [],
			show_create_api: false,
			new_api: {"name": "", "des": "" },
			user_edit_modal: false,
			token: "",
			loggedin: false,
			float_msg: "",
			session_error: "",
			username: "",
			password: "",
			login_msg: "",
			login_error: "",
			login_captcha_code: "",
			login_captcha_img: "",
			login_captcha: "",
			session_id: "##session_id##",
			captcha_api_url: "##captcha_api_url##",
			tables: [],
			TableName: "",
			table: {},
			tableindex: -1,
			tab: "users",
			brtype: "scan",
			browse_error: "",
			records: [],
			fields: {},
			fields2: [],
			LastEvaluatedKey: false,
			order: "asc",
			query_index: "main",
			query_schema: {},
			query_schema_fields: {},
			query: {},
			edit_form_div: false,
			delete_ids: [],
			edit_record_str: "",
			edit_record: {},
			save_error: "",
			save_msg: "",
			edit_form_rec_id: -1,
			access_keys: false,
			help: false,
			ak_keys: [],
			tokens: [],
			ak_error: "",
			ak_msg: "",
			ak_add_form: false,
			ak_record: {
				"active": "y",
				"expire": "60",
				"policies": [
					{
						"allow_actions": ["find"],
						"allow_tables": [{"table":"*","_id":"*"}],
					}
				],
				"allow_ips": ["192.168.1.1/32"],
				"allow_sessions": false,
			},
			token_record: {
				"active": "y",
				"expire": "60",
				"policies": [
					{
						"allow_actions": ["find"],
						"allow_tables": [{"table":"*","_id":"*"}],
					}
				],
				"allow_ips": ["192.168.1.1/32"],
				"allow_sessions": false,
			},
			expire_timestamp: 0,
			expire_minits: 5,
			expire_project: "",
			token_expire_project: "",
			ak_add_msg: "",
			ak_add_error: "",
			ak_edit_vi: -1,
			token_edit_vi: -1,
			config_allow_actions: ["*","find", "scan", "insert", "update", "delete"],
			config_allow_tables: [],
			users_tab: false,
			users: [],
			user_msg: "",
			user_error: "",
			user_add_form: false,
			user_add_msg: "",
			user_add_error: "",
			user_edit_vi: -1,
			user_record: {
				"username": "",
				"password": "",
				"active": "y",
				"policies": [{
					"allow_ips": ["192.168.1.1/32"],
					"allow_actions": ["find"],
					"allow_tables": [{"table":"*","_id":"*"}],
					"allow_apis": [{"table":"*","_id":"*"}],
				}],
				"pwdexpire": 2, // in months
				"policy_expire": 5 // minits
			},
		};
	},
	mounted(){
		//this.load_apis();
		this.load_users();
		this.load_tables();
	},
	methods: {
		nchange: function(){
			if( this.new_api['des']=="" ){
				this.new_api['des'] = this.new_api['name']+'';
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
		load_apis(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?", {
				"action":"get_token",
				"event":"getapis."+this.app_id,
				"expire":2
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.load_apis2();
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
		load_apis2(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?",{"action":"get_apis","app_id":this.app_id,"token":this.token}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.apis = response.data['data'];
							}else{
								alert("Token error: " + response.data['error']);
								this.err = "Token Error: " + response.data['error'];
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
		api_show_create_form(){
			this.create_app_modal = new bootstrap.Modal(document.getElementById('create_app_modal'));
			this.create_app_modal.show();
			this.cmsg = ""; this.cerr = "";
		},
		cleanit(v){
			v = v.replace( /\-/g, "DASH" );
			v = v.replace( /\_/g, "UDASH" );
			v = v.replace( /\W/g, "-" );
			v = v.replace( /DASH/g, "-" );v = v.replace( /UDASH/g, "_" );
			v = v.replace( /[\-]{2,5}/g, "-" );
			v = v.replace( /[\_]{2,5}/g, "_" );
			return v;
		},
		createnow(){
			this.cerr = "";
			this.new_api['name'] = this.cleanit(this.new_api['name']);
			if( this.new_api['name'].match(/^[a-z0-9\.\-\_\ ]{3,100}$/i) == null ){
				this.cerr = "Name incorrect. Special chars not allowed. Length minimum 3 max 100";
				return false;
			}
			if( this.new_api['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\ \r\n]{5,200}$/i) == null ){
				this.cerr = "Description incorrect. Special chars not allowed. Length minimum 5 max 200";
				return false;
			}
			this.cmsg = "Creating...";
			axios.post("?", {
				"action": "create_api", 
				"new_api": this.new_api
			}).then(response=>{
				this.cmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.cmsg = "Created";
								this.create_app_modal.hide();
								this.load_apis();
							}else{
								this.cerr = response.data['error'];
							}
						}else{
							this.cerr = "Incorrect response";
						}
					}else{
						this.cerr = "Incorrect response Type";
					}
				}else{
					this.cerr = "Response Error: " . response.status;
				}
			});
		},
		user_record_action_select: function(pi){
			setTimeout(this.user_record_action_select2,200,pi);
		},
		user_record_action_select2: function(pi){
			var f = false;
			for(var j=0;j<this.user_record['policies'][pi]['allow_actions'].length;j++){
				if( this.user_record['policies'][pi]['allow_actions'][j] == "*" ){
					f = true;
				}
			}
			if( f ){
				this.user_record['policies'][pi]['allow_actions'] = ["*"];
			}
		},
		ak_record_action_select: function(pi){
			setTimeout(this.user_record_action_select2,200,pi);
		},
		ak_record_action_select2: function(pi){
			var f = false;
			for(var j=0;j<this.ak_record['policies'][pi]['allow_actions'].length;j++){
				if( this.ak_record['policies'][pi]['allow_actions'][j] == "*" ){
					f = true;
				}
			}
			if( f ){
				this.ak_record['policies'][pi]['allow_actions'] = ["*"];
			}
		},
		user_record_table_select: function(pi,i){
			for(var j=0;j<this.config_allow_tables.length;j++){
				if( this.user_record['policies'][pi]['allow_tables'][i]['_id'] == this.config_allow_tables[j]['_id'] ){
					this.user_record['policies'][pi]['allow_tables'][i]['table'] = this.config_allow_tables[j]['table']+'';
				}
			}
		},
		ak_record_table_select: function(pi,i){
			for(var j=0;j<this.config_allow_tables.length;j++){
				if( this.ak_record['policies'][pi]['allow_tables'][i]['_id'] == this.config_allow_tables[j]['_id'] ){
					this.ak_record['policies'][pi]['allow_tables'][i]['table'] = this.config_allow_tables[j]['table']+'';
				}
			}
		},
		open_tab: function(v){
			this.tab = v;
			if( v== 'keys' ){
				this.load_access_keys();
			}
			if( v== 'tokens' ){
				this.load_tokens();
			}
			if( v== 'users' ){
				this.load_users();
			}
		},
		select_table: function(vi){
			this.tableindex = vi;
			this.records = [];
			this.LastEvaluatedKey = false;
			this.tab = "table";
			this.brtype = "scan";
			this.table = JSON.parse( JSON.stringify( this.tables[ vi ] ) );
			this.query_index = "main";
			this.TableName = this.tables[ vi ]['TableName']+'';
			this.check_query_schema();
		},
		check_brtype: function(){
			this.LastEvaluatedKey = false;
			if( this.brtype == "scan" ){
				this.records = [];
				this.LastEvaluatedKey = false;
				this.load_records();
			}
		},
		check_query_schema: function(){
			this.records = [];
			this.LastEvaluatedKey = false;
			var q = {};
			var f = {};
			if( this.query_index == "main" ){
				q = JSON.parse( JSON.stringify(this.table['KeySchema']) );
				for(var i=0;i<q.length;i++){
					q[i]["cond"] = "=";
					q[i]["value"] = "";
					f[ q[i]['AttributeName'] ] = q[i]['Type'];
				}
				this.query_schema = q;
				this.query_schema_fields = f;
			}else{
				for(var i=0;i<this.table['GlobalSecondaryIndexes'].length;i++){
					if( this.table['GlobalSecondaryIndexes'][i]["IndexName"] == this.query_index ){
						q = JSON.parse( JSON.stringify(this.table['GlobalSecondaryIndexes'][i]['KeySchema']) );
						for(var i=0;i<q.length;i++){
							q[i]["cond"] = "=";
							q[i]["value"] = "";
							f[ q[i]['AttributeName'] ] = q[i]['Type'];
						}
						this.query_schema = q;
						this.query_schema_fields = f;
					}
				}
			}
			if( this.brtype == 'scan' ){
				this.search_now();
			}
		},
		reset: function(){
			this.last_key = false;
		},
		goto_next: function(){
			if( this.LastEvaluatedKey ){
				this.load_records();
			}
		},
		search_now: function(){
			this.LastEvaluatedKey = false;
			this.load_records();
		},
		load_records: function(){
			if( this.brtype == "scan" ){
				axios.post("/admin/scan_records", {
					"TableName":this.TableName,
					"LastEvaluatedKey": this.LastEvaluatedKey,
					"Index": this.query_index,
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( "status" in response.data ){
								if( response.data['status'] == "success" ){
									this.records = response.data['Items'];
									this.fields = response.data['fields'];
									this.LastEvaluatedKey = response.data["LastEvaluatedKey"];
									this.float_msg = "Consumed Units: " + response.data["ConsumedCapacity"];
									console.log( this.float_msg );
									setTimeout(this.hide_float_msg,5000);
									this.find_fields();
								}else{
									this.browse_error = response.data['error'];
								}
							}else{
								this.browse_error = "Incorrect response!";
							}
						}else{
							this.browse_error = "Incorrect response!";
						}
					}else{
						this.browse_error = "Incorrect response!";
					}
				});
			}else{
				if( this.query[ this.query_schema[0]["AttributeName"] ] != "" ){
					axios.post("/admin/query_records", {
						"TableName":this.TableName,
						"LastEvaluatedKey": this.LastEvaluatedKey,
						"Schema": this.query_schema,
						"Index": this.query_index,
						"Order": this.order
					}).then(response=>{
						if( response.status == 200 ){
							if( typeof(response.data) == "object" ){
								if( "status" in response.data ){
									if( response.data['status'] == "success" ){
										this.records = response.data['Items'];
										this.fields = response.data['fields'];
										this.LastEvaluatedKey = response.data["LastEvaluatedKey"];
										this.float_msg = "Consumed Units: " + response.data["ConsumedCapacity"];
										console.log( this.float_msg );
										setTimeout(this.hide_float_msg,5000);
										this.find_fields();
									}else{
										this.browse_error = response.data['error'];
									}
								}else{
									this.browse_error = "Incorrect response!";
								}
							}else{
								this.browse_error = "Incorrect response!";
							}
						}else{
							this.browse_error = "Incorrect response!";
						}
					});
				}
			}
		},
		hide_float_msg: function(){
			this.float_msg = "";
		},
		find_fields: function(){
			var f = [];
			var f2 = {};
			var t = this.tables[ this.tableindex ];
			for(var i=0;i<t['KeySchema'].length;i++){
				f.push( t['KeySchema'][i]['AttributeName'] );
			}
			for(var j in this.fields ){
				if( f.indexOf(j) == -1 ){
					f.push( j );
				}
			}
			this.fields2 = f;
		},
		open_add_form: function( vi ){
			this.edit_form_rec_id = -1;
			this.edit_form_div = true;
			var k = {};
			for(var i=0;i<this.records.length;i++){
				for(var ky in this.records[i] ){
					k[ ky ] = "";
				}
			}
			for(var i=0;i<this.table['KeySchema'].length;i++){
				k[ this.table['KeySchema'][i]['AttributeName'] ]= this.table['KeySchema'][i]['KeyType'] + " " + this.table['KeySchema'][i]['Type'];
			}
			this.edit_record_str = JSON.stringify( k, null, 4 );
		},
		delete_record: function(vi){
			if( confirm("Are you sure?") ){
				var key = {};
				for(var i=0;i<this.table['KeySchema'].length;i++){
					key[ this.table['KeySchema'][i]['AttributeName'] ] = this.records[vi][ this.table['KeySchema'][i]['AttributeName'] ];
				}
				axios.post("/admin/delete_item", {
					"TableName": this.TableName,
					"Key": key,
					"vi": vi,
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( "status" in response.data ){
								if( response.data['status'] == "success" ){
									this.records.splice( Number(response.data['vi']), 1 );
								}else{
									alert("Error: " + response.data['error'] );
								}
							}else{
								alert("Incorrect response!");
							}
						}else{
							alert("Incorrect response!");
						}
					}else{
						alert("Incorrect response!");
					}
				});
			}
		},
		open_edit_form: function( vi ){
			this.edit_form_rec_id = vi;
			this.edit_form_div = true;
			this.edit_record_str = JSON.stringify( this.records[ vi ], null, 4 );
			this.edit_record = JSON.parse( this.edit_record_str );
		},
		save_record: function(){
			this.save_error = "";
			try{
				if( this.edit_record_str == "" ){
					this.save_error = "Incorrect";
					return false;
				}
				var v = JSON.parse( this.edit_record_str );
				var key = {};
				for(var i=0;i<this.table['KeySchema'].length;i++){
					var k = this.table['KeySchema'][i]['AttributeName'];
					var t = this.table['KeySchema'][i]['Type'];
					if( this.table['KeySchema'][i]['KeyType'] == "HASH" ){
						var kt = "Primary Key";
					}else{
						var kt = "Search Key";
					}
					if( k in v == false ){
						this.save_error = kt + ": '" + k +"' is missing";
						return false;
					}else if( t == "N" && typeof(v[k]) != "number" ){
						this.save_error =  kt + ": '" + k + "' should be Numeric";
						return false;
					}
					key[ k ] = v[ k ];
				}
				this.save_msg = "Submitting...";
				axios.post("/admin/save_item", {
					"TableName": this.TableName,
					"Item": v,
					"Key": key
				}).then(response=>{
					this.save_msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( "status" in response.data ){
								if( response.data['status'] == "success" ){
									if( this.edit_form_rec_id > -1 ){
										this.records[ this.edit_form_rec_id ] = JSON.parse( this.edit_record_str );
									}
									if( 'extra' in response.data ){
										this.save_msg = "Success. Updated existing record!";
									}else{
										this.save_msg = "Success. Created new record";
									}

									setTimeout(this.hide_save_msg, 10000);
								}else{
									this.save_error = response.data['error'];
								}
							}else{
								this.save_error = "Incorrect response!";
							}
						}else{
							this.save_error = "Incorrect response!";
						}
					}else{
						this.save_error = "Incorrect response!";
					}
				});
			}catch(e){
				this.save_error = "JSON Format error!";
			}
		},
		hide_save_msg: function(){
			this.save_msg = "";
		},
		load_access_keys: function(){
			this.ak_error = "";
			this.ak_msg = "Loading...";
			axios.post("?",{"action": "load_access_keys"}).then(response=>{
				this.ak_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( "status" in response.data ){
							if( response.data['status'] == "success" ){
								this.ak_keys = response.data['data'];
							}else{
								this.ak_error = response.data['error'];
							}
						}else{
							this.ak_error = "Incorrect response!";
						}
					}else{
						this.ak_error = "Incorrect response!";
					}
				}else{
					this.ak_error = "Incorrect response!";
				}
			});
		},
		load_tokens: function(){
			this.ak_error = "";
			this.ak_msg = "Loading...";
			axios.post( "?", {"action": "load_tokens"} ).then(response=>{
				this.ak_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( "status" in response.data ){
							if( response.data['status'] == "success" ){
								this.tokens = response.data['data'];
							}else{
								this.ak_error = response.data['error'];
							}
						}else{
							this.ak_error = "Incorrect response!";
						}
					}else{
						this.ak_error = "Incorrect response!";
					}
				}else{
					this.ak_error = "Incorrect response!";
				}
			});
		},
		show_ak_add: function(){
			this.ak_edit_modal = new bootstrap.Modal( document.getElementById('key_edit_modal') );
			this.ak_edit_modal.show();
			this.cmsg = ""; this.cerr = "";
			var t = parseInt((new Date()).getTime()/1000)+(60*5);
			this.ak_record = {
				"active": "y",
				"expire": t,
				"policies": [
					{
						"allow_actions": ["find"],
						"allow_tables": [{"table":"*","_id":"*"}],
					}
				],
				"allow_ips": ["192.168.1.1/32"],
				"allow_sessions": false,
			};
			this.expire_project = new Date(t*1000).toString();
			this.expire_minits = 5;
		},
		close_ak_add: function(){
			this.ak_edit_modal.hide();
		},
		ak_save_record: function(){
			this.ak_add_error = "";
			this.ak_add_msg = "";
			for(var pi=0;pi<this.ak_record['policies'].length;pi++){
				for(var i=0;i<this.ak_record['policies'][pi]['allow_actions'].length;i++){
					if( this.ak_record['policies'][pi]['allow_actions'][i] == "*" ){
						if( this.ak_record['policies'][pi]['allow_actions'].length > 1 ){
							this.ak_add_error= "Policy "+(pi+1)+": When all Actions allowed, single rule is required!";return false;
						}
					}else if( this.config_allow_actions.indexOf( this.ak_record['policies'][pi]['allow_actions'][i] ) == -1 ){
						this.ak_add_error= "Policy "+(pi+1)+": Action `"+this.ak_record['policies'][pi]['allow_actions'][i]+"`not found";return false;
					}
				}
				for(var i=0;i<this.ak_record['policies'][pi]['allow_tables'].length;i++){
					if( this.ak_record['policies'][pi]['allow_tables'][i]['_id'] == "*" ){
						if( this.ak_record['policies'][pi]['allow_tables'].length > 1 ){
							this.ak_add_error= "Policy "+(pi+1)+": When all Tables allowed, single rule is required!";return false;
						}
					}else if( this.ak_record['policies'][pi]['allow_tables'][i]['_id'].match( /^(.*?)\:[a-f0-9]{24}$/ ) == null ){
						this.ak_add_error= "Policy "+(pi+1)+": TableName incorrect";return false;
					}else{
						var f = false;
						for(var j=0;j<this.config_allow_tables.length;j++){
							if( this.config_allow_tables[j]['_id'] == this.ak_record['policies'][pi]['allow_tables'][i]['_id'] ){
								f = true;
							}
						}
						if( f == false ){
							this.ak_add_error= "Policy "+(pi+1)+": TableName `"+this.ak_record['policies'][pi]['allow_tables'][i]['table']+"` not found";return false;
						}
					}
				}
			}
			for(var i=0;i<this.ak_record['allow_ips'].length;i++){
				if( this.ak_record['allow_ips'][i] == "*" ){
					if( this.ak_record['allow_ips'].length > 1 ){
						this.ak_add_error= "When all IPs allowed, single rule is required!";return false;
					}
				}else if( this.ak_record['allow_ips'][i].match( /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/ ) == null ){
					this.ak_add_error= "IP address should be #.#.#.#/(32/24/16)";return false;
				}else{
					var m = this.ak_record['allow_ips'][i].match( /^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\/(32|24|16)$/ );
					if( Number(m[1]) < 1 || Number(m[1]) > 255 ){
						this.ak_add_error= "Ip address should be #.#.#.#/(32/24/16). digit should be between 1-255";return false;
					}else if( Number(m[2]) < 1 || Number(m[2]) > 255 ){
						this.ak_add_error= "Ip address should be #.#.#.#/(32/24/16). digit should be between 1-255";return false;
					}else if( Number(m[3]) < 0 || Number(m[3]) > 255 ){
						this.ak_add_error= "Ip address should be #.#.#.#/(32/24/16). digit should be between 1-255";return false;
					}else if( Number(m[4]) < 0 || Number(m[4]) > 255 ){
						this.ak_add_error= "Ip address should be #.#.#.#/(32/24/16). digit should be between 1-255";return false;
					}
				}
			}
			if( typeof(this.ak_record['expire']) != "number" ){
				this.ak_add_error= "Expire timestamp should be number";return false;
			}else{
				var t = Number( this.ak_record['expire'] )*1000;
				t2 = new Date(t).getTime();
				t1 = new Date().getTime();
				if( t2 < t1 ){
					this.ak_add_error= "Expire timestamp should be future time";return false;
				}
			}
			this.ak_add_msg = "Saving...";
			axios.post("?", {
				"action": "save_key",
				"key": this.ak_record,
				"expire_minits": this.expire_minits
			}).then(response=>{
				this.ak_add_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( "status" in response.data ){
							if( response.data['status'] == "success" ){
								this.load_access_keys();
								if( '_id' in this.ak_record == false ){
									this.ak_add_msg = "Created new key: " + response.data['key']['_id'];
								}else{
									this.ak_add_msg = "Updated key";
								}
								this.ak_record = response.data['key'];
							}else{
								this.ak_add_error = response.data['error'];
							}
						}else{
							this.ak_add_error = "Incorrect response!";
						}
					}else{
						this.ak_add_error = "Incorrect response!";
					}
				}else{
					this.ak_add_error = "Incorrect response!";
				}
			});
		},
		ak_add_del_policy: function(vi){
			if( this.ak_record['policies'].length > 1 ){
				this.ak_record['policies'].splice(vi,1);
			}else{
				alert("Need at least one policy!");
			}
		},
		ak_add_add_policy: function(){
			if( this.ak_record['policies'].length < 5 ){
				this.ak_record['policies'].push({
					"allow_ips": ["192.168.1.1/32"],
					"allow_actions": ["find"],
					"allow_tables": ["*"],
				});
			}else{
				alert("Too many policies!");
			}
		},
		ak_add_ip_add: function(){ 
			if( this.ak_record['allow_ips'].length < 20 ){
				this.ak_record['allow_ips'].push("192.168.1.1");
			}else{
				alert("Too many ips");
			}
		},
		ak_add_ip_delete: function(vi){ 
			if( this.ak_record['allow_ips'].length > 1 ){
				this.ak_record['allow_ips'].splice(vi,1);
			}else{
				alert("Need at least one ip condition. but it can be *")
			}
		},
		ak_add_table_add: function(pi){
			if( this.ak_record['policies'][pi]['allow_tables'].length < 20 ){
				this.ak_record['policies'][pi]['allow_tables'].push({"table":"*","_id":"*"}); 
			}else{
				alert("Too many Tables");
			}
		},
		ak_add_table_delete: function(pi,vi){
			if( this.ak_record['policies'][pi]['allow_tables'].length > 1 ){
				this.ak_record['policies'][pi]['allow_tables'].splice(vi,1);
			}else{
				alert("Need at least one table. but it can be *")
			}
		},
		ak_record_timestamp: function(){
			var t = parseInt((new Date().getTime())/1000);
			t = t + (Number( this.expire_minits )*60);
			this.ak_record[ 'expire' ] = t;
			var d = new Date(t*1000);
			this.expire_project = d.toString();
		},
		token_record_timestamp: function(){
			var t = parseInt((new Date().getTime())/1000);
			t = t + (Number( this.expire_minits )*60);
			this.token_record[ 'expire' ] = t;
			var d = new Date(t*1000);
			this.token_expire_project = d.toString();
		},
		ak_record_expire_change: function(){
			setTimeout(this.ak_record_expire_change2,200);
		},
		ak_record_expire_change2: function(){
			var t = (Number(this.ak_record['expire'])*1000);
			var d = new Date(t);
			this.expire_project = d.toString();
		},
		token_record_expire_change: function(){
			setTimeout(this.token_record_expire_change2,200);
		},
		token_record_expire_change2: function(){
			var t = (Number(this.token_record['expire'])*1000);
			var d = new Date(t);
			this.token_expire_project = d.toString();
		},
		hide_ak_add_msg: function(){
			this.ak_add_msg = "";
		},
		ak_edit_open: function(vi){
			this.ak_edit_vi = vi;
			this.ak_record = JSON.parse( JSON.stringify(this.ak_keys[vi]) );
			this.expire_minits = "";
			this.ak_record_expire_change2();
			this.ak_edit_modal = new bootstrap.Modal( document.getElementById('key_edit_modal') );
			this.ak_edit_modal.show();
		},
		token_edit_open: function(vi){
			this.token_edit_vi = vi;
			this.token_record = JSON.parse( JSON.stringify(this.ak_keys[vi]) );
			this.expire_minits = "";
			this.token_record_expire_change2();
			this.token_edit_modal = new bootstrap.Modal( document.getElementById('token_edit_modal') );
			this.token_edit_modal.show();
		},
		ak_list_get_date: function(t){
			var t = Number(t)*1000;
			var d = new Date(t);
			var t2 = (new Date()).getTime();
			var dd = d.toISOString().substr(0,16).replace("T", " ");
			if( t < t2 ){
				dd = dd + "<BR><span class='text-danger' >Expired</span>";
			}
			return dd;
		},
		//----------------------------------------------------
		open_user: function(){
			this.access_keys = true;
			this.load_access_keys();
		},
		load_users: function(){
			this.user_error = "";
			this.user_msg = "Loading...";
			axios.post("?",{"action": "auth_load_users"}).then(response=>{
				this.user_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( "status" in response.data ){
							if( response.data['status'] == "success" ){
								this.users = response.data['data'];
							}else{
								this.user_error = response.data['error'];
							}
						}else{
							this.user_error = "Incorrect response!";
						}
					}else{
						this.user_error = "Incorrect response!";
					}
				}else{
					this.user_error = "Incorrect response!";
				}
			});
		},
		user_delete: function( vi ){
			if( confirm("Are you sure to delete this user account?" ) ){
				this.user_error = "";
				this.user_msg = "Deleting...";
				axios.post("?", {"action":"auth_user_delete", "user_id": this.users[ vi ]['_id'] } ).then(response=>{
					this.user_msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( "status" in response.data ){
								if( response.data['status'] == "success" ){
									this.load_users();
								}else{
									this.user_error = response.data['error'];
								}
							}else{
								this.user_error = "Incorrect response!";
							}
						}else{
							this.user_error = "Incorrect response!";
						}
					}else{
						this.user_error = "Incorrect response!";
					}
				});
			}
		},
		ak_delete: function( vi ){
			if( confirm("Are you sure to delete this Access Key?" ) ){
				this.user_error = "";
				this.user_msg = "Deleting...";
				axios.post( "?", {
					"action":"auth_access_key_delete", 
					"access_key_id": this.ak_keys[ vi ]['_id']
				}).then(response=>{
					this.user_msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( "status" in response.data ){
								if( response.data['status'] == "success" ){
									this.load_access_keys();
								}else{
									this.user_error = response.data['error'];
								}
							}else{
								this.user_error = "Incorrect response!";
							}
						}else{
							this.user_error = "Incorrect response!";
						}
					}else{
						this.user_error = "Incorrect response!";
					}
				});
			}
		},
		show_user_add: function(){
			this.user_record = {
				"username": "",
				"password": "",
				"active": "y",
				"policies": [{
					"allow_actions": ["*"],
					"allow_tables": [{"table":"*", "_id":"*"}],
				}],
				"policy_expire": 10,
				"pwdexpire": 2,
			};
			this.user_edit_modal = new bootstrap.Modal( document.getElementById('user_edit_modal') );
			this.user_edit_modal.show();
			this.cmsg = ""; this.cerr = "";
		},
		user_add_policy_add: function(){
			if( this.user_record['policies'].length < 5 ){
				this.user_record['policies'].push({
					"allow_actions": ["find"],
					"allow_tables": [{"table":"*", "_id":"*"}],
				});
			}else{
				alert("Too many policies!");
			}
		},
		user_add_policy_del: function(pi){
			if( this.user_record['policies'].length > 1 ){
				this.user_record['policies'].splice(pi,1);
			}else{
				alert("Need at least one policy!");
			}
		},
		user_save_record: function(){
			this.user_add_error = "";
			this.user_add_msg = "";
			if( this.user_record['username'].match(/^[a-z][a-z0-9\-]{2,50}$/i) == null ){
				this.user_add_error= "Username should be [a-z][a-z0-9\-]{2,50}";return false;
			}
			if( '_id' in this.user_record == false ){
				if( this.user_record['password'].length < 8 ){
					this.user_add_error= "Password should be minimum 8 characters";return false;
				}
			}else if( this.user_record['ch_pwd'] ){
				if( this.user_record['password'].length < 8 ){
					this.user_add_error= "Password should be minimum 8 characters";return false;
				}
			}
			for(var pi=0;pi<this.user_record['policies'].length;pi++){
				for(var i=0;i<this.user_record['policies'][pi]['allow_actions'].length;i++){
					if( this.user_record['policies'][pi]['allow_actions'][i] == "*" ){
						if( this.user_record['policies'][pi]['allow_actions'].length > 1 ){
							this.user_add_error= "Policy "+(pi+1)+": When all Actions allowed, single rule is required!";return false;
						}
					}else if( this.config_allow_actions.indexOf( this.user_record['policies'][pi]['allow_actions'][i] ) == -1 ){
						this.user_add_error= "Policy "+(pi+1)+": Action `"+this.user_record['policies'][pi]['allow_actions'][i]+"`not found";return false;
					}
				}
				for(var i=0;i<this.user_record['policies'][pi]['allow_tables'].length;i++){
					if( this.user_record['policies'][pi]['allow_tables'][i]['_id'] == "*" ){
						if( this.user_record['policies'][pi]['allow_tables'].length > 1 ){
							this.user_add_error= "Policy "+(pi+1)+": When all Tables allowed, single rule is required!";return false;
						}
					}else if( this.user_record['policies'][pi]['allow_tables'][i]['_id'].match( /^(.*?)\:[a-f0-9]{24}$/ ) == null ){
						this.user_add_error= "Policy "+(pi+1)+": Incorrect table id";return false;
					}else{
						var f = false;
						for(var j=0;j<this.config_allow_tables.length;j++){
							if( this.config_allow_tables[j]['_id'] == this.user_record['policies'][pi]['allow_tables'][i]['_id'] ){
								f = true;
							}
						}
						if( f == false ){
							this.user_add_error= "Policy "+(pi+1)+": TableName `"+this.user_record['policies'][pi]['allow_tables'][i]['table']+"` not found";return false;
						}
					}
				}
			}
			this.user_add_msg = "Saving...";
			axios.post("?", {"action":"save_user", "user": this.user_record}).then(response=>{
				this.user_add_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( "status" in response.data ){
							if( response.data['status'] == "success" ){
								if( '_id' in this.user_record == false ){
									this.user_edit_close();
								}
								this.load_users();
								this.user_add_msg = "Success!";
								setTimeout(this.hide_user_add_msg, 10000);
							}else{
								this.user_add_error = response.data['error'];
							}
						}else{
							this.user_add_error = "Incorrect response!";
						}
					}else{
						this.user_add_error = "Incorrect response!";
					}
				}else{
					this.user_add_error = "Incorrect response!";
				}
			});
		},
		user_add_table_add: function(pi){
			if( this.user_record['policies'][pi]['allow_tables'].length < 20 ){
				this.user_record['policies'][pi]['allow_tables'].push({"table":"*", "_id":"*"}); 
			}else{
				alert("Too many Tables");
			}
		},
		user_add_table_delete: function(pi,vi){
			if( this.user_record['policies'][pi]['allow_tables'].length > 1 ){
				this.user_record['policies'][pi]['allow_tables'].splice(vi,1);
			}else{
				alert("Need at least one table. but it can be *")
			}
		},
		hide_user_add_msg: function(){
			this.user_add_msg = "";
		},
		user_edit_open: function(vi){
			this.user_edit_vi = vi;
			this.user_record = JSON.parse( JSON.stringify(this.users[vi]) );
			this.user_edit_modal = new bootstrap.Modal( document.getElementById('user_edit_modal') );
			this.user_edit_modal.show();
			this.cmsg = ""; this.cerr = "";
		},
		user_edit_close: function(){
			this.user_edit_modal.hide();
		},

		load_tables: function(){
			axios.post("?", {"action":"auth_load_tables"}).then(response=>{
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( "status" in response.data ){
							if( response.data['status'] == "success" ){
								this.config_allow_tables = response.data['tables'];
								console.log( response.data );
							}else{
								this.session_error = response.data['error'];
							}
						}else{
							this.session_error = "Incorrect response!";
						}
					}else{
						this.session_error = "Incorrect response!";
					}
				}else{
					this.session_error = "Incorrect response!";
				}
			})
		},


	}
}).mount("#app");
</script>