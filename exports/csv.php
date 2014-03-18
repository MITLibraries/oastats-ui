<?php 

require_once('../includes/salt.php'); 

// connect to Mongo
require_once('../includes/include_mongo_connect.php');

require_once('../includes/initialize.php');

require_once('../includes/query_builder.php');

$strContext = findContext();
$arrQuery = buildQuery($strContext);

$strFilename = "OA_Stats_".$strContext.".csv";
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$strFilename);
header("Pragma: no-cache");
header("Expires: 0");

$export = fopen('php://output','w');

/*
echo "<pre>";
print_r($arrQuery);
echo "</pre>";
*/

// execute query into cursor
$cursor = $summaries->find($arrQuery["criteria"],$arrQuery["projection"]);

// ############################################################################
// ############################################################################
// ############################################################################
//
// Each different tab will probably need its own rendering loop, apparently

// Field labels
fputcsv($export,$arrQuery["fields"]);

// Transfer recordset to local array
$arrRS = array();
foreach($cursor as $document) {
	switch($strContext){
		case "Data":
			array_push($arrRS,$document);
			break;
		case "Time":
			// fputcsv($export,array_keys($document["dates"][0]));
			foreach($document["dates"] as $item) {
				array_push($arrRS,$item);
			}			
			break;
		case "Map":
			// fputcsv($export,array_keys($document["countries"][0]));
			foreach($document["countries"] as $item) {
				array_push($arrRS,$item);
			}			
			break;
		default:
	}
}

// Sort array
switch($strContext) {
	case "Data":
	case "Time":
		sort($arrRS);
		break;
	case "Map":
		arsort($arrRS);
		usort($arrRS, function($a, $b) {
    		return $b['downloads'] - $a['downloads'];
		});
		break;
	default:
}

// Augment array (running totals for timeline, country names for map, etc)
switch($strContext) {
	case "Data":
		break;
	case "Time":
		$intRunning = 0;
		for($i=0;$i<count($arrRS);$i++) {
			$intRunning += $arrRS[$i]['downloads'];
			$arrRS[$i]['cumulative'] = $intRunning;
		}
		break;
	case "Map":
		break;
	default:
}

// Spit out contents
foreach($arrRS as $line) {
	fputcsv($export,$line);
}

require_once('../includes/include_mongo_disconnect.php'); 
?>