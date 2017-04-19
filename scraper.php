<?php
$ch = curl_init();
$threads = array();
$match = array();

curl_setopt($ch, CURLOPT_URL, "https://bitcointalk.org/index.php?board=77.0");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$data = curl_exec($ch);
// ~https://bitcointalk.org/index.php?topic=~
preg_match_all('~(https:\/\/bitcointalk\.org\/index\.php\?topic=\d\d\d\d\d\d\d)\.0..(\[\d\d\d\d-\d\d-\d\d\])~', $data, $match);
$threads['url'] = $match[1];
$threads['date'] = $match[2];
print_r($threads['url']);
print_r($threads['date']);
  
curl_close($ch);
?>
