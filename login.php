<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/angular-material.min.css">
    <script src="angular/dependence/angular.min.js"></script>
    <script src="angular/dependence/fontawsome.js"></script>
    <script src="angular/dependence/angular-animate.min.js"></script>
    <script src="angular/dependence/angular-aria.min.js"></script>
    <script src="angular/dependence/angular-messages.min.js"></script>
    <script src="angular/dependence/angular-material.min.js"></script>
</head>
<body ng-app="VoteSys">
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Learn2Vote</h3>
    </div>
</div>
<div class="mainArea" ng-controller="loginController">
    <div class="login">
        <div class="panel-body">
            <p class="ng-hide" id="formerrortext" ng-show="isFormError">{{errtext}}</p>
            <img src="images/logo.svg">
            <form novalidate class="input-field" name="loginForm" method=post ng-enter="login()">
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
                    <!----------------------->

                    <input type="email" name="username" class="form-control"
                           ng-model="user.name"
                           placeholder="username" required>

                </div>
                <!------------------------- password ---------------------------------->
                <div class="form-group">
                    <!--send error message when password invalid-->
                    <div class="alert"
                         ng-show="loginForm.password.$touched &&
                         loginForm.password.$error.required">
                        Password can't be empty.
                    </div>

                    <!----------------------->

                    <input type="password" name="password" class="form-control"
                           ng-model="user.password"
                           placeholder="password" required>

                </div>
                <button class="btn form-control" type=button value="Login"
                        ng-disabled="loginForm.$invalid" ng-click="login()">
                    <i class="fa fa-sign-in" aria-hidden="true"><b> Login</b></i>
                </button>
            </form>
            <div class="registerLink">
                <!-- <span><input type="checkbox">Remember me</span> -->
                <a href="register.php">New to Learn2Vote? Register here!</a>
            </div>
        </div>
    </div>
</div>


</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/loginController.js"></script>


</html>
