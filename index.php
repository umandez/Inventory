<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<!--[if IE]>
<script type="text/javascript">
window.location = "/invalidBrowser.php";
</script>
<![endif]-->

<?php 
ob_start();
session_start();  

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;   

include ('/config/dbconnectionconfig.php');
include ('/includes/functions.php');

?>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
<script src="/includes/validate/dist/jquery.validate.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<link rel="shortcut icon" href="images/favicon.ico" />
<link rel="stylesheet" type="text/css" media="screen" href="style/layout.css" />
<title>ETEL :: Inventory System</title>

</head>
<body>

<div id="header2">
			<center><img src="images/stapletons-logo.png" / ></center>
	</div>	

<div id="container">
	

<div id="content">	
<div id="left">

<!-- First Left Box -->
	<div id="contentHeader">
				<div class="gradient">
				<h1 class="white">My Account</h1>
				</div>
			</div> 
	<div id="left1">
<?php	
if(!empty($_SESSION['username']))
{
?>   
		
			<div class="gradient">
			<?php 

			print "<h2><center>Hello ". $_SESSION['username'] ."</center></h2>";
			//echo "Email: ".$_SESSION['email'];
			?>
			<hr>
			<center><a href="?p=account/logout" ><button class='minimal'>Logout</button></a></center>
			</div>	
<?php				
}
else
{
?>
			<div class="gradient">
			<?php include 'account\loginform.php'; ?>
			<br />
			<!--
			<a href='#' class="myButtonRed">Placeholder</a>
			<a href='#' class="myButtonRed">Placeholder</a>
			-->
			</div>
<?php 					 
}
?>
</div>


<?php	
if(!empty($_SESSION['username']))
{
?>  
<div id="contentHeader">
				<div class="gradient">
				<h1 class="white">My Links</h1>
				</div>
			</div> 
	<div id="left1">  
		
			<div class="gradient">
						<hr>
			<center><a href="/" ><button class='minimal'>Home</button></a></center>
						<hr>
			<center><a href="/?addAsset" ><button class='minimal'>New Asset</button></a></center>
						<hr>
			<center><a href="/?accounting" ><button class='minimal'>White Board V2</button></a></center>
						<hr>
			<center><a href="/?byModel" ><button class='minimal'>Item By Model</button></a></center>
						<hr>
			</div>	
</div>
<div id="contentHeader">
				<div class="gradient">
				<h1 class="white">Admin</h1>
				</div>
			</div> 
	<div id="left1">  
		
			<div class="gradient">
						<hr>
			<center><a href="/?addManufacturer" ><button class='minimal'>Add Manufacturer</button></a></center>
						<hr>
			<center><a href="/?addCat" ><button class='minimal'>Add Category</button></a></center>
						<hr>
			</div>	
</div>
<?php
}
?>
		
<!-- End of Left Content -->
</div>
<!-- End of Left Content -->

<!-- Main Center Content -->
<?php include 'includes\content.php'; ?>
<div id="main"> 
	<div id="contentHeaderCenter">
	<div class="gradient">
		<h1><?php echo $title ?></h1>
	</div>
</div>
	<div id="main1">
	<div class="gradient">
	<?php 


	pullContent(); 
	?>
	</div>
	</div>
</div>

<?php //include 'includes\leaderBoards.php'; 
		//include 'includes\ts3Widget.php';
?>

		
	<div class="clear"></div>


   </div>

</div>

<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
?>

<!-- Footer Box -->	
	<div id="footer" align="center">
			<i><font color="#FFFFFF">
			Created By Ryan Riddell</a> | Page generated in <?php echo $total_time ?> seconds |<?php
			$ip = getenv('REMOTE_ADDR');
			echo ' Your IP: ' . $ip . '';
			?></font> 
			</i>              	
	</div>
</body>
</html>