<?php
session_start();
include_once 'include/classes.php';
	
	$html.=startPage();
	$userType=getLoginType();
	
	if(checkLogin()) {
		$html.="<div class=\"shaded\">";
		$html.="<a href=\"index.php\">Home</a> || <a href=\"logout.php\">Logout (as ".$userType.")</a>";
		if($userType=="admin") {
			$html.=" || <a href=\"admin.php\">Admin Area</a> || <a href=\"import.php\">Import Tower Mail</a> || <a href=\"report.php\">POS Reporting</a>";
		}
		$html.="<hr>";
		
		if($_POST['id']!="") {
			$sid=$_POST['sid'];
			$id=$_POST['id'];//tower id
			$qu=$_POST['q'];
			if($qu!="") {
				if(is_numeric($qu)) {
					updatePOSDetails($id,$sid,$qu);
				} else {
					$html.=buildError("Quantity can only be a numerical number(ie: 3).");
				}
			} else {
				updatePOSDetails($id,$sid,"0");
			}
		}	
		if($_GET['id']!="") {	
			$corpName=getCorpByTowerID($_GET['id']);
			$allianceName=getAllianceByTowerID($_GET['id']);
			$moonName=getMoonNameByTowerID($_GET['id']);
			$towerType=getTowerByTowerID($_GET['id']);
			
			$html.="<div class=\"tableHeader\">";	
			$html.=$moonName." Tower Details (<a href=\"report.php?sid=".getSystemByMoonName($moonName)."&submit=System+Report\">View other towers for this system</a>)<br />";
			$html.="Owning Alliance: <i>".$allianceName."</i> (<a href=\"report.php?aid=".getAllianceByName($allianceName)."&submit=Alliance+Report\">View other towers for this alliance</a>)<br />";
			$html.="Owning Corp: <i>".$corpName."</i> (<a href=\"report.php?cid=".getCorpByName($corpName)."&submit=Corp+Report\">View other towers for this corp</a>)<br />";
			$html.="Tower Type: <i>".$towerType."</i><br />";
			$html.="</div>";
			$html.=getTowerDetails($_GET['id']);
			$html.="<hr>";
			$html.=startForm("POST");
			$html.="Add a Structure to this Tower: ".buildSelect(buildTowerStructureList(),"sid");
			$html.="Quantity: ".buildTextbox("q","1");
			$html.=buildHiddenInput("id",$_GET['id']);
			$html.=endForm("UPDATE");
			$html.="To remove a structure, update a quantity to 0 or ''.";
		}
		$html.="</div>";
	}
	
	$html.=endPage();
	print($html);
?>