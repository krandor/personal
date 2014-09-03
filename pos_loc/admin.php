<?php
	session_start();
	include_once 'include/classes.php';
	$html.=startPage();

	if(checkLogin() && getLoginType()=="admin") {
		$userType=getLoginType();
		$alliances = buildAllianceList();
		$html.="<div class=\"shaded\">";
		$html.="<a href=\"index.php\">Home</a> || <a href=\"logout.php\">Logout (as ".$userType.")</a> || ".
		"<a href=\"admin.php\">Admin Area</a> || <a href=\"import.php\">Import Tower Mail</a> || <a href=\"report.php\">POS Reporting</a>";
		$html.="<hr>Choose a table to update.";
		$html.=startForm();
		$html.=startTable(true);
		$html.="<tr><td>".buildRadioBtn("update","Alliances","0",true)."</td></tr>";
		$html.="<tr><td>".buildRadioBtn("update","Alliance Member Corps","1")."<br />";
		$html.="Choose an Alliance To Update<br />".buildSelect($alliances,"aid")."</td></tr>";
		$html.=endTable();
		$html.=endForm();
		$html.="</div>";
		
		if($_GET['update']=="0") {
			$html .= updateAllianceList();
		} elseif($_GET['update']=="1" && $_GET['aid']!="") {
			$html.=updateAllianceCorp($_GET['aid']);
		}
	} else {
		$html.="You are <u>NOT</u> an Admin, I curse you with herpes.<br /><a href=\"index.php\">Home</a>";
	}
	
	$html.=endPage();
	echo $html;
?>