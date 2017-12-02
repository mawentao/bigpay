<?php
require_once 'uc.class.php';
require_once 'utils.class.php';
require_once 'bksvr.class.php';
require_once 'log.class.php';
require_once 'validate.class.php';
require_once 'setting.class.php';
class wxconnect_env
{
    private static $_log_obj = null;
    private static $_conf_objs = array();

	// get discuz site's url(discuz root)
    public static function get_siteurl()
    {/*{{{*/
        global $_G;
        $siteurl = rtrim($_G['siteurl'], '/');
		return preg_replace("/\/source\/plugin.*$/i", "", $siteurl);
    }/*}}}*/

	// get sitename(utf-8)
    public static function get_sitename()
    {/*{{{*/
        global $_G;
        $sitename = $_G["setting"]["sitename"];
        $charset = strtolower($_G['charset']);
        if ($charset=='gbk') {
            $sitename = wxconnect_utils::piconv($charset, "UTF-8", $sitename);
        }
        return $sitename;
    }/*}}}*/

	// get bbname(utf-8)
    public static function get_bbname()
    {/*{{{*/
        global $_G;
        $sitename = $_G["setting"]["bbname"];
        $charset = strtolower($_G['charset']);
        if ($charset=='gbk') {
            $sitename = wxconnect_utils::piconv($charset, "UTF-8", $sitename);
        }
        return $sitename;
    }/*}}}*/

    // get admin-email
    public static function get_admin_email()
    {/*{{{*/
        global $_G;
        return $_G["setting"]["adminemail"];
    }/*}}}*/

    // get current plugin path
    public static function get_plugin_path()
    {/*{{{*/
        return self::get_siteurl().'/source/plugin/wxconnect';
    }/*}}}*/

	// get wxlogin logo
	public static function get_wxlogin_logo() 
	{/*{{{*/
		return self::get_plugin_path()."/template/libs/site/wxlogin.jpg";
	}/*}}}*/

    // get pc wxlogin url
	public static function get_wxlogin_url_pc()
	{/*{{{*/
		return self::get_siteurl()."/plugin.php?id=wxconnect&mod=wxlogin";
	}/*}}}*/

    // api output
    public static function result(array $result, $json_header=true)
    {/*{{{*/
        if (!isset($result['retcode'])) {
            $result['retcode'] = 0;
        }
        if (!isset($result['retmsg'])) {
            $result['retmsg'] = 'succ';
        }
        if ($json_header) {
            header("Content-type: application/json");
        }
        echo json_encode($result);
        exit;
    }/*}}}*/

    // get request param
    public static function get_param($key, $dv=null, $field='request')
    {/*{{{*/
        if ($field=='GET') {
            return isset($_GET[$key]) ? $_GET[$key] : $dv;
        }
        else if ($field=='POST') {
            return isset($_POST[$key]) ? $_POST[$key] : $dv;
        }
        else {
            return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $dv;
        }
    }/*}}}*/

    // get conf
    public static function getconf($confname='main')
    {/*{{{*/
        if (!isset(self::$_conf_objs[$confname])) {
            $confpath = dirname(__FILE__)."/../conf";
            $pi = pathinfo($confname);
            if (!isset($pi["extension"]) || strtolower($pi["extension"]) != "ini") {
                $confname .= ".ini";
            }   
            $conffile = $confpath."/".$confname;
            if (is_file($conffile)) {
                self::$_conf_objs[$confname] = parse_ini_file($conffile, true);
            } else {
                self::$_conf_objs[$confname] = array();
            }   
        }   
        return self::$_conf_objs[$confname];
    }/*}}}*/
    
    // get log object
    public static function getlog()
    {/*{{{*/
        if (!self::$_log_obj) {
            $conf = self::getconf();
            $logcfg = $conf['log'];
            self::$_log_obj = new wxconnect_log($logcfg);
        }   
        return self::$_log_obj;
    }/*}}}*/

    // get aksk
    public static function getaksk()
    {/*{{{*/
         if (!self::$_aksk) {
            //1. 读取本地aksk
            $res = wxconnect_utils::getLocalAkSk();
            //2. 本地读取失败，远程读取
            if ($res===false) {
                self::getlog()->warning('get_local_aksk fail');
                $request = array (
                    "sitename"    => self::get_sitename(),
                    "siteurl"     => self::get_siteurl(),
                    "admin_email" => self::get_admin_email(),
                );  
                $rt = wxconnect_bksvr::getAkSk($request);
                if ($rt["retcode"]==0) {
                    $res = array (
                        "ak" => $rt["data"]["ak"],
                        "sk" => $rt["data"]["sk"],
                    );  
                    wxconnect_utils::setLocalAkSk($res);
                    self::getlog()->trace('get_remote_aksk succ');
                } else {
                    $res === false;
                    self::getlog()->warning('get_remote_aksk fail');
                }
            } else {
                self::getlog()->trace('wxconnect', 'get_local_aksk succ');
            }   
            if ($res!==false && isset($res["ak"]) && isset($res["sk"])) {
                self::$_aksk = array (
                    "ak" => $res["ak"],
                    "sk" => $res["sk"],
                );  
            }   
        }   
        return self::$_aksk;
    }/*}}}*/

    // 生成短地址
    public static function create_short_url($url, $retry = 3)
    {/*{{{*/
		if(empty($url)){
			return $url;
		}   
		while($retry > 0) {
			$dwz = "http://dwz.cn/create.php";
			//$dwz = "http://s.youzu.com/gen.php";
			$data=array('url'=>$url);
			$res = self::http_request($dwz , 'POST' ,$data);
			$result =json_decode($res,true);
			$shortUrl = $url;
			if(isset($result['tinyurl'])){
				$shortUrl = $result['tinyurl'];
				break;
			}   
			$retry--;
		}
		return $shortUrl;
	}/*}}}*/

    // 发送http请求
	public static function http_request($url ,$method = 'GET',$params = null)
	{/*{{{*/
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if('POST' == $method){
			curl_setopt($ch, CURLOPT_POST, true);
			if(!empty($params)){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			}
		}else{
			curl_setopt($ch, CURLOPT_HEADER, false);
		}   
		$result = curl_exec($ch);
		curl_close($ch);
		return $result; 
	}/*}}}*/
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
