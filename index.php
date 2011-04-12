<?php
require("includes/db.php");
require("includes/twitter/twitter.class.php");
require("includes/twitter/TweetScheduler.php");

$ts = new TweetScheduler;
$charsRemaining = 140;

if ($_POST)
{

    foreach ($_POST as $key => $value)
    {
        $post[$key] = mysql_real_escape_string($value);
    }

    $today = date('m/d/Y');

    $post['send_time'] = strtotime($post['send_time']. " ". $post['send_hour'] . ":" . $post['send_minute'] . " " . $post['meridian']);

    if (empty($post['message']))
    {
        $error = 'You must provide your tweet';
    }
    // Make sure the tweet is for a future date
     else if ($post['send_time'] < $today)
    {
        $error = 'Your appointment must be for a future date';
    } else if (strlen($post['message']) > 140)
    {
        $error = 'Your tweet must be less than 140 characters';
    } else if (($post['direct_message'] == 1) && empty($post['recipient_twitter_name']))
    {
        $error = 'Please provide the recipient\'s twitter name';
    }
    else
    {
        $ts->saveScheduledTweet($post);
    }

    $message = $post['message'];
    $send_time = $post['send_time'];
    $charsRemaining = 140 - strlen($post['message']);
}


$queue = $ts->getQueue();
$sent = $ts->getSent();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="includes/css/style.css" />
        <link rel="stylesheet" type="text/css" href="includes/css/jquery-ui-1.7.2.custom.css" />

        <script type="text/javascript" src="includes/js/jquery-1.3.2.js"></script>
        <script type="text/javascript" src="includes/js/jquery.ui.core.js"></script>
        <script type="text/javascript" src="includes/js/jquery.ui.widget.js"></script>
        <script type="text/javascript" src="includes/js/jquery.ui.tabs.js"></script>
        <script type="text/javascript" src="includes/js/jquery.ui.datepicker.js"></script>

        <script type="text/javascript" src="includes/js/functions.js"></script>

        <title>Tweet Scheduler</title>
    </head>
    <body>
        <div id="container">
            <h1>Tweet Scheduler</h1>
		<p>This page is only a demo.  You can add and remove tweets but they <b>will not</b> be sent.  Ready to purchase this script?  <a href="http://codecanyon.net/item/tweet-scheduler/51911?ref=akozlik">Grab it from Code Canyon!</a></p>
            <?php
            if (isset($error)) { ?>
                <div id="errors">
                    <p><?php echo $error ?></p>
                </div>
            <?php } else if (!isset($error) && $_POST) { ?>
                <div id="success">
                    <p>Your tweet has been scheduled</p>
                </div>
            <?php } ?>

            <form action="index.php" method="post" id="schedule_form">
                <div id="tweet">
                    <p>
                        <label>Type your Tweet</label>
                        <textarea id="message" name="message"><?php if (isset($message)) echo $message ?></textarea><br />
                        <p style="margin-left: 140px; ">You have <span id="charRemaining"><?php echo $charsRemaining ?></span> characters remaining</p>
                    </p>
                </div>

                <p>
                    <label>Send Date</label><input type="text" id="datepicker" name="send_time" value="<?php if (isset($send_time)) echo $send_time; ?>"/>
                </p>

                <p>
                    <label>Send Time</label>
                    <select name="send_hour">
                        <?php for ($i=1; $i<=12; $i++)
                        {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select> :
                    <select name="send_minute" style="margin-left: 5px;">
                        <?php for ($i=00; $i<=60; $i++)
                        {
                            if ($i<10)
                                $i = "0" . $i;
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                    <select name="meridian" style="margin-left: 5px;"><option value="am">am</option><option value="pm">pm</option></select>
                </p>

                <p>
                    <label>Direct Message?</label>
                    <select name="direct_message" >
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </p>

                <p>
                    <label>Client Twitter Name</label>
                    <input type="text" id="recipient_twitter_name" name="recipient_twitter_name" />
                </p>
                <input type="submit" value="Set Reminder" id="setReminder" />
            </form>

            <div id="tabs">
                <ul>
                    <li><a href="#dashboard">dashboard</a></li>
                    <li><a href="#queue">queue</a></li>
                    <li><a href="#sent">sent</a></li>
                </ul>
                <div id="dashboard">
                    <p>This is the dashboard</p>
                    <p>There are <?php echo sizeof($queue) ?> queued tweets</p>
                    <p>You have sent <?php echo sizeof($sent) ?> tweets</p>
                </div>

                <div id="queue">
                    <p>
                        <table width="100%">
                            <th>Message</th>
                            <th>Send Date</th>
                            <th>Direct Message?</th>
                            <th>Recipient</th>
                            <th></th>
                            <?php for ($i=0; $i<sizeof($queue); $i++) { ?>
                            <tr>
                                <td><?php echo $queue[$i]['tweet']?></td>
                                <td><?php echo date('m-d-Y H:i:s', $queue[$i]['send_time'])?></td>
                                <td><?php if ($queue[$i]['direct_message'] == 1) echo "Yes"; else echo "No"?></td>
                                <td><?php echo $queue[$i]['recipient_twitter_name']?></td>
                                <td><a href="scripts/delete_scheduled_tweet.php?id=<?php echo $queue[$i]['schedule_id']; ?>">Delete</a></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </p>
                </div>

                <div id="sent">
                    <p>
                        <table width="100%">
                            <th>Message</th>
                            <th>Send Date</th>
                            <th>Direct Message?</th>
                            <th>Recipient</th>
                            <th></th>
                            <?php for ($i=0; $i<sizeof($sent); $i++) { ?>
                            <tr>
                                <td><?php echo $sent[$i]['tweet']?></td>
                                <td><?php echo date('m-d-Y H:i:s', $sent[$i]['send_time'])?></td>
                                <td><?php if ($sent[$i]['direct_message'] == 1) echo "Yes"; else echo "No"?></td>
                                <td><?php echo $sent[$i]['recipient_twitter_name']?></td>
                                <td><a href="scripts/delete_scheduled_tweet.php?id=<?php echo $sent[$i]['schedule_id']?>">Delete</a></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </p>
                </div>
            </div>
        </div>
    </body>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-8439952-2");
pageTracker._trackPageview();
} catch(err) {}</script>

</html>
