<?php
    
    // When opened, this file will fetch all tweets from the database and print them out to the webpage.
    // The output is HTML formatted.
    
    include "func/twitter.php";
    
    // Load database settings from config file
    $settings = array();
    $settings = load_config();
    
    $mysql_user = $settings[0];
    $mysql_host = $settings[1];
    $mysql_pass = $settings[2];
    $mysql_database = $settings[3];
    
    // Establish connection to the database
    $con = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_database);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $database_tweets = get_database_tweets($con);
    $database_tweets_count = count($database_tweets) - 1;
    
    // List the tweets in reverse order, so they appear from approximately latest first
    for($i = $database_tweets_count; $i >= 0; $i--)
    {
        $time_unit = "minutes";
        
        // This grabs the timestamp from the beginning of the tweet
        $create_time = substr($database_tweets[$i], 0, 10);
        // Get time since tweeted in minutes
        $time_since_tweet = round((time() - $create_time) / 60, 0);
        
        if($time_since_tweet == 1)
        {
            $time_unit = "minute";
        }
        
        // Change unit from minutes to hours
        if($time_since_tweet >= 60 and $time_since_tweet < 1440)
        {
            $time_unit = "hours";
            $time_since_tweet = round($time_since_tweet / 60);
            
            if($time_since_tweet == 1)
            {
                $time_unit = "hour";
            }
        }
        // Change unit from minutes to days
        else if($time_since_tweet >= 1440 and $time_since_tweet < 43805)
        {
            $time_unit = "days";
            $time_since_tweet = round($time_since_tweet / 1440);
            
            if($time_since_tweet == 1)
            {
                $time_unit = "day";
            }
        }
        // Change unit from minutes to months
        else if($time_since_tweet >= 43805)
        {
            $time_unit = "months";
            $time_since_tweet = round($time_since_tweet / 43805);
            
            if($time_since_tweet == 1)
            {
                $time_unit = "month";
            }
        }
        
        
        // Print the rest of the tweet and the time since it was tweeted
        print convert_tweet_to_html(substr($database_tweets[$i], 11) . " - <i>") . $time_since_tweet . " " . $time_unit . " ago </i>";
        print "<br/><br/>";
    }
    
?>
