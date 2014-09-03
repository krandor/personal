<?php
   include_lan(e_PLUGIN."rss_reader/languages/rss_reader_".e_LANGUAGE.".php");
   $menutitle  = "RSS Reader Options";

   $butname[]  = LAN_MENU_TITLE;
   $butlink[]  = "admin_rss_reader_config.php";
   $butid[]    = "config";
   
   //$butname[]  = "RSS Menu";
   //$butlink[]  = "admin_rss_reader_menu.php";
   //$butid[]    = "menu";

   $butname[]  = LAN_READ_ME;
   $butlink[]  = "admin_readme.php";
   $butid[]    = "readme";

   global $pageid;
   for ($i=0; $i<count($butname); $i++) {
      $var[$butid[$i]]['text'] = $butname[$i];
      $var[$butid[$i]]['link'] = $butlink[$i];
   };

   show_admin_menu($menutitle, $pageid, $var);
?>
