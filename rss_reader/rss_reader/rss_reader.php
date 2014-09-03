<?php
	//include all the e107 required stuffs
	require_once("../../class2.php");
	//display the header
	require_once(HEADERF);
	
	include_lan(e_PLUGIN."rss_reader/languages/rss_reader_".e_LANGUAGE.".php");

	$text = "\n<script type=\"text/javascript\" src=\"include/getrss.js\"></script>\n".
	"<form>\n".
	LAN_RSS_SELECT_FEED.": <select class='select' name='rss_list' id='rss_list' onchange=\"showRSS(this.value);\">\n";
	$text .="<option selected='selected'>".LAN_RSS_SELECT_DEFAULT."</option>\n";

	global $sql;
	$sql->DB_Select("rss_reader","rss_feed_id, rss_feed_nm","rss_feed_active=1");
	
	while($row=$sql->db_Fetch(MYSQL_NUM)) {
		$text.="<option value=\"".$row[0]."\">".$row[1]."</option>\n";
	}

	$text.="</select>\n".
	"</form>\n".
	"<div id='status'></div>\n".
	"<p><div id=\"rssOutput\">\n".
	"<b>".LAN_RSSDIV.".</b></div></p>\n";

	$ns->tablerender(LAN_TITLE, $text);

	//display the footer
	require_once(FOOTERF);

?>
