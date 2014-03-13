<?php 

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=export.csv");
header("Pragma: no-cache");
header("Expires: 0");
$export = fopen('php://output','w');

require_once('../includes/salt.php'); 

session_start();

// connect to Mongo
require_once('../includes/include_mongo_connect.php');

// build query criteria
if(isset($_GET["user"])) {
	$reqUser = urldecode($_GET["user"]);
}
if(isset($_GET["d"])) {
	$reqD = $_GET["d"];
	$strFilterTerm = '_id';
	$arrCriteria = array('type' => 'author');
	$nextType = "a";
	$strGroup = "Author";
} elseif(isset($_GET["a"])) {
	$reqA = $_GET["a"];
	$reqA = str_replace('@mit.edu','',$reqA);
	$strFilterTerm = '_id';
	$arrCriteria = array('type' => 'handle','parents.mitid'=>$salt.$_SESSION["hash"]);
	$nextType = "";
	$strGroup = "Paper";
} else {
	$strFilterTerm = '_id';
	$arrCriteria = array('type' => 'dlc');
	$nextType = "d";
	$strGroup = "Department, Lab or Center";
}
if(isset($_GET["user"])) {
	$reqUser = urldecode($_GET["user"]);
}

if(isset($_GET["filter"])) {
	$reqFilter = $_GET["filter"];
	$arrFilter = array();
	// iterate over reqFilter, padding out values
	foreach($reqFilter as $term) {
		array_push($arrFilter,array($strFilterTerm=>$term));
	}
	// if a filter is set, that should trump whatever was set as the criteria above
	$arrCriteria = array( '$or' => $arrFilter);
}

$arrProjection = array(
	'_id'=>1,
	'size'=>1,
	'downloads'=>1
);

// execute query into cursor
$cursor = $summaries->find($arrCriteria,$arrProjection);

// Field labels in the first row
$arrLabels = array($strGroup);
if(!isset($reqA)) { array_push($arrLabels,"Articles"); }
array_push($arrLabels,"Downloads");
fputcsv($export,$arrLabels);

// Field values
foreach($cursor as $document) {

	$arrLine = array($document["_id"]);
	if(!isset($reqA)) {
		array_push($arrLine,$document["size"]);
	}
	array_push($arrLine,$document["downloads"]);
	fputcsv($export,$arrLine);
}

require_once('../includes/include_mongo_disconnect.php'); 
?>