<div id="title"><h2 class='white'>White Board V2</h2></div>

<?php

if (!isset($_GET['interval'])) {
	$interval = "30";
} else {
	$interval = $_GET['interval'];
}
if (!isset($_GET['accounted'])) {
	$accounted = "N";
} else {
	$accounted = $_GET['accounted'];
}

 echo "<form action='' method='post'>";
 echo "<font color='black'>Show all results that are</font> <select name='accounted' id='accounted' class='textboxDropdown'>";
 	echo "<option value='N'>Not Accounted</option>";
 	echo "<option value='Y'>Accounted</option>";
 echo "</select>";

 echo " <font color='black'>between NOW and the</font>  <select name='xdaysDD' id='xdaysDD' class='textboxDropdown'>";
 echo "<option value='' disabled selected >last ".$interval." days.</option>";
 echo "<option value='5'>Last 5 Days</option>";
  echo "<option value='10'>Last 10 Days</option>";
   echo "<option value='15'>Last 15 Days</option>";
    echo "<option value='20'>Last 20 Days</option>";
     echo "<option value='25'>Last 25 Days</option>";
      echo "<option value='30'>Last 30 Days</option>";
       echo "<option value='60'>Last 60 Days</option>";
        echo "<option value='90'>Last 90 Days</option>";
         echo "<option value='120'>Last 120 Days</option>";
          echo "<option value='365'>Last 365 Days</option>";
 echo "</select>";
 echo " <input type='submit' class='minimal' name='xdays' value='Select' />";
 echo "</form>";

 if (isset($_POST['xdays'])) {
 	$interval = $_POST['xdaysDD'];
 	$accounted = $_POST['accounted'];
 	if (!$_POST['xdaysDD'] == "") {
 		Refresh("accounting&interval=".$interval."&accounted=".$accounted."");
 	} else {
 		Refresh("accounting&interval=30&accounted=".$accounted."");
 	}
 }

/*	$sql = "SELECT  *
		FROM    inventory
		WHERE   dateAdded BETWEEN NOW() - INTERVAL $interval DAY AND NOW() AND accounted = '$accounted'";
*/

	$sql = "Select
			  inventory.inventory.dateAdded,
			  inventory.inventory.accounted,
			  Max(inventory.inventory_movements.id) As Max_id,
			  inventory.inventory_movements.reasonid,
			  inventory.inventory_movements.owner,
			  DATE_FORMAT(inventory.inventory_movements.date, '%d-%m-%Y %H:%i:%s') As date,
			  inventory.inventory.capexNo,
			  inventory.inventory.price,
			  inventory.inventory.serialCode,
			  inventory.inventory.itemName,
			  inventory.inventory.assetTag
			From
			  inventory.inventory Inner Join
			  inventory.inventory_movements On inventory.inventory_movements.itemid =
			    inventory.inventory.assetTag
			Where
			  inventory.inventory_movements.date Between CURDATE() - Interval $interval Day And CURDATE() And
			  inventory.inventory.accounted = '$accounted' And
			  inventory.inventory_movements.reasonid = '2'
			Group By
			  inventory.inventory.dateAdded, inventory.inventory.accounted,
			  inventory.inventory_movements.reasonid, inventory.inventory.capexNo,
			  inventory.inventory.price, inventory.inventory.serialCode,
			  inventory.inventory.itemName, inventory.inventory.assetTag";

	$result = mysqli_query($link, $sql);

	if ($accounted == 'Y') {$accountedText = "";} else {$accountedText = "<font color='red'>not</font>";}

	echo "<p>Showing all results ".$accountedText." accounted between NOW and the last ".$interval." days. All these Assets <font color='red'>have been deployed!</font></p>";
	echo "<hr><div class='inventory'>";
	echo "<form action='' method='post'>";
	echo "<table>";
	echo "<tr>
					<td colspan='1'>Asset No</td>
					<td colspan='1'>Date Deployed</td>
					<td colspan='1'>Product</td>
					<td colspan='1'>Capex No</td>
					<td colspan='1'>Cost</td>
					<td colspan='1'>Owner</td>
					<td colspan='1'>Accounted</td>
				</tr>";

		while($row = mysqli_fetch_array($result)) {
			$id = $row['assetTag'];
			$name = $row['itemName'];

			$dateAdded = $row['date'];
			//$dateAdded = date('d-m-Y', strtotime($dateAdded));

			$owner = $row['owner'];
			$capexNo = $row['capexNo'];
			$price = $row['price'];

			echo "<tr>
					<td colspan='1'><a href='/?assetNo=$id'>".$id."</a></td>
					<td colspan='1'>".$dateAdded."</td>
					<td colspan='1'>".$name."</td>
					<td colspan='1'>".$capexNo."</td>
					<td colspan='1'>£".$price."</td>
					<td colspan='1'>".$owner."</td>
					<td colspan='1'><input type='checkbox' name='account[]' value='".$id."'></td>
				</tr>";
		}
		echo "</table></div><hr>";

		if (isset($_SESSION['accmessage'])) {
   			echo $_SESSION['accmessage'];
		}

		echo "<hr><center><input type='submit' class='minimal' name='accountItems' value='Update' /></center>";
	echo "</form><br />";

if (isset($_POST['accountItems'])) {

	$count = 0;

	foreach($_POST['account'] as $val) {
	   mysqli_query($link,"UPDATE inventory SET accounted = 'Y' WHERE assetTag = '$val' ");
	   $count = $count + 1;
	}
  $_SESSION['accmessage'] = "<p class='success'>Success! ".$count." item(s) have been accounted!</p>";
  Refresh("accounting");	
}

?>