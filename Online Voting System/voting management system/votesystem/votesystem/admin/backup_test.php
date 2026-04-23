<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Backup Diagnostics</h2>";

// 1. Check exec() is enabled
echo "<h3>1. exec() function</h3>";
if (function_exists('exec')) {
    echo "✅ exec() is available<br>";
} else {
    echo "❌ exec() is DISABLED in php.ini — this is your problem<br>";
}

// 2. Check ZipArchive
echo "<h3>2. ZipArchive</h3>";
if (class_exists('ZipArchive')) {
    echo "✅ ZipArchive is available<br>";
} else {
    echo "❌ ZipArchive is DISABLED — enable php_zip in php.ini<br>";
}

// 3. Check mysqldump path
echo "<h3>3. mysqldump path</h3>";
$path = "C:\\xampp\\mysql\\bin\\mysqldump.exe";
if (file_exists($path)) {
    echo "✅ Found mysqldump at: $path<br>";
} else {
    echo "❌ Not found at: $path<br>";
    exec("where mysqldump 2>&1", $out);
    echo "System PATH result: " . implode(", ", $out) . "<br>";
}

// 4. Check backups directory
// backup.php is in votesystem/admin/, so backups folder = votesystem/backups/
echo "<h3>4. Backups directory</h3>";
$backupDir = __DIR__ . '/../backups'; // goes up from admin/ to votesystem/
echo "Trying to create/access: " . realpath(__DIR__ . '/..') . "/backups<br>";
if (!is_dir($backupDir)) {
    $made = mkdir($backupDir, 0777, true);
    echo $made ? "✅ Created backups folder<br>" : "❌ Failed to create backups folder<br>";
} else {
    echo "✅ Backups folder already exists<br>";
}
if (is_writable($backupDir)) {
    echo "✅ Backups folder is writable<br>";
} else {
    echo "❌ Backups folder is NOT writable<br>";
}

// 5. Try running mysqldump directly
echo "<h3>5. Test mysqldump execution</h3>";
$command = "\"C:\\xampp\\mysql\\bin\\mysqldump.exe\" --version 2>&1";
exec($command, $output, $code);
echo "Exit code: $code<br>";
echo "Output: " . implode("<br>", $output) . "<br>";

// 6. Current file location check
echo "<h3>6. File location</h3>";
echo "backup_test.php is located at: <b>" . __FILE__ . "</b><br>";
echo "Backups will be saved to: <b>" . realpath($backupDir) . "</b><br>";

// 7. Session check
echo "<h3>7. Session</h3>";
echo "Session status: " . (session_status() === PHP_SESSION_ACTIVE ? "✅ Active" : "❌ Not active") . "<br>";

echo "<br><b>Done. Share these results.</b>";
?>