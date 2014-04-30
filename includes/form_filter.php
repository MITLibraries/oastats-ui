<div id="oafilter">
<form method="get">
<?php

if($_SERVER["SCRIPT_NAME"]=="/author.php") {
	$reqA = $_SESSION["user"];
}

// default projection
$arrProjection = array(
	'_id'=>1,
);
$arrSort = array(
	'_id'=>1,
);

// get filter values
if(isset($_GET["filter"])){
	$reqFilter = $_GET["filter"];
} else {
	$reqFilter = array();
}
// build the right filter query
if($reqD!="") {
	$strInstructions = "Authors:";
	$arrCriteria = array('type' => 'author');
} elseif($reqA!="") {
	include($_SERVER["DOCUMENT_ROOT"]."/includes/salt.php");
	$reqA = str_replace('@mit.edu','',$reqA);
	$strInstructions = "Papers:";
	$arrCriteria = array('type' => 'handle','parents.mitid'=>$salt.$_SESSION["hash"]);
	$arrProjection = array(
		'_id'=>1,
		'title'=>1
	);
	$arrSort = array(
		'title'=>1
	);
} else {
	$strInstructions = "Departments, Labs or Centers:";
	$arrCriteria = array('type' => 'dlc');
}

$cursor = $summaries->find($arrCriteria,$arrProjection)->sort($arrSort);

$arrOptions = array();
foreach($cursor as $document) {
	if($reqA!="") {
		$strTitle = $document["title"];
	} else {
		$strTitle = $document["_id"];
	}
	$arrOption = array(
		"_id" => $document["_id"],
		"title" => $strTitle
	);
	array_push($arrOptions,$arrOption);
}
usort($arrOptions, function($a,$b) {
	return strcasecmp($a["title"],$b["title"]);
});
?>
	<h2>Show Only</h2>
		<?php
		// store the current depth in the filter form
		if($reqD!="") {
			echo '<input type="hidden" name="d" value="'.$reqD.'">';
		} else if($reqA!="") {
			echo '<input type="hidden" name="a" value="'.$reqA.'">';			
		}
		?>
		<div class="filter">
			<label class="checkbox" for="all"><input type="checkbox" name="filter[]" id="all" value="all"<?php if(!$reqFilter) { echo 'checked="checked"'; }?>><span>All <?php echo $strInstructions; ?></span></label>
		<?php
			foreach($arrOptions as $document) {
		?>
			<label class="checkbox" for="<?php echo urlencode($document['_id']); ?>">
				<input type="checkbox" name="filter[]" id="<?php echo urlencode($document['_id']); ?>" value="<?php echo $document['_id']; ?>"<?php if(in_array($document['_id'],$reqFilter)) { echo 'checked="checked"'; } ?>>
				<span><?php echo $document['title']; ?></span>
			</label>
		<?php
			}
		?>
		</div>
	<input type="submit" value="Submit">
	</form>
	<h2>Export</h2>
	<!-- Export options are placed here by the active tab -->
	<ul id="exports">
		<li>Export</li>
	</ul>
</div>
