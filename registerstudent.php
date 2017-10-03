<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Registration - TestVote</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/registerstudent.css">
</head>
<body ng-app="myApp">
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Voting System</h3>
    </div>
</div>
<div class="mainArea" ng-controller="studentController">
    <div class="user-line">
        <p>Student Registration</p>
    </div>
    <p class="user-note">
        A student can only register here after an administrator creates
        a student account for them. Please use your full name and student ID.
        If you have problems registering, please contact the school
        administrator in charge of elections.
    </p>
    <div class="registration">
        <div class="panel-body">
            <form novalidate class="input-field" name="registrationForm" method=post>
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
                    <div class="alert"
                         ng-show="registrationForm.studentid.$touched &&
                         registrationForm.studentid.$error.required">
                        Please input yor student ID
                    </div>
                    <input class="form-control" type="text" ng-model="user.studentid"
                           name="studentid" placeholder="Student ID..." required>
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
                    <input class="form-control" type="password" name="confirm"
                           ng-model="user.confirm"
                           placeholder="Confirm Password..." required/>
                </div>
                <button type="submit" value="Register Student">Register Student</button>
            </form>
        </div>
    </div>
</div>
</body>

<script>
    /*define a angular app here for further feature*/
    var myApp = angular.module('myApp', []);
    myApp.controller('studentController', ['$scope', function ($scope) {
//        leave an empty controller here for further building
    }]);
</script>
</html>
