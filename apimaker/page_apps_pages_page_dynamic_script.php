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
			pagepath: '<?=$config_global_apimaker_path ?>apps/<?=$config_param1 ?>/pages/<?=$config_param3 ?>/<?=$config_param4 ?>',
			global_data__: {"s":"sss"},
			app_id: "<?=$config_param1 ?>",
			page_id: "<?=$config_param3 ?>",
			page_version_id: "<?=$config_param4 ?>",
			app__: <?=json_encode($app) ?>,
			page__: <?=json_encode($page) ?>,
			msg__: "", err__: "",
			float_msg__: "", float_err__: "",

			tab: "structure",
			comp_tab: -1,
			vshow: true,

			popup_type: "", popup_modal: false, popup_title: "",

			structure_block_index: -1,
			structure_new_block: {"type": "html", "des": ""},
			component_new: {"name": "name", "des": ""},

			test_environments__: <?=json_encode($test_environments) ?>,
			ace_editors__: {'structure':[]},

			control_frame: false,

		};
	},
	mounted(){
		if( 'headtag' in this.page__ == false ){
			this.page__['headtag'] = {
				"bootstrap": true
			}
		}
		if( 'dynamicstructure' in this.page__ == false ){
			this.page__['dynamicstructure'] = {
				"version": 1,
				"vue_version": "3",
				"head_tags": {
					"title": "This is a sample page",
					"meta-names": [
						{"name": "description", "content": ""},
						{"name": "robots", "content": "noindex,nofollow"},
						{"name": "google-site-verification", "content": ""}
					],
					"meta-props": [
						{"name": "og:title", "content": ""},
						{"name": "og:type", "content": ""},
						{"name": "og:description", "content": ""},
						{"name": "og:url", "content": ""},
						{"name": "og:site_name", "content": ""},
						{"name": "og:description", "content": ""},
						{"name": "og:image", "content": ""}
					],
					"othertags": [
						{"data": `<link ref="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" >` },
						{"data": `<s`+`cript href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" ></s`+`cript>` },
					]
				},
				"blocks": [
					{
						"des": "html block", 
						"type": "html", 
						"data": `<div class="container" ><h2>Sample Vue app</h2>\n<div id="app" ></div></div>`
					}
				]
			};
		}
		var v = this.page__['dynamicstructure']['head_tags']['meta-names'];
		if( typeof(v) == "undefined" || "length" in v == false ){
			this.page__['dynamicstructure']['head_tags']['meta-names'] = [];
		}
		var v = this.page__['dynamicstructure']['head_tags']['meta-props'];
		if( typeof(v) == "undefined" || "length" in v == false ){
			this.page__['dynamicstructure']['head_tags']['meta-props'] = [];
		}
		var v = this.page__['dynamicstructure']['head_tags']['othertags'];
		if( typeof(v) == "undefined" || "length" in v == false ){
			this.page__['dynamicstructure']['head_tags']['othertags'] = [];
		}
		for(var i=0;i<this.page__['dynamicstructure']['blocks'].length;i++){
			this.ace_editors__['structure'].push(false);
		}
		setTimeout(this.initialize_editors,1000);
	},
	methods: {
		meta_name_add: function(){
			this.page__['dynamicstructure']['head_tags']['meta-names'].push({
				"name": "", "content": ""
			});
		},
		meta_name_del: function(vi){
			this.page__['dynamicstructure']['head_tags']['meta-names'].splice(vi,1);
		},
		meta_prop_add: function(){
			this.page__['dynamicstructure']['head_tags']['meta-props'].push({
				"name": "", "content": ""
			});
		},
		meta_prop_del: function(vi){
			this.page__['dynamicstructure']['head_tags']['meta-props'].splice(vi,1);
		},
		other_add: function(){
			this.page__['dynamicstructure']['head_tags']['othertags'].push({
				"data": ""
			});
		},
		other_del: function(vi){
			this.page__['dynamicstructure']['head_tags']['othertags'].splice(vi,1);
		},
		open_tab: function(tb){
			this.tab = tb+'';
			if( tb == 'control' ){
				this.control_frame = this.$refs.control_iframe__;
				console.log( this.control_frame );
				this.control_frame.src = this.path + "codeeditor/pagecontrol/" + this.page_version_id;
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
				return v['u'] + this.page__['name'];
			}else if( v['t'] == 'cloud' ){
				return v['u'] + this.page__['name'];
			}else if( v['t'] == 'cloud-alias' ){
				return v['u'] + this.page__['name'];
			}
		},
		previewit: function(){	
			this.url_modal = new bootstrap.Modal(document.getElementById('url_modal'));
			this.url_modal.show();
		},
		structure_move_up: function(vi){
			this.un_initialize_editors();
			var t = JSON.parse(JSON.stringify(this.page__['dynamicstructure']['blocks'].splice(vi,1)));
			this.page__['dynamicstructure']['blocks'].splice(vi-1,0,t[0]);
			this.initialize_editors();
		},
		structure_move_down: function(vi){
			this.un_initialize_editors();
			var t = JSON.parse(JSON.stringify(this.page__['dynamicstructure']['blocks'].splice(vi,1)));
			this.page__['dynamicstructure']['blocks'].splice(vi+1,0,t[0]);
			this.initialize_editors();
		},
		structure_add_block: function( vpos ){
			this.popup_type = 'structure_add_block';
			this.popup_title = "Page Structure Blocks";
			this.popup_modal = new bootstrap.Modal(document.getElementById('popup_modal'));
			this.popup_modal.show();
			if( vpos == 'bottom' ){
				this.structure_block_index = this.page__['dynamicstructure']['blocks'].length;
			}else{
				this.structure_block_index = vpos;
			}
		},
		structure_add_block2: function(){
			this.popup_modal.hide();
			if( this.structure_new_block['type'] == 'html' ){
				var vdata = `<div>A div Tag</div>\n`;
			}else if( this.structure_new_block['type'] == 'javascript' ){
				var vdata = `var a = 10;\n`;
			}else if( this.structure_new_block['type'] == 'style' ){
				var vdata = `.someclass{ color:red; }`;
			}
			this.un_initialize_editors();
			this.page__['dynamicstructure']['blocks'].splice( this.structure_block_index, 0, {
				"type": this.structure_new_block['type']+'',
				"des":  this.structure_new_block['des']+'',
				"data": vdata,
			});
			this.ace_editors__['structure'].splice( this.structure_block_index, 0, [false]);
			setTimeout(this.initialize_editors,1000);
		},
		structure_delete_block: function(vi){
			if( confirm("Are you sure?") ){
				this.un_initialize_editors();
				this.page__['dynamicstructure']['blocks'].splice(vi,1);
				this.ace_editors__['structure'].splice( vi,1 );
				setTimeout(this.initialize_editors,1000);
			}
		},
		setEditorHeight: function( e ){
			//console.log( e );
			//console.log( e.target.parentNode );
			var vi = e.target.parentNode.getAttribute("id");
			this.setEditorHeight2( vi );
		},
		setEditorHeight2: function( vi ){
			//console.log( vi );
			var x = vi.split(/\_/);
			//console.log( x );
			if( x[0] == 'structure' ){
				var h = ( this.ace_editors__['structure'][ Number(x[1]) ].session.getLength() + 2 )*20;
				document.getElementById(vi).style.height=h+"px";
				this.ace_editors__['structure'][ Number(x[1]) ].resize();
			}
		},
		un_initialize_editors: function(){
			console.log( this.page__['dynamicstructure'] );
			console.log( this.ace_editors__['structure'] );
			for( var i=0;i<this.page__['dynamicstructure']['blocks'].length;i++){
				document.getElementById("structure_" + i).removeEventListener('keyup', (e) => {this.setEditorHeight(e);});
				this.page__['dynamicstructure']['blocks'][i]['data'] = this.ace_editors__['structure'][ i ].getValue();
				this.ace_editors__['structure'][ i ].remove();
			}
		},
		initialize_editors: function(){
			for( var i=0;i<this.page__['dynamicstructure']['blocks'].length;i++ ){
				this.ace_editors__['structure'][ i ] = ace.edit("structure_" + i);
				document.getElementById("structure_" + i).addEventListener('keyup', (e) => {this.setEditorHeight(e);});
				this.ace_editors__['structure'][ i ].setOptions({
					enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
				});
				if( this.page__['dynamicstructure']['blocks'][i]['type'] == "html" ){
					this.ace_editors__['structure'][ i ].session.setMode("ace/mode/html");
					this.ace_editors__['structure'][ i ].setValue( html_beautify(this.page__['dynamicstructure']['blocks'][ i ]['data'] ) );

				}else if( this.page__['dynamicstructure']['blocks'][i]['type'] == "javascript" ){
					this.ace_editors__['structure'][ i ].session.setMode("ace/mode/javascript");
					this.ace_editors__['structure'][ i ].setValue( js_beautify(this.page__['dynamicstructure']['blocks'][ i ]['data'] ) );

				}else if( this.page__['dynamicstructure']['blocks'][i]['type'] == "style" ){
					this.ace_editors__['structure'][ i ].session.setMode("ace/mode/css");
					this.ace_editors__['structure'][ i ].setValue( this.page__['dynamicstructure']['blocks'][ i ]['data'] );

				}else{
					this.ace_editors__['structure'][ i ].session.setMode("ace/mode/html");
					this.ace_editors__['structure'][ i ].setValue( html_beautify(this.page__['dynamicstructure']['blocks'][ i ]['data'] ) );
				}
				setTimeout( this.setEditorHeight2( "structure_"+i ), 1000 );
			}
		},
		save_page: function(){
			for( var i=0;i<this.page__['dynamicstructure']['blocks'].length;i++){
				this.page__['dynamicstructure']['blocks'][i]['data'] = this.ace_editors__['structure'][ i ].getValue();
			}
			this.float_err__ = "";
			this.float_msg__ = "";
			var d = JSON.parse( JSON.stringify( this.page__['dynamicstructure'] ) );
			if( typeof(d) == "undefined" ){
				this.float_err__ = "Not initialized";return;
			}
			this.float_msg__  = "Saving...";
			axios.post("?", {
				"action": "save_page_dynamicstructure",
				"data": this.page__['dynamicstructure'],
			}).then( response=>{
				this.float_msg__ = "";
				if( 'status' in response.data ){
					if( response.data['status'] == "success" ){
						this.float_msg__ = "Page saved successfully";
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

<?php foreach( $components as $i=>$j ){ ?>
	app.component( "<?=$j ?>", <?=$j ?> );
<?php } ?>
var app1 =app.mount("#app");

</script>