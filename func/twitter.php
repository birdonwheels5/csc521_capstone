<?php

require_once "general.php";

require_once __DIR__ . '/TwitterOAuth/TwitterOAuth.php';
require_once __DIR__ . '/TwitterOAuth/Exception/TwitterException.php';

use TwitterOAuth\TwitterOAuth;

// Leave this at 1 for now to avoid having to choose between tweets to add to the database
// Should probably have this in the config, but since it shouldn't be touched as the program is now, it can stay here.
$number_of_tweets = 1;

// Parameter is of type array, and can contain a maximum of 12 string values, due to the rate limits of the Twitter API
// $database_connection is the result of the mysqli_connect() function
// (assuming this method gets called exactly once per minute. Should go with 11 max to be safe).
function get_tweets($user_list)
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
    
    $keyword_list_length = count($keyword_list);
    $raw_search_data_length = count($raw_search_data);
    $tweet_counter = 0;
    
    $results = array();
    
    // Loop through users
    for($i = 0; $i < $raw_search_data_length; $i++)
    {
        $user_array = array();
        
        // Loop through tweets within users
        for($t = 1; $t <= $number_of_tweets; $t++)
        {
            // Loop through keywords within tweets
            for($k = 0; $k < $keyword_list_length; $k++)
            {
                // Search tweet for keywords
                if(stristr($raw_search_data[$i][$t], $keyword_list[$k]) != false)
                {
                    // Store the usernames and tweets similar to how we did in the get_tweets function
                    $user_array[0] = $raw_search_data[$i][0]; //username
                    $user_array[$t] = $raw_search_data[$i][$t]; // tweet
                    $results[$i] = $user_array;
                    
                    $tweet_counter++;
                    break;
                }
            }
        }
        
        // Reset tweet_counter to 0 since we are beginning a new user
    }
    
    // reset all indexes
    $results = array_values($results);
    for($i = 0; $i < count($results); $i++)
    {
        $results[$i] = array_values($results[$i]);
    }
    
    return $results;
}

// $processed_tweets is a multimensional array containing usernames and all tweets found by the search.
// Eg: $results[0][0] returns the username of the first user in the array
// Eg: $results[0][3] returns their third tweet 
// $database_connection is the result of the mysqli_connect() function
// This method compares relevant tweets from our search and compares them to the tweets in the database
// to find the set of all new tweets that are not currently in the database, and discards the others.
function compare_tweets($processed_tweets, $database_connection)
{
    global $number_of_tweets;
    
    $number_of_users = count($processed_tweets);
    
    $results = array();
    
    for($i = 0; $i < $number_of_users; $i++)
    {
        $user_array = array();
        
        for($t = 1; $t < (count($processed_tweets[$i])); $t++) // $t is for tweets
        {
            $post_text = $processed_tweets[$i][$t];
            $username = $processed_tweets[$i][0];
            $timestamp = substr($processed_tweets[$i][0], 0, 10); // First 10 chars are the timestamp
            
            $result = mysqli_query($database_connection, "SELECT post_text FROM Twitter_Posts WHERE (post_text='$post_text' AND tstamp=$timestamp)");
            
            if(mysqli_fetch_array($result) == null)
            {
                $user_array[0] = $processed_tweets[$i][0]; //username
                $user_array[$t] = $processed_tweets[$i][$t]; // tweet
                $results[$i] = $user_array;
            }
        }
    }
    
    // reset all indexes
    $results = array_values($results);
    for($i = 0; $i < count($results); $i++)
    {
        $results[$i] = array_values($results[$i]);
    }
    
    return $results;
}

// $database_connection is the result of the mysqli_connect() function
// Returns an array with all the tweets from the database
function get_database_tweets($database_connection, $tweet_limit)
{
    /*$result = mysqli_query($database_connection, "SELECT * FROM Twitter_Posts");
    
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
    
    return $database_tweets;*/
    
    $result = mysqli_query($database_connection, "SELECT username, post_text, tstamp FROM Twitter_Posts ORDER BY tstamp DESC LIMIT $tweet_limit");
    
    // Obtain the number of rows from the result of the query
    $num_rows = mysqli_num_rows($result);
            
    // Will be storing all the rows in here
    $array_of_rows = array();
                            
    // Get all the rows
    for($i = 0; $i < $num_rows; $i++)
    {
        $array_of_rows[$i] = mysqli_fetch_array($result);
    }
    $size_of_array_of_rows = $num_rows;
                            
    $usernames = array();
    $post_texts = array();
    $timestamps = array();
    
    // Get an array of all values for each field
    for($i = 0; $i < $size_of_array_of_rows; $i++)
    {
        $usernames[$i] = $array_of_rows[$i]["username"];
        $post_texts[$i] = $array_of_rows[$i]["post_text"];
        $timestamps[$i] = $array_of_rows[$i]["tstamp"];
    }
    
    // Package all user data and return as an array
    $database_tweets = array();
    
    $database_tweets[0] = $usernames;
    $database_tweets[1] = $post_texts;
    $database_tweets[2] = $timestamps;
    
    return $database_tweets;
}

// $database_connection is the result of the mysqli_connect() function
// $database_name (string)
// Returns boolean
function add_tweet($username, $new_tweet, $tweet_timestamp, $database_connection)
{
    $insert = "INSERT INTO Twitter_Posts (username, post_text, tstamp) VALUES ('$username', '$new_tweet', $tweet_timestamp);";
    
    $result = mysqli_query($database_connection, $insert);
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

?>
