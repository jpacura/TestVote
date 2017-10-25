
myApp.controller('schoolController', ['$scope', '$http', function ($scope, $http) {

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
					if(response.data.errorcode == 5)
					{
						// NOT LOGGED IN
						window.location.href = "logout.php";
					}
				}
			},
			function errorCallback(response) {
				console.log(response.statusText);
				console.log("HTTP status code:" + response.status);
			})

    $scope.register = function () {

        $scope.school.operation = "REGISTER";

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
						else if(response.data.errorcode == 4)
						{
							// SCHOOL ALREADY EXISTS
							errout = "This School Already Exists!";
						}
						
						$scope.errtext = errout;
						$scope.isNotEnrolled = true;
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
}]);
