<?php 
require("page_apps_objects.css");
?>

<style>
div.zz, a.zz{ display: block; max-width:250px; max-height:150px; overflow:auto; white-space:nowrap; }
div.zz::-webkit-scrollbar {width: 6px;height: 6px;}
div.zz::-webkit-scrollbar-track { background: #f1f1f1;}
div.zz::-webkit-scrollbar-thumb { background: #888;}
div.zz::-webkit-scrollbar-thumb:hover { background: #555;}
a.zz::-webkit-scrollbar {width: 6px;height: 6px;}
a.zz::-webkit-scrollbar-track { background: #f1f1f1;}
a.zz::-webkit-scrollbar-thumb { background: #888;}
a.zz::-webkit-scrollbar-thumb:hover { background: #555;}

table.zz td div{ max-width:250px; max-height:75px;overflow:auto; white-space:nowrap; }
table.zz thead td { background-color:#666; color:white; }

div.zz::-webkit-scrollbar {width: 6px;height: 6px;}
div.zz::-webkit-scrollbar-track { background: #f1f1f1;}
div.zz::-webkit-scrollbar-thumb { background: #888;}
div.zz::-webkit-scrollbar-thumb:hover { background: #555;}

pre.zzz{ max-height:150px; width:auto;overflow:auto; margin:20px 10px; padding:10px; border:1px solid #999; }
pre.zzz::-webkit-scrollbar {width: 12px;height: 12px;}
pre.zzz::-webkit-scrollbar-track { background: #f1f1f1;}
pre.zzz::-webkit-scrollbar-thumb { background: #888;}
pre.zzz::-webkit-scrollbar-thumb:hover { background: #555;}

pre.fff{ max-height:300px; width:auto;overflow:auto; padding:10px; margin-right:20px; border:1px solid #999; }
pre.fff::-webkit-scrollbar {width: 12px;height: 12px;}
pre.fff::-webkit-scrollbar-track { background: #f1f1f1;}
pre.fff::-webkit-scrollbar-thumb { background: #888;}
pre.fff::-webkit-scrollbar-thumb:hover { background: #555;}

pre.sample_data{ height:300px;overflow:auto; white-space:nowrap; border:1px solid #333; }
pre.sample_data::-webkit-scrollbar {width: 6px;height: 6px;}
pre.sample_data::-webkit-scrollbar-track { background: #f1f1f1;}
pre.sample_data::-webkit-scrollbar-thumb { background: #888;}
pre.sample_data::-webkit-scrollbar-thumb:hover { background: #555;}

.graph_tabs_nav_bar{ position:relative; height:30px; border-bottom:2px solid #aaa; margin-bottom:10px; }
.graph_tabs_nav_container{ position:absolute; display:flex; height:30px; width:calc( 100% - 30px ); overflow:hidden; }
.graph_tabs_nav_scrollbtn{ position:absolute; right:0px; height:30px; z-index:5; background-color:#f8f0f0; display:none; }
.graph_tabs_nav_scrollbtn2{ position:absolute; left:0px; height:30px; z-index:5; background-color:#f8f0f0; display:none; }
.graph_tab_btn{ display: flex; column-gap:10px; margin-left:5px; padding:0px 10px; border:1px solid #ccc;border-top-left-radius:5px;border-top-right-radius:5px; border-bottom:2px solid #aaa; white-space:nowrap; cursor:pointer; align-items:center; }
.graph_tab_btn .head{}
.graph_tab_btn .cbtn{}
.graph_tab_btn:hover{background-color:#f8f8f8;}
.graph_btn_active{border:1px solid #999; border-bottom:2px solid white; }
.graph_tab_btn:hover .graph_btn_active{ border-bottom:2px solid white; }
.graph_tabs_container{ position:relative; height:calc( 100% - 150px ); background-color: white; overflow:auto;  padding-right:10px; }

.graph_object_tabs_nav_bar{ position:relative; height:30px; border-bottom:2px solid #aaa; }
.graph_object_tabs_nav_container{ position:absolute; display:flex; height:30px; width:calc( 100% - 30px ); overflow:hidden; }
.graph_object_tabs_nav_scrollbtn{ position:absolute; right:0px; height:30px; z-index:5; background-color:#f8f0f0; }
.graph_object_tab_btn{ display: flex; column-gap:10px; margin-left:5px; padding:0px 10px; border:1px solid #ccc;border-top-left-radius:5px;border-top-right-radius:5px; border-bottom:2px solid #aaa; white-space:nowrap; cursor:pointer; align-items:center; }
.graph_object_tab_btn .head{}
.graph_object_tab_btn .cbtn{}
.graph_object_tab_btn:hover{background-color:#f8f8f8;}
.graph_object_btn_active{border:1px solid #999; border-bottom:2px solid white; }
.graph_object_tab_btn:hover .graph_btn_active{ border-bottom:2px solid white; }
.graph_object_tabs_container{ position:relative; height:calc( 100% - 150px ); background-color: white; overflow:auto;  padding-right:10px; }

div.objecticon{ cursor: pointer; text-align:center; }
div.objecticon .objecticonsvg{ margin-top:3px; width:50px; }
div.objecticon .objecticonemoji{ font-size:1.2rem; }
div.objecticon .objecticonfont{ font-size:1.2rem; }
div.objecticon .objecticonflag{ width:10px; }
div.objecticon .objecticonflag img{ min-width:initial; min-height:initial; width:100%; height:initial; }
div.objecticon .objecticonimg{ width:100px; }
div.objecticon .objecticonimg img{ min-width:initial; min-height:initial; width:100%; height:initial; }

div.objecticoninline{ text-align:center; width:25px; height:25px; }
div.objecticoninline .objecticonsvg{ margin-top:3px; width:100%; }
div.objecticoninline .objecticonemoji{ font-size:1.2rem; }
div.objecticoninline .objecticonfont{ font-size:1.2rem; }
div.objecticoninline .objecticonflag{ width:10px; }
div.objecticoninline .objecticonflag img{ min-width:initial; min-height:initial; width:100%; height:initial; }
div.objecticoninline .objecticonimg{ }
div.objecticoninline .objecticonimg img{ min-width:initial; min-height:initial; width:120%; height:initial; }

</style>

<div id="app" v-cloak >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>
	<div style="position: fixed;left:150px; top:40px; width:calc( 100% - 150px ); height: calc( 100% - 50px ); background-color: white; " >
		<div style="padding: 10px;" >

			<a class="btn btn-sm btn-outline-dark float-end" v-bind:href="path+'objects'" >Back</a>
			<div class="btn btn-sm btn-outline-dark float-end me-2" v-on:click="open_settings__()" >Settings</div>
			<button class="btn btn-outline-dark btn-sm me-2 float-end" v-on:click="previewit" id="previewbtn" >
				<i class="fas fa-fas fa-link" ></i>
			</button>
			
			<div v-if="thing_id!=-1" class="btn btn-sm btn-outline-dark float-end me-2" v-on:click="thing_id=-1" >Home</div>

			<div class="h3 mb-3"><span class="text-secondary" >Object Store</span> <span>{{ db_name }}</span></div>

			<div style="margin-bottom:10px;">
				<div class="btn btn-sm btn-outline-dark float-end me-2" v-on:click="show_create()" >Create Node</div>
				<div class="btn btn-sm btn-outline-dark float-end me-2" v-on:click="router_open_newtab('import2')" >Import</div>
				<div class="btn btn-sm btn-outline-dark float-end me-2" v-on:click="router_open_newtab('ops')" >Ops</div>

				<div style="display:flex; width:300px; column-gap:5px;border:1px solid #ccc;background-color: white; align-items: center; ">
					<div class="thing_search_bar" title="Thing" data-type="dropdown" data-var="search_thing:v" data-list="graph-thing" data-thing="GT-ALL" data-thing-label="Things" data-context-callback-function="goto1" >Search</div>
					<div><i class="fa fa-search"></i></div>
				</div>
			</div>

			<div class="graph_tabs_nav_bar">
				<div class="graph_tabs_nav_scrollbtn2" id="tabs_left_scrollbar" v-on:click="window_tabs_focus_left()"><div class="btn btn-light btn-sm" style="height:30px;" ><i class="fa-solid fa-chevron-left"></i></div></div>
				<div class="graph_tabs_nav_container" id="tabs_container">
					<div v-bind:class="{'graph_tab_btn':true,'graph_btn_active':(vtabi==current_tab)}" v-for="vtabi,i in window_tabs_order" v-bind:id="'tab_'+vtabi">
						<div v-on:click.prevent.stop="router_open_newtab(vtabi)">{{ window_tabs[ vtabi ]['title'] }}</div>
						<div v-if="vtabi!='home'&&vtabi!='summary'&&vtabi!='browse'&&vtabi!='import2'"><div class="btn btn-outline-danger btn-sm py-0 px-0" v-on:click.prevent.stop="window_close_tab(vtabi)" ><i class="fa-solid fa-xmark"></i></div></div>
					</div>
				</div>
				<div class="graph_tabs_nav_scrollbtn"  id="tabs_right_scrollbar"  v-on:click="window_tabs_focus_right()"><div class="btn btn-light btn-sm" style="height:30px;" ><i class="fa-solid fa-chevron-right"></i></div></div>
			</div>
			<template v-for="vtab,vtabi in window_tabs" >
				<div class="graph_tabs_container" v-bind:id="'tabs_container_'+vtabi" v-show="current_tab==vtabi" >
					<template v-if="vtabi=='summary'" >
						<div v-for="v in things" >
							<div type="button" class="btn btn-link btn-sm" v-on:click="show_thing(v['i'])" >{{ v['l']['v'] }}</div> in 
							<div type="button" class="btn btn-link btn-sm" v-on:click="show_thing(v['i_of']['i'])">{{ v['i_of']['v'] }}</div> ({{ v['cnt'] }})
						</div>
					</template>
					<template v-else-if="vtabi=='import2'" >
						<objects_import_v2 ref="import2" refname="import2" v-bind:vtab="window_tabs[vtabi]" ></objects_import_v2>
					</template>
					<template v-else-if="vtabi=='browse'" >

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

							<div style="position:relative;overflow: auto; height: calc( 100% - 100px );">
								<table class="table table-bordered table-sm w-auto" >
									<thead class="bg-light" style="position:sticky; top:0px;">
									<tr>
										<th>_id</th>
										<th>Label</th>
										<th>Type</th>
										<th>Instance Of</th>
										<th>Nodes</th>
										<th>UpdatedOn</th>
										<th>-</th>
									</tr>
									</thead>
									<tbody>
										<tr v-for="rec,reci in browse_list">
											<td><div class="zz" ><a href="#" v-on:click.prevent.stop="show_thing(rec['_id'])" >{{ rec['_id'] }}</a></div></td>
											<td><div style="display: flex; column-gap:10px;" >
												<div class="objecticoninline" v-if="'ic' in rec" ><icon_view v-bind:data="rec['ic']" ></icon_view> </div>
												<div class="zz" ><span v-if="'l' in rec" >{{ rec['l']['v'] }}</span></div>
											</div>
											</td>
											<td>{{ get_node_type(rec['i_t']['v']) }}</td>
											<td><div class="zz" ><a href="#" v-on:click.prevent.stop="show_thing(rec['i_of']['i'])" >{{ rec['i_of']['v'] }}</a></div></td>
											<td><a v-if="'cnt' in rec" href="#" v-on:click.prevent.stop="show_thing2(rec['_id'])" >{{ rec['cnt'] }}</a></td>
											<td><span v-if="'m_u' in rec" >{{ rec['m_u'].substr(0,10) }}</span></td>
											<td><div class="btn btn-light btn-sm py-1 text-danger"  v-on:click="browse_list_delete(reci)" ><i class="fa-regular fa-trash-can"></i></div></td>
										</tr>
									</tbody>
								</table>
							</div>

					</template>
					<object_template_create_v2 v-else-if="vtabi=='thingnew'" v-bind:ref="vtabi" v-bind:refname="vtabi" v-bind:data="vtab['data']" ></object_template_create_v2>
					<template v-else-if="vtabi=='ops'" >
						<object_ops v-bind:ref="vtabi" v-bind:refname="vtabi" v-bind:data="vtab['data']" ></object_ops>
					</template>
					<template v-else-if="vtab['type']=='thing'" >
						<graph_object_v2 v-bind:ref="vtabi" v-bind:refname="vtabi" v-bind:object_id="vtab['thing_id']" v-bind:data="vtab['data']" ></graph_object_v2>
					</template>
					<template v-else >
						<pre>{{ vtab }}</pre>
					</template>
				</div>
			</template>
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
		<template v-else-if="context_type__=='list-assoc'" >
			<div v-for="v,k in context_list__" class="context_item" v-on:click.stop="context_select__(k,'')" >{{ v }}</div>
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
			<div v-if="context_thing_allow_create__" style="float:right;" class="btn btn-outline-dark btn-sm py-0" v-on:click.prevent.stop="context_change_to_graph_create__()" >Create Node</div>
			<div>{{ context_thing_label__ }}</div>
			<div><input spellcheck="false" type="text" id="contextmenu_key1" data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="context_menu_key__" v-on:keyup="context_menu_key_edit__" ></div>
			<div class="context_menu_list__" data-context="contextmenu" >
				<div v-if="context_thing_msg__" class="text-success" >{{ context_thing_msg__ }}</div>
				<div v-if="context_thing_err__" class="text-danger" >{{ context_thing_err__ }}</div>
				<div v-for="fv,fi in context_thing_graph_list__" class="context_item" v-on:click.stop="context_select__(fv,context_type__)" v-html="fv['r']" ></div>
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
				<div v-else-if="popup_type__=='import_template_edit'" class="code_line" >
					<object_template_edit ref="objects_component_template" refname="objects_component_template"  v-bind:object_id="popup_data__['object_id']" ></object_template_edit>
				</div>
				<div v-else-if="popup_type__=='import_template_create'||popup_type__=='template_create'" class="code_line" >
					<object_template_create_v2 ref="create_popup_component" refname="create_popup_component" v-bind:data="popup_data__['data']" ></object_template_create_v2>
				</div>
				<div v-else-if="popup_type__=='dataset_edit_record'" >
					<object_dataset_edit_record ref="dataset_record_edit" refname="dataset_record_edit" v-bind:data="popup_data__" ></object_dataset_edit_record>
				</div>
				<div v-else-if="popup_type__=='dataset_create_record'" >
					<object_dataset_create_record ref="dataset_record_create" refname="dataset_record_create" v-bind:data="popup_data__" ></object_dataset_create_record>
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
			<template v-else-if="simple_popup_type__=='graph-create'" >
				<div style="display:flex; column-gap: 20px; margin-bottom:10px;" class="code_line">
					<div>
						<div>Instance</div>
						<div title="Thing" data-type="dropdown" data-var="create_node__:i_of:v" data-list="graph-thing" data-thing="GT-ALL" data-thing-label="Things" >{{ create_node__['i_of']['v'] }}</div>
					</div>
					<div>
						<div>Node</div>
						<div title="Text" class="editable" data-var="create_node__:l:v" ><div style="white-space:nowrap;" contenteditable spellcheck="false" data-type="editable" data-var="create_node__:l:v" id="create_node__:l:v" data-allow="T" >{{ create_node__['l']['v'] }}</div></div>
					</div>
				</div>
				<div style="margin-bottom: 10px;">
					<input type="button" class="btn btn-outline-secondary btn-sm me-2" v-on:click.prevent.stop="hide_simple_popup__()" value="Cancel" >
					<input type="button" class="btn btn-outline-dark btn-sm" v-on:click.prevent.stop="create_node_on_fly__" value="Create" >
				</div>
				<div v-if="create_node_msg__" style="color:blue;" >{{ create_node_msg__}}</div>
				<div v-if="create_node_err__" style="color:red;" >{{ create_node_err__}}</div>
				<p>&nbsp;-</p>
			</template>
			<div v-else>
				<div>context editor</div>
				<div>{{ simple_popup_type__ }}</div>
				<pre>{{ simple_popup_data__ }}</pre>
			</div>
		</div>
	</div>

	<div class="modal fade" id="settings_modal" tabindex="-1" >
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Settings</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" >
					<template v-if="'settings' in db" >
						<p>Image Library Settings</p>
						<p><label><input type="checkbox" v-model="db['settings']['library_enable']" v-on:click="library_enable_click()" > Enable Image Library </label></p>
						<template v-if="db['settings']['library_enable'] && 'library' in db['settings']" >
							<div>Storage Vault</div>
							<div class="mb-2"><select v-model="db['settings']['library']['vault_id']" class="form-select form-select-sm" v-on:change="select_storage_valut__()" >
								<option value="" >Select Vault</option>
								<option v-for="vv,vi in storage_vaults_list__" v-bind:value="vv['_id']" >{{ vv['vault_type'] + ': ' + vv['des'] }}</option>
							</select></div>
							<div>Allow Uploads</div>
							<div class="mb-2"><label><input type="checkbox" v-model="db['settings']['library']['upload']" > Allow Uploading </label></div>
							<div>Size Limit</div>
							<div class="mb-2"><input type="number" v-model="db['settings']['library']['size']" class="form-control form-control-sm w-auto" ></div>
							<div>Destination Path</div>
							<div class="mb-2"><input type="text" v-model="db['settings']['library']['dest_path']" class="form-control form-control-sm" placeholder="/image-path/" ></div>
							<div>Thumb Rewrite Path</div>
							<div class="mb-2">
								<input type="text" v-model="db['settings']['library']['thumb_path']" class="form-control form-control-sm" placeholder="/images-thumbs/" >
								<div class="text-secondary">/<b>thumb-rewrite-path</b>/thumb-settings/destination-path/filename.image</div>
							</div>
							<div>Images Node</div>
							<div class="mb-2">
								<input type="text" v-model="db['settings']['library']['thing_id']" class="form-control form-control-sm" placeholder="T1" >
								<div class="text-secondary">Graph Node where meta data of uploaded images are stored.</div>
							</div>
						</template>
						<div class="mb-2">
							<input type="button" class="btn btn-outline-dark btn-sm" value="UPDATE" v-on:click="save_settings_library()" >
							<div class="text-danger">{{ set_err }}</div>
							<div class="text-success">{{ set_msg }}</div>
						</div>
					</template>
					<div v-else>Settings not initialized</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="url_modal" tabindex="-1" >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Engine Environment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <!-- <pre>{{ test_environments__ }}</pre> -->
              <div v-for="v,i in test_environments__" class="mb-2" >
              	<label style="cursor: pointer;" v-on:click="test_environment_select__()" >
              		<input class="me-2" type="radio" v-model="current_test_environment_selection__" v-bind:value="i" > 
              		<div style="display:inline-block; width:100px; margin-right:20px; text-align: right;">{{ v['t'] }}</div>
              		<span> {{ v['u'] }} </span> 
              	</label>
              </div>
          </div>
        </div>
      </div>
    </div>

	<template v-for="vtab,vtabi in window_tabs" >
		<template v-if="vtab['type']=='thing'" >
			<template v-if="'data' in vtab" >
				<template v-if="'thing' in vtab['data']" >
					<template v-if="'body' in vtab['data']['thing']" >
						<editor_component v-if="'_id' in vtab['data']['thing']" v-bind:editor_div_id="'editor_div_'+vtab['data']['thing']['_id']" v-bind:editor_wrapper_div_id="'tabs_container_'+vtabi" v-bind:data="vtab['data']['thing']['body']" v-on:edited="thing_editor_updated(vtabi,$event)"></editor_component>
					</template>
				</template>
			</template>
		</template>
	</template>
	<div v-if="float_msg" style="position:fixed; z-index:500; color:blue; bottom:10px; right:10px; background-color: rgba(0,0,200,0.5); padding:10px;">{{ float_msg }}</div>
	<div v-if="float_err" style="position:fixed; z-index:500; color:red; bottom:10px; right:10px; background-color: rgba(200,0,0,0.5); padding:10px;">{{ float_red }}</div>

</div>

<?php require("page_apps_objects_import_v2.php"); ?>
<?php require("page_apps_objects_component_template.php"); ?>
<?php require("page_apps_objects_component_template_create_v2.php"); ?>
<?php require("page_apps_objects_component_graph_object_v2.php"); ?>
<?php require("page_apps_objects_component_dataset_create.php"); ?>
<?php require("page_apps_objects_component_dataset_edit.php"); ?>
<?php require("page_apps_objects_component_ops.php"); ?>

<script src="//<?=$html_editor_settings['editor_domain'] ?>/editor_enc.js"></script>
<link rel="stylesheet" href="//<?=$html_editor_settings['editor_domain'] ?>/fontawesome/css/all.min.css" defer async />
<link rel="stylesheet" href="//<?=$html_editor_settings['editor_domain'] ?>/RemixIcon/fonts/remixicon.css" defer async />

<script>
<?php
$components = [
	"inputtextbox", "inputtextbox2", "inputtextview", 
	"varselect", "varselect2", 
	"vobject", "vobject2", "vlist", 
	"vfield", "vdt", "vdtm", "vts"
];
foreach( $components as $i=>$j ){
	require($apps_folder."/" . $j . ".js");
}

?>

var app = Vue.createApp({
	"data": function(){
		return {
			"path": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
			"objectpath": "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/<?=$config_param3 ?>",
			"app_id" : "<?=$app['_id'] ?>",
			"db_id": "<?=$config_param3 ?>",
			"db_name": "<?=$graph['name'] ?>",
			"db": <?=json_encode($graph) ?>,
			"test_environments__": <?=json_encode($test_environments,JSON_PRETTY_PRINT) ?>,
			"current_test_environment__": <?=json_encode($test_environments[0],JSON_PRETTY_PRINT) ?>,
			"current_test_environment_selection__": 0,

			"icon_popup": false,
			"current_icon": {},
			"icon_settings": <?=json_encode($icon_settings) ?>,
			"image_library_settings": <?=json_encode($image_library_settings) ?>,
			"graph_settings": <?=json_encode($graph_settings) ?>,
			"editor_session_key": "",

			"storage_vaults_list__": [],
			"context_api_url__": "?",
			"smsg": "", "serr":"","msg": "", "err":"","cmsg": "", "cerr":"","kmsg": "", "kerr":"", "bmsg": "", "berr":"",
			"set_err": "", "set_msg": "",
			"keyword": "",
			"token": "",
			"keys": [], 
			"settings_popup": false, "confmsg": "", "conferr": "",
			"create_popup": false, "create_popup_displayed__": false,
			"things": [],
			"thing": {}, "thing_save_need": false, "thing_loading": false, "thing_save_msg": "", "thing_save_err": "",
			"browse_list": [],
			"browse_from": "", "browse_last": "", "browse_sort": "label", "browse_order": "asc", "browse_list_index":-1,
			"search_thing": {'t':'GT','v':"Search Thing",'i':""},
			"records": [],
			"tab": "home",
			"node_types": {"N": "Node", "L": "DataSet", "D":"Document", "M": "Media"},
			"temp": {
				"new_field": {"t":"T", "v":""},
				"new_type": {"t":"KV", "k":"T", "v":"Text"},
			},
			"test": {"t":"GT", "v": "Search", "i":"ddddd"},
			"show_key": {},
			"thing_id": -1, 
			"new_thing": {
				"l": {"t":"T", "v":"Node"},
				"i_of": {"t":"GT", "i":"", "v":""},
				"i_t": {"t":"T", "v":"N"}
			},
			"create_node__": {
				"l": {"t":"T", "v":""},
				"i_of": {"t":"GT", "i":"", "v":""},
			},
			"create_node_msg__": "","create_node_err__": "",
			"vtemplate": {
				"template": {},
				"edit_field": "",
			},
			"convert_to_link_temp": {
				"link": {"t": "GT", "i": "", "v": "Click to search"}, 
				"label": {"t":"T", "v": ""}
			},
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
			context_data__: {},
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
			context_thing_allow_create__: false,
			context_thing_list__: {},
			context_thing_list_basic__: [],
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

			current_tab: "summary",
			window_tabs: {
				"summary": {"title": "Summary",	"type": "summary",},
				"browse": {	"title": "Browse", "type": "browse",},
			},
			window_tabs_order: ["summary", "browse"],
			float_msg: "", float_err: "",
			toasts:[],
			auth_error_msg: false,
		};
	},
	mounted: function(){
		document.addEventListener("keyup", this.event_keyup__ );
		document.addEventListener("keydown", this.event_keydown__);
		document.addEventListener("click", this.event_click__, true);
		document.addEventListener("scroll", this.event_scroll__, true);
		document.addEventListener("blur", this.event_blur__, true);
		window.addEventListener("paste", this.event_paste__, true);

		if( 'settings' in this.db == false ){
			this.db['settings'] = {
				'library_enable':false,
			};
		}

		this.load_nodes();
		//setTimeout(this.load_sample_tabs,1000);
		this.image_library_settings['image_domain'] = this.current_test_environment__['d'];
		if( document.location.hash != "" ){
			setTimeout(function(v){v.show_thing( document.location.hash.replace("#", "") );},1000,this);
		}
	},
	watch: {

	},
	methods: {
		make_graph_url: function(){ // no underscores

		},
		get_access_token: function(){ //called from editor.js // no underscores
			console.log('step1');
			this.float_err = "";
			this.float_msg = "Loading...";
			var vpost = {
				"action":"get_editor_graph_key",
			};
			axios.post( '?', vpost, {
				"headers":{
					"Content-Type": "application/x-www-form-urlencoded"
				}
			}).then(response=>{
				this.float_msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.editor_session_key = response.data['session-key'];
							}else{
								this.float_err = "Token Failed: " + response.data['error'];
							}
						}else{
							this.float_err = "Token Failed: Incorrect response";
						}
					}else{
						this.float_err = "Token Failed: Incorrect response";
					}
				}else{
					this.float_err = "Token Failed: Incorrect response";
				}
			}).catch(error=>{
				this.float_err = "Token Failed: " + this.get_http_error__(error) ;
			});
		},
		test_environment_select__:function(){
			setTimeout(this.test_environment_select2__,100);
		},
		test_environment_select2__: function(){
			this.current_test_environment__ = JSON.parse( JSON.stringify( this.test_environments__[ this.current_test_environment_selection__ ] ) );
			this.image_library_settings['image_domain'] = this.current_test_environment__['d']+'';
		},
		previewit: function(){
			this.url_modal = new bootstrap.Modal(document.getElementById('url_modal'));
			this.url_modal.show();
		},
		open_settings__: function(){
			this.settings_popup = new bootstrap.Modal( document.getElementById('settings_modal') );
			this.settings_popup.show();
			setTimeout(this.load_storage_vaults__, 100);
		},
		load_storage_vaults__: function(){
			axios.get("?action=load_storage_vaults").then(response=>{
				this.storage_vaults_list__ = response.data['data'];
			}).catch(error=>{
				console.error( "Loadstorage vaults: " + error.message );
			});
		},
		select_storage_valut__: function(){
			if( 'settings' in this.db ){
				if( 'library' in this.db['settings'] ){
					if( this.db['settings']['library']['vault_id'] ){
						for(var i=0;i<this.storage_vaults_list__.length;i++){
							if( this.storage_vaults_list__[i]['_id'] == this.db['settings']['library']['vault_id'] ){
								this.db['settings']['library']['vault'] = JSON.parse(JSON.stringify(this.storage_vaults_list__[i]));
							}
						}
					}
				}
			}
		},
		library_enable_click: function(){
			setTimeout(this.library_enable_click2, 200);
		},
		library_enable_click2: function(){
			if( this.db['settings']['library_enable'] ){
				if( 'library' in this.db['settings'] == false ){
					this.db['settings']['library'] = {
						'vault_id': "",
						'vault': {},
						"upload": false,
						"size": 5,
						"dest_path": "/image-library-path/",
						"thumb_path": "/image-thumbs/",
						"thing_id": ""
					};
				}
			}
		},
		save_settings_library: function(){
			this.set_msg = "Saving...";
			this.set_err = "";
			axios.post("?", {
				"action": "save_settings_library",
				"settings": this.db['settings']
			}).then(response=>{
				this.set_msg = "";
				if( response.data['status'] == "success" ){
					this.set_msg = "Settings updated successfully";
					setTimeout("document.location.reload()",3000);
				}else{
					this.set_err = response.data['error'];
				}
			}).catch(error=>{
				this.set_err = "Error: " + this.get_http_error__(error);
			});
		},
		goto_required_path: function(){
        	console.log( "goto required path");
        	console.log( this.$route.path );
        	vpath = this.$route.path.substr(1,9999);
        	if( vpath == '' ){
        		vpath = 'summary';
        	}
        	var thing_id = "";
			var m = vpath.match(/^thing\/([a-z0-9]+)$/i);
			if( m ){
				thing_id = m[1];
				vpath = 'thing-'+thing_id;
			}
			if( vpath in this.window_tabs == false ){
				if( thing_id ){
					this.window_open_newtab( vpath, {"type":"thing", "thing_id": thing_id, "loaded":false} );
				}else{
					this.window_open_newtab( vpath );
				}
			}else{
				setTimeout(this.window_open_tab2,100,vpath);
			}
        },
		thing_editor_updated: function(vtabi, vdata){
			// console.log( "thing editor updated" );
			// console.log( vtabi );
			// console.log( vdata );
			//this.window_tabs[ vtabi ]['data']['thing']['body']['html'] = vdata+'';
			this.$refs[ vtabi ][0].html_body_updated__(vdata);
		},
		editor_updated: function(v){
			
		},
		get_node_type: function( v ){
			if( v in this.node_types ){
				return this.node_types[v];
			}
			return v;
		},
		load_sample_tabs: function(){
			this.show_thing("T1");
			this.show_thing("T2");
			this.show_thing("T3");
			this.show_thing("T4");
			this.show_thing("T5");
			this.show_thing("T6");
			this.show_thing("T7");
			setTimeout(this.show_thing,2000,"T8");
		},
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
			this.hide_simple_popup__();
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
		after_create: function(vi){
			if( "thing-"+vi in this.window_tabs ){
				this.$refs[ "thing-"+vi ][0].open_records();
			}
		},
		show_thing: function(vi){
			console.log( "show_thing:" + vi );
			this.$router.push(this.objectpath+ '/thing/'+vi);
			//this.window_open_newtab( "thing-"+vi, {"type":"thing", "thing_id": vi, "loaded":false} );
		},
		show_thing2: function(vi){
			this.$router.push({'path':this.objectpath+'/thing/'+vi, 'query':{'open_records':true} } );
			//this.window_open_newtab( "thing-"+vi, {"type":"thing", "thing_id": vi, "loaded":false, "open_records":true} );
		},
		show_thing_path: function(vtabi){
			if( vtabi.match(/^thing\-/i) ){
				vtabi = vtabi.replace('\-', '/');
			}
			this.$router.push(this.objectpath+'/'+vtabi);
		},
		thing_open_records: function(){
			console.log( this.$refs );
			this.$refs['thing_object'].open_records();
		},
		open_tab_home: function(){
			this.tab = 'home';
		},
		open_tab_import: function(){
			this.tab = 'import2';
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
		browse_list_delete: function(vi){
			this.browse_list_index = vi;
			if( confirm("Are you sure to delete the node?" ) ){
				axios.post("?",{
					"action": "objects_delete_node",
					"object_id": this.browse_list[ vi ]['_id'],
				}).then(response=>{
					if( response.status == 200 ){
						if( typeof( response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.browse_list.splice( this.browse_list_index, 1 );
									alert("Node is deleted successfully");
								}else{
									alert(response.data['error']);
								}
							}else{
								alert( "Incorrect response");
							}
						}else{
							alert("Incorrect response");
						}
					}else{
						alert("http error: " . response.status );
					}
				}).catch(error=>{
					alert(this.get_http_error__(error) );
				});
			}
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
				//console.log("event_keyup__: data-type not found");
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
					this.hide_simple_popup__();
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
					if( v === false ){console.log("event_click: value false 1");return false;}
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
					if( v === false ){console.log("event_click: value false 2");return false;}

					this.simple_popup_data__ = v;
					this.simple_popup_type__ = el_data_type.getAttribute("editable-type");
					this.simple_popup_modal__ = true;
					this.simple_popup_title__ = "Editable";
					//this.show_and_focus_context_menu__();
					this.set_simple_popup_style__();

				}else if( t == "payloadeditable" ){
					this.popup_datavar__ = data_var;
					var v = this.get_editable_value__({'data_var':data_var});
					if( v === false ){console.log("event_click: value false 3");return false;}
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
					if( v === false ){console.log("event_click: value false 4");return false;}
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
							//console.log("xxx: " + el_data_type.hasAttribute("allow-create") );
							if( el_data_type.hasAttribute("allow-create") ){
								this.context_thing_allow_create__ = true;
							}else{
								this.context_thing_allow_create__ = false;
							}
							//console.log( this.context_thing__ );
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
						}else if( ld in this.context_data__ ){
							this.context_list__ = this.context_data__[ ld ];
						}else{
							this.context_list__ = ld.split(",");
						}
					}
					if( this.context_type__ == "list-assoc" ){
						var ld = el_data_type.getAttribute( "data-list-values"   );
						if( ld == 'node-type' ){
							this.context_list__ = {"N":'Node',"L":'DataSet',"M":'Media',"D":'Document',"S":'Sheet'};
						}else if( ld in this.context_data__ ){
							this.context_list__ = this.context_data__[ ld ];
						}else{
							if( typeof(ld) == "string" ){
								this.context_list__ = JSON.parse(ld);
							}else{
								this.context_list__ = ld;
							}
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
						}else if( ld in this.context_data__ ){
							this.context_list__ = this.context_data__[ ld ];
						}else{
							this.context_list__ = [];
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
					this.hide_simple_popup__();
				}
			}
		},
		context_change_to_graph_create__: function(){
			this.hide_context_menu__();
			this.simple_popup_el__ = this.context_el__;
			this.simple_popup_datavar__ = this.context_datavar__;
			this.simple_popup_data__ = {};
			this.simple_popup_type__ = "graph-create";
			this.simple_popup_modal__ = true;
			this.simple_popup_title__ = "Create Node";
			//this.show_and_focus_context_menu__();
			this.set_simple_popup_style__();
		},
		create_node_select__: function(vd){
			console.log( this.simple_popup_datavar__ );
			var x = this.simple_popup_datavar__.split(/\:/g);
			x.pop();
			var parent = x.join(":");
			this.set_sub_var__(this, parent, {
				"t": "GT", 
				"i": vd['i']+'',
				"v": vd['v']+''
			});
			this.hide_simple_popup__();
			if( this.simple_popup_datavar__ == "search_thing:v" ){
				this.show_thing( vd['i'] );
			}else{

			}
		},
		create_node_on_fly__: function(){
			this.create_node_msg__ = "";
			this.create_node_err__ = "";
			if( this.create_node__['i_of']['i']=="" ){
				this.create_node_err__ = "Need instance";return;
			}
			if( this.create_node__['l']['v'].match(/^[a-z0-9\.\-\_\@\&\(\)\[\]\{\}\,\?\ ]{2,200}$/i) == null ){
				this.create_node_err__ = "Label required and plain";return;
			}

			this.create_node_msg__ = "Creating node...";
			axios.post("?",{
				"action": "objects_create_node_on_fly",
				"node": this.create_node__
			}).then(response=>{
				this.create_node_msg__ = "";
				if( response.status == 200 ){
					if( typeof( response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.create_node_select__({"i": response.data['inserted_id'], "v": response.data['label']});
							}else{
								this.create_node_err__ = response.data['error'];
							}
						}else{
							this.create_node_err__ = "Incorrect response";
						}
					}else{
						this.create_node_err__ = "Incorrect response";
					}
				}else{
					this.create_node_err__ = "http error: " . response.status ;
				}
			}).catch(error=>{
				this.create_node_err__ = this.get_http_error__(error);
			});
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
		show_create: function( vi_of ){
			if( typeof(vi_of) != "undefined" ){
				this.$router.push({'path':this.objectpath + '/thingnew','query':JSON.stringify({'vi_of':vi_of}) });
			}else{
				this.$router.push({'path':this.objectpath + '/thingnew'});
			}
		},
		show_create2: function(vi_of){
			if( typeof(vi_of) == "undefined" ){
				if( this.thing_id != -1 ){
					vi_of = {'t':"GT", "i": this.thing['i_of']['i']+'', "v": this.thing['i_of']['v']+''};
				}else{
					vi_of = {'t':"GT", "i": "T1", "v": "Root"};
				}
			}else if( 'i' in vi_of == false || 'v' in vi_of == false ){
				vi_of = {'t':"GT", "i": "T1", "v": "Root"};
			}
			this.window_open_newtab("thingnew", {
				"title": "CreateNode",
				"type": "thingnew",
				"i_of": JSON.parse(JSON.stringify(vi_of)),
				"data": {}
			});
			setTimeout(function(v){
				console.log( v.$refs );
			},1000,this);
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
				var d = {
					"t":"GT",
					"i":k['i']+'',
					"v":k['l']['v']+'',
				};
				if( 'ol' in k && k['t'] == "p" ){
					d['v'] = k['ol'];
				}
				this.set_sub_var__(this, parent, d);
			}else{
				this.set_sub_var__(this, this.context_datavar__, k );
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
			this.show_thing( this.search_thing['i'] );
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
				if( k == "ref" ){
					if( x[1] in this.$refs ){
						x.splice(0,1); k = x.splice(0,1);
						if( "length" in this.$refs[ k ] ){
							return this.set_sub_var__( this.$refs[ k ][0], x.join(":"), value, create_sub_node );
						}else{
							return this.set_sub_var__( this.$refs[ k ], x.join(":"), value, create_sub_node );
						}
					}
				}
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
				if( k == "ref" ){
					if( x[1] in this.$refs ){
						x.splice(0,1); k = x.splice(0,1);
						if( "length" in this.$refs[ k ] ){
							return this.get_sub_var__( this.$refs[ k ][0], x.join(":") );
						}else{
							return this.get_sub_var__( this.$refs[ k ], x.join(":") );
						}
					}
				}
				//this.echo__( x );
				var k = x[0];
				if( k.match(/^[0-9]+$/) ){
					k = Number(k);
				}
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( typeof(vv[ k ]) == "object" && vv[ k ] != null ){
							this.remove_sub_var__( vv[ k ], x.join(":") );
						}
					}else{
						delete(vv[k]);
					}
				}
			}catch(e){console.error(e);console.log("set_sub_var__ error: " + vpath );return false;}
		},
		get_sub_var__: function(vv, vpath){
			try{
				var x = vpath.split(":");
				var k = x[0];
				if( k == "ref" ){
					if( x[1] in this.$refs ){
						x.splice(0,1); k = x.splice(0,1);
						if( "length" in this.$refs[ k ] ){
							return this.get_sub_var__( this.$refs[ k ][0], x.join(":") );
						}else{
							return this.get_sub_var__( this.$refs[ k ], x.join(":") );
						}
					}
				}
				if( k.match(/^[0-9]+$/) && "length" in vv ){
					k = Number(k);
				}
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( typeof(vv[ k ]) == "object" && vv[ k ] != null ){
							var a_ = this.get_sub_var__( vv[ k ], x.join(":") );
							return a_;
						}else{
							console.log( "xx" );
							return false;
						}
					}else{
						console.log( "yy" );
						return vv[k];
					}
				}else{
					console.log( "dd" );
					return false;
				}
			}catch(e){console.log("get_sub_var__ error: " + vpath + ": " + e );return false;}
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
			if( this.simple_popup_modal__ ){
				document.getElementById("simple_popup_modal__").appendChild( document.getElementById("context_menu__") );
			}else if( this.popup_modal_displayed__ ){
				document.getElementById("popup_modal_body__").appendChild( document.getElementById("context_menu__") );
			}else if( this.create_popup_displayed__ ){
				document.getElementById("create_popup_body").appendChild( document.getElementById("context_menu__") );
			}
			this.context_expand_key__ = '';
		},
		set_context_menu_style__: function(){
			var s = this.context_el__.getBoundingClientRect();
			//this.finx_zindex(this.context_el__);
			if( this.simple_popup_modal__ ){
				var s2 = document.getElementById("simple_popup_modal__").getBoundingClientRect();
				this.context_style__ = "display:block;top: "+(Number(s.top)-Number(s2.top))+"px;left: "+(Number(s.left)-Number(s2.left))+"px;";
				//console.log( this.context_style__ );
			}else if( this.popup_modal_displayed__ ){
				var s2 = document.getElementById("popup_modal_body__").getBoundingClientRect();
				this.context_style__ = "display:block;top: "+(Number(s.top)-Number(s2.top))+"px;left: "+(Number(s.left)-Number(s2.left))+"px;";
			}else if( this.create_popup_displayed__ ){
				var s2 = document.getElementById("create_popup_body").getBoundingClientRect();
				this.context_style__ = "display:block;top: "+(Number(s.top)-Number(s2.top))+"px;left: "+(Number(s.left)-Number(s2.left))+"px;";
			}else{
				this.context_style__ = "display:block;top: "+s.top+"px;left: "+s.left+"px;";
			}
		},
		set_simple_popup_style__: function(){
			var s = this.simple_popup_el__.getBoundingClientRect();
			if( this.popup_modal_displayed__ ){
				var s2 = document.getElementById("popup_modal_body__").getBoundingClientRect();
				document.getElementById("popup_modal_body__").appendChild( document.getElementById("simple_popup_modal__") );
				this.simple_popup_style__ = "top: "+(Number(s.top)-Number(s2.top))+"px;left: "+(Number(s.left)-Number(s2.left))+"px;";
			}else if( this.create_popup_displayed__ ){
				var s2 = document.getElementById("create_popup_body").getBoundingClientRect();
				document.getElementById("create_popup_body").appendChild( document.getElementById("simple_popup_modal__") );
				this.simple_popup_style__ = "top: "+(Number(s.top)-Number(s2.top))+"px;left: "+(Number(s.left)-Number(s2.left))+"px;";
			}else{
				this.simple_popup_style__ = "top: "+(Number(s.top))+"px;left: "+(Number(s.left))+"px;";
			}
		},
		hide_simple_popup__: function(){
			if( document.getElementById("simple_popup_modal__").parentNode.nodeName != "BODY" ){
				console.log("moving simple_popup_modal__ back to body ");
				document.body.appendChild( document.getElementById("simple_popup_modal__") );
			}
			setTimeout(this.hide_simple_popup2__,100);
		},
		hide_simple_popup2__: function(){
			this.simple_popup_modal__ = false;
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
													if( v2[j]['l']['v'].toLowerCase() == v[i]['l']['v'].toLowerCase() && v2[j]['i_of']['i'] == v[i]['i_of']['i'] ){
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
													if( v2[j]['l']['v'].toLowerCase() > v2[j+1]['l']['v'].toLowerCase() ){
														var t = v2.splice(j,1);
														v2.splice(j+1,0,t[0]);
													}
												}
											}
											//this.echo__( v2 );
											this.context_thing_list__[ this.context_thing__ ] = v2;
											if( this.context_menu_key__ == "" && this.context_thing_list_basic__.length == 0 ){
												this.context_thing_list_basic__ = v2;
											}
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
				if( this.context_menu_key__ == "" ){
					var v3 = [];
					var vbasic = this.context_thing_list_basic__;
					//this.echo__(vbasic);
					for(var i=0;i<vbasic.length&&v3.length<50;i++){
						vbasic[i]['r'] = "<b>" + vbasic[i]['l']['v'] + "</b> in <span class='text-secondary'>" + vbasic[i]['i_of']['v'] + "</span> [" + vbasic[i]['i'] + "]";
						v3.push(vbasic[i]);
					}
					this.context_thing_graph_list__ = v3;
				}else{
					var v2 = this.context_thing_list__[ this.context_thing__ ];
					var vkey = this.context_menu_key__+'';
					var w = vkey.split(/\W+/g);
					//this.echo__( w );
					w.reverse();
					var key2 = w.join(".*");
					var k = vkey.trim().replace(/\W+/g, ".*");
					var kpr = new RegExp("^"+k,"i");
					w.reverse();
					var v3 = [];
					var vkeys = {};
					var k = this.context_menu_key__.toLowerCase();

					var match_found_at = -1;
					//console.log("Total: " + v2.length);
					for(var i=0;i<v2.length;i++){
						if( v2[i]['i'] in vkeys == false ){
							if( v2[i]['l']['v'].match(kpr) ){
								var vres = JSON.parse(JSON.stringify(v2[i]));
								v3.push( vres );
								vkeys[ v2[i]['i'] ] = 1;
								if( match_found_at == -1 ){
									match_found_at = i;
									//console.log("1stmatch: " + i);
								}
							}else if( match_found_at > -1 ){
								//console.log("lastmatch: " + i);
								break;
							}
						}
					}
					//console.log("v3:"+v3.length);
					if( v3.length < 100 ){
						var kpr = new RegExp(k,"i");
						var kpr2 = new RegExp(key2,"i");
						for(var i=0;i<v2.length&&v3.length<100;i++){
							if( v2[i]['l']['v'].match(kpr) || v2[i]['l']['v'].match(kpr2) ){
								if( v2[i]['i'] in vkeys == false ){
									var vres = JSON.parse(JSON.stringify(v2[i]));
									v3.push( vres );
								}
							}
						}
					}
					//console.log("v3:"+v3.length);
					for(var i=0;i<v3.length;i++){
							v3[i]['r'] = v3[i]['l']['v']+'';
							for(var wi=0;wi<w.length;wi++){
								var rg = new RegExp( w[wi], "i" );
								var rgm = v3[i]['r'].match(rg);
								if( rgm ){
									v3[i]['r'] = v3[i]['r'].replace(rgm[0], "zzzz"+rgm+"-zzzz");
									//console.log( w[wi] );
									v3[i]['rg'] = w[wi];
									//v3[i]['rg'] = rgm;
								}
							}
							v3[i]['r'] = v3[i]['r'].replace( /\-zzzz/g, "</span>" );
							v3[i]['r'] = v3[i]['r'].replace( /zzzz/g, "<span class='text-danger'>" );
							if( 'ol' in v3[i] ){
								if( v3[i]['ol'] != v3[i]['l']['v'] ){
									if( 'rg' in v3[i] && v3[i]['t'] != 'a' ){
										//this.echo__( v3[i] );
										var rg = "";
										var w2 = v3[i]['ol'].split(/\W+/g);
										for(var wi=0;wi<w2.length;wi++){
											var rgm = w2[wi].match( new RegExp(w[0],"i") );
											if( rgm ){
												rg = w2[wi];
												rg = rg.replace(rgm[0], "zzzz"+rgm+"-zzzz");
												break;
											}
										}
										rg = rg.replace( /\-zzzz/g, "</span>" );
										rg = rg.replace( /zzzz/g, "<span class='text-danger'>" );
										v3[i]['r'] = "<b>" + rg + " </b> alias <b>"+v3[i]['ol']+"</b> in <span class='text-secondary'><em><b>" + v3[i]['i_of']['v'] + "</b></em></span> ["+ v3[i]['i'] + "]";
									}else{
										v3[i]['r'] = "<b>" + v3[i]['r'] + "</b> alias <b>"+v3[i]['ol']+"</b> in <span class='text-secondary'><em><b>" + v3[i]['i_of']['v'] + "</b></em></span> ["+ v3[i]['i'] + "]";
									}
								}else{
									v3[i]['r'] = "<b>" + v3[i]['r'] + "</b> in <span class='text-secondary'><em><b>" + v3[i]['i_of']['v'] + "</b></em></span> ["+ v3[i]['i'] + "]";
								}
							}else{
								v3[i]['r'] = "<b>" + v3[i]['r'] + "</b> in <span class='text-secondary'><em><b>" + v3[i]['i_of']['v'] + "</b></em></span> ["+ v3[i]['i'] + "]";
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
						this.echo__(this.popup_type__ );
						this.popup_type__ = "";
					console.log("Popup closed");
					this.popup_modal_displayed__ = false;
					if( this.popup_type__ == 'import_template_edit' ){
					}
				});
			}
			this.popup_modal__.show();
			this.popup_modal_displayed__ = true;
		},
		popup_modal_hide__: function(){
			this.popup_modal__.hide();
			this.popup_modal_displayed__ = false;
		},
		popup_callback__: function( vdata ){
			if( this.popup_type__ =='import_template_create' ){
				this.popup_modal_hide__();
				if( "length" in this.$refs['import2'] ){
					this.$refs['import2'][0].new_thing_created( vdata );
				}else{
					this.$refs['import2'].new_thing_created( vdata );
				}
			}else if( this.popup_type__ =='dataset_edit_record' ){
				if( "length" in this.$refs[ this.popup_source__ ] ){
					this.$refs[ this.popup_source__ ][0].dataset_record_updated( vdata );
				}else{
					this.$refs[ this.popup_source__ ].dataset_record_updated( vdata );
				}
			}else if( this.popup_type__ =='dataset_create_record' ){
				if( "length" in this.$refs[ this.popup_source__ ] ){
					this.$refs[ this.popup_source__ ][0].dataset_record_created( vdata );
				}else{
					this.$refs[ this.popup_source__ ].dataset_record_created( vdata );
				}
			}else{
				this.call_callback__( this.popup_type__, vdata );
			}
		},
		call_callback__: function( refname, vdata ){
			if( refname in this.$refs == false ){
				console.log("call_callback__:" + refname + ": not found");
			}else if( "length" in this.$refs[ refname ] ){
				this.$refs[ refname ][0].callback__( vdata );
			}else{
				this.$refs[ refname ].callback__( vdata );
			}
		},
		import_template_edit_popup_open: function( vobject_id, vobject_name ){
			this.popup_datavar__ = "none";
			this.popup_data__ = {"object_id":vobject_id, "object_name": vobject_name};
			this.popup_type__ = 'import_template_edit';
			//this.popup_source__ = refname;
			this.popup_title__ = "Edit Template of `" + vobject_name + "`";
			this.popup_modal_open__();
		},
		import_template_create_popup_open: function( vdefault = {} ){
			this.popup_datavar__ = "none";
			if( 'data' in vdefault == false ){
				vdefault['data'] = {};
			}
			this.popup_data__ = vdefault;
			this.popup_type__ = 'import_template_create';
			this.popup_title__ = "Create a Node & Template";
			this.popup_modal_open__();
		},
		create_with_template_popup: function(){
			this.popup_datavar__ = "none";
			this.popup_data__ = {};
			this.popup_type__ = 'template_create';
			this.popup_title__ = "Create a Node";
			this.popup_modal_open__();
		},
		show_create_dataset_record: function(refname, vd){
			this.popup_datavar__ = "none";
			this.popup_data__ = vd;
			this.popup_source__ = refname;
			this.popup_type__ = 'dataset_create_record';
			this.popup_title__ = "Dataset '"+vd['thing']['l']['v']+"' Create Record";
			this.popup_modal_open__();
		},
		show_edit_dataset_record: function(refname, vd){
			this.popup_datavar__ = "none";
			this.popup_data__ = JSON.parse(JSON.stringify(vd));
			this.popup_source__ = refname;
			this.popup_type__ = 'dataset_edit_record';
			this.popup_title__ = "Dataset '"+vd['thing']['l']['v']+"' Edit Record: '"+vd['record']['_id']+"'";
			//this.popup_title__ = "Edit Record in DataSet: " + vd['v'];
			this.popup_modal_open__();
		},
		window_close_tab: function(vtabi){
			this.current_tab = '';
			var vt = vtabi+'';
			if( vt != 'home' && vt != 'summary' && vt != 'browse' && vt != 'import2' ){
				var i = this.window_tabs_order.indexOf( vt+'' );
				this.window_tabs_order.splice(i,1);
				delete(this.window_tabs[ vt ]);
				setTimeout(function(v){
					var k = Object.keys( v.window_tabs );
					v.current_tab = k[ k.length-1 ]+'';
					var vpath = v.current_tab+'';
					if( vpath.match(/^thing\-/) ){
						vpath = vpath.replace("-","/");
					}
					v.$router.push(v.objectpath+'/'+vpath);
				}, 500, this);
			}
		},
		window_open_tab: function(vtabi){
			this.current_tab = '';
			console.log( "Router add: " + vtabi );
			this.$router.push(this.objectpath+"/"+vtabi);
		},
		window_open_tab2: function(vtabi){
			var tb = vtabi+'';
			if( tb in this.window_tabs ){
				if( tb == 'browse' ){
					if( this.browse_list.length == 0 ){
						this.load_browse_list();
					}
				}
				if( tb == 'summary' ){
					this.load_nodes();
				}
				// var i = this.window_tabs_order.indexOf( tb );
				// if( i > 2 ){
				// 	this.window_tabs_order.splice(i,1);
				// 	this.window_tabs_order.splice(3,0,tb+'');
				// }
				this.current_tab = tb+'';
			}else{
				alert("tabindex not found:  " + tb);
			}
			setTimeout(this.window_tab_focus,500);
		},
		window_tab_focus: function(){
			//this.echo__( this.window_tabs );
			//this.echo__( "window_tab_focus");
			var d = document.getElementById("tabs_container").children;
			var sw = 0;
			for(var i=d.length-1;i>=0;i--){
				var s = d[i].getBoundingClientRect();
				sw = sw + s.width + 5;
			}
			//console.log( "container_width: " + sw );
			var ml= document.getElementById("tabs_container").firstElementChild.style.marginLeft;
			// console.log( ml );
			if( ml != "initial" && ml != "" ){
				ml = Number( ml.replace("px","") );
			}else{ml = 0;}
			//console.log( sw );
			// sw = sw + ml;
			var pw = document.getElementById("tabs_container").parentNode.clientWidth;
			console.log( "ScrollWidth:"+ sw + " > PageWidth: " + pw );
			if( sw > pw ){
				document.getElementById("tabs_left_scrollbar").style.display ='block';
				document.getElementById("tabs_container").style.marginLeft = "30px";
				document.getElementById("tabs_right_scrollbar").style.display ='block';
			}else{
				document.getElementById("tabs_left_scrollbar").style.display ='none';
				document.getElementById("tabs_container").style.marginLeft = "initial";
				document.getElementById("tabs_container").children[0].style.marginLeft = "initial";
				document.getElementById("tabs_right_scrollbar").style.display ='none';
			}
			var id = "tab_"+this.current_tab;
			//console.log(id);
			if( document.getElementById(id) != null ){
				var s = document.getElementById(id).getBoundingClientRect();
				//this.echo__(s);
				var s2 = document.getElementById("tabs_container").getBoundingClientRect();
				//this.echo__(s2);
				//this.echo__(w);
				//console.log( document.getElementById("tabs_container").firstElementChild );
				if( s['right'] > s2['right'] ){
					var dif = s['right']-s2['right'];
					ml = ml - dif - 30;
					//console.log( ml );
					document.getElementById("tabs_container").firstElementChild.style.marginLeft = ml+"px";
				}
			}
		},
		window_tabs_focus_left: function(){
			var s1 = document.getElementById("tabs_left_scrollbar").getBoundingClientRect();
			//console.log( s1.left + ": " + s1.right );
			var s1left = s1.left + 30;
			var d = document.getElementById("tabs_container").children;
			for(var i=d.length-1;i>=0;i--){
				var s = d[i].getBoundingClientRect();
				//console.log( i + ": " + s.left + ": " + s.right );
				if( s1left > s.left ){
					//console.log( "need to move right: " + (s1left-s.left) );
					var ml = document.getElementById("tabs_container").firstElementChild.style.marginLeft;
					if( ml == "initial" ){ var so = 0; }else{
						var so = Number( document.getElementById("tabs_container").firstElementChild.style.marginLeft.replace("px","") );
					}
					//console.log( so );
					so = so + (s1left-s.left);
					//console.log( so );
					document.getElementById("tabs_container").firstElementChild.style.marginLeft = (so) + "px";
					break;
				}
			}
		},
		window_tabs_focus_right: function(){
			var s1 = document.getElementById("tabs_right_scrollbar").getBoundingClientRect();
			//console.log( s1.left + ": " + s1.right );
			var s1right = s1.right-30;
			var d = document.getElementById("tabs_container").children;
			for(var i=0;i<d.length;i++){
				var s = d[i].getBoundingClientRect();
				//console.log( i + ": " + s.left + ": " + s.right );
				if( s1right < s.right ){
					//console.log( "need to move left: " + (s.right-s1right) );
					var ml = document.getElementById("tabs_container").firstElementChild.style.marginLeft;
					if( ml == "initial" ){ var so = 0; }else{
						var so = Number( document.getElementById("tabs_container").firstElementChild.style.marginLeft.replace("px","") );
					}
					//console.log( so );
					so = so - (s.right-s1right);
					//console.log( so );
					document.getElementById("tabs_container").firstElementChild.style.marginLeft = (so) + "px";
					break;
				}
			}
		},
		router_open_newtab: function(vtabi,vd){
			this.$router.push(this.objectpath + '/'+vtabi);
		},
		window_open_newtab: function(vtabi,vd){
			this.current_tab = '';
			var tb = vtabi+'';
			if( tb in this.window_tabs ){
				if( typeof(vd) == "object" ){
					if( "type" in vd ){
						if( vd['type'] == 'thing' ){
							vd['msg'] = "Reloading...";
						}
					}
				}
			}else{
				if( typeof(vd) == "object" ){
					if( "type" in vd ){
						if( vd['type'] == 'thing' ){
							vd['title'] = "Thing: " + vd['thing_id'] +' Loading';
							vd['msg'] = "Loading...";
							vd['err'] = "";
							vd['thing'] = {};
						}
					}
				}
				if( vtabi == "import2" ){
					var vd = {
						"title": "Import",
						"type": "import2",
						"vimport": {
							"i_of": {"t": "GT", "i": "", "v": ""},
							"data": [],
							"template":{},
							"edit_field":"",
						},
					};
				}
				if( vtabi == "ops" ){
					var vd = {
						"title": "Operations",
						"type": "ops",
						"data": {"op":""}
					};
				}
				this.window_tabs[ vtabi+'' ] = vd;
				//this.window_tabs_order.splice(3,0,tb);
				this.window_tabs_order.push(tb);
			}
			this.window_open_tab2(tb);
		},
	}
});

var icon_view = {
	data: function(){
		return {
			icon_domain: "unknown.com",
			image_domain: "unknown.com",
		};
	},
	props: ["data"],
	mounted: function(){
		if( typeof(this.$root.icon_settings) != "undefined" ){
			this.icon_domain  = this.$root.icon_settings['icon_domain']  + '';
			this.image_domain = this.$root.icon_settings['image_domain'] + '';
		}
	},
	methods: {
		get_icon_url: function(vcountrycode, vsize){
			return "/"+"/" + this.icon_domain + "/flag-icons/flags/"+vsize+"/"+vcountrycode+".svg";
		},
		get_image_url: function(v){
			return "/"+"/" + this.image_domain + v;
		},
	},
	template: `<div>
		<div v-if="'t' in data==false" style="font-size:1.2rem;text-align: center;" >
			<div><i class="fas fa-face-grin-stars"></i> Add Icon</div>
		</div>
		<div v-else-if="data['t']!='IC'" style="font-size:1.2rem;text-align: center;" >
			<div><i class="fas fa-face-grin-stars"></i> Add Icon</div>
		</div>
		<div v-else-if="data['it']=='font'||data['it']=='fontawesome'||data['it']=='remix'" class="objecticonfont" v-bind:title="data['l']"><i v-bind:class="data['v']" ></i></div>
		<div v-else-if="data['it']=='emoji'"  class="objecticonemoji"v-html="data['v']" v-bind:title="data['l']"></div>
		<div v-else-if="data['it']=='svg'" class="objecticonsvg" v-html="data['v']" v-bind:title="data['l']"></div>
		<div v-else-if="data['it']=='flag'" class="objecticonflag" ><img v-bind:src="get_icon_url(data['v'], data['sz'])" v-bind:title="data['l']"></div>
		<div v-else-if="data['it']=='img'" class="objecticonimg" ><img v-bind:src="get_image_url(data['v'])" ></div>
		<div v-else style="width:50px; height:50px; font-size:0.8rem;">Unknown Format</div>
	</div>`
};

<?php foreach( $components as $i=>$j ){ ?>
	app.component( "<?=$j ?>", <?=$j ?> );
<?php } ?>
app.component( "objects_import_v2", objects_import_v2 );
app.component( "object_template_edit", object_template_edit );
app.component( "object_template_create_v2", object_template_create_v2 );
app.component( "graph_object_v2", graph_object_v2 );
app.component( "object_dataset_create_record", object_dataset_create_record );
app.component( "object_dataset_edit_record", object_dataset_edit_record );
app.component( "object_ops", object_ops );
app.component( "editor_component", editor_component );
app.component( "iconsapp_component", iconsapp_component );
app.component( "icon_view", icon_view );


const component_default = {
	template: `<div>okk</div>`
};

const routes = [
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/<?=$config_param3 ?>/', component: component_default  },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/<?=$config_param3 ?>/summary', component: component_default  },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/<?=$config_param3 ?>/browse', component: component_default },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/<?=$config_param3 ?>/about', component: component_default },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/<?=$config_param3 ?>/thing/:id', component: component_default },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/<?=$config_param3 ?>/thingnew', component: component_default },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/<?=$config_param3 ?>/ops', component: component_default },
	{ path: '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/<?=$config_param3 ?>/import2', component: component_default },
];

const router = VueRouter.createRouter({
	history: VueRouter.createWebHistory(), routes
});

router.beforeEach((to,from)=>{
	console.log("Router changed: ");
	console.log( from );
	console.log( to );

	var vpath = to.path.replace( '<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/objects/<?=$config_param3 ?>', '');
	vpath = vpath.substr(1,9999);

	if( vpath == "" ){
		vpath = "summary";
	}

	if( vpath == 'thingnew' ){
		if( 'query' in to ){
			if( 'vi_of' in to.query ){
				router.app_object.show_create2(JSON.parse(to.query['vi_of']));
			}else{
				router.app_object.show_create2();
			}
		}else{
			router.app_object.show_create2();
		}
		return true;
	}

	var thing_id = "";
	var m = vpath.match(/^thing\/([a-z0-9]+)$/i);
	if( m ){
		thing_id = m[1];
		vpath = 'thing-'+thing_id;
	}

	console.log( Object.keys(router.app_object.window_tabs) );

	if( vpath in router.app_object.window_tabs == false ){
		if( thing_id ){
			router.app_object.window_open_newtab( vpath, {"type":"thing", "thing_id": thing_id, "loaded":false} );
		}else{
			router.app_object.window_open_newtab( vpath );
		}
	}else{
		setTimeout(router.app_object.window_open_tab2,100,vpath);
	}
	// if (!isAuthenticated &&	to.name !== 'Login' ) {
	// 	return { name: 'Login' }
	// }
});
router.afterEach((to, from) => {
	// do something...

	// if (Object.keys(to.query).length)
    // return { path: to.path, query: {}, hash: to.hash }

    //if (to.hash) return { path: to.path, query: to.query, hash: '' }

});

app.use(router);
var app1 = app.mount("#app");
router.app_object = app1;

function getElementFocused(){
	return document.getSelection().getRangeAt(0).startContainer;
}


function get_http_error__(e){
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
}

</script>
