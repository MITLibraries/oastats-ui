<?php
	$strPageTitle = "Welcome to MIT's Open Access Article Statistics";

	require_once('includes/initialize.php'); 

	require_once('includes/header.php'); 

?>
<div id="content">
<p>View statistics on downloaded articles in the <a href="http://dspace.mit.edu/handle/1721.1/49433">MIT Open Access Articles Collection</a> in DSpace@MIT. This collection contains MIT-authored articles deposited as part of the <a href="http://libraries.mit.edu/scholarly/mit-open-access/open-access-at-mit/mit-open-access-policy/">MIT Faculty Open Access Policy</a>.</p>
<p class="option"><a href="public.php" class="button">Public statistics</a><span>View aggregated data for all articles, or specify particular departments, labs, and centers. View this data via tables, timelines, or on a world map.</span></p>
<p class="option"><a href="author.php" class="button">Statistics for MIT authors</a><span>Statistics for individual papers are available only to the paper's author(s). To see statistics for your papers, <a href="author.php">log in using MIT's Touchstone</a>.</span></p>
<p>Curious about the data captured and displayed through this site? <a href="faq.php">See the FAQ.</a></p>
</div>
<?php
	require_once('includes/footer.php'); 
?>