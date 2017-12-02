<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once dirname(__FILE__).'/libs/env.class.php';

/*
//$openid = 'ozsECwacscQ9oAMhSAoo4j_frZKg';
$openid = 'ozsECwR7rG5SiIYHFw4Pj5u841Jw';
$access_token = wxconnect_setting::get_wx_access_token();
$userinfo = wxconnect_wxapi::get_userinfo($openid, $access_token);
die(json_encode($userinfo));
$nickname = $userinfo['nickname'];
/*
$uid = wxconnect_uc::regist($nickname);

$nickname = wxconnect_uc::clear($userinfo['nickname']);


echo 'uid: '.$uid.' | access_token: '.$access_token.' | nickname: '.$nickname;

echo json_encode($userinfo);
*/

//$uid = wxconnect_uc::regist('aas');

//echo "uid: $uid <hr>";


$plugin = "wxconnect";
$plugin_enabled = 0;
if(isset($_G['setting']['plugins']['available']) && in_array($plugin, $_G['setting']['plugins']['available'])){
    $plugin_enabled = 1;
}
if(isset($_GET['log']) && $_GET['log']){
	header("Content-type:text/plain;charset=utf-8");
	$dateStr = date('Ym');
	if(isset($_POST['date'])){
		$dateStr = $_POST['date'];
	}
	$file = rtrim(DISCUZ_ROOT, '/') . '/data/log/' . $dateStr . "_$plugin.php";
	if(is_readable($file)){
		$tmp = @file($file);
		$cnt = count($tmp);
		$lines = array();
		for($i = 0; $i < $cnt; $i++){
			$line = trim($tmp[$i]);
			if(!empty($line)){
				$lines[] = $tmp[$i];
			}
		}
		$cnt = count($lines);
		$i = 0;
		$total = 1024;
		if(isset($_GET['count']) && $_GET['count']){
			$total = intval($_GET['count']);
		}
		if($cnt >= $total){
			$i = $cnt - $total;
		}
		for(;$i < $cnt; $i++){
			echo $lines[$i];
		}
	}else{
		echo 'such log file does not exists or not readable [ log file: ' . '${DISCUZ_ROOT}/data/log/' . $dateStr . "_$plugin.php" . ' ]';
	}
	die(0);
}
$result = array (
    'env' => array (
        "charset"         => $_G['charset'],
        "discuz_version"  => $_G['setting']['version'],
        "php_version"     => phpversion(),
        'server_name'     => php_uname(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'],
    ),  
    'site' => array (
        'siteurl'     => wxconnect_env::get_siteurl(),
        'sitename'    => wxconnect_env::get_sitename(),
        'admin_email' => wxconnect_env::get_admin_email(),
    ), 
    'wxconnect' => array(
        'plugin_version' => $_G['setting']['plugins']['version']["wxconnect"],
        'plugin_enabled' => $plugin_enabled,
    ),
);
wxconnect_env::result($result);
?>
