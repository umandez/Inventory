<div id="title"><h2 class='white'>Report By Model</h2></div>

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
 echo "<font color='black'>Show all items with Model Name </font> ";
 include ('/includes/modelDropdownList.php');


 echo " <font color='black'> and show results that are currently </font>";
 include ('/includes/reasonDropdownList.php');

 echo "<br /><br /><center>";
 echo "<input type='submit' class='minimal' name='byModel' value='Select' /></form>";
?> <a href="/?byModel" class='minimal'>Reset</a> <?php
 echo "</center>";

 if (isset($_POST['byModel'])) {
 	$reason = $_POST['reason'];
 	$model = $_POST['model'];
 	if (!$_POST['reason'] == "") {
 		Refresh("byModel&reasonCode=".$reason."&model=".$model."");
 	} else {
 		Refresh("byModel&reasonCode=ANY&model=".$model."");
 	}
 } else {
 	$_POST['byModel'] = false;
 }
	
	if ((isset($_GET['reasonCode'])) && ($_GET['reasonCode'] != "ANY"))
	{
		$model = $_GET['model'];
		$model = urldecode($model);

		$reason = $_GET['reasonCode'];

		$sql = "Select
				  inventory.inventory.assetTag,
				  inventory.inventory.itemName,
				  inventory.inventory.manufacturer,
				  inventory.inventory.serialCode,
				  inventory.inventory.category,
				  inventory.inventory.dateAdded As date,
				  inventory.inventory_reason.reason,
				  inventory.inventory.reasonid,
				  inventory.inventory.capexNo,
				  inventory.inventory.price,
				  inventory.inventory_cat.name As cat_name,
				  inventory.inventory_manufacturer.name As manu_name
				From
				  inventory.inventory Inner Join
				  inventory.inventory_reason On inventory.inventory_reason.id =
				    inventory.inventory.reasonid Inner Join
				  inventory.inventory_cat On inventory.inventory.category =
				    inventory.inventory_cat.id Inner Join
				  inventory.inventory_manufacturer On inventory.inventory.manufacturer =
				    inventory.inventory_manufacturer.id
				Where
				  inventory.inventory.itemName = '$model' And
				  inventory.inventory.reasonid = '$reason'
				Group By
				  inventory.inventory.assetTag, inventory.inventory.reasonid,
				  inventory.inventory_cat.name, inventory.inventory_manufacturer.name";
	}
	elseif ((isset($_GET['reasonCode'])) && ($_GET['reasonCode'] == "ANY"))
	{
		$model = $_GET['model'];
		$model = urldecode($model);

		$sql = "Select
				  inventory.inventory.assetTag,
				  inventory.inventory.itemName,
				  inventory.inventory.manufacturer,
				  inventory.inventory.serialCode,
				  inventory.inventory.category,
				  inventory.inventory.dateAdded As date,
				  inventory.inventory_reason.reason,
				  inventory.inventory.reasonid,
				  inventory.inventory.capexNo,
				  inventory.inventory.price,
				  inventory.inventory_cat.name As cat_name,
				  inventory.inventory_manufacturer.name As manu_name
				From
				  inventory.inventory Inner Join
				  inventory.inventory_reason On inventory.inventory_reason.id =
				    inventory.inventory.reasonid Inner Join
				  inventory.inventory_cat On inventory.inventory.category =
				    inventory.inventory_cat.id Inner Join
				  inventory.inventory_manufacturer On inventory.inventory.manufacturer =
				    inventory.inventory_manufacturer.id
				Where
				  inventory.inventory.itemName = '$model'
				Group By
				  inventory.inventory.assetTag, inventory.inventory.reasonid,
				  inventory.inventory_cat.name, inventory.inventory_manufacturer.name";
	}
	elseif ((($_POST['byModel']) || (!$_POST['byModel'])) && (!isset($_GET['reasonCode'])) && (!isset($_GET['model'])))
	{	
		$sql = "Select
				  inventory.inventory.assetTag,
				  inventory.inventory.itemName,
				  inventory.inventory.manufacturer,
				  inventory.inventory.serialCode,
				  inventory.inventory.category,
				  inventory.inventory.dateAdded As date,
				  inventory.inventory_reason.reason,
				  inventory.inventory.reasonid,
				  inventory.inventory.capexNo,
				  inventory.inventory.price,
				  inventory.inventory_cat.name As cat_name,
				  inventory.inventory_manufacturer.name As manu_name
				From
				  inventory.inventory Inner Join
				  inventory.inventory_reason On inventory.inventory_reason.id =
				    inventory.inventory.reasonid Inner Join
				  inventory.inventory_cat On inventory.inventory.category =
				    inventory.inventory_cat.id Inner Join
				  inventory.inventory_manufacturer On inventory.inventory.manufacturer =
				    inventory.inventory_manufacturer.id
				Group By
				  inventory.inventory.assetTag, inventory.inventory.reasonid,
				  inventory.inventory_cat.name, inventory.inventory_manufacturer.name";		
	}

	$result = mysqli_query($link, $sql);

	if (isset($model)) {
		if ($model == "") {$modelEcho = "ANY";} else {$modelEcho = $model;}
	} else {
		$modelEcho = "ANY";
	}

	if (!isset($_GET['reasonCode'])) {$reason = "ANY";} else {$reason = $_GET['reasonCode'];}

	echo "<p>Showing all results with Model (<font color=red>".$modelEcho."</font>) and Status: <font color=red>".$reason."</font></p>";
	
	echo "<hr><div class='inventory'>";
	echo "<table>";
	echo "<tr>
					<td colspan='1'>Asset No</td>
					<td colspan='1'>Date Deployed</td>
					<td colspan='1'>Product</td>
					<td colspan='1'>Cost</td>
					<td colspan='1'>Capex No</td>
					<td colspan='1'>Status</td>
					<td colspan='1'>Owner</td>
				</tr>";

		while($row = mysqli_fetch_array($result)) {
			$id = $row['assetTag'];
			$name = $row['itemName'];
			$status = $row['reason'];

			$manufacturer = $row['manu_name'];
			$dateAdded = $row['date'];
			$dateAdded = date('d-m-Y', strtotime($dateAdded));
			$capexNo = $row['capexNo'];
			$price = $row['price'];


			$sqlMovements = "Select
							  Max(id) As Max_id
							From
							  inventory_movements
							Where
							  itemid = '".$id."'
							Group By
							  itemid";

			$resultMovements = mysqli_query($link, $sqlMovements);
  			$rowMovements = mysqli_fetch_assoc($resultMovements);
  			$maxid = $rowMovements['Max_id'];

  			$sqlMovementsOwner = "Select owner From inventory_movements Where id = '".$maxid."'";
			$resultMovementsOwner = mysqli_query($link, $sqlMovementsOwner);
  			$rowMovementsOwner = mysqli_fetch_assoc($resultMovementsOwner);

  			$owner = $rowMovementsOwner['owner'];
  			

			echo "<tr>
					<td colspan='1'><a href='/?assetNo=$id'>".$id."</a></td>
					<td colspan='1'>".$dateAdded."</td>
					<td colspan='1'>".$manufacturer." - ".$name."</td>
					<td colspan='1'>£".$price."</td>
					<td colspan='1'>".$capexNo."</td>
					<td colspan='1'>".$status."</td>
					<td colspan='1'>".$owner."</td>
				</tr>";
		}
		echo "</table></div><hr>";
?>