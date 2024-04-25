const varselect = {
	data(){return{
		"v2": "",
	}},
	props: ["datafor", "datatype","datavar", "v", "datalist", "vars", "dataktype", "dataplg", "fn", "fnparam"],
	methods:{
		find_o_sub_var: function( vv, vpath ){
			var x = vpath.split("->",1);
			var k = x[0];
			if( k in vv ){
				if( x.length > 1 ){
					x.splice(0,1);
					if( vv[ k ]['t'] == "O" ){
						return this.find_o_sub_var( vv[ k ]['_'], x.join("->") );
					}else{
						return false;
					}
				}else{
					return true;
				}
			}else{
				return false;
			}
		},
		get_o_sub_var: function( vv, vpath ){
			var x = vpath.split("->");
			var k = x[0];
			if( k in vv ){
				if( x.length > 1 ){
					x.splice(0,1);
					if( vv[ k ]['t'] == "O" ){
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
		},
	},
	mounted(){
		if( this.datafor == undefined){
			this.datafor = "stages";
		}
		if( 'vs' in this.v == false ){
			this.v['vs'] = {"v":"", "t":"", "d": {}};
		}
	},
	template:`<div style="display:flex; align-items:flex-start;">
		<div v-bind:data-type="datatype" v-bind:fn="fn" v-bind:fnparam="fnparam"  v-bind:data-for="datafor" v-bind:data-list="datalist" v-bind:data-var="datavar" >{{ v['v'] }}</div>
		<template v-if="typeof(v['vs'])=='object'" >
			<template v-if="find_o_sub_var(vars, v['v'])" >
				<div v-if="(v['vs']['v']=='.'||v['vs']['v']=='')&&'plg' in v" title="Properties or Methods" data-type="dropdown" class="varsubpre" data-list="plgsub" v-bind:var-for="v['plg']"  v-bind:fn="fn" v-bind:fnparam="fnparam" v-bind:data-for="datafor" v-bind:data-var="datavar+':vs:v'"  v-bind:data-var-parent="datavar" >{{ v['vs']['v'] }}</div>
				<div v-else-if="v['vs']['v']=='.'||v['vs']['v']==''" 			 title="Properties or Methods" data-type="dropdown3" class="varsubpre" data-list="varsub" v-bind:var-for="v['t']"    v-bind:fn="fn" v-bind:fnparam="fnparam" v-bind:data-for="datafor" v-bind:data-var="datavar+':vs:v'"  v-bind:data-var-parent="datavar" >{{ v['vs']['v'] }}</div>
				<template v-else >
					<div>-&gt;</div>
					<div v-if="'plg' in v" title="Properties or Methods" class="varsub" data-type="dropdown" data-list="plgsub" v-bind:data-for="datafor" v-bind:var-for="v['plg']" v-bind:data-var="datavar+':vs:v'" v-bind:data-var-parent="datavar" >{{ v['vs']['v'] }}</div>
					<div v-else title="Properties or Methods" class="varsub" data-type="dropdown" data-list="varsub" v-bind:data-for="datafor" v-bind:var-for="v['t']" v-bind:data-var="datavar+':vs:v'" v-bind:data-var-parent="datavar" >{{ v['vs']['v'] }}</div>
					<template v-if="'d' in v['vs']">
					<template v-if="typeof(v['vs']['d'])&&'inputs' in v['vs']['d']">
						<div class="varsub-inputs">
							<template v-for="pd,pi in v['vs']['d']['inputs']" >
								<div style="cursor:default;  text-align:right;">{{ pd['n'] }}</div>
								<inputtextbox2 v-bind:fn="v['vs']['v']" v-bind:fnparam="pi"  v-bind:v="pd['v']" v-bind:types="pd['types']" v-bind:datafor="datafor" v-bind:datavar="datavar+':vs:d:inputs:'+pi+':v'"  v-bind:dataktype="dataktype" v-bind:dataplg="dataplg"  ></inputtextbox2>
								<div v-if="'h' in pd" ><div class="help-div" v-bind:doc="pd['h']" >?</div></div>
								<div v-else-if="'hh' in pd" ><div class="help-div2" v-bind:data-help="pd['hh']" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" v-bind:data-bs-title="pd['hh']" >?</div></div>
								<div v-else></div>
							</template>
						</div>
					</template>
					<div v-if="'h' in v['vs']['d']" class="help-div" v-bind:doc="v['vs']['d']['h']">?</div>
					</template>
				</template>
			</template>
		</template>
	</div>`
};