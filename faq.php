<?php
	$strPageTitle = "Frequently Asked Questions";

	require_once('includes/initialize.php'); 

	require_once('includes/header.php'); 

?>
<div id="content">

<h2>How are the usage statistics generated?</h2>
<p>We use access logs from DSpace@MIT to count article downloads from the MIT Open Access Articles collection.  Downloads are attributed to each individual and each organization associated with the downloaded article.  If there are multiple MIT authors for an article, each author will see the article title and associated downloads through My Stats.  If there are multiple MIT organizations for an article, the downloads are attributed to each organization, with cumulative article and download data available through Public Stats.</p>
<hr>

<h2>Is any filtering done to remove automated download requests?</h2>
<p>Yes, using standard methodologies we remove automated downloads such as those from crawlers, robots and spiders.</p>
<hr>

<h2>How often are the statistics updated?</h2>
<p>Currently, we expect the service to be updated on a monthly basis.  Our goal is to move to more frequent updating as the service matures.</p>
<hr>

<h2>What time period is covered by the OA Stats service?</h2>
<p>OA Stats reports article downloads from the first month of comprehensive usage data (August, 2010) to the present.</p>
<hr>

<h2>Who can view My Stats?</h2>
<p>My Stats is available for current MIT faculty, staff and students who have authored articles in the MIT Open Access Articles collection and who log in via MIT’s Touchstone.  Download data for individual papers is available only to the MIT authors of those papers.  Public Stats are available to all users.</p>
<hr>

<h2>How can I update or correct the list of articles that are linked to My Stats?</h2>
<p>While best efforts are made to accurately associate authors with their articles, some mismatches or omissions are possible.  Please e-mail <a href="mailto:oastats@mit.edu">oastats@mit.edu</a> to notify our staff of any corrections needed.</p>
<hr>

<h2>As an MIT researcher, how can I contribute my research articles to the MIT Open Access Articles collection?</h2>
<p>Submissions may be made here <a href="http://dspace.mit.edu/handle/1721.1/49433/submit">http://dspace.mit.edu/handle/1721.1/49433/submit</a> (requires authentication via MIT’s Touchstone). </p>
<hr>

<h2>What does ‘cumulative article downloads’ mean on the Timeline view?</h2>
<p>The Timeline visualizes the total article downloads to date, starting from August 2010, and is interactive.  More detailed information, including daily download statistics, is available in the CSV export from the left-hand menu.</p>
<hr>

<h2>Why do the article counts differ from those in DSpace@MIT for the same MIT department, lab or center?</h2>
<p>DSpace@MIT has live updating in place as new articles are deposited, whereas the statistics interface is updated on a monthly basis.  DSpace@MIT counts are often higher as a result of the most recently deposited articles.</p>
<hr>

<h2>Can I see similar statistics for other collections in DSpace@MIT?</h2>
<p>While we do hope to expand the service to other collections in the future, currently only the MIT Open Access Articles collection is available through this service.</p>
<hr>

<h2>How can I provide feedback?</h2>
<p>Please send an e-mail to <a href="mailto:oastats@mit.edu">oastats@mit.edu</a>.  We would love to hear your feedback on the current service or how you feel it could be improved.</p>

</div>
<?php
	require_once('includes/footer.php'); 
?>