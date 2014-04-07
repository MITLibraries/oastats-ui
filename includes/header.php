<!doctype html>
<?php


// generic variables for determining what level is being visualized
$reqD = "";
$reqA = "";

if(!isset($strPageTitle)) {
	$strPageTitle = '';
}

?>
<html lang="en">
	<head>
		<?php require_once('includes/include_mongo_connect.php'); ?>
		<title><?php echo $strPageTitle; ?> | MIT's Open Access Article Statistics</title>
		<link rel="stylesheet" href="styles/reset.css">
		<link rel="stylesheet" href="styles/styles.css">
<?php
	switch ($_SERVER["SERVER_NAME"]) {
		case "oastats-dev.mit.edu":
		case "oastats-test.mit.edu":
		case "oastats.mit.edu":
?>
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<?php
		default:
?>
		<script src="scripts/jquery-1.8.3.js"></script>
		<script src="scripts/jquery-ui-1.10.3.js"></script>
<?php
	}
?>		
		<script src="scripts/jquery.ba-bbq.min.js" charset="utf-8"></script>
		<script src="scripts/d3.v3.min.js" charset="utf-8"></script>
		<script src="scripts/d3.geo.projection.v0.min.js" charset="utf-8"></script>
		<script src="scripts/topojson.v1.min.js" charset="utf-8"></script>
		<script src="scripts/datamaps.world.min.js" charset="utf-8"></script>
		<script src="scripts/underscore.js"></script>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-1760176-28', 'mit.edu');
		  ga('send', 'pageview');

		</script>
	</head>
	<body>
		<div id="page">
			<div class="page-inner">
				<div id="masthead">
					<h1>MIT's Open Access Article Statistics</h1>
				</div>
				<?php require_once('includes/navigation.php'); ?>
				<h2 class="pagetitle"><?php echo $strPageTitle; ?></h2>
				<section class="main">
