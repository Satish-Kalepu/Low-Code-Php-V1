
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
							<td>Topic</td><td>Function</td><td>Type</td><td>Queue</td><td>Processed</td><td>Total</td><td>&nbsp;</td>
						</tr>
						<tr v-for="d,di in settings['internal']">
							<td>{{ d['topic'] }}</td>
							<td><a v-bind:href="path+'functions/'+d['fn_id']+'/'+d['fn_vid']" >{{ d['fn'] }}</a></td>
							<td>{{ d['type']=='s'?'Single Thread':'Multi Threaded' }}</td>
							<td align="center">0</td>
							<td align="center">0</td>
							<td align="center">0</td>
							<td>
								<div class="btn btn-outline-dark btn-sm me-2" v-on:click="edit_internal_queue(di)" ><i class="fa fa-edit"></i></div>
								<div class="btn btn-outline-danger btn-sm me-2" v-on:click="delete_internal_queue(di)"  ><i class="fa fa-remove"></i></div>
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

				<div v-if="smsg" class="alert alert-primary" >{{ smsg }}</div>
				<div v-if="serr" class="alert alert-danger" >{{ serr }}</div>

				
				

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
							<select v-model="new_queue['type']" class="form-select form-select-sm w-auto">
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
						<td><input type="button" class="btn btn-outline-dark btn-sm" value="Add Queue" v-on:click="save_queue" ></td>
					</tr>
				</table>
				<div v-if="ipmsg" class="alert alert-primary" >{{ ipmsg }}</div>
				<div v-if="iperr" class="alert alert-danger" >{{ iperr }}</div>
				<pre>{{ new_queue }}</pre>

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
				"settings": <?=json_encode($app['queues']) ?>,
				"smsg": "", "serr":"","msg": "", "err":"","ipmsg": "", "iperr":"","kmsg": "", "kerr":"",
				keyword: "",
				token: "",
				saved: <?=($saved?"true":"false") ?>,
				functions: [], popup: false, vip: false,
				tab: "queue",
				new_queue: {
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
				if( Number(this.new_queue['con']) < 1 || Number(this.new_queue['con']) > 5 ){
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
