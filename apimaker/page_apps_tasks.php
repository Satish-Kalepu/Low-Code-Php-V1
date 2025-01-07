<style>
	.mongoid{ display:block; cursor:pointer; width:30px; }
	.mongoid:hover{ background-color:#eee; }
	.mongoid div{ display:none; }
	.mongoid:hover div{ display: block; position:absolute; background-color:white; box-shadow:2px 2px 5px #666; border:1px solid #999; padding:0px 10px; }
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

			<div class="btn btn-sm btn-outline-dark float-end" v-on:click="show_configure()" >Configure</div>
			<div class="h3 mb-3">Tasks &amp; Queues</div>

			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a v-bind:class="{'nav-link':true,'active':tab=='queue'}" v-on:click="open_link('queue')" href="#">Queues</a>
				</li>
				<li class="nav-item">
					<a v-bind:class="{'nav-link':true,'active':tab=='active'}" v-on:click="open_link('active')"  href="#">Active Tasks</a>
				</li>
				<li class="nav-item">
					<a v-bind:class="{'nav-link':true,'active':tab=='bg'}" v-on:click="open_link('bg')" href="#">Background Jobs</a>
				</li>
				<li class="nav-item">
					<a v-bind:class="{'nav-link':true,'active':tab=='cron'}" v-on:click="open_link('cron')" >Cron Jobs</a>
				</li>
			</ul>
			<div>&nbsp;</div>
			
			<div style="position:relative;overflow: auto; height: calc( 100% - 160px );">

				<div v-if="fmsg" class="alert alert-primary" >{{ fmsg }}</div>
				<div v-if="ferr" class="alert alert-danger" >{{ ferr }}</div>
				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

				<div v-if="tab=='queue'" >

					<div style="border-bottom:1px solid #ccc;line-height: 30px; padding:5px; margin-bottom: 10px; background-color:#f0f0f0;">Internal Queues</div>

					<table class="table table-bordered table-sm w-auto" >
						<tr>
							<td>#<td>Topic</td><td>Function</td><td>Processor</td>
							<td>Queue</td><td>Success</td><td>Fail</td>
							<td>Workers</td>
							<td>Action</td><td>&nbsp;</td>
						</tr>
						<tr v-for="d,di in settings['internal']">
							<td><div class="vid">#<pre class="vid">{{d['_id']}}</pre></div></td>
							<td nowrap>{{ d['topic'] }}</td>
							<td nowrap><a v-bind:href="path+'functions/'+d['fn_id']+'/'+d['fn_vid']" >{{ d['fn'] }}</a></td>
							<td nowrap>{{ d['type']=='s'?'Single Thread':'Multi Threaded' }}</td>
							<td nowrap align="center"><div style="min-width: 50px; display: inline-block;">{{ d['queue'] }} </div> <div title="Click to Delete Queue" class="btn btn-outline-dark btn-sm me-2" v-on:click="flush_queue(di)" ><i class="fa-solid fa-trash"></i></div></td>
							<td nowrap align="center" class="text-success"><span v-if="'processed' in d" >{{ d['success'] }}</span></td>
							<td nowrap align="center" class="text-danger"><span v-if="'processed' in d" >{{ d['fail'] }}</span></td>
							<td nowrap align="center" class="text-danger"><span v-if="'workers' in d" ><span v-if="typeof(d['workers'])=='object'" >{{ Object.keys(d['workers']).length }}</span></span></td>
							<td nowrap>
								<div v-if="'started' in d==false" title="Click to Start Worker nodes" class="btn btn-outline-success btn-sm me-2" v-on:click="start_internal_queue(di)" ><i class="fa-solid fa-play"></i></div>
								<div v-else title="Click to Stop Worker nodes" class="btn btn-outline-danger btn-sm me-2" v-on:click="pause_queue(di)" ><i class="fa-solid fa-pause"></i></div>
								<div title="Click to view log" class="btn btn-outline-dark btn-sm me-2" v-on:click="view_log(di)" ><i class="fa-solid fa-eye"></i></div>
							</td>
							<td nowrap>
								<div title="Click to Edit" class="btn btn-outline-dark btn-sm me-2" v-on:click="edit_internal_queue(di)" ><i class="fa-solid fa-edit"></i></div>
								<div title="Click to delete queue" class="btn btn-outline-danger btn-sm me-2" v-on:click="delete_internal_queue(di)"  ><i class="fa-solid fa-trash"></i></div>
							</td>
						</tr>
					</table>
					<p><div class="btn btn-outline-dark btn-sm" v-on:click="show_internal_add" >Add Queue</div></p>

					<div style="border-bottom:1px solid #ccc;line-height: 30px; padding:5px; margin-bottom: 10px; background-color:#f0f0f0;">External Queues</div>

					<table class="table table-bordered table-sm w-auto" >
						<tr>
							<td>#<td>Topic</td><td>System</td><td>Function</td><td>Processor</td>
							<td>Queue</td><td>Success</td><td>Fail</td>
							<td>Workers</td>
							<td>Action</td><td>&nbsp;</td>
						</tr>
						<tr v-for="d,di in settings['external']">
							<td><div class="vid">#<pre class="vid">{{d['_id']}}</pre></div></td>
							<td nowrap>{{ d['topic'] }}</td>
							<td nowrap>{{ d['system'] }}</td>
							<td nowrap><a v-bind:href="path+'functions/'+d['fn_id']+'/'+d['fn_vid']" >{{ d['fn'] }}</a></td>
							<td nowrap>{{ d['type']=='s'?'Single Thread':'Multi Threaded' }}</td>
							<td nowrap align="center"><div style="min-width: 50px; display: inline-block;">{{ d['queue'] }} </div> <div title="Click to Delete Queue" class="btn btn-outline-dark btn-sm me-2" v-on:click="flush_queue(di)" ><i class="fa-solid fa-trash"></i></div></td>
							<td nowrap align="center" class="text-success"><span v-if="'processed' in d" >{{ d['success'] }}</span></td>
							<td nowrap align="center" class="text-danger"><span v-if="'processed' in d" >{{ d['fail'] }}</span></td>
							<td nowrap align="center" class="text-danger"><span v-if="'workers' in d" ><span v-if="typeof(d['workers'])=='object'" >{{ Object.keys(d['workers']).length }}</span></span></td>
							<td nowrap>
								<div v-if="'started' in d==false" title="Click to Start Worker nodes" class="btn btn-outline-success btn-sm me-2" v-on:click="start_internal_queue(di)" ><i class="fa-solid fa-play"></i></div>
								<div v-else title="Click to Stop Worker nodes" class="btn btn-outline-danger btn-sm me-2" v-on:click="pause_queue(di)" ><i class="fa-solid fa-pause"></i></div>
								<div title="Click to view log" class="btn btn-outline-dark btn-sm me-2" v-on:click="view_log(di)" ><i class="fa-solid fa-eye"></i></div>
							</td>
							<td nowrap>
								<div title="Click to Edit" class="btn btn-outline-dark btn-sm me-2" v-on:click="edit_internal_queue(di)" ><i class="fa-solid fa-edit"></i></div>
								<div title="Click to delete queue" class="btn btn-outline-danger btn-sm me-2" v-on:click="delete_internal_queue(di)"  ><i class="fa-solid fa-trash"></i></div>
							</td>
						</tr>
					</table>

					<p><div class="btn btn-outline-dark btn-sm" v-on:click="show_external_add" >Add Queue</div></p>

				</div>
				<div v-if="tab=='cron'" >
					<div style="border-bottom:1px solid #ccc;line-height: 30px; padding:5px; margin-bottom: 10px;  background-color:#f0f0f0;">CronJobs</div>

					<table class="table table-bordered table-sm w-auto" >
						<tr>
							<td>#</td>
							<td>Des</td>
							<td>Function</td>
							<td>Schedule</td>
							<td>-</td>
							<td>-</td>
						</tr>
						<tr v-for="d,di in cronjobs" >
							<td><div class="vid">#<pre class="vid">{{ d['_id'] }}</pre></div></td>
							<td>{{ d['des'] }}</td>
							<td nowrap><a v-if="'fn' in d" v-bind:href="path+'functions/'+d['fn_id']+'/'+d['fn_vid']" >{{ d['fn'] }}</a></td>
							<td>{{ d['type'] }} {{ find_schedule(d) }}</td>
							<td nowrap>
								<div v-if="d['active']==false" title="Click to Activate cron" class="btn btn-outline-success btn-sm me-2" v-on:click="cron_activate(di)" ><i class="fa-solid fa-play"></i></div>
								<div v-else title="Click to Deactivate Cron" class="btn btn-outline-danger btn-sm me-2" v-on:click="cron_deactivate(di)" ><i class="fa-solid fa-pause"></i></div>
								<div title="Click to view log" class="btn btn-outline-dark btn-sm me-2" v-on:click="cron_view_log(d['_id'])" ><i class="fa-solid fa-eye"></i></div>
							</td>
							<td nowrap>
								<div title="Click to Edit" class="btn btn-outline-dark btn-sm me-2" v-on:click="edit_cronjob(di)" ><i class="fa-solid fa-edit"></i></div>
								<div title="Click to delete cron" class="btn btn-outline-danger btn-sm me-2" v-on:click="delete_cronjob(di)"  ><i class="fa-solid fa-trash"></i></div>
							</td>
						</tr>
					</table>
					<p><div class="btn btn-outline-dark btn-sm" v-on:click="show_cron_edit('new')" >Add Cron</div></p>
				</div>

				<div v-if="tab=='bg'" >
					<div style="border-bottom:1px solid #ccc;line-height: 30px; padding:5px; margin-bottom: 10px; background-color:#f0f0f0;">Background Jobs</div>
					<table class="table table-bordered table-sm w-auto" >
						<tr>
							<td>#</td>
							<td>Date</td>
							<td>Source</td>
							<td>Function</td>
							<td>Status</td>
							<td>Dur</td>
							<td>Details</td>
						</tr>
						<tr v-for="d,di in bgtasks" >
							<td><div class="vid">#<pre class="vid">{{ d['_id'] }}</pre></div></td>
							<td nowrap>{{ d['start'] }}</td>
							<td nowrap>
								<span v-if="'source' in d" >
									<span v-if="d['source']['type']=='cron'" >
										Cron: <a v-if="'id' in d['source']"    v-bind:href="path+'tasks/cron/'+d['source']['id']" >{{ d['source']['des'] }}</a>
									</span>
									<span v-else-if="d['source']['type']=='fn'" >
										<span v-if="'fn_id' in d['source']&&'des' in d['source']" >
											Function: <a v-if="'fn_id' in d['source']"  v-bind:href="path+'functions/'+d['source']['fn_id']+'/'+d['source']['fn_vid']" >{{ d['source']['des'] }}</a>
										</span>
										<span v-else>Function</span>
									</span>
									<span v-else-if="d['source']['type']=='api'" >
										<span v-if="'api_id' in d['source']&&'des' in d['source']" >
											API: <a v-if="'api_id' in d['source']"  v-bind:href="path+'apis/'+d['source']['api_id']+'/'+d['source']['api_vid']" >{{ d['source']['des'] }}</a>
										</span>
										<span v-else>API</span>
									</span>
									<span v-else >Unknown</span>
								</span>
								<span v-else >Unknown</span>
							</td>
							<td nowrap>
								<template v-if="'fn' in d" >
									<template v-if="typeof(d['fn'])=='object'" >
										<a v-if="'fn_id' in d['fn']" v-bind:href="path+'functions/'+d['fn']['fn_id']+'/'+d['fn']['fn_vid']" >{{ d['fn']['fn'] }}</a>
									</template>
								</template>
								<span v-else>{{ d['fn']['fn_vid'] }}</span>
							</td>
							<td>{{ d['status'] }}</td>
							<td><span v-if="'time' in d" >{{ d['time'].toFixed(3) }}</span></td>
							<td>
								<template v-for="vd,vi in d" >
									<div v-if="vi!='status'&&vi!='source'&&vi!='cron'&&vi!='fn'&&vi!='_id'&&vi!='fn'&&vi!='fn_id'&&vi!='fn_vid'&&vi!='start'&&vi!='status'&&vi!='app_id'&&vi!='time'" >
										<div>{{ vi }}</div>
										<pre>{{ vd }}</pre>
									</div>
								</template>
							</td>
						</tr>
					</table>
				</div>

			</div>

		</div>
	</div>


		<div class="modal fade" id="settings_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Settings</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="start_queue_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Start Internal Task Queue Worker</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >
		      	<template v-if="internal_queue_index>-1" >
					<p>Select Execution Environment: </p>
					<p><select v-model="qei" class="form-select form-select-sm w-auto" >
						<option value="-1" >Select environment</option>
						<template v-for="d,i in test_environments" ><option v-if="d['t']!='cloud-alias'" v-bind:value="i" >{{ d['u'] }}</option></template>
					</select></p>
					<div v-if="settings['internal'][ internal_queue_index ]['type']=='s'">
						<p>Single worker mode</p>
						<input type="button" value="Start Worker" class="btn btn-outline-dark btn-sm" v-on:click="start_internal_queue2('single')" >
					</div>
					<div v-else-if="settings['internal'][ internal_queue_index ]['type']=='m'">
						<p>Multi worker mode</p>
						<input type="button" value="Start Single Worker" class="btn btn-outline-dark btn-sm me-3" v-on:click="start_internal_queue2('single')" >
						<input type="button" value="Start All Workers" class="btn btn-outline-dark btn-sm" v-on:click="start_internal_queue2('all')" >
					</div>
		      	</template>

				<div v-if="smsg" class="alert alert-primary" >{{ smsg }}</div>
				<div v-if="serr" class="alert alert-danger" >{{ serr }}</div>		      	

		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="internal_queue_log_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg modal-xl">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Queue Log</h5>
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
								<td>TaskId</td>
								<td>Info</td>
							</tr>
							<tr v-for="d,i in internal_log">
								<td><div class="mongoid" ><div>{{ d['_id'] }}</div><span>#</span></div></td>
								<td><span v-if="'tid' in d" >{{ d['tid'] }}</span></td>
								<td nowrap>{{ d['date'] }}</td>
								<td nowrap>{{ d['event'] }}</td>
								<td nowrap><span v-if="'task_id' in d" >{{ d['task_id'] }}</td>
								<td>
									 <template v-for="dd,ii in d" ><div v-if="ii!='task_id'&&ii!='tid'&&ii!='_id'&&ii!='event'&&ii!='date'&&ii!='m_i'" style="white-space: nowrap;" >{{ ii }}: {{ dd }}</div></template>
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

		<div class="modal fade" id="internal_queue" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Save Queue</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

				<table class="table table-bordered table-sm">
					<tr>
						<td>Topic</td>
						<td><input type="text" v-model="new_queue['topic']" class="form-control form-control-sm" placeholder="topicname"></td>
					</tr>
					<tr>
						<td>Type</td>
						<td>
							<select v-model="new_queue['type']" class="form-select form-select-sm w-auto" >
								<option value="s">Single Thread</option>
								<option value="m">Multi Threaded</option>
							</select>
							<div class="text-secondary">Single thread serve as FIFO (first in first out). Multi threaded serve in best effort ordering in separate processes</div>
						</td>
					</tr>
					<tr v-if="new_queue['type']=='m'">
						<td>Threads</td>
						<td><input type="number" v-model="new_queue['con']" class="form-control form-control-sm w-auto d-inline" > Consumers<div class="text-secondary" >Max 5 threads</div></td>
					</tr>
					<tr>
						<td>Function</td>
						<td>
							<select v-model="new_queue['fn_id']" class="form-select form-select-sm w-auto" v-on:change="internal_selected_function()" >
								<option value="">Select function</option>
								<option v-for="v,i in functions" v-bind:value="v['_id']" >{{ v['name'] }}</option>
								<option v-if="new_queue['fn_id']!=''" v-bind:value="new_queue['fn_id']" >{{ new_queue['fn'] }}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td nowrap>Timeout</td>
						<td><input type="number" v-model="new_queue['wait']" class="form-control form-control-sm w-auto d-inline" > Seconds <div class="text-secondary" >Execution timeout for each item. Max 60 seconds</div></td>
					</tr>
					<tr>
						<td nowrap>Retry</td>
						<td><input type="number" v-model="new_queue['retry']" class="form-control form-control-sm w-auto d-inline" ><div class="text-secondary" >Retry on fail. max 3</div></td>
					</tr>
					<tr>
						<td nowrap>Log Retention</td>
						<td><input type="number" v-model="new_queue['ret']" class="form-control form-control-sm w-auto d-inline" > Days <div class="text-secondary" >Max 5 days</div></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="button" class="btn btn-outline-dark btn-sm" value="Save Queue" v-on:click="save_queue" ></td>
					</tr>
				</table>
				<div v-if="'_id' in new_queue">Restart Queue for picking up new changes</div>
				<div v-if="ipmsg" class="alert alert-primary" >{{ ipmsg }}</div>
				<div v-if="iperr" class="alert alert-danger" >{{ iperr }}</div>
				<!-- <pre>{{ new_queue }}</pre> -->

		      </div>
		    </div>
		  </div>
		</div>



		<div class="modal fade" id="start_queue_external_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Start External Task Queue Worker</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >
		      	<template v-if="external_queue_index>-1" >
					<p>Select Execution Environment: </p>
					<p><select v-model="qei" class="form-select form-select-sm w-auto" >
						<option value="-1" >Select environment</option>
						<template v-for="d,i in test_environments" ><option v-if="d['t']!='cloud-alias'" v-bind:value="i" >{{ d['u'] }}</option></template>
					</select></p>
					<div v-if="settings['external'][ external_queue_index ]['type']=='s'">
						<p>Single worker mode</p>
						<input type="button" value="Start Worker" class="btn btn-outline-dark btn-sm" v-on:click="start_external_queue2('single')" >
					</div>
					<div v-else-if="settings['external'][ external_queue_index ]['type']=='m'">
						<p>Multi worker mode</p>
						<input type="button" value="Start Single Worker" class="btn btn-outline-dark btn-sm me-3" v-on:click="start_external_queue2('single')" >
						<input type="button" value="Start All Workers" class="btn btn-outline-dark btn-sm" v-on:click="start_external_queue2('all')" >
					</div>
		      	</template>

				<div v-if="smsg" class="alert alert-primary" >{{ smsg }}</div>
				<div v-if="serr" class="alert alert-danger" >{{ serr }}</div>		      	

		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="external_queue_log_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg modal-xl">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">External Queue Log</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

		      	<div style="height: 40px; display: flex; column-gap:10px;">
		      		<div>
		      			<div title="Refresh" v-on:click="load_external_queue_log()"  class="btn btn-outline-dark btn-sm" ><i class="fa-solid fa-arrows-rotate"></i></div>
		      		</div>
		      		<div>
		      			<div style="display:flex; column-gap: 5px;">
		      				<span>Task ID: </span>
			      			<input type="text" class="form-control form-control-sm w-auto" v-model="queue_log_keyword" placeholder="Search Task">
			      			<input type="button" class="btn btn-outline-dark btn-sm" value="Search" v-on:click="load_external_queue_log()">
			      			<input v-if="external_log.length>=100" type="button" class="btn btn-outline-dark btn-sm" value="Next" v-on:click="load_external_queue_next()">
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
								<td>TaskId</td>
								<td>Info</td>
							</tr>
							<tr v-for="d,i in external_log">
								<td><div class="mongoid" ><div>{{ d['_id'] }}</div><span>#</span></div></td>
								<td><span v-if="'tid' in d" >{{ d['tid'] }}</span></td>
								<td nowrap>{{ d['date'] }}</td>
								<td nowrap>{{ d['event'] }}</td>
								<td nowrap><span v-if="'task_id' in d" >{{ d['task_id'] }}</td>
								<td>
									 <template v-for="dd,ii in d" ><div v-if="ii!='task_id'&&ii!='tid'&&ii!='_id'&&ii!='event'&&ii!='date'&&ii!='m_i'" style="white-space: nowrap;" >{{ ii }}: {{ dd }}</div></template>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- <pre>{{ external_log }}</pre> -->

		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="external_queue" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Save External Queue</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

				<table class="table table-bordered table-sm">
					<tr>
						<td>Topic</td>
						<td><input type="text" v-model="new_external_queue['topic']" class="form-control form-control-sm" placeholder="topicname"></td>
					</tr>
					<tr>
						<td>System</td>
						<td>
							<select v-model="new_external_queue['system']" class="form-select form-select-sm w-auto" v-on:change="external_queue_select_system" >
								<option value="">Select Message Broker</option>
								<option value="awssqs">AWS SQS</option>
								<option value="awssns">AWS SNS</option>
								<option value="rabbitmq">RabbitMQ</option>
							</select>
						</td>
					</tr>
					<template v-if="new_external_queue['system'] in external_queue_types" >
						<template v-if="new_external_queue['system'] in new_external_queue" >
							<tr>
								<td>{{ external_queue_types[ new_external_queue['system'] ]['label'] }}</td>
								<td>
									<template v-if="new_external_queue['system'] in new_external_queue" >
										<table class="table table-bordered table-sm">
											<tr v-for="vd,vf in external_queue_types[ new_external_queue['system'] ]['fields']">
												<td>{{ vd['label'] }}</td>
												<td><input type="text" v-model="new_external_queue[ new_external_queue['system'] ][vf]" class="form-control form-control-sm" v-bind:placeholder="vd['label']"></td>
											</tr>
										</table>
									</template>
									<div v-else>Template not found</div>
									<!-- <pre>{{ new_external_queue[ new_external_queue['system'] ] }}</pre> -->
								</td>
							</tr>
						</template>
						<tr v-else ><td colspan="2">Template not defined</td></tr>
					</template>
					<tr v-else ><td colspan="2">Template not defined</td></tr>
					<tr>
						<td>Auth Type</td>
						<td>
							<div style="display:flex; column-gap:10px;">
								<div>
									<select v-model="new_external_queue['authtype']" class="form-select form-select-sm w-auto" v-on:change="external_select_authtype()" >
										<option value="stored">Stored Credentials</option>
										<option value="profile">System Profile</option>
										<option value="env">Environment Variable</option>
									</select>
								</div>
								<div>
									<div v-if="new_external_queue['authtype']=='stored'" style="display:flex; column-gap:10px;" >
										<select v-model="new_external_queue['cred']['cred_id']" class="form-select form-select-sm w-auto" style="width:calc( 100% - 30px )" v-on:change="external_select_cred()" >
											<option value="">Select Stored Credential</option>
											<option v-for="vd,vi in creds" v-bind:value="vd['cred_id']" >{{ vd['name'] + " - " + vd['type'] }}</option>
											<option v-if="new_external_queue['cred']['cred_id']" v-bind:value="new_external_queue['cred']['cred_id']" >{{ new_external_queue['cred']['name'] }}</option>
										</select>
										<div style="width:25px; height: 25px; cursor:pointer; border: 1px solid #ccc; " v-on:click="load_stored_creds()" >
											<svg viewBox="0 0 64 64" fill="currentcolor">
												<path d="m54,32c0,12.15-9.85,22-22,22s-22-9.85-22-22,9.85-22,22-22h2.26l-5.76-5.76,4.24-4.24,13,13-13,13-4.24-4.24,5.76-5.76h-2.26c-8.84,0-16,7.16-16,16s7.16,16,16,16,16-7.16,16-16h6Z" />
											</svg>
										</div>
									</div>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>Type</td>
						<td>
							<select v-model="new_external_queue['type']" class="form-select form-select-sm w-auto" >
								<option value="s">Single Thread</option>
								<option value="m">Multi Threaded</option>
							</select>
							<div class="text-secondary">Single thread serve as FIFO (first in first out). Multi threaded serve in best effort ordering in separate processes</div>
						</td>
					</tr>
					<tr v-if="new_external_queue['type']=='m'">
						<td>Threads</td>
						<td><input type="number" v-model="new_external_queue['con']" class="form-control form-control-sm w-auto d-inline" > Consumers<div class="text-secondary" >Max 5 threads</div></td>
					</tr>
					<tr>
						<td>Function</td>
						<td>
							<select v-model="new_external_queue['fn_id']" class="form-select form-select-sm w-auto" v-on:change="external_selected_function()" >
								<option value="">Select function</option>
								<option v-for="v,i in functions" v-bind:value="v['_id']" >{{ v['name'] }}</option>
								<option v-if="new_external_queue['fn_id']!=''" v-bind:value="new_external_queue['fn_id']" >{{ new_external_queue['fn'] }}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td nowrap>Timeout</td>
						<td><input type="number" v-model="new_external_queue['wait']" class="form-control form-control-sm w-auto d-inline" > Seconds <div class="text-secondary" >Execution timeout for each item. Max 60 seconds</div></td>
					</tr>
					<tr>
						<td nowrap>Retry</td>
						<td><input type="number" v-model="new_external_queue['retry']" class="form-control form-control-sm w-auto d-inline" ><div class="text-secondary" >Retry on fail. max 3</div></td>
					</tr>
					<tr>
						<td nowrap>Log Retention</td>
						<td><input type="number" v-model="new_external_queue['ret']" class="form-control form-control-sm w-auto d-inline" > Days <div class="text-secondary" >Max 5 days</div></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="button" class="btn btn-outline-dark btn-sm" value="Save Queue" v-on:click="save_external_queue" ></td>
					</tr>
				</table>
				<div v-if="'_id' in new_external_queue">Restart Queue for picking up new changes</div>
				<div v-if="exmsg" class="alert alert-primary" >{{ exmsg }}</div>
				<div v-if="exerr" class="alert alert-danger" >{{ exerr }}</div>
				<!-- <pre>{{ new_external_queue }}</pre> -->

		      </div>
		    </div>
		  </div>
		</div>




		<div class="modal fade" id="cron_edit_popup" tabindex="-1" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Cron Edit</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

				<table v-if="'_id' in edit_cron" class="table table-bordered table-sm">
					<tr>
						<td>Description</td>
						<td><textarea v-model="edit_cron['des']" class="form-control form-control-sm" placeholder="Description"></textarea></td>
					</tr>
					<tr>
						<td>Function</td>
						<td>
							<select v-model="edit_cron['fn_id']" class="form-select form-select-sm w-auto" v-on:change="cron_selected_function()" >
								<option value="">Select function</option>
								<option v-for="v,i in functions" v-bind:value="v['_id']" >{{ v['name'] }}</option>
								<option v-if="edit_cron['fn_id']!=''" v-bind:value="edit_cron['fn_id']" >{{ edit_cron['fn'] }}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Function</td>
						<td>
							<select v-model="edit_cron['fn_id']" class="form-select form-select-sm w-auto" v-on:change="cron_selected_function()" >
								<option value="">Select function</option>
								<option v-for="v,i in functions" v-bind:value="v['_id']" >{{ v['name'] }}</option>
								<option v-if="edit_cron['fn_id']!=''" v-bind:value="edit_cron['fn_id']" >{{ edit_cron['fn'] }}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Type</td>
						<td>
							<select v-model="edit_cron['type']" class="form-select form-select-sm w-auto" v-on:change="cron_change_type()" >
								<option value="onetime">OneTime</option>
								<option value="repeat">Repeat</option>
								<option value="reboot">On Reboot</option>
							</select>
						</td>
					</tr>
					<tr v-if="edit_cron['type']=='onetime'">
						<td>Schedule</td>
						<td>
							<div><input class="form-control form-control-sm" type="datetime-local" v-model="edit_cron['onetime']" v-on:change="onetime_change()"></div>
							<div v-if="cron_note" style="font-weight: bold; line-height:30px; border: 1px solid #ccc; border-radius:10px; padding:10px;" >{{ cron_note }}</div>
						</td>
					</tr>
					<tr v-if="edit_cron['type']=='repeat'">
						<td>Repeat</td>
						<td>
							<table class="table table-bordered table-sm">
								<tr>
									<td>Minute</td>
									<td>
										<input v-model="edit_cron['repeat']['minute']" class="form-control form-control-sm" placeholder="Minutes" v-on:change="calc_cron()">
										<div class="text-secondary small">0 - 59</div>
									</td>
								</tr>
								<tr>
									<td>Hour</td>
									<td>
										<input v-model="edit_cron['repeat']['hour']" class="form-control form-control-sm" placeholder="Minutes" v-on:change="calc_cron()">
										<div class="text-secondary small">0 - 23</div>
									</td>
								</tr>
								<tr>
									<td>Day</td>
									<td>
										<input v-model="edit_cron['repeat']['day']" class="form-control form-control-sm" placeholder="Minutes" v-on:change="calc_cron()">
										<div class="text-secondary small">1 - 31</div>
									</td>
								</tr>
								<tr>
									<td>Month</td>
									<td>
										<input v-model="edit_cron['repeat']['month']" class="form-control form-control-sm" placeholder="Minutes" v-on:change="calc_cron()">
										<div class="text-secondary small">1 - 12</div>
									</td>
								</tr>
								<tr>
									<td>WeekDays</td>
									<td>
										<input v-model="edit_cron['repeat']['weekday']" class="form-control form-control-sm" placeholder="Minutes" v-on:change="calc_cron()">
										<div class="text-secondary small">0 - 6 (Sunday to Saturday)</div>
									</td>
								</tr>
							</table>
							<div class="text-secondary" >
								<div>* = everyminute</div>
								<div>1 = 1st minute</div>
								<div>5,10,15 = 5th, 10th, and 15th minute</div>
								<div>*/5 = every 5 minutes</div>
							</div>
							<div v-if="cron_note" style="font-weight: bold; line-height:30px; border: 1px solid #ccc; border-radius:10px; padding:10px;" >{{ cron_note }}</div>
							<div v-if="cronerr2" class="alert alert-danger" >{{ cronerr2 }}</div>
							<div v-if="cron_times.length>0" >
								<div>Expected Cron Schedules</div>
								<ul>
									<li v-for="d in cron_times" >{{ d }}</li>
								</ul>
							</div>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<div><input type="button" class="btn btn-outline-dark btn-sm" value="Save Queue" v-on:click="save_cron" ></div>
							<div v-if="cronmsg" class="alert alert-primary" >{{ cronmsg }}</div>
							<div v-if="cronerr" class="alert alert-danger" >{{ cronerr }}</div>
						</td>
					</tr>
				</table>

				<pre>{{ edit_cron }}</pre>

		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="cron_log_modal" tabindex="-1" >
		  <div class="modal-dialog modal-lg modal-xl">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">CronJob Log</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body" >

		      	<div style="height: 40px; display: flex; column-gap:10px;">
		      		<div>
		      			<div title="Refresh" v-on:click="cron_load_log()"  class="btn btn-outline-dark btn-sm" ><i class="fa-solid fa-arrows-rotate"></i></div>
		      		</div>
		      		<div>
		      			<div style="display:flex; column-gap: 5px;">
			      			<input type="text" class="form-control form-control-sm w-auto" v-model="cron_log_keyword" placeholder="Search">
			      			<input type="button" class="btn btn-outline-dark btn-sm" value="Search" v-on:click="cron_load_log()">
			      			<input v-if="cron_log_records.length>=100" type="button" class="btn btn-outline-dark btn-sm" value="Next" v-on:click="cron_load_log_next()">
		      			</div>
		      		</div>
		      		<div>
						<div v-if="qlmsg" class="alert alert-primary py-0" >{{ cronlogmsg }}</div>
						<div v-if="qlerr" class="alert alert-danger py-0" >{{ cronlogerr }}</div>
					</div>
		      	</div>


				<div style="overflow: auto; height: 500px;">
					<table class="table table-bordered table-striped table-sm w-auto" >
						<tbody>
							<tr style="position: sticky; top:0px; background-color: white;">
								<td>#</td>
								<td>Date</td>
								<td>TaskID</td>
								<td>Info</td>
							</tr>
							<tr v-for="d,i in cron_log_records">
								<td><div class="mongoid" ><div>{{ d['_id'] }}</div><span>#</span></div></td>
								<td nowrap>{{ d['date'] }}</td>
								<td nowrap>
									<span v-if="'task_id' in d" ><a v-bind:href="path+'tasks/bg/'+d['task_id']" target="_blank" >{{ d['task_id'] }}</a></span>
								</td>
								<td>
									 -
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- <pre>{{ external_log }}</pre> -->

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
				"taskpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tasks",
				"test_environments": <?=json_encode($test_environments) ?>,
				"app_id" : "<?=$app['_id'] ?>",
				"settings": {'internal':[],'external':[]},
				"smsg": "", "serr":"",
				"msg": "", "err":"",
				"qlmsg": "", "qlerr":"",
				"kmsg": "", "kerr":"",
				
				"ipmsg": "", "iperr":"",
				"internal_queue_log_popup": false,
				"internal_queue_id": "",
				"internal_queue_index": -1,
				"internal_log": [],
				"new_queue_template": {
					"type": "s",
					"topic": "",
					"des": "",
					"timeout": 30,
					"ret": 1,
					"delay": 0,
					"con": 2,
					"retry": 0,
					"wait": 10,
					"fn": "","fn_id": "","fn_vid": "",
				},
				"new_queue":{},

				"external_queue_types": <?=json_encode($external_queue_types) ?>,
				"exmsg": "", "exerr":"",
				"external_queue_log_popup": false,
				"external_queue_id": "",
				"external_queue_index": -1,
				"external_log": [],
				"new_external_queue_template": {
					"source": "", //awssqs,awssns,rabbitmq,
					"authtype": "stored", //saved, profile, environment
					"profile": "",
					"type":"s", //single thread,multi thread
					"cred":{"cred_id":"","name":""},
					"topic": "",
					"des": "",
					"timeout": 30,
					"ret": 1,
					"delay": 0,
					"con": 2,
					"retry": 0,
					"wait": 10,
					"fn": "","fn_id": "","fn_vid": "",
				},
				"new_external_queue": {},
				"creds":[],
				
				"queue_log_keyword": "",
				"start_queue_popup": false,	
				"qei": -1,
				"keyword": "",
				"token": "",
				"saved": <?=($saved?"true":"false") ?>,
				"functions": [], popup: false, vip: false,
				"tab": "queue",
				
				cronerr: "",cronerr2: "",
				cronmsg: "",
				cronloadmsg: "", cronloaderr: "",
				cronlogmsg: "", cronlogerr:"",
				edit_cron: {},
				new_cron: {
					"_id": "new",
					"des": "",
					"type": "onetime", //onetime/repeat/reboot
					"repeat": {
						"minute": "*",
						"hour": "*",
						"day": "*",
						"month": "*",
						"weekday": "*"
					},
					"onetime": "2010-10-10T10:10",
					"onetime_gmt": "2010-10-10 10:10",
					"fn": "","fn_id": "","fn_vid": "",
					"active": true
				},
				cron_note: "",
				cronpopup: false,
				cron_times: [],
				cronjobs: [],
				cron_log_keyword: "",
				cron_log_popup: false,
				cron_log_records: [],
				bgtasks: [],
			};
		},
		mounted:function(){
			this.load_queues();
			this.load_functions();
		},
		methods: {
			open_link: function(t){
				this.$router.push(this.taskpath + '/'+t);
			},
			open_tab: function(t){
				this.tab = t;
				if( t == 'cron' ){
					this.crons_load();
				}
				if( t == 'bg' ){
					this.bgtasks_load();
				}
			},
			view_log: function(di){
				this.queue_log_keyword = "";
				this.internal_queue_id = this.settings['internal'][ di ]['_id'];
				this.internal_queue_log_popup = new bootstrap.Modal( document.getElementById('internal_queue_log_modal') );
				this.internal_queue_log_popup.show();
				this.load_internal_queue_log();
			},
			load_internal_queue_log: function(){
				this.qlmsg = "Loading...";
				this.internal_log = [];
				this.qlerr = "";
				axios.post("?", {
					"action":"task_load_internal_queue_log", 
					"queue_id":this.internal_queue_id,
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
					"action":"task_load_internal_queue_log", 
					"queue_id":this.internal_queue_id,
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
			start_internal_queue: function(di){
				this.serr = "";
				this.smsg = "";
				this.internal_queue_id = this.settings['internal'][ di ]['_id'];
				this.internal_queue_index = di;
				this.start_queue_popup = new bootstrap.Modal( document.getElementById('start_queue_modal') );
				this.start_queue_popup.show();
			},
			start_internal_queue2: function(vmode){
				this.serr = "";
				if( Number(this.qei) == -1 ){
					this.serr = "Select workder node";return;
				}
				this.smsg = "Starting working node";
				axios.post("?", {
					"action":"task_queue_start",
					"queue_id":this.internal_queue_id,
					"env": this.test_environments[ Number(this.qei) ],
					"mode": vmode,
				}).then(response=>{
					this.smsg = "";
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.settings['internal'][ this.internal_queue_index ]['started']=true;
							this.smsg = "Success";
							setTimeout(this.load_queues,3000);setTimeout(this.load_queues,10000);
						}else{
							this.serr = (response.data['error']);
						}
					}else{
						this.serr = ( "Error: incorrect response" );
					}
				}).catch(error=>{
					this.serr = ("Error: "+error.message);
				});
			},
			pause_queue: function(vi){
				axios.post("?", {"action":"task_queue_stop","queue_id":this.settings['internal'][ vi ]['_id']}).then(response=>{
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							delete(this.settings['internal'][ vi ]['started']);
							alert("Queue stopped");
							setTimeout(this.load_queues,3000);setTimeout(this.load_queues,10000);
						}else{
							alert(response.data['error']);
						}
					}else{
						alert( "Error: incorrect response" );
					}
				}).catch(error=>{
					alert("Error: "+error.message);
				});
			},
			flush_queue: function(vi){
				axios.post("?", {"action":"task_queue_flush","queue_id":this.settings['internal'][ vi ]['_id']}).then(response=>{
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							alert("Queue flushed");
							setTimeout(this.load_queues,3000);setTimeout(this.load_queues,10000);
						}else{
							alert(response.data['error']);
						}
					}else{
						alert( "Error: incorrect response" );
					}
				}).catch(error=>{
					alert("Error: "+error.message);
				});
			},
			internal_selected_function: function(){
				for(var i=0;i<this.functions.length;i++){
					if( this.functions[i]['_id'] == this.new_queue['fn_id'] ){
						this.new_queue['fn'] = this.functions[i]['name']+'';
						this.new_queue['fn_vid'] = this.functions[i]['version_id']+'';
					}
				}
			},
			show_configure: function(){
				this.popup = new bootstrap.Modal(document.getElementById('settings_modal'));
				this.popup.show();
			},
			show_internal_add: function(){
				this.iperr = "";this.ipmsg = "";
				this.new_queue = JSON.parse(JSON.stringify(this.new_queue_template));
				this.vip = new bootstrap.Modal(document.getElementById('internal_queue'));
				this.vip.show();
			},
			edit_internal_queue: function(di){
				this.iperr = "";this.ipmsg = "";
				this.new_queue = JSON.parse(JSON.stringify(this.settings['internal'][di]));
				this.vip = new bootstrap.Modal(document.getElementById('internal_queue'));
				this.vip.show();
			},
			delete_internal_queue: function(di){
				if( confirm("Are you sure to delete topic?\nAny pending tasks in the queue will get discorded") ){
					axios.post("?", {
						"action": "task_queue_delete", "queue_id": this.settings['internal'][di]['_id']
					}).then(response=>{
						if( response.status == 200 ){
							if( typeof(response.data) == "object" ){
								if( 'status' in response.data ){
									if( response.data['status'] == "success" ){
										alert("Queue Deleted successfully");
										this.load_queues();
									}else{
										alert(response.data['error']);
									}
								}else{
									alert("Invalid response");
								}
							}else{
								alert("Incorrect response");
							}
						}else{
							alert("http:"+response.status);
						}
					}).catch(error=>{
						if( typeof(error.response.data) == "object" ){
							if( 'error' in error.response['data'] ){
								alert("error:"+error.response['data']['error']);
							}else{
								alert("error:"+response.message + "\n " + JSON.stringify(error.response['data']).substr(0,200));
							}
						}else{
							alert("error:"+response.message + "\n " + error.response['data'].substr(0,200));
						}
					});
				}
			},
			save_queue: function(){
				this.iperr = "";
				this.ipmsg = "";
				this.new_queue['topic'] = this.new_queue['topic'].toLowerCase().trim();
				if( this.new_queue['topic'].match(/^[a-z0-9\.\-\_]{2,20}$/) == null ){
					this.iperr = "Queue name should be: [a-z0-9\.\-\_]{2,25}";return false;
				}
				if( this.new_queue['fn'] ==""){
					this.iperr = "Need function";return false;
				}
				if( this.new_queue['type'] =='s' ){
					this.new_queue['con'] = 1;
				}else if( Number(this.new_queue['con']) < 1 || Number(this.new_queue['con']) > 5 ){
					this.new_queue['con'] = 2; alert("Threads corrected"); return false;
				}
				if( Number(this.new_queue['ret']) < 1 || Number(this.new_queue['ret']) > 5 ){
					this.new_queue['ret'] = 1;alert("Retention period corrected");return false;
				}
				if( Number(this.new_queue['wait']) < 5 || Number(this.new_queue['wait']) > 60 ){
					this.new_queue['wait'] = 10;alert("Timeout corrected");return false;
				}
				if( Number(this.new_queue['retry']) > 3 ){
					this.new_queue['retry'] = 0;alert("Retry corrected");return false;
				}
				axios.post("?", {
					"action": "save_task_queue", "queue": this.new_queue
				}).then(response=>{
					this.ipmsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.ipmsg = "Updated successfully";
									this.load_queues();
									setTimeout(function(v){v.vip.hide();v.ipmsg="";},2000,this);
								}else{
									this.iperr = response.data['error'];
								}
							}else{
								this.iperr = "Invalid response";
							}
						}else{
							this.iperr = "Incorrect response";
						}
					}else{
						this.iperr = "http:"+response.status;
					}
				}).catch(error=>{
					if( typeof(error.response.data) == "object" ){
						if( 'error' in error.response['data'] ){
							this.iperr = "error:"+error.response['data']['error'];
						}else{
							this.iperr = "error:"+response.message + " " + JSON.stringify(error.response['data']).substr(0,200);
						}
					}else{
						this.iperr = "error:"+response.message + " " + error.response['data'].substr(0,200);
					}
				});
			},
			/* --------------------------------------- */

			view_log: function(di){
				this.queue_log_keyword = "";
				this.external_queue_id = this.settings['external'][ di ]['_id'];
				this.external_queue_log_popup = new bootstrap.Modal( document.getElementById('external_queue_log_modal') );
				this.external_queue_log_popup.show();
				this.load_external_queue_log();
			},
			load_external_queue_log: function(){
				this.qlmsg = "Loading...";
				this.external_log = [];
				this.qlerr = "";
				axios.post("?", {
					"action":"task_load_external_queue_log", 
					"queue_id":this.external_queue_id,
					"task_id": this.queue_log_keyword,
				}).then(response=>{
					this.qlmsg = "";
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.external_log=response.data['data'];
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
			load_external_queue_next: function(){
				this.qlmsg = "Loading...";
				var last = this.external_log[ this.external_log.length-1 ]['_id'];
				this.external_log = [];
				this.qlerr = "";
				axios.post("?", {
					"action":"task_load_external_queue_log", 
					"queue_id":this.external_queue_id,
					"task_id": this.queue_log_keyword,
					"last": last
				}).then(response=>{
					this.qlmsg = "";
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.external_log=response.data['data'];
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
			start_external_queue: function(di){
				this.serr = "";
				this.smsg = "";
				this.external_queue_id = this.settings['external'][ di ]['_id'];
				this.external_queue_index = di;
				this.start_queue_popup = new bootstrap.Modal( document.getElementById('start_queue_external_modal') );
				this.start_queue_popup.show();
			},
			start_external_queue2: function(vmode){
				this.serr = "";
				if( Number(this.qei) == -1 ){
					this.serr = "Select workder node";return;
				}
				this.smsg = "Starting working node";
				axios.post("?", {
					"action":"task_queue_external_start",
					"queue_id":this.external_queue_id,
					"env": this.test_environments[ Number(this.qei) ],
					"mode": vmode,
				}).then(response=>{
					this.smsg = "";
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.settings['external'][ this.external_queue_index ]['started']=true;
							this.smsg = "Success";
							setTimeout(this.load_queues,3000);setTimeout(this.load_queues,10000);
						}else{
							this.serr = (response.data['error']);
						}
					}else{
						this.serr = ( "Error: incorrect response" );
					}
				}).catch(error=>{
					this.serr = ("Error: "+error.message);
				});
			},
			pause_external_queue: function(vi){
				axios.post("?", {"action":"task_queue_external_stop","queue_id":this.settings['external'][ vi ]['_id']}).then(response=>{
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							delete(this.settings['external'][ vi ]['started']);
							alert("Queue stopped");
							setTimeout(this.load_queues,3000);setTimeout(this.load_queues,10000);
						}else{
							alert(response.data['error']);
						}
					}else{
						alert( "Error: incorrect response" );
					}
				}).catch(error=>{
					alert("Error: "+error.message);
				});
			},
			flush_external_queue: function(vi){
				axios.post("?", {"action":"task_queue_external_flush","queue_id":this.settings['external'][ vi ]['_id']}).then(response=>{
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							alert("Queue flushed");
							setTimeout(this.load_queues,3000);setTimeout(this.load_queues,10000);
						}else{
							alert(response.data['error']);
						}
					}else{
						alert( "Error: incorrect response" );
					}
				}).catch(error=>{
					alert("Error: "+error.message);
				});
			},

			external_queue_select_system: function(){
				if( this.new_external_queue["system"] in this.external_queue_types ){
					var v = this.external_queue_types[ this.new_external_queue["system"] ]['fields'];
					var vv = {};
					for( var i in v ){
						vv[ i ] = "";
					}
					this.new_external_queue[ this.new_external_queue["system"] ] = vv;
				}else{
					this.new_external_queue[ this.new_external_queue["system"] ] = {};
				}
			},

			external_selected_function: function(){
				for(var i=0;i<this.functions.length;i++){
					if( this.functions[i]['_id'] == this.new_external_queue['fn_id'] ){
						this.new_external_queue['fn'] = this.functions[i]['name']+'';
						this.new_external_queue['fn_vid'] = this.functions[i]['version_id']+'';
					}
				}
			},
			external_select_authtype: function(){
				if( this.new_external_queue['authtype'] == "stored" ){
					this.load_stored_creds();
				}
			},
			external_select_cred: function(){
				for( var i=0;i<this.creds.length;i++){
					if( this.new_external_queue['cred']['cred_id']  == this.creds[i]['cred_id'] ){
						this.new_external_queue['cred']['name'] = this.creds[i]['name'];
					}
				}
			},
			load_stored_creds: function(){
				axios.post("?", {"action":"task_load_creds"}).then(response=>{
					if( 'status' in response.data ){
						if( response.data['status']=="success" ){
							this.creds = response.data['data'];
						}else{
							alert(response.data['error']);
						}
					}else{
						alert( "Error: incorrect response" );
					}
				}).catch(error=>{
					alert("Error: "+error.message);
				});
			},
			show_external_add: function(){
				this.exerr = "";this.exmsg = "";
				this.external_queue_id = "new";
				this.new_external_queue = JSON.parse(JSON.stringify(this.new_external_queue_template));
				this.vip = new bootstrap.Modal( document.getElementById('external_queue') );
				this.vip.show();
			},
			edit_external_queue: function(di){
				this.exerr = "";this.exmsg = "";
				this.new_external_queue = JSON.parse(JSON.stringify(this.settings['external'][di]));
				this.vip = new bootstrap.Modal(document.getElementById('external_queue'));
				this.vip.show();
			},
			delete_external_queue: function(di){
				if( confirm("Are you sure to delete topic?\nAny pending tasks in the queue will get discorded.") ){
					axios.post("?", {
						"action": "task_external_queue_delete", "queue_id": this.settings['external'][di]['_id']
					}).then(response=>{
						if( response.status == 200 ){
							if( typeof(response.data) == "object" ){
								if( 'status' in response.data ){
									if( response.data['status'] == "success" ){
										alert("Queue Deleted successfully");
										this.load_queues();
									}else{
										alert(response.data['error']);
									}
								}else{
									alert("Invalid response");
								}
							}else{
								alert("Incorrect response");
							}
						}else{
							alert("http:"+response.status);
						}
					}).catch(error=>{
						if( typeof(error.response.data) == "object" ){
							if( 'error' in error.response['data'] ){
								alert("error:"+error.response['data']['error']);
							}else{
								alert("error:"+response.message + "\n " + JSON.stringify(error.response['data']).substr(0,200));
							}
						}else{
							alert("error:"+response.message + "\n " + error.response['data'].substr(0,200));
						}
					});
				}
			},
			save_external_queue: function(){
				this.exerr = "";
				this.exmsg = "";
				this.new_external_queue['topic'] = this.new_external_queue['topic'].toLowerCase().trim();
				if( this.new_external_queue['topic'].match(/^[a-z0-9\.\-\_]{2,20}$/) == null ){
					this.exerr = "Queue name should be: [a-z0-9\.\-\_]{2,25}";return false;
				}
				if( this.new_external_queue['system'] == "" ){
					this.exerr = "Queue system required";return false;
				}
				if( this.new_external_queue['system'] in this.new_external_queue == false ){
					this.exerr = "Something wrong";return false;
				}
				if( this.new_external_queue['system'] in this.external_queue_types == false ){
					this.exerr = "Something wrong 2";return false;
				}
				for( var vf in this.external_queue_types[ this.new_external_queue['system'] ]['fields'] ){
					console.log( vf );
					var vd = this.external_queue_types[ this.new_external_queue['system'] ]['fields'][vf];
					if( vf in this.new_external_queue[ this.new_external_queue['system'] ] == false ){
						this.exerr = "Field: " + vd['label'] + " not found";return false;
					}
					var val = this.new_external_queue[ this.new_external_queue['system'] ][ vf ]['value']+'';
					console.log( 'x'+val+'x' );
					if( val.match(/^[a-z0-9\!\@\%\^\*\(\)\_\-\+\=\,\.\:\;]{2,250}$/i) == null ){
						this.exerr = "Field: " + vd['label'] + " incorrect value";return false;
					}
				}
				if( this.new_external_queue['authtype'] == "" ){
					this.exerr = "Need Authentication type";return false;
				}
				if( this.new_external_queue['authtype'] == "stored" ){
					if( this.new_external_queue['cred']['cred_id'] == "" || this.new_external_queue['cred']['name'] == "" ){
						this.exerr = "Need Credentials";return false;
					}
				}
				if( this.new_external_queue['fn'] =="" ){
					this.exerr = "Need function";return false;
				}
				if( this.new_external_queue['type'] =='s' ){
					this.new_external_queue['con'] = 1;
				}else if( Number(this.new_external_queue['con']) < 1 || Number(this.new_external_queue['con']) > 5 ){
					this.new_external_queue['con'] = 2; alert("Threads corrected"); return false;
				}
				if( Number(this.new_external_queue['ret']) < 1 || Number(this.new_external_queue['ret']) > 5 ){
					this.new_external_queue['ret'] = 1;alert("Retention period corrected");return false;
				}
				if( Number(this.new_external_queue['wait']) < 5 || Number(this.new_external_queue['wait']) > 60 ){
					this.new_external_queue['wait'] = 10;alert("Timeout corrected");return false;
				}
				if( Number(this.new_external_queue['retry']) > 3 ){
					this.new_external_queue['retry'] = 0;alert("Retry corrected");return false;
				}
				axios.post("?", {
					"action": "save_task_queue_external", 
					"queue": this.new_external_queue,
					"queue_id": this.external_queue_id
				}).then(response=>{
					this.exmsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.exmsg = "Updated successfully";
									this.load_queues();
									setTimeout(function(v){v.vip.hide();v.exmsg="";},2000,this);
								}else{
									this.exerr = response.data['error'];
								}
							}else{
								this.exerr = "Invalid response";
							}
						}else{
							this.exerr = "Incorrect response";
						}
					}else{
						this.exerr = "http:"+response.status;
					}
				}).catch(error=>{
					if( typeof(error.response.data) == "object" ){
						if( 'error' in error.response['data'] ){
							this.exerr = "error:"+error.response['data']['error'];
						}else{
							this.exerr = "error:"+response.message + " " + JSON.stringify(error.response['data']).substr(0,200);
						}
					}else{
						this.exerr = "error:"+response.message + " " + error.response['data'].substr(0,200);
					}
				});
			},
			load_queues: function(){
				this.err = "";
				this.msg = "";
				axios.post("?", {
					"action": "load_task_queues"
				}).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.settings = response.data['data'];
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
					if( typeof(error.response.data) == "object" ){
						if( 'error' in error.response['data'] ){
							this.err = "error:"+error.response['data']['error'];
						}else{
							this.err = "error:"+response.message + " " + JSON.stringify(error.response['data']).substr(0,200);
						}
					}else{
						this.err = "error:"+response.message + " " + error.response['data'].substr(0,200);
					}
				});
			},
			load_functions: function(){
				this.ferr = "";
				this.fmsg = "";
				axios.post("?", {
					"action": "load_functions"
				}).then(response=>{
					this.fmsg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.functions = response.data['data'];
								}else{
									this.ferr = response.data['error'];
								}
							}else{
								this.ferr = "Invalid response";
							}
						}else{
							this.ferr = "Incorrect response";
						}
					}else{
						this.ferr = "http:"+response.status;
					}
				}).catch(error=>{
					if( typeof(error.response.data) == "object" ){
						if( 'error' in error.response['data'] ){
							this.ferr = "error:"+error.response['data']['error'];
						}else{
							this.ferr = "error:"+response.message + " " + JSON.stringify(error.response['data']).substr(0,200);
						}
					}else{
						this.ferr = "error:"+response.message + " " + error.response['data'].substr(0,200);
					}
				});
			},
			show_cron_edit: function(vid){
				this.cronerr = "";this.cronmsg = "";
				this.edit_cron = JSON.parse(JSON.stringify(this.new_cron));
				this.cronpopup = new bootstrap.Modal(document.getElementById('cron_edit_popup'));
				this.cronpopup.show();
				this.calc_cron();
			},
			edit_cronjob: function(vid){
				this.cronerr = "";this.cronmsg = "";
				this.edit_cron = JSON.parse(JSON.stringify(this.cronjobs[ vid ]));
				this.cronpopup = new bootstrap.Modal(document.getElementById('cron_edit_popup'));
				this.cronpopup.show();
				this.calc_cron();
			},
			cron_selected_function: function(){
				for(var i=0; i<this.functions.length; i++){
					if( this.functions[i]['_id'] == this.edit_cron['fn_id'] ){
						this.edit_cron['fn'] = this.functions[i]['name']+'';
						this.edit_cron['fn_vid'] = this.functions[i]['version_id']+'';
					}
				}
			},
			onetime_change: function(){
				this.calc_cron();
			},
			calc_cron: function(){
				this.cron_times = [];
				this.cron_note = "";
				var t = [];
				if( this.edit_cron['type'] == "repeat" ){
					this.edit_cron['repeat']['minute'] = this.edit_cron['repeat']['minute'].trim()+'';
					if( this.edit_cron['repeat']['minute'].match(/^[0-9\,]+$/) ){
						var x = this.edit_cron['repeat']['minute'].split(/\,/g);
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								if( min < 0 || min > 59 ){
									this.cronerr = "Incorrect minute description";return;
								}
							}else{
								this.cronerr = "Incorrect minute description";return;
							}
						}
						t.push( this.edit_cron['repeat']['minute'] + " minute(s)" );
					}else if( this.edit_cron['repeat']['minute'].match(/^\*\/[0-9]+$/) ){
						var m = this.edit_cron['repeat']['minute'].match(/^\*\/([0-9]+)$/);
						var min = Number(m[1]);
						if( min < 2 || min > 10 ){
							this.cronerr = "Incorrect minute description";return;
						}
						t.push( "every " + min + " minutes" );
					}else if( this.edit_cron['repeat']['minute'] == "*" ){
						t.push( "every minute" );
					}else{
						this.cronerr = "Incorrect minute description";return;
					}

					this.edit_cron['repeat']['hour'] = this.edit_cron['repeat']['hour'].trim()+'';
					if( this.edit_cron['repeat']['hour'].match(/^[0-9\,]+$/) ){
						var x = this.edit_cron['repeat']['hour'].split(/\,/g);
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								if( min < 0 || min > 23 ){
									this.cronerr = "Incorrect hour description";return;
								}
							}else{
								this.cronerr = "Incorrect hour description";return;
							}
						}
						t.push( "at " + this.edit_cron['repeat']['hour'] + " hour(s)" );
					}else if( this.edit_cron['repeat']['hour'].match(/^\*\/[0-9]+$/) ){
						var m = this.edit_cron['repeat']['hour'].match(/^\*\/([0-9]+)$/);
						var min = Number(m[1]);
						if( min < 1 || min > 5 ){
							this.cronerr = "Incorrect hour description";return;
						}
						t.push( "at every " + min + " hour(s)" );
					}else if( this.edit_cron['repeat']['hour'] == "*" ){
						t.push( "at every hour" );
					}else if( this.edit_cron['repeat']['hour'] != "*" ){
						this.cronerr = "Incorrect hour description";return;
					}

					this.edit_cron['repeat']['day'] = this.edit_cron['repeat']['day'].trim()+'';
					if( this.edit_cron['repeat']['day'].match(/^[0-9\,]+$/) ){
						var x = this.edit_cron['repeat']['day'].split(/\,/g);
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								if( min < 1 || min > 31 ){
									this.cronerr = "Incorrect day description";return;
								}
							}else{
								this.cronerr = "Incorrect day description";return;
							}
						}
						t.push( " on " + this.edit_cron['repeat']['day'] + " date(s)" );
					}else if( this.edit_cron['repeat']['day'] == "*" ){
						t.push( " on every day" );
					}else{
						this.cronerr = "Incorrect date description";return;
					}
					this.edit_cron['repeat']['month'] = this.edit_cron['repeat']['month'].trim()+'';
					if( this.edit_cron['repeat']['month'].match(/^[0-9\,]+$/) ){
						var x = this.edit_cron['repeat']['month'].split(/\,/g);
						var mons = [];
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								if( min < 1 || min > 12 ){
									this.cronerr = "Incorrect month description";return;
								}
								mons.push(["January","February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][ min-1 ]);
							}else{
								this.cronerr = "Incorrect month description";return;
							}
						}
						t.push( " in " + mons.join(" ") + " month(s)" );
					}else if( this.edit_cron['repeat']['month'] == "*" ){
						//t.push( "every month" );
					}else if( this.edit_cron['repeat']['month'] != "*" ){
						this.cronerr = "Incorrect month description";return;
					}
					this.edit_cron['repeat']['weekday'] = this.edit_cron['repeat']['weekday'].trim()+'';
					if( this.edit_cron['repeat']['weekday'].match(/^[0-9\,]+$/) ){
						var x = this.edit_cron['repeat']['weekday'].split(/\,/g);
						var w = [];
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								if( min < 0 || min > 6 ){
									this.cronerr = "Incorrect weekday description";return;
								}
								w.push(["Sundary", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"][ min ]);
							}else{
								this.cronerr = "Incorrect weekday description";return;
							}
						}
						t.push( " on " + w.join( " " ) + " " );
					}else if( this.edit_cron['repeat']['weekday'] == "*" ){
						//t.push( "every weekdays" );
					}else{
						this.cronerr = "Incorrect weekday description";return;
					}
					this.cron_note = t.join( " " );
					this.cron_find_next_10();
				}else{
					console.log("xxxx");console.log( this.edit_cron['onetime'] );
					var dt = new Date( this.edit_cron['onetime']+'' );
					this.edit_cron['onetime_gmt'] = dt.toISOString().substr(0,16).replace("T", " ");
					var t = Date.now();
					var t2 = dt.getTime();
					console.log( t );
					console.log( t2 );
					var diff = parseInt((t2 - t)/1000);
					console.log( diff );
					if( diff > 0 ){
						if( diff > 60 ){
							diff = diff/60;
							if( diff > 60 ){
								diff = diff/60;
								if( diff > 60 ){
									diff = diff/24;
									this.cron_note = "Next run in " + Math.floor(diff) + " days";
									console.log("xxx1");
								}else{
									this.cron_note = "Next run in " + Math.floor(diff) + " hours";
									console.log("xxx2");
								}
							}else{
								this.cron_note = "Next run in " + Math.floor(diff) + " minutes";
								console.log("xxx2");
							}
						}else{
							this.cron_note = "Next run in " + Math.floor(diff) + " seconds";
							console.log("xxx3");
						}
					}else{
						this.cron_note = "Cron wont run";
						this.cronerr = "Schedule date crossed";
						console.log("xxx4");
					}
				}
			},
			cron_change_type: function(){
				var next_runs = [];
				this.cronerr2 = "";
				this.cron_note = "";
				this.cronerr = "";
				console.log(1111);
				setTimeout(this.calc_cron,1000);
			},
			cron_find_next_10: function(){
				this.cronerr2 = "";
				var dt = new Date();
				// var m = dt.toString().match(/GMT[\+\-]([0-9]{2})([0-9]{2})/);
				// if( m ){
				// 	var hours = Number(m[1]);
				// 	var minits = Number(m[2]);
				// }
				// var t = dt.getTime();
				// t = t + ( hours*3600*1000 ) + (minits*60*1000);
				// var dt = new Date(t);
				// dt.toISOString();
				var next_runs = [];

				if( this.edit_cron['type'] == "repeat" ){
					var min_range = [];
					if( this.edit_cron['repeat']['minute'].match(/^[0-9\,]+$/) ){
						var x = this.edit_cron['repeat']['minute'].split(/\,/g);
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								min_range.push( min );
							}
						}
					}else if( this.edit_cron['repeat']['minute'].match(/^\*\/[0-9]+$/) ){
						var m = this.edit_cron['repeat']['minute'].match(/^\*\/([0-9]+)$/);
						var min = Number(m[1]);
						for(var di=0;di<60;di=di+min){
							min_range.push( di );
						}
					}else if( this.edit_cron['repeat']['minute'] == "*" ){
						for(var di=0;di<=59;di++){
							min_range.push(di);
						}
					}

					var hour_range = [];
					if( this.edit_cron['repeat']['hour'].match(/^[0-9\,]+$/) ){
						var x = this.edit_cron['repeat']['hour'].split(/\,/g);
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								hour_range.push( min );
							}
						}
					}else if( this.edit_cron['repeat']['hour'].match(/^\*\/[0-9]+$/) ){
						var m = this.edit_cron['repeat']['hour'].match(/^\*\/([0-9]+)$/);
						var min = Number(m[1]);
						for(var di=0;di<24;di=di+min){
							hour_range.push( di );
						}
					}else if( this.edit_cron['repeat']['hour'] == "*" ){
						for(var di=0;di<=59;di++){
							hour_range.push(di);
						}
					}

					var date_range = [];
					if( this.edit_cron['repeat']['day'].match(/^[0-9\,]+$/) ){
						var x = this.edit_cron['repeat']['day'].split(/\,/g);
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								date_range.push(min);
							}
						}
					}else if( this.edit_cron['repeat']['day'] == "*" ){
						for(var di=1;di<=31;di++){
							date_range.push(di);
						}
					}

					var month_range = [];
					if( this.edit_cron['repeat']['month'].match(/^[0-9\,]+$/) ){
						var x = this.edit_cron['repeat']['month'].split(/\,/g);
						var mons = [];
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								month_range.push(min);
							}
						}
					}else if( this.edit_cron['repeat']['month'] == "*" ){
						for(var di=0;di<12;di++){
							month_range.push(di);
						}
					}

					var week_range = [];
					if( this.edit_cron['repeat']['weekday'].match(/^[0-9\,]+$/) ){
						var x = this.edit_cron['repeat']['weekday'].split(/\,/g);
						var w = [];
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								week_range.push( min );
							}
						}
					}else if( this.edit_cron['repeat']['weekday'] == "*" ){
						for(var di=0;di<=6;di++){
							week_range.push(di);
						}
					}

					console.log( JSON.stringify({"minutes":min_range,"hours":hour_range,"days":date_range,"months":month_range,"weeks":week_range},null,4) );

					var t = Date.now();
					var year = dt.getFullYear();

					var scan_months = 0;
					var first_min = dt.getMinutes();
					var first_hour = dt.getHours();
					var first_date = dt.getDate();
					var first_month = dt.getMonth();
					console.log( first_min );
					console.log( first_hour );
					console.log( first_date );
					console.log( first_month );
					var loops = 0;
					while( 1 ){
						var month_i=0;
						while( month_i<month_range.length ){
							var month = month_range[ month_i ];
							scan_months++;
							if( month < first_month ){
								console.log("Month Skipping: " + month);
								month_i++;
								continue;
							}else{
								first_month = -1;
							}
							var date_i=0;
							while( date_i<date_range.length ){
								var date = date_range[ date_i ];
								if( date < first_date ){
									console.log("Date Skipping: " + date);
									date_i++;
									continue;
								}
								var dt = new Date(Date.UTC(year,month,date,1,1,0));
								console.log( dt );
								if( week_range.indexOf( dt.getDay() ) == -1 ){
									console.log("Weekday skipping: " + dt.getDay() );
									date_i++;
									continue;
								}
								var hour_i=0;
								while( hour_i<hour_range.length ){
									var hour = hour_range[ hour_i ];
									if( hour < first_hour ){
										console.log("Hour Skipping: " + hour);
										hour_i++;
										continue;
									}
									var min_i=0;
									while( min_i<min_range.length ){
										var min = min_range[ min_i ];
										if( min < first_min ){
											console.log("Minute Skipping: " + min + " : " + first_min);
											min_i++;
											continue;
										}
										var dt = new Date(Date.UTC(year,month,date,hour,min,0));
										var dd = dt.toISOString();
										dd = dd.substr(0,16);
										dd = dd.replace("T", "   ");
										next_runs.push( dd );
										min_i++;
										if( next_runs.length > 20 ){
											break;
										}
									}
									first_min = -1;
									console.log("Next hour");
									hour_i++;
									if( next_runs.length > 20 ){
										break;
									}
								}
								first_hour = -1;
								first_min = -1;
								console.log("Next Date");
								date_i++;
								if( next_runs.length > 20 ){
									break;
								}
							}
							first_date = -1;
							first_hour = -1;
							first_min = -1;
							console.log("Next Month " + month_i + " " + month);
							month_i++;
							if( next_runs.length > 20 ){
								break;
							}
						}
						year++;
						if( scan_months > 24 && next_runs.length==0 ){
							console.log("Cron schedule not found in 24 months");
							this.cronerr2 = "Cron schedule not found in next 24 months";
							break;
						}
						first_month = -1;
						first_date = -1;
						first_hour = -1;
						first_min = -1;
						if( next_runs.length > 20 ){
							break;
						}
						loops++;
						if( loops > 100 ){
							console.log("Max Loops reached");
							break;
						}
					}
				}
				this.cron_times = next_runs;
			},
			save_cron: function(){
				this.cronerr = "";
				if( this.edit_cron['des'].match(/^[a-z0-9\.\(\)\!\@\%\&\*\ \r\n]{2,100}$/i) == null ){
					this.cronerr = "Cron description required";
					return;
				}
				if( this.edit_cron['fn_id'].match(/^[a-f0-9]{24}$/) == null ){
					this.cronerr = "Function required";
					return;
				}
				this.calc_cron();
				if( this.cronerr != "" ){
					return;
				}

				this.cronerr = "";
				this.cronmsg = "Saving Cron";
				axios.post("?", {
					"action":"task_cron_save", 
					"cron": this.edit_cron,
				}).then(response=>{
					this.cronmsg = "";
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.crons_load();
							this.cronpopup.hide();
						}else{
							this.cronerr = response.data['error'];
						}
					}else{
						this.cronerr = ( "Error: incorrect response" );
					}
				}).catch(error=>{
					this.cronerr = this.get_http_error__(error);
				});
			},
			delete_cronjob: function(di){
				if( confirm("Are you sure?" ) ){
					axios.post("?", {
						"action":"task_cron_delete", 
						"cron_id": this.cronjobs[ di ]['_id'],
					}).then(response=>{
						if( 'status' in response.data ){
							if( response.data['status']=="success"){
								this.crons_load();
							}else{
								alert(response.data['error']);
							}
						}else{
							alert( "Error: incorrect response" );
						}
					}).catch(error=>{
						alert( this.get_http_error__(error) );
					});
				}
			},
			crons_load: function(){
				this.cronloaderr = "";
				axios.post("?", {
					"action":"task_crons_load", 
				}).then(response=>{
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.cronjobs = response.data['data'];
						}else{
							this.cronloaderr = response.data['error'];
						}
					}else{
						this.cronloaderr = ( "Error: incorrect response" );
					}
				}).catch(error=>{
					this.cronloaderr = this.get_http_error__(error);
				});
			},
			cron_activate: function(di){
				axios.post("?", {
					"action":"task_cron_activate", 
					"cron_id": this.cronjobs[ di ]['_id']
				}).then(response=>{
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.cronjobs[ di ]['active'] = true;
						}else{
							alert( response.data['error'] );
						}
					}else{
						alert( "Error: incorrect response" );
					}
				}).catch(error=>{
					alert(  this.get_http_error__(error) );
				});
			},
			cron_deactivate: function(di){
				axios.post("?", {
					"action":"task_cron_deactivate", 
					"cron_id": this.cronjobs[ di ]['_id']
				}).then(response=>{
					if( 'status' in response.data ){
						if( response.data['status']=="success" ){
							this.cronjobs[ di ]['active'] = false;
						}else{
							alert( response.data['error'] );
						}
					}else{
						alert( "Error: incorrect response" );
					}
				}).catch(error=>{
					alert(  this.get_http_error__(error) );
				});
			},
			find_schedule: function( d ){
				if( d['type']=='onetime' ){
					return "run at " + d['onetime'].replace("T", " ").substr(0,16);
				}else if( d['type']=='repeat' ){
					var t = [];
					if( d['repeat']['minute'].match(/^[0-9\,]+$/) ){
						t.push( d['repeat']['minute'] + " minute(s)" );
					}else if( d['repeat']['minute'].match(/^\*\/[0-9]+$/) ){
						var m = d['repeat']['minute'].match(/^\*\/([0-9]+)$/);
						var min = Number(m[1]);
						t.push( "every " + min + " minutes" );
					}else if( d['repeat']['minute'] == "*" ){
						t.push( "every minute" );
					}

					if( d['repeat']['hour'].match(/^[0-9\,]+$/) ){
						t.push( "at " + d['repeat']['hour'] + " hour(s)" );
					}else if( d['repeat']['hour'].match(/^\*\/[0-9]+$/) ){
						var m = d['repeat']['hour'].match(/^\*\/([0-9]+)$/);
						var min = Number(m[1]);
						t.push( "at every " + min + " hour(s)" );
					}else if( d['repeat']['hour'] == "*" ){
						t.push( "at every hour" );
					}

					if( d['repeat']['day'].match(/^[0-9\,]+$/) ){
						t.push( " on " + d['repeat']['day'] + " date(s)" );
					}else if( d['repeat']['day'] == "*" ){
						t.push( " on every day" );
					}
					if( d['repeat']['month'].match(/^[0-9\,]+$/) ){
						var x = d['repeat']['month'].split(/\,/g);
						var mons = [];
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								mons.push(["January","February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][ min-1 ]);
							}
						}
						t.push( " in " + mons.join(" ") + " month(s)" );
					}
					if( d['repeat']['weekday'].match(/^[0-9\,]+$/) ){
						var x = d['repeat']['weekday'].split(/\,/g);
						var w = [];
						for(var i=0;i<x.length;i++){
							if( x[i]!="" ){
								var min = Number(x[i]);
								w.push(["Sundary", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"][ min ]);
							}
						}
						t.push( " on " + w.join( " " ) + " " );
					}
					return t.join( " " );
				}
			},
			cron_view_log: function(di){
				this.cron_log_keyword = "";
				this.cron_id = di;
				this.cron_log_popup = new bootstrap.Modal( document.getElementById('cron_log_modal') );
				this.cron_log_popup.show();
				this.cron_load_log();
			},
			cron_load_log: function(){
				this.cronlogmsg = "Loading...";
				this.cron_log_records = [];
				this.cronlogerr = "";
				axios.post("?", {
					"action":"task_load_cron_log", 
					"cron_id":this.cron_id,
					"keyword": this.cron_log_keyword,
				}).then(response=>{
					this.cronlogmsg = "";
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.cron_log_records=response.data['data'];
						}else{
							this.cronlogerr = response.data['error'];
						}
					}else{
						this.cronlogerr = ( "Error: incorrect response" );
					}
				}).catch(error=>{
					this.cronlogerr = ("Error: "+error.message);
				});
			},
			cron_load_log_next: function(){
				this.cronlogmsg = "Loading...";
				var last = this.cron_log_records[ this.cron_log_records.length-1 ]['_id'];
				this.cron_log_records = [];
				this.cronlogerr = "";
				axios.post("?", {
					"action":"task_load_cron_log", 
					"cron_id":this.cron_id,
					"keyword": this.cron_log_keyword,
					"last": last
				}).then(response=>{
					this.cronlogmsg = "";
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.cron_log_records=response.data['data'];
						}else{
							this.cronlogerr = response.data['error'];
						}
					}else{
						this.cronlogerr = ( "Error: incorrect response" );
					}
				}).catch(error=>{
					this.cronlogerr = ("Error: "+error.message);
				});
			},
			bgtasks_load: function(){
				this.bgloaderr = "";
				axios.post("?", {
					"action":"task_bg_load", 
				}).then(response=>{
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.bgtasks = response.data['data'];
						}else{
							this.bgloaderr = response.data['error'];
						}
					}else{
						this.bgloaderr = ( "Error: incorrect response" );
					}
				}).catch(error=>{
					this.bgloaderr = this.get_http_error__(error);
				});
			},


			load_aws_credentail_profiles: function(){
				axios.post("?", {
					"action":"load_aws_credential_profiles", 
				}).then(response=>{
					if( 'status' in response.data ){
						if( response.data['status']=="success"){
							this.bgtasks = response.data['data'];
						}else{
							this.bgloaderr = response.data['error'];
						}
					}else{
						this.bgloaderr = ( "Error: incorrect response" );
					}
				}).catch(error=>{
					this.bgloaderr = this.get_http_error__(error);
				});
			},

			get_http_error__: function(e){
				if( typeof(e) == "object" ){
					if( 'status' in e ){
						if( 'error' in e ){
							return e['error'];
						}else{
							return "There was no error";
						}
					}else if( 'response' in e ){
						var s = e.response.status;
						if( typeof( e['response']['data'] ) == "object" ){
							if( 'error' in e['response']['data'] ){
								return s + ": " + e['response']['data']['error'];
							}else{
								return s + ": " + JSON.stringify(e['response']['data']).substr(0,100);
							}
						}else{
							return s + ": " + e['response']['data'].substr(0,100);
						}
					}else if( 'message' in e ){
						return e['message'];
					}else{
						return "Incorrect response";
					}
				}else{
					return "Invalid response"
				}
			},




		}
});

const component_default = {
	template: `<div>-</div>`
};

const routes = [
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tasks', component: component_default  },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tasks/queue/', component: component_default  },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tasks/active/', component: component_default  },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tasks/bg/', component: component_default  },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tasks/cron/', component: component_default  },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tasks/cron/:cron_id', component: component_default  },
];

const router = VueRouter.createRouter({
	history: VueRouter.createWebHistory(), routes
});

router.beforeEach((to,from)=>{
	console.log( to );
	var vpath = to.path.replace( '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tasks', '');
	vpath = vpath.substr(1,9999);
	var x = vpath.split(/\//g);
	console.log( x );
	//to.path

	if( x[0] == "" || x[0] == 'queue' ){
		router.app_object.open_tab('queue');
	}else{
		router.app_object.open_tab(x[0]);
	}
});
router.afterEach((to, from) => {
});

app.use(router);
var app1 = app.mount("#app");
router.app_object = app1;



</script>
