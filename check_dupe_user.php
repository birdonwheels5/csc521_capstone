<?php
    include 'helper_functions.php';
    $username = $_GET['u'];
    
    // No need to validate/clean the user input because we will be hashing it anyway
    $uuid = hash("sha256", $username);
    if(is_array(get_user_data($uuid)))
    {
        print "Username unavailable.";
    }
    else
    {
        print "Username available!";
    }
    
?>
