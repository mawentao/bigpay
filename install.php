<?php
/*******************************************************
 * 此脚本文件用于插件的安装
 * 提示：可使用runquery() 函数执行SQL语句
 *       表名可以直接写“cdb_”
 * 注意：需在导出的 XML 文件结尾加上此脚本的文件名
 *******************************************************/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
// install db
/* 微信登录 */
$sql = "CREATE TABLE IF NOT EXISTS `".DB::table('wxconnect_member')."` ". <<<EOF
(
 `uid` mediumint(8) unsigned NOT NULL,
 `openid` char(32) NOT NULL DEFAULT '',
 `nickname` char(32) NOT NULL DEFAULT '', 
 `userinfo` text NOT NULL DEFAULT '',
 `addtime` int unsigned NOT NULL DEFAULT '0',
 `uptime` int unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`uid`),
 UNIQUE KEY `openid` (`openid`)
) ENGINE=MyISAM
EOF;
runquery($sql);

/* 登录二维码记录表 */
$sql = "CREATE TABLE IF NOT EXISTS `".DB::table('wxconnect_login_qrcode')."` ". <<<EOF
(
 `qrid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `uid` mediumint(8) unsigned NOT NULL DEFAULT 0,
 `openid` char(32) NOT NULL DEFAULT '',
 `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 `isdel` tinyint(1) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`qrid`)
) ENGINE=MyISAM
EOF;
runquery($sql);


/* 微信支付订单 */
$sql = "CREATE TABLE IF NOT EXISTS `".DB::table('wxconnect_pay_order')."` ". <<<EOF
(
 `order_id` int unsigned NOT NULL AUTO_INCREMENT,
 `order_body` varchar(128) NOT NULL DEFAULT '',
 `total_fee` int unsigned NOT NULL DEFAULT 1,
 `addtime` int unsigned NOT NULL DEFAULT '0',
 `addby` varchar(64) NOT NULL DEFAULT '', 
 PRIMARY KEY (`order_id`),
 UNIQUE KEY `uk_body_fee` (`order_body`,`total_fee`)
) ENGINE=MyISAM
EOF;
runquery($sql);
$addtime = time();
$sql = "INSERT IGNORE INTO ".DB::table('wxconnect_pay_order')." VALUES (1,'test',1,$addtime,'admin')";
runquery($sql);

/* 微信支付记录 */
$sql = "CREATE TABLE IF NOT EXISTS `".DB::table('wxconnect_pay_log')."` ". <<<EOF
(
 `pay_id` bigint unsigned NOT NULL AUTO_INCREMENT,
 `order_id` int unsigned NOT NULL DEFAULT 0,
 `outer_id` varchar(128) NOT NULL DEFAULT '',
 `pay_fee` int unsigned NOT NULL DEFAULT 0,
 `openid` varchar(128) NOT NULL DEFAULT '',
 `paytime` int unsigned NOT NULL DEFAULT '0',
 `detail` text NOT NULL DEFAULT '',
 `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0:unpay,1:payed,2:payfail',
 PRIMARY KEY (`pay_id`),
 KEY `idx_oid_status` (`order_id`,`status`),
 KEY `idx_openid_status` (`openid`,`status`)
) ENGINE=MyISAM
EOF;
runquery($sql);


$finish = TRUE;
?>
