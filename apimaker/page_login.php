
<div id="app">
	<div class="card mx-auto border-0 ">
		<div class="card-body">
				<h3>LOGIN</h3>
				<table cellpadding="5">
					<tr>
						<td>User</td>
						<td><input type="text" class="form-control form-control-sm" v-model="user" placeholder="UserName" autocomplete="off" ></td>
					</tr>
					<tr>
						<td>Pass</td>
						<td><input type="password" class="form-control form-control-sm" v-model="pass" placeholder="Password" autocomplete="off" ></td>
					</tr>
					<tr v-if="cap">
						<td></td>
						<td><img v-bind:src="captcha_img" style="min-width:100px;min-height:50px;" /><u style="cursor: pointer;" v-on:click="get_captcha">refresh</u></td>
					</tr>
					<tr v-if="cap">
						<td>Code</td>
						<td><input type="text" class="form-control form-control-sm" v-model="captcha" placeholder="Captcha Code" autocomplete="off" ></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="button" class="btn btn-primary btn-sm" value="LOGIN" v-on:click="dologin" ></td>
					</tr>
				</table>
				<div v-if="msg" class="text-primary" >{{ msg }}</div>
				<div v-if="err" class="text-danger" >{{ err }}</div>
		</div>
	</div>
</div>

<script>
const { createApp } = Vue
var app = createApp({
  data() {
    return {
      user: "",
      pass: "", 
      msg: "",
      err: "",
      captcha: "",
      captcha_img: "",
      captcha_code: "",
      cap: false,
      token: "<?=get_token("login", 15) ?>",
      token_err: "",
    }
  },
  mounted: function(){

  },
  methods: {
  	dologin: function(){
  		if( this.cap == false ){
  			this.user = this.user.trim();
  			if( this.user.match(/^[a-z0-9]+$/) && this.pass.length>3 ){
	  			this.cap = true;
	  			this.get_captcha();
  			}
  		}else if( this.captcha ){
  			this.msg = "Submitting...";
  			this.err = "";
  			axios.post("<?=$config_global_apimaker_path ?>login",{
  				"action": "login",
  				"user": this.user,
  				"pass": "a1b2c3d4ef"+btoa(this.pass),
  				"captcha": this.captcha,
  				"captcha_code": this.captcha_code,
  				"token": this.token,
  			}).then(response=>{
	  			this.msg = "";
					if( response.status == 200 ){
	  				if( typeof(response.data) == "object" ){
	  					if( 'status' in response.data ){
	  						if( response.data['status'] == "success" ){
	  							document.location = "home?event=success";
	  						}else if( response.data['status'] == "TokenError" ){
									this.err = "Error: TokenError: " + response.data['error'] + ". Reloading...";
									setTimeout("document.location.reload()",2000);
	  						}else{
	  							this.get_captcha();
	  							this.captcha = "";
									this.err = "Error: " + response.data['error'];
	  						}
	  					}else{
	  						this.err = "Error: incorrect response";
	  					}
	  				}else{
							this.err = "Error: unexpected response";
						}
	  			}else{
						this.err = "Error: http: " . response.status;
					}
  			});
  		}
  	},
  	get_captcha: function(){
  		this.msg = "Loading capthca ...";
  		this.cap_img = "";
  		this.err = "";
  		axios.get("<?=$config_global_apimaker_path ?>captcha?action=getcaptcha").then(response=>{
  			this.msg = "";
  			if( response.status == 200 ){
  				if( typeof(response.data) == "object" ){
  					if( 'img' in response.data && 'code' in response.data ){
  						this.captcha_img = response.data['img'];
  						this.captcha_code = response.data['code'];
  					}else{
  						this.err = "Error loading captcha";
  					}
  				}else{
						this.err = "Error loading captcha";
					}
  			}else{
					this.err = "Error loading captcha";
				}
  		});
  	}
  }
}).mount('#app');
</script>
