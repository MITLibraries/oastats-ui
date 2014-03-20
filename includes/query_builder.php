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

function buildQueryCriteria($salt,$strQueryType) {
	$arrTemp = array();
	// first, assume no filter set - this is the default
	switch($strQueryType) {
		case "Data":
			$arrTemp = array(
				'type'=>'dlc'
			);
			if(isset($_GET["page"])) {
				if($_GET["page"]=="/author.php") {
					$arrTemp = array(
						'type' => 'handle',
						'parents.mitid' => $salt.$_SESSION["hash"]
					);
				}
			}
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
			if(isset($_GET["page"])) {
				if($_GET["page"]=="/author.php") {
					$arrTemp = array(
						'type' => 'handle',
						'parents.mitid' => $salt.$_SESSION["hash"]
					);
				}
			}
			break;
		default:
	}
	// if a filter is set, then we build up a mongo Or clause
	if(isset($_GET["filter"])) {
		$reqFilter = $_GET["filter"];
		$arrFilter = array();
		foreach($reqFilter as $term) {
			array_push($arrFilter,array('_id'=>$term));
		}
		$arrTemp = array('$or'=>$arrFilter);
	}
	return $arrTemp;
}

function buildQueryFields($strQueryType) {
	$arrTemp = array();
	switch($strQueryType) {
		case "Data":
			$arrTemp = array(
				"Department, Lab or Center",
				"Articles",
				"Downloads"
			);
			if(isset($_GET["page"])) {
				if($_GET["page"]=="/author.php") {
					$arrTemp = array(
						"Article",
						"URL",
						"Downloads"
					);
				}
			}
			break;
		case "Time":
			if(isset($_GET["filter"])) {
				$arrFilter = $_GET["filter"];
				$arrTemp = array("Date");
				foreach($arrFilter as $filter) {
					array_push($arrTemp,$filter." - Downloads",$filter." - Cumulative");
				}
			} else {
				$arrTemp = array(
					"Date",
					"Downloads",
					"Cumulative"
				);
			}
			break;
		case "Map":
			if(isset($_GET["filter"])) {
				$arrFilter = $_GET["filter"];
				$arrTemp = array("Country","Overall");
				foreach($arrFilter as $filter) {
					array_push($arrTemp,$filter);
				}
			} else {
				$arrTemp = array("Country","Overall");
			}
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
			if(isset($_GET["page"])) {
				if($_GET["page"]=="/author.php") {
					$arrTemp = array(
						'_id' => 1,
						'title' => 1,
						'downloads' => 1
					);
				}
			}
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

function buildQuery($salt,$strQueryType) {
	// fields
	$arrFields = buildQueryFields($strQueryType);
	// criteria
	$arrCriteria = buildQueryCriteria($salt,$strQueryType);
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