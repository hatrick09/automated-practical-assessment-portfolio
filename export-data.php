<?php

$db = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$output = "-- Laravel SQLite data export\n";
$output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
$output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

$tables = $db->query("
    SELECT name
    FROM sqlite_master
    WHERE type = 'table'
      AND name NOT LIKE 'sqlite_%'
      AND name != 'migrations'
    ORDER BY name
")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    $columns = $db->query("PRAGMA table_info(\"$table\")")->fetchAll(PDO::FETCH_ASSOC);

    if (!$columns) {
        continue;
    }

    $columnNames = array_column($columns, 'name');

    $rows = $db->query("SELECT * FROM \"$table\"")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $values = [];

        foreach ($columnNames as $column) {
            $value = $row[$column];

            if ($value === null) {
                $values[] = "NULL";
            } else {
                $values[] = "'" . str_replace("'", "''", $value) . "'";
            }
        }

        $output .= "INSERT INTO `$table` (`"
            . implode("`, `", $columnNames)
            . "`) VALUES ("
            . implode(", ", $values)
            . ");\n";
    }

    $output .= "\n";
}

$output .= "SET FOREIGN_KEY_CHECKS=1;\n";

file_put_contents(__DIR__ . '/railway-data.sql', $output);

echo "Export completed successfully.\n";
echo "File created: " . __DIR__ . "/railway-data.sql\n";