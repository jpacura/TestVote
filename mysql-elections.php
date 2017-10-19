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
			if(isUserEnrolled())
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
				
				// CHECK IF REQUESTING USER IS ADMINISTRATOR
				$getadmin = "SELECT Administrator FROM enrollment WHERE UserID = :uid AND SchoolID = :sid";
				$query = $conn->prepare($getadmin);
				$query->bindParam(':uid', $mysql_userid);
				$query->bindParam(':sid', $mysql_schoolid);
				$query->execute();
				$mysql_administrator = $query->fetchColumn();
				
				if($mysql_administrator == 1)
				{
					// USER IS ADMINISTRATOR. RETURN ALL ELECTIONS
					$getelections = "SELECT ElectionID, Name, Enabled FROM elections WHERE SchoolID = :sid";
					$query = $conn->prepare($getelections);
					$query->bindParam(':sid', $mysql_schoolid);
					$query->execute();
					$numrows = $query->rowCount();
					
					if($numrows == 0)
					{
						// NO ELECTIONS
						echo "{ \"error\" : true , \"errorcode\" : 10 , \"response\" : \"noelections\" }";
					}
					else
					{
						$tabledata = $query->fetchAll(PDO::FETCH_ASSOC);
						$tabledata = json_encode($tabledata);
						echo "{ \"error\" : false , \"name\" : \"$mysql_username\" , \"elections\" : $tabledata }";
					}
				}
				else
				{
					// NOT AN ADMINISTRATOR. ONLY RETURN ENABLED ELECTIONS
					$getelections = "SELECT ElectionID, Name FROM elections WHERE SchoolID = :sid AND Enabled = 1";
					$query = $conn->prepare($getelections);
					$query->bindParam(':sid', $mysql_schoolid);
					$query->execute();
					$numrows = $query->rowCount();
					
					if($numrows == 0)
					{
						// NO ELECTIONS
						echo "{ \"error\" : true , \"errorcode\" : 10 , \"response\" : \"noelections\" }";
					}
					else
					{
						$tabledata = $query->fetchAll(PDO::FETCH_ASSOC);
						$tabledata = json_encode($tabledata);
						echo "{ \"error\" : false , \"name\" : \"$mysql_username\" , \"elections\" : $tabledata }";
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
	
	function isUserEnrolled()
	{
		global $servername, $database, $username, $password;
		
		$post_schoolusername = $data->schoolUsername;
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
		$checkenrollment = "Select SchoolID FROM enrollment WHERE UserID = :uid AND SchoolID = :sid";
		$query = $conn->prepare($checkenrollment);
		$query->bindParam(':uid', $mysql_userid);
		$query->bindParam(':sid', $mysql_schoolid);
		$query->execute();
		$numrows = $query->rowCount();
		
		if($numrows != 0)
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
