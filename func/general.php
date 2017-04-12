<?php

// Toggle switch for using this on my local machine and the weblab server
// I got sick of commenting things every time I switched...
$local_filepaths = false;

if($local_filepaths)
{
    // Local configs
    $GLOBALS['config_dir'] = "/var/www/bitquote/config.txt";
    $GLOBALS['log_dir'] = "/var/www/bitquote/log.txt";
}
else
{
    // Athough the student ID is in the config, this is necessary to find that config.
    $s_id = "S0280512";
    
    // Weblab configs
    $GLOBALS['config_dir'] = "../../bitquote/config.txt";
    $GLOBALS['log_dir'] = "/home/student/$s_id/bitquote/log.txt";
    
    //$GLOBALS['config_dir'] = "../../config.txt";
    //$GLOBALS['log_dir'] = "../../log.txt";
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

// Load student ID, if there is any
$GLOBALS['student_id'] = $settings[6];



// Returns an array of all settings from the config file
// Additional config options can be added as functionality expands.
function load_config()
{
    $filename = $GLOBALS['config_dir'];
    $mysql_user = "";
    $mysql_host = "";
    $mysql_pass = "";
    $mysql_database = "";
    $twitter_public_key = "";
    $twitter_secret_key = "";
    $student_id = "";

    $settings = array();
    
    if(fopen($filename, "r") == false)
    {
        $log_message = "CRITICAL: Unable to load config file! Webpages will not load at all without it.";
        log_to_file($log_message);
    }
    $handle = fopen($filename, "r") or die ("Error loading config file! Please contact a system administrator to get this fixed! Webservices are non-functional without it.");
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
        if (strcmp(stristr($line, "twitter_public_key:"), $line) == 0)
        {
            $twitter_public_key = trim(str_ireplace("twitter_public_key:", "", $line));
        }
        if (strcmp(stristr($line, "twitter_secret_key:"), $line) == 0)
        {
            $twitter_secret_key = trim(str_ireplace("twitter_secret_key:", "", $line));
        }
        if (strcmp(stristr($line, "student_id:"), $line) == 0)
        {
            $student_id = trim(str_ireplace("student_id:", "", $line));
        }
        
    }
    
    fclose($handle);
    
    
    $settings[0] = $mysql_user;
    $settings[1] = $mysql_host;
    $settings[2] = $mysql_pass;
    $settings[3] = $mysql_database;
    $settings[4] = $twitter_public_key;
    $settings[5] = $twitter_secret_key;
    
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
	
    // Leave blank if no student id is present
    if(empty($student_id))
    {
        $settings[6] = "";
    }
    else
    {
        $settings[6] = "/~" . $student_id; // The / is for the directory structure
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
            <p><a href ="' . $GLOBALS['student_id'] . '/index.php">Index</a></p>
        </div>
        
        <div class="button">';
                
                        if($cookie_handler->get_exists())
                        {
                            if($cookie_handler->get_validity())
                            {
                                print "<p><a href =\"/$s_id/login/logout.php\">Logout</a></p>";
                            }
                            else
                            {
                                $cookie_handler->delete_cookie($cookie_name);
                                clear_session($uuid);
                                print '<p><a href ="' . $GLOBALS['student_id'] . '/login/login.php">Login</a></p>';
                            }
                        }
                        else
                        {
                            print '<p><a href ="' . $GLOBALS['student_id'] . '/login/login.php">Login</a></p>';
                        }
                print '</div>
                
                <div class="button">';
                        if($cookie_handler->get_exists())
                        {
                            if($cookie_handler->get_validity())
                            {
                                print '<p><a href ="' . $GLOBALS['student_id'] . '/login/passwd.php">Change Password</a></p>';
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
					<p><a href ="' . $GLOBALS['student_id'] . '/user.php">Member Area</a></p>
				</div>
                
                <div class="button">
					<p><a href ="' . $GLOBALS['student_id'] . '/admin.php">Admin Area</a></p>
				</div>
				
			</header>';
}

// Cleans a given input to prevent cross-site scripting attacks
function clean_input($input)
{
    $input = trim($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Converts given url to an html verson of the url
function convert_url_to_html($url)
{
    $url_prefix = "<a href =\"" . $url . "\" target=\"_blank\">";
    $url_suffix = "</a>";
    
    $url = $url_prefix . $url . $url_suffix;
    
    return $url;
}

// Code taken from http://www.justin-cook.com/wp/2006/03/31/php-parse-a-string-between-two-strings/
// Returns the substring that is between two specified strings
function get_string_between($string, $start, $end)
{
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);   
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}

?>
