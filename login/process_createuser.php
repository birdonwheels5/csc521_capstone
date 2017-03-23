<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Account Creation Results</title>
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
                print_header($cookie_handler, $cookie_name);
            
            ?>
			
					<!-- <center><img src="logo_big.png"></center> Insert Main Logo here -->
		<div class="row center">
			<div class="col-3 empty"></div>
			<div class="col-6 object shadow">
				<center><h1>Account Creation Results</h1></center>
				<p>
								<?php
                                    $username = trim(htmlspecialchars($_POST["username"]));
                                    $password = trim(htmlspecialchars($_POST["password"]));
                                    $email = trim(htmlspecialchars($_POST["email"]));
                                    
                                    $authority_level = 100; // Default authority level
                                    // See if the username contains only valid characters
                                    $match_result = preg_match('/^[A-Za-z0-9\d_]*$/i', $_POST["username"]);
                                    
                                    // Check to see if the user is already in the database.
                                    // The function will return an array if they are.
                                    // Error messages
                                    $uuid = hash("sha256", $username);
                                    if(is_array(get_user_data($uuid)))
                                    {
                                        print "Username already exists! Please press the back button to try another username.";
                                    }
                                    else if(empty($password))
                                    {
                                        print "Password cannot be empty! Please press the back button to try again.";
                                    }
                                    else if(strlen($password) < 6)
                                    {
                                        print "Your password can not be less than 6 characters long. Please press back and try again.";
                                    }
                                    else if(strlen($username) > 25)
                                    {
                                        print "Username can not exceed 25 characters long! Please press back and try again.";
                                    }
                                    else if(strlen($username) < 3)
                                    {
                                        print "Your username can not be less than 3 characters long. Please press back and try again.";
                                    }
                                    else if($match_result === false)
                                    {
                                        print "There was an error with your username. Please try a different one.";
                                    }
                                    else if($match_result === 0)
                                    {
                                        print "Your username contains illegal characters. Please remove them and try again.";
                                    }
                                    else if(!is_email_unique($email))
                                    {
                                        print "A user with the email address " . $email . " already exists! Please use a different email address.";
                                    }
                                    else if(strlen($email) > 255)
                                    {
                                        print "Email addresses can not exceed 255 characters long! Please press back and try again.";
                                    }
                                    else if(stristr($email, "@") === false)
                                    {
                                        print "Malformed email address. Press the back button to try again.";
                                    }
                                    else
                                    {
                                        $result = create_user($username, $password, $email, $authority_level);
                                        if($result == false)
                                        {
                                            print "Error creating account!";
                                        }
                                        else
                                        {
                                            $user_data = get_user_data($uuid);
                                            $user_id_num = $user_data[9];
                                            
                                            send_validation_email($user_id_num, $uuid, $email);
                                            
                                            print "Account created successfully!";
                                            print "<br/><br/>";
                                            print "You must validate your email address before you can <a href =\"./login.php\">log in</a>. An email has been sent to $email.";
                                        }
                                    }
                                    
                                    
                                ?>
				</p>
			</div>
			<div class="col-3 empty">	</div>
		</div>
	</body>
	
</html>
