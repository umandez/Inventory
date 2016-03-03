<?php
// Title the header...
$title = "Register"; 

function pullContent() {
	include ($_SERVER['DOCUMENT_ROOT'].'/scripts/jquery.php');
	include ($_SERVER['DOCUMENT_ROOT'].'/config/variables.php');
	include ($_SERVER['DOCUMENT_ROOT'].'/includes/steamsignin.php');
	ob_start();
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

		$('#reg').validate({
			rules: {
				username: {
					required: true,
					rangelength: [3, 16],
					noSpace: true,
					accept: "[a-zA-Z0-9]+"
				},
				alias: {
					required: true,
					rangelength: [3, 16],
					//accept: "[a-zA-Z]+",
				},
				email: {
					required: true,
					email: true,
					noSpace: true
				},
				armaguid: {
					required: true,
					rangelength: [17, 17],
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
	if (isset($_POST['createacc'])) 	{	
		if (verifyFormToken('regForm')) {
			$user = stripcleantohtml($_POST['username']);
			$user = mysqli_real_escape_string($link,$user);
			$pwd = stripcleantohtml($_POST['password']);
			$pwd = mysqli_real_escape_string($link,$pwd);	
			$pwd = password_hash($pwd, PASSWORD_BCRYPT).'\n';

			$em = stripcleantohtml($_POST['email']);
			$em = mysqli_real_escape_string($link,$em);
			$guid = stripcleantohtml($_POST['armaguid']);
			$guid = mysqli_real_escape_string($link,$guid);
			$fn = "";
			$fn=ucwords($fn);
			$ln = "";
			$ln=ucwords($ln);
			$alias = stripcleantohtml($_POST['alias']);
			$alias = mysqli_real_escape_string($link,$alias);

			$rs = "SELECT
			users.userName,
			users.email,
			users.GUID,
			users.alias,
			count(*) as counter
			FROM
			users
			WHERE
			users.userName = '$user' OR
			users.email = '$em' OR
			users.GUID = '$guid' OR
			users.alias = '$alias'";

			$result = mysqli_query($link, $rs);
			$arr1 = mysqli_fetch_array($result);

			$query = "SELECT
			players.playerid
			count(*) as counter
			FROM
			players
			WHERE
			players.playerid = '$guid'";

			$result = mysqli_query($link, $query);
			$queryarr = mysqli_fetch_array($result);

			// If result matched $myusername and, table row must be 1 row or user does not exist.
			if ($arr1['counter'] == 1){
				die('<font color="red"><br /><b>** Sorry, but the Username, Email, GUID or Alias has already been taken, please choose another. **</b></font>');
				// Free result set
				mysqli_free_result($result);
			}
			elseif ($arr1['counter'] == 0){

				$startingCash = "25000";

				mysqli_query($link,"INSERT into users(userName, password, firstName, lastName, auth, email, guid, alias ) VALUES('$user', '$pwd', '$fn', '$ln', '0', '$em', '$guid', '$alias')");
				
				if ($queryarr['counter'] == 1){
				}
				elseif ($queryarr['counter'] == 0){
					mysqli_query($link2arma,"INSERT into players(uid, name, playerid, bankacc, cop_licenses, civ_licenses, med_licenses, cop_gear, med_gear, civ_gear) VALUES(LAST_INSERT_ID(), '$alias', '$guid', '$startingCash', '[]', '[]', '[]', '[]', '[]', '[]')");
				}
				else {die('Wtf boom?');}

				$to = $em;
				$subject = 'Altis Life: New Account Details';
				$message  = 'Hello ' . $alias . ', your new account for Diversity Gaming has been made. Your login details are;' . "\n" . "\n";
				$message  .= 'User Name: ' . $user . "\n";
				$message  .= 'Please keep your details safe and secure.';
				$headers = "From: no-reply@diversitygaming.net"; 
				mail ($to, $subject, $message, $headers);
?>
				<meta http-equiv="refresh" content="2;url=http://diversitygaming.net">
<?php
				echo "<center class='succes'><img src='images\loading.gif' alt='Loading' /> Account created successfully...</center>";	
			}
			else {
				die('An error has occured in the UserAccounts database a doublicate account has been found please delete it: "'.$user.'"');
			}
		}
		else {
			echo "Hacking is bad mmmKay?.";
		}
		mysqli_close($link);
	}

	//start of form itself
	if(isset($_SESSION['Auth'])){
		echo "<b class='error'>You are already registered and logged in</b>";
	}
	else { 
		$newValToken = generateValToken('regForm');  
?>
	<br />

	<form action="" method="post" input type="hidden" id="reg">
		<table WIDTH='100%' CELLPADDING='6' CELLSPACING='1' BORDER='1' class="table">
			<th colspan='4'>Making a new account</th>
			<tr>
				<td colspan='1'><label>*Arma 3 GUID (Steam64ID): </label><br />Please begin by linking your steam account. If you have already made a character this will link your website account to it!<br />Once you have done this the next part of registration will appear.</td>
				<td colspan='1'>

				<?php
						if((isset($_GET['steamid'])) )
						{
						$steam_login_verify = SteamSignIn::validate();
						echo "<center><img src='/images/accept.png' /></center><br />";
						echo "<input type='text' name='armaguid' hidden id='armaguid' class='textbox' value='".$steam_login_verify."'>";
							if ($steam_login_verify == "")
							{
								header("location:/?p=account/reg");
							}
						}
						else
						{
						$steam_sign_in_url = SteamSignIn::genUrl();
						echo "<center><a href=\"$steam_sign_in_url\"><img src='http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_large_noborder.png' /></a></center>";
						}
					?>

				</td>
			</tr>

			<?php
			if (!isset($_GET['steamid']))
			{}
			else
			{
				?>
			
						<tr>
							<td colspan='1'><label>*Username: </label><br />Your username to login to the website.</td>
							<td colspan='1'><input type="text" name="username" id="username" class="textbox"></td>
						</tr>
						<tr>
							<td colspan='1'><label>*Alias: </label><br />What you would like to be known as, this is your RP name. A good example of an RP name is Tom Jones.</td>
							<td colspan='1'><input type="text" name="alias" id="alias" class="textbox"></td>
						</tr>
						<tr>
							<td colspan='1'><label>*Email Address: </label></td>
							<td colspan='1'><input type="text" placeholder="" name="email" id="email" class="textbox"></td>
						</tr>
						<tr>
							<td colspan='1'><label>*Password: </label></td>
							<td colspan='1'><input type="password" name="password2" id="password2" class="textbox"></td>
						</tr>
						<tr>
							<td colspan='1'><label>*Confirm Password: </label></td>
							<td colspan='1'><input type="password" name="password" id="password" class="textbox"></td>
						</tr>
				<?php			
			}
				?>
		</table>

		<br />
		<input type='hidden' name='valtoken' value="<?php echo $newValToken; ?>">

		<?php
		if (isset($_GET['steamid']))
		{
			echo "<center><input type='submit' name='createacc' class='btnRed' value='Create my account'><br /><br /></center>";
		}
		?>
	</form>

	<!-- <center><iframe width="560" height="315" src="https://www.youtube.com/embed/SGxVj2RXmto" frameborder="0" allowfullscreen></iframe></center><br /> -->

<?php
	}//end of form and check if already logged in
}
?>

