<?php
// Micro Group of Computer Institute - Helper Functions
require_once __DIR__ . '/database.php';

function get_setting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT key_value FROM settings WHERE key_name = ? LIMIT 1");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['key_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

function update_setting($key, $value) {
    global $pdo;
    try {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite' || $driver === 'pgsql') {
            $stmt = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES (?, ?) ON CONFLICT(key_name) DO UPDATE SET key_value=EXCLUDED.key_value");
        } else {
            $stmt = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE key_value = VALUES(key_value)");
        }
        return $stmt->execute([$key, $value]);
    } catch (Exception $e) {
        return false;
    }
}

function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
}

function format_currency($amount) {
    return '₹' . number_format((float)$amount, 2);
}

function calculate_grade($percentage) {
    $pct = (float)$percentage;
    if ($pct >= 85) return 'A+';
    if ($pct >= 75) return 'A';
    if ($pct >= 60) return 'B';
    if ($pct >= 50) return 'C';
    if ($pct >= 40) return 'D';
    return 'F';
}

function generate_certificate_no() {
    global $pdo;
    $prefix = get_setting('certificate_prefix', 'MGI-2026-');
    $stmt = $pdo->query("SELECT id FROM certificates ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch();
    $nextId = $last ? ($last['id'] + 1) : 1;
    return $prefix . str_pad($nextId, 5, '0', STR_PAD_LEFT);
}

function generate_roll_number() {
    global $pdo;
    $year = date('Y');
    $stmt = $pdo->query("SELECT id FROM students ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch();
    $nextId = $last ? ($last['id'] + 1) : 1;
    return 'MG' . $year . str_pad($nextId, 2, '0', STR_PAD_LEFT);
}

function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_message']['type'];
        $message = $_SESSION['flash_message']['message'];
        unset($_SESSION['flash_message']);
        
        $alertClass = 'alert-' . ($type === 'error' ? 'danger' : $type);
        $icon = ($type === 'success') ? 'bi-check-circle-fill' : (($type === 'danger' || $type === 'error') ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill');
        
        echo "<div class='alert {$alertClass} alert-dismissible fade show d-flex align-items-center shadow-sm' role='alert'>
                <i class='bi {$icon} me-2 fs-5'></i>
                <div>{$message}</div>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
}

// Student Auth Helpers
function is_student_logged_in() {
    return isset($_SESSION['student_id']) && !empty($_SESSION['student_id']);
}

function require_student_login() {
    if (!is_student_logged_in()) {
        set_flash_message('warning', 'Please login to access your student dashboard.');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function get_logged_student() {
    global $pdo;
    if (!is_student_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT s.*, c.course_name, c.short_name as course_code FROM students s LEFT JOIN courses c ON s.course_id = c.id WHERE s.id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    return $stmt->fetch();
}

// Admin Auth Helpers
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        set_flash_message('warning', 'Please login to access the admin panel.');
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function get_logged_admin() {
    global $pdo;
    if (!is_admin_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

// Student Profile & ID Card Helpers
function get_student_avatar_url($student) {
    if (!$student) return BASE_URL . '/assets/images/default_avatar.svg';
    $img = $student['profile_image'] ?? '';
    if (!empty($img) && $img !== 'default_avatar.svg') {
        $path = __DIR__ . '/../assets/uploads/students/' . $img;
        if (file_exists($path)) {
            return BASE_URL . '/assets/uploads/students/' . htmlspecialchars($img);
        }
    }
    return BASE_URL . '/assets/images/default_avatar.svg';
}

function get_student_profile_completion($student) {
    if (!$student) return ['percentage' => 0, 'is_complete' => false, 'missing' => [], 'filled_count' => 0, 'total_count' => 6];
    
    $required = [
        'name' => 'विद्यार्थी का नाम (Full Name)',
        'father_name' => 'पिता का नाम (Father\'s Name)',
        'mobile' => 'मोबाइल नंबर (Mobile Number)',
        'dob' => 'जन्मतिथि (Date of Birth)',
        'address' => 'स्थायी पता (Full Address)',
        'profile_image' => 'पासपोर्ट साइज फोटो (Passport Photo)'
    ];

    $missing = [];
    $total = count($required);
    $filled = 0;

    foreach ($required as $field => $label) {
        if ($field === 'profile_image') {
            $img = $student['profile_image'] ?? '';
            $hasPhoto = (!empty($img) && $img !== 'default_avatar.svg');
            if ($hasPhoto) {
                $filled++;
            } else {
                $missing[$field] = $label;
            }
        } else {
            $val = trim((string)($student[$field] ?? ''));
            if (!empty($val)) {
                $filled++;
            } else {
                $missing[$field] = $label;
            }
        }
    }

    $percentage = (int)round(($filled / $total) * 100);
    $is_complete = ($percentage === 100);

    return [
        'percentage' => $percentage,
        'is_complete' => $is_complete,
        'missing' => $missing,
        'filled_count' => $filled,
        'total_count' => $total
    ];
}

function is_student_profile_complete($student) {
    $info = get_student_profile_completion($student);
    return $info['is_complete'];
}