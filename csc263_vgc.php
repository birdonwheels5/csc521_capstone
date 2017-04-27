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
							
						}
					?>
					<?php	
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

					?>

					<h4>Please choose a query:</h4>

					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

					<select name="dname">

					<?PHP		
						$queries = array();
						$queries = 
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
							"",
							""
						];
						
						$num_queries = count($queries);
						
						// Form SQL query string
						$query= "SELECT dname FROM department";

						for($i = 0; $i < $num_queries; $i++)
						{
							echo "<option>",$dname,"\n";
							print '<option value="' . $i . '">' . $queries[$i] . '</option>';
						}
					?>

					</select>

					<input type="submit" value="Display Employee Details">

					</form>
					</p>
				</div>
			</div>
			<div class="col-3 empty">	</div>
		</div>
		
	</body>
	
</html>
