<?php

require("../includes/db.php");

$sql = "CREATE TABLE IF NOT EXISTS `scheduled_tweets` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `send_time` int(11) NOT NULL,
  `tweet` varchar(140) NOT NULL,
  `direct_message` int(11) NOT NULL DEFAULT '0',
  `recipient_twitter_name` varchar(15) NOT NULL,
  `sent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`schedule_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;";

mysql_query($sql);
?>
