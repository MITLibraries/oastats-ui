<?php

	include_once("/includes/initialize.php");

	destroySession();

	$strBaseURL = buildReturnURL();

	header('Location: '.$strBaseURL);

?>