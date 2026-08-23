<?php
session_start();
include 'includes/conn.php';

if (isset($_POST['login'])) {

    $voter       = $_POST['voter'];
    $password    = $_POST['password'];
    $unique_code = $_POST['unique_code'];
    $precinct    = $_POST['precinct'];

    // validate precinct
    if (empty($precinct)) {
        $_SESSION['error'] = 'Please select your precinct';
        header('location: index.php');
        exit();
    }

    // validate unique code presence
    if (empty($unique_code)) {
        $_SESSION['error'] = 'Please enter your unique code';
        header('location: index.php');
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM voters WHERE voters_id = ?");
    $stmt->bind_param("s", $voter);
    $stmt->execute();
    $query = $stmt->get_result();

    if ($query->num_rows < 1) {

        $_SESSION['error'] = 'Cannot find voter with the ID';

    } else {

        $row = $query->fetch_assoc();

        if (strcasecmp($password, $row['password']) === 0) {

            // check unique code (case-sensitive, exact match)
            if (hash_equals((string)$row['code'], (string)$unique_code)) {

                $_SESSION['voter'] = $row['id'];

                // FORCE INTEGER PRECINCT
                $_SESSION['precinct'] = (int)$precinct;

            } else {
                $_SESSION['error'] = 'Incorrect unique code';
            }

        } else {
            $_SESSION['error'] = 'Incorrect password';
        }
    }

    $stmt->close();

} else {
    $_SESSION['error'] = 'Input voter credentials first';
}

header('location: index.php');
exit();
?>