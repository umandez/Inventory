<?php
	$link = @new mysqli("127.0.0.1", "root", "", "inventory");

	if($link->connect_errno > 0){
		header("location:/pages/error.php");
    die('Unable to connect to database [' . $link->connect_error . ']');
	}
?>