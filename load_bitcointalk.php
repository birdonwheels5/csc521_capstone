<link rel='stylesheet' type="text/css" href="main.css">
<?php
    
    // When opened, this file will fetch all tweets from the database and print them out to the webpage.
    // The output is HTML formatted.
    
    include "func/general.php";
    include "func/scraper.php";
    
    // Load database settings from config file
    $settings = array();
    $settings = load_config();
    
    $mysql_user = $settings[0];
    $mysql_host = $settings[1];
    $mysql_pass = $settings[2];
    $mysql_database = $settings[3];
    
    // This is also the size of the array
    $num_posts = 8;
    
    // Establish connection to the database
    $con = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_database);
    
    if (mysqli_connect_errno()) 
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $database_posts = get_database_forum_posts($con, $num_posts, "Bitcointalk");
    
    // List the posts. The order is taken care of by the database.
    for($i = 0; $i < $num_posts; $i++)
    {
        $time_since_post = time_since($database_posts[3][$i]);
        
        $post_url = $database_posts[0][$i];
        $username = $database_posts[1][$i];
        $post_title = $database_posts[2][$i];
        
        // Print the rest of the post and the time since it was posted
        print "<div class='tweet'>";
        print "$username: $post_title \n<br/> Topic link: <a href=$post_url>$post_url</a> </n><br/> <i> $time_since_post ago </i>";
        print "</div>";
        print "<br/><br/>";
    }
    
?>
