<script>
<?php 

	// functions list
	require("page_apps_apis_api_functions.js");
	require("page_apps_apis_api_functions2.js");
	require("page_apps_apis_api_stage_params.js");
	require("page_apps_apis_api_plugins.js");

	$components = [
		"input_object", "input_values", 
		"inputtextbox", "inputtextbox2", 
		"varselect", "varselect2", "pluginselect",
		"vobject", "vobject2", "vobject_payload", "vlist", 
		"vfield", "vfield_payload", 
		"plugin_database",
		"vdt", "vdtm", "vts",
		"thing", 
		"mongodbv1", "mongoq", "mongop", "mongop2", "mongod", "mongod2", "mongod3", "mongoq_field", "mongop_field",
		"mysqldbv1", "mysqlq", "mysqlp", "mysqld", "mysqls", "mysql_field",
		"internal_table",
		"httprequest",
	];
	$plugins = [
		//"Date"
	];
	foreach( $components as $i=>$j ){
		require($apps_folder."/" . $j . ".js");
	}

?>
const temp1 = {
	template: `<div>temp1 component</div>`
};

String.prototype.toProperCase = function(){
    return this.replace(/\w\S*/g, function(txt){
    	return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
	});
};


var app = Vue.createApp({
	data(){
		return {
			path: '<?=$config_global_apimaker_path ?>apps/<?=$config_param1 ?>/',
			global_data__: {"s":"sss"},
			msg: "",
			err: "",
			cmsg: "",
			cerr: "",
			app__: <?=json_encode($app) ?>,
			function__: <?=json_encode($function) ?>,
			edit_function: {},
			edit_modal: false,
			token: "",
			engine__: {},
			vshow: false,
			settings: {},

			toasts__: [],

			"server_host__"			: "<?=$config_page_domain ?>",
			"version__"			: "<?=$function["_id"] ?>",
			"versions_list"			: {},
			"api_tables__"			: {},
			"api_dynamic_tables__"		: {},
			"api_elastic_tables__"		: {},
			"api_redis_tables__"		: {},
			"api_things__"			: {},
			"page__"			: {},
			"stagei__": -1,
			"alias_lists__"			: {},
			"api_blocks__"			: {},
			"api_articles__"		: {},
			"files_list__"			: {},
			"test1111"			: 11111,
			"show_add_input_factor_form__"	: false,
			"html_elements__"		: {},
			"is_locked__"			: false,
			"verror__"			: "",
			"all_factors__"			: {},
			"all_factors_stage_wise__"	: {},
			"show_saving__"			: false,
			"save_message__"		: "Saving..",
			"save_need__"			: false,
			"show_stages__"			: true,
			"show_test_tab__"		: false,
			"code_editor_full__"	: true,
			"test__"				: {
				"domain": "",
				"path": "",
				"factors": {"t":"O", "v": {}}
			},
			"test_status__"			: "",
			"test_error__"			: "",
			"test_response__"		: false,
			"test_headers_show__"		: false,
			"test_log__"			: [],
			"test_debug__"			: false,
			"test_waiting__"		: false,
			"test_url__"			: "",
			"json_view__"			: false,
            "lock_status__"			: 0,
            "async_used__"			: false,
            "async_skip__"			: true,
			"vrand__"				: "s_" + ( Math.random() * 1000000 ).toFixed(0),
			"label_names__"			: [],
			"stage_names__"			: {},
			"functions_data__"		: config_functions_data__,
			"functions__"			: config_functions__,
			"config_object_properties__" 	: config_object_properties__,
			"plugin_data__"			: config_default_plugins__,
			"redis_functions__"		: {},
			"show_add_new_of__"		: false,
			"current_year__"		: "2020",
			"date_today__"			: "2020-01-01",
			"datetime__"			: "2020-01-01 01:01:01",
    		"checks__"			: [],
    		"checked_items__"		: 0,
    		"flags__"			: {},
			"test_url_main"			: "",
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
			},
			"data_types1__"		: {
				"V": "Variable",
				"T": "Text",
				"N": "Number",
				"B": "Boolean",
				"NL": "Null", 
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
			},
			"data_types2__"		: {
				"TI": "Thing Item",
				"TH": "Thing",
				"THL": "Thing List",
				"L": "List",
				"O": "Assoc List",
				"TT": "MultiLine Text",
				"HT": "HTML Text",
				"BIN": "Binary",
				"B64": "Base64",
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
			"stages_by_type__"		: [
				{
					"group": "Expression",
					"sub": [
						"Let",
						"Assign",
						"Math",
						"Expression",
						"Function"
					]
				},
				{
					"group": "Control",
					"sub": [
						"If",
						"While",
						"For",
						"ForEach",
						"BreakLoop",
						"NextLoop",
						"SetLabel",
						"JumpToLabel",
						"Sleep",
					]
				},
				{
					"group": "Output",
					"sub": [
						"RespondJSON", "RespondVar", "Log",
					]
				},
				{
					"group": "Components",
					"sub": [
						"HTTPRequest",
						"APICall",
						"FunctionCall",
						"Internal-Table",
						"Elastic-Table"
					]
				},
				{
					"group": "Database",
					"sub": [
						"MySql",
						"MongoDb",
						"DynamoDb",
						"Redis",
						"MSSql",
						"Cassandra",
						"SQLite",
						"FireBase",
						"FireStore",
						"BigQuery",
					]
				},
				{
					"group": "SDK",
					"sub": [
						"wkHtmlToPdf",
						"AWS",
						"GCP",
						"Azure",
						"AirTable",
						"Facebook",
						"Slack",
						"Whatsapp",
						"Telegram",
						"Jira",
						"GoogleMaps",
						"Celery",
						"AMPq",
						"RabbitMQ",
						"Swagger",
						"RazorPay",
						"Paytm",
						"Stripe",
						"PayPal",
						"CCAvenue",
						"PayU",
						"BillDesk",
						"Instamojo",
						"Mobikwik",
					]
				}
			],

			"stage_params__"	: config_stage_params__,
			"just_created_stage__": -1,

			context_menu__: false,
			context_for__: 'stages',
			context_var_for__: '',
			context_dependency__: "",
			context_callback__: "",
			context_el__: false,
			context_style__: "top:50px;left:50px;",
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
			popup_import__: false,
			popup_import_str__: `{}`,
			doc_popup__: false,
			doc_popup_doc__: "",
			doc_popup_text__: "Loading...",

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
			selected_plugin__: "",
			selected_plugin_fn__: "",
			selected_plugin_fn_param__: "",
			things_used__: {},
			dynamic_function__: function(){},
		};
	},
	mounted(){
		for(var f in this.config_object_properties__){
			if( 'inputs' in this.config_object_properties__[f] ){
				for( var p in this.config_object_properties__[f]['inputs'] ){
					this.config_object_properties__[f]['inputs'][p]['vs'] = {
						"v": ".",
						"t": "n",
						"d": {},
					};
				}
			}
		}
		this.load_initial_data__();
		//document.addEventListener("mousedown", this.event_mousedown );
		document.addEventListener("keyup", this.event_keyup );
		document.addEventListener("keydown", this.event_keydown);
		document.addEventListener("click", this.event_click__, true);
		document.addEventListener("scroll", this.event_scroll, true);
		document.addEventListener("blur", this.event_blur__, true);
		window.addEventListener("paste", this.event_paste__, true);
	},
	methods: {
		select_test_environment__: function(){
			setTimeout(this.select_test_environment__2,200);
		},
		select_test_environment__2: function(){
			for( var d in this.app__['settings']['domains'] ){
				if( this.app__['settings']['domains'][ d ]['domain'] == this.test__['domain'] ){
					this.test__['path'] = this.app__['settings']['domains'][ d ]['path'];
					var tu = this.app__['settings']['domains'][ d ]['url'] + "?function_version_id=<?=$config_param4 ?>&test_token=<?=md5($config_param4) ?>";
					if( this.test_debug__ ){
						tu  = tu + "&debug=true";
					}
					this.test_url__ = tu;
					break;
				}
			}
		},
		event_scroll: function(e){
			if( e.target.className == "codeeditor_block_a" ){
				if( this.context_menu__ ){
					this.set_context_menu_style__();
				}else if( this.simple_popup_modal__ ){
					this.set_simple_popup_style__();
				}
			}
		},
		event_keyup: function(e){
			if( e.target.hasAttribute("data-type") ){
				console.log("event_keyup: "+e.target.getAttribute("data-type"));
				if( e.target.getAttribute("data-type") == "editable" ){
					setTimeout(this.editable_check__, 100, e.target);
				}else if( e.target.getAttribute("data-type") == "popupeditable" ){
					setTimeout(this.editable_check__, 100, e.target);
				}else{
					console.log("Error: unknown data-type: " + e.target.getAttribute("data-type") );
				}
			}else{
				console.log("event_keyup: data-type not found");
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
			console.log( el.innerText );
			console.log( el.innerHTML );
			if( el.innerText != el.innerHTML ){
				el.innerText = el.innerText+'';
			}
		},
		event_blur__: function( e ){
			// console.log( "blur event:" );
			// console.log( e.target );
			if( e.target.hasAttribute("data-type") ){
				if( e.target.getAttribute("data-type") == "editable" ){
					e.stopPropagation();
					e.preventDefault();
					var s = this.find_parents(e.target);
					if( !s ){ return false; }
					var v = e.target.innerText;
					// console.log( " =====  " + v );
					v = v.replace(/[\u{0080}-\u{FFFF}]/gu, "");
					// v = v.replace(/\&nbsp\;/g, " ");
					// v = v.replace(/\&gt\;/g, ">");
					// v = v.replace(/\&lt\;/g, "<");
					var vv = this.v_filter__( v, e.target );
					// console.log( "==" + v + "== : ==" + vv + "==" );
					if( v == vv ){
						this.update_editable_value( s, v );
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
					var s = this.find_parents(e.target);
					if( !s ){ return false; }
					var v = e.target.innerText;
					v = this.v_filter__( v, e.target );
					if( v ){
						this.update_editable_value( s, v );
						setTimeout(this.editable_check__, 200, e.target );
						setTimeout(this.updated_option__, 200);
					}else{console.log("incorrect value formed!");}
				}
			}
		},
		editable_check__: function(el){
			var data_var = el.getAttribute("data-var");
			var s = this.find_parents(el);
			if( !s ){ return false; }
			var v = this.get_editable_value(s);
			if( v === false ){console.log("editable_check: value false");return false;}
			if( v != el.innerText ){
				if( el.nextSibling ){
				}else{
					el.insertAdjacentHTML("afterend", `<div class="inlinebtn" data-type="editablebtn" ><i class="fa fa-check-square-o" ></i></div>` );
				}
			}else{
				if( el.nextSibling ){
					el.nextSibling.outerHTML = '';
				}
			}
		},
		event_keydown: function(e){
			if( e.ctrlKey && e.keyCode == 86 ){
			//	e.preventDefault();e.stopPropagation();
			}
			if( e.keyCode == 27 ){
				if( this.context_menu__ ){
					this.context_menu__ = false;
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
						var v = e.target.innerText;
						v = this.v_filter__( v, e.target );
						if( v ){
							if( e.target.nextSibling ){
								e.target.nextSibling.outerHTML = "";
							}
							s = this.find_parents(e.target);
							if( !s ){ return false; }
							this.update_editable_value( s, v );
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
							this.show_doc_popup(doc);
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
			if( el_data_type ){
				var t = el_data_type.getAttribute("data-type");
				console.log( t );
				if( t == "type_pop" ){

				}else if( t == "objecteditable" ){
					this.popup_stage_id__ = stage_id;
					this.popup_datavar__ = data_var;
					this.popup_for__ = data_for;
					console.log("objecteditable" + data_var);
					var v = this.get_editable_value({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
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
					if( this.popup_modal__ == false ){
						this.popup_modal__ = new bootstrap.Modal( document.getElementById('popup_modal__') );
					}
					this.popup_modal__.show();
				}else if( t == "popupeditable" ){
					this.simple_popup_el__ = el_data_type;
					this.simple_popup_stage_id__ = stage_id;
					this.simple_popup_datavar__ = data_var;
					this.simple_popup_for__ = data_for;
					var v = this.get_editable_value({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
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
					console.log("payloadeditable" + data_var);
					var v = this.get_editable_value({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
					if( v === false ){console.log("event_click: value false");return false;}
					this.popup_data__ = v;
					this.popup_type__ = 'PayLoad';
					this.popup_title__ = "Request Payload Editor";
					if( this.popup_modal__ == false ){
						this.popup_modal__ = new bootstrap.Modal( document.getElementById('popup_modal__') );
					}
					this.popup_modal__.show();

				}else if( t == "dropdown" || t == "dropdown2" || t == "dropdown3" || t == "dropdown4" ){
					this.context_el__ = el_data_type;
					this.context_value__ = el_data_type.innerHTML;
					this.context_menu_key__ = "";
					this.context_for__ = data_for;
					this.context_datavar__ = data_var;
					var v = this.get_editable_value({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
					if( v === false ){console.log("event_click: value false");return false;}
					console.log("dropdown click: " + data_for + ": " + data_var );
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
						}else if( ld == 'output-type' ){
							if( this.function__['input-method'] == "GET" ){
								this.context_list__ = ["application/json", "application/xml", "text/html", "text/plain"];
							}else{
								this.context_list__ = ["application/json", "application/xml"];
							}
						}else{
							this.context_list__ = ld.split(",");
						}
					}
					console.log("ok");
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
					this.context_menu__ = false;
				}
				if( this.simple_popup_modal__ ){
					this.simple_popup_modal__ = false;
				}
			}
		},
		context_thing_list_load_check__: function(){
			if( this.context_thing__ in this.context_thing_list__ == false ){
				this.context_thing_list__[ this.context_thing__ ] = [];
			}
			if( this.context_thing_list__[ this.context_thing__ ].length == 0 ){
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
									this.context_thing_list__[ this.context_thing__ ] = response.data['things'];
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
						this.context_thing_err__ = "Response Error: " . response.status;
					}
				}).catch(error=>{
					this.context_thing_err__ = "Error Loading";
				});
			}
		},
		editablebtn_click__: function( el_data_type, data_var, data_for, stage_id, e ){
			console.log( "editablebtn" );
			console.log( el_data_type.previousSibling );
			var v = el_data_type.previousSibling.innerText;
			console.log( v );
			v = v.replace(/[\u{0080}-\u{FFFF}]/gu, "");
			// v = v.replace( /\&nbsp\;/g, " " );
			// v = v.replace( /\&gt\;/g,  ">" );
			// v = v.replace( /\&lt\;/g,  "<" );
			vv = this.v_filter__(v, el_data_type.previousSibling );
			if( vv == v ){
				this.update_editable_value({'data_var':data_var,'data_for':data_for,'stage_id':stage_id}, v);
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
		update_editable_value: function(s, v){
			// this.echo__("update_editable_value: " );
			// this.echo__(s);
			if( s['data_for'] == 'stages' ){
				var ov = this.get_sub_var__(this.engine__['stages'][ s['stage_id'] ], s['data_var'], v);
				if( ov != v ){
					this.set_sub_var__(this.engine__['stages'][ s['stage_id'] ], s['data_var'], v);
					this.check_sub_key(this.engine__['stages'][ s['stage_id'] ], s['data_var'], v);
					if( this.engine__['stages'][ s['stage_id'] ]['k']['v'] == "Let" && s['data_var'] == "d:lhs" ){
						this.update_variable_change_in_sub_stages__( s['stage_id'], ov+'', v+'' );
					}
				}
			}else if( s['data_for'] == 'api' ){
				var ov = this.get_sub_var__(this.api__, s['data_var'], v);
				if( ov != v ){
					this.set_sub_var__(this.api__, s['data_var'], v);
					this.check_sub_key(this.api__, s['data_var'], v);
				}
			}else if( s['data_for'] == 'engine' ){
				var ov = this.get_sub_var__(this.engine__, s['data_var'], v);
				if( ov != v ){
					this.set_sub_var__(this.engine__, s['data_var'], v);
					this.check_sub_key(this.engine__, s['data_var'], v);
				}
			}else if( s['data_for'] == 'test__' ){
				var ov = this.get_sub_var__(this.test__, s['data_var'], v);
				if( ov != v ){
					this.set_sub_var__(this.test__, s['data_var'], v);
					this.check_sub_key(this.test__, s['data_var'], v);
				}
			}else{
				console.error("update_editable_value: data_for unknown: " + s['data_for'] + ": " + s['data_var'] );
				return false;
			}
		},
		get_editable_value: function(s){
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
		check_sub_key: function(vv, data_var, v){
			x = data_var.split(/\:/g);
			var vkey = x.pop();
			if( vkey == 'k' ){
				var data_var = x.join(":");
				//this.echo__( data_var );
				var mdata = this.get_sub_var__( vv, data_var );
				//this.echo__(mdata);
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
		popup_data_save__: function(){
			this.popup_modal__.hide();
		},
		find_parents: function(el){
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
		show_and_focus_context_menu__: function(){
			setTimeout(function(){try{document.getElementById("contextmenu_key1").focus();}catch(e){}},300);
			this.context_menu__ = true;
			this.context_expand_key__ = '';
		},
		set_context_menu_style__: function(){
			var s = this.context_el__.getBoundingClientRect();
			//this.finx_zindex(this.context_el__);
			this.context_style__ = "top: "+s.top+"px;left: "+s.left+"px;";
		},
		set_simple_popup_style__: function(){
			var s = this.simple_popup_el__.getBoundingClientRect();
			this.simple_popup_style__ = "top: "+s.top+"px;left: "+s.left+"px;";
		},
		find_zindex: function(el){
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
			console.log( "context select: "+ this.context_for__  + ": " + this.context_datavar__ + ": " + k +  ": " + t );
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
			}else if( this.context_for__ == 'stages' ){
				if( this.context_datavar__ == "k" ){
					if( t == 'o' ){
						var d = this.get_o_sub_var( this.all_factors_stage_wise__[ this.context_stage_id__ ], k );
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

				}else{
					if( typeof(k) == "string" || typeof(k) == "number" ){
						this.set_stage_sub_var__( this.context_stage_id__, this.context_datavar__, k );
					}
					if( t == 'prop' ){
						var vt = this.get_stage_sub_var__( this.context_stage_id__, this.context_datavar_parent__+":t" );
						if( vt in this.config_object_properties__ ){
							if( k in this.config_object_properties__[ vt ] ){
								//this.echo__( this.get_stage_sub_var__( this.context_stage_id__, this.context_datavar_parent__ ) );
								this.set_stage_sub_var__( this.context_stage_id__, this.context_datavar_parent__+":vs:d", this.json__(this.config_object_properties__[ vt ][k]) );
							}
						}
					}
					if( t == 'plgprop' ){
						var vt = this.get_stage_sub_var__( this.context_stage_id__, this.context_datavar_parent__+":t" );
						//this.echo__( vt );
						if( vt in this.plugin_data__ ){
							if( 'p' in this.plugin_data__[ vt ] ){
								if( k in this.plugin_data__[ vt ]['p'] ){
									var d = this.json__( this.get_stage_sub_var__( this.context_stage_id__, this.context_datavar_parent__ ) );
									//d['vs'] = {"v":"", "t":"", "d":{}};
									this.set_stage_sub_var__( this.context_stage_id__, this.context_datavar_parent__+":vs:d", {} );
									d['vs']['d'] = this.json__(this.plugin_data__[ vt ]['p'][k]);
									setTimeout(this.set_stage_sub_var__,100,this.context_stage_id__, this.context_datavar_parent__, d );
									//this.nextTick();
								}else{
									this.echo__( k + " not found in plugin data " + vt );
								}
							}else{
								this.echo__("plugging data sub p not found");
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
						//this.echo__( k );
						//this.echo__( this.get_stage_sub_var__( this.context_stage_id__, this.context_datavar__ ) );
						this.set_stage_sub_var__( this.context_stage_id__, this.context_datavar__, k );
					}
					if( t == "datatype" ){
						this.update_variable_type__( this.engine__['stages'][ this.context_stage_id__ ], this.context_datavar__, k );
						if( this.engine__['stages'][ this.context_stage_id__ ]['k']['v'] == "Let" ){
							var a = this.engine__['stages'][ this.context_stage_id__ ]['d']['lhs'];
							var t = this.engine__['stages'][ this.context_stage_id__ ]['d']['rhs']['t'];
							if( t == "TT" ){ t = "T"; }
							setTimeout(this.update_variable_type_change_in_sub_stages__, 100, this.context_stage_id__, a, t);
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
						var d = this.get_o_sub_var( this.all_factors_stage_wise__[ this.context_stage_id__ ], k );
						// this.echo__("var select");
						// this.echo__( d );
						// this.echo__( this.context_datavar__ );
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
			}else{
				console.error("context_select error: data_for unknown: "+ this.context_for__ );
			}
			setTimeout(this.updated_option__,100);
			this.context_menu__ = false;
		},
		set_function_inputs__: function(v,p,r,s){
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
			}catch(e){console.log("get_sub_var__ error: " +  + vpath + ": " + e );return false;}
		},
		is_token_ok(t){
			if( t!= "OK" && t.match(/^[a-f0-9]{24}$/)==null ){
				setTimeout(this.token_validate,100,t);
				return false;
			}else{
				return true;
			}
		},
		open_edit_form: function(){
			this.edit_modal = new bootstrap.Modal(document.getElementById('edit_modal'));
			this.edit_modal.show();
			this.cmsg = ""; this.cerr = "";
			this.edit_function = this.json__(this.function__);
		},
		token_validate(t){
			if( t.match(/^(SessionChanged|NetworkChanged)$/) ){
				this.err = "Login Again";
				alert("Need to Login Again");
			}else{
				this.err = "Token Error: " + t;
			}
		},
		replace_variables_in_object: function( vd ){
			return vd;
		},
		load_functions(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?", {"action":"get_token","event":"getfunctions","expire":2}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.load_functions2();
								}
							}else{
								alert("Token error: " + response.data['data']);
								this.err = "Token Error: " + response.data['data'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response Type";
					}
				}else{
					this.err = "Response Error: " . response.status;
				}
			});
		},
		load_functions2(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?",{"action":"get_functions","token":this.token}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.functions = response.data['data'];
							}else{
								alert("Token error: " + response.data['error']);
								this.err = "Token Error: " + response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response Type";
					}
				}else{
					this.err = "Response Error: " . response.status;
				}
			});
		},
		cleanit(v){
			v = v.replace( /\-/g, "DASH" );
			v = v.replace( /\_/g, "UDASH" );
			v = v.replace( /\W/g, "-" );
			v = v.replace( /DASH/g, "-" );v = v.replace( /UDASH/g, "_" );
			v = v.replace( /[\-]{2,5}/g, "-" );
			v = v.replace( /[\_]{2,5}/g, "_" );
			return v;
		},
		editnow(){
			this.cerr = "";
			this.edit_function['name'] = this.cleanit(this.edit_function['name']);
			if( this.edit_function['name'].trim() == "" ){
				this.cerr = "Name incorrect";
				return false;
			}
			if( this.edit_function['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\t\ \r\n]{5,200}$/i) == null ){
				this.cerr = "Description incorrect. Special chars not allowed";
				return false;
			}
			this.cmsg = "Editing...";
			axios.post("?", {
				"action":"get_token",
				"event":"edit_function"+this.edit_function['_id'],
				"expire":2
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									axios.post("?", {
										"action": "edit_function", 
										"edit_function": this.edit_function,
										"token": this.token
									}).then(response=>{
										this.cmsg = "";
										if( response.status == 200 ){
											if( typeof(response.data) == "object" ){
												if( 'status' in response.data ){
													if( response.data['status'] == "success" ){
														this.cmsg = "Created";
														this.edit_modal.hide();
														this.function__ = JSON.parse( JSON.stringify(this.edit_function));
													}else{
														this.cerr = response.data['error'];
													}
												}else{
													this.cerr = "Incorrect response";
												}
											}else{
												this.cerr = "Incorrect response Type";
											}
										}else{
											this.cerr = "Response Error: " . response.status;
										}
									});
								}
							}else{
								alert("Token error: " + response.dat['data']);
								this.err = "Token Error: " + response.data['data'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response Type";
					}
				}else{
					this.err = "Response Error: " . response.status;
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
					}
					this.load_initial_data2__();
				}else{
					alert("Server Error.Please Try After Sometime");
				}
			});
		},
		load_initial_data2__: function(){
			///console.log("load initial data2 ");
			if( this.engine__.hasOwnProperty("input_factors") == false ){
				this.engine__['input_factors'] ={};
				this.save_need__=true;
			}else if( this.engine__["input_factors"].hasOwnProperty("length") ){
				this.engine__['input_factors'] = {};
				this.save_need__=true;
			}
			//console.log( this.engine__ );
			if( 'output' in this.engine__ == false ){
				this.engine__['output'] = {"t":"O", "_":{}};
			}
			if( 'stages' in this.engine__ == false ){
				this.engine__['stages'] = [
					    {
					      "k": {"v": "Let", "t": "c", "vs": false}, "pk": "Let",
					      "t": "c",
					      "d": {"lhs": "a","rhs": {"t": "N","v": 10}},
					      "l": 1,"e": false,"ee": true,"er": "","wr": ""
					    },
					    {
					      "k": {"v": "Let", "t": "c", "vs": false}, "pk": "Let",
					      "t": "c",
					      "d": {"lhs": "b","rhs": {"t": "N","v": 10}},
					      "l": 1,"e": false,"ee": true,"er": "","wr": ""
					    },
					    {
					      "k": {"v": "Let", "t": "c", "vs": false}, "pk": "Let",
					      "t": "c",
					      "d": {"lhs": "c","rhs": {"t": "N","v": 0}},
					      "l": 1,"e": false,"ee": true,"er": "","wr": ""
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
					      "l": 1,"e": false,"ee": true,"er": "","wr": ""
					    },
					    {
					      "k": {"v": "Respond","t": "c","vs": false},"pk": "Respond",
					      "t": "c",
					      "d": {"t": "O","v": {"status": {"t": "T","v": "success","k":"status"},"data": {"t": "V","v": {"v": "c","t": "N","vs": {"v": "","t": "","d": []}},"k":"data"}}},
					      "l": 1,"e": false,"ee": true,"er": "","wr": ""
					    }
				];
				this.save_need__=true;
			}
			var dt = new Date();
			this.current_year__ = dt.getFullYear();
			this.date_today__ = dt.toJSON().substr(0,10);
			this.datetime__ = dt.toJSON().substr(0,19).replace("T", " " );
			this.find_checks__();
			this.fill_variables__();
			this.vshow	= true;
			this.select_test_environment__();
		},
		input_type_change__: function(){
			this.save_need__ = true;
		},
		page_method_change__: function(){
			this.save_need__ = true;
		},
		input_factors_edited__: function( vdata ){
			this.engine__[  'input_factors' ] =  vdata ;
			this.save_need__ = true;
			this.updated_option__();
		},
		test_factors_edited__: function( vdata ){
			this.test__['factors']['v'] =  vdata;
			this.select_test_environment__();
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
		merge_variables__: function( vo, vd ){
			for( var key in vd ){
				vo[ key+'' ] = this.json__(vd[key]);
			}
			//return vo;
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
			//this.echo__( "get object notation" );
			//this.echo__( v );
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
		get_dbcond_object_notation__(v){
			return v;
			var vv = {};
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
			return Object.fromEntries(Object.entries(vv).sort());
			return vv;
		},
		update_variable_change_in_sub_stages__: function( sid, vold, vnew ){
			for(var stagei__=sid;stagei__<this.engine__['stages'].length;stagei__++ ){
				var staged__ = this.engine__['stages'][stagei__];
				if( staged__['k']['t'] == 'o' || staged__['k']['t'] == 'PLG' ){
					if( staged__['k']['v'] == vold ){
						staged__['k']['v'] = vnew;
					}
				}
				this.update_variable_change_in_sub_stage_params__( staged__, vold, vnew );
			}
		},
		update_variable_change_in_sub_stage_params__: function( vv, vold, vnew ){
			try{
				if( "t" in vv && "v" in vv ){
					if( vv['t'] == "V" || vv['t'] == "PLG" ){
						if( typeof( vv['v'] ) == "object" && vv['v'] != null ){
							if( vv['v']['v'] == vold ){
								vv['v']['v'] = vnew;
							}
						}
					}
				}
				for( var k in vv ){
					if( typeof(vv[k]) == "object" && vv[k] != null ){
						this.update_variable_change_in_sub_stage_params__( vv[ k ], vold, vnew );
					}
				}
			}catch(e){console.error(e);console.log("set_sub_var__ error: " + vpath );return false;}
		},
		update_variable_type_change_in_sub_stages__: function( sid, a, t ){
			for(var stagei__=sid;stagei__<this.engine__['stages'].length;stagei__++ ){
				var staged__ = this.engine__['stages'][stagei__];
				this.update_variable_type_change_in_sub_stage_params__( staged__, a, t );
			}
		},
		update_variable_type_change_in_sub_stage_params__: function( vv, a, t ){
			try{
				if( "t" in vv && "v" in vv ){
					if( vv['t'] == "V" || vv['t'] == "PLG" ){
						if( typeof( vv['v'] ) == "object" && vv['v'] != null ){
							if( vv['v']['a'] == a ){
								if( vv['v']['t'] != t ){
									vv['v']['t'] = t+'';
								}
							}
						}
					}
				}
				for( var k in vv ){
					if( typeof(vv[k]) == "object" && vv[k] != null ){
						this.update_variable_type_change_in_sub_stage_params__( vv[ k ], a, t );
					}
				}
			}catch(e){console.error(e);console.log("set_sub_var__ error: " + e ); console.log( vold ); console.log( vnew ); return false;}
		},
		fill_variables__: function(){
			var used_outputs = {};
			var o = {};
			vin = this.convert_array_structure_to_stage_vars__( this.engine__['input_factors'] );
			this.merge_variables__( o, vin );
			for(var stagei__=0;stagei__<this.engine__['stages'].length;stagei__++ ){
				var staged__ = this.engine__['stages'][stagei__];
				this.stagei__ = stagei__;
				//console.log("fill_variables__ stage:" + stagei__ + ": " + staged__['k']['v'] + ": " + staged__['k']['t'] );
				this.all_factors_stage_wise__[  Number(stagei__) ] =  this.json__(o);
				var er = "";var wr = "";

				var sub_wr = this.find_sub_type_differences__( stagei__, o, staged__ );
				if( sub_wr ){
					wr = wr + sub_wr;
				}

				if( staged__['t'] != 'c' && staged__['t'] != 'n' ){
					if( this.find_o_sub_var(o, staged__['k']['v']) == false ){
						er = er + " Variable `" + staged__['k']['v'] +"` not available;";
					}
					if( 'vs' in staged__['k'] ){if( typeof(staged__['k']['vs'])=="object" ){ if( 'd' in staged__['k']['vs'] ){
						if( staged__['k']['vs']['d']['self'] == false ){
							er = er + " result assignment missing; ";
						}
					}}}
					er = er + this.find_variable_usage__( o, staged__['k']['vs'] );
					wr = wr + this.find_variable_empty__( staged__['k']['vs'] );
				}else{
					er = er + this.find_variable_usage__( o, staged__['d'] );
					wr = wr + this.find_variable_empty__( staged__['d'] );
				}

				if( staged__['k']['v'] == "LetComponent" ){
					//o[ staged__['d']['lhs']+'' ] = this.get_variable_final_form_as_input( this.json__( d ) );
					//o[ staged__['d']['lhs']+'' ] = this.get_variable_final_form_as_input( this.json__( staged__['d']['rhs'] ) );
					if( staged__['d']['lhs'] == "" ){
						er = er + " lhs variable empty";
					}else if( staged__['d']['lhs'] in o ){
						wr = wr + " Warning variable `" + staged__['d']['lhs'] + "` override;";
					}
					var comp = staged__['d']['rhs']['v']['i']['v']+'';
					// this.echo__("LetComponent: " );
					// this.echo__( this.json__( this.plugin_data__[ comp ]['data'] ) );
					if( comp in this.plugin_data__ ){
						o[ staged__['d']['lhs']+'' ] = {
							"t": comp, 
							"_": {}
						};
						//this.convert_array_structure_to_stage_vars__( this.json__( this.plugin_data__[ comp ]['data'] ) )
						// this.echo__("LetComponent: " );
						// this.echo__( o[ staged__['d']['lhs']+'' ] );
					}
					//o[ staged__['d']['lhs']+'' ]["PLG"] = 
				}else if( staged__['k']['v'] == "Let" ){
					if( staged__['d']['lhs'] == "" ){
						er = er + " lhs variable empty";
					}else if( staged__['d']['lhs'] in o ){
						wr = wr + " Warning variable `" + staged__['d']['lhs'] + "` override;";
					}
					if( staged__['d']['rhs']['t'] == "V" ){
						if( staged__['d']['rhs']['v']['v'] != "" ){
							var d = this.get_o_sub_var( o, staged__['d']['rhs']['v']['v'] );
							//this.echo__( d );
							//this.echo__( staged__['d']['rhs'] );
							if( d ){
								if( 'vs' in staged__['d']['rhs']['v'] ){
									if( staged__['d']['rhs']['v']['vs'] != false ){
										if( 'd' in staged__['d']['rhs']['v']['vs'] ){
											if( typeof( staged__['d']['rhs']['v']['vs']['d'] ) == "object" ){
												if( staged__['d']['rhs']['v']['t'] == "L" && staged__['d']['rhs']['v']['vs']['v'] == 'getItem' ){
													d = {'t':'O','_':this.json__( d )['_'] };
												}else if( 'return' in staged__['d']['rhs']['v']['vs']['d'] ){
													d = {"t": staged__['d']['rhs']['v']['vs']['d']['return'], "_":{}};
													//if( staged__['d']['rhs']['v']['vs']['d'][''] )
													if( 'structure' in staged__['d']['rhs']['v']['vs']['d'] ){
														var d = this.json__(staged__['d']['rhs']['v']['vs']['d']['structure']);
													}
												}
											}else{
												this.echo__("Stage Let Variable: vs d not found!");
											}
										}
									}
								}
								//this.echo__( d );
								//this.echo__( this.get_variable_final_form_as_input( this.json__( d ) ) );
								o[ staged__['d']['lhs']+'' ] = this.get_variable_final_form_as_input( this.json__( d ) );
							}else{
								console.log("Let Stage variable: " + staged__['d']['rhs']['v']['v'] + ": not found in all factors" );
							}
						}else{
							console.log("Let variable empty!");
						}
						//o[ staged__['d']['n']+'' ] = 'T';
					}else if( staged__['d']['rhs']['t'] == "TH" ){
						if( staged__['d']['rhs']['v']['l']['v'] != "" ){
							var p = staged__['d']['rhs']['v']+'';
							if( p in this.plugin_data__ ){
								o[ staged__['d']['lhs']+'' ] = {"t":"PLG","plg":p};
							}else{
								console.log("Let Stage variable: " + staged__['d']['rhs']['v']['v'] + ": not found in plugins" );
							}
						}else{
							console.log("Let variable empty!");
						}
					}else if( staged__['d']['rhs']['t'] == "THL" ){
						if( staged__['d']['rhs']['v']['th'] != "" ){
							var p = staged__['d']['rhs']['v']['th']+'';
							o[ staged__['d']['lhs']+'' ] = {"t":"THL","th":p};
							this.things_used__[ p ] = [];
						}else{
							console.log("Let variable empty!");
						}
					}else if( staged__['d']['rhs']['t'] == "TH" ){
						if( staged__['d']['rhs']['v']['th'] != "" ){
							var p = staged__['d']['rhs']['v']['th']+'';
							o[ staged__['d']['lhs']+'' ] = {"t":"TH","th":p};
							this.things_used__[ p ] = [];
						}else{
							console.log("Let variable empty!");
						}
						//o[ staged__['d']['n']+'' ] = 'T';
					}else{
						o[ staged__['d']['lhs']+'' ] = this.get_variable_final_form_as_input( this.json__( staged__['d']['rhs'] ) );
					}
				}
				if( staged__['k']['v'] == "Assign" ){
					if( staged__['d']['lhs']['t'] != "V" ){
						er = er + " Incorrect lhs type";
					}
					var t1 = staged__['d']['lhs']['v']['t'];
					var t2 = staged__['d']['rhs']['t'];
					if( t2 == "O" ){
						//this.echo__("Assign Object" );
						this.set_o_sub_var(o, staged__['d']['lhs']['v']['v'], this.get_variable_final_form_as_input( this.json__( staged__['d']['rhs'] ) ) );
					}
					if( t2 == "V" ){
						var t2 = staged__['d']['rhs']['v']['t'];
						if( t2 in this.config_object_properties__ ){
							var d = this.get_o_sub_var( o, staged__['d']['rhs']['v']['v'] );
							if( 'vs' in staged__['d']['rhs']['v'] ){
								if( staged__['d']['rhs']['v']['vs'] != false ){
									var fn = staged__['d']['rhs']['v']['vs']['v'];
									if( fn != '' ){
										if( fn in this.config_object_properties__[ t2 ] == false ){
											er = er + " function:`" + fn + "` not found in " + t2;
										}else{
											t2 = this.config_object_properties__[ t2 ][ fn ]['return'];
											if( t1 != t2 ){
												wr = wr + " Warning: data type mismatch: " + t1 + " = " + t2;
											}
										}
									}
									if( 'd' in staged__['d']['rhs']['v']['vs'] ){
										if( typeof( staged__['d']['rhs']['v']['vs']['d'] ) == "object" ){
											if( staged__['d']['rhs']['v']['t'] == "L" && staged__['d']['rhs']['v']['vs']['v'] == 'getItem' ){
												d = {'t':'O','_':this.json__( d )['_'] };
											}else if( 'return' in staged__['d']['rhs']['v']['vs']['d'] ){
												d = {"t": staged__['d']['rhs']['v']['vs']['d']['return'], "_":{}};
												//if( staged__['d']['rhs']['v']['vs']['d'][''] )
												if( 'structure' in staged__['d']['rhs']['v']['vs']['d'] ){
													var d = this.json__(staged__['d']['rhs']['v']['vs']['d']['structure']);
												}
											}
											if( d ){
												o[ staged__['d']['lhs']['v']['v'] ] = this.get_variable_final_form_as_input( d );
											}
										}else{
											this.echo__("Stage Let Variable: vs d not found!");
										}
									}
								}
							}
						}else{
							console.log( t2 + ": not found in object props" );
						}
					}
				}
				if( staged__['k']['v'] == "FunctionCall" ){
					if( 'return' in staged__['d']['fn']['v'] ){
						o[ staged__['d']['lhs']['v']['v']+'' ] = staged__['d']['fn']['v']['return'];
					}
				}
				if( staged__['k']['v'] == "For" ){
					if( staged__['d']['as'] in o ){
						wr = wr + " Warning variable `" + staged__['d']['as'] + "` override;";
					}
					o[ staged__['d']['as']+'' ] = {"t":"N", "v":""};
				}
				if( staged__['k']['v'] == "EndFor" ){
					delete o[ staged__['d']['as']+'' ];
				}
				if( staged__['k']['v'] == "ForEach" ){
					if( staged__['d']['key'] in o ){
						wr = wr + " Warning variable `" + staged__['d']['key'] + "` override;";
					}
					if( staged__['d']['value'] in o ){
						wr = wr + " Warning variable `" + staged__['d']['value'] + "` override;";
					}
					if( staged__['d']['var']['v']['v'] in o ){
						if( o[ staged__['d']['var']['v']['v'] ]['t'] != "O" && o[ staged__['d']['var']['v']['v'] ]['t'] != "L" ){
							wr = wr + " Warning variable `" + staged__['d']['var']['v']['v'] + "` is not a List or Assoc List";
						}
					}else{
						this.echo__(" 111 " )
					}
					var key = {"t":"T"};
					var val = {"t":"T"};
					var arr = staged__['d']['var']['v']['v'];
					var d = this.get_o_sub_var(o, staged__['d']['var']['v']['v']);
					// this.echo__( "value" );
					// this.echo__( d );
					// this.echo__( "value2" );
					if( d ){
						var t = d['t'];
						if( t == "O" ){
							if( '_' in d ){
								key = {"t":"T"};
								val = d['_'][ Object.keys(d['_'])[0] ];
							}else{
								er = er + " incorrect source var `"+staged__['d']['var']['v']['v']+"` structure";
							}
						}else if( t == "L" ){
							if( '_' in d ){
								key = {"t":"N"};
								if( typeof(d['_']) == 'object' && "length" in d['_']){
									val = d['_'][0]['_'];
								}else{
									val = d['_'];
								}
							}else{
								er = er + " incorrect source var `"+staged__['d']['var']['v']['v']+"` structure";
							}
						}else{
							er = er + " incorrect source var `"+staged__['d']['var']['v']['v']+"` structure";
						}
					}

					//this.echo__( "-----" );
					//this.echo__( val );

					o[ staged__['d']['key']+'' ] = key;
					o[ staged__['d']['value']+'' ] = {'t':'O', '_':val};
				}
				if( staged__['k']['v'] == "EndForEach" ){
					var i = this.find_prev_rand__( stagei__ );
					var d = this.engine__['stages'][i]['d'];
					delete o[ d['key']+'' ];
					delete o[ d['value']+'' ];
				}
				if( staged__['k']['v'] == "MongoDb" ){
					if( 'data' in staged__['d']){
						if( 'output' in staged__['d']['data'] ){
							var oo = staged__['d']['data']['output']['v']+'';
							var act = staged__['d']['data']['action']['v']+'';
							if( act == "FindOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"O", "_":staged__['d']['data']['projects'] },
									"error": {"t":"T"}
								}}
							}
							if( act == "FindMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"L", "_":staged__['d']['data']['projects']},
									"error": {"t":"T"}
								}}
							}
							if( act == "InsertOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"insertId": {"t":"T"},
									"error": {"t":"T"}
								}}
							}
							if( act == "UpdateOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"modified_count":  {"t":"N"},
									"upserted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "UpdateMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"modified_count":  {"t":"N"},
									"upserted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "DeleteOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"deleted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "DeleteMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"deleted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
						}
					}
				}
				if( staged__['k']['v'] == "MySql" ){
					if( 'data' in staged__['d']){
						if( 'output' in staged__['d']['data'] ){
							var oo = staged__['d']['data']['output']['v']+'';
							var act = staged__['d']['data']['query']['v']+'';
							console.log( "mysql" );
							console.log( oo );
							console.log( act );
							if( act == "Select" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"L", "_":staged__['d']['data']['fields']['v']},
									"error": {"t":"T"},
									"count": {"t":"N"}
								}}
							}
							if( act == "SelectAssoc" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"O", "_":{}},
									"error": {"t":"T"},
									"count": {"t":"N"}
								}}
							}
							if( act == "SelectKeyValue" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"O", "_":{}},
									"error": {"t":"T"},
									"count": {"t":"N"}
								}}
							}
							if( act == "Update" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"updated":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "Delete" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"deleted":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "Insert" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"insertId":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
						}
					}
				}
				if( staged__['k']['v'] == "Internal-Table" ){
					if( 'data' in staged__['d'] ){
						if( 'output' in staged__['d']['data'] ){
							var oo = staged__['d']['data']['output']['v']+'';
							var act = staged__['d']['data']['action']['v']+'';
							if( act == "FindOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"O", "_":staged__['d']['data']['projects'] },
									"error": {"t":"T"}
								}}
							}
							if( act == "FindMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"L", "_":staged__['d']['data']['projects']},
									"error": {"t":"T"}
								}}
							}
							if( act == "InsertOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"insertId": {"t":"T"},
									"error": {"t":"T"}
								}}
							}
							if( act == "UpdateOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"modified_count":  {"t":"N"},
									"upserted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "UpdateMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"modified_count":  {"t":"N"},
									"upserted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "DeleteOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"deleted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "DeleteMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"deleted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
						}
					}
				}
				if( staged__['k']['v'] == "HTTPRequest" ){
					var oo = staged__['d']['data']['output']['v']+'';
					o[ oo ] = {"t": "O", "_":staged__['d']['data']['struct']};
				}
				if( staged__['k']['v'] == "RespondJSON" ){
					this.engine__['output'] = this.get_variable_final_form_as_input( this.json__( staged__['d']['output'] ) );
				}
				if( staged__['k']['v'] == "RespondVar" ){
					var d = this.get_o_sub_var( o, staged__['d']['output']['v']['v'] );
					//this.echo__( d );
					this.engine__['output'] = this.get_variable_final_form_as_input( this.json__( d ) );
				}

				this.engine__['stages'][ stagei__ ]['er'] = er;
				this.engine__['stages'][ stagei__ ]['wr'] = wr;

				if( staged__['k']['v'] != "none" ){
					if( staged__['k']['v'] in this.stage_params__ ){
						if( '_o_v' in staged__ ){
							for( var oi in staged__['_o_v'] ){
								o[ oi+'' ] = staged__['_o_v'][ oi ];
							}
						}
					}
				}
			}

			var ol= [];
			for( var i=0; i<this.engine__['stages'].length; i++ ){
				if( this.engine__['stages'][i]['k']['v'] == "SetLabel" ){
					ol.push( this.engine__['stages'][i]['d']['v']+"" );
				}
			}
			this.label_names__ = ol;
		},
		find_variable_usage__: function( vars, vd ){
			// (vd);
			var er = "";
			try{
			if( vd == null ){}else
			if( typeof(vd) == "object" && "length" in vd == false ){
				if( 't' in vd && 'v' in vd ){
					if( vd['t'] == "V" ){
						if( vd['v']['v'] == "" ){
							//er = er + " Variable selection missing;";
						}else if( this.find_o_sub_var(vars, vd['v']['v']) == false ){
							er = er + " Variable `" + vd['v']['v'] + "` is not available;";
						}
					}else{
						if( vd['v'] == null ){}else
						if( typeof(vd['v']) =='object' ){
							er = er + this.find_variable_usage__(vars, vd['v']);
						}
					}
				}else for(var k in vd){
					if( vd[k] == null ){}else
					if( typeof(vd[k]) =='object' ){
						er = er + this.find_variable_usage__(vars, vd[k]);
					}
				}
			}else if( typeof(vd) == "object" && "length" in vd ){
				for(var i=0;i<vd.length;i++){
					if( vd[i] == null ){}else 
					if( typeof(vd[i]) == "object" && vd[i] != null ){
						if( 't' in vd[i] && 'v' in vd[i] ){
							if( vd[i]['t'] == "V" ){
								if( vd[i]['v']['v'] == "" ){
									//er = er + " Variable selection missing;";
								}else if( this.find_o_sub_var(vars, vd[i]['v']['v']) == false ){
									er = er + " Variable `" + vd[i]['v']['v'] + "` is not available;";
								}
							}else{
								if( vd[i]['v'] == null ){}else 
								if( typeof(vd[i]['v']) =='object' ){
									er = er + this.find_variable_usage__(vars, vd[i]['v']);
								}
							}
						}else if( typeof(vd[i]) == "object" && vd[i] != null ){
						 	for(var k in vd[i]){
						 		if( vd[i][k] == null ){}else
						 		if( typeof(vd[i][k]) =='object' ){
									er = er + this.find_variable_usage__(vars, vd[i][k]);
								}
							}
						}
					}else{
						er = er + this.find_variable_usage__(vd[i]);
					}
				}
			}
			}catch(e){
				console.error("find_variable_usage__: " + e);
				this.echo__( vd );
			}
			return er;
		},
		find_variable_empty__: function( vd ){
			var wr = "";
			try{
			if( typeof(vd) == "object" && "length" in vd == false ){
				if( 't' in vd && 'v' in vd ){
					if( vd['t'] == "V" ){
						if( vd['v']['v'] == "" ){
							wr = wr + " Variable selection missing;";
						}
					}else{
						if( vd['v'] == null ){}else
						if( typeof(vd['v']) =='object' ){
							wr = wr + this.find_variable_empty__(vd['v']);
						}
					}
				}else for(var k in vd){
					if( vd[k] == null ){}else
					if( typeof(vd[k]) =='object' ){
						wr = wr + this.find_variable_empty__(vd[k]);
					}
				}
			}else if( typeof(vd) == "object" && "length" in vd ){
				for(var i=0;i<vd.length;i++){
					if( vd[i] == null ){}else 
					if( typeof(vd[i]) == "object" && vd[i] != null ){
						if( 'length' in vd[i] == false ){
							if( 't' in vd[i] && 'v' in vd[i] ){
								if( vd[i]['t'] == "V" ){
									if( vd[i]['v']['v'] == "" ){
										wr = wr + " Variable selection missing;";
									}
								}else{
									if( vd[i]['v'] == null ){}else 
									if( typeof(vd[i]['v']) =='object' ){
										wr = wr + this.find_variable_empty__(vd[i]['v']);
									}
								}
							}else if( typeof(vd[i]) == "object" && vd[i] != null ){
							 	for(var k in vd[i]){
							 		if( vd[i][k] == null ){}else
							 		if( typeof(vd[i][k]) =='object' ){
										wr = wr + this.find_variable_empty__(vd[i][k]);
									}
								}
							}
						}else{
							wr = wr + this.find_variable_empty__(vd[i]);
						}
					}
				}
			}
			}catch(e){
				console.error("find_variable_empty__: " + e);
				this.echo__( vd );
			}
			return wr;
		},
		find_sub_type_differences__: function( stagei__, vars, staged__ ){
			//this.echo__( "stage_wise_update_variable_Types__" );
			//this.echo__( staged__ );
			var wr = "";
			for(var k in staged__ ){
				//this.echo__( "top: " + k );
				if( typeof(staged__[k]) == "object" && staged__[k] != null ){
					if( 't' in staged__[ k ] && 'v' in staged__[ k ] ){
						if( staged__[ k ]['t'] == "V" ){
							var av = staged__[ k ]['v']['v'];
							var at = staged__[ k ]['v']['t'];
							//console.log( "finding: " + av );
							var sv = this.get_o_sub_var(vars, av );
							if( sv ){
								if( at != sv['t'] ){
									console.log( stagei__ + " = " + at + ":" + av + " = " + sv['t'] + ":" + sv['v'] );
									wr = wr + " var type for " + av + "=" + at + " but actual is " + sv['t'] + "; ";
								}
							}
						}
					}
					//this.echo__( staged__[k] );
					for( var j in staged__[k] ){
						//this.echo__( j );
						//this.echo__( staged__[k][j] );
						if( staged__[k][j] != undefined ){
							if( typeof(staged__[k][j]) == "object" && staged__[k][j] != null ){
								wr = wr + this.find_sub_type_differences__( stagei__, vars, staged__[k][j] );
							}
						}
					}
					//this.echo__( "over==" );
				}
			}
			return wr;
		},
		find_o_sub_var: function( vv, vpath ){
			try{
				//console.log( "find_o_sub_var: "+ vpath );
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
							return this.find_o_sub_var( vv[ k ]['_'], x.join("->") );
						}else if( vv[ k ]['t'] == "L" ){
							return this.get_o_sub_var( vv[ k ]['_'], x.join("->") );
						}else{
							return false;
						}
					}else{
						return true;
					}
				}else{
					return false;
				}
			}catch(e){console.log("find_o_sub_var error");return false;}
		},
		get_o_sub_var: function( vv, vpath ){
			//this.echo__("get_o_sub_var: " );this.echo__( vv ); this.echo__( vpath );
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
							return this.get_o_sub_var( vv[ k ]['_'], x.join("->") );
						}else if( vv[ k ]['t'] == "L" ){
							return this.get_o_sub_var( vv[ k ]['_'], x.join("->") );
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
				//console.log("get_o_sub_var error");
				this.echo__("get_o_sub_var:" + vpath);this.echo__(vv);
				return false;
			}
		},
		set_o_sub_var: function( vv, vpath, value ){
			this.echo__("set_o_sub_var: " );this.echo__( vv ); this.echo__( vpath );
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
							this.set_o_sub_var( vv[ k ]['_'], x.join("->"), value );
						}else if( vv[ k ]['t'] == "L" ){
							this.get_o_sub_var( vv[ k ]['_'], x.join("->"), value );
						}else{
							console.log("set_o_sub_var: false");
						}
					}else{
						vv[ k ]['_'] = value['_'];
					}
				}else{
					vv[ k ]['_'] = value['_'];
				}
			}catch(e){
				//console.log("get_o_sub_var error");
				this.echo__("get_o_sub_var:" + vpath);this.echo__(vv);
				return false;
			}
		},
		show_stage_alert__(i){
			alert(this.engine__['stages'][ i ]['er'] );
		},
		get_variable_final_form_as_input: function( v ){
			//console.log("Get variable finaal form:" );this.echo__( v );
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
						var sb = {'t': v['v'][0]['t']}
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
			}else{
				if( '_' in v ){
					vv['_'] = this.json__(v['_']);
				}else if( typeof(v['v'])=="object" && v['v'] != null ){
					if( Object.keys(v['v']).length > 0 ){
						vv['_'] = this.convert_array_structure_to_stage_vars__( this.json__(v['v']) );
					}else{
						vv['_'] = {};
					}
				}else{
					this.echo__("variable type "+v['t'] + " missing sub structure");
				}
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
						var d = this.get_o_sub_var( this.all_factors_stage_wise__[ this.stagei__ ], v[k]['v']['v'] );
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
		sessexpired__: function(v){
			$('#ses_expired').modal('show');
			setTimeout(function() {
			$('#ses_expired').modal('hide');
				document.location.reload();
			}, 3000);
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
		save_common__: function( stagei__, vdata ){
			//console.log( stagei__);
			//console.log( vdata);
			for( var vdi in vdata ){
				this.engine__['stages'][ stagei__ ][  vdi+'' ] =  this.json__(vdata[ vdi ]) ;
			}
			this.updated_option__();
		},
		save_test__: function(){
			this.show_saving__ = true;
			var vd__ =  {
				"action"		: "save_engine_test",
				"test"			: this.test__,
			};
			axios.post("?", vd__).then(response=>{
				if( response.data["status"] == "success" ){
					this.show_saving__ = false;
				}else if( response.data["details"] == "SessionExpired" ){
					this.save_message__ = "Session Expired.. Redirecting to Home page";
					this.sessexpired__();
				}else{
					alert( "Error in response:\n" + response.data['status'] );
				}
			});
		},
		save_data__: function(){
			this.save_need__ = false;
			this.show_saving__ = true;
			var vd__ = {
				"action"		: "save_engine_data",
				"data"			: this.engine__,
				"version_id"		: "<?=$config_param4 ?>",
				"function_id"		: "<?=$config_param3 ?>",
			};
			axios.post( "?", vd__).then(response=>{
				this.save_need__ = false;
				if( response.data["status"] == "success" ){
					this.show_saving__ = false;
					this.save_need__ = false;
				}else if( response.data["error"] == "Token Expired" ){
					this.save_message__ = "Token expired!. please reload page and try again!";
				}else if( response.data["error"] == "Incorrect Token" ){
					this.save_message__ = "Token expired/Incorrect!. please reload page and try again!";
				}else if( response.data["error"] == "SessionExpired" ){
					this.save_message__ = "Session Expired.. Redirecting to Home page";
					this.sessexpired__();
				}else{
					alert( "Error in response:\n" + response.data['status'] );
				}
			});
		},
		get_object_array__: function( v ){
			var val = {};
			for( var i in v ){
				if( v[i]['t'] == "L" ){
					val[ i ] = [];
					for(var k=0;k<v[i]['v'].length;k++){
						val[i].push( this.get_object_array__( v[i]['v'][k] ) );
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
			return val;
		},
		response_clean_before_display__: function( v ){
			if( v == null ){
				return "NULL";
			}else if( typeof(v) == "object" ){
				return v;
			}else if( typeof(v) == "string" ){
				if( v.match(/^\{[\S\s]+\}$/) ){
					try{
						s = JSON.parse(v);
						return JSON.stringify(s);
					}catch(e){
						this.test_error__ = "json parse failed";
						if( v.length > 1024 ){
							return v + " ...striped";
						}else{
							return v;
						}
					}
				}else{
					if( v.length > 1024 ){
						return v + " ...striped";
					}else{
						return v;
					}
				}
			}else{
				return v;
			}
		},
		split_headers__: function(v){
			var vv = {};
			for(i=0;i<v.length;i++){
				vv[ v[i][0] ] = v[i][1];
			}
			return vv;
		},
		test_simulation__: function(){
			this.test_status__="Testing...";
			this.test_error__="";
			this.test_response__ = false;
			this.test_headers_show__ = false;
			this.test_waiting__=true;
			var vpostdata = "";
			var vops = {'headers':{}, 'crossDomain': true };
				if( this.function__['input-type'] == "application/x-www-form-urlencoded" ){
					vops['headers']['content-type'] = "application/x-www-form-urlencoded";
					var vpostdata = this.make_query_string__( this.test__['factors']['v'] );
					if( this.test_debug__ ){
						vpostdata = vpostdata + "&debug=true";
					}
				}else{
					vops['headers']['content-type'] = "application/json";
					var vpostdata = this.get_object_array__(this.test__['factors']['v']);
					if( this.test_debug__ ){
						vpostdata[ "debug" ] = true;
					}
				}
				axios.post(this.test_url__, vpostdata, vops).then(response=>{
					this.test_waiting__=false;
					console.log( "Success" );
					var h = {};
					for( var d  in response.headers ){
						h[ d ] = response.headers[ d ];
					}
					this.test_response__ = {
						"status": response.status,
						"body": response.data,
						"headers": h
					};
				}).catch(error=>{
					this.test_waiting__=false;
					console.log( "Error" );
					var h = {};
					for( var d  in error.response.headers ){
						h[ d ] = error.response.headers[ d ];
					}
					console.log( h );
					this.test_response__ = {
						"status": error.response.status,
						"body": error.response.data,
						"headers": h
					};
				});
		},
		create_test_variables__: function(){
			this.test__['factors']['v'] = this.input_factors_to_values( this.engine__['input_factors'] );
		},
		url_neat: function(v){
			v = v.replace(/\?/g, "?\n");
			v = v.replace(/\&/g, "&\n");
			if( v.length > 250 ){
				return v.substr(0,250)+ ".....";
			}else{
				return v;
			}
		},
		input_factors_to_values: function(v){
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
						vvv.push( this.input_factors_to_values( v[ k ]['v'][ vi ] ) );
					}
					vv[k] = {"k":k,"t":"L", "v": vvv , "m":v[k]['m']};
				}else if( v[ k ]['t'] == "O" ){
					vv[k] = {"k":k,"t":"O", "v": this.input_factors_to_values( v[ k ]['v'] ) , "m":v[k]['m']};
				}else if( v[ k ]['t'] == "B" ){
					vv[k] = {"k":k,"t":"B", "v": [], "m":v[k]['m']};
				}else if( v[ k ]['t'] == "NL" ){
					vv[k] = {"k":k,"t":"NL", "v": null, "m":v[k]['m']};
				}
			}
			return vv;
		},
		make_query_string__: function( v ){
			var q = [];
			for( var i in v ){
				if( v[i]['t'] == "T" || v[i]['t'] == "N" || v[i]['t'] == "B" ){
					q.push( i+ "=" + encodeURIComponent( v[i]['v'] ) );
				}else if( v[i]['t'] == "O" || v[i]['t'] == "L" ){
					q.push( i+ "=Object");
				}else{
					q.push( i+ "=UnHandled");
				}
			}
			return q.join("&");
		},
		initiate_export_data__: function(){
			var vp = prompt("Enter password");
			if( vp ){
				var vurl__ = "/admin/app/<?=$config_param3 ?>/pages/<?=$page["_id"]?>/edit?version=&action=export_function_engine&page_version_id=<?=md5($page['_id']) ?>&password="+encodeURIComponent(vp);
				window.open( vurl__ );
			}
		},
		find_checks__: function(){
			var c = [];
			for(var i=0;i<this.engine__['stages'].length;i++){
				c.push( {"checked":false, "if":false} );
			}
			this.checks__ = c;
		},
		item_clicked__: function( k ){
			setTimeout(this.item_clicked2__,50, k);
		},
		item_clicked2__: function( stagei__ ){
			var vfirst = -1;
			for( var i=0;i<stagei__;i++ ){
				if( this.checks__[i]['checked'] ){
					vfirst = i+0;
				}
			}
			if( vfirst > -1 && vfirst < stagei__ &&  this.checks__[stagei__]['checked'] ){
				for(var i=vfirst+1;i<=stagei__;i++){
					if( this.engine__['stages'][i]['k']['v'].match( /^(If|While|for|ForEach|HTMLElement)$/i) ){
						break;
					}
					this.checks__[  i ] =  {"checked":true,"if":false} ;
				}
			}
			if( this.engine__['stages'][stagei__]['k']['v'].match( /^(If|While|for|ForEach|HTMLElement)$/i) ){
				if( this.checks__[stagei__]['checked'] == true ){
					var k2 = this.find_next_rand__( stagei__ );
					for(var i=stagei__+1;i<=k2;i++){
						this.checks__[  i ] =  {"checked":true,"if":true} ;
					}
				}else{
					this.find_checks__();
				}
			}
			setTimeout(this.find_checked_count__, 50);
		},
		find_checked_count__: function(){
			var c = 0;
			var is_checked = 0;
			for( var i=0;i<this.checks__.length;i++ ){
				if( this.checks__[i]['checked'] ){
					c++;
					if( is_checked == 0 ){
						is_checked= 1;
					}
				}
				if( is_checked == 1 ){
					if( this.checks__[i]['checked'] == false ){
						is_checked = 2;
					}
				}else if( is_checked == 2 && this.checks__[i]['checked'] ){
					this.checks__[  i ] =  {"checked":false,"if":false} ;
					c--;
				}
			}
			this.checked_items__ = c;
		},
		delete_stages__: function(v){
			var delcnt=0;
			for( var i=0;i<this.checks__.length;i++){if( this.checks__[i]['checked'] ){
				this.engine__['stages'].splice(i-delcnt,1);
				delcnt++;
			}}
			this.find_checks__();
			this.find_checked_count__();
			this.fill_variables__();
			this.save_need__=true;
		},
		duplicate_stages__: function(){
			var sp = [];
			var lasti = 0;
			var vc = [];
			for( var i=0;i<this.checks__.length;i++){if( this.checks__[i]['checked'] ){
				sp.push( JSON.parse( JSON.stringify( this.engine__['stages'][i] ) ) );
				vc.push( this.checks__[i] );
				lasti = i;
			}}
			var c = [];
			lasti++;
			for(var i=0;i<sp.length;i++){
				this.checks__.splice( lasti, 0, {"checked":false,"if":false} );
				this.engine__['stages'].splice( lasti, 0, sp[i] );
				c.push(lasti);
				lasti++;
			}
			this.find_checks__();
			for(var i=0;i<c.length;i++){
				this.checks__[  c[i] ] =  vc[i] ;
			}
			this.find_checked_count__();
			this.fill_variables__();
			this.save_need__=true;
		},
		uncheck_all__: function(){
			this.checked_items__ = 0;
			this.find_checks__();
		},
		move_up__: function(){
			var sp = [];
			var c = [];
			var firsti = -1;
			var vl = 0;
			var vc = [];
			for( var i=0;i<this.checks__.length;i++){if( this.checks__[i]['checked'] ){
				sp.push( JSON.parse( JSON.stringify( this.engine__['stages'][i] ) ) );
				if( firsti == -1){
					firsti = i;
				}
				vc.push( this.checks__[i] );
				c.push( i );
			}}
			if( firsti > 0 ){
				vl = 0;
				if( this.engine__['stages'][firsti-1]['k']['v'].match( /^(EndIf|EndWhile|EndFor|EndForEach|HTMLElementEnd)$/i ) ){
					vl = 1;
				}else if( this.engine__['stages'][firsti-1]['k']['v'].match( /^(If|while|for|foreach|htmlelement)$/i ) ){
					if( this.engine__['stages'][firsti-1]['k']['v'] == "HTMLElement" ){
						if( this.engine__['stages'][firsti-1]['htmlelement']['single'] == false ){
							vl = -1;
						}
					}else{
						vl = -1;
					}
				}
				var delcnt=0;
				for(var i=0;i<this.checks__.length;i++){if( this.checks__[i]['checked'] ){
					this.engine__['stages'].splice( i-delcnt, 1 );
					delcnt++;
				}}
				firsti--;
				for(var i=0;i<sp.length;i++){
					sp[i]['l'] = sp[i]['l']+vl;
					this.engine__['stages'].splice( firsti, 0, sp[i] );
					firsti++;
				}
				this.find_checks__();
				for(var i=0;i<c.length;i++){
					this.checks__[  Number(c[i])-1 ] =  vc[i] ;
				}
				this.find_checked_count__();
				this.fill_variables__();
				this.save_need__=true;
			}
		},
		move_down__: function(vid){
			var sp = [];
			var c = [];
			var firsti = -1;
			var lasti = -1;
			var vl = 0;
			var vc = [];
			for( var i=0;i<this.checks__.length;i++){if( this.checks__[i]['checked'] ){
				sp.push( JSON.parse( JSON.stringify( this.engine__['stages'][i] ) ) );
				if( firsti == -1){
					firsti = i;
				}
				lasti = i;
				c.push( i );
				vc.push( this.checks__[i] );
			}}
			if( firsti > -1 && lasti < this.checks__.length-1 ){
				vl = 0;
				if( this.engine__['stages'][lasti+1]['k']['v'].match( /^(EndIf|EndWhile|EndFor|EndForEach|htmlelementend)$/i ) ){
					vl = -1;
				}else if( this.engine__['stages'][lasti+1]['k']['v'].match( /^(If|While|For|ForEach|htmlelement)$/i ) ){
					if( this.engine__['stages'][lasti+1]['k']['v'] == "HTMLElement" ){
						if( this.engine__['stages'][lasti+1]['htmlelement']['single'] == false ){
							vl = 1;
						}
					}else{
						vl = 1;
					}
				}
				var delcnt=0;
				for(var i=0;i<this.checks__.length;i++){if( this.checks__[i]['checked'] ){
					this.engine__['stages'].splice( i-delcnt, 1 );
					delcnt++;
				}}
				firsti++;
				for(var i=0;i<sp.length;i++){
					sp[i]['l'] = sp[i]['l']+vl;
					this.engine__['stages'].splice( firsti, 0, sp[i] );
					firsti++;
				}
				this.find_checks__();
				for(var i=0;i<c.length;i++){
					this.checks__[  Number(c[i])+1 ] =  vc[i] ;
				}
				this.find_checked_count__();
				this.fill_variables__();
				this.save_need__=true;
			}
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
						new_val={"th":"", "v":{"i":{"t":"T", "v":""}, "l": {"t":"T", "v":""}}};
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
		add_stage__: function( vp ){
			this.checks__.push({"checked":false,"if":false});
			var new_stage_id = vp;
			if( vp == 'last' ){
				this.engine__['stages'].push({
					"k": {"t":"none", "v": "none", "vs": {}},
					"pk": "none",
					"t": "n", // n=none,c=cmd,o=object
					"d": {}, // data
					"l": 1, // level
					"e": false,
					"ee": true, //editable
					"er": "",
					"wr": "",
				});
				new_stage_id = this.engine__['stages'].length-1;
			}else{
				var vl = Number( this.engine__['stages'][vp]['l'] );
				if( this.engine__['stages'][vp]['k']['v'] == "EndIf" || this.engine__['stages'][vp]['k']['v'] == "EndWhile" || this.engine__['stages'][vp]['k']['v'] == "EndForEach" || this.engine__['stages'][vp]['k']['v'] == "EndFor"  || this.engine__['stages'][vp]['k']['v'] == "HTMLElementEnd" ){
					vl++;
				}
				this.engine__['stages'].splice( vp, 0, {
					"k": {"t":"none", "v": "none", "vs": {}},
					"pk": "none",
					"t": "n", // n=none,c=cmd,o=object
					"d": {},
					"l": vl,
					"e": false,
					"ee": true,
					"er": "",
					"wr": "",
				});
			}
			this.find_checks__();
			this.save_need__ = true;
			this.fill_variables__();
		},
		stage_change_stage__: function( vid, new_key, new_type ){
			this.just_created_stage__ = vid;
			setTimeout(function(v){v.just_created_stage__=-1;},10000,this);
			console.log("stage change: " + vid + " : " + new_key['v'] + ": " + new_key['t'] + ": " + new_type);
			var curstage = Number(this.engine__['stages'][ vid ]['l']);
			this.engine__['stages'][ vid ]['e'] = true;
			this.engine__['stages'][ vid ]['d'] = false;

			console.log( this.engine__['stages'][ vid ]['pk'] + " : " + new_key['v']  );
			if( this.engine__['stages'][ vid ]['pk'] != new_key['v'] && this.engine__['stages'][ vid ]['k']['v'].match(/^(If|For|ForEach|While)$/i) ){
				if( 'vrand' in this.engine__['stages'][ vid ] ){
					this.engine__['stages'][ vid ]['pk'] = "None";
					this.engine__['stages'][ vid ]['k']['v'] =  "None";
					this.engine__['stages'][ vid ]['k']['t'] =  "n";
					this.engine__['stages'][ vid ]['t'] =  "n";

					console.log("changing stage to normal");
					var lastif = this.find_next_rand__( vid );
					this.checks__.pop();
					this.engine__['stages'].splice(lastif,1);
					for(var i=Number(vid)+1;i<lastif;i++){
						this.engine__['stages'][ i ][ 'l' ] =  Number(this.engine__['stages'][ i ]['l'])-1;
					}
				}else{
					console.log("Group command vrand not found");
				}
			}

			if( new_key['t'] == "c" ){
				if( new_key['v'] in this.stage_params__ ){
					this.engine__['stages'][ vid ]['d'] = this.json__( this.stage_params__[ new_key['v'] ]['p'] );
					if( 'group' in this.stage_params__[ new_key['v'] ] ){
						if( this.stage_params__[ new_key['v'] ]['group'] ){
							var vrand__ = "v_" + ( (Math.random()*10000000).toFixed() );
							this.engine__['stages'][ vid ]['vrand'] = vrand__;
							this.checks__.push({"checked":false,"if":false});
							this.insert_after__( vid, {
								"k": {"t":"c", "v": this.stage_params__[ new_key['v'] ]['end'], "vs": {}},
								"t": "c",
								"l": curstage,
								"e": false,
								"d": {},
								"er": "","wr": "",
								"vrand": vrand__,
								"vend": true
							});
							this.checks__.push({"checked":false,"if":false});
							this.insert_after__( vid, {
								"k": {"t":"n", "v": "None"},
								"t": "n",
								"l": (curstage+1),
								"e": false,
								"d": {},
								"er": "","wr": "",
							});
						}
					}
				}else{
					console.log("Command: " + new_key['v'] + " not found in stage_params");
				}
			}else if( new_key['t'] in this.config_object_properties__ ){
				if( this.get_o_sub_var( this.all_factors_stage_wise__[ vid ], new_key['v'] ) ){
					new_key['vs'] = {
						"v": ".",
						"t": "n",
						"d": {},
					};
				}else{
					console.log("Object: " + new_key['v'] + " not found in stage wise params");
				}
			}else if( new_key['t'] in this.plugin_data__ ){
				if( this.get_o_sub_var( this.all_factors_stage_wise__[ vid ], new_key['v'] ) ){
					new_key['plg'] = new_key['t']+'';
					new_key['vs'] = {
						"v": ".",
						"t": "n",
						"d": {},
					};
				}else{
					console.log("Object: " + new_key['v'] + " not found in stage wise params");
				}
			}else if( new_key['t'] == "TH" ){
				console.log("TH found");
				var d = this.get_o_sub_var( this.all_factors_stage_wise__[ vid ], new_key['v'] );
				this.echo__( d );
				if( d ){
					//  CHECK IF TH VAR IS PART OF SPECIAL PLUGGINS
					//new_key['plg'] = d['plg']+'';
					new_key['vs'] = {
						"v": ".",
						"t": "n",
						"d": {},
					};
				}else{
					console.log("Object: " + new_key['v'] + " not found in stage wise params");
				}
			}else if( new_key['t'] == "PLG" ){
				var d = this.get_o_sub_var( this.all_factors_stage_wise__[ vid ], new_key['v'] );
				if( d ){
					new_key['plg'] = d['plg']+'';
					new_key['vs'] = {
						"v": ".",
						"t": "n",
						"d": {},
					};
				}else{
					console.log("Object: " + new_key['v'] + " not found in stage wise params");
				}
			}

			this.engine__['stages'][ vid ]['k'] = new_key;
			this.engine__['stages'][ vid ]['t'] = new_type;
			this.engine__['stages'][ vid ]['pk'] =  new_key['v'];
			this.engine__['stages'][ vid ]['e'] = false;

			this.save_need__ = true;
		},
		json__: function( v ){
			if( typeof(v) == "object" ){
				return JSON.parse( JSON.stringify( v ) );
			}else{
				return v;
			}
		},
		insert_after__: function( vid, vd ){
			if( Number(this.engine__['stages'].length)-1 == Number(vid) ){
				this.engine__['stages'].push(vd);
			}else{
				this.engine__['stages'].splice( Number(vid)+1, 0, vd );
			}
			this.save_need__ = true;
		},
		getlevel__: function(vid){
			var vl = (Number(this.engine__['stages'][vid]['l'])-1);
			if( vl < 0 ){
				vl = 0;
			}
			var v = "margin-left:"+(vl*20)+"px; ";
			return v;
		},
		find_next_rand__: function( vi ){
			var lastif = -1;
			var start_rand = this.engine__['stages'][ Number(vi) ]['vrand'];
			for(var i=Number(vi)+1;i<this.engine__['stages'].length;i++){
				if( 'vrand' in this.engine__['stages'][ i ] ){
					if( start_rand == this.engine__['stages'][ i ]['vrand']+"" ){
						lastif = i+0;
						break;
					}
				}
			}
			return lastif;
		},
		find_prev_rand__: function( vi ){
			var lastif = -1;
			var start_rand = this.engine__['stages'][ Number(vi) ]['vrand'];
			for(var i=Number(vi)-1;i>=0;i--){
				if( 'vrand' in this.engine__['stages'][ i ] ){
					if( start_rand == this.engine__['stages'][ i ]['vrand']+"" ){
						lastif = i+0;
						break;
					}
				}
			}
			return lastif;
		},
		add_if_condition__: function( vi ){
			this.engine__['stages'][ vi ]['d']['cond'].push({
				"lhs": {"t":"V","v":{"v":"","t":"","vs":false}},
				"op": "==",
				"rhs": {"t":"T","v":""},
			});
			this.save_need__ = true;
		},
		delete_if_condition__: function( vi, vfi ){
			this.engine__['stages'][ vi ]['d']['cond'].splice(vfi,1);
			this.save_need__ = true;
		},
		popup_import_json_data__: function(){
			try{
				var d = JSON.parse(this.popup_import_str__);
				this.popup_data__ = this.plain_json_to_template(d);
				this.set_stage_sub_var__(this.popup_stage_id__, this.popup_datavar__, this.popup_data__);
				this.popup_import__ = false;
				this.updated_option__();
			}catch(e){
				console.log("Popup Import failed: "  + e );
			}
		},
		show_doc_popup: function(vdoc__){
			this.doc_popup_doc__ = vdoc__;
			this.doc_popup_text__ = "Loading...";
			axios.get("<?=$config_global_apimaker_path ?>docs/"+this.doc_popup_doc__).then(response=>{
				if( response.status == 200 ){
					this.doc_popup_text__ = response.data;
				}else{
					this.doc_popup_text__ = "File not found";
				}
			}).catch(e=>{
				this.doc_popup_text__ = "File not found";
			});
			if( this.doc_popup__ == false ){
				this.doc_popup__ = new bootstrap.Modal( document.getElementById('doc_popup__') );
			}
			this.doc_popup__.show();
		},
		plain_json_to_template: function( v ){
			if( typeof(v) == "object" ){
				if( "length" in v == false ){
					for( var key in v ){
						if( v[ key ] == null ){
							v[ key ] = {"k": key, "t":"NL", "v": null };
						}else if( typeof(v[key]) == "object" && v[key] != null ){
							if( "length" in v[ key ] ){
								v[ key ] = {"k": key, "t":"L", "v": this.plain_json_to_template( v[key] ) };
							}else{
								v[ key ] = {"k": key, "t":"O", "v": this.plain_json_to_template( v[key] ) };
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
								v[ key ] = {"t":"L", "v": this.plain_json_to_template( v[key] ) };
							}else{
								v[ key ] = {"t":"O", "v": this.plain_json_to_template( v[key] ) };
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
				console.log("plain_json_to_template: "+ typeof(v) + " Incorrect data type");
			}
			return v;
		}
	}
});

<?php foreach( $components as $i=>$j ){ ?>
	app.component( "<?=$j ?>", <?=$j ?> );
<?php } ?>
<?php foreach( $plugins as $i=>$j ){ ?>
	app.component( "<?=$j ?>", <?=$j ?> );
<?php } ?>
app.mount("#app");


function get_object_notation__( v ){
	console.log("get_object_notation: " );
	console.log( v );
	var vv = {};
	if( typeof(v)==null ){
		this.error("get_object_notation: null ");
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