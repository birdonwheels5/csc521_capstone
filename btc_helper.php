<?php
    
    require_once __DIR__ . '/TwitterOAuth/TwitterOAuth.php';
    require_once __DIR__ . '/TwitterOAuth/Exception/TwitterException.php';
    
    use TwitterOAuth\TwitterOAuth;
    
    // Leave this at 1 for now to avoid having to choose between tweets to add to the database
    // Should probably have this in the config, but since it shouldn't be touched as the program is now, it can stay here.
    $number_of_tweets = 1;
    
    // Test new exchanges here...
    //print get_btc_price("bitstamp");
    
    /* Fetch the current BTC price from specified exchange. 
    * Parameters: 
    *   $exchange (string) - Chooses which exchange to fetch price from. Options are: bitstamp, cryptsy, poloniex, ...
    */
    function get_btc_price($exchange)
    {
        $btc_price = 0; // If the price shows up as 0, then we know there's a problem connecting to the API(s)
        $usd_cny = calculate_cny_exchange_rate();
        
        if($exchange == "bitstamp")
        {
            $url = fopen("http://www.bitstamp.net/api/ticker/", "r");
            
            $json = json_decode(stream_get_contents($url));
            
            $btc_price = $json->{"last"};
        }/*
        // We are not using Cryptsy because of its low BTC/USD volume (<10,000 BTC)
        else if($exchange == "cryptsy")
        {
            // We're using  the server pubapi2 because at the time of programming, pubapi1 seems to be down. When you remove the "2" from pubapi2, either server is randomly selected.
            $url = fopen("http://pubapi2.cryptsy.com/api.php?method=singlemarketdata&marketid=2", "r");
            
            $json = json_decode(stream_get_contents($url));
            
            $btc_price = $json->{"return"}->{"markets"}->{"BTC"}->{"lasttradeprice"};
        }*/
        else if($exchange == "cex.io")
        {
            $url = fopen("https://cex.io/api/ticker/BTC/USD", "r");
            
            $json = json_decode(stream_get_contents($url));
            var_dump($json);
            
            //$btc_price = $json->{"return"}->{"markets"}->{"BTC"}->{"lasttradeprice"};
        }
        else if($exchange == "coinbase")
        {
            $url = fopen("https://coinbase.com/api/v1/prices/spot_rate", "r");
            
            $data = json_decode(stream_get_contents($url));
            
            $btc_price = $data->amount;
        }
        else if($exchange == "btc-e")
        {
            $url = fopen("https://btc-e.com/api/3/ticker/btc_usd", "r");
            
            $json = json_decode(stream_get_contents($url));
            
            $btc_price = $json->{"btc_usd"}->{"last"};
        }
        else if($exchange == "bitfinex")
        {
            $url = fopen("https://api.bitfinex.com/v1/ticker/btcusd", "r");
            
            $json = json_decode(stream_get_contents($url));
            
            $btc_price = $json->mid;
        }
        else if($exchange == "kraken")
        {
            $url = fopen("https://api.kraken.com/0/public/Ticker?pair=XXBTZUSD", "r");
            
            $json = json_decode(stream_get_contents($url));
            
            $btc_price = $json->{"result"}->{"XXBTZUSD"}->{"a"}[0];
        }
        else if($exchange == "btcchina" && $usd_cny > 0)
        {
            $url = fopen("https://data.btcchina.com/data/ticker?market=btccny", "r");
            
            $json = json_decode(stream_get_contents($url));
            
            $btc_price = ($json->{"ticker"}->{"last"} * $usd_cny);
        }
        else if($exchange == "huobi" && $usd_cny > 0)
        {
            $url = fopen("http://api.huobi.com/staticmarket/ticker_btc_json.js", "r");
            
            $json = json_decode(stream_get_contents($url));
            
            $btc_price = ($json->{"ticker"}->{"last"} * $usd_cny);
        }
        else if($exchange == "okcoin") // OKCoin has a BTC/USD market
        {
            $url = fopen("https://www.okcoin.com/api/ticker.do?ok=1", "r");
            
            $json = json_decode(stream_get_contents($url));
            
            $btc_price = ($json->{"ticker"}->{"last"});
        }
        else
        // This will be triggered if there is no method to connect to the given exchange's API
        // Remember to create a new table in the database for any new exchanges. Match the structure of the other exchanges by duplicating another exchange's table (including values).
        {
            $btc_price = -1;
            
            $log_message = "WARNING: No method found to connect to exchange \"" . $exchange . "\"'s API. Please fix this.";
            log_to_file($log_message);
        }
        
        return (float)number_format($btc_price, 2, ".", "");
        
    }
    
    // Calculates USD/CNY based on BTC data obtained from blockchain.info
    // Returns a double with the exchange rate, or -1 if it failed to fetch the rate
    // This is a work around due to the fact that the Chinese exchanges do not have a BTC/USD pair
    function calculate_cny_exchange_rate()
    {
        $usd_btc = 0;
        $cny_btc = 0;
        $usd_cny = 0;
        
        $url = fopen("https://blockchain.info/ticker", "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $usd_btc = $json->{"USD"}->{"last"};
        $cny_btc = $json->{"CNY"}->{"last"};
        
        $usd_cny = ($usd_btc / $cny_btc);
        
        return $usd_cny;
    }
    
    // Returns an array of all settings from the config file
    // Additional config options can be added as functionality expands.
    function load_config()
    {
        $filename = "/var/bitquote/config.txt";
        $mysql_user = "";
        $mysql_host = "";
        $mysql_pass = "";
        $mysql_database = "";
        $twitter_public_key = "";
        $twitter_secret_key = "";
    
        $settings = array();
        
        if(fopen($filename, "r") == false)
        {
            $log_message = "CRITICAL: Unable to load config file! Webpages will not load at all without it.";
            log_to_file($log_message);
        }
        $handle = fopen($filename, "r") or die ("Error loading config file! Please contact a system administrator to get this fixed! Webservices are non-functional without it.");
        while (($line = fgets($handle)) !== false)
        {
            // Fetch config information line-by-line
            if (strcmp(stristr($line, "mysql_user:"), $line) == 0)
            {
                $mysql_user = trim(str_ireplace("mysql_user:", "", $line));
            }
            if (strcmp(stristr($line, "mysql_host:"), $line) == 0)
            {
                $mysql_host = trim(str_ireplace("mysql_host:", "", $line));
            }
            if (strcmp(stristr($line, "mysql_pass:"), $line) == 0)
            {
                $mysql_pass = trim(str_ireplace("mysql_pass:", "", $line));
            }
            if (strcmp(stristr($line, "mysql_database:"), $line) == 0)
            {
                $mysql_database = trim(str_ireplace("mysql_database:", "", $line));
            }
            if (strcmp(stristr($line, "twitter_public_key:"), $line) == 0)
            {
                $twitter_public_key = trim(str_ireplace("twitter_public_key:", "", $line));
            }
            if (strcmp(stristr($line, "twitter_secret_key:"), $line) == 0)
            {
                $twitter_secret_key = trim(str_ireplace("twitter_secret_key:", "", $line));
            }
            
        }
        
        fclose($handle);
        
        
        $settings[0] = $mysql_user;
        $settings[1] = $mysql_host;
        $settings[2] = $mysql_pass;
        $settings[3] = $mysql_database;
        $settings[4] = $twitter_public_key;
        $settings[5] = $twitter_secret_key;
        
        // Check to see if any of the settings are empty. If they are, 
        // that means that there is a typo in one of the settings
        // ie "myr_rpc_uer: " instead of "myr_rpc_user: "
        for($i = 0; $i < count($settings); $i++)
        {
            if(empty($settings[$i]))
            {
                $log_message = "CRITICAL: Unable to load config file due to a damaged setting! Please go through the config file to correct the error. Webpages will not load at all without the config file.";
                log_to_file($log_message);
                
                die ("Error loading config file! Please contact a system administrator to get this fixed! Webservices are non-functional without it.");
            }
        }
        
        return $settings;
    }
    
    // Logs a given message to the log file.
    function log_to_file($log_message)
    {
        $log_filename = "/var/bitquote/log.txt";
        
        // Append the date and time of message to the beginning of the message
        $text = date("Y-m-d H:i:s") . ": " . $log_message . PHP_EOL;
        file_put_contents($log_filename, $text, FILE_APPEND) or print "Error loading logs file! Please contact a system administrator.";
    }
    
    // Cleans a given input to prevent cross-site scripting attacks
    function clean_input($input)
    {
        $input = trim($input);
        $input = htmlspecialchars($input);
        return $input;
    }
    
    // Parameter is of type array, and can contain a maximum of 12 string values, due to the rate limits of the Twitter API
    // $database_connection is the result of the mysqli_connect() function
    // (assuming this method gets called exactly once per minute. Should go with 11 max to be safe).
    function get_tweets($user_list, $database_connection)
    {
        global $number_of_tweets;
        
        // Load database settings from config file
        $settings = array();
        $settings = load_config();
        
        $twitter_public_key = $settings[4];
        $twitter_secret_key = $settings[5];
        
        date_default_timezone_set('UTC');
        
        
        /**
         * Array with the OAuth tokens provided by Twitter when you create application
         *
         * output_format - Optional - Values: text|json|array|object - Default: object
        */
        $config = array(
            'consumer_key'       => $twitter_public_key, // API key
            'consumer_secret'    => $twitter_secret_key, // API secret
            'oauth_token'        => '', // not needed for app only
            'oauth_token_secret' => '',
            'output_format'      => 'object'
        );
        
        /**
         * Instantiate TwitterOAuth class with set tokens
         */
        $connection = new TwitterOAuth($config);
        
        
        // -------------------------------------------------------------
        
        // Get an application-only token
        // more info: https://dev.twitter.com/docs/auth/application-only-auth
        
        $bearer_token = $connection->getBearerToken();
        
        // You can store the bearer-token locally (database, file) and re-use it
        //    $connection->setBearerToken($token);

        // You can revoke / invalidate a bearer-token with
        //    $connection->invalidateBearerToken($token);
        
        // Return multimensional array of with users and tweets
        // Eg: $results[0][0] returns the username of the first user in the array
        // Eg: $results[0][3] returns their third tweet
        $results = array();
        
        for($i = 0; $i < count($user_list); $i++)
        {
            $user_array = array();
            
            $params = array(
                'screen_name' => $user_list[$i],
                'count' => $number_of_tweets,
                'exclude_replies' => true
            );
            
            $response = $connection->get('statuses/user_timeline', $params);
            
            $timestamp = strtotime($response[0]->created_at);
            
            // Timestamp will always be 10 characters long
            $user_array[0] = $timestamp . " <b>" . $user_list[$i] . "</b>";
                        
            for($t = 0; $t < $number_of_tweets; $t++)
            {
                // Store tweets with username who tweeted it in the user array.
                // The $t keeps track of the tweet position in the API's returned array.
                // We will use mysqli_real_escape_string() later to ensure the text is escaped and enterable into the database
                $user_array[$t + 1] = $response[$t]->text;
                $results[$i] = $user_array;
            }
            
        }
        
        return $results;
    }
    
    // $raw_search_data is a multidimensional array, containing usernames and all tweets found by the search.
    // $keyword_list is of type array, containing all keywords to search the supplied tweets for.
    // This method filters tweets based on a specified keyword list.
    function filter_tweets($raw_search_data, $keyword_list)
    {
        global $number_of_tweets;
        
        $processed_tweets = array();
        $keyword_list_length = count($keyword_list);
        $raw_search_data_length = count($raw_search_data);
        $tweet_counter = 0;
        
        // Loop through users
        for($i = 0; $i < $raw_search_data_length; $i++)
        {
            // Loop through tweets within users
            for($t = 1; $t <= $number_of_tweets; $t++)
            {
                // Loop through keywords within tweets
                for($k = 0; $k < $keyword_list_length; $k++)
                {
                    // Search tweet for keywords
                    if(stristr($raw_search_data[$i][$t], $keyword_list[$k]) != false)
                    {
                        //                              username                            tweet
                        $processed_tweets[$tweet_counter] = $raw_search_data[$i][0] . ": " . $raw_search_data[$i][$t];
                        $tweet_counter++;
                        break;
                    }
                }
            }
        }
        
        return $processed_tweets;
    }
    
    // $processed_tweets is of type array. 
    // $database_connection is the result of the mysqli_connect() function
    // This method compares relevant tweets from our search and compares them to the tweets in the database
    // to find the set of all new tweets that are not currently in the database, and discards the others.
    function compare_tweets($processed_tweets, $database_connection)
    {
        
        $database_tweets = get_database_tweets($database_connection);
        var_dump($database_tweets);
        
        //for($i = 0; $i < count($database_tweets) - 1; $i++)
        //{
        //    $database_tweets[$i] = clean_input($database_tweets[$i]);
        //}
        
        // We use the array_values function to reset the index of the resulting array (ie. start at 0)
        $results = array_values(array_diff($processed_tweets, $database_tweets));
        
        var_dump($results);
        
        // Use mysqli_real_escape_string on the tweets after comparison to fix the issue with special characters during comparison.
        for($i = 0; $i < count($results); $i++)
        {
            $results[$i] = mysqli_real_escape_string($database_connection, $results[$i]);
        }
        
        return $results;
    }
    
    // $database_connection is the result of the mysqli_connect() function
    // Returns an array with all the tweets from the database
    function get_database_tweets($database_connection)
    {
        $result = mysqli_query($database_connection, "SELECT * FROM `tweets`");
        
        // Obtain the number of rows from the result of the query
        $num_rows = mysqli_num_rows($result);
        // Obtain number of columns
        $num_columns = mysqli_field_count($database_connection);
        
        // Will be storing all the rows in here
        // Multidimensional array of form rows[table][row]
        $database_tweets = array();
        
        // Get all the rows
        for($i = 0; $i < $num_rows; $i++)
        {
            $raw_database_tweets[$i] = mysqli_fetch_array($result);
        }
        
        // Will be filling this with the tweets in the database to match the format of $processed_tweets for an array compare
        $database_tweets = array();
                
        
        $results = array();
        
        // Fill the $database_tweets array with the tweets in the database
        for($i = 0; $i < $num_columns - 1; $i++)
        {
            
            $database_tweets[$i] = $raw_database_tweets[0]["tweet" . $i];
            
        }
        //var_dump($database_tweets);
        
        return $database_tweets;
    }
    
    // $database_connection is the result of the mysqli_connect() function
    // $database_name (string)
    // Returns boolean
    function add_tweet($new_tweet, $database_connection, $database_name)
    {
        
        $result = mysqli_query($database_connection, "SELECT * FROM `tweets`");
        
        // Obtain the number of rows from the result of the query
        $num_rows = mysqli_num_rows($result);
        // Obtain number of columns
        $num_columns = mysqli_field_count($database_connection);
        
        // Will be storing all the rows in here
        // Multidimensional array of form rows[table][row]
        $rows = array();
        
        // Get all the rows
        for($i = 0; $i < $num_rows; $i++)
        {
            $rows[$i] = mysqli_fetch_array($result);
        }
        
        //var_dump($rows[0][7]);
        
        // Update rows in the database, starting from the bottom, with the tweet above it. Eg tweet0 becomes tweet1, tweet1 becomes tweet2, ect.
        for($i = 0; $i <= ($num_columns - 2); $i++) // the 2 is to offset the extra column, normally would be 1
        {
            if($i == ($num_columns - 2))
            {
                // Insert new tweet into last position in the database.
                $update = "UPDATE `" . $database_name . "`.`tweets` SET `tweet" . $i . "` = '" . $new_tweet . "' WHERE `tweets`.`extra` =0;";
            }
            else
            {
                // Have to escape again because the text becomes unescaped when we take it out of the database, aka our little array
                $update = "UPDATE `" . $database_name . "`.`tweets` SET `tweet" . $i . "` = '" . mysqli_real_escape_string($database_connection, $rows[0]["tweet" . ($i + 1)]) . "' WHERE `tweets`.`extra` =0;";
            }
            
            $result = mysqli_query($database_connection, $update);
        }
        
        return $result;
    }
    
    // Returns btc_price from database
    // $exchange (string): The exchange we want to fetch the price from
    // $database_connection is the result of the mysqli_connect() function
    function get_btc_price_from_database($exchange, $database_connection)
    {
        $result = mysqli_query($database_connection, "SELECT * FROM `" . $exchange . "`");
        
        // num_rows should always be one because we only store one price per exchange
        $num_rows = 1;
        
        // Will be storing all the rows in here
        // Multidimensional array of form rows[table][row]
        $rows = array();
        
        // Get all the rows
        for($i = 0; $i < $num_rows; $i++)
        {
            $rows[$i] = mysqli_fetch_array($result);
        }
        
        return $rows[0][0]; // btc_price
    }
    
    // $database_connection is the result of the mysqli_connect() function
    // $database_name (string)
    // Returns boolean
    function add_buffer_exchange_btc_price($exchange, $database_connection, $database_name)
    {
        
        $btc_price = get_btc_price($exchange);
        if($btc_price > 0)
        {
            // Update old price with the new one.
            $update = "UPDATE `" . $database_name . "`.`" . $exchange . "` SET `btc_price` = '" . $btc_price . "' WHERE `" . $exchange . "`.`extra` =0;";
            
            $result = mysqli_query($database_connection, $update);
        }
        else
        {
            $result = "NOTICE: Failed to get btc_price from exchange " . $exchange . ". Database was not modified.";
            log_to_file($result);
        }
        
        return $result;
    }
    
    // $database_connection is the result of the mysqli_connect() function
    // $database_name (string)
    // Returns boolean
    function add_chart_exchange_btc_price($exchange, $database_connection)
    {
        
        $btc_price = get_btc_price($exchange);
        if($btc_price > 0)
        {
            $insert = "INSERT INTO `" . $exchange . "` (`btc_price`, `timestamp`) VALUES ('" . $btc_price . "', '" . time() . "');";
            
            $result = mysqli_query($database_connection, $insert);
        }
        else
        {
            $result = "NOTICE: Failed to get btc_price from exchange " . $exchange . ". Database was not modified.";
            log_to_file($result);
        }
        
        var_dump($result);
        
        return $result;
    }
    
    // Converts a given tweet's urls to work in html webpages (makes links in the tweet clickable)
    function convert_tweet_to_html($tweet)
    {
        // Searches a given string for all occurrences of a twitter url and stores the url suffixes in an array
        $urls = array(); // Holds the url(s) for later manipulation
        $current_substring = $tweet; // Starts with the full tweet. If there is more than one link, this will act as an index
        $needle = "https://t.co/";
        $needle_count = substr_count($current_substring, $needle);
        if(stristr($current_substring, $needle) == false)
        {
            return $tweet;
        }
        else
        {
            for($i = 0; $i < $needle_count; $i++)
            {
                $current_substring = stristr($current_substring, $needle);
                $urls[$i] = $needle . substr($current_substring, 13, 10); // 13 is the start, starts us right after "t.co/"
                $current_substring = substr($current_substring, 23); // Set the current substring to the string right AFTER the link we just retrieved.
            }
            
            // Now we need to replace the urls in the tweet with the html formatted version of the url
            // To do this, we will fill the $urls array with each modified part of the string, then
            // put the pieces together at the end to reconstruct the tweet
            $tweet_prefix = stristr($tweet, $needle, true); // Piece of tweet before needle
            $tweet_suffix = ""; // Piece of tweet after needle
            $result_tweet = ""; // Final product
            
            for($i = 0; $i < $needle_count; $i++)
            {
                $in_between = "";
                if($i == ($needle_count - 1)) // Last url
                {
                    $tweet_suffix = substr(stristr($tweet, $urls[$i]), 23);
                    $urls[$i] = convert_url_to_html($urls[$i]);
                    break;
                }
                
                $in_between = get_string_between($tweet, $urls[$i], $urls[$i + 1]);
                
                $urls[$i] = convert_url_to_html($urls[$i]) . $in_between;
            }
            
            $result_tweet = $tweet_prefix;
            
            for($i = 0; $i < $needle_count; $i++)
            {
                $result_tweet .= $urls[$i];
            }
            
            $result_tweet .= $tweet_suffix;
            
            //var_dump($result_tweet);
            
            return $result_tweet;
        }
        
    }
    
    // Converts given url to an html verson of the url
    function convert_url_to_html($url)
    {
        $url_prefix = "<a href =\"" . $url . "\" target=\"_blank\">";
        $url_suffix = "</a>";
        
        $url = $url_prefix . $url . $url_suffix;
        
        return $url;
    }
    
    // Code taken from http://www.justin-cook.com/wp/2006/03/31/php-parse-a-string-between-two-strings/
    // Returns the substring that is between two specified strings
    function get_string_between($string, $start, $end)
    {
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);   
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
    }
?>
