<?php
	$strPageTitle = "Welcome";

	require_once('includes/initialize.php'); 

	require_once('includes/header.php'); 

?>
<div id="content">
<p>This website contains statistics about downloads from the <a href="http://dspace.mit.edu/handle/1721.1/49433">MIT Open Access Articles Collection</a> in DSpace@MIT. This collection contains MIT-authored articles deposited as part of the <a href="http://libraries.mit.edu/scholarly/mit-open-access/open-access-at-mit/mit-open-access-policy/">MIT Faculty Open Access Policy</a>.</p>
<p>Statistics are available in two categories:</p>
<p class="option">
	<a href="public.php">
		<span class="button">For the public</span>
		<span>Summary information about the Open Access Articles Collection as a whole is available to all visitors. The public can also see subtotals for individual departments, labs, or centers. No login is required.</span>
	</a>
</p>
<p class="option">
	<a href="/secure/?return=author.php">
		<span class="button">For MIT authors</span>
		<span>Information on individual papers is available only to the paper's author(s). To see details for your papers, log in using MIT's Touchstone.</span>
	</a>
</p>
<p>Curious about the data captured and displayed through this site? <a href="faq.php">See the FAQ.</a></p>
</div>
<?php
	require_once('includes/footer.php'); 
?>