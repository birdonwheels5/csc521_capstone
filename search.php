<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Search</title>
		<link rel="stylesheet" type="text/css" href="main.css" title="Default Styles" media="screen"/>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans" title="Font Styles"/>
		<?php include "login/CookieHandler.php";
              include "func/login.php"; ?>
        <style>
            footer
            {
                position: relative; 
            }
            
        </style>
	</head>
	
	<body>
		<div class="container">
            
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
            
            <?php 
                // Authenticate user
                authenticate_user(100);
            ?>
			
			<article>
				<div class="row center">
					
					<div class="col-4 empty"></div>
                    
                    
					<div class="col-4 search">
						
                        
                        
						<form action="process_search.php" name="search" method="post" onkeyup="" class="object shadow search">
							<p class="search">
								<h2>Database Search</h2>
                                <hr/>
                                <br/>
                                Search term: <input type="text" name="search_term">
								Data select:<br>
								Twitter <input type="checkbox" name="twitter" value="1">
								Reddit <input type="checkbox" name="reddit" value="1">
								Bitcointalk.org <input type="checkbox" name="forum" value="1">
                            	Search by Username <input type="checkbox" name="user" value="1">
								<input type="submit" name="submit_search" value="Search">
							</p>
						</form>
						
					</div>
					
					<div class="col-4 empty"></div>
				</div>	
			
			</article>
			
		</div>
	</body>
	
</html>
