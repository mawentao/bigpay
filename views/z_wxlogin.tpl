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
            require("wxlogin/page").execute();
        }};
    });
    jQuery(document).ready(function($) {
        require(["main"],function(main){
            main.init(); 
        });
    });   
/*
    jQuery(document).ready(function($) {
        genurl();
    });
    function genurl() {
        var loginurl = v.loginurl;
        var code = "<a href='"+loginurl+"' target='_blank'>"+loginurl+"</a>";
        jQuery('#login-url-td').html(code);
        
        jQuery('#login-url-qr').html('');
        var qrcode = new QRCode(document.getElementById('login-url-qr'), {
            width  : 150,
            height : 150
        });
	    qrcode.makeCode(loginurl);
    }
*/
  </script>
</head>
<body>
  <div id='navdiv' style='margin-top:10px;'></div>
  <!-- 登陆入口 -->
  <div id='login-div' style='margin-top:10px;display:none;'>
    <table class="tb tb2">
      <tr><th class="partition">微信登录入口（仅限在微信APP中打开）</th></tr>
      <tr><td>
        <p id='login-url-td' style='margin:10px 0;'></p>
        <p id='login-url-qr'></p>
      </td></tr>
    </table>  
  </div>
  <!-- 微信用户列表 -->
  <div id='userlist-div' style='margin-top:10px;display:none;'>
    <div id="usergrid-div">userlist</div>
  </div>
</body>
</html>
