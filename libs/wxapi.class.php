<?php
/**
 * 微信公众号API封装
 **/
class wxconnect_wxapi
{
    // 获取access_token
    public static function get_access_token($appid, $appsecret)
    {/*{{{*/
        $api = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
		$arr = self::jsonrpc($api);
		if (isset($arr['errmsg'])) {
			throw new Exception($arr['errmsg']);
		}
		return $arr;
    }/*}}}*/

    // 获取openid
    public static function get_openid($appid, $appsecret, $code)
    {/*{{{*/
		$api = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
        $res = self::jsonrpc($api);
        return $res;
/*
        if ($res['access_token']) {
            $api = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$appid.'&grant_type=refresh_token&refresh_token='.$res['refresh_token'];
            $r = self::jsonrpc($api);
            echo json_encode($r).'      ';
            $userinfo = self::get_userinfo($r['openid'], $r['access_token']);
            die(json_encode($userinfo));
        }
die(json_encode($res));
        return $res['openid'];
*/
    }/*}}}*/

    // 获取微信用户基础信息（网页授权的）
    public static function get_userinfo_sns($openid, $token)
    {/*{{{*/
		$api = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$token.'&openid='.$openid.'&lang=zh_CN';
		return self::jsonrpc($api);
    }/*}}}*/

    // 获取微信用户基础信息（关注公众号的用户信息）
    public static function get_userinfo($openid, $token)
    {/*{{{*/
		$api = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$openid.'&lang=zh_CN';
		return self::jsonrpc($api);
    }/*}}}*/

   
    private static function jsonrpc($url, $post=false)
    {/*{{{*/
        $ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($post){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
		}
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
    }/*}}}*/
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
