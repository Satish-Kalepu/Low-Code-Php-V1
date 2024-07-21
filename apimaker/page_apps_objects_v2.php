<?php require("page_apps_apis_api_css.php"); ?>
<?php require("page_apps_objects.css"); ?>

<style>
div.zz{ max-width:250px; max-height:150px; overflow:auto; white-space:nowrap; }
div.zz::-webkit-scrollbar {width: 6px;height: 6px;}
div.zz::-webkit-scrollbar-track { background: #f1f1f1;}
div.zz::-webkit-scrollbar-thumb { background: #888;}
div.zz::-webkit-scrollbar-thumb:hover { background: #555;}	
</style>

<div id="app" >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; width:calc( 100% - 150px ); height: calc( 100% - 50px ); background-color: white; " >
		<div style="padding: 10px;" >

			<div class="btn btn-sm btn-outline-dark float-end" v-on:click="show_configure()" >Configure</div>
			<div class="btn btn-sm btn-outline-dark float-end me-2" v-on:click="show_create()" >Create Node</div>
			<div v-if="thing_id!=-1" class="btn btn-sm btn-outline-dark float-end me-2" v-on:click="thing_id=-1" >Home</div>

			<div style="display:flex; column-gap: 50px;">
				<div class="h3 mb-3"><span class="text-secondary" >Object Store</span></div>
				<div>
					<div style="display:flex; width:300px; column-gap:5px;border:1px solid #ccc;background-color: white; align-items: center; ">
						<div class="thing_search_bar" title="Thing" data-type="dropdown" data-var="search_thing:v" data-list="graph-thing" data-thing="GT-ALL" data-thing-label="Things" data-context-callback-function="goto1" >Search</div>
						<div><i class="fa fa-search"></i></div>
					</div>
				</div>
			</div>

			<div style="position:relative;overflow: auto; height: calc( 100% - 60px );">
				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>

				<RouterView />

				<div>OKOK</div>

				<pre>{{ $route }}</pre>

			</div>

		</div>
	</div>
	<div class="modal fade" id="create_popup" tabindex="-1" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Create Object</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" id="create_popup_body">
					<p>Create new Name/Thing/Node</p>

					<graph_object_new datavar="new_thing" v-bind:v="new_thing"  ></graph_object_new>
					<div><input type="button" class="btn btn-outline-dark btn-sm" value="Create Object" v-on:click="create_new_thing()"></div>
					<div v-if="cmsg" class="alert alert-primary" >{{ cmsg }}</div>
					<div v-if="cerr" class="alert alert-danger" >{{ cerr }}</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
				</div>
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
			</div>
		</template>
		<template v-else-if="context_type__=='list'" >
			<div v-for="id in context_list__" class="context_item" v-on:click.stop="context_select__(id,'')" >{{ id }}</div>
		</template>
		<template v-else-if="context_type__=='list-kv'" >
			<div v-for="iv,ik in context_list__" class="context_item" v-on:click.stop="context_select__(iv,'kv')" >{{ iv['v'] }}</div>
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
		<template v-else-if="context_type__=='boolean'" >
			<div class="context_item" v-on:click.stop="context_select__('true','')" >true</div>
			<div class="context_item" v-on:click.stop="context_select__('false','')" >false</div>
		</template>
		<template v-else-if="context_type__=='order'" >
			<div class="context_item" v-on:click.stop="context_select__('a-z','')" >a-z</div>
			<div class="context_item" v-on:click.stop="context_select__('z-a','')" >z-a</div>
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
			<div><input spellcheck="false" type="text" id="contextmenu_key1" data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="context_menu_key__" v-on:keyup="context_menu_key_edit__" ></div>
			<div class="context_menu_list__" data-context="contextmenu" >
				<!--<pre>{{ context_thing_list__ }}</pre>-->
				<div v-if="context_thing_msg__" class="text-success" >{{ context_thing_msg__ }}</div>
				<div v-if="context_thing_err__" class="text-danger" >{{ context_thing_err__ }}</div>
				<div v-for="fv,fi in context_thing_graph_list__" class="context_item" v-on:click.stop="context_select__(fv,context_type__)" v-html="fv['r']" ></div>
				<!-- <template v-if="context_thing__ in context_thing_list__" >
					<template v-for="fv,fi in context_thing_list__[ context_thing__ ]" >
						<div v-if="context_menu_key_match__(fv['l']['v'])" class="context_item" v-on:click.stop="context_select__(fv,context_type__)" v-html="context_menu_thing_highlight_graph_thing__(fv)" ></div>
					</template>
				</template> 
				<div v-else>List undefined</div>
				-->
			</div>
		</template>
		<div v-else>No list configured {{ context_type__ }}</div>
	</div>

	<div class="modal fade" id="popup_modal__" tabindex="-1" >
	  <div class="modal-dialog modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <div class="modal-title" ><h5 class="d-inline">{{ popup_title__ }}</h5></div>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body" id="popup_modal_body__" v-bind:data-stagei="popup_stage_id__" style="position: relative;">
	      		<template v-if="popup_type__=='O'||popup_type__=='L'" >
	      			<template v-if="popup_import__==false" >
			      		<div align="right"><div class="btn btn-link btn-sm" style="position:absolute; margin-top:-60px; margin-left:-100px;" v-on:click="popup_import__=true" >Import</div></div>
						<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
			      		<!-- <pre>{{ popup_data__}}</pre> -->
			        	<vobject v-if="popup_type__=='O'" v-bind:v="popup_data__" v-bind:datafor="popup_for__" v-bind:datavar="popup_datavar__"  ></vobject>
			        	<vlist v-else-if="popup_type__=='L'" v-bind:v="popup_data__" v-bind:datafor="popup_for__" v-bind:datavar="popup_datavar__"  ></vlist>
			        	</div>
			        </template>
			        <template v-else >
						<textarea spellcheck="false" style="width:100%; height:100%; min-height: 200px; max-width:calc( 100% - 20px );max-height: 400px; font-family: revert;" v-model="popup_import_str__" v-on:keydown.tab.prevent.stop ></textarea>
			      		<div class="p-2">
			      			<div class="btn btn-secondary btn-sm" v-on:click="popup_import__=false" >Cancel</div>&nbsp; &nbsp;
							<div class="btn btn-primary btn-sm" v-on:click="popup_import_json_data__" >Import</div>
			      		</div>
			        </template>
	        	</template>
	        	<div v-else-if="popup_type__=='TT'" >
					<textarea spellcheck="false" style="width:100%; height:100%; white-space: nowrap;  font-family: revert; min-height: 200px; max-width:calc( 100% - 20px ); max-height: 400px;outline:0px;" v-model="popup_data__" v-on:keydown.tab.prevent.stop v-on:blur="popup_TT__()" ></textarea>
					<div align="center"><div class="btn btn-outline-dark btn-sm" v-on:click="popup_TT_update__()">Update</div></div>
	        	</div>
	        	<div v-else-if="popup_type__=='HT'" >
					<div id="popup_html_editor">HTML Editor coming sooon!</div>
	        	</div>
	        	<div v-else >Unhandled popup type {{ popup_type__ }}</div>
	      </div>
	    </div>
	  </div>
	</div>

	<div v-show="simple_popup_modal__" id="simple_popup_modal__" data-context="contextmenu" class="simple_popup__" v-bind:style="simple_popup_style__"  >
		<div style="padding:5px 10px; background-color:#f0f0f0; border-bottom:1px solid #ccc; " ><b>{{ simple_popup_title__ }}</b></div>
		<div style="padding:5px 10px;">
			<div v-if="simple_popup_type__=='convert_to_link'" >
				<div class="code_line" >
					<table cellpadding="5"><tbody>
						<tr valign="top"><td>Display Label</td><td><inputtextbox2 types="T" v-bind:v="convert_to_link_temp['label']" datavar="convert_to_link_temp:label" ></inputtextbox2></td></tr>
						<tr valign="top"><td>Link to Object</td><td>
							<inputtextbox2 types="GT" v-bind:v="convert_to_link_temp['link']" datavar="convert_to_link_temp:link" v-bind:initial_keyword="convert_to_link_temp['label']['v']" ></inputtextbox2>
							<div>{{ convert_to_link_temp['link']['i'] }} &nbsp;</div>
						</td></tr>
					</tbody></table>
				</div>
				<input type="button" class="btn btn-outline-dark btn-sm" value="Update" v-on:click.prevent.stop="convert_to_link_do" style="float:right;" >
				<input type="button" class="btn btn-outline-secondary btn-sm" value="Cancel" v-on:click.prevent.stop="simple_popup_modal__=false" style="float:left;" >
				<p>&nbsp;</p>
			</div>
			<template v-else-if="simple_popup_type__=='hh'" >
				<div v-html="simple_popup_data__" ></div>
			</template>
			<template v-else-if="simple_popup_type__=='d'" >
				<vdt v-bind:v="simple_popup_data__" v-bind:datafor="simple_popup_for__" v-bind:datavar="simple_popup_datavar__" v-on:close="simple_popup_modal__=false"  v-on:update="simple_popup_set_value__($event)" ></vdt>
			</template>
			<template v-else-if="simple_popup_type__=='dt'" >
				<vdtm v-bind:v="simple_popup_data__" v-bind:datafor="simple_popup_for__" v-bind:datavar="simple_popup_datavar__" v-on:close="simple_popup_modal__=false"  v-on:update="simple_popup_set_value__($event)"></vdtm>
			</template>
			<template v-else-if="simple_popup_type__=='ts'" >
				<vts v-bind:v="simple_popup_data__" v-bind:datafor="simple_popup_for__" v-bind:datavar="simple_popup_datavar__" v-on:close="simple_popup_modal__=false"  v-on:update="simple_popup_set_value__($event)"></vts>
			</template>
			<div v-else>
				<div>context editor</div>
				<div>{{ simple_popup_type__ }}</div>
				<pre>{{ simple_popup_data__ }}</pre>
			</div>
		</div>
	</div>

</div>


<script>
<?php
$components = [
	"graph_object2","graph_object_new",
	"inputtextbox", "inputtextbox2", "inputtextview", 
	"varselect", "varselect2", 
	"vobject", "vobject2", "vlist", 
	"vfield", "vdt", "vdtm", "vts", 
];
foreach( $components as $i=>$j ){
	require($apps_folder."/" . $j . ".js");
}
?>

const HomeView = {
	data: function(){
		return {};
	},
	mounted: function(){
		console.log("OKOKK");
	},
	template: `<div>OK</div>`
}

const HomeView2 = {
	data: function(){
		return {
			"tab": "home",
			"browse_list": [],
			"browse_from": "", 
			"browse_last": "", 
			"browse_sort": "label", 
			"browse_order": "asc",
		}
	},
	props:[ "things", ],
	mounted: function(){
		console.log("OKOK");
	},
	methods: {
		show_thing: function(vi){
			this.$root.show_thing(vi);
			this.msg = "loading thing";
		},
		open_tab_home: function(){
			this.tab = 'home';
		},
		open_tab_browse: function(){
			this.tab = 'browse';
			this.load_browse_list();
		},
		load_browse_list: function(){
			this.browse_list = [];
			this.browse_last = "";
			this.load_browse_list2();
		},
		load_browse_list_next: function(){
			if( this.browse_sort == 'label' ){
				this.browse_last = this.browse_list[ this.browse_list.length-1 ]['l']['v'];
			}else if( this.browse_sort == 'ID' ){
				this.browse_last = this.browse_list[ this.browse_list.length-1 ]['_id'];
			}else{
				this.browse_last = '';
			}
			this.load_browse_list2();
		},
		load_browse_list2: function(){
			this.bmsg = "loading list";
			var cond = {
				"action": "objects_load_browse_list",
				"sort": this.browse_sort, "order": this.browse_order,
			};
			if( this.browse_from.trim() != "" ){
				cond['from'] = this.browse_from;
			}
			if( this.browse_last.trim() != "" ){
				cond['last'] = this.browse_last;
			}
			axios.post("?",cond).then(response=>{
				this.bmsg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.browse_list = response.data['data'];
							}else{
								this.berr = response.data['error'];
							}
						}else{
							this.berr = "Incorrect response";
						}
					}else{
						this.berr = "Incorrect response";
					}
				}else{
					this.berr = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.berr = this.get_http_error__(error);
			});
		},
		getlink: function(vi){
			//return this.$root.objectpath+'?object_id='+vi;
			this.$root.load_new_thing(vi);
		},
		getlink2: function(vi){
			//return this.$root.objectpath+'?object_id='+vi;
			this.$root.load_new_thing(vi);
			setTimeout(this.$root.thing_open_records,1000);
		},
	},
	template: `<ul class="nav nav-tabs">
			<li class="nav-item">
				<a v-bind:class="{'nav-link py-0':true, 'active':tab=='home'}" href="#" v-on:click.prevent.stop="open_tab_home()">Summary</a>
			</li>
			<li class="nav-item">
				<a v-bind:class="{'nav-link py-0':true, 'active':tab=='browse'}"  href="#" v-on:click.prevent.stop="open_tab_browse()">Browse</a>
			</li>
		</ul>
		<div>&nbsp;</div>
		<template v-if="tab=='home'" >
			<div v-for="v in things" >
				<div type="button" class="btn btn-link btn-sm" v-on:click="show_thing(v['i'])" >{{ v['l']['v'] }}</div> <span class="text-secondary">({{ v['i'] }})</span> in 
				<div type="button" class="btn btn-link btn-sm" v-on:click="show_thing(v['i_of']['i'])">{{ v['i_of']['v'] }}</div>
			</div>
		</template>
		<template v-else-if="tab=='browse'" >
			
			<div style="height:40px; display:flex; column-gap:20px; padding:5px; border:1px solid #ccc;" >
				<div>
					<div style="display:flex; column-gap:10px;" >
						<div>Sort:</div>
						<div style="display: flex; column-gap:5px;">
							<select v-model="browse_sort" class="form-select form-select-sm" v-on:change="browse_from=''" >
								<option value="label" >Label</option>
								<option value="ID" >ID</option>
								<option value="nodes" >Nodes</option>
							</select>
							<select v-model="browse_order" class="form-select form-select-sm">
								<option value="asc" >Asc</option>
								<option value="dsc" >Desc</option>
							</select>
						</div>
					</div>
				</div>
				<div>
					<div v-if="browse_sort=='label'" style="display:flex; column-gap:10px;" >
						<div>From Label:</div>
						<div><input type="text" class="form-control form-control-sm" v-model="browse_from" ></div>
					</div>
					<div v-if="browse_sort=='ID'" style="display:flex; column-gap:10px;" >
						<div>From ID:</div>
						<div><input type="text" class="form-control form-control-sm" v-model="browse_from" ></div>
					</div>
				</div>
				<div>
					<input type="button" class="btn btn-outline-dark btn-sm" value="Search" v-on:click="load_browse_list()" >
				</div>
				<div>
					<input v-if="browse_list.length>0" type="button" class="btn btn-outline-dark btn-sm" value="Next" v-on:click="load_browse_list_next()" >
				</div>
			</div>

			<table class="table table-bordered table-sm w-auto" >
				<thead class="bg-light" style="position:sticky; top:0px;">
				<tr>
					<th>_id</th>
					<th>Label</th>
					<th>Instance Of</th>
					<th>Nodes</th>
					<th>UpdatedOn</th>
				</tr>
				</thead>
				<tbody>
				<tr v-for="rec,reci in browse_list">
					<td><div class="zz" ><a href="#" v-on:click.prevent.stop="getlink(rec['_id'])" >{{ rec['_id'] }}</a></div></td>
					<td><div class="zz" >{{ rec['l']['v'] }}</div></td>
					<td><div class="zz" ><a href="#" v-on:click.prevent.stop="getlink(rec['i_of']['i'])" >{{ rec['i_of']['v'] }}</a></div></td>
					<td><a v-if="'cnt' in rec" href="#" v-on:click.prevent.stop="getlink2(rec['_id'])" >{{ rec['cnt'] }}</a></td>
					<td><span v-if="'m_u' in rec" >{{ rec['m_u'].substr(0,10) }}</span></td>
				</tr>
				</tbody>
			</table>
		</template>`
};


var app_d = {
	"data": function(){
		return {
			"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			"objectpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/",
			"app_id" : "<?=$app['_id'] ?>",
			"context_api_url__": "?",
			"smsg": "", "serr":"","msg": "", "err":"","cmsg": "", "cerr":"","kmsg": "", "kerr":"", "bmsg": "", "berr":"",
			"keyword": "",
			"token": "",
			"saved": <?=($saved?"true":"false") ?>,
			"keys": [], "settings_popup": false, "create_popup": false, "create_popup_displayed__": false,
			"things": [],
			"thing": {}, "thing_save_need": false, "thing_loading": false, "thing_save_msg": "", "thing_save_err": "",
			"search_thing": {'t':'GT','v':"Search Thing",'i':""},
			"records": [],
			"tab": "home",
			"temp": {
				"new_field": {"t":"T", "v":""},
				"new_type": {"t":"KV", "k":"T", "v":"Text"},
			},
			"test": {"t":"GT", "v": "Search", "i":"ddddd"},
			"show_key": {},
			"thing_id": -1, 
			"new_thing": {
				"l": {"t":"T", "v":"testing"},
				"i_of": {"t":"GT", "i":"", "v":""}
			},
			"convert_to_link_temp": {"link": {"t": "GT", "i": "", "v": "Click to search"}, "label": {"t":"T", "v": ""} },
			"edit_z_t": {}, 
			"is_locked__"			: false,
			"all_factors__"			: {},
			"show_saving__"			: false,
			"save_message__"		: "Saving..",
			"save_need__"			: false,
			"first_save__"			: false,
			"data_types__"		: {
				"T": "Text",
				"TT": "MultiLineText",
				"HT": "HTMLText",
				"N": "Number",
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
				"GT": "Object Thing",
				"L": "List",
				"O": "Assoc List",
				"B": "Boolean",
				"NL": "Null", 
				"BIN": "Binary",
				"B64": "Base64",
			},
			"data_types1__"		: {
				"T": "Text",
				"N": "Number",
				"B": "Boolean",
				"NL": "Null", 
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
			},
			"data_types2__"		: {
				"GT": "Object Thing",
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
			context_callback_function__: "",
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
			context_thing_graph_list__: [],
			context_thing_list_keys__: {},
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
			simple_popup_title__: "",
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
		this.load_nodes();
	},
	watch: {

	},
	methods: {
		convert_to_link( el, vdatavar ){
			var v = this.get_editable_value__({'data_var':vdatavar});
			if( v === false ){console.log("convert_to_link: datavar value false");return false;}
			this.convert_to_link_temp['link'] = {"t": "GT", "i": "", "v": "Click to search"};
			this.convert_to_link_temp['label'] = {"t":"T", "v": v['v']+''};
			this.simple_popup_el__ = el;
			this.simple_popup_datavar__ = vdatavar;
			this.simple_popup_data__ = v;
			this.simple_popup_type__ = "convert_to_link";
			this.simple_popup_modal__ = true;
			this.simple_popup_title__ = "Convert to Link";
			//this.show_and_focus_context_menu__();
			this.set_simple_popup_style__();
		},
		convert_to_link_do: function(){
			this.simple_popup_modal__ = false;
			var v = this.convert_to_link_temp['label']['v']+'';
			if( v=="" ){
				v = this.convert_to_link_temp['link']['v']+'';
			}
			this.set_sub_var__(this, this.simple_popup_datavar__, {
				"t": "GT", 
				"i": this.convert_to_link_temp['link']['i']+'',
				"v": v
			});
		},
		getlink: function(vi){
			//return this.$root.objectpath+'?object_id='+vi;
			this.load_new_thing(vi);
		},
		getlink2: function(vi){
			//return this.$root.objectpath+'?object_id='+vi;
			this.load_new_thing(vi);
			setTimeout(this.thing_open_records,1000);
		},
		thing_open_records: function(){
			console.log( this.$refs );
			this.$refs['thing_object'].open_records();
		},
		
		save_thing: function(){
			this.thing_save_msg = "Saving thing";
			//this.echo__( this.thing );
			//this.echo__( this.template_to_json__(this.thing) );
			//return false;
			axios.post("?",{
				"action": "objects_save_object",
				"data": this.thing,
			}).then(response=>{
				this.thing_save_msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.thing_save_need = false;
								this.thing_save_msg = "Saved";
								setTimeout(function(v){v.this.thing_save_msg = "";},3000,this);
							}else{
								this.thing_save_err = response.data['error'];
							}
						}else{
							this.thing_save_err = "Incorrect response";
						}
					}else{
						this.thing_save_err = "Incorrect response";
					}
				}else{
					this.thing_save_err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.thing_save_err = this.get_http_error__(error);
			});
		},
		load_new_thing: function(vi){
			this.thing = {};
			this.records = {};
			this.show_thing( vi );
		},
		show_thing: function(vi){
			this.thing_id = vi;
			this.msg = "loading thing";
			this.thing_loading = true;
			this.thing = {};this.records = [];
			axios.post("?",{
				"action": "objects_load_object",
				"object_id": vi
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								var v = response.data['data'];
								this.thing = v;
								//setTimeout(function(v){v.load_records();},500,this);
								setTimeout(function(v){v.thing_loading = false;},200,this);
								this.thing_save_need = false;
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response";
					}
				}else{
					this.err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.err = this.get_http_error__(error);
			});
		},
		load_records: function(){
			axios.post("?",{
				"action": "objects_load_records",
				"object_id": this.thing['_id'],
			}).then(response=>{
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.records = response.data['data'];
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response";
					}
				}else{
					this.err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.err = this.get_http_error__(error);
			});
		},
		load_nodes: function(){
			this.err = "";this.msg = "";
			axios.post("?",{
				"action": "objects_load_basic",
			}).then(response=>{
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.things = response.data['data'];
							}else{
								this.err = response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response";
					}
				}else{
					this.err = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.err = this.get_http_error__(error);
			});
		},
		create_new_thing: function(){
			this.cerr = "";this.cmsg = "";
			if( this.new_thing['l']['v'] == "" ){
				this.cerr = "Need label";return false;
			}else if( this.new_thing['l']['v'].match(/^[a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}$/i) == null ){
				this.cerr = "Label should follow format [a-z0-9\-\_\,\.\@\%\&\*\(\)\+\=\?\"\'\ ]{2,100}";return false;
			}
			if( this.new_thing['i_of']['v'] == "" || this.new_thing['i_of']['i'] == "" ){
				this.cerr = "Need instance/tag";return false;
			}
			axios.post("?",{
				"action": "object_create_object",
				"data": this.new_thing
			}).then(response=>{
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.cmsg = "Successfully created: " + response.data['inserted_id'];
								setTimeout(function(vi,v){
									v.create_popup.hide();
									v.create_popup_displayed__ = false;
									alert("loading: " + vi );
									v.load_new_thing(vi);
								},1000,response.data['inserted_id'],this);
							}else{
								this.cerr = response.data['error'];
							}
						}else{
							this.cerr = "Incorrect response";
						}
					}else{
						this.cerr = "Incorrect response";
					}
				}else{
					this.cerr = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.cerr = this.get_http_error__(error);
			});
		},
		get_http_error__: function(e){
			if( typeof(e) == "object" ){
				if( 'status' in e ){
					if( 'error' in e ){
						return e['error'];
					}else{
						return "There was no error";
					}
				}else if( 'response' in e ){
					var s = e.response.status;
					if( typeof( e['response']['data'] ) == "object" ){
						if( 'error' in e['response']['data'] ){
							return s + ": " + e['response']['data']['error'];
						}else{
							return s + ": " + JSON.stringify(e['response']['data']).substr(0,100);
						}
					}else{
						return s + ": " + e['response']['data'].substr(0,100);
					}
				}else if( 'message' in e ){
					return e['message'];
				}else{
					return "Incorrect response";
				}
			}else{
				return "Invalid response"
			}
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
				//console.log("event_keyup__: "+e.target.getAttribute("data-type"));
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
				}else if( this.simple_popup_modal__ ){
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
							this.simple_popup_title__ = "Help";
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
					this.simple_popup_title__ = "Editable";
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
					if( el_data_type.hasAttribute("data-context-callback-function") ){
						this.context_callback_function__ = el_data_type.getAttribute("data-context-callback-function");
					}else{
						this.context_callback_function__ = "";
					}
					if( el_data_type.hasAttribute("data-list-filter") ){
						var tl = el_data_type.getAttribute("data-list-filter").split(/\,/g);
						this.context_list_filter__ = tl;
					}else{
						this.context_list_filter__ = [];
					}
					if( el_data_type.hasAttribute("data-thing-initial-keyword") ){
						var ik = el_data_type.getAttribute("data-thing-initial-keyword");
						if( ik.trim() != "" ){
							this.context_menu_key__ = ik;
						}
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
					if( this.context_type__ == "list-kv" ){
						if( el_data_type.hasAttribute("data-list-label") ){
							this.context_thing_label__ = el_data_type.getAttribute("data-list-label");
						}
						var ld = el_data_type.getAttribute("data-list-values");
						if( ld == 'datatypes-kv' ){
							this.context_list__ = [
								{"t":"KV", "k":"T", "v":"Text"},
								{"t":"KV", "k":"N", "v":"Number"},
								{"t":"KV", "k":"B", "v":"Boolean"},
								{"t":"KV", "k":"D", "v":"Date"},
								{"t":"KV", "k":"DT", "v":"DateTime"},
								{"t":"KV", "k":"TS", "v":"Timestamp"},
								{"t":"KV", "k":"O", "v":"Object"},
								{"t":"KV", "k":"L", "v":"List"},
								{"t":"KV", "k":"GT", "v":"Graph Node"},
								{"t":"KV", "k":"NL", "v":"Null"},
								{"t":"KV", "k":"TT", "v":"Text Multiline"},
								{"t":"KV", "k":"HT", "v":"HTML Text"}
							];
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
				}else if( this.simple_popup_modal__ ){
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
					console.log( " =====  " + v );
					v = v.replace(/[\u{0080}-\u{FFFF}]/gu, "");
					// v = v.replace(/\&nbsp\;/g, " ");
					// v = v.replace(/\&gt\;/g, ">");
					// v = v.replace(/\&lt\;/g, "<");
					var vv = this.v_filter__( v, e.target );
					//console.log( "==" + v + "== : ==" + vv + "==" );
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
		show_configure: function(){
			this.settings_popup = new bootstrap.Modal(document.getElementById('settings_modal'));
			this.settings_popup.show();
		},
		show_create: function(){
			this.cmsg = ""; this.cerr= "";
			if( this.thing_id != -1 ){
				this.new_thing = {
					"l": {"t":"T", "v":"New thing"},
					"i_of": {"t":"GT", "i":this.thing['i_of']['i']+'', "v":this.thing['i_of']['v']+''}
				};
			}else{
				this.new_thing = {
					"l": {"t":"T", "v":"testing"},
					"i_of": {"t":"GT", "i":"", "v":""}
				};
			}
			if( this.create_popup == false ){
				this.create_popup = new bootstrap.Modal(document.getElementById('create_popup'));
				document.getElementById('create_popup').addEventListener('hide.bs.modal', event => {
					this.create_popup_displayed__ = false;
				});
			}
			this.create_popup.show();
			this.create_popup_displayed__ = true;
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
		context_menu_key_edit__: function(){
			if( this.context_type__ == "graph-thing" ){
				setTimeout( this.context_thing_filter_final__, 100 );
				if( this.context_menu_key__ != '' ){
					setTimeout( this.context_thing_list_load_check__, 200 );
				}
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
		context_menu_thing_highlight_graph_thing__: function(v){

			var r = new RegExp( this.context_menu_key__ , "i" );
			var c = v['l']['v'].match( r );
			return v['i'] + ": <b>" + v['l']['v'].replace( c, "<span>"+c+"</span>" ) + "</b> in <span class='text-secondary'>" + v['i_of']['v'] + "</span>";
			// if( v['l']['v'] == v['l']['i'] ){
			// 	return v['v'].replace( c, "<span>"+c+"</span>" );
			// }else{
				
			// }
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
			if( t == "kv" ){
				this.set_sub_var__(this, this.context_datavar__, JSON.parse( JSON.stringify(k) ) );
			}else if( t == "datatype" ){
				this.set_sub_var__(this, this.context_datavar__, k );
				this.update_variable_type__(this, this.context_datavar__, k );
			}else if( t == "graph-thing" ){
				this.echo__( this.context_datavar__ );
				var x = this.context_datavar__.split(/\:/g);
				x.pop();
				var parent = x.join(":");
				this.echo__( parent );
				var d = {
					"t":"GT",
					"i":k['i']+'',
					"v":k['l']['v']+'',
				};
				this.set_sub_var__(this, parent, d );
			}
			if( this.context_callback__ ){
				var x = this.context_callback__.split(/\:/g);
				var vref = x.splice(0,1);
				if( vref in this.$refs ){
					if( "length" in this.$refs[ vref ] ){ this.$refs[ vref ][0].callback__(x.join(":")); }
					else{this.$refs[ vref ].callback__(x.join(":"));}
				}else{
					console.error("Ref: " + vref + ": not found");
				}
			}
			if( this.context_callback_function__ ){
				this[this.context_callback_function__]();
			}
			this.hide_context_menu__();
			setTimeout(this.updated_option__,100);
		},
		goto1: function(){
			this.echo__(this.search_thing);
			this.load_new_thing( this.search_thing['i'] );
		},
		simple_popup_set_value__: function(k){
			// var x = this.simple_popup_datavar__.split(/\:/g);
			// x.pop();
			// var parent = x.join(":");
			this.set_sub_var__( this, this.simple_popup_datavar__, k );
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
			//this.echo__("get_sub_var__: " + vpath);
			//this.echo__( vv );
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
			setTimeout(function(){try{document.getElementById("contextmenu_key1").focus();}catch(e){}},500);
			this.context_menu__ = true;
			if( this.popup_modal_displayed__ ){
				document.getElementById("popup_modal_body__").appendChild( document.getElementById("context_menu__") );
			}else if( this.create_popup_displayed__ ){
				document.getElementById("create_popup_body").appendChild( document.getElementById("context_menu__") );
			}else if( this.simple_popup_modal__ ){
				document.getElementById("simple_popup_modal__").appendChild( document.getElementById("context_menu__") );
			}
			this.context_expand_key__ = '';
		},
		set_context_menu_style__: function(){
			var s = this.context_el__.getBoundingClientRect();
			//this.finx_zindex(this.context_el__);
			if( this.popup_modal_displayed__ ){
				var s2 = document.getElementById("popup_modal_body__").getBoundingClientRect();
				this.context_style__ = "display:block;top: "+(Number(s.top)-Number(s2.top))+"px;left: "+(Number(s.left)-Number(s2.left))+"px;";
			}else if( this.create_popup_displayed__ ){
				var s2 = document.getElementById("create_popup_body").getBoundingClientRect();
				this.context_style__ = "display:block;top: "+(Number(s.top)-Number(s2.top))+"px;left: "+(Number(s.left)-Number(s2.left))+"px;";
			}else if( this.simple_popup_modal__ ){
				var s2 = document.getElementById("simple_popup_modal__").getBoundingClientRect();
				this.context_style__ = "display:block;top: "+(Number(s.top)-Number(s2.top))+"px;left: "+(Number(s.left)-Number(s2.left))+"px;";
				console.log( this.context_style__ );
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
		derive_value__: function( v ){
			if( v['t'] == "T" || v['t']== "D" ){
				return v['v'];
			}else if( v['t']== "N" ){
				return Number(v['v']);
			}else if( v['t'] == 'O' ){
				return this.template_to_json__(v['v']);
			}else if( v['t'] == 'L' ){
				return this.template_list_to_json__(v['v']);
			}else if( v['t'] == 'B' ){
				return (v['v']?true:false);
			}else if( v['t'] == 'D' ){
				return v['v'];
			}else if( v['t'] == 'DT' ){
				return v['v'] + " " + v['tz'];
			}else if( v['t'] == 'TS' ){
				return Number(v['v']);
			}else if( v['t'] == 'GT' ){
				//return "["+v['v']['th']['i']['v']+"::"+v['v']['th']['l']['v']+"]";
				return this.template_to_json__(v['v']);
			}else if( v['t'] == 'TH' ){
				return "Unknown TH";
			}else{
				return "unknown";
			}
		},
		template_to_json_derive_value__: function( v ){
			if( v['t'] == "T" ){
				return v['v'];
			}else if( v['t']== "N" ){
				return Number(v['v']);
			}else if( v['t'] == 'O' ){
				return this.template_to_json__(v['v']);
			}else if( v['t'] == 'L' ){
				return this.template_list_to_json__(v['v']);
			}else if( v['t'] == 'B' ){
				return (v['v']?true:false);
			}else if( v['t'] == 'D' ){
				return v['v'];
			}else if( v['t'] == 'DT' ){
				return v['v'] + " " + v['tz'];
			}else if( v['t'] == 'TS' ){
				return Number(v['v']);
			}else if( v['t'] == 'GT' ){
				//return "["+v['v']['th']['i']['v']+"::"+v['v']['th']['l']['v']+"]";
				return this.template_to_json__(v['v']);
			}else if( v['t'] == 'TH' ){
				return "Unknown TH";
			}else{
				return "unknown";
			}
		},
		template_to_json__( v ){
			var vv = {};
			if( typeof(v)==null ){
				console.error("get_object_notation: null ");
			}else if( typeof(v)=="object" ){
				if( "length" in v == false ){
					for(var k in v ){
						vv[ k ] = this.template_to_json_derive_value__(v[k]);
					}
				}else{ console.error("get_object_notation: got list instead of object "); this.echo__(v); }
			}else{ console.error("get_object_notation: incorrect type: "+ typeof(v) ); }
			return Object.fromEntries(Object.entries(vv).sort());
		},
		template_list_to_json__( v ){
			var vv = [];
			if( typeof(v)=="object" ){
				if( "length" in v ){
					for(var k=0;k<v.length;k++ ){
						vv.push( this.template_to_json_derive_value__(v[k]) );
					}
				}else{ console.error("get_list_notation: not a list "); }
			}else{ console.error("get_list_notation: incorrect type: "+ typeof(v) ); }
			return vv;
		},
		context_thing_list_load_check__: function(){
			if( this.context_thing__ in this.context_thing_list__ == false ){
				this.context_thing_list__[ this.context_thing__ ] = [];
				this.context_thing_list_keys__[ this.context_thing__ ] = {};
			}

			if( this.context_menu_key__ in this.context_thing_list_keys__[ this.context_thing__ ] ){
				return ;
			}
			// for( var k in this.context_thing_list_keys__[ this.context_thing__ ] ){
			// 	console.log( k + ":" + this.context_thing_list_keys__[ this.context_thing__ ][ k ] );
			// }
			//this.echo__( this.context_thing_list_keys__[ this.context_thing__ ] );
			var k  = this.context_menu_key__.substr(0,this.context_menu_key__.length-1);
			while( k.length > 1 ){
				//console.log( k );
				if( k in this.context_thing_list_keys__[ this.context_thing__ ] ){
					//this.echo__(k + " results: " + this.context_thing_list_keys__[ this.context_thing__ ][ k ] );
					if( this.context_thing_list_keys__[ this.context_thing__ ][ k ] < 100 ){
						this.echo__("cancel search as " + k + " results are below 100: " + this.context_thing_list_keys__[ this.context_thing__ ][ k ] );
						return;
					}
				}
				var k  = k.substr(0,k.length-1);
			}
			//if( this.context_thing_list__[ this.context_thing__ ].length == 0 )
			{
				this.context_thing_msg__ = "Loading...";
				this.context_thing_err__ = "";
				if( this.context_type__ != "graph-thing" ){
					this.context_thing_list__[ this.context_thing__ ] = [];
					this.context_thing_list_keys__[ this.context_thing__ ] = {};
				}
				var cond = {
					"action": "context_load_things",
					"app_id": "<?=$config_param1 ?>",
					"thing": this.context_thing__,
					"depend": this.context_dependency__,
				};
				if( this.context_type__ == "graph-thing" ){
					if( this.context_menu_key__ != '' ){
						cond['keyword'] = this.context_menu_key__+'';
					}
				}
				axios.post(this.context_api_url__, cond).then(response=>{
					this.context_thing_msg__ = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									if( response.data['things'] == null ){
										alert("Error context list");
									}else if( typeof(response.data['things']) == "object" ){
										if( this.context_type__ == "graph-thing" ){
											var v = response.data['things'];
											var v2 = JSON.parse(JSON.stringify(this.context_thing_list__[ this.context_thing__ ]));
											//console.log( "current length: " + v2.length );
											if( v2.length > 1000 ){
												v2 = [];
											}
											for(var i=0;i<v.length;i++){
												var f = false;
												for(var j=0;j<v2.length;j++){
													if( v2[j]['i'] == v[i]['i'] ){
														f = true;break;
													}
												}
												if( f == false ){
													v2.push( v[i] );
												}
											}
											//console.log( "new length: " + v2.length );
											for(var i=0;i<v2.length;i++){
												for(var j=0;j<v2.length-1;j++){
													if( v2[j]['l']['v'] > v2[j+1]['l']['v'] ){
														var t = v2.splice(j,1);
														v2.splice(j+1,0,t[0]);
														// v2[i] = v2[i+1];
														// v2[i+1] = t;
													}
												}
											}
											//this.echo__( v2 );
											this.context_thing_list__[ this.context_thing__ ] = v2;
											this.context_thing_list_keys__[ this.context_thing__ ][ response.data['keyword'] ] = v.length;
											setTimeout(this.context_thing_filter_final__, 100);
										}else{
											this.context_thing_list__[ this.context_thing__ ] = response.data['things'];
										}
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
					this.context_thing_err__ = "Error Loading: " + error.message;
					console.log( error.message );
				});
			}
		},
		context_thing_filter_final__: function(){
			if( this.context_thing__ in this.context_thing_list__ ){
				var v2 = this.context_thing_list__[ this.context_thing__ ];
				if( this.context_menu_key__ == "" ){
					var v3 = v2.slice(0,100);
					for(var i=0;i<v3.length;i++){
						v3[i]['r'] = "<b>" + v3[i]['l']['v'] + "</b> in <span class='text-secondary'>" + v3[i]['i_of']['v'] + "</span> [" + v3[i]['i'] + "]";
					}
					this.context_thing_graph_list__ = v3;
				}else{
					var vkey = this.context_menu_key__+'';
					var w = vkey.split(/\W+/g);
					//this.echo__( w );
					w.reverse();
					var key2 = w.join(".*");
					var k = vkey.trim().replace(/\W+/g, ".*");
					var kpr = new RegExp("^"+k,"i");
					var v3 = [];
					var vkeys = {};
					var k = this.context_menu_key__.toLowerCase();
					for(var i=0;i<v2.length&&v3.length<100;i++){
						//console.log( v2[i]['l']['v'] );
						if( v2[i]['l']['v'].match(kpr) ){
							//console.log( "Matched1" );
							var vres = v2[i];
							vres['r'] = vres['l']['v']+'';
							//console.log( "1: " + vres['r'] );
							for(var wi=0;wi<w.length;wi++){
								var rg = new RegExp( w[wi], "i" );
								//console.log( rg );
								var rgm = vres['r'].match(rg);
								//console.log( rgm );
								if( rgm ){
									vres['r'] = vres['r'].replace(rgm[0], "zzzz"+rgm+"-zzzz");
									//console.log( rgm[0] );
									//console.log( "Matched: " + vres['r'] );
								}
							}
							vres['r'] = vres['r'].replace( /\-zzzz/g, "</span>" );
							vres['r'] = vres['r'].replace( /zzzz/g, "<span class='text-danger'>" );
							vres['r'] = "<b>" + vres['r'] + "</b> in <span class='text-secondary'>" + vres['i_of']['v'] + "</span> ["+ vres['i'] + "]";
							v3.push( vres );
							vkeys[ v2[i]['i'] ] = 1;
						}
					}
					if( v3.length < 100 ){
						var kpr = new RegExp(k,"i");
						var kpr2 = new RegExp(key2,"i");
						for(var i=0;i<v2.length&&v3.length<100;i++){
							//console.log( v2[i]['l']['v'] );
							if( v2[i]['l']['v'].match(kpr) || v2[i]['l']['v'].match(kpr2) ){
								//console.log( "Matched 2" );
								if( v2[i]['i'] in vkeys == false ){
									var vres = v2[i];
									vres['r'] = vres['l']['v']+'';
									//console.log( "2: " + vres['r'] );
									for(var wi=0;wi<w.length;wi++){
										var rg = new RegExp( w[wi], "i" );
										var rgm = vres['r'].match(rg);
										if( rgm ){vres['r'] = vres['r'].replace(rgm[0], "zzzz"+rgm+"-zzzz");}
									}
									vres['r'] = vres['r'].replace( /\-zzzz/g, "</span>" );
									vres['r'] = vres['r'].replace( /zzzz/g, "<span class='text-danger'>" );
									vres['r'] = "<b>" + vres['r'] + "</b> in <span class='text-secondary'>" + vres['i_of']['v'] + "</span> [" + vres['i'] + "]";
									v3.push( vres );
								}else{
									//console.log( "But Skipped ");
								}
							}
						}
					}
					this.context_thing_graph_list__ = v3;
					//this.echo__( v3 );
				}
			}else{
				this.context_thing_graph_list__ = [];
			}
		},
		json_to_template__: function( v ){
			if( typeof(v) == "object" ){
				if( "length" in v == false ){
					for( var key in v ){
						if( v[ key ] == null ){
							v[ key ] = {"k": key, "t":"NL", "v": null };
						}else if( typeof(v[key]) == "object" && v[key] != null ){
							if( "length" in v[ key ] ){
								v[ key ] = {"k": key, "t":"L", "v": this.json_to_template__( v[key] ) };
							}else{
								if( "v" in v[key] && "i" in v[key] ){
									var s = this.json_to_template__( v[key] );
									s['k']=key; s['t']="GT";
									v[ key ] = s;
								}else{
									v[ key ] = {"k": key, "t":"O", "v": this.json_to_template__( v[key] ) };
								}
							}
						}else if( typeof(v[key]) == "string" ){
							if( v[ key ].match(/^[0-9]{4}\-/i) ){
								if( v[ key ].match(/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/i) ){
									v[ key ] = {"k": key, "t":"D", "v": v[key] };
								}else if( v[ key ].match(/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}/i) ){
									v[ key ] = {"k": key, "t":"DT", "v": v[key] };
								}else{
									v[ key ] = {"k": key, "t":"T", "v": v[key] };
								}
							}else{
								v[ key ] = {"k": key, "t":"T", "v": v[key] };
							}
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
								v[ key ] = {"t":"L", "v": this.json_to_template__( v[key] ) };
							}else{
								if( "v" in v[key] && "i" in v[key] ){
									var s = this.json_to_template__( v[key] );
									s['k']=key; s['t']="GT";
									v[ key ] = s;
								}else{
									v[ key ] = {"k": key, "t":"O", "v": this.json_to_template__( v[key] ) };
								}
							}
						}else if( typeof(v[key]) == "string" ){
							if( v[ key ].match(/^[0-9]{4}\-/i) ){
								if( v[ key ].match(/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/i) ){
									v[ key ] = {"k": key, "t":"D", "v": v[key] };
								}else if( v[ key ].match(/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}/i) ){
									v[ key ] = {"k": key, "t":"DT", "v": v[key] };
								}else{
									v[ key ] = {"k": key, "t":"T", "v": v[key] };
								}
							}else{
								v[ key ] = {"k": key, "t":"T", "v": v[key] };
							}
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
				console.log("json_to_template__: "+ typeof(v) + " Incorrect data type");
			}
			return v;
		},
		update_variable_type__: function(data, data_var, val){
			try{
				var x = data_var.split(/\:/g);
				if( x.length> 1 ){
					var new_Val = {"t":"T", "v":"Undefined"};
					x.pop();
					var data_var2 = x.join(":");
					if( val == "N" ){
						var s = this.get_sub_var__( data, data_var2);
						if( typeof(s['v'])=="string" ){
							if( s['v'].match(/^[0-9\.]+$/) ){
								new_val={"t":"N", "v":Number(['v'])};
							}else{
								new_val={"t":"N", "v":0};
							}
						}else{
							new_val={"t":"N", "v":0};
						}
					}else if( val == "T" || val == "TT" || val == "HT" ){
						new_val= {"t":val, "v": String(s['v'])};
					}else if( val == "L" ){
						new_val={"t":"L", "v":[{"t":"O", "v":{"one":{"k":"one", "t":"T","v":""}} }]};
					}else if( val == "O" ){
						new_val={"t":"O", "v":{"one":{"k":"one", "t":"T","v":""}}};
					}else if( val == "GT" ){
						new_val={"t":"GT", "v":"", "i":""};
					}else if( val == "NL" ){
						new_val={"t":"NL", "v":null};
					}else if( val == "D" ){
						new_val={"t":"D", "v":"<?=date("Y-m-d") ?>"};
					}else if( val == "DT" ){
						new_val={"t":"DT", "v":"<?=date("Y-m-d H:i:s") ?>", "tz":"UTC+00:00"};
					}else if( val == "TS" ){
						new_val={"t":"TS", "v":"<?=time() ?>"};
					}else if( val == "B" ){
						new_val={"t": "B", "v":true};
					}else{
						new_val={"t":"T", "v":"Unknown"};
					}
					this.set_sub_var__(data, data_var2, new_val );
				}
			}catch(e){
				console.error("update_engine_var_datatype__: " + data_var + ": " );
				this.echo__(val);
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

	}
};

var app = Vue.createApp(app_d);
<?php foreach( $components as $i=>$j ){ ?>
	app.component( "<?=$j ?>", <?=$j ?> );
<?php } ?>
app.component("HomeVue", HomeView);
app.component("graph_object2", graph_object2);
const routes = [
  //{ path: '', component: HomeView },
  { path: '/apimaker/apps/:appid/objects', component: HomeView },
  { path: '/apimaker/apps/:appid/objects/', component: HomeView },
  { path: '/apimaker/apps/:appid/objects/:thing_id', component: "graph_object2" },
];

const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(), 
  routes,
});

app.use(router);
var app1 = app.mount("#app");

</script>
