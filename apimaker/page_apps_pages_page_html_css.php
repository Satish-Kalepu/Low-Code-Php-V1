<style>
	.editor_block_a{
		position: fixed;
		left:160px; top:90px; 
		height:calc( 100% - 100px - 40px ); 
		width:calc( 100% - 170px - 20px ); 
		background-color: #f8f8f8; 
		overflow: auto; 
		text-align: center;
		user-select: none;
		box-shadow: 2px 2px 5px gray;
	}
	.editor_block_a{
		position: fixed;
		left:160px; top:90px; 
		height:calc( 100% - 100px - 40px ); 
		width:calc( 100% - 170px - 20px ); 
		background-color: #f8f8f8; 
		overflow: auto; 
		text-align: center;
		user-select: none;
		box-shadow: 2px 2px 5px gray;
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

	.tag_btn{ display:inline-block; padding:0px 5px; margin-right:5px; border:1px solid #ccc; cursor:pointer; font-size:0.8rem;}
	.tag_btn_a{ background-color:#f0f0f0; cursor:initial; font-size:0.8rem; }
	.tag_btn:hover{ background-color:#f0e0d0; border:1px solid black; }
	.tag_btn_a:hover{ background-color:#f0f0f0; border:1px solid #ccc; }
	.tag_btn:before{ position: absolute; margin-left:-12px; content:'>'; }

	.editor_page_iframe{
		width:100%;height:100%;
		border:1px solid #ccc;
		background-color: white;
	}

	.editor_page_border{
		text-align: left;
		border:1px solid #ccc;
		background-color: white;
	}
	.editor_border_left{
		position: fixed;
		right:0px; top:80px; 
		height:calc( 100% - 80px - 40px ); 
		width:20px; 
		background-color: #f0f0f0; 
		overflow: hidden;
		cursor:e-resize;
		vertical-align: middle;
		line-height: 95%;
		user-select: none;
	}
	.editor_border_bottom{
		position: fixed;
		bottom:30px; left:150px; 
		width:calc( 100% - 150px - 22px ); 
		height:20px; 
		background-color: #f0f0f0; 
		overflow: hidden;
		cursor:n-resize;
		text-align: center;
		user-select: none;
	}




.editor_toolbar{ }
.editor_toolbar a{ background-color: #f0f0f0; text-decoration: none; border:1px solid #aaa; padding:0px 3px; }
.editor_toolbar a:hover{ background-color: #eee; border:1px solid #999; }
.editor_toolbar button{ padding:0px; margin-right: 0px; border:1px solid #ccc; }
.editor_toolbar_group{ display: inline; margin-bottom: 5px; }
.editor_toolbar2{ z-index: 402; position: fixed; bottom: 5px; left: 100px; min-width: 100px; background-color: white; border:1px solid #999; padding: 2px; box-shadow: -2px 2px 5px #333; }

.table_settings *{ font-size:12px; }

</style>
