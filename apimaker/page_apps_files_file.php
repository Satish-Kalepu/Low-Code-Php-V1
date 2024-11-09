<script  src="<?=$config_global_apimaker_path ?>js/beautify-html.js" ></script>
<script  src="<?=$config_global_apimaker_path ?>js/beautify-css.js" ></script>
<script  src="<?=$config_global_apimaker_path ?>js/beautify.js" ></script>

<div id="app" >
  <div class="leftbar" >
    <?php require("page_apps_leftbar.php"); ?>
  </div>
  <div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
    <div style="padding: 10px;" >
      <div style="float:right;" >
        <button class="btn btn-outline-dark btn-sm me-2" v-on:click="showvars" >
          Variables
        </button>
        <button class="btn btn-outline-dark btn-sm me-2" v-on:click="showsetting" id="showsetting" >
          <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="20px" height="20px"><path d="M 22.205078 2 A 1.0001 1.0001 0 0 0 21.21875 2.8378906 L 20.246094 8.7929688 C 19.076509 9.1331971 17.961243 9.5922728 16.910156 10.164062 L 11.996094 6.6542969 A 1.0001 1.0001 0 0 0 10.708984 6.7597656 L 6.8183594 10.646484 A 1.0001 1.0001 0 0 0 6.7070312 11.927734 L 10.164062 16.873047 C 9.583454 17.930271 9.1142098 19.051824 8.765625 20.232422 L 2.8359375 21.21875 A 1.0001 1.0001 0 0 0 2.0019531 22.205078 L 2.0019531 27.705078 A 1.0001 1.0001 0 0 0 2.8261719 28.691406 L 8.7597656 29.742188 C 9.1064607 30.920739 9.5727226 32.043065 10.154297 33.101562 L 6.6542969 37.998047 A 1.0001 1.0001 0 0 0 6.7597656 39.285156 L 10.648438 43.175781 A 1.0001 1.0001 0 0 0 11.927734 43.289062 L 16.882812 39.820312 C 17.936999 40.39548 19.054994 40.857928 20.228516 41.201172 L 21.21875 47.164062 A 1.0001 1.0001 0 0 0 22.205078 48 L 27.705078 48 A 1.0001 1.0001 0 0 0 28.691406 47.173828 L 29.751953 41.1875 C 30.920633 40.838997 32.033372 40.369697 33.082031 39.791016 L 38.070312 43.291016 A 1.0001 1.0001 0 0 0 39.351562 43.179688 L 43.240234 39.287109 A 1.0001 1.0001 0 0 0 43.34375 37.996094 L 39.787109 33.058594 C 40.355783 32.014958 40.813915 30.908875 41.154297 29.748047 L 47.171875 28.693359 A 1.0001 1.0001 0 0 0 47.998047 27.707031 L 47.998047 22.207031 A 1.0001 1.0001 0 0 0 47.160156 21.220703 L 41.152344 20.238281 C 40.80968 19.078827 40.350281 17.974723 39.78125 16.931641 L 43.289062 11.933594 A 1.0001 1.0001 0 0 0 43.177734 10.652344 L 39.287109 6.7636719 A 1.0001 1.0001 0 0 0 37.996094 6.6601562 L 33.072266 10.201172 C 32.023186 9.6248101 30.909713 9.1579916 29.738281 8.8125 L 28.691406 2.828125 A 1.0001 1.0001 0 0 0 27.705078 2 L 22.205078 2 z M 23.056641 4 L 26.865234 4 L 27.861328 9.6855469 A 1.0001 1.0001 0 0 0 28.603516 10.484375 C 30.066026 10.848832 31.439607 11.426549 32.693359 12.185547 A 1.0001 1.0001 0 0 0 33.794922 12.142578 L 38.474609 8.7792969 L 41.167969 11.472656 L 37.835938 16.220703 A 1.0001 1.0001 0 0 0 37.796875 17.310547 C 38.548366 18.561471 39.118333 19.926379 39.482422 21.380859 A 1.0001 1.0001 0 0 0 40.291016 22.125 L 45.998047 23.058594 L 45.998047 26.867188 L 40.279297 27.871094 A 1.0001 1.0001 0 0 0 39.482422 28.617188 C 39.122545 30.069817 38.552234 31.434687 37.800781 32.685547 A 1.0001 1.0001 0 0 0 37.845703 33.785156 L 41.224609 38.474609 L 38.53125 41.169922 L 33.791016 37.84375 A 1.0001 1.0001 0 0 0 32.697266 37.808594 C 31.44975 38.567585 30.074755 39.148028 28.617188 39.517578 A 1.0001 1.0001 0 0 0 27.876953 40.3125 L 26.867188 46 L 23.052734 46 L 22.111328 40.337891 A 1.0001 1.0001 0 0 0 21.365234 39.53125 C 19.90185 39.170557 18.522094 38.59371 17.259766 37.835938 A 1.0001 1.0001 0 0 0 16.171875 37.875 L 11.46875 41.169922 L 8.7734375 38.470703 L 12.097656 33.824219 A 1.0001 1.0001 0 0 0 12.138672 32.724609 C 11.372652 31.458855 10.793319 30.079213 10.427734 28.609375 A 1.0001 1.0001 0 0 0 9.6328125 27.867188 L 4.0019531 26.867188 L 4.0019531 23.052734 L 9.6289062 22.117188 A 1.0001 1.0001 0 0 0 10.435547 21.373047 C 10.804273 19.898143 11.383325 18.518729 12.146484 17.255859 A 1.0001 1.0001 0 0 0 12.111328 16.164062 L 8.8261719 11.46875 L 11.523438 8.7734375 L 16.185547 12.105469 A 1.0001 1.0001 0 0 0 17.28125 12.148438 C 18.536908 11.394293 19.919867 10.822081 21.384766 10.462891 A 1.0001 1.0001 0 0 0 22.132812 9.6523438 L 23.056641 4 z M 25 17 C 20.593567 17 17 20.593567 17 25 C 17 29.406433 20.593567 33 25 33 C 29.406433 33 33 29.406433 33 25 C 33 20.593567 29.406433 17 25 17 z M 25 19 C 28.325553 19 31 21.674447 31 25 C 31 28.325553 28.325553 31 25 31 C 21.674447 31 19 28.325553 19 25 C 19 21.674447 21.674447 19 25 19 z"/></svg>
        </button>
        <button class="btn btn-outline-dark btn-sm  me-2" v-on:click="previewit" id="previewbtn" >
          <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M14.851 11.923c-.179-.641-.521-1.246-1.025-1.749-1.562-1.562-4.095-1.563-5.657 0l-4.998 4.998c-1.562 1.563-1.563 4.095 0 5.657 1.562 1.563 4.096 1.561 5.656 0l3.842-3.841.333.009c.404 0 .802-.04 1.189-.117l-4.657 4.656c-.975.976-2.255 1.464-3.535 1.464-1.28 0-2.56-.488-3.535-1.464-1.952-1.951-1.952-5.12 0-7.071l4.998-4.998c.975-.976 2.256-1.464 3.536-1.464 1.279 0 2.56.488 3.535 1.464.493.493.861 1.063 1.105 1.672l-.787.784zm-5.703.147c.178.643.521 1.25 1.026 1.756 1.562 1.563 4.096 1.561 5.656 0l4.999-4.998c1.563-1.562 1.563-4.095 0-5.657-1.562-1.562-4.095-1.563-5.657 0l-3.841 3.841-.333-.009c-.404 0-.802.04-1.189.117l4.656-4.656c.975-.976 2.256-1.464 3.536-1.464 1.279 0 2.56.488 3.535 1.464 1.951 1.951 1.951 5.119 0 7.071l-4.999 4.998c-.975.976-2.255 1.464-3.535 1.464-1.28 0-2.56-.488-3.535-1.464-.494-.495-.863-1.067-1.107-1.678l.788-.785z"/></svg>
        </button>
        <button class="btn btn-outline-dark btn-sm  me-2" v-on:click="saveit" >SAVE</button>
      </div>
      <div class="h4 mb-3" style="display: flex;">
        <div>File: </div>
        <div style="min-width:5px; cursor: pointer; padding:0px 5px; border:1px solid #ccc;" v-on:click="change_path('/')" >/</div>
        <div v-for="vv in paths" style="min-width:20px; cursor: pointer; padding:0px 5px; border:1px solid #ccc;" v-on:click="change_path(vv['tp'])" >{{ vv['p'] }}/</div>
        <div>{{ file__['name'] }}</div>
      </div>
      <div style="height: calc( 100% - 100px ); " >
          <div v-if="file__['t']=='inline'" id="editorblock" style="width:100%; height: 100%; " ></div>
          <div v-if="file__['t']=='base64'" style="width:100%; height: 100%; text-align: center; " >
            <template v-if="file__['type'].indexOf('image')>-1" >
              <img v-bind:src="image_src" style="max-width:100%; max-height: 100%;vertical-align: middle; " >
            </template>
            <template v-else >
              <div>{{ file__['type'] }}</div>
              <div>File is not editable</div>
            </template>
          </div>
      </div>
    </div>
  </div>

  <div style="position: fixed;left:150px; bottom:0px; width:calc( 100% - 150px ); background-color: white; " >
    <div v-if="msg" class="alert alert-primary alert-sm p-2" >{{ msg }}</div>
    <div v-if="err" class="alert alert-danger alert-sm p-2" >{{ err }}</div>
  </div>


    <div class="modal fade" id="url_modal" tabindex="-1" >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Browse/Download File</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <template v-if="'cloud' in vurls" >
                <p>Cloud Hosting: </p>
                <p>
                  <a target="_blank" v-bind:href="vurls['cloud']" >{{ vurls['cloud'] }}</a>
                </p>
                <template v-if="'alias' in vurls" >
                  <p>Alias domain:</p>
                  <p>
                    <a target="_blank" v-bind:href="vurls['alias']" >{{ vurls['alias'] }}</a>
                  </p>
                </template>
              </template>
              <template v-if="'domains' in vurls" >
                <p>Custom Hosting: </p>
                <p v-for="u in vurls['domains']" >
                  <a target="_blank" v-bind:href="u" >{{ u }}</a>
                </p>
              </template>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="setting_modal" tabindex="-1" >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit File Settings</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div>Name/URL Slug</div>
              <input type="text" class="form-control form-control-sm" v-model="edit_file['name']" placeholder="Name" v-on:keyup="nchange" >
              <div class="text-secondary small">no spaces. no special chars. except dash(-). lowercase recommended</div>
              <div>&nbsp;</div>
              <div>Type</div>
              <input type="text" list="eee" class="form-control form-control-sm" v-model="edit_file['type']" placeholder="text/html" >
              <datalist id="eee" ><option v-for="v in ext" v-bind:value="v" ></option></datalist>
              <div>&nbsp;</div>
              <div v-if="cmsg" class="alert alert-success" >{{ cmsg }}</div>
              <div v-if="cerr" class="alert alert-success" >{{ cerr }}</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm"  v-on:click="updatesettings">Update</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="vars_modal" tabindex="-1" >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Global Variables</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div>--engine-path--</div>
              <div>--engine-url--</div>
          </div>
        </div>
      </div>
    </div>


</div>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ace.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-language_tools.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-beautify.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-modelist.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-options.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-searchbox.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-statusbar.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-themelist.js" ></script>
<script defer src="<?=$config_global_apimaker_path ?>ace/src/ext-searchbox.js" ></script>

<style>
  .ace_editor{ font-size:1rem; }
</style>

<script>

if( 1==1 ){
var app = Vue.createApp({
  data(){
    return {
      path: "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
      app_id: "<?=$app['_id'] ?>",
      app__: <?=json_encode($app) ?>,
      file__: <?=json_encode($file) ?>,
      test_envs__: [],
      test_url__: "",
      test_domain__: "",
      image_src: "",
      vurls: {},
      current_path: "/",
      paths: [],
      msg: "",
      err: "",
      cmsg: "",
      cerr: "",
      ext: {
        "txt":"text/plain",
        "text":"text/plain",
        "js":"text/javascript",
        "json": "application/json",
        "html": "text/html",
        "xml": "text/xml",
        "svg": "image/svg",
        "css": "text/css",
      },
      token: "",
      code: `var a = 10;`,
      extensions: [],
      setting_modal: false,
      vars_modal: false,
      url_modal: false,
      edit_file: {'name':'', 'type':'',},
    };
  },

  mounted(){
    if( this.file__['t'] =="base64" && this.file__['type'].indexOf("image") === 0 ){
      if( 'data' in this.file__ == false ){
        this.load_content();
      }else{
        this.isitimage();
      }
    }else if( this.file__['t'] == "inline" ){
      setTimeout(this.init,100);
    }
    this.update_paths();
    this.set_test_environments__();
  },
  methods: {

    set_test_environments__: function(){
      var e = [];
      for( var d in this.app__['settings']['domains'] ){
        e.push({
          "t": "custom",
          "u": this.app__['settings']['domains'][ d ]['url'],
          "d": this.app__['settings']['domains'][ d ]['domain'],
        });
      }
      if( 'cloud' in this.app__['settings'] ){
        if( this.app__['settings']['cloud'] ){
          var d = this.app__['settings']['cloud-subdomain'] + "." + this.app__['settings']['cloud-domain'];
          e.push({
            "t": "cloud",
            "u": "https://" + d + "/",
            "d": d,
          });
        }
      }
      if( 'alias' in this.app__['settings'] ){
        if( this.app__['settings']['alias'] ){
          var d = this.app__['settings']['alias-domain'];
          e.push({
            "t": "cloud-alias",
            "u": "https://" + d + "/",
            "d": d,
          });
        }
      }
      this.test_envs__ = e;
      if( e.length == 1 ){
        this.test_domain__ = e[1]['d']+'';
        this.select_test_environment__2();
      }
    },
    select_test_environment__: function(){
      setTimeout(this.select_test_environment__2,200);
    },
    select_test_environment__2: function(){
      for( var i=0;i<this.test_envs__.length;i++ ){
        //in this.app__['settings']['domains'] ){
        if( this.test_envs__[i]['d'] == this.test_domain__ ){
          //this.test__['path'] = this.app__['settings']['domains'][ d ]['path'];
          var tu = this.test_envs__[i]['u'] + "?version_id=<?=$config_param4 ?>&test_token=<?=md5($config_param4) ?>";
          if( this.test_debug__ ){
            tu  = tu + "&debug=true";
          }
          if( this.api__['input-method'] == "GET" ){
            tu = tu + "&" + this.make_query_string__( this.test__['factors']['v'] );
          }
          this.test_url__ = tu;
          break;
        }
      }
    },

    isitimage: function(){
      if( this.file__['type'].match(/^image/i) ){
        this.image_src = "data:"+this.file__['type']+";base64," + this.file__['data'];
      }
    },
    load_content: function(){
      axios.post("?", {
        "action": "file_load_content", 
        "file_id": this.file__['_id'],
      }).then(response=>{
        this.cmsg = "";
        if( response.status == 200 ){
          if( typeof(response.data) == "object" ){
            if( 'status' in response.data ){
              if( response.data['status'] == "success" ){
                this.file__['data'] = response.data['data'];
                this.isitimage();
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
    change_path: function( tp ){
      console.log("change path: "+ tp );
      document.location = this.path + "files/?path=" + encodeURIComponent( tp );
    },
    update_paths(){
        console.log( this.file__['path'] );
        var paths = this.file__['path'].split(/\//g);
        paths.splice(0, 1);
        if( paths[ paths.length-1 ] == "" ){
          paths.pop();
        }
        var p = [];
        var tp = "/";
        console.log( JSON.stringify(paths,null,4) );
        for(var i=0;i<paths.length;i++){
          tp = tp + paths[i] + "/";
          p.push({
            "p":paths[i],
            "tp": tp+'',
          });
        }
        this.paths = p;
        console.log( JSON.stringify(p,null,4) );
    },
    showsetting: function(){
      this.edit_file = {
        'name': this.file__['name']+'',
        'type': this.file__['type']+'',
      };
      this.setting_modal = new bootstrap.Modal(document.getElementById('setting_modal'));
      this.setting_modal.show();
      this.cmsg = ""; this.cerr = "";
    },
    showvars: function(){
      this.vars_modal = new bootstrap.Modal(document.getElementById('vars_modal'));
      this.vars_modal.show();
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
    updatesettings: function(){
      //console.log( this.ce.viewState.state.doc.text );
      this.cmsg = "Loading...";
      this.cerr = "";
      axios.post("?", {
        "action":"get_token",
        "event":"file.setting.save."+this.app_id+"."+this.file__['_id'],
        "expire":2
      }).then( response=>{
        this.msg = "";
        if( response.status == 200 ){
          if( typeof(response.data) == "object" ){
            if( 'status' in response.data ){
              if( response.data['status'] == "success" ){
                this.token = response.data['token'];
                if( this.is_token_ok(this.token) ){
                  this.updatesettings2();
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
    updatesettings2(){
      this.cerr = "";
      this.edit_file['name'] = this.cleanit(this.edit_file['name']);
      if( this.edit_file['name'].match(/^[a-z0-9\.\-\_\/]{3,100}\.[a-z]{2,4}$/i) == null ){
        this.cerr = "Filename must have an extension. Special chars not allowed. Length minimum 4 max 100";
        return false;
      }
      if( this.edit_file['type'].match(/^[a-z]{2,50}\/[a-z]{2,50}$/i) == null ){
        this.cerr = "File type incorrect format";
        return false;
      }
      this.cmsg = "Updating...";
      axios.post("?", {
        "action": "file_update_settings", 
        "edit_file": this.edit_file,
        "app_id": "<?=$config_param1 ?>",
        "file_id": "<?=$config_param3 ?>",
        "token": this.token,
      }).then(response=>{
        this.cmsg = "";
        if( response.status == 200 ){
          if( typeof(response.data) == "object" ){
            if( 'status' in response.data ){
              if( response.data['status'] == "success" ){
                this.cmsg = "Success";
                this.setting_modal.hide();
                this.file__['name'] = this.edit_file['name']+'';
                this.file__['type'] = this.edit_file['type']+'';
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
    nchange: function(){
      var m = this.edit_file['name'].match(/\.([a-z]{2,5})$/);
      if( m != null ){
        if( m[1] in this.ext ){
          this.edit_file['type'] = this.ext[ m[1] ];
        }
      }
    },
    saveit: function(){
      //console.log( this.ce.viewState.state.doc.text );
      this.file__['data'] = this.ce.getValue();

      if(  this.file__['ext']  == 'html' ){
        this.file__['data'] = html_beautify(this.file__['data']);
      }else if(  this.file__['ext']  == 'js' ){
        this.file__['data'] = js_beautify(this.file__['data']);
      }else if(  this.file__['ext']  == 'css' ){
        this.file__['data'] = css_beautify(this.file__['data']);
      }

      this.ce.setValue( this.file__['data'] );

      this.msg = "Loading...";
      this.err = "";
      axios.post("?", {
        "action":"get_token",
        "event":"file.save."+this.app_id+"."+this.file__['_id'],
        "expire":2
      }).then( response=>{
        this.msg = "";
        if( response.status == 200 ){
          if( typeof(response.data) == "object" ){
            if( 'status' in response.data ){
              if( response.data['status'] == "success" ){
                this.token = response.data['token'];
                if( this.is_token_ok(this.token) ){
                  this.saveit2();
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
    saveit2(){
      this.msg = "Loading...";
      this.err = "";
      axios.post("?",{
        "action":"file_save_content",
        "app_id":this.app_id,
        "file_id":this.file__['_id'],
        "token":this.token,
        "data": this.file__['data'],
      }).then(response=>{
        this.msg = "";
        if( response.status == 200 ){
          if( typeof(response.data) == "object" ){
            if( 'status' in response.data ){
              if( response.data['status'] == "success" ){
                this.msg = "Success";
                setTimeout(function(v){v.msg="";},5000,this);
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
    previewit: function(){
      //alert( this.vurl );
      var urls = {};
      if( 'cloud' in this.app__['settings'] ){if( this.app__['settings']['cloud'] ){
          urls['cloud'] = "https://" + this.app__['settings']['cloud-subdomain'] + '.' + this.app__['settings']['cloud-domain'] + '/' + this.app__['settings']['cloud-enginepath'] + this.file__['path'].substr(1,500) + this.file__['name'];

          if( 'alias' in this.app__['settings'] ){if( this.app__['settings']['alias'] ){
            urls['alias'] = "https://" + this.app__['settings']['alias-domain'] + this.file__['path'].substr(1,500) + this.file__['name'];
        }}
      }}

      if( 'domains' in this.app__['settings'] ){
        urls['domains'] = [];
        for(var d=0;d<this.app__['settings']['domains'].length;d++ ){
          urls['domains'].push( this.app__['settings']['domains'][ d ]['url'] + this.file__['path'].substr(1,500) + this.file__['name'] );
        }
      }
      this.vurls = urls;
      this.url_modal = new bootstrap.Modal(document.getElementById('url_modal'));
      this.url_modal.show();
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

    init: function(){

      if( typeof(ace) == "undefined" ){
        console.log("Waiting");
        setTimeout(this.init,100);
      }else{

        //https://github.com/ajaxorg/ace/wiki/Configuring-Ace

        this.ce = ace.edit("editorblock");
        this.ce.setOptions({
          enableAutoIndent: true,
          behavioursEnabled: true,
          useSoftTabs: true,
          showPrintMargin: false,
          printMargin: false,
          showFoldWidgets: true,
          showLineNumbers: true,
          customScrollbar: true,
          //fontSize: "12px",
          //fontFamily: "Arial",
          // theme:
          // mode: 
          // tabSize: number
          // wrap: "off"|"free"|"printmargin"|boolean|number
          //readOnly: false,
        });
        //this.ce.setTheme("ace/theme/monokai");
        console.log( this.file__['ext'] );
        if( this.file__['ext'] == "js" ){
          this.ce.session.setMode("ace/mode/javascript");
        }else{
          this.ce.session.setMode("ace/mode/html");
        }
        this.ce.commands.addCommands([{ 
          name: "showSettingsMenu",
          bindKey: {win: "Ctrl-q", mac: "Ctrl-q"},
          exec: function(editor) {
            editor.showSettingsMenu();
          },
          readOnly: true
        }]);
        if(  this.file__['ext']  == 'html' ){
          this.file__['data'] = html_beautify(this.file__['data']);
        }else if(  this.file__['ext']  == 'js' ){
          this.file__['data'] = js_beautify(this.file__['data']);
        }else if(  this.file__['ext']  == 'css' ){
          this.file__['data'] = css_beautify(this.file__['data']);
        }
        this.ce.setValue( this.file__['data'] );
        //editor.setReadOnly(true);
        //var beautiful = ace.require("ace/ext/beautify");
        //console.log( beautiful );
        //beautiful.beautify(this.ce.session);
      }

    }
  }
}).mount("#app");


}
</script>