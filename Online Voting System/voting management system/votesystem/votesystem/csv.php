<?php
set_time_limit(0);
include 'includes/conn.php';

// CHECK: Only run script if confirmed
if (!isset($_POST['run'])) {
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial; background: #F1E9D2; text-align: center; padding-top: 100px; }
        button { padding: 15px 30px; font-size: 18px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
    </style>
</head>
<body>

<h2>CSV Import Script</h2>
<p>This will import data into your database.</p>

<form method="POST" onsubmit="return confirm('Are you sure you want to run this import?');">
    <button type="submit" name="run">Run Import</button>
</form>

</body>
</html>
<?php
exit(); // STOP script here if not confirmed
}
?>

<!DOCTYPE html>
<html>
<head>
<style>
body { font-family: Arial, sans-serif; background: #F1E9D2; padding: 20px; }
#log { border: 1px solid #ccc; background: #fff; padding: 10px; height: 400px; overflow-y: scroll; }
.progress-bar { width: 0%; height: 25px; background: #4CAF50; text-align: center; color: white; }
.progress-container { width: 100%; background: #ccc; margin-bottom: 10px; }
</style>
</head>
<body>

<h2>CSV Import Progress</h2>
<div class='progress-container'><div class='progress-bar' id='progress'>0%</div></div>
<div id='log'></div>

<?php
flush();

$batchSize = 100;
$batch = [];
$inserted = 0;
$skipped = 0;
$totalRows = 0;
$processedRows = 0;

// Count total rows
if (($handleCount = fopen("users.csv", "r")) !== FALSE) {
    while (($row = fgetcsv($handleCount, 1000, ",")) !== FALSE) $totalRows++;
    fclose($handleCount);
    $totalRows--;
}

// Process CSV
if (($handle = fopen("users.csv", "r")) !== FALSE) {
    fgetcsv($handle);

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $voters_id = $conn->real_escape_string($data[0]);
        $plain_password = $conn->real_escape_string($data[1]);
        $firstname = $conn->real_escape_string($data[2]);
        $lastname = $conn->real_escape_string($data[3]);

        // NO HASHING — store password directly
        $batch[] = "('$voters_id', '$plain_password', '$firstname', '$lastname')";
        $processedRows++;

        if (count($batch) >= $batchSize) {
            $sql = "INSERT IGNORE INTO voters (voters_id, password, firstname, lastname) VALUES " . implode(',', $batch);
            if($conn->query($sql)){
                $inserted += $conn->affected_rows;
                $skipped += count($batch) - $conn->affected_rows;
            }

            $batch = [];

            $percent = round(($processedRows / $totalRows) * 100);
            echo "<script>
                document.getElementById('progress').style.width = '{$percent}%';
                document.getElementById('progress').innerHTML = '{$percent}%';
                document.getElementById('log').innerHTML += 'Processed {$processedRows} of {$totalRows} rows<br>';
                document.getElementById('log').scrollTop = document.getElementById('log').scrollHeight;
            </script>";
            flush();
        }
    }

    if (count($batch) > 0) {
        $sql = "INSERT IGNORE INTO voters (voters_id, password, firstname, lastname) VALUES " . implode(',', $batch);
        if($conn->query($sql)){
            $inserted += $conn->affected_rows;
            $skipped += count($batch) - $conn->affected_rows;
        }
    }

    fclose($handle);

    echo "<script>
        document.getElementById('progress').style.width = '100%';
        document.getElementById('progress').innerHTML = '100%';
        document.getElementById('log').innerHTML += '<b>Import finished!</b><br>Total inserted: {$inserted}<br>Total skipped: {$skipped}';
    </script>";
} else {
    echo "<p>Failed to open CSV file!</p>";
}

$conn->close();
?>

</body>
</html>
