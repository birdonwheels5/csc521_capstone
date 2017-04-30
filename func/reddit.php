<?php

class RedditPost		
{		
    private $tstamp = "";		
    private $rp_id = "";		
    private $post_url = "";		
    private $OP = "";		
    private $post_text = "";		
    private $post_number;
    
    //function RedditPost($post_number)
    //{
        
    //}
                            
    
    
    function get_json_post($subreddit)
    {
        $url_text = "https://www.reddit.com/r/$subreddit/new/.json?count=20";
        $url = fopen($url_text, "r");
        
        $json = json_decode(stream_get_contents($url));
        
        var_dump($json->{"data"}->{"children"}[1]);
        
        print "\n\n";
    }

}

$post = new RedditPost();
$post->get_json_post("bitcoin");



?>
