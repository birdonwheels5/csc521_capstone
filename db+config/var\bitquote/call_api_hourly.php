<?php
    
    include "/var/www/html/capstone/btc_helper.php";
    
    // Load database settings from config file
    $settings = array();
    $settings = load_config();
    
    $mysql_user = $settings[0];
    $mysql_host = $settings[1];
    $mysql_pass = $settings[2];
    $mysql_database = "btc_quotation";
    
    // Establish connection to the database
    $con = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_database);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database while attempting to update the database tables! Please check your database and database settings!";
        log_to_file($log_message);
    }

    add_chart_exchange_btc_price("bitfinex", $con);
    add_chart_exchange_btc_price("bitstamp", $con);
    //add_chart_exchange_btc_price("cryptsy", $con);
    add_chart_exchange_btc_price("coinbase", $con);
    add_chart_exchange_btc_price("kraken", $con);
    add_chart_exchange_btc_price("okcoin", $con);
    add_chart_exchange_btc_price("btcchina", $con);
    add_chart_exchange_btc_price("huobi", $con);
    add_chart_exchange_btc_price("btc-e", $con);
    
    mysqli_close($con);
    
?>
