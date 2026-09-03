<?php

$host = 'altaria.proxy.rlwy.net';
$port = '27397';
$database = 'railway';
$username = 'root';
$password = 'rKzKwcJbAsYYbWSMsVgZLWCkzEilDvsM';

$sqlFile = __DIR__ . '/railway-data.sql';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => true,
        ]
    );

    echo "Connected to Railway MySQL successfully.\n";

    /*
     * Clear previously imported data.
     * We exclude the migrations table because Railway's
     * migrations must remain intact.
     */
    $tables = $pdo->query("
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name != 'migrations'
    ")->fetchAll(PDO::FETCH_COLUMN);

    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE `$table`");
    }

    echo "Existing application data cleared.\n";

    $sql = file_get_contents($sqlFile);

    if ($sql === false) {
        throw new Exception("Could not read railway-data.sql");
    }

    /*
     * Remove SQL comments so statements such as
     * SET FOREIGN_KEY_CHECKS=0 are not accidentally skipped.
     */
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);

    $statements = array_filter(
        array_map(
            'trim',
            preg_split('/;\s*(?:\r?\n|$)/', $sql)
        )
    );

    $count = 0;

    foreach ($statements as $statement) {
        if ($statement === '') {
            continue;
        }

        $pdo->exec($statement);
        $count++;
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    echo "Import completed successfully.\n";
    echo "SQL statements executed: $count\n";

} catch (Throwable $e) {

    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    } catch (Throwable $ignored) {
    }

    echo "IMPORT FAILED:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}