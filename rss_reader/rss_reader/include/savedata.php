<?php 
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	require_once("../../../class2.php");
		
	if(isset($_POST['q']) && isset($_POST['v'])) {
		global $sql;
						
		if($_POST['q']=="i"){ //if it's a new feed
			$sql->db_Insert("rss_reader", "null, ".$_POST['v'].", now(), now() ");

		}elseif($_POST['q']=="u"){//if it's a feed getting editted
			if(isset($_POST['whr'])) {
				$sql->db_Update("rss_reader",stripslashes($_POST['v']).", mod_dt=now() WHERE rss_feed_id=".$_POST['whr']);
			}
			//echo $tmp;
		}elseif($_POST['q']=="d") { //if a feed is getting deleted
			if(isset($_POST['whr'])) {
				$sql->db_Delete("rss_reader", "rss_feed_id='".$_POST['whr']."'");

			}
		}
		
	}
?>
