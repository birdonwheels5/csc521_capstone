<?php

// Toggle switch for using this on my local machine and the weblab server
// I got sick of commenting things every time I switched...
$local_filepaths = false;

if($local_filepaths)
{
    // Local configs
    $GLOBALS['config_dir'] = "../csc435_config.txt";
    $GLOBALS['log_dir'] = "../csc435_log.txt";
}
else
{
    // Weblab configs
    $GLOBALS['config_dir'] = "/home/student/S0276910/csc435_config.txt";
    $GLOBALS['log_dir'] = "/home/student/S0276910/csc435_log.txt";
}

// Load database settings from config file
$settings = array();
$settings = load_config();

$GLOBALS['mysql_user'] = $settings[0];
$GLOBALS['mysql_host'] = $settings[1];
$GLOBALS['mysql_pass'] = $settings[2];
$GLOBALS['mysql_database'] = $settings[3];

// Normally all secret keys should go outside of the web directory in case the php source is leaked, but whatever
// Different from the secret key in cookie_handler
$GLOBALS['secret_key'] = "1251577d0b06ceec7bfc27b8309e279306521c16a";

// Returns boolean
function create_user($username, $password, $email, $authority_level)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
        
        return false;
    }
    
    $username = mysqli_real_escape_string($con, $username);
    $password = mysqli_real_escape_string($con, $password);
    $email = mysqli_real_escape_string($con, $email);
    
    // Creates a random string for our salt
    $salt = openssl_random_pseudo_bytes(8);
    $creation_time = time();
    $uuid = hash("sha256", $username);
    $hashed_password = hash("sha512", $password . $salt);
    // For future password resets
    $new_hashed_password = "";
    
    // For some reason this doesn't work after adding the email to the end
    $insert = "INSERT INTO `" .  $GLOBALS['mysql_database'] . "`.`users` (`username`, `uuid`, `hashed_password`, `new_hashed_password`, `salt`, `authority_level`, `creation_time`, `last_login`, `email`, `validate`, `session_id`) 
    VALUES (\"" . $username . "\", \"" . $uuid . "\", \"" . $hashed_password . "\", \"" . $new_hashed_password . "\", \"" . $salt . "\", " . $authority_level . ", " . $creation_time . ", 0, \"" . $email . "\", 0, '')";
    
    // Add user to the database
    $result = mysqli_query($con, $insert);
    
    return $result;
    
}

function set_session($uuid)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
        
        return false;
    }
    
    $session_id = hash("sha256", openssl_random_pseudo_bytes(16) . $GLOBALS['secret_key'] . time());
    
    $update = "UPDATE  `" . $GLOBALS['mysql_database'] . "`.`users` SET  `session_id` = '$session_id' WHERE `users`.`uuid` = \"" . $uuid . "\";";
    
    $result = mysqli_query($con, $update);
}

function clear_session($uuid)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
        
        return false;
    }
    
    $update = "UPDATE  `" . $GLOBALS['mysql_database'] . "`.`users` SET  `session_id` = '' WHERE `users`.`uuid` = \"" . $uuid . "\";";
    $result = mysqli_query($con, $update);
}

// Returns the user's session ID. If they are not logged in or the uuid can not be found, the function returns false.
function get_session($uuid)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
        
        return false;
    }
    
    $result = mysqli_query($con, "SELECT * FROM `users` WHERE uuid = '" . mysqli_real_escape_string($con, $uuid) . "';");
    $user = mysqli_fetch_array($result);
    
    if(empty($user[11]))
    {
        // Empty or not found
        return " ";
    }
    
    return $user[11]; // User's session ID
}

function is_email_unique($email)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
        
        return false;
    }
    
	$result = mysqli_query($con, 'SELECT * FROM `users` WHERE email = "' . $email . '"');
    $user = mysqli_fetch_array($result);
        
    if($user == NULL)
    {
        return true;
    }
    else
    {
        return false;
    }
}

// Returns array, containing all user data if no uuid is specified, and the data for that specific user if a uuid is specified
function get_user_data($uuid='all')
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
	
	$result = mysqli_query($con, "SELECT * FROM `users`");
	
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
    $uuids = array();
	$hashed_passwords = array();
	$salts = array();
	$authority_levels = array();
	$creation_times = array();
    $last_logins = array();
    $emails = array();
    $validates = array();
    $user_id_nums = array();
    $new_hashed_passwords = array();
	
	// Get an array of all values for each field
	for($i = 0; $i < $size_of_array_of_rows; $i++)
	{
		$usernames[$i] = $array_of_rows[$i]["username"];
        $uuids[$i] = $array_of_rows[$i]["uuid"];
		$hashed_passwords[$i] = $array_of_rows[$i]["hashed_password"];
		$salts[$i] = $array_of_rows[$i]["salt"];
		$authority_levels[$i] = $array_of_rows[$i]["authority_level"];
		$creation_times[$i] = $array_of_rows[$i]["creation_time"];
        $last_logins[$i] = $array_of_rows[$i]["last_login"];
        $emails[$i] = $array_of_rows[$i]["email"];
        $validates[$i] = $array_of_rows[$i]["validate"];
        $user_id_nums[$i] = $array_of_rows[$i]["user_id_num"];
        $new_hashed_passwords[$i] = $array_of_rows[$i]["new_hashed_password"];
	}
	
    if($uuid == 'all')
    {
        // Package all user data and return as an array
        $user_package = array();
        $user_package[0] = $usernames;
        $user_package[1] = $uuids;
        $user_package[2] = $hashed_passwords;
        $user_package[3] = $salts;
        $user_package[4] = $authority_levels;
        $user_package[5] = $creation_times;
        $user_package[6] = $last_logins;
        $user_package[7] = $emails;
        $user_package[8] = $validates;
        $user_package[9] = $user_id_nums;
        $user_package[10] = $new_hashed_passwords;
        
        return $user_package;
    }
    else
    {
        // Search for requested user using a linear search
        // This is really bad since we could just ask mysql to find the user for us,
        // but at this point this isn't worth fixing
        $result = -1;
        for($i = 0; $i < $size_of_array_of_rows; $i++)
        {
            if($uuids[$i] == $uuid)
            {
                $result = $i;
            }
        }
        
        if($result == -1)
        {
            return -1;
        }
        
        // Package the user's data and return as an array
        $user_package = array();
        $user_package[0] = $usernames[$result];
        $user_package[1] = $uuids[$result];
        $user_package[2] = $hashed_passwords[$result];
        $user_package[3] = $salts[$result];
        $user_package[4] = $authority_levels[$result];
        $user_package[5] = $creation_times[$result];
        $user_package[6] = $last_logins[$result];
        $user_package[7] = $emails[$result];
        $user_package[8] = $validates[$result];
        $user_package[9] = $user_id_nums[$result];
        $user_package[10] = $new_hashed_passwords[$result];
        
        return $user_package;
    }
}

// Returns nothing. Halts page execution if user lacks appropriate permissions
function authenticate_user($required_authority_level)
{    
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
    
    $color = "hsla(360, 100%, 50%, 0.9)";
    
    if($cookie_handler->get_exists())
    {
        if($cookie_handler->get_validity())
        {
            // Fetch user data
            $results = get_user_data($user_cookie->get_uuid());
            $user_authority_level = $results[4];
            
            // Check authentication level
            if($user_authority_level < $required_authority_level)
            {
                print "<div class=\"box\" style=\"background-color:" . $color . ";margin-top:25px;\">You are not authorized to view this page.</div>";
                exit;
            }
        }
        else
        {
            print "<div class=\"box\" style=\"background-color:" . $color . ";margin-top:25px;\">Invalid cookie. You need a valid login with the appropriate permissions in order to access this page.</div>";
            exit;
        }
    }
    else
    {
        print "<div class=\"box\" style=\"background-color:" . $color . ";margin-top:25px;\">You need to be logged in to access this resource.</div>";
        exit;
    }
}

// Returns boolean
function update_user_password($uuid)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    $result = mysqli_query($con, "SELECT * FROM `users` WHERE uuid = \"" . $uuid . "\";");
    $user = mysqli_fetch_array($result);
    $new_hashed_password = $user[4];
    
    // Overwrite the old password with the new one
    $update = "UPDATE  `" . $GLOBALS['mysql_database'] . "`.`users` SET  `hashed_password` =  '" . $new_hashed_password . "' WHERE `users`.`uuid` = '" . $uuid . "' LIMIT 1 ;";
    
    // Execute password update
    $result = mysqli_query($con, $update);
    
    // Reset the new_hashed_password field so that users can't keep clicking the reset link
    $update = "UPDATE  `" . $GLOBALS['mysql_database'] . "`.`users` SET  `new_hashed_password` = '' WHERE `users`.`uuid` = '" . $uuid . "' LIMIT 1 ;";
    
    $result = mysqli_query($con, $update);
    
    return $result;
    
}

function set_new_hashed_password($uuid, $new_hashed_password)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    // Overwrite the old password with the new one
    $update = "UPDATE  `" . $GLOBALS['mysql_database'] . "`.`users` SET  `new_hashed_password` =  '" . $new_hashed_password . "' WHERE `users`.`uuid` = '" . $uuid . "' LIMIT 1 ;";
    
    // Execute password update
    $result = mysqli_query($con, $update);
}

// Returns boolean
function update_last_login($uuid)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    $time = time();
    
    $update = "UPDATE  `" . $GLOBALS['mysql_database'] . "`.`users` SET  `last_login` =  '" . $time . "' WHERE `users`.`uuid` = '" . $uuid . "' LIMIT 1 ;";
    
    // Execute last login update
    $result = mysqli_query($con, $update);
}

// Returns an array of all settings from the config file
// Additional config options can be added as functionality expands.
function load_config()
{
    $mysql_user = "";
    $mysql_host = "";
    $mysql_pass = "";
    $mysql_database = "";

    $settings = array();
    
    if(fopen($GLOBALS['config_dir'], "r") == false)
    {
        $log_message = "CRITICAL: Unable to load config file! Webpages will not load at all without it.";
        log_to_file($log_message);
    }
    $handle = fopen($GLOBALS['config_dir'], "r") or die ("Error loading config file! Please contact a system administrator to get this fixed! Webservices are non-functional without it.");
    while (($line = fgets($handle)) !== false)
    {
        // Fetch config information line-by-line
        if (strcmp(stristr($line, "mysql_user:"), $line) == 0)
        {
            $mysql_user = trim(str_ireplace("mysql_user:", "", $line));
        }
        if (strcmp(stristr($line, "mysql_host:"), $line) == 0)
        {
            $mysql_host = trim(str_ireplace("mysql_host:", "", $line));
        }
        if (strcmp(stristr($line, "mysql_pass:"), $line) == 0)
        {
            $mysql_pass = trim(str_ireplace("mysql_pass:", "", $line));
        }
        if (strcmp(stristr($line, "mysql_database:"), $line) == 0)
        {
            $mysql_database = trim(str_ireplace("mysql_database:", "", $line));
        }
        
    }
    
    fclose($handle);
    
    
    $settings[0] = $mysql_user;
    $settings[1] = $mysql_host;
    $settings[2] = $mysql_pass;
    $settings[3] = $mysql_database;
    
    // Check to see if any of the settings are empty. If they are, 
    // that means that there is a typo in one of the settings
    // ie "myr_rpc_uer: " instead of "myr_rpc_user: "
    for($i = 0; $i < count($settings); $i++)
    {
        if(empty($settings[$i]))
        {
            $log_message = "CRITICAL: Unable to load config file due to a damaged setting! Please go through the config file to correct the error. Webpages will not load at all without the config file.";
            log_to_file($log_message);
            
            die ("Error loading config file! Please contact a system administrator to get this fixed! Webservices are non-functional without it.");
        }
    }
    
    return $settings;
}

// Logs a given message to the log file.
function log_to_file($log_message)
{
    // Append the date and time of message to the beginning of the message
    $text = date("Y-m-d H:i:s") . ": " . $log_message . PHP_EOL;
    file_put_contents($GLOBALS['log_dir'], $text, FILE_APPEND) or print "Error loading logs file! Please contact a system administrator.";
}

// Prints out the nav bar on all the pages.
function print_header($cookie_handler, $cookie_name)
{
    $user_cookie = $cookie_handler->get_cookie($cookie_name);
    $cookie_handler->cookie_exists($cookie_name);
    $uuid = $user_cookie->get_uuid();
    
    print '<header>
    
        <div class="logoContainer">
            <!-- <img src="logo-bar.png"> -->
        </div>
        
        <div class="button">
            <p><a href ="index.php">Index</a></p>
        </div>
        
        <div class="button">';
                
                        if($cookie_handler->get_exists())
                        {
                            if($cookie_handler->get_validity())
                            {
                                print "<p><a href =\"logout.php\">Logout</a></p>";
                            }
                            else
                            {
                                $cookie_handler->delete_cookie($cookie_name);
                                clear_session($uuid);
                                print "<p><a href =\"login.php\">Login</a></p>";
                            }
                        }
                        else
                        {
                            print "<p><a href =\"login.php\">Login</a></p>";
                        }
                print '</div>
                
                <div class="button">';
                        if($cookie_handler->get_exists())
                        {
                            if($cookie_handler->get_validity())
                            {
                                print "<p><a href =\"my_messages.php\">My Messages</a></p>";
                            }
                            else
                            {
                                $cookie_handler->delete_cookie($cookie_name);
                                clear_session($uuid);
                                print "<p><a href =\"createuser.php\">Create an Account</a></p>";
                            }
                        }
                        else
                        {
                            print "<p><a href =\"createuser.php\">Create an Account</a></p>";
                        } 
				print '</div>
                
                <div class="button">';
                        if($cookie_handler->get_exists())
                        {
                            if($cookie_handler->get_validity())
                            {
                                print "<p><a href =\"passwd.php\">Change Password</a></p>";
                            }
                            else
                            {
                                $cookie_handler->delete_cookie($cookie_name);
                                clear_session($uuid);
                            }
                        }
                        else
                        {
                            
                        }
				print '</div>
                
                <div class="button">
					<p><a href ="bulletin.php">Bulletin Board</a></p>
				</div>
                
                 <div class="button">';
                        if($cookie_handler->get_exists())
                        {
                            if($cookie_handler->get_validity())
                            {
                                print "<p><a href =\"my_posts.php\">My Posts</a></p>";
                            }
                            else
                            {
                                $cookie_handler->delete_cookie($cookie_name);
                                clear_session($uuid);
                            }
                        }
                        else
                        {
                            
                        }
                        
                print '</div> 
				
				<div class="button">
					<p><a href ="user.php">Member Area</a></p>
				</div>
                
                <div class="button">
					<p><a href ="admin.php">Admin Area</a></p>
				</div>
				
			</header>';
}

// $uuid is an optional argument that allows you to specify a uuid to fetch the posts of, if you don't then all posts are returned.
function get_posts($uuid='all')
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    
	if($uuid == 'all') // Gather and return all posts
	{
        $result = mysqli_query($con, "SELECT * FROM `posts`");
    }
    else // Gather and return posts from the specified user
    {
        $result = mysqli_query($con, "SELECT * FROM `users` WHERE uuid = " . "\"$uuid\"" . ";");
        $user = mysqli_fetch_array($result);
        $user_id_num = $user[0];
        $result = mysqli_query($con, "SELECT * FROM `posts` WHERE op_id_num=" . $user_id_num);
    }
    
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
    
    $post_ids = array();
    $subjects = array();
    $contents = array();
    $ops = array();
    $created = array();
    $last_edited = array();
    
    // Get an array of all values for each field
    for($i = 0; $i < $size_of_array_of_rows; $i++)
    {
        $post_ids[$i] = $array_of_rows[$i]["post_id"];
        $subjects[$i] = $array_of_rows[$i]["subject"];
        $contents[$i] = $array_of_rows[$i]["content"];
        $ops[$i] = $array_of_rows[$i]["op_id_num"];
        $created[$i] = $array_of_rows[$i]["created"];
        $last_edited[$i] = $array_of_rows[$i]["last_edited"];
    }
    
    if($result == -1)
    {
        return -1;
    }
    
    // Package the user's data and return as an array
    $posts = array();
    $posts[0] = $post_ids;
    $posts[1] = $subjects;
    $posts[2] = $contents;
    $posts[3] = $ops;
    $posts[4] = $created;
    $posts[5] = $last_edited;
    
    return $posts;
    
}

function get_post_by_id($post_id)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    $result = mysqli_query($con, "SELECT * FROM `posts` WHERE post_id = " . "$post_id" . ";");
    $post = mysqli_fetch_array($result);
    
    return $post;
}

function get_post_count($uuid='all')
{
    if($uuid == 'all')
    {
        $posts = get_posts();
    }
    else
    {
        $posts = get_posts($uuid);
    }
    
    return count($posts[0]);
}

function submit_post($cookie_handler, $cookie_name, $subject, $content)
{
    // Time the post was created
    $created = $last_edited = time();
    
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    // Escape and clean the post contents and subject line
    $subject = mysqli_real_escape_string($con, trim(htmlspecialchars($subject)));
    $content = mysqli_real_escape_string($con, trim(htmlspecialchars($content)));
    
    do
    {
        
        // If cookie exists, get user_id_num from database and submit post linked to them
        if($cookie_handler->get_exists() == true)
        {
            $cookie = $cookie_handler->get_cookie($cookie_name);
            $uuid = $cookie->get_uuid();
            $result = mysqli_query($con, "SELECT * FROM `users` WHERE uuid = \"" . $uuid . "\";");
            $user = mysqli_fetch_array($result);
            $user_id_num = $user[0];
            if($user_id_num == NULL)
            {
                // Post as guest instead because something went wrong with the user
                log_to_file("ERROR: User " . $uuid . " attemped to submit post, but user could not be located in database!");
                break;
            }
            
            $insert = "INSERT INTO " . $GLOBALS['mysql_database'] . ".`posts` (`subject`, `content`, `op_id_num`, `created`, `last_edited`) VALUES (\"" . $subject . "\", \"" . $content . "\", \"" . $user_id_num . "\", " . $created . ", " . $last_edited . ");";
            $result = mysqli_query($con, $insert);
            // Debug
            //var_dump($result);
            //print $user_id_num . " " . $content . " " . $subject . " " . $created . " " . $last_edited . " ";
            return 0; // Success
        }
    }while(false);
    
    // Submit post as a guest
    $user_id_num = -1;
    $insert = "INSERT INTO " . $GLOBALS['mysql_database'] . ".`posts` (`subject`, `content`, `op_id_num`, `created`, `last_edited`) VALUES (\"" . $subject . "\", \"" . $content . "\", \"" . $user_id_num . "\", " . $created . ", " . $last_edited . ");";
    $result = mysqli_query($con, $insert);
    
    return 0; // Success
}

// Guests cannot edit posts, for obvious reasons
function edit_post($raw_subject, $raw_content, $post_id)
{    
    if($post_id == -1) // User is not really editing a post; they loaded up the page without using the edit button
    {
        return -1;
    }
    
    // Time the post was edited
    $last_edited = time();
    
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    // Escape and clean the post contents and subject line
    $subject = mysqli_real_escape_string($con, trim(htmlspecialchars($raw_subject)));
    $content = mysqli_real_escape_string($con, trim(htmlspecialchars($raw_content)));
        
    $update = "UPDATE  `" . $GLOBALS['mysql_database'] . "`.`posts` SET  `subject` =  '" . $subject . "', `content` = '" . $content . "', `last_edited` = '" . $last_edited . "' WHERE `posts`.`post_id` = '" . $post_id . "';";    $result = mysqli_query($con, $update);
    // Debug
    //var_dump($result);
    //print $user_id_num . " " . $content . " " . $subject . " " . $created . " " . $last_edited . " ";
    return 0; // Success
}

// This function also has an optional argument, much like get_posts().
function display_posts($uuid='all')
{
    if($uuid == 'all')
    {
        $posts = get_posts();
    }
    else
    {
        $posts = get_posts($uuid);
    }
    
    $num_posts = count($posts[0]);
    
    for($i = 0; $i < $num_posts; $i++)
    {
        print '<div class="box">
                    <p>
                        <center>
                            ' . 
                            
                            'Post ID: ' . $posts[0][$i] . '<br/>' . 
                            'Subject: ' . $posts[1][$i] . ', Posted by: ' . get_user_by_id_num($posts[3][$i]) .' at ' . date("m/d/y, h:i:s A", $posts[4][$i]) . '<br/><br/>' . 
                            $posts[2][$i] . '<br/><br/>' . 
                            'Last Edited: '. date("m/d/y, h:i:s A", $posts[5][$i]) . '<br/>';
                            
                            // This is for displaying the edit links for the My Posts page
                            if($uuid != 'all')
                            {
                                print '<br/>';
                                print '<form method="post" action="edit_post.php">
                                       <input type="hidden" name="post_id" value="' . $posts[0][$i] . '">
                                       <input type="submit" name="submit" value="Edit">
                                       </form>'; 
                            }
                            
                            print '
                        </center>
                    </p>
                </div>';
    }
    
}

// Returns a string that is the user's username, found using their unique id number. If the user was deleted, it returns [deleted] (which conveniently is an invalid username).
function get_user_by_id_num($user_id_num)
{
    if($user_id_num == -1)
    {
        return "Guest";
    }
    
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    $result = mysqli_query($con, "SELECT * FROM `users` WHERE user_id_num = " . mysqli_real_escape_string($con, $user_id_num) . ";");
    $user = mysqli_fetch_array($result);
    
    if(empty($user[1]))
    {
        // User was deleted
        return "[deleted]";
    }
    
    return $user[1]; // [1] is the username
}

// Returns a string that is the user's uuid, found using their unique id number. If the user does not exists, it returns [unknown] (which conveniently is an invalid username).
function get_uuid_by_id_num($user_id_num)
{
    
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    $result = mysqli_query($con, "SELECT * FROM `users` WHERE user_id_num = " . mysqli_real_escape_string($con, $user_id_num) . ";");
    $user = mysqli_fetch_array($result);
    
    if(empty($user[2]))
    {
        // User does not exist
        return "[unknown]";
    }
    
    return $user[2]; // [2] is the uuid
}

function get_user_id_num_by_email($email)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    $result = mysqli_query($con, "SELECT * FROM `users` WHERE email = '" . mysqli_real_escape_string($con, $email) . "';");
    $user = mysqli_fetch_array($result);
    
    if(empty($user[2]))
    {
        // User does not exist
        return "[unknown]";
    }
    
    return $user[0]; // [0] is the id number
}

function send_validation_email($user_id_num, $uuid, $email)
{
    $site_web_address = "weblab.salemstate.edu/~S0276910/CSC435/login-project/verification.php?id=$user_id_num&etoken=";
    $site_name = 'Lizard Squad BB';
    
    $token = sha1($uuid . $user_id_num . $GLOBALS['secret_key']);
    $verification_url = $site_web_address . $token;
    
    $email_text = 'Hello, thank you for registering with ' . $site_name . '. In order to log in, visit the following link to activate your account: ' . $verification_url;
    
    mail($email, 'Email Verification for ' . $site_name, $email_text);
}

// Returns a boolean value based on whether or not the tokens (from email url and expected token) match.
function validate_email_address($user_id_num, $uuid, $url_token)
{
    $expected_token = sha1($uuid . $user_id_num . $GLOBALS['secret_key']);
    
    if($url_token == $expected_token)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function send_password_validation_email($user_id_num, $uuid, $email, $new_hashed_password, $reset_pass=false)
{
    if($reset_pass == false)
    {
        $site_web_address = "weblab.salemstate.edu/~S0276910/CSC435/login-project/verification.php?ptoken=";
    }
    else
    {
        $site_web_address = "weblab.salemstate.edu/~S0276910/CSC435/login-project/verification.php?id=$user_id_num&rptoken=";
    }
    $site_name = 'Lizard Squad BB';
    
    $token = sha1($uuid . $user_id_num . $new_hashed_password . $GLOBALS['secret_key']);
    $verification_url = $site_web_address . $token;
    
    if($reset_pass == false)
    {
        $email_text = 'Hello, thank you for using ' . $site_name . '. In order to change your password, visit the following link: ' . $verification_url;
    }
    else
    {
        $email_text = 'Hello, thank you for using ' . $site_name . '. In order to reset your password, visit the following link: ' . $verification_url;
    }
    
    mail($email, 'Password Verification for ' . $site_name, $email_text);
}

// Returns a boolean value based on whether or not the tokens (from email url and expected token) match.
function validate_new_password($user_id_num, $uuid, $new_hashed_password, $url_token)
{
    $expected_token = sha1($uuid . $user_id_num . $new_hashed_password . $GLOBALS['secret_key']);
    
    if($url_token == $expected_token)
    {
        return true;
    }
    else
    {
        return false;
    }
}

// This gets called after the user's email has been validated. This updates the database so that they can log in.
function set_validate_field($uuid)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    $update = "UPDATE  `" . $GLOBALS['mysql_database'] . "`.`users` SET  `validate` = '1' WHERE `users`.`uuid` = \"" . $uuid . "\";";
    $result = mysqli_query($con, $update);
}

// $to_user_id_nums is of type array
function new_message($subject, $message_text, $from_user_id_num, $to_user_id_nums)
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
        die("Unable to connect to the database!");
    }
    
    // Escape and clean the post contents and subject line
    $subject = mysqli_real_escape_string($con, trim(htmlspecialchars($subject)));
    $message_text = mysqli_real_escape_string($con, trim(htmlspecialchars($message_text)));
    
    $sent = time();
    $read_it = -1; // User has not read the message yet
    
    // Insert into messages table
    $insert = "INSERT INTO " . $GLOBALS['mysql_database'] . ".`messages` (`from_id`, `sent`, `subject`, `text`) VALUES (" . $from_user_id_num . ", " . $sent . ", \"" . $subject . "\", \"" . $message_text . "\");";
    
    $result = mysqli_query($con, $insert);
    
    $result = mysqli_query($con, "SELECT * FROM `messages` WHERE sent = \"" . $sent . "\";");
    $message = mysqli_fetch_array($result);
    $message_id_num = $message[0];
    
    // Prepare the to_ids for inserting into the database
    $to_user_id_nums_string = $to_user_id_nums[0]; // initilize it
    for($i = 1; $i < count($to_user_id_nums); $i++)
    {
        $to_user_id_nums_string .= "|" . $to_user_id_nums[$i];
    }
    
    // Insert into send_tos table
    $insert = "INSERT INTO " . $GLOBALS['mysql_database'] . ".`send_tos` (`message_id`, `to_id`, `read_it`) VALUES (" . $message_id_num . ", \"" . $to_user_id_nums_string . "\", " . $read_it . ");";
    $result = mysqli_query($con, $insert);
    
}

function get_messages($uuid, $message_id='all')
{
    // Establish connection to the database
    $con = mysqli_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_pass'], $GLOBALS['mysql_database']);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $log_message = "CRITICAL: Failed to connect to database! Please check your database and database settings!";
        log_to_file($log_message);
    }
    
    $message_ids_result = array();
	
	$result = mysqli_query($con, "SELECT * FROM `send_tos` WHERE INSTR(to_id, '$uuid') > 0;");
        
	// Obtain the number of rows from the result of the query
	$num_rows = mysqli_num_rows($result);
    
    for($i = 0; $i < $num_rows; $i++)
    {
        $data = mysqli_fetch_array($result);
        
        $message_ids_result[$i] = $data[1];
    }
    
    $result = mysqli_query($con, "SELECT * FROM `messages`;");
    
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
							
	$message_ids = array();
    $from_ids = array();
	$sents = array();
	$subjects = array();
	$texts = array();
	
	// Get an array of all values for each field
	for($i = 0; $i < $size_of_array_of_rows; $i++)
	{
        for($k = 0; $k < count($message_ids_result); $k++)
        {
            if($array_of_rows[$i]["message_id"] == $message_ids_result[$k])
            {
                
                $message_ids[$i] = $array_of_rows[$i]["message_id"];
                $from_ids[$i] = $array_of_rows[$i]["from_id"];
                $sents[$i] = $array_of_rows[$i]["sent"];
                $subjects[$i] = $array_of_rows[$i]["subject"];
                $texts[$i] = $array_of_rows[$i]["text"];
                
                break;
            }
        }
		
	}
    
    // Fix the indexes on the arrays since we filtered as we iterated through the data
    $message_ids = array_values($message_ids);
    $from_ids = array_values($from_ids);
	$sents = array_values($sents);
	$subjects = array_values($subjects);
	$texts = array_values($texts);
	
    if($message_id == 'all')
    {
        // Package all message data for this user and return as an array
        $message_package = array();
        $message_package[0] = $message_ids;
        $message_package[1] = $from_ids;
        $message_package[2] = $sents;
        $message_package[3] = $subjects;
        $message_package[4] = $texts;

        return $message_package;
    }
    else
    {
        // Search for requested message using a linear search
        // Recycled from get_user_data(). I know it's bad.
        $result = -1;
        for($i = 0; $i < count($message_ids); $i++)
        {
            if($message_ids[$i] == $message_id)
            {
                $result = $i;
            }
        }
        
        if($result == -1)
        {
            return -1;
        }
        
        // Package the message data and return as an array
        $message = array();
        $message[0] = $message_ids[$result];
        $message[1] = $from_ids[$result];
        $message[2] = $sents[$result];
        $message[3] = $subjects[$result];
        $message[4] = $texts[$result];
        
        return $message;
    }
}

function display_all_messages($messages_array)
{
    $message_count = count($messages_array[0]);
    
    //print '<form method="post" action="display.php">';
    print "<table>";
    print "<tr><td>From</td><td>Time</td><td>Subject</td><td></td></tr>";
    
    // Pretty formatting
    print "<tr><td><hr/></td><td><hr/></td><td><hr/></td></tr>";
    
    for($i = 0; $i < $message_count; $i++)
    {
        print '<form method="post" action="display.php">
        <input type="hidden" name="message_id" value="' . $messages_array[0][$i] . '">';
        
        print "<tr><td>" . get_user_by_id_num($messages_array[1][$i]) . "</td><td>" . date("m/d/y, h:i:s A", $messages_array[2][$i]) . "</td><td>" . $messages_array[3][$i] . "</td><td><input type=\"submit\" name=\"submit\" value=\"View Message\"></td></tr>";
        print '</form>';
        print "\n";
    }
    
    print "</table>";
}

function display_single_message($message_data)
{
    $message_data_count = count($message_data);
    
    print "<table>";
    
    print "<tr><td>From: </td><td>" . get_user_by_id_num($message_data[1]) . "</td></tr>";
    print "<tr><td>Time: </td><td>" . date("m/d/y, h:i:s A", $message_data[2]) . "</td></tr>";
    print "<tr><td>Subject: </td><td>" . $message_data[3] . "</td></tr>";
    print "<tr><td>Message: </td><td>" . $message_data[4] . "</td></tr>";
    
    print "</table>";
}


?>
