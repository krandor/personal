<?php
   // Remember that we must include class2.php
   require_once("../../class2.php");
   include_lan(e_PLUGIN."rss_reader/languages/rss_reader_".e_LANGUAGE.".php");
   // Check current user is an admin, redirect to main site if not
   if (!getperms("P")) {
      header("location:".e_HTTP."index.php");
      exit;
   }

   // Include page header stuff for admin pages
   require_once(e_ADMIN."auth.php");

   // Our informative text
   $text = "The RSS Reader allows you to add in feeds from other sites so that users can access news/content/etc in once central location.<br />".
   		   "<strong>What Works:</strong><br />".
   		   "-The RSS Reader currently lets admins add, edit, and delete feeds. <br />".
		   "-The RSS Reader can be installed, upgraded, and uninstalled. <br />".
		   "-Users can view the RSS Reader page and read feeds without needing to refresh the page<br />".
   		   "-Users can view the RSS Reader menu and read the latest entry on each active feed<br />".
  		   "-On installation a Site link is added for the Reader<br />".
		   "<strong>What Needs Work:</strong><br />".
		   "-Option for the reader need to be created... if they're needed, currently i don't see any need for them. <br />".		   
		   "-Database validation is a current issue which is being worked on. <br />".
   		   "-Bugs with different types of RSS Feeds is being worked on. Some formats make the reader break... <br />".
		   "-Issues with the rss_reader_menu.php file are being addressed. Same issue as above... <br />".
		   "<br />".
		   "<strong>Acknowledgements:</strong> <br />".
		   "<a href=\"http://www.w3schools.com/php/php_ajax_rss_reader.asp\" target=\"_new\">W3Schools PHP&AJAX Tutorials</a>--I dun git learned.<br />".
		   "<a href=\"http://www.sonotsoft.com\" target=\"_new\">So!Soft</a>--we're cool, we make software, check us out.<br />".
		   "<a href=\"http://www.google.com\" target=\"_new\">Google</a>--your best friend ;)<br />";

   // The usual, tell e107 what to include on the page
   $ns->tablerender(LAN_READ_ME, $text);

   require_once(e_ADMIN."footer.php");
?>
