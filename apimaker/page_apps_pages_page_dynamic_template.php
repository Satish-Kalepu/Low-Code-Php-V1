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
					<div v-bind:class="{'tab_btn':true, 'tab_btn_active':(tab=='structure')}" id="structure">
						<div v-on:click.prevent.stop="open_tab('structure')">Page Structure</div>
					</div>
					<div v-bind:class="{'tab_btn':true, 'tab_btn_active':(tab=='control')}" id="control">
						<div v-on:click.prevent.stop="open_tab('control')">Control</div>
					</div>
				</div>
			</div>
	</div>

	<div v-if="vshow__==false||'dynamicstructure' in page__==false" class="mid_block_div" >Loading...</div>
	<template v-else >

		<iframe v-show="tab=='control'" ref="control_iframe__" id="page_vuejs_control_block_div" class="mid_block_iframe" ></iframe>
		<div v-show="tab=='structure'" class="mid_block_div">

			<div class="mb-2" style="border: 1px solid #ccc;">
				<div style="padding:10px; background-color:#f8f8f8;">Title</div>
				<div style="padding:10px;"><input type="text" v-model="page__['dynamicstructure']['head_tags']['title']" class="form-control form-control-sm"></div>
			</div>

			<div class="mb-2" style="border: 1px solid #ccc;">
				<div style="padding:10px; background-color:#f8f8f8;">Meta Tags</div>
				<div style="padding:10px;">
					<div v-for="vd,vi in page__['dynamicstructure']['head_tags']['meta-names']" style="display:flex; column-gap: 10px;" >
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
					<div v-for="vd,vi in page__['dynamicstructure']['head_tags']['meta-props']" style="display:flex; column-gap: 10px;" >
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
					<div v-for="vd,vi in page__['dynamicstructure']['head_tags']['othertags']" style="display:flex; column-gap: 10px;" >
						<div><input type="button" class="btn btn-outline-danger btn-sm py-0" v-on:click="other_del(vi)" value="X"></div>
						<div style="width:calc( 100% - 50px ) "><textarea class="form-control form-control-sm" v-model="vd['data']"></textarea></div>
					</div>
					<div><div class="btn btn-outline-dark btn-sm py-1" v-on:click="other_add()">+</div></div>
				</div>
			</div>

			<div style="padding:10px; background-color:#f8f8f8; margin-bottom: 10px;">Body Blocks</div>

			<div v-for="vc,vci in page__['dynamicstructure']['blocks']" >
				<div class="mt-2 mb-2"><input type="button" class="btn btn-outline-dark btn-sm py-1" value="Add Block" v-on:click="structure_add_block(vci)" ></div>
				<div class="structure_btn" >
					<div style="float:right;" >
						<input type="button" class="btn btn-outline-danger btn-sm" v-bind:disabled="vci==0" v-on:click="structure_move_up(vci)" value="&uarr;">
						<input type="button" class="btn btn-outline-danger btn-sm" v-bind:disabled="vci==page__['dynamicstructure']['blocks'].length-1" v-on:click="structure_move_down(vci)" value="&darr;" >
						<div class="btn btn-outline-danger btn-sm" v-on:click="structure_delete_block(vci)">X</div>
					</div>
					<div style="display: flex; column-gap:10px; width:80%;">
						<div style="width:60%;">
							<input type="text" class="form-control form-control-sm" placeholder="Block Description" v-model="vc['des']" >
						</div>
						<div>{{ vc['type'] }}</div>
					</div>
				</div>
				<div class="structure_content" > 
					<div class="structure_editor_div" v-bind:id="'structure_'+vci"></div>
				</div>
			</div>
			<div class="mt-2 mb-2"><input type="button" class="btn btn-outline-dark btn-sm py-1" value="Add Block" v-on:click="structure_add_block('bottom')" ></div>

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

	</template>

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