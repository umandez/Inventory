<p><b>Note:</b> Item's cannot be deleted from this list if assets are still attached to them!</p>
<div id="title"><h2 class='white'>Categories</h2></div>

<?php

$sql = "Select * from inventory_cat";
	$result = mysqli_query($link, $sql);

	echo "<div class='inventory'>";
	echo "<form action='' method='post'>";
	echo "<table>";
	echo "<tr>
					<td colspan='2'>ID</td>
					<td colspan='2'>Category</td>
					<td colspan='1'>Option</td>
				</tr>";

		while($row = mysqli_fetch_array($result)) {
			$id = $row['id'];
			$name = $row['name'];

			echo "<tr>
					<td colspan='2'>".$id."</td>
					<td colspan='2'>".$name."</td>
					<td colspan='1'><input type='checkbox' name='cat[]' value='".$name."'></td>
				</tr>";
		}

		echo "</table></div><br />";
//		echo "<input type='submit' class='btnRed' name='deleteItem' value='Delete' />";
	echo "</form><br />";

if (isset($_POST['deleteItem'])) {
	foreach($_POST['cat'] as $val) {
	   mysqli_query($link,"DELETE FROM inventory_cat WHERE name = '".$val."'");
	}
  $_SESSION['message'] = "<p class='success'>Success! Category has been removed!</p>";
  Refresh("addCat");	
}
?>

<br />
<div id="title"><h2 class='white'>Add New</h2></div>
<form action="" method="post">
<div class="inventory">
        <table>
            <tr>
               <td colspan="2">
               <b>Category</b>
               </td>
               <td colspan="2">
                <?php dropdownTextBox("select name from inventory_cat","name","cat") ?>
               </td>
            </tr>
        </table>
</div>
<br />
	<center><input type="submit" class="minimal" name="addCat" value="Add Category" /></center>    
</form>
<?php

// Database Insert
if (isset($_SESSION['message2'])) {echo $_SESSION['message2'];}
if (isset($_POST['addCat'])) {

$update = true;	
	//intval($_POST['assetTag']);

	if ((!$_POST['cat'] == NULL)) 
	{
		$cat = $_POST['cat'];

	if ($update)
		{
			mysqli_query($link,"INSERT into inventory_cat(name) VALUES('$cat')");
		
		$_SESSION['message2'] = "<p class='success'>Success! ".$cat." has been inserted!</p>";
		}
		
		Refresh("addCat");	
	}
	elseif (!$update) {
		$_SESSION['message2'] = "<p class='error'>Please fill out all fields correctly!</p>";
		Refresh("addCat");	
	}
}

?>