<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Login</title>
		<link rel="stylesheet" type="text/css" href="styles.css" title="Default Styles" media="screen"/>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans" title="Font Styles"/>
		<?php include "CookieHandler.php";
               include "helper_functions.php"; ?>
	</head>
	
	<body link="#E2E2E2" vlink="#ADABAB">
        <script>
            
            function check_username()
            {
                var form = document.forms.login;
                var username = form.username.value;
                var status = document.getElementById("username_status");
                
                // Regex on the username
                var pattern = /([a-zA-Z0-9\s])*$/;
                var result = pattern.exec(username);
                
                if(username.length == 0 || username === '')
                {
                    status.innerHTML = '<font color="red">Username cannot be empty.</font>';
                    return false;
                }
                else if(username.length < 3)
                {
                    status.innerHTML = '<font color="red">Username too short.</font>';
                    return false;
                }
                else if(username.length > 25)
                {
                    status.innerHTML = '<font color="red">Username too long.</font>';
                    return false;
                }
                else if(result.index > 0)
                {
                    status.innerHTML = '<font color="red">Username contains illegal characters!</font>';
                    return false;
                }
                else
                {
                    status.innerHTML = '';
                }
                
                form.username.value = username.trim();
                form.username.value = username.replace( /\s\s+/g, ' ' )
                
                return true;
                
            }
            
            function check_password()
            {
                var form = document.forms.login;
                var password = form.password.value;
                var status = document.getElementById("password_status");
                
                if(password.length == 0 || password === '')
                {
                    status.innerHTML = '<font color="red">Password cannot be empty.</font>';
                    return false;
                }
                else if(password.length < 6)
                {
                    status.innerHTML = '<font color="red">Password too short.</font>';
                    return false;
                }
                else
                {
                    status.innerHTML = '';
                }
                
                return true;
            }
            
            function check_form()
            {
                user_field = check_username();
                
                pass_field = check_password();
                
                email_field = check_email();
                
                if(user_field == false || pass_field == false || email_field == false)
                {
                    return;
                }
                
                // Submit Form
                
                return;
            }
            
        </script>
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
					<center><h1>User Login</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
								<form name="login" method="post" action="process_login.php" onkeyup="check_form()">
                                    <center><table>
                                        <tr>
                                            <td>
                                                Username: 
                                            </td>
                                            <td>
                                                <input type="text" name="username" size="10">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Password: 
                                            </td>
                                            <td>
                                                <input type="password" name="password" size="10">
                                            </td>
                                        </tr>
                                    </table></center>
								    <center><input type="submit" name="submit" value="Login"></center>
								</form>
							</p>
						</div>
                        
                        <div class="box">
                            <p>
                                Forgot your password? <a href="reset_pwd.php">Reset your password</a>.
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
