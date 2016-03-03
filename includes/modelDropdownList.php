<?php

$sql = "Select * from inventory group by itemName";
	$result = mysqli_query($link, $sql);
	echo "<select name='model' id='model' class='textboxDropdown'>";

	if ((isset($_GET['reasonCode'])) && (isset($_GET['model'])))
	{
		echo "<option value='".$_GET['model']."' selected >".$_GET['model']."</option>";
		echo "<option value='' disabled >----------------------</option>";
	}	else   {
		echo "<option value='' disabled selected >Please select an action..</option>";
	}

		while($row = mysqli_fetch_array($result)) {
			$itemName = $row['itemName'];
			
			echo "<option value='".$itemName."'>".$itemName."</option>";
		}

	echo "</select>";

?>