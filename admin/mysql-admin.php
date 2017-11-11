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
	
	if ($operation == "LISTUSERS" || $operation == "LISTADMINISTRATORS")
	{
		// LIST ALL USERS THAT THE SELECTED SCHOOL HAS
		
		$post_schoolusername = $data->schoolusername;
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
				
				// GET USERS NAME FROM MYSQL
				$getuserid = "SELECT Name FROM users WHERE Email = :uname";
				$query = $conn->prepare($getuserid);
				$query->bindParam(':uname', $post_username);
				$query->execute();
				$mysql_username = $query->fetchColumn();
				
				// GET SCHOOLS ID FROM MYSQL
				$getschoolid = "SELECT SchoolID,Name FROM schools WHERE Username = :name";
				$query = $conn->prepare($getschoolid);
				$query->bindParam(':name', $post_schoolusername);
				$query->execute();
				$results = $query->fetch(PDO::FETCH_ASSOC);
				$mysql_schoolid = $results['SchoolID'];
				$mysql_schoolname = $results['Name'];
				
				// GET REQUESTED USERS FROM MYSQL
				$getusers = "";
				
				if($operation == "LISTUSERS")
				{
					$getusers = "SELECT users.UserID,users.Name,users.Email,users.StudentID FROM users LEFT JOIN enrollment ON users.UserID = enrollment.UserID WHERE enrollment.SchoolID = :sid AND enrollment.Administrator = 0";
				}
				else if($operation == "LISTADMINISTRATORS")
				{
					$getusers = "SELECT users.UserID,users.Name,users.Email FROM users LEFT JOIN enrollment ON users.UserID = enrollment.UserID WHERE enrollment.SchoolID = :sid AND enrollment.Administrator = 1";
				}
				
				$query = $conn->prepare($getusers);
				$query->bindParam(':sid', $mysql_schoolid);
				$query->execute();
				$users = $query->fetchAll(PDO::FETCH_ASSOC);
				$numrows = $query->rowCount();
				
				if($numrows == 0)
				{
					// NO USERS IN SCHOOL
					echo "{ \"error\" : true , \"errorcode\" : 14 , \"name\" : \"$mysql_username\", \"schoolname\" : \"$mysql_schoolname\" , \"response\" : \"nousers\" }";
				}
				else
				{
					// RETURN LIST OF REQUESTED USERS
					$users = json_encode($users);
					echo "{ \"error\" : false , \"name\" : \"$mysql_username\", \"schoolname\" : \"$mysql_schoolname\" , \"userlist\" : $users }";
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
	else if ($operation == "LISTELECTIONS")
	{
		// LIST ALL ELECTIONS THAT THE SELECTED SCHOOL HAS
		
		$post_schoolusername = $data->schoolusername;
		$post_username = $_SESSION["username"];
		
		// CHECK IF VALID USER IS LOGGED IN
		if(isTokenValid())
		{
			// THERE IS A VALID USER LOGGED IN
			// CHECK TO SEE IF THEY ARE ENROLLED IN THE SCHOOL
			if(isUserAdmin($post_schoolusername))
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
					// USER IS ADMINISTRATOR. RETURN ALL ELECTIONS
					$getelections = "SELECT ElectionID, Name, Enabled FROM elections WHERE SchoolID = :sid";
					$query = $conn->prepare($getelections);
					$query->bindParam(':sid', $mysql_schoolid);
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
				else
				{
					// NOT AN ADMINISTRATOR. THIS SHOULD NEVER HAPPEN IN THIS SCRIPT
					// RETURN AN ERROR
					
					echo "{ \"error\" : true , \"errorcode\" : 10, \"name\" : \"$mysql_username\", \"schoolname\" : \"$mysql_schoolname\" , \"response\" : \"noelections\" }";
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
	else if ($operation == "REMOVEUSER")
	{
		// LEAVE A SCHOOL THAT YOU ARE ENROLLED IN
		
		$post_schoolusername = $data->schoolusername;
		$post_usertoremove = $data->usertoremove;
		$post_username = $_SESSION["username"];
		
		if(isTokenValid() && isUserAdmin($post_schoolusername))
		{
			// LOGGED IN
			
			$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
			
			// GET USERS ID FROM MYSQL
			$getuserid = "SELECT UserID FROM users WHERE Email = :uname";
			$query = $conn->prepare($getuserid);
			$query->bindParam(':uname', $post_usertoremove);
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
	else if ($operation == "TOGGLEELECTION")
	{
		// LIST ALL ELECTIONS THAT THE SELECTED SCHOOL HAS
		
		$post_schoolusername = $data->schoolusername;
		$post_electionid = $data->electionid;
		$post_username = $_SESSION["username"];
		
		// CHECK IF VALID USER IS LOGGED IN
		if(isTokenValid())
		{
			// THERE IS A VALID USER LOGGED IN
			// CHECK TO SEE IF THEY ARE ENROLLED IN THE SCHOOL
			if(isUserAdmin($post_schoolusername))
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
				
				// CHECK IF ELECTION ACTUALLY BELONGS TO REQUESTING SCHOOL
				$checkschool = "SELECT Enabled FROM elections WHERE ElectionID = :eid AND SchoolID = :sid";
				$query = $conn->prepare($checkschool);
				$query->bindParam(':eid', $post_electionid);
				$query->bindParam(':sid', $mysql_schoolid);
				$query->execute();
				$numrows = $query->rowCount();
				
				if($numrows > 0)
				{
					// THIS SCHOOL OWNS THIS ELECTION
					
					// GET CURRENT ELECTION STATUS
					$election_enabled = $query->fetchColumn();
					
					// TO TOGGLE THE ENABLED BIT, WE CAN DO SOME NICE BINARY MAGIC
					$election_toggled = 1 - $election_enabled;
					
					$toggleelection = "UPDATE elections SET Enabled = :en WHERE ElectionID = :eid";
					$query = $conn->prepare($toggleelection);
					$query->bindParam(':en', $election_toggled);
					$query->bindParam(':eid', $post_electionid);
					$query->execute();
					
					echo "{ \"error\" : false , \"response\" : \"toggledelection\" }";
				}
				else
				{
					// ELECTION DOES NOT BELONG TO SCHOOL
					// THIS SHOULD NEVER HAPPEN LEGITIMATELY
					// LOG USER OUT
					echo "{ \"error\" : true , \"errorcode\" : 5 , \"response\" : \"notloggedin\" }";
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
	else if ($operation == "ENROLL")
	{
		// ENROLL IN AN EXISTING SCHOOL
		
		if(isTokenValid())
		{
			// LOGGED IN
			$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
			
			$post_newadminemail = $data->email;
			$post_schoolusername = $data->schoolid;
			$post_username = $_SESSION["username"];
			
			if(!isUserAdmin($post_schoolusername))
			{
				echo "{ \"error\" : true , \"errorcode\" : 5 , \"response\" : \"notloggedin\" }";
			}
			else
			{
				// GET USERS ID FROM MYSQL AND MAKE SURE USER EXISTS
				$getuserid = "SELECT UserID FROM users WHERE Email = :uname";
				$query = $conn->prepare($getuserid);
				$query->bindParam(':uname', $post_newadminemail);
				$query->execute();
				$mysql_userid = $query->fetchColumn();
				$numrows = $query->rowCount();
				
				if($numrows == 0)
				{
					// USER DOES NOT EXIST
					echo "{ \"error\" : true , \"errorcode\" : 15 , \"response\" : \"userdoesnotexist\" }";
				}
				else
				{
					// GET SCHOOLS ID FROM MYSQL
					$getschoolid = "SELECT SchoolID FROM schools WHERE Username = :name";
					$query = $conn->prepare($getschoolid);
					$query->bindParam(':name', $post_schoolusername);
					$query->execute();
					$mysql_schoolid = $query->fetchColumn();
					
					// DELETE ALL OTHER ENROLLMENTS TO THIS SCHOOL IF EXISTS
					$checkenrollment = "DELETE FROM enrollment WHERE UserID = :uid AND SchoolID = :sid";
					$query = $conn->prepare($checkenrollment);
					$query->bindParam(':uid', $mysql_userid);
					$query->bindParam(':sid', $mysql_schoolid);
					$query->execute();
					
					// ENROLL USER INTO SCHOOL
					$makeadministrator = "INSERT INTO enrollment (UserID, SchoolID, Administrator) VALUES (:uid, :sid, 1)";
					$query = $conn->prepare($makeadministrator);
					$query->bindParam(':uid', $mysql_userid);
					$query->bindParam(':sid', $mysql_schoolid);
					$query->execute();
					echo "{ \"error\" : false, \"response\" : \"enrolled\"}";
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
