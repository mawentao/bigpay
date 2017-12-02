<?php
if (!defined('IN_WXCONNECT_API')) {
    exit('Access Denied');
}

require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();

////////////////////////////////////
// action的用户组列表（空表示全部用户组）
$actionlist = array(
    'callback' => array(),
);
////////////////////////////////////
$uid = $_G['uid'];
$username = $_G['username'];
$groupid = $_G["groupid"];
$action = isset($_GET['action']) ? $_GET['action'] : "query";

try {
    if (!isset($actionlist[$action])) {
        throw new Exception('unknow action');
    }
    $groups = $actionlist[$action];
    if (!empty($groups) && !in_array($groupid, $groups)) {
        throw new Exception('illegal request');
    }
    $res = $action();
    wxconnect_env::result($res);
} catch (Exception $e) {
    wxconnect_env::result(array('retcode'=>100010,'retmsg'=>$e->getMessage()));
}

function callback()
{
    $log = "GET:".json_encode($_GET)."|POST:".json_encode($_POST);
    wxconnect_env::getlog()->notice($log);

    $data = $_POST;
    $key = "c8954e982fb72170949e2cfa31af2e50";
    if (!checkWxPaySign($data, $key)) {
        throw new Exception("签名校验错误");
    }

    $payid = $data['attach']; //3
    $status = (strtoupper($data['result_code'])=='SUCCESS') ? 1 : 2;
    $detail = json_encode($data);
    C::t("#wxconnect#wxconnect_pay_log")->update_result($payid, $status,$detail);

    $ret = array();
    return $ret;
}

// 校验签名
// 参考文档：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
function checkWxPaySign($data, $key)
{
	ksort($data);
	$str = '';
	foreach ($data as $k => $v) {
		if ($k=='sign' || $v=='') continue;
		$str .= "$k=$v&";
	}
	$str = trim($str, '&');
	$stringSignTemp = $str."&key=".$key;
	$sign = strtoupper(md5($stringSignTemp));
	return $sign == $data['sign'];
}
?>
