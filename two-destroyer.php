<?php
	session_start();
	
	unset($_SESSION["user"]);

	header('Location: https://libraries-test.mit.edu/oastats/two.php');
?>