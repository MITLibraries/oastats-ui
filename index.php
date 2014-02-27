<?php

	// collect possible query parameters
	if(isset($_GET["d"])) {
		$reqD = urldecode($_GET["d"]);
		$strBreadcrumb = $reqD;
	} elseif(isset($_GET["a"])) {
		$reqA = urldecode($_GET["a"]);
		$strBreadcrumb = $reqA;
	} elseif(isset($_GET["p"])) {
		$reqA = urldecode($reqUser);
		$strBreadcrumb = $reqA;
	}

	require_once('includes/header.php'); 

	require_once('includes/form_filter.php'); 
?>
<div id="tabs">
	<ul>
		<li><a href="data.php?<?php echo $_SERVER["QUERY_STRING"]; ?>">Data</a></li>
		<li><a href="time.php?<?php echo $_SERVER["QUERY_STRING"]; ?>">Timeline</a></li>
		<li><a href="map.php?<?php echo $_SERVER["QUERY_STRING"]; ?>">Map</a></li>
	</ul>
</div>
<script>
$(document).ready(function() {

	$( "#tabs" ).tabs({
		beforeLoad: function( event, ui ) {
			ui.panel.html("Loading...");
			ui.jqXHR.error(function() {
				ui.panel.html(
				"Sorry, the contents of this tab could not be loaded right now." );
			});
		}
	});
});	
</script>		
<?php
	require_once('includes/footer.php'); 
?>