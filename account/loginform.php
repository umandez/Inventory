<?php
ob_start();

if (isset($_POST['treasure2'])) {	
			
		// username and password sent from form
		$myusername= $_POST['username'];
		$mypassword= $_POST['password'];

		$myusername = stripslashes($myusername);
		$mypassword = stripslashes($mypassword);
 
$user = $myusername;
$password = $mypassword;
$host = 'stsgroup.local';
$domain = 'STSGROUP';
$basedn = 'dc=STSGROUP,dc=local';
$group = 'IT_Inventory';
 
$ad = ldap_connect("stsgroup.local") or die('Could not connect to LDAP server.');
 
ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

 
@ldap_bind($ad, $user .'@stsgroup', $password) or (header("location:/?error=true"));

$userdn = getDN($ad, $user, $basedn);

//$sr=ldap_search($ad, $userdn, '(|(cn=rriddell*)(ou=STSGroup*))', array('mail'));
//$info = ldap_get_entries($ad, $sr);
//$_SESSION['email'] = $info['mail'][0];
 
if (checkGroupEx($ad, $userdn, getDN($ad, $group, $basedn)))
{
  // if (checkGroup($ad, $userdn, getDN($ad, $group, $basedn))) {
  $_SESSION['username'] = getCN($userdn);
  $_SESSION['loginuser'] = $myusername;

  	header("location:/");
}
else
{
  echo "<p class='error'>Invalid permissions. <br />You must be a member of CN: <b>".$group."</b> to access this page.</p>";
}
 
ldap_unbind($ad);

}

?>
<form action="" method="post">
	<br /><input type="text" name="username" size="12" class="textboxSmall" placeholder="Username" /><br />
	<br /><input type="password" name="password" size="12" class="textboxSmall"  placeholder="Password" /><br />
	<br /><input type="ip" name="ip" size="12" class="textboxSmall" hidden value='<?php echo getenv('REMOTE_ADDR'); ?>' />
	<?php if(isset($_GET['error'])) {echo "<p class='error'>Wrong username or password<p>";} ?>
	<center><input type="submit" class="btnRed" name="treasure2" value="Log In" /></center>
	<br />
</form>

<?php
function getDN($ad, $samaccountname, $basedn)
{
  $result = ldap_search($ad, $basedn, "(samaccountname={$samaccountname})", array(
    'dn'
  ));
  if (! $result)
  {
    return '';
  }
 
  $entries = ldap_get_entries($ad, $result);
  if ($entries['count'] > 0)
  {
    return $entries[0]['dn'];
  }
 
  return '';
}
 
/**
 * This function retrieves and returns Common Name from a given Distinguished
 * Name.
 *
 * @param string $dn
 *          The Distinguished Name.
 * @return string The Common Name.
 */
function getCN($dn)
{
  preg_match('/[^,]*/', $dn, $matchs, PREG_OFFSET_CAPTURE, 3);
  return $matchs[0][0];
}
 
/**
 * This function checks group membership of the user, searching only in
 * specified group (not recursively).
 *
 * @param resource $ad
 *          An LDAP link identifier, returned by ldap_connect().
 * @param string $userdn
 *          The user Distinguished Name.
 * @param string $groupdn
 *          The group Distinguished Name.
 * @return boolean Return true if user is a member of group, and false if not
 *         a member.
 */
function checkGroup($ad, $userdn, $groupdn)
{
  $result = ldap_read($ad, $userdn, "(memberof={$groupdn})", array(
    'members'
  ));
  if (! $result)
  {
    return false;
  }
 
  $entries = ldap_get_entries($ad, $result);
 
  return ($entries['count'] > 0);
}
 
/**
 * This function checks group membership of the user, searching in specified
 * group and groups which is its members (recursively).
 *
 * @param resource $ad
 *          An LDAP link identifier, returned by ldap_connect().
 * @param string $userdn
 *          The user Distinguished Name.
 * @param string $groupdn
 *          The group Distinguished Name.
 * @return boolean Return true if user is a member of group, and false if not
 *         a member.
 */
function checkGroupEx($ad, $userdn, $groupdn)
{
  $result = ldap_read($ad, $userdn, '(objectclass=*)', array(
    'memberof'
  ));
  if (! $result)
  {
    return false;
  }
 
  $entries = ldap_get_entries($ad, $result);
  if ($entries['count'] <= 0)
  {
    return false;
  }
 
  if (empty($entries[0]['memberof']))
  {
    return false;
  }
 
  for ($i = 0; $i < $entries[0]['memberof']['count']; $i ++)
  {
    if ($entries[0]['memberof'][$i] == $groupdn)
    {
      return true;
    }
    elseif (checkGroupEx($ad, $entries[0]['memberof'][$i], $groupdn))
    {
      return true;
    }
  }
 
  return false;
}
?>
