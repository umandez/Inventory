<?php
function getdate1() {
	$offset=0*60*60; //converting hours to seconds.
	$dateFormat="Y-m-d H:i:s";
	$timeNdate=gmdate($dateFormat, time()+$offset);		
		return $timeNdate;
}

function checkAssetExists($assetTag) {
    include ('/config/dbconnectionconfig.php');
      $query = "SELECT
                   count(inventory.inventory.assetTag) as counter
                FROM
                   inventory.inventory
                WHERE
                   inventory.inventory.assetTag = '$assetTag'";

          $result = mysqli_query($link, $query);
          $arr = mysqli_fetch_array($result);

          if ($arr['counter'] == 1) {
            return true;
          } else {
            return false;
          }
    }

function sendEmail($email,$subject,$message) {
  $to = $email;

    //ini_set("SMTP", "localhost");
     $subject = $subject;
     $message = $message;
     $headers = "From: inventory@stapletons-tyres.co.uk";
    
    mail ($to, $subject, $message, $headers);
}    

// Redirect to Error Page
function error() {
	header("location:/?p=loginError");
}	 

function isImage($url){
   $params = array('http' => array(
                'method' => 'HEAD'
             ));
   $ctx = stream_context_create($params);
   $fp = @fopen($url, 'rb', false, $ctx);
   if (!$fp) 
      return false;  // Problem with url

  $meta = stream_get_meta_data($fp);
  if ($meta === false){
      fclose($fp);
      return false;  // Problem reading data from url
  }
}

function dropdownList($query,$text,$post) {
include ('/config/dbconnectionconfig.php');

  $result = mysqli_query($link, $query);

  echo "<div id='updateAsset'>";
  echo "<select name='".$post."' id='".$post."' class='textboxDropdown'>";
  echo "<option value='' disabled selected >Please select from the list..</option>";

    while($row = mysqli_fetch_array($result)) {
      $id = $row['id'];
      $reason = $row[$text];

      echo "<option value='".$id."'>".$reason."</option>";
    }

  echo "</select>";
}
function dropdownTextBox($query,$text,$post) {
include ('/config/dbconnectionconfig.php');

  $result = mysqli_query($link, $query);

  echo "<div id='updateAsset'>";
  echo "<input type='text' name='".$post."' id='".$post."' list='itemNameOption".$post."' class='textboxLarge'>";
  echo "<datalist id='itemNameOption".$post."'>";

    while($row = mysqli_fetch_array($result)) {
      $reason = $row[$text];

      echo "<option value='".$reason."'>";
    }

  echo "</datalist>";
}

function Refresh($page) {
  header("location:/?".$page);  
}

function saveValue($value) {
  if (isset($_GET[$value])) {
    echo $_GET[$value];
  } else {
    echo "";
  }
}















?>

