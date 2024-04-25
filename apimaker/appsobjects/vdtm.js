const vdtm = {
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
		tzs: ["UTC−12:00","UTC−11:00","UTC−10:00","UTC−09:30","UTC−09:00","UTC−08:00","UTC−07:00","UTC−06:00","UTC−05:00","UTC−04:00","UTC−03:30","UTC−03:00","UTC−02:00","UTC−01:00","UTC+00:00","UTC+01:00","UTC+02:00","UTC+03:00","UTC+03:30","UTC+04:00","UTC+04:30","UTC+05:00","UTC+05:30","UTC+05:45","UTC+06:00","UTC+06:30","UTC+07:00","UTC+08:00","UTC+08:45","UTC+09:00","UTC+09:30","UTC+10:00","UTC+10:30","UTC+11:00","UTC+12:00","UTC+12:45","UTC+13:00","UTC+14:00"],
		cal: [[1,2,3]],
		wks: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
		wms: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
		vdate: "2023-07-55",
		vy: 2023,
		vm: 1,
		vd: 1,
		vhr: 1,vmn: 1,vtz:"",
		vys: [],
		tz: "",
	}},
	props: ["datafor", "datavar", "v"],
	mounted(){
		console.log("DT initialized");
		if( this.datafor == undefined ){
			this.datafor = "stages";
		}
		this.vtz = this.v['tz'];
		var dt = new Date( this.v['v'] );
		this.vy  = Number(dt.getFullYear());
		this.vm  = Number(dt.getMonth()+1);
		this.vd  = Number(dt.getDate());
		this.vhr = Number(dt.getHours());
		this.vmn = Number(dt.getMinutes());
		var ys = [];
		for(var i=this.vy-100;i<=this.vy+200;i++){
			ys.push(i);
		}
		this.vys = ys;
		setTimeout(function(){document.getElementById("dtinput").focus();},1000);
	},
	watch: {
	},
	methods:{
		close: function(){
			this.$emit("close");
		},
		pad: function(v){
			return ("000"+v).slice(-2);
		},
		set: function(){
			var dt = new Date();
			dt.setUTCDate(this.vd);dt.setUTCMonth(this.vm-1);dt.setUTCFullYear(this.vy);dt.setUTCHours(this.vhr);dt.setUTCMinutes(this.vmn);
			dt.setUTCSeconds(0);
			//dt.setDate(this.vtz);
			this.v['v'] = dt.toJSON().substr(0,19).replace("T", " ");
			this.v['tz'] = this.vtz;
			this.$emit("update",this.v);
			this.$emit("close");
		}
	},
	template:`<div>
		<input type="button" class="btn btn-secondary btn-sm" style="float:right; padding:0px 5px;" value="X" v-on:click="close" >
		<p>Date Time</p>
		<table>
			<tr>
				<td>Year</td>
				<td>Month</td>
				<td>Date</td>
				<td>Hour</td>
				<td>Min</td>
				<td>Timezone</td>
			</tr>
			<tr>
				<td><select v-model="vy"  ><option v-for="y in vys" v-bind:value="y" >{{ y }}</option></select></td>
				<td><select v-model="vm" style="padding:0px 5px;"  ><option v-for="m in 12" v-bind:value="m"  >{{ pad(m) }}</option></select></td>
				<td><select v-model="vd" style="padding:0px 5px;"  ><option v-for="d in 31" v-bind:value="d"  >{{ pad(d) }}</option></select></td>
				<td><select v-model="vhr" style="padding:0px 5px;" ><option v-for="h in 24" v-bind:value="h-1"  >{{ pad(h-1) }}</option></select></td>
				<td><select v-model="vmn" style="padding:0px 5px;" id="dtinput" ><option v-for="m in 60" v-bind:value="m-1"  >{{ pad(m-1) }}</option></select></td>
				<td><select v-model="vtz" ><option v-for="t in tzs" v-bind:value="t" >{{ t }}</option></select></td>
			</tr>
		</table>
		<div><input type="button" class="btn btn-secondary btn-sm" value="SET" v-on:click="set" ></div>
	</div>`
};