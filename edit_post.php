<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Edit Post</title>
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
					<center><h1>Edit Post</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
                                <center>
                                    <?php 
                                        
                                        $post_id = $_POST["post_id"];
                                        
                                        $post_data = get_post_by_id($post_id);
                                        
                                        if(empty($post_id))
                                        {
                                            $post_id = -1;
                                        }
                                        
                                    ?>
                                    
                                    <hr/>
                                    <h2>Editing: <?php print $post_data[1] ?></h2>
                                    <hr/>
                                        <form method="post" action="process_post_edit.php">
                                            Subject: 
                                            <br/>
                                            <textarea rows=1 cols=75 name="subject"><?php print $post_data[1] ?></textarea>
                                            <br/>
                                            <br/>
                                            Post Contents:
                                            <br/>
                                            <textarea rows=15 cols=75 name="post_contents"><?php print $post_data[2] ?></textarea>
                                            <center><input type="submit" name="submit" value="Submit"></center>
                                            <input type="hidden" name="post_id" value="<?php print $post_id ?>">
                                        </form>
                                </center>
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
