				<div id="usertab">
<?php
	// if the user has logged in, the REMOTE_USER server variable will be set
	if(isset($_SERVER["REMOTE_USER"])) {
		$reqUser = $_SERVER["REMOTE_USER"];
		echo '<div class="user">'.$reqUser.'</div>';
	}
	if(isset($reqUser) && $reqUser != "admin") {
		echo '<a href="/Shibboleth.sso/Logout?return=/oastats/two.php">Logout</a><a href="/oastats/two.php">Public Stats</a><a href="/oastats/secure/author.php">My Papers</a>';
	} elseif(isset($reqUser) && $reqUser = "admin") {
		echo '<a href="/oastats/two.php">Logout</a>';
	} else {
		echo '<a href="/oastats/secure/author.php">Login</a>';
	}
?>					
				</div>
