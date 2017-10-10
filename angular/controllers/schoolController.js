
myApp.controller('schoolController', ['$scope', '$http', function ($scope, $http) {

    $scope.register = function () {

        $scope.school.operation = "REGISTER";

        var RegisterData = JSON.stringify($scope.school);

        console.log("JSON sent to server:" + RegisterData);

        $http({
            method: 'POST',
            url: './mysql-users.php',
            data: RegisterData
        })
            .then(
                function successCallback(response) {
                    console.log('server says:' + response.data);
                    $scope.temp = response.data;
                },
                function errorCallback(response) {
                    console.log(response.statusText);
                    console.log("HTTP status code:" + response.status);
                })
    }
}]);