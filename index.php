<!DOCTYPE html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<title>Welcome</title>
	<?php
		include "login/CookieHandler.php";
		include "func/login.php";
	?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	    <script type="text/javascript">

		// Refresh all Bitcoin prices and the News feed
		
		// Defaults to twitter on page load
		var news_flag = "twitter";

		$(function() {
		    refreshBtcPrice(30);
		});

		$(function() {
		    refreshNewsFeed(30);
		});

		function refreshBtcPrice(seconds) {
		    setInterval(function() {
			    $('#BtcPrice').load('load_exchanges.php');
		    }, seconds * 1000)
		}

		function refreshNewsFeed(seconds) {
		    setInterval(function() 
		    {
			var refresh_link = "load_tweets.php"; // For loading the twitter news
			
			if(news_flag == "reddit")
			{
				refresh_link = "load_reddit.php"; // For loading Reddit news
			}
			
			if(news_flag == "bitcointalk")
		 	{
				refresh_link = "load_bitcointalk.php"; // For loading Bitcointalk news
			}
			
			$('#newsFeed').load(refresh_link);
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

			var min_price = Math.min(Math, min_array);
			var max_price = Math.max(Math, max_array);
			
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
				min: Math.floor(min_price),
				max: Math.ceil(max_price)
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
	    	<link rel='stylesheet' type="text/css" href="index.css">

</head>

<body class="color-0">
			
	<div class="row"></div>
	
		<div class="empty col-2"> <!-- Left Margin -->
		</div>
		
		<div class="col-4"> <!-- Column 1 -->
			
			<div class="object shadow">
			
				<div id="chart_div"></div>
				
				<form action="" name="chart" method="get" onkeyup="" class="row chart">
					<input class="chart" type="number" name="x-axis" min="1">
					<input class="chart" type="radio" name="unit" value="1" checked> Hours
					<input class="chart" type="radio" name="unit" value="24" checked> Days
					
				</form>
				
				<div id="BtcPrice">
					<script type="text/javascript">
						$('#BtcPrice').load('load_exchanges.php');
					</script>
				</div>
						
			</div>
		</div>
			
				
		<div class="col-4"> <!-- Column 2 -->
			<div class="object shadow">
				<div id="news_nav">
					<ul class="topnav" id="newsnav">
						<!-- Select twitter and set the news flag so that twitter will be refreshed every x seconds -->
						<li><a onclick="
							$('#newsFeed').load('load_tweets.php');
							news_flag = 'twitter';">Twitter</a></li>
						<!-- Select Reddit and set the news flag so that Reddit will be refreshed every x seconds -->
						<li><a onclick="$('#newsFeed').load('load_reddit.php');
							news_flag = 'reddit';">Reddit</a></li>
						<!-- Select Bitcointalk and set the news flag so that Bitcointalk will be refreshed every x seconds -->
						<li><a onclick="$('#newsFeed').load('load_bitcointalk.php');
							news_flag = 'bitcointalk';">Bitcointalk</a></li>
					</ul>
				</div>
				
				<div class="row">
				</div>
				
				<div id="newsFeed">
					<script type="text/javascript">
						$('#newsFeed').load('load_tweets.php');
					</script>
				</div>
			</div>
		</div>
		
		<div class="empty col-2"> <!-- Right Margin -->
		</div>
		
	
</body>
