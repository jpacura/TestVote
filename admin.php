<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrative Panel - TestVote</title>
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
<div ng-init='schoolid="<?php echo $_POST['school']; ?>"'></div>
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Administrative Panel</h3>
    </div>
    <a href="logout.php">Log Out</a>
</div>
<div class="mainArea" ng-controller="adminController">
    <div class="user-line">
        <h3><i class="fa fa-graduation-cap" aria-hidden="true"><b>Administrative Panel for {{schoolname}}</b></i></h3>
    </div>
    <div class="registration">
        <p class="ng-hide" id="errortext" ng-show="isError">{{errtext}}</p>
        <div class="panel-body">
			<button ng-click="viewstudents(schoolid)">View Students</button>
			<button ng-click="viewadministrators(schoolid)">View Administrators</button>
			<button ng-click="viewelections(schoolid)">View Elections</button>
        </div>
        
        <!-- INVISIBLE FORM FOR POST DATA -->
		<form method="post" id="gotopage" action="">
			<input type="hidden" id="schoolidform" name="school">
		</form>
        
    </div>
</div>
</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/adminController.js"></script>
</html>
