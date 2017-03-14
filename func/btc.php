<?php

require_once "general.php";

// Test new exchanges here...
//print get_btc_price("bitstamp");

/* Fetch the current BTC price from specified exchange. 
* Parameters: 
*   $exchange (string) - Chooses which exchange to fetch price from. Options are: bitstamp, cryptsy, poloniex, ...
*/

function get_btc_price($exchange)
{
    $btc_price = 0; // If the price shows up as 0, then we know there's a problem connecting to the API(s)
    $usd_cny = calculate_cny_exchange_rate();
    
    if($exchange == "bitstamp")
    {
        $url = fopen("http://www.bitstamp.net/api/ticker/", "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $btc_price = $json->{"last"};
    }/*
    // We are not using Cryptsy because of its low BTC/USD volume (<10,000 BTC)
    else if($exchange == "cryptsy")
    {
        // We're using  the server pubapi2 because at the time of programming, pubapi1 seems to be down. When you remove the "2" from pubapi2, either server is randomly selected.
        $url = fopen("http://pubapi2.cryptsy.com/api.php?method=singlemarketdata&marketid=2", "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $btc_price = $json->{"return"}->{"markets"}->{"BTC"}->{"lasttradeprice"};
    }*/
    else if($exchange == "cex.io")
    {
        $url = fopen("https://cex.io/api/ticker/BTC/USD", "r");
        
        $json = json_decode(stream_get_contents($url));
        var_dump($json);
        
        //$btc_price = $json->{"return"}->{"markets"}->{"BTC"}->{"lasttradeprice"};
    }
    else if($exchange == "coinbase")
    {
        $url = fopen("https://coinbase.com/api/v1/prices/spot_rate", "r");
        
        $data = json_decode(stream_get_contents($url));
        
        $btc_price = $data->amount;
    }
    else if($exchange == "btc-e")
    {
        $url = fopen("https://btc-e.com/api/3/ticker/btc_usd", "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $btc_price = $json->{"btc_usd"}->{"last"};
    }
    else if($exchange == "bitfinex")
    {
        $url = fopen("https://api.bitfinex.com/v1/ticker/btcusd", "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $btc_price = $json->mid;
    }
    else if($exchange == "kraken")
    {
        $url = fopen("https://api.kraken.com/0/public/Ticker?pair=XXBTZUSD", "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $btc_price = $json->{"result"}->{"XXBTZUSD"}->{"a"}[0];
    }
    else if($exchange == "btcchina" && $usd_cny > 0)
    {
        $url = fopen("https://data.btcchina.com/data/ticker?market=btccny", "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $btc_price = ($json->{"ticker"}->{"last"} * $usd_cny);
    }
    else if($exchange == "huobi" && $usd_cny > 0)
    {
        $url = fopen("http://api.huobi.com/staticmarket/ticker_btc_json.js", "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $btc_price = ($json->{"ticker"}->{"last"} * $usd_cny);
    }
    else if($exchange == "okcoin") // OKCoin has a BTC/USD market
    {
        $url = fopen("https://www.okcoin.com/api/ticker.do?ok=1", "r");
        
        $json = json_decode(stream_get_contents($url));
        
        $btc_price = ($json->{"ticker"}->{"last"});
    }
    else
    // This will be triggered if there is no method to connect to the given exchange's API
    // Remember to create a new table in the database for any new exchanges. Match the structure of the other exchanges by duplicating another exchange's table (including values).
    {
        $btc_price = -1;
        
        $log_message = "WARNING: No method found to connect to exchange \"" . $exchange . "\"'s API. Please fix this.";
        log_to_file($log_message);
    }
    
    return (float)number_format($btc_price, 2, ".", "");
    
}

// Calculates USD/CNY based on BTC data obtained from blockchain.info
// Returns a double with the exchange rate, or -1 if it failed to fetch the rate
// This is a work around due to the fact that the Chinese exchanges do not have a BTC/USD pair
function calculate_cny_exchange_rate()
{
    $usd_btc = 0;
    $cny_btc = 0;
    $usd_cny = 0;
    
    $url = fopen("https://blockchain.info/ticker", "r");
    
    $json = json_decode(stream_get_contents($url));
    
    $usd_btc = $json->{"USD"}->{"last"};
    $cny_btc = $json->{"CNY"}->{"last"};
    
    $usd_cny = ($usd_btc / $cny_btc);
    
    return $usd_cny;
}

// Returns btc_price from database
// $exchange (string): The exchange we want to fetch the price from
// $database_connection is the result of the mysqli_connect() function
function get_btc_price_from_database($exchange, $database_connection)
{
    $result = mysqli_query($database_connection, "SELECT * FROM `" . $exchange . "`");
    
    // num_rows should always be one because we only store one price per exchange
    $num_rows = 1;
    
    // Will be storing all the rows in here
    // Multidimensional array of form rows[table][row]
    $rows = array();
    
    // Get all the rows
    for($i = 0; $i < $num_rows; $i++)
    {
        $rows[$i] = mysqli_fetch_array($result);
    }
    
    return $rows[0][0]; // btc_price
}
// $database_connection is the result of the mysqli_connect() function
// $database_name (string)
// Returns boolean
function add_buffer_exchange_btc_price($exchange, $database_connection, $database_name)
{
    
    $btc_price = get_btc_price($exchange);
    if($btc_price > 0)
    {
        // Update old price with the new one.
        $update = "UPDATE `" . $database_name . "`.`" . $exchange . "` SET `btc_price` = '" . $btc_price . "' WHERE `" . $exchange . "`.`extra` =0;";
        
        $result = mysqli_query($database_connection, $update);
    }
    else
    {
        $result = "NOTICE: Failed to get btc_price from exchange " . $exchange . ". Database was not modified.";
        log_to_file($result);
    }
    
    return $result;
}

// $database_connection is the result of the mysqli_connect() function
// $database_name (string)
// Returns boolean
function add_chart_exchange_btc_price($exchange, $database_connection)
{
    
    $btc_price = get_btc_price($exchange);
    if($btc_price > 0)
    {
        $insert = "INSERT INTO `" . $exchange . "` (`btc_price`, `timestamp`) VALUES ('" . $btc_price . "', '" . time() . "');";
        
        $result = mysqli_query($database_connection, $insert);
    }
    else
    {
        $result = "NOTICE: Failed to get btc_price from exchange " . $exchange . ". Database was not modified.";
        log_to_file($result);
    }
    
    var_dump($result);
    
    return $result;
}

?>
