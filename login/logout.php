<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Logout</title>
		<link rel="stylesheet" type="text/css" href="../styles.css" title="Default Styles" media="screen"/>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans" title="Font Styles"/>
        <?php include "CookieHandler.php";
              include "login_functions.php"; ?>
	</head>
	
	<body>
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
                }
                print_header($cookie_handler, $cookie_name);
            
            ?>
			
			<article>
				<p>
					<!-- <center><img src="logo_big.png"></center> Insert Main Logo here -->
					
					<hr/>
					<center><h1>Logout Error</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
                                <?php
                                    
                                    // Perform logout here
                                    if($cookie_handler->get_exists())
                                    {
                                        $cookie_handler->delete_cookie($cookie_name);
                                        clear_session($uuid);
                                        header("location:/index.php");
                                    }
                                    else
                                    {
                                        print "Unable to log out because user is not logged in!";
                                    }
                                ?>
							</p>
						</div>
								
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

