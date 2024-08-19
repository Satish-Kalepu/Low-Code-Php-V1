<div id="app" v-cloak >
	<template v-if="page_type=='codeeditor'" >
		<?php require("page_apps_apis_api_html_logic_2.php"); ?>
	</template>
	<template v-else>

		<div class="leftbar">
			<?php require("page_apps_leftbar.php"); ?>
		</div>

		<div style="position: fixed;left:150px; top:40px; height: 60px; width:calc( 100% - 150px ); background-color: white; overflow: hidden; border-bottom:1px solid #ccc; " >
			<div style="padding: 10px;" >
				<div>
					<h5 class="d-inline">{{ property_type.toUpperCase() }}: {{ (property_type=='api'?s2_iiiiiiiipa['path']:'') }}{{ s2_iiiiiiiipa['name'] }}</h5>
					<a v-bind:href="path+'apis?path='+s2_iiiiiiiipa['path']" class="btn btn-outline-secondary btn-sm me-1" id="top:back" style="float:right;" >Back</a>
					<div v-on:click="s2_mrof_tide_nepo" class="btn btn-outline-dark btn-sm me-1" style="float:right;" id="top:edit" >Edit</div>
					<div class="btn btn-outline-dark btn-sm me-1" style="float:right;" v-html="s2_eton_noisrev_teg()" v-on:click="s2_snoisrev_wohs()" id="top:showversions" ></div>
				</div>
				<div class="d-inline" >{{ s2_iiiiiiiipa['des'] }}</div>
				<div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
				<div v-if="err" class="alert alert-danger" >{{ err }}</div>
			</div>
		</div>

		<div v-if="s2_wwwwwwohsv==false" >Loading...</div>
		<div v-else>
			<div class="codeeditor_block_a" v-if="s2_lluf_rotide_edoc" >
				<div style="padding: 10px;" >
					<?php require("page_apps_apis_api_html_logic_2.php"); ?>
				</div>
			</div>
			<div class="codeeditor_block_b" v-if="s2_lluf_rotide_edoc==false" v-on:click="s2_bat_tset_wohs=false;s2_lluf_rotide_edoc=true" id="logicshow" >
				<div style="padding:5px; text-align:center;"><i class="fa-solid fa-bars" ></i><BR/>L<BR/>O<BR/>G<BR/>I<BR/>C</div>
			</div>
			<div class="test_menu_div_a" v-if="s2_bat_tset_wohs==false&&s2_evas_tsrif==true" v-on:click="s2_bat_tset_wohs=true;s2_lluf_rotide_edoc=false" id="codeshow" >
				<div style="padding:5px; text-align:center;"><i class="fa-solid fa-bars" ></i><BR/>T<BR/>E<BR/>S<BR/>T</div>
			</div>
			<div class="test_menu_div_a" v-else-if="s2_bat_tset_wohs==false" v-on:click="s2_aatad_evas" id="savebtn1" >
				<div style="padding:5px; text-align:center;"><i class="fa-solid fa-bars" ></i><BR/>S<BR/>A<BR/>V<BR/>E</div>
			</div>
			<div class="test_menu_div_b" v-if="s2_bat_tset_wohs" >
				<?php require("page_apps_apis_api_html_test_2.php"); ?>
			</div>
		</div>

	</template>

	<div v-if="s2_ddeen_evas&&page_type!='codeeditor'" class="save_block_a" >
		<input spellcheck="false" type="button" class="btn btn-primary btn-sm" id="save2" v-on:click="s2_aatad_evas" value="SAVE">
	</div>
	<div v-if="s2_ddeen_evas&&page_type=='codeeditor'" class="save_block_aa" >
		<input spellcheck="false" type="button" class="btn btn-primary btn-sm" id="save5" v-on:click="s2_aatad_evas" value="SAVE">
	</div>
	<div v-if="s2_gnivas_wohs&&page_type!='codeeditor'" class="save_block_b">{{ s2_egassem_evas }}</div>
	<div v-if="s2_gnivas_wohs&&page_type=='codeeditor'" class="save_block_bb">{{ s2_egassem_evas }}</div>


	<div id="s2_unem_txetnoc" data-context="contextmenu" class="s2_unem_txetnoc" v-bind:style="s2_elyts_txetnoc">
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
				<div v-for="id in s2_retlif_tsil_txetnoc" v-bind:class="{'context_item':true,'cse':s2_eulav_txetnoc==id}" v-on:click.stop="s2_tceles_txetnoc(id,'datatype')" ><div style="min-width:30px;padding-right:10px;display: inline-block;" >{{ id }}</div><div style="display: inline; color:gray;" v-if="id in s2_sepyt_atad" >{{ s2_sepyt_atad[ id ] }}</div></div>
			</template>
			<div v-else >
				<div style="display:flex;gap:20px;" >
					<div>
						<div v-for="id,ii in s2_1sepyt_atad" v-bind:class="{'context_item':true,'cse':s2_eulav_txetnoc==ii}" v-on:click.stop="s2_tceles_txetnoc(ii,'datatype')" ><div style="min-width:30px;padding-right:10px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
					</div>
					<div>
						<div v-for="id,ii in s2_2sepyt_atad" v-bind:class="{'context_item':true,'cse':s2_eulav_txetnoc==ii}" v-on:click.stop="s2_tceles_txetnoc(ii,'datatype')" ><div style="min-width:30px;padding-right:10px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
					</div>
				</div>
				<div v-if="s2_egats_ngissa_tel_ti_si()" class="context_item" v-on:click.stop="s2_tceles_txetnoc('s2_fffffffftc','datatype')" >Convert to Function</div>
			</div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='inputfactortypes'" >
			<div v-for="id,ii in s2_sepyt_tupni" class="context_item" v-on:click.stop="s2_tceles_txetnoc(ii,'inputtype')" ><div style="min-width:30px;padding-right:10px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
		</template>
		<template v-else-if="s2_epyt_txetnoc=='inputfactortypes2'" >
			<div v-for="id,ii in s2_2sepyt_tupni" class="context_item" v-on:click.stop="s2_tceles_txetnoc(ii,'inputtype')" ><div style="min-width:30px;padding-right:10px;display: inline-block;" >{{ ii }}</div><div style="display: inline; color:gray;" >{{ id }}</div></div>
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




	<div class="modal fade" id="edit_modal" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Edit API Meta Data</h5>
	        <button type="button" id="edit_modal_close_btn" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        	<div>Name</div>
	        	<input spellcheck="false" type="text" class="form-control" v-model="s2_iiipa_tide['name']" placeholder="Name" >
	        	<div>no spaces. no special chars. except -</div>
	        	<div>&nbsp;</div>
	        	<div>Description</div>
	        	<textarea spellcheck="false" class="form-control" v-model="s2_iiipa_tide['des']" ></textarea>
	        	<div v-if="s2_gggggggsmc" class="alert alert-success" >{{ s2_gggggggsmc }}</div>
	        	<div v-if="s2_rrrrrrrrec" class="alert alert-danger" >{{ s2_rrrrrrrrec }}</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
	        <button type="button" class="btn btn-primary btn-sm"  v-on:click="s2_wwwwontide">SAVE</button>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="s2_ladom_tropmi" tabindex="-1" >
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Import Engine</h5>
	        <button type="button" class="btn-close"  id="import_modal_close_btn" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        	<div>Password</div>
	        	<input spellcheck="false" type="text" class="form-control form-control-sm" v-model="s2_drowssap_tropmi" placeholder="Password" >
	        	<div>&nbsp;</div>
	        	<div>File</div>
	        	<input type="file" spellcheck="false" class="form-control form-control-sm" id="s2_elif_tropmi" v-on:change="s2_detceles_tropmi" >
	        	<div>&nbsp;</div>
	        	<div>Version</div>
	        	<div style="display:flex; column-gap:10px;">
		        	<label><input type="radio" v-model="s2_noisrev_tropmi" value="create" > Create New Version</label>
		        	<label><input type="radio" v-model="s2_noisrev_tropmi" value="replace" > Replace Current Version</label>
		        </div>
	        	<div>&nbsp;</div>
	        	<input type="button" spellcheck="false" class="btn btn-outline-dark btn-sm" v-on:click="s2_ipa_tropmi" value="Import" >
	        	<div v-if="s2_gggggggsmi" class="alert alert-success" >{{ s2_gggggggsmi }}</div>
	        	<div v-if="s2_rrrrrrrrei" class="alert alert-danger"  >{{ s2_rrrrrrrrei }}</div>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="s2_ladom_noisrev" tabindex="-1" >
	  <div class="modal-dialog modal-lg modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Versions</h5>
	        <button type="button" class="btn-close"  id="version_modal_close_btn" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      	<table class="table table-bordered table-sm w-auto" >
	      		<tr class="table-active">
	      			<td>-</td><td align="center">Version</td><td>Last Updated</td><td>Active</td><td></td><td></td><td></td>
	      		</tr>
	      		<tr v-for="vd in s2_snoisrev_ipa">
	      			<td align="center">
	      				<a v-if="s2_di_noisrev!=vd['_id']" class="btn btn-outline-dark btn-sm" v-bind:href="'<?=$config_global_apimaker_path ?>apps/<?=$config_param1 ?>/'+page_type+'/<?=$config_param3 ?>/'+vd['_id']" >Edit</a>
	      				<div v-else>Editing</div>
	      			</td>
	      			<td align="center">{{ vd['version'] }}</td>
	      			<td>{{ vd['updated'].substr(0,16) }}</td>
	      			<td><input v-if="s2_di_noisrev_tnerruc!=vd['_id']" type="button" class="btn btn-outline-dark btn-sm" value="Activate" v-on:click="s2_hctiws_noisrev(vd['version'],vd['_id'])" >
	      				<span v-else>Active</span>
	      			</td>
	      			<td><input type="button" class="btn btn-outline-dark btn-sm" value="Clone" v-on:click="s2_enolc_noisrev(vd['_id'])" ></td>
	      			<td>
	      				<input v-if="s2_di_noisrev_tnerruc!=vd['_id']" type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="s2_eteled_noisrev(vd['_id'])" >
	      			</td>
					<td><span v-if="'vn' in vd" >{{ vd['vn'] }}</span></td>
	      		</tr>
	      	</table>
	      	<div v-if="s2_gggggggsmv" class="alert alert-success" >{{ s2_gggggggsmv }}</div>
	        <div v-if="s2_rrrrrrrrev" class="alert alert-danger"  >{{ s2_rrrrrrrrev }}</div>

	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="ses_expired" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Sessions Expired</h4>
					<button type="button" class="close"  id="session_modal_close_btn" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<p>
						Session Expired, Your will be redricted to Home Page
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="s2_ppupop_cod" tabindex="-1" >
	<div class="modal-dialog modal-lg modal-xl">
	<div class="modal-content">
	  <div class="modal-header">
	    <div class="modal-title" ><h5 class="d-inline">Help</h5></div>
	    <button type="button" class="btn-close"  id="doc_modal_close_btn" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  <div class="modal-body" style="position: relative;">
	  	<div v-html="s2_txet_pupop_cod" ></div>
	  </div>
	</div>
	</div>
	</div>


	<div class="modal fade" id="s2_ladom_lmth_pupop" tabindex="-1" >
	  <div class="modal-dialog modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <div class="modal-title" ><h5 class="d-inline">HTML Editor</h5></div>
	        <button type="button" class="btn-close"  id="popup_html_modal_close_btn" data-bs-dismiss="modal" aria-label="Close" v-on:click="s2_etadpu_TH_pupop()"></button>
	      </div>
	      <div class="modal-body" v-bind:data-stagei="s2_di_egats_pupop" style="position: relative;">
	      	<div id="popup_html_editor" style="width:100%; height:350px; position: relative;" ></div>
	      	<div align="center"><div class="btn btn-outline-dark btn-sm" v-on:click="s2_etadpu_TH_pupop()">Update</div></div>
	      </div>
	    </div>
	  </div>
	</div>


	<div class="modal fade" id="s2_ladom_pupop" tabindex="-1" >
	  <div class="modal-dialog modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <div class="modal-title" ><h5 class="d-inline">{{ s2_eltit_pupop }}</h5></div>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"  id="popup_modal_close_btn"></button>
	      </div>
	      <div class="modal-body" id="s2_ydob_ladom_pupop" v-bind:data-stagei="s2_di_egats_pupop" style="position: relative;">
	      		<template v-if="s2_epyt_pupop=='O2'" >
					<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
					<vobject2 v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" v-bind:suggest="s2_tsil_tseggus_pupop" ></vobject2>
					</div>
	      		</template>
	      		<template v-else-if="s2_epyt_pupop=='O'||s2_epyt_pupop=='L'" >
	      			<template v-if="s2_tropmi_pupop==false" >
			      		<div align="right"><div class="btn btn-link btn-sm" style="position:absolute; margin-top:-60px; margin-left:-100px;" v-on:click="s2_tropmi_pupop=true" >Import</div></div>
						<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
			      		<!-- <pre>{{ s2_atad_pupop}}</pre> -->
			        	<vobject v-if="s2_epyt_pupop=='O'" v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" ></vobject>
			        	<vlist v-else-if="s2_epyt_pupop=='L'" v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" ></vlist>
			        	</div>
			        </template>
			        <template v-else >
						<textarea spellcheck="false" style="width:100%; height:100%; min-height: 200px; max-width:calc( 100% - 20px );max-height: 400px; font-family: revert;" v-model="s2_rts_tropmi_pupop" v-on:keydown.tab.prevent.stop ></textarea>
			      		<div class="p-2">
			      			<div class="btn btn-secondary btn-sm" v-on:click="s2_tropmi_pupop=false" >Cancel</div>&nbsp; &nbsp;
							<div class="btn btn-primary btn-sm" v-on:click="s2_atad_nosj_tropmi_pupop" >Import</div>
			      		</div>
			        </template>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='PayLoad'" >
	      			<template v-if="s2_tropmi_pupop==false" >
			      		<div align="right"><div class="btn btn-link btn-sm" style="position:absolute; margin-top:-60px; margin-left:-100px;" v-on:click="s2_tropmi_pupop=true" >Import</div></div>
						<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px ); max-height: 400px;" >
			      		<!--<pre>{{ s2_atad_pupop}}</pre>-->
			        	<vobject_payload v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop"   ></vobject_payload>
			        	</div>
			        </template>
			        <template v-else >
						<textarea spellcheck="false" style="width:100%; height:100%; min-height: 200px; font-family: revert; max-width:calc( 100% - 20px ); max-height: 400px;" v-model="s2_rts_tropmi_pupop" v-on:keydown.tab.prevent.stop ></textarea>
			      		<div class="p-2">
			      			<div class="btn btn-secondary btn-sm" v-on:click="s2_tropmi_pupop=false" >Cancel</div>&nbsp; &nbsp;
							<div class="btn btn-primary btn-sm" v-on:click="s2_daolyap_rof_atad_nosj_tropmi_pupop" >Import</div>
			      		</div>
			        </template>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MongoQ'" >
	        		<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px ); max-height: 400px;" >
	        		<mongoq v-bind:v="s2_atad_pupop" ref="mongoq" refname="mongoq" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" 
					v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" 
	        		 ></mongoq>
	        		</div>
	        	</template>
				<template v-else-if="s2_epyt_pupop=='MongoD'" >
	        		<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
	        		<mongod v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
	        		v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" 
	        		 ></mongod>
	        		</div>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MongoD3'" >
	        		<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
	        		<mongod3 v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
	        		v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" 
	        		 ></mongod3>
	        		</div>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MongoD2'" >
	        		<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
	        		<mongod2 v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
	        		v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" 
	        		 ></mongod2>
	        		</div>
	        	</template>
				<template v-else-if="s2_epyt_pupop=='MongoP'" >
					<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
					<mongop v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
					v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
					v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
					v-bind:ref="s2_ffer_pupop" v-bind:refname="s2_ffer_pupop" 
					v-on:updated="s2_noitpo_detadpu" 
					></mongop>
					</div>
				</template>
				<template v-else-if="s2_epyt_pupop=='MongoP2'" >
					<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
					<mongop2 v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
					v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
					v-bind:datavar="s2_ravatad_pupop" v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
					v-on:updated="s2_noitpo_detadpu" 
					></mongop2>
					</div>
				</template>
	        	<template v-else-if="s2_epyt_pupop=='MySqlQ'" >
	        		<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
	        		<mysqlq v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
	        		v-bind:datavar="s2_ravatad_pupop" 
	        		v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" ></mysqlq>
	        		</div>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MysqlQ'" >
	        		<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
	        		<mysqlq v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" 
	        		v-bind:datavar="s2_ravatad_pupop" 
	        		v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  
	        		v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" 
	        		v-on:updated="s2_noitpo_detadpu" ></mysqlq>
	        		</div>
	        	</template>
				<template v-else-if="s2_epyt_pupop=='MySqlD'" >
					<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
					<mysqld v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" v-on:updated="s2_noitpo_detadpu" ></mysqld>
					</div>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MySqlP'" >
	        		<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
	        		<mysqlp v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" v-on:updated="s2_noitpo_detadpu" ></mysqlp>
	        		</div>
	        	</template>
	        	<template v-else-if="s2_epyt_pupop=='MySqlS'" >
	        		<div class="code_line" style="overflow: auto; max-width:calc( 100% - 20px );max-height: 400px;" >
	        		<mysqls v-bind:v="s2_atad_pupop" v-bind:datafor="s2_rrof_pupop" v-bind:datavar="s2_ravatad_pupop" v-bind:rootdata="s2_eeeeenigne['stages'][ s2_di_egats_pupop ]['d']"  v-bind:vars="s2_esiw_egats_srotcaf_lla[ s2_di_egats_pupop]" v-on:updated="s2_noitpo_detadpu" ></mysqls>
	        		</div>
	        	</template>
	        	<div v-else-if="s2_epyt_pupop=='TT'" >
					<textarea spellcheck="false" style="width:100%; height:100%; white-space: nowrap;  font-family: revert; min-height: 200px; max-width:calc( 100% - 20px ); max-height: 400px;outline:0px;" v-model="s2_atad_pupop" v-on:keydown.tab.prevent.stop v-on:blur="s2_TTTT_pupop()" ></textarea>
					<div align="center"><div class="btn btn-outline-dark btn-sm" v-on:click="s2_etadpu_TT_pupop()">Update</div></div>
	        	</div>
	        	<div v-else-if="s2_epyt_pupop=='HT'" >
					<div id="popup_html_editor">HTML Editor coming sooon!</div>
	        	</div>
	        	<div v-else >Unhandled popup type {{ s2_epyt_pupop }}</div>
	      </div>
	    </div>
	  </div>
	</div>	

	<div v-if="s2_ladom_pupop_elpmis" data-context="contextmenu" class="s2_unem_txetnoc" v-bind:style="s2_elyts_pupop_elpmis"  v-bind:data-stagei="s2_di_egats_pupop_elpmis"  >
		<template v-if="s2_epyt_pupop_elpmis=='hh'" >
			<div v-html="s2_atad_pupop_elpmis" ></div>
		</template>
		<template v-else-if="s2_epyt_pupop_elpmis=='d'" >
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



	<div id="snackbar" v-if="s2_ssssstsaot.length>0" >
		<div class="snackbard" v-for="v in s2_ssssstsaot">{{ v }}</div>
	</div>
</div>