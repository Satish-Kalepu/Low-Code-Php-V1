<style>
	.redisk{ padding:0px 5px; border-bottom:1px solid #ccc; cursor:pointer; }
	.redisk:hover{ background-color:#f8f8f8; }
</style>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div class="btn btn-sm btn-outline-secondary float-end" v-on:click="show_configure()" >Configure</div>
			<div class="h3 mb-3">Code Export</div>

			<div style="position:relative;overflow: auto; height: calc( 100% - 130px );">

				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

				<p>Export app as deployable bundle</p>

				<div class="btn btn-outline-dark btn-sm mb-2" >AWS Lambda Function</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >Azure Function</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >Google Cloud Function</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >Digital Ocean Node</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >Docker Container</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >PHP-fpm Nginx Application</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >PHP Apache Application</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >PHP Laravel Application</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >Docker Bundle</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >ReactJs APP</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >VueJs APP</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >NodeJs + ExpressJs App</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >Java APP</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >.Net APP</div> &nbsp; 
				<div class="btn btn-outline-dark btn-sm mb-2" >Python Django APP</div> &nbsp; 

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
