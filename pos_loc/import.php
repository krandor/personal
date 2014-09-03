<?php
session_start();
include_once 'include/classes.php';
$html.=startPage();

if(checkLogin() && getLoginType()=="admin") {
	$userType=getLoginType();
	$html.="<div class=\"shaded\">";
	$html.="<a href=\"index.php\">Home</a> || <a href=\"logout.php\">Logout (as ".$userType.")</a> || ".
	"<a href=\"admin.php\">Admin Area</a> || <a href=\"import.php\">Import Tower Mail</a> || <a href=\"report.php\">POS Reporting</a><hr>";
	if($_POST['mail']!="") {
		$mail = $_POST['mail'];
		$html .= stripTowerMail($mail);
	} else {
		$html.=startForm("POST");
		$html .= "Paste Tower Mail Here<br /><textarea cols=100 rows=10 class=\"tbox\" name=\"mail\"></textarea><br />";
		$html.=endForm("Import Mail");
	}
} else {
	$html.="Umm.. something is wrong, you sure you're not trying to be sly?";
}
$html.="</div>";
$html.=endPage();
print($html);

function stripTowerMail($mail) {

	$arr = explode("\n", $mail);	
	$system = "";
	$moon = "";
	$prevMoon = "";
	$type = "";
	$prevTower = "";
	$corp = "";
	$prevCorp = "";
	$alliance = "";
	$prevAlliance ="";
	//print_r($arr);
	$i=0;
	//print(count($arr)."<br />");
	for($i=0;$i < count($arr);$i++) {
		if(preg_match('{^System: +[-a-zA-Z0-9]+}',$arr[$i], $systems)) {
			$system = trim(substr($systems[0], strpos($systems[0], ":")+1));
			//print("System[".$system."]<br />");
		}
		if(preg_match('{^Moon: +[-a-zA-Z0-9]+ [XIV]+ \- Moon [0-9]+}',$arr[$i],$moons)) {
			$moon = trim(substr($moons[0], strpos($moons[0], ":")+1));
			//print("Moon[".$moon."]<br />");
		}
		if(preg_match('{Type: +[\sa-zA-Z]+}',$arr[$i],$towers)) {
			$type = trim(substr($towers[0], strpos($towers[0], ":")+1));
			//print("Tower[".$type."]<br />");
		}
		if(preg_match('{^Corp: +[\s\.a-zA-Z0-9]+}',$arr[$i],$corps)) {
			$corp = trim(substr($corps[0], strpos($corps[0], ":")+1));
			//print("Corp[".$corp."]<br />");
		}
		if(preg_match('{Alliance: +[\sa-zA-Z0-9]+}',$arr[$i],$alliances)) {
			$alliance = trim(substr($alliances[0], strpos($alliances[0], ":")+1));
			//print("Alliance[".$alliance."]<br />");
		}
		if($system!="" && $moon!="" && $alliance!="" && $corp!="" && $type!="") {					
			$html.="System: ".$system." Moon: ".$moon." Corp: ".$corp." Type: ".$type."<br />";
			$crpID=getCorpByName($corp);
			if($crpID=="0") {
				$crpID=getCorpIDByName($corp);
				$allID=getAllianceIDFromCorpID($crpID);
				if($allID==0){
					saveNonAllianceCorp($crpID,$corp);
				}
			}
			$sysID=getSystemByName($system);
			$moonID=getMoonByName($moon);
			$planetID=getPlanetByMoonID($moonID);
			$posTypeID=getPOSByName($type);	
			//print("Corp: ".$corp." ".$crpID."<br />System: ".$sysID."<br />Moon: ".$moonID."<br />Planet: ".$planetID."<br />Type: ".$posTypeID."<br />---------<br />");
			savePOS($crpID,$sysID,$planetID,$moonID,$posTypeID,"");	
			
			$moon="";
			$type="";
			
		}
	}
	$html.="Importing Complete. <a href=\"import.php\">Import another mail.</a>";
	return $html;
}

?>