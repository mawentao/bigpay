<?php
/**
 * 微信支付结果通用通知接口
 * 参考文档：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_7
 **/
try {
    //1. 获取返回数据
	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
	if ($xml=='') {
        throw new Exception('参数格式校验错误');
	}
    $data = WxPayNotify::xml_to_array($xml);    
    //print_r($data);

    //2. 校验签名
    $conffile = dirname(__FILE__)."/conf/wxconnect.ini";
    $conf = parse_ini_file($conffile, true);
    $key = $conf['wx_mchkey'];
    if (!WxPayNotify::checkSign($data, $key)) {
        throw new Exception("签名校验失败");
    } 

    //3. 业务处理（注意：同样的通知可能会多次发送给商户系统。商户系统必须能够正确处理重复的通知。）
    $jumpurl = $conf['wx_pay_notify_biz'];
    $rs = WxPayNotify::callback($jumpurl, $data);

    $res = array (
        'return_code' => 'SUCCESS',
        'return_msg' => 'OK',
    ); 
    WxPayNotify::output($res);
} catch (Exception $e) {
    $res = array (
        'return_code' => 'FAIL',
        'return_msg' => $e->getMessage(),
    );
    WxPayNotify::output($res);
} 

class WxPayNotify
{/*{{{*/
	public static function xml_to_array($xml)
	{
		$obj = simplexml_load_string($xml);
		if ($obj === false) {
			throw new Exception('参数格式校验错误');
		}
		$res = array();
		foreach ($obj->children() as $a => $b) {
			$res[$a] = trim($b);
		}
		return $res;
	}

	// 校验签名
	// 参考文档：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
	public static function checkSign($data, $key)
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

    public static function callback($url, $postData)
    {
        $ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postData);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
    }

    public static function output($res)
	{
		header("Content-type: application/json");
		echo json_encode($res);
		exit(0);
	}
}/*}}}*/


// vim600: sw=4 ts=4 fdm=marker syn=php
?>
