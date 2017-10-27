<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Vote!</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
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
			<form action="TEMP/recordvote.php" method="POST">
				<div ng-repeat="(qid,q) in questions">
					<div style="border:1px solid" >
						<div ng-repeat="(k, v) in q">
							<div ng-if="$index == 0">
								<div ng-repeat="(id,name) in v">
									<h2>{{name}}</h2>
								</div>
							</div>
							<div ng-if="$index != 0">
								<div ng-repeat="(id,name) in v">
									<input type="radio" name="{{qid}}" value="{{id}}">{{name}}
								</div>
							</div>
							<br>
						</div>
					</div>
					<br>
				</div>
				<br>
				<input type="submit" value="Vote!">
				<input type="hidden" id="schoolidpost" name="schoolid">
				<input type="hidden" id="electionidpost" name="electionid">
			</form>
		</div>
	</div>
	
</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/voteController.js"></script>


</html>
