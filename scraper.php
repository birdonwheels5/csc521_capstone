<?php
$ch = curl_init();
$threads = array();

curl_setopt($ch, CURLOPT_URL, "https://bitcointalk.org/index.php?board=77.0");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$data = curl_exec($ch);
//<span id="msg_18575527"><a href="https://bitcointalk.org/index.php?topic=1868785.0">[2017-04-14]How Greg Maxwell Exploited Bitcoin Unlimit
preg_match_all('~<a href="https://bitcointalk.org/index.php?topic=~', $data, $match);
$threads['url'] = $match[1];
print_r($threads['url']);die;

  
curl_close($ch);
?>
