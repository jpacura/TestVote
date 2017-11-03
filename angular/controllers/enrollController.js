/*enroll controller commit test */
myApp.controller('enrollController', ['$scope', '$http', function ($scope, $http) {
        
	var RegisterData = "{\"operation\" : \"TOKEN\"}";

	console.log("JSON sent to server:" + RegisterData);

	$http({
		method: 'POST',
		url: './mysql-users.php',
		data: RegisterData
	})
		.then(
			function successCallback(response) {
				console.log('server says:' + response.data);
				
				if(response.data.error)
				{
					// THERE IS AN ERROR
					
					if(response.data.errorcode == 5)
					{
						// NOT LOGGED IN
						window.location.href = "logout.php";
					}
					
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
			
			
		$scope.enroll = function () {

        $scope.school.operation = "ENROLL";
        var RegisterData = JSON.stringify($scope.school);

        console.log("JSON sent to server:" + RegisterData);

        $http({
            method: 'POST',
            url: './mysql-schools.php',
            data: RegisterData
        })
            .then(
                function successCallback(response) {
                    console.log('server says:' + response.data);
                    
                    var errout = "ERROR: UNKNOWN SERVER ERROR!";
                    if(response.data.error)
                    {
						// THERE IS AN ERROR
						if(response.data.errorcode == 9)
						{
							// ALREADY ENROLLED
							var errout = "You are already enrolled in this school!";
						}
						else if(response.data.errorcode == 8)
						{
							// SCHOOL DOES NOT EXIST
							var errout = "The specified school does not exist!";
						}
						else if(response.data.errorcode == 5)
						{
							// NOT LOGGED IN
							window.location.href = "logout.php";
						}
						$scope.errtext = errout;
						$scope.isError = true;
					}
					else
					{
						// NO ERROR
						window.location.href = "schools.php";
					}
                },
                function errorCallback(response) {
                    console.log(response.statusText);
                    console.log("HTTP status code:" + response.status);
                })
    }
			
}]);
