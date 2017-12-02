<?php
class wxconnect_utils
{
    public static function to_utf8($str) {
        return self::piconv(CHARSET, "UTF-8", $str);
    }

    public static function to_site_charset($str_utf8)
    {   
        return self::piconv("UTF-8",CHARSET,$str_utf8);
    }

    public static function piconv($from_charset, $to_charset, $str)
    {
		if(function_exists('iconv')){
			$str = @iconv($from_charset, $to_charset.'//ignore', $str);
		}else{
			$str = @mb_convert_encoding($str, $to_charset, $from_charset);
		}
		return $str;
    }

    public static function loadtpl($tpl, $vars ,$tplVars=null)
    {
        $json = json_encode($vars);
        $js_script = '<script type="text/javascript"> v = eval(\'(' . $json . ")');</script>\n";
        $content = @file_get_contents($tpl);
        if (false === $content) {
            return false;
        }
        $charset = strtolower(CHARSET);
        if (is_string($content) && $charset!='utf-8' && $charset!='utf8') {
            $content = self::piconv('UTF-8', CHARSET, $content);
        }
		$tplVars['js_script'] = $js_script;
		$tplVars['app_charset'] = CHARSET;
		if (is_array($tplVars)) {
		    foreach($tplVars as $key => $value){
                $content = str_replace("<%".$key."%>",$value,$content);
                $content = str_replace("<% ".$key." %>",$value,$content);
            }
        }
		echo $content;
    }

    private static $_aksk_setting_key = "wxconnect_aksk";

    // 读取本地aksk&appid
    public static function getLocalAkSk()
    {   
        global $_G;
        require_once libfile('function/core');
        require_once libfile('function/cache');
        $key = self::$_aksk_setting_key;  
        if(isset($_G['setting'][$key]) && !is_array($_G['setting'][$key])){
            $_G['setting'][$key] = unserialize($_G['setting'][$key]);
        }   
        $aksk = $_G['setting'][$key];
        if(isset($aksk['ak']) && $aksk['ak']!="" &&
           isset($aksk['sk']) && $aksk['sk']!=""
        ){  
            return $aksk;
        }   
        return false;
    }   

    // 设置本地aksk&appid
    public static function setLocalAkSk(array $data)
    {   
        global $_G;
        require_once libfile('function/core');
        require_once libfile('function/cache');
        $key = self::$_aksk_setting_key;
        C::t('common_setting')->update_batch(array($key=>$data));
        updatecache('setting');
    }

    // 整型id加密
    public static function encodeId($intid) 
    {   
        if (!is_int($intid) && !is_numeric($intid)) {
            return $intid;
        }   
        $id = ($intid & 0x0000ff00) << 16; 
        $id += (($intid & 0xff000000) >> 8) & 0x00ff0000;
        $id += ($intid & 0x000000ff) << 8;
        $id += ($intid & 0x00ff0000) >> 16; 
        $id ^= 457854;
        return $id;
    }   

    // 整型id解密
    public static function decodeId($intid) 
    {   
        if (!is_int($intid) && !is_numeric($intid)) {
            return $intid;
        }   
        $intid ^= 457854;
        $id = ($intid & 0x00ff0000) << 8;
        $id += ($intid & 0x000000ff) << 16; 
        $id += (($intid & 0xff000000) >> 16) & 0x0000ff00;
        $id += ($intid & 0x0000ff00) >> 8;
        return $id;
    }

}
?>
