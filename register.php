<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Registration - TestVote</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/registerstudent.css">
    <script src="angular/dependence/angular.min.js"></script>
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
        <img src="images/whiteLogo.png">
        <h3 style="padding-top: 7px">Learn2Vote</h3>
    </div>
</div>
<div class="mainArea" ng-controller="studentController">
    <div class="user-line">
        <h3><i class="fa fa-graduation-cap" aria-hidden="true"><b> Student Registration</b></i></h3>
    </div>
    <p class="user-note"></p> <!-- This message was removed. This p was left in for style reasons -->
    <div class="registration">
        <div class="panel-body">
            <form novalidate class="input-field" name="registrationForm" method=post>
                <p class="ng-hide" id="errortext" ng-show="isError">{{errtext}}</p>
                <div class="form-group">
                    <div class="alert"
                         ng-show="registrationForm.fname.$touched &&
                         registrationForm.fname.$error.required">
                        Please input your full name.
                    </div>
                    <input class="form-control" type=text ng-model="user.fname"
                           name="fname" placeholder="Full Name..." required/>
                </div>
                <div class="form-group">
                    <div ng-show="registrationForm.email.$touched">
                        <div class="alert" ng-show="registrationForm.email.$error.required">
                            Email address can't be empty.
                        </div>
                        <div class="alert" ng-show="registrationForm.email.$error.email">
                            Please input valid email address.
                        </div>
                    </div>
                    <input class="form-control" type="email"
                           ng-model="user.email" name="email"
                           placeholder="Email Address..." required/>
                </div>
                <div class="form-group">
                    <div class="alert"
                         ng-show="registrationForm.password.$touched &&
                        registrationForm.password.$error.required">
                        Password can't be empty.
                    </div>
                    <input class="form-control" type="password" name="password"
                           ng-model="user.password"
                           placeholder="Create Password..." required/>
                </div>
                <div class="form-group">
                    <div class="alert"
                         ng-show="registrationForm.confirm.$touched &&
                         registrationForm.confirm.$error.required">
                        Please confirm your password.
                    </div>
                    <div class="alert"
                         ng-show="registrationForm.confirm.$touched &&
                         registrationForm.confirm.$invalid && !registrationForm.confirm.$error.required">
                        Confirm password doesn't match.
                    </div>
                    <input class="form-control" type="password" name="confirm"
                           ng-model="user.confirm"
                           placeholder="Confirm Password..." required password-match="user.password"/>
                </div>
                <button type="button" value="Register Student"
                        ng-disabled="registrationForm.$invalid" ng-click="register()">
                    <i class="fa fa-graduation-cap" aria-hidden="true"><b> Register Student</b></i></button>
            </form>
        </div>
    </div>
</div>
</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/studentController.js"></script>
<script src="angular/directives/passwordMatch.js"></script>
</html>
