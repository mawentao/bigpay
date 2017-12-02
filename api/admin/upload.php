<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$method = isset($_GET['method']) ? $_GET['method'] : 'img';
$methodlist = array(
    'img', 'upfile'
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

/**
 * 上传图片
 **/
function img()
{
    //1. 检查图片类型
    $imgconfmap = array (
        // 资源图标
		'rscicon' => array('width'=>60, 'height'=>60, 'size'=>1048576),
    );
    $imgtype = wxconnect_validate::getNCParameter("imgtype","imgtype","string");
    if (!isset($imgconfmap[$imgtype])) {
        throw new Exception("unknow imgtype");
    }
    $imgcfg = $imgconfmap[$imgtype];
    //2. 检查图片文件
    $fileid  = wxconnect_validate::getNCParameter("fileElementId","fileElementId","string");
    $upfile  = get_upload_file($fileid);
    check_upload_img($upfile, $imgcfg);
    //3. 存储文件
    $res = array (
        'imgurl' => save_file($upfile),
    );
    return $res;
}

/**
 * 上传文件
 **/
function upfile()
{
    $fileid  = wxconnect_validate::getNCParameter("fileElementId","fileElementId","string");
    $upfile  = get_upload_file($fileid);
    $tmpFile  = $upfile['tmp_name'];
    $fileSize = $upfile['size'];
    $res = array (
        'filesize' => get_file_size_string($fileSize),
        'fileurl'  => save_file($upfile),
    );
    return $res;
}

///////////////////////////////////////////////////////////////////////

function get_upload_file($fileid)
{
    $upfile = $_FILES[$fileid];
    if ($upfile["error"]!==0) {
        $err = $upfile["error"];
        $errMap = array(
            '1' => '文件大小超出服务器空间大小',
            '2' => '文件超出浏览器限制大小',
            '3' => '文件仅部分被上传',
            '4' => '未找到要上传的文件',
            '5' => '服务器临时文件丢失',
            '6' => '文件写入到临时文件出错',
        );  
        $errMsg = isset($errMap[$err]) ? $errMap[$err] : "文件未上传或上传失败";
        throw new Exception($errMsg);
    }   
    return $upfile;
}

function check_upload_img($upfile, $imgcfg)
{
    $tmpFile  = $upfile['tmp_name'];
    $fileSize = $upfile['size'];
    $imginfo = @getimagesize($tmpFile);
    if (false===$imginfo) {
        throw new Exception('请上传图片文件');
    }
    $width  = $imgcfg["width"];
    $height = $imgcfg["height"];
    $size   = $imgcfg["size"];
    if ($width!=$imginfo[0] || $height!=$imginfo[1]) {
        throw new Exception("请上传 ".$width."x".$height." 的图片文件");
    }
    if ($fileSize>$size) {
        throw new Exception("图片大小不得超过"+$size+"B");
    }
}

function save_file($upfile)
{
    global $_G;
    $upload = new discuz_upload();
    if(!$upload->init($upfile, 'common', rand(0, 100000), 'wxconnect_' . md5_file($upfile['tmp_name']))) {
        throw new Exception("文件保存失败");
    }
    if(!$upload->save(1)){
        throw new Exception("文件存储失败");
    }
    $url = $upload->attach['attachment'];
    if(strpos($_G['setting']['attachurl'],'http') === false ){
        $url = $_G['siteurl'] . $_G['setting']['attachurl'] . 'common/' . $url;
    }else{
        $url = $_G['setting']['attachurl'] . 'common/' . $url;
    }
    return $url;
}

function get_file_size_string($size)
{
    if ($size<1024) return $size." B";
    if ($size<1024*1024) {
        $s = floatval($size) / 1024;
        return number_format($s, 2)." KB";
    }
    if ($size<1024*1024*1024) {
        $s = floatval($size) / (1024*1024);
        return number_format($s, 2)." MB";
    }
	$s = floatval($size) / (1024*1024*1024);
	return number_format($s, 2)." GB";
}

?>
