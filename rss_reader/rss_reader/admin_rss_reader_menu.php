<?php
   // Remember that we must include class2.php
   require_once("../../class2.php");
   include_once("include/classes.php");
   require_once(e_ADMIN."auth.php");
   include_lan(e_PLUGIN."rss_reader/languages/rss_reader_".e_LANGUAGE.".php");
   //menu options for the RSS Reader side menu. None atm.
   $text = "";
   
   $ns->tablerender(RSS_READER_LAN_ADMIN_MENU_TITLE, $text);

   require_once(e_ADMIN."footer.php");
?>