<?php
	if (isset($_GET['p'])) {

			$PFile = $_GET['p'].'.php';

			if(file_exists($PFile)) {
					include($PFile);
			} else {
					//$title = 'Page not found';
					//$content = '';
				header("location:/?p=404");
			}
	} else {
			include('home.php');
	}
?>
