<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$memuLang = array(
    "browser_tip" => '请使用<a href="http://www.google.cn/chrome/browser/desktop/index.html" target="_blank"><font style="color:red;font-weight:bold">chrome</font></a>或<a href="http://www.firefox.com.cn/" target="_blank"><font style="color:red;font-weight:bold">firefox</font></a>浏览器使用本插件',
);
$charset = strtolower($_G['charset']);
if($charset!='utf-8' && $charset!='utf8'){
    foreach($memuLang as $k => &$v){
        $v = wxconnect_utils::piconv("UTF-8", $charset, $v);
    }   
}
if(isset($lang)){
    $lang = array_merge($lang,$memuLang);
}else{
    $lang = $memuLang;
}
$str = '<li>' . $lang['browser_tip'] . '</li>';
//showtips($str, '', true);
?>
