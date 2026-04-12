<?php
require_once __DIR__ . '/includes/conn.php';

$message = [];
$imagesDir = __DIR__ . '/images/';

// create folder if not exists
if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0777, true);
}

// Counters
$totalProcessed = 0;
$skippedCount = 0;

/*
|--------------------------------------------------------------------------
| UPLOAD & MATCH
|--------------------------------------------------------------------------
*/
if (isset($_POST['upload'])) {

    if (!empty($_FILES['photos']['name'][0])) {

        $stmt = $conn->prepare("UPDATE voters SET photo = ? WHERE password = ?");

        foreach ($_FILES['photos']['tmp_name'] as $key => $tmpName) {

            $fileName = basename($_FILES['photos']['name'][$key]);
            $targetPath = $imagesDir . $fileName;

            $passbookNumber = strtoupper(pathinfo($fileName, PATHINFO_FILENAME));

            if (move_uploaded_file($tmpName, $targetPath)) {

                $stmt->bind_param("ss", $fileName, $passbookNumber);
                $stmt->execute();

                $totalProcessed++;

                if ($stmt->affected_rows > 0) {
                    $message[] = "✅ Updated: $passbookNumber → $fileName";
                } else {
                    $message[] = "⚠️ No match in DB: $passbookNumber";
                    $skippedCount++;
                }

            } else {
                $message[] = "❌ Upload failed: $fileName";
                $skippedCount++;
            }
        }

        $stmt->close();

    } else {
        $message[] = "⚠️ No files selected.";
    }
}

/*
|--------------------------------------------------------------------------
| SCAN EXISTING FILES (for manual copy)
|--------------------------------------------------------------------------
*/
if (isset($_POST['scan'])) {

    if (!is_dir($imagesDir)) {
        $message[] = "❌ Folder not found.";
    } else {

        $files = scandir($imagesDir);
        $stmt = $conn->prepare("UPDATE voters SET photo = ? WHERE password = ?");

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $passbookNumber = strtoupper(pathinfo($file, PATHINFO_FILENAME));

            $stmt->bind_param("ss", $file, $passbookNumber);
            $stmt->execute();

            $totalProcessed++;

            if ($stmt->affected_rows > 0) {
                $message[] = "✅ Matched: $passbookNumber → $file";
            } else {
                $message[] = "⚠️ No DB match: $passbookNumber";
                $skippedCount++;
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Batch Photo Upload</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .box { max-width: 700px; margin: auto; }
        .log { background: #f4f4f4; padding: 10px; margin-top: 20px; max-height: 400px; overflow-y: auto; }
        button { padding: 10px 15px; margin-right: 10px; }
        .summary { margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>

<div class="box">
    <h2>Batch Upload Voter Photos</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="photos[]" multiple>
        <br><br>
        <button type="submit" name="upload">Upload & Match</button>
        <button type="submit" name="scan">Scan Existing Files</button>
    </form>

    <p>Note: Move files manually first to use scan existing files</p>

    <div class="log">
        <?php
        if (!empty($message)) {
            foreach ($message as $msg) {
                echo "<div>$msg</div>";
            }
            echo "<div class='summary'>Total processed: $totalProcessed | Skipped: $skippedCount</div>";
        }
        ?>
    </div>
</div>

</body>
</html>
