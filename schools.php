<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Select School</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
</head>
<body ng-app="VoteSys">
	<div class="navi">
		<div class="title">
			<img src="images/logoblack.svg">
			<h3 style="padding-top: 7px">Select School</h3>
			<h2><a href="logout.php">Log Out</a></h2>
		</div>
	</div>
	
	<div ng-controller="selectSchoolController">
		<h2>Welcome, {{studentusername}}</h2>
		<p class="ng-hide" id="deleteerrortext" ng-show="isDeleteError">{{deleteerrtext}}</p>
		<p class="ng-hide" id="tableerrortext" ng-show="isNotEnrolled">{{errtext}}</p>
		<table class="ng-hide" rules=all frame=border ng-show="isTableVisible">
			<tr><th>School Name:</th><th>Go to School:</th><th>Remove School:</th></tr>
			<tr ng-repeat="x in tabledata">
				<td>{{x.Name}}</td>
				<td ng-if="x.Administrator == 0"><a href="" ng-click="election(x.Username)">Go to Elections</a></td>
				<td ng-if="x.Administrator == 1"><a href="" ng-click="admin(x.Username)">Administrator Panel</a></td>
				<td><a href="" ng-click="removeschool(x.Username, x.Name)">Remove School</a></td>
				
			</tr>
		</table>
		<br>
		<button onclick="window.location.href='./enroll.php'">Enroll in a School</button>
		
		<!-- INVISIBLE FORM FOR POST DATA -->
		<form method="post" id="gotopage" action="">
			<input type="hidden" id="schoolnamepost" name="school">
		</form>
	</div>
	
</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/selectSchoolController.js"></script>


</html>
