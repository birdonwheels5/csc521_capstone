<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Bulletin Board</title>
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
                    $session_id = get_session($user_cookie->get_uuid());
                    $cookie_handler->validate_cookie($user_cookie, $session_id);
                }
                print_header($cookie_handler, $cookie_name);
            ?>
		
			
			
			<article style="color:#FFFFFF;">
				<p>
					<!-- <center><img src="logo_big.png"></center> Insert Main Logo here -->
					
					<hr/>
					<center><h1>Bulletin Board</h1></center>
					<hr/>
					<p>
						<div class="box" id="top">
							<p>
								<center>
                                    An aggregation of random posts by random users.
                                    <br/><br/>
                                    You can create your own post by <a href="bulletin.php#create">clicking here</a>.
                                    <h2>Latest Posts</h2>
                                    <?php 
                                        $num_posts = get_post_count();
                                        $post_threshhold = 10;
                                        
                                        if($num_posts == 0)
                                        {
                                            print "There doesn't seem to be anything here";
                                        }
                                        else
                                        {
                                            display_posts();
                                            
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
                        
                        <?php
                            // print out latest 10 posts here
                        ?>
                        
                        <div class="box" id="create">
							<p>
                                <center>
                                    <hr/>
                                    <h2>Create a New Post</h2>
                                    <hr/>
                                        <form method="post" action="process_bulletin_post.php">
                                            Subject: 
                                            <br/>
                                            <textarea rows=1 cols=75 name="subject"></textarea>
                                            <br/>
                                            <br/>
                                            Post Contents:
                                            <br/>
                                            <textarea rows=15 cols=75 name="post_contents"></textarea>
                                            <center><input type="submit" name="submit" value="Submit"></center>
                                        </form>
                                </center>
							</p>
						</div>
                        
                        <?php
                            
                        ?>
                        
                        
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
