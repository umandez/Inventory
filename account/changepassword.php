<?php 
$title = "Change Password";

function pullContent() {
	include ($_SERVER['DOCUMENT_ROOT'].'/scripts/jquery.php');
	include ($_SERVER['DOCUMENT_ROOT'].'/config/variables.php');
	?>

	<script type="text/javascript">
		$(document).ready(function () {

			jQuery.validator.addMethod("noSpace", function(value, element) { 
				return value.indexOf(" ") < 0 && value != ""; 
				}, "Spaces are not allowed"
			);
		  
			jQuery.validator.addMethod("accept", function(value, element, param) {
				return value.match(new RegExp("^" + param + "$"));
				}, "Cannot contain numbers"
			);
		  
			$('#changepassword').validate({
				rules: {
					currentpass: {
					   required: true,
					   noSpace: true
					}, 	
					password2: {
					   required: true,
					   noSpace: true
					}, 
					password: {
						required: true,
						equalTo: '#password2',
						noSpace: true
					} 				
				}
			});
		});
	</script>


<?php
	include ($_SERVER['DOCUMENT_ROOT'].'/config/dbconnectionconfig.php');
	
	// the form update part
	if (isset($_POST['changepas'])) {	
		if (verifyFormToken('changepass')) {
			$currentpwd = stripcleantohtml($_POST['currentpass']);
			$currentpwd = mysqli_real_escape_string($link,$currentpwd);
			//$currentpwd = password_hash($currentpwd, PASSWORD_BCRYPT).'\n';

			$sql = "SELECT userName, password FROM users WHERE userName = '".$_SESSION['username']."'";		
			$result = mysqli_query($link, $sql);
			$row = mysqli_fetch_assoc($result);		
			$hashedPassword = $row['password'];

			$pwd = stripcleantohtml($_POST['password']);
			$pwd = mysqli_real_escape_string($link,$pwd);
			$pwd = password_hash($pwd, PASSWORD_BCRYPT).'\n';
		
			if (password_verify($currentpwd, $hashedPassword)){
				mysqli_query($link,"UPDATE users SET users.password='".$pwd."'  WHERE userName='".$_SESSION['username']."'");
					
?>
				<meta http-equiv="refresh" content="1;url=?p=account/logout">
<?php
				echo "<center class='succes'><img src='images\loading.gif' alt='Loading' /> Account information updated successfully...</center>";
			}	
	
			else{
				die('<br><b class="error">Wrong password.</b>');
			}
		}
	}

	$newValToken = generateValToken('changepass');  
?>
	<br />
	<form action="" method="post" input type="hidden" id="changepassword">
		<table WIDTH='100%' CELLPADDING='6' CELLSPACING='1' BORDER='1' class="table">
			<th colspan='4'>Change Password</th>
			<tr>
				<td colspan='1'><label>*Current Password: </label></td>
				<td colspan='1'><input type="password" name="currentpass" id="currentpass" class="textbox"></td>
			</tr>
			<tr>
				<td colspan='1'><label>*New Password: </label></td>
				<td colspan='1'><input type="password" name="password2" id="password2" class="textbox"></td>
			</tr>
			<tr>
				<td colspan='1'><label>*Confirm  New Password: </label></td>
				<td colspan='1'><input type="password" name="password" id="password" class="textbox"></td>
			</tr>
		</table>

		<br />
		<input type='hidden' name='valtoken' value="<?php echo $newValToken; ?>">
		<center><input type='submit' name='changepas' class="btnRed" value='Change Password'><br /><br /></center>
	</form>  
<?php
}
?>

