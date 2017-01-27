<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Bulletin Board</title>
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
                    $session_id = get_session($user_cookie->get_uuid());
                    $cookie_handler->validate_cookie($user_cookie, $session_id);
                }
                print_header($cookie_handler, $cookie_name);
            
            ?>
			
			<article style="color:#FFFFFF;">
				<p>
					<!-- <center><img src="logo_big.png"></center> Insert Main Logo here -->
					
					<hr/>
					<center><h1>Post Submission Error</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
								<?php
                                    $subject = $_POST["subject"];
                                    $content = $_POST["post_contents"];
                                    
                                    if(empty($subject))
                                    {
                                        print "The post subject cannot be empty! Please press the back button to try again.";
                                    }
                                    else if(strlen($subject) > 50)
                                    {
                                        print "The subject can not exceed 50 characters long! Please press back and try again.";
                                    }
                                    else if(strlen($subject) < 3)
                                    {
                                        print "The subject can not be less than 3 characters long. Please press back and try again.";
                                    }
                                    else if(empty($content))
                                    {
                                        print "The post content cannot be empty! Please press the back button to try again.";
                                    }
                                    else
                                    {
                                        $result = submit_post($cookie_handler, $cookie_name, $subject, $content);
                                        
                                        if($result == 0)
                                        {
                                            // Post successful
                                            header("location:bulletin.php");
                                        }
                                    }
                                    
                                   
                                    
                                ?>
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
