/*define a angular app here for further feature*/

myApp.controller('loginController', ['$scope', '$http', function ($scope, $http) {

    $scope.login = function () {

        $scope.user.operation = "LOGIN";

        var UserLoginData = JSON.stringify($scope.user);

        console.log("JSON sent to server:" + UserLoginData);

        $http({
            method: 'POST',
            url: './mysql-users.php',
            data: UserLoginData
        })
            .then(
                function successCallback(response) {
                    console.log('server says:' + response.data);
                    
                    if(response.data.error)
					{
						// THERE IS AN ERROR
						
						var errout = "ERROR: UNKNOWN SERVER ERROR!";
						if(response.data.errorcode == 1)
						{
							// LOGIN INCORRECT
							errout = "Incorrect Password!";
						}
						
						$scope.errtext = errout;
						$scope.isFormError = true;
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
