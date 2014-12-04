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
			<form action="simple.php" method="get">
				<div class="input-group">
					<input type="text" class="form-control" id="simpleTextField" placeholder="Simple text search">
					<span class="input-group-btn">
						<button class="btn btn-success" id="simpleTextButton" type="button">Search</button>
					</span>
				</div>
			</form>
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
	
	echo "<div class='resultHeader'><h1>Advanced Search</h1></div>";
	$totalFormInputs = 3;
	$tables = array($totalFormInputs);

	//========================================
	//======== FILTER BY AUCTION TYPE ========
	$auctionType = $_GET['auctionType'];
	switch($auctionType) {
		case "both":	//auction AND buyout
			$tables[0] = "SELECT * FROM Salim.Auction";
			break;
		case "auction":	//auction ONLY, so buyout=0
			$tables[0] = "SELECT * FROM Salim.Auction WHERE Buyout=0";
			break;
		default:		//default = auction AND buyout
			$tables[0] = "SELECT * FROM Salim.Auction";
			break;
	}

	//=====================================
	//======== FILTER BY ITEM TYPE ========
	$itemType = $_GET['itemType'];
	switch($itemType) {
		case "weapon":

			break;
		case "armor":

			break;
		//etc...
		default:

			break;
	}

	//=====================================
	//======== FILTER BY ITEM NAME ========
	$itemName = $_GET['itemName'];
	/*sql using wildcards for the name*/

	//======================================
	//======== FILTER BY OWNER NAME ========
	$ownerName = $_GET['ownerName'];
	/*sql using wildcards for the name*/

	//==================================
	//======== FILTER BY PRICE ========
	$minPriceUP = $_GET['minPrice'];
	$maxPriceUP = $_GET['maxPrice'];
	$gold = 10000;	//conversion factor
	$silver = 100;	//conversion factor
	$minPriceP = 1;				//default
	$maxPriceP = 9999999*$gold;	//default

	if(!preg_match("/[\d]*g[\d]*s[\d]*c/",$minPriceUP)) {
		echo "<p class='queryError'>Error entering minimum price. Setting minimum price to 0g0s1c.</p>";
	}
	else {	//format is good so parse the string to an int
		$parseGold = preg_replace("/g[\d]*s[\d]*c/","",$minPriceUP);
		$parseSilver = preg_replace("/[\d]*g/","",$minPriceUP);
		$parseSilver = preg_replace("/s[\d]*c/","",$parseSilver);
		$parseCopper = preg_replace("/[\d]*g[\d]*s/","",$minPriceUP);
		$parseCopper = preg_replace("/c/","",$parseCopper);
		$minPriceP = $parseGold*$gold + $parseSilver*$silver + $parseCopper;
	}
	if(!preg_match("/[\d]*g[\d]*s[\d]*c/",$maxPriceUP)) {
		echo "<p class='queryError'>Error entering maximum price. Setting maximum price to 9999999g0s0c.</p>";
	}
	else {	//format is good so parse the string to an int
		$parseGold = preg_replace("/g[\d]*s[\d]*c/","",$maxPriceUP);
		$parseSilver = preg_replace("/[\d]*g/","",$maxPriceUP);
		$parseSilver = preg_replace("/s[\d]*c/","",$parseSilver);
		$parseCopper = preg_replace("/[\d]*g[\d]*s/","",$maxPriceUP);
		$parseCopper = preg_replace("/c/","",$parseCopper);
		$maxPriceP = $parseGold*$gold + $parseSilver*$silver + $parseCopper;
	}


	//===================================
	//======== FILTER BY QUALITY ========
	//variables for the rest of the qualities


	$sql = "SELECT * FROM (" . $tables[0] . ") WHERE ROWNUM<=25";
	$statement = oci_parse($connection, $sql);
	oci_execute($statement);

	//create HTML results table headers
	echo "<table border=1 id='resultTable' class='resultsTable tableSorter' align='center'>\n";
	echo "\t<thead><tr>
		<th>Auction ID<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
		<th>Item Data<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
		<th>Owner<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
		<th>Owner Realm<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
		<th>Bid<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
		<th>Buyout<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
		<th>Quantity<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
		<th>Time Left<span class='glyphicon glyphicon glyphicon-resize-vertical' aria-hidden='true'></span></th>
	</tr></thead><tbody>\n";
	
	//create HTML results table entries
	while ($row = oci_fetch_array($statement)) {
		$bidGold = (int)($row[4]/10000);
		$bidSilver = (int)(($row[4]-$bidGold*10000)/100);
		$bidCopper = (int)($row[4]-($bidSilver*100+$bidGold*10000));
		$buyGold = (int)($row[5]/10000);
		$buySilver = (int)(($row[5]-$buyGold*10000)/100);
		$buyCopper = (int)($row[5]-($buySilver*100+$buyGold*10000));

		echo "\t<tr>
			<td>" . $row[0] . "</td>
			<td class='item'><a href='#' rel='item=" . $row[1] . "'>Loading...</a></td>
			<td>" . $row[2] . "</td>
			<td>" . $row[3] . "</td>
			<td>" . $bidGold."<span class='goldGlyph'>&bull;</span>". $bidSilver."<span class='silverGlyph'>&bull;</span>". $bidCopper."<span class='copperGlyph'>&bull;</span>" . "</td>
			<td>" . $buyGold ."<span class='goldGlyph'>&bull;</span>". $buySilver."<span class='silverGlyph'>&bull;</span>". $buyCopper."<span class='copperGlyph'>&bull;</span>" . "</td>
			<td>" . $row[6] . "</td>
			<td>" . $row[7] . "</td>
		</tr>\n";
	}
	echo "</tbody></table>\n";

	//close the connection
	oci_free_statement($statement);
	oci_close($connection);
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