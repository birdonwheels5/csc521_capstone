<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Message Send Results</title>
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
                    
                    // So we can get the current user's user_id_num to use for the "from" field of the message
                    $user_data = get_user_data($uuid);
                }
                print_header($cookie_handler, $cookie_name);
            
            ?>
            
            <?php
                authenticate_user(100);
            ?>
			
			<article style="color:#FFFFFF;">
				<p>
					<!-- <center><img src="logo_big.png"></center> Insert Main Logo here -->
					
					<hr/>
					<center><h1>Message Send Error</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
                                <?php
                                    
                                    $users_array = array();
                                    
                                    foreach($_POST as $key => $value)
                                    {
                                        if($key == "subject" || $key == "message" || $key == "submit" || $key == "send")
                                        {
                                            break;
                                        }
                                        
                                        // Debug
                                        /*
                                        print $key . "<br/>\n";
                                        print $value;
                                        print "<br/>\n";
                                        print "<br/>\n";
                                        */
                                        
                                        $users_array[$value] = $value;
                                    }
                                    
                                    // This array contains the user ids of the users the message is for
                                    $users_array = array_values($users_array);
                                    
                                    $subject = $_POST["subject"];
                                    $message_text = $_POST["message"];
                                    
                                    if(empty($subject))
                                    {
                                        print "The subject cannot be empty! Please press the back button to try again.";
                                    }
                                    else if(strlen($subject) > 50)
                                    {
                                        print "The subject can not exceed 50 characters long! Please press back and try again.";
                                    }
                                    else if(strlen($subject) < 3)
                                    {
                                        print "The subject can not be less than 3 characters long. Please press back and try again.";
                                    }
                                    else if(empty($message_text))
                                    {
                                        print "The message cannot be empty! Please press the back button to try again.";
                                    }
                                    else
                                    {
                                        new_message($subject, $message_text, $user_data[9], $users_array);
                                        
                                        // Success
                                        print "Your message was sent successfully!";
                                        print "<br/>\n";
                                        print "You will be redirected to your messages in 3 seconds.";
                                        header('Refresh: 3; URL=my_messages.php');
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
