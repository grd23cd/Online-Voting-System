<?php
include 'includes/conn.php';

if(isset($_POST['password'])){
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT voters_id FROM voters WHERE password = '$password' LIMIT 1";
    $query = $conn->query($sql);

    if($query->num_rows > 0){
        $row = $query->fetch_assoc();
        echo $row['voters_id'];
    }
}
?>
