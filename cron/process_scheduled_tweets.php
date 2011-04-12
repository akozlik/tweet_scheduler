<?php
require("../includes/db.php");
require("../includes/twitter/twitter.class.php");
require("../includes/twitter/TweetScheduler.php");

$ts = new TweetScheduler;

$ts->processScheduledTweets();

$status = $ts->rateLimitStatus();

print_r($status);


?>
