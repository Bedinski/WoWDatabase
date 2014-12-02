<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Search Results</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="project.css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
  </head>
	
  <body>
	<header id="navbar">
		<form method="get" action="login.html">
			<button type="submit" class="btn btn-success" type="button" id="loginButton">Login</button>
		</form>
		<div class="col-md-6 col-md-offset-3" id="simpleSearchBar">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Simple text search">
				<span class="input-group-btn">
					<button class="btn btn-success" type="button">Search</button>
				</span>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<form method="get" action="index.html">
				<button class="btn btn-primary btn-block">Home Page</button>
			</form>
		</div>
	</div>


	<?php
	require 'db_connection.php';	//login to CISE Oracle

	switch($_GET['spec_cat']) {	//determines the pages title based on which Special Category the user chose
		case "low_prices":
			echo "<div class='resultHeader'><h1>Unusually Low Prices</h1></div>";
			break;
		case "high_prices":
			echo "<div class='resultHeader'><h1>Unusually High Prices</h1></div>";
			break;
		case "rare_items":
			echo "<div class='resultHeader'><h1>Rare Items</h1></div>";
			break;
		case "common_items":
			echo "<div class='resultHeader'><h1>Common Items</h1></div>";
			break;
		case "high_volume":
			echo "<div class='resultHeader'><h1>Unusually High Volumes</h1></div>";
			break;
		case "low_volume":
			echo "<div class='resultHeader'><h1>Unusually Low Volume</h1></div>";
			break;
		case "newest_items":
			echo "<div class='resultHeader'><h1>Newest Items</h1></div>";
			break;
		case "oldest_items":
			echo "<div class='resultHeader'><h1>Oldest Items</h1></div>";
			break;
		default:	//URL error
			echo "<div class='resultHeader'><h1>Category Does Not Exist!</h1></div>";
	}

	$sql = "SELECT *
			FROM (
			  SELECT T.*, ROWNUM rnum
			  FROM (
				SELECT * 
				FROM Salim.Auction
			  ) T
			  WHERE ROWNUM <= 25 --max row
			)
			WHERE rnum >= 1 --min row";
	// Uncomment this to see the effect (it will replace the previous one)
	// This query will only show Frostweave Cloth (most sold).
	// Once we have the Item table, we should be able to join those two and get the proper name and stats.
	/*
	$sql = "SELECT *
			FROM (
			  SELECT T.*, ROWNUM rnum
			  FROM (
				SELECT * 
				FROM Salim.Auction
				WHERE Item = 33470 --Frostweave Cloth
			  ) T
			  WHERE ROWNUM <= 25 --max row
			)
			WHERE rnum >= 1 --min row";
	*/
	$statement = oci_parse($connection, $sql);
	oci_execute($statement);

	// print table
	// Unfortunately, I could not find a way to make Oracle refer to the columns by name.
	// We will just have to use 0-10 indexes for the columns we need.
	echo "<table border=1>\n";
	echo "\t<tr><td>Auction</td><td>Item</td><td>Owner</td><td>Owner Realm</td><td>Bid</td><td>Buyout</td><td>Quantity</td><td>Time Left</td><td>Rand</td><td>Seed</td><td>Context</td></tr>\n";
	while ($row = oci_fetch_array($statement)) {
		echo "\t<tr><td>" . $row[0] . "</td><td>" . 
							$row[1] . "</td><td>" . 
							$row[2] . "</td><td>" . 
							$row[3] . "</td><td>" . 
							$row[4] . "</td><td>" . 
							$row[5] . "</td><td>" . 
							$row[6] . "</td><td>" . 
							$row[7] . "</td><td>" . 
							$row[8] . "</td><td>" . 
							$row[9] . "</td><td>" . 
							$row[10] . "</td></tr>\n";
	}
	echo "</table>\n";


	// VERY important to close Oracle Database Connections and free statements!
	oci_free_statement($statement);
	oci_close($connection);
	?>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script src="project.js"></script>
	<script type="text/javascript" src="http://static.wowhead.com/widgets/power.js"></script><script>var wowhead_tooltips = { "colorlinks": true, "iconizelinks": true, "renamelinks": true }</script>
  </body>
</html>