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
                    
                    update_last_login($uuid);
                }
                
                print_header($cookie_handler, $cookie_name);
            
            ?>
            
            <?php 
                // Authenticate user
                authenticate_user(100);
                
                // Get the form field values from the form
                // Flags will either be NULL or 1
                $search_term = trim(htmlspecialchars($_POST["search_term"]));
                $twitter_flag = $_POST["twitter"];
                $reddit_flag = $_POST["reddit"];
                $forum_flag = $_POST["forum"];
                $user_flag = $_POST["user"];
                
                // Count how many rows we will have by adding the three flags together
                // If they aren't checked, they are null, but PHP doesn't seem to mind adding NULL to a number...
                $num_columns = $twitter_flag + $reddit_flag + $forum_flag;
                
                // No boxes are checked, set um_columns to 1 so the error displays properly
                if($num_columns == 0)
                {
                    $num_columns = 1;
                }
                
                // Get search results
                if($twitter_flag == 1 || $reddit_flag == 1 || $forum_flag == 1)
                {
                    $search_results = search_database_posts($search_term, $twitter_flag, $reddit_flag, $forum_flag, $user_flag);
                }
                
            ?>
			
			<article>
                <div class="row">
                
                <?php 
                    // We need to print out the divs correctly based on how many rows we will be displaying
                    if($num_columns == 1)
                    {
                        print '
                                <div class="empty col-4">
                                </div>';
                    }
                    else if($num_columns == 2)
                    {
                        print '
                                <div class="empty col-2">
                                </div>';
                    }
                    else if($num_columns == 3)
                    {
                        // Print nothing because there are no margins on the page
                    }
                            
                            
                            
                            if($twitter_flag == 0 && $reddit_flag == 0 && $forum_flag == 0)
                            {
                                print '
                                        <div class="col-4 object shadow">
					                        Please check a box under "Data select" to search. Press the back button to try again.
                                        </div>';
                            }
                            else if($twitter_flag == 1)
                            {
                                $twitter_search_results_length = count($search_results[0][0]); // We care about the length of the third (last array) in the package of arrays
                                
                                if($twitter_search_results_length == 0)
                                {
                                    print_column_div($num_columns);
                                    print '
                                                <p>';
                                    
                                    print "No results found in Twitter posts for query '$search_term'."; if($user_flag ==1){ print " (Usernames)";}
                                    
                                    print '
                                                </p>
                                            </div>';
                                }
                                else
                                {
                                    print_column_div($num_columns);
                                    print '
                                                    <p> <h2>Twitter Results'; if($user_flag ==1){ print " (Usernames)";} print "</h2>";
                                                        
                                    for($i = 0; $i < $twitter_search_results_length; $i++)
                                    {
                                        print '<hr/>';
                                        
                                        $time_since_post = time_since($search_results[0][2][$i]);
                                        
                                        print "Username: " . $search_results[0][0][$i] . " <br/><br/>\n
                                               Post: " . $search_results[0][1][$i] . " <br/><br/>\n
                                               Posted $time_since_post ago. <br/><br/>\n";
                                    }
                                    
                                    print '</p>
                                                    </div>';
                                }
                            }
                            
                            if($reddit_flag == 1)
                            {                                
                                $reddit_search_results_length = count($search_results[1][0]); // We care about the length of the third (last array) in the package of arrays
                                
                                if($reddit_search_results_length == 0)
                                {
                                    print_column_div($num_columns);
                                    print '
                                                <p>';
                                    
                                    print "No results found in Reddit posts for query '$search_term'."; 
                                    if($user_flag ==1){ print " (Usernames)";}
                                    
                                    print '
                                                </p>
                                            </div>';
                                }
                                else
                                {
                                    print_column_div($num_columns);
                                    print '
                                                    <p> <h2>Reddit Results'; if($user_flag ==1){ print " (Usernames)";} print "</h2>";
                                                        
                                    for($i = 0; $i < $reddit_search_results_length; $i++)
                                    {
                                        print '<hr/>';
                                        
                                        $time_since_post = time_since($search_results[1][2][$i]);
                                        $post_url = $search_results[1][3][$i];
                                        
                                        print "Username: " . $search_results[1][0][$i] . " <br/><br/>\n
                                               Post: " . $search_results[1][1][$i] . " <br/><br/>\n
                                               Posted $time_since_post ago. <br/><br/>\n
                                               URL to original post: <a href=\"$post_url\">$post_url</a> <br/><br/>\n";
                                    }
                                    
                                    print '</p>
                                                    </div>';
                                }
                            }
                            
                            if($forum_flag == 1)
                            {                                
                                $forum_search_results_length = count($search_results[2][0]); // We care about the length of the third (last array) in the package of arrays
                                
                                if($forum_search_results_length == 0)
                                {
                                    print_column_div($num_columns);
                                    print '
                                                <p>';
                                    
                                    print "No results found in Bitcointalk posts for query '$search_term'."; if($user_flag ==1){ print " (Usernames)";}
                                    
                                    print '
                                                </p>
                                            </div>';
                                }
                                else
                                {
                                    print_column_div($num_columns);
                                    print '
                                                    <p> <h2>Bitcointalk Results'; if($user_flag ==1){ print " (Usernames)";} print "</h2>";
                                                        
                                    for($i = 0; $i < $forum_search_results_length; $i++)
                                    {
                                        print '<hr/>';
                                        
                                        $time_since_post = time_since($search_results[2][2][$i]);
                                        $post_url = $search_results[2][3][$i];
                                        
                                        print "Username: " . $search_results[2][0][$i] . " <br/><br/>\n
                                               Post: " . $search_results[2][1][$i] . " <br/><br/>\n
                                               Posted $time_since_post ago. <br/><br/>\n
                                               URL to original post: <a href=\"$post_url\">$post_url</a> <br/><br/>\n";
                                    }
                                    
                                    print '</p>
                                                    </div>';
                                }
                            }
                            
                            if($num_columns == 1)
                            {
                                print '
                                        <div class="empty col-4">
                                        </div>
                                    </div>';
                            }
                            else if($num_columns == 2)
                            {
                                print '
                                        <div class="empty col-2">
                                        </div>
                                    </div>';
                            }
                            else if($num_columns == 3)
                            {
                                print '
                                    </div>';
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
                                
                                // This prints out the correct div depending on how many columns we currently have in the search display (values range from 1 to 3)
                                // I was going to use this to make the 3 columns case have a shorter div, but the padding does not line up... For now it will just print the same
                                // thing for everything.
                                function print_column_div($num_columns)
                                {
                                    if($num_columns == 1 || $num_columns == 2 || $num_columns == 3)
                                    {
                                        print '<div class="col-4 object shadow">';
                                    }
                                }
                        ?>
			</article>
			
		</div>
	</body>
	
</html>
