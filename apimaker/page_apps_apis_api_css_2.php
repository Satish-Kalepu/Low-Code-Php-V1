<style>
	.test_menu_div_a{
		position:fixed;
		top:100px; right:0px; 
		width:30px; height: calc( 100% - 100px ); 
		background-color:white; 
		cursor: pointer;
		border-left:1px solid #cccccc; 
		z-index:5;
	}
	.test_menu_div_a:hover{background-color:f0f0f0; }
	.test_menu_div_b{
		position:fixed;
		top:100px; right:0px; 
		width: calc(100% - 180px); height: calc( 100% - 100px );
		background-color:white; 
		border-left:1px solid #cccccc; 
		z-index:5;
	}
	.codeeditor_block_a{
		position: fixed;
		left:150px; top:100px; 
		height: calc( 100% - 140px ); 
		width:calc( 100% - 180px ); 
		background-color: white; 
		overflow: auto; 
	}
	.codeeditor_block_b{
		position: fixed;
		left:150px; top:100px; 
		height: calc( 100% - 100px ); 
		width: 30px; 
		cursor: pointer;
		background-color: #f8f8f8; 
		overflow: auto; 
	}
	.codeeditor_block_b:hover{ background-color:#f0f0f0; }

	.save_block_a{
		position:fixed; 
		bottom:0px; left:150px; z-index:600; 
		background-color:white; color:black; 
		height:40px; padding:3px; 
		width:calc( 100% - 180px );  
		border-top:1px solid black; 
		font-weight:bold; text-align:center;
	}
	.save_block_aa{
		position:fixed; 
		bottom:0px; left:10px; z-index:600; 
		background-color:white; color:black; 
		height:40px; padding:3px; 
		width:calc( 100% - 20px ); 
		border-top:1px solid black; 
		font-weight:bold; text-align:center;
	}
	.save_block_b{
		position:fixed; 
		bottom:0px; left:150px; z-index:1001; 
		background-color:orange; color:white; 
		height:40px; padding:3px; 
		width:calc( 100% - 180px );  
		border-top:1px solid red; 
		font-weight:bold;
	}
	.save_block_bb{
		position:fixed; 
		bottom:0px; left:10px; z-index:1001; 
		background-color:orange; color:white; 
		height:40px; padding:3px; 
		width:calc( 100% - 20px );  
		border-top:1px solid red; 
		font-weight:bold;
	}



	.myrow1{
		display:flex;
		flex-wrap: nowrap;
		align-items: flex-start;
		border-bottom:1px solid white;
		margin-bottom:5px;
		min-width:700px;
	}
	.mycol1{
		position:relative;
		width:25px;
		flex:0 0 25px;
		-webkit-box-flex: 0;
		min-height:20px;
	}
	.mycol1d{
		position:relative;
		width:10px;
		min-height:20px;
	}
	.disable_btn{ width:10px; min-height:20px; background-color:#f8f8f8; cursor:pointer; align-self: stretch; }
	.disable_btn:hover{ background-color:#aaa; }
	.enable_btn{ width:10px; min-height:20px; background-color:#aaa; cursor:pointer; align-self: stretch; }
	.enable_btn:hover{ background-color:#f8f8f8; }
	.mycol11{
		position:relative;
		width:25px;
		flex:0 0 25px;
		-webkit-box-flex: 0;
		min-height:20px;
		padding:2px;
	}
	.mycol2{
		position:relative;
		width: 45px;
		flex:0 0 45px;
		-webkit-box-flex: 0;
		min-height:20px;
		border-right:1px solid white;
		margin-right:5px;
		background-color:#eeeeee75;
	}
	.mycol3{
		width: calc( 100% - 110px );
		min-height:20px;
		position:relative;
		flex-grow:1;
		flex-basis:0;
		max-width:calc( 100% - 110px );;
		-webkit-box-flex:1;
	}
	
	.mycolv{
		width:98%; max-height:200px; overflow:auto; resize:both;
	}

	.simple-table{color: #000;background-color: #fff;font-size: 0.9rem;line-height: 1.5;width: initial;}
	.simple-table th, .simple-table td{background-color:initial;padding:initial;border: initial;}

	.test_menu_a{height:100%; cursor:pointer;}
	.test_menu_a:hover {background-color:#f0f0f0;}
	.test_menu_pre{background-color:white; border-bottom:1px solid #cdcdcd;margin-bottom:1rem }
	.test_menu_pre_a{padding-right:30px;}
	.test_menu_pre_b{padding-right:00px;}
	[v-cloak]{display:none;}
	
	.table_noborder_padding3 {margin:0px !important; padding:0px !important; border:0px !important; }
	.table_noborder_padding3 > tr > td,.table_noborder_padding3 ~ th{padding:3px !important; border:0px solid #cdcdcd !important;}
	span.cmd1 { background-color:#f0f0f0;  padding:0px 2px; border:1px solid #ccc; }
	pre span.var1{ background-color:#f0f0ff;  padding:0px 2px; border:1px solid #ccc; }
	pre span.stat1{ background-color:#e8ffe8; padding:0px 2px; border:1px solid #ccc; }
	pre span.fun1{ background-color:#ffe8ff;  padding:0px 5px; border:1px solid #ccc; }
	pre span.alert1{ background-color:#ffe8ff;  padding:0px 5px; border:1px solid #ccc; }
	
	div span.var1{ background-color:#f0f0ff;  padding:0px 2px; border:1px solid #ccc; }
	div span.stat1{ background-color:#e8ffe8; padding:0px 2px; border:1px solid #ccc; }
	div span.fun1{ background-color:#ffe8ff;  padding:0px 5px; border:1px solid #ccc; }
	div span.alert1{ background-color:#ffe8ff;  padding:0px 5px; border:1px solid #ccc; }

	.stage_col1{ float:left; width:60px; overflow:hidden; }
	.stage_col2{ float:left; width:calc( 100% - 70px ); clear:right;  }

	.stage_col1_{ float:left; width:110px; overflow:hidden; }
	.stage_col2_{ float:left; width:calc( 100% - 120px ); overflow:auto; max-height:300px; resize:both; clear:right;  }

	.simple_code_t1 { border-collapse:collapse !important; }
	.simple_code_t1 > tbody > tr > td{
		padding:0px 10px !important; border:0px solid #cdcdcd !important;
	}
	.simple_code_t2 { border-collapse:collapse !important; width:99%; }
	.simple_code_t2 > tbody > tr > td{
		padding:5px 3px !important; border-bottom:1px solid #f0f0f0 !important;
	}
	.simple_code_t3 { border-collapse:collapse !important; }
	.simple_code_t3 > tbody > tr > td{
		padding:0px 10px 0px 0px !important; border:0px solid #ababab !important;
	}

	.stagerow{ display: flex; align-items: flex-start;justify-content: flex-start; border-bottom:2px solid #eee; }
	.stagerowcol1{ width:30px; padding: 0px 5px; text-align: right; }
	.stagerowcol2{ width:35px; padding: 0px 5px; text-align: center; }
	
	table.simpleborder { border-collapse:collapse; width:initial; }
	table.simpleborder td, table.simpleborder th { border:1px solid #cdcdcd; padding:2px; }
	
	.stagedisp {
		//display:block;
		cursor:pointer;
		margin:0px; 
		padding:0px; 
		color:black;
		font-size:0.9rem;
		padding:0px 5px;
	}
	.stagedisp::-webkit-scrollbar{width: 5px;height:5px;}
	.stagedisp::-webkit-scrollbar-track {background:#aaa;}
	.stagedisp::-webkit-scrollbar-thumb {background:#f0f0f0;}
	.stagedisp::-webkit-scrollbar-thumb:hover {background:#666;}
	.stagedisp:hover{ background-color:#f8f8f0; }

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

	.s2_unem_txetnoc{
		position:absolute; border:1px solid #bbcccc; padding: 5px; box-shadow: 2px 2px 5px black;z-index:2000;
		background-color: #f0f0f0;
		min-height:50px; min-width:50px;
	}
	.s2_tsil_unem_txetnoc{
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
	.s2_unem_txetnoc .cse{ background-color: #d0d0e0; }

	.s2_sulp_meti_txetnoc{ padding:0px 5px; background-color:lightblue; cursor:pointer; margin-left:10px;}
	.s2_sulp_meti_txetnoc:hover{ background-color:blueviolet; }

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

    .mycol3disabled{
		background-color: #ccc;
	}
	.mycol3disabled *{
		color:#999;
		background-color: #ccc;
	}


    .test_loader2 {
	  width: 60px;
	  height: 40px;
	  position: relative;
	  display: inline-block;
	  --base-color: #263238; /*use your base color*/
	}
	.test_loader2::before {
	  content: '';  
	  left: 0;
	  top: 0;
	  position: absolute;
	  width: 36px;
	  height: 36px;
	  border-radius: 50%;
	  background-color: #FFF;
	  background-image: radial-gradient(circle 8px at 18px 18px, var(--base-color) 100%, transparent 0), radial-gradient(circle 4px at 18px 0px, var(--base-color) 100%, transparent 0), radial-gradient(circle 4px at 0px 18px, var(--base-color) 100%, transparent 0), radial-gradient(circle 4px at 36px 18px, var(--base-color) 100%, transparent 0), radial-gradient(circle 4px at 18px 36px, var(--base-color) 100%, transparent 0), radial-gradient(circle 4px at 30px 5px, var(--base-color) 100%, transparent 0), radial-gradient(circle 4px at 30px 5px, var(--base-color) 100%, transparent 0), radial-gradient(circle 4px at 30px 30px, var(--base-color) 100%, transparent 0), radial-gradient(circle 4px at 5px 30px, var(--base-color) 100%, transparent 0), radial-gradient(circle 4px at 5px 5px, var(--base-color) 100%, transparent 0);
	  background-repeat: no-repeat;
	  box-sizing: border-box;
	  animation: rotationBack 3s linear infinite;
	}
	.test_loader2::after {
	  content: '';  
	  left: 35px;
	  top: 15px;
	  position: absolute;
	  width: 24px;
	  height: 24px;
	  border-radius: 50%;
	  background-color: #FFF;
	  background-image: radial-gradient(circle 5px at 12px 12px, var(--base-color) 100%, transparent 0), radial-gradient(circle 2.5px at 12px 0px, var(--base-color) 100%, transparent 0), radial-gradient(circle 2.5px at 0px 12px, var(--base-color) 100%, transparent 0), radial-gradient(circle 2.5px at 24px 12px, var(--base-color) 100%, transparent 0), radial-gradient(circle 2.5px at 12px 24px, var(--base-color) 100%, transparent 0), radial-gradient(circle 2.5px at 20px 3px, var(--base-color) 100%, transparent 0), radial-gradient(circle 2.5px at 20px 3px, var(--base-color) 100%, transparent 0), radial-gradient(circle 2.5px at 20px 20px, var(--base-color) 100%, transparent 0), radial-gradient(circle 2.5px at 3px 20px, var(--base-color) 100%, transparent 0), radial-gradient(circle 2.5px at 3px 3px, var(--base-color) 100%, transparent 0);
	  background-repeat: no-repeat;
	  box-sizing: border-box;
	  animation: rotationBack 4s linear infinite reverse;
	}
	@keyframes rotationBack {
	  0% {
	    transform: rotate(0deg);
	  }
	  100% {
	    transform: rotate(-360deg);
	  }
	}

	.cal_btn {cursor: pointer;}
	.cal_btn:hover { background-color:#eee; color:black; outline:1px solid black; }


#snackbar {
  min-width: 150px; /* Set a default minimum width */
  background-color: rgba(0,0,0,0.5); /* Black background color */
  text-align: center; /* Centered text */
  position: fixed; /* Sit on top of the screen */
  z-index: 100; /* Add a z-index if needed */
  right: 50px; /* Center the snackbar */
  bottom: 100px; /* 30px from the bottom */
}
#snackbar .snackbard{ margin:5px; padding:5px; border:1px solid #ccc; background-color:white; color:black; }

.help-div{ display:inline; padding:2px; cursor:pointer; background-color:#f8f8f8; padding:0px 5px; }
.help-div:hover{ font-weight:bold; background-color:#e0e0e0; }

.help-div2{ display:inline; padding:2px; cursor:pointer; background-color:#f8f8f8; padding:0px 5px; }
.help-div2:hover{ font-weight:bold; background-color:#e0e0e0; }

</style>
