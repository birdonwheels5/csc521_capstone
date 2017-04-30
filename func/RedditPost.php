<?php

class RedditPost		
{		
    private $tstamp = "";
    private $post_url = "";
    private $OP = "";
    private $post_text = "";
    private $subreddit = "";
    private $post_number = 0;
    
    function __construct($subreddit, $post_number, $is_fetching_new_posts)
    {
        $this->post_number = $post_number;
        $this->subreddit = $subreddit;
        if($is_fetching_new_posts)
        {
            $this->get_json_post();
        }
        
        
    }
    
    // Constructor for creating new posts, populated with predefined data
    public function create_post($tstamp, $post_url, $OP, $post_text, $subreddit)
    {
        $post = new RedditPost($subreddit, 0, false); // Says that we are not fetching new posts, and the post number does not matter
        $post->set_tstamp($tstamp);
        $post->set_post_url($post_url);
        $post->set_OP($OP);
        $post->set_post_text($post_text);
        // Subreddit is already set from default constructor
        return $post;
    }
    
    function get_json_post()
    {
        $url_text = "https://www.reddit.com/r/$this->subreddit/new/.json?count=20";
        $url = fopen($url_text, "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $this->tstamp = $json->{'data'}->{'children'}[$this->post_number]->{'data'}->{'created_utc'};
        $this->post_url = "https://en.reddit.com" . $json->{"data"}->{"children"}[$this->post_number]->{'data'}->{'permalink'};
        $this->OP = $json->{'data'}->{'children'}[$this->post_number]->{'data'}->{'author'};
        $this->post_text = $json->{'data'}->{'children'}[$this->post_number]->{'data'}->{'title'};
    }
    
    function get_tstamp()
    {
        return $this->tstamp;
    }
    
    function get_post_url()
    {
        return $this->post_url;
    }
    
    function get_OP()
    {
        return $this->OP;
    }
    
    function get_post_text()
    {
        return $this->post_text;
    }
    
    function get_subreddit()
    {
        return $this->subreddit;
    }
    
    function set_tstamp($tstamp)
    {
        $this->tstamp = $tstamp;
    }
    
    function set_post_url($post_url)
    {
        $this->post_url = $post_url;
    }
    
    function set_OP($OP)
    {
        $this->OP = $OP;
    }
    
    function set_post_text($post_text)
    {
        $this->post_text = $post_text;
    }
    
    function set_post_text($subreddit)
    {
        $this->subreddit = $subreddit;
    }
    
    function add_post($database_connection)
    {
        $insert = "INSERT INTO Forum_Posts (post_url, post_text, tstamp, OP, subreddit) VALUES ('$this->post_url', '$this->post_text', '$this->tstamp', '$this->OP', '$this->subreddit');";
    
        $result = mysqli_query($database_connection, $insert);
        return $result;
    }
        
        
}

/* Retrieves a specified number of reddit posts from the database from a specified subreddit
 * Similar to get_database_tweets() and get_database_reddit_posts()
 * 
 * $database_connection is the result of the mysqli_connect() function.
 * (Int) post_limit: The number of posts to return.
 * (String) subreddit: The name of the forum that you want to get the posts from. Currently the only supported forum is Bitcointalk.
 *
 * Returns a multidimensional array in the following format:
 * Position [0] = Post urls;
 * Position [1] = OPs;
 * Position [2] = Titles;
 * Position [3] = Timestamps;
 */
function get_database_reddit_posts($database_connection, $post_limit, $subreddit)
{
    $result = mysqli_query($database_connection, "SELECT post_url, OP, post_text, tstamp FROM Reddit_Posts WHERE (subreddit='$subreddit') ORDER BY tstamp DESC LIMIT $post_limit");
    
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
    
    $post_urls = array();
    $ops = array();
    $titles = array();
    $timestamps = array();
    
    // Get an array of all values for each field
    for($i = 0; $i < $size_of_array_of_rows; $i++)
    {
        $post_urls[$i] = $array_of_rows[$i]["post_url"];
        $ops[$i] = $array_of_rows[$i]["OP"];
        $titles[$i] = $array_of_rows[$i]["post_text"];
        $timestamps[$i] = $array_of_rows[$i]["tstamp"];
    }
    
    // Package all user data and return as an array
    $database_posts = array();
    
    $database_posts[0] = $post_urls;
    $database_posts[1] = $ops;
    $database_posts[2] = $titles;
    $database_posts[3] = $timestamps;
    
    return $database_posts;
}

// $posts is an array of RedditPost objects
function compare_posts($posts, $database_connection)
{
    $number_of_posts = count($posts);
    
    $unique_posts = array();
    
    for($i = 0; $i < $number_of_posts; $i++)
    {
        // Escaped because the posts in the database are escaped, and we will be comparing to those
        $post_url = mysqli_real_escape_string($database_connection, $posts[$i]->get_post_url());
        $username = $posts[$i]->get_OP();
        $timestamp = $posts[$i]->get_tstamp();
        $result = mysqli_query($database_connection, "SELECT post_url FROM Reddit_Posts WHERE (post_url='$post_url')");
        if(mysqli_fetch_array($result) == null)
        {
            $unique_posts[$i] = RedditPost::create_post($posts[$i]->get_tstamp, $posts[$i]->get_post_url, $posts[$i]->get_OP, $posts[$i]->get_post_text, $posts[$i]->get_subreddit);
        }
    }
    
    return $unique_posts;
}  

?>
