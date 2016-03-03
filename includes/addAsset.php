
<div id="title"><h2 class='white'>Add a new asset into the system</h2></div>

<script type="text/javascript">
    function play_soundSuccess() {
        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', '/sounds/success.mp3');
        audioElement.setAttribute('autoplay', 'autoplay');
        audioElement.load();
        audioElement.play();
    }
</script>
 <script>
  $(document).ready(function() { 
    $("#newAsset").validate({ 
         rules: { 
         assetTag: {
           required: true,
           minlength: 5,
           maxlength: 7,
           number: true
           },
          itemName: {
           required: true
           },
          manu: {
           required: true
           },
          cat: {
           required: true
           },
          serialCode: {
           required: true,
           minlength: 5,
           maxlength: 26
           },
          price: {
           range: [1, 10000],
           number: true
           },
          capexNo: {
           maxlength: 10
           }
         }, 
         messages: { 
           assetTag: {
            required: "<br>Please enter a valid Asset Tag!",
            minlength: "<br>You must enter more than 4 numbers!",
            maxlength: "<br>You must enter less than 8 numbers!",
            number: "<br>Must be fully numeric!"
            },
          itemName: {
            required: "<br>You must select a product!"
            },
          manu: {
            required: "<br>You must select a Manufacturer!"
            },
          cat: {
            required: "<br>You must select a Category!"
            },
          serialCode: {
            required: "<br>Please enter a valid Serial Number!",
            minlength: "<br>You must enter more than 5 characters!",
            maxlength: "<br>You must enter more than 26 characters!"
            },
          price: {
            range: "<br>Please enter a price between 1 - 10000",
            number: "<br>Must be fully numeric!"
            },
          capexNo: {
            maxlength: "<br>Must not be more than 10 digits long!"
            }
         } 
        }); 
      }); 
  </script>

<?php

  // Get our next Asset Tag
  $sql = "Select max(AssetTag) from inventory";
  $result = mysqli_query($link, $sql);
  $row = mysqli_fetch_assoc($result);
  $row = $row['max(AssetTag)'];
  $row = $row + 1;

  echo "<p class='right'>Next available AssetTag: ".$row."</p>"; 
?>

<form action="" id="newAsset" method="post">
<div class="inventory">
        <table>
            <tr>
               <td colspan="2">
               <b>Asset Tag *</b>
               </td>
               <td colspan="2">
               <input type="text" name="assetTag" id="assetTag" autofocus class="textboxLarge" placeholder=" Scan Asset Tag here.." value="<?php saveValue('assetTag'); ?>"/>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Serial Code *</b>
               </td>
               <td colspan="2">
               <input type="text" name="serialCode" id="serialCode" class="textboxLarge" placeholder=" Scan or enter Serial Code here.." value="<?php saveValue('serialCode'); ?>"/>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Category *</b>
               </td>
               <td colspan="2">
               <?php dropdownList("select * from inventory_cat","name","cat") ?>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Manufacturer *</b>
               </td>
               <td colspan="2">
               <?php dropdownList("select * from inventory_manufacturer","name","manu") ?>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Item Name / Model Name *</b>
               </td>
               <td colspan="2">
                <p><font color='red'>This field will auto suggest, if the product already exists please select it rather than making a new one.</font></p>
               <?php dropdownTextBox("Select inventory.inventory.itemName From inventory.inventory Group By inventory.inventory.itemName","itemName","itemName") ?>
               <p><i>For example Latitude 5540.</i></p></td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Description</b>
               </td>
               <td colspan="2">
               <textarea name="description" class="textareaSmall" placeholder="E.g. what has been installed? Additional notes on why it was bought, who asked for it to be bought?" ></textarea>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Price Bought For</b>
               </td>
               <td colspan="2">
               <input type="text" name="price" id="price" class="textboxLarge" placeholder=" £ Enter price bought for here.." value="<?php saveValue('price'); ?>"/>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Capex Number?</b>
               </td>
               <td colspan="2">
               <input type="text" name="capexNo" id="capexNo" class="textboxLarge" placeholder=" Enter Capex no, if none leave blank.." value="<?php saveValue('capexNo'); ?>"/>
               </td>
            </tr>
        </table>
</div>
<p><i>* Denotes a required field</i></p>
	<center><input type="submit" class="minimal" name="addAsset" value="Add Asset" /></center>    
</form>
<?php

// Database Insert
if (isset($_SESSION['error'])) {
   echo $_SESSION['error'];
}
if (isset($_POST['addAsset'])) {

$update = false;	
	//intval($_POST['assetTag']);

	if ((!$_POST['assetTag'] == NULL)
		&& ($_POST['assetTag'] != 0) 
			&& (!$_POST['itemName'] == NULL) 
				&& (!$_POST['manu'] == NULL) 
					&& (!$_POST['cat'] == NULL) 
						&& (!$_POST['serialCode'] == NULL)) 
	{
    $update = true;
    $duplicate = false;

    $rs = "SELECT
      assetTag,
      count(*) as counter
      FROM
      inventory
      WHERE
      assetTag = '".$_POST['assetTag']."'";

      $result = mysqli_query($link, $rs);
      $arr = mysqli_fetch_array($result);

		$assetTag = $_POST['assetTag'];
		$itemName = $_POST['itemName'];
		$manu = $_POST['manu'];
		$cat = $_POST['cat'];
		$serialCode = $_POST['serialCode'];
		$description = $_POST['description'];
		$price = $_POST['price'];
		$capexNo = $_POST['capexNo'];
		$username = $_SESSION['username'];

	// Run some basic valiadation
	if (!(is_numeric($assetTag))) {
		$update=false;
			$_SESSION['error']="<p class='error'>Asset Tag must be completely numeric!</p>";
				Refresh("addAsset");   
	}

  if ($_POST['price'] != "")
  {
      if (!(is_numeric($price))) {
          $update=false;
          $_SESSION['error']="<p class='error'>Price must be a valid number!</p>";
            Refresh("addAsset");   
       }
  }

	if ($update)
		{
    if ($arr['counter'] == 0) {
  
		mysqli_query($link,"INSERT into inventory(assetTag, itemName, manufacturer, description, serialCode, category, price, capexNo) VALUES('$assetTag', '$itemName', '$manu', '$description', '$serialCode', '$cat', '$price', '$capexNo')");
		mysqli_query($link,"INSERT into inventory_movements(itemid, reasonid, changedBy) VALUES('$assetTag', '1', '$username')");
		
		$_SESSION['error'] = "<p class='success'>Success! Asset (#".$assetTag.") ".$itemName." has been inserted!</p>";
      $success = true;
      }
      else
      {
        $update = false;
        $duplicate = true;
      }
		}
		
		Refresh("addAsset");  	
	}
	elseif (!$update) {
  if (!$duplicate) {
		$_SESSION['error'] = "<p class='error'>Please fill out all field`s correctly!</p>";
  }
  else {
    $_SESSION['error'] = "<p class='error'>This Asset Number is already in use!</p>";
  }
      $success = false;
		Refresh("addAsset&capexNo=".$_POST['CapexNo']."&assetTag=".$_POST['assetTag']."&serialCode=".$_POST['serialCode']."&price=".$_POST['price']."");	
	}
}

?>