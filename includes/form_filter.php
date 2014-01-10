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
	$cursor = $collection->distinct('author',array('dlc'=>$reqD));
} elseif($reqA!="") {
	$strInstructions = "Show only these Papers:";
	$cursor = $collection->distinct('handle',array('author'=>$reqA));
} else {
	$strInstructions = "Show only these Departments, Labs or Centers:";
	$cursor = $collection->distinct('dlc');	
}
sort($cursor);
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
					<option value="<?php echo $document; ?>" <?php if(in_array($document,$reqFilter)) { echo 'selected="selected"'; } ?> ><?php echo $document; ?></option>
					<?php
				}
			?>
			</select>
		</label>
	<input type="submit">
	</form>
</div>
