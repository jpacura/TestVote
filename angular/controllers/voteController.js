
myApp.controller('voteController', ['$scope', '$http', function ($scope, $http) {
        
	var RegisterData = "{\"operation\" : \"GETQUESTIONS\" , \"schoolusername\" : \"" + $scope.schoolid + "\", \"electionid\" : \"" + $scope.electionid + "\" }";

	console.log("JSON sent to server:" + RegisterData);
	
	$scope.formdata = {};

	$http({
		method: 'POST',
		url: './mysql-vote.php',
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
						$scope.electionname = response.data.electionname;
						errout = "The school currently has no elections!";
						$scope.errtext = errout;
						$scope.isElectionVisible = false;
						$scope.isError = true;
					}
					else if(response.data.errorcode == 11)
					{
						// INVALID ELECTION
						window.location.href = "schools.php";
					}
					else if(response.data.errorcode == 12)
					{
						// NO QUESTIONS
						$scope.studentusername = response.data.name;
						$scope.schoolname = response.data.schoolname;
						$scope.electionname = response.data.electionname;
						errout = "This election has no questions!";
						$scope.errtext = errout;
						$scope.isElectionVisible = false;
						$scope.isError = true;
					}
					else if(response.data.errorcode == 13)
					{
						// USER ALREADY VOTED
						$scope.studentusername = response.data.name;
						$scope.schoolname = response.data.schoolname;
						$scope.electionname = response.data.electionname;
						errout = "You have already voted in this election!";
						$scope.errtext = errout;
						$scope.isElectionVisible = false;
						$scope.isError = true;
					}
					
					
				}
				else
				{
					// NO ERRORS
					$scope.studentusername = response.data.username;
					$scope.schoolname = response.data.schoolname;
					$scope.electionname = response.data.electionname;
					document.getElementById("electionidpost").value = $scope.electionid;
					document.getElementById("schoolidpost").value = $scope.schoolid;
					$scope.isError = false;
					$scope.isElectionVisible = true;
				}
				$scope.questions = response.data.questions;
				
			},
			function errorCallback(response) {
				console.log(response.statusText);
				console.log("HTTP status code:" + response.status);
			})
			
		$scope.submitvotes = function ()
		{
			var form = JSON.stringify($scope.formdata);
			
			var RegisterData = "{\"operation\" : \"VOTE\" , \"schoolusername\" : \"" + $scope.schoolid + "\", \"electionid\" : \"" + $scope.electionid + "\" , \"formdata\" : " + form + "}";

			console.log("JSON sent to server:" + RegisterData);
			
			$http({
		method: 'POST',
		url: './mysql-vote.php',
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
						errout = "The school currently has no elections!";
						$scope.errtext = errout;
						$scope.isElectionVisible = false;
						$scope.isError = true;
					}
					else if(response.data.errorcode == 11)
					{
						// INVALID ELECTION
						window.location.href = "schools.php";
					}
					else if(response.data.errorcode == 12)
					{
						// NO QUESTIONS
						errout = "This election has no questions!";
						$scope.errtext = errout;
						$scope.isElectionVisible = false;
						$scope.isError = true;
					}
					else if(response.data.errorcode == 13)
					{
						// USER ALREADY VOTED
						errout = "You have already voted in this election!";
						$scope.errtext = errout;
						$scope.isElectionVisible = false;
						$scope.isError = true;
					}
					else if(response.data.errorcode == 17)
					{
						// USER SELECTED NO OPTIONS
						errout = "You did not select any options!";
						$scope.errtext = errout;
						$scope.isError = true;
					}
					
					
				}
				else
				{
					// NO ERRORS
					window.location.href = "thankyou.php";
				}
				
			},
			function errorCallback(response) {
				console.log(response.statusText);
				console.log("HTTP status code:" + response.status);
			})
		}
			
}]);
