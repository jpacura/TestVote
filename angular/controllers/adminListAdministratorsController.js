
myApp.controller('adminListAdministratorsController', ['$scope', '$http', function ($scope, $http) {
        
	var RegisterData = "{\"operation\" : \"LISTADMINISTRATORS\" , \"schoolusername\" : \"" + $scope.schoolid + "\" }";

	console.log("JSON sent to server:" + RegisterData);
	
	

	$http({
		method: 'POST',
		url: './mysql-admin.php',
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
						window.location.href = "../logout.php";
					}
					else if(response.data.errorcode == 6)
					{
						// NOT ENROLLED IN SCHOOL
						errout = "ERROR: NOT ENROLLED IN SELECTED SCHOOL!";
						window.location.href = "../schools.php";
					}
					else if(response.data.errorcode == 14)
					{
						// NO USERS
						$scope.studentusername = response.data.name;
						$scope.schoolname = response.data.schoolname;
						errout = "The school currently has no students!";
						$scope.errtext = errout;
						$scope.isTableVisible = false;
						$scope.isError = true;
					}
					
					
				}
				else
				{
					// NO ERRORS
					$scope.studentusername = response.data.name;
					$scope.schoolname = response.data.schoolname;
					$scope.isError = false;
					$scope.isTableVisible = true;
				}
				$scope.tabledata = response.data.userlist;
				
			},
			function errorCallback(response) {
				console.log(response.statusText);
				console.log("HTTP status code:" + response.status);
			})
			
			$scope.removeadmin = function (schoolid, username, fullname) {

				var UserLoginData = "{\"operation\" : \"REMOVEUSER\", \"schoolusername\" : \"" + schoolid + "\" , \"usertoremove\" : \"" + username + "\" }";

				console.log("JSON sent to server:" + UserLoginData);
				
				var c = window.confirm("Are you sure that you would like to delete the administrator " + fullname + "?");
				
				if(c)
				{
					$http({
						method: 'POST',
						url: './mysql-admin.php',
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
										window.location.href = "../logout.php";
									}
									else if(response.data.errorcode == 7)
									{
										// LAST ADMINISTRATOR
										errout = "You cannot remove the last administrator from a school!";
									}
									
									$scope.errtext = errout;
									$scope.isError = true;
								}
								else
								{
									// NO ERRORS
									document.getElementById("schoolidrefresh").value = schoolid;
									document.getElementById("refresh").submit();
								}
							},
							function errorCallback(response) {
								console.log(response.statusText);
								console.log("HTTP status code:" + response.status);
							})
				}
			}
			
			$scope.gotoadminpanel = function (schoolid)
			{
				document.getElementById("refresh").action = "../admin.php";
				document.getElementById("schoolidrefresh").value = schoolid;
				document.getElementById("refresh").submit();
			}
			
			$scope.addadmin = function (schoolid)
			{
				document.getElementById("refresh").action = "addadministrator.php";
				document.getElementById("schoolidrefresh").value = schoolid;
				document.getElementById("refresh").submit();
			}
			
			//$scope.vote = function (electionid, schoolid)
			//{
				//document.getElementById("electionidpost").value = electionid;
				//document.getElementById("schoolidpost").value = schoolid;
				//document.getElementById("gotopage").submit();
			//}
}]);
