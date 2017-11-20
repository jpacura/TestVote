
myApp.controller('adminResultsController', ['$scope', '$http', function ($scope, $http) {
        
	//var RegisterData = "{\"operation\" : \"LISTELECTIONS\" , \"schoolusername\" : \"" + $scope.schoolid + "\" }";

	//console.log("JSON sent to server:" + RegisterData);
	
	

	//$http({
		//method: 'POST',
		//url: './mysql-admin.php',
		//data: RegisterData
	//})
		//.then(
			//function successCallback(response) {
				//console.log('server says:' + response.data);
				
				//if(response.data.error)
				//{
					//// THERE IS AN ERROR
					
					//var errout = "ERROR: UNKNOWN SERVER ERROR!";
					//if(response.data.errorcode == 5)
					//{
						//// NOT LOGGED IN
						//errout = "ERROR: NOT LOGGED IN!";
						//window.location.href = "logout.php";
					//}
					//else if(response.data.errorcode == 6)
					//{
						//// NOT ENROLLED IN SCHOOL
						//errout = "ERROR: NOT ENROLLED IN SELECTED SCHOOL!";
						//window.location.href = "schools.php";
					//}
					//else if(response.data.errorcode == 10)
					//{
						//// NO ELECTIONS
						//$scope.studentusername = response.data.name;
						//$scope.schoolname = response.data.schoolname;
						//errout = "The school currently has no elections!";
						//$scope.errtext = errout;
						//$scope.isTableVisible = false;
						//$scope.isNoElections = true;
					//}
					
					
				//}
				//else
				//{
					//// NO ERRORS
					//$scope.studentusername = response.data.name;
					//$scope.schoolname = response.data.schoolname;
					//$scope.isNoElections = false;
					//$scope.isTableVisible = true;
				//}
				//$scope.tabledata = response.data.elections;
				
			//},
			//function errorCallback(response) {
				//console.log(response.statusText);
				//console.log("HTTP status code:" + response.status);
			//})
			
			$scope.gotoadminpanel = function (schoolid)
			{
				document.getElementById("refresh").action = "../admin.php";
				document.getElementById("schoolidrefresh").value = schoolid;
				document.getElementById("refresh").submit();
			}
			
			//$scope.toggle = function (schoolid, electionid) {

				//var UserLoginData = "{\"operation\" : \"TOGGLEELECTION\", \"schoolusername\" : \"" + schoolid + "\" , \"electionid\" : \"" + electionid + "\" }";

				//console.log("JSON sent to server:" + UserLoginData);
				
				//$http({
					//method: 'POST',
					//url: './mysql-admin.php',
					//data: UserLoginData
				//})
					//.then(
						//function successCallback(response) {
							//console.log('server says:' + response.data);
							
							//if(response.data.error)
							//{
								//// THERE IS AN ERROR
								
								//var errout = "ERROR: UNKNOWN SERVER ERROR!";
								//if(response.data.errorcode == 5)
								//{
									//// NOT LOGGED IN
									//window.location.href = "../logout.php";
								//}
								//else if(response.data.errorcode == 6)
								//{
									//// NOT ENROLLED IN SCHOOL
									//errout = "ERROR: NOT ENROLLED IN SELECTED SCHOOL!";
									//window.location.href = "schools.php";
								//}
								
								//$scope.errtext = errout;
								//$scope.isError = true;
							//}
							//else
							//{
								//// NO ERRORS
								//document.getElementById("schoolidrefresh").value = schoolid;
								//document.getElementById("refresh").submit();
							//}
						//},
						//function errorCallback(response) {
							//console.log(response.statusText);
							//console.log("HTTP status code:" + response.status);
						//})
			//}
			
			//$scope.vote = function (electionid, schoolid)
			//{
				//document.getElementById("electionidpost").value = electionid;
				//document.getElementById("schoolidpost").value = schoolid;
				//document.getElementById("gotopage").submit();
			//}
}]);
