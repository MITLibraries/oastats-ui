<link rel="stylesheet" href="styles/data.css">
<link rel="stylesheet" href="styles/jquery.dataTables.css">
<script src="scripts/jquery.dataTables.min.js" charset="utf-8"></script>
<script>
	$(document).ready(function() {
		$( "table.data" ).dataTable({
			"bFilter": false,
			"bLengthChange": false,
			"bPaginate": false,
			"bInfo": false,
		});
	});
</script>
<?php

// connect to Mongo
require_once('includes/include_mongo_connect.php');

$strFilterTerm = 'dlc';
// collect possible query parameters
if(isset($_GET["d"])) {
	$reqD = $_GET["d"];
	$strFilterTerm = 'author';
}
if(isset($_GET["a"])) {
	$reqA = $_GET["a"];
	$strFilterTerm = 'handle';
}
if(isset($_GET["filter"])) {
	$reqFilter = $_GET["filter"];
}

$arrQuery = array();
// Apply filter values
if(isset($reqFilter)){
	$arrMatch = array();
	$arrFilter = array();
	// iterate over reqFilter, padding out values
	foreach($reqFilter as $term) {
		array_push($arrFilter,array($strFilterTerm=>$term));
	}
	$arrMatch = array('$match' => array( '$or' => $arrFilter));
	// add arrMatch to built query
	array_push($arrQuery,$arrMatch);
}

if(isset($reqD)) {
	$strGroup = "Author";
	$charNext = "a";
	array_push($arrQuery,
		array('$match' => array('dlc'=>$reqD) )
	);
	array_push($arrQuery,
		array('$group' => array(
			'_id'=>array(
				'author'=>'$author',
				'handle'=>'$handle'
			),'downloads'=>array('$sum'=>1)
			)
		)
	);
	array_push($arrQuery,
		array('$group' => array(
			'_id'=>'$_id.author', 
			'size'=>array('$sum'=>1),
			'downloads'=>array('$sum'=>'$downloads')
			)
		)
	);
	array_push($arrQuery,
		array('$sort'=>array('_id'=>1))
	);

} elseif (isset($reqA)) {
	$strGroup = "Paper";
	$charNext = "";
	array_push($arrQuery,
		array('$match' => array('author'=>$reqA) )
	);
	array_push($arrQuery,
		array('$group' => array(
			'_id'=>'$handle',
			'downloads'=>array('$sum'=>1)
			)
		)
	);
	array_push($arrQuery,
		array('$sort'=>array('_id'=>1))
	);

} else {
	$strGroup = "Group";
	$charNext = "d";
	array_push($arrQuery,
		array('$group' => array(
			'_id'=>array(
				'dlc'=>'$dlc',
				'handle'=>'$handle'
			),'downloads'=>array('$sum'=>1)
			)
		)
	);
	array_push($arrQuery,
		array('$group' => array(
			'_id'=>'$_id.dlc', 
			'size'=>array('$sum'=>1),
			'downloads'=>array('$sum'=>'$downloads')
			)
		)
	);
	array_push($arrQuery,
		array('$sort'=>array('_id'=>1))
	);

}

$cursor = $collection->aggregate($arrQuery);

?>
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
foreach($cursor["result"] as $document) {
?>
	<tr>
		<td><a href="?<?php echo $charNext; ?>=<?php echo urlencode($document["_id"]); ?>"><?php echo $document["_id"]; ?></a></td>
		<?php if(!isset($reqA)) { ?><td><?php echo $document["size"]; ?></td><?php } ?>
		<td><?php echo $document["downloads"]; ?></td>
	</tr>
<?php
}
?>
</table>
<?php require_once('includes/include_mongo_disconnect.php'); ?>