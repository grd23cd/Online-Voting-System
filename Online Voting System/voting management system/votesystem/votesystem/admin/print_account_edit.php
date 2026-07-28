<?php
include 'includes/session.php';

if(isset($_POST['edit'])){

    $id = $_POST['id'];
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = trim($_POST['password']);

    // Check if another account already uses this username
    $check = "SELECT * FROM print_accounts
              WHERE username = '$username'
              AND id != '$id'";

    $result = $conn->query($check);

    if($result->num_rows > 0){

        $_SESSION['error'] = 'Username already exists.';

    }
    else{

        // Keep current password if left blank
        if(empty($password)){

            $sql = "UPDATE print_accounts
                    SET fullname = '$fullname',
                        username = '$username'
                    WHERE id = '$id'";

        }
        else{

            $password = mysqli_real_escape_string($conn, $_POST['password']);

            $sql = "UPDATE print_accounts
                    SET fullname = '$fullname',
                        username = '$username',
                        password = '$password'
                    WHERE id = '$id'";

        }

        if($conn->query($sql)){
            $_SESSION['success'] = 'Authorized user updated successfully.';
        }
        else{
            $_SESSION['error'] = $conn->error;
        }

    }

}
else{

    $_SESSION['error'] = 'Fill up the edit form first.';

}

header('location: print_accounts.php');
?>