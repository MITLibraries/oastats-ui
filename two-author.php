<?php
	$strBreadcrumb = "My Stats";
	require_once('includes/two_header.php'); 
	if(isset($_SESSION["user"])) {
		$reqA = $_SESSION["user"];
?>
<script>
$(document).ready(function() {

	var listbuilder = app.listbuilder;
	listbuilder.initialize(
		debugFlag = false
	);

	$('.listcontainer .option').click(function() {
		// Stop whatever might otherwise happen
		// event.preventDefault();
		// Pass the value of the clicked item to listbuilder
		var target = $(this);
		listbuilder.getClick(target);
	});

	$('.listcontainer input#listfilter').bind('keyup', function(e) {
		listbuilder.getFilter(this.value);
	});

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
<?php require_once('includes/form_filter.php'); ?>
<div id="tabs">
	<ul>
		<li><a href="data.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Data</a></li>
		<li><a href="time.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Timeline</a></li>
		<li><a href="map.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Map</a></li>
	</ul>
</div>
<?php
	} else {
?>
<p>Please <a href="secure/?return=two-author.php">login</a> to see your author statistics.</p>
<?php
	}
	require_once('includes/two_footer.php'); 
?>