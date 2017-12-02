<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title></title>
  <link rel="stylesheet" href="<%plugin_path%>/template/libs/mwt/2.6.1/mwt.min.css"/>
  <script src="<%plugin_path%>/template/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="<%plugin_path%>/template/libs/requirejs/2.1.9/require.js"></script>
  <%js_script%>
  <script>
    require.config({
        baseUrl: "<%plugin_path%>/views/src/",
        packages: [
            {name:'jquery', location:'<%plugin_path%>/template/libs/jquery/1.11.2', main:'jquery.min'},
            {name:'qrcode', location:'<%plugin_path%>/template/libs', main:'qrcode'},
            {name:'mwt', location:'<%plugin_path%>/template/libs/mwt/2.6.1', main:'mwt.min'}
        ]
    });
    define("main",function(require){
        require("mwt");
        return { init:function(){
            require("wxpay/page").execute();
        }};
    });
    jQuery(document).ready(function($) {
        require(["main"],function(main){
            main.init(); 
        });
    });
  </script>
</head>
<body>
  <div id='navdiv' style='margin-top:10px;'></div>
  <!-- pageset -->
  <div id='payorder-div' style='margin-top:10px;display:none;'>
    <div id="payorder-grid-div">pay order</div>
    <div id="payorder-dialog-div"></div>
    <div id="qr-dialog-div"></div>
  </div>
  <!-- category set -->
  <div id='paylog-div' style='margin-top:10px;display:none;'>
    <div id="paylog-grid-div">pay log</div>
  </div>
</body>
</html>
