<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title></title>
  <link rel="stylesheet" href="<%plugin_path%>/template/libs/mwt/2.6.0/mwt.min.css"/>
  <script src="<%plugin_path%>/template/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="<%plugin_path%>/template/libs/mwt/2.6.0/mwt.min.js" charset="utf-8"></script>
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
        require("common/file_upload");
        return { init:function(){
            page_init();
        }};
    });
    jQuery(document).ready(function($) {
        require(["main"],function(main){
            main.init(); 
        });
    });

    function page_init() 
    {
        jQuery('[name=wx_app_id]').val(v.wx_app_id);
        jQuery('[name=wx_app_secret]').val(v.wx_app_secret);
        jQuery('[name=wx_login_callback]').val(v.wx_login_callback);
        parse_login_callback_domain();
        jQuery('[name=wx_login_landpage]').val(v.wx_login_landpage);

        jQuery('[name=wx_mchid]').val(v.wx_mchid);
        jQuery('[name=wx_mchname]').val(v.wx_mchname);
        jQuery('[name=wx_mchkey]').val(v.wx_mchkey);
        jQuery('[name=wx_sslcert_path]').val(v.wx_sslcert_path);
        jQuery('[name=wx_sslkey_path]').val(v.wx_sslkey_path);
        jQuery('[name=wx_pay_notifyurl]').val(v.wx_pay_notifyurl);

        if (v.wx_sslcert_path != "") {
            var code = "<a href='"+v.wx_sslcert_path+"' target='_blank'><img src='"+v.cert_img+"'></a>";
            jQuery("#certdiv").html(code);
        }
        if (v.wx_sslkey_path != "") {
            var code = "<a href='"+v.wx_sslkey_path+"' target='_blank'><img src='"+v.certkey_img+"'></a>";
            jQuery("#certkeydiv").html(code);
        }

        ///////////////////////////////////////
        jQuery('#login_callback_resbtn').click(function(){
            jQuery('[name=wx_login_callback]').val(v.default_callback);
            parse_login_callback_domain();
            return false;
        });
        jQuery('#paynresbtn').click(function(){
            jQuery('[name=wx_pay_notifyurl]').val(v.default_paynotify);
            return false;
        });
        jQuery('#landbtn').click(function(){
            jQuery('[name=wx_login_landpage]').val(v.siteurl);
            return false;
        });
        jQuery("#certbtn").click(function(){
            require("common/file_upload").upload(function(res){
                jQuery("#certdiv").html(res.filename);
                jQuery("[name=wx_sslcert_path]").val(res.fileurl);
            });
        });
        jQuery("#certkeybtn").click(function(){
            require("common/file_upload").upload(function(res){
                jQuery("#certkeydiv").html(res.filename);
                jQuery("[name=wx_sslkey_path]").val(res.fileurl);
            });
        });
    }
    function parse_login_callback_domain()
    {
        var login_callback_url = jQuery('[name=wx_login_callback]').val();
        var domain = login_callback_url.replace(/^http[s]?:\/\//i,'');
        var arr = domain.split('/');
        domain = arr[0];
        jQuery("#lcdomain").html(domain);
    }
  </script>
</head>
<body>
  <form method="post" action="admin.php?action=plugins&operation=config&identifier=wxconnect&pmod=z_setting">
  <table class="tb tb2">
    <!-- ----------------------------------------- -->
    <tr><th colspan="3" class="partition">微信应用账号设置</th></tr>
    <tr>
      <td width='90'>微信应用ID：</td>
      <td width='200'>
        <input name="wx_app_id" value="" type="text" class="txt" style='width:400px;'>
      </td>
      <td class='tips2'>微信应用ID（AppID）</td>
    </tr>
    <tr>
      <td>微信应用密钥：</td>
      <td><input name="wx_app_secret" value="" type="text" class="txt" style='width:400px;'></td>
      <td class='tips2'>微信应用密钥（AppSecret）</td>
    </tr>
    <!-- ----------------------------------------- -->
    <tr><th colspan="3" class="partition">微信登录设置</th></tr>
    <tr>
      <td>登录回调地址：</td>
      <td><input name="wx_login_callback" value="" type="text" class="txt" style='width:400px;'></td>
      <td class='tips2'><button id='login_callback_resbtn' class='btn'>设为默认地址</button> &nbsp;微信登录后的回调地址</td>
    </tr>
    <tr>
      <td></td>
      <td colspan='2' class='tips2'>
        请将微信网页授权回调页面域名设为：<b style="color:red;" id='lcdomain'></b>
        <a href="http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html" target="_blank" style="margin-left:10px;">如何设置?</a>
      </td>
    </tr>
    <tr>
      <td>登录后落地页：</td>
      <td><input name="wx_login_landpage" value="" type="text" class="txt" style='width:400px;'></td>
      <td class='tips2'><button id='landbtn' class='btn'>设为站点首页</button> &nbsp;登录成功后的跳转地址</td>
    </tr>
    <!-- ----------------------------------------- --> 
    <tr><th colspan="3" class="partition">微信（公众号）支付设置</th></tr>
    <tr>
      <td colspan='3' class='tips2'>
        注意：请将公众号支付授权目录设为：<b style="color:red;"><%siteurl%></b>
        <a href="https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_3" target="_blank" style="margin-left:10px;">如何设置?</a>
      </td>
    </tr>
    <tr>
      <td>商户ID：</td>
      <td><input name="wx_mchid" value="" type="text" class="txt" style='width:400px;'></td>
      <td class='tips2'>微信商户平台注册的商户ID，<a href='https://pay.weixin.qq.com/' target="_blank">点此前往申请</a></td>
    </tr>
    <tr>
      <td>商户名称：</td>
      <td><input name="wx_mchname" value="" type="text" class="txt" style='width:400px;'></td>
      <td class='tips2'>显示在支付页面上的收款方名称</td>
    </tr>
    <tr>
      <td>商户KEY：</td>
      <td><input name="wx_mchkey" value="" type="text" class="txt" style='width:400px;'></td>
      <td class='tips2'>显示在支付页面上的收款方名称</td>
    </tr>
    <tr>
      <td>商户证书：</td>
      <td>
        <input name="wx_sslcert_path" value="" type="hidden" class="txt" style='width:400px;'>
        <div id='certdiv' style='display:inline-block;vertical-align:middle;margin-right:10px;'></div>
        <a href="javascript:;" id="certbtn">上传证书文件</a>
      </td>
      <td class='tips2'>apiclient_cert.pem文件，可登录<a href='https://pay.weixin.qq.com/' target="_blank">微信商户平台</a>下载</td>
    </tr>
    <tr>
      <td>商户证书Key：</td>
      <td>
        <input name="wx_sslkey_path" value="" type="hidden" class="txt" style='width:400px;'>
        <div id='certkeydiv' style='display:inline-block;vertical-align:middle;margin-right:10px;'></div>
        <a href="javascript:;" id="certkeybtn">上传证书KEY文件</a>
      </td>
      <td class='tips2'>apiclient_key.pem文件，可登录<a href='https://pay.weixin.qq.com/' target="_blank">微信商户平台</a>下载</td>
    </tr>
    <tr>
      <td>支付通知地址：</td>
      <td><input name="wx_pay_notifyurl" value="" type="text" class="txt" style='width:400px;'></td>
      <td class='tips2'><button id='paynresbtn' class='btn'>设为默认地址</button> &nbsp;微信支付成功后，会向该地址发送支付结果。</td>
    </tr>
    <!-- ----------------------------------------- --> 
    <tr>
      <td colspan="3">
        <input type="submit" id='subbtn' class='btn' value="提交"/>
      </td>
    </tr>
  </table>
  </form>
</body>
</html>
