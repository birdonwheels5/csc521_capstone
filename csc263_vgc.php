<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>VGC Database</title>
	    <link rel='stylesheet' type="text/css" href="main.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	
	<body>
		<div class="row">
			<div class="col-3 empty"></div>
			<div class="col-6">
				<div class="object shadow">
					<center><h1>Query VGC Database</h1></center>
					<p>
					<h4>Please choose a query:</h4>

					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

					<select name="query_num">

					<?PHP
						
						// Needs to be the same length as the $queries array
						// Gives the queries names to be displayed
						$query_names = array();
						$query_names = 
						[
							// 0 // 2 columns : Companies, consoles
							"Companies who make consoles",
							// 1 // 1 column: Consoles
							"Consoles ordered by release date descending",
							// 2 // 1 column: Consoles
							"White consoles released by Sony in 2000", // Can change the year so we get a result
							// 3 // All attributes for Game table
							"Xbox exclusive games released in 2009", // Again the date is flexible
							// 4 // 1 column: Number of games
							"Number of games released for Xbox One and PS4 in 2016",
							// 5 // 1 column: Number of net worth
							"Net worth of Nintendo",
							// 6 // All attributes for Game table
							"WiiU exclusive games",
							// 7 // All attributes for Game table
							"PS4 exclusive games rated 8/10 or higher, released between 2015 and 2017", // Rating and year are flexible
							// 8 // All attributes for Game table
							"All games released by Nintendo in 2017",
							// 9 // All attributes for Game table
							"Games developed by 343 Industries and Bungie between 2000 and 2016 that are first person shooters"
							// Should add the admin queries, like add game, delete game, update game
						];
						
						
						$num_queries = count($query_names);

						for($i = 0; $i < $num_queries; $i++)
						{
							print '<option value="' . $i . '" name="">' . $query_names[$i] . '</option>';
						}
					?>

					</select>

					<input type="submit" value="Display Query Results">

					</form>
					</p>
					
					<?php
						// Separate PHP block because this is executed separately from the rest of the code
						if ($_SERVER["REQUEST_METHOD"] == "POST") 
						{
							// Report all PHP errors
							ini_set('display_errors', 1);
							error_reporting(E_ALL);

							$host="127.0.0.1";
							$username="vgc";
							$password="vgc17";
							$database="vgc";

							// Create a new database connect object
							$dbcon=new mysqli($host, $username, $password, $database);

							// Check connection
							if($dbcon->connect_error) die ($dbcon->connect_error);

							// Make the database connection globally
							global $dbcon;
							
							$queries = array();
                        
							// This one is special because I'm not gonna write out g.year LIKE %20xx% a million times.
							// This would be avoided if our database only stored years for release dates instead of mm/dd/yyyy format.
							$query10 = "SELECT Title, Release_Date, Publisher, Genre, Rating FROM Developer as d, Develops as devs, Game as g WHERE (((devs.Dev_ID=1 AND devs.Game_ID=g.Game_ID) OR (devs.Dev_ID=4 AND devs.Game_ID=g.Game_ID) AND devs.Game_ID=g.Game_ID) AND ((g.Release_Date LIKE '%$2000%')";
							for($i = 2001; $i <= 2016; $i++)
							{
							    $query10 .= " OR (g.Release_Date LIKE '%$i%')";
							}
							$query10 .= ")) GROUP BY g.Game_ID ORDER BY g.Title ASC;";

							$queries = 
							[
								// These first five are untested, and probably do not work as is
								"SELECT comp.Name as CompanyName, cons.Name as ConsoleName FROM Company as comp, Console as cons, Makes as m WHERE ((cons.Console_ID=m.Console_ID) AND (comp.Company_ID=m.Company_ID))",
								"SELECT cons.Name as ConsoleName FROM Console as cons ORDER BY cons.Release_Date DESC",
								"SELECT comp.Name as CompanyName, cons.Name as ConsoleName FROM Company as comp, Console as cons, Makes as m, Console_Colors as color WHERE ((cons.Console_ID=m.Console_ID) AND (m.Company_ID=SONY) AND (cons.Console_ID=color.Console_ID) AND (color.Color='white') AND (Release_Date LIKE '%2000%'))",
								"SELECT cons.Name as ConsoleName, g.Name as GameName FROM Game as g, Console as cons, Compat_With as cw WHERE ((cw.Console_ID=30) AND (g.Game_ID=cw.Game_ID) AND (Release_Date LIKE '%2009%'))", // 30 is the console ID for the original XBOX
								"SELECT SUM(g.Game_ID) as Num_Games FROM Compat_with as cw, Game as g WHERE (((cw.Console_ID=32 AND cw.Game_ID=g.Game_ID) OR (cw.Console_ID=20 AND cw.Game_ID=g.Game_ID) AND cw.Game_ID=g.Game_ID) AND ((g.Release_Date LIKE '%$2016%'))) GROUP BY g.Game_ID ORDER BY g.Title ASC;",
								"SELECT d.Net_Worth FROM Developer as d WHERE d.Dev_ID=13;",
								// Idk if this works because I can't insert the data for Compat_With without the Consoles data
								"SELECT Title, Release_Date, Publisher, Genre, Rating FROM Developer as d, Compat_With as cw, Game as g WHERE (((cw.Console_ID=29) AND (cw.Game_ID=g.Game_ID))) GROUP BY g.Game_ID ORDER BY g.Title ASC",
								// Idk if this works because I can't insert the data for Compat_With without the Consoles data
								"SELECT Title, Release_Date, Publisher, Genre, Rating FROM Developer as d, Compat_With as cw, Game as g WHERE (((cw.Console_ID=20) AND (cw.Game_ID=g.Game_ID)) AND ((g.Release_Date LIKE '%2015%') OR (g.Release_Date LIKE '%2016%') OR (g.Release_Date LIKE '%2017%')) AND (g.Rating>=8)) GROUP BY g.Game_ID ORDER BY g.Title ASC",
								"SELECT Title, Release_Date, Publisher, Genre, Rating FROM Developer as d, Develops as devs, Game as g WHERE (((devs.Dev_ID=13) AND (devs.Game_ID=g.game_ID)) AND (g.Release_Date LIKE '%2017%')) GROUP BY g.Game_ID ORDER BY g.Title ASC",
								$query10
							];
							
							// Needs to be the same length as the $queries array
							// Gives the queries names to be displayed
							$query_names = array();
							$query_names = 
							[
								// 0 // 2 columns : Companies, consoles
								"Companies who make consoles",
								// 1 // 1 column: Consoles
								"Consoles ordered by release date descending",
								// 2 // 1 column: Consoles
								"White consoles released by Sony in 2000", // Can change the year so we get a result
								// 3 // All attributes for Game table
								"Xbox exclusive games released in 2009", // Again the date is flexible
								// 4 // 1 column: Number of games
								"Number of games released for Xbox One and PS4 in 2016",
								// 5 // 1 column: Number of net worth
								"Net worth of Nintendo",
								// 6 // All attributes for Game table
								"WiiU exclusive games",
								// 7 // All attributes for Game table
								"PS4 exclusive games rated 8/10 or higher, released between 2015 and 2017", // Rating and year are flexible
								// 8 // All attributes for Game table
								"All games released by Nintendo in 2017",
								// 9 // All attributes for Game table
								"Games developed by 343 Industries and Bungie between 2000 and 2016 that are first person shooters"
								// Should add the admin queries, like add game, delete game, update game
							];
							
							$query_num = $_POST['query_num'];
							print $query_num;
							print "<br/>\n";
							print $query_names[$query_num];
							print "<br/>\n";
							// Debugging
							$query = "NULL";
							
							// Handle the different cases for the different queries
							switch($query_num)
							{
								case 0:
									// Display 2 columns: Companies, Consoles
									$query = $queries[$query_num];
									
									//Execute SQL query and try to receive result
									if ($result = $dbcon->query($query)) 
									{
										// Create table and table header
										echo '<table border="2" cellspacing="2" cellpadding="2">';
										echo '<tr>';
										echo '<th><font face="Arial,Helvetica,sans-serif">Companies</font></th>';
										echo '<th><font face="Arial,Helvetica,sans-serif">Consoles</font></th>';
										echo '</tr>';

										// Fetch object array
										while ($obj = $result->fetch_object()) 
										{
											//Get information
											$company_name = $obj->CompanyName;
											$console_name = $obj->ConsoleName;

											//Display information in a table

											echo '<tr>';
											echo '<td><font face="Arial, Helvetica, sans-serif">';
											echo $company_name;
											echo '</font></td>';
											echo '<td><font face="Arial, Helvetica, sans-serif">';
											echo $console_name;
											echo '</font></td>';
											echo '</tr>';

										}

										echo '</table>';

										// free result set
										$result->close();
									}
									
									break;
								
								case ($query_num == 1 || $query_num == 2):
									// Display 1 column: Consoles
									$query = $queries[$query_num];
									
									//Execute SQL query and try to receive result
									if ($result = $dbcon->query($query)) 
									{
										// Create table and table header
										echo '<table border="2" cellspacing="2" cellpadding="2">';
										echo '<tr>';
										echo '<th><font face="Arial,Helvetica,sans-serif">Consoles</font></th>';
										echo '</tr>';

										// Fetch object array
										while ($obj = $result->fetch_object()) 
										{
											// Get information
											$console_name = $obj->ConsoleName;

											// Display information in a table

											echo '<tr>';
											echo '<td><font face="Arial, Helvetica, sans-serif">';
											echo $console_name;
											echo '</font></td>';
											echo '</tr>';

										}

										echo '</table>';

										// free result set
										$result->close();
									}
									break;
								
								case 4:
									// Display 1 column: Number of Games
									$query = $queries[$query_num];
									
									//Execute SQL query and try to receive result
									if ($result = $dbcon->query($query)) 
									{
										// Create table and table header
										echo '<table border="2" cellspacing="2" cellpadding="2">';
										echo '<tr>';
										echo '<th><font face="Arial,Helvetica,sans-serif">Number of Games</font></th>';
										echo '</tr>';

										// Fetch object array
										while ($obj = $result->fetch_object()) 
										{
											// Get information
											$num_games = $obj->Num_Games;

											// Display information in a table

											echo '<tr>';
											echo '<td><font face="Arial, Helvetica, sans-serif">';
											echo $num_games;
											echo '</font></td>';
											echo '</tr>';

										}

										echo '</table>';

										// free result set
										$result->close();
									}
									break;
								
								case 5: 
									// Display 1 column: Number of net worth
									$query = $queries[$query_num];
									
									//Execute SQL query and try to receive result
									if ($result = $dbcon->query($query)) 
									{
										// Create table and table header
										echo '<table border="2" cellspacing="2" cellpadding="2">';
										echo '<tr>';
										echo '<th><font face="Arial,Helvetica,sans-serif">Net Worth</font></th>';
										echo '</tr>';

										// Fetch object array
										while ($obj = $result->fetch_object()) 
										{
											// Get information
											$net_worth = $obj->Net_Worth;

											// Display information in a table

											echo '<tr>';
											echo '<td><font face="Arial, Helvetica, sans-serif">';
											echo $net_worth;
											echo '</font></td>';
											echo '</tr>';

										}

										echo '</table>';

										// free result set
										$result->close();
									}
									break;
								
								default:
									// All attributes in the Game table (minus the id number)
									$query = $queries[$query_num];
									
									//Execute SQL query and try to receive result
									if ($result = $dbcon->query($query)) 
									{
										// Create table and table header
										echo '<table border="2" cellspacing="2" cellpadding="2">';
										echo '<tr>';
										echo '<th><font face="Arial,Helvetica,sans-serif">Title</font></th>';
										echo '<th><font face="Arial,Helvetica,sans-serif">Release Date</font></th>';
										echo '<th><font face="Arial,Helvetica,sans-serif">Publisher</font></th>';
										echo '<th><font face="Arial,Helvetica,sans-serif">Genre</font></th>';
										echo '<th><font face="Arial,Helvetica,sans-serif">Rating</font></th>';
										echo '</tr>';

										// Fetch object array
										while ($obj = $result->fetch_object()) 
										{
											//Get information
											$title = $obj->Title;
											$release_date = $obj->Release_Date;
											$publisher = $obj->Publisher;
											$genre = $obj->Genre;
											$rating = $obj->Rating;

											//Display information in a table
											
											echo '<tr>';
											echo '<td><font face="Arial, Helvetica, sans-serif">';
											echo $title;
											echo '</font></td>';
											echo '<td><font face="Arial, Helvetica, sans-serif">';
											//echo $release_date;
											echo substr($release_date, 6);
											echo '</font></td>';
											echo '<td><font face="Arial, Helvetica, sans-serif">';
											echo $publisher;
											echo '</font></td>';
											echo '<td><font face="Arial, Helvetica, sans-serif">';
											echo $genre;
											echo '</font></td>';
											echo '<td><font face="Arial, Helvetica, sans-serif">';
											echo $rating;
											echo '</font></td>';
											echo '</tr>';

										}

										echo '</table>';

										// free result set
										$result->close();
									}
							}
							
							print "<br/><br/>\n <h2>MySQL Query: </h2>" . $queries[$query_num];
						}
						
					?>
					
				</div>
			</div>
			<div class="col-3 empty">	</div>
		</div>
		
	</body>
	
</html>
