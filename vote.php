<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>TestVote - Vote!</title>
    <link rel="stylesheet" href="css/angular-material.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/elections.css">
</head>
<body ng-app="VoteSys">
<div ng-init='schoolid="<?php echo $_POST['schoolid']; ?>"'></div>
<div ng-init='electionid="<?php echo $_POST['electionid']; ?>"'></div>

<div class="navi">
    <div class="title">
        <img src="images/whiteLogo.png">
        <h3 style="padding-top: 7px">Learn2Vote</h3>
    </div>
    <a href="logout.php">Log Out</a>
</div>

<div class="mainArea" ng-controller="voteController">
    <div class="user-line">
        <h3><i class="fa fa-smile-o" aria-hidden="true"><b> Welcome, {{studentusername}}</b></i></h3>
        <h4><i class="fa fa-university" aria-hidden="true"><b>{{electionname}} for {{schoolname}}:</b></i></h4>
    </div>

    <div class="message">
        <div class="ng-hide alert" id="errortext" ng-show="isError">{{errtext}}</div>
    </div>
    <!--this the select option area begins -->
    <div class="ng-hide mainArea" ng-show="isElectionVisible">
        <form method="POST">

            <div ng-repeat="(qid,q) in questions">
                <div class="questionForm">
                    <div ng-repeat="(k, v) in q">

                        <div ng-if="$index == 0">
                            <div ng-repeat="(id,name) in v">
                                <h2>{{name}}</h2>
                            </div>
                        </div>

                        <div ng-if="$index != 0">
                            <div ng-repeat="(id,name) in v">
                                <input type="radio" ng-model="formdata[qid]" name="{{qid}}" value="{{id}}">{{name}}
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
    
    <!-- INVISIBLE FORM FOR POST DATA -->
    <form method="post" id="gotopage" action="thankyou.php">
        <input type="hidden" id="electionidpost" name="electionid">
        <input type="hidden" id="schoolidpost" name="schoolid">
        <input type="hidden" id="voteidpost" name="voteid">
    </form>
    
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
<script src="angular/controllers/voteController.js"></script>


</html>
