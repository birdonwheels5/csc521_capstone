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
				<div class="row">
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
                            
                            var_dump($search_results);
                            
                            
                            // Returns array in format:
                            // [0][1][x] = twitter results
                            // [1][[1][x] = reddit results
                            // [2][1][x] = forum results
                            // [x][0][x] is the user's name who made the post
                            function search_database_posts($search_term, $twitter_flag, $reddit_flag, $forum_flag)
                            {
                                $twitter_posts = array();
                                $reddit_posts = array();
                                $forum_posts = array();
                                
                                // Establish connection to the database
                                $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
                                
                                if (mysqli_connect_errno()) 
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                    $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
                                    log_to_file($log_message);
                                }
                                
                                $search_term = mysqli_real_escape_string($con, $search_term);
                                
                                $results = array();
                                
                                if($twitter_flag == 1)
                                {
                                    $query = "SELECT username, post_text FROM Twitter_Posts WHERE post_text LIKE '%$search_term%' OR username='$search_term'";
                                    
                                    $result = mysqli_query($con, $query);
                                    
                                    // Obtain the number of rows from the result of the query
                                    $num_rows = mysqli_num_rows($result);
                                            
                                    // Will be storing all the rows in here
                                    $array_of_rows = array();
                                                            
                                    // Get all the rows
                                    for($i = 0; $i < $num_rows; $i++)
                                    {
                                        $array_of_rows[$i] = mysqli_fetch_array($result);
                                    }
                                    $size_of_array_of_rows = $num_rows;
                                                            
                                    $usernames = array();
                                    $post_texts = array();
                                    
                                    // Get an array of all values for each field
                                    for($i = 0; $i < $size_of_array_of_rows; $i++)
                                    {
                                        $usernames[$i] = $array_of_rows[$i]["username"];
                                        $post_texts[$i] = $array_of_rows[$i]["post_text"];
                                    }

                                        
                                        // Package the data in an array
                                        $twitter_posts[0] = $usernames[$result];
                                        $twitter_posts[1] = $post_texts[$result];
                                }
                                    
                                if($reddit_flag == 1)
                                {
                                    $query = "SELECT OP, post_text FROM Reddit_Posts WHERE post_text LIKE '%$search_term%'";
                                    
                                    $result = mysqli_query($con, $query);
                                    
                                    // Obtain the number of rows from the result of the query
                                    $num_rows = mysqli_num_rows($result);
                                            
                                    // Will be storing all the rows in here
                                    $array_of_rows = array();
                                                            
                                    // Get all the rows
                                    for($i = 0; $i < $num_rows; $i++)
                                    {
                                        $array_of_rows[$i] = mysqli_fetch_array($result);
                                    }
                                    $size_of_array_of_rows = $num_rows;
                                                            
                                    $usernames = array();
                                    $post_texts = array();
                                    
                                    // Get an array of all values for each field
                                    for($i = 0; $i < $size_of_array_of_rows; $i++)
                                    {
                                        $usernames[$i] = $array_of_rows[$i]["OP"];
                                        $post_texts[$i] = $array_of_rows[$i]["post_text"];
                                    }

                                        
                                        // Package the data in an array
                                        $reddit_posts[0] = $usernames[$result];
                                        $reddit_posts[1] = $post_texts[$result];
                                }
                                
                                if($forum_flag == 1)
                                {
                                    $query = "SELECT username, post_text FROM Forum_Posts WHERE post_text LIKE '%$search_term%'";
                                    
                                    $result = mysqli_query($con, $query);
                                    
                                    // Obtain the number of rows from the result of the query
                                    $num_rows = mysqli_num_rows($result);
                                            
                                    // Will be storing all the rows in here
                                    $array_of_rows = array();
                                                            
                                    // Get all the rows
                                    for($i = 0; $i < $num_rows; $i++)
                                    {
                                        $array_of_rows[$i] = mysqli_fetch_array($result);
                                    }
                                    $size_of_array_of_rows = $num_rows;
                                                            
                                    $usernames = array();
                                    $post_texts = array();
                                    
                                    // Get an array of all values for each field
                                    for($i = 0; $i < $size_of_array_of_rows; $i++)
                                    {
                                        $usernames[$i] = $array_of_rows[$i]["username"];
                                        $post_texts[$i] = $array_of_rows[$i]["post_text"];
                                    }

                                        
                                        // Package the data in an array
                                        $forum_posts[0] = $usernames[$result];
                                        $forum_posts[1] = $post_texts[$result];
                                }
                                
                                $results[0] = $twitter_posts;
                                $results[1] = $reddit_posts;
                                $results[2] = $forum_posts;
                                
                                return $results;
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
