<div id="oafilter">
<?php

$cursor = $collection->aggregate(
	array('$group' => array( '_id'=>'$dlc')),
	array('$sort'=>array('_id'=>1))
);

?>
	<form method="get">
		<label for="filter">
			Show only these departments:<br>
			<select id="filter" name="filter" multiple="true" class="listbuilder">
			<?php
				foreach($cursor["result"] as $document) {
					echo "<option value=\"".$document["_id"]."\">".$document["_id"]."</option>";
				}
			?>
			</select>
		</label>
	<input type="submit">
	</form>
</div>
