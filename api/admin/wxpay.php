<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$method = isset($_GET['method']) ? $_GET['method'] : '';
$methodlist = array(
    'queryorder','querylog',
    'saveorder',
    'delorder',
);

try {
    if (!in_array($method, $methodlist)) {
        throw new Exception('unknow method');
    }
	$res = $method();
    wxconnect_env::result($res, false);
} catch (Exception $e) {
    wxconnect_env::result(array('retcode'=>100010,'retmsg'=>$e->getMessage()));
}

/* 支付订单列表 */
function queryorder()
{
    return C::t("#wxconnect#wxconnect_pay_order")->query();
}

/* 支付记录列表 */
function querylog()
{
    return C::t("#wxconnect#wxconnect_pay_log")->query();
}

/* 保存支付订单 */
function saveorder()
{
    C::t("#wxconnect#wxconnect_pay_order")->save();
    return array();
}

/* 删除订单 */
function delorder()
{
    C::t("#wxconnect#wxconnect_pay_order")->del();
    return array();
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
