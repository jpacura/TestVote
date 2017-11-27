<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Create Election</title>
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
<div class="navi">
    <div class="title">
        <img src="../images/logoblack.svg">
        <h3 style="padding-top: 7px">Create Election</h3>
    </div>
    <a href="../logout.php">Log Out</a>
</div>

<div class="mainArea" ng-controller="adminCreateController">
	
	<div class="message">
        <div class="alert" id="tableerrortext" ng-if="isError">{{errtext}}</div>
    </div>
	
	<form novalidate class="input-field" name="newelection" method=post>
		<div>
			<p>
				<b>Election Name: </b>
				<input id="electionname" required type=text placeholder="Election Name">
			</p>
		</div>
		<br><br>
		
		<div id="questionDiv">
			<!-- THIS IS NOT AN EMPTY DIV, THE JAVASCRIPT ADDS STUFF TO IT -->
		</div>
		
		<br id="buttonSeparator">
		
		<button onclick="addquestion()">Add Question</button>
		<button ng-click="createelection(schoolid)" style="margin-left:20px">Create Election</button>
	</form>
	
	<!-- INVISIBLE FORM FOR POST DATA -->
    <form method="post" id="refresh" action="elections.php">
        <input type="hidden" id="schoolidrefresh" name="school">
    </form>
	
</div>



</body>
<script src="../angular/controllers/myApp.js"></script>
<script src="../angular/controllers/adminCreateController.js"></script>
</html>
