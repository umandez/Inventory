<script>
$(document).ready(function () {
    toggleFields(); //call this first so we start out with the correct visibility depending on the selected form values
    //this will call our toggleFields function every time the selection value of our underAge field changes
    $("#reason").change(function () {
        toggleFields();
    });

});
//this toggles the visibility of our parent permission fields depending on the current selected value of the underAge field
function toggleFields() {
    if ($("#reason").val() == null)
        $("#notes").hide();
    else
        $("#notes").show();

    //This is hiding both our fields

    if ($("#reason").val() == null)
        $("#updateBtn").hide();
    else
        $("#updateBtn").show();

    //This is hiding user update if there is no need for it

    if (($("#reason").val() == 2) || ($("#reason").val() == 7))
        $("#user").show();
    else
        $("#user").hide();

    if (($("#reason").val() == 2) || ($("#reason").val() == 7))
        $("#servicerecord").show();
    else
        $("#servicerecord").hide();
}
</script>
<hr><br />
<?php

$sql = "Select * from inventory_reason";
	$result = mysqli_query($link, $sql);

	echo "<div id='updateAsset'>";
	echo "<form action='' method='post'>";
	echo "<select name='reason' id='reason' class='textboxDropdown'>";
	echo "<option value='' disabled selected >Please select an action..</option>";

		while($row = mysqli_fetch_array($result)) {
			$id = $row['id'];
			$reason = $row['reason'];

			echo "<option value='".$id."'>".$reason."</option>";
		}

	echo "</select>";

?>
	<input type="submit" class="btnRedSmall" name="update" id="updateBtn" value="Update" />
		<div id="notes">
			<br />
			<input type="number" name="servicerecord" id="servicerecord" class="textboxLarge" placeholder="Please enter Service Desk Reference number here.." />
			<br />
			<textarea name="notes" class="textareaSmall" placeholder="Please add any notes regarding the action here.."></textarea>
			<input type="text" name="user" id="user" class="textboxLarge" placeholder="New owners name here.." />
			<br />		
		</div>
	</form>
</div>

<?php

if (isset($_POST['update'])) {

	$note = $_POST['notes'];
	$reasonid = $_POST['reason'];
	$itemid = $_GET['assetNo'];
	$changedBy = $_SESSION['username'];
	$owner = $_POST['user'];
	$linkedServiceRecord = $_POST['servicerecord'];

	mysqli_query($link,"INSERT into inventory_movements(itemid, reasonid, changedBy, owner, notes) VALUES('$itemid', '$reasonid', '$changedBy', '$owner', '$note')");
	// Update reasonid field in root table, to make queries easier...
	mysqli_query($link,"UPDATE inventory SET reasonid = '$reasonid' WHERE assetTag = '".$itemid."'");


	switch ($reasonid) {
		case 1: // In Store
			mysqli_query($link,"UPDATE inventory SET issued = 'N', returned = 'N', disposed = 'N' WHERE assetTag = '".$itemid."'");
			break;
		case 2: // Deployed
			mysqli_query($link,"UPDATE inventory SET issued = 'Y', returned = 'N', disposed = 'N', serviceRecord = '".$linkedServiceRecord."' WHERE assetTag = '".$itemid."'");
			if ($linkedServiceRecord) {sendEmail("servicedesk.it@etelimited.co.uk","Inventory System: #". $linkedServiceRecord ." Asset booked out","Inventory scanned out details below;" . "\n" . "\n" . "AssetTag: ".$itemid . "\n" . "Item Name: ".$manufacturer." - ".$itemName. "\n" . "Serial Code: ".$serialCode."". "\n" . "Capex Number: ".$capexNo. "\n" . "\n" . "Notes from Inventory System: " . $note . "");}
			break;
		case 4: // Returned
			mysqli_query($link,"UPDATE inventory SET issued = 'N', returned = 'Y', disposed = 'N' WHERE assetTag = '".$itemid."'");
			break;
		case 5: // Faulty
			mysqli_query($link,"UPDATE inventory SET issued = 'N', returned = 'Y', disposed = 'N' WHERE assetTag = '".$itemid."'");
			break;
		case 6: // Disposed
			mysqli_query($link,"UPDATE inventory SET issued = 'N', returned = 'N', disposed = 'Y' WHERE assetTag = '".$itemid."'");
			break;
		case 7: // On Loan
			mysqli_query($link,"UPDATE inventory SET issued = 'Y', returned = 'N', disposed = 'N', serviceRecord = '".$linkedServiceRecord."' WHERE assetTag = '".$itemid."'");
			if ($linkedServiceRecord) {sendEmail("servicedesk.it@etelimited.co.uk","Inventory System: #". $linkedServiceRecord ." Asset booked out","Inventory scanned out details below;" . "\n" . "\n" . "AssetTag: ".$itemid . "\n" . "Item Name: ".$manufacturer." - ".$itemName. "\n" . "Serial Code: ".$serialCode."". "\n" . "Capex Number: ".$capexNo. "\n" . "\n" . "Notes from Inventory System: " . $note . "");}
			break;
	}

	$loginUser = $_SESSION['loginuser'];

	//$to = "ryan.riddell@stapletons-tyres.co.uk";

	//			$subject = 'Altis Life: New Account Details';
	//			$message  = 'Hello ' . $alias . ', your new account for Diversity Gaming has been made. Your login details are;' . "\n" . "\n";
	//			$message  .= 'User Name: ' . $user . "\n";
	//			$message  .= 'Please keep your details safe and secure.';
	//			$headers = "From: no-reply@etelimited.co.uk"; 
	//			mail ($to, $subject, $message, $headers);

	header("location:/?assetNo=".$itemid);	
}

?>
<br /><hr>