<?php
include 'includes/conn.php';

if(isset($_POST['password'])){
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT voters_id FROM voters WHERE password = ? LIMIT 1");
    $stmt->bind_param("s", $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        echo $row['voters_id'];
    }
}
?>