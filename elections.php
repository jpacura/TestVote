<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Select Election</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/selectSchool.css">
    <script src="angular/dependence/angular.min.js"></script>
</head>
<body ng-app="VoteSys">
<div ng-init='schoolid="<?php echo $_POST['school']; ?>"'></div>
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Select Election</h3>
    </div>
    <a href="logout.php">Log Out</a>
</div>

<div class="mainArea" ng-controller="selectElectionController">
    <div class="user-line">
        <h2>Welcome, {{studentusername}}</h2>
        <h3>Elections for {{schoolname}}:</h3>
    </div>

    <div class="message">
        <div class="alert" id="tableerrortext" ng-if="isNoElections">{{errtext}}</div>
    </div>
    <div class="table-responsive">
        <table class="table" rules=all frame=border ng-if="isTableVisible">
            <thead>
            <tr>
                <th>Election Name</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="x in tabledata">
                <td>{{x.Name}}</td>
                <td>
                    <button ng-click="vote(x.ElectionID)">Vote!</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>


    <!-- INVISIBLE FORM FOR POST DATA -->
    <form method="post" id="gotopage" action="vote.php">
        <input type="hidden" id="electionidpost" name="electionid">
    </form>
</div>

</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/selectElectionController.js"></script>


</html>
