<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Select School</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/selectSchool.css">
    <script src="angular/dependence/angular.min.js"></script>
</head>
<body ng-app="VoteSys">
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Select School</h3>
    </div>
    <a href="logout.php">Log Out</a>
</div>

<div class="mainArea" ng-controller="selectSchoolController">
    <div class="user-line">
        <p>Welcome, {{studentusername}}</p>
    </div>

    <div class="message">
        <div class="alert" id="deleteerrortext" ng-if="isDeleteError">{{deleteerrtext}}</div>
        <div class="alert" id="tableerrortext" ng-if="isNotEnrolled">{{errtext}}</div>
    </div>

    <div class="table-responsive">
        <table class="table" rules=all frame=border ng-if="isTableVisible">
            <thead>
            <tr>
                <th>School Name</th>
                <th ng-if="x.Administrator == 0">Go to School</th>
                <th ng-if="x.Administrator == 1">Remove School</th>
                <th>Option</th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="x in tabledata">
                <td>{{x.Name}}</td>
                <td ng-if="x.Administrator == 0">
                    <button ng-click="election(x.Username)">Go to Elections</button>
                </td>
                <td ng-if="x.Administrator == 1">
                    <button ng-click="admin(x.Username)">Administrator Panel</button>
                </td>
                <td>
                    <button ng-click="removeschool(x.Username, x.Name)">Remove School</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="user-line">
        <button onclick="window.location.href='./enroll.php'">Enroll in a School</button>
        <button onclick="window.location.href='./registerschool.php'">Register a New School</button>

    </div>

    <!-- INVISIBLE FORM FOR POST DATA -->
    <form method="post" id="gotopage" action="">
        <input type="hidden" id="schoolnamepost" name="school">
    </form>
</div>

</body>

<script src="angular/controllers/myApp.js"></script>
<script src="angular/controllers/selectSchoolController.js"></script>


</html>
