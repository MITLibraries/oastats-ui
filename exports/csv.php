<?php 

require_once('../includes/salt.php'); 

require_once('../includes/include_mongo_connect.php');

require_once('../includes/initialize.php');

require_once('../includes/query_builder.php');

// Determine export context
$strContext = findContext();

// Build query
$arrQuery = buildQuery($salt,$strContext);
/*
echo "<pre>";
print_r($arrQuery);
echo "</pre>";
*/

// Execute query into cursor object
$cursor = $summaries->find($arrQuery["criteria"],$arrQuery["projection"]);
/*
echo '<pre>';
foreach($cursor as $document) {
	print_r($document);
}
echo '</pre>';
*/

// Transfer cursor objct to local array, for sorting and augmentation
$arrRS = array();
foreach($cursor as $document) {
	switch($strContext){
		case "Data":
			array_push($arrRS,$document);
			break;
		case "Time":
			// fputcsv($export,array_keys($document["dates"][0]));
			foreach($document["dates"] as $item) {
				$arrRS[$item["date"]][$document["_id"]] = $item["downloads"];
				echo '<pre>';
				print_r($arrRS);
				echo '</pre>';
				// array_push($arrRS,$item);
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
		if(!isset($_GET["page"])) {	break; }
		if($_GET["page"]=="/author.php") {
			// Author sorts
			usort($arrRS, function($a, $b) {
	    		return strcmp($a['title'],$b['title']);
			});
			for($i=0;$i<count($arrRS);$i++) {
				uksort($arrRS[$i], function($a, $b) {
					if($a===$b){ return 0;}
					$order = array("title","_id","downloads");
					$posA = array_search($a,$order);
					$posB = array_search($b,$order);
					if ($posB!==false && $posA!==false) { return ($posA<$posB) ? -1 : 1; }
					if ($posA!==false) { return -1; }
					if ($posB!==false) { return 1; }
					return ($a < $b) ? -1 : 1;
				});
			}
		} else {
			// DLC sorts
			sort($arrRS);
			for($i=0;$i<count($arrRS);$i++) {
				uksort($arrRS[$i], function($a, $b) {
					if($a===$b){ return 0;}
					$order = array("_id","size","downloads");
					$posA = array_search($a,$order);
					$posB = array_search($b,$order);
					if ($posB!==false && $posA!==false) { return ($posA<$posB) ? -1 : 1; }
					if ($posA!==false) { return -1; }
					if ($posB!==false) { return 1; }
					return ($a < $b) ? -1 : 1;
				});
			}
		}
		break;
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

$strFilename = "OA_Stats_".$strContext.".csv";
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$strFilename);
header("Pragma: no-cache");
header("Expires: 0");

$export = fopen('php://output','w');

// Render field labels
fputcsv($export,$arrQuery["fields"]);

// Render contents
foreach($arrRS as $line) {
	fputcsv($export,$line);
}

require_once('../includes/include_mongo_disconnect.php'); 
?>