<?php

class RedditPost		
{		
    private $tstamp = "";	
    private $post_url = "";		
    private $OP = "";		
    private $post_text = "";		
    private $post_number = 0;
    
    function __construct($subreddit, $post_number)
    {
        $this->post_number = $post_number;
        get_json_post($subreddit);
        
        
    }
                            
    
    
    function get_json_post($subreddit)
    {
        $url_text = "https://www.reddit.com/r/$subreddit/new/.json?count=20";
        $url = fopen($url_text, "r");
        
        $json = json_decode(stream_get_contents($url));
        
        var_dump($json->{"data"}->{"children"}[$this->post_number]);
        
        print "\n\n";
    }

}

$post = new RedditPost("bitcoin", 5);



?>
