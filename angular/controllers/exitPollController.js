
myApp.controller('exitPollController', ['$scope', '$http', function ($scope, $http) {
        
	var RegisterData = "{\"operation\" : \"EXITPOLLLOAD\" , \"schoolusername\" : \"" + $scope.schoolid + "\", \"electionid\" : \"" + $scope.electionid + "\", \"voteid\" : \"" + $scope.voteid + "\" }";

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
					window.location.href = "logout.php";
				}
				else
				{
					// NO ERRORS
					
				}
				
			},
			function errorCallback(response) {
				console.log(response.statusText);
				console.log("HTTP status code:" + response.status);
			})
			
			
			
			$scope.submitvotes = function()
			{
				var c = JSON.stringify($scope.survey);
				
				if(c === undefined)
				{
					var confirm = window.confirm("The exit poll is blank. Are you sure you want to leave without submitting?");
					
					if(confirm)
					{
						window.location.href = "schools.php";
					}
				}
				else
				{
					var RD2 = "{\"operation\" : \"EXITPOLLVOTE\" , \"schoolusername\" : \"" + $scope.schoolid + "\", \"electionid\" : \"" + $scope.electionid + "\", \"voteid\" : \"" + $scope.voteid + "\" , \"survey\" : " + c + " }";
					
					$http({
					method: 'POST',
					url: './mysql-vote.php',
					data: RD2
					})
					.then(
						function successCallback(response) {
							console.log('server says:' + response.data);
							
							if(response.data.error)
							{
								window.location.href = "logout.php";
							}
							else
							{
								// NO ERRORS
								alert("Thank you for filling out the survey!");
								window.location.href = "schools.php";
							}
							
						},
						function errorCallback(response) {
							console.log(response.statusText);
							console.log("HTTP status code:" + response.status);
						})
				}
			}
}]);
