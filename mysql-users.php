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
		// REGISTER A NEW USER
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		$post_fullname = $data->fname;
		$post_username = $data->email;
		$post_password = $data->password;
		$post_confirm = $data->confirm;
		
		$checkifexists = "SELECT Email FROM users WHERE Email = :uname";
		$query = $conn->prepare($checkifexists);
		$query->bindParam(':uname', $post_username);
		$query->execute();
		$numrows = $query->rowCount();
		
		if($numrows != 0)
		{
			echo "{ \"error\" : true , \"errorcode\" : 2 , \"response\" : \"useralreadyexists\" }";
		}
		else
		{
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
				echo "{ \"error\" : false , \"response\" : \"usercreated\" }";
			}
			else
			{
				echo "{ \"error\" : true , \"errorcode\" : 3 , \"response\" : \"passwordsdonotmatch\" }";
			}
		}
	}
	else if ($operation == "LOGIN")
	{
		// LOGIN AS AN EXISTING USER
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		
		$post_username = $data->name;
		$post_password = $data->password;
		
		$checkifexists = "SELECT Email FROM users WHERE Email = :uname";
		$query = $conn->prepare($checkifexists);
		$query->bindParam(':uname', $post_username);
		$query->execute();
		$numrows = $query->rowCount();
		
		if($numrows == 0)
		{
			echo "{ \"error\" : true , \"errorcode\" : 1 , \"response\" : \"loginincorrect\" }";
		}
		else
		{
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
				$userid = $results['UserID'];
				$rand = random_bytes(2048);
				$token = hash('SHA512', $rand);
				
				$expireoldtoken = "UPDATE tokens SET Expired = 1 WHERE UserID = :uid AND Expired = 0";
				$query = $conn->prepare($expireoldtoken);
				$query->bindParam(':uid', $userid);
				$query->execute();
				
				$addtoken = "INSERT INTO tokens (UserID, Token, ExpirationTime) VALUE (:uid, :token, NOW() + INTERVAL 1 DAY)";
				$query = $conn->prepare($addtoken);
				$query->bindParam(':uid', $userid);
				$query->bindParam(':token', $token);
				$query->execute();
				
				$_SESSION["username"] = $post_username;
				$_SESSION["token"] = $token;
				
				echo "{ \"error\" : false, \"response\" : \"passwordcorrect\" , \"username\" : \"$post_username\" , \"token\" : \"$token\"}";
			}
			else
			{
				echo "{ \"error\" : true , \"errorcode\" : 1 , \"response\" : \"loginincorrect\" }";
			}
		}
	}
	else if ($operation == "LOGOUT")
	{
		// LOG OUT
		session_unset();
		session_destroy();
	}
	else if ($operation == "TOKEN")
	{
		// USER ALREADY LOGGED IN, VERIFY TOKEN
		if(isTokenValid())
		{
			// LOGGED IN
			echo "{ \"error\" : false , \"response\" : \"loggedin\" }";
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
