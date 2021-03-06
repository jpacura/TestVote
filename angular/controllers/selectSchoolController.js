myApp.controller('selectSchoolController', ['$scope', '$http', function ($scope, $http) {
        
	var RegisterData = "{\"operation\" : \"LISTSCHOOLS\"}";

	console.log("JSON sent to server:" + RegisterData);

	$http({
		method: 'POST',
		url: './mysql-schools.php',
		data: RegisterData
	})
		.then(
			function successCallback(response) {
				console.log('server says:' + response.data);
				
				if(response.data.error)
				{
					// THERE IS AN ERROR
					
					var errout = "ERROR: UNKNOWN SERVER ERROR!";
					if(response.data.errorcode == 5)
					{
						// NOT LOGGED IN
						errout = "ERROR: NOT LOGGED IN!";
						window.location.href = "logout.php";
					}
					else if(response.data.errorcode == 6)
					{
						// NOT ENROLLED
						errout = "You are not currently enrolled in any schools!";
					}
					
					$scope.studentusername = response.data.name;
					$scope.errtext = errout;
					$scope.isTableVisible = false;
					$scope.isNotEnrolled = true;
				}
				else
				{
					// NO ERRORS
					$scope.studentusername = response.data.name;
					$scope.isNotEnrolled = false;
					$scope.isTableVisible = true;
				}
				
				$scope.tabledata = response.data.schools;
			},
			function errorCallback(response) {
				console.log(response.statusText);
				console.log("HTTP status code:" + response.status);
			})
			
		$scope.removeschool = function (schoolid, schoolname) {

			var UserLoginData = "{\"operation\" : \"LEAVESCHOOL\", \"schoolusername\" : \"" + schoolid + "\" }";

			console.log("JSON sent to server:" + UserLoginData);
			
			var c = window.confirm("Are you sure that you would like to delete the school " + schoolname + "?");
			
			if(c)
			{
				$http({
					method: 'POST',
					url: './mysql-schools.php',
					data: UserLoginData
				})
					.then(
						function successCallback(response) {
							console.log('server says:' + response.data);
							
							if(response.data.error)
							{
								// THERE IS AN ERROR
								
								var errout = "ERROR: UNKNOWN SERVER ERROR!";
								if(response.data.errorcode == 5)
								{
									// NOT LOGGED IN
									window.location.href = "logout.php";
								}
								else if(response.data.errorcode == 7)
								{
									// LAST ADMINISTRATOR
									errout = "You are the only administrator for this school. Please delete this school from the Administrators Panel";
								}
								
								$scope.deleteerrtext = errout;
								$scope.isDeleteError = true;
							}
							else
							{
								// NO ERRORS
								window.location.href = "schools.php";
							}
						},
						function errorCallback(response) {
							console.log(response.statusText);
							console.log("HTTP status code:" + response.status);
						})
			}
		}
		
		$scope.election = function (schoolid)
		{
			document.getElementById("gotopage").setAttribute("action", "elections.php");
			document.getElementById("schoolnamepost").value = schoolid;
			document.getElementById("gotopage").submit();
		}
		
		$scope.admin = function (schoolid)
		{
			document.getElementById("gotopage").setAttribute("action", "admin.php");
			document.getElementById("schoolnamepost").value = schoolid;
			document.getElementById("gotopage").submit();
		}
}]);
