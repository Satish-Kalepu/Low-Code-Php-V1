<script>
const graph_document = {
	data(){
		return {};
	},
	props: ["refname", "data", "object_id"],
	watch: {},
	mounted: function(){
		//this.$root['data']
	},
	methods: {
		echo__: function(v__){
			if( typeof(v__)=="object" ){
				console.log( JSON.stringify(v__,null,4) );
			}else{
				console.log( v__ );
			}
		},
	},
	template: `<div>

		<div v-bind:id="refname+'_editor_div__" v-bind:ref="refname+'_editor_div__" class="satish_doc_editor" data-id="document-root" spellcheck="false" draggable="false" contenteditable="true" ></div>

		<pre>{{ data }}</pre>
	</div>`
};
</script>