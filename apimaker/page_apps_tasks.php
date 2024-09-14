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

			<div style="position:relative;overflow: auto; height: calc( 100% - 130px );">

				<ul class="nav nav-tabs">
					<li class="nav-item">
						<a v-bind:class="{'nav-link':true,'active':tab=='queue'}" v-on:click="tab='queue'" href="#">Queues</a>
					</li>
					<li class="nav-item">
						<a v-bind:class="{'nav-link':true,'active':tab=='active'}" v-on:click="tab='active'"  href="#">Active Tasks</a>
					</li>
					<li class="nav-item">
						<a v-bind:class="{'nav-link':true,'active':tab=='background'}" v-on:click="tab='background'" href="#">Background Jobs</a>
					</li>
					<li class="nav-item">
						<a v-bind:class="{'nav-link':true,'active':tab=='crons'}" v-on:click="tab='crons'" >Cron Jobs</a>
					</li>
				</ul>
				<div>&nbsp;</div>

				<div v-if="fmsg" class="alert alert-primary" >{{ fmsg }}</div>
				<div v-if="ferr" class="alert alert-danger" >{{ ferr }}</div>
				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

				<div v-if="tab=='queue'" >

					<p style="border-bottom:1px solid #ccc; background-color:#f0f0f0;">Internal Queues</p>

					<table class="table table-bordered table-sm w-auto" >
						<tr>
							<td>#<td>Topic</td><td>Function</td><td>Type</td>
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

					<p style="border-bottom:1px solid #ccc; background-color:#f0f0f0;">External Queues</p>

					<table class="table table-bordered table-sm w-auto" >
						<tr>
							<td>Topic</td><td>Type</td><td>Queue</td><td>Processed</td><td>Total</td>
						</tr>
					</table>

					<p><div class="btn btn-outline-dark btn-sm" v-on:click="show_external_add" >Add Queue</div></p>

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

</div>
<script>

var app = Vue.createApp({
		"data": function(){
			return {
				"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
				"taskpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/tasks/",
				"test_environments": <?=json_encode($test_environments) ?>,
				"app_id" : "<?=$app['_id'] ?>",
				"settings": {'internal':[],'external':[]},
				"smsg": "", "serr":"",
				"msg": "", "err":"",
				"ipmsg": "", "iperr":"",
				"qlmsg": "", "qlerr":"",
				"kmsg": "", "kerr":"",
				"internal_queue_log_popup": false,
				"internal_queue_id": "",
				"internal_queue_index": -1,
				"internal_log": [],
				"queue_log_keyword": "",
				"start_queue_popup": false,	
				"qei": -1,
				"keyword": "",
				"token": "",
				"saved": <?=($saved?"true":"false") ?>,
				"functions": [], popup: false, vip: false,
				"tab": "queue",
				"new_queue": {
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
				}
			};
		},
		mounted:function(){
			this.load_queues();
			this.load_functions();
		},
		methods: {
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
				this.new_queue = {
					"type": "s", "topic": "", "des": "",
					"timeout": 30, "ret": 1, "delay": 0, "con": 2,
					"retry": 0, "wait": 10,"fn": "","fn_id": "","fn_vid": "",
				};
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
		}
}).mount("#app");
</script>


