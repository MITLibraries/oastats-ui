<div id="oafilter">
<?php
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
	$reqA = str_replace('@mit.edu','',$reqA);
	$strInstructions = "Papers:";
	$arrCriteria = array('type' => 'paper','parents'=>$reqA);
} else {
	$strInstructions = "Departments, Labs or Centers:";
	$arrCriteria = array('type' => 'dlc');
}

$arrProjection = array(
	'_id'=>1,
);

$arrSort = array(
	'_id'=>1,
);
$cursor = $summaries->find($arrCriteria,$arrProjection)->sort($arrSort);

?>
	<h2>Filter</h2>
	<form method="get">
		<?php
		// store the current depth in the filter form
		if($reqD!="") {
			echo '<input type="hidden" name="d" value="'.$reqD.'">';
		} else if($reqA!="") {
			echo '<input type="hidden" name="a" value="'.$reqA.'">';			
		}
		?>
		<div class="filter">
			<label class="checkbox" for="all"><input type="checkbox" name="filter[]" id="all" value="all">All <?php echo $strInstructions; ?></label>
		<?php
			foreach($cursor as $document) {
		?>
			<label class="checkbox" for="<?php echo urlencode($document['_id']); ?>"><input type="checkbox" name="filter[]" id="<?php echo urlencode($document['_id']); ?>" value="<?php echo $document['_id']; ?>"<?php if(in_array($document['_id'],$reqFilter)) { echo 'checked="checked"'; } ?>><span><?php echo $document['_id']; ?></span></label>
		<?php
			}
		?>
		</div>
	<input type="submit">
	</form>
	<h2>Export</h2>
	<!-- Export options are placed here by the active tab -->
	<ul id="exports">
		<li>Export</li>
	</ul>
</div>
