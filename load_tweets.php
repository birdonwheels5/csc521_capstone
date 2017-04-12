<link rel='stylesheet' type="text/css" href="main.css">
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
    
    // This is also the size of the array
    $num_tweets = 8;
    
    // Establish connection to the database
    $con = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_database);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $database_tweets = get_database_tweets($con, $num_tweets);
    
    // List the tweets. The order is taken care of by the database.
    for($i = 0; $i < $num_tweets; $i++)
    {
        $time_unit = "minutes";
        
        // Position [2] is the timestamp
        $create_time = $database_tweets[2][$i];
        
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
        
        // Position [0] is the username, and position [1] is the tweet text
        $username = $database_tweets[0][$i];
        $html_converted_tweet = convert_tweet_to_html($database_tweets[1][$i]);
        
        // Print the rest of the tweet and the time since it was tweeted
        print "<div class='tweet'>";
        print "$username: $html_converted_tweet \n<br/><i> $time_since_tweet $time_unit ago </i>";
        print "</div>";
        print "<br/><br/>";
    }
    
?>
