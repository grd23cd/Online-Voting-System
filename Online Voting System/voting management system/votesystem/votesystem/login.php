<?php
session_start();
include 'includes/conn.php';

if (isset($_POST['login'])) {

    $voter = $_POST['voter'];
    $password = $_POST['password'];
    $precinct = $_POST['precinct'];

    // validate precinct
    if (empty($precinct)) {
        $_SESSION['error'] = 'Please select your precinct';
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

            $_SESSION['voter'] = $row['id'];

            // FORCE INTEGER PRECINCT
            $_SESSION['precinct'] = (int)$precinct;

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
