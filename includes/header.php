<!doctype html>
<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

?>
<html lang="en">
	<head>
		<?php require_once('includes/include_mongo_connect.php'); ?>
		<title>Open Access Statistics</title>
		<link rel="stylesheet" href="styles/reset.css">
		<link rel="stylesheet" href="styles/styles.css">
		<link rel="stylesheet" href="styles/listbuilder.css">
		<script src="https://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script src="scripts/d3.v3.min.js" charset="utf-8"></script>
		<script src="scripts/d3.geo.projection.v0.min.js" charset="utf-8"></script>
		<script src="scripts/topojson.v1.min.js" charset="utf-8"></script>
		<script src="scripts/datamaps.world.min.js" charset="utf-8"></script>
		<script src="scripts/listbuilder.js" charset="utf-8"></script>
	</head>
	<body>
		<div id="page">
			<div class="page-inner">
				<div id="masthead">
					<h1>Open Access Statistics</h1>
				</div>
				<?php require_once('includes/include_login.php'); ?>
				<section class="main">
