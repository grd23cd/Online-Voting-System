<?php
include 'includes/session.php';

$precinct = $_GET['precinct'];

$sql = "
  SELECT 
    positions.description AS position,
    CONCAT(candidates.firstname, ' ', candidates.lastname) AS candidate,
    CONCAT(voters.firstname, ' ', voters.lastname) AS voter
  FROM votes
  LEFT JOIN positions ON positions.id = votes.position_id
  LEFT JOIN candidates ON candidates.id = votes.candidate_id
  LEFT JOIN voters ON voters.id = votes.voters_id
  WHERE votes.precinct_number = '$precinct'
  ORDER BY positions.priority ASC
";

$query = $conn->query($sql);

$data = [];

while($row = $query->fetch_assoc()){
  $data[] = $row;
}

echo json_encode($data);
?>
