<?php
    
    include "/home/student/S0280512/public_html/func/btc.php";
    include "/home/student/S0280512/public_html/func/twitter.php";
    include "/home/student/S0280512/public_html/scraper.php";
    
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
        $log_message = "CRITICAL: Failed to connect to database while attempting to update the database tables! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    // Recommended maximum of 8 users, however it can go as high as 10 without any modification to the database.
    // Do not add users who barely tweet, because their same old tweet will cycle through the feed indefinitely.
    $user_list = array();
    $user_list[0] = "bitcoin";
    $user_list[1] = "actualcryptos";
    $user_list[2] = "cryptocoinsnews";
    $user_list[3] = "btcforum";
    $user_list[4] = "coindesk";
    
    // For some reason this one breaks the twitter feed (causes duplicate entries of other tweets, namely the second tweet in the feed)
    //$user_list[5] = "coindesk";
    // FIXED as of v1.01
    
    $keyword_list = array();
    $keyword_list[0] = "btc";
    $keyword_list[1] = "bitcoin";
    $keyword_list[2] = "blockchain";
    $keyword_list[3] = "currency";
    $keyword_list[4] = "digital";
    
    $raw_tweets = array();
    $raw_tweets = get_tweets($user_list);
    // Debug
    print "\n Raw Tweets \n";
    var_dump($raw_tweets);
    $processed_tweets = array();
    $processed_tweets = filter_tweets($raw_tweets, $keyword_list);
    // Debug
    print "\n Processed Tweets \n";
    var_dump($processed_tweets);
    $unique_processed_tweets = array();
    $unique_processed_tweets = compare_tweets($processed_tweets, $con);
    // Debug
    print "\n Unique Processed Tweets \n";
    var_dump($unique_processed_tweets);
    print "\n";
    
    add_buffer_exchange_btc_price("bitfinex", $con);
    add_buffer_exchange_btc_price("bitstamp", $con);
    //add_buffer_exchange_btc_price("cryptsy", $con);
    add_buffer_exchange_btc_price("coinbase", $con);
    add_buffer_exchange_btc_price("kraken", $con);
    add_buffer_exchange_btc_price("okcoin", $con);
    add_buffer_exchange_btc_price("btcchina", $con);
    add_buffer_exchange_btc_price("huobi", $con);
    add_buffer_exchange_btc_price("btc-e", $con);
    
    // add new tweets to database
    for($i = 0; $i < count($unique_processed_tweets); $i++)
    {
        // Get the tweet timestamp from the username
        $tweet_timestamp = substr($unique_processed_tweets[$i][0], 0, 10);
        
        // Get rid of the timestamp from the username
        $unique_processed_tweets[$i][0] = substr($unique_processed_tweets[$i][0], 11);
        
        for($t = 1; $t < (count($unique_processed_tweets[$i])); $t++) // $t is for tweets
        {
            $escaped_username = mysqli_real_escape_string($con, $unique_processed_tweets[$i][0]);
            $escaped_tweet = mysqli_real_escape_string($con, $unique_processed_tweets[$i][$t]);
            
            var_dump($escaped_username);
            var_dump($escaped_tweet);
            
            var_dump(add_tweet($escaped_username, $escaped_tweet, $tweet_timestamp, $con));
            
            //print "\n" . $escaped_username . " " . $escaped_tweet . " " . $tweet_timestamp . " " . $con . "\n";
        }
    }
    

    //add forum posts to database
    $threads = array();
    $threads = scrape_bitcointalk();
    
    $unique_threads = compare_threads($threads, $con);
    
    $number_of_unique_threads = count($unique_threads['url']);
    
    // Debug
    print "\n Unique Bitcointalk Threads \n";
    var_dump($unique_threads);
    print "\n";

    for ($f = 0; $f < $number_of_unique_threads; $f++)
    {
        $unique_threads['url'][$f] = mysqli_real_escape_string($con, $unique_threads['url'][$f]);
        $unique_threads['title'][$f] = mysqli_real_escape_string($con, $unique_threads['title'][$f]);
        $unique_threads['timestamp'][$f] = mysqli_real_escape_string($con, $unique_threads['timestamp'][$f]);
        $unique_threads['names'][$f] = mysqli_real_escape_string($con, $unique_threads['names'][$f]);
        
        // Compare function doesn't work
        add_post($unique_threads['url'][$f], $unique_threads['title'][$f], $unique_threads['timestamp'][$f], $unique_threads['names'][$f], 'Bitcointalk', $con);
    }

    mysqli_close($con);

?>
