var editor_component = {
	props:["editor_div_id", "editor_wrapper_div_id"],
	data: function(){
		return {
			target_editor_id__: "editor_div",
			target_wrapper_id__: "editor_block_a",
			graph_search_api_key__: "",
			graph_search_api__: "",
			css_template__: '',
			enabled__: true,
			insert_tag__: true,
			clipboards__: [],
			contextmenu__: false,
			contextmenu_style__: "display:none;",
			contextmenu_type__: "context",
			contextmenu_type_name__: "context",
			contextmenu_submenu__: false,
			contextmenu_submenu_style__: "visibility:hidden;",
			contextmenu_submenu_type__: "context",
			contextsidemenu__: false,
			contextsidemenu_style__: "visibility:hidden;",
			contextsidemenu_type__: "",
			link_search_bar__: false,
			link_search_bar_style__: "display:none;",

			dragger__: false,dragger_x__: -1,dragger_y__: -1,dragger_w__: -1,dragger_h__: -1,dragger_t__: -1,dragger_l__: -1,
			dragger_style__: "visibility:hidden;", 
			dragger_img__: "",

			text_colors__: ["red", "darkRed", "pink", "deepPink", "tomato", "orange", "gold", "yellow", "purple", "indigo", "green", "teal", "cyan", "royalBlue", "blue", "brown", "maroon", "white", "beige", "silver", "gray", "black"],
			clientY__: -1, clientX__: -1,
			relative_x__: -1,relative_y__: -1,relative_w__: -1,relative_h__: -1,
			selection_start__: false,
			sections_selected__: false,
			sections_copied__: [],
			sections_list__: [],
			sections_is_all_lis__: false,
			focused_bounds_style__: "visibility:hidden;",

			table_cell__: false,
			table_cell_style__: "",
			table_cells_clipboard__: false,

			side_settings_el__: false,
			side_settings_type__: "",
			side_settings_style__: "visibility:hidden;",
			side_settings_style2__: "visibility:hidden;",
			side_settings_pos__: "",

			drop_target_el__: false,
			drop_target_type__: "",
			drop_target_style__: "visibility:hidden;",
			drop_target_style2__: "",
			drop_target_pos__: "",

			config_tags__: {
				"P": "Paragraph",
				"H1": "Heading 1",
				"H2": "Heading 2",
				"H3": "Heading 3",
				"H4": "Heading 4",
				"UL": "Bullet List",
				"OL": "Numbered list",
				"DefList": "Definition List",
				"BlockQuote": "Quote",
				"CallOut": "CallOut",
				"PRE": "Code Snippet",
				"Grid": "Grid",
				"Table": "Table",
				"IMG": "Image",
				"Figure": "Figure",
				"Video": "Video",
				"Audio": "Audio",
			},
			table_settings__: {
				"border": "none", //border a,b,c
				"striped": "none", //striped 0,1
				"spacing": "none", //padding 1,2,3,4
				"hover": "none", //hover
				"theme": "none",
				"width": "none", // auto,full
				"header": "none",
				"footer": "none",
				"colheader": "none",
				"mheight": "none",
				"overflow": "auto",
			},
			ul_type__: "disc",
			ul_types__: {
				"list-style-disc": "list-style-disc",
				"list-style-square": "list-style-square",
				"list-style-circle": "list-style-circle",
				"list-style-decimal": "list-style-decimal",
				"list-style-decimal-leading": "list-style-decimal-leading",
				"list-style-lower-alpha": "list-style-lower-alpha",
				"list-style-lower-greek": "list-style-lower-greek",
				"list-style-lower-latin": "list-style-lower-latin",
				"list-style-lower-roman": "list-style-lower-roman",
				"list-style-upper-alpha": "list-style-upper-alpha",
				"list-style-upper-greek": "list-style-upper-greek",
				"list-style-upper-latin": "list-style-upper-latin",
				"list-style-upper-roman": "list-style-upper-roman",
			},
			pre_edit_popup__: false,
			pre_text__: "",
			pre_style__: "",

			anchor_at_range__: false,
			anchor_href__: "",
			anchor_graph__: {"t": "GT", "l": "", "i":""},
			anchor_text__: "",
			anchor_type__: "url",
			anchor_graph_search_window__: false,
			anchor_graph_search_window_style__: "display:none",
			anchor_graph_search_key__: "",
			anchor_graph_search_msg__: "",
			anchor_graph_search_err__: "",
			anchor_graph_search_keys__: {},
			anchor_graph_keywords_basic__: [],
			anchor_graph_keywords__: [],
			anchor_graph_keywords_filtered__: [],

			td_sel_start__: false,
			td_sel_start_tr__: -1,
			td_sel_start_td__: -1,
			td_sel_end_tr__: -1,
			td_sel_end_td__: -1,
			td_sel_cells__: [],
			td_sel_cnt__: 0,

			paste_shift__: false,

			focused__: false,
			focused_selection__: false,
			focused_className__: "",
			focused_styles__: {},
			focused_attributes__: {},
			focused_type__: "",
			focused_block_type__: "",
			focused_block__: false,
			focused_table__: false,
			focused_anchor__: false,
			focused_td__: false,
			focused_tr__: false,
			focused_li__: false,
			focused_ul__: false,
			focused_img__: false,
			focused_tree__: [],

			gt__: document.getElementById.bind(document),
			ce__: document.createElement.bind(document),
		};
	},
	mounted: function(){
		if( typeof(this.$root.graph_key) != "undefined" ){
			this.graph_search_api_key__ = this.$root.graph_key+'';
		}
		if( typeof(this.$root.graph_search_api) != "undefined" ){
			this.graph_search_api__ = this.$root.graph_search_api+'';
		}
		this.target_editor_id__= this.editor_div_id+'';
		this.target_wrapper_id__= this.editor_wrapper_div_id+'';
		console.log("Editor Initialized");
		if( editor_component['css_template__'] ){
			this.css_template__ = editor_component['css_template__'];
			if( this.gt__("editor_css_style_tag__") == null ){
				var vl = this.ce__("style");
				vl.innerText = this.css_template__;
				document.body.appendChild(vl);
			}
		}
		document.addEventListener("mousemove", this.event_mousemove__);
		document.addEventListener("scroll", this.window_scroll__);
		setTimeout(this.initialize_events__,500);
		setTimeout(this.initialize_tables__,500);
	},
	methods: {
		show_anchor_graph_search_window__: function(e){
			this.anchor_graph_search_set_style__(e);
		},
		anchor_graph_search_keyup__: function(){
			setTimeout( this.anchor_graph_search_filter__, 100 );
			if( this.anchor_graph_search_key__ != '' ){
				setTimeout( this.anchor_graph_keyword_load__, 200 );
			}
		},
		anchor_graph_search_set_style__: function(){
			var sy = window.scrollY; //Number(scrollY);
			var sx = window.scrollX; //Number(scrollX);
			var x = Number(this.clientX__);
			var y = Number(this.clientY__);
			var xw = window.innerWidth;
			var xh = window.innerHeight;
			if( (y+250) > xh ){
				y  = xh-20-250;
			}
			this.anchor_graph_search_window_style__ = "top:"+(y+sy-50)+"px;left:"+(x+sx+20)+"px;min-width:150px;";
			this.anchor_graph_search_window__ = true;
		},
		anchor_graph_keyword_load__: function(){
			if( this.anchor_graph_search_key__ in this.anchor_graph_search_keys__ ){
				return ;
			}
			var k  = this.anchor_graph_search_key__.substr(0,this.anchor_graph_search_key__.length-1);
			while( k.length > 1 ){
				if( k in this.anchor_graph_search_keys__ ){
					if( this.anchor_graph_search_keys__[ k ] < 100 ){
						this.echo__("cancel search as " + k + " results are below 100: " + this.anchor_graph_search_keys__[ k ] );
						return;
					}
				}
				var k  = k.substr(0,k.length-1);
			}
			{
				this.anchor_graph_search_msg__ = "Loading...";
				this.anchor_graph_search_err__ = "";
				var cond = {
					"action": "keywordSearch",
					"keyword": this.anchor_graph_search_key__+''
				};
				axios.post(this.graph_search_api__, cond, {"headers":{"Access-Key":this.graph_search_api_key__} }).then(response=>{
					this.anchor_graph_search_msg__ = "";
					if( response.status == 200 ){
						if( typeof(response.data) == "object" ){
							if( 'status' in response.data ){
								if( response.data['status'] == "success" ){
									if( response.data['things'] == null ){
										alert("Error context list");
									}else if( typeof(response.data['things']) == "object" ){
											var v = response.data['things'];
											var v2 = JSON.parse(JSON.stringify(this.anchor_graph_keywords__));
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
											this.anchor_graph_keywords__ = v2;
											if( this.anchor_graph_search_key__ == "" && this.anchor_graph_keywords_basic__.length == 0 ){
												this.anchor_graph_keywords_basic__ = v2;
											}
											this.anchor_graph_search_keys__[ response.data['keyword'] ] = v.length;
											setTimeout(this.anchor_graph_search_filter__, 100);
									}
								}else{
									this.anchor_graph_search_err__ = "Token Error: " + response.data['data'];
								}
							}else{
								this.anchor_graph_search_err__ = "Incorrect response";
							}
						}else{
							this.anchor_graph_search_err__ = "Incorrect response Type";
						}
					}else{
						this.anchor_graph_search_err__ = "Response Error: " + response.status;
					}
				}).catch(error=>{
					this.anchor_graph_search_err__ = "Error Loading: " + error.message;
					console.log( error.message );
				});
			}
		},
		anchor_graph_search_filter__: function(){
				if( this.anchor_graph_search_key__ == "" ){
					var v3 = [];
					var vbasic = this.anchor_graph_keywords_basic__;
					//this.echo__(vbasic);
					for(var i=0;i<vbasic.length&&v3.length<50;i++){
						vbasic[i]['r'] = "<b>" + vbasic[i]['l']['v'] + "</b> in <span class='text-secondary'>" + vbasic[i]['i_of']['v'] + "</span> [" + vbasic[i]['i'] + "]";
						v3.push(vbasic[i]);
					}
					this.anchor_graph_keywords_filtered__ = v3;
				}else{
					var v2 = this.anchor_graph_keywords__;
					var vkey = this.anchor_graph_search_key__+'';
					var w = vkey.split(/\W+/g);
					//this.echo__( w );
					w.reverse();
					var key2 = w.join(".*");
					var k = vkey.trim().replace(/\W+/g, ".*");
					var kpr = new RegExp("^"+k,"i");
					w.reverse();
					var v3 = [];
					var vkeys = {};
					var k = this.anchor_graph_search_key__.toLowerCase();

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
					this.anchor_graph_keywords_filtered__ = v3;
				}
		},
		anchor_graph_search_select__: function(k, t){
			var d = {
				"t":"GT",
				"i":k['i']+'',
				"v":k['l']['v']+'',
			};
			if( 'ol' in k && k['t'] == "p" ){
				d['v'] = k['ol'];
			}
			this.anchor_graph__ = d;
			this.anchor_graph_search_window__ = false;
		},
		window_scroll__: function(e){
			this.hide_bounds__();
			this.hide_contextmenu__();
			this.hide_other_menus__();
		},
		initialize_events__: function(){
			if( this.gt__(this.target_wrapper_id__) == null ){
				console.log(this.target_wrapper_id__ + " not found");
				setTimeout(this.initialize_events__,1000);
				return;
			}
			if( this.gt__(this.target_editor_id__) == null ){
				console.log("Editor initialization failed: Missing "+this.target_editor_id__);
				return;
			}
			this.find_relative_positon__();
			var vl2 = this.gt__(this.target_wrapper_id__);
			vl2.addEventListener("scroll", this.editor_scroll__);
			var vl = this.gt__(this.target_editor_id__);
			var c = "satish_editor_v1";
			if( vl.hasAttribute("class") ){
				var c= vl.getAttribute("class");
				if( c.match("/satish_editor_v1/i") == null ){
					c = c + " satish_editor_v1";
				}
			}
			vl.setAttribute("class",c);

			vl.setAttribute("data-id", "root");
			vl.setAttribute("spellcheck", "false");
			vl.setAttribute("contenteditable", "true");
			vl.addEventListener("drop", this.drop_event__, true);
			vl.addEventListener("click", this.clickit__, true);
			vl.addEventListener("dblclick", this.dblclickit__, true);
			vl.addEventListener("paste", this.onpaste__,true);
			vl.addEventListener("keydown", this.keydown__, true);
			vl.addEventListener("contextmenu", this.contextmenu_event__, true);
			vl.addEventListener("keyup", this.keyup__);
			vl.addEventListener("mousedown", this.this_mousedown__,true);
			vl.addEventListener("mouseup", this.this_mouseup__,true);
			vl.addEventListener("mousemove", this.this_mousemove__,false);
			vl.addEventListener("dragenter", this.dragenter__,true);
			vl.addEventListener("dragover", this.dragover__,true);

			document.addEventListener("keydown", this.keydown_iframe__,true);
			window.addEventListener( 'dblclick', this.dblclickit__, true );
			window.addEventListener( 'click', this.clickdoc__, true );
			window.addEventListener( 'dragstart', this.dragstart__, true );
			window.addEventListener( 'drag', this.dragstart__, true );
			window.addEventListener( 'keydown', this.keydown_outside__, true );
			window.addEventListener( 'mousemove', this.mousemove__, true );
			window.addEventListener( 'mouseover', this.mouseover__, true );
			window.addEventListener( 'drop', this.window_drop__, true );
		},
		find_relative_positon__: function(){
			var v = this.gt__(this.target_wrapper_id__);
			while( 1 ){
				console.log( v.outerHTML.substr(0,100) );
				vs = getComputedStyle(v);
				console.log( vs['position'] );
				if( vs['position'] == "relative" || vs['position'] == "fixed" ){
					//console.log( vs );
					vs2 = v.getBoundingClientRect();
					this.relative_x__ = Number(vs2['left']);
					this.relative_y__ = Number(vs2['top']);
					console.log( this.relative_x__ + "x" + this.relative_y__ );
					console.log( vs2.top + "x" + vs2.left );
					break;
				}
				v = v.parentNode;
				if( v.nodeName == "BODY" ){break;}
			}
		},
		event_mousemove__: function(e){
			this.clientX__ = Number( e.clientX );
			this.clientY__ = Number( e.clientY );
		},
		delete_tag__: function(){
			this.focused__.remove();
			this.unset_focused__();
			this.hide_contextmenu__();
		},
		set_focus_to__: function( vi ){
			this.set_focused2__( this.focused_tree__[ vi ]['v'] );
		},
			select_element_text__: function( v ){
				var s = new Range();
				s.setStart(v.childNodes[0],0);
				var ve = v.lastChild;
				if( ve.nodeName == "#text" ){
					s.setEnd( ve, ve.data.length );
				}else{
					s.setEnd( ve, ve.childNodes.length );
				}
				document.getSelection().removeAllRanges();
				document.getSelection().addRange(s);
			},
			select_range___with_element__: function( v ){
				var s = new Range();
				s.setStart(v,0);
				s.setEnd(v, v.childNodes.length);
				document.getSelection().removeAllRanges();
				document.getSelection().addRange(s);
			},
			range_select_full_nodes__: function(){
				var sr = document.getSelection().getRangeAt(0);
				if( sr.startContainer.nodeName == "#text" ){
					if( sr.startContainer.parentNode.nodeName.match(/^(B|I|EM|STRONG)$/) ){
						sr.setStartBefore( sr.startContainer.parentNode );
					}
				}
				if( sr.endContainer.nodeName == "#text" ){
					if( sr.endContainer.parentNode.nodeName.match(/^(B|I|EM|STRONG)$/) ){
						sr.setEndAfter( sr.endContainer.parentNode );
					}
				}
				//return false;
				var sr = document.getSelection().getRangeAt(0);
				const nodeIterator = document.createNodeIterator(
					sr.commonAncestorContainer,NodeFilter.SHOW_ALL,(node) => {return NodeFilter.FILTER_ACCEPT;}
				);
				var nodelist__ = [];
				var s = false;
				while (currentnode__ = nodeIterator.nextNode()) {
					if( currentnode__ == sr.startContainer ){
						s = true;
					}
					if( s ){
						if( currentnode__.nodeName.match( /^(B|STRONG|EM|I)$/ ) ){
							nodelist__.push( currentnode__ );
						}
					}
					if( currentnode__ == sr.endContainer ){break;}
				}
				while( nodelist__.length ){
					var v = nodelist__.pop();
					v.outerHTML = v.innerHTML;
				}
			},
			set_focus_at__: function( v ){
				var s = new Range();
				s.setStart(v,1);
				s.setEnd(v, 1);
				document.getSelection().removeAllRanges();
				document.getSelection().addRange(s);
			},
			side_settings_insert_form__: function(){
				if( this.side_settings_pos__ == 'top' ){
					var vpos = "beforebegin";
				}else if( this.side_settings_pos__ == 'bottom' ){
					var vpos = "afterend";
				}
				if( this.side_settings_type__ == "GridRow" ){
					var vl = this.ce__("DIV");
					vl.className="gridrow row";
					vl.innerHTML = `<div class="gridcol col-6"><p>Item 1</p></div>
							<div class="gridcol col-6"><p>Item 1</p></div>`;
					this.side_settings_el__.insertAdjacentElement( vpos, vl );
					return;
				}
				if( this.side_settings_type__ == "DefListItem" ){
					var vl = this.ce__("DIV");
					vl.className="row deflistrow";
					vl.innerHTML = `<div class="col-4 deflisttitle" >Title</div>
							<div class="col-8 deflistdata" ><p>Content</p></div>`;
					this.side_settings_el__.insertAdjacentElement( vpos, vl );
					return;
				}
				if( this.side_settings_el__.nodeName == "LI" ){
					var vl = this.ce__("LI");
					this.side_settings_el__.insertAdjacentElement( vpos, vl );
					return;
				}
				this.contextmenu_type__ = "insert_tag__";
				this.contextmenu_type_name__ = this.side_settings_type__+'';
				this.contextmenu_initiate__();
			},
			insert_item_at_location__: function( vtag ){
				this.hide_contextmenu__();
				this.unset_focused__();
				//if( this.insert_tag__ )
				{
					var newel = this.ce__("div");
					try{
						if(vtag in tag_settings_configs__ ){
							if( 'html' in tag_settings_configs__[ vtag ] ){
								newel.innerHTML = tag_settings_configs__[ vtag ]['html'];
							}else{
								newel.innerHTML = "<p>Error: Tag settings not found ...</p>";
							}
						}else{
							newel.innerHTML = "<p>Error: Tag settings not found ..</p>";
						}
					}catch(e){
						newel.innerHTML = "<p>Error: Tag settings not found</p>";
					}
					while(newel.children.length > 0 ){
						var s = newel.children[0];
						if( s.nodeName == "#text" ){
							newel.removeChild( newel.children[0] );
						}else if( this.side_settings_el__ ){
							if( this.side_settings_pos__ == "inside" ){
								this.side_settings_el__.insertAdjacentElement("beforeend", s);
							}else if( this.side_settings_pos__ == "top" ){
								this.side_settings_el__.insertAdjacentElement("beforebegin", s );
							}else{
								this.side_settings_el__.insertAdjacentElement("afterend", s );
							}
							this.side_settings_el__ = this.side_settings_el__.nextElementSibling;
						}
					}
					this.set_focused2__( s );
					if( vtag == "Table" ){
						setTimeout(this.initialize_tables__,500);
					}
				}
			},

			contextmenu_event__: function(e){
				//this.clickit__(e);
				//setTimeout(this.edit_tag_initiate,500);
				e.preventDefault();e.stopPropagation();
				console.log("contextmenu__Event");
				if( e.target.hasAttribute("data-id") && e.target.hasAttribute("class") ){
					if( e.target.getAttribute("data-id") == "bounds" && e.target.getAttribute("class").match(/table\_cell/i) ){
						this.contextmenu_type__ = 'table-cell';
						setTimeout(this.contextmenu_initiate__,50);
						return ;
					}
				}

				this.anchor_at_range__ = false;
				if( this.sections_list__.length ){
					this.sections_selected__= true;
					this.contextmenu_type__ = 'sections';
					console.log("1");
					setTimeout(this.contextmenu_initiate__,100);
				}else{
					var f = false;
					if( document.getSelection().rangeCount > 0 ){
						var sr = document.getSelection().getRangeAt(0);
						console.log( sr );
						if( sr.collapsed == false ){
							f = true;
						}
					}
					if( f ){
						this.contextmenu_type__ = 'inline';
						console.log("2");
						if( sr.endContainer.nodeName!="#text" || sr.startContainer.nodeName != "#text" ){
							var editable__ = this.find_target_editable__();
							var vs = editable__.children[0];
							sr.setStart(vs , 0);
							var ve = editable__.children[editable__.children.length-1];
							if( ve.nodeName != "#text" ){
								sr.setEnd( ve, ve.children.length);
							}else{
								sr.setEnd( ve, ve.data.length);
							}
						}
						this.anchor_at_range__ = sr;
						this.contextmenu_initiate__();
					}else{
						this.set_focused2__(e.target);
						console.log( this.focused_type__ );
						if( this.focused_anchor__ ){
							this.contextmenu_type__ = "context";
							this.contextmenu_initiate__();
						}else if( e.target == this.focused__ ){
							//this.edit_tag_initiate();
							this.contextmenu_type__ = "context";
							console.log("3");
							this.contextmenu_initiate__();
						}else{
							console.log( e.target );
							console.log("contextmenu__ not shown");
						}
					}
				}
			},

			show_context_submenu__: function(vt){
				this.contextmenu_submenu__ = true;
				this.contextmenu_submenu_type__ = vt+'';
			},
			show_context_sidemenu__: function(vt,e){
				this.contextsidemenu__ = true;
				this.contextsidemenu_type__ = vt;
				this.contextsidemenu_set_style__(e.target);
			},
			hide_contextmenu__: function(){
				this.contextmenu__ = false;
				this.contextmenu_submenu__ = false;
				this.contextsidemenu__ = false;
			},
			contextmenu_initiate__: function(){
				this.contextmenu_submenu__ = false;
				this.contextsidemenu__ = false;
				this.contextmenu__ = true;
				this.contextmenu_set_style__();
			},
			contextmenu_set_style__: function(){
				var l=0;var t=0;var w=0;var h=0;var b=0;var r=0;
				try{
					var v = this.side_settings_el__.getBoundingClientRect();
					var l=Number(v.left);var t=Number(v.top); var w=Number(v.width); var h=Number(v.height); var b=Number(v.bottom); var r=Number(v.right);
				}catch(e){
					console.error("contextmenu_set_style__: "+e);
					return ;
				}
				var sy = window.scrollY; //Number(scrollY);
				var sx = window.scrollX; //Number(scrollX);
				var x = Number(this.clientX__);
				var y = Number(this.clientY__);
				var xw = window.innerWidth;
				var xh = window.innerHeight;
				if( (y+250) > xh ){
					y  = xh-20-250;
				}
				this.contextmenu_style__ = "top:"+(y+sy-50)+"px;left:"+(x+sx+20)+"px;min-width:150px;";
				this.contextmenu__ = true;
			},
			contextsidemenu_set_style__: function(el){
				var l=0;var t=0;var w=0;var h=0;var b=0;var r=0;
				try{
					var v = el.getBoundingClientRect();
					var l=Number(v.left);var t=Number(v.top); var w=Number(v.width); var h=Number(v.height); var b=Number(v.bottom); var r=Number(v.right);
				}catch(e){

				}
				var x = Number(window.scrollX);
				var y = Number(window.scrollY);
				var xw = window.innerWidth;
				var xh = window.innerHeight;
				this.contextsidemenu_style__ = "top:"+(t-20+y)+"px;left:"+(r-10+x)+"px;min-width:100px;";
				this.contextsidemenu__ = true;
			},
			select_elements__: function(v){
				var sr = new Range();
				sr.setStart( v[0], 0 );
				var ve = v[v.length-1];
				sr.setEnd( ve, ve.children.length );
				var sel = document.getSelection();
				sel.removeAllRanges();
				sel.addRange(sr);
			},
			select_sections__: function(v){
				var sr = new Range();
				sr.setStart( v[0], 0 );
				var ve = v[v.length-1];
				sr.setEnd( ve, ve.children.length );
				var sel = document.getSelection();
				sel.removeAllRanges();
				sel.addRange(sr);
			},
			this_mousedown__: function(e){
				//this.insert_tag__ = false;
				this.set_focused__( e.target );
				if( e.buttons == 2 ){
					e.preventDefault();
					e.stopPropagation();
				}else{
					this.sections_list__ = [];
					this.sections_selected__ = false;
				}
			},
			this_mousemove__: function(e){
				//if( this.insert_tag__ )
				if( this.contextmenu__ == false && this.selection_start__ == false && this.sections_selected__ == false ){
					var v = e.target;
					if( v.hasAttribute("data-id") ){
						if( v.getAttribute("data-id") == "root" ){
							e.preventDefault();
							e.stopPropagation();
							return false;
						}
					}
					var cnt = 0;
					while( 1 ){
						cnt++;
						if( cnt>5 ){console.error("focuselement + 3");return ;break;}
						if( v.hasAttribute("data-id") ){
							if( v.getAttribute("data-id") == "root" ){
								break;
							}
						}
						if( v.nodeName.match(/^(BLOCKQUOTE|P|DIV|H1|H2|H3|H4|UL|OL|LI|TABLE)$/i) ){
							break;
						}
						v = v.parentNode;
					}
					var is_it_root = false;
					if( v.parentNode.hasAttribute("data-id") ){
						is_it_root = true;
					}
					var nodetype = "";
					var vv = v.parentNode;
					if( vv.nodeName == "DIV" ){
						if( vv.hasAttribute("class") ){
							var c = vv.getAttribute("class");
							if( c.match(/gridcol/i) && c.match(/col\-/i) ){
								nodetype = "GridColumn";
							}
							if( c.match(/grid/i) && c.match(/row/i) ){
								nodetype = "GridRow";
							}
							if( c.match(/callouticon/i) ){
								nodetype = "CallOut";
							}
							if( c.match(/calloutdata/i) ){
								nodetype = "CallOut";
							}
							if( c.match(/(deflisttitle|deflistdata)/i) ){
								nodetype = "DefListItem";
							}
						}
					}
					if( v.nodeName == "DIV" ){
						if( v.hasAttribute("class") ){
							var c = v.getAttribute("class");
							if( c.match(/gridcol/i) && c.match(/col\-/i) ){
								v = v.parentNode;
								nodetype = "GridRow";
							}
							if( c.match(/grid/i) && c.match(/row/i) ){
								nodetype = "GridRow";
							}
							if( c.match(/callouticon/i) ){
								v = v.parentNode;
								nodetype = "CallOut";
							}
							if( c.match(/calloutdata/i) ){
								v = v.parentNode;
								nodetype = "CallOut";
							}
							if( c.match(/(deflisttitle|deflistdata)/i) ){
								v = v.parentNode;
								nodetype = "DefListItem";
							}
						}
					}

					this.side_settings_el__ = v;
					this.side_settings_type__ = nodetype;

					var s = v.getBoundingClientRect();
					var l=Number(s.left);
					var t=Number(s.top);
					var w=Number(s.width);
					var h=Number(s.height);
					var b=Number(s.bottom);
					var r=Number(s.right);
					var oY = e.clientY;
					var smid = Number(s.top) + Number(s.height)/2;
					//console.log( oY + ": " + smid + ": " + s.top + ": " + s.bottom );
					//console.log( "top: "+ t + ";left: " + l + ";width: " + w + ";height: " + h + "; ");
					var sy = window.scrollY; //Number(scrollY);
					var sx = window.scrollX; //Number(scrollX);
					if( h > 20 ){
						if( is_it_root ){
							this.side_settings_style2__ = "user-select:none;top:" + (s.top+sy+5) + "px;left:" + (s.left-30+sx) + "px;";
						}else{
							this.side_settings_style2__ = "display:none;"
						}
						if( oY < smid ){
							this.side_settings_style__ = "top:" + (s.top+sy-3) + "px;left:" + (s.left+sx) + "px;width:"+(s.width)+";height:5px;";
							this.side_settings_pos__ = "top";
						}else{
							this.side_settings_style__ = "top:" + (s.bottom+sy-3) + "px;left:" + (s.left+sx) + "px;width:"+(s.width)+";height:5px;";
							this.side_settings_pos__ = "bottom";
						}
					}else{
						this.side_settings_style__ = "visibility:hidden;";
					}
					//console.log( "side:"+ this.side_settings_style__ );
				}
				if( e.buttons == 1 ){
					if( this.td_sel_cnt__ == 0 ){
						this.selection_start__ = true;
					}
				}
				return false;
			},
			this_mouseup__: function(e){
				if( this.selection_start__ ){
					this.selection_start__ = false;
					this.insert_tag__ = true;
					this.selectionchange2__();
				}
			},

			live_editing_update: function( vevent ){
				
			},

			load_links: function(){
				var vpost = {};
			},
			disable_activity: function(){
				this.enabled__ = false;
			},
			enable_activity: function(){
				this.enabled__ = true;
			},
			initialize_tables__: function(){
				var vtables__ = this.gt__(this.target_editor_id__).getElementsByTagName("table");
				for(var i=0;i < vtables__.length;i++ ){
					this.initialize_table__(vtables__[i]);
				}
			},
			initialize_table__: function( vtable__ ){
				console.log("Initialize Table");
				var tr_cnt = 0;
				var td_cnt = 0;
				if( vtable__.children[0].nodeName ==  "TBODY" || vtable__.children[0].nodeName ==  "THEAD" || vtable__.children[0].nodeName ==  "TBODY" ){
					var trs = Array.from(vtable__.children[0].children);
					for(var j=0;j < trs.length;j++){
						if( trs[j].nodeName != "TR" ){ trs[j].remove(); trs.splice(j,1); j--; }
					}
					tr_cnt = trs.length;
					for( var j=0;j < trs.length;j++){
						var tds = Array.from(trs[j].children);
						for( var k=0;k < tds.length;k++){
							if( tds[k].nodeName != "TD" && tds[k].nodeName != "TH" ){ tds[k].remove(); tds.splice(k,1); k--; }
						}
						td_cnt = tds.length;
						for(var k=0;k<tds.length;k++){
							tds[k].addEventListener("mouseup", this.td_mouse_up__);
							tds[k].addEventListener("mousedown", this.td_mouse_down__);
							tds[k].addEventListener("mousemove", this.td_mouse_move__);
						}
					}
				}
			},
			table_cell_get__: function( i, j ){
				if( this.focused_table__ ){
					try{
						return this.focused_table__.children[0].children[i].children[j];
					}catch(e){
						console.error("table_cell_get__: " + i + " " + j + " : " + e);
					}
				}
				return false;
			},
			td_mouse_down__: function( e ){if( this.enabled__ ){
				console.log("td mouse down");
				var v = e.target;
				while( 1 ){
					if( v.nodeName.match(/^(TD|TH)$/) ){
						break;
					}
					v = v.parentNode;
				}
				var vtri = Number(v.parentNode.rowIndex);
				var vtdi = Number(v.cellIndex);
				this.focused_table__ = v.parentNode.parentNode.parentNode;
				this.td_sel_start__= true;
				this.td_sel_start_tr__=vtri;
				this.td_sel_start_td__=vtdi;
				this.td_sel_end_tr__=vtri;
				this.td_sel_end_td__=vtdi;
				this.td_sel_cells__ = [];
				this.td_sel_cnt__ = 0;
				setTimeout(this.td_calc_cells__,50);
			}},
			td_mouse_move__: function(e){if( this.enabled__ ){
				//console.log("td mouse move");
				if( e.target.nodeName != "TD" && e.target.nodeName != "TH" && e.target.nodeName != "TR" ){
					return false;
				}
				// var vtri = Number(e.target.getAttribute("data-tr-id"));
				// var vtdi = Number(e.target.getAttribute("data-td-id"));
				var vtri = Number( e.target.parentNode.rowIndex );
				var vtdi = Number( e.target.cellIndex );
				var tb = e.target.parentNode.parentNode.parentNode;
				if( tb != this.focused_table__ ){
					return false;
				}
				if( e.buttons == 1 && this.td_sel_start__ ){
					this.hide_bounds__('table');
					this.td_sel_end_tr__=vtri;
					this.td_sel_end_td__=vtdi;
					if( this.td_sel_cnt__ > 1 ){
						var sr = document.getSelection().getRangeAt(0);
						sr.setEnd( sr.startContainer, 0 );
					}
					setTimeout(this.td_calc_cells__,50);
				}
			}},
			selection_collapse__: function(){
				var sr = document.getSelection().getRangeAt(0);
				sr.setEnd( sr.startContainer, 0 );
				setTimeout(this.selectionchange2__,50);
			},
			td_mouse_up__: function(e){if( this.enabled__ ){
				console.log("td mouse up");
				this.td_sel_start__= false;
				setTimeout(function(v){v.table_cell_style__ = v.table_cell_style__ + ";pointer-events:initial; background-color:rgba(255,255,255,0.2)";},100,this);
				console.log( this.table_cell_style__ );
			}},
			td_sel_unfocus__: function(){
				this.td_sel_start__= false;
				this.td_sel_start_tr__=-1;
				this.td_sel_start_td__=-1;
				this.td_sel_end_tr__=-1;
				this.td_sel_end_td__=-1;
				this.td_sel_cells__ = [];
				this.table_cell_style__ = "display:none";
				if( this.focused_table__ ){
					this.td_calc_cells__();
				}
			},
			td_calc_cells__: function(){
				if( !this.focused_table__ ){
					console.log("td_calc_cells__: table not in focus");
					return false;
				}
				var vtot_tr = Number( this.focused_table__.children[0].children.length );
				var vtot_td = Number( this.focused_table__.children[0].children[0].children.length );

				var s_tr = this.td_sel_start_tr__;
				var e_tr = this.td_sel_end_tr__;
				var s_td = this.td_sel_start_td__;
				var e_td = this.td_sel_end_td__;

				if( e_tr < s_tr ){
					var t = s_tr;
					s_tr = e_tr;
					e_tr = t;
				}
				if( e_td < s_td ){
					var t = s_td;
					s_td = e_td;
					e_td = t;
				}
				var cells__ = [];
				var cnt = 0;
				for(var i=0;i<vtot_tr;i++){
					for(var j=0;j<vtot_td;j++){
						var td = this.table_cell_get__(i,j);
						if( this.td_sel_start__ ){
							if( i >= s_tr && i <= e_tr && j >= s_td && j <= e_td ){
								cnt++;
							}
						}
					}
				}
				for(var i=0;i<vtot_tr;i++){
					var cellss__ = [];
					for(var j=0;j<vtot_td;j++){
						var td = this.table_cell_get__(i,j);
						if( this.td_sel_start__ ){
							if( i >= s_tr && i <= e_tr && j >= s_td && j <= e_td ){
								if( cnt > 1 ){
									
								}
								cellss__.push({"coli":j,"col":this.table_cell_get__(i,j)});
							}else{
								
							}
						}else{
							try{td.removeAttribute("class");}catch(e){}
						}
					}
					if( cellss__.length ){
						cells__.push({"rowi":i, "cols":cellss__ });
					}
				}
				this.td_sel_cnt__ = cnt;
				this.td_sel_cells__ = cells__;
				if( cnt > 1 ){
					this.focused_tds_set_bounds__();
				}else{
					this.table_cell_style__ = "display:none";
				}
			},
			focused_tds_set_bounds__: function(){
				if( this.focused_table__ ){
					if( this.td_sel_start_tr__ > this.td_sel_end_tr__ ){
						var trsi = this.td_sel_end_tr__;
						var trei = this.td_sel_start_tr__;
					}else{
						var trsi = this.td_sel_start_tr__;
						var trei = this.td_sel_end_tr__;
					}
					if( this.td_sel_start_td__ > this.td_sel_end_td__ ){
						var tdsi = this.td_sel_end_td__;
						var tdei = this.td_sel_start_td__;
					}else{
						var tdsi = this.td_sel_start_td__;
						var tdei = this.td_sel_end_td__;
					}
					var v1 = this.focused_table__.children[0].children[trsi].children[tdsi].getBoundingClientRect();
					var v2 = this.focused_table__.children[0].children[trei].children[tdei].getBoundingClientRect();
					var sy = window.scrollY; //Number(scrollY);
					var sx = window.scrollX; //Number(scrollX);
					var l=Number(v1.left);var t=Number(v1.top); 
					var w=Number(v2.right-v1.left); var h=Number(v2.bottom-v1.top); 
					this.table_cell_style__ = "top:"+(t+sy)+"px;left:"+(l+sx)+"px;width:"+(w)+"px;height:"+(h)+"px";
					//console.log("cells__tyle: "+ this.table_cell_style__ );
				}else{
					//this.table_cell_style__ = "";
					this.table_cell_style__ = "display:none";
					//console.log("cells__tyle: "+ this.table_cell_style__ );
				}
			},
			paragraph_to_text__: function(){
				if( this.focused__.nodeName.match(/^(P|DIV|H1|H2|H3|H4)$/) ){
					if( this.focused__.parentNode.hasAttribute("contenteditable") == false ){
						if( this.focused__.parentNode.nodeName.match(/^(LI|TD|TH)$/) ){
							var v = Array.from(this.focused__.children);
							while( v.length ){
								if( v[0].nodeName == "#text"){
									this.focused__.insertAdjacentText("afterend", v[0].data );
								}else{
									this.focused__.insertAdjacentElement("afterend", v[0] );
								}
								v.splice(0,1);
							}
							this.select_range___with_element__(this.focused__.parentNode);
							this.focused__.remove();
							this.selectionchange2__();
						}else{
							var v = Array.from(this.focused__.childNodes);
							while( v.length ){
								if( v[0].nodeName == "#text"){
									this.focused__.insertAdjacentText("afterend", v[0].data );
								}else{
									this.focused__.insertAdjacentElement("afterend", v[0] );
								}
								v.splice(0,1);
							}
							this.select_range___with_element__(this.focused__.parentNode);
							this.focused__.remove();
							this.selectionchange2__();
						}
					}
				}
			},
			text_to_paragraph__: function(){
				this.hide_contextmenu__();
				if( this.focused__.nodeName == "LI" || this.focused__.nodeName == "TD" ){
					var vl = this.ce__("p");
					vl.innerHTML = this.focused__.innerHTML;
					this.focused__.innerHTML = "";
					this.focused__.appendChild( vl );
					this.focused_block_set_bounds__();
					// this.select_range___with_element__( vl );
					// this.selectionchange2__();
				}
			},
			echo__: function(v){
				if( typeof(v)=="object" ){
					console.log( JSON.stringify(v,null,4));
				}else{
					console.log( v );
				}
			},
			
			is_it_in_text__: function(){
				var sr = document.getSelection().getRangeAt(0);
				const nodeIterator = document.createNodeIterator(
					sr.commonAncestorContainer,NodeFilter.SHOW_ALL,(node) => {return NodeFilter.FILTER_ACCEPT;}
				);
				var s = false;
				var f = true;
				while( currentnode__ = nodeIterator.nextNode() ){
					if( currentnode__ == sr.startContainer ){
						s = true;
					}
					if( currentnode__.nodeName.match(/^(P|LI|TD|TH|DIV)$/) ){
						f = false;
						return false;
					}
					if( currentnode__ == sr.endContainer ){
						break;
					}
				}
				return true;
			},
			ul_change_to_text__: function(){
				if( this.focused_ul__ ){
					var parent_node = this.focused_ul__.parentNode;
					var parent_node_type = this.focused_ul__.parentNode.nodeName;
					var lis= Array.from(this.focused_ul__.childNodes);
					for( var i=0;i<lis.length;i++){
						if( lis[i].nodeName != "LI" ){
							lis[i].remove();
							lis.splice(i,1);
							i--;
						}
					}
					var nodes = [];
					var nxt = this.focused_ul__;
					for(var i=0;i<lis.length;i++){
						if( parent_node_type.match(/^(LI)$/) ){
							parent_node.insertAdjacentElement("afterend",lis[i]);
							parent_node = lis[i];
							nodes.push(lis[i]);
						}else if( parent_node_type.match(/^(P)$/) ){
							var vl = this.ce__("P");
							vl.innerHTML = lis[i].innerHTML;
							parent_node.insertAdjacentElement("afterend",vl);
							parent_node = vl;
							nodes.push(vl);
						}else{
							var vl = this.ce__("P");
							vl.innerHTML = lis[i].innerHTML;
							lis[i].remove();
							nxt.insertAdjacentElement("afterend", vl);
							nxt = vl;
							nodes.push(vl);
						}
					}
					this.focused_ul__.remove();
					this.select_elements__( nodes );
					setTimeout(this.selectionchange2__,50);
				}else{
					console.log("UL not focused__!");
				}
			},
			is_parent_in_bold__: function( v){
				var cnt = 0;
				while( 1 ){cnt++;if(cnt>10){break;}
					if( v.nodeName !="#text" ){
					if( v.nodeName == "#document" || v.nodeName == "BODY" ){return false;}
					if( "hasAttribute" in v == false ){return false;}
					if( v.nodeName.match(/^(B|STRONG)$/) ){
						return v;
					}
					}
					v = v.parentNode;
				}
				return false;
			},
			make_bold__: function(){
				this.hide_contextmenu__();
				if( this.anchor_at_range__ ){
					var sel = document.getSelection();
					sel.removeAllRanges();
					sel.addRange(this.anchor_at_range__);
				}
				var sr = document.getSelection().getRangeAt(0);
				if( sr.collapsed ){
					if( sr.startContainer.nodeName == "#text" ){
						var bold_parent__ = this.is_parent_in_bold__(sr.startContainer.parentNode);
						if( bold_parent__ ){
							while( bold_parent__.childNodes.length ){
								if( bold_parent__.childNodes[0].nodeName=="#text" ){
									bold_parent__.insertAdjacentText("beforebegin", bold_parent__.childNodes[0].data);
									bold_parent__.childNodes[0].remove();
								}else{
									bold_parent__.insertAdjacentElement("beforebegin", bold_parent__.childNodes[0]);
								}
							}
							this.set_focus_at__( bold_parent__.parentNode.childNodes[0] );
							bold_parent__.remove();
						}else{
							var vt = sr.commonAncestorContainer;
							if( vt.nodeName == "#text"){
								vt = vt.parentNode;
							}
							var vl = this.ce__("strong");
							while( vt.childNodes.length ){
								vl.appendChild( vt.childNodes[0] );
							}
							vt.appendChild(vl);
							this.select_range___with_element__(vl);
						}
					}
				}else{
					var bold_parent___start = this.is_parent_in_bold__( sr.startContainer );
					var bold_parent___end = this.is_parent_in_bold__( sr.endContainer );
					if( bold_parent___start && bold_parent___end && bold_parent___start == bold_parent___end ){
						var bold_parent__ = bold_parent___start;
						while( bold_parent__.childNodes.length ){
							if( bold_parent__.childNodes[0].nodeName=="#text" ){
								bold_parent__.insertAdjacentText("beforebegin", bold_parent__.childNodes[0].data);
								bold_parent__.childNodes[0].remove();
							}else{
								bold_parent__.insertAdjacentElement("beforebegin", bold_parent__.childNodes[0]);
							}
						}
						this.set_focus_at__( bold_parent__.parentNode.childNodes[0] );
						bold_parent__.remove();
					}else if( sr.startContainer.nodeName =="#text" && sr.endContainer.nodeName =="#text" && sr.startContainer == sr.endContainer ){
						this.apply_style_text__('bold');
					}else{
						this.range_remove_style__( 'bold' );
						if( this.is_it_single_text_node__() ){
							this.apply_style_text__('bold');
						}else if( this.is_it_in_text__() ){
							this.range_apply_style_text__('bold');
						}else{
							this.range_apply_style__('bold');
						}
					}
				}
				return false;
			},
			make_italic__: function(){
				alert('pending');
			},
			anchor_edit__: function(){
				if( this.focused_anchor__ ){
					this.contextmenu_submenu_type__ = 'A';
					this.contextmenu_submenu__ = true;
					this.anchor_href__ = this.focused_anchor__.getAttribute("href");
					this.anchor_text__ = this.focused_anchor__.innerText;
				}else{
					this.hide_contextmenu__();
					console.error("focused__ andhor not found");
				}
			},
			make_link__: function(){
				this.contextmenu_submenu_type__ = 'A';
				this.contextmenu_submenu__ = true;
				if( this.anchor_at_range__ ){
					var c = this.anchor_at_range__.cloneContents().childNodes;
					var vh = "";
					for(var i=0;i<c.length;i++){
						if( c[i].nodeName == "#text" ){
							vh = vh + c[i].data;
						}else{
							vh = vh + c[i].innerText;
						}
					}
					this.anchor_href__ = "";
					this.anchor_text__ = vh;
				}
			},
			anchor_create__: function(){
				if( this.focused_anchor__ ){
					this.focused_anchor__.setAttribute("href", this.anchor_href__+'');
					this.focused_anchor__.innerHTML = this.anchor_text__;
				}else if( this.anchor_at_range__ ){
					var a = this.ce__("a");
					a.innerHTML = this.anchor_text__+'';
					a.setAttribute("href", this.anchor_href__+'');
					var sel = document.getSelection();
					sel.removeAllRanges();
					sel.addRange(this.anchor_at_range__);
					this.anchor_at_range__ = false;
					var sr = document.getSelection().getRangeAt(0);
					sr.deleteContents();
					sr.insertNode( a );
					sel.collapseToStart();
					a.focus();
					this.set_focused2__( a );
				}else{
					console.error("anchor_create__: not found");
				}
				this.hide_contextmenu__();
			},
			make_ol__: function(){
				this.hide_bounds__();this.hide_contextmenu__();
				if( this.sections_list__.length == 0 ){
					if( this.focused__.nodeName.match(/^(P|DIV)$/) ){
						this.sections_list__ = [this.focused__];
					}else if( this.focused__.nodeName.match( /^(TD|TH)$/ ) ){
						var vul = this.ce__("ol");
						var vli = this.ce__("li");
						vul.appendChild( vli );
						this.focused__.appendChild( vul );
						while( this.focused__.childNodes[ 0 ].nodeName == "#text" ){
							vli.appendChild( this.focused__.childNodes[0] );
						}
						return false;
					}
				}
				if( this.sections_list__.length ){
					if( this.sections_is_all_lis__ ){
						if( confirm("Do you want remove bullets?") ){
							//var vids = JSON.
							this.remove_bullets__();
						}
					}else{
						var vs = this.sections_list__[0];
						var insertinul = false;
						if( vs.previousElementSibling ){
							if( vs.previousElementSibling.nodeName.match(/^(UL|OL)$/) ){
								insertinul = vs.previousElementSibling;
							}else{
								var insertinul = this.ce__("OL");
								vs.insertAdjacentElement("beforebegin", insertinul );
							}
						}else{
							var insertinul = this.ce__("OL");
							vs.insertAdjacentElement("beforebegin", insertinul );
						}
						var newels = [];
						for(var i=0;i<this.sections_list__.length;i++){
							var vl = this.ce__("li");
							vl.innerHTML = this.sections_list__[i].innerHTML;
							newels.push(vl);
							insertinul.appendChild( vl );
							this.sections_list__[i].remove();
						}
						var sel = document.getSelection();
						var sr = new Range();
						sr.setStart( newels[0],0 );
						sr.setEnd( newels[ newels.length-1 ], newels[ newels.length-1 ].childNodes.length );
						sel.removeAllRanges();
						sel.addRange(sr);
						setTimeout(this.selectionchange2__,50);
						if( insertinul.nextElementSibling ){
							if( insertinul.nextElementSibling.nodeName == "UL" ){
								while( insertinul.nextElementSibling.childNodes.length ){
									insertinul.appendChild( insertinul.nextElementSibling.childNodes[0] );
								}
								insertinul.nextElementSibling.remove();
							}
						}
					}
				}
			},
			make_ul__: function(){
				this.hide_bounds__();this.hide_contextmenu__();
				if( this.sections_list__.length == 0 ){
					if( this.focused__.nodeName.match(/^(P|DIV)$/) ){
						this.sections_list__ = [this.focused__];
					}else if( this.focused__.nodeName.match( /^(TD|TH)$/ ) ){
						var vul = this.ce__("ul");
						var vli = this.ce__("li");
						vul.appendChild( vli );
						this.focused__.appendChild( vul );
						while( this.focused__.childNodes[ 0 ].nodeName == "#text" ){
							vli.appendChild( this.focused__.childNodes[0] );
						}
						return false;
					}
				}
				if( this.sections_list__.length ){
					if( this.sections_is_all_lis__ ){
						if( confirm("Do you want remove bullets?") ){
							//var vids = JSON.
							this.remove_bullets__();
						}
					}else{
						var vs = this.sections_list__[0];
						var insertinul = false;
						if( vs.previousElementSibling ){
							if( vs.previousElementSibling.nodeName.match(/^(UL|OL)$/) ){
								insertinul = vs.previousElementSibling;
							}else{
								var insertinul = this.ce__("UL");
								vs.insertAdjacentElement("beforebegin", insertinul );
							}
						}else{
							var insertinul = this.ce__("UL");
							vs.insertAdjacentElement("beforebegin", insertinul );
						}
						var newels = [];
						for(var i=0;i<this.sections_list__.length;i++){
							var vl = this.ce__("li");
							vl.innerHTML = this.sections_list__[i].innerHTML;
							newels.push(vl);
							insertinul.appendChild( vl );
							this.sections_list__[i].remove();
						}
						var sel = document.getSelection();
						var sr = new Range();
						sr.setStart( newels[0],0 );
						sr.setEnd( newels[ newels.length-1 ], newels[ newels.length-1 ].childNodes.length );
						sel.removeAllRanges();
						sel.addRange(sr);
						setTimeout(this.selectionchange2__,50);
					}
				}
			},
			remove_bullets__: function(){
				this.hide_bounds__();this.hide_contextmenu__();
				if( this.sections_list__.length == 1 && this.sections_list__[0].nodeName.match( /^(UL|OL)$/ ) ){
					this.sections_list__ = this.sections_list__[0].childNodes;
				}

				var vs = this.sections_list__[0];
				var ve = this.sections_list__[ this.sections_list__.length-1 ];
				var vp = vs.parentNode;

				if( vs.previousElementSibling == null && ve.nextElementSibling == null ){
					this.ul_change_to_text__();
					return false;
				}else if( vs.previousElementSibling == null ){
					var k = this.sections_list__;
					var nodelist__ = [];
					var parent_ul__ = vs.parentNode;
					for( var i=0;i<k.length;i++){
						var vl = this.ce__('P');
						vl.innerHTML = k[i].innerHTML;
						k[i].remove();
						parent_ul__.insertAdjacentElement("beforebegin", vl);
						parent_ul__ = vl;
						nodelist__.push( vl );
					}
					this.select_elements__(nodelist__);
					setTimeout(this.selectionchange2__,50)
				}else if( ve.nextElementSibling == null ){
					var k = this.sections_list__;
					var nodelist__ = [];
					var parent_ul__ = ve.parentNode;
					for( var i=0;i<k.length;i++){
						var vl = this.ce__('P');
						vl.innerHTML = k[i].innerHTML;
						k[i].remove();
						parent_ul__.insertAdjacentElement("afterend", vl);
						parent_ul__ = vl;
						nodelist__.push( vl );
					}
					this.select_elements__(nodelist__);
					setTimeout(this.selectionchange2__,50)
				}else{
					var parent_section__ = vs.parentNode;

					var nodelist__ = [];
					var k = this.sections_list__;
					for( var i=0;i<k.length;i++){
						nodelist__.push(k[i]);
					}
					var newul__ = this.ce__("UL");
					while( ve.nextElementSibling ){
						newul__.appendChild( ve.nextElementSibling );
					}
					//parentul.insertAdjacentElement("afterend", newel);
					var new_nodes = [];
					while( nodelist__.length ){
						var vl = this.ce__("p");
						vl.innerHTML = nodelist__[0].innerHTML;
						nodelist__[0].remove();
						parent_section__.insertAdjacentElement("afterend", vl);
						parent_section__ = vl;
						new_nodes.push( vl );
						nodelist__.splice(0,1);
					}
					parent_section__.insertAdjacentElement("afterend", newul__);
					this.select_elements__( new_nodes );
					setTimeout(this.selectionchange2__,50)
				}
			},
			select_range__: function(r){
				document.getSelection().removeAllRanges();
				document.getSelection().addRange(r);
			},
			make_indent__: function(){
				this.hide_contextmenu__();
				this.set_focused__();
			},
			make_unindent__: function(){
				this.hide_contextmenu__();
				this.set_focused__();				
			},
			make_clear__: function(){
				this.hide_contextmenu__();
				this.set_focused__();
			},
			range_remove_style__: function( vop ){
				var sr = document.getSelection().getRangeAt(0);
				if( sr.startContainer.nodeName == "#text" ){
					if( sr.startContainer.parentNode.nodeName.match(/^(B|I|EM|STRONG|SPAN)$/) ){
						sr.setStartBefore( sr.startContainer.parentNode );
					}
				}
				if( sr.endContainer.nodeName == "#text" ){
					if( sr.endContainer.parentNode.nodeName.match(/^(B|I|EM|STRONG|SPAN)$/) ){
						sr.setEndAfter( sr.endContainer.parentNode );
					}
				}
				var start_c = sr.startContainer;
				var end_c = sr.endContainer;
				if( end_c.nextElementSibling ){
					var end_sel_node__ = end_c.nextElementSibling;
				}else{
					var end_sel_node__ = end_c.parentNode.nextElementSibling;
				}
				var sr = document.getSelection().getRangeAt(0);
				const nodeIterator = document.createNodeIterator(
					sr.commonAncestorContainer,NodeFilter.SHOW_ALL,(node) => {return NodeFilter.FILTER_ACCEPT;}
				);
				var nodelist__ = [];
				var s = false;
				while (currentnode__ = nodeIterator.nextNode()) {
					if( currentnode__ == sr.startContainer ){
						s = true;
					}
					if( s ){
						if( currentnode__.nodeName.match( /^(B|STRONG|SPAN|EM|I|H1|H2|H3|H4)$/ ) ){
							nodelist__.push( currentnode__ );
						}else if( currentnode__.nodeName !="#text" ){
							if( currentnode__.hasAttribute("style") ){
								currentnode__.removeAttribute("style");
							}
						}
					}
					if( currentnode__ == end_sel_node__ ){
						break;
					}
				}
				while( nodelist__.length ){
					var v = nodelist__.pop();
					if( 1==2 && v == end_c ){
					}else{
						v.outerHTML = v.innerHTML;
					}
				}
				var r = new Range();
				r.setStart(start_c, 0);
				r.setEnd(end_c, end_c.childNodes.length );
				document.getSelection().removeAllRanges();
				document.getSelection().addRange(r);
			},
			range_apply_style__: function( vop ){
				var sr = document.getSelection().getRangeAt(0);
				const nodeIterator = document.createNodeIterator(
					sr.commonAncestorContainer,NodeFilter.SHOW_ALL,(node) => {return NodeFilter.FILTER_ACCEPT;}
				);
				var start_c = sr.startContainer;
				var end_c = sr.endContainer;
				if( sr.endContainer.nextElementSibling ){
					var end_sel_node__ = sr.endContainer.nextElementSibling;
				}else{
					var end_sel_node__ = sr.endContainer.parentNode.nextElementSibling;
				}
				var nodelist__ = [];
				var s = false;
				while( currentnode__ = nodeIterator.nextNode() ){
					if( currentnode__ == end_sel_node__ ){
						break;
					}
					if( currentnode__ == sr.startContainer ){
						s = true;
						if( currentnode__.nodeName == "#text" ){
							if( currentnode__.parentNode.nodeName.match( /^(P|LI|TD)$/ ) ){
								nodelist__.push( currentnode__.parentNode );
							}
						}
					}
					if( s ){
						if( currentnode__.nodeName.match( /^(P|LI|TD)$/ ) ){
							nodelist__.push( currentnode__ );
						}
					}
				}
				for(var i=0;i<nodelist__.length;i++){
					if( nodelist__[ i ].nodeName.match( /^(LI|TD)$/ ) ){
						var f = true;
						for(var j=0;j<nodelist__[ i ].childNodes.length;j++){
							if( nodelist__[ i ].childNodes[ j ].nodeName != "#text" ){
								f = false;
							}
						}
						if( f ){
							var k = this.ce__("strong");
							nodelist__[ i ].appendChild(k);
							while( nodelist__[ i ].childNodes.length > 1 ){
								k.appendChild( nodelist__[ i ].childNodes[0] );
							}
						}
					}
					if( nodelist__[ i ].nodeName.match( /^(P)$/ ) ){
						var k = this.ce__("strong");
						nodelist__[ i ].appendChild(k);
						while( nodelist__[ i ].childNodes.length > 1 ){
							k.appendChild( nodelist__[ i ].childNodes[0] );
						}
					}
				}
				var r = new Range();
				r.setStart(start_c, 0);
				r.setEnd(end_c, end_c.childNodes.length );
				document.getSelection().removeAllRanges();
				document.getSelection().addRange(r);
			},
			range_apply_style_text__: function( vop ){
				var sr = document.getSelection().getRangeAt(0);
				const nodeIterator = document.createNodeIterator(
					sr.commonAncestorContainer,NodeFilter.SHOW_ALL,(node) => {return NodeFilter.FILTER_ACCEPT;}
				);
				var nodelist__ = [];
				var s = false;
				while( currentnode__ = nodeIterator.nextNode() ){
					if( currentnode__ == sr.startContainer ){
						s = true;
					}
					if( s ){
						if( currentnode__.nodeName == "#text" ){
							if( currentnode__ == sr.startContainer ){
								nodelist__.push({"n":currentnode__,"s":sr.startOffset});
							}else{
								nodelist__.push({"n":currentnode__,"s":-1});
							}
						}
						if( currentnode__.nodeName.match( /^(P|LI|TD)$/ ) ){
							nodelist__.push( currentnode__ );
						}
					}
					if( currentnode__ == sr.endContainer ){
						if( currentnode__.nodeName == "#text" ){
							nodelist__.push( currentnode__.parentNode );
						}
						break;
					}
				}
				for(var i=0;i<nodelist__.length;i++){
					if( nodelist__[ i ].nodeName.match( /^(LI|TD)$/ ) ){
						var f = true;
						for(var j=0;j<nodelist__[ i ].childNodes.length;j++){
							if( nodelist__[ i ].childNodes[ j ].nodeName != "#text" ){
								f = false;
							}
						}
						if( f ){
							var k = this.ce__("strong");
							nodelist__[ i ].appendChild(k);
							while( nodelist__[ i ].childNodes.length > 1 ){
								k.appendChild( nodelist__[ i ].childNodes[0] );
							}
						}
					}
					if( nodelist__[ i ].nodeName.match( /^(P)$/ ) ){
						var k = this.ce__("strong");
						nodelist__[ i ].appendChild(k);
						while( nodelist__[ i ].childNodes.length > 1 ){
							k.appendChild( nodelist__[ i ].childNodes[0] );
						}
					}
				}
			},
			is_it_single_text_node__: function(vop){
				var sr = document.getSelection().getRangeAt(0);
				if( sr.startContainer.nodeName == "#text" && sr.startContainer == sr.endContainer ){
					return true;
				}else{
					return false;
				}
			},
			apply_style_text__: function( vop ){
				var sr = document.getSelection().getRangeAt( 0 );
				var av = Array.from( sr.startContainer.parentNode.childNodes );
				var ai = av.indexOf( sr.startContainer );
				var v1 = sr.startContainer.parentNode.childNodes[ ai ];
				var v1i = sr.startOffset;
				var v2i = sr.endOffset;
				var kp = new DocumentFragment();
				kp.appendChild( document.createTextNode( v1.nodeValue.substr(0, v1i) ) );
				var b = this.ce__("strong");
				b.innerHTML = v1.nodeValue.substr( v1i, (v2i-v1i) );
				kp.appendChild( b );
				kp.appendChild( document.createTextNode( v1.nodeValue.substr( (v2i), 3333) ) );
				sr.startContainer.parentNode.insertBefore( kp, v1 );
				v1.remove();
				var nr = new Range();
				nr.setStart(b,0);
				nr.setEndAfter(b);
				var sel = document.getSelection();
				sel.removeAllRanges();
				sel.addRange(nr);
			},
			ul_type_change__: function(e){
				this.focused_ul__.className = this.ul_type__;
			},
			mousedown__: function(e){if( this.enabled__ ){
			}},
			mouseup__: function(e){if( this.enabled__ ){

			}},
			mouseover__: function(e){if( this.enabled__ ){

			}},
			mousemove__: function(e){if( this.enabled__ ){
				if( this.image_popup ){
					return false;
				}
				if( this.sections_selected__ ){
					return false;
				}
				return false;
			}},
			
			selectstart__: function(e){
				console.log("select start");
			},
			selectionchange__: function(e){if( this.enabled__ ){
				if( document.getSelection().isCollapsed == false ){
					var t = Number( new Date().getTime() );
					if( ( t  - this.selection_t ) > 100 ){
						this.selection_t = t;
						setTimeout(this.selectionchange2__, 50, e);
					}else{
						//console.log("skip");
					}
				}else{
					this.sections_selected__= false;
				}
			}},
			selectionchange2__: function( e ){
				this.hide_bounds__();
				var vids = this.find_sections__();
				if( vids ){
					this.sections_list__ = vids;
					this.sections_selected__= true;
				}else{
					this.sections_selected__= false;
					this.sections_list__ = [];
				}
			},
			dblclickit__: function(e){
				if( this.enabled__ ){
					setTimeout(this.dblclickit2__, 50, e);
				}
			},
			dblclickit2__: function(e){
				if( e.target.nodeName == "IMG" ){
					if( this.focused_block_type__ == "IMAGE" ){
						this.hide_other_menus__();
						setTimeout(this.show_image_update_popup,100);
					}
				}else{
					//this.edit_tag_initiate();
				}
			},

			check_after_enter__: function(){
				//return false;
				var sr = document.getSelection().getRangeAt(0);
				if( sr.startOffset == sr.endOffset ){
					var v = false;
					if( sr.startContainer.nodeName == "#text" ){
						var vtext = sr.startContainer;
						var vprev = vtext.previousSibling;
						if( vprev ){
							if( vprev.nodeName == "BR" ){
								v = vprev;
							}
						}
					}else{
						v = sr.startContainer.childNodes[ sr.startOffset ];
					}
					if( v ){
						if( v.nodeName == "BR" ){
							if( v.parentNode.nodeName.match( /^(P|LI|TD)$/ ) == null ){
								var vl = this.ce__("p");
								vl.innerHTML= "&nbsp;";
								v.replaceWith( vl );
								this.select_element_text__(vl);
								if( vl.previousSibling ){
									var vprev = vl.previousSibling;
								}else{
									var vprev = false;
								}
								if( vl.nextSibling ){
									var vnext = vl.nextSibling;
								}else{ var vnext = false;}
								if( vnext ){
									if( vnext.nodeName == "#text" ){
										vl.appendChild( vnext );
									}
								}
								if( vprev ){
									if( vprev.nodeName == "#text" ){
										var v2 = this.ce__("p");
										v2.innerHTML = vprev.data;
										vprev.replaceWith(v2);
										if( v2.previousSibling ){
											vprev = v2.previousSibling;
										}else{
											vprev = false;
										}
									}
									if( vprev.nodeName == "BR" ){
										var vtext = vprev.previousSibling;
										if( vtext ){
											if( vtext.previousSibling != null ){
												if( vtext.previousSibling.nodeName == "#text" ){
													vtext.data = vtext.previousSibling.data+ vtext.data;
													vtext.previousSibling.remove();
												}else{
													var vtext_prev = vtext.previousSibling;
													console.error("After enter. Previous element sibling found!");
												}
											}{
												var v2 = this.ce__("p");
												v2.innerHTML = vtext.data;
												vtext.replaceWith(v2);
												vprev.remove();
											}
										}
									}
								}
								return false;
							}
						}else if( v.previousElementSibling ){
							if( v.previousElementSibling.nodeName == "BR" ){
								v = v.previousElementSibling;
								if( v.parentNode.nodeName != "P" ){
									var vl = this.ce__("p");
									vl.innerHTML= "&nbsp;";
									v.replaceWith( vl );
								}
							}else{
								console.log( "it is not br");
							}
						}else{
							console.log("no previous eleemnt");
						}
					}else{
						console.log( "Check after enter 2: " );
						console.log( sr );
					}
				}else{
					console.log( "Check after enter 3: " );
					console.log( sr );
				}
				this.set_focused__();
				return false;
				if( this.focused__.nodeName == "DIV" ){
					if( this.focused__.hasAttribute("data-id") ){
						if( this.focused__.getAttribute("data-id") == "root" ){
							return false;
						}
					}
					var vl = this.ce__("p");
					vl.innerHTML = this.focused__.innerHTML;
					this.focused__.replaceWith(vl);
				}
			},
			keydown_iframe__: function(e){
				this.echo__("keydown_iframe__"+e.keyCode);
				if( e.keyCode == 27 ){ // escape
					this.hide_other_menus__();
					//this.insert_tag__ =false;
				}
			},
			keydown_outside__: function(e){if( this.enabled__ ){
				if( e.keyCode == 27 ){ // escape
					return false;
				}
			}},
			keydown__: function(e){if( this.enabled__ ){
				//this.echo__("keydown__: "+e.keyCode);
				this.side_settings_style__ = "visibility:hidden;";
				if( e.keyCode == 27 ){ // escape
					//this.insert_tag__ =false;
					this.hide_other_menus__();
					return false;
				}
				if( e.keyCode == 73 && e.ctrlKey ){ // ctrl + i
					this.make_link__();
					e.preventDefault();
					e.stopPropagation();
					return false;
				}
				if( e.keyCode == 67 && e.ctrlKey ){ // ctrl + C  on table cells__
					if( this.focused_table__ && this.td_sel_cnt__ > 1 ){
						this.table_cells_copy__();
						return;
					}else{
						console.log("no sections selection for copy");
					}
				}
				if( e.keyCode == 88 && e.ctrlKey ){  // ctrl + X  on table cells__
					if( this.focused_table__ && this.td_sel_cnt__ > 1 ){
						e.preventDefault();e.stopPropagation();
						this.table_cells_copy__();
						this.table_cells_delete__();
						return;
					}else if( this.sections_selected__ ){
						return false;
					}else{
						console.log("no sections selection for cut");
					}
				}
				this.set_focused__("fromkeydown__");
				if( ["F5"].indexOf( e.key ) > -1 ){
					return false;
				}
				if( this.focused__.hasAttribute("data-block-type") ){
					if( [33,34,35,36,37,38,39,40,9].indexOf( e.keyCode ) == -1 ){
						e.preventDefault();e.stopPropagation();
					}
				}
				try{
				if( this.focused__.className.match(/^(image|note1|note2|note3)$/) ){
					if( [33,34,35,36,37,38,39,40,9].indexOf( e.keyCode ) == -1 ){
						e.preventDefault();e.stopPropagation();
					}
				}
				if( this.focused__.className.match(/^(image_caption)$/) ){
					if( [13,10,9].indexOf( e.keyCode ) != -1 || e.ctrlKey ){
						e.preventDefault();e.stopPropagation();
					}
				}
				}catch(e){}
				//console.log( "keydown__: " + this.focused__.nodeName + ": " +  e.keyCode + (e.ctrlKey?" CTRLS ":"") + (e.shiftKey?" Shift ":"") );
				if( this.focused__.nodeName == "PRE" ){
					this.pre_keydown2__(e);
					return false;
				}
				if( e.keyCode == 8 ){ //backspace
					if( this.focused__.nodeName == "P" || this.focused__.nodeName == "DIV" ){
						if( this.focused__.innerHTML.trim().toLowerCase() == "<br>" || this.focused__.innerHTML.trim() == "" || this.focused__.innerHTML.trim() == "&nbsp;" ){
							e.preventDefault();
							this.focused__.remove();
						}
					}
					setTimeout(this.set_focused__,200);
				}
				if( e.keyCode == 46 ){ //delete
					if( this.td_sel_cnt__ > 1 ){
						e.preventDefault();
						this.table_cells_delete__();
					}else if( this.sections_list__.length > 1 ){
						return false;
						e.preventDefault();
						if( confirm("Are you sure to delete?") ){
							this.selection_to_clipboard__();
							this.selection_to_delete__();
						}
					}else{
						if( this.focused__.nodeName == "P" || this.focused__.nodeName == "DIV" ){
							if( this.focused__.innerHTML.trim().toLowerCase() == "<br>" || this.focused__.innerHTML.trim() == "" || this.focused__.innerHTML.trim() == "&nbsp;" ){
								e.preventDefault();
								this.focused__.remove();
							}
						}
					}
					setTimeout(this.set_focused__,200);
				}
				if( e.keyCode == 67 && e.ctrlKey ){ // ctrl + C
					if( this.focused_table__ && this.td_sel_cnt__ > 1 ){
						this.table_cells_copy__();
					}else{
						console.log("no sections selection for copy");
					}
				}
				if( e.keyCode == 88 && e.ctrlKey ){  // ctrl + X
					if( this.focused_table__ && this.td_sel_cnt__ > 1 ){
						e.preventDefault();e.stopPropagation();
						this.table_cells_copy__();
						this.table_cells_delete__();
					}else if( this.sections_selected__ ){
						return false;
					}else{
						console.log("no sections selection for cut");
					}
				}
				if( e.keyCode == 86 && e.ctrlKey ){ // ctrl + V paste
					//this.onpaste__(e);
					if( e.shiftKey ){
						this.paste_shift__ = true;
					}
				}
				if( e.keyCode == 9 ){ // tab
					if( this.sections_selected__ ){
						e.preventDefault();
						if( e.shiftKey ){
							this.make_unindent__();
						}else{
							this.make_indent__();
						}
					}else{ // tab in editable
						console.log( "tab in editable: " + this.focused__.nodeName );
						e.preventDefault();
						if( this.focused_li__ ){
							if( e.shiftKey ){
								var li = this.focused_li__;
								var ul = li.parentNode;
								var parent = ul.parentNode;
								var parent_li = false;
								if( parent.nodeName == "LI" ){
									parent_li = parent;
								}else if( parent.paretNode.nodeName == "LI" ){
									parent_li = parent.parentNode;
								}
								if( parent_li ){
										if( li.previousElementSibling == null ){
											parent_li.insertAdjacentElement("afterend", li);
											if( ul.children.length == 0 ){
												ul.remove();
											}else{
												li.appendChild( ul );
											}
											this.select_range___with_element__( li );
										}else if( li.nextElementSibling == null ){
											parent_li.insertAdjacentElement("afterend", li);
											if( ul.children.length == 0 ){
												ul.remove();
											}
											this.select_range___with_element__( li );
										}else{
											var vi = Array.from(ul.children).indexOf( li );
											parent_li.insertAdjacentElement("afterend", li);
											if( (Number(ul.children.length)-1) >= vi ){
												var vl = this.ce__("UL");
												while( ul.children.length-1 >= vi ){
														vl.appendChild( ul.children[vi] );
												}
												li.appendChild( vl );
											}
										}
								}else{

								}
							}else{
								if( this.focused_li__.previousElementSibling ){
									var k = this.focused_li__;
									var kv = this.focused_li__.previousElementSibling;
									var f = false;
									if( kv.childNodes[ kv.childNodes.length-1 ].nodeName == "#text" ){
										if( kv.childNodes.length > 1 ){
											if( kv.childNodes[ kv.childNodes.length-2 ].nodeName == "UL" ){
												var kv = kv.childNodes[ kv.childNodes.length-2 ];
												f = true;
											}
										}
									}else if( kv.childNodes[ kv.childNodes.length-1 ].nodeName == "UL" ){
										var kv = kv.childNodes[ kv.childNodes.length-1 ];
										f = true;
									}
									if( f ){
										kv.appendChild( k );
										this.select_range___with_element__( k );
									}else{
										var vul = this.ce__("ul");
										this.focused_li__.previousElementSibling.appendChild( vul );
										vul.appendChild( k );
										this.select_range___with_element__( k );
									}
								}
							}
						}else if( this.focused_td__ ){
							if( e.shiftKey ){
								if( this.focused_td__.previousElementSibling ){
									this.select_range___with_element__( this.focused_td__.previousElementSibling );
								}else{
									if( this.focused_tr__.previousElementSibling ){
										this.select_range___with_element__( this.focused_tr__.previousElementSibling.children[ this.focused_tr__.previousElementSibling.children.length-1 ] );
									}
								}
							}else{
								if( this.focused_td__.nextElementSibling ){
									this.select_range___with_element__( this.focused_td__.nextElementSibling );
								}else{
									if( this.focused_tr__.nextElementSibling ){
										this.select_range___with_element__( this.focused_tr__.nextElementSibling.children[0] );
									}else{
										var cnt = this.focused_tr__.children.length;
										var vtr = this.ce__("tr");
										for(var i=0;i<cnt;i++){
											var vtd = this.ce__("td");
											vtr.appendChild(vtd);
										}
										this.focused_tr__.insertAdjacentElement("afterend",vtr);
										this.select_range___with_element__( vtr.children[0] );
										setTimeout(this.initialize_tables__,100);
									}
								}
							}
							//setTimeout(this.initialize_tables__,100);
						}else{
							console.log("Tab unhandled:");
						}
					}
					return false;
				}
				if( (e.keyCode == 13 || e.keyCode == 10) && e.shiftKey == true ){
					this.set_focused__("fromkeydown__");
				}
				if( e.keyCode == 13 && e.shiftKey == false ){
					if( this.focused__.className.match(/^(image_caption|image|note1|note2|note3)$/) ){
						e.preventDefault();e.stopPropagation();
					}else{
						setTimeout(this.check_after_enter__,50);
					}
				}
			}},
			keyup__: function(e){if( this.enabled__ ){
				setTimeout(this.keyup2__,10,e);
			}},
			keyup2__: function(e){
				// top , bottom, left, right
				//console.log( "keyup2__:" + e.keyCode );
				if( e.keyCode == 38 || e.keyCode == 40 || e.keyCode == 37 || e.keyCode == 39 ){
					setTimeout(this.selectionchange2__,50)
				}else{
					this.set_focused__( this.focused__ );
				}
			},
			selected_lis_to_indent__: function( shiftkey ){
				var is_all_lis__ = true;
				for(var i=0;i<this.sections_list__.length;i++){
					if( this.sections_list__[i].nodeName == "#text" ){
						if( this.sections_list__[i].data.match(/^[\t\r\n]+$/) ){
							this.sections_list__[i].remove();
							this.sections_list__.splice(i,1);
							i--;
							continue;
						}
					}
					if( this.sections_list__[i].nodeName != "LI" ){
						is_all_lis__ = false;
					}
				}
				if( is_all_lis__ ){
					{
						var lis = this.sections_list__;
						var parent_ul__ = lis[0].parentNode;
						var parent_section__ = parent_ul__.parentNode;
						if( shiftkey == true ){
							if( lis[0].previousElementSibling == null && lis[ lis.length-1 ].nextElementSibling == null ){
								//it is total UL
								this.ul_change_to_text__();
							}else if( lis[ lis.length-1 ].nextElementSibling == null ){
								var nodelist__ = [];
								if( parent_section__.nodeName == "LI" ){
									for( var i=0;i<lis.length;i++){
										var kv = lis[i];
										parent_section__.insertAdjacentElement( "afterend", kv );
										parent_section__ = kv;
										nodelist__.push( kv );
									}
								}else{
									for( var i=0;i<lis.length;i++){
										var vl = this.ce__("p");
										vl.innerHTML = lis[i].innerHTML;
										lis[i].remove();
										parent_ul__.insertAdjacentElement( "afterend", vl );
										parent_ul__ = vl;
										nodelist__.push( vl );
									}
								}
								this.select_elements__( nodelist__ );
								setTimeout(this.selectionchange2__,50);
							}else if( lis[ 0 ].previousElementSibling == null ){
								//need to insert an UL between li elements
								var nodelist__ = [];
								if( parent_section__.nodeName == "LI" ){
									while( lis.length ){
										var kv = lis[0];
										parent_section__.insertAdjacentElement( "afterend", kv );
										parent_section__ = kv;
										nodelist__.push( kv );
										lis.splice(0,1);
									}
									parent_section__.appendChild( parent_ul__ );
								}else{
									for( var i=0;i<lis.length;i++){
										var vl = this.ce__("p");
										vl.innerHTML = lis[i].innerHTML;
										lis[i].remove();
										parent_ul__.insertAdjacentElement( "afterend", vl );
										parent_ul__ = vl;
										nodelist__.push( vl );
									}
								}
								this.select_elements__( nodelist__ );
								setTimeout(this.selectionchange2__,50);
							}else{
								var nodelist__ = [];
								for( var i=0;i<lis.length;i++){
									nodelist__.push( lis[i] );
								}
								var newul__ = this.ce__("UL");
								while( lis[ lis.length-1 ].nextElementSibling ){
									newul__.appendChild( lis[ lis.length-1 ].nextElementSibling );
								}
								//parentul.insertAdjacentElement("afterend", newel);
								var new_nodes = [];
								if( parent_section__.nodeName == "LI" ){
									while( nodelist__.length ){
										parent_section__.insertAdjacentElement("afterend", nodelist__[0]);
										parent_section__ = nodelist__[0];
										new_nodes.push( nodelist__[0] );
										nodelist__.splice(0,1);
									}
									parent_section__.appendChild( newul__ );
								}else{
									while( nodelist__.length ){
										var vl = this.ce__("p");
										vl.innerHTML = nodelist__[0].innerHTML;
										nodelist__[0].remove();
										parent_ul__.insertAdjacentElement("afterend", vl);
										parent_ul__ = vl;
										new_nodes.push( vl );
										nodelist__.splice(0,1);
									}
									parent_ul__.insertAdjacentElement("afterend", newul__);
								}
								this.select_elements__( new_nodes );
								setTimeout(this.selectionchange2__,50);
							}
						}else{
							if( lis[0].previousElementSibling ){
								var prev_li = lis[0].previousElementSibling;
								var f = false;
								if( prev_li.childNodes[ prev_li.childNodes.length-1 ].nodeName == "#text" ){
									if( prev_li.childNodes.length > 1 ){
										if( prev_li.childNodes[ prev_li.childNodes.length-2 ].nodeName == "UL" ){
											prev_li = prev_li.childNodes[ prev_li.childNodes.length-2 ];
											f = true;
										}
									}
								}else if( prev_li.childNodes[ prev_li.childNodes.length-1 ].nodeName == "UL" ){
									prev_li = prev_li.childNodes[ prev_li.childNodes.length-1 ];
									f = true;
								}
								if( f ){
									var new_ul = prev_li;
								}else{
									var new_ul = this.ce__("ul");
									prev_li.appendChild( new_ul );
								}
								for( var i=0;i<lis.length;i++){
									new_ul.appendChild( lis[i] );
								}
								this.select_sections__(lis);
								setTimeout(this.selectionchange2__,50);
							}else{
								console.log("Selection start has no previous LI element");
							}
						}
					}
				}else{
					console.log("selection has non LI elements");
				}
			},
			selection_to_clipboard__: function(){
				var sr = document.getSelection().getRangeAt(0);
				console.log( "Copy: " + sr.commonAncestorContainer.nodeName );
				var h = sr.cloneContents();
				var vnn = this.ce__("div");
				vnn.appendChild( h );
				//delete( vnn );
				const blob = new Blob([vnn.innerHTML], { type: "text/html" });
				var txt = vnn.innerText;
				var txt2 = "";
				for(var i=0;i<txt.length;i++){ if( txt.charCodeAt(i) < 126 ){ txt2 = txt2+ txt.substr(i,1); } }
				const blob2 = new Blob([txt2], { type: "text/plain" });
				const richTextInput__ = new ClipboardItem({ "text/html": blob, "text/plain": blob2 });
				navigator.clipboard.write([richTextInput__]);
				console.log("Selection copied");
			},
			selection_to_delete__: function(){
				var sr = document.getSelection().getRangeAt(0);
				sr.deleteContents();
				this.unset_focused__();
			},
			find_root_sections_list__: function(){
				var sr = document.getSelection().getRangeAt(0);
				var v = sr.startContainer;
				var start_vid = "";
				var cnt = 0;
				while( 1 ){
					if( cnt > 20 ){console.log("loop1 end");return false;}cnt++;
					if( v.nodeName != "#text" ){
					if( v.parentNode.hasAttribute("data-id") ){
						if( v.parentNode.getAttribute("data-id", "root") ){
							start_vid = v;
							break;
						}
					}
					}
					v = v.parentNode;
				}
				var v = sr.endContainer;
				var end_vid = "";
				var cnt = 0;
				while( 1 ){
					if( cnt > 20 ){console.log("loop2 end");return false;}cnt++;
					if( v.nodeName != "#text" ){
					if( "hasAttribute" in v.parentNode == false ){
						return false;
					}else{
						if( v.parentNode.hasAttribute("data-id") ){
							if( v.parentNode.getAttribute("data-id", "root") ){
								end_vid = v;
								break;
							}
						}
					}
					}
					v = v.parentNode;
				}
				var vids = [];
				vids.push( start_vid.id );
				var v = start_vid.nextElementSibling;
				var cnt = 0;
				if( start_vid != end_vid ){
					while( v != end_vid ){
						if( v.nodeName != "#text" ){
							vids.push( v.id );
						}
						if( v.nextElementSibling ){
							v = v.nextElementSibling;
						}else{
							console.log( v );
							console.log( "loop3 error" );
							return false;
						}
						if( cnt > 20 ){console.log("loop3 end");return false;}
						cnt++;
					}
				}
				vids.push( end_vid.id );
				return vids;
			},
			move_focused__: function( vi ){

			},
			change_focused__: function( v ){

			},
			clickdoc__: function(e){if( this.enabled__ ){
				setTimeout(this.clickdoc2__,100,e);
			}},
			clickdoc2__: function(e){
				var pos = "document";
				var v = e.target;
				while( 1 ){
					try{
						if( v.nodeName == "#text" ){
							v = v.parentNode;
						}else{
							if( v.nodeName == "HTML" || v.nodeName == "BODY" ){
								break;
							}
							if( v.hasAttribute("data-id") ){
								pos = v.getAttribute("data-id");
								break;
							}
							v = v.parentNode;
						}
					}catch(e){
						return;
					}
				}
				if( pos == "document" ){
					if( this.contextmenu__ ){
						this.hide_contextmenu__();
					}
					this.unset_focused__();
				}
			},
			editor_scroll__: function(e){
				this.hide_bounds__();
				this.hide_contextmenu__();
				this.hide_other_menus__();
			},
			clickit__: function( e ){if( this.enabled__ ){
				//this.insert_tag__ = false;
				e.preventDefault();
				e.stopPropagation();
				this.hide_contextmenu__();
				this.anchor_at_range__ = false;
				console.log("clickit__: ");
				console.log(e.target);
				var sel = document.getSelection();
				if( sel.rangeCount ){
					var sr = sel.getRangeAt(0);
					if( sr.collapsed == false ){
						return false;
					}
				}
				this.hide_other_menus__();
				this.insert_tag__ = true;
				if( e.target.nodeName == "IMG" ){
					this.set_focused__( e.target );
				}else{
					this.set_focused__();
				}
			}},
			unset_focused__: function(vexcept = ''){
				this.focused_tree__ = [];
				this.focused__= false;
				this.focused_type__= "";
				this.focused_block_type__= "";
				this.focused_block__= false;
				this.focused_table__= false;
				this.focused_anchor__= false;
				this.focused_td__= false;
				this.focused_tr__= false;
				this.focused_table__= false;
				this.focused_li__= false;
				this.focused_ul__= false;
				this.focused_img__= false;
				this.focused_anchor__= false;
				this.hide_bounds__(vexcept);
			},
			hide_bounds__: function(vexcept=''){
				this.focused_bounds_style__= "visibility: hidden;";
				this.side_settings_style__="visibility: hidden;";
				if( vexcept != 'table' ){
					this.table_cell__=false;
					this.table_cell_style__="";
				}
				this.insert_tag__ = false;
			},
			set_focused_className__: function(){
				var v = this.focused_className__.split(/\ /i);
				//this.focused___class
			},
			set_focused__: function( vtarget__ = false ){
				if( this.td_sel_cnt__ > 1 ){
					this.focused_tds_set_bounds__();
				}else{
					var vfromkeydown__ = false;
					if( vtarget__ == "fromkeydown__" ){
						vfromkeydown__ = true;
						vtarget__ = false;
					}
					if( vtarget__ == false ){
						if( document.getSelection().rangeCount == 0 ){
							this.hide_other_menus__();
							return false;
						}
						var sr = document.getSelection().getRangeAt(0);
						var v = false;
						if( sr.startContainer.nodeName == "#text" ){
							v = sr.startContainer.parentNode;
						}else{
							v = sr.startContainer;
						}
					}else{
						v = vtarget__;
						if( v.nodeName == "#text" ){
							v = v.parentNode;
						}
					}
					if( this.td_sel_start__ == false && this.td_sel_cnt__ < 2 ){
						this.set_focused2__( v, vfromkeydown__ );
					}
				}
			},
			set_focused2__: function( v, vfromkeydown__=false ){
				var is_sel__ = false;
				var cnt = 0;
				while( 1 ){
					cnt++;if( cnt>3 ){console.error("focuselement + 3");break;}
					try{
						if( v.nodeName.match(/^(BLOCKQUOTE|P|H1|H2|H3|H4|DIV|UL|OL|LI|TABLE|TBODY|TFOOT|THEAD|TH|TD|TR)$/i) ){
							break;
						}
						v = v.parentNode;
					}catch(e){
						console.error("set_focused2__: " + e);
						console.log( v );
						return ;
					}
				}
				if( vfromkeydown__ ){
					if( this.focused__ == v ){
						setTimeout(this.focused_block_set_bounds__,50);
						return false;
					}
				}
				if( v.hasAttribute("data-id") ){
					if( v.getAttribute("data-id") == "root" ){
						console.log( "root element");
						for( var i=0;i<v.childNodes.length;i++ ){
							if( v.childNodes[i].nodeName == "#text" ){
								var vl = this.ce__("p");
								if( v.childNodes[i].nodeValue.trim() != "" ){
									vl.innerHTML = v.nodeValue;
									v.childNodes[i].remove();
									v.appendChild( vl );
								}
							}else if( v.childNodes[i].nodeName == "BR" ){
								v.childNodes[i].outerHTML = "<p>Initial Paragraph</p>";
							}
						}
						//return ;						
						var v = this.gt__("editor_div");
						if( v.childNodes.length == 0 ){
							var vl = this.ce__("p");
							vl.innerHTML = "Initial Paragraph";
							v.appendChild( vl );
							this.focused__ = vl;
						}else{
							this.focused__ = v.childNodes[0];
						}
						// this.select_range___with_element__( v.childNodes[0] );
						// this.selectionchange2__();
					}
				}
				{
					this.unset_focused__();
					this.insert_tag__ = true;

					var nodename__ = v.nodeName+'';
					if( v.hasAttribute("class") ){
						var c = v.getAttribute("class");
						if( c.match(/gridcol/i) && c.match(/col\-/i) ){
							nodename__ = "GridColumn";
						}
						if( c.match(/grid/i) && c.match(/row/i) ){
							nodename__ = "GridRow";
						}
						if( c.match(/callouticon/i) ){
							
						}
						if( c.match(/calloutdata/i) ){
							
						}
					}
					if( v.nodeName == "BLOCKQUOTE" ){
						nodename__ = "Quote";
					}

					this.focused_tree__.push({
						"a":nodename__,
						"v":v, 
						"c":v.className,
						"b":(v.hasAttribute("data-block-type")?v.getAttribute("data-block-type"):"") 
					});
					this.focused__ = v;
					this.focused_type__ = this.focused__.nodeName;
					if( this.focused__.nodeName == "IMG" ){
						this.focused_img__ = this.focused__;
						this.image_url = this.focused_img__.src+'';
					}
					if( this.focused__.nodeName == "A" ){
						this.focused_anchor__ = this.focused__;
					}
					if( this.focused__.nodeName == "TD" || this.focused__.nodeName == "TH" ){
						this.focused_td__ = this.focused__;
						this.focused_tr__ = this.focused__.parentNode;
						this.focused_table__ = this.focused_tr__.parentNode.parentNode;
						setTimeout(this.td_settings_read,50);
						setTimeout(this.table_settings_read__,50);
					}
					if( this.focused__.nodeName == "TR" ){
						this.focused_tr__ = this.focused__;
						this.focused_table__ = this.focused_tr__.parentNode.parentNode;
						setTimeout(this.table_settings_read__,50);
					}
					if( this.focused__.nodeName == "TBODY" || this.focused__.nodeName == "THEAD" || this.focused__.nodeName == "TFOOT" ){
						this.focused_table__ = this.focused__.parentNode;
						setTimeout(this.table_settings_read__,50);
					}
					if( this.focused__.nodeName == "TABLE"  ){
						this.focused_table__ = this.focused__;
						setTimeout(this.table_settings_read__,50);
					}
					if( this.focused__.nodeName == "LI" ){
						this.focused_li__ = this.focused__;
						this.focused_ul__ = this.focused__.parentNode;
						this.ul_type__ = (this.focused_ul__.className?this.focused_ul__.className:"list-style-disc");
					}
					this.focused_block__ = false;
					this.focused_block_type__ = "";
					var v = this.focused__.parentNode;
					if( v != null ){
					var cnt=0;
					while(1){
						cnt++;if(cnt>4){break;}
						if( "hasAttribute" in v == false ){break;}
						if( v.hasAttribute("data-id") ){
							break;
						}
						var nodename__ = v.nodeName+'';
						if( v.hasAttribute("class") ){
							var c = v.getAttribute("class");
							if( c.match(/gridcol/i) && c.match(/col\-/i) ){
								nodename__ = "GridColumn";
							}
							if( c.match(/grid/i) && c.match(/row/i) ){
								nodename__ = "GridRow";
							}
							if( c.match(/callouticon/i) ){
								continue;
							}
							if( c.match(/calloutdata/i) ){
								continue;
							}
						}
						if( v.nodeName == "BLOCKQUOTE" ){
							nodename__ = "Quote";
						}

						this.focused_tree__.push({
							"a":nodename__,
							"v":v, 
							"c":v.className, 
							"b":(v.hasAttribute("data-block-type")?v.getAttribute("data-block-type"):"") 
						});
						if( v.nodeName.match( /^(TD|TH)$/ ) ){
							if( this.focused_td__ == false ){
								this.focused_td__ = v;
								this.focused_tr__ = v.parentNode;
								this.focused_table__ = v.parentNode.parentNode.parentNode;
							}
						}
						if( v.nodeName.match( /^(LI)$/ ) ){
							if( this.focused_li__ == false ){
								this.focused_li__ = v;
								this.focused_ul__ = v.parentNode;
								this.ul_type__ = (this.focused_ul__.className?this.focused_ul__.className:"list-style-disc");
							}
						}
						if( v.hasAttribute("data-type") ){
							if( v.getAttribute("data-type") ){
								this.focused_block__ = v;
								this.focused_block_type__ = v.getAttribute("data-type");
							}
						}
						v = v.parentNode;
					}
					}
				}
				if( is_sel__ == false ){
					this.focused_block_set_bounds__();
				}
			},
			focused_block_set_bounds__: function(){
				if( this.focused__.hasAttribute("data-id") ){
					if( this.focused__.getAttribute("data-id") == "root" ){
						this.focused_bounds_style__ = "visibility:hidden;";
						return false;
					}
				}
				var v = this.focused__.getBoundingClientRect();
				var sy = window.scrollY; //Number(scrollY);
				var sx = window.scrollX; //Number(scrollX);
				var l=Number(v.left);var t=Number(v.top); var w=Number(v.width); var h=Number(v.height); var b=Number(v.bottom); var r=Number(v.right);
				this.focused_bounds_style__ = "top:"+(t+sy)+"px;left:"+(l+sx)+"px;width:"+(w)+"px;height:"+(h)+"px";
			},
			anchor_remove__: function(){
				this.focused_anchor__.outerHTML = this.focused_anchor__.innerHTML;
				this.unset_focused__();this.hide_contextmenu__();
			},
			pre_keydown2__: function(e){
				if( e.keyCode == 13 || e.keyCode == 10 ){
					e.preventDefault();e.stopPropagation();
					setTimeout(this.pre_enter_indent2__,50,e);
				}
				if( e.keyCode == 66 && e.ctrlKey ){
					e.preventDefault();
					return false;
				}
				if( e.keyCode == 9 ){
					e.preventDefault();e.stopPropagation();
					var sr = document.getSelection().getRangeAt(0);
					if( sr.startContainer.nodeName == "#text" && sr.endContainer.nodeName == "#text" && sr.startContainer == sr.endContainer ){
						var sc = sr.startContainer;
						var st = sr.startOffset;
						var en = sr.endOffset;
						if( (en-st) == 0 ){
							var t1 = sc.nodeValue.substr(0,st);
							var t2 = sc.nodeValue.substr(st,99999);
							sc.data = t1 + "\t" + t2;
							var sr = new Range();
							sr.setStart(sc, st+1);
							sr.setEnd(sc, st+1);
							var sel = document.getSelection();
							sel.removeAllRanges();
							sel.addRange(sr);
						}else{
							var txt = sr.startContainer.nodeValue;
							var t = txt.substr(st, (en-st) );
							var l = t.split(/[\r\n]+/g);
							for(var i=0;i<l.length;i++){
								if( e.shiftKey ){
									l[i] = l[i].replace(/(\t|[\ ]{1,4})/, "");
								}else{
									l[i] = "\t" + l[i];
								}
							}
							var newtext = l.join('\n');
							var d = newtext.length-t.length;
							en = en + d;
							var sr = document.getSelection().getRangeAt(0);
							var sc = sr.startContainer;
							sc.data = txt.replace(t, newtext);
							var sr = new Range();
							sr.setStart(sc, st);
							sr.setEnd(sc, en);
							var sel = document.getSelection();
							sel.removeAllRanges();
							sel.addRange(sr);
						}
					}
				}
			},
			pre_enter_indent2__: function( e ){
				var sr = document.getSelection().getRangeAt(0);
				if( sr.startContainer.nodeName =="#text" && sr.endContainer.nodeName =="#text" && sr.startContainer == sr.endContainer ){
					var st = sr.startOffset;
					var en = sr.endOffset;
					var sc = sr.startContainer;
					var tb = sc.nodeValue.substr(0, st);
					var tb2 = sc.nodeValue.substr(st, 999999);
					var l = tb.split(/[\r\n]+/g);
					var ml= 0;
					if( l.length > 0 ){
						var m = l[ l.length-1 ].match(/^[\t\ ]+/);
						if( m ){
							sc.data = tb + "\n" + m[0] + tb2;
							ml = m[0].length;
						}else{
							sc.data = tb + "\n" + tb2;
							ml = 0;
						}
					}else{
						sc.data = tb + "\n" + tb2;
						ml = 1;
					}
					var sr = document.getSelection().getRangeAt(0);
					var sc = sr.startContainer;
					var sr = new Range();
					var e2 = st+ml+1;
					if( sc.nodeValue.length < e2 ){
						e2 = sc.nodeValue.length-1;
					}
					sr.setStart( sc, e2 );
					sr.setEnd( sc, e2 );
					var sel = document.getSelection();
					sel.removeAllRanges();
					sel.addRange(sr);
				}
			},
			pre_insert_text__: function(e,d){
				var sr = document.getSelection().getRangeAt(0);
				var sc = sr.startContainer;
				var st = sr.startOffset;
				var en = sr.endOffset;
				if( sc.nodeName == "PRE" ){
					sc.innerHTML = d;
				}else{
					var t1 = sc.nodeValue.substr(0,st);
					var t2 = sc.nodeValue.substr(en,99999);
					sc.data = t1 + d + t2;
					var sr = new Range();
					sr.setStart(sc, st);
					sr.setEnd(sc, st+d.length);
					var sel = document.getSelection();
					sel.removeAllRanges();
					sel.addRange(sr);
					e.target.focus();
				}
			},
			clean_html__: function(h){
				h = h.replace(/contenteditable[\=\Wtrue]+/g, " ");
				h = h.replace(/data\-focused__[\=\Wtrue]+/g, " ");
				return h;
			},
			clipboard_find_table__: function(){
				try{
					cp = window.clipboardData;
					var types = {};
					for( var i=0;i<cp.items.length;i++ ){
						types[ cp.items[i].type ] = i;
					}
					if( "text/html" in types ){
						var d = cp.getData('text/html');
						var d = this.clean_html__( cleanpasted( d ) );
						var vsections = check_article_body_parts( d );
						if( vsections.length==1 && vsections[0].substr(0,6).toLowerCase() == "<table" ){
							var vs = vsections[0].replace(/[\r\n\t]+/g,"");
							var newl = this.ce__("div");
							newl.innerHTML = vs;
							newtable = newl.children[0];
							return newtable;
						}
					}
				}catch(e){
					console.error("finding clipboard: " + e)
				}
				return false;
			},
			onpaste__: function( e ){
				cp = e.clipboardData || window.clipboardData;
				if( this.paste_shift__ ){
					if( e.target.hasAttribute("contenteditable" ) ){
						e.preventDefault();
						console.log("Shift Ctrl V : pasting as text");
						var d= cp.getData("Text");
						document.execCommand("insertText", false,d );
						this.paste_shift__ = false;
						return false;
					}
				}
				var types = {};
				for( var i=0;i<cp.items.length;i++ ){
					types[ cp.items[i].type ] = i;
				}
				this.echo__(types);
				if( e.target.nodeName == "PRE" ){
					var d = cp.getData('Text');
					e.preventDefault();
					this.pre_insert_text__(e, d );
				}else{
					var is_img_found__ = false;
					if( "image/jpeg" in types ){
						is_img_found__ = "image/jpeg";
					}else if( "image/svg+xml" in types ){
						is_img_found__ = "image/svg+xml";
					}else if( "image/png" in types ){
						is_img_found__ = "image/png";
					}
					if( "text/html" in types ){
						var d = cp.getData('text/html');
						var d = this.clean_html__( cleanpasted( d ) );
						var vsections = check_article_body_parts( d );
						var v1 = e.target;
						if( v1.nodeName == "#text" ){
							v1 = v1.parentNode;
							if( v1.hasAttribute("data-id") ){
								if( v1.getAttribute("data-id") == "root" ){
									v1 = e.target.previousElementSibling;
								}
							}
						}
						if( v1.nodeName.match(/^(P|LI|TD|TH|PRE|DIV|H1|H2|H3|H4|H5|TABLE)$/) == null ){
							v1 = v1.parentNode;
						}
						if( v1.nodeName.match(/^(P|LI|TD|TH|PRE|DIV|H1|H2|H3|H4|H5|TABLE)$/) == null ){
							v1 = v1.parentNode;
						}
						var isitli = (v1.nodeName == "LI"?v1:false);
						if( isitli == false ){
							isitli = (v1.parentNode.nodeName == "LI")?v1.parentNode:false;
						}
						var isittd = (v1.nodeName.match(/^(TD|TH)$/)?v1:false);
						if( isittd == false ){
							isittd = (v1.parentNode.nodeName.match(/^(TD|TH)$/))?v1.parentNode:false;
						}
						var iscontentli = false;
						if( vsections.length==1 && vsections[0].match(/[\>\<]/) == null ){
							console.log("skipping to natural paste");
							e.preventDefault();
							e.stopPropagation();
							document.execCommand("insertText", false, vsections[0]);
							return false;
						}
						e.preventDefault();
						this.echo__( vsections );
						if( vsections.length==1 && vsections[0].substr(0,3).toLowerCase() == "<ul" ){
							var vnew_ul = this.ce__("div");
							vnew_ul.innerHTML = vsections[0].replace(/[\r\n\t]+/g,"");;
							var vnew_lis = vnew_ul.getElementsByTagName("LI");
							for(var i=0;i<vnew_lis.length;i++){
								if( isitli ){
									isitli.insertAdjacentElement( "afterend", vnew_lis[i] );
								}else if( v1.nodeName.match( /^(TD|TH)$/ ) ){
									v1.appendChild( vnew_ul );
								}else{
									v1.insertAdjacentElement( "afterend", vnew_ul );
								}
							}
						}else if( vsections.length==1 && vsections[0].substr(0,6).toLowerCase() == "<table" ){

							var vs = vsections[0].replace(/[\r\n\t]+/g,"");
							var newl = this.ce__("div");
							newl.innerHTML = vs;
							newtable = newl.children[0];
							this.clean_html_table__( newtable );
							this.echo__( newtable.outerHTML );
							console.log( newtable );
							if( this.focused_table__ && this.td_sel_cnt__ > 1 ){
								console.log("66666");
								this.table_cells_paste__(newtable);
							}else if( this.focused_td__ ){
								console.log("33333");
								this.table_cells_paste__(newtable);
								newl.remove();
							}else{
								this.initialize_table__(newtable);
								v1.insertAdjacentElement( "afterend", newtable );
								newl.remove();
							}
						}else{
							for(var i=0;i<vsections.length;i++){
								var newl = this.ce__("p");
								//newl.innerHTML = vsections[i].replace(/[\r\n\t]+/g,"");
								newl.innerHTML = vsections[i];
								if( isittd || isitli ){
									while( newl.childNodes.length ){
										if( newl.childNodes[0].nodeName != "PRE" && newl.childNodes[0].nodeName != "#text" ){
											newl.childNodes[0].innerHTML = newl.childNodes[0].innerHTML.replace( /[\r\n\t]+/g, "" );
										}
										if( newl.childNodes[0].nodeName.match(/^(UL|OL|P|DIV|H1|H2|H3|H4|PRE)$/) ){
											var v2 = newl.childNodes[0];
											v1.appendChild( v2 );
										}else if( newl.childNodes[0].nodeName.match(/^(A)$/) ){
											var v2 = newl.childNodes[0];
											var vln = this.ce__("p");
											vln.appendChild( newl.childNodes[0] );
											v1.appendChild( vln );
										}else if( newl.childNodes[0].nodeName == "#text" ){
											if( newl.childNodes[ 0 ].data.trim() != "" ){
												newl.childNodes[ 0 ].data = newl.childNodes[ 0 ].data.trim();
												var v2 = this.ce__("p");
												v2.appendChild( newl.childNodes[0] );
												v1.appendChild( v2 );
											}else{
												newl.childNodes[0].remove();
											}
										}else if( newl.childNodes[0].nodeName == "IMG" ){
											var v2 = this.get_image_html(newl.childNodes[0]);
											v1.appendChild( v2 );
										}else if( newl.childNodes[0].nodeName == "TABLE" ){
											var newtable = newl.childNodes[0];
											this.clean_html_table__( newtable );
											v1.appendChild( newtable );
											this.initialize_table__( newtable );
										}else{
											console.log( "skipping pasting unknown tag" );
											console.log( newl.childNodes[0] );
											newl.childNodes[0].remove();
										}
									}
								}else{
									while( newl.childNodes.length ){
										if( newl.childNodes[0].nodeName != "PRE" && newl.childNodes[0].nodeName != "#text" ){
											newl.childNodes[0].innerHTML = newl.childNodes[0].innerHTML.replace( /[\r\n\t]+/g, "" );
										}
										if( newl.childNodes[0].nodeName.match(/^(UL|OL|P|DIV|H1|H2|H3|H4|PRE)$/) ){
											var v2 = newl.childNodes[0];
											v1.insertAdjacentElement("afterend", v2 );
											v1 = v2;
										}else if( newl.childNodes[0].nodeName.match(/^(A)$/) ){
											var v2 = newl.childNodes[0];
											var vln = this.ce__("p");
											vln.appendChild( newl.childNodes[0] );
											v1.insertAdjacentElement("afterend", vln );
										}else if( newl.childNodes[0].nodeName == "#text" ){
											if( newl.childNodes[0].data.trim() != "" ){
												newl.childNodes[0].data = newl.childNodes[0].data.trim();
												var v2 = this.ce__("p");
												v2.appendChild( newl.childNodes[0] );
												v1.insertAdjacentElement("afterend", v2 );
												v1 = v2;
											}else{
												newl.childNodes[0].remove();
											}
										}else if( newl.childNodes[0].nodeName == "IMG" ){
											var v2 = this.get_image_html(newl.childNodes[0]);
											v1.insertAdjacentElement("afterend", v2 );
										}else if( newl.childNodes[0].nodeName == "TABLE" ){
											var newtable = newl.childNodes[0];
											this.clean_html_table__( newtable );
											v1.insertAdjacentElement("afterend", newtable );
											this.initialize_table__( newtable );
										}else{
											console.log( "skipping pasting unknown tag" );
											console.log( newl.childNodes[0] );
											newl.childNodes[0].remove();
										}
									}
								}
							}
						}
						//document.execCommand("insertHTML", false, d );
					}else if( is_img_found__ ){
						e.preventDefault();
						var v = e.target;
						var loopcnt = 0;
						var isok = false;
						while(1){loopcnt++;if(loopcnt>20){break;}
							if( v.nodeName.match(/^(#document|BODY|HTML)$/) ){
								isok = false; break;
							}
							if( v.nodeName.match(/^(P|LI|TD|TH|DIV)$/) ){
								if( v.hasAttribute('data-id') ){
									if( v.getAttribute('data-id') == "root" ){
										isok = false;break;
									}
								}
								isok = true;
								break;
							}
							v = v.parentNode;
						}
						if( isok ){
							//var imgdata = cp.getData(is_img_found__);
							//console.log( imgdata );
							if( v.className == "popup_drop"){

							}else{
							this.image_at = v;
							this.image_at_pos = 'b';
							}
							if( is_img_found__ == "image/svg+xml"){
								console.error("Unhandled file type: " + is_img_found__ );
								return false;
							}else{
								var blob = cp.items[ types[is_img_found__] ].getAsFile();
								var reader = new FileReader();
								reader.onload = function(event){
									newapp.image_paste_step2(event.target.result);
								};
								reader.readAsDataURL(blob);
								cp.clearData();
								return false;
							}
							return false;
						}else{
							console.log("Ignored image paste in inside elements");
						}
					}else if( "text/plain" in types ){
						d = cleanpasted( cp.getData('Text') );
						e.preventDefault();
						console.log( d );
						document.execCommand("insertText", false, d );
					}else{
						console.log("Unhandled paste");
					}
					setTimeout(this.initialize_tables__,500);
				}
			},
			clean_html_table__: function( vtable__ ){
				var trs = Array.from(vtable__.children[0].children);
				var max_tds = 0;
				for(var i=0;i<trs.length;i++){
					if( trs[i].nodeName != "TR" ){
						trs[i].remove();
						trs.splice(i,1);
						i--;
					}else{
						var tds = Array.from(trs[i].children);
						for(var j=0;j<tds.length;j++){
							if( tds[j].nodeName != "TD" && tds[j].nodeName != "TH" ){
								tds[j].remove();
								tds.splice(j,1);
								j--;
							}
						}
						if( tds.length > max_tds ){
							max_tds = tds.length;
						}
					}
				}
				for(var i=0;i<trs.length;i++){
					var tds = Array.from(trs[i].children);
					while( tds.length < max_tds ){
						var vtd = this.ce__("td");
						trs[i].appendChild( vtd );
						tds = Array.from(trs[i].children);
					}
				}
			},

			
			find_sections__: function(){
				this.echo__("find_sections__: ");
				this.focused_li__ = false;
				var sel = document.getSelection();
				if( sel.rangeCount ){
					var sr = document.getSelection().getRangeAt(0);
					if( sr.collapsed ){
						console.log("it is collapsed");
						return false;
					}
					var v = sr.commonAncestorContainer;
					if( v.nodeName == "#text" ){
						if( v.parentNode.hasAttribute("contenteditable") ){
							return false;
						}
					}
					if( sr.commonAncestorContainer.nodeName.match(/^(UL|OL)$/) && sr.commonAncestorContainer.nodeName == sr.startContainer.nodeName && sr.commonAncestorContainer.nodeName == sr.endContainer.nodeName && sr.startContainer == sr.endContainer ){
						var vids = [];
						var vlist = Array.from(sr.commonAncestorContainer.childNodes);
						for(var i=0;i<vlist.length;i++){
							if( vlist[i].nodeName == "LI" ){
								vids.push(vlist[i]);
							}
						}
						return vids;
					}else{
						var v = sr.commonAncestorContainer;
						var ev = sr.startContainer;
						var ee = sr.endContainer;
						if( ev == v || ee == v ){
							return false;
						}
						var cnt =0;
						while( 1 ){
							if( cnt > 10){break;return false;}cnt++;
							if( ev.parentNode == v ){
								break;
							}
							ev = ev.parentNode;
						}
						var starti = Array.from(v.childNodes).indexOf( ev );
						var ev = sr.endContainer;
						if( ev.nodeName != "#text" && sr.endOffset == 0 ){
							while( ev.parentNode != v ){
								ev = ev.parentNode;
							}
							if( ev.previousElementSibling ){
								ev = ev.previousElementSibling;
							}else{
								console.error("Error find_sections__: 33333");
								return false;
							}
						}
						var cnt =0;
						while( 1 ){
							if( cnt > 10){break;return false;}cnt++;
							if( ev.parentNode == v ){
								break;
							}
							ev = ev.parentNode;
						}
						var endi =Array.from(v.childNodes).indexOf( ev );
						var vids = [];
						var f = true;
						if( starti > -1 && endi > -1 ){
							for(var i=starti;i<=endi;i++){
								if( v.childNodes[i].nodeName.match(/^(a|abbr|acronym|b|bdo|big|br|cite|code|dfn|kbd|map|output|q|samp|script|small|span|strong|sub|sup|time|tt|var)$/i) == null ){
									vids.push(v.childNodes[i]);
								}
							}
						}
						if( vids.length ){
							var is_all_lis__ = true;
							for(var i=0;i<vids.length;i++){
								if( vids[i].nodeName != "#text" && vids[i].nodeName != "LI" ){
									is_all_lis__ = false;
								}
							}
							this.sections_is_all_lis__ = is_all_lis__;
							var r = new Range();
							r.setStart(vids[0],0);
							r.setEnd(vids[ vids.length-1 ], vids[ vids.length-1 ].childNodes.length );
							document.getSelection().removeAllRanges();
							document.getSelection().addRange(r);
							this.echo__( "found sections:" );
							for(var i=0;i<vids.length;i++){
								console.log( vids[i].outerHTML.substr(0,200) );
							}
							return vids;
						}
						return false;
					}
				}else{
					console.log("find sections non editor");
					console.log( sr );
				}
				return false;
			},
			delete_selection_elements__: function( m ){
				var vels =[];
				var ev = m.startContainer;
				while( 1 ){
					vels.push( ev );
					if( ev == m.endContainer ){
						break;
					}
					ev = ev.nextElementSibling;
				}
				while( vels.length ){
					vels[0].outerHTML = '';
					vels.splice(0,1);
				}
			},
			find_sections_in_html__: function( v ){
				var vl = v.childNodes;
				if( vl.length == 1 && v.childNodes[0].nodeName == "#text" ){
					return false;
				}
				var f = true;
				for( var i=0;i<vl.length;i++){
					if( vl[i].nodeName.match(/^(P|DIV|H1|H2|H3|H4|PRE|TABLE|UL|OL|\#text)$/) == null ){
						console.log("find_sections_in_html__: found: " + vl[i].nodeName );
						f = false;
					}
				}
				if( f ){
					return vl;
				}else{
					return false;
				}
			},
			drop_event__: function(e){
				console.log( "drop event" );
				console.log( e.target );
				e.stopPropagation();
				e.preventDefault();
				if( this.enabled__ ){
					var vfile = e.dataTransfer.files[0];
					if( vfile.type.match(/image/i) ){
						this.image_url = "";
						if( e.target.className == "popup_drop" ){

						}else{
							this.image_at = e.target;
							this.image_at_pos = 't';
						}
						var reader = new FileReader();
						reader.onload = function(event){
							newapp.image_paste_step2(event.target.result);
						};
						reader.readAsDataURL(vfile);
					}else{
					}
				}else{
					alert("editor is not enabled__");
				}
			},

			hide_other_menus__: function(){
				document.body.style.overflow='';
				this.anchor_graph_search_window__ = false;
				this.hide_contextmenu__();
			},
			find_target_editable__: function(vt){
				var cnt = 0;
				var editable_node__ = false;
				var is_it_in_editor__ = false;
				while( 1 ){
					cnt++; if( cnt > 20 ){break;}
					if( vt.nodeName != "#text"  ){
						try{
							if( "getAttribute" in vt == false ){
								break;
							}
							if( vt.getAttribute('data-id') == "root" ){
								is_it_in_editor__ = true;
								break;
							}
							if( vt.nodeName.match(/^(H1|H2|H3|H4|P|TR|TD|TH|PRE|DIV|LI|A|IMG|INPUT|TEXTAREA|SELECT|BUTTON)$/i) ){
								if( editable_node__ == false ){
								editable_node__ = vt;
								}
							}
						}catch(e){ console.log("find_editable"); console.log( e ); console.log( vt ); return false;}
					}
					vt = vt.parentNode;
				}
				if( editable_node__ && is_it_in_editor__ ){
					return editable_node__;
				}
				return false;
			},
			dataURItoBlob__: function(dataURI) {
				var x = dataURI.split(',');
				var byteString = atob(x[1]);
				var mimeString = x[0].split(':')[1].split(';')[0];
				var ab = new ArrayBuffer(byteString.length);
				var ia = new Uint8Array(ab);
				for (var i = 0; i < byteString.length; i++) {
					ia[i] = byteString.charCodeAt(i);
				}
				var blob = new Blob([ab], {type: mimeString});
				return blob;
			},
			
			/*Table Functions*/
			table_settings_read__: function(){if( this.focused_table__ != false ){
					if( this.focused_table__.hasAttribute("data-tb-border") ){
						this.table_settings__['border'] = this.focused_table__.getAttribute("data-tb-border");
					}else{
						this.table_settings__['border'] = "none";
					}
					if( this.focused_table__.hasAttribute("data-tb-spacing") ){
						this.table_settings__['spacing'] = this.focused_table__.getAttribute("data-tb-spacing");
					}else{
						this.table_settings__['spacing'] = "none";
					}
					if( this.focused_table__.hasAttribute("data-tb-hover") ){
						this.table_settings__['hover'] = this.focused_table__.getAttribute("data-tb-hover");
					}else{
						this.table_settings__['hover'] = "none";
					}
					if( this.focused_table__.hasAttribute("data-tb-theme") ){
						this.table_settings__['theme'] = this.focused_table__.getAttribute("data-tb-theme");
					}else{
						this.table_settings__['theme'] = "none";
					}
					if( this.focused_table__.hasAttribute("data-tb-width") ){
						this.table_settings__['width'] = this.focused_table__.getAttribute("data-tb-width");
					}else{
						this.table_settings__['width'] = "none";
					}
					if( this.focused_table__.hasAttribute("data-tb-header") ){
						this.table_settings__['header'] = this.focused_table__.getAttribute("data-tb-header");
					}else{
						this.table_settings__['header'] = "none";
					}
					if( this.focused_table__.hasAttribute("data-tb-footer") ){
						this.table_settings__['footer'] = this.focused_table__.getAttribute("data-tb-footer");
					}else{
						this.table_settings__['footer'] = "none";
					}
					if( this.focused_table__.hasAttribute("data-tb-colheader") ){
						this.table_settings__['colheader'] = this.focused_table__.getAttribute("data-tb-colheader");
					}else{
						this.table_settings__['colheader'] = "none";
					}
					if( this.focused_table__.hasAttribute("data-tb-striped") ){
						this.table_settings__['striped'] = this.focused_table__.getAttribute("data-tb-striped");
					}else{
						this.table_settings__['striped'] = "none";
					}					
				}
			},
			table_toggle_settings__: function(v,e){
				if( this.focused_table__ ){
					if( this.table_settings__[v] == "none" ){
						this.table_settings__[v] = "yes";
					}else{
						this.table_settings__[v] = "none";
					}
					this.table_update_settings__();
					this.hide_contextmenu__();
				}
			},
			table_apply_settings__: function(k,v,e){
				if( this.focused_table__ ){
					this.table_settings__[ k ] = v;
					this.table_update_settings__();
					this.hide_contextmenu__();
				}
			},
			table_update_settings__: function(v, d, e){
				if( this.focused_table__ ){
					this.table_settings__[v] = d;
					this.focused_table__.removeAttribute("data-tb-theme");
					this.focused_table__.removeAttribute("data-tb-header");
					this.focused_table__.removeAttribute("data-tb-colheader");
					this.focused_table__.removeAttribute("data-tb-striped");
					this.focused_table__.removeAttribute("data-tb-hover");
					if( this.table_settings__['border'] != "none" ){
						this.focused_table__.setAttribute("data-tb-border", this.table_settings__['border'] );
					}else{
						this.focused_table__.removeAttribute("data-tb-border");
					}
					if( this.table_settings__['theme'] != "none" ){
						this.focused_table__.setAttribute("data-tb-theme", this.table_settings__['theme'] );
					}else{
						
					}
					if( this.table_settings__['header'] != "none" ){
						this.focused_table__.setAttribute("data-tb-header", this.table_settings__['header'] );
					}else{
						this.focused_table__.removeAttribute("data-tb-header");
					}
					if( this.table_settings__['footer'] != "none" ){
						this.focused_table__.setAttribute("data-tb-footer", this.table_settings__['footer'] );
					}else{
						this.focused_table__.removeAttribute("data-tb-footer");
					}
					if( this.table_settings__['colheader'] != "none" ){
						this.focused_table__.setAttribute("data-tb-colheader", this.table_settings__['colheader'] );
					}else{
						this.focused_table__.removeAttribute("data-tb-colheader");
					}
					if( this.table_settings__['hover'] != "none" ){
						this.focused_table__.setAttribute("data-tb-hover", this.table_settings__['hover'] );
					}else{
						this.focused_table__.removeAttribute("data-tb-hover");
					}
					if( this.table_settings__['spacing'] != "none" ){
						this.focused_table__.setAttribute("data-tb-spacing", this.table_settings__['spacing'] );
					}else{
						this.focused_table__.removeAttribute("data-tb-spacing");
					}
					if( this.table_settings__['striped'] != "none" ){
						this.focused_table__.setAttribute("data-tb-striped", this.table_settings__['striped'] );
					}else{
						this.focused_table__.removeAttribute("data-tb-striped");
					}
					if( this.table_settings__['width'] != "none" ){
						this.focused_table__.setAttribute("data-tb-width", this.table_settings__['width'] );
					}else{
						this.focused_table__.removeAttribute("data-tb-width");
					}

				}
			},
			table_insert_column__: function(vt){
				if( vt =='right' ){
					vt = "afterend";
				}else if( vt =='left' ){
					vt = "beforebegin";
				}else{ return ; }
				var vtr = this.focused_td__.parentNode;
				var col_index = -1;
				for(var i=0;i<vtr.children.length;i++){
					if( vtr.children[i] == this.focused_td__ ){
						col_index = i;break;
					}
				}
				var vtable__ = vtr.parentNode;
				for( var i=0;i<vtable__.children.length;i++){
					var vl = this.ce__("TD");
					vtable__.children[i].children[ col_index ].insertAdjacentElement(vt,vl);
				}
				this.initialize_tables__();
				this.hide_contextmenu__();
				this.unset_focused__();
			},
			table_insert_row__: function(vt){
				if( vt =='top' ){
					vt = "beforebegin";
				}else if( vt =='bottom' ){
					vt = "afterend";
				}else{ return ; }
				var vtr = this.focused_td__.parentNode;
				var vl = this.ce__("TR");
				for(var i=0;i<vtr.children.length;i++){
					var vl2 = this.ce__("TD");
					vl.appendChild(vl2);
				}
				vtr.insertAdjacentElement(vt,vl);
				this.initialize_tables__();
				this.hide_contextmenu__();
				this.unset_focused__();
			},
			table_insert_row2__: function(vt){
				if( vt =='top' ){
					vt = "beforebegin";
				}else if( vt =='bottom' ){
					vt = "afterend";
				}else{ return ; }
				var vtr = this.focused_tr__;
				var vl = this.ce__("TR");
				for(var i=0;i<vtr.children.length;i++){
					var vl2 = this.ce__("TD");
					vl.appendChild(vl2);
				}
				vtr.insertAdjacentElement(vt,vl);
				this.initialize_tables__();
				this.hide_contextmenu__();
				this.unset_focused__();
			},
			table_insert_column_while_paste__: function(vtd){
				var vtr = vtd.parentNode;
				col_index = vtd.cellIndex;
				var vtable__ = vtr.parentNode;
				for( var i=0;i<vtable__.children.length;i++){
					var vl = this.ce__("TD");
					vtable__.children[i].children[ col_index ].insertAdjacentElement("afterend",vl);
				}
			},
			table_insert_row_while_paste__: function(vtr){
				var vl = this.ce__("TR");
				for(var i=0;i<vtr.children.length;i++){
					var vl2 = this.ce__("TD");
					vl.appendChild(vl2);
				}
				vtr.insertAdjacentElement("afterend",vl);
			},
			table_move_cells_top__: function(){
				var vtr = this.focused_td__.parentNode;
				var vtdi = this.focused_td__.cellIndex;
				var vtable__ = vtr.parentNode;
				for( var i=vtr.rowIndex+1;i<vtable__.children.length;i++ ){
					vtable__.children[i-1].children[vtdi].innerHTML = vtable__.children[i].children[vtdi].innerHTML;
				}
				vtable__.children[vtable__.children.length-1].children[vtdi].innerHTML = "";
				this.hide_contextmenu__();this.hide_other_menus__();
			},
			table_move_cells_left__: function(){
				var vtr = this.focused_td__.parentNode;
				var vtdi = this.focused_td__.cellIndex;
				var vtable__ = vtr.parentNode;
				for( var i=vtdi+1;i<vtr.children.length;i++ ){
					vtr.children[i-1].innerHTML = vtr.children[i].innerHTML;
				}
				vtr.children[ vtr.children.length-1 ].innerHTML = "";
				this.hide_contextmenu__();this.hide_other_menus__();
			},
			table_move_cells_right__: function(){
				var vtr = this.focused_td__.parentNode;
				var vtdi = this.focused_td__.cellIndex;
				var vtable__ = vtr.parentNode;
				this.table_insert_column_while_paste__( vtr.children[ vtr.children.length-1 ] );
				for( var i=vtr.children.length-1;i>vtdi;i-- ){
					vtr.children[i].innerHTML = vtr.children[i-1].innerHTML;
				}
				this.focused_td__.innerHTML = "";
				this.hide_contextmenu__();this.hide_other_menus__();
			},
			table_move_cells_bottom__: function(){
				var vtr = this.focused_td__.parentNode;
				var vtdi = this.focused_td__.cellIndex;
				var vtable__ = vtr.parentNode;
				var vl = this.ce__("TR");
				for( var i=0;i<vtr.children.length;i++ ){
					var vl2 = this.ce__("TD");
					vl.appendChild(vl2);
				}
				vtable__.appendChild(vl);
				for( var i=vtable__.children.length-1;i>vtr.rowIndex;i-- ){
					vtable__.children[i].children[vtdi].innerHTML = vtable__.children[i-1].children[vtdi].innerHTML;
				}
				vtable__.children[vtr.rowIndex].children[vtdi].innerHTML = "";
				this.hide_contextmenu__();this.hide_other_menus__();
			},
			table_delete_row__: function(){
				var vtr = this.focused_td__.parentNode;
				var vtable__ = vtr.parentNode;
				if( vtable__.children.length <= 1 ){
					this.hide_contextmenu__();
					this.unset_focused__();				
					alert("Minimum One row expected for a table");return;
				}
				vtr.remove();
				this.initialize_tables__();
				this.hide_contextmenu__();
				this.unset_focused__();
			},
			table_delete_row2__: function(){
				var vtr = this.focused_tr__;
				var vtable__ = vtr.parentNode;
				if( vtable__.children.length <= 1 ){
					this.hide_contextmenu__();
					this.unset_focused__();				
					alert("Minimum One row expected for a table");return;
				}
				vtr.remove();
				this.initialize_tables__();
				this.hide_contextmenu__();
				this.unset_focused__();
			},
			table_delete_column__: function(){
				var vtr = this.focused_td__.parentNode;
				if( vtr.children.length <= 1 ){
					this.hide_contextmenu__();
					this.unset_focused__();					
					alert("Minimum One column expected for a table");return;
				}
				var vtable__ = vtr.parentNode;
				var col_index = -1;
				for(var i=0;i<vtr.children.length;i++){
					if( vtr.children[i] == this.focused_td__ ){
						col_index = i;break;
					}
				}
				var vtable__ = vtr.parentNode;
				for( var i=0;i<vtable__.children.length;i++){
					vtable__.children[i].children[ col_index ].remove();
				}
				this.initialize_tables__();
				this.hide_contextmenu__();
				this.unset_focused__();
			},
			table_delete_table__: function(){
				this.focused_td__.parentNode.parentNode.parentNode.remove();
				this.initialize_tables__();
				this.hide_contextmenu__();
				this.unset_focused__();
			},
			table_duplicate_row__: function(){
				this.focused_td__.parentNode.insertAdjacentHTML("afterend", this.focused_td__.parentNode.outerHTML );
				this.initialize_tables__();
				this.hide_contextmenu__();
				this.unset_focused__();
			},
			table_duplicate_row2__: function(){
				this.focused_tr__.parentNode.insertAdjacentHTML("afterend", this.focused_tr__.outerHTML );
				this.initialize_tables__();
				this.hide_contextmenu__();
				this.unset_focused__();
			},
			table_duplicate_column__: function(){
				var vtr = this.focused_td__.parentNode;
				var vtable__ = vtr.parentNode;
				var col_index = -1;
				for(var i=0;i<vtr.children.length;i++){
					if( vtr.children[i] == this.focused_td__ ){
						col_index = i;break;
					}
				}
				var vtable__ = vtr.parentNode;
				for( var i=0;i<vtable__.children.length;i++){
					vtable__.children[i].children[ col_index ].insertAdjacentHTML("afterend", vtable__.children[i].children[ col_index ].outerHTML );
				}
				this.initialize_tables__();
				this.hide_contextmenu__();
				this.unset_focused__();
			},
			table_split__: function(){
				if( this.focused_tr__ ){
					var ntdiv = this.ce__("div");
					ntdiv.setAttribute("data-block-type", "TABLE");
					var nt = this.ce__("table");
					var ntb = this.ce__("tbody");
					var trs = Array.from(this.focused_tr__.parentNode.children );
					var tri = trs.indexOf( this.focused_tr__ );
					if( tri > 1 && tri < (trs.length*.8) ){
						var cnt = 0;
						while(trs.length > (tri) ){ cnt++; if( cnt > 20 ){break;}
							ntb.appendChild( trs[tri] );
							trs.splice( (tri), 1 );
						}
						nt.appendChild( ntb );
						ntdiv.appendChild(nt);
						if( this.focused_block_type__ == "TABLE" ){
							this.focused_block__.insertAdjacentElement("afterend", ntdiv);
						}else{
							this.focused_table__.insertAdjacentElement("afterend", ntdiv);
						}
					}else{
						alert("Select middle row for splitting");
					}
				}
				this.hide_other_menus__();
				setTimeout(this.initialize_tables__,100);
			},
			table_cells_delete__: function(){
				for(var i=0;i<this.td_sel_cells__.length;i++){
					for(var j=0;j<this.td_sel_cells__[i]['cols'].length;j++){
						this.td_sel_cells__[i]['cols'][j]['col'].innerHTML = "";
					}
				}
				this.hide_other_menus__();
			},
			table_cells_clean_styles__: function(){
				for(var i=0;i<this.td_sel_cells__.length;i++){
					for(var j=0;j<this.td_sel_cells__[i]['cols'].length;j++){
						this.td_sel_cells__[i]['cols'][j]['col'].removeAttribute("class");
					}
				}
				this.hide_other_menus__();
			},
			table_cells_clean_text__: function(){
				for(var i=0;i<this.td_sel_cells__.length;i++){
					for(var j=0;j<this.td_sel_cells__[i]['cols'].length;j++){
						this.td_sel_cells__[i]['cols'][j]['col'].innerHTML = this.td_sel_cells__[i]['cols'][j]['col'].innerText;
					}
				}
				this.hide_other_menus__();
			},
			table_cells_delete_rows__: function(){
				var trs = Array.from(this.focused_table__.children[0].childNodes);
				for(var i=0;i<this.td_sel_cells__.length;i++){
					trs[ this.td_sel_cells__[i]['rowi'] ].remove();
				}
				this.td_sel_cells__ = [];
				this.td_sel_unfocus__();
				this.hide_other_menus__();
				this.initialize_table__( this.focused_table__ );
			},
			table_cells_delete_columns__: function(){
				var cols = [];
				for(var i=0;i<this.td_sel_cells__.length;i++){
					for(var j=0;j<this.td_sel_cells__[i]['cols'].length;j++){
						cols.push(this.td_sel_cells__[i]['cols'][j]['coli']);
					}
					break;
				}
				cols.sort(function(a,b){return b-a;});
				console.log( cols );
				var trs = Array.from(this.focused_table__.children[0].childNodes);
				for(var i=0;i<trs.length;i++){
					for(var j=0;j<cols.length;j++){
						console.log("Deleting: " + i + ": " + j);
						this.table_cell_get__(i,cols[j]).remove();
					}
				}
				this.td_sel_cells__ = [];
				this.td_sel_unfocus__();
				this.hide_other_menus__();
				this.initialize_table__( this.focused_table__ );
			},
			table_cells_copy_column_left__: function(){
				var cols = [];
				for(var i=0;i<this.td_sel_cells__.length;i++){
					for(var j=0;j<this.td_sel_cells__[i]['cols'].length;j++){
						cols.push(this.td_sel_cells__[i]['cols'][j]['coli']);
					}
					break;
				}
				var trs = Array.from(this.focused_table__.children[0].childNodes);
				for(var i=0;i<trs.length;i++){
					var tds = Array.from(trs[i].childNodes);
					var firsttd =false;
					for(var j=0;j<tds.length;j++){
						if( cols.indexOf(j) > -1 ){
							var vid = "td_" + this.focused_table__.id + "_" + i + "_" + j;
							var vtd = this.table_cell_get__(i,j );
							if( !firsttd ){
								firsttd = vtd;
							}
							var newtd = vtd.cloneNode(true);
							newtd.removeAttribute("id");
							var fvtd = firsttd;
							fvtd.insertAdjacentElement("beforebegin", newtd);
						}
					}
				}
				this.td_sel_unfocus__();
				this.hide_other_menus__();
				this.initialize_table__( this.focused_table__ );
			},
			table_cells_copy_column_right__: function(){
				var cols = [];
				for(var i=0;i<this.td_sel_cells__.length;i++){
					for(var j=0;j<this.td_sel_cells__[i]['cols'].length;j++){
						cols.push(this.td_sel_cells__[i]['cols'][j]['coli']);
					}
					break;
				}
				if( !this.focused_table__ ){
					console.error( "table not found");
					return false;
				}
				var trs = Array.from(this.focused_table__.children[0].childNodes);
				for(var i=0;i<trs.length;i++){
					var tds = Array.from(trs[i].childNodes);
					var firsttd = "";
					for(var j=0;j<tds.length;j++){
						if( cols.indexOf(j) > -1 ){
							var vtd = this.table_cell_get__(i,j );
							if( firsttd == "" ){
								firsttd = vtd;
							}
							var newtd = vtd.cloneNode(true);
							newtd.removeAttribute("id");
							var fvtd = firsttd;
							fvtd.insertAdjacentElement("afterend", newtd);
						}
					}
				}
				this.td_sel_unfocus__();
				this.hide_other_menus__();
				this.initialize_table__( this.focused_table__ );
			},
			table_cells_copy__: function(){
				var vtxt = "";
				var vtable__ = "<table><tbody>";
				for(var i=0;i<this.td_sel_cells__.length;i++){
					vtable__ = vtable__ + "<tr>";
					var cols = this.td_sel_cells__[i]['cols'];
					for(var j=0;j<cols.length;j++){
						vtable__ = vtable__ + "<td>" + cols[j]['col'].innerHTML + "</td>";
						vtxt = vtxt + cols[j]['col'].innerHTML + " \t";
					}
					vtxt = vtxt + "\n";
					vtable__ = vtable__ + "</tr>";
				}
				vtable__ = vtable__ + "</table>";
				var vtxt2 = "";
				for(var i=0;i<vtxt.length;i++){ if( vtxt.charCodeAt(i) < 126 ){ vtxt2 = vtxt2+ vtxt.substr(i,1); } }
				const blob = new Blob([vtable__], { type: "text/html" });
				const blob2 = new Blob([vtxt2], { type: "text/plain" });
				const richTextInput__ = new ClipboardItem({ "text/html": blob, "text/plain": blob2 });
				navigator.clipboard.write([richTextInput__]);
				this.table_cells_clipboard__ = vtable__;
				console.log("Table copied");
				this.hide_other_menus__();
				this.td_sel_unfocus__();
			},
			table_cells_cut__: function(){
				var vtxt = "";
				var vtable__ = "<table><tbody>";
				for(var i=0;i<this.td_sel_cells__.length;i++){
					vtable__ = vtable__ + "<tr>";
					var cols = this.td_sel_cells__[i]['cols'];
					for(var j=0;j<cols.length;j++){
						vtable__ = vtable__ + "<td>" + cols[j]['col'].innerHTML + "</td>";
						vtxt = vtxt + cols[j]['col'].innerHTML + " \t";
						cols[j]['col'].innerHTML = "";
					}
					vtxt = vtxt + "\n";
					vtable__ = vtable__ + "</tr>";
				}
				vtable__ = vtable__ + "</table>";
				var vtxt2 = "";
				for(var i=0;i<vtxt.length;i++){ if( vtxt.charCodeAt(i) < 126 ){ vtxt2 = vtxt2+ vtxt.substr(i,1); } }
				const blob = new Blob([vtable__], { type: "text/html" });
				const blob2 = new Blob([vtxt2], { type: "text/plain" });
				const richTextInput__ = new ClipboardItem({ "text/html": blob, "text/plain": blob2 });
				navigator.clipboard.write([richTextInput__]);
				console.log("Table copied");
				this.hide_other_menus__();
				this.td_sel_unfocus__();
			},
			table_cells_paste_cells__: function(){
				if( this.table_cells_clipboard__ ){
					var vl = this.ce__("div");
					vl.innerHTML = this.table_cells_clipboard__;
					this.table_cells_paste__(vl.children[0]);
					vl.remove();
				}
			},
			table_cells_paste__: function(vtable__){
				var newtrs = Array.from(vtable__.children[0].children);
				if( this.td_sel_cnt__ > 1 ){
					var start_td__ = this.td_sel_cells__[0]['cols'][0]['col']
				}else{
					var start_td__ = this.focused_td__;
				}
				var vtri = Number(start_td__.parentNode.rowIndex);
				var vtdi = Number(start_td__.cellIndex);
				var current_td__ = start_td__;
				var current_tr__ = current_td__.parentNode;
				for(var i=0;i<newtrs.length;i++){
					var current_td__ = current_tr__.children[ vtdi ];
					var newtds = Array.from(newtrs[i].children);
					for(var j=0;j<newtds.length;j++){
						current_td__.innerHTML = newtds[j].innerHTML;
						if( j < newtds.length-1 )
						{
							if( current_td__.nextElementSibling ){
								current_td__ = current_td__.nextElementSibling;
							}else{
								this.table_insert_column_while_paste__(current_td__);
								current_td__ = current_td__.nextElementSibling;
							}
						}
					}
					if( i < newtrs.length-1 ){
						if( current_tr__.nextElementSibling ){
							current_tr__ = current_tr__.nextElementSibling;
						}else{
							this.table_insert_row_while_paste__(current_tr__);
							current_tr__ = current_tr__.nextElementSibling;
						}
						current_td__ = current_tr__.childNodes[ vtdi ];
					}
				}
				this.initialize_table__( this.focused_table__ );
				this.hide_other_menus__();
				this.td_sel_unfocus__();
			},
	
			/*Table Functions*/

			apply_text_color__: function(v){
				if( this.focused__.hasAttribute("class") ){
					var c = this.focused__.getAttribute("class").split(/[\ \t\r\n]/g);
				}else{
					var c = [];
				}
				for( var i=0;i<c.length;i++){
					if( c[i].trim()=="" ){
						c.splice(i,1);i--;
					}else if( c[i].match(/^fg\-/) ){
						c.splice(i,1);i--;
					}
				}
				if( v != "none" ){c.push( v );}
				this.focused__.setAttribute("class",  c.join(" ") );
				this.hide_contextmenu__();
			},
			apply_background_color__: function(v){
				if( this.focused__.hasAttribute("class") ){
					var c = this.focused__.getAttribute("class").split(/[\ \t\r\n]/g);
				}else{
					var c = [];
				}
				for( var i=0;i<c.length;i++){
					if( c[i].trim()=="" ){
						c.splice(i,1);i--;
					}else if( c[i].match(/^bg\-/) ){
						c.splice(i,1);i--;
					}
				}
				if( v != "none" ){c.push( v );}
				this.focused__.setAttribute("class", c.join(" ") );
				this.hide_contextmenu__();
			},
			convertible_to_text__:function(){
				if( this.focused_type__.match(/^(P|DIV|H1|H2|H3|H4|TD|TH|LI)$/) ){
					console.log("convertible_to_text__");
					console.log( this.focused__.innerHTML );
					var pr  = new RegExp( "\<(.*?)\<\/" );
					if( this.focused__.innerHTML.match(pr) ){
						console.log("tags found");
						return true;
					}
				}else{
					console.log("convertible_to_text__ not");
					return false;
				}
			},
			convertible_to_paragraph__:function(){
				if( this.focused_type__.match(/^(TD|TH|LI)$/) ){
					if( this.focused__.innerHTML.match(/\<(P|DIV|OL|UL|TABLE)/i) ){
						return false;
					}else{
						return true;
					}
				}else{
					return false;
				}
			},
			is_duplicatable__: function(){
				if( this.focused_type__.match(/^(P|DIV|UL|OL|LI)$/) ){
					return true;
				}else{
					return false;
				}
			},
			innerhtml_to_text__: function(){
				this.focused__.innerHTML = this.focused__.innerText+'';
				this.hide_contextmenu__();
				this.focused_block_set_bounds__();
			},
			innerhtml_to_blocks__: function(){
				var vl = this.ce__("P");
				vl.innerHTML = this.focused__.innerHTML;
				this.focused__.innerHTML = "";
				this.focused__.appendChild(vl);
				this.set_focused2__(vl);
				this.hide_contextmenu__();
				this.focused_block_set_bounds__();
			},
			change_to_types_applicable__: function(){
				if( this.focused_type__.match(/^(DIV|P|H1|H2|H3|H4|CallOut|Quote|Code)$/) ){
					var s =  ["DIV","P","H1","H2","H3","H4","CallOut","Quote","Code"];
					var si = s.indexOf( this.focused_type__ );
					if( si > -1 ){
						s.splice(si,1);
					}
					return s;
				}
				return [];
			},
			change_to_types_allowed__: function(){
				if( this.focused_type__.match(/^(DIV|P|H1|H2|H3|H4|CallOut|Quote|Code)$/) ){
					return true;
				}
				return false;
			},
			convert_tag_to__: function(vto){
				this.contextmenu__=false;
				console.log("Convert tag from: " + this.focused_type__ + " : " + vto);
				if( this.focused_type__.match(/^(P|DIV|H1|H2|H3|H4)$/) ){
					if( vto.match(/^(P|DIV|H1|H2|H3|H4)$/) ){
						var vl = this.ce__(vto);
						vl.innerHTML = this.focused__.innerHTML;
						this.focused__.insertAdjacentElement("afterend",vl);
						this.focused__.remove();
						this.set_focused2__(vl);
					}
				}
			},
			dragger_mousedown__: function(e){	
				this.hide_bounds__();
				//return false;
				var s = this.side_settings_el__.getBoundingClientRect();
				this.dragger__ = true;
				var sy=window.scrollY;
				var sx=window.scrollX;
				this.dragger_style__ = "width:" + (s.width+30) + "px;height:"+(s.height+10)+"px;top:"+(s.top+sy-5)+"px;left:"+(s.left+sx-40)+"px;opacity:0.1;";
				this.dragger_w__ = s.width+30;
				this.dragger_h__ = s.height+10;
				this.dragger_t__ = s.top+sy-5;
				this.dragger_l__ = s.left+sx-40;
				html2canvas(this.side_settings_el__).then((canvas) => {
				    const base64image = canvas.toDataURL("image/png");
				    this.dragger_img__ = base64image;
				});
			},
			dragger_check_pos__: function(){

			},
			dragger_mousemove__: function(e){
				var ox = Number(e.clientX);
				var oy = Number(e.clientY);
				//console.log( ox + "x" + oy );
				this.dragger_style__ = "width:" + (this.dragger_w__) + "px;height:"+(this.dragger_h__)+"px;top:"+(oy-10)+"px;left:"+(ox-10)+"px;";
				//console.log( this.dragger_style__ );
			},
			dragger_mouseup__: function(e){
				console.log("dragger_mouseup__");
				this.dragger__ = false;
				this.dragger_img__ = "";
				this.dragger_style__ = "visibility:hidden;";
				this.drop_target_style__ = "visibility:hidden;";
			},
			bar_dragenter__: function(e){
				// console.log("drag enter");
				// console.log( e );
				e.preventDefault();
			},
			bar_dragover__: function(e){
				// console.log("drag over");
				// console.log( e );
				e.preventDefault();
			},
			bar_drop__: function(e){
				console.log("bar drop");
				console.log( e );
				e.preventDefault();
				this.dragger__ = false;
				this.dragger_img__ = "";
				this.dragger_style__ = "visibility:hidden;";
				this.drop_target_style__ = "visibility:hidden;";
				this.side_settings_style__ = "visibility:hidden;";
				this.side_settings_style2__ = "visibility:hidden;";

				if( this.drop_target_pos__ == 'top' ){
					this.drop_target_el__.insertAdjacentElement("beforebegin", this.side_settings_el__ );
				}else{
					this.drop_target_el__.insertAdjacentElement("afterend", this.side_settings_el__ );
				}
			},
			dragenter__: function(e){
				// console.log("drag enter");
				// console.log( e );
				e.preventDefault();
				e.dataTransfer.effectAllowed = "copyMove";
			},
			dragstart__: function(e){
				//console.log( e );	
				console.log("drag started");
				e.dataTransfer.effectAllowed = "copyMove";
				this.dragger_style__ = this.dragger_style__+";opacity:0.01;";
			},
			dragover__: function(e){
				// console.log("drag over");
				// console.log( e );
				//e.preventDefault();
				var ox = Number(e.clientX);
				var oy = Number(e.clientY);

				var v = e.target;
				if( v.hasAttribute("data-id") ){
					if( v.getAttribute("data-id") == "root" ){
						e.preventDefault();
						e.stopPropagation();
						return false;
					}
				}
				var cnt = 0;
				while( 1 ){
					cnt++;
					if( cnt>5 ){console.error("focuselement + 3");return ;break;}
					if( v.hasAttribute("data-id") ){
						if( v.getAttribute("data-id") == "root" ){
							break;
						}
					}
					if( v.nodeName.match(/^(P|DIV|H1|H2|H3|H4|UL|OL|TABLE)$/i) ){
						break;
					}
					v = v.parentNode;
				}
				var s = v.getBoundingClientRect();
				var l=Number(s.left);
				var t=Number(s.top);
				var w=Number(s.width);
				var h=Number(s.height);
				var b=Number(s.bottom);
				var r=Number(s.right);
				var oY = e.clientY;
				var smid = Number(s.top) + Number(s.height)/2;
				//console.log( oY + ": " + smid + ": " + s.top + ": " + s.bottom );
				var sy = window.scrollY; //Number(scrollY);
				var sx = window.scrollX; //Number(scrollX);
				if( h > 20 ){
					this.drop_target_el__ = v;
					this.drop_target_type__ = v.nodeName;
					if( oY < smid ){
						var s2_h = 0;
						if( this.drop_target_el__.previousElementSibling != null ){
							var s2 = this.drop_target_el__.previousElementSibling.getBoundingClientRect();
							s2_h = s.top-s2.bottom;
						}else{
							s2_h = 20;
						}
						var s2_h2 = parseInt(h/2);
						this.drop_target_style__ = "top:" + (smid-s2_h2-s2_h+sy) + "px;left:" + (s.left+sx) + "px;width:"+(s.width)+";height:"+(s2_h2+s2_h+10)+"px;";
						this.drop_target_style2__ = "border: 1px dashed black;height:"+(s2_h+5)+"px;background-color:#f8f8f8;";
						this.drop_target_pos__ = "top";
					}else{
						var s2_h = 0;
						if( this.drop_target_el__.nextElementSibling != null ){
							var s2 = this.drop_target_el__.nextElementSibling.getBoundingClientRect();
							s2_h = s2.top-s.bottom;
						}else{
							s2_h = 20;
						}
						var s2_h2 = parseInt(h/2);
						this.drop_target_style__ = "top:" + (smid+sy) + "px;left:" + (s.left+sx) + "px;width:"+(s.width)+";height:"+(s2_h2+s2_h+10)+"px;";
						this.drop_target_style2__ = "border: 1px dashed black;margin-top:"+(s2_h2-10+sy)+"px;height:"+(s2_h+5)+"px;background-color:#f8f8f8;";
						this.drop_target_pos__ = "bottom";
					}
				}else{
					this.drop_target_style__ = "visibility:hidden;";
				}
			},
			window_drop__: function(e){
				console.log("window drop");
				e.preventDefault();
			}
	},
	template: ``
};


const tag_settings_configs__ = {
	"DIV": {"html":`<div>Div content</div>`},
	"P": {"html":`<p>Paragraph content</p>`},
	"H1": {"html":`<h1>Heading 1</h1>`},
	"H2": {"html":`<h2>Heading 2</h2>`},
	"H3": {"html":`<h3>Heading 3</h3>`},
	"H4": {"html":`<h4>Heading 4</h4>`},
	"BlockQuote": {"html":`<blockquote><p>Quote Content</p></blockquote>`},
	"CallOut": {"html":`<div data-type="callout" class="callout"><div class="callouticon">ICON</div><div class="calloutdata"><p>Quote Content</p></div></div>`},
	"PRE": {"html": "<pre>Rich text</pre>"},
	"UL": {"html":`<ul><li>Item 1</li><li>Item 2</li></ul>`},
	"OL": {"html":`<ol><li>Item 1</li><li>Item 2</li></ol>`},
	"Grid": {"html":`<div data-type="grid" class="grid" >
		<div class="gridrow row" >
			<div class="gridcol col-6"><p>Item 1</p></div>
			<div class="gridcol col-6"><p>Item 1</p></div>
		</div>
		<div class="gridrow row" >
			<div class="gridcol col-6"><p>Item 1</p></div>
			<div class="gridcol col-6"><p>Item 1</p></div>
		</div>
	</div>`},
	"Table": {"html":`<table class="table table-bordered table-striped table-sm" ><tbody><tr><td>Col 1</td><td>Col 2</td><td>Col 3</td></tr><tr><td>Col 1</td><td>Col 2</td><td>Col 3</td></tr></tbody></table>`},
	"IMG": {"html":`<div><img src="" alt="Image" /></div>`},
	"Figure": {"html":`<figure class="figure">
		  <img src="..." class="figure-img img-fluid rounded" alt="...">
		  <figcaption class="figure-caption">A caption for the above image.</figcaption>
	</figure>`},
	"DefList": {"html": `<div data-type="deflist" class="deflist" >
			<div class="row deflistrow" >
				<div class="col-4 deflisttitle" >One</div>
				<div class="col-8 deflistdata" ><p>Each of the nine words in the sentence, "The Quick brown fox jumps over the lazy dog" is written on a separate piece of paper.</p></div>
			</div>
			<div class="row deflistrow" >
				<div class="col-4 deflisttitle" >Two</div>
				<div class="col-8 deflistdata" ><p>Each of the nine words in the sentence, "The Quick brown fox jumps over the lazy dog" is written on a separate piece of paper.</p></div>
			</div>
			<div class="row deflistrow" >
				<div class="col-4 deflisttitle" >Three</div>
				<div class="col-8 deflistdata" ><p>Each of the nine words in the sentence, "The Quick brown fox jumps over the lazy dog" is written on a separate piece of paper.</p></div>
			</div>
			<div class="row deflistrow" >
				<div class="col-4 deflisttitle" >Four</div>
				<div class="col-8 deflistdata" ><p>Each of the nine words in the sentence, "The Quick brown fox jumps over the lazy dog" is written on a separate piece of paper.</p></div>
			</div>
		</div>`},
};

String.prototype.toProperCase = function(){
    return this.replace(/\w\S*/g, function(txt){
    	return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
	});
};

var css_template__ = `.satish_editor_v1{outline:none;}

.satish_editor_v1 a { color:#00005d; text-decoration: underline; }
.satish_editor_v1 a:hover { color:blue; text-decoration: underline; background-color: #f0f0e0; }
.satish_editor_v1 p,.satish_editor_v1 td,.satish_editor_v1 th,.satish_editor_v1 li{line-height: 1.5;}
.satish_editor_v1 p,.satish_editor_v1 h1,.satish_editor_v1 h2,.satish_editor_v1 h3,.satish_editor_v1 h4{margin-bottom: 1rem;}

.satish_editor_v1 > p,.satish_editor_v1 > div, .satish_editor_v1 > table, .satish_editor_v1 > ul{}

.satish_editor_v1 table {border-collapse: collapse; margin-bottom: 10px; }
.satish_editor_v1 > table {border-collapse: collapse; margin-bottom: 1rem; }
.satish_editor_v1 table td,.satish_editor_v1 table th{ border:1px solid #eee; border-bottom: 1px solid #aaa; padding:3px; min-width: 100px; vertical-align: top; }
.satish_editor_v1 table[data-tb-border="a"] th,.satish_editor_v1 table[data-tb-border="a"] td{ border:1px solid #ccc; }
.satish_editor_v1 table[data-tb-border="b"] th,.satish_editor_v1 table[data-tb-border="b"] td{ border:1px solid #666; }
.satish_editor_v1 table[data-tb-border="c"] th,.satish_editor_v1 table[data-tb-border="c"] td{ border:2px solid #aaa; }
.satish_editor_v1 table[data-tb-spacing="1"] td,.satish_editor_v1 table[data-tb-spacing="1"] td{ padding:2px; }
.satish_editor_v1 table[data-tb-spacing="2"] td,.satish_editor_v1 table[data-tb-spacing="2"] td{ padding:5px; }
.satish_editor_v1 table[data-tb-spacing="3"] td,.satish_editor_v1 table[data-tb-spacing="3"] td{ padding:10px; }
.satish_editor_v1 table[data-tb-spacing="4"] td,.satish_editor_v1 table[data-tb-spacing="4"] td{ padding:15px; }
.satish_editor_v1 table[data-tb-width="full"]{ width:100%; }

.satish_editor_v1 table[data-tb-header] tr:first-child td{ font-weight: bold; border-bottom-width: 2px; }
.satish_editor_v1 table[data-tb-colheader="yes"] td:first-child { font-weight: bold; border-right-width: 2px solid #aaa; }
.satish_editor_v1 table[data-tb-hover] tr:hover{background-color: #eeeeee;}
.satish_editor_v1 table[data-tb-striped] tr:nth-child(even) td{background-color: #f8f8f8;}

.satish_editor_v1 table[data-tb-theme="blue"  ] td{ background-color: #f5f5ff; border-bottom: 1px solid #a6a6ff; }
.satish_editor_v1 table[data-tb-theme="green" ] td{ background-color: #e1ffe1; border-bottom: 1px solid #6bff6b; }
.satish_editor_v1 table[data-tb-theme="red"   ] td{ background-color: #fff1f1; border-bottom: 1px solid #ff7474; }
.satish_editor_v1 table[data-tb-theme="orange"] td{ background-color: #ffedcc; border-bottom: 1px solid #ffc04c; }
.satish_editor_v1 table[data-tb-theme="purple"] td{ background-color: #ffe9ff; border-bottom: 1px solid #ff68ff; }
.satish_editor_v1 table[data-tb-theme="gray"  ] td{ background-color: #e4e4e4; border-bottom: 1px solid #7a7a7a; }
.satish_editor_v1 table[data-tb-theme="light" ] td{ background-color: #efefef; border-bottom: 1px solid #b7b7b7; }
.satish_editor_v1 table[data-tb-theme="dark"  ] td{ background-color: #666666; border-bottom: 1px solid #3d3d3d; color:white; }

.satish_editor_v1 table[data-tb-theme="blue"  ][data-tb-striped] tr:nth-child(even) td{ background-color: #f5f5ff5a; }
.satish_editor_v1 table[data-tb-theme="green" ][data-tb-striped] tr:nth-child(even) td{ background-color: #e1ffe15a; }
.satish_editor_v1 table[data-tb-theme="red"   ][data-tb-striped] tr:nth-child(even) td{ background-color: #fff1f15a; }
.satish_editor_v1 table[data-tb-theme="orange"][data-tb-striped] tr:nth-child(even) td{ background-color: #ffedcc5a; }
.satish_editor_v1 table[data-tb-theme="purple"][data-tb-striped] tr:nth-child(even) td{ background-color: #ffe9ff5a; }
.satish_editor_v1 table[data-tb-theme="gray"  ][data-tb-striped] tr:nth-child(even) td{ background-color: #e4e4e45a; }
.satish_editor_v1 table[data-tb-theme="light" ][data-tb-striped] tr:nth-child(even) td{ background-color: #efefef5a; }
.satish_editor_v1 table[data-tb-theme="dark"  ][data-tb-striped] tr:nth-child(even) td{ background-color: #6666665a; }

.satish_editor_v1 table[data-tb-theme="blue"  ][data-tb-hover] tr:hover td{ background-color: #f5f5ff66; }
.satish_editor_v1 table[data-tb-theme="green" ][data-tb-hover] tr:hover td{ background-color: #e1ffe166; }
.satish_editor_v1 table[data-tb-theme="red"   ][data-tb-hover] tr:hover td{ background-color: #fff1f166; }
.satish_editor_v1 table[data-tb-theme="orange"][data-tb-hover] tr:hover td{ background-color: #ffedcc66; }
.satish_editor_v1 table[data-tb-theme="purple"][data-tb-hover] tr:hover td{ background-color: #ffe9ff66; }
.satish_editor_v1 table[data-tb-theme="gray"  ][data-tb-hover] tr:hover td{ background-color: #e4e4e466; }
.satish_editor_v1 table[data-tb-theme="light" ][data-tb-hover] tr:hover td{ background-color: #efefef66; }
.satish_editor_v1 table[data-tb-theme="dark"  ][data-tb-hover] tr:hover td{ background-color: #66666666; }

.satish_editor_v1 table[data-tb-theme="blue"  ][data-tb-header] tr:first-child td{ background-color: #cdcdff;}
.satish_editor_v1 table[data-tb-theme="green" ][data-tb-header] tr:first-child td{ background-color: #a7ffa7;}
.satish_editor_v1 table[data-tb-theme="red"   ][data-tb-header] tr:first-child td{ background-color: #ffcccc;}
.satish_editor_v1 table[data-tb-theme="orange"][data-tb-header] tr:first-child td{ background-color: #ffda97;}
.satish_editor_v1 table[data-tb-theme="purple"][data-tb-header] tr:first-child td{ background-color: #ffafff;}
.satish_editor_v1 table[data-tb-theme="gray"  ][data-tb-header] tr:first-child td{ background-color: #acacac;}
.satish_editor_v1 table[data-tb-theme="light" ][data-tb-header] tr:first-child td{ background-color: #d6d6d6;}
.satish_editor_v1 table[data-tb-theme="dark"  ][data-tb-header] tr:first-child td{ background-color: #5d5d5d;}

.satish_editor_v1 table[data-tb-theme="blue"  ][data-tb-colheader="yes"] td:first-child{ background-color: #cdcdff; border-right: 1px solid #a6a6ff; }
.satish_editor_v1 table[data-tb-theme="green" ][data-tb-colheader="yes"] td:first-child{ background-color: #a7ffa7; border-right: 1px solid #6bff6b; }
.satish_editor_v1 table[data-tb-theme="red"   ][data-tb-colheader="yes"] td:first-child{ background-color: #ffcccc; border-right: 1px solid #ff7474; }
.satish_editor_v1 table[data-tb-theme="orange"][data-tb-colheader="yes"] td:first-child{ background-color: #ffda97; border-right: 1px solid #ffc04c; }
.satish_editor_v1 table[data-tb-theme="purple"][data-tb-colheader="yes"] td:first-child{ background-color: #ffafff; border-right: 1px solid #ff68ff; }
.satish_editor_v1 table[data-tb-theme="gray"  ][data-tb-colheader="yes"] td:first-child{ background-color: #acacac; border-right: 1px solid #7a7a7a; }
.satish_editor_v1 table[data-tb-theme="light" ][data-tb-colheader="yes"] td:first-child{ background-color: #d6d6d6; border-right: 1px solid #b7b7b7; }
.satish_editor_v1 table[data-tb-theme="dark"  ][data-tb-colheader="yes"] td:first-child{ background-color: #5d5d5d; border-right: 1px solid #3d3d3d; color:white; }


.satish_editor_v1 table td.sel,.satish_editor_v1 table th.sel{ background-color: rgb(235,255,210)!important; }
.satish_editor_v1 table td[data-align="left"]{ text-align: left; }
.satish_editor_v1 table td[data-align="right"]{ text-align: right; }
.satish_editor_v1 table td[data-align="center"]{ text-align: center; }
.satish_editor_v1 table td[data-wrap="no"]{ white-space: nowrap; }

.satish_editor_v1 div[data-block-type="PRE"]{ display:flex; }
.satish_editor_v1 div[data-block-type]{ margin-bottom:10px; }

.satish_editor_v1 ul{ padding-bottom: 10px; }
.satish_editor_v1 a:empty:before {content: '       ';white-space: pre;}
.satish_editor_v1 p:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 div:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 span:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 h1:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 h2:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 h3:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 h4:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 pre:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 li:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 td:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 th:empty:before {content: ' ';white-space: pre;}

.satish_editor_v1 img { max-width: 100%; cursor: pointer; }
.satish_editor_v1 img:hover{ outline-color: #aaa; outline-width: thin; outline-style: dashed; }

.satish_editor_v1 div.image { min-height:100px; min-width: 100px; margin-top: 10px; margin-bottom: 10px; text-align: center; padding-bottom: 5px; }
.satish_editor_v1 > div.image {margin-bottom: 1rem;}
.satish_editor_v1 div.image[data-im="left"] { float:left; margin-right:40px; }
.satish_editor_v1 div.image[data-im="right"] { float:right; margin-left:40px; }
.satish_editor_v1 div.image[data-is="large"] img{ max-width:100%; }
.satish_editor_v1 div.image[data-is="medium"] img{ max-width:70%; }
.satish_editor_v1 div.image[data-is="small"] img{ max-width: 40%; }
.satish_editor_v1 div.image[data-is="thumb"] img{ max-width: 200px; }
.satish_editor_v1 div.image[data-isf="medium"] { width:60%; }
.satish_editor_v1 div.image[data-isf="medium"] img{ max-width:100% }
.satish_editor_v1 div.image[data-isf="small"] { width: 30%; }
.satish_editor_v1 div.image[data-isf="small"] img{ max-width:100% }
.satish_editor_v1 div.image[data-isf="thumb"] { width: 150px; }
.satish_editor_v1 div.image[data-isf="thumb"] img{ max-width:100% }
.satish_editor_v1 div.image img { border: 1px solid #ccc; border-radius: 3px; max-width: 100%; }
.satish_editor_v1 div.image div.image_caption{ margin: 10px; }
.satish_editor_v1 div.image div.image_caption[data-display="no"] {display: none;}

.satish_editor_v1 div[data-type="callout"] {display:flex; column-gap:10px; padding: 10px; margin-bottom: 20px;}
.satish_editor_v1 div[data-type="callout"] div.callouticon{ width:50px; }
.satish_editor_v1 div[data-type="callout"] div.calloutdata{ width:calc( 100% - 55px ); }

.satish_editor_v1 blockquote{ padding:20px 50px; font-size:1.5rem;  }

.satish_editor_v1 div[data-type="grid"] {padding: 10px 10px; margin-bottom: 20px;}
.satish_editor_v1 div[data-type="grid"] div.gridcol{border:1px solid #ccc; padding:5px 10px; }

.satish_editor_v1 div[data-type="deflist"] {padding: 10px 10px; margin-bottom: 20px;}

.satish_editor_v1 ol.list-style-disc { list-style-type:disc; }
.satish_editor_v1 ol.list-style-square { list-style-type:square; }
.satish_editor_v1 ol.list-style-circle { list-style-type:circle; }
.satish_editor_v1 ol.list-style-decimal { list-style-type:decimal; }
.satish_editor_v1 ol.list-style-decimal-leading{ list-style-type:decimal-leading-zero; }
.satish_editor_v1 ol.list-style-lower-alpha{ list-style-type:lower-alpha; }
.satish_editor_v1 ol.list-style-lower-greek{ list-style-type:lower-greek; }
.satish_editor_v1 ol.list-style-lower-roman{ list-style-type:lower-roman; }
.satish_editor_v1 ol.list-style-upper-alpha{ list-style-type:upper-alpha; }
.satish_editor_v1 ol.list-style-upper-greek{ list-style-type:upper-greek; }
.satish_editor_v1 ol.list-style-upper-roman{ list-style-type:upper-roman; }
.satish_editor_v1 ul.list-style-disc { list-style-type:disc; }
.satish_editor_v1 ul.list-style-square { list-style-type:square; }
.satish_editor_v1 ul.list-style-circle { list-style-type:circle; }
.satish_editor_v1 ul.list-style-decimal { list-style-type:decimal; }
.satish_editor_v1 ul.list-style-decimal-leading{ list-style-type:decimal-leading-zero; }
.satish_editor_v1 ul.list-style-lower-alpha{ list-style-type:lower-alpha; }
.satish_editor_v1 ul.list-style-lower-greek{ list-style-type:lower-greek; }
.satish_editor_v1 ul.list-style-lower-roman{ list-style-type:lower-roman; }
.satish_editor_v1 ul.list-style-upper-alpha{ list-style-type:upper-alpha; }
.satish_editor_v1 ul.list-style-upper-greek{ list-style-type:upper-greek; }
.satish_editor_v1 ul.list-style-upper-roman{ list-style-type:upper-roman; }

.satish_editor_v1 pre{
	color: #00424f;
	margin-bottom: 20px;
	padding: 10px 20px;
	line-height: 1.2;
	background-color: #e5e5f7;
	background-image: linear-gradient(#f0f0f0 1px, transparent 1px), linear-gradient(to right, #f0f0f0 1px, #fff 1px);
	background-size: 5px 5px;
	border: 1px solid #eee;
	border-left: 10px solid #eee;
}

.satish_editor_v1 pre::-webkit-scrollbar {width: 5px; height: 5px;}
.satish_editor_v1 pre::-webkit-scrollbar-track {background: #f1f1f1;}
.satish_editor_v1 pre::-webkit-scrollbar-thumb {background: #888;}
.satish_editor_v1 pre::-webkit-scrollbar-thumb:hover {background: #555;}

.satish_editor_v1 .fg-red{ color:#FF0000; }
.satish_editor_v1 .fg-darkRed{ color:#8B0000; }
.satish_editor_v1 .fg-pink{ color:#FFC0CB; }
.satish_editor_v1 .fg-deepPink{ color:#FF1493; }
.satish_editor_v1 .fg-tomato{ color:#FF6347; }
.satish_editor_v1 .fg-orange{ color:#FFA500; }
.satish_editor_v1 .fg-gold{ color:#FFD700; }
.satish_editor_v1 .fg-yellow{ color:#FFFF00; }
.satish_editor_v1 .fg-purple{ color:#800080; }
.satish_editor_v1 .fg-indigo{ color:#4B0082; }
.satish_editor_v1 .fg-green{ color:#008000; }
.satish_editor_v1 .fg-teal{ color:#008080; }
.satish_editor_v1 .fg-cyan{ color:#00FFFF; }
.satish_editor_v1 .fg-royalBlue{ color:#4169E1; }
.satish_editor_v1 .fg-blue{ color:#0000FF; }
.satish_editor_v1 .fg-brown{ color:#A52A2A; }
.satish_editor_v1 .fg-maroon{ color:#800000; }
.satish_editor_v1 .fg-white{ color:#FFFFFF; }
.satish_editor_v1 .fg-beige{ color:#F5F5DC; }
.satish_editor_v1 .fg-silver{ color:#C0C0C0; }
.satish_editor_v1 .fg-gray{ color:#808080; }
.satish_editor_v1 .fg-black{ color:#000000; }

.satish_editor_v1 .bg-red{ background-color:#FF000033; }
.satish_editor_v1 .bg-darkRed{ background-color:#8B000033; }
.satish_editor_v1 .bg-pink{ background-color:#FFC0CB33; }
.satish_editor_v1 .bg-deepPink{ background-color:#FF149333; }
.satish_editor_v1 .bg-tomato{ background-color:#FF634733; }
.satish_editor_v1 .bg-orange{ background-color:#FFA50033; }
.satish_editor_v1 .bg-gold{ background-color:#FFD70033; }
.satish_editor_v1 .bg-yellow{ background-color:#FFFF0033; }
.satish_editor_v1 .bg-purple{ background-color:#80008033; }
.satish_editor_v1 .bg-indigo{ background-color:#4B008233; }
.satish_editor_v1 .bg-green{ background-color:#00800033; }
.satish_editor_v1 .bg-teal{ background-color:#00808033; }
.satish_editor_v1 .bg-cyan{ background-color:#00FFFF33; }
.satish_editor_v1 .bg-royalBlue{ background-color:#4169E133; }
.satish_editor_v1 .bg-blue{ background-color:#0000FF33; }
.satish_editor_v1 .bg-brown{ background-color:#A52A2A33; }
.satish_editor_v1 .bg-maroon{ background-color:#80000033; }
.satish_editor_v1 .bg-white{ background-color:#FFFFFF33; }
.satish_editor_v1 .bg-beige{ background-color:#F5F5DC33; }
.satish_editor_v1 .bg-silver{ background-color:#C0C0C033; }
.satish_editor_v1 .bg-gray{ background-color:#80808033; }
.satish_editor_v1 .bg-black{ background-color:#00000033; }

.satish_editor_v1 select,#editor_div input, #editor_div textarea, #editor_div button{outline: 0px;}
.satish_editor_v1 img{ min-width:50px; min-height:30px; cursor:pointer; }
.satish_editor_v1 img[src=""]{ background-color:#ccc; }

.satish_editor_v1 ul{ padding-bottom: 10px; }
.satish_editor_v1 a:empty:before {content: '       ';white-space: pre;}
.satish_editor_v1 p:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 div:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 span:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 h1:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 h2:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 h3:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 h4:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 pre:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 li:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 td:empty:before {content: ' ';white-space: pre;}
.satish_editor_v1 th:empty:before {content: ' ';white-space: pre;}


.satish_editor_controlls .contextmenu{ position: absolute; background-color: white; border: 1px solid #999; box-shadow: 2px 2px 5px #666; z-index: 500; white-space: nowrap; }
.satish_editor_controlls .contextmenu_btn { cursor: pointer; border:1px solid #aaa; }
.satish_editor_controlls .contextmenu_btn:hover { background-color: #ccc; }
.satish_editor_controlls .contextmenu_submenu__{ position: absolute; background-color: white;border: 1px solid #999; box-shadow: 2px 2px 5px #666; padding: 5px; z-index: 402;  }
.satish_editor_controlls .contextsidemenu__{ position: absolute; background-color: white;border: 1px solid #999; box-shadow: 2px 2px 5px #666; padding: 5px; z-index: 501; }
.satish_editor_controlls .contextmenu a{ background-color: #f0f0f0; text-decoration: none; border:1px solid #aaa; padding:0px 3px; }
.satish_editor_controlls .contextmenu a:hover{ background-color: #eee; border:1px solid #999; }
.satish_editor_controlls .contextmenu .tag_btn{ display:inline-block; padding:5px; border:1px solid #ccc; background-color:white; cursor:pointer; margin-left:2px; }
.satish_editor_controlls .contextmenu .tag_btn_a{cursor:initial; pointer-events:none;}
.satish_editor_controlls .contextmenu .tag_btn:hover{ background-color:#f0f0f0; }


.satish_editor_controlls .side_dragger__{ position:absolute; background-color:rgba(255,255,255,0.5); user-select:none; z-index:510; cursor:pointer; }

.satish_editor_controlls .focused_bounds{ position:absolute; z-index:401; top:100px; left:100px; width:300px; height:300px;outline:3px dashed rgba(155,155,155); pointer-events:none; }
.satish_editor_controlls .focused_bounds_tip{ position:absolute; z-index:401; right:0px; top:-20px; height:20px; background-color: rgb(155,155,155,0.5); cursor: pointer; min-width:20px; text-align: center; color: white; font-weight: bold; font-size:10px; padding: 0px 5px; line-height:20px; overflow:hidden; }

.satish_editor_controlls .table_cell__{ position:absolute; z-index:401; outline:2px solid rgba(0,0,200,0.5); pointer-events:none; }

.satish_editor_controlls .side_settings_bar{ position:absolute; z-index:402; top:100px; left:100px; height:5px; background-color:rgb(255,55,55,0.2); cursor:pointer; }
.satish_editor_controlls .side_settings_bar:hover{}
.satish_editor_controlls .side_settings_bar_sub{ width:100%; height:10px; margin-top:-3px; text-align:center; cursor:pointer; display:none; }
.satish_editor_controlls .side_settings_bar_sub2{ width:100%; height:5px; margin-top:2px; text-align:center; cursor:pointer; background-color:rgb(255,55,55,0.2); }
.satish_editor_controlls .side_settings_bar:hover .side_settings_bar_sub{ height:10px; background-color:rgba(255,5,5,0.2); display:block; }
.satish_editor_controlls .side_settings_bar_sub svg{color:orange !important; margin-top:-5px}

.satish_editor_controlls .drop_target_bar{ position:absolute; z-index:402; top:100px; left:100px; height:5px; background-color:rgb(255,255,255,0.5); cursor:pointer; }

.satish_editor_controlls .link_suggest{ position: absolute; padding: 2px; min-width: 300px; max-width: 600px; max-height: 300px; overflow: auto; z-index: 501; background-color: white; box-shadow: 2px 2px 4px #666; border: 1px solid #333; }
.satish_editor_controlls .link_suggest div {border-bottom: 1px solid #bbb; padding: 2px; cursor: pointer; white-space: nowrap;}
.satish_editor_controlls .link_suggest div.link_suggest_select {background-color: #ffcefe;}
.satish_editor_controlls .link_suggest div:hover {background-color: #ffcefe;}
.satish_editor_controlls .link_suggest div span span{color: red;}
.satish_editor_controlls .link_suggest div span:nth-child(1) {font-weight: 600;}
.satish_editor_controlls .link_suggest div span:nth-child(2) {font-style: italic; font-weight: bold; color: #aaa;}
.satish_editor_controlls .link_suggest div span:nth-child(3) {color: maroon;}
.satish_editor_controlls .image_inline_menu{ position: absolute; z-index: 404; background-color: white; box-shadow: 3px 3px 5px black; padding: 10px; }
.satish_editor_controlls .image_inline_menu a{ text-decoration: none; }

.satish_editor_controlls .image_popup{ position: fixed; min-width: 800px; max-width: 90%; top: 50px; left: 50px; height: calc( 100% - 100px ); z-index: 404; background-color: white; border: 1px solid #666; box-shadow: 2px 2px 5px black; border-radius:5px; }
.satish_editor_controlls .image_popup_back{ position: fixed; width:100%; height: 100%; background-color: rgba(0,0,0,0.5); top: 0px; left: 0px; z-index: 403; }
.satish_editor_controlls .image_popup_head{ border-bottom: 1px solid #333; padding: 10px 20px; border-top-left-radius:5px;border-top-right-radius:5px; }
.satish_editor_controlls .image_popup_head span{ font-weight: bold; }
.satish_editor_controlls .image_popup_cls_btn {float: right; cursor: pointer; border-top: 1px solid #ccc;}
.satish_editor_controlls .image_popup_cls_btn:hover{ color:red; }
.satish_editor_controlls .image_popup_content{ height: calc( 100% - 35px); overflow: auto; padding: 10px; border-radius:5px; }
.satish_editor_controlls .image_popup_tab{ display: inline-block; padding: 0px 5px; margin-right:5px; border: 1px solid #ccc; cursor: pointer; border-bottom:5px solid #ccc; }
.satish_editor_controlls .image_popup_tab:hover{ background-color: #ffe9d1; }
.satish_editor_controlls .image_popup_tab.active{ border-bottom:5px solid orange; }

.satish_editor_controlls .image_crop_popup{ position: fixed; min-width: 500px; max-width: 90%; top: 50px; left: 50px; height: calc( 100% - 100px ); z-index: 404; background-color: white; border: 1px solid #666; box-shadow: 2px 2px 5px black; border-radius:5px; }
.satish_editor_controlls .attachment_popup{ position: fixed; top: 200px; left:200px; border-radius: 1px solid #ccc; box-shadow: 2px 2px 5px black; z-index: 500; background-color: white; }

.satish_editor_controlls .anchor_graph_search_popup{position: absolute;border: 1px solid #bbcccc; padding: 5px; box-shadow: 2px 2px 5px black; z-index: 503;background-color: #f0f0f0; min-height: 50px; min-width: 50px; height:200px;}
.satish_editor_controlls .graph_search_item{padding-left: 2px; border-bottom: 1px solid #ccc; cursor: pointer; padding-right: 10px; white-space: nowrap;}`;
var editor_template__ = `<div>
	<div id="satish_editor_controlls" class="satish_editor_controlls" >
		<div v-if="selection_start__==false" class="focused_bounds"  data-id="bounds" v-bind:style="focused_bounds_style__" >
			<div class="focused_bounds_tip" >{{ focused_type__ + (focused_className__?'.'+focused_className__:'') }}</div>
		</div>
		<div v-if="selection_start__==false" class="table_cell__" data-id="bounds" v-bind:style="table_cell_style__" v-on:contextmenu.prevent.stop="contextmenu_event__" v-on:click.prevent.stop="hide_other_menus__();td_sel_unfocus__()" >
		</div>
		<div class="side_settings_bar" data-id="bounds" v-bind:style="side_settings_style__" v-on:dragenter__="bar_dragenter__"  v-on:dragover__="bar_dragover__" v-on:contextmenu.prevent.stop=""  >
			<div v-if="dragger__==false" class="side_settings_bar_sub" v-on:click.prevent.stop="side_settings_insert_form__" >
				<svg height="20px" width="20px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 490 490" xml:space="preserve"><g><g><g><path d="M227.8,174.1v53.7h-53.7c-9.5,0-17.2,7.7-17.2,17.2s7.7,17.2,17.2,17.2h53.7v53.7c0,9.5,7.7,17.2,17.2,17.2 	 s17.1-7.7,17.1-17.2v-53.7h53.7c9.5,0,17.2-7.7,17.2-17.2s-7.7-17.2-17.2-17.2h-53.7v-53.7c0-9.5-7.7-17.2-17.1-17.2 					S227.8,164.6,227.8,174.1z"/><path d="M71.7,71.7C25.5,118,0,179.5,0,245s25.5,127,71.8,173.3C118,464.5,179.6,490,245,490s127-25.5,173.3-71.8 					C464.5,372,490,310.4,490,245s-25.5-127-71.8-173.3C372,25.5,310.5,0,245,0C179.6,0,118,25.5,71.7,71.7z M455.7,245 					c0,56.3-21.9,109.2-61.7,149s-92.7,61.7-149,61.7S135.8,433.8,96,394s-61.7-92.7-61.7-149S56.2,135.8,96,96s92.7-61.7,149-61.7 					S354.2,56.2,394,96S455.7,188.7,455.7,245z"/></g></g></g></svg>
			</div>
		</div>
		<div class="drop_target_bar" data-id="bounds" v-bind:style="drop_target_style__" v-on:dragenter__="bar_dragenter__" v-on:dragover__="bar_dragover__" v-on:drop="bar_drop__" >
			<div v-bind:style="drop_target_style2__">&nbsp;</div>
		</div>
		<div v-if="dragger__" class="side_dragger__" data-id="bounds" v-bind:style="dragger_style__" v-on:mousemove__.stop="dragger_mousemove__" v-on:mouseup__.stop="dragger_mouseup__" v-on:mouseout.stop="dragger_mouseup__" draggable="true" >
			<div style="margin-left:30px;width:200px;max-height:200px;overflow: hidden;" >
				<img v-bind:src="dragger_img__" style="" >
			</div>
		</div>
		<div class="side_settings_bar" data-id="bounds" v-bind:style="side_settings_style2__" v-on:mousedown__.stop="dragger_mousedown__" v-on:mouseup__.stop="dragger_mouseup__">
			<div style="width:20px; height: 20px; background-color:#f8f8f8; text-align:center; font-weight:1.2rem; line-height: 15px; border:1px solid #f0f0f0; "  >:::</div>
		</div>
		<!-- mouse move highlight -->

		<div v-if="contextsidemenu__" data-id="contextmenu" class="contextsidemenu__" v-bind:style="contextsidemenu_style__" >
			<div v-if="contextsidemenu_type__=='td_insert'" >
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_insert_column__('left')" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Insert Column Left</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_insert_column__('right')" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Insert Column Right</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_insert_row__('top')" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Insert Row Top</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_insert_row__('bottom')" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Insert Row Bottom</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_move_cells_right__()" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Move cells__ Right</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_move_cells_bottom__()" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Move cells__ Bottom</div>
				</div>
			</div>
			<div v-if="contextsidemenu_type__=='td_delete'" >
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_delete_row__()" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Delete Row</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_delete_column__()" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Delete Column</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_delete_table__()" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Delete Table</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_move_cells_left__()" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Move cells__ Left</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_move_cells_top__()" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Move cells__ Top</div>
				</div>
			</div>
			<div v-if="contextsidemenu_type__=='td_copy'" >
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_duplicate_row__()" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Duplicate Row</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_duplicate_column__()" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Duplicate Column</div>
				</div>
			</div>
			<div v-if="contextsidemenu_type__=='table_theme'" >
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_apply_settings__('theme','none',$event)" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>None</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_apply_settings__('theme','blue',$event)" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Blue</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_apply_settings__('theme','green',$event)" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Green</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_apply_settings__('theme','red',$event)" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Red</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_apply_settings__('theme','orange',$event)" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Orange</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_apply_settings__('theme','purple',$event)" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Purple</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_apply_settings__('theme','gray',$event)" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Gray</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_apply_settings__('theme','light',$event)" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Light</div>
				</div>
				<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_apply_settings__('theme','dark',$event)" style="display:flex;">
					<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
					<div>Dark</div>
				</div>
			</div>
		</div>
		<div v-if="contextmenu__" data-id="contextmenu__" class="contextmenu" v-bind:style="contextmenu_style__" >
			<template v-if="contextmenu_submenu__" >
				<div style="background-color: #f0f0f0; padding: 5px; column-gap:10px;">
					<div style="display: flex; column-gap: 10px;cursor: pointer;" v-on:click="contextmenu_submenu__=false">
						<svg role="graphics-symbol" viewBox="0 0 16 16" class="arrowLeftThick" style="width: 16px; height: 16px; display: block; fill: rgba(55, 53, 47, 0.45); flex-shrink: 0;"><path d="M1.54004 8.05762C1.54004 8.2627 1.62891 8.46094 1.78613 8.61133L6.29102 13.1162C6.45508 13.2734 6.63965 13.3486 6.82422 13.3486C7.25488 13.3486 7.5625 13.041 7.5625 12.6309C7.5625 12.4189 7.48047 12.2344 7.34375 12.1045L5.8125 10.5527L3.78906 8.70703L5.38867 8.80273H13.7012C14.1455 8.80273 14.46 8.49512 14.46 8.05762C14.46 7.61328 14.1455 7.3125 13.7012 7.3125H5.38867L3.7959 7.4082L5.8125 5.5625L7.34375 4.01074C7.48047 3.87402 7.5625 3.68945 7.5625 3.47754C7.5625 3.06738 7.25488 2.7666 6.82422 2.7666C6.63965 2.7666 6.45508 2.83496 6.27734 3.00586L1.78613 7.50391C1.62891 7.64746 1.54004 7.85254 1.54004 8.05762Z"></path></svg>
						<div>
							<div v-if="contextmenu_type__=='context'" >Back to Block Settings</div>
							<div v-else >Back to Main Menu</div>
						</div>
					</div>
				</div>
				<div style="padding: 5px; height: 300px; overflow: auto;">
					<div v-if="contextmenu_submenu_type__=='A'" style="width:350px;resize:both;" >
						<div>Text:</div>
						<div><input type="text" class="form-control form-control-sm" v-model="anchor_text__" placeholder="Content" ></div>
						<div>
							<label style="cursor:pointer; margin-right: 30px;" ><input type="radio" v-model="anchor_type__" value="url" > URL</label>
							<label style="cursor:pointer;" ><input type="radio" v-model="anchor_type__" value="graph" > Graph Nodes</label>
						</div>
						<div v-if="anchor_type__=='url'"><input type="text" class="form-control form-control-sm" v-model="anchor_href__" placeholder="URL" ></div>
						<div v-if="anchor_type__=='graph'">
							<div class="text-secondary" v-on:click.stop="show_anchor_graph_search_window__">{{ anchor_graph__['v'] }} ({{ anchor_graph__['i'] }}) <a v-bind:href="anchor_href__" target="_blank" v-on:click.stop >#</a></div>
						</div>
						<div>&nbsp;</div>
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="Create" v-on:click.prevent.stop="anchor_create__" ></div>
					</div>
					<div v-else-if="contextmenu_submenu_type__=='change_to'" style="width:350px;resize:both;" >
							<P>Convert {{ focused_type__ }}</P>
							<div v-for="td in change_to_types_applicable__()" class="contextmenu_btn" style="display:flex;" v-on:click="convert_tag_to__(td)" >to {{ td }}</div>
					</div>
					<template v-else-if="contextmenu_submenu_type__=='color'" >
						<div class="satish_editor_v1" style="display:flex; column-gap:10px;">
							<div>
								<div>ForeGround</div>
								<div v-on:click="apply_text_color__('none')" >None</div>
								<div v-for="c,ci in text_colors__" style="cursor:pointer;border:1px solid #ccc;" v-bind:class="'fg-'+c" v-on:click="apply_text_color__('fg-'+c)" >{{ c }}</div>
							</div>
							<div>
								<div>Background</div>
								<div v-on:click="apply_background_color__('none')" >None</div>
								<div v-for="c,ci in text_colors__" style="cursor:pointer;border:1px solid #ccc;" v-bind:class="'bg-'+c" v-on:click="apply_background_color__('bg-'+c)" >{{ c }}</div>
							</div>
						</div>
					</template>
					<template v-else-if="contextmenu_submenu_type__=='table_settings__'" >
						<template v-if="'striped' in table_settings__" >
							<div class="contextmenu_btn" v-on:click.stop.prevent="table_toggle_settings__('striped',$event)"  style="display:flex; column-gap: 10px;">
								<div><input type="checkbox" v-bind:checked="table_settings__['striped']=='yes'" ></div>
								<div>Striped</div>
							</div>
						</template>
						<template v-if="'hover' in table_settings__" >
							<div class="contextmenu_btn" v-on:click.stop.prevent="table_toggle_settings__('hover',$event)"  style="display:flex; column-gap: 10px;">
								<div><input type="checkbox" v-bind:checked="table_settings__['hover']=='yes'" ></div>
								<div>Hover</div>
							</div>
						</template>
						<template v-if="'header' in table_settings__" >
							<div class="contextmenu_btn" v-on:click.stop.prevent="table_toggle_settings__('header',$event)"  style="display:flex; column-gap: 10px;">
								<div><input type="checkbox" v-bind:checked="table_settings__['header']=='yes'" ></div>
								<div>Header</div>
							</div>
						</template>
						<template v-if="'footer' in table_settings__" >
							<div class="contextmenu_btn" v-on:click.stop.prevent="table_toggle_settings__('footer',$event)"  style="display:flex; column-gap: 10px;">
								<div><input type="checkbox" v-bind:checked="table_settings__['footer']=='yes'" ></div>
								<div>Footer</div>
							</div>
						</template>
						<template v-if="'colheader' in table_settings__" >
							<div class="contextmenu_btn" v-on:click.stop.prevent="table_toggle_settings__('colheader',$event)"  style="display:flex; column-gap: 10px;">
								<div><input type="checkbox" v-bind:checked="table_settings__['colheader']=='yes'" ></div>
								<div>Column Header</div>
							</div>
						</template>
						<template v-if="'theme' in table_settings__" >
							<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="show_context_sidemenu__('table_theme',$event)" style="display:flex;">
								<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
								<div>Theme: {{ table_settings__['theme'] }}</div>
							</div>
						</template>
						<template v-if="'border' in table_settings__" >
							<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="show_context_sidemenu__('table_border',$event)" style="display:flex;">
								<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
								<div>Border: {{ table_settings__['border'] }}</div>
							</div>
						</template>
					</template>
				</div>
			</template>
			<template v-else-if="contextmenu_type__=='table-cell'" >
				<div style="background-color: #f0f0f0; padding: 5px;margin-bottom: 10px;">
					Table cells__ Selected
				</div>
				<div style="padding: 5px; min-height: 100px;">
					<div class="contextmenu_btn" style="display:flex;" v-on:click.prevent.stop="table_cells_delete__" >
						<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm1.6-3.8a1 1 0 0 1-1.2 1.6l-3-2a1 1 0 0 1 0-1.6l3-2a1 1 0 0 1 1.2 1.6L6.8 12l1.8 1.2z" fill-rule="evenodd"/></svg>
						<div>Delete Contents</div>
					</div>
					<div class="contextmenu_btn" title="Copy Tag" v-on:click.stop.prevent="table_cells_copy__" style="display:flex;">
						<svg width="15px" height="15px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z" stroke="#1C274C" stroke-width="1.5"/>
							<path opacity="0.5" d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5" stroke="#1C274C" stroke-width="1.5"/>
						</svg>
						<div> Copy</div>
					</div>
					<div class="contextmenu_btn" title="Copy Tag" v-on:click.stop.prevent="table_cells_cut__" style="display:flex;">
						<svg width="15px" height="15px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z" stroke="#1C274C" stroke-width="1.5"/>
							<path opacity="0.5" d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5" stroke="#1C274C" stroke-width="1.5"/>
						</svg>
						<div> Cut</div>
					</div>
					<div v-if="table_cells_clipboard__" class="contextmenu_btn" title="Paste Tag" v-on:click.stop.prevent="table_cells_paste_cells__()" style="display:flex;">
						<svg width="15px" height="15px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 21h5v-1H4V4h2v2h10V4h2v3h.4a.989.989 0 0 1 .6.221V3h-3V2h-3a2 2 0 0 0-4 0H6v1H3zM7 3h3V1.615A.615.615 0 0 1 10.614 1h.771a.615.615 0 0 1 .615.615V3h3v2H7zm4 14h9v1h-9zM9 8v16h13V11.6L18.4 8zm12 15H10V9h7v4h4zm0-11h-3V9h.31L21 11.69zm-10 2h9v1h-9zm0 6h7v1h-7z"/><path fill="none" d="M0 0h24v24H0z"/></svg>
						<div> Paste</div>
					</div>
					<div class="contextmenu_btn" style="display:flex;" v-on:click.prevent.stop="table_cells_clean_text__" >
						<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
						<div>Clean Text</div>
					</div>
					<div class="contextmenu_btn" style="display:flex;" v-on:click.prevent.stop="table_cells_clean_styles__" >
						<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
						<div>Remove Styles</div>
					</div>
					<div class="contextmenu_btn" style="display:flex;" v-on:click.prevent.stop="table_cells_delete_columns__" >
						<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
						<div>Delete Columns</div>
					</div>
					<div class="contextmenu_btn" style="display:flex;" v-on:click.prevent.stop="table_cells_delete_rows__" >
						<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
						<div>Delete Rows</div>
					</div>
				</div>
			</template>
			<template v-else-if="contextmenu_type__=='context'" >
				<div style="background-color: #f0f0f0; padding: 5px;margin-bottom: 10px;">
					<div v-if="focused_tree__.length>0" class="mb-1">
						<div v-if="focused_tree__.length>2" class="tag_btn" v-on:click="set_focus_to__(2)" >{{ focused_tree__[2]['a'] }}</div>
						<div v-if="focused_tree__.length>1" class="tag_btn" v-on:click="set_focus_to__(1)" >{{ focused_tree__[1]['a'] }}</div>
						<div v-if="focused_tree__.length>0" class="tag_btn tag_btn_a" >{{ focused_tree__[0]['a'] }}</div>
					</div>
				</div>
				<div style="padding: 5px; min-height: 100px;">
					<div v-if="focused_anchor__" >
						<div class="contextmenu_btn" style="display:flex;" v-on:click.prevent.stop="anchor_remove__" >
							<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm1.6-3.8a1 1 0 0 1-1.2 1.6l-3-2a1 1 0 0 1 0-1.6l3-2a1 1 0 0 1 1.2 1.6L6.8 12l1.8 1.2z" fill-rule="evenodd"/></svg>
							<div>Remove Link</div>
						</div>
						<div class="contextmenu_btn" style="display:flex;" v-on:click.prevent.stop="anchor_edit__" >
							<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm1.6-3.8a1 1 0 0 1-1.2 1.6l-3-2a1 1 0 0 1 0-1.6l3-2a1 1 0 0 1 1.2 1.6L6.8 12l1.8 1.2z" fill-rule="evenodd"/></svg>
							<div>Edit Link</div>
						</div>
					</div>
					<div v-if="change_to_types_allowed__()" class="contextmenu_btn" style="display:flex;" v-on:click="show_context_submenu__('change_to')" >
						<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm1.6-3.8a1 1 0 0 1-1.2 1.6l-3-2a1 1 0 0 1 0-1.6l3-2a1 1 0 0 1 1.2 1.6L6.8 12l1.8 1.2z" fill-rule="evenodd"/></svg>
						<div>Turn Into</div>
					</div>
					<div class="contextmenu_btn" style="display:flex;" v-on:click="delete_tag__">
						<svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 11V17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M14 11V17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M4 7H20" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6 7H12H18V18C18 19.6569 16.6569 21 15 21H9C7.34315 21 6 19.6569 6 18V7Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7H9V5Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
						<div>Delete Block</div>
					</div>
					<div class="contextmenu_btn" v-if="is_duplicatable__()" style="display:flex;" >
						<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm1.6-3.8a1 1 0 0 1-1.2 1.6l-3-2a1 1 0 0 1 0-1.6l3-2a1 1 0 0 1 1.2 1.6L6.8 12l1.8 1.2z" fill-rule="evenodd"/></svg>
						<div>Duplicate</div>
					</div>
					<div v-if="convertible_to_text__()" >
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="innerhtml_to_text__()" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Clean Text</div>
						</div>
					</div>
					<div v-if="convertible_to_paragraph__()" >
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="innerhtml_to_blocks__()" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>
								<div>Text to Block</div>
								<div class="text-secondary small">Enable blocks</div>
							</div>
							
						</div>
					</div>
					<div class="contextmenu_btn" style="display:flex;" v-on:click="show_context_submenu__('color')" >
						<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm1.6-3.8a1 1 0 0 1-1.2 1.6l-3-2a1 1 0 0 1 0-1.6l3-2a1 1 0 0 1 1.2 1.6L6.8 12l1.8 1.2z" fill-rule="evenodd"/></svg>
						<div>Color</div>
					</div>
					<div v-if="focused_type__=='LI'||focused_li__" class="contextmenu_btn"  title="Un Indent" v-on:click.stop.prevent="make_unindent__" style="display:flex;">
						<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm1.6-3.8a1 1 0 0 1-1.2 1.6l-3-2a1 1 0 0 1 0-1.6l3-2a1 1 0 0 1 1.2 1.6L6.8 12l1.8 1.2z" fill-rule="evenodd"/></svg>
						<div>Unindent</div>
					</div>
					<div v-if="focused_type__=='LI'||focused_li__" class="contextmenu_btn" title="Indent Item" v-on:click.stop.prevent="make_indent__" style="display:flex;">
						<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm-2.6-3.8L6.2 12l-1.8-1.2a1 1 0 0 1 1.2-1.6l3 2a1 1 0 0 1 0 1.6l-3 2a1 1 0 1 1-1.2-1.6z" fill-rule="evenodd"/></svg>
						<div>Indent</div>
					</div>
					<div v-if="focused_type__=='LI'||focused_li__" class="contextmenu_btn" title="Indent Item" v-on:click.stop.prevent="li_insert_above" style="display:flex;">
						<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm-2.6-3.8L6.2 12l-1.8-1.2a1 1 0 0 1 1.2-1.6l3 2a1 1 0 0 1 0 1.6l-3 2a1 1 0 1 1-1.2-1.6z" fill-rule="evenodd"/></svg>
						<div>Insert Above</div>
					</div>
					<div v-if="focused_type__=='LI'||focused_li__" class="contextmenu_btn" title="Indent Item" v-on:click.stop.prevent="li_insert_below" style="display:flex;">
						<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm-2.6-3.8L6.2 12l-1.8-1.2a1 1 0 0 1 1.2-1.6l3 2a1 1 0 0 1 0 1.6l-3 2a1 1 0 1 1-1.2-1.6z" fill-rule="evenodd"/></svg>
						<div>Insert Below</div>
					</div>
					<div v-if="focused_type__=='TD'||focused_type__=='TH'" >
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="show_context_sidemenu__('td_insert',$event)" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Insert</div>
						</div>
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="show_context_sidemenu__('td_delete',$event)" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Delete</div>
						</div>
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="show_context_sidemenu__('td_copy',$event)" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Copy</div>
						</div>
						<div v-if="table_cells_clipboard__" class="contextmenu_btn" title="Paste Tag" v-on:click.stop.prevent="table_cells_paste_cells__()" style="display:flex;">
							<svg width="15px" height="15px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 21h5v-1H4V4h2v2h10V4h2v3h.4a.989.989 0 0 1 .6.221V3h-3V2h-3a2 2 0 0 0-4 0H6v1H3zM7 3h3V1.615A.615.615 0 0 1 10.614 1h.771a.615.615 0 0 1 .615.615V3h3v2H7zm4 14h9v1h-9zM9 8v16h13V11.6L18.4 8zm12 15H10V9h7v4h4zm0-11h-3V9h.31L21 11.69zm-10 2h9v1h-9zm0 6h7v1h-7z"/><path fill="none" d="M0 0h24v24H0z"/></svg>
							<div> Paste</div>
						</div>
					</div>
					<div v-if="focused_type__=='TR'" >
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_insert_row2__('top')" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Insert Row Above</div>
						</div>
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_insert_row2__('bottom')" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Insert Row Below</div>
						</div>
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_delete_row2__()" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Delete Row</div>
						</div>
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_duplicate_row2__()" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Duplicate Row</div>
						</div>
					</div>
					<div v-if="focused_type__=='TD'||focused_type__=='TH'||focused_type__=='TR'" >
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_split__()" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Split Table</div>
						</div>
					</div>
					<div v-if="focused_type__=='TBODY'||focused_type__=='THEAD'||focused_type__=='TFOOT'||focused_type__=='TABLE'" >
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="table_delete_table__()" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Delete Table</div>
						</div>
					</div>
					<div v-if="focused_type__=='TD'||focused_type__=='TH'||focused_type__=='TR'||focused_type__=='TBODY'||focused_type__=='THEAD'||focused_type__=='TFOOT'||focused_type__=='TABLE'" >
						<div class="contextmenu_btn" title="Convert" v-on:click.stop.prevent="show_context_submenu__('table_settings__')" style="display:flex;">
							<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
							<div>Table Settings</div>
						</div>
					</div>
				</div>
			</template>
			<template v-else-if="contextmenu_type__=='insert_tag__'">
				<div style="background-color: #f0f0f0; padding: 5px;margin-bottom: 10px;">
					Insert Block
				</div>
				<div style="padding: 5px; min-height: 100px; max-height:200px; overflow: auto;">
					<div>{{ contextmenu_type_name__ }}</div>
					<template v-if="contextmenu_type_name__=='GridColumn'" >
						<div class="contextmenu_btn" v-on:click="insert_item_at_location__('P')" >Paragraph</div>
						<div class="contextmenu_btn" v-on:click="insert_item_at_location__('UL')" >Bullet List</div>
						<div class="contextmenu_btn" v-on:click="insert_item_at_location__('OL')" >Numbered List</div>
						<div class="contextmenu_btn" v-on:click="insert_item_at_location__('IMG')" >Image</div>
						<div class="contextmenu_btn" v-on:click="insert_item_at_location__('PRE')" >Code Snippet</div>
					</template>
					<template v-if="contextmenu_type_name__=='DefListItem'" >
						<div class="contextmenu_btn" v-on:click="insert_item_at_location__('P')" >Paragraph</div>
						<div class="contextmenu_btn" v-on:click="insert_item_at_location__('UL')" >Bullet List</div>
						<div class="contextmenu_btn" v-on:click="insert_item_at_location__('OL')" >Numbered List</div>
						<div class="contextmenu_btn" v-on:click="insert_item_at_location__('IMG')" >Image</div>
						<div class="contextmenu_btn" v-on:click="insert_item_at_location__('PRE')" >Code Snippet</div>
					</template>
					<template v-else >
						<div class="contextmenu_btn" v-for="td,ti in config_tags__" v-on:click="insert_item_at_location__(ti)" >{{ td }}</div>
					</template>
				</div>
			</template>
			<template v-else-if="contextmenu_type__=='inline'">
				<div style="background-color: #f0f0f0; padding: 5px;margin-bottom: 10px;">
					Text Format
				</div>
				<div style="padding: 5px; min-height: 100px;">
					<div class="contextmenu_btn" title="Bold Text" v-on:click.prevent.stop="make_bold__" style="display:flex;">
						<svg height="24" width="24"><path d="M7.8 19c-.3 0-.5 0-.6-.2l-.2-.5V5.7c0-.2 0-.4.2-.5l.6-.2h5c1.5 0 2.7.3 3.5 1 .7.6 1.1 1.4 1.1 2.5a3 3 0 0 1-.6 1.9c-.4.6-1 1-1.6 1.2.4.1.9.3 1.3.6s.8.7 1 1.2c.4.4.5 1 .5 1.6 0 1.3-.4 2.3-1.3 3-.8.7-2.1 1-3.8 1H7.8zm5-8.3c.6 0 1.2-.1 1.6-.5.4-.3.6-.7.6-1.3 0-1.1-.8-1.7-2.3-1.7H9.3v3.5h3.4zm.5 6c.7 0 1.3-.1 1.7-.4.4-.4.6-.9.6-1.5s-.2-1-.7-1.4c-.4-.3-1-.4-2-.4H9.4v3.8h4z" fill-rule="evenodd"/></svg>
						<div>Bold</div>
					</div>
					<div class="contextmenu_btn" title="Italic Text" v-on:click.stop.prevent="make_italic__" style="display:flex;">
						<svg height="24" width="24"><path d="M16.7 4.7l-.1.9h-.3c-.6 0-1 0-1.4.3-.3.3-.4.6-.5 1.1l-2.1 9.8v.6c0 .5.4.8 1.4.8h.2l-.2.8H8l.2-.8h.2c1.1 0 1.8-.5 2-1.5l2-9.8.1-.5c0-.6-.4-.8-1.4-.8h-.3l.2-.9h5.8z" fill-rule="evenodd"/></svg>
						<div>Italic</div>
					</div>
					<div class="contextmenu_btn" title="Bold Text" v-on:click.stop.prevent="make_link__" style="display:flex;">
						<svg height="24" width="24"><path d="M6.2 12.3a1 1 0 0 1 1.4 1.4l-2.1 2a2 2 0 1 0 2.7 2.8l4.8-4.8a1 1 0 0 0 0-1.4 1 1 0 1 1 1.4-1.3 2.9 2.9 0 0 1 0 4L9.6 20a3.9 3.9 0 0 1-5.5-5.5l2-2zm11.6-.6a1 1 0 0 1-1.4-1.4l2-2a2 2 0 1 0-2.6-2.8L11 10.3a1 1 0 0 0 0 1.4A1 1 0 1 1 9.6 13a2.9 2.9 0 0 1 0-4L14.4 4a3.9 3.9 0 0 1 5.5 5.5l-2 2z" fill-rule="nonzero"/></svg>
						<div>Create Link</div>
					</div>
					<div class="contextmenu_btn" title="Clear formatting" v-on:click.stop.prevent="make_clear__" style="display:flex;">
						<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
						<div>Clear Styles</div>
					</div>
					<div class="contextmenu_btn" v-if="focused_type__=='LI'||focused_type__=='TD'||focused_type__=='TH'" title="Convert" v-on:click.stop.prevent="text_to_paragraph__" style="display:flex;">
						<svg height="24" width="24"><path d="M13.2 6a1 1 0 0 1 0 .2l-2.6 10a1 1 0 0 1-1 .8h-.2a.8.8 0 0 1-.8-1l2.6-10H8a1 1 0 1 1 0-2h9a1 1 0 0 1 0 2h-3.8zM5 18h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm13 1.5L16.5 18 15 19.5a.7.7 0 0 1-1-1l1.5-1.5-1.5-1.5a.7.7 0 0 1 1-1l1.5 1.5 1.5-1.5a.7.7 0 0 1 1 1L17.5 17l1.5 1.5a.7.7 0 0 1-1 1z" fill-rule="evenodd"/></svg>
						<div>Convert to Paragraph</div>
					</div>
					<div class="contextmenu_btn" title="Copy Tag" v-on:click.stop.prevent="copy_tag" style="display:flex;">
						<svg width="15px" height="15px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z" stroke="#1C274C" stroke-width="1.5"/>
							<path opacity="0.5" d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5" stroke="#1C274C" stroke-width="1.5"/>
						</svg>
						<div>Copy</div>
					</div>
					<div class="contextmenu_btn" title="Paste Tag" v-on:click.stop.prevent="paste_tag" style="display:flex;">
						<svg width="15px" height="15px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 21h5v-1H4V4h2v2h10V4h2v3h.4a.989.989 0 0 1 .6.221V3h-3V2h-3a2 2 0 0 0-4 0H6v1H3zM7 3h3V1.615A.615.615 0 0 1 10.614 1h.771a.615.615 0 0 1 .615.615V3h3v2H7zm4 14h9v1h-9zM9 8v16h13V11.6L18.4 8zm12 15H10V9h7v4h4zm0-11h-3V9h.31L21 11.69zm-10 2h9v1h-9zm0 6h7v1h-7z"/><path fill="none" d="M0 0h24v24H0z"/></svg>
						<div>Paste</div>
					</div>
					<div class="contextmenu_btn" title="Paste Tag" v-on:click.stop.prevent="paste_tag" style="display:flex;">
						<svg width="15px" height="15px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 21h5v-1H4V4h2v2h10V4h2v3h.4a.989.989 0 0 1 .6.221V3h-3V2h-3a2 2 0 0 0-4 0H6v1H3zM7 3h3V1.615A.615.615 0 0 1 10.614 1h.771a.615.615 0 0 1 .615.615V3h3v2H7zm4 14h9v1h-9zM9 8v16h13V11.6L18.4 8zm12 15H10V9h7v4h4zm0-11h-3V9h.31L21 11.69zm-10 2h9v1h-9zm0 6h7v1h-7z"/><path fill="none" d="M0 0h24v24H0z"/></svg>
						<div>Delete</div>
					</div>
				</div>
			</template>
			<template v-if="contextmenu_type__=='sections'" >
				<div style="background-color: #f0f0f0; padding: 5px;margin-bottom: 10px;">
					Sections
				</div>
				<div style="padding: 5px; min-height: 100px;">
					<div class="btn btn-sm p-0" title="Paste Tag" v-on:click.stop.prevent="paste_tag" style="display:flex;">
						<svg width="15px" height="15px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 21h5v-1H4V4h2v2h10V4h2v3h.4a.989.989 0 0 1 .6.221V3h-3V2h-3a2 2 0 0 0-4 0H6v1H3zM7 3h3V1.615A.615.615 0 0 1 10.614 1h.771a.615.615 0 0 1 .615.615V3h3v2H7zm4 14h9v1h-9zM9 8v16h13V11.6L18.4 8zm12 15H10V9h7v4h4zm0-11h-3V9h.31L21 11.69zm-10 2h9v1h-9zm0 6h7v1h-7z"/><path fill="none" d="M0 0h24v24H0z"/></svg>
						<div>Delete Content</div>
					</div>
					<div class="btn btn-sm p-0" title="Numbered List" v-on:click.stop.prevent="make_ol__" style="display:flex;">
						<svg height="24" width="24"><path d="M10 17h8c.6 0 1 .4 1 1s-.4 1-1 1h-8a1 1 0 0 1 0-2zm0-6h8c.6 0 1 .4 1 1s-.4 1-1 1h-8a1 1 0 0 1 0-2zm0-6h8c.6 0 1 .4 1 1s-.4 1-1 1h-8a1 1 0 1 1 0-2zM6 4v3.5c0 .3-.2.5-.5.5a.5.5 0 0 1-.5-.5V5h-.5a.5.5 0 0 1 0-1H6zm-1 8.8l.2.2h1.3c.3 0 .5.2.5.5s-.2.5-.5.5H4.9a1 1 0 0 1-.9-1V13c0-.4.3-.8.6-1l1.2-.4.2-.3a.2.2 0 0 0-.2-.2H4.5a.5.5 0 0 1-.5-.5c0-.3.2-.5.5-.5h1.6c.5 0 .9.4.9 1v.1c0 .4-.3.8-.6 1l-1.2.4-.2.3zM7 17v2c0 .6-.4 1-1 1H4.5a.5.5 0 0 1 0-1h1.2c.2 0 .3-.1.3-.3 0-.2-.1-.3-.3-.3H4.4a.4.4 0 1 1 0-.8h1.3c.2 0 .3-.1.3-.3 0-.2-.1-.3-.3-.3H4.5a.5.5 0 1 1 0-1H6c.6 0 1 .4 1 1z" fill-rule="evenodd"/></svg>
						<div>Change to Numbered List</div>
					</div>
					<div class="btn btn-sm p-0" title="Bullet list" v-on:click.stop.prevent="make_ul__" style="display:flex;">
						<svg height="24" width="24"><path d="M11 5h8c.6 0 1 .4 1 1s-.4 1-1 1h-8a1 1 0 0 1 0-2zm0 6h8c.6 0 1 .4 1 1s-.4 1-1 1h-8a1 1 0 0 1 0-2zm0 6h8c.6 0 1 .4 1 1s-.4 1-1 1h-8a1 1 0 0 1 0-2zM4.5 6c0-.4.1-.8.4-1 .3-.4.7-.5 1.1-.5.4 0 .8.1 1 .4.4.3.5.7.5 1.1 0 .4-.1.8-.4 1-.3.4-.7.5-1.1.5-.4 0-.8-.1-1-.4-.4-.3-.5-.7-.5-1.1zm0 6c0-.4.1-.8.4-1 .3-.4.7-.5 1.1-.5.4 0 .8.1 1 .4.4.3.5.7.5 1.1 0 .4-.1.8-.4 1-.3.4-.7.5-1.1.5-.4 0-.8-.1-1-.4-.4-.3-.5-.7-.5-1.1zm0 6c0-.4.1-.8.4-1 .3-.4.7-.5 1.1-.5.4 0 .8.1 1 .4.4.3.5.7.5 1.1 0 .4-.1.8-.4 1-.3.4-.7.5-1.1.5-.4 0-.8-.1-1-.4-.4-.3-.5-.7-.5-1.1z" fill-rule="evenodd"/></svg>
						<div>Change to Bullet List</div>
					</div>
					<div v-if="sections_is_all_lis__||focused_li__" class="btn btn-sm p-0" title="Un Indent" v-on:click.stop.prevent="make_unindent__" style="display:flex;">
						<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm1.6-3.8a1 1 0 0 1-1.2 1.6l-3-2a1 1 0 0 1 0-1.6l3-2a1 1 0 0 1 1.2 1.6L6.8 12l1.8 1.2z" fill-rule="evenodd"/></svg>
						<div>Unindent</div>
					</div>
					<div v-if="sections_is_all_lis__||focused_li__" class="btn btn-sm p-0" title="Indent Item" v-on:click.stop.prevent="make_indent__" style="display:flex;">
						<svg height="24" width="24"><path d="M7 5h12c.6 0 1 .4 1 1s-.4 1-1 1H7a1 1 0 1 1 0-2zm5 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm0 4h7c.6 0 1 .4 1 1s-.4 1-1 1h-7a1 1 0 0 1 0-2zm-5 4h12a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2zm-2.6-3.8L6.2 12l-1.8-1.2a1 1 0 0 1 1.2-1.6l3 2a1 1 0 0 1 0 1.6l-3 2a1 1 0 1 1-1.2-1.6z" fill-rule="evenodd"/></svg>
						<div>Indent</div>
					</div>
				</div>
			</template>
		</div>
		<div class="anchor_graph_search_popup" v-if="anchor_graph_search_window__" data-id="bounds" v-bind:style="anchor_graph_search_window_style__" v-on:click.prevent.stop >
			<div><input spellcheck="false" type="text" id="anchor_graph_search_key__" class="form-control form-control-sm" v-model="anchor_graph_search_key__" v-on:keyup="anchor_graph_search_keyup__"  v-on:click.stop ></div>
			<div v-if="anchor_graph_search_msg__" class="text-success" v-on:click.prevent.stop >{{ anchor_graph_search_msg__ }}</div>
			<div v-if="anchor_graph_search_err__" class="text-danger"  v-on:click.prevent.stop >{{ anchor_graph_search_err__ }}</div>
			<div v-for="fv,fi in anchor_graph_keywords_filtered__" class="graph_search_item" v-on:click.stop="anchor_graph_search_select__(fv)" v-html="fv['r']" ></div>
		</div>
	</div>
</div>
`;
editor_component['template'] = editor_template__;
editor_component['css_template__'] = css_template__;
