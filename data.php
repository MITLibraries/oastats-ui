<?php 

require_once('includes/salt.php'); 

session_start();

?>
<link rel="stylesheet" href="styles/data.css">
<link rel="stylesheet" href="styles/jquery.dataTables.css">
<script src="scripts/jquery.dataTables.min.js" charset="utf-8"></script>
<script>
	$(document).ready(function() {

		var dt = $( "table.data" ).dataTable({
			"bFilter": false,
			"bLengthChange": false,
			"bInfo": false,
			"sPaginationType": "full_numbers",
			"iDisplayLength": 25
		});

		var toggle = $(".paging_full_numbers").append('<a class="showall paginate_button">Show All</a>');

		$(".showall").click(function() {
			console.log("clicked");
			var dtSettings = dt.fnSettings();
			var label = $(this).html();
			if(label == "Show All") {
				dtSettings._iDisplayLength = -1;
				$(this).text("Show 25");
			} else {
				dtSettings._iDisplayLength = 25;
				$(this).text("Show All");
			}
			dt.fnDraw();
			console.log("changed");
		});

		// Set export options
		$("#exports").empty();
		$("#exports").append('<li><a data-format="csv">CSV</a></li>')
			.append('<li><a data-format="pdf">PDF</a></li>');
	});
</script>
<?php

// connect to Mongo
require_once('includes/include_mongo_connect.php');


$arrProjection = array(
	'_id'=>1,
	'size'=>1,
	'downloads'=>1
);

// collect possible query parameters
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
	$arrProjection = array(
		'_id'=>1,
		'size'=>1,
		'downloads'=>1,
		'title'=>1
	);
} elseif(isset($_GET["p"])) {
	$reqA = $_GET["user"];
	$strFilterTerm = 'handle';
	$arrCriteria = array('type' => 'paper');
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


$cursor = $summaries->find($arrCriteria,$arrProjection);

?>
<table class="data">
	<thead>
		<tr>
			<th scope="col"><?php echo $strGroup; ?></th>
			<?php if(!isset($reqA)) { ?><th scope="col">Articles</th><?php } ?>
			<th scope="col">Downloads</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach($cursor as $document) {
	if(isset($_GET["a"])) {
		$strEquivalent = 'View '.$document["_id"].' in DSpace@MIT';
		$strLink = '<a href="'.$document["_id"].'"><img src="/images/icon-link.png" alt="'.$strEquivalent.'" title="'.$strEquivalent.'"></a>';
		$strTitle = $document["title"];
	} else {
		$strEquivalent = 'View papers from '.$document["_id"].' in DSpace@MIT';
		$strLink = '<a href="http://dspace.mit.edu/advanced-search?num_search_field=1&results_per_page=10&scope=1721.1%2F49432&field1=department&query1='.urlencode($document["_id"]).'&rpp=10&sort_by=0&order=DESC"><img src="/images/icon-link.png" alt="'.$strEquivalent.'" title="'.$strEquivalent.'"></a>';
		$strTitle = $document["_id"];
	}
?>
	<tr>
		<?php if(isset($reqUser) && $reqUser == "admin") { ?>
		<td><a href="?user=admin&amp;<?php echo $nextType; ?>=<?php echo urlencode($document["_id"]); ?>"><?php echo $document["_id"]; ?></a></td>
		<?php } else { ?>
		<td><?php echo $strLink." ".$strTitle; ?></td>
		<?php } ?>
		<?php if(!isset($reqA)) { ?><td><?php echo $document["size"]; ?></td><?php } ?>
		<td><?php echo $document["downloads"]; ?></td>
	</tr>
<?php
}
?>
	</tbody>
</table>
<?php require_once('includes/include_mongo_disconnect.php'); ?>