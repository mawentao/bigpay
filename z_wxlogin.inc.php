<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
require_once dirname(__FILE__).'/libs/env.class.php';
require_once dirname(__FILE__).'/libs/menu.inc.php';
$siteurl = wxconnect_env::get_siteurl();
$params = array(
    'siteurl'  => $siteurl,
    'loginurl' => $siteurl.'/plugin.php?id=wxconnect:wxlogin',
    'adminapi' => $siteurl.'/plugin.php?id=wxconnect:adminapi&inajax=1&action=',
);
$tplVars = array(
    'plugin_path' => wxconnect_env::get_plugin_path(),
);
wxconnect_utils::loadtpl(dirname(__FILE__).'/views/z_wxlogin.tpl', $params, $tplVars);
wxconnect_env::getlog()->trace("show admin page [z_wxlogin] success");
