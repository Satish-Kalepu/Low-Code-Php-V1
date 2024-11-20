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

			tab: "vue",
			comp_tab: -1,
			vshow: true,

			popup_type: "", popup_modal: false, popup_title: "",

			structure_block_index: -1,
			structure_new_block: {"type": "html", "des": ""},
			component_new: {"name": "name", "des": ""},

			test_environments__: <?=json_encode($test_environments) ?>,
			ace_editors__: {},

			control_frame: false,
			save_queue: {},
			save_cnt: 0,
			save_busy: false,

		};
	},
	mounted(){
		this.control_frame = this.$refs.control_iframe__;
		if( 'headtag' in this.page__ == false ){
			this.page__['headtag'] = {
				"bootstrap": true
			}
		}
		if( 'vuestructure' in this.page__ == false ){
			this.page__['vuestructure'] = {
				"version": 1,
				"vue_version": "3",
				"element": "#app",
				"hmr": false,
				"data": {
					'data':`{\n\treturn {\n\t\tcounter:1,\na: 1,\n\t};\n}`, 
					'ace': false
				},
				"mounted": {'data': `{\n\t/`+`/ do something\n}`},
				"methods": {'data':`methods = {\n\tdo_plus_one: function(){\n\t\tthis.counter++;\t\n},\n\tmethod_two: function(){\n\t\t/`+`/do something\t\n}\n}`},
				"template": {'data': `<div>\n\t<div>Template Content</div>\n</div>`},
				"template_use": false,
				"components": [
					{
						"name": "new","des": "New Component",
						"props": ["var1"],
						"data": {
							'data':`{\n\treturn {\n\t\ta: 1,\n\t};\n}`, 
							'ace': false
						},
						"mounted": {'data': `{\n\t/`+`/ do something\n}`},
						"methods": {'data':`methods = {\n\tmethod_one: function(){\n\t\t/`+`/do something\t\n},\n\tmethod_two: function(){\n\t\t/`+`/do something\t\n}\n}`},
						"template": {'data': `<div>\n\t<div>Template Content</div>\n</div>`},
					},
				],
				"router_enable": false,
				"router": [
					{"path": "/", "component": "", "props":[], "meta": []}
				],
				"beforeeach": {'data':`{

	console.log("Router changed: ");
	console.log( from );
	console.log( to );

	console.log("login status: " + router.app_object.islogin);
	if( router.app_object.islogin == false ){
		console.log( "stop routing");
		return true;
	}
	/`+`/ if (!isAuthenticated &&	to.path !== '/login' ) {
	/`+`/ 	return { path: '/login' }
	/`+`/ }
				}`},
				"aftereach":{'data': `{\n\n\t/`+`/ do something\n\n}\n`},
				"structure": {

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
							"data": `<div class="container" >
	<h2>Sample Vue app</h2>
	<div id="app" >
		<div>{{ counter }}</div>
		<div class="btn btn-outline-dark btn-sm" v-on:click="do_plus_one" >Click here</div>
	</div>
</div>`
						}
					]
				},
			};
		}
		var v = this.page__['vuestructure']['structure']['head_tags']['meta-names'];
		if( typeof(v) == "undefined" || "length" in v == false ){
			this.page__['vuestructure']['structure']['head_tags']['meta-names'] = [];
		}
		var v = this.page__['vuestructure']['structure']['head_tags']['meta-props'];
		if( typeof(v) == "undefined" || "length" in v == false ){
			this.page__['vuestructure']['structure']['head_tags']['meta-props'] = [];
		}
		var v = this.page__['vuestructure']['structure']['head_tags']['othertags'];
		if( typeof(v) == "undefined" || "length" in v == false ){
			this.page__['vuestructure']['structure']['head_tags']['othertags'] = [];
		}
		if( "template_use" in this.page__['vuestructure'] == false ){
			this.page__['vuestructure']['template_use'] = false;
		}
		if( "beforeeach" in this.page__['vuestructure'] == false ){
			this.page__['vuestructure']['beforeeach'] = {'data':`{\n\n\t/`+`/ do something\n\n}\n`};
		}
		if( "aftereach" in this.page__['vuestructure'] == false ){
			this.page__['vuestructure']['aftereach'] = {'data':`{\n\n\t/`+`/ do something\n\n}\n`};
		}
		if( "hmr" in this.page__['vuestructure'] == false ){
			this.page__['vuestructure']['hmr'] = false;
		}
		for( var ri=0;ri<this.page__['vuestructure']['router'].length;ri++){
			var v = this.page__['vuestructure']['router'][ri]['meta'];
			if( typeof(v) != "object" || "length" in v == false ){
				this.page__['vuestructure']['router'][ri]['meta'] = [];
			}
			var v = this.page__['vuestructure']['router'][ri]['props'];
			if( typeof(v) != "object" || "length" in v == false ){
				this.page__['vuestructure']['router'][ri]['props'] = [];
			}
		}
		this.ace_editors__ = {
			"data": false, "mounted": false, "methods": false, "template": false,
			"components": [],
			"structure": [],
		}
		for(var i=0;i<this.page__['vuestructure']['components'].length;i++){
			this.ace_editors__['components'].push({"data": false, "mounted": false, "methods": false, "template": false});
		}
		for(var i=0;i<this.page__['vuestructure']['structure']['blocks'].length;i++){
			this.ace_editors__['structure'].push(false);
		}
		if(this.page__['vuestructure']['components'].length == 1){
			this.comp_tab = 0;
		}
		setTimeout(this.initialize_editors,1000);
	},
	methods: {
		meta_name_add: function(){
			this.page__['vuestructure']['structure']['head_tags']['meta-names'].push({
				"name": "", "content": ""
			});
		},
		meta_name_del: function(vi){
			this.page__['vuestructure']['structure']['head_tags']['meta-names'].splice(vi,1);
		},
		meta_prop_add: function(){
			this.page__['vuestructure']['structure']['head_tags']['meta-props'].push({
				"name": "", "content": ""
			});
		},
		meta_prop_del: function(vi){
			this.page__['vuestructure']['structure']['head_tags']['meta-props'].splice(vi,1);
		},
		other_add: function(){
			this.page__['vuestructure']['structure']['head_tags']['othertags'].push({
				"data": ""
			});
		},
		other_del: function(vi){
			this.page__['vuestructure']['structure']['head_tags']['othertags'].splice(vi,1);
		},

		open_tab: function(tb){
			this.tab = tb+'';
			if( tb == 'control' ){
				console.log( this.control_frame );
				this.control_frame.src = this.path + "codeeditor/pagecontrol/" + this.page_version_id;
			}
		},
		open_comp: function(tb){
			if( this.comp_tab == tb ){
				this.comp_tab = -1;
			}else{
				this.comp_tab = tb;
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
		setEditorHeight: function( e ){
			var vi = e.target.parentNode.getAttribute("id");
			this.setEditorHeight2( vi );
		},
		setEditorHeight2: function( vi ){
			var x = vi.split(/\_/);
			if( x[0] == 'main' ){
				var h = ( this.ace_editors__[ x[1] ].session.getLength() +2 )*20;
				document.getElementById(vi).style.height=h+"px";
				this.ace_editors__[ x[1] ].resize();
			}else if( x[0] == 'component' ){
				var h = ( this.ace_editors__['components'][ Number(x[1]) ][ x[2] ].session.getLength() + 2 )*20;
				document.getElementById(vi).style.height=h+"px";
				this.ace_editors__['components'][ Number(x[1]) ][ x[2] ].resize();
				this.add_component_save_queue({"index": Number(x[1]), "block": x[2]});
			}else if( x[0] == 'structure' ){
				var h = ( this.ace_editors__['structure'][ Number(x[1]) ].session.getLength() + 2 )*20;
				document.getElementById(vi).style.height=h+"px";
				this.ace_editors__['structure'][ Number(x[1]) ].resize();
			}
		},
		add_component_save_queue: function(v){
			this.save_cnt++;
			this.save_queue[ v['index'] ] = v['block'];
			if( this.save_busy == false ){
				this.save_busy = true;
				setTimeout(this.save_block,1000);
			}
		},
		initialize_editors: function(){
			this.ace_editors__['data'] = ace.edit("main_data");
			this.ace_editors__['data'].session.setMode("ace/mode/javascript");
			this.ace_editors__['data'].setOptions({
				enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
			});
			document.getElementById("main_data").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
			this.ace_editors__['data'].setValue( js_beautify(this.page__['vuestructure']['data']['data'] ) );
			setTimeout(this.setEditorHeight2("main_data"),1000);

			this.ace_editors__['mounted'] = ace.edit("main_mounted");
			this.ace_editors__['mounted'].session.setMode("ace/mode/javascript");
			this.ace_editors__['mounted'].setOptions({
				enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
			});
			document.getElementById("main_mounted").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
			this.ace_editors__['mounted'].setValue( js_beautify(this.page__['vuestructure']['mounted']['data'] ) );
			setTimeout(this.setEditorHeight2("main_mounted"),1000);

			this.ace_editors__['methods'] = ace.edit("main_methods");
			this.ace_editors__['methods'].session.setMode("ace/mode/javascript");
			this.ace_editors__['methods'].setOptions({
				enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
			});
			document.getElementById("main_methods").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
			this.ace_editors__['methods'].setValue( js_beautify(this.page__['vuestructure']['methods']['data'] ) );
			setTimeout(this.setEditorHeight2("main_methods"),1000);

			this.ace_editors__['template'] = ace.edit("main_template");
			this.ace_editors__['template'].session.setMode("ace/mode/html");
			this.ace_editors__['template'].setOptions({
				enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
			});
			document.getElementById("main_template").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
			this.ace_editors__['template'].setValue( html_beautify(this.page__['vuestructure']['template']['data']) );
			setTimeout(this.setEditorHeight2("main_template"),1000);

			this.ace_editors__['beforeeach'] = ace.edit("main_beforeeach");
			this.ace_editors__['beforeeach'].session.setMode("ace/mode/javascript");
			this.ace_editors__['beforeeach'].setOptions({
				enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
			});
			document.getElementById("main_beforeeach").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
			this.ace_editors__['beforeeach'].setValue( html_beautify(this.page__['vuestructure']['beforeeach']['data']) );
			setTimeout(this.setEditorHeight2("main_beforeeach"),1000);

			this.ace_editors__['aftereach'] = ace.edit("main_aftereach");
			this.ace_editors__['aftereach'].session.setMode("ace/mode/javascript");
			this.ace_editors__['aftereach'].setOptions({
				enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
			});
			document.getElementById("main_aftereach").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
			this.ace_editors__['aftereach'].setValue( html_beautify(this.page__['vuestructure']['aftereach']['data']) );
			setTimeout(this.setEditorHeight2("main_aftereach"),1000);

			this.initialize_editors_for_components();
			this.initialize_editors_for_page_structure();
		},
		initialize_editors_for_components: function(){
			for( var comp_index=0;comp_index<this.page__['vuestructure']['components'].length;comp_index++){

				this.ace_editors__['components'][ comp_index ]['data'] = ace.edit("component_"+comp_index+"_data");
				this.ace_editors__['components'][ comp_index ]['data'].session.setMode("ace/mode/javascript");
				this.ace_editors__['components'][ comp_index ]['data'].setOptions({
					enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
				});
				document.getElementById("component_"+comp_index+"_data").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
				this.ace_editors__['components'][ comp_index ]['data'].setValue( js_beautify(this.page__['vuestructure']['components'][ comp_index ]['data']['data'] ) );
				setTimeout(this.setEditorHeight2("component_"+comp_index+"_data"),1000);

				this.ace_editors__['components'][ comp_index ]['mounted'] = ace.edit("component_"+comp_index+"_mounted");
				this.ace_editors__['components'][ comp_index ]['mounted'].session.setMode("ace/mode/javascript");
				this.ace_editors__['components'][ comp_index ]['mounted'].setOptions({
					enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
				});
				document.getElementById("component_"+comp_index+"_mounted").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
				this.ace_editors__['components'][ comp_index ]['mounted'].setValue( js_beautify(this.page__['vuestructure']['components'][ comp_index ]['mounted']['data'] ) );
				setTimeout(this.setEditorHeight2("component_"+comp_index+"_mounted"),1000);

				this.ace_editors__['components'][ comp_index ]['methods'] = ace.edit("component_"+comp_index+"_methods");
				this.ace_editors__['components'][ comp_index ]['methods'].session.setMode("ace/mode/javascript");
				this.ace_editors__['components'][ comp_index ]['methods'].setOptions({
					enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
				});
				document.getElementById("component_"+comp_index+"_methods").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
				this.ace_editors__['components'][ comp_index ]['methods'].setValue( js_beautify(this.page__['vuestructure']['components'][ comp_index ]['methods']['data'] ) );
				setTimeout(this.setEditorHeight2("component_"+comp_index+"_methods"),1000);

				this.ace_editors__['components'][ comp_index ]['template'] = ace.edit("component_"+comp_index+"_template");
				this.ace_editors__['components'][ comp_index ]['template'].session.setMode("ace/mode/html");
				this.ace_editors__['components'][ comp_index ]['template'].setOptions({
					enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
				});
				document.getElementById("component_"+comp_index+"_template").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
				this.ace_editors__['components'][ comp_index ]['template'].setValue( html_beautify(this.page__['vuestructure']['components'][ comp_index ]['template']['data']) );
				setTimeout(this.setEditorHeight2("component_"+comp_index+"_template"),1000);

			}
		},
		un_initialize_editors_for_components: function(){
			for( var i=0;i<this.page__['vuestructure']['components'].length;i++){
				document.getElementById("component_"+i+"_data").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
				document.getElementById("component_"+i+"_mounted").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
				document.getElementById("component_"+i+"_methods").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
				document.getElementById("component_"+i+"_template").addEventListener('keyup', (e) => {this.setEditorHeight(e);});
			
				this.page__['vuestructure']['components'][ i ]['data']['data'] = this.ace_editors__['components'][ i ]['data'].getValue();
				this.page__['vuestructure']['components'][ i ]['mounted']['data'] = this.ace_editors__['components'][ i ]['mounted'].getValue();
				this.page__['vuestructure']['components'][ i ]['methods']['data'] = this.ace_editors__['components'][ i ]['methods'].getValue();
				this.page__['vuestructure']['components'][ i ]['template']['data'] = this.ace_editors__['components'][ i ]['template'].getValue();

				this.ace_editors__['components'][ i ]['data'].remove();
				this.ace_editors__['components'][ i ]['mounted'].remove();
				this.ace_editors__['components'][ i ]['methods'].remove();
				this.ace_editors__['components'][ i ]['template'].remove();
			}
		},
		componet_name_fix: function(vci){
			var n = this.page__['vuestructure']['components'][ vci ]['name']+'';
			n = n.replace(/\-/g, 'HHH');n = n.replace(/\_/g, 'UUU');
			n = n.replace(/\W/g, '');
			n = n.replace(/HHH/g, '-');n = n.replace(/UUU/g, '_');
			this.page__['vuestructure']['components'][ vci ]['name'] = n;
		},
		component_delete: function( vpos ){
			if( confirm("Are you sure?") ){
				this.un_initialize_editors_for_components();
				this.page__['vuestructure']['components'].splice(vpos,1);
				setTimeout(this.initialize_editors_for_components,5000);
			}
		},
		component_add: function( vpos ){
			this.popup_type = 'component_add';
			this.popup_title = "Add new Component";
			this.popup_modal = new bootstrap.Modal(document.getElementById('popup_modal'));
			this.popup_modal.show();
		},
		component_add2: function( vpos ){
			for( var i =0;i<this.page__['vuestructure']['components'].length;i++){
				if( this.page__['vuestructure']['components'][i]['name'] == this.component_new['name'] ){
					alert("Component with same name already exists");return ;
				}
			}

			this.popup_modal.hide();
			this.un_initialize_editors_for_components();
			this.page__['vuestructure']['components'].push({
				"name": this.component_new['name']+'',
				"des": this.component_new['des']+'',
				"props": ["var1"],
				"data": {'data':`{\n\treturn {\n\t\ta: 1,\n\t};\n}`},
				"mounted": {'data': `{\n\t/`+`/ do something\n}`},
				"methods": {'data':`methods = {\n\tmethod_one: function(){\n\t\t/`+`/do something\t\n},\n\tmethod_two: function(){\n\t\t/`+`/do something\t\n}\n}`},
				"template": {'data': `<div>\n\t<div>Template Content</div>\n</div>`},
			});
			this.ace_editors__['components'].push({"data":false,"mounted":false,"methods":false,"template":false});
			setTimeout(this.initialize_editors_for_components,1000);
			this.comp_tab = -1;
			this.component_new = {"name": "name", "des": ""};
		},
		router_add_path: function(){
			this.page__['vuestructure']['router'].push({
				"path": "/path",
				"component": "none",
				"query":{},
				"props":{},
				"meta":{},
			});
		},
		router_remove_path: function( ri ){
			if( this.page__['vuestructure']['router'].length == 1 ){
				alert("At least one route is required for app");return;
			}
			if( confirm("Are you sure?") ){
				this.page__['vuestructure']['router'].splice( ri, 1 );
			}
		},
		router_move_up: function( ri ){
			if( ri <= 0 ){return ;}
			var t = JSON.parse( JSON.stringify( this.page__['vuestructure']['router'].splice( ri, 1) ) );
			this.page__['vuestructure']['router'].splice( ri-1, 0, t[0]);
		},
		router_move_down: function( ri ){
			if( ri > this.page__['vuestructure']['router'].length-1 ){return ;}
			var t = JSON.parse( JSON.stringify( this.page__['vuestructure']['router'].splice( ri, 1) ) );
			this.page__['vuestructure']['router'].splice( ri+1, 0, t[0]);
		},
		structure_add_block: function( vpos ){
			this.popup_type = 'structure_add_block';
			this.popup_title = "Page Structure Blocks";
			this.popup_modal = new bootstrap.Modal(document.getElementById('popup_modal'));
			this.popup_modal.show();
			if( vpos == 'bottom' ){
				this.structure_block_index = this.page__['vuestructure']['structure']['blocks'].length;
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
			}else{
				vdata = '';
			}
			this.un_initialize_editors_for_page_structure();
			this.page__['vuestructure']['structure']['blocks'].splice( this.structure_block_index, 0, {
				"type": this.structure_new_block['type']+'',
				"des":  this.structure_new_block['des']+'',
				"data": vdata,
			});
			this.ace_editors__['structure'].splice( this.structure_block_index, 0, [false]);
			setTimeout(this.initialize_editors_for_page_structure,1000);
		},
		structure_delete_block: function(vi){
			if( confirm("Are you sure?") ){
				this.un_initialize_editors_for_page_structure();
				this.page__['vuestructure']['structure']['blocks'].splice(vi,1);
				this.ace_editors__['structure'].splice( vi,1 );
				setTimeout(this.initialize_editors_for_page_structure,1000);
			}
		},
		un_initialize_editors_for_page_structure: function(){
			console.log( this.page__['vuestructure']['structure'] );
			console.log( this.ace_editors__['structure'] );
			for( var i=0;i<this.page__['vuestructure']['structure']['blocks'].length;i++){
				document.getElementById( "structure_" + i ).addEventListener('keyup', (e) => {this.setEditorHeight(e);});
				this.page__['vuestructure']['structure']['blocks'][i]['data'] = this.ace_editors__['structure'][ i ].getValue();
				this.ace_editors__['structure'][ i ].remove();
			}
		},
		initialize_editors_for_page_structure: function(){
			for( var i=0;i<this.page__['vuestructure']['structure']['blocks'].length;i++){
				this.ace_editors__['structure'][ i ] = ace.edit("structure_" + i);
				this.ace_editors__['structure'][ i ].setOptions({
					enableAutoIndent: true, behavioursEnabled: true, showPrintMargin: false, printMargin: false, showFoldWidgets: false, 
				});
				document.getElementById( "structure_" + i ).addEventListener('keyup', (e) => {this.setEditorHeight(e);});
				if( this.page__['vuestructure']['structure']['blocks'][i]['type'] == "html" ){
					this.ace_editors__['structure'][ i ].session.setMode("ace/mode/html");
					this.ace_editors__['structure'][ i ].setValue( html_beautify(this.page__['vuestructure']['structure']['blocks'][ i ]['data'] ) );
				}else if( this.page__['vuestructure']['structure']['blocks'][i]['type'] == "javascript" ){
					this.ace_editors__['structure'][ i ].session.setMode("ace/mode/javascript");
					this.ace_editors__['structure'][ i ].setValue( js_beautify(this.page__['vuestructure']['structure']['blocks'][ i ]['data'] ) );
				}else if( this.page__['vuestructure']['structure']['blocks'][i]['type'] == "style" ){
					this.ace_editors__['structure'][ i ].session.setMode("ace/mode/css");
					this.ace_editors__['structure'][ i ].setValue( this.page__['vuestructure']['structure']['blocks'][ i ]['data'] );
				}else{
					this.ace_editors__['structure'][ i ].session.setMode("ace/mode/html");
					this.ace_editors__['structure'][ i ].setValue( html_beautify(this.page__['vuestructure']['structure']['blocks'][ i ]['data'] ) );
				}
				setTimeout( this.setEditorHeight2( "structure_"+i ), 1000 );
			}
		},
		meta_add: function(ri){
			this.page__['vuestructure']['router'][ri]['meta'].push({
				"key": "", "value": ""
			});
		},
		meta_delete: function(ri,vi){
			this.page__['vuestructure']['router'][ri]['meta'].splice(vi,1);
		},
		props_add: function(ri){
			this.page__['vuestructure']['router'][ri]['props'].push({
				"key": "", "value": ""
			});
		},
		props_delete: function(ri,vi){
			this.page__['vuestructure']['router'][ri]['props'].splice(vi,1);
		},
		save_page: function(){
			this.page__['vuestructure']['data']['data'] = this.ace_editors__['data'].getValue();
			this.page__['vuestructure']['mounted']['data'] = this.ace_editors__['mounted'].getValue();
			this.page__['vuestructure']['methods']['data'] = this.ace_editors__['methods'].getValue();
			this.page__['vuestructure']['template']['data'] = this.ace_editors__['template'].getValue();
			this.page__['vuestructure']['beforeeach']['data'] = this.ace_editors__['beforeeach'].getValue();
			this.page__['vuestructure']['aftereach']['data'] = this.ace_editors__['aftereach'].getValue();
			for( var i=0;i<this.page__['vuestructure']['components'].length;i++ ){
				this.page__['vuestructure']['components'][i]['data']['data'] = this.ace_editors__['components'][ i ]['data'].getValue();
				this.page__['vuestructure']['components'][i]['mounted']['data'] = this.ace_editors__['components'][ i ]['mounted'].getValue();
				this.page__['vuestructure']['components'][i]['methods']['data'] = this.ace_editors__['components'][ i ]['methods'].getValue();
				this.page__['vuestructure']['components'][i]['template']['data'] = this.ace_editors__['components'][ i ]['template'].getValue();
			}
			for( var i=0;i<this.page__['vuestructure']['structure']['blocks'].length;i++){
				this.page__['vuestructure']['structure']['blocks'][i]['data'] = this.ace_editors__['structure'][ i ].getValue();
			}
			this.float_err__ = "";
			this.float_msg__ = "";
			var d = JSON.parse( JSON.stringify( this.page__['vuestructure'] ) );
			if( typeof(d) == "undefined" ){
				this.float_err__ = "Not initialized";return;
			}
			this.float_msg__  = "Saving...";
			axios.post("?", {
				"action": "save_page_vuestructure",
				"data": this.page__['vuestructure'],
			}).then( response=>{
				this.float_msg__ = "";
				if( 'status' in response.data ){
					if( response.data['status'] == "success" ){
						this.float_msg__ = "Page saved successfully";
						setTimeout( function(v){ v.float_msg__ = ""; }, 3000, this);
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
app.mount("#app");

</script>