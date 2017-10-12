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
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		$post_schoolname = $data->schoolName;
		$post_fullname = $data->adminName;
		$post_username = $data->email;
		$post_password = $data->password;
		$post_confirm = $data->confirm;
		
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
			$checkifexists = "SELECT Email FROM users WHERE Email = :uname";
			$query = $conn->prepare($checkifexists);
			$query->bindParam(':uname', $post_username);
			$query->execute();
			$numrows = $query->rowCount();
			
			if($numrows != 0)
			{
				// ADMINISTRATOR USER ALREADY EXISTS
				
				$getpasswd = "SELECT Password,Salt,UserID FROM users WHERE Email = :uname";
				$query = $conn->prepare($getpasswd);
				$query->bindParam(':uname', $post_username);
				$query->execute();
				$results = $query->fetch(PDO::FETCH_ASSOC);
				$dbpwd = $results['Password'];
				$dbsalt = $results['Salt'];
				$typedpwd = "$dbsalt$post_password";
				$typedpwd = hash('SHA512', $typedpwd);
				if($typedpwd == $dbpwd)
				{
					$register = "INSERT INTO schools (Name) VALUES (:name)";
					$query = $conn->prepare($register);
					$query->bindParam(':name', $post_schoolname);
					$query->execute();
					
					$getuserid = "SELECT UserID FROM users WHERE Email = :uname";
					$query = $conn->prepare($getuserid);
					$query->bindParam(':uname', $post_username);
					$query->execute();
					$mysql_userid = $query->fetchColumn();
					
					$getschoolid = "SELECT SchoolID FROM schools WHERE Name = :name";
					$query = $conn->prepare($getschoolid);
					$query->bindParam(':name', $post_schoolname);
					$query->execute();
					$mysql_schoolid = $query->fetchColumn();
					
					$makeadministrator = "INSERT INTO enrollment (UserID, SchoolID, Administrator) VALUES (:uid, :sid, 1)";
					$query = $conn->prepare($makeadministrator);
					$query->bindParam(':uid', $mysql_userid);
					$query->bindParam(':sid', $mysql_schoolid);
					$query->execute();
					
					echo "{ \"error\" : false, \"response\" : \"schoolcreated\"}";
				}
				else
				{
					echo "{ \"error\" : true , \"errorcode\" : 1 , \"response\" : \"loginincorrect\" }";
				}
			}
			else
			{
				// ADMINISTRATOR USER DOES NOT ALREADY EXIST
				if($post_password == $post_confirm)
				{
					$salt = random_bytes(2048);
					$salt = hash('SHA512', $salt);
					
					$typedpwd = "$salt$post_password";
					$typedpwd = hash('SHA512', $typedpwd);
					
					$register = "INSERT INTO users (Email, Password, Salt, Name) VALUES (:uname, :pw, :salt, :fname)";
					$query = $conn->prepare($register);
					$query->bindParam(':uname', $post_username);
					$query->bindParam(':pw', $typedpwd);
					$query->bindParam(':salt', $salt);
					$query->bindParam(':fname', $post_fullname);
					$query->execute();
					
					$register = "INSERT INTO schools (Name) VALUES (:name)";
					$query = $conn->prepare($register);
					$query->bindParam(':name', $post_schoolname);
					$query->execute();
					
					$getuserid = "SELECT UserID FROM users WHERE Email = :uname";
					$query = $conn->prepare($getuserid);
					$query->bindParam(':uname', $post_username);
					$query->execute();
					$mysql_userid = $query->fetchColumn();
					
					$getschoolid = "SELECT SchoolID FROM schools WHERE Name = :name";
					$query = $conn->prepare($getschoolid);
					$query->bindParam(':name', $post_schoolname);
					$query->execute();
					$mysql_schoolid = $query->fetchColumn();
					
					$makeadministrator = "INSERT INTO enrollment (UserID, SchoolID, Administrator) VALUES (:uid, :sid, 1)";
					$query = $conn->prepare($makeadministrator);
					$query->bindParam(':uid', $mysql_userid);
					$query->bindParam(':sid', $mysql_schoolid);
					$query->execute();
					
					echo "{ \"error\" : false, \"response\" : \"schoolcreated\"}";
				}
				else
				{
					echo "{ \"error\" : true , \"errorcode\" : 3 , \"response\" : \"passwordsdonotmatch\" }";
				}
			}
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
	else if ($operation == "LOGIN")
	{
		// THIS IS LEFTOVER FROM THE USERS LOGIN SCRIPT
		// A SCHOOL DOES NOT HAVE THE ABILITY TO LOG IN
		// I AM LEAVING THIS HERE IN CASE I NEED IT LATER
		
		//$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		//$post_username = $data->name;
		//$post_password = $data->password;
		
		//$checkifexists = "SELECT Email FROM users WHERE Email = :uname";
		//$query = $conn->prepare($checkifexists);
		//$query->bindParam(':uname', $post_username);
		//$query->execute();
		//$numrows = $query->rowCount();
		
		//if($numrows == 0)
		//{
			//echo "{ \"error\" : false , \"errorcode\" : 1 , \"response\" : \"loginincorrect\" }";
		//}
		//else
		//{
			//$getpasswd = "SELECT Password,Salt,UserID FROM users WHERE Email = :uname";
			//$query = $conn->prepare($getpasswd);
			//$query->bindParam(':uname', $post_username);
			//$query->execute();
			//$results = $query->fetch(PDO::FETCH_ASSOC);
			//$dbpwd = $results['Password'];
			//$dbsalt = $results['Salt'];
			//$typedpwd = "$dbsalt$post_password";
			//$typedpwd = hash('SHA512', $typedpwd);
			//if($typedpwd == $dbpwd)
			//{
				//$userid = $results['UserID'];
				//$rand = random_bytes(2048);
				//$token = "$post_username + $userid + $rand";
				//$token = hash('SHA512', $token);
				
				//$expireoldtoken = "UPDATE tokens SET Expired = 1 WHERE UserID = :uid AND Expired = 0";
				//$query = $conn->prepare($expireoldtoken);
				//$query->bindParam(':uid', $userid);
				//$query->execute();
				
				//$addtoken = "INSERT INTO tokens (UserID, Token, ExpirationTime) VALUE (:uid, :token, NOW() + INTERVAL 1 DAY)";
				//$query = $conn->prepare($addtoken);
				//$query->bindParam(':uid', $userid);
				//$query->bindParam(':token', $token);
				//$query->execute();
				
				//echo "{ \"error\" : false, \"response\" : \"passwordcorrect\" , \"token\" : \"$token\"}";
			//}
			//else
			//{
				//echo "{ \"error\" : true , \"errorcode\" : 1 , \"response\" : \"loginincorrect\" }";
			//}
		//}
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
