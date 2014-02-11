<?php
	session_start();
	
	$strBaseURL = "https://libraries-test.mit.edu/oastats/";
	if(isset($_GET["return"])) {
		$strURL = $strBaseURL . $_GET["return"];
	} else {
		$strURL = $strBaseURL . "two.php";
	}
	// Check for server variable, and update session variable if needed
	if(isset($_SERVER["REMOTE_USER"])) {
		$_SESSION["user"] = $_SERVER["REMOTE_USER"];
	} 
	// echo $_SESSION["user"];
	// echo $strURL;
	header('Location: '.$strURL);
?>