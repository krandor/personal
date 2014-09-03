<?php
	require_once("../../../class2.php");
	
	if($_GET['w']=='g') {
		global $sql;
		//run queries!
		$sql->DB_Select("rss_reader","rss_feed_id, rss_feed_nm, rss_feed_addr, rss_feed_active","1 ORDER BY rss_feed_id");
		

		$resp = "<table id='tbl_rss_reader' style='width: 95%' class='fborder'>";
		$resp.="<th class='fcaption'>Feed ID</th><th class='fcaption'>Feed Name</th><th class='fcaption'>Feed Address(URL)</th><th class='fcaption'>Active</th>";
		$i=0;
		while($row=$sql->db_Fetch(MYSQL_NUM)) {
			$resp.="<tr id='feed_".$i."'onClick='feedEdit(this.childNodes[0].innerHTML, this.id);'><td id='id_".$i."' class='forumheader3'>".$row[0]."</td><td id='nm_".$i."' class='forumheader3'>".$row[1]."</td><td id='addr_".$i."' class='forumheader3'>".$row[2]."</td><td id='act_".$i."' class='forumheader3'>".$row[3]."</td></tr>";
			$i++;
		}
	
		$resp.="</table>";
	}
	echo $resp;
?>