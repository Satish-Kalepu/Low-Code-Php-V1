const vdt = {
	data(){return{
		"data_types__":{
			"T": "Text",
			"N": "Number",
			"D": "Date",
			"DT": "DateTime",
			"L": "List",
			"O": "Assoc List",
			"B": "Boolean",
			"NL": "Null", 
			"BIN": "Binary",
			"V": "Variable",
		},
		cal: [[1,2,3]],
		wks: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
		wms: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
		vdate: "2023-07-55",
		vy: 2023,
		vm: 1,
		vd: 1,
		vys: [],
	}},
	props: ["datafor", "datavar", "v"],
	mounted(){
		console.log("DT initialized");
		if( this.datafor == undefined ){
			this.datafor = "stages";
		}

		console.log( this.v );
		var dt = new Date( this.v+'' );
		var cur_yr = Number(dt.getFullYear());
		if( typeof(cur_yr) == "object" ){
			var dt = new Date();
			var cur_yr = Number(dt.getFullYear());
		}
		this.dt= dt;
		this.vd = Number(dt.getDate());
		this.vm = Number(dt.getMonth());
		this.vy = Number(dt.getFullYear());
		var ys = [];
		for(var i=cur_yr-100;i<=cur_yr+200;i++){
			ys.push(i);
		}
		this.vys = ys;
		this.gen_cal();
	},
	watch: {
	},
	methods:{
		gen_cal: function(){
			var dt = new Date(this.vy, this.vm, this.vd, 12,12,12);
			var start_d = 1;
			dt.setDate(start_d);
			var start_day = dt.getDay();
			var end_d = Number(new Date(dt.getFullYear(), dt.getMonth()+1, 0).getDate());
			var sd = start_d + start_day;
			var se = end_d + start_day;
			var sdd = 1;
			var cc = [];
			for(var i=1;i<=42;i++){
				if( i>=sd && i<=se ){
					cc.push(sdd);
					sdd++;
				}else{
					cc.push("");
				}
			}
			var c = [];
			while( cc.length ){
				c.push( cc.splice(0,7) );
			}
			this.cal = c;
		},
		get_object_notation: function(){
			return 'Object Editable';
		},
		setdate: function( vi ){
			this.vd = Number(vi)+1;
			var dt = new Date();
			dt.setDate( this.vd );
			dt.setMonth( this.vm );
			dt.setYear( this.vy );
			//this.v = dt.toJSON().substr(0,10);
			this.$emit("update",dt.toJSON().substr(0,10));
			this.$emit("close");
		},
		setmonth: function(  ){
			console.log( this.vm );
			var dt = new Date();
			dt.setDate( this.vd );
			dt.setMonth( this.vm );
			dt.setYear( this.vy );
			//this.v = dt.toJSON().substr(0,10);
			this.gen_cal();
		},
		setyear: function( vy ){
			var dt = new Date();
			dt.setDate( this.vd );
			dt.setMonth( this.vm );
			dt.setYear( this.vy );
			//this.v = dt.toJSON().substr(0,10);
			this.gen_cal();
		},
		close: function(){
			this.$emit("close");
		}
	},
	template:`<div>
		<input type="button" class="btn btn-secondary btn-sm" style="float:right; padding:0px 5px;" value="X" v-on:click="close" >
		<div>Date</div>
		<div class="d-flex" >
			<select class="form-select form-select-sm" v-model="vm" v-on:change="setmonth" ><option v-for="m,mi in wms" v-bind:value="mi" >{{ m }}</option></select>
			<select class="form-select form-select-sm" v-model="vy" v-on:change="setyear" ><option v-for="y in vys" v-bind:value="y" >{{ y }}</option></select>
		</div>
		<table class="table table-bordered table-sm" >
			<tbody>
			<tr>
				<td v-for="wn,wi in wks" class="text-center" ><div style="width:30px;" >{{ wn }}</div></td>
			</tr>
			<tr v-for="w,wi in cal">
				<td v-for="dt,di in w" v-bind:class="{'text-center cal_btn':true,'text-danger bg-white':dt==vd}" v-on:click="setdate(dt)" >{{ dt }}</td>
			</tr>
			<tbody>
		</table>
	</div>`
};