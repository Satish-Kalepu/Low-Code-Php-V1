<div id="app" v-cloak >
	<div class="leftbar">
		<?php require("page_apps_leftbar.php"); ?>
	</div>

	<div style="position: fixed;left:150px; top:40px; height: 60px; width:calc( 100% - 150px ); background-color: white; overflow: hidden; border-bottom:1px solid #ccc; " >
		<div style="padding: 10px;" >
			<div>
				<h5 class="d-inline">Function: {{ s2_nnnoitcnuf['name'] }}</h5>
				<div v-on:click="open_edit_form" class="btn btn-default btn-sm" style="float:right;" ><i class="fa-solid fa-square-check" ></i></div>
			</div>
			<div class="d-inline" >{{ s2_nnnoitcnuf['des'] }}</div>
			<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
			<div v-if="err" class="alert alert-danger" >{{ err }}</div>
		</div>
	</div>

	<div v-if="vshow==false" >Loading...</div>
	<div v-else>
		<div class="codeeditor_block_a" v-if="s2_lluf_rotide_edoc" >
			<div style="padding: 10px;" >
				<?php require("page_apps_functions_function_html_logic_2.php"); ?>
			</div>
		</div>
		<div class="codeeditor_block_b" v-if="s2_lluf_rotide_edoc==false" v-on:click="s2_bat_tset_wohs=false;s2_lluf_rotide_edoc=true"  >
			<div style="padding:5px; text-align:center;"><i class="fa-solid fa-bars" ></i><BR/>L<BR/>O<BR/>G<BR/>I<BR/>C</div>
		</div>
		<div class="test_menu_div_a" v-if="s2_bat_tset_wohs==false" v-on:click="s2_bat_tset_wohs=true;s2_lluf_rotide_edoc=false" >
			<div style="padding:5px; text-align:center;"><i class="fa-solid fa-bars" ></i><BR/>T<BR/>E<BR/>S<BR/>T</div>
		</div>
		<div class="test_menu_div_b" v-if="s2_bat_tset_wohs" >
			<?php require("page_apps_functions_function_html_test_2.php"); ?>
		</div>
	</div>


	<div v-if="s2_ddeen_evas" class="save_block_a" >
		<input spellcheck="false" type="button" class="btn btn-primary btn-sm" v-on:click="s2_aatad_evas" value="SAVE">
	</div>
	<div v-if="s2_gnivas_wohs" class="save_block_b">{{ s2_egassem_evas }}</div>  



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

	<div v-if="s2_pupop_tropmi_wohs" style="border-bottom:1px solid #bbcccc; margin-bottom:10px;">
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

	<div class="modal fade" id="s2_ppupop_cod" tabindex="-1" >
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-header">
	    <div class="modal-title" ><h5 class="d-inline">Help</h5></div>
	    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  <div class="modal-body" style="position: relative;">
	  	<div v-html="s2_txet_pupop_cod" ></div>
	  </div>
	</div>
	</div>
	</div>

	<div class="modal fade" id="s2_ladom_pupop" tabindex="-1" >
	  <div class="modal-dialog modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <div class="modal-title" ><h5 class="d-inline">{{ s2_eltit_pupop }}</h5></div>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body" v-bind:data-stagei="s2_di_egats_pupop" style="position: relative;">
	      		<template v-if="s2_epyt_pupop=='O2'" >
					<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
					<vobject2 v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" v-bind:suggest="s2_tsil_tseggus_pupop" ></vobject2>
					</div>
	      		</template>
	      		<template v-else-if="s2_epyt_pupop=='O'||s2_epyt_pupop=='L'" >
	      			<template v-if="s2_tropmi_pupop==false" >
			      		<div align="right"><div class="btn btn-link btn-sm" style="position:absolute; margin-top:-60px; margin-left:-100px;" v-on:click="s2_tropmi_pupop=true" >Import</div></div>
						<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
			      		<!-- <pre>{{ s2_atad_pupop}}</pre> -->
			        	<vobject v-if="s2_epyt_pupop=='O'" v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" ></vobject>
			        	<vlist v-else-if="s2_epyt_pupop=='L'" v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" ></vlist>
			        	</div>
			        </template>
			        <template v-else >
						<textarea spellcheck="false" style="width:100%; height:100%; min-height: 200px; max-width:750px;max-height: 400px;" v-model="s2_rts_tropmi_pupop" v-on:keydown.tab.prevent.stop ></textarea>
			      		<div class="p-2">
			      			<div class="btn btn-secondary btn-sm" v-on:click="s2_tropmi_pupop=false" >Cancel</div>&nbsp; &nbsp;
							<div class="btn btn-primary btn-sm" v-on:click="s2_atad_nosj_tropmi_pupop" >Import</div>
			      		</div>
			        </template>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='PayLoad'" >
	      			<template v-if="s2_tropmi_pupop==false" >
			      		<div align="right"><div class="btn btn-link btn-sm" style="position:absolute; margin-top:-60px; margin-left:-100px;" v-on:click="s2_tropmi_pupop=true" >Import</div></div>
						<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
			      		<!--<pre>{{ s2_atad_pupop}}</pre>-->
			        	<vobject_payload v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop"   ></vobject_payload>
			        	</div>
			        </template>
			        <template v-else >
						<textarea spellcheck="false" style="width:100%; height:100%; min-height: 200px; max-width:750px;max-height: 400px;" v-model="s2_rts_tropmi_pupop" v-on:keydown.tab.prevent.stop ></textarea>
			      		<div class="p-2">
			      			<div class="btn btn-secondary btn-sm" v-on:click="s2_tropmi_pupop=false" >Cancel</div>&nbsp; &nbsp;
							<div class="btn btn-primary btn-sm" v-on:click="s2_atad_nosj_tropmi_pupop" >Import</div>
			      		</div>
			        </template>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MongoQ'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mongoq v-bind:v="s2_atad_pupop" ref="mongoq" refname="mongoq" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" 
					v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" 
	        		 ></mongoq>
	        		</div>
	        	</template>
				<template v-else-if="s2_epyt_pupop=='MongoD'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mongod v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
	        		v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" 
	        		 ></mongod>
	        		</div>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MongoD3'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mongod3 v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
	        		v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" 
	        		 ></mongod3>
	        		</div>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MongoD2'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mongod2 v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
	        		v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" 
	        		 ></mongod2>
	        		</div>
	        	</template>
				<template v-else-if="s2_epyt_pupop=='MongoP'" >
					<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
					<mongop v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
					v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
					v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
					v-bind:ref="s2_ffer_pupop" v-bind:refname="s2_ffer_pupop" 
					v-on:updated="s2_noitpo_detadpu" 
					></mongop>
					</div>
				</template>
				<template v-else-if="s2_epyt_pupop=='MongoP2'" >
					<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
					<mongop2 v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
					v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
					v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
					v-on:updated="s2_noitpo_detadpu" 
					></mongop2>
					</div>
				</template>
	        	<template v-else-if="s2_epyt_pupop=='MySqlQ'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mysqlq v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
	        		v-bind:datavar="s2_ravatad_pupop" 
	        		v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" ></mysqlq>
	        		</div>
	        	</template>
				<template v-else-if="s2_epyt_pupop=='MySqlD'" >
					<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
					<mysqld v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" v-on:updated="s2_noitpo_detadpu" ></mysqld>
					</div>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MySqlP'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mysqlp v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" v-on:updated="s2_noitpo_detadpu" ></mysqlp>
	        		</div>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MySqlS'" >
	        		<div class="code_line" style="overflow: auto; max-width:750px;max-height: 400px;" >
	        		<mysqls v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" v-on:updated="s2_noitpo_detadpu" ></mysqls>
	        		</div>
	        	</template>
	        	<div v-else-if="s2_epyt_pupop=='TT'" >
					<textarea spellcheck="false" style="width:100%; height:100%; white-space: nowrap; min-height: 200px; max-width:750px;max-height: 400px;" v-model="s2_atad_pupop" v-on:keydown.tab.prevent.stop  v-on:blur="s2_noitpo_detadpu" ></textarea>
	        	</div>
	        	<div v-else-if="s2_epyt_pupop=='HT'" >
					<div>HTML Editor coming sooon!</div>
	        	</div>
	        	<div v-else >Unhandled popup type {{ s2_epyt_pupop }}</div>
	      </div>
	    </div>
	  </div>
	</div>	

	<div v-if="s2_ladom_pupop_elpmis" data-context="contextmenu" class="s2_unem_txetnoc" v-bind:style="s2_elyts_pupop_elpmis"  v-bind:data-stagei="s2_di_egats_pupop_elpmis"  >
		<template v-if="s2_epyt_pupop_elpmis=='d'" >
			<vdt v-bind:v="s2_atad_pupop_elpmis" v-bind:datafor="s2_rof_pupop_elpmis" v-bind:datavar="s2_ravatad_pupop_elpmis" v-on:close="s2_ladom_pupop_elpmis=false"  v-on:update="s2_rav_bus_egats_tes(s2_di_egats_pupop_elpmis,s2_ravatad_pupop_elpmis,$event)" ></vdt>
		</template>
		<template v-else-if="s2_epyt_pupop_elpmis=='dt'" >
			<vdtm v-bind:v="s2_atad_pupop_elpmis" v-bind:datafor="s2_rof_pupop_elpmis" v-bind:datavar="s2_ravatad_pupop_elpmis" v-on:close="s2_ladom_pupop_elpmis=false"  v-on:update="s2_rav_bus_egats_tes(s2_di_egats_pupop_elpmis,s2_ravatad_pupop_elpmis,$event)"></vdtm>
		</template>
		<template v-else-if="s2_epyt_pupop_elpmis=='ts'" >
			<vts v-bind:v="s2_atad_pupop_elpmis" v-bind:datafor="s2_rof_pupop_elpmis" v-bind:datavar="s2_ravatad_pupop_elpmis" v-on:close="s2_ladom_pupop_elpmis=false"  v-on:update="s2_rav_bus_egats_tes(s2_di_egats_pupop_elpmis,s2_ravatad_pupop_elpmis,$event)"></vts>
		</template>
		<div v-else>
			<div>context editor</div>
			<div>{{ s2_epyt_pupop_elpmis }}</div>
			<pre>{{ s2_atad_pupop_elpmis }}</pre>
		</div>
	</div>

	<div v-if="s2_unem_txetnoc" data-context="contextmenu" class="s2_unem_txetnoc" v-bind:style="s2_elyts_txetnoc">
		<template v-if="s2_epyt_txetnoc=='all'" >
			<div><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="s2_yek_unem_txetnoc" ></div>
			<div class="s2_tsil_unem_txetnoc" data-context="contextmenu" >
				<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('None','n')" v-html="s2_thgilhgih_yek_unem_txetnoc('None')" ></div>
				<template v-if="s2_yek_unem_txetnoc==''" >
					<template v-if="Object.keys(s2_esiw_egats_srotcaf_lla[ s2_di_egats_txetnoc ]).length>0" >
						<div><b style="color:#999;">Things</b></div>
						<template v-for="v,k in s2_esiw_egats_srotcaf_lla[ s2_di_egats_txetnoc ]" >
							<template v-if="s2_hctam_yek_unem_txetnoc(k)" >
								<div v-if="v['t']=='O'||v['t']=='L'" style="display:flex;" >
									<div class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(k,'o')" v-html="s2_thgilhgih_yek_unem_txetnoc(k)+ ': <abbr>'+s2_sepyt_atad[v['t']]+'</abbr>'" ></div>
									<div v-if="s2_yek_dnapxe_txetnoc!=k" class="s2_sulp_meti_txetnoc" v-on:click.prevent.stop="s2_yek_dnapxe_txetnoc=k" >+</div>
									<div v-if="s2_yek_dnapxe_txetnoc==k" class="s2_sulp_meti_txetnoc" v-on:click.prevent.stop="s2_yek_dnapxe_txetnoc=''" >-</div>
								</div>
								<div v-else-if="v['t'] in s2_atad_nigulp" style="display:flex;" >
									<div class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(k,'o')" v-html="s2_thgilhgih_yek_unem_txetnoc(k)+ ': <abbr>'+v['t']+'</abbr>'" ></div>
								</div>
								<div v-else class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(k,'o')"  v-html="s2_thgilhgih_yek_unem_txetnoc(k)+s2_noitaton_epyt_teg_txetnoc(v)" ></div>
								<template v-if="s2_yek_dnapxe_txetnoc==k" >
									<template v-for="v2,k2 in get_object_props_list(s2_di_egats_txetnoc,k)" >
										<template v-if="v['t']=='O'" >
											<div class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(k + '->' + v2['k'],'o')" v-html="k + '->' + v2['k'] + ': <abbr>'+s2_sepyt_atad[v2['t']]+'</abbr>'" ></div>
										</template>
										<template v-if="v['t']=='L'" >
											<div class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(k + '->[]->' + v2['k'],'o')" v-html="k + '->[]->' + v2['k'] + ': <abbr>'+s2_sepyt_atad[v2['t']]+'</abbr>'" ></div>
										</template>
									</template>
								</template>
							</template>
						</template>
					</template>
					<div v-for="s2_pppppppprg,groupi in s2_epyt_yb_segats"  >
						<template v-if="s2_pppppppprg['group']!='none'" >
						<div><b style="color:#999;">{{ s2_pppppppprg['group'] }}</b></div>
						<div v-for="s2_iepytegats in s2_pppppppprg['sub']" class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(s2_iepytegats,'c')" >{{ s2_iepytegats }}</div>
						</template>
					</div>
				</template>
				<template v-else >
					<template v-if="Object.keys(s2_esiw_egats_srotcaf_lla[ s2_di_egats_txetnoc ]).length>0" >
						<div><b style="color:#999;">Things</b></div>
						<template v-for="v,k in s2_esiw_egats_srotcaf_lla[ s2_di_egats_txetnoc ]" >
							<template v-if="s2_hctam_yek_unem_txetnoc(k)" >
								<div v-if="v['t']=='O'||v['t']=='L'" style="display:flex;" >
									<div class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(k,'o')" v-html="s2_thgilhgih_yek_unem_txetnoc(k)+ ': <abbr>'+s2_sepyt_atad[v['t']]+'</abbr>'" ></div>
									<div v-if="s2_yek_dnapxe_txetnoc!=k" class="s2_sulp_meti_txetnoc" v-on:click.prevent.stop="s2_yek_dnapxe_txetnoc=k" >+</div>
									<div v-if="s2_yek_dnapxe_txetnoc==k" class="s2_sulp_meti_txetnoc" v-on:click.prevent.stop="s2_yek_dnapxe_txetnoc=''" >-</div>
								</div>
								<div v-else class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(k,'o')"  v-html="s2_thgilhgih_yek_unem_txetnoc(k)+s2_noitaton_epyt_teg_txetnoc(v)" ></div>
								<template v-if="s2_yek_dnapxe_txetnoc==k" >
									<template v-for="v2,k2 in get_object_props_list(s2_di_egats_txetnoc,k)" >
										<template v-if="v['t']=='O'" >
											<div class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(k + '->' + v2['k'],'o')" v-html="k + '->' + v2['k'] + ': <abbr>'+s2_sepyt_atad[v2['t']]+'</abbr>'" ></div>
										</template>
										<template v-if="v['t']=='L'" >
											<div class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(k + '->[]->' + v2['k'],'o')" v-html="k + '->[]->' + v2['k'] + ': <abbr>'+s2_sepyt_atad[v2['t']]+'</abbr>'" ></div>
										</template>
									</template>
								</template>
							</template>
						</template>
					</template>
					<div><b style="color:#999;">Commands</b></div>
					<template v-for="s2_pppppppprg,groupi in s2_epyt_yb_segats"  >
					<template v-if="s2_pppppppprg['group']!='none'" v-for="s2_iepytegats in s2_pppppppprg['sub']" >
						<div class="context_item ps-2" v-if="s2_hctam_yek_unem_txetnoc(s2_iepytegats)" v-html="s2_thgilhgih_yek_unem_txetnoc(s2_iepytegats)" v-on:click.stop="s2_tceles_txetnoc(s2_iepytegats,'c')" ></div>
					</template>
					</template>
				</template>
				<div>{{ s2_meti_tnerruc_unem_txetnoc }}</div>
			</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='functions'" >
			<div><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="s2_yek_unem_txetnoc" ></div>
			<div class="s2_tsil_unem_txetnoc" data-context="contextmenu" >
				<template v-if="s2_yek_unem_txetnoc==''" >
					<template v-for="ft in ['Text','Number','List','Assoc List','Convert','Date','Cryptography','Miscellaneous']" >
						<div><b style="color:#999;">{{ ft }}</b></div>
						<template v-for="fv,fi in s2_ssnoitcnuf" >
						<div v-if="fv['t']==ft" class="context_item ps-2" v-on:click.stop="s2_tceles_txetnoc(fi,'function')" >{{ fi }}</div>
						</template>
					</template>
				</template>
				<template v-else>
					<template v-for="fv,fi in s2_ssnoitcnuf" >
					<div class="context_item" v-on:click.stop="s2_tceles_txetnoc(fi,'function')" v-if="s2_hctam_yek_unem_txetnoc(fi)" v-html="fv['t']+': '+s2_thgilhgih_yek_unem_txetnoc(fi)" ></div>
					</template>
				</template>
			</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='vartype'" >
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('S','')" >Static</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('V','')" >Variable</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='datatype'" >
			<template v-if="s2_retlif_tsil_txetnoc.length>0" >
				<div v-for="id in s2_retlif_tsil_txetnoc" v-bind:class="{'context_item':true,'cse':s2_eulav_txetnoc==id}" v-on:click.stop="s2_tceles_txetnoc(id,'datatype')" ><div style="width:30px;display: inline-block;" >{{ id }}</div><div style="display: inline; color:gray;" v-if="id in s2_sepyt_atad" >{{ s2_sepyt_atad[ id ] }}</div></div>
			</template>
			<div v-else style="display:flex;gap:20px;" >
				<div>
					<div v-for="id,ii in s2_1sepyt_atad" v-bind:class="{'context_item':true,'cse':s2_eulav_txetnoc==ii}" v-on:click.stop="s2_tceles_txetnoc(ii,'datatype')" ><div style="width:30px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
				</div>
				<div>
					<div v-for="id,ii in s2_2sepyt_atad" v-bind:class="{'context_item':true,'cse':s2_eulav_txetnoc==ii}" v-on:click.stop="s2_tceles_txetnoc(ii,'datatype')" ><div style="width:30px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
				</div>
			</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='inputfactortypes'" >
			<div v-for="id,ii in s2_sepyt_tupni" class="context_item" v-on:click.stop="s2_tceles_txetnoc(ii,'inputtype')" ><div style="width:30px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='inputfactortypes2'" >
			<div v-for="id,ii in s2_2sepyt_tupni" class="context_item" v-on:click.stop="s2_tceles_txetnoc(ii,'inputtype')" ><div style="width:30px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='list'" >
			<div v-for="id in s2_tsil_txetnoc" class="context_item" v-on:click.stop="s2_tceles_txetnoc(id,'')" >{{ id }}</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='list2'" >
			<template v-if="'list2' in s2_atad_labolg" >
				<template v-if="typeof(s2_atad_labolg['list2'])=='object'" >
					<div v-for="fd,fi in s2_atad_labolg['list2']" class="context_item" v-on:click.stop="s2_tceles_txetnoc(fd['k'],'')" >{{ fd['k'] + ': ' + fd['t'] }}</div>
				</template>
				<div v-else >List values incorrect</div>
			</template>
			<div v-else >List not defined</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='vars'" >
			<div v-if="Object.keys(s2_esiw_egats_srotcaf_lla[ s2_di_egats_txetnoc ]).length==0">No vars found</div>
			<template v-else >
				<div v-if="Object.keys(s2_esiw_egats_srotcaf_lla[ s2_di_egats_txetnoc ]).length>5" ><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="s2_yek_unem_txetnoc"  ></div>
				<div class="s2_tsil_unem_txetnoc" data-context="contextmenu" >
					<template v-for="v,k in s2_esiw_egats_srotcaf_lla[ s2_di_egats_txetnoc ]" >
						<template v-if="s2_hctam_yek_unem_txetnoc(k)" >
							<div v-if="v['t']=='O'" style="display:flex;" >
								<div class="context_item" v-on:click.stop="s2_tceles_txetnoc(k,'var')" v-html="s2_thgilhgih_yek_unem_txetnoc(k)+ ': <abbr>'+s2_sepyt_atad[v['t']]+'</abbr>'" ></div>
								<div v-if="s2_yek_dnapxe_txetnoc!=k" class="s2_sulp_meti_txetnoc" v-on:click.prevent.stop="s2_yek_dnapxe_txetnoc=k" >+</div>
								<div v-if="s2_yek_dnapxe_txetnoc==k" class="s2_sulp_meti_txetnoc" v-on:click.prevent.stop="s2_yek_dnapxe_txetnoc=''" >-</div>
							</div>
							<div v-else-if="v['t'] in s2_atad_nigulp" style="display:flex;" >
								<div class="context_item" v-on:click.stop="s2_tceles_txetnoc(k,'var')" v-html="s2_thgilhgih_yek_unem_txetnoc(k)+ ': <abbr>'+v['t']+'</abbr>'" ></div>
							</div>
							<div v-else class="context_item" v-on:click.stop="s2_tceles_txetnoc(k,'var')" v-html="s2_thgilhgih_yek_unem_txetnoc(k)+s2_noitaton_epyt_teg_txetnoc(v)" ></div>
							<template v-if="s2_yek_dnapxe_txetnoc==k" >
								<template v-for="v2,k2 in get_object_props_list(s2_di_egats_txetnoc,k)" >
									<div class="context_item" v-on:click.stop="s2_tceles_txetnoc(k + '->' + v2['k'],'var')" v-html="k + '->' + v2['k'] + ': <abbr>'+s2_sepyt_atad[v2['t']]+ '</abbr>'" ></div>
								</template>
							</template>
						</template>
					</template>
				</div>
			</template>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='plugins'" >
			<div v-if="Object.keys(s2_atad_nigulp).length>5" ><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="s2_yek_unem_txetnoc"  ></div>
			<div class="s2_tsil_unem_txetnoc" data-context="contextmenu" >
				<template v-for="v,k in s2_atad_nigulp" >
					<template v-if="s2_hctam_yek_unem_txetnoc(k)" >
						<div class="context_item" v-on:click.stop="s2_tceles_txetnoc(k,'plugin')" v-html="s2_thgilhgih_yek_unem_txetnoc(k)" ></div>
					</template>
				</template>
			</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='varsub'" >
			<div v-if="s2_rof_rav_txetnoc in s2_seitreporp_tcejbo_gifnoc==false">No Props found {{ s2_rof_rav_txetnoc }}</div>
			<template v-else >
				<div class="s2_tsil_unem_txetnoc" data-context="contextmenu" >
					<template v-for="v,k in s2_seitreporp_tcejbo_gifnoc[ s2_rof_rav_txetnoc ]" >
						<div class="context_item" v-on:click.stop="s2_tceles_txetnoc(k,'prop')" >{{ k }}</div>
					</template>
				</div>
			</template>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='plgsub'" >
			<div v-if="s2_rof_rav_txetnoc in s2_atad_nigulp==false">No Plugin Props found {{ s2_rof_rav_txetnoc }}</div>
			<template v-else >
				<div class="s2_tsil_unem_txetnoc" data-context="contextmenu" >
					<template v-for="v,k in s2_atad_nigulp[ s2_rof_rav_txetnoc ]['p']" >
						<div class="context_item" v-on:click.stop="s2_tceles_txetnoc(k,'plgprop')" >{{ k }}</div>
					</template>
				</div>
			</template>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='boolean'" >
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('true','')" >true</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('false','')" >false</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='order'" >
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('a-z','')" >a-z</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('z-a','')" >z-a</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='things'" >
			<div v-for="td,ti in s2_desu_sgniht" class="context_item" v-on:click.stop="s2_tceles_txetnoc(ti,'thing')" >{{ ti }}</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='thing'" >
			<div>{{ s2_gniht_txetnoc }}</div>
			<template v-if="s2_gniht_txetnoc in s2_tsil_gniht_txetnoc" >
				<template v-if="s2_tsil_gniht_txetnoc[s2_gniht_txetnoc].length>5" >
					<div><input spellcheck="false" type="text" id="contextmenu_key1"  data-context="contextmenu" data-context-key="contextmenu"  class="form-control form-control-sm" v-model="s2_yek_unem_txetnoc"  ></div>
				</template>
			</template>
			<div class="s2_tsil_unem_txetnoc" data-context="contextmenu" >
				<!--<pre>{{ s2_tsil_gniht_txetnoc }}</pre>-->
				<div v-if="s2_gsm_gniht_txetnoc" class="text-success" >{{ s2_gsm_gniht_txetnoc }}</div>
				<div v-if="s2_rre_gniht_txetnoc" class="text-danger" >{{ s2_rre_gniht_txetnoc }}</div>
				<template v-if="s2_gniht_txetnoc in s2_tsil_gniht_txetnoc" >
					<template v-for="fv,fi in s2_tsil_gniht_txetnoc[ s2_gniht_txetnoc ]" >
						<div v-if="s2_hctam_yek_unem_txetnoc(fv['l']['v'])" class="context_item" v-on:click.stop="s2_tceles_txetnoc(fv,s2_epyt_txetnoc)" v-html="s2_thgilhgih_gniht_unem_txetnoc(fv)" ></div>
					</template>
				</template>
				<div v-else>List undefined</div>
			</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='operator'" >
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('+','operator')" >+ Addition</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('-','operator')" >- Subtract</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('*','operator')" >* Multiply</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('/','operator')" >/ Divide</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('%','operator')" >% Percent</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('^','operator')" >^ Modulo</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('.','operator')" >. End</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='roperator'" >
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('==','roperator')" >==</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('!=','roperator')" >!=</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('<','roperator')" >&lt;</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('<=','roperator')" >&lt;=</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('>','roperator')" >&gt;</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('>=','roperator')" >&gt;=</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('!','roperator')" >Not</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='mysqloperator'" >
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('=','roperator')" >=</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('!=','roperator')" >!=</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('<','roperator')" >&lt;</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('<=','roperator')" >&lt;=</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('>','roperator')" >&gt;</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('>=','roperator')" >&gt;=</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='ifop'" >
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('and','ifop')" >and</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('or','ifop')" >or</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='padding_modes'" >
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('Left','padding_mode')" >Left</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('Right','padding_mode')" >Right</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('Center','padding_mode')" >Center</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='match_return'" >
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('true','match_return')" >True</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('List','match_return')" >List</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('$0','match_return')" >$0</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('$1','match_return')" >$1</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('$2','match_return')" >$2</div>
			<div class="context_item" v-on:click.stop="s2_tceles_txetnoc('$3','match_return')" >$3</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc in s2_atad_snoitcnuf" >
			<div class="s2_tsil_unem_txetnoc" data-context="contextmenu" >
				<template v-for="v in s2_atad_snoitcnuf[ s2_epyt_txetnoc ]" >
					<div class="context_item" v-on:click.stop="s2_tceles_txetnoc(v,'')" >{{ v }}</div>
				</template>
			</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='labels'" >
			<div class="s2_tsil_unem_txetnoc" data-context="contextmenu" >
				<template v-if="'length' in s2_seman_lebal" >
					<div v-if="s2_seman_lebal.length==0">Labels not found</div>
					<template v-for="v in s2_seman_lebal" >
						<div class="context_item" v-on:click.stop="s2_tceles_txetnoc(v,'label')" >{{ v }}</div>
					</template>
				</template>
			</div>
		</template>
		<div v-else>No list configured {{ s2_epyt_txetnoc }}</div>
	</div>
	<div id="snackbar" v-if="s2_ssssstsaot.length>0" >
		<div class="snackbard" v-for="v in s2_ssssstsaot">{{ v }}</div>
	</div>
</div>