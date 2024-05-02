
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div class="btn btn-sm btn-outline-secondary float-end" v-on:click="show_configure()" >Configure</div>
			<div class="h3 mb-3">Event Hub</div>

			<div style="position:relative;overflow: auto; height: calc( 100% - 130px );">

				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

				<p>Events & Webhooks</p>


				<img style="width:100%;" src="<?=$config_global_apimaker_path ?>images/eventshub.png" >

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
				"settings": {},
				"smsg": "", "serr":"","msg": "", "err":"","kmsg": "", "kerr":"",
				keyword: "",
				token: "",
				saved: <?=($saved?"true":"false") ?>,
				keys: [], popup: false,
				show_key: {}
			};
		},
		mounted:function(){
			
		},
		methods: {
			show_configure: function(){
				this.popup = new bootstrap.Modal(document.getElementById('settings_modal'));
				this.popup.show();
			},
			
		}
}).mount("#app");
</script>
