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
<div class="navi">
    <div class="title">
        <img src="../images/logoblack.svg">
        <h3 style="padding-top: 7px">Election Results</h3>
    </div>
    <a href="../logout.php">Log Out</a>
</div>

<div class="mainArea" ng-controller="adminResultsController">
    <div class="user-line">
        <h3><i class="fa fa-smile-o" aria-hidden="true"><b> Welcome, {{studentusername}}</b></i></h3>
        <h4><i class="fa fa-university" aria-hidden="true"><b> Results for {{electionname}}</b></i></h4>
    </div>
	<button ng-click="gotoadminpanel(schoolid)">Return to Admin Panel</button>
</div>


    <!-- INVISIBLE FORM FOR POST DATA -->
    <form method="post" id="refresh" action="elections.php">
        <input type="hidden" id="schoolidrefresh" name="school">
    </form>
    
    <!-- INVISIBLE FORM FOR POST DATA -->
    <form method="post" id="gotopage" action="vote.php">
        <input type="hidden" id="electionidpost" name="electionid">
        <input type="hidden" id="schoolidpost" name="schoolid">
    </form>
</div>


</body>
<script src="../angular/controllers/myApp.js"></script>
<script src="../angular/controllers/adminResultsController.js"></script>
</html>