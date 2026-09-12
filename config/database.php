<?php
// Micro Group of Computer Institute - Universal Multi-Platform Database Driver
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = null;

// 1. Try MySQL Connection (Local XAMPP or Cloud MySQL / TiDB / Aiven / PlanetScale)
$db_host = getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: '127.0.0.1');
$db_name = getenv('DB_NAME') ?: (getenv('MYSQLDATABASE') ?: 'micro_group_institute');
$db_user = getenv('DB_USER') ?: (getenv('MYSQLUSER') ?: 'root');
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : '');
$db_port = getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: null);

$ports = $db_port ? [(int)$db_port] : [3307, 3306, 3308];

foreach ($ports as $port) {
    try {
        $dsn = "mysql:host={$db_host};port={$port};dbname={$db_name};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 2
        ]);
        break; // Connected to MySQL successfully
    } catch (Exception $e) {
        continue;
    }
}

// 2. Cloud Fallback: If no MySQL server is reachable, use standalone high-speed SQLite database
if (!$pdo) {
    try {
        $sqliteFile = __DIR__ . '/micro_group.sqlite';
        $isNew = !file_exists($sqliteFile);
        $pdo = new PDO("sqlite:" . $sqliteFile, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        if ($isNew || filesize($sqliteFile) < 1000) {
            // Bootstrap SQLite tables & seed data
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS admins (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    password TEXT NOT NULL,
                    role TEXT DEFAULT 'superadmin',
                    status TEXT DEFAULT 'active',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
                CREATE TABLE IF NOT EXISTS courses (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    course_name TEXT NOT NULL,
                    short_name TEXT NOT NULL,
                    duration TEXT NOT NULL,
                    eligibility TEXT NOT NULL,
                    fee REAL NOT NULL DEFAULT 0,
                    admission_fee REAL NOT NULL DEFAULT 0,
                    discount REAL NOT NULL DEFAULT 0,
                    final_fee REAL NOT NULL DEFAULT 0,
                    description TEXT,
                    certificate_available INTEGER DEFAULT 1,
                    status TEXT DEFAULT 'active'
                );
                CREATE TABLE IF NOT EXISTS students (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    roll_number TEXT NOT NULL UNIQUE,
                    name TEXT NOT NULL,
                    father_name TEXT,
                    email TEXT,
                    phone TEXT,
                    course_id INTEGER,
                    password TEXT NOT NULL,
                    status TEXT DEFAULT 'active',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
                CREATE TABLE IF NOT EXISTS subjects (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    course_id INTEGER,
                    subject_name TEXT NOT NULL,
                    subject_code TEXT,
                    description TEXT
                );
                CREATE TABLE IF NOT EXISTS notes (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    course_id INTEGER,
                    subject_id INTEGER,
                    title TEXT NOT NULL,
                    chapter_name TEXT,
                    file_path TEXT,
                    file_size TEXT,
                    description TEXT,
                    content TEXT
                );
                CREATE TABLE IF NOT EXISTS exams (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    course_id INTEGER,
                    title TEXT NOT NULL,
                    duration_minutes INTEGER DEFAULT 15,
                    total_questions INTEGER DEFAULT 10,
                    max_marks INTEGER DEFAULT 50,
                    passing_percentage REAL DEFAULT 40,
                    status TEXT DEFAULT 'active'
                );
                CREATE TABLE IF NOT EXISTS questions (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    exam_id INTEGER,
                    question_text TEXT NOT NULL,
                    option_a TEXT NOT NULL,
                    option_b TEXT NOT NULL,
                    option_c TEXT NOT NULL,
                    option_d TEXT NOT NULL,
                    correct_option TEXT NOT NULL,
                    marks INTEGER DEFAULT 5
                );
                CREATE TABLE IF NOT EXISTS results (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    exam_id INTEGER,
                    student_id INTEGER,
                    roll_number TEXT,
                    student_name TEXT,
                    total_questions INTEGER,
                    attempted INTEGER,
                    correct INTEGER,
                    wrong INTEGER,
                    obtained_marks REAL,
                    max_marks REAL,
                    percentage REAL,
                    grade TEXT,
                    status TEXT,
                    attempt_date DATETIME DEFAULT CURRENT_TIMESTAMP
                );
                CREATE TABLE IF NOT EXISTS certificates (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    certificate_number TEXT NOT NULL UNIQUE,
                    student_id INTEGER,
                    course_id INTEGER,
                    issue_date DATE,
                    grade TEXT,
                    percentage REAL,
                    status TEXT DEFAULT 'active'
                );
                CREATE TABLE IF NOT EXISTS contact_messages (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT,
                    phone TEXT,
                    subject TEXT,
                    message TEXT NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
                CREATE TABLE IF NOT EXISTS typing_results (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    candidate_name TEXT NOT NULL,
                    phone TEXT,
                    duration_mins INTEGER DEFAULT 1,
                    net_wpm INTEGER DEFAULT 0,
                    gross_wpm INTEGER DEFAULT 0,
                    accuracy REAL DEFAULT 100.00,
                    mistakes INTEGER DEFAULT 0,
                    grade TEXT DEFAULT 'A',
                    certificate_no TEXT NOT NULL UNIQUE,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
                CREATE TABLE IF NOT EXISTS settings (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    setting_key TEXT NOT NULL UNIQUE,
                    setting_value TEXT
                );
            ");

            // Seed Admin
            $adminHash = '$2y$10$HUsT/vVwM2s0f7NxfMfPBuq3ZMoQEHTk88Al7YGPcvFpEXyo52Cj.'; // Micro@DK#2026!
            $pdo->exec("INSERT OR IGNORE INTO admins (id, name, email, password, role) VALUES (1, 'DK Singh (Director)', 'admin@microgroup.com', '{$adminHash}', 'superadmin')");

            // Seed Settings
            $settings = [
                'institute_name' => 'Micro Group of Computer Institute',
                'manager_name' => 'DK Singh',
                'location' => 'Bhoopganj Payagpur',
                'branch_address' => 'Main Market, Bhoopganj Payagpur, Bahraich, Uttar Pradesh',
                'phone' => '+91 98765 43210',
                'email' => 'info@microgroupinstitute.com',
                'tagline' => 'Learn • Practice • Test • Achieve',
                'default_passing_percentage' => '40',
                'certificate_prefix' => 'MGI-2026-',
                'developer_name' => 'Durgesh Pratap Singh'
            ];
            foreach ($settings as $k => $v) {
                $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)")->execute([$k, $v]);
            }

            // Seed Courses
            $courses = [
                [1, 'Advance Diploma in Computer Application', 'ADCA', '12 Months', '10th / 12th Pass', 12000, 500, 2500, 10000, 'Comprehensive 1-year master diploma covering office productivity, web design, graphic tools, accounting, and software fundamentals.'],
                [2, 'Course on Computer Concepts', 'CCC', '3 Months', 'Any Qualification', 4000, 300, 800, 3500, 'Government-recognized foundational computing syllabus required for national & state competitive examinations.'],
                [3, 'Diploma in Computer Application', 'DCA', '6 Months', '10th Pass', 7000, 400, 1400, 6000, '6-month foundational diploma emphasizing word processing, data spreadsheets, and office automation tools.'],
                [4, 'Post Graduate Diploma in Computer Applications', 'PGDCA', '12 Months', 'Graduate in Any Stream', 18000, 1000, 4000, 15000, 'Advanced postgraduate specialization in database architecture, software management, and systems analysis.'],
                [5, 'Tally Prime with GST & E-Way Bill', 'Tally Prime', '3 Months', '10th / 12th / Commerce', 6000, 300, 1300, 5000, 'Professional financial accounting with taxation, voucher entries, balance sheets, and GST compliance.']
            ];
            foreach ($courses as $c) {
                $pdo->prepare("INSERT OR REPLACE INTO courses (id, course_name, short_name, duration, eligibility, fee, admission_fee, discount, final_fee, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")->execute($c);
            }

            // Seed Sample Student
            $stHash = '$2y$10$8v5Z2sLgQc5J5Pj1j5o9v.3xR7kK4f8jL2gT7y8u9v0w1x2y3z4a5'; // student123
            $pdo->exec("INSERT OR IGNORE INTO students (id, roll_number, name, father_name, email, phone, course_id, password) VALUES (1, 'MG202601', 'Rahul Kumar', 'Shri R. P. Verma', 'rahul@gmail.com', '9876543210', 1, '{$stHash}')");

            // Seed Sample Certificate
            $pdo->exec("INSERT OR IGNORE INTO certificates (id, certificate_number, student_id, course_id, issue_date, grade, percentage, status) VALUES (1, 'MGI-2026-00001', 1, 1, '2026-08-15', 'A+', 94.5, 'active')");
        }
    } catch (Exception $e) {
        die("Database Initialization Error: " . $e->getMessage());
    }
}

// Global base URL helper with HTTPS Reverse Proxy / Cloudflare / Render detection
function get_base_url() {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
        || (!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false);

    $protocol = $isHttps ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // In local XAMPP
    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        return $protocol . $host . '/micro-group';
    }
    
    // In Render / cloud root domain
    if (strpos($host, 'onrender.com') !== false || strpos($host, 'alwaysdata.net') !== false) {
        return $protocol . $host;
    }
    
    return $protocol . $host . '/micro-group';
}

if (!defined('BASE_URL')) {
    define('BASE_URL', get_base_url());
}