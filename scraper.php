<?php
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://bitcointalk.org/index.php?board=77.0");

$data = curl_exec($ch);
preg_match_all('<a href="https://bitcointalk.org/index.php?topic=/([/d]+)000/', $data, $match);
  
curl_close($ch);
?>
