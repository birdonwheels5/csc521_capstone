<!DOCTYPE html>
<head>
	<meta charset="ISO-8859-1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitQuote</title>
    <link rel='stylesheet' type="text/css" href="../main.css">
	<?php include "CookieHandler.php";
          include "../func/login.php"; ?>
</head>
<body class="color-0">
	
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
		<div class="empty col-4">
		</div>
		<div class="col-4">
    	<form action="process_login.php" name="login" method="post" class="object shadow" onkeyup="check_form()">
        	<input type="text" name="username" placeholder="Username" required>
        	<input type="password" name="password" placeholder="Password" required>
			<input type="submit" name="submit" value="Login" required>
			<div class="small"><a href="reset_pwd.php">Reset your password</a>, <a href="createuser.php">Register</a> or <a href="../index.php">Continue as Guest</a></div>
		</form>
		</div>
		<div class="col-4 empty">
		</div>
	</div>
</body>
