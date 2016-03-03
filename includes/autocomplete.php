<?php
 include ('/config/dbconnectionconfig.php');

 $q=$_GET['q'];
 $my_data=mysql_real_escape_string($q);

 $sql = "select itemName from inventory";
 $result = mysqli_query($link,$sql) or die(mysqli_error());

 if($result)
 {
  while($row=mysqli_fetch_array($result))
  {
   echo $row['itemName']."\n";
  }
 }
?>