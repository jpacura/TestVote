/*define a angular app here for further feature*/

myApp.controller('indexController', ['$scope', '$http', function ($scope, $http) {

    $scope.login = function ()
    {
		window.location.href = "login.php";    
    }
}]);
