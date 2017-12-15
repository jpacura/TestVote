<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Learn2Vote</title>
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
        <img src="images/whiteLogo.png">
        <h3 style="padding-top: 7px">Learn2Vote</h3>
    </div>
    <a href="login.php">Login</a>
</div>
<div class="mainArea" ng-controller="indexController">
    
    <h1 style="text-align: center; font-size: 300%">Welcome to Learn2Vote!</h1>
    
    <p style="text-align: center; margin-left: 20%; margin-right: 20%; font-size: 100%">
		Learn2Vote is a new online voting system designed for schools to use.
		Schools can create an account quickly and easily to create elections for their students.
		Students can easily vote in the elections that their school creates, and the school can easily
		access the results of their elections. 
	</p>
	
	<p style="text-align: center; margin-left: 20%; margin-right: 20%; margin-top: 40px; font-size: 100%">
		<a href="login.php">Click Here</a> to begin using Learn2Vote
	</p>
    
</div>


</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/indexController.js"></script>


</html>
