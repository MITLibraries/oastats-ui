<?php

// Enable page debugging
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

session_start();

// Functions that may be needed by various pages
function setImpersonate() {
	// This checks to see whether an administrator has chosen to impersonate another user.
	// Check if the admin flag is set, and is true
	if(isset($_SESSION["admin"])) {
		if($_SESSION["admin"] == true) {
			// If a new impersonation is requested...
			if(isset($_GET["impersonate"])) {
				if($_GET["impersonate"]<>"") {
					// need to stash the user's real username somewhere, if it isn't already
					if(!isset($_SESSION["impersonator"])) {
						$_SESSION["impersonator"] = $_SESSION["user"];
					}
					// while we set the username to the something else
					$_SESSION["user"] = $_GET["impersonate"];
				} else {
					// No username was submitted, so lets stop impersonating
					if(isset($_SESSION["impersonator"])) {
						$_SESSION["user"] = $_SESSION["impersonator"];
						unset($_SESSION["impersonator"]);
					}
				}
				// now lookup this user's information
				warehouseLookup();
			}

		}		
	}
}

function debugSession() {
	foreach($_SESSION as $key => $val) {
		echo "<p>".$key." = ".$val."</p>";
	}
}

function destroySession() {
	unset($_SESSION["user"]);
	unset($_SESSION["admin"]);
	unset($_SESSION["impersonate"]);
	unset($_SESSION["impersonator"]);
	unset($_SESSION["hash"]);	
	unset($_SESSION["fullname"]);
}

function buildReturnURL() {
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
	return $strURL;
}

function buildLogoutURL() {
	switch ($_SERVER["SERVER_NAME"]) {
		case "oastats":
			// local development
			$strURL = "/destroyer.php";
			break;
		case "oastats-dev.mit.edu":
		case "oastats-test.mit.edu":
		case "oastats.mit.edu":
			// oastats servers
			$strURL = "/Shibboleth.sso/Logout?return=/destroyer.php";
			break;
		default:
			$strURL = "/";
	}
	return $strURL;
}


function warehouseLookup() {
	include($_SERVER["DOCUMENT_ROOT"]."/includes/salt.php");

	switch ($_SERVER["SERVER_NAME"]) {
		case "oastats":
			// local development, no Shibboleth available
			if($_SESSION["user"]=="jhkroll"){
				$_SESSION["fullname"] = "Kroll, Jesse";
				$_SESSION["hash"] = "kabam";
			} elseif($_SESSION["user"]=="hal") {
				$_SESSION["fullname"] = "Abelson, Hal";
				$_SESSION["hash"] = "wumpus";
			} else {
				$_SESSION["fullname"] = "Bernhardt, Matthew J";
				$_SESSION["hash"] = "phooey";
			}
			break;
		case "oastats-dev.mit.edu":
		case "oastats-test.mit.edu":
		case "oastats.mit.edu":
			// Look up and hash the needed MIT ID
			$reqA = $_SESSION["user"];

			// From this Touchstone name, Look up and hash the MIT ID for this user
			$warehouse = oci_connect('libuser','tmp3216', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=warehouse.mit.edu)(PORT=1521)))(CONNECT_DATA=(SID=DWRHS)))');

			if (!$warehouse) {
				echo "<p>Error - Unable to connect to data warehouse for identity lookup.</p>";
			} else {
				$reqA = strtoupper($reqA);
				$reqA = str_replace('@MIT.EDU', '', $reqA);
				// search warehouse for ID and Kerberos name, if not found result will be set to false
				$sql = "select FULL_NAME, MIT_ID from library_employee where krb_name_uppercase = '$reqA'";
				$statement = oci_parse($warehouse, $sql);
				oci_execute($statement, OCI_DEFAULT);
				$results = oci_fetch_assoc($statement);
				// disconnect from warehouse
				oci_close($warehouse);
				// Set fullname and temporary ID
				if(!$results["MIT_ID"]) {
					$intID = 0;
					$_SESSION["fullname"] = "";
				} else {
					$intID = $results["MIT_ID"];
					$_SESSION["fullname"] = $results["FULL_NAME"];
				}
				// hash and store temporary ID
				$strHash = md5($salt.$intID);
				$_SESSION["hash"] = $strHash;
			}
			break;
		default:
			// anything else
			unset($_SESSION["user"]);
	}


}

?>