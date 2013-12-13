<!doctype html>
<html lang="en">
<head>
	<title>Connection test to remote Mongo</title>
</head>
<body>
<p>Test begins.</p>
<h2>Local</h2>
<?php
	error_reporting(E_ALL);
	ini_set('display_errors',TRUE);
	ini_set('display_startup_errors',TRUE);
	$l = new MongoClient();
	$db = $l->comedy;
	$collection = $db->cartoons;
	$cursor = $collection->find();
	var_dump($l);
	var_dump($db);
	var_dump($collection);
	var_dump($cursor);
	foreach($cursor as $document) {
		var_dump($document) . "\n";
	}
?>
<h2>Remote Dev:</h2>
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
<h2>Remote Test:</h2>
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
<h2>Remote Dev:</h2>
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
<p>Test finished.</p>
</body>
</html>