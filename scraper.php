<?php
$ch = curl_init();
$threads = array();
$match = array();

curl_setopt($ch, CURLOPT_URL, "https://bitcointalk.org/index.php?board=77.0");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$data = curl_exec($ch);
// ~https://bitcointalk.org/index.php?topic=~
$match = preg_match('~(https:\/\/bitcointalk\.org\/index\.php\?topic=\d\d\d\d\d\d\d)\.0..(\[\d\d\d\d-\d\d-\d\d\])~', $data);
$threads['url'] = $match[1];
print_r($threads['url']);
  
curl_close($ch);
?>
