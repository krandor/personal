<?php
   // Remember that we must include class2.php
   require_once("../../class2.php");
   include_lan(e_PLUGIN."rss_reader/languages/rss_reader_".e_LANGUAGE.".php");
  	
	function isURL($url) {
   		$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		if (eregi($urlregex, $url)) {
			return true;
		} else {
			return false;
		} 
	}
   
   $html = "";
   // Check current user is an admin, redirect to main site if not
   if (!getperms("P")) {
      header("location:".e_HTTP."index.php");
      exit;
   }
   // Include page header stuff for admin pages
   require_once(e_ADMIN."auth.php");
	//build the RSS Feed Config page
	if(isset($_POST['feed_nm']) && isset($_POST['feed_addr']) && isset($_POST['feed_active'])) {
		if(isURL($_POST['feed_addr'])){
			//global sql var used for interacting w/ the database
			global $sql;
			
			$error = $sql->DB_Insert("rss_reader","null,'".strip_tags($_POST['feed_nm'])."','".$_POST['feed_addr']."','".stripslashes($_POST['feed_active'])."',now(), now()");
			$html .= "<br />";
			$html.="<br /><div id=\"page_updates\"><strong>".LAN_RSS_FEED_ADDED.".</strong></div><br />";					
		} else {
		 $html.="<br /><div id=\"page_updates\"><strong>".LAN_RSS_FEED_URL_INVALID.".</strong></div><br />";
		}
	} else {
		 $html.="<br /><div id=\"page_updates\"></div><br />";
	}
	
	$html.="<script type=\"text/javascript\" src=\"include/admin.js\"></script>\n";
	$html.="<link rel=\"stylesheet\" type=\"text/css\" href=\"include/style.css\" />\n";
	$html.="<div class='cap_border'><div class='main_caption'><div class='bevel'>".LAN_RSS_ADD_NEW_FEED."</div></div></div><br />\n";
	$html.="<span id='sp_new_rss_reader'>";
	$html.="<form id='new_feed' action='".$PHP_SELF."' method='post'>";
	$html.="<table style='width: 95%' class='fborder'>";
	$html.="<tr><td class='forumheader3'>".LAN_RSS_ADD_NEW_FEED_NAME.": </td><td class='forumheader3'><input type='text' name='feed_nm' class='tbox' /></td></tr>";
	$html.="<tr><td class='forumheader3'>".LAN_RSS_ADD_NEW_FEED_URL.": </td><td class='forumheader3'><input type='text' name='feed_addr' class='tbox' /></td></tr>";
	$html.="<tr><td class='forumheader3'>".LAN_RSS_ADD_NEW_FEED_ACTIVE.": </td><td class='forumheader3'><select name='feed_active' class='select'><option value='1' selected='selected'>Yes</option><option value='0'>No</option></select></td></tr>";
	$html.="</table><input type='submit' class='button' name='btnOK' value='Save'></form><br /></span>\n";
   	
	$html .="<div class='cap_border'><div class='main_caption'><div class='bevel'>".LAN_RSS_EXISTING_FEED."</div></div></div><br />\n";
	$html .= "<span id='sp_rss_reader'></span>";
   // Our informative text
   $text = $html;

   // The usual, tell e107 what to include on the page
   $ns->tablerender(LAN_ADMIN_TITLE, $text);

   require_once(e_ADMIN."footer.php");
?>
