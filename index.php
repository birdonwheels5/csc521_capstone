<!DOCTYPE html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel='stylesheet' type="text/css" href="main.css">
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
	</script>
</head>

<body class="color-0">
	
    <div class="row">
		
		<div class="empty col-2"> <!-- Left Margin -->
		</div>
		
		<div class="col-4"> <!-- Column 1 -->
			<div class="object shadow" id="BtcPrice">
				<script type="text/javascript">
					$('#BtcPrice').load('load_exchanges.php');
                </script>
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
		
	</div>
	
</body>
