<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
require_once dirname(__FILE__)."/../libs/env.class.php";
require_once dirname(__FILE__)."/../libs/wxlib/WxPay.JsApiPay.php";
class model_wxconnect_wxpay
{
    private $_notify_url = '';
    private $_mchid = '';
    private $_mchname = '';

    public function __construct()
    {
        $wxconf = wxconnect_env::getconf('wxconnect');
        WxPayConfig::$APPID        = $wxconf['wx_app_id'];
        WxPayConfig::$APPSECRET    = $wxconf['wx_app_secret'];
        WxPayConfig::$MCHID        = $wxconf['wx_mchid'];
        WxPayConfig::$KEY          = $wxconf['wx_mchkey'];
        WxPayConfig::$SSLCERT_PATH = $wxconf['wx_sslcert_path'];
        WxPayConfig::$SSLKEY_PATH  = $wxconf['wx_sslkey_path'];

        $this->_notify_url = $wxconf['wx_pay_notifyurl'];
        $this->_mchid = $wxconf['wx_mchid'];
        $this->_mchname = $wxconf['wx_mchname'];
    }

    // 获取商户名称
    public function getMchName()
    {
        return $this->_mchname;
    }

    // 生成外部订单ID
    public function genOutTradeNo()
    {
        return $this->_mchid.date("YmdHis");
    }

    // 创建微信JSAPI支付的参数
    public function getJsApiParameters($data)
    {
        $notify_url = isset($data['notify_url']) ? $data['notify_url'] : $this->_notify_url;

		$input = new WxPayUnifiedOrder();
		$input->SetBody($data['body']);
		$input->SetAttach($data['attach']);
		$input->SetOut_trade_no($data['out_trade_no']);
		$input->SetTotal_fee($data['total_fee']);
		$input->SetTime_start($data['time_start']);
		$input->SetTime_expire($data['time_expire']);
		$input->SetGoods_tag($data['goods_tag']);
		$input->SetNotify_url($notify_url);
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($data['openid']);
		$order = WxPayApi::unifiedOrder($input);
		/*
		   echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>'; 
		   foreach($order as $key=>$value){
		   echo "<font color='#00ff55;'>$key</font> : $value <br/>";
		   }
		*/

		$tools = new JsApiPay();
		$jsApiParameters = $tools->GetJsApiParameters($order);
		//$editAddress = $tools->GetEditAddressParameters();

        return $jsApiParameters;
    }
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
