const vts = {
	data(){return{
		vy: 2023, vm: 1, vd: 1,
		vhr: 1, vmn: 1, vsc: 0, vtz:"",
		vys: [], vtst: 0
	}},
	props: ["datafor", "datavar", "v"],
	mounted(){
		console.log("TS initialized");
		console.log( this.v );
		if( this.datafor == undefined ){
			this.datafor = "stages";
		}
		var dt = new Date( Number(this.v)*1000 );
		this.vy  = Number(dt.getFullYear());
		this.vm  = Number(dt.getMonth())+1;
		this.vd  = Number(dt.getDate());
		this.vhr = Number(dt.getHours());
		this.vmn = Number(dt.getMinutes());
		this.vsc = Number(dt.getSeconds());
		this.vtst = Number(this.v);
		var ys = [];
		for(var i=this.vy-100;i<=this.vy+200;i++){
			ys.push(i);
		}
		this.vys = ys;
		setTimeout(function(){document.getElementById("tsinput").focus();},1000);
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
		sel: function(){
			var dt = new Date();
			dt.setDate(this.vd);dt.setMonth(this.vm-1);dt.setFullYear(this.vy);dt.setHours(this.vhr);dt.setMinutes(this.vmn);
			dt.setSeconds(this.vsc);
			this.vtst = parseInt(dt.getTime()/1000);
		},
		change: function(){
			var dt = new Date( Number(this.vtst)*1000 );
			this.vd = dt.getDate();
			this.vm = dt.getMonth()+1;
			this.vy = dt.getFullYear();
			this.vhr = dt.getHours();
			this.vmn = dt.getMinutes();
			this.vsc = dt.getSeconds();
		},

		set: function(){
			this.$emit("update",this.vtst);
			this.$emit("close");
		}
	},
	template:`<div>
		<input type="button"  class="btn btn-secondary btn-sm" style="float:right; padding:0px 5px;" value="X" v-on:click="close" >
		<p>Unix TimeStamp</p>
		<div><input id="tsinput" type="number" v-model="vtst" style="width:99%" v-on:change="change" ></div>
		<table>
			<tr>
				<td>Year</td>
				<td>Month</td>
				<td>Date</td>
				<td>Hour</td>
				<td>Min</td>
				<td>Sec</td>
			</tr>
			<tr>
				<td><select v-model="vy" v-on:change="sel"  ><option v-for="y in vys" v-bind:value="y" >{{ y }}</option></select></td>
				<td><select v-model="vm" v-on:change="sel" style="padding:0px 5px;"  ><option v-for="m in 12" v-bind:value="m"  >{{ pad(m) }}</option></select></td>
				<td><select v-model="vd" v-on:change="sel" style="padding:0px 5px;"  ><option v-for="d in 31" v-bind:value="d"  >{{ pad(d) }}</option></select></td>
				<td><select v-model="vhr" v-on:change="sel" style="padding:0px 5px;" ><option v-for="h in 24" v-bind:value="h-1"  >{{ pad(h-1) }}</option></select></td>
				<td><select v-model="vmn" v-on:change="sel" style="padding:0px 5px;" ><option v-for="m in 60" v-bind:value="m-1"  >{{ pad(m-1) }}</option></select></td>
				<td><select v-model="vsc" v-on:change="sel" style="padding:0px 5px;" ><option v-for="m in 60" v-bind:value="m-1"  >{{ pad(m-1) }}</option></select></td>
			</tr>
		</table>
		<div><input type="button" class="btn btn-secondary btn-sm" value="SET" v-on:click="set" ></div>
	</div>`
};