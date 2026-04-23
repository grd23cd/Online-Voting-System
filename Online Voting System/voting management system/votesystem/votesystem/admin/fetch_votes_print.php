<?php
include 'includes/session.php';

$precinct = $_GET['precinct'];

$sql = "
  SELECT
    CONCAT(voters.firstname, ' ', voters.lastname) AS voter,
    GROUP_CONCAT(
      CONCAT(
        positions.description,
        ' - ',
        candidates.firstname,
        ' ',
        candidates.lastname
      )
      ORDER BY positions.priority ASC
      SEPARATOR ', '
    ) AS votes_cast
  FROM votes
  LEFT JOIN positions ON positions.id = votes.position_id
  LEFT JOIN candidates ON candidates.id = votes.candidate_id
  LEFT JOIN voters ON voters.id = votes.voters_id
  WHERE votes.precinct_number = '$precinct'
  GROUP BY votes.voters_id
  ORDER BY voter ASC
";

$query = $conn->query($sql);

$data = [];

while($row = $query->fetch_assoc()){
  $data[] = [
    'voter' => $row['voter'],
    'votes_cast' => $row['votes_cast']
  ];
}

echo json_encode($data);
?>
