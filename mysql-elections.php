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
	
	if ($operation == "LISTELECTIONS")
	{
		// LIST ALL ELECTIONS THAT THE SELECTED SCHOOL HAS
		
		$post_schoolusername = $data->schoolusername;
		$post_username = $_SESSION["username"];
		
		// CHECK IF VALID USER IS LOGGED IN
		if(isTokenValid())
		{
			// THERE IS A VALID USER LOGGED IN
			// CHECK TO SEE IF THEY ARE ENROLLED IN THE SCHOOL
			if(isUserEnrolled($post_schoolusername))
			{
				// USER IS ENROLLED IN SCHOOL
				$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
				// GET USERS ID FROM MYSQL
				$getuserid = "SELECT UserID,Name FROM users WHERE Email = :uname";
				$query = $conn->prepare($getuserid);
				$query->bindParam(':uname', $post_username);
				$query->execute();
				$results = $query->fetch(PDO::FETCH_ASSOC);
				$mysql_userid = $results['UserID'];
				$mysql_username = $results['Name'];
				
				// GET SCHOOLS ID FROM MYSQL
				$getschoolid = "SELECT SchoolID,Name FROM schools WHERE Username = :name";
				$query = $conn->prepare($getschoolid);
				$query->bindParam(':name', $post_schoolusername);
				$query->execute();
				$results = $query->fetch(PDO::FETCH_ASSOC);
				$mysql_schoolid = $results['SchoolID'];
				$mysql_schoolname = $results['Name'];
				
				// CHECK IF REQUESTING USER IS ADMINISTRATOR
				$getadmin = "SELECT Administrator FROM enrollment WHERE UserID = :uid AND SchoolID = :sid";
				$query = $conn->prepare($getadmin);
				$query->bindParam(':uid', $mysql_userid);
				$query->bindParam(':sid', $mysql_schoolid);
				$query->execute();
				$mysql_administrator = $query->fetchColumn();
				
				if($mysql_administrator == 1)
				{
					// USER IS ADMINISTRATOR. THIS SHOULD NEVER HAPPEN IN THIS SCRIPT
					// THIS FUNCTIONALITY WAS REMOVED SO THAT ACCESS RIGHTS COULD BE SEPARATED
					// IT WAS PLACED INTO THE MYSQL-ADMIN FILE INSTEAD
					// RETURN AN ERROR
					
					echo "{ \"error\" : true , \"errorcode\" : 10, \"name\" : \"$mysql_username\", \"schoolname\" : \"$mysql_schoolname\" , \"response\" : \"noelections\" }";
				}
				else
				{
					// NOT AN ADMINISTRATOR. ONLY RETURN ENABLED ELECTIONS
					// ELECTIONS MUST NOT HAVE ALREADY BEEN VOTED ON, AND MUST CONTAIN AT LEAST ONE QUESTION
					$getelections = "SELECT ElectionID, Name FROM elections WHERE SchoolID = :sid AND Enabled = 1 AND NOT EXISTS (SELECT UserID FROM userVote WHERE UserID = :uid AND userVote.ElectionID = elections.ElectionID) AND EXISTS (SELECT QuestionID FROM question WHERE question.ElectionID = elections.ElectionID)";
					$query = $conn->prepare($getelections);
					$query->bindParam(':sid', $mysql_schoolid);
					$query->bindParam(':uid', $mysql_userid);
					$query->execute();
					$numrows = $query->rowCount();
					
					if($numrows == 0)
					{
						// NO ELECTIONS
						echo "{ \"error\" : true , \"errorcode\" : 10, \"name\" : \"$mysql_username\", \"schoolname\" : \"$mysql_schoolname\" , \"response\" : \"noelections\" }";
					}
					else
					{
						$tabledata = $query->fetchAll(PDO::FETCH_ASSOC);
						$tabledata = json_encode($tabledata);
						echo "{ \"error\" : false , \"name\" : \"$mysql_username\", \"schoolname\" : \"$mysql_schoolname\" , \"elections\" : $tabledata }";
					}
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
