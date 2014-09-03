<?php
	require_once("../../../class2.php");
	if(isset($_GET['sid'])) {
		global $sql;
		$sql->db_Select("rss_reader", "rss_feed_nm, rss_feed_addr", "rss_feed_active='1' ORDER BY rss_feed_id ");
		//set the loop that will get the addresses for the feeds, then take the op item from each one
		while($row = $sql->db_Fetch(MYSQL_NUM)) {
		
			//open a new XML document and load the feed address
			$xmlDoc = new DOMDocument();
			$xmlDoc->load($row[1]);

			//check to see if there are items for the feed
			$y=$xmlDoc->getElementsByTagName('channel');
			if($y->length > 0) {			
			 	//get the first item from the feed
				$x=$xmlDoc->getElementsByTagName('item');
				$item_title=$x->item(0)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
				$item_link=$x->item(0)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
				$text .= "{$row[0]}: <a href='" . $item_link . "'>" . $item_title . "</a><br /><br />";
			}
		}		
	}
	echo $text;
?>