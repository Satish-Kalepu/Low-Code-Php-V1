<div id="app" v-cloak >
	<div class="leftbar" >
		<?php require("page_apps_leftbar.php"); ?>
	</div>

	<div style="position: fixed;left:150px; top:40px; height: 40px; width:calc( 100% - 150px ); background-color: white; overflow: hidden; " >
		<div style="padding: 5px 10px;" >
			<div class="btn btn-outline-dark btn-sm" style="float:right; padding:4px 5px;"  v-on:click="save_sdk" >Save</div>
			<h5 class="d-inline">SDK: {{ sdk__['name'] }}</h5>

		</div>
	</div>

	<div v-if="vshow__==false||'structure' in sdk__==false" class="mid_block_div" >Loading...</div>
	<template v-else >
	
		<div class="mid_block_div">
			
		</div>

	</template>

	<div class="modal fade" id="popup_modal" tabindex="-1" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ popup_title }}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div v-if="popup_type=='new_method'" >
						<div>Description</div>
						<div><input type="text" class="form-control form-control-sm" placeholder="Block Description" v-model="new_method['des']" ></div>
						<div>&nbsp;</div>
						<div>Type</div>
						<div><label style="cursor: pointer;" ><input type="radio" v-model="new_method['type']" value="html" > HTML </label></div>
						<div><label style="cursor: pointer;" ><input type="radio" v-model="new_method['type']" value="javascript" > Javascript </label></div>
						<div><label style="cursor: pointer;" ><input type="radio" v-model="new_method['type']" value="style" > CSS </label></div>
						<div>&nbsp;</div>
						<div><input type="button" class="btn btn-outline-dark btn-sm" value="ADD" v-on:click="structure_add_method2()" ></div>
					</div>
					<div v-else>Unknown popup type</div>
				</div>
			</div>
		</div>
	</div>

	<div v-if="float_msg__" style="position:fixed; z-index:500; bottom:10px; right:10px; border-radius:10px; box-shadow: 2px 2px 5px black; background-color: rgba(0,0,220,0.5); padding:10px;" v-on:click.stop.prevent v-html="float_msg__" ></div>
	<div v-if="float_err__" style="position:fixed; z-index:500; bottom:10px; right:10px; border-radius:10px; box-shadow: 2px 2px 5px black; background-color: rgba(220,0,0,0.5); padding:10px;" v-on:click.stop.prevent >
		<div>{{ float_err__ }}</div>
	</div>
	
</div>