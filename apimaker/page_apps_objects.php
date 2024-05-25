<?php require("page_apps_apis_api_css.php"); ?>
<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div class="btn btn-sm btn-outline-dark float-end" v-on:click="show_configure()" >Configure</div>
			<div class="btn btn-sm btn-outline-dark float-end me-2" v-on:click="show_create()" >Create Node</div>
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


				<div>
					<graph_object_new datavar="new_object" v-bind:v="new_object" ></graph_object_new>
					<div><input type="button" class="btn btn-outline-dark btn-sm" value="Create Object" v-on:click="create_new_object()"></div>
				</div>


				<div class="code_row code_line" >
					<!-- <graph_object datavar="new_object" v-bind:v="new_object" ></graph_object> -->
				</div>

				<pre>{{ new_object }}</pre>

				
			</div>

		</div>
	</div>




	<div id="context_menu__" data-context="contextmenu" class="context_menu__" v-bind:style="context_style__">
		<template v-if="context_type__=='datatype'" >
			<template v-if="context_list_filter__.length>0" >
				<div v-for="id in context_list_filter__" v-bind:class="{'context_item':true,'cse':context_value__==id}" v-on:click.stop="context_select__(id,'datatype')" ><div style="min-width:30px;padding-right:10px;display: inline-block;" >{{ id }}</div><div style="display: inline; color:gray;" v-if="id in data_types__" >{{ data_types__[ id ] }}</div></div>
			</template>
			<div v-else >
				<div style="display:flex;gap:20px;" >
					<div>
						<div v-for="id,ii in data_types1__" v-bind:class="{'context_item':true,'cse':context_value__==ii}" v-on:click.stop="context_select__(ii,'datatype')" ><div style="min-width:30px;padding-right:10px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
					</div>
					<div>
						<div v-for="id,ii in data_types2__" v-bind:class="{'context_item':true,'cse':context_value__==ii}" v-on:click.stop="context_select__(ii,'datatype')" ><div style="min-width:30px;padding-right:10px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
					</div>
				</div>
				<div v-if="is_it_let_assign_stage__()" class="context_item" v-on:click.stop="context_select__('ctf__','datatype')" >Convert to Function</div>
			</div>
		</template>
		<template v-else-if="context_type__=='inputfactortypes'" >
			<div v-for="id,ii in input_types__" class="context_item" v-on:click.stop="context_select__(ii,'inputtype')" ><div style="min-width:30px;padding-right:10px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
		</template>
		<template v-else-if="context_type__=='inputfactortypes2'" >
			<div v-for="id,ii in input_types2__" class="context_item" v-on:click.stop="context_select__(ii,'inputtype')" ><div style="min-width:30px;padding-right:10px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
		</template>
		<template v-else-if="context_type__=='list'" >
			<div v-for="id in context_list__" class="context_item" v-on:click.stop="context_select__(id,'')" >{{ id }}</div>
		</template>
		<template v-else-if="context_type__=='list2'" >
			<template v-if="'list2' in global_data__" >
				<template v-if="typeof(global_data__['list2'])=='object'" >
					<div v-for="fd,fi in global_data__['list2']" class="context_item" v-on:click.stop="context_select__(fd['k'],'')" >{{ fd['k'] + ': ' + fd['t'] }}</div>
				</template>
				<div v-else >List values incorrect</div>
			</template>
			<div v-else >List not defined</div>
		</template>
		<template v-else-if="context_type__=='vars'" >
			<div v-if="Object.keys(all_factors_stage_wise__[ context_stage_id__ ]).length==0">No vars found</div>
			<template v-else >
				<div v-if="Object.keys(all_factors_stage_wise__[ context_stage_id__ ]).length>5" ><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="context_menu_key__"  ></div>
				<div class="context_menu_list__" data-context="contextmenu" >
					<template v-for="v,k in all_factors_stage_wise__[ context_stage_id__ ]" >
						<template v-if="context_menu_key_match__(k)" >
							<div v-if="v['t']=='O'" style="display:flex;" >
								<div class="context_item" v-on:click.stop="context_select__(k,'var')" v-html="context_menu_key_highlight__(k)+ ': <abbr>'+data_types__[v['t']]+'</abbr>'" ></div>
								<div v-if="context_expand_key__!=k" class="context_item_plus__" v-on:click.prevent.stop="context_expand_key__=k" >+</div>
								<div v-if="context_expand_key__==k" class="context_item_plus__" v-on:click.prevent.stop="context_expand_key__=''" >-</div>
							</div>
							<div v-else-if="v['t'] in plugin_data__" style="display:flex;" >
								<div class="context_item" v-on:click.stop="context_select__(k,'var')" v-html="context_menu_key_highlight__(k)+ ': <abbr>'+v['t']+'</abbr>'" ></div>
							</div>
							<div v-else class="context_item" v-on:click.stop="context_select__(k,'var')" v-html="context_menu_key_highlight__(k)+context_get_type_notation__(v)" ></div>
							<template v-if="context_expand_key__==k" >
								<template v-for="v2,k2 in get_object_props_list(context_stage_id__,k)" >
									<div class="context_item" v-on:click.stop="context_select__(k + '->' + v2['k'],'var')" v-html="k + '->' + v2['k'] + ': <abbr>'+data_types__[v2['t']]+ '</abbr>'" ></div>
								</template>
							</template>
						</template>
					</template>
				</div>
			</template>
		</template>
		<template v-else-if="context_type__=='varsub'" >
			<div v-if="context_var_for__ in config_object_properties__==false">No Props found {{ context_var_for__ }}</div>
			<template v-else >
				<div class="context_menu_list__" data-context="contextmenu" >
					<template v-for="v,k in config_object_properties__[ context_var_for__ ]" >
						<div class="context_item" v-on:click.stop="context_select__(k,'prop')" >{{ k }}</div>
					</template>
				</div>
			</template>
		</template>
		<template v-else-if="context_type__=='boolean'" >
			<div class="context_item" v-on:click.stop="context_select__('true','')" >true</div>
			<div class="context_item" v-on:click.stop="context_select__('false','')" >false</div>
		</template>
		<template v-else-if="context_type__=='order'" >
			<div class="context_item" v-on:click.stop="context_select__('a-z','')" >a-z</div>
			<div class="context_item" v-on:click.stop="context_select__('z-a','')" >z-a</div>
		</template>
		<template v-else-if="context_type__=='things'" >
			<div v-for="td,ti in things_used__" class="context_item" v-on:click.stop="context_select__(ti,'thing')" >{{ ti }}</div>
		</template>
		<template v-else-if="context_type__=='thing'" >
			<div>{{ context_thing__ }}</div>
			<template v-if="context_thing__ in context_thing_list__" >
				<template v-if="context_thing_list__[context_thing__].length>5" >
					<div><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="context_menu_key__"  ></div>
				</template>
			</template>
			<div class="context_menu_list__" data-context="contextmenu" >
				<!--<pre>{{ context_thing_list__ }}</pre>-->
				<div v-if="context_thing_msg__" class="text-success" >{{ context_thing_msg__ }}</div>
				<div v-if="context_thing_err__" class="text-danger" >{{ context_thing_err__ }}</div>
				<template v-if="context_thing__ in context_thing_list__" >
					<template v-for="fv,fi in context_thing_list__[ context_thing__ ]" >
						<div v-if="context_menu_key_match__(fv['l']['v'])" class="context_item" v-on:click.stop="context_select__(fv,context_type__)" v-html="context_menu_thing_highlight__(fv)" ></div>
					</template>
				</template>
				<div v-else>List undefined</div>
			</div>
		</template>
		<template v-else-if="context_type__=='graph-thing'" >
			<div>{{ context_thing_label__ }}</div>
			<template v-if="context_thing__ in context_thing_list__" >
				<template v-if="context_thing_list__[context_thing__].length>5" >
					<div><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="context_menu_key__"  ></div>
				</template>
			</template>
			<div class="context_menu_list__" data-context="contextmenu" >
				<!--<pre>{{ context_thing_list__ }}</pre>-->
				<div v-if="context_thing_msg__" class="text-success" >{{ context_thing_msg__ }}</div>
				<div v-if="context_thing_err__" class="text-danger" >{{ context_thing_err__ }}</div>
				<template v-if="context_thing__ in context_thing_list__" >
					<template v-for="fv,fi in context_thing_list__[ context_thing__ ]" >
						<div v-if="context_menu_key_match__(fv['l']['v'])" class="context_item" v-on:click.stop="context_select__(fv,context_type__)" v-html="context_menu_thing_highlight__(fv)" ></div>
					</template>
				</template>
				<div v-else>List undefined</div>
			</div>
		</template>
		<div v-else>No list configured {{ context_type__ }}</div>
	</div>


	<div class="modal fade" id="create_popup" tabindex="-1" >
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Create Object</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">
		        	<div>Name/Thing/Node</div>

					<div class="code_row code_line" >
						<graph_object_new datavar="new_object" v-bind:v="new_object" ></graph_object_new>
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="Create Object"></div>
					</div>

		        	<div v-if="cmsg" class="alert alert-success" >{{ cmsg }}</div>
		        	<div v-if="cerr" class="alert alert-success" >{{ cerr }}</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
		        <button type="button" class="btn btn-primary btn-sm"  v-on:click="createnow">Create</button>
		      </div>
		    </div>
		  </div>
	</div>



</div>


<script>
<?php
$components = [
	"graph_object","graph_object_new",
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
			"context_api_url__": "?",
			"smsg": "", "serr":"","msg": "", "err":"","cmsg": "", "cerr":"","kmsg": "", "kerr":"",
			"keyword": "",
			"token": "",
			"saved": <?=($saved?"true":"false") ?>,
			"keys": [], "settings_popup": false, "create_popup": false,
			"show_key": {},
			"new_object": {
				"l": {"t":"T","v":"testing"},
				"i_of": {
					"t":"GT", 
					"th":{"l": {"t":"T","v":""}, "i":{"t":"T","v":""} },
					"v": {"l": {"t":"T","v":""}, "i":{"t":"T","v":""} }
				}
			},
			"edit_object": {
				"l": {"t":"T","v":"testing"},
				"i_of": {"t":"L","v":[
					{
						"t":"GT", 
						"th":{"l": {"t":"T","v":""}, "i":{"t":"T","v":""} },
						"v":{"l": {"t":"T","v":""}, "i":{"t":"T","v":""} }
					}
				]},
				"p_of": [{"t":"TH", "th": "", "v":{"l": {"t":"T","v":""}, "i":{"t":"T","v":""} }}],
				"props": [
					{"p": {"t":"T","v":"" }, "v": [{"t":"TH","v":{"l": {"t":"T","v":""}, "i":{"t":"T","v":""} }}]}
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
			context_thing_label__: "",
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

		create_new_object: function(){
			this.cerr = "";this.cmsg = "";
			if( this.new_object['l']['v'] == "" ){
				this.cerr = "Need label";return false;
			}else if( this.new_object['l']['v'].match(/^[a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{1,100}$/) == null ){
				this.cerr = "Label should follow format [a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}";return false;
			}
			if( this.new_object['i_of']['th']['i']['v'] == "" ){
				this.cerr = "Need instance/tag type";return false;
			}
			if( this.new_object['i_of']['v']['i']['v'] == "" ){
				this.cerr = "Need instance/tag";return false;
			}
			axios.post("?",{
				"action": "object_create_object",
				"data": this.new_object
			}).then(response=>{
				
			}).catch(error=>{
				
			});
		},

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
			var data_var = "";
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
						if( el.hasAttribute("data-var") && data_var == false ){
							data_var = el.getAttribute("data-var");
						}
						if( el.hasAttribute("data-var-parent") && data_var_parent == "" ){
							data_var_parent = el.getAttribute("data-var-parent");
						}
						if( el.className == "help-div" ){
							doc = el.getAttribute("doc");
							this.show_doc_popup__(doc);
							return 0;
						}
						if( el.className == "help-div2" ){
							doc = el.getAttribute("data-help");
							this.simple_popup_el__ = el;
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
			console.log( el );
			console.log( el_data_type );
			if( el_data_type ){
				var t = el_data_type.getAttribute("data-type");
				if( t == "type_pop" ){

				}else if( t == "objecteditable" ){
					this.popup_datavar__ = data_var;
					var v = this.get_editable_value__({'data_var':data_var});
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
					this.simple_popup_datavar__ = data_var;
					var v = this.get_editable_value__({'data_var':data_var});
					if( v === false ){console.log("event_click: value false");return false;}

					this.simple_popup_data__ = v;
					this.simple_popup_type__ = el_data_type.getAttribute("editable-type");
					this.simple_popup_modal__ = true;
					//this.show_and_focus_context_menu__();
					this.set_simple_popup_style__();

				}else if( t == "payloadeditable" ){
					this.popup_datavar__ = data_var;
					var v = this.get_editable_value__({'data_var':data_var});
					if( v === false ){console.log("event_click: value false");return false;}
					this.popup_data__ = v;
					this.popup_type__ = 'PayLoad';
					this.popup_title__ = "Request Payload Editor";
					this.popup_modal_open__();

				}else if( t == "dropdown" || t == "dropdown2" || t == "dropdown3" || t == "dropdown4" ){
					this.context_el__ = el_data_type;
					this.context_value__ = el_data_type.innerHTML;
					this.context_menu_key__ = "";
					this.context_datavar__ = data_var;
					var v = this.get_editable_value__({'data_var':data_var});
					console.log( v );
					if( v === false ){console.log("event_click: value false");return false;}
					this.context_type__ = el_data_type.getAttribute("data-list");
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
							console.log( this.context_thing__ );
							setTimeout(this.context_thing_list_load_check__,300);
						}else{
							this.context_thing__ = "UnKnown";
						}
					}
					if( this.context_type__ == "graph-thing" ){
						if( el_data_type.hasAttribute("data-thing") ){
							this.context_thing__ = el_data_type.getAttribute("data-thing");
							this.context_thing_label__ = el_data_type.getAttribute("data-thing-label");
							console.log( this.context_thing__ );
							setTimeout(this.context_thing_list_load_check__,300);
						}else{
							this.context_thing__ = "UnKnown";
							this.context_thing_label__ = "Unknown";
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
					setTimeout( this.editablebtn_click__, 100, el_data_type, data_var, e );
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
			this.settings_popup = new bootstrap.Modal(document.getElementById('settings_modal'));
			this.settings_popup.show();
		},
		show_create: function(){
			this.create_popup = new bootstrap.Modal(document.getElementById('create_popup'));
			this.create_popup.show();
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
			this.set_sub_var__( this, this.context_datavar__, k );
			if( t == "datatype" ){
				this.update_variable_type__( this.context_datavar__, k );
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
				}
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
				'data_var': '',
				'data_type': '',
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

		editablebtn_click__: function( el_data_type, data_var,e ){
			var v = el_data_type.previousSibling.innerText;
			v = v.replace(/[\u{0080}-\u{FFFF}]/gu, "");
			// v = v.replace( /\&nbsp\;/g, " " );
			// v = v.replace( /\&gt\;/g,  ">" );
			// v = v.replace( /\&lt\;/g,  "<" );
			vv = this.v_filter__(v, el_data_type.previousSibling );
			if( vv == v ){
				this.update_editable_value__({'data_var':data_var}, v);
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
			var ov = this.get_sub_var__(this, s['data_var'], v);
			if( ov != v ){
				this.set_sub_var__(this, s['data_var'], v);
				this.check_sub_key__(this, s['data_var'], v);
			}
		},
		get_editable_value__: function(s){
			return this.get_sub_var__(this, s['data_var']);
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
		context_thing_list_load_check__: function(){
			if( this.context_thing__ in this.context_thing_list__ == false ){
				this.context_thing_list__[ this.context_thing__ ] = [];
			}
			//if( this.context_thing_list__[ this.context_thing__ ].length == 0 )
			{
				this.context_thing_msg__ = "Loading...";
				this.context_thing_err__ = "";
				this.context_thing_list__[ this.context_thing__ ] = [];
				axios.post(this.context_api_url__, {
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
	}
});

<?php foreach( $components as $i=>$j ){ ?>
	app.component( "<?=$j ?>", <?=$j ?> );
<?php } ?>
var app1 = app.mount("#app");

</script>
