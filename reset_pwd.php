<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Reset Password</title>
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
					<center><h1>Reset Password</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
								<form method="post" action="process_reset_pwd.php">
                                    <center><table>
                                        <tr>
                                            <td>
                                                Forgot password? 
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                New Password: 
                                            </td>
                                            <td>
                                                <input type="password" name="new_password" size="10">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Repeat New Password: 
                                            </td>
                                            <td>
                                                <input type="password" name="new_password_repeat" size="10">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Email: 
                                            </td>
                                            <td>
                                                <input type="text" name="email" size="10">
                                            </td>
                                        </tr>
                                    </table></center>
								    <center><input type="submit" name="submit" value="Change Password"></center>
								</form>
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
