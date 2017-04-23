<?php

scrape_bitcointalk();
//print make_timestamp("today at 12:01:01 AM");
$test1 = explode(" ", "today at 12:01:01 AM");
//print make_timestamp("april 21, 2017, 2:30:57 PM");
$test2 = explode(" ", "april 21, 2017, 2:30:57 PM");
//print_r(make_timestamp($test1));
print_r(make_timestamp($test2));

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
  //print_r($threads['time']);
  $pieces = explode( " ", $threads['time']);
  print_r($pieces);
  $time = make_timestamp($pieces);
  //print_r($time);
  
  //for ($i = 0; $i < $number_of_threads; $i++)
  //{
    //$thread = make_curl($threads['url'][$i]);
    
    
  //}
}

function make_timestamp($timestamp)
{
  //timestamp format: 02:19:08 AM"
  if ( count($timestamp) == 4)
  {
    $time = explode(":", $timestamp[2]);
    $hours = (int)$time[0];
    print($hours);
    if ($timestamp[3] == 'PM')
    {
      $hours = $hours + 12;
      $new_time = $hours . $time[1] . $time[2];
    }
    else if ($hours == '12' && $timestamp[3] == 'AM')
    {
      $new_time = '00' . $time[1] . $time[2];
    }
    else
    {
      $new_time = $time;
      $new_time = implode("", $new_time);
    }
  }
  else
  {
    $time = explode(":", $timestamp[3]);
    
    $hours = (int)$time[0];
    print($hours);
    if ($timestamp[4] == 'PM')
    {
      $hours = $hours + 12;
      $new_time = $hours . $time[1] . $time[2];
    }
    else if ($hours == '12' && $timestamp[4] == 'AM')
    {
      $new_time = '00' . $time[1] . $time[2];
    }
    else
    {
      $new_time = $time;
      $new_time = implode("", $new_time);
    }
  }
  


  return $new_time;
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

