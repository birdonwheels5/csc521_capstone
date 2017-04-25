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
		
		// Chart variables for customizability.
		// These are the default values.
		var chart_div = "chart_div";
		var timespan = 24;
		var time_unit = "Hours";
		
		// Colors
		var active_color = "SlateGrey";
		var passive_color = "Snow";
		    
		// The twitter's color is changed on page load (located in the body tag)

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

		
	    google.charts.load("visualization", "1", {packages:["corechart"]});
	    google.charts.setOnLoadCallback(drawTrendlines);

	/* Draws a bitcoin chart based on the given parameters:
	 * (String) chart_div: The div to write the chart to.
	 * (Int) timespan:     The number of hours of price data to display
	 * (String) time_unit: "Days" or "Hours"
	 */
	function drawTrendlines() 
	{     
	      var data = new google.visualization.DataTable();
	      // Set the correct scale for the horizontal axis if time_unit is in days
	      var h_scale = timespan;

	      //if(time_unit == "Days")
	      //{
		  h_scale = (timespan) - 1;
	      /*}
	      else
	      {
		  // So the hours will display correctly
		  h_scale = h_scale - 1;
	      }*/

	      data.addColumn('number', 'X');
	      data.addColumn('number', 'BTCChina');
	      data.addColumn('number', 'BTC-e');
	      data.addColumn('number', 'Bitfinex');
	      data.addColumn('number', 'Bitstamp');
	      data.addColumn('number', 'Coinbase');
	      data.addColumn('number', 'Huobi');
	      data.addColumn('number', 'Kraken');
	      data.addColumn('number', 'OKCoin');

	      var chart = new google.visualization.LineChart(document.getElementById(chart_div));

	      $.when($.getJSON('get_price_data.php?span=' + timespan)).done( function(json_data) 
	      {
		for(i = 0; i < timespan; i++) 
		{
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
		
		while(min_price == 0)
		{
			for(var i = 0; i < (min_array.length); i++)
			{
				if(min_array[i] == 0 || min_array[i] == -1)
				{
					min_array[i] = "1000000000"; // Make this a really big number (1 billion) so it will not be the minimum value in the array
				}
			}
			
			min_price = Math.min(Math, min_array);
		}
		
		var max_price = Math.max.apply(Math, max_array);
		
		console.log(min_array);
		console.log("Min");
		console.log(min_price);
		console.log(max_array);
		console.log("Max");
		console.log(max_price);
		

		// Give a little wiggle room
		max_price = max_price * 1.05;
		min_price = min_price * 0.95;
		var options = 
		{
		  hAxis: 
		  {
		      title: 'Time (' + time_unit + ')',
		      direction: '-1',
		      gridlines: 
		      {
			  count: (h_scale)
		      },
		      viewWindowMode: 'explicit',
		      viewWindow: 
		      {
			  min: 0,
			  max: (h_scale)
		      }
		  },

		  vAxis: 
		  {
			title: 'Bitcoin Price',
			viewWindowMode: 'explicit',
			viewWindow: 
			{
				min: Math.floor(min_price),
				max: Math.ceil(max_price)
			}
		   },
			    backgroundColor: "#FFFAFA",
			    legend: 
			    {
				alignment: 'right',
				position: 'top',
				maxLines: '8',
				minLines: '1'
			    },
			    colors: ['black', 'red', 'blue', 'green', 'orange', 'purple', 'yellow', 'gray']
			};
				chart.draw(data, options);
			});
		    }
		    
		    function update_chart()
		    {
			chart_settings = document.forms.chart_settings;
			
			time_unit = chart_settings.unit.value;
			
			if(time_unit == "Days")
			{
			    timespan = chart_settings.timespan.value;
			    timespan = timespan * 24;
			}
			else // Hours
			{
			    timespan = chart_settings.timespan.value;
			}
			
			drawTrendlines();
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

<body class="color-0" onload="$('#li_twitter').css('background-color', active_color);">
			
	<div class="row"></div>
	
		<div class="empty col-2"> <!-- Left Margin -->
		</div>
		
		<div class="col-4"> <!-- Column 1 -->
			
			<div class="object shadow">
			
				<div id="chart_div"></div>
				
				<form action="" name="chart_settings" method="post" onchange="update_chart()" class="row chart">
					<input class="chart" type="number" name="timespan" min="1" value="1">
					<input class="chart" type="radio" name="unit" value="Hours"> Hours
					<input class="chart" type="radio" name="unit" value="Days" checked> Days
					
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
						<li id="li_twitter"><a onclick="
							$('#newsFeed').load('load_tweets.php');
							news_flag = 'twitter';
							
							// Make the active news tab a different color than the rest
							$('#li_twitter').css('background-color', active_color);
							$('#li_reddit').css('background-color', passive_color);
							$('#li_bitcointalk').css('background-color', passive_color);">Twitter</a></li>
						<!-- Select Reddit and set the news flag so that Reddit will be refreshed every x seconds -->
						<li id="li_reddit"><a onclick="$('#newsFeed').load('load_reddit.php');
							news_flag = 'reddit';
							
							// Make the active news tab a different color than the rest
							$('#li_twitter').css('background-color', passive_color);
							$('#li_reddit').css('background-color', active_color);
							$('#li_bitcointalk').css('background-color', passive_color);">Reddit</a></li>
						<!-- Select Bitcointalk and set the news flag so that Bitcointalk will be refreshed every x seconds -->
						<li id="li_bitcointalk"><a onclick="$('#newsFeed').load('load_bitcointalk.php');
							news_flag = 'bitcointalk';
							
							// Make the active news tab a different color than the rest
							$('#li_twitter').css('background-color', passive_color);
							$('#li_reddit').css('background-color', passive_color);
							$('#li_bitcointalk').css('background-color', active_color);">Bitcointalk</a></li>
					</ul>
					
				<hr/>
				
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
