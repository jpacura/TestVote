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
		
			$post_schoolname = $data->schoolFullName;
			$post_schoolusername = $data->schoolUsername;
			$post_username = $_SESSION["username"];
			
			// MAKE SURE NEW SCHOOL DOES NOT ALREADY EXIST
			$checkifschoolexists = "SELECT Username FROM schools WHERE Username = :uname";
			$query = $conn->prepare($checkifschoolexists);
			$query->bindParam(':uname', $post_schoolusername);
			$query->execute();
			$numrows = $query->rowCount();
			
			if($numrows != 0)
			{
				echo "{ \"error\" : true , \"errorcode\" : 4 , \"response\" : \"schoolalreadyexists\" }";
			}
			else
			{
				// ADD SCHOOL TO MYSQL
				$register = "INSERT INTO schools (Name, Username) VALUES (:name, :uname)";
				$query = $conn->prepare($register);
				$query->bindParam(':name', $post_schoolname);
				$query->bindParam(':uname', $post_schoolusername);
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
			
			$getuserid = "SELECT UserID,Name FROM users WHERE Email = :uname";
			$query = $conn->prepare($getuserid);
			$query->bindParam(':uname', $session_username);
			$query->execute();
			$results = $query->fetch(PDO::FETCH_ASSOC);
			$userid = $results['UserID'];
			$mysql_username = $results['Name'];
			
			$getschools = "SELECT schools.Name, schools.Username, enrollment.Administrator FROM users INNER JOIN enrollment ON users.UserID = enrollment.UserID INNER JOIN schools ON enrollment.SchoolID = schools.SchoolID WHERE users.UserID = :uid";
			$query = $conn->prepare($getschools);
			$query->bindParam(':uid', $userid);
			$query->execute();
			$numrows = $query->rowCount();
			
			if($numrows == 0)
			{
				// NOT ENROLLED IN ANY SCHOOLS
				echo "{ \"error\" : true , \"errorcode\" : 6 , \"response\" : \"notenrolled\" , \"name\" : \"$mysql_username\" }";
			}
			else
			{
				$tabledata = $query->fetchAll(PDO::FETCH_ASSOC);
				$tabledata = json_encode($tabledata);
				echo "{ \"error\" : false , \"name\" : \"$mysql_username\" , \"schools\" : $tabledata }";
			}
		}
		else
		{
			// NOT LOGGED IN
			echo "{ \"error\" : true , \"errorcode\" : 5 , \"response\" : \"notloggedin\" }";
		}
		
	}
	else if ($operation == "LEAVESCHOOL")
	{
		// LEAVE A SCHOOL THAT YOU ARE ENROLLED IN
		
		if(isTokenValid())
		{
			// LOGGED IN
			
			$post_username = $_SESSION["username"];
			$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
			
			// GET USERS ID FROM MYSQL
			$getuserid = "SELECT UserID FROM users WHERE Email = :uname";
			$query = $conn->prepare($getuserid);
			$query->bindParam(':uname', $post_username);
			$query->execute();
			$mysql_userid = $query->fetchColumn();
			
			// GET SCHOOL USERNAME FROM POST DATA
			$post_schoolusername = $data->schoolusername;
			$getschoolid = "SELECT SchoolID FROM schools WHERE Username = :uname";
			$query = $conn->prepare($getschoolid);
			$query->bindParam(':uname', $post_schoolusername);
			$query->execute();
			$post_schoolID = $query->fetchColumn();
			
			
			// GET ENROLLMENT FROM MYSQL
			$getenrollment = "SELECT Administrator, ID FROM enrollment WHERE SchoolID = :sid AND UserID = :uid";
			$query = $conn->prepare($getenrollment);
			$query->bindParam(':sid', $post_schoolID);
			$query->bindParam(':uid', $mysql_userid);
			$query->execute();
			$results = $query->fetch(PDO::FETCH_ASSOC);
			$is_administrator = $results['Administrator'];
			$enrollment_id = $results['ID'];
			
			// CHECK IF ADMINISTRATOR
			if($is_administrator)
			{
				// USER IS ADMINISTRATOR
				
				// CHECK IF THERE ARE MORE ADMINISTRATORS
				$countadministrators = "SELECT ID FROM enrollment WHERE SchoolID = :sid AND Administrator = 1";
				$query = $conn->prepare($countadministrators);
				$query->bindParam(':sid', $post_schoolID);
				$query->execute();
				$numrows = $query->rowCount();
				
				if($numrows < 2)
				{
					// USER IS ONLY ADMINISTRATOR
					echo "{ \"error\" : true , \"errorcode\" : 7 , \"response\" : \"lastadministrator\" }";
				}
				else
				{
					// THERE IS ANOTHER ADMINISTRATOR
					// DELETE ENROLLMENT
					$deleteenrollment = "DELETE FROM enrollment WHERE ID = :eid";
					$query = $conn->prepare($deleteenrollment);
					$query->bindParam(':eid', $enrollment_id);
					$query->execute();
					
					echo "{ \"error\" : false , \"response\" : \"leftschool\" }";
				}
			}
			else
			{
				// USER IS NOT ADMINISTRATOR
				// DELETE ENROLLMENT
				$deleteenrollment = "DELETE FROM enrollment WHERE ID = :eid";
				$query = $conn->prepare($deleteenrollment);
				$query->bindParam(':eid', $enrollment_id);
				$query->execute();
				
				echo "{ \"error\" : false , \"response\" : \"leftschool\" }";
			}
		}
		else
		{
			// NOT LOGGED IN
			echo "{ \"error\" : true , \"errorcode\" : 5 , \"response\" : \"notloggedin\" }";
		}
	}
	else if ($operation == "ENROLL")
	{
		// ENROLL IN AN EXISTING SCHOOL
		
		if(isTokenValid())
		{
			// LOGGED IN
			$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
			$post_schoolusername = $data->username;
			$post_username = $_SESSION["username"];
			
			// MAKE SURE SCHOOL ALREADY EXISTS
			$checkifschoolexists = "SELECT Username FROM schools WHERE Username = :uname";
			$query = $conn->prepare($checkifschoolexists);
			$query->bindParam(':uname', $post_schoolusername);
			$query->execute();
			$numrows = $query->rowCount();
			
			if($numrows == 0)
			{
				echo "{ \"error\" : true , \"errorcode\" : 8 , \"response\" : \"schooldoesnotexist\" }";
			}
			else
			{
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
				
				// MAKE SURE USER IS NOT ALREADY ENROLLED
				$checkenrollment = "Select SchoolID FROM enrollment WHERE UserID = :uid AND SchoolID = :sid";
				$query = $conn->prepare($checkenrollment);
				$query->bindParam(':uid', $mysql_userid);
				$query->bindParam(':sid', $mysql_schoolid);
				$query->execute();
				$numrows = $query->rowCount();
				
				if($numrows == 0)
				{
					// ENROLL USER INTO SCHOOL
					$makeadministrator = "INSERT INTO enrollment (UserID, SchoolID, Administrator) VALUES (:uid, :sid, 0)";
					$query = $conn->prepare($makeadministrator);
					$query->bindParam(':uid', $mysql_userid);
					$query->bindParam(':sid', $mysql_schoolid);
					$query->execute();
					echo "{ \"error\" : false, \"response\" : \"enrolled\"}";
				}
				else
				{
					// ALREADY ENROLLED
					echo "{ \"error\" : true , \"errorcode\" : 9 , \"response\" : \"alreadyenrolled\" }";
				}
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
