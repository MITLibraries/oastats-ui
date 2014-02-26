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
	$strInstructions = "Show only these Authors:";
	$arrCriteria = array('type' => 'author');
} elseif($reqA!="") {
	$reqA = str_replace('@mit.edu','',$reqA);
	$strInstructions = "Show only these Papers:";
	$arrCriteria = array('type' => 'paper','parents'=>$reqA);
} else {
	$strInstructions = "Show only these Departments, Labs or Centers:";
	$arrCriteria = array('type' => 'dlc');
}

$arrProjection = array(
	'_id'=>1,
);

$cursor = $summaries->find($arrCriteria,$arrProjection);

?>
	<form method="get">
		<?php
		// store the current depth in the filter form
		if($reqD!="") {
			echo '<input type="hidden" name="d" value="'.$reqD.'">';
		} else if($reqA!="") {
			echo '<input type="hidden" name="a" value="'.$reqA.'">';			
		}
		?>
		<label for="filter">
			<?php echo $strInstructions; ?><br>
			<select id="filter" name="filter[]" multiple="true" class="listbuilder">
			<?php
				foreach($cursor as $document) {
					?>
					<option value="<?php echo $document['_id']; ?>" <?php if(in_array($document['_id'],$reqFilter)) { echo 'selected="selected"'; } ?> ><?php echo $document['_id']; ?></option>
					<?php
				}
			?>
			</select>
		</label>
	<input type="submit" name="Filter" value="Filter">
	<div class="clear"></div>
	</form>
</div>
