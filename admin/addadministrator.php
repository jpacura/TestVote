<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Add an Administrator - TestVote</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/navibar.css">
    <link rel="stylesheet" href="../css/registerstudent.css">
    <script src="../angular/dependence/angular.min.js"></script>
    <link rel="stylesheet" href="../css/angular-material.min.css">
    <script src="../angular/dependence/angular.min.js"></script>
    <script src="../angular/dependence/fontawsome.js"></script>
    <script src="../angular/dependence/angular-animate.min.js"></script>
    <script src="../angular/dependence/angular-aria.min.js"></script>
    <script src="../angular/dependence/angular-messages.min.js"></script>
    <script src="../angular/dependence/angular-material.min.js"></script>
</head>
<body ng-app="VoteSys">
<div ng-init='schoolid="<?php echo $_POST['school']; ?>"'></div>
<div class="navi">
    <div class="title">
        <img src="../images/logoblack.svg">
        <h3 style="padding-top: 7px">Add New Administrator</h3>
    </div>
</div>
<div class="mainArea" ng-controller="adminEnrollController">
    <div class="user-line">
        <h3><i class="fa fa-graduation-cap" aria-hidden="true"><b>Add a New Administrator</b></i></h3>
    </div>
    <div class="registration">
        <p class="ng-hide" id="errortext" ng-show="isError">{{errtext}}</p>
        <div class="panel-body">
            <form novalidate class="input-field" name="registrationForm" method=post>
                <div class="form-group">
                    <div class="alert"
                         ng-show="registrationForm.username.$touched &&
                         registrationForm.username.$error.required">
                        Please enter the new administrator's email address
                    </div>
                    <input class="form-control" type=text ng-model="newadmin.email"
                           name="email" placeholder="Email Address" required/>
                </div>
                <button type="button" value="Enroll"
                        ng-disabled="registrationForm.$invalid" ng-click="enroll(schoolid)">
                    <i class="fa fa-graduation-cap" aria-hidden="true"><b> Add New Administrator</b></i>
                </button>
            </form>
        </div>
        
        <!-- INVISIBLE FORM FOR POST DATA -->
		<form method="post" id="goback" action="administrators.php">
			<input type="hidden" id="schoolidback" name="school">
		</form>
        
    </div>
</div>
</body>
<script src="../angular/controllers/myApp.js"></script>
<script src="../angular/controllers/adminEnrollController.js"></script>
</html>
