<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Index</title>
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
                    
                    update_last_login($uuid);
                }
                print_header($cookie_handler, $cookie_name);
            ?>
		
			
			
			<article style="color:#FFFFFF;">
				<p>
					<!-- <center><img src="logo_big.png"></center> Insert Main Logo here -->
					
					<hr/>
					<center><h1>Site Index</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
								Welcome to our project!
							</p>
						</div>
						
						<div class="box">
							<p>
								<center>
                                    <h3>Info Box</h3>
								    
								    <?php
								    	// Test code here
                                    
                                       if($cookie_handler->get_exists())
                                        {
                                            print "Hello, " . $user_data[0] . "!";
                                            print "<br/><br/>";
                                        }
                                        
                                        print "User cookie: <br/><br/>";
                                        
                                        if($cookie_handler->get_exists())
                                        {
                                            print $user_cookie->get_uuid() . "|" . $user_cookie->get_session_id() . "|" . $user_cookie->get_hmac_hash()  . "|" . $user_cookie->get_expiration();
                                            print "<br/><br/>";
                                            print "Key: \"uuid|session id|MAC|cookie expiration time\"";
                                        }
                                        else
                                        {
                                            print $cookie_name . " cookie was not found on the client's computer!";
                                        }
                                    ?>
								</center>
							</p>
						</div>
                        <div class="box">
                            <center>
                                <h3>Check out our new bulletin board!</h3>
                                <p>
                                    <a href ="bulletin.php">Bulletin Board</a>
                                </p>
                            </center>
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
