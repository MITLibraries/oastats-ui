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

// collect possible query parameters
if(isset($_GET["d"])) {
	$reqD = urldecode($_GET["d"]);
}
if(isset($_GET["a"])) {
	$reqA = urldecode($_GET["a"]);
}

/*
This needs to translate the MongoDB query into PHP syntax:
db.requests.aggregate( [ 
	{ $group : { _id : { dlc : "$dlc" , handle : "$handle" }, downloads : { $sum : 1 } } } , 
	{ $group : { _id : "$_id.dlc" , size : { $sum : 1 } , downloads : { $sum: "$downloads"} } } ,
	{ $sort : { _id : 1 } } 
] )

	array('$match'=>array('author'=>'http://example.com/author/1195')),
	array('$group'=>array('_id'=>'$handle','downloads'=>array('$sum'=>1)))

*/

if(isset($reqD)) {
	$strGroup = "Author";
	$charNext = "a";
	$cursor = $collection->aggregate(
		array('$match' => array('dlc'=>$reqD) ),
		array('$group' => array(
			'_id'=>array(
				'author'=>'$author',
				'handle'=>'$handle'
			),'downloads'=>array('$sum'=>1)
			)
		),
		array('$group' => array(
			'_id'=>'$_id.author', 
			'size'=>array('$sum'=>1),
			'downloads'=>array('$sum'=>'$downloads')
			)
		),
		array('$sort'=>array('_id'=>1))
	);

} elseif (isset($reqA)) {
	$strGroup = "Paper";
	$charNext = "";
	$cursor = $collection->aggregate(
		array('$match' => array('author'=>$reqA) ),
		array('$group' => array(
			'_id'=>'$handle',
			'downloads'=>array('$sum'=>1)
			)
		),
		array('$sort'=>array('_id'=>1))
	);

} else {
	$strGroup = "Group";
	$charNext = "d";
	$cursor = $collection->aggregate(
		array('$group' => array(
			'_id'=>array(
				'dlc'=>'$dlc',
				'handle'=>'$handle'
			),'downloads'=>array('$sum'=>1)
			)
		),
		array('$group' => array(
			'_id'=>'$_id.dlc', 
			'size'=>array('$sum'=>1),
			'downloads'=>array('$sum'=>'$downloads')
			)
		),
		array('$sort'=>array('_id'=>1))
	);

}

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