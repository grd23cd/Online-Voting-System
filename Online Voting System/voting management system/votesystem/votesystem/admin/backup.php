<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// include '../includes/session.php';

set_time_limit(300);
ini_set('memory_limit', '512M');

ob_start();

date_default_timezone_set('Asia/Manila');

$timestamp = date('Y-m-d_H-i-s');
$dbName    = "votesystem";
$dbUser    = "root";
$dbPass    = "";
$dbHost    = "localhost";

$zipName   = "votesystem_backup_$timestamp.zip";

// ─── Use system temp folder instead of creating backups folder ───────────────
$tempDir   = sys_get_temp_dir();                        // e.g. C:\xampp\tmp
$sqlFile   = $tempDir . "/votesystem_$timestamp.sql";
$zipPath   = $tempDir . "/" . $zipName;

// ─── 1. Dump the database ────────────────────────────────────────────────────
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

// ─── 2. Create ZIP in temp folder ────────────────────────────────────────────
$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Cannot create ZIP file.");
}

// Add SQL dump
$zip->addFile($sqlFile, "database/votesystem.sql");

// ─── 3. Add all project files ─────────────────────────────────────────────────
$projectRoot = realpath(__DIR__ . '/../');  // votesystem/ folder

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(
        $projectRoot,
        RecursiveDirectoryIterator::SKIP_DOTS
    ),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($iterator as $file) {
    $filePath = $file->getRealPath();

    // Skip unreadable files
    if (!is_readable($filePath)) continue;

    // Skip the temp sql and zip files if they somehow appear
    if ($filePath === $sqlFile || $filePath === $zipPath) continue;

    $relativePath = 'files/' . substr($filePath, strlen($projectRoot) + 1);
    $relativePath = str_replace('\\', '/', $relativePath);

    $zip->addFile($filePath, $relativePath);
}

$zip->close();

// Delete the temp SQL file immediately
if (file_exists($sqlFile)) unlink($sqlFile);

if (!file_exists($zipPath) || filesize($zipPath) === 0) {
    die("ZIP was not created or is empty.");
}

// ─── 4. Stream download then delete ZIP ──────────────────────────────────────
ob_end_clean();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($zipPath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

// Stream in chunks
$handle = fopen($zipPath, 'rb');
while (!feof($handle)) {
    echo fread($handle, 8192);
    flush();
}
fclose($handle);

// Delete ZIP from temp folder after download — leaves no trace anywhere
if (file_exists($zipPath)) unlink($zipPath);

exit;