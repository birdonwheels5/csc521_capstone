<?php
$ch = curl_init();
$threads = array();
$match = array();

curl_setopt($ch, CURLOPT_URL, "https://bitcointalk.org/index.php?board=77.0");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$data = curl_exec($ch);
// ~https://bitcointalk.org/index.php?topic=~
$match = preg_match_all('~<a href="https://bitcointalk.org/index.php?topic=~', $data);
var_dump($match);

  
curl_close($ch);
?>
