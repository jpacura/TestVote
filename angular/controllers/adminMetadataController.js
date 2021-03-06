
myApp.controller('adminMetadataController', ['$scope', '$http', function ($scope, $http) {
        
	var RegisterData = "{\"operation\" : \"VIEWRESULTS\" , \"schoolusername\" : \"" + $scope.schoolid + "\" }";

	console.log("JSON sent to server:" + RegisterData);
	
	

	$http({
		method: 'POST',
		url: './mysql-metadata.php',
		data: RegisterData
	})
		.then(
			function successCallback(response) {
				console.log('server says:' + response.data);
				
				$scope.temp = response.data;
				
				if(response.data.error)
				{
					// THERE IS AN ERROR
					
					var errout = "ERROR: UNKNOWN SERVER ERROR!";
					if(response.data.errorcode == 5)
					{
						// NOT LOGGED IN
						errout = "ERROR: NOT LOGGED IN!";
						//window.location.href = "../logout.php";
					}
					else if(response.data.errorcode == 6)
					{
						// NOT ENROLLED IN SCHOOL
						errout = "ERROR: NOT ENROLLED IN SELECTED SCHOOL!";
						//window.location.href = "../schools.php";
					}
					else if(response.data.errorcode == 11)
					{
						// ELECTION INVALID
						errout = "ERROR: ELECTION IS INVALID!";
						//window.location.href = "../schools.php";
					}
				}
				else
				{
					// NO ERRORS
					
				}
				$scope.tabledata = response.data.results;
				
			},
			function errorCallback(response) {
				console.log(response.statusText);
				console.log("HTTP status code:" + response.status);
			})
			
	
			
			$scope.gotoadminpanel = function (schoolid)
			{
				document.getElementById("refresh").action = "elections.php";
				document.getElementById("schoolidrefresh").value = schoolid;
				document.getElementById("refresh").submit();
			}
			
			$scope.deletevote = function (userid, electionid) {

				var UserLoginData = "{\"operation\" : \"DELETEVOTE\", \"schoolusername\" : \"" + $scope.schoolid + "\" , \"userid\" : \"" + userid + "\" }";

				console.log("JSON sent to server:" + UserLoginData);
				
				$http({
					method: 'POST',
					url: './mysql-elections.php',
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
									//window.location.href = "../logout.php";
								}
								else if(response.data.errorcode == 6)
								{
									// NOT ENROLLED IN SCHOOL
									errout = "ERROR: NOT ENROLLED IN SELECTED SCHOOL!";
									//window.location.href = "schools.php";
								}
								
								$scope.errtext = errout;
								$scope.isError = true;
							}
							else
							{
								
							}
							
							{
								// NO ERRORS
								document.getElementById("schoolidrefresh").value = $scope.schoolid;
								document.getElementById("electionidrefresh").value = electionid;
								document.getElementById("refresh").submit();
							}
						},
						function errorCallback(response) {
							console.log(response.statusText);
							console.log("HTTP status code:" + response.status);
						})
			}
}]);
