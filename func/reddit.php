<?php

class RedditPost		
{		
    private $tstamp = "";		
    private $rp_id = "";		
    private $post_url = "";		
    private $OP = "";		
    private $post_text = "";		
    
    
    
    function get_post($subreddit)
    {
         $url = fopen("https://www.reddit.com/r/bitcoin/new/.json?count=20", "r");
        
         $json = json_decode(stream_get_contents($url));
         var_dump($json);
    }

}

RedditPost post = new RedditPost();
post.get_post();



?>
