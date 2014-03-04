<?php
	session_start();
	
	unset($_SESSION["user"]);
	unset($_SESSION["admin"]);
	unset($_SESSION["impersonate"]);
	unset($_SESSION["hash"]);

	$strBaseURL = 'https://'.$_SERVER["SERVER_NAME"].'/';

	header('Location: '.$strBaseURL);
?>