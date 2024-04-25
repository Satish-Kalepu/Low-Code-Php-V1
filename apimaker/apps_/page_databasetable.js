const page_databasetable = { data(){ return { "html": "", "tables": [], "options": { "theme": "", "auth": "none", "dbtype": "", "table": {"i":"", "l":"Select Table"}, "engine": "", "search": false, "infinate": false, "add": false, "edit": false, "delete": false, "deletebulk": false, } } }, props: ['tag', 'vars', 'refname'], mounted: function(){ this.init(); }, watch: { }, methods: { s2_ooooooohce: function(s2_vvvvvvvvvv){ if( typeof(s2_vvvvvvvvvv)=="object" ){ console.log( JSON.stringify(s2_vvvvvvvvvv,null,4) ); }else{ console.log( s2_vvvvvvvvvv ); } }, init: function(){ if( this.tag.hasAttribute( "dataappoptions" ) ){ var v = this.tag.getAttribute( "dataappoptions" ); console.log( v ); if( v != "" ){ v = atob(v); console.log( v ); this.options = JSON.parse(v); this.s2_ooooooohce( this.options ); if( this.options['dbtype'] != "" ){ this.dbtableselect(); } } } }, dbtableselect: function(){ axios.post( this.$root.rootpath + "things/", { "action": "context_load_things", "app_id": this.$root.app_id, "thing": (this.options['dbtype']=="internal-table"?"page_edit_tables_internal":(this.options['dbtype']=="external-table"?"page_edit_tables_external":"none")), }).then(response=>{ this.tables = response.data['things']; }).catch(error=>{ }); }, tableselect: function(){ var x = this.options['table']['i']; for(var i=0;i<this.tables.length;i++){ if( this.tables[i]['i'] == x ){ this.options['table']['l'] = this.tables[i]['l']+''; } } this.maketag(); }, maketag: function(){ this.tag.setAttribute( "dataappoptions", btoa(JSON.stringify(this.options)) ); var v = "<p>DatabaseTable</p>"; v = v + "<p>" + this.options['dbtype'] + "</p>"; v = v + "<p>" + this.options['table']['l'] + "</p>"; v = v + "<p>double click to edit</p>"; this.tag.innerHTML= v; }, opchange: function(){ this.maketag(); } }, template: `<div style="" > <div>Database</div> <div><select v-model="options['dbtype']" class="form-select form-select-sm" v-on:change="dbtableselect" > <option value="internal-table" >Internal Table</option> <option value="external-table" >External Table</option> <option value="elastic-table"  >Elastic Table</option> </select> <div>Table</div> <div><select v-model="options['table']['i']" class="form-select form-select-sm" v-on:change="tableselect" > <option v-for="td,ti in tables" v-bind:value="td['i']" >{{ td['l'] }}</option> <option v-if="options['table']['i']!=''" v-bind:value="options['table']['i']" >{{ options['table']['l'] }}</option> </select></div> <div>Options</div> <div><label style="cursor:pointer;" ><input type="checkbox" v-model="options['search']" v-on:change="opchange" > Search</label></div> <div><label style="cursor:pointer;" ><input type="checkbox" v-model="options['infinate']" v-on:change="opchange" > Infinate Scroll</label></div> <div><label style="cursor:pointer;" ><input type="checkbox" v-model="options['add']" v-on:change="opchange" > Add Record</label></div> <div><label style="cursor:pointer;" ><input type="checkbox" v-model="options['edit']" v-on:change="opchange" > Edit Record</label></div> <div><label style="cursor:pointer;" ><input type="checkbox" v-model="options['delete']" v-on:change="opchange" > Delete Record</label></div> <div><label style="cursor:pointer;" ><input type="checkbox" v-model="options['deletebulk']" v-on:change="opchange" > Delete Bulk</label></div> <div>Theme: <select v-model="options['theme']" class="form-select form-select-sm" v-on:change="opchange" > <option v-bind:value="one" >One</option> <option v-bind:value="two" >Two</option> <option v-bind:value="three" >Three</option> <option v-bind:value="four" >Four</option> </select></div> <!-- <pre>{{ html }}</pre> --> </div>` };