<?php
set_time_limit(0);
include 'includes/conn.php';

// ---------- CODE CONFIG ----------
$codeLength  = 6; // characters per code (fixed, per request)
$charset     = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // 32 chars, no 0/O/1/I
$attemptsMax = 200; // per-code retry cap before giving up
// ----------------------------------

// ---------- FILTER CONFIG ----------
$filterPrefix = 'R00'; // only import rows whose passbook/voters_id starts with this
// ------------------------------------

function generateCode($length, $charset){
    $max = strlen($charset) - 1;
    $code = '';
    for($i = 0; $i < $length; $i++){
        $code .= $charset[random_int(0, $max)];
    }
    return $code;
}

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
<p>This will import data into your database and generate a unique code for each voter.</p>
<p><b>Filter active:</b> Only passbook numbers starting with "<?php echo htmlspecialchars($filterPrefix); ?>" will be imported.</p>

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
$filteredOut = 0;
$totalRows = 0;
$processedRows = 0;

// ---------- Preload existing codes for in-memory uniqueness checks ----------
$existingCodes = [];
$codeResult = $conn->query("SELECT code FROM voters WHERE code IS NOT NULL");
if ($codeResult) {
    while ($row = $codeResult->fetch_assoc()) {
        $existingCodes[$row['code']] = true;
    }
    $codeResult->free();
} else {
    echo "<p style='color:red'>Warning: could not read existing codes (" . $conn->error . "). Make sure the 'code' column exists on 'voters'.</p>";
}

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
        // CSV layout: [0] Name, [1] P.B. No, [2] First Name, [3] Last Name
        $voters_id = trim($data[1]);

        // ---- FILTER: only keep passbook numbers starting with the configured prefix ----
        if (strpos($voters_id, $filterPrefix) !== 0) {
            $filteredOut++;
            $processedRows++;
            continue;
        }

        // No password column in the CSV — default the password to the P.B. No itself.
        $plain_password = $voters_id;

        $voters_id = $conn->real_escape_string($voters_id);
        $plain_password = $conn->real_escape_string($plain_password);
        $firstname = $conn->real_escape_string(trim($data[2]));
        $lastname = $conn->real_escape_string(trim($data[3]));

        // Generate a unique code for this voter (checked against DB + this run's batch)
        $attempts = 0;
        do {
            $code = generateCode($codeLength, $charset);
            $attempts++;
            if ($attempts > $attemptsMax) {
                echo "<script>
                    document.getElementById('log').innerHTML += '<b style=\"color:red\">Ran out of unique codes after {$processedRows} rows. Codespace (32^6 = 1,073,741,824) may be nearly exhausted.</b><br>';
                </script>";
                $conn->close();
                exit();
            }
        } while (isset($existingCodes[$code]));

        $existingCodes[$code] = true; // reserve immediately so no other row in this run can reuse it

        $batch[] = "('$voters_id', '$plain_password', '$firstname', '$lastname', '$code')";
        $processedRows++;

        if (count($batch) >= $batchSize) {
            $sql = "INSERT IGNORE INTO voters (voters_id, password, firstname, lastname, code) VALUES " . implode(',', $batch);
            if($conn->query($sql)){
                $inserted += $conn->affected_rows;
                $skipped += count($batch) - $conn->affected_rows;
            }

            $batch = [];

            $percent = round(($processedRows / $totalRows) * 100);
            echo "<script>
                document.getElementById('progress').style.width = '{$percent}%';
                document.getElementById('progress').innerHTML = '{$percent}%';
                document.getElementById('log').innerHTML += 'Processed {$processedRows} of {$totalRows} rows ({$filteredOut} filtered out so far)<br>';
                document.getElementById('log').scrollTop = document.getElementById('log').scrollHeight;
            </script>";
            flush();
        }
    }

    if (count($batch) > 0) {
        $sql = "INSERT IGNORE INTO voters (voters_id, password, firstname, lastname, code) VALUES " . implode(',', $batch);
        if($conn->query($sql)){
            $inserted += $conn->affected_rows;
            $skipped += count($batch) - $conn->affected_rows;
        }
    }

    fclose($handle);

    echo "<script>
        document.getElementById('progress').style.width = '100%';
        document.getElementById('progress').innerHTML = '100%';
        document.getElementById('log').innerHTML += '<b>Import finished!</b><br>Total rows in file: {$totalRows}<br>Filtered out (no \"" . addslashes($filterPrefix) . "\" prefix): {$filteredOut}<br>Total inserted: {$inserted}<br>Total skipped (duplicates): {$skipped}';
    </script>";
} else {
    echo "<p>Failed to open CSV file!</p>";
}

$conn->close();
?>

</body>
</html>