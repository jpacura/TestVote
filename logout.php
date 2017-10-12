<?php
	session_start();
?>

<!--
	DO NOT CONVERT THIS SCRIPT TO ANGULARJS!
	THIS IS JUST A STUB FOR DESTROYING PHP SESSION VARIABLES
	IT WORKS, DO NOT BREAK IT!
-->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  <!-- REQUIRED HTML HEADER -->
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"> <!-- REQUIRED HTML HEADER -->
	<head>
		<title>Logging Out...</title>
	</head>
	
	<body>
		<p>Logging out...</p>
		<?php	
			session_unset();
			session_destroy();
		?>
		<p><a href="login.php">Click here</a> if you are not redirected to login in 5 seconds</p>
		
		<script>
			document.ready(window.setTimeout(location.href = "login.php",2000));
		</script>
	</body>
