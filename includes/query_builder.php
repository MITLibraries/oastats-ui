<?php
// this tries to abstract out the process of building queries, so that the display armature and export armature don't have to maintain separate-but-identical query logic

function findContext() {
	$strContext = $_SERVER["SCRIPT_NAME"];
	$strQueryString = $_SERVER["QUERY_STRING"];
	switch($strContext) {
		case "/tab_data.php":
			$strQueryType = "Data";
			break;
		case "/data/json-time-running.php":
			$strQueryType = "Time";
			break;
		case "/tab_map.php":
			$strQueryType = "Map";
			break;
		case "/exports/csv.php":
			// CSV export is passed the context via the tab key
			switch($_GET["tab"]) {
				case "Data":
					$strQueryType = "Data";
					break;
				case "Timeline":
					$strQueryType = "Time";
					break;
				case "Map":
					$strQueryType = "Map";
					break;
				default:
					$strQueryType = "Unknown";
			}
			break;
		default:
			$strQueryType = "Unknown";
	}
	return $strQueryType;
}

function buildQueryCriteria($strQueryType) {
	$arrTemp = array();
	switch($strQueryType) {
		case "Data":
			$arrTemp = array(
				'type'=>'dlc'
			);
			break;
		case "Time":
			$arrTemp = array(
				'_id'=>'Overall'
			);
			break;
		case "Map":
			$arrTemp = array(
				'_id'=>'Overall'
			);
			break;
		default:
	}
	return $arrTemp;
}

function buildQueryFields($strQueryType) {
	$arrTemp = array();
	switch($strQueryType) {
		case "Data":
			$arrTemp = array(
				"Department, Lab or Center",
				"Downloads",
				"Articles"
			);
			break;
		case "Time":
			$arrTemp = array(
				"Date",
				"Downloads",
				"Cumulative"
			);
			break;
		case "Map":
			$arrTemp = array(
				"Country",
				"Downloads"
			);
			break;
		default:
	}
	return $arrTemp;
}

function buildQueryProjection($strQueryType) {
	$arrTemp = array();
	switch($strQueryType) {
		case "Data":
			$arrTemp = array(
				'_id'=>1,
				'size'=>1,
				'downloads'=>1
			);
			break;
		case "Time":
			$arrTemp = array(
				'_id'=>1,
				'dates'=>1
			);
			break;
		case "Map":
			$arrTemp = array(
				'countries'=>1
			);
			break;
		default:
	}
	// the author report explicitly sends the current username (even impersonated) as the "a" key
	// in these cases we need to get article titles for display
	if(isset($_GET["a"])) {
		if($_GET["a"]<>"") {
			$arrTemp["title"]=1;
		}
	}
	return $arrTemp;
}

function buildQuery($strQueryType) {
	// fields
	$arrFields = buildQueryFields($strQueryType);
	// criteria
	$arrCriteria = buildQueryCriteria($strQueryType);
	// projection
	$arrProjection = buildQueryProjection($strQueryType);
	// assemble final array
	$arrQuery = array(
		"criteria" => $arrCriteria,
		"fields" => $arrFields,
		"projection" => $arrProjection
	);
	return $arrQuery;
}

?>