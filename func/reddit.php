<?php

class RedditPost		
{		
    private $tstamp = "";	
    private $post_url = "";		
    private $OP = "";		
    private $post_text = "";		
    private $post_number = 0;
    
    function __construct($subreddit, $post_number, $is_fetching_new_posts)
    {
        $this->post_number = $post_number;
        if($is_fetching_new_posts)
        {
            $this->get_json_post($subreddit);
        }
        
        
    }
    
    
    function get_json_post($subreddit)
    {
        $url_text = "https://www.reddit.com/r/$subreddit/new/.json?count=20";
        $url = fopen($url_text, "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $this->tstamp = var_dump($json->{'data'}->{'children'}[$this->post_number]->{'data'});//->{'title'}->{'created_utc'};
        $this->post_url = "https://www.reddit.com" . $json->{"data"}->{"children"}[$this->post_number]->{'permalink'};
      
        
        print "\n\n";
    }

}

$post = new RedditPost("bitcoin", 5, true);



?>
