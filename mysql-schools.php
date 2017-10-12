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
	
	if ($operation == "REGISTER")
	{
		// REGISTER A NEW SCHOOL
		
		if(isTokenValid())
		{
			// LOGGED IN
			$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
			$post_schoolname = $data->schoolName;
			$post_username = $_SESSION["username"];
			
			// MAKE SURE NEW SCHOOL DOES NOT ALREADY EXIST
			$checkifschoolexists = "SELECT Name FROM schools WHERE Name = :sname";
			$query = $conn->prepare($checkifschoolexists);
			$query->bindParam(':sname', $post_schoolname);
			$query->execute();
			$numrows = $query->rowCount();
			
			if($numrows != 0)
			{
				echo "{ \"error\" : true , \"errorcode\" : 4 , \"response\" : \"schoolalreadyexists\" }";
			}
			else
			{
				// ADD SCHOOL TO MYSQL
				$register = "INSERT INTO schools (Name) VALUES (:name)";
				$query = $conn->prepare($register);
				$query->bindParam(':name', $post_schoolname);
				$query->execute();
				
				// GET USERS ID FROM MYSQL
				$getuserid = "SELECT UserID FROM users WHERE Email = :uname";
				$query = $conn->prepare($getuserid);
				$query->bindParam(':uname', $post_username);
				$query->execute();
				$mysql_userid = $query->fetchColumn();
				
				// GET SCHOOLS ID FROM MYSQL
				$getschoolid = "SELECT SchoolID FROM schools WHERE Name = :name";
				$query = $conn->prepare($getschoolid);
				$query->bindParam(':name', $post_schoolname);
				$query->execute();
				$mysql_schoolid = $query->fetchColumn();
				
				// ENROLL USER INTO SCHOOL AS ADMINISTRATOR
				$makeadministrator = "INSERT INTO enrollment (UserID, SchoolID, Administrator) VALUES (:uid, :sid, 1)";
				$query = $conn->prepare($makeadministrator);
				$query->bindParam(':uid', $mysql_userid);
				$query->bindParam(':sid', $mysql_schoolid);
				$query->execute();
				
				echo "{ \"error\" : false, \"response\" : \"schoolcreated\"}";
			}
		}
		else
		{
			// NOT LOGGED IN
			echo "{ \"error\" : true , \"errorcode\" : 5 , \"response\" : \"notloggedin\" }";
		}
	}
	else if ($operation == "LISTSCHOOLS")
	{
		// LIST ALL SCHOOLS THAT A USER IS ENROLLED IN
		
		if(isTokenValid())
		{
			// USER IS PROPERLY LOGGED IN, RETURN TABLE
			$session_username = $_SESSION["username"];
			
			$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
			
			$getuserid = "SELECT UserID FROM users WHERE Email = :uname";
			$query = $conn->prepare($getuserid);
			$query->bindParam(':uname', $session_username);
			$query->execute();
			$userid = $query->fetchColumn();
			
			$getschools = "SELECT schools.Name, schools.SchoolID, enrollment.Administrator FROM users INNER JOIN enrollment ON users.UserID = enrollment.UserID INNER JOIN schools ON enrollment.SchoolID = schools.SchoolID WHERE users.UserID = :uid";
			$query = $conn->prepare($getschools);
			$query->bindParam(':uid', $userid);
			$query->execute();
			$numrows = $query->rowCount();
			
			if($numrows == 0)
			{
				// NOT ENROLLED IN ANY SCHOOLS
				echo "{ \"error\" : true , \"errorcode\" : 6 , \"response\" : \"notenrolled\" }";
			}
			else
			{
				$tabledata = $query->fetchAll(PDO::FETCH_ASSOC);
				$tabledata = json_encode($tabledata);
				echo "{ \"error\" : false , \"schools\" : $tabledata }";
			}
		}
		else
		{
			// NOT LOGGED IN
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
