<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  <!-- REQUIRED HTML HEADER -->
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"> <!-- REQUIRED HTML HEADER -->
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
</head>
<body ng-app="myApp">
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Voting System</h3>
    </div>
</div>
<div class="mainArea" ng-controller="loginController">
    <div class="login">
        <div class="panel-body">
            <img src="images/logo.svg">
            <form novalidate class="input-field" name="loginForm" method=post>
                <!--form validation by angular-->
                <div class="form-group">
                    <!--send error message when username invalid-->
                    <div ng-show="loginForm.username.$touched">
                        <div class="alert" ng-show="loginForm.username.$error.required">
                            Username can't be empty.
                        </div>
                        <div class="alert" ng-show="loginForm.username.$error.email">
                            Please input valid email address.
                        </div>
                    </div>
                    <!---->
                    <input type="email" name="username" class="form-control"
                           ng-model="user.name"
                           placeholder="username" required>
                </div>
                <div class="form-group">
                    <!--send error message when password invalid-->
                    <div class="alert"
                         ng-show="loginForm.password.$touched &&
                         loginForm.password.$error.required">
                        Password can't be empty.
                    </div>
                    <!---->
                    <input type="password" name="password" class="form-control"
                           ng-model="user.password"
                           placeholder="password" required>
                </div>
                <button class="btn form-control" type=button value="Login" ng-click="login(user)">Login</button>
            </form>
            <div class="registerLink">
                <!-- <span><input type="checkbox">Remember me</span> -->
                <a href="registration.php">Register here</a>
            </div>
        </div>
    </div>
    <p>{{temp}}</p>
</div>




</body>

<script>
    /*define a angular app here for further feature*/
    var myApp = angular.module('myApp', []);
    myApp.controller('loginController', ['$scope', '$http', function ($scope, $http) {
		$scope.login = function(user)
		{
			$scope.user.operation = "LOGIN";
			$http.post("mysql-users.php", user).then(function(data, status) {
                            $scope.temp = data.data;
                        })
		}
    }]);
</script>

</html>
