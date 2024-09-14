function check_article_body_parts( vbody ){
	//console.log( vbody );
	vbody = vbody.replace( /\>[\r\n\t]+\</g, "><");
	vbody = vbody.replace( /\<p\>\&nbsp\;\<\/p\>/g, "");
	vbody = vbody.replace( /\<p\>[\r\n\ \t]+\<\/p\>/g, "");
	vbody = vbody.replace( /\<p\>\<\/p\>/g, "");
	//console.log( vbody );
	//vbody = vbody.replace( /\<p\>\<img(.*?)\>\<\/p\>/
	var vparts = vbody.split( /\</g );
	var vparts2 = [];
	for(var j=0;j<vparts.length;j++){
		if( j > 0 ){
			var v = "<" + vparts[j];
		}else{
			var v= vparts[j];
		}
		var vv = v.split( /\>/g );
		for(var x=0;x<vv.length;x++){
			if( x == vv.length-1 ){
				vparts2.push( vv[x] );
			}else{
				vparts2.push( vv[x] + ">" );
			}
		}
	}
	for(var j=0;j<vparts2.length;j++){
		if( vparts2[j]==""){
			vparts2.splice(j,1);
			j--;
		}
		//console.log
	}
	for(var j=0;j<vparts2.length-2;j++){
		if( vparts2[j]=="<p>" && vparts2[j+1].match(/^\<img/) && vparts2[j+2]=="</p>" ){
			vparts2.splice(j,1);
			vparts2.splice(j+1,1);
			j--;
		}
	}
	for(var j=0;j<vparts2.length-2;j++){
		if( vparts2[j]=="<p>" && vparts2[j+1].match(/^[\r\n\ \t]+$/) && vparts2[j+2]=="</p>" ){
			vparts2.splice(j,3);
			j--;
		}
	}
	for(var j=0;j<vparts2.length-2;j++){
		if( vparts2[j]=="<p>" && vparts2[j+1]=="</p>" ){
			vparts2.splice(j,2);
			j--;
		}
	}
	for(var j=0;j<vparts2.length-2;j++){
		if( vparts2[j]=="<p>" && vparts2[j+1].match(/^\<table/) ){
			vparts2.splice(j,1);
			j--;
		}
		if( vparts2[j]=="</p>" && vparts2[j+1] == "</table>" ){
			vparts2.splice(j,1);
			j--;
		}
	}
	//return 0;
	var vt = "";
	vsecid = 0;
	vsub = 0;
	var vsections = [ [] ];
	for(var j=0;j<vparts2.length;j++){
		if( vt == "" ){
			if( vparts2[j].match( /^\<(table|h1|h2|h3|img|pre|p|ul|ol)/ ) ){
				k = vparts2[j].match( /^\<(table|h1|h2|h3|img|pre|p|ul|ol)/ );
				if( k[1] != "img" ){
					vt = k[1];
				}
				vsub = 0;
				vsecid++;
				vsections.push([]);
				vsections[vsecid].push( vparts2[j] );
				if( k[1] == "img" ){
					vsections.push([]);
					vsecid++;
				}
			}else{
				vsections[vsecid].push( vparts2[j] );
			}
		}else if( vt != "" ){
			if( vparts2[j].match( /^\<(table|h1|h2|h3|pre|p|ul|ol)/ ) ){
				k = vparts2[j].match( /^\<(table|h1|h2|h3|pre|p|ul|ol)/ );
				if( k[1] == vt ){
					vsub++;
				}
				vsections[vsecid].push( vparts2[j] );
			}else if( vsub == 0 ){
				k = vparts2[j].match( /^\<\/(table|h1|h2|h3|pre|p|ul|ol)/ );
				/*if( vt == "p" && vparts2[j].match( /^\<img/ ) ){
					vsections[vsecid].push("</p>");
					vsections.push([]);
					vsecid++;
					vsections[vsecid].push( vparts2[j] );
					vsections.push([]);
					vsecid++;
				}else
				*/
				if( vparts2[j].match( /^\<\/(table|h1|h2|h3|pre|p|ul|ol)/ ) ){
					k = vparts2[j].match( /^\<\/(table|h1|h2|h3|pre|p|ul|ol)/ );
					if( k[1] == vt ){
						if( vsub == 0 ){
							vsections[vsecid].push( vparts2[j] );
							vsecid++;
							vsections.push([]);
							vt = "";
						}else{
							vsections[vsecid].push( vparts2[j] );
						}
					}else{
						vsections[vsecid].push( vparts2[j] );
					}
				}else{
					vsections[vsecid].push( vparts2[j] );
				}
			}else if( vsub ){
				if( vparts2[j].match( /^\<\/(table|h1|h2|h3|pre|p|ul|ol)/ ) ){
					k = vparts2[j].match( /^\<\/(table|h1|h2|h3|pre|p|ul|ol)/ );
					if( k[1] == vt ){
						vsub--;
					}
					vsections[vsecid].push( vparts2[j] );
				}else{
					vsections[vsecid].push( vparts2[j] );
				}
			}
		}else{
			vsections[vsecid].push( vparts2[j] );
		}
	}
	for(j=0;j<vsections.length;j++){
		vsections[j] = vsections[j].join("\n");
	}
	if( vsections[0] == "" ){ vsections.splice(0,1); }
	if( vsections[ vsections.length-1 ] == "" ){ vsections.splice( vsections.length-1 ,1); }
	for(j=0;j<vsections.length-1;j++){
		if( vsections[j].trim() == ""){
			vsections.splice(j,1);
			j--;
		}
	}
	if(1==2){ // joining of sections
	for(j=0;j<vsections.length-1;j++){
		if( vsections[j].match(/^\<(p|ul|ol|div|a)[\ \>]/) && vsections[j+1].match(/^\<(p|ul|ol|div|a)[\ \>]/) && vsections[j].length < 2048 ){
			vsections[j] = vsections[j] + vsections[j+1];
			vsections.splice(j+1,1);
			j--;
		}
	}
	}
	//console.log("doing final cleaning of sections");
	for(j=0;j<vsections.length;j++){
		//console.log( vsections[j].substr(0,150) );
		if( vsections[j].trim() == "<p>" || vsections[j].trim()== "</p>" ){
			vsections.splice(j,1);
			j--;
		}
	}
	for(j=0;j<vsections.length;j++){
		//console.log( vsections[j].substr(0,150) );
		if( vsections[j].trim() == "" ){
			vsections.splice(j,1);
			j--;
		}
	}
	return vsections;
}
function cleanpasted( pasted_content_html ){
	pasted_content_html = pasted_content_html.replace(/\<style([\S\s]+?)\<\/style\>/g, "");
	pasted_content_html = pasted_content_html.replace(/\<script([\S\s]+?)\<\/script\>/g, "");
	pasted_content_html = pasted_content_html.replace(/\<iframe([\S\s]+?)\<\/iframe\>/g, "");
	pasted_content_html = pasted_content_html.replace(/\<head([\S\s]+?)\<\/head\>/g, "");
	pasted_content_html = pasted_content_html.replace(/\<pre (.*?)\>/g, "<pre>");
	pasted_content_html = pasted_content_html.replace(/\r\n/g, "");
	var vparts = pasted_content_html.split(/\<pre\>/g );
	var vparts2 = [];
	for(var j=0;j<vparts.length;j++){
		if( j > 0 ){
			var v = "<pre>" + vparts[j];
		}else{
			var v= vparts[j];
		}
		var vv = v.split( /\<\/pre\>/g );
		for(var x=0;x<vv.length;x++){
			if( x == vv.length-1 ){
				vparts2.push( vv[x] );
			}else{
				vparts2.push( vv[x] + "</pre>" );
			}
		}
	}
	for(var j=0;j<vparts2.length;j++){
		if( vparts2[j].match(/^\<pre/) ){
			vparts2[j] = vparts2[j].replace("<pre>", "XXXXXX1");
			vparts2[j] = vparts2[j].replace("</pre>", "XXXXXX2");
			vparts2[j] = vparts2[j].replace(/(\<br(.*?)\>)/gi, "\n");
			vparts2[j] = vparts2[j].replace(/(<([^>]+)>)/gi, "");
			vparts2[j] = vparts2[j].replace(/([\r\n]{2,10})/gi, "\n");
			//vparts2[j] = vparts2[j].replace( /(\&gt\;)/g, ">");
			//vparts2[j] = vparts2[j].replace( /\&lt\;/g, "<");
			//vparts2[j] = vparts2[j].replace( /\&quote\;/g, "\"");
			vparts2[j] = vparts2[j].replace( "XXXXXX1", "<pre>" );
			vparts2[j] = vparts2[j].replace( "XXXXXX2", "</pre>" );
		}else{
			vparts2[j] = cleanpasted2( vparts2[j] );
		}
	}
	vtext = "";
	for(var j=0;j<vparts2.length;j++){if( vparts2[j] ){
		vtext = vtext + vparts2[j];
	}}

	//if( vtext.substr(0,))

	return vtext;
}
function cleanpasted2( pasted_content_html ){
		//pasted_content_html = pasted_content_html.replace(/\<\/(iframe|script|span|div|i|b|html|body)\>/g, "");
		pasted_content_html = pasted_content_html.replace(/[\r\n\t\ ]{2,25}/g, " ");
		pasted_content_html = pasted_content_html.replace(/\<select(.*?)\>/g, "");
		pasted_content_html = pasted_content_html.replace(/\<\/select\>/g, "");
		pasted_content_html = pasted_content_html.replace(/\<option (.*?)\>/g, "<p>");
		pasted_content_html = pasted_content_html.replace(/\<\/option\>/g, "</p>");
		//pasted_content_html = pasted_content_html.replace(/\>[\r\n\t\ ]+\</g, "><");
		var vparts = pasted_content_html.split( /\</g );
		var vparts2 = [];
		for(var j=0;j<vparts.length;j++){
			if( j > 0 ){
				var v = "<" + vparts[j];
			}else{
				var v= vparts[j];
			}
			var vv = v.split( /\>/g );
			for(var x=0;x<vv.length;x++){
				if( x == vv.length-1 ){
					vparts2.push( vv[x] );
				}else{
					vparts2.push( vv[x] + ">" );
				}
			}
		}
		for(var j=0;j<vparts2.length;j++){
			var v = vparts2[j];
			if( v.match(/^\<\!(.*?)\>$/i) ){
				vparts2[j] = "";
			}
			if( v.match(/^\<([a-z0-9\:]+)(.*?)\>$/i) ){
				k = v.match(/^\<([a-z0-9\:]+)(.*?)\>$/i);
				if( k[1].match( /^(p|strong|em|ul|li|ol|td|th|tr|pre|h1|h2|h3|h4|h5)$/i ) ){
					var m = vparts2[j].match(/class\=[\'\"](.*?)[\'\"]/i);
					if( m ){
						if( m[1].match( /(note-alert|note-default|note-info|quote1)/i ) ){
							var c = m[1].match( /(note-alert|note-default|note-info|quote1)/ );
							vparts2[j] = "<"+k[1]+" class=\""+c[1]+"\">";
						}else{
							vparts2[j] = "<" + k[1] + ">";
						}
					}else{
						vparts2[j] = "<" + k[1] + ">";
					}
				}else if( k[1] == "table" ){
					vparts2[j] = "<table>";
				}else if( k[1] == "img" ){
					m = vparts2[j].match(/src\=[\'\"](.*?)[\'\"]/i);
					if( m ){
						vparts2[j] = "<img src=\""+ m[1]+"\">";
					}else{
						vparts2[j] = "";
					}
				}else if( k[1] == "a" ){
					m = vparts2[j].match(/href\=[\'\"](.*?)[\'\"]/i);
					if( m ){
						vparts2[j] = "<a href=\""+ m[1]+"\">";
					}else{
						vparts2[j] = "";
					}
				}else{
					vparts2[j] = "";
				}
			}
			if( v.match(/^\<\/([a-z0-9\:]+)\>$/i) ){
				k = v.match(/^\<\/([a-z0-9\:]+)\>$/i);
				if( k[1].match( /^(p|strong|em|ul|li|ol|table|td|th|tr|pre|h1|h2|h3|h4|h5|a)$/i ) ){
					vparts2[j] = "</" + k[1] + ">";
				}else{
					vparts2[j] = "";
				}
			}
		}
		for(var j=0;j<vparts2.length;j++){if( vparts2[j] =="" ){
			vparts2.splice(j,1);
			j--;
		}}
		for(var j=0;j<vparts2.length-1;j++){
			if( vparts2[j].match(/^\<a/) && vparts2[j+1].match(/^\<img/) && vparts2[j+2] == "</a>" ){
				vparts2[j] = vparts2[j+1]+"";
				vparts2.splice(j+1,1);vparts2.splice(j+1,1);
				j--;
			}
			if( vparts2[j] == "<p>" && vparts2[j+1] == "</p>" ){
				vparts2.splice(j,1);vparts2.splice(j,1);
				j--;
			}
			if( vparts2[j] == "<p>" && ( vparts2[j+1] == "&nbsp;" || vparts2[j+1].match(/^[\r\n\t \ ]+$/) ) && vparts2[j+2] == "</p>" ){
				vparts2.splice(j,1);
				vparts2.splice(j,1);
				vparts2.splice(j,1);
				j--;
			}
		}
		vtext = "";
		for(var j=0;j<vparts2.length;j++){if( vparts2[j] ){
			vtext = vtext + vparts2[j];
		}}
		vtext = vtext.replace(/\<\/a\>\<a/g, "</a><BR><a");
		vtext = vtext.replace(/\<p\>[\r\n\ \t]*\<\/p\>/g, "");
		vtext = vtext.replace(/\<p\>[\r\n\ \t]*\&nbsp\;[\r\n\ \t]*\<\/p\>/g, "");

		if( vtext.substr(0,3) == "<li" ){
			vtext = "<ul>" + vtext;
		}
		if( vtext.substr(0,3) == "<td" ){
			vtext = "<table><tbody><tr>" + vtext;
		}
		if( vtext.substr( vtext.length-5,4) == "</li" ){
			vtext = vtext+"</ul>";
		}
		if( vtext.substr( vtext.length-5,4) == "</td" ){
			vtext = vtext+"</td></tbody></table>";
		}

	return vtext;
}