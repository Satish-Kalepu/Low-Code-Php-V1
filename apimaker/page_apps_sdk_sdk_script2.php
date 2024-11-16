<script src="<?=$config_global_apimaker_path ?>ace/src-noconflict/ace.js" ></script>
<script src="<?=$config_global_apimaker_path ?>js/beautify-html.js" ></script>
<script src="<?=$config_global_apimaker_path ?>js/beautify-css.js" ></script>
<script src="<?=$config_global_apimaker_path ?>js/beautify.js" ></script>
<script src="<?=$config_global_apimaker_path ?>js/htmlclean.js" ></script>
<script>

//import beautify from "./ace/ext/beautify";

<?php

	$components = [
		"input_object", "input_values", 
		"inputtextbox", "inputtextbox2", 
		"varselect", "varselect2", "pluginselect",
		"vobject", "vobject2", "vobject_payload", "vlist", 
		"vfield", "vfield_payload", 
		"plugin_database",
		"vdt", "vdtm", "vts",
		"thing", 
	];

	foreach( $components as $i=>$j ){
		require($apps_folder."/" . $j . ".js");
	}

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

			"data_types__"		: {
				"V": "Variable",
				"T": "Text",
				"TT": "MultiLineText",
				"HT": "HTMLText",
				"N": "Number",
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
				"TI": "Thing Item",
				"TH": "Thing", // not visible for general use.
				"THL": "Thing List",
				"L": "List",
				"O": "Assoc List",
				"B": "Boolean",
				"NL": "Null", 
				"BIN": "Binary",
				"B64": "Base64",
				"MongoQ": "MongoDB Query",
				"MysqlQ": "Mysql Query",				
			},
			"data_types1__"		: {
				"V": "Variable",
				"T": "Text",
				"N": "Number",
				"B": "Boolean",
				"NL": "Null", 
				"L": "List",
				"O": "Assoc List",
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
			},
			"data_types2__"		: {
				"TT": "MultiLine Text",
				"HT": "HTML Text",
				"BIN": "Binary",
				"B64": "Base64",
				"TI": "Thing Item",
				"TH": "Thing",
				"THL": "Thing List",
				"MongoQ": "MongoDB Query",
				"MysqlQ": "Mysql Query",
			},
			"input_types__"		: {
				"T": "Text",
				"TT": "MultiLineText",
				"N": "Number",
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
				"L": "List",
				"O": "Assoc List",
				"B": "Boolean",
				"NL": "Null", 
				"B64": "Base64",
			},
			"input_types2__"		:{
				"T": "Text",
				"N": "Number",
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
				"B": "Boolean",
				"B64": "Base64",
			},

			context_menu__: false,
			context_for__: 'stages',
			context_var_for__: '',
			context_dependency__: "",
			context_callback__: "",
			context_el__: false,
			context_style__: "display:none;",
			context_stage_id__: -1,
			context_list__: [],
			context_list_filter__: [],
			context_type__: "",
			context_value__: "",
			context_datavar__: "",
			context_datavar_parent__: "",
			context_menu_current_item__: "",
			context_menu_key__: "",
			context_expand_key__: "",
			context_thing__: "",
			context_thing_list__: {},
			context_thing_loaded__: false,
			context_thing_msg__: "",
			context_thing_err__: "",

			popup_stage_id__: -1,
			popup_data__: {},
			popup_for__: "",
			popup_datavar__: "",
			popup_type__: "json",
			popup_title__: "Popup Title",
			popup_suggest_list__: [],
			popup_ref__: "",
			popup_modal__: false,
			popup_modal_displayed__: false,
			popup_html_modal__: false,
			popup_import__: false,
			popup_import_str__: `{}`,

			simple_popup_stage_id__: -1,
			simple_popup_data__: {},
			simple_popup_for__: "",
			simple_popup_datavar__: "",
			simple_popup_type__: "json",
			simple_popup_title__: "Popup Title",
			simple_popup_modal__: false,
			simple_popup_import__: false,
			simple_popup_import_str__: `{}`,
			simple_popup_el__: false,
			simple_popup_style__:  "top:50px;left:50px;",

			thing_options__: [],
			thing_options_msg__: "",
			thing_options_err__: "",
			things_used__: {},

			document.addEventListener("keyup", this.event_keyup__ );
			document.addEventListener("keydown", this.event_keydown__);
			document.addEventListener("click", this.event_click__, true);
			document.addEventListener("scroll", this.event_scroll__, true);
			document.addEventListener("blur", this.event_blur__, true);
			window.addEventListener("paste", this.event_paste__, true);

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

			new_method_template: {
				"name": "methodOne", 
				"des": "Method one does one thing perfectly",
				"inputs": [
					{
						"name": "var1", "type": "string", "default": "", "mandatory": false,
					}
				],
				"outputs": [
					{
						"name": "status", "type": "string", "value": "success", "fixed_key": true, "fixed_values":["success", "fail"],
					},
					{
						"name": "error", "type": "string", "value": "", "fixed_key": true,
					}
				],
				"code": `$a = 10;\nreturn ['status'=>'success', 'data'=>'OK'];`,
				"tests": [
					{
						"name": "Test 1", 
						"input": [
							{
								"name": "var1", "type": "string", "default": "", "mandatory": false,
							}
						],
						"output": [
							{
								"name": "status", "type": "string", "value": "success", "fixed_key": true, "fixed_values":["success", "fail"],
							},
							{
								"name": "error", "type": "string", "value": "", "fixed_key": true,
							}
						],
					}
				]
			},

		};
	},
	mounted(){
		if( 'structure' in this.sdk__ == false ){
			this.sdk__['structure'] = {
				"version": 1,
				"vars": [
					{
						"name": "var1", "label": "Variable One", "type": "string", "value": "",
					}
				],
				"constructor": {
					"inputs": [
						{
							"name": "var1", "label": "Variable One", "type": "string", "default": "",
						}
					],
				},
				"methods": [
					this.new_method_template,
				]
			};
		}
		for(var i=0;i<this.sdk__['structure']['methods'].length;i++){
			this.ace_editors__['methods'].push(false);
		}
		console.log( JSON.stringify(this.sdk__,null,4) );
		setTimeout(this.initialize_editors,1000);
	},
	methods: {
		event_scroll__: function(e){
			if( this.context_menu__ ){
				this.set_context_menu_style__();
			}else if( this.simple_popup_modal__ ){
				this.set_simple_popup_style__();
			}
			// if( e.target.className == "codeeditor_block_a" ){
			// }else if( e.target.className == "codeeditor_block_a" ){
			// }
		},
		event_keyup__: function(e){
			if( e.target.hasAttribute("data-type") ){
				console.log("event_keyup__: "+e.target.getAttribute("data-type"));
				if( e.target.getAttribute("data-type") == "editable" ){
					setTimeout(this.editable_check__, 100, e.target);
				}else if( e.target.getAttribute("data-type") == "popupeditable" ){
					setTimeout(this.editable_check__, 100, e.target);
				}else{
					console.log("Error: unknown data-type: " + e.target.getAttribute("data-type") );
				}
			}else{
				console.log("event_keyup__: data-type not found");
			}
		},
		show_toast__: function( v ){
			this.toasts__.push( v );
			if( this.toasts__.length == 1 ){
				setTimeout(this.toast_close__, 1000);
			}
		},
		toast_close__: function(){
			this.toasts__.splice(0,1);
			if( this.toasts__.length > 0 ){
				setTimeout(this.toast_close__, 1000);
			}
		},
		event_paste__: function( e ){
			e.preventDefault();e.stopPropagation();
			clipboardData = e.clipboardData || window.clipboardData;
			var paste_data_ = clipboardData.getData('Text');
			document.execCommand('inserttext', false, paste_data_);
			// console.log( paste_data_ );
			// var r = document.getSelection().getRangeAt(0);
			// console.log( r );
			//setTimeout(this.after_paste__,100,e.target);
		},
		after_paste__: function(el){
			if( el.innerText != el.innerHTML ){
				el.innerText = el.innerText+'';
			}
		},
		event_blur__: function( e ){
			if( e.target.hasAttribute("data-type") ){
				if( e.target.getAttribute("data-type") == "editable" ){
					e.stopPropagation();
					e.preventDefault();
					var s = this.find_parents__(e.target);
					if( !s ){ return false; }
					var v = e.target.innerText;
					v = v.replace(/[\u{0080}-\u{FFFF}]/gu, "");
					v = v.trim();
					// v = v.replace(/\&nbsp\;/g, " ");
					// v = v.replace(/\&gt\;/g, ">");
					// v = v.replace(/\&lt\;/g, "<");
					var vv = this.v_filter__( v, e.target );
					// console.log( "==" + v + "== : ==" + vv + "==" );
					if( v == vv ){
						this.update_editable_value__( s, v );
						setTimeout(this.editable_check__, 200, e.target );
						setTimeout(this.updated_option__, 200);
						if( e.target.hasAttribute("validation_error") ){
							e.target.removeAttribute("validation_error");
						}
					}else{ this.show_toast__("Incorrect value entered!"); e.target.setAttribute("validation_error", "sss"); }
				}
				if( e.target.getAttribute("data-type") == "popupeditable" ){
					e.stopPropagation();
					e.preventDefault();
					var s = this.find_parents__(e.target);
					if( !s ){ return false; }
					var v = e.target.innerText;
					v = this.v_filter__( v, e.target );
					if( v ){
						this.update_editable_value__( s, v );
						setTimeout(this.editable_check__, 200, e.target );
						setTimeout(this.updated_option__, 200);
					}else{console.log("incorrect value formed!");}
				}
			}
		},
		editable_check__: function(el){
			var data_var = el.getAttribute("data-var");
			var s = this.find_parents__(el);
			if( !s ){ return false; }
			var v = this.get_editable_value__(s);
			if( v === false ){console.log("editable_check: value false");return false;}
			if( v != el.innerText ){
				if( el.nextSibling ){
				}else{
					el.insertAdjacentHTML("afterend", `<div class="inlinebtn" data-type="editablebtn" ><i class="fa-solid fa-square-check" ></i></div>` );
				}
			}else{
				if( el.nextSibling ){
					el.nextSibling.outerHTML = '';
				}
			}
		},
		event_keydown__: function(e){
			if( e.ctrlKey && e.keyCode == 86 ){
			//	e.preventDefault();e.stopPropagation();
			}
			if( e.keyCode == 27 ){
				if( this.context_menu__ ){
					this.hide_context_menu__();
				}
				if( this.simple_popup_modal__ ){
					this.simple_popup_modal__ = false;
				}
			}
			if( e.target.hasAttribute("data-type") ){
				if( e.target.getAttribute("data-type") =="editable" ){
					if( e.target.className == "editabletextarea" ){

					}else if( e.keyCode == 13 || e.keyCode == 10 ){
						e.preventDefault();
						e.stopPropagation();
						var v = e.target.innerText.trim();
						v = this.v_filter__( v, e.target );
						if( v ){
							if( e.target.nextSibling ){
								e.target.nextSibling.outerHTML = "";
							}
							s = this.find_parents__(e.target);
							if( !s ){ return false; }
							this.update_editable_value__( s, v );
							//setTimeout(this.editable_check__, 100, e.target);
							setTimeout(this.updated_option__, 200);
						}else{console.log("incorrect value formed!");}
					}
				}
			}
		},
		event_click__: function(e){
			var el = e.target;
			var f = false;
			var el_context = false;
			var el_data_type = false;
			var stage_id = -1;
			var data_var = "";
			var data_for = "";
			var data_var_parent = "";
			var data_var_l = [];
			var zindex=0;
			var ktype = '';
			var plugin = '';
			for(var c=0;c<50;c++){
				try{
					if( el.nodeName != "#text" ){
						//console.log( "zindex: " + el.style.zIndex + ": " + el.style.--bs-modal-zindex );
						if( el.nodeName == "BODY" || el.nodeName == "HTML" || el.className == "stageroot" ){
							break;
						}
						if( el.hasAttribute("data-context") && el_context == false ){
							el_context = el;
						}
						if( el.hasAttribute("data-type") && el_data_type == false ){
							el_data_type = el;
						}
						if( el.hasAttribute("data-for") && data_for == '' ){
							data_for = el.getAttribute("data-for");
						}
						if( el.hasAttribute("data-plg") && plugin == '' ){
							plugin = el.getAttribute("data-plg");
						}
						if( el.hasAttribute("data-k-type") && ktype == '' ){
							ktype = el.getAttribute("data-k-type");
						}
						if( el.hasAttribute("data-var") && data_var == false ){
							data_var = el.getAttribute("data-var");
						}
						if( el.hasAttribute("data-var-parent") && data_var_parent == "" ){
							data_var_parent = el.getAttribute("data-var-parent");
						}
						if( el.hasAttribute("data-stagei") ){
							stage_id = Number(el.getAttribute("data-stagei"));
						}
						if( el.className == "help-div" ){
							doc = el.getAttribute("doc");
							this.show_doc_popup__(doc);
							return 0;
						}
						if( el.className == "help-div2" ){
							doc = el.getAttribute("data-help");
							this.simple_popup_el__ = el;
							this.simple_popup_stage_id__ = -1;
							this.simple_popup_datavar__ = "d";
							this.simple_popup_for__ = "stages";
							this.simple_popup_data__ = doc;
							this.simple_popup_type__ = "hh";
							this.simple_popup_modal__ = true;
							//this.show_and_focus_context_menu__();
							this.set_simple_popup_style__();

							return 0;
						}
					}
					el = el.parentNode;
				}catch(e){
					console.log( "event click Error: " + e );
					break;
				}
			}
			//console.log();
			this.echo__({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
			if( el_data_type ){
				var t = el_data_type.getAttribute("data-type");
				if( t == "type_pop" ){

				}else if( t == "objecteditable" ){
					this.popup_stage_id__ = stage_id;
					this.popup_datavar__ = data_var;
					this.popup_for__ = data_for;
					var v = this.get_editable_value__({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
					if( v === false ){console.log("event_click: value false");return false;}
					this.popup_data__ = v;
					this.popup_type__ = el_data_type.getAttribute("editable-type");
					this.popup_title__ = "Data Editor";
					this.popup_ref__ = "";
					if( el_data_type.hasAttribute("data-ref") ){
						this.popup_ref__ = el_data_type.getAttribute("data-ref");
					}
					if( el_data_type.hasAttribute("editable-title") ){
						this.popup_title__ = el_data_type.getAttribute("editable-title");
					}else if( this.popup_type__ == "O" ){
						this.popup_title__ = "Object/Associative Array Structure";
					}else if( this.popup_type__ == "TT" ){
						this.popup_title__ = "Multiline Text";
					}else if( this.popup_type__ == "HT" ){
						this.popup_title__ = "HTML Editor";
					}
					if( this.popup_type__ == "HT" ){
						if( this.popup_html_modal__ == false ){
							this.popup_html_modal__ = new bootstrap.Modal( document.getElementById('popup_html_modal__') );
						}
						this.popup_html_modal__.show();

						this.ace_editor2 = ace.edit("popup_html_editor");
						this.ace_editor2.session.setMode("ace/mode/html");
						this.ace_editor2.setOptions({
							enableAutoIndent: true, behavioursEnabled: true,
							showPrintMargin: false, printMargin: false, 
							showFoldWidgets: false, 
						});
						this.ace_editor2.setValue( html_beautify(this.popup_data__) );

					}else{
						this.popup_modal_open__();
					}

				}else if( t == "popupeditable" ){
					this.simple_popup_el__ = el_data_type;
					this.simple_popup_stage_id__ = stage_id;
					this.simple_popup_datavar__ = data_var;
					this.simple_popup_for__ = data_for;
					var v = this.get_editable_value__({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
					if( v === false ){console.log("event_click: value false");return false;}

					this.simple_popup_data__ = v;
					this.simple_popup_type__ = el_data_type.getAttribute("editable-type");
					this.simple_popup_modal__ = true;
					//this.show_and_focus_context_menu__();
					this.set_simple_popup_style__();

				}else if( t == "payloadeditable" ){
					this.popup_stage_id__ = stage_id;
					this.popup_datavar__ = data_var;
					this.popup_for__ = data_for;
					var v = this.get_editable_value__({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
					if( v === false ){console.log("event_click: value false");return false;}
					this.popup_data__ = v;
					this.popup_type__ = 'PayLoad';
					this.popup_title__ = "Request Payload Editor";
					this.popup_modal_open__();

				}else if( t == "dropdown" || t == "dropdown2" || t == "dropdown3" || t == "dropdown4" ){
					this.context_el__ = el_data_type;
					this.context_value__ = el_data_type.innerHTML;
					this.context_menu_key__ = "";
					this.context_for__ = data_for;
					this.context_datavar__ = data_var;
					var v = this.get_editable_value__({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
					if( v === false ){console.log("event_click: value false");return false;}
					//console.log("dropdown click: " + data_for + ": " + data_var );
					this.context_stage_id__ = stage_id;
					this.context_type__ = el_data_type.getAttribute("data-list");
					if( this.context_type__ == "varsub" || this.context_type__ == "plgsub" ){
						this.context_var_for__ = el_data_type.getAttribute("var-for");
					}else{
						this.context_var_for__ = "";
					}
					if( el_data_type.hasAttribute("data-context-dependency") ){
						this.context_dependency__ = el_data_type.getAttribute("data-context-dependency");
					}else{
						this.context_dependency__ = "";
					}
					if( el_data_type.hasAttribute("data-context-callback") ){
						this.context_callback__ = el_data_type.getAttribute("data-context-callback");
					}else{
						this.context_callback__ = "";
					}
					if( el_data_type.hasAttribute("data-list-filter") ){
						var tl = el_data_type.getAttribute("data-list-filter").split(/\,/g);
						this.context_list_filter__ = tl;
					}else{
						this.context_list_filter__ = [];
					}
					if( this.context_type__ == "thing" ){
						if( el_data_type.hasAttribute("data-thing") ){
							this.context_thing__ = el_data_type.getAttribute("data-thing");
							setTimeout(this.context_thing_list_load_check__,300);
						}else{
							this.context_thing__ = "UnKnown";
						}
					}
					this.context_datavar_parent__ = data_var_parent;
					if( this.context_type__ == "list" ){
						var ld = el_data_type.getAttribute("data-list-values");
						if( ld == 'input-method' ){
							this.context_list__ = ["GET", "POST"];
						}else if( ld == 'post-input-type' ){
							this.context_list__ = ["application/x-www-form-urlencoded", "application/json", "application/xml"];
						}else if( ld == 'get-input-type' ){
							this.context_list__ = ["query_string"];
						}else if( ld == 'auth-type' ){
							this.context_list__ = ["None", "Access-Key", "Credentials", "Bearer"];
						}else if( ld == 'output-type' ){
							if( this.api__['input-method'] == "GET" ){
								this.context_list__ = ["application/json", "application/xml", "text/html", "text/plain"];
							}else{
								this.context_list__ = ["application/json", "application/xml"];
							}
						}else{
							this.context_list__ = ld.split(",");
						}
					}
					this.show_and_focus_context_menu__();
					this.set_context_menu_style__();

				}else if( t == "editablebtn" ){
					setTimeout( this.editablebtn_click__, 100, el_data_type, data_var, data_for, stage_id, e );
				}else{
					console.log("event_click__Unknown");
				}
			}else if( el_context ){
				console.log("Element Data-Context");
			}else{
				if( this.context_menu__ ){
					this.hide_context_menu__();
				}
				if( this.simple_popup_modal__ ){
					this.simple_popup_modal__ = false;
				}
			}
		},
		popup_modal_open__: function(){
			if( this.popup_modal__ == false ){
				this.popup_modal__ = new bootstrap.Modal(document.getElementById('popup_modal__'));
					document.getElementById('popup_modal__').addEventListener('hide.bs.modal', event => {
					console.log("Popup closed");
					this.popup_modal_displayed__ = false;
				});
			}
			this.popup_modal__.show();
			this.popup_modal_displayed__ = true;
		},
		context_thing_list_load_check__: function(){
			if( this.context_thing__ in this.context_thing_list__ == false ){
				this.context_thing_list__[ this.context_thing__ ] = [];
			}
			//if( this.context_thing_list__[ this.context_thing__ ].length == 0 )
			{
				this.context_thing_msg__ = "Loading...";
				this.context_thing_err__ = "";
				this.context_thing_list__[ this.context_thing__ ] = [];
				axios.post("<?=$config_global_apimaker_path ?>things", {
					"action": "context_load_things",
					"app_id": "<?=$config_param1 ?>",
					"thing": this.context_thing__,
					"depend": this.context_dependency__,
				}).then(response=>{
					this.context_thing_msg__ = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									if( response.data['things'] == null ){
										alert("Error context list");
									}else if( typeof(response.data['things']) == "object" ){
										this.context_thing_list__[ this.context_thing__ ] = response.data['things'];
									}
								}else{
									this.context_thing_err__ = "Token Error: " + response.data['data'];
								}
							}else{
								this.context_thing_err__ = "Incorrect response";
							}
						}else{
							this.context_thing_err__ = "Incorrect response Type";
						}
					}else{
						this.context_thing_err__ = "Response Error: " + response.status;
					}
				}).catch(error=>{
					this.context_thing_err__ = "Error Loading";
				});
			}
		},
		editablebtn_click__: function( el_data_type, data_var, data_for, stage_id, e ){
			var v = el_data_type.previousSibling.innerText;
			v = v.replace(/[\u{0080}-\u{FFFF}]/gu, "");
			// v = v.replace( /\&nbsp\;/g, " " );
			// v = v.replace( /\&gt\;/g,  ">" );
			// v = v.replace( /\&lt\;/g,  "<" );
			vv = this.v_filter__(v, el_data_type.previousSibling );
			if( vv == v ){
				this.update_editable_value__({'data_var':data_var,'data_for':data_for,'stage_id':stage_id}, v);
				setTimeout( this.editable_check__, 100, e.target );
				setTimeout( this.updated_option__, 200 );
				if( e.target.hasAttribute("validation_error") ){
					e.target.removeAttribute("validation_error");
				}
			}else{ this.show_toast__("Incorrect value entered!"); e.target.setAttribute("validation_error", "sss"); }
		},
		v_filter__: function(v,el){
			if( el.hasAttribute("data-allow") ){
				if( el.getAttribute("data-allow") == "variable_name" ){
					v = v.replace(/[^A-Za-z0-9\.\-\_]/g, '');
				}else if( el.getAttribute("data-allow") == "expression" ){
					v = v.replace(/[^A-Za-z0-9\.\*\[\]\(\)\+\/\%\-\_\ ]/g, '');
				}else if( el.getAttribute("data-allow") == "number" || el.getAttribute("data-allow") == "N" ){
					v = v.replace(/[^0-9\.\-]/g, '');
				}
			}
			return v;
		},
		update_editable_value__: function(s, v){
			if( s['data_for'] == 'stages' ){
				var ov = this.get_sub_var__(this.engine__['stages'][ s['stage_id'] ], s['data_var'], v);
				if( ov != v ){
					this.set_sub_var__(this.engine__['stages'][ s['stage_id'] ], s['data_var'], v);
					this.check_sub_key__(this.engine__['stages'][ s['stage_id'] ], s['data_var'], v);
					if( this.engine__['stages'][ s['stage_id'] ]['k']['v'] == "Let" && s['data_var'] == "d:lhs" ){
						this.update_variable_change_in_sub_stages__( s['stage_id'], ov+'', v+'' );
					}
				}
			}else if( s['data_for'] == 'api' ){
				var ov = this.get_sub_var__(this.api__, s['data_var'], v);
				if( ov != v ){
					this.set_sub_var__(this.api__, s['data_var'], v);
					this.check_sub_key__(this.api__, s['data_var'], v);
				}
			}else if( s['data_for'] == 'engine' ){
				var ov = this.get_sub_var__(this.engine__, s['data_var'], v);
				if( ov != v ){
					this.set_sub_var__(this.engine__, s['data_var'], v);
					this.check_sub_key__(this.engine__, s['data_var'], v);
				}
			}else if( s['data_for'] == 'test__' ){
				var ov = this.get_sub_var__(this.test__, s['data_var'], v);
				if( ov != v ){
					this.set_sub_var__(this.test__, s['data_var'], v);
					this.check_sub_key__(this.test__, s['data_var'], v);
				}
			}else{
				console.error("update_editable_value__: data_for unknown: " + s['data_for'] + ": " + s['data_var'] );
				return false;
			}
		},
		get_editable_value__: function(s){
			if( s['data_for'] == 'stages' ){
				return this.get_sub_var__(this.engine__['stages'][ s['stage_id'] ], s['data_var']);
			}else if( s['data_for'] == 'api' ){
				return this.get_sub_var__(this.api__, s['data_var']);
			}else if( s['data_for'] == 'engine' ){
				return this.get_sub_var__(this.engine__, s['data_var']);
			}else if( s['data_for'] == 'test__' ){
				return this.get_sub_var__(this.test__, s['data_var']);
			}else{
				console.error("get_editbale_value: data_for unknown: " + s['data_for'] + ": " + s['data_var'] );
				return false;
			}
		},
		check_sub_key__: function(vv, data_var, v){
			x = data_var.split(/\:/g);
			var vkey = x.pop();
			if( vkey == 'k' ){
				var data_var = x.join(":");
				var mdata = this.get_sub_var__( vv, data_var );
				if( 'k' in mdata && 'v' in mdata && 't' in mdata ){
					var vkey = x.pop();
					if( vkey != v ){
						var data_var = x.join(":");
						var mdata2 = this.get_sub_var__( vv, data_var );
						mdata2[ v+'' ] = this.json__(mdata);
						delete mdata2[ vkey ];
					}
				}else{
					this.echo__("Not key object");
				}
			}else{this.echo__("k not found");}
		},
		respondvars_push__: function(v){
			v.push({"t":"V", "v":{"v":"","t":"","vs":false} });
		},
		respondvars_del__: function(v,ri){
			v.splice(ri,1);
		},
		popup_data_save__: function(){
			this.popup_modal__.hide();
		},
		find_parents__: function(el){
			var v = {
				'stage_id':-1,
				'data_var': '',
				'data_type': '',
				'data_for': '',
				'plugin': '',
			};
			var f = false;
			for(var c=0;c<20;c++){
				try{
					if( el.nodeName != "#text" ){
					if( el.nodeName == "BODY" || el.nodeName == "HTML" || el.className == "stageroot" ){
						f = true;
						break;
					}
					if( el.hasAttribute("data-var") && v['data_var'] == '' ){
						v['data_var'] = el.getAttribute("data-var");
					}
					if( el.hasAttribute("data-for") && v['data_for'] == '' ){
						v['data_for'] = el.getAttribute("data-for");
					}
					if( el.hasAttribute("data-stagei") ){
						v['stage_id'] = Number(el.getAttribute("data-stagei"));
					}
					if( el.hasAttribute("data-plg") && v['plugin'] == '' ){
						v['plugin'] = el.getAttribute("data-plg");
					}
					}
					el = el.parentNode;
				}catch(e){
					console.log( "find parents Error: " + e );
					return false;
					break;
				}
			}
			return v;
		},
		hide_context_menu__: function(){
			this.context_menu__ = false;
			this.context_style__ = "display:none;";
			if( document.getElementById("context_menu__").parentNode.nodeName != "BODY" ){
				console.log("moving context menu back to body ");
				document.body.appendChild( document.getElementById("context_menu__") );
			}
		},
		show_and_focus_context_menu__: function(){
			setTimeout(function(){try{document.getElementById("contextmenu_key1").focus();}catch(e){}},300);
			this.context_menu__ = true;
			if( this.popup_modal_displayed__ ){
				document.getElementById("popup_modal_body__").appendChild( document.getElementById("context_menu__") );
				//this.set_context_menu_style__();
			}
			this.context_expand_key__ = '';
		},
		set_context_menu_style__: function(){
			var s = this.context_el__.getBoundingClientRect();
			//this.finx_zindex(this.context_el__);
			var s5 = window.scrollY;
			if( this.popup_modal_displayed__ ){
				var s2 = document.getElementById("popup_modal_body__").getBoundingClientRect();
				this.context_style__ = "display:block;top: "+(Number(s.top)-Number(s2.top))+"px;left: "+(Number(s.left)-Number(s2.left))+"px;";
			}else{
				this.context_style__ = "display:block;top: "+(s5+s.top)+"px;left: "+s.left+"px;";
			}
		},
		set_simple_popup_style__: function(){
			var s = this.simple_popup_el__.getBoundingClientRect();
			var s5 = window.scrollY;
			this.simple_popup_style__ = "top: "+(s5+s.top)+"px;left: "+s.left+"px;";
		},
		find_zindex__: function(el){
			for(var i=0;i<20;i++){
				el = el.parentNode;
			}
		},
		context_menu_key_match__: function(v){
			if( this.context_menu_key__ == "" ){
				return true;
			}else if( v.toLowerCase().indexOf(this.context_menu_key__.toLowerCase() ) > -1 ){
				return true;
			}
		},
		context_menu_key_highlight__: function(v){
			var r = new RegExp( this.context_menu_key__ , "i" );
			var c = v.match( r );
			return v.replace( c, "<span>"+c+"</span>" );
		},
		context_menu_thing_highlight__: function(v){
			var r = new RegExp( this.context_menu_key__ , "i" );
			var c = v['l']['v'].match( r );
			if( v['l']['v'] == v['i']['v'] ){
				return v['l']['v'].replace( c, "<span>"+c+"</span>" );
			}else{
				return v['i']['v'] + ": " + v['l']['v'].replace( c, "<span>"+c+"</span>" );
			}
		},
		context_get_type_notation__: function(v){
			if( v['t'] == "PLG" ){
				return ': <abbr>Plugin: '+ v['plg'] +'</abbr>';
			}else if( v['t'] == "THL" ){
				return ': <abbr>Thing List: '+ v['th'] +'</abbr>';
			}else if( v['t'] == "TH" ){
				return ': <abbr>Thing: '+ v['th'] +'</abbr>';
			}else{
				return ': <abbr>'+this.data_types__[v['t']]+'</abbr>';
			}
		},
		context_select__: function(k, t){
			//console.log( "context select: "+ this.context_for__  + ": " + this.context_datavar__ + ": " + k +  ": " + t );
			if( this.context_for__ == 'engine' ){
				this.set_sub_var__( this.engine__, this.context_datavar__, k );
				if( t == "inputtype" ){
					this.update_variable_type__( this.engine__, this.context_datavar__, k );
				}
			}else if( this.context_for__ == 'test__' ){
				this.set_sub_var__( this.test__, this.context_datavar__, k );
				console.log( t );
				if( t == "datatype" ||  t == "inputtype" ){
					this.update_variable_type__( this.test__, this.context_datavar__, k );
				}
			}else if( this.context_for__ == 'api' ){
				this.set_sub_var__( this.api__, this.context_datavar__, k );
				if( this.context_datavar__ == "input-method" ){
					if( k == 'GET' ){
						this.set_sub_var__(this.api__, 'input-type', 'query_string' );
						this.set_sub_var__(this.api__, 'output-type', 'application/json' );
					}else if( k == 'POST' ){
						this.set_sub_var__(this.api__, 'input-type', 'application/json' );
						this.set_sub_var__(this.api__, 'output-type', 'application/json' );
					}
				}
			}else if( this.context_for__ == 'stages' ){
				if( this.context_datavar__ == "k" ){
					if( t == 'o' ){
						var d = this.get_o_sub_var__( this.all_factors_stage_wise__[ this.context_stage_id__ ], k );
						if( d ){
							t = d['t'];
						}else{
							this.echo__( k + " not found in stage_vars ");
						}
					}
					if( t == 'c' ){

					}
					var k = {
						"v": k,
						"t": t,
						"vs": false,
					};
					this.stage_change_stage__(this.context_stage_id__, k, t);
					this.hide_context_menu__();
					this.updated_option__();return;

				}else{
					if( t == "datatype" && this.context_datavar__ == "d:rhs:t" && k == "ctf__" ){
						this.stage_change_stage_to_function__( this.context_stage_id__ );
						this.hide_context_menu__();
						return;
					}else{
						if( typeof(k) == "string" || typeof(k) == "number" ){
							this.set_stage_sub_var__( this.context_stage_id__, this.context_datavar__, k );
						}
						if( t == 'prop' ){
							var vt = this.get_stage_sub_var__( this.context_stage_id__, this.context_datavar_parent__+":t" );
							if( vt in this.config_object_properties__ ){
								if( k in this.config_object_properties__[ vt ] ){
									this.set_stage_sub_var__( this.context_stage_id__, this.context_datavar_parent__+":vs:d", this.json__(this.config_object_properties__[ vt ][k]) );
								}
							}
						}
						if( t == "plugin" ){
							if( k in this.plugin_data__ ){
								var x = this.context_datavar__.split(/\:/g);
								x.pop(0);
								var dvp = x.join(":");
								this.set_stage_sub_var__( this.context_stage_id__, dvp+':vs', {"v": ".", "t": "n", "d": {}} );
							}else{
								console.error("selected plugin: " + k + " not found");
								this.set_stage_sub_var__( this.context_stage_id__, this.context_datavar_, "" );
							}
						}
						if( t == "thing" ){
							this.set_stage_sub_var__( this.context_stage_id__, this.context_datavar__, k );
						}
						if( t == "datatype" ){
							if( k == "ctf__" ){
								
							}else{
								this.update_variable_type__( this.engine__['stages'][ this.context_stage_id__ ], this.context_datavar__, k );
								if( this.engine__['stages'][ this.context_stage_id__ ]['k']['v'] == "Let" ){
									var a = this.engine__['stages'][ this.context_stage_id__ ]['d']['lhs'];
									if( this.engine__['stages'][ this.context_stage_id__ ]['d']['rhs']['t'] == "Function" ){
										var t = this.engine__['stages'][ this.context_stage_id__ ]['d']['rhs']['v']['return']+'';
									}else{
										var t = this.engine__['stages'][ this.context_stage_id__ ]['d']['rhs']['t'];
									}
									if( t == "TT" ){ t = "T"; }
									if( t != "Function" ){
										setTimeout(this.update_variable_type_change_in_sub_stages__, 100, this.context_stage_id__, a, t);
									}
								}
							}
						}
						if( t == "function" ){
							if( k != "" ){
								if( k in this.functions__ ){
									var vt = this.context_datavar_parent__+":inputs";
									this.set_stage_sub_var__( this.context_stage_id__, vt, {} );
									var p__ = this.json__( this.functions__[k]['inputs'] );
									var r__ = this.functions__[k]['return'];
									var s__ = this.functions__[k]['self'];
									setTimeout(this.set_function_inputs__, 100, this.context_datavar_parent__, p__, r__, s__);
								}else{
									console.log("function error: " + k + " not found!");
								}
							}
						}
						if( t == "var" ){
							var d = this.get_o_sub_var__( this.all_factors_stage_wise__[ this.context_stage_id__ ], k );
							if( d ){
								var x = this.context_datavar__.split(/\:/g);
								x.pop();
								var new_path = x.join(":");
								var var_type = d['t'];
								//console.log( var_type );
								this.set_stage_sub_var__( this.context_stage_id__, new_path+':t', var_type );
								this.set_stage_sub_var__( this.context_stage_id__, new_path+':vs', {"v": "","t": "","d": {} } );
								if( var_type in this.plugin_data__ ){
									this.set_stage_sub_var__( this.context_stage_id__, new_path+':plg', var_type, true );
								}else{
									this.remove_stage_sub_var__( this.context_stage_id__, new_path+':plg' );
								}
								var s = this.get_stage_sub_var__( this.context_stage_id__, new_path );
								this.set_stage_sub_var__( this.context_stage_id__, new_path, this.json__( s ) );
							}
						}
						if( t == "operator" ){
							var op = this.get_stage_sub_var__( this.context_stage_id__, this.context_datavar__ );
							x = this.context_datavar__.split(/\:/g);
							x.pop();
							var vn = Number(x.pop());
							var mvar = x.join(":");
							var mdata = this.get_stage_sub_var__( this.context_stage_id__, mvar );
							if( mvar == "d:rhs" ){
								if( op == "." ){
									while( mdata.length-1 > vn ){
										mdata.pop();
									}
									this.set_stage_sub_var__( this.context_stage_id__, mvar, mdata );
								}else{
									if( mdata.length-1 == vn ){
										mdata.push({ "m": [ {"t":"N","v":"333", "OP":"."} ], "OP": "." });
										this.set_stage_sub_var__( this.context_stage_id__, mvar, mdata );
									}else{
										this.echo__("update existing operator");
									}
								}
							}else{
								if( op == "." ){
									while( mdata.length-1 > vn ){
										mdata.pop();
									}
									this.set_stage_sub_var__( this.context_stage_id__, mvar, mdata );
								}else{
									if( mdata.length-1 == vn ){
										mdata.push({"t":"N","v":"333", "OP":"."});
										this.set_stage_sub_var__( this.context_stage_id__, mvar, mdata );
									}else{
										this.echo__("update existing operator");
									}
								}
							}
						}
						if( this.context_callback__ ){
							var x = this.context_callback__.split(/\:/g);
							var vref = x.splice(0,1);
							if( vref in this.$refs ){
								if( "length" in this.$refs[ vref ] ){
									this.$refs[ vref ][0].callback__(x.join(":"));
								}else{
									this.$refs[ vref ].callback__(x.join(":"));
								}
							}else{
								console.error("Ref: " + vref + ": not found");
								//this.$refs[ x[0] ][ x[1] ]();
							}
						}
					}
				}
			}else{
				console.error("context_select error: data_for unknown: "+ this.context_for__ );
			}
			this.hide_context_menu__();
			setTimeout(this.updated_option__,100);
		},
		set_function_inputs__: function(v, p, r, s){
			var vt = v+":inputs";
			this.set_stage_sub_var__( this.context_stage_id__, vt, p );
			var vt = v+":return";
			this.set_stage_sub_var__( this.context_stage_id__, vt, r );
			var vt = v+":self";
			this.set_stage_sub_var__( this.context_stage_id__, vt, s );
		},
		set_stage_sub_var__: function( vstagei, datavar, d, create_sub_node = false ){
			this.set_sub_var__( this.engine__['stages'][ vstagei ], datavar, d, create_sub_node );
		},
		set_sub_var__: function( vv, vpath, value, create_sub_node = false ){
			try{
				var x = vpath.split(":");
				var k = x[0];
				if( k.match(/^[0-9]+$/) ){
					k = Number(k);
				}
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( typeof(vv[ k ]) == "object" && vv[ k ] != null ){
							return this.set_sub_var__( vv[ k ], x.join(":"), value, create_sub_node );
						}else{
							return false;
						}
					}else{
						vv[k] = value;
						return true;
					}
				}else{
					if( create_sub_node ){
						if( x.length == 1 ){
							vv[ k ] = value;
						}else{
							return false;
						}
					}else{
						return false;
					}
				}
			}catch(e){console.error(e);console.log("set_sub_var__ error: " + vpath );return false;}
		},
		remove_stage_sub_var__: function( vstagei, datavar ){
			this.remove_sub_var__( this.engine__['stages'][ vstagei ], datavar );
		},
		remove_sub_var__: function( vv, vpath ){
			// this.echo__("set_sub_var__: " + vpath + " - " + value + " : " + (create_sub_node?'create_sub_node':'')) ;
			// this.echo__( vv );
			try{
				var x = vpath.split(":");
				//this.echo__( x );
				var k = x[0];
				if( k.match(/^[0-9]+$/) ){
					k = Number(k);
				}
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( typeof(vv[ k ]) == "object" && vv[ k ] != null ){
							this.set_sub_var__( vv[ k ], x.join(":") );
						}
					}else{
						delete(vv[k]);
					}
				}
			}catch(e){console.error(e);console.log("set_sub_var__ error: " + vpath );return false;}
		},
		get_stage_sub_var__: function( stage_id, datavar ){
			var d = this.get_sub_var__( this.engine__['stages'][ stage_id ], datavar );
			if( d === false ){
				console.error("get stage sub var error: " + stage_id + ": " + datavar + ": ");
				this.echo__( this.engine__['stages'][ stage_id ] );
			}
			return d;
		},
		get_sub_var__: function(vv, vpath){
			// this.echo__("get_sub_var__: " + vpath);
			// this.echo__( vv );
			try{
				var x = vpath.split(":");
				//this.echo__( x );
				var k = x[0];
				if( k.match(/^[0-9]+$/) && "length" in vv ){
					k = Number(k);
				}
				// console.log("Key: " + k );
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( typeof(vv[ k ]) == "object" && vv[ k ] != null ){
							var a_ = this.get_sub_var__( vv[ k ], x.join(":") );
							return a_;
						}else{
							// console.log( "xx" );
							return false;
						}
					}else{
						// console.log( "yy" );
						return vv[k];
					}
				}else{
					// console.log( "dd" );
					return false;
				}
			}catch(e){console.log("get_sub_var__ error: " + vpath + ": " + e );return false;}
		},
		is_token_ok__(t){
			if( t!= "OK" && t.match(/^[a-f0-9]{24}$/)==null ){
				setTimeout(this.token_validate__,100,t);
				return false;
			}else{
				return true;
			}
		},
		open_edit_form__: function(){
			this.edit_modal__ = new bootstrap.Modal(document.getElementById('edit_modal'));
			this.edit_modal__.show();
			this.cmsg__ = ""; this.cerr__ = "";
			this.edit_api__ = this.json__(this.api__);
		},
		token_validate__(t){
			if( t.match(/^(SessionChanged|NetworkChanged)$/) ){
				this.err__ = "Login Again";
				alert("Need to Login Again");
			}else{
				this.err__ = "Token Error: " + t;
			}
		},
		replace_variables_in_object__: function( vd ){
			return vd;
		},
		cleanit__( v ){
			v = v.replace( /\-/g, "DASH" );
			v = v.replace( /\_/g, "UDASH" );
			v = v.replace( /\W/g, "-" );
			v = v.replace( /DASH/g, "-" );v = v.replace( /UDASH/g, "_" );
			v = v.replace( /[\-]{2,5}/g, "-" );
			v = v.replace( /[\_]{2,5}/g, "_" );
			return v;
		},
		editnow__(){
			this.cerr__ = "";
			this.edit_api__['name'] = this.cleanit__(this.edit_api__['name']);
			if( this.edit_api__['name'].trim() == "" ){
				this.cerr__ = "Name incorrect";
				return false;
			}
			if( this.edit_api__['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\t\ \r\n]{5,200}$/i) == null ){
				this.cerr__ = "Description incorrect. Special chars not allowed";
				return false;
			}
			this.cmsg__ = "Editing...";
			axios.post("?", {
				"action":"get_token",
				"event":"edit_"+this.property_type+this.edit_api__['_id'],
				"expire":2
			}).then(response=>{
				this.msg__ = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token__ = response.data['token'];
								if( this.is_token_ok__(this.token__) ){
									axios.post("?", {
										"action": "edit_"+this.property_type, 
										"edit_api": this.edit_api__,
										"token": this.token__
									}).then(response=>{
										this.cmsg__ = "";
										if( response.status == 200 ){
											if( typeof(response.data) == "object" ){
												if( 'status' in response.data ){
													if( response.data['status'] == "success" ){
														this.cmsg__ = "Created";
														this.edit_modal__.hide();
														this.api__ = JSON.parse( JSON.stringify(this.edit_api__));
													}else{
														this.cerr__ = response.data['error'];
													}
												}else{
													this.cerr__ = "Incorrect response";
												}
											}else{
												this.cerr__ = "Incorrect response Type";
											}
										}else{
											this.cerr__ = "Response Error: " + response.status;
										}
									});
								}
							}else{
								alert("Token error: " + response.dat['data']);
								this.err__ = "Token Error: " + response.data['data'];
							}
						}else{
							this.err__ = "Incorrect response";
						}
					}else{
						this.err__ = "Incorrect response Type";
					}
				}else{
					this.err__ = "Response Error: " + response.status;
				}
			});
		},
		load_initial_data__: function(){
			var vd__ = {
				"action"		: "load_engine_data",
			};
			axios.post( "?",vd__).then(response=>{
				if( response.data["status"] == "success" ){
					if( typeof( response.data["engine"] ) == "object" && 'length' in response.data["engine"] == false ){
						this.engine__		= response.data["engine"];
					}
					if( typeof( response.data["test"] ) == "object" && 'length' in response.data["test"] == false ){
						this.test__ 		= response.data["test"];
						// if( 'headers' in this.test__ ){

						// }
					}
					this.load_initial_data2__();
				}else{
					alert("Server Error.Please Try After Sometime");
				}
			});
		},
		load_initial_data2__: function(){
			if( "input_factors" in this.engine__ == false ){
				this.engine__['input_factors'] ={};
				this.save_need__=true;
			}else if( "length" in this.engine__["input_factors"] ){
				this.engine__['input_factors'] = {};
				this.save_need__=true;
			}
			if( 'stages' in this.engine__ == false ){
				if( this.page_type =='codeeditor' ){
					this.engine__['stages'] = [
						{
							"k": {"v": "Let", "t": "c", "vs": false}, "pk": "Let",
							"t": "c",
							"d": {"lhs": "a","rhs": {"t": "N","v": 10}},
							"l": 1,"e": false,"ee": true,"er": "","wr": "", "a": false,
						}
					];
				}else{
					this.engine__['stages'] = [
						    {
						      "k": {"v": "Let", "t": "c", "vs": false}, "pk": "Let",
						      "t": "c",
						      "d": {"lhs": "a","rhs": {"t": "N","v": 10}},
						      "l": 1,"e": false,"ee": true,"er": "","wr": "", "a": false,
						    },
						    {
						      "k": {"v": "Let", "t": "c", "vs": false}, "pk": "Let",
						      "t": "c",
						      "d": {"lhs": "b","rhs": {"t": "N","v": 10}},
						      "l": 1,"e": false,"ee": true,"er": "","wr": "", "a": false,
						    },
						    {
						      "k": {"v": "Let", "t": "c", "vs": false}, "pk": "Let",
						      "t": "c",
						      "d": {"lhs": "c","rhs": {"t": "N","v": 0}},
						      "l": 1,"e": false,"ee": true,"er": "","wr": "", "a": false,
						    },
						    {
						      "k": {"v": "Math","t": "c","vs": false},"pk": "Math",
						      "t": "c",
						      "d": {
						        "lhs": {"t": "V","v": {"v": "c","t": "N","vs": false}},
						        "rhs": [
						            {"m": [
						              {"t": "V","v": {"v": "a","t": "N","vs": false},"OP": "+"},
						              {"t": "V","v": {"v": "b","t": "N","vs": false},"OP": "+"},
						              {"t": "N","v": "10","OP": "."}
						            ],"OP": "."}
						        ]
						      },
						      "l": 1,"e": false,"ee": true,"er": "","wr": "", "a": false,
						    },
						    {
								"k": {"v": "RespondJSON", "t": "c", "vs": false}, "pk": "RespondJSON",
								"t": "c",
								"d": {
									"output": {
									  "t": "O",
									  "v": {
										"status": {"t": "T","v": "success","k": "status"},
										"data": {"t": "V","v": {"v": "c","t": "N","vs": {"v": "","t": "","d": []}},"k": "data"}
									  }
									},
									"pretty": {"t": "B","v": "false"}
								},
								"l": 1,"e": false,"ee": true,"er": "","wr": "", "a": false,
							}
					];
				}
				this.save_need__=true;
			}else{
				this.first_save__ = true;
				for(var i=0;i<this.engine__['stages'].length;i++){
					if( 'a' in this.engine__['stages'][i] == false ){
						this.engine__['stages'][i]['a'] = false;
						this.save_need__=true;
					}
				}
			}
			var dt = new Date();
			this.current_year__ = dt.getFullYear();
			this.date_today__ = dt.toJSON().substr(0,10);
			this.datetime__ = dt.toJSON().substr(0,19).replace("T", " " );
			this.find_checks__();
			this.fill_variables__();
			this.vshow__	= true;
			this.select_test_environment__();
		},
		input_type_change__: function(){
			if( this.property_type == "api" ){
				if( this.api__['input-type'] == "application/x-www-form-urlencoded" ){
					for(var i in this.engine__['input_factors'] ){
						if( this.engine__['input_factors'][i]['t'] != "T" && this.engine__['input_factors'][i]['t'] != "N" ){
							this.engine__['input_factors'][i]['t'] =  "T";
							this.engine__['input_factors'][i]['v'] = "";
						}
					}
				}
				this.save_need__ = true;
			}
		},
		get_object_props_list: function( stage_id, k ){
			console.log("Get object props list: " + stage_id + ": " + k );
			//this.echo__( this.all_factors_stage_wise__[ stage_id ]  );
			var o = [];
			if( k in this.all_factors_stage_wise__[ stage_id ] ){
				if( this.all_factors_stage_wise__[ stage_id ][k]['t'] == "O" && '_' in this.all_factors_stage_wise__[ stage_id ][k] ){
					o = this.get_object_to_list__( this.all_factors_stage_wise__[ stage_id ][k]['_'] );
				}
				if( this.all_factors_stage_wise__[ stage_id ][k]['t'] == "L" && '_' in this.all_factors_stage_wise__[ stage_id ][k] ){
					o = this.get_object_to_list__( this.all_factors_stage_wise__[ stage_id ][k]['_'] );
				}
			}
			//this.echo__( o );
			return o;
		},
		get_object_to_list__: function( vd ){
			// this.echo__( "get_object_to_list__" );
			// this.echo__( vd );
			var v = this.get_object_to_list2__( vd, "" );
			return v
		},
		get_object_to_list2__: function( vd, vp ){
			// this.echo__( vd );
			// this.echo__( vp );
			var v = [];
				for( var i in vd ){
					v.push({
						"k": vp + i,
						"t": vd[i]['t'],
					});
					if( vd[i]['t'] == "O" ){
						var v2 = this.get_object_to_list2__( vd[i]['_'], vp + i + "->" );
						for( var i2=0;i2<v2.length;i2++){
							v.push( v2[i2] );
						}
					}
					if( vd[i]['t'] == "L" ){
						if( typeof(vd[i]['_'])=="object" ){
							if( "length" in vd[i]['_'] ){
								if( vd[i]['_'].length >0 ){
									v.push({
										"k": vp + i + "->[]",
										"t": vd[i]['_']['t'],
									});
									if( vd[i]['_']['t'] == "O" ){
										var v2 = this.get_object_to_list2__( vd[i]['_']['_'], vp + i + "->[]->" );
										for( var i2=0;i2<v2.length;i2++){
											v.push( v2[i2] );
										}
									}
								}
							}
						}
					}
				}
			return v;
		},
		ksort__: function( vd ){
			var oo = {};
			var _o = Object.keys(vd).sort();
			for( var i in _o ){
				oo[ _o[i]+"" ] = vd[ _o[i] ];
			}
			return oo
		},
		get_object_notation__( v ){
			var vv = {};
			if( typeof(v)==null ){
				console.error("get_object_notation: null ");
			}else if( typeof(v)=="object" ){
				if( "length" in v == false ){
					for(var k in v ){
						if( v[k]['t'] == "V" ){
							vv[ k ] = this.data_types__[ v[k]['t'] ] + "["+v[k]['v']['v']+"]";
							if( 'vs' in v[k]['v'] ){
								if( v[k]['v']['vs'] ){
									if( v[k]['v']['vs']['v'] ){
										vv[ k ] = vv[ k ] + '->' + v[k]['v']['vs']['v'];
									}
								}
							}
						}else{
							vv[ k ] = this.derive_value__(v[k]);
						}
					}
				}else{ console.error("get_object_notation: got list instead of object "); this.echo__(v); }
			}else{ console.error("get_object_notation: incorrect type: "+ typeof(v) ); }
			return Object.fromEntries(Object.entries(vv).sort());
		},
		get_list_notation__( v ){
			var vv = [];
			if( typeof(v)=="object" ){
				if( "length" in v ){
					for(var k=0;k<v.length;k++ ){
						if( v[k]['t'] == "V" ){
							nv = this.data_types__[ v[k]['t'] ] + "["+v[k]['v']['v']+"]";
							if( 'vs' in v[k]['v'] ){
								if( v[k]['v']['vs'] ){
									if( v[k]['v']['vs']['v'] ){
										nv = nv + '->' + v[k]['v']['vs']['v'];
									}
								}
							}
							vv.push(nv);
						}else{
							vv.push( this.derive_value__(v[k]) );
						}
					}
				}else{ console.error("get_list_notation: not a list "); }
			}else{ console.error("get_list_notation: incorrect type: "+ typeof(v) ); }
			return vv;
		},
		find_o_sub_var__: function( vv, vpath ){
			try{
				//console.log( "find_o_sub_var__: "+ vpath );
				var x = vpath.split("->",1);
				var k = x[0];
				if( k == "[]" ){
					x.splice(0,1);
					k = x[0];
				}
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( vv[ k ]['t'] == "O" ){
							return this.find_o_sub_var__( vv[ k ]['_'], x.join("->") );
						}else if( vv[ k ]['t'] == "L" ){
							return this.get_o_sub_var__( vv[ k ]['_'], x.join("->") );
						}else{
							return false;
						}
					}else{
						return true;
					}
				}else{
					return false;
				}
			}catch(e){console.log("find_o_sub_var__ error");return false;}
		},
		get_o_sub_var__: function( vv, vpath ){
			//this.echo__("get_o_sub_var__: " );this.echo__( vv ); this.echo__( vpath );
			try{
				var x = vpath.split("->");
				var k = x[0];
				if( k == "[]" ){
					x.splice(0,1);
					k = x[0];
				}
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( vv[ k ]['t'] == "O" ){
							return this.get_o_sub_var__( vv[ k ]['_'], x.join("->") );
						}else if( vv[ k ]['t'] == "L" ){
							return this.get_o_sub_var__( vv[ k ]['_'], x.join("->") );
						}else{
							return false;
						}
					}else{
						return vv[ k ];
					}
				}else{
					return false;
				}
			}catch(e){
				//console.log("get_o_sub_var__ error");
				this.echo__("get_o_sub_var__:" + vpath);this.echo__(vv);
				return false;
			}
		},
		set_o_sub_var__: function( vv, vpath, value ){
			//this.echo__("set_o_sub_var__: " );this.echo__( vv ); this.echo__( vpath );
			try{
				var x = vpath.split("->");
				var k = x[0];
				if( k == "[]" ){
					x.splice(0,1);
					k = x[0];
				}
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( vv[ k ]['t'] == "O" ){
							this.set_o_sub_var__( vv[ k ]['_'], x.join("->"), value );
						}else if( vv[ k ]['t'] == "L" ){
							this.get_o_sub_var__( vv[ k ]['_'], x.join("->"), value );
						}else{
							console.log("set_o_sub_var__: false");
						}
					}else{
						vv[ k ]['_'] = value['_'];
					}
				}else{
					vv[ k ]['_'] = value['_'];
				}
			}catch(e){
				//console.log("get_o_sub_var__ error");
				//this.echo__("get_o_sub_var__:" + vpath);this.echo__(vv);
				return false;
			}
		},
		get_variable_final_form_as_input__: function( v ){
			//console.log("Get variable finaal form:" );
			//this.echo__( v );
			var t = v['t']+'';
			if( t == "TT" ){ t = "T"; }
			var vv = {"t": t+''};
			if( 'vs' in v ){
				//console.log()
				if( v['vs']['v'] ){
					if( v['vs']['v']  in this.config_object_properties__[ v['t'] ] ){
						var fn = this.config_object_properties__[ v['t'] ][ v['vs']['v'] ];
						if( fn['return'] == "self" ){
							vv['t'] = v['t']+'';
						}else{
							vv['t'] = fn['return'];
						}
					}else{
						this.echo__("prop: " + v['vs']['v'] + " not found in type: " + v['t'] );
					}
				}
			}
			if( v['t'] == "TH" ){
				if( '_' in v ){
					vv['_'] = this.json__(v['_']);
				}else if( typeof(v['v'])=="object" && v['v'] != null ){
					this.echo__( v['v'] );
					this.echo__("not knowing from here");
					vv['_'] = this.convert_array_structure_to_stage_vars__( this.json__(v['v']) );
				}else{
					this.echo__("variable type "+v['t'] + " missing sub structure");
				}
			}else if( v['t'] == "L" ){
				if( '_' in v ){
					vv['_'] = this.json__(v['_']);
				}else if( typeof(v['v'])=="object" && "length" in v['v'] ){
					if( v['v'].length > 0 ){
						var sb = {'t': v['v'][0]['t']};
						if( sb['t'] == "O" ){
							sb['_'] = this.convert_array_structure_to_stage_vars__( this.json__(v['v'][0]['v']) );
						}else if( sb['t'] == "L" ){
							sb['_'] = this.convert_array_structure_to_stage_vars__( this.json__(v['v'][0]['v'][0]) );
						}
						vv['_'] = [sb];
					}else{
						vv['_'] = [];
					}
				}else{
					this.echo__("variable type "+v['t'] + " missing sub structure");
				}
			}else if( v['t'] == "O" ){
				if( '_' in v ){
					vv['_'] = this.json__(v['_']);
				}else if( typeof(v['v'])=="object" && v['v'] != null && "length" in v['v'] == false ){
					if( Object.keys(v['v']).length > 0 ){
						vv['_'] = this.convert_array_structure_to_stage_vars__( this.json__(v['v']) );
					}else{
						vv['_'] = {};
					}
				}else{
					this.echo__("variable type "+v['t'] + " missing sub structure");
				}
			}else{
			}
			//this.echo__( vv );
			return vv;
		},
		convert_array_structure_to_stage_vars__: function(v){
			// this.echo__( v );
			if( typeof(v)=='object' && 'length' in v ){
				var vv = [];
				for(var k=0;k<v.length;k++ ){
					vv[k] = {"t": v[k]['t']};
					if( v[k]['t'] == 'O'  ){
						if( typeof(v[k]['v']) == "object" && "length" in v[k]['v'] == false ){
							vv[k]['_'] = this.convert_array_structure_to_stage_vars__( v[k]['v'] );
						}else{
							vv[k]['_'] = {};
						}
					}
				}
				return vv;
			}else if( typeof(v)=='object' && 'length' in v == false ){
				var vv = {};
				for(var k in v ){
					vv[k] = {"t": v[k]['t']};
					if( v[k]['t'] == 'V' ){
						// this.echo__("Found variable ");
						// this.echo__( v[k] );
						// this.echo__( this.stagei__ );
						var d = this.get_o_sub_var__( this.all_factors_stage_wise__[ this.stagei__ ], v[k]['v']['v'] );
						//this.echo__( d );
						vv[k] = d;
					}else if( v[k]['t'] == 'O' ){
						if( typeof(v[k]['v']) == "object" && "length" in v[k]['v'] == false ){
							vv[k]['_'] = this.convert_array_structure_to_stage_vars__( v[k]['v'] );
						}else{
							vv[k]['_'] = {};
						}
					}else if( v[k]['t'] == 'L' ){
						if( typeof(v[k]['v']) == "object" && "length" in v[k]['v']  ){
							vv[k]['_'] = this.convert_array_structure_to_stage_vars__( v[k]['v'] );
						}else{
							vv[k]['_'] = [];
						}
					}
				}
			}
			// this.echo__( vv );
			return vv;
		},
		echo__: function(v){
			if( typeof(v) == "object" ){
				console.log( JSON.stringify(v,null,4) );
			}else{
				console.log( v );
			}
		},
		derive_value__: function(v ){
			if( v['t'] == "T" || v['t']== "D" ){
				return v['v'];
			}else if( v['t']== "N" ){
				return Number(v['v']);
			}else if( v['t'] == 'O' ){
				return this.get_object_notation__(v['v']);
			}else if( v['t'] == 'L' ){
				return this.get_list_notation__(v['v']);
			}else if( v['t'] == 'B' ){
				return (v['v']?true:false);
			}else{
				return "unknown";
			}
		},
		get_object_array__: function( v ){
			//this.echo__("get_object_array__");
			//this.echo__(v );
			var val = {};
			for( var i in v ){
				if( v[i]['t'] == "L" ){
					val[ i ] = this.get_list_array__( v[i]['v'] );
				}else if( v[i]['t'] == "O" ){
					val[ i ] = this.get_object_array__( v[i]['v'] );
				}else if( v[i]['t'] == "B" ){
					val[ i ] = v[i]['v']=="true"?true:false;
				}else if( v[i]['t'] == "T" ){
					val[ i ] = String( v[i]['v'] );
				}else if( v[i]['t'] == "N" ){
					val[ i ] = Number( v[i]['v'] );
				}else{
					val[ i ] = v[i]['v'];
				}
			}
			//this.echo__(val );
			return val;
		},
		get_list_array__: function( v ){
			//this.echo__("get_list_array__");
			//this.echo__(v );
			var val = [];
			for( var i=0;i<v.length;i++){
				val.push("");
				if( v[i]['t'] == "L" ){
					val[ i ] = [];
					for(var k=0;k<v[i]['v'].length;k++){
						val[i].push( this.get_list_array__( v[i]['v'] ) );
					}
				}else if( v[i]['t'] == "O" ){
					val[ i ] = this.get_object_array__( v[i]['v'] );
				}else if( v[i]['t'] == "B" ){
					val[ i ] = v[i]['v']=="true"?true:false;
				}else if( v[i]['t'] == "T" ){
					val[ i ] = String( v[i]['v'] );
				}else if( v[i]['t'] == "N" ){
					val[ i ] = Number( v[i]['v'] );
				}else{
					val[ i ] = v[i]['v'];
				}
			}
			//this.echo__(val );
			return val;
		},
		input_factors_to_values__: function(v){
			var vv = {};
			for( var k in v ){
				if( v[ k ]['t'] == "T" ){
					vv[k] = {"k":k, "t":"T", "v": "", "m":v[k]['m']};
				}else if( v[ k ]['t'] == "N" ){
					vv[k] = {"k":k,"t":"N", "v": 0, "m":v[k]['m']};
				}else if( v[ k ]['t'] == "D" ){
					vv[k] = {"k":k,"t":"D", "v": "2023-03-23", "m":v[k]['m']};
				}else if( v[ k ]['t'] == "DT" ){
					vv[k] = {"k":k,"t":"DT", "v": "2023-03-23 23:23:23", "m":v[k]['m']};
				}else if( v[ k ]['t'] == "TS" ){
					vv[k] = {"k":k,"t":"TS", "v": "2023-03-23 23:23:23", "m":v[k]['m']};
				}else if( v[ k ]['t'] == "L" ){
					var vvv = [];
					for( var vi=0; vi<v[ k ]['v'].length; vi++ ){
						vvv.push( {"t":"O","v": this.input_factors_to_values__( v[ k ]['v'][ vi ]['v'] ) } );
					}
					vv[k] = {"k":k,"t":"L", "v": vvv , "m":v[k]['m']};
				}else if( v[ k ]['t'] == "O" ){
					vv[k] = {"k":k,"t":"O", "v": this.input_factors_to_values__( v[ k ]['v'] ) , "m":v[k]['m']};
				}else if( v[ k ]['t'] == "B" ){
					vv[k] = {"k":k,"t":"B", "v": [], "m":v[k]['m']};
				}else if( v[ k ]['t'] == "NL" ){
					vv[k] = {"k":k,"t":"NL", "v": null, "m":v[k]['m']};
				}
			}
			return vv;
		},
		update_variable_type__: function(data, data_var, val){
			try{
				var x = data_var.split(/\:/g);
				if( x.length> 1 ){
					var new_Val = "sssssss";
					x[ x.length-1 ] = 'v';
					var data_var2 = x.join(":");
					if( val == "N" ){
						var s = this.get_sub_var__( data, data_var2);
						if( typeof(s)=="string" ){
							if( s.match(/^[0-9\.]+$/) ){
								new_val=Number(s);
							}else{
								new_val=0;
							}
						}else{
							new_val=0;
						}
					}else if( val == "T" || val == "TT" || val == "HT" ){
						new_val= String(this.get_sub_var__( data, data_var2));
					}else if( val == "TI" ){
						new_val={"i":{"t":"T", "v":""}, "l": {"t":"T", "v":""}};
					}else if( val == "TH" ){
						new_val={"th":"", "i":{"t":"T", "v":""}, "l": {"t":"T", "v":""}};
					}else if( val == "THL" ){
						new_val={"th":{"t":"T", "v":""}, "list":[{"i":{"t":"T", "v":"id"}, "l":{"t":"T", "v":"Label"}}]};
					}else if( val == "L" ){
						new_val=[{"t":"O", "v":{"one":{"k":"one", "t":"T","v":""}} }];
					}else if( val == "O" ){
						new_val={};
					}else if( val == "NL" ){
						new_val=null;
					}else if( val == "V" ){
						new_val={"v":"", "t": "c", "vs":false};
					}else if( val == "D" ){
						new_val="<?=date("Y-m-d") ?>";
					}else if( val == "DT" ){
						new_val={"v":"<?=date("Y-m-d H:i:s") ?>", "t": "DT", "tz":"UTC+00:00"};
					}else if( val == "TS" ){
						new_val=<?=time() ?>;
					}else if( val == "MongoQ" ){
						new_val=[{
							"f":{"t":"T", "v":"field"},
							"c":{"t":"T", "v":"="},
							"v":{"t":"T", "v":"value"},
						}];
					}else if( val == "MysqlQ" ){
						new_val=[{
							"f":{"t":"T", "v":"field"},
							"c":{"t":"T", "v":"="},
							"v":{"t":"T", "v":"value"},
							"n":{"t":"T", "v":"and"}
						}];
					}else if( val == "B" ){
						new_val=true;
					}else if( val in this.functions_data__ ){
						new_val= this.json__( this.functions_data__[val][0] );
					}else{
						new_val="Unknown";
					}
					this.set_sub_var__(data, data_var2, new_val );
				}
			}catch(e){
				console.error("update_engine_var_datatype__: " + data_var + ": " );
				this.echo__(val);
			}
		},
		updated_option__: function(){
			this.fill_variables__();
			this.save_need__=true;
		},
		json__: function( v ){
			if( typeof(v) == "object" ){
				return JSON.parse( JSON.stringify( v ) );
			}else{
				return v;
			}
		},
		popup_TT__: function(){
			this.set_stage_sub_var__(this.popup_stage_id__, this.popup_datavar__, this.popup_data__);
			this.updated_option__();
		},
		popup_TT_update__: function(){
			this.set_stage_sub_var__(this.popup_stage_id__, this.popup_datavar__, this.popup_data__);
			this.updated_option__();
			if( this.popup_modal__ ){
				this.popup_modal__.hide();
			}
		},
		popup_HT_update__: function(){
			var v = this.ace_editor2.getValue();
			this.set_stage_sub_var__(this.popup_stage_id__, this.popup_datavar__, v);
			this.updated_option__();
			if( this.popup_html_modal__ ){
				this.popup_html_modal__.hide();
			}
		},
		popup_import_json_data__: function(){
			try{
				var d = JSON.parse(this.popup_import_str__);
				this.popup_data__ = this.plain_json_to_template__(d);
				this.set_stage_sub_var__(this.popup_stage_id__, this.popup_datavar__, this.popup_data__);
				this.popup_import__ = false;
				this.updated_option__();
			}catch(e){
				console.log("Popup Import failed: "  + e );
			}
		},
		popup_import_json_data_for_payload__: function(){
			try{
				var d = JSON.parse(this.popup_import_str__);
				this.popup_data__ = this.plain_json_to_template__(d);
				this.set_sub_var__(this.test__, this.popup_datavar__, this.popup_data__);
				this.popup_import__ = false;
				this.updated_option__();
			}catch(e){
				console.log("Popup Import failed: "  + e );
			}
		},
		plain_json_to_template__: function( v ){
			if( typeof(v) == "object" ){
				if( "length" in v == false ){
					for( var key in v ){
						if( v[ key ] == null ){
							v[ key ] = {"k": key, "t":"NL", "v": null };
						}else if( typeof(v[key]) == "object" && v[key] != null ){
							if( "length" in v[ key ] ){
								v[ key ] = {"k": key, "t":"L", "v": this.plain_json_to_template__( v[key] ) };
							}else{
								v[ key ] = {"k": key, "t":"O", "v": this.plain_json_to_template__( v[key] ) };
							}
						}else if( typeof(v[key]) == "string" ){
							v[ key ] = {"k": key, "t":"T", "v": v[key] };
						}else if( typeof(v[key]) == "number" ){
							v[ key ] = {"k": key, "t":"N", "v": v[key]};
						}else if( typeof(v[key]) == "boolean" ){
							v[ key ] = {"k": key, "t":"B", "v": v[key] };
						}else{
							v[ key ] = {"k": key, "t":"T", "v": "Unknown" };
						}
					}
				}else{
					for( var key=0;key<v.length;key++ ){
						if( v[ key ] == null ){
							v[ key ] = {"k": key, "t":"NL", "v": null };
						}else if( typeof(v[key]) == "object" && v[key] != null ){
							if( "length" in v[ key ] ){
								v[ key ] = {"t":"L", "v": this.plain_json_to_template__( v[key] ) };
							}else{
								v[ key ] = {"t":"O", "v": this.plain_json_to_template__( v[key] ) };
							}
						}else if( typeof(v[key]) == "string" ){
							v[ key ] = {"t":"T", "v": v[key] };
						}else if( typeof(v[key]) == "number" ){
							v[ key ] = {"t":"N", "v": v[key]};
						}else if( typeof(v[key]) == "boolean" ){
							v[ key ] = {"t":"B", "v": v[key] };
						}else{
							v[ key ] = {"t":"T", "v": "Unknown" };
						}
					}
				}
			}else{
				console.log("plain_json_to_template__: "+ typeof(v) + " Incorrect data type");
			}
			return v;
		}




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

<?php foreach( $components as $i=>$j ){ ?>
	app.component( "<?=$j ?>", <?=$j ?> );
<?php } ?>
app.mount("#app");

function get_object_notation__( v ){
	console.log("global get_object_notation: " );
	console.log( v );
	var vv = {};
	if( typeof(v)==null ){
		this.err__or("get_object_notation: null ");
	}else if( typeof(v)=="object" ){
		if( "length" in v == false ){
			for(var k in v ){
				if( v[k]['t'] == "V" ){
					vv[ k ] = v[k]['t'] + "["+v[k]['v']['v']+"]";
					if( 'vs' in v[k]['v'] ){
						if( v[k]['v']['vs'] ){
							if( v[k]['v']['vs']['v'] ){
								vv[ k ] = vv[ k ] + '->' + v[k]['v']['vs']['v'];
							}
						}
					}
				}else{
					vv[ k ] = derive_value__(v[k]);
				}
			}
		}else{ console.error("get_object_notation: got list instead of object "); }
	}else{ console.error("get_object_notation: incorrect type: "+ typeof(v) ); }
	return Object.fromEntries(Object.entries(vv).sort());
}
function get_list_notation__( v ){
	var vv = [];
	if( typeof(v)=="object" ){
		if( "length" in v ){
			for(var k=0;k<v.length;k++ ){
				if( v[k]['t'] == "V" ){
					nv = v[k]['t'] + "["+v[k]['v']['v']+"]";
					if( 'vs' in v[k]['v'] ){
						if( v[k]['v']['vs'] ){
							if( v[k]['v']['vs']['v'] ){
								nv = nv + '->' + v[k]['v']['vs']['v'];
							}
						}
					}
					vv.push(nv);
				}else{
					vv.push( derive_value__(v[k]) );
				}
			}
		}else{ console.error("get_list_notation: not a list "); }
	}else{ console.error("get_list_notation: incorrect type: "+ typeof(v) ); }
	return vv;
}
function derive_value__(v ){
	if( v['t'] == "T" || v['t'] == "TT" ||  v['t'] == "HT" || v['t']== "D" ){
		return v['v'].toString();
	}else if( v['t']== "N" ){
		return Number(v['v']);
	}else if( v['t'] == 'O' ){
		return get_object_notation__(v['v']);
	}else if( v['t'] == 'L' ){
		return get_list_notation__(v['v']);
	}else if( v['t'] == 'NL' ){
		return null;
	}else if( v['t'] == 'B' ){
		return (v['v']?true:false);
	}else if( v['t'] == 'DT' ){
		return (v['v']['v'] + " " + v['v']['tz']).toString();
	}else if( v['t'] == 'D' || v['t'] == 'TS' ){
		return (v['v']).toString();
	}else if( v['t'] == 'D' || v['t'] == 'DT' || v['t'] == 'TS' ){
		return (v['v']).toString();
	}else{
		return "unknown: "+ v['t'];
	}
}

</script>