<?php
include 'includes/session.php';

$precinct = $_GET['precinct'];

$sql = "
  SELECT
    CONCAT(v.firstname, ' ', v.lastname) AS voter,
    COALESCE(
      GROUP_CONCAT(
        CONCAT(
          p.description,
          ' - ',
          c.firstname,
          ' ',
          c.lastname
        )
        ORDER BY p.priority ASC
        SEPARATOR ', '
      ),
      'Abstained'
    ) AS votes_cast
  FROM votes vt
  LEFT JOIN voters v ON v.id = vt.voters_id
  LEFT JOIN positions p ON p.id = vt.position_id
  LEFT JOIN candidates c ON c.id = vt.candidate_id
  WHERE vt.precinct_number = '$precinct'
  GROUP BY vt.voters_id, v.firstname, v.lastname
  ORDER BY voter ASC
";

$query = $conn->query($sql);

// Handle SQL error (prevents fatal crash)
if(!$query){
  echo json_encode([
    [
      'voter' => 'SQL Error',
      'votes_cast' => $conn->error
    ]
  ]);
  exit;
}

$data = [];

while($row = $query->fetch_assoc()){
  $data[] = [
    'voter' => $row['voter'],
    'votes_cast' => $row['votes_cast']
  ];
}

echo json_encode($data);
?>