<script src="<?=$config_global_apimaker_path ?>ace/src-noconflict/ace.js" ></script>
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
			sdk__: <?=json_encode($sdk) ?>,
			msg__: "", err__: "",
			float_msg__: "", float_err__: "",

			tab: "structure",
			current_method: -1,
			vshow: true,

			popup_type: "", popup_modal: false, popup_title: "",

			structure_block_index: -1,
			new_method: {},
			component_new: {"name": "name", "des": ""},

			test_environments__: <?=json_encode($test_environments) ?>,
			ace_editors__: {'methods':[]},

			control_frame: false,

		};
	},
	mounted(){
		if( 'structure' in this.sdk__ == false ){
			this.sdk__['structure'] = {
				"version": 1,
				"body": ``
			};
		}
		for(var i=0;i<this.sdk__['structure']['methods'].length;i++){
			this.ace_editors__['methods'].push(false);
		}
		console.log( JSON.stringify(this.sdk__,null,4) );
		setTimeout(this.initialize_editors,1000);
	},
	methods: {


		open_method: function(vi){
			if( this.current_method == vi ){
				this.current_method = -1;
			}else{
				this.current_method = vi;
			}
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
		structure_add_method: function( vpos ){
			var n = prompt("New method name");
			if( n ){
				this.un_initialize_editors();
				this.current_method = -1;
				this.new_method = JSON.parse( JSON.stringify(this.new_method_template) );
				this.new_method['name'] = n;
				this.sdk__['structure']['methods'].push(JSON.parse( JSON.stringify(this.new_method)));
				this.ace_editors__['methods'].push(false);
				this.current_method = this.ace_editors__['methods'].length-1;
				setTimeout(this.initialize_editors,1000);
			}
		},
		structure_add_method2: function(){
			this.popup_type = 'new_method';
			this.popup_title = "New Method";
			this.popup_modal = new bootstrap.Modal(document.getElementById('popup_modal'));
			this.popup_modal.show();
			this.popup_modal.hide();
		},
		structure_delete_block: function(vi){
			if( confirm("Are you sure?") ){
				this.un_initialize_editors();
				this.sdk__['structure']['methods'].splice(vi,1);
				this.ace_editors__['methods'].splice( vi,1 );
				setTimeout(this.initialize_editors,1000);
			}
		},
		un_initialize_editors: function(){
			console.log( this.sdk__['structure']['methods'] );
			console.log( this.ace_editors__['methods'] );
			for( var i=0;i<this.sdk__['structure']['methods'].length;i++){
				this.sdk__['structure']['methods'][i]['code'] = this.ace_editors__['methods'][ i ].getValue();
				this.ace_editors__['methods'][ i ].remove();
				document.getElementById("method_" + i).removeEventListener('keyup', () => {this.setEditorHeight(i);});
			}
		},
		initialize_editors: function(){
			for( var i=0;i<this.sdk__['structure']['methods'].length;i++){
				this.ace_editors__['methods'][ i ] = ace.edit("method_" + i);
				this.ace_editors__['methods'][ i ].setOptions({
					enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
				});
				this.ace_editors__['methods'][ i ].session.setMode("ace/mode/php");
				this.ace_editors__['methods'][ i ].setValue( this.sdk__['structure']['methods'][ i ]['code'] );
				document.getElementById("method_" + i).setAttribute("data-id", i);
				document.getElementById("method_" + i).addEventListener('keyup', (e) => {this.setEditorHeight(e);});
			}
		},
		setEditorHeight: function( e ){
			console.log( e );
			console.log( e.target.parentNode );
			var vi = Number(e.target.parentNode.getAttribute("data-id"));
			var h = ( this.ace_editors__['methods'][ vi ].session.getLength() + 3 )*20;
			if(h < 400) {
			}else{
				h = 400;
			}
			document.getElementById("method_" + vi).style.height=h+"px";
			this.ace_editors__['methods'][ vi ].resize();
		},
		save_sdk: function(){
			for( var i=0;i<this.sdk__['structure']['methods'].length;i++){
				this.sdk__['structure']['methods'][i]['code'] = this.ace_editors__['methods'][ i ].getValue();
			}
			this.float_err__ = "";
			this.float_msg__ = "";
			var d = JSON.parse( JSON.stringify( this.sdk__['structure'] ) );
			if( typeof(d) == "undefined" ){
				this.float_err__ = "Not initialized";return;
			}
			this.float_msg__  = "Saving...";
			axios.post("?", {
				"action": "save_sdk_structure",
				"data": this.sdk__['structure'],
			}).then( response=>{
				this.float_msg__ = "";
				if( 'status' in response.data ){
					if( response.data['status'] == "success" ){
						this.float_msg__ = "SDK saved successfully";
						setTimeout( function(v){ v.float_msg__ = ""; }, 3000, this );
					}else{
						this.float_err__ = response.data['error'];
					}
				}
			}).catch( error=>{
				this.float_err__ = error.message
			});
		}
	}
});

app.mount("#app");
</script>