<?php
$q = ($_GET['q']);

$con = mysqli_connect('localhost','root','','inventory');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

$sql="
Select
  inventory.inventory.assetTag,
  inventory.inventory.itemName,
  inventory.inventory.serialCode,
  inventory.inventory.dateAdded,
  inventory.inventory.serviceRecord,
  inventory.inventory_cat.name As cat_name,
  inventory.inventory_manufacturer.name As manu_name,
  Max(inventory.inventory_movements.id) As Max_id
From
  inventory.inventory Inner Join
  inventory.inventory_cat On inventory.inventory.category =
    inventory.inventory_cat.id Inner Join
  inventory.inventory_manufacturer On inventory.inventory.manufacturer =
    inventory.inventory_manufacturer.id Inner Join
  inventory.inventory_movements On inventory.inventory_movements.itemid =
    inventory.inventory.assetTag
Where
  (inventory.inventory.assetTag = '$q') Or
  (inventory.inventory.itemName Like '%$q%') Or
  (inventory.inventory.serialCode = '$q') Or
  (inventory.inventory.serviceRecord = '$q')
Group By
  inventory.inventory.assetTag, inventory.inventory.itemName,
  inventory.inventory.serialCode, inventory.inventory.dateAdded,
  inventory.inventory_cat.name, inventory.inventory_manufacturer.name
  ";

$result = mysqli_query($con,$sql);

echo "<br /><div class='inventory'><table>
<tr>
<td>Asset #</td>
<td>Product</td>
<td>Category</td>
<td>Date Added</td>
<td>Serial No</td>
<td>Status</td>
<td>Owner</td>
</tr>";

while($row = mysqli_fetch_array($result)) {

  $assetTag = $row['assetTag'];
  $dateAdded = date('d-m-Y', strtotime($row['dateAdded']));

  $sqlMovement = "
Select
  inventory.inventory_movements.id,
  inventory.inventory_reason.reason,
  inventory.inventory_movements.owner,
  inventory.inventory_movements.itemid
From
  inventory.inventory_movements Inner Join
  inventory.inventory_reason On inventory.inventory_movements.reasonid =
  inventory.inventory_reason.id
Where
  inventory_movements.id = '".$row['Max_id']."'
  ";
  $result2 = mysqli_query($con,$sqlMovement);
  $row2 = mysqli_fetch_array($result2)

    ?> <tr style="cursor:pointer" onclick="document.location.href='<?php echo "/?assetNo=$assetTag"; ?>'"
            onmouseover="this.background='#FFFFFF'" onmouseoff="this.background='#000000'"> 
    <?php
    echo "<td >" . $row['assetTag'] . "</td>";
    echo "<td><a href='?byModel&reasonCode=ANY&model=".$row['itemName']."'>" . $row['manu_name'] . " - " . $row['itemName'] . "<a/></td>";
    echo "<td>" . $row['cat_name'] . "</td>";
    echo "<td>" . $dateAdded . "</td>";
    echo "<td>" . $row['serialCode'] . "</td>";
    echo "<td>" . $row2['reason'] . "</td>";
    echo "<td>" . $row2['owner'] . "</td>";
    echo "</tr>";
}
echo "</table>";
mysqli_close($con);
?>
</div>