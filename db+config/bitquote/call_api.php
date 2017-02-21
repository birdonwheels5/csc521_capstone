<?php
    
    include "../public_html/func/btc.php";
    
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
    $raw_tweets = get_tweets($user_list, $con);
    // Debug
    var_dump($raw_tweets);
    $processed_tweets = array();
    $processed_tweets = filter_tweets($raw_tweets, $keyword_list);
    // Debug
    var_dump($processed_tweets);
    $unique_processed_tweets = array();
    $unique_processed_tweets = compare_tweets($processed_tweets, $con);
    // Debug
    //var_dump($unique_processed_tweets);
    
    add_buffer_exchange_btc_price("bitfinex", $con, $mysql_database);
    add_buffer_exchange_btc_price("bitstamp", $con, $mysql_database);
    //add_buffer_exchange_btc_price("cryptsy", $con, $mysql_database);
    add_buffer_exchange_btc_price("coinbase", $con, $mysql_database);
    add_buffer_exchange_btc_price("kraken", $con, $mysql_database);
    add_buffer_exchange_btc_price("okcoin", $con, $mysql_database);
    add_buffer_exchange_btc_price("btcchina", $con, $mysql_database);
    add_buffer_exchange_btc_price("huobi", $con, $mysql_database);
    add_buffer_exchange_btc_price("btc-e", $con, $mysql_database);
    
    // Debugging code
    /*$unique_processed_tweets[0] = "Cats are really nice and stuff";
    $unique_processed_tweets[1] = mysqli_real_escape_string($database_connection, "Hello world I can't program because");
    $unique_processed_tweets[2] = "I am writing this debugging code";
    $unique_processed_tweets[3] = "To test my really bad speghetti code";*/
    
    //var_dump($unique_processed_tweets);
    
    //add_tweet($unique_processed_tweets[1], $con);
    
    // add new tweets to database
    for($i = 0; $i < count($unique_processed_tweets); $i++)
    {
        add_tweet($unique_processed_tweets[$i], $con, $mysql_database);
    }
    
    mysqli_close($con);

?>
