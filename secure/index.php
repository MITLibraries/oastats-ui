<?php
	session_start();
	
	$strBaseURL = "https://".$_SERVER["SERVER_NAME"]."/";

	if(isset($_GET["return"])) {
		$strURL = $strBaseURL . $_GET["return"];
	} else {
		$strURL = $strBaseURL . "index.php";
	}

	// Check for server variable, and update session variable if needed
	if(isset($_SERVER["REMOTE_USER"])) {
		$_SESSION["user"] = $_SERVER["REMOTE_USER"];

		// look up whether this user is an admin
		if($_SERVER["REMOTE_USER"]=='mjbernha@mit.edu') {
			$_SESSION["admin"] = true;
		}
	} 

	header('Location: '.$strURL);
?>