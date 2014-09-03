CREATE TABLE rss_reader (
  rss_feed_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  rss_feed_nm VARCHAR(45) NOT NULL,
  rss_feed_addr VARCHAR(255) NOT NULL,
  rss_feed_active INT(10) UNSIGNED NOT NULL,
  create_dt DATETIME NOT NULL,
  mod_dt DATETIME NOT NULL,
  PRIMARY KEY (rss_feed_id,rss_feed_nm)
)