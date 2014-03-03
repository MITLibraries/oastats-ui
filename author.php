<?php
	require_once('includes/header.php'); 

	if(isset($_SESSION["user"])) {
		$reqA = $_SESSION["user"];
		
		require_once('includes/form_filter.php'); 
?>
<div id="tabs" class="tabs">
	<ul>
		<li><a href="data.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Data</a></li>
		<li><a href="time.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Timeline</a></li>
		<li><a href="map.php?a=<?php echo $reqA; ?>&amp;<?php echo $_SERVER["QUERY_STRING"]; ?>">Map</a></li>
	</ul>
</div>
<?php
	} else {
?>
<p>Please <a href="secure/?return=author.php">login</a> to see your author statistics.</p>
<?php
	}
	require_once('includes/footer.php'); 
?>