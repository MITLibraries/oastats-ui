<?php
	session_start();

	include_once("../includes/include_mongo_connect.php");

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
		$admin = $db->admin;
		$arrCriteria = array('username'=>$_SESSION["user"]);
		$result = $admin->find($arrCriteria)->count();

		if ($result > 0) {
			$_SESSION["admin"] = true;
		} else {
			unset($_SESSION["admin"]);
		}

	} 

	include_once("../includes/include_mongo_disconnect.php");

	header('Location: '.$strURL);
?>