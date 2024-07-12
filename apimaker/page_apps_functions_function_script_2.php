<script>
<?php 

	// functions list
	require("page_apps_apis_api_functions_2.js");
	require("page_apps_apis_api_functions2_2.js");
	require("page_apps_apis_api_stage_params_2.js");
	require("page_apps_apis_api_plugins_2.js");

	$components = [
		"input_object", "input_values", 
		"inputtextbox", "inputtextbox2", 
		"varselect", "varselect2", "pluginselect",
		"vobject", "vobject2", "vobject_payload", "vlist", 
		"vfield", "vfield_payload", 
		"plugin_database",
		"vdt", "vdtm", "vts",
		"thing", 
		"mongodbv1", "mongoq", "mongop", "mongop2", "mongod", "mongod2", "mongod3", "mongoq_field", "mongop_field",
		"mysqldbv1", "mysqlq", "mysqlp", "mysqld", "mysqls", "mysql_field",
		"internal_table",
		"httprequest",
	];
	$plugins = [
		//"Date"
	];
	foreach( $components as $i=>$j ){
		require($apps_folder."/" . $j . ".js");
	}

?>
const temp1 = {
	template: `<div>temp1 component</div>`
};

String.prototype.toProperCase = function(){
    return this.replace(/\w\S*/g, function(txt){
    	return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
	});
};


var app = Vue.createApp({
	data(){
		return {
			path: '<?=$config_global_apimaker_path ?>apps/<?=$config_param1 ?>/',
			s2_atad_labolg: {"s":"sss"},
			msg: "",
			err: "",
			cmsg: "",
			cerr: "",
			s2_pppppppppa: <?=json_encode($app) ?>,
			s2_nnnoitcnuf: <?=json_encode($function) ?>,
			edit_function: {},
			edit_modal: false,
			token: "",
			s2_eeeeenigne: {},
			vshow: false,
			settings: {},

			s2_ssssstsaot: [],

			"s2_tsoh_revres"			: "<?=$config_page_domain ?>",
			"s2_nnnnoisrev"			: "<?=$function["_id"] ?>",
			"versions_list"			: {},
			"s2_selbat_ipa"			: {},
			"s2_selbat_cimanyd_ipa"		: {},
			"s2_selbat_citsale_ipa"		: {},
			"s2_selbat_sider_ipa"		: {},
			"s2_sgniht_ipa"			: {},
			"s2_eeeeeeegap"			: {},
			"s2_iiiiiegats": -1,
			"s2_stsil_saila"			: {},
			"s2_skcolb_ipa"			: {},
			"s2_selcitra_ipa"		: {},
			"s2_tsil_selif"			: {},
			"test1111"			: 11111,
			"s2_mrof_rotcaf_tupni_dda_wohs"	: false,
			"s2_stnemele_lmth"		: {},
			"s2_ddekcol_si"			: false,
			"s2_rrrrrorrev"			: "",
			"s2_srotcaf_lla"			: {},
			"s2_esiw_egats_srotcaf_lla"	: {},
			"s2_gnivas_wohs"			: false,
			"s2_egassem_evas"		: "Saving..",
			"s2_ddeen_evas"			: false,
			"s2_segats_wohs"			: true,
			"s2_bat_tset_wohs"		: false,
			"s2_lluf_rotide_edoc"	: true,
			"s2_tttttttset"				: {
				"domain": "",
				"path": "",
				"factors": {"t":"O", "v": {}}
			},
			"s2_sutats_tset"			: "",
			"s2_rorre_tset"			: "",
			"s2_esnopser_tset"		: false,
			"s2_wohs_sredaeh_tset"		: false,
			"s2_gggol_tset"			: [],
			"s2_gubed_tset"			: false,
			"s2_gnitiaw_tset"		: false,
			"s2_lllru_tset"			: "",
			"s2_wweiv_nosj"			: false,
            "s2_sutats_kcol"			: 0,
            "s2_desu_cnysa"			: false,
            "s2_piks_cnysa"			: true,
			"s2_ddddddnarv"				: "s_" + ( Math.random() * 1000000 ).toFixed(0),
			"s2_seman_lebal"			: [],
			"s2_seman_egats"			: {},
			"s2_atad_snoitcnuf"		: s2_atad_snoitcnuf_gifnoc,
			"s2_ssnoitcnuf"			: s2_snoitcnuf_gifnoc,
			"s2_seitreporp_tcejbo_gifnoc" 	: s2_seitreporp_tcejbo_gifnoc,
			"s2_atad_nigulp"			: s2_snigulp_tluafed_gifnoc,
			"s2_snoitcnuf_sider"		: {},
			"s2_fo_wen_dda_wohs"		: false,
			"s2_raey_tnerruc"		: "2020",
			"s2_yadot_etad"			: "2020-01-01",
			"s2_eeemitetad"			: "2020-01-01 01:01:01",
    		"s2_ssssskcehc"			: [],
    		"s2_smeti_dekcehc"		: 0,
    		"s2_ssssssgalf"			: {},
			"test_url_main"			: "",
			"s2_sepyt_atad"		: {
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
			"s2_1sepyt_atad"		: {
				"V": "Variable",
				"T": "Text",
				"N": "Number",
				"B": "Boolean",
				"NL": "Null", 
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
			},
			"s2_2sepyt_atad"		: {
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
			"s2_sepyt_tupni"		: {
				"T": "Text",
				"TT": "MultiLineText",
				"N": "Number",
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
				"L": "List",
				"O": "Assoc List",
				"B": "Boolean",
				"NL": "Null", 
				"B64": "Base64",
			},
			"s2_2sepyt_tupni"		:{
				"T": "Text",
				"N": "Number",
				"D": "Date",
				"DT": "DateTime",
				"TS": "Timestamp",
				"B": "Boolean",
				"B64": "Base64",
			},
			"s2_epyt_yb_segats"		: [
				{
					"group": "Expression",
					"sub": [
						"Let",
						"Assign",
						"Math",
						"Expression",
						"Function"
					]
				},
				{
					"group": "Control",
					"sub": [
						"If",
						"While",
						"For",
						"ForEach",
						"BreakLoop",
						"NextLoop",
						"SetLabel",
						"JumpToLabel",
						"Sleep",
					]
				},
				{
					"group": "Output",
					"sub": [
						"RespondJSON", "RespondVar", "Log",
					]
				},
				{
					"group": "Components",
					"sub": [
						"HTTPRequest",
						"APICall",
						"FunctionCall",
						"Internal-Table",
						"Elastic-Table"
					]
				},
				{
					"group": "Database",
					"sub": [
						"MySql",
						"MongoDb",
						"DynamoDb",
						"Redis",
						"MSSql",
						"Cassandra",
						"SQLite",
						"FireBase",
						"FireStore",
						"BigQuery",
					]
				},
				{
					"group": "SDK",
					"sub": [
						"wkHtmlToPdf",
						"AWS",
						"GCP",
						"Azure",
						"AirTable",
						"Facebook",
						"Slack",
						"Whatsapp",
						"Telegram",
						"Jira",
						"GoogleMaps",
						"Celery",
						"AMPq",
						"RabbitMQ",
						"Swagger",
						"RazorPay",
						"Paytm",
						"Stripe",
						"PayPal",
						"CCAvenue",
						"PayU",
						"BillDesk",
						"Instamojo",
						"Mobikwik",
					]
				}
			],

			"s2_smarap_egats"	: s2_smarap_egats_gifnoc,
			"s2_egats_detaerc_tsuj": -1,

			s2_unem_txetnoc: false,
			s2_rof_txetnoc: 'stages',
			s2_rof_rav_txetnoc: '',
			s2_ycnedneped_txetnoc: "",
			s2_kcabllac_txetnoc: "",
			s2_le_txetnoc: false,
			s2_elyts_txetnoc: "top:50px;left:50px;",
			s2_di_egats_txetnoc: -1,
			s2_tsil_txetnoc: [],
			s2_retlif_tsil_txetnoc: [],
			s2_epyt_txetnoc: "",
			s2_eulav_txetnoc: "",
			s2_ravatad_txetnoc: "",
			s2_tnerap_ravatad_txetnoc: "",
			s2_meti_tnerruc_unem_txetnoc: "",
			s2_yek_unem_txetnoc: "",
			s2_yek_dnapxe_txetnoc: "",
			s2_gniht_txetnoc: "",
			s2_tsil_gniht_txetnoc: {},
			s2_dedaol_gniht_txetnoc: false,
			s2_gsm_gniht_txetnoc: "",
			s2_rre_gniht_txetnoc: "",

			s2_di_egats_pupop: -1,
			s2_atad_pupop: {},
			s2_rrof_pupop: "",
			s2_ravatad_pupop: "",
			s2_epyt_pupop: "json",
			s2_eltit_pupop: "Popup Title",
			s2_tsil_tseggus_pupop: [],
			s2_ffer_pupop: "",
			s2_ladom_pupop: false,
			s2_tropmi_pupop: false,
			s2_rts_tropmi_pupop: `{}`,
			s2_ppupop_cod: false,
			s2_cod_pupop_cod: "",
			s2_txet_pupop_cod: "Loading...",

			s2_di_egats_pupop_elpmis: -1,
			s2_atad_pupop_elpmis: {},
			s2_rof_pupop_elpmis: "",
			s2_ravatad_pupop_elpmis: "",
			s2_epyt_pupop_elpmis: "json",
			s2_eltit_pupop_elpmis: "Popup Title",
			s2_ladom_pupop_elpmis: false,
			s2_tropmi_pupop_elpmis: false,
			s2_rts_tropmi_pupop_elpmis: `{}`,
			s2_le_pupop_elpmis: false,
			s2_elyts_pupop_elpmis:  "top:50px;left:50px;",

			s2_snoitpo_gniht: [],
			s2_gsm_snoitpo_gniht: "",
			s2_rre_snoitpo_gniht: "",
			s2_nigulp_detceles: "",
			s2_nf_nigulp_detceles: "",
			s2_marap_nf_nigulp_detceles: "",
			s2_desu_sgniht: {},
			s2_noitcnuf_cimanyd: function(){},
		};
	},
	mounted(){
		for(var f in this.s2_seitreporp_tcejbo_gifnoc){
			if( 'inputs' in this.s2_seitreporp_tcejbo_gifnoc[f] ){
				for( var p in this.s2_seitreporp_tcejbo_gifnoc[f]['inputs'] ){
					this.s2_seitreporp_tcejbo_gifnoc[f]['inputs'][p]['vs'] = {
						"v": ".",
						"t": "n",
						"d": {},
					};
				}
			}
		}
		this.s2_atad_laitini_daol();
		//document.addEventListener("mousedown", this.event_mousedown );
		document.addEventListener("keyup", this.event_keyup );
		document.addEventListener("keydown", this.event_keydown);
		document.addEventListener("click", this.s2_kcilc_tneve, true);
		document.addEventListener("scroll", this.event_scroll, true);
		document.addEventListener("blur", this.s2_rulb_tneve, true);
		window.addEventListener("paste", this.s2_etsap_tneve, true);
	},
	methods: {
		s2_tnemnorivne_tset_tceles: function(){
			setTimeout(this.s2_tnemnorivne_tset_tceles2,200);
		},
		s2_tnemnorivne_tset_tceles2: function(){
			for( var d in this.s2_pppppppppa['settings']['domains'] ){
				if( this.s2_pppppppppa['settings']['domains'][ d ]['domain'] == this.s2_tttttttset['domain'] ){
					this.s2_tttttttset['path'] = this.s2_pppppppppa['settings']['domains'][ d ]['path'];
					var tu = this.s2_pppppppppa['settings']['domains'][ d ]['url'] + "?function_version_id=<?=$config_param4 ?>&test_token=<?=md5($config_param4) ?>";
					if( this.s2_gubed_tset ){
						tu  = tu + "&debug=true";
					}
					this.s2_lllru_tset = tu;
					break;
				}
			}
		},
		event_scroll: function(e){
			if( e.target.className == "codeeditor_block_a" ){
				if( this.s2_unem_txetnoc ){
					this.s2_elyts_unem_txetnoc_tes();
				}else if( this.s2_ladom_pupop_elpmis ){
					this.s2_elyts_pupop_elpmis_tes();
				}
			}
		},
		event_keyup: function(e){
			if( e.target.hasAttribute("data-type") ){
				console.log("event_keyup: "+e.target.getAttribute("data-type"));
				if( e.target.getAttribute("data-type") == "editable" ){
					setTimeout(this.s2_kcehc_elbatide, 100, e.target);
				}else if( e.target.getAttribute("data-type") == "popupeditable" ){
					setTimeout(this.s2_kcehc_elbatide, 100, e.target);
				}else{
					console.log("Error: unknown data-type: " + e.target.getAttribute("data-type") );
				}
			}else{
				console.log("event_keyup: data-type not found");
			}
		},
		s2_tsaot_wohs: function( v ){
			this.s2_ssssstsaot.push( v );
			if( this.s2_ssssstsaot.length == 1 ){
				setTimeout(this.s2_esolc_tsaot, 1000);
			}
		},
		s2_esolc_tsaot: function(){
			this.s2_ssssstsaot.splice(0,1);
			if( this.s2_ssssstsaot.length > 0 ){
				setTimeout(this.s2_esolc_tsaot, 1000);
			}
		},
		s2_etsap_tneve: function( e ){
			e.preventDefault();e.stopPropagation();
			clipboardData = e.clipboardData || window.clipboardData;
			var paste_data_ = clipboardData.getData('Text');
			document.execCommand('inserttext', false, paste_data_);
			// console.log( paste_data_ );
			// var r = document.getSelection().getRangeAt(0);
			// console.log( r );
			//setTimeout(this.s2_etsap_retfa,100,e.target);
		},
		s2_etsap_retfa: function(el){
			console.log( el.innerText );
			console.log( el.innerHTML );
			if( el.innerText != el.innerHTML ){
				el.innerText = el.innerText+'';
			}
		},
		s2_rulb_tneve: function( e ){
			// console.log( "blur event:" );
			// console.log( e.target );
			if( e.target.hasAttribute("data-type") ){
				if( e.target.getAttribute("data-type") == "editable" ){
					e.stopPropagation();
					e.preventDefault();
					var s = this.find_parents(e.target);
					if( !s ){ return false; }
					var v = e.target.innerText;
					// console.log( " =====  " + v );
					v = v.replace(/[\u{0080}-\u{FFFF}]/gu, "");
					// v = v.replace(/\&nbsp\;/g, " ");
					// v = v.replace(/\&gt\;/g, ">");
					// v = v.replace(/\&lt\;/g, "<");
					var vv = this.s2_rrretlif_v( v, e.target );
					// console.log( "==" + v + "== : ==" + vv + "==" );
					if( v == vv ){
						this.update_editable_value( s, v );
						setTimeout(this.s2_kcehc_elbatide, 200, e.target );
						setTimeout(this.s2_noitpo_detadpu, 200);
						if( e.target.hasAttribute("validation_error") ){
							e.target.removeAttribute("validation_error");
						}
					}else{ this.s2_tsaot_wohs("Incorrect value entered!"); e.target.setAttribute("validation_error", "sss"); }
				}
				if( e.target.getAttribute("data-type") == "popupeditable" ){
					e.stopPropagation();
					e.preventDefault();
					var s = this.find_parents(e.target);
					if( !s ){ return false; }
					var v = e.target.innerText;
					v = this.s2_rrretlif_v( v, e.target );
					if( v ){
						this.update_editable_value( s, v );
						setTimeout(this.s2_kcehc_elbatide, 200, e.target );
						setTimeout(this.s2_noitpo_detadpu, 200);
					}else{console.log("incorrect value formed!");}
				}
			}
		},
		s2_kcehc_elbatide: function(el){
			var data_var = el.getAttribute("data-var");
			var s = this.find_parents(el);
			if( !s ){ return false; }
			var v = this.get_editable_value(s);
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
		event_keydown: function(e){
			if( e.ctrlKey && e.keyCode == 86 ){
			//	e.preventDefault();e.stopPropagation();
			}
			if( e.keyCode == 27 ){
				if( this.s2_unem_txetnoc ){
					this.s2_unem_txetnoc = false;
				}
				if( this.s2_ladom_pupop_elpmis ){
					this.s2_ladom_pupop_elpmis = false;
				}
			}
			if( e.target.hasAttribute("data-type") ){
				if( e.target.getAttribute("data-type") =="editable" ){
					if( e.target.className == "editabletextarea" ){

					}else if( e.keyCode == 13 || e.keyCode == 10 ){
						e.preventDefault();
						e.stopPropagation();
						var v = e.target.innerText;
						v = this.s2_rrretlif_v( v, e.target );
						if( v ){
							if( e.target.nextSibling ){
								e.target.nextSibling.outerHTML = "";
							}
							s = this.find_parents(e.target);
							if( !s ){ return false; }
							this.update_editable_value( s, v );
							//setTimeout(this.s2_kcehc_elbatide, 100, e.target);
							setTimeout(this.s2_noitpo_detadpu, 200);
						}else{console.log("incorrect value formed!");}
					}
				}
			}
		},
		s2_kcilc_tneve: function(e){
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
							this.show_doc_popup(doc);
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
				console.log( t );
				if( t == "type_pop" ){

				}else if( t == "objecteditable" ){
					this.s2_di_egats_pupop = stage_id;
					this.s2_ravatad_pupop = data_var;
					this.s2_rrof_pupop = data_for;
					console.log("objecteditable" + data_var);
					var v = this.get_editable_value({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
					if( v === false ){console.log("event_click: value false");return false;}
					this.s2_atad_pupop = v;
					this.s2_epyt_pupop = el_data_type.getAttribute("editable-type");
					this.s2_eltit_pupop = "Data Editor";
					this.s2_ffer_pupop = "";
					if( el_data_type.hasAttribute("data-ref") ){
						this.s2_ffer_pupop = el_data_type.getAttribute("data-ref");
					}
					if( el_data_type.hasAttribute("editable-title") ){
						this.s2_eltit_pupop = el_data_type.getAttribute("editable-title");
					}else if( this.s2_epyt_pupop == "O" ){
						this.s2_eltit_pupop = "Object/Associative Array Structure";
					}else if( this.s2_epyt_pupop == "TT" ){
						this.s2_eltit_pupop = "Multiline Text";
					}else if( this.s2_epyt_pupop == "HT" ){
						this.s2_eltit_pupop = "HTML Editor";
					}
					if( this.s2_ladom_pupop == false ){
						this.s2_ladom_pupop = new bootstrap.Modal( document.getElementById('s2_ladom_pupop') );
					}
					this.s2_ladom_pupop.show();
				}else if( t == "popupeditable" ){
					this.s2_le_pupop_elpmis = el_data_type;
					this.s2_di_egats_pupop_elpmis = stage_id;
					this.s2_ravatad_pupop_elpmis = data_var;
					this.s2_rof_pupop_elpmis = data_for;
					var v = this.get_editable_value({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
					if( v === false ){console.log("event_click: value false");return false;}

					this.s2_atad_pupop_elpmis = v;
					this.s2_epyt_pupop_elpmis = el_data_type.getAttribute("editable-type");
					this.s2_ladom_pupop_elpmis = true;
					//this.s2_unem_txetnoc_sucof_dna_wohs();
					this.s2_elyts_pupop_elpmis_tes();
				}else if( t == "payloadeditable" ){
					this.s2_di_egats_pupop = stage_id;
					this.s2_ravatad_pupop = data_var;
					this.s2_rrof_pupop = data_for;
					console.log("payloadeditable" + data_var);
					var v = this.get_editable_value({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
					if( v === false ){console.log("event_click: value false");return false;}
					this.s2_atad_pupop = v;
					this.s2_epyt_pupop = 'PayLoad';
					this.s2_eltit_pupop = "Request Payload Editor";
					if( this.s2_ladom_pupop == false ){
						this.s2_ladom_pupop = new bootstrap.Modal( document.getElementById('s2_ladom_pupop') );
					}
					this.s2_ladom_pupop.show();

				}else if( t == "dropdown" || t == "dropdown2" || t == "dropdown3" || t == "dropdown4" ){
					this.s2_le_txetnoc = el_data_type;
					this.s2_eulav_txetnoc = el_data_type.innerHTML;
					this.s2_yek_unem_txetnoc = "";
					this.s2_rof_txetnoc = data_for;
					this.s2_ravatad_txetnoc = data_var;
					var v = this.get_editable_value({'data_var':data_var,'data_for':data_for,'stage_id':stage_id});
					if( v === false ){console.log("event_click: value false");return false;}
					console.log("dropdown click: " + data_for + ": " + data_var );
					this.s2_di_egats_txetnoc = stage_id;
					this.s2_epyt_txetnoc = el_data_type.getAttribute("data-list");
					if( this.s2_epyt_txetnoc == "varsub" || this.s2_epyt_txetnoc == "plgsub" ){
						this.s2_rof_rav_txetnoc = el_data_type.getAttribute("var-for");
					}else{
						this.s2_rof_rav_txetnoc = "";
					}
					if( el_data_type.hasAttribute("data-context-dependency") ){
						this.s2_ycnedneped_txetnoc = el_data_type.getAttribute("data-context-dependency");
					}else{
						this.s2_ycnedneped_txetnoc = "";
					}
					if( el_data_type.hasAttribute("data-context-callback") ){
						this.s2_kcabllac_txetnoc = el_data_type.getAttribute("data-context-callback");
					}else{
						this.s2_kcabllac_txetnoc = "";
					}
					if( el_data_type.hasAttribute("data-list-filter") ){
						var tl = el_data_type.getAttribute("data-list-filter").split(/\,/g);
						this.s2_retlif_tsil_txetnoc = tl;
					}else{
						this.s2_retlif_tsil_txetnoc = [];
					}
					if( this.s2_epyt_txetnoc == "thing" ){
						if( el_data_type.hasAttribute("data-thing") ){
							this.s2_gniht_txetnoc = el_data_type.getAttribute("data-thing");
							setTimeout(this.s2_kcehc_daol_tsil_gniht_txetnoc,300);
						}else{
							this.s2_gniht_txetnoc = "UnKnown";
						}
					}
					this.s2_tnerap_ravatad_txetnoc = data_var_parent;
					if( this.s2_epyt_txetnoc == "list" ){
						var ld = el_data_type.getAttribute("data-list-values");
						if( ld == 'input-method' ){
							this.s2_tsil_txetnoc = ["GET", "POST"];
						}else if( ld == 'post-input-type' ){
							this.s2_tsil_txetnoc = ["application/x-www-form-urlencoded", "application/json", "application/xml"];
						}else if( ld == 'get-input-type' ){
							this.s2_tsil_txetnoc = ["query_string"];
						}else if( ld == 'output-type' ){
							if( this.s2_nnnoitcnuf['input-method'] == "GET" ){
								this.s2_tsil_txetnoc = ["application/json", "application/xml", "text/html", "text/plain"];
							}else{
								this.s2_tsil_txetnoc = ["application/json", "application/xml"];
							}
						}else{
							this.s2_tsil_txetnoc = ld.split(",");
						}
					}
					console.log("ok");
					this.s2_unem_txetnoc_sucof_dna_wohs();
					this.s2_elyts_unem_txetnoc_tes();

				}else if( t == "editablebtn" ){
					setTimeout( this.s2_kcilc_ntbelbatide, 100, el_data_type, data_var, data_for, stage_id, e );
				}else{
					console.log("s2_kcilc_tneveUnknown");
				}
			}else if( el_context ){
				console.log("Element Data-Context");
			}else{
				if( this.s2_unem_txetnoc ){
					this.s2_unem_txetnoc = false;
				}
				if( this.s2_ladom_pupop_elpmis ){
					this.s2_ladom_pupop_elpmis = false;
				}
			}
		},
		s2_kcehc_daol_tsil_gniht_txetnoc: function(){
			if( this.s2_gniht_txetnoc in this.s2_tsil_gniht_txetnoc == false ){
				this.s2_tsil_gniht_txetnoc[ this.s2_gniht_txetnoc ] = [];
			}
			if( this.s2_tsil_gniht_txetnoc[ this.s2_gniht_txetnoc ].length == 0 ){
				this.s2_gsm_gniht_txetnoc = "Loading...";
				this.s2_rre_gniht_txetnoc = "";
				this.s2_tsil_gniht_txetnoc[ this.s2_gniht_txetnoc ] = [];
				axios.post("<?=$config_global_apimaker_path ?>things", {
					"action": "context_load_things",
					"app_id": "<?=$config_param1 ?>",
					"thing": this.s2_gniht_txetnoc,
					"depend": this.s2_ycnedneped_txetnoc,
				}).then(response=>{
					this.s2_gsm_gniht_txetnoc = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									this.s2_tsil_gniht_txetnoc[ this.s2_gniht_txetnoc ] = response.data['things'];
								}else{
									this.s2_rre_gniht_txetnoc = "Token Error: " + response.data['data'];
								}
							}else{
								this.s2_rre_gniht_txetnoc = "Incorrect response";
							}
						}else{
							this.s2_rre_gniht_txetnoc = "Incorrect response Type";
						}
					}else{
						this.s2_rre_gniht_txetnoc = "Response Error: " . response.status;
					}
				}).catch(error=>{
					this.s2_rre_gniht_txetnoc = "Error Loading";
				});
			}
		},
		s2_kcilc_ntbelbatide: function( el_data_type, data_var, data_for, stage_id, e ){
			console.log( "editablebtn" );
			console.log( el_data_type.previousSibling );
			var v = el_data_type.previousSibling.innerText;
			console.log( v );
			v = v.replace(/[\u{0080}-\u{FFFF}]/gu, "");
			// v = v.replace( /\&nbsp\;/g, " " );
			// v = v.replace( /\&gt\;/g,  ">" );
			// v = v.replace( /\&lt\;/g,  "<" );
			vv = this.s2_rrretlif_v(v, el_data_type.previousSibling );
			if( vv == v ){
				this.update_editable_value({'data_var':data_var,'data_for':data_for,'stage_id':stage_id}, v);
				setTimeout( this.s2_kcehc_elbatide, 100, e.target );
				setTimeout( this.s2_noitpo_detadpu, 200 );
				if( e.target.hasAttribute("validation_error") ){
					e.target.removeAttribute("validation_error");
				}
			}else{ this.s2_tsaot_wohs("Incorrect value entered!"); e.target.setAttribute("validation_error", "sss"); }
		},
		s2_rrretlif_v: function(v,el){
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
		update_editable_value: function(s, v){
			// this.s2_ooooooohce("update_editable_value: " );
			// this.s2_ooooooohce(s);
			if( s['data_for'] == 'stages' ){
				var ov = this.s2_rav_bus_teg(this.s2_eeeeenigne['stages'][ s['stage_id'] ], s['data_var'], v);
				if( ov != v ){
					this.s2_rav_bus_tes(this.s2_eeeeenigne['stages'][ s['stage_id'] ], s['data_var'], v);
					this.check_sub_key(this.s2_eeeeenigne['stages'][ s['stage_id'] ], s['data_var'], v);
					if( this.s2_eeeeenigne['stages'][ s['stage_id'] ]['k']['v'] == "Let" && s['data_var'] == "d:lhs" ){
						this.s2_segats_bus_ni_egnahc_elbairav_etadpu( s['stage_id'], ov+'', v+'' );
					}
				}
			}else if( s['data_for'] == 'api' ){
				var ov = this.s2_rav_bus_teg(this.s2_iiiiiiiipa, s['data_var'], v);
				if( ov != v ){
					this.s2_rav_bus_tes(this.s2_iiiiiiiipa, s['data_var'], v);
					this.check_sub_key(this.s2_iiiiiiiipa, s['data_var'], v);
				}
			}else if( s['data_for'] == 'engine' ){
				var ov = this.s2_rav_bus_teg(this.s2_eeeeenigne, s['data_var'], v);
				if( ov != v ){
					this.s2_rav_bus_tes(this.s2_eeeeenigne, s['data_var'], v);
					this.check_sub_key(this.s2_eeeeenigne, s['data_var'], v);
				}
			}else if( s['data_for'] == 's2_tttttttset' ){
				var ov = this.s2_rav_bus_teg(this.s2_tttttttset, s['data_var'], v);
				if( ov != v ){
					this.s2_rav_bus_tes(this.s2_tttttttset, s['data_var'], v);
					this.check_sub_key(this.s2_tttttttset, s['data_var'], v);
				}
			}else{
				console.error("update_editable_value: data_for unknown: " + s['data_for'] + ": " + s['data_var'] );
				return false;
			}
		},
		get_editable_value: function(s){
			if( s['data_for'] == 'stages' ){
				return this.s2_rav_bus_teg(this.s2_eeeeenigne['stages'][ s['stage_id'] ], s['data_var']);
			}else if( s['data_for'] == 'api' ){
				return this.s2_rav_bus_teg(this.s2_iiiiiiiipa, s['data_var']);
			}else if( s['data_for'] == 'engine' ){
				return this.s2_rav_bus_teg(this.s2_eeeeenigne, s['data_var']);
			}else if( s['data_for'] == 's2_tttttttset' ){
				return this.s2_rav_bus_teg(this.s2_tttttttset, s['data_var']);
			}else{
				console.error("get_editbale_value: data_for unknown: " + s['data_for'] + ": " + s['data_var'] );
				return false;
			}
		},
		check_sub_key: function(vv, data_var, v){
			x = data_var.split(/\:/g);
			var vkey = x.pop();
			if( vkey == 'k' ){
				var data_var = x.join(":");
				//this.s2_ooooooohce( data_var );
				var mdata = this.s2_rav_bus_teg( vv, data_var );
				//this.s2_ooooooohce(mdata);
				if( 'k' in mdata && 'v' in mdata && 't' in mdata ){
					var vkey = x.pop();
					if( vkey != v ){
						var data_var = x.join(":");
						var mdata2 = this.s2_rav_bus_teg( vv, data_var );
						mdata2[ v+'' ] = this.s2_nnnnnnnosj(mdata);
						delete mdata2[ vkey ];
					}
				}else{
					this.s2_ooooooohce("Not key object");
				}
			}else{this.s2_ooooooohce("k not found");}
		},
		s2_evas_atad_pupop: function(){
			this.s2_ladom_pupop.hide();
		},
		find_parents: function(el){
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
		s2_unem_txetnoc_sucof_dna_wohs: function(){
			setTimeout(function(){try{document.getElementById("contextmenu_key1").focus();}catch(e){}},300);
			this.s2_unem_txetnoc = true;
			this.s2_yek_dnapxe_txetnoc = '';
		},
		s2_elyts_unem_txetnoc_tes: function(){
			var s = this.s2_le_txetnoc.getBoundingClientRect();
			//this.finx_zindex(this.s2_le_txetnoc);
			this.s2_elyts_txetnoc = "top: "+s.top+"px;left: "+s.left+"px;";
		},
		s2_elyts_pupop_elpmis_tes: function(){
			var s = this.s2_le_pupop_elpmis.getBoundingClientRect();
			this.s2_elyts_pupop_elpmis = "top: "+s.top+"px;left: "+s.left+"px;";
		},
		find_zindex: function(el){
			for(var i=0;i<20;i++){
				el = el.parentNode;
			}
		},
		s2_hctam_yek_unem_txetnoc: function(v){
			if( this.s2_yek_unem_txetnoc == "" ){
				return true;
			}else if( v.toLowerCase().indexOf(this.s2_yek_unem_txetnoc.toLowerCase() ) > -1 ){
				return true;
			}
		},
		s2_thgilhgih_yek_unem_txetnoc: function(v){
			var r = new RegExp( this.s2_yek_unem_txetnoc , "i" );
			var c = v.match( r );
			return v.replace( c, "<span>"+c+"</span>" );
		},
		s2_thgilhgih_gniht_unem_txetnoc: function(v){
			var r = new RegExp( this.s2_yek_unem_txetnoc , "i" );
			var c = v['l']['v'].match( r );
			if( v['l']['v'] == v['i']['v'] ){
				return v['l']['v'].replace( c, "<span>"+c+"</span>" );
			}else{
				return v['i']['v'] + ": " + v['l']['v'].replace( c, "<span>"+c+"</span>" );
			}
		},
		s2_noitaton_epyt_teg_txetnoc: function(v){
			if( v['t'] == "PLG" ){
				return ': <abbr>Plugin: '+ v['plg'] +'</abbr>';
			}else if( v['t'] == "THL" ){
				return ': <abbr>Thing List: '+ v['th'] +'</abbr>';
			}else if( v['t'] == "TH" ){
				return ': <abbr>Thing: '+ v['th'] +'</abbr>';
			}else{
				return ': <abbr>'+this.s2_sepyt_atad[v['t']]+'</abbr>';
			}
		},
		s2_tceles_txetnoc: function(k, t){
			console.log( "context select: "+ this.s2_rof_txetnoc  + ": " + this.s2_ravatad_txetnoc + ": " + k +  ": " + t );
			if( this.s2_rof_txetnoc == 'engine' ){
				this.s2_rav_bus_tes( this.s2_eeeeenigne, this.s2_ravatad_txetnoc, k );
				if( t == "inputtype" ){
					this.s2_epyt_elbairav_etadpu( this.s2_eeeeenigne, this.s2_ravatad_txetnoc, k );
				}
			}else if( this.s2_rof_txetnoc == 's2_tttttttset' ){
				this.s2_rav_bus_tes( this.s2_tttttttset, this.s2_ravatad_txetnoc, k );
				console.log( t );
				if( t == "datatype" ||  t == "inputtype" ){
					this.s2_epyt_elbairav_etadpu( this.s2_tttttttset, this.s2_ravatad_txetnoc, k );
				}
			}else if( this.s2_rof_txetnoc == 'api' ){
				this.s2_rav_bus_tes( this.s2_iiiiiiiipa, this.s2_ravatad_txetnoc, k );
			}else if( this.s2_rof_txetnoc == 'stages' ){
				if( this.s2_ravatad_txetnoc == "k" ){
					if( t == 'o' ){
						var d = this.get_o_sub_var( this.s2_esiw_egats_srotcaf_lla[ this.s2_di_egats_txetnoc ], k );
						if( d ){
							t = d['t'];
						}else{
							this.s2_ooooooohce( k + " not found in stage_vars ");
						}
					}
					if( t == 'c' ){

					}
					var k = {
						"v": k,
						"t": t,
						"vs": false,
					};
					this.s2_egats_egnahc_egats(this.s2_di_egats_txetnoc, k, t);

				}else{
					if( typeof(k) == "string" || typeof(k) == "number" ){
						this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, this.s2_ravatad_txetnoc, k );
					}
					if( t == 'prop' ){
						var vt = this.s2_rav_bus_egats_teg( this.s2_di_egats_txetnoc, this.s2_tnerap_ravatad_txetnoc+":t" );
						if( vt in this.s2_seitreporp_tcejbo_gifnoc ){
							if( k in this.s2_seitreporp_tcejbo_gifnoc[ vt ] ){
								//this.s2_ooooooohce( this.s2_rav_bus_egats_teg( this.s2_di_egats_txetnoc, this.s2_tnerap_ravatad_txetnoc ) );
								this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, this.s2_tnerap_ravatad_txetnoc+":vs:d", this.s2_nnnnnnnosj(this.s2_seitreporp_tcejbo_gifnoc[ vt ][k]) );
							}
						}
					}
					if( t == 'plgprop' ){
						var vt = this.s2_rav_bus_egats_teg( this.s2_di_egats_txetnoc, this.s2_tnerap_ravatad_txetnoc+":t" );
						//this.s2_ooooooohce( vt );
						if( vt in this.s2_atad_nigulp ){
							if( 'p' in this.s2_atad_nigulp[ vt ] ){
								if( k in this.s2_atad_nigulp[ vt ]['p'] ){
									var d = this.s2_nnnnnnnosj( this.s2_rav_bus_egats_teg( this.s2_di_egats_txetnoc, this.s2_tnerap_ravatad_txetnoc ) );
									//d['vs'] = {"v":"", "t":"", "d":{}};
									this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, this.s2_tnerap_ravatad_txetnoc+":vs:d", {} );
									d['vs']['d'] = this.s2_nnnnnnnosj(this.s2_atad_nigulp[ vt ]['p'][k]);
									setTimeout(this.s2_rav_bus_egats_tes,100,this.s2_di_egats_txetnoc, this.s2_tnerap_ravatad_txetnoc, d );
									//this.nextTick();
								}else{
									this.s2_ooooooohce( k + " not found in plugin data " + vt );
								}
							}else{
								this.s2_ooooooohce("plugging data sub p not found");
							}
						}
					}
					if( t == "plugin" ){
						if( k in this.s2_atad_nigulp ){
							var x = this.s2_ravatad_txetnoc.split(/\:/g);
							x.pop(0);
							var dvp = x.join(":");
							this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, dvp+':vs', {"v": ".", "t": "n", "d": {}} );
						}else{
							console.error("selected plugin: " + k + " not found");
							this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, this.context_datavar_, "" );
						}
					}
					if( t == "thing" ){
						//this.s2_ooooooohce( k );
						//this.s2_ooooooohce( this.s2_rav_bus_egats_teg( this.s2_di_egats_txetnoc, this.s2_ravatad_txetnoc ) );
						this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, this.s2_ravatad_txetnoc, k );
					}
					if( t == "datatype" ){
						this.s2_epyt_elbairav_etadpu( this.s2_eeeeenigne['stages'][ this.s2_di_egats_txetnoc ], this.s2_ravatad_txetnoc, k );
						if( this.s2_eeeeenigne['stages'][ this.s2_di_egats_txetnoc ]['k']['v'] == "Let" ){
							var a = this.s2_eeeeenigne['stages'][ this.s2_di_egats_txetnoc ]['d']['lhs'];
							var t = this.s2_eeeeenigne['stages'][ this.s2_di_egats_txetnoc ]['d']['rhs']['t'];
							if( t == "TT" ){ t = "T"; }
							setTimeout(this.s2_segats_bus_ni_egnahc_epyt_elbairav_etadpu, 100, this.s2_di_egats_txetnoc, a, t);
						}
					}
					if( t == "function" ){
						if( k != "" ){
							if( k in this.s2_ssnoitcnuf ){
								var vt = this.s2_tnerap_ravatad_txetnoc+":inputs";
								this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, vt, {} );
								var s2_pppppppppp = this.s2_nnnnnnnosj( this.s2_ssnoitcnuf[k]['inputs'] );
								var s2_rrrrrrrrrr = this.s2_ssnoitcnuf[k]['return'];
								var s2_ssssssssss = this.s2_ssnoitcnuf[k]['self'];
								setTimeout(this.s2_stupni_noitcnuf_tes, 100, this.s2_tnerap_ravatad_txetnoc, s2_pppppppppp, s2_rrrrrrrrrr, s2_ssssssssss);
							}else{
								console.log("function error: " + k + " not found!");
							}
						}
					}
					if( t == "var" ){
						var d = this.get_o_sub_var( this.s2_esiw_egats_srotcaf_lla[ this.s2_di_egats_txetnoc ], k );
						// this.s2_ooooooohce("var select");
						// this.s2_ooooooohce( d );
						// this.s2_ooooooohce( this.s2_ravatad_txetnoc );
						if( d ){
							var x = this.s2_ravatad_txetnoc.split(/\:/g);
							x.pop();
							var new_path = x.join(":");
							var var_type = d['t'];
							//console.log( var_type );
							this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, new_path+':t', var_type );
							this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, new_path+':vs', {"v": "","t": "","d": {} } );
							if( var_type in this.s2_atad_nigulp ){
								this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, new_path+':plg', var_type, true );
							}else{
								this.s2_rav_bus_egats_evomer( this.s2_di_egats_txetnoc, new_path+':plg' );
							}
							var s = this.s2_rav_bus_egats_teg( this.s2_di_egats_txetnoc, new_path );
							this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, new_path, this.s2_nnnnnnnosj( s ) );
						}
					}
					if( t == "operator" ){
						var op = this.s2_rav_bus_egats_teg( this.s2_di_egats_txetnoc, this.s2_ravatad_txetnoc );
						x = this.s2_ravatad_txetnoc.split(/\:/g);
						x.pop();
						var vn = Number(x.pop());
						var mvar = x.join(":");
						var mdata = this.s2_rav_bus_egats_teg( this.s2_di_egats_txetnoc, mvar );
						if( mvar == "d:rhs" ){
							if( op == "." ){
								while( mdata.length-1 > vn ){
									mdata.pop();
								}
								this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, mvar, mdata );
							}else{
								if( mdata.length-1 == vn ){
									mdata.push({ "m": [ {"t":"N","v":"333", "OP":"."} ], "OP": "." });
									this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, mvar, mdata );
								}else{
									this.s2_ooooooohce("update existing operator");
								}
							}
						}else{
							if( op == "." ){
								while( mdata.length-1 > vn ){
									mdata.pop();
								}
								this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, mvar, mdata );
							}else{
								if( mdata.length-1 == vn ){
									mdata.push({"t":"N","v":"333", "OP":"."});
									this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, mvar, mdata );
								}else{
									this.s2_ooooooohce("update existing operator");
								}
							}
						}
					}
					if( this.s2_kcabllac_txetnoc ){
						var x = this.s2_kcabllac_txetnoc.split(/\:/g);
						var vref = x.splice(0,1);
						if( vref in this.$refs ){
							if( "length" in this.$refs[ vref ] ){
								this.$refs[ vref ][0].s2_kkkcabllac(x.join(":"));
							}else{
								this.$refs[ vref ].s2_kkkcabllac(x.join(":"));
							}
						}else{
							console.error("Ref: " + vref + ": not found");
							//this.$refs[ x[0] ][ x[1] ]();
						}
					}
				}
			}else{
				console.error("context_select error: data_for unknown: "+ this.s2_rof_txetnoc );
			}
			setTimeout(this.s2_noitpo_detadpu,100);
			this.s2_unem_txetnoc = false;
		},
		s2_stupni_noitcnuf_tes: function(v,p,r,s){
			var vt = v+":inputs";
			this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, vt, p );
			var vt = v+":return";
			this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, vt, r );
			var vt = v+":self";
			this.s2_rav_bus_egats_tes( this.s2_di_egats_txetnoc, vt, s );
		},
		s2_rav_bus_egats_tes: function( vstagei, datavar, d, create_sub_node = false ){
			this.s2_rav_bus_tes( this.s2_eeeeenigne['stages'][ vstagei ], datavar, d, create_sub_node );
		},
		s2_rav_bus_tes: function( vv, vpath, value, create_sub_node = false ){
			// this.s2_ooooooohce("s2_rav_bus_tes: " + vpath + " - " + value + " : " + (create_sub_node?'create_sub_node':'')) ;
			// this.s2_ooooooohce( vv );
			try{
				var x = vpath.split(":");
				//this.s2_ooooooohce( x );
				var k = x[0];
				if( k.match(/^[0-9]+$/) ){
					k = Number(k);
				}
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( typeof(vv[ k ]) == "object" && vv[ k ] != null ){
							return this.s2_rav_bus_tes( vv[ k ], x.join(":"), value, create_sub_node );
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
			}catch(e){console.error(e);console.log("s2_rav_bus_tes error: " + vpath );return false;}
		},
		s2_rav_bus_egats_evomer: function( vstagei, datavar ){
			this.s2_rav_bus_evomer( this.s2_eeeeenigne['stages'][ vstagei ], datavar );
		},
		s2_rav_bus_evomer: function( vv, vpath ){
			// this.s2_ooooooohce("s2_rav_bus_tes: " + vpath + " - " + value + " : " + (create_sub_node?'create_sub_node':'')) ;
			// this.s2_ooooooohce( vv );
			try{
				var x = vpath.split(":");
				//this.s2_ooooooohce( x );
				var k = x[0];
				if( k.match(/^[0-9]+$/) ){
					k = Number(k);
				}
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( typeof(vv[ k ]) == "object" && vv[ k ] != null ){
							this.s2_rav_bus_tes( vv[ k ], x.join(":") );
						}
					}else{
						delete(vv[k]);
					}
				}
			}catch(e){console.error(e);console.log("s2_rav_bus_tes error: " + vpath );return false;}
		},
		s2_rav_bus_egats_teg: function( stage_id, datavar ){
			var d = this.s2_rav_bus_teg( this.s2_eeeeenigne['stages'][ stage_id ], datavar );
			if( d === false ){
				console.error("get stage sub var error: " + stage_id + ": " + datavar + ": ");
				this.s2_ooooooohce( this.s2_eeeeenigne['stages'][ stage_id ] );
			}
			return d;
		},
		s2_rav_bus_teg: function(vv, vpath){
			// this.s2_ooooooohce("s2_rav_bus_teg: " + vpath);
			// this.s2_ooooooohce( vv );
			try{
				var x = vpath.split(":");
				//this.s2_ooooooohce( x );
				var k = x[0];
				if( k.match(/^[0-9]+$/) && "length" in vv ){
					k = Number(k);
				}
				// console.log("Key: " + k );
				if( k in vv ){
					if( x.length > 1 ){
						x.splice(0,1);
						if( typeof(vv[ k ]) == "object" && vv[ k ] != null ){
							var a_ = this.s2_rav_bus_teg( vv[ k ], x.join(":") );
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
			}catch(e){console.log("s2_rav_bus_teg error: " +  + vpath + ": " + e );return false;}
		},
		is_token_ok(t){
			if( t!= "OK" && t.match(/^[a-f0-9]{24}$/)==null ){
				setTimeout(this.token_validate,100,t);
				return false;
			}else{
				return true;
			}
		},
		open_edit_form: function(){
			this.edit_modal = new bootstrap.Modal(document.getElementById('edit_modal'));
			this.edit_modal.show();
			this.cmsg = ""; this.cerr = "";
			this.edit_function = this.s2_nnnnnnnosj(this.s2_nnnoitcnuf);
		},
		token_validate(t){
			if( t.match(/^(SessionChanged|NetworkChanged)$/) ){
				this.err = "Login Again";
				alert("Need to Login Again");
			}else{
				this.err = "Token Error: " + t;
			}
		},
		replace_variables_in_object: function( vd ){
			return vd;
		},
		load_functions(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?", {"action":"get_token","event":"getfunctions","expire":2}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									this.load_functions2();
								}
							}else{
								alert("Token error: " + response.data['data']);
								this.err = "Token Error: " + response.data['data'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response Type";
					}
				}else{
					this.err = "Response Error: " . response.status;
				}
			});
		},
		load_functions2(){
			this.msg = "Loading...";
			this.err = "";
			axios.post("?",{"action":"get_functions","token":this.token}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.functions = response.data['data'];
							}else{
								alert("Token error: " + response.data['error']);
								this.err = "Token Error: " + response.data['error'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response Type";
					}
				}else{
					this.err = "Response Error: " . response.status;
				}
			});
		},
		cleanit(v){
			v = v.replace( /\-/g, "DASH" );
			v = v.replace( /\_/g, "UDASH" );
			v = v.replace( /\W/g, "-" );
			v = v.replace( /DASH/g, "-" );v = v.replace( /UDASH/g, "_" );
			v = v.replace( /[\-]{2,5}/g, "-" );
			v = v.replace( /[\_]{2,5}/g, "_" );
			return v;
		},
		editnow(){
			this.cerr = "";
			this.edit_function['name'] = this.cleanit(this.edit_function['name']);
			if( this.edit_function['name'].trim() == "" ){
				this.cerr = "Name incorrect";
				return false;
			}
			if( this.edit_function['des'].match(/^[a-z0-9\.\-\_\&\,\!\@\'\"\t\ \r\n]{5,200}$/i) == null ){
				this.cerr = "Description incorrect. Special chars not allowed";
				return false;
			}
			this.cmsg = "Editing...";
			axios.post("?", {
				"action":"get_token",
				"event":"edit_function"+this.edit_function['_id'],
				"expire":2
			}).then(response=>{
				this.msg = "";
				if( response.status == 200 ){
					if( typeof(response.data) == "object" ){
						if( 'status' in response.data ){
							if( response.data['status'] == "success" ){
								this.token = response.data['token'];
								if( this.is_token_ok(this.token) ){
									axios.post("?", {
										"action": "edit_function", 
										"edit_function": this.edit_function,
										"token": this.token
									}).then(response=>{
										this.cmsg = "";
										if( response.status == 200 ){
											if( typeof(response.data) == "object" ){
												if( 'status' in response.data ){
													if( response.data['status'] == "success" ){
														this.cmsg = "Created";
														this.edit_modal.hide();
														this.s2_nnnoitcnuf = JSON.parse( JSON.stringify(this.edit_function));
													}else{
														this.cerr = response.data['error'];
													}
												}else{
													this.cerr = "Incorrect response";
												}
											}else{
												this.cerr = "Incorrect response Type";
											}
										}else{
											this.cerr = "Response Error: " . response.status;
										}
									});
								}
							}else{
								alert("Token error: " + response.dat['data']);
								this.err = "Token Error: " + response.data['data'];
							}
						}else{
							this.err = "Incorrect response";
						}
					}else{
						this.err = "Incorrect response Type";
					}
				}else{
					this.err = "Response Error: " . response.status;
				}
			});
		},
		s2_atad_laitini_daol: function(){
			var s2_dddddddddv = {
				"action"		: "load_engine_data",
			};
			axios.post( "?",s2_dddddddddv).then(response=>{
				if( response.data["status"] == "success" ){
					if( typeof( response.data["engine"] ) == "object" && 'length' in response.data["engine"] == false ){
						this.s2_eeeeenigne		= response.data["engine"];
					}
					if( typeof( response.data["test"] ) == "object" && 'length' in response.data["test"] == false ){
						this.s2_tttttttset 		= response.data["test"];
					}
					this.s2_2atad_laitini_daol();
				}else{
					alert("Server Error.Please Try After Sometime");
				}
			});
		},
		s2_2atad_laitini_daol: function(){
			///console.log("load initial data2 ");
			if( this.s2_eeeeenigne.hasOwnProperty("input_factors") == false ){
				this.s2_eeeeenigne['input_factors'] ={};
				this.s2_ddeen_evas=true;
			}else if( this.s2_eeeeenigne["input_factors"].hasOwnProperty("length") ){
				this.s2_eeeeenigne['input_factors'] = {};
				this.s2_ddeen_evas=true;
			}
			//console.log( this.s2_eeeeenigne );
			if( 'output' in this.s2_eeeeenigne == false ){
				this.s2_eeeeenigne['output'] = {"t":"O", "_":{}};
			}
			if( 'stages' in this.s2_eeeeenigne == false ){
				this.s2_eeeeenigne['stages'] = [
					    {
					      "k": {"v": "Let", "t": "c", "vs": false}, "pk": "Let",
					      "t": "c",
					      "d": {"lhs": "a","rhs": {"t": "N","v": 10}},
					      "l": 1,"e": false,"ee": true,"er": "","wr": ""
					    },
					    {
					      "k": {"v": "Let", "t": "c", "vs": false}, "pk": "Let",
					      "t": "c",
					      "d": {"lhs": "b","rhs": {"t": "N","v": 10}},
					      "l": 1,"e": false,"ee": true,"er": "","wr": ""
					    },
					    {
					      "k": {"v": "Let", "t": "c", "vs": false}, "pk": "Let",
					      "t": "c",
					      "d": {"lhs": "c","rhs": {"t": "N","v": 0}},
					      "l": 1,"e": false,"ee": true,"er": "","wr": ""
					    },
					    {
					      "k": {"v": "Math","t": "c","vs": false},"pk": "Math",
					      "t": "c",
					      "d": {
					        "lhs": {"t": "V","v": {"v": "c","t": "N","vs": false}},
					        "rhs": [
					            {"m": [
					              {"t": "V","v": {"v": "a","t": "N","vs": false},"OP": "+"},
					              {"t": "V","v": {"v": "b","t": "N","vs": false},"OP": "+"},
					              {"t": "N","v": "10","OP": "."}
					            ],"OP": "."}
					        ]
					      },
					      "l": 1,"e": false,"ee": true,"er": "","wr": ""
					    },
					    {
					      "k": {"v": "Respond","t": "c","vs": false},"pk": "Respond",
					      "t": "c",
					      "d": {"t": "O","v": {"status": {"t": "T","v": "success","k":"status"},"data": {"t": "V","v": {"v": "c","t": "N","vs": {"v": "","t": "","d": []}},"k":"data"}}},
					      "l": 1,"e": false,"ee": true,"er": "","wr": ""
					    }
				];
				this.s2_ddeen_evas=true;
			}
			var dt = new Date();
			this.s2_raey_tnerruc = dt.getFullYear();
			this.s2_yadot_etad = dt.toJSON().substr(0,10);
			this.s2_eeemitetad = dt.toJSON().substr(0,19).replace("T", " " );
			this.s2_skcehc_dnif();
			this.s2_selbairav_llif();
			this.vshow	= true;
			this.s2_tnemnorivne_tset_tceles();
		},
		s2_egnahc_epyt_tupni: function(){
			this.s2_ddeen_evas = true;
		},
		s2_egnahc_dohtem_egap: function(){
			this.s2_ddeen_evas = true;
		},
		s2_detide_srotcaf_tupni: function( vdata ){
			this.s2_eeeeenigne[  'input_factors' ] =  vdata ;
			this.s2_ddeen_evas = true;
			this.s2_noitpo_detadpu();
		},
		s2_detide_srotcaf_tset: function( vdata ){
			this.s2_tttttttset['factors']['v'] =  vdata;
			this.s2_tnemnorivne_tset_tceles();
		},
		get_object_props_list: function( stage_id, k ){
			console.log("Get object props list: " + stage_id + ": " + k );
			//this.s2_ooooooohce( this.s2_esiw_egats_srotcaf_lla[ stage_id ]  );
			var o = [];
			if( k in this.s2_esiw_egats_srotcaf_lla[ stage_id ] ){
				if( this.s2_esiw_egats_srotcaf_lla[ stage_id ][k]['t'] == "O" && '_' in this.s2_esiw_egats_srotcaf_lla[ stage_id ][k] ){
					o = this.s2_tsil_ot_tcejbo_teg( this.s2_esiw_egats_srotcaf_lla[ stage_id ][k]['_'] );
				}
				if( this.s2_esiw_egats_srotcaf_lla[ stage_id ][k]['t'] == "L" && '_' in this.s2_esiw_egats_srotcaf_lla[ stage_id ][k] ){
					o = this.s2_tsil_ot_tcejbo_teg( this.s2_esiw_egats_srotcaf_lla[ stage_id ][k]['_'] );
				}
			}
			//this.s2_ooooooohce( o );
			return o;
		},
		s2_tsil_ot_tcejbo_teg: function( vd ){
			// this.s2_ooooooohce( "s2_tsil_ot_tcejbo_teg" );
			// this.s2_ooooooohce( vd );
			var v = this.s2_2tsil_ot_tcejbo_teg( vd, "" );
			return v
		},
		s2_2tsil_ot_tcejbo_teg: function( vd, vp ){
			// this.s2_ooooooohce( vd );
			// this.s2_ooooooohce( vp );
			var v = [];
				for( var i in vd ){
					v.push({
						"k": vp + i,
						"t": vd[i]['t'],
					});
					if( vd[i]['t'] == "O" ){
						var v2 = this.s2_2tsil_ot_tcejbo_teg( vd[i]['_'], vp + i + "->" );
						for( var i2=0;i2<v2.length;i2++){
							v.push( v2[i2] );
						}
					}
					if( vd[i]['t'] == "L" ){
						if( typeof(vd[i]['_'])=="object" ){
							if( "length" in vd[i]['_'] ){
								if( vd[i]['_'].length >0 ){
									v.push({
										"k": vp + i + "->[]",
										"t": vd[i]['_']['t'],
									});
									if( vd[i]['_']['t'] == "O" ){
										var v2 = this.s2_2tsil_ot_tcejbo_teg( vd[i]['_']['_'], vp + i + "->[]->" );
										for( var i2=0;i2<v2.length;i2++){
											v.push( v2[i2] );
										}
									}
								}
							}
						}
					}
				}
			return v;
		},
		s2_selbairav_egrem: function( vo, vd ){
			for( var key in vd ){
				vo[ key+'' ] = this.s2_nnnnnnnosj(vd[key]);
			}
			//return vo;
		},
		s2_ttttttrosk: function( vd ){
			var oo = {};
			var _o = Object.keys(vd).sort();
			for( var i in _o ){
				oo[ _o[i]+"" ] = vd[ _o[i] ];
			}
			return oo
		},
		s2_noitaton_tcejbo_teg( v ){
			var vv = {};
			if( typeof(v)==null ){
				console.error("get_object_notation: null ");
			}else if( typeof(v)=="object" ){
				if( "length" in v == false ){
					for(var k in v ){
						if( v[k]['t'] == "V" ){
							vv[ k ] = this.s2_sepyt_atad[ v[k]['t'] ] + "["+v[k]['v']['v']+"]";
							if( 'vs' in v[k]['v'] ){
								if( v[k]['v']['vs'] ){
									if( v[k]['v']['vs']['v'] ){
										vv[ k ] = vv[ k ] + '->' + v[k]['v']['vs']['v'];
									}
								}
							}
						}else{
							vv[ k ] = this.s2_eulav_evired(v[k]);
						}
					}
				}else{ console.error("get_object_notation: got list instead of object "); this.s2_ooooooohce(v); }
			}else{ console.error("get_object_notation: incorrect type: "+ typeof(v) ); }
			return Object.fromEntries(Object.entries(vv).sort());
		},
		s2_noitaton_tsil_teg( v ){
			//this.s2_ooooooohce( "get object notation" );
			//this.s2_ooooooohce( v );
			var vv = [];
			if( typeof(v)=="object" ){
				if( "length" in v ){
					for(var k=0;k<v.length;k++ ){
						if( v[k]['t'] == "V" ){
							nv = this.s2_sepyt_atad[ v[k]['t'] ] + "["+v[k]['v']['v']+"]";
							if( 'vs' in v[k]['v'] ){
								if( v[k]['v']['vs'] ){
									if( v[k]['v']['vs']['v'] ){
										nv = nv + '->' + v[k]['v']['vs']['v'];
									}
								}
							}
							vv.push(nv);
						}else{
							vv.push( this.s2_eulav_evired(v[k]) );
						}
					}
				}else{ console.error("get_list_notation: not a list "); }
			}else{ console.error("get_list_notation: incorrect type: "+ typeof(v) ); }
			return vv;
		},
		s2_noitaton_tcejbo_dnocbd_teg(v){
			return v;
			var vv = {};
			for(var k in v ){
				if( v[k]['t'] == "V" ){
					vv[ k ] = this.s2_sepyt_atad[ v[k]['t'] ] + "["+v[k]['v']['v']+"]";
					if( 'vs' in v[k]['v'] ){
						if( v[k]['v']['vs'] ){
							if( v[k]['v']['vs']['v'] ){
								vv[ k ] = vv[ k ] + '->' + v[k]['v']['vs']['v'];
							}
						}
					}
				}else{
					vv[ k ] = this.s2_eulav_evired(v[k]);
				}
			}
			return Object.fromEntries(Object.entries(vv).sort());
			return vv;
		},
		s2_segats_bus_ni_egnahc_elbairav_etadpu: function( sid, vold, vnew ){
			for(var s2_iiiiiegats=sid;s2_iiiiiegats<this.s2_eeeeenigne['stages'].length;s2_iiiiiegats++ ){
				var s2_dddddegats = this.s2_eeeeenigne['stages'][s2_iiiiiegats];
				if( s2_dddddegats['k']['t'] == 'o' || s2_dddddegats['k']['t'] == 'PLG' ){
					if( s2_dddddegats['k']['v'] == vold ){
						s2_dddddegats['k']['v'] = vnew;
					}
				}
				this.s2_smarap_egats_bus_ni_egnahc_elbairav_etadpu( s2_dddddegats, vold, vnew );
			}
		},
		s2_smarap_egats_bus_ni_egnahc_elbairav_etadpu: function( vv, vold, vnew ){
			try{
				if( "t" in vv && "v" in vv ){
					if( vv['t'] == "V" || vv['t'] == "PLG" ){
						if( typeof( vv['v'] ) == "object" && vv['v'] != null ){
							if( vv['v']['v'] == vold ){
								vv['v']['v'] = vnew;
							}
						}
					}
				}
				for( var k in vv ){
					if( typeof(vv[k]) == "object" && vv[k] != null ){
						this.s2_smarap_egats_bus_ni_egnahc_elbairav_etadpu( vv[ k ], vold, vnew );
					}
				}
			}catch(e){console.error(e);console.log("s2_rav_bus_tes error: " + vpath );return false;}
		},
		s2_segats_bus_ni_egnahc_epyt_elbairav_etadpu: function( sid, a, t ){
			for(var s2_iiiiiegats=sid;s2_iiiiiegats<this.s2_eeeeenigne['stages'].length;s2_iiiiiegats++ ){
				var s2_dddddegats = this.s2_eeeeenigne['stages'][s2_iiiiiegats];
				this.s2_smarap_egats_bus_ni_egnahc_epyt_elbairav_etadpu( s2_dddddegats, a, t );
			}
		},
		s2_smarap_egats_bus_ni_egnahc_epyt_elbairav_etadpu: function( vv, a, t ){
			try{
				if( "t" in vv && "v" in vv ){
					if( vv['t'] == "V" || vv['t'] == "PLG" ){
						if( typeof( vv['v'] ) == "object" && vv['v'] != null ){
							if( vv['v']['a'] == a ){
								if( vv['v']['t'] != t ){
									vv['v']['t'] = t+'';
								}
							}
						}
					}
				}
				for( var k in vv ){
					if( typeof(vv[k]) == "object" && vv[k] != null ){
						this.s2_smarap_egats_bus_ni_egnahc_epyt_elbairav_etadpu( vv[ k ], a, t );
					}
				}
			}catch(e){console.error(e);console.log("s2_rav_bus_tes error: " + e ); console.log( vold ); console.log( vnew ); return false;}
		},
		s2_selbairav_llif: function(){
			var used_outputs = {};
			var o = {};
			vin = this.s2_srav_egats_ot_erutcurts_yarra_trevnoc( this.s2_eeeeenigne['input_factors'] );
			this.s2_selbairav_egrem( o, vin );
			for(var s2_iiiiiegats=0;s2_iiiiiegats<this.s2_eeeeenigne['stages'].length;s2_iiiiiegats++ ){
				var s2_dddddegats = this.s2_eeeeenigne['stages'][s2_iiiiiegats];
				this.s2_iiiiiegats = s2_iiiiiegats;
				//console.log("s2_selbairav_llif stage:" + s2_iiiiiegats + ": " + s2_dddddegats['k']['v'] + ": " + s2_dddddegats['k']['t'] );
				this.s2_esiw_egats_srotcaf_lla[  Number(s2_iiiiiegats) ] =  this.s2_nnnnnnnosj(o);
				var er = "";var wr = "";

				var sub_wr = this.s2_secnereffid_epyt_bus_dnif( s2_iiiiiegats, o, s2_dddddegats );
				if( sub_wr ){
					wr = wr + sub_wr;
				}

				if( s2_dddddegats['t'] != 'c' && s2_dddddegats['t'] != 'n' ){
					if( this.find_o_sub_var(o, s2_dddddegats['k']['v']) == false ){
						er = er + " Variable `" + s2_dddddegats['k']['v'] +"` not available;";
					}
					if( 'vs' in s2_dddddegats['k'] ){if( typeof(s2_dddddegats['k']['vs'])=="object" ){ if( 'd' in s2_dddddegats['k']['vs'] ){
						if( s2_dddddegats['k']['vs']['d']['self'] == false ){
							er = er + " result assignment missing; ";
						}
					}}}
					er = er + this.s2_egasu_elbairav_dnif( o, s2_dddddegats['k']['vs'] );
					wr = wr + this.s2_ytpme_elbairav_dnif( s2_dddddegats['k']['vs'] );
				}else{
					er = er + this.s2_egasu_elbairav_dnif( o, s2_dddddegats['d'] );
					wr = wr + this.s2_ytpme_elbairav_dnif( s2_dddddegats['d'] );
				}

				if( s2_dddddegats['k']['v'] == "LetComponent" ){
					//o[ s2_dddddegats['d']['lhs']+'' ] = this.get_variable_final_form_as_input( this.s2_nnnnnnnosj( d ) );
					//o[ s2_dddddegats['d']['lhs']+'' ] = this.get_variable_final_form_as_input( this.s2_nnnnnnnosj( s2_dddddegats['d']['rhs'] ) );
					if( s2_dddddegats['d']['lhs'] == "" ){
						er = er + " lhs variable empty";
					}else if( s2_dddddegats['d']['lhs'] in o ){
						wr = wr + " Warning variable `" + s2_dddddegats['d']['lhs'] + "` override;";
					}
					var comp = s2_dddddegats['d']['rhs']['v']['i']['v']+'';
					// this.s2_ooooooohce("LetComponent: " );
					// this.s2_ooooooohce( this.s2_nnnnnnnosj( this.s2_atad_nigulp[ comp ]['data'] ) );
					if( comp in this.s2_atad_nigulp ){
						o[ s2_dddddegats['d']['lhs']+'' ] = {
							"t": comp, 
							"_": {}
						};
						//this.s2_srav_egats_ot_erutcurts_yarra_trevnoc( this.s2_nnnnnnnosj( this.s2_atad_nigulp[ comp ]['data'] ) )
						// this.s2_ooooooohce("LetComponent: " );
						// this.s2_ooooooohce( o[ s2_dddddegats['d']['lhs']+'' ] );
					}
					//o[ s2_dddddegats['d']['lhs']+'' ]["PLG"] = 
				}else if( s2_dddddegats['k']['v'] == "Let" ){
					if( s2_dddddegats['d']['lhs'] == "" ){
						er = er + " lhs variable empty";
					}else if( s2_dddddegats['d']['lhs'] in o ){
						wr = wr + " Warning variable `" + s2_dddddegats['d']['lhs'] + "` override;";
					}
					if( s2_dddddegats['d']['rhs']['t'] == "V" ){
						if( s2_dddddegats['d']['rhs']['v']['v'] != "" ){
							var d = this.get_o_sub_var( o, s2_dddddegats['d']['rhs']['v']['v'] );
							//this.s2_ooooooohce( d );
							//this.s2_ooooooohce( s2_dddddegats['d']['rhs'] );
							if( d ){
								if( 'vs' in s2_dddddegats['d']['rhs']['v'] ){
									if( s2_dddddegats['d']['rhs']['v']['vs'] != false ){
										if( 'd' in s2_dddddegats['d']['rhs']['v']['vs'] ){
											if( typeof( s2_dddddegats['d']['rhs']['v']['vs']['d'] ) == "object" ){
												if( s2_dddddegats['d']['rhs']['v']['t'] == "L" && s2_dddddegats['d']['rhs']['v']['vs']['v'] == 'getItem' ){
													d = {'t':'O','_':this.s2_nnnnnnnosj( d )['_'] };
												}else if( 'return' in s2_dddddegats['d']['rhs']['v']['vs']['d'] ){
													d = {"t": s2_dddddegats['d']['rhs']['v']['vs']['d']['return'], "_":{}};
													//if( s2_dddddegats['d']['rhs']['v']['vs']['d'][''] )
													if( 'structure' in s2_dddddegats['d']['rhs']['v']['vs']['d'] ){
														var d = this.s2_nnnnnnnosj(s2_dddddegats['d']['rhs']['v']['vs']['d']['structure']);
													}
												}
											}else{
												this.s2_ooooooohce("Stage Let Variable: vs d not found!");
											}
										}
									}
								}
								//this.s2_ooooooohce( d );
								//this.s2_ooooooohce( this.get_variable_final_form_as_input( this.s2_nnnnnnnosj( d ) ) );
								o[ s2_dddddegats['d']['lhs']+'' ] = this.get_variable_final_form_as_input( this.s2_nnnnnnnosj( d ) );
							}else{
								console.log("Let Stage variable: " + s2_dddddegats['d']['rhs']['v']['v'] + ": not found in all factors" );
							}
						}else{
							console.log("Let variable empty!");
						}
						//o[ s2_dddddegats['d']['n']+'' ] = 'T';
					}else if( s2_dddddegats['d']['rhs']['t'] == "TH" ){
						if( s2_dddddegats['d']['rhs']['v']['l']['v'] != "" ){
							var p = s2_dddddegats['d']['rhs']['v']+'';
							if( p in this.s2_atad_nigulp ){
								o[ s2_dddddegats['d']['lhs']+'' ] = {"t":"PLG","plg":p};
							}else{
								console.log("Let Stage variable: " + s2_dddddegats['d']['rhs']['v']['v'] + ": not found in plugins" );
							}
						}else{
							console.log("Let variable empty!");
						}
					}else if( s2_dddddegats['d']['rhs']['t'] == "THL" ){
						if( s2_dddddegats['d']['rhs']['v']['th'] != "" ){
							var p = s2_dddddegats['d']['rhs']['v']['th']+'';
							o[ s2_dddddegats['d']['lhs']+'' ] = {"t":"THL","th":p};
							this.s2_desu_sgniht[ p ] = [];
						}else{
							console.log("Let variable empty!");
						}
					}else if( s2_dddddegats['d']['rhs']['t'] == "TH" ){
						if( s2_dddddegats['d']['rhs']['v']['th'] != "" ){
							var p = s2_dddddegats['d']['rhs']['v']['th']+'';
							o[ s2_dddddegats['d']['lhs']+'' ] = {"t":"TH","th":p};
							this.s2_desu_sgniht[ p ] = [];
						}else{
							console.log("Let variable empty!");
						}
						//o[ s2_dddddegats['d']['n']+'' ] = 'T';
					}else{
						o[ s2_dddddegats['d']['lhs']+'' ] = this.get_variable_final_form_as_input( this.s2_nnnnnnnosj( s2_dddddegats['d']['rhs'] ) );
					}
				}
				if( s2_dddddegats['k']['v'] == "Assign" ){
					if( s2_dddddegats['d']['lhs']['t'] != "V" ){
						er = er + " Incorrect lhs type";
					}
					var t1 = s2_dddddegats['d']['lhs']['v']['t'];
					var t2 = s2_dddddegats['d']['rhs']['t'];
					if( t2 == "O" ){
						//this.s2_ooooooohce("Assign Object" );
						this.set_o_sub_var(o, s2_dddddegats['d']['lhs']['v']['v'], this.get_variable_final_form_as_input( this.s2_nnnnnnnosj( s2_dddddegats['d']['rhs'] ) ) );
					}
					if( t2 == "V" ){
						var t2 = s2_dddddegats['d']['rhs']['v']['t'];
						if( t2 in this.s2_seitreporp_tcejbo_gifnoc ){
							var d = this.get_o_sub_var( o, s2_dddddegats['d']['rhs']['v']['v'] );
							if( 'vs' in s2_dddddegats['d']['rhs']['v'] ){
								if( s2_dddddegats['d']['rhs']['v']['vs'] != false ){
									var fn = s2_dddddegats['d']['rhs']['v']['vs']['v'];
									if( fn != '' ){
										if( fn in this.s2_seitreporp_tcejbo_gifnoc[ t2 ] == false ){
											er = er + " function:`" + fn + "` not found in " + t2;
										}else{
											t2 = this.s2_seitreporp_tcejbo_gifnoc[ t2 ][ fn ]['return'];
											if( t1 != t2 ){
												wr = wr + " Warning: data type mismatch: " + t1 + " = " + t2;
											}
										}
									}
									if( 'd' in s2_dddddegats['d']['rhs']['v']['vs'] ){
										if( typeof( s2_dddddegats['d']['rhs']['v']['vs']['d'] ) == "object" ){
											if( s2_dddddegats['d']['rhs']['v']['t'] == "L" && s2_dddddegats['d']['rhs']['v']['vs']['v'] == 'getItem' ){
												d = {'t':'O','_':this.s2_nnnnnnnosj( d )['_'] };
											}else if( 'return' in s2_dddddegats['d']['rhs']['v']['vs']['d'] ){
												d = {"t": s2_dddddegats['d']['rhs']['v']['vs']['d']['return'], "_":{}};
												//if( s2_dddddegats['d']['rhs']['v']['vs']['d'][''] )
												if( 'structure' in s2_dddddegats['d']['rhs']['v']['vs']['d'] ){
													var d = this.s2_nnnnnnnosj(s2_dddddegats['d']['rhs']['v']['vs']['d']['structure']);
												}
											}
											if( d ){
												o[ s2_dddddegats['d']['lhs']['v']['v'] ] = this.get_variable_final_form_as_input( d );
											}
										}else{
											this.s2_ooooooohce("Stage Let Variable: vs d not found!");
										}
									}
								}
							}
						}else{
							console.log( t2 + ": not found in object props" );
						}
					}
				}
				if( s2_dddddegats['k']['v'] == "FunctionCall" ){
					if( 'return' in s2_dddddegats['d']['fn']['v'] ){
						o[ s2_dddddegats['d']['lhs']['v']['v']+'' ] = s2_dddddegats['d']['fn']['v']['return'];
					}
				}
				if( s2_dddddegats['k']['v'] == "For" ){
					if( s2_dddddegats['d']['as'] in o ){
						wr = wr + " Warning variable `" + s2_dddddegats['d']['as'] + "` override;";
					}
					o[ s2_dddddegats['d']['as']+'' ] = {"t":"N", "v":""};
				}
				if( s2_dddddegats['k']['v'] == "EndFor" ){
					delete o[ s2_dddddegats['d']['as']+'' ];
				}
				if( s2_dddddegats['k']['v'] == "ForEach" ){
					if( s2_dddddegats['d']['key'] in o ){
						wr = wr + " Warning variable `" + s2_dddddegats['d']['key'] + "` override;";
					}
					if( s2_dddddegats['d']['value'] in o ){
						wr = wr + " Warning variable `" + s2_dddddegats['d']['value'] + "` override;";
					}
					if( s2_dddddegats['d']['var']['v']['v'] in o ){
						if( o[ s2_dddddegats['d']['var']['v']['v'] ]['t'] != "O" && o[ s2_dddddegats['d']['var']['v']['v'] ]['t'] != "L" ){
							wr = wr + " Warning variable `" + s2_dddddegats['d']['var']['v']['v'] + "` is not a List or Assoc List";
						}
					}else{
						this.s2_ooooooohce(" 111 " )
					}
					var key = {"t":"T"};
					var val = {"t":"T"};
					var arr = s2_dddddegats['d']['var']['v']['v'];
					var d = this.get_o_sub_var(o, s2_dddddegats['d']['var']['v']['v']);
					// this.s2_ooooooohce( "value" );
					// this.s2_ooooooohce( d );
					// this.s2_ooooooohce( "value2" );
					if( d ){
						var t = d['t'];
						if( t == "O" ){
							if( '_' in d ){
								key = {"t":"T"};
								val = d['_'][ Object.keys(d['_'])[0] ];
							}else{
								er = er + " incorrect source var `"+s2_dddddegats['d']['var']['v']['v']+"` structure";
							}
						}else if( t == "L" ){
							if( '_' in d ){
								key = {"t":"N"};
								if( typeof(d['_']) == 'object' && "length" in d['_']){
									val = d['_'][0]['_'];
								}else{
									val = d['_'];
								}
							}else{
								er = er + " incorrect source var `"+s2_dddddegats['d']['var']['v']['v']+"` structure";
							}
						}else{
							er = er + " incorrect source var `"+s2_dddddegats['d']['var']['v']['v']+"` structure";
						}
					}

					//this.s2_ooooooohce( "-----" );
					//this.s2_ooooooohce( val );

					o[ s2_dddddegats['d']['key']+'' ] = key;
					o[ s2_dddddegats['d']['value']+'' ] = {'t':'O', '_':val};
				}
				if( s2_dddddegats['k']['v'] == "EndForEach" ){
					var i = this.s2_dnar_verp_dnif( s2_iiiiiegats );
					var d = this.s2_eeeeenigne['stages'][i]['d'];
					delete o[ d['key']+'' ];
					delete o[ d['value']+'' ];
				}
				if( s2_dddddegats['k']['v'] == "MongoDb" ){
					if( 'data' in s2_dddddegats['d']){
						if( 'output' in s2_dddddegats['d']['data'] ){
							var oo = s2_dddddegats['d']['data']['output']['v']+'';
							var act = s2_dddddegats['d']['data']['action']['v']+'';
							if( act == "FindOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"O", "_":s2_dddddegats['d']['data']['projects'] },
									"error": {"t":"T"}
								}}
							}
							if( act == "FindMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"L", "_":s2_dddddegats['d']['data']['projects']},
									"error": {"t":"T"}
								}}
							}
							if( act == "InsertOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"insertId": {"t":"T"},
									"error": {"t":"T"}
								}}
							}
							if( act == "UpdateOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"modified_count":  {"t":"N"},
									"upserted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "UpdateMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"modified_count":  {"t":"N"},
									"upserted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "DeleteOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"deleted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "DeleteMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"deleted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
						}
					}
				}
				if( s2_dddddegats['k']['v'] == "MySql" ){
					if( 'data' in s2_dddddegats['d']){
						if( 'output' in s2_dddddegats['d']['data'] ){
							var oo = s2_dddddegats['d']['data']['output']['v']+'';
							var act = s2_dddddegats['d']['data']['query']['v']+'';
							console.log( "mysql" );
							console.log( oo );
							console.log( act );
							if( act == "Select" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"L", "_":s2_dddddegats['d']['data']['fields']['v']},
									"error": {"t":"T"},
									"count": {"t":"N"}
								}}
							}
							if( act == "SelectAssoc" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"O", "_":{}},
									"error": {"t":"T"},
									"count": {"t":"N"}
								}}
							}
							if( act == "SelectKeyValue" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"O", "_":{}},
									"error": {"t":"T"},
									"count": {"t":"N"}
								}}
							}
							if( act == "Update" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"updated":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "Delete" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"deleted":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "Insert" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"insertId":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
						}
					}
				}
				if( s2_dddddegats['k']['v'] == "Internal-Table" ){
					if( 'data' in s2_dddddegats['d'] ){
						if( 'output' in s2_dddddegats['d']['data'] ){
							var oo = s2_dddddegats['d']['data']['output']['v']+'';
							var act = s2_dddddegats['d']['data']['action']['v']+'';
							if( act == "FindOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"O", "_":s2_dddddegats['d']['data']['projects'] },
									"error": {"t":"T"}
								}}
							}
							if( act == "FindMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"data": {"t":"L", "_":s2_dddddegats['d']['data']['projects']},
									"error": {"t":"T"}
								}}
							}
							if( act == "InsertOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"insertId": {"t":"T"},
									"error": {"t":"T"}
								}}
							}
							if( act == "UpdateOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"modified_count":  {"t":"N"},
									"upserted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "UpdateMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"modified_count":  {"t":"N"},
									"upserted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "DeleteOne" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"deleted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
							if( act == "DeleteMany" ){
								o[ oo ] = {"t": "O", "_":{
									"status": {"t":"T"},
									"matched_count":  {"t":"N"},
									"deleted_count":  {"t":"N"},
									"error": {"t":"T"}
								}}
							}
						}
					}
				}
				if( s2_dddddegats['k']['v'] == "HTTPRequest" ){
					var oo = s2_dddddegats['d']['data']['output']['v']+'';
					o[ oo ] = {"t": "O", "_":s2_dddddegats['d']['data']['struct']};
				}
				if( s2_dddddegats['k']['v'] == "RespondJSON" ){
					this.s2_eeeeenigne['output'] = this.get_variable_final_form_as_input( this.s2_nnnnnnnosj( s2_dddddegats['d']['output'] ) );
				}
				if( s2_dddddegats['k']['v'] == "RespondVar" ){
					var d = this.get_o_sub_var( o, s2_dddddegats['d']['output']['v']['v'] );
					//this.s2_ooooooohce( d );
					this.s2_eeeeenigne['output'] = this.get_variable_final_form_as_input( this.s2_nnnnnnnosj( d ) );
				}

				this.s2_eeeeenigne['stages'][ s2_iiiiiegats ]['er'] = er;
				this.s2_eeeeenigne['stages'][ s2_iiiiiegats ]['wr'] = wr;

				if( s2_dddddegats['k']['v'] != "none" ){
					if( s2_dddddegats['k']['v'] in this.s2_smarap_egats ){
						if( '_o_v' in s2_dddddegats ){
							for( var oi in s2_dddddegats['_o_v'] ){
								o[ oi+'' ] = s2_dddddegats['_o_v'][ oi ];
							}
						}
					}
				}
			}

			var ol= [];
			for( var i=0; i<this.s2_eeeeenigne['stages'].length; i++ ){
				if( this.s2_eeeeenigne['stages'][i]['k']['v'] == "SetLabel" ){
					ol.push( this.s2_eeeeenigne['stages'][i]['d']['v']+"" );
				}
			}
			this.s2_seman_lebal = ol;
		},
		s2_egasu_elbairav_dnif: function( vars, vd ){
			// (vd);
			var er = "";
			try{
			if( vd == null ){}else
			if( typeof(vd) == "object" && "length" in vd == false ){
				if( 't' in vd && 'v' in vd ){
					if( vd['t'] == "V" ){
						if( vd['v']['v'] == "" ){
							//er = er + " Variable selection missing;";
						}else if( this.find_o_sub_var(vars, vd['v']['v']) == false ){
							er = er + " Variable `" + vd['v']['v'] + "` is not available;";
						}
					}else{
						if( vd['v'] == null ){}else
						if( typeof(vd['v']) =='object' ){
							er = er + this.s2_egasu_elbairav_dnif(vars, vd['v']);
						}
					}
				}else for(var k in vd){
					if( vd[k] == null ){}else
					if( typeof(vd[k]) =='object' ){
						er = er + this.s2_egasu_elbairav_dnif(vars, vd[k]);
					}
				}
			}else if( typeof(vd) == "object" && "length" in vd ){
				for(var i=0;i<vd.length;i++){
					if( vd[i] == null ){}else 
					if( typeof(vd[i]) == "object" && vd[i] != null ){
						if( 't' in vd[i] && 'v' in vd[i] ){
							if( vd[i]['t'] == "V" ){
								if( vd[i]['v']['v'] == "" ){
									//er = er + " Variable selection missing;";
								}else if( this.find_o_sub_var(vars, vd[i]['v']['v']) == false ){
									er = er + " Variable `" + vd[i]['v']['v'] + "` is not available;";
								}
							}else{
								if( vd[i]['v'] == null ){}else 
								if( typeof(vd[i]['v']) =='object' ){
									er = er + this.s2_egasu_elbairav_dnif(vars, vd[i]['v']);
								}
							}
						}else if( typeof(vd[i]) == "object" && vd[i] != null ){
						 	for(var k in vd[i]){
						 		if( vd[i][k] == null ){}else
						 		if( typeof(vd[i][k]) =='object' ){
									er = er + this.s2_egasu_elbairav_dnif(vars, vd[i][k]);
								}
							}
						}
					}else{
						er = er + this.s2_egasu_elbairav_dnif(vd[i]);
					}
				}
			}
			}catch(e){
				console.error("s2_egasu_elbairav_dnif: " + e);
				this.s2_ooooooohce( vd );
			}
			return er;
		},
		s2_ytpme_elbairav_dnif: function( vd ){
			var wr = "";
			try{
			if( typeof(vd) == "object" && "length" in vd == false ){
				if( 't' in vd && 'v' in vd ){
					if( vd['t'] == "V" ){
						if( vd['v']['v'] == "" ){
							wr = wr + " Variable selection missing;";
						}
					}else{
						if( vd['v'] == null ){}else
						if( typeof(vd['v']) =='object' ){
							wr = wr + this.s2_ytpme_elbairav_dnif(vd['v']);
						}
					}
				}else for(var k in vd){
					if( vd[k] == null ){}else
					if( typeof(vd[k]) =='object' ){
						wr = wr + this.s2_ytpme_elbairav_dnif(vd[k]);
					}
				}
			}else if( typeof(vd) == "object" && "length" in vd ){
				for(var i=0;i<vd.length;i++){
					if( vd[i] == null ){}else 
					if( typeof(vd[i]) == "object" && vd[i] != null ){
						if( 'length' in vd[i] == false ){
							if( 't' in vd[i] && 'v' in vd[i] ){
								if( vd[i]['t'] == "V" ){
									if( vd[i]['v']['v'] == "" ){
										wr = wr + " Variable selection missing;";
									}
								}else{
									if( vd[i]['v'] == null ){}else 
									if( typeof(vd[i]['v']) =='object' ){
										wr = wr + this.s2_ytpme_elbairav_dnif(vd[i]['v']);
									}
								}
							}else if( typeof(vd[i]) == "object" && vd[i] != null ){
							 	for(var k in vd[i]){
							 		if( vd[i][k] == null ){}else
							 		if( typeof(vd[i][k]) =='object' ){
										wr = wr + this.s2_ytpme_elbairav_dnif(vd[i][k]);
									}
								}
							}
						}else{
							wr = wr + this.s2_ytpme_elbairav_dnif(vd[i]);
						}
					}
				}
			}
			}catch(e){
				console.error("s2_ytpme_elbairav_dnif: " + e);
				this.s2_ooooooohce( vd );
			}
			return wr;
		},
		s2_secnereffid_epyt_bus_dnif: function( s2_iiiiiegats, vars, s2_dddddegats ){
			//this.s2_ooooooohce( "s2_sepyT_elbairav_etadpu_esiw_egats" );
			//this.s2_ooooooohce( s2_dddddegats );
			var wr = "";
			for(var k in s2_dddddegats ){
				//this.s2_ooooooohce( "top: " + k );
				if( typeof(s2_dddddegats[k]) == "object" && s2_dddddegats[k] != null ){
					if( 't' in s2_dddddegats[ k ] && 'v' in s2_dddddegats[ k ] ){
						if( s2_dddddegats[ k ]['t'] == "V" ){
							var av = s2_dddddegats[ k ]['v']['v'];
							var at = s2_dddddegats[ k ]['v']['t'];
							//console.log( "finding: " + av );
							var sv = this.get_o_sub_var(vars, av );
							if( sv ){
								if( at != sv['t'] ){
									console.log( s2_iiiiiegats + " = " + at + ":" + av + " = " + sv['t'] + ":" + sv['v'] );
									wr = wr + " var type for " + av + "=" + at + " but actual is " + sv['t'] + "; ";
								}
							}
						}
					}
					//this.s2_ooooooohce( s2_dddddegats[k] );
					for( var j in s2_dddddegats[k] ){
						//this.s2_ooooooohce( j );
						//this.s2_ooooooohce( s2_dddddegats[k][j] );
						if( s2_dddddegats[k][j] != undefined ){
							if( typeof(s2_dddddegats[k][j]) == "object" && s2_dddddegats[k][j] != null ){
								wr = wr + this.s2_secnereffid_epyt_bus_dnif( s2_iiiiiegats, vars, s2_dddddegats[k][j] );
							}
						}
					}
					//this.s2_ooooooohce( "over==" );
				}
			}
			return wr;
		},
		find_o_sub_var: function( vv, vpath ){
			try{
				//console.log( "find_o_sub_var: "+ vpath );
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
							return this.find_o_sub_var( vv[ k ]['_'], x.join("->") );
						}else if( vv[ k ]['t'] == "L" ){
							return this.get_o_sub_var( vv[ k ]['_'], x.join("->") );
						}else{
							return false;
						}
					}else{
						return true;
					}
				}else{
					return false;
				}
			}catch(e){console.log("find_o_sub_var error");return false;}
		},
		get_o_sub_var: function( vv, vpath ){
			//this.s2_ooooooohce("get_o_sub_var: " );this.s2_ooooooohce( vv ); this.s2_ooooooohce( vpath );
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
							return this.get_o_sub_var( vv[ k ]['_'], x.join("->") );
						}else if( vv[ k ]['t'] == "L" ){
							return this.get_o_sub_var( vv[ k ]['_'], x.join("->") );
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
				//console.log("get_o_sub_var error");
				this.s2_ooooooohce("get_o_sub_var:" + vpath);this.s2_ooooooohce(vv);
				return false;
			}
		},
		set_o_sub_var: function( vv, vpath, value ){
			this.s2_ooooooohce("set_o_sub_var: " );this.s2_ooooooohce( vv ); this.s2_ooooooohce( vpath );
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
							this.set_o_sub_var( vv[ k ]['_'], x.join("->"), value );
						}else if( vv[ k ]['t'] == "L" ){
							this.get_o_sub_var( vv[ k ]['_'], x.join("->"), value );
						}else{
							console.log("set_o_sub_var: false");
						}
					}else{
						vv[ k ]['_'] = value['_'];
					}
				}else{
					vv[ k ]['_'] = value['_'];
				}
			}catch(e){
				//console.log("get_o_sub_var error");
				this.s2_ooooooohce("get_o_sub_var:" + vpath);this.s2_ooooooohce(vv);
				return false;
			}
		},
		s2_trela_egats_wohs(i){
			alert(this.s2_eeeeenigne['stages'][ i ]['er'] );
		},
		get_variable_final_form_as_input: function( v ){
			//console.log("Get variable finaal form:" );this.s2_ooooooohce( v );
			var t = v['t']+'';
			if( t == "TT" ){ t = "T"; }
			var vv = {"t": t+''};
			if( 'vs' in v ){
				//console.log()
				if( v['vs']['v'] ){
					if( v['vs']['v']  in this.s2_seitreporp_tcejbo_gifnoc[ v['t'] ] ){
						var fn = this.s2_seitreporp_tcejbo_gifnoc[ v['t'] ][ v['vs']['v'] ];
						if( fn['return'] == "self" ){
							vv['t'] = v['t']+'';
						}else{
							vv['t'] = fn['return'];
						}
					}else{
						this.s2_ooooooohce("prop: " + v['vs']['v'] + " not found in type: " + v['t'] );
					}
				}
			}
			if( v['t'] == "TH" ){
				if( '_' in v ){
					vv['_'] = this.s2_nnnnnnnosj(v['_']);
				}else if( typeof(v['v'])=="object" && v['v'] != null ){
					this.s2_ooooooohce( v['v'] );
					this.s2_ooooooohce("not knowing from here");
					vv['_'] = this.s2_srav_egats_ot_erutcurts_yarra_trevnoc( this.s2_nnnnnnnosj(v['v']) );
				}else{
					this.s2_ooooooohce("variable type "+v['t'] + " missing sub structure");
				}
			}else if( v['t'] == "L" ){
				if( '_' in v ){
					vv['_'] = this.s2_nnnnnnnosj(v['_']);
				}else if( typeof(v['v'])=="object" && "length" in v['v'] ){
					if( v['v'].length > 0 ){
						var sb = {'t': v['v'][0]['t']}
						if( sb['t'] == "O" ){
							sb['_'] = this.s2_srav_egats_ot_erutcurts_yarra_trevnoc( this.s2_nnnnnnnosj(v['v'][0]['v']) );
						}else if( sb['t'] == "L" ){
							sb['_'] = this.s2_srav_egats_ot_erutcurts_yarra_trevnoc( this.s2_nnnnnnnosj(v['v'][0]['v'][0]) );
						}
						vv['_'] = [sb];
					}else{
						vv['_'] = [];
					}
				}else{
					this.s2_ooooooohce("variable type "+v['t'] + " missing sub structure");
				}
			}else{
				if( '_' in v ){
					vv['_'] = this.s2_nnnnnnnosj(v['_']);
				}else if( typeof(v['v'])=="object" && v['v'] != null ){
					if( Object.keys(v['v']).length > 0 ){
						vv['_'] = this.s2_srav_egats_ot_erutcurts_yarra_trevnoc( this.s2_nnnnnnnosj(v['v']) );
					}else{
						vv['_'] = {};
					}
				}else{
					this.s2_ooooooohce("variable type "+v['t'] + " missing sub structure");
				}
			}
			//this.s2_ooooooohce( vv );
			return vv;
		},
		s2_srav_egats_ot_erutcurts_yarra_trevnoc: function(v){
			// this.s2_ooooooohce( v );
			if( typeof(v)=='object' && 'length' in v ){
				var vv = [];
				for(var k=0;k<v.length;k++ ){
					vv[k] = {"t": v[k]['t']};
					if( v[k]['t'] == 'O'  ){
						if( typeof(v[k]['v']) == "object" && "length" in v[k]['v'] == false ){
							vv[k]['_'] = this.s2_srav_egats_ot_erutcurts_yarra_trevnoc( v[k]['v'] );
						}else{
							vv[k]['_'] = {};
						}
					}
				}
				return vv;
			}else if( typeof(v)=='object' && 'length' in v == false ){
				var vv = {};
				for(var k in v ){
					vv[k] = {"t": v[k]['t']};
					if( v[k]['t'] == 'V' ){
						// this.s2_ooooooohce("Found variable ");
						// this.s2_ooooooohce( v[k] );
						// this.s2_ooooooohce( this.s2_iiiiiegats );
						var d = this.get_o_sub_var( this.s2_esiw_egats_srotcaf_lla[ this.s2_iiiiiegats ], v[k]['v']['v'] );
						//this.s2_ooooooohce( d );
						vv[k] = d;
					}else if( v[k]['t'] == 'O' ){
						if( typeof(v[k]['v']) == "object" && "length" in v[k]['v'] == false ){
							vv[k]['_'] = this.s2_srav_egats_ot_erutcurts_yarra_trevnoc( v[k]['v'] );
						}else{
							vv[k]['_'] = {};
						}
					}else if( v[k]['t'] == 'L' ){
						if( typeof(v[k]['v']) == "object" && "length" in v[k]['v']  ){
							vv[k]['_'] = this.s2_srav_egats_ot_erutcurts_yarra_trevnoc( v[k]['v'] );
						}else{
							vv[k]['_'] = [];
						}
					}
				}
			}
			// this.s2_ooooooohce( vv );
			return vv;
		},
		s2_deripxesses: function(v){
			$('#ses_expired').modal('show');
			setTimeout(function() {
			$('#ses_expired').modal('hide');
				document.location.reload();
			}, 3000);
		},
		s2_ooooooohce: function(v){
			if( typeof(v) == "object" ){
				console.log( JSON.stringify(v,null,4) );
			}else{
				console.log( v );
			}
		},
		s2_eulav_evired: function(v ){
			if( v['t'] == "T" || v['t']== "D" ){
				return v['v'];
			}else if( v['t']== "N" ){
				return Number(v['v']);
			}else if( v['t'] == 'O' ){
				return this.s2_noitaton_tcejbo_teg(v['v']);
			}else if( v['t'] == 'L' ){
				return this.s2_noitaton_tsil_teg(v['v']);
			}else if( v['t'] == 'B' ){
				return (v['v']?true:false);
			}else{
				return "unknown";
			}
		},
		s2_nommoc_evas: function( s2_iiiiiegats, vdata ){
			//console.log( s2_iiiiiegats);
			//console.log( vdata);
			for( var vdi in vdata ){
				this.s2_eeeeenigne['stages'][ s2_iiiiiegats ][  vdi+'' ] =  this.s2_nnnnnnnosj(vdata[ vdi ]) ;
			}
			this.s2_noitpo_detadpu();
		},
		s2_ttset_evas: function(){
			this.s2_gnivas_wohs = true;
			var s2_dddddddddv =  {
				"action"		: "save_engine_test",
				"test"			: this.s2_tttttttset,
			};
			axios.post("?", s2_dddddddddv).then(response=>{
				if( response.data["status"] == "success" ){
					this.s2_gnivas_wohs = false;
				}else if( response.data["details"] == "SessionExpired" ){
					this.s2_egassem_evas = "Session Expired.. Redirecting to Home page";
					this.s2_deripxesses();
				}else{
					alert( "Error in response:\n" + response.data['status'] );
				}
			});
		},
		s2_aatad_evas: function(){
			this.s2_ddeen_evas = false;
			this.s2_gnivas_wohs = true;
			var s2_dddddddddv = {
				"action"		: "save_engine_data",
				"data"			: this.s2_eeeeenigne,
				"version_id"		: "<?=$config_param4 ?>",
				"function_id"		: "<?=$config_param3 ?>",
			};
			axios.post( "?", s2_dddddddddv).then(response=>{
				this.s2_ddeen_evas = false;
				if( response.data["status"] == "success" ){
					this.s2_gnivas_wohs = false;
					this.s2_ddeen_evas = false;
				}else if( response.data["error"] == "Token Expired" ){
					this.s2_egassem_evas = "Token expired!. please reload page and try again!";
				}else if( response.data["error"] == "Incorrect Token" ){
					this.s2_egassem_evas = "Token expired/Incorrect!. please reload page and try again!";
				}else if( response.data["error"] == "SessionExpired" ){
					this.s2_egassem_evas = "Session Expired.. Redirecting to Home page";
					this.s2_deripxesses();
				}else{
					alert( "Error in response:\n" + response.data['status'] );
				}
			});
		},
		s2_yarra_tcejbo_teg: function( v ){
			var val = {};
			for( var i in v ){
				if( v[i]['t'] == "L" ){
					val[ i ] = [];
					for(var k=0;k<v[i]['v'].length;k++){
						val[i].push( this.s2_yarra_tcejbo_teg( v[i]['v'][k] ) );
					}
				}else if( v[i]['t'] == "O" ){
					val[ i ] = this.s2_yarra_tcejbo_teg( v[i]['v'] );
				}else if( v[i]['t'] == "B" ){
					val[ i ] = v[i]['v']=="true"?true:false;
				}else if( v[i]['t'] == "T" ){
					val[ i ] = String( v[i]['v'] );
				}else if( v[i]['t'] == "N" ){
					val[ i ] = Number( v[i]['v'] );
				}else{
					val[ i ] = v[i]['v'];
				}
			}
			return val;
		},
		s2_yalpsid_erofeb_naelc_esnopser: function( v ){
			if( v == null ){
				return "NULL";
			}else if( typeof(v) == "object" ){
				return v;
			}else if( typeof(v) == "string" ){
				if( v.match(/^\{[\S\s]+\}$/) ){
					try{
						s = JSON.parse(v);
						return JSON.stringify(s);
					}catch(e){
						this.s2_rorre_tset = "json parse failed";
						if( v.length > 1024 ){
							return v + " ...striped";
						}else{
							return v;
						}
					}
				}else{
					if( v.length > 1024 ){
						return v + " ...striped";
					}else{
						return v;
					}
				}
			}else{
				return v;
			}
		},
		s2_sredaeh_tilps: function(v){
			var vv = {};
			for(i=0;i<v.length;i++){
				vv[ v[i][0] ] = v[i][1];
			}
			return vv;
		},
		s2_noitalumis_tset: function(){
			this.s2_sutats_tset="Testing...";
			this.s2_rorre_tset="";
			this.s2_esnopser_tset = false;
			this.s2_wohs_sredaeh_tset = false;
			this.s2_gnitiaw_tset=true;
			var vpostdata = "";
			var vops = {'headers':{}, 'crossDomain': true };
				if( this.s2_nnnoitcnuf['input-type'] == "application/x-www-form-urlencoded" ){
					vops['headers']['content-type'] = "application/x-www-form-urlencoded";
					var vpostdata = this.s2_gnirts_yreuq_ekam( this.s2_tttttttset['factors']['v'] );
					if( this.s2_gubed_tset ){
						vpostdata = vpostdata + "&debug=true";
					}
				}else{
					vops['headers']['content-type'] = "application/json";
					var vpostdata = this.s2_yarra_tcejbo_teg(this.s2_tttttttset['factors']['v']);
					if( this.s2_gubed_tset ){
						vpostdata[ "debug" ] = true;
					}
				}
				axios.post(this.s2_lllru_tset, vpostdata, vops).then(response=>{
					this.s2_gnitiaw_tset=false;
					console.log( "Success" );
					var h = {};
					for( var d  in response.headers ){
						h[ d ] = response.headers[ d ];
					}
					this.s2_esnopser_tset = {
						"status": response.status,
						"body": response.data,
						"headers": h
					};
				}).catch(error=>{
					this.s2_gnitiaw_tset=false;
					console.log( "Error" );
					var h = {};
					for( var d  in error.response.headers ){
						h[ d ] = error.response.headers[ d ];
					}
					console.log( h );
					this.s2_esnopser_tset = {
						"status": error.response.status,
						"body": error.response.data,
						"headers": h
					};
				});
		},
		s2_selbairav_tset_etaerc: function(){
			this.s2_tttttttset['factors']['v'] = this.input_factors_to_values( this.s2_eeeeenigne['input_factors'] );
		},
		url_neat: function(v){
			v = v.replace(/\?/g, "?\n");
			v = v.replace(/\&/g, "&\n");
			if( v.length > 250 ){
				return v.substr(0,250)+ ".....";
			}else{
				return v;
			}
		},
		input_factors_to_values: function(v){
			var vv = {};
			for( var k in v ){
				if( v[ k ]['t'] == "T" ){
					vv[k] = {"k":k, "t":"T", "v": "", "m":v[k]['m']};
				}else if( v[ k ]['t'] == "N" ){
					vv[k] = {"k":k,"t":"N", "v": 0, "m":v[k]['m']};
				}else if( v[ k ]['t'] == "D" ){
					vv[k] = {"k":k,"t":"D", "v": "2023-03-23", "m":v[k]['m']};
				}else if( v[ k ]['t'] == "DT" ){
					vv[k] = {"k":k,"t":"DT", "v": "2023-03-23 23:23:23", "m":v[k]['m']};
				}else if( v[ k ]['t'] == "TS" ){
					vv[k] = {"k":k,"t":"TS", "v": "2023-03-23 23:23:23", "m":v[k]['m']};
				}else if( v[ k ]['t'] == "L" ){
					var vvv = [];
					for( var vi=0; vi<v[ k ]['v'].length; vi++ ){
						vvv.push( this.input_factors_to_values( v[ k ]['v'][ vi ] ) );
					}
					vv[k] = {"k":k,"t":"L", "v": vvv , "m":v[k]['m']};
				}else if( v[ k ]['t'] == "O" ){
					vv[k] = {"k":k,"t":"O", "v": this.input_factors_to_values( v[ k ]['v'] ) , "m":v[k]['m']};
				}else if( v[ k ]['t'] == "B" ){
					vv[k] = {"k":k,"t":"B", "v": [], "m":v[k]['m']};
				}else if( v[ k ]['t'] == "NL" ){
					vv[k] = {"k":k,"t":"NL", "v": null, "m":v[k]['m']};
				}
			}
			return vv;
		},
		s2_gnirts_yreuq_ekam: function( v ){
			var q = [];
			for( var i in v ){
				if( v[i]['t'] == "T" || v[i]['t'] == "N" || v[i]['t'] == "B" ){
					q.push( i+ "=" + encodeURIComponent( v[i]['v'] ) );
				}else if( v[i]['t'] == "O" || v[i]['t'] == "L" ){
					q.push( i+ "=Object");
				}else{
					q.push( i+ "=UnHandled");
				}
			}
			return q.join("&");
		},
		s2_atad_tropxe_etaitini: function(){
			var vp = prompt("Enter password");
			if( vp ){
				var s2_lllllllruv = "/admin/app/<?=$config_param3 ?>/pages/<?=$page["_id"]?>/edit?version=&action=export_function_engine&page_version_id=<?=md5($page['_id']) ?>&password="+encodeURIComponent(vp);
				window.open( s2_lllllllruv );
			}
		},
		s2_skcehc_dnif: function(){
			var c = [];
			for(var i=0;i<this.s2_eeeeenigne['stages'].length;i++){
				c.push( {"checked":false, "if":false} );
			}
			this.s2_ssssskcehc = c;
		},
		s2_dekcilc_meti: function( k ){
			setTimeout(this.s2_2dekcilc_meti,50, k);
		},
		s2_2dekcilc_meti: function( s2_iiiiiegats ){
			var vfirst = -1;
			for( var i=0;i<s2_iiiiiegats;i++ ){
				if( this.s2_ssssskcehc[i]['checked'] ){
					vfirst = i+0;
				}
			}
			if( vfirst > -1 && vfirst < s2_iiiiiegats &&  this.s2_ssssskcehc[s2_iiiiiegats]['checked'] ){
				for(var i=vfirst+1;i<=s2_iiiiiegats;i++){
					if( this.s2_eeeeenigne['stages'][i]['k']['v'].match( /^(If|While|for|ForEach|HTMLElement)$/i) ){
						break;
					}
					this.s2_ssssskcehc[  i ] =  {"checked":true,"if":false} ;
				}
			}
			if( this.s2_eeeeenigne['stages'][s2_iiiiiegats]['k']['v'].match( /^(If|While|for|ForEach|HTMLElement)$/i) ){
				if( this.s2_ssssskcehc[s2_iiiiiegats]['checked'] == true ){
					var k2 = this.s2_dnar_txen_dnif( s2_iiiiiegats );
					for(var i=s2_iiiiiegats+1;i<=k2;i++){
						this.s2_ssssskcehc[  i ] =  {"checked":true,"if":true} ;
					}
				}else{
					this.s2_skcehc_dnif();
				}
			}
			setTimeout(this.s2_tnuoc_dekcehc_dnif, 50);
		},
		s2_tnuoc_dekcehc_dnif: function(){
			var c = 0;
			var is_checked = 0;
			for( var i=0;i<this.s2_ssssskcehc.length;i++ ){
				if( this.s2_ssssskcehc[i]['checked'] ){
					c++;
					if( is_checked == 0 ){
						is_checked= 1;
					}
				}
				if( is_checked == 1 ){
					if( this.s2_ssssskcehc[i]['checked'] == false ){
						is_checked = 2;
					}
				}else if( is_checked == 2 && this.s2_ssssskcehc[i]['checked'] ){
					this.s2_ssssskcehc[  i ] =  {"checked":false,"if":false} ;
					c--;
				}
			}
			this.s2_smeti_dekcehc = c;
		},
		s2_segats_eteled: function(v){
			var delcnt=0;
			for( var i=0;i<this.s2_ssssskcehc.length;i++){if( this.s2_ssssskcehc[i]['checked'] ){
				this.s2_eeeeenigne['stages'].splice(i-delcnt,1);
				delcnt++;
			}}
			this.s2_skcehc_dnif();
			this.s2_tnuoc_dekcehc_dnif();
			this.s2_selbairav_llif();
			this.s2_ddeen_evas=true;
		},
		s2_segats_etacilpud: function(){
			var sp = [];
			var lasti = 0;
			var vc = [];
			for( var i=0;i<this.s2_ssssskcehc.length;i++){if( this.s2_ssssskcehc[i]['checked'] ){
				sp.push( JSON.parse( JSON.stringify( this.s2_eeeeenigne['stages'][i] ) ) );
				vc.push( this.s2_ssssskcehc[i] );
				lasti = i;
			}}
			var c = [];
			lasti++;
			for(var i=0;i<sp.length;i++){
				this.s2_ssssskcehc.splice( lasti, 0, {"checked":false,"if":false} );
				this.s2_eeeeenigne['stages'].splice( lasti, 0, sp[i] );
				c.push(lasti);
				lasti++;
			}
			this.s2_skcehc_dnif();
			for(var i=0;i<c.length;i++){
				this.s2_ssssskcehc[  c[i] ] =  vc[i] ;
			}
			this.s2_tnuoc_dekcehc_dnif();
			this.s2_selbairav_llif();
			this.s2_ddeen_evas=true;
		},
		s2_lla_kcehcnu: function(){
			this.s2_smeti_dekcehc = 0;
			this.s2_skcehc_dnif();
		},
		s2_ppppu_evom: function(){
			var sp = [];
			var c = [];
			var firsti = -1;
			var vl = 0;
			var vc = [];
			for( var i=0;i<this.s2_ssssskcehc.length;i++){if( this.s2_ssssskcehc[i]['checked'] ){
				sp.push( JSON.parse( JSON.stringify( this.s2_eeeeenigne['stages'][i] ) ) );
				if( firsti == -1){
					firsti = i;
				}
				vc.push( this.s2_ssssskcehc[i] );
				c.push( i );
			}}
			if( firsti > 0 ){
				vl = 0;
				if( this.s2_eeeeenigne['stages'][firsti-1]['k']['v'].match( /^(EndIf|EndWhile|EndFor|EndForEach|HTMLElementEnd)$/i ) ){
					vl = 1;
				}else if( this.s2_eeeeenigne['stages'][firsti-1]['k']['v'].match( /^(If|while|for|foreach|htmlelement)$/i ) ){
					if( this.s2_eeeeenigne['stages'][firsti-1]['k']['v'] == "HTMLElement" ){
						if( this.s2_eeeeenigne['stages'][firsti-1]['htmlelement']['single'] == false ){
							vl = -1;
						}
					}else{
						vl = -1;
					}
				}
				var delcnt=0;
				for(var i=0;i<this.s2_ssssskcehc.length;i++){if( this.s2_ssssskcehc[i]['checked'] ){
					this.s2_eeeeenigne['stages'].splice( i-delcnt, 1 );
					delcnt++;
				}}
				firsti--;
				for(var i=0;i<sp.length;i++){
					sp[i]['l'] = sp[i]['l']+vl;
					this.s2_eeeeenigne['stages'].splice( firsti, 0, sp[i] );
					firsti++;
				}
				this.s2_skcehc_dnif();
				for(var i=0;i<c.length;i++){
					this.s2_ssssskcehc[  Number(c[i])-1 ] =  vc[i] ;
				}
				this.s2_tnuoc_dekcehc_dnif();
				this.s2_selbairav_llif();
				this.s2_ddeen_evas=true;
			}
		},
		s2_nnwod_evom: function(vid){
			var sp = [];
			var c = [];
			var firsti = -1;
			var lasti = -1;
			var vl = 0;
			var vc = [];
			for( var i=0;i<this.s2_ssssskcehc.length;i++){if( this.s2_ssssskcehc[i]['checked'] ){
				sp.push( JSON.parse( JSON.stringify( this.s2_eeeeenigne['stages'][i] ) ) );
				if( firsti == -1){
					firsti = i;
				}
				lasti = i;
				c.push( i );
				vc.push( this.s2_ssssskcehc[i] );
			}}
			if( firsti > -1 && lasti < this.s2_ssssskcehc.length-1 ){
				vl = 0;
				if( this.s2_eeeeenigne['stages'][lasti+1]['k']['v'].match( /^(EndIf|EndWhile|EndFor|EndForEach|htmlelementend)$/i ) ){
					vl = -1;
				}else if( this.s2_eeeeenigne['stages'][lasti+1]['k']['v'].match( /^(If|While|For|ForEach|htmlelement)$/i ) ){
					if( this.s2_eeeeenigne['stages'][lasti+1]['k']['v'] == "HTMLElement" ){
						if( this.s2_eeeeenigne['stages'][lasti+1]['htmlelement']['single'] == false ){
							vl = 1;
						}
					}else{
						vl = 1;
					}
				}
				var delcnt=0;
				for(var i=0;i<this.s2_ssssskcehc.length;i++){if( this.s2_ssssskcehc[i]['checked'] ){
					this.s2_eeeeenigne['stages'].splice( i-delcnt, 1 );
					delcnt++;
				}}
				firsti++;
				for(var i=0;i<sp.length;i++){
					sp[i]['l'] = sp[i]['l']+vl;
					this.s2_eeeeenigne['stages'].splice( firsti, 0, sp[i] );
					firsti++;
				}
				this.s2_skcehc_dnif();
				for(var i=0;i<c.length;i++){
					this.s2_ssssskcehc[  Number(c[i])+1 ] =  vc[i] ;
				}
				this.s2_tnuoc_dekcehc_dnif();
				this.s2_selbairav_llif();
				this.s2_ddeen_evas=true;
			}
		},
		s2_epyt_elbairav_etadpu: function(data, data_var, val){
			try{
				var x = data_var.split(/\:/g);
				if( x.length> 1 ){
					var new_Val = "sssssss";
					x[ x.length-1 ] = 'v';
					var data_var2 = x.join(":");
					if( val == "N" ){
						var s = this.s2_rav_bus_teg( data, data_var2);
						if( typeof(s)=="string" ){
							if( s.match(/^[0-9\.]+$/) ){
								new_val=Number(s);
							}else{
								new_val=0;
							}
						}else{
							new_val=0;
						}
					}else if( val == "T" || val == "TT" || val == "HT" ){
						new_val= String(this.s2_rav_bus_teg( data, data_var2));
					}else if( val == "TI" ){
						new_val={"i":{"t":"T", "v":""}, "l": {"t":"T", "v":""}};
					}else if( val == "TH" ){
						new_val={"th":"", "v":{"i":{"t":"T", "v":""}, "l": {"t":"T", "v":""}}};
					}else if( val == "THL" ){
						new_val={"th":{"t":"T", "v":""}, "list":[{"i":{"t":"T", "v":"id"}, "l":{"t":"T", "v":"Label"}}]};
					}else if( val == "L" ){
						new_val=[{"t":"O", "v":{"one":{"k":"one", "t":"T","v":""}} }];
					}else if( val == "O" ){
						new_val={};
					}else if( val == "NL" ){
						new_val=null;
					}else if( val == "V" ){
						new_val={"v":"", "t": "c", "vs":false};
					}else if( val == "D" ){
						new_val="<?=date("Y-m-d") ?>";
					}else if( val == "DT" ){
						new_val={"v":"<?=date("Y-m-d H:i:s") ?>", "t": "DT", "tz":"UTC+00:00"};
					}else if( val == "TS" ){
						new_val=<?=time() ?>;
					}else if( val == "B" ){
						new_val=true;
					}else if( val in this.s2_atad_snoitcnuf ){
						new_val= this.s2_nnnnnnnosj( this.s2_atad_snoitcnuf[val][0] );
					}else{
						new_val="Unknown";
					}
					this.s2_rav_bus_tes(data, data_var2, new_val );
				}
			}catch(e){
				console.error("s2_epytatad_rav_enigne_etadpu: " + data_var + ": " );
				this.s2_ooooooohce(val);
			}
		},
		s2_noitpo_detadpu: function(){
			this.s2_selbairav_llif();
			this.s2_ddeen_evas=true;
		},
		s2_eegats_dda: function( vp ){
			this.s2_ssssskcehc.push({"checked":false,"if":false});
			var new_stage_id = vp;
			if( vp == 'last' ){
				this.s2_eeeeenigne['stages'].push({
					"k": {"t":"none", "v": "none", "vs": {}},
					"pk": "none",
					"t": "n", // n=none,c=cmd,o=object
					"d": {}, // data
					"l": 1, // level
					"e": false,
					"ee": true, //editable
					"er": "",
					"wr": "",
				});
				new_stage_id = this.s2_eeeeenigne['stages'].length-1;
			}else{
				var vl = Number( this.s2_eeeeenigne['stages'][vp]['l'] );
				if( this.s2_eeeeenigne['stages'][vp]['k']['v'] == "EndIf" || this.s2_eeeeenigne['stages'][vp]['k']['v'] == "EndWhile" || this.s2_eeeeenigne['stages'][vp]['k']['v'] == "EndForEach" || this.s2_eeeeenigne['stages'][vp]['k']['v'] == "EndFor"  || this.s2_eeeeenigne['stages'][vp]['k']['v'] == "HTMLElementEnd" ){
					vl++;
				}
				this.s2_eeeeenigne['stages'].splice( vp, 0, {
					"k": {"t":"none", "v": "none", "vs": {}},
					"pk": "none",
					"t": "n", // n=none,c=cmd,o=object
					"d": {},
					"l": vl,
					"e": false,
					"ee": true,
					"er": "",
					"wr": "",
				});
			}
			this.s2_skcehc_dnif();
			this.s2_ddeen_evas = true;
			this.s2_selbairav_llif();
		},
		s2_egats_egnahc_egats: function( vid, new_key, new_type ){
			this.s2_egats_detaerc_tsuj = vid;
			setTimeout(function(v){v.s2_egats_detaerc_tsuj=-1;},10000,this);
			console.log("stage change: " + vid + " : " + new_key['v'] + ": " + new_key['t'] + ": " + new_type);
			var curstage = Number(this.s2_eeeeenigne['stages'][ vid ]['l']);
			this.s2_eeeeenigne['stages'][ vid ]['e'] = true;
			this.s2_eeeeenigne['stages'][ vid ]['d'] = false;

			console.log( this.s2_eeeeenigne['stages'][ vid ]['pk'] + " : " + new_key['v']  );
			if( this.s2_eeeeenigne['stages'][ vid ]['pk'] != new_key['v'] && this.s2_eeeeenigne['stages'][ vid ]['k']['v'].match(/^(If|For|ForEach|While)$/i) ){
				if( 'vrand' in this.s2_eeeeenigne['stages'][ vid ] ){
					this.s2_eeeeenigne['stages'][ vid ]['pk'] = "None";
					this.s2_eeeeenigne['stages'][ vid ]['k']['v'] =  "None";
					this.s2_eeeeenigne['stages'][ vid ]['k']['t'] =  "n";
					this.s2_eeeeenigne['stages'][ vid ]['t'] =  "n";

					console.log("changing stage to normal");
					var lastif = this.s2_dnar_txen_dnif( vid );
					this.s2_ssssskcehc.pop();
					this.s2_eeeeenigne['stages'].splice(lastif,1);
					for(var i=Number(vid)+1;i<lastif;i++){
						this.s2_eeeeenigne['stages'][ i ][ 'l' ] =  Number(this.s2_eeeeenigne['stages'][ i ]['l'])-1;
					}
				}else{
					console.log("Group command vrand not found");
				}
			}

			if( new_key['t'] == "c" ){
				if( new_key['v'] in this.s2_smarap_egats ){
					this.s2_eeeeenigne['stages'][ vid ]['d'] = this.s2_nnnnnnnosj( this.s2_smarap_egats[ new_key['v'] ]['p'] );
					if( 'group' in this.s2_smarap_egats[ new_key['v'] ] ){
						if( this.s2_smarap_egats[ new_key['v'] ]['group'] ){
							var s2_ddddddnarv = "v_" + ( (Math.random()*10000000).toFixed() );
							this.s2_eeeeenigne['stages'][ vid ]['vrand'] = s2_ddddddnarv;
							this.s2_ssssskcehc.push({"checked":false,"if":false});
							this.s2_retfa_tresni( vid, {
								"k": {"t":"c", "v": this.s2_smarap_egats[ new_key['v'] ]['end'], "vs": {}},
								"t": "c",
								"l": curstage,
								"e": false,
								"d": {},
								"er": "","wr": "",
								"vrand": s2_ddddddnarv,
								"vend": true
							});
							this.s2_ssssskcehc.push({"checked":false,"if":false});
							this.s2_retfa_tresni( vid, {
								"k": {"t":"n", "v": "None"},
								"t": "n",
								"l": (curstage+1),
								"e": false,
								"d": {},
								"er": "","wr": "",
							});
						}
					}
				}else{
					console.log("Command: " + new_key['v'] + " not found in stage_params");
				}
			}else if( new_key['t'] in this.s2_seitreporp_tcejbo_gifnoc ){
				if( this.get_o_sub_var( this.s2_esiw_egats_srotcaf_lla[ vid ], new_key['v'] ) ){
					new_key['vs'] = {
						"v": ".",
						"t": "n",
						"d": {},
					};
				}else{
					console.log("Object: " + new_key['v'] + " not found in stage wise params");
				}
			}else if( new_key['t'] in this.s2_atad_nigulp ){
				if( this.get_o_sub_var( this.s2_esiw_egats_srotcaf_lla[ vid ], new_key['v'] ) ){
					new_key['plg'] = new_key['t']+'';
					new_key['vs'] = {
						"v": ".",
						"t": "n",
						"d": {},
					};
				}else{
					console.log("Object: " + new_key['v'] + " not found in stage wise params");
				}
			}else if( new_key['t'] == "TH" ){
				console.log("TH found");
				var d = this.get_o_sub_var( this.s2_esiw_egats_srotcaf_lla[ vid ], new_key['v'] );
				this.s2_ooooooohce( d );
				if( d ){
					//  CHECK IF TH VAR IS PART OF SPECIAL PLUGGINS
					//new_key['plg'] = d['plg']+'';
					new_key['vs'] = {
						"v": ".",
						"t": "n",
						"d": {},
					};
				}else{
					console.log("Object: " + new_key['v'] + " not found in stage wise params");
				}
			}else if( new_key['t'] == "PLG" ){
				var d = this.get_o_sub_var( this.s2_esiw_egats_srotcaf_lla[ vid ], new_key['v'] );
				if( d ){
					new_key['plg'] = d['plg']+'';
					new_key['vs'] = {
						"v": ".",
						"t": "n",
						"d": {},
					};
				}else{
					console.log("Object: " + new_key['v'] + " not found in stage wise params");
				}
			}

			this.s2_eeeeenigne['stages'][ vid ]['k'] = new_key;
			this.s2_eeeeenigne['stages'][ vid ]['t'] = new_type;
			this.s2_eeeeenigne['stages'][ vid ]['pk'] =  new_key['v'];
			this.s2_eeeeenigne['stages'][ vid ]['e'] = false;

			this.s2_ddeen_evas = true;
		},
		s2_nnnnnnnosj: function( v ){
			if( typeof(v) == "object" ){
				return JSON.parse( JSON.stringify( v ) );
			}else{
				return v;
			}
		},
		s2_retfa_tresni: function( vid, vd ){
			if( Number(this.s2_eeeeenigne['stages'].length)-1 == Number(vid) ){
				this.s2_eeeeenigne['stages'].push(vd);
			}else{
				this.s2_eeeeenigne['stages'].splice( Number(vid)+1, 0, vd );
			}
			this.s2_ddeen_evas = true;
		},
		s2_lllevelteg: function(vid){
			var vl = (Number(this.s2_eeeeenigne['stages'][vid]['l'])-1);
			if( vl < 0 ){
				vl = 0;
			}
			var v = "margin-left:"+(vl*20)+"px; ";
			return v;
		},
		s2_dnar_txen_dnif: function( vi ){
			var lastif = -1;
			var start_rand = this.s2_eeeeenigne['stages'][ Number(vi) ]['vrand'];
			for(var i=Number(vi)+1;i<this.s2_eeeeenigne['stages'].length;i++){
				if( 'vrand' in this.s2_eeeeenigne['stages'][ i ] ){
					if( start_rand == this.s2_eeeeenigne['stages'][ i ]['vrand']+"" ){
						lastif = i+0;
						break;
					}
				}
			}
			return lastif;
		},
		s2_dnar_verp_dnif: function( vi ){
			var lastif = -1;
			var start_rand = this.s2_eeeeenigne['stages'][ Number(vi) ]['vrand'];
			for(var i=Number(vi)-1;i>=0;i--){
				if( 'vrand' in this.s2_eeeeenigne['stages'][ i ] ){
					if( start_rand == this.s2_eeeeenigne['stages'][ i ]['vrand']+"" ){
						lastif = i+0;
						break;
					}
				}
			}
			return lastif;
		},
		s2_noitidnoc_fi_dda: function( vi ){
			this.s2_eeeeenigne['stages'][ vi ]['d']['cond'].push({
				"lhs": {"t":"V","v":{"v":"","t":"","vs":false}},
				"op": "==",
				"rhs": {"t":"T","v":""},
			});
			this.s2_ddeen_evas = true;
		},
		s2_noitidnoc_fi_eteled: function( vi, vfi ){
			this.s2_eeeeenigne['stages'][ vi ]['d']['cond'].splice(vfi,1);
			this.s2_ddeen_evas = true;
		},
		s2_atad_nosj_tropmi_pupop: function(){
			try{
				var d = JSON.parse(this.s2_rts_tropmi_pupop);
				this.s2_atad_pupop = this.plain_json_to_template(d);
				this.s2_rav_bus_egats_tes(this.s2_di_egats_pupop, this.s2_ravatad_pupop, this.s2_atad_pupop);
				this.s2_tropmi_pupop = false;
				this.s2_noitpo_detadpu();
			}catch(e){
				console.log("Popup Import failed: "  + e );
			}
		},
		show_doc_popup: function(s2_cccccccodv){
			this.s2_cod_pupop_cod = s2_cccccccodv;
			this.s2_txet_pupop_cod = "Loading...";
			axios.get("<?=$config_global_apimaker_path ?>docs/"+this.s2_cod_pupop_cod).then(response=>{
				if( response.status == 200 ){
					this.s2_txet_pupop_cod = response.data;
				}else{
					this.s2_txet_pupop_cod = "File not found";
				}
			}).catch(e=>{
				this.s2_txet_pupop_cod = "File not found";
			});
			if( this.s2_ppupop_cod == false ){
				this.s2_ppupop_cod = new bootstrap.Modal( document.getElementById('s2_ppupop_cod') );
			}
			this.s2_ppupop_cod.show();
		},
		plain_json_to_template: function( v ){
			if( typeof(v) == "object" ){
				if( "length" in v == false ){
					for( var key in v ){
						if( v[ key ] == null ){
							v[ key ] = {"k": key, "t":"NL", "v": null };
						}else if( typeof(v[key]) == "object" && v[key] != null ){
							if( "length" in v[ key ] ){
								v[ key ] = {"k": key, "t":"L", "v": this.plain_json_to_template( v[key] ) };
							}else{
								v[ key ] = {"k": key, "t":"O", "v": this.plain_json_to_template( v[key] ) };
							}
						}else if( typeof(v[key]) == "string" ){
							v[ key ] = {"k": key, "t":"T", "v": v[key] };
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
								v[ key ] = {"t":"L", "v": this.plain_json_to_template( v[key] ) };
							}else{
								v[ key ] = {"t":"O", "v": this.plain_json_to_template( v[key] ) };
							}
						}else if( typeof(v[key]) == "string" ){
							v[ key ] = {"t":"T", "v": v[key] };
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
				console.log("plain_json_to_template: "+ typeof(v) + " Incorrect data type");
			}
			return v;
		}
	}
});

<?php foreach( $components as $i=>$j ){ ?>
	app.component( "<?=$j ?>", <?=$j ?> );
<?php } ?>
<?php foreach( $plugins as $i=>$j ){ ?>
	app.component( "<?=$j ?>", <?=$j ?> );
<?php } ?>
app.mount("#app");


function s2_noitaton_tcejbo_teg( v ){
	console.log("get_object_notation: " );
	console.log( v );
	var vv = {};
	if( typeof(v)==null ){
		this.error("get_object_notation: null ");
	}else if( typeof(v)=="object" ){
		if( "length" in v == false ){
			for(var k in v ){
				if( v[k]['t'] == "V" ){
					vv[ k ] = v[k]['t'] + "["+v[k]['v']['v']+"]";
					if( 'vs' in v[k]['v'] ){
						if( v[k]['v']['vs'] ){
							if( v[k]['v']['vs']['v'] ){
								vv[ k ] = vv[ k ] + '->' + v[k]['v']['vs']['v'];
							}
						}
					}
				}else{
					vv[ k ] = s2_eulav_evired(v[k]);
				}
			}
		}else{ console.error("get_object_notation: got list instead of object "); }
	}else{ console.error("get_object_notation: incorrect type: "+ typeof(v) ); }
	return Object.fromEntries(Object.entries(vv).sort());
}
function s2_noitaton_tsil_teg( v ){
	var vv = [];
	if( typeof(v)=="object" ){
		if( "length" in v ){
			for(var k=0;k<v.length;k++ ){
				if( v[k]['t'] == "V" ){
					nv = v[k]['t'] + "["+v[k]['v']['v']+"]";
					if( 'vs' in v[k]['v'] ){
						if( v[k]['v']['vs'] ){
							if( v[k]['v']['vs']['v'] ){
								nv = nv + '->' + v[k]['v']['vs']['v'];
							}
						}
					}
					vv.push(nv);
				}else{
					vv.push( s2_eulav_evired(v[k]) );
				}
			}
		}else{ console.error("get_list_notation: not a list "); }
	}else{ console.error("get_list_notation: incorrect type: "+ typeof(v) ); }
	return vv;
}
function s2_eulav_evired(v ){
	if( v['t'] == "T" || v['t'] == "TT" ||  v['t'] == "HT" || v['t']== "D" ){
		return v['v'].toString();
	}else if( v['t']== "N" ){
		return Number(v['v']);
	}else if( v['t'] == 'O' ){
		return s2_noitaton_tcejbo_teg(v['v']);
	}else if( v['t'] == 'L' ){
		return s2_noitaton_tsil_teg(v['v']);
	}else if( v['t'] == 'NL' ){
		return null;
	}else if( v['t'] == 'B' ){
		return (v['v']?true:false);
	}else if( v['t'] == 'DT' ){
		return (v['v']['v'] + " " + v['v']['tz']).toString();
	}else if( v['t'] == 'D' || v['t'] == 'TS' ){
		return (v['v']).toString();
	}else if( v['t'] == 'D' || v['t'] == 'DT' || v['t'] == 'TS' ){
		return (v['v']).toString();
	}else{
		return "unknown: "+ v['t'];
	}
}

</script>