<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Vote!</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
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
	<div ng-init='schoolid="<?php echo $_POST['schoolid']; ?>"'></div>
	<div ng-init='electionid="<?php echo $_POST['electionid']; ?>"'></div>
	<div class="navi">
		<div class="title">
			<img src="images/logoblack.svg">
			<h3 style="padding-top: 7px">Vote!</h3>
			<h2><a href="logout.php">Log Out</a></h2>
		</div>
	</div>
	
	<div ng-controller="voteController">
		<h2>Welcome, {{studentusername}}</h2>
		<h3>{{electionname}} for {{schoolname}}:</h3>
		
		<p class="ng-hide" id="errortext" ng-show="isError">{{errtext}}</p>
		<div class="ng-hide" ng-show="isElectionVisible">
			<form method="POST">
				<div ng-repeat="(qid,q) in questions">
					<div style="border:1px solid; padding:15px; margin:15px" >
						<div ng-repeat="(k, v) in q">
							<div ng-if="$index == 0">
								<div ng-repeat="(id,name) in v">
									<b style="font-size: 18pt">{{name}}</b>
								</div>
							</div>
							<div ng-if="$index != 0">
								<div ng-repeat="(id,name) in v">
									<input style="margin-top: 15px" type="radio" ng-model="formdata[qid]" name="{{qid}}" value="{{id}}">{{name}}
								</div>
							</div>
						</div>
					</div>
				</div>
				<br>
				<button ng-click="submitvotes()">Vote!</button>
				<input type="hidden" id="schoolidpost" name="schoolid">
				<input type="hidden" id="electionidpost" name="electionid">
			</form>
		</div>
	</div>
	
</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/voteController.js"></script>


</html>
