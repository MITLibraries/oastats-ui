<?php
	try {
		// connect to Mongo
		switch ($_SERVER["SERVER_NAME"]) {
			case "oastats-dev.mit.edu":
				$m = new Mongo('mongodb://libdb-dev.mit.edu:27017');
				break;
			case "oastats-test.mit.edu":
				$m = new Mongo('mongodb://libdb-test.mit.edu:27017');
				break;
			case "oastats.mit.edu":
				$m = new Mongo('mongodb://libdb-test.mit.edu:27017');
				break;
			default:			
				$m = new Mongo();
		}
		// $m = new Mongo();
		$db = $m->oatest;
		$collection = $db->request;
		$summaries = $db->summaries;
	} catch (Exception $e) {
		die('Error: ' . $e->getMessage());
	} catch (MongoConnectionException $e) {
		die('Error connecting to MongoDB');
	} catch (MongoException $e) {
		die('Error: ' . $e->getMessage());
	}
?>