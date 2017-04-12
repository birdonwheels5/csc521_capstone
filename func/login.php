<?php

require_once "general.php";

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
    
    // For some reason this doesn't work after adding the email to the end
    //$insert = "INSERT INTO `" . $GLOBALS['mysql_database'] . "`.`users` (`user_id_num`, `username`, `uuid`, `hashed_password`, `new_hashed_password`, `salt`, `authority_level`, `creation_time`, `last_login`, `email`, `validate`, `session_id`) VALUES (0, '$username', '$uuid', '$hashed_password', NULL, '$salt', '$authority_level', '$creation_time', '0', '$email', '0', NULL);";
    
    $insert = "INSERT INTO `users` (`user_id_num`, `username`, `uuid`, `hashed_password`, `new_hashed_pasword`, `salt`, `authority_level`, `creation_time`, `last_login`, `email`, `validate`, `session_id`) VALUES ('0', '$username', '$uuid', '$hashed_password', NULL, '$salt', '100', '$creation_time', NULL, '$email', '0', NULL);";
    
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
    
    return $result;
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
    
    return $result;
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
    $s_id = substr($GLOBALS['student_id']);
    $site_web_address = "weblab.salemstate.edu" . "$s_id/login/verification.php?id=$user_id_num&etoken=";
    $site_name = $GLOBALS['website_name'];
    
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
    $s_id = substr($GLOBALS['student_id']);
    $site_name = $GLOBALS['website_name'];
    
    if($reset_pass == false)
    {
        $site_web_address = "weblab.salemstate.edu" . "$s_id/login/verification.php?ptoken=";
    }
    else
    {
        $site_web_address = "weblab.salemstate.edu" . "$s_id/login/verification.php?id=$user_id_num&rptoken=";
    }
    
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

?>
