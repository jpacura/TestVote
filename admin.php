<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrative Panel - TestVote</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
</head>
<body ng-app="VoteSys">
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Voting System</h3>
    </div>
    <button href="#">Log Out</button>
</div>

<div class="mainArea" ng-controller="loginController">
    <h1>Administrative Panel:</h1>
    
    

    <div class="linklist">
        <a href="admin/elections.php">Manage Elections</a><br/>
        <a href="admin/students.php">Manage Students</a><br/>
        <a href="admin/administrators.php">Manage Administrators</a>
    </div>

</div>
</body>

</html>
