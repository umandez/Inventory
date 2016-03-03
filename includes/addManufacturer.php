<p><b>Note:</b> Item's cannot be deleted from this list if assets are still attached to them!</p>
<div id="title"><h2 class='white'>Manufacturers</h2></div>

<?php

$sql = "Select * from inventory_manufacturer";
	$result = mysqli_query($link, $sql);

	echo "<div class='inventory'>";
	echo "<form action='' method='post'>";
	echo "<table>";
	echo "<tr>
					<td colspan='2'>ID</td>
					<td colspan='2'>Manufacturer</td>
					<td colspan='1'>Option</td>
				</tr>";

		while($row = mysqli_fetch_array($result)) {
			$id = $row['id'];
			$name = $row['name'];

			echo "<tr>
					<td colspan='2'>".$id."</td>
					<td colspan='2'>".$name."</td>
					<td colspan='1'><input type='checkbox' name='manu[]' value='".$name."'></td>
				</tr>";
		}

		echo "</table></div><br />";
//		echo "<input type='submit' class='btnRed' name='deleteItem' value='Delete' />";
	echo "</form><br />";

if (isset($_POST['deleteItem'])) {
	foreach($_POST['manu'] as $val) {
	   mysqli_query($link,"DELETE FROM inventory_manufacturer WHERE name = '".$val."'");
	}
  $_SESSION['message'] = "<p class='success'>Success! Manufacturer has been removed!</p>";
  Refresh("addManufacturer");	
}
?>

<br />
<div id="title"><h2 class='white'>Add New</h2></div>
<form action="" method="post">
<div class="inventory">
        <table>
            <tr>
               <td colspan="2">
               <b>Manufacturer</b>
               </td>
               <td colspan="2">
                <?php dropdownTextBox("select name from inventory_manufacturer","name","manu") ?>
               </td>
            </tr>
        </table>
</div>
<br />
	<center><input type="submit" class="minimal" name="addManu" value="Add Manufacturer" /></center>    
</form>
<?php

// Database Insert
if (isset($_SESSION['message'])) {echo $_SESSION['message'];}
if (isset($_POST['addManu'])) {

$update = true;	
	//intval($_POST['assetTag']);

	if ((!$_POST['manu'] == NULL)) 
	{
		$manu = $_POST['manu'];

	if ($update)
		{
			mysqli_query($link,"INSERT into inventory_manufacturer(name) VALUES('$manu')");
		
		$_SESSION['message'] = "<p class='success'>Success! ".$manu." has been inserted!</p>";
		}
		
		Refresh("addManufacturer");	
	}
	elseif (!$update) {
		$_SESSION['message'] = "<p class='error'>Please fill out all fields correctly!</p>";
		Refresh("addManufacturer");	
	}
}

?>