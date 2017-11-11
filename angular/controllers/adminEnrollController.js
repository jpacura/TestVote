/*enroll controller commit test */
myApp.controller('adminEnrollController', ['$scope', '$http', function ($scope, $http) {
        
	var RegisterData = "{\"operation\" : \"TOKEN\"}";

	console.log("JSON sent to server:" + RegisterData);

	$http({
		method: 'POST',
		url: '../mysql-users.php',
		data: RegisterData
	})
		.then(
			function successCallback(response) {
				console.log('server says:' + response.data);
				
				$scope.temp = response.data;
				
				if(response.data.error)
				{
					// THERE IS AN ERROR
					
					if(response.data.errorcode == 5)
					{
						// NOT LOGGED IN
						window.location.href = "../logout.php";
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
			
			
		$scope.enroll = function (schoolid) {

        var RegisterData = "{ \"operation\" : \"ENROLL\" , \"schoolid\" : \"" + schoolid + "\" , \"email\" : \"" + $scope.newadmin.email + "\" }";

        console.log("JSON sent to server:" + RegisterData);

        $http({
            method: 'POST',
            url: './mysql-admin.php',
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
						else if(response.data.errorcode == 15)
						{
							// SCHOOL DOES NOT EXIST
							var errout = "This user does not exist!";
						}
						else if(response.data.errorcode == 5)
						{
							// NOT LOGGED IN
							window.location.href = "../logout.php";
						}
						$scope.errtext = errout;
						$scope.isError = true;
					}
					else
					{
						// NO ERROR
						document.getElementById("schoolidback").value = schoolid;
						document.getElementById("goback").submit();
					}
                },
                function errorCallback(response) {
                    console.log(response.statusText);
                    console.log("HTTP status code:" + response.status);
                })
    }
			
}]);
