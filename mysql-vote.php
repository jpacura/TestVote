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
		$getuserid = "SELECT Name FROM users WHERE Email = :uname";
		$query = $conn->prepare($getuserid);
		$query->bindParam(':uname', $post_username);
		$query->execute();
		$mysql_username = $query->fetchColumn();
		
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
						$validquestioncount = 0; // MAKE SURE THIS IS CHANGED TO 0 LATER
						
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