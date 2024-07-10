<div id="app" >
  <div class="leftbar" >
    <?php require("page_apps_leftbar.php"); ?>
  </div>
  <div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
    <div style="padding: 10px;" >
      <div style="float:right;" >
        <button class="btn btn-outline-secondary btn-sm ms-1" v-on:click="previewit" >
          <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M14.851 11.923c-.179-.641-.521-1.246-1.025-1.749-1.562-1.562-4.095-1.563-5.657 0l-4.998 4.998c-1.562 1.563-1.563 4.095 0 5.657 1.562 1.563 4.096 1.561 5.656 0l3.842-3.841.333.009c.404 0 .802-.04 1.189-.117l-4.657 4.656c-.975.976-2.255 1.464-3.535 1.464-1.28 0-2.56-.488-3.535-1.464-1.952-1.951-1.952-5.12 0-7.071l4.998-4.998c.975-.976 2.256-1.464 3.536-1.464 1.279 0 2.56.488 3.535 1.464.493.493.861 1.063 1.105 1.672l-.787.784zm-5.703.147c.178.643.521 1.25 1.026 1.756 1.562 1.563 4.096 1.561 5.656 0l4.999-4.998c1.563-1.562 1.563-4.095 0-5.657-1.562-1.562-4.095-1.563-5.657 0l-3.841 3.841-.333-.009c-.404 0-.802.04-1.189.117l4.656-4.656c.975-.976 2.256-1.464 3.536-1.464 1.279 0 2.56.488 3.535 1.464 1.951 1.951 1.951 5.119 0 7.071l-4.999 4.998c-.975.976-2.255 1.464-3.535 1.464-1.28 0-2.56-.488-3.535-1.464-.494-.495-.863-1.067-1.107-1.678l.788-.785z"/></svg>
        </button>
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

<script>

if( 1==1 ){
var app = Vue.createApp({
  data(){
    return {
      path: "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
      app_id: "<?=$app['_id'] ?>",
      app__: <?=json_encode($app) ?>,
      file__: <?=json_encode($file) ?>,
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
  },
  methods: {
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
    previewit: function(){
      //alert( this.vurl );
      var urls = {};
      if( 'cloud' in this.app__['settings'] ){if( this.app__['settings']['cloud'] ){
          urls['cloud'] = "https://" + this.app__['settings']['cloud-subdomain'] + '.' + this.app__['settings']['cloud-domain'] + '/' + this.app__['settings']['cloud-enginepath'] + this.file__['path'].substr(1,500) + this.file__['name'];
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
        this.ce.setValue( this.file__['data'] );
        //editor.setReadOnly(true);
        var beautiful = ace.require("ace/ext/beautify");
        console.log( beautiful );
        beautiful.beautify(this.ce.session);
      }

    }
  }
}).mount("#app");


}
</script>