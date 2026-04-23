<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// include '../includes/session.php';

ob_start();

date_default_timezone_set('Asia/Manila');

$timestamp = date('Y-m-d_H-i-s');
$backupDir = __DIR__ . '/../backups';

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$dbName   = "votesystem";
$dbUser   = "root";
$dbPass   = "";
$dbHost   = "localhost";

$sqlFile  = $backupDir . "/votesystem_$timestamp.sql";
$zipName  = "votesystem_backup_$timestamp.zip";
$zipPath  = $backupDir . "/" . $zipName;

// ─── 1. Dump the database ───────────────────────────────────────────────────
$mysqldump = "C:\\xampp\\mysql\\bin\\mysqldump.exe";

$command = escapeshellarg($mysqldump)
    . " -h " . escapeshellarg($dbHost)
    . " -u " . escapeshellarg($dbUser)
    . " " . escapeshellarg($dbName)
    . " -r " . escapeshellarg($sqlFile)
    . " 2>&1";

exec($command, $output, $result);

if ($result !== 0) {
    die("mysqldump failed (exit code $result):<br><pre>" . implode("\n", $output) . "</pre>");
}

if (!file_exists($sqlFile) || filesize($sqlFile) === 0) {
    die("SQL file was not created or is empty.");
}

// ─── 2. Create ZIP ──────────────────────────────────────────────────────────
$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Cannot create ZIP file.");
}

// Add the SQL dump first
$zip->addFile($sqlFile, "database/votesystem.sql");

// ─── 3. Add all project files ───────────────────────────────────────────────
$projectRoot = realpath(__DIR__ . '/../'); // votesystem/ folder

// Folders/files to SKIP (backups folder itself, temp files, etc.)
$skipPaths = [
    realpath($backupDir),           // skip the backups folder
    realpath($zipPath),             // skip the zip being created
    realpath($sqlFile),             // skip the sql file
];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(
        $projectRoot,
        RecursiveDirectoryIterator::SKIP_DOTS
    ),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($iterator as $file) {
    $filePath = $file->getRealPath();

    // Skip the backups directory and its contents
    $skip = false;
    foreach ($skipPaths as $skipPath) {
        if ($skipPath && strpos($filePath, $skipPath) === 0) {
            $skip = true;
            break;
        }
    }
    if ($skip) continue;

    // Skip if not readable
    if (!is_readable($filePath)) continue;

    // Path inside the ZIP: files/votesystem/...
    $relativePath = 'files/' . substr($filePath, strlen($projectRoot) + 1);
    // Normalize slashes for ZIP
    $relativePath = str_replace('\\', '/', $relativePath);

    $zip->addFile($filePath, $relativePath);
}

$zip->close();

if (!file_exists($zipPath) || filesize($zipPath) === 0) {
    die("ZIP was not created or is empty.");
}

// Clean up the loose SQL file
unlink($sqlFile);

// ─── 4. Stream the ZIP download ─────────────────────────────────────────────

// Increase limits for large files
set_time_limit(300);        // 5 minutes
ini_set('memory_limit', '512M');

ob_end_clean();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($zipPath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

// Stream in chunks to avoid memory issues with large files
$handle = fopen($zipPath, 'rb');
while (!feof($handle)) {
    echo fread($handle, 8192);
    flush();
}
fclose($handle);

// Optional: delete zip from server after download
// unlink($zipPath);

exit;