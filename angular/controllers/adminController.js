/*enroll controller commit test */
myApp.controller('adminController', ['$scope', '$http', function ($scope, $http) {
       
        var RegisterData = "{\"operation\" : \"LISTELECTIONS\" , \"schoolusername\" : \"" + $scope.schoolid + "\" }";
       
		$http({
		method: 'POST',
		url: './mysql-elections.php',
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
						// NOT ENROLLED IN SCHOOL
						errout = "ERROR: NOT ENROLLED IN SELECTED SCHOOL!";
						window.location.href = "schools.php";
					}
					else if(response.data.errorcode == 10)
					{
						// NO ELECTIONS
						$scope.studentusername = response.data.name;
						$scope.schoolname = response.data.schoolname;
						errout = "The school currently has no elections!";
						$scope.errtext = errout;
						$scope.isTableVisible = false;
						$scope.isNoElections = true;
					}
					
					
				}
				else
				{
					// NO ERRORS
					//$scope.studentusername = response.data.name;
					$scope.schoolname = response.data.schoolname;
					//$scope.isNoElections = false;
					//$scope.isTableVisible = true;
				}
				//$scope.tabledata = response.data.elections;
				
			},
			function errorCallback(response) {
				console.log(response.statusText);
				console.log("HTTP status code:" + response.status);
			})
			
			$scope.viewstudents = function (schoolid)
			{
				document.getElementById("gotopage").action = "admin/users.php";
				document.getElementById("schoolidform").value = schoolid;
				document.getElementById("gotopage").submit();
			}
			
			$scope.viewadministrators = function (schoolid)
			{
				document.getElementById("gotopage").action = "admin/administrators.php";
				document.getElementById("schoolidform").value = schoolid;
				document.getElementById("gotopage").submit();
			}
			
			$scope.viewelections = function (schoolid)
			{
				document.getElementById("gotopage").action = "admin/elections.php";
				document.getElementById("schoolidform").value = schoolid;
				document.getElementById("gotopage").submit();
			}
			
}]);
