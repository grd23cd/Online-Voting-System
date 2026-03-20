<?php
set_time_limit(0);
include 'includes/conn.php';

echo "<!DOCTYPE html><html><head>
<style>
body { font-family: Arial, sans-serif; background: #F1E9D2; padding: 20px; }
#log { border: 1px solid #ccc; background: #fff; padding: 10px; height: 400px; overflow-y: scroll; }
.progress-bar { width: 0%; height: 25px; background: #4CAF50; text-align: center; color: white; }
.progress-container { width: 100%; background: #ccc; margin-bottom: 10px; }
</style>
</head><body>";

echo "<h2>CSV Import Progress</h2>";
echo "<div class='progress-container'><div class='progress-bar' id='progress'>0%</div></div>";
echo "<div id='log'></div>";

flush(); // make sure output starts sending to browser

$batchSize = 100;
$batch = [];
$inserted = 0;
$skipped = 0;
$totalRows = 0;
$processedRows = 0;

// Count total rows first
if (($handleCount = fopen("users.csv", "r")) !== FALSE) {
    while (($row = fgetcsv($handleCount, 1000, ",")) !== FALSE) $totalRows++;
    fclose($handleCount);
    $totalRows--; // exclude header
}

// Open CSV file for processing
if (($handle = fopen("users.csv", "r")) !== FALSE) {
    fgetcsv($handle); // skip header

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $voters_id = $conn->real_escape_string($data[0]);
        $plain_password = $data[1];
        $firstname = $conn->real_escape_string($data[2]);
        $lastname = $conn->real_escape_string($data[3]);
        $password_hash = password_hash($plain_password, PASSWORD_BCRYPT);

        $batch[] = "('$voters_id', '$password_hash', '$firstname', '$lastname')";
        $processedRows++;

        // Flush progress every batch
        if (count($batch) >= $batchSize) {
            $sql = "INSERT IGNORE INTO voters (voters_id, password, firstname, lastname) VALUES " . implode(',', $batch);
            if($conn->query($sql)){
                $inserted += $conn->affected_rows;
                $skipped += count($batch) - $conn->affected_rows;
            }

            $batch = []; // reset batch

            // Update progress bar
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

    // Insert remaining rows
    if (count($batch) > 0) {
        $sql = "INSERT IGNORE INTO voters (voters_id, password, firstname, lastname) VALUES " . implode(',', $batch);
        if($conn->query($sql)){
            $inserted += $conn->affected_rows;
            $skipped += count($batch) - $conn->affected_rows;
        }
    }

    fclose($handle);

    // Final progress update
    echo "<script>
        document.getElementById('progress').style.width = '100%';
        document.getElementById('progress').innerHTML = '100%';
        document.getElementById('log').innerHTML += '<b>Import finished!</b><br>Total inserted: {$inserted}<br>Total skipped: {$skipped}';
        document.getElementById('log').scrollTop = document.getElementById('log').scrollHeight;
    </script>";
} else {
    echo "<p>Failed to open CSV file!</p>";
}

$conn->close();

echo "</body></html>";
?>
