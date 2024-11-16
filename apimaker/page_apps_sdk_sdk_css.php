<style>
	.codeline_gt_link{ display:inline-block; min-width:30px; padding:0px 5px; border:1px solid #ccc; }
	.codeline_gt_link:hover{ background-color:#666; color:white; }

	.codeline_thing_pop{
		background-color: #f8f8f8; color:#999;
		padding:0px 3px; text-align: center; min-height: 25px;
		display:inline-block; cursor: pointer;
	}
	.codeline_thing_pop:hover{color:black;}
	.codeline_thing_pop2{
		background-color: #f8ffff; color:black;  font-size:1.2rem; line-height: 20px;
		padding:0px 3px; text-align: center; min-height: 25px;
		display:inline-block; cursor: pointer;
	}
	.codeline_thing_pop2:hover{color:black; background-color: #f0deff;}

	.codeline_thing{
		background-color: white; color:black; display: flex; align-items: start;
	}
	.codeline_thing_sub{
		background-color: #f8f8f0; color:#666; border:1px solid #f0f0e8; padding:0px 5px;
	}

	.code_row{ display:flex; gap:5px; align-items:flex-start; white-space:nowrap; min-width: max-content; }
	.code_line [data-type=dropdown]:empty:before{content: "\feff";}
	.code_line [data-type=dropdown]{
		display: inline-block; position: relative;
		min-width:50px; min-height: 25px;
		outline: 0px; 
		padding-left:5px; padding-right:25px;
		cursor:pointer; 
	}
	.code_line [data-type=dropdown]:after{
	    position: absolute;
	    content: "";
	    top: 10px;
	    right: 5px;
	    width: 0;
	    height: 0;
	    border: 6px solid transparent;
	    border-color: #9490ca transparent transparent transparent;
	}
	.code_line [data-type=dropdown]:hover:after{ 
	    border-color: #333 transparent transparent transparent;
	}
	.code_line [data-type=dropdown2]{ min-width: 20px;border: 1px solid #bbb; cursor:pointer; }
	.code_line [data-type=dropdown2]:empty:before{content: "\feff";}

	.code_line [data-type=dropdown3]:empty:before{content: "\feff";}
	.code_line [data-type=dropdown3]{display: inline-block; position: relative; min-width:50px; min-height: 25px;outline: 0px; padding-left:5px; padding-right:25px;cursor:pointer;}
	.code_line [data-type=dropdown3]:after{position: absolute; content: ""; top: 10px; right: 5px; width: 0; height: 0; border: 6px solid transparent; border-color: #9490ca transparent transparent transparent;}
	.code_line [data-type=dropdown3]:hover:after{border-color: #333 transparent transparent transparent;}

	.code_line [data-type=outdropdown]:empty:before{content: "\feff";}
	.code_line [data-type=outdropdown]{ 
		display: inline-block; position: relative;
		min-width:50px; min-height: 25px;
		outline: 0px; 
		padding-left:5px; padding-right:25px;
		cursor:pointer; 
	}
	.code_line [data-type=outdropdown]:after{ 
	    position: absolute;
	    content: "";
	    top: 10px;
	    right: 5px;
	    width: 0;
	    height: 0;
	    border: 6px solid transparent;
	    border-color: #ddd transparent transparent transparent;
	}
	.code_line [data-type=outdropdown]:hover:after{ 
	    border-color: #333 transparent transparent transparent;
	}
	.code_line [data-type=outdropdown2]{ min-width: 20px; }
	.code_line [data-type=outdropdown2]:empty:before{content: "\feff";}

	.code_line .divbox{
		min-height:50px; max-height:200px;
		min-width:150px; max-width:300px; overflow:auto; background-color:#f8f8f8; 
		border:1px solid #eee; padding-left:10px; padding-right:20px; resize:both;
	}
	.code_line .divbox:empty:before{content: "\feff";}
	.code_line .divbox::-webkit-scrollbar{width: 5px;height:5px;}
	.code_line .divbox::-webkit-scrollbar-track {background:#dabbbb;cursor: default;}
	.code_line .divbox::-webkit-scrollbar-thumb {background:#be8989;cursor: pointer;}
	.code_line .divbox::-webkit-scrollbar-thumb:hover {background:#8f5050;}

	.code_line pre[data-type=objecteditable]{
		cursor:pointer; min-height:50px; max-height:300px;
		min-width:200px; max-width:500px; overflow:auto; background-color:#fffced; 
		border:1px solid #eae0ad; padding-left:10px; padding-right:20px; resize:both;
	}
	.code_line pre[data-type=objecteditable]:empty:before{content: "\feff";}
	.code_line pre[data-type=objecteditable]::-webkit-scrollbar{width: 5px;height:5px;}
	.code_line pre[data-type=objecteditable]::-webkit-scrollbar-track {background:#dabbbb;cursor: default;}
	.code_line pre[data-type=objecteditable]::-webkit-scrollbar-thumb {background:#be8989;cursor: pointer;}
	.code_line pre[data-type=objecteditable]::-webkit-scrollbar-thumb:hover {background:#8f5050;}

	.code_line div[data-type=objecteditable]{
		cursor:pointer; max-height:300px;
		min-width:100px; max-width:300px; overflow:auto; background-color:#fffced; 
		border:1px solid #eae0ad; padding-left:10px; padding-right:20px; resize:both;
	}

	.code_line pre[data-type=payloadeditable]{
		cursor:pointer; min-height:50px; max-height:300px;
		min-width:200px; max-width:300px; overflow:auto; background-color:#fffced; 
		border:1px solid #eae0ad; padding-left:5px; padding-right:20px;
	}
	.code_line pre[data-type=payloadeditable]:empty:before{content: "\feff";}
	.code_line pre[data-type=payloadeditable]::-webkit-scrollbar{width: 5px;height:5px;}
	.code_line pre[data-type=payloadeditable]::-webkit-scrollbar-track {background:#dabbbb;cursor: default;}
	.code_line pre[data-type=payloadeditable]::-webkit-scrollbar-thumb {background:#be8989;cursor: pointer;}
	.code_line pre[data-type=payloadeditable]::-webkit-scrollbar-thumb:hover {background:#8f5050;}

	.code_line [data-type=d],.code_line [data-type=dt]{
		padding:0px 5px; border:1px solid #ccc; background-color: #f8f8f8; cursor: pointer;
	}

	.code_line [data-type=dropdown]{
		background-color: #e8e8f2;
		border:1px solid #c3c3ff;
	}
	.code_line [data-type=dropdown]:hover{
		background-color: #ffe3e3;
	}

	.code_line [data-list=vars]{ 
	    background-color: #fff6fc;
    	border: 1px solid #c688b1;
	}
	.code_line [data-list=vars]:hover{
		background-color: #d5eccd;
	}
	.code_line [data-list=functions]{
		background-color:#fff1ec; border:1px solid #ffbaa0;
	}
	.code_line [data-list=functions]:hover{
		background-color: #ff93ca;
	}
	.code_line [data-list=plugins]{
		background-color: #cbf2ff;
	}
	.code_line [data-list=plugins]:hover{
		background-color: #a4e8ff;
	}
	.code_line [data-list=operator],.code_line [data-list=roperator]{
		background-color: white;
		border:1px solid #bbb;
		font-weight: 500;
	}

	.code_line [validation_error]{ border:1px solid red; }

	.code_line .varsubpre{ color:#aaa; border:1px solid #ffcce6; cursor:pointer; background-color:white; }
	.code_line .varsubpre:before{ content: '->'; display:inline-block; }
	.code_line .varsubpre:hover{ color:black; background-color: #ff93ca; }

	.varsub{ border:0px solid #c080c4; }
	.varsub:hover{ background-color:#ff93ca; }	
	.code_line .varsub{ background-color:#fff1ec; border:1px solid #ffbaa0; }
	.code_line .varsub:hover{ background-color:#ffd6eb; }	

	.varsub-inputs{ border:1px solid #ffa56a; margin-top:-2px; padding:5px; display:inline-grid; grid-column-gap: 5px; grid-template-columns: auto auto auto;  }

	.code_line .thing_type{
		color:#999;
	    border-color: #333 transparent transparent transparent;
	}
	.code_line .thing_type:hover{
	    border-color: #333 transparent transparent transparent;
	}
	.code_line text1{
		min-width:100px;outline:0px; border:1px solid 333; padding: 0px 5px; background-color: #f8e0e0;
	}
	.code_line .editable{
		display:flex;
		min-width:50px; border:1px solid #817fac; background-color: white; color:black;
	}
	.code_line .editable:hover{
		background-color: #ffe3e3;
	}
	.code_line .editable [contenteditable]{
		min-width:50px;
		padding-left:5px;padding-right:10px;
		outline: 0px; display: inline-block;
		max-width:400px;overflow: auto;
	}
	.code_line .editable [contenteditable]::-webkit-scrollbar{width: 5px;height:5px;}
	.code_line .editable [contenteditable]::-webkit-scrollbar-track {background:#dabbbb;cursor: default;}
	.code_line .editable [contenteditable]::-webkit-scrollbar-thumb {background:#be8989;cursor: pointer;}
	.code_line .editable [contenteditable]::-webkit-scrollbar-thumb:hover {background:#8f5050;}
	
	.code_line .editable .editabletextarea{
		min-width:150px; min-height:60px; white-space: nowrap;
		max-width:300px; max-height:200px; overflow: auto; resize:both;
	}
	.code_line .editable .editabletextarea::-webkit-scrollbar{width: 5px;height:5px;}
	.code_line .editable .editabletextarea::-webkit-scrollbar-track {background:#dabbbb;cursor: default;}
	.code_line .editable .editabletextarea::-webkit-scrollbar-thumb {background:#be8989;cursor: pointer;}
	.code_line .editable .editabletextarea::-webkit-scrollbar-thumb:hover {background:#8f5050;}

	.code_line .inlinebtn{display: inline-block;cursor: pointer; padding: 0px 3px;  color:red;}

	.context_menu__{
		position:absolute; border:1px solid #bbcccc; padding: 5px; box-shadow: 2px 2px 5px black;z-index:2000;
		background-color: #f0f0f0;
		min-height:50px; min-width:50px;
	}
	.context_menu_list__{
		overflow: auto; max-height: 200px; min-height: 100px; min-width: 100px; max-width: 450px;
	}
	.context_item{
		padding-left:2px;
		border-bottom:1px solid #ccc;cursor:pointer; padding-right:10px; white-space: nowrap;
	}
	.context_item span{
		color:red;
	}
	.context_item abbr{
		color:gray;
	}
	.context_item:hover{background-color: #e0e0e0; }
	.context_menu__ .cse{ background-color: #d0d0e0; }

	.context_item_plus__{ padding:0px 5px; background-color:lightblue; cursor:pointer; margin-left:10px;}
	.context_item_plus__:hover{ background-color:blueviolet; }

	.tendstooperator2{ color:#eee; }
	.tendstooperator2:hover{ color:black; }

	div.editable div[placeholder]:empty::before {
	    content: attr(placeholder);
	    color: #999; 
	}
	div.editable div[placeholder]:empty:focus::before {
	    content: "";
	}

	.test_loader {
	    width: 48px; height: 48px;
	    border: 5px solid #FFF;
	    border-bottom-color: #FF3D00;
	    border-radius: 50%;
	    display: inline-block;
	    box-sizing: border-box;
	    animation: rotation 1s linear infinite;
    }
    @keyframes rotation {
	    0% {
	        transform: rotate(0deg);
	    }
	    100% {
	        transform: rotate(360deg);
	    }
    } 










	.mid_block_div{
		position: fixed;
		left:150px; top:120px; 
		height:calc( 100% - 120px ); 
		width:calc( 100% - 150px );
		background-color: white; 
		overflow: auto; 
		padding:10px;
	}
	.mid_block_iframe{
		position: fixed;
		left:150px; top:120px; 
		height:calc( 100% - 120px ); 
		width:calc( 100% - 150px );
		background-color: white; 
		overflow: auto; 
	}

	.save_block_a{
		position: fixed;
		left:150px; bottom:0px; 
		height:40px; 
		width:calc( 100% - 150px );
		background-color: #f8f8f8; 
		color:black;
		overflow: hidden; 
		user-select: none;
		border-top:1px solid #ccc;
	}

	.leftbar_scroll::-webkit-scrollbar{width: 7px;height:7px;}
	.leftbar_scroll::-webkit-scrollbar-track {background:#eee;}
	.leftbar_scroll::-webkit-scrollbar-thumb {background:#666;}
	.leftbar_scroll::-webkit-scrollbar-thumb:hover {background:#551;}

.tabs_nav_bar {
    position: relative;
    height: 30px;
    border-bottom: 2px solid #aaa;
    margin-bottom: 10px;
}
.tabs_nav_container{ position:absolute; display:flex; height:30px; width:calc( 100% - 30px ); overflow:hidden; }
.tab_btn{ display: flex; column-gap:10px; margin-left:5px; padding:0px 10px; border:1px solid #ccc;border-top-left-radius:5px;border-top-right-radius:5px; border-bottom:2px solid #aaa; white-space:nowrap; cursor:pointer; align-items:center; }
.tab_btn:hover{background-color:#f8f8f8;}
.tab_btn_active{border:1px solid #999; border-bottom:2px solid white; }
.tab_btn:hover .tab_btn_active{ border-bottom:2px solid white; }

.block_head{ padding:5px; background-color:#e0e0e0; border-bottom:1px solid #aaa; }
.block_content{ padding:5px; min-height:200px;resize:auto; margin-bottom:20px; border:1px solid #aaa; }
.block_editor_div{ position:relative; min-height:200px; overflow:visible; }

.comp_btn{ padding:10px; background-color:#f8f8f8; font-weight:bold; border:1px solid #ccc; }
.comp_btn:hover{ background-color:#f0f0f0; }
.comp_content{ padding:10px; background-color:white; border:1px solid #ccc; }

.structure_btn{ padding:10px; background-color:#f8f8f8; font-weight:bold; border:1px solid #ccc; }
.structure_content{ padding:10px; background-color:white; border:1px solid #ccc; }
.structure_editor_div{ position:relative; min-height:200px; overflow:visible; }

</style>
