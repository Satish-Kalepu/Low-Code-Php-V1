<div id="app" >
  <div class="leftbar" >
    <?php require("page_apps_leftbar.php"); ?>
  </div>
  <div style="position: fixed;left:150px; top:40px; height: calc( 100% - 40px ); width:calc( 100% - 150px ); background-color: white; " >
    <div style="padding: 10px;" >
      <div style="float:right;" >
        <button class="btn btn-outline-dark btn-sm" v-on:click="saveit" >SAVE</button>
        <button class="btn btn-outline-secondary btn-sm" v-on:click="previewit" >
          <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M14.851 11.923c-.179-.641-.521-1.246-1.025-1.749-1.562-1.562-4.095-1.563-5.657 0l-4.998 4.998c-1.562 1.563-1.563 4.095 0 5.657 1.562 1.563 4.096 1.561 5.656 0l3.842-3.841.333.009c.404 0 .802-.04 1.189-.117l-4.657 4.656c-.975.976-2.255 1.464-3.535 1.464-1.28 0-2.56-.488-3.535-1.464-1.952-1.951-1.952-5.12 0-7.071l4.998-4.998c.975-.976 2.256-1.464 3.536-1.464 1.279 0 2.56.488 3.535 1.464.493.493.861 1.063 1.105 1.672l-.787.784zm-5.703.147c.178.643.521 1.25 1.026 1.756 1.562 1.563 4.096 1.561 5.656 0l4.999-4.998c1.563-1.562 1.563-4.095 0-5.657-1.562-1.562-4.095-1.563-5.657 0l-3.841 3.841-.333-.009c-.404 0-.802.04-1.189.117l4.656-4.656c.975-.976 2.256-1.464 3.536-1.464 1.279 0 2.56.488 3.535 1.464 1.951 1.951 1.951 5.119 0 7.071l-4.999 4.998c-.975.976-2.255 1.464-3.535 1.464-1.28 0-2.56-.488-3.535-1.464-.494-.495-.863-1.067-1.107-1.678l.788-.785z"/></svg>
        </button>
      </div>
      <div class="h3 mb-3">File: {{ file__['name'] }}</div>
      <div style="height: calc( 100% - 100px ); " >

          <div id="editorblock" style="width:100%; height: calc( 100% - 100px ); " ></div>

      </div>
    </div>
  </div>

  <div style="position: fixed;left:150px; bottom:0px; width:calc( 100% - 150px ); background-color: white; " >
    <div v-if="msg" class="alert alert-primary" >{{ msg }}</div>
    <div v-if="err" class="alert alert-danger" >{{ err }}</div>
  </div>

</div>
<script defer src="<?=$config_global_apimaker_path ?>js/codemirror.js" ></script>
<script>

if( 1==1 ){
var app = Vue.createApp({
  data(){
    return {
      path: "<?=$config_global_apimaker_path ?>apps/<?=$app['_id'] ?>/",
      app_id: "<?=$app['_id'] ?>",
      app__: <?=json_encode($app) ?>,
      file__: <?=json_encode($file) ?>,
      vurl: "https://<?=$app['cloud-subdomain'] . ".". $app['cloud-domain'] . "/" . $app['cloud-enginepath'] ?>/",
      msg: "",
      err: "",
      cmsg: "",
      cerr: "",
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
      token: "",
      code: `var a = 10;`,
      extensions: [],

    };
  },

  mounted(){
    setTimeout(this.init,100);
  },
  methods: {
    saveit: function(){
      //console.log( this.ce.viewState.state.doc.text );
      this.file__['data'] = this.ce.viewState.state.doc.text.valueOf().join("\n");
      console.log( this.file__['data'] );
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
      alert( this.vurl );
    },
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

    init: function(){

      if( typeof(CM) == "undefined" ){
        console.log("Waiting");
        setTimeout(this.init,100);
      }else{

        const {basicSetup,EditorView,drawSelection} = CM["codemirror"];
        const {javascript,javascriptLanguage,scopeCompletionSource} = CM["@codemirror/lang-javascript"];
        const {lineNumbers,keymap} = CM["@codemirror/view"];
        const {EditorState, Compartment} =  CM["@codemirror/state"];
        const {indentWithTab} =  CM["@codemirror/commands"];
        const {htmlLanguage, html} =  CM["@codemirror/lang-html"];
        const {language,syntaxHighlighting,  defaultHighlightStyle} =  CM["@codemirror/language"];
        const {python} = CM["@codemirror/lang-python"];


        const fixedHeightEditor = EditorView.theme({
          ".cm-content, .cm-gutter": {height: "400px",maxHeight: "400px"},
          ".cm-scroller": {overflow: "auto"}
        })

        state = EditorState.create({
          extensions: [
            basicSetup, 
            javascript(), 
            //autoLanguage, 
            lineNumbers(),
            keymap.of([indentWithTab]),
            syntaxHighlighting(defaultHighlightStyle),
            fixedHeightEditor,
          ],
        });
        this.cs = state;

        this.ce = new EditorView({
          doc: `function hello(who = "world") {
          console.log(\`Hello, \${who}!\`)
          }`,
          state,
          parent: document.querySelector("#editorblock")
        });
      }


    }
  }
}).mount("#app");


}
</script>