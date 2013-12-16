<?php
	try {
		// disconnect from Mongo
		$m->close();
	} catch (MongoConnectionException $e) {
		die('Error disconnecting to MongoDB');
	} catch (MongoException $e) {
		die('Error: ' . $e->getMessage());
	}
?>