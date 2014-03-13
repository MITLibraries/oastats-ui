<?php

	require_once('includes/initialize.php'); 

	// collect possible query parameters
	if(isset($_GET["d"])) {
		$reqD = urldecode($_GET["d"]);
		$strBreadcrumb = $reqD;
	} elseif(isset($_GET["a"])) {
		$reqA = urldecode($_GET["a"]);
		$strBreadcrumb = $reqA;
	}

	require_once('includes/header.php'); 

	require_once('includes/form_filter.php'); 
?>
<div id="tabs" class="tabs">
	<ul>
		<li><a href="data.php?<?php echo $_SERVER["QUERY_STRING"]; ?>">Data</a></li>
		<li><a href="time.php?<?php echo $_SERVER["QUERY_STRING"]; ?>">Timeline</a></li>
		<li><a href="map.php?<?php echo $_SERVER["QUERY_STRING"]; ?>">Map</a></li>
	</ul>
</div>
<?php
	require_once('includes/footer.php'); 
?>