<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <title>Register a School - TestVote</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/registerstudent.css">
</head>

<body ng-app="VoteSys">
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Voting System</h3>
    </div>
</div>
<div class="mainArea" ng-controller="schoolController">
    <div class="user-line">
        <p>School Registration</p>
    </div>
    <p class="user-note">
        When you register a new school, you must first create an
        administrator user. This user can add other administrators
        as well as add other administrators.
    </p>
    <div class="registration">
        <div class="panel-body">
            <form novalidate class="input-field" name="registrationForm" method=post>
                <div class="form-group">
                    <div class="alert"
                         ng-show="registrationForm.schoolName.$touched &&
                         registrationForm.schoolName.$error.required">
                        Please input school name.
                    </div>
                    <input class="form-control" type=text ng-model="school.schoolName"
                           name="schoolName" placeholder="Name of School..." required/>
                </div>
                <div class="form-group">
                    <div class="alert"
                         ng-show="registrationForm.adminName.$touched &&
                         registrationForm.adminName.$error.required">
                        Please input administration full name
                    </div>
                    <input class="form-control" type="text" ng-model="school.adminName"
                           name="adminName" placeholder="Administrator Full Name..." required>
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
                    <input class="form-control" type="text" name="email" ng-model="school.email"
                           placeholder="Administrator Email Address..." required/>
                </div>
                <div class="form-group">
                    <div class="alert"
                         ng-show="registrationForm.password.$touched &&
                        registrationForm.password.$error.required">
                        Password can't be empty.
                    </div>
                    <input class="form-control" type="password" name="password" ng-model="school.password"
                           placeholder="Administrator Password..." required/>
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
                    <input class="form-control" type="password" name="confirm" ng-model="school.confirm"
                           placeholder="Confirm Password..." required password-match="school.password"/>
                </div>
                <button type="submit" value="Register Student"
                        ng-disabled="registrationForm.$invalid" ng-click="register()" >Register School</button>
            </form>
        </div>
    </div>
</div>
</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/schoolController.js"></script>
<script src="angular/directives/passwordMatch.js"></script>
</html>
