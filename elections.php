<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Select Election</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
</head>
<body ng-app="VoteSys">
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Select Election</h3>
    </div>
    <a href="logout.php">Log Out</a>
</div>

<div ng-controller="selectElectionController">
    <h2>Welcome, {{studentusername}}</h2>
    <p class="ng-hide" id="tableerrortext" ng-show="isNoElections">{{errtext}}</p>
    <table class="ng-hide" rules=all frame=border ng-show="isTableVisible">
        <tr>
            <th>School Name:</th>
            <th>Go to School:</th>
            <th>Remove School:</th>
        </tr>
        <tr ng-repeat="x in tabledata">
            <td>{{x.Name}}</td>

        </tr>
    </table>
    <p>{{temp}}</p>
</div>

</body>
<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/selectElectionController.js"></script>


</html>
