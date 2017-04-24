void function draw_chart(var chart_div, var timespan)
{
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
		    backgroundColor: "#FFFAFA",
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
}
