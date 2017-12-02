<?php
if (!defined('IN_WXCONNECT_API')) {
    exit('Access Denied');
}

require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();

////////////////////////////////////
// action的用户组列表（空表示全部用户组）
$actionlist = array(
    'query' => array(1),
);
////////////////////////////////////
$uid = $_G['uid'];
$username = $_G['username'];
$groupid = $_G["groupid"];
$action = isset($_GET['action']) ? $_GET['action'] : "query";

try {
    if (!isset($actionlist[$action])) {
        throw new Exception('unknow action');
    }
    $groups = $actionlist[$action];
    if (!empty($groups) && !in_array($groupid, $groups)) {
        throw new Exception('illegal request');
    }
    $res = $action();
    wxconnect_env::result($res);
} catch (Exception $e) {
    wxconnect_env::result(array('retcode'=>100010,'retmsg'=>$e->getMessage()));
}

function query()
{
    /*
    $key   = isset($_REQUEST["key"]) ? real_escape_string($_REQUEST["key"]) : ""; 
    $sort  = isset($_REQUEST["sort"]) ? $_REQUEST["sort"] : "ctime";
    $dir   = isset($_REQUEST["dir"]) ? $_REQUEST["dir"] : "DESC";
    $start = isset($_REQUEST["start"]) ? $_REQUEST["start"] : 0;
    $limit = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : 0;
    $where = '1';
    if ($key!='') {
        if (strlen($key)==32) {
            $where.= " AND app_key='$key'";
        }   
        else {
            $where.= " AND (site_name like '%$key%' OR site_url like '%$key%')";
        }   
    }   
    $sql = "SELECT SQL_CALC_FOUND_ROWS * ".
           "FROM site_info ".
           "WHERE $where ".
           "ORDER BY `$sort` $dir";
    if ($limit>0) $sql.= " LIMIT $start,$limit";
    $res1 = wxconnect_env::getpdo()->queryAll($sql);
    $res2 = wxconnect_env::getpdo()->queryAll("SELECT FOUND_ROWS() AS total");
    $ret =array(
        "root" => $res1,
        "totalProperty" => $res2[0]['total'],
    );
    */
    $ret = array (
        "totalProperty" => 0,
        "root" => array(),
    );
    return $ret;
}
?>
