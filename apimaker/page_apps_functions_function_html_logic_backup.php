	<div class="code_line" style="border-bottom:1px solid #bbcccc; margin-bottom:10px;" >
		<div style="font-size:14px; ">Input Object: </div>
		<input_object v-bind:v="engine__['input_factors']" datafor="engine" datavar="input_factors" viewas="json" allowsub="yes" v-on:edited="input_factors_edited__"></input_object>
	</div>
	<div v-if="verror__" class="alert alert-danger" v-html="verror__" ></div>

	<div style="background-color:white;" v-if="show_stages__" >
		<div style="font-size:14px; border-bottom:1px solid #cdcdcd; margin-bottom:5px; ">Stages of Execution: Develop & derive logical result</div>
		<div style="position:sticky;top:0px;z-index:50;background-color: white; margin-bottom:5px;border-bottom:1px solid #cdcdcd;  " >
			<div id="div_stages_menu" ref="div_stages_menu" style="padding:5px;" >
				<div style="padding:0px 10px; display:inline-block;" >
				Selected <span class="badge bg-secondary" >{{ checked_items__ }}</span>
				</div>
				<template v-if="checked_items__" >
					<button class="btn btn-outline-dark btn-sm ms-5" type="button" style="line-height: 1.2;" v-on:click="duplicate_stages__" >Copy</button>
					<button class="btn btn-default btn-sm ms-2" type="button" style=" width:30px;line-height: 1.2;line-height: 1;font-weight:bold;color:black;" v-on:click="move_up__" >&#8673;</button>
					<button class="btn btn-default btn-sm ms-2" type="button" style=" width:30px;line-height: 1.2;font-weight:bold;color:black;" v-on:click="move_down__" >&#8675;</button>
					<button class="btn btn-outline-danger btn-sm ms-2" type="button"  style="line-height: 1.2;" v-on:click="delete_stages__" >Delete</button>
					<button class="btn btn-outline-secondary btn-sm ms-2" type="button"  style="line-height: 1.2;" v-on:click="uncheck_all__" >Reset</button>
				</template>
			</div>
		</div>

		<div v-for="staged__,stagei__ in engine__['stages']" class="myrow1 stageroot" >
			<div class="mycol1" >
				<input type="checkbox" v-bind:disabled="is_locked__||staged__['t']=='HTMLElementEnd'||staged__['t']=='EndIf'||staged__['t']=='EndWhile'||staged__['t']=='EndForEach'||checks__[stagei__]['if']" v-model="checks__[stagei__]['checked']"  v-on:click="item_clicked__(stagei__)"  style="width:20px;height:20px;" >
			</div>
			<div class="mycol1" >
				<input type="button" class="btn btn-outline-secondary btn-sm py-0" v-if="checked_items__==0" value="+" v-on:click="add_stage__(stagei__)" style="padding:0px 3px;" v-bind:disabled="is_locked__" >
			</div>
			<div class="mycol2" style="align-self: stretch;" >
				<div style="width:40px; padding:0px 5px;" align='right'>{{ (stagei__+1) }}</div>
			</div>
			<div class="mycol3" >
				<template v-if="just_created_stage__!=stagei__" >
					<div v-if="staged__['er']" style=" padding:0px 5px; background-color:#feb9b9;cursor:pointer;color:black;" v-bind:title="staged__['er']" v-on:click="show_stage_alert__(stagei__)">{{ staged__['er'] }} </div>
					<div v-if="staged__['wr']" style=" padding:0px 5px; background-color:#f4ddb4;cursor:pointer;color:black;" v-bind:title="staged__['er']" v-on:click="show_stage_alert__(stagei__)">{{ staged__['wr'] }} </div>
				</template>
				<div class="code_row code_line" v-bind:style="getlevel__(stagei__)" v-bind:data-stagei="stagei__" >
					<template v-if="staged__['e']==false" >
						<div v-if="'vend' in staged__" data-list="all" style="padding:0px 10px;" >{{ staged__['k']['v'] }}</div>
						<varselect v-else datatype="dropdown" datalist="all" datavar="k" datafor="stages" v-bind:v="staged__['k']" v-bind:dataktype="staged__['k']['t']"  v-bind:dataplg="staged__['k']['plg']" v-bind:vars="all_factors_stage_wise__[ stagei__ ]" ></varselect>
						<template v-if="staged__['k']['v']=='Let'" >
							<div class="editable" data-var="d:lhs" data-for="stages" ><div contenteditable data-for="stages" data-type="editable" data-var="d:lhs" data-allow="variable_name" >{{ staged__['d']['lhs'] }}</div></div>
							<div> as </div>
							<inputtextbox datafor="stages" v-bind:v="staged__['d']['rhs']" datavar="d:rhs" v-bind:vars="all_factors_stage_wise__[stagei__]" ></inputtextbox>
						</template>
						<template v-if="staged__['k']['v']=='LetComponent'" >
							<div class="editable" data-var="d:lhs" data-for="stages" ><div contenteditable data-for="stages" data-type="editable" data-var="d:lhs" data-allow="variable_name" >{{ staged__['d']['lhs'] }}</div></div>
							<div> as </div>
							<thing datafor="stages" v-bind:v="staged__['d']['rhs']" datavar="d:rhs" v-bind:vars="all_factors_stage_wise__[stagei__]" ></thing>
						</template>
						<template v-if="staged__['k']['v']=='Assign'" >
							<div data-type="dropdown" data-for="stages" data-list="vars" data-var="d:lhs:v:v">{{ staged__['d']['lhs']['v']['v'] }}</div>
							<div> = </div>
							<inputtextbox datafor="stages" v-bind:v="staged__['d']['rhs']" datavar="d:rhs" v-bind:vars="all_factors_stage_wise__[stagei__]" ></inputtextbox>
						</template>
						<template v-if="staged__['k']['v']=='If'" >
							<div style="display: flex;">
								<div>
									<div v-for="ifv__,ifi__ in staged__['d']['cond']" v-bind:class="{'pt-1':(ifi__>0)}" style="display:flex; align-items: flex-start; padding-bottom: 2px;" >
										<div v-if="staged__['d']['cond'].length>1" class="px-2">
										<input class="btn btn-secondary btn-sm py-0 px-1" type="button" v-on:click="delete_if_condition__(stagei__, ifi__)" title="Add Condition" value="X">
										</div>
										<inputtextbox datafor="stages" v-bind:v="ifv__['lhs']" v-bind:datavar="'d:cond:'+ifi__+':lhs'" v-bind:vars="all_factors_stage_wise__[stagei__]"></inputtextbox>
										<div data-type="dropdown" data-for="stages" data-list="roperator" v-bind:data-var="'d:cond:'+ifi__+':op'" style="margin:0px 10px; font-weight: bold; text-align: center;">{{ ifv__['op'] }}</div>
										<inputtextbox datafor="stages" v-bind:v="ifv__['rhs']" v-bind:datavar="'d:cond:'+ifi__+':rhs'" v-bind:vars="all_factors_stage_wise__[stagei__]"></inputtextbox>
										<div class="px-2">
											<input v-if="ifi__==staged__['d']['cond'].length-1" class="btn btn-secondary btn-sm py-0 px-1" type="button" v-on:click="add_if_condition__(stagei__)" title="Add Condition" value="+" >
										</div>
									</div>
								</div>
								<div v-if="staged__['d']['cond'].length>1" class="px-2">
									<div data-type="dropdown" data-for="stages" data-list="ifop" v-bind:data-var="'d:op'" style="margin:0px 10px; font-weight: 400; text-align: center;">{{ staged__['d']['op'] }}</div>
								</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='While'" >
							<div style="display: flex; align-items:flex-start;">
								<div style="padding-right:5px;">
									<div v-for="ifv__,ifi__ in staged__['d']['cond']" v-bind:class="{'pt-1':(ifi__>0)}"  style="display:flex; align-items: flex-start;" >
										<div v-if="staged__['d']['cond'].length>1"  class="px-2">
											<input class="btn btn-secondary btn-sm py-0 px-1" type="button" v-on:click="delete_if_condition__(stagei__, ifi__)" title="Delete Condition" value="X">
										</div>
										<inputtextbox datafor="stages" v-bind:v="ifv__['lhs']" v-bind:datavar="'d:cond:'+ifi__+':lhs'" v-bind:vars="all_factors_stage_wise__[stagei__]"></inputtextbox>
										<div data-type="dropdown" data-for="stages" data-list="roperator" v-bind:data-var="'d:cond:'+ifi__+':op'" style="margin:0px 10px; font-weight: bold; text-align: center;">{{ ifv__['op'] }}</div>
										<inputtextbox datafor="stages" v-bind:v="ifv__['rhs']" v-bind:datavar="'d:cond:'+ifi__+':rhs'" v-bind:vars="all_factors_stage_wise__[stagei__]"></inputtextbox>
										<div class="px-2">
											<input v-if="ifi__==staged__['d']['cond'].length-1" class="btn btn-secondary btn-sm py-0 px-1" type="button" v-on:click="add_if_condition__(stagei__)" title="Add Condition" value="+" >
										</div>
									</div>
								</div>
								<div v-if="staged__['d']['cond'].length>1" class="px-2">
									<div data-type="dropdown" data-for="stages" data-list="ifop" v-bind:data-var="'d:op'" style="margin:0px 10px; font-weight: 400; text-align: center;">{{ staged__['d']['op'] }}</div>
								</div>
								<div>
									<div style="display:flex;">
										<div>MaxLoops: </div>
										<div class="editable" data-var="d:maxloops" data-for="stages" ><div contenteditable data-for="stages" data-type="editable" data-var="d:maxloops" data-allow="number" >{{ staged__['d']['maxloops'] }}</div></div>
									</div>
								</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='For'" >
							<div style="display:flex; align-items: flex-start; border-left:1px solid #ccc;border-top:1px solid #ccc;border-right:1px solid #ccc; background-color: #f8f8f8; gap:5px; padding-left:5px; padding-right:5px;">
								<div>
									<div>Start</div>
									<div><inputtextbox2 datafor="stages" v-bind:v="staged__['d']['start']" types="N,V" datavar="d:start" v-bind:vars="all_factors_stage_wise__[stagei__]"></inputtextbox2></div>
								</div>
								<div>
									<div>End</div>
									<div><inputtextbox2 datafor="stages" v-bind:v="staged__['d']['end']" types="N,V" datavar="d:end" v-bind:vars="all_factors_stage_wise__[stagei__]"></inputtextbox2></div>
								</div>
								<div>
									<div>Order</div>
									<div>
										<div class="codeline_thing_pop" data-type="dropdown" data-for="stages" data-list="order" data-var="d:order" >{{ staged__['d']['order'] }}</div>
									</div>
								</div>
								<div>
									<div>Modifier</div>
									<div><inputtextbox2 datafor="stages" v-bind:v="staged__['d']['modifier']" types="N,V" datavar="d:modifier"  v-bind:vars="all_factors_stage_wise__[stagei__]"></inputtextbox2></div>
								</div>
								<div>
									<div>As</div>
									<div class="editable" data-var="d:as" data-for="stages" ><div contenteditable data-type="editable" data-var="d:as" data-allow="text" data-for="stages" >{{ staged__['d']['as'] }}</div></div>
								</div>
								<div>
									<div>MaxLoops</div>
									<div class="editable" data-var="d:maxloops" data-for="stages" ><div contenteditable data-type="editable" data-var="d:maxloops" data-allow="number" data-for="stages" >{{ staged__['d']['maxloops'] }}</div></div>
								</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='ForEach'" >
							<div style="display:flex; align-items: flex-start; border:1px solid #ccc; gap:5px; padding-left:5px; padding-right:5px;">
								<div>
									<div>List</div>
									<div><div class="codeline_thing_pop" data-type="dropdown" data-for="stages" data-list="vars" data-var="d:var:v:v" >{{ staged__['d']['var']['v']['v'] }}</div></div>
								</div>
								<div> as </div>
								<div>
									<div>Key</div>
									<div class="editable" data-var="d:key"  data-for="stages"><div contenteditable data-type="editable"  data-for="stages" data-var="d:key" data-allow="text" >{{ staged__['d']['key'] }}</div></div>
								</div>
								<div>
									<div>Value</div>
									<div class="editable" data-var="d:value"  data-for="stages"><div contenteditable data-type="editable"  data-for="stages" data-var="d:value" data-allow="text" >{{ staged__['d']['value'] }}</div></div>
								</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='Math'" >
							<div style="display: flex; align-items:flex-start;">
								<div data-type="dropdown" data-for="stages" data-list="vars" v-bind:data-var="'d:lhs:v:v'">{{ staged__['d']['lhs']['v']['v'] }}</div>
								<div>&nbsp;&nbsp;=&nbsp;&nbsp; </div>
								<div v-for="md,mi in staged__['d']['rhs']" style="display:flex; align-items: flex-start; gap:5px;" >
									<div style="font-size:1.2rem;line-height: 20px;"> ( </div>
									<template v-for="mds,mis in md['m']" >
										<inputtextbox2 data-for="stages" v-bind:v="mds" types="N,V" v-bind:datavar="'d:rhs:'+mi+':m:'+mis" v-bind:vars="all_factors_stage_wise__[stagei__]"></inputtextbox2>
										<div class="codeline_thing_pop2" data-type="dropdown2" data-for="stages" data-list="operator" v-bind:data-var="'d:rhs:'+mi+':m:'+mis+':OP'">{{ mds['OP'] }}</div>
									</template>
									<div style="font-size:1.2rem;line-height: 20px;"> ) </div>
									<div class="codeline_thing_pop2" data-type="dropdown2" data-for="stages" data-list="operator" v-bind:data-var="'d:rhs:'+mi+':OP'">{{ md['OP'] }}</div>
								</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='Expression'" >
							<div style="display: flex; align-items:flex-start;">
								<div data-type="dropdown" data-for="stages" data-list="vars" v-bind:data-var="'d:lhs:v:v'">{{ staged__['d']['lhs']['v']['v'] }}</div>
								<div>&nbsp;&nbsp;=&nbsp;&nbsp; </div>
								<div class="editable" data-var="d:rhs:v" data-for="stages" ><div contenteditable data-for="stages" data-type="editable" data-var="d:rhs:v" data-allow="expression" >{{ staged__['d']['rhs']['v'] }}</div></div>
								<div class="help-div" doc="expression.html">?</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='Function'" >
							<template v-if="staged__['d']['return']" >
								<div data-type="dropdown" data-for="stages" data-list="vars" data-var="d:lhs:v:v" style="white-space: nowrap;">{{ staged__['d']['lhs']['v']['v'] }}</div>
								<div> = </div>
							</template>
							<div data-type="dropdown" data-for="stages" data-list="functions" data-var="d:fn" data-var-parent="d" style="white-space: nowrap;">{{ staged__['d']['fn'] }}</div>
							<template v-if="'inputs' in staged__['d']" >
							<template v-if="typeof(staged__['d']['inputs'])=='object'" >
							<div class="varsub-inputs" style="margin-left:-5px;">
								<template v-for="fv,fi in staged__['d']['inputs']" >
									<div style="min-width:50px;text-align:right;">{{ fv['n'] }}</div>
									<inputtextbox2 datafor="stages" v-bind:v="fv" v-bind:types="fv['types']" v-bind:datavar="'d:inputs:'+fi" v-bind:vars="all_factors_stage_wise__[ stagei__ ]"></inputtextbox2>
								</template>
							</div>
							</template>
							</template>
						</template>
						<template v-if="staged__['k']['v']=='FunctionCall'" >
							<div style="display: flex; align-items:flex-start;">
								<div data-type="dropdown" data-for="stages" data-list="vars" v-bind:data-var="'d:lhs:v:v'">{{ staged__['d']['lhs']['v']['v'] }}</div>
								<div>&nbsp;&nbsp;=&nbsp;&nbsp; </div>
								<div data-type="dropdown" data-for="stages" data-list="thing" data-thing="Functions" v-bind:data-var="'d:fn:v'">{{ staged__['d']['fn']['v']['l']['v'] }}</div>
								<template v-if="'inputs' in staged__['d']['fn']['v']" >
								<template v-if="typeof(staged__['d']['fn']['v']['inputs'])=='object'" >
								<div class="varsub-inputs" style="">
									<template v-for="fv,fi in staged__['d']['fn']['v']['inputs']['v']" >
										<div style="min-width:50px;text-align:right;">{{ fi }}</div>
										<inputtextbox datafor="stages" v-bind:v="fv" v-bind:datavar="'d:fn:v:inputs:v:'+fi" v-bind:vars="all_factors_stage_wise__[ stagei__ ]"></inputtextbox>
									</template>
								</div>
								</template>
								</template>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='Respond'" >
							<div>
								<pre title="Object or Associative List" data-type="objecteditable" editable-type="O" data-for="stages" data-var="d:v" style="margin-bottom:5px;" >{{ get_object_notation__(staged__['d']['v']) }}</pre>
								<div>End Execution</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='RespondJSON'" >
							<div>
								<div style="display:flex; column-gap:5px;">
									<div>JSON</div>
									<pre title="Object or Associative List" data-type="objecteditable" editable-type="O" data-for="stages" data-var="d:output:v" style="margin-bottom:5px;" >{{ get_object_notation__(staged__['d']['output']['v']) }}</pre>
								</div>
								<div style="display:flex; column-gap:5px;">
									<div>Indent</div>
									<div title="JSON Indent" data-type="dropdown" data-for="stages" data-var="d:pretty:v" data-list="boolean"  style="margin-bottom:5px;" >{{ staged__['d']['pretty']['v'] }}</div>
								</div>
								<div>End Execution</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='RespondVar'" >
							<div>
								<div style="display:flex; column-gap:5px;">
									<div>Variable</div>
									<div title="Variable" data-type="dropdown" data-for="stages" data-var="d:output:v:v" data-list="vars"  style="margin-bottom:5px;" >{{ staged__['d']['output']['v']['v'] }}</div>
								</div>
								<div>End Execution</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='RespondXML'" >
							<div>
								<div style="display:flex;">
									<div>XML</div>
									<pre title="Object or Associative List" data-type="objecteditable" editable-type="O" data-for="stages" data-var="d:output:v" style="margin-bottom:5px;" >{{ get_object_notation__(staged__['d']['output']['v']) }}</pre>
								</div>
								<div style="display:flex;">
									<div>Indent</div>
									<div title="JSON Indent" data-type="dropdown" data-for="stages" data-var="d:pretty:v" data-list="boolean"  style="margin-bottom:5px;" >{{ staged__['d']['pretty']['v'] }}</div>
								</div>
								<div>End Execution</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='AddHTML'" >
							<div>
								<inputtextbox2 datafor="stages" v-bind:v="staged__['d']" types="T,TT,HT,V" v-bind:datavar="'d'" v-bind:vars="all_factors_stage_wise__[ stagei__ ]"></inputtextbox2>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='RenderHTML'" >
							<pre title="HTML Text" data-type="objecteditable" editable-type="HT" data-for="stages" data-var="d:html:v" style="margin-bottom:5px;" >{{ staged__['d']['html']['v'] }}</pre>
						</template>
						<template v-if="staged__['k']['v']=='RespondError'" >
							<div>
								<pre title="Object or Associative List" data-type="objecteditable" editable-type="O" data-for="stages" data-var="d:v" style="margin-bottom:5px;" >{{ get_object_notation__(staged__['d']['v']) }}</pre>
								<div>End Execution</div>
							</div>
						</template>
						<template v-if="staged__['k']['v']=='Log'" >
							<div><pre title="Object or Associative List" data-type="objecteditable" editable-type="O" data-for="stages" data-var="d:v" style="margin-bottom:5px;" >{{ get_object_notation__(staged__['d']['v']) }}</pre></div>
						</template>
						<template v-if="staged__['k']['v']=='SetLabel'" >
							<inputtextbox2 datafor="stages" v-bind:v="staged__['d']" datavar="d" types="T" v-bind:vars="all_factors_stage_wise__[ stagei__ ]"></inputtextbox2>
						</template>
						<template v-if="staged__['k']['v']=='JumpToLabel'" >
							<div data-type="dropdown" data-for="stages" data-list="labels" data-var="d:v" >{{ staged__['d']['v'] }}</div>
						</template>
						<template v-if="staged__['k']['v']=='Sleep'" >
							<div class="editable" data-var="d:v" data-for="stages"><div contenteditable data-for="stages" data-type="editable" data-var="d:v" data-allow="number" >{{ staged__['d']['v'] }}</div></div>
						</template>
						<template v-if="staged__['k']['v']=='MongoDb'" >
							<mongodbv1 v-bind:ref="'stage_'+stagei__+'_comp'"  v-bind:refname="'stage_'+stagei__+'_comp'"  datafor="stages" v-bind:v="staged__['d']" datavar="d"  v-bind:vars="all_factors_stage_wise__[ stagei__ ]" v-on:updated="updated_option__"  ></mongodbv1>
						</template>
						<template v-if="staged__['k']['v']=='MySql'" >
							<mysqldbv1 v-bind:ref="'stage_'+stagei__+'_comp'"  v-bind:refname="'stage_'+stagei__+'_comp'"  datafor="stages" v-bind:v="staged__['d']" datavar="d"  v-bind:vars="all_factors_stage_wise__[ stagei__ ]" v-on:updated="updated_option__"  ></mysqldbv1>
						</template>
						<template v-if="staged__['k']['v']=='HTTPRequest'" >
							<httprequest v-bind:ref="'stage_'+stagei__+'_comp'"  v-bind:refname="'stage_'+stagei__+'_comp'"  datafor="stages" v-bind:v="staged__['d']" datavar="d" v-bind:vars="all_factors_stage_wise__[ stagei__ ]" v-on:updated="updated_option__" ></httprequest>
						</template>
						<template v-if="staged__['k']['v']=='Internal-Table'" >
							<internal_table v-bind:ref="'stage_'+stagei__+'_comp'"  v-bind:refname="'stage_'+stagei__+'_comp'"  datafor="stages" v-bind:v="staged__['d']" datavar="d"  v-bind:vars="all_factors_stage_wise__[ stagei__ ]" v-on:updated="updated_option__"  ></internal_table>
						</template>
						<div v-if="staged__['k']['v']=='Dynamic-Table'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Elastic-Tabl'" >Yet to Implement</div>						
						<div v-if="staged__['k']['v']=='APICall'" class="text-danger" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='wkHtmlToPdf'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='AWS'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='GCP'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Azure'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='AirTable'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Facebook'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Whatsapp'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Telegram'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Slack'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Jira'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='GoogleMaps'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Celery'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='AMPq'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='RabbitMQ'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Swagger'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Stripe'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Paypal'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='CCAvenue'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='RazorPay'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='PayU'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='BillDesk'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Paytm'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='DynamoDb'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Redis'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='MSSql'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='Cassandra'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='SQLite'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='FireBase'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='FireStore'" >Yet to Implement</div>
						<div v-if="staged__['k']['v']=='BigQuery'" >Yet to Implement</div>
					</template>
				</div>
			</div>
		</div>
		<div class="myrow1" >
			<div class="mycol1" >&nbsp;</div>
			<div class="mycol1" >
				<input type="button" class="btn btn-outline-dark btn-sm py-0"   v-if="checked_items__==0" value="+" v-on:click="add_stage__('last')" style="padding:0px 3px;" v-bind:disabled="is_locked__" >
			</div>
			<div class="mycol23" >&nbsp;</div>
		</div>
		<div style="clear:both;"></div>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<pre>{{ engine__['output'] }}</pre>
		<?php if( $_GET['show']=="debug" ){ ?>
			<p><B>Debug</b></p>
			<pre>{{ plugin_function_options__ }}</pre>
			<p>Engine:</p>
			<pre v-text="engine__"></pre>
			<pre v-text="function__"></pre>
			<pre v-text="all_factors_stage_wise__"></pre>
			<p>Test:</p>
			<pre v-if="json_view__" v-text="test__"></pre>
		<?php } ?>
	</div>

	<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>

