<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Password Change Results</title>
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
            
            <?php
                authenticate_user(100);
            ?>
		
		<div class="row center">
			<div class="col-3 empty"></div>
			<div class="col-6">
				<div class="object shadow">
				<center><h1>Password Change Results</h1></center>
				<p>
					<?php
                                    $old_password = trim(htmlspecialchars($_POST["old_password"]));
                                    $new_password = trim(htmlspecialchars($_POST["new_password"]));
                                    $new_password_repeat = trim(htmlspecialchars($_POST["new_password_repeat"]));
                                    
                                    // This case should never happen because the cookie is checked
                                    // on page load, and is deleted if it has been tampered with
                                    if(!$cookie_handler->get_validity())
                                    {
                                        print "Error: Invalid cookie. The offending cookie has been deleted. Please log in again.";
                                        $cookie_handler->delete_cookie($cookie_name);
                                        clear_session($uuid);
                                    }
                                    else if($new_password != $new_password_repeat)
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
                                    else
                                    {
                                        $uuid = $user_cookie->get_uuid();
                                        $results = get_user_data($uuid);
                                        
                                        $database_password = $results[2];
                                        $salt = $results[3];
                                        $email = $results[7];
                                        
                                        // Validate that the supplied password is correct
                                        $hashed_password = hash("sha512", $old_password . $salt);
                                        
                                        if(($database_password == $hashed_password))
                                        {
                                            // New password
                                            $hashed_new_password = hash("sha512", $new_password . $salt);
                                            
                                            set_new_hashed_password($uuid, $hashed_new_password);
                                            
                                            $user_id_num = $results[9];
                                            
                                            send_password_validation_email($user_id_num, $uuid, $email, $hashed_new_password);
                                            
                                            print "Password update pending! An email has been sent to $email. Please check your email to confirm the new password. In the meantime, you can still log in with your old password.";
                                        }
                                        else
                                        {
                                            print "Error: Invalid password. Press the back button to try again.";
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
