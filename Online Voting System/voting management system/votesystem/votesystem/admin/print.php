<?php
include 'includes/session.php';

if(
    !isset($_SESSION['print_auth']) ||
    !isset($_SESSION['print_user1']) ||
    !isset($_SESSION['print_user2'])
){
    header('location: print_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Official Election Report</title>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
}

h1, h2, h3 {
    text-align: center;
    margin: 10px 0;
    page-break-after: avoid;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 40px;
    font-size: 12px;
    table-layout: fixed;
    word-wrap: break-word;
}

table, th, td {
    border: 1px solid black;
}

th, td {
    padding: 6px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

.summary-table td {
    font-weight: bold;
}

.page-break {
    page-break-after: always;
}

@page {
    size: letter;
    margin: 1in;
}

@media print {
    button {
        display: none !important;
    }
}
</style>
</head>

<body>

<h1>Official Election Report</h1>

<p style="text-align:center; margin-bottom:25px;">
    <strong>Authorized User 1:</strong>
    <?php echo $_SESSION['print_user1']; ?>
    <br>
    <strong>Authorized User 2:</strong>
    <?php echo $_SESSION['print_user2']; ?>
</p>

<button onclick="window.open(window.location.href, '_blank', 'width=1000,height=800')">
    Print / Save as PDF
</button>

<h2>Summary</h2>

<?php
$total_voters = $conn->query("SELECT COUNT(*) AS total_voters FROM voters")->fetch_assoc()['total_voters'];
$voters_voted = $conn->query("SELECT COUNT(DISTINCT voters_id) AS voted_count FROM votes")->fetch_assoc()['voted_count'];
$total_candidates = $conn->query("SELECT COUNT(*) AS total_candidates FROM candidates")->fetch_assoc()['total_candidates'];
$total_votes = $conn->query("SELECT COUNT(*) AS total_votes FROM votes")->fetch_assoc()['total_votes'];
?>

<table class="summary-table">
    <tbody>
        <tr>
            <td>Total Registered Voters</td>
            <td><?= $total_voters ?></td>
        </tr>
        <tr>
            <td>Voters Who Voted</td>
            <td><?= $voters_voted ?></td>
        </tr>
        <tr>
            <td>Total Candidates</td>
            <td><?= $total_candidates ?></td>
        </tr>
        <tr>
            <td>Total Votes Cast</td>
            <td><?= $total_votes ?></td>
        </tr>
    </tbody>
</table>

<div class="page-break"></div>

<h2>Voters Who Voted</h2>

<?php
$sql = "
    SELECT DISTINCT v.id, v.firstname, v.lastname, v.password
    FROM voters v
    INNER JOIN votes vt ON vt.voters_id = v.id
    ORDER BY v.lastname ASC
";

$query = $conn->query($sql);

$rows = [];
while ($row = $query->fetch_assoc()) {
    $rows[] = $row;
}

$rows_per_page = 25;
$total = count($rows);
$pages = ceil($total / $rows_per_page);

$count = 1;

for ($p = 0; $p < $pages; $p++) {

    echo "<table>
            <thead>
                <tr>
                    <th style='width:5%;'>#</th>
                    <th style='width:60%;'>Full Name</th>
                    <th style='width:35%;'>Passbook ID</th>
                </tr>
            </thead>
            <tbody>";

    $start = $p * $rows_per_page;
    $end = min($start + $rows_per_page, $total);

    for ($i = $start; $i < $end; $i++) {
        $row = $rows[$i];

        echo "<tr>
                <td>".$count++."</td>
                <td>".$row['lastname'].", ".$row['firstname']."</td>
                <td>".$row['password']."</td>
              </tr>";
    }

    echo "</tbody></table>";

    if ($p < $pages - 1) {
        echo '<div class="page-break"></div>';
    }
}
?>

<div class="page-break"></div>

<h2>Candidate Votes Per Position</h2>

<?php
$positions = $conn->query("SELECT * FROM positions ORDER BY priority ASC");

while ($position = $positions->fetch_assoc()) {

    echo "<h3>".$position['description']."</h3>";

    $candidates = $conn->query("
        SELECT c.id, c.firstname, c.lastname, COUNT(v.id) AS votes
        FROM candidates c
        LEFT JOIN votes v ON v.candidate_id = c.id
        WHERE c.position_id = '".$position['id']."'
        GROUP BY c.id
        ORDER BY votes DESC, c.lastname ASC
    ");

    echo "<table>
            <thead>
                <tr>
                    <th style='width:5%;'>#</th>
                    <th style='width:65%;'>Candidate Name</th>
                    <th style='width:30%;'>Votes</th>
                </tr>
            </thead>
            <tbody>";

    $count = 1;

    while ($cand = $candidates->fetch_assoc()) {
        echo "<tr>
                <td>".$count++."</td>
                <td>".$cand['lastname'].", ".$cand['firstname']."</td>
                <td>".$cand['votes']."</td>
              </tr>";
    }

    echo "</tbody></table>";
}
?>

<script>

    window.onload = function(){

        window.print();

        setTimeout(function(){

            window.location = "print_logout.php";

        },1000);

    };

</script>

</body>
</html>
