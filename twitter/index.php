<?php
//Set the user and password
//$twitter_user = "krandor";
//$twitter_pw = "neophyte";
$twitter_user = "sonotsoft";
$twitter_pw = "S0!S3cur3";
// Define credentials for the Twitter account
define('TWITTER_CREDENTIALS', $twitter_user.':'.$twitter_pw);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $twitter_user; ?>'s Friends</title>
<style type="text/css">
<!--
body {
	font: 100% Verdana, Arial, Helvetica, sans-serif;
	background: #666666;
	margin: 0; /* it's good practice to zero the margin and padding of the body element to account for differing browser defaults */
	padding: 0;
	text-align: center; /* this centers the container in IE 5* browsers. The text is then set to the left aligned default in the #container selector */
	color: #000000;
}

/* Tips for Elastic layouts 
1. Since the elastic layouts overall sizing is based on the user's default fonts size, they are more unpredictable. Used correctly, they are also more accessible for those that need larger fonts size since the line length remains proportionate.
2. Sizing of divs in this layout are based on the 100% font size in the body element. If you decrease the text size overall by using a font-size: 80% on the body element or the #container, remember that the entire layout will downsize proportionately. You may want to increase the widths of the various divs to compensate for this.
3. If font sizing is changed in differing amounts on each div instead of on the overall design (ie: #sidebar1 is given a 70% font size and #mainContent is given an 85% font size), this will proportionately change each of the divs overall size. You may want to adjust based on your final font sizing.
*/
.oneColElsCtrHdr #container {
	width: 46em;  /* this width will create a container that will fit in an 800px browser window if text is left at browser default font sizes */
	background: #FFFFFF;
	margin: 0 auto; /* the auto margins (in conjunction with a width) center the page */
	border: 1px solid #000000;
	text-align: left; /* this overrides the text-align: center on the body element. */
}
.oneColElsCtrHdr #header { 
	background: #DDDDDD; 
	padding: 0 10px 0 20px;  /* this padding matches the left alignment of the elements in the divs that appear beneath it. If an image is used in the #header instead of text, you may want to remove the padding. */
} 
.oneColElsCtrHdr #header h1 {
	margin: 0; /* zeroing the margin of the last element in the #header div will avoid margin collapse - an unexplainable space between divs. If the div has a border around it, this is not necessary as that also avoids the margin collapse */
	padding: 10px 0; /* using padding instead of margin will allow you to keep the element away from the edges of the div */
}
.oneColElsCtrHdr #mainContent {
	padding: 0 20px; /* remember that padding is the space inside the div box and margin is the space outside the div box */
	background: #FFFFFF;
}
.oneColElsCtrHdr #footer { 
	padding: 0 10px; /* this padding matches the left alignment of the elements in the divs that appear above it. */
	background:#DDDDDD;
} 
.oneColElsCtrHdr #footer p {
	margin: 0; /* zeroing the margins of the first element in the footer will avoid the possibility of margin collapse - a space between divs */
	padding: 10px 0; /* padding on this element will create space, just as the the margin would have, without the margin collapse issue */
}
-->
</style>
<script src="SpryAssets/SpryCollapsiblePanel.js" type="text/javascript"></script>
<link href="SpryAssets/SpryCollapsiblePanel.css" rel="stylesheet" type="text/css" />
</head>

<body class="oneColElsCtrHdr">

<div id="container">
  <div id="header">
    <h1><?php echo $twitter_user; ?>'s Friends Tweets</h1>
  <!-- end #header --></div>
  <div id="mainContent">
<?php		
		
		// Set up CURL with the Twitter URL and some options
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://twitter.com/statuses/friends.xml');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


		// Twitter uses HTTP authentication, so tell CURL to send our Twitter account details
		curl_setopt($ch, CURLOPT_USERPWD, TWITTER_CREDENTIALS);


		// Execute the curl process and then close
		$data = curl_exec($ch);
		curl_close($ch);

		// If nothing went wrong, we should now have some XML data 
		//echo '<pre>'.htmlentities(print_r($data, true)).'</pre>';
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($data);
		parseFriends($xmlDoc);
		//parseFeed($xmlDoc);
		//echo $data;
	function parseFriends($xmlDoc) {
		$x = $xmlDoc->getElementsByTagName('user');
		//echo $x->length;
		for($i=0; $i<($x->length);$i++) {
		
			$fid = $x->item($i)->getElementsByTagName('id')->item(0)->nodeValue;
			$fname = $x->item($i)->getElementsByTagName('screen_name')->item(0)->nodeValue;
			$fdesc = $x->item($i)->getElementsByTagName('description')->item(0)->nodeValue;
			$fpropic = $x->item($i)->getElementsByTagName('profile_image_url')->item(0)->nodeValue;
			
			print("<div id='friend_".$i."'><img src='".$fpropic."' border=0 />".$fname." - ".$fdesc."\n");	
			parseFriendFeed($fid,$i);
			print("<script type=\"text/javascript\">\n".
			"<!--\n".
			"var CollapsiblePanel".$i." = new Spry.Widget.CollapsiblePanel(\"CollapsiblePanel".$i."\",{contentIsOpen:false});\n".
			"//-->\n".
			"</script>");
			print("</div><br />\n");
		}
	}
	
	function parseFriendFeed($fid,$i) {
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://twitter.com/statuses/user_timeline/'.$fid.'.rss?count=5');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// Twitter uses HTTP authentication, so tell CURL to send our Twitter account details
		curl_setopt($ch, CURLOPT_USERPWD, TWITTER_CREDENTIALS);
		// Execute the curl process and then close
		$data = curl_exec($ch);
		curl_close($ch);
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($data);
		print("<div id='CollapsiblePanel".$i."' class='CollapsiblePanel'>");
		print("<div class='CollapsiblePanelTab' tabindex='".$i."'>Tweets</div>");
	    print("<div class='CollapsiblePanelContent'>");
  		parseFeed($xmlDoc);
		print("</div></div>\n");
				//echo '<pre>'.htmlentities(print_r($data, true)).'</pre>';
	}
	
	function parseFeed($xmlDoc) {

		$channel=$xmlDoc->getElementsByTagName('channel')->item(0);
		//$channel_title = $channel->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
		$channel_title = $channel->getElementsByTagName('title')->item(0)->nodeValue;
		$channel_link = $channel->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
		
		//get and output "<item>" elements 
		
		$x=$xmlDoc->getElementsByTagName('item');
		//echo "<div id='num_entries'>Number of Entries: <strong>".($x->length)."</strong></div><br />";
		print("<ul>");
		for ($i=0; $i<($x->length); $i++)
		{
			 $item_title=$x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
			 //$item_link=$x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
			 $item_link=$x->item($i)->getElementsByTagName('link')->item(0)->nodeValue;
			 //$item_desc=$x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
			
			 print("<li><p><a href='" . $item_link . "' target='_blank'>" . $item_title . "</a>");
			 print("<br />");
			 //echo (trim($item_desc) . "</p></li>");
			 print("</p></li>");
		}
		print("</ul>");
	
	}
?>
  </div>
  <div id="footer">
    <p></p>
<!-- end #footer --></div>
<!-- end #container --></div>

</body>
</html>
