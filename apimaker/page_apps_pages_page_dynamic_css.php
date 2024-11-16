<style>
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
.structure_editor_div{ position:relative; position:relative; height: 100px;line-height: 20px; font-size:1rem; }

</style>
