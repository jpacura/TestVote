<?php
	session_start();
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$servername = "localhost";
		$username = "testvote";
		$password = "12345";
		$database = "TestVote";
		
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		$schoolid = $_POST['schoolid'];
		$electionid = $_POST['electionid'];
		$post_username = $_SESSION['username'];
		
		// GET USERS ID FROM MYSQL
		$getuserid = "SELECT UserID FROM users WHERE Email = :uname";
		$query = $conn->prepare($getuserid);
		$query->bindParam(':uname', $post_username);
		$query->execute();
		$mysql_userid = $query->fetchColumn();
		
		// MARK USER AS VOTED
		$vote = "INSERT INTO userVote (UserID, ElectionID) VALUES (:uid, :eid)";
		$query = $conn->prepare($vote);
		$query->bindParam(':uid', $mysql_userid);
		$query->bindParam(':eid', $electionid);
		$query->execute();
		
		$vote_id = $conn->lastInsertId();
		
		// INSERT ALL VOTES INTO DATABASE
		foreach($_POST as $k => $v)
		{
			if($k == "schoolid" || $k == "electionid") { continue; }
			
			$addVote = "INSERT INTO vote (VoteID, UserID, QuestionID, OptionID) VALUES (:vid, :uid, :qid, :oid)";
			$query = $conn->prepare($addVote);
			$query->bindParam(':vid', $vote_id);
			$query->bindParam(':uid', $mysql_userid);
			$query->bindParam(':qid', $k);
			$query->bindParam(':oid', $v);
			$query->execute();
		}
		
		echo "Vote Recorded!<br><br>";
		echo "NOTE: THIS IS A TESTING SCRIPT TO TEST THE BACKEND. IT WILL BE REPLACED BY A PROPER SCRIPT<br><br>";
		echo "<a href=\"../schools.php\">Click Here</a> to return to the schools page";
	}
	else
	{
		echo "ERROR: NO POST DATA DETECTED!";
	}
	
