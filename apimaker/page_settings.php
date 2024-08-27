<div id="app" >

	<div  class="leftbar"  >
		<a class="left_btn" href="<?=$config_global_apimaker_path ?>">APPs</a>
		<a class="left_btn" href="<?=$config_global_apimaker_path ?>users">Users</a>
		<a class="left_btn" href="<?=$config_global_apimaker_path ?>settings">Settings</a>
	</div>

	<div style="position: fixed;left:150px; top:40px; height: 40px; width:calc( 100% - 150px ); background-color: white; border-bottom:1px solid #ccc; " >
		<div style="padding: 5px 10px;" >
			<div>
				<h5 class="d-inline">Settings</h5>
			</div>
		</div>
	</div>

	<div id="content_div" style="position: fixed;left:150px; top:90px; height: calc( 100% - 90px );width:calc( 100% - 150px ); overflow: auto; " >
		<div style="padding: 10px;" >

				<div style="border: 1px solid #ccc; margin-bottom: 20px; " >
					<div style="background-color:#e8e8e8; padding: 5px 10px;">Backup Hub</div>
					<div style="padding:10px;">

						<div v-if="is_hub_loggedin==false" >
							<p>You can take cloud backup of your app in the Hub, which you can access from anywhere.</p>
							<p><input type="button" class="btn btn-outline-dark btn-sm" value="Login" v-on:click="hub_login()" ></p>

						</div>
						<template v-else >

							<input type="button" class="btn btn-outline-danger btn-sm" style="float:right;" value="UnLink" v-on:click="hub_reset" >
							<p>Backup Hub is linked with user: {{hub_login_email}}  </p>

						</template>

					</div>
				</div>



		</div>
	</div>


	<div class="modal fade" id="hub_login_modal" tabindex="-1" >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Backup Hub Login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div>Email</div>
            <input type="text" spellcheck="false" autocomplete="false" class="form-control form-control-sm" v-model="hub_login_d['email']" placeholder="Email">
            <div>Password</div>
            <input type="password" spellcheck="false" autocomplete="false" class="form-control form-control-sm" v-model="hub_login_d['password']" >
            <div>&nbsp;</div>
            <div><input type="button" class="btn btn-outline-dark btn-sm" value="Login" v-on:click="hub_login_now()" ></div>
            <div v-if="hub_login_msg" class="alert alert-success" >{{ hub_login_msg }}</div>
            <div v-if="hub_login_err" class="alert alert-success" >{{ hub_login_err }}</div>
        </div>
      </div>
    </div>
  </div>


</div>
<script>
var app = Vue.createApp({
	data(){
		return {
			path: "<?=$config_global_apimaker_path ?>",

			hub_restore_task: <?=$hub_restore_task?"true":"false" ?>,
			hub_restore_status: 0,
			hub_restore_msg: "",
			hub_restore_error: "",

			hub_login_msg: "", hub_login_err: "",
			hub_backup_msg: "", hub_backup_err: "",
			hub_create_msg: "", hub_create_err: "",
			hub_versions_msg: "", hub_versions_err: "",
			hub_login_form: false,
			is_hub_loggedin: <?=$is_hub_login?"true":"false" ?>,
			hub_login_email: "<?=$hub_login_email ?>",
			hub_login_d: {"email": "", "password": ""},
			hub_create: {"name": "", "des": "", "visibility": "public"},
			hub_link_modal: false,
			hub: <?=isset($app['hub'])?json_encode($app['hub']):"{}" ?>,
			hub_backup_busy: false,
			hub_restore_modal: false,
			hub_versions: [],
			hub_repo_list: [],

		};
	},
	mounted(){

	},
	methods: {
		hub_login: function(){
			this.hub_login_modal = new bootstrap.Modal(document.getElementById('hub_login_modal'));
			this.hub_login_modal.show();
			this.hub_login_msg = ""; this.hub_login_err = "";
		},
		hub_login_now: function(){
			this.hub_login_msg = "Checking...";
			this.hub_login_err = "";
			axios.post("?",{
				"action":"exports_hub_login",
				"login":{
					"email": btoa(this.hub_login_d['email']),
					"password": btoa(this.hub_login_d['password']),
				}
			}).then(response=>{
				this.hub_login_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.is_hub_loggedin = true;
								this.hub_login_email = this.hub_login_d['email']+'';
								this.hub_login_modal.hide();
							}else{
								alert("Token error: " + response.data['error']);
								this.hub_login_err = "Token Error: " + response.data['error'];
							}
						}else{
							this.hub_login_err = "Incorrect response";
						}
					}else{
						this.hub_login_err = "Incorrect response Type";
					}
				}else{
					this.hub_login_err = "Response Error: " + response.status;
				}
			}).catch(error=>{
				this.hub_login_err = "Response Error: " + error.message;
			});
		},
		hub_reset: function(){
			if( confirm("Are you sure to Logout from Backup Hub?") ){
				axios.post("?",{
					"action":"exports_hub_logout",
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.is_hub_loggedin = false;
									this.hub_login_email = '';
								}else{
									alert(response.data['error']);
								}
							}else{
								alert("Incorrect response");
							}
						}else{
							alert("Incorrect response Type");
						}
					}else{
						alert("Response Error: " + response.status);
					}
				}).catch(error=>{
					alert("Response Error: " + error.message);
				});
			}
		},		
	},

}).mount("#app");
</script>