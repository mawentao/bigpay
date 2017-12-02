<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once dirname(__FILE__).'/libs/env.class.php';

class plugin_wxconnect
{
    public function common()
    {
    }

	public function global_login_extra() { 
		global $_G;
		if (!$_G['uid']) {
			$url = wxconnect_env::get_wxlogin_url_pc();
			$code = '<div class="fastlg_fm y" style="margin-right: 10px; padding-right: 10px">'.
			          '<a href="'.$url.'"><img src="'.wxconnect_env::get_wxlogin_logo().'" class="vm"></a>'.
                    '</div>';
			return $code;
		}
	}

	public function global_usernav_extra1() {

	}
}

class plugin_wxconnect_member extends plugin_wxconnect {
    function logging_method() {
		global $_G;
		$url = wxconnect_env::get_wxlogin_url_pc();
		$code = '<a href="'.$url.'"><img src="'.wxconnect_env::get_wxlogin_logo().'" class="vm"></a>';
		return $code;
	}
}

