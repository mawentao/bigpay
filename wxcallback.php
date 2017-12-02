<?php
if (!isset($_GET['rui'])) {
	echo json_encode($_GET);
	echo '<hr>';
	echo json_encode($_POST);
	echo '<hr>';
	echo 'hello';
}

$rui = $_GET["rui"];
unset($_GET['rui']);
$sp = (strpos($rui, "?")===false) ? "?" : "&";
$rui .= $sp.toUrlParams($_GET);
Header("Location: $rui");
exit();

function toUrlParams($urlObj)
{   
	$buf = ""; 
	foreach ($urlObj as $k => $v) {
		$buff .= $k . "=" . $v . "&";
	}   
	return trim($buff, "&");
}
?>
