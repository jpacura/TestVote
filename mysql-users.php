<?php
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
		$post_studentid = $data->studentid;
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
				$typedpwd = $post_password;
				$typedpwd = hash('SHA512', $typedpwd);
				
				$register = "INSERT INTO users (Email, Password, Name, StudentID) VALUES (:uname, :pw, :fname, :sid)";
				$query = $conn->prepare($register);
				$query->bindParam(':uname', $post_username);
				$query->bindParam(':pw', $typedpwd);
				$query->bindParam(':fname', $post_fullname);
				$query->bindParam(':sid', $post_studentid);
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
			echo "{ \"error\" : false , \"errorcode\" : 1 , \"response\" : \"loginincorrect\" }";
		}
		else
		{
			$getpasswd = "SELECT Password,UserID FROM users WHERE Email = :uname";
			$query = $conn->prepare($getpasswd);
			$query->bindParam(':uname', $post_username);
			$query->execute();
			$results = $query->fetch(PDO::FETCH_ASSOC);
			$dbpwd = $results['Password'];
			$typedpwd = $post_password;
			$typedpwd = hash('SHA512', $typedpwd);
			if($typedpwd == $dbpwd)
			{
				$userid = $results['UserID'];
				$rand = random_bytes(2048);
				$token = "$post_username + $userid + $rand";
				$token = hash('SHA512', $token);
				
				$expireoldtoken = "UPDATE tokens SET Expired = 1 WHERE UserID = :uid AND Expired = 0";
				$query = $conn->prepare($expireoldtoken);
				$query->bindParam(':uid', $userid);
				$query->execute();
				
				$addtoken = "INSERT INTO tokens (UserID, Token, ExpirationTime) VALUE (:uid, :token, NOW() + INTERVAL 1 DAY)";
				$query = $conn->prepare($addtoken);
				$query->bindParam(':uid', $userid);
				$query->bindParam(':token', $token);
				$query->execute();
				
				echo "{ \"error\" : false, \"response\" : \"passwordcorrect\" , \"token\" : \"$token\"}";
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
	}
	else if ($operation == "TOKEN")
	{
		// USER ALREADY LOGGED IN, VERIFY TOKEN
	}
	else
	{
		// INVALID OPERATION
		echo "{ \"error\" : true , \"errorcode\" : 0 , \"response\" : \"invalidoperation\" }";
	}
