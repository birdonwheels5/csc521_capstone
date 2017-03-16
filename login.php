<!DOCTYPE html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel='stylesheet' type="text/css" href="main.css">
	<?php include "CookieHandler.php";
          include "../func/login.php"; ?>
</head>
<body class="color-0">
	

	
    <div class="row center">
		<div class="empty col-4">
		</div>
		<div class="col-4">
    	<form action="process_login.php" name="login" method="post" class="object shadow" onkeyup="check_form()">
        	<input type="text" name="username" placeholder="Username" required>
        	<input type="password" name="password" placeholder="Password" required>
			<input type="submit" name="submit" value="Login" required>
			<div class="small"><a href="./login/reset_pwd.php">Reset your password</a>, <a href="register.php">Register</a> or <a href="view.php">Continue as Guest</a></div>
		</form>
		</div>
		<div class="col-4 empty">
		</div>
	</div>
</body>