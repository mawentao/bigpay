<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once dirname(__FILE__)."/libs/env.class.php";

try {
	//1. 获取订单信息
    $orderid = isset($_GET['orderid']) ? $_GET['orderid'] : 0;
	$pay_order = C::t("#wxconnect#wxconnect_pay_order")->getByOrderId($orderid);
	if (empty($pay_order)) {
        throw new Exception("该笔支付订单已删除或已过期！");
	}
	$body = wxconnect_utils::to_utf8($pay_order['order_body']);
	$fee  = intval($pay_order['total_fee']);

	//2. 构造订单
    $openid = 'ozsECwacscQ9oAMhSAoo4j_frZKg'; 
    //$openid = C::m("#wxconnect#wxconnect_wxapi")->getOpenId();

    $outerid = isset($_GET['outerid']) ? $_GET['outerid'] : C::m("#wxconnect#wxconnect_wxpay")->genOutTradeNo();
    $payid = C::t("#wxconnect#wxconnect_pay_log")->create($orderid, $openid, $outerid, $fee);
	$rui  = isset($_GET['rui']) ? urldecode($_GET['rui']) : "";
    if ($rui!="") {
        $sp = strpos($rui,'?')===false ? "?" : "&";
        $rui.=$sp."payid=".$payid;
    }
    $data = array (
        'body' => $body,
        'attach' => $payid,
        'out_trade_no' => $outerid,
        'total_fee' => $fee,
        'time_start' => date("YmdHis"),
        'time_expire' => date("YmdHis", time() + 600),
        'goods_tag' => 'goods_tag',
        'trade_type' => 'JSAPI',
        'openid' => $openid,
    );
    $jsApiParameters = C::m("#wxconnect#wxconnect_wxpay")->getJsApiParameters($data);

    //3. 页面其他一些参数
	$plugin_path = wxconnect_env::get_plugin_path();
    $ajaxapi = $plugin_path."/index.php?module=";
    $feestr = "￥".number_format($fee/100,2);
    $mchname = C::m("#wxconnect#wxconnect_wxpay")->getMchName();

	include template("wxconnect:wxpay");
} catch (Exception $e) {
    $msg = wxconnect_utils::to_site_charset($e->getMessage());
    showmessage($msg,"");
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
