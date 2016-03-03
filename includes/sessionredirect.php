<?php 

function authQuery($level = 0)
{
	if( ((!empty($_SESSION['username'])) && ($_SESSION['Auth'] >= $level)))
	{

	}
	else
	{
  	 	header("location:/?p=loginError");				 
	}
}


?>