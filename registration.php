<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  <!-- REQUIRED HTML HEADER -->
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"> <!-- REQUIRED HTML HEADER -->
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/navibar.css">
    <link rel="stylesheet" href="css/registration.css">
</head>
<body ng-app="VoteSys">
<div class="navi">
    <div class="title">
        <img src="images/logoblack.svg">
        <h3 style="padding-top: 7px">Voting System</h3>
    </div>
</div>
<div class="mainArea" ng-controller="registerController">
    <div class="user-line">
        <p>Create New User</p>
    </div>
    <p class="user-note">When you register a new user, you must provide valid email as user name and confirm your
        password.</p>
    <div class="registration">
        <div class="user-line">
            <p>LOGIN INFO</p>
        </div>
        <div class="panel-body">
            <form novalidate class="input-field" name="registrationForm" method=post>
                <!--form validation by angular-->
                <div class="form-group">
                    <!--send error message when username invalid-->
                    <div ng-show="registrationForm.username.$touched">
                        <div class="alert" ng-show="registrationForm.username.$error.required">
                            Username can't be empty.
                        </div>
                        <div class="alert" ng-show="registrationForm.username.$error.email">
                            Please input valid email address.
                        </div>
                    </div>
                    <!---->
                    <input type="email" name="username" id="username"
                           class="form-control" ng-model="user.name"
                           placeholder="username..." required/>
                </div>
                <div class="form-group">
                    <!--send error message when password invalid-->
                    <div class="alert"
                         ng-show="registrationForm.password.$touched &&
                         registrationForm.password.$error.required">
                        Password can't be empty.
                    </div>
                    <!---->
                    <input type="password" name="password" id="password"
                           class="form-control" ng-model="user.password"
                           placeholder="password..." required/>
                </div>
                <div class="form-group">
                    <!--send error message when password invalid-->
                    <div class="alert"
                         ng-show="registrationForm.confirm.$touched &&
                         registrationForm.confirm.$error.required">
                        Please confirm your password.
                    </div>
                    <!---->
                    <input type=password name="confirm" id="confirm"
                           class="form-control" ng-model="user.confirmPassword"
                           placeholder="confirm your password..." required/>
                </div>
                <button class="btn form-control" type="submit" value="Register"
                        ng-disabled="registrationForm.$invalid" ng-click="register()">Submit</button>
            </form>
        </div>
    </div>
</div>

<!-- START NEW PHP SCRIPT -->
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') # CHECK IF POST DATA EXISTS (ONLY IF SUBMIT BUTTON HAS BEEN PRESSED)
{
    $formerror = FALSE; # FLAG TO SEE IF THERE IS ERROR IN TYPED DATA

    if (empty($_POST['username'])) # MAKE SURE USERNAME IS NOT BLANK WHEN SUBMITTING
    {
        $formerror = TRUE; # SET ERROR FLAG TO TRUE
//        echo "<br />ERROR: Username is blank"; # PRINT ERROR MESSAGE THAT USERNAME IS BLANK
    }

    if (empty($_POST['password'])) # MAKE SURE PASSWORD IS NOT BLANK WHEN SUBMITTING
    {
        $formerror = TRUE;
//        echo "<br />ERROR: Password is blank"; # PRINT ERROR MESSAGE THAT PASSWORD IS BLANK
    }

    if (empty($_POST['confirm'])) # MAKE SURE CONFIRM IS NOT BLANK WHEN SUBMITTING
    {
        $formerror = TRUE;
//        echo "<br />ERROR: Confirm is blank"; # PRINT ERROR MESSAGE
    }

    if (!$formerror) # MAKE SURE ERROR FLAG IS NOT SET (MAKE SURE NO ERRORS)
    {
        if (!($_POST['password'] == $_POST['confirm'])) # MAKE SURE THE TWO PASSWORDS ARE THE SAME
        {
            $formerror = TRUE; # SET ERROR FLAG TO TRUE
//            echo "<br />ERROR: Passwords do not match"; # PRINT ERROR MESSAGE
        }

        if (!$formerror) # CHECK AGAIN FOR FORM ERRORS BEFORE TALKING TO MYSQL
        {
            $servername = "localhost"; # MYSQL IP ADDRESS
            $username = "testlogin1";  # MYSQL USERNAME
            $password = "12345";       # MYSQL PASSWORD
            $database = "testlogin1";  # MYSQL DATABASE
            $conn = new mysqli($servername, $username, $password, $database); # MAKE NEW MYSQL CONNECTION

            $uname = $_POST['username']; # GET ENTERED USERNAME FROM POST
            $checkifexists = "SELECT username FROM testlogin1 WHERE username = ?"; # MYSQL SELECT QUERY TO SEE IF USERNAME IS IN DATABASE
            # WE DONT NEED TO CHECK PASSWORD YET, WE WANT TO SEE IF THE ACCOUNT EXISTS FIRST
            $query = $conn->prepare($checkifexists); # PREPARE MYSQL CONNECTION
            $query->bind_param('s', $uname); # SAFELY BIND TYPED USERNAME TO MYSQL QUERY TO PREVENT HACKING
            $query->execute(); # EXECUTE MYSQL QUERY
            $doesexist = $query->get_result(); # GET MYSQL RESULT
            $numrows = $doesexist->num_rows; # COUNT NUMBER OF ROWS RETURNED BY MYSQL

            # THIS IS A REGISTRATION PAGE FOR NEW USERS, WE NEED TO MAKE SURE THE USER DOES NOT ALREADY EXIST (YOU CANT REGISTER TWICE)
            # IF IT DOES NOT EXIST, MYSQL WILL RETURN 0 ROWS

            if ($numrows == 0) # CHECK IF MYSQL RETURNS 0 ROWS
            {
                $uname = $_POST['username']; # GET ENTERED USERNAME FROM POST DATA
                $passwd = $_POST['password']; # GET ENTERED PASSWORD FROM POST DATA
                $adduser = "INSERT INTO testlogin1 (username, password) VALUES (?, ?)"; # MYSQL INSERT QUERY TO ADD NEW USERNAME AND PASSWORD TO DATBASE
                $query = $conn->prepare($adduser); # PREPARE MYSQL CONNECTION WITHOUT VARIABLES
                $query->bind_param("ss", $uname, $passwd); # SAFELY ADD ENTERED USERNAME AND PASSWORD TO MYSQL QUERY TO PREVENT HACKING
                $query->execute(); # EXECUTE MYSQL QUERY

                echo "<br />User Created!"; # PRINT USER CREATED MESSAGE
            } else {
                # IF THE USER ALREADY EXISTS DONT ALLOW A SECOND REGISTRATION
                echo "<br />ERROR: User already exists!"; # PRINT ERROR MESSAGE
            }
        }
    }
}
?> <!-- END OF PHP SCRIPT -->

</body>

<script>
    /*define a angular app here for further feature*/
    var myApp = angular.module('VoteSys', []);
    myApp.controller('registerController', ['$scope', '$http', function ($scope, $http) {
        $scope.register = function () {

            $scope.user.operation = "REGISTER";
            var RegisterData = JSON.stringify($scope.user);

            console.log("JSON sent to server:" + RegisterData);

            $http({
                method: 'POST',
                url: './mysql-users.php',
                data: RegisterData
            })
                .then(
                    function successCallback(response) {
                        console.log('server says:' + response.data);
                        $scope.temp = response.data;
                    },
                    function errorCallback(response) {
                        console.log(response.statusText);
                        console.log("HTTP status code:" + response.status);
                    })
        }
    }]);
</script>

</html>
