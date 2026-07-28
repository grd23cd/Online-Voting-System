<?php
include 'includes/session.php';

unset($_SESSION['print_auth']);
unset($_SESSION['print_user1']);
unset($_SESSION['print_user2']);

header('location: home.php');
exit();
?>