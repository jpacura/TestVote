<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Vote!</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/angular-material.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/elections.css">
</head>
<body ng-app="VoteSys">
<div ng-init='schoolid="<?php echo $_POST['schoolid']; ?>"'></div>
<div ng-init='electionid="<?php echo $_POST['electionid']; ?>"'></div>

<div class="navi">
    <div class="title">
        <img src="images/whiteLogo.png">
        <h3 style="padding-top: 7px">Learn2Vote</h3>
    </div>
</div>

<div class="mainArea" ng-controller="thankYouController">
    <div class="user-line">
        <h3><i class="fa fa-smile-o" aria-hidden="true"><b> Thank you, {{studentusername}}</b></i></h3>
    </div>

    <div class="login">
		<p>
			Your vote has been recorded for this election.
		</p>
		<br>
		<p>
			Would you like to fill out an anonymous survey about the election?
		</p>
		<div>
			<button>Yes</button>
			<button onclick='window.location.href = "schools.php";'>No</button>
		</div>
		<p><?php echo $_POST['schoolid']; ?></p>
    </div>
    
</div>
</body>
<script src="angular/dependence/angular.min.js"></script>
<script src="angular/dependence/angular.min.js"></script>
<script src="angular/dependence/fontawsome.js"></script>
<script src="angular/dependence/angular-animate.min.js"></script>
<script src="angular/dependence/angular-aria.min.js"></script>
<script src="angular/dependence/angular-messages.min.js"></script>
<script src="angular/dependence/angular-material.min.js"></script>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/thankYouController.js"></script>


</html>
