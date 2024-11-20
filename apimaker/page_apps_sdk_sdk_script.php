<script src="<?=$config_global_apimaker_path ?>ace/src-min/ace.js" ></script>
<script src="<?=$config_global_apimaker_path ?>js/beautify-html.js" ></script>
<script src="<?=$config_global_apimaker_path ?>js/beautify-css.js" ></script>
<script src="<?=$config_global_apimaker_path ?>js/beautify.js" ></script>
<script src="<?=$config_global_apimaker_path ?>js/htmlclean.js" ></script>
<script>

//import beautify from "./ace/ext/beautify";

<?php
$components = [];
?>

var global_frame = false;

var app = Vue.createApp({	
	data(){
		return {
			rootpath: '<?=$config_global_apimaker_path ?>',
			path: '<?=$config_global_apimaker_path ?>apps/<?=$config_param1 ?>/',
			sdkpath: '<?=$config_global_apimaker_path ?>apps/<?=$config_param1 ?>/sdk/<?=$config_param3 ?>/<?=$config_param4 ?>',
			global_data__: {"s":"sss"},
			app_id: "<?=$config_param1 ?>",
			sdk_id: "<?=$config_param3 ?>",
			sdk_version_id: "<?=$config_param4 ?>",
			app__: <?=json_encode($app) ?>,
			main_sdk__: <?=json_encode($main_sdk) ?>,
			sdk__: <?=json_encode($sdk) ?>,
			msg__: "", err__: "", set_msg: "", set_err: "",
			float_msg__: "", float_err__: "",

			tab: "script",
			current_method: -1,
			vshow: true,

			popup_type: "", popup_modal: false, popup_title: "",

			structure_block_index: -1,
			new_method: {},
			component_new: {"name": "name", "des": ""},

			test_environments__: <?=json_encode($test_environments) ?>,
			ace_editor_body: false,

			control_frame: false,
			default_body: `<?=base64_encode(file_get_contents("page_apps_sdk_sdk_default_class.php")) ?>`,
			default_help: atob(`<?=base64_encode(file_get_contents("page_apps_sdk_sdk_default_help.php")) ?>`),

		};
	},
	mounted(){

		if( 'raw' in this.sdk__ == false ){
			var v  = atob(this.default_body)+'';
			this.sdk__['raw'] = v
		}else{
			this.sdk__['raw'] = atob(this.sdk__['raw']);
		}
		setTimeout(this.initialize_editors, 500);

	},
	methods: {
		add_keyword: function(){
			this.main_sdk__['keywords'].push("");
		},
		delete_keyword: function(vi){
			if( this.main_sdk__['keywords'].length == 1 ){
				alert("minimum one keyword is required for search");return;
			}
			this.main_sdk__['keywords'].splice(vi,1);
		},
		clean_keyword: function(vi){
			this.main_sdk__['keywords'][vi] = this.cleanit2( this.main_sdk__['keywords'][vi] );
		},
		nchange: function(){
			this.main_sdk__['name'] = this.cleanit(this.main_sdk__['name']);
			if( this.main_sdk__['des']=="" ){
				this.main_sdk__['des'] = this.main_sdk__['name']+'';
			}
		},
		cleanit(v){
			v = v.replace( /\-/g, "DASH" );
			v = v.replace( /\_/g, "UDASH" );
			v = v.replace( /\W/g, "-" );
			v = v.replace( /UDASH/g, "_" );v = v.replace( /DASH/g, "-" );
			v = v.replace( /[\-]{2,5}/g, "-" );
			v = v.replace( /[\_]{2,5}/g, "_" );
			return v;
		},
		cleanit2(v){
			v = v.replace( /\-/g, "DDD" );
			v = v.replace( /\_/g, "UUU" );
			v = v.replace( /\./g, "DTDT" );
			v = v.replace( /\ /g, "SPACE" );
			v = v.replace( /[\W]+/g, "" );
			v = v.replace( /SPACE/g, " " );
			v = v.replace( /[\ ]{2,5}/g, " " );
			v = v.trim();
			v = v.replace( /UUU/g, "_" );v = v.replace( /DDD/g, "-" );v = v.replace( /DTDT/g, "." );
			v = v.replace( /[\-]{2,5}/g, "-" );
			v = v.replace( /[\_]{2,5}/g, "_" );			
			return v;
		},

		open_tab: function(vi){
			this.tab = vi+'';
		},
		is_single_test_environment__: function(){
			//"t"=> "custom",	"u"=> $dd['url'],"d"=> $dd['domain'],"e"=> $dd['path'],
			if( this.test_environments__.length == 1 ){
				return true;
			}
			return false;
		},
		get_test_environment_url__: function(vi){
			var v = this.test_environments__[vi];
			if( v['t'] == 'custom' ){
				return v['u'] + this.sdk__['name'];
			}else if( v['t'] == 'cloud' ){
				return v['u'] + this.sdk__['name'];
			}else if( v['t'] == 'cloud-alias' ){
				return v['u'] + this.page__['name'];
			}
		},
		previewit: function(){
			this.url_modal = new bootstrap.Modal(document.getElementById('url_modal'));
			this.url_modal.show();
		},
		un_initialize_editors: function(){
			console.log( this.sdk__['methods'] );
			console.log( this.ace_editors__['methods'] );
			this.sdk__['raw'] = this.ace_editor_body.getValue();
			this.ace_editor_body.remove();
			document.getElementById("editor_body").removeEventListener('keyup', (e) => {this.setEditorHeight(e);});
		},
		initialize_editors: function(){
			this.ace_editor_body = ace.edit("editor_body");
			this.ace_editor_body.setOptions({
				enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
			});
			this.ace_editor_body.session.setMode("ace/mode/php");
			this.ace_editor_body.setValue( this.sdk__['raw'] ,-1);
			document.getElementById("editor_body").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
			setTimeout(this.setEditorHeight,1000);
		},
		setEditorHeight: function( e ){
			var h = ( this.ace_editor_body.session.getLength() + 3 )*20;
			document.getElementById("editor_body").style.height=h+"px";
			this.ace_editor_body.resize();
		},
		save_sdk: function(){
			this.sdk__['raw'] = this.ace_editor_body.getValue();
			this.float_err__ = "";
			this.float_msg__  = "Saving...";
			axios.post("?", {
				"action": "save_sdk_structure",
				"raw": this.sdk__['raw'],
			}).then( response=>{
				this.float_msg__ = "";
				if( 'status' in response.data ){
					if( response.data['status'] == "success" ){
						this.float_msg__ = "SDK saved successfully";
						setTimeout( function(v){ v.float_msg__ = ""; }, 3000, this );
					}else if( 'msg' in response.data ){
						this.float_err__ = response['data']['error'] + ' ' + response.data['msg'];
						if( 'script' in response.data ){
							for( var i in response.data ){
								console.log(i);
								console.log(response.data[i]);
							}
						}
					}else{
						this.float_err__ = response.data['error'];
					}
				}
			}).catch( error=>{
				this.float_err__ = error.message
			});
		},
		show_settings: function(){
			this.settings_popup = new bootstrap.Modal( document.getElementById('settings_modal') );
			this.settings_popup.show();
		},
		save_settings: function(){
			this.set_msg = "";
			this.set_err = "";
			this.main_sdk__['name'] = this.cleanit( this.main_sdk__['name']  );
			if( this.main_sdk__['name'].match(/^[a-z][a-z0-9\.\-\_]{2,150}$/i) == null ){
				this.set_err = "Name incorrect. Special chars not allowed. Length minimum 3 max 100";
				return false;
			}
			if( this.main_sdk__['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\ \r\n]{2,300}$/i) == null ){
				this.set_err = "Description incorrect. Special chars not allowed. Length minimum 5 max 200";
				return false;
			}
			for( var i=0;i<this.main_sdk__['keywords'].length;i++){
				if( this.main_sdk__['keywords'][i].trim() == "" ){
					this.main_sdk__['keywords'].splice(i,1);
					i--;
				}
			}
			if( this.main_sdk__['keywords'].length == 0){
				this.set_err = "One keyword is required for search";return;
			}
			this.set_msg = "Saving...";
			this.set_err = "";
			axios.post("?", {
				"action": "save_settings",
				"name": this.main_sdk__['name'],
				"des": this.main_sdk__['des'],
				"keywords": this.main_sdk__['keywords'],
			}).then(response=>{
				this.set_msg = "";
				if( response.data['status'] == "success" ){
					this.set_msg = "Settings updated successfully";

				}else{
					this.set_err = response.data['error'];
				}
			}).catch(error=>{
				this.set_err = "Error: " + this.get_http_error__(error);
			});
		}
	}
});

app.mount("#app");
</script>