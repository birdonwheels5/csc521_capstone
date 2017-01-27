<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>View Message</title>
		<link rel="stylesheet" type="text/css" href="styles.css" title="Default Styles" media="screen"/>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans" title="Font Styles"/>
		<?php include "CookieHandler.php";
              include "helper_functions.php"; ?>
	</head>
	
	<body link="#E2E2E2" vlink="#ADABAB">
		<center><div class="container">
            
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
                }
                print_header($cookie_handler, $cookie_name);
            ?>
            
            <?php 
                // Authenticate user
                authenticate_user(100);
            ?>
		
			
			
			<article style="color:#FFFFFF;">
				<p>
					<!-- <center><img src="logo_big.png"></center> Insert Main Logo here -->
					
					<hr/>
					<center><h1>View Message</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
								<center>
                                    <?php
                                        $message_data = get_messages($user_data[1], $_POST["message_id"]);
                                    ?>
                                    <h3><?php print $message_data[3]; ?></h3>
								    
                                    <?php
                                        
                                        display_single_message($message_data);
                                        
                                    ?>
								    
								</center>
							</p>
						</div>

					</p>

				</p>
			
			
			</article>
			
			<div class="paddingBottom">
			</div>
			
			<footer>
				2016 Lizard Squad.
			</footer>
		</div>
	</body>
	
</html>
