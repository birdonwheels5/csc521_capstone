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
    
    function add_post($database_connection)
    {
        $insert = "INSERT INTO Forum_Posts (post_url, post_text, tstamp, OP, subreddit) VALUES ('$this->post_url', '$this->post_text', '$this->tstamp', '$this->OP', '$this->subreddit');";
    
        $result = mysqli_query($database_connection, $insert);
        return $result;
    }
        
        
}



$post = new RedditPost("bitcoin", 5, true);

var_dump($post);



?>
