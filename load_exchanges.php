<?php
    
    include "btc_helper.php";
    
    // Load database settings from config file
    $settings = array();
    $settings = load_config();
    
    $mysql_user = $settings[0];
    $mysql_host = $settings[1];
    $mysql_pass = $settings[2];
    $mysql_database = $settings[3];
    
    $average_price = 0;
    
    // Establish connection to the database
    $con = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_database);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database while attempting to load exchanges! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    $exchanges = array();
    $exchanges[0] = "BTCChina";
    $exchanges[1] = "BTC-e";
    $exchanges[2] = "Bitfinex";
    $exchanges[3] = "Bitstamp";
    $exchanges[4] = "Coinbase";
    //$exchanges[5] = "Cryptsy";
    $exchanges[5] = "Huobi";
    $exchanges[6] = "Kraken";
    $exchanges[7] = "OKCoin";
    
    $num_exchanges = count($exchanges);
    
    print "<table>\n";
    for($i = 0;$i < $num_exchanges; $i++)
    {
        $exchange_price = get_btc_price_from_database(strtolower($exchanges[$i]), $con);
        // Start computing average price
        $average_price = $average_price + $exchange_price;
        print "<tr>\n";
        print "<td>" . $exchanges[$i] . "</td>\n";
        //                                            Make exchange string lowercase because the get_btc_price function expects it to be lowercase
        print "\n<td>\n $". $exchange_price . " <td/>\n";
    }
    // Finish calculating average across exchanges
    $average_price = $average_price / $num_exchanges;
    print "<tr>\n";
    print "<td> Average Price: </td>\n";
    print "\n<td>\n $". (float)number_format($average_price, 2, ".", "") . " <td/>\n";
    print "</table>\n";
    
?>
