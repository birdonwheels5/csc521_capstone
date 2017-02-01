<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Create New Account</title>
		<link rel="stylesheet" type="text/css" href="../styles.css" title="Default Styles" media="screen"/>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans" title="Font Styles"/>
		<?php include "CookieHandler.php";
              include "../func/login.php"; ?>
        <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	</head>
	
	<body>
        <script>
            
            function check_username()
            {
                var form = document.forms.register;
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
                form.username.value = username.replace( /\s\s+/g, ' ' );
                
                // Ajax http request
                var req = new XMLHttpRequest();
                req.open('GET', 'http://weblab.salemstate.edu/~S0276910/CSC435/login-project/check_dupe_user.php?u=' + username, true);
                req.onreadystatechange = function()
                {
                    if(req.readyState)
                    {
                        if(req.status != 200)
                        {
                            username_status.innerHTML = 'htmlstatus' + req.status;
                        }
                        else
                        {
                            username_status.innerHTML = req.responseText;
                        }
                    }
                };
                
                req.send();
                
                // Jquery, because Ajax gives a 500 error
                /*$.get( "http://weblab.salemstate.edu/~S0276910/CSC435/login-project/check_dupe_user.php?u=" + username, function( data ) {
                    username_status.innerHTML = data;
                });*/
                
                // Jquery doesn't work either
                
                return true;
                
            }
            
            function check_password()
            {
                var form = document.forms.register;
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
            
            function check_email()
            {
                var form = document.forms.register;
                var email = form.email.value;
                var status = document.getElementById("email_status");
                
                // Regex for first part of email:
                // /[a-zA-Z]|\d|\.|[#_~!$&()*+,;=:\-]/
                
                // Regex for second part of email:
                // /[a-zA-Z0-9]|\d|\.?\w|\-/
                
                // We can't continue if the email is improperly formed.
                if(email.indexOf("@") == -1)
                {
                    status.innerHTML = '<font color="red">Email address must contain exactly 1 @.</font>';
                    return false;
                }
                
                var num_symbols = email.match(/@/g).length;
                
                if(email.length == 0 || email === '')
                {
                    status.innerHTML = '<font color="red">Email cannot be empty.</font>';
                    return false;
                }
                // Check how many @'s we have in the email string. Because we can't reliably split the string if we have multiple @'s.
                else if(num_symbols > 1)
                {
                    status.innerHTML = '<font color="red">Email address must contain exactly 1 @.</font>';
                    return false;
                }
                else
                {
                    status.innerHTML = '';
                }
                
                var split_email = email.split("@");
                
                var first = split_email[0];
                var second = split_email[1];
                var pattern_first = /([a-zA-Z0-9\.#_~!$&()*+,;=:\-])*$/;
                var pattern_second = /([a-zA-Z0-9]|\d|\.?\w|\-)*$/;
                var result1 = pattern_first.exec(first);
                var result2 = pattern_second.exec(second);
                
                // Check first half
                if(split_email[0].length < 1)
                {
                    status.innerHTML = '<font color="red">First half of email address must be at least one character.</font>';
                    return false;
                }
                else if(split_email[0].length > 64)
                {
                    status.innerHTML = '<font color="red">First half of email address must be less than 64 characters.</font>';
                    return false;
                }
                else if(result1.index > 0)
                {
                    status.innerHTML = '<font color="red">First half of email address contains illegal characters!</font>';
                    return false;
                }
                // Check second half
                else if((split_email[0].length + split_email[1].length) > 254)
                {
                    status.innerHTML = '<font color="red">Email address too long!</font>';
                    return false;
                }
                else if(split_email[1].length < 3)
                {
                    status.innerHTML = '<font color="red">Second half of email address must be at least three characters.</font>';
                    return false;
                }
                else if(split_email[1].indexOf(".") < 0)
                {
                    status.innerHTML = '<font color="red">Second half of email address must contain at least one period.</font>';
                    return false;
                }
                else if(result2.index > 0)
                {
                    status.innerHTML = '<font color="red">Second half of email address contains illegal characters!</font>';
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
                    return false;
                }
                
                return true;
            }
            
            function submit_form()
            {
                var form = document.forms.register;
                
                if(check_form() === true)
                {
                    form.submit();
                }
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
			
			<article>
				<p>
					<!-- <center><img src="logo_big.png"></center> Insert Main Logo here -->
					
					<hr/>
					<center><h1>Create a new Account</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
								<form name="register" method="post" action="process_createuser.php" onkeyup="check_form()">
                                    <center><table>
                                        <tr>
                                            <td>
                                                Username: 
                                            </td>
                                            <td>
                                                <input type="text" name="username" size="10">
                                                <span id="username_status"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Password: 
                                            </td>
                                            <td>
                                                <input type="password" name="password" size="10">
                                                <span id="password_status"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Email: 
                                            </td>
                                            <td>
                                                <input type="text" name="email" size="10">
                                                <span id="email_status"></span>
                                            </td>
                                        </tr>
                                    </table></center>
								    <center><input type="button" onclick="submit_form()" value="Create Account"></center>
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
