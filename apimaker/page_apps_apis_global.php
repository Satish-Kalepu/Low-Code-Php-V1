<script  src="<?=$config_global_apimaker_path ?>ace/src-min/ace.js" ></script>
<script  src="<?=$config_global_apimaker_path ?>js/beautify-html.js" ></script>
<script  src="<?=$config_global_apimaker_path ?>js/beautify-css.js" ></script>
<script  src="<?=$config_global_apimaker_path ?>js/beautify.js" ></script>

<style>
	div.vid{ padding:0px 2px; cursor:pointer; }
	div.vid pre.vid{display: none; position: absolute; background-color: white; padding: 3px; border: 1px solid #aaa;}
	div.vid:hover pre.vid{display: block;}
</style>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >
			<div class="h3 mb-3">Global APIs</div>
			<div style="clear:both;"></div>

			
			<div style="height: calc( 100% - 100px ); overflow: auto;" >


				<p>Engine Environment: <select v-model="test__['domain']" v-on:click="select_test_environment__" >
						<option v-for="d,i in test_envs__" v-bind:value="d['d']" >{{ d['d'] }} ({{d['t']}})</option>
					</select>
				</p>
				<p>{{ test_url__ }}</p>

				<div class="accordion" id="main" v-if="'apis' in apis" >

				  <div class="accordion-item">
				    <h2 class="accordion-header" id="apis">
				      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseapis"  aria-controls="collapseapis">My APIs</button>
				    </h2>
				    <div id="collapseapis" class="accordion-collapse collapse" aria-labelledby="apis" data-bs-parent="#main">
				      <div class="accordion-body">
						<p>Custom APIs created by you</p>
						<template v-if="apis['apis'].length==0" >
							<p>API list emtpy</p>
						</template>
						<div class="accordion" id="apis_list">
							<div v-for="d,ti in apis['apis']" class="accordion-item">
								<h2 class="accordion-header" v-bind:id="d['_id']">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#collapse'+d['_id']"  v-bind:aria-controls="'collapse'+d['_id']">
									{{ d['path'] }}{{ d['name'] }}
									</button>
								</h2>
								<div v-bind:id="'collapse'+d['_id']" class="accordion-collapse collapse" v-bind:aria-labelledby="d['_id']" data-bs-parent="#apis_list">
									<div class="accordion-body">
										<div class="btn btn-outline-dark btn-sm" style="float:right;" v-on:click="test_user_apis__('apis',ti)">Test</div>
										<a class="btn btn-outline-dark btn-sm me-2" style="float:right;" v-bind:href="path+'apis/'+d['_id']+'/'+d['version_id']" target="_blank">Edit API</a>
										<p>{{ d['des'] }}</p>
										<div>{{ d['input-method'] }} {{ test_url__ }}{{ d['path'] }}{{ d['name'] }}</div>
								      	<div v-if="d['input-method']=='POST'">Content-Type: {{ d['input-type'] }}</div>
										<div v-if="d['input-method']=='POST'">Access-Key: xxxxxxx (optional)</div>
										<div v-if="d['input-method']=='POST'">&nbsp;</div>
										<pre v-if="d['input-method']=='POST'&&'vpost_help' in d">{{ d['vpost_help'] }}</pre>
										<pre v-else-if="d['input-method']=='POST'&&'vpost' in d">{{ d['vpost'] }}</pre>
									</div>
								</div>
							</div>
						</div>
				      </div>
				    </div>
				  </div>

				  <div class="accordion-item">
				    <h2 class="accordion-header" id="auth_apis">
				      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseauthapis"  aria-controls="collapseapis">
				        Authentication
				      </button>
				    </h2>
				    <div id="collapseauthapis" class="accordion-collapse collapse" aria-labelledby="auth_apis" data-bs-parent="#main">
				      <div class="accordion-body">
						<!-- <p>Authentication APIs</p> -->
						<div class="accordion" id="auth_apis_list">
							<div v-for="d,ti in apis['auth_apis']" class="accordion-item">
								<h2 class="accordion-header" v-bind:id="d['_id']">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#collapse'+d['_id']"  v-bind:aria-controls="'collapse'+d['_id']">
									{{ d['path'] }}
									</button>
								</h2>
								<div v-bind:id="'collapse'+d['_id']" class="accordion-collapse collapse" v-bind:aria-labelledby="d['_id']" data-bs-parent="#auth_apis_list">
									<div class="accordion-body">
										<div class="btn btn-outline-dark btn-sm" style="float:right;" v-on:click="show_test3('auth_apis',ti)">Test</div>
										<p>{{ d['des'] }}</p>
										<div>{{ d['input-method'] }} {{ test_url__ }}{{ d['path'] }}</div>
										<div>Content-Type: application/json</div>
										<div>Access-Key: xxxxxxx</div>
										<div>&nbsp;</div>
										<pre v-if="'vpost_help' in d">{{ d['vpost_help'] }}</pre>
										<pre v-else-if="'vpost' in d">{{ d['vpost'] }}</pre>
									</div>
								</div>
							</div>
						</div>
				      </div>
				    </div>
				  </div>

				  <div class="accordion-item">
				    <h2 class="accordion-header" id="captcha_apis">
				      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsecaptchaapis"  aria-controls="collapsecaptcha">
				        Captcha
				      </button>
				    </h2>
				    <div id="collapsecaptchaapis" class="accordion-collapse collapse" aria-labelledby="captcha_apis" data-bs-parent="#main">
				      <div class="accordion-body">
						<!-- <p>Authentication APIs</p> -->
						<div class="accordion" id="captcha_apis_list">
							<div v-for="d,ti in apis['captcha']" class="accordion-item">
								<h2 class="accordion-header" v-bind:id="d['_id']">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#collapse'+d['_id']"  v-bind:aria-controls="'collapse'+d['_id']">
									{{ d['path'] }}
									</button>
								</h2>
								<div v-bind:id="'collapse'+d['_id']" class="accordion-collapse collapse" v-bind:aria-labelledby="d['_id']" data-bs-parent="#captcha_apis_list">
									<div class="accordion-body">
										<div class="btn btn-outline-dark btn-sm" style="float:right;" v-on:click="show_test6('captcha',ti)">Test</div>
										<p>{{ d['des'] }}</p>
										<div>{{ d['input-method'] }} {{ test_url__ }}{{ d['path'] }}</div>
										<div>Content-Type: application/json</div>
										<div>Access-Key: xxxxxxx</div>
										<div>&nbsp;</div>
										<pre v-if="'vpost_help' in d">{{ d['vpost_help'] }}</pre>
										<pre v-else-if="'vpost' in d">{{ d['vpost'] }}</pre>
									</div>
								</div>
							</div>
						</div>
				      </div>
				    </div>
				  </div>

				  <div class="accordion-item">
				    <h2 class="accordion-header" id="tables_dynamic">
				      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsetables_dynamic"  aria-controls="collapsetables_dynamic">
				        Internal Tables
				      </button>
				    </h2>
				    <div id="collapsetables_dynamic" class="accordion-collapse collapse" aria-labelledby="tables_dynamic" data-bs-parent="#main">
				      <div class="accordion-body">
							<p>Internal Database Tables</p>

							<template v-if="apis['databases'].length==0" >
								<p>No internal tables were defined</p>
							</template>

							<div class="accordion" id="tables_dynamic_list">
							  <div v-for="d,ti in apis['tables_dynamic']" class="accordion-item">
							    <h2 class="accordion-header" v-bind:id="d['_id']">
							      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#collapse'+d['_id']"  v-bind:aria-controls="'collapse'+d['_id']">
							        {{ d['table'] }}
							      </button>
							    </h2>
							    <div v-bind:id="'collapse'+d['_id']" class="accordion-collapse collapse" v-bind:aria-labelledby="d['_id']" data-bs-parent="#tables_dynamic_list">
							      <div class="accordion-body">


									<div class="accordion" v-bind:id="'table'+d['_id']">
									  <div class="accordion-item" v-for="apitype in api_types" >
									    <h2 class="accordion-header" v-bind:id="apitype+d['_id']">
									      <button v-bind:class="{'accordion-button':true,'collapsed':d['show']!=apitype}" type="button" v-on:click="toggle_td(ti,apitype)" >
									        {{ apitype }}
									      </button>
									    </h2>
									    <div v-bind:id="'collapse'+apitype+d['_id']" v-bind:class="{'accordion-collapse':true, 'collapse':d['show']!=apitype}">
									      <div class="accordion-body" style="overflow: auto;">
									      	<div v-if="apitype in d == false" >API Configuration Missing</div>
									      	<div v-else>
										      	<div class="btn btn-outline-dark btn-sm" style="float:right;" v-on:click="show_test('tables_dynamic',ti,apitype)">Test</div>
										      	<div>POST {{ test_url__ }}{{ d['path'] }}</div>
										      	<div>Content-Type: application/json</div>
												<div>Access-Key: xxxxxxx</div>
												<div>&nbsp;</div>
										        <pre v-if="apitype in d">{{ d[apitype] }}</pre>
										        <pre v-else>api data not found</pre>
										    </div>
									      </div>
									    </div>
									  </div>
									</div>


							      </div>
							    </div>
							  </div>
							</div>

				      </div>
				    </div>
				  </div>
				  <div class="accordion-item">
				    <h2 class="accordion-header" id="databases">
				      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsedatabases"  aria-controls="collapsedatabases">
				        External Tables (Databases)
				      </button>
				    </h2>
				    <div id="collapsedatabases" class="accordion-collapse collapse" aria-labelledby="databases" data-bs-parent="#main">
				      <div class="accordion-body">
				        	<p>External Databases</p>
				        	<template v-if="apis['databases'].length==0" >
								<p>No external databases has been linked</p>
							</template>
							<div class="accordion" id="databases_list">
							  <div v-for="dd,db_id in apis['databases']" class="accordion-item">
							    <h2 class="accordion-header" v-bind:id="db_id">
							      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#collapse'+db_id"  v-bind:aria-controls="'collapse'+db_id">
							        {{ dd['db']['des'] }} - {{ dd['db']['engine'] }}
							      </button>
							    </h2>
							    <div v-bind:id="'collapse'+db_id" class="accordion-collapse collapse" v-bind:aria-labelledby="db_id" data-bs-parent="#databases_list">
							      <div class="accordion-body">
							        	
							        	<!-- <pre>{{ dd }}</pre> -->
										<div  v-if="dd['tables'].length"  class="accordion" v-bind:id="'tables_'+db_id">
										  <div v-for="td,ti in dd['tables']" class="accordion-item">
										    <h2 class="accordion-header" v-bind:id="td['_id']">
										      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#collapse'+td['_id']"  v-bind:aria-controls="'collapse'+td['_id']">
										        {{ td['table'] }}
										      </button>
										    </h2>
										    <div v-bind:id="'collapse'+td['_id']" class="accordion-collapse collapse" v-bind:aria-labelledby="td['_id']" v-bind:data-bs-parent="'#tables_'+db_id">
										      <div class="accordion-body">
										        	<!-- <pre>{{ td }}</pre> -->

														<div class="accordion" v-bind:id="'table_'+td['_id']">
														  <div class="accordion-item" v-for="apitype in api_types" >
														    <h2 class="accordion-header" v-bind:id="apitype+td['_id']">
														      <button v-bind:class="{'accordion-button':true,'collapsed':td['show']!='findOne'}" type="button" v-on:click="toggle_t(db_id,ti,apitype)" >
														        {{ apitype }}
														      </button>
														    </h2>
														    <div v-bind:id="'collapse'+apitype+td['_id']" v-bind:class="{'accordion-collapse':true, 'collapse':td['show']!=apitype}">
														      <div class="accordion-body" style="overflow: auto;">
	      														<div v-if="apitype in td == false" >API Configuration Missingxx</div>
														      	<div v-else>

															      	<div class="btn btn-outline-dark btn-sm" style="float:right;" v-on:click="show_test2('tables',db_id,ti,apitype)">Test</div>
															      	<div>POST {{ test_url__ }}{{ td['path'] }}</div>
															      	<div>Content-Type: application/json</div>
																	<div>Access-Key: xxxxxxx</div>
																	<div>&nbsp;</div>
															        <pre v-if="apitype in td">{{ td[apitype] }}</pre>
											        				<pre v-else>api data not found</pre>
											        			</div>
														      </div>
														    </div>
														  </div>
														</div>




										      </div>
										    </div>
										  </div>
										</div>
										<div v-else>No tables found</div>

							      </div>
							    </div>
							  </div>
							</div>


				      </div>
				    </div>
				  </div>



				  <div class="accordion-item">
				    <h2 class="accordion-header" id="objects">
				      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseobjects"  aria-controls="collapseobjects">
				        Graph Objects
				      </button>
				    </h2>
				    <div id="collapseobjects" class="accordion-collapse collapse" aria-labelledby="objects" data-bs-parent="#main">
				      <div class="accordion-body">

							<template v-if="apis['objects'].length==0" >
								<p>No Graph databases were defined</p>
							</template>

							<div class="accordion" id="objects_list">
							  <div v-for="d,ti in apis['objects']" class="accordion-item">
							    <h2 class="accordion-header" v-bind:id="d['_id']">
							      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#collapse'+d['_id']"  v-bind:aria-controls="'collapse'+d['_id']">
							        {{ d['name'] }}
							      </button>
							    </h2>
							    <div v-bind:id="'collapse'+d['_id']" class="accordion-collapse collapse" v-bind:aria-labelledby="d['_id']" data-bs-parent="#objects_list">
							      <div class="accordion-body">

									<div class="accordion" v-bind:id="'object'+d['_id']">
									  <div class="accordion-item" v-for="objectapi, apitype in d['apis']" >
									    <h2 class="accordion-header" v-bind:id="d['_id']+apitype">
									      <button v-bind:class="{'accordion-button':true,'collapsed':d['show']!=apitype}" type="button" v-on:click="toggle_object(ti,apitype)" >
									        {{ apitype }}
									      </button>
									    </h2>
									    <div v-bind:id="'collapse'+d['_id']+apitype" v-bind:class="{'accordion-collapse':true, 'collapse':d['show']!=apitype}">
									      <div class="accordion-body" style="overflow: auto;">
									      	<div v-if="apitype in d['apis'] == false" >API Configuration Missing</div>
									      	<div v-else>
										      	<div class="btn btn-outline-dark btn-sm" style="float:right;" v-on:click="show_test_graph('objects',ti,apitype)">Test</div>
										      	<div>POST {{ test_url__ }}{{ d['path'] }}</div>
										      	<div>Content-Type: application/json</div>
												<div>Access-Key: xxxxxxx</div>
												<div>&nbsp;</div>
										        <pre v-if="apitype in d['apis']">{{ objectapi['post'] }}</pre>
										        <pre v-else>api data not found</pre>
										    </div>
									      </div>
									    </div>
									  </div>
									</div>


							      </div>
							    </div>
							  </div>
							</div>

				      </div>
				    </div>
				  </div>


				  
				  <div class="accordion-item">
				    <h2 class="accordion-header" id="files">
				      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsefiles"  aria-controls="collapsefiles">
				        Files Internal
				      </button>
				    </h2>
				    <div id="collapsefiles" class="accordion-collapse collapse" aria-labelledby="files" data-bs-parent="#main">
				      <div class="accordion-body">
						<div class="accordion" id="files_list">
							<div v-for="d,ti in apis['files']" class="accordion-item">
								<h2 class="accordion-header" v-bind:id="d['name']">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#collapse'+d['name']"  v-bind:aria-controls="'collapse'+d['name']">
									{{ d['name'] }}
									</button>
								</h2>
								<div v-bind:id="'collapse'+d['name']" class="accordion-collapse collapse" v-bind:aria-labelledby="d['name']" data-bs-parent="#files_list">
									<div class="accordion-body">
										<div class="btn btn-outline-dark btn-sm" style="float:right;" v-on:click="show_test_dynamic('files',ti)">Test</div>
										<p>{{ d['des'] }}</p>
										<div>{{ d['input-method'] }} {{ test_url__ }}{{ d['path'] }}</div>
										<div>Content-Type: {{ d['content-type'] }}</div>
										<div>Access-Key: xxxxxxx</div>
										<div>&nbsp;</div>
										<div>Payload:</div>
										<pre v-if="'vpost_help' in d">{{ d['vpost_help'] }}</pre>
										<template v-else-if="'payload' in d">
											<pre v-if="d['content-type']=='application/json'" >{{ d['payload'] }}</pre>
											<div v-else-if="d['content-type']=='multipart/form-data'" >
												<table class="table table-bordered table-sm w-auto">
												<tr v-for="pd,pi in d['payload']">
													<td>{{ pi }}</td><td>{{ pd }}</td>
												</tr>
												</table>
											</div>
											<div v-else>Not found</div>
										</template>
										<template v-if="'response-type' in d">
											<div>Response:</div>
											<div>Content-Type: {{ d['response-type'] }}</div>
											<pre>{{ d['response-body'] }}</pre>
										</template>
									</div>
								</div>
							</div>
						</div>
				      </div>
				    </div>
				  </div>

				  <div class="accordion-item">
				    <h2 class="accordion-header" id="storage">
				      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsestorage"  aria-controls="collapsestorage">
				        Files External (Storage Vaults)
				      </button>
				    </h2>
				    <div id="collapsestorage" class="accordion-collapse collapse" aria-labelledby="storage" data-bs-parent="#main">
				      <div class="accordion-body">
							<p>Cloud storage services</p>
							<template v-if="apis['storage'].length==0" >
								<p>Not configured</p>
							</template>
							<div class="accordion" id="storage_list">
							  <div v-for="d,ti in apis['storage']" class="accordion-item">
							    <h2 class="accordion-header" v-bind:id="d['_id']">
							      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#collapse'+d['_id']"  v-bind:aria-controls="'collapse'+d['_id']">
							        {{ d['des'] }} - &nbsp;&nbsp;&nbsp;<span class="text-secondary" >{{ d['vault_type'] }}</span>
							      </button>
							    </h2>
							    <div v-bind:id="'collapse'+d['_id']" class="accordion-collapse collapse" v-bind:aria-labelledby="d['_id']" data-bs-parent="#storage_list">
							      <div class="accordion-body">

									<div class="accordion" v-bind:id="'storage'+d['_id']">
									  <div class="accordion-item" v-for="sapi,sapiname in d['apis']" >
									    <h2 class="accordion-header" v-bind:id="sapiname+d['_id']">
									      <button v-bind:class="{'accordion-button':true,'collapsed':d['show']!=sapiname}" type="button" v-on:click="toggle_sapi(ti,sapiname)" >
									        {{ sapiname }}
									      </button>
									    </h2>
									    <div v-bind:id="'collapse'+sapiname+d['_id']" v-bind:class="{'accordion-collapse':true, 'collapse':d['show']!=sapiname}">
									      <div class="accordion-body" style="overflow: auto;">
									      	<div class="btn btn-outline-dark btn-sm" style="float:right;" v-on:click="show_test_dynamic2('storage',ti,sapiname)">Test</div>
									      	<div>POST {{ test_url__ }}{{ sapi['path'] }}</div>
									      	<div>Content-Type: {{ sapi['content-type'] }}</div>
											<div>Access-Key: xxxxxxx</div>
											<div>&nbsp;</div>
									        
									        <div>Payload:</div>
											<pre v-if="'vpost_help' in sapi">{{ sapi['vpost_help'] }}</pre>
											<template v-else-if="'payload' in sapi">
												<pre v-if="sapi['content-type']=='application/json'" >{{ sapi['payload'] }}</pre>
												<div v-else-if="sapi['content-type']=='multipart/form-data'" >
													<table class="table table-bordered table-sm w-auto">
													<tr v-for="pd,pi in sapi['payload']">
														<td>{{ pi }}</td><td>{{ pd }}</td>
													</tr>
													</table>
												</div>
												<div v-else>Not found</div>
											</template>
											<template v-if="'response-type' in sapi">
												<div>Response:</div>
												<div>Content-Type: {{ sapi['response-type'] }}</div>
												<pre>{{ sapi['response-body'] }}</pre>
											</template>

									      </div>
									    </div>
									  </div>
									</div>


							      </div>
							    </div>
							  </div>
							</div>

				      </div>
				    </div>
				  </div>




				</div>

			</div>
		</div>
	</div>


	<div class="modal fade" id="popup_test__" tabindex="-1" >
	  <div class="modal-dialog modal-lg modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <div class="modal-title" ><h5 class="d-inline">API Test</h5></div>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
	      </div>
	      <div class="modal-body"  style="position: relative;">

	      	<p>Engine Environment: <select v-model="test__['domain']" v-on:click="select_test_environment__" >
					<option v-for="d,i in test_envs__" v-bind:value="d['d']" >{{ d['d'] }} ({{d['t']}})</option>
				</select>
			</p>
			<!-- <pre>{{ apis__[ test_type__ ][ test_table_id__ ] }}</pre> -->

			<div>URL</div>
			<div>{{ test_api_method__ }} {{ test_url__ }}{{ test_path__ }}</div>
			<div>Access-Key:</div>
			<div><input type="text" class="form-control form-control-sm" v-model="access_token__" ></div>
			<div style="float:left;">
				<div style="color: blue;" v-if="kmsg">{{ kmsg }}</div>
				<div style="color: red;" v-if="kerr">{{ kerr }}</div>
			</div>
			<div style="float:right;"><div class="btn btn-outline-dark btn-sm py-0" v-on:click="generate_access_key()" >Generate Temporary Access key</div></div>
			<div style="clear: both;"></div>

			<div>Body</div>
			<!-- <textarea spellcheck="false" class="form-control form-control-sm" style="height:200px;" v-model="test_data__"></textarea> -->
			<table v-if="test_api_method__=='GET'" class="table table-bordered table-sm w-auto" >
				<tr v-for="dd,di in test_data__" >
					<td>{{ di }}</td>
					<td>
						<input v-if="dd['type']=='boolean'" type="checkbox" class="form-control form-control-sm" v-model="dd['value']">
						<input v-else v-bind:type="dd['type']" class="form-control form-control-sm" v-model="dd['value']">
					</td>
				</tr>
			</table>
			<template v-else-if="'content-type' in test_thing__" >
				<div v-if="test_thing__['content-type']=='application/json'" id="json_editor_block" style="display: relative; width:100%; height:200px;resize:both; font-size:1rem;" ></div>
				<table v-else class="table table-bordered table-sm w-auto" >
					<tr v-for="dd,di in test_thing__['formdata']" >
						<td>{{ di }}</td>
						<td>
							<input v-if="dd['type']=='file'" type="file" class="form-control form-control-sm" v-bind:id="'form_'+di">
							<input v-else v-bind:type="dd['type']" class="form-control form-control-sm" v-model="dd['value']">
						</td>
					</tr>
				</table>
			</template>
			<template v-else>
				<div id="json_editor_block" style="display: relative; width:100%; height:200px;resize:both; font-size:1rem;" ></div>
			</template>
			<div>&nbsp;</div>
			<input type="button" class="btn btn-outline-dark btn-sm" value="TEST" v-on:click="dotest_table()" >

			<div v-if="tmsg" style="color: blue;">{{ tmsg }}</div>
			<div v-if="terr" style="color: red;" >{{ terr }}</div>

			<div style="min-height: 200px;">

				<div v-if="'status' in test_response__" >
					<div style="color: blue;">Http Status: {{ test_response__['status'] }}</div>
					<div>Headers: <div  v-if="show_headers==false"  class="btn btn-link btn-sm py-0" v-on:click="show_headers=true">Show</div><div v-if="show_headers" class="btn btn-link btn-sm py-0" v-on:click="show_headers=false">Hide</div></div>
					<pre v-if="show_headers" style="margin:0px;padding:0px 10px; border:1px solid #ccc; background-color: #f8f0f8;">{{ test_response__['headers'] }}</pre>
					<div>Response Body:</div>
					<div v-if="is_it_attachment()" >
						<div>File downloaded</div>
						<p><div class="btn btn-link btn-sm" v-on:click="save_attachment()">Click to Save</div></p>
					</div>
					<pre v-else style="margin:0px;padding:0px 10px; background-color: #f8f0f8; border:1px solid #ccc;">{{ test_response__['data'] }}</pre>
				</div>
				<div v-if="'code' in test_error__" >
					<div style="color:red;">{{ test_error__['message'] }}</div>
					<div v-if="'response' in test_error__" >
						<div>Http Status: {{ test_error__['response']['status'] }}</div>
						<div>Headers: <div v-if="show_headers==false" class="btn btn-link btn-sm py-0" v-on:click="show_headers=true">Show</div><div v-if="show_headers" class="btn btn-link btn-sm py-0" v-on:click="show_headers=false">Hide</div></div>
						<pre v-if="show_headers"  style="padding:0px 10px; border:1px solid #ccc; background-color: #f8f0f8;">{{ test_error__['response']['headers'] }}</pre>
						<div>Response Body:</div>
						<pre style="padding:0px 10px; background-color: #f8f0f8; border:1px solid #ccc;">{{ test_error__['response']['data'] }}</pre>
					</div>
				</div>

			</div>
	      	
			<div style="height:50px">&nbsp;</div>

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
			msg: "",err: "",
			kmsg: "",kerr: "",
			tmsg: "",terr: "",
			cmsg: "",
			cerr: "",
			apis: {},
			show_create_api: false,
			api_types: ['getSchema','findOne','findMany','insertOne','insertMany','updateOne', 'updateMany', 'deleteOne', 'deleteMany'],
			access_token__: "",
			create_app_modal: false,
			token: "",
			ace_editor: false,
			test__: {
				"domain": "",
				"path": "",
				"factors": {"t":"O", "v": {}}
			},
			"test_envs__": [],
			"test_url__"			: "",
			"test_path__": "",
			"test_type__": "",
			"test_thing_id__": "","test_thing__": {},
			"test_db_id__": "",
			"test_api_type__": "",
			"test_api_method__": "GET",
			"test_popup__": false,
			"test_response__": {},
			"test_error__": {},
			"show_headers": false,
		};
	},
	mounted(){
		this.load_apis();
		this.set_test_environments__();
	},
	methods: {
		set_test_environments__: function(){
			var e = [];
			for( var d in this.app__['settings']['domains'] ){
				e.push({
					"t": "custom",
					"u": this.app__['settings']['domains'][ d ]['url'],
					"d": this.app__['settings']['domains'][ d ]['domain'],
				});
			}
			if( 'cloud' in this.app__['settings'] ){
				if( this.app__['settings']['cloud'] ){
					var d = this.app__['settings']['cloud-subdomain'] + "." + this.app__['settings']['cloud-domain'];
					e.push({
						"t": "cloud",
						"u": "https://" + d + "/",
						"d": d,
					});
				}
			}
			if( 'alias' in this.app__['settings'] ){
				if( this.app__['settings']['alias'] ){
					var d = this.app__['settings']['alias-domain'];
					e.push({
						"t": "cloud-alias",
						"u": "https://" + d + "/",
						"d": d,
					});
				}
			}
			this.test_envs__ = e;
		},
		dotest_table: function(){

			this.terr = "";
			this.test_response__ = {};
			this.test_error__ = {};
			if( this.test_url__ == "" ){
				this.terr = "Select test environment";
				return false;
			}


			if( this.test_api_method__ == "POST" ){
				var multipart = false;
				if( 'content-type' in this.test_thing__ ){
					if( this.test_thing__['content-type']=="multipart/form-data" ){
						multipart = true;
					}
				}
				if( multipart ){
					var vdata = new FormData();
					for( var di in this.test_thing__['formdata'] ){
						if( this.test_thing__['formdata'][di]['type']=="file" ){
							if( 'files' in document.getElementById("form_"+di) ){
								if( document.getElementById("form_"+di).files.length ){
									vdata.append( di, document.getElementById("form_"+di).files[0] );
								}else{
									this.terr = "Select file for input " + di;return false;
								}
							}else{
								this.terr = "File input not found " + di;return false;
							}
						}else{
							vdata.append( di, this.test_thing__['formdata'][di]['value'] );
						}
					}
				}else{
					this.test_data__ = this.ace_editor.getValue();
					var vdata = {};
					try{
						vdata = JSON.parse( this.test_data__ );
					}catch(e){
						this.terr = "JSON format error";
						return false;
					}
				}
			}else{
				var v = this.test_data__;
				console.log( JSON.stringify(v) );
				var vquery = [];
				for( var i in v ){
					vquery.push( i + "=" + encodeURIComponent( v[ i ]['value'] ) );
				}
				console.log( JSON.stringify(vquery) );
				vquery = vquery.join("&");
			}
			var vh = {};
			if( this.access_token__ != "" ){
				vh = {"headers":{"Access-Key":this.access_token__}};
			}
			if( 'response-body' in this.test_thing__ ){
				if( typeof(this.test_thing__['response-body']) == "string" ){
					if( this.test_thing__['response-body'].match(/binary/i) ){
						vh['responseType'] = "blob";
					}
				}
			}

			this.tmsg = "Testing API ...";
			this.terr = "";
			if( this.test_api_method__ == "POST" ){
				axios.post( this.test_url__ + this.test_path__, vdata, vh, {
					//'responseType': "blob"
				} ).then(response=>{
					this.tmsg = "";
					if( response.status == 200 ){
						this.test_response__ = response;
						if( 'responseType' in vh ){
							this.convert_response();
						}
					}else{
						this.terr = "Response Error: " . response.status;
					}
				}).catch(error=>{
					this.tmsg = "";this.terr = "Error";
					console.log( error );
					this.test_error__ = error;
					if( 'responseType' in vh ){
						this.convert_response_error();
					}
				});
			}else{
				axios.get( this.test_url__ + this.test_path__ + "?"+vquery, vh ).then(response=>{
					this.tmsg = "";
					if( response.status == 200 ){
						this.test_response__ = response;
						if( 'responseType' in vh ){
							this.convert_response();
						}
					}else{
						this.terr = "Response Error: " . response.status;
					}
				}).catch(error=>{
					this.tmsg = "";this.terr = "Error";
					console.log( error );
					this.test_error__ = error;
					if( 'responseType' in vh ){
						this.convert_response_error();
					}
				});
			}
		},
		is_it_attachment: function(){
			//console.log( this.test_response__ );
			var f= false;
			for( var h in this.test_response__['headers'] ){
				console.log( h );
				if( h.match(/disposition/i) && this.test_response__['headers'][ h ].match(/attachment/i) ){
					//console.log("ok");
					f = true;
				}
				if( h.match(/content\-type/i) && this.test_response__['headers'][ h ].match(/octet/i) ){
					//console.log("ok2");
					f = true;
				}
			}
			return f;
			///2023-3.jpg
		},
		convert_response: function(){
			//return false;
			for( var h in this.test_response__['headers'] ){
				if( h.match(/content\-type/i) ){
					if( this.test_response__['headers'][ h ].match(/octet/) == null ){
						//this.test_response__['data'] = _arrayBufferToString( this.test_response__['data'] );
						console.log("Converted response: " );
						console.log( this.test_response__['data'] );
						this.test_thing__['response-body']
					}
				}
			}
		},
		convert_response_error: function(){
			//return false;
			for( var h in this.test_error__['response']['headers'] ){
				if( h.match(/content\-type/i) ){
					if( this.test_error__['response']['headers'][ h ].match(/octet/) == null ){
						this.test_error__['response']['data'] = _arrayBufferToString( this.test_error__['response']['data'] );
						console.log("Converted response: " );
						console.log( this.test_error__['response']['data'] );
					}
				}
			}
		},
		save_attachment: function(){
			var t= "application/unknown";
			var f= "unknown-file.file";
			var d = this.test_response__['data'];
			console.log( this.test_response__ );
			for( var h in this.test_response__['headers'] ){
				if( h.match(/content\-type/i) ){
					t = this.test_response__['headers'][ h ];
				}
				if( h.match(/disposition/i) && this.test_response__['headers'][ h ].match(/attachment/i) ){
					var m = this.test_response__['headers'][ h ].match(/filename\=\"(.*?)\"/i);
					if( m!=null ){ f = m[1];}
				}
			}
			console.log( f );
			var file = new Blob([d], {"type": t});
			var a = document.createElement("a"), url = URL.createObjectURL(file);
			a.href = url; a.download = f;
			document.body.appendChild(a);
			a.click();
			setTimeout(function() { document.body.removeChild(a); window.URL.revokeObjectURL(url); }, 0); 
		},
		generate_access_key: function(){
			this.kmsg = "Generating Temporary Key ...";
			this.kerr = "";
			axios.post("?", {
				"action": "generate_access_token",
				"type": this.test_type__,
				"thing_id": this.test_thing_id__,
				"db_id": this.test_db_id__,
				"api_type": this.test_api_type__,
			}).then(response=>{
				this.kmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.kmsg = "Key is generated";
								setTimeout(function(v){v.kmsg="";},5000,this);
								this.access_token__ = response.data['key'];
							}else{
								this.kerr = "Error: " + response.data['error'];
							}
						}else{
							this.kerr = "Incorrect response";
						}
					}else{
						this.kerr = "Incorrect response Type";
					}
				}else{
					this.kerr = "Response Error: " . response.status;
				}
			}).catch(error=>{
				this.kerr = "Error: " + error.message;
			});
		},
		toggle_td: function( ti, o ){
			if( this.apis['tables_dynamic'][ ti ]['show'] == o ){
				this.apis['tables_dynamic'][ ti ]['show'] = "";
			}else{
				this.apis['tables_dynamic'][ ti ]['show'] = o;
			}
		},
		toggle_object: function( ti, o ){
			if( this.apis['objects'][ ti ]['show'] == o ){
				this.apis['objects'][ ti ]['show'] = "";
			}else{
				this.apis['objects'][ ti ]['show'] = o;
			}
		},
		toggle_sapi: function( ti, o ){
			if( this.apis['storage'][ ti ]['show'] == o ){
				this.apis['storage'][ ti ]['show'] = "";
			}else{
				this.apis['storage'][ ti ]['show'] = o;
			}
		},
		toggle_t: function(db_id, ti, o ){
			if( this.apis['databases'][ db_id ]['tables'][ ti ]['show'] == o ){
				this.apis['databases'][ db_id ]['tables'][ ti ]['show'] = "";
			}else{
				this.apis['databases'][ db_id ]['tables'][ ti ]['show'] = o;
			}
		},
		show_test: function(t,ti,at){
			this.terr = "";
			this.test_response__ = {};
			this.test_error__ = {};
			this.test_type__ = t;
			this.test_thing_id__ = this.apis[ t ][ ti ]['_id'];
			this.test_api_type__ = at;
			this.test_api_method__ = "POST";
			// console.log( this.apis[ t ][ ti ][ at ] );
			this.test_path__ = this.apis['tables_dynamic'][ ti ]['path']+'';
			this.test_data__ = JSON.stringify( this.apis[ t ][ ti ][ at ], null, 4 );
			// console.log( this.test_data__ );
			this.test_popup__ = new bootstrap.Modal( document.getElementById('popup_test__') );
			this.test_popup__.show();
			setTimeout(this.init_ace, 500);
		},
		show_test_graph: function(t,ti,at){
			this.terr = "";
			this.test_response__ = {};
			this.test_error__ = {};
			this.test_type__ = t;
			this.test_thing_id__ = this.apis[ t ][ ti ]['_id'];
			this.test_api_type__ = at;
			this.test_api_method__ = "POST";
			// console.log( this.apis[ t ][ ti ][ at ] );
			this.test_path__ = this.apis['objects'][ ti ]['path']+'';
			this.test_data__ = JSON.stringify( this.apis[ t ][ ti ]['apis'][ at ]['post'], null, 4 );
			// console.log( this.test_data__ );
			this.test_popup__ = new bootstrap.Modal( document.getElementById('popup_test__') );
			this.test_popup__.show();
			setTimeout(this.init_ace, 500);
		},
		init_ace: function(){
			console.log("Ace initialized");
			//if( !this.ace_editor )
			{
				this.ace_editor = ace.edit("json_editor_block");
				this.ace_editor.session.setMode("ace/mode/json");
				this.ace_editor.setOptions({
					enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: true, 
				});
			}
			this.ace_editor.setValue( this.test_data__ );
		},
		show_test2: function(t,di,ti,at){
			this.terr = "";
			this.test_response__ = {};
			this.test_error__ = {};
			this.test_type__ = t;
			this.test_db_id__ = di;
			this.test_thing_id__ = this.apis[ 'databases' ][ di ]['tables'][ ti ]['_id'];
			this.test_thing__ = {};
			this.test_api_type__ = at;
			this.test_api_method__ = "POST";
			this.test_path__ = this.apis[ 'databases' ][ di ]['tables'][ ti ]['path']+'';
			this.test_data__ = JSON.stringify( this.apis[ 'databases' ][ di ]['tables'][ ti ][ at ], null, 4 );
			// console.log( this.test_data__ );
			this.test_popup__ = new bootstrap.Modal( document.getElementById('popup_test__') );
			this.test_popup__.show();
			setTimeout(this.init_ace, 500);
		},
		show_test3: function(t,ti){
			this.terr = "";
			this.test_response__ = {};
			this.test_error__ = {};
			this.test_type__ = t;
			this.test_db_id__ = "";
			this.test_thing_id__ = this.apis[ 'auth_apis' ][ ti ]['_id']+'';
			this.test_thing__ = {};
			this.test_api_type__ = "";
			this.test_api_method__ = "POST";
			this.test_path__ = this.apis[ 'auth_apis' ][ ti ]['path']+'';
			this.test_data__ = this.apis[ 'auth_apis' ][ ti ][ 'vpost' ];
			// console.log( this.test_data__ );
			this.test_popup__ = new bootstrap.Modal( document.getElementById('popup_test__') );
			this.test_popup__.show();
			setTimeout(this.init_ace, 500);
		},
		test_user_apis__: function(t,ti){
			this.terr = "";
			this.test_response__ = {};
			this.test_error__ = {};
			this.test_type__ = t;
			this.test_db_id__ = "";
			this.test_thing_id__ = this.apis[ 'apis' ][ ti ]['_id']+'';
			this.test_thing__ = {};
			this.test_api_type__ = "";
			this.test_api_method__ = this.apis[ 'apis' ][ ti ][ 'input-method' ];
			this.test_path__ = this.apis[ 'apis' ][ ti ]['path'].substr(1,1000)+this.apis[ 'apis' ][ ti ]['name'];
			if( this.test_api_method__ == "GET" ){
				this.test_data__ = this.apis[ 'apis' ][ ti ]['formdata'];
			}else{
				this.test_data__ = this.apis[ 'apis' ][ ti ][ 'vpost' ];
			}
			// console.log( this.test_data__ );
			this.test_popup__ = new bootstrap.Modal( document.getElementById('popup_test__') );
			this.test_popup__.show();
			if( this.test_api_method__ == "POST" ){
				setTimeout(this.init_ace, 500);
			}
		},
		show_test6: function(t,ti){
			this.terr = "";
			this.test_response__ = {};
			this.test_error__ = {};
			this.test_type__ = t;
			this.test_db_id__ = "";
			this.test_thing_id__ = this.apis[ 'captcha' ][ ti ]['_id']+'';
			this.test_thing__ = {};
			this.test_api_type__ = "";
			this.test_api_method__ = this.apis[ 'captcha' ][ ti ][ 'input-method' ];
			this.test_path__ = this.apis[ 'captcha' ][ ti ]['path'];
			this.test_data__ = this.apis[ 'captcha' ][ ti ][ 'vpost' ];
			// console.log( this.test_data__ );
			this.test_popup__ = new bootstrap.Modal( document.getElementById('popup_test__') );
			this.test_popup__.show();
			setTimeout(this.init_ace, 500);
		},
		show_test_dynamic: function(t,ti,action=""){
			this.terr = "";
			this.test_response__ = {};
			this.test_error__ = {};
			this.test_type__ = t;
			this.test_thing_id__ = this.apis[ t ][ ti ]['_id']+'';
			this.test_thing__ = this.apis[ t ][ ti ];
			this.test_api_method__ = this.apis[ t ][ ti ][ 'input-method' ];
			this.test_path__ = this.apis[ t ][ ti ]['path'];
			this.test_data__ = JSON.stringify( this.apis[ t ][ ti ]['payload'], null, 4 );
			// console.log( this.test_data__ );
			this.test_popup__ = new bootstrap.Modal( document.getElementById('popup_test__') );
			this.test_popup__.show();
			if( 'content-type' in this.test_thing__ ){
				if( this.test_thing__['content-type'] == "application/json" ){
					setTimeout(this.init_ace, 500);
				}
			}else{
				setTimeout(this.init_ace, 500);
			}
		},
		show_test_dynamic2: function(t,ti,action=""){
			if( t in this.apis == false ){
				alert(t + " not found in apis list");return;
			}
			if( ti in this.apis[ t ] == false ){
				alert(ti + " not found in apis list " + t );return;
			}
			if( 'apis' in this.apis[ t ][ ti ] == false ){
				alert("apis not found in apis list " + t );return;
			}
			if( action in this.apis[ t ][ ti ]['apis'] == false ){
				alert( action + " not found in apis list " + t + " : " + ti );return;
			}
			console.log( JSON.stringify(this.apis[ t ][ ti ]['apis'][ action ],null,4) );
			this.terr = "";
			this.test_response__ = {};
			this.test_error__ = {};
			this.test_type__ = t;
			this.test_thing_id__ = this.apis[ t ][ ti ]['_id']+'';
			this.test_thing__ = this.apis[ t ][ ti ]['apis'][ action ];
			this.test_api_method__ = this.apis[ t ][ ti ]['apis'][ action ][ 'input-method' ];
			this.test_path__ = this.apis[ t ][ ti ]['apis'][ action ]['path'];
			this.test_data__ = JSON.stringify( this.apis[ t ][ ti ]['apis'][ action ]['payload'], null, 4 );
			// console.log( this.test_data__ );
			this.test_popup__ = new bootstrap.Modal( document.getElementById('popup_test__') );
			this.test_popup__.show();
			if( 'content-type' in this.test_thing__ ){
				if( this.test_thing__['content-type'] == "application/json" ){
					setTimeout(this.init_ace, 500);
				}
			}else{
				setTimeout(this.init_ace, 500);
			}
		},
		select_test_environment__: function(){
			setTimeout(this.select_test_environment__2,200);
		},
		select_test_environment__2: function(){
			for( var i=0;i<this.test_envs__.length;i++ ){
				if( this.test_envs__[i]['d'] == this.test__['domain'] ){
					var tu = this.test_envs__[i]['u']
					this.test_url__ = tu;
					break;
				}
			}
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
				"event":"get_global_apis."+this.app_id,
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
			axios.post("?",{"action":"get_global_apis","app_id":this.app_id,"token":this.token}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.apis = response.data['apis'];
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
		
	}
}).mount("#app");

function str2ab(str) {
  var buf = new ArrayBuffer(str.length * 2); // 2 bytes for each char
  var bufView = new Uint16Array(buf);
  for (var i = 0, strLen = str.length; i < strLen; i++) {
    bufView[i] = str.charCodeAt(i);
  }
  return buf;
}

function ab2str(buf) {
  return String.fromCharCode.apply(null, new Uint16Array(buf));
}

function _arrayBufferToString( buffer ) {
    var binary = '';
    var bytes = new Uint8Array( buffer );
    var len = bytes.byteLength;
    for (var i = 0; i < len; i++) {
        binary += String.fromCharCode( bytes[ i ] );
    }
    return binary;
}

</script>