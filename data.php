<link rel="stylesheet" href="styles/data.css">
<link rel="stylesheet" href="styles/jquery.dataTables.css">
<script src="scripts/jquery.dataTables.min.js" charset="utf-8"></script>
<script>
	$(document).ready(function() {
		$( "table.data" ).dataTable({
			"bFilter": false,
			"bLengthChange": false,
			"bInfo": false,
			"sPaginationType": "full_numbers"
		});
	});
</script>
<?php

// connect to Mongo
require_once('includes/include_mongo_connect.php');

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
	$strFilterTerm = '_id';
	$arrCriteria = array('type' => 'paper');
	$nextType = "";
	$strGroup = "Paper";
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

$arrProjection = array(
	'_id'=>1,
	'size'=>1,
	'downloads'=>1
);

$cursor = $summaries->find($arrCriteria,$arrProjection);

?>
<div class="export">
	<a>CSV</a>
	<a>PDF</a>
</div>

<table class="data">
	<thead>
		<tr>
			<th scope="col"><?php echo $strGroup; ?></th>
			<?php if(!isset($reqA)) { ?><th scope="col">Items</th><?php } ?>
			<th scope="col">Downloads</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach($cursor as $document) {
?>
	<tr>
		<?php if(isset($reqUser) && $reqUser == "admin") { ?>
		<td><a href="?user=admin&amp;<?php echo $nextType; ?>=<?php echo urlencode($document["_id"]); ?>"><?php echo $document["_id"]; ?></a></td>
		<?php } else { ?>
		<td><?php echo $document["_id"]; ?></td>
		<?php } ?>
		<?php if(!isset($reqA)) { ?><td><?php echo number_format($document["size"]); ?></td><?php } ?>
		<td><?php echo number_format($document["downloads"]); ?></td>
	</tr>
<?php
}
?>
</table>

<?php require_once('includes/include_mongo_disconnect.php'); ?>