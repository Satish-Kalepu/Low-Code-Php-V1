<div id="app" v-cloak >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>

	<div style="position: fixed;left:150px; top:40px; height: 40px; width:calc( 100% - 150px ); background-color: white; overflow: hidden; " >
		<div style="padding: 5px 10px;" >
			<div class="btn btn-outline-dark btn-sm" style="float:right; padding:4px 5px;"  v-on:click="save_page" >Save Page</div>

			<a v-if="is_single_test_environment__()" v-bind:href="get_test_environment_url__(0)" target="_blank" ><img src="<?=$config_global_apimaker_path ?>edit.png" style="float:right;cursor: pointer; margin-right:20px;" title="Preview" ></a>
			<div v-else v-on:click="previewit()" style="float:right;cursor: pointer; margin-right:20px;" title="Preview" ><img src="<?=$config_global_apimaker_path ?>edit.png" ></div>

			<h5 class="d-inline">Page: {{ page__['name'] }}</h5>

		</div>
	</div>

	<div style="position: fixed;left:150px; top:80px; height: 40px; width:calc( 100% - 150px ); background-color: white; overflow: hidden; " >
			<div class="tabs_nav_bar">
				<div class="tabs_nav_container" id="tabs_container">
					<div v-bind:class="{'tab_btn':true, 'tab_btn_active':(tab=='vue')}" id="vue">
						<div v-on:click.prevent.stop="open_tab('vue')">Vue App</div>
					</div>
					<div v-bind:class="{'tab_btn':true, 'tab_btn_active':(tab=='components')}" id="components">
						<div v-on:click.prevent.stop="open_tab('components')">Vue Components</div>
					</div>
					<div v-bind:class="{'tab_btn':true, 'tab_btn_active':(tab=='structure')}" id="structure">
						<div v-on:click.prevent.stop="open_tab('structure')">Page Structure</div>
					</div>
					<div v-bind:class="{'tab_btn':true, 'tab_btn_active':(tab=='control')}" id="control">
						<div v-on:click.prevent.stop="open_tab('control')">Control</div>
					</div>
					<div v-bind:class="{'tab_btn':true, 'tab_btn_active':(tab=='help')}" id="control">
						<div v-on:click.prevent.stop="open_tab('help')">Help</div>
					</div>
				</div>
			</div>
	</div>

	<div v-show="tab=='control'" >
		<iframe ref="control_iframe__" id="page_vuejs_control_block_div" style="position: fixed; left:150px; top:120px; width:calc( 100% - 150px ); height: calc( 100% - 120px ); padding:10px; overflow: auto;" ></iframe>
	</div>
	<div v-show="tab!='control'" style="position: fixed; left:150px; top:120px; width:calc( 100% - 150px ); height: calc( 100% - 120px ); padding:10px; overflow: auto;">
		<div v-if="vshow__==false||'vuestructure' in page__==false" >Loading...</div>
		<div v-else>

			<div v-show="tab=='vue'" >
				
				<div v-if="'vuestructure' in page__==false" style="color:red;">VueStructure missing</div>
				
				<template v-else >
					<div>App Mount</div>
					<div class="mb-2"><input type="text" class="form-control form-control-sm w-auto" v-model="page__['vuestructure']['element']" placeholder="Tag Selector"></div>
					<div class="col-8 mb-2"><label style="cursor:pointer;"><input type="checkbox" v-model="page__['vuestructure']['hmr']" > Enable Auto-Save and Hot Module Replacement</label></div>

					<div class="block_head" >data: function()</div>
					<div class="block_content" >
						<div v-if="'data' in page__['vuestructure']==false" style="color:red;">
							data block missing
						</div>
						<div class="block_editor_div"  id="main_data" style="position:relative;" ></div>
					</div>
					<div class="block_head" >mounted: function()</div>
					<div class="block_content" >
						<div v-if="'mounted' in page__['vuestructure']==false" style="color:red;">
							mounted block missing
						</div>
						<div class="block_editor_div"  id="main_mounted"  style="position:relative;" ></div>
					</div>
					<div class="block_head" >methods</div>
					<div class="block_content" >
						<div v-if="'methods' in page__['vuestructure']==false" style="color:red;">
							methods block missing
						</div>
						<div class="block_editor_div"  id="main_methods"  style="position:relative;" ></div>
					</div>
					<div class="block_head" >template <label style="cursor:pointer;"> <input type="checkbox" v-model="page__['vuestructure']['template_use']" > Use custom template, Else use body template </label></div>
					<div class="block_content" v-show="page__['vuestructure']['template_use']" >
						<div v-if="'methods' in page__['vuestructure']==false" style="color:red;">
							template block missing
						</div>
						<div class="block_editor_div"  id="main_template"  style="position:relative;" ></div>
					</div>
					<div class="mb-1">&nbsp;</div>

					<div class="block_head" >Router <label style="cursor:pointer;"><input type="checkbox" v-model="page__['vuestructure']['router_enable']" > Enable Vue Router</label> </div>
					<div class="block_content" v-show="page__['vuestructure']['router_enable']" >
						<div class="mb-1"><input type="button" class="btn btn-outline-dark btn-sm" v-on:click="router_add_path()" value="Add Route"></div>
						<table class="table table-bordered table-sm">
							<tbody>
								<tr>
									<td>Path</td>
									<td>Component</td>
									<td style="width:100px;">-</td>
								</tr>
								<template v-for="rd,ri in page__['vuestructure']['router']">
								<tr>
									<td><input type="text" class="form-control form-control-sm" v-model="rd['path']" placeholder="/path"></td>
									<td>
										<select class="form-select form-select-sm" v-model="rd['component']" >
											<option value="none" >None</option>
											<option v-for="cd,ci in page__['vuestructure']['components']" v-bind:value="cd['name']" >{{ cd['name'] }}</option>
										</select>
									</td>
									<td>
										<input type="button" class="btn btn-outline-danger btn-sm py-0 me-1" v-on:click="router_remove_path(ri)" value="X">
										<input type="button" class="btn btn-outline-dark btn-sm py-0 me-1" v-bind:disabled="ri==0" v-on:click="router_move_up(ri)" value="&uarr;">
										<input type="button" class="btn btn-outline-dark btn-sm py-0 me-1" v-bind:disabled="ri==page__['vuestructure']['router'].length-1" v-on:click="router_move_down(ri)" value="&darr;">
									</td>
								</tr>
								</template>
							</tbody>
						</table>


							<!--
							<div v-for="rd,ri in page__['vuestructure']['router']" style="border: 1px solid #ccc; margin-bottom: 10px; padding: 10px;" >
								<div style="display: flex; column-gap:10px; margin-bottom: 10px;" >
									<div style="width:calc( 100% - 50% - 50px );">
										<div>Path</div>
										<div><input type="text" class="form-control form-control-sm" v-model="rd['path']" placeholder="/path"></div>
									</div>
									<div style="width:calc( 100% - 50% - 50px );">
										<div>Component</div>
										<div>
											<select class="form-select form-select-sm" v-model="rd['component']" >
												<option value="none" >None</option>
												<option v-for="cd,ci in page__['vuestructure']['components']" v-bind:value="cd['name']" >{{ cd['name'] }}</option>
											</select>
										</div>
									</div>
									<div>
										<input type="button" class="btn btn-outline-danger btn-sm py-0" v-on:click="router_remove_path(ri)" value="X">
										<input type="button" class="btn btn-outline-dark btn-sm py-0" v-bind:disabled="ri==0" v-on:click="router_move_up(ri)" value="&uarr;">
										<input type="button" class="btn btn-outline-dark btn-sm py-0" v-bind:disabled="ri==page__['vuestructure']['router'].length-1" v-on:click="router_move_down(ri)" value="&darr;">
									</div>
								</div>
								<div class="row mb-2" >
									<div class="col-1" >&nbsp;-</div>
									<div class="col-6" style="border:1px solid #ccc;">
										<div>Meta</div>
										<div v-for="vd,vi in rd['meta']" style="display:flex; column-gap: 5px;" >
											<div><input type="button" value="X" class="btn btn-outline-danger btn-sm py-0" v-on:click="meta_delete(ri,vi)"></div>
											<div><input type="text" v-model="vd['prop']" placeholder="Key" class="form-control form-control-sm"></div>
											<div><input type="text" v-model="vd['value']" placeholder="Value" class="form-control form-control-sm"></div>
										</div>
										<div class="mb-2"><input type="button" value="+" class="btn btn-outline-dark btn-sm py-0" v-on:click="meta_add(ri)"></div>
									</div>
									<div class="col-5" style="border:1px solid #ccc;">
										<div>Props</div>
										<div v-for="vd,vi in rd['props']" style="display:flex; column-gap: 5px;" >
											<div><input type="button" value="X" class="btn btn-outline-danger btn-sm py-0" v-on:click="props_delete(ri,vi)"></div>
											<div><input type="text" v-model="vd['prop']" placeholder="Key" class="form-control form-control-sm"></div>
											<div><input type="text" v-model="vd['value']" placeholder="Value" class="form-control form-control-sm"></div>
										</div>
										<div class="mb-2"><input type="button" value="+" class="btn btn-outline-dark btn-sm py-0" v-on:click="props_add(ri)"></div>
									</div>
								</div>
							</div>
						-->

						<div>router.beforeEach( (from,to) => </div>
						<div class="block_editor_div" id="main_beforeeach" style="position:relative;" ></div>

						<div>router.afterEach( (from,to) => </div>
						<div class="block_editor_div" id="main_aftereach"  style="position:relative;" ></div>
					</div>

					<div style="height:200px;">&nbsp;</div>

				</template>

			</div>
			<div v-show="tab=='components'" >
				<div v-if="'vuestructure' in page__==false" style="color:red;">
					components missing
				</div>
				<template v-else >
					<div class="mb-2 row">
						<div class="col-4"><input type="button" class="btn btn-outline-dark btn-sm py-1" value="Add Component" v-on:click="component_add" ></div>
					</div>
					<div v-for="vc,vci in page__['vuestructure']['components']" class="mb-2" >
						<div class="comp_btn" style="cursor:pointer;" v-on:click="open_comp(vci)">{{ vc['name'] }}</div>
						<div class="comp_content" v-show="comp_tab==vci"> 
							<div style="float:right;">
								<div class="btn btn-outline-danger btn-sm" v-on:click="component_delete(vci)">X</div>
							</div>

							<div class="mb-2" style="display: flex; column-gap:10px;">
								<div style="width:40%;">
									<div>Name</div>
									<div class="mb-1"><input type="text" class="form-control form-control-sm" v-model="vc['name']" placeholder="Component Name" v-on:change="componet_name_fix(vci)" ></div>
								</div>
								<div style="width:50%;">
									<div>Description</div>
									<div class="mb-1"><input type="text" class="form-control form-control-sm" v-model="vc['des']" placeholder="Description" ></div>
								</div>
							</div>

							<div class="block_head" >Data</div>
							<div class="block_content" >
								<div v-if="'data' in vc==false" style="color:red;">
									data block missing
								</div>
								<div class="block_editor_div"  v-bind:id="'component_'+vci+'_data'" style="position:relative;" ></div>
							</div>
							<div class="block_head" >Mounted</div>
							<div class="block_content" >
								<div v-if="'mounted' in vc==false" style="color:red;">
									mounted block missing
								</div>
								<div class="block_editor_div"   v-bind:id="'component_'+vci+'_mounted'"  style="position:relative;" ></div>
							</div>
							<div class="block_head" >Methods</div>
							<div class="block_content" >
								<div v-if="'methods' in vc==false" style="color:red;">
									methods block missing
								</div>
								<div class="block_editor_div"   v-bind:id="'component_'+vci+'_methods'"  style="position:relative;" ></div>
							</div>
							<div class="block_head" >Template</div>
							<div class="block_content" >
								<div v-if="'methods' in vc==false" style="color:red;">
									template block missing
								</div>
								<div class="block_editor_div"   v-bind:id="'component_'+vci+'_template'"  style="position:relative;" ></div>
							</div>
						</div>
					</div>
				</template>
			</div>
			<div v-show="tab=='structure'" >
				<div v-if="'structure' in page__['vuestructure']==false" style="color:red;">
					structure missing
				</div>
				<template v-else >

					<div class="mb-2" style="border: 1px solid #ccc;">
						<div style="padding:10px; background-color:#f8f8f8;">Title</div>
						<div style="padding:10px;"><input type="text" v-model="page__['vuestructure']['structure']['head_tags']['title']" class="form-control form-control-sm"></div>
					</div>

					<div class="mb-2" style="border: 1px solid #ccc;">
						<div style="padding:10px; background-color:#f8f8f8;">Meta Tags</div>
						<div style="padding:10px;">
							<div v-for="vd,vi in page__['vuestructure']['structure']['head_tags']['meta-names']" style="display:flex; column-gap: 10px;" >
								<div><input type="button" class="btn btn-outline-danger btn-sm py-0" v-on:click="meta_name_del(vi)" value="X"></div>
								<div style="width:300px;"><input type="text" class="form-control form-control-sm" v-model="vd['name']"></div>
								<div style="width:calc( 100% - 350px ) "><input type="text" class="form-control form-control-sm" v-model="vd['content']" ></div>
							</div>
							<div><div class="btn btn-outline-dark btn-sm py-1" v-on:click="meta_name_add()">+</div></div>
						</div>
					</div>

					<div class="mb-2" style="border: 1px solid #ccc;">
						<div style="padding:10px; background-color:#f8f8f8;">Meta Properties</div>
						<div style="padding:10px;">
							<div v-for="vd,vi in page__['vuestructure']['structure']['head_tags']['meta-props']" style="display:flex; column-gap: 10px;" >
								<div><input type="button" class="btn btn-outline-danger btn-sm py-0" v-on:click="meta_prop_del(vi)" value="X"></div>
								<div style="width:300px;"><input type="text" class="form-control form-control-sm" v-model="vd['name']"></div>
								<div style="width:calc( 100% - 350px ) "><input type="text" class="form-control form-control-sm" v-model="vd['content']"></div>
							</div>
							<div><div class="btn btn-outline-dark btn-sm py-1" v-on:click="meta_prop_add()">+</div></div>
						</div>
					</div>

					<div class="mb-2" style="border: 1px solid #ccc;">
						<div style="padding:10px; background-color:#f8f8f8;">Head Tags</div>
						<div style="padding:10px;" >
							<div v-for="vd,vi in page__['vuestructure']['structure']['head_tags']['othertags']" style="display:flex; column-gap: 10px;" >
								<div><input type="button" class="btn btn-outline-danger btn-sm py-0" v-on:click="other_del(vi)" value="X"></div>
								<div style="width:calc( 100% - 50px ) "><textarea class="form-control form-control-sm" v-model="vd['data']"></textarea></div>
							</div>
							<div><div class="btn btn-outline-dark btn-sm py-1" v-on:click="other_add()">+</div></div>
						</div>
					</div>

					<div v-for="vc,vci in page__['vuestructure']['structure']['blocks']" >
						<div class="mb-2 mt-2"><input type="button" class="btn btn-outline-dark btn-sm py-1" value="Add Block" v-on:click="structure_add_block(vci)" ></div>
						<div class="structure_btn" >
							<div class="btn btn-outline-danger btn-sm" style="float:right;" v-on:click="structure_delete_block(vci)">X</div>
							<div style="display: flex; column-gap:10px; width:80%;">
								<div style="width:60%;">
									<input type="text" class="form-control form-control-sm" placeholder="Block Description" v-model="vc['des']" >
								</div>
								<div>{{ vc['type'] }}</div>
							</div>
						</div>
						<div class="structure_content" > 
							<div class="structure_editor_div" v-bind:id="'structure_'+vci" style="position:relative;" ></div>
						</div>
					</div>
					<div class="mb-2 mt-2"><input type="button" class="btn btn-outline-dark btn-sm py-1" value="Add Block" v-on:click="structure_add_block('bottom')" ></div>
				</template>
			</div>
			<div v-show="tab=='help'" >
				<p>Variables</p>
				<ul>
					<li>%base_path%: </li>
				</ul>
			</div>
		</div>
	</div>

	<div class="modal fade" id="url_modal" tabindex="-1" >
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Browse Page</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<template v-for="v,i in test_environments__" >
						<p>
							<div>{{ v['t'] }}</div>
							<div><a target="_blank" v-bind:href="get_test_environment_url__(i)" >{{ get_test_environment_url__(i) }}</a></div>
						</p>
					</template>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="popup_modal" tabindex="-1" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ popup_title }}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div v-if="popup_type=='structure_add_block'" >
						<div>Description</div>
						<div><input type="text" class="form-control form-control-sm" placeholder="Block Description" v-model="structure_new_block['des']" ></div>
						<div>&nbsp;</div>
						<div>Type</div>
						<div><label style="cursor: pointer;" ><input type="radio" v-model="structure_new_block['type']" value="html" > HTML </label></div>
						<div><label style="cursor: pointer;" ><input type="radio" v-model="structure_new_block['type']" value="javascript" > Javascript </label></div>
						<div><label style="cursor: pointer;" ><input type="radio" v-model="structure_new_block['type']" value="style" > CSS </label></div>
						<div>&nbsp;</div>
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="ADD" v-on:click="structure_add_block2()" ></div>
					</div>
					<div v-else-if="popup_type=='component_add'" >
						<div>Name</div>
						<div><input type="text" class="form-control form-control-sm" placeholder="Name" v-model="component_new['name']" ></div>
						<div>&nbsp;</div>
						<div>Description</div>
						<div><input type="text" class="form-control form-control-sm" placeholder="Block Description" v-model="component_new['des']" ></div>
						<div>&nbsp;</div>
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="ADD" v-on:click="component_add2()" ></div>
					</div>
					<div v-else>Unknown popup type</div>
				</div>
			</div>
		</div>
	</div>

	<div v-if="float_msg__" style="position:fixed; z-index:500; bottom:10px; right:10px; border-radius:10px; box-shadow: 2px 2px 5px black; background-color: rgba(0,0,220,0.5); padding:10px;" v-on:click.stop.prevent v-html="float_msg__" ></div>
	<div v-if="float_err__" style="position:fixed; z-index:500; bottom:10px; right:10px; border-radius:10px; box-shadow: 2px 2px 5px black; background-color: rgba(220,0,0,0.5); padding:10px;" v-on:click.stop.prevent >
		<div>{{ float_err__ }}</div>
	</div>
	
</div>