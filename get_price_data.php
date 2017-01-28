<?php

include "btc_helper.php";

if ($_SERVER["REQUEST_METHOD"] == "GET") 
{
    // Load database settings from config file
    $settings = array();
    $settings = load_config();
    
    $mysql_user = $settings[0];
    $mysql_host = $settings[1];
    $mysql_pass = $settings[2];
    $mysql_database = "btc_quotation";
    
    
    $timespan = $_GET["span"];
    
    // Establish connection to the database
    $con = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_database);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $btcchina = array();
    $btcchina = get_price_data($con, "btcchina", $timespan);
    
    $btce = array();
    $btce = get_price_data($con, "btc-e", $timespan);
    
    $bitfinex = array();
    $bitfinex = get_price_data($con, "bitfinex", $timespan);
    
    $bitstamp = array();
    $bitstamp = get_price_data($con, "bitstamp", $timespan);
    
    $coinbase = array();
    $coinbase = get_price_data($con, "coinbase", $timespan);
    
    $huobi = array();
    $huobi = get_price_data($con, "huobi", $timespan);
    
    $kraken = array();
    $kraken = get_price_data($con, "kraken", $timespan);
    
    $okcoin = array();
    $okcoin = get_price_data($con, "okcoin", $timespan);
    
    mysqli_close($con);
    
    $exchanges = [
    "btcchina"  => $btcchina,
    "btce"      => $btce,
    "bitfinex"  => $bitfinex,
    "bitstamp"  => $bitstamp,
    "coinbase"  => $coinbase,
    "huobi"     => $huobi,
    "kraken"    => $kraken,
    "okcoin"    => $okcoin,
    ];
    
    // Encode the data into JSON format
    $json = json_encode($exchanges);
    
    // Spit out the encoded JSON so the browser can read the data
    print $json;
}

function get_price_data($con, $exchange, $timespan)
{
    // Query database for exchange prices
    $result = mysqli_query($con, "SELECT * FROM `" . $exchange . "`");
    
    // Obtain the number of rows from the result of the query
    $num_rows = mysqli_num_rows($result);
    
    // Will be storing all the rows in here
    $rows = array();
    
    // Get all the rows
    for($i = 0; $i < $num_rows; $i++)
    {
        $rows[$i] = mysqli_fetch_array($result);
    }
    $size_of_rows = $num_rows - 1;
    
    //var_dump($rows);
    
    $prices = array();
    $timestamps = array();
    
    // Get an array of the past N prices for each exchange
    for($i = ($size_of_rows); $i > ($size_of_rows - $timespan); $i--)
    {
        $prices[$i] = $rows[$i]["btc_price"];
        $timestamps[$i] = $rows[$i]["timestamp"];
    }
    
    // Query database for min exchange price. The queries are structured this way because MYSQL orders then limits the results, but we need to limit then order, and limit again.
    $min_result = mysqli_query($con, "SELECT * FROM (SELECT * FROM `" . $exchange . "` LIMIT " . ($size_of_rows - $timespan) . "," . $timespan . ") `" . $exchange . "` ORDER BY `btc_price` ASC LIMIT 1;");
    $max_result = mysqli_query($con, "SELECT * FROM (SELECT * FROM `" . $exchange . "` LIMIT 16,8) `" . $exchange . "` ORDER BY `btc_price` DESC LIMIT 1;");
    
    $min_array = mysqli_fetch_array($min_result);
    $max_array = mysqli_fetch_array($max_result);
    
    $min_max_values = [
        "min" => $min_array[0],
        "max" => $max_array[0]
    ];
    
    $data = array();
    $data[0] = array_values($prices);
    $data[1] = array_values($timestamps);
    $data[2] = $min_max_values;
    
    //var_dump($data[0]);
    return $data;
}
    
?>
