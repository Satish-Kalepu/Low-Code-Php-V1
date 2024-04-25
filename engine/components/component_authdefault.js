const app_AuthDefault = {
	data: function(){
		return {
			"a":1, "init_error": "",
			"user": "",
			"pass": "", 
			"msg": "","err": "","cmsg": "","cerr": "",
			"captcha": "",
			"captcha_img": "",
			"captcha_code": "",
			"cap": false,
			"login": false,
			"session_key": "",
		};
	},
	props: ["data-app-options"],
	mounted: function(){
		if( typeof(page_data) != "object" ){
			this.init_error = "page_data missing";
		}else if( "login_session_token" in page_data == false ){
			this.init_error = "session token missing";
		}else if( "global_api_url" in page_data == false ){
			this.init_error = "api url missing";
		}else{
			this.init_error = "";
		}
	},
	methods: {
		dologin: function(){
			if( this.init_error ){
				alert(this.init_error);exit;
			}
			if( this.cap == false || this.captcha == "" ){
				this.user = this.user.trim();
				if( this.user.match(/^[a-z0-9]+$/) && this.pass.length>3 ){
					this.get_captcha();
				}
			}else if( this.captcha ){
				this.msg = "Submitting...";
				this.err = "";
				axios.post(page_data['global_api_url'] + "auth/user_auth_captcha", {
					"action": "user_auth_captcha",
					"username": this.user,
					"password": this.pass,
					"captcha": this.captcha,
					"code": this.captcha_code,
					"token": this.token,
				}, {"headers":{"Access-Key": page_data['login_session_token']} }).then(response=>{
					this.msg = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.msg = "Login successfull";
									this.login = true;
									this.session_key = response.data['access-key'];
									this.captcha = "";
								}else if( response.data['status'] == "TokenError" ){
									this.err = "Error: TokenError: " + response.data['error'] + ". Please reload page...";
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
				}).catch(error=>{
					this.msg = "";
					if( typeof(error.response.data)=="object" ){
						if( "error" in error.response.data ){
							this.get_captcha();
							this.err = error.response.data['error'];
							this.captcha = "";
						}else{
							this.err = "Unknown response: " + error.response.data;
						}
					}else{
						this.err = error.message;
					}
				});
			}
		},
		get_captcha: function(){
			this.msg = "Loading capthca ...";
			this.cap_img = "";
			this.cerr = "";
			axios.post(page_data['global_api_url'] + "captcha/get", {
				"action":"captcha_get"
			}, {"headers":{"Access-Key": page_data['login_session_token']} }).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'img' in response.data && 'code' in response.data ){
							this.cap = true;
							this.captcha_img = response.data['img'];
							this.captcha_code = response.data['code'];
						}else{
							this.cerr = "Error loading captcha";
						}
					}else{
						this.cerr = "Error loading captcha";
					}
				}else{
					this.cerr = "Error loading captcha";
				}
			}).catch(error=>{
				this.msg = "";
				if( typeof(error.response.data)=="object" ){
					if( "error" in error.response.data ){
						this.cerr = error.response.data['error'];
					}else{
						this.cerr = "Unknown response: " + error.response.data;
					}
				}else{
					this.cerr = error.message;
				}
			});
		}
	},
	template: `<div>
		<div class="card" style="max-width:400px;" >
			<div class="card-header" >Login Form</div>
			<div class="card-body" >
				<template v-if="login==false" >
				<table width="100%" >
				<tr>
					<td>Username</td>
					<td><input type="text" v-model="user" class="form-control form-control-sm" placeholder="Username" ></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="password" v-model="pass" class="form-control form-control-sm" placeholder="Password" ></td>
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
					<td><input type="button" class="btn btn-outline-success btn-sm" value="Login" v-on:click="dologin()" ></td>
				</tr>
				</table>
				<div v-if="msg" class="text-primary" >{{ msg }}</div>
				<div v-if="err" class="text-danger" >{{ err }}</div>
				<div v-if="cerr" class="text-danger" >{{ cerr }}</div>

				</template>
				<div v-else>
					<div class="alert alert-success" >Login successfull</div>
					<div>Session key issued: {{ this.session_key }}</div>
				</div>

			</div>
		</div>
		<div class="alert alert-danger" v-if="init_error">
			<p>{{ init_error }}</p>
		</div>
	</div>`
};