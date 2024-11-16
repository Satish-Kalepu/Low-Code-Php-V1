<div id="app" v-cloak >
	<div class="leftbar leftbar_pages" >
		<div style=" height: 40px;overflow: hidden; border-bottom:1px solid #ccc; " >
			<a class="left_btn" v-bind:href="path+'pages'">Back to Pages</a>
		</div>
		<div>
		<?php require("page_apps_pages_page_html_leftbar.html"); ?>
		</div>
	</div>

	<div style="position: fixed;left:150px; top:40px; height: 40px; width:calc( 100% - 150px ); background-color: white; overflow: hidden; border-bottom:1px solid #ccc; " >
		<div style="padding: 5px 10px;" >
			<div class="btn btn-outline-dark btn-sm" style="float:right; padding:4px 5px;"  v-on:click="save_page" >Save Page</div>

			<a v-if="vurls_list.length==1" v-bind:href="vurls_list[0]" target="_blank" ><img src="<?=$config_global_apimaker_path ?>edit.png" style="float:right;cursor: pointer; margin-right:20px;" title="Preview" ></a>
			<div v-else v-on:click="previewit()" style="float:right;cursor: pointer; margin-right:20px;" title="Preview" ><img src="<?=$config_global_apimaker_path ?>edit.png" ></div>

			<h5 class="d-inline">Page: {{ page__['name'] }}</h5>

		</div>
	</div>

	<div v-if="vshow__==false" >Loading...</div>
	<div v-else>

		<div v-show="edit_tab=='html'" >

			<iframe ref="editor_iframe__"  class="editor_block_a" id="editor_block_a" ></iframe>
			<div class="editor_border_left" data-item="editor_border_left" >
				<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M14 15L17 12L14 9M10 9L7 12L10 15" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<div style="position: absolute; width: 10px;height: 10px; overflow: hidden;" >
				<?php require("page_apps_pages_page_html_editor_template.html"); ?>
			</div>

		</div>
		<div v-show="edit_tab=='source'" >
			<div id="page_source_block" style="position: fixed; font-size:1rem; left: 160px; top:90px; height:calc( 100% - 100px - 40px ); width:calc( 100% - 170px ); background-color: #f8f8f8; " ></div>
		</div>
		<div v-show="edit_tab=='sourcescript'" >
			<div id="page_script_block" style="position: fixed; font-size:1rem; left: 160px; top:90px; height:calc( 100% - 100px - 40px ); width:calc( 100% - 170px ); background-color: #f8f8f8; " ></div>
		</div>
		<div v-show="edit_tab=='control'" >
			<iframe ref="control_iframe__"  style="position: fixed; left: 150px; top:80px; height:calc( 100% - 90px ); width:calc( 100% - 150px ); "  id="editor_block_aa" ></iframe>
		</div>

	</div>


	<div class="save_block_a" v-if="edit_tab!='control'" >
		<div style=" display: inline-block; padding: 3px; margin-left: 10px;margin-right: 10px;" ><div class="btn btn-outline-dark btn-sm"  v-on:click="save_page" >SAVE</div></div>
		<div style=" display: inline-block; padding: 3px;" >
			<div v-if="msg__" class="text-success px-3" >{{ msg__ }}</div>
			<div v-if="err__" class="text-danger px-3" >{{ err__ }}</div>
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

	<div class="modal fade" id="tag_settings_popup" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="font-size:1rem;">{{ tag_settings_popup_title }}</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

					<div v-if="tag_settings_type=='DatabaseTable'" >
								<page_databasetable v-bind:tag="focused_app" v-on:updated="focused_app_update_event($event)"></page_databasetable>
					</div>
					<div v-else-if="tag_settings_type=='AuthDefault'" v-on:updated="focused_app_update_event($event)" >
								<page_auth_default v-bind:tag="focused_app"></page_auth_default>
					</div>
					<div v-else-if="tag_settings_type=='new'" >
						<div v-for="tt,ti in config_tags">
							<div>{{ ti }}:</div>
							<div v-for="td in tt" class="btn btn-outline-dark btn-sm" v-on:click="insert_item_at_location(td)" >{{ td }}</div>
						</div>
						<hr/>
						<div>Raw HTML:</div>
						<textarea class="form-control" id="raw_html_block" v-model="raw_html" style="height: 200px;resize:both;"></textarea>
						<div><div class="btn btn-outline-dark btn-sm" v-on:click="insert_item_at_location('raw')"  >Insert</div></div>
					</div>
					<!-- <div v-else-if="tag_settings_type=='A'" >
						<div>URL:</div>
						<div><input type="text" class="form-control form-control-sm" v-model="anchor_href" placeholder="URL" ></div>
						<div>Text:</div>
						<div><input type="text" class="form-control form-control-sm" v-model="anchor_text" placeholder="Content" ></div>
						<div>&nbsp;</div>
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="Update" v-on:click.prevent.stop="anchor_create" ></div>
						<div><input type="button" class="btn btn-outline-danger btn-sm" value="Remove Link" v-on:click.prevent.stop="anchor_remove" ></div>
					</div> -->
					<div v-else-if="tag_settings_type=='MakeLink'"  >
						<div>URL:</div>
						<div><input type="text" class="form-control form-control-sm" v-model="anchor_href" placeholder="URL" ></div>
						<div>Text:</div>
						<div><input type="text" class="form-control form-control-sm" v-model="anchor_text" placeholder="Content" ></div>
						<div>&nbsp;</div>
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="Create" v-on:click.prevent.stop="anchor_create" ></div>
					</div>
					<!-- 
					<div style="border: 1px solid #ccc; margin-bottom: 10px;">
						<div style="padding: 5px; background-color: #f0f0f0;" >Settings</div>
						<div style="padding:5px;">
							<div>Class:</div>
							<input type="text" class="form-control form-control-sm" v-model="focused_className">
							<div v-if="Object.keys(focused_attributes).length>0" >
								<div>Attributes:</div>
								<div v-for="v,av in focused_attributes" style="border:1px solid #ccc; margin:2px;" >
									<div>{{ av }}</div>
									<input type="text" class="form-control form-control-sm" v-model="v">
								</div>
							</div>
							<div><input type="button" class="btn btn-outline-dark btn-sm" value="Update" ></div>
						</div>
					</div>
					 -->
					<div v-else-if="tag_settings_type!='new'&&focused_type&&focused_selection==false" style=" margin-bottom: 10px;">

						<div v-if="focused_tree.length>0" >
							<div v-if="focused_tree.length>5" class="tag_btn" v-on:click="set_focus_to(5)" >{{ focused_tree[5]['a'] }}</div>
							<div v-if="focused_tree.length>4" class="tag_btn" v-on:click="set_focus_to(4)" >{{ focused_tree[4]['a'] }}</div>
							<div v-if="focused_tree.length>3" class="tag_btn" v-on:click="set_focus_to(3)" >{{ focused_tree[3]['a'] }}</div>
							<div v-if="focused_tree.length>2" class="tag_btn" v-on:click="set_focus_to(2)" >{{ focused_tree[2]['a'] }}</div>
							<div v-if="focused_tree.length>1" class="tag_btn" v-on:click="set_focus_to(1)" >{{ focused_tree[1]['a'] }}</div>
							<div v-if="focused_tree.length>0" class="tag_btn tag_btn_a" >{{ focused_tree[0]['a'] }}</div>
						</div>

<!-- 						<ul class="nav nav-tabs">
							<li class="nav-item">
							<a v-bind:class="{'nav-link':true, 'active':tag_settings_tab=='html'}" href="#" v-on:click="tag_settings_tab='html'">Html Edit</a>
							</li>
							<li class="nav-item">
							<a v-bind:class="{'nav-link':true, 'active':tag_settings_tab=='settings'}" href="#" v-on:click="tag_settings_tab='settings'">Tag Settings</a>
							</li>
							<li class="nav-item" v-if="focused_img">
							<a v-bind:class="{'nav-link':true, 'active':tag_settings_tab=='IMG'}" href="#" v-on:click="tag_settings_tab='IMG'">Image Settings</a>
							</li>
							<li class="nav-item" v-if="focused_td">
							<a v-bind:class="{'nav-link':true, 'active':tag_settings_tab=='TD'}" href="#" v-on:click="tag_settings_tab='TD'">Table Cell Settings</a>
							</li>
							<li class="nav-item" v-if="focused_table">
							<a v-bind:class="{'nav-link':true, 'active':tag_settings_tab=='TABLE'}" href="#" v-on:click="tag_settings_tab='TABLE'">Table Settings</a>
							</li>
						</ul> -->

						<div style="padding:10px; border:1px solid #ccc;">
							<div>Markup</div>
							<div id="raw_html_block" style="border:1px solid #ccc; display: relative; width:100%; font-size:1.1rem; height:200px;" ></div>
							<!-- <textarea class="form-control form-control-sm" style="min-height: 200px;" v-model="tag_settings_html"></textarea> -->
							<!-- <div>----------</div> -->
							<div><input type="button" class="btn btn-outline-dark btn-sm" value="Update" v-on:click="tag_settings_html_update" ></div>
						</div>
						<div style="padding:10px; border:1px solid #ccc;">
								<div>Tag Attributes:</div>
								<table class="table table-bordered table-sm">
									<tr>
										<td width="150">class</td>
										<td>{{ focused_attributes['class'] }}</td>
										<td></td>
									</tr>
									<tr>
										<td width="150">style</td>
										<td>{{ focused_attributes['style'] }}</td>
										<td></td>
									</tr>
									<template v-for="ad,ai in focused_attributes">
									<tr v-if="ai!='class'&&ai!='style'">
										<td>{{ ai }}</td>
										<td><input type="text" class="form-control form-control-sm" v-bind:value="focused_attributes[ai]" ></td>
										<td><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="del_tag_attribute(ai)" ></td>
									</tr>
									</template>
									<tr>
										<td><input type="text" class="form-control form-control-sm" v-model="html_tag_attribute" placeholder="New Attribute"></td>
										<td><input type="button" class="btn btn-outline-dark btn-sm" value="+" v-on:click="add_tag_attribute()" ></td>
										<td></td>
									</tr>
								</table>

						</div>
						<div v-show="focused_img" style="padding:10px; border:1px solid #ccc;">
							<div>Image Settings</div>
						</div>
						<div v-show="focused_td" style="padding:10px; border:1px solid #ccc;">
							<div>Table Cell Settings</div>
						</div>
						<div v-show="focused_table" style="padding:10px; border:1px solid #ccc;">
							<div>Table Settings</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="url_modal" tabindex="-1" >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Browse/Download File</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
          	<template v-if="typeof(vurls)=='object'" >
              <template v-if="'cloud' in vurls" >
                <p>Cloud Hosting: </p>
                <p>
                  <a target="_blank" v-bind:href="vurls['cloud']" >{{ vurls['cloud'] }}</a>
                </p>
                <template v-if="'alias' in vurls" >
                  <p>Alias domain:</p>
                  <p>
                    <a target="_blank" v-bind:href="vurls['alias']" >{{ vurls['alias'] }}</a>
                  </p>
                </template>
              </template>
              <template v-if="'domains' in vurls" >
                <p>Custom Hosting: </p>
                <p v-for="u in vurls['domains']" >
                  <a target="_blank" v-bind:href="u" >{{ u }}</a>
                </p>
              </template>
            </template>
          </div>
        </div>
      </div>
    </div>
	
</div>