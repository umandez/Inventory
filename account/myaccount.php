<?php
include ($_SERVER['DOCUMENT_ROOT'].'/includes/sessionredirect.php');
authQuery();

// Title the header...
$title = "My Account Details"; 

function pullContent()
{
	include ($_SERVER['DOCUMENT_ROOT'].'/scripts/jquery.php');
	include ($_SERVER['DOCUMENT_ROOT'].'/config/variables.php');
	ob_start();
?>

	<script type="text/javascript">
		$(document).ready(function () {
		
		  jQuery.validator.addMethod("noSpace", function(value, element) 
		  { 
			 return value.indexOf(" ") < 0 && value != ""; 
		  }, "Spaces are not allowed");
		  
		  jQuery.validator.addMethod("accept", function(value, element, param) 
		  {
		  return value.match(new RegExp("/^" + param + "$"));
		  }, "Cannot contain numbers");

			$('#editacc').validate({
				rules: {
					alias: {
						required: true,
						rangelength: [3, 16],
						//accept: "[A-Za-z]+",
						noSpace: false
					},
					fname: {
						required: false,
						rangelength: [2, 16],
						noSpace: true,
						accept: "[a-zA-Z]+"
					},
					lname: {
						required: false,
						rangelength: [2, 16],
						noSpace: true,
						accept: "[a-zA-Z]+"
					},
					email: {
						required: true,
						email: true,
						noSpace: true
					},
					password: {
						required: true,
						noSpace: true
					} 			
				}

			});
		});
	</script>
	<br />

<?php
	include ($_SERVER['DOCUMENT_ROOT'].'/config/dbconnectionconfig.php');
	//request account data
	$accountResult = mysqli_query($link,
	"SELECT
	users.firstName,
	users.lastName,
	users.email,
	users.alias,
	users.avatar,
	users.guid
	FROM
	users
	WHERE
	users.userName = '".$_SESSION['username']."'"
	);
	$accountData = mysqli_fetch_row($accountResult);

	$accountFName = $accountData['0'];
	$accountLName = $accountData['1'];
	$accountEmail = $accountData['2'];
	$accountAlias = $accountData['3'];
	$accountAvatar = $accountData['4'];
	$accountGUID = $accountData['5'];
	
// the form update part
	if (isset($_POST['editAccount'])) {	
		if (verifyFormToken('editaccForm')) {
			$pwd = stripcleantohtml($_POST['password']);
			$pwd = mysqli_real_escape_string($link,$pwd);
			//$pwd = password_hash($pwd, PASSWORD_BCRYPT).'\n';

			$em = stripcleantohtml($_POST['email']);
			$em = mysqli_real_escape_string($link,$em);
			$fn = "";
			$fn=ucwords($fn);
			$ln = "";
			$ln=ucwords($ln);
			$alias = $_POST['alias'];
			$avatar = $_POST['avatar'];

			$sql = "SELECT userName, password FROM users WHERE userName = '".$_SESSION['username']."'";		
			$result = mysqli_query($link, $sql);
			$row = mysqli_fetch_assoc($result);		
			$hashedPassword = $row['password'];

			$rs = "SELECT
			users.alias,
			count(*) as counter
			FROM
			users
			WHERE
			users.alias = '$alias'";

			$result = mysqli_query($link, $rs);
			$arr1 = mysqli_fetch_array($result);

			$rs2 = "SELECT
			users.email,
			count(*) as counter
			FROM
			users
			WHERE
			users.email = '$em'";

			$result2 = mysqli_query($link, $rs2);
			$arr2 = mysqli_fetch_array($result2);
			$usernamepwc = $_SESSION['username'];
		
			if (password_verify($pwd, $hashedPassword)){
				// If result matched $myusername and, table row must be 1 row or user does not exist.
				if (($arr1['counter'] == 1) && ($accountAlias != $alias))
				{
					die('<br /><b class="error">** Sorry, but the Alias <font color="white">"'.$alias.'"</font> has already been taken, please choose another. **</b>');
					// Free result set
					mysqli_free_result($result);
				}
				elseif (($arr1['counter'] == 0) || ($accountAlias == $alias)) {
					if (($arr2['counter'] == 1) && ($accountEmail != $em)) {
							die('<br /><b class="error">** Sorry, but the Email <font color="white">"'.$em.'"</font> has already been taken, please choose another. **</b>');
						}
					elseif (($arr2['counter'] == 0) || ($accountEmail == $em)) {
						mysqli_query($link,"UPDATE users SET email='".$em."', alias='".$alias."', avatar='".$avatar."'  WHERE userName='".$_SESSION['username']."'");
						mysqli_query($link,"UPDATE transactions SET account='".$em."' WHERE account='".$_SESSION['Email']."'");
					
						$to = $em;
						$subject = 'Diversity Gaming: New Account Details';
						$message  = 'Hello ' . $alias . ', your account details for Diversity Gaming have been updated. Your new details are;' . "\n" . "\n";
						$message  .= 'Display Name: '. $alias ."\n";
						$message  .= 'Email: ' . $em . "\n" . "\n";
						$message  .= 'Please keep your details safe and secure.';
						$headers = "From: no-reply@diversitygaming.net"; 
						mail ($to, $subject, $message, $headers);
				
						?>
						<meta http-equiv="refresh" content="1;url=?p=account/logout">
						<?php
						echo "<center class='succes'><img src='images\loading.gif' alt='Loading' /> Account information updated successfully...</center>";
					}	
				}
				
				else {
					die('<b class="error">An error has occured in the UserAccounts database, please notify an admin.</b>');
				}
			}
			else{
				die('<br><b class="error">Wrong password.</b>');
			}
		}
		else {
		echo "<b class='error'>Hacking is bad mmmKay.</b>";
		}
	}


//the form and autofiling it's content
	
	$newValToken = generateValToken('editaccForm');  
	echo "
	<form action='' method='post' input type='hidden' id='editacc'>
		<table WIDTH='100%' CELLPADDING='6' CELLSPACING='1' BORDER='1' class='table'>
			<th colspan='4'>Your account details</th>
			<tr>
				<td colspan='2'><label>*Alias: </label></td>
				<td colspan='2'><input type='text' name='alias' id='alias' class='textbox' value='".$accountAlias."'></td>
			</tr>
			<tr>
				<td colspan='2'><label>*Arma 3 GUID: </label></td>
				<td colspan='2'><input type='text' name='armaguid' disabled id='armaguid' class='textbox' value='".$accountGUID."'></td>
			</tr>
			<tr>
				<td colspan='2'><label>*Email Address: </label><br />Please enter your email address</td>
				<td colspan='2'><input type='text' name='email' id='email' class='textbox' value='".$accountEmail."'></td>
			</tr>
			<tr>
				<td colspan='2'><label>Avatar URL: </label><br />Enter a valid url to an image the size must be 128 x 128 pixels</td>
				<td colspan='2'><input type='text' name='avatar' id='avatar' class='textbox' value='".$accountAvatar."'></td>
			</tr>
			<tr>
				<td colspan='2'><label>*Password: </label><br />Please verify the update with your password</td></td>
				<td colspan='2'><input type='password' name='password' id='password' class='textbox'></td>
			</tr>
		</table>
		";
?>
		<br />
		<input type='hidden' name='valtoken' value="<?php echo $newValToken; ?>">
		<input type='submit' name='editAccount' class="btnRed" value='Update'>
	</form>
	<br />

<?php


	mysqli_close($link);
}
?>

