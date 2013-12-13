<?php
	try {
		// connect to Mongo
		$m = new Mongo('mongodb://libdb-dev.mit.edu:27017');
		$db = $m->oastats;
		$collection = $db->requests;
	} catch (MongoConnectionException $e) {
		die('Error connecting to MongoDB');
	} catch (MongoException $e) {
		die('Error: ' . $e->getMessage());
	}
?>