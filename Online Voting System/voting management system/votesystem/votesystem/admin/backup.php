<?php
include 'includes/session.php';

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "votesystem";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET NAMES 'utf8'");

$tables = [];
$result = $conn->query("SHOW TABLES");

while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

$output = "";

foreach ($tables as $table) {

    $res = $conn->query("SELECT * FROM $table");
    $fields_count = $res->field_count;

    $output .= "DROP TABLE IF EXISTS `$table`;\n";

    $create = $conn->query("SHOW CREATE TABLE $table");
    $row2 = $create->fetch_array();
    $output .= $row2[1] . ";\n\n";

    while ($row = $res->fetch_array()) {
        $output .= "INSERT INTO `$table` VALUES(";

        for ($i = 0; $i < $fields_count; $i++) {

            $value = isset($row[$i]) ? $conn->real_escape_string($row[$i]) : "";

            $output .= '"' . $value . '"';

            if ($i < ($fields_count - 1)) {
                $output .= ",";
            }
        }

        $output .= ");\n";
    }

    $output .= "\n\n";
}

$filename = "votesystem-backup-" . date("Y-m-d_H-i-s") . ".sql";

header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename=' . $filename);
header('Pragma: no-cache');
header('Expires: 0');

echo $output;
exit;
?>
