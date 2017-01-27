<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>My Posts</title>
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
					<center><h1>My Posts</h1></center>
					<hr/>
					<p>
						<div class="box">
							<p>
                                <center>
                                    <?php 
                                        $num_posts = get_post_count($uuid);
                                        $post_threshhold = 10;
                                        
                                        print "Viewing all posts (" . $num_posts . ") created by <b>" . $user_data[0] . "</b>. Click on the Edit button to edit.<br/><br/>";
                                        
                                        if($num_posts == 0)
                                        {
                                            print "There doesn't seem to be anything here";
                                        }
                                        else
                                        {
                                            display_posts($uuid);
                                            
                                            // Print out a link to return to the top of the page if there are more than x posts
                                            if($num_posts > $post_threshhold)
                                            {
                                                print '<a href ="bulletin.php#top">Top</a>';
                                            }
                                        }
                                         
                                    ?>
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
