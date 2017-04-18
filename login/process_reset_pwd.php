<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Password Reset Results</title>
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
                    $session_id = get_session($user_cookie->get_uuid());
                    $cookie_handler->validate_cookie($user_cookie, $session_id);
                }            
            ?>
		
		<div class="row center">
			<div class="col-3 empty"></div>
			<div class="col-6">
				<div class="object shadow">
				<center><h1>Password Reset Results</h1></center>
				<p>
					<?php
                                    $new_password = trim(htmlspecialchars($_POST["new_password"]));
                                    $new_password_repeat = trim(htmlspecialchars($_POST["new_password_repeat"]));
                                    $post_email = trim(htmlspecialchars($_POST["email"]));
                                    
                                    $user_id_num = get_user_id_num_by_email($post_email);
                                    
                                    if($new_password != $new_password_repeat)
                                    {
                                        print "Error: New passwords do not match. Press the back button to try again.";
                                    }
                                    else if(empty($new_password))
                                    {
                                        print "Password cannot be empty! Please press the back button to try again.";
                                    }
                                    else if(strlen($new_password) < 6)
                                    {
                                        print "Your password can not be less than 6 characters long. Please press back and try again.";
                                    }
                                    else if(stristr($post_email, "@") === false)
                                    {
                                        print "Malformed email address. Press the back button to try again.";
                                    }
                                    else
                                    {
                                        
                                        // The function "get_user_id_num_by_email" returns [Unknown] if the email address does not match anything in the database
                                        if($user_id_num != "[unknown]")
                                        {
                                            $uuid = get_uuid_by_id_num($user_id_num);
                                            $user_data = get_user_data($uuid);
                                            $salt = $user_data[3];
                                            
                                            // New password
                                            $hashed_new_password = hash("sha512", $new_password . $salt);
                                            
                                            set_new_hashed_password($uuid, $hashed_new_password);
                                            
                                            send_password_validation_email($user_id_num, $uuid, $post_email, $hashed_new_password, true);
                                            
                                            print "Password update pending! An email has been sent to $post_email. Please check your email to confirm the new password. In the meantime, you can still log in with your old password.";
                                        }
                                        else
                                        {
                                            print "Error: Invalid email. Press the back button to try again.";
                                        }
                                    }
                                ?>
				</p>
				</div>
			</div>
			<div class="col-3 empty">	</div>
		</div>
	</body>
	
</html>
