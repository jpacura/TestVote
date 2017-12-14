<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Election Results</title>
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
        <h3 style="padding-top: 7px">Election Results</h3>
    </div>
    <a href="../logout.php">Log Out</a>
</div>

<div class="mainArea" ng-controller="adminResultsController">
    <div class="user-line">
        <h3><i class="fa fa-smile-o" aria-hidden="true"><b> Welcome, {{studentusername}}</b></i></h3>
        <h4><i class="fa fa-university" aria-hidden="true"><b> Results for {{electionname}}</b></i></h4>
    </div>
    
    <div>
		<h2>Election Results:</h2>
		
		<div ng-repeat="(questionname,answers) in resultsdata">
			<div class="table-responsive">
				<table class="table" rules=all frame=border>
					<thead>
						<tr>
							<th>{{questionname}}:</th>
							<th>Votes:</th>
						</tr>
					</thead>
					
					<tbody>
						<tr ng-repeat="(response,votes) in answers">
							<td>{{response}}</td>
							<td>{{votes}}</td>
						</tr>
					</tbody>
					
				</table>
			</div>
		</div>
		
    </div>
    
    <br>
    
    <div>
		<h2>Voters:</h2>
		
		<div class="message">
			<div class="alert" id="tableerrortext" ng-if="isNoVoters">{{errtext}}</div>
		</div>
		
		<div class="table-responsive ng-hide" ng-show="isUsersTableVisible">
		
			<table class="table" rules=all frame=border>
				<thead>
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>Delete Vote</th>
					</tr>
				</thead>
				
				<tbody>
					<tr ng-repeat="users in voterdata">
						<td>{{users.Name}}</td>
						<td>{{users.Email}}</td>
						
						<td>
							<button ng-click="deletevote(users.UserID, electionid)">Delete Vote</button>
						</td>
						
					</tr>
				</tbody>
			
			</table>
		
		</div>
    </div>
    
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
<script src="../angular/controllers/adminResultsController.js"></script>
</html>
