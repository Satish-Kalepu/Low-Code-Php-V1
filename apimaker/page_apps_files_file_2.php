
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/htmlmixed/htmlmixed.min.js"></script>
 -->
<!-- 
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/php/php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/python/python.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/vue/vue.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/lint/json-lint.min.js"></script> 
-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" />
<script type="module">
//import {minimalSetup, EditorView} from CodeMirror

var value = <?=json_encode($file['data']) ?>;

CodeMirror(document.querySelector('#editor'), {
  lineNumbers: true,
  tabSize: 4,
  mode: '<?=$mode ?>',
  value: `value`
});

</script>

<div><?=$mode ?></div>
<div id="editor"></div>


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
<script>
var app = Vue.createApp({
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
    this.load_files();
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
    load_files(){
      this.msg = "Loading...";
      this.err = "";
      axios.post("?", {
        "action":"get_token",
        "event":"getfiles."+this.app_id,
        "expire":2
      }).then(response=>{
        this.msg = "";
        if( response.status == 200 ){
          if( typeof(response.data) == "object" ){
            if( 'status' in response.data ){
              if( response.data['status'] == "success" ){
                this.token = response.data['token'];
                if( this.is_token_ok(this.token) ){
                  this.load_files2();
                }
              }else{
                alert("Token error: " + response.dat['data']);
                this.err = "Token Error: " + response.data['data'];
              }
            }else{
              this.err = "Incorrect response";
            }
          }else{
            this.err = "Incorrect response Type";
          }
        }else{
          this.err = "Response Error: " . response.status;
        }
      });
    },
    load_files2(){
      this.msg = "Loading...";
      this.err = "";
      axios.post("?",{"action":"get_files","app_id":this.app_id,"token":this.token}).then(response=>{
        this.msg = "";
        if( response.status == 200 ){
          if( typeof(response.data) == "object" ){
            if( 'status' in response.data ){
              if( response.data['status'] == "success" ){
                this.files = response.data['data'];
              }else{
                alert("Token error: " + response.data['error']);
                this.err = "Token Error: " + response.data['error'];
              }
            }else{
              this.err = "Incorrect response";
            }
          }else{
            this.err = "Incorrect response Type";
          }
        }else{
          this.err = "Response Error: " . response.status;
        }
      });
    },
    file_show_create_form(){
      this.create_file_modal = new bootstrap.Modal(document.getElementById('create_file_modal'));
      this.create_file_modal.show();
      this.cmsg = ""; this.cerr = "";
    },
    file_show_upload_form(){
      this.upload_file_modal = new bootstrap.Modal(document.getElementById('upload_file_modal'));
      this.upload_file_modal.show();
      this.cmsg = ""; this.cerr = "";
    },
    cleanit( v ){
      v = v.replace( /\-/g, "DASH" );
      v = v.replace( /\//g, "SLASHS" );
      v = v.replace( /\_/g, "UDASH" );
      v = v.replace( /\./g, "DOTT" );
      v = v.replace( /\W/g, "-" );
      v = v.replace( /DASH/g, "-" );
      v = v.replace( /UDASH/g, "_" );
      v = v.replace( /DOTT/g, "." );
      v = v.replace( /SLASHS/g, "/" );
      v = v.replace( /[\-]{2,5}/g, "-" );
      v = v.replace( /[\_]{2,5}/g, "_" );
      return v;
    },
    createnow(){
      this.cerr = "";
      this.new_file['name'] = this.cleanit(this.new_file['name']);
      if( this.new_file['name'].match(/^[a-z0-9\.\-\_\/]{3,100}\.[a-z]{2,4}$/i) == null ){
        this.cerr = "Filename must have an extension. Special chars not allowed. Length minimum 4 max 100";
        return false;
      }
      if( this.new_file['type'].match(/^[a-z]{2,50}\/[a-z]{2,50}$/i) == null ){
        this.cerr = "File type incorrect format";
        return false;
      }
      this.cmsg = "Creating...";
      axios.post("?", {
        "action": "create_file", 
        "new_file": this.new_file
      }).then(response=>{
        this.cmsg = "";
        if( response.status == 200 ){
          if( typeof(response.data) == "object" ){
            if( 'status' in response.data ){
              if( response.data['status'] == "success" ){
                this.cmsg = "Created";
                this.create_file_modal.hide();
                this.load_files();
              }else{
                this.cerr = response.data['error'];
              }
            }else{
              this.cerr = "Incorrect response";
            }
          }else{
            this.cerr = "Incorrect response Type";
          }
        }else{
          this.cerr = "Response Error: " . response.status;
        }
      });
    },
    delete_file( vi ){
      if( confirm("Are you sure?") ){
        this.msg = "Deleting...";
        this.err = "";
        axios.post("?", {
          "action":"get_token",
          "event":"deletefile"+this.app_id+this.files[vi]['_id'],
          "expire":1
        }).then(response=>{
          this.msg = "";
          if( response.status == 200 ){
            if( typeof(response.data) == "object" ){
              if( 'status' in response.data ){
                if( response.data['status'] == "success" ){
                  this.token = response.data['token'];
                  if( this.is_token_ok(this.token) ){
                    axios.post("?", {
                      "action":"delete_file",
                      "token":this.token,
                      "file_id": this.files[ vi ]['_id']
                    }).then(response=>{
                      this.msg = "";
                      if( response.status == 200 ){
                        if( typeof(response.data) == "object" ){
                          if( 'status' in response.data ){
                            if( response.data['status'] == "success" ){
                              this.load_files();
                            }else{
                              alert("Token error: " + response.data['data']);
                              this.err = "Token Error: " + response.data['data'];
                            }
                          }else{
                            this.err = "Incorrect response";
                          }
                        }else{
                          this.err = "Incorrect response Type";
                        }
                      }else{
                        this.err = "Response Error: " . response.status;
                      }
                    });
                  }
                }else{
                  alert("Token error: " + response.dat['data']);
                  this.err = "Token Error: " + response.data['data'];
                }
              }else{
                this.err = "Incorrect response";
              }
            }else{
              this.err = "Incorrect response Type";
            }
          }else{
            this.err = "Response Error: " . response.status;
          }
        });
      }
    }
  }
}).mount("#app");
</script>