<?php

function make_curl($url)
{
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  return curl_exec($ch);
}

function scrape_bitcointalk()
{
  $url = "https://bitcointalk.org/index.php?board=77.0";
  $threads = array();
  $match = array();
  
  //make curl
  $data = make_curl($url);
  
  //get urls, dates, and titles of threads
  preg_match_all('~(https:\/\/bitcointalk\.org\/index\.php\?topic=.*)\.0..(\[\d\d\d\d-\d\d-\d\d\])(.*)</a>~', $data, $match);
  $threads['url'] = $match[1];
  $threads['date'] = $match[2];
  $threads['title'] = $match[3];
  
  //get username of thread creator
  preg_match_all('~<a href="https:\/\/bitcointalk\.org\/index\.php\?action=profile;u=\d*" title="View the profile of .*">(.*)<\/a>~', $data, $names);
  $threads['names'] = $names[1];
  $number_of_threads = count($threads['url']);
  $threads['names'] = array_slice($threads['names'], 3, $number_of_threads);
  
  //get a timestamp of when the thread was created
  for ($i = 0; $i < $number_of_threads; $i++)
  {
    $thread = make_curl($threads['url'][$i]);
    $result = preg_match('~<div class="smalltext">(.*)<\/div><\/td>~', $thread, $match2);
    if ($result == 1)
    {
      $threads['time'][$i] = $match2[1];
      $pieces = explode( " ", $threads['time'][$i]);
      $timestamp = make_timestamp($pieces, $threads['date'][$i]);
      $threads['timestamp'][$i] = $timestamp;
    }
    else
    {
      $timestamp = time();
      $threads['timestamp'][$i] = $timestamp;
    }

  }
  
  //return all the data
  //print_r($threads);
  return $threads;
}

function make_timestamp($timestamp, $date)
{
  //timestamp format: 02:19:08 AM"
  if ( count($timestamp) == 4)
  {
    $time = explode(":", $timestamp[2]);
    $hours = (int)$time[0];
    if ( $hours != 12 && $timestamp[3] == 'PM')
    {
      $hours = $hours + 12;
      $new_time = $hours . ":" . $time[1] . ":" . $time[2];
    }
    else if ($hours == '12' && $timestamp[3] == 'AM')
    {
      $new_time = '00' . ":" . $time[1] . ":" . $time[2];
    }
    else
    {
      $new_time = $time;
      $new_time = implode(":", $new_time);
    }
  }
  else if (count($timestamp) == 5)
  {
    $time = explode(":", $timestamp[3]);
    
    $hours = (int)$time[0];
    if ($hours != 12 && $timestamp[4] == 'PM')
    {
      $hours = $hours + 12;
      $new_time = $hours . ":" . $time[1] . ":" . $time[2];
    }
    else if ($hours == '12' && $timestamp[4] == 'AM')
    {
      $new_time = '00' . ":" . $time[1] . ":" .  $time[2];
    }
    else
    {
      $new_time = $time;
      $new_time = implode(":", $new_time);
    }
  }
  else
  {
    $new_time = null;
  }
  preg_match('~\[(.*)\]~', $date, $match);
  $tstamp = $match[1] . " " . $new_time; 
  $tstamp = strtotime($tstamp);
  return $tstamp;
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
  
function add_post($url, $title, $timestamp, $username, $database_connection)
{
    $insert = "INSERT INTO Forum_Posts (post_url, post_text, tstamp, username) VALUES ('$url', '$post_text', $timestamp, $username);";
    
    $result = mysqli_query($database_connection, $insert);
    return $result;
}


  ?>

