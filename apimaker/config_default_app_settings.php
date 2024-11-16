<?php

$config_page_record = json_decode('{
  "_id": "",
  "app_id": "",
  "name": "home",
  "des": "Home page for my app",
  "type": "html",
  "created": "2023-12-05 14:36:40",
  "updated": "2023-12-05 14:36:40",
  "active": true,
  "version": 1,
  "version_id": ""
}',true);
if( json_last_error() ){
	echo "json decode failed: ". json_last_error_msg() ;exit;
}

$config_page_version_record = json_decode('{
  "_id": "",
  "app_id": "",
  "page_id": "",
  "name": "home",
  "des": "Home page for my app",
  "type": "html",
  "created": "2023-12-05 14:36:40",
  "updated": "2024-01-17 16:36:01",
  "active": true,
  "version": 1,
  "html": "<div class=\"col-lg-8 mx-auto p-3 py-md-5\">\n  <header class=\"d-flex align-items-center pb-3 mb-5 border-bottom\">\n    <a href=\"/\" class=\"d-flex align-items-center text-dark text-decoration-none\">\n      <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"40\" height=\"32\" class=\"me-2\" viewBox=\"0 0 118 94\" role=\"img\"><title>Bootstrap</title><path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M24.509 0c-6.733 0-11.715 5.893-11.492 12.284.214 6.14-.064 14.092-2.066 20.577C8.943 39.365 5.547 43.485 0 44.014v5.972c5.547.529 8.943 4.649 10.951 11.153 2.002 6.485 2.28 14.437 2.066 20.577C12.794 88.106 17.776 94 24.51 94H93.5c6.733 0 11.714-5.893 11.491-12.284-.214-6.14.064-14.092 2.066-20.577 2.009-6.504 5.396-10.624 10.943-11.153v-5.972c-5.547-.529-8.934-4.649-10.943-11.153-2.002-6.484-2.28-14.437-2.066-20.577C105.214 5.894 100.233 0 93.5 0H24.508zM80 57.863C80 66.663 73.436 72 62.543 72H44a2 2 0 01-2-2V24a2 2 0 012-2h18.437c9.083 0 15.044 4.92 15.044 12.474 0 5.302-4.01 10.049-9.119 10.88v.277C75.317 46.394 80 51.21 80 57.863zM60.521 28.34H49.948v14.934h8.905c6.884 0 10.68-2.772 10.68-7.727 0-4.643-3.264-7.207-9.012-7.207zM49.948 49.2v16.458H60.91c7.167 0 10.964-2.876 10.964-8.281 0-5.406-3.903-8.178-11.425-8.178H49.948z\" fill=\"currentColor\"></path></svg>\n      <span class=\"fs-4\">Starter template</span>\n    </a>\n  </header>\n\n  <main>\n    <h1>Get started with Bootstrap</h1>\n    <p class=\"fs-5 col-md-8\">Quickly and easily get started with Bootstrap`s compiled, production-ready files with this barebones example featuring some basic HTML and helpful links. Download all our examples to get started.</p>\n\n    <hr class=\"col-3 col-md-2 mb-5\">\n\n    <div class=\"row g-5\">\n      <div class=\"col-md-6\">\n        <h2>Starter projects</h2>\n        <p>Ready to beyond the starter template? Check out these open source projects that you can quickly duplicate to a new GitHub repository.</p>\n        <ul class=\"icon-list\">\n          <li><a href=\"https://github.com/twbs/bootstrap-npm-starter\" rel=\"noopener\" target=\"_blank\">Bootstrap npm starter</a></li>\n  !cropped...",
  "settings": {
    "one": 1
  }
}',true);
if( json_last_error() ){
	echo "json decode failed: ". json_last_error_msg() ;exit;
}

