<!doctype html>
<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$strBreadcrumb = "";
// collect possible query parameters
$reqD = "";
if(isset($_GET["d"])) {
	$reqD = urldecode($_GET["d"]);
	$strBreadcrumb = $reqD;
}
$reqA = "";
if(isset($_GET["a"])) {
	$reqA = urldecode($_GET["a"]);
	$strBreadcrumb = $reqA;
}
if(isset($_GET["user"])) {
	$reqUser = urldecode($_GET["user"]);
}
?>
<html lang="en">
	<head>
		<?php require_once('includes/include_mongo_connect.php'); ?>
		<title>Open Access Statistics - Mockup One</title>
		<link rel="stylesheet" href="styles/reset.css">
		<link rel="stylesheet" href="styles/one.css">
		<link rel="stylesheet" href="styles/listbuilder.css">
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
		<script src="http://d3js.org/d3.geo.projection.v0.min.js" charset="utf-8"></script>
		<script src="http://d3js.org/topojson.v1.min.js" charset="utf-8"></script>
		<script src="scripts/listbuilder.js" charset="utf-8"></script>
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
	</head>
	<body>
		<div class="container">
			<?php require_once('includes/include_header.html'); ?>
			<div id="breadcrumb">
				<p>
					<span class="semantic">You are here: </span>
					<span class="level home"><a href="/oastats/">Home</a></span>
					<span class="semantic">in subsection </span>
					<span class="level"><a href="one.php">Mockup One</a></span>
					<?php if($strBreadcrumb!="") { ?>
						<span class="semantic">in subsection </span>
						<span class="level"><?php echo $strBreadcrumb; ?></span>
					<?php }; ?>
				</p>
			</div>
			<section class="main">
				<h1>Open Access Statistics - Mockup One</h1>
				<?php require_once('includes/form_filter.php'); ?>
				<div id="tabs">
					<ul>
						<li><a href="data.php?<?php echo $_SERVER["QUERY_STRING"]; ?>">Data</a></li>
						<li><a href="time.php?<?php echo $_SERVER["QUERY_STRING"]; ?>">Timeline</a></li>
						<li><a href="map.php?<?php echo $_SERVER["QUERY_STRING"]; ?>">Map</a></li>
					</ul>
				</div>
			</section>
			<?php require_once('includes/include_footer.html'); ?>
		</div>
		<?php require_once('includes/include_mongo_disconnect.php'); ?>
	</body>
</html>			