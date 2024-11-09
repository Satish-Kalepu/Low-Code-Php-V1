<style>
	.sbadge{ border:1px solid #aaa; border-radius:3px; padding:0px 5px; margin-right:5px; background-color:#f0f0f0; display:inline-block; }
	div label.act {cursor: pointer;}
	div label.act:hover{ background-color:#f0f0f0; }
</style>
<div id="app" >
	<div class="leftbar" >
		<?php 
		require("page_apps_leftbar.php");
		?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >
			<div class="h3 mb-3">Authentication</div>
			<div style="clear:both;"></div>

				<div>
					<ul class="nav nav-tabs">
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true,'active':(tab=='users')}" href="#" v-on:click="open_tab('users')">User Pools</a>
						</li>
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true,'active':(tab=='roles')}" href="#" v-on:click="open_tab('roles')">Roles</a>
						</li>
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true,'active':(tab=='keys')}" href="#" v-on:click="open_tab('keys')">Access Keys</a>
						</li>
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true,'active':(tab=='tokens')}" href="#" v-on:click="open_tab('tokens')">Session Tokens</a>
						</li>
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true,'active':(tab=='sessions')}" href="#" v-on:click="open_tab('sessions')">Sessions</a>
						</li>
						<li class="nav-item">
							<a v-bind:class="{'nav-link':true,'active':(tab=='log')}" href="#" v-on:click="open_tab('log')">Log</a>
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
						<p>Access Keys can be used as Authorization-Keys, which are best suitable for server side, contains permissions boundary, and can be used to create session keys</p>
						<div v-if="ak_msg" >{{ ak_msg }}</div>
						<div v-if="ak_error" ckass="text-danger" >{{ ak_error }}</div>
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="Add Key" v-on:click="show_ak_add" ></div>
						<table class="table table-bordered table-sm w-auto">
							<tr>
								<td></td><td></td>
								<td>Key</td>
								<td>Updated</td>
								<td>Expires</td>
								<td>Last Used</td>
								<td>Hits</td>
							</tr>
							<tr v-for="v,i in ak_keys">
								<td><div class="bsvg" v-on:click="ak_edit_open(i)"><img src="<?=$config_global_apimaker_path ?>images/edit.svg" style="width:20px;" ></div></td>
								<td><div class="bsvg" v-on:click="ak_delete(i)"><img src="<?=$config_global_apimaker_path ?>images/delete.svg" style="width:20px;"></div></td>
								<td>
									<div style="white-space: nowrap">{{ v['_id'] }}</div>
									<div class="text-secondary">{{ v['des'] }}</div>
								</td>
								<td nowrap>{{ v['updated'].substr(0,16) }}</td>
								<td nowrap>
									<!-- <div>{{ v['expire'] }}</div> -->
									<div v-html="ak_list_get_date( v['expire_utc'] )"></div>
								</td>
								<td nowrap>
									<template v-if="'last_used' in v" ><div v-html="ak_list_last_date( v['last_used'] )"></div></template>
								</td>
								<td>
									<template v-if="'hits' in v" >{{ v['hits'] }}</template>
								</td>
							</tr>
						</table>

					</div>
					<div v-if="tab=='roles'" >
						<div v-if="r_msg" >{{ r_msg }}</div>
						<div v-if="r_error" ckass="text-danger" >{{ r_error }}</div>
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="Add Role" v-on:click="show_role_add" ></div>
						<table class="table table-bordered table-sm w-auto">
							<tr>
								<td></td><td></td>
								<td>Name</td>
								<td>Updated</td>
								<td>Details</td>
							</tr>
							<tr v-for="v,i in roles">
								<td><div class="bsvg" v-on:click="role_edit_open(i)"><img src="<?=$config_global_apimaker_path ?>images/edit.svg" style="width:20px;" ></div></td>
								<td><div class="bsvg" v-on:click="role_delete(i)"><img src="<?=$config_global_apimaker_path ?>images/delete.svg" style="width:20px;"></div></td>
								<td nowrap>
									<div style="white-space:nowrap;">{{ v['_id'] }}</div>
									<div class="text-secondary">{{ v['name'] }}</div>
								</td>
								<td nowrap>{{ v['updated'].substr(0,16) }}</td>
								<td>
									<div v-for="p in v['policies']" style="padding:5px; border:1px solid #ccc; margin-bottom: 5px;" >
										<table class="table table-bordered table-sm w-auto">
										<tr><td>Service</td><td><span class="sbadge" >{{ p['service'] }}</span></td></tr>
										<tr><td>Things</td><td><template v-for="t in p['things']" ><span class="sbadge"  >{{ t['thing'] }}</span> </template></td></tr>
										<tr><td>Actions</td><td><template v-for="a in p['actions']" ><span class="sbadge" >{{ a }}</span> </template></td></tr>
										</table>
									</div>
								</td>
							</tr>
						</table>

					</div>
					<div v-if="tab=='tokens'" >
						<p>Session Tokens are short-lived Access-Keys, which are best suitable for client side authentication. Can be used as CSRF Tokens. Expire timestamp can be specified but max limit is 2 hours</p>
						<div style="min-height:30px;">
							<div v-if="ak_msg" >{{ ak_msg }}</div>
							<div v-if="ak_error" ckass="text-danger" >{{ ak_error }}</div>
						</div>
						<table class="table table-bordered table-sm w-auto">
							<tr>
								<td></td><td></td>
								<td>Token</td>
								<td>Created</td>
								<td>Expires</td>
								<td>Last Used</td>
								<td>Hits</td>
								<td>IP</td>
								<td>UserAgent</td>
							</tr>
							<tr v-for="v,i in tokens">
								<td><div class="bsvg" v-on:click="token_edit_open(i)"><img src="<?=$config_global_apimaker_path ?>images/edit.svg" style="width:20px;" ></div></td>
								<td><div class="bsvg" v-on:click="token_delete(i)"><img src="<?=$config_global_apimaker_path ?>images/delete.svg" style="width:20px;"></div></td>
								<td>
									<div style="white-space:nowrap;">{{ v['_id'] }}</div>
									<div v-if="'des' in v" class="text-secondary">{{ v['des'] }}</div>
								</td>
								<td nowrap>{{ v['updated'].substr(0,16) }}</td>
								<td nowrap>
									<!-- <div>{{ v['expire'] }}</div> -->
									<div v-if="'expire_utc' in v " v-html="ak_list_get_date( v['expire_utc'] )"></div>
									<div v-else>Expire UTC Missing</div>
								</td>
								<td nowrap>
									<template v-if="'last_used' in v" ><div v-html="token_last_date( v['last_used'] )"></div></template>
								</td>
								<td>
									<template v-if="'hits' in v" >{{ v['hits'] }}</template>
								</td>
								<td>{{ v['ips'][0] }}</td>
								<td>
									<div v-if="'ua' in v">{{ v['ua'] }}</div>
									<div v-if="'sess_id' in v" >Session: {{v['sess_id']}}</div>
									<div v-if="'ak_id' in v" >Key: {{v['ak_id']}}</div>
								</td>
							</tr>
						</table>

					</div>
					<div v-if="tab=='sessions'" >
						<p>Sessions are active user logins</p>
						<div style="min-height:30px;">
							<div v-if="sess_msg" >{{ ak_msg }}</div>
							<div v-if="sess_error" ckass="text-danger" >{{ sess_error }}</div>
						</div>
						<table class="table table-bordered table-sm w-auto">
							<tr>
								<td></td>
								<td>Token</td>
								<td>User ID</td>
								<td>Expires</td>
								<td>Last Used</td>
							</tr>
							<tr v-for="v,i in sessions">
								<td><div class="bsvg" v-on:click="token_delete(i)"><img src="<?=$config_global_apimaker_path ?>images/delete.svg" style="width:20px;"></div></td>
								<td>
									<div style="white-space:nowrap;">{{ v['_id'] }}</div>
								</td>
								<td>
									<div style="white-space:nowrap;">{{ v['user_id'] }}</div>
									<div class="text-secondary">{{ v['username'] }}</div>
								</td>
								<td nowrap>
									{{ v['expire'] }}
								</td>
								<td nowrap>
									<template v-if="'last_used' in v" ><div v-html="token_last_date( v['last_used'] )"></div></template>
								</td>
							</tr>
						</table>

					</div>
					<div v-else-if="tab=='users'" >
						<div class="alret alert-danger py-1" >{{user_error}}</div>
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="Add User" v-on:click="show_user_add" ></div>
						<table class="table table-bordered table-sm w-auto">
							<tr>
								<td></td><td></td>
								<td>Username</td>
								<td>Updated</td>
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
							<td><input type="text" spellcheck="false" autocomplete="false" class="form-control form-control-sm" v-model="user_record['username']"></td>
						</tr>
						<tr v-if="'_id' in user_record">
							<td>Passowrd</td>
							<td>
								<div><label style="cursor:pointer;"><input  type="checkbox" v-model="user_record['ch_pwd']"> Change Password</label></div>
								<div v-if="user_record['ch_pwd']"><input  type="text" spellcheck="false" autocomplete="false" class="form-control form-control-sm" v-model="user_record['password']" placeholder="New Password"></div>
							</td>
						</tr>
						<tr v-else>
							<td>Passowrd</td>
							<td><input type="text" spellcheck="false" autocomplete="false" class="form-control form-control-sm" v-model="user_record['password']">
								<div v-if="'_id' in user_record">Fill to update password.</div>
							</td>
						</tr>
						<tr>
							<td>Roles</td>
							<td>
								<div v-for="role,rolei in user_record['roles']" style="display: flex;" >
									<select class="form-select form-select-sm w-auto" v-model="user_record['roles'][rolei]['_id']" v-on:change="user_record_role_select(rolei,role)" >
										<template v-if="roles.length" ><option v-for="tv,ti in roles" v-bind:value="tv['_id']" >{{ tv['name'] }}</option></template>
										<option v-if="role['_id']" v-bind:value="role['_id']" >{{ role['name'] }}</option>
									</select>
									<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="user_record_delete_role(rolei)">
								</div>
								<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="user_record_add_role()"></div>
							</td>
						</tr>
					</table>
					<div v-if="user_add_error" class="text-danger" >{{ user_add_error }}</div>
					<div v-if="user_add_msg" class="text-success" >{{ user_add_msg }}</div>

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
										<td>Description</td>
										<td><input class="form-control form-control-sm" type="text" spellcheck="false" autocomplete="false" v-model="ak_record['des']" placeholder="Purpose/Name"></td>
									</tr>
									<tr>
										<td>Policies</td>
										<td>
											<div v-for="pv,pi in ak_record['policies']" style="padding:10px; border:1px solid #bbb; margin-top:10px;" >
												<div v-on:click="ak_del_policy(pi)" class="btn btn-link btn-sm py-0" style="float:right;" >Delete Policy</div>
												<p>Policy: <b>{{ pi+1 }}</b> </p>
												<table class="table table-bordered table-sm w-auto" >
													<tr>
														<td>Service</td>
														<td>
															<select class="form-select form-select-sm w-auto" v-model="ak_record['policies'][pi]['service']" v-on:change="ak_record_service_select(pi)" >
																<option value="apis" >apis</option>
																<option value="tables" >tables</option>
																<option value="files" >files</option>
																<option value="storage" >storage</option>
																<option value="objects" >objects</option>
															</select>
														</td>
													</tr>
													<tr v-if="ak_record['policies'][pi]['service']">
														<td>{{ ak_record['policies'][pi]['service'] }}</td>
														<td>
															<template v-if="ak_record['policies'][pi]['service']=='apis'" >
																<div v-for="v,i in ak_record['policies'][pi]['things']" style="display: flex;" >
																	<select class="form-select form-select-sm w-auto" v-model="ak_record['policies'][pi]['things'][i]['_id']" v-on:change="ak_record_thing_select(pi,i)" >
																		<option value="*" >*</option>
																		<option v-for="tv,ti in config_allow_apis" v-bind:value="tv['_id']" >{{ tv['thing'] }}</option>
																		<option v-if="ak_record['policies'][pi]['things'][i]['thing']!='*'&&ak_record['policies'][pi]['things'][i]['thing']!=''" v-bind:value="ak_record['policies'][pi]['things'][i]['_id']" >{{ ak_record['policies'][pi]['things'][i]['thing'] }}</option>
																	</select>
																	<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="ak_thing_delete(pi,i)">
																</div>
																<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="ak_thing_add(pi)"></div>
															</template>
															<template v-if="ak_record['policies'][pi]['service']=='tables'" >
																<div v-for="v,i in ak_record['policies'][pi]['things']" style="display: flex;" >
																	<select class="form-select form-select-sm w-auto" v-model="ak_record['policies'][pi]['things'][i]['_id']" v-on:change="ak_record_thing_select(pi,i)" >
																		<option value="*" >*</option>
																		<option v-for="tv,ti in config_allow_tables" v-bind:value="tv['_id']" >{{ tv['thing'] }}</option>
																		<option v-if="ak_record['policies'][pi]['things'][i]['thing']!='*'&&ak_record['policies'][pi]['things'][i]['thing']!=''" v-bind:value="ak_record['policies'][pi]['things'][i]['_id']" >{{ ak_record['policies'][pi]['things'][i]['thing'] }}</option>
																	</select>
																	<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="ak_thing_delete(pi,i)">
																</div>
																<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="ak_thing_add(pi)"></div>
															</template>
															<template v-if="ak_record['policies'][pi]['service']=='storage'" >
																<div v-for="v,i in ak_record['policies'][pi]['things']" style="display: flex;" >
																	<select class="form-select form-select-sm w-auto" v-model="ak_record['policies'][pi]['things'][i]['_id']" v-on:change="ak_record_thing_select(pi,i)" >
																		<option value="*" >*</option>
																		<option v-for="tv,ti in config_allow_storage" v-bind:value="tv['_id']" >{{ tv['thing'] }}</option>
																		<option v-if="ak_record['policies'][pi]['things'][i]['thing']!='*'&&ak_record['policies'][pi]['things'][i]['thing']!=''" v-bind:value="ak_record['policies'][pi]['things'][i]['_id']" >{{ ak_record['policies'][pi]['things'][i]['thing'] }}</option>
																	</select>
																	<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="ak_thing_delete(pi,i)">
																</div>
																<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="ak_thing_add(pi)"></div>
															</template>
															<template v-if="ak_record['policies'][pi]['service']=='files'" >
																<div v-for="v,i in ak_record['policies'][pi]['things']" style="display: flex;" >
																	<select class="form-select form-select-sm w-auto" v-model="ak_record['policies'][pi]['things'][i]['_id']" v-on:change="ak_record_thing_select(pi,i)" >
																		<option value="*" >*</option>
																		<option v-for="tv,ti in config_allow_files" v-bind:value="tv['_id']" >{{ tv['thing'] }}</option>
																		<option v-if="ak_record['policies'][pi]['things'][i]['thing']!='*'&&ak_record['policies'][pi]['things'][i]['thing']!=''" v-bind:value="ak_record['policies'][pi]['things'][i]['_id']" >{{ ak_record['policies'][pi]['things'][i]['thing'] }}</option>
																	</select>
																	<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="ak_thing_delete(pi,i)">
																</div>
																<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="ak_thing_add(pi)"></div>
															</template>
															<template v-if="ak_record['policies'][pi]['service']=='objects'" >
																<div v-for="v,i in ak_record['policies'][pi]['things']" style="display: flex;" >
																	<select class="form-select form-select-sm w-auto" v-model="ak_record['policies'][pi]['things'][i]['_id']" v-on:change="ak_record_thing_select(pi,i)" >
																		<option value="*" >*</option>
																		<option v-for="tv,ti in config_allow_objects" v-bind:value="tv['_id']" >{{ tv['thing'] }}</option>
																		<option v-if="ak_record['policies'][pi]['things'][i]['thing']!='*'&&ak_record['policies'][pi]['things'][i]['thing']!=''" v-bind:value="ak_record['policies'][pi]['things'][i]['_id']" >{{ ak_record['policies'][pi]['things'][i]['thing'] }}</option>
																	</select>
																	<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="ak_thing_delete(pi,i)">
																</div>
																<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="ak_thing_add(pi)"></div>
															</template>
														</td>
													</tr>
													<tr v-if="ak_record['policies'][pi]['service']">
														<td>Actions</td>
														<td>
															<template v-if="ak_record['policies'][pi]['service'] in config_allow_actions" >
															<div v-for="v,i in config_allow_actions[ ak_record['policies'][pi]['service'] ]" ><label class="act"><input type="checkbox" v-model="ak_record['policies'][pi]['actions']" v-bind:value="v"  v-on:click="ak_record_action_select(pi)"> {{ v }}</label></div>
															</template>
														</td>
													</tr>
													<tr v-if="ak_record['policies'][pi]['service']">
														<td>Things</td>
														<td>
															<div v-for="rd,ri in ak_record['policies'][pi]['records']" style="display: flex;column-gap:5px;" >
																<input type="text" class="form-control form-control-sm w-auto" v-model="ak_record['policies'][pi]['records'][ri]"  spellcheck="false" autocomplete="false">
																<input type="button" class="btn btn-outline-danger btn-sm py-0" value="x" v-on:click="ak_record_delete_record(pi,ri)" >
															</div>
															<input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="ak_record_add_record(pi)" >
														</td>
													</tr>
												</table>
											</div>
											<div><div v-on:click="ak_add_policy" class="btn btn-link btn-sm py-0" >Add Policy</div></div>
										</td>
									</tr>
									<tr>
										<td>IPs</td>
										<td>
											<div v-for="v,i in ak_record['ips']" ><input type="text"  spellcheck="false" autocomplete="false" v-model="ak_record['ips'][i]" placeholder="IPAddress"><input type="button" value="X" v-on:click="ak_ip_delete(i)"></div>
											<div><input type="button" value="+" v-on:click="ak_ip_add"></div>
										</td>
									</tr>
									<tr>
										<td>Session Tokens</td>
										<td>
											<div><label style="cursor: pointer;"><input type="checkbox" v-model="ak_record['s']" > Allow creation of Session Tokens</label></div>
											<div v-if="ak_record['s']" >
												Maximum Active Sessions: <input type="number" v-model="ak_record['max_s']" style="width:70px;" > -1 for unlimited
											</div>
										</td>
									</tr>
									<tr>
										<td>Expires In</td>
										<td>
											<div>Timestamp: <input type="text" spellcheck="false" autocomplete="false" v-model="ak_record['expire']" v-on:keyup="ak_record_timestamp_edit" ></div>
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
											<input type="button" v-on:click="ak_record_timestamp_change" value="SET" >
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


		<div class="modal fade" id="role_edit_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title"><span v-if="'_id' in role_record" >Role: {{ role_record['name'] }}</span><span v-else >Create Role</span></h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">

								<table class="table table-bordered table-sm w-auto" >
									<tr>
										<td>Name</td>
										<td>
											<input type="text" spellcheck="false" autocomplete="false" class="form-control form-control-sm" v-model="role_record['name']" placeholder="role name">
										</td>
									</tr>
									<tr>
										<td>Policies</td>
										<td>
											<div v-for="pv,pi in role_record['policies']" style="padding:10px; border:1px solid #bbb; margin-top:10px;" >
												<div v-on:click="role_del_policy(pi)" class="btn btn-link btn-sm py-0" style="float:right;" >Delete Policy</div>
												<p>Policy: <b>{{ pi+1 }}</b> </p>
												<table class="table table-bordered table-sm w-auto" >
													<tr>
														<td>Service</td>
														<td>
															<select class="form-select form-select-sm w-auto" v-model="role_record['policies'][pi]['service']" v-on:change="role_record_service_select(pi)" >
																<option value="apis" >apis</option>
																<option value="tables" >tables</option>
																<option value="files" >files</option>
																<option value="storage" >storage</option>
																<option value="objects" >objects</option>
															</select>
														</td>
													</tr>
													<tr v-if="role_record['policies'][pi]['service']">
														<td>{{ role_record['policies'][pi]['service'] }}</td>
														<td>
															<template v-if="role_record['policies'][pi]['service']=='apis'" >
																<div v-for="v,i in role_record['policies'][pi]['things']" style="display: flex;" >
																	<select class="form-select form-select-sm w-auto" v-model="role_record['policies'][pi]['things'][i]['_id']" v-on:change="role_record_thing_select(pi,i)" >
																		<option value="*" >*</option>
																		<option v-for="tv,ti in config_allow_apis" v-bind:value="tv['_id']" >{{ tv['thing'] }}</option>
																		<option v-if="role_record['policies'][pi]['things'][i]['thing']!='*'&&role_record['policies'][pi]['things'][i]['thing']!=''" v-bind:value="role_record['policies'][pi]['things'][i]['_id']" >{{ role_record['policies'][pi]['things'][i]['thing'] }}</option>
																	</select>
																	<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="role_thing_delete(pi,i)">
																</div>
																<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="role_thing_add(pi)"></div>
															</template>
															<template v-if="role_record['policies'][pi]['service']=='tables'" >
																<div v-for="v,i in role_record['policies'][pi]['things']" style="display: flex;" >
																	<select class="form-select form-select-sm w-auto" v-model="role_record['policies'][pi]['things'][i]['_id']" v-on:change="role_record_thing_select(pi,i)" >
																		<option value="*" >*</option>
																		<option v-for="tv,ti in config_allow_tables" v-bind:value="tv['_id']" >{{ tv['thing'] }}</option>
																		<option v-if="role_record['policies'][pi]['things'][i]['thing']!='*'&&role_record['policies'][pi]['things'][i]['thing']!=''" v-bind:value="role_record['policies'][pi]['things'][i]['_id']" >{{ role_record['policies'][pi]['things'][i]['thing'] }}</option>
																	</select>
																	<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="role_thing_delete(pi,i)">
																</div>
																<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="role_thing_add(pi)"></div>
															</template>
															<template v-if="role_record['policies'][pi]['service']=='storage'" >
																<div v-for="v,i in role_record['policies'][pi]['things']" style="display: flex;" >
																	<select class="form-select form-select-sm w-auto" v-model="role_record['policies'][pi]['things'][i]['_id']" v-on:change="role_record_thing_select(pi,i)" >
																		<option value="*" >*</option>
																		<option v-for="tv,ti in config_allow_storage" v-bind:value="tv['_id']" >{{ tv['thing'] }}</option>
																		<option v-if="role_record['policies'][pi]['things'][i]['thing']!='*'&&role_record['policies'][pi]['things'][i]['thing']!=''" v-bind:value="role_record['policies'][pi]['things'][i]['_id']" >{{ role_record['policies'][pi]['things'][i]['thing'] }}</option>
																	</select>
																	<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="role_thing_delete(pi,i)">
																</div>
																<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="role_thing_add(pi)"></div>
															</template>
															<template v-if="role_record['policies'][pi]['service']=='files'" >
																<div v-for="v,i in role_record['policies'][pi]['things']" style="display: flex;" >
																	<select class="form-select form-select-sm w-auto" v-model="role_record['policies'][pi]['things'][i]['_id']" v-on:change="role_record_thing_select(pi,i)" >
																		<option value="*" >*</option>
																		<option v-for="tv,ti in config_allow_files" v-bind:value="tv['_id']" >{{ tv['thing'] }}</option>
																		<option v-if="role_record['policies'][pi]['things'][i]['thing']!='*'&&role_record['policies'][pi]['things'][i]['thing']!=''" v-bind:value="role_record['policies'][pi]['things'][i]['_id']" >{{ role_record['policies'][pi]['things'][i]['thing'] }}</option>
																	</select>
																	<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="role_thing_delete(pi,i)">
																</div>
																<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="role_thing_add(pi)"></div>
															</template>
															<template v-if="role_record['policies'][pi]['service']=='objects'" >
																<div v-for="v,i in role_record['policies'][pi]['things']" style="display: flex;" >
																	<select class="form-select form-select-sm w-auto" v-model="role_record['policies'][pi]['things'][i]['_id']" v-on:change="role_record_thing_select(pi,i)" >
																		<option value="*" >*</option>
																		<option v-for="tv,ti in config_allow_objects" v-bind:value="tv['_id']" >{{ tv['thing'] }}</option>
																		<option v-if="role_record['policies'][pi]['things'][i]['thing']!='*'&&role_record['policies'][pi]['things'][i]['thing']!=''" v-bind:value="role_record['policies'][pi]['things'][i]['_id']" >{{ role_record['policies'][pi]['things'][i]['thing'] }}</option>
																	</select>
																	<input type="button" class="btn btn-outline-danger btn-sm py-0" value="X" v-on:click="role_thing_delete(pi,i)">
																</div>
																<div><input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="role_thing_add(pi)"></div>
															</template>
														</td>
													</tr>
													<tr v-if="role_record['policies'][pi]['service']">
														<td>Actions</td>
														<td>
															<template v-if="role_record['policies'][pi]['service'] in config_allow_actions" >
															<div v-for="v,i in config_allow_actions[ role_record['policies'][pi]['service'] ]" ><label class="act"><input type="checkbox" v-model="role_record['policies'][pi]['actions']" v-bind:value="v"  v-on:click="role_record_action_select(pi)"> {{ v }}</label></div>
															</template>
														</td>
													</tr>
													<tr v-if="role_record['policies'][pi]['service']">
														<td>Things</td>
														<td>
															<div v-for="rd,ri in role_record['policies'][pi]['records']" style="display: flex;column-gap:5px;" >
																<input type="text" spellcheck="false" autocomplete="false" class="form-control form-control-sm w-auto" v-model="role_record['policies'][pi]['records'][ri]" >
																<input type="button" class="btn btn-outline-danger btn-sm py-0" value="x" v-on:click="role_record_delete_record(pi,ri)" >
															</div>
															<input type="button" class="btn btn-outline-dark btn-sm py-0" value="+" v-on:click="role_record_add_record(pi)" >
														</td>
													</tr>
												</table>
											</div>
											<div><div v-on:click="role_add_policy" class="btn btn-link btn-sm py-0" >Add Policy</div></div>
										</td>
									</tr>
								</table>
								<div v-if="ra_err" class="text-danger" >{{ ra_err }}</div>
								<div v-if="ra_msg" class="text-success" >{{ ra_msg }}</div>


							<!-- <pre>{{ role_record }}</pre> -->


		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
		        <button type="button" class="btn btn-outline-dark btn-sm"  v-on:click="role_save_record">Save Role</button>
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

						<table v-if="'policies' in token_record" class="table table-bordered table-sm w-auto" >
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
							<tr>
								<td>IP</td>
								<td>
									<div>{{ token_record['ips'][0] }}</div>
								</td>
							</tr>
							<tr>
								<td>Max Hits</td>
								<td>
									<div>{{ token_record['maxhits'] }}</div>
								</td>
							</tr>
							<tr>
								<td>Max Hits per minute</td>
								<td>
									<div>{{ token_record['hitsmin'] }}</div>
								</td>
							</tr>
							<tr>
								<td>Hits</td>
								<td>
									<div>{{ token_record['hits'] }}</div>
								</td>
							</tr>
						</table>

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
			myip: "<?=$_SERVER['REMOTE_ADDR'] . "/32" ?>",
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
			roles: [],
			sessions: [],
			ak_error: "",ak_msg: "",r_err: "",r_msg: "",ra_err: "",ra_msg: "",sess_err: "",sess_msg: "",
			ak_form: false,
			ak_record: {
				"app_id": "<?=$config_param1 ?>",
				"des": "",
				"active": "y",
				"expire": "","expire_utc": "",
				"policies": [
					{
						"service": "tables",
						"actions": ["*"],
						"things": [{"thing":"*","_id":"*"}],
						"records": ["*"],
					}
				],
				"ips": ["192.168.1.1/32"],
				"s": false,
				"max_s": false,
			},
			role_record: {
				"name": "",
				"policies": [
					{
						"service": "tables",
						"actions": ["*"],
						"things": [{"thing":"*","_id":"*"}],
						"records": ["*"],
					}
				],
			},
			role_edit_vi: -1,
			token_record: {
				"app_id": "<?=$config_param1 ?>",
				"active": "y",
				"expire": "60",
				"policies": [
					{
						"service": "tables",
						"actions": ["*"],
						"things": [{"thing":"*","_id":"*"}],
						"records": ["*"],
					}
				],
				"ips": ["192.168.1.1/32"],
				"allow_sessions": false,
			},
			expire_timestamp: 0,
			expire_minits: 5,
			token_expire_project: "",
			token_edit_modal: false,
			ak_add_msg: "",
			ak_add_error: "",
			ak_edit_vi: -1,
			token_edit_vi: -1,
			config_allow_actions: {},
			config_allow_tables: [],
			config_allow_apis: [],
			config_allow_storage: [],
			config_allow_files: [],
			config_allow_objects: [],
			users_tab: false,
			users: [],
			user_add_msg: "",
			user_add_error: "",
			user_form: false,
			user_msg: "",
			user_error: "",
			user_edit_vi: -1,
			user_record: {
				"app_id": "<?=$config_param1 ?>",
				"username": "",
				"password": "",
				"active": "y",
				"roles": [],
			},
		};
	},
	mounted(){
		//this.load_apis();
		this.load_users();
		this.load_things();
	},
	methods: {
		getExpireString: function( minutes = 0 ){
			var dt = new Date();
			var diff = dt.getTimezoneOffset()*-1;
			var t = dt.getTime();
			t = t + ( diff * 60 * 1000 );
			t = t + ( minutes * 60 * 1000 );
			var dt2 = new Date( t );
			return dt2.toISOString().substr(0,10) + " " + dt2.toISOString().substr(11,8);
		},
		getExpireStringUTC: function( minutes = 0 ){
			var dt = new Date();
			var diff = dt.getTimezoneOffset()*-1;
			var t = dt.getTime();
			t = t + ( minutes * 60 * 1000 );
			var dt2 = new Date( t );
			return dt2.toISOString().substr(0,10) + " " + dt2.toISOString().substr(11,8);
		},
		getUTCDate: function( dd ){
			var dt = new Date(dd);
			return dt.toISOString().substr(0,10) + " " + dt.toISOString().substr(11,8);
		},
		fix_policies: function(v){
			if( v == null ){
				return [];
			}
			if( 'length' in v == false || typeof(v) != "object" ){
				return [];
			}
			for( var i=0;i<v.length;i++ ){
				if( 'records' in v[i] == false ){
					v[i]['records'] = ["*"];
				}
			}
			return v;
		},
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
		ak_record_delete_record: function(pi,ri){
			this.ak_record['policies'][pi]['records'].splice(ri,1);
			if( this.ak_record['policies'][pi]['records'].length == 0 ){
				this.ak_record['policies'][pi]['records'].push("*");
			}
		},
		ak_record_add_record: function(pi){
			this.ak_record['policies'][pi]['records'].push("*");
		},
		ak_record_action_select: function(pi){
			setTimeout(this.ak_record_action_select2,200,pi);
		},
		ak_record_action_select2: function(pi){
			var f = false;
			for(var j=0;j<this.ak_record['policies'][pi]['actions'].length;j++){
				if( this.ak_record['policies'][pi]['actions'][j] == "*" ){
					f = true;
				}
			}
			if( f ){
				this.ak_record['policies'][pi]['actions'] = ["*"];
			}
		},
		ak_record_thing_select: function(pi,i){
			if( this.ak_record['policies'][pi]['service']=='tables' ){
				for(var j=0;j<this.config_allow_tables.length;j++){
					if( this.ak_record['policies'][pi]['things'][i]['_id'] == this.config_allow_tables[j]['_id'] ){
						this.ak_record['policies'][pi]['things'][i]['thing'] = this.config_allow_tables[j]['thing']+'';
					}
				}
			}else if( this.ak_record['policies'][pi]['service']=='apis' ){
				for(var j=0;j<this.config_allow_apis.length;j++){
					if( this.ak_record['policies'][pi]['things'][i]['_id'] == this.config_allow_apis[j]['_id'] ){
						this.ak_record['policies'][pi]['things'][i]['thing'] = this.config_allow_apis[j]['thing']+'';
					}
				}
			}
		},
		ak_record_service_select: function(pi){
			this.ak_record['policies'][pi]['things'] = [{"thing":"*", "_id":"*"}];
		},
		open_tab: function(v){
			this.tab = v;
			if( v== 'keys' ){
				this.load_access_keys();
			}
			if( v== 'sessions' ){
				this.load_sessions();
			}
			if( v== 'tokens' ){
				this.load_tokens();
			}
			if( v== 'users' ){
				this.load_users();
			}
			if( v== 'roles' ){
				this.load_roles();
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
		goto_next: function(){if( this.LastEvaluatedKey ){this.load_records();}},
		search_now: function(){this.LastEvaluatedKey = false;this.load_records();},
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
								for( var i=0;i<response.data['data'].length;i++){
									if( 'policies' in response.data['data'][i] ){
										response.data['data'][i]['policies'] = this.fix_policies(response.data['data'][i]['policies']);
									}
								}
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
		load_roles: function(){
			this.r_error = "";
			this.r_msg = "Loading...";
			axios.post("?",{"action": "load_roles"}).then(response=>{
				this.r_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( "status" in response.data ){
							if( response.data['status'] == "success" ){
								for( var i=0;i<response.data['data'].length;i++){
									if( 'policies' in response.data['data'][i] ){
										response.data['data'][i]['policies'] = this.fix_policies(response.data['data'][i]['policies']);
									}
								}
								this.roles = response.data['data'];
							}else{
								this.r_error = response.data['error'];
							}
						}else{
							this.r_error = "Incorrect response!";
						}
					}else{
						this.r_error = "Incorrect response!";
					}
				}else{
					this.r_error = "Incorrect response!";
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
								for( var i=0;i<response.data['data'].length;i++){
									if( 'policies' in response.data['data'][i] ){
										if( 'policies' in response.data['data'][i] ){
											response.data['data'][i]['policies'] = this.fix_policies(response.data['data'][i]['policies']);
										}
									}
								}
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
		load_sessions: function(){
			this.sess_error = "";
			this.sess_msg = "Loading...";
			axios.post( "?", {"action": "load_sessions"} ).then(response=>{
				this.sess_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( "status" in response.data ){
							if( response.data['status'] == "success" ){
								for( var i=0;i<response.data['data'].length;i++){
									if( 'policies' in response.data['data'][i] ){
										if( 'policies' in response.data['data'][i] ){
											response.data['data'][i]['policies'] = this.fix_policies(response.data['data'][i]['policies']);
										}
									}
								}
								this.sessions = response.data['data'];
							}else{
								this.sess_error = response.data['error'];
							}
						}else{
							this.sess_error = "Incorrect response!";
						}
					}else{
						this.sess_error = "Incorrect response!";
					}
				}else{
					this.sess_error = "Incorrect response!";
				}
			});
		},
		show_ak_add: function(){
			this.ak_edit_modal = new bootstrap.Modal( document.getElementById('key_edit_modal') );
			this.ak_edit_modal.show();
			this.cmsg = ""; this.cerr = "";
			this.ak_record = {
				"app_id": "<?=$config_param1 ?>",
				"des": "",
				"active": "y",
				"expire": this.getExpireString( 5 ),
				"expire_utc": this.getExpireStringUTC( 5 ),
				"policies": [
					{
						"service": "",
						"actions": ["*"],
						"things": [{"thing":"*","_id":"*"}],
						"records": ["*"],
					}
				],
				"ips": [this.myip],
				"allow_sessions": false,
				"s": false,
				"max_s": -1
			};
			this.expire_minits = 5;
		},
		close_ak_add: function(){
			this.ak_edit_modal.hide();
		},
		ak_save_record: function(){
			this.ak_add_error = "";
			this.ak_add_msg = "";
			if( this.ak_record['des'].match( /^[a-z0-9\-\_\,\.\(\)\ ]+$/i) == null && this.ak_record['des'].length <=200 ){
				this.ak_add_error= "Description should be simple. No special chars. Max length 200";return false;
			}
			for(var pi=0;pi<this.ak_record['policies'].length;pi++){
				if( this.ak_record['policies'][pi]['service'] =="" ){
					this.ak_add_error= "Policy "+(pi+1)+": Service Required";return false;
				}
				if( this.ak_record['policies'][pi]['service'] in this.config_allow_actions == false ){
					this.ak_add_error= "Service unknown";return false;
				}
				for(var i=0;i<this.ak_record['policies'][pi]['actions'].length;i++){
					if( this.ak_record['policies'][pi]['actions'][i] == "*" ){
						if( this.ak_record['policies'][pi]['actions'].length > 1 ){
							this.ak_add_error= "Policy "+(pi+1)+": When all Actions allowed, single rule is required!";return false;
						}
					}else if( this.config_allow_actions[  this.ak_record['policies'][pi]['service']  ].indexOf( this.ak_record['policies'][pi]['actions'][i] ) == -1 ){
						this.ak_add_error= "Policy "+(pi+1)+": Action `"+this.ak_record['policies'][pi]['actions'][i]+"`not found";return false;
					}
				}

				for(var i=0;i<this.ak_record['policies'][pi]['things'].length;i++){
					if( this.ak_record['policies'][pi]['things'][i]['_id'] == "*" ){
						if( this.ak_record['policies'][pi]['things'].length > 1 ){
							this.ak_add_error= "Policy "+(pi+1)+": When all Tables allowed, single rule is required!";return false;
						}
					}else if( this.ak_record['policies'][pi]['things'][i]['_id'].match( /^(.*?)\:[a-f0-9]+$/ ) == null ){
						this.ak_add_error= "Policy "+(pi+1)+": Incorrect thing iD";return false;
					}else{
						if( this.ak_record['policies'][pi]['service']=='tables' ){
							var f = false;
							for(var j=0;j<this.config_allow_tables.length;j++){
								if( this.config_allow_tables[j]['_id'] == this.ak_record['policies'][pi]['things'][i]['_id'] ){
									f = true;
								}
							}
							if( f == false ){
								this.ak_add_error= "Policy "+(pi+1)+": TableName `"+this.ak_record['policies'][pi]['things'][i]['thing']+"` not found";return false;
							}
						}else if( this.ak_record['policies'][pi]['service']=='apis' ){
							var f = false;
							for(var j=0;j<this.config_allow_apis.length;j++){
								if( this.config_allow_apis[j]['_id'] == this.ak_record['policies'][pi]['things'][i]['_id'] ){
									f = true;
								}
							}
							if( f == false ){
								this.ak_add_error= "Policy "+(pi+1)+": API `"+this.ak_record['policies'][pi]['things'][i]['thing']+"` not found";return false;
							}
						}else if( this.ak_record['policies'][pi]['service']=='files' ){
							var f = false;
							for(var j=0;j<this.config_allow_files.length;j++){
								if( this.config_allow_files[j]['_id'] == this.ak_record['policies'][pi]['things'][i]['_id'] ){
									f = true;
								}
							}
							if( f == false ){
								this.ak_add_error= "Policy "+(pi+1)+": API `"+this.ak_record['policies'][pi]['things'][i]['thing']+"` not found";return false;
							}
						}else if( this.ak_record['policies'][pi]['service']=='storage' ){
							var f = false;
							for(var j=0;j<this.config_allow_storage.length;j++){
								if( this.config_allow_storage[j]['_id'] == this.ak_record['policies'][pi]['things'][i]['_id'] ){
									f = true;
								}
							}
							if( f == false ){
								this.ak_add_error= "Policy "+(pi+1)+": API `"+this.ak_record['policies'][pi]['things'][i]['thing']+"` not found";return false;
							}
						}else if( this.ak_record['policies'][pi]['service']=='objects' ){
							var f = false;
							for(var j=0;j<this.config_allow_objects.length;j++){
								if( this.config_allow_objects[j]['_id'] == this.ak_record['policies'][pi]['things'][i]['_id'] ){
									f = true;
								}
							}
							if( f == false ){
								this.ak_add_error= "Policy "+(pi+1)+": API `"+this.ak_record['policies'][pi]['things'][i]['thing']+"` not found";return false;
							}
						}else{
							this.ak_add_error= "Policy "+(pi+1)+": Unknown Thing `"+this.ak_record['policies'][pi]['things'][i]['thing']+"` ";return false;
						}
					}
				}
				if( this.ak_record['policies'][pi]['records'].length > 10 ){
					this.ak_add_error= "Policy "+(pi+1)+": Records too many entries";return false;
				}
				var ra = false;
				for(var i=0;i<this.ak_record['policies'][pi]['records'].length;i++){
					if( this.ak_record['policies'][pi]['records'][i] == "*" ){
						if( ra ){
							this.ak_add_error= "Policy "+(pi+1)+": Records `*` Duplicate";return false;
						}
						ra = true;
					}else if( this.ak_record['policies'][pi]['records'][i].match(/^[a-z0-9\:\.\-\_\/\@\*]{1,200}$/i) == null ){
						this.ak_add_error= "Policy "+(pi+1)+": Records `"+this.ak_record['policies'][pi]['records'][i]+"` Invalid Format";return false;
					}
				}
				if( ra ){
					for(var i=0;i<this.ak_record['policies'][pi]['records'].length;i++){
						if( this.ak_record['policies'][pi]['records'][i] != "*" ){
							this.ak_add_error= "Policy "+(pi+1)+": Records `*` overrides other records";return false;
						}
					}
				}
			}
			for(var i=0;i<this.ak_record['ips'].length;i++){
				if( this.ak_record['ips'][i] == "*" ){
					if( this.ak_record['ips'].length > 1 ){
						this.ak_add_error= "When all IPs allowed, single rule is required!";return false;
					}
				}else if( this.ak_record['ips'][i].match( /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(32|24|16)$/ ) == null ){
					this.ak_add_error= "IP address should be #.#.#.#/(32/24/16)";return false;
				}else{
					var m = this.ak_record['ips'][i].match( /^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\/(32|24|16)$/ );
					if( Number(m[1]) < 1 || Number(m[1]) > 255 ){
						this.ak_add_error= "Ip address should be #.#.#.#/(32/24/16). digit should be between 1-255";return false;
					}else if( Number(m[2]) < 0 || Number(m[2]) > 255 ){
						this.ak_add_error= "Ip address should be #.#.#.#/(32/24/16). digit should be between 0-255";return false;
					}else if( Number(m[3]) < 0 || Number(m[3]) > 255 ){
						this.ak_add_error= "Ip address should be #.#.#.#/(32/24/16). digit should be between 0-255";return false;
					}else if( Number(m[4]) < 0 || Number(m[4]) > 255 ){
						this.ak_add_error= "Ip address should be #.#.#.#/(32/24/16). digit should be between 0-255";return false;
					}
				}
			}
			if( this.ak_record['s'] ){
				console.log( this.ak_record['max_s'].toString() );
				if( this.ak_record['max_s'].toString().match(/^[0-9]+$/) == null && this.ak_record['max_s'].toString() != "-1" ){
					this.ak_add_error= "Maximum Sessions should be a number";return false;
				}
				if( this.ak_record['max_s'].toString() != "-1" ){
					if( Number(this.ak_record['max_s']) < 1 || Number(this.ak_record['max_s']) > 1000 ){
						this.ak_add_error= "Maximum Sessions should be between 1 to 200";return false;
					}
				}
			}
			try{
				this.ak_record['expire_utc'] = this.getUTCDate( this.ak_record['expire'] );
				console.log( this.getUTCDate( this.ak_record['expire'] ) );
				var dt = new Date(this.ak_record['expire']);
				var t1 = dt.getTime();
				var dt2 = new Date();
				var t2 = dt2.getTime();
				if( t1 < t2 ){
					this.ak_add_error= "Expire timestamp should be a time in future";return false;
				}
			}catch(e){
				this.ak_add_error= "Invalid timestamp";return false;
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
		ak_del_policy: function(vi){
			if( this.ak_record['policies'].length > 1 ){
				this.ak_record['policies'].splice(vi,1);
			}else{
				alert("Need at least one policy!");
			}
		},
		ak_add_policy: function(){
			if( this.ak_record['policies'].length < 5 ){
				this.ak_record['policies'].push({
					"service": "",
					"actions": ["*"],
					"things": [{"_id":"*", "thing": "*"}],
					"records": ["*"],
				});
			}else{
				alert("Too many policies!");
			}
		},
		ak_ip_add: function(){
			if( this.ak_record['ips'].length < 20 ){
				this.ak_record['ips'].push("192.168.1.1");
			}else{
				alert("Too many ips");
			}
		},
		ak_ip_delete: function(vi){
			if( this.ak_record['ips'].length > 1 ){
				this.ak_record['ips'].splice(vi,1);
			}else{
				alert("Need at least one ip condition. but it can be *");
			}
		},
		ak_thing_add: function(pi){
			if( this.ak_record['policies'][pi]['things'].length < 20 ){
				this.ak_record['policies'][pi]['things'].push({"thing":"*","_id":"*"}); 
			}else{
				alert("Too many things");
			}
		},
		ak_thing_delete: function(pi,vi){
			if( this.ak_record['policies'][pi]['things'].length > 1 ){
				this.ak_record['policies'][pi]['things'].splice(vi,1);
			}else{
				alert("Need at least one table. but it can be *")
			}
		},
		ak_record_timestamp_edit: function(){
			console.log( this.getUTCDate( this.ak_record['expire'] ) );
			//this.ak_record['expire_utc'] = this.getExpireStringUTC( Number( this.expire_minits ) );
		},
		ak_record_timestamp_change: function(){
			this.ak_record['expire'] = this.getExpireString( Number( this.expire_minits ) );
			this.ak_record['expire_utc'] = this.getExpireStringUTC( Number( this.expire_minits ) );
		},
		token_record_expire_change: function(){
			setTimeout(this.token_record_expire_change2,200);
		},
		token_record_expire_change2: function(){
			var t = (Number(this.token_record['expire'])*1000);
			var d = new Date(t);
		},
		ak_edit_open: function(vi){
			this.ak_edit_vi = vi;
			this.ak_record = JSON.parse( JSON.stringify(this.ak_keys[vi]) );
			this.expire_minits = "";
			this.ak_edit_modal = new bootstrap.Modal( document.getElementById('key_edit_modal') );
			this.ak_edit_modal.show();
		},
		token_edit_open: function(vi){
			this.token_edit_vi = vi;
			this.token_record = JSON.parse( JSON.stringify(this.tokens[vi]) );
			this.expire_minits = "";
			this.token_record_expire_change2();
			this.token_edit_modal = new bootstrap.Modal( document.getElementById('token_edit_modal') );
			this.token_edit_modal.show();
		},
		ak_list_get_date: function(t){
			var dt = new Date();
			var diff = dt.getTimezoneOffset();
			var d = new Date(t);
			var t = d.getTime();
			var t3 = t + ( diff*-1 * 60 * 1000 ) + ( diff*-1 * 60 * 1000 );
			var t2 = (new Date()).getTime();
			//var dd = d.toISOString().substr(0,16).replace("T", " ");
			var dd = new Date(t3).toISOString().substr(0,10) + " " + new Date(t3).toISOString().substr(11,5);
			var exp_diff = t+( diff*-1 * 60 * 1000 ) - t2 ;
			exp_diff = parseInt(exp_diff/1000);
			if( exp_diff > 0 ){
				if( exp_diff < 90 ){
					dd = dd + "<BR>Expires in " + exp_diff + " seconds";
				}else{
					exp_diff = parseInt(exp_diff/60);
					if( exp_diff < 120 ){
						dd = dd + "<BR>Expires in " + exp_diff + " minutes";
					}else{
						exp_diff = parseInt(exp_diff/60);
						if( exp_diff < 36 ){
							dd = dd + "<BR>Expires in " + exp_diff + " hours";
						}else{
							exp_diff = parseInt(exp_diff/24);
							dd = dd + "<BR>Expires in " + exp_diff + " days";
						}
					}
				}
			}
			if( t+( diff*-1 * 60 * 1000 ) < t2 ){
				dd = dd + "<BR><span class='text-danger' >Expired</span>";
			}
			return dd;
		},
		ak_list_last_date: function(t){
			var t = Number(t)*1000;
			var d = new Date(t);
			var dd = d.toDateString() + " "+ d.toTimeString().substr(0,20);
			var dd = this.toDateString(t);
			return dd;
		},
		token_get_date: function(t){
			var t = Number(t)*1000;
			var d = new Date(t);
			var t2 = (new Date()).getTime();
			console.log( t - t2 );
			//var dd = d.toISOString().substr(0,16).replace("T", " ");
			var dd = d.toDateString() + " "+ d.toTimeString().substr(0,20);
			var dd = this.toDateString(t);
			if( t < t2 ){
				dd = dd + "<BR><span class='text-danger' >Expired</span>";
			}
			return dd;
		},
		token_last_date: function(t){
			var t = Number(t)*1000;
			var d = new Date(t);
			//var dd = d.toISOString().substr(0,16).replace("T", " ");
			//var dd = d.toDateString() + " "+ d.toTimeString().substr(0,20);
			var dd = this.toDateString(t);
			return dd;
		},
		toDateString(t){
			var dt = new Date( t );
			var vy  = Number(dt.getFullYear());
			var vm  = Number(dt.getMonth()+1);
			var vd  = Number(dt.getDate());
			var vhr = Number(dt.getHours());
			var vmn = Number(dt.getMinutes());
			var vsc = Number(dt.getSeconds());
			if( vm < 10 ){ vm = "0"+vm;}if( vd < 10 ){ vd = "0"+vd;}if( vhr < 10 ){ vhr = "0"+vhr;}if( vmn < 10 ){ vmn = "0"+vmn;}if( vsc < 10 ){ vsc = "0"+vsc;}
			return vy + "-" + vm + "-" + vd + " " + vhr + ":" + vmn + ":" + vsc;
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
								for( var i=0;i<response.data['data'].length;i++){
									if( 'roles' in response.data['data'][i] == false ){
										response.data['data'][i]['roles'] =[];
									}else if( 'length' in response.data['data'][i]['roles'] == false || typeof(response.data['data'][i]['roles']) != "object" ){
										response.data['data'][i]['roles'] =[];
									}
								}
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
				this.ak_error = "";
				this.ak_msg = "Deleting...";
				axios.post( "?", {
					"action":"auth_access_key_delete", 
					"access_key_id": this.ak_keys[ vi ]['_id']
				}).then(response=>{
					this.ak_msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( "status" in response.data ){
								if( response.data['status'] == "success" ){
									this.load_access_keys();
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
			}
		},
		token_delete: function( vi ){
			if( confirm("Are you sure to delete this User Session Key?" ) ){
				this.ak_error = "";
				this.ak_msg = "Deleting...";
				axios.post( "?", {
					"action":"auth_session_key_delete", 
					"access_key_id": this.tokens[ vi ]['_id']
				}).then(response=>{
					this.ak_msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( "status" in response.data ){
								if( response.data['status'] == "success" ){
									this.load_tokens();
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
			}
		},
		show_user_add: function(){
			this.user_record = {
				"app_id": "<?=$config_param1 ?>",
				"username": "",
				"password": "",
				"active": "y",
				"roles": [],
			};
			this.load_roles();
			this.user_edit_modal = new bootstrap.Modal( document.getElementById('user_edit_modal') );
			this.user_edit_modal.show();
			this.cmsg = ""; this.cerr = "";
		},
		user_record_role_select: function(rolei,role){
			this.user_record['roles'][rolei]['name'] = role['name']+'';
		},
		user_record_delete_role: function(rolei){
			this.user_record['roles'].splice(rolei,1);
		},
		user_record_add_role: function(){
			if( this.user_record['roles'].length >= 5 ){alert("maximum roles assigned");}
			this.user_record['roles'].push({"_id": "","name": ""});
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
		user_edit_open: function(vi){
			this.load_roles();
			this.user_edit_vi = vi;
			this.user_record = JSON.parse( JSON.stringify(this.users[vi]) );
			this.user_edit_modal = new bootstrap.Modal( document.getElementById('user_edit_modal') );
			this.user_edit_modal.show();
			this.cmsg = ""; this.cerr = "";
		},
		user_edit_close: function(){
			this.user_edit_modal.hide();
		},

		load_things: function(){
			axios.post("?", {"action":"auth_load_things"}).then(response=>{
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( "status" in response.data ){
							if( response.data['status'] == "success" ){
								this.config_allow_tables = response.data['tables'];
								this.config_allow_apis = response.data['apis'];
								this.config_allow_storage = response.data['storage'];
								this.config_allow_files = response.data['files'];
								this.config_allow_objects = response.data['objects'];
								this.config_allow_actions = response.data['actions'];
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


		show_role_add: function(){
			this.role_edit_modal = new bootstrap.Modal( document.getElementById('role_edit_modal') );
			this.role_edit_modal.show();
			this.ra_msg = ""; this.ra_err = "";
			this.role_record = {
				"name": "",
				"policies": [
					{
						"service": "",
						"actions": ["*"],
						"things": [{"thing":"*","_id":"*"}],
						"records": ["*"],
					}
				],
			};
		},
		close_role_add: function(){
			this.role_edit_modal.hide();
		},
		role_edit_open: function(vi){
			this.role_edit_vi = vi;
			this.role_record = JSON.parse( JSON.stringify(this.roles[vi]) );
			this.role_edit_modal = new bootstrap.Modal( document.getElementById('role_edit_modal') );
			this.role_edit_modal.show();
		},
		role_record_add_record: function(pi){
			this.role_record['policies'][pi]['records'].push("*");
		},
		role_record_delete_record: function(pi,ri){
			this.role_record['policies'][pi]['records'].splice(ri,1);
			if( this.role_record['policies'][pi]['records'].length == 0 ){
				this.role_record['policies'][pi]['records'].push("*");
			}
		},
		role_record_action_select: function(pi){
			setTimeout(this.role_record_action_select2,200,pi);
		},
		role_record_action_select2: function(pi){
			var f = false;
			for(var j=0;j<this.role_record['policies'][pi]['actions'].length;j++){
				if( this.role_record['policies'][pi]['actions'][j] == "*" ){
					f = true;
				}
			}
			if( f ){
				this.role_record['policies'][pi]['actions'] = ["*"];
			}
		},
		role_record_thing_select: function(pi,i){
			if( this.role_record['policies'][pi]['service']=='tables' ){
				for(var j=0;j<this.config_allow_tables.length;j++){
					if( this.role_record['policies'][pi]['things'][i]['_id'] == this.config_allow_tables[j]['_id'] ){
						this.role_record['policies'][pi]['things'][i]['thing'] = this.config_allow_tables[j]['thing']+'';
					}
				}
			}else if( this.role_record['policies'][pi]['service']=='apis' ){
				for(var j=0;j<this.config_allow_apis.length;j++){
					if( this.role_record['policies'][pi]['things'][i]['_id'] == this.config_allow_apis[j]['_id'] ){
						this.role_record['policies'][pi]['things'][i]['thing'] = this.config_allow_apis[j]['thing']+'';
					}
				}
			}else if( this.role_record['policies'][pi]['service']=='objects' ){
				for(var j=0;j<this.config_allow_objects.length;j++){
					if( this.role_record['policies'][pi]['things'][i]['_id'] == this.config_allow_objects[j]['_id'] ){
						this.role_record['policies'][pi]['things'][i]['thing'] = this.config_allow_objects[j]['thing']+'';
					}
				}
			}else if( this.role_record['policies'][pi]['service']=='storage' ){
				for(var j=0;j<this.config_allow_storage.length;j++){
					if( this.role_record['policies'][pi]['things'][i]['_id'] == this.config_allow_storage[j]['_id'] ){
						this.role_record['policies'][pi]['things'][i]['thing'] = this.config_allow_storage[j]['thing']+'';
					}
				}
			}else if( this.role_record['policies'][pi]['service']=='files' ){
				for(var j=0;j<this.config_allow_files.length;j++){
					if( this.role_record['policies'][pi]['things'][i]['_id'] == this.config_allow_files[j]['_id'] ){
						this.role_record['policies'][pi]['things'][i]['thing'] = this.config_allow_files[j]['thing']+'';
					}
				}
			}
		},
		role_record_service_select: function(pi){
			this.role_record['policies'][pi]['things'] = [{"thing":"*", "_id":"*"}];
		},
		role_save_record: function(){
			this.ra_err = "";
			this.ra_msg = "";
			if( this.role_record['name'].match(/^[a-z][a-z0-9\-\_\.\ ]{2,50}$/i) == null ){
				this.ra_err= "Name should be simple. no special chars and spaces";return false;
			}
			for(var pi=0;pi<this.role_record['policies'].length;pi++){
				if( this.role_record['policies'][pi]['service'] =="" ){
					this.ra_err= "Policy "+(pi+1)+": Service Required";return false;
				}
				if( this.role_record['policies'][pi]['service'] in this.config_allow_actions == false ){
					this.ra_err= "Service unknown";return false;
				}
				for(var i=0;i<this.role_record['policies'][pi]['actions'].length;i++){
					if( this.role_record['policies'][pi]['actions'][i] == "*" ){
						if( this.role_record['policies'][pi]['actions'].length > 1 ){
							this.ra_err= "Policy "+(pi+1)+": When all Actions allowed, single rule is required!";return false;
						}
					}else if( this.config_allow_actions[  this.role_record['policies'][pi]['service']  ].indexOf( this.role_record['policies'][pi]['actions'][i] ) == -1 ){
						this.ra_err= "Policy "+(pi+1)+": Action `"+this.role_record['policies'][pi]['actions'][i]+"`not found";return false;
					}
				}

				for(var i=0;i<this.role_record['policies'][pi]['things'].length;i++){
					if( this.role_record['policies'][pi]['things'][i]['_id'] == "*" ){
						if( this.role_record['policies'][pi]['things'].length > 1 ){
							this.ra_err= "Policy "+(pi+1)+": When all Tables allowed, single rule is required!";return false;
						}
					}else if( this.role_record['policies'][pi]['things'][i]['_id'].match( /^(.*?)\:[a-f0-9]+$/ ) == null ){
						this.ra_err= "Policy "+(pi+1)+": Incorrect thing iD";return false;
					}else{
						if( this.role_record['policies'][pi]['service']=='tables' ){
							var f = false;
							for(var j=0;j<this.config_allow_tables.length;j++){
								if( this.config_allow_tables[j]['_id'] == this.role_record['policies'][pi]['things'][i]['_id'] ){
									f = true;
								}
							}
							if( f == false ){
								this.ra_err= "Policy "+(pi+1)+": TableName `"+this.role_record['policies'][pi]['things'][i]['thing']+"` not found";return false;
							}
						}else if( this.role_record['policies'][pi]['service']=='apis' ){
							var f = false;
							for(var j=0;j<this.config_allow_apis.length;j++){
								if( this.config_allow_apis[j]['_id'] == this.role_record['policies'][pi]['things'][i]['_id'] ){
									f = true;
								}
							}
							if( f == false ){
								this.ra_err= "Policy "+(pi+1)+": API `"+this.role_record['policies'][pi]['things'][i]['thing']+"` not found";return false;
							}
						}else if( this.role_record['policies'][pi]['service']=='files' ){
							var f = false;
							for(var j=0;j<this.config_allow_files.length;j++){
								if( this.config_allow_files[j]['_id'] == this.role_record['policies'][pi]['things'][i]['_id'] ){
									f = true;
								}
							}
							if( f == false ){
								this.ra_err= "Policy "+(pi+1)+": API `"+this.role_record['policies'][pi]['things'][i]['thing']+"` not found";return false;
							}
						}else if( this.role_record['policies'][pi]['service']=='storage' ){
							var f = false;
							for(var j=0;j<this.config_allow_storage.length;j++){
								if( this.config_allow_storage[j]['_id'] == this.role_record['policies'][pi]['things'][i]['_id'] ){
									f = true;
								}
							}
							if( f == false ){
								this.ra_err= "Policy "+(pi+1)+": API `"+this.role_record['policies'][pi]['things'][i]['thing']+"` not found";return false;
							}
						}else if( this.role_record['policies'][pi]['service']=='objects' ){
							var f = false;
							for(var j=0;j<this.config_allow_objects.length;j++){
								if( this.config_allow_objects[j]['_id'] == this.role_record['policies'][pi]['things'][i]['_id'] ){
									f = true;
								}
							}
							if( f == false ){
								this.ra_err= "Policy "+(pi+1)+": API `"+this.role_record['policies'][pi]['things'][i]['thing']+"` not found";return false;
							}
						}else{
							this.ra_err= "Policy "+(pi+1)+": Unknown Thing `"+this.role_record['policies'][pi]['things'][i]['thing']+"` ";return false;
						}
					}
				}
				if( this.role_record['policies'][pi]['records'].length > 10 ){
					this.ra_err= "Policy "+(pi+1)+": Records too many entries";return false;
				}
				var ra = false;
				for(var i=0;i<this.role_record['policies'][pi]['records'].length;i++){
					if( this.role_record['policies'][pi]['records'][i] == "*" ){
						if( ra ){
							this.ra_err= "Policy "+(pi+1)+": Records `*` Duplicate";return false;
						}
						ra = true;
					}else if( this.role_record['policies'][pi]['records'][i].match(/^[a-z0-9\:\.\-\_\/\@\*]{1,200}$/i) == null ){
						this.ra_err= "Policy "+(pi+1)+": Records `"+this.role_record['policies'][pi]['records'][i]+"` Invalid Format";return false;
					}
				}
				if( ra ){
					for(var i=0;i<this.role_record['policies'][pi]['records'].length;i++){
						if( this.role_record['policies'][pi]['records'][i] != "*" ){
							this.ra_err= "Policy "+(pi+1)+": Records `*` overrides other records";return false;
						}
					}
				}
			}

			this.ra_msg = "Saving...";
			axios.post("?", {
				"action": "auth_save_role",
				"role": this.role_record,
			}).then(response=>{
				this.ra_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( "status" in response.data ){
							if( response.data['status'] == "success" ){
								if( '_id' in this.role_record == false ){
									this.ra_msg = "Created new role: " + response.data['role']['_id'];
								}else{
									this.ra_msg = "Updated key";
								}
								this.role_record = response.data['role'];
								this.load_roles();
							}else{
								this.ra_err = response.data['error'];
							}
						}else{
							this.ra_err = "Incorrect response!";
						}
					}else{
						this.ra_err = "Incorrect response!";
					}
				}else{
					this.ra_err = "Incorrect response!";
				}
			});
		},
		role_del_policy: function(vi){
			if( this.role_record['policies'].length > 1 ){
				this.role_record['policies'].splice(vi,1);
			}else{
				alert("Need at least one policy!");
			}
		},
		role_add_policy: function(){
			if( this.role_record['policies'].length < 5 ){
				this.role_record['policies'].push({
					"service": "",
					"actions": ["*"],
					"things": [{"_id":"*", "thing": "*"}],
					"records": ["*"],
				});
			}else{
				alert("Too many policies!");
			}
		},
		role_thing_add: function(pi){
			if( this.role_record['policies'][pi]['things'].length < 20 ){
				this.role_record['policies'][pi]['things'].push({"thing":"*","_id":"*"}); 
			}else{
				alert("Too many things");
			}
		},
		role_thing_delete: function(pi,vi){
			if( this.role_record['policies'][pi]['things'].length > 1 ){
				this.role_record['policies'][pi]['things'].splice(vi,1);
			}else{
				alert("Need at least one table. but it can be *")
			}
		},
		role_delete: function( vi ){
			if( confirm("Are you sure to delete this Role?" ) ){
				axios.post( "?", {
					"action":"auth_role_delete", 
					"role_id": this.roles[ vi ]['_id']
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( "status" in response.data ){
								if( response.data['status'] == "success" ){
									this.load_roles();
									alert("Deleted Successfully");
								}else{
									alert( "Error: " + response.data['error'] );
								}
							}else{
								alert( "Error: Incorrect response!" );
							}
						}else{
							alert( "Error: Incorrect response!" );
						}
					}else{
						alert( "Error: Incorrect response!" );
					}
				}).catch(error=>{
					alert( "Error: " + error.message );
				});
			}
		},

	}
}).mount("#app");
</script>