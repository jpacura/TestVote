
myApp.controller('selectSchoolController', ['$scope', '$http', function ($scope, $http) {

    $scope.listSchools = function () {

        //$scope.school.operation = "LISTSCHOOLS";

        //var RegisterData = JSON.stringify($scope.school);
        
        var RegisterData = "{\"operation\" : \"LISTSCHOOLS\"}";

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
						var errout = "ERROR: UNKNOWN SERVER ERROR!";
						if(response.data.errorcode == 5)
						{
							// NOT LOGGED IN
							errout = "ERROR: NOT LOGGED IN!";
							window.location.href = "login.php";
						}
						else if(response.data.errorcode == 6)
						{
							// NOT ENROLLED
							errout = "You are not currently enrolled in any schools!";
						}
						
						$scope.errtext = errout;
						$scope.isTableVisible = false;
						$scope.isNotEnrolled = true;
					}
					else
					{
						$scope.isNotEnrolled = false;
						$scope.isTableVisible = true;
					}
                    
                    $scope.tabledata = response.data.schools;
                },
                function errorCallback(response) {
                    console.log(response.statusText);
                    console.log("HTTP status code:" + response.status);
                })
    }
}]);
