<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Verification Results</title>
		<link rel="stylesheet" type="text/css" href="../styles.css" title="Default Styles" media="screen"/>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans" title="Font Styles"/>
		<?php include "CookieHandler.php"; 
              include "../func/login.php"; ?>
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
                    
                    // So we can get the current user's user_id_num to use for the "from" field of the message
                    $user_data = get_user_data($uuid);
                }
                print_header($cookie_handler, $cookie_name);
            
            ?>
			
			<article>
				<p>
					<!-- <center><img src="logo_big.png"></center> Insert Main Logo here -->
					
					<hr/>
					<center><h1>Verification Results</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
                                <?php
                                    
                                    $user_id_num = $user_data[9];
                                    $new_hashed_password = $user_data[10];
                                    
                                    if(!empty($_GET["etoken"]))
                                    {
                                        
                                        $match_result = preg_match('/^[A-Za-z0-9\d]*$/i', $_GET["etoken"]);
                                        $id = intval($_GET["id"]);
                                        $new_user_uuid = get_uuid_by_id_num($id);
                                        $etoken = $_GET["etoken"];
                                        
                                        if(!is_integer($id))
                                        {
                                            print "ID must be an integer!";
                                        }
                                        else if($id < 0)
                                        {
                                            print "ID can not be less than 0!";
                                        }
                                        else if($match_result === false)
                                        {
                                            print "There was an error with the token.";
                                        }
                                        else if($match_result === 0)
                                        {
                                            print "The token contains illegal characters.";
                                        }
                                        else if(strlen($etoken) != 40)
                                        {
                                            print "The token is not the correct length.";
                                        }
                                        else if($new_user_uuid == "[unknown]")
                                        {
                                            print "Unknown ID.";
                                        }
                                        // Check if supplied token is valid
                                        else if(validate_email_address($id, $new_user_uuid, $etoken))
                                        {
                                            set_validate_field($new_user_uuid);
                                            
                                            print "Success! You can now <a href =\"./login/login.php\">log in</a> with your new account!";
                                        }
                                        else
                                        {
                                            print "Invalid Token!";
                                        }
                                    }
                                    else if(!empty($_GET["ptoken"]))
                                    {
                                        $match_result = preg_match('/^[A-Za-z0-9\d]*$/i', $_GET["ptoken"]);
                                        $ptoken = trim(htmlspecialchars($_GET["ptoken"]));
                                        
                                        if($match_result === false)
                                        {
                                            print "There was an error with the token.";
                                        }
                                        else if($match_result === 0)
                                        {
                                            print "The token contains illegal characters.";
                                        }
                                        else if(strlen($ptoken) != 40)
                                        {
                                            print "The token is not the correct length.";
                                        }
                                        // Check if the token is correct
                                        else if(validate_new_password($user_id_num, $uuid, $new_hashed_password, $ptoken))
                                        {
                                            update_user_password($uuid);
                                            
                                            print "Success! You can now <a href =\"./login/login.php\">log in</a> with your new password! Your old password will no longer work.";
                                            print "<br/><br/>";
                                            print "You have been logged out.";
                                            $cookie_handler->delete_cookie($cookie_name);
                                            clear_session($uuid);
                                        }
                                        else
                                        {
                                            print "Invalid Token!";
                                        }
                                    }
                                    if(!empty($_GET["rptoken"]))
                                    {
                                        
                                        $match_result = preg_match('/^[A-Za-z0-9\d]*$/i', $_GET["rptoken"]);
                                        $id = intval($_GET["id"]);
                                        $uuid = get_uuid_by_id_num($id);
                                        $user_data = get_user_data($uuid);
                                        $new_hashed_password = $user_data[10];
                                        $rptoken = $_GET["rptoken"];
                                        
                                        if(!is_integer($id))
                                        {
                                            print "ID must be an integer!";
                                        }
                                        else if($id < 0)
                                        {
                                            print "ID can not be less than 0!";
                                        }
                                        else if($match_result === false)
                                        {
                                            print "There was an error with the token.";
                                        }
                                        else if($match_result === 0)
                                        {
                                            print "The token contains illegal characters.";
                                        }
                                        else if(strlen($rptoken) != 40)
                                        {
                                            print "The token is not the correct length.";
                                        }
                                        else if($uuid == "[unknown]")
                                        {
                                            print "Unknown ID.";
                                        }
                                        // Check if supplied token is valid
                                        else if(validate_new_password($id, $uuid, $new_hashed_password, $rptoken))
                                        {
                                            update_user_password($uuid);
                                            
                                            print "Success! You can now <a href =\"./login/login.php\">log in</a> with your new password! Your old password will no longer work.";
                                            print "<br/><br/>";
                                            print "You have been logged out if you were logged in.";
                                            $cookie_handler->delete_cookie($cookie_name);
                                            clear_session($uuid);
                                        }
                                        else
                                        {
                                            print "Invalid Token!";
                                        }
                                    }
                                    else
                                    {
                                        print "Error: No token supplied.";
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
				2017 Bitquote.
			</footer>
		</div>
	</body>
	
</html>
