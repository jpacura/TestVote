<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  <!-- REQUIRED HTML HEADER -->
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"> <!-- REQUIRED HTML HEADER -->
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
</head>
<body ng-app="VoteSys">
	<div class="navi">
		<div class="title">
			<img src="images/logoblack.svg">
			<h3 style="padding-top: 7px">Voting System</h3>
		</div>
	</div>
	
	<div ng-controller="selectSchoolController">
		<button class="btn form-control" type=button value="TEST"
			ng-click="listSchools()">TEST
		</button>
		
		<p class="ng-hide" id="tableerrortext" ng-show="isNotEnrolled">{{errtext}}</p>
		<table class="ng-hide" rules=all frame=border ng-show="isTableVisible">
			<tr><th>School Name:</th><th>Go to School:</th><th>Remove School:</th></tr>
			<tr ng-repeat="x in tabledata">
				<td>{{x.Name}}</td>
				<td ng-if="x.Administrator == 0"><a ng-href="elections.php?sid={{x.SchoolID}}">Go to Elections</a></td>
				<td ng-if="x.Administrator == 1"><a ng-href="admin.php?sid={{x.SchoolID}}">Administrator Panel</a></td>
				<td><a ng-href="removeschool.php?sid={{x.SchoolID}}">Remove School</a></td>
				
			</tr>
		</table>
	</div>
	
</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/selectSchoolController.js"></script>


</html>
