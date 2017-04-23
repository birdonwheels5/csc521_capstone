<?php

scrape_bitcointalk();
//make_timestamp()
//make_timestamp()

function make_curl($url)
{
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  return curl_exec($ch);
}

function scrape_bitcointalk()
{
  $threads = array();
  $match = array();
  $data = make_curl("https://bitcointalk.org/index.php?board=77.0");
  preg_match_all('~(https:\/\/bitcointalk\.org\/index\.php\?topic=.*)\.0..(\[\d\d\d\d-\d\d-\d\d\])(.*)</a>~', $data, $match);
  $threads['url'] = $match[1];
  $threads['date'] = $match[2];
  $threads['title'] = $match[3];
  $number_of_threads = count($threads['url']);
  //print_r($threads['url']);
  //print_r($threads['date']);
  //print_r($threads['title']);
  //print($number_of_threads . " \n\n");
  
  
  $thread = make_curl($threads['url'][0]);
  //print($threads['url'][0] . " \n");
  preg_match('~<div class="smalltext">(.*)</div></td>~', $thread, $match2);
  $threads['time'] = $match2[1];
  print_r($threads['time']);
  //$time = make_timestamp($threads['time']);
  //print $time;
  
  //for ($i = 0; $i < $number_of_threads; $i++)
  //{
    //$thread = make_curl($threads['url'][$i]);
    
    
  //}
}

function make_timestamp($timestamp)
{
  $pieces = explode( " ", $timestamp);
  //pieces[2][3] format: 02:19:08 AM"
  $time = explode(":", $pieces[2]);
  //print($hours);
  $hours = (int)$time[0];
  if ($pieces[3] == 'PM')
  {
    $hours += 12;
    $new_time = $hours . $time[1] . $time[2];
  }
  if ($hours == '12' && $pieces[3] == 'AM')
  {
    $new_time = '00' . $time[1] . time[2];
  }
  else
  {
    $new_time = $pieces[2];
  }
  return $time;
}

function compare_threads($processed_tweets, $database_connection)
{
    global $number_of_tweets;
    
    $number_of_users = count($processed_tweets);
    
    $results = array();
    
    for($i = 0; $i < $number_of_users; $i++)
    {
        $user_array = array();
        
        for($t = 1; $t < (count($processed_tweets[$i])); $t++) // $t is for tweets
        {
            // Escaped because the tweets in the database are escaped, and we will be comparing to those
            $post_text = mysqli_real_escape_string($database_connection, $processed_tweets[$i][$t]);
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
}  
  
  ?>
