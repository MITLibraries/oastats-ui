<?php
	require_once('includes/salt.php'); 

	require_once('includes/header.php'); 

	if(isset($_SESSION["user"])) {

		// Look up and hash the needed MIT ID

		// By default this is the logged in username...
		$reqA = $_SESSION["user"];

		// ... but if the user is an admin ...		
		if(isset($_SESSION["admin"])) {
			if($_SESSION["admin"] == true) {
				// ... and they've submitted a different username ...
				if(isset($_GET["impersonate"])) {
					if($_GET["impersonate"]) {
						// ... then swap that username in for the default
						$_SESSION["impersonate"] = $_GET["impersonate"];
						$reqA = $_SESSION["impersonate"];						
					}
				}
			}
		}

		// From this Touchstone name, Look up and hash the MIT ID for this user
		$warehouse = oci_connect('libuser','tmp3216', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=warehouse.mit.edu)(PORT=1521)))(CONNECT_DATA=(SID=DWRHS)))');

		if (!$warehouse) {
			echo "<p>Error - Unable to connect to data warehouse for identity lookup.</p>";
		} else {

			$reqA = strtoupper($reqA);
			$reqA = str_replace('@MIT.EDU', '', $reqA);
			// search warehouse for ID and Kerberos name, if not found result will be set to false
			$sql = "select MIT_ID from library_employee where krb_name_uppercase = '$reqA'";
			$statement = oci_parse($warehouse, $sql);
			oci_execute($statement, OCI_DEFAULT);
			$results = oci_fetch_assoc($statement);

			// disconnect from warehouse
			oci_close($warehouse);

			if(!$results["MIT_ID"]) {
				$intID = 0;
			} else {
				$intID = $results["MIT_ID"];
			}

			$strHash = md5($salt.$intID);
			$_SESSION["hash"] = $strHash;

		}

		require_once('includes/form_filter.php'); 
?>
<div id="tabs" class="tabs">
	<ul>
		<li><a href="data.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Data</a></li>
		<li><a href="time.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Timeline</a></li>
		<li><a href="map.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Map</a></li>
	</ul>
</div>
<?php
	} else {
?>
<p>Please <a href="secure/?return=author.php">login</a> to see your author statistics.</p>
<?php
	}
	require_once('includes/footer.php'); 
?>