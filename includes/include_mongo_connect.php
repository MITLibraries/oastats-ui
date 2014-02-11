<?php
	try {
		// connect to Mongo
		$m = new Mongo('mongodb://libdb-dev.mit.edu:27017');
		// $m = new Mongo();
		$db = $m->oastats;
		$collection = $db->requests;
		$summaries = $db->summaries;
	} catch (Exception $e) {
		die('Error: ' . $e->getMessage());
	} catch (MongoConnectionException $e) {
		die('Error connecting to MongoDB');
	} catch (MongoException $e) {
		die('Error: ' . $e->getMessage());
	}
?>