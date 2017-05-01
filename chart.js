		// Refresh all Bitcoin prices and the News feed
		
		// Defaults to twitter on page load
		var news_flag = "twitter";
		
		// Chart variables for customizability.
		// These are the default values.
		var chart_div = "chart_div";
		var timespan = (7*24)+1;
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
	    google.charts.setOnLoadCallback(drawBitcoinPriceChart);
	function drawBitcoinPriceChart() 
	{     
	      var data = new google.visualization.DataTable();
	      
	      // Set the correct scale for the horizontal axis if time_unit is in day
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
		   data.addRow([i,
		   parseFloat(json_data.btcchina[0][i]), 
		   parseFloat(json_data.btce[0][i]), 
		   parseFloat(json_data.bitfinex[0][i]), 
		   parseFloat(json_data.bitstamp[0][i]), 
		   parseFloat(json_data.coinbase[0][i]), 
		   parseFloat(json_data.huobi[0][i]), 
		   parseFloat(json_data.kraken[0][i]), 
		   parseFloat(json_data.okcoin[0][i])
		  ]);
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
		
		var zero_count = 0;
		
		for(i = 0; i < min_array.length; i++)
			{
				// If all exchanges have the same price for both min and max, we know that their true min price is 0
				if(min_array[i] == max_array[i])
				   {
				   	zero_count++;
				   }
			}
		      	
		      	// If all exchanges have a true min price of 0, set min price to 0.
		      	// The reason this happens is because normally we ignore the 0 prices and set them to the max price, so the window
		     	// doesn't get messed up. But, if this is true for all of them, that means we hit the end of the database's data.
		      	// That means the min really is 0.
		      	if(zero_count == 8)
			{
				min_price = 0;
			}
		// Give a little wiggle room
		max_price = max_price * 1.05;
		min_price = min_price * 0.95;
		
		var height = screen.availHeight*0.75;
		var options = 
		{
			
			chartArea:{width:'85%',height:'85%'},
			axisTitlesPosition: 'in',
			crosshair: { trigger: 'both' },
			'height':height,
		  hAxis: 
		  {
		      //title: 'Time (Hours' + time_unit + ')',
		      title: 'Time (Hours)',
		      direction: '-1',
		      gridlines: 
		      {
			      color: 'transparent',
			  count: (timespan)
		      },
		      viewWindowMode: 'explicit',
		      viewWindow: 
		      {
			  min: 0,
			  max: (timespan - 1)
		      },
		      maxValue: 24,
		      format: 0
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
			
			    timespan = chart_settings.timespan.value;
			    
			if(time_unit == "Days")
			{
				if(timespan > 365){
					timespan = 365;
				}
			    timespan = timespan * 24;
			}
			    else if(timespan > (365*24) ) {
				    timespan = (365*24);
			    }
			    			
			timespan++; // Because timespan = timespan + 1; would instead multiply timespan by 10, for some unknown reason
			
			drawBitcoinPriceChart();
		    }
