<?php require("page_apps_apis_api_css.php"); ?>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div class="btn btn-sm btn-outline-secondary float-end" v-on:click="show_configure()" >Configure</div>
			<div class="h3 mb-3"><span class="text-secondary" >Object Store</span></div>

			<div v-if="saved&&settings['enable']" style="display:flex; height: 40px;">
				<div>
					<input type="text" class="form-control form-control-sm w-auto d-inline" v-model="keyword" placeholder="Key">
					<input type="button" class="btn btn-outline-dark btn-sm" value="Search" v-on:click="searchit()">
				</div>
			</div>
			<div style="position:relative;overflow: auto; height: calc( 100% - 130px );">

				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>


				<div class="code_row code_line" >
					<table class="table table-bordered table-sm w-auto" >
						<tr>
							<td>Label</td>
							<td><inputtextbox datafor="new_object" v-bind:v="new_object['l']" datavar="new_object:l" ></inputtextbox></td>
						</tr>
						<tr>
							<td>Instance Of</td>
							<td><inputtextbox datafor="new_object" v-bind:v="new_object['l']" datavar="new_object:l" ></inputtextbox></td>
						</tr>
					</table>
				</div>

				
			</div>

		</div>
	</div>



</div>


<script>
<?php
$components = [
	"inputtextbox", "inputtextbox2", 
	"varselect", "varselect2", 
	"vobject", "vobject2", "vlist", 
	"vfield", "vdt", "vdtm", "vts", 
];
foreach( $components as $i=>$j ){
	require($apps_folder."/" . $j . ".js");
}
?>

var app = Vue.createApp({
	"data": function(){
		return {
			"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			"objectpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/",
			"app_id" : "<?=$app['_id'] ?>",
			"settings": <?=json_encode($app['internal_redis']) ?>,
			"smsg": "", "serr":"","msg": "", "err":"","kmsg": "", "kerr":"",
			"keyword": "",
			"token": "",
			"saved": <?=($saved?"true":"false") ?>,
			"keys": [], popup: false,
			"show_key": {},
			"new_object": {
				"l": {"t":"T","v":"testing"},
				"i_of": [{"t":"th","v":{"l": "", "i":""}}],
				"p_of": [{"t":"th","v":{"l": "", "i":""}}],
				"props": [
					{"p": {"t":"T","v":""}, "v": [{"t":"th","v":{"l": "", "i":""}}]}
				]
			},
			"is_locked__"			: false,
			"all_factors__"			: {},
			"show_saving__"			: false,
			"save_message__"		: "Saving..",
			"save_need__"			: false,
			"first_save__"			: false,
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
			context_menu__: false,
			context_for__: 'stages',
			context_var_for__: '',
			context_dependency__: "",
			context_callback__: "",
			context_el__: false,
			context_style__: "display:none;",
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
			ace_editor2: false,
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
			things_used__: {},
		};
	},
	mounted:function(){
		document.addEventListener("keyup", this.event_keyup__ );
		document.addEventListener("keydown", this.event_keydown__);
		document.addEventListener("click", this.event_click__, true);
		document.addEventListener("scroll", this.event_scroll__, true);
		document.addEventListener("blur", this.event_blur__, true);
		window.addEventListener("paste", this.event_paste__, true);
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
						var v = e.target.innerText;
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
		event_blur__: function( e ){
			if( e.target.hasAttribute("data-type") ){
				if( e.target.getAttribute("data-type") == "editable" ){
					e.stopPropagation();
					e.preventDefault();
					var s = this.find_parents__(e.target);
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
					el.insertAdjacentHTML("afterend", `<div class="inlinebtn" data-type="editablebtn" ><i class="fa fa-check-square-o" ></i></div>` );
				}
			}else{
				if( el.nextSibling ){
					el.nextSibling.outerHTML = '';
				}
			}
		},
		show_configure: function(){
			this.popup = new bootstrap.Modal(document.getElementById('settings_modal'));
			this.popup.show();
		},
		load_keys: function(){
			var k = "";
			if( this.keyword != "" ){
				k = this.keyword+'';
			}

			this.msg = "Loading...";
			axios.post("?", {
				"action" 		: "redis_load_keys",
				"keyword": k,
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.keys = response.data['keys'];
								for(var i=0;i<this.keys.length;i++){
									this.keys.splice(i,1);break;
								}
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = "Invalid response";
						}
					}else{
						this.err = "Incorrect response";
					}
				}else{
					this.err = "http:"+response.status;
				}
			}).catch(error=>{
				this.err = error.message;
			});
		},
		saveit: function(){
			this.smsg = "Saving...";
			this.serr = "";
			axios.post("?",{
				"action" 		: "redis_save_settings",
				"settings": this.settings,
			}).then(response=>{
				this.smsg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.smsg = "Saved";
								this.saved = true;
							}else{
								this.serr = response.data['error'];
							}
						}else{
							this.serr = "Invalid response";
						}
					}else{
						this.serr = "Incorrect response";
					}
				}else{
					this.serr = "http:"+response.status;
				}
			}).catch(error=>{
				this.serr = error.message;
			});
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
			if( this.popup_modal_displayed__ ){
				var s2 = document.getElementById("popup_modal_body__").getBoundingClientRect();
				this.context_style__ = "display:block;top: "+(Number(s.top)-Number(s2.top))+"px;left: "+(Number(s.left)-Number(s2.left))+"px;";
			}else{
				this.context_style__ = "display:block;top: "+s.top+"px;left: "+s.left+"px;";
			}
		},
		set_simple_popup_style__: function(){
			var s = this.simple_popup_el__.getBoundingClientRect();
			this.simple_popup_style__ = "top: "+s.top+"px;left: "+s.left+"px;";
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
			if( s['data_for'] in this ){
				var ov = this.get_sub_var__(this[ s['data_for'] ], s['data_var'], v);
				if( ov != v ){
					this.set_sub_var__(this[ s['data_for'] ], s['data_var'], v);
					this.check_sub_key__(this[ s['data_for'] ], s['data_var'], v);
				}
			}else{
				console.error("update_editable_value__: data_for unknown: " + s['data_for'] + ": " + s['data_var'] );
				return false;
			}
		},
		get_editable_value__: function(s){
			if( s['data_for'] in this ){
				return this.get_sub_var__(this[ s['data_for'] ], s['data_var']);
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
	}
});

<?php foreach( $components as $i=>$j ){ ?>
	app.component( "<?=$j ?>", <?=$j ?> );
<?php } ?>
var app1 = app.mount("#app");

</script>
