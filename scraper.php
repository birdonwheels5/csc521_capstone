<?php
$ch = curl_init();
$threads = array();

curl_setopt($ch, CURLOPT_URL, "https://bitcointalk.org/index.php?board=77.0");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$data = curl_exec($ch);
// ~https://bitcointalk.org/index.php?topic=~
//preg_match_all('~<a href="https://bitcointalk.org/index.php?topic=~', $data, $match);
//$threads['url'] = $match[1];
//print_r($threads['url']);die;

  
curl_close($ch);
?>
