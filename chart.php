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
	<script src="chart.js"></script>
	    
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
<body onresize="update_chart()">
	<div class="row"></div>
	<div class="col-12">
		<div class="object shadow">

			<div id="chart_div" class="chart"></div>

			<form action="javascript:void(0);" onsubmit="update_chart()" name="chart_settings" method="post" onchange="update_chart()" onclick="update_chart()" onkeyup="update_chart()" class="row chart">
				<input class="chart" type="number" name="timespan" min="1" max="365" value="7">
			    <input class="chart" type="radio" name="unit" value="Hours"> Hours
			    <input class="chart" type="radio" name="unit" value="Days" checked> Days
			</form>

		</div>
	</div>
</body>
