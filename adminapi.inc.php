<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
/**
 * 后台管理页面调用的api
 **/
require_once dirname(__FILE__) . '/libs/env.class.php';
// 0表示没有权限限制，其他表示有权限的用户组id
$action_rights = array (
    'upload' => array(1),
    'wxmember' => array(1),
    'wxpay' => array(1),
);
$action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";
check_action($action);
require_once(get_action_file($action));

function check_action($action)
{
	global $_G,$action_rights;
	$groupid = $_G['groupid'];
	if (!isset($action_rights[$action])) {
		wxconnect_env::result(array("retcode"=>100010,"retmsg"=>"unkown action"));
	}
	$groupids = $action_rights[$action];
	if (!in_array($groupid,$groupids) && !in_array(0,$groupids)) {
		wxconnect_env::result(array("retcode"=>100020,"retmsg"=>"no rights to do"));
	}
}

function get_action_file($action) 
{
	$path = dirname(__FILE__)."/api/admin/";
	$actionfile = $path.strtolower($action).".php";
	if (!is_file($actionfile)) {
        wxconnect_env::result(array("retcode"=>100030,"retmsg"=>"$actionfile is not exist"));
	}
	return $actionfile;
}

