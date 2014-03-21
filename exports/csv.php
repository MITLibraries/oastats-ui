<?php 

require_once('../includes/salt.php'); 

require_once('../includes/include_mongo_connect.php');

require_once('../includes/initialize.php');

require_once('../includes/query_builder.php');

iconv_set_encoding("output_encoding", "UTF-8");

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

if($strContext=="Map") {
	header("Content-Disposition: inline");
}
*/

// Transfer cursor objct to local array, for sorting and augmentation
$arrRS = array();
foreach($cursor as $document) {
	switch($strContext){
		case "Data":
			array_push($arrRS,$document);
			break;
		case "Time":
			// Time cursor needs to be folded from one-dimensional to two-dimensional array
			// fputcsv($export,array_keys($document["dates"][0]));
			foreach($document["dates"] as $item) {
				$arrRS[$item["date"]][$document["_id"]] = $item["downloads"];
			}
			break;
		case "Map":
			// Map cursor also needs to be folded, and subtotals ("Overall") generated
			foreach($document["countries"] as $item) {
				if(array_key_exists($item["country"],$arrRS) && array_key_exists("Overall",$arrRS[$item["country"]])) {
					$arrRS[$item["country"]]["Overall"] += $item["downloads"];
				} else {
					$arrRS[$item["country"]]["Overall"] = $item["downloads"];
				}
				$arrRS[$item["country"]][$document["_id"]] = $item["downloads"];
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
	    		return strcasecmp($a['title'],$b['title']);
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
			usort($arrRS, function($a,$b) {
				return strcasecmp($a['_id'],$b['_id']);
			});
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
		echo '<p>Sorting by date</p>';
		ksort($arrRS);
		print_r($arrQuery["fields"]);
		echo '<h2>After Sorting, Before augmentation</h2>';
		echo '<pre>';
		print_r($arrRS);
		echo '</pre>';
		break;
	case "Map":
		// We've positioned the overall first in the field list, so we can do a simple descending sort
		arsort($arrRS);
		break;
	default:
}

// Augment array (running totals for timeline, country names for map, etc)
switch($strContext) {
	case "Data":
		break;
	case "Time":
		// augmentation for the timeline is to tally running downloads for each series
		$arrAugmented = array();
		for($i=0;$i<count($arrRS);$i++) {
			echo $arrRS[$i];
		}
		echo '<pre>';
		print_r($arrAugmented);
		echo '</pre>';
		break;
	case "Map":
		// This needs to load country names, as well as zero pad each column
		$arrAugmented = array();
		// Load country data
		$countries = json_decode(file_get_contents('../data/countries.json'),true);
		while ($item = current($arrRS)) {
			// set up blank arrItem
			$arrItem = array();
			// debug this item
			$strCountryName = 'Unknown Country';
			foreach($countries as $country) {
				if($country['cca3']===key($arrRS)) {
					$strCountryName = $country['name'];
					break;
				}
			}
			// push country to item
			array_push($arrItem,$strCountryName);
			// loop over fields array, pushing each value to item
			foreach($arrQuery["fields"] as $term) {
				if($term!="Country") {
					if(array_key_exists($term,$item)) {
						array_push($arrItem,$item[$term]);
					} else {
						array_push($arrItem,0);
					}
				}
			}
			array_push($arrAugmented,$arrItem);
			next($arrRS);
		}
		$arrRS = $arrAugmented;
		// Fieldnames also need to be swapped out for filtered author reports
		if($_GET["page"]=="/author.php" && isset($_GET["filter"])) {
			foreach($cursor as $document) {
				// via http://stackoverflow.com/questions/8668826/search-and-replace-value-in-php-array
				$arrQuery["fields"] = array_replace($arrQuery["fields"],
					array_fill_keys(
						array_keys($arrQuery["fields"], $document["_id"]),
						$document["title"]
					)
				);
			}
		}
		break;
	default:
}

$strFilename = "OA_Stats_".$strContext.".csv";
header("Content-type: text/csv; charset=utf-8");
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