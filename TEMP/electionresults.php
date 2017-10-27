<html>

	<head>
		<title>DEBUG: ELECTION RESULTS</title>
	</head>

	<body>
		<h2>ELECTION RESULTS VIEWER</h2>
		<p>This is a DEBUGGING script. It will be replaced and removed</p>
		
		<form method="POST">
			<input type="text" name="electionid" placeholder="Enter Election ID Here!">
			<input type="submit" value="Get Election Results">
		</form>
		
		<div>
			<?php
				if ($_SERVER['REQUEST_METHOD'] == 'POST')
				{
					$servername = "localhost";
					$username = "testvote";
					$password = "12345";
					$database = "TestVote";
					
					$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
					
					// CHECK IF ELECTION ID EXISTS
					$getelectionid = "SELECT SchoolID,Name,Enabled FROM elections WHERE ElectionID = :eid";
					$query = $conn->prepare($getelectionid);
					$query->bindParam(':eid', $_POST['electionid']);
					$query->execute();
					$numrows = $query->rowCount();
					
					if($numrows == 0)
					{
						// NO ELECTION FOUND
						echo "ERROR: NO ELECTION FOUND!";
					}
					else
					{
						$results = $query->fetch(PDO::FETCH_ASSOC);
						$school_id = $results['SchoolID'];
						$election_name = $results['Name'];
						$election_enabled = $results['Enabled'];
						
						// GET QUESTION NAMES FROM MYSQL
						$getquestionnames = "SELECT Name FROM question WHERE ElectionID = :eid";
						$query = $conn->prepare($getquestionnames);
						$query->bindParam(':eid', $_POST['electionid']);
						$query->execute();
						$numrows = $query->rowCount();
						
						if($numrows == 0)
						{
							echo "ERROR: ELECTION HAS NO QUESTIONS!";
						}
						else
						{
							$question_names = $query->fetchAll(PDO::FETCH_ASSOC);
							
							// GET ALL USER VOTES FOR THIS ELECTION
							$getuservotes = "SELECT VoteID,UserID FROM userVote WHERE ElectionID = :eid";
							$query = $conn->prepare($getuservotes);
							$query->bindParam(':eid', $_POST['electionid']);
							$query->execute();
							$numrows = $query->rowCount();
							
							if($numrows == 0)
							{
								echo "This election has no results!";
							}
							else
							{
								$results = $query->fetchAll(PDO::FETCH_ASSOC);
								
								echo "<table rules=all frame=border>";
								echo "<td>School ID: $school_id</td>";
								echo "<td>Election Name: $election_name</td>";
								echo "<td>Election Enabled: $election_enabled</td>";
								echo "</table><br>";
								
								echo "<table rules=all frame=border>";
								echo "<tr><th>Voter Name</th>";
								
								foreach($question_names as $qn)
								{
									$qn_name = $qn['Name'];
									echo "<th>$qn_name</th>";
								}
								
								echo "</tr>";
								
								foreach($results as $userVote)
								{
									$voteID = $userVote['VoteID'];
									$userID = $userVote['UserID'];
									
									// GET USERS NAME FROM MYSQL
									$getuserid = "SELECT Name FROM users WHERE UserID = :uid";
									$query = $conn->prepare($getuserid);
									$query->bindParam(':uid', $userID);
									$query->execute();
									$mysql_username = $query->fetchColumn();
									
									// GET USERS VOTES FROM MYSQL
									$getvotes = "SELECT questionOptions.Value FROM vote LEFT JOIN questionOptions ON questionOptions.OptionID = vote.OptionID WHERE VoteID = :vid";
									$query = $conn->prepare($getvotes);
									$query->bindParam(':vid', $voteID);
									$query->execute();
									$results = $query->fetchAll(PDO::FETCH_ASSOC);
									
									echo "<tr><td>$mysql_username</td>";
									
									foreach($results as $v)
									{
										$a = $v['Value'];
										echo "<td>$a</td>";
									}
									
									echo "</tr>";
									
									
									
								}
								
								echo "</table>";
							}
						}
					}
				}
			?>
		</div>
	</body>

</html>
