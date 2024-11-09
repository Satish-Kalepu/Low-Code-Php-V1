<style>
	.mongoid{ display:block; cursor:pointer; width:30px; }
	.mongoid:hover{ background-color:#eee; }
	.mongoid div{ display:none; }
	.mongoid:hover div{ display: block; position:absolute; background-color:white; box-shadow:2px 2px 5px #666; border:1px solid #999; padding:0px 10px; }
</style>

<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="check-circle-fill" viewBox="0 0 16 16">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
  <symbol id="info-fill" viewBox="0 0 16 16">
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
  </symbol>
  <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
</svg>

<div id="app" v-cloak >

	<div  class="leftbar"  >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: 40px; width:calc( 100% - 150px ); background-color: white; border-bottom:1px solid #ccc; " >
		<div style="padding: 5px 10px;" >
			<div>
				<h5 class="d-inline">{{ app__['app'] }} <span class="text-secondary">Settings</span></h5>
			</div>
		</div>
	</div>

	<div id="content_div" style="position: fixed;left:150px; top:90px; height: calc( 100% - 90px );width:calc( 100% - 150px ); overflow: auto; " >
		<div style="padding: 10px;" >

			<div class="alert alert-danger d-flex align-items-center" v-if="'settings' in app__==false" >
				<svg xmlns="http://www.w3.org/2000/svg" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" style="width:30px;" viewBox="0 0 16 16" role="img" aria-label="Warning:"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
				<div>Configure app settings to continue</div>
			</div>

			<div style="border: 1px solid #999; margin-bottom: 20px; " >
				<div style="background-color:#e8e8e8; padding: 10px; font-size:1.2rem;">Application</div>
				<div style="padding:10px;">
					<!-- <pre>{{ settings }}</pre> -->

					<div class="mb-2" >
						<label class="form-label">App name</label>
						<div><input type="text" spellcheck="false" class="form-control form-control-sm" v-model="edit_app['app']" ></div>
					</div>
					<div class="mb-2" >
						<label class="form-label">Description</label>
						<div><textarea spellcheck="false" class="form-control form-control-sm" v-model="edit_app['des']" ></textarea></div>
					</div>
					<div class="mb-2" v-if="app__['app']!=edit_app['app']||app__['des']!=edit_app['des']" >
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="UPDATE" v-on:click="save_name" ></div>
					</div>

					<div v-if="msg1" class="alert alert-primary" >{{ msg1 }}</div>
					<div v-if="err1" class="alert alert-danger" >{{ err1 }}</div>

				</div>
			</div>

			<div v-if="config_cloud_enabled" style="border: 1px solid #999; margin-bottom: 20px; " >
				<div style="background-color:#e8e8e8; padding: 5px 10px; font-size:1.2rem;">Cloud Hosting</div>
				<div style="padding:10px;">

					<div><label style="cursor: pointer;">Enable cloud hosting  <input type="checkbox" v-model="settings['cloud']" ></label></div>
					<template v-if="'cloud' in settings" >
						<template v-if="settings['cloud']" >

						<div class="input-group mb-3 mt-3">
						  <span class="input-group-text">https://</span>
						  <input type="text" spellcheck="false" class="form-control form-control-sm" placeholder="SubDomain"  v-model="settings['cloud-subdomain']"  style="max-width: 150px;" >
						  <span class="input-group-text">.</span>
						  <select class="form-select form-select-sm" placeholder="Server" v-model="settings['cloud-domain']"  style="max-width: 250px;" >
							<option v-for="d in cd" v-bind:value="d" >{{ d }}</option>
						  </select>
						  <span class="input-group-text">/</span>
						  <!-- <input type="text" class="form-control  form-control-sm" placeholder="Path" v-model="settings['cloud-enginepath']" style="max-width: 150px;" > -->
						</div>
						<div class="text-secondary"><a target="_blank" v-bind:href="getclouddomain()" >{{ getclouddomain() }}</a></div>

						<div>&nbsp;</div>
						<div><label style="cursor: pointer;">Use an alias name for above domain  <input type="checkbox" v-model="settings['alias']" ></label></div>
						<div v-if="settings['alias']" class="input-group mb-3 mt-3">
						  <span class="input-group-text">https://</span>
						  <input type="text" spellcheck="false" class="form-control form-control-sm" style="max-width: 250px;" placeholder="Alias domain" v-model="settings['alias-domain']" >
						  <span class="input-group-text">/</span>
						</div>

						</template>
					</template>

					<!-- <pre>{{ settings }}</pre> -->

					<div class="mb-2" >
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="UPDATE" v-on:click="app_save_cloud_settings" ></div>
					</div>

					<div v-if="msg2" class="alert alert-primary" >{{ msg2 }}</div>
					<div v-if="err2" class="alert alert-danger" >{{ err2 }}</div>

					<div class="alert alert-light" style="border-color:#999;" v-if="alias_saved" >
						<div style="display:flex;">
						<div><svg class="bi flex-shrink-0 me-2" style="width:20px; height:20px;" role="img" ><use xlink:href="#exclamation-triangle-fill"/></svg></div>
						<div>
							<div>Alias DNS: point your domain to engine endpoint by creating below dns entry</div>
							<table class="table table-bordered table-sm"><tbody>
							<tr>
								<td>Name</td><td>Type</td><td>Value</td>
							</tr>
							<tr>
								<td>{{ settings['alias-domain'] }}</td><td>CNAME</td><td>{{ alb_cname }}</td>
							</tr>
							</tbody></table>
							<div>Contact Admin for SSL support</div>
						</div>
						</div>
					</div>

				</div>
			</div>

			<div style="border: 1px solid #999; margin-bottom: 20px; " >
				<div style="background-color:#e8e8e8; padding: 5px 10px; font-size:1.2rem;">Custom Hosting</div>
				<div style="padding:10px;">
					<p><label style="cursor: pointer;">Enable custom hosting <input type="checkbox" v-model="settings['host']" ></label></p>

					<template v-if="settings['host']" >
					<div style="border: 1px solid #ccc; margin-bottom: 20px; " >
						<div style="background-color:#e8e8e8; padding: 5px 10px;">URLs Allowed</div>
						<div style="padding:10px;">
							<div class="small">Urls with path where engine is configured</div>
							<table class="table table-sm" >
							<tr v-for="dd,di in settings['domains']">
								<td><input spellcheck="false" type="text" class="form-control form-control-sm" v-model="dd['url']" ></td>
								<td width="50"><input type="button" value="X" class="btn btn-outline-dark btn-sm" v-on:click="delete_url(di)"></td>
							</tr>
							</table>
							<div><input type="button" value="Add Engine URL Endpoint"  class="btn btn-outline-dark btn-sm" v-on:click="add_domain"></div>
						</div>
					</div>

					<div style="border: 1px solid #ccc; margin-bottom: 20px; " >
						<div style="background-color:#e8e8e8; padding: 5px 10px;">Access Keys</div>
						<div style="padding:10px;">
							<div v-for="dd,di in settings['keys']" style=" border:1px solid #ccc; margin-bottom:10px; " >
								<div class="p-2" style="border-bottom:1px solid #ccc;" >
									<input type="button" spellcheck="false" class="btn btn-outline-dark btn-sm" style="float:right;" value="X" v-on:click="delete_key(di)" >
									<div>Key: {{ dd['key'] }}</div>
								</div>
								<div class="p-3">
								<div>IPs Allowed</div>
								<div v-for="ip,ipi in dd['ips_allowed']" style="display: flex; column-gap: 5px; padding:5px;" >
									<input type="text" spellcheck="false" class="form-control form-control-sm w-auto" v-model="ip['ip']" >
									<select class="form-select form-select-sm w-auto" v-model="ip['action']" >
										<option value="Allow" >Allow</option><option value="Reject" >Reject</option>
									</select>
									<input type="button" class="btn btn-outline-dark btn-sm" value="X" class="btn btn-danger btn-sm" v-on:click="delete_ip(di,ipi)">
								</div>
								<div><input type="button" class="btn btn-outline-dark btn-sm" value="Add IP Range" v-on:click="add_ip(di)" ></div>
								</div>
							</div>
							<div style="padding: 5px;"><input type="button" class="btn btn-outline-dark btn-sm" value="Create Access Key" v-on:click="add_key" ></div>
						</div>
					</div>
					</template>

					<div class="mb-2" >
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="UPDATE" v-on:click="app_save_custom_settings" ></div>
					</div>

					<div v-if="msg3" class="alert alert-primary" >{{ msg3 }}</div>
					<div v-if="err3" class="alert alert-danger" >{{ err3 }}</div>					

				</div>
			</div>

			<div style="border: 1px solid #999; margin-bottom: 20px; " >
				<div style="background-color:#e8e8e8; padding: 5px 10px; font-size:1.2rem;">Other Settings</div>
				<div style="padding:10px;">
					<p>Home page </p>
					<template v-if="'homepage' in settings" >
						<p>Type: <select class="form-select form-select-sm w-auto d-inline" v-model="settings['homepage']['t']">
							<option value="" >Select</option>
							<option value="file" >File</option>
							<option value="page" >Page</option>
							<option value="apisummary" >Api Summary</option>
						</select>
						<template v-if="'t' in settings['homepage']" >
							<template v-if="settings['homepage']['t']=='page'" >
								<select class="form-select form-select-sm w-auto d-inline" v-model="settings['homepage']['v']" >
									<option v-for="p in pages" v-bind:value="p['_id']+':'+p['version_id']" >{{ p['name'] }}</option>
								</select>
							</template>
							<template v-if="settings['homepage']['t']=='file'" >
								<select class="form-select form-select-sm w-auto d-inline" v-model="settings['homepage']['v']" >
									<option v-for="p in files" v-bind:value="p['_id']" >{{ p['path']+p['name'] }}</option>
								</select>
							</template>
						</template>
						</p>
					</template>
					<div class="mb-2" >
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="UPDATE" v-on:click="app_save_other_settings" ></div>
					</div>
					<div v-if="msg4" class="alert alert-primary" >{{ msg4 }}</div>
					<div v-if="err4" class="alert alert-danger" >{{ err4 }}</div>

				</div>
			</div>

			<template v-if="host_saved&&'host' in settings" >
			<div v-if="settings['host']===true" style="border: 1px solid #999; margin-bottom: 20px; " >
				<div style="background-color:#e8e8e8; padding: 5px 10px; font-size:1.2rem;">Engine</div>
				<div style="padding:10px;">

					<template v-if="enginep" >
						<p>Engine configuration file:</p>
						<div>{{ enginep }}</div>
						<pre style="width:98%; height: 150px;overflow: auto; padding: 10px; border: 1px solid #ccc;">{{ engined[0] }}</pre>

						<div v-if="default_app" style="color:blue;" >This app is the default app</div>
						<div v-else>
							<p style="color:red;">This app is not the default app</p>
							<p>You can update the configuration file to make the current app default.</p>
						</div>
					</template>
					<div v-else>Engine configuration file does not exist</div>

				</div>
			</div>
			</template>

			<div style="border: 1px solid #999; margin-bottom: 20px; " >
				<div style="background-color:#e8e8e8; padding: 5px 10px; font-size:1.2rem;">TaskScheduler Engine</div>
				<div style="padding:10px;">


						<div style="float:right;"><input type="button" class="btn btn-outline-dark btn-sm" value="View Log" v-on:click="tasks_view_log()" ></div>
						<p>TaskScheduler Worker daemon</p>

						<template v-if="is_scheduler_running()" >
							<p style="color:blue;" >Daemon is Running</p>
							<!-- <pre style="background-color:#333; color:white; padding:10px;overflow:auto;">{{ settings['tasks'] }}</pre> -->
							<div><input type="button" class="btn btn-outline-danger btn-sm" value="Stop Daemon" v-on:click="stop_background_job()" ></div>
						</template>
						<div v-else >
							<p style="color:red;">Daemon is not running</p>
							<p>Click here to auto start the daemon</p>
							<div><input type="button" class="btn btn-outline-dark btn-sm" value="START" v-on:click="start_background_job()" ></div>
							<div>This is a AdHoc script which can be stopped by any system event. This will not auto start when the system is rebooted or application configuration is updated.</div>
						</div>

						<p>Last updated at: {{ settings['daemon_run_last'] }}</p>

						<div style="margin:10px; border:1px solid #aaa;">
							<div style="background-color: #f8f8f8;padding:5px;">AutoStart</div>
							<div style="padding:5px;">
								<div>Create a cronjob in the system where you have installed your engine. </div>
								<pre style="background-color:#333; color:white; padding:10px;overflow:auto;">@reboot curl --location http://domain/enginepath/_api_service \
--header 'Access-Key: <?=$akey ?>' \
--header 'Content-type: application/json' \
--data '{
	"action": "start_taskscheduler",
	"app_id": "<?=$config_param1 ?>"
}'</pre>
							</div>
						</div>

				</div>
			</div>

			<div style="border: 1px solid #999; margin-bottom: 20px; " >
				<div style="background-color:#e8e8e8; padding: 5px 10px; font-size:1.2rem;">Other background jobs</div>
				<div style="padding:10px;">

					<table class="table table-bordered table-striped table-sm w-auto">
						<tbody>
							<tr>
								<td>Name</td>
								<td>Status</td>
								<td>Workers</td>
								<td>-</td>
							</tr>
							<tr v-for="d,dd in background_jobs"  >
								<td>{{ d['name'] }}</td>
								<td>{{ d['run'] }}</td>
								<td>{{ d['workers'] }}</td>
								<td><input type="button" class="btn btn-outline-dark btn-sm py-0" value="View Log" v-on:click="display_background_log(dd)" ></td>
							</tr>
						</tbody>
					</table>

					<p>&nbsp;</p>

				</div>
			</div>

			<p>&nbsp;</p>-<p>&nbsp;</p>

		</div>
	</div>


		<div class="modal fade" id="start_backgroundjob_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Start Background Job Scheduler</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
					<div class="modal-body" >
						<p>Select Execution Environment: </p>
						<p>
							<select v-model="qei" class="form-select form-select-sm w-auto" >
								<option value="-1" >Select environment</option>
								<template v-for="d,i in test_environments" ><option v-if="d['t']!='cloud-alias'" v-bind:value="i" >{{ d['u'] }}</option></template>
							</select>
						</p>
						<div><input type="button" value="Start Worker" class="btn btn-outline-dark btn-sm" v-on:click="start_background_job2" ></div>

						<div v-if="jmsg" class="alert alert-primary" >{{ jmsg }}</div>
						<div v-if="jerr" class="alert alert-danger" >{{ jerr }}</div>		      	

		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="internal_queue_log_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg modal-xl">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Scheduler Job Log</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

		      	<div style="height: 40px; display: flex; column-gap:10px;">
		      		<div>
		      			<div title="Refresh" v-on:click="load_internal_queue_log()"  class="btn btn-outline-dark btn-sm" ><i class="fa-solid fa-arrows-rotate"></i></div>
		      		</div>
		      		<div>
		      			<div style="display:flex; column-gap: 5px;">
		      				<span>Task ID: </span>
			      			<input type="text" class="form-control form-control-sm w-auto" v-model="queue_log_keyword" placeholder="Search Task">
			      			<input type="button" class="btn btn-outline-dark btn-sm" value="Search" v-on:click="load_internal_queue_log()">
			      			<input v-if="internal_log.length>=100" type="button" class="btn btn-outline-dark btn-sm" value="Next" v-on:click="load_internal_queue_next()">
		      			</div>
		      		</div>
		      		<div>
								<div v-if="qlmsg" class="alert alert-primary py-0" >{{ qlmsg }}</div>
								<div v-if="qlerr" class="alert alert-danger py-0" >{{ qlerr }}</div>
							</div>
						</div>

						<div style="overflow: auto; height: 500px;">
							<table class="table table-bordered table-striped table-sm w-auto" >
								<tbody>
									<tr style="position: sticky; top:0px; background-color: white;">
										<td>#</td>
										<td>Thread</td>
										<td>Date</td>
										<td>Event</td>
										<td>Message</td>
										<td>TaskId</td>
										<td>Info</td>
									</tr>
									<tr v-for="d,i in internal_log">
										<td><div class="mongoid" ><div>{{ d['_id'] }}</div><span>#</span></div></td>
										<td><span v-if="'tid' in d" >{{ d['tid'] }}</span></td>
										<td nowrap>{{ d['date'] }}</td>
										<td nowrap>{{ d['event'] }}</td>
										<td nowrap>{{ d['message'] }}</td>
										<td nowrap><span v-if="'task_id' in d" >{{ d['task_id'] }}</td>
										<td>
											 <template v-for="dd,ii in d" ><div v-if="ii!='message'&&ii!='task_id'&&ii!='tid'&&ii!='_id'&&ii!='event'&&ii!='date'&&ii!='m_i'" style="white-space: nowrap;" >{{ ii }}: {{ dd }}</div></template>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<!-- <pre>{{ internal_log }}</pre> -->

		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="background_job_log_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg modal-xl">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Background Job Log</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

		      	<p><b>{{ background_job_popup_title }}</b></p>

		      	<div style="height: 40px; display: flex; column-gap:10px;">
		      		<div>
		      			<div title="Refresh" v-on:click="load_background_job_log()"  class="btn btn-outline-dark btn-sm" ><i class="fa-solid fa-arrows-rotate"></i></div>
		      		</div>
		      		<div>
		      			<div style="display:flex; column-gap: 5px;">
		      				<span>Task ID: </span>
			      			<input type="text" class="form-control form-control-sm w-auto" v-model="queue_log_keyword" placeholder="Search Task">
			      			<input type="button" class="btn btn-outline-dark btn-sm" value="Search" v-on:click="load_background_job_log()">
			      			<input v-if="background_job_log.length>=100" type="button" class="btn btn-outline-dark btn-sm" value="Next" v-on:click="load_background_job_log_next()">
		      			</div>
		      		</div>
		      		<div>
								<div v-if="qlmsg" class="alert alert-primary py-0" >{{ qlmsg }}</div>
								<div v-if="qlerr" class="alert alert-danger py-0" >{{ qlerr }}</div>
							</div>
						</div>

						<div style="overflow: auto; height: 500px;">
							<table class="table table-bordered table-striped table-sm w-auto" >
								<tbody>
									<tr style="position: sticky; top:0px; background-color: white;">
										<td>#</td>
										<td>Thread</td>
										<td>Date</td>
										<td>Event</td>
										<td>Message</td>
										<td>TaskId</td>
										<td>Info</td>
									</tr>
									<tr v-for="d,i in background_job_log">
										<td><div class="mongoid" ><div>{{ d['_id'] }}</div><span>#</span></div></td>
										<td><span v-if="'tid' in d" >{{ d['tid'] }}</span></td>
										<td nowrap>{{ d['date'] }}</td>
										<td nowrap>{{ d['event'] }}</td>
										<td nowrap>{{ d['message'] }}</td>
										<td nowrap><span v-if="'task_id' in d" >{{ d['task_id'] }}</td>
										<td>
											 <template v-for="dd,ii in d" ><div v-if="ii!='message'&&ii!='task_id'&&ii!='tid'&&ii!='_id'&&ii!='event'&&ii!='date'&&ii!='m_i'" style="white-space: nowrap;" >{{ ii }}: {{ dd }}</div></template>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<!-- <pre>{{ internal_log }}</pre> -->

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
			edit_app: {"app":"", "des":""},
			config_cloud_enabled: <?=isset($config_global_apimaker['config_cloud_enabled'])?($config_global_apimaker['config_cloud_enabled']?"true":"false"):false ?>,
			cd: <?=isset($config_global_apimaker['config_cloud_domains'])?json_encode($config_global_apimaker['config_cloud_domains']):'[]' ?>,
			alb_cname: "<?=$config_global_apimaker['config_cloud_alb_cname'] ?>",
			msg1: "",err1: "",msg2: "",err2: "",msg3: "",err3: "",msg4: "",err4: "",jmsg: "",jerr: "",
			pages:[],files:[],
			enginep: "<?=$enginep ?>", 
			engined: <?=json_encode([$engined]) ?>,
			default_app: <?=$default_app?"true":"false" ?>,
			settings: <?=json_encode($settings) ?>,
			show_create_api: false,
			new_api: { "name": "", "des": "" },
			create_app_modal: false,
			test_environments: <?=json_encode($test_environments) ?>,
			start_queue_popup: false,	
			qlmsg: "", "qlerr":"",
			internal_queue_log_popup: false,
			internal_log: [],
			queue_log_keyword: "",
			qei: -1,
			token: "",
			custom_edited: false, cloud_edited: false, other_edited: false,
			alias_saved: false,host_saved: false,
			background_jobs: <?=json_encode($background_jobs) ?>,
			background_job_popup: false,
			background_job_set: {},
			background_job_log: [],
			background_job_popup_title: "",
		};
	},
	watch: {
		app__: {
			handler: function(){
				console.log("edited");
			}, deep: true, immediate: true,
		}
	},
	mounted(){
		this.edit_app = {
			"app": this.app__['app']+'',
			"des": this.app__['des']+''
		};
		if( 'cloud' in this.settings == false && this.config_cloud_enabled ){
			this.settings['cloud'] = false;
			this.settings['cloud-domain'] = "backendmaker.com";
			this.settings['cloud-subdomain'] = this.app__['app']+'';
			this.settings['cloud-enginepath'] = '';
		}
		if( 'host' in this.settings == false ){
			this.settings['host'] = false;
			this.settings['domains'] = [];
			this.settings['keys'] = [];
		}else{
			this.host_saved = true;
		}
		if( 'alias' in this.settings == false ){
			this.settings['alias'] = false;
			this.settings['alias-domain'] = "www.example.com";
		}else if( this.settings['alias'] === true ){
			this.alias_saved = true;
		}
		if( 'homepage' in this.settings == false ){
			this.settings['homepage'] = {
				"t":"page",
				"v":"",
			};
		}
		this.load_pages();
	},
	methods: {
		display_background_log: function(d){
			this.background_job_set = d;
			this.queue_log_keyword = "";
			this.background_job_popup_title = this.background_jobs[ d ]['name'];
			this.background_job_popup = new bootstrap.Modal( document.getElementById('background_job_log_modal') );
			this.background_job_popup.show();
			this.load_background_job_log();
		},
		load_background_job_log: function(){
			this.qlmsg = "Loading...";
			this.background_job_log = [];
			this.qlerr = "";
			axios.post("?", {
				"action":"settings_load_background_job_log", 
				"task_id": this.queue_log_keyword,
				"logtype": this.background_job_set,
			}).then(response=>{
				this.qlmsg = "";
				if( 'status' in response.data ){
					if( response.data['status']=="success"){
						this.background_job_log=response.data['data'];
					}else{
						this.qlerr = response.data['error'];
					}
				}else{
					this.qlerr = ( "Error: incorrect response" );
				}
			}).catch(error=>{
				this.qlerr = ("Error: "+error.message);
			});
		},
		load_background_job_log_next: function(){
			this.qlmsg = "Loading...";
			var last = this.background_job_log[ this.background_job_log.length-1 ]['_id'];
			this.background_job_log = [];
			this.qlerr = "";
			axios.post("?", {
				"action":"settings_load_background_job_log", 
				"task_id": this.queue_log_keyword,
				"logtype": this.background_job_set,
				"last": last
			}).then(response=>{
				this.qlmsg = "";
				if( 'status' in response.data ){
					if( response.data['status']=="success"){
						this.background_job_log=response.data['data'];
					}else{
						this.qlerr = response.data['error'];
					}
				}else{
					this.qlerr = ( "Error: incorrect response" );
				}
			}).catch(error=>{
				this.qlerr = ("Error: "+error.message);
			});
		},
		tasks_view_log: function(di){
			this.queue_log_keyword = "";
			this.internal_queue_log_popup = new bootstrap.Modal( document.getElementById('internal_queue_log_modal') );
			this.internal_queue_log_popup.show();
			this.load_internal_queue_log();
		},
		load_internal_queue_log: function(){
			this.qlmsg = "Loading...";
			this.internal_log = [];
			this.qlerr = "";
			axios.post("?", {
				"action":"settings_load_tasks_log", 
				"task_id": this.queue_log_keyword,
			}).then(response=>{
				this.qlmsg = "";
				if( 'status' in response.data ){
					if( response.data['status']=="success"){
						this.internal_log=response.data['data'];
					}else{
						this.qlerr = response.data['error'];
					}
				}else{
					this.qlerr = ( "Error: incorrect response" );
				}
			}).catch(error=>{
				this.qlerr = ("Error: "+error.message);
			});
		},
		load_internal_queue_next: function(){
			this.qlmsg = "Loading...";
			var last = this.internal_log[ this.internal_log.length-1 ]['_id'];
			this.internal_log = [];
			this.qlerr = "";
			axios.post("?", {
				"action":"settings_load_tasks_log", 
				"task_id": this.queue_log_keyword,
				"last": last
			}).then(response=>{
				this.qlmsg = "";
				if( 'status' in response.data ){
					if( response.data['status']=="success"){
						this.internal_log=response.data['data'];
					}else{
						this.qlerr = response.data['error'];
					}
				}else{
					this.qlerr = ( "Error: incorrect response" );
				}
			}).catch(error=>{
				this.qlerr = ("Error: "+error.message);
			});
		},
		is_scheduler_running: function(){
			if( 'tasks' in this.settings == false ){
				return false;
			}else if( 'run' in this.settings['tasks'] == false ){
				return false;
			}else if( this.settings['tasks']['run'] == false ){
				return false;
			}
			return this.settings['daemon_run_status'];
		},
		stop_background_job: function(){
			axios.post("?", {
				"action": "app_settings_stop_job",
			}).then(response=>{
				this.jmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								alert("Job stopped successfully");
								this.settings['tasks']['run'] = false;
							}else{
								alert( response.data['error'] );
							}
						}else{
							alert( "Incorrect response" );
						}
					}else{
						alert( "Incorrect response Type" );
					}
				}else{
					alert( "Response Error: " . response.status );
				}
			});
		},
		start_background_job: function(){
			this.start_queue_popup = new bootstrap.Modal( document.getElementById('start_backgroundjob_modal') );
			this.start_queue_popup.show();
		},
		start_background_job2: function(){
			this.jmsg = "";this.jerr = "";
			this.jmsg = "Connecting...";
			axios.post("?", {
				"action": "app_settings_start_job",
				"env": this.test_environments[ Number(this.qei) ],
			}).then(response=>{
				this.jmsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.jmsg = "job started successfully";
								this.settings['tasks']['run'] = true;
								this.settings['tasks']['env'] = this.test_environments[ Number(this.qei) ];
							}else{
								this.jerr = response.data['error'];
							}
						}else{
							this.jerr = "Incorrect response";
						}
					}else{
						this.jerr = "Incorrect response Type";
					}
				}else{
					this.jerr = "Response Error: " . response.status;
				}
			});
		},
		getclouddomain: function(){
			if( this.config_cloud_enabled){
				return 'https://'+this.settings['cloud-subdomain'] + '.' + this.settings['cloud-domain'] +'/'+ (this.settings['cloud-enginepath']!=''?this.settings['cloud-enginepath']+'/':'');
			}else{
				return "Disabled";
			}
		},
		load_pages: function(){
			axios.post("?", {
				"action":"get_token",
				"event":"getpages."+this.app_id,
				"expire":2
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.load_pages2();
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
		load_pages2: function(){
			axios.post("?",{"action":"settings_load_pages","app_id":this.app_id,"token":this.token}).then(response=>{
				this.pages = response.data['pages'];
				this.files = response.data['files'];
			})
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
		add_domain: function(){
			if( 'domains' in this.settings == false ){
				this.settings['domains'] = [];
			}
			this.settings['domains'].push({
				"domain": "example.com",
				"url": "https://www.example.com/engine/"
			});
		},
		delete_url: function(vi){
			if( this.settings['domains'].length <= 1 ){
				alert("At lease one engine endpoint is required for the application serve.");
			}else if( confirm("Are you sure to delete URL?\nPlease make sure that your existing applications are not disturbed!" ) ){
				this.settings['domains'].splice(vi,1);
			}
		},
		delete_key: function(vi){
			if( this.settings['keys'].length <= 1 ){
				alert("At lease one access key is required for application to work!\nIt is always suggested to generate another key before deleting current key");
			}else if( confirm("Are you sure to delete the Access Key?\nAny existing applications being configured with the given will fail to work!\nNote: There is no going back once deleted!" ) ){
				this.settings['keys'].splice(vi,1);
			}
		},
		delete_ip: function(di,ipi){
			if( this.settings['keys'][ di ][ 'ips_allowed' ].length <= 1 ){
				alert("At lease one ip range is required for application to work!");
			}else{
				this.settings['keys'][ di ][ 'ips_allowed' ].splice(ipi,1);
			}
		},
		add_key: function(){
			if( 'keys' in this.settings == false ){
				this.settings['keys'] = [];
			}else if( this.settings['keys'] == null || typeof(this.settings['keys']) != "object" ){
				this.settings['keys'] = [];
			}
			axios.post("?", {
				"action": "get_new_key",
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.settings['keys'].push({
									"key": response.data['key'],
									"ips_allowed": [
										{"ip": "0.0.0.0/0", "action":"Allow"}
									]
								});
							}else{
								this.err = response.data['error'];
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
		add_ip: function( ki ){
			this.settings['keys'][ ki ]['ips_allowed'].push({
				"ip": "0.0.0.0/0"
			});
		},
		save_name: function(){
			this.edit_app['app'] = this.edit_app['app'].trim().toLowerCase().replace(/\W/g, "-").replace(/[\-]{2,10}/g, "-");
			while( this.edit_app['app'].substr( this.edit_app['app'].length-1, 1 ) == "-" ){
				this.edit_app['app'] = this.edit_app['app'].substr( 0, this.edit_app['app'].length-1 );
			}
			while( this.edit_app['app'].substr(0,1) == "-" ){
				this.edit_app['app'] = this.edit_app['app'].substr( 1, 999 );
			}
			if( this.edit_app['app'].match(/^[a-z][a-z0-9\-]{3,25}$/) == null ){
				this.err1 = "App name should be simple. no special chars";return false;
			}
			if( this.edit_app['app'].match(/^[A-Za-z0-9\.\,\-\ \_\(\)\[\]\ \@\#\!\&\r\n\t]{4,50}$/) == null ){
				this.err1 = "Description min 5 chars, max 50";return false;
			}
			this.msg1 = "Loading...";
			this.err1 = "";
			axios.post("?", {
				"action":"get_token",
				"event":"app_update."+this.app_id,
				"expire":2
			}).then(response=>{
				this.msg1 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.save_name2();
								}
							}else{
								alert("Token error: " + response.dat['data']);
								this.err1 = "Token Error: " + response.data['data'];
							}
						}else{
							this.err1 = "Incorrect response";
						}
					}else{
						this.err1 = "Incorrect response Type";
					}
				}else{
					this.err1 = "Response Error: " . response.status;
				}
			});
		},
		save_name2: function(){
			this.err1 = "";
			this.msg1 = "Saving...";
			axios.post("?", {
				"action": "app_update_name", 
				"app": this.edit_app,
				"token": this.token
			}).then(response=>{
				this.msg1 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg1 = "Saved Successfully";
								setTimeout(function(v){v.msg1= '';},5000,this);
							}else{
								this.err1 = response.data['error'];
							}
						}else{
							this.err1 = "Incorrect response";
						}
					}else{
						this.err1 = "Incorrect response Type";
					}
				}else{
					this.err1 = "Response Error: " . response.status;
				}
			});
		},
		app_save_cloud_settings: function(){
			if( this.config_cloud_enabled ){
				if( 'alias' in this.settings ){
					if( this.settings['alias'] ){
						if( this.settings['alias-domain'].match(/^[a-z0-9\-\_\.]{2,50}$/i) == null ){
							alert("Cloud alias domain invalid");return false;
						}
					}
				}
				if( 'cloud' in this.settings ){
					if( this.settings['cloud'] ){
						if( this.settings['cloud-subdomain'].match(/^[a-z0-9\-\_\.]{2,50}$/i) == null ){
							alert("Cloud sub domain invalid");return false;
						}
						if( this.settings['cloud-enginepath'] != "" ){
						if( this.settings['cloud-enginepath'].match(/^[a-z0-9\-\_\.]{2,50}$/i) == null ){
							alert("Cloud sub domain should be plain text without spaces\n\nEngine path is not mandatory");return false;
						}
						}
					}
				}

				this.msg2 = "Loading...";
				this.err2 = "";
				axios.post("?", {
					"action":"get_token",
					"event":"cloud_settings."+this.app_id,
					"expire":2
				}).then(response=>{
					this.msg2 = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.token = response.data['token'];
									if( this.is_token_ok(this.token) ){
										this.app_save_cloud_settings2();
									}
								}else{
									alert("Token error: " + response.dat['data']);
									this.err2 = "Token Error: " + response.data['data'];
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
			}else{
				alert("Disabled");
			}
		},
		app_save_cloud_settings2: function(){
			this.err2 = "";
			this.msg2 = "Saving...";
			axios.post("?", {
				"action": "app_save_cloud_settings", 
				"settings": this.settings,
				"token": this.token
			}).then(response=>{
				this.msg2 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg2 = "Saved Successfully";
								if( this.settings['alias'] == true ){
									this.alias_saved = true;
								}else{
									this.alias_saved = false;
								}
								setTimeout(function(v){v.msg2= '';},5000,this);
							}else{
								this.err2 = response.data['error'];
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
		},
		app_save_custom_settings: function(){
			this.err3 = "";
			if( 'domains' in this.settings == false ){
				this.err3 = ("Something wrong");return;
			}
			if( this.settings['domains'] == null || typeof(this.settings['domains']) != "object" ){
				this.err3 = ("Something wrong");return;
			}
			if( this.settings['domains'].length == 0 ){
				this.err3 = ("Need hosting url");return;
			}else{
				for(var i=0;i<this.settings['domains'].length;i++){
					try{ var s = new URL(this.settings['domains'][i]['url']) }catch(e){
						this.err3 = ("Incorrect URL");return;
					}
				}
			}
			if( 'keys' in this.settings == false ){
				this.err3 = ("Something wrong");return;
			}
			if( this.settings['keys'] == null || typeof(this.settings['keys']) != "object" ){
				this.err3 = ("Something wrong");return;
			}
			if( this.settings['keys'].length == 0 ){
				this.err3 = ("Need Key for hosting");return;
			}else{
				for(var i=0;i<this.settings['keys'].length;i++){
					for(var j=0;j<this.settings['keys'][i]['ips_allowed'].length;j++){
						if( this.settings['keys'][i]['ips_allowed'][j]['ip'] != "*" && this.settings['keys'][i]['ips_allowed'][j]['ip'] != "0.0.0.0/0" ){
							if( this.settings['keys'][i]['ips_allowed'][j]['ip'].match(/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/(0|8|16|24|32)$/) ==null ){
								this.err3 = ("Incorrect IP format");return;
							}
						}
					}
				}
			}
			this.msg3 = "Loading...";
			axios.post("?", {
				"action":"get_token",
				"event":"cloud_settings."+this.app_id,
				"expire":2
			}).then(response=>{
				this.msg3 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.app_save_custom_settings2();
								}
							}else{
								alert("Token error: " + response.dat['data']);
								this.err3 = "Token Error: " + response.data['data'];
							}
						}else{
							this.err3 = "Incorrect response";
						}
					}else{
						this.err3 = "Incorrect response Type";
					}
				}else{
					this.err3 = "Response Error: " . response.status;
				}
			});
		},
		app_save_custom_settings2: function(){
			this.err3 = "";
			this.msg3 = "Saving...";
			axios.post("?", {
				"action": "app_save_custom_settings", 
				"settings": this.settings,
				"token": this.token
			}).then(response=>{
				this.msg3 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg3 = "Saved Successfully";
								if( this.settings['host'] === true ){
									this.host_saved = true;
								}else{
									this.host_saved = false;
								}
								setTimeout(function(v){v.msg3= '';},5000,this);
							}else{
								this.err3 = response.data['error'];
							}
						}else{
							this.err3 = "Incorrect response";
						}
					}else{
						this.err3 = "Incorrect response Type";
					}
				}else{
					this.err3 = "Response Error: " . response.status;
				}
			});
		},
		app_save_other_settings: function(){
			this.msg4 = "Loading...";
			this.err4 = "";
			axios.post("?", {
				"action":"get_token",
				"event":"cloud_settings."+this.app_id,
				"expire":2
			}).then(response=>{
				this.msg4 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.app_save_other_settings2();
								}
							}else{
								alert("Token error: " + response.dat['data']);
								this.err4 = "Token Error: " + response.data['data'];
							}
						}else{
							this.err4 = "Incorrect response";
						}
					}else{
						this.err4 = "Incorrect response Type";
					}
				}else{
					this.err4 = "Response Error: " . response.status;
				}
			});
		},
		app_save_other_settings2: function(){
			this.err4 = "";
			this.msg4 = "Saving...";
			axios.post("?", {
				"action": "app_save_other_settings", 
				"homepage": this.settings['homepage'],
				"token": this.token
			}).then(response=>{
				this.msg4 = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.msg4 = "Saved Successfully";
								setTimeout(function(v){v.msg4= '';},5000,this);
							}else{
								this.err4 = response.data['error'];
							}
						}else{
							this.err4 = "Incorrect response";
						}
					}else{
						this.err4 = "Incorrect response Type";
					}
				}else{
					this.err4 = "Response Error: " . response.status;
				}
			});
		},
	}
}).mount("#app");
</script>