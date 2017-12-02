<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$method = isset($_GET['method']) ? $_GET['method'] : 'query';
$methodlist = array(
    'query'
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

/* 微信用户列表 */
function query()
{
    return C::t("#wxconnect#wxconnect_member")->query();
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
