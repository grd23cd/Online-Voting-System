<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$password = $_POST['password']; // plain text passbook

		// recompute voters_id as LASTNAME, FIRSTNAME
		$voter = strtoupper($lastname . ', ' . $firstname);

		$sql = "UPDATE voters 
		        SET voters_id = '$voter',
		            firstname = '$firstname', 
		            lastname = '$lastname', 
		            password = '$password' 
		        WHERE id = '$id'";

		if($conn->query($sql)){
			$_SESSION['success'] = 'Voter updated successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: voters.php');
?>
