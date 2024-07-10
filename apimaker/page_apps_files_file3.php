<div id="app" >
  <div class="leftbar" >
    <?php require("page_apps_leftbar.php"); ?>
  </div>
  <div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
    <div style="padding: 10px;" >
      <div style="float:right;" >
        <div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
        <div v-if="err" class="alert alert-danger" >{{ err }}</div>
      </div>
      <div style="float:right;" >
        <button class="btn btn-outline-secondary btn-sm" v-on:click="file_show_create_form()" >Create</button>
        <button class="btn btn-outline-secondary btn-sm" v-on:click="file_show_upload_form()" >Upload</button>
      </div>
      <div class="h3 mb-3">Files</div>
      <div style="height: calc( 100% - 100px ); overflow: auto;" >
      <table class="table table-striped table-bordered table-sm" >
        <tr>
          <td>ID</td>
          <td>Name/Path</td>
          <td></td>
        </tr>
        <tr v-for="v,i in files">
          <td><pre class="id">{{v['_id']}}</pre></td>
          <td width="70%">
            <div><a v-bind:href="path+'files/'+v['_id']+'/edit'" >/{{ v['name'] }}</a></div>
          </td>
          <td>{{ v['type'] }}</td>
          <td><input type="button" class="btn btn-outline-danger btn-sm" value="X" v-on:click="delete_file(i)" ></td>
        </tr>
      </table>
      </div>
    </div>
  </div>
    <div class="modal fade" id="create_file_modal" tabindex="-1" >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create File</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div>Name/URL Slug</div>
              <input type="text" class="form-control form-control-sm" v-model="new_file['name']" placeholder="Name" v-on:keyup="nchange" >
              <div class="text-secondary small">no spaces. no special chars. except dash(-). lowercase recommended</div>
              <div>&nbsp;</div>
              <div>Type</div>
              <input type="text" class="form-control form-control-sm" v-model="new_file['type']" placeholder="text/html" >
              <div>&nbsp;</div>
              <div v-if="cmsg" class="alert alert-success" >{{ cmsg }}</div>
              <div v-if="cerr" class="alert alert-success" >{{ cerr }}</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm"  v-on:click="createnow">Create</button>
          </div>
        </div>
      </div>
    </div>
</div>
<script type="module">

  var cm = require("<?=$config_global_apimaker_path ?>node/node_modules/codemirror");

 //import {EditorState} from "<?=$config_global_apimaker_path ?>node/node_modules/@codemirror/state"
// import {EditorView, keymap} from "@codemirror/view"
// import {defaultKeymap} from "@codemirror/commands"

  // import { createApp } from '<?=$config_global_apimaker_path ?>node/node_modules/vue/index.js';
  // import { basicSetup } from '<?=$config_global_apimaker_path ?>node/node_modules/codemirror/index.js';
  // import VueCodemirror from '<?=$config_global_apimaker_path ?>node/node_modules/vue-codemirror/index.js';

if( 1==2 ){
var app = createApp({
  data(){
    return {
      path: "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
      app_id: "<?=$app['_id'] ?>",
      app__: <?=json_encode($app) ?>,
      msg: "",
      err: "",
      cmsg: "",
      cerr: "",
      files: [],
      ext: {
        "txt":"text/plain",
        "text":"text/plain",
        "js":"application/javascript",
        "json": "application/json",
        "html": "text/html",
        "xml": "text/xml",
        "svg": "image/svg",
        "css": "text/css",
      },
      show_create_file: false,
      new_file: {
        "name": "",
        "type": "html",
        "ssr": false,
      },
      create_file_modal: false,
      upload_file_modal: false,
      token: "",
    };
  },
  mounted(){
   // this.load_files();
  },
  methods: {
    nchange: function(){
      var m = this.new_file['name'].match(/\.([a-z]{2,5})$/);
      if( m != null ){
        if( m[1] in this.ext ){
          this.new_file['type'] = this.ext[ m[1] ];
        }
      }
    },
    is_token_ok(t){
      if( t!= "OK" && t.match(/^[a-f0-9]{24}$/)==null ){
        setTimeout(this.token_validate,100,t);
        return false;
      }else{
        return true;
      }
    },
    token_validate(t){
      if( t.match(/^(SessionChanged|NetworkChanged)$/) ){
        this.err = "Login Again";
        alert("Need to Login Again");
      }else{
        this.err = "Token Error: " + t;
      }
    },

    
  }
}).mount("#app");
}
</script>