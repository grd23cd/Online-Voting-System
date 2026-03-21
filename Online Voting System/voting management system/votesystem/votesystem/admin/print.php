<?php
include 'includes/session.php';
include 'includes/conn.php';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Official Election Report</title>
<style>
    /* ===== Body & General Text ===== */
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

    /* ===== Tables ===== */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 40px;
        font-size: 12px;
        table-layout: fixed; /* ensures content fits */
        word-wrap: break-word; /* long names wrap inside cells */
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

    /* ===== Page Breaks ===== */
    .page-break { page-break-after: always; }

    /* ===== Letter Paper & Margins ===== */
    @page {
        size: letter;
        margin: 1in; /* standard 1-inch margins */
    }

    /* ===== Adjust existing dynamic header & footer to stay inside margins ===== */
    body::before {
        /* this is your existing dynamic header */
        display: block;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        max-width: calc(100% - 2in); /* keep inside left + right margins */
        margin: 0 1in; /* shift content inside margins */
        box-sizing: border-box;
    }

    body::after {
        /* this is your existing dynamic footer (page number) */
        display: block;
        font-size: 12px;
        text-align: right;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        max-width: calc(100% - 2in); /* keep inside margins */
        margin: 0 1in;
        box-sizing: border-box;
    }

    /* ===== Print Media Adjustments ===== */
    @media print {
        button { display: none; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        /* keep header/footer fixed but inside margins */
        body::before { position: fixed; top: 0; }
        body::after { position: fixed; bottom: 0; }
    }
</style>
</head>
<body>

<h1>Official Election Report</h1>
<button onclick="window.print()">Print / Save as PDF</button>

<!-- =================== Summary =================== -->
<h2>Summary</h2>

<?php
$total_voters_sql = "SELECT COUNT(*) AS total_voters FROM voters";
$total_voters_res = $conn->query($total_voters_sql)->fetch_assoc();
$total_voters = $total_voters_res['total_voters'];

$voters_voted_sql = "SELECT COUNT(DISTINCT voters_id) AS voted_count FROM votes";
$voters_voted_res = $conn->query($voters_voted_sql)->fetch_assoc();
$voters_voted = $voters_voted_res['voted_count'];

$total_cand_sql = "SELECT COUNT(*) AS total_candidates FROM candidates";
$total_cand_res = $conn->query($total_cand_sql)->fetch_assoc();
$total_candidates = $total_cand_res['total_candidates'];

$total_votes_sql = "SELECT COUNT(*) AS total_votes FROM votes";
$total_votes_res = $conn->query($total_votes_sql)->fetch_assoc();
$total_votes = $total_votes_res['total_votes'];
?>

<table class="summary-table">
    <tbody>
        <tr>
            <td>Total Registered Voters</td>
            <td><?php echo $total_voters; ?></td>
        </tr>
        <tr>
            <td>Voters Who Voted</td>
            <td><?php echo $voters_voted; ?></td>
        </tr>
        <tr>
            <td>Total Candidates</td>
            <td><?php echo $total_candidates; ?></td>
        </tr>
        <tr>
            <td>Total Votes Cast</td>
            <td><?php echo $total_votes; ?></td>
        </tr>
    </tbody>
</table>

<div class="page-break"></div>

<!-- =================== Voters Who Voted =================== -->
<h2>Voters Who Voted</h2>

<table>
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th style="width:60%;">Full Name</th>
            <th style="width:35%;">Passbook ID</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "
            SELECT DISTINCT v.id, v.firstname, v.lastname, v.password
            FROM voters v
            INNER JOIN votes vt ON vt.voters_id = v.id
            ORDER BY v.lastname ASC
        ";
        $query = $conn->query($sql);
        $count = 1;
        while($row = $query->fetch_assoc()){
            echo "<tr>
                    <td>".$count++."</td>
                    <td>".$row['lastname'].", ".$row['firstname']."</td>
                    <td>".$row['password']."</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<div class="page-break"></div>

<!-- =================== Candidate Votes =================== -->
<h2>Candidate Votes Per Position</h2>

<?php
$pos_sql = "SELECT * FROM positions ORDER BY priority ASC";
$pos_query = $conn->query($pos_sql);

while($position = $pos_query->fetch_assoc()){
    echo "<h3>Position: ".$position['description']."</h3>";

    $cand_sql = "
        SELECT c.id, c.firstname, c.lastname, COUNT(v.id) as votes
        FROM candidates c
        LEFT JOIN votes v ON v.candidate_id = c.id
        WHERE c.position_id = '".$position['id']."'
        GROUP BY c.id
        ORDER BY votes DESC, c.lastname ASC
    ";
    $cand_query = $conn->query($cand_sql);

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
    while($cand = $cand_query->fetch_assoc()){
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
    window.onload = function() {
        window.print();
    }
</script>

</body>
</html>
