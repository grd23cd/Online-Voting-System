<?php
include 'includes/session.php';

/* 1. Delete all votes */
$sql = "DELETE FROM votes";
if($conn->query($sql)){

    /* 2. Reset all voters to not voted */
    $sql2 = "UPDATE voters SET voted = 0";
    $conn->query($sql2);

    $_SESSION['success'] = "Votes reset successfully";
}
else{
    $_SESSION['error'] = "Something went wrong in resetting";
}

header('location: votes.php');
?>
