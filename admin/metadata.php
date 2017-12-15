<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Learn2Vote - Exit Poll Results</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/navibar.css">
    <link rel="stylesheet" href="../css/elections.css">
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
<div ng-init='electionid="<?php echo $_POST['electionid']; ?>"'></div>
<div class="navi">
    <div class="title">
        <img src="../images/whiteLogo.png">
        <h3 style="padding-top: 7px">Exit Poll Results</h3>
    </div>
    <a href="../logout.php">Log Out</a>
</div>

<div class="mainArea" ng-controller="adminMetadataController">
    <div class="user-line">
        <h3><i class="fa fa-smile-o" aria-hidden="true"><b> Welcome, {{studentusername}}</b></i></h3>
        <h4><i class="fa fa-university" aria-hidden="true"><b> Exit Poll Results</b></i></h4>
    </div>
    
    <div>
		<h2>Exit Poll Results:</h2>
		
		<table class="table" rules=all frame=border>
			<thead>
				<tr>
					<th>Education Level:</th>
					<th>Age:</th>
					<th>Age of Candidate:</th>
					<th>Ethnicity:</th>
					<th>Candidate Ethnicity:</th>
					<th>Relation to Candidate:</th>
					<th>Freeform Response</th>
				</tr>
			</thead>
			
			<tbody>
				<div>
					<tr ng-repeat="votes in tabledata">
						<td>{{votes.EducationLevel}}</td>
						<td>{{votes.Age}}</td>
						<td>{{votes.CandidateAge}}</td>
						<td>{{votes.Ethnicity}}</td>
						<td>{{votes.CandidateEthnicity}}</td>
						<td>{{votes.Relation}}</td>
						<td>{{votes.Freeform}}</td>
					</tr>
				</div>
			</tbody>
		</table>
    </div>
    
    <br>
    
	<button ng-click="gotoadminpanel(schoolid)">Return to Elections</button>
</div>


    <!-- INVISIBLE FORM FOR POST DATA -->
    <form method="post" id="refresh" action="results.php">
        <input type="hidden" id="schoolidrefresh" name="school">
        <input type="hidden" id="electionidrefresh" name="electionid">
    </form>
</div>


</body>
<script src="../angular/controllers/myApp.js"></script>
<script src="../angular/controllers/adminMetadataController.js"></script>
</html>
