<?php
echo "Loaded PDO drivers: " . implode(", ", PDO::getAvailableDrivers()) . "<br>";
$db_url = getenv('DATABASE_URL');
echo "DATABASE_URL: " . ($db_url ? "Set" : "Not Set") . "<br>";
if ($db_url) {
    $dbopts = parse_url($db_url);
    $pg_host = $dbopts["host"] ?? '';
    $pg_port = $dbopts["port"] ?? 5432;
    $pg_user = $dbopts["user"] ?? '';
    $pg_pass = $dbopts["pass"] ?? '';
    $pg_db   = ltrim($dbopts["path"] ?? '', '/');
    try {
        $dsn = "pgsql:host={$pg_host};port={$pg_port};dbname={$pg_db}";
        echo "DSN: " . $dsn . "<br>";
        $pdo = new PDO($dsn, $pg_user, $pg_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "PostgreSQL Connection: SUCCESS!<br>";
    } catch (Exception $e) {
        echo "PostgreSQL Error: " . $e->getMessage() . "<br>";
    }
}
?>
