<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
require_once dirname(__FILE__).'/libs/env.class.php';

// 保存设置
if (isset($_POST["wx_app_id"])) { 
    wxconnect_setting::read();
    wxconnect_setting::set('wx_app_id', $_POST["wx_app_id"]);
    wxconnect_setting::set('wx_app_secret', $_POST["wx_app_secret"]);
    wxconnect_setting::set('wx_login_callback', $_POST['wx_login_callback']);
    wxconnect_setting::set('wx_login_landpage', $_POST['wx_login_landpage']);
    
    wxconnect_setting::set('wx_mchid', $_POST['wx_mchid']);
    wxconnect_setting::set('wx_mchname', $_POST['wx_mchname']);
    wxconnect_setting::set('wx_mchkey', $_POST['wx_mchkey']);
    wxconnect_setting::set('wx_pay_notifyurl', $_POST['wx_pay_notifyurl']);
    wxconnect_setting::set('wx_sslcert_path', $_POST['wx_sslcert_path']);
    wxconnect_setting::set('wx_sslkey_path', $_POST['wx_sslkey_path']);

    wxconnect_setting::save();

    $landurl = 'action=plugins&operation=config&do='.$pluginid.'&identifier=wxconnect&pmod=z_setting';
	cpmsg('plugins_edit_succeed', $landurl, 'succeed');
}

/////////////////////////////////////////////////////

require_once dirname(__FILE__).'/libs/menu.inc.php';
$siteurl = wxconnect_env::get_siteurl();
$params = wxconnect_setting::read();
$params['siteurl'] = $siteurl;
$params['default_callback']  = wxconnect_setting::get_default_login_callback();
$params['default_paynotify'] = wxconnect_setting::get_default_pay_notify();
$params['adminapi'] = $siteurl.'/plugin.php?id=wxconnect:adminapi&inajax=1&action=';
$params['cert_img'] = wxconnect_env::get_plugin_path()."/template/libs/site/cert.png";
$params['certkey_img'] = wxconnect_env::get_plugin_path()."/template/libs/site/certkey.png";
$tplVars = array(
    'siteurl' => $siteurl,
    'plugin_path' => wxconnect_env::get_plugin_path(),
);
wxconnect_utils::loadtpl(dirname(__FILE__).'/views/z_setting.tpl', $params, $tplVars);
wxconnect_env::getlog()->trace("show admin page [z_setting] success");
