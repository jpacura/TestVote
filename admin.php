<html>

	<head>
		<title>DEBUG: ADMIN PAGE STUB</title>
	</head>

	<body>
		<p>THIS IS A TEMPORARY STUB TO PASS POST DATA TO THE ADMIN PAGES.</p>
		<p>THIS WILL BE A FULL PAGE LATER</p>
		
		<?php
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				// POST DATA EXISTS
				if(isset($_POST['school']))
				{
					// SCHOOL NAME EXISTS IN POST DATA
					
					$sname = $_POST['school'];
					echo "<p>School Name Posted: $sname</p>";
					echo "<br><br><br>";
					echo '<p><a href="#" onclick="users()">Click Here</a> to go to admin/users.php</p>';
					echo '<p><a href="#" onclick="admins()">Click Here</a> to go to admin/administrators.php</p>';
					
					echo '<form method="post" id="go" action="">';
					echo "	<input type=\"hidden\" id=\"schoolnamepost\" name=\"school\" value=\"$sname\">";
					echo '</form>';
				}
				else
				{
					// NO SCHOOL NAME POSTED
					echo "<h2>WARNING: NO SCHOOL DATA POSTED!</h2>";
				}
			}
			else
			{
				// NO POST DATA
				echo "<h2>WARNING: NO POST DATA RECEIVED!</h2>";
			}
		?>
		
		<script>
			function users()
			{
				document.getElementById("go").action = "admin/users.php";
				document.getElementById("go").submit();
			}
			
			function admins()
			{
				document.getElementById("go").action = "admin/administrators.php";
				document.getElementById("go").submit();
			}
		</script>
		
	</body>

</html>
