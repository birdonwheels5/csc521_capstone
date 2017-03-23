<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Reset Password</title>
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
                    $session_id = get_session($user_cookie->get_uuid());
                    $cookie_handler->validate_cookie($user_cookie, $session_id);
                }
                print_header($cookie_handler, $cookie_name);
            
            ?>
	<div class="row center">
		<div class="empty col-4">
		</div>
		<div class="col-4">
    	<form action="process_reset_pwd.php" name="reset_pwd" method="post" class="object shadow" onkeyup="check_form()">
        	<input type="password" name="new_password" placeholder="New Password" required>
        	<input type="password" name="new_password_repeat" placeholder="Repeat New Password" required>
			<input type="email" name="email" placeholder="Email" required>
			<input type="submit" name="submit" value="Reset Password" required>
			<div class="small"><a href="./login/reset_pwd.php">Reset your password</a>, <a href="login.php">Login</a> or <a href="view.php">Continue as Guest</a></div>
		</form>
		</div>
		<div class="col-4 empty">
		</div>
	</div>
	</body>
	
</html>
