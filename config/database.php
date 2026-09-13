<?php
// Micro Group of Computer Institute - Universal Multi-Platform Database Driver
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = null;

// 0. Try PostgreSQL (Render Internal Database URL)
$db_url = getenv('DATABASE_URL');
if ($db_url) {
    $dbopts = parse_url($db_url);
    if (isset($dbopts['scheme']) && $dbopts['scheme'] === 'postgres') {
        $pg_host = $dbopts["host"] ?? '';
        $pg_port = $dbopts["port"] ?? 5432;
        $pg_user = $dbopts["user"] ?? '';
        $pg_pass = $dbopts["pass"] ?? '';
        $pg_db   = ltrim($dbopts["path"] ?? '', '/');
        
        try {
            $dsn = "pgsql:host={$pg_host};port={$pg_port};dbname={$pg_db}";
            $pdo = new PDO($dsn, $pg_user, $pg_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 3
            ]);
            
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS settings (id SERIAL PRIMARY KEY, key_name VARCHAR(255) NOT NULL UNIQUE, key_value TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS admins (id SERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, role VARCHAR(50) DEFAULT 'superadmin', status VARCHAR(50) DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS courses (id SERIAL PRIMARY KEY, course_name VARCHAR(255) NOT NULL, short_name VARCHAR(50) NOT NULL, description TEXT, duration VARCHAR(100), fee DECIMAL(10,2) NOT NULL DEFAULT 0.00, admission_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00, discount DECIMAL(10,2) NOT NULL DEFAULT 0.00, final_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00, eligibility VARCHAR(255) DEFAULT '10th / 12th Pass', certificate_available INT DEFAULT 1, course_image VARCHAR(255) DEFAULT 'adca.svg', status VARCHAR(50) DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS subjects (id SERIAL PRIMARY KEY, course_id INT NOT NULL, subject_name VARCHAR(255) NOT NULL, subject_code VARCHAR(100), description TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS students (id SERIAL PRIMARY KEY, roll_number VARCHAR(100) NOT NULL UNIQUE, name VARCHAR(255) NOT NULL, father_name VARCHAR(255), email VARCHAR(255), mobile VARCHAR(50), phone VARCHAR(50), password VARCHAR(255) NOT NULL, course_id INT NOT NULL DEFAULT 1, admission_date DATE, status VARCHAR(50) DEFAULT 'active', profile_image VARCHAR(255) DEFAULT 'default_avatar.svg', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS notes (id SERIAL PRIMARY KEY, course_id INT NOT NULL DEFAULT 1, subject_id INT NOT NULL DEFAULT 1, chapter_name VARCHAR(255), title VARCHAR(255) NOT NULL, file_path VARCHAR(255), file_type VARCHAR(50) DEFAULT 'pdf', file_size VARCHAR(50) DEFAULT '1.2 MB', description TEXT, content TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS exams (id SERIAL PRIMARY KEY, course_id INT NOT NULL DEFAULT 1, subject_id INT, exam_title VARCHAR(255) DEFAULT '', title VARCHAR(255) DEFAULT '', description TEXT, total_questions INT NOT NULL DEFAULT 10, duration_minutes INT NOT NULL DEFAULT 15, max_marks INT NOT NULL DEFAULT 50, passing_percentage INT NOT NULL DEFAULT 40, status VARCHAR(50) DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS questions (id SERIAL PRIMARY KEY, exam_id INT NOT NULL DEFAULT 1, question_text TEXT NOT NULL, option_a TEXT NOT NULL, option_b TEXT NOT NULL, option_c TEXT NOT NULL, option_d TEXT NOT NULL, correct_option VARCHAR(10) NOT NULL, marks INT NOT NULL DEFAULT 5, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS exam_attempts (id SERIAL PRIMARY KEY, student_id INT, exam_id INT NOT NULL DEFAULT 1, roll_number VARCHAR(100), student_name VARCHAR(255), course_name VARCHAR(255), start_time TIMESTAMP NOT NULL, end_time TIMESTAMP, total_questions INT NOT NULL DEFAULT 0, attempted INT NOT NULL DEFAULT 0, correct_answers INT NOT NULL DEFAULT 0, wrong_answers INT NOT NULL DEFAULT 0, unattempted INT NOT NULL DEFAULT 0, total_marks INT NOT NULL DEFAULT 0, obtained_marks INT NOT NULL DEFAULT 0, percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00, grade VARCHAR(10) DEFAULT 'F', status VARCHAR(50) DEFAULT 'in_progress', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS exam_answers (id SERIAL PRIMARY KEY, attempt_id INT NOT NULL, question_id INT NOT NULL, selected_option VARCHAR(10), is_correct INT DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS results (id SERIAL PRIMARY KEY, attempt_id INT NOT NULL UNIQUE, student_id INT, exam_id INT NOT NULL, roll_number VARCHAR(100) NOT NULL, total_marks INT NOT NULL, obtained_marks INT NOT NULL, percentage DECIMAL(5,2) NOT NULL, grade VARCHAR(10) NOT NULL, pass_status VARCHAR(50) NOT NULL, date DATE NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS certificates (id SERIAL PRIMARY KEY, certificate_number VARCHAR(100) NOT NULL UNIQUE, student_id INT NOT NULL DEFAULT 0, course_id INT NOT NULL DEFAULT 0, result_id INT NOT NULL DEFAULT 0, student_name VARCHAR(255), roll_number VARCHAR(100), course_name VARCHAR(255), duration VARCHAR(100), percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00, grade VARCHAR(10) NOT NULL DEFAULT 'A', issue_date DATE, status VARCHAR(50) DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS contact_messages (id SERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255), phone VARCHAR(50), subject VARCHAR(255), message TEXT NOT NULL, status VARCHAR(50) DEFAULT 'unread', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
                CREATE TABLE IF NOT EXISTS typing_results (id SERIAL PRIMARY KEY, candidate_name VARCHAR(255) NOT NULL, phone VARCHAR(50), duration_mins INT DEFAULT 1, net_wpm INT DEFAULT 0, gross_wpm INT DEFAULT 0, accuracy DECIMAL(5,2) DEFAULT 100.00, mistakes INT DEFAULT 0, grade VARCHAR(10) DEFAULT 'A', certificate_no VARCHAR(100) NOT NULL UNIQUE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
            ");
            
            $sc = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
            if ($sc == 0) {
                $defaults = [
                    'institute_name' => 'Micro Group of Computer Institute',
                    'short_name' => 'MGI',
                    'tagline' => 'Learn • Practice • Test • Achieve',
                    'manager_name' => 'DK Singh',
                    'location' => 'Bhoopganj Payagpur',
                    'branch_address' => 'Main Market, Bhoopganj Payagpur / Fukganj, Uttar Pradesh',
                    'phone' => '+91 9792686570',
                    'email' => 'Deep2180411008@gmail.com',
                    'alt_email' => 'support@microgroupinstitute.com',
                    'default_passing_percentage' => '40',
                    'certificate_prefix' => 'MGI-2026-',
                    'hero_title' => 'Empowering Students With Digital Skills',
                    'hero_subtitle' => 'Learn computer skills with quality education, digital study material, online examinations and recognized course completion certificates.'
                ];
                $st = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES (?, ?)");
                foreach ($defaults as $k => $v) {
                    $st->execute([$k, $v]);
                }
            }
            
            $ac = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
            if ($ac == 0) {
                $hash = password_hash('Verna@325901', PASSWORD_BCRYPT);
                $pdo->prepare("INSERT INTO admins (name, email, password, role, status) VALUES ('DK Singh (Director/Manager)', 'Deep2180411008@gmail.com', ?, 'superadmin', 'active')")->execute([$hash]);
            }
        } catch (Exception $e) {
            $pdo = null;
        }
    }
}

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

        // Register MySQL compatibility functions in SQLite
        $pdo->sqliteCreateFunction('CURDATE', function() {
            return date('Y-m-d');
        });
        $pdo->sqliteCreateFunction('NOW', function() {
            return date('Y-m-d H:i:s');
        });

        // Ensure all required tables and columns exist
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                key_name TEXT NOT NULL UNIQUE,
                key_value TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
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
                description TEXT NOT NULL DEFAULT '',
                duration TEXT NOT NULL DEFAULT '',
                fee REAL NOT NULL DEFAULT 0.00,
                admission_fee REAL NOT NULL DEFAULT 0.00,
                discount REAL NOT NULL DEFAULT 0.00,
                final_fee REAL NOT NULL DEFAULT 0.00,
                eligibility TEXT NOT NULL DEFAULT '10th / 12th Pass',
                certificate_available INTEGER DEFAULT 1,
                course_image TEXT DEFAULT 'adca.svg',
                status TEXT DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS subjects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                course_id INTEGER NOT NULL,
                subject_name TEXT NOT NULL,
                subject_code TEXT,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS students (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                roll_number TEXT NOT NULL UNIQUE,
                name TEXT NOT NULL,
                father_name TEXT,
                email TEXT,
                mobile TEXT,
                phone TEXT,
                password TEXT NOT NULL,
                course_id INTEGER NOT NULL DEFAULT 1,
                admission_date DATE,
                status TEXT DEFAULT 'active',
                profile_image TEXT DEFAULT 'default_avatar.svg',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS notes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                course_id INTEGER NOT NULL DEFAULT 1,
                subject_id INTEGER NOT NULL DEFAULT 1,
                chapter_name TEXT NOT NULL DEFAULT '',
                title TEXT NOT NULL,
                file_path TEXT NOT NULL DEFAULT '',
                file_type TEXT DEFAULT 'pdf',
                file_size TEXT DEFAULT '1.2 MB',
                description TEXT,
                content TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS exams (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                course_id INTEGER NOT NULL DEFAULT 1,
                subject_id INTEGER,
                exam_title TEXT NOT NULL DEFAULT '',
                title TEXT DEFAULT '',
                description TEXT,
                total_questions INTEGER NOT NULL DEFAULT 10,
                duration_minutes INTEGER NOT NULL DEFAULT 15,
                max_marks INTEGER NOT NULL DEFAULT 50,
                passing_percentage INTEGER NOT NULL DEFAULT 40,
                status TEXT DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS questions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                exam_id INTEGER NOT NULL DEFAULT 1,
                question_text TEXT NOT NULL,
                option_a TEXT NOT NULL,
                option_b TEXT NOT NULL,
                option_c TEXT NOT NULL,
                option_d TEXT NOT NULL,
                correct_option TEXT NOT NULL,
                marks INTEGER NOT NULL DEFAULT 5,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS exam_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id INTEGER,
                exam_id INTEGER NOT NULL DEFAULT 1,
                roll_number TEXT NOT NULL DEFAULT '',
                student_name TEXT NOT NULL DEFAULT '',
                course_name TEXT NOT NULL DEFAULT '',
                start_time DATETIME NOT NULL,
                end_time DATETIME,
                total_questions INTEGER NOT NULL DEFAULT 0,
                attempted INTEGER NOT NULL DEFAULT 0,
                correct_answers INTEGER NOT NULL DEFAULT 0,
                wrong_answers INTEGER NOT NULL DEFAULT 0,
                unattempted INTEGER NOT NULL DEFAULT 0,
                total_marks INTEGER NOT NULL DEFAULT 0,
                obtained_marks INTEGER NOT NULL DEFAULT 0,
                percentage REAL NOT NULL DEFAULT 0.00,
                grade TEXT DEFAULT 'F',
                status TEXT DEFAULT 'in_progress',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS exam_answers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                attempt_id INTEGER NOT NULL,
                question_id INTEGER NOT NULL,
                selected_option TEXT,
                is_correct INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS results (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                attempt_id INTEGER NOT NULL UNIQUE,
                student_id INTEGER,
                exam_id INTEGER NOT NULL,
                roll_number TEXT NOT NULL,
                total_marks INTEGER NOT NULL,
                obtained_marks INTEGER NOT NULL,
                percentage REAL NOT NULL,
                grade TEXT NOT NULL,
                pass_status TEXT NOT NULL,
                date DATE NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS certificates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                certificate_number TEXT NOT NULL UNIQUE,
                student_id INTEGER NOT NULL DEFAULT 0,
                course_id INTEGER NOT NULL DEFAULT 0,
                result_id INTEGER NOT NULL DEFAULT 0,
                student_name TEXT NOT NULL DEFAULT '',
                roll_number TEXT NOT NULL DEFAULT '',
                course_name TEXT NOT NULL DEFAULT '',
                duration TEXT NOT NULL DEFAULT '',
                percentage REAL NOT NULL DEFAULT 0.00,
                grade TEXT NOT NULL DEFAULT 'A',
                issue_date DATE,
                status TEXT DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS contact_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT,
                phone TEXT,
                subject TEXT,
                message TEXT NOT NULL,
                status TEXT DEFAULT 'unread',
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
        ");

        // Dynamic safe migrations for existing SQLite databases
        try {
            $cols = $pdo->query("PRAGMA table_info(contact_messages)")->fetchAll(PDO::FETCH_COLUMN, 1);
            if (!in_array('status', $cols)) {
                $pdo->exec("ALTER TABLE contact_messages ADD COLUMN status TEXT DEFAULT 'unread'");
            }
        } catch (Exception $e) {}

        try {
            $cols = $pdo->query("PRAGMA table_info(exams)")->fetchAll(PDO::FETCH_COLUMN, 1);
            if (!in_array('exam_title', $cols)) {
                $pdo->exec("ALTER TABLE exams ADD COLUMN exam_title TEXT DEFAULT ''");
                $pdo->exec("UPDATE exams SET exam_title = title WHERE exam_title = '' OR exam_title IS NULL");
            }
        } catch (Exception $e) {}

        try {
            $cols = $pdo->query("PRAGMA table_info(certificates)")->fetchAll(PDO::FETCH_COLUMN, 1);
            if (!in_array('student_name', $cols)) {
                $pdo->exec("ALTER TABLE certificates ADD COLUMN student_name TEXT DEFAULT ''");
                $pdo->exec("ALTER TABLE certificates ADD COLUMN roll_number TEXT DEFAULT ''");
                $pdo->exec("ALTER TABLE certificates ADD COLUMN course_name TEXT DEFAULT ''");
                $pdo->exec("ALTER TABLE certificates ADD COLUMN duration TEXT DEFAULT ''");
                $pdo->exec("ALTER TABLE certificates ADD COLUMN result_id INTEGER DEFAULT 0");
            }
        } catch (Exception $e) {}

        try {
            $cols = $pdo->query("PRAGMA table_info(students)")->fetchAll(PDO::FETCH_COLUMN, 1);
            if (!in_array('mobile', $cols)) {
                $pdo->exec("ALTER TABLE students ADD COLUMN mobile TEXT DEFAULT ''");
                $pdo->exec("UPDATE students SET mobile = phone WHERE mobile = '' OR mobile IS NULL");
            }
        } catch (Exception $e) {}

        // Ensure superadmin exists with latest password
        $adminCount = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
        if ($adminCount == 0) {
            $adminHash = password_hash('Micro@DK#2026!', PASSWORD_BCRYPT);
            $pdo->prepare("INSERT INTO admins (id, name, email, password, role, status) VALUES (1, 'DK Singh (Director/Manager)', 'admin@microgroup.com', ?, 'superadmin', 'active')")->execute([$adminHash]);
        }

    } catch (Exception $e) {
        die("<div style='font-family:sans-serif;padding:30px;text-align:center;'><h2>Micro Group of Computer Institute</h2><p style='color:red;'>Database Connection Error: " . htmlspecialchars($e->getMessage()) . "</p></div>");
    }
}

// 3. Dynamic Base URL Detection
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        define('BASE_URL', $protocol . $host . '/micro-group');
    } else {
        define('BASE_URL', $protocol . $host);
    }
}