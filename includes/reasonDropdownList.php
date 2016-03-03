<?php

$sql = "Select * from inventory_reason";
	$result = mysqli_query($link, $sql);

	echo "<select name='reason' id='reason' class='textboxDropdown'>";

	if ((isset($_GET['reasonCode'])) && (isset($_GET['model'])))
	{
		echo "<option value='' disabled selected >".$_GET['reasonCode']."</option>";
	}	else   {
		echo "<option value='' disabled selected >Please select an action..</option>";
	}

		while($row = mysqli_fetch_array($result)) {
			$id = $row['id'];
			$reason = $row['reason'];

			echo "<option value='".$id."'>".$reason."</option>";
		}

	echo "</select>";

?>