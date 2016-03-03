<?php 


$title = "Forgotten Password";

function pullContent() {
include ($_SERVER['DOCUMENT_ROOT'].'/scripts/jquery.php'); 

?>

<script type="text/javascript">
$(document).ready(function () {

  jQuery.validator.addMethod("noSpace", function(value, element) 
  { 
     return value.indexOf(" ") < 0 && value != ""; 
  }, "Spaces are not allowed");
  
  jQuery.validator.addMethod("accept", function(value, element, param) 
  {
  return value.match(new RegExp("^" + param + "$"));
  }, "Cannot contain numbers");

    $('#newpassword').validate({
        rules: {
			email: {
                required: true,
				email: true,
				noSpace: true
            }			
        }
    });
});
</script>
<?php
	include ($_SERVER['DOCUMENT_ROOT'].'/config/dbconnectionconfig.php');

	// the form update part
	if (isset($_POST['newpas'])) {	
		if (verifyFormToken('newpass')) {

			$email = stripcleantohtml($_POST['email']);
			$email = mysqli_real_escape_string($link,$email);
			
			$rs1 = "SELECT
			users.email,
			count(*) as counter
			FROM
			users
			WHERE
			users.email = '$email'";
			$result1 = mysqli_query($link, $rs1);
			$arr1 = mysqli_fetch_array($result1);
			
			if ($arr1['counter'] == 1){
			
				$rs2 = "SELECT
				users.password,
				users.userName
				FROM
				users
				WHERE
				users.email = '$email'";
				
				$result2 = mysqli_query($link, $rs2);
				$arr2 = mysqli_fetch_array($result2);
				

				$encrypt = $arr2['password'];
				$to=$email;
				$subject="Password Reset";
				$from = 'no-reply@diversitygaming.net';
				$body='Hello '.$arr2['userName'].', <br/> <br/> We received a request to reset your password.<br/> <br/>Your username is '.$arr2['userName'].' <br><br>Click here to reset your password http://diversitygaming.net/?p=account/reset&encrypt='.$encrypt.'&action=reset   <br/> <br/> Did not request a password reset? please contact a diversitygaming.net admin<br/> <br/>--<br>Kind regards, diversitygaming.net.';
				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				mail($to,$subject,$body,$headers);
				
?>
				<meta http-equiv="refresh" content="1;url=?p=account/logout">
<?php
				echo "<center class='succes'><img src='images\loading.gif' alt='Loading' /> A password reset link has been emailed</center>";
			
			}
			else {
				die('<br><b class="error">Account not found, please signup now.</b>');
			}
		}
	}

	$newValToken = generateValToken('newpass');  
?>
	<br />
	<form action="" method="post" input type="hidden" id="newpassword">
		<table WIDTH='100%' CELLPADDING='6' CELLSPACING='1' BORDER='1' class="table">
			<th colspan='4'>Change Password</th>
			<tr>
				<td colspan='1'><label>*Email: </label></td>
				<td colspan='1'><input type="text" name="email" id="email" class="textbox"></td>
			</tr>
		</table>

		<br />
		<input type='hidden' name='valtoken' value="<?php echo $newValToken; ?>">
		<center><input type='submit' name='newpas' class="btnRed" value='New Password'><br /><br /></center>
	</form>  
<?php
}
?>




























