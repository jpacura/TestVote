<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Register a School - TestVote</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/registerstudent.css">
    <script src="angular/dependence/angular.min.js"></script>
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
        When you register a new school, you will be added as the first administrator user. This user can add other administrators, as well as add other administrators.
    </p>
    <div class="registration">
        <div class="panel-body">
			
			<p class="ng-hide" id="tableerrortext" ng-show="isNotEnrolled">{{errtext}}</p>
			
            <form novalidate class="input-field" name="registrationForm" method=post>
                <div class="form-group">
                    <div class="alert"
                         ng-show="registrationForm.schoolFullName.$touched &&
                         registrationForm.schoolFullName.$error.required">
                        Please input school full name.
                    </div>
                    <input class="form-control" type=text ng-model="school.schoolFullName"
                           name="schoolFullName" placeholder="School Full Name" required/>
                </div>
                <div class="form-group">
                    <div class="alert"
                         ng-show="registrationForm.schoolUsername.$touched &&
                         registrationForm.schoolUsername.$error.required">
                        Please input school username.
                    </div>
                    <input class="form-control" type=text ng-model="school.schoolUsername"
                           name="schoolUsername" placeholder="School Username" required/>
                </div>
                <button type="button" value="Register Student"
                        ng-disabled="registrationForm.$invalid" ng-click="register(school)" >Register School</button>
            </form>
        </div>
    </div>
</div>
</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/schoolController.js"></script>
</html>
