<?php

class TweetScheduler extends twitter
{
    // Remove a scheduled tweet from the database
    public function deleteScheduledTweet($schedule_id)
    {
        $sql = "delete from scheduled_tweets where schedule_id = $schedule_id";
        $result = mysql_query($sql);
    }

    // Returns all outstanding scheduled tweets in an array
    public function getQueue()
    {
        $queue = array();
        $sql = "select * from scheduled_tweets where sent = 0";
        $result = mysql_query($sql) or die ( mysql_error() );
        while($row = mysql_fetch_assoc($result))
        {
            array_push($queue, $row);
        }
        return $queue;
    }

    // Returns all sent tweets in an array
    public function getSent()
    {
        $sent = array();
        $sql = "select * from scheduled_tweets where sent = 1";
        $result = mysql_query($sql);
        while($row = mysql_fetch_assoc($result))
        {
            array_push($sent, $row);
        }
        return $sent;
    }

    // Grabs all the outstanding tweets who's sent time has past, and sends them to twitter
    public function processScheduledTweets()
    {
        $now = time()+60*60*24*10;
        $sql = "select * from scheduled_tweets where send_time < $now and sent = 0";
        $result = mysql_query($sql);

        if (mysql_num_rows($result) > 0)
        {
            while ($row = mysql_fetch_assoc($result))
            {
                echo $row['tweet'];
                if ($row['direct_message'] == 1)
                {
                    $stat = $this->sendDirectMessage($row['recipient_twitter_name'], $row['tweet']);
                } else
                {
                    $stat = $this->update($row['tweet']);
                }

                echo "<pre>";
                print_r($stat);
                echo "</pre>";

                $sql = "update scheduled_tweets set sent = 1 where schedule_id = " . $row['schedule_id'];
                $update_result = mysql_query($sql);
            }
        }
    }

    // Saves a scheduled tweet to the database
    public function saveScheduledTweet($data)
    {
        
        $message = $data['message'];
        $send_time = $data['send_time'];
        $direct_message = $data['direct_message'];

        $recipient_twitter_name = $data['recipient_twitter_name'];
                
        $sql = "insert into scheduled_tweets (tweet, send_time, direct_message, recipient_twitter_name)
                values ('$message', $send_time, $direct_message, '$recipient_twitter_name')";

        $result = mysql_query($sql) or die ( mysql_error() );
    }
}
?>
