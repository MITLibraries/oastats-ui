<?php
	include_once("../includes/initialize.php");

	include_once("../includes/include_mongo_connect.php");

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

	$strPageTitle = "You have opted out";

	require_once($_SERVER["DOCUMENT_ROOT"].'/includes/header.php'); 

	echo "<div id='content'>";
	// Look up whether this user has already opted out
	$optout = $db->optout;
	$arrCriteria = array('username'=>$_SESSION["user"]);
	$result = $optout->find($arrCriteria)->count();
	if ($result > 0) {
		// already opted out
		echo "<p>Thank your visiting the opt-out page for the MIT Libraries' Open Access Article Statistics service. We have previously received a similar request for your email address. If you have continued to receive messages from this effort, please contact: <a href='mailto:oastats@mit.edu'>oastats@mit.edu</a>.</p>";
		echo "<p>We apologize for this inconvenience.</p>";
	} else {
		// new opt-out
		$result = $optout->insert($arrCriteria);
		echo "<p>The MIT Libraries have received your request not to be contacted by the Open Access Article Statistics service. You will no longer receive communications connected to this effort.</p>";
		echo "<p>Thank you for your time.</p>";
	}
	echo "</div";

	require_once($_SERVER["DOCUMENT_ROOT"].'/includes/footer.php'); 

	// Lookup full name and MIT ID
	// warehouseLookup();

	include_once("../includes/include_mongo_disconnect.php");

?>