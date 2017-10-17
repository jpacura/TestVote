<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  <!-- REQUIRED HTML HEADER -->
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"> <!-- REQUIRED HTML HEADER -->
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
		<p class="ng-hide" id="deleteerrortext" ng-show="isDeleteError">{{deleteerrtext}}</p>
		<p class="ng-hide" id="tableerrortext" ng-show="isNotEnrolled">{{errtext}}</p>
		<table class="ng-hide" rules=all frame=border ng-show="isTableVisible">
			<tr><th>School Name:</th><th>Go to School:</th><th>Remove School:</th></tr>
			<tr ng-repeat="x in tabledata">
				<td>{{x.Name}}</td>
				<td ng-if="x.Administrator == 0"><a href="" ng-click="gotoschool(x.SchoolID)">Go to Elections</a></td>
				<td ng-if="x.Administrator == 1"><a href="" ng-click="gotoadmin(x.SchoolID)">Administrator Panel</a></td>
				<td><a href="" ng-click="removeschool(x.SchoolID)">Remove School</a></td>
				
			</tr>
		</table>
	</div>
	
</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/selectSchoolController.js"></script>


</html>
