<?php
include 'includes/session.php';

if(isset($_POST['add'])){

    // Limit to only 2 authorized users
    $count = $conn->query("SELECT COUNT(*) AS total FROM print_accounts")->fetch_assoc();

    if($count['total'] >= 2){
        $_SESSION['error'] = 'Only two authorized users are allowed.';
        header('location: print_accounts.php');
        exit();
    }

    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Check duplicate username
    $check = "SELECT * FROM print_accounts WHERE username='$username'";
    $result = $conn->query($check);

    if($result->num_rows > 0){

        $_SESSION['error'] = 'Username already exists.';

    }
    else{

        $password = $_POST['password'];

        $sql = "INSERT INTO print_accounts(fullname, username, password)
                VALUES('$fullname','$username','$password')";

        if($conn->query($sql)){
            $_SESSION['success'] = 'Authorized user added successfully.';
        }
        else{
            $_SESSION['error'] = $conn->error;
        }

    }

}
else{
    $_SESSION['error'] = 'Fill up the form first.';
}

header('location: print_accounts.php');
?>