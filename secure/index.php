<?php
	session_start();

	include_once("../includes/initialize.php");

	include_once("../includes/include_mongo_connect.php");

	// Build Return URL
	switch ($_SERVER["SERVER_NAME"]) {
		case "oastats":
			// local development, no HTTPS
			$strBaseURL = "http://".$_SERVER["SERVER_NAME"]."/";
			break;
		case "oastats-dev.mit.edu":
		case "oastats-test.mit.edu":
		case "oastats.mit.edu":
			// oastats servers
			$strBaseURL = "https://".$_SERVER["SERVER_NAME"]."/";
			break;
		default:
			$strBaseURL = "http://web.mit.edu/";
	}
	if(isset($_GET["return"])) {
		$strURL = $strBaseURL . $_GET["return"];
	} else {
		$strURL = $strBaseURL . "index.php";
	}

	// Set Username
	switch ($_SERVER["SERVER_NAME"]) {
		case "oastats":
			// local development, no Shibboleth available
			$_SESSION["user"] = "mjbernha@mit.edu";
			break;
		case "oastats-dev.mit.edu":
		case "oastats-test.mit.edu":
		case "oastats.mit.edu":
			// oastats servers
			// Check for server variable, and update session variable if needed
			if(isset($_SERVER["REMOTE_USER"])) {
				$_SESSION["user"] = $_SERVER["REMOTE_USER"];
			} else {
				unset($_SESSION["user"]);
			}
			break;
		default:
			// anything else
			unset($_SESSION["user"]);
	}

	// Look up this user's admin status
	$admin = $db->admin;
	$arrCriteria = array('username'=>$_SESSION["user"]);
	$result = $admin->find($arrCriteria)->count();
	if ($result > 0) {
		$_SESSION["admin"] = true;
	} else {
		unset($_SESSION["admin"]);
	}

	// Lookup full name and MIT ID
	warehouseLookup();

	include_once("../includes/include_mongo_disconnect.php");

	header('Location: '.$strURL);
?>