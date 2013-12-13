<!doctype html>
<?php

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

?>
<html lang="en">
	<head>
		<?php require_once('includes/include_mongo_connect.php'); ?>
		<title>OA Statistics Mockup</title>
		<link rel="stylesheet" href="styles/reset.css">
		<link rel="stylesheet" href="styles/styles.css">
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
	</head>
	<body>
		<div class="container">
			<?php require_once('includes/include_header.html'); ?>
			<div id="breadcrumb">
				<p>
					<span class="semantic">You are here: </span>
					<span class="level home"><a href="/oastats/">Home</a></span>
						<span class="semantic">in subsection </span>
						<span class="level">Page</span>
				</p>
			</div>
			<section class="main">
				<h1>Open Access Statistics Mockup</h1>
				<?php require_once('includes/form_filter.php'); ?>
			</section>
			<?php require_once('includes/include_footer.html'); ?>
		</div>
		<?php require_once('includes/include_mongo_disconnect.php'); ?>
	</body>
</html>			