<?php
include 'includes/session.php';

if(isset($_POST['edit'])){

    $id = $_POST['id'];
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['password']);

    // Fetch the account so we can check the current password
    $check_id = "SELECT * FROM print_accounts WHERE id = '$id'";
    $result = $conn->query($check_id);

    if($result->num_rows == 0){

        $_SESSION['error'] = 'Account not found.';

    }
    else{

        $account = $result->fetch_assoc();

        // Verify current password matches what's on file
        if($current_password !== $account['password']){

            $_SESSION['error'] = 'Current password is incorrect.';

        }
        else{

            // Check if another account already uses this username
            $check_username = "SELECT * FROM print_accounts
                                WHERE username = '$username'
                                AND id != '$id'";

            $username_check = $conn->query($check_username);

            if($username_check->num_rows > 0){

                $_SESSION['error'] = 'Username already exists.';

            }
            else{

                // Keep current password if new password left blank
                if(empty($new_password)){

                    $sql = "UPDATE print_accounts
                            SET fullname = '$fullname',
                                username = '$username'
                            WHERE id = '$id'";

                }
                else{

                    $new_password = mysqli_real_escape_string($conn, $new_password);

                    $sql = "UPDATE print_accounts
                            SET fullname = '$fullname',
                                username = '$username',
                                password = '$new_password'
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

    }

}
else{

    $_SESSION['error'] = 'Fill up the edit form first.';

}

header('location: print_accounts.php');
?>