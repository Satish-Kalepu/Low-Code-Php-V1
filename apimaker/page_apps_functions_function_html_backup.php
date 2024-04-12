<div id="app" v-cloak >
	<div class="leftbar">
		<?php require("page_apps_leftbar.php"); ?>
	</div>

	<div style="position: fixed;left:150px; top:40px; height: 60px; width:calc( 100% - 150px ); background-color: white; overflow: hidden; border-bottom:1px solid #ccc; " >
		<div style="padding: 10px;" >
			<div>
				<h5 class="d-inline">Function: {{ function__['name'] }}</h5>
				<div v-on:click="open_edit_form" class="btn btn-default btn-sm" style="float:right;" ><i class="fa fa-lg fa-pencil-square-o" ></i></div>
			</div>
			<div class="d-inline" >{{ function__['des'] }}</div>
			<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
			<div v-if="err" class="alert alert-danger" >{{ err }}</div>
		</div>
	</div>

	<div v-if="vshow==false" >Loading...</div>
	<div v-else>
		<div class="codeeditor_block_a" v-if="code_editor_full__" >
			<div style="padding: 10px;" >
				<?php require("page_apps_functions_function_html_logic.php"); ?>
			</div>
		</div>
		<div class="codeeditor_block_b" v-if="code_editor_full__==false" v-on:click="show_test_tab__=false;code_editor_full__=true"  >
			<div style="padding:5px; text-align:center;"><i class="fa fa-bars" ></i><BR/>L<BR/>O<BR/>G<BR/>I<BR/>C</div>
		</div>
		<div class="test_menu_div_a" v-if="show_test_tab__==false" v-on:click="show_test_tab__=true;code_editor_full__=false" >
			<div style="padding:5px; text-align:center;"><i class="fa fa-bars" ></i><BR/>T<BR/>E<BR/>S<BR/>T</div>
		</div>
		<div class="test_menu_div_b" v-if="show_test_tab__" >
			<?php require("page_apps_functions_function_html_test.php"); ?>
		</div>
	</div>


	<div v-if="save_need__" class="save_block_a" >
		<input spellcheck="false" type="button" class="btn btn-primary btn-sm" v-on:click="save_data__" value="SAVE">
	</div>
	<div v-if="show_saving__" class="save_block_b">{{ save_message__ }}</div>  



	<div class="modal fade" id="edit_modal" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Edit Function Meta Data</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        	<div>Name</div>
	        	<input spellcheck="false" type="text" class="form-control" v-model="edit_function['name']" placeholder="Name" >
	        	<div>no spaces. no special chars. except -</div>
	        	<div>&nbsp;</div>
	        	<div>Description</div>
	        	<textarea spellcheck="false" class="form-control" v-model="edit_function['des']" ></textarea>
	        	<div v-if="cmsg" class="alert alert-success" >{{ cmsg }}</div>
	        	<div v-if="cerr" class="alert alert-success" >{{ cerr }}</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
	        <button type="button" class="btn btn-primary btn-sm"  v-on:click="editnow">SAVE</button>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="ses_expired" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Sessions Expired</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<p>
						Session Expired, Your will be redricted to Home Page
					</p>
				</div>
			</div>
		</div>
	</div>

	<div v-if="show_import_popup__" style="border-bottom:1px solid #bbcccc; margin-bottom:10px;">
		<form method="post" enctype="multipart/form-data">
			<div><input spellcheck="false" type="file" name="function_data" placeholder="Select file with extension .wiki" accept="*.wiki" required></div>
			<div><input spellcheck="false" type="password" name="function_secret" placeholder="Enter file password" required ></div>
			<div><label style="cursor:pointer;"><input type="radio" name="mode" value="replace" checked>Replace Stages</label> </div>
			<div><label style="cursor:pointer;"><input type="radio" name="mode" value="append">Append Stages</label> </div>
			<div><input type="submit" name="btn" value="IMPORT"></div>
			<input type="hidden" name="page_id" value="<?=pass_encrypt($config_param4) ?>">
			<input type="hidden" name="action" value="import_function_engine">
		</form>
		<div class="text-danger" >This action will replace existing rules & input factors</div>
	</div>

	<div class="modal fade" id="doc_popup__" tabindex="-1" >
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-header">
	    <div class="modal-title" ><h5 class="d-inline">Help</h5></div>
	    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  <div class="modal-body" style="position: relative;">
	  	<div v-html="doc_popup_text__" ></div>
	  </div>
	</div>
	</div>
	</div>

	<div class="modal fade" id="popup_modal__" tabindex="-1" >
	  <div class="modal-dialog modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <div class="modal-title" ><h5 class="d-inline">{{ popup_title__ }}</h5></div>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body" v-bind:data-stagei="popup_stage_id__" style="position: relative;">
	      		<template v-if="popup_type__=='O2'" >
					<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
					<vobject2 v-bind:v="popup_data__" v-bind:datafor="popup_for__" v-bind:datavar="popup_datavar__" v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" v-bind:suggest="popup_suggest_list__" ></vobject2>
					</div>
	      		</template>
	      		<template v-else-if="popup_type__=='O'||popup_type__=='L'" >
	      			<template v-if="popup_import__==false" >
			      		<div align="right"><div class="btn btn-link btn-sm" style="position:absolute; margin-top:-60px; margin-left:-100px;" v-on:click="popup_import__=true" >Import</div></div>
						<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
			      		<!-- <pre>{{ popup_data__}}</pre> -->
			        	<vobject v-if="popup_type__=='O'" v-bind:v="popup_data__" v-bind:datafor="popup_for__" v-bind:datavar="popup_datavar__" v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" ></vobject>
			        	<vlist v-else-if="popup_type__=='L'" v-bind:v="popup_data__" v-bind:datafor="popup_for__" v-bind:datavar="popup_datavar__" v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" ></vlist>
			        	</div>
			        </template>
			        <template v-else >
						<textarea spellcheck="false" style="width:100%; height:100%; min-height: 200px; max-width:750px;max-height: 400px;" v-model="popup_import_str__" v-on:keydown.tab.prevent.stop ></textarea>
			      		<div class="p-2">
			      			<div class="btn btn-secondary btn-sm" v-on:click="popup_import__=false" >Cancel</div>&nbsp; &nbsp;
							<div class="btn btn-primary btn-sm" v-on:click="popup_import_json_data__" >Import</div>
			      		</div>
			        </template>
	        	</template>
	        	<template v-else-if="popup_type__=='PayLoad'" >
	      			<template v-if="popup_import__==false" >
			      		<div align="right"><div class="btn btn-link btn-sm" style="position:absolute; margin-top:-60px; margin-left:-100px;" v-on:click="popup_import__=true" >Import</div></div>
						<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
			      		<!--<pre>{{ popup_data__}}</pre>-->
			        	<vobject_payload v-bind:v="popup_data__" v-bind:datafor="popup_for__" v-bind:datavar="popup_datavar__"   ></vobject_payload>
			        	</div>
			        </template>
			        <template v-else >
						<textarea spellcheck="false" style="width:100%; height:100%; min-height: 200px; max-width:750px;max-height: 400px;" v-model="popup_import_str__" v-on:keydown.tab.prevent.stop ></textarea>
			      		<div class="p-2">
			      			<div class="btn btn-secondary btn-sm" v-on:click="popup_import__=false" >Cancel</div>&nbsp; &nbsp;
							<div class="btn btn-primary btn-sm" v-on:click="popup_import_json_data__" >Import</div>
			      		</div>
			        </template>
	        	</template>
	        	<template v-else-if="popup_type__=='MongoQ'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mongoq v-bind:v="popup_data__" ref="mongoq" refname="mongoq" v-bind:datafor="popup_for__" v-bind:datavar="popup_datavar__" 
					v-bind:rootdata="engine__['stages'][ popup_stage_id__ ]['d']"  
	        		v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" 
	        		v-on:updated="updated_option__" 
	        		 ></mongoq>
	        		</div>
	        	</template>
				<template v-else-if="popup_type__=='MongoD'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mongod v-bind:v="popup_data__" v-bind:datafor="popup_for__" 
	        		v-bind:rootdata="engine__['stages'][ popup_stage_id__ ]['d']"  
	        		v-bind:datavar="popup_datavar__" v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" 
	        		v-on:updated="updated_option__" 
	        		 ></mongod>
	        		</div>
	        	</template>
	        	<template v-else-if="popup_type__=='MongoD3'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mongod3 v-bind:v="popup_data__" v-bind:datafor="popup_for__" 
	        		v-bind:rootdata="engine__['stages'][ popup_stage_id__ ]['d']"  
	        		v-bind:datavar="popup_datavar__" v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" 
	        		v-on:updated="updated_option__" 
	        		 ></mongod3>
	        		</div>
	        	</template>
	        	<template v-else-if="popup_type__=='MongoD2'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mongod2 v-bind:v="popup_data__" v-bind:datafor="popup_for__" 
	        		v-bind:rootdata="engine__['stages'][ popup_stage_id__ ]['d']"  
	        		v-bind:datavar="popup_datavar__" v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" 
	        		v-on:updated="updated_option__" 
	        		 ></mongod2>
	        		</div>
	        	</template>
				<template v-else-if="popup_type__=='MongoP'" >
					<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
					<mongop v-bind:v="popup_data__" v-bind:datafor="popup_for__" 
					v-bind:rootdata="engine__['stages'][ popup_stage_id__ ]['d']"  
					v-bind:datavar="popup_datavar__" v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" 
					v-bind:ref="popup_ref__" v-bind:refname="popup_ref__" 
					v-on:updated="updated_option__" 
					></mongop>
					</div>
				</template>
				<template v-else-if="popup_type__=='MongoP2'" >
					<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
					<mongop2 v-bind:v="popup_data__" v-bind:datafor="popup_for__" 
					v-bind:rootdata="engine__['stages'][ popup_stage_id__ ]['d']"  
					v-bind:datavar="popup_datavar__" v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" 
					v-on:updated="updated_option__" 
					></mongop2>
					</div>
				</template>
	        	<template v-else-if="popup_type__=='MySqlQ'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mysqlq v-bind:v="popup_data__" v-bind:datafor="popup_for__" 
	        		v-bind:datavar="popup_datavar__" 
	        		v-bind:rootdata="engine__['stages'][ popup_stage_id__ ]['d']"  
	        		v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" 
	        		v-on:updated="updated_option__" ></mysqlq>
	        		</div>
	        	</template>
				<template v-else-if="popup_type__=='MySqlD'" >
					<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
					<mysqld v-bind:v="popup_data__" v-bind:datafor="popup_for__" v-bind:datavar="popup_datavar__" v-bind:rootdata="engine__['stages'][ popup_stage_id__ ]['d']"  v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" v-on:updated="updated_option__" ></mysqld>
					</div>
	        	</template>
	        	<template v-else-if="popup_type__=='MySqlP'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mysqlp v-bind:v="popup_data__" v-bind:datafor="popup_for__" v-bind:datavar="popup_datavar__" v-bind:rootdata="engine__['stages'][ popup_stage_id__ ]['d']"  v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" v-on:updated="updated_option__" ></mysqlp>
	        		</div>
	        	</template>
	        	<template v-else-if="popup_type__=='MySqlS'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mysqls v-bind:v="popup_data__" v-bind:datafor="popup_for__" v-bind:datavar="popup_datavar__" v-bind:rootdata="engine__['stages'][ popup_stage_id__ ]['d']"  v-bind:vars="all_factors_stage_wise__[ popup_stage_id__]" v-on:updated="updated_option__" ></mysqls>
	        		</div>
	        	</template>
	        	<div v-else-if="popup_type__=='TT'" >
					<textarea spellcheck="false" style="width:100%; height:100%; white-space: nowrap; min-height: 200px; max-width:750px;max-height: 400px;" v-model="popup_data__" v-on:keydown.tab.prevent.stop  v-on:blur="updated_option__" ></textarea>
	        	</div>
	        	<div v-else-if="popup_type__=='HT'" >
					<div>HTML Editor coming sooon!</div>
	        	</div>
	        	<div v-else >Unhandled popup type {{ popup_type__ }}</div>
	      </div>
	    </div>
	  </div>
	</div>	

	<div v-if="simple_popup_modal__" data-context="contextmenu" class="context_menu__" v-bind:style="simple_popup_style__"  v-bind:data-stagei="simple_popup_stage_id__"  >
		<template v-if="simple_popup_type__=='d'" >
			<vdt v-bind:v="simple_popup_data__" v-bind:datafor="simple_popup_for__" v-bind:datavar="simple_popup_datavar__" v-on:close="simple_popup_modal__=false"  v-on:update="set_stage_sub_var__(simple_popup_stage_id__,simple_popup_datavar__,$event)" ></vdt>
		</template>
		<template v-else-if="simple_popup_type__=='dt'" >
			<vdtm v-bind:v="simple_popup_data__" v-bind:datafor="simple_popup_for__" v-bind:datavar="simple_popup_datavar__" v-on:close="simple_popup_modal__=false"  v-on:update="set_stage_sub_var__(simple_popup_stage_id__,simple_popup_datavar__,$event)"></vdtm>
		</template>
		<template v-else-if="simple_popup_type__=='ts'" >
			<vts v-bind:v="simple_popup_data__" v-bind:datafor="simple_popup_for__" v-bind:datavar="simple_popup_datavar__" v-on:close="simple_popup_modal__=false"  v-on:update="set_stage_sub_var__(simple_popup_stage_id__,simple_popup_datavar__,$event)"></vts>
		</template>
		<div v-else>
			<div>context editor</div>
			<div>{{ simple_popup_type__ }}</div>
			<pre>{{ simple_popup_data__ }}</pre>
		</div>
	</div>

	<div v-if="context_menu__" data-context="contextmenu" class="context_menu__" v-bind:style="context_style__">
		<template v-if="context_type__=='all'" >
			<div><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="context_menu_key__" ></div>
			<div class="context_menu_list__" data-context="contextmenu" >
				<div class="context_item" v-on:click.stop="context_select__('None','n')" v-html="context_menu_key_highlight__('None')" ></div>
				<template v-if="context_menu_key__==''" >
					<template v-if="Object.keys(all_factors_stage_wise__[ context_stage_id__ ]).length>0" >
						<div><b style="color:#999;">Things</b></div>
						<template v-for="v,k in all_factors_stage_wise__[ context_stage_id__ ]" >
							<template v-if="context_menu_key_match__(k)" >
								<div v-if="v['t']=='O'||v['t']=='L'" style="display:flex;" >
									<div class="context_item ps-2" v-on:click.stop="context_select__(k,'o')" v-html="context_menu_key_highlight__(k)+ ': <abbr>'+data_types__[v['t']]+'</abbr>'" ></div>
									<div v-if="context_expand_key__!=k" class="context_item_plus__" v-on:click.prevent.stop="context_expand_key__=k" >+</div>
									<div v-if="context_expand_key__==k" class="context_item_plus__" v-on:click.prevent.stop="context_expand_key__=''" >-</div>
								</div>
								<div v-else-if="v['t'] in plugin_data__" style="display:flex;" >
									<div class="context_item ps-2" v-on:click.stop="context_select__(k,'o')" v-html="context_menu_key_highlight__(k)+ ': <abbr>'+v['t']+'</abbr>'" ></div>
								</div>
								<div v-else class="context_item ps-2" v-on:click.stop="context_select__(k,'o')"  v-html="context_menu_key_highlight__(k)+context_get_type_notation__(v)" ></div>
								<template v-if="context_expand_key__==k" >
									<template v-for="v2,k2 in get_object_props_list(context_stage_id__,k)" >
										<template v-if="v['t']=='O'" >
											<div class="context_item ps-2" v-on:click.stop="context_select__(k + '->' + v2['k'],'o')" v-html="k + '->' + v2['k'] + ': <abbr>'+data_types__[v2['t']]+'</abbr>'" ></div>
										</template>
										<template v-if="v['t']=='L'" >
											<div class="context_item ps-2" v-on:click.stop="context_select__(k + '->[]->' + v2['k'],'o')" v-html="k + '->[]->' + v2['k'] + ': <abbr>'+data_types__[v2['t']]+'</abbr>'" ></div>
										</template>
									</template>
								</template>
							</template>
						</template>
					</template>
					<div v-for="grp__,groupi in stages_by_type__"  >
						<template v-if="grp__['group']!='none'" >
						<div><b style="color:#999;">{{ grp__['group'] }}</b></div>
						<div v-for="stagetypei__ in grp__['sub']" class="context_item ps-2" v-on:click.stop="context_select__(stagetypei__,'c')" >{{ stagetypei__ }}</div>
						</template>
					</div>
				</template>
				<template v-else >
					<template v-if="Object.keys(all_factors_stage_wise__[ context_stage_id__ ]).length>0" >
						<div><b style="color:#999;">Things</b></div>
						<template v-for="v,k in all_factors_stage_wise__[ context_stage_id__ ]" >
							<template v-if="context_menu_key_match__(k)" >
								<div v-if="v['t']=='O'||v['t']=='L'" style="display:flex;" >
									<div class="context_item ps-2" v-on:click.stop="context_select__(k,'o')" v-html="context_menu_key_highlight__(k)+ ': <abbr>'+data_types__[v['t']]+'</abbr>'" ></div>
									<div v-if="context_expand_key__!=k" class="context_item_plus__" v-on:click.prevent.stop="context_expand_key__=k" >+</div>
									<div v-if="context_expand_key__==k" class="context_item_plus__" v-on:click.prevent.stop="context_expand_key__=''" >-</div>
								</div>
								<div v-else class="context_item ps-2" v-on:click.stop="context_select__(k,'o')"  v-html="context_menu_key_highlight__(k)+context_get_type_notation__(v)" ></div>
								<template v-if="context_expand_key__==k" >
									<template v-for="v2,k2 in get_object_props_list(context_stage_id__,k)" >
										<template v-if="v['t']=='O'" >
											<div class="context_item ps-2" v-on:click.stop="context_select__(k + '->' + v2['k'],'o')" v-html="k + '->' + v2['k'] + ': <abbr>'+data_types__[v2['t']]+'</abbr>'" ></div>
										</template>
										<template v-if="v['t']=='L'" >
											<div class="context_item ps-2" v-on:click.stop="context_select__(k + '->[]->' + v2['k'],'o')" v-html="k + '->[]->' + v2['k'] + ': <abbr>'+data_types__[v2['t']]+'</abbr>'" ></div>
										</template>
									</template>
								</template>
							</template>
						</template>
					</template>
					<div><b style="color:#999;">Commands</b></div>
					<template v-for="grp__,groupi in stages_by_type__"  >
					<template v-if="grp__['group']!='none'" v-for="stagetypei__ in grp__['sub']" >
						<div class="context_item ps-2" v-if="context_menu_key_match__(stagetypei__)" v-html="context_menu_key_highlight__(stagetypei__)" v-on:click.stop="context_select__(stagetypei__,'c')" ></div>
					</template>
					</template>
				</template>
				<div>{{ context_menu_current_item__ }}</div>
			</div>
		</template>
		<template v-else-if="context_type__=='functions'" >
			<div><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="context_menu_key__" ></div>
			<div class="context_menu_list__" data-context="contextmenu" >
				<template v-if="context_menu_key__==''" >
					<template v-for="ft in ['Text','Number','List','Assoc List','Convert','Date','Cryptography','Miscellaneous']" >
						<div><b style="color:#999;">{{ ft }}</b></div>
						<template v-for="fv,fi in functions__" >
						<div v-if="fv['t']==ft" class="context_item ps-2" v-on:click.stop="context_select__(fi,'function')" >{{ fi }}</div>
						</template>
					</template>
				</template>
				<template v-else>
					<template v-for="fv,fi in functions__" >
					<div class="context_item" v-on:click.stop="context_select__(fi,'function')" v-if="context_menu_key_match__(fi)" v-html="fv['t']+': '+context_menu_key_highlight__(fi)" ></div>
					</template>
				</template>
			</div>
		</template>
		<template v-else-if="context_type__=='vartype'" >
			<div class="context_item" v-on:click.stop="context_select__('S','')" >Static</div>
			<div class="context_item" v-on:click.stop="context_select__('V','')" >Variable</div>
		</template>
		<template v-else-if="context_type__=='datatype'" >
			<template v-if="context_list_filter__.length>0" >
				<div v-for="id in context_list_filter__" v-bind:class="{'context_item':true,'cse':context_value__==id}" v-on:click.stop="context_select__(id,'datatype')" ><div style="width:30px;display: inline-block;" >{{ id }}</div><div style="display: inline; color:gray;" v-if="id in data_types__" >{{ data_types__[ id ] }}</div></div>
			</template>
			<div v-else style="display:flex;gap:20px;" >
				<div>
					<div v-for="id,ii in data_types1__" v-bind:class="{'context_item':true,'cse':context_value__==ii}" v-on:click.stop="context_select__(ii,'datatype')" ><div style="width:30px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
				</div>
				<div>
					<div v-for="id,ii in data_types2__" v-bind:class="{'context_item':true,'cse':context_value__==ii}" v-on:click.stop="context_select__(ii,'datatype')" ><div style="width:30px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
				</div>
			</div>
		</template>
		<template v-else-if="context_type__=='inputfactortypes'" >
			<div v-for="id,ii in input_types__" class="context_item" v-on:click.stop="context_select__(ii,'inputtype')" ><div style="width:30px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
		</template>
		<template v-else-if="context_type__=='inputfactortypes2'" >
			<div v-for="id,ii in input_types2__" class="context_item" v-on:click.stop="context_select__(ii,'inputtype')" ><div style="width:30px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
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
		<template v-else-if="context_type__=='plugins'" >
			<div v-if="Object.keys(plugin_data__).length>5" ><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="context_menu_key__"  ></div>
			<div class="context_menu_list__" data-context="contextmenu" >
				<template v-for="v,k in plugin_data__" >
					<template v-if="context_menu_key_match__(k)" >
						<div class="context_item" v-on:click.stop="context_select__(k,'plugin')" v-html="context_menu_key_highlight__(k)" ></div>
					</template>
				</template>
			</div>
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
		<template v-else-if="context_type__=='plgsub'" >
			<div v-if="context_var_for__ in plugin_data__==false">No Plugin Props found {{ context_var_for__ }}</div>
			<template v-else >
				<div class="context_menu_list__" data-context="contextmenu" >
					<template v-for="v,k in plugin_data__[ context_var_for__ ]['p']" >
						<div class="context_item" v-on:click.stop="context_select__(k,'plgprop')" >{{ k }}</div>
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
		<template v-else-if="context_type__=='operator'" >
			<div class="context_item" v-on:click.stop="context_select__('+','operator')" >+ Addition</div>
			<div class="context_item" v-on:click.stop="context_select__('-','operator')" >- Subtract</div>
			<div class="context_item" v-on:click.stop="context_select__('*','operator')" >* Multiply</div>
			<div class="context_item" v-on:click.stop="context_select__('/','operator')" >/ Divide</div>
			<div class="context_item" v-on:click.stop="context_select__('%','operator')" >% Percent</div>
			<div class="context_item" v-on:click.stop="context_select__('^','operator')" >^ Modulo</div>
			<div class="context_item" v-on:click.stop="context_select__('.','operator')" >. End</div>
		</template>
		<template v-else-if="context_type__=='roperator'" >
			<div class="context_item" v-on:click.stop="context_select__('==','roperator')" >==</div>
			<div class="context_item" v-on:click.stop="context_select__('!=','roperator')" >!=</div>
			<div class="context_item" v-on:click.stop="context_select__('<','roperator')" >&lt;</div>
			<div class="context_item" v-on:click.stop="context_select__('<=','roperator')" >&lt;=</div>
			<div class="context_item" v-on:click.stop="context_select__('>','roperator')" >&gt;</div>
			<div class="context_item" v-on:click.stop="context_select__('>=','roperator')" >&gt;=</div>
			<div class="context_item" v-on:click.stop="context_select__('!','roperator')" >Not</div>
		</template>
		<template v-else-if="context_type__=='mysqloperator'" >
			<div class="context_item" v-on:click.stop="context_select__('=','roperator')" >=</div>
			<div class="context_item" v-on:click.stop="context_select__('!=','roperator')" >!=</div>
			<div class="context_item" v-on:click.stop="context_select__('<','roperator')" >&lt;</div>
			<div class="context_item" v-on:click.stop="context_select__('<=','roperator')" >&lt;=</div>
			<div class="context_item" v-on:click.stop="context_select__('>','roperator')" >&gt;</div>
			<div class="context_item" v-on:click.stop="context_select__('>=','roperator')" >&gt;=</div>
		</template>
		<template v-else-if="context_type__=='ifop'" >
			<div class="context_item" v-on:click.stop="context_select__('and','ifop')" >and</div>
			<div class="context_item" v-on:click.stop="context_select__('or','ifop')" >or</div>
		</template>
		<template v-else-if="context_type__=='padding_modes'" >
			<div class="context_item" v-on:click.stop="context_select__('Left','padding_mode')" >Left</div>
			<div class="context_item" v-on:click.stop="context_select__('Right','padding_mode')" >Right</div>
			<div class="context_item" v-on:click.stop="context_select__('Center','padding_mode')" >Center</div>
		</template>
		<template v-else-if="context_type__=='match_return'" >
			<div class="context_item" v-on:click.stop="context_select__('true','match_return')" >True</div>
			<div class="context_item" v-on:click.stop="context_select__('List','match_return')" >List</div>
			<div class="context_item" v-on:click.stop="context_select__('$0','match_return')" >$0</div>
			<div class="context_item" v-on:click.stop="context_select__('$1','match_return')" >$1</div>
			<div class="context_item" v-on:click.stop="context_select__('$2','match_return')" >$2</div>
			<div class="context_item" v-on:click.stop="context_select__('$3','match_return')" >$3</div>
		</template>
		<template v-else-if="context_type__ in functions_data__" >
			<div class="context_menu_list__" data-context="contextmenu" >
				<template v-for="v in functions_data__[ context_type__ ]" >
					<div class="context_item" v-on:click.stop="context_select__(v,'')" >{{ v }}</div>
				</template>
			</div>
		</template>
		<template v-else-if="context_type__=='labels'" >
			<div class="context_menu_list__" data-context="contextmenu" >
				<template v-if="'length' in label_names__" >
					<div v-if="label_names__.length==0">Labels not found</div>
					<template v-for="v in label_names__" >
						<div class="context_item" v-on:click.stop="context_select__(v,'label')" >{{ v }}</div>
					</template>
				</template>
			</div>
		</template>
		<div v-else>No list configured {{ context_type__ }}</div>
	</div>
	<div id="snackbar" v-if="toasts__.length>0" >
		<div class="snackbard" v-for="v in toasts__">{{ v }}</div>
	</div>
</div>