<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>User Area</title>
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
					<div class="col-4">
						<div class="object shadow">
							<!-- <b>Search the Database</b> -->
							</div>
					</div>
					<div class="col-4">
						<?php
                            
                            $search_term = trim(htmlspecialchars($_POST["search_term"]));
                            $twitter_flag = $_POST["twitter"];
                            $reddit_flag = $_POST["reddit"];
                            $forum_flag = $_POST["forum"];
                            
                            $search_results = search_database_posts($search_term, $twitter_flag, $reddit_flag, $forum_flag);
                            
                            $twitter_search_results_length = count($search_results[0][0]); // We care about the length of the third (last array) in the package of arrays
                            
                            print "Twitter Results";
                            
                            if($twitter_search_results_length == 0)
                            {
                                print '<div class="row center">
                                                <div class="object shadow">
                                                    <p>';
                                                    
                                print "No results found for query '$search_term'.";
                                
                                print '</p>
                                                </div>
                                            </div>';
                            }
                            else
                            {
                                for($i = 0; $i < $twitter_search_results_length; $i++)
                                {
                                    $time_since_post = time_since_post($search_results[0][2][$i]);
                                                                    
                                    print '<div class="row center">
                                                <div class="object shadow">
                                                    <p>';
                                    
                                    print "Username: " . $search_results[0][0][$i] . " <br/><br/>\n
                                           Post: " . $search_results[0][1][$i] . " <br/><br/>\n
                                           Posted $time_since_post ago. <br/><br/>\n";
                                    
                                    print '</p>
                                                </div>
                                            </div>';
                                }
                            }
                            
                        ?>
					</div>
					<div class="col-4 empty">
					</div>
				</div>	
			
			</article>
			
		</div>
	</body>
	
</html>
