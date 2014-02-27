<?php
	session_start();
	
	unset($_SESSION["user"]);

	$strBaseURL = 'https://'.$_SERVER["SERVER_NAME"].'/';

	header('Location: '.$strBaseURL);
?>