<?php
class wxconnect_bksvr
{
    /** 
     * 获取AkSk和AppId
     * param $request: array (
     *        "sitename"    => "",    //!< 站点名称（作为AppName，UTF8编码）
     *        "siteurl"     => "",    //!< 站点地址
     *        "admin_email" => "",    //!< 站点管理员邮箱
     *         
     **/
    public static function getAkSk(array $request)
    {/*{{{*/
        $api = 'http://139.196.29.35:8888/api/site/regist';
        $postData = array (
            "plugin"   => 'wxconnect',
            "sitename" => self::getParam($request, "sitename"),
            "siteurl"  => self::getParam($request, "siteurl"),
            "admin_email" => self::getParam($request, "admin_email"),
        );
        try {
            $res = self::http_request($api, $postData);
            if ($res["retcode"]!=0) {
                throw new Exception($res["retmsg"]);
            }
            return self::result(0, $res);
        } catch (Exception $e) {
            return self::result(100010, $e->getMessage());
        }   
    }/*}}}*/


    //////////////////////////////////////////////////////

    private static function result($code, &$data)
    {/*{{{*/
        if ($code==0) {
            return array (
                "retcode" => 0,
                "retmsg"  => "succ",
                "data" => $data
            );
        } else {
            return array (
                "retcode" => $code,
                "retmsg"  => $data,
            );
        }
    }/*}}}*/

    private static function getParam(array &$arr, $key, $defaultValue="")
    {/*{{{*/
        return isset($arr[$key]) ? trim($arr[$key]) : $defaultValue;
    }/*}}}*/

    /* 网络请求 */
    private static function http_request($url, $postData=null)
    {/*{{{*/
        $data = ""; 
        $urlarr = array($url);
        foreach ($urlarr as $k => $ithurl) {
            $ch = curl_init();
            if ($k!=0 && $domain!="") {
                $header = array ("Host: $domain");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            }   
            if(!is_null($postData)){
                $curlPost = http_build_query($postData);
                curl_setopt($ch, CURLOPT_POST, 1); 
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
            }   
            curl_setopt($ch, CURLOPT_URL, $ithurl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            $data      = curl_exec($ch);
            $errorInfo = curl_error($ch);
            $httpCode  = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            if($httpCode!=200 || !empty($errorInfo)){
                curl_close($ch);
                continue;
            }   
            if(empty($data) && empty($postData)){
                curl_close($ch);
                break;
            }   
            curl_close($ch);
        }   
        if ($data == "") {
            $tmp = file_get_contents($url);
            if(!empty($tmp)){
                $data = $tmp;
            }   
        }   
        $res = json_decode($data,true);
        if (empty($res)) {
            throw new Exception("http_request_fail [res:$data]");
        }
        wxconnect_env::getlog()->trace("url:$url|req:".json_encode($postData)."|res:".$data);
        return $res;
    }/*}}}*/
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
