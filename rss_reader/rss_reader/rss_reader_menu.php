<?php
//check to see if this is being viewed as part of a e107 site
if (!defined('e107_INIT')) { exit; }
	include_lan(e_PLUGIN."rss_reader/languages/rss_reader_".e_LANGUAGE.".php");
	//include the javascript
	$text = "\n<script type=\"text/javascript\" src=\"".e_PLUGIN."rss_reader/include/getrssmenu.js\"></script>\n";
	//tell the window.loaded funtion to fire and pass in the path for the plugin directory
	$text .= "\n<script type=\"text/javascript\">window.load=loaded('".e_PLUGIN."');</script>\n";
	//set the div that will be filled with the feeds
	$text .= "<div id=\"rssOutputMenu\">".LAN_RSS_LOADING_DATA."...";
	$text .= "</div>";
	//render it all
	$ns->tablerender(LAN_LATEST_RSS, $text);

?>
