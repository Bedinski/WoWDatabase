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

	<div class="row rowFix">
		<div class="col-md-6 col-md-offset-3">
			<form method="get" action="index.html">
				<button class="btn btn-primary btn-block">Home Page</button>
			</form>
		</div>
	</div>


	<?php
	require 'db_connection.php';	//login to CISE Oracle

	$isUrlValid = true;
	switch($_GET['spec_cat']) {	//determines the page's title and SQL command based on which Special Category the user chose
		case "low_prices":
			echo "<div class='resultHeader'><h1>Unusually Low Prices</h1></div>";
			//$sql = "";
			break;
		case "high_prices":
			echo "<div class='resultHeader'><h1>Unusually High Prices</h1></div>";
			//$sql = "";
			break;
		case "rare_items":
			echo "<div class='resultHeader'><h1>Rare Items</h1></div>";
			//$sql = "";
			break;
		case "common_items":
			echo "<div class='resultHeader'><h1>Common Items</h1></div>";
			//$sql = "";
			break;
		case "high_volume":
			echo "<div class='resultHeader'><h1>Unusually High Volumes</h1></div>";
			//$sql = "";
			break;
		case "low_volume":
			echo "<div class='resultHeader'><h1>Unusually Low Volume</h1></div>";
			//$sql = "";
			break;
		case "newest_items":
			echo "<div class='resultHeader'><h1>Newest Items</h1></div>";
			$sql = "SELECT Auc,Item,OwnerRealm,Bid,Buyout,Quantity,Timeleft FROM Salim.Auction WHERE Timeleft='VERY_LONG' AND ROWNUM<=25";
			break;
		case "oldest_items":
			echo "<div class='resultHeader'><h1>Oldest Items</h1></div>";
			$sql = "SELECT * FROM Salim.Auction WHERE Timeleft='SHORT'";
			break;
		default:	//URL error
			$isUrlValid = false;
			echo "<div class='resultHeader'><h1>Category Does Not Exist!</h1></div>";
			$sql = "";
			break;
	}

	if($isUrlValid) {
		//TOTAL DISTINCT ITEMS: $sql = "SELECT SUM(Total) FROM (SELECT DISTINCT Item,COUNT(Item) AS Total FROM Salim.Auction GROUP BY Item)";
		//TOTAL ITEMS BEING SOLD: 
		//$sql = "SELECT SUM(Item) FROM (SELECT DISTINCT Item,COUNT(Item) AS Total FROM Salim.Auction GROUP BY Item)";
		
		$statement = oci_parse($connection, $sql);
		oci_execute($statement);

		echo "<table border=1 id='resultTable' class='resultsTable tableSorter' align='center'>\n";
		echo "\t<thead><tr>
				<th>Auction ID<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
				<th>Item Data<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
				<th>Owner Realm<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
				<th>Bid<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
				<th>Buyout<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
				<th>Quantity<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
				<th>Time Left<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
			</tr></thead><tbody>\n";
		while ($row = oci_fetch_array($statement)) {
			$bidGold = (int)($row[3]/10000);
			$bidSilver = (int)(($row[3]-$bidGold*10000)/100);
			$bidCopper = (int)($row[3]-($bidSilver*100+$bidGold*10000));
			$buyGold = (int)($row[4]/10000);
			$buySilver = (int)(($row[4]-$buyGold*10000)/100);
			$buyCopper = (int)($row[4]-($buySilver*100+$buyGold*10000));

			echo "\t<tr>
						<td>" . $row[0] . "</td>
						<td class='item'><a href='#' rel='item=" . $row[1] . "'>Loading...</a></td>
						<td>" . $row[2] . "</td>
						<td>" . $bidGold."<span class='goldGlyph'>&bull;</span>". $bidSilver."<span class='silverGlyph'>&bull;</span>". $bidCopper."<span class='copperGlyph'>&bull;</span>" . "</td>
						<td>" . $buyGold."<span class='goldGlyph'>&bull;</span>". $buySilver."<span class='silverGlyph'>&bull;</span>". $buyCopper."<span class='copperGlyph'>&bull;</span>" . "</td>
						<td>" . $row[5] . "</td>
						<td>" . $row[6] . "</td>
					</tr>\n";
		}
		echo "</tbody></table>\n";

		/*echo "<table border=1 class='resultsTable' align='center'>\n";
		echo "\t<tr>
					<td>Total Distinct Items</td>
				</tr>\n";
		while ($row = oci_fetch_array($statement)) {
			echo "\t<tr>
						<td>" . $row[0] . "</td>
					</tr>\n";
		}
		echo "</table>\n";*/

		oci_free_statement($statement);		//IMPORTANT!!
	}
	else {
		echo "<p class='errorReport'>Sorry, something went wrong! Please return to the homepage <a href='index.html'>here</a> and select one of the options under the 'Special Category' section.</p>";
	}
	
	oci_close($connection);		//IMPORTANT!!
	?>


	<footer class="col-xs-12">
		<hr>
		<p>Made by <a href="#">Jeremy Baker</a>, <a href="#">Salim Chaouqi</a>, <a href="#">Igor Vakulenko</a>, and <a href="#">Ryan Hessen</a>.</p>
	</footer>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script src="project.js"></script>
	<script src="jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="http://static.wowhead.com/widgets/power.js"></script><script>var wowhead_tooltips = { "colorlinks": true, "iconizelinks": true, "renamelinks": true }</script>
  </body>
</html>