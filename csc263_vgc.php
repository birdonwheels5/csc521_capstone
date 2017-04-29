<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>VGC Database</title>
	    <link rel='stylesheet' type="text/css" href="main.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	
	<body>
		<div class="row center">
			<div class="col-3 empty"></div>
			<div class="col-6">
				<div class="object shadow">
					<center><h1>Query VGC Database</h1></center>
					<p>
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
							$queries = // This is copy pasted from the $queries array below
							[
								"SELECT comp.Name as CompanyName, cons.Name as ConsoleName FROM Company as comp, Console as cons, Makes as m WHERE ((cons.Console_ID=m.Console_ID) AND (comp.Company_ID=m.Company_ID))",
								"",
								"",
								"",
								"",
								"",
								"",
								"",
								"",
								""
							];
							
							//Execute SQL query and try to receive result
							if ($result = $dbcon->query($query)) 
							{
							    	// Create table and table header
								echo '<table border="2" cellspacing="2" cellpadding="2">';
							   	echo '<tr>';
							    	echo '<th><font face="Arial,Helvetica,sans-serif">Last Name</font></th>';
							    	echo '<th><font face="Arial,Helvetica,sans-serif">First Name</font></th>';
							    	echo '<th><font face="Arial,Helvetica,sans-serif">Address</font></th>';
							    	echo '<th><font face="Arial,Helvetica,sans-serif">Salary</font></th>';
							    	echo '<th><font face="Arial,Helvetica,sans-serif">Department</font></th>';
							    	echo '</tr>';
								
								// Fetch object array
								while ($obj = $result->fetch_object()) 
								{
									//Get employee information
									$firstName = $obj->fname;
									$lastName = $obj->lname;
									$address = $obj->address;
									$salary = $obj->salary;
									$dname = $obj->dname;
									
									//Display employee information in a table
									
									echo '<tr>';
									echo '<td><font face="Arial, Helvetica, sans-serif">';
									echo $lastName;
									echo '</font></td>';
									echo '<td><font face="Arial, Helvetica, sans-serif">';
									echo $firstName;
									echo '</font></td>';
									echo '<td><font face="Arial, Helvetica, sans-serif">';
									echo $address;
									echo '</font></td>';
									echo '<td><font face="Arial, Helvetica, sans-serif">';
									echo $salary;
									echo '</font></td>';
									echo '<td><font face="Arial, Helvetica, sans-serif">';
									echo $dname;
									echo '</font></td>';
									echo '</tr>';

									}
								
							    	echo '</table>';
							    	
								// free result set
							    	$result->close();
								}
						}
					?>

					<h4>Please choose a query:</h4>

					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

					<select name="query">

					<?PHP		
						$queries = array();
						$queries = 
						[
							"SELECT comp.Name as CompanyName, cons.Name as ConsoleName FROM Company as comp, Console as cons, Makes as m WHERE ((cons.Console_ID=m.Console_ID) AND (comp.Company_ID=m.Company_ID))",
							"SELECT cons.Name as ConsoleName FROM Console as cons ORDER BY cons.Release_Date DESC",
							"SELECT comp.Name as CompanyName, cons.Name as ConsoleName FROM Company as comp, Console as cons, Makes as m, Console_Colors as color WHERE ((cons.Console_ID=m.Console_ID) AND (m.Company_ID=SONY) AND (cons.Console_ID=color.Console_ID) AND (color.Color='white') AND (Release_Date LIKE '%2000%'))",
							"SELECT cons.Name as ConsoleName, g.Name as GameName FROM Game as g, Console as cons, Compat_With as cw WHERE ((cw.Console_ID=30) AND (g.Game_ID=cw.Game_ID) AND (Release_Date LIKE '%2009%'))", // 30 is the console ID for the original XBOX
							"",
							"",
							"",
							"",
							"",
							""
						];
						
						// Needs to be the same length as the $queries array
						// Gives the queries names to be displayed
						$query_names = array();
						$query_names = 
						[
							// 1 // 2 columns : Companies, consoles
							"Companies who make consoles",
							// 2 // 1 column: Consoles
							"Consoles ordered by release date descending",
							// 3 // 1 column: Consoles
							"White consoles released by Sony in 2000", // Can change the year so we get a result
							// 4 // All attributes for Game table
							"Xbox exclusive games released in 2009", // Again the date is flexible
							// 5 // 1 column: Number of games
							"Number of games released for Xbox One and PS4 in 2016",
							// 6 // 1 column: Number of net worth
							"Net worth of Nintendo",
							// 7 // All attributes for Game table
							"WiiU exclusive games",
							// 8 // All attributes for Game table
							"PS4 exclusive games rated 8/10, released between 2015 and 2017", // Rating and year are flexible
							// 9 // All attributes for Game table
							"All games released by Nintendo in 2016",
							// 10 // All attributes for Game table
							"Games developed by 343 Industries and Bungie between 2000 and 2016 that are first person shooters"
							// Should add the admin queries, like add game, delete game, update game
						];
						
						
						$num_queries = count($queries);

						for($i = 0; $i < $num_queries; $i++)
						{
							print '<option value="' . $i . '">' . $query_names[$i] . '</option>';
						}
					?>

					</select>

					<input type="submit" value="Display Query Results">

					</form>
					</p>
				</div>
			</div>
			<div class="col-3 empty">	</div>
		</div>
		
	</body>
	
</html>
