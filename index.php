<!DOCTYPE html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<title>Welcome</title>
    	<link rel='stylesheet' type="text/css" href="main.css">
	<?php
		include "login/CookieHandler.php";
		include "func/login.php";
	?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	    <script type="text/javascript">

		// Refresh all Bitcoin prices and the Twitter feed

		$(function() {
		    refreshBtcPrice(30);
		});

		$(function() {
		    refreshTwitter(30);
		});

		function refreshBtcPrice(seconds) {
		    setInterval(function() {
			    $('#BtcPrice').load('load_exchanges.php');
		    }, seconds * 1000)
		}

		function refreshTwitter(seconds) {
		    setInterval(function() {
			    $('#twitter').load('load_tweets.php');
		    }, seconds * 1000)
		}


		google.charts.load('current', {packages: ['corechart', 'line']});
		google.charts.setOnLoadCallback(drawTrendlines);

	        function drawTrendlines() {
		var data = new google.visualization.DataTable();
		data.addColumn('number', 'X');
		data.addColumn('number', 'BTCChina');
		data.addColumn('number', 'BTC-e');
		data.addColumn('number', 'Bitfinex');
		data.addColumn('number', 'Bitstamp');
		data.addColumn('number', 'Coinbase');
		data.addColumn('number', 'Huobi');
		data.addColumn('number', 'Kraken');
		data.addColumn('number', 'OKCoin');

		var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
		var timespan = 24; // In hours

		$.when($.getJSON('get_price_data.php?span=' + timespan)).done( function(json_data) {
			for(i = 0; i < timespan; i++) {
			    data.addRow(
				[i, parseFloat(json_data.btcchina[0][i]), parseFloat(json_data.btce[0][i]), parseFloat(json_data.bitfinex[0][i]), parseFloat(json_data.bitstamp[0][i]), 
				    parseFloat(json_data.coinbase[0][i]), parseFloat(json_data.huobi[0][i]), parseFloat(json_data.kraken[0][i]), parseFloat(json_data.okcoin[0][i])]
			    );
			}
			var min_array = [
			    json_data.btcchina[2].min,
			    json_data.btce[2].min,
			    json_data.bitfinex[2].min,
			    json_data.bitstamp[2].min,
			    json_data.coinbase[2].min,
			    json_data.huobi[2].min,
			    json_data.kraken[2].min,
			    json_data.okcoin[2].min
			];

			var max_array = [
			    json_data.btcchina[2].max,
			    json_data.btce[2].max,
			    json_data.bitfinex[2].max,
			    json_data.bitstamp[2].max,
			    json_data.coinbase[2].max,
			    json_data.huobi[2].max,
			    json_data.kraken[2].max,
			    json_data.okcoin[2].max
			];

			var min_price = Math.min.apply(Math, min_array);
			var max_price = Math.max.apply(Math, max_array);
			
			// Give a little wiggle room
			max_price = max_price * 1.05;
			min_price = min_price * 0.95;

			var options = {
		    hAxis: {
		    title: 'Time (Hours)',
		    direction: '-1',
		    gridlines: {count: (timespan - 1)},
		    viewWindowMode: 'explicit',
		    viewWindow: {
			    min: 0,
			    max: (timespan - 1)
		    }
		 },
		    vAxis: {
			title: 'Bitcoin Price',
			viewWindowMode: 'explicit',
			viewWindow: {
				min: (Math.floor(min_price) - 5),
				max: (Math.ceil(max_price) + 5)
			    }
		    },
		    colors: ['#AB0D06', '#007329'],
		    legend: {
			alignment: 'right',
			position: 'top',
			maxLines: '8'
		    },
		    colors: ['black', 'red', 'blue', 'green', 'orange', 'purple', 'yellow', 'gray']
		};

			chart.draw(data, options);
		});
	    }

	</script>
	<?php 
            
                $cookie_handler = new CookieHandler();
                $cookie_name = $cookie_handler->get_cookie_name();
                $cookie_handler->cookie_exists($cookie_name);
                
                // Check to see if the cookie exists
                if($cookie_handler->get_exists())
                {
                    $user_cookie = $cookie_handler->get_cookie($cookie_name);
                    $uuid = $user_cookie->get_uuid();
                    $session_id = get_session($uuid);
                    $cookie_handler->validate_cookie($user_cookie, $session_id);
                    
                    // So we can personalize the page a little for the user
                    $user_data = get_user_data($uuid);
                    
                    update_last_login($uuid);
                }
		
                print_header($cookie_handler, $cookie_name);
            ?>
</head>

<body class="color-0">
			
		<div class="empty col-2"> <!-- Left Margin -->
		</div>
		
		<div class="col-4"> <!-- Column 1 -->
			
			<div class="object shadow">
			
				<div id="chart_div"></div>
				
				<form action="process_login.php" name="login" method="post" class="object shadow" onkeyup="check_form()">
        			<input type="text" name="username" placeholder="Username" required>
        			<input type="password" name="password" placeholder="Password" required>
					<input type="submit" name="submit" value="Login" required>
					<div class="small"><a href="./login/reset_pwd.php">Reset your password</a>, <a href="createuser.php">Register</a> or <a href="view.php">Continue as Guest</a></div>
				</form>

				<div id="BtcPrice">
					<script type="text/javascript">
						$('#BtcPrice').load('load_exchanges.php');
					</script>
				</div>
						
			</div>
			
		</div>
		
		<div class="col-4"> <!-- Column 2 -->
			<div class="object shadow" id="twitter">
				<script type="text/javascript">
					$('#twitter').load('load_tweets.php');
				</script>
			</div>
		</div>
		
		<div class="empty col-2"> <!-- Right Margin -->
		</div>
		
	
</body>
