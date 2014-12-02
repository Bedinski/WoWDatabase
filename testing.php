<?php

require 'db_connection.php';	// DB credentials

$sql = "SELECT DISTINCT Item FROM Salim.Auction WHERE RAND!=0";

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