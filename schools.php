<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Select School</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/selectSchool.css">
    <script src="angular/dependence/angular.min.js"></script>
    <link rel="stylesheet" href="css/angular-material.min.css">
    <script src="angular/dependence/angular.min.js"></script>
    <script src="angular/dependence/fontawsome.js"></script>
    <script src="angular/dependence/angular-animate.min.js"></script>
    <script src="angular/dependence/angular-aria.min.js"></script>
    <script src="angular/dependence/angular-messages.min.js"></script>
    <script src="angular/dependence/angular-material.min.js"></script>
</head>
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
        <h3><i class="fa fa-smile-o" aria-hidden="true"><b> Welcome, {{studentusername}}</b></i></h3>
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
                <th>Go to School</th>
                <th>Remove School</th>
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
        <button onclick="window.location.href='./enroll.php'">
            <i class="fa fa-user-plus" aria-hidden="true"><b> Enroll in a School</b></i></button>
        <button onclick="window.location.href='./registerschool.php'">
            <i class="fa fa-university" aria-hidden="true"><b> Register a New School</b></i></button>

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
