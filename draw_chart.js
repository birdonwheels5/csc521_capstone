
    google.charts.load('current', {packages: ['corechart', 'line']});
    google.charts.setOnLoadCallback(drawTrendlines);

/* Draws a bitcoin chart based on the given parameters:
 * (String) chart_div: The div to write the chart to.
 * (Int) timespan:     The number of hours of price data to display
 * (String) time_unit: "Days" or "Hours"
 */
function drawTrendlines(var chart_div, var timespan, var time_unit) 
{     
      // Set the correct scale for the horizontal axis if time_unit is in days
      var h_scale = timespan;
      
      if(time_unit == "Days")
      {
          h_scale = (timespan / 24);
      }
      else
      {
          // So the hours will display correctly
          h_scale = h_scale - 1;
      }
      
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
        var min_price = Math.min(Math, min_array);
        var max_price = Math.max(Math, max_array);

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
			maxLines: '8'
		    },
		    colors: ['black', 'red', 'blue', 'green', 'orange', 'purple', 'yellow', 'gray']
		};
			chart.draw(data, options);
		});
	    }
}
