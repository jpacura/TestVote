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
	
	if($operation == "CREATEELECTION")
	{
		// MAKE SURE USER IS ADMINISTRATOR AND PROPERLY SIGNED IN
		
		$post_schoolusername = $data->schoolusername;
		$post_electionname = $data->electionname;
		$post_electiondata = $data->electiondata;
		$post_username = $_SESSION["username"];
		
		// CHECK IF VALID USER IS LOGGED IN
		if(isTokenValid())
		{
			// THERE IS A VALID USER LOGGED IN
			// CHECK TO SEE IF THEY ARE AN ADMINISTRATOR OF THE SCHOOL
			if(isUserAdmin($post_schoolusername))
			{
				// USER IS ENROLLED IN SCHOOL
				
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
				
				if(empty($post_electionname))
				{
					// ELECTION HAS NO NAME
					echo "{ \"error\" : true , \"errorcode\" : 16 , \"response\" : \"namemissing\" }";
				}
				else
				{
					$createelection = "INSERT INTO elections (SchoolID, Name) VALUES (:sid, :ename)";
					$query = $conn->prepare($createelection);
					$query->bindParam(':sid', $mysql_schoolid);
					$query->bindParam(':ename', $post_electionname);
					$query->execute();
					$electionid = $conn->lastInsertId();
					
					$questionnumber = 1;
					
					foreach ($post_electiondata as $question)
					{
						foreach ($question as $questionname => $options)
						{
							if(count($options) == 0)
							{
								// QUESTION HAS NO OPTIONS
								// THIS SHOULD NOT BE ALLOWED BY THE FRONTEND
								// WE WILL VALIDATE ANYWAY IN CASE OF FORM TAMPERING
								
								// DELETE UNFINISHED ELECTION FROM DATABASE
								// THE FOREIGN KEY CONSTRAINTS WILL ALSO DELETE THE BAD QUESTIONS
								$deleteelection = "DELETE FROM elections WHERE ElectionID = :eid";
								$query = $conn->prepare($deleteelection);
								$query->bindParam(':eid', $electionid);
								$query->execute();
								echo "{ \"error\" : true , \"errorcode\" : 17 , \"response\" : \"optionmissing\" }";
							}
							else
							{
								$createquestion = "INSERT INTO question (ElectionID, Name, QuestionOrder) VALUES (:eid, :qname, :order)";
								$query = $conn->prepare($createquestion);
								$query->bindParam(':eid', $electionid);
								$query->bindParam(':qname', $questionname);
								$query->bindParam(':order', $questionnumber);
								$query->execute();
								$questionid = $conn->lastInsertId();
								
								$optionnumber = 1;
								
								foreach ($options as $value)
								{
									$createoption = "INSERT INTO questionOptions (QuestionID, Value, OptionOrder) VALUES (:qid, :val, :order)";
									$query = $conn->prepare($createoption);
									$query->bindParam(':qid', $questionid);
									$query->bindParam(':val', $value);
									$query->bindParam(':order', $optionnumber);
									$query->execute();
									
									$optionnumber = $optionnumber + 1;
								}
								
								$questionnumber = $questionnumber + 1;
							}
						}
					}
					
					echo "{ \"error\" : false , \"response\" : \"electioncreated\" }";
				}
			}
			else
			{
				// USER IS NOT ENROLLED IN SCHOOL
				echo "{ \"error\" : true , \"errorcode\" : 6 , \"response\" : \"notenrolled\" }";
			}
		}
		else
		{
			// THERE IS NOBODY LOGGED IN
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
	
	function isUserAdmin($schoolusername)
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
		
		// MAKE SURE USER IS ENROLLED AND IS ADMINISTRATOR
		$checkenrollment = "SELECT SchoolID FROM enrollment WHERE UserID = :uid AND SchoolID = :sid AND Administrator = 1";
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
