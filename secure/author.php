<html lang="en">
	<head>
		<?php require_once('../includes/include_mongo_connect.php'); ?>
		<title>Open Access Statistics - Mockup Two</title>
		<link rel="stylesheet" href="../styles/reset.css">
		<link rel="stylesheet" href="../styles/two.css">
		<link rel="stylesheet" href="../styles/listbuilder.css">
		</script>		
	</head>
	<body>
		<div id="page">
			<div class="page-inner">
				<div id="masthead">
					<h1>Open Access Statistics - Mockup Two</h1>
				</div>	
				<?php require_once('../includes/include_login.php'); ?>
				<?php
				$strBreadcrumb = "Author Stats";
				?>
				<div id="breadcrumb">
					<p>
					<span class="semantic">You are here: </span>
					<span class="level home"><a href="/oastats/">Home</a></span>
					<span class="semantic">in subsection </span>
					<span class="level"><a href="/oastats/two.php">Mockup Two</a></span>
						<?php if($strBreadcrumb!="") { ?>
							<span class="semantic">in subsection </span>
							<span class="level"><?php echo $strBreadcrumb; ?></span>
						<?php }; ?>
					</p>
				</div>
			</div>
		</div>
		<?php require_once('../includes/include_mongo_disconnect.php'); ?>
	</body>
</html>			