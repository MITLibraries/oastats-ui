<?php
	// Check for server variable, and update session variable if needed
	if(isset($_SERVER["REMOTE_USER"])) {
		$_SESSION["user"] = $_SERVER["REMOTE_USER"];
	} 
?>
				<div id="navigation">
					<ul>
						<li><a href="index.php">Home</a></li>
						<li><a href="faq.php"><abbr title="Frequently Asked Questions">FAQ</abbr></a></li>
						<li><a href="public.php">Public Stats</a></li>
<?php
	if(isset($_SESSION["user"])) {
?>
						<li><a href="author.php">My Stats</a></li>
						<li class="login"><a href="<?php echo buildLogoutURL(); ?>">Logout</a></li>
<?php		
	} else {
?>
						<li class="login"><a href="secure/?return=author.php">Login</a></li>
<?php		
	}
?>						
					</ul>
				</div>
