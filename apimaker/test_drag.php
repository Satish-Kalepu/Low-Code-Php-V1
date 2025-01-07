<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>API Maker</title>
	<link rel="stylesheet" href="/apimaker/bootstrap/bootstrap.min.css" >
	<style>
		:root{ --bs-border-color: #ccc; }
	</style>
	<script src="/apimaker/bootstrap/bootstrap.bundle.min.js"></script>
	<script src="/apimaker/bootstrap/popper.min.js"></script>
	<script src="/apimaker/js/vue3.min.prod.js"></script>
	<script src="/apimaker/js/vue-router.global.js"></script>
	<script src="/apimaker/js/axios.min.js"></script>
	<link rel="stylesheet" href="/apimaker/common.css" />
	<link rel="stylesheet" href="/apimaker/fontawesome/css/all.min.css" />
	<link rel="stylesheet" href="/apimaker/RemixIcon/fonts/remixicon.css" />
</head>
<body>
<div id="#app" >
	<div style="padding: 50px; border: 1px solid #ccc; margin: 50px;">
		<div class="draggable_box" v-for="v,i in data"><comp1 v-bind:data="v"></comp1></div>
	</div>
</div>
<script>
var comp1 = {
	data: function(){
		return {

		};
	},
	props: ["data"],
	mounted: function(){

	},
	methods: {

	},
	template: `<div>
		<input type="number" v-model="data['a']"  ><span> + </span><input type="number" v-model="data['b']" ><span> = </span><input type="number" v-model="data['c']" >
	</div>`
};
var app = Vue.createApp({
	data: function(){
		return {
			"data": [
				{
					"a": 44,
					"b": 55,
					"c": 99
				},
				{
					"a": 44,
					"b": 55,
					"c": 99
				},
				{
					"a": 44,
					"b": 55,
					"c": 99
				},
				{
					"a": 44,
					"b": 55,
					"c": 99
				},
				{
					"a": 44,
					"b": 55,
					"c": 99
				},
			]
		};
	},
	mounted: function(){

	},
	methods: {

	},
});
app.component("comp1", comp1);
var app1 = app.mount("#app");
</script>
</body>
</html>