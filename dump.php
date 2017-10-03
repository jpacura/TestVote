<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  <!-- REQUIRED HTML HEADER -->

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"> <!-- REQUIRED HTML HEADER -->

	<head> <!-- HEAD IS WHERE THE TITLE AND STYLE SHEETS GO -->
		<title>Database Dump</title> <!-- TITLE THAT SHOWS IN BROWSER -->
	</head> <!-- THIS IS LIKE A CLOSING BRACKET FROM JAVA. THE "/" MEANS THAT IT CLOSES THE HEAD ON LINE 6 -->

	<body> <!-- BODY IS WHERE ALL OF THE PAGE CONTENT GOES -->
		<h1>Database Dump:</h1> <!-- h1 MAKES THE TEXT BETWEEN IT BOLD AND BIGGER -->
		
		<br /><br /> <!-- br MAKES NEW LINES. THIS MAKES 2 NEW LINES IN THE PAGE -->
		
		<div>Note: This page doesnt do shit yet because we haven't set up MySQL, but we will</div>
		
		<!-- THE LINE BELOW STARTS A NEW PHP SCRIPT. THIS IS A DIFFERENT LANGUAGE THAN HTML -->
		<?php # BEGINNING OF PHP SCRIPT
			#$servername = "localhost"; # IP ADDRESS OF MYSQL SERVER. USE "localhost" IF RUNNING ON SAME COMPUTER
			#$username = "testlogin1";  # MYSQL USERNAME FOR DATABASE
			#$password = "12345";       # MYSQL PASSWORD FOR DATABASE
			#$database = "testlogin1";  # NAME OF MYSQL TABLE WE USE FOR THIS PROJECT
			#$conn = new mysqli($servername, $username, $password, $database); # OPEN A NEW MYSQL CONNECTION (NEEDS SERVERNAME, USERNAME, PASSWORD, AND DATABASE NAME
			
			#$getdata = "SELECT username,password FROM testlogin1"; # SIMPLE MYSQL SELECT STATEMENT. SELECTS USERNAMES AND PASSWORDS FROM TABLE "testlogin1"
			#$query = $conn->prepare($getdata); # PREPARE THE ABOVE STATEMENT TO SEND TO MYSQL
			#$query->execute(); # RUN THE MYSQL QUERY
			#$query->bind_result($uname, $pwd); # BIND USERNAME AND PASSWORD TO TWO VARIABLES, "uname" AND "pwd"
			
			#echo "<table rules=all frame=border><tr><th>Username</th><th>Password</th></tr>"; # ECHO IS USED TO PRINT HTML TO THE WEBPAGE. THIS LINE STARTS A NEW HTML TABLE
			#while($query->fetch()) # WHILE LOOP TO SEE IF MYSQL HAS MORE DATA
			#{
			#	$uname = htmlspecialchars($uname); # CLEAN UP USERNAME TO PREVENT HACKING
			#	$pwd = htmlspecialchars($pwd);     # CLEAN UP PASSWORD TO PREVENT HACKING
			#	echo "<tr><td>$uname</td><td>$pwd</td></tr>"; # ADD A NEW LINE TO THE TABLE WITH A USERNAME AND PASSWORD
			#} # EXIT WHILE LOOP
			#echo "</table>"; # CLOSE TABLE PROPERLY
		?> <!-- END OF PHP SCRIPT, BACK TO HTML -->
		
	</body> <!-- CLOSE THE BODY ON LINE 10 -->
</html> <!-- CLOSE THE HTML ON LINE 4 -->
