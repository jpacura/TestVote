<?php
	session_start();
?>

<!--
	DO NOT CONVERT THIS SCRIPT TO ANGULARJS!
	THIS IS JUST A STUB FOR DESTROYING PHP SESSION VARIABLES
	IT WORKS, DO NOT BREAK IT!
-->

<!DOCTYPE html>
<html lang="en">
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
</html>
