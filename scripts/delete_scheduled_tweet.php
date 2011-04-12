<?php
require("../includes/db.php");
require("../includes/twitter/twitter.class.php");
require("../includes/twitter/TweetScheduler.php");

foreach ($_GET as $key => $value)
{
    $get[$key] = mysql_real_escape_string($value);
}

$ts = new TweetScheduler;
$ts->deleteScheduledTweet($get['id']);
header("Location: ../index.php");
exit;
?>
