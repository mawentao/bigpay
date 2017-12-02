<?php
/**
 * api入口
 **/
define("IN_WXCONNECT_API", 1);
define("PLUGIN_PATH", dirname(__FILE__));
define("LIB_PATH", PLUGIN_PATH."/libs");
chdir("../../../");

require_once LIB_PATH."/env.class.php";

$modules = array (
    "wxlogin",
    'wxpay',
);

if(!in_array($_GET['module'], $modules)) {
    module_not_exists();
}
$module  = $_GET['module'];
$version = !empty($_GET['version']) ? intval($_GET['version']) : 1;
while ($version>=1) {
    $apifile = PLUGIN_PATH."/api/$version/$module.php";
    if(file_exists($apifile)) {
        require_once $apifile;
        exit(0);
    }
    --$version;    
}
module_not_exists();

function module_not_exists()
{
	header("Content-type: application/json");
    echo json_encode(array('error' => 'module_not_exists'));
    exit;
}

?>
