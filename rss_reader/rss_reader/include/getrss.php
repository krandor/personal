<?php
	require_once("../../../class2.php");
		
//get the q parameter from URL	
$q=$_GET["q"];
if($q!=="") {
	global $sql;
	
	if($q!="menu") {
		//find out which feed was selected
		$sql->DB_Select("rss_reader","rss_feed_addr","rss_feed_id=".$q);
		
		$row=$sql->db_Fetch(MYSQL_NUM);
	
		$xml=$row[0];
		//echo $xml;
		
			$xmlDoc = new DOMDocument();

			//if($xmlDoc->load($xml)) {
			//} else {
				//loading the file directly failed. now try downloading it and opening it that way.
				//print($xml."<br />");
				$contents = file_get_contents($xml);
				//print($contents);
				if($contents!=false) {
					$xmlDoc->loadXML($contents);
					//normalize the document
					$xmlDoc->normalizeDocument();
					//check to see if there were any channels found
					$y=$xmlDoc->getElementsByTagName("channel");
					
					//if there are channel(s) then parse the rest of the feed.
					if($y->length > 0) {			
						parseFeed($xmlDoc);
					} else { 
					    //no channels means no valid feed.
						//$y=$xmlDoc->getElementsByTagName('<rss>');
						echo "<br />Channels Found: ".$y->length."<br />";
						echo "<strong>RSS Error, feed may be broken.</strong>";
					} 										
				} else {
					echo "<strong>RSS Error, feed may be broken or the RSS Reader doesn't support the feed.</strong>";
				}
				
		
	} 
	 
}

function parseFeed($xmlDoc) {

	$channel=$xmlDoc->getElementsByTagName('channel')->item(0);
	//$channel_title = $channel->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
	$channel_title = $channel->getElementsByTagName('title')->item(0)->nodeValue;
	$channel_link = $channel->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
	//$channel_desc = $channel->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue; //was having trouble with google rss, so i took the channel desc out.
	
	//output elements from "<channel>"
	echo("<br /><p><a href='" . $channel_link . "' target='_new'>" . $channel_title . "</a>");
	echo("<br /><br />");
	//echo($channel_desc . "</p>"); //was having trouble with google rss, so i took the channel desc out.
    echo("</p>");
	
	//get and output "<item>" elements 
	
	$x=$xmlDoc->getElementsByTagName('item');
	echo "<div id='num_entries'>Number of Entries: <strong>".($x->length)."</strong></div><br />";
	echo("<ul>");
	for ($i=0; $i<($x->length); $i++)
	{
		 $item_title=$x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
		 //$item_link=$x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
		 $item_link=$x->item($i)->getElementsByTagName('link')->item(0)->nodeValue;
		 $item_desc=$x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
		
		 echo ("<li><p><a href='" . $item_link . "' target='_new'>" . $item_title . "</a>");
		 echo ("<br />");
		 echo (trim($item_desc) . "</p></li>");
	}
	echo("</ul>");
	
}
?>