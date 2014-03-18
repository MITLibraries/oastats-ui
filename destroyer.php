<?php

	include_once($_SERVER["DOCUMENT_ROOT"]."/includes/initialize.php");

	destroySession();

	$strBaseURL = buildReturnURL();

	header('Location: '.$strBaseURL);

?>