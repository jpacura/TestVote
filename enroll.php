<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Enroll - TestVote</title>
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
<div class="mainArea" ng-controller="enrollController">
    <div class="user-line">
        <p>Student Registration</p>
    </div>
    <div class="registration">
		<p class="ng-hide" id="errortext" ng-show="isError">{{errtext}}</p>
        <div class="panel-body">
            <form novalidate class="input-field" name="registrationForm" method=post>
                <div class="form-group">
                    <div class="alert"
                         ng-show="registrationForm.username.$touched &&
                         registrationForm.username.$error.required">
                        Please enter the school's username
                    </div>
                    <input class="form-control" type=text ng-model="school.username"
                           name="username" placeholder="School Username" required/>
                </div>
                <button type="button" value="Enroll"
                        ng-disabled="registrationForm.$invalid" ng-click="enroll()">Register Student</button>
            </form>
        </div>
    </div>
</div>
</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/enrollController.js"></script>
</html>
