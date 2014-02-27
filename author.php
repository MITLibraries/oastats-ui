<?php
	require_once('includes/header.php'); 

	if(isset($_SESSION["user"])) {
		$reqA = $_SESSION["user"];
		
		require_once('includes/form_filter.php'); 
?>
<div id="tabs">
	<ul>
		<li><a href="data.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Data</a></li>
		<li><a href="time.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Timeline</a></li>
		<li><a href="map.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Map</a></li>
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
	} else {
?>
<p>Please <a href="secure/?return=author.php">login</a> to see your author statistics.</p>
<?php
	}
	require_once('includes/footer.php'); 
?>