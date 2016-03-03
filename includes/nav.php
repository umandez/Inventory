<?php
if(!empty($_SESSION['username']))
{
?>
<a href="/">Home</a>
<font size="3.5px" color="white">|</font>
<a href="/forums">Forums</a>
<font size="3.5px" color="white">|</font>
<!-- <a href="?p=about">About</a>
<font size="3.5px" color="white">|</font> -->
<a href="?p=servers">Servers</a> 
<font size="3.5px" color="white">|</font>
<a href="?p=donate">Donate</a> 
<font size="3.5px" color="white">|</font>
<a href="?p=rules">Rules</a>
<?php
if($_SESSION['Auth'] >= 1)
{
?>

<font size="3.5px" color="white">|</font>
<a href="/downloads">Downloads</a>

<?php
}
?>

<?php
/*
if ($_SESSION['Auth'] >= 2)
    {   
        echo "<font size='3.5px' color='white'>|</font>";
        echo "<a href='?p=admin'>Admin</a>";
    }
*/
?>

<?php include $_SERVER['DOCUMENT_ROOT'].'\includes\tokenbalance.php'; ?>
<!-- <img id="tokens" src="images\tokens.png" alt="Donation Tokens"><?php echo $tokenbalance; ?> -->
<?php
}
else
{
?>
<a href="/">Home</a>
<font size="3.5px" color="white">|</font>
<a href="/forums">Forums</a>
<!-- <font size="3.5px" color="white">|</font>
<a href="?p=about">About</a> -->
<font size="3.5px" color="white">|</font>
<a href="?p=servers">Servers</a> 
<?php
}
?>