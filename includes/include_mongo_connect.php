<?php
	try {
		// connect to Mongo
		switch ($_SERVER["SERVER_NAME"]) {
			case "oastats-dev.mit.edu":
				$m = new Mongo('mongodb://libdb-dev.mit.edu:27017');
				$db = $m->oatest;
				$collection = $db->request;
				$summaries = $db->summary;
				break;
			case "oastats-test.mit.edu":
				// this should be libdb-test when we launch
				$m = new Mongo('mongodb://libdb-dev.mit.edu:27017');
				$db = $m->oastats;
				$collection = $db->requests;
				$summaries = $db->summary;
				break;
			case "oastats.mit.edu":
				$m = new Mongo('mongodb://libdb-3.mit.edu:270171');
				$db = $m->oatest;
				$collection = $db->request;
				$summaries = $db->summaries;
				break;
			default:
				$m = new Mongo();
				$db = $m->oatest;
				$collection = $db->request;
				$summaries = $db->summary;
		}
	} catch (Exception $e) {
		die('<div class="error"><h2>General Error</h2><p>This website was unable to connect to the database server. If this is the first time you have seen this message, please try reloading the page. The specific error returned was:</p><p>' . $e->getMessage().'</p></div>');
	} catch (MongoConnectionException $e) {
		die('<div class="error"><h2>Connection Error</h2><p>This website was unable to connect to the database server. If this is the first time you have seen this message, please try reloading the page. The specific error returned was:</p><p>' . $e->getMessage().'</p></div>');
	} catch (MongoException $e) {
		die('<div class="error"><h2>Database Error</h2><p>An error has occurred within this site\'s database server. If this is the first time you have seen this message, please try reloading the page. The specific error returned was:</p><p>' . $e->getMessage().'</p></div>');
	}
?>