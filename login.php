<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  <!-- REQUIRED HTML HEADER -->
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"> <!-- REQUIRED HTML HEADER -->
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
</head>
<body ng-app="myApp">
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Voting System</h3>
    </div>
</div>
<div class="mainArea" ng-controller="loginController">
    <div class="login">
        <div class="panel-body">
            <img src="images/logo.svg">
            <form novalidate class="input-field" name="loginForm" method=post>
                <!--form validation by angular-->
                <div class="form-group">
                    <!--send error message when username invalid-->
                    <div ng-show="loginForm.username.$touched">
                        <div class="alert" ng-show="loginForm.username.$error.required">
                            Username can't be empty.
                        </div>
                        <div class="alert" ng-show="loginForm.username.$error.email">
                            Please input valid email address.
                        </div>
                    </div>
                    <!---->
                    <input type="email" name="username" class="form-control"
                           ng-model="user.name"
                           placeholder="username" required>
                </div>
                <div class="form-group">
                    <!--send error message when password invalid-->
                    <div class="alert"
                         ng-show="loginForm.password.$touched &&
                         loginForm.password.$error.required">
                        Password can't be empty.
                    </div>
                    <!---->
                    <input type="password" name="password" class="form-control"
                           ng-model="user.password"
                           placeholder="password" required>
                </div>
                <button class="btn form-control" type=submit value="Login">Login</button>
            </form>
            <div class="registerLink">
                <span><input type="checkbox">Remember me</span>
                <a href="registration.php">Register here</a>
            </div>
        </div>
    </div>
</div>


<?php
# input validation on password and username
$username = new DOMElement('username');
$doc = new DOMDocument();
$alert = $doc->createElement("alert");
$alert->appendChild(new DOMText(' ERROR: Username is blank'));


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $formerror = FALSE;

    if (empty($_POST['username'])) # MAKE SURE USERNAME IS NOT EMPTY
    {
        $formerror = TRUE;
//        echo "<div class=\"alert\">ERROR: Password is blank</div>";
    }


    if (empty($_POST['password'])) # MAKE SURE PASSWORD IS NOT EMPTY
    {
        $formerror = TRUE;
//        echo "<div class=\"alert\">ERROR: Password is blank</div>";
    }

    #post request, current on semi data.

    if (!$formerror) # MAKE SURE NO ERRORS
    {
        $servername = "localhost";
        $username = "test1"; #semi user
        $password = "123";      #semi password
        $database = "testlogin1";
        $conn = new mysqli($servername, $username, $password, $database);

        $uname = $_POST['username'];
        $checkifexists = "SELECT username FROM testlogin1 WHERE username = ?"; # SELECT QUERY TO MAKE SURE USER EXISTS (CANT LOG INTO ACCOUNT THAT DOESNT EXIST)
        $query = $conn->prepare($checkifexists);
        $query->bind_param('s', $uname);
        $query->execute();
        $doesexist = $query->get_result();
        $numrows = $doesexist->num_rows;

        if ($numrows == 0) {
            echo "<br />ERROR: User does not exist!";
        } else {
            $getpasswd = "SELECT password FROM testlogin1 WHERE username = ?"; # SELECT QUERY TO GET PASSWORD FROM DATABASE
            $query = $conn->prepare($getpasswd);
            $query->bind_param('s', $uname);
            $query->execute();
            $query->bind_result($dbpwd);
            $typedpwd = $_POST['password']; # GET PASSWORD THAT USER TYPED IN
            $query->fetch();
            if ($typedpwd == $dbpwd) # CHECK IF TYPED PASSWORD MATCHES THE ONE STORED IN THE DATABASE
            {
                echo "<br />Login Successful!";
            } else {
                echo "<br />Password Incorrect!";
            }
        }
    }
}
?>

</body>

<script>
    /*define a angular app here for further feature*/
    var myApp = angular.module('myApp', []);
    myApp.controller('loginController', ['$scope', function ($scope) {
//        leave an empty controller here for further building
    }]);
</script>

</html>
