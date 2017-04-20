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
                            $user_flag = $_POST["user"];
                            
                            if($twitter_flag == 0 && $reddit_flag == 0 && $forum_flag == 0 && $user_flag == 0)
                            {
                                print "Please check a box to search. Press the back button to try again.";
                            }
                            else
                            {
                                $search_results = search_database_posts($search_term, $twitter_flag, $reddit_flag, $forum_flag, $user_flag);
                                
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
                                        $time_since_post = time_since($search_results[0][2][$i]);
                                                                        
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
                            }
                            
                            // DATABASE SEARCH FUNCTIONS
                            
                            // Returns array in format:
                            // [0][1][x] = twitter results
                            // [1][[1][x] = reddit results
                            // [2][1][x] = forum results
                            // [x][0][x] is the user's name who made the post
                            // [x][3][x] is the time the post was made
                            function search_database_posts($search_term, $twitter_flag, $reddit_flag, $forum_flag, $user_flag)
                            {
                                // Establish connection to the database
                                $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
                                
                                if (mysqli_connect_errno()) 
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                    $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
                                    log_to_file($log_message);
                                }
                                
                                $twitter_posts = array();
                                $reddit_posts = array();
                                $forum_posts = array();
                                
                                // Sanitize the input before sending it to the database in a query
                                $search_term = mysqli_real_escape_string($con, $search_term);
                                
                                // Separate the search query into multiple words, if there are any
                                $search_terms = array();
                                
                                // We split the string and store all separate words in an array so we can add them to our query.
                                $search_terms = split_string($search_term);
                                
                                $results = array();
                                
                                if($twitter_flag == 1)
                                {
                                    $twitter_posts = array();
                                    
                                    $twitter_posts = get_search_results($con, $search_terms, "Twitter_Posts", $user_flag);
                                }
                                
                                if($reddit_flag == 1)
                                {
                                    $reddit_posts = array();
                                    
                                    $reddit_posts = get_search_results($con, $search_terms, "Reddit_Posts", $user_flag);
                                }
                                
                                if($forum_flag == 1)
                                {
                                    $forum_posts = array();
                                    
                                    $forum_posts = get_search_results($con, $search_terms, "Forum_Posts", $user_flag);
                                }
                                
                                $results[0] = $twitter_posts;
                                $results[1] = $reddit_posts;
                                $results[2] = $forum_posts;
                                
                                mysqli_close($con);
                                
                                return $results;
                            }

                            /* Helper function for search_database_posts()
                             * Creates and runs the search query against the specified table in the database
                             * 
                             * Parameters:
                             * $con: MySQL connection
                             * $search_terms: (Array) Array of strings to be searched
                             * $table_name: (String) The name of the table
                             * $user_flag: (Int) A 0 or a 1 that says whether or not the search is to be done on usernames instead of posts
                             * 
                             * Returns: A multidemensional array where
                             * [0][x] = usernames
                             * [1][x] = post_texts
                             * [2][x] = timestamps
                             * 
                             * And for Reddit_Posts and Forum_Posts tables:
                             * [3][x] = Post URL
                             */
                            function get_search_results($con, $search_terms, $table_name, $user_flag)
                            {
                                $num_search_terms = count($search_terms);
                                
                                // This will add the url field for use when the table is either reddit or forum
                                if($table_name == "Twitter_Posts")
                                {
                                    $url = "";
                                }
                                else
                                {
                                    $url = ", post_url"; // The table name must be either reddit or forum
                                }
                                
                                // We need this because the Reddit table in the database has a different term for username (OP).
                                if($table_name == "Reddit_Posts")
                                {
                                    $username = "OP";
                                }
                                else
                                {
                                    $username = "username";
                                }
                                
                                if($user_flag == 1)
                                {
                                    $query = "SELECT $username, post_text, tstamp$url FROM $table_name WHERE ($username LIKE '%" . $search_terms[0] . "%') ORDER BY tstamp DESC";
                                }
                                else
                                {
                                    // Construct query based on number of search terms provided
                                    $query = "SELECT $username, post_text, tstamp$url FROM $table_name WHERE ";
                                    
                                    if($num_search_terms < 2)
                                    {
                                        // We have to catch words with . , ? or ! characters following the search term. If we don't do this, then a search for "bit" will return "bitcoin" and we don't want that.
                                        // Unfortunately this adds a lot to the query and is probably inefficient, but we don't have enough time to worry so much about the search function.
                                        $query .= "(post_text LIKE '% " . $search_terms[0] . " %') OR (post_text LIKE '% " . $search_terms[0] . ".%') OR (post_text LIKE '% " . $search_terms[0] . ".%') OR (post_text LIKE '% " . $search_terms[0] . "?%') OR (post_text LIKE '% " . $search_terms[0] . "!%') ORDER BY tstamp DESC"; // No trailing OR because there is only one term
                                    }
                                    else
                                    {
                                        $index = 0;
                                        
                                        do
                                        {
                                            $query .= "(post_text LIKE '% " . $search_terms[$index] . " %') OR (post_text LIKE '% " . $search_terms[$index] . ".%') OR (post_text LIKE '% " . $search_terms[$index] . ",%') OR (post_text LIKE '% " . $search_terms[$index] . "?%') OR (post_text LIKE '% " . $search_terms[$index] . "!%') OR ";
                                            
                                            $index++;
                                        }while($index < ($num_search_terms - 1)); // Stop before the last term so we can add it without the trailing OR
                                        
                                        $query .= "(post_text LIKE '% " . $search_terms[($num_search_terms - 1)] . " %') OR (post_text LIKE '% " . $search_terms[($num_search_terms - 1)] . ".%') OR (post_text LIKE '% " . $search_terms[($num_search_terms - 1)] . ",%') OR (post_text LIKE '% " . $search_terms[($num_search_terms - 1)] . "?%') OR (post_text LIKE '% " . $search_terms[($num_search_terms - 1)] . "!%') ORDER BY tstamp DESC"; // Finish the query
                                    }
                                }
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
                                    
                                    // Branch because the Reddit_Posts and Forum_Posts tables have an extra field
                                    if($table_name == "Twitter_Posts")
                                    {
                                        $usernames = array();
                                        $post_texts = array();
                                        $tstamps = array();
                                        
                                        // Get an array of all values for each field
                                        for($i = 0; $i < $size_of_array_of_rows; $i++)
                                        {
                                            $usernames[$i] = $array_of_rows[$i]["username"];
                                            $post_texts[$i] = $array_of_rows[$i]["post_text"];
                                            $tstamps[$i] = $array_of_rows[$i]["tstamp"];
                                        }
                                            
                                            // Package the data in an array
                                            $posts = array();
                                            
                                            $posts[0] = $usernames;
                                            $posts[1] = $post_texts;
                                            $posts[2] = $tstamps;
                                    }
                                    else
                                    {
                                        $usernames = array();
                                        $post_texts = array();
                                        $tstamps = array();
                                        $post_urls = array();
                                        
                                        // Get an array of all values for each field
                                        for($i = 0; $i < $size_of_array_of_rows; $i++)
                                        {
                                            $usernames[$i] = $array_of_rows[$i]["username"];
                                            $post_texts[$i] = $array_of_rows[$i]["post_text"];
                                            $tstamps[$i] = $array_of_rows[$i]["tstamp"];
                                            $post_urls[$i] = $array_of_rows[$i]["post_url"];
                                        }
                                            
                                            // Package the data in an array
                                            $posts = array();
                                            
                                            $posts[0] = $usernames;
                                            $posts[1] = $post_texts;
                                            $posts[2] = $tstamps;
                                            $posts[3] = $post_urls;
                                            
                                    }
                                    
                                    return $posts;
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
