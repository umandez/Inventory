<?php

// Title of Header
$title = "Inventory";

// Function to pull news into correct position on page
function pullContent()
{
if ((isset($_SESSION['username'])) ) {
  ?>

<head>
<script type="text/javascript">
function showUser(str) {
    if (str == null) {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
            }
        }

        xmlhttp.open("GET","test.php?q="+str,true);
        xmlhttp.send();
    }
}
</script>

</head>

<?php
if (!isset($_GET['addAsset'])  
  && !isset($_GET['accounting'])
   && !isset($_GET['addManufacturer'])
    && !isset($_GET['addCat'])
     && !isset($_GET['byModel'])) 
{
?>

<body>
<form>  
<h3>Search: <input type="text" name="assetNo" value="" autofocus placeholder="Asset Tag, Serial or Item Name.." class="textbox" oninput="showUser(this.value)" /></h3>
</form>
<div id="txtHint">  <!-- Results will be passed here -->  </div><br /><hr /><br />
</body>
<?php
}

include ('/config/dbconnectionconfig.php');

if (empty($_GET))
{

	$sql =    "Select
              inventory.inventory.assetTag,
              inventory.inventory.itemName,
              inventory.inventory_manufacturer.name AS manu_name,
              inventory.inventory_cat.name AS cat_name
            From
              inventory.inventory Inner Join
              inventory.inventory_manufacturer On inventory.inventory.manufacturer =
                inventory.inventory_manufacturer.id Inner Join
              inventory.inventory_cat On inventory.inventory.category =
                inventory.inventory_cat.id
            Group By
              inventory.inventory.itemName";

	$result = mysqli_query($link, $sql);

    ?>

<div class="inventory">
                <table class="homepageTable">
                    <tr>
                        <td>
                            Category
                        </td>
                        <td >
                            Manufacturer
                        </td>
                        <td >
                            Product
                        </td>
                        <td>
                            Quantity In Stock
                        </td>
                        <!--
                         <td>
                            Quantity Deployed
                        </td>
                      -->
                    </tr>


    <?php

	while ($row = mysqli_fetch_row($result)) {
				// Get results from lookup
				$id=$row[0];
				$itemName=$row[1];
        $manufacturer=$row[2];
				$category=$row[3];

        $sql2 = "Select
                  Count(inventory.inventory.itemName) As Count_itemName,
                  inventory.inventory.issued
                From
                  inventory.inventory
                Where
                  (inventory.inventory.itemName = '$itemName' &&
                    inventory.inventory.issued = 'N' &&
                    inventory.inventory.disposed = 'N')
                ORDER By
                    inventory.inventory.assetTag DESC
                  ";

        $result2 = mysqli_query($link, $sql2);
        $row2 = mysqli_fetch_assoc($result2);   
          $countDeployed = $row2['Count_itemName'];
 		
		?>
                    <tr>
                        <td >
                        	<?php echo $category; ?>
                        </td>
                        <td>            
                            <?php echo $manufacturer; ?>
                        </td>
                        <td>            
                            <?php echo "<a href='?byModel&reasonCode=1&model=".$itemName."'>".$itemName."</a>"; ?>
                        </td>
                        <td>
                            <?php echo $countDeployed; if ($countDeployed == 0) {echo " <img src='/images/warning.png' alt='More Stock needs to be ordered!'/>";} ?>
                        </td>
                        <!--
                        <td>
                            
                        </td>
                      -->
                    </tr>
             
        <?php    	
	}
  ?>
 </table>
</div>  
  <?php

  } 
  elseif (isset($_GET['assetNo'])) {

      //Includes our asset information in detail (Per Asset)
      include ('/includes/assetNo.php');

  }
  elseif (isset($_GET['addAsset'])) {

      // Includes our section for adding an asset - Keeping amount of lines down
      include ('/includes/addAsset.php');

  }
  elseif (isset($_GET['addManufacturer'])) {

      // Includes our section for adding a manufacturer - Keeping amount of lines down
      include ('/includes/addManufacturer.php');

  }
  elseif (isset($_GET['addCat'])) {

      // Includes our section for adding a category - Keeping amount of lines down
      include ('/includes/addCat.php');

  }
  elseif (isset($_GET['accounting'])) {

      // Includes our section for whiteboard - Keeping amount of lines down
      include ('/includes/report_lastxdays.php');

  }
  elseif (isset($_GET['byModel'])) {

      // Includes our section for reporting by model - Keeping amount of lines down
      include ('/includes/report_byModel.php');

  }
}
else {echo "<p class='error'>You must be logged in with the correct permissions to view this pannel</p>";}
}
 

?>
