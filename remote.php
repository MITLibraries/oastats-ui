<!doctype html>
<html lang="en">
	<head>
		<title>Connection test to remote Mongo</title>
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
				<h1>Connection Test</h1>

<p>Test begins.</p>
<?php
	error_reporting(E_ALL);
	ini_set('display_errors',TRUE);
	ini_set('display_startup_errors',TRUE);
?>
<h2>Remote Dev:</h2>
<pre>
<?php
	$r = new Mongo('mongodb://libdb-dev.mit.edu:27017');
	$db = $r->oastats;
	$collection = $db->requests;
	$cursor = $collection->find();
	var_dump($r);
	var_dump($db);
	var_dump($collection);
	var_dump($cursor);
	foreach($cursor as $document) {
		var_dump($document) . "\n";
	}

?>
</pre>
<h2>Remote Test:</h2>
<pre>
<?php
	$r = new Mongo('mongodb://libdb-test.mit.edu:27017');
	$db = $r->oastats;
	$collection = $db->requests;
	$cursor = $collection->find();
	var_dump($r);
	var_dump($db);
	var_dump($collection);
	var_dump($cursor);
	foreach($cursor as $document) {
		var_dump($document) . "\n";
	}
?>
</pre>
<h2>Remote Prod:</h2>
<pre>
<?php
	$r = new Mongo('mongodb://libdb-1.mit.edu:27017');
	$db = $r->oastats;
	$collection = $db->requests;
	$cursor = $collection->find();
	var_dump($r);
	var_dump($db);
	var_dump($collection);
	var_dump($cursor);
	foreach($cursor as $document) {
		var_dump($document) . "\n";
	}
?>
</pre>
<p>Test finished.</p>
			</section>
			<?php require_once('includes/include_footer.html'); ?>
		</div>
	</body>
</html>