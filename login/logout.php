<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Logout</title>
	    <link rel='stylesheet' type="text/css" href="../main.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php include "CookieHandler.php";
              include "../func/login.php"; ?>
	</head>
	
	<body>
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
                }            
		
                print_header($cookie_handler, $cookie_name);
            ?>
		
		<div class="row center">
			<div class="col-3 empty"></div>
			<div class="col-6">
				<div class="object shadow">
					<center><h1>Logout Error</h1></center>
					<p>
					<?php
                                    
                                    // Perform logout here
                                    if($cookie_handler->get_exists())
                                    {
                                        $cookie_handler->delete_cookie($cookie_name);
                                        clear_session($uuid);
                                        header("location:./../index.php");
                                    }
                                    else
                                    {
                                        print "Unable to log out because user is not logged in!";
                                    }
					?>
					</p>
				</div>
			</div>
			<div class="col-3 empty">	</div>
		</div>
		
	</body>
	
</html>
