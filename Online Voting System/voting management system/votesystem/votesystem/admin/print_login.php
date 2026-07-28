<?php
include 'includes/session.php';

if(isset($_SESSION['print_auth'])){
    header('location: print.php');
    exit();
}

if(isset($_POST['login'])){

    $username1 = mysqli_real_escape_string($conn, $_POST['username1']);
    $password1 = mysqli_real_escape_string($conn, $_POST['password1']);

    $username2 = mysqli_real_escape_string($conn, $_POST['username2']);
    $password2 = mysqli_real_escape_string($conn, $_POST['password2']);

    if($username1 == $username2){
        $_SESSION['error'] = 'Authorized User 1 and Authorized User 2 must be different.';
    }
    else{

        $sql1 = "SELECT * FROM print_accounts WHERE username='$username1'";
        $query1 = $conn->query($sql1);

        $sql2 = "SELECT * FROM print_accounts WHERE username='$username2'";
        $query2 = $conn->query($sql2);

        if($query1->num_rows < 1 || $query2->num_rows < 1){
            $_SESSION['error'] = 'One or both authorized users do not exist.';
        }
        else{

            $user1 = $query1->fetch_assoc();
            $user2 = $query2->fetch_assoc();

            if(
                $password1 == $user1['password'] &&
                $password2 == $user2['password']
            ){

                $_SESSION['print_auth'] = true;

                $_SESSION['print_user1'] = $user1['fullname'];
                $_SESSION['print_user2'] = $user2['fullname'];

                header('location: print.php');
                exit();

            }
            else{
                $_SESSION['error'] = 'Invalid username or password.';
            }

        }

    }

}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>Authorized Print Login</title>

<link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="../plugins/iCheck/square/blue.css">

</head>

<body class="hold-transition login-page">

<div class="login-box">

    <div class="login-logo">
        <b>Authorized Print Login</b>
    </div>

    <div class="login-box-body">

        <p class="login-box-msg">
            Both authorized users must log in before printing.
        </p>

        <?php

        if(isset($_SESSION['error'])){

            echo "
            <div class='alert alert-danger'>
                ".$_SESSION['error']."
            </div>
            ";

            unset($_SESSION['error']);

        }

        ?>

        <form method="POST">

            <h4><b>Authorized User 1</b></h4>

            <div class="form-group has-feedback">

                <input
                    type="text"
                    class="form-control"
                    name="username1"
                    placeholder="Username"
                    required>

                <span class="glyphicon glyphicon-user form-control-feedback"></span>

            </div>

            <div class="form-group has-feedback">

                <input
                    type="password"
                    class="form-control"
                    name="password1"
                    placeholder="Password"
                    required>

                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

            </div>

            <hr>

            <h4><b>Authorized User 2</b></h4>

            <div class="form-group has-feedback">

                <input
                    type="text"
                    class="form-control"
                    name="username2"
                    placeholder="Username"
                    required>

                <span class="glyphicon glyphicon-user form-control-feedback"></span>

            </div>

            <div class="form-group has-feedback">

                <input
                    type="password"
                    class="form-control"
                    name="password2"
                    placeholder="Password"
                    required>

                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

            </div>

            <br>

            <button
                type="submit"
                name="login"
                class="btn btn-primary btn-block btn-flat">

                Continue to Print

            </button>

        </form>

    </div>

</div>

<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

</body>
</html>