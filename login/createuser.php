<!DOCTYPE html>
<head>
	<meta charset="ISO-8859-1">
    <title>BitQuote</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type="text/css" href="../main.css">
	<?php include "CookieHandler.php";
          include "../func/login.php"; ?>
	
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
                    
                    update_last_login($uuid);
                }
		
                print_header($cookie_handler, $cookie_name);
            ?>
</head>

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

<body class="color-0">
	<div class="row center">
		<div class="empty col-4">
		</div>
		<div class="col-4">
    	<form action="process_createuser.php" name="register" method="post" class="object shadow" onkeyup="check_form()">
        	<input type="text" name="username" placeholder="Username" required>
        	<input type="password" name="password" placeholder="Password" required>
			<input type="email" name="email" placeholder="Email" required>
			<input type="submit" name="submit" value="Create User" required>
			<div class="small"><a href="./login/reset_pwd.php">Reset your password</a>, <a href="login.php">Login</a> or <a href="view.php">Continue as Guest</a></div>
		</form>
		</div>
		<div class="col-4 empty">
		</div>
	</div>
</body>
