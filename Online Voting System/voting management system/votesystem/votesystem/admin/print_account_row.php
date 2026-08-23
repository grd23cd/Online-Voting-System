<?php
include 'includes/session.php';

header('Content-Type: application/json');

if(isset($_POST['id'])){

    $id = $_POST['id'];

    $sql = "SELECT id, fullname, username FROM print_accounts WHERE id = '$id'";
    $query = $conn->query($sql);

    if($query->num_rows > 0){
        $row = $query->fetch_assoc();
        echo json_encode($row);
    }
}
?>