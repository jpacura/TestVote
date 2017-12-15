<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Learn2Vote - Exit Poll!</title>
    <link rel="stylesheet" href="css/angular-material.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/elections.css">
</head>
<body ng-app="VoteSys">
<div ng-init='schoolid="<?php echo $_POST['schoolid']; ?>"'></div>
<div ng-init='electionid="<?php echo $_POST['electionid']; ?>"'></div>
<div ng-init='voteid="<?php echo $_POST['voteid']; ?>"'></div>

<div class="navi">
    <div class="title">
        <img src="images/whiteLogo.png">
        <h3 style="padding-top: 7px">Learn2Vote</h3>
    </div>
    <a href="logout.php">Log Out</a>
</div>

<div class="mainArea" ng-controller="exitPollController">
    <div class="user-line">
        <h3><i class="fa fa-smile-o" aria-hidden="true"><b> Welcome, {{studentusername}}</b></i></h3>
        <h4><i class="fa fa-university" aria-hidden="true"><b>Exit Poll:</b></i></h4>
    </div>

    <div class="message">
        <div class="ng-hide alert" id="errortext" ng-show="isError">{{errtext}}</div>
    </div>
    <!--this the select option area begins -->
    <div class="mainArea">
        <form method="POST">
			<div class="table-responsive">
				<p>{{temp}}</p>
				<table class="table" rules=all frame=border>
					<thead>
						<tr><th>Question:</th><th>Answer:</th></tr>
					</thead>
					
					<tbody>
						
						<tr>
							<td>What type of school do you currently attend?</td>
							<td>
								<select ng-model="survey.EducationLevel">
									<option></option>
									<option>Elementary School</option>
									<option>Middle School</option>
									<option>High School</option>
									<option>College</option>
									<option>Other / None</option>
								</select>
							</td>
						</tr>
						
						<tr>
							<td>What is your current age?</td>
							<td><input ng-model="survey.Age" type="text"></td>
						</tr>
						
						<tr>
							<td>My chosen candidate is: </td>
							<td>
								<select ng-model="survey.CandidateAge">
									<option></option>
									<option>The same age as me</option>
									<option>Older than me</option>
									<option>Younger than me</option>
									<option>Unknown</option>
								</select>
							</td>
						</tr>
						
						<tr>
							<td>What is your race or ethnicity?</td>
							<td>
								<select ng-model="survey.Ethnicity">
									<option></option>
									<option>White</option>
									<option>Black or African American</option>
									<option>American Indian or Alaska Native</option>
									<option>Asian</option>
									<option>Native Hawaiian or Other Pacific Islander</option>
									<option>Other / Prefer not to Answer</option>
								</select>
							</td>
						</tr>
						
						<tr>
							<td>What is the race or ethnicity of your chosen candidate?</td>
							<td>
								<select ng-model="survey.CandidateEthnicity">
									<option></option>
									<option>White</option>
									<option>Black or African American</option>
									<option>American Indian or Alaska Native</option>
									<option>Asian</option>
									<option>Native Hawaiian or Other Pacific Islander</option>
									<option>Other / Prefer not to Answer</option>
								</select>
							</td>
						</tr>
						
						<tr>
							<td>How well do you know the candidate you voted for?</td>
							<td>
								<select ng-model="survey.Relation">
									<option></option>
									<option>Very Well</option>
									<option>Somewhat Well</option>
									<option>Well</option>
									<option>Not Really</option>
									<option>Not at All</option>
								</select>
							</td>
						</tr>
						
						<tr>
							<td>Is there any additional information that you wish to provide?</td>
							<td><textarea ng-model="survey.Freeform"></textarea></td>
						</tr>
						
						<tr>
							<td>Submit Poll:</td>
							<td><button ng-click="submitvotes()">Submit</button></td>
						</tr>
					</tbody>
				</table>
            </div>
            <br>
        </form>
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
<script src="angular/controllers/exitPollController.js"></script>


</html>
