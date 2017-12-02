<?php
require_once 'wxapi.class.php';
class wxconnect_setting
{
    private static $_conf_filename = 'wxconnect.ini';

    private static $_setting = array (
        'wx_app_id' => array('name'=>'微信应用的APPID','value'=>''),
        'wx_app_secret' => array('name'=>'微信应用的APPSECRET','value'=>''),

        'wx_login_callback' => array('name'=>'登录回调地址','value'=>''),
        'wx_login_landpage' => array('name'=>'登录后落地页','value'=>''),

        'wx_mchid' => array('name'=>'微信商户号','value'=>''),
        'wx_mchname' => array('name'=>'微信商户名称（页面显示用）', 'value'=>''),
        'wx_sslcert_path' => array('name'=>'微信商户证书路径','value'=>''),
        'wx_sslkey_path' => array('name'=>'微信商户证书KEY文件路径','value'=>''),
        'wx_mchkey' => array('name'=>'微信商户KEY','value'=>''),
        'wx_pay_notifyurl' => array('name'=>'微信支付通知接口地址','value'=>''),
        'wx_pay_notify_biz' => array('name'=>'微信支付通知业务处理接口（建议不要改动）','value'=>''),
    );

    public static function set($k, $v) 
    {
        if (isset(self::$_setting[$k])) {
            self::$_setting[$k]['value'] = $v;
        }
    }

    // 读取配置
    public static function read()
    {
        //1. 默认配置
        $setting = &self::$_setting;
        $siteurl = wxconnect_env::get_siteurl();
        $setting['wx_login_callback']['value'] = self::get_default_login_callback();
        $setting['wx_login_landpage']['value'] = $siteurl;
        $setting['wx_pay_notifyurl']['value'] = self::get_default_pay_notify();
        $setting['wx_pay_notify_biz']['value'] = $siteurl."/source/plugin/wxconnect/index.php?module=wxpay&action=callback";

        //2. 读取配置文件
        $conffile = self::get_conf_file();
        if (!is_file($conffile)) {
            self::save();
		} else {
			$conf = @parse_ini_file($conffile, true);
            foreach ($setting as $k => &$v) {
                if (isset($conf[$k])) {
                    $v['value'] = $conf[$k];
                }
            }
		}
        //3.返回kv格式
		$res = array();
		foreach ($setting as $k => &$v) {
			$res[$k] = $v['value'];
		}
		return $res;
	}

	// 保存到配置文件
	public static function save()
	{
		ksort(self::$_setting);
		$content = ";wxconnect configure file\n\n";
		foreach (self::$_setting as $k => $v) {
			$name  = $v['name'];
			$value = $v['value'];
			$content.= ";$name\n$k=\"$value\"\n";
		}
		$conffile = self::get_conf_file();
		file_put_contents($conffile, $content);
	}

    private static function get_conf_file()
    {
        return dirname(__FILE__)."/../conf/".self::$_conf_filename;
    }

    // 默认的登录回调地址
    public static function get_default_login_callback()
    {
        return wxconnect_env::get_siteurl().'/source/plugin/wxconnect/wxcallback.php';
    }

    // 默认的支付通知地址
    public static function get_default_pay_notify()
    {
        return wxconnect_env::get_siteurl().'/source/plugin/wxconnect/wxpaynotify.php';
    }
    

/*
    public static function get()
    {
        global $_G;
        $setting = array(
            'wx_app_id'     => '',
            'wx_app_secret' => '',

            'wx_login_basecheck' => 1,
            'wx_login_callback'  => self::get_default_login_callback(),
            'wx_login_landpage'  => wxconnect_env::get_siteurl(),

            'wx_login_url'       => '',
            'wx_login_url_short' => '',
        );
        if (isset($_G['setting']['wxconnect_setting'])){
			$set = unserialize($_G['setting']['wxconnect_setting']);
            self::copy_param($setting, $set, array_keys($setting));
        }
        if ($setting['wx_login_url']=='') {
            self::gen_login_url($setting);
        }
        return $setting;
    }

    public static function get_wx_access_token()
    {
        global $_G;
        $nt = time();
        if (isset($_G['setting']['wxconnect_wxtoken'])){
			$set = unserialize($_G['setting']['wxconnect_wxtoken']);
            if ($nt < $set['expires_in']) return $set['access_token'];
        }
        $setting = self::get();
        $appid = $setting['wx_app_id'];
        $appsecret = $setting['wx_app_secret'];
        $param = wxconnect_wxapi::get_access_token($appid, $appsecret);
        $param['expires_in'] = $nt + 3600; //$param['expires_in'];
        require_once libfile('function/core');
        require_once libfile('function/cache');
		C::t('common_setting')->update_batch(array("wxconnect_wxtoken"=>$param));
		updatecache('setting');
        return $param['access_token'];
    }

    private static function copy_param(array &$to_arr, array &$from_arr, array $keys)
    {
        foreach ($keys as $key) {
			if(isset($from_arr[$key])) $to_arr[$key] = $from_arr[$key];
        }
    }

    public static function gen_login_url(array &$setting)
    {
        $appid = $setting['wx_app_id'];
        $redirect_uri = $setting['wx_login_callback'];
        $scope = $setting['wx_login_basecheck'] ? 'snsapi_userinfo' : 'snsapi_base';
        $loginurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=1#wechat_redirect";
        $setting['wx_login_url'] = $loginurl;
        $setting['wx_login_url_short'] = wxconnect_env::create_short_url($loginurl);
    }
*/
}
?>
