<?php
// Micro Group of Computer Institute - Database Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = '127.0.0.1';
$db_name = 'micro_group_institute';
$db_user = 'root';
$db_pass = '';

$pdo = null;
$ports = [3307, 3306, 3308];

foreach ($ports as $port) {
    try {
        $dsn = "mysql:host={$db_host};port={$port};dbname={$db_name};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 2
        ]);
        break; // Connected successfully
    } catch (PDOException $e) {
        continue;
    }
}

if (!$pdo) {
    die("Database Connection Error: Could not connect to MySQL server. Please make sure MySQL is started in XAMPP.");
}

// Global base URL helper with HTTPS Reverse Proxy / Cloudflare detection
function get_base_url() {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
        || (!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false);

    $protocol = $isHttps ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Remove port if in Cloudflare/proxy
    if ($isHttps && strpos($host, ':') !== false) {
        $parts = explode(':', $host);
        $host = $parts[0];
    }
    
    return $protocol . $host . '/micro-group';
}

if (!defined('BASE_URL')) {
    define('BASE_URL', get_base_url());
}