<?php 	$fields = [];
	foreach($table["schema"]["default"]["fields"] as $field => $fn ){
		if( $fn["type"] != "_id" && $fn["type"] != "text" && $fn["type"] != "number"){
			$fields[] = $field;
		}
	}
?>
<div class="container-fluid" id="db_list_app" >
	<form method="post" id="dynamodb_export">
		<?php if($_SESSION["export_error"] != ""){?>
		<div class="alert alert-danger"><?=$_SESSION["export_error"]?></div>
		<?php }?>
		<h3>Export</h3>
		<div class="text-danger" v-if=" export_type == 'csv' && fields.length >0 "><span class="text-danger">Fields that can't be import </span>{{fields.join()}}</div>
		<div class="row mb-1">
			<div class="col-2">Schema</div>
			<div class="col-8" >
				<select v-model="selected_schema" name="selected_schema" id="selected_schema" class="form-control form-control-sm" style="width:150px;display:inline;">
					<option v-for="sc,si in table['schema']" v-bind:value="si" >{{ si }}</option>
				</select>
			</div>
		</div>
		<div class="row mb-1">
			<div class="col-2">Filter</div>
			<div class="col-8" >
				<table width="100%">
					<tr>
						<td>
							<select v-model="search_index" class="form-control form-control-sm" style="width:150px;display:inline;" v-on:change="change_index">
								<option value="primary">Primary Index</option>
								<option v-for="v,indexname in table['keys']" v-bind:value="indexname">{{ indexname }}</option>
							</select>
						</td>
						<td>
							<div v-if="search_index=='primary'">
								<table>
									<tr>
										<td><span style="padding: 0px 10px;" >_id = </span></td>
										<td>
											<select v-model="primary_search['c']" class="form-control form-control-sm" style="width:70px;display:inline;">
												<option v-for="f,i in filters" v-bind:value="i" >{{ f }}</option>
											</select>
										</td>
										<td>
											<template v-if="primary_search['c']!='><'">
												<input type="text" autocomplete="off" v-model="primary_search['v']" placeholder="Search"  v-bind:class="{'form-control form-control-sm':true,'border-danger':av}"  style="width:150px;display:inline;" >
											</template>
											<template v-else>
												<input type="text" autocomplete="off" v-model="primary_search['v']" placeholder="From"  v-bind:class="{'form-control form-control-sm':true,'border-danger':av}"  style="width:80px;display:inline;" >
												<input type="text" autocomplete="off" v-model="primary_search['v2']" placeholder="To"  v-bind:class="{'form-control form-control-sm':true,'border-danger':av2}"  style="width:80px;display:inline;" >
											</template>
										</td>
										<td>
											<select v-model="primary_search['sort']" class="form-control form-control-sm" style="width:100px;display:inline;">
												<option value="asc" >Ascending</option>
												<option value="desc" >Descending</option>
											</select>
										</td>
									</tr>
								</table>
							</div>
							<div v-else-if="search_index in table['keys']">
								<table v-if="index_search.length>0">
									<tr v-for="kd,ki in index_search">
										<td><span style="padding: 0px 10px;" >{{ kd['field'].replace(".","->") }}</span></td>
										<td>
											<select v-model="kd['c']" class="form-control form-control-sm" style="width:70px;display:inline;">
												<option v-for="f,i in filters" v-bind:value="i" >{{f}}</option>
											</select>
										</td>
										<td>
											<template v-if="kd['c']!='><'">
												<input v-bind:type="kd['type']" autocomplete="off" v-model="kd['v']" placeholder="Search" v-bind:class="{'form-control form-control-sm':true,'border-danger':bv}"  style="width:150px;display:inline;" >
											</template>
											<template v-else>
												<input v-bind:type="kd['type']" autocomplete="off" v-model="kd['v']" placeholder="From" v-bind:class="{'form-control form-control-sm':true,'border-danger':bv}"  style="width:80px;display:inline;" >
												<input v-bind:type="kd['type']" autocomplete="off" v-model="kd['v2']" placeholder="To" v-bind:class="{'form-control form-control-sm':true,'border-danger':bv2}"  style="width:80px;display:inline;" >
											</template>
										</td>
										<td>
											<select v-model="kd['sort']" class="form-control form-control-sm" style="width:100px;display:inline;">
												<option value="asc" >Ascending</option>
												<option value="desc" >Descending</option>
											</select>
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row mb-1">
			<div class="col-2">Limit</div>
			<div class="col-8" >
				<input name="limit" id="limit" v-model="limit" class="form-control form-control-sm" style="width:150px;display:inline;">
			</div>
		</div>
		<div class="row mb-1">
			<div class="col-2">Delimeter</div>
			<div class="col-8" >
				<select v-model="primary_search['delimeter']" class="form-control form-control-sm" style="width:150px;display:inline;">
					<option v-bind:value="i__" v-for="v__,i__ in delimeter_list">{{v__}}({{i__}})</option>
				</select>
			</div>
		</div>
		<div class="row mb-1">
			<div class="col-2">Export Type</div>
			<div class="col-8" >
				<input type="radio" id="export_csv" v-on:change="blur_update" value="csv" v-model="export_type">
				<label for="export_csv">Download Csv File</label>
				<input type="radio" id="export_json"  v-on:change="blur_update" value="json" v-model="export_type">
				<label for="export_json">Download Json File</label>
			</div>
		</div>		
		<input type="hidden" name="action" id="action" value="export_mongodb_data">
		<input type="hidden" name="table_id" id="table_id" v-model=" table['_id'] ">
		<input type="hidden" name="db_id" id="db_id" v-model="table['db_id'] ">
		<input type="hidden" name="search_index" id="search_index" v-model="search_index ">
		<input type="hidden" name="index_search" id="index_search" v-model="index_search ">
		<input type="hidden" name="primary_search" id="primary_search" v-model="primary_search">
		<input type="hidden" name="export_type" id="export_type" v-model="export_type">
		<button class="btn btn-sm btn-primary" type="button" v-on:click="load_records" >Export</button>
		<input type="hidden" name="security_token" id="security_token" value="<?=get_new_token( 'database_mongodb_export',$config_param3 )?>">
	</form>
</div>
<script type="text/javascript">
	var db_list_app = new Vue({
		"el"	: "#db_list_app",
		"data"	: {
			"export_type"		: "csv",
			"table"			: <?=json_encode($table,JSON_PRETTY_PRINT) ?>,
			"fields"		: <?=json_encode($fields,JSON_PRETTY_PRINT) ?>,
			"selected_schema"	: "default",
			"search_index"		: "primary",
			"primary_search"	: {"c":"=","v":"", "v2":"", "sort":"asc","delimeter":","},
			"index_search"		: [],
			"av"			: false,
			"av2"			: false,
			"bv"			: false,
			"bv2"			: false,
			"error"			: "",
			"limit"			: "<?=$total_cnt?$total_cnt:500?>",
			"delimeter_list"	: {
							",": "Comma",
							".": "Dot",
							"/": "Slash",
							"-": "Hyphen",
							"$": "Dollor",
							"_": "Underscore",
							"#": "Hash",
							"'": "Single Qutote",
							'"': "Double Qutote",
							':': "Colon",
						},
			"filters"	  	: {
							"="	: "=",
							"!="	: "!=",
							"<"  	: "<",
							"<="	: "<=",
							">"	: ">",
							">="	: ">=",
							"><"	: "><",
							"^."  : "^...",
						},
		},
		mounted : function(){
		},
		methods : {
		        echo__: function(v__){
				if( typeof(v__) == "object" ){
					console.log( JSON.stringify(v__,null,4) );
				}else{
					console.log( v__ );
				}
			},
			blur_update:function(){   
			},
			change_index: function(){
				if( this.search_index == 'primary' ){

				}else{
					if( this.search_index in this.table['keys'] ){
						var k = [];
						for(var i=0;i<this.table['keys'][ this.search_index ]['keys'].length;i++){
							var j = this.table['keys'][ this.search_index ]['keys'][i];
							k.push({
								"field": j['name']+'',
								"type": j['type']+'',
								"cond": "=",
								"value": "",
								"value2": "",
								"sort": "asc",
							});
						}
						this.index_search = k;
					}
				}
			},
			load_records: function(){
				document.getElementById('primary_search').value = JSON.stringify(this.primary_search);
				document.getElementById('index_search').value = this.index_search;
				document.getElementById('search_index').value = this.search_index;
				document.getElementById('limit').value = this.limit;
				document.getElementById("dynamodb_export").submit()
			},
		}
	});
</script>