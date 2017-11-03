
myApp.controller('studentController', ['$scope', '$http', function ($scope, $http) {

    $scope.register = function () {

        $scope.user.operation = "REGISTER";
        var RegisterData = JSON.stringify($scope.user);

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
						var errout = "ERROR: UNKNOWN SERVER ERROR!";
						if(response.data.errorcode == 2)
						{
							// USER ALREADY EXISTS
							errout = "User already exists!";
						}
						else if(response.data.errorcode == 3)
						{
							// PASSWORDS DO NOT MATCH
							errout = "Passwords do not match!";
						}
						$scope.errtext = errout;
						$scope.isError = true;
					}
					else
					{
						// NO ERROR
						window.location.href = "login.php";
					}
                },
                function errorCallback(response) {
                    console.log(response.statusText);
                    console.log("HTTP status code:" + response.status);
                })
    }
}]);
