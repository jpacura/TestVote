<?php
	session_start();

	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	
	$post_data = file_get_contents("php://input");
	$data = json_decode($post_data);
	
	$operation = $data->operation;
	
	$servername = "localhost";
	$username = "testvote";
	$password = "12345";
	$database = "TestVote";
	
	if($operation == "GETQUESTIONS")
	{
		$post_schoolusername = $data->schoolusername;
		$post_electionid = $data->electionid;
		$post_username = $_SESSION["username"];
		
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		// GET USERS NAME FROM MYSQL
		$getuserid = "SELECT Name,UserID FROM users WHERE Email = :uname";
		$query = $conn->prepare($getuserid);
		$query->bindParam(':uname', $post_username);
		$query->execute();
		$results = $query->fetch(PDO::FETCH_ASSOC);
		$mysql_userid = $results['UserID'];
		$mysql_username = $results['Name'];
		
		// GET SCHOOLS NAME FROM MYSQL
		$getschoolid = "SELECT Name FROM schools WHERE Username = :name";
		$query = $conn->prepare($getschoolid);
		$query->bindParam(':name', $post_schoolusername);
		$query->execute();
		$mysql_schoolname = $query->fetchColumn();
		
		if(isTokenValid())
		{
			// USER IS PROPERLY LOGGED IN
			if(isUserEnrolled($post_schoolusername))
			{
				// USER IS ENROLLED IN SCHOOL
				if(isElectionValid($post_schoolusername, $post_electionid))
				{
					// ELECTION IS OWNED BY SCHOOL AND ENABLED
					
					$checkifvoted = "SELECT VoteID FROM userVote WHERE UserID = :uid AND ElectionID = :eid";
					$query = $conn->prepare($checkifvoted);
					$query->bindParam(':uid', $mysql_userid);
					$query->bindParam(':eid', $post_electionid);
					$query->execute();
					$rowcount = $query->rowCount();
					
					if($rowcount == 0)
					{	
						// USER HAS NOT ALREADY VOTED
						
						// GET ELECTION NAME
						$getelectionname = "SELECT Name FROM elections WHERE ElectionID = :eid";
						$query = $conn->prepare($getelectionname);
						$query->bindParam(':eid', $post_electionid);
						$query->execute();
						$mysql_electionname = $query->fetchColumn();
						
						// GET ALL QUESTIONS IN THIS ELECTION
						$getelectionquestions = "SELECT Name,QuestionID FROM question WHERE ElectionID = :eid ORDER BY QuestionOrder";
						$query = $conn->prepare($getelectionquestions);
						$query->bindParam(':eid', $post_electionid);
						$query->execute();
						$rowcount = $query->rowCount();
						
						if($rowcount == 0)
						{
							// THERE ARE NO QUESTIONS IN THIS ELECTION
							echo "{ \"error\" : true , \"errorcode\" : 12 , \"response\" : \"noquestions\" }";
						}
						else
						{
							$mysql_questions = $query->fetchAll(PDO::FETCH_ASSOC);
							
							$outputtext = "{ \"error\" : false, \"username\" : \"$mysql_username\", \"schoolname\" : \"$mysql_schoolname\", \"electionname\" : \"$mysql_electionname\" , \"questions\" : { ";
							$validquestioncount = 0;
							
							$questionarray = "";
							foreach($mysql_questions as $q)
							{
								// GET ALL CHOICES FOR QUESTION
								$q_id = $q['QuestionID'];
								$getchoices = "SELECT OptionID,Value FROM questionOptions WHERE QuestionID = :qid ORDER BY OptionOrder";
								$query = $conn->prepare($getchoices);
								$query->bindParam(':qid', $q_id);
								$query->execute();
								$rowcount = $query->rowCount();
								
								if($rowcount == 0)
								{
									continue;
								}
								$validquestioncount = $validquestioncount + 1;
								
								$q_name = $q['Name'];
								$questionarray = "$questionarray\"$q_id\" : [ {\"$q_id\" : \"$q_name\"},";
								
								$mysql_answers = $query->fetchAll(PDO::FETCH_ASSOC);
								
								foreach($mysql_answers as $a)
								{
									$a_id = $a['OptionID'];
									$a_value = $a['Value'];
									$questionarray = "$questionarray{\"$a_id\" : \"$a_value\"},";
								}
								$questionarray = rtrim($questionarray, ',');
								
								$questionarray = "$questionarray ] ,";
							}
							$questionarray = rtrim($questionarray, ',');
							
							
							if($validquestioncount > 0)
							{
								$outputtext = "$outputtext$questionarray } }";
								echo $outputtext;
							}
							else
							{
								// NO VALID QUESTIONS
								echo "{ \"error\" : true , \"errorcode\" : 12 , \"response\" : \"noquestions\" }";
							}
						}
					}
					else
					{
						// USER ALREADY VOTED
						echo "{ \"error\" : true , \"errorcode\" : 13 , \"response\" : \"alreadyvoted\" }";
					}
				}
				else
				{
					// ELECTION IS INVALID
					echo "{ \"error\" : true , \"errorcode\" : 11 , \"response\" : \"electioninvalid\" }";
				}
			}
			else
			{
				// USER IS NOT PROPERLY ENROLLED
				echo "{ \"error\" : true , \"errorcode\" : 6 , \"response\" : \"notenrolled\" }";
			}
		}
		else
		{
			// USER IS NOT PROPERLY LOGGED IN
			echo "{ \"error\" : true , \"errorcode\" : 5 , \"response\" : \"notloggedin\" }";
		}
	}
	else if($operation == "VOTE")
	{
		$post_schoolusername = $data->schoolusername;
		$post_electionid = $data->electionid;
		$post_username = $_SESSION["username"];
		$post_formdata = $data->formdata;
		
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		// GET USERS ID FROM MYSQL
		$getuserid = "SELECT UserID FROM users WHERE Email = :uname";
		$query = $conn->prepare($getuserid);
		$query->bindParam(':uname', $post_username);
		$query->execute();
		$mysql_userid = $query->fetchColumn();
		
		
		if(isTokenValid())
		{
			if(isUserEnrolled($post_schoolusername))
			{
				if(isElectionValid($post_schoolusername, $post_electionid))
				{
					$checkifvoted = "SELECT VoteID FROM userVote WHERE UserID = :uid AND ElectionID = :eid";
					$query = $conn->prepare($checkifvoted);
					$query->bindParam(':uid', $mysql_userid);
					$query->bindParam(':eid', $post_electionid);
					$query->execute();
					$rowcount = $query->rowCount();
					
					if($rowcount == 0)
					{
						// MAKE SURE THE USER ACTUALLY SELECTED SOME VOTE OPTIONS
						
						// MARK USER AS VOTED
						$vote = "INSERT INTO userVote (UserID, ElectionID) VALUES (:uid, :eid)";
						$query = $conn->prepare($vote);
						$query->bindParam(':uid', $mysql_userid);
						$query->bindParam(':eid', $post_electionid);
						$query->execute();
						
						$vote_id = $conn->lastInsertId();
							
						// INSERT ALL VOTES INTO DATABASE
						$counter = 0;
						foreach($post_formdata as $k => $v)
						{
							$counter = $counter + 1;
							$addVote = "INSERT INTO vote (VoteID, UserID, QuestionID, OptionID) VALUES (:vid, :uid, :qid, :oid)";
							$query = $conn->prepare($addVote);
							$query->bindParam(':vid', $vote_id);
							$query->bindParam(':uid', $mysql_userid);
							$query->bindParam(':qid', $k);
							$query->bindParam(':oid', $v);
							$query->execute();
						}
						
						if($counter == 0)
						{
							// NO VOTES WERE RECORDED
							
							// REMOVE EMPTY VOTE RECORD
							$delvote = "DELETE FROM userVote WHERE VoteID = :vid";
							$query = $conn->prepare($delvote);
							$query->bindParam(':vid', $vote_id);
							$query->execute();
							
							echo "{ \"error\" : true , \"errorcode\" : 17 , \"response\" : \"optionmissing\" }";
						}
						else
						{
							echo "{ \"error\" : false , \"voteid\" : $vote_id }";
						}
					}
					else
					{
						// USER HAS ALREADY VOTED
						echo "{ \"error\" : true , \"errorcode\" : 13 , \"response\" : \"alreadyvoted\" }";
					}
					
				}
				else
				{
					// ELECTION IS NOT VALID
					echo "{ \"error\" : true , \"errorcode\" : 11 , \"response\" : \"electioninvalid\" }";
				}
			}
			else
			{
				// USER IS NOT ENROLLED
				echo "{ \"error\" : true , \"errorcode\" : 6 , \"response\" : \"notenrolled\" }";
			}
		}
		else
		{
			// TOKEN IS NOT VALID
			echo "{ \"error\" : true , \"errorcode\" : 5 , \"response\" : \"notloggedin\" }";
		}
	}
	else if($operation == "EXITPOLLLOAD")
	{
		$post_schoolusername = $data->schoolusername;
		$post_electionid = $data->electionid;
		$post_voteid = $data->voteid;
		
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		//ENSURE THAT A VOTE HAS JUST BEEN DONE, BUT NOT AN EXIT POLL
		$checkifvoted = "SELECT ExitPollCompleted FROM userVote WHERE VoteID = :vid";
		$query = $conn->prepare($checkifvoted);
		$query->bindParam(':vid', $post_voteid);
		$query->execute();
		$rowcount = $query->rowCount();
		
		if($rowcount == 0)
		{
			// NO ELECTION DATA HAS BEEN POSTED
			echo "{ \"error\" : true }";
		}
		else
		{
			// GET SCHOOLS ID FROM MYSQL
			$getschoolid = "SELECT SchoolID FROM schools WHERE Username = :name";
			$query = $conn->prepare($getschoolid);
			$query->bindParam(':name', $post_schoolusername);
			$query->execute();
			$mysql_schoolid = $query->fetchColumn();
			
			// CHECK IF SCHOOL OWNS ELECTION
			$checkelection = "SELECT * FROM elections WHERE ElectionID = :eid AND SchoolID = :sid";
			$query = $conn->prepare($checkelection);
			$query->bindParam(':eid', $post_electionid);
			$query->bindParam(':sid', $mysql_schoolid);
			$query->execute();
			$rowcount = $query->rowCount();
			
			if($rowcount == 0)
			{
				// SCHOOL DOES NOT OWN ELECTION OR ELECTION DOES NOT EXIST
				echo "{ \"error\" : true }";
			}
			else
			{
				// CHECK IF VOTE WAS CAST FOR THIS ELECTION
				$checkvote = "SELECT * FROM userVote WHERE ElectionID = :eid AND VoteID = :vid AND ExitPollCompleted = 0";
				$query = $conn->prepare($checkvote);
				$query->bindParam(':eid', $post_electionid);
				$query->bindParam(':vid', $post_voteid);
				$query->execute();
				$rowcount = $query->rowCount();
				
				if($rowcount == 0)
				{
					// VOTE DOES NOT BELONG TO SPECIFIED ELECTION OR DOES NOT EXIST
					echo "{ \"error\" : true }";
				}
				else
				{
					echo "{ \"error\" : false }";
				}
			}
		}
	}
	else if($operation == "EXITPOLLVOTE")
	{
		$post_schoolusername = $data->schoolusername;
		$post_electionid = $data->electionid;
		$post_voteid = $data->voteid;
		$post_survey = $data->survey;
		
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		// MARK EXIT VOTE AS COMPLETED
		$updatevote = "UPDATE userVote SET ExitPollCompleted = 1 WHERE VoteID = :vid";
		$query = $conn->prepare($updatevote);
		$query->bindParam(':vid', $post_voteid);
		$query->execute();
		
		// GET SCHOOLS ID FROM MYSQL
		$getschoolid = "SELECT SchoolID FROM schools WHERE Username = :name";
		$query = $conn->prepare($getschoolid);
		$query->bindParam(':name', $post_schoolusername);
		$query->execute();
		$mysql_schoolid = $query->fetchColumn();
		
		$fields = "(SchoolID";
		$values = "($mysql_schoolid";
		
		foreach($post_survey as $k => $v)
		{
			$a = htmlspecialchars($k);
			$b = htmlspecialchars($v);
			
			$fields = "$fields, $a";
			$values = "$values, \"$b\"";
		}
		
		$fields = "$fields)";
		$values = "$values)";
		
		$insert_query = "INSERT INTO exitPoll $fields VALUES $values";
		$query = $conn->prepare($insert_query);
		$query->execute();
		
		echo json_encode($insert_query);
	}
	else
	{
		// INVALID OPERATION
		echo "{ \"error\" : true , \"errorcode\" : 0 , \"response\" : \"invalidoperation\" }";
	}
	
	function isTokenValid()
	{
		global $servername, $database, $username, $password;
		
		if(isset($_SESSION["username"]) && isset($_SESSION["token"]))
		{
			$session_username = $_SESSION["username"];
			$session_token = $_SESSION["token"];
			
			$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
			
			$checkusername = "SELECT UserID FROM users WHERE Email = :uname";
			
			$query = $conn->prepare($checkusername);
			$query->bindParam(':uname', $session_username);
			$query->execute();
			$numrows = $query->rowCount();
			
			if($numrows == 0)
			{
				// THE USER SPECIFIED IN THE TOKEN DOES NOT EXIST
				session_unset();
				session_destroy();
				return 0;
			}
			else
			{
				// THE USER EXISTS, CHECK IF TOKEN IS EXISTS
				$uid = $query->fetchColumn();
				$gettoken = "SELECT Token FROM tokens WHERE UserID = :uid AND Expired = 0";
				$query = $conn->prepare($gettoken);
				$query->bindParam(':uid', $uid);
				$query->execute();
				$numrows = $query->rowCount();
				
				if($numrows == 0)
				{
					// THE USER HAS NO VALID TOKENS
					session_unset();
					session_destroy();
					return 0;
				}
				else
				{
					// THE USER HAS A VALID TOKEN
					// CHECK WHETHER IT MATCHES THE SENT TOKEN
					
					$mysql_token = $query->fetchColumn();
					
					if($session_token == $mysql_token)
					{
						return 1;
					}
					else
					{
						session_unset();
						session_destroy();
						return 0;
					}
				}
			}
			
		}
		else
		{
			return 0;
		}
	}
	
	function isUserEnrolled($schoolusername)
	{
		global $servername, $database, $username, $password;
		
		$post_schoolusername = $schoolusername;
		$post_username = $_SESSION["username"];
		
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		// GET USERS ID FROM MYSQL
		$getuserid = "SELECT UserID FROM users WHERE Email = :uname";
		$query = $conn->prepare($getuserid);
		$query->bindParam(':uname', $post_username);
		$query->execute();
		$mysql_userid = $query->fetchColumn();
		
		// GET SCHOOLS ID FROM MYSQL
		$getschoolid = "SELECT SchoolID FROM schools WHERE Username = :name";
		$query = $conn->prepare($getschoolid);
		$query->bindParam(':name', $post_schoolusername);
		$query->execute();
		$mysql_schoolid = $query->fetchColumn();
		
		// MAKE SURE USER IS ENROLLED
		$checkenrollment = "SELECT SchoolID FROM enrollment WHERE UserID = :uid AND SchoolID = :sid";
		$query = $conn->prepare($checkenrollment);
		$query->bindParam(':uid', $mysql_userid);
		$query->bindParam(':sid', $mysql_schoolid);
		$query->execute();
		$numrows = $query->rowCount();
		
		if($numrows > 0)
		{
			// USER IS ENROLLED
			return 1;
		}
		else
		{
			// USER IS NOT ENROLLED
			return 0;
		}
	}

	function isElectionValid($schoolusername, $electionid)
	{
		global $servername, $database, $username, $password;
		
		$post_schoolusername = $schoolusername;
		$post_electionid = $electionid;
		
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		// GET SCHOOLS ID FROM MYSQL
		$getschoolid = "SELECT SchoolID FROM schools WHERE Username = :name";
		$query = $conn->prepare($getschoolid);
		$query->bindParam(':name', $post_schoolusername);
		$query->execute();
		$mysql_schoolid = $query->fetchColumn();
		
		// GET SCHOOL ID AND ELECTION ENABLED FROM MYSQL
		$getelectionschool = "SELECT SchoolID,Enabled FROM elections WHERE ElectionID = :eid";
		$query = $conn->prepare($getelectionschool);
		$query->bindParam(':eid', $post_electionid);
		$query->execute();
		$results = $query->fetch(PDO::FETCH_ASSOC);
		$mysql_electionschoolid = $results['SchoolID'];
		$mysql_enabled = $results['Enabled'];
		
		if($mysql_schoolid == $mysql_electionschoolid)
		{
			// THE SCHOOL OWNS THIS ELECTION
			if($mysql_enabled == 1)
			{
				// ELECTION IS ENABLED
				return 1;
			}
		}
		return 0;
	}
