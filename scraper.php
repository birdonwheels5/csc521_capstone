<?php
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://bitcointalk.org/index.php?board=77.0");

$data = curl_exec($ch);
var_dump($data);
  
curl_close($ch);
?>
