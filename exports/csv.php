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

// Transfer cursor objct to local array, for sorting and augmentation
$arrRS = array();
foreach($cursor as $document) {
	switch($strContext){
		case "Data":
			if($_GET["page"]=="/author.php") {
				array_push($arrRS,array('_id'=>$document['_id'],'downloads'=>$document['downloads'],'title'=>$document['title']));
			} else {
				array_push($arrRS,array('_id'=>$document['_id']['display'],'size'=>$document['size'],'downloads'=>$document['downloads']));
			}
			//array_push($arrRS,$document);
			break;
		case "Time":
			// Time cursor needs to be folded from one-dimensional to two-dimensional array
			// fputcsv($export,array_keys($document["dates"][0]));
			foreach($document["dates"] as $item) {
				$strIDField = "Foo";
				if(isset($_GET["page"])) {
					if($_GET["page"]=="/public.php") {
						$strIDField = $document["_id"]["display"];
					} elseif ($_GET["page"]=="/author.php") {
						if(!isset($_GET["filter"])) {
							$strIDField = $document["_id"]["name"];
						} else {
							$strIDField = $document["title"];
						}
					}
				} 
				$arrRS[$item["date"]][$strIDField] = $item["downloads"];
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
				if($_GET["page"]=="/author.php") {
					$arrRS[$item["country"]][$document["_id"]] = $item["downloads"];
				} else {
					$arrRS[$item["country"]][$document["_id"]["display"]] = $item["downloads"];
				}
			}
			break;
		default:
	}
}

/*
echo "<h2>After Fold</h2>";
echo "<pre>";
print_r($arrRS);
echo "</pre>";
*/

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
		ksort($arrRS);
		break;
	case "Map":
		// We've positioned the overall first in the field list, so we can do a simple descending sort
		arsort($arrRS);
		break;
	default:
}

/*
echo "<h2>After Sort</h2>";
echo "<pre id='post-sort'>";
print_r($arrRS);
echo "</pre>";
*/

// Augment array (running totals for timeline, country names for map, etc)
switch($strContext) {
	case "Data":
		break;
	case "Time":
		// augmentation for the timeline is to tally running downloads for each series
		$arrAugmented = array();
		$arrItem = array();
		// Initialize arrItem for public views
		// Author views need to be rebuilt because they can't be predicted accurately
		if($_GET["page"]=="/public.php") {
			while ($field = current($arrQuery["fields"])) {
				$arrItem[$field] = 0;
				if($field!="Date") {
					$arrItem["Cumulative ".$field] = 0;
				}
				next($arrQuery["fields"]);
			}
		} else {
			$arrFields = array(0=>"Date");
			if(!isset($_GET["filter"])) {
				array_push($arrFields,$strIDField);
			} else {
				// loop over query results, rebuilding fieldlist with paper titles
				foreach($cursor as $document){
					array_push($arrFields,$document["title"]);
				}
			}
			$arrQuery["fields"] = $arrFields;

		}
		// Loop through recordset
		while ($item = current($arrRS)) {

			// Reset arrItem with zeros
			while ($field = current($arrQuery["fields"])) {
				$arrItem[$field] = 0;
				next($arrQuery["fields"]);
			}
			reset($arrQuery["fields"]);

			// Date
			$arrItem["Date"] = key($arrRS);

			// Each other point in the array
			foreach ($item as $key => $val) {
				$strBucket = $key;
				$strCumulativeBucket = "Cumulative ".$key;
				$arrItem[$strBucket] = $val;
				if(array_key_exists($strCumulativeBucket, $arrItem)) {
					$arrItem[$strCumulativeBucket] += $val;
				} else {
					$arrItem[$strCumulativeBucket] = $val;
				}

			}

			array_push($arrAugmented,$arrItem);
			next($arrRS);
		}
		$arrRS = $arrAugmented;
		// Query fields so far only have the filtered buckets - need to expand to add cumulative columns
		$arrQuery["fields"] = array_keys($arrItem);
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

/*
echo "<h2>After Augmentation</h2>";
echo "<pre id='post-augment'>";
print_r($arrRS);
echo "</pre>";
*/

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