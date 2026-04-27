<?php
include 'includes/session.php';
include 'includes/slugify.php';

if(isset($_POST['vote'])){

    $_SESSION['post'] = $_POST;

    if(!isset($_SESSION['precinct'])){
        $_SESSION['error'][] = 'Precinct not set. Please login again.';
        header('location: index.php');
        exit();
    }

    $precinct = (int)$_SESSION['precinct'];

    $sql = "SELECT * FROM positions";
    $query = $conn->query($sql);

    $error = false;

    while($row = $query->fetch_assoc()){

        $position = slugify($row['description']);
        $pos_id = $row['id'];

        // IF voter selected candidates
        if(isset($_POST[$position])){

            // MULTIPLE VOTE
            if($row['max_vote'] > 1){

                if(count($_POST[$position]) > $row['max_vote']){
                    $error = true;
                    $_SESSION['error'][] =
                        'You can only choose '.$row['max_vote'].' candidates for '.$row['description'];
                }
                else{
                    foreach($_POST[$position] as $values){

                        $conn->query(
                            "INSERT INTO votes (voters_id, candidate_id, position_id, precinct_number)
                             VALUES ('".$voter['id']."', '$values', '$pos_id', '$precinct')"
                        );
                    }
                }

            }
            // SINGLE VOTE
            else{

                $candidate = $_POST[$position];

                $conn->query(
                    "INSERT INTO votes (voters_id, candidate_id, position_id, precinct_number)
                     VALUES ('".$voter['id']."', '$candidate', '$pos_id', '$precinct')"
                );
            }

        }
        // ❗ NO VOTE → BLANK ENTRY
        else{

            $conn->query(
                "INSERT INTO votes (voters_id, candidate_id, position_id, precinct_number)
                 VALUES ('".$voter['id']."', NULL, '$pos_id', '$precinct')"
            );
        }
    }

    if(!$error){

        // MARK AS VOTED
        $conn->query("UPDATE voters SET voted = 1 WHERE id = '".$voter['id']."'");

        unset($_SESSION['post']);
        $_SESSION['success'] = 'Ballot Submitted Successfully';
    }

} else {
    $_SESSION['error'][] = 'Select candidates to vote first';
}

header('location: home.php');
?>
