<?php
  $assetTag = $_GET['assetNo'];
  if (!checkAssetExists($assetTag)) {
    Refresh("");
  }

  $sql = "
  Select
    inventory.inventory.assetTag,
    inventory.inventory.itemName,
    inventory.inventory_manufacturer.name As manu_name,
    inventory.inventory_cat.name As cat_name,
    inventory.inventory.serialCode,
    inventory.inventory.description,
    inventory.inventory.issued,
    inventory.inventory.returned,
    inventory.inventory.disposed,
    inventory.inventory.capexNo,
    inventory.inventory.dateAdded,
    inventory.inventory.serviceRecord
  From
    inventory.inventory Inner Join
    inventory.inventory_manufacturer On inventory.inventory.manufacturer =
      inventory.inventory_manufacturer.id Inner Join
    inventory.inventory_cat On inventory.inventory.category =
      inventory.inventory_cat.id
  Where
    inventory.inventory.assetTag = '".$assetTag."'
  Group By
    inventory.inventory.itemName, inventory.inventory.assetTag,
    inventory.inventory_manufacturer.name, inventory.inventory_cat.name,
    inventory.inventory.serialCode, inventory.inventory.description,
    inventory.inventory.issued, inventory.inventory.returned,
    inventory.inventory.disposed, inventory.inventory.capexNo,
    inventory.inventory.dateAdded";

  $result = mysqli_query($link, $sql);
  $row = mysqli_fetch_assoc($result);

  // Gets our Tick and Cross Images
  $tick = "<img src='images/tick.png' />";
  $cross = "<img src='images/cross.png' />";

  // Parse Data
    $manufacturer = $row['manu_name'];
    $itemName = $row['itemName'];
    $cat_name = $row['cat_name'];
    $serialCode = $row['serialCode'];
    $description = $row['description'];
    $issued = $row['issued'];
    $returned = $row['returned'];
    $disposed = $row['disposed'];
    $capexNo = $row['capexNo'];
    $dateAdded = $row['dateAdded'];
    $serviceRecord = $row['serviceRecord']; 

    $dateAdded = date('d-m-Y H:i:s', strtotime($dateAdded));

    if (isset($_GET['edit'])) {echo "True";} else {echo "False";}

    switch ($manufacturer) {
      case "Dell":
      $serialCode = "<a href='https://www.dell.com/support/home/us/en/04/product-support/servicetag/".$serialCode."/warranty'>".$serialCode."</a>";
      break;
      default:
      $serialCode = $serialCode;
    }

  // Display our Data
    ?>
    <div id="title"><h2 class='white'>Asset - <?php echo "(#" . $assetTag . ")  " . $manufacturer . " " . $itemName; ?></h2></div>
    <a href="/?assetNo=<?php echo $assetTag; ?>&edit" class="editAssetNo">Edit</a>
    <div class="inventory">
        <table>
            <tr>
               <td colspan="2">
               <b>Category</b>
               </td>
               <td colspan="2">
               <?php echo $cat_name; ?>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Manufacturer</b>
               </td>
               <td colspan="2">
               <?php echo $manufacturer; ?>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Model / Product</b>
               </td>
               <td colspan="2">
               <?php echo $itemName; ?>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Issued</b>
               </td>
               <td colspan="2">
               <?php echo ($issued == 'N' ? $cross : $tick); ?>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Returned</b>
               </td>
               <td colspan="2">
               <?php echo ($returned == 'N' ? $cross : $tick); ?>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Disposed</b>
               </td>
               <td colspan="2">
               <?php echo ($disposed == 'N' ? $cross : $tick); ?>
               </td>
            </tr>
            <tr>
               <td colspan="1">
               <b>Serial Code</b>
               </td>
               <td colspan="1">
               <?php echo $serialCode; ?>
               </td>
               <td colspan="1">
               <b>Capex Number</b>
               </td>
               <td colspan="1">
               <?php echo $capexNo; ?>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Date Added</b>
               </td>
               <td colspan="2">
               <?php echo $dateAdded; ?>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Description</b>
               </td>
               <td colspan="2">
               <?php echo $description; ?>
               </td>
            </tr>
            <tr>
               <td colspan="2">
               <b>Linked to Service Record:</b>
               </td>
               <td colspan="2">
               <?php echo "#".$serviceRecord; ?>
               </td>
            </tr>
        </table>
    </div>

    <br />
    <!-- Includes our Reason Dropdown list -->
    <div id="title"><h2 class='white'>Add an Action</h2></div>
    <?php include ('/includes/reasonDropdown.php');

    $sql = "
    Select
      inventory.inventory_movements.id,
      inventory.inventory_movements.itemid,
      inventory.inventory_reason.reason,
      inventory.inventory_movements.date,
      inventory.inventory_movements.changedBy,
      inventory.inventory_movements.owner,
      inventory.inventory_movements.notes
    From
      inventory.inventory_movements Inner Join
      inventory.inventory_reason On inventory.inventory_movements.reasonid =
      inventory.inventory_reason.id
    Where
      inventory.inventory_movements.itemid = '".$assetTag."'
    Order By
      inventory.inventory_movements.id DESC
    "
    ?>
    <br />

     <div id="title"><h2 class='white'>Asset Movement</h2></div>
    <div class="inventory">
                <table>
                    <tr>
                        <td colspan="1">
                            ID
                        </td>
                        <td colspan="1">
                            Date
                        </td>
                        <td colspan="1">
                            Note
                        </td>  
                        <td colspan="1">
                            State
                        </td>
                         <td colspan="1">
                            Owner
                        </td>
                        <td colspan="2">
                            Updated By
                        </td>
                    </tr>
    <?php

    $result = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_assoc($result)) {

      $id = $row['id'];
      $dateMovement = $row['date'];
      $reason = $row['reason'];
      $changedby = $row['changedBy'];
      $owner = $row['owner'];
      $notes = $row['notes'];
      $notes = nl2br($notes);

      $dateMovement = date('d-m-Y H:i:s', strtotime($dateMovement));

    ?>
                    <tr>
                        <td colspan="1">
                          <?php echo $id; ?>
                        </td>
                        <td colspan="1">            
                            <?php echo $dateMovement ?>
                        </td>
                        <td colspan="1">
                            <?php echo $notes; ?>
                        </td>
                        <td colspan="1">
                          <?php echo $reason; ?>
                        </td>
                        <td colspan="1">
                            <?php echo $owner; ?>
                        </td colspan="2">
                        <td>
                            <?php echo $changedby; ?>
                        </td>
                    </tr>
             
        <?php     
  }
  ?>
 </table> 
 </div>