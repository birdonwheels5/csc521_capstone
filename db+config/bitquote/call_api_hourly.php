<?php
    
    include "../~public_html/func/btc.php";
    
    // Load database settings from config file
    $settings = array();
    $settings = load_config();
    
    $mysql_user = $settings[0];
    $mysql_host = $settings[1];
    $mysql_pass = $settings[2];
    $mysql_database = $settings[3];
    
    // Establish connection to the database
    $con = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_database);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database while attempting to update the database tables! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    $exchanges = array();
    $exchanges[0] = "btcchina";
    $exchanges[1] = "btc-e";
    $exchanges[2] = "bitfinex";
    $exchanges[3] = "bitstamp";
    $exchanges[4] = "coinbase";
    //$exchanges[5] = "cryptsy";
    $exchanges[5] = "huobi";
    $exchanges[6] = "kraken";
    $exchanges[7] = "okcoin";
    
    print add_chart_exchange_btc_prices($exchanges, $con);
    
    mysqli_close($con);
    
?>
