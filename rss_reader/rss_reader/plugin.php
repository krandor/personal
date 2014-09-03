<?php
/*************
RSS Reader - A plugin for reading RSS feeds from other sites
************/

if (!defined('e107_INIT')) { exit; }
$eplug_name="rss_reader";
$eplug_version="1.10";
$eplug_author="iceqube";
$eplug_folder="rss_reader";
$eplug_description="A plugin to read RSS feeds from your favorite sites and display them on your screen.";
$eplug_compatible="e107v0.7+";
$eplug_url="http://www.sonotsoft.com";
$eplug_email="dan@sonotsoft.com";
$eplug_readme="admin_readme.php";
$eplug_menu_name="admin_menu";
$eplug_conffile="admin_rss_reader_config.php";
$eplug_done="Installation Successful...";
$eplug_upgrade_done="Upgrade Successful...";
$eplug_icon	= $eplug_folder."/images/rss_reader_32.png";
$eplug_icon_small = $eplug_folder."/images/rss_reader_16.png";
$lan_file = e_PLUGIN."rss_reader/languages/rss_reader_".e_LANGUAGE.".php";
include_lan($lan_file);
$eplug_link = TRUE;
$eplug_link_name  = "RSS Feeds";
$eplug_link_url = e_PLUGIN."rss_reader/rss_reader.php";
$eplug_link_perms = "Everyone";

if(!function_exists("rss_reader_uninstall")) {
	function rss_reader_uninstall() {
		global $sql;
		$sql->db_Delete( MPREFIX."rss_reader", "rss_feed_id>0");
	}
}
$eplug_table_names = array("rss_reader");
$eplug_tables = array("CREATE TABLE ".MPREFIX."rss_reader (
	rss_feed_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	rss_feed_nm VARCHAR(45) NOT NULL,
	rss_feed_addr VARCHAR(255) NOT NULL,
	rss_feed_active INT(10) UNSIGNED NOT NULL,
	create_dt DATETIME NOT NULL,
	mod_dt DATETIME NOT NULL,
	PRIMARY KEY (rss_feed_id,rss_feed_nm)
);
");  



?>