<?php 


require_once('../includes/salt.php'); 

// connect to Mongo
require_once('../includes/include_mongo_connect.php');

require_once('../includes/initialize.php');

require_once('../includes/query_builder.php');

$strContext = findContext();
$arrQuery = buildQuery($strContext);

$strFilename = "oastats_".$strContext.".csv";
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

// Data exports have to handle their field headings here
if($strContext=="Data"){
	fputcsv($export,array_keys($arrQuery["projection"]));
}

foreach($cursor as $document) {
	// we have to custom parse each different type, unfortunately
	switch($strContext){
		case "Data":
			fputcsv($export,$document);
			break;
		case "Time":
			fputcsv($export,array_keys($document["dates"][0]));
			foreach($document["dates"] as $item) {
				fputcsv($export,$item);
			}			
			break;
		case "Map":
			fputcsv($export,array_keys($document["countries"][0]));
			foreach($document["countries"] as $item) {
				fputcsv($export,$item);
			}			
			break;
		default:
	}
}

require_once('../includes/include_mongo_disconnect.php'); 
?>