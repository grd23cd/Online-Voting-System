<?php
	include 'includes/session.php';

	/**
	 * Generates a unique code for a new voter by checking the database
	 * and regenerating if a collision is found.
	 */
	function generateUniqueVoterCode($conn, $length = 6) {
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$charLength = strlen($characters);

		do {
			$code = '';
			for ($i = 0; $i < $length; $i++) {
				$code .= $characters[random_int(0, $charLength - 1)];
			}

			$stmt = $conn->prepare("SELECT id FROM voters WHERE code = ? LIMIT 1");
			$stmt->bind_param("s", $code);
			$stmt->execute();
			$stmt->store_result();
			$exists = $stmt->num_rows > 0;
			$stmt->close();

		} while ($exists);

		return $code;
	}

	if(isset($_POST['add'])){
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];

		$password = $_POST['password'];

		$filename = $_FILES['photo']['name'];
		if(!empty($filename)){
			move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
		}

		// voters id as LASTNAME, FIRSTNAME
		$voter = strtoupper($lastname . ', ' . $firstname);

		// generate a unique code, checked against the database
		$code = generateUniqueVoterCode($conn);

		$stmt = $conn->prepare("INSERT INTO voters (voters_id, password, firstname, lastname, photo, code) 
		        VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssss", $voter, $password, $firstname, $lastname, $filename, $code);

		if($stmt->execute()){
			$_SESSION['success'] = 'Voter added successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
		$stmt->close();
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: voters.php');
?>