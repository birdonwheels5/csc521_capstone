<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Messages</title>
		<link rel="stylesheet" type="text/css" href="styles.css" title="Default Styles" media="screen"/>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans" title="Font Styles"/>
		<?php include "CookieHandler.php";
              include "helper_functions.php"; ?>
        <style>
            footer
                {
                    position: relative; 
                }
        </style>
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
					<center><h1>My Messages</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
								<center>
                                    <h3>Message List</h3>
								    
                                    <?php
                                        
                                        $messages = get_messages($user_data[1]);
                                        
                                        display_all_messages($messages);
                                        
                                    ?>
								    
								</center>
							</p>
						</div>
                        <div class="box">
                            <center>
                                <h3>Send a New Message</h3>
                                <p>
                                    <h4>Compose</h4>
                                    <form method="post" action="process_new_message.php">
                                    <center><table>
                                        <tr>
                                            <td>
                                                To: 
                                            </td>
                                        </tr>
                                                <?php
                                                    // Print out all users in checkboxes
                                                    
                                                    $user_data = get_user_data();
                                                    $num_users = count($user_data[0]);
                                                    
                                                    for($i = 0; $i < $num_users; $i++)
                                                    {
                                                        print "<tr><td>";
                                                        print '<input type=checkbox name="' . $user_data[0][$i] . '" value="' . $user_data[1][$i] . '" unchecked>' . $user_data[0][$i] . '</input>';
                                                        print "</td></tr>";
                                                    }
                                                    
                                                ?>
                                        </table>
                                        
                                        <table>
                                        <tr>
                                            <td>
                                                Subject: 
                                            </td>
                                            <td>
                                                <textarea rows=1 cols=75 name="subject"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Message: 
                                            </td>
                                            <td>
                                                <textarea rows=15 cols=75 name="message"></textarea>
                                            </td>
                                        </tr>
                                    </table></center>
								    <center><input type="submit" name="submit" value="Send"></center>
								</form>
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
